<?php

namespace App\Http\Controllers\App\Profile;

use App\Helpers\App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;

class DealerController extends Controller
{
    public function daftarDealer(Request $request) {
        $per_page = 10;

        if((double)$request->get('per_page') == 10 || (double)$request->get('per_page') == 25 ||
            (double)$request->get('per_page') == 50 || (double)$request->get('per_page') == 100) {
            $per_page = $request->get('per_page');
        }

        $responseApi = Service::DealerDaftar($request->get('page'), $per_page, $request->get('kode_dealer'),
                            strtoupper(trim($request->session()->get('app_user_id'))),
                            strtoupper(trim($request->session()->get('app_user_role_id'))),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            $data_dealer = $data->data;

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
                'kode_dealer'   => trim($request->get('kode_dealer')),
            ]);

            $data_user = new Collection();
            $data_user->push((object) [
                'user_id'       => strtoupper(trim($request->session()->get('app_user_id'))),
                'role_id'       => strtoupper(trim($request->session()->get('app_user_role_id'))),
            ]);

            return view('layouts.profile.dealer.dealer', [
                'title_menu'    => 'Dealer',
                'data_user'     => $data_user->first(),
                'data_filter'   => $data_filter->first(),
                'data_page'     => $data_page->first(),
                'data_dealer'   => $data_dealer
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function formDealer($kode_dealer, Request $request) {
        $responseApi = Service::DealerForm($kode_dealer, strtoupper(trim($request->session()->get('app_user_id'))),
                            strtoupper(trim($request->session()->get('app_user_role_id'))),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            return view('layouts.profile.dealer.dealerform', [
                'title_menu'    => 'Dealer',
                'data'          => $data
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }
}
