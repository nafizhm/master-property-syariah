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

                    $jenis = $row->customer->jenis_pembelian ?? null;

                    $cetakUrl = '#';

                    if ($jenis === 'KPR') {
                        $cetakUrl = route('ppjb.cetak-kpr', $row->id_customer);
                    } elseif ($jenis === 'Cash Bertahap') {
                        $cetakUrl = route('ppjb.cetak-cash-bertahap', $row->id_customer);
                    } elseif ($jenis === 'Pembelian Cash') {
                        $cetakUrl = route('ppjb.cetak-pembelian-cash', $row->id_customer);
                    }

                    $deleteUrl = route('spr.destroy', $row->id);

                    $btn = '<div>';

                    if ($cetakUrl !== '#') {
                        $btn .= '<a href="' . e($cetakUrl) . '" target="_blank"
                    class="btn btn-primary btn-xs mr-1">Cetak PPJB</a>';
                    }

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

    // kpr
    public function cetakKpr($id_customer)
    {
        $customer = Customer::with([
            'kavling',
            'lokasi',
            'marketing',
            'spr',
            'pemasukans' => function ($q) {
                $q->whereIn('id_kategori_transaksi', [1, 2]);
            },
        ])->findOrFail($id_customer);

        $bookingFee = optional(
            $customer->pemasukans->where('id_kategori_transaksi', 1)->first()
        )->nominal ?? 0;

        $DP = optional(
            $customer->pemasukans->where('id_kategori_transaksi', 2)->first()
        )->nominal ?? 0;

        $bookingFeeFormat = $bookingFee
            ? number_format($bookingFee, 0, ',', '.')
            : '-';

        $DPFormat = $DP
            ? number_format($DP, 0, ',', '.')
            : '-';

        $tempatLahir = $customer->tempat_lahir ?? '-';
        \Carbon\Carbon::setLocale('id');

        $tglLahir = $customer->tgl_lahir
            ? \Carbon\Carbon::parse($customer->tgl_lahir)->translatedFormat('j F Y')
            : '-';

        $ttl = $tempatLahir . ', ' . $tglLahir;

        $templatePath = public_path('templates/PPJB.pdf');

        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);

        $pageCount = $pdf->setSourceFile($templatePath);

        for ($page = 1; $page <= $pageCount; $page++) {
            $pdf->AddPage();
            $tplId = $pdf->importPage($page);
            $pdf->useTemplate($tplId, 0, 0, 210, 297);

            if ($page >= 2) {
                $this->headerKavling($pdf, $customer);
                $this->footerKavling($pdf, $customer);
            }

            if ($page == 1) {

                $pdf->SetFont('Times', 'B', 35);

                $text1      = optional($customer->lokasi)->nama_kavling ?? '-';
                $textWidth1 = $pdf->GetStringWidth($text1);
                $pageWidth  = $pdf->GetPageWidth();

                $pdf->SetXY(($pageWidth - $textWidth1) / 2, 75);
                $pdf->Cell(0, 5, $text1);

                $pdf->SetFont('Times', 'B', 20);

                $text2      = optional($customer->spr)->no_spr ?? '-';
                $textWidth2 = $pdf->GetStringWidth($text2);

                $pdf->SetXY(($pageWidth - $textWidth2) / 2, 235);
                $pdf->Cell(0, 5, $text2);
            }

            if ($page == 2) {
                $tanggal = Carbon::now()->translatedFormat('d');
                $bulan   = Carbon::now()->translatedFormat('F');
                $tahun   = Carbon::now()->translatedFormat('Y');

                $pdf->SetFont('Times', 'B', 12);

                $text = 'No. ' . (optional($customer->spr)->no_spr ?? '-');

                $pageWidth = $pdf->GetPageWidth();
                $textWidth = $pdf->GetStringWidth($text);

                $x = ($pageWidth - $textWidth) / 2;

                $pdf->SetXY($x, 28.8);
                $pdf->Cell(0, 5, $text);

                $pdf->SetXY(53.4, 39.6);
                $pdf->Cell(15, 5, $tanggal);

                $pdf->SetXY(68.8, 39.6);
                $pdf->Cell(30, 5, $bulan);

                $pdf->SetXY(97, 39.6);
                $pdf->Cell(20, 5, $tahun);

                $pdf->SetXY(66, 95);
                $pdf->Cell(0, 5, $customer->nama_lengkap ?? '-');

                $pdf->SetXY(66, 102);
                $pdf->Cell(0, 5, $ttl);

                $pdf->SetXY(66, 109);
                $alamat = $customer->alamat_ktp ?? '-';
                $alamat = mb_substr($alamat, 0, 50);

                $pdf->MultiCell(140, 5, $alamat);

                $pdf->SetXY(66, 116);
                $pdf->Cell(0, 5, $customer->nik ?? '-');

                $pdf->SetFont('Times', 'B', 10);
                $pdf->SetXY(130, 198);
                $luasTanah = optional($customer->kavling)->luas_bangunan ?? '-';
                $pdf->Cell(0, 5, $luasTanah !== '-' ? $luasTanah . ' m²' : '-');

                $pdf->SetXY(115, 204);
                $luasTanah = optional($customer->kavling)->luas_tanah ?? '-';
                $pdf->Cell(0, 5, $luasTanah !== '-' ? $luasTanah . ' m²' : '-');

                $pdf->SetFont('Times', 'B', 12);

                $lokasi = optional($customer->lokasi);

                $pdf->SetXY(80, 224);
                $pdf->Cell(0, 5, $lokasi->nama_jalan ?? '-');

                $pdf->SetXY(80, 231);
                $pdf->Cell(0, 5, $lokasi->desa_kelurahan ?? '-');

                $pdf->SetXY(80, 238);
                $pdf->Cell(0, 5, $lokasi->kecamatan ?? '-');

                $pdf->SetXY(80, 245);
                $pdf->Cell(0, 5, $lokasi->kabupaten_kota ?? '-');

                $pdf->SetXY(80, 256);
                $pdf->Cell(0, 5, $lokasi->provinsi ?? '-');
            }

            if ($page == 3) {
                $pdf->SetFont('Times', 'B', 12);

                $text = (optional($customer->lokasi)->nama_kavling ?? '-') . ', KAVLING ' . strtoupper(optional($customer->kavling)->kode_kavling ?? '-');

                $pdf->SetXY(108.5, 32.3);
                $pdf->Cell(0, 5, $text);

                $hargaJual = optional($customer->kavling)->hrg_jual;

                $hargaFormat = $hargaJual
                    ? number_format($hargaJual, 0, ',', '.')
                    : '-';

                $pdf->SetFont('Times', 'B', 12);
                $pdf->SetXY(94, 133.8);
                $pdf->Cell(0, 5, $hargaFormat);

                $pdf->SetFont('Times', '', 10);
                $pdf->SetXY(85, 138);
                $pdf->MultiCell(
                    110,
                    10,
                    $hargaJual
                        ? '(' . ucwords($this->terbilang($hargaJual)) . 'Rupiah)'
                        : '-'
                );

                $pdf->SetFont('Times', 'B', 12);
                $pdf->SetXY(94, 147.1);
                $pdf->Cell(0, 5, $DPFormat);

                $pdf->SetFont('Times', '', 10);
                $pdf->SetXY(85, 154);
                $pdf->MultiCell(
                    110,
                    5,
                    $DP
                        ? '(' . ucwords($this->terbilang($DP)) . 'Rupiah)'
                        : '-'
                );

                $diskon = $customer->diskon ?? 0;

                $diskonFormat = $diskon
                    ? number_format($diskon, 0, ',', '.')
                    : '-';

                $pdf->SetFont('Times', 'B', 12);
                $pdf->SetXY(94, 160.5);
                $pdf->Cell(0, 5, $diskonFormat);

                $pdf->SetFont('Times', '', 10);
                $pdf->SetXY(85, 166);
                $pdf->MultiCell(
                    110,
                    5,
                    $diskon
                        ? '(' . ucwords($this->terbilang($diskon)) . 'Rupiah)'
                        : '-'
                );

                $pdf->SetFont('Times', 'B', 12);
                $pdf->SetXY(94, 173.8);
                $pdf->Cell(0, 5, $bookingFeeFormat);

                $pdf->SetFont('Times', '', 10);
                $pdf->SetXY(85, 179);
                $pdf->MultiCell(
                    110,
                    6,
                    $bookingFee
                        ? '(' . ucwords($this->terbilang($bookingFee)) . 'Rupiah)'
                        : '-'
                );
            }
            if ($page == 4) {

            }
            if ($page == 5) {

            }
            if ($page == 6) {

            }
            if ($page == 7) {

            }
            if ($page == 8) {

            }
            if ($page == 9) {

            }
            if ($page == 10) {
                $pdf->SetFont('Times', '', 11);
                $pdf->SetXY(160.5, 69);
                $pdf->Cell(0, 5, $customer->spr->tanggal_spr ? Carbon::parse($customer->spr->tanggal_spr)->translatedFormat('d F Y') : '-');

                $pdf->SetFont('Times', 'B', 11);
                $pdf->SetXY(80, 111.5);
                $pdf->Cell(0, 5, $customer->nama_lengkap ?? '-');
            }
            if ($page == 11) {

            }
        }

        $pdf->SetTitle('PPJB KPR - ' . ($customer->nama_lengkap ?? '-'));

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'inline; filename="PPJB KPR - ' . ($customer->nama_lengkap ?? '-') . '.pdf"'
            );
    }

    // cash bertahap
    public function cetakCashBertahap($id_customer)
    {
        $customer = Customer::with([
            'kavling',
            'lokasi',
            'marketing',
            'spr',
            'pemasukans' => function ($q) {
                $q->whereIn('id_kategori_transaksi', [1, 2]);
            },
        ])->findOrFail($id_customer);

        $bookingFee = optional(
            $customer->pemasukans->where('id_kategori_transaksi', 1)->first()
        )->nominal ?? 0;

        $DP = optional(
            $customer->pemasukans->where('id_kategori_transaksi', 2)->first()
        )->nominal ?? 0;

        $bookingFeeFormat = $bookingFee
            ? number_format($bookingFee, 0, ',', '.')
            : '-';

        $DPFormat = $DP
            ? number_format($DP, 0, ',', '.')
            : '-';

        $tempatLahir = $customer->tempat_lahir ?? '-';
        \Carbon\Carbon::setLocale('id');

        $tglLahir = $customer->tgl_lahir
            ? \Carbon\Carbon::parse($customer->tgl_lahir)->translatedFormat('j F Y')
            : '-';

        $ttl = $tempatLahir . ', ' . $tglLahir;

        $templatePath = public_path('templates/PPJB-CASHBERTAHAP.pdf');

        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);

        $pageCount = $pdf->setSourceFile($templatePath);

        for ($page = 1; $page <= $pageCount; $page++) {
            $pdf->AddPage();
            $tplId = $pdf->importPage($page);
            $pdf->useTemplate($tplId, 0, 0, 210, 297);

            if ($page >= 2) {
                $this->headerKavling($pdf, $customer);
                $this->footerKavling($pdf, $customer);
            }

            if ($page == 1) {

                $pdf->SetFont('Times', 'B', 35);

                $text1      = optional($customer->lokasi)->nama_kavling ?? '-';
                $textWidth1 = $pdf->GetStringWidth($text1);
                $pageWidth  = $pdf->GetPageWidth();

                $pdf->SetXY(($pageWidth - $textWidth1) / 2, 75);
                $pdf->Cell(0, 5, $text1);

                $pdf->SetFont('Times', 'B', 20);

                $text2      = optional($customer->spr)->no_spr ?? '-';
                $textWidth2 = $pdf->GetStringWidth($text2);

                $pdf->SetXY(($pageWidth - $textWidth2) / 2, 232);
                $pdf->Cell(0, 5, $text2);
            }

            if ($page == 2) {
                $tanggal = Carbon::now()->translatedFormat('d');
                $bulan   = Carbon::now()->translatedFormat('F');
                $tahun   = Carbon::now()->translatedFormat('Y');

                $pdf->SetFont('Times', 'B', 12);

                $text = 'No. ' . (optional($customer->spr)->no_spr ?? '-');

                $pageWidth = $pdf->GetPageWidth();
                $textWidth = $pdf->GetStringWidth($text);

                $x = ($pageWidth - $textWidth) / 2;

                $pdf->SetXY($x, 28.8);
                $pdf->Cell(0, 5, $text);
                $pdf->SetXY(63.5, 40.1);
                $pdf->Cell(15, 5, $tanggal);

                $pdf->SetXY(79.5, 40.1);
                $pdf->Cell(30, 5, $bulan);

                $pdf->SetXY(104, 40.1);
                $pdf->Cell(20, 5, $tahun);

                $pdf->SetXY(74, 94);
                $pdf->Cell(0, 5, $customer->nama_lengkap ?? '-');

                $pdf->SetXY(74, 101);
                $pdf->Cell(0, 5, $ttl);

                $alamat = $customer->alamat_ktp ?? '-';
                $alamat = mb_substr($alamat, 0, 50);

                $pdf->SetXY(74, 108);
                $pdf->MultiCell(140, 5, $alamat ?? '-');

                $pdf->SetXY(74, 115);
                $pdf->Cell(0, 5, $customer->nik ?? '-');

                $pdf->SetFont('Times', 'B', 8);
                $pdf->SetXY(162, 196);
                $luasTanah = optional($customer->kavling)->luas_bangunan ?? '-';
                $pdf->Cell(0, 5, $luasTanah !== '-' ? $luasTanah . ' m²' : '-');

                $pdf->SetXY(149, 203);
                $luasTanah = optional($customer->kavling)->luas_tanah ?? '-';
                $pdf->Cell(0, 5, $luasTanah !== '-' ? $luasTanah . ' m²' : '-');

                $pdf->SetFont('Times', 'B', 11);
                $pdf->SetXY(87, 222.5);
                $pdf->Cell(0, 5, $customer->lokasi->nama_jalan ?? '-');

                $pdf->SetXY(87, 229.5);
                $pdf->Cell(0, 5, $customer->lokasi->desa_kelurahan ?? '-');

                $pdf->SetXY(87, 235.6);
                $pdf->Cell(0, 5, $customer->lokasi->kecamatan ?? '-');

                $pdf->SetXY(87, 243);
                $pdf->Cell(0, 5, $customer->lokasi->kabupaten_kota ?? '-');

                $pdf->SetXY(87, 249.3);
                $pdf->Cell(0, 5, $customer->lokasi->provinsi ?? '-');
            }

            if ($page == 3) {
                $pdf->SetFont('Times', 'B', 11);

                $text = (optional($customer->lokasi)->nama_kavling ?? '-') . ', Kavling ' . strtoupper(optional($customer->kavling)->kode_kavling ?? '-');

                $pdf->SetXY(118.5, 21.1);
                $pdf->Cell(0, 5, $text);

                $hargaJual = optional($customer->kavling)->hrg_jual;

                $hargaFormat = $hargaJual
                    ? number_format($hargaJual, 0, ',', '.')
                    : '-';

                $pdf->SetFont('Times', 'B', 11);
                $pdf->SetXY(100, 124);
                $pdf->Cell(0, 5, $hargaFormat);

                $pdf->SetFont('Times', '', 11);
                $pdf->SetXY(93, 128.5);
                $pdf->MultiCell(
                    110,
                    10,
                    $hargaJual
                        ? '(' . ucwords($this->terbilang($hargaJual)) . 'Rupiah)'
                        : '-'
                );

                $discount = $customer->diskon ?? 0;

                $discountFormat = $discount
                    ? number_format($discount, 0, ',', '.')
                    : '-';

                $pdf->SetFont('Times', 'B', 11);
                $pdf->SetXY(100, 137.5);
                $pdf->Cell(0, 5, $discountFormat);

                $pdf->SetFont('Times', '', 11);
                $pdf->SetXY(93, 144);
                $pdf->MultiCell(
                    110,
                    5,
                    $discount
                        ? '(' . ucwords($this->terbilang($discount)) . 'Rupiah)'
                        : '-'
                );

                $pdf->SetFont('Times', 'B', 11);
                $pdf->SetXY(100, 151);
                $pdf->Cell(0, 5, $DPFormat);

                $pdf->SetFont('Times', '', 11);
                $pdf->SetXY(93, 157.5);
                $pdf->MultiCell(
                    110,
                    6,
                    $DP
                        ? '(' . ucwords($this->terbilang($DP)) . 'Rupiah)'
                        : '-'
                );

                $pdf->SetFont('Times', 'B', 11);
                $pdf->SetXY(100, 165);
                $pdf->Cell(0, 5, $bookingFeeFormat);

                $pdf->SetFont('Times', '', 11);
                $pdf->SetXY(93, 171.5);
                $pdf->MultiCell(
                    110,
                    6,
                    $bookingFee
                        ? '(' . ucwords($this->terbilang($bookingFee)) . 'Rupiah)'
                        : '-'
                );

            }
            if ($page == 4) {

            }
            if ($page == 5) {

            }
            if ($page == 6) {
            }
            if ($page == 7) {
            }
            if ($page == 8) {
            }
            if ($page == 9) {
            }
            if ($page == 10) {
            }
            if ($page == 11) {

                $pdf->SetFont('Times', 'B', 11);
                $pdf->SetXY(93.5, 74.5);
                $pdf->Cell(0, 5, $customer->nama_lengkap ?? '-');
            }
        }

        $pdf->SetTitle('PPJB CashB - ' . ($customer->nama_lengkap ?? '-'));

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'inline; filename="PPJB CashB - ' . ($customer->nama_lengkap ?? '-') . '.pdf"'
            );
    }

    // cash keras
    public function cetakPembelianCash($id_customer)
    {
        $customer = Customer::with([
            'kavling',
            'lokasi',
            'marketing',
            'spr',
            'pemasukans' => function ($q) {
                $q->whereIn('id_kategori_transaksi', [1, 2]);
            },
        ])->findOrFail($id_customer);

        $bookingFee = optional(
            $customer->pemasukans->where('id_kategori_transaksi', 1)->first()
        )->nominal ?? 0;

        $DP = optional(
            $customer->pemasukans->where('id_kategori_transaksi', 2)->first()
        )->nominal ?? 0;

        $bookingFeeFormat = $bookingFee
            ? number_format($bookingFee, 0, ',', '.')
            : '-';

        $DPFormat = $DP
            ? number_format($DP, 0, ',', '.')
            : '-';

        $tempatLahir = $customer->tempat_lahir ?? '-';
        \Carbon\Carbon::setLocale('id');

        $tglLahir = $customer->tgl_lahir
            ? \Carbon\Carbon::parse($customer->tgl_lahir)->translatedFormat('j F Y')
            : '-';

        $ttl = $tempatLahir . ', ' . $tglLahir;

        $templatePath = public_path('templates/PPJB-CASHKERAS.pdf');

        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);

        $pageCount = $pdf->setSourceFile($templatePath);

        for ($page = 1; $page <= $pageCount; $page++) {
            $pdf->AddPage();
            $tplId = $pdf->importPage($page);
            $pdf->useTemplate($tplId, 0, 0, 210, 297);

            if ($page >= 2) {
                $this->headerKavling($pdf, $customer);
                $this->footerKavling($pdf, $customer);
            }

            if ($page == 1) {

                $pdf->SetFont('Times', 'B', 35);

                $text1      = optional($customer->lokasi)->nama_kavling ?? '-';
                $textWidth1 = $pdf->GetStringWidth($text1);
                $pageWidth  = $pdf->GetPageWidth();

                $pdf->SetXY(($pageWidth - $textWidth1) / 2, 75);
                $pdf->Cell(0, 5, $text1);

                $pdf->SetFont('Times', 'B', 20);

                $text2      = optional($customer->spr)->no_spr ?? '-';
                $textWidth2 = $pdf->GetStringWidth($text2);

                $pdf->SetXY(($pageWidth - $textWidth2) / 2, 232);
                $pdf->Cell(0, 5, $text2);
            }

            if ($page == 2) {
                $tanggal = Carbon::now()->translatedFormat('d');
                $bulan   = Carbon::now()->translatedFormat('F');
                $tahun   = Carbon::now()->translatedFormat('Y');

                $pdf->SetFont('Times', 'B', 12);

                $text = 'No. ' . (optional($customer->spr)->no_spr ?? '-');

                $pageWidth = $pdf->GetPageWidth();
                $textWidth = $pdf->GetStringWidth($text);

                $x = ($pageWidth - $textWidth) / 2;

                $pdf->SetXY($x, 28.8);
                $pdf->Cell(0, 5, $text);
                $pdf->SetXY(63.5, 40.1);
                $pdf->Cell(15, 5, $tanggal);

                $pdf->SetXY(79.5, 40.1);
                $pdf->Cell(30, 5, $bulan);

                $pdf->SetXY(104, 40.1);
                $pdf->Cell(20, 5, $tahun);

                $pdf->SetXY(74, 94);
                $pdf->Cell(0, 5, $customer->nama_lengkap ?? '-');

                $pdf->SetXY(74, 101);
                $pdf->Cell(0, 5, $ttl);

                $alamat = $customer->alamat_ktp ?? '-';
                $alamat = mb_substr($alamat, 0, 50);

                $pdf->SetXY(74, 108);
                $pdf->MultiCell(140, 5, $alamat ?? '-');

                $pdf->SetXY(74, 115);
                $pdf->Cell(0, 5, $customer->nik ?? '-');

                $pdf->SetFont('Times', 'B', 8);
                $pdf->SetXY(162, 196);
                $luasTanah = optional($customer->kavling)->luas_bangunan ?? '-';
                $pdf->Cell(0, 5, $luasTanah !== '-' ? $luasTanah . ' m²' : '-');

                $pdf->SetXY(149, 203);
                $luasTanah = optional($customer->kavling)->luas_tanah ?? '-';
                $pdf->Cell(0, 5, $luasTanah !== '-' ? $luasTanah . ' m²' : '-');

                $pdf->SetFont('Times', 'B', 11);
                $pdf->SetXY(87, 222.5);
                $pdf->Cell(0, 5, $customer->lokasi->nama_jalan ?? '-');

                $pdf->SetXY(87, 229.5);
                $pdf->Cell(0, 5, $customer->lokasi->desa_kelurahan ?? '-');

                $pdf->SetXY(87, 235.6);
                $pdf->Cell(0, 5, $customer->lokasi->kecamatan ?? '-');

                $pdf->SetXY(87, 243);
                $pdf->Cell(0, 5, $customer->lokasi->kabupaten_kota ?? '-');

                $pdf->SetXY(87, 249.3);
                $pdf->Cell(0, 5, $customer->lokasi->provinsi ?? '-');
            }

            if ($page == 3) {
                $pdf->SetFont('Times', 'B', 11);

                $text = (optional($customer->lokasi)->nama_kavling ?? '-') . ', Kavling ' . strtoupper(optional($customer->kavling)->kode_kavling ?? '-');

                $pdf->SetXY(118.5, 21.1);
                $pdf->Cell(0, 5, $text);

                $hargaJual = optional($customer->kavling)->hrg_jual;

                $hargaFormat = $hargaJual
                    ? number_format($hargaJual, 0, ',', '.')
                    : '-';

                $pdf->SetFont('Times', 'B', 11);
                $pdf->SetXY(100, 124);
                $pdf->Cell(0, 5, $hargaFormat);

                $pdf->SetFont('Times', '', 11);
                $pdf->SetXY(93, 128.5);
                $pdf->MultiCell(
                    110,
                    10,
                    $hargaJual
                        ? '(' . ucwords($this->terbilang($hargaJual)) . 'Rupiah)'
                        : '-'
                );

                $discount = $customer->diskon ?? 0;

                $discountFormat = $discount
                    ? number_format($discount, 0, ',', '.')
                    : '-';

                $pdf->SetFont('Times', 'B', 11);
                $pdf->SetXY(100, 137.5);
                $pdf->Cell(0, 5, $discountFormat);

                $pdf->SetFont('Times', '', 11);
                $pdf->SetXY(93, 144);
                $pdf->MultiCell(
                    110,
                    5,
                    $discount
                        ? '(' . ucwords($this->terbilang($discount)) . 'Rupiah)'
                        : '-'
                );

                $pdf->SetFont('Times', 'B', 11);
                $pdf->SetXY(100, 151);
                $pdf->Cell(0, 5, $DPFormat);

                $pdf->SetFont('Times', '', 11);
                $pdf->SetXY(93, 157.5);
                $pdf->MultiCell(
                    110,
                    6,
                    $DP
                        ? '(' . ucwords($this->terbilang($DP)) . 'Rupiah)'
                        : '-'
                );

                $pdf->SetFont('Times', 'B', 11);
                $pdf->SetXY(100, 165);
                $pdf->Cell(0, 5, $bookingFeeFormat);

                $pdf->SetFont('Times', '', 11);
                $pdf->SetXY(93, 171.5);
                $pdf->MultiCell(
                    110,
                    6,
                    $bookingFee
                        ? '(' . ucwords($this->terbilang($bookingFee)) . 'Rupiah)'
                        : '-'
                );

            }
            if ($page == 4) {
            }
            if ($page == 5) {
            }
            if ($page == 6) {

            }
            if ($page == 7) {

            }
            if ($page == 8) {
            }
            if ($page == 9) {
            }
            if ($page == 10) {
            }
            if ($page == 11) {

                $pdf->SetFont('Times', 'B', 11);
                $pdf->SetXY(93.5, 74.5);
                $pdf->Cell(0, 5, $customer->nama_lengkap ?? '-');
            }
        }

        $pdf->SetTitle('PPJB CashK - ' . ($customer->nama_lengkap ?? '-'));

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'inline; filename="PPJB CashK - ' . ($customer->nama_lengkap ?? '-') . '.pdf"'
            );
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
