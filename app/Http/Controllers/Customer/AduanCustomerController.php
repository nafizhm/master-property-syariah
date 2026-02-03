<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\FileAduan;
use App\Models\FileProsesAduan;
use App\Models\SerahKunci;
use App\Traits\LogAktivitasTrait;
use Illuminate\Http\Request;
use App\Models\AduanProses;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Models\Aduan;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AduanCustomerController extends Controller
{
    use LogAktivitasTrait;
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = Aduan::with(['nasabah', 'kavling'])->select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('id_customer', function ($row) {
                    $nama = $row->nasabah->nama_lengkap ?? '-';
                    $kavling = $row->kavling->kode_kavling ?? '-';
                    return $nama . '<br><strong>' . $kavling . '</strong>';
                })

                ->editColumn('stt_aduan', function ($row) {
                    switch ($row->stt_aduan) {
                        case 0:
                            return '<span class="badge bg-warning">Aduan Terkirim</span>';
                        case 1:
                            return '<span class="badge bg-primary">Proses Admin</span>';
                        case 2:
                            return '<span class="badge bg-success">Proses Pengerjaan</span>';
                        case 3:
                            return '<span class="badge bg-secondary">Proses Selesai</span>';
                        default:
                            return '<span class="badge bg-light text-dark">Tidak Diketahui</span>';
                    }
                })
                ->addColumn('action', function ($row) use ($permissions) {
                    $showUrl = route('aduan-customer.show', $row->id);
                    $deleteUrl = route('aduan-customer.destroy', $row->id);

                    $btn = '<div class="d-block justify-content-center">';
                    if ($permissions['edit']) {
                        $btn .= '<a href="' . e($showUrl) . '" class="btn btn-primary btn-xs">
                                Detail
                            </a>';
                    }
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

                ->rawColumns(['stt_aduan', 'action', 'id_customer'])
                ->make(true);
        }

        return view('admin.customer.aduan_customer.index', compact('permissions'));
    }

    public function show($id)
    {
        $aduan = Aduan::with(['nasabah', 'kavling'])->findOrFail($id);
        $proses = AduanProses::where('id_aduan', $id)->get();

        return view('admin.customer.aduan_customer.detail', compact('aduan', 'proses'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'catatan' => 'required|string',
                'stt_aduan' => 'required|integer',
                'fotoProses' => 'required_if:stt_aduan,3',
                'fotoProses.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ],
            [
                'catatan.required' => 'Catatan wajib diisi.',
                'catatan.string' => 'Catatan harus berupa teks.',
                'stt_aduan.required' => 'Status wajib diisi.',
                'stt_aduan.integer' => 'Status harus berupa angka.',
                'fotoProses.required_if' => 'Foto proses wajib diunggah jika status selesai.',
                'fotoProses.*.image' => 'File harus berupa gambar.',
                'fotoProses.*.mimes' => 'Format file harus jpeg, png, jpg, atau gif.',
                'fotoProses.*.max' => 'Ukuran file maksimal 2MB.',
            ]
        );

        DB::beginTransaction();
        try {
            $aduan = Aduan::findOrFail($id);
            $aduan->stt_aduan = $request->stt_aduan;
            $aduan->save();

            $ad = AduanProses::create([
                'id_aduan' => $aduan->id,
                'tgl_update' => Carbon::now('Asia/Jakarta'),
                'catatan' => $request->catatan,
                'stt_proses_aduan' => $request->stt_aduan,
            ]);

            $this->logCreate('Detail Aduan Customer', $ad->id);

            $serah = SerahKunci::where('id_customer', $aduan->id_customer)
                ->orderBy('id', 'desc')
                ->first();
            $serah->status = $request->stt_aduan;
            $serah->save();

            if ($request->stt_aduan == 3 && $request->hasFile('fotoProses')) {
                foreach ($request->file('fotoProses') as $file) {
                    $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

                    $file->move(public_path('assets/proses_aduan/'), $filename);

                    FileProsesAduan::create([
                        'id_aduan' => $aduan->id,
                        'nama_file' => $filename,
                    ]);
                }
            }

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $data = Aduan::findOrFail($id);

        $lampiran = FileAduan::where('id_aduan', $data->id)->get();
        foreach ($lampiran as $l) {
            if (!empty($l->nama_file) && file_exists(public_path('assets/aduan/' . $l->nama_file))) {
                unlink(public_path('assets/aduan/' . $l->nama_file));
            }
            $l->delete();
        }

        $prosesLampiran = FileProsesAduan::where('id_aduan', $data->id)->get();
        foreach ($prosesLampiran as $l) {
            if (!empty($l->nama_file) && file_exists(public_path('assets/proses_aduan/' . $l->nama_file))) {
                unlink(public_path('assets/proses_aduan/' . $l->nama_file));
            }
            $l->delete();
        }

        AduanProses::where('id_aduan', $data->id)->delete();
        $this->logDelete('Aduan Customer', $data->id);
        $data->delete();

        return response()->json(['status' => 'success']);
    }
}
