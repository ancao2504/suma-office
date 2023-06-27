<?php

namespace app\Http\Controllers\App\Auth;

use Illuminate\Http\Request;
use App\Helpers\App\ServiceShopee;
use App\Http\Controllers\Controller;


class AuthShopeeController extends Controller {

    public function index() {
        $responseApi = ServiceShopee::Authorization();
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
        $responseApi = ServiceShopee::AuthorizationGenerateLink();
        return json_decode($responseApi, true);
    }

    public function simpanAccessCode(Request $request) {
        $responseApi = ServiceShopee::AuthorizationSimpan($request->get('access_code'),
            strtoupper(trim($request->session()->get('app_user_company_id'))),
            strtoupper(trim($request->session()->get('app_user_id'))));
        return json_decode($responseApi, true);
    }
}
