<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\Customer\AduanCustomerController;
use App\Http\Controllers\Customer\ArsipCustomerController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Customer\ProspekController;
use App\Http\Controllers\Customer\SerahTerimaKunciController;
use App\Http\Controllers\Customer\UploudFileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImageOrFileController;
use App\Http\Controllers\Keuangan\HutangController;
use App\Http\Controllers\Keuangan\KategoriTransaksiController;
use App\Http\Controllers\Keuangan\KomisiController;
use App\Http\Controllers\Keuangan\LaporanArusKasController;
use App\Http\Controllers\Keuangan\MutasiSaldoController;
use App\Http\Controllers\Keuangan\PemasukanController;
use App\Http\Controllers\Keuangan\PembukuanBiayaController;
use App\Http\Controllers\Keuangan\PengeluaranController;
use App\Http\Controllers\Keuangan\PiutangController;
use App\Http\Controllers\Legal\BerkasPengajuanController;
use App\Http\Controllers\Legal\ListrikAirController;
use App\Http\Controllers\Marketing\MarketingAgentController;
use App\Http\Controllers\Marketing\MarketingOfflineController;
use App\Http\Controllers\Master\BankKPRController;
use App\Http\Controllers\Master\BankTransaksiController;
use App\Http\Controllers\Master\BarangController;
use App\Http\Controllers\Master\KavlingController;
use App\Http\Controllers\Master\LokasiKavlingController;
use App\Http\Controllers\Master\NotarisController;
use App\Http\Controllers\Master\PerusahaanController;
use App\Http\Controllers\Master\SatuanController;
use App\Http\Controllers\Master\SupplierController;
use App\Http\Controllers\OPBangunan\JenisPekerjaanBangunanController;
use App\Http\Controllers\OPBangunan\ProjectBangunanController;
use App\Http\Controllers\OPJalan\JalanController;
use App\Http\Controllers\OPJalan\JenisPekerjaanJalanController;
use App\Http\Controllers\OPJalan\ProjectJalanController;
use App\Http\Controllers\OPSaluran\JenisPekerjaanSaluranController;
use App\Http\Controllers\OPSaluran\ProjectSaluranController;
use App\Http\Controllers\OPSaluran\SaluranController;
use App\Http\Controllers\PanduanAplikasiController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\Pembelian\BarangMasukController;
use App\Http\Controllers\Pembelian\InputPOController;
use App\Http\Controllers\PengajuanHoldController;
use App\Http\Controllers\Pengaturan\HakAksesController;
use App\Http\Controllers\Pengaturan\KontenController;
use App\Http\Controllers\Pengaturan\ListPenjualanController;
use App\Http\Controllers\Pengaturan\LogAktivitasController;
use App\Http\Controllers\Pengaturan\PengaturanMediaController;
use App\Http\Controllers\Pengaturan\PengaturanPenggunaController;
use App\Http\Controllers\Pengaturan\PengaturanProfilController;
use App\Http\Controllers\Pengaturan\RoleUserController;
use App\Http\Controllers\Siteplan\SiteplanPenjualanController;
use App\Http\Controllers\Siteplan\SiteplanProyekController;
use App\Http\Controllers\Siteplan\SiteplanUnitReadyController;
use App\Http\Controllers\Transaksi\AccBankController;
use App\Http\Controllers\Transaksi\AkadController;
use App\Http\Controllers\Transaksi\BastController;
use App\Http\Controllers\Transaksi\GantiNamaController;
use App\Http\Controllers\Transaksi\PembelianCancelController;
use App\Http\Controllers\Transaksi\PindahUnitController;
use App\Http\Controllers\Transaksi\PPJBController;
use App\Http\Controllers\Transaksi\SPRController;
use App\Http\Controllers\Transaksi\WawancaraController;
use App\Http\Controllers\UnitReadyController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $frontPage = DB::table('konfigurasi')->value('front_page');

    if ($frontPage == 1) {
        return redirect()->route('homepage');
    }

    if (! Auth::check()) {
        return redirect()->route('login');
    }

    $menus = session('getmenus');

    if (! $menus || $menus->isEmpty()) {
        Auth::logout();

        return redirect()->route('login')->withErrors(['msg' => 'Anda tidak punya akses menu.']);
    }

    $beranda = $menus->firstWhere('route_name', 'beranda.index');

    if ($beranda) {
        return redirect()->route('beranda.index');
    }

    $firstMenu = $menus->first();

    if ($firstMenu) {
        if ($firstMenu->children && $firstMenu->children->isNotEmpty()) {
            return redirect()->route($firstMenu->children->first()->route_name);
        }

        return redirect()->route($firstMenu->route_name);
    }

    return redirect()->route('login');
});

