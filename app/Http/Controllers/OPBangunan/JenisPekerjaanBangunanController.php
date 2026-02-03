<?php
namespace App\Http\Controllers\OPBangunan;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\JenisPekerjaanBangunan;
use App\Models\LokasiKavling;
use App\Traits\LogAktivitasTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class JenisPekerjaanBangunanController extends Controller
{
    use LogAktivitasTrait;
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = LokasiKavling::orderBy('id', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('jenis-pekerjaan-bangunan.show', $row->id);

                    $btn = '<div class="d-flex justify-content-center">';
                    if ($permissions['edit']) {
                        $btn .= '<a class="btn btn-primary btn-sm" href="' . e($editUrl) . '">Cek</a>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.op_bangunan.jenis_pekerjaan.index', compact('permissions'));
    }

    public function show(Request $request, $id)
    {
        if ($request->ajax()) {
            $data = JenisPekerjaanBangunan::where('id_lokasi', $id)->orderBy('id', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('harga', function ($row) {
                    $hargaFormatted = number_format($row->harga, 0, ',', '.');
                    return '<div class="d-flex justify-content-between"><span>Rp.</span><span class="text-end" style="min-width: 100px;">' . $hargaFormatted . '</span></div>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl   = route('jenis-pekerjaan-bangunan.edit', $row->id);
                    $deleteUrl = route('jenis-pekerjaan-bangunan.destroy', $row->id);

                    $btn = '<div class="d-flex justify-content-center">';
                    $btn .= '<button class="btn btn-primary btn-sm mx-1 edit-button" data-id="' . e($row->id) . '" data-url="' . e($editUrl) . '">Edit</button>';
                    $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="delete-button btn btn-danger btn-sm mx-1">Hapus</button></form>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action', 'harga'])
                ->make(true);
        }

        $data = LokasiKavling::findOrFail($id);

        $totalPresentasi = JenisPekerjaanBangunan::where('id_lokasi', $id)->sum('presentasi');

        return view('admin.op_bangunan.jenis_pekerjaan.show', compact('data', 'totalPresentasi'));
    }

    public function edit($id)
    {
        $list = JenisPekerjaanBangunan::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $list,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis'      => 'required|unique:jenis_pekerjaan_bangunan,jenis',
            'presentasi' => 'required|numeric',
            'harga'      => 'required',
        ], [
            'jenis.required'      => 'Jenis pekerjaan wajib diisi.',
            'jenis.unique'        => 'Jenis pekerjaan sudah digunakan.',
            'presentasi.required' => 'Presentasi wajib diisi.',
            'harga.required'      => 'Harga wajib diisi.',
        ]);

        $total = JenisPekerjaanBangunan::where('id_lokasi', $request->id_lokasi)->sum('presentasi');
        if (($total + $request->presentasi) > 100) {
            return response()->json([
                'status' => 'error',
                'errors' => ['presentasi' => ['Total persen melebihi 100']],
            ], 422);
        }

        $db = [
            'id_lokasi'  => $request->id_lokasi,
            'jenis'      => $request->jenis,
            'presentasi' => $request->presentasi,
            'harga'      => str_replace('.', '', $request->harga ?? 0),
        ];

        $jp = JenisPekerjaanBangunan::create($db);
        $this->logCreate('Jenis Pekerjaan Bangunan', $jp->id);

        return response()->json(['status' => 'success']);
    }

    public function update(Request $request, $id)
    {
        $data = JenisPekerjaanBangunan::findOrFail($id);

        $request->validate([
            'jenis'      => 'required|unique:jenis_pekerjaan_bangunan,jenis,' . $data->id . ',id',
            'presentasi' => 'required|numeric',
            'harga'      => 'required',
        ], [
            'jenis.required'      => 'Jenis pekerjaan wajib diisi.',
            'jenis.unique'        => 'Jenis pekerjaan sudah digunakan.',
            'presentasi.required' => 'Presentasi wajib diisi.',
            'harga.required'      => 'Harga wajib diisi.',
        ]);

        $total     = JenisPekerjaanBangunan::where('id_lokasi', $data->id_lokasi)->sum('presentasi');
        $totalBaru = ($total - $data->presentasi) + $request->presentasi;
        if ($totalBaru > 100) {
            return response()->json([
                'status' => 'error',
                'errors' => ['presentasi' => ['Total persen melebihi 100']],
            ], 422);
        }

        $db = [
            'jenis'      => $request->jenis,
            'presentasi' => $request->presentasi,
            'harga'      => str_replace('.', '', $request->harga ?? 0),
        ];

        $data->update($db);
        $this->logEdit('Jenis Pekerjaan Bangunan', $data->id);

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        $data = JenisPekerjaanBangunan::findOrFail($id);

        $this->logDelete('Jenis Pekerjaan Bangunan', $data->id);
        $data->delete();

        return response()->json(['status' => 'success']);
    }
}
