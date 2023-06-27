<?php

namespace app\Http\Controllers\App\Setting\HargaNetto;

use App\Helpers\App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HargaNettoPartsControllers extends Controller
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
        $responseApi = Service::HargaNettoPartDaftar(
            $param->page??1,
            $param->per_page??10,
            $companyid,
            $role_id,
            $param->search??''

        );
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            return view(
                'layouts.settings.aturanharga.harganetto.harganettopart',
                [
                    'title_menu'    => 'Pengaturan Harga Netto Parts',
                    'data'     => $data,
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
    public function storeDestroy(Request $request)
    {
        $user_id = strtoupper(trim($request->session()->get('app_user_id')));
        $companyid = strtoupper(trim($request->session()->get('app_user_company_id')));

        $responseApi = Service::HargaNettoPartSimpan(
            trim($request->get('part_number')),
            trim($request->get('status')),
            trim(str_replace('.', '', $request->get('harga'))),
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
}