Route::get('/booking', [PengajuanHoldController::class, 'booking'])->name('booking');
Route::get('/booking-sukses', [PengajuanHoldController::class, 'bookingSukses'])->name('booking.sukses');

Route::get('/get-kavling-hold/{id_lokasi}', [PengajuanHoldController::class, 'getKavlingHold'])->name('pengajuan-hold.getKavling');
Route::get('/get-harga-kavling-hold/{id_kavling}', [PengajuanHoldController::class, 'getHargaKavlingHold'])->name('pengajuan-hold.getHargaKavling');
Route::get('/get-kavling/{id_lokasi}', [PengajuanHoldController::class, 'getKavling'])->name('booking.getKavling');
Route::get('/get-harga-kavling/{id_kavling}', [PengajuanHoldController::class, 'getHargaKavling'])->name('booking.getHargaKavling');
Route::post('booking/store', [PengajuanHoldController::class, 'bookingStore'])->name('store.booking');
Route::get('/homepage', [KontenController::class, 'homepage'])->name('homepage');
Route::get('/agents', [KontenController::class, 'marketing'])->name('marketing');
Route::get('/aboutus', [KontenController::class, 'aboutus'])->name('aboutus');
Route::get('/progres', [KontenController::class, 'progres'])->name('progres');
Route::get('/siteplan/{id}', [KontenController::class, 'siteplan'])->name('konten.siteplan');

Route::group(['middleware' => 'guest'], function () {
    Route::get('admin/login', [AuthController::class, 'getLogin'])->name('login');
    Route::post('admin/post-login', [AuthController::class, 'postLogin'])->name('admin.loginPost');
});

Route::prefix('form-aduan')->controller(SerahTerimaKunciController::class)->group(function () {
    Route::get('tracking', 'tracking')->name('tracking.form');
    Route::post('submit', 'submitInput')->name('serah-terima-kunci.submit');
    Route::get('sukses', 'showSukses')->name('form-aduan.sukses');
    Route::get('{id?}', 'inputForm')->name('serah-terima-kunci.form');
    Route::post('fetch-customer', 'fetchCustomer')->name('serah-terima-kunci.fetch-customer');
});

Route::get('/generate-qr-code', [SerahTerimaKunciController::class, 'generateQRCode']);

Route::view('form-aduan-expired', 'frontend.form_aduan.expired')
    ->name('form-aduan.expired');

Route::get('/api/get-po/{id}', [BarangMasukController::class, 'getPO']);
Route::get('/serah-terima-kunci/nasabah/{id}', [SerahTerimaKunciController::class, 'getNasabahDetails'])->name('nasabah.details');
Route::get('/generate-qr-code', [SerahTerimaKunciController::class, 'generateQRCode']);

Route::get('get-blok-by-lokasi/{id}', [ProjectBangunanController::class, 'getBlokByLokasi'])->name('get.blok.by.lokasi');
Route::get('/get-unit-by-lokasi-blok', [ProjectBangunanController::class, 'getUnitByLokasiBlok']);

