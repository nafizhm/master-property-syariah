<?php
namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GenerateNumberController;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\Customer;
use App\Models\PPJB;
use App\Traits\LogAktivitasTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpWord\TemplateProcessor;

Carbon::setLocale('id');
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
                ->select('ppjb.*')
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

                    $jenis = $row->customer->jenis_pembelian ?? null;

                    $cetakUrl = '#';

                    if ($jenis === 'KPR') {
                        $cetakUrl = route('ppjb.cetak-kpr', $row->id);
                    } elseif ($jenis === 'Cash Bertahap') {
                        $cetakUrl = route('ppjb.cetak-cash-bertahap', $row->id);
                    } elseif ($jenis === 'Pembelian Cash') {
                        $cetakUrl = route('ppjb.cetak-pembelian-cash', $row->id);
                    }

                    $deleteUrl = route('ppjb.destroy', $row->id);

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

        return view('admin.transaksi.ppjb.index', compact('permissions', 'customerList'));
    }

    public function detailPpjb($id)
    {
        $customer = Customer::with(['lokasi', 'kavling', 'pemasukans' => function ($q) {
            $q->whereIn('id_kategori_transaksi', [1, 2]);
        }])->findOrFail($id);

        Carbon::setLocale('id');

        $ttl = null;
        if ($customer->tempat_lahir && $customer->tgl_lahir) {
            $ttl = $customer->tempat_lahir . ', ' . Carbon::parse($customer->tgl_lahir)->translatedFormat('j F Y');
        }

        $booking = optional($customer->pemasukans->where('id_kategori_transaksi', 1)->first())->nominal;
        $dp      = optional($customer->pemasukans->where('id_kategori_transaksi', 2)->first())->nominal;

        return response()->json([
            'customer'    => $customer,
            'ttl'         => $ttl,
            'booking_fee' => $booking,
            'dp'          => $dp,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_ppjb'  => 'required',
            'id_customer'   => 'required',
            'nama_perum'    => 'required',
            'kode_kavling'  => 'required',
            'saksi'         => 'required',
            'nama_customer' => 'required',
            'ttl'           => 'required',
            'alamat_ktp'    => 'required',
            'no_ktp'        => 'required',
            'luas_tanah'    => 'required',
            'luas_bangunan' => 'required',
            'nama_jalan'    => 'required',
            'desa'          => 'required',
            'kec'           => 'required',
            'kota'          => 'required',
            'prov'          => 'required',
            'no_shm'        => 'required',
            'hrg_jual'      => 'required',
            'diskon'        => 'required',
            'dp'            => 'required',
            'booking_fee'   => 'required',
        ], [
            'tanggal_ppjb.required'  => 'Tanggal PPJB wajib diisi.',
            'id_customer.required'   => 'Customer wajib dipilih.',
            'nama_perum.required'    => 'Nama perumahan wajib diisi.',
            'kode_kavling.required'  => 'Kode kavling wajib diisi.',
            'saksi.required'         => 'Nama saksi wajib diisi.',
            'nama_customer.required' => 'Nama customer wajib diisi.',
            'ttl.required'           => 'Tempat/Tanggal lahir wajib diisi.',
            'alamat_ktp.required'    => 'Alamat KTP wajib diisi.',
            'no_ktp.required'        => 'No. KTP wajib diisi.',
            'luas_tanah.required'    => 'Luas tanah wajib diisi.',
            'luas_bangunan.required' => 'Luas bangunan wajib diisi.',
            'nama_jalan.required'    => 'Nama jalan wajib diisi.',
            'desa.required'          => 'Desa/Kelurahan wajib diisi.',
            'kec.required'           => 'Kecamatan wajib diisi.',
            'kota.required'          => 'Kota wajib diisi.',
            'prov.required'          => 'Provinsi wajib diisi.',
            'no_shm.required'        => 'No. SHM wajib diisi.',
            'hrg_jual.required'      => 'Harga jual wajib diisi.',
            'diskon.required'        => 'Diskon wajib diisi.',
            'dp.required'            => 'DP wajib diisi.',
            'booking_fee.required'   => 'Booking fee wajib diisi.',
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

            $hrg_jual    = str_replace('.', '', $request->hrg_jual);
            $diskon      = str_replace('.', '', $request->diskon);
            $dp          = str_replace('.', '', $request->dp);
            $booking_fee = str_replace('.', '', $request->booking_fee);

            $ppjb = PPJB::create([
                'tanggal_ppjb'  => $request->tanggal_ppjb,
                'id_customer'   => $request->id_customer,
                'no_ppjb'       => $noPPJB,
                'nama_perum'    => $request->nama_perum,
                'kode_kavling'  => $request->kode_kavling,
                'nama_customer' => $request->nama_customer,
                'ttl'           => $request->ttl,
                'alamat_ktp'    => $request->alamat_ktp,
                'no_ktp'        => $request->no_ktp,
                'luas_bangunan' => $request->luas_bangunan,
                'luas_tanah'    => $request->luas_tanah,
                'nama_jalan'    => $request->nama_jalan,
                'desa'          => $request->desa,
                'kec'           => $request->kec,
                'kota'          => $request->kota,
                'prov'          => $request->prov,
                'no_shm'        => $request->no_shm,
                'hrg_jual'      => $hrg_jual ?: null,
                'diskon'        => $diskon ?: null,
                'dp'            => $dp ?: null,
                'booking_fee'   => $booking_fee ?: null,
                'saksi'         => $request->saksi,
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

    // kpr
    public function cetakKpr($id)
    {
        $data = PPJB::findOrFail($id);

        $template = new TemplateProcessor(
            public_path('templates/PPJB_KPR.docx')
        );

        $rupiah = fn($angka) => number_format($angka, 0, ',', '.');

        $terbilang = fn($angka) => ucwords($this->terbilang($angka)) . ' Rupiah';

        $template->setValue('nama_kavling',   $data->nama_perum   ?? '-');
        $template->setValue('no_ppjb',        $data->no_ppjb      ?? '-');
        $template->setValue('nama_customer',  $data->nama_customer ?? '-');
        $template->setValue('nama_saksi', $data->saksi ?? '-');
        $template->setValue('ttl',            $data->ttl          ?? '-');
        $template->setValue('alamat_ktp',     $data->alamat_ktp   ?? '-');
        $template->setValue('no_ktp',         $data->no_ktp       ?? '-');
        $template->setValue('nama_jalan',     $data->nama_jalan   ?? '-');
        $template->setValue('nama_desa',      $data->desa         ?? '-');
        $template->setValue('kecamatan',      $data->kec          ?? '-');
        $template->setValue('kota',           $data->kota         ?? '-');
        $template->setValue('provinsi',       $data->prov         ?? '-');
        $template->setValue('no_shm',         $data->no_shm       ?? '-');

        $template->setValue('harga_jual',          isset($data->hrg_jual)    ? $rupiah($data->hrg_jual)    : '-');
        $template->setValue('terbilang_harga_jual', isset($data->hrg_jual)   ? $terbilang($data->hrg_jual) : '-');

        $template->setValue('dp',             isset($data->dp)       ? $rupiah($data->dp)       : '-');
        $template->setValue('terbilang_dp',   isset($data->dp)       ? $terbilang($data->dp)    : '-');

        $template->setValue('diskon',         isset($data->diskon)   ? $rupiah($data->diskon)   : '-');
        $template->setValue('terbilang_diskon', isset($data->diskon) ? $terbilang($data->diskon): '-');

        $template->setValue('booking_fee',          isset($data->booking_fee) ? $rupiah($data->booking_fee)    : '-');
        $template->setValue('terbilang_booking_fee', isset($data->booking_fee)? $terbilang($data->booking_fee) : '-');

        if ($data->tanggal_ppjb) {
            $tgl = Carbon::parse($data->tanggal_ppjb);
            $template->setValue('tanggal_ppjb', $tgl->format('d-m-Y'));
            $template->setValue('tanggal',      $tgl->format('d'));
            $template->setValue('bulan',        $tgl->translatedFormat('F'));
            $template->setValue('tahun',        $tgl->format('Y'));
        } else {
            $template->setValue('tanggal_ppjb', '-');
            $template->setValue('tanggal', '-');
            $template->setValue('bulan', '-');
            $template->setValue('tahun', '-');
        }

        $fileName = 'PPJB-KPR-' . $data->id . '.docx';
        $path     = storage_path('app/' . $fileName);

        $template->saveAs($path);

        return response()->download($path, 'PPJB-KPR-' . ($data->nama_customer ?? $data->id) . '.docx')
                        ->deleteFileAfterSend(true);
    }

    // cash bertahap
    public function cetakCashBertahap($id)
    {
        $data = PPJB::findOrFail($id);

        $customer = Customer::findOrFail($data->id_customer);

        $template = new TemplateProcessor(
            public_path('templates/PPJB-CASH-BERTAHAP.docx')
        );

        $rupiah    = fn($angka) => number_format($angka, 0, ',', '.');
        $terbilang = fn($angka) => ucwords($this->terbilang($angka)) . ' Rupiah';

        $template->setValue('nama_kavling',  $data->nama_perum    ?? '-');
        $template->setValue('no_ppjb',       $data->no_ppjb       ?? '-');
        $template->setValue('kode_kavling',  $data->kode_kavling  ?? '-');
        $template->setValue('saksi',         $data->saksi         ?? '-');

        $template->setValue('nama_customer', $data->nama_customer ?? '-');
        $template->setValue('nama_saksi', $data->saksi ?? '-');
        $template->setValue('ttl',           $data->ttl           ?? '-');
        $template->setValue('alamat_ktp',    $data->alamat_ktp    ?? '-');
        $template->setValue('no_ktp',        $data->no_ktp        ?? '-');

        $template->setValue('luas_tanah',    $data->luas_tanah    ?? '-');
        $template->setValue('luas_bangunan', $data->luas_bangunan ?? '-');
        $template->setValue('nama_jalan',    $data->nama_jalan    ?? '-');
        $template->setValue('nama_desa',     $data->desa          ?? '-');
        $template->setValue('kecamatan',     $data->kec           ?? '-');
        $template->setValue('kota',          $data->kota          ?? '-');
        $template->setValue('provinsi',      $data->prov          ?? '-');
        $template->setValue('no_shm',        $data->no_shm        ?? '-');

        $template->setValue('harga_jual',           isset($data->hrg_jual)    ? $rupiah($data->hrg_jual)    : '-');
        $template->setValue('terbilang_harga_jual',  isset($data->hrg_jual)   ? $terbilang($data->hrg_jual) : '-');
        $template->setValue('diskon',               isset($data->diskon)      ? $rupiah($data->diskon)      : '-');
        $template->setValue('terbilang_diskon',      isset($data->diskon)     ? $terbilang($data->diskon)   : '-');
        $template->setValue('dp',                   isset($data->dp)          ? $rupiah($data->dp)          : '-');
        $template->setValue('terbilang_dp',          isset($data->dp)         ? $terbilang($data->dp)       : '-');
        $template->setValue('booking_fee',          isset($data->booking_fee) ? $rupiah($data->booking_fee)    : '-');
        $template->setValue('terbilang_booking_fee', isset($data->booking_fee)? $terbilang($data->booking_fee) : '-');

        if ($data->tanggal_ppjb) {
            $tgl = Carbon::parse($data->tanggal_ppjb);
            $template->setValue('tanggal_ppjb', $tgl->format('d-m-Y'));
            $template->setValue('tanggal',      $tgl->format('d'));
            $template->setValue('bulan',        $tgl->translatedFormat('F'));
            $template->setValue('tahun',        $tgl->format('Y'));
        } else {
            foreach (['tanggal_ppjb', 'tanggal', 'bulan', 'tahun'] as $key) {
                $template->setValue($key, '-');
            }
        }

        $totalHarga = (float) ($customer->total_harga_rumah ?? 0);
        $terminXB   = (int)   ($customer->termin_x_cash_b        ?? 1);

        $jumlahPerTahap  = $terminXB > 0 ? floor($totalHarga / $terminXB) : 0;
        $sisaPembulatan  = $totalHarga - ($jumlahPerTahap * $terminXB);

        $template->cloneRow('tahap', $terminXB);

        $totalPembayaran = 0;

        for ($i = 1; $i <= $terminXB; $i++) {
            $jumlah = ($i === $terminXB)
                ? $jumlahPerTahap + $sisaPembulatan
                : $jumlahPerTahap;

            $totalPembayaran += $jumlah;

            $template->setValue("tahap#{$i}",  'Tahap ' . $i);
            $template->setValue("jumlah#{$i}", $rupiah($jumlah));
        }

        $template->setValue('total_pembayaran', $rupiah($totalPembayaran));

        $fileName = 'PPJB-CashB-' . $data->id . '.docx';
        $path     = storage_path('app/' . $fileName);

        $template->saveAs($path);

        return response()->download(
            $path,
            'PPJB Cash Bertahap - ' . ($data->nama_customer ?? $data->id) . '.docx'
        )->deleteFileAfterSend(true);
    }

    // cash keras
    public function cetakPembelianCash($id)
    {
        $data = PPJB::findOrFail($id);

        $customer = Customer::findOrFail($data->id_customer);

        $template = new TemplateProcessor(
            public_path('templates/PPJB-CASH-KERAS.docx')
        );

        $rupiah    = fn($angka) => number_format($angka, 0, ',', '.');
        $terbilang = fn($angka) => ucwords($this->terbilang($angka)) . ' Rupiah';

        $template->setValue('nama_kavling',  $data->nama_perum    ?? '-');
        $template->setValue('lokasi_kavling',  $data->nama_perum    ?? '-');
        $template->setValue('no_ppjb',       $data->no_ppjb       ?? '-');
        $template->setValue('kode_kavling',  $data->kode_kavling  ?? '-');
        $template->setValue('saksi',         $data->saksi         ?? '-');

        $template->setValue('nama_customer', $data->nama_customer ?? '-');
        $template->setValue('nama_saksi', $data->saksi ?? '-');
        $template->setValue('ttl',           $data->ttl           ?? '-');
        $template->setValue('alamat_ktp',    $data->alamat_ktp    ?? '-');
        $template->setValue('no_ktp',        $data->no_ktp        ?? '-');

        $template->setValue('luas_tanah',    $data->luas_tanah    ?? '-');
        $template->setValue('luas_bangunan', $data->luas_bangunan ?? '-');
        $template->setValue('nama_jalan',    $data->nama_jalan    ?? '-');
        $template->setValue('desa',          $data->desa          ?? '-');
        $template->setValue('kecamatan',     $data->kec           ?? '-');
        $template->setValue('kota',          $data->kota          ?? '-');
        $template->setValue('provinsi',      $data->prov          ?? '-');
        $template->setValue('no_shm',        $data->no_shm        ?? '-');

        $template->setValue('harga_jual',           isset($data->hrg_jual)    ? $rupiah($data->hrg_jual)    : '-');
        $template->setValue('terbilang_harga_jual',  isset($data->hrg_jual)   ? $terbilang($data->hrg_jual) : '-');
        $template->setValue('diskon',               isset($data->diskon)      ? $rupiah($data->diskon)      : '-');
        $template->setValue('terbilang_diskon',      isset($data->diskon)     ? $terbilang($data->diskon)   : '-');
        $template->setValue('dp',                   isset($data->dp)          ? $rupiah($data->dp)          : '-');
        $template->setValue('terbilang_dp',          isset($data->dp)         ? $terbilang($data->dp)       : '-');
        $template->setValue('booking_fee',          isset($data->booking_fee) ? $rupiah($data->booking_fee)    : '-');
        $template->setValue('terbilang_booking_fee', isset($data->booking_fee)? $terbilang($data->booking_fee) : '-');

        if ($data->tanggal_ppjb) {
            $tgl = Carbon::parse($data->tanggal_ppjb);
            $template->setValue('tanggal_ppjb', $tgl->format('d-m-Y'));
            $template->setValue('tanggal',      $tgl->format('d'));
            $template->setValue('bulan',        $tgl->translatedFormat('F'));
            $template->setValue('tahun',        $tgl->format('Y'));
        } else {
            foreach (['tanggal_ppjb', 'tanggal', 'bulan', 'tahun'] as $key) {
                $template->setValue($key, '-');
            }
        }

        $totalHarga = (float) ($customer->total_harga_rumah ?? 0);

        $template->setValue('jumlah',           $totalHarga ? $rupiah($totalHarga) : '-');
        $template->setValue('total_pembayaran', $totalHarga ? $rupiah($totalHarga) : '-');

        $fileName = 'PPJB-CashK-' . $data->id . '.docx';
        $path     = storage_path('app/' . $fileName);

        $template->saveAs($path);

        return response()->download(
            $path,
            'PPJB Pembelian Cash - ' . ($data->nama_customer ?? $data->id) . '.docx'
        )->deleteFileAfterSend(true);
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
        $data = PPJB::findOrFail($id);

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
