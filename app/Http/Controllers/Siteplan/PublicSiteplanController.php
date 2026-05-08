<?php

namespace App\Http\Controllers\Siteplan;

use App\Http\Controllers\Controller;
use App\Models\KavlingPeta;
use App\Models\LokasiKavling;
use App\Models\ProgresListPenjualan;
use Illuminate\Http\Request;

class PublicSiteplanController extends Controller
{
    /**
     * Display the public siteplan.
     */
    public function index()
    {
        $lokasiKavling = LokasiKavling::with(['kavlingPeta.customer.progres', 'kavlingPeta.progres'])
            ->orderBy('urutan', 'asc')
            ->get();

        $legend = ProgresListPenjualan::whereNotNull('warna')
            ->where('warna', '!=', '')
            ->where('stt_tampil', 1)
            ->orderBy('urutan', 'asc')
            ->get();

        return view('frontend.public_siteplan.index', compact('lokasiKavling', 'legend'));
    }

    /**
     * Fetch kavling details for the public popup.
     */
    public function show($id)
    {
        $data = KavlingPeta::with(['lokasi'])->findOrFail($id);

        $prices = [
            'cash' => $data->hrg_jual ?? 0,
            'kpr' => $data->harga_kpr ?? 0,
            'inhouse' => 0, // Inhouse column not found in schema, setting to 0 or could be mapped if known
        ];

        // Build dimension string
        $dimension = null;
        if ($data->lebar_depan && $data->panjang_kanan) {
            $dimension = number_format($data->lebar_depan, 0, ',', '.') . 'm x ' .
                number_format($data->panjang_kanan, 0, ',', '.') . 'm';
        }

        return response()->json([
            'success'   => true,
            'data'      => $data,
            'cash_price' => $prices['cash'],
            'kpr_price' => $prices['kpr'],
            'inhouse_price' => $prices['inhouse'],
            'dimension' => $dimension,
            'status_label' => $data->status_label,
            'status_color' => $data->siteplan_color,
        ]);
    }
}
