<?php

namespace App\Http\Controllers\App\Parts;

use App\Helpers\App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent as Agent;

class BackOrderController extends Controller
{
    public function index(Request $request) {
        $Agent = new Agent();
        $device = '';
        if($Agent->isMobile()) {
            $device = 'Mobile';
        } else {
            $device = 'Desktop';
        }

        $per_page = 12;
        if((double)$request->get('per_page') == 12 || (double)$request->get('per_page') == 28 ||
            (double)$request->get('per_page') == 56 || (double)$request->get('per_page') == 112) {
            $per_page = $request->get('per_page');
        }

        $user_id = strtoupper(trim($request->session()->get('app_user_id')));
        $role_id = strtoupper(trim($request->session()->get('app_user_role_id')));
        $companyid = strtoupper(trim($request->session()->get('app_user_company_id')));

        $kode_sales = '';
        $kode_dealer = '';

        if(strtoupper(trim($request->session()->get('app_user_role_id'))) == "D_H3") {
            $responseApi = Service::ValidasiDealer(strtoupper(trim($request->session()->get('app_user_id'))),
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

        $responseApi = Service::BackOrderDaftar($request->get('page'), $per_page,
                            $kode_sales, $kode_dealer, $request->get('part_number'),
                            $user_id, $role_id, $companyid);
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            $data_bo = $data->data;

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
                'salesman'      => trim($kode_sales),
                'dealer'        => trim($kode_dealer),
                'part_number'   => trim($request->get('part_number')),
            ]);

            $data_user = new Collection();
            $data_user->push((object) [
                'user_id'   => strtoupper(trim($request->session()->get('app_user_id'))),
                'role_id'   => strtoupper(trim($request->session()->get('app_user_role_id'))),
            ]);

            $data_device = new Collection();
            $data_device->push((object) [
                'device'    => $device
            ]);

            return view ('layouts.parts.backorder.backorder', [
                'title_menu'    => 'Back Order',
                'data_device'   => $data_device->first(),
                'data_page'     => $data_page->first(),
                'data_filter'   => $data_filter->first(),
                'data_user'     => $data_user->first(),
                'data_bo'       => $data_bo
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }
}
