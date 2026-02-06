<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\KavlingPeta;
use App\Models\LokasiKavling;
use App\Traits\LogAktivitasTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use TCPDF;

class KavlingController extends Controller
{
    use LogAktivitasTrait;

    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = KavlingPeta::with('lokasi');

            if ($request->id_lokasi && $request->id_lokasi != 0) {
                $data->where('id_lokasi', $request->id_lokasi);
            }

            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('panjang', function ($row) {
                    return '
                <p>Pjg Kanan : <strong>'.$row->panjang_kanan.' m</strong></p>
                <p>Pjg Kiri : <strong>'.$row->panjang_kiri.' m</strong></p>
            ';
                })
                ->addColumn('lebar', function ($row) {
                    return '
                <p>Lebar Depan: <strong>'.$row->lebar_depan.' m</strong></p>
                <p>Lebar Belakang: <strong>'.$row->lebar_belakang.' m</strong></p>
            ';
                })
                ->addColumn('luas', function ($row) {
                    return '
                <p>Luas Tanah: <strong>'.$row->luas_tanah.' m</strong></p>
                <p>Luas Bangunan: <strong>'.$row->luas_bangunan.' m</strong></p>
            ';
                })
                ->editColumn('rincian_harga', function ($row) {
                    return '
        <div class="w-100">
            <div class="d-flex justify-content-between harga-format">
                <span>Harga Rumah : </span>
                <span>Rp. '.number_format($row->hrg_jual, 0, ',', '.').'</span>
            </div>
            <div class="d-flex justify-content-between harga-format">
                <span>Biaya Surat : </span>
                <span>Rp. '.number_format($row->biaya_surat, 0, ',', '.').'</span>
            </div>
            <div class="d-flex justify-content-between harga-format">
                <span>Peningkatan Mutu : </span>
                <span>Rp. '.number_format($row->biaya_lain, 0, ',', '.').'</span>
            </div>
        </div>
    ';
                })

                ->addColumn('total_harga', function ($row) {
                    $total = $row->hrg_jual + $row->biaya_surat + $row->biaya_lain;

                    return '
        <div class="d-flex justify-content-between harga-format w-100">
            <span>Rp.</span>
            <span>'.number_format($total, 0, ',', '.').'</span>
        </div>
    ';
                })