Route::get('get-jalan-by-lokasi/{id}', [ProjectJalanController::class, 'getJalanByLokasi'])->name('get.jalan.by.lokasi');
Route::get('get-jalan-detail/{id}', [ProjectJalanController::class, 'getJalanDetail'])->name('get.jalan.detail');

Route::get('get-saluran-by-lokasi/{id}', [ProjectSaluranController::class, 'getSaluranByLokasi'])->name('get.saluran.by.lokasi');
Route::get('get-saluran-detail/{id}', [ProjectSaluranController::class, 'getSaluranDetail'])->name('get.saluran.detail');

Route::get('/hutang/sisa-bayar/{id}', [HutangController::class, 'getSisaBayar']);
Route::get('/piutang/sisa-bayar/{id}', [PiutangController::class, 'getSisaBayar']);

Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->controller(DashboardController::class)->group(function () {
        Route::resource('beranda', BerandaController::class);
        Route::get('/dashboard', 'dashboard')->name('dashboard.index');
        Route::get('/dashboard/lokasi-penjualan/{id}', 'showLokasiPenjualan')->name('dashboard.lokasi-penjualan-show');
        Route::get('/dashboard/customer-status-progres/{id}', 'showCustomer')->name('dashboard.customer-status-progres-show');
        Route::get('/dashboard/customer-bank/{id}', 'showCustomer')->name('dashboard.customer-bank-show');
        Route::get('/dashboard/customer-marketing/{id}', 'showCustomer')->name('dashboard.customer-marketing-show');
        Route::get('/dashboard/customer-agent/{id}', 'showCustomer')->name('dashboard.customer-agent-show');
        Route::get('/total-unit', 'totalUnit')->name('dashboard.total-unit');
        Route::get('/booking-unit', 'booking')->name('dashboard.booking-unit');
        Route::get('/wawancara-unit', 'wawancara')->name('dashboard.wawancara-unit');
        Route::get('/akad-unit', 'akad')->name('dashboard.akad-unit');

        Route::prefix('/siteplan')->group(function () {
            Route::resource('siteplan-penjualan', SiteplanPenjualanController::class);
            Route::get('siteplan-penjualan/cetak/pdf/{id_lokasi}', [SiteplanPenjualanController::class, 'cetakPDF'])->name('siteplan-penjualan.cetak.pdf');
            Route::get('siteplan-penjualan/cetak/jpg/{id_lokasi}', [SiteplanPenjualanController::class, 'cetakJPG'])->name('siteplan-penjualan.cetak.jpg');

            Route::resource('siteplan-proyek', SiteplanProyekController::class);
            Route::get('siteplan-proyek/cetak/pdf/{id_lokasi}', [SiteplanProyekController::class, 'cetakPDF'])->name('siteplan-proyek.cetak.pdf');
            Route::get('siteplan-proyek/cetak/jpg/{id_lokasi}', [SiteplanProyekController::class, 'cetakJPG'])->name('siteplan-proyek.cetak.jpg');

            Route::resource('siteplan-unit-ready', SiteplanUnitReadyController::class);
            Route::get('siteplan-unit-ready/cetak/pdf/{id_lokasi}', [SiteplanUnitReadyController::class, 'cetakPDF'])->name('siteplan-unit-ready.cetak.pdf');
            Route::get('siteplan-unit-ready/cetak/jpg/{id_lokasi}', [SiteplanUnitReadyController::class, 'cetakJPG'])->name('siteplan-unit-ready.cetak.jpg');
        });
    });

    Route::resource('unit-ready', UnitReadyController::class);

    Route::prefix('admin')->group(function () {
        Route::get('/pengajuan-hold/arsip', [PengajuanHoldController::class, 'viewArsip'])->name('pengajuan-hold.arsip');
        Route::post('/pengajuan-hold/{id}/upload', [PengajuanHoldController::class, 'upload'])->name('pengajuan-hold.upload');
        Route::post('/pengajuan-hold/{id}/delete-file', [PengajuanHoldController::class, 'deleteFile'])->name('pengajuan-hold.delete-file');
        Route::get('/pengajuan-hold/{id}/verifikasi', [PengajuanHoldController::class, 'verifikasi'])->name('pengajuan-hold.verifikasi');
        Route::post('/pengajuan-hold/{id}/verifikasi', [PengajuanHoldController::class, 'simpanVerifikasi'])->name('pengajuan-hold.verifikasi.simpan');
        Route::get('/pengajuan-hold/{id}/arsip-detail', [PengajuanHoldController::class, 'arsipDetail'])->name('pengajuan-hold.arsip.detail');
        Route::resource('pengajuan-hold', PengajuanHoldController::class);
    });

    Route::prefix('admin')->controller(PembayaranController::class)->group(function () {
        Route::get('pembayaran/{id}/detail', 'detail')->name('pembayaran.detail');
        Route::get('pembayaran/detail-tagihan/{id}', 'detailTagihan')->name('pembayaran.detail-tagihan');
        Route::get('pembayaran/detail-pemasukan/{id}', 'detailPemasukan')->name('pembayaran.detail-pemasukan');
        Route::put('pembayaran/update-harga-rumah/{id}', 'UpdateHargaRumah')->name('Pembayaran.update-harga-rumah');

        Route::get('Pembayaran/rekap-pembayaran', 'rekapPembayaran')->name('pembayaran.rekap');

        Route::post('pembayaran/tambah-tagihan/{id}', 'tambahTagihan')->name('pembayaran.tambah-tagihan');
        Route::post('pembayaran/tambah-pemasukan/{id}', 'tambahPemasukan')->name('pembayaran.tambah-pemasukan');

        Route::delete('pembayaran/delete-tagihan/{id}', 'DeleteTagihan')->name('pembayaran.delete-tagihan');
        Route::delete('pembayaran/delete-pemasukan/{id}', 'DeletePemasukan')->name('pembayaran.delete-pemasukan');
        Route::get('/customer/cetak-rekap/{id}', 'cetakRekap')->name('customer.cetak-rekap');
        Route::get('/pembayaran/cetak/{id}', 'cetak')->name('pembayaran.cetak');
        Route::get('/pembayaran/print/{id}', 'print')->name('pembayaran.print');
        Route::resource('pembayaran', PembayaranController::class);
    });

    Route::prefix('admin/transaksi')->group(function () {
        Route::resource('wawancara', WawancaraController::class);
        Route::get('/wawancara/detail-customer/{id_customer}', [WawancaraController::class, 'detailCustomer'])->name('wawancara.detail-customer');
        Route::post('/wawancara/acc-bank/{id_wawancara}', [WawancaraController::class, 'simpanSp3k'])->name('wawancara.sp3k');
        Route::get('/wawancara/{id}/acc', [WawancaraController::class, 'acc'])->name('wawancara.acc');
        Route::get('/sp3k/data', [AccBankController::class, 'getDataSp3k'])->name('sp3k.data');

        Route::resource('acc-bank', AccBankController::class);
        Route::resource('akad', AkadController::class);
        Route::post('akad/seleksi-customer/{id_akad}', [AkadController::class, 'seleksiCustomer'])->name('akad.seleksi-customer');
        Route::get('akad/seleksi-customer/hadir/{id_detail}', [AkadController::class, 'showHadir'])->name('akad.seleksi-customer.get-hadir');
        Route::post('akad/seleksi-customer/hadir/{id_detail}', [AkadController::class, 'updateHadir'])->name('akad.seleksi-customer.update-hadir');

        Route::get('akad/detail-excel/{id}', [AkadController::class, 'cetakDetailExcel'])->name('akad.detail.excel');
        Route::delete('akad/detail/{id}', [AkadController::class, 'destroyDetail'])->name('akad.detail.destroy');
        Route::get('akad/detail-pdf/{id}', [AkadController::class, 'cetakDetailPDF'])->name('akad.detail.pdf');
        Route::get('/akad/{id}/seleksi', [AkadController::class, 'seleksi'])->name('akad.detail.seleksi');
        Route::get('akad/get-customer-detail/{id}', [AkadController::class, 'getCustomerDetail'])->name('akad.getCustomerDetail');
        Route::get('akad/detail/data/{id}', [AkadController::class, 'detailData'])->name('akad.detail.data');
        Route::get('/akad/download/{id}', [AkadController::class, 'downloadWord'])->name('akad.download');

        Route::get('cetak-bast/{id_customer}', [BastController::class, 'cetakBast'])
            ->name('bast.cetak');
        Route::get('/bast/generate-no', [BASTController::class, 'generateNoBAST'])->name('generateNoBAST');
        Route::resource('bast', BASTController::class);
        Route::get('/bast/detail-customer/{id}', [BastController::class, 'detailBast'])->name('bast.detail');

        Route::get('/ppjb/kpr/{id_customer}', [PPJBController::class, 'cetakKpr'])->name('ppjb.cetak-kpr');
        Route::get('/ppjb/cash-bertahap/{id_customer}', [PPJBController::class, 'cetakCashBertahap'])->name('ppjb.cetak-cash-bertahap');
        Route::get('/ppjb/pembelian-cash/{id_customer}', [PPJBController::class, 'cetakPembelianCash'])->name('ppjb.cetak-pembelian-cash');
        Route::resource('ppjb', PPJBController::class);
        Route::get('/ppjb/detail-customer/{id}', [PpjbController::class, 'detailPpjb'])->name('ppjb.detail');

        Route::resource('spr', SPRController::class);
        Route::get('/spr/detail-customer/{id}', [SPRController::class, 'detailSpr'])->name('spr.detail');

        Route::get('pindah-unit/kwitansi/{id}', [PindahUnitController::class, 'cetakKwitansi'])->name('pindah-unit.kwitansi');
        Route::get('pindah-unit/cetak-word/{id}', [PindahUnitController::class, 'cetakWord'])->name('pindah-unit.cetak-word');
        Route::get('pindah-unit/detail-customer/{id_customer}', [PindahUnitController::class, 'detailCustomer'])->name('pindah-unit.detail-customer');
        Route::get('pindah-unit/get-kavling-baru/{id_customer}', [PindahUnitController::class, 'getKavlingBaru'])->name('pindah-unit.getKavlingBaru');

        Route::get('pembatalan/kwitansi/{id}', [PembelianCancelController::class, 'cetakKwitansi'])->name('pembatalan.kwitansi');

        Route::resource('pindah-unit', PindahUnitController::class);
        Route::resource('pembelian-cancel', PembelianCancelController::class);
        Route::get('ganti-nama/cetak/{id}', [GantiNamaController::class, 'cetak'])->name('ganti-nama.cetak');
        Route::resource('ganti-nama', GantiNamaController::class);
        Route::get('ganti-nama/{id}/get-customer', [GantiNamaController::class, 'getCustomer'])->name('ganti-nama.get-customer');
    });

    Route::prefix('admin/customer')->group(function () {
        Route::get('get-kavling/{idLokasi}', [CustomerController::class, 'getKavling'])->name('customer.getKavling');
        Route::get('get-harga-kavling/{id_kavling}', [CustomerController::class, 'getHargaKavling'])->name('customer.getHargaKavling');
        Route::get('customer/cetak', [CustomerController::class, 'cetakData'])->name('customer.cetak');
        Route::get('customer/{id_customer}/subsidi-cetak', [CustomerController::class, 'cetakFormSubsidi'])->name('subsidi.cetak');

        Route::resource('customer', CustomerController::class);
        Route::resource('prospek', ProspekController::class);
        Route::resource('upload-file', UploudFileController::class);
        Route::resource('arsip-customer', ArsipCustomerController::class);
        Route::resource('aduan-customer', AduanCustomerController::class);
        Route::resource('serah-terima-kunci', SerahTerimaKunciController::class);
    });

    Route::prefix('admin/marketing')->group(function () {
        Route::resource('marketing-inhouse', MarketingOfflineController::class);
        Route::resource('marketing-agent', MarketingAgentController::class);
    });

    Route::prefix('admin/op-bangunan')->group(function () {
        Route::resource('proyek-bangunan', ProjectBangunanController::class);
        Route::resource('jenis-pekerjaan-bangunan', JenisPekerjaanBangunanController::class);
    });

    Route::prefix('op-bangunan/proyek-bangunan')->name('proyek-bangunan.')->group(function () {
        Route::get('cetak-rekapitulasi/{id}/{opKe}', [ProjectBangunanController::class, 'cetakRekapitulasi'])->name('cetak-rekapitulasi');
        Route::get('cetak-opname/{id}/{opKe}', [ProjectBangunanController::class, 'cetakOpname'])->name('cetak-opname');
        Route::get('detail-opname/{id}', [ProjectBangunanController::class, 'detail'])->name('detail');
        Route::get('create-opname/{id}', [ProjectBangunanController::class, 'createDetail'])->name('detail-create');
        Route::post('detail-store/{id}', [ProjectBangunanController::class, 'storeDetail'])->name('detail-store');
        Route::delete('detail-destroy/{id}', [ProjectBangunanController::class, 'destroyDetail'])->name('detail-destroy');
    });

    Route::prefix('admin/op-jalan')->group(function () {
        Route::resource('proyek-jalan', ProjectJalanController::class);
        Route::resource('jenis-pekerjaan-jalan', JenisPekerjaanJalanController::class);
        Route::resource('jalan', JalanController::class);
    });

    Route::prefix('OP-jalan/proyek-jalan')->name('proyek-jalan.')->group(function () {
        Route::get('cetak-opname/{id}/{opKe}', [ProjectJalanController::class, 'cetakOpname'])->name('cetak-opname');
        Route::get('detail-opname/{id}', [ProjectJalanController::class, 'detail'])->name('detail');
        Route::get('create-opname/{id}', [ProjectJalanController::class, 'createDetail'])->name('detail-create');
        Route::post('detail-store/{id}', [ProjectJalanController::class, 'storeDetail'])->name('detail-store');
        Route::delete('detail-destroy/{id}', [ProjectJalanController::class, 'destroyDetail'])->name('detail-destroy');
    });

    Route::prefix('admin/op-saluran')->group(function () {
        Route::resource('proyek-saluran', ProjectSaluranController::class);
        Route::resource('jenis-pekerjaan-saluran', JenisPekerjaanSaluranController::class);
        Route::resource('saluran', SaluranController::class);
    });

    Route::prefix('OP-saluran/proyek-saluran')->name('proyek-saluran.')->group(function () {
        Route::get('cetak-opname/{id}/{opKe}', [ProjectSaluranController::class, 'cetakOpname'])->name('cetak-opname');
        Route::get('detail-opname/{id}', [ProjectSaluranController::class, 'detail'])->name('detail');
        Route::get('create-opname/{id}', [ProjectSaluranController::class, 'createDetail'])->name('detail-create');
        Route::post('detail-store/{id}', [ProjectSaluranController::class, 'storeDetail'])->name('detail-store');
        Route::delete('detail-destroy/{id}', [ProjectSaluranController::class, 'destroyDetail'])->name('detail-destroy');
    });

    Route::controller(ImageOrFileController::class)->prefix('file')->group(function () {
        Route::get('get-foto-proyek-bangunan/{id}', 'getFotoProyekBangunan')->name('getFoto.bangunan');
        Route::get('get-foto-proyek-jalan/{id}', 'getFotoProyekJalan')->name('getFoto.jalan');
        Route::get('get-foto-proyek-saluran/{id}', 'getFotoProyekSaluran')->name('getFoto.saluran');
        Route::get('get-foto-aduan/{id}', 'getFotoAduan')->name('getFoto.aduan');
        Route::get('get-foto-proses-aduan/{id}', 'getFotoProsesAduan')->name('getFoto.prosesAduan');
        Route::delete('delete-foto-proyek-bangunan/{id}', [ImageOrFileController::class, 'destroyFotoProyekBangunan'])->name('hapusFoto.bangunan');
        Route::delete('delete-foto-proyek-jalan/{id}', [ImageOrFileController::class, 'destroyFotoProyekJalan'])->name('hapusFoto.jalan');
        Route::delete('delete-foto-proyek-saluran/{id}', [ImageOrFileController::class, 'destroyFotoProyekSaluran'])->name('hapusFoto.saluran');
    });

    Route::prefix('admin/legal')->group(function () {
        Route::resource('listrik-air', ListrikAirController::class);
        Route::resource('pengajuan-berkas', BerkasPengajuanController::class);
    });

    Route::prefix('admin/keuangan')->group(function () {
        Route::get('/laporan-arus-kas/filter', [LaporanArusKasController::class, 'filter'])->name('laporan-arus-kas.filter');
        Route::get('laporan-arus-kas/export-pdf', [LaporanArusKasController::class, 'exportPdf'])->name('laporan-arus-kas.exportPDF');
        Route::get('laporan-arus-kas/export-excel', [LaporanArusKasController::class, 'exportExcel'])->name('laporan-arus-kas.exportExcel');

        Route::resource('pemasukan', PemasukanController::class);
        Route::resource('pengeluaran', PengeluaranController::class);
        Route::resource('hutang', HutangController::class);
        Route::resource('piutang', PiutangController::class);
        Route::resource('komisi', KomisiController::class);
        Route::resource('pembukuan-biaya', PembukuanBiayaController::class);
        Route::resource('kategori-transaksi', KategoriTransaksiController::class);
        Route::resource('mutasi-saldo', MutasiSaldoController::class);
        Route::resource('laporan-arus-kas', LaporanArusKasController::class);
    });

    Route::prefix('admin/master')->group(function () {
        // Perusahaan
        Route::resource('perusahaan', PerusahaanController::class)->except('show');
        Route::put('lokasi-kavling/{id}/updateDetail', [LokasiKavlingController::class, 'updateDetail'])->name('LokasiKavling.updateDetail');
        Route::get('/lokasi-kavling/{id}/setting', [LokasiKavlingController::class, 'setting'])->name('LokasiKavling.setting');
        Route::put('/lokasi-kavling/{id}/setting', [LokasiKavlingController::class, 'updateSetting'])->name('LokasiKavling.updateSetting');
        Route::get('/lokasi-kavling/{id}/detail', [LokasiKavlingController::class, 'detail'])->name('LokasiKavling.detail');
        Route::get('/lokasi-kavling/export/{id}', [LokasiKavlingController::class, 'exportDetail'])->name('LokasiKavling.export');
        Route::post('/lokasi-kavling/upload-excel', [LokasiKavlingController::class, 'uploadExcel'])->name('LokasiKavling.uploadExcel');
        Route::get('/lokasi-kavling/{id}/edit-detail', [LokasiKavlingController::class, 'editDetail'])->name('LokasiKavling.editDetail');
        Route::put('/lokasi-kavling/{id}/update-detail', [LokasiKavlingController::class, 'updateDetail'])->name('LokasiKavling.updateDetail');
        Route::get('get-perusahaan', [LokasiKavlingController::class, 'getPerusahaan'])->name('getPerusahaan');

        Route::get('/kavling/cetak-excel/{id_lokasi}', [KavlingController::class, 'cetakExcel'])->name('kavling.cetakExcel');
        Route::get('kavling/cetak-pdf/{id_lokasi}', [KavlingController::class, 'cetakPdf'])->name('kavling.cetakPdf');
        Route::post('kavling/uploud', [KavlingController::class, 'uploud'])->name('kavling.uploud');
        Route::get('kavling/{id}/lampiran', [KavlingController::class, 'lampiran'])->name('kavling.lampiran');
        Route::post('kavling/{id}/lampiran/upload', [KavlingController::class, 'uploadLampiran'])->name('kavling.lampiran.upload');

        Route::resource('lokasi-kavling', LokasiKavlingController::class);
        Route::put('lokasi-kavling-denah/{id}', [LokasiKavlingController::class, 'updateDenah'])->name('lokasi-kavling-denah.update');
        Route::resource('kavling', KavlingController::class);
        Route::put('kavling/foto/{id}', [KavlingController::class, 'updateFoto'])->name('kavling.foto-update');
        Route::resource('bank-transaksi', BankTransaksiController::class);
        Route::resource('bank-kpr', BankKPRController::class);
        Route::get('bank/data/list', [BankTransaksiController::class, 'getBankList'])->name('bank.list');
        Route::resource('barang', BarangController::class);
        Route::resource('supplier', SupplierController::class);
        Route::resource('satuan', SatuanController::class);
        Route::resource('notaris', NotarisController::class);
    });

    Route::prefix('admin/pengaturan')->group(function () {
        Route::resource('pengaturan-profil', PengaturanProfilController::class);
        Route::resource('pengaturan-media', PengaturanMediaController::class);
        Route::put('pengaturan-pengguna/user-update/{id}', [PengaturanPenggunaController::class, 'updateUser'])->name('pengaturan-pengguna.update-user');
        Route::resource('pengaturan-pengguna', PengaturanPenggunaController::class);
        Route::resource('konten', KontenController::class);
        Route::resource('list-penjualan', ListPenjualanController::class);
        Route::resource('hak-akses', HakAksesController::class);
        Route::get('get-hak-akses', [HakAksesController::class, 'getHakAkses'])->name('admin.getHakAkses');
        Route::put('updateHakAkses', [HakAksesController::class, 'updateHakAkses'])->name('admin.updateHakAkses');
        Route::resource('role-user', RoleUserController::class);
        Route::get('get-role-user', [RoleUserController::class, 'getRoleUser'])->name('admin.getRoleUser');
        Route::put('updateRoleUser', [RoleUserController::class, 'updateRoleUser'])->name('admin.updateRoleUser');
        Route::resource('log-aktivitas', LogAktivitasController::class)->only(['index']);
    });

    Route::prefix('admin/pembelian')->group(function () {
        Route::resource('input-po', InputPoController::class);
        Route::put('input-po-pembayaran/{id}', [InputPoController::class, 'pembayaran'])->name('input-po.pembayaran');
        Route::resource('barang-masuk', BarangMasukController::class);
        Route::post('/barang-masuk/validasi-jumlah', [BarangMasukController::class, 'validateJumlah'])->name('barang-masuk.validate-jumlah');
    });

    Route::resource('admin/barang-keluar', BarangKeluarController::class);
    Route::post('/barang-keluar/validasi-jumlah', [BarangKeluarController::class, 'validateJumlah'])->name('barang-keluar.validate-jumlah');

    Route::get('/hutang/sisa-bayar/{id}', [HutangController::class, 'getSisaBayar']);
    Route::get('/piutang/sisa-bayar/{id}', [PiutangController::class, 'getSisaBayar']);

    Route::get('/panduan-aplikasi/menu', [PanduanAplikasiController::class, 'getMenuByRole']);
    Route::resource('panduan-aplikasi', PanduanAplikasiController::class);

    Route::post('admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
});

Route::get('/refresh-csrf', function () {
    return response()->json(['token' => csrf_token()]);
})->name('refresh.csrf');

Route::get('/paksa-logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/')->with('success', 'Anda telah logout.');
});
