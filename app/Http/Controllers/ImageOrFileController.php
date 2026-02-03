<?php

namespace App\Http\Controllers;

use App\Models\Aduan;
use App\Models\FileAduan;
use App\Models\FileProsesAduan;
use App\Models\FotoProyekBangunan;
use App\Models\FotoProyekJalan;
use App\Models\FotoProyekSaluran;
use App\Models\ProyekBangunanDetail;
use App\Models\ProyekJalanDetail;
use App\Models\ProyekSaluranDetail;
use Illuminate\Support\Facades\File;

class ImageOrFileController extends Controller
{
    public function getFotoProyekBangunan($id)
    {
        $proyek = ProyekBangunanDetail::find($id);
        if (!$proyek) {
            abort(404, 'Proyek tidak ditemukan.');
        }

        $fotos = FotoProyekBangunan::where('id_proyek_bangunan_detail', $proyek->id)->get();

        if ($fotos->isEmpty()) {
            abort(404, 'Tidak ada foto untuk proyek ini.');
        }

        $fotoData = [];

        foreach ($fotos as $foto) {
            $path = public_path('assets/proyek_bangunan/' . $foto->foto);
            if (!File::exists($path)) {
                continue;
            }

            $fotoData[] = [
                'id' => $foto->id,
                'url' => asset('assets/proyek_bangunan/' . $foto->foto),
            ];
        }

        if (empty($fotoData)) {
            return response()->json([
                'status' => 'success',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $fotoData
        ]);
    }
    public function getFotoProyekJalan($id)
    {
        $proyek = ProyekJalanDetail::find($id);
        if (!$proyek) {
            abort(404, 'Proyek tidak ditemukan.');
        }

        $fotos = FotoProyekJalan::where('id_proyek_jalan_detail', $proyek->id)->get();

        if ($fotos->isEmpty()) {
            abort(404, 'Tidak ada foto untuk proyek ini.');
        }

        $fotoData = [];

        foreach ($fotos as $foto) {
            $path = public_path('assets/proyek_jalan/' . $foto->foto);
            if (!File::exists($path)) {
                continue;
            }

            $fotoData[] = [
                'id' => $foto->id,
                'url' => asset('assets/proyek_jalan/' . $foto->foto),
            ];
        }

        if (empty($fotoData)) {
            return response()->json([
                'status' => 'success',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $fotoData
        ]);
    }
    public function getFotoProyekSaluran($id)
    {
        $proyek = ProyekSaluranDetail::find($id);
        if (!$proyek) {
            abort(404, 'Proyek tidak ditemukan.');
        }

        $fotos = FotoProyekSaluran::where('id_proyek_saluran_detail', $proyek->id)->get();

        if ($fotos->isEmpty()) {
            abort(404, 'Tidak ada foto untuk proyek ini.');
        }

        $fotoData = [];

        foreach ($fotos as $foto) {
            $path = public_path('assets/proyek_saluran/' . $foto->foto);
            if (!File::exists($path)) {
                continue;
            }

            $fotoData[] = [
                'id' => $foto->id,
                'url' => asset('assets/proyek_saluran/' . $foto->foto),
            ];
        }

        if (empty($fotoData)) {
            return response()->json([
                'status' => 'success',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $fotoData
        ]);
    }
    public function getFotoAduan($id)
    {
        $aduan = Aduan::findOrFail($id);
        if (!$aduan) {
            abort(404, 'Aduan tidak ditemukan.');
        }

        $fotos = FileAduan::where('id_aduan', $aduan->id)->get();

        if ($fotos->isEmpty()) {
            abort(404, 'Tidak ada foto untuk aduan ini.');
        }

        $fotoData = [];

        foreach ($fotos as $foto) {
            $path = public_path('assets/aduan/' . $foto->nama_file);
            if (!File::exists($path)) {
                continue;
            }

            $fotoData[] = [
                'id' => $foto->id,
                'url' => asset('assets/aduan/' . $foto->nama_file),
            ];
        }

        if (empty($fotoData)) {
            return response()->json([
                'status' => 'success',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $fotoData
        ]);
    }

    public function getFotoProsesAduan($id)
    {
        $aduan = Aduan::findOrFail($id);
        if (!$aduan) {
            abort(404, 'Aduan tidak ditemukan.');
        }

        $fotos = FileProsesAduan::where('id_aduan', $aduan->id)->get();

        if ($fotos->isEmpty()) {
            abort(404, 'Tidak ada foto untuk aduan ini.');
        }

        $fotoData = [];

        foreach ($fotos as $foto) {
            $path = public_path('assets/proses_aduan/' . $foto->nama_file);
            if (!File::exists($path)) {
                continue;
            }

            $fotoData[] = [
                'id' => $foto->id,
                'url' => asset('assets/proses_aduan/' . $foto->nama_file),
            ];
        }

        if (empty($fotoData)) {
            return response()->json([
                'status' => 'success',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $fotoData
        ]);
    }
    public function destroyFotoProyekBangunan($id)
    {
        $foto = FotoProyekBangunan::find($id);

        if (!$foto) {
            return response()->json([
                'status' => 'error',
                'message' => 'Foto tidak ditemukan.'
            ], 404);
        }

        $path = public_path('assets/proyek_bangunan/' . $foto->foto);
        if (File::exists($path)) {
            File::delete($path);
        }

        $foto->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Foto berhasil dihapus.'
        ]);
    }
    public function destroyFotoProyekJalan($id)
    {
        $foto = FotoProyekJalan::find($id);

        if (!$foto) {
            return response()->json([
                'status' => 'error',
                'message' => 'Foto tidak ditemukan.'
            ], 404);
        }

        $path = public_path('assets/proyek_jalan/' . $foto->foto);
        if (File::exists($path)) {
            File::delete($path);
        }

        $foto->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Foto berhasil dihapus.'
        ]);
    }
    public function destroyFotoProyekSaluran($id)
    {
        $foto = FotoProyekSaluran::find($id);

        if (!$foto) {
            return response()->json([
                'status' => 'error',
                'message' => 'Foto tidak ditemukan.'
            ], 404);
        }

        $path = public_path('assets/proyek_saluran/' . $foto->foto);
        if (File::exists($path)) {
            File::delete($path);
        }

        $foto->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Foto berhasil dihapus.'
        ]);
    }
}
