<?php

namespace App\Http\Controllers\App\Auth;

use App\Helpers\ApiServiceShopee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Jenssegers\Agent\Agent as Agent;


class AuthShopeeController extends Controller {

    public function index() {
        $responseApi = ApiServiceShopee::Authorization();
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;

            return view('layouts.auth.authshopee', [
                'title_menu'    => 'Auth Marketplace',
                'refresh_token' =>  (empty($dataApi->refresh_token)) ? '' : $dataApi->refresh_token,
                'access_token'  =>  (empty($dataApi->access_token)) ? '' : $dataApi->access_token,
                'code'          =>  (empty($dataApi->code)) ? '' : $dataApi->code,
                'date_process'  =>  (empty($dataApi->date_process)) ? '' : $dataApi->date_process,
                'user_process'  =>  (empty($dataApi->user_process)) ? '' : $dataApi->user_process,
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function generateLink() {
        $responseApi = ApiServiceShopee::AuthorizationGenerateLink();
        return json_decode($responseApi, true);
    }

    public function simpanAccessCode(Request $request) {
        $responseApi = ApiServiceShopee::AuthorizationSimpan($request->get('access_code'),
            strtoupper(trim($request->session()->get('app_user_company_id'))),
            strtoupper(trim($request->session()->get('app_user_id'))));
        return json_decode($responseApi, true);
    }
}
