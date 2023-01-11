<?php

namespace App\Http\Controllers\app\Setting\Diskon;

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
        $param = json_decode(base64_decode($request->get('param')));
        $responseApi = ApiService::DiskonDealerDaftar(
            $param->page??1,
            $param->per_page??10,
            $role_id,
            $companyid,
            $param->search??''
        );
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            return view(
                'layouts.settings.aturanharga.diskon.diskondealer',
                [
                    'title_menu'    => 'Pengaturan Diskon Dealer',
                    'data_disc_dealer'     => $data,
                    'companyid'     => $companyid,
                ]
            );
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_id = strtoupper(trim($request->session()->get('app_user_id')));
        $companyid = strtoupper(trim($request->session()->get('app_user_company_id')));

        $responseApi = ApiService::DiskonDealerSimpan(
            trim($request->get('dealer')),
            trim($request->get('disc_default')),
            trim($request->get('disc_plus')),
            trim($request->get('umur_faktur')),
            trim($companyid),
            trim($user_id)
        );

        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;
        if ($statusApi == 1) {
            return redirect()->back()->with('success', $messageApi);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $companyid = strtoupper(trim($request->session()->get('app_user_company_id')));
        $responseApi = ApiService::DiskonDealerHapus(
            trim($request->get('dealer')),
            trim($companyid)
        );

        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            return redirect()->back()->with('success', $messageApi);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }
}
