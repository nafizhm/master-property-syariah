<?php

namespace App\Http\Controllers\OPJalan;

use App\Models\Jalan;
use App\Traits\LogAktivitasTrait;
use Illuminate\Http\Request;
use App\Models\LokasiKavling;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Pengaturan\HakAksesController;

class JalanController extends Controller
{
    use LogAktivitasTrait;
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = Jalan::orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('id_lokasi', function ($row) {
                    return LokasiKavling::find($row->id_lokasi)->nama_kavling ?? '-';
                })
                ->addColumn('panjang', function ($row) {
                    return number_format($row->panjang, 0, ',', '.') . ' m';
                })
                ->addColumn('lebar', function ($row) {
                    return number_format($row->lebar, 0, ',', '.') . ' m';
                })
                ->addColumn('luas', function ($row) {
                    return number_format($row->luas, 0, ',', '.') . ' m²';
                })
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('jalan.edit', $row->id);
                    $deleteUrl = route('jalan.destroy', $row->id);

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

        return view('admin.op_jalan.jalan.index', compact('permissions', 'lokasiList'));
    }

    public function edit($id)
    {
        $list = Jalan::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $list,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_lokasi' => 'required',
            'nama' => 'required|unique:jalan,nama',
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

        $jl = Jalan::create($db);
        $this->logCreate('Jalan', $jl->id);

        return response()->json(['status' => 'success']);
    }

    public function update(Request $request, $id)
    {
        $data = Jalan::findOrFail($id);

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
        $this->logEdit('Jalan', $data->id);

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        $data = Jalan::findOrFail($id);
        $this->logDelete('Jalan', $data->id);
        $data->delete();

        return response()->json(['status' => 'success']);
    }
}
