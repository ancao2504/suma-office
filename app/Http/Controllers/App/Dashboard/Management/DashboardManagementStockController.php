<?php

namespace App\Http\Controllers\App\Dashboard\Management;

use App\Helpers\ApiRequest;
use App\Http\Controllers\App\Dashboard\DashboardSalesmanController;
use App\Http\Controllers\Controller;
use App\Helpers\ApiService;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Cast\Double;

class DashboardManagementStockController extends Controller
{
    public function index(Request $request) {
        $year = date('Y');
        $month = date('m');
        $fields = 'AMOUNT';

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

        $responseApi = ApiService::DashboardManagementStockByProduct($year, $month, $fields, $request->get('level'), $request->get('produk'),
                            strtoupper(trim($request->session()->get('app_user_id'))), strtoupper(trim($request->session()->get('app_user_role_id'))),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataStockByProduct = json_decode($responseApi)->data;

            return view('layouts.dashboard.management.dashboardmanagementstock', [
                'title_menu'    => 'Dashboard Management Stock',
                'year'          => $year,
                'month'         => $month,
                'fields'        => $fields,
                'produk'        => $request->get('produk'),
                'level'         => $request->get('level'),
                'stock_total'   => $dataStockByProduct->total_stock,
                'pembelian'     => $dataStockByProduct->pembelian,
                'total'         => $dataStockByProduct->company,
                'fs'            => $dataStockByProduct->fs,
                'cno'           => $dataStockByProduct->cno,
                'product'       => $dataStockByProduct->product,
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }
}
