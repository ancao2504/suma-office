<?php

namespace App\Http\Controllers\app\Online\Shopee;

use App\Helpers\ApiServiceShopee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent as Agent;

class HistorySaldoController extends Controller
{
    public function index() {
        return redirect()->route('online.historysaldo.shopee.daftar');
    }

    public function daftarHistorySaldoGroup(Request $request) {
        if(strtoupper(trim($request->session()->get('app_user_role_id'))) == 'MD_REQ_API') {
            return redirect()->back()->withInput()->with('failed', 'Anda tidak memiliki akses untuk membuka halaman ini');
        }
        $start_date = Carbon::now()->addDay(-6)->format('Y-m-d');
        $end_date = Carbon::now()->format('Y-m-d');

        if(!empty($request->get('start_date'))) {
            $start_date = $request->get('start_date');
        }

        if(!empty($request->get('end_date'))) {
            $end_date = $request->get('end_date');
        }

        $per_page = 100;
        if(!empty($request->get('per_page')) && $request->get('per_page') != '') {
            if($request->get('per_page') == 10 || $request->get('per_page') == 25 || $request->get('per_page') == 50 || $request->get('per_page') == 100) {
                $per_page = $request->get('per_page');
            } else {
                $per_page = 100;
            }
        }

        $responseApi = ApiServiceShopee::HistorySaldoDaftarGroup($request->get('page'), $per_page,
                        $start_date, $end_date, strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;

            $data_filter = new Collection();
            $data_filter->push((object) [
                'start_date'    => $start_date,
                'end_date'      => $end_date,
                'view'          => 'GROUP'
            ]);

            $view = view('layouts.online.shopee.historysaldo.historysaldogroup', [
                'title_menu'    => 'History Saldo Tokopedia',
                'data_filter'   => $data_filter->first(),
                'data_saldo'    => $dataApi
            ]);

            if ($request->ajax()) {
                return [
                    'status'    => $statusApi,
                    'message'   => $messageApi,
                    'data'      => Str::between($view->render(), '<!--Start List History Saldo-->', '<!--End List History Saldo-->')
                ];
            } else {
                return $view;
            }
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function detailHistorySaldoGroup(Request $request) {
        $responseApi = ApiServiceShopee::HistorySaldoDaftarGroupDetail(strtoupper(trim($request->get('nomor_invoice'))),
                strtoupper(trim($request->session()->get('app_user_company_id'))));
        return json_decode($responseApi, true);
    }



    public function daftarHistorySaldoDetail(Request $request) {
        if(strtoupper(trim($request->session()->get('app_user_role_id'))) == 'MD_REQ_API') {
            return redirect()->back()->withInput()->with('failed', 'Anda tidak memiliki akses untuk membuka halaman ini');
        }
        $start_date = Carbon::now()->addDay(-6)->format('Y-m-d');
        $end_date = Carbon::now()->format('Y-m-d');

        if(!empty($request->get('start_date'))) {
            $start_date = $request->get('start_date');
        }

        if(!empty($request->get('end_date'))) {
            $end_date = $request->get('end_date');
        }

        $responseApi = ApiServiceShopee::HistorySaldoDaftarDetail($request->get('page'), $start_date, $end_date,
                                        strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;

            $data_filter = new Collection();
            $data_filter->push((object) [
                'start_date'    => $start_date,
                'end_date'      => $end_date,
                'view'          => 'DETAIL'
            ]);

            $view = view('layouts.online.shopee.historysaldo.historysaldodetail', [
                'title_menu'    => 'History Saldo Tokopedia',
                'data_filter'   => $data_filter->first(),
                'data_saldo'    => $dataApi
            ]);

            if ($request->ajax()) {
                return [
                    'status'    => $statusApi,
                    'message'   => $messageApi,
                    'data'      => Str::between($view->render(), '<!--Start List History Saldo-->', '<!--End List History Saldo-->')
                ];
            } else {
                return $view;
            }
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }
}
