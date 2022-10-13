<?php

namespace App\Http\Controllers\app\Setting;

use App\Helpers\ApiService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DiskonProdukController extends Controller
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

        $responseApi = ApiService::DiskonProdukDaftar(
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
            // dd(json_decode($responseApi)->data);
            return view(
                'layouts.settings.aturanharga.diskonproduk',
                [
                    'title_menu'    => 'Diskon Produk',
                    'data_disc'     => $data,
                    'companyid'     => $companyid,
                ]
            );
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function validasiProduk(Request $request)
    {
        $responseApi = ApiService::ValidasiProduk(trim($request->kd_produk));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            return response()->json([
                'status' => 1,
                'data' => $data,
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => $messageApi,
            ]);
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

        $responseApi = ApiService::DiskonProdukSimpan(
            trim($request->get('cabang')),
            trim($request->get('produk')),
            trim($request->get('disc_normal')),
            trim($request->get('disc_max')),
            trim($request->get('disc_plus_normal')),
            trim($request->get('disc_plus_max')),
            trim($request->get('umur_faktur')),
            trim($user_id)
        );

        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;
        if ($statusApi == 1) {
            return redirect()->back()->withInput()->with('success', $messageApi);
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

        $responseApi = ApiService::DiskonProdukHapus(
            trim($request->get('cabang')),
            trim($request->get('produk'))
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
