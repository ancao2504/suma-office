<?php

namespace App\Http\Controllers\App\Dashboard\Management;

use App\Helpers\App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;

class DashboardManagementSalesController extends Controller
{
    public function index(Request $request) {
        $year = date('Y');
        $month = date('m');
        $fields = 'SELLING_PRICE_IN_PPN';

        if(!empty($request->get('year'))) {
            $year = $request->get('year');
        }
        if(!empty($request->get('month'))) {
            $month = $request->get('month');
        }
        if(!empty($request->get('fields'))) {
            $fields = $request->get('fields');
        }

        if(strtoupper(trim($request->session()->get('app_user_role_id'))) != 'MD_H3_MGMT') {
            return redirect()->back()->withInput()->with('failed', 'Anda tidak dapat mengakses halaman ini');
        }

        $responseApi = Service::DashboardManagementSalesByProduct($year, $month, $fields, $request->get('level'), $request->get('produk'),
                            strtoupper(trim($request->session()->get('app_user_id'))), strtoupper(trim($request->session()->get('app_user_role_id'))),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataSalesByProduct = json_decode($responseApi)->data;

            $data_selling = $dataSalesByProduct->selling;
            $data_margin = $dataSalesByProduct->margin;
            $data_comparison = $dataSalesByProduct->comparison;
            $data_best_sales = $dataSalesByProduct->best_sales;

            $responseApi = Service::DashboardManagementSalesByDate($year, $month, $fields, $request->get('level'), $request->get('produk'),
                                strtoupper(trim($request->session()->get('app_user_id'))), strtoupper(trim($request->session()->get('app_user_role_id'))),
                                strtoupper(trim($request->session()->get('app_user_company_id'))));
            $statusApi = json_decode($responseApi)->status;
            $messageApi =  json_decode($responseApi)->message;
            if($statusApi == 1) {
                $dataSalesByDate = json_decode($responseApi)->data;

                $data_filter = new Collection();
                $data_filter->push((object) [
                    'year'      => $year,
                    'month'     => $month,
                    'fields'    => $fields,
                    'level'     => $request->get('level'),
                    'produk'    => $request->get('produk'),
                ]);

                return view('layouts.dashboard.management.dashboardmanagementsales', [
                    'title_menu'            => 'Dashboard Management Sales',
                    'data_filter'           => $data_filter->first(),
                    'selling'       => [
                        'selling_total'             => $dataSalesByProduct->selling->selling_total,
                        'selling_total_status'      => $dataSalesByProduct->selling->selling_total_status,
                        'selling_total_prosentase'  => $dataSalesByProduct->selling->selling_total_status_prosentase,
                        'selling_detail'    => [
                            'selling_pusat'   => collect($data_selling->selling_detail)->where('company', 'Pusat')->first(),
                            'selling_pc'      => collect($data_selling->selling_detail)->where('company', 'Part Center')->first(),
                            'selling_online'  => collect($data_selling->selling_detail)->where('company', 'Online')->first(),
                        ]
                    ],
                    'margin'        => [
                        'margin_total'              => $dataSalesByProduct->margin->margin_total,
                        'margin_total_prosentase'   => $dataSalesByProduct->margin->margin_total_prosentase,
                        'margin_total_status'       => $dataSalesByProduct->margin->margin_total_status,
                        'margin_total_status_prosentase'  => $dataSalesByProduct->margin->margin_total_status_prosentase,
                        'margin_detail'     => [
                            'margin_pusat'  => collect($data_margin->margin_detail)->where('company', 'Pusat')->first(),
                            'margin_pc'     => collect($data_margin->margin_detail)->where('company', 'Part Center')->first(),
                            'margin_online' => collect($data_margin->margin_detail)->where('company', 'Online')->first(),
                        ]
                    ],
                    'best_sales'    => $data_best_sales,
                    'sales_all'     => $data_selling->selling_detail,
                    'gross_profit'  => $data_margin->margin_detail,
                    'comparison'    => $data_comparison,
                    'by_product'    => $dataSalesByProduct->product,
                    'by_date'       => $dataSalesByDate,
                ]);
            } else {
                return redirect()->back()->withInput()->with('failed', $messageApi);
            }
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }
}
