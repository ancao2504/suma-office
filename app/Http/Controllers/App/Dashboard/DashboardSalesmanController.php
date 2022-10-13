<?php

namespace App\Http\Controllers\App\Dashboard;

use App\Http\Controllers\Controller;
use App\Helpers\ApiService;

use Illuminate\Http\Request;
use Jenssegers\Agent\Agent as Agent;

class DashboardSalesmanController extends Controller
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

        $responseApi = ApiService::DashboardSalesmanPenjualanBulanan($year, $month, $request->get('role_salesman'),
                $request->get('kode_sales_spv'), strtoupper(trim($request->session()->get('app_user_id'))),
                strtoupper(trim($request->session()->get('app_user_role_id'))),
                strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataSalesMonth = json_decode($responseApi)->data;

            $responseApi = ApiService::DashboardSalesmanPenjualanHarian($year, $month, $request->get('role_salesman'),
                            $request->get('kode_sales_spv'), strtoupper(trim($request->session()->get('app_user_id'))), strtoupper(trim($request->session()->get('app_user_role_id'))),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));

            if($statusApi == 1) {
                $dataDailySales = json_decode($responseApi)->data;

                $Agent = new Agent();
                $device = 'Desktop';
                if ($Agent->isMobile()) {
                    $device = 'Mobile';
                }

                return view('layouts.dashboard.dashboardsalesman', [
                    'title_menu'                        => 'Dashboard Salesman',
                    'device'                            => $device,
                    'month'                             => $dataSalesMonth->month,
                    'year'                              => $dataSalesMonth->year,
                    'role_id'                           => $dataSalesMonth->role_id,
                    'role_salesman'                     => $dataSalesMonth->role_salesman,
                    'kode_sales_spv'                    => $dataSalesMonth->kode_sales_spv,
                    'target_amount_total'               => (double)$dataSalesMonth->target_amount_total,
                    'target_amount_keterangan'          => $dataSalesMonth->target_amount_keterangan,
                    'target_amount_prosentase'          => (double)$dataSalesMonth->target_amount_prosentase,
                    'penjualan_amount_total'            => (double)$dataSalesMonth->penjualan_amount_total,
                    'penjualan_amount_keterangan'       => $dataSalesMonth->penjualan_amount_keterangan,
                    'penjualan_amount_prosentase'       => (double)$dataSalesMonth->penjualan_amount_prosentase,
                    'retur_amount_total'                => (double)$dataSalesMonth->retur_amount_total,
                    'retur_amount_keterangan'           => $dataSalesMonth->retur_amount_keterangan,
                    'retur_amount_prosentase'           => (double)$dataSalesMonth->retur_amount_prosentase,
                    'omset_amount_total'                => (double)$dataSalesMonth->omset_amount_total,
                    'omset_amount_keterangan'           => $dataSalesMonth->omset_amount_keterangan,
                    'omset_amount_prosentase'           => (double)$dataSalesMonth->omset_amount_prosentase,
                    'prosentase_amount_total'           => (double)$dataSalesMonth->prosentase_amount_total,
                    'detail_group_per_level'            => $dataSalesMonth->detail_group_per_level,
                    'detail_omset'                      => $dataSalesMonth->detail_omset,
                    'detail_daily'                      => $dataDailySales
                ]);
            } else {
                return redirect()->back()->withInput()->with('failed', $messageApi);
            }
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }
}
