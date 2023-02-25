<?php

namespace App\Http\Controllers\App\Online\Tokopedia;

use App\Helpers\ApiService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

class ProductController extends Controller
{
    public function index(Request $request) {
        $data_filter = new Collection();
        $data_filter->push((object) [
            'part_number'   => $request->get('part_number')
        ]);
        return view ('layouts.online.tokopedia.product.product', [
            'title_menu'    => 'Products',
            'data_filter'   => $data_filter->first()
        ]);
    }

    public function daftarPartNumber(Request $request) {
        $responseApi = ApiService::OnlineProductTokopediaSearchPartNumber(strtoupper(trim($request->get('part_number'))),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;

        if($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;

            $table_header = '';
            $table_detail = '';
            $jumlah_data = 0;

            foreach($dataApi->data as $data) {
                $jumlah_data = (double)$jumlah_data + 1;

                $image_url_part = $data->images;
                $image_not_found_url_part = "'" . asset('assets/images/background/part_image_not_found.png') . "'";

                $table_detail .= '<tr>
                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                        <span class="fs-6 fw-bolder text-gray-800">'.$jumlah_data.'</span>
                    </td>
                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                        <div class="d-flex mb-7">
                            <span class="symbol symbol-100px me-5">
                                <img src="'.$image_url_part.'"
                                    onerror="this.onerror=null; this.src='.$image_not_found_url_part.'"
                                    alt="'.trim($data->part_number).'">
                            </span>
                            <div class="flex-grow-1">
                                <div class="row">
                                    <p class="fs-6 text-gray-800 fw-bolder descriptionpart">'.strtoupper(trim($data->description)).'</p>
                                    <span class="fs-6 text-gray-700 fw-bolder">'.strtoupper(trim($data->part_number)).'</span>';

                if(strtoupper(trim($data->product_id)) == 0) {
                    $table_detail .= '<span class="fs-7 text-danger fw-boldest">(ProductID Masih Belum Terisi)</span>';
                } else {
                    $table_detail .= '<span class="fs-7 text-danger fw-boldest">'.strtoupper(trim($data->product_id)).'</span>';
                }


                $table_detail .= '<span class="fs-8 text-gray-400 fw-bolder mt-4">Harga:</span>
                                    <span class="fs-5 text-dark fw-bolder">Rp. '.number_format($data->het).'</span>
                                    <span class="fs-8 text-gray-400 fw-bolder mt-4">Stock:</span>
                                    <div class="align-items-center">
                                        <span class="fs-6 text-dark fw-bolder">'.number_format($data->stock).'</span>
                                        <span class="fs-7 text-gray-600 fw-bolder ms-2">PCS</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>';

                if(trim($data->marketplace->sku) == '') {
                    $table_detail .= '<td class="ps-3 pe-3" style="text-align:center;vertical-align:center;background-color: rgba(230, 230, 230, 0.4);">
                        <p class="fs-6 text-dark fw-boldest descriptionpart">PRODUCT ID TIDAK TERHUBUNG<br>DENGAN MARKETPLACE</p>
                    </td>';
                } else {
                    $table_detail .= '<td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                        <div class="d-flex mb-7">
                            <span class="symbol symbol-100px me-5">
                                <img src="'.trim($data->marketplace->pictures).'" onerror="this.onerror=null; this.src='.$image_not_found_url_part.'"
                                    alt="'.trim($data->marketplace->product_id).'">
                            </span>
                            <div class="flex-grow-1">
                                <div class="row">
                                    <p class="fs-6 text-gray-800 fw-bolder descriptionpart">'.trim($data->marketplace->name).'</p>
                                    <span class="fs-6 text-gray-700 fw-bolder">'.trim($data->marketplace->sku).'</span>
                                    <span class="fs-7 text-danger fw-boldest">'.trim($data->marketplace->product_id).'</span>
                                    <span class="fs-8 text-gray-400 fw-bolder mt-4">Harga:</span>
                                    <span class="fs-5 text-dark fw-bolder">Rp. '.number_format($data->marketplace->price).'</span>
                                    <span class="fs-8 text-gray-400 fw-bolder mt-4">Stock:</span>
                                    <div class="align-items-center">
                                        <span class="fs-6 text-dark fw-bolder">'.number_format($data->marketplace->stock).'</span>
                                        <span class="fs-7 text-gray-600 fw-bolder ms-2">PCS</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>';
                }
                $table_detail .= '<td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                        <button id="btnUpdateProductId" class="btn btn-icon btn-sm btn-danger" type="button"
                            data-part_number="'.strtoupper(trim($data->part_number)).'"
                            data-description="'.strtoupper(trim($data->description)).'"
                            data-product_id="'.strtoupper(trim($data->product_id)).'">
                            <i class="fa fa-database" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr>';
            }

            if((double)$jumlah_data <= 0) {
                $table_detail = '<tr>
                    <td colspan="4" class="pt-12 pb-12">
                        <div class="row text-center pe-10">
                            <span class="svg-icon svg-icon-muted">
                                <svg class="h-100px w-100px" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M21.7 18.9L18.6 15.8C17.9 16.9 16.9 17.9 15.8 18.6L18.9 21.7C19.3 22.1 19.9 22.1 20.3 21.7L21.7 20.3C22.1 19.9 22.1 19.3 21.7 18.9Z" fill="currentColor"/>
                                    <path opacity="0.3" d="M11 20C6 20 2 16 2 11C2 6 6 2 11 2C16 2 20 6 20 11C20 16 16 20 11 20ZM11 4C7.1 4 4 7.1 4 11C4 14.9 7.1 18 11 18C14.9 18 18 14.9 18 11C18 7.1 14.9 4 11 4ZM8 11C8 9.3 9.3 8 11 8C11.6 8 12 7.6 12 7C12 6.4 11.6 6 11 6C8.2 6 6 8.2 6 11C6 11.6 6.4 12 7 12C7.6 12 8 11.6 8 11Z" fill="currentColor"/>
                                </svg>
                            </span>
                        </div>
                        <div class="row text-center pt-8">
                            <span class="fs-6 fw-bolder text-gray-500">-  Tidak ada data yang ditampilkan -</span>
                        </div>
                    </td>
                </tr>';
            }

            $table_header = '<div class="table-responsive">
                <table class="table align-middle gs-0 gy-3">
                    <thead class="border">
                        <tr class="fs-8 fw-bolder text-muted">
                            <th class="w-50px ps-3 pe-3 text-center">No</th>
                            <th class="min-w-300px ps-3 pe-3 text-center">Suma</th>
                            <th class="min-w-300px ps-3 pe-3 text-center">Tokopedia</th>
                            <th class="w-100px ps-3 pe-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="border">'.$table_detail.'</tbody>
                </table>
            </div>';


            return ['status' => 1, 'message' => 'success', 'data' => $table_header];
        } else {
            return json_decode($responseApi, true);
        }
    }

    public function cekProductId(Request $request) {
        $responseApi = ApiService::OnlineProductTokopediaCekProductId(strtoupper(trim($request->get('product_id'))),
                        strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;

        if($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;

            $image_not_found_url_part = "'" . asset('assets/images/background/part_image_not_found.png') . "'";

            $data_tokopedia = '';

            if($dataApi->internal->status == 0) {
                $data_tokopedia = '<div class="fv-row mt-6">
                        <div class="alert alert-danger">
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-danger">Informasi</h4>
                                <span>'.$dataApi->internal->message.'</span>
                            </div>
                        </div>
                    </div>';
            } else {
                $data_tokopedia = '<div class="fv-row mt-6">
                        <div class="alert alert-success">
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-success">Informasi</h4>
                                <span>'.$dataApi->internal->message.'</span>
                            </div>
                        </div>
                    </div>';
            }

            $data_tokopedia .= '<div class="fv-row mt-6">
                    <div class="d-flex mb-7">
                        <span class="symbol symbol-200px me-5">
                            <img src="'.trim($dataApi->marketplace->pictures).'" onerror="this.onerror=null; this.src='.$image_not_found_url_part.'"
                                alt="'.trim($dataApi->marketplace->product_id).'">
                        </span>
                        <div class="flex-grow-1">
                            <div class="row">
                                <span class="fs-8 text-gray-400 fw-bolder">Nama Product:</span>
                                <p class="fs-6 text-gray-800 fw-bolder descriptionpart">'.trim($dataApi->marketplace->name).'</p>
                                <span class="fs-8 text-gray-400 fw-bolder mt-4">SKU:</span>
                                <span class="fs-6 text-gray-700 fw-bolder">'.trim($dataApi->marketplace->sku).'</span>
                                <span class="fs-8 text-gray-400 fw-bolder mt-4">Product ID:</span>
                                <span class="fs-7 text-danger fw-boldest">'.trim($dataApi->marketplace->product_id).'</span>
                            </div>
                        </div>
                    </div>
                </div>';

            return ['status' => 1, 'message' => 'success', 'data' => $data_tokopedia];
        } else {
            return json_decode($responseApi, true);
        }
    }

    public function updateProductId(Request $request) {
        $responseApi = ApiService::OnlineProductTokopediaUpdateProductId(strtoupper(trim($request->get('part_number'))),
                        strtoupper(trim($request->get('product_id'))),
                        strtoupper(trim($request->session()->get('app_user_company_id'))));
        return json_decode($responseApi, true);
    }
}
