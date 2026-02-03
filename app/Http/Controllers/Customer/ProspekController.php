<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\Customer;
use App\Models\Freelance;
use App\Models\KavlingPeta;
use App\Models\Marketing;
use App\Models\MarketingFreelance;
use App\Models\MarketingOffline;
use App\Models\ProspekCustomer;
use App\Traits\LogAktivitasTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ProspekController extends Controller
{
    use LogAktivitasTrait;
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();
        if ($request->ajax()) {

            $data = ProspekCustomer::with('marketing', 'freelance')->orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('nama_lengkap', function ($row) {
                    return '
                    <div>
                        <p>' . e($row->nama_lengkap) . '</p>
                        <p class="badge bg-info">' . e($row->marketing->nama_marketing ?? $row->freelance->nama_freelance) . '</p>
                    </div>
                ';
                })
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl   = route('prospek.edit', $row->id);
                    $deleteUrl = route('prospek.destroy', $row->id);

                    $btn = '<div class="text-center">';

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
                ->rawColumns(['action', 'nama_lengkap'])
                ->make(true);
        }

        $marketing = MarketingOffline::all();
        $freelance = MarketingFreelance::all();

        return view('admin.customer.prospek.index', compact('permissions', 'marketing', 'freelance'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'penghasilan' => str_replace('.', '', $request->penghasilan),
        ]);

        $request->validate([
            'tgl_terima'       => 'required',
            'nama_lengkap'     => 'required|string',
            'no_wa'            => 'required',
            'usia'             => 'required',
            'pekerjaan'        => 'required',
            'penghasilan'      => 'required|gt:0',
            'sumber_informasi' => 'required',
            'rangking'         => 'required',
            'id_marketing'     => 'required',
        ], [
            'tgl_terima.required'       => 'Tanggal wajib diisi.',
            'nama_lengkap.required'     => 'Nama lengkap wajib diisi.',
            'nama_lengkap.max'          => 'Nama lengkap maksimal 20 karakter.',
            'no_wa.required'            => 'Nomor telepon wajib diisi.',
            'usia.required'             => 'Usia wajib dipilih.',
            'pekerjaan.required'        => 'Pekerjaan wajib diisi.',
            'penghasilan.required'      => 'Penghasilan wajib diisi.',
            'penghasilan.numeric'       => 'penghasilan harus berupa angka.',
            'sumber_informasi.required' => 'Sumber informasi wajib diisi.',
            'rangking.required'         => 'Rangking wajib dipilih.',
            'id_marketing.required'     => 'Marketing wajib dipilih.',
            'penghasilan.gt'            => 'Penghasilan harus lebih besar dari 0.',
        ]);

        DB::beginTransaction();
        try {

            $prospek = ProspekCustomer::create([
                'tgl_terima'       => $request->tgl_terima,
                'nama_lengkap'     => $request->nama_lengkap,
                'no_telp'          => $request->no_wa,
                'usia'             => $request->usia,
                'pekerjaan'        => $request->pekerjaan,
                'penghasilan'      => str_replace('.', '', $request->penghasilan),
                'sumber_informasi' => $request->sumber_informasi,
                'rangking'         => $request->rangking,
                'id_marketing'     => $request->id_marketing,
                'id_freelance'     => $request->id_freelance ?? 0,
                'keterangan_belum' => $request->keterangan_belum ?? '',
                'email'            => $request->email ?? '',
            ]);

            $this->logCreate('Prospek', $prospek->id);

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $list = ProspekCustomer::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $list,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->merge([
            'penghasilan' => str_replace('.', '', $request->penghasilan),
        ]);
        $request->validate([
            'tgl_terima'       => 'required',
            'nama_lengkap'     => 'required|string',
            'no_wa'            => 'required',
            'usia'             => 'required',
            'pekerjaan'        => 'required',
            'penghasilan'      => 'required|gt:0',
            'sumber_informasi' => 'required',
            'rangking'         => 'required',
            'id_marketing'     => 'required',
            'keterangan_belum' => 'required',
        ], [
            'tgl_terima.required'       => 'Tanggal wajib diisi.',
            'nama_lengkap.required'     => 'Nama lengkap wajib diisi.',
            'nama_lengkap.max'          => 'Nama lengkap maksimal 20 karakter.',
            'no_wa.required'            => 'Nomor telepon wajib diisi.',
            'usia.required'             => 'Usia wajib dipilih.',
            'pekerjaan.required'        => 'Pekerjaan wajib diisi.',
            'penghasilan.required'      => 'Penghasilan wajib diisi.',
            'penghasilan.numeric'       => 'penghasilan harus berupa angka.',
            'sumber_informasi.required' => 'Sumber informasi wajib diisi.',
            'rangking.required'         => 'Rangking wajib dipilih.',
            'id_marketing.required'     => 'Marketing wajib dipilih.',
            'keterangan_belum.required' => 'Keterangan wajib diisi.',
            'penghasilan.gt'            => 'Penghasilan harus lebih besar dari 0.',
        ]);

        $data = ProspekCustomer::findOrFail($id);

        DB::beginTransaction();
        try {

            $data->update([
                'tgl_terima'       => $request->tgl_terima,
                'nama_lengkap'     => $request->nama_lengkap,
                'no_telp'          => $request->no_wa,
                'usia'             => $request->usia,
                'pekerjaan'        => $request->pekerjaan,
                'penghasilan'      => str_replace('.', '', $request->penghasilan),
                'sumber_informasi' => $request->sumber_informasi,
                'rangking'         => $request->rangking,
                'id_marketing'     => $request->id_marketing,
                'id_freelance'     => $request->id_freelance ?? 0,
                'keterangan_belum' => $request->keterangan_belum,
                'email'            => $request->email ?? '',
            ]);

            $this->logEdit('Prospek', $data->id);
            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
            ], 500);
        }
    }

    public function utj($id)
    {
        $data = prospekCustomer::find($id);

        $marketing = DB::table('marketing')->get();
        $freelance = DB::table('freelance')->get();
        $progres   = DB::table('progres_list_penjualan')->get();
        $bank      = DB::table('bank')->get();
        $lokasi    = DB::table('lokasi_kavling')->get();
        $kavling   = DB::table('kavling_peta')->get();

        return view('admin.customer.prospek.prosesUtj', compact('lokasi', 'data', 'kavling', 'marketing', 'freelance', 'progres', 'bank'));
    }

    public function createUtj(Request $request)
    {
        $request->validate([
            'tgl_terima'        => 'required',
            'nama_lengkap'      => 'required|string',
            'no_wa'             => 'required',
            'pekerjaan'         => 'required',
            'id_marketing'      => 'required',
            'id_freelance'      => 'required',
            'no_ktp'            => 'required|numeric',
            'no_ktp_p'          => 'required|numeric',
            'tempat_lahir'      => 'required|string|max:50',
            'tgl_lahir'         => 'required',
            'jenis_kelamin'     => 'required',
            'email'             => 'required',
            'npwp'              => 'required',
            'alamat'            => 'required',
            'alamat_domisili'   => 'required',
            'id_lokasi'         => 'required',
            'id_kavling'        => 'required',
            'referal_fee'       => 'required',
            'id_bank'           => 'required',
            'id_status_progres' => 'required',
            'penghasilan'       => 'required',
        ], [
            'tgl_terima.required'        => 'Tanggal wajib diisi.',
            'nama_lengkap.required'      => 'Nama lengkap wajib diisi.',
            'nama_lengkap.max'           => 'Nama lengkap maksimal 20 karakter.',
            'no_wa.required'             => 'Nomor telepon wajib diisi.',
            'pekerjaan.required'         => 'Pekerjaan wajib diisi.',
            'id_marketing.required'      => 'Marketing wajib dipilih.',
            'id_freelance.required'      => 'Freelance wajib dipilih.',
            'no_ktp.required'            => 'No. KTP wajib diisi.',
            'no_ktp.numeric'             => 'No. KTP harus berupa angka.',
            'no_ktp_p.required'          => 'No. KTP Pasangan wajib diisi.',
            'no_ktp_p.numeric'           => 'No. KTP Pasangan harus berupa angka.',
            'tempat_lahir.required'      => 'Tempat lahir wajib diisi.',
            'tempat_lahir.max'           => 'Tempat lahir maksimal 50 karakter.',
            'tgl_lahir.required'         => 'Tanggal lahir wajib diisi.',
            'jenis_kelamin.required'     => 'Jenis kelamin wajib dipilih.',
            'email.required'             => 'Email wajib diisi.',
            'npwp.required'              => 'NPWP wajib diisi.',
            'alamat.required'            => 'Alamat wajib diisi.',
            'alamat_domisili.required'   => 'Alamat domisili wajib diisi.',
            'id_lokasi.required'         => 'Lokasi wajib dipilih.',
            'id_kavling.required'        => 'Kavling wajib dipilih.',
            'referal_fee.required'       => 'Referal fee wajib diisi.',
            'referal_fee.max'            => 'Referal fee maksimal 20 karakter.',
            'id_bank.required'           => 'Bank wajib dipilih.',
            'id_status_progres.required' => 'Status progres wajib dipilih.',
            'penghasilan.required'       => 'Penghasilan wajib diisi.',
        ]);

        $lastCustomer = DB::table('nasabah')
            ->where('kode_customer', 'like', 'GDI-%')
            ->orderByDesc('id_customer')
            ->first();

        if ($lastCustomer && preg_match('/GDI-(\d+)/', $lastCustomer->kode_customer, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        $kodeCustomer = 'GDI-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        Customer::create([
            'kode_customer'        => $kodeCustomer,
            'tgl_terima'           => $request->tgl_terima,
            'nama_lengkap'         => $request->nama_lengkap,
            'no_wa'                => $request->no_wa,
            'no_telp'              => $request->no_wa,
            'pekerjaan'            => $request->pekerjaan,
            'id_marketing'         => $request->id_marketing,
            'id_freelance'         => $request->id_freelance,
            'no_ktp'               => $request->no_ktp,
            'no_ktp_p'             => $request->no_ktp_p,
            'tempat_lahir'         => $request->tempat_lahir,
            'tgl_lahir'            => $request->tgl_lahir,
            'jenis_kelamin'        => $request->jenis_kelamin,
            'email'                => $request->email,
            'npwp'                 => $request->npwp,
            'alamat'               => $request->alamat,
            'alamat_domisili'      => $request->alamat_domisili,
            'id_lokasi'            => $request->id_lokasi,
            'id_kavling'           => $request->id_kavling,
            'hrg_rumah'            => $request->hrg_jual,
            'referal_fee'          => $request->referal_fee,
            'id_bank'              => $request->id_bank,
            'id_status_progres'    => $request->id_status_progres,
            'penghasilan'          => $request->penghasilan,
            'id_admin_pemberkasan' => $request->id_admin_pemberkasan ?? '0',
            'stt_delete'           => '0',
            'no_kk'                => '',
            'ket_cashback'         => $request->ket_cashback ?? '',
            'jenis_pembelian'      => $request->jenis_pembelian ?? '',

        ]);

        return response()->json(['status' => 'success']);
    }

    public function getKavling($idLokasi)
    {
        $data = KavlingPeta::where('id_lokasi', $idLokasi)
            ->select('id_kavling', 'kode_kavling', 'hrg_jual')
            ->get();

        return response()->json($data);
    }

    public function getHargaKavling($id_kavling)
    {
        $data      = KavlingPeta::findOrFail($id_kavling);
        $formatted = 'Rp. ' . number_format($data->hrg_jual, 0, ',', '.');

        return response()->json([
            'hrg_jual'  => $data->hrg_jual,
            'formatted' => $formatted,
        ]);
    }

    public function destroy($id)
    {
        $prospek = ProspekCustomer::findOrFail($id);
        $this->logEdit('Prospek', $prospek->id);
        $prospek->delete();

        return response()->json(['status' => 'success']);
    }
}
