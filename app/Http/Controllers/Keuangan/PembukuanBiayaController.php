<?php
namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Traits\LogAktivitasTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PembukuanBiayaController extends Controller
{
    use LogAktivitasTrait;
    public function index(Request $request)
{
    if ($request->ajax()) {

        $data = Customer::with([
            'wawancara.wawancaraSp3k',
            'pemasukans',
        ])
            ->where('stt_arsip', 0)
            ->orderBy('tanggal_verif', 'desc');

        return DataTables::of($data)
            ->addIndexColumn()

            ->addColumn('customer', function ($row) {
                return '
                <div>
                    <div><strong>' . e($row->nama_lengkap) . '</strong></div>
                    <div class="text-muted small">' . e($row->kode_customer) . '</div>
                    <span class="badge bg-primary">' . e($row->jenis_pembelian) . '</span>
                </div>
            ';
            })

            ->addColumn('booking_fee', function ($row) {
                $val = optional($row->pemasukans->where('id_kategori_transaksi', 1)->first())->nominal;

                return $val ? '
                <div class="d-flex justify-content-between w-100">
                    <span>' . number_format($val, 0, ',', '.') . '</span>
                </div>
            ' : '';
            })

            ->addColumn('dp', function ($row) {
                $val = optional($row->pemasukans->where('id_kategori_transaksi', 2)->first())->nominal;

                return $val ? '
                <div class="d-flex justify-content-between w-100">
                    <span>' . number_format($val, 0, ',', '.') . '</span>
                </div>
            ' : '';
            })

            ->addColumn('diskon', function ($row) {
                return $row->diskon ? '
                <div class="d-flex justify-content-between w-100">
                    <span>' . number_format($row->diskon, 0, ',', '.') . '</span>
                </div>
            ' : '';
            })

            ->addColumn('bonus', function ($row) {
                return $row->bonus_konsumen ? '
                <div class="d-flex justify-content-between w-100">
                    <span>' . number_format($row->bonus_konsumen, 0, ',', '.') . '</span>
                </div>
            ' : '';
            })

            ->addColumn('biaya_custom', function ($row) {
                return $row->biaya_custom ? '
                <div class="d-flex justify-content-between w-100">
                    <span>' . number_format($row->biaya_custom, 0, ',', '.') . '</span>
                </div>
            ' : '';
            })

            ->addColumn('biaya_kpr', function ($row) {

                if ($row->biaya_kpr) {

                    $badge = $row->stt_free_biaya_kpr == 1
                        ? '<span class="badge bg-success">Free</span>'
                        : '<span class="badge bg-danger">Tidak Free</span>';

                    return '
                    <div>
                        <div class="d-flex justify-content-between w-100">
                            <span>' . number_format($row->biaya_kpr, 0, ',', '.') . '</span>
                        </div>
                        <div class="text-end mt-1">' . $badge . '</div>
                    </div>
                ';
                }

                return '';
            })

            ->addColumn('biaya_notaris', function ($row) {

                if ($row->biaya_notaris) {

                    $badge = $row->stt_free_biaya_notaris == 1
                        ? '<span class="badge bg-success">Free</span>'
                        : '<span class="badge bg-danger">Tidak Free</span>';

                    return '
                    <div>
                        <div class="d-flex justify-content-between w-100">
                            <span>' . number_format($row->biaya_notaris, 0, ',', '.') . '</span>
                        </div>
                        <div class="text-end mt-1">' . $badge . '</div>
                    </div>
                ';
                }

                return '';
            })

            ->addColumn('pajak_bphtb', function ($row) {

                if ($row->pajak_bphtb) {

                    $badge = $row->stt_free_pajak_bphtb == 1
                        ? '<span class="badge bg-success">Free</span>'
                        : '<span class="badge bg-danger">Tidak Free</span>';

                    return '
                    <div>
                        <div class="d-flex justify-content-between w-100">
                            <span>' . number_format($row->pajak_bphtb, 0, ',', '.') . '</span>
                        </div>
                        <div class="text-end mt-1">' . $badge . '</div>
                    </div>
                ';
                }

                return '';
            })

            ->addColumn('ppn', function ($row) {
                return $row->ppn ? '
                <div class="d-flex justify-content-between w-100">
                    <span>' . number_format($row->ppn, 0, ',', '.') . '</span>
                </div>
            ' : '';
            })

            ->addColumn('biaya_lain', function ($row) {
                return $row->biaya_lain_lain ? '
                <div class="d-flex justify-content-between w-100">
                    <span>' . number_format($row->biaya_lain_lain, 0, ',', '.') . '</span>
                </div>
            ' : '';
            })

            ->addColumn('total_plafond', function ($row) {

                $val = optional(
                    $row->wawancara
                        ->flatMap(fn($w) => $w->wawancaraSp3k)
                        ->sortByDesc('id')
                        ->first()
                )->acc_plafon;

                return $val ? '
                <div class="d-flex justify-content-between w-100">
                    <span>' . number_format($val, 0, ',', '.') . '</span>
                </div>
            ' : '';
            })

            ->addColumn('total_harga_unit', function ($row) {
                return $row->total_harga_rumah ? '
                <div class="d-flex justify-content-between w-100">
                    <span>' . number_format($row->total_harga_rumah, 0, ',', '.') . '</span>
                </div>
            ' : '';
            })

            ->rawColumns([
                'customer',
                'booking_fee',
                'dp',
                'diskon',
                'bonus',
                'biaya_custom',
                'biaya_kpr',
                'biaya_notaris',
                'pajak_bphtb',
                'ppn',
                'biaya_lain',
                'total_plafond',
                'total_harga_unit',
            ])
            ->make(true);
    }

    return view('admin.keuangan.pembukuan_biaya.index');
}
}
