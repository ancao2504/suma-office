<?php

namespace App\Http\Controllers\App\Setting;


use App\Helpers\App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingController extends Controller {

    public function clossingMarketing(Request $request) {
        $responseApi = Service::SettingClossingMarketing(strtoupper(trim($request->session()->get('app_user_company_id'))));

        return json_decode($responseApi, true);
    }
}
