<?php

namespace App\Http\Controllers\App\Profile;

use App\Helpers\ApiService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DealerController extends Controller
{
    public function index(Request $request) {
        $responseApi = ApiService::DealerDaftar($request->get('page'), $request->get('search'), strtoupper(trim($request->session()->get('app_user_id'))),
                            strtoupper(trim($request->session()->get('app_user_role_id'))), strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            $data_dealer = $data->data;

            if ($request->ajax()) {
                $view = view('layouts.profile.dealer.dealerlist', compact('data_dealer'))->render();
                return response()->json([ 'html' => $view ]);
            }

            return view('layouts.profile.dealer.dealer', [
                'title_menu'    => 'Dealer',
                'kode_dealer'   => $request->get('search'),
                'data_dealer'   => $data_dealer
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function dealerProfile($kode_dealer, Request $request) {
        $responseApi = ApiService::DealerProfile($kode_dealer, strtoupper(trim($request->session()->get('app_user_id'))),
                            strtoupper(trim($request->session()->get('app_user_role_id'))), strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            return view('layouts.profile.dealer.dealerprofile', [
                'title_menu'    => 'Dealer',
                'kode_dealer'   => trim($data->kode_dealer),
                'nama_dealer'   => trim($data->nama_dealer),
                'cabang'        => trim($data->cabang),
                'kode_sales'    => trim($data->kode_sales),
                'nama_sales'    => trim($data->nama_sales),
                'kode_area'     => trim($data->kode_area),
                'nama_area'     => trim($data->nama_area),
                'npwp'          => trim($data->npwp),
                'ktp'           => trim($data->ktp),
                'alamat'        => trim($data->alamat),
                'kabupaten'     => trim($data->kabupaten),
                'karesidenan'   => trim($data->karesidenan),
                'kota'          => trim($data->kota),
                'telepon'       => trim($data->telepon),
                'email'         => trim($data->email),
                'sts'           => trim($data->sts),
                'status'        => trim($data->status),
                'nama_dealer_sj' => trim($data->nama_dealer_sj),
                'alamat_dealer_sj' => trim($data->alamat_dealer_sj),
                'kota_dealer_sj' => trim($data->kota_dealer_sj),
                'keterangan_limit' => trim($data->keterangan_limit),
                'sisa_limit'    => trim($data->sisa_limit),
                'status_limit'  => trim($data->status_limit),
                'latitude'      => trim($data->latitude),
                'longitude'     => trim($data->longitude),
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }
}
