<?php

namespace app\Http\Controllers\App\Online\Tokopedia;

use App\Helpers\App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\App\ServiceTokopedia;

class EkspedisiController extends Controller
{
    public function daftarEkspedisi() {
        $responseApi = Service::OptionEkspedisiOnline();
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataOptionEkspedisi = json_decode($responseApi)->data;

            $responseApi = ServiceTokopedia::EkspedisiDaftar();
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
        $responseApi = ServiceTokopedia::EkspedisiSimpan($request->get('id'), $request->get('tokopedia_id'),
                            $request->get('kode'), $request->get('nama'),
                            strtoupper(trim($request->session()->get('app_user_id'))));

        return json_decode($responseApi, true);
    }
}
