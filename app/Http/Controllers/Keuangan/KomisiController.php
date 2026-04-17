<?php
namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\Bank;
use App\Models\Customer;
use App\Models\Komisi;
use App\Models\Pengeluaran;
use App\Traits\LogAktivitasTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class KomisiController extends Controller
{
    use LogAktivitasTrait;
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = Komisi::with(['customer'])->orderByDesc('tanggal_komisi');

            if ($request->filled('filter_tanggal')) {
                $data->whereDate('tanggal_komisi', $request->filter_tanggal);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($row) {
                    return Carbon::parse($row->tanggal_komisi)->translatedFormat('j F Y');
                })
                ->editColumn('nominal_inhouse', function ($row) {
                    return '
                    <div class="d-flex justify-content-between harga-format w-100">
                        <span>Rp.</span>
                        <span>' . number_format($row->nominal_inhouse, 0, ',', '.') . '</span>
                    </div>';
                })
                ->editColumn('nominal_agent', function ($row) {
                    return '
                    <div class="d-flex justify-content-between harga-format w-100">
                        <span>Rp.</span>
                        <span>' . number_format($row->nominal_agent, 0, ',', '.') . '</span>
                    </div>';
                })
                ->filterColumn('tanggal', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->WhereDate('tanggal_komisi', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl   = route('komisi.edit', $row->id);
                    $deleteUrl = route('komisi.destroy', $row->id);
                    $btn       = '<div class="text-center">';

                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-sm edit-button" data-id="' . e($row->id) . '" data-url="' . e($editUrl) . '">Edit</button>';
                    }

                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="submit" class="delete-button btn btn-danger btn-sm">
                            Hapus
                        </button>
                     </form>';
                    }

                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action', 'tanggal', 'nominal_inhouse', 'nominal_agent'])
                ->make(true);
        }

        $customers = Customer::orderBy('id', 'desc')->get();
        $rekenings = Bank::all();

        return view('admin.keuangan.komisi.index', compact('permissions', 'customers', 'rekenings'));
    }

    public function edit($id)
    {
        $list = Komisi::with('customer')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $list,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_komisi' => 'required|date',
            'id_customer'    => 'required',
            'id_bank'        => 'required',
        ], [
            'tanggal_komisi.required' => 'Tanggal Komisi wajib diisi.',
            'tanggal_komisi.date'     => 'Format tanggal komisi tidak valid.',
            'id_customer.required'    => 'Customer wajib dipilih.',
            'id_bank.required'        => 'Rekening wajib dipilih.',

        ]);

        DB::beginTransaction();
        try {

            $db = [
                'tanggal_komisi'  => $request->tanggal_komisi,
                'id_customer'     => $request->id_customer,
                'id_bank'         => $request->id_bank,
                'nominal_inhouse' => str_replace('.', '', $request->nominal_inhouse),
                'persen_inhouse'  => $request->persen_inhouse,
                'nominal_agent'   => str_replace('.', '', $request->nominal_agent),
                'persen_agent'    => $request->persen_agent,
            ];

            $km = Komisi::create($db);
            $this->logCreate('Komisi Marketing', $km->id);

            $cust = Customer::find($request->id_customer);

            if ($request->nominal_inhouse && str_replace('.', '', $request->nominal_inhouse) > 0) {
                Pengeluaran::create([
                    'tanggal'               => $request->tanggal_komisi,
                    'id_bank'               => $request->id_bank,
                    'id_komisi'             => $km->id,
                    'nominal'               => str_replace('.', '', $request->nominal_inhouse),
                    'id_kategori_transaksi' => 21,
                    'lampiran'              => '',
                    'keterangan'            => 'Komisi Inhouse - Customer : ' . $cust->nama_lengkap,
                ]);
            }

            if ($request->nominal_agent && str_replace('.', '', $request->nominal_agent) > 0) {
                Pengeluaran::create([
                    'tanggal'               => $request->tanggal_komisi,
                    'id_bank'               => $request->id_bank,
                    'id_komisi'             => $km->id,
                    'nominal'               => str_replace('.', '', $request->nominal_agent),
                    'id_kategori_transaksi' => 22,
                    'lampiran'              => '',
                    'keterangan'            => 'Komisi Agent - Customer : ' . $cust->nama_lengkap,
                ]);
            }

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_komisi' => 'required|date',
            'id_customer'    => 'required',
            'id_bank'        => 'required',
        ], [
            'tanggal_komisi.required' => 'Tanggal Komisi wajib diisi.',
            'tanggal_komisi.date'     => 'Format tanggal komisi tidak valid.',
            'id_customer.required'    => 'Customer wajib dipilih.',
            'id_bank.required'        => 'Rekening wajib dipilih.',
        ]);

        DB::beginTransaction();
        try {

            $km = Komisi::findOrFail($id);

            $db = [
                'tanggal_komisi'  => $request->tanggal_komisi,
                'id_customer'     => $request->id_customer,
                'id_bank'         => $request->id_bank,

                'nominal_inhouse' => str_replace('.', '', $request->nominal_inhouse),
                'persen_inhouse'  => $request->persen_inhouse,
                'nominal_agent'   => str_replace('.', '', $request->nominal_agent),
                'persen_agent'    => $request->persen_agent,
            ];

            $km->update($db);

            Pengeluaran::where('id_komisi', $km->id)->delete();

            $cust = Customer::find($request->id_customer);

            if ($request->nominal_inhouse && str_replace('.', '', $request->nominal_inhouse) > 0) {
                Pengeluaran::create([
                    'tanggal'               => $request->tanggal_komisi,
                    'id_bank'               => $request->id_bank,
                    'id_komisi'             => $km->id,
                    'nominal'               => str_replace('.', '', $request->nominal_inhouse),
                    'id_kategori_transaksi' => 21,
                    'lampiran'              => '',
                    'keterangan'            => 'Komisi Inhouse - Customer : ' . $cust->nama_lengkap,
                ]);
            }

            if ($request->nominal_agent && str_replace('.', '', $request->nominal_agent) > 0) {
                Pengeluaran::create([
                    'tanggal'               => $request->tanggal_komisi,
                    'id_bank'               => $request->id_bank,
                    'id_komisi'             => $km->id,
                    'nominal'               => str_replace('.', '', $request->nominal_agent),
                    'id_kategori_transaksi' => 22,
                    'lampiran'              => '',
                    'keterangan'            => 'Komisi Agent - Customer : ' . $cust->nama_lengkap,
                ]);
            }

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $km = Komisi::findOrFail($id);

            Pengeluaran::where('id_komisi', $km->id)->delete();
            $km->delete();

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }
}
