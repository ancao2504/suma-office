<?php

namespace App\Http\Controllers\app\Online\Tokopedia;

use App\Helpers\ApiService;
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

            $responseApi = ApiServiceTokopedia::EkspedisiDaftar();
            $statusApi = json_decode($responseApi)->status;
            $messageApi =  json_decode($responseApi)->message;

            if($statusApi == 1) {
                $dataApi = json_decode($responseApi)->data;

                return view('layouts.online.tokopedia.ekspedisi.ekspedisi', [
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
        $responseApi = ApiServiceTokopedia::EkspedisiSimpan($request->get('id'), $request->get('tokopedia_id'),
                            $request->get('kode'), $request->get('nama'),
                            strtoupper(trim($request->session()->get('app_user_id'))));

        return json_decode($responseApi, true);
    }
}
