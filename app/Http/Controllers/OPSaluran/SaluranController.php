<?php

namespace App\Http\Controllers\OPSaluran;

use App\Models\Jalan;
use App\Models\Saluran;
use App\Traits\LogAktivitasTrait;
use Illuminate\Http\Request;
use App\Models\LokasiKavling;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Pengaturan\HakAksesController;

class SaluranController extends Controller
{
    use LogAktivitasTrait;
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = Saluran::orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('id_lokasi', function ($row) {
                    return LokasiKavling::find($row->id_lokasi)->nama_kavling ?? '-';
                })
                ->addColumn('id_jalan', content: function ($row) {
                    return Jalan::find($row->id_jalan)->nama ?? '-';
                })
                ->addColumn('panjang', function ($row) {
                    return number_format($row->panjang, 0, ',', '.') . ' m';
                })
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('saluran.edit', $row->id);
                    $deleteUrl = route('saluran.destroy', $row->id);

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

        $lokasiList = LokasiKavling::all();

        return view('admin.op_saluran.saluran.index', compact('permissions', 'lokasiList'));
    }

    public function edit($id)
    {
        $list = Saluran::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $list,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_lokasi' => 'required',
            'id_jalan' => 'required',
            'nama' => 'required|unique:saluran,nama',
            'panjang' => 'required',
        ], [
            'id_lokasi.required' => 'Lokasi Perumahan wajib diisi.',
            'id_jalan.required' => 'Jalan wajib diisi.',
            'nama.required' => 'Nama saluran wajib diisi.',
            'nama.unique' => 'Nama saluran sudah digunakan.',
            'panjang.required' => 'Panjang saluran wajib diisi.',
        ]);

        $db = [
            'id_lokasi' => $request->id_lokasi,
            'id_jalan' => $request->id_jalan,
            'nama' => $request->nama,
            'panjang' => str_replace('.', '', $request->panjang),
        ];

        $s = Saluran::create($db);
        $this->logCreate('Saluran', $s->id);

        return response()->json(['status' => 'success']);
    }

    public function update(Request $request, $id)
    {
        $data = Saluran::findOrFail($id);

        $request->validate([
            'id_lokasi' => 'required',
            'nama' => 'required|unique:jalan,nama,' . $data->id . ',id',
            'panjang' => 'required',
            'lebar' => 'required',
        ], [
            'id_lokasi.required' => 'Lokasi Perumahan wajib diisi.',
            'nama.required' => 'Nama jalan wajib diisi.',
            'nama.unique' => 'Nama jalan sudah digunakan.',
            'panjang.required' => 'Panjang jalan wajib diisi.',
            'lebar.required' => 'Lebar jalan wajib diisi.',
        ]);

        $db = [
            'id_lokasi' => $request->id_lokasi,
            'nama' => $request->nama,
            'panjang' => str_replace('.', '', $request->panjang),
            'lebar' => str_replace('.', '', $request->lebar),
            'luas' => str_replace('.', '', $request->panjang) * str_replace('.', '', $request->lebar),
        ];

        $data->update($db);
        $this->logEdit('Saluran', $data->id);

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        $data = Saluran::findOrFail($id);

        $this->logDelete('Saluran', $data->id);
        $data->delete();

        return response()->json(['status' => 'success']);
    }
}
