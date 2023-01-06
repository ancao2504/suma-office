<?php

namespace App\Http\Controllers\App\Orders\PurchaseOrderForm;

use App\Http\Controllers\Controller;
use App\Helpers\ApiService;

use Illuminate\Http\Request;
use Jenssegers\Agent\Agent as Agent;

class PurchaseOrderFormDetailController extends Controller
{
    public function purchaseOrderFormDetailDaftar(Request $request) {
        $responseApi = ApiService::PurchaseOrderFormDetailDaftar(trim($request->get('nomor_pof')),
                strtoupper(trim($request->session()->get('app_user_id'))),
                strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            $table_header = '';
            $table_detail = '';
            $jumlah_data = 0;

            $Agent = new Agent();

            if($Agent->isMobile()) {
                $image_url_part = config('constants.app.app_images_url');
                $image_not_found_url_part = "'" . asset('assets/images/background/part_image_not_found.png') . "'";

                foreach($data->data_pof_detail as $data_detail) {
                    $jumlah_data = (double)$jumlah_data + 1;

                    if($data_detail->terlayani > 0) {
                        $table_detail .= '<div class="card card-flush" id="viewDetailPofTerlayani" type="button" data-kode="'.strtoupper(trim($data_detail->part_number)).'">';
                    } else {
                        $table_detail .= '<div class="card card-flush">';
                    }

                    $table_detail .= '<div class="card-body">
                                <div class="d-flex">
                                <span class="symbol symbol-100px me-5">
                                <span class="symbol-label" style="background-image:url('.$image_url_part.'/'.strtoupper(trim($data_detail->part_number)).'.jpg'.'), url('.$image_not_found_url_part.');"></span>
                            </span>
                            <div class="flex-grow-1">
                                <div class="row">
                                    <span class="fs-6 text-dark fw-bolder">'.strtoupper(trim($data_detail->part_number)).'</span>
                                    <span class="fs-7 text-muted fw-bold descriptionpart">'.trim($data_detail->nama_part).'</span>
                                    <span class="fs-5 text-dark fw-bolder mt-4">Rp. '.number_format($data_detail->harga).'</span>';

                    if((double)$data_detail->harga != (double)$data_detail->het) {
                        $table_detail .= '<div class="d-flex align-items-center">';

                        if((double)$data_detail->disc_detail > 0) {
                            $table_detail .= '<div class="badge badge-light-danger fw-bolder fs-7 p-1">'.number_format($data_detail->disc_detail, 2).'%</div>';
                        }

                        $table_detail .= '<del class="text-gray-600 fw-bolder fs-7 ms-2">Rp. '.number_format($data_detail->het).'</del>
                            </div>';
                    }

                    $table_detail .= '<div class="row mt-4">
                                        <div class="col-6">
                                            <span class="text-muted d-block fw-bold">Order:</span>
                                            <div class="align-items-center">
                                                <span class="fs-6 fw-bolder text-dark">'.number_format($data_detail->jml_order).'</span>
                                                <span class="fs-7 fw-bolder text-gray-600 ms-2">PCS</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted d-block fw-bold">Terlayani:</span>
                                            <div class="align-items-center">';

                    if((double)$data_detail->terlayani > 0) {
                        if((double)$data_detail->terlayani >= (double)$data_detail->jml_order) {
                            $table_detail .= '<span class="fs-6 fw-bolder text-success">'.number_format($data_detail->terlayani).'</span>';
                        } else {
                            $table_detail .= '<span class="fs-6 fw-bolder text-dark">'.number_format($data_detail->terlayani).'</span>';
                        }
                    } else {
                        $table_detail .= '<span class="fs-6 fw-bolder text-danger">'.number_format($data_detail->terlayani).'</span>';
                    }

                    $table_detail .= '<span class="fs-7 fw-bolder text-gray-600 ms-2">PCS</span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="fs-5 text-danger fw-boldest mt-6">Rp. '.number_format($data_detail->total_detail).'</span>';

                    if((int)$data->approve == 0 && (int)$data->status_faktur == 0) {
                        if(trim($data_detail->keterangan_bo) != '') {
                            $table_detail .= '<div class="row mt-2">
                                    <div class="d-flex">
                                        <span class="fs-8 fw-boldest badge badge-light-primary mt-1 animation-blink">'.trim($data_detail->keterangan_bo).'</span>
                                    </div>
                                </div>';
                        }

                        if(trim($data_detail->keterangan_disc_produk) != '') {
                            $table_detail .= '<div class="row mt-2">
                                    <div class="d-flex">
                                        <span class="fs-8 fw-boldest badge badge-light-success mt-1 animation-blink">'.trim($data_detail->keterangan_disc_produk).'</span>
                                    </div>
                                </div>';
                        }

                        if(trim($data_detail->keterangan_disc_produk) != '') {
                            $table_detail .= '<div class="row mt-2">
                                    <div class="d-flex">
                                        <span class="fs-8 fw-boldest badge badge-light-warning mt-1 animation-blink">'.trim($data_detail->keterangan_disc_produk).'</span>
                                    </div>
                                </div>';
                        }

                        if(trim($data_detail->keterangan_penjualan_rugi) != '') {
                            $table_detail .= '<div class="row mt-2">
                                    <div class="d-flex">
                                        <span class="fs-8 fw-boldest badge badge-light-danger mt-1 animation-blink">'.trim($data_detail->keterangan_penjualan_rugi).'</span>
                                    </div>
                                </div>';
                        }

                        if(trim($data_detail->keterangan_harga) != '') {
                            $table_detail .= '<div class="row mt-2">
                                    <div class="d-flex">
                                        <span class="fs-8 fw-boldest badge badge-light-info mt-1 animation-blink">'.trim($data_detail->keterangan_harga).'</span>
                                    </div>
                                </div>';
                        }

                        if(trim($data_detail->keterangan_disc_tpc20) != '') {
                            $table_detail .= '<div class="row mt-2">
                                    <div class="d-flex">
                                        <span class="fs-8 fw-boldest badge badge-light-danger mt-1 animation-blink">'.trim($data_detail->keterangan_disc_tpc20).'</span>
                                    </div>
                                </div>';
                        }

                        if(trim($data_detail->keterangan_disc_2x) != '') {
                            $table_detail .= '<div class="row mt-2">
                                    <div class="d-flex">
                                        <span class="fs-8 fw-boldest badge badge-light-danger mt-1 animation-blink">'.trim($data_detail->keterangan_disc_2x).'</span>
                                    </div>
                                </div>';
                        }
                    }

                    if((int)$data->approve == 0 && (int)$data->status_faktur == 0) {
                        $table_detail .= '<div class="separator my-5"></div>
                            <div class="align-items-center">
                                <button class="btn btn-link btn-color-primary btn-active-color-primary me-12" id="btnEditPofPart" type="button" data-kode="'.strtoupper($data_detail->part_number).'">
                                    <span class="svg-icon svg-icon-muted svg-icon-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                        </svg>
                                    </span> Edit
                                </button>
                                <button class="btn btn-link btn-color-danger btn-active-color-danger" id="btnDeletePofPart" type="button" data-kode="'.strtoupper($data_detail->part_number).'">
                                    <span class="svg-icon svg-icon-muted svg-icon-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M6.7 19.4L5.3 18C4.9 17.6 4.9 17 5.3 16.6L16.6 5.3C17 4.9 17.6 4.9 18 5.3L19.4 6.7C19.8 7.1 19.8 7.7 19.4 8.1L8.1 19.4C7.8 19.8 7.1 19.8 6.7 19.4Z" fill="currentColor"></path>
                                            <path d="M19.5 18L18.1 19.4C17.7 19.8 17.1 19.8 16.7 19.4L5.40001 8.1C5.00001 7.7 5.00001 7.1 5.40001 6.7L6.80001 5.3C7.20001 4.9 7.80001 4.9 8.20001 5.3L19.5 16.6C19.9 16.9 19.9 17.6 19.5 18Z" fill="currentColor"></path>
                                        </svg>
                                    </span> Hapus
                                </button>
                            </div>';
                    }
                    $table_detail .= '</div>
                            </div>
                        </div>
                    </div>
                </div>';
                }

                $table_detail .= '<div class="card card-flush mt-6">
                        <div class="card-body">
                            <div class="row">
                                <div class="d-flex flex-stack mb-3">
                                    <div class="fw-boldest pe-10 text-gray-600 fs-7">SUBTOTAL:</div>
                                    <div class="text-end fw-bolder fs-6 text-gray-800">Rp. '.number_format($data->sub_total).'</div>
                                </div>
                                <div class="d-flex flex-stack mb-3">
                                    <div class="fw-boldest pe-10 text-gray-600 fs-7">DISCOUNT (%):</div>
                                    <div class="text-end fw-bolder fs-6 text-gray-800">'.number_format($data->disc_header, 2).' % / Rp. '.number_format(((double)$data->sub_total * (double)$data->disc_header) / 100).'</div>
                                </div>
                                <div class="d-flex flex-stack mb-3">
                                    <div class="fw-boldest pe-10 text-gray-600 fs-7">TOTAL:</div>
                                    <div class="text-end fw-boldest fs-6 text-danger">Rp. '.number_format($data->grand_total).'</div>
                                </div>
                            </div>
                        </div>
                    </div>';

                if((double)$jumlah_data == 0) {
                    $table_detail = '<div class="card card-flush">
                            <div class="card-body">
                                <div class="fw-boldest fs-6 text-gray-600 text-hover-primary text-center p-20">- TIDAK ADA DATA YANG DITAMPILKAN -</div>
                            </div>
                        </div>';
                }

                return ['status' => 1, 'message' => 'success', 'data' => $table_detail];
            } else {
                foreach($data->data_pof_detail as $data_detail) {
                    $jumlah_data = (double)$jumlah_data + 1;

                    $image_url_part = config('constants.app.app_images_url');
                    $image_not_found_url_part = "'" . asset('assets/images/background/part_image_not_found.png') . "'";

                    $table_detail .= '<tr>';

                    if((int)$data->status_faktur == 0 && (int)$data->approve == 0) {
                        $table_detail .= '<td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                            <button class="btn btn-icon btn-sm btn-light-primary mt-1" id="btnEditPofPart" type="button" data-kode="'.trim($data_detail->part_number).'">
                                <span class="svg-icon svg-icon-muted svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"/>
                                        <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"/>
                                    </svg>
                                </span>
                            </button>
                            <button class="btn btn-icon btn-sm btn-light-danger mt-1" id="btnDeletePofPart" type="button" data-kode="'.trim($data_detail->part_number).'">
                                <span class="svg-icon svg-icon-muted svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M6.7 19.4L5.3 18C4.9 17.6 4.9 17 5.3 16.6L16.6 5.3C17 4.9 17.6 4.9 18 5.3L19.4 6.7C19.8 7.1 19.8 7.7 19.4 8.1L8.1 19.4C7.8 19.8 7.1 19.8 6.7 19.4Z" fill="currentColor"/>
                                        <path d="M19.5 18L18.1 19.4C17.7 19.8 17.1 19.8 16.7 19.4L5.40001 8.1C5.00001 7.7 5.00001 7.1 5.40001 6.7L6.80001 5.3C7.20001 4.9 7.80001 4.9 8.20001 5.3L19.5 16.6C19.9 16.9 19.9 17.6 19.5 18Z" fill="currentColor"/>
                                    </svg>
                                </span>
                            </button>
                        </td>';
                    }

                    $table_detail .= '<td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                        <div class="d-flex">
                            <span class="symbol symbol-50px">
                                <img src="'.$image_url_part.'/'.trim($data_detail->part_number).'.jpg"
                                    onerror="this.onerror=null; this.src='.$image_not_found_url_part.'"
                                    alt="'.trim($data_detail->part_number).'">
                            </span>
                        <div class="ms-5">
                            <span class="fs-7 fw-bolder text-gray-800 d-inline-block descriptionpart">'.trim($data_detail->nama_part).'</span>
                            <div class="fs-7 fw-bolder text-muted">'.trim($data_detail->part_number).'</div>';

                    if((int)$data->approve == 0 && (int)$data->status_faktur == 0) {
                        if(trim($data_detail->keterangan_bo) != '') {
                            $table_detail .= '<div class="row mt-2">
                                    <div class="d-flex">
                                        <span class="fs-8 fw-boldest badge badge-light-primary mt-1 animation-blink">'.trim($data_detail->keterangan_bo).'</span>
                                    </div>
                                </div>';
                        }

                        if(trim($data_detail->keterangan_disc_produk) != '') {
                            $table_detail .= '<div class="row mt-2">
                                    <div class="d-flex">
                                        <span class="fs-8 fw-boldest badge badge-light-success mt-1 animation-blink">'.trim($data_detail->keterangan_disc_produk).'</span>
                                    </div>
                                </div>';
                        }

                        if(trim($data_detail->keterangan_disc_produk) != '') {
                            $table_detail .= '<div class="row mt-2">
                                    <div class="d-flex">
                                        <span class="fs-8 fw-boldest badge badge-light-warning mt-1 animation-blink">'.trim($data_detail->keterangan_disc_produk).'</span>
                                    </div>
                                </div>';
                        }

                        if(trim($data_detail->keterangan_penjualan_rugi) != '') {
                            $table_detail .= '<div class="row mt-2">
                                    <div class="d-flex">
                                        <span class="fs-8 fw-boldest badge badge-light-danger mt-1 animation-blink">'.trim($data_detail->keterangan_penjualan_rugi).'</span>
                                    </div>
                                </div>';
                        }

                        if(trim($data_detail->keterangan_harga) != '') {
                            $table_detail .= '<div class="row mt-2">
                                    <div class="d-flex">
                                        <span class="fs-8 fw-boldest badge badge-light-info mt-1 animation-blink">'.trim($data_detail->keterangan_harga).'</span>
                                    </div>
                                </div>';
                        }

                        if(trim($data_detail->keterangan_disc_tpc20) != '') {
                            $table_detail .= '<div class="row mt-2">
                                    <div class="d-flex">
                                        <span class="fs-8 fw-boldest badge badge-light-danger mt-1 animation-blink">'.trim($data_detail->keterangan_disc_tpc20).'</span>
                                    </div>
                                </div>';
                        }

                        if(trim($data_detail->keterangan_disc_2x) != '') {
                            $table_detail .= '<div class="row mt-2">
                                    <div class="d-flex">
                                        <span class="fs-8 fw-boldest badge badge-light-danger mt-1 animation-blink">'.trim($data_detail->keterangan_disc_2x).'</span>
                                    </div>
                                </div>';
                        }
                    }

                    $table_detail .= '</div>
                            </div>
                        </td>
                        <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                            <span class="fs-7 text-gray-800 fw-bolder">'.number_format($data_detail->jml_order).'</span>
                        </td>
                        <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">';

                    if((double)$data_detail->terlayani > 0) {
                        if((double)$data_detail->terlayani <= (double)$data_detail->jml_order) {
                            $table_detail .= '<a id="viewDetailPofTerlayani" type="button" data-kode="'.trim($data_detail->part_number).'"
                            class="text-success fw-bolder text-hover-info fs-7">'.number_format($data_detail->terlayani).'</a>';
                        } else {
                            $table_detail .= '<a id="viewDetailPofTerlayani" type="button" data-kode="'.trim($data_detail->part_number).'"
                            class="text-primary fw-bolder text-hover-info fs-7">'.number_format($data_detail->terlayani).'</a>';
                        }

                    } else {
                        $table_detail .= '<span class="fs-7 text-danger fw-bolder">'.number_format($data_detail->terlayani).'</span>';
                    }
                    $table_detail .= '</td>
                            <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                <span class="fs-7 text-gray-800 fw-bolder">'.number_format($data_detail->harga).'</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                <span class="fs-7 text-gray-800 fw-bolder">'.number_format($data_detail->disc_detail, 2).'</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                <span class="fs-7 text-gray-800 fw-bolder">'.number_format($data_detail->total_detail).'</span>
                            </td>
                        </tr>';
                }

                if((double)$jumlah_data == 0) {
                    $table_detail .= '<tr>
                            <td colspan="7" class="text-center p-20">
                                <div class="fw-boldest fs-6 text-gray-600 text-hover-primary">- TIDAK ADA DATA YANG DITAMPILKAN -</div>
                            </td>
                        </tr>';
                } else {
                    $table_detail .= '<tr>';

                    if((int)$data->status_faktur == 0 && (int)$data->approve == 0) {
                        $table_detail .= '<td colspan="2" class="ps-3 pe-3" style="text-align:center;vertical-align:center;">
                                <span class="fs-8 fw-boldest text-gray-500">TOTAL ITEM</span>
                            </td>';
                    } else {
                        $table_detail .= '<td colspan="1" class="ps-3 pe-3" style="text-align:center;vertical-align:center;">
                                <span class="fs-8 fw-boldest text-gray-500">TOTAL ITEM</span>
                            </td>';
                    }

                    $table_detail .= '<td colspan="1" class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                            <span class="fs-7 fw-bolder text-gray-800">'.number_format($data->total_order).'</span>
                        </td>
                        <td class="ps-3 pe-3" style="text-align:right;vertical-align:center;">';


                    if ((double)$data->total_order > (double)$data->total_terlayani) {
                        if((double)$data->total_terlayani > 0) {
                            $table_detail .= '<span class="fs-7 fw-bolder text-primary">'.number_format($data->total_terlayani).'</span>';
                        } else {
                            $table_detail .= '<span class="fs-7 fw-bolder text-danger">'.number_format($data->total_terlayani).'</span>';
                        }
                    } else {
                        $table_detail .= '<span class="fs-7 fw-bolder text-success">'.number_format($data->total_terlayani).'</span>';
                    }

                    $table_detail .= '</td>
                            <td class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                <span class="fs-8 fw-boldest text-gray-500">SUBTOTAL</span>
                            </td>
                            <td></td>
                            <td class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                <span class="fs-7 fw-bolder text-gray-800">'.number_format($data->sub_total).'</span>
                            </td>
                        </tr>
                        <tr>';

                    if((int)$data->status_faktur == 0 && (int)$data->approve == 0) {
                        $table_detail .= '<td colspan="5" class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                <button class="btn btn-sm btn-primary m-2" id="btnEditPofDiscount" type="button">Discount (%)</button>
                            </td>';
                    } else {
                        $table_detail .= '<td colspan="4" class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                <span class="fs-8 fw-boldest text-gray-500">DISCOUNT (%)</span>
                            </td>';
                    }

                    $table_detail .= '</td>
                            <td class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                <span class="fs-7 fw-bolder text-gray-800">'.number_format($data->disc_header, 2).'</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                <span class="fs-7 fw-bolder text-gray-800">'.number_format(($data->sub_total * $data->disc_header) / 100).'</span>
                            </td>
                        </tr>
                        <tr>';

                    if((int)$data->status_faktur == 0 && (int)$data->approve == 0) {
                        $table_detail .= '<td colspan="5" class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                <span class="fs-8 fw-boldest text-gray-500">GRAND TOTAL</span>
                            </td>';
                    } else {
                        $table_detail .= '<td colspan="4" class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                <span class="fs-8 fw-boldest text-gray-500">GRAND TOTAL</span>
                            </td>';
                    }

                    $table_detail .= '<td></td>
                                        <td class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                            <span class="fs-7 fw-bolder text-gray-800">'.number_format($data->grand_total).'</span>
                                        </td>
                                    </tr> ';
                }

                $table_header = '<div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0">
                        <thead class="border">
                            <tr class="fs-8 fw-boldest text-gray-500 text-uppercase gs-0">';

                if((int)$data->status_faktur == 0 && (int)$data->approve == 0) {
                    $table_header .= '<th class="w-100px text-center ps-3 pe-3">Action</th>';
                }

                $table_header .= '<th class="min-w-100px ps-3 pe-3 text-center">Part Number</th>
                                <th class="w-70px ps-3 pe-3 text-center">Order</th>
                                <th class="w-70px ps-3 pe-3 text-center">Terlayani</th>
                                <th class="w-100px ps-3 pe-3 text-center">Harga</th>
                                <th class="w-70px ps-3 pe-3 text-center">Disc</th>
                                <th class="w-100px ps-3 pe-3 text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody class="fw-bolder text-gray-600 border">'.$table_detail.'</tbody>
                    </table>
                </div>';
            }

            return ['status' => 1, 'message' => 'success', 'data' => $table_header];
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function purchaseOrderFormDetailEditPart(Request $request) {
        $responseApi = ApiService::PurchaseOrderFormDetailEditPart($request->get('nomor_pof'), $request->get('part_number'),
            strtoupper(trim($request->session()->get('app_user_id'))), strtoupper(trim($request->session()->get('app_user_company_id'))));

        return json_decode($responseApi, true);
    }

    public function PurchaseOrderFormDetailSimpanPart(Request $request) {
        $responseApi = ApiService::PurchaseOrderFormDetailSimpanPart($request->get('nomor_pof'), $request->get('part_number'),
            (double)str_replace(',', '', $request->get('jml_order')), (double)str_replace(',','', $request->get('harga')),
            (double)str_replace(',', '', $request->get('discount')), strtoupper(trim($request->session()->get('app_user_id'))),
            strtoupper(trim($request->session()->get('app_user_company_id'))));

        return json_decode($responseApi, true);
    }

    public function purchaseOrderFormDetailHapusPart(Request $request) {
        $responseApi = ApiService::PurchaseOrderFormDetailHapusPart($request->get('nomor_pof'), $request->get('part_number'),
            strtoupper(trim($request->session()->get('app_user_id'))), strtoupper(trim($request->session()->get('app_user_company_id'))));

        return json_decode($responseApi, true);
    }
}
