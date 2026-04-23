<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\Bank;
use App\Models\Customer;
use App\Models\KavlingPeta;
use App\Models\LokasiKavling;
use App\Models\MarketingAgent;
use App\Models\MarketingOffline;
use App\Models\MetodeBayar;
use App\Models\Pemasukan;
use App\Models\PengajuanHold;
use App\Models\PersyaratanLegal;
use App\Models\Piutang;
use App\Models\ProgresListPenjualan;
use App\Models\UploudFile;
use App\Traits\LogAktivitasTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class PengajuanHoldController extends Controller
{
    use LogAktivitasTrait;
    protected GenerateNumberController $generator;

    public function __construct(GenerateNumberController $generator)
    {
        $this->generator = $generator;
    }

    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = PengajuanHold::with(['marketing', 'agent', 'lokasi', 'kavling'])->where('stt_reg', '!=', 2)->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama_lengkap', function ($row) {
                    $nama   = '<strong>' . e($row->nama_lengkap) . '</strong>';
                    $noTelp = $row->no_telp
                        ? '<br><small class="text-primary">' . e($row->no_telp) . '</small>'
                        : '';

                    return $nama . $noTelp;
                })

                ->addColumn('stt_reg', function ($row): string {
                    switch ($row->stt_reg) {
                        case 1:
                            return '<span class="badge bg-dark">Pending</span>';
                        case 2:
                            return '<span class="badge bg-success">Disetujui</span>';
                        case 3:
                            return '<span class="badge bg-danger">Ditolak</span>';
                        default:
                            return '<span class="badge bg-secondary">Unknown</span>';
                    }
                })

                ->addColumn('nama_marketing', function ($row) {
                    if ((int) $row->id_marketing === 0) {
                        return 'Non Marketing';
                    }

                    return $row->marketing->nama_marketing ?? '-';
                })

                ->addColumn('kode_kavling', function ($row) {
                    $namaLokasi  = '<strong>' . ($row->lokasi->nama_kavling ?? '-') . '</strong>';
                    $kodeKavling = $row->kavling->kode_kavling ?? '-';

                    return $namaLokasi . '<br>' . $kodeKavling;
                })
                ->addColumn('action', function ($row) use ($permissions): string {
                    $editUrl     = route('pengajuan-hold.edit', $row->id);
                    $deleteUrl   = route('pengajuan-hold.destroy', $row->id);
                    $lampiranUrl = route('pengajuan-hold.show', $row->id);
                    $verifUrl    = route('pengajuan-hold.verifikasi', $row->id);

                    $btn = '<div class="text-start">';

                    if ($permissions['edit'] && $row->stt_reg != 2) {
                        $btn .= '<button class="btn btn-warning btn-xs mr-1 edit-button" data-id="' . e($row->id) . '" data-url="' . e($editUrl) . '">Edit</button>';
                        $btn .= '<a class="btn btn-success btn-xs mr-1" href="' . e($lampiranUrl) . '">Lampiran</a>';
                        $btn .= '<a class="btn btn-primary btn-xs mr-1" href="' . e($verifUrl) . '">Verifikasi</a>';
                    }

                    if ($permissions['hapus'] && $row->stt_reg != 2) {
                        $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="delete-button btn btn-danger btn-xs">Hapus</button></form>';
                    }

                    $btn .= '</div>';

                    return $btn;
                })

                ->rawColumns([
                    'nama_marketing',
                    'nama_lengkap',
                    'kode_kavling',
                    'stt_reg',
                    'action',
                ])

                ->make(true);
        }

        Carbon::setLocale('id');

        $marketing = MarketingOffline::all();
        $agent     = MarketingAgent::all();
        $bank      = Bank::all();
        $progres   = ProgresListPenjualan::all();
        $lokasi    = LokasiKavling::all();

        return view('admin.pengajuan_hold.index', compact('permissions', 'marketing', 'agent', 'lokasi', 'progres', 'bank'));
    }

    public function viewArsip(Request $request)
    {

        if ($request->ajax()) {
            $data = PengajuanHold::with(['marketing', 'agent', 'lokasi', 'kavling'])->where('stt_reg', 2)->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama_lengkap', function ($row) {
                    $nama   = '<strong>' . e($row->nama_lengkap) . '</strong>';
                    $noTelp = $row->no_telp ? '<br><small class="text-muted">' . e($row->no_telp) . '</small>' : '';

                    return $nama . $noTelp;
                })
                ->addColumn('stt_reg', function ($row): string {
                    switch ($row->stt_reg) {
                        case 1:
                            return '<span class="badge bg-warning">Pending</span>';
                        case 2:
                            return '<span class="badge bg-success">Disetujui</span>';
                        case 3:
                            return '<span class="badge bg-danger">Ditolak</span>';
                        default:
                            return '<span class="badge bg-secondary">Unknown</span>';
                    }
                })

                ->addColumn('nama_marketing', function ($row) {
                    return $row->marketing->nama_marketing ?? '-';
                })
                ->addColumn('kode_kavling', function ($row) {
                    $namaLokasi  = '<strong>' . ($row->lokasi->nama_kavling ?? '-') . '</strong>';
                    $kodeKavling = $row->kavling->kode_kavling ?? '-';

                    return $namaLokasi . '<br>' . $kodeKavling;
                })
                ->addColumn('action', function ($row) {
                    $verifUrl  = route('pengajuan-hold.arsip.detail', $row->id);
                    $btn       = '<div class="text-start">';
                    $btn      .= '<a class="btn btn-primary btn-sm mr-1" href="' . e($verifUrl) . '">Detail</a>';
                    $btn      .= '</div>';

                    return $btn;
                })

                ->rawColumns([
                    'nama_marketing',
                    'nama_lengkap',
                    'kode_kavling',
                    'stt_reg',
                    'action',
                ])
                ->make(true);
        }

        Carbon::setLocale('id');

        $marketing = MarketingOffline::all();
        $agent     = MarketingAgent::all();
        $bank      = Bank::all();
        $progres   = ProgresListPenjualan::all();
        $lokasi    = LokasiKavling::all();

        return view('admin.pengajuan_hold.arsip', compact('marketing', 'agent', 'lokasi', 'progres', 'bank'));
    }

    public function arsipDetail($id, Request $request)
    {
        Carbon::setLocale('id');

        $data            = PengajuanHold::findOrFail($id);
        $bankList        = Bank::all();
        $metodeBayarList = MetodeBayar::all();

        if (! empty($data->tgl_booking)) {
            $data->tgl_booking_formatted = Carbon::createFromFormat('Y-m-d', $data->tgl_booking)
                ->locale('id')
                ->translatedFormat('j F Y');
        } else {
            $data->tgl_booking_formatted = null;
        }

        if (! empty($data->tgl_lahir)) {
            $data->tgl_lahir_formatted = Carbon::createFromFormat('Y-m-d', $data->tgl_lahir)
                ->locale('id')
                ->translatedFormat('j F Y');
        } else {
            $data->tgl_lahir_formatted = null;
        }

        return view('admin.pengajuan_hold.detail', compact('data', 'bankList', 'metodeBayarList'));
    }

    public function edit($id)
    {
        $list = PengajuanHold::with('kavling')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $list,
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = PengajuanHold::findOrFail($id);

        $request->merge([
            'booking_fee' => str_replace('.', '', $request->booking_fee),
            'hrg_jual'    => str_replace('.', '', $request->hrg_jual),
        ]);

        $request->validate([
            'tgl_booking'     => 'required',
            'nama_lengkap'    => 'required',
            'nik'             => 'required|digits:16',
            'tempat_lahir'    => 'required',
            'tgl_lahir'       => 'required|date',
            'jenis_kelamin'   => 'required',
            'no_telp'         => 'required',
            'alamat_ktp'      => 'required',
            'email'           => 'nullable|email',
            'id_lokasi'       => 'required',
            'id_kavling'      => 'required',
            'jenis_properti'  => 'required',
            'id_marketing'    => 'required',
            'booking_fee'     => 'required|gt:0',
            'hrg_jual'        => 'required|gt:0',
            'jenis_perumahan' => 'required',
            'jenis_pembelian' => 'required',
        ], [
            'tgl_booking.required'     => 'Tanggal booking wajib diisi.',
            'nama_lengkap.required'    => 'Nama lengkap wajib diisi.',
            'nik.required'             => 'NIK wajib diisi.',
            'nik.digits'               => 'NIK harus terdiri dari 16 digit.',
            'tempat_lahir.required'    => 'Tempat lahir wajib diisi.',
            'tgl_lahir.required'       => 'Tanggal lahir wajib diisi.',
            'tgl_lahir.date'           => 'Tanggal lahir harus berupa tanggal yang valid.',
            'jenis_kelamin.required'   => 'Jenis kelamin wajib dipilih.',
            'no_telp.required'         => 'Nomor telepon wajib diisi.',
            'alamat_ktp.required'      => 'Alamat KTP wajib diisi.',
            'alamat_domisili.required' => 'Alamat domisili wajib diisi.',
            'email.email'              => 'Format email tidak valid.',
            'id_lokasi.required'       => 'Lokasi wajib dipilih.',
            'id_kavling.required'      => 'Kavling wajib dipilih.',
            'jenis_properti.required'  => 'Jenis Properti wajib diisi.',
            'id_marketing.required'    => 'Marketing wajib dipilih.',
            'booking_fee.required'     => 'Booking fee wajib diisi.',
            'booking_fee.gt'           => 'Booking fee harus lebih dari 0.',
            'hrg_jual.required'        => 'Harga jual wajib diisi.',
            'hrg_jual.gt'              => 'Harga jual harus lebih dari 0.',
            'jenis_perumahan.required' => 'Jenis Perumahan wajib dipilih.',
            'jenis_pembelian.required' => 'Jenis Pembelian wajib dipilih.',
        ]);

        DB::beginTransaction();
        try {

            KavlingPeta::find($data->id_kavling)->update(['status' => 0]);
            KavlingPeta::find($request->id_kavling)->update(['status' => 1]);

            $db = [
                'tgl_booking'       => $request->tgl_booking,
                'nama_lengkap'      => $request->nama_lengkap,
                'nik'               => $request->nik,
                'no_telp'           => $request->no_telp,
                'email'             => $request->email,
                'alamat_ktp'        => $request->alamat_ktp,
                'alamat_domisili'   => $request->alamat_domisili,
                'jenis_kelamin'     => $request->jenis_kelamin,
                'tempat_lahir'      => $request->tempat_lahir,
                'tgl_lahir'         => $request->tgl_lahir,
                'npwp'              => $request->npwp,
                'pekerjaan'         => $request->pekerjaan,
                'status_pernikahan' => $request->status_pernikahan,
                'nama_p'            => $request->nama_p,
                'nik_p'             => $request->nik_p,
                'nama_saudara'      => $request->nama_saudara,
                'no_telp_saudara'   => $request->no_telp_saudara,
                'no_bpjs_kes'       => $request->no_bpjs_kes,
                'id_lokasi'         => $request->id_lokasi,
                'id_kavling'        => $request->id_kavling,
                'booking_fee'       => str_replace('.', '', $request->booking_fee),
                'hrg_jual'          => str_replace('.', '', $request->hrg_jual),
                'id_marketing'      => $request->id_marketing,
                'id_agent'          => $request->id_agent,
                'jenis_perumahan'   => $request->jenis_perumahan,
                'jenis_properti'    => $request->jenis_properti,
                'jenis_pembelian'   => $request->jenis_pembelian,
            ];

            $data->update($db);
            $this->logEdit('Pengajuan Hold', $data->id);

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

    public function show($id)
    {
        $data = PengajuanHold::findOrFail($id);

        return view('admin.pengajuan_hold.lampiran', compact('data'));
    }

    public function booking(Request $request)
    {
        Carbon::setLocale('id');

        $marketing = MarketingOffline::all();
        $agent     = MarketingAgent::all();
        $bank      = Bank::all();
        $progres   = ProgresListPenjualan::all();
        $lokasi    = LokasiKavling::all();

        $tgl = Carbon::now()->translatedFormat('j F Y');

        return view('frontend.booking.index', compact('marketing', 'agent', 'lokasi', 'progres', 'bank', 'tgl'));
    }

    public function bookingSukses()
    {
        return view('frontend.booking.sukses');
    }

    public function upload(Request $request, $id)
    {
        $data = PengajuanHold::findOrFail($id);

        $fotoKtpRule = empty($data->foto_ktp)
            ? 'required|mimes:jpg,jpeg,png,pdf'
            : 'nullable|mimes:jpg,jpeg,png,pdf';

        $rules = [
            'foto_ktp'     => $fotoKtpRule,
            'foto_npwp'    => 'nullable|mimes:jpg,jpeg,png,pdf',
            'foto_kk'      => 'nullable|mimes:jpg,jpeg,png,pdf',
            'foto_bpjs'    => 'nullable|mimes:jpg,jpeg,png,pdf',
            'foto_ktp_p'   => 'nullable|mimes:jpg,jpeg,png,pdf',
            'file_bukti'   => 'nullable|mimes:jpg,jpeg,png,pdf',
            'foto_pemohon' => 'nullable|mimes:jpg,jpeg,png,pdf',
        ];

        $messages = [
            'foto_ktp.required'  => 'Foto KTP wajib diunggah.',
            'foto_ktp.mimes'     => 'File harus berformat JPG, PNG, atau PDF.',
            'foto_npwp.mimes'    => 'File harus berformat JPG, PNG, atau PDF.',
            'foto_kk.mimes'      => 'File harus berformat JPG, PNG, atau PDF.',
            'foto_bpjs.mimes'    => 'File harus berformat JPG, PNG, atau PDF.',
            'foto_ktp_p.mimes'   => 'File harus berformat JPG, PNG, atau PDF.',
            'file_bukti.mimes'   => 'File harus berformat JPG, PNG, atau PDF.',
            'foto_pemohon.mimes' => 'File harus berformat JPG, PNG, atau PDF.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {

            $folder = public_path('assets/booking');

            if (! file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            $fields = [
                'foto_ktp',
                'foto_npwp',
                'foto_kk',
                'foto_bpjs',
                'foto_ktp_p',
                'file_bukti',
                'foto_pemohon',
            ];

            $db = [];

            foreach ($fields as $field) {
                if ($request->hasFile($field)) {

                    if (! empty($data->$field) && file_exists($folder . '/' . $data->$field)) {
                        unlink($folder . '/' . $data->$field);
                    }

                    $file = $request->file($field);

                    $filename = $this->compressImageNative($file, $folder);

                    $db[$field] = $filename;
                } else {
                    $db[$field] = $data->$field;
                }
            }

            $data->update($db);

            DB::commit();

            return response()->json([
                'status' => 'success',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memperbarui Booking.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteFile(Request $request, $id)
    {
        $field   = $request->input('field');
        $allowed = ['foto_pemohon', 'foto_ktp_p', 'file_bukti', 'foto_ktp', 'foto_npwp', 'foto_kk', 'foto_bpjs'];

        if (! in_array($field, $allowed)) {
            return response()->json(['success' => false]);
        }

        $data = PengajuanHold::findOrFail($id);

        if ($data->$field) {
            $filePath = public_path('assets/booking/' . $data->$field);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $data->$field = null;
            $data->save();
        }

        return response()->json(['success' => true]);
    }

    public function getKavling($id)
    {
        $kavling = KavlingPeta::where('id_lokasi', $id)
            ->where('status', 0)
            ->get(['id', 'kode_kavling']);

        return response()->json($kavling);
    }

    public function getHargaKavling($id)
    {
        $data = KavlingPeta::findOrFail($id);

        return response()->json([
            'hrg_jual' => $data->hrg_jual,
        ]);
    }

    public function getKavlingHold($id)
    {
        $kavling = KavlingPeta::where('id_lokasi', $id)
            ->get(['id', 'kode_kavling']);

        return response()->json($kavling);
    }

    public function getHargaKavlingHold($id)
    {
        $data      = KavlingPeta::findOrFail($id);
        $formatted = 'Rp. ' . number_format($data->hrg_jual, 0, ',', '.');

        return response()->json([
            'hrg_jual'  => $data->hrg_jual,
            'formatted' => $formatted,
        ]);
    }

    public function bookingStore(Request $request)
    {
        $request->merge([
            'booking_fee' => $request->booking_fee ? str_replace('.', '', $request->booking_fee) : 0,
            'hrg_jual'    => $request->hrg_jual ? str_replace('.', '', $request->hrg_jual) : 0,
        ]);

        $request->validate([
            'nama_lengkap'    => 'required',
            'nik'             => 'required|digits:16',
            'tempat_lahir'    => 'required',
            'tgl_lahir'       => 'required|date',
            'jenis_kelamin'   => 'required',
            'no_telp'         => 'required',
            'alamat_ktp'      => 'required',
            'alamat_domisili' => 'nullable',
            'email'           => 'nullable|email',
            'id_lokasi'       => 'required',
            'id_kavling'      => 'required',
            'hrg_jual'        => 'required',
            'jenis_properti'  => 'required',
            'id_marketing'    => 'required',
            'booking_fee'     => 'required|gt:0',
            'jenis_perumahan' => 'required',
            'jenis_pembelian' => 'required',
            'foto_ktp'        => 'required|file|mimes:jpg,jpeg,png,pdf',
            'foto_npwp'       => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'foto_kk'         => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'foto_bpjs'       => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'foto_ktp_p'      => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'file_bukti'      => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'foto_pemohon'    => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        ], [
            'nama_lengkap.required'     => 'Nama lengkap wajib diisi.',
            'nik.required'              => 'NIK wajib diisi.',
            'nik.digits'                => 'NIK harus 16 digit.',
            'tempat_lahir.required'     => 'Tempat lahir wajib diisi.',
            'tgl_lahir.required'        => 'Tanggal lahir wajib diisi.',
            'tgl_lahir.date'            => 'Tanggal lahir harus berupa tanggal yang valid.',
            'jenis_kelamin.required'    => 'Jenis kelamin wajib dipilih.',
            'no_telp.required'          => 'Nomor telepon wajib diisi.',
            'alamat_ktp.required'       => 'Alamat KTP wajib diisi.',
            'email.email'               => 'Format email tidak valid.',
            'id_lokasi.required'        => 'Lokasi wajib dipilih.',
            'id_kavling.required'       => 'Kavling wajib dipilih.',
            'hrg_jual.required'         => 'Harga jual wajib diisi.',
            'jenis_properti.required'   => 'Jenis Properti wajib diisi.',
            'biaya_surat.required'      => 'Biaya surat wajib diisi.',
            'peningkatan_mutu.required' => 'Peningkatan mutu wajib diisi.',
            'id_marketing.required'     => 'Marketing wajib dipilih.',
            'booking_fee.required'      => 'Booking fee wajib diisi.',
            'booking_fee.gt'            => 'Booking fee harus lebih dari 0.',
            'jenis_perumahan.required'  => 'Jenis Perumahan wajib dipilih.',
            'jenis_pembelian.required'  => 'Jenis Pembelian wajib dipilih.',
            'foto_ktp.required'         => 'Foto KTP wajib diunggah.',
            'foto_ktp.mimes'            => 'Foto KTP harus berformat JPG, JPEG, PNG, atau PDF.',
            'foto_npwp.mimes'           => 'Foto NPWP harus berformat JPG, JPEG, PNG, atau PDF.',
            'foto_kk.mimes'             => 'Foto KK harus berformat JPG, JPEG, PNG, atau PDF.',
            'foto_bpjs.mimes'           => 'Foto BPJS harus berformat JPG, JPEG, PNG, atau PDF.',
            'foto_ktp_p.mimes'          => 'Foto KTP pasangan harus berformat JPG, JPEG, PNG, atau PDF.',
            'foto_pemohon.mimes'        => 'Foto pemohon harus berformat JPG, JPEG, PNG, atau PDF.',
            'file_bukti.mimes'          => 'File bukti harus berformat JPG, JPEG, PNG, atau PDF.',
        ]);

        DB::beginTransaction();
        try {

            $folder = public_path('assets/booking');

            if (! file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            $files = [
                'foto_ktp',
                'foto_npwp',
                'foto_kk',
                'foto_bpjs',
                'file_bukti',
                'foto_pemohon',
                'foto_ktp_p',
            ];

            $fileNames = [];

            foreach ($files as $file) {

                if ($request->hasFile($file)) {

                    $uploadedFile = $request->file($file);

                    $filename = $this->compressImageNative($uploadedFile, $folder);

                    $fileNames[$file] = $filename;
                } else {

                    $fileNames[$file] = null;
                }
            }

            $no_registrasi = $this->generateNoRegistrasi();

            PengajuanHold::create([
                'no_registrasi'     => $no_registrasi,
                'tgl_booking'       => Carbon::now()->format('Y-m-d'),
                'nama_lengkap'      => $request->nama_lengkap,
                'nik'               => $request->nik ?? '',
                'no_telp'           => $request->no_telp ?? '',
                'email'             => $request->email ?? '',
                'alamat_ktp'        => $request->alamat_ktp ?? '',
                'jenis_kelamin'     => $request->jenis_kelamin ?? '',
                'tempat_lahir'      => $request->tempat_lahir ?? '',
                'tgl_lahir'         => $request->tgl_lahir ?? null,
                'npwp'              => $request->npwp ?? '',
                'pekerjaan'         => $request->pekerjaan ?? '',
                'status_pernikahan' => $request->status_pernikahan ?? '',
                'nama_p'            => $request->nama_p ?? '',
                'nik_p'             => $request->nik_p ?? '',
                'nama_saudara'      => $request->nama_saudara ?? '',
                'no_telp_saudara'   => $request->no_telp_saudara ?? '',
                'no_bpjs_kes'       => $request->no_bpjs_kes ?? '',
                'id_lokasi'         => $request->id_lokasi,
                'id_kavling'        => $request->id_kavling,
                'hrg_jual'          => $request->hrg_jual ?? 0,
                'jenis_properti'    => $request->jenis_properti,
                'booking_fee'       => $request->booking_fee ?? 0,
                'id_marketing'      => $request->id_marketing ?? 0,
                'id_agent'          => $request->id_agent ?? 0,
                'jenis_perumahan'   => $request->jenis_perumahan ?? '',
                'jenis_pembelian'   => $request->jenis_pembelian ?? '',
                'foto_ktp'          => $fileNames['foto_ktp'] ?? null,
                'foto_npwp'         => $fileNames['foto_npwp'] ?? null,
                'foto_kk'           => $fileNames['foto_kk'] ?? null,
                'foto_bpjs'         => $fileNames['foto_bpjs'] ?? null,
                'foto_ktp_p'        => $fileNames['foto_ktp_p'] ?? null,
                'file_bukti'        => $fileNames['file_bukti'] ?? null,
                'foto_pemohon'      => $fileNames['foto_pemohon'] ?? null,
                'stt_reg'           => 1,
            ]);

            KavlingPeta::find($request->id_kavling)->update(['status' => 1]);

            DB::commit();

            session([
                'nama'   => $request->nama_lengkap,
                'lokasi' => LokasiKavling::find($request->id_lokasi)->nama_kavling,
                'blok'   => KavlingPeta::find($request->id_kavling)->kode_kavling,
            ]);

            return response()->json([
                'status' => 'success',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memperbarui Booking.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    private function generateNoRegistrasi()
    {
        $latest = PengajuanHold::orderBy('no_registrasi', 'desc')->first();

        if (! $latest || ! preg_match('/^\d{3}$/', $latest->no_registrasi)) {
            return '001';
        }

        $lastNumber = intval($latest->no_registrasi);

        return str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }

    private function createCustomer($id, $request)
    {
        $data = PengajuanHold::findOrFail($id);

        $lokasi = LokasiKavling::findOrFail($data->id_lokasi);
        $prefix = $lokasi->nama_singkat;

        $latest = Customer::where('kode_customer', 'LIKE', $prefix . '-%')
            ->latest('kode_customer')
            ->first();

        if ($latest && preg_match('/' . $prefix . '-(\d+)/', $latest->kode_customer, $match)) {
            $number  = (int) $match[1] + 1;
            $newKode = $prefix . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        } else {
            $newKode = $prefix . '-0001';
        }

        $files = [
            'file_bukti'   => 'Bukti Transfer Booking',
            'foto_ktp'     => 'Foto KTP',
            'foto_kk'      => 'Foto KK',
            'foto_npwp'    => 'Foto NPWP',
            'foto_bpjs'    => 'Foto BPJS',
            'foto_ktp_p'   => 'Foto KTP Pasangan',
            'foto_pemohon' => 'Foto Pemohon',
        ];

        $customerFiles = [];
        foreach ($files as $field => $label) {
            $oldPath = public_path('assets/booking/' . $data->$field);

            if ($data->$field && File::exists($oldPath)) {
                $customerPath = public_path('assets/customer/' . $data->$field);

                File::copy($oldPath, $customerPath);

                if ($field === 'file_bukti') {
                    $keuanganPath = public_path('assets/keuangan/pemasukan/' . $data->$field);
                    File::copy($oldPath, $keuanganPath);
                }

                File::delete($oldPath);

                $customerFiles[$field] = $data->$field;
            } else {
                $customerFiles[$field] = null;
            }
        }

        $cust = [
            'kode_customer'          => $newKode,
            'tanggal_verif'          => Carbon::now('Asia/Jakarta'),
            'id_lokasi'              => $data->id_lokasi,
            'id_kavling'             => $data->id_kavling,
            'hrg_jual'               => $data->hrg_jual,

            'nama_lengkap'           => $data->nama_lengkap,
            'nik'                    => $data->nik,
            'jenis_kelamin'          => $data->jenis_kelamin,
            'tempat_lahir'           => $data->tempat_lahir,
            'tgl_lahir'              => $data->tgl_lahir,
            'alamat_ktp'             => $data->alamat_ktp,
            'alamat_domisili'        => $data->alamat_domisili,
            'status_pernikahan'      => $data->status_pernikahan,

            'nama_p'                 => $data->nama_p,
            'nik_p'                  => $data->nik_p,
            'nama_saudara'           => $data->nama_saudara,
            'no_telp_saudara'        => $data->no_telp_saudara,

            'jenis_properti'         => $data->jenis_properti,
            'jenis_perumahan'        => $data->jenis_perumahan,
            'jenis_pembelian'        => $data->jenis_pembelian,

            'id_agent'               => $data->id_agent,
            'id_marketing'           => $data->id_marketing,

            'no_telp'                => $data->no_telp,
            'email'                  => $data->email,
            'npwp'                   => $data->npwp,
            'no_bpjs_kes'            => $data->no_bpjs_kes,
            'pekerjaan'              => $data->pekerjaan,

            'id_status_progres'      => 2,

            'an_surat_cash'          => $request->an_surat_cash,
            'termin_x_cash_b'        => $request->termin_x_cash_b ?? 0,

            'pajak_bphtb'            => $data->pajak_bphtb,
            'stt_free_pajak_bphtb'   => $data->stt_free_pajak_bphtb,

            'biaya_notaris'          => $data->biaya_notaris,
            'stt_free_biaya_notaris' => $data->stt_free_biaya_notaris,

            'biaya_kpr'              => $data->biaya_kpr,
            'stt_free_biaya_kpr'     => $data->stt_free_biaya_kpr,

            'biaya_custom'           => $data->biaya_custom,
            'biaya_lain_lain'        => $data->biaya_lain_lain,

            'ppn'                    => $data->ppn,
            'pajak_pph'              => $data->pajak_pph,
            'bonus_konsumen'         => $data->bonus_konsumen,

            'diskon'                 => $data->diskon,

            'total_harga_rumah'      => $data->total_harga_rumah,
            'total_harga_komisi'     => $data->total_harga_komisi,
        ];

        $customer = Customer::create($cust);

        KavlingPeta::find($data->id_kavling)->update(['id_customer' => $customer->id]);

        PersyaratanLegal::create([
            'id_customer' => $customer->id,
        ]);

        foreach ($files as $field => $label) {
            if ($customerFiles[$field]) {
                UploudFile::create([
                    'tanggal'     => Carbon::now(),
                    'id_customer' => $customer->id,
                    'nama_file'   => $label,
                    'keterangan'  => '',
                    'lampiran'    => $customerFiles[$field],
                ]);
            }
        }

        return $customer;
    }

    public function verifikasi($id, Request $request)
    {
        Carbon::setLocale('id');

        $data            = PengajuanHold::findOrFail($id);
        $bankList        = Bank::all();
        $metodeBayarList = MetodeBayar::all();

        if (! empty($data->tgl_booking)) {
            $data->tgl_booking_formatted = Carbon::createFromFormat('Y-m-d', $data->tgl_booking)
                ->locale('id')
                ->translatedFormat('j F Y');
        } else {
            $data->tgl_booking_formatted = null;
        }

        if (! empty($data->tgl_lahir)) {
            $data->tgl_lahir_formatted = Carbon::createFromFormat('Y-m-d', $data->tgl_lahir)
                ->locale('id')
                ->translatedFormat('j F Y');
        } else {
            $data->tgl_lahir_formatted = null;
        }

        return view('admin.pengajuan_hold.verif', compact('data', 'bankList', 'metodeBayarList'));
    }

    public function simpanVerifikasi(Request $request, $id)
    {
        $data = PengajuanHold::with(['kavling', 'lokasi'])->findOrFail($id);

        $request->merge([
            'diskon'             => $request->diskon ? str_replace('.', '', $request->diskon) : null,
            'pajak_bphtb'        => $request->pajak_bphtb ? str_replace('.', '', $request->pajak_bphtb) : null,
            'biaya_notaris'      => $request->biaya_notaris ? str_replace('.', '', $request->biaya_notaris) : null,
            'biaya_kpr'          => $request->biaya_kpr ? str_replace('.', '', $request->biaya_kpr) : null,
            'biaya_custom'       => $request->biaya_custom ? str_replace('.', '', $request->biaya_custom) : null,
            'biaya_lain_lain'    => $request->biaya_lain_lain ? str_replace('.', '', $request->biaya_lain_lain) : null,
            'ppn'                => $request->ppn ? str_replace('.', '', $request->ppn) : null,
            'pajak_pph'          => $request->pajak_pph ? str_replace('.', '', $request->pajak_pph) : null,
            'bonus_konsumen'     => $request->bonus_konsumen ? str_replace('.', '', $request->bonus_konsumen) : null,
            'total_harga_rumah'  => $request->total_harga_rumah ? str_replace('.', '', $request->total_harga_rumah) : null,
            'total_harga_komisi' => $request->total_harga_komisi ? str_replace('.', '', $request->total_harga_komisi) : null,
        ]);

        $rules = [
            'tgl_booking_fee'        => 'required',
            'stt_reg'                => 'required',
            'id_metode_bayar'        => 'required',
            'id_bank'                => 'required',
            'jenis_pembelian'        => 'required',

            'diskon'                 => 'nullable',
            'pajak_bphtb'            => 'nullable',
            'biaya_notaris'          => 'nullable',
            'biaya_kpr'              => 'nullable',
            'biaya_custom'           => 'nullable',
            'biaya_lain_lain'        => 'nullable',
            'ppn'                    => 'nullable',
            'pajak_pph'              => 'nullable',
            'bonus_konsumen'         => 'nullable',
            'total_harga_rumah'      => 'nullable',
            'total_harga_komisi'     => 'nullable',

            'stt_free_pajak_bphtb'   => 'required_with:pajak_bphtb|in:1,2',
            'stt_free_biaya_notaris' => 'required_with:biaya_notaris|in:1,2',
            'stt_free_biaya_kpr'     => 'required_with:biaya_kpr|in:1,2',

            'an_surat_cash'          => 'required_if:jenis_pembelian,Pembelian Cash',
            'termin_x_cash_b'        => 'required_if:jenis_pembelian,Cash Bertahap',
        ];

        $messages = [
            'tgl_booking_fee.required'             => 'Tanggal Booking Fee wajib diisi!',
            'stt_reg.required'                     => 'Status Verifikasi wajib dipilih!',
            'jenis_pembelian.required'             => 'Jenis Pembelian wajib dipilih!',
            'id_metode_bayar.required'             => 'Metode Pembayaran wajib dipilih!',
            'id_bank.required'                     => 'Bank wajib dipilih!',
            'an_surat_cash.required_if'            => 'Atas Nama Surat wajib diisi!',
            'termin_x_cash_b.required_if'          => 'Termin wajib diisi!',
            'stt_free_pajak_bphtb.required_with'   => 'Status BPHTB wajib dipilih jika pajak diisi!',
            'stt_free_biaya_notaris.required_with' => 'Status Notaris wajib dipilih jika biaya diisi!',
            'stt_free_biaya_kpr.required_with'     => 'Status KPR wajib dipilih jika biaya diisi!',
            'stt_free_pajak_bphtb.in'              => 'Status BPHTB tidak valid!',
            'stt_free_biaya_notaris.in'            => 'Status Notaris tidak valid!',
            'stt_free_biaya_kpr.in'                => 'Status KPR tidak valid!',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {

            $hasil = $this->hitungTotalHarga([
                'hrg_jual'               => $data->hrg_jual,
                'diskon'                 => $request->diskon,
                'pajak_bphtb'            => $request->pajak_bphtb,
                'biaya_notaris'          => $request->biaya_notaris,
                'biaya_kpr'              => $request->biaya_kpr,
                'biaya_custom'           => $request->biaya_custom,
                'biaya_lain_lain'        => $request->biaya_lain_lain,
                'ppn'                    => $request->ppn,
                'pajak_pph'              => $request->pajak_pph,
                'bonus_konsumen'         => $request->bonus_konsumen,
                'stt_free_pajak_bphtb'   => $request->stt_free_pajak_bphtb,
                'stt_free_biaya_notaris' => $request->stt_free_biaya_notaris,
                'stt_free_biaya_kpr'     => $request->stt_free_biaya_kpr,
            ]);

            $common = [
                'tgl_booking_fee'        => $request->tgl_booking_fee,
                'stt_reg'                => $request->stt_reg,
                'id_metode_bayar'        => $request->id_metode_bayar,
                'id_bank'                => $request->id_bank,
                'jenis_pembelian'        => $request->jenis_pembelian,

                'diskon'                 => $request->diskon,
                'pajak_bphtb'            => $request->pajak_bphtb,
                'stt_free_pajak_bphtb'   => $request->stt_free_pajak_bphtb,

                'biaya_notaris'          => $request->biaya_notaris,
                'stt_free_biaya_notaris' => $request->stt_free_biaya_notaris,

                'biaya_kpr'              => $request->biaya_kpr,
                'stt_free_biaya_kpr'     => $request->stt_free_biaya_kpr,

                'biaya_custom'           => $request->biaya_custom,
                'biaya_lain_lain'        => $request->biaya_lain_lain,

                'ppn'                    => $request->ppn,
                'pajak_pph'              => $request->pajak_pph,
                'bonus_konsumen'         => $request->bonus_konsumen,

                'total_harga_rumah'      => $hasil['total_harga_rumah'],
                'total_harga_komisi'     => $hasil['total_harga_komisi'],
            ];

            switch ($request->jenis_pembelian) {
                case 'Pembelian Cash':
                    $specific = [
                        'an_surat_cash'   => $request->an_surat_cash,
                        'termin_x_cash_b' => null,
                    ];
                    break;

                case 'Cash Bertahap':
                    $specific = [
                        'termin_x_cash_b' => $request->termin_x_cash_b,
                        'an_surat_cash'   => null,
                    ];
                    break;

                default:
                    $specific = [
                        'an_surat_cash'   => null,
                        'termin_x_cash_b' => null,
                    ];
                    break;
            }

            $data->update(array_merge($common, $specific));

            if ($request->stt_reg == 2) {

                $customer = $this->createCustomer($data->id, $request);
                $tglNow   = Carbon::now('Asia/Jakarta')->toDateString();

                Piutang::create([
                    'id_customer'     => $customer->id,
                    'id_bank'         => $request->id_bank,
                    'tanggal_piutang' => $tglNow,
                    'deskripsi'       => 'Total Penjualan Rumah tipe ' . $data->kavling->tipe_bangunan . ' ' . $data->lokasi->nama_kavling . ' Blok ' . $data->kavling->kode_kavling,
                    'nominal'         => $hasil['total_harga_rumah'],
                    'lampiran'        => '',
                    'status'          => 1,
                    'terbayar'        => $data->booking_fee,
                    'sisa_bayar'      => $hasil['total_harga_rumah'] - $data->booking_fee,
                    'tgl_pelunasan'   => null,
                ]);

                $no_kwitansi = $this->generator->generateNomorDokumen(
                    $data->lokasi,
                    'no_kwitansi',
                    Pemasukan::class
                );

                Pemasukan::create([
                    'id_bank'               => $request->id_bank,
                    'id_metode_bayar'       => $request->id_metode_bayar,
                    'id_customer'           => $customer->id,
                    'tanggal'               => $data->tgl_booking_fee,
                    'no_kwitansi'           => $no_kwitansi,
                    'nominal'               => $data->booking_fee,
                    'lampiran'              => $data->file_bukti ?? '',
                    'id_kategori_transaksi' => 1,
                    'keterangan'            => 'Booking Fee Rumah tipe ' . $data->kavling->tipe_bangunan . ' ' . $data->lokasi->nama_kavling . ' Blok ' . $data->kavling->kode_kavling,
                ]);

                KavlingPeta::where('id', $data->id_kavling)->update(['status' => 2]);
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
            $data = PengajuanHold::findOrFail($id);

            $files = [
                $data->foto_ktp,
                $data->foto_npwp,
                $data->foto_kk,
                $data->foto_bpjs,
                $data->foto_pemohon,
                $data->foto_ktp_p,
                $data->file_bukti,
            ];

            foreach ($files as $file) {
                if (! empty($file) && file_exists(public_path('assets/booking/' . $file))) {
                    unlink(public_path('assets/booking/' . $file));
                }
            }

            KavlingPeta::find($data->id_kavling)->update(['status' => 0]);

            $this->logDelete('Pengajuan Hold', $id);
            $data->delete();

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

    private function compressImageNative($file, $folder)
    {
        $ext = strtolower($file->getClientOriginalExtension());

        if ($ext === 'pdf') {
            $filename = Str::random(25) . '.pdf';
            $file->move($folder, $filename);
            return $filename;
        }

        $filename    = Str::random(25) . '.jpg';
        $destination = $folder . '/' . $filename;

        $path = $file->getPathname();

        if ($ext === 'jpg' || $ext === 'jpeg') {
            $image = imagecreatefromjpeg($path);
        } elseif ($ext === 'png') {
            $image = imagecreatefrompng($path);
        } else {
            $filename = Str::random(25) . '.' . $ext;
            $file->move($folder, $filename);
            return $filename;
        }

        imagejpeg($image, $destination, 75);

        imagedestroy($image);

        return $filename;
    }

    public function hitungTotalHarga($data)
    {
        $hrg_jual = (int) ($data['hrg_jual'] ?? 0);

        $diskon        = (int) ($data['diskon'] ?? 0);
        $pajak_bphtb   = (int) ($data['pajak_bphtb'] ?? 0);
        $biaya_notaris = (int) ($data['biaya_notaris'] ?? 0);
        $biaya_kpr     = (int) ($data['biaya_kpr'] ?? 0);
        $biaya_custom  = (int) ($data['biaya_custom'] ?? 0);
        $biaya_lain    = (int) ($data['biaya_lain_lain'] ?? 0);
        $ppn           = (int) ($data['ppn'] ?? 0);
        $pajak_pph     = (int) ($data['pajak_pph'] ?? 0);
        $bonus         = (int) ($data['bonus_konsumen'] ?? 0);

        $stt_bphtb   = $data['stt_free_pajak_bphtb'] ?? null;
        $stt_notaris = $data['stt_free_biaya_notaris'] ?? null;
        $stt_kpr     = $data['stt_free_biaya_kpr'] ?? null;

        $total_rumah = $hrg_jual;

        if ($stt_bphtb != 1) {
            $total_rumah += $pajak_bphtb;
        }

        if ($stt_notaris != 1) {
            $total_rumah += $biaya_notaris;
        }

        if ($stt_kpr != 1) {
            $total_rumah += $biaya_kpr;
        }

        $total_rumah += $biaya_custom + $biaya_lain + $ppn;
        $total_rumah -= $diskon;

        $total_komisi = $hrg_jual;

        $total_komisi -= $pajak_pph;
        if ($stt_bphtb == 1) {
            $total_komisi -= $pajak_bphtb;
        }

        if ($stt_notaris == 1) {
            $total_komisi -= $biaya_notaris;
        }

        if ($stt_kpr == 1) {
            $total_komisi -= $biaya_kpr;
        }

        $total_komisi -= ($bonus + $diskon + $ppn + $biaya_lain);

        return [
            'total_harga_rumah'  => $total_rumah,
            'total_harga_komisi' => $total_komisi,
        ];
    }
}
