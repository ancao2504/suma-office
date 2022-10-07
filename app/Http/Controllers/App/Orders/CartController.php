<?php

namespace App\Http\Controllers\App\Orders;

use App\Http\Controllers\App\Imports\ExcelCartController;
use App\Http\Controllers\App\Imports\ExcelDealerCartController;
use App\Http\Controllers\Controller;
use App\Helpers\ApiService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Jenssegers\Agent\Agent as Agent;
use Mockery\Undefined;

class CartController extends Controller
{
    public function index(Request $request) {
        $responseApi = ApiService::CartHeader(strtoupper(trim($request->session()->get('app_user_id'))),
                                    strtoupper(trim($request->session()->get('app_user_role_id'))),
                                    strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            $Agent = new Agent();
            $device = 'Desktop';

            if($Agent->isMobile()) {
                $device = 'Mobile';
            }

            return view('layouts.orders.cart.cart', [
                'title_menu'    => 'Cart',
                'device'        => $device,
                'role_id'       => trim($request->session()->get('app_user_role_id')),
                'kode_cart'     => trim($request->session()->get('app_user_id')),
                'tanggal'       => $data->tanggal,
                'salesman'      => $data->salesman,
                'dealer'        => $data->dealer,
                'back_order'    => $data->back_order,
                'keterangan'    => $data->keterangan
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function daftarCartDetail(Request $request) {
        $responseApi = ApiService::CartDetail(strtoupper(trim($request->session()->get('app_user_id'))),
                                    strtoupper(trim($request->session()->get('app_user_role_id'))),
                                    strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            $Agent = new Agent();

            if($Agent->isMobile()) {
                $data_card_detail = '';
                $jumlah_data_detail = 0;
                $data_grand_total = (double)$data->grand_total;
                $data_estimasi_total = (double)$data->estimasi_total;

                foreach($data->detail as $data) {
                    $jumlah_data_detail = (double)$jumlah_data_detail + 1;

                    $image_url_part = config('constants.app.app_images_url');
                    $image_not_found_url_part = "'".asset('assets/images/background/part_image_not_found.png')."'";

                    $data_kode_tpc = '';
                    if($data->kode_tpc == 14) {
                        $data_kode_tpc = '<span class="badge badge-light-primary fs-8 fw-bolder mt-1">TPC '.$data->kode_tpc.'</span>';
                    } else {
                        $data_kode_tpc = '<span class="badge badge-light-danger fs-8 fw-bolder mt-1">TPC '.$data->kode_tpc.'</span>';
                    }

                    $data_keterangan_bo = '';
                    if(trim($data->keterangan_bo) != '') {
                        $data_keterangan_bo = '<div class="flex-grow-1">
                            <span class="badge badge-info fs-8 fw-bolder mt-1">'.trim($data->keterangan_bo).'</span>
                        </div>';
                    }

                    $data_keterangan_harga = '';
                    if(trim($data->keterangan_harga) != '') {
                        $data_keterangan_harga = '<div class="flex-grow-1">
                            <span class="badge badge-danger fs-8 fw-bolder mt-1">'.trim($data->keterangan_harga).'</span>
                        </div>';
                    }

                    $data_keterangan_disc_produk = '';
                    if(trim($data->keterangan_disc_produk) != '') {
                        $data_keterangan_disc_produk = '<div class="flex-grow-1">
                            <span class="badge badge-light-danger fs-8 fw-bolder mt-1">'.trim($data->keterangan_disc_produk).'</span>
                        </div>';
                    }

                    $data_keterangan_disc_2x = '';
                    if(trim($data->keterangan_disc_2kali) != '') {
                        $data_keterangan_disc_2x = '<div class="flex-grow-1">
                            <span class="badge badge-light-danger fs-8 fw-bolder mt-1">'.trim($data->keterangan_disc_2kali).'</span>
                        </div>';
                    }

                    $data_keterangan_harga_tpc20 = '';
                    if($data->keterangan_harga_tpc20 != '') {
                        $data_keterangan_harga_tpc20 = '<div class="flex-grow-1">
                            <span class="badge badge-light-info fs-8 fw-bolder mt-1">'.trim($data->keterangan_harga_tpc20).'</span>
                        </div>';
                    }

                    $data_disc1 = '';
                    $data_decimal_disc1 = '';
                    if($data->disc1 > 0) {
                        if(str_contains(trim(number_format($data->disc1, 2)), '.00')) {
                            $data_decimal_disc1 = number_format($data->disc1);
                        } else {
                            $data_decimal_disc1 = number_format($data->disc1, 2);
                        }
                        $data_disc1 = '<span class="badge badge-light-danger fw-bolder fs-8">'.$data_decimal_disc1.'%</span>';
                    }

                    $data_disc2 = '';
                    $data_decimal_disc2 = '';
                    if($data->disc2 > 0) {
                        if(str_contains(trim(number_format($data->disc2, 2)), '.00')) {
                            $data_decimal_disc2 = number_format($data->disc2);
                        } else {
                            $data_decimal_disc2 = number_format($data->disc2, 2);
                        }
                        $data_disc2 = '<span class="badge badge-light-danger fw-bolder fs-8">'.$data_decimal_disc2.'%</span>';
                    }

                    $data_harga_coret = '';
                    if($data->harga_satuan < $data->het) {
                        $data_harga_coret = '<del class="text-muted fw-bold fs-7">Rp. '.number_format($data->het).'</del>';
                    }

                    $data_card_detail .= '<div class="card card-flush mb-8">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex">
                                    <div class="symbol symbol-100px me-5">
                                        <img src="'.$image_url_part.'/'.trim($data->part_number).'.jpg"
                                            onerror="this.onerror=null; this.src='.$image_not_found_url_part.'"
                                            alt="'.trim($data->part_number).'">
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="text-dark fw-bolder fs-6">'.trim($data->part_number).'</span>
                                        <span class="text-dark d-block fw-bold">'.trim($data->nama_part).'</span>
                                        '.$data_kode_tpc.'
                                        <span class="badge badge-light-info fs-8 fw-bolder mt-1">'.$data->umur_faktur.'-Hari</span>
                                        <div class="row d-flex flex-column mt-4">
                                            <div class="fw-bolder text-dark fs-5">Rp. '.number_format($data->harga_satuan).'</div>
                                            <div class="d-flex align-items-center mt-1">
                                                '.$data_disc1.$data_disc2.'
                                            </div>
                                            '.$data_harga_coret.'
                                        </div>
                                        <div class="row d-flex flex-column mt-4">
                                            <span class="text-muted d-block fw-bold">Jumlah Order :</span>
                                            <span class="text-dark fw-bold fs-5">'.number_format($data->jml_order).'
                                                <span class="text-muted fw-bold ms-2">PCS</span>
                                            </span>
                                        </div>
                                        <div class="row d-flex flex-column mt-4">
                                            <span class="text-muted d-block fw-bold">Total :</span>
                                            <span class="text-danger fw-bolder fs-5">Rp. '.number_format($data->jumlah).'</span>
                                        </div>
                                        <div class="row d-flex flex-column mt-4">
                                            '.$data_keterangan_bo.$data_keterangan_harga.$data_keterangan_disc_produk.$data_keterangan_disc_2x.$data_keterangan_harga_tpc20.'
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="separator my-5"></div>
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <button class="btn btn-sm btn-primary" id="btnEditPartCart" type="button" data-bs-toggle="modal" data-bs-target="#modalCartDetail" data-kode="'.trim($data->part_number).'">
                                        <span class="svg-icon svg-icon-muted svg-icon-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"/>
                                                <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"/>
                                            </svg>
                                        </span> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger" id="btnDeletePartCart" type="button" data-kode="'.trim($data->part_number).'">
                                        <span class="svg-icon svg-icon-muted svg-icon-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3" d="M6.7 19.4L5.3 18C4.9 17.6 4.9 17 5.3 16.6L16.6 5.3C17 4.9 17.6 4.9 18 5.3L19.4 6.7C19.8 7.1 19.8 7.7 19.4 8.1L8.1 19.4C7.8 19.8 7.1 19.8 6.7 19.4Z" fill="currentColor"/>
                                                <path d="M19.5 18L18.1 19.4C17.7 19.8 17.1 19.8 16.7 19.4L5.40001 8.1C5.00001 7.7 5.00001 7.1 5.40001 6.7L6.80001 5.3C7.20001 4.9 7.80001 4.9 8.20001 5.3L19.5 16.6C19.9 16.9 19.9 17.6 19.5 18Z" fill="currentColor"/>
                                            </svg>
                                        </span> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>';
                }

                if((double)$jumlah_data_detail <= 0) {
                    $data_card_detail = ' <div class="card card-flush">
                        <div class="card-body">
                            <center>
                                <span class="text-muted d-block fw-bold fs-4">Detail cart masih kosong</span>
                            </center>
                        </div>
                    </div>';
                }

                $data_card_detail .= '<div class="card card-flush">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <span class="text-muted d-block fw-bolder fs-6">Grand Total</span>
                            </div>
                            <div class="col-6">
                                <span class="text-dark d-block fw-bolder fs-6 text-end">Rp. '.$data_grand_total.'</span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <span class="text-muted d-block fw-bolder fs-6">Estimasi Terlayani</span>
                            </div>
                            <div class="col-6">
                                <span class="text-danger d-block fw-bolder fs-6 text-end">Rp. '.$data_estimasi_total.'</span>
                            </div>
                        </div>
                    </div>
                </div>';

                return [ 'status' => 1, 'message' => 'success', 'data' => $data_card_detail ];
            } else {
                $data_table_detail = '';
                $jumlah_data_detail = 0;
                $data_grand_total = (double)$data->grand_total;
                $data_estimasi_total = (double)$data->estimasi_total;

                foreach($data->detail as $data) {
                    $jumlah_data_detail = (double)$jumlah_data_detail + 1;

                    $image_url_part = config('constants.app.app_images_url');
                    $image_not_found_url_part = "'".asset('assets/images/background/part_image_not_found.png')."'";

                    $data_kode_tpc = '';
                    if($data->kode_tpc == 14) {
                        $data_kode_tpc = '<span class="badge badge-light-primary fs-8 fw-bolder mt-1">TPC '.$data->kode_tpc.'</span>';
                    } else {
                        $data_kode_tpc = '<span class="badge badge-light-danger fs-8 fw-bolder mt-1">TPC '.$data->kode_tpc.'</span>';
                    }

                    $data_keterangan_bo = '';
                    if(trim($data->keterangan_bo) != '') {
                        $data_keterangan_bo = '<div class="flex-grow-1">
                            <span class="badge badge-info fs-8 fw-bolder mt-1">'.trim($data->keterangan_bo).'</span>
                        </div>';
                    }

                    $data_keterangan_harga = '';
                    if(trim($data->keterangan_harga) != '') {
                        $data_keterangan_harga = '<div class="flex-grow-1">
                            <span class="badge badge-danger fs-8 fw-bolder mt-1">'.trim($data->keterangan_harga).'</span>
                        </div>';
                    }

                    $data_keterangan_disc_produk = '';
                    if(trim($data->keterangan_disc_produk) != '') {
                        $data_keterangan_disc_produk = '<div class="flex-grow-1">
                            <span class="badge badge-light-danger fs-8 fw-bolder mt-1">'.trim($data->keterangan_disc_produk).'</span>
                        </div>';
                    }

                    $data_keterangan_harga_tpc20 = '';
                    if($data->keterangan_harga_tpc20 != '') {
                        $data_keterangan_harga_tpc20 = '<div class="flex-grow-1">
                            <span class="badge badge-light-info fs-8 fw-bolder mt-1">'.trim($data->keterangan_harga_tpc20).'</span>
                        </div>';
                    }

                    $data_disc1 = '';
                    $data_decimal_disc1 = '';
                    if($data->disc1 > 0) {
                        if(str_contains(trim(number_format($data->disc1, 2)), '.00')) {
                            $data_decimal_disc1 = number_format($data->disc1);
                        } else {
                            $data_decimal_disc1 = number_format($data->disc1, 2);
                        }
                        $data_disc1 = '<span class="badge badge-light-danger fw-bolder fs-8">'.$data_decimal_disc1.'%</span>';
                    }

                    $data_disc2 = '';
                    $data_decimal_disc2 = '';
                    if($data->disc2 > 0) {
                        if(str_contains(trim(number_format($data->disc2, 2)), '.00')) {
                            $data_decimal_disc2 = number_format($data->disc2);
                        } else {
                            $data_decimal_disc2 = number_format($data->disc2, 2);
                        }
                        $data_disc2 = '<span class="badge badge-light-danger fw-bolder fs-8">'.$data_decimal_disc2.'%</span>';
                    }

                    $data_harga_coret = '';
                    if($data->harga_satuan < $data->het) {
                        $data_harga_coret = '<del class="text-muted fw-bold fs-7">Rp. '.number_format($data->het).'</del>';
                    }

                    $data_table_detail .= '<tr>
                        <td style="text-align:left;vertical-align:top;">
                            <button class="btn btn-icon btn-bg-primary btn-sm me-1 mt-2" id="btnEditPartCart" type="button" data-bs-toggle="modal" data-bs-target="#modalCartDetail" data-kode="'.trim($data->part_number).'">
                                <span class="svg-icon svg-icon-white svg-icon-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"/>
                                        <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"/>
                                    </svg>
                                </span>
                            </button>
                            <button class="btn btn-icon btn-bg-danger btn-sm me-1 mt-2" id="btnDeletePartCart" type="button" data-kode="'.trim($data->part_number).'">
                                <span class="svg-icon svg-icon-white svg-icon-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M6.7 19.4L5.3 18C4.9 17.6 4.9 17 5.3 16.6L16.6 5.3C17 4.9 17.6 4.9 18 5.3L19.4 6.7C19.8 7.1 19.8 7.7 19.4 8.1L8.1 19.4C7.8 19.8 7.1 19.8 6.7 19.4Z" fill="currentColor"/>
                                        <path d="M19.5 18L18.1 19.4C17.7 19.8 17.1 19.8 16.7 19.4L5.40001 8.1C5.00001 7.7 5.00001 7.1 5.40001 6.7L6.80001 5.3C7.20001 4.9 7.80001 4.9 8.20001 5.3L19.5 16.6C19.9 16.9 19.9 17.6 19.5 18Z" fill="currentColor"/>
                                    </svg>
                                </span>
                            </button>
                        </td>
                        <td style="text-align:left;vertical-align:top;">
                            <div class="d-flex">
                                <div class="symbol symbol-50px me-5">
                                    <img src="'.$image_url_part.'/'.trim($data->part_number).'.jpg"
                                        onerror="this.onerror=null; this.src='.$image_not_found_url_part.'"
                                        alt="'.trim($data->part_number).'">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-start flex-column">
                                        <span class="text-dark fw-bolder fs-6">'.trim($data->part_number).'</span>
                                        <span class="text-dark fw-bold fs-7">'.trim($data->nama_part).'</span>
                                        <div class="flex-grow-1 mb-2">
                                            '.$data_kode_tpc.'
                                            <span class="badge badge-light-info fs-8 fw-bolder mt-1">'.$data->umur_faktur.'-Hari</span>
                                        </div>
                                        '.$data_keterangan_bo.$data_keterangan_harga.$data_keterangan_disc_produk.$data_keterangan_harga_tpc20.'
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="text-align:center;vertical-align:top;">
                            <div class="text-dark fw-bolder fs-6">Rp. '.number_format($data->harga_satuan).'</div>
                            '.$data_disc1.$data_disc2.'
                            <div class="row mt-1">
                                '.$data_harga_coret.'
                            </div>
                        </td>
                        <td class="fw-bolder text-dark fs-6 text-end" style="text-align:left;vertical-align:top;">'.number_format($data->jml_order).'</td>
                        <td class="fw-bolder text-dark fs-6 text-end" style="text-align:left;vertical-align:top;">Rp. '.number_format($data->jumlah).'</td>
                    </tr>';
                }

                if((double)$jumlah_data_detail <= 0) {
                    $data_table_detail = '<tr>
                            <td>-</td>
                            <td><span class="text-muted fw-bold text-muted fs-7">DETAIL CART MASIH KOSONG</td>
                        </tr>';
                }
                $data_table_detail .= '<tr>
                        <td colspan="4" class="fw-bolder text-muted text-end fs-6">Grand Total</td>
                        <td class="fw-bolder text-dark text-end fs-6">Rp. '.number_format($data_grand_total).'</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="fw-bolder text-muted text-end fs-6">Estimasi Terlayani</td>
                        <td class="fw-bolder text-danger text-end fs-6">Rp. '.number_format($data_estimasi_total).'</td>
                    </tr>';
            }

            $data_table_header = '<div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bolder text-muted">
                                <th class="min-w-80px">#</th>
                                <th class="min-w-150px">PART NUMBER</th>
                                <th class="min-w-100px text-center">HARGA</th>
                                <th class="min-w-60px text-end">ORDER</th>
                                <th class="min-w-100px text-end">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>'.$data_table_detail.'</tbody>
                    </table>
                </div>';

            return [ 'status' => 1, 'message' => 'success', 'data' => $data_table_header ];
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function cartInformation(Request $request) {
        $responseApi = ApiService::CartInformation(strtoupper(trim($request->session()->get('app_user_id'))),
                                    strtoupper(trim($request->session()->get('app_user_role_id'))),
                                    strtoupper(trim($request->session()->get('app_user_company_id'))));
        $messageApi = json_decode($responseApi)->message;
        $statusApi = json_decode($responseApi)->status;

        $view_total_estimate_cart = '';
        $view_total_item_cart = '';
        if($statusApi == 1) {
            if(strtoupper(trim($messageApi)) == 'SUCCESS') {
                $data = json_decode($responseApi)->data;
                $view_total_estimate_cart = '<div class="toolbar" id="kt_toolbar">
                        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
                            <div class="d-flex align-items-center gap-2 gap-lg-3">
                                <div class="m-0">
                                    <h1 class="d-flex text-dark fw-bolder fs-3 align-items-center my-1">'.strtoupper(trim($data->kode_dealer)).'
                                        <span class="h-20px border-1 border-gray-200 border-start ms-3 mx-2 me-1"></span>
                                        <span class="text-muted fs-6 fw-bold ms-2">#Estimasi Cart:</span>
                                        <span class="text-danger fs-6 fw-bolder ms-4">Rp. '.number_format($data->total).'</span>
                                    </h1>
                                </div>
                                <a href="'.url('/orders/cart').'" class="btn btn-sm btn-success ms-2" role="button">Lihat Cart</a>
                            </div>
                        </div>
                    </div>';

                $view_total_item_cart = '<span class="position-absolute top-0 start-100 translate-middle badge badge-circle badge-danger mt-2">'.number_format($data->jumlah_item).'</span>';
            } else {
                if(strtoupper(trim($request->get('app_user_role_id'))) != 'D_H3') {
                    $view_total_estimate_cart = '<div class="toolbar" id="kt_toolbar">
                        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
                            <div class="d-flex align-items-center gap-2 gap-lg-3">
                                <button id="btnSalesmanDealerIndex" name="btnSalesmanDealerIndex" type="button" class="btn btn-sm btn-success ms-2">Pilih Kode Dealer</button>
                            </div>
                        </div>
                    </div>';
                }
            }

            $data = [ 'status' => 1, 'view_total_estimate_cart' => $view_total_estimate_cart, 'view_total_item_cart' => $view_total_item_cart ];
        } else {
            $data = [ 'status' => 0, 'view_total_estimate_cart' => $view_total_estimate_cart, 'view_total_item_cart' => $view_total_item_cart ];
        }
        return $data;
    }

    public function cartResetData(Request $request) {
        $responseApi = ApiService::CartReset(strtoupper(trim($request->session()->get('app_user_id'))),
                                    strtoupper(trim($request->session()->get('app_user_company_id'))));
        return json_decode($responseApi, true);
    }

    public function cartEditHeader(Request $request) {
        $responseApi = ApiService::CartHeader(strtoupper(trim($request->session()->get('app_user_id'))),
                                    strtoupper(trim($request->session()->get('app_user_role_id'))),
                                    strtoupper(trim($request->session()->get('app_user_company_id'))));

        return json_decode($responseApi, true);
    }

    public function cartSimpanDraft(Request $request) {
        $kode_sales = '';
        $kode_dealer = '';

        if($request->get('form') == 'index') {
            $kode_sales = $request->get('salesmanIndex');
            $kode_dealer = $request->get('dealerIndex');
        } else {
            $kode_sales = $request->get('salesman');
            $kode_dealer = $request->get('dealer');
        }

        $responseApi = ApiService::CartSimpanDraft($kode_sales, $kode_dealer, $request->get('back_order'),
                            $request->get('keterangan'), strtoupper(trim($request->session()->get('app_user_id'))),
                            strtoupper(trim($request->session()->get('app_user_role_id'))),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi = json_decode($responseApi)->message;

        if($statusApi == 1) {
            return redirect()->back();
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function cartEditDetail(Request $request) {
        $responseApi = ApiService::CartEditPart($request->get('part_number'), strtoupper(trim($request->session()->get('app_user_id'))),
                                    strtoupper(trim($request->session()->get('app_user_role_id'))),
                                    strtoupper(trim($request->session()->get('app_user_company_id'))));

        return json_decode($responseApi, true);
    }

    public function cartSimpanPart(Request $request) {
        $responseApi = ApiService::CartSimpanPart($request->get('part_number'), $request->get('tpc'),
                                    str_replace(',', '', $request->get('jml_order')), str_replace(',', '', $request->get('harga')),
                                    $request->get('discount'), $request->get('discount_plus'), strtoupper(trim($request->session()->get('app_user_id'))),
                                    strtoupper(trim($request->session()->get('app_user_role_id'))),
                                    strtoupper(trim($request->session()->get('app_user_company_id'))));
        return json_decode($responseApi, true);
    }

    public function cartHapusPart(Request $request) {
        $responseApi = ApiService::CartDeletePart($request->get('part_number'), strtoupper(trim($request->session()->get('app_user_id'))),
                                    strtoupper(trim($request->session()->get('app_user_company_id'))));
        return json_decode($responseApi, true);
    }

    public function cartImportExcel(Request $request) {
        $validate = Validator::make($request->all(), [
            'salesman'  => 'required|string',
            'dealer'    => 'required|string',
        ]);

        if($validate->fails()) {
            return [ 'status' => 0, 'message' => 'Pilih data salesman dan dealer terlebih dahulu' ];
        }

        $validate = Validator::make($request->all(), [
            'selectFileExcel'   => 'required|max:50000|mimes:xlsx,xls'
        ]);

        if($validate->fails()) {
            return [ 'status' => 0, 'message' => 'File yang anda pilih bukan format excel' ];
        }

        if(!empty($request->file('selectFileExcel'))) {
            $fileName = str_replace('.', '', str_replace('_', '', trim($request->session()->get('app_user_company_id')).trim($request->session()->get('app_user_role_id')).trim($request->session()->get('app_user_id')).date('YmdHis')));
            $path = $request->file('selectFileExcel')->getRealPath();
            $data = Excel::toCollection(new ExcelCartController, $path);

            if($data->count() > 0) {
                foreach($data->toArray() as $value) {
                    foreach($value as $row) {
                        $kode_tpc = 'AUTO';
                        $harga_part = 0;
                        $disc_part = 0;
                        $disc_plus_part = 0;

                        if(strtoupper(trim($request->session()->get('app_user_role_id'))) != 'D_H3') {
                            $kode_tpc = ((double)$row['harga'] > 0) ? '20' : '14';
                            $harga_part = ((double)$row['harga'] > 0) ? (double)$row['harga'] : 0;
                            $disc_part = ((double)$row['harga'] > 0) ? 0 : (((double)$row['disc'] > 0) ? (double)$row['disc'] : 0);
                            $disc_plus_part = ((double)$row['harga'] > 0) ? 0 : (((double)$row['disc_plus'] > 0) ? (double)$row['disc_plus'] : 0);
                        }

                        $data_excel[] = array(
                            'kd_key'        => strtoupper(trim($request->session()->get('app_user_id'))),
                            'kd_cart'       => strtoupper(trim($request->session()->get('app_user_id'))),
                            'kd_sales'      => strtoupper($request->get('salesman')),
                            'kd_dealer'     => strtoupper($request->get('dealer')),
                            'pn'            => (String)$row['part_number'],
                            'kd_part'       => '',
                            'kd_lokasi'     => '',
                            'kd_rak'        => '',
                            'jml_order'     => (double)$row['order'],
                            'harga'         => (double)$harga_part,
                            'disc1'         => (double)$disc_part,
                            'disc2'         => (double)$disc_plus_part,
                            'jumlah'        => 0,
                            'kd_tpc'        => $kode_tpc,
                            'hrg_satuan'    => 0,
                            'het'           => 0,
                            'hrg_pokok'     => 0,
                            'umur_faktur'   => 0,
                            'file_name'     => strtoupper($fileName),
                            'perbandingan'  => strtoupper($request->get('jenis_part_number')),
                            'keterangan'    => '',
                            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
                            'usertime'      => strtoupper(trim($request->session()->get('app_user_id'))),
                        );
                    }
                }

                $responseApi = ApiService::cartImportExcel($fileName, $data_excel, strtoupper(trim($request->session()->get('app_user_id'))),
                                    strtoupper(trim($request->session()->get('app_user_role_id'))),
                                    strtoupper(trim($request->session()->get('app_user_company_id'))));
                $statusApi = json_decode($responseApi)->status;
                $messageApi =  json_decode($responseApi)->message;

                if($statusApi == 1) {
                    $responseApi = ApiService::CartProsesExcel($fileName, strtoupper($request->get('jenis_part_number')),
                                        strtoupper(trim($request->session()->get('app_user_id'))),
                                        strtoupper(trim($request->session()->get('app_user_role_id'))),
                                        strtoupper(trim($request->session()->get('app_user_company_id'))));
                    $statusApi = json_decode($responseApi)->status;
                    $messageApi =  json_decode($responseApi)->message;

                    if($statusApi == 1) {
                        $dataApi = json_decode($responseApi)->data;

                        if($messageApi == 'SUCCESS_WITH_MESSAGE') {
                            $tableDetail = '';
                            foreach($dataApi as $data) {
                                $tableDetail .= '<tr class="fw-bold text-dark fs-7">
                                    <td class="w-150px">'.trim($data->part_number).'</td>
                                    <td class="min-w-200px">'.trim($data->keterangan).'</td>
                                </tr>';
                            }
                            $tableHeader = '<div class="table-responsive">
                                <table id="tableImportExcelResultCart" class="table table-row-dashed table-row-gray-300">
                                    <thead>
                                        <tr class="fw-bolder text-dark">
                                            <th class="min-w-150px">Part Number</th>
                                            <th class="min-w-200px">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>'.$tableDetail.'</tbody>
                                </table>
                            </div>';


                            return [ 'status' => 1, 'message' => $messageApi, 'data' => $tableHeader ];
                        } else {
                            return [ 'status' => 1, 'message' => $messageApi, 'data' => $dataApi ];
                        }

                    } else {
                        return [ 'status' => 0, 'message' => $messageApi ];
                    }
                } else {
                    return [ 'status' => 0, 'message' => $messageApi ];
                }
            } else {
                return [ 'status' => 0, 'message' => 'Row excel tidak ditemukan' ];
            }
        } else {
            return [ 'status' => 0, 'message' => 'Pilih file excel terlebih dahulu' ];
        }
    }

    public function cartCheckOutCekAturanHarga(Request $request) {
        $responseApi = ApiService::cartCheckOutCekAturanHarga(strtoupper(trim($request->session()->get('app_user_id'))),
                        strtoupper(trim($request->session()->get('app_user_company_id'))));
        return json_decode($responseApi, true);
    }

    public function cartCheckOut(Request $request) {
        $responseApi = ApiService::CartCheckOutProses($request->get('password'), $request->get('password_confirm'),
                        strtoupper(trim($request->session()->get('app_user_id'))),
                        strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            return redirect()->route('orders.cart-check-out-result', [ 'kode' => $data ])->with('success', 'Data Berhasil Diproses');
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function cartCheckOutResult(Request $request) {
        $responseApi = ApiService::CartCheckOutResult(strtoupper(trim($request->session()->get('app_user_role_id'))), json_encode($request->get('kode')),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            ApiService::CartHapusTemporary(strtoupper(trim($request->session()->get('app_user_id'))),
                        strtoupper(trim($request->session()->get('app_user_role_id'))),
                        strtoupper(trim($request->session()->get('app_user_company_id'))));

            return view ('layouts.orders.cart.cartresult', [
                'title_menu'    => 'Cart',
                'faktur'        => $data->faktur,
                'bo'            => $data->bo,
                'pof'           => $data->pof,
            ])->with('success', 'Data Berhasil Di Proses');;
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }
}
