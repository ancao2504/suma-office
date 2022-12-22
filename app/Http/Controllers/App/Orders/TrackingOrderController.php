<?php

namespace App\Http\Controllers\App\Orders;

use App\Http\Controllers\Controller;
use App\Helpers\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Jenssegers\Agent\Agent as Agent;

class TrackingOrderController extends Controller
{
    public function index(Request $request) {
        $year = date('Y');
        $month = date('m');
        $kode_sales = '';
        $kode_dealer = '';

        if(!empty($request->get('year'))) {
            $year = $request->get('year');
        }
        if(!empty($request->get('month'))) {
            $month = $request->get('month');
        }

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

        $per_page = 10;
        if(!empty($request->get('per_page')) && $request->get('per_page') != '') {
            if($request->get('per_page') == 10 || $request->get('per_page') == 25 || $request->get('per_page') == 50 || $request->get('per_page') == 100) {
                $per_page = $request->get('per_page');
            } else {
                $per_page = 10;
            }
        }

        $responseApi = ApiService::TrackingOrderDaftar($request->get('page'), $per_page,
                                        $year, $month, $kode_sales, $kode_dealer, $request->get('nomor_faktur'),
                                        strtoupper(trim($request->session()->get('app_user_id'))),
                                        strtoupper(trim($request->session()->get('app_user_role_id'))),
                                        strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            $data_tracking = $data->data;

            $data_page = new Collection();
            $data_page->push((object) [
                'from'          => $data->from,
                'to'            => $data->to,
                'total'         => $data->total,
                'current_page'  => $data->current_page,
                'per_page'      => $data->per_page,
                'links'         => $data->links
            ]);

            $data_filter = new Collection();
            $data_filter->push((object) [
                'year'          => $year,
                'month'         => $month,
                'role_id'       => strtoupper(trim($request->session()->get('app_user_role_id'))),
                'kode_sales'    => $kode_sales,
                'kode_dealer'   => $kode_dealer,
                'nomor_faktur'  => $request->get('nomor_faktur'),
            ]);

            $data_user = new Collection();
            $data_user->push((object) [
                'user_id'       => strtoupper(trim($request->session()->get('app_user_id'))),
                'role_id'       => strtoupper(trim($request->session()->get('app_user_role_id'))),
            ]);

            return view('layouts.orders.trackingorder.trackingorder', [
                'title_menu'    => 'Tracking Order',
                'data_page'     => $data_page->first(),
                'data_filter'   => $data_filter->first(),
                'data_user'     => $data_user->first(),
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
                'data'          => $data
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }
}
