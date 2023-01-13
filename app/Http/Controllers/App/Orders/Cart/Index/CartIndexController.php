<?php

namespace App\Http\Controllers\App\Orders\Cart\Index;
use App\Http\Controllers\Controller;
use App\Helpers\ApiService;
use Illuminate\Http\Request;

class CartIndexController extends Controller
{
    public function index(Request $request)
    {
        $responseApi = ApiService::CartHeader(strtoupper(trim($request->session()->get('app_user_id'))),
                            strtoupper(trim($request->session()->get('app_user_role_id'))),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));

        return json_decode($responseApi, true);
    }

    public function estimasiCart(Request $request)
    {
        $responseApi = ApiService::CartEstimasi(strtoupper(trim($request->session()->get('app_user_id'))),
                            strtoupper(trim($request->session()->get('app_user_role_id'))),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $messageApi = json_decode($responseApi)->message;
        $statusApi = json_decode($responseApi)->status;

        $view_total_estimate_cart = '';
        $view_total_item_cart = '';
        if ($statusApi == 1) {
            if (strtoupper(trim($messageApi)) == 'SUCCESS') {
                $data = json_decode($responseApi)->data;
                $view_total_estimate_cart = '<div class="toolbar" id="kt_toolbar">
                        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
                            <div class="d-flex align-items-center gap-2 gap-lg-3">
                                <div class="m-0">
                                    <h1 class="d-flex text-dark fw-bolder fs-3 align-items-center my-1">' . strtoupper(trim($data->kode_dealer)) . '
                                        <span class="h-20px border-1 border-gray-200 border-start ms-3 mx-2 me-1"></span>
                                        <span class="text-muted fs-6 fw-bold ms-2">#Estimasi Cart:</span>
                                        <span class="text-danger fs-6 fw-bolder ms-4">Rp. ' . number_format($data->total) . '</span>
                                    </h1>
                                </div>
                                <a href="' . route('orders.cart.index') . '" class="btn btn-sm btn-success ms-2" role="button">Lihat Cart</a>
                            </div>
                        </div>
                    </div>';

                $view_total_item_cart = '<span class="position-absolute top-0 start-100 translate-middle badge badge-circle badge-danger mt-2">' . number_format($data->jumlah_item) . '</span>';
            } else {
                if (strtoupper(trim($request->get('app_user_role_id'))) != 'D_H3') {
                    $view_total_estimate_cart = '<div class="toolbar" id="kt_toolbar">
                        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
                            <div class="d-flex align-items-center gap-2 gap-lg-3">
                                <button id="btnSalesmanDealerHeaderIndex" name="btnSalesmanDealerHeaderIndex" type="button" class="btn btn-sm btn-success ms-2">Pilih Kode Dealer</button>
                            </div>
                        </div>
                    </div>';
                }
            }

            $data = ['status' => 1, 'view_estimate_cart' => $view_total_estimate_cart, 'view_item_cart' => $view_total_item_cart];
        } else {
            $data = ['status' => 0, 'view_estimate_cart' => $view_total_estimate_cart, 'view_item_cart' => $view_total_item_cart];
        }
        return $data;
    }
}
