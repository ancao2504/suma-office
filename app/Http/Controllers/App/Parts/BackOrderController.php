<?php

namespace App\Http\Controllers\App\Parts;

use App\Http\Controllers\Controller;
use App\Helpers\ApiService;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent as Agent;

class BackOrderController extends Controller
{
    public function index(Request $request) {
        $user_id = strtoupper(trim($request->session()->get('app_user_id')));
        $role_id = strtoupper(trim($request->session()->get('app_user_role_id')));
        $companyid = strtoupper(trim($request->session()->get('app_user_company_id')));

        $kode_sales = '';
        $kode_dealer = '';

        if(strtoupper(trim($request->session()->get('app_user_role_id'))) == "D_H3") {
            $responseApi = ApiService::ValidasiDealer(strtoupper(trim($request->session()->get('app_user_id'))),
                                strtoupper(trim($request->session()->get('app_user_company_id'))));
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

        $responseApi = ApiService::BackOrderDaftar($kode_sales, $kode_dealer, $request->get('part_number'),
                                $request->get('page'), $user_id, $role_id, $companyid);
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            $data_bo = $data->data;

            $Agent = new Agent();

            if($request->ajax()) {
                if($Agent->isMobile()) {
                    $view = view('layouts.parts.backorder.mobile.backorderlist', compact('data_bo'))->render();
                } else {
                    $view = view('layouts.parts.backorder.desktop.backorderlist', compact('data_bo'))->render();
                }
                return response()->json([ 'html' => $view ]);
            }

            $device = '';
            if($Agent->isMobile()) {
                $device = 'Mobile';
            } else {
                $device = 'Desktop';
            }

            return view ('layouts.parts.backorder.backorder', [
                'title_menu'    => 'Back Order',
                'device'        => $device,
                'role_id'       => trim($request->session()->get('app_user_role_id')),
                'kode_sales'    => trim($kode_sales),
                'kode_dealer'   => trim($kode_dealer),
                'part_number'   => trim($request->get('part_number')),
                'data_bo'       => $data_bo
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }
}
