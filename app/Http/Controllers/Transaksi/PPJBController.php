<?php
namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GenerateNumberController;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\Customer;
use App\Models\KavlingPeta;
use App\Models\LokasiKavling;
use App\Models\PPJB;
use App\Traits\LogAktivitasTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;
use Yajra\DataTables\Facades\DataTables;
use setasign\Fpdi\Fpdi;

class PPJBController extends Controller
{
    use LogAktivitasTrait;
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();
        Carbon::setLocale('id');

        if ($request->ajax()) {
            $data = PPJB::with(
                'customer',
                'customer.lokasi',
                'customer.kavling'
            )
                ->whereHas('customer', function ($q) {
                    $q->where('stt_arsip', 0);
                })
                ->orderByDesc('id');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('tanggal_ppjb', function ($row) {
                    return Carbon::parse($row->tanggal_ppjb)->translatedFormat('d F Y');
                })
                ->addColumn('lokasi_rumah', function ($row) {
                    $lokasi  = $row->customer->lokasi->nama_kavling ?? '-';
                    $kavling = $row->customer->kavling->kode_kavling ?? '-';
                    return '<strong>' . $lokasi . '</strong><br>' . $kavling;
                })
                ->addColumn('action', function ($row) use ($permissions) {
                    $cetakUrl  = route('ppjb.cetak', $row->id_customer);
                     $cetakKprUrl  = route('ppjb.cetakKpr', $row->id_customer);
                    $deleteUrl = route('ppjb.destroy', $row->id);

                    $btn = '<div >';

                        $btn .= '<a href="' . e($cetakUrl) . '" target="_blank"
                                    class="btn btn-dark btn-xs mr-1">Cetak</a>';

                        $btn .= '<a href="' . e($cetakKprUrl) . '" target="_blank"
                                    class="btn btn-primary btn-xs mr-1">PPJB KPR</a>';

                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">'
                        . csrf_field()
                        . method_field('DELETE')
                            . '<button type="submit" class="delete-button btn btn-danger btn-xs">Hapus</button></form>';
                    }

                    return $btn . '</div>';
                })
                ->rawColumns(['lokasi_rumah', 'action'])
                ->make(true);
        }

        $customerList = Customer::all();

        return view('admin.transaksi.ppjb.index', compact('permissions', 'customerList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_ppjb' => 'required',
            'id_customer'  => 'required',
        ], [
            'tanggal_ppjb.required' => 'Tanggal PPJB wajib diisi.',
            'id_customer.required'  => 'Customer wajib dipilih.',
        ]);

        DB::beginTransaction();
        try {

            $customer = Customer::with('lokasi')
                ->lockForUpdate()
                ->findOrFail($request->id_customer);

            $generator = new GenerateNumberController();

            $noPPJB = $generator->generateNomorDokumen(
                $customer->lokasi,
                'no_ppjb',
                PPJB::class
            );

            $ppjb = PPJB::create([
                'tanggal_ppjb' => $request->tanggal_ppjb,
                'id_customer'  => $request->id_customer,
                'no_ppjb'      => $noPPJB,
            ]);

            $this->logCreate('PPJB', $ppjb->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'no_ppjb' => $noPPJB,
            ], 200);

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function cetakPPJB($id_customer)
    {
        $customer = Customer::findOrFail($id_customer);
        $ppjb     = PPJB::where('id_customer', $id_customer)->firstOrFail();
        $kavling  = KavlingPeta::where('id', $customer->id_kavling)->first();
        $lokasi   = LokasiKavling::where('id', $customer->id_lokasi)->first();

        $tanggal          = Carbon::parse($ppjb->tanggal_ppjb)->locale('id');
        $tanggalTerbilang = ucwords($this->numberToWords($tanggal->day));
        $hari             = $ppjb->hari_bast ?? $tanggal->translatedFormat('l');
        $bulan            = $tanggal->translatedFormat('F');
        $tahun            = $tanggal->format('Y');
        $tahun_terbilang  = ucwords($this->numberToWords($tahun));

        $templatePath = public_path('templates/tempate_ppjb.docx');

        $templateProcessor = new TemplateProcessor($templatePath);

        $templateProcessor->setValues([
            'no_ppjb'         => $ppjb->no_ppjb,
            'hari'            => $hari,
            'tanggal'         => $tanggalTerbilang,
            'bulan'           => $bulan,
            'tahun_terbilang' => $tahun_terbilang,
            'nama_customer'   => $customer->nama_lengkap,
            'no_ktp'          => $customer->nik,
            'no_telp'         => $customer->no_telp,
            'pekerjaan'       => $customer->pekerjaan ?? '',
            'alamat_ktp'      => $customer->alamat ?? '',
            'kode_kavling'    => $kavling->kode_kavling ?? '',
            'nama_perumahan'  => $lokasi->nama_kavling ?? '',
            'tipe_rumah'      => $kavling->tipe_bangunan ?? '',
            'luas_tanah'      => $kavling->luas_tanah ?? '',
            'norek_listrik'   => $customer->norek_listrik ?? '',
            'norek_air'       => $customer->norek_air ?? '',
            'luas_bangunan'   => $kavling->luas_bangunan ?? '',
            'tanggal_ppjb'    => $tanggal->translatedFormat('j F Y'),
        ]);

        $fileName = 'ppjb_' . Str::slug($customer->nama_lengkap) . '.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'phpword');
        $templateProcessor->saveAs($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    private function numberToWords($number)
    {
        $f = new \NumberFormatter("id", \NumberFormatter::SPELLOUT);
        return $f->format($number);
    }

    public function cetakKpr($id_customer)
    {
        $customer = Customer::with([
            'kavlingPeta.lokasi',
            'lokasiKavling',
            'marketing'
        ])->findOrFail($id_customer);
        $tempatLahir = $customer->tempat_lahir ?? '-';

        $tglLahir = $customer->tgl_lahir
            ? \Carbon\Carbon::parse($customer->tgl_lahir)->format('d-m-Y')
            : '-';

        $ttl = $tempatLahir . ', ' . $tglLahir;

        $templatePath = public_path('templates/PPJB_KPR.pdf');

        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);

        $pageCount = $pdf->setSourceFile($templatePath);

        for ($page = 1; $page <= $pageCount; $page++) {
            $pdf->AddPage();
            $tplId = $pdf->importPage($page);
            $pdf->useTemplate($tplId, 0, 0, 210, 297);

            $pdf->SetFont('Arial', '', 10);

            if ($page == 2) {
                $pdf->SetXY(66, 96);
                $pdf->Cell(0, 5, $customer->nama_lengkap ?? '-');

                $pdf->SetXY(66, 103);
                $pdf->Cell(0, 5, $ttl);

                $pdf->SetXY(66, 110);
                $pdf->MultiCell(140, 5, $customer->alamat_ktp ?? '-');

                $pdf->SetXY(66, 117);
                $pdf->Cell(0, 5, $customer->nik ?? '-');

            }
        }

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'inline; filename="PPJB-KPR-' . $customer->kode_customer . '.pdf"'
            );
    }

    public function destroy($id)
    {
        $data = PPJB::findOrFail($id);

        $this->logDelete('PPJB', $data->id);
        $data->delete();

        return response()->json(['status' => 'success']);
    }
}
