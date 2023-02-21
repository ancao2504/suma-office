<?php

namespace App\Http\Controllers\app\Online\Shopee;

use App\Helpers\ApiService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent as Agent;

class UpdateHargaShopeeController extends Controller
{
    
    public function daftarUpdateHarga(Request $request) {
        $year = date('Y');
        $month = date('m');

        $per_page = 10;
        if(!empty($request->get('per_page')) && $request->get('per_page') != '') {
            // if($request->get('per_page') == 10 || $request->get('per_page') == 25 || $request->get('per_page') == 50 || $request->get('per_page') == 100) {
            //     $per_page = $request->get('per_page');
            // } else {
            //     $per_page = 10;
            // }
            
            in_array($request->get('per_page'), [10,25,50,100]) ? $per_page = $request->get('per_page') : $per_page = 10;
        }

        $Agent = new Agent();
        $device = 'Desktop';
        if($Agent->isMobile()) {
            $device = 'Mobile';
        }

        $responseApi = ApiService::SettingClossingMarketing(strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            if(!empty($request->get('year'))) {
                $year = $request->get('year');
            } else {
                $year = $data->tahun_aktif;
            }
            if(!empty($request->get('month'))) {
                $month = $request->get('month');
            } else {
                $month = $data->bulan_aktif;
            }
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }

        $responseApi = ApiService::OnlineUpdateHargaShopeeDaftar($request->get('page'), $per_page, $year, $month,
                        $request->get('search'), strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;
            $dataUpdateHarga = json_decode($responseApi)->data->data;

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
                'year'          => $year,
                'month'         => $month,
                'kode_lokasi'   => config('constants.shopee.kode_lokasi')
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

            $view = view('layouts.online.shopee.updateharga.updateharga', [
                'title_menu'        => 'Update Harga Shopee',
                'data_page'         => $data_page->first(),
                'data_filter'       => $data_filter->first(),
                'data_device'       => $data_device->first(),
                'data_user'         => $data_user->first(),
                'data_update_harga' => $dataUpdateHarga,
            ]);

            if($request->ajax()) {
                return  response()->json([
                    'status' => $statusApi,
                    'message' => $messageApi,
                    'data' => Str::between($view->render(), '<!--start::container-->', '<!--end::container-->')
                ]);
            } else {
                return $view;
            }
        } else {
            if($request->ajax()) {
                return  response()->json([
                    'status' => $statusApi,
                    'message' => $messageApi,
                    'data' => ''
                ]);
            } else {
                return redirect()->back()->withInput()->with('failed', $messageApi);
            }
        }
    }
    
    public function buatDokumen(Request $request) {
        $responseApi = ApiService::OnlineUpdateHargaShopeeBuatDokumen($request->get('kode'), date('Y-m-d'),
                        strtoupper(trim($request->session()->get('app_user_company_id'))),
                        strtoupper(trim($request->session()->get('app_user_id'))));
        return json_decode($responseApi, true);
    }

    public function formUpdateHarga($param, Request $request) {
        $data_param = base64_decode($param);
        $data_param = json_decode($data_param, true);

        $responseApi = ApiService::OnlineUpdateHargaShopeeForm($data_param['nomor_dokumen'],
                        strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;
            
            $view = view('layouts.online.shopee.updateharga.updatehargaform', [
                'title_menu'    => 'Update Harga Shopee',
                'filter_old'    => $data_param['filter'],
                'dataApi'          => $dataApi
            ]);

            if($request->ajax()) {
                return  response()->json([
                    'status' => $statusApi,
                    'message' => $messageApi,
                    'data' => Str::between($view->render(), '<!--start::container-->', '<!--end::container-->')
                ]);
            } else {
                return $view;
            }

        } else {
            if($request->ajax()) {
                return  response()->json([
                    'status' => $statusApi,
                    'message' => $messageApi,
                    'data' => ''
                ]);
            } else {
                return redirect()->back()->withInput()->with('failed', $messageApi);
            }
        }
    }

    public function updateHargaPerPartNumber(Request $request) {
        $responseApi = ApiService::OnlineUpdateHargaShopeeUpdatePerPartNumber(strtoupper(trim($request->get('nomor_dokumen'))),
                                trim($request->get('part_number')), strtoupper(trim($request->session()->get('app_user_company_id'))));

        return json_decode($responseApi, true);
    }

    public function updateHargaStatusPerPartNumber(Request $request) {
        $responseApi = ApiService::OnlineUpdateHargaShopeeUpdateStatusPartNumber(strtoupper(trim($request->get('nomor_dokumen'))),
                trim($request->get('part_number')), strtoupper(trim($request->session()->get('app_user_company_id'))));

        return json_decode($responseApi, true);
    }
}
