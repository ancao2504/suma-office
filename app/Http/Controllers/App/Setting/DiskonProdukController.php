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
                ]
            );
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function cekDiskonProduk(Request $request)
    {
        $responseApi = ApiService::ValidasiDiskonProduk(trim($request->kd_produk), trim($request->cabang));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            if ($messageApi == 'success') {
                return response()->json([
                    'status' => 0,
                ]);
            } else {
                $data = json_decode($responseApi)->data;
                return response()->json([
                    'status' => 1,
                    'message' => 'Data Diskon Produk : ' . trim($data->kode_produk) . ' Cabang : ' . trim($data->cabang) . ' sudah ada, apakah anda ingin mengubahnya ?',
                    'data' => $data,
                ]);
            }
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
        // dd($request->all());
        $user_id = strtoupper(trim($request->session()->get('app_user_id')));

        if (in_array(trim($request->get('cabang')), array('RK', 'PC'))) {
            $cabang = strtoupper(trim($request->get('cabang')));
        } else {
            return redirect()->back()->withInput()->with('failed', 'Cabang tidak valid');
        }

        $responseApi = ApiService::DiskonProdukSimpan(
            trim($cabang),
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
