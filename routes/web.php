<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\authLogin;
use app\Option\OptionController;
use app\Validasi\ValidasiController;
use app\Visit\PlanningVisitController;
use app\Setting\Diskon\DiskonDealerController;
use app\Setting\Diskon\DiskonProdukController;
use app\Orders\Penerimaan\PembayaranController;
use app\Orders\Penerimaan\SuratJalanController;
use app\Setting\CetakUlang\CetakUlangController;
use app\setting\Diskon\DiskonProdukDealerController;
use app\Setting\HargaNetto\HargaNettoPartsControllers;
use app\Setting\HargaNetto\HargaNettoPartsDealerControllers;

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

        Route::name('home.')->group(function () {
            Route::get('/', 'App\Home\HomeController@index')->name('index');
        });

        Route::name('dashboard.')->group(function () {
            Route::name('salesman.')->group(function () {
                Route::get('/dashboard/salesman', 'App\Dashboard\DashboardSalesmanController@index')->name('salesman');
                Route::get('/dashboard/salesman/penjualanharian', 'App\Dashboard\DashboardSalesmanController@dashboardPenjualanHarian')->name('penjualan-harian');
                Route::get('/dashboard/salesman/penjualangrouplevel', 'App\Dashboard\DashboardSalesmanController@dashboardPenjualanGroupLevel')->name('penjualan-group-level');
            });


            Route::name('dealer.')->group(function () {
                Route::get('/dashboard/dealer', 'App\Dashboard\DashboardDealerController@index')->name('dealer');
            });

            Route::name('management.')->group(function () {
                Route::get('/dashboard/management', 'App\Dashboard\Management\DashboardManagementController@index')->name('management');
                Route::get('/dashboard/management/sales', 'App\Dashboard\Management\DashboardManagementSalesController@index')->name('sales');
                Route::get('/dashboard/management/stock', 'App\Dashboard\Management\DashboardManagementStockController@index')->name('stock');
            });

            Route::name('marketing.')->group(function () {
                Route::name('pencapaian.')->group(function () {
                    Route::get('/dashboard/marketing/pencapaian/perlevel', 'App\Dashboard\Marketing\DashboardMarketingController@dashboardPencapaianPerLevel')->name('perlevel');
                    Route::get('/dashboard/marketing/pencapaian/growth', 'App\Dashboard\Marketing\DashboardMarketingController@dashboardPencapaianGrowth')->name('growth');
                    Route::get('/dashboard/marketing/pencapaian/perproduk', 'App\Dashboard\Marketing\DashboardMarketingController@dashboardPencapaianPerProduk')->name('produk');
                });
            });
        });

        Route::name('profile.')->group(function () {
            Route::name('dealer.')->group(function () {
                Route::get('/profile/dealer/daftar', 'App\Profile\DealerController@daftarDealer')->name('daftar');
                Route::get('/profile/dealer/form/{kode}', 'App\Profile\DealerController@formDealer')->where('kode', '(.*)')->name('form');
            });

            Route::name('users.')->group(function () {
                Route::get('/profile/users/daftar', 'App\Profile\UserController@daftarUser')->name('daftar');
                Route::get('/profile/users/tambah', 'App\Profile\UserController@tambahUser')->name('tambah');
                Route::get('/profile/users/form/{user_id}', 'App\Profile\UserController@formUser')->name('form');
                Route::post('/profile/users/simpan', 'App\Profile\UserController@simpanUser')->name('simpan');
            });

            Route::name('account.')->group(function () {
                Route::get('/profile/account/index', 'App\Profile\AccountController@index')->name('index');
                Route::post('/profile/account/simpan', 'App\Profile\AccountController@saveAccount')->name('simpan');
                Route::get('/profile/account/changepassword', 'App\Profile\AccountController@changePassword')->name('change-password');
                Route::post('/profile/account/changepassword/simpan', 'App\Profile\AccountController@saveChangePassword')->name('simpan-password');
            });


        });

        Route::name('parts.')->group(function () {
            Route::name('partnumber.')->group(function () {
                Route::get('/parts/partnumber/daftar', 'App\Parts\PartNumberController@daftarPartNumber')->name('daftar');
                Route::post('/parts/partnumber/cart/tambah', 'App\Parts\PartNumberController@tambahCartPartNumber')->name('tambah');
                Route::post('/parts/partnumber/cart/proses', 'App\Parts\PartNumberController@prosesCartPartNumber')->name('proses');
            });
            
            Route::name('uploadimage.')->group(function () {
                Route::post('/parts/uploadimage/part', 'App\Parts\uplooadImageController@daftarImagePart')->name('daftar');
                Route::get('/parts/uploadimage/part/input', 'App\Parts\uplooadImageController@index')->name('form-input');
                Route::post('/parts/uploadimage/part/simpan', 'App\Parts\uplooadImageController@store')->name('simpan');
            });

            Route::name('backorder.')->group(function () {
                Route::get('/parts/backorder/daftar', 'App\Parts\BackOrderController@index')->name('daftar');
            });

            Route::name('stockharian.')->group(function () {
                Route::get('/parts/stockharian/form', 'App\Parts\StockHarianController@index')->name('form');
                Route::get('/parts/stockharian/proses', 'App\Parts\StockHarianController@proses')->name('proses');
            });
        });

        Route::name('orders.')->group(function () {
            Route::name('purchaseorderform.')->group(function () {
                Route::get('/orders/purchaseorderform/daftar', 'App\Orders\PurchaseOrderForm\PurchaseOrderFormController@index')->name('daftar');
                Route::post('/orders/purchaseorderform/terlayani', 'App\Orders\PurchaseOrderForm\PurchaseOrderFormController@viewDetailPofTerlayani')->name('terlayani');

                Route::name('form.')->group(function () {
                    Route::get('/orders/purchaseorderform/form/{nomor_pof}', 'App\Orders\PurchaseOrderForm\PurchaseOrderFormController@purchaseOrderForm')->name('detail');
                    Route::post('/orders/purchaseorderform/form/updatetpc', 'App\Orders\PurchaseOrderForm\PurchaseOrderFormController@purchaseOrderFormUpdateTpc')->name('update-tpc');
                    Route::post('/orders/purchaseorderform/form/editdiscount', 'App\Orders\PurchaseOrderForm\PurchaseOrderFormController@purchaseOrderFormEditDiscount')->name('edit-discount');
                    Route::post('/orders/purchaseorderform/form/updatediscount', 'App\Orders\PurchaseOrderForm\PurchaseOrderFormController@purchaseOrderFormUpdateDiscount')->name('update-discount');
                    Route::post('/orders/purchaseorderform/form/simpan', 'App\Orders\PurchaseOrderForm\PurchaseOrderFormController@purchaseOrderFormSimpan')->name('simpan');
                    Route::post('/orders/purchaseorderform/form/batalapprove', 'App\Orders\PurchaseOrderForm\PurchaseOrderFormController@purchaseOrderFormBatalApprove')->name('batal-approve');

                    Route::name('detail.')->group(function () {
                        Route::get('/orders/purchaseorderform/form/detail/daftar', 'App\Orders\PurchaseOrderForm\PurchaseOrderFormDetailController@purchaseOrderFormDetailDaftar')->name('daftar');
                        Route::post('/orders/purchaseorderform/form/detail/editpartnumber', 'App\Orders\PurchaseOrderForm\PurchaseOrderFormDetailController@purchaseOrderFormDetailEditPart')->name('edit');
                        Route::post('/orders/purchaseorderform/form/detail/simpanpartnumber', 'App\Orders\PurchaseOrderForm\PurchaseOrderFormDetailController@purchaseOrderFormDetailSimpanPart')->name('simpan');
                        Route::post('/orders/purchaseorderform/form/detail/hapuspartnumber', 'App\Orders\PurchaseOrderForm\PurchaseOrderFormDetailController@purchaseOrderFormDetailHapusPart')->name('hapus');
                    });
                });
            });

            Route::name('cart.')->group(function () {
                Route::name('index.')->group(function () {
                    Route::get('/orders/cart/index/index', 'App\Orders\Cart\Index\CartIndexController@index')->name('index');
                    Route::get('/orders/cart/index/estimasi', 'App\Orders\Cart\Index\CartIndexController@estimasiCart')->name('estimasi-cart');
                });
                Route::get('/orders/cart/index', 'App\Orders\Cart\CartController@index')->name('index');
                Route::post('/orders/cart/importexcel', 'App\Orders\Cart\CartController@cartImportExcel')->name('import-excel');
                Route::post('/orders/cart/resetdata', 'App\Orders\Cart\CartController@cartResetData')->name('reset');
                Route::post('/orders/cart/simpandraft', 'App\Orders\Cart\CartController@cartSimpanDraft')->name('simpan-draft');

                Route::name('detail.')->group(function () {
                    Route::get('/orders/cart/cartdetail', 'App\Orders\Cart\CartController@daftarCartDetail')->name('daftar');
                    Route::post('/orders/cart/cartdetail/edit/', 'App\Orders\Cart\CartController@cartEditDetail')->name('cart-detail-edit');
                    Route::post('/orders/cart/cartdetail/simpan', 'App\Orders\Cart\CartController@cartSimpanPart')->name('simpan');
                    Route::post('/orders/cart/cartdetail/hapus', 'App\Orders\Cart\CartController@cartHapusPart')->name('hapus');
                });

                Route::name('checkout.')->group(function () {
                    Route::get('/orders/cart/checkout/prepare', 'App\Orders\CartController@cartCheckOutPrepare')->name('prepare');
                    Route::post('/orders/cart/checkout/checkout', 'App\Orders\CartController@cartCheckOut')->name('check-out');
                    Route::get('/orders/cart/checkout/result', 'App\Orders\CartController@cartCheckOutResult')->name('result');
                    Route::post('/orders/cart/checkout/cekaturanharga', 'App\Orders\CartController@cartCheckOutCekAturanHarga')->name('cek-aturan-harga');
                });
            });


            Route::name('faktur.')->group(function () {
                Route::get('/orders/faktur/daftar', 'App\Orders\FakturController@index')->name('daftar');
                Route::get('/orders/faktur/form/{no_faktur}', 'App\Orders\FakturController@fakturForm')->where('no_faktur', '(.*)')->name('form');
            });

            Route::name('trackingorder.')->group(function () {
                Route::get('/orders/tracking/daftar', 'App\Orders\TrackingOrderController@daftarTrackingOrder')->name('daftar');
                Route::get('/orders/tracking/form/{no_faktur}', 'App\Orders\TrackingOrderController@formTrackingOrder')->where('no_faktur', '(.*)')->name('form');
            });

            Route::name('pembayaranfaktur.')->group(function () {
                Route::get('/orders/pembayaranfaktur', 'App\Orders\PembayaranFaktur\PembayaranFakturController@index')->name('index');
                Route::get('/orders/pembayaranfaktur/belumterbayar', 'App\Orders\PembayaranFaktur\PembayaranFakturController@pembayaranFakturBelumTerbayar')->name('daftar-belum-terbayar');
                Route::get('/orders/pembayaranfaktur/terbayar', 'App\Orders\PembayaranFaktur\PembayaranFakturController@pembayaranFakturTerbayar')->name('daftar-terbayar');

                Route::name('detail.')->group(function () {
                    Route::post('/orders/pembayaranfaktur/detail/faktur', 'App\Orders\PembayaranFaktur\PembayaranFakturController@pembayaranFakturDetailPerFaktur')->name('faktur');
                    Route::post('/orders/pembayaranfaktur/detail/bpk', 'App\Orders\PembayaranFaktur\PembayaranFakturController@pembayaranFakturDetailPerBpk')->name('bpk');
                });
            });

            Route::name('warehouse.')->group(function () {
                Route::name('penerimaan.')->group(function () {
                    
                    Route::name('pembayaran.')->group(function () {
                        Route::controller(PembayaranController::class)->group(function () {
                            Route::post('/orders/warehouse/penerimaan/pembayaran/dealer/daftar', 'daftarPembayaranDealer')->name('daftar');
                            Route::post('/orders/warehouse/penerimaan/pembayaran/dealer/simpan', 'store')->name('simpan');
                            Route::get('/orders/warehouse/penerimaan/pembayaran/dealer/input', 'index')->name('form-input');
                        });
                    });
                    Route::name('suratjalan.')->group(function () {
                        Route::controller(SuratJalanController::class)->group(function () {
                            Route::get('/orders/warehouse/penerimaan/suratjalan/create', 'create')->name('form-input');
                            Route::get('/orders/warehouse/penerimaan/suratjalan/cek', 'CekPenerimaanSJ')->name('cek');
                            Route::post('/orders/warehouse/penerimaan/suratjalan/simpan', 'store')->name('simpan');
                            Route::post('/orders/warehouse/penerimaan/suratjalan/hapus', 'destroy')->name('hapus');
                            Route::get('/orders/warehouse/penerimaan/suratjalan/filter', 'filter')->name('filter');
                            Route::get('/orders/warehouse/penerimaan/suratjalan/report', 'report')->name('report');
                        });
                    });
                });
            });
            
        });

        Route::name('option.')->group(function () {
            Route::controller(OptionController::class)->group(function () {
                Route::get('/option/dealer', 'optionDealer')->name('dealer');
                Route::get('/option/dealersalesman', 'optionDealerSalesman')->name('dealer-salesman');
                Route::get('/option/salesman', 'optionSalesman')->name('salesman');
                Route::get('/option/supervisor', 'optionSupervisor')->name('supervisor');
                Route::get('/option/partnumber', 'optionPartNumber')->name('part-number');
                Route::get('/option/tipemotor', 'optionTipeMotor')->name('tipe-motor');
                Route::get('/option/groupproduk', 'OptionGroupProduk')->name('group-produk');
            });
        });

        Route::name('setting.')->group(function () {
            Route::name('default.')->group(function () {
                Route::get('/setting/clossingmkr', 'App\Setting\SettingController@clossingMarketing')->name('clossing-marketing');
            });

            Route::name('cetakulang.')->group(function () {
                Route::controller(CetakUlangController::class)->group(function () {
                    Route::get('/setting/cetakulang', 'index')->name('daftar');
                    Route::post('/setting/cetakulang/cekdokumen', 'cekNomorDokumen')->name('cek-dokumen');
                    Route::post('/setting/cetakulang/simpan', 'simpanCetakUlang')->name('simpan');
                });
            });
            
            Route::name('diskon.')->group(function () {
                Route::name('prosentase.')->group(function () {
                    Route::name('produk.')->group(function () {
                        // diskon produk
                        Route::controller(DiskonProdukController::class)->group(function () {
                            Route::get('/setting/diskon/prosentase/produk', 'index')->name('daftar');
                            Route::post('/setting/diskon/prosentase/produk/cek', 'cekDiskonProduk')->name('cek');
                            Route::post('/setting/diskon/prosentase/produk/simpan', 'store')->name('simpan');
                            Route::post('/setting/diskon/prosentase/produk/hapus', 'destroy')->name('hapus');
                        });

                        Route::name('dealer.')->group(function () {
                            // diskon produk dealer
                            Route::controller(DiskonProdukDealerController::class)->group(function () {
                                Route::get('/setting/diskon/prosentase/dealer/produk', 'index')->name('daftar');
                                Route::post('/setting/diskon/prosentase/dealer/produk/simpan', 'store')->name('simpan');
                                Route::post('/setting/diskon/prosentase/dealer/produk/hapus', 'destroy')->name('hapus');
                            });
                        });
                    });

                    Route::name('dealer.')->group(function () {
                        // diskon dealer
                        Route::controller(DiskonDealerController::class)->group(function () {
                            Route::get('/setting/diskon/prosentase/dealer/default', 'index')->name('daftar');
                            Route::post('/setting/diskon/prosentase/dealer/default/simpan', 'store')->name('simpan');
                            Route::post('/setting/diskon/prosentase/dealer/default/hapus', 'destroy')->name('hapus');
                        });
                    });

                    // Route::name('part.')->group(function () {
                    //     Route::get('/setting/diskon/default/dealer', 'App\Setting\Diskon\DiskonDealerController@index')->name('daftar');
                    //     Route::post('/setting/diskon/default/dealer/simpan', 'App\Setting\Diskon\DiskonDealerController@store')->name('simpan');
                    //     Route::post('/setting/diskon/default/dealer/hapus', 'App\Setting\Diskon\DiskonDealerController@destroy')->name('hapus');
                    // });
                });
                
            });
            // setting.netto.dealer.part
            Route::name('netto.')->group(function () {
                Route::name('dealer.')->group(function () {
                    Route::name('part.')->group(function () {
                        // netto dealer
                        Route::controller(HargaNettoPartsDealerControllers::class)->group(function () {
                            Route::get('/setting/netto/dealer/part', 'index')->name('daftar');
                            Route::post('/setting/netto/dealer/part/simpan', 'store')->name('simpan');
                            Route::post('/setting/netto/dealer/part/hapus', 'destroy')->name('hapus');
                        });
                    });
                });
                // setting.netto.part
                Route::name('part.')->group(function () {
                    // netto part
                    Route::controller(HargaNettoPartsControllers::class)->group(function () {
                        Route::get('/setting/netto/part', 'index')->name('daftar');
                        Route::post('/setting/netto/part/simpan', 'storeDestroy')->name('simpan');
                    });
                });
            });
        });

        Route::name('validasi.')->group(function () {
            Route::controller(ValidasiController::class)->group(function () {
                Route::post('/validasi/salesman', 'validasiSalesman')->name('salesman');
                Route::post('/validasi/dealer', 'validasiDealer')->name('dealer');
                Route::post('/validasi/dealersalesman', 'validasiDealerSalesman')->name('dealer-salesman');
                Route::post('/validasi/partnumber', 'validasiPartNumber')->name('part-number');
                Route::post('/validasi/produk', 'validasiProduk')->name('produk');
            });
        });

        Route::name('visit.')->group(function () {
            Route::name('planning.')->group(function () {
                Route::get('/visit/planningvisit/daftar', 'App\Visit\PlanningVisitController@daftarPlanningVisit')->name('daftar');
                Route::post('/visit/planningvisit/simpan', 'App\Visit\PlanningVisitController@simpanPlanningVisit')->name('simpan');
                Route::post('/visit/planningvisit/hapus', 'App\Visit\PlanningVisitController@hapusPlanningVisit')->name('hapus');
            });
        });
    });



    Route::get('/reportheader', function () {
        return view('reports.main.reportHeader');
    });
});