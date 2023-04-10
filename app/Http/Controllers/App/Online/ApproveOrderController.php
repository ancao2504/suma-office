<?php

namespace App\Http\Controllers\app\Online;

use App\Helpers\ApiService;
use App\Helpers\ApiServiceShopee;
use App\Helpers\ApiServiceTokopedia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Jenssegers\Agent\Agent as Agent;

class ApproveOrderController extends Controller
{
    public function daftarApproveOrder(Request $request) {
        $per_page = 10;
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

        $responseApi = ApiService::OnlineApproveOrderDaftar($request->get('page'), $per_page,
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;
            $data_faktur = json_decode($responseApi)->data->data;

            $data_page = new Collection();
            $data_page->push((object) [
                'from'          => $dataApi->from,
                'to'            => $dataApi->to,
                'total'         => $dataApi->total,
                'current_page'  => $dataApi->current_page,
                'per_page'      => $dataApi->per_page,
                'links'         => $dataApi->links
            ]);

            $data_filter = new Collection();
            $data_filter->push((object) [

            ]);

            $data_device = new Collection();
            $data_device->push((object) [
                'device'    => $device
            ]);

            $data_user = new Collection();
            $data_user->push((object) [
                'user_id'       => strtoupper(trim($request->session()->get('app_user_id'))),
                'role_id'       => strtoupper(trim($request->session()->get('app_user_role_id'))),
            ]);

            return view('layouts.online.approveorder.approveorder', [
                'title_menu'        => 'Serah Terima Ekspedisi',
                'data_page'         => $data_page->first(),
                'data_filter'       => $data_filter->first(),
                'data_device'       => $data_device->first(),
                'data_user'         => $data_user->first(),
                'data_faktur'       => $data_faktur,
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function formApproveTokopedia($nomor_invoice, Request $request) {
        $responseApi = ApiServiceTokopedia::OrderForm($nomor_invoice,
                strtoupper(trim($request->session()->get('app_user_company_id'))),
                strtoupper(trim($request->session()->get('app_user_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;

            return view ('layouts.online.approveorder.approveorderformtokopedia', [
                'title_menu'    => 'Orders Tokopedia',
                'tanggal'       => date('Y-m-d'),
                'data'          => $dataApi
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function formApproveShopee($nomor_invoice, Request $request) {
        $responseApi = ApiServiceShopee::OrderForm($nomor_invoice,
                strtoupper(trim($request->session()->get('app_user_company_id'))),
                strtoupper(trim($request->session()->get('app_user_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;

            return view ('layouts.online.approveorder.approveorderformshopee', [
                'title_menu'    => 'Orders Shopee',
                'tanggal'       => date('Y-m-d'),
                'data'          => $dataApi
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function formApproveInternal($nomor_faktur, Request $request) {
        $responseApi = ApiService::ApproveOrderMarketplaceDetail($nomor_faktur,
                strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;

            return view ('layouts.online.approveorder.approveorderforminternal', [
                'title_menu'    => 'Orders Internal',
                'tanggal'       => date('Y-m-d'),
                'data'          => $dataApi
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function prosesApproveMarketplace(Request $request) {
        $responseApi = ApiService::ApproveOrderMarketplaceProses($request->get('nomor_invoice'),
                strtoupper(trim($request->session()->get('app_user_company_id'))),
                strtoupper(trim($request->session()->get('app_user_id'))));
        return json_decode($responseApi, true);
    }

    public function prosesApproveInternal(Request $request) {
        $responseApi = ApiService::ApproveOrderInternalProses($request->get('nomor_faktur'),
                strtoupper(trim($request->session()->get('app_user_company_id'))),
                strtoupper(trim($request->session()->get('app_user_id'))));
        return json_decode($responseApi, true);
    }
}
