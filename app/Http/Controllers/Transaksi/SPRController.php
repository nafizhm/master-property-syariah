<?php
namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\Customer;
use App\Models\SPR;
use App\Traits\LogAktivitasTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpWord\TemplateProcessor;

Carbon::setLocale('id');
class SPRController extends Controller
{
    use LogAktivitasTrait;
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();
        Carbon::setLocale('id');

        if ($request->ajax()) {
            $data = SPR::with(
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
                ->addColumn('lokasi_rumah', function ($row) {
                    $lokasi  = $row->customer->lokasi->nama_kavling ?? '-';
                    $kavling = $row->customer->kavling->kode_kavling ?? '-';
                    return '<strong>' . $lokasi . '</strong><br>' . $kavling;
                })
                ->addColumn('action', function ($row) use ($permissions) {

                $cetakUrl =  route('spr.cetak-word', $row->id);
                $deleteUrl = route('spr.destroy', $row->id);

                $btn = '<div>';

                $btn .= '<a href="' . e($cetakUrl) . '" target="_blank"
                    class="btn btn-primary btn-xs mr-1">Cetak SPR</a>';

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

        return view('admin.transaksi.spr.index', compact('permissions', 'customerList'));
    }

    public function detailSpr($id)
    {
        $customer = Customer::with(['lokasi', 'kavling', 'marketing', 'pemasukans' => function ($q) {
            $q->whereIn('id_kategori_transaksi', [1, 2]);
        }])->findOrFail($id);

        Carbon::setLocale('id');

        $booking = optional($customer->pemasukans->where('id_kategori_transaksi', 1)->first())->nominal;
        $dp      = optional($customer->pemasukans->where('id_kategori_transaksi', 2)->first())->nominal;

        return response()->json([
            'customer'    => $customer,
            'booking_fee' => $booking,
            'dp'          => $dp,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_customer'        => 'required',
            'nama_lengkap'       => 'required',
            'alamat_ktp'         => 'required',
            'no_telp'            => 'required',
            'nik'                => 'required',
            'pekerjaan'          => 'required',
            'nama_perum'         => 'required',
            'tipe_bangunan'      => 'required',
            'kode_kavling'       => 'required',
            'luas_tanah'         => 'required',
            'luas_bangunan'      => 'required',
            'nama_marketing'     => 'required',
            'pic'                => 'required',
            'hrg_jual'           => 'required',
            'total_harga_unit'   => 'required',
            'sumber_dana'        => 'required',
            'tujuan_pembelian'   => 'required',
            'pembelian_rumah_ke' => 'required',
            'nama_proyek'        => 'required',
            'hrg_jual_std'       => 'required',
            'metode_pembayaran'  => 'required',
        ], [
            'id_customer.required'        => 'Customer wajib dipilih.',
            'nama_lengkap.required'       => 'Nama lengkap wajib diisi.',
            'alamat_ktp.required'         => 'Alamat KTP wajib diisi.',
            'no_telp.required'            => 'No. telp wajib diisi.',
            'nik.required'                => 'NIK wajib diisi.',
            'pekerjaan.required'          => 'Pekerjaan wajib diisi.',
            'nama_perum.required'         => 'Nama perumahan wajib diisi.',
            'tipe_bangunan.required'      => 'Tipe bangunan wajib diisi.',
            'kode_kavling.required'       => 'Kode kavling wajib diisi.',
            'luas_tanah.required'         => 'Luas tanah wajib diisi.',
            'luas_bangunan.required'      => 'Luas bangunan wajib diisi.',
            'nama_marketing.required'     => 'Nama marketing wajib diisi.',
            'pic.required'                => 'PIC wajib diisi.',
            'hrg_jual.required'           => 'Harga jual wajib diisi.',
            'biaya_kpr.required'          => 'Biaya KPR wajib diisi.',
            'biaya_custom.required'       => 'Biaya custom wajib diisi.',
            'diskon.required'             => 'Diskon wajib diisi.',
            'biaya_lain.required'         => 'Biaya lain wajib diisi.',
            'total_harga_unit.required'   => 'Total harga unit wajib diisi.',
            'sumber_dana.required'        => 'Sumber dana wajib diisi.',
            'tujuan_pembelian.required'   => 'Tujuan pembelian wajib diisi.',
            'pembelian_rumah_ke.required' => 'Pembelian rumah ke wajib diisi.',
            'nama_proyek.required'        => 'Nama proyek wajib diisi.',
            'hrg_jual_std.required'       => 'Harga jual standard wajib diisi.',
            'metode_pembayaran.required'  => 'Metode pembayaran wajib dipilih.',
            'booking_fee.required'        => 'Booking fee wajib diisi.',
            'dp.required'                 => 'DP wajib diisi.',
            'kewajiban_kredit.required'   => 'Kewajiban kredit wajib diisi.',
        ]);

        DB::beginTransaction();
        try {

            $customer = Customer::lockForUpdate()->findOrFail($request->id_customer);

            $hrg_jual         = str_replace('.', '', $request->hrg_jual);
            $biaya_kpr        = str_replace('.', '', $request->biaya_kpr);
            $biaya_custom     = str_replace('.', '', $request->biaya_custom);
            $diskon           = str_replace('.', '', $request->diskon);
            $biaya_lain       = str_replace('.', '', $request->biaya_lain);
            $total_harga_unit = str_replace('.', '', $request->total_harga_unit);
            $hrg_jual_std     = str_replace('.', '', $request->hrg_jual_std);
            $booking_fee      = str_replace('.', '', $request->booking_fee);
            $dp               = str_replace('.', '', $request->dp);
            $kewajiban_kredit = str_replace('.', '', $request->kewajiban_kredit);

            $spr = SPR::create([
                'id_customer'         => $request->id_customer,
                'nama_lengkap'        => $request->nama_lengkap,
                'alamat_ktp'          => $request->alamat_ktp,
                'no_telp'             => $request->no_telp,
                'nik'                 => $request->nik,
                'pekerjaan'           => $request->pekerjaan,
                'nama_perum'          => $request->nama_perum,
                'tipe_bangunan'       => $request->tipe_bangunan,
                'kode_kavling'        => $request->kode_kavling,
                'luas_tanah'          => $request->luas_tanah,
                'luas_bangunan'       => $request->luas_bangunan,
                'nama_marketing'      => $request->nama_marketing,
                'pic'                 => $request->pic,
                'hrg_jual'            => $hrg_jual ?: null,
                'biaya_kpr'           => $biaya_kpr ?: null,
                'biaya_custom'        => $biaya_custom ?: null,
                'diskon'              => $diskon ?: null,
                'biaya_lain'          => $biaya_lain ?: null,
                'total_harga_unit'    => $total_harga_unit ?: null,
                'estimasi_pendapatan' => $request->estimasi_pendapatan ?: null,
                'join_income'         => $request->join_income ?: null,
                'sumber_dana'         => $request->sumber_dana,
                'tujuan_pembelian'    => $request->tujuan_pembelian,
                'pembelian_rumah_ke'  => $request->pembelian_rumah_ke,
                'nama_proyek'         => $request->nama_proyek,
                'hrg_jual_std'        => $hrg_jual_std ?: null,
                'metode_pembayaran'   => $request->metode_pembayaran,
                'termin_soft'         => $request->termin_soft,
                'termin_kpr'          => $request->termin_kpr,
                'booking_fee'         => $booking_fee ?: null,
                'dp'                  => $dp ?: null,
                'kewajiban_kredit'    => $kewajiban_kredit ?: null,
                'catatan'             => $request->catatan,
            ]);

            $this->logCreate('SPR', $spr->id);

            DB::commit();

            return response()->json([
                'success' => true,
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

    public function cetakWord($id)
    {
        $spr = SPR::findOrFail($id);

        Carbon::setLocale('id');

        $template = new TemplateProcessor(public_path('templates/SPR.docx'));

        $template->setValue('nama_kavling', $spr->nama_perum ?? '-');
        $template->setValue('nama_customer', $spr->nama_lengkap ?? '-');
        $template->setValue('no_telp', $spr->no_telp ?? '-');
        $template->setValue('no_ktp', $spr->nik ?? '-');
        $template->setValue('pekerjaan', $spr->pekerjaan ?? '-');
        $template->setValue('tipe_rumah', $spr->tipe_bangunan ?? '-');
        $template->setValue('kode_kav', $spr->kode_kavling ?? '-');
        $template->setValue('luas_t', $spr->luas_tanah ?? '-');
        $template->setValue('luas_b', $spr->luas_bangunan ?? '-');
        $template->setValue('nama_marketing', $spr->nama_marketing ?? '-');
        $template->setValue('pic', $spr->pic ?? '-');

        $template->setValue('harga_jual', number_format($spr->hrg_jual, 0, ',', '.'));
        $template->setValue('biaya_kpr', number_format($spr->biaya_kpr, 0, ',', '.'));
        $template->setValue('biaya_custom', number_format($spr->biaya_custom, 0, ',', '.'));
        $template->setValue('diskon', number_format($spr->diskon, 0, ',', '.'));
        $template->setValue('biaya_lain', number_format($spr->biaya_lain, 0, ',', '.'));
        $template->setValue('total_harga', number_format($spr->total_harga_unit, 0, ',', '.'));
        $template->setValue('booking_fee', number_format($spr->booking_fee, 0, ',', '.'));
        $template->setValue('dp', number_format($spr->dp, 0, ',', '.'));

        $template->setValue(
            'tanggal_spr',
            Carbon::now()->translatedFormat('d F Y')
        );

        $fileName = 'SPR_' . ($spr->nama_lengkap ?? 'customer') . '.docx';

        $path = storage_path('app/public/' . $fileName);
        $template->saveAs($path);

        return response()->download($path)->deleteFileAfterSend(true);
    }

    private function terbilang($angka)
    {
        $angka = abs((int) $angka);

        $bilangan = [
            '', 'satu', 'dua', 'tiga', 'empat', 'lima',
            'enam', 'tujuh', 'delapan', 'sembilan',
            'sepuluh', 'sebelas',
        ];

        if ($angka < 12) {
            return $bilangan[$angka];
        } elseif ($angka < 20) {
            return $this->terbilang($angka - 10) . ' belas';
        } elseif ($angka < 100) {
            return $this->terbilang(intval($angka / 10)) . ' puluh ' . $this->terbilang($angka % 10);
        } elseif ($angka < 200) {
            return 'seratus ' . $this->terbilang($angka - 100);
        } elseif ($angka < 1000) {
            return $this->terbilang(intval($angka / 100)) . ' ratus ' . $this->terbilang($angka % 100);
        } elseif ($angka < 2000) {
            return 'seribu ' . $this->terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return $this->terbilang(intval($angka / 1000)) . ' ribu ' . $this->terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            return $this->terbilang(intval($angka / 1000000)) . ' juta ' . $this->terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            return $this->terbilang(intval($angka / 1000000000)) . ' miliar ' . $this->terbilang($angka % 1000000000);
        }

        return 'angka terlalu besar';
    }

    public function destroy($id)
    {
        $data = SPR::findOrFail($id);

        $this->logDelete('PPJB', $data->id);
        $data->delete();

        return response()->json(['status' => 'success']);
    }

    private function headerKavling($pdf, $customer)
    {
        $pdf->SetFont('Times', 'B', 15);

        $yText = 12;

        $namaKavling = optional($customer->lokasi)->nama_kavling ?? '-';

        $pageWidth = $pdf->GetPageWidth();
        $textWidth = $pdf->GetStringWidth($namaKavling);

        $xText = ($pageWidth - $textWidth) / 2;

        $pdf->SetLineWidth(1.2);

        $pdf->Line(20, $yText + 3, $xText - 3, $yText + 3);

        $pdf->SetXY($xText, $yText);
        $pdf->Cell(0, 5, $namaKavling);

        $pdf->Line(
            $xText + $textWidth + 3,
            $yText + 3,
            $pageWidth - 20,
            $yText + 3
        );
    }

    private function footerKavling($pdf, $customer)
    {
        $pdf->SetFont('Times', '', 12);

        $pdf->SetXY(23.5, 273);
        $pdf->Cell(0, 5, optional($customer->lokasi)->nama_kavling ?? '-');
    }
}
