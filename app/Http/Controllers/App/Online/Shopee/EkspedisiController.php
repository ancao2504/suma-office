<?php

namespace App\Http\Controllers\App\Online\Shopee;

use App\Helpers\ApiService;
use App\Helpers\ApiServiceShopee;
use App\Helpers\ApiServiceTokopedia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent as Agent;

class EkspedisiController extends Controller
{
    public function daftarEkspedisi() {
        $responseApi = ApiService::OptionEkspedisiOnline();
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataOptionEkspedisi = json_decode($responseApi)->data;

            $responseApi = ApiServiceShopee::EkspedisiDaftar();
            $statusApi = json_decode($responseApi)->status;
            $messageApi =  json_decode($responseApi)->message;

            if($statusApi == 1) {
                $dataApi = json_decode($responseApi)->data;

                return view('layouts.online.shopee.ekspedisi.ekspedisi', [
                    'title_menu'        => 'Ekspedisi (Logistic)',
                    'option_ekspedisi'  => $dataOptionEkspedisi,
                    'data'              => $dataApi
                ]);
            } else {
                return redirect()->back()->withInput()->with('failed', $messageApi);
            }
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function simpanEkspedisi(Request $request) {
        $responseApi = ApiServiceShopee::EkspedisiSimpan($request->get('id'), $request->get('shopee_id'),
                        $request->get('kode'), $request->get('nama'),
                        strtoupper(trim($request->session()->get('app_user_id'))));

        return json_decode($responseApi, true);
    }
}
