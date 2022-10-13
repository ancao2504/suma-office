<?php

namespace App\Http\Controllers\App\Setting;

use App\Helpers\ApiService;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class SettingController extends Controller {

    public function clossingMarketing(Request $request) {
        $responseApi = ApiService::SettingClossingMarketing(strtoupper(trim($request->session()->get('app_user_company_id'))));

        return json_decode($responseApi, true);
    }
}
