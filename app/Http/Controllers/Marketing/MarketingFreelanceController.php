<?php
namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\MarketingFreelance;
use App\Traits\LogAktivitasTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class MarketingFreelanceController extends Controller
{
    use LogAktivitasTrait;
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();
        if ($request->ajax()) {

            $data = MarketingFreelance::orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kode_freelance', function ($row) {
                    $iconUrl = ($row->foto != null && $row->foto != '')
                    ? asset('assets/marketing/marketing_freelance/' . $row->foto)
                    : ($row->jenis_kelamin == 1
                        ? asset('assets/img/men-icon.png')
                        : asset('assets/img/women-icon.png'));

                    return '<img src="' . $iconUrl . '" alt="icon" style="width:30px; height:30px; border-radius:50%; margin-right:8px;">' . e($row->kode_freelance);
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
                    $editUrl   = route('marketing-freelance.edit', $row->id);
                    $deleteUrl = route('marketing-freelance.destroy', $row->id);
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
                ->rawColumns(['action', 'kode_freelance', 'alamat', 'status', 'rekening'])
                ->make(true);
        }

        return view('admin.marketing.marketing_freelance.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $rules = [
            'nama_freelance' => 'required|unique:marketing_freelance,nama_freelance',
            // 'jenis_kelamin'  => 'required',
            // 'pekerjaan'      => 'required',
            // 'no_telp'        => 'required|unique:marketing_freelance,no_telp',
            // 'alamat'         => 'nullable',
            // 'sosmed'         => 'nullable',
            // 'status'         => 'required',
            // 'nama_bank'      => 'required',
            // 'no_rekening'    => 'required|unique:marketing_freelance,no_rekening',
            // 'atas_nama'      => 'required',
            // 'foto'           => 'nullable|mimes:jpg,jpeg,png|max:2048',
        ];

        $messages = [
            'nama_freelance.required' => 'Nama Marketing Freelance wajib diisi.',
            'nama_freelance.unique'   => 'Nama Marketing Freelance sudah terdaftar.',

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

            $lastMarketing = DB::table('marketing_freelance')
                ->where('kode_freelance', 'like', 'M-%')
                ->orderByDesc('id')
                ->first();

            $nextNumber = ($lastMarketing && preg_match('/M-(\d+)/', $lastMarketing->kode_freelance, $matches))
            ? intval($matches[1]) + 1
            : 1;

            $kodeMarketing = 'F-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $ext  = $foto->getClientOriginalExtension();

                $filename = Str::random(25) . '.' . $ext;
                $foto->move(public_path('assets/marketing/marketing_freelance/'), $filename);
            }

            $marketing = MarketingFreelance::create([
                'kode_freelance' => $kodeMarketing,
                'nama_freelance' => $request->nama_freelance,
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

            $this->logCreate('Marketing Freelance', $marketing->id);

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
        $data = MarketingFreelance::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = MarketingFreelance::findOrFail($id);

        $rules = [
            'nama_freelance' => 'required|unique:marketing_freelance,nama_freelance,' . $data->id . ',id',
            // 'jenis_kelamin'  => 'required',
            // 'pekerjaan'      => 'required',
            // 'no_telp'        => 'required|unique:marketing_freelance,no_telp,' . $data->id . ',id',
            // 'alamat'         => 'nullable',
            // 'sosmed'         => 'nullable',
            // 'status'         => 'required',
            // 'nama_bank'      => 'required',
            // 'no_rekening'    => 'required|unique:marketing_freelance,no_rekening,' . $data->id . ',id',
            // 'atas_nama'      => 'required',
            // 'foto'           => 'nullable|mimes:jpg,jpeg,png|max:2048',
        ];

        $messages = [
            'nama_freelance.required' => 'Nama Marketing Freelance wajib diisi.',
            'nama_freelance.unique'   => 'Nama Marketing Freelance sudah terdaftar.',

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
                if (! empty($data->foto) && file_exists(public_path('assets/marketing/marketing_freelance/' . $data->foto))) {
                    unlink(public_path('assets/marketing/marketing_freelance/' . $data->foto));
                }

                $foto     = $request->file('foto');
                $ext      = $foto->getClientOriginalExtension();
                $filename = Str::random(25) . '.' . $ext;
                $foto->move(public_path('assets/marketing/marketing_freelance/'), $filename);
            }

            $db = [
                'nama_freelance' => $request->nama_freelance,
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
            $this->logEdit('Marketing Freelance', $data->id);

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
            $data = MarketingFreelance::findOrFail($id);

            if (! empty($data->foto) && file_exists(public_path('assets/marketing/marketing_freelance/' . $data->foto))) {
                unlink(public_path('assets/marketing/marketing_freelance/' . $data->foto));
            }

            $this->logDelete('Marketing Freelance', $data->id);
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
