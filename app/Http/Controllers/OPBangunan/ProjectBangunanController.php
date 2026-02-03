<?php
namespace App\Http\Controllers\OPBangunan;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\Bank;
use App\Models\FotoProyekBangunan;
use App\Models\JenisPekerjaanBangunan;
use App\Models\KavlingPeta;
use App\Models\LokasiKavling;
use App\Models\Pengeluaran;
use App\Models\ProyekBangunan;
use App\Models\ProyekBangunanBlok;
use App\Models\ProyekBangunanDetail;
use App\Models\ProyekBangunanDetailKerja;
use App\Models\ProyekBangunanUnit;
use App\Traits\LogAktivitasTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use TCPDF;
use Yajra\DataTables\Facades\DataTables;

class ProjectBangunanController extends Controller
{
    use LogAktivitasTrait;
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = ProyekBangunan::orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($row) {
                    return Carbon::parse($row->tanggal)->translatedFormat('d F Y');
                })
                ->addColumn('jumlah_unit', function ($row) {
                    $total          = (int) $row->nilai_pekerjaan;
                    $jumlahUnit     = number_format($row->jumlah_unit, 0, ',', '.');
                    $totalFormatted = 'Rp. ' . number_format($total, 0, ',', '.');
                    return "{$jumlahUnit} Unit<br>{$totalFormatted}";
                })
                ->addColumn('progres', function ($row) {
                    if ($row->id_bayar == 2) {
                        $detailWithTanggal = ProyekBangunanDetail::where('id_proyek_bangunan', $row->id)
                            ->whereNotNull('tanggal');

                        if (! $detailWithTanggal->exists()) {
                            return 'Belum Ada';
                        }

                        $lastDetail  = $detailWithTanggal->orderByDesc('op_ke')->first();
                        $totalPersen = $detailWithTanggal->sum('persen');
                    } else {
                        $lastDetail = ProyekBangunanDetail::where('id_proyek_bangunan', $row->id)
                            ->orderByDesc('op_ke')
                            ->first();

                        $totalPersen = ProyekBangunanDetail::where('id_proyek_bangunan', $row->id)
                            ->sum('persen');
                    }

                    $persenFormatted = number_format($totalPersen, 2, ',', '.') . ' %';

                    if ($lastDetail) {
                        $opKe = $lastDetail->op_ke;
                        return "OP ke : {$opKe}<br>Progres : {$persenFormatted}";
                    } else {
                        return 'Belum Ada';
                    }
                })
                ->addColumn('no_kontrak', function ($row) {
                    $namaProyek = $row->nama_proyek;
                    $noKontrak  = '<span class="badge bg-info text-dark">' . e($row->no_kontrak) . '</span>';
                    return $namaProyek . '<br>' . $noKontrak;
                })

                ->addColumn('id_bayar', function ($row) {
                    return $row->id_bayar == 1 ? 'Persentase' : ($row->id_bayar == 2 ? 'Termin' : '-');
                })
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl   = route('proyek-bangunan.edit', $row->id);
                    $showUrl   = route('proyek-bangunan.show', $row->id);
                    $deleteUrl = route('proyek-bangunan.destroy', $row->id);

                    $btn = '<div class="d-flex justify-content-center align-items-center gap-1">';
                    if ($permissions['edit']) {
                        $btn .= '<a class="btn btn-success btn-xs" href="' . e($showUrl) . '">Rekap Opname</a>';
                        $btn .= '<button class="btn btn-primary btn-xs mx-1 edit-button" data-id="' . e($row->id) . '" data-url="' . e($editUrl) . '">Edit</button>';
                    }

                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="delete-button btn btn-danger btn-xs">Hapus</button></form>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['jumlah_unit', 'progres', 'action', 'no_kontrak'])
                ->make(true);
        }

        $lokasiList = LokasiKavling::all();
        $bankList   = Bank::all();

        return view('admin.op_bangunan.proyek_bangunan.index', compact('permissions', 'lokasiList', 'bankList'));
    }

    public function getBlokByLokasi($id)
    {
        $blokList = KavlingPeta::where('id_lokasi', $id)
            ->pluck('kode_kavling')
            ->map(function ($kode) {
                if (preg_match('/^[A-Z]+/', $kode, $match)) {
                    return $match[0];
                }
                return null;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return response()->json($blokList);
    }

    public function getUnitByLokasiBlok(Request $request)
    {
        $blokList = (array) $request->blok;

        $unitList = KavlingPeta::where('id_lokasi', $request->id_lokasi)
            ->where(function ($query) use ($blokList) {
                foreach ($blokList as $blok) {
                    $query->orWhere('kode_kavling', 'like', $blok . '-%');
                }
            })
            ->pluck('kode_kavling')
            ->sortBy(function ($item) {
                preg_match('/^([A-Z]+)/', $item, $blokMatch);
                $blok = $blokMatch[1] ?? '';

                preg_match('/-(\d+)/', $item, $numMatch);
                $nomor = isset($numMatch[1]) ? (int) $numMatch[1] : 0;

                return sprintf('%s-%04d', $blok, $nomor);
            })
            ->values();

        return response()->json($unitList);
    }

    public function edit($id)
    {
        $proyek = ProyekBangunan::find($id);

        $blokRumah = ProyekBangunanBlok::where('id_proyek_bangunan', $id)
            ->pluck('blok')
            ->toArray();

        $unitRumah = ProyekBangunanUnit::where('id_proyek_bangunan', $id)
            ->pluck('kode_kavling')
            ->toArray();

        $terminList = ProyekBangunanDetail::where('id_proyek_bangunan', $id)
            ->orderBy('id')
            ->pluck('persen')
            ->take($proyek->jumlah_termin)
            ->toArray();

        return response()->json([
            'status' => 'success',
            'data'   => $proyek,
            'blok'   => $blokRumah,
            'unit'   => $unitRumah,
            'termin' => $terminList,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'no_kontrak'       => 'required|unique:proyek_bangunan,no_kontrak',
            'tanggal'          => 'required|date',
            'nama_proyek'      => 'required',
            'nama_pemborong'   => 'required',
            'id_lokasi'        => 'required',
            'id_bank'          => 'required',
            'blok'             => 'required|array|min:1',
            'blok.*'           => 'string',
            'unit_rumah'       => 'required|array',
            'unit_rumah.*'     => 'string',
            'jumlah_unit'      => 'required|min:1',
            'tipe_rumah'       => 'required',
            'volume_pekerjaan' => 'required',
            'harga_satuan'     => 'required',
            'nilai_pekerjaan'  => 'required',
            'id_bayar'         => 'required',
            'jumlah_termin'    => 'required_if:id_bayar,2|nullable',
            'termin'           => 'required_if:id_bayar,2|array',
            'termin.*'         => 'required_if:id_bayar,2|numeric|min:0|max:100',
        ];

        $messages = [
            'no_kontrak.required'       => 'No Kontrak wajib diisi.',
            'no_kontrak.unique'         => 'No Kontrak sudah digunakan.',
            'tanggal.required'          => 'Tanggal wajib diisi.',
            'tanggal.date'              => 'Tanggal tidak valid.',
            'nama_proyek.required'      => 'Nama Proyek wajib diisi.',
            'nama_pemborong.required'   => 'Nama Pemborong wajib diisi.',
            'id_lokasi.required'        => 'Lokasi wajib dipilih.',
            'id_bank.required'          => 'Rekening wajib dipilih.',
            'blok.required'             => 'Blok wajib dipilih.',
            'blok.*.string'             => 'Blok tidak valid.',
            'unit_rumah.required'       => 'Unit Rumah wajib dipilih.',
            'unit_rumah.*.string'       => 'Unit Rumah tidak valid.',
            'jumlah_unit.required'      => 'Jumlah Unit wajib diisi.',
            'jumlah_unit.min'           => 'Jumlah Unit tidak valid.',
            'tipe_rumah.required'       => 'Tipe Rumah wajib dipilih.',
            'volume_pekerjaan.required' => 'Volume Pekerjaan wajib diisi.',
            'harga_satuan.required'     => 'Harga Satuan wajib diisi.',
            'nilai_pekerjaan.required'  => 'Nilai Pekerjaan wajib diisi.',
            'id_bayar.required'         => 'Metode Pembayaran wajib dipilih.',
            'jumlah_termin.required_if' => 'Jumlah Termin wajib diisi.',
            'jumlah_termin.numeric'     => 'Jumlah Termin tidak valid.',
            'jumlah_termin.min'         => 'Jumlah Termin tidak valid.',
            'jumlah_termin.max'         => 'Jumlah Termin tidak valid.',
            'termin.required_if'        => 'Termin wajib diisi.',
            'termin.*.required_if'      => 'Termin wajib diisi.',
            'termin.*.numeric'          => 'Termin tidak valid.',
            'termin.*.min'              => 'Termin tidak valid.',
            'termin.*.max'              => 'Termin tidak valid.',
        ];

        $request->validate($rules, $messages);

        if ($request->id_bayar == 2) {
            $totalTermin = collect($request->termin)
                ->filter(fn($val) => $val !== null)
                ->sum();

            if ($totalTermin != 100) {
                $messages = [];
                foreach ($request->termin as $index => $val) {
                    $messages["termin.$index"] = 'Total semua termin harus 100.';
                }

                throw ValidationException::withMessages($messages);
            }
        }

        $data = [
            'no_kontrak'       => $request->no_kontrak,
            'tanggal'          => $request->tanggal,
            'nama_proyek'      => $request->nama_proyek,
            'nama_pemborong'   => $request->nama_pemborong,
            'id_lokasi'        => $request->id_lokasi,
            'id_bank'          => $request->id_bank,
            'tipe_rumah'       => str_replace('.', '', $request->tipe_rumah),
            'volume_pekerjaan' => str_replace('.', '', $request->volume_pekerjaan),
            'harga_satuan'     => str_replace('.', '', $request->harga_satuan),
            'nilai_pekerjaan'  => str_replace('.', '', $request->nilai_pekerjaan),
            'jumlah_unit'      => $request->jumlah_unit,
            'id_bayar'         => $request->id_bayar,
            'jumlah_termin'    => $request->jumlah_termin ?? 0,
        ];

        $proyek = ProyekBangunan::create($data);
        $this->logCreate('Proyek Bangunan', $proyek->id);

        foreach ($request->unit_rumah as $kodeUnit) {
            ProyekBangunanUnit::create([
                'id_proyek_bangunan' => $proyek->id,
                'kode_kavling'       => $kodeUnit,
            ]);
        }

        foreach ($request->blok as $b) {
            ProyekBangunanBlok::create([
                'id_proyek_bangunan' => $proyek->id,
                'blok'               => $b,
            ]);
        }

        if ($request->id_bayar == 2 && is_array($request->termin)) {
            foreach ($request->termin as $index => $persen) {
                $proyekDetail = ProyekBangunanDetail::create([
                    'id_proyek_bangunan' => $proyek->id,
                    'op_ke'              => $index + 1,
                    'tanggal'            => null,
                    'persen'             => $persen,
                    'nilai_pekerjaan'    => 0,
                ]);

                $jenis_pekerjaan = JenisPekerjaanBangunan::where('id_lokasi', $proyek->id_lokasi)->get();
                foreach ($jenis_pekerjaan as $jenis) {
                    ProyekBangunanDetailKerja::create([
                        'id_proyek_bangunan'        => $proyek->id,
                        'id_proyek_bangunan_detail' => $proyekDetail->id,
                        'id_jenis_pekerjaan'        => $jenis->id,
                        'op_lalu'                   => 0,
                        'op_sekarang'               => 0,
                    ]);
                }
            }
        }

        return response()->json(['status' => 'success']);
    }

    public function update(Request $request, $id)
    {
        $data = ProyekBangunan::findOrFail($id);

        $request->validate([
            'no_kontrak'     => 'required|unique:proyek_bangunan,no_kontrak,' . $data->id . ',id',
            'tanggal'        => 'required|date',
            'nama_proyek'    => 'required',
            'nama_pemborong' => 'required',
        ], [
            'no_kontrak.required'     => 'No. Kontrak wajib diisi.',
            'no_kontrak.unique'       => 'No. Kontrak sudah digunakan.',
            'tanggal.required'        => 'Tanggal wajib diisi.',
            'tanggal.date'            => 'Tanggal tidak valid.',
            'nama_proyek.required'    => 'Nama proyek wajib diisi.',
            'nama_pemborong.required' => 'Nama pemborong wajib diisi.',
        ]);

        $db = [
            'no_kontrak'     => $request->no_kontrak,
            'tanggal'        => $request->tanggal,
            'nama_proyek'    => $request->nama_proyek,
            'nama_pemborong' => $request->nama_pemborong,
        ];

        $data->update($db);
        $this->logEdit('Proyek Bangunan', $data->id);

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        $data = ProyekBangunan::findOrFail($id);

        $details = ProyekBangunanDetail::where('id_proyek_bangunan', $id)->get();

        foreach ($details as $detail) {
            $fotos = FotoProyekBangunan::where('id_proyek_bangunan_detail', $detail->id)->get();
            foreach ($fotos as $foto) {
                $path = public_path('assets/proyek_bangunan/' . $foto->foto);
                if (file_exists($path)) {
                    unlink($path);
                }
            }

            FotoProyekBangunan::where('id_proyek_bangunan_detail', $detail->id)->delete();
        }

        ProyekBangunanDetail::where('id_proyek_bangunan', $id)->delete();

        ProyekBangunanBlok::where('id_proyek_bangunan', $id)->delete();

        ProyekBangunanUnit::where('id_proyek_bangunan', $id)->delete();

        ProyekBangunanDetailKerja::where('id_proyek_bangunan', $id)->delete();

        $this->logDelete('Proyek Bangunan', $data->id);
        $data->delete();

        return response()->json(['status' => 'success']);
    }

    public function show(Request $request, $id)
    {
        Carbon::setLocale('id');

        if ($request->ajax()) {
            $data = ProyekBangunanDetail::where('id_proyek_bangunan', $id)->orderBy('id', 'asc');

            $total = (clone $data)->get()->reduce(function ($carry, $item) {
                $carry['persen'] += $item->persen;
                $carry['nilai'] += $item->nilai_pekerjaan;
                return $carry;
            }, ['persen' => 0, 'nilai' => 0]);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($row) {
                    return $row->tanggal ? Carbon::parse($row->tanggal)->translatedFormat('d F Y') : 'Belum Ada';
                })
                ->addColumn('persen', function ($row) {
                    return number_format($row->persen, 2, ',', '.') . ' %';
                })
                ->addColumn('nilai_pekerjaan', function ($row) {
                    return '<div class="d-flex justify-content-between"><span>Rp.</span><span>' . number_format($row->nilai_pekerjaan, 0, ',', '.') . '</span></div>';
                })
                ->addColumn('action', function ($row) use ($id) {
                    $showUrl   = route('proyek-bangunan.detail', $row->id);
                    $deleteUrl = route('proyek-bangunan.detail-destroy', $row->id);
                    $proyek    = ProyekBangunan::findOrFail($row->id_proyek_bangunan);

                    $lastDetailId = ProyekBangunanDetail::where('id_proyek_bangunan', $id)
                        ->orderByDesc('id')
                        ->value('id');

                    $btn = '<div class="d-flex justify-content-center align-items-center gap-1">';
                    $btn .= '<a class="btn btn-success btn-xs mr-1" href="' . e($showUrl) . '">Detail Opname</a>';

                    if ($row->tanggal === null) {
                        $btn .= '<button class="btn btn-dark btn-xs mr-1" disabled>Cetak OP</button>';
                        $btn .= '<button class="btn btn-dark btn-xs mr-1" disabled>Cetak Rekapitulasi</button>';
                    } else {
                        $btn .= '<a href="' . route('proyek-bangunan.cetak-opname', [$row->id_proyek_bangunan, $row->op_ke]) . '" target="_blank" class="btn btn-dark btn-xs mr-1">Cetak OP</a>';
                        $btn .= '<a href="' . route('proyek-bangunan.cetak-rekapitulasi', [$row->id_proyek_bangunan, $row->op_ke]) . '" target="_blank" class="btn btn-dark btn-xs mr-1">Cetak Rekapitulasi</a>';
                    }

                    if ($proyek->id_bayar == 1) {
                        if ($row->id == $lastDetailId) {
                            $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">' .
                            csrf_field() . method_field('DELETE') .
                                '<button type="submit" class="delete-button btn btn-danger btn-xs">Hapus</button></form>';
                        } else {
                            $btn .= '<button class="btn btn-danger btn-xs" disabled>Hapus</button>';
                        }
                    }

                    $btn .= '</div>';
                    return $btn;
                })

                ->with([
                    'total_persen' => number_format($total['persen'], 2, ',', '.') . ' %',
                    'total_nilai'  => '<div class="d-flex justify-content-between"><span>Rp.</span><span>' . number_format($total['nilai'], 0, ',', '.') . '</span></div>',
                ])
                ->rawColumns(['nilai_pekerjaan', 'action'])
                ->make(true);
        }

        $proyek        = ProyekBangunan::findOrFail($id);
        $jumlah_terisi = ProyekBangunanDetail::where('id_proyek_bangunan', $id)
            ->whereNotNull('tanggal')
            ->count();
        $tampilkan_tombol = ! ($proyek->id_bayar == 2 && $jumlah_terisi >= $proyek->jumlah_termin);

        return view('admin.op_bangunan.proyek_bangunan.detail.detail', compact('proyek', 'tampilkan_tombol'));
    }

    public function createDetail(Request $request, $id)
    {
        Carbon::setLocale('id');

        $proyekBangunan = ProyekBangunan::findOrFail($id);

        if ($request->ajax()) {
            $data = JenisPekerjaanBangunan::where('id_lokasi', $proyekBangunan->id_lokasi)->orderBy('id', 'asc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('presentasi', function ($row) {
                    return $row->presentasi . ' %';
                })
                ->addColumn('op_lalu', function ($row) use ($id) {
                    $nilai = ProyekBangunanDetailKerja::where('id_proyek_bangunan', $id)
                        ->where('id_jenis_pekerjaan', $row->id)
                        ->sum('op_sekarang');

                    return number_format((float) $nilai, 2) . ' %';
                })
                ->addColumn('op_sekarang', function ($row) use ($id) {
                    $opLalu = ProyekBangunanDetailKerja::where('id_proyek_bangunan', $id)
                        ->where('id_jenis_pekerjaan', $row->id)
                        ->sum('op_sekarang');

                    $opLalu = $opLalu ?? 0;

                    $disabled = $opLalu >= 100 ? 'disabled' : '';

                    $input = '
        <div class="input-group input-group-sm">
            <input type="number" name="op_sekarang[]" class="form-control op-sekarang" max="100" data-id="' . $row->id . '" value="0" ' . $disabled . '>
            <div class="input-group-append">
                <span class="input-group-text">%</span>
            </div>
        </div>
    ';

                    if ($disabled) {
                        $input .= '<input type="hidden" name="op_sekarang[]" value="0">';
                    }

                    return $input;
                })
                ->rawColumns(['op_sekarang', 'op_lalu'])
                ->make(true);
        }

        $proyek = ProyekBangunan::findOrFail($id);

        if ($proyek->id_bayar == 2) {
            $lastDetail = ProyekBangunanDetail::where('id_proyek_bangunan', $id)
                ->whereNotNull('tanggal')
                ->orderByDesc('op_ke')
                ->first();

            $op_ke = $lastDetail ? $lastDetail->op_ke + 1 : 1;
        } else {
            $jumlahOp = ProyekBangunanDetail::where('id_proyek_bangunan', $id)->count();
            $op_ke    = $jumlahOp + 1;
        }

        return view('admin.op_bangunan.proyek_bangunan.detail.create', compact('proyek', 'op_ke'));
    }

    public function storeDetail(Request $request, $id)
    {
        $rules = [
            'tanggal'       => 'required|date',
            'foto.*'        => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'op_sekarang'   => 'nullable|array',
            'op_sekarang.*' => 'nullable',
        ];

        $messages = [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date'     => 'Tanggal tidak valid.',
            'foto.required'    => 'Foto wajib diunggah.',
            'foto.array'       => 'Format upload foto tidak valid.',
            'foto.*.required'  => 'Foto wajib diunggah.',
            'foto.*.image'     => 'Setiap file harus berupa gambar.',
            'foto.*.mimes'     => 'Format gambar harus jpeg, png, atau jpg.',
            'foto.*.max'       => 'Ukuran gambar maksimal 2MB per file.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {
            $nilaiDasar = ProyekBangunan::findOrFail($id);

            $presentasiData = JenisPekerjaanBangunan::where('id_lokasi', $nilaiDasar->id_lokasi)->orderBy('id', 'asc')->pluck('presentasi', 'id')->toArray();
            $opSekarang     = $request->op_sekarang;

            $totalPersen = 0;

            foreach ($opSekarang as $index => $op) {
                $idJenis    = array_keys($presentasiData)[$index];
                $presentasi = $presentasiData[$idJenis] ?? 0;
                $op         = floatval($op);
                $totalPersen += ($presentasi * $op) / 100;
            }

            $nilaiPekerjaan = ($totalPersen / 100) * $nilaiDasar->nilai_pekerjaan;

            if ($nilaiDasar->id_bayar == 1) {

                $data = [
                    'id_proyek_bangunan' => $id,
                    'op_ke'              => $request->op_ke,
                    'tanggal'            => $request->tanggal,
                    'persen'             => $totalPersen,
                    'nilai_pekerjaan'    => (int) $nilaiPekerjaan,
                ];

                $proyekDetail = ProyekBangunanDetail::create($data);

                Pengeluaran::create([
                    'id_proyek_bangunan_detail' => $proyekDetail->id,
                    'id_bank'                   => $nilaiDasar->id_bank,
                    'tanggal'                   => $request->tanggal,
                    'nominal'                   => $nilaiPekerjaan,
                    'id_kategori_transaksi'     => 13,
                    'keterangan'                => 'Pembayaran OP Proyek Bangunan' . $nilaiDasar->nama_proyek . ' ke-' . $proyekDetail->op_ke,
                    'lampiran'                  => '',
                ]);

                $files = $request->file('foto');
                foreach ($files as $file) {
                    $ext  = $file->getClientOriginalExtension();
                    $foto = Str::random(25) . '.' . $ext;
                    $file->move(public_path('assets/proyek_bangunan/'), $foto);

                    FotoProyekBangunan::create([
                        'id_proyek_bangunan_detail' => $proyekDetail->id,
                        'foto'                      => $foto,
                    ]);
                }

                foreach ($opSekarang as $index => $op) {
                    $idJenis = array_keys($presentasiData)[$index];
                    $op      = (int) $op;

                    $opLalu = ProyekBangunanDetailKerja::where('id_proyek_bangunan', $id)
                        ->where('id_jenis_pekerjaan', $idJenis)
                        ->latest('id')
                        ->value('op_sekarang') ?? 0;

                    ProyekBangunanDetailKerja::create([
                        'id_proyek_bangunan'        => $id,
                        'id_proyek_bangunan_detail' => $proyekDetail->id,
                        'id_jenis_pekerjaan'        => $idJenis,
                        'op_lalu'                   => $opLalu,
                        'op_sekarang'               => $op,
                    ]);
                }
            } elseif ($nilaiDasar->id_bayar == 2) {
                $proyekDetail = ProyekBangunanDetail::where('id_proyek_bangunan', $id)
                    ->whereNull('tanggal')
                    ->orderBy('id')
                    ->first();

                if (! $proyekDetail) {
                    throw new \Exception('Opname sudah lengkap semua.');
                }

                $fotosLama = FotoProyekBangunan::where('id_proyek_bangunan_detail', $proyekDetail->id)->get();

                foreach ($fotosLama as $foto) {
                    $path = public_path('assets/proyek_bangunan/' . $foto->foto);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                    $foto->delete();
                }

                $files = $request->file('foto');

                foreach ($files as $file) {
                    $ext  = $file->getClientOriginalExtension();
                    $foto = Str::random(25) . '.' . $ext;
                    $file->move(public_path('assets/proyek_bangunan/'), $foto);

                    FotoProyekBangunan::create([
                        'id_proyek_bangunan_detail' => $proyekDetail->id,
                        'foto'                      => $foto,
                    ]);
                }

                $proyekDetail->update([
                    'tanggal'         => $request->tanggal,
                    'persen'          => $totalPersen,
                    'nilai_pekerjaan' => (int) $nilaiPekerjaan,
                ]);

                foreach ($opSekarang as $index => $op) {
                    $idJenis = array_keys($presentasiData)[$index];
                    $op      = (int) $op;

                    $opLalu = ProyekBangunanDetailKerja::where('id_proyek_bangunan', $id)
                        ->where('id_proyek_bangunan_detail', '<', $proyekDetail->id)
                        ->where('id_jenis_pekerjaan', $idJenis)
                        ->sum('op_sekarang');

                    $detailKerja = ProyekBangunanDetailKerja::where('id_proyek_bangunan', $id)
                        ->where('id_proyek_bangunan_detail', $proyekDetail->id)
                        ->where('id_jenis_pekerjaan', $idJenis)
                        ->first();

                    if ($detailKerja) {
                        $detailKerja->update([
                            'op_lalu'     => $opLalu,
                            'op_sekarang' => $op,
                        ]);
                    } else {
                        ProyekBangunanDetailKerja::create([
                            'id_proyek_bangunan'        => $id,
                            'id_proyek_bangunan_detail' => $proyekDetail->id,
                            'id_jenis_pekerjaan'        => $idJenis,
                            'op_lalu'                   => $opLalu,
                            'op_sekarang'               => $op,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroyDetail($id)
    {
        $data = ProyekBangunanDetail::findOrFail($id);

        $pengeluaran = Pengeluaran::where('id_proyek_bangunan_detail', $data->id)->first();
        if ($pengeluaran) {
            if (! empty($pengeluaran->lampiran) && file_exists(public_path('assets/keuangan/pengeluaran/' . $pengeluaran->lampiran))) {
                unlink(public_path('assets/keuangan/pengeluaran/' . $pengeluaran->lampiran));
            }
            $pengeluaran->delete();
        }

        $fotos = FotoProyekBangunan::where('id_proyek_bangunan_detail', $data->id)->get();
        foreach ($fotos as $foto) {
            $path = public_path('assets/proyek_bangunan/' . $foto->foto);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        FotoProyekBangunan::where('id_proyek_bangunan_detail', $data->id)->delete();

        ProyekBangunanDetailKerja::where('id_proyek_bangunan_detail', $data->id)->delete();

        $data->delete();

        return response()->json(['status' => 'success']);
    }

    public function detail(Request $request, $id)
    {
        Carbon::setLocale('id');

        if ($request->ajax()) {
            $proyekDetail = ProyekBangunanDetail::findOrFail($id);

            $idProyek = $proyekDetail->id_proyek_bangunan;

            $proyekBangunan = ProyekBangunan::findOrFail($idProyek);

            $data = JenisPekerjaanBangunan::where('id_lokasi', $proyekBangunan->id_lokasi)->orderBy('id', 'asc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('presentasi', function ($row) {
                    return $row->presentasi . ' %';
                })
                ->addColumn('op_lalu', function ($row) use ($idProyek, $id) {
                    $opLalu = ProyekBangunanDetailKerja::where('id_proyek_bangunan', $idProyek)
                        ->where('id_proyek_bangunan_detail', '<', $id)
                        ->where('id_jenis_pekerjaan', $row->id)
                        ->sum('op_sekarang');

                    return number_format((float) $opLalu, 2) . ' %';
                })
                ->addColumn('op_sekarang', function ($row) use ($idProyek, $id) {
                    $opSekarang = ProyekBangunanDetailKerja::where('id_proyek_bangunan', $idProyek)
                        ->where('id_proyek_bangunan_detail', $id)
                        ->where('id_jenis_pekerjaan', $row->id)
                        ->value('op_sekarang') ?? 0;

                    return '
                    <div class="input-group input-group-sm">
                        <input type="number" name="op_sekarang[]" class="form-control op-sekarang" value="' . $opSekarang . '" disabled>
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <input type="hidden" name="op_sekarang[]" value="' . $opSekarang . '">
                ';
                })
                ->rawColumns(['op_sekarang', 'op_lalu'])
                ->make(true);
        }

        $proyekDetail = ProyekBangunanDetail::findOrFail($id);
        $proyek       = ProyekBangunan::find($proyekDetail->id_proyek_bangunan);

        return view('admin.op_bangunan.proyek_bangunan.detail.detail_kerja', compact('proyekDetail', 'proyek'));
    }

    public function cetakOpname($id, $opKe)
    {
        $proyek       = ProyekBangunan::findOrFail($id);
        $detailHeader = ProyekBangunanDetail::where('id_proyek_bangunan', $id)
            ->where('op_ke', $opKe)
            ->firstOrFail();

        $details = ProyekBangunanDetailKerja::where('id_proyek_bangunan_detail', $detailHeader->id)->get();

        $JenisPekerjaanBangunan = JenisPekerjaanBangunan::where('id_lokasi', $proyek->id_lokasi)->get()->keyBy('id');
        $jumlah_unit            = $proyek->jumlah_unit ?? 0;
        $nilaiProyek            = $proyek->nilai_pekerjaan;
        $nilaiProyekTotal       = $nilaiProyek * $jumlah_unit;

        $pdf = new TCPDF('L', 'mm', 'A3', true, 'UTF-8', false);
        $pdf->SetTitle('PERSENTASI KEMAJUAN PEKERJAAN');
        $pdf->AddPage();
        $pdf->Ln(2);
        $pdf->SetFont('helvetica', '', 11);

        $kolomKiri = [
            'Proyek'           => strtoupper($proyek->nama_proyek),
            'Opname ke-'       => $opKe,
            'Blok / Nomor'     => ($proyek->blok ?? '-'),
            'Volume pekerjaan' => number_format($proyek->volume_pekerjaan) . ' M2',
            'Harga Satuan'     => 'Rp ' . number_format($proyek->harga_satuan, 0, ',', '.') . ' /M2',
            'Nilai Pekerjaan'  => 'Rp ' . number_format($nilaiProyekTotal, 0, ',', '.'),
        ];

        $kolomKanan = [
            'Tanggal' => Carbon::parse($proyek->tanggal)->translatedFormat('d F Y'),
            'Type'    => $proyek->tipe_rumah,
            'Jumlah'  => $jumlah_unit . ' Unit',
        ];

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'PERSENTASI KEMAJUAN PEKERJAAN', 0, 1, 'C');
        $pdf->Ln(2);
        $pdf->SetFont('helvetica', '', 11);

        $startXKiri = 35;
        $startYKiri = $pdf->GetY();
        foreach ($kolomKiri as $label => $value) {
            $pdf->SetXY($startXKiri, $startYKiri);
            $pdf->Cell(40, 6, $label, 0, 0);
            $pdf->Cell(5, 6, ':', 0, 0);
            $pdf->Cell(80, 6, $value, 0, 1);
            $startYKiri += 6;
        }

        $startXKanan = 230;
        $startYKanan = $pdf->GetY() - (count($kolomKiri) * 6);
        foreach ($kolomKanan as $label => $value) {
            $pdf->SetXY($startXKanan, $startYKanan);
            $pdf->Cell(30, 6, $label, 0, 0);
            $pdf->Cell(5, 6, ':', 0, 0);
            $pdf->Cell(60, 6, $value, 0, 1);
            $startYKanan += 6;
        }

        $pdf->Ln(20);
        $pdf->SetX(20);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor(255, 255, 153);
        $pdf->Cell(9, 14, 'No.', 1, 0, 'C', 1);
        $pdf->Cell(100, 14, 'Jenis Pekerjaan', 1, 0, 'C', 1);
        $pdf->Cell(25, 14, 'Presentasi', 1, 0, 'C', 1);
        $pdf->Cell(72, 7, 'Persentasi Progres Pekerjaan', 1, 0, 'C', 1);
        $pdf->Cell(40, 14, 'Satuan Harga per Unit', 1, 0, 'C', 1);
        $pdf->Cell(40, 14, 'Total Persentasi', 1, 0, 'C', 1);
        $pdf->Cell(90, 7, 'Harga Opname Pekerjaan', 1, 1, 'C', 1);

        $pdf->SetX(23 + 116 + 15);
        $pdf->Cell(24, 7, 'Opname Lalu', 1, 0, 'C', 1);
        $pdf->Cell(24, 7, 'Opname Skrg', 1, 0, 'C', 1);
        $pdf->Cell(24, 7, 'Total Opname', 1, 0, 'C', 1);
        $pdf->SetX(23 + 178 + 15 + 45 + 25 + 20);
        $pdf->Cell(30, 7, 'Opname Lalu', 1, 0, 'C', 1);
        $pdf->Cell(30, 7, 'Opname Skrg', 1, 0, 'C', 1);
        $pdf->Cell(30, 7, 'Total Opname', 1, 1, 'C', 1);

        $pdf->SetFont('helvetica', '', 10);
        $no                     = 1;
        $total_presentasi       = 0;
        $total_op_lalu          = 0;
        $total_op_skrg          = 0;
        $total_op_total         = 0;
        $total_harga_unit       = 0;
        $total_total_presentasi = 0;
        $total_op_lalu_rp       = 0;
        $total_op_skrg_rp       = 0;
        $total_op_total_rp      = 0;

        foreach ($details as $detail) {
            $jenis = $JenisPekerjaanBangunan[$detail->id_jenis_pekerjaan] ?? null;
            if (! $jenis) {
                continue;
            }

            $presentasi = floatval($jenis->presentasi);
            $hargaUnit  = $nilaiProyekTotal * ($presentasi / 100);
            $opLalu     = floatval($detail->op_lalu);
            $opSkrg     = floatval($detail->op_sekarang);
            $opTotal    = $opLalu + $opSkrg;

            $opSkrgRp  = $hargaUnit * ($opSkrg / 100);
            $opLaluRp  = $hargaUnit * ($opLalu / 100);
            $opTotalRp = $opLaluRp + $opSkrgRp;

            $pdf->SetX(20);
            $pdf->Cell(9, 6, $no++, 1, 0, 'C');
            $pdf->Cell(100, 6, $jenis->jenis, 1, 0);
            $pdf->Cell(25, 6, number_format($presentasi, 2) . ' %', 1, 0, 'R');
            $pdf->Cell(24, 6, $opLalu . ' %', 1, 0, 'R');
            $pdf->Cell(24, 6, $opSkrg . ' %', 1, 0, 'R');
            $pdf->Cell(24, 6, $opTotal . ' %', 1, 0, 'R');
            $pdf->Cell(40, 6, 'Rp ' . number_format($hargaUnit), 1, 0, 'R');
            $pdf->Cell(40, 6, number_format($presentasi, 2) . ' %', 1, 0, 'R');
            $pdf->Cell(30, 6, 'Rp ' . number_format($opLaluRp), 1, 0, 'R');
            $pdf->Cell(30, 6, 'Rp ' . number_format($opSkrgRp), 1, 0, 'R');
            $pdf->Cell(30, 6, 'Rp ' . number_format($opTotalRp), 1, 1, 'R');

            $total_presentasi += $presentasi;
            $total_op_lalu += ($presentasi * $opLalu / 100);
            $total_op_skrg += ($presentasi * $opSkrg / 100);
            $total_op_total += ($presentasi * $opTotal / 100);
            $total_harga_unit += $hargaUnit;
            $total_total_presentasi += $presentasi;
            $total_op_lalu_rp += $opLaluRp;
            $total_op_skrg_rp += $opSkrgRp;
            $total_op_total_rp += $opTotalRp;
        }

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetX(20);
        $pdf->Cell(9, 6, '', 1, 0);
        $pdf->Cell(100, 6, 'TOTAL', 1, 0);
        $pdf->Cell(25, 6, number_format($total_presentasi, 2) . ' %', 1, 0, 'R');
        $pdf->Cell(24, 6, number_format($total_op_lalu, 2) . ' %', 1, 0, 'R');
        $pdf->Cell(24, 6, number_format($total_op_skrg, 2) . ' %', 1, 0, 'R');
        $pdf->Cell(24, 6, number_format($total_op_total, 2) . ' %', 1, 0, 'R');
        $pdf->Cell(40, 6, 'Rp ' . number_format($total_harga_unit), 1, 0, 'R');
        $pdf->Cell(40, 6, number_format($total_total_presentasi, 2) . ' %', 1, 0, 'R');
        $pdf->Cell(30, 6, 'Rp ' . number_format($total_op_lalu_rp), 1, 0, 'R');
        $pdf->Cell(30, 6, 'Rp ' . number_format($total_op_skrg_rp), 1, 0, 'R');
        $pdf->Cell(30, 6, 'Rp ' . number_format($total_op_total_rp), 1, 1, 'R');

        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(60, 6, 'Opname Yang Akan Dibayarkan', 0, 0);
        $pdf->Cell(5, 6, ': Rp', 0, 0);
        $pdf->Cell(30, 6, number_format($total_op_skrg_rp), 0, 1, 'R');

        $pdf->SetX(20);
        $pdf->Cell(60, 6, 'Sisa Opname Pekerjaan', 0, 0);
        $pdf->Cell(5, 6, ': Rp', 0, 0);
        $pdf->Cell(30, 6, number_format($nilaiProyekTotal - ($total_op_lalu_rp + $total_op_skrg_rp)), 0, 1, 'R');

        $pdf->Cell(150, 6, '', 0, 0);
        $pdf->Cell(60, 6, 'Dibuat oleh,', 0, 0, 'C');
        $pdf->Cell(60, 6, 'Diketahui oleh,', 0, 0, 'C');
        $pdf->Cell(60, 6, 'Disetujui oleh,', 0, 1, 'C');

        $pdf->Ln(25);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(150, 6, '', 0, 0);
        $pdf->Cell(60, 6, 'Divisi Proyek', 0, 0, 'C');
        $pdf->Cell(60, 6, 'Kepala Tukang', 0, 0, 'C');
        $pdf->Cell(60, 6, 'Manajer Proyek', 0, 1, 'C');

        $pdf->Output('laporan_opname.pdf', 'I');
    }

    public function cetakRekapitulasi($id, $opKe)
    {
        $proyek      = ProyekBangunan::findOrFail($id);
        $detail      = ProyekBangunanDetail::where('id_proyek_bangunan', $id)->where('op_ke', $opKe)->firstOrFail();
        $detailKerja = ProyekBangunanDetailKerja::where('id_proyek_bangunan_detail', $detail->id)->get();

        $nilaiProyek = $proyek->harga_satuan * $proyek->jumlah_unit;

        $pdf = new TCPDF('P', 'mm', 'A3', true, 'UTF-8', false);
        $pdf->SetMargins(20, 10, 20);
        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'REKAPITULASI Opname UPAH', 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(25, 5, 'Proyek', 0, 0);
        $pdf->Cell(2, 5, ':', 0, 0);
        $pdf->Cell(60, 5, strtoupper($proyek->nama_proyek), 0, 0);
        $pdf->Cell(30, 5, '', 0, 0);
        $pdf->Cell(20, 5, 'Pemborong', 0, 0);
        $pdf->Cell(2, 5, ':', 0, 0);
        $pdf->Cell(50, 5, strtoupper($proyek->nama_pemborong), 0, 1);

        $pdf->Cell(25, 5, 'Opname ke-', 0, 0);
        $pdf->Cell(2, 5, ':', 0, 0);
        $pdf->Cell(60, 5, $detail->op_ke, 0, 0);
        $pdf->Cell(30, 5, '', 0, 0);
        $pdf->Cell(20, 5, 'SPK Nomor', 0, 0);
        $pdf->Cell(2, 5, ':', 0, 0);
        $pdf->Cell(50, 5, $proyek->no_kontrak, 0, 1);

        $pdf->Cell(25, 5, 'Tanggal', 0, 0);
        $pdf->Cell(2, 5, ':', 0, 0);
        $pdf->Cell(60, 5, \Carbon\Carbon::parse($detail->tanggal)->translatedFormat('d F Y'), 0, 0);
        $pdf->Cell(30, 5, '', 0, 0);
        $pdf->Cell(20, 5, 'Tanggal SPK', 0, 0);
        $pdf->Cell(2, 5, ':', 0, 0);
        $pdf->Cell(50, 5, \Carbon\Carbon::parse($proyek->tanggal)->translatedFormat('d F Y'), 0, 1);

        $pdf->Ln(5);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetFillColor(255, 255, 153);
        $pdf->SetLineWidth(0.2);
        $pdf->SetX(20);

        $colWidths = [10, 20, 20, 20, 20, 20, 25, 25, 25, 25, 25, 25];
        $colLabels = [
            ['No.'],
            ['Nama Blok'],
            ['Type'],
            ['Jumlah Unit'],
            ['Harga Satuan'],
            ['Progres Yg Lalu'],
            ['Progres Skrng'],
            ['Total Progres'],
            ['Total Nilai Pekerjaan'],
            ['Harga Opname Skrng'],
            ['Jumlah Harga Opname'],
            ['Sisa Opname Pekerjaan'],
        ];

        foreach ($colLabels as $i => $label) {
            $pdf->MultiCell($colWidths[$i], 8, implode("\n", $label), 1, 'C', true, 0);
        }
        $pdf->Ln();

        $opLalu = 0;

        if ($opKe > 1) {
            $opLalu = ProyekBangunanDetail::where('id_proyek_bangunan', $id)
                ->where('op_ke', '<', $opKe)
                ->sum('persen');
        }

        $opSkrg = ProyekBangunanDetail::where('id_proyek_bangunan', $id)
            ->where('op_ke', $opKe)
            ->value('persen');

        $opTotal = $opLalu + $opSkrg;

        $hargaSatuan    = $proyek->harga_satuan;
        $nilaiPekerjaan = ProyekBangunanDetail::where('id_proyek_bangunan', $id)
            ->where('op_ke', '<=', $opKe)
            ->sum('nilai_pekerjaan');

        $hargaOpSkrg = $nilaiPekerjaan * ($opSkrg / 100);
        $jumlahHarga = $nilaiPekerjaan + $hargaOpSkrg;

        $totalHargaOpnameSkrg = $hargaOpSkrg;
        $totalHargaOpname     = $jumlahHarga;

        $rowData = [
            '1',
            $proyek->blok,
            $proyek->tipe_rumah,
            $proyek->jumlah_unit,
            'Rp ' . number_format($hargaSatuan, 0, ',', '.'),
            $opKe == 1 ? '-' : number_format($opLalu, 2) . '%',
            number_format($opSkrg, 2) . '%',
            number_format($opTotal, 2) . '%',
            'Rp ' . number_format($nilaiPekerjaan, 0, ',', '.'),
            'Rp ' . number_format($hargaOpSkrg, 0, ',', '.'),
            'Rp ' . number_format($jumlahHarga, 0, ',', '.'),
            '',
        ];

        $pdf->SetX(20);
        foreach ($rowData as $i => $cell) {
            $pdf->MultiCell($colWidths[$i], 6, $cell, 1, 'C', false, 0);
        }
        $pdf->Ln();

        $pdf->SetX(20);
        $pdf->MultiCell(
            array_sum(array_slice($colWidths, 0, 5)),
            12,
            "Satuan Harga / M2 Tidak Termasuk Harga Upah untuk\nPekerjaan Instansi Dalam Listrik dan pengecetan\nInterior/Exterior Bangunan",
            1,
            'L',
            false,
            0
        );
        for ($i = 5; $i < count($colWidths); $i++) {
            $pdf->MultiCell($colWidths[$i], 12, '', 1, 'C', false, 0);
        }
        $pdf->Ln();

        $pdf->SetX(20);
        $pdf->SetFont('', 'B', 6.5);
        $pdf->MultiCell($colWidths[0] + $colWidths[1] + $colWidths[2], 6, 'TOTAL', 1, 'C', false, 0);
        $pdf->MultiCell($colWidths[3], 6, $proyek->jumlah_unit, 1, 'C', false, 0);
        $pdf->MultiCell($colWidths[4], 6, '', 1, 'C', false, 0);
        $pdf->MultiCell($colWidths[5], 6, $opKe == 1 ? '-' : number_format($opLalu, 2) . '%', 1, 'C', false, 0);
        $pdf->MultiCell($colWidths[6], 6, number_format($opSkrg, 2) . '%', 1, 'C', false, 0);
        $pdf->MultiCell($colWidths[7], 6, number_format($opTotal, 2) . '%', 1, 'C', false, 0);
        $pdf->MultiCell($colWidths[8], 6, 'Rp ' . number_format($nilaiPekerjaan, 0, ',', '.'), 1, 'C', false, 0);
        $pdf->MultiCell($colWidths[9], 6, 'Rp ' . number_format($hargaOpSkrg, 0, ',', '.'), 1, 'C', false, 0);
        $pdf->MultiCell($colWidths[10], 6, 'Rp ' . number_format($jumlahHarga, 0, ',', '.'), 1, 'C', false, 0);
        $pdf->MultiCell($colWidths[11], 6, '', 1, 'C', false, 1);

        $retensiSkrg = $totalHargaOpnameSkrg * 0.05;
        $retensiLalu = 0;
        $retensiList = [];

        if ($opKe > 1) {
            $retensiLaluDetail = ProyekBangunanDetail::where('id_proyek_bangunan', $id)
                ->where('op_ke', '<', $opKe)->get();
            foreach ($retensiLaluDetail as $r) {
                $r5 = $r->nilai_pekerjaan * 0.05;
                $retensiLalu += $r5;
                $retensiList[] = [
                    'tgl'     => Carbon::parse($r->tanggal)->format('d/m/Y'),
                    'nilai'   => $r->nilai_pekerjaan,
                    'retensi' => $r5,
                ];
            }
        }

        $pdf->Ln(6);
        $pdf->SetFont('', '', 9);
        $pdf->Cell(8, 7, '', 'LTB', 0, 'C');
        $pdf->Cell(226, 7, 'Jumlah Harga Opname', 'TB', 0, 'L');
        $pdf->Cell(26, 7, ': Rp ' . number_format($totalHargaOpname, 0, ',', '.'), 'RTB', 1, 'R');

        $pdf->Cell(8, 7, 'B', 'L', 0, 'C');
        $pdf->Cell(226, 7, 'Jumlah Retensi', '', 0, 'L');
        $pdf->Cell(26, 7, ': Rp ' . number_format($retensiLalu + $retensiSkrg, 0, ',', '.'), 'R', 1, 'R');

        $pdf->Cell(8, 7, '', 'L', 0, 'C');
        $pdf->Cell(150, 7, '1. Retensi Pekerjaan 5%, Opname Lalu', '', 0, 'L');
        $pdf->Cell(80, 7, ': Rp ' . number_format($retensiLalu, 0, ',', '.'), '', 0, 'R');
        $pdf->Cell(22, 7, '', 'R', 1, 'R');

        foreach ($retensiList as $r) {
            $pdf->Cell(10, 10, '', 'L', 0, 'C');
            $pdf->Cell(90, 10, '- Tgl. ' . $r['tgl'], '', 0, 'L');
            $pdf->Cell(90, 10, '= 5% x Rp.' . number_format($r['nilai'], 0, ',', '.') . ',-', '', 0, 'L');
            $pdf->Cell(20, 10, 'Rp', '', 0, 'L');
            $pdf->Cell(50, 10, number_format($r['retensi'], 0, ',', '.'), 'R', 1, 'L');
        }

        $pdf->Cell(8, 10, '', 'L', 0, 'C');
        $pdf->Cell(92, 10, '2. Retensi Pekerjaan 5%, Opname Sekarang', '', 0, 'L');
        $pdf->Cell(120, 10, '= 5% x Rp.' . number_format($totalHargaOpnameSkrg, 0, ',', '.') . ',-', '', 0, 'L');
        $pdf->Cell(18, 10, ': Rp ' . number_format($retensiSkrg, 0, ',', '.'), '', 0, 'L');
        $pdf->Cell(22, 10, '', 'R', 1, 'R');

        $pdf->Cell(8, 10, '', 'LB', 0, 'C');
        $pdf->Cell(92, 10, '3. Pembayaran Retensi', 'B', 0, 'L');
        $pdf->Cell(120, 10, '= ', 'B', 0, 'L');
        $pdf->Cell(18, 10, ':Rp', 'B', 0, 'L');
        $pdf->Cell(22, 10, '', 'RB', 1, 'L');

        $pdf->Cell(8, 7, 'B', 'L', 0, 'C');
        $pdf->Cell(226, 7, 'Pembayaran Sebelumnya', '', 0, 'L');
        $pdf->Cell(26, 7, '', 'R', 1, 'R');

        $pdf->Cell(8, 7, '', 'L', 0, 'C');
        $pdf->Cell(150, 7, '1. Tgl. 00/03/2024, Voucher /III/2024 | Marhaposan Situmorang', '', 0, 'L');
        $pdf->Cell(80, 7, ': RP -', '', 0, 'R');
        $pdf->Cell(22, 7, '', 'R', 1, 'R');
        $pdf->Cell(8, 7, '', 'L', 0, 'C');
        $pdf->Cell(150, 7, '2. Tgl. 00/03/2024, Voucher /III/2024 | Marhaposan Situmorang', '', 0, 'L');
        $pdf->Cell(80, 7, ': RP -', '', 0, 'R');
        $pdf->Cell(22, 7, '', 'R', 1, 'R');
        $pdf->Cell(8, 7, '', 'LB', 0, 'C');
        $pdf->Cell(150, 7, '3. Tgl. 00/03/2024, Voucher /III/2024 | Marhaposan Situmorang', 'B', 0, 'L');
        $pdf->Cell(80, 7, ': RP -', 'B', 0, 'R');
        $pdf->Cell(22, 7, '', 'RB', 1, 'R');

        $pdf->Cell(8, 7, '', 'L', 0, 'C');
        $pdf->Cell(226, 7, 'Pembayaran Opname dan Realisasi Pembayaran Retensi Saat Ini (sebelum potong pajak)', '', 0, 'L');
        $pdf->Cell(26, 7, ': Rp -', 'R', 0, 'R');
        $pdf->Cell(22, 7, '', 'R', 1, 'R');
        $pdf->Cell(8, 7, '', 'L', 0, 'C');
        $pdf->Cell(150, 7, '1. Opname Pekerja Saat Ini', '', 0, 'L');
        $pdf->Cell(80, 7, ': RP -', '', 0, 'R');
        $pdf->Cell(22, 7, '', 'R', 1, 'R');
        $pdf->Cell(8, 7, '', 'LB', 0, 'C');
        $pdf->Cell(150, 7, '2. Realisasi Pembayaran Retensi', 'B', 0, 'L');
        $pdf->Cell(80, 7, ': RP -', 'B', 0, 'R');
        $pdf->Cell(22, 7, '', 'RB', 1, 'R');

        $pdf->SetFont('', '', 9);
        $pdf->Cell(8, 7, '', 'LTB', 0, 'C');
        $pdf->Cell(226, 7, 'Pajak Penghasilan = -% x Rp.-,- ', 'TB', 0, 'L');
        $pdf->Cell(26, 7, ': RP -', 'RTB', 1, 'R');

        $pdf->SetFont('', '', 9);
        $pdf->Cell(8, 7, '', 'LTB', 0, 'C');
        $pdf->Cell(226, 7, 'Pembayaran Sekarang (setelah potong pajak)', 'TB', 0, 'L');
        $pdf->Cell(26, 7, ': RP -', 'RTB', 1, 'R');

        $pdf->Ln(10);
        $pdf->SetFont('helvetica', '', 8);
        $divisi = ['Dibuat,', 'Diketahui,', 'Diverifikasi,', 'Dibayar,', 'Dipriksa,', 'Disetujui,x'];
        foreach ($divisi as $d) {
            $pdf->Cell(43, 6, $d, 0, 0, 'C');
        }
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 8);
        $divisi = ['Pelaksana', 'Kepala Tukang', 'Manajer Proyek', 'Keuangan', 'Accounting', 'Direktur'];
        foreach ($divisi as $d) {
            $pdf->Cell(43, 6, $d, 0, 0, 'C');
        }

        return $pdf->Output('rekapitulasi-opname.pdf', 'I');
    }
}
