<?php

use Illuminate\Http\Request;
use Api\Auth\ApiAuthController;
use Api\Auth\ApiAuthShopeeController;
use Illuminate\Support\Facades\Route;
use Api\Backend\Orders\ApiCartController;
use Api\Backend\Profile\ApiUserController;
use Api\Backend\Orders\ApiFakturController;
use Api\Backend\Konsumen\KonsumenController;
use Api\Backend\Profile\ApiDealerController;
use Api\Backend\Options\ApiOptionsController;
use Api\Backend\Parts\ApiBackOrderController;
use Api\Backend\Profile\ApiAccountController;
use Api\Backend\Setting\ApiSettingController;
use Api\Backend\Parts\ApiPartNumberController;
use Api\Backend\Parts\ApiStockHarianController;
use Api\Backend\Validasi\ApiValidasiController;
use Api\Backend\Gudang\Online\PackingController;
use Api\Backend\Online\ApiSerahTerimaController;
use Api\Backend\Upload\File\PriceListController;
use Api\Backend\Online\ApiApproveOrderController;
use Api\Backend\Orders\ApiPenerimaanSJController;
use Api\Backend\Visit\ApiPlanningVisitController;
use Api\Backend\Orders\ApiTrackingOrderController;
use Api\Backend\Orders\ApiPembayaranFakturController;
use Api\Backend\Orders\ApiPurchaseOrderFormController;
use Api\Backend\Setting\ApiSettingPartNettoController;
use Api\Backend\Reports\ReturController as ReportRetur;
use Api\Backend\Setting\ApiSettingCetakUlangController;
use Api\Backend\Dashboard\ApiDashboardSalesmanController;
use Api\Backend\Orders\ApiPenerimaanPembayaranController;
use Api\Backend\Reports\FakturController as ReportFaktur;
use Api\Backend\Setting\ApiSettingDiskonDealerController;
use Api\Backend\Setting\ApiSettingDiskonProdukController;
use Api\Backend\Konsumen\Konsumen_lokasi\LokasiController;
use Api\Backend\Retur\KonsumenController as ReturKonsumen;
use Api\Backend\Retur\SupplierController as ReturSupplier;
use Api\Backend\Reports\PackingController as ReportPacking;
use Api\Backend\Dashboard\ApiDashboardMarketplaceController;
use Api\Backend\Setting\ApiSettingPartNettoDealerController;
use Api\Backend\Reports\KonsumenController as Reportkonsumen;
use Api\Backend\Setting\ApiSettingDiskonProdukDealerController;
use Api\Backend\Online\Shopee\ApiOrderController as OrderShopee;
use Api\Backend\Online\Tiktok\ApiOrderController as OrderTiktok;
use Api\Backend\Online\ApiProductController as ProductMarketplace;
use Api\Backend\Dashboard\Marketing\ApiDashboardMarketingController;
use Api\Backend\Online\Shopee\ApiProductController as ProductShopee;
use Api\Backend\Online\Tiktok\ApiProductController as ProductTiktok;
use Api\Backend\Retur\SupplierJawabController as ReturSupplierJawab;
use Api\Backend\Online\Shopee\ApiShippingController as ShippingShopee;
use Api\Backend\Online\Tiktok\ApiShippingController as ShippingTiktok;
use Api\Backend\Online\Tokopedia\ApiOrderController as OrderTokopedia;
use Api\Backend\Upload\File\TypeMotorController;
use Api\Backend\Online\ApiPemindahanController as PemindahanMarketplace;
use Api\Backend\Online\Shopee\ApiEkspedisiController as EkspedisiShopee;
use Api\Backend\Online\Tiktok\ApiEkspedisiController as EkspedisiTiktok;
use Api\Backend\Online\Shopee\ApiPemindahanController as PemindahanShopee;
use Api\Backend\Online\Tiktok\ApiPemindahanController as PemindahanTiktok;
use Api\Backend\Online\Tokopedia\ApiProductController as ProductTokopedia;
use Api\Backend\Dashboard\Management\ApiDashboardManagementSalesController;
use Api\Backend\Dashboard\Management\ApiDashboardManagementStockController;
use Api\Backend\Online\shopee\ApiUpdateHargaController as UpdateHargaShopee;
use Api\Backend\Online\Tokopedia\ApiShippingController as ShippingTokopedia;
use App\Http\Controllers\Api\Backend\Dashboard\ApiDashboardDealerController;
use Api\Backend\Dashboard\Management\ApiDashboardManagementKuartalController;
use Api\Backend\Online\Shopee\ApiHistorySaldoController as HistorySaldoShopee;
use Api\Backend\Online\Tokopedia\ApiEkspedisiController as EkspedisiTokopedia;
use Api\Backend\Online\Tokopedia\ApiPemindahanController as PemindahanTokopedia;
use Api\Backend\Online\Tokopedia\ApiUpdateHargaController as UpdateHargaTokopedia;
use Api\Backend\Online\Tokopedia\ApiHistorySaldoController as HistorySaldoTokopedia;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'authBasic'], function () {
    Route::controller(ApiAuthController::class)->group(function () {
        Route::post('/auth/login', 'login');
        Route::post('/oauth/token', 'registerToken');
    });

    Route::group(['middleware' => 'authCheckToken'], function () {
        Route::name('account.')->group(function () {
            Route::controller(ApiAccountController::class)->group(function () {
                Route::post('/backend/account/profile', 'dataAccount')->name('profile-account');
                Route::post('/backend/account/profile/changepassword', 'simpanChangePassword')->name('simpan-password-account');
                Route::post('/backend/account/profile/simpan', 'simpanAccount')->name('simpan-profile-account');
            });
        });

        Route::name('backend.')->group(function () {
            Route::name('dashboard.')->group(function () {
                Route::controller(ApiDashboardSalesmanController::class)->group(function () {
                    Route::post('/backend/dashboard/salesman/penjualanbulanan', 'dashboardPenjualanBulanan')->name('dashboard-salesman-penjualan-bulanan');
                    Route::post('/backend/dashboard/salesman/penjualanbulanangrouplevel', 'dashboardPenjualanBulananGroupLevel')->name('dashboard-salesman-penjualan-bulanan-group-level');
                    Route::post('/backend/dashboard/salesman/penjualanharian', 'dashboardPenjualanHarian')->name('dashboard-salesman-penjualan-harian');
                });

                Route::controller(ApiDashboardDealerController::class)->group(function () {
                    Route::post('/backend/dashboard/dealer/penjualanbulanan', 'dashboardPenjualanBulanan')->name('dashboard-dealer-penjualan-bulanan');
                });

                Route::controller(ApiDashboardManagementSalesController::class)->group(function () {
                    Route::post('/backend/dashboard/management/sales/byproduct', 'dashboardSalesByProduct');
                    Route::post('/backend/dashboard/management/sales/bydate', 'dashboardSalesByDate');
                });

                Route::controller(ApiDashboardManagementStockController::class)->group(function () {
                    Route::post('/backend/dashboard/management/stock/stockbyproduct', 'dashboardStockByProduct');
                });

                Route::controller(ApiDashboardManagementKuartalController::class)->group(function () {
                    Route::post('/backend/dashboard/management/kuartal/index', 'dashboardSalesKuartal');
                });

                Route::controller(ApiDashboardMarketingController::class)->group(function () {
                    Route::post('/backend/dashboard/marketing/pencapaian/perproduk', 'dashboardPencapaianPerProduk');
                    Route::post('/backend/dashboard/marketing/pencapaian/perlevel', 'dashboardPencapaianPerLevel');
                    Route::post('/backend/dashboard/marketing/pencapaian/growth', 'dashboardPerbandinganGrowthPerTahun');
                });

                Route::controller(ApiDashboardMarketplaceController::class)->group(function () {
                    Route::post('/backend/dashboard/marketplace/salesbydate', 'salesByDate');
                    Route::post('/backend/dashboard/marketplace/salesbylocation', 'salesByLocation');
                });
            });

            Route::name('options.')->group(function () {
                Route::controller(ApiOptionsController::class)->group(function () {
                    Route::post('/backend/options/company', 'optionCompany')->name('option-company');
                    Route::post('/backend/options/dealer', 'optionDealer')->name('option-dealer');
                    Route::post('/backend/options/dealersalesman', 'optionDealerSalesman')->name('option-dealer-salesman');
                    Route::post('/backend/options/kabupaten', 'optionKabupaten')->name('option-kabupaten');
                    Route::post('/backend/options/roleuser', 'optionRoleUser')->name('option-role-user');
                    Route::post('/backend/options/user', 'optionUser')->name('option-user');
                    Route::post('/backend/options/classproduk', 'optionClassProduk')->name('option-class-produk');
                    Route::post('/backend/options/groupproduk', 'optionGroupProduk')->name('option-group-produk');
                    Route::post('/backend/options/levelproduk', 'optionLevelProduk')->name('option-level-produk');
                    Route::post('/backend/options/partnumber', 'optionPartNumber')->name('option-part-number');
                    Route::post('/backend/options/salesman', 'optionSalesman')->name('option-salesman');
                    Route::post('/backend/options/supervisor', 'optionSupervisor')->name('option-supervisor');
                    Route::post('/backend/options/subproduk', 'optionSubProduk')->name('option-sub-produk');
                    Route::post('/backend/options/typemotor', 'optionTypeMotor')->name('option-type-motor');
                    Route::post('/backend/options/updateharga', 'optionUpdateHarga')->name('option-update-harga');
                    Route::post('/backend/options/ekspedisionline', 'optionEkspedisiOnline')->name('option-ekspedisi-online');
                    // ! sby
                    Route::post('/backend/option/konsumen', 'dataKonsumen')->name('konsumen');
                    Route::post('/backend/pof', 'dataPof')->name('pof');
                    Route::post('/backend/campaign', 'dataCampaign')->name('campaign');
                    Route::post('/backend/salesman', 'dataSalesman')->name('salesman');
                    Route::post('/backend/retur', 'dataRetur')->name('retur');
                    Route::post('/backend/supplier', 'dataSupplier')->name('supplier');
                    Route::post('/backend/dealer', 'dataDealer')->name('dealer');
                    Route::post('/backend/faktur/konsumen', 'dataFakturKonsumen')->name('faktur-konsumen');
                    Route::post('/backend/faktur/klaim', 'dataFakturKlaim')->name('faktur-klaim');
                    Route::post('/backend/part', 'dataPart')->name('part');
                    Route::post('/backend/produk', 'dataProduk')->name('produk');
                    Route::post('/backend/merekmotor', 'dataMerekmotor')->name('merekmotor');
                    Route::post('/backend/typemotor', 'dataTypemotor')->name('typemotor');
                    Route::post('/backend/ukuranring', 'dataUkuranRing')->name('ukuranring');
                    Route::post('/backend/gudang/paking/online/meja', 'dataMejaPackingOnline')->name('meja');
                    Route::post('/backend/gudang/paking/online/packer', 'dataPackerPackingOnline')->name('packer');
                    Route::post('/backend/gudang/wh', 'dataWH')->name('wh');
                    Route::post('/backend/cabang', 'dataCabang')->name('cabang');
                    // ! end sby
                });
            });

            Route::name('orders.')->group(function () {
                Route::name('purchaseorderform.')->group(function () {
                    Route::controller(ApiPurchaseOrderFormController::class)->group(function () {
                        Route::post('/backend/orders/purchaseorderform', 'daftarPurchaseOrderForm')->name('daftar-purchase-order-form');
                        Route::post('/backend/orders/purchaseorderform/tpc/update', 'updateTpcPurchaseOrderForm')->name('update-tpc-purchase-order-form');
                        Route::post('/backend/orders/purchaseorderform/discount', 'editDiscountPurchaseOrderForm')->name('edit-discount-purchase-order-form');
                        Route::post('/backend/orders/purchaseorderform/discount/update', 'updateDiscountPurchaseOrderForm')->name('update-discount-purchase-order-form');
                        Route::post('/backend/orders/purchaseorderform/faktur', 'fakturPurchaseOrderForm')->name('faktur-purchase-order-form');
                        Route::post('/backend/orders/purchaseorderform/simpan', 'simpanPurchaseOrderForm')->name('simpan-purchase-order-form');
                        Route::post('/backend/orders/purchaseorderform/batalapprove', 'batalApprovePurchaseOrderForm')->name('batal-approve-purchase-order-form');

                        Route::name('detail.')->group(function () {
                            Route::post('/backend/orders/purchaseorderform/detail', 'detailPurchaseOrderForm')->name('detail-purchase-order-form');
                            Route::post('/backend/orders/purchaseorderform/detail/daftar', 'daftarPurchaseOrderFormDetail')->name('detail-purchase-order-form-daftar');
                            Route::post('/backend/orders/purchaseorderform/detail/partnumber', 'partPurchaseOrderFormPartEdit')->name('detail-purchase-order-form-edit-part');
                            Route::post('/backend/orders/purchaseorderform/detail/partnumber/simpan', 'partPurchaseOrderFormPartSimpan')->name('detail-purchase-order-form-simpan-part');
                            Route::post('/backend/orders/purchaseorderform/detail/partnumber/hapus', 'partPurchaseOrderFormPartHapus')->name('detail-purchase-order-form-hapus-part');
                        });
                    });
                });

                Route::name('cart.')->group(function () {
                    Route::controller(ApiCartController::class)->group(function () {
                        Route::post('/backend/orders/cart/estimasi', 'estimasiCart');
                        Route::post('/backend/orders/cart/header', 'headerCart');
                        Route::post('/backend/orders/cart/detail', 'detailCart')->name('detail-cart');
                        Route::post('/backend/orders/cart/reset', 'resetCart')->name('reset-cart');
                        Route::post('/backend/orders/cart/editpart', 'editPartNumberCart')->name('edit-cart-part');
                        Route::post('/backend/orders/cart/importexcel', 'importExcelCart')->name('import-excel-cart');
                        Route::post('/backend/orders/cart/prosesexcel', 'prosesExcelCart')->name('proses-excel-cart');
                        Route::post('/backend/orders/cart/simpandraft', 'simpanCartDraft')->name('simpan-draft-cart');
                        Route::post('/backend/orders/cart/simpanpart', 'simpanPartNumberCart')->name('simpan-part-cart');
                        Route::post('/backend/orders/cart/deletepart', 'hapusPartNumberCart')->name('delete-part-cart');
                        Route::post('/backend/orders/cart/checkout/proses', 'prosesCheckOutCart')->name('proses-check-out-cart');
                        Route::post('/backend/orders/cart/checkout/result', 'resultCheckOutCart')->name('result-check-out-cart');
                        Route::post('/backend/orders/cart/deleteall', 'hapusCartTemporaryAll')->name('hapus-check-cart-all');
                        Route::post('/backend/orders/cart/checkout/cekaturanharga', 'cekAturanHargaCart')->name('cek-aturan-harga-cart');
                    });
                });

                Route::name('faktur.')->group(function () {
                    Route::controller(ApiFakturController::class)->group(function () {
                        Route::post('/backend/orders/faktur', 'daftarFaktur');
                        Route::post('/backend/orders/faktur/form', 'formFaktur');
                    });
                });

                Route::controller(ApiTrackingOrderController::class)->group(function () {
                    Route::post('/backend/orders/trackingorder', 'daftarTrackingOrder');
                    Route::post('/backend/orders/trackingorder/form', 'detailTrackingOrder');
                });

                Route::controller(ApiPenerimaanSJController::class)->group(function () {
                    Route::post('/backend/orders/penerimaan/sj', 'cekPenerimaanSJ')->name('cek-penerimaan-sj');
                    Route::post('/backend/orders/penerimaan/sj/simpan', 'simpanPenerimaanSJ')->name('simpan-penerimaan-sj');
                    Route::post('/backend/orders/penerimaan/sj/hapus', 'hapusPenerimaanSJ')->name('hapus-penerimaan-sj');
                    Route::post('/backend/orders/penerimaan/sj/report', 'reportPenerimaanSJ')->name('report-penerimaan-sj');
                });

                Route::name('pembayaranfaktur.')->group(function () {
                    Route::controller(ApiPembayaranFakturController::class)->group(function () {
                        Route::post('/backend/orders/pembayaranfaktur', 'daftarPembayaranFaktur')->name('daftar-pembayaran-faktur');
                        Route::post('/backend/orders/pembayaranfaktur/detailperfaktur', 'detailPembayaranPerFaktur')->name('detail-pembayaran-per-faktur');
                        Route::post('/backend/orders/pembayaranfaktur/detailperbpk', 'detailPembayaranPerBpk')->name('detail-pembayaran-per-bpk');
                    });
                });

                Route::controller(ApiPenerimaanPembayaranController::class)->group(function () {
                    Route::post('/backend/orders/penerimaan/pembayaran/daftar', 'pembayaranDealerDaftar')->name('daftar-pembayaran-dealer');
                    Route::post('/backend/orders/penerimaan/pembayaran/simpan', 'simpanPembayaran')->name('simpan-pembayaran-dealer');
                });
            });

            Route::name('profile.')->group(function () {
                Route::name('dealer.')->group(function () {
                    Route::controller(ApiDealerController::class)->group(function () {
                        Route::post('/backend/profile/dealer/daftar', 'daftarDealer');
                        Route::post('/backend/profile/dealer/form', 'formDealer');
                    });
                });

                Route::name('profile.')->group(function () {
                    Route::controller(ApiUserController::class)->group(function () {
                        Route::post('/backend/profile/users/daftar', 'daftarUser');
                        Route::post('/backend/profile/users/form', 'formUser');
                        Route::post('/backend/profile/users/simpan', 'simpanUser');
                    });
                });
            });

            Route::name('parts.')->group(function () {
                Route::name('partnumber.')->group(function () {
                    Route::controller(ApiPartNumberController::class)->group(function () {
                        Route::post('/backend/parts/partnumber/daftar', 'daftarPartNumber');
                        Route::post('/backend/parts/partnumber/image/list', 'PartNumberImageList');
                        Route::post('/backend/parts/partnumber/form/cart', 'formCartPartNumber');
                        Route::post('/backend/parts/partnumber/form/cart/proses', 'prosesCartPartNumber');
                    });
                });

                Route::name('backorder.')->group(function () {
                    Route::controller(ApiBackOrderController::class)->group(function () {
                        Route::post('/backend/parts/backorder/daftar', 'daftarBackOrder');
                    });
                });
            });

            Route::name('setting.')->group(function () {
                Route::name('default.')->group(function () {
                    Route::controller(ApiSettingController::class)->group(function () {
                        Route::post('/backend/setting/default/clossingmkr', 'settingCloseMarketing');
                    });
                });

                Route::name('cetakulang.')->group(function () {
                    Route::controller(ApiSettingCetakUlangController::class)->group(function () {
                        Route::post('/backend/setting/cetakulang/daftar', 'daftarCetakUlang');
                        Route::post('/backend/setting/cetakulang/cekdokumen', 'cekNomorDokumen');
                        Route::post('/backend/setting/cetakulang/simpan', 'simpanCetakUlang');
                    });
                });


                Route::controller(ApiSettingDiskonProdukController::class)->group(function () {
                    Route::post('/backend/setting/diskonproduk', 'daftarDiskonProduk')->name('setting-diskon-produk-daftar');
                    Route::post('/backend/setting/diskonproduk/cekproduk', 'cekKodeProdukCabang')->name('setting-diskon-produk-cek');
                    Route::post('/backend/setting/diskonproduk/simpan', 'simpanDiskonProduk')->name('setting-diskon-produk-simpan');
                    Route::post('/backend/setting/diskonproduk/hapus', 'hapusDiskonProduk')->name('setting-diskon-produk-hapus');
                });

                Route::controller(ApiSettingDiskonProdukDealerController::class)->group(function () {
                    Route::post('/backend/setting/diskonproduk/dealer', 'daftarDiskonProdukDealer')->name('setting-diskon-produk-dealer-daftar');
                    Route::post('/backend/setting/diskonproduk/cekproduk', 'cekDiskonProdukDealer')->name('setting-diskon-produk-dealer-cek');
                    Route::post('/backend/setting/diskonproduk/dealer/simpan', 'simpanDiskonProdukDealer')->name('setting-diskon-produk-dealer-simpan');
                    Route::post('/backend/setting/diskonproduk/dealer/hapus', 'hapusDiskonProdukDealer')->name('setting-diskon-produk-dealer-hapus');
                });

                Route::controller(ApiSettingDiskonDealerController::class)->group(function () {
                    Route::post('/backend/setting/diskon/dealer', 'daftarDiskonDealer')->name('setting-diskon-dealer-daftar');
                    Route::post('/backend/setting/diskon/dealer/simpan', 'simpanDiskonDealer')->name('setting-diskon-dealer-simpan');
                    Route::post('/backend/setting/diskon/dealer/hapus', 'hapusDiskonDealer')->name('setting-diskon-dealer-hapus');
                });

                Route::controller(ApiSettingPartNettoController::class)->group(function () {
                    Route::post('/backend/setting/harga/partnetto', 'daftarPartNetto')->name('setting-part-netto-daftar');
                    Route::post('/backend/setting/harga/partnetto/simpan', 'simpanPartNetto')->name('setting-part-netto-simpan');
                });

                Route::controller(ApiSettingPartNettoDealerController::class)->group(function () {
                    Route::post('/backend/setting/harga/partnetto/dealer', 'daftarPartNettoDealer')->name('setting-part-netto-dealer-daftar');
                    Route::post('/backend/setting/harga/partnetto/dealer/simpan', 'simpanPartNettoDealer')->name('setting-part-netto-dealer-simpan');
                    Route::post('/backend/setting/harga/partnetto/dealer/hapus', 'hapusPartNettoDealer')->name('setting-part-netto-dealer-dealer');
                });
            });

            Route::name('stockharian.')->group(function () {
                Route::controller(ApiStockHarianController::class)->group(function () {
                    Route::post('/backend/parts/stockharian/option', 'indexLaporanStockHarian')->name('option-stock-harian');
                    Route::post('/backend/parts/stockharian/proses/perlokasi', 'prosesStockPerlokasi')->name('proses-stock-per-lokasi');
                    Route::post('/backend/parts/stockharian/proses/marketplace', 'prosesStockMarketplace')->name('proses-stock-market-place');
                });
            });

            Route::name('validasi.')->group(function () {
                Route::controller(ApiValidasiController::class)->group(function () {
                    Route::post('/backend/validasi/salesman', 'validasiSalesman')->name('validasi-salesman');
                    Route::post('/backend/validasi/dealer', 'validasiDealer')->name('validasi-dealer');
                    Route::post('/backend/validasi/partnumber', 'validasiPartNumber')->name('validasi-part-number');
                    Route::post('/backend/validasi/produk', 'validasiProduk')->name('validasi-produk');
                    Route::post('/backend/validasi/dealersalesman', 'validasiDealerSalesman')->name('validasi-dealer-salesman');
                    Route::post('/backend/validasi/userid/tidakterdaftar', 'validasiUserIdTidakTerdaftar')->name('validasi-user-id-tidak-terdaftar');
                    Route::post('/backend/validasi/email/tidakterdaftar', 'validasiEmailTidakTerdaftar')->name('validasi-email-tidak-terdaftar');
                });
            });

            Route::name('visit.')->group(function () {
                Route::name('planningvisit.')->group(function () {
                    Route::controller(ApiPlanningVisitController::class)->group(function () {
                        Route::post('/backend/visit/planningvisit/daftar', 'daftarPlanningVisit')->name('daftar');
                        Route::post('/backend/visit/planningvisit/simpan', 'simpanPlanningVisit')->name('simpan');
                        Route::post('/backend/visit/planningvisit/hapus', 'hapusPlanningVisit')->name('hapus');
                    });
                });
            });

            Route::name('online.')->group(function () {
                Route::name('auth.')->group(function () {
                    Route::name('shopee.')->group(function () {
                        Route::controller(ApiAuthShopeeController::class)->group(function () {
                            Route::post('/backend/auth/shopee/token/access', 'dataAuthShopee');
                            Route::post('/backend/auth/shopee/token/access/generate', 'generateAuthorizationToken');
                            Route::post('/backend/auth/shopee/token/access/simpan', 'simpanAccessToken');
                        });
                    });
                });

                Route::name('pemindahan.')->group(function () {
                    Route::controller(PemindahanMarketplace::class)->group(function () {
                        Route::post('/backend/online/pemindahan/marketplace/daftar', 'daftarPemindahan');
                        Route::post('/backend/online/pemindahan/marketplace/detail', 'detailPemindahan');
                        Route::post('/backend/online/pemindahan/marketplace/update/stock', 'updateStock')->name('stock-perdokumen');
                        Route::post('/backend/online/pemindahan/marketplace/update/statuspartnumber', 'updateStatusPerPartNumber');
                    });

                    Route::name('tokopedia.')->group(function () {
                        Route::controller(PemindahanTokopedia::class)->group(function () {
                            Route::post('/backend/online/pemindahan/tokopedia/daftar', 'daftarPemindahan');
                            Route::post('/backend/online/pemindahan/tokopedia/form', 'formPemindahan');
                            Route::post('/backend/online/pemindahan/tokopedia/form/detail', 'formDetailPemindahan');
                            Route::post('/backend/online/pemindahan/tokopedia/form/update/statuspartnumber', 'updateStatusPerPartNumber');
                            Route::post('/backend/online/pemindahan/tokopedia/form/update/partnumber', 'updateStockPerPartNumber');
                            Route::post('/backend/online/pemindahan/tokopedia/form/update/dokumen', 'updateStockPerNomorDokumen');
                        });
                    });

                    Route::name('shopee.')->group(function () {
                        Route::controller(PemindahanShopee::class)->group(function () {
                            Route::post('/backend/online/pemindahan/shopee/daftar', 'daftarPemindahan');
                            Route::post('/backend/online/pemindahan/shopee/detail', 'detailPemindahan');
                            Route::post('/backend/online/pemindahan/shopee/update/stock/dokumen', 'updateStockperDokumen')->name('stock-perdokumen');
                            Route::post('/backend/online/pemindahan/shopee/update/stock/part', 'updateStockperPart')->name('stock-perpart');
                            Route::post('/backend/online/pemindahan/shopee/update/statuspartnumber', 'updateStatusPerPartNumber');
                        });
                    });

                    Route::name('tiktok.')->group(function () {
                        Route::controller(PemindahanTiktok::class)->group(function () {
                            Route::post('/backend/online/pemindahan/tiktok/daftar', 'daftarPemindahan');
                            Route::post('/backend/online/pemindahan/tiktok/form', 'formPemindahan');
                            Route::post('/backend/online/pemindahan/tiktok/form/detail', 'formDetailPemindahan');
                            Route::post('/backend/online/pemindahan/tiktok/form/update/statuspartnumber', 'updateStatusPerPartNumber');
                            Route::post('/backend/online/pemindahan/tiktok/form/update/partnumber', 'updateStockPerPartNumber');
                            Route::post('/backend/online/pemindahan/tiktok/form/update/dokumen', 'updateStockPerNomorDokumen');
                        });
                    });
                });

                Route::name('updateharga.')->group(function () {
                    Route::name('tokopedia.')->group(function () {
                        Route::controller(UpdateHargaTokopedia::class)->group(function () {
                            Route::post('/backend/online/updateharga/tokopedia/daftar', 'daftarUpdateHarga');
                            Route::post('/backend/online/updateharga/tokopedia/buatdokumen', 'buatDokumen');
                            Route::post('/backend/online/updateharga/tokopedia/form', 'formUpdateHarga');
                            Route::post('/backend/online/updateharga/tokopedia/update/partnumber', 'updateHargaPerPartNumber');
                            Route::post('/backend/online/updateharga/tokopedia/update/statuspartnumber', 'updateHargaStatusPerPartNumber');
                            Route::post('/backend/online/updateharga/tokopedia/update/dokumen', 'updateHargaPerNomorDokumen');
                        });
                    });


                    Route::name('shopee.')->group(function () {
                        Route::name('updateharga.')->group(function () {
                            Route::controller(UpdateHargaShopee::class)->group(function () {
                                Route::post('/backend/online/updateharga/shopee/daftar', 'daftarUpdateHarga');
                                Route::post('/backend/online/updateharga/shopee/buatdokumen', 'buatDokumen');
                                Route::post('/backend/online/updateharga/shopee/form', 'formUpdateHarga');
                                Route::post('/backend/online/updateharga/shopee/update/partnumber', 'updateHargaPerPartNumber');
                                Route::post('/backend/online/updateharga/shopee/update/statuspartnumber', 'updateHargaStatusPerPartNumber');
                                Route::post('/backend/online/updateharga/shopee/update/dokumen', 'updateHargaPerNomorDokumen');
                            });
                        });
                    });
                });

                Route::name('products.')->group(function () {
                    Route::name('tokopedia.')->group(function () {
                        Route::controller(ProductTokopedia::class)->group(function () {
                            Route::post('/backend/online/products/tokopedia/daftar', 'daftarPartNumber');
                            Route::post('/backend/online/products/tokopedia/cek/productid', 'cekProductId');
                            Route::post('/backend/online/products/tokopedia/update', 'updateProductID');
                        });
                    });

                    Route::name('shopee.')->group(function () {
                        Route::controller(ProductShopee::class)->group(function () {
                            Route::post('/backend/online/products/shopee/daftar',  'daftarPartNumber');
                            Route::post('/backend/online/products/shopee/cek/productid',  'cekProductId');
                            Route::post('/backend/online/products/shopee/update',  'updateProductID');

                            Route::post('/backend/online/products/shopee/brand',  'getBrandList');
                        });
                    });

                    Route::name('tiktok.')->group(function () {
                        Route::controller(ProductTiktok::class)->group(function () {
                            Route::post('/backend/online/products/tiktok/daftar', 'daftarPartNumber');
                            Route::post('/backend/online/products/tiktok/cek/productid', 'cekProductId');
                            Route::post('/backend/online/products/tiktok/update', 'updateProductID');
                        });
                    });

                    Route::controller(ProductMarketplace::class)->group(function () {
                        Route::post('/backend/online/products/marketplace/daftar',  'DaftarProduct');
                        Route::post('/backend/online/products/marketplace/detail',  'DetailProduct');
                        Route::post('/backend/online/products/marketplace/add',  'AddProduct');
                    });
                });

                Route::name('orders.')->group(function () {
                    Route::name('tokopedia.')->group(function () {
                        Route::controller(OrderTokopedia::class)->group(function () {
                            Route::post('/backend/online/orders/tokopedia/daftar', 'daftarOrder');
                            Route::post('/backend/online/orders/tokopedia/single', 'singleOrder');
                            Route::post('/backend/online/orders/tokopedia/form', 'formOrder');
                            Route::post('/backend/online/orders/tokopedia/proses', 'prosesFaktur');
                            Route::post('/backend/online/orders/tokopedia/update-kurir', 'updateKurir');
                        });
                    });

                    Route::name('shopee.')->group(function () {
                        Route::controller(OrderShopee::class)->group(function () {
                            Route::post('/backend/online/orders/shopee/daftar', 'daftarOrder');
                            Route::post('/backend/online/orders/shopee/single', 'singleOrder');
                            Route::post('/backend/online/orders/shopee/form', 'formOrder');
                            Route::post('/backend/online/orders/shopee/proses', 'prosesFaktur');
                        });
                    });

                    Route::name('tiktok.')->group(function () {
                        Route::controller(OrderTiktok::class)->group(function () {
                            Route::post('/backend/online/orders/tiktok/daftar', 'daftarOrder');
                            Route::post('/backend/online/orders/tiktok/single', 'singleOrder');
                            Route::post('/backend/online/orders/tiktok/form', 'formOrder');
                            Route::post('/backend/online/orders/tiktok/proses', 'prosesFaktur');
                        });
                    });

                    Route::name('approve.')->group(function () {
                        Route::controller(ApiApproveOrderController::class)->group(function () {
                            Route::post('/backend/online/order/approve/daftar', 'daftarApproveOrder');
                            Route::post('/backend/online/order/approve/form/tokopedia', 'formApproveTokopedia');
                            Route::post('/backend/online/order/approve/form/shopee', 'formApproveShopee');
                            Route::post('/backend/online/order/approve/form/internal', 'formApproveInternal');

                            Route::post('/backend/online/order/approve/proses/marketplace', 'prosesApproveMarketplace');
                            Route::post('/backend/online/order/approve/proses/internal', 'prosesApproveInternal');
                        });
                    });
                });

                Route::name('shipping.')->group(function () {
                    Route::name('tokopedia.')->group(function () {
                        Route::controller(ShippingTokopedia::class)->group(function () {
                            Route::post('/backend/online/shipping/tokopedia/pickup', 'prosesPickup');
                            Route::post('/backend/online/shipping/tokopedia/cetak-label', 'prosesCetakLabel');
                        });
                    });

                    Route::name('shopee.')->group(function () {
                        Route::controller(ShippingShopee::class)->group(function () {
                            Route::post('/backend/online/shipping/shopee/metode-pengiriman', 'metodePengiriman');
                            Route::post('/backend/online/shipping/shopee/pickup', 'prosesPickup');
                            Route::post('/backend/online/shipping/shopee/buat-dokumen', 'prosesBuatDokumenPengiriman');
                            Route::post('/backend/online/shipping/shopee/cetak-label', 'prosesCetakLabel');
                        });
                    });

                    Route::name('tiktok.')->group(function () {
                        Route::controller(ShippingTiktok::class)->group(function () {
                            Route::post('/backend/online/shipping/tiktok/pickup', 'prosesPickup');
                            Route::post('/backend/online/shipping/tiktok/cetak-label', 'prosesCetakLabel');
                        });
                    });
                });

                Route::name('ekspedisi.')->group(function () {
                    Route::name('tokopedia.')->group(function () {
                        Route::controller(EkspedisiTokopedia::class)->group(function () {
                            Route::post('/backend/online/ekspedisi/tokopedia/daftar', 'daftarEkspedisi');
                            Route::post('/backend/online/ekspedisi/tokopedia/simpan', 'simpanEkspedisi');
                        });
                    });

                    Route::name('shopee.')->group(function () {
                        Route::controller(EkspedisiShopee::class)->group(function () {
                            Route::post('/backend/online/ekspedisi/shopee/daftar', 'daftarEkspedisi');
                            Route::post('/backend/online/ekspedisi/shopee/simpan', 'simpanEkspedisi');
                        });
                    });

                    Route::name('tiktok.')->group(function () {
                        Route::controller(EkspedisiTiktok::class)->group(function () {
                            Route::post('/backend/online/ekspedisi/tiktok/daftar', 'daftarEkspedisi');
                            Route::post('/backend/online/ekspedisi/tiktok/simpan', 'simpanEkspedisi');
                        });
                    });
                });

                Route::name('serahterima.')->group(function () {
                    Route::name('online.')->group(function () {
                        Route::controller(ApiSerahTerimaController::class)->group(function () {
                            Route::post('/backend/online/serahterima/daftar', 'daftarSerahTerima');
                            Route::post('/backend/online/serahterima/form', 'formSerahTerima');
                            Route::post('/backend/online/serahterima/request/pickup', 'prosesPickupMarketplace');
                            Route::post('/backend/online/serahterima/update/status', 'updateStatusSerahTerima');
                        });
                    });
                });

                Route::name('historysaldo.')->group(function () {
                    Route::name('tokopedia.')->group(function () {
                        Route::controller(HistorySaldoTokopedia::class)->group(function () {
                            Route::post('/backend/online/historysaldo/tokopedia/daftar', 'daftarHistorySaldo');
                        });
                    });

                    Route::name('shopee.')->group(function () {
                        Route::controller(HistorySaldoShopee::class)->group(function () {
                            Route::post('/backend/online/historysaldo/shopee/daftar/group', 'daftarHistorySaldo');
                            Route::post('/backend/online/historysaldo/shopee/daftar/group/detail', 'detailHistorySaldo');
                            Route::post('/backend/online/historysaldo/shopee/daftar/detail', 'listDetailHistorySaldo');
                        });
                    });
                });
            });
            // ! sby

            Route::name('gudang.')->group(function () {
                Route::name('packing.')->group(function () {
                    Route::controller(PackingController::class)->group(function () {
                        Route::post('/backend/gudang/packing/online/simpan',  'store')->name('store');
                    });
                });
            });

            Route::name('retur.')->group(function () {
                Route::name('konsumen.')->group(function () {
                    Route::controller(ReturKonsumen::class)->group(function () {
                        Route::post('/backend/retur/konsumen/daftar',  'index')->name('index');
                        Route::post('/backend/retur/konsumen/simpan',  'store')->name('store');
                        Route::post('/backend/retur/konsumen/delete',  'destroy')->name('delete');
                    });
                });
                Route::name('supplier.')->group(function () {
                    Route::controller(ReturSupplier::class)->group(function () {
                        Route::post('/backend/retur/supplier/daftar',  'index')->name('index');
                        Route::post('/backend/retur/supplier/simpan',  'store')->name('store');
                        Route::post('/backend/retur/supplier/delete',  'destroy')->name('delete');
                    });
                    Route::name('jawab.')->group(function () {
                        Route::controller(ReturSupplierJawab::class)->group(function () {
                            Route::post('/backend/retur/supplier/jawab/simpan',  'store')->name('store');
                            Route::post('/backend/retur/supplier/jawab/delete',  'destroy')->name('delete');
                        });
                    });
                });
            });

            Route::name('report.')->group(function () {
                Route::name('faktur.')->group(function () {
                    Route::controller(ReportFaktur::class)->group(function () {
                        Route::post('/backend/report/faktur',  'data')->name('data');
                        Route::post('/backend/report/faktur/export',  'export')->name('export');
                    });
                });
                Route::name('retur.')->group(function () {
                    Route::controller(ReportRetur::class)->group(function () {
                        Route::post('/backend/report/retur',  'index')->name('index');
                        Route::post('/backend/report/retur',  'data')->name('data');
                        Route::post('/backend/report/retur/export',  'export')->name('export');
                    });
                });
                Route::name('packing.')->group(function () {
                    Route::controller(ReportPacking::class)->group(function () {
                        Route::post('/backend/report/packing',  'index')->name('index');
                        Route::post('/backend/report/packing',  'data')->name('data');
                        Route::post('/backend/report/packing/export',  'export')->name('export');
                    });
                });
                Route::name('konsumen.')->group(function () {
                    Route::controller(Reportkonsumen::class)->group(function () {
                        Route::post('/backend/report/konsumen/daftar',  'daftarKonsumen')->name('daftar');
                        Route::post('/backend/report/konsumen/daftar/export',  'exportDaftarKonsumen')->name('daftar.export');
                    });
                });
            });

            Route::name('konsumen.')->group(function () {
                Route::controller(LokasiController::class)->group(function () {
                    Route::post('/backend/konsumen/lokasi/',  'index')->name('lokasi');
                });
                Route::controller(KonsumenController::class)->group(function () {
                    Route::post('/backend/konsumen/',  'index')->name('index');
                    Route::post('/backend/konsumen/simpan',  'konsumenStore')->name('simpan');
                    Route::post('/backend/konsumen/hapus',  'konsumenDelete')->name('hapus');
                });
            });
            // ! end sby

            Route::name('Upload.')->group(function () {
                Route::name('file.')->group(function () {
                    Route::controller(PriceListController::class)->group(function () {
                        Route::post('backend/uploadfile/pricelist', 'index')->name('get');
                        Route::post('backend/uploadfile/pricelist/simpan', 'store')->name('simpan');
                        Route::post('backend/uploadfile/pricelist/hapus', 'destroy')->name('hapus');
                    });
                    Route::controller(TypeMotorController::class)->group(function () {
                        Route::post('backend/uploadimage/typemotor', 'master');
                        Route::post('backend/uploadimage/typemotordetail', 'detail');
                        Route::post('backend/uploadfile/motormaster/simpan', 'store');
                    });
                });
            });
        });
    });
});
