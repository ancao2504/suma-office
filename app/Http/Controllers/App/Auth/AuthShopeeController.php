<?php

namespace App\Http\Controllers\App\Auth;

use App\Helpers\ApiServiceShopee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Jenssegers\Agent\Agent as Agent;


class AuthShopeeController extends Controller {

    public function index(Request $request) {
        $responseApi = ApiServiceShopee::authPartnerCek(strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;

            return view('layouts.auth.authshopee', [
                'title_menu'    => 'Auth Marketplace',
                'code'          =>  $dataApi->code,
                'access_token'  =>  $dataApi->access_token,
                'refresh_token' =>  $dataApi->refresh_token,
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }
}
