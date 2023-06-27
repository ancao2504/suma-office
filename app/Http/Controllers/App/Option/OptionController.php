<?php

namespace App\Http\Controllers\App\Option;

use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use App\Helpers\App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;

class OptionController extends Controller
{
    public function optionCompany(Request $request)
    {
        $responseApi = Service::OptionCompany($request->get('search'), $request->get('page'),
                            $request->get('per_page'));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            $data_per_page = $data->per_page;
            $data_link_page = $data->links;
            $data_from_record = $data->from;
            $data_to_record = $data->to;
            $data_total_record = $data->total;

            $dataCompany = new LengthAwarePaginator(
                array_values($data->data),
                $data->total,
                $data->per_page,
                $data->current_page
            );

            $table_row = '';
            $table_pagination = '';

            foreach ($dataCompany as $data) {
                $table_row .= '<tr>
                        <td>'.$data->companyid.'</td>
                        <td>'.$data->keterangan.'</td>
                        <td class="text-center">
                            <button id="selectedOptionCompany" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                data-companyid="'.$data->companyid.'">
                                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                            </button>
                        </td>
                    </tr>';
            }

            foreach ($data_link_page as $data) {
                $page = '';
                if(!empty($data->url)) {
                    $pages = explode("?page=", $data->url);
                    $page = $pages[1];
                }

                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';

                if (Str::contains(trim($data->label), 'previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if (Str::contains(trim($data->label), 'next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if ($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                            <span class="page-link">'.trim($label).'</span></span>
                        </li>';
                } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                            <a href="#" class="page-link" data-page="'.trim($page).'">'.trim($label).'</a>
                        </li>';
                }
            }

            $table_per_page10 = '';
            $table_per_page25 = '';
            $table_per_page50 = '';
            $table_per_page100 = '';
            if ($data_per_page == '10') {
                $table_per_page10 = 'selected';
            } elseif ($data_per_page == '25') {
                $table_per_page25 = 'selected';
            } elseif ($data_per_page == '50') {
                $table_per_page50 = 'selected';
            } elseif ($data_per_page == '100') {
                $table_per_page100 = 'selected';
            }

            if($table_row == '') {
                $table_row .= '<tr>
                            <td colspan="3" class="pt-12 pb-12">
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

            $table_header = '<table id="tableSearchCompany" class="table align-middle table-row-bordered fs-6">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-100px">CompanyID</th>
                                <th class="min-w-150px">Keterangan</th>
                                <th class="min-w-50px text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fs-7 fw-bold text-gray-800">'.$table_row.'</tbody>
                    </table>
                    <div id="pageCompany" class="mt-5">
                        <div class="row">
                            <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                <div class="dataTables_length">
                                    <label>
                                        <select id="selectPerPageOptionCompany" name="selectPerPageOptionCompany" aria-controls="selectPerPageOption"
                                            class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                            <option value="10"'.$table_per_page10.'>10</option>
                                            <option value="25"'.$table_per_page25.'>25</option>
                                            <option value="50"'.$table_per_page50.'>50</option>
                                            <option value="100"'.$table_per_page100.'>100</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="dataTables_info" id="selectPerPageOptionCompanyInfo" role="status" aria-live="polite">Showing <span id="startRecordOptionCompany">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                            </div>
                            <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                <div class="dataTables_paginate paging_simple_numbers" id="paginationOptionCompany">
                                    <ul class="pagination">'.$table_pagination.'</ul>
                                </div>
                            </div>
                        </div>
                    </div>';

            return response()->json(['status' => 1, 'message' => 'success', 'data' => $table_header]);
        } else {
            return response()->json(['status' => 0, 'message' => $messageApi]);
        }
    }

    public function optionDealer(Request $request)
    {
        $responseApi = Service::optionDealer($request->get('search'), $request->get('page'), $request->get('per_page'),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            $data_per_page = $data->per_page;
            $data_link_page = $data->links;
            $data_from_record = $data->from;
            $data_to_record = $data->to;
            $data_total_record = $data->total;

            $dataDealerSales = new LengthAwarePaginator(
                array_values($data->data),
                $data->total,
                $data->per_page,
                $data->current_page
            );

            $table_row = '';
            $table_pagination = '';

            foreach ($dataDealerSales as $data) {
                $table_row .= '<tr>
                        <td>'.$data->kode_dealer.'</td>
                        <td>'.$data->nama_dealer.'</td>
                        <td class="text-center">
                            <button type="button" id="selectedOptionDealer" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                data-kode_dealer="'.$data->kode_dealer.'" data-nama_dealer="'.$data->nama_dealer.'">
                                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                            </button>
                        </td>
                    </tr>';
            }

            foreach ($data_link_page as $data) {
                $page = '';
                if(!empty($data->url)) {
                    $pages = explode("?page=", $data->url);
                    $page = $pages[1];
                }

                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';

                if (Str::contains(trim($data->label), 'previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if (Str::contains(trim($data->label), 'next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if ($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                            <span class="page-link">'.trim($label).'</span></span>
                        </li>';
                } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                            <a href="#" class="page-link" data-page="'.trim($page).'">'.trim($label).'</a>
                        </li>';
                }
            }

            $table_per_page10 = '';
            $table_per_page25 = '';
            $table_per_page50 = '';
            $table_per_page100 = '';
            if ($data_per_page == '10') {
                $table_per_page10 = 'selected';
            } elseif ($data_per_page == '25') {
                $table_per_page25 = 'selected';
            } elseif ($data_per_page == '50') {
                $table_per_page50 = 'selected';
            } elseif ($data_per_page == '100') {
                $table_per_page100 = 'selected';
            }

            if($table_row == '') {
                $table_row .= '<tr>
                            <td colspan="3" class="pt-12 pb-12">
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

            $table_header = '<table id="tableSearchDealer" class="table align-middle table-row-bordered fs-6">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-100px">Kode Dealer</th>
                                <th class="min-w-150px">Nama Dealer</th>
                                <th class="min-w-50px text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fs-7 fw-bold text-gray-800">'.$table_row.'</tbody>
                    </table>
                    <div id="pageDealer" class="mt-5">
                        <div class="row">
                            <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                <div class="dataTables_length">
                                    <label>
                                        <select id="selectPerPageOptionDealer" name="selectPerPageOptionDealer" aria-controls="selectPerPageOption"
                                            class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                            <option value="10"'.$table_per_page10.'>10</option>
                                            <option value="25"'.$table_per_page25.'>25</option>
                                            <option value="50"'.$table_per_page50.'>50</option>
                                            <option value="100"'.$table_per_page100.'>100</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="dataTables_info" id="selectPerPageOptionDealerInfo" role="status" aria-live="polite">Showing <span id="startRecordOptionDealer">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                            </div>
                            <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                <div class="dataTables_paginate paging_simple_numbers" id="paginationOptionDealer">
                                    <ul class="pagination">'.$table_pagination.'</ul>
                                </div>
                            </div>
                        </div>
                    </div>';

            return response()->json(['status' => 1, 'message' => 'success', 'data' => $table_header]);
        } else {
            return response()->json(['status' => 0, 'message' => $messageApi]);
        }
    }

    public function optionDealerSalesman(Request $request)
    {
        $responseApi = Service::optionDealerSalesman($request->get('salesman'), $request->get('search'),
                            $request->get('page'), $request->get('per_page'),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            $data_per_page = $data->per_page;
            $data_link_page = $data->links;
            $data_from_record = $data->from;
            $data_to_record = $data->to;
            $data_total_record = $data->total;

            $dataDealerSales = new LengthAwarePaginator(
                array_values($data->data),
                $data->total,
                $data->per_page,
                $data->current_page
            );

            $table_row = '';
            $table_pagination = '';

            foreach ($dataDealerSales as $data) {
                $table_row .= '<tr>
                        <td>'.$data->kode_dealer.'</td>
                        <td>'.$data->nama_dealer.'</td>
                        <td class="text-center">
                            <button id="selectedOptionDealerSalesman" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                data-kode_dealer="'.$data->kode_dealer.'" data-nama_dealer="'.$data->nama_dealer.'">
                                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                            </button>
                        </td>
                    </tr>';
            }


            foreach ($data_link_page as $data) {
                $page = '';
                if(!empty($data->url)) {
                    $pages = explode("?page=", $data->url);
                    $page = $pages[1];
                }

                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';

                if (Str::contains(trim($data->label), 'previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if (Str::contains(trim($data->label), 'next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if ($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                            <span class="page-link">'.trim($label).'</span></span>
                        </li>';
                } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                            <a href="#" class="page-link" data-page="'.trim($page).'">'.trim($label).'</a>
                        </li>';
                }
            }

            $table_per_page10 = '';
            $table_per_page25 = '';
            $table_per_page50 = '';
            $table_per_page100 = '';
            if ($data_per_page == '10') {
                $table_per_page10 = 'selected';
            } elseif ($data_per_page == '25') {
                $table_per_page25 = 'selected';
            } elseif ($data_per_page == '50') {
                $table_per_page50 = 'selected';
            } elseif ($data_per_page == '100') {
                $table_per_page100 = 'selected';
            }

            if($table_row == '') {
                $table_row .= '<tr>
                            <td colspan="3" class="pt-12 pb-12">
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

            $table_header = '<table id="tableSearchDealerSalesman" class="table align-middle table-row-bordered fs-6">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-100px">Kode Dealer</th>
                                <th class="min-w-150px">Nama Dealer</th>
                                <th class="min-w-50px text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fs-7 fw-bold text-gray-800">'.$table_row.'</tbody>
                    </table>
                    <div id="pageDealerSalesman" class="mt-5">
                        <div class="row">
                            <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                <div class="dataTables_length">
                                    <label>
                                        <select id="selectPerPageOptionDealerSalesman" name="selectPerPageOptionDealerSalesman" aria-controls="selectPerPageOptionSalesman"
                                            class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                            <option value="10"'.$table_per_page10.'>10</option>
                                            <option value="25"'.$table_per_page25.'>25</option>
                                            <option value="50"'.$table_per_page50.'>50</option>
                                            <option value="100"'.$table_per_page100.'>100</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="dataTables_info" id="selectPerPageOptionDealerSalesmanInfo" role="status" aria-live="polite">Showing <span id="startRecordOptionDealerSalesman">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                            </div>
                            <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                <div class="dataTables_paginate paging_simple_numbers" id="paginationOptionDealerSalesman">
                                    <ul class="pagination">'.$table_pagination.'</ul>
                                </div>
                            </div>
                        </div>
                    </div>';

            return response()->json(['status' => 1, 'message' => 'success', 'data' => $table_header]);
        } else {
            return response()->json(['status' => 0, 'message' => $messageApi]);
        }
    }

    public function optionKabupaten(Request $request)
    {
        $responseApi = Service::OptionKabupaten($request->get('search'), $request->get('page'),
                            $request->get('per_page'));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            $data_per_page = $data->per_page;
            $data_link_page = $data->links;
            $data_from_record = $data->from;
            $data_to_record = $data->to;
            $data_total_record = $data->total;

            $dataKabupaten = new LengthAwarePaginator(
                array_values($data->data),
                $data->total,
                $data->per_page,
                $data->current_page
            );

            $table_row = '';
            $table_pagination = '';

            foreach ($dataKabupaten as $data) {
                $table_row .= '<tr>
                        <td>'.$data->kode.'</td>
                        <td>'.$data->keterangan.'</td>
                        <td class="text-center">
                            <button id="selectedOptionKabupaten" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                data-kode_kabupaten="'.$data->kode.'">
                                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                            </button>
                        </td>
                    </tr>';
            }

            foreach ($data_link_page as $data) {
                $page = '';
                if(!empty($data->url)) {
                    $pages = explode("?page=", $data->url);
                    $page = $pages[1];
                }

                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';

                if (Str::contains(trim($data->label), 'previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if (Str::contains(trim($data->label), 'next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if ($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                            <span class="page-link">'.trim($label).'</span></span>
                        </li>';
                } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                            <a href="#" class="page-link" data-page="'.trim($page).'">'.trim($label).'</a>
                        </li>';
                }
            }

            $table_per_page10 = '';
            $table_per_page25 = '';
            $table_per_page50 = '';
            $table_per_page100 = '';
            if ($data_per_page == '10') {
                $table_per_page10 = 'selected';
            } elseif ($data_per_page == '25') {
                $table_per_page25 = 'selected';
            } elseif ($data_per_page == '50') {
                $table_per_page50 = 'selected';
            } elseif ($data_per_page == '100') {
                $table_per_page100 = 'selected';
            }

            if($table_row == '') {
                $table_row .= '<tr>
                            <td colspan="3" class="pt-12 pb-12">
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

            $table_header = '<table id="tableSearchKabupaten" class="table align-middle table-row-bordered fs-6">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-100px">Kode</th>
                                <th class="min-w-150px">Keterangan</th>
                                <th class="min-w-50px text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fs-7 fw-bold text-gray-800">'.$table_row.'</tbody>
                    </table>
                    <div id="pageKabupaten" class="mt-5">
                        <div class="row">
                            <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                <div class="dataTables_length">
                                    <label>
                                        <select id="selectPerPageOptionKabupaten" name="selectPerPageOptionKabupaten" aria-controls="selectPerPageOption"
                                            class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                            <option value="10"'.$table_per_page10.'>10</option>
                                            <option value="25"'.$table_per_page25.'>25</option>
                                            <option value="50"'.$table_per_page50.'>50</option>
                                            <option value="100"'.$table_per_page100.'>100</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="dataTables_info" id="selectPerPageOptionKabupatenInfo" role="status" aria-live="polite">Showing <span id="startRecordOptionKabupaten">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                            </div>
                            <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                <div class="dataTables_paginate paging_simple_numbers" id="paginationOptionKabupaten">
                                    <ul class="pagination">'.$table_pagination.'</ul>
                                </div>
                            </div>
                        </div>
                    </div>';

            return response()->json(['status' => 1, 'message' => 'success', 'data' => $table_header]);
        } else {
            return response()->json(['status' => 0, 'message' => $messageApi]);
        }
    }

    public function optionPartNumber(Request $request)
    {

        $Agent = new Agent();
        $responseApi = Service::optionPartNumber($request->get('search'),
                            $request->get('page'), $request->get('per_page'),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));

        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            $data_per_page = $data->per_page;
            $data_link_page = $data->links;
            $data_from_record = $data->from;
            $data_to_record = $data->to;
            $data_total_record = $data->total;

            $dataPartNumber = new LengthAwarePaginator(
                array_values($data->data),
                $data->total,
                $data->per_page,
                $data->current_page,
            );

            $table_row = '';
            $table_pagination = '';

            if ($Agent->isDesktop()) {
                foreach ($dataPartNumber as $data) {
                    $table_row .= '<tr>
                            <td>'.strtoupper(trim($data->part_number)).'</td>
                            <td>'.trim($data->description).'</td>
                            <td>'.strtoupper(trim($data->produk)).'</td>
                            <td class="text-end">'.number_format($data->het).'</td>
                            <td class="text-center">
                                <button id="selectedOptionPartNumber" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                    data-part_number="'.strtoupper(trim($data->part_number)).'" data-nama_part="'.trim($data->description).'"
                                    data-produk="'.strtoupper(trim($data->produk)).'" data-het="'.number_format($data->het).'">
                                    <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                                </button>
                            </td>
                        </tr>';
                }
            }

            foreach ($data_link_page as $data) {
                $page = '';
                if(!empty($data->url)) {
                    $pages = explode("?page=", $data->url);
                    $page = $pages[1];
                }

                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';

                if (Str::contains(trim($data->label), 'previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if (Str::contains(trim($data->label), 'next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if ($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                        <span class="page-link">'.trim($label).'</span></span>
                    </li>';
                } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                        <a href="#" class="page-link" data-page="'.trim($page).'">'.trim($label).'</a>
                    </li>';
                }
            }

            $table_per_page10 = '';
            $table_per_page25 = '';
            $table_per_page50 = '';
            $table_per_page100 = '';
            if ($data_per_page == '10') {
                $table_per_page10 = 'selected';
            } elseif ($data_per_page == '25') {
                $table_per_page25 = 'selected';
            } elseif ($data_per_page == '50') {
                $table_per_page50 = 'selected';
            } elseif ($data_per_page == '100') {
                $table_per_page100 = 'selected';
            }

            if($table_row == '') {
                $table_row .= '<tr>
                            <td colspan="5" class="pt-12 pb-12">
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

            // cek apakah isdektrop
        if ($Agent->isDesktop()) {
            $table_header = '<table id="tableSearchPartNumber" class="table align-middle table-row-bordered fs-6">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                            <th class="w-200px">Part Number</th>
                            <th class="min-w-100px">Nama Part</th>
                            <th class="w-50px">Produk</th>
                            <th class="w-50px text-end">HET</th>
                            <th class="min-w-50px text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="fs-7 fw-bold text-gray-800">'.$table_row.'</tbody>
                </table>';
        } else {
            $table_header = '';
            foreach ($dataPartNumber as $data) {
            $table_header .= '<div id="selectedOptionPartNumber" class="card mb-3 border-2 shadow-sm" style="max-width: 540px;" data-part_number="'.strtoupper(trim($data->part_number)).'" data-nama_part="'.trim($data->description).'"data-produk="'.strtoupper(trim($data->produk)).'" data-het="'.number_format($data->het).'">
                                <div class="row g-0">
                                    <div class="col-md-4 col-4">
                                    <img src="http://43.252.9.117/suma-images/'.strtoupper(trim($data->part_number)).'.jpg" class="img-fluid rounded-start max-width-540 overflow-hidden" alt="'.strtoupper(trim($data->part_number)).'" onerror="defaultImage(this)">
                                    </div>
                                    <div class="col-md-8 col-8">
                                        <div class="card-body">
                                        <span class="card-title fs-5 text-dark fw-bolder">'.strtoupper(trim($data->part_number)).'</span>
                                        <span class="card-title fs-6 text-muted fw-bold d-block">'.trim($data->description).'</span>
                                        <span class="card-text fs-4 text-dark fw-bolder mt-4 d-block">HET : Rp '.number_format($data->het).'</span>
                                        <span class="card-text fs-6 text-muted fw-bold d-block">Produk : '.strtoupper(trim($data->produk)).'</span>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            }
        }

            $table_header .= '<div id="pagePartNumber" class="mt-5">
                    <div class="row">
                        <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                            <div class="dataTables_length">
                                <label>
                                    <select id="selectPerPageOptionPartNumber" name="selectPerPageOptionPartNumber" aria-controls="selectPerPageOption"
                                        class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                        <option value="10"'.$table_per_page10.'>10</option>
                                        <option value="25"'.$table_per_page25.'>25</option>
                                        <option value="50"'.$table_per_page50.'>50</option>
                                        <option value="100"'.$table_per_page100.'>100</option>
                                    </select>
                                </label>
                            </div>
                            <div class="dataTables_info" id="selectPerPageOptionPartNumberInfo" role="status" aria-live="polite">Showing <span id="startRecordOptionPartNumber">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                        </div>
                        <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                            <div class="dataTables_paginate paging_simple_numbers" id="paginationOptionPartNumber">
                                <ul class="pagination">'.$table_pagination.'</ul>
                            </div>
                        </div>
                    </div>
                </div>';

            return response()->json(['status' => 1, 'message' => 'success', 'data' => $table_header]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function optionSalesman(Request $request)
    {
        $responseApi = Service::optionSalesman($request->get('search'), $request->get('page'), $request->get('per_page'),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            $data_per_page = $data->per_page;
            $data_link_page = $data->links;
            $data_from_record = $data->from;
            $data_to_record = $data->to;
            $data_total_record = $data->total;

            $dataSales = new LengthAwarePaginator(
                array_values($data->data),
                $data->total,
                $data->per_page,
                $data->current_page
            );

            $table_row = '';
            $table_pagination = '';

            foreach ($dataSales as $data) {
                $table_row .= '<tr>
                        <td>'.$data->kode_sales.'</td>
                        <td>'.$data->nama_sales.'</td>
                        <td class="text-center">
                            <button id="selectedOptionSalesman" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                data-kode_sales="'.$data->kode_sales.'" data-nama_sales="'.$data->nama_sales.'">
                                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                            </button>
                        </td>
                    </tr>';
            }

            foreach ($data_link_page as $data) {
                $page = '';
                if(!empty($data->url)) {
                    $pages = explode("?page=", $data->url);
                    $page = $pages[1];
                }

                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';

                if (Str::contains(trim($data->label), 'previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if (Str::contains(trim($data->label), 'next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if ($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                        <span class="page-link">'.trim($label).'</span></span>
                    </li>';
                } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                        <a href="#" class="page-link" data-page="'.trim($page).'">'.trim($label).'</a>
                    </li>';
                }
            }

            $table_per_page10 = '';
            $table_per_page25 = '';
            $table_per_page50 = '';
            $table_per_page100 = '';
            if ($data_per_page == '10') {
                $table_per_page10 = 'selected';
            } elseif ($data_per_page == '25') {
                $table_per_page25 = 'selected';
            } elseif ($data_per_page == '50') {
                $table_per_page50 = 'selected';
            } elseif ($data_per_page == '100') {
                $table_per_page100 = 'selected';
            }

            if($table_row == '') {
                $table_row .= '<tr>
                            <td colspan="3" class="pt-12 pb-12">
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

            $table_header = '<table id="tableSearchSalesman" class="table align-middle table-row-bordered fs-6">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-100px">Kode Sales</th>
                            <th class="min-w-150px">Nama Sales</th>
                            <th class="min-w-50px text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="fs-7 fw-bold text-gray-800">'.$table_row.'</tbody>
                </table>
                <div id="pageSalesman" class="mt-5">
                    <div class="row">
                        <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                            <div class="dataTables_length">
                                <label>
                                    <select id="selectPerPageOptionSalesman" name="selectPerPageOptionSalesman" aria-controls="selectPerPageOption"
                                        class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                        <option value="10"'.$table_per_page10.'>10</option>
                                        <option value="25"'.$table_per_page25.'>25</option>
                                        <option value="50"'.$table_per_page50.'>50</option>
                                        <option value="100"'.$table_per_page100.'>100</option>
                                    </select>
                                </label>
                            </div>
                            <div class="dataTables_info" id="selectPerPageOptionSalesmanInfo" role="status" aria-live="polite">Showing <span id="startRecordOptionSalesman">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                        </div>
                        <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                            <div class="dataTables_paginate paging_simple_numbers" id="paginationOptionSalesman">
                                <ul class="pagination">'.$table_pagination.'</ul>
                            </div>
                        </div>
                    </div>
                </div>';


            return response()->json(['status' => 1, 'message' => 'success', 'data' => $table_header]);
        } else {
            return response()->json(['status' => 0, 'message' => $messageApi]);
        }
    }

    public function optionSupervisor(Request $request)
    {
        $responseApi = Service::optionSupervisor($request->get('search'), $request->get('page'),
                            $request->get('per_page'), strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            $data_per_page = $data->per_page;
            $data_link_page = $data->links;
            $data_from_record = $data->from;
            $data_to_record = $data->to;
            $data_total_record = $data->total;

            $dataSupervisor = new LengthAwarePaginator(
                array_values($data->data),
                $data->total,
                $data->per_page,
                $data->current_page
            );

            $table_row = '';
            $table_pagination = '';

            foreach ($dataSupervisor as $data) {
                $table_row .= '<tr>
                        <td>'.$data->kode_spv.'</td>
                        <td>'.$data->nama_spv.'</td>
                        <td class="text-center">
                            <button id="selectedOptionSupervisor" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                data-kode_spv="'.$data->kode_spv.'" data-nama_spv="'.$data->nama_spv.'">
                                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                            </button>
                        </td>
                    </tr>';
            }

            foreach ($data_link_page as $data) {
                $page = '';
                if(!empty($data->url)) {
                    $pages = explode("?page=", $data->url);
                    $page = $pages[1];
                }

                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';

                if (Str::contains(trim($data->label), 'previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if (Str::contains(trim($data->label), 'next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if ($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                        <span class="page-link">'.trim($label).'</span></span>
                    </li>';
                } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                        <a href="#" class="page-link" data-page="'.trim($page).'">'.trim($label).'</a>
                    </li>';
                }
            }

            $table_per_page10 = '';
            $table_per_page25 = '';
            $table_per_page50 = '';
            $table_per_page100 = '';
            if ($data_per_page == '10') {
                $table_per_page10 = 'selected';
            } elseif ($data_per_page == '25') {
                $table_per_page25 = 'selected';
            } elseif ($data_per_page == '50') {
                $table_per_page50 = 'selected';
            } elseif ($data_per_page == '100') {
                $table_per_page100 = 'selected';
            }

            if($table_row == '') {
                $table_row .= '<tr>
                            <td colspan="3" class="pt-12 pb-12">
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

            $table_header = '<table id="tableSearchSupervisor" class="table align-middle table-row-bordered fs-6">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-100px">Kode SPV</th>
                            <th class="min-w-150px">Nama SPV</th>
                            <th class="min-w-50px text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="fs-7 fw-bold text-gray-800">'.$table_row.'</tbody>
                </table>
                <div id="pageSupervisor" class="mt-5">
                    <div class="row">
                        <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                            <div class="dataTables_length">
                                <label>
                                    <select id="selectPerPageOptionSupervisor" name="selectPerPageOptionSupervisor" aria-controls="selectPerPageOption"
                                        class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                        <option value="10"'.$table_per_page10.'>10</option>
                                        <option value="25"'.$table_per_page25.'>25</option>
                                        <option value="50"'.$table_per_page50.'>50</option>
                                        <option value="100"'.$table_per_page100.'>100</option>
                                    </select>
                                </label>
                            </div>
                            <div class="dataTables_info" id="selectPerPageOptionSupervisorInfo" role="status" aria-live="polite">Showing <span id="startRecordOptionSupervisor">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                        </div>
                        <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                            <div class="dataTables_paginate paging_simple_numbers" id="paginationOptionSupervisor">
                                <ul class="pagination">'.$table_pagination.'</ul>
                            </div>
                        </div>
                    </div>
                </div>';


            return response()->json(['status' => 1, 'message' => 'success', 'data' => $table_header]);
        } else {
            return response()->json(['status' => 0, 'message' => $messageApi]);
        }
    }

    public function optionTipeMotor(Request $request)
    {
        $responseApi = Service::OptionTipeMotor($request->get('search'), $request->get('page'), $request->get('per_page'),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            $data_per_page = $data->per_page;
            $data_link_page = $data->links;
            $data_from_record = $data->from;
            $data_to_record = $data->to;
            $data_total_record = $data->total;

            $dataDealerSales = new LengthAwarePaginator(
                array_values($data->data),
                $data->total,
                $data->per_page,
                $data->current_page
            );

            $table_row = '';
            $table_pagination = '';

            foreach ($dataDealerSales as $data) {
                $table_row .= '<tr>
                        <td>'.strtoupper(trim($data->kode)).'</td>
                        <td>'.strtoupper(trim($data->keterangan)).'</td>
                        <td class="text-center">
                            <button id="selectedOptionTipeMotor" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                data-kode="'.strtoupper(trim($data->kode)).'" data-keterangan="'.strtoupper(trim($data->keterangan)).'">
                                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                            </button>
                        </td>
                    </tr>';
            }

            foreach ($data_link_page as $data) {
                $page = '';
                if(!empty($data->url)) {
                    $pages = explode("?page=", $data->url);
                    $page = $pages[1];
                }

                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';

                if (Str::contains(trim($data->label), 'previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if (Str::contains(trim($data->label), 'next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if ($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                            <span class="page-link">'.trim($label).'</span></span>
                        </li>';
                } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                            <a href="#" class="page-link" data-page="'.trim($page).'">'.trim($label).'</a>
                        </li>';
                }
            }

            $table_per_page10 = '';
            $table_per_page25 = '';
            $table_per_page50 = '';
            $table_per_page100 = '';
            if ($data_per_page == '10') {
                $table_per_page10 = 'selected';
            } elseif ($data_per_page == '25') {
                $table_per_page25 = 'selected';
            } elseif ($data_per_page == '50') {
                $table_per_page50 = 'selected';
            } elseif ($data_per_page == '100') {
                $table_per_page100 = 'selected';
            }

            if($table_row == '') {
                $table_row .= '<tr>
                            <td colspan="3" class="pt-12 pb-12">
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

            $table_header = '<table id="tableSearchTipeMotor" class="table align-middle table-row-bordered fs-6">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-100px">Kode</th>
                                <th class="min-w-150px">Keterangan</th>
                                <th class="min-w-50px text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fs-7 fw-bold text-gray-800">'.$table_row.'</tbody>
                    </table>
                    <div id="pageTipeMotor" class="mt-5">
                        <div class="row">
                            <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                <div class="dataTables_length">
                                    <label>
                                        <select id="selectPerPageOptionTipeMotor" name="selectPerPageOptionTipeMotor" aria-controls="selectPerPageOption"
                                            class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                            <option value="10"'.$table_per_page10.'>10</option>
                                            <option value="25"'.$table_per_page25.'>25</option>
                                            <option value="50"'.$table_per_page50.'>50</option>
                                            <option value="100"'.$table_per_page100.'>100</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="dataTables_info" id="selectPerPageOptionTipeMotorInfo" role="status" aria-live="polite">Showing <span id="startRecordOptionTipeMotor">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                            </div>
                            <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                <div class="dataTables_paginate paging_simple_numbers" id="paginationOptionTipeMotor">
                                    <ul class="pagination">'.$table_pagination.'</ul>
                                </div>
                            </div>
                        </div>
                    </div>';

            return response()->json(['status' => 1, 'message' => 'success', 'data' => $table_header]);
        } else {
            return response()->json(['status' => 0, 'message' => $messageApi]);
        }
    }

    public function optionGroupProduk(Request $request)
    {
        $responseApi = Service::OptionGroupProduk($request->get('level'), $request->get('search'),
                            $request->get('page'), $request->get('per_page'));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            $data_per_page = $data->per_page;
            $data_link_page = $data->links;
            $data_from_record = $data->from;
            $data_to_record = $data->to;
            $data_total_record = $data->total;

            $dataGroupProduk = new LengthAwarePaginator(
                array_values($data->data),
                $data->total,
                $data->per_page,
                $data->current_page
            );

            $table_row = '';
            $table_pagination = '';

            foreach ($dataGroupProduk as $data) {
                $table_row .= '<tr>
                        <td>'.$data->kode_produk.'</td>
                        <td>'.$data->keterangan.'</td>
                        <td class="text-center">
                            <button id="selectedOptionProduk" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                data-kode_produk="'.$data->kode_produk.'" data-keterangan="'.$data->keterangan.'">
                                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                            </button>
                        </td>
                    </tr>';
            }

            foreach ($data_link_page as $data) {
                $page = '';
                if(!empty($data->url)) {
                    $pages = explode("?page=", $data->url);
                    $page = $pages[1];
                }

                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';

                if (Str::contains(trim($data->label), 'previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if (Str::contains(trim($data->label), 'next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if ($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                            <span class="page-link">'.trim($label).'</span></span>
                        </li>';
                } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                            <a href="#" class="page-link" data-page="'.trim($page).'">'.trim($label).'</a>
                        </li>';
                }
            }

            $table_per_page10 = '';
            $table_per_page25 = '';
            $table_per_page50 = '';
            $table_per_page100 = '';
            if ($data_per_page == '10') {
                $table_per_page10 = 'selected';
            } elseif ($data_per_page == '25') {
                $table_per_page25 = 'selected';
            } elseif ($data_per_page == '50') {
                $table_per_page50 = 'selected';
            } elseif ($data_per_page == '100') {
                $table_per_page100 = 'selected';
            }

            if($table_row == '') {
                $table_row .= '<tr>
                            <td colspan="3" class="pt-12 pb-12">
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

            $table_header = '<table id="tableSearchProduk" class="table align-middle table-row-bordered fs-6">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-100px">Kode Produk</th>
                                <th class="min-w-150px">Nama Produk</th>
                                <th class="min-w-50px text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fs-7 fw-bold text-gray-800">'.$table_row.'</tbody>
                    </table>
                    <div id="pageProduk" class="mt-5">
                        <div class="row">
                            <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                <div class="dataTables_length">
                                    <label>
                                        <select id="selectPerPageOptionProduk" name="selectPerPageOptionProduk" aria-controls="selectPerPageOption"
                                            class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                            <option value="10"'.$table_per_page10.'>10</option>
                                            <option value="25"'.$table_per_page25.'>25</option>
                                            <option value="50"'.$table_per_page50.'>50</option>
                                            <option value="100"'.$table_per_page100.'>100</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="dataTables_info" id="selectPerPageOptionProdukInfo" role="status" aria-live="polite">Showing <span id="startRecordOptionProduk">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                            </div>
                            <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                <div class="dataTables_paginate paging_simple_numbers" id="paginationOptionGroupProduk">
                                    <ul class="pagination">'.$table_pagination.'</ul>
                                </div>
                            </div>
                        </div>
                    </div>';

            return response()->json(['status' => 1, 'message' => 'success', 'data' => $table_header]);
        } else {
            return response()->json(['status' => 0, 'message' => $messageApi]);
        }
    }

    public function optionUpdateHarga(Request $request)
    {
        $responseApi = Service::OptionUpdateHarga($request->get('lokasi'), $request->get('page'),
                            $request->get('per_page'), $request->get('search'),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            $data_per_page = $data->per_page;
            $data_link_page = $data->links;
            $data_from_record = $data->from;
            $data_to_record = $data->to;
            $data_total_record = $data->total;

            $dataUpdateHarga = new LengthAwarePaginator(
                array_values($data->data),
                $data->total,
                $data->per_page,
                $data->current_page
            );

            $table_row = '';
            $table_pagination = '';

            foreach ($dataUpdateHarga as $data) {
                $table_row .= '<tr>
                        <td>'.$data->tanggal.'</td>
                        <td>'.$data->kode.'</td>
                        <td class="text-center">
                            <button id="selectedOptionUpdateHarga" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                data-tanggal="'.$data->tanggal.'" data-kode="'.$data->kode.'">
                                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                            </button>
                        </td>
                    </tr>';
            }

            foreach ($data_link_page as $data) {
                $page = '';
                if(!empty($data->url)) {
                    $pages = explode("?page=", $data->url);
                    $page = $pages[1];
                }

                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';

                if (Str::contains(trim($data->label), 'previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if (Str::contains(trim($data->label), 'next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if ($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                            <span class="page-link">'.trim($label).'</span></span>
                        </li>';
                } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                            <a href="#" class="page-link" data-page="'.trim($page).'">'.trim($label).'</a>
                        </li>';
                }
            }

            $table_per_page10 = '';
            $table_per_page25 = '';
            $table_per_page50 = '';
            $table_per_page100 = '';
            if ($data_per_page == '10') {
                $table_per_page10 = 'selected';
            } elseif ($data_per_page == '25') {
                $table_per_page25 = 'selected';
            } elseif ($data_per_page == '50') {
                $table_per_page50 = 'selected';
            } elseif ($data_per_page == '100') {
                $table_per_page100 = 'selected';
            }

            if($table_row == '') {
                $table_row .= '<tr>
                            <td colspan="3" class="pt-12 pb-12">
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

            $table_header = '<table id="tableSearchUpdateHarga" class="table align-middle table-row-bordered fs-6">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-100px">Tanggal</th>
                                <th class="min-w-150px">Kode</th>
                                <th class="min-w-50px text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fs-7 fw-bold text-gray-800">'.$table_row.'</tbody>
                    </table>
                    <div id="pageUpdateHarga" class="mt-5">
                        <div class="row">
                            <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                <div class="dataTables_length">
                                    <label>
                                        <select id="selectPerPageOptionUpdateHarga" name="selectPerPageOptionUpdateHarga" aria-controls="selectPerPageOption"
                                            class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                            <option value="10"'.$table_per_page10.'>10</option>
                                            <option value="25"'.$table_per_page25.'>25</option>
                                            <option value="50"'.$table_per_page50.'>50</option>
                                            <option value="100"'.$table_per_page100.'>100</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="dataTables_info" id="selectPerPageOptionUpdateHargaInfo" role="status" aria-live="polite">Showing <span id="startRecordOptionUpdateHarga">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                            </div>
                            <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                <div class="dataTables_paginate paging_simple_numbers" id="paginationOptionUpdateHarga">
                                    <ul class="pagination">'.$table_pagination.'</ul>
                                </div>
                            </div>
                        </div>
                    </div>';

            return response()->json(['status' => 1, 'message' => 'success', 'data' => $table_header]);
        } else {
            return response()->json(['status' => 0, 'message' => $messageApi]);
        }
    }

    // ! suma sby
    public static function salesman(Request $request){
        $responseApi = Service::dataSalesman($request);
        $statusApi = json_decode($responseApi)?->status;
        $messageApi =  json_decode($responseApi)?->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            if($request->option == 'first'){
                $respon = $data;
            } else if($request->option == 'select'){
                $respon = '';
                foreach ($data as $key => $value) {
                    $respon .= '<option value="'.$value->kd_sales.'" data-ket="'.$value->nm_sales.'">'.$value->kd_sales.($value->nm_sales ? '('.$value->nm_sales.')' : '').'</option>';
                }
            } else if($request->option == 'page'){
                $respon = view('layouts.option.option', [
                    'data' => $data,
                    'modal' => (object)[
                        'title' => 'List Salse',
                        'size' => 'modal-lg',
                    ],
                    'cari' => (object)[
                        'title' => 'Kode Sales',
                        'value' => $request->kd_sales,
                    ],
                    'table' => (object)[
                        'thead' => (object)[
                            (object)['class' => 'w-100px', 'text' => 'kode Sales'],
                            (object)['class' => 'w-100px', 'text' => 'nm_sales'],
                            (object)['class' => 'w-auto', 'text' => 'Action'],
                        ],
                        'tbody' => (object)[
                            (object)[ 'option' => 'text', 'class' => 'w-50px', 'key' => 'kd_sales'],
                            (object)[ 'option' => 'text', 'class' => 'w-auto', 'key' => 'nm_sales'],
                            (object)[ 'option' => 'button', 'class' => 'w-auto', 'button' => [
                                (object)[ 'class' => 'btn btn-primary me-2 pilih', 'text' => 'Pilih'],
                            ]],
                        ],
                    ],
                    'per_page' => (object)[
                        'value' => $request->per_page,
                    ]
                ])->render();
            }
            return Response()->json(['status' => 1, 'message' => 'success', 'data' => $respon], 200);
        } else {
            if($messageApi == null){
                $messageApi = 'Maaf terjadi kesalahan, silahkan coba beberapa saat lagi';
            }
            return Response()->json(['status' => 0, 'message' => $messageApi, 'data'=> ''], 200);
        }
    }
    public static function dealer(Request $request){
        $responseApi = Service::dataDealer($request);
        $statusApi = json_decode($responseApi)?->status;
        $messageApi =  json_decode($responseApi)?->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            
            if($request->option == 'first'){
                $respon = $data;
            } else if($request->option == 'select'){
                $respon = '';
                foreach ($data as $key => $value) {
                    $respon .= '<option value="'.$value->kd_dealer.'" data-ket="'.$value->nm_dealer.'">'.$value->kd_dealer.($value->nm_dealer ? '('.$value->nm_dealer.')' : '').'</option>';
                }
            } else if($request->option == 'page'){
                $respon = view('layouts.option.option', [
                    'data' => $data,
                    'modal' => (object)[
                        'title' => 'List Dealer',
                        'size' => 'modal-lg',
                    ],
                    'cari' => (object)[
                        'title' => 'Kode Dealer',
                        'value' => $request->kd_dealer,
                    ],
                    'table' => (object)[
                        'thead' => (object)[
                            (object)['class' => 'w-50px', 'text' => 'kode Dealer'],
                            (object)['class' => 'w-200px', 'text' => 'nama Dealer'],
                            (object)['class' => 'w-200px', 'text' => 'alamat'],
                            (object)['class' => 'w-50px', 'text' => 'kota'],
                            (object)['class' => 'w-auto', 'text' => 'Action'],
                        ],
                        'tbody' => (object)[
                            (object)[ 'option' => 'text', 'class' => 'w-50px', 'key' => 'kd_dealer'],
                            (object)[ 'option' => 'text', 'class' => 'w-200px', 'key' => 'nm_dealer'],
                            (object)[ 'option' => 'text', 'class' => 'w-200px', 'key' => 'alamat1'],
                            (object)[ 'option' => 'text','class' => 'w-50px', 'key' => 'kotasj'],
                            (object)[ 'option' => 'button', 'class' => 'w-auto text-center', 'button' => [
                                (object)[ 'class' => 'btn btn-primary me-2 pilih', 'text' => 'Pilih',
                                    'data' => [(object)['key' => 'a','value' => 'kd_dealer']]
                                ],
                            ]],
                        ],
                    ],
                    'per_page' => (object)[
                        'value' => $request->per_page,
                    ]
                ])->render();
            }
            return Response()->json(['status' => 1, 'message' => 'success', 'data' => $respon], 200);
        } else if($statusApi == 0) {
            return Response()->json(['status' => 0, 'message' => $messageApi, 'data'=> ''], 200);
        } else {
            return Response()->json(['status' => 2, 'message' => 'Maaf terjadi kesalahan, silahkan coba beberapa saat lagi', 'data'=> ''], 200);
        }
    }
    public static function faktur(Request $request){
        $request->merge(['divisi' => (in_array($request->companyid, collect(session('app_user_company')->fdr->lokasi)->pluck('companyid')->toArray()))?'fdr':'honda']);
        
        $responseApi = Service::dataFaktur($request);
        $statusApi = json_decode($responseApi)?->status;
        $messageApi =  json_decode($responseApi)?->message;

        if ($statusApi == 1) {
            $respon = json_decode($responseApi)->data;

            return Response()->json(['status' => 1, 'message' => $messageApi, 'data' => $respon], 200);
        } else if($statusApi == 0) {
            return Response()->json(['status' => 0, 'message' => $messageApi, 'data'=> ''], 200);
        } else {
            return Response()->json(['status' => 2, 'message' => 'Maaf terjadi kesalahan, silahkan coba beberapa saat lagi', 'data'=> ''], 200);
        }
    }
    public static function konsumen(Request $request){
        // $request->merge(['divisi' => ($request->companyid == $lokasi->fdr->companyid)?$lokasi->fdr->divisi:$lokasi->honda->divisi]);
        $request->merge(['divisi' => (in_array($request->companyid, collect(session('app_user_company')->fdr->lokasi)->pluck('companyid')->toArray()))?'fdr':'honda']);

        $responseApi = Service::dataKonsumen($request);
        $statusApi = json_decode($responseApi)?->status;
        $messageApi =  json_decode($responseApi)?->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            
            if($request->option == 'first'){
                $respon = $data;
            }else if($request->option == 'page'){
                $respon = view('layouts.option.option', [
                    'data' => $data,
                    'modal' => (object)[
                        'title' => 'List Konsumen',
                        'size' => 'modal-xl modal-fullscreen-lg-down',
                    ],
                    'cari' => (object)[
                        'title' => 'Dapat mencari : NIK, Nama, Alamat, Telepon, Email, No Pol',
                        'value' => $request->search,
                    ],
                    'table' => (object)[
                        'thead' => (object)[
                            (object)['class' => 'w-50px', 'text' => 'NIK'],
                            (object)['class' => 'w-200px', 'text' => 'NAMA'],
                            (object)['class' => 'w-200px', 'text' => 'TEMPAT TGL LAHIR'],
                            (object)['class' => 'w-200px', 'text' => 'ALAMAT'],
                            (object)['class' => 'w-200px', 'text' => 'TELEPON'],
                            (object)['class' => 'w-200px', 'text' => 'EMAIL'],
                            (object)['class' => 'w-200px', 'text' => 'NO POL'],
                            (object)['class' => 'w-200px', 'text' => 'Action'],
                        ],
                        'tbody' => (object)[
                            (object)['option' => 'text', 'class' => 'w-25px', 'key' => 'nik'],
                            (object)['option' => 'text', 'class' => 'w-200px', 'key' => 'nama'],
                            (object)['option' => 'text', 'class' => 'w-200px', 'key' => ['tempat_lahir', 'tgl_lahir']],
                            (object)['option' => 'text', 'class' => 'w-200px', 'key' => 'alamat'],
                            (object)['option' => 'text', 'class' => 'w-200px', 'key' => 'telepon'],
                            (object)['option' => 'text', 'class' => 'w-200px', 'key' => 'email'],
                            (object)['option' => 'text', 'class' => 'w-200px', 'key' => 'nopol'],
                            (object)['option' => 'button', 'class' => 'w-auto', 'button' => [
                                (object)[ 'class' => 'btn-sm btn-icon btn-primary me-2 text-white pilih', 'text' => 'Pilih',
                                    'data' => [
                                        (object)[
                                                'key' => 'a',
                                                'value' => ['nik', 'nama', 'tempat_lahir', 'tgl_lahir', 'alamat', 'telepon', 'email', 'nopol']
                                            ]
                                        ]
                                    ]
                                ],
                            ]
                        ],
                    ],
                    'per_page' => (object)[
                        'value' => $request->per_page,
                    ]
                ])->render();
            }
            return Response()->json(['status' => 1, 'message' => 'success', 'data' => $respon], 200);
        } else if($statusApi == 0) {
            return Response()->json(['status' => 0, 'message' => $messageApi, 'data'=> ''], 200);
        } else {
            return Response()->json(['status' => 2, 'message' => 'Maaf terjadi kesalahan, silahkan coba beberapa saat lagi', 'data'=> ''], 200);
        }
    }
    public static function part(Request $request){
        $responseApi = Service::dataPart($request);
        $statusApi = json_decode($responseApi)?->status;
        $messageApi =  json_decode($responseApi)?->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            
            if($request->option == 'first'){
                $respon = $data;
            }else if($request->option == 'page'){
                $respon = view('layouts.option.option', [
                    'data' => $data,
                    'modal' => (object)[
                        'title' => 'List Part',
                        'size' => 'modal-lg',
                    ],
                    'cari' => (object)[
                        'title' => 'Kode Part',
                        'value' => $request->kd_part,
                    ],
                    'table' => (object)[
                        'thead' => (object)[
                            (object)['class' => 'w-50px', 'text' => 'kode Part'],
                            (object)['class' => 'w-200px', 'text' => 'nama Part'],
                            (object)['class' => 'w-25', 'text' => 'Action'],
                        ],
                        'tbody' => (object)[
                            (object)[ 'option' => 'text', 'class' => 'w-50px', 'key' => 'kd_part'],
                            (object)[ 'option' => 'text', 'class' => 'w-200px', 'key' => 'nm_part'],
                            (object)[ 'option' => 'button', 'class' => 'w-auto text-center', 'button' => [
                                (object)[ 'class' => 'btn btn-primary me-2 pilih', 'text' => 'Pilih',
                                    'data' => [(object)['key' => 'a','value' => 'kd_part']]
                                ],
                            ]],
                        ],
                    ],
                    'per_page' => (object)[
                        'value' => $request->per_page,
                    ]
                ])->render();
            }
            return Response()->json(['status' => 1, 'message' => 'success', 'data' => $respon], 200);
        } else if($statusApi == 0) {
            return Response()->json(['status' => 0, 'message' => $messageApi, 'data'=> ''], 200);
        } else {
            return Response()->json(['status' => 2, 'message' => 'Maaf terjadi kesalahan, silahkan coba beberapa saat lagi', 'data'=> ''], 200);
        }
    }
    public static function produk(Request $request){
        $responseApi = Service::dataProduk($request);
        $statusApi = json_decode($responseApi)?->status;
        $messageApi =  json_decode($responseApi)?->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            
            if($request->option == 'first'){
                $respon = $data;
            }else if($request->option == 'select'){
                $respon = '';
                foreach ($data as $key => $value) {
                    $respon .= '<option value="'.$value->kd_produk.'" data-ket="'.$value->nm_produk.'">'.$value->kd_produk.($value->nm_produk ? '('.$value->nm_produk.')' : '').'</option>';
                }
            }else if($request->option == 'page'){
                $respon = view('layouts.option.option', [
                    'data' => $data,
                    'modal' => (object)[
                        'title' => 'List Produk',
                        'size' => 'modal-lg',
                    ],
                    'cari' => (object)[
                        'title' => 'Kode Produk',
                        'value' => $request->kd_produk,
                    ],
                    'table' => (object)[
                        'thead' => (object)[
                            (object)['class' => 'w-50px', 'text' => 'kode Produk'],
                            (object)['class' => 'w-200px', 'text' => 'nama Produk'],
                            (object)['class' => 'w-auto', 'text' => 'Action'],
                        ],
                        'tbody' => (object)[
                            (object)[ 'option' => 'text', 'class' => 'w-50px', 'key' => 'kd_produk'],
                            (object)[ 'option' => 'text', 'class' => 'w-200px', 'key' => 'nm_produk'],
                            (object)[ 'option' => 'button', 'class' => 'w-auto text-center', 'button' => [
                                (object)[ 'class' => 'btn btn-primary me-2 pilih', 'text' => 'Pilih',
                                    'data' => [(object)['key' => 'a','value' => 'kd_produk']]
                                ],
                            ]],
                        ],
                    ],
                    'per_page' => (object)[
                        'value' => $request->per_page,
                    ]
                ])->render();
            }
            return Response()->json(['status' => 1, 'message' => 'success', 'data' => $respon], 200);
        } else if($statusApi == 0) {
            return Response()->json(['status' => 0, 'message' => $messageApi, 'data'=> ''], 200);
        } else {
            return Response()->json(['status' => 2, 'message' => 'Maaf terjadi kesalahan, silahkan coba beberapa saat lagi', 'data'=> ''], 200);
        }
    }
    public static function merekmotor(Request $request){
        $responseApi = Service::dataMerekmotor($request);
        $statusApi = json_decode($responseApi)?->status;
        $messageApi =  json_decode($responseApi)?->message;
        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            if($request->option == 'first'){
                $respon = $data;
            } else if($request->option == 'select'){
                $respon = '';
                foreach ($data as $key => $value) {
                    $respon .= '<option value="'.$value->MerkMotor.'">'.$value->MerkMotor.'</option>';
                }
            }
            return Response()->json(['status' => 1, 'message' => 'success', 'data' => $respon], 200);
        } else {
            if($messageApi == null){
                $messageApi = 'Maaf terjadi kesalahan, silahkan coba beberapa saat lagi';
            }
            return Response()->json(['status' => 0, 'message' => $messageApi, 'data'=> ''], 200);
        }
    }

    public static function typemotor(Request $request){
        $responseApi = Service::dataTypemotor($request);
        $statusApi = json_decode($responseApi)?->status;
        $messageApi =  json_decode($responseApi)?->message;

        return $responseApi;
        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            if($request->option == 'first'){
                $respon = $data;
            } else if($request->option == 'get'){
                $respon = $data;
            }
            return Response()->json(['status' => 1, 'message' => 'success', 'data' => $respon], 200);
        } else {
            if($messageApi == null){
                $messageApi = 'Maaf terjadi kesalahan, silahkan coba beberapa saat lagi';
            }
            return Response()->json(['status' => 0, 'message' => $messageApi, 'data'=> ''], 200);
        }
    }
    // ! 
}
