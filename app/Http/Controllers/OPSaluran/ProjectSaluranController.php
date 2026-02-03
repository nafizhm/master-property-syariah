<?php
namespace App\Http\Controllers\OPSaluran;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\Bank;
use App\Models\FotoProyekSaluran;
use App\Models\JenisPekerjaanSaluran;
use App\Models\LokasiKavling;
use App\Models\Pengeluaran;
use App\Models\ProyekSaluran;
use App\Models\ProyekSaluranDetail;
use App\Models\ProyekSaluranDetailKerja;
use App\Models\Saluran;
use App\Traits\LogAktivitasTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use TCPDF;
use Yajra\DataTables\Facades\DataTables;

class ProjectSaluranController extends Controller
{
    use LogAktivitasTrait;
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = ProyekSaluran::orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($row) {
                    return Carbon::parse($row->tanggal)->translatedFormat('d F Y');
                })
                ->addColumn('saluran', function ($row) {
                    $saluran = Saluran::find($row->id_saluran);
                    if (! $saluran) {
                        return '-';
                    }

                    $panjang = number_format($saluran->panjang, 0, ',', '.');

                    return "<b>{$saluran->nama}</b><br>
                    <span style='font-family:monospace'>
                    Panjang&nbsp;&nbsp;: {$panjang} m
                    </span>";
                })
                ->addColumn('progres', function ($row) {
                    if ($row->id_bayar == 2) {
                        $detailWithTanggal = ProyekSaluranDetail::where('id_proyek_saluran', $row->id)
                            ->whereNotNull('tanggal');

                        if (! $detailWithTanggal->exists()) {
                            return 'Belum Ada';
                        }

                        $lastDetail  = $detailWithTanggal->orderByDesc('op_ke')->first();
                        $totalPersen = $detailWithTanggal->sum('persen');
                    } else {
                        $lastDetail = ProyekSaluranDetail::where('id_proyek_saluran', $row->id)
                            ->orderByDesc('op_ke')
                            ->first();

                        $totalPersen = ProyekSaluranDetail::where('id_proyek_saluran', $row->id)
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
                    $editUrl   = route('proyek-saluran.edit', $row->id);
                    $showUrl   = route('proyek-saluran.show', $row->id);
                    $deleteUrl = route('proyek-saluran.destroy', $row->id);

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
                ->rawColumns(['saluran', 'progres', 'action', 'no_kontrak'])
                ->make(true);
        }

        $lokasiList = LokasiKavling::all();
        $bankList   = Bank::all();

        return view('admin.op_saluran.proyek_saluran.index', compact('permissions', 'lokasiList', 'bankList'));
    }

    public function getSaluranByLokasi($id)
    {
        $saluranList = Saluran::where('id_lokasi', $id)->get(['id', 'nama', 'panjang']);

        return response()->json($saluranList);
    }

    public function getSaluranDetail($id)
    {
        $saluran = Saluran::findOrFail($id);

        return response()->json([
            'panjang' => $saluran->panjang,
        ]);
    }

    public function edit($id)
    {
        $proyek = ProyekSaluran::find($id);

        $terminList = ProyekSaluranDetail::where('id_proyek_saluran', $id)
            ->orderBy('id')
            ->pluck('persen')
            ->take($proyek->jumlah_termin)
            ->toArray();

        return response()->json([
            'status' => 'success',
            'data'   => $proyek,
            'termin' => $terminList,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'no_kontrak'      => 'required|unique:proyek_saluran,no_kontrak',
            'tanggal'         => 'required|date',
            'nama_proyek'     => 'required',
            'nama_pemborong'  => 'required',
            'id_lokasi'       => 'required',
            'id_bank'         => 'required',
            'id_saluran'      => 'required',
            'harga_satuan'    => 'required',
            'nilai_pekerjaan' => 'required',
            'id_bayar'        => 'required',
            'jumlah_termin'   => 'required_if:id_bayar,2|nullable',
            'termin'          => 'required_if:id_bayar,2|array',
            'termin.*'        => 'required_if:id_bayar,2|numeric|min:0|max:100',
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
            'id_saluran.required'       => 'Jalan wajib dipilih.',
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
            'no_kontrak'      => $request->no_kontrak,
            'tanggal'         => $request->tanggal,
            'nama_proyek'     => $request->nama_proyek,
            'nama_pemborong'  => $request->nama_pemborong,
            'id_lokasi'       => $request->id_lokasi,
            'id_bank'         => $request->id_bank,
            'id_saluran'      => $request->id_saluran,
            'harga_satuan'    => str_replace('.', '', $request->harga_satuan),
            'nilai_pekerjaan' => str_replace('.', '', $request->nilai_pekerjaan),
            'id_bayar'        => $request->id_bayar,
            'jumlah_termin'   => $request->jumlah_termin ?? 0,
        ];

        $proyek = ProyekSaluran::create($data);
        $this->logCreate('Proyek Saluran', $proyek->id);

        if ($request->id_bayar == 2 && is_array($request->termin)) {
            foreach ($request->termin as $index => $persen) {
                $proyekDetail = ProyekSaluranDetail::create([
                    'id_proyek_saluran' => $proyek->id,
                    'op_ke'             => $index + 1,
                    'tanggal'           => null,
                    'persen'            => $persen,
                    'nilai_pekerjaan'   => 0,
                ]);

                $jenis_pekerjaan = JenisPekerjaanSaluran::all();
                foreach ($jenis_pekerjaan as $jenis) {
                    ProyekSaluranDetailKerja::create([
                        'id_proyek_saluran'        => $proyek->id,
                        'id_proyek_saluran_detail' => $proyekDetail->id,
                        'id_jenis_pekerjaan'       => $jenis->id,
                        'op_lalu'                  => 0,
                        'op_sekarang'              => 0,
                    ]);
                }
            }
        }

        return response()->json(['status' => 'success']);
    }

    public function update(Request $request, $id)
    {
        $data = ProyekSaluran::findOrFail($id);

        $request->validate([
            'no_kontrak'     => 'required|unique:proyek_saluran,no_kontrak,' . $data->id . ',id',
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
        $this->logEdit('Proyek Saluran', $data->id);

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        $data = ProyekSaluran::findOrFail($id);

        $details = ProyekSaluranDetail::where('id_proyek_saluran', $id)->get();

        foreach ($details as $detail) {
            $fotos = FotoProyekSaluran::where('id_proyek_saluran_detail', $detail->id)->get();
            foreach ($fotos as $foto) {
                $path = public_path('assets/proyek_saluran/' . $foto->foto);
                if (file_exists($path)) {
                    unlink($path);
                }
            }

            FotoProyekSaluran::where('id_proyek_saluran_detail', $detail->id)->delete();
        }

        ProyekSaluranDetail::where('id_proyek_saluran', $data->id)->delete();

        ProyekSaluranDetailKerja::where('id_proyek_saluran', $data->id)->delete();

        $this->logDelete('Proyek Saluran', $data->id);
        $data->delete();

        return response()->json(['status' => 'success']);
    }

    public function show(Request $request, $id)
    {
        Carbon::setLocale('id');

        if ($request->ajax()) {
            $data = ProyekSaluranDetail::where('id_proyek_saluran', $id)->orderBy('id', 'asc');

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
                    $showUrl   = route('proyek-saluran.detail', $row->id);
                    $deleteUrl = route('proyek-saluran.detail-destroy', $row->id);
                    $proyek    = ProyekSaluran::findOrFail($row->id_proyek_saluran);

                    $lastDetailId = ProyekSaluranDetail::where('id_proyek_saluran', $id)
                        ->orderByDesc('id')
                        ->value('id');

                    $btn = '<div class="d-flex justify-content-center align-items-center gap-1">';
                    $btn .= '<a class="btn btn-success btn-xs mr-1" href="' . e($showUrl) . '">Detail Opname</a>';
                    $btn .= '<a href="' . route('proyek-saluran.cetak-opname', [$row->id_proyek_saluran, $row->op_ke]) . '" target="_blank" class="btn btn-dark btn-xs mr-1">Cetak OP</a>';
                    $btn .= '<button class="btn btn-dark btn-xs mr-1">Cetak Rekapitulasi</button>';

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

        $proyek        = ProyekSaluran::findOrFail($id);
        $jumlah_terisi = ProyekSaluranDetail::where('id_proyek_saluran', $id)
            ->whereNotNull('tanggal')
            ->count();
        $tampilkan_tombol = ! ($proyek->id_bayar == 2 && $jumlah_terisi >= $proyek->jumlah_termin);

        return view('admin.op_saluran.proyek_saluran.detail.detail', compact('proyek', 'tampilkan_tombol'));
    }

    public function createDetail(Request $request, $id)
    {
        Carbon::setLocale('id');

        if ($request->ajax()) {
            $data = JenisPekerjaanSaluran::orderBy('id', 'asc')->get();

            $detailKerja = ProyekSaluranDetailKerja::where('id_proyek_saluran', $id);
            $maxDetailId = $detailKerja->max('id_proyek_saluran_detail');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('presentasi', function ($row) {
                    return $row->presentasi . ' %';
                })
                ->addColumn('op_lalu', function ($row) use ($id) {
                    $nilai = ProyekSaluranDetailKerja::where('id_proyek_saluran', $id)
                        ->where('id_jenis_pekerjaan', $row->id)
                        ->sum('op_sekarang');

                    return number_format((float) $nilai, 2) . ' %';
                })
                ->addColumn('op_sekarang', function ($row) use ($id, $maxDetailId) {
                    $opLalu = ProyekSaluranDetailKerja::where('id_proyek_saluran', $id)
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

        $proyek = ProyekSaluran::findOrFail($id);

        if ($proyek->id_bayar == 2) {
            $lastDetail = ProyekSaluranDetail::where('id_proyek_saluran', $id)
                ->whereNotNull('tanggal')
                ->orderByDesc('op_ke')
                ->first();

            $op_ke = $lastDetail ? $lastDetail->op_ke + 1 : 1;
        } else {
            $jumlahOp = ProyekSaluranDetail::where('id_proyek_saluran', $id)->count();
            $op_ke    = $jumlahOp + 1;
        }

        return view('admin.op_saluran.proyek_saluran.detail.create', compact('proyek', 'op_ke'));
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
            $presentasiData = JenisPekerjaanSaluran::orderBy('id', 'asc')->pluck('presentasi', 'id')->toArray();
            $opSekarang     = $request->op_sekarang;

            $totalPersen = 0;

            foreach ($opSekarang as $index => $op) {
                $idJenis    = array_keys($presentasiData)[$index];
                $presentasi = $presentasiData[$idJenis] ?? 0;
                $op         = floatval($op);
                $totalPersen += ($presentasi * $op) / 100;
            }

            $nilaiDasar     = ProyekSaluran::findOrFail($id);
            $nilaiPekerjaan = ($totalPersen / 100) * ($nilaiDasar->nilai_pekerjaan);

            if ($nilaiDasar->id_bayar == 1) {

                $data = [
                    'id_proyek_saluran' => $id,
                    'op_ke'             => $request->op_ke,
                    'tanggal'           => $request->tanggal,
                    'persen'            => $totalPersen,
                    'nilai_pekerjaan'   => (int) $nilaiPekerjaan,
                ];

                $proyekDetail = ProyekSaluranDetail::create($data);
                $this->logCreate('Proyek Saluran Detail', $proyekDetail->id);

                Pengeluaran::create([
                    'id_proyek_saluran_detail' => $proyekDetail->id,
                    'id_bank'                  => $nilaiDasar->id_bank,
                    'tanggal'                  => $request->tanggal,
                    'nominal'                  => $nilaiPekerjaan,
                    'id_kategori_transaksi'    => 15,
                    'keterangan'               => 'Pembayaran OP Proyek Saluran ' . $nilaiDasar->nama_proyek . ' ke-' . $proyekDetail->op_ke,
                    'lampiran'                 => '',
                ]);

                $files = $request->file('foto');
                foreach ($files as $file) {
                    $ext  = $file->getClientOriginalExtension();
                    $foto = Str::random(25) . '.' . $ext;
                    $file->move(public_path('assets/proyek_saluran/'), $foto);

                    FotoProyekSaluran::create([
                        'id_proyek_saluran_detail' => $proyekDetail->id,
                        'foto'                     => $foto,
                    ]);
                }

                foreach ($opSekarang as $index => $op) {
                    $idJenis = array_keys($presentasiData)[$index];
                    $op      = (int) $op;

                    $opLalu = ProyekSaluranDetailKerja::where('id_proyek_saluran', $id)
                        ->where('id_jenis_pekerjaan', $idJenis)
                        ->latest('id')
                        ->value('op_sekarang') ?? 0;

                    ProyekSaluranDetailKerja::create([
                        'id_proyek_saluran'        => $id,
                        'id_proyek_saluran_detail' => $proyekDetail->id,
                        'id_jenis_pekerjaan'       => $idJenis,
                        'op_lalu'                  => $opLalu,
                        'op_sekarang'              => $op,
                    ]);
                }
            } elseif ($nilaiDasar->id_bayar == 2) {
                $proyekDetail = ProyekSaluranDetail::where('id_proyek_saluran', $id)
                    ->whereNull('tanggal')
                    ->orderBy('id')
                    ->first();

                if (! $proyekDetail) {
                    throw new \Exception('Opname sudah lengkap semua.');
                }

                $fotosLama = FotoProyekSaluran::where('id_proyek_saluran_detail', $proyekDetail->id)->get();

                foreach ($fotosLama as $foto) {
                    $path = public_path('assets/proyek_saluran/' . $foto->foto);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                    $foto->delete();
                }

                $files = $request->file('foto');

                foreach ($files as $file) {
                    $ext  = $file->getClientOriginalExtension();
                    $foto = Str::random(25) . '.' . $ext;
                    $file->move(public_path('assets/proyek_saluran/'), $foto);

                    FotoProyekSaluran::create([
                        'id_proyek_saluran_detail' => $proyekDetail->id,
                        'foto'                     => $foto,
                    ]);
                }

                $proyekDetail->update([
                    'tanggal'         => $request->tanggal,
                    'persen'          => $totalPersen,
                    'nilai_pekerjaan' => (int) $nilaiPekerjaan,
                ]);
                $this->logEdit('Proyek Saluran Detail', $proyekDetail->id);

                foreach ($opSekarang as $index => $op) {
                    $idJenis = array_keys($presentasiData)[$index];
                    $op      = (int) $op;

                    $opLalu = ProyekSaluranDetailKerja::where('id_proyek_saluran', $id)
                        ->where('id_proyek_saluran_detail', '<', $proyekDetail->id)
                        ->where('id_jenis_pekerjaan', $idJenis)
                        ->sum('op_sekarang');

                    $detailKerja = ProyekSaluranDetailKerja::where('id_proyek_saluran', $id)
                        ->where('id_proyek_saluran_detail', $proyekDetail->id)
                        ->where('id_jenis_pekerjaan', $idJenis)
                        ->first();

                    if ($detailKerja) {
                        $detailKerja->update([
                            'op_lalu'     => $opLalu,
                            'op_sekarang' => $op,
                        ]);
                    } else {
                        ProyekSaluranDetailKerja::create([
                            'id_proyek_saluran'        => $id,
                            'id_proyek_saluran_detail' => $proyekDetail->id,
                            'id_jenis_pekerjaan'       => $idJenis,
                            'op_lalu'                  => $opLalu,
                            'op_sekarang'              => $op,
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
        $data = ProyekSaluranDetail::findOrFail($id);

        $pengeluaran = Pengeluaran::where('id_proyek_saluran_detail', $data->id)->first();
        if ($pengeluaran) {
            if (! empty($pengeluaran->lampiran) && file_exists(public_path('assets/keuangan/pengeluaran/' . $pengeluaran->lampiran))) {
                unlink(public_path('assets/keuangan/pengeluaran/' . $pengeluaran->lampiran));
            }
            $pengeluaran->delete();
        }

        $fotos = FotoProyekSaluran::where('id_proyek_saluran_detail', $data->id)->get();
        foreach ($fotos as $foto) {
            $path = public_path('assets/proyek_saluran/' . $foto->foto);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        FotoProyekSaluran::where('id_proyek_saluran_detail', $data->id)->delete();

        ProyekSaluranDetailKerja::where('id_proyek_saluran_detail', $data->id)->delete();

        $this->logDelete('Proyek Saluran Detail', $data->id);
        $data->delete();

        return response()->json(['status' => 'success']);
    }

    public function detail(Request $request, $id)
    {
        Carbon::setLocale('id');

        if ($request->ajax()) {
            $data = JenisPekerjaanSaluran::orderBy('id', 'asc')->get();

            $proyekDetail = ProyekSaluranDetail::findOrFail($id);
            $idProyek     = $proyekDetail->id_proyek_saluran;

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('presentasi', function ($row) {
                    return $row->presentasi . ' %';
                })
                ->addColumn('op_lalu', function ($row) use ($idProyek, $id) {
                    $opLalu = ProyekSaluranDetailKerja::where('id_proyek_saluran', $idProyek)
                        ->where('id_proyek_saluran_detail', '<', $id)
                        ->where('id_jenis_pekerjaan', $row->id)
                        ->sum('op_sekarang');

                    return number_format((float) $opLalu, 2) . ' %';
                })
                ->addColumn('op_sekarang', function ($row) use ($idProyek, $id) {
                    $opSekarang = ProyekSaluranDetailKerja::where('id_proyek_saluran', $idProyek)
                        ->where('id_proyek_saluran_detail', $id)
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

        $proyekDetail = ProyekSaluranDetail::findOrFail($id);
        $proyek       = ProyekSaluran::find($proyekDetail->id_proyek_saluran);

        return view('admin.op_saluran.proyek_saluran.detail.detail_kerja', compact('proyekDetail', 'proyek'));
    }

    public function cetakOpname($id, $opKe)
    {
        $proyek       = ProyekSaluran::findOrFail($id);
        $detailHeader = ProyekSaluranDetail::where('id_proyek_saluran', $id)
            ->where('op_ke', $opKe)
            ->firstOrFail();

        $details               = ProyekSaluranDetailKerja::where('id_proyek_saluran_detail', $detailHeader->id)->get();
        $JenisPekerjaanSaluran = JenisPekerjaanSaluran::all()->keyBy('id');
        $nilaiProyek           = $proyek->nilai_pekerjaan;

        $pdf = new TCPDF('L', 'mm', 'A3', true, 'UTF-8', false);
        $pdf->SetTitle('PERSENTASI KEMAJUAN PEKERJAAN');
        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'PERSENTASI KEMAJUAN PEKERJAAN', 0, 1, 'C');
        $pdf->Ln(2);

        $kolomKiri = [
            'Proyek'          => strtoupper($proyek->nama_proyek),
            'Obname ke-'      => $opKe,
            'Harga Satuan'    => 'Rp ' . number_format($proyek->harga_satuan, 0, ',', '.') . ' /M2',
            'Nilai Pekerjaan' => 'Rp ' . number_format($proyek->nilai_pekerjaan, 0, ',', '.'),
        ];
        $kolomKanan = [
            'Tanggal'   => Carbon::parse($proyek->tanggal)->translatedFormat('d F Y'),
            'Pemborong' => $proyek->nama_pemborong ?? '-',
        ];

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
        $no             = 1;
        $totalHargaUnit = 0;
        $totalOpLaluRp  = 0;
        $totalOpSkrgRp  = 0;

        $sumPresentasi  = 0;
        $sumOpLaluPct   = 0;
        $sumOpSkrgPct   = 0;
        $sumOpTotalPct  = 0;
        $sumTotalPersen = 0;

        foreach ($details as $detail) {
            $jenis = $JenisPekerjaanSaluran[$detail->id_jenis_pekerjaan] ?? null;
            if (! $jenis) {
                continue;
            }

            $presentasi = $jenis->presentasi;
            $hargaUnit  = $nilaiProyek * ($presentasi / 100);

            $opLalu  = $detail->op_lalu;
            $opSkrg  = $detail->op_sekarang;
            $opTotal = $opLalu + $opSkrg;

            $opLaluRp  = $hargaUnit * ($opLalu / 100);
            $opSkrgRp  = $hargaUnit * ($opSkrg / 100);
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

            $totalHargaUnit += $hargaUnit;
            $totalOpLaluRp += $opLaluRp;
            $totalOpSkrgRp += $opSkrgRp;

            $sumPresentasi += $presentasi;
            $sumOpLaluPct += ($presentasi * $opLalu) / 100;
            $sumOpSkrgPct += ($presentasi * $opSkrg) / 100;
            $sumOpTotalPct += ($presentasi * $opTotal) / 100;
            $sumTotalPersen += $presentasi;
        }

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetX(20);
        $pdf->Cell(109, 6, 'TOTAL', 1, 0, 'C');
        $pdf->Cell(25, 6, number_format($sumPresentasi, 2) . ' %', 1, 0, 'R');
        $pdf->Cell(24, 6, number_format($sumOpLaluPct, 2) . ' %', 1, 0, 'R');
        $pdf->Cell(24, 6, number_format($sumOpSkrgPct, 2) . ' %', 1, 0, 'R');
        $pdf->Cell(24, 6, number_format($sumOpTotalPct, 2) . ' %', 1, 0, 'R');
        $pdf->Cell(40, 6, 'Rp ' . number_format($totalHargaUnit), 1, 0, 'R');
        $pdf->Cell(40, 6, number_format($sumTotalPersen, 2) . ' %', 1, 0, 'R');
        $pdf->Cell(30, 6, 'Rp ' . number_format($totalOpLaluRp), 1, 0, 'R');
        $pdf->Cell(30, 6, 'Rp ' . number_format($totalOpSkrgRp), 1, 0, 'R');
        $pdf->Cell(30, 6, 'Rp ' . number_format($totalOpLaluRp + $totalOpSkrgRp), 1, 1, 'R');

        $pdf->Ln(10);
        $pdf->SetX(20);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(60, 6, 'Obname Yang Akan Dibayarkan', 0, 0);
        $pdf->Cell(5, 6, ': Rp', 0, 0);
        $pdf->Cell(30, 6, number_format($totalOpSkrgRp), 0, 1, 'R');

        $pdf->SetX(20);
        $pdf->Cell(60, 6, 'Sisa Obname Pekerjaan', 0, 0);
        $pdf->Cell(5, 6, ': Rp', 0, 0);
        $pdf->Cell(30, 6, number_format($nilaiProyek - ($totalOpSkrgRp + $totalOpLaluRp)), 0, 1, 'R');

        $pdf->Ln(20);
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

        $pdf->Output('laporan_opname_saluran.pdf', 'I');
    }

}
