<?php

namespace App\Http\Controllers\Customer;

use App\Models\Customer;
use App\Models\Aduan;
use App\Models\FileAduan;
use App\Models\SerahKunci;
use App\Models\AduanProses;
use App\Models\KavlingPeta;
use App\Traits\LogAktivitasTrait;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\LokasiKavling;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Controllers\Pengaturan\HakAksesController;

class SerahTerimaKunciController extends Controller
{
    use LogAktivitasTrait;
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = SerahKunci::orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tgl_serah_terima', function ($row) {
                    return Carbon::parse($row->tgl_serah_terima)->translatedFormat('d F Y');
                })
                ->addColumn('customer', function ($row) {
                    return $row->customer ? $row->customer->nama_lengkap : '-';
                })
                ->addColumn('tgl_expired', function ($row) {
                    return Carbon::parse($row->tgl_expired)->translatedFormat('d F Y');
                })
                ->addColumn('lokasi_kavling', function ($row) {
                    $customer = Customer::find($row->id_customer);
                    $namaLokasi = $customer && $customer->id_lokasi ? optional(LokasiKavling::find($customer->id_lokasi))->nama_kavling : '-';
                    $kodeKavling = $customer && $customer->id_kavling ? optional(KavlingPeta::find($customer->id_kavling))->kode_kavling : '-';

                    return '<div><b>' . e($namaLokasi) . '</b><br>' . e($kodeKavling) . '</div>';
                })
                ->addColumn('status', function ($row) {
                    $statusMap = [
                        0 => ['label' => 'Aduan Terkirim', 'class' => 'secondary'],
                        1 => ['label' => 'Proses Admin', 'class' => 'info'],
                        2 => ['label' => 'Proses Pengerjaan', 'class' => 'warning'],
                        3 => ['label' => 'Proses Selesai', 'class' => 'success'],
                    ];

                    $status = $statusMap[$row->status ?? 0];
                    return '<span class="badge bg-' . $status['class'] . '">' . $status['label'] . '</span>';
                })
                ->addColumn('action', function ($row) use ($permissions) {
                    $deleteUrl = route('serah-terima-kunci.destroy', $row->id);

                    $btn = '<div class="d-block justify-content-center">';
                    $btn .= '<button type="button" class="btn btn-success btn-xs btn-qr-code"
                            data-id_customer="' . $row->id . '">
                            QR Code
                        </button>';

                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="delete-button btn btn-danger btn-xs">
                                    Hapus
                                </button>
                            </form>';
                    }

                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action', 'lokasi_kavling', 'status'])
                ->make(true);
        }

        $customers = Customer::where('stt_arsip', 0)->get();

        return view('admin.customer.serah_kunci.index', compact('permissions', 'customers'));
    }

    public function generateQRCode(Request $request)
    {
        $url = $request->input('url');

        $qr = QrCode::size(470)->errorCorrection('H')->generate($url);

        return response()->json([
            'qr' => $qr->__toString()
        ]);
    }

    public function getNasabahDetails($id)
    {
        $customer = Customer::find($id);

        $lokasi = LokasiKavling::find($customer->id_lokasi);
        $kavling = KavlingPeta::find($customer->id_kavling);

        return response()->json([
            'alamat' => $customer->alamat_domisili,
            'nama_lokasi' => $lokasi ? $lokasi->nama_kavling : null,
            'kode_kavling' => $kavling ? $kavling->kode_kavling : null,
        ]);
    }

    public function tracking(Request $request)
    {
        $no_aduan = $request->no_aduan;
        $aduan = null;
        $allProses = [];
        $latestStatus = null;

        if ($no_aduan) {
            $aduan = Aduan::where('no_aduan', $no_aduan)->first();
            if ($aduan) {
                $allProses = AduanProses::where('id_aduan', $aduan->id)
                    ->orderBy('tgl_update', 'asc')
                    ->get();

                $latestStatus = $allProses->last();
            }
        }

        $statusList = [
            0 => 'Aduan Terkirim',
            1 => 'Proses Admin',
            2 => 'Proses Pengerjaan',
            3 => 'Proses Selesai',
        ];

        return view('frontend.form_aduan.tracking', compact('aduan', 'allProses', 'statusList', 'latestStatus'));
    }

    public function inputForm($id = null)
    {
        if (!$id) {
            return view('frontend.form_aduan.form_input');
        }

        $data = SerahKunci::with(['kavling.lokasi', 'customer'])->findOrFail($id);

        if (Carbon::parse($data->tgl_expired, 'Asia/Jakarta')->isPast()) {
            return redirect()->route('form-aduan.expired');
        }

        $customer = Customer::findOrFail($data->id_customer);
        $kavling = KavlingPeta::findOrFail($customer->id_kavling);

        return view('frontend.form_aduan.form_input', compact('customer', 'kavling', 'data'));
    }

    public function fetchCustomer(Request $request)
    {
        $request->validate([
            'no_kontrak' => 'required|string',
            'no_telepon' => 'required|string',
        ]);

        try {
            $noKontrak = preg_replace('/\s+/', '', $request->no_kontrak);
            $noTelp = preg_replace('/\s+/', '', $request->no_telepon);

            $customer = Customer::where('kode_customer', $noKontrak)
                ->whereRaw("REPLACE(no_telp, ' ', '') = ?", [$noTelp])
                ->first();

            if (!$customer) {
                return response()->json([
                    'message' => 'Data customer tidak ditemukan dengan nomor kontrak dan telepon yang diberikan!'
                ], 404);
            }

            $serahKunci = SerahKunci::where('id_customer', $customer->id)->first();

            if ($serahKunci && Carbon::parse($serahKunci->tgl_expired, 'Asia/Jakarta')->isPast()) {
                return response()->json([
                    'expired' => true,
                    'message' => 'Link form aduan sudah expired!'
                ], 410);
            }

            $kavling = KavlingPeta::find($customer->id_kavling);

            return response()->json([
                'success' => true,
                'id_customer' => $customer->id,
                'id_kavling' => $kavling ? $kavling->id : null,
                'nama_lengkap' => $customer->nama_lengkap,
                'lokasi' => $kavling && $kavling->lokasi ? $kavling->lokasi->nama_kavling : '',
                'blok_unit' => $kavling ? $kavling->kode_kavling : '',
                'tgl_serah_terima' => $serahKunci ? $serahKunci->tgl_serah_terima : null,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat memuat data customer!'
            ], 500);
        }
    }

    public function submitInput(Request $request)
    {
        $kavling = KavlingPeta::with('lokasi')->find($request->id_kavling);

        $request->validate([
            'no_kontrak' => 'required',
            'no_telepon' => 'required',
            'isi_aduan' => 'required',
            'id_customer' => 'required|integer',
            'id_kavling' => 'required|integer',
            'nama_lengkap' => 'required|string',
        ]);

        $lokasi = $kavling?->lokasi?->nama_kavling ?? '-';
        $blok = $kavling?->kode_kavling ?? '-';

        $last = Aduan::orderBy('id', 'desc')->first();
        $lastNumber = $last ? intval(substr($last->no_aduan, -4)) : 0;
        $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        $no_aduan = 'ADN-' . $nextNumber;

        $aduan = Aduan::create([
            'id_customer' => $request->id_customer,
            'id_kavling' => $request->id_kavling,
            'no_kontrak' => $request->no_kontrak,
            'isi_aduan' => $request->isi_aduan,
            'tanggal' => now(),
            'no_aduan' => $no_aduan,
            'stt_aduan' => 0,
        ]);

        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

                $file->move(public_path('assets/aduan'), $filename);

                $customer = Customer::where('nama_lengkap', $request->nama_lengkap)->first();

                FileAduan::create([
                    'id_aduan' => $aduan->id,
                    'id_customer' => $customer?->id ?? $request->id_customer,
                    'no_kontrak' => $request->no_kontrak,
                    'nama_file' => $filename,
                ]);
            }
        }

        AduanProses::create([
            'id_aduan' => $aduan->id,
            'tgl_update' => Carbon::now('Asia/Jakarta'),
            'catatan' => "Melapor",
            'stt_proses_aduan' => 0,
        ]);

        return response()->json([
            'status' => 'success',
            'no_aduan' => $no_aduan,
            'nama' => $request->nama_lengkap,
            'lokasi' => $lokasi,
            'blok' => $blok,
        ]);
    }

    public function showSukses(Request $request)
    {
        return view('frontend.form_aduan.sukses', [
            'no_aduan' => $request->no_aduan,
            'nama' => $request->nama,
            'lokasi' => $request->lokasi,
            'blok' => $request->blok,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_serah_terima' => 'required|date',
            'id_customer' => 'required',
            'keterangan' => 'required',
        ], [
            'tgl_serah_terima.required' => 'Tanggal wajib diisi.',
            'id_customer.required' => 'Customer wajib diisi.',
            'keterangan.required' => 'Keterangan wajib diisi.',
        ]);

        $sk = SerahKunci::create([
            'id_customer' => $request->id_customer,
            'tgl_serah_terima' => $request->tgl_serah_terima,
            'keterangan' => $request->keterangan,
            'status' => 0,
            'tgl_expired' => Carbon::parse($request->tgl_serah_terima)->addDays(90),
        ]);

        $this->logCreate('Serah Terima Kunci', $sk->id);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function destroy($id_bstk)
    {
        $sk = SerahKunci::findOrFail($id_bstk);
        $this->logDelete('Serah Terima', $id_bstk);
        $sk->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}
