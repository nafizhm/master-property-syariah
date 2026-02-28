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
                    // $cetakUrl  = route('ppjb.cetak', $row->id_customer);
                     $cetakKprUrl  = route('ppjb.cetakKpr', $row->id_customer);
                    $deleteUrl = route('ppjb.destroy', $row->id);

                    $btn = '<div >';

                        // $btn .= '<a href="' . e($cetakUrl) . '" target="_blank"
                        //             class="btn btn-dark btn-xs mr-1">Cetak</a>';

                        $btn .= '<a href="' . e($cetakKprUrl) . '" target="_blank"
                                    class="btn btn-primary btn-xs mr-1">Cetak PPJB</a>';

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

    public function cetakKpr($id_customer)
    {
       $customer = Customer::with([
            'kavlingPeta.lokasi',
            'lokasiKavling',
            'marketing',
            'ppjb',
            'pemasukans' => function ($q) {
                $q->whereIn('id_kategori_transaksi', [1 ,2]);
            }
        ])->findOrFail($id_customer);

       $totalKategori1 = $customer->pemasukans
            ->where('id_kategori_transaksi', 1)
            ->sum('nominal');

        $totalKategori2 = $customer->pemasukans
            ->where('id_kategori_transaksi', 2)
            ->sum('nominal');

        $totalKategori1Format = $totalKategori1
            ? number_format($totalKategori1, 0, ',', '.')
            : '-';

        $totalKategori2Format = $totalKategori2
            ? number_format($totalKategori2, 0, ',', '.')
            : '-';

        $tempatLahir = $customer->tempat_lahir ?? '-';
        $tglLahir = $customer->tgl_lahir
            ? \Carbon\Carbon::parse($customer->tgl_lahir)->format('d-m-Y')
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

            if($page == 1){
                $pdf->SetFont('Arial', 'B', 25);
                $pdf->SetXY(75, 30);
                $pdf->Cell(0, 5, optional($customer->lokasiKavling)->nama_kavling ?? '-');

                $pdf->SetFont('Arial', 'B', 23);
                $pdf->SetXY(68, 99);
                $pdf->Cell(0, 5, optional($customer->ppjb)->no_ppjb ?? '-');
            }


            if ($page == 2) {
                $tanggal = Carbon::now()->translatedFormat('d');
                $bulan   = Carbon::now()->translatedFormat('F');
                $tahun   = Carbon::now()->translatedFormat('Y');

                $pdf->SetFont('Arial', 'B', 25);

                $xText = 75;
                $yText = 8;

                $namaKavling = optional($customer->lokasiKavling)->nama_kavling ?? '-';

                $pdf->SetLineWidth(1.2);

                $pdf->Line(20, $yText + 3, $xText - 3, $yText + 3);

                $pdf->SetXY($xText, $yText);
                $pdf->Cell(0, 5, $namaKavling);

                $textWidth = $pdf->GetStringWidth($namaKavling);
                $pdf->Line(
                    $xText + $textWidth + 3,
                    $yText + 3,
                    190,
                    $yText + 3
                );

                $pdf->SetFont('Arial', '', 10);
                $pdf->SetXY(75, 29);
                $pdf->Cell(0, 5, optional($customer->ppjb)->no_ppjb ?? '-');

                 $pdf->SetXY(53.5, 40.5);
                $pdf->Cell(15, 5, $tanggal);

                $pdf->SetXY(69, 40.5);
                $pdf->Cell(30, 5, $bulan);

                $pdf->SetXY(98, 40.5);
                $pdf->Cell(20, 5, $tahun);

                $pdf->SetXY(66, 96);
                $pdf->Cell(0, 5, $customer->nama_lengkap ?? '-');

                $pdf->SetXY(66, 103);
                $pdf->Cell(0, 5, $ttl);

                $pdf->SetXY(66, 110);
                $pdf->MultiCell(140, 5, $customer->alamat_ktp ?? '-');

                $pdf->SetXY(66, 117);
                $pdf->Cell(0, 5, $customer->nik ?? '-');

                $pdf->SetFont('Arial', 'B', 10);
                $pdf->SetXY(130, 198);
                $luasTanah = optional($customer->kavlingPeta)->luas_bangunan ?? '-';
                $pdf->Cell(0, 5, $luasTanah !== '-' ? $luasTanah . ' m²' : '-');

                $pdf->SetXY(115, 204);
                $luasTanah = optional($customer->kavlingPeta)->luas_tanah ?? '-';
                $pdf->Cell(0, 5, $luasTanah !== '-' ? $luasTanah . ' m²' : '-');
                }

            if ($page == 3) {

                $pdf->SetFont('Arial', 'B', 25);

                $xText = 75;
                $yText = 8;

                $namaKavling = optional($customer->lokasiKavling)->nama_kavling ?? '-';

                $pdf->SetLineWidth(1.2);

                $pdf->Line(20, $yText + 3, $xText - 3, $yText + 3);

                $pdf->SetXY($xText, $yText);
                $pdf->Cell(0, 5, $namaKavling);

                $textWidth = $pdf->GetStringWidth($namaKavling);
                $pdf->Line(
                    $xText + $textWidth + 3,
                    $yText + 3,
                    190,
                    $yText + 3
                );

                $pdf->SetFont('Arial', '', 10);

                $pdf->SetXY(110, 32.4);
                $pdf->Cell(0, 5, optional($customer->lokasiKavling)->nama_kavling ?? '-');

                $hargaJual = optional($customer->kavlingPeta)->hrg_jual;

                $hargaFormat = $hargaJual
                    ? number_format($hargaJual, 0, ',', '.')
                    : '-';

                $pdf->SetFont('Arial', 'B', 10);
                $pdf->SetXY(94, 134);
                $pdf->Cell(0, 5, $hargaFormat);

                $pdf->SetFont('Arial', '', 10);
                $pdf->SetXY(94, 139);
                $pdf->MultiCell(
                    110,
                    10,
                    $hargaJual
                        ? '(' . ucwords($this->terbilang($hargaJual)) . ' Rupiah)'
                        : '-'
                );
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->SetXY(94, 147.4);
                $pdf->Cell(0, 5, $totalKategori2Format);

                $pdf->SetFont('Arial', '', 10);
                $pdf->SetXY(94, 155);
                $pdf->MultiCell(
                    110,
                    5,
                    $totalKategori2
                        ? '(' . ucwords($this->terbilang($totalKategori2)) . ' Rupiah)'
                        : '-'
                );

                $pdf->SetFont('Arial', 'B', 10);
                $pdf->SetXY(94, 174.4);
                $pdf->Cell(0, 5, $totalKategori1Format);

                $pdf->SetFont('Arial', '', 10);
                $pdf->SetXY(94, 180);
                $pdf->MultiCell(
                    110,
                    6,
                    $totalKategori1
                        ? '(' . ucwords($this->terbilang($totalKategori1)) . ' Rupiah)'
                        : '-'
                );
            }
            if($page == 4){
              $pdf->SetFont('Arial', 'B', 25);

                $xText = 75;
                $yText = 8;

                $namaKavling = optional($customer->lokasiKavling)->nama_kavling ?? '-';

                $pdf->SetLineWidth(1.2);

                $pdf->Line(20, $yText + 3, $xText - 3, $yText + 3);

                $pdf->SetXY($xText, $yText);
                $pdf->Cell(0, 5, $namaKavling);

                $textWidth = $pdf->GetStringWidth($namaKavling);
                $pdf->Line(
                    $xText + $textWidth + 3,
                    $yText + 3,
                    190,
                    $yText + 3
                );
            }
            if($page == 5){
                $pdf->SetFont('Arial', 'B', 25);

                $xText = 75;
                $yText = 8;

                $namaKavling = optional($customer->lokasiKavling)->nama_kavling ?? '-';

                $pdf->SetLineWidth(1.2);

                $pdf->Line(20, $yText + 3, $xText - 3, $yText + 3);

                $pdf->SetXY($xText, $yText);
                $pdf->Cell(0, 5, $namaKavling);

                $textWidth = $pdf->GetStringWidth($namaKavling);
                $pdf->Line(
                    $xText + $textWidth + 3,
                    $yText + 3,
                    190,
                    $yText + 3
                );
            }
            if($page == 6){
                $pdf->SetFont('Arial', 'B', 25);

                $xText = 75;
                $yText = 8;

                $namaKavling = optional($customer->lokasiKavling)->nama_kavling ?? '-';

                $pdf->SetLineWidth(1.2);

                $pdf->Line(20, $yText + 3, $xText - 3, $yText + 3);

                $pdf->SetXY($xText, $yText);
                $pdf->Cell(0, 5, $namaKavling);

                $textWidth = $pdf->GetStringWidth($namaKavling);
                $pdf->Line(
                    $xText + $textWidth + 3,
                    $yText + 3,
                    190,
                    $yText + 3
                );
            }
            if($page == 7){
                $pdf->SetFont('Arial', 'B', 25);

                $xText = 75;
                $yText = 8;

                $namaKavling = optional($customer->lokasiKavling)->nama_kavling ?? '-';

                $pdf->SetLineWidth(1.2);

                $pdf->Line(20, $yText + 3, $xText - 3, $yText + 3);

                $pdf->SetXY($xText, $yText);
                $pdf->Cell(0, 5, $namaKavling);

                $textWidth = $pdf->GetStringWidth($namaKavling);
                $pdf->Line(
                    $xText + $textWidth + 3,
                    $yText + 3,
                    190,
                    $yText + 3
                );
            }
            if($page == 8){
                $pdf->SetFont('Arial', 'B', 25);

                $xText = 75;
                $yText = 8;

                $namaKavling = optional($customer->lokasiKavling)->nama_kavling ?? '-';

                $pdf->SetLineWidth(1.2);

                $pdf->Line(20, $yText + 3, $xText - 3, $yText + 3);

                $pdf->SetXY($xText, $yText);
                $pdf->Cell(0, 5, $namaKavling);

                $textWidth = $pdf->GetStringWidth($namaKavling);
                $pdf->Line(
                    $xText + $textWidth + 3,
                    $yText + 3,
                    190,
                    $yText + 3
                );
            }
            if($page == 9){
                $pdf->SetFont('Arial', 'B', 25);

                $xText = 75;
                $yText = 8;

                $namaKavling = optional($customer->lokasiKavling)->nama_kavling ?? '-';

                $pdf->SetLineWidth(1.2);

                $pdf->Line(20, $yText + 3, $xText - 3, $yText + 3);

                $pdf->SetXY($xText, $yText);
                $pdf->Cell(0, 5, $namaKavling);

                $textWidth = $pdf->GetStringWidth($namaKavling);
                $pdf->Line(
                    $xText + $textWidth + 3,
                    $yText + 3,
                    190,
                    $yText + 3
                );
            }
            if($page == 10){
                $pdf->SetFont('Arial', 'B', 25);

                $xText = 75;
                $yText = 8;

                $namaKavling = optional($customer->lokasiKavling)->nama_kavling ?? '-';

                $pdf->SetLineWidth(1.2);

                $pdf->Line(20, $yText + 3, $xText - 3, $yText + 3);

                $pdf->SetXY($xText, $yText);
                $pdf->Cell(0, 5, $namaKavling);

                $textWidth = $pdf->GetStringWidth($namaKavling);
                $pdf->Line(
                    $xText + $textWidth + 3,
                    $yText + 3,
                    190,
                    $yText + 3
                );
            }
            if($page == 11){
                $pdf->SetFont('Arial', 'B', 25);

                $xText = 75;
                $yText = 8;

                $namaKavling = optional($customer->lokasiKavling)->nama_kavling ?? '-';

                $pdf->SetLineWidth(1.2);

                $pdf->Line(20, $yText + 3, $xText - 3, $yText + 3);

                $pdf->SetXY($xText, $yText);
                $pdf->Cell(0, 5, $namaKavling);

                $textWidth = $pdf->GetStringWidth($namaKavling);
                $pdf->Line(
                    $xText + $textWidth + 3,
                    $yText + 3,
                    190,
                    $yText + 3
                );
            }
        }

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'inline; filename="PPJB-KPR-' . $customer->kode_customer . '.pdf"'
            );
    }

     private function terbilang($angka)
    {
        $angka = abs((int) $angka);

        $bilangan = [
            '', 'satu', 'dua', 'tiga', 'empat', 'lima',
            'enam', 'tujuh', 'delapan', 'sembilan',
            'sepuluh', 'sebelas'
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
}
