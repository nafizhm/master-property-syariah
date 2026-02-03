<?php
namespace App\Http\Controllers\OPSaluran;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\JenisPekerjaanSaluran;
use App\Traits\LogAktivitasTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class JenisPekerjaanSaluranController extends Controller
{
    use LogAktivitasTrait;
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = JenisPekerjaanSaluran::orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl   = route('jenis-pekerjaan-saluran.edit', $row->id);
                    $deleteUrl = route('jenis-pekerjaan-saluran.destroy', $row->id);

                    $btn = '<div class="d-flex justify-content-center">';
                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-sm mx-1 edit-button" data-id="' . e($row->id) . '" data-url="' . e($editUrl) . '">Edit</button>';
                    }

                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="delete-button btn btn-danger btn-sm mx-1">Hapus</button></form>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $totalPresentasi = JenisPekerjaanSaluran::sum('presentasi');

        return view('admin.op_saluran.jenis_pekerjaan.index', compact('permissions', 'totalPresentasi'));
    }

    public function edit($id)
    {
        $list = JenisPekerjaanSaluran::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $list,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis'      => 'required|unique:jenis_pekerjaan_saluran,jenis',
            'presentasi' => 'required|numeric',
        ], [
            'jenis.required'      => 'Jenis pekerjaan wajib diisi.',
            'jenis.unique'        => 'Jenis pekerjaan sudah digunakan.',
            'presentasi.required' => 'Presentasi wajib diisi.',
        ]);

        $total = JenisPekerjaanSaluran::sum('presentasi');
        if (($total + $request->presentasi) > 100) {
            return response()->json([
                'status' => 'error',
                'errors' => ['presentasi' => ['Total persen melebihi 100']],
            ], 422);
        }

        $db = [
            'jenis'      => $request->jenis,
            'presentasi' => $request->presentasi,
        ];

        $jps = JenisPekerjaanSaluran::create($db);
        $this->logCreate('Jenis Pekerjaan Saluran', $jps->id);

        return response()->json(['status' => 'success']);
    }

    public function update(Request $request, $id)
    {
        $data = JenisPekerjaanSaluran::findOrFail($id);

        $request->validate([
            'jenis'      => 'required|unique:jenis_pekerjaan_saluran,jenis,' . $data->id . ',id',
            'presentasi' => 'required|numeric',
        ], [
            'jenis.required'      => 'Jenis pekerjaan wajib diisi.',
            'jenis.unique'        => 'Jenis pekerjaan sudah digunakan.',
            'presentasi.required' => 'Presentasi wajib diisi.',
        ]);

        $total     = JenisPekerjaanSaluran::sum('presentasi');
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
        ];

        $data->update($db);
        $this->logEdit('Jenis Pekerjaan Saluran', $data->id);

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        $data = JenisPekerjaanSaluran::findOrFail($id);

        $this->logDelete('Jenis Pekerjaan Saluran', $data->id);
        $data->delete();

        return response()->json(['status' => 'success']);
    }
}
