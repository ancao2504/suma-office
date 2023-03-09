<?php

namespace App\Http\Controllers\app\Online;

use App\Helpers\ApiService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent as Agent;

class SerahTerimaController extends Controller
{
    public function daftarSerahTerima(Request $request) {
        if(strtoupper(trim($request->session()->get('app_user_role_id'))) == 'MD_REQ_API') {
            return redirect()->back()->withInput()->with('failed', 'Anda tidak memiliki akses untuk membuka halaman ini');
        }

        $start_date = Carbon::now()->startOfMonth()->format('Y-m-d');
        $end_date = Carbon::now()->endOfMonth()->format('Y-m-d');

        if(!empty($request->get('start_date'))) {
            $start_date = $request->get('start_date');
        }

        if(!empty($request->get('end_date'))) {
            $end_date = $request->get('end_date');
        }

        $per_page = 10;
        if(!empty($request->get('per_page')) && $request->get('per_page') != '') {
            if($request->get('per_page') == 10 || $request->get('per_page') == 25 || $request->get('per_page') == 50 || $request->get('per_page') == 100) {
                $per_page = $request->get('per_page');
            } else {
                $per_page = 10;
            }
        }

        $Agent = new Agent();
        $device = 'Desktop';
        if($Agent->isMobile()) {
            $device = 'Mobile';
        }

        $responseApi = ApiService::OnlineSerahTerimaDaftar($request->get('page'), $per_page,
                        $start_date, $end_date, $request->get('search'), strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;
            $data_serah_terima = json_decode($responseApi)->data->data;

            $data_page = new Collection();
            $data_page->push((object) [
                'from'          => $dataApi->from,
                'to'            => $dataApi->to,
                'total'         => $dataApi->total,
                'current_page'  => $dataApi->current_page,
                'per_page'      => $dataApi->per_page,
                'links'         => $dataApi->links
            ]);

            $data_filter = new Collection();
            $data_filter->push((object) [
                'start_date'    => $start_date,
                'end_date'      => $end_date,
                'search'        => trim($request->get('search')),
            ]);

            $data_device = new Collection();
            $data_device->push((object) [
                'device'    => $device
            ]);

            $data_user = new Collection();
            $data_user->push((object) [
                'user_id'       => strtoupper(trim($request->session()->get('app_user_id'))),
                'role_id'       => strtoupper(trim($request->session()->get('app_user_role_id'))),
            ]);

            return view('layouts.online.serahterima.serahterima', [
                'title_menu'        => 'Serah Terima Ekspedisi',
                'data_page'         => $data_page->first(),
                'data_filter'       => $data_filter->first(),
                'data_device'       => $data_device->first(),
                'data_user'         => $data_user->first(),
                'data_serah_terima' => $data_serah_terima,
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function formSerahTerima($nomor_dokumen, Request $request) {
        $responseApi = ApiService::OnlineSerahTerimaForm($nomor_dokumen, strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;

            return view('layouts.online.serahterima.serahterimaform', [
                'title_menu'        => 'Serah Terima Ekspedisi',
                'data'              => $dataApi
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function requestPickupPerNomorFaktur(Request $request) {
        $responseApi = ApiService::OnlineSerahTerimaRequestPickupPerNomorFaktur(strtoupper(trim($request->get('nomor_faktur'))),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        return json_decode($responseApi, true);
    }

    public function updateStatusPerNomorFaktur(Request $request) {
        $responseApi = ApiService::OnlineSerahTerimaUpdateStatusPerNomorFaktur(strtoupper(trim($request->get('nomor_faktur'))),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        return json_decode($responseApi, true);
    }


}
