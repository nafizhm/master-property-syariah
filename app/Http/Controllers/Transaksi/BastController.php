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
use setasign\Fpdi\Fpdi;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpWord\TemplateProcessor;

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

    public function detailBast($id)
    {
        $customer = Customer::with('lokasi', 'kavling')->findOrFail($id);

        return response()->json($customer);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_bast'       => 'required',
            'id_customer'        => 'required',
            'nama_perum'         => 'required',
            'nama_customer'      => 'required',
            'alamat_ktp'         => 'required',
            'nama_jalan'         => 'required',
            'desa'               => 'required',
            'kec'                => 'required',
            'kota'               => 'required',
            'kode_kavling'       => 'required',
            'luas_tanah'         => 'required',
            'luas_bangunan'      => 'required',
            'petugas_pendamping' => 'required',
        ], [
            'tanggal_bast.required'       => 'Tanggal BAST wajib diisi.',
            'id_customer.required'        => 'Customer wajib dipilih.',
            'nama_perum.required'         => 'Nama perumahan wajib diisi.',
            'nama_customer.required'      => 'Nama customer wajib diisi.',
            'alamat_ktp.required'         => 'Alamat KTP wajib diisi.',
            'nama_jalan.required'         => 'Alamat perumahan wajib diisi.',
            'desa.required'               => 'Desa wajib diisi.',
            'kec.required'                => 'Kecamatan wajib diisi.',
            'kota.required'               => 'Kota wajib diisi.',
            'kode_kavling.required'       => 'Kode kavling wajib diisi.',
            'luas_tanah.required'         => 'Luas tanah wajib diisi.',
            'luas_bangunan.required'      => 'Luas bangunan wajib diisi.',
            'petugas_pendamping.required' => 'Petugas pendamping wajib diisi.',
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
                'tanggal_bast'       => $request->tanggal_bast,
                'id_customer'        => $request->id_customer,
                'no_bast'            => $noBast,
                'nama_perum'         => $request->nama_perum,
                'nama_customer'      => $request->nama_customer,
                'alamat_ktp'         => $request->alamat_ktp,
                'nama_jalan'         => $request->nama_jalan,
                'desa'               => $request->desa,
                'kec'                => $request->kec,
                'kota'               => $request->kota,
                'kode_kavling'       => $request->kode_kavling,
                'luas_tanah'         => $request->luas_tanah,
                'luas_bangunan'      => $request->luas_bangunan,
                'petugas_pendamping' => $request->petugas_pendamping,
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
        $bast = BAST::where('id_customer', $id_customer)->firstOrFail();

        $tanggalBast = Carbon::parse($bast->tanggal_bast);

        $tanggal = $tanggalBast->day;
        $bulan   = $tanggalBast->translatedFormat('F');
        $tahun   = $tanggalBast->year;

        $template = new TemplateProcessor(public_path('templates/BAST.docx'));

        $template->setValue('nama_kavling', $bast->nama_perum ?? '-');
        $template->setValue('no_bast', $bast->no_bast ?? '-');
        $template->setValue('tanggal', $tanggal);
        $template->setValue('bulan', $bulan);
        $template->setValue('tahun', $tahun);
        $template->setValue('nama_customer', $bast->nama_customer ?? '-');
        $template->setValue('alamat', $bast->alamat_ktp ?? '-');
        $template->setValue('desa', $bast->desa ?? '-');
        $template->setValue('kecamatan', $bast->kec ?? '-');
        $template->setValue('kota', $bast->kota ?? '-');
        $template->setValue('kode_kav', $bast->kode_kavling ?? '-');
        $template->setValue('luas_tanah', $bast->luas_tanah ?? '-');
        $template->setValue('luas_bangunan', $bast->luas_bangunan ?? '-');
        $template->setValue('tanggal_bast', $tanggalBast->format('d-m-Y'));
        $template->setValue('tanggal_pemeriksaan', $tanggalBast->format('d-m-Y'));
        $template->setValue('petugas', $bast->petugas_pendamping ?? '-');

        $filename = 'BAST_' . $bast->id_customer . '_' . date('Ymd') . '.docx';

        $tempFile = storage_path($filename);
        $template->saveAs($tempFile);

        return response()->download($tempFile)->deleteFileAfterSend(true);
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
