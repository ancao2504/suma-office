<?php

namespace App\Http\Controllers\app\Setting;

use App\Helpers\ApiService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DiskonDealerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role_id = strtoupper(trim($request->session()->get('app_user_role_id')));
        $companyid = strtoupper(trim($request->session()->get('app_user_company_id')));

        $responseApi = ApiService::DiskonDealerDaftar(
            $request->get('page'),
            $request->get('per_page'),
            $companyid,
            $role_id,
            // $request->get('search')
        );
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            return view(
                'layouts.settings.aturanharga.diskondealer',
                [
                    'title_menu'    => 'Diskon Dealer',
                    'data_disc'     => $data,
                    'companyid'     => $companyid,
                ]
            );
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }
}
