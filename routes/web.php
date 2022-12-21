<?php

use Illuminate\Support\Facades\authLogin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'preventbackhistory'], function () {
    Route::name('auth.')->group(function () {
        Route::get('/login', 'App\Auth\AuthController@index')->middleware('guest')->name('index');
        Route::post('/login/login', 'App\Auth\AuthController@login')->middleware('guest')->name('login');
        Route::get('/logout', 'App\Auth\AuthController@logout')->middleware('authLogin')->name('logout');

        Route::get('/auth/disableaccess', 'App\Auth\AuthController@disableAccess')->name('disable-access');
    });

    Route::group(['middleware' => 'authLogin'], function () {
        Route::name('header.')->group(function () {
            Route::post('/header/carttotal', 'App\Orders\CartController@cartInformation')->name('cart-total');
        });

        Route::name('home.')->group(function () {
            Route::get('/', 'App\Home\HomeController@index')->name('index');
        });

        Route::name('dashboard.')->group(function () {
            Route::get('/dashboard/salesman', 'App\Dashboard\DashboardSalesmanController@index')->name('dashboard-salesman');
            Route::get('/dashboard/salesman/penjualanharian', 'App\Dashboard\DashboardSalesmanController@dashboardPenjualanHarian')->name('dashboard-salesman-penjualan-harian');
            Route::get('/dashboard/salesman/penjualangrouplevel', 'App\Dashboard\DashboardSalesmanController@dashboardPenjualanGroupLevel')->name('dashboard-salesman-penjualan-group-level');

            Route::get('/dashboard/dealer', 'App\Dashboard\DashboardDealerController@index')->name('dashboard-dealer');

            Route::get('/dashboard/management', 'App\Dashboard\Management\DashboardManagementController@index')->name('dashboard-management');
            Route::get('/dashboard/management/sales', 'App\Dashboard\Management\DashboardManagementSalesController@index')->name('dashboard-management-sales');
            Route::get('/dashboard/management/stock', 'App\Dashboard\Management\DashboardManagementStockController@index')->name('dashboard-management-stock');

            Route::get('/dashboard/marketing/pencapaian/perlevel', 'App\Dashboard\Marketing\DashboardMarketingController@dashboardPencapaianPerLevel')->name('dashboard-marketing-pencapaian-perlevel');
            Route::get('/dashboard/marketing/pencapaian/growth', 'App\Dashboard\Marketing\DashboardMarketingController@dashboardPencapaianGrowth')->name('dashboard-marketing-pencapaian-growth');
            Route::get('/dashboard/marketing/pencapaian/perproduk', 'App\Dashboard\Marketing\DashboardMarketingController@dashboardPencapaianPerProduk')->name('dashboard-marketing-pencapaian-perproduk');
        });

        Route::name('profile.')->group(function () {
            Route::get('/profile/dealer', 'App\Profile\DealerController@index')->name('dealer');
            Route::get('/profile/dealer/view/{kode}', 'App\Profile\DealerController@dealerProfile')->where('kode', '(.*)')->name('dealer-profile');

            Route::get('/profile/account', 'App\Profile\AccountController@index')->name('account-profile');
            Route::post('/profile/account/save', 'App\Profile\AccountController@saveAccount')->name('save-account-profile');
            Route::get('/profile/account/changepassword', 'App\Profile\AccountController@changePassword')->name('account-change-password-profile');
            Route::post('/profile/account/changepassword/save', 'App\Profile\AccountController@saveChangePassword')->name('save-change-password-profile');

            Route::get('/profile/users', 'App\Profile\UserController@index')->name('users');
            Route::get('/profile/users/add', 'App\Profile\UserController@userAdd')->name('add-users');
            Route::post('/profile/users/save', 'App\Profile\UserController@userSave')->name('save-users');
            Route::get('/profile/users/edit/{user_id}', 'App\Profile\UserController@userEdit')->name('edit-users');
        });

        Route::name('parts.')->group(function () {
            Route::get('/parts/partnumber', 'App\Parts\PartNumberController@index')->name('part-number');
            Route::post('/parts/partnumber/viewcart', 'App\Parts\PartNumberController@partNumberViewCart')->name('view-cart-part-number');
            Route::post('/parts/partnumber/viewcart/add', 'App\Parts\PartNumberController@partNumberAddCart')->name('add-cart-part-number');


            Route::get('/parts/backorder', 'App\Parts\BackOrderController@index')->name('back-order');

            Route::get('/parts/stockharian', 'App\Parts\StockHarianController@index')->name('stock-harian');
            Route::get('/parts/stockharian/proses', 'App\Parts\StockHarianController@proses')->name('stock-harian-proses');
        });

        Route::name('orders.')->group(function () {
            Route::get('/orders/purchaseorder', 'App\Orders\PurchaseOrderController@index')->name('purchase-order');
            Route::get('/orders/purchaseorder/view/{nomor_pof}', 'App\Orders\PurchaseOrderController@purchaseOrderForm')->name('purchase-order-form');
            Route::get('/orders/purchaseorder/view/detail/{kode_key}', 'App\Orders\PurchaseOrderController@purchaseOrderFormDetail')->name('purchase-order-form-edit-detail');
            Route::post('/orders/purchaseorder/view/detail/updatetpc', 'App\Orders\PurchaseOrderController@purchaseOrderFormUpdateTpc')->name('purchase-order-form-update-tpc');
            Route::post('/orders/purchaseorder/view/detail/editdiscount', 'App\Orders\PurchaseOrderController@purchaseOrderFormEditDiscount')->name('purchase-order-form-edit-discount');
            Route::post('/orders/purchaseorder/view/detail/updatediscount', 'App\Orders\PurchaseOrderController@purchaseOrderFormUpdateDiscount')->name('purchase-order-form-update-discount');
            Route::post('/orders/purchaseorder/view/detail/editpartnumber', 'App\Orders\PurchaseOrderController@purchaseOrderFormEditPart')->name('purchase-order-form-edit-part');
            Route::post('/orders/purchaseorder/view/detail/simpanpartnumber', 'App\Orders\PurchaseOrderController@purchaseOrderFormSimpanPart')->name('purchase-order-form-simpan-part');
            Route::post('/orders/purchaseorder/view/detail/hapuspartnumber', 'App\Orders\PurchaseOrderController@purchaseOrderFormHapusPart')->name('purchase-order-form-hapus-part');
            Route::post('/orders/purchaseorder/view/detail/save', 'App\Orders\PurchaseOrderController@purchaseOrderFormSave')->name('purchase-order-form-save');
            Route::post('/orders/purchaseorder/view/detail/batalapprove', 'App\Orders\PurchaseOrderController@purchaseOrderFormBatalApprove')->name('purchase-order-form-batal-approve');
            Route::post('/orders/purchaseorder/terlayani', 'App\Orders\PurchaseOrderController@viewDetailPofTerlayani')->name('purchase-order-form-terlayani');


            Route::post('/orders/cart/editheader', 'App\Orders\CartController@cartEditHeader')->name('edit-header-cart');
            Route::post('/orders/cart/importexcel', 'App\Orders\CartController@cartImportExcel')->name('import-excel-cart');
            Route::post('/orders/cart/resetdata', 'App\Orders\CartController@cartResetData')->name('reset-cart');

            Route::get('/orders/cart/cartdetail', 'App\Orders\CartController@daftarCartDetail')->name('daftar-cart-detail');
            Route::post('/orders/cart/cartdetail/insert', 'App\Orders\CartController@cartSimpanPart')->name('insert-cart-detail');
            Route::post('/orders/cart/cartdetail/delete', 'App\Orders\CartController@cartHapusPart')->name('delete-cart-detail');

            Route::get('/orders/cart', 'App\Orders\CartController@index')->name('cart');
            Route::post('/orders/cart/edit/', 'App\Orders\CartController@cartEditDetail')->name('cart-detail-edit');
            Route::post('/orders/cart/simpandraft', 'App\Orders\CartController@cartSimpanDraft')->name('cart-simpan-draft');

            Route::get('/orders/cart/checkout/prepare', 'App\Orders\CartController@cartCheckOutPrepare')->name('cart-prepare-check-out');
            Route::post('/orders/cart/checkout/checkout', 'App\Orders\CartController@cartCheckOut')->name('cart-check-out');
            Route::get('/orders/cart/checkout/result', 'App\Orders\CartController@cartCheckOutResult')->name('cart-check-out-result');
            Route::post('/orders/cart/checkout/cekaturanharga', 'App\Orders\CartController@cartCheckOutCekAturanHarga')->name('cart-check-out-cek-aturan-harga');



            Route::get('/orders/faktur', 'App\Orders\FakturController@index')->name('faktur');
            Route::get('/orders/faktur/view/{no_faktur}', 'App\Orders\FakturController@fakturView')->where('no_faktur', '(.*)')->name('faktur-view');

            Route::name('trackingorder.')->group(function () {
                Route::get('/orders/tracking', 'App\Orders\TrackingOrderController@index')->name('daftar');
                Route::get('/orders/tracking/view/{no_faktur}', 'App\Orders\TrackingOrderController@trackingOrderView')->where('no_faktur', '(.*)')->name('view');
            });

            Route::get('/orders/pembayaranfaktur', 'App\Orders\PembayaranFakturController@index')->name('pembayaran-faktur');
            Route::get('/orders/pembayaranfaktur/belumterbayar', 'App\Orders\PembayaranFakturController@pembayaranFakturBelumTerbayar')->name('pembayaran-faktur-belum-terbayar');
            Route::get('/orders/pembayaranfaktur/terbayar', 'App\Orders\PembayaranFakturController@pembayaranFakturTerbayar')->name('pembayaran-faktur-terbayar');
            Route::post('/orders/pembayaranfaktur/detailperfaktur', 'App\Orders\PembayaranFakturController@pembayaranFakturDetailPerFaktur')->name('pembayaran-faktur-detail-per-faktur');
            Route::post('/orders/pembayaranfaktur/detailperbpk', 'App\Orders\PembayaranFakturController@pembayaranFakturDetailPerBpk')->name('pembayaran-faktur-detail-per-bpk');

            // surat jalan
            Route::get('/orders/penerimaan/ceksj', 'App\Orders\Penerimaan\SuratJalanController@CekPenerimaanSJ')->name('cek_penerimaan_sj');

            Route::get('/orders/penerimaan/sj', 'App\Orders\Penerimaan\SuratJalanController@create')->name('surat_jalan');
            Route::post('/orders/penerimaan/sj/simpan', 'App\Orders\Penerimaan\SuratJalanController@store')->name('surat_jalan_simpan');
            Route::post('/orders/penerimaan/sj/hapus', 'App\Orders\Penerimaan\SuratJalanController@destroy')->name('surat_jalan_hapus');

            Route::get('/orders/penerimaan/sj/filter', 'App\Orders\Penerimaan\SuratJalanController@filter')->name('surat_jalan_filter');
            Route::get('/orders/penerimaan/sj/report', 'App\Orders\Penerimaan\SuratJalanController@report')->name('surat_jalan_report');

            // pembayaran
            Route::get('/orders/penerimaan/pembayaran/daftar', 'App\Orders\Penerimaan\PembayaranController@daftarPembayaranDealer')->name('daftar-pembayaran');
            Route::post('/orders/penerimaan/pembayaran/simpan', 'App\Orders\Penerimaan\PembayaranController@store')->name('simpan-pembayaran');
            Route::get('/orders/penerimaan/pembayaran', 'App\Orders\Penerimaan\PembayaranController@index')->name('pembayaran');
        });

        Route::name('option.')->group(function () {
            Route::get('/option/dealer', 'App\Option\OptionController@optionDealer')->name('option-dealer');
            Route::get('/option/dealerindex', 'App\Option\OptionController@optionDealerIndex')->name('option-dealer-index');
            Route::get('/option/dealersalesman', 'App\Option\OptionController@optionDealerSalesman')->name('option-dealer-salesman');
            Route::get('/option/salesman', 'App\Option\OptionController@optionSalesman')->name('option-salesman');
            Route::get('/option/supervisor', 'App\Option\OptionController@optionSupervisor')->name('option-supervisor');
            Route::get('/option/partnumber', 'App\Option\OptionController@optionPartNumber')->name('option-part-number');
            Route::get('/option/tipemotor', 'App\Option\OptionController@optionTipeMotor')->name('option-tipe-motor');
            Route::get('/option/groupproduk', 'App\Option\OptionController@OptionGroupProduk')->name('option-group-produk');
        });

        Route::name('setting.')->group(function () {
            Route::get('/setting/clossingmkr', 'App\Setting\SettingController@clossingMarketing')->name('setting-clossing-marketing');

            Route::get('/setting/cetakulang', 'App\Setting\CetakUlang\CetakUlangController@index')->name('setting-cetak-ulang');
            Route::post('/setting/cetakulang/cekdokumen', 'App\Setting\CetakUlang\CetakUlangController@cekNomorDokumen')->name('setting-cetak-ulang-cek-dokumen');
            Route::post('/setting/cetakulang/simpan', 'App\Setting\CetakUlang\CetakUlangController@simpanCetakUlang')->name('setting-cetak-ulang-simpan');

            // diskon Produk
            Route::get('/setting/diskon/produk', 'App\Setting\Diskon\DiskonProdukController@index')->name('setting-diskon-produk');
            Route::post('/setting/diskon/produk/simpan', 'App\Setting\Diskon\DiskonProdukController@store')->name('setting-diskon-produk-simpan');
            Route::post('/setting/diskon/produk/hapus', 'App\Setting\Diskon\DiskonProdukController@destroy')->name('setting-diskon-produk-hapus');

            Route::post('/setting/diskon/produk/cekproduk', 'App\Setting\Diskon\DiskonProdukController@cekDiskonProduk')->name('setting-validasi-diskon-produk');

            // diskon Produk Dealer
            Route::get('/setting/diskon/dealer/produk', 'App\Setting\Diskon\DiskonProdukDealerController@index')->name('setting-diskon-produk-dealer');
            Route::post('/setting/diskon/dealer/produk/simpan', 'App\Setting\Diskon\DiskonProdukDealerController@store')->name('setting-diskon-produk-dealer-simpan');
            Route::post('/setting/diskon/dealer/produk/hapus', 'App\Setting\Diskon\DiskonProdukDealerController@destroy')->name('setting-diskon-produk-dealer-hapus');

            // diskon Dealer
            Route::get('/setting/diskon/default/dealer', 'App\Setting\Diskon\DiskonDealerController@index')->name('setting-diskon-dealer');
            Route::post('/setting/diskon/default/dealer/simpan', 'App\Setting\Diskon\DiskonDealerController@store')->name('setting-diskon-dealer-simpan');
            Route::post('/setting/diskon/default/dealer/hapus', 'App\Setting\Diskon\DiskonDealerController@destroy')->name('setting-diskon-dealer-hapus');

            // part Netto
            Route::get('/setting/harga/netto/part', 'App\Setting\HargaNetto\HargaNettoPartsControllers@index')->name('setting-harga-netto-parts');
            Route::post('/setting/harga/netto/part/simpan', 'App\Setting\HargaNetto\HargaNettoPartsControllers@storeDestroy')->name('setting-harga-netto-parts-simpan');

            // part Netto Dealer
            Route::get('/setting/harga/netto/dealer/part', 'App\Setting\HargaNetto\HargaNettoPartsDealerControllers@index')->name('setting-harga-netto-parts-dealer');
            Route::post('/setting/harga/netto/dealer/part/simpan', 'App\Setting\HargaNetto\HargaNettoPartsDealerControllers@store')->name('setting-harga-netto-parts-dealer-simpan');
            Route::post('/setting/harga/netto/dealer/part/hapus', 'App\Setting\HargaNetto\HargaNettoPartsDealerControllers@destroy')->name('setting-harga-netto-parts-dealer-hapus');
        });

        Route::name('validasi.')->group(function () {
            Route::post('/validasi/salesman', 'App\Validasi\ValidasiController@validasiSalesman')->name('validasi-salesman');
            Route::post('/validasi/dealer', 'App\Validasi\ValidasiController@validasiDealer')->name('validasi-dealer');
            Route::post('/validasi/dealersalesman', 'App\Validasi\ValidasiController@validasiDealerSalesman')->name('validasi-dealer-salesman');
            Route::post('/validasi/partnumber', 'App\Validasi\ValidasiController@validasiPartNumber')->name('validasi-part-number');

            // validasiProduk
            Route::post('/validasi/produk', 'App\Validasi\ValidasiController@validasiProduk')->name('validasi-produk');
        });

        Route::name('visit.')->group(function () {
            Route::get('/visit/planningvisit', 'App\Visit\PlanningVisitController@index')->name('planning-visit');
            Route::post('/visit/planningvisit/save', 'App\Visit\PlanningVisitController@savePlanningVisit')->name('save-planning-visit');
            Route::post('/visit/planningvisit/delete', 'App\Visit\PlanningVisitController@deletePlanningVisit')->name('delete-planning-visit');
        });
    });



    Route::get('/reportheader', function () {
        return view('reports.main.reportHeader');
    });
});
