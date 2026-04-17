<?php
namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\MarketingAgent;
use App\Traits\LogAktivitasTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class MarketingAgentController extends Controller
{
    use LogAktivitasTrait;
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();
        if ($request->ajax()) {

            $data = MarketingAgent::orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kode_agent', function ($row) {
                    $iconUrl = ($row->foto != null && $row->foto != '')
                    ? asset('assets/marketing/marketing_agent/' . $row->foto)
                    : ($row->jenis_kelamin == 1
                        ? asset('assets/img/men-icon.png')
                        : asset('assets/img/women-icon.png'));

                    return '<img src="' . $iconUrl . '" alt="icon" style="width:30px; height:30px; border-radius:50%; margin-right:8px;">' . e($row->kode_agent);
                })
                ->addColumn('alamat', function ($row) {
                    $alamat = e($row->alamat);
                    $noTelp = $row->no_telp
                    ? '<span class="badge badge-danger">No Telp : ' . e($row->no_telp) . '</span>'
                    : '';

                    return "{$alamat}" . ($noTelp ? "<br>{$noTelp}" : '');
                })
                ->addColumn('status', function ($row) {
                    return $row->status == 1
                    ? '<span class="badge badge-success">Aktif</span>'
                    : '<span class="badge badge-danger">Tidak Aktif</span>';
                })
                ->addColumn('rekening', function ($row) {
                    $nama_bank   = e($row->nama_bank);
                    $no_rekening = '<span class="badge badge-success">' . e($row->no_rekening) . '</span>';
                    $atas_nama   = $row->atas_nama ? '<small>An: ' . e($row->atas_nama) . '</small>' : '';

                    return "{$nama_bank}<br>{$no_rekening}" . ($atas_nama ? "<br>{$atas_nama}" : '');
                })
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl   = route('marketing-agent.edit', $row->id);
                    $deleteUrl = route('marketing-agent.destroy', $row->id);
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
                ->rawColumns(['action', 'kode_agent', 'alamat', 'status', 'rekening'])
                ->make(true);
        }

        return view('admin.marketing.marketing_agent.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $rules = [
            'nama_agent' => 'required|unique:marketing_agent,nama_agent',
            // 'jenis_kelamin'  => 'required',
            // 'pekerjaan'      => 'required',
            // 'no_telp'        => 'required|unique:marketing_agent,no_telp',
            // 'alamat'         => 'nullable',
            // 'sosmed'         => 'nullable',
            // 'status'         => 'required',
            // 'nama_bank'      => 'required',
            // 'no_rekening'    => 'required|unique:marketing_agent,no_rekening',
            // 'atas_nama'      => 'required',
            // 'foto'           => 'nullable|mimes:jpg,jpeg,png|max:2048',
        ];

        $messages = [
            'nama_agent.required' => 'Nama Marketing Agent wajib diisi.',
            'nama_agent.unique'   => 'Nama Marketing Agent sudah terdaftar.',

            // 'jenis_kelamin.required'  => 'Jenis kelamin wajib diisi.',

            // 'pekerjaan.required'      => 'Pekerjaan wajib diisi.',

            // 'no_telp.required'        => 'Nomor telepon wajib diisi.',
            // 'no_telp.unique'          => 'Nomor telepon sudah terdaftar.',

            // 'status.required'         => 'Status wajib diisi.',

            // 'nama_bank.required'      => 'Nama bank wajib diisi.',

            // 'no_rekening.required'    => 'Nomor rekening wajib diisi.',
            // 'no_rekening.unique'      => 'Nomor rekening sudah terdaftar.',

            // 'atas_nama.required'      => 'Atas nama wajib diisi.',

            // 'foto.mimes'              => 'Foto harus berformat JPG, JPEG, atau PNG.',
            // 'foto.max'                => 'Ukuran foto maksimal 2 MB.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {

            $lastMarketing = DB::table('marketing_agent')
                ->where('kode_agent', 'like', 'M-%')
                ->orderByDesc('id')
                ->first();

            $nextNumber = ($lastMarketing && preg_match('/M-(\d+)/', $lastMarketing->kode_agent, $matches))
            ? intval($matches[1]) + 1
            : 1;

            $kodeMarketing = 'F-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $ext  = $foto->getClientOriginalExtension();

                $filename = Str::random(25) . '.' . $ext;
                $foto->move(public_path('assets/marketing/marketing_agent/'), $filename);
            }

            $marketing = MarketingAgent::create([
                'kode_agent' => $kodeMarketing,
                'nama_agent' => $request->nama_agent,
                'alamat'         => $request->alamat ?? '',
                'jenis_kelamin'  => $request->jenis_kelamin ?? 1,
                'pekerjaan'      => $request->pekerjaan ?? '',
                'no_telp'        => $request->no_telp ?? '',
                'foto'           => $filename ?? '',
                'status'         => $request->status ?? 1,
                'sosmed'         => $request->sosmed ?? '',
                'nama_bank'      => $request->nama_bank ?? '',
                'no_rekening'    => $request->no_rekening ?? '',
                'atas_nama'      => $request->atas_nama ?? '',
            ]);

            $this->logCreate('Marketing Agent', $marketing->id);

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $data = MarketingAgent::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = MarketingAgent::findOrFail($id);

        $rules = [
            'nama_agent' => 'required|unique:marketing_agent,nama_agent,' . $data->id . ',id',
            // 'jenis_kelamin'  => 'required',
            // 'pekerjaan'      => 'required',
            // 'no_telp'        => 'required|unique:marketing_agent,no_telp,' . $data->id . ',id',
            // 'alamat'         => 'nullable',
            // 'sosmed'         => 'nullable',
            // 'status'         => 'required',
            // 'nama_bank'      => 'required',
            // 'no_rekening'    => 'required|unique:marketing_agent,no_rekening,' . $data->id . ',id',
            // 'atas_nama'      => 'required',
            // 'foto'           => 'nullable|mimes:jpg,jpeg,png|max:2048',
        ];

        $messages = [
            'nama_agent.required' => 'Nama Marketing Agent wajib diisi.',
            'nama_agent.unique'   => 'Nama Marketing Agent sudah terdaftar.',

            // 'jenis_kelamin.required'  => 'Jenis kelamin wajib diisi.',

            // 'pekerjaan.required'      => 'Pekerjaan wajib diisi.',

            // 'no_telp.required'        => 'Nomor telepon wajib diisi.',
            // 'no_telp.unique'          => 'Nomor telepon sudah terdaftar.',

            // 'status.required'         => 'Status wajib diisi.',

            // 'nama_bank.required'      => 'Nama bank wajib diisi.',

            // 'no_rekening.required'    => 'Nomor rekening wajib diisi.',
            // 'no_rekening.unique'      => 'Nomor rekening sudah terdaftar.',

            // 'atas_nama.required'      => 'Atas nama wajib diisi.',

            // 'foto.mimes'              => 'Foto harus berformat JPG, JPEG, atau PNG.',
            // 'foto.max'                => 'Ukuran foto maksimal 2 MB.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {

            if ($request->hasFile('foto')) {
                if (! empty($data->foto) && file_exists(public_path('assets/marketing/marketing_agent/' . $data->foto))) {
                    unlink(public_path('assets/marketing/marketing_agent/' . $data->foto));
                }

                $foto     = $request->file('foto');
                $ext      = $foto->getClientOriginalExtension();
                $filename = Str::random(25) . '.' . $ext;
                $foto->move(public_path('assets/marketing/marketing_agent/'), $filename);
            }

            $db = [
                'nama_agent' => $request->nama_agent,
                'alamat'         => $request->alamat ?? '',
                'jenis_kelamin'  => $request->jenis_kelamin ?? 1,
                'pekerjaan'      => $request->pekerjaan ?? '',
                'no_telp'        => $request->no_telp ?? '',
                'foto'           => $filename ?? '',
                'status'         => $request->status ?? 1,
                'sosmed'         => $request->sosmed ?? '',
                'nama_bank'      => $request->nama_bank ?? '',
                'no_rekening'    => $request->no_rekening ?? '',
                'atas_nama'      => $request->atas_nama ?? '',
            ];

            $data->update($db);
            $this->logEdit('Marketing Agent', $data->id);

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
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
            $data = MarketingAgent::findOrFail($id);

            if (! empty($data->foto) && file_exists(public_path('assets/marketing/marketing_agent/' . $data->foto))) {
                unlink(public_path('assets/marketing/marketing_agent/' . $data->foto));
            }

            $this->logDelete('Marketing Agent', $data->id);
            $data->delete();

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }
}
