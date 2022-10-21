<?php

namespace App\Http\Controllers\App\Orders;

use App\Http\Controllers\Controller;
use App\Helpers\ApiService;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent as Agent;

class TrackingOrderController extends Controller
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

        $responseApi = ApiService::TrackingOrderDaftar($year, $month, $kode_sales, $kode_dealer, $request->get('nomor_faktur'),
                                        $request->get('page'), strtoupper(trim($request->session()->get('app_user_id'))), strtoupper(trim($request->session()->get('app_user_role_id'))),
                                        strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            $data_tracking = $data->data;

            if ($request->ajax()) {
                $view = view('layouts.orders.trackingorder.trackingorderlist', compact('data_tracking'))->render();
                return response()->json([ 'html' => $view ]);
            }

            return view('layouts.orders.trackingorder.trackingorder', [
                'title_menu'    => 'Tracking Order',
                'year'          => $year,
                'month'         => $month,
                'role_id'       => strtoupper(trim($request->session()->get('app_user_role_id'))),
                'kode_sales'    => $kode_sales,
                'kode_dealer'   => $kode_dealer,
                'nomor_faktur'  => $request->get('nomor_faktur'),
                'data_tracking' => $data_tracking
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function trackingOrderView($nomor_faktur, Request $request) {
        $responseApi = ApiService::TrackingOrderDetail(strtoupper(trim($nomor_faktur)), strtoupper(trim($request->session()->get('app_user_id'))),
                                        strtoupper(trim($request->session()->get('app_user_role_id'))), strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            $Agent = new Agent();
            $device = 'Desktop';
            if ($Agent->isMobile()) {
                $device = 'Mobile';
            }

            return view('layouts.orders.trackingorder.trackingorderform', [
                'title_menu'    => 'Tracking Order',
                'device'        => $device,
                'nomor_faktur'  => $data->nomor_faktur,
                'nomor_pof'     => $data->nomor_pof,
                'tanggal_faktur' => $data->tanggal_faktur,
                'kode_sales'    => $data->kode_sales,
                'kode_dealer'   => $data->kode_dealer,
                'kode_tpc'      => $data->kode_tpc,
                'bo'            => $data->bo,
                'total_jual'    => (double)$data->total_jual,
                'sub_total'     => (double)$data->sub_total,
                'disc_header'   => (double)$data->disc_header,
                'nominal_disc_header' => (double)$data->nominal_disc_header,
                'disc_rupiah'   => (double)$data->disc_rupiah,
                'grand_total'   => (double)$data->grand_total,
                'detail_faktur' => $data->detail_faktur,
                'detail_pengiriman' => $data->detail_pengiriman
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }
}