                ->addColumn('id_lokasi', fn ($row) => $row->lokasi->nama_kavling ?? '-')
                ->addColumn('action', function ($row) use ($permissions): string {
                    $editUrl = route('kavling.edit', $row->id);
                    $showUrl = route('kavling.show', $row->id);

                    $btn = '<div class="text-center">';

                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-sm edit-button mr-1" data-id="'.e($row->id).'" data-url="'.e($editUrl).'">Edit</button>';
                        $btn .= '<button class="btn btn-success btn-sm foto-button" data-id="'.e($row->id).'" data-url="'.e($showUrl).'">Foto</button>';
                    }

                    $btn .= '</div>';

                    return $btn;
                })
                ->rawColumns(['panjang', 'lebar', 'luas', 'rincian_harga', 'total_harga', 'action', 'id_lokasi'])
                ->make(true);
        }

        $lokasiList = LokasiKavling::all();

        return view('admin.master.kavling.index', compact('permissions', 'lokasiList'));
    }

    public function edit($id)
    {
        $data = KavlingPeta::with('lokasi')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function show($id)
    {
        $data = KavlingPeta::with('lokasi')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = KavlingPeta::findOrFail($id);

        $rules = [
            'panjang_kanan' => 'required',
            'panjang_kiri' => 'required',
            'lebar_depan' => 'required',
            'lebar_belakang' => 'required',
            'luas_tanah' => 'required',
            'luas_bangunan' => 'required',
            'hrg_meter' => 'required',
            'tipe_bangunan' => 'required',
            'hrg_jual' => 'required',
        ];

        $messages = [
            'panjang_kanan.required' => 'Panjang kanan wajib diisi.',
            'panjang_kiri.required' => 'Panjang kiri wajib diisi.',
            'lebar_depan.required' => 'Lebar depan wajib diisi.',
            'lebar_belakang.required' => 'Lebar belakang wajib diisi.',
            'luas_tanah.required' => 'Luas tanah wajib diisi.',
            'luas_bangunan.required' => 'Luas bangunan wajib diisi.',
            'hrg_meter.required' => 'Harga per meter wajib diisi.',
            'tipe_bangunan.required' => 'Tipe rumah wajib diisi.',
            'hrg_jual.required' => 'Harga jual wajib diisi.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {
            $db = [
                'panjang_kanan' => $request->panjang_kanan,
                'panjang_kiri' => $request->panjang_kiri,
                'lebar_depan' => $request->lebar_depan,
                'lebar_belakang' => $request->lebar_belakang,
                'luas_tanah' => $request->luas_tanah,
                'luas_bangunan' => $request->luas_bangunan,
                'hrg_meter' => str_replace('.', '', $request->hrg_meter ?? 0),
                'tipe_bangunan' => str_replace('.', '', $request->tipe_bangunan ?? 0),
                'hrg_jual' => str_replace('.', '', $request->hrg_jual ?? 0),
                'daya_listrik' => str_replace('.', '', $request->daya_listrik ?? 0),
                'keterangan' => $request->keterangan ?? '',
                'no_sertifikat' => $request->no_sertifikat ?? '',
            ];

            $data->update($db);
            $this->logEdit('Kavling', $data->id);

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateFoto(Request $request, $id)
    {
        $data = KavlingPeta::findOrFail($id);

        $rules = [
            'foto' => ($data->foto == null || $data->foto == '')
                ? 'required|mimes:jpg,jpeg,png,webp|max:2048'
                : 'nullable|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        $messages = [
            'foto.required' => 'Foto wajib diupload jika belum ada.',
            'foto.mimes' => 'Foto harus berformat JPG, JPEG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $ext = $foto->getClientOriginalExtension();

                $filename = Str::random(25).'.'.$ext;
                $foto->move(public_path('assets/foto_kavling/'), $filename);
            }

            $db = [
                'foto' => $filename ?? $data->foto,
            ];

            $data->update($db);

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function cetakPdf(Request $request, $id_lokasi)
    {
        $query = KavlingPeta::with('lokasi');

        if ($id_lokasi && $id_lokasi != 0) {
            $query->where('id_lokasi', $id_lokasi);
        }

        $data = $query->get();

        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Laravel');
        $pdf->SetAuthor('App');
        $pdf->SetTitle('Data Kavling');
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 14);
        $namaLokasi = 'Semua Lokasi';

        if ($request->id_lokasi && $request->id_lokasi != 0) {
            $namaLokasi = LokasiKavling::find($request->id_lokasi)->nama_kavling ?? 'Semua Lokasi';
        }

        $pdf->Cell(0, 10, 'Data Kavling '.$namaLokasi, 0, 1, 'C');

        $pdf->Ln(5);

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(220, 220, 220);

        $pdf->Cell(10, 10, 'No', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Perumahan', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Kode Kavling', 1, 0, 'C', true);
        $pdf->Cell(55, 10, 'Panjang', 1, 0, 'C', true);
        $pdf->Cell(55, 10, 'Lebar', 1, 0, 'C', true);
        $pdf->Cell(55, 10, 'Luas', 1, 0, 'C', true);
        $pdf->Cell(35, 10, 'Harga', 1, 1, 'C', true);

        $pdf->SetFont('helvetica', '', 9);
        $no = 1;

        foreach ($data as $row) {
            $pdf->Cell(10, 10, $no++, 1, 0, 'C');
            $pdf->Cell(40, 10, $row->lokasi->nama_kavling ?? '-', 1, 0);
            $pdf->Cell(30, 10, $row->kode_kavling ?? '-', 1, 0);

            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->MultiCell(55, 10,
                "Kanan: {$row->panjang_kanan} m\nKiri: {$row->panjang_kiri} m",
                1, 'L', false, 0, '', '', true, 0, false, true, 10, 'M'
            );
            $pdf->MultiCell(55, 10,
                "Depan: {$row->lebar_depan} m\nBelakang: {$row->lebar_belakang} m",
                1, 'L', false, 0, '', '', true, 0, false, true, 10, 'M'
            );
            $pdf->MultiCell(55, 10,
                "Tanah: {$row->luas_tanah} m²\nBangunan: {$row->luas_bangunan} m²",
                1, 'L', false, 0, '', '', true, 0, false, true, 10, 'M'
            );

            $pdf->Cell(35, 10, 'Rp '.number_format($row->hrg_jual, 0, ',', '.'), 1, 1, 'R');
        }

        $pdf->Output('data_kavling.pdf', 'I');
    }

    public function cetakExcel(Request $request, $id_lokasi)
    {
        $query = KavlingPeta::with('lokasi');

        if ($id_lokasi && $id_lokasi != 0) {
            $query->where('id_lokasi', $id_lokasi);
        }

        $data = $query->get();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Kavling '.($id_lokasi ? LokasiKavling::find($id_lokasi)->nama_kavling : 'Semua Lokasi'));

        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'Data Kavling');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = ['No', 'Perumahan', 'Kode Kavling', 'Panjang', 'Lebar', 'Luas', 'Harga'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col.'2', $header);
            $sheet->getStyle($col.'2')->getFont()->setBold(true);
            $sheet->getStyle($col.'2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $rowNum = 3;
        $no = 1;
        foreach ($data as $row) {
            $sheet->setCellValue("A{$rowNum}", $no++);
            $sheet->setCellValue("B{$rowNum}", $row->lokasi->nama_kavling ?? '-');
            $sheet->setCellValue("C{$rowNum}", $row->kode_kavling ?? '-');

            $sheet->setCellValue("D{$rowNum}", "Kanan: {$row->panjang_kanan} m\nKiri: {$row->panjang_kiri} m");
            $sheet->setCellValue("E{$rowNum}", "Depan: {$row->lebar_depan} m\nBelakang: {$row->lebar_belakang} m");
            $sheet->setCellValue("F{$rowNum}", "Tanah: {$row->luas_tanah} m²\nBangunan: {$row->luas_bangunan} m²");

            $sheet->setCellValue("G{$rowNum}", $row->hrg_jual);

            $sheet->getStyle("D{$rowNum}:F{$rowNum}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("A{$rowNum}:G{$rowNum}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->getStyle("G{$rowNum}")
                ->getNumberFormat()
                ->setFormatCode('#,##0');

            $rowNum++;
        }

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle('A2:G'.($rowNum - 1))->applyFromArray($styleArray);

        foreach (range(2, $rowNum - 1) as $r) {
            $sheet->getRowDimension($r)->setRowHeight(-1);
        }

        $fileName = 'data_kavling.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
