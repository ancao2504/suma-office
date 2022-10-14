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
            $companyid,
            $request->get('page'),
            $request->get('per_page'),
            $role_id,
            $request->get('search')
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
            trim($request->get('produk')),
            trim($request->get('dealer')),
            trim($request->get('keterangan')),
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
        $responseApi = ApiService::DiskonDealerHapus(
            trim($request->get('produk')),
            trim($request->get('dealer')),
            trim($request->get('cabang'))
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
