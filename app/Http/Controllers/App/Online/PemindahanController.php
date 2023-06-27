<?php

namespace app\Http\Controllers\App\Online;

use Illuminate\Support\Str;
use App\Helpers\App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class PemindahanController extends Controller
{
    
    public function daftarPemindahan(Request $request){
        // menagkap param dan mengabungkan ke varibel request
        if(!empty($request->get('param'))){
            $filter = json_decode(base64_decode($request->get('param')));
            $request->merge([
                'search'        => $filter->search,
                'start_date'    => $filter->start_date,
                'end_date'      => $filter->end_date,
                'page'          => $filter->page,
                'per_page'      => $filter->per_page
            ]);
        }

        $responseApi_SCM = Service::SettingClossingMarketing(strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi_SCM = json_decode($responseApi_SCM)->status;
        $messageApi_SCM =  json_decode($responseApi_SCM)->message;

        if($statusApi_SCM == 0 && !$request->ajax()){
            return redirect()->back()->withInput()->with('failed', $messageApi_SCM);
        } 
        if($statusApi_SCM == 0 && $request->ajax()){
            return response()->json([
                'status'    => $statusApi_SCM,
                'message'   => $messageApi_SCM,
                'data'      => ''
            ] , 400);
        }

        $dataApi_SCM =  json_decode($responseApi_SCM)->data;
        $start_date = empty($request->get('start_date'))? $dataApi_SCM->tanggal_aktif : $request->get('start_date');
        $end_date =  empty($request->get('end_date'))? Carbon::now()->format('Y-m-d') : $request->get('end_date');
        $per_page = in_array($request->get('per_page'), [10,25,50,100]) ? $request->get('per_page') : 10;

        $responseApi = Service::PemindahanMarketplaceDaftar(
            $request->get('search'),
            $start_date,
            $end_date,
            strtoupper(trim($request->session()->get('app_user_company_id'))),
            $request->get('page'),
            $per_page
        );

        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 0 && !$request->ajax()){
            return redirect()->back()->withInput()->with('failed', $messageApi);
        } 
        if($statusApi == 0 && $request->ajax()){
            return response()->json([
                'status'    => $statusApi,
                'message'   => $messageApi,
                'data'      => ''
            ] , 400);
        }

        $data_all = json_decode($responseApi)->data;

        $view = view('layouts.online.pemindahan.pemindahan', [
            'title_menu'    => 'Update Stok Antar Marketplace',
            'data_all'      => $data_all,
            'filter'        => (object)[
                'search'        => $request->get('search'),
                'start_date'    => $start_date,
                'end_date'      => $end_date,
                'page'          => $request->get('page'),
                'per_page'      => $per_page
            ]
        ]);
        
        if(!$request->ajax()){
            return  $view;
        }
        if($request->ajax()){
            return  response()->json([
                'status' => $statusApi,
                'message' => $messageApi,
                'data' => (object)[
                    'view' => Str::between($view->render(), '<!--start::container-->', '<!--end::container-->'),
                    'filter' => (object)[
                        'search'        => $request->get('search'),
                        'start_date'    => $start_date,
                        'end_date'      => $end_date,
                        'page'          => $request->get('page'),
                        'per_page'      => $per_page
                    ]
                ]
            ]);
        }
    }

    public function detailPemindahan($param, Request $request){
        $param_data = json_decode(base64_decode($param));

        $responseApi = Service::PemindahanMarketplaceDetail(
            trim($param_data->nomor_dokumen),
            strtoupper(trim($request->session()->get('app_user_company_id')))
        );
        // dd($responseApi);
        $statusApi = json_decode($responseApi)->status; 
        $messageApi = json_decode($responseApi)->message;

        if($statusApi == 0 && !$request->ajax()){
            return redirect()->route('online.pemindahan.daftar')->withInput()->with('failed', $messageApi);
        }
        if($statusApi == 0 && $request->ajax()){
            return response()->json([
                'status'    => $statusApi,
                'message'   => $messageApi,
                'data'      => ''
            ] , 400);
        }

        $data_header = json_decode($responseApi)->data??'';
        $view = view('layouts.online.pemindahan.pemindahanDetail', [
            'title_menu'    => 'Update Stok Shopee',
            'filter_header' => $param_data,
            'data_header'   => $data_header,
        ]);

        if(!$request->ajax()){
            return  $view;
        }
        if($request->ajax()){
            return  response()->json([
                'status' => $statusApi,
                'message' => $messageApi,
                'data' => (object)[
                    'view' => Str::between($view->render(), '<!--start::container-->', '<!--end::container-->'),
                ]
            ]);
        }
    }

    public function updateStock(Request $request){
        $responseApi = Service::UpdateStockMarketplace(
            $request->nomor_dokumen,
            $request->kode_part,
            strtoupper(trim($request->session()->get('app_user_company_id')))
        );
        
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if (empty($statusApi) || $statusApi == 0) {
            return response()->json([
                'status'  => 0,
                'data'    => '',
                'message' => $messageApi
            ]);
        }

        $data_all = json_decode($responseApi)->data;

        $view_respon = view('layouts.online.pemindahan.modal_respon_update', [
            'data_all'      => $data_all
        ]);

        return response()->json([
            'status' => 1,
            'message' => $messageApi,
            'data' => (object)[
                'modal_respown' => $view_respon->render(),
            ]
        ]);
    }

    public function updateStatusPerPartNumber(Request $request) {
        $responseApi = Service::PemindahanUpdateStatusMarketplacePerPartNumber(strtoupper(trim($request->get('nomor_dokumen'))),
                        trim($request->get('kode_part')), strtoupper(trim($request->session()->get('app_user_company_id'))));

        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if (empty($statusApi) || $statusApi == 0) {
            return response()->json([
                'status' => 0,
                'message' => $messageApi
            ]);
        } else {
            return response()->json([
                'status' => 1,
                'message' => $messageApi
            ]);
        }
    }
}
