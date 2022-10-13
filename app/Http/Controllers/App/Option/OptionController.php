<?php

namespace App\Http\Controllers\App\Option;

use App\Helpers\ApiService;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OptionController extends Controller {

    public function optionDealer(Request $request) {
        $responseApi = ApiService::optionDealer($request->get('search'), $request->get('page'), $request->get('per_page'),
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

            $dataDealerSales = new LengthAwarePaginator(array_values($data->data), $data->total, $data->per_page, $data->current_page,
            [ 'path' => '#', 'query' => request()->query()  ]);

            $table_row = '';
            $table_pagination = '';

            foreach($dataDealerSales as $data) {
                $table_row .= '<tr>
                        <td>'.$data->kode_dealer.'</td>
                        <td>'.$data->nama_dealer.'</td>
                        <td class="text-center">
                            <button id="selectDealer" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                data-kode_dealer="'.$data->kode_dealer.'" data-nama_dealer="'.$data->nama_dealer.'">
                                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                            </button>
                        </td>
                    </tr>';
            }

            foreach($data_link_page as $data) {
                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';
                $url = $data->url;

                if(Str::contains(trim($data->label), 'Previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if(Str::contains(trim($data->label), 'Next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                            <span class="page-link">'.trim($label).'</span></span>
                        </li>';
                    } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                            <a href="#" class="page-link" data-page="'.trim($url).'">'.trim($label).'</a>
                        </li>';
                    }
                }

                $table_per_page10 = '';
                $table_per_page25 = '';
                $table_per_page50 = '';
                $table_per_page100 = '';
                if($data_per_page == '10') {
                    $table_per_page10 = 'selected';
                } elseif($data_per_page == '25') {
                    $table_per_page25 = 'selected';
                } elseif($data_per_page == '50') {
                    $table_per_page50 = 'selected';
                } elseif($data_per_page == '100') {
                    $table_per_page100 = 'selected';
                }

                $table_header = '<table id="tableSearchDealer" class="table align-middle table-row-bordered fs-6">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-100px">Kode Sales</th>
                                <th class="min-w-150px">Nama Sales</th>
                                <th class="min-w-50px text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fs-6 fw-bold text-gray-800">'.$table_row.'</tbody>
                    </table>
                    <div id="pageDealer" class="mt-5">
                        <div class="row">
                            <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                <div class="dataTables_length">
                                    <label>
                                        <select id="selectPerPageDealer" name="selectPerPageDealer" aria-controls="selectPerPage"
                                            class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                            <option value="10" '.$table_per_page10.'>10</option>
                                            <option value="25" '.$table_per_page25.'>25</option>
                                            <option value="50" '.$table_per_page50.'>50</option>
                                            <option value="100" '.$table_per_page100.'>100</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="dataTables_info" id="selectPerPageDealerInfo" role="status" aria-live="polite">Showing <span id="startRecordSalesman">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                            </div>
                            <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                <div class="dataTables_paginate paging_simple_numbers" id="kt_datatable_example_5_paginate">
                                    <ul class="pagination">'.$table_pagination.'</ul>
                                </div>
                            </div>
                        </div>
                    </div>';

            return response()->json([ 'status' => 1, 'message' => 'success', 'data' => $table_header ]);
        } else {
            return response()->json([ 'status' => 0, 'message' => $messageApi ]);
        }
    }

    public function optionDealerIndex(Request $request) {
        $responseApi = ApiService::optionDealerSalesman($request->get('salesman'), $request->get('search'), $request->get('page'), $request->get('per_page'),
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

            $dataDealerSales = new LengthAwarePaginator(array_values($data->data), $data->total, $data->per_page, $data->current_page,
            [ 'path' => '#', 'query' => request()->query()  ]);

            $table_row = '';
            $table_pagination = '';

            foreach($dataDealerSales as $data) {
                $table_row .= '<tr>
                        <td>'.$data->kode_dealer.'</td>
                        <td>'.$data->nama_dealer.'</td>
                        <td class="text-center">
                            <button id="selectDealerIndex" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                data-kode_dealer="'.$data->kode_dealer.'" data-nama_dealer="'.$data->nama_dealer.'">
                                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                            </button>
                        </td>
                    </tr>';
            }

            foreach($data_link_page as $data) {
                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';
                $url = $data->url;

                if(Str::contains(trim($data->label), 'Previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if(Str::contains(trim($data->label), 'Next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                            <span class="page-link">'.trim($label).'</span></span>
                        </li>';
                    } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                            <a href="#" class="page-link" data-page="'.trim($url).'">'.trim($label).'</a>
                        </li>';
                    }
                }

                $table_per_page10 = '';
                $table_per_page25 = '';
                $table_per_page50 = '';
                $table_per_page100 = '';
                if($data_per_page == '10') {
                    $table_per_page10 = 'selected';
                } elseif($data_per_page == '25') {
                    $table_per_page25 = 'selected';
                } elseif($data_per_page == '50') {
                    $table_per_page50 = 'selected';
                } elseif($data_per_page == '100') {
                    $table_per_page100 = 'selected';
                }

                $table_header = '<table id="tableSearchDealerIndex" class="table align-middle table-row-bordered fs-6">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-100px">Kode Sales</th>
                                <th class="min-w-150px">Nama Sales</th>
                                <th class="min-w-50px text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fs-6 fw-bold text-gray-800">'.$table_row.'</tbody>
                    </table>
                    <div id="pageDealerIndex" class="mt-5">
                        <div class="row">
                            <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                <div class="dataTables_length">
                                    <label>
                                        <select id="selectPerPageDealerIndex" name="selectPerPageDealerIndex" aria-controls="selectPerPageIndex"
                                            class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                            <option value="10" '.$table_per_page10.'>10</option>
                                            <option value="25" '.$table_per_page25.'>25</option>
                                            <option value="50" '.$table_per_page50.'>50</option>
                                            <option value="100" '.$table_per_page100.'>100</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="dataTables_info" id="selectPerPageDealerIndexInfo" role="status" aria-live="polite">Showing <span id="startRecordIndex">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                            </div>
                            <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                <div class="dataTables_paginate paging_simple_numbers" id="kt_datatable_example_5_paginate">
                                    <ul class="pagination">'.$table_pagination.'</ul>
                                </div>
                            </div>
                        </div>
                    </div>';

            return response()->json([ 'status' => 1, 'message' => 'success', 'data' => $table_header ]);
        } else {
            return response()->json([ 'status' => 0, 'message' => $messageApi ]);
        }
    }

    public function optionDealerSalesman(Request $request) {
        $responseApi = ApiService::optionDealerSalesman($request->get('salesman'), $request->get('search'), $request->get('page'), $request->get('per_page'),
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

            $dataDealerSales = new LengthAwarePaginator(array_values($data->data), $data->total, $data->per_page, $data->current_page,
            [ 'path' => '#', 'query' => request()->query()  ]);

            $table_row = '';
            $table_pagination = '';

            foreach($dataDealerSales as $data) {
                $table_row .= '<tr>
                        <td>'.$data->kode_dealer.'</td>
                        <td>'.$data->nama_dealer.'</td>
                        <td class="text-center">
                            <button id="selectDealerSalesman" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                data-kode_dealer="'.$data->kode_dealer.'" data-nama_dealer="'.$data->nama_dealer.'">
                                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                            </button>
                        </td>
                    </tr>';
            }

            foreach($data_link_page as $data) {
                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';
                $url = $data->url;

                if(Str::contains(trim($data->label), 'Previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if(Str::contains(trim($data->label), 'Next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                            <span class="page-link">'.trim($label).'</span></span>
                        </li>';
                    } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                            <a href="#" class="page-link" data-page="'.trim($url).'">'.trim($label).'</a>
                        </li>';
                    }
                }

                $table_per_page10 = '';
                $table_per_page25 = '';
                $table_per_page50 = '';
                $table_per_page100 = '';
                if($data_per_page == '10') {
                    $table_per_page10 = 'selected';
                } elseif($data_per_page == '25') {
                    $table_per_page25 = 'selected';
                } elseif($data_per_page == '50') {
                    $table_per_page50 = 'selected';
                } elseif($data_per_page == '100') {
                    $table_per_page100 = 'selected';
                }

                $table_header = '<table id="tableSearchDealerSalesman" class="table align-middle table-row-bordered fs-6">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-100px">Kode Sales</th>
                                <th class="min-w-150px">Nama Sales</th>
                                <th class="min-w-50px text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fs-6 fw-bold text-gray-800">'.$table_row.'</tbody>
                    </table>
                    <div id="pageDealerSalesman" class="mt-5">
                        <div class="row">
                            <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                <div class="dataTables_length">
                                    <label>
                                        <select id="selectPerPageDealerSalesman" name="selectPerPageDealerSalesman" aria-controls="selectPerPageSalesman"
                                            class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                            <option value="10" '.$table_per_page10.'>10</option>
                                            <option value="25" '.$table_per_page25.'>25</option>
                                            <option value="50" '.$table_per_page50.'>50</option>
                                            <option value="100" '.$table_per_page100.'>100</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="dataTables_info" id="selectPerPageDealerSalesmanInfo" role="status" aria-live="polite">Showing <span id="startRecordSalesman">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                            </div>
                            <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                <div class="dataTables_paginate paging_simple_numbers" id="kt_datatable_example_5_paginate">
                                    <ul class="pagination">'.$table_pagination.'</ul>
                                </div>
                            </div>
                        </div>
                    </div>';

            return response()->json([ 'status' => 1, 'message' => 'success', 'data' => $table_header ]);
        } else {
            return response()->json([ 'status' => 0, 'message' => $messageApi ]);
        }
    }

    public function optionPartNumber(Request $request) {
        // $responseApi = ApiService::optionPartNumber($request->get('part_number'), strtoupper(trim($request->session()->get('app_user_company_id'))));
        // $statusApi = json_decode($responseApi)->status;
        // $messageApi =  json_decode($responseApi)->message;

        // if($statusApi == 1) {
        //     $data = json_decode($responseApi)->data;

        //     if($request->ajax()) {
        //         return Datatables::of($data)
        //         ->addIndexColumn()
        //         ->addColumn('action', function($data) {
        //             $action = '<button class="btn btn-icon btn-primary btn-sm border-0" id="selectPartNumber"
        //                             data-part_number="'.trim($data->part_number).'" data-description="'.trim($data->description).'"
        //                             data-produk="'.trim($data->produk).'"  data-het="'.trim($data->het).'" >
        //                             <i class="fa fa-check" data-toggle="tooltip" data-placement="top" title="Select"></i>
        //                         </button>';
        //                     return $action;
        //                 })
        //         ->rawColumns(['action'])
        //         ->make(true);
        //     }
        // } else {
        //     return redirect()->back()->withInput()->with('failed', $messageApi);
        // }
    }

    public function optionSalesman(Request $request) {
        $responseApi = ApiService::optionSalesman($request->get('search'), $request->get('page'), $request->get('per_page'),
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

            $dataSales = new LengthAwarePaginator(array_values($data->data), $data->total, $data->per_page, $data->current_page,
            [ 'path' => '#', 'query' => request()->query()  ]);

            $table_row = '';
            $table_pagination = '';

            foreach($dataSales as $data) {
                $table_row .= '<tr>
                        <td>'.$data->kode_sales.'</td>
                        <td>'.$data->nama_sales.'</td>
                        <td class="text-center">
                            <button id="selectSalesman" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                data-kode_sales="'.$data->kode_sales.'" data-nama_sales="'.$data->nama_sales.'">
                                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                            </button>
                        </td>
                    </tr>';
            }

            foreach($data_link_page as $data) {
                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';
                $url = $data->url;

                if(Str::contains(trim($data->label), 'Previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if(Str::contains(trim($data->label), 'Next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                        <span class="page-link">'.trim($label).'</span></span>
                    </li>';
                } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                        <a href="#" class="page-link" data-page="'.trim($url).'">'.trim($label).'</a>
                    </li>';
                }
            }

            $table_per_page10 = '';
            $table_per_page25 = '';
            $table_per_page50 = '';
            $table_per_page100 = '';
            if($data_per_page == '10') {
                $table_per_page10 = 'selected';
            } elseif($data_per_page == '25') {
                $table_per_page25 = 'selected';
            } elseif($data_per_page == '50') {
                $table_per_page50 = 'selected';
            } elseif($data_per_page == '100') {
                $table_per_page100 = 'selected';
            }

            $table_header = '<table id="tableSearchSalesman" class="table align-middle table-row-bordered fs-6">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-100px">Kode Sales</th>
                            <th class="min-w-150px">Nama Sales</th>
                            <th class="min-w-50px text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="fs-6 fw-bold text-gray-800">'.$table_row.'</tbody>
                </table>
                <div id="pageSalesman" class="mt-5">
                    <div class="row">
                        <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                            <div class="dataTables_length">
                                <label>
                                    <select id="selectPerPageSalesman" name="selectPerPageSalesman" aria-controls="selectPerPage"
                                        class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                        <option value="10" '.$table_per_page10.'>10</option>
                                        <option value="25" '.$table_per_page25.'>25</option>
                                        <option value="50" '.$table_per_page50.'>50</option>
                                        <option value="100" '.$table_per_page100.'>100</option>
                                    </select>
                                </label>
                            </div>
                            <div class="dataTables_info" id="selectPerPageSalesmanInfo" role="status" aria-live="polite">Showing <span id="startRecordSalesman">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                        </div>
                        <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                            <div class="dataTables_paginate paging_simple_numbers" id="kt_datatable_example_5_paginate">
                                <ul class="pagination">'.$table_pagination.'</ul>
                            </div>
                        </div>
                    </div>
                </div>';


            return response()->json([ 'status' => 1, 'message' => 'success', 'data' => $table_header ]);
        } else {
            return response()->json([ 'status' => 0, 'message' => $messageApi ]);
        }
    }

    public function optionTipeMotor(Request $request) {
        $responseApi = ApiService::OptionTipeMotor($request->get('search'), $request->get('page'), $request->get('per_page'),
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

            $dataDealerSales = new LengthAwarePaginator(array_values($data->data), $data->total, $data->per_page, $data->current_page,
            [ 'path' => '#', 'query' => request()->query()  ]);

            $table_row = '';
            $table_pagination = '';

            foreach($dataDealerSales as $data) {
                $table_row .= '<tr>
                        <td>'.strtoupper(trim($data->kode)).'</td>
                        <td>'.strtoupper(trim($data->keterangan)).'</td>
                        <td class="text-center">
                            <button id="selectTipeMotor" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                data-kode="'.strtoupper(trim($data->kode)).'" data-keterangan="'.strtoupper(trim($data->keterangan)).'">
                                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                            </button>
                        </td>
                    </tr>';
            }

            foreach($data_link_page as $data) {
                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';
                $url = $data->url;

                if(Str::contains(trim($data->label), 'Previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if(Str::contains(trim($data->label), 'Next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                            <span class="page-link">'.trim($label).'</span></span>
                        </li>';
                    } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                            <a href="#" class="page-link" data-page="'.trim($url).'">'.trim($label).'</a>
                        </li>';
                    }
                }

                $table_per_page10 = '';
                $table_per_page25 = '';
                $table_per_page50 = '';
                $table_per_page100 = '';
                if($data_per_page == '10') {
                    $table_per_page10 = 'selected';
                } elseif($data_per_page == '25') {
                    $table_per_page25 = 'selected';
                } elseif($data_per_page == '50') {
                    $table_per_page50 = 'selected';
                } elseif($data_per_page == '100') {
                    $table_per_page100 = 'selected';
                }

                $table_header = '<table id="tableSearchTipeMotor" class="table align-middle table-row-bordered fs-6">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-100px">Kode</th>
                                <th class="min-w-150px">Keterangan</th>
                                <th class="min-w-50px text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fs-6 fw-bold text-gray-800">'.$table_row.'</tbody>
                    </table>
                    <div id="pageTipeMotor" class="mt-5">
                        <div class="row">
                            <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                <div class="dataTables_length">
                                    <label>
                                        <select id="selectPerPageTipeMotor" name="selectPerPageTipeMotor" aria-controls="selectPerPage"
                                            class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                            <option value="10" '.$table_per_page10.'>10</option>
                                            <option value="25" '.$table_per_page25.'>25</option>
                                            <option value="50" '.$table_per_page50.'>50</option>
                                            <option value="100" '.$table_per_page100.'>100</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="dataTables_info" id="selectPerPageTipeMotorInfo" role="status" aria-live="polite">Showing <span id="startRecordSalesman">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                            </div>
                            <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                <div class="dataTables_paginate paging_simple_numbers" id="kt_datatable_example_5_paginate">
                                    <ul class="pagination">'.$table_pagination.'</ul>
                                </div>
                            </div>
                        </div>
                    </div>';

            return response()->json([ 'status' => 1, 'message' => 'success', 'data' => $table_header ]);
        } else {
            return response()->json([ 'status' => 0, 'message' => $messageApi ]);
        }
    }
}
