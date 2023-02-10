<?php

use App\Auth\AuthController;

use App\Profile\UserController;
use App\Option\OptionController;
use App\Orders\FakturController;
use App\Profile\DealerController;
use App\Profile\AccountController;
use App\Orders\Cart\CartController;
use App\Parts\PartNumberController;
use App\Parts\StockHarianController;
use App\Validasi\ValidasiController;
use App\Parts\uplooadImageController;
use Illuminate\Support\Facades\Route;
use App\Visit\PlanningVisitController;
use App\Orders\TrackingOrderController;
use App\Dashboard\DashboardSalesmanController;
use App\Orders\Cart\Index\CartIndexController;
use App\Setting\Diskon\DiskonDealerController;
use App\Setting\Diskon\DiskonProdukController;
use App\Orders\Penerimaan\PembayaranController;
use App\Orders\Penerimaan\SuratJalanController;
use App\Setting\CetakUlang\CetakUlangController;
use App\setting\Diskon\DiskonProdukDealerController;
use App\Setting\HargaNetto\HargaNettoPartsControllers;
use App\Dashboard\Marketing\DashboardMarketingController;
use App\Http\Controllers\App\Online\Shopee\PemindahanShopeeController;
use App\Http\Controllers\App\Online\Tokopedia\PemindahanTokopediaController;
use App\Http\Controllers\app\Online\Tokopedia\UpdateHargaTokopediaController;
use App\Orders\PembayaranFaktur\PembayaranFakturController;
use App\Setting\HargaNetto\HargaNettoPartsDealerControllers;
use App\Orders\PurchaseOrderForm\PurchaseOrderFormController;
use App\Orders\PurchaseOrderForm\PurchaseOrderFormDetailController;

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
        Route::controller(AuthController::class)->group(function () {
            Route::get('/login', 'index')->middleware('guest')->name('index');
            Route::post('/login/login', 'login')->middleware('guest')->name('login');
            Route::get('/logout', 'logout')->middleware('authLogin')->name('logout');
            Route::get('/auth/disableaccess', 'disableAccess')->name('disable-access');
        });
    });

    Route::group(['middleware' => 'authLogin'], function () {

        Route::name('home.')->group(function () {
            Route::get('/', 'App\Home\HomeController@index')->name('index');
        });

        Route::name('dashboard.')->group(function () {
            Route::name('salesman.')->group(function () {
                Route::controller(DashboardSalesmanController::class)->group(function () {
                    Route::get('/dashboard/salesman', 'index')->name('salesman');
                    Route::get('/dashboard/salesman/penjualanharian', 'dashboardPenjualanHarian')->name('penjualan-harian');
                    Route::get('/dashboard/salesman/penjualangrouplevel', 'dashboardPenjualanGroupLevel')->name('penjualan-group-level');
                });
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
                    Route::controller(DashboardMarketingController::class)->group(function () {
                        Route::get('/dashboard/marketing/pencapaian/perlevel', 'dashboardPencapaianPerLevel')->name('perlevel');
                        Route::get('/dashboard/marketing/pencapaian/growth', 'dashboardPencapaianGrowth')->name('growth');
                        Route::get('/dashboard/marketing/pencapaian/perproduk', 'dashboardPencapaianPerProduk')->name('produk');
                    });
                });
            });
        });

        Route::name('profile.')->group(function () {
            Route::name('dealer.')->group(function () {
                Route::controller(DealerController::class)->group(function () {
                    Route::get('/profile/dealer/daftar', 'daftarDealer')->name('daftar');
                    Route::get('/profile/dealer/form/{kode}', 'formDealer')->where('kode', '(.*)')->name('form');
                });
            });

            Route::name('users.')->group(function () {
                Route::controller(UserController::class)->group(function () {
                    Route::get('/profile/users/daftar', 'daftarUser')->name('daftar');
                    Route::get('/profile/users/tambah', 'tambahUser')->name('tambah');
                    Route::get('/profile/users/form/{user_id}', 'formUser')->name('form');
                    Route::post('/profile/users/simpan', 'simpanUser')->name('simpan');
                });
            });

            Route::name('account.')->group(function () {
                Route::controller(AccountController::class)->group(function () {
                    Route::get('/profile/account/index', 'index')->name('index');
                    Route::post('/profile/account/simpan', 'saveAccount')->name('simpan');
                    Route::get('/profile/account/changepassword', 'changePassword')->name('change-password');
                    Route::post('/profile/account/changepassword/simpan', 'saveChangePassword')->name('simpan-password');
                });
            });
        });

        Route::name('parts.')->group(function () {
            Route::name('partnumber.')->group(function () {
                Route::controller(PartNumberController::class)->group(function () {
                    Route::get('/parts/partnumber/daftar', 'daftarPartNumber')->name('daftar');
                    Route::post('/parts/partnumber/cart/tambah', 'tambahCartPartNumber')->name('tambah');
                    Route::post('/parts/partnumber/cart/proses', 'prosesCartPartNumber')->name('proses');
                });
            });

            Route::name('uploadimage.')->group(function () {
                Route::controller(uplooadImageController::class)->group(function () {
                    Route::post('/parts/uploadimage/part', 'daftarImagePart')->name('daftar');
                    Route::get('/parts/uploadimage/part/input', 'index')->name('form-input');
                    Route::post('/parts/uploadimage/part/simpan', 'store')->name('simpan');
                });
            });

            Route::name('backorder.')->group(function () {
                Route::get('/parts/backorder/daftar', 'App\Parts\BackOrderController@index')->name('daftar');
            });

            Route::name('stockharian.')->group(function () {
                Route::controller(StockHarianController::class)->group(function () {
                    Route::get('/parts/stockharian/form', 'index')->name('form');
                    Route::get('/parts/stockharian/proses', 'proses')->name('proses');
                });
            });
        });

        Route::name('orders.')->group(function () {
            Route::name('purchaseorderform.')->group(function () {
                Route::controller(PurchaseOrderFormController::class)->group(function () {
                    Route::get('/orders/purchaseorderform/daftar', 'index')->name('daftar');
                    Route::post('/orders/purchaseorderform/terlayani', 'viewDetailPofTerlayani')->name('terlayani');
                });

                Route::name('form.')->group(function () {
                    Route::controller(PurchaseOrderFormController::class)->group(function () {
                        Route::get('/orders/purchaseorderform/form/{nomor_pof}', 'purchaseOrderForm')->name('detail');
                        Route::post('/orders/purchaseorderform/form/updatetpc', 'purchaseOrderFormUpdateTpc')->name('update-tpc');
                        Route::post('/orders/purchaseorderform/form/editdiscount', 'purchaseOrderFormEditDiscount')->name('edit-discount');
                        Route::post('/orders/purchaseorderform/form/updatediscount', 'purchaseOrderFormUpdateDiscount')->name('update-discount');
                        Route::post('/orders/purchaseorderform/form/simpan', 'purchaseOrderFormSimpan')->name('simpan');
                        Route::post('/orders/purchaseorderform/form/batalapprove', 'purchaseOrderFormBatalApprove')->name('batal-approve');
                    });

                    Route::name('detail.')->group(function () {
                        Route::controller(PurchaseOrderFormDetailController::class)->group(function () {
                            Route::get('/orders/purchaseorderform/form/detail/daftar', 'purchaseOrderFormDetailDaftar')->name('daftar');
                            Route::post('/orders/purchaseorderform/form/detail/editpartnumber', 'purchaseOrderFormDetailEditPart')->name('edit');
                            Route::post('/orders/purchaseorderform/form/detail/simpanpartnumber', 'purchaseOrderFormDetailSimpanPart')->name('simpan');
                            Route::post('/orders/purchaseorderform/form/detail/hapuspartnumber', 'purchaseOrderFormDetailHapusPart')->name('hapus');
                        });
                    });
                });
            });

            Route::name('cart.')->group(function () {
                Route::name('index.')->group(function () {
                    Route::controller(CartIndexController::class)->group(function () {
                        Route::get('/orders/cart/index/index', 'index')->name('index');
                        Route::get('/orders/cart/index/estimasi', 'estimasiCart')->name('estimasi-cart');
                    });
                });

                Route::controller(CartController::class)->group(function () {
                    Route::get('/orders/cart/index', 'index')->name('index');
                    Route::post('/orders/cart/importexcel', 'cartImportExcel')->name('import-excel');
                    Route::post('/orders/cart/resetdata', 'cartResetData')->name('reset');
                    Route::post('/orders/cart/simpandraft', 'cartSimpanDraft')->name('simpan-draft');

                    Route::name('detail.')->group(function () {
                        Route::get('/orders/cart/cartdetail', 'daftarCartDetail')->name('daftar');
                        Route::post('/orders/cart/cartdetail/edit/', 'cartEditDetail')->name('cart-detail-edit');
                        Route::post('/orders/cart/cartdetail/simpan', 'cartSimpanPart')->name('simpan');
                        Route::post('/orders/cart/cartdetail/hapus', 'cartHapusPart')->name('hapus');
                    });

                    Route::name('checkout.')->group(function () {
                        Route::get('/orders/cart/checkout/prepare', 'cartCheckOutPrepare')->name('prepare');
                        Route::post('/orders/cart/checkout/checkout', 'cartCheckOut')->name('check-out');
                        Route::get('/orders/cart/checkout/result', 'cartCheckOutResult')->name('result');
                        Route::post('/orders/cart/checkout/cekaturanharga', 'cartCheckOutCekAturanHarga')->name('cek-aturan-harga');
                    });
                });
            });


            Route::name('faktur.')->group(function () {
                Route::controller(FakturController::class)->group(function () {
                    Route::get('/orders/faktur/daftar', 'index')->name('daftar');
                    Route::get('/orders/faktur/form/{no_faktur}', 'fakturForm')->where('no_faktur', '(.*)')->name('form');
                });
            });

            Route::name('trackingorder.')->group(function () {
                Route::controller(TrackingOrderController::class)->group(function () {
                    Route::get('/orders/tracking/daftar', 'daftarTrackingOrder')->name('daftar');
                    Route::get('/orders/tracking/form/{no_faktur}', 'formTrackingOrder')->where('no_faktur', '(.*)')->name('form');
                });
            });

            Route::name('pembayaranfaktur.')->group(function () {

                Route::controller(PembayaranFakturController::class)->group(function () {
                    Route::get('/orders/pembayaranfaktur', 'index')->name('index');
                    Route::get('/orders/pembayaranfaktur/belumterbayar', 'pembayaranFakturBelumTerbayar')->name('daftar-belum-terbayar');
                    Route::get('/orders/pembayaranfaktur/terbayar', 'pembayaranFakturTerbayar')->name('daftar-terbayar');

                    Route::name('detail.')->group(function () {
                        Route::post('/orders/pembayaranfaktur/detail/faktur', 'pembayaranFakturDetailPerFaktur')->name('faktur');
                        Route::post('/orders/pembayaranfaktur/detail/bpk', 'pembayaranFakturDetailPerBpk')->name('bpk');
                    });
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
                Route::get('/option/updateharga', 'OptionUpdateHarga')->name('update-harga');
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
                Route::post('/validasi/produk', 'validasiProduk')->name('produk');
                Route::post('/validasi/partnumber', 'validasiPartNumber')->name('part-number');
            });
        });

        Route::name('visit.')->group(function () {
            Route::name('planning.')->group(function () {
                Route::controller(PlanningVisitController::class)->group(function () {
                    Route::get('/visit/planningvisit/daftar', 'daftarPlanningVisit')->name('daftar');
                    Route::post('/visit/planningvisit/simpan', 'simpanPlanningVisit')->name('simpan');
                    Route::post('/visit/planningvisit/hapus', 'hapusPlanningVisit')->name('hapus');
                });
            });
        });

        // buat route group online.
        Route::name('online.')->group(function(){

            Route::name('pemindahan.')->group(function () {
                Route::name('tokopedia.')->group(function () {
                    Route::get('/online/pemindahan/tokopedia/daftar', [PemindahanTokopediaController::class,'daftarPemindahan'])->name('daftar');

                    Route::name('form.')->group(function () {
                        Route::name('detail.')->group(function () {
                            Route::get('/online/pemindahan/tokopedia/form/detail', [PemindahanTokopediaController::class,'formPemindahanDetail'])->name('detail');
                            Route::post('/online/pemindahan/tokopedia/form/detail/update/partnumber', [PemindahanTokopediaController::class,'updateStockPerPartNumber'])->name('update-per-part-number');
                            Route::post('/online/pemindahan/tokopedia/form/detail/update/dokumen', [PemindahanTokopediaController::class,'updateStockPerNomorDokumen'])->name('update-per-dokumen');
                        });
                        Route::get('/online/pemindahan/tokopedia/form/{nomor_dokumen}', [PemindahanTokopediaController::class,'formPemindahan'])->where('nomor_dokumen', '(.*)')->name('form');
                    });
                });

                Route::name('shopee.')->group(function () {
                    Route::get('/online/pemindahan/shopee/daftar', [PemindahanShopeeController::class,'daftarPemindahan'])->name('daftar');
                    Route::post('/online/pemindahan/shopee/detail', [PemindahanShopeeController::class,'detailPemindahan'])->name('detail');
                });
            });

            Route::name('pemindahan.')->group(function () {
                Route::name('tokopedia.')->group(function () {
                    Route::get('/online/updateharga/tokopedia/daftar', [UpdateHargaTokopediaController::class,'daftarUpdateHarga'])->name('daftar');
                });
            });
        });
    });

    Route::get('/reportheader', function () {
        return view('reports.main.reportHeader');
    });
});
