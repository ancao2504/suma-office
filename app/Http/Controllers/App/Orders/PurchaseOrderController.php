<?php

namespace App\Http\Controllers\App\Orders;

use App\Http\Controllers\Controller;
use App\Helpers\ApiService;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Yajra\Datatables\Datatables;
use Jenssegers\Agent\Agent as Agent;

class PurchaseOrderController extends Controller
{
    public function index(Request $request) {
        $year = date('Y');
        $month = date('m');

        if(!empty($request->get('year'))) {
            $year = $request->get('year');
        }
        if(!empty($request->get('month'))) {
            $month = $request->get('month');
        }

        $kode_sales = '';
        $kode_dealer = '';
        $per_page = 10;

        if(strtoupper(trim($request->session()->get('app_user_role_id'))) == "D_H3") {
            $responseApi = ApiService::ValidasiDealer(strtoupper(trim($request->session()->get('app_user_id'))), strtoupper(trim($request->session()->get('app_user_company_id'))));
            $statusApi = json_decode($responseApi)->status;
            $messageApi =  json_decode($responseApi)->message;

            if($statusApi == 1) {
                $kode_dealer = strtoupper(trim(json_decode($responseApi)->data->kode_dealer));
                $kode_sales = strtoupper(trim(json_decode($responseApi)->data->kode_sales));
            } else {
                return redirect()->back()->withInput()->with('failed', $messageApi);
            }
        } elseif(strtoupper(trim($request->session()->get('app_user_role_id'))) == "MD_H3_SM") {
            $kode_sales = trim($request->session()->get('app_user_id'));
        }

        if($kode_sales == '') {
            if(!empty($request->get('salesman'))) {
                $kode_sales = trim($request->get('salesman'));
            }
        }

        if($kode_dealer == '') {
            if(!empty($request->get('dealer'))) {
                $kode_dealer = trim($request->get('dealer'));
            }
        }

        if(!empty($request->get('per_page')) && $request->get('per_page') != '') {
            if($request->get('per_page') == 10 || $request->get('per_page') == 25 || $request->get('per_page') == 50 || $request->get('per_page') == 100) {
                $per_page = $request->get('per_page');
            } else {
                $per_page = 10;
            }
        }

        $Agent = new Agent();
        $device = 'Desktop';
        if($Agent->isMobile()) {
            $device = 'Mobile';
        }

        $responseApi = ApiService::PurchaseOrderFormDaftar($year, $month, $kode_sales, $kode_dealer, $request->get('page'), $per_page,
                                    strtoupper(trim($request->session()->get('app_user_id'))),  strtoupper(trim($request->session()->get('app_user_role_id'))),
                                    strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            $data_page = new Collection();
            $data_page->push((object) [
                'from'  => $data->from,
                'to'    => $data->to,
                'total' => $data->total,
                'page'  => $data->current_page,
                'per_page' => $data->per_page
            ]);

            if($Agent->isMobile()) {
                $data_pof = $data->data;

                if ($request->ajax()) {
                    $view = view('layouts.orders.purchaseorder.mobile.purchaseorderlist', compact('data_pof'))->render();
                    return response()->json([ 'html' => $view ]);
                }
            } else {
                $data_pof = new LengthAwarePaginator(array_values($data->data), $data->total, $data->per_page, $data->current_page,
                [ 'path' => route('orders.purchase-order'), 'query' => request()->query()  ]);
            }

            return view('layouts.orders.purchaseorder.purchaseorder', [
                'title_menu'    => 'Purchase Order',
                'device'        => $device,
                'role_id'       => trim($request->session()->get('app_user_role_id')),
                'month'         => $month,
                'year'          => $year,
                'kode_sales'    => $kode_sales,
                'kode_dealer'   => $kode_dealer,
                'page'          => $data_page->first(),
                'data_pof'      => $data_pof
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function purchaseOrderForm($nomor_pof, Request $request) {
        $responseApi = ApiService::PurchaseOrderFormTransfer($nomor_pof, strtoupper(trim($request->session()->get('app_user_id'))),
                            strtoupper(trim($request->session()->get('app_user_role_id'))), strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $responseApi = ApiService::PurchaseOrderFormDetail($nomor_pof, strtoupper(trim($request->session()->get('app_user_id'))),
                        strtoupper(trim($request->session()->get('app_user_company_id'))));

            $statusApi = json_decode($responseApi)->status;
            $messageApi =  json_decode($responseApi)->message;

            if($statusApi == 1) {
                $data = json_decode($responseApi)->data;

                $Agent = new Agent();
                $device = 'Desktop';

                if ($Agent->isMobile()) {
                    $device = 'Mobile';
                }

                return view('layouts.orders.purchaseorder.purchaseorderform', [
                    'title_menu'    => 'Purchase Order',
                    'device'        => $device,
                    'role_id'       => strtoupper(trim($request->session()->get('app_user_role_id'))),
                    'nomor_pof'     => $data->nomor_pof,
                    'tanggal_pof'   => $data->tanggal_pof,
                    'kode_sales'    => $data->kode_sales,
                    'kode_dealer'   => $data->kode_dealer,
                    'kode_tpc'      => $data->kode_tpc,
                    'bo'            => $data->bo,
                    'umur_pof'      => $data->umur_pof,
                    'total_order'   => (double)$data->total_order,
                    'total_terlayani' => (double)$data->total_terlayani,
                    'sub_total'     => (double)$data->sub_total,
                    'disc_header'   => (double)$data->disc_header,
                    'grand_total'   => (double)$data->grand_total,
                    'approve'       => (int)$data->approve,
                    'approve_user'  => $data->approve_user,
                    'keterangan'    => $data->keterangan,
                    'status_faktur' => $data->status_faktur,
                    'detail_pof'    => $data->data_pof_detail
                ]);
            } else {
                return redirect()->back()->withInput()->with('failed', $messageApi);
            }
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function purchaseOrderFormDetail($nomor_pof, Request $request) {
        $responseApi = ApiService::PurchaseOrderFormDetail($nomor_pof, strtoupper(trim($request->session()->get('app_user_id'))),
                        strtoupper(trim($request->session()->get('app_user_company_id'))));

        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            $Agent = new Agent();

            if ($Agent->isMobile()) {
                return view('layouts.orders.purchaseorder.mobile.purchaseorderformlist', [
                    'nomor_pof'     => $data->nomor_pof,
                    'tanggal_pof'   => $data->tanggal_pof,
                    'kode_sales'    => $data->kode_sales,
                    'kode_dealer'   => $data->kode_dealer,
                    'kode_tpc'      => $data->kode_tpc,
                    'bo'            => $data->bo,
                    'umur_pof'      => $data->umur_pof,
                    'total_order'   => (double)$data->total_order,
                    'total_terlayani' => (double)$data->total_terlayani,
                    'sub_total'     => (double)$data->sub_total,
                    'disc_header'   => (double)$data->disc_header,
                    'grand_total'   => (double)$data->grand_total,
                    'approve'       => (int)$data->approve,
                    'approve_user'  => $data->approve_user,
                    'status_faktur' => $data->status_faktur,
                    'detail_pof'    => $data->data_pof_detail
                ]);
            } else {
                return view('layouts.orders.purchaseorder.desktop.purchaseorderformlist', [
                    'nomor_pof'     => $data->nomor_pof,
                    'tanggal_pof'   => $data->tanggal_pof,
                    'kode_sales'    => $data->kode_sales,
                    'kode_dealer'   => $data->kode_dealer,
                    'kode_tpc'      => $data->kode_tpc,
                    'bo'            => $data->bo,
                    'umur_pof'      => $data->umur_pof,
                    'total_order'   => (double)$data->total_order,
                    'total_terlayani' => (double)$data->total_terlayani,
                    'sub_total'     => (double)$data->sub_total,
                    'disc_header'   => (double)$data->disc_header,
                    'grand_total'   => (double)$data->grand_total,
                    'approve'       => (int)$data->approve,
                    'approve_user'  => $data->approve_user,
                    'status_faktur' => $data->status_faktur,
                    'detail_pof'    => $data->data_pof_detail
                ]);
            }
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function purchaseOrderFormUpdateTpc(Request $request) {
        $responseApi = ApiService::PurchaseOrderFormUpdateTpc($request->get('nomor_pof'), $request->get('tpc'),
                        strtoupper(trim($request->session()->get('app_user_id'))), strtoupper(trim($request->session()->get('app_user_company_id'))));

        return json_decode($responseApi, true);
    }

    public function purchaseOrderFormEditDiscount(Request $request) {
        $responseApi = ApiService::purchaseOrderFormEditDiscount($request->get('nomor_pof'), strtoupper(trim($request->session()->get('app_user_id'))),
                        strtoupper(trim($request->session()->get('app_user_company_id'))));

        return json_decode($responseApi, true);
    }

    public function purchaseOrderFormUpdateDiscount(Request $request) {
        $responseApi = ApiService::purchaseOrderFormUpdateDiscount($request->get('nomor_pof'), (double)$request->get('discount'),
                        strtoupper(trim($request->session()->get('app_user_id'))), strtoupper(trim($request->session()->get('app_user_company_id'))));

        return json_decode($responseApi, true);
    }

    public function purchaseOrderFormEditPart(Request $request) {
        $responseApi = ApiService::PurchaseOrderFormDetailEditPart($request->get('nomor_pof'), $request->get('part_number'),
                        strtoupper(trim($request->session()->get('app_user_id'))),  strtoupper(trim($request->session()->get('app_user_company_id'))));

        return json_decode($responseApi, true);
    }

    public function purchaseOrderFormSimpanPart(Request $request) {
        $responseApi = ApiService::PurchaseOrderFormDetailSimpanPart($request->get('nomor_pof'), $request->get('part_number'),
                        (double)$request->get('jml_order'), (double)$request->get('harga'), (double)$request->get('discount'),
                        strtoupper(trim($request->session()->get('app_user_id'))),  strtoupper(trim($request->session()->get('app_user_company_id'))));

        return json_decode($responseApi, true);
    }

    public function purchaseOrderFormHapusPart(Request $request) {
        $responseApi = ApiService::PurchaseOrderFormDetailHapusPart($request->get('nomor_pof'), $request->get('part_number'),
                        strtoupper(trim($request->session()->get('app_user_id'))),  strtoupper(trim($request->session()->get('app_user_company_id'))));

        return json_decode($responseApi, true);
    }

    public function viewDetailPofTerlayani(Request $request) {
        $responseApi = ApiService::PurchaseOrderFormFaktur(strtoupper($request->get('nomor_pof')),
                            strtoupper($request->get('part_number')), strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            $Agent = new Agent();

            $device = 'Desktop';
            if ($Agent->isMobile()) {
                $device = 'Mobile';
            }

            if(strtoupper(trim($device)) == 'DESKTOP') {
                $table_detail = '';

                foreach($data as $data) {
                    $table_detail .= '<tr class="text-start text-gray-800 fw-bolder fs-6 text-uppercase gs-0">
                            <td>'.$data->nomor_faktur.'</td>
                            <td>'.$data->tanggal.'</td>
                            <td class="text-end">'.(double)$data->jml_item.'</td>
                            <td class="text-end">
                                <a href="'.route('orders.faktur-view', trim($data->nomor_faktur)).'" class="btn btn-icon btn-bg-primary btn-sm">
                                    <span class="svg-icon svg-icon-3 text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M9.89557 13.4982L7.79487 11.2651C7.26967 10.7068 6.38251 10.7068 5.85731 11.2651C5.37559 11.7772 5.37559 12.5757 5.85731 13.0878L9.74989 17.2257C10.1448 17.6455 10.8118 17.6455 11.2066 17.2257L18.1427 9.85252C18.6244 9.34044 18.6244 8.54191 18.1427 8.02984C17.6175 7.47154 16.7303 7.47154 16.2051 8.02984L11.061 13.4982C10.7451 13.834 10.2115 13.834 9.89557 13.4982Z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                </a>
                            </td>
                        </tr>';
                }

                $table_header = '<div class="table-responsive">
                        <table id="tableFakturPof" class="table align-middle table-row-dashed fs-6">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-75px">Nomor Faktur</th>
                                    <th class="min-w-50px">Tanggal</th>
                                    <th class="min-w-50px text-end">Terlayani</th>
                                    <th class="min-w-100px text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>'.$table_detail.'</tbody>
                        </table>
                    </div>';

                $data = [ 'status' => 1, 'message' => 'success', 'data' => $table_header ];

                return response()->json($data);
            } else {
                $list_design = '';
                foreach($data as $data) {
                    $list_design .= '
                    <div class="fv-row mb-7 rounded border-gray-300 border-1 border-gray-300 border-dashed px-7 py-3 mb-3">
                        <div class="row">
                            <span class="text-muted fw-bold fs-7">Nomor Faktur:</span>
                            <span id="nomor_faktur" name="nomor_faktur" class="text-dark fw-bold fs-6">'.$data->nomor_faktur.'</span>
                        </div>
                        <div class="row mt-4">
                            <span class="text-muted fw-bold fs-7">Tanggal Faktur:</span>
                            <span id="tanggal_faktur" name="tanggal_faktur" class="text-dark fw-bold fs-6">'.$data->tanggal.'</span>
                        </div>
                        <div class="row mt-4">
                            <span class="text-muted fw-bold fs-7">Terlayani:</span>
                            <span id="jml_item" name="jml_item" class="text-dark fw-bold fs-6">'.(double)$data->jml_item.'</span>
                        </div>
                        <div class="row mt-4">
                            <a href="'.route('orders.faktur-view', trim($data->nomor_faktur)).'" class="btn btn-primary">Lihat Detail Faktur</a>
                        </div>
                    </div>';
                }
                $data = [ 'status' => 1, 'message' => 'success', 'data' => $list_design ];

                return response()->json($data);
            }

        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function purchaseOrderFormSave(Request $request) {
        $responseApi = ApiService::PurchaseOrderFormSimpan(strtoupper($request->get('nomor_pof')),
                            strtoupper($request->get('salesman')), strtoupper($request->get('dealer')),
                            $request->get('tpc'), $request->get('umur_pof'), $request->get('back_order'),
                            $request->get('btnSimpanPof'), $request->get('keterangan'), strtoupper(trim($request->session()->get('app_user_id'))),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            return redirect()->back()->withInput()->with('success', $messageApi);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }

    }

    public function purchaseOrderFormBatalApprove(Request $request) {
        $responseApi = ApiService::purchaseOrderFormBatalApprove(strtoupper($request->get('nomor_pof')),
                         strtoupper(trim($request->session()->get('app_user_company_id'))));
        return json_decode($responseApi, true);
    }


}
