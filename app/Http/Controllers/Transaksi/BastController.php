<?php
namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GenerateNumberController;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\BAST;
use App\Models\Customer;
use App\Models\KavlingPeta;
use App\Models\LokasiKavling;
use App\Traits\LogAktivitasTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ListrikAir;
use setasign\Fpdi\Fpdi;

class BastController extends Controller
{
    use LogAktivitasTrait;
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();
        Carbon::setLocale('id');

        if ($request->ajax()) {
            $data = BAST::with(
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
                ->editColumn('tanggal_bast', function ($row) {
                    return Carbon::parse($row->tanggal_bast)->translatedFormat('d F Y');
                })
                ->addColumn('lokasi_rumah', function ($row) {
                    $lokasi  = $row->customer->lokasi->nama_kavling ?? '-';
                    $kavling = $row->customer->kavling->kode_kavling ?? '-';
                    return '<strong>' . $lokasi . '</strong><br>' . $kavling;
                })
                ->addColumn('action', function ($row) use ($permissions) {
                    $cetakUrl  = route('bast.cetak', $row->id_customer);
                    $deleteUrl = route('bast.destroy', $row->id);

                    $btn = '<div class="d-flex justify-content-center">';

                    if ($permissions['edit']) {
                        $btn .= '<a href="' . e($cetakUrl) . '" target="_blank" class="btn btn-dark btn-sm mr-2">Cetak</a>';
                    }

                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">'
                        . csrf_field()
                        . method_field('DELETE')
                            . '<button type="submit" class="delete-button btn btn-danger btn-sm">Hapus</button></form>';
                    }

                    return $btn . '</div>';
                })
                ->rawColumns(['lokasi_rumah', 'action'])
                ->make(true);
        }

        $customerList = Customer::all();

        return view('admin.transaksi.bast.index', compact('permissions', 'customerList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_bast' => 'required',
            'id_customer'  => 'required',
        ], [
            'tanggal_bast.required' => 'Tanggal BAST wajib diisi.',
            'id_customer.required'  => 'Customer wajib dipilih.',
        ]);

        DB::beginTransaction();
        try {

            $customer = Customer::with('lokasi')
                ->lockForUpdate()
                ->findOrFail($request->id_customer);

            $generator = new GenerateNumberController();

            $noBast = $generator->generateNomorDokumen(
                $customer->lokasi,
                'no_bast',
                BAST::class
            );

            $bast = BAST::create([
                'tanggal_bast' => $request->tanggal_bast,
                'id_customer'  => $request->id_customer,
                'no_bast'      => $noBast,
            ]);

            $this->logCreate('BAST', $bast->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'no_bast' => $noBast,
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

    public function cetakBast($id_customer)
    {
        $customer = Customer::with(['lokasiKavling', 'kavlingPeta'])
            ->findOrFail($id_customer);
        $bast = BAST::where('id_customer', $id_customer)->firstOrFail();
        $lokasi  = $customer->lokasiKavling;
        $kavling = $customer->kavlingPeta;
        $no_bast        = $bast->no_bast ?? '-';
        $nama_kavling   = $lokasi->nama_kavling ?? '-';
        $alamat_lokasi  = $lokasi->alamat ?? '-';
        $nama_lengkap   = $customer->nama_lengkap ?? '-';
        $alamat_ktp     = $customer->alamat_ktp ?? '-';
        $kode_kavling   = $kavling->kode_kavling ?? '-';
        $luas_tanah     = $kavling->luas_tanah ?? '-';
        $luas_bangunan  = $kavling->luas_bangunan ?? '-';
        $tipe_bangunan  = $kavling->tipe_bangunan ?? '-';

        $bulan_id = [
            1  => 'Januari',   2  => 'Februari', 3  => 'Maret',
            4  => 'April',     5  => 'Mei',       6  => 'Juni',
            7  => 'Juli',      8  => 'Agustus',   9  => 'September',
            10 => 'Oktober',   11 => 'November',  12 => 'Desember',
        ];
        $tgl_hari_ini   = (int) date('d');
        $bln_hari_ini   = $bulan_id[(int) date('m')];
        $thn_hari_ini   = date('Y');
        $tanggal_lengkap = $tgl_hari_ini . ' ' . $bln_hari_ini . ' ' . $thn_hari_ini;


        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);

        $templatePath = public_path('templates/BAST.pdf');

        $totalPages = $pdf->setSourceFile($templatePath);

        for ($pageNo = 1; $pageNo <= $totalPages; $pageNo++) {

            $tplIdx = $pdf->importPage($pageNo);
            $size   = $pdf->getTemplateSize($tplIdx);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);

            $pdf->useTemplate($tplIdx, 0, 0, $size['width'], $size['height']);

            $pdf->SetTextColor(0, 0, 0);
            if ($pageNo === 1) {

                $pdf->SetFont('Times', 'B', 40);
                $pdf->SetXY(65, 100);
                $pdf->Write(0, $nama_kavling);

                $pdf->SetFont('Times', 'B', 20);
                $pdf->SetXY(80, 236);
                $pdf->Write(0, $no_bast);

            }

            if ($pageNo === 2) {
                $pdf->SetFont('Times', '', 12);

                $pdf->SetXY(72, 44);
                $pdf->Write(0, $tgl_hari_ini);

                $pdf->SetXY(100, 44);
                $pdf->Write(0, $bln_hari_ini);

                $pdf->SetXY(134, 44);
                $pdf->Write(0, $thn_hari_ini);

                $pdf->SetXY(61, 134);
                $pdf->Write(0, $nama_lengkap);

                $pdf->SetXY(61, 141);
                $pdf->Write(0, $alamat_ktp);

                $pdf->SetXY(96, 174);
                $pdf->Write(0, $nama_kavling);

                $pdf->SetXY(96, 182);
                $pdf->Write(0, $alamat_lokasi);

                $pdf->SetXY(96, 212);
                $pdf->Write(0, $kode_kavling);

                $pdf->SetXY(96, 219);
                $pdf->Write(0, $luas_tanah . ' m²');

                $pdf->SetXY(96, 228);
                $pdf->Write(0, $tipe_bangunan);
            }

            if ($pageNo === 4) {
                $pdf->SetXY(158, 183);
                $pdf->Write(0, $tgl_hari_ini);

                $pdf->SetXY(163, 183);
                $pdf->Write(0, $bln_hari_ini);

                $pdf->SetXY(174, 183);
                $pdf->Write(0, $thn_hari_ini);

                $pdf->SetXY(137, 242);
                $pdf->Write(0, $nama_lengkap);
            }

            if ($pageNo === 5) {
                $pdf->SetXY(72, 42);
                $pdf->Write(0, $nama_kavling);

                $pdf->SetXY(72, 50);
                $pdf->Write(0, $kode_kavling);

                $pdf->SetXY(72, 58);
                $pdf->Write(0, $luas_tanah . ' m²');

                $pdf->SetXY(86, 58);
                $pdf->Write(0, '/');

                $pdf->SetXY(88, 58);
                $pdf->Write(0, $luas_bangunan . ' m²');
            }

        }


        $filename = 'BAST_' . $customer->kode_customer . '_' . date('Ymd') . '.pdf';

        $pdf->Output('I', $filename);
        exit;
    }

    private function numberToWords($number)
    {
        $f = new \NumberFormatter("id", \NumberFormatter::SPELLOUT);
        return $f->format($number);
    }

    public function destroy($id)
    {
        $data = BAST::findOrFail($id);

        $this->logDelete('BAST', $data->id);
        $data->delete();

        return response()->json(['status' => 'success']);
    }
}
