<?php
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\Bank;
use App\Traits\LogAktivitasTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BankTransaksiController extends Controller
{
    use LogAktivitasTrait;
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = Bank::orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl   = route('bank-transaksi.edit', $row->id);
                    $deleteUrl = route('bank-transaksi.destroy', $row->id);

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

        return view('admin.master.bank_transaksi.index', compact('permissions'));
    }

    public function edit($id)
    {
        $list = Bank::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $list,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'        => 'required',
            'no_rek'      => 'required',
            'pemilik_rek' => 'required',
        ], [
            'nama.required'        => 'Nama bank wajib diisi.',
            'no_rek.required'      => 'Nomor rekening wajib diisi.',
            'pemilik_rek.required' => 'Pemilik rekening wajib diisi.',
        ]);

        $db = [
            'nama'        => $request->nama,
            'no_rek'      => $request->no_rek,
            'pemilik_rek' => $request->pemilik_rek,
        ];

        $bt = Bank::create($db);
        $this->logCreate('Bank Transaksi', $bt->id);

        return response()->json(['status' => 'success']);
    }

    public function update(Request $request, $id)
    {
        $data = Bank::findOrFail($id);

        $request->validate([
            'nama'        => 'required',
            'no_rek'      => 'required',
            'pemilik_rek' => 'required',
        ], [
            'nama.required'        => 'Nama bank wajib diisi.',
            'no_rek.required'      => 'Nomor rekening wajib diisi.',
            'pemilik_rek.required' => 'Pemilik rekening wajib diisi.',
        ]);

        $db = [
            'nama'        => $request->nama,
            'no_rek'      => $request->no_rek,
            'pemilik_rek' => $request->pemilik_rek,
        ];

        $data->update($db);
        $this->logEdit('Bank Transaksi', $data->id);

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        $data = Bank::findOrFail($id);

        $this->logDelete('Bank Transaksi', $data->id);
        $data->delete();

        return response()->json(['status' => 'success']);
    }
}
