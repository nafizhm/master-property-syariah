<?php
namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\Customer;
use App\Models\SPR;
use App\Models\Pemasukan;
use App\Traits\LogAktivitasTrait;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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

        \Carbon\Carbon::setLocale('id');

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('templates/template_spr.xlsx'));
        $sheet = $spreadsheet->getActiveSheet();

        $checked   = '☑';
        $unchecked = '☐';

        $sheet->setCellValue('X2', $spr->nama_perum ?? '-');
        $sheet->setCellValue('M5', $spr->nama_lengkap ?? '-');
        $sheet->setCellValue('M6', $spr->alamat_ktp ?? '-');
        $sheet->setCellValue('M7', $spr->no_telp ?? '-');
        $sheet->setCellValue('M8', $spr->nik ?? '-');
        $sheet->setCellValue('M9', $spr->pekerjaan ?? '-');

        $income = (int) ($spr->estimasi_pendapatan ?? 0);

        $sheet->setCellValue('M10', $income < 10000000 ? $checked : $unchecked);
        $sheet->setCellValue('U10', ($income >= 10000001 && $income <= 15000000) ? $checked : $unchecked);
        $sheet->setCellValue('AF10', ($income >= 15000001 && $income <= 20000000) ? $checked : $unchecked);
        $sheet->setCellValue('M11', ($income >= 20000001 && $income <= 25000000) ? $checked : $unchecked);
        $sheet->setCellValue('Y11', $income > 25000000 ? $checked : $unchecked);

        $sumber = strtolower(trim($spr->sumber_dana ?? ''));

        $sheet->setCellValue('M12', str_contains($sumber, 'gaji') ? $checked : $unchecked);
        $sheet->setCellValue('P12', str_contains($sumber, 'usaha') ? $checked : $unchecked);
        $sheet->setCellValue('T12', str_contains($sumber, 'tabungan') ? $checked : $unchecked);
        $sheet->setCellValue('Y12', str_contains($sumber, 'warisan') ? $checked : $unchecked);
        $sheet->setCellValue('AD12', str_contains($sumber, 'investasi') ? $checked : $unchecked);
        $sheet->setCellValue('AI12', str_contains($sumber, 'lain') ? $checked : $unchecked);

        $tujuan = strtolower(trim($spr->tujuan_pembelian ?? ''));

        $sheet->setCellValue('M13', str_contains($tujuan, 'tempat') ? $checked : $unchecked);
        $sheet->setCellValue('W13', str_contains($tujuan, 'investasi') ? $checked : $unchecked);
        $sheet->setCellValue('AC13', str_contains($tujuan, 'sewa') ? $checked : $unchecked);
        $sheet->setCellValue('AG13', str_contains($tujuan, 'lain') ? $checked : $unchecked);

        $rumahKe = (int) ($spr->pembelian_rumah_ke ?? 0);

        $sheet->setCellValue('M14', $rumahKe === 1 ? $checked : $unchecked);
        $sheet->setCellValue('Q14', $rumahKe >= 2 ? $checked : $unchecked);

        $sheet->setCellValue('K18', $spr->nama_perum ?? '-');
        $sheet->setCellValue('K19', $spr->tipe_bangunan ?? '-');
        $sheet->setCellValue('K20', $spr->kode_kavling ?? '-');
        $sheet->setCellValue('K21', ($spr->luas_tanah ?? '-') . ' / ' . ($spr->luas_bangunan ?? '-'));
        $sheet->setCellValue('K22', $spr->nama_marketing ?? '-');
        $sheet->setCellValue('K23', $spr->pic ?? '-');

        $sheet->setCellValue('AG18', number_format($spr->hrg_jual, 0, ',', '.'));
        $sheet->setCellValue('AG19', number_format($spr->biaya_kpr, 0, ',', '.'));
        $sheet->setCellValue('AG20', number_format($spr->biaya_custom, 0, ',', '.'));
        $sheet->setCellValue('AG21', number_format($spr->diskon, 0, ',', '.'));
        $sheet->setCellValue('AG22', number_format($spr->biaya_lain, 0, ',', '.'));
        $sheet->setCellValue('AG23', number_format($spr->total_harga_unit, 0, ',', '.'));

        $metode = strtolower(trim($spr->metode_pembayaran ?? ''));

        $sheet->setCellValue('C26', str_contains($metode, 'hard') ? $checked : $unchecked);
        $sheet->setCellValue('C27', str_contains($metode, 'soft') ? $checked : $unchecked);
        $sheet->setCellValue('C28', str_contains($metode, 'kpr') ? $checked : $unchecked);

        if (str_contains($metode, 'soft')) {
            $sheet->setCellValue('J27', $spr->termin_soft ?? '-');
        }

        if (str_contains($metode, 'kpr')) {
            $sheet->setCellValue('J28', $spr->termin_kpr ?? '-');
        }

        $sheet->setCellValue('AG26', number_format($spr->hrg_jual, 0, ',', '.'));
        $sheet->setCellValue('AG27', number_format($spr->booking_fee, 0, ',', '.'));
        $sheet->setCellValue('AG28', number_format($spr->dp, 0, ',', '.'));
        $sheet->setCellValue('AG29', number_format($spr->kewajiban_kredit, 0, ',', '.'));

        $sheet->setCellValue('J35', $spr->catatan ?? '-');
        $sheet->setCellValue('G51', $spr->nama_lengkap ?? '-');

        $pemasukans = Pemasukan::where('id_customer', $spr->id_customer)
            ->where('keterangan', 'NOT LIKE', 'Biaya ganti nama%')
            ->orderBy('tanggal', 'asc')
            ->get();

        $totalTagihan = \App\Models\Piutang::where('id_customer', $spr->id_customer)
            ->sum('nominal');

        $startRow = 33;
        $totalRows = $pemasukans->count();

        if ($totalRows > 1) {
            $sheet->insertNewRowBefore($startRow + 1, $totalRows - 1);
        }

        $no = 1;
        $totalBayar = 0;

       foreach ($pemasukans as $index => $item) {

            $row = $startRow + $index;

            $totalBayar += $item->nominal;
            $sisa = max($totalTagihan - $totalBayar, 0);

            $sheet->mergeCells("D{$row}:I{$row}");
            $sheet->mergeCells("J{$row}:O{$row}");
            $sheet->mergeCells("P{$row}:V{$row}");
            $sheet->mergeCells("W{$row}:AC{$row}");
            $sheet->mergeCells("AD{$row}:AJ{$row}");
            $sheet->mergeCells("AK{$row}:AO{$row}");

            $sheet->setCellValue("C{$row}", $no++);
            $sheet->setCellValue("D{$row}", $spr->metode_pembayaran ?? '-');
            $sheet->setCellValue("J{$row}", \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y'));
            $sheet->setCellValue("P{$row}", number_format($item->nominal, 0, ',', '.'));
            $sheet->setCellValue("AD{$row}", number_format($sisa, 0, ',', '.'));
            $sheet->setCellValue("AK{$row}", $item->keterangan ?? '-');
        }

        if ($totalRows > 0) {

            $endRow = $startRow + $totalRows - 1;
            $range = "C{$startRow}:AO{$endRow}";

            $sheet->getStyle($range)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);

            for ($i = $startRow; $i <= $endRow; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(-1);
            }
        }

        $fileName = 'SPR_' . ($spr->nama_lengkap ?? 'customer') . '.xlsx';
        $path = storage_path('app/public/' . $fileName);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($path);

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
