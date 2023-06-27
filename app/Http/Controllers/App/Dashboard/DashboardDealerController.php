<?php

namespace App\Http\Controllers\App\Dashboard;

use App\Helpers\App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardDealerController extends Controller
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

        $kode_dealer = '';
        if(strtoupper(trim($request->session()->get('app_user_role_id'))) == 'D_H3') {
            $kode_dealer = strtoupper(trim($request->session()->get('app_user_id')));
        } else {
            $kode_dealer = $request->get('kode_dealer');
        }

        $responseApi = Service::DashboardDealerPenjualanBulanan($year, $month, $kode_dealer,
                            strtoupper(trim($request->session()->get('app_user_id'))),
                            strtoupper(trim($request->session()->get('app_user_role_id'))),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data =  json_decode($responseApi)->data;

            return view('layouts.dashboard.dashboarddealer', [
                'title_menu'        => 'Dashboard Dealer',
                'year'              => $year,
                'month'             => $month,
                'role_id'           => strtoupper(trim($request->session()->get('app_user_role_id'))),
                'kode_dealer'       => $kode_dealer,
                'sisa_piutang'      => $data->sisa_piutang,
                'poin_campaign'     => $data->poin_campaign,
                'sisa_limit_piutang' => $data->sisa_limit_piutang,
                'order_on_process'  => $data->order_on_process,
                'omset_penjualan'   => $data->omset_penjualan,
                'order'             => $data->order,
                'terlayani'         => $data->terlayani,
                'bo_pcs'            => $data->bo_pcs,
                'bo_item'           => $data->bo_item,
                'detail_piutang'    => $data->pembayaran_piutang,
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }
}
