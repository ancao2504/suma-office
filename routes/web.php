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

Route::get('/', 'App\Dashboard\DashboardController@index')->middleware('authLogin')->name('dashboard');

Route::group(['middleware' => 'preventbackhistory'], function () {
    Route::name('auth.')->group(function () {
        Route::get('/login', 'App\Auth\AuthController@index')->middleware('guest')->name('index');
        Route::post('/login/login', 'App\Auth\AuthController@login')->middleware('guest')->name('login');
        Route::get('/logout', 'App\Auth\AuthController@logout')->middleware('authLogin')->name('logout');

        Route::get('/auth/disableaccess', 'App\Auth\AuthController@disableAccess')->middleware('authLogin')->name('disable-access');
    });

    Route::name('header.')->group(function () {
        Route::post('/header/carttotal', 'App\Orders\CartController@cartInformation')->middleware('authLogin')->name('cart-total');
    });

    Route::name('dashboard.')->group(function () {
        Route::get('/dashboard/salesman', 'App\Dashboard\DashboardSalesmanController@index')->middleware('authLogin')->name('dashboard-salesman');
        Route::get('/dashboard/salesman/penjualanharian', 'App\Dashboard\DashboardSalesmanController@dashboardPenjualanHarian')->middleware('authLogin')->name('dashboard-salesman-penjualan-harian');
        Route::get('/dashboard/salesman/penjualangrouplevel', 'App\Dashboard\DashboardSalesmanController@dashboardPenjualanGroupLevel')->middleware('authLogin')->name('dashboard-salesman-penjualan-group-level');

        Route::get('/dashboard/dealer', 'App\Dashboard\DashboardDealerController@index')->middleware('authLogin')->name('dashboard-dealer');

        Route::get('/dashboard/management', 'App\Dashboard\Management\DashboardManagementController@index')->middleware('authLogin')->name('dashboard-management');
        Route::get('/dashboard/management/sales', 'App\Dashboard\Management\DashboardManagementSalesController@index')->middleware('authLogin')->name('dashboard-management-sales');
        Route::get('/dashboard/management/stock', 'App\Dashboard\Management\DashboardManagementStockController@index')->middleware('authLogin')->name('dashboard-management-stock');

        Route::get('/dashboard/marketing/pencapaian/perlevel', 'App\Dashboard\Marketing\DashboardMarketingController@dashboardPencapaianPerLevel')->middleware('authLogin')->name('dashboard-marketing-pencapaian-perlevel');
        Route::get('/dashboard/marketing/pencapaian/growth', 'App\Dashboard\Marketing\DashboardMarketingController@dashboardPencapaianGrowth')->middleware('authLogin')->name('dashboard-marketing-pencapaian-growth');
    });

    Route::name('profile.')->group(function () {
        Route::get('/profile/dealer', 'App\Profile\DealerController@index')->middleware('authLogin')->name('dealer');
        Route::get('/profile/dealer/view/{kode}', 'App\Profile\DealerController@dealerProfile')->where('kode', '(.*)')->middleware('authLogin')->name('dealer-profile');

        Route::get('/profile/account', 'App\Profile\AccountController@index')->middleware('authLogin')->name('account-profile');
        Route::post('/profile/account/save', 'App\Profile\AccountController@saveAccount')->middleware('authLogin')->name('save-account-profile');
        Route::get('/profile/account/changepassword', 'App\Profile\AccountController@changePassword')->middleware('authLogin')->name('account-change-password-profile');
        Route::post('/profile/account/changepassword/save', 'App\Profile\AccountController@saveChangePassword')->middleware('authLogin')->name('save-change-password-profile');

        Route::get('/profile/users', 'App\Profile\UserController@index')->middleware('authLogin')->name('users');
        Route::get('/profile/users/add', 'App\Profile\UserController@userAdd')->middleware('authLogin')->name('add-users');
        Route::post('/profile/users/save', 'App\Profile\UserController@userSave')->middleware('authLogin')->name('save-users');
        Route::get('/profile/users/edit/{user_id}', 'App\Profile\UserController@userEdit')->middleware('authLogin')->name('edit-users');
    });

    Route::name('parts.')->group(function () {
        Route::get('/parts/partnumber', 'App\Parts\PartNumberController@index')->middleware('authLogin')->name('part-number');
        Route::post('/parts/partnumber/viewcart', 'App\Parts\PartNumberController@partNumberViewCart')->middleware('authLogin')->name('view-cart-part-number');
        Route::post('/parts/partnumber/viewcart/add', 'App\Parts\PartNumberController@partNumberAddCart')->middleware('authLogin')->name('add-cart-part-number');


        Route::get('/parts/backorder', 'App\Parts\BackOrderController@index')->middleware('authLogin')->name('back-order');

        Route::get('/parts/stockharian', 'App\Parts\StockHarianController@index')->middleware('authLogin')->name('stock-harian');
        Route::get('/parts/stockharian/proses', 'App\Parts\StockHarianController@proses')->middleware('authLogin')->name('stock-harian-proses');
    });

    Route::name('orders.')->group(function () {
        Route::get('/orders/purchaseorder', 'App\Orders\PurchaseOrderController@index')->middleware('authLogin')->name('purchase-order');
        Route::get('/orders/purchaseorder/view/{nomor_pof}', 'App\Orders\PurchaseOrderController@purchaseOrderForm')->middleware('authLogin')->name('purchase-order-form');
        Route::get('/orders/purchaseorder/view/detail/{kode_key}', 'App\Orders\PurchaseOrderController@purchaseOrderFormDetail')->middleware('authLogin')->name('purchase-order-form-edit-detail');
        Route::post('/orders/purchaseorder/view/detail/updatetpc', 'App\Orders\PurchaseOrderController@purchaseOrderFormUpdateTpc')->middleware('authLogin')->name('purchase-order-form-update-tpc');
        Route::post('/orders/purchaseorder/view/detail/editdiscount', 'App\Orders\PurchaseOrderController@purchaseOrderFormEditDiscount')->middleware('authLogin')->name('purchase-order-form-edit-discount');
        Route::post('/orders/purchaseorder/view/detail/updatediscount', 'App\Orders\PurchaseOrderController@purchaseOrderFormUpdateDiscount')->middleware('authLogin')->name('purchase-order-form-update-discount');
        Route::post('/orders/purchaseorder/view/detail/editpartnumber', 'App\Orders\PurchaseOrderController@purchaseOrderFormEditPart')->middleware('authLogin')->name('purchase-order-form-edit-part');
        Route::post('/orders/purchaseorder/view/detail/simpanpartnumber', 'App\Orders\PurchaseOrderController@purchaseOrderFormSimpanPart')->middleware('authLogin')->name('purchase-order-form-simpan-part');
        Route::post('/orders/purchaseorder/view/detail/hapuspartnumber', 'App\Orders\PurchaseOrderController@purchaseOrderFormHapusPart')->middleware('authLogin')->name('purchase-order-form-hapus-part');
        Route::post('/orders/purchaseorder/view/detail/save', 'App\Orders\PurchaseOrderController@purchaseOrderFormSave')->middleware('authLogin')->name('purchase-order-form-save');
        Route::post('/orders/purchaseorder/view/detail/batalapprove', 'App\Orders\PurchaseOrderController@purchaseOrderFormBatalApprove')->middleware('authLogin')->name('purchase-order-form-batal-approve');
        Route::post('/orders/purchaseorder/terlayani', 'App\Orders\PurchaseOrderController@viewDetailPofTerlayani')->middleware('authLogin')->name('purchase-order-form-terlayani');



        Route::post('/orders/cart/editheader', 'App\Orders\CartController@cartEditHeader')->name('edit-header-cart');
        Route::post('/orders/cart/importexcel', 'App\Orders\CartController@cartImportExcel')->name('import-excel-cart');
        Route::post('/orders/cart/resetdata', 'App\Orders\CartController@cartResetData')->name('reset-cart');

        Route::get('/orders/cart/cartdetail', 'App\Orders\CartController@daftarCartDetail')->name('daftar-cart-detail');
        Route::post('/orders/cart/cartdetail/insert', 'App\Orders\CartController@cartSimpanPart')->name('insert-cart-detail');
        Route::post('/orders/cart/cartdetail/delete', 'App\Orders\CartController@cartHapusPart')->name('delete-cart-detail');

        Route::get('/orders/cart', 'App\Orders\CartController@index')->middleware('authLogin')->name('cart');
        Route::post('/orders/cart/edit/', 'App\Orders\CartController@cartEditDetail')->middleware('authLogin')->name('cart-detail-edit');
        Route::post('/orders/cart/simpandraft', 'App\Orders\CartController@cartSimpanDraft')->middleware('authLogin')->name('cart-simpan-draft');

        Route::get('/orders/cart/checkout/prepare', 'App\Orders\CartController@cartCheckOutPrepare')->middleware('authLogin')->name('cart-prepare-check-out');
        Route::post('/orders/cart/checkout/checkout', 'App\Orders\CartController@cartCheckOut')->middleware('authLogin')->name('cart-check-out');
        Route::get('/orders/cart/checkout/result', 'App\Orders\CartController@cartCheckOutResult')->middleware('authLogin')->name('cart-check-out-result');
        Route::post('/orders/cart/checkout/cekaturanharga', 'App\Orders\CartController@cartCheckOutCekAturanHarga')->middleware('authLogin')->name('cart-check-out-cek-aturan-harga');



        Route::get('/orders/faktur', 'App\Orders\FakturController@index')->middleware('authLogin')->name('faktur');
        Route::get('/orders/faktur/view/{no_faktur}', 'App\Orders\FakturController@fakturView')->where('no_faktur', '(.*)')->middleware('authLogin')->name('faktur-view');

        Route::get('/orders/tracking', 'App\Orders\TrackingOrderController@index')->middleware('authLogin')->name('tracking-order');
        Route::get('/orders/tracking/view/{no_faktur}', 'App\Orders\TrackingOrderController@trackingOrderView')->where('no_faktur', '(.*)')->middleware('authLogin')->name('tracking-order-view');

        Route::get('/orders/pembayaranfaktur', 'App\Orders\PembayaranFakturController@index')->middleware('authLogin')->name('pembayaran-faktur');
        Route::get('/orders/pembayaranfaktur/belumterbayar', 'App\Orders\PembayaranFakturController@pembayaranFakturBelumTerbayar')->middleware('authLogin')->name('pembayaran-faktur-belum-terbayar');
        Route::get('/orders/pembayaranfaktur/terbayar', 'App\Orders\PembayaranFakturController@pembayaranFakturTerbayar')->middleware('authLogin')->name('pembayaran-faktur-terbayar');
        Route::post('/orders/pembayaranfaktur/detailperfaktur', 'App\Orders\PembayaranFakturController@pembayaranFakturDetailPerFaktur')->middleware('authLogin')->name('pembayaran-faktur-detail-per-faktur');
        Route::post('/orders/pembayaranfaktur/detailperbpk', 'App\Orders\PembayaranFakturController@pembayaranFakturDetailPerBpk')->middleware('authLogin')->name('pembayaran-faktur-detail-per-bpk');
    });

    Route::name('option.')->group(function () {
        Route::get('/option/dealer', 'App\Option\OptionController@optionDealer')->middleware('authLogin')->name('option-dealer');
        Route::get('/option/dealerindex', 'App\Option\OptionController@optionDealerIndex')->middleware('authLogin')->name('option-dealer-index');
        Route::get('/option/dealersalesman', 'App\Option\OptionController@optionDealerSalesman')->middleware('authLogin')->name('option-dealer-salesman');
        Route::get('/option/salesman', 'App\Option\OptionController@optionSalesman')->middleware('authLogin')->name('option-salesman');
        Route::get('/option/supervisor', 'App\Option\OptionController@optionSupervisor')->middleware('authLogin')->name('option-supervisor');
        Route::get('/option/partnumber', 'App\Option\OptionController@optionPartNumber')->middleware('authLogin')->name('option-part-number');
        Route::get('/option/tipemotor', 'App\Option\OptionController@optionTipeMotor')->middleware('authLogin')->name('option-tipe-motor');
        Route::get('/option/groupproduk', 'App\Option\OptionController@OptionGroupProduk')->middleware('authLogin')->name('option-group-produk');
    });

    Route::name('setting.')->group(function () {
        Route::get('/setting/clossingmkr', 'App\Setting\SettingController@clossingMarketing')->middleware('authLogin')->name('setting-clossing-marketing');
        // diskon Produk
        Route::get('/setting/diskonproduk', 'App\Setting\Diskon\DiskonProdukController@index')->middleware('authLogin')->name('setting-diskon-produk');
        Route::post('/setting/diskonproduk/simpan', 'App\Setting\Diskon\DiskonProdukController@store')->middleware('authLogin')->name('setting-diskon-produk-simpan');
        Route::post('/setting/diskonproduk/hapus', 'App\Setting\Diskon\DiskonProdukController@destroy')->middleware('authLogin')->name('setting-diskon-produk-hapus');

        Route::post('/setting/diskonproduk/cekproduk', 'App\Setting\Diskon\DiskonProdukController@cekDiskonProduk')->middleware('authLogin')->name('setting-validasi-diskon-produk');

        // diskon Produk Dealer
        Route::get('/setting/diskonproduk/dealer', 'App\Setting\Diskon\DiskonProdukDealerController@index')->middleware('authLogin')->name('setting-diskon-produk-dealer');
        Route::post('/setting/diskonproduk/dealer/simpan', 'App\Diskon\Setting\DiskonProdukDealerController@store')->middleware('authLogin')->name('setting-diskon-produk-dealer-simpan');
        Route::post('/setting/diskonproduk/dealer/hapus', 'App\Diskon\Setting\DiskonProdukDealerController@destroy')->middleware('authLogin')->name('setting-diskon-produk-dealer-hapus');

        // part Netto
        Route::get('/setting/harga/partnetto', 'App\Setting\HargaNetto\HargaNettoPartsControllers@index')->middleware('authLogin')->name('setting-harga-netto-parts');
        Route::post('/setting/harga/partnetto/simpan', 'App\Setting\HargaNetto\HargaNettoPartsControllers@storeDestroy')->middleware('authLogin')->name('setting-harga-netto-parts-simpan');

        // part Netto Dealer
        Route::get('/setting/harga/partnettodealer', 'App\Setting\HargaNetto\HargaNettoPartsDealerControllers@index')->middleware('authLogin')->name('setting-harga-netto-parts-dealer');
        Route::post('/setting/harga/partnettodealer/simpan', 'App\Setting\HargaNetto\HargaNettoPartsDealerControllers@store')->middleware('authLogin')->name('setting-harga-netto-parts-dealer-simpan');
        Route::post('/setting/harga/partnettodealer/hapus', 'App\Diskon\Setting\HargaNettoPartsDealerControllers@destroy')->middleware('authLogin')->name('setting-harga-netto-parts-dealer-hapus');
    });

    Route::name('validasi.')->group(function () {
        Route::post('/validasi/salesman', 'App\Validasi\ValidasiController@validasiSalesman')->middleware('authLogin')->name('validasi-salesman');
        Route::post('/validasi/dealer', 'App\Validasi\ValidasiController@validasiDealer')->middleware('authLogin')->name('validasi-dealer');
        Route::post('/validasi/dealersalesman', 'App\Validasi\ValidasiController@validasiDealerSalesman')->middleware('authLogin')->name('validasi-dealer-salesman');
        Route::post('/validasi/partnumber', 'App\Validasi\ValidasiController@validasiPartNumber')->middleware('authLogin')->name('validasi-part-number');

        // validasiProduk
        Route::post('/validasi/produk', 'App\Validasi\ValidasiController@validasiProduk')->middleware('authLogin')->name('validasi-produk');
    });

    Route::name('visit.')->group(function () {
        Route::get('/visit/planningvisit', 'App\Visit\PlanningVisitController@index')->middleware('authLogin')->name('planning-visit');
        Route::post('/visit/planningvisit/save', 'App\Visit\PlanningVisitController@savePlanningVisit')->middleware('authLogin')->name('save-planning-visit');
        Route::post('/visit/planningvisit/delete', 'App\Visit\PlanningVisitController@deletePlanningVisit')->middleware('authLogin')->name('delete-planning-visit');
    });

    Route::get('/reportheader', function () {
        return view('reports.main.reportHeader');
    });
});
