<?php

namespace App\Http\Controllers\app\Online\Shopee;

use Carbon\Carbon;
use App\Helpers\ApiService;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Svg\Tag\Rect;

class PemindahanShopeeController extends Controller
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

        $responseApi_SCM = ApiService::SettingClossingMarketing(strtoupper(trim($request->session()->get('app_user_company_id'))));
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

        $responseApi = ApiService::OnlinePemindahanShopeeDaftar(
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

        $view = view('layouts.online.shopee.pemindahan.pemindahan', [
            'title_menu'    => 'Update Stok Shopee',
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

        // $view_content = '';
        // $page_view='';
        
        // if($Agent->isDesktop()) {
        //     $view_content ='
        //         <div class="table-responsive" id="tabel">
        //                 <table class="table table-row-dashed table-row-gray-300 align-middle">
        //                     <thead class="border">
        //                         <tr rowspan="2" class="fs-8 fw-bolder text-muted">
        //                             <th rowspan="2" class="w-50px ps-3 pe-3 text-center">No</th>
        //                             <th rowspan="2" class="w-100px ps-3 pe-3 text-center">No Dokumen</th>
        //                             <th rowspan="2" class="w-50px ps-3 pe-3 text-center">Lokasi Awal</th>
        //                             <th rowspan="2" class="w-50px ps-3 pe-3 text-center">Lokasi Tujuan</th>
        //                             <th rowspan="2" class="w-150px ps-3 pe-3 text-center">Keterangan</th>
        //                             <th rowspan="2" class="w-50px ps-3 pe-3 text-center">User</th>
        //                             <th rowspan="2" class="w-50px ps-3 pe-3 text-center">Sts Cetak</th>
        //                             <th rowspan="2" class="w-50px ps-3 pe-3 text-center">Sts SJ</th>
        //                             <th rowspan="2" class="w-50px ps-3 pe-3 text-center">Sts Validasi</th>
        //                             <th rowspan="2" class="w-50px ps-3 pe-3 text-center">Sts Marketplace</th>
        //                             <th colspan="2" class="w-150px ps-3 pe-3 text-center">Action</th>
        //                         </tr>
        //                         <tr class="fs-8 fw-bolder text-muted">
        //                             <th class="w-50px ps-3 pe-3 text-center">Update</th>
        //                             <th class="w-50px ps-3 pe-3 text-center">Detail</th>
        //                         </tr>
        //                     </thead>
        //                 <tbody class="border">
        //         ';
        //         $view_tbBoady = '';
        //     if($statusApi == 1) {
        //         if ($data_all->total > 0){
        //             $no = $data_all->from;
        //             foreach($data_all->data as $data){
        //                 $page_detail_data = json_encode([
        //                     'nomor_dokumen' => $data->nomor_dokumen,
        //                     'filter' => [
        //                                     'search'        => $request->get('search'),
        //                                     'start_date'    => $request->get('start_date'),
        //                                     'end_date'      => $request->get('end_date'),
        //                                     'page'          => $request->get('page'),
        //                                     'per_page'      => $request->get('per_page'),
        //                                     'marketplace'   => $data->status_marketplace
        //                                 ]
        //                 ]);
        //                 $view_tbBoady .='
        //                     <tr class="fs-6 fw-bold text-gray-700" data-no="'.$data->nomor_dokumen.'">
        //                         <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
        //                             <span class="fs-7 fw-bolder text-gray-800">'.$no.'</span>
        //                         </td>
        //                         <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
        //                             <span class="fs-7 fw-bolder text-gray-800 d-block">'.$data->nomor_dokumen.'</span>
        //                             <span class="fs-8 fw-bolder text-gray-600">'.date('d F Y', strtotime($data->tanggal)).'</span>
        //                         </td>
        //                         <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
        //                             <span class="fs-7 fw-bolder '.(($data->lokasi_awal=='OS')?'text-success':'text-muted').'">'.$data->lokasi_awal.'</span>
        //                         </td>
        //                         <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
        //                             <span class="fs-7 fw-bolder '.(($data->lokasi_tujuan=='OS')?'text-success':'text-muted').'">'.$data->lokasi_tujuan.'</span>
        //                         </td>
        //                         <td class="ps-3 pe-3" style="text-align:'.(!empty($data->keterangan)?'left':'center').';vertical-align:top;">
        //                             <span class="fs-7 fw-bolder text-muted">'.(!empty($data->keterangan)?$data->keterangan:'-').'</span>
        //                         </td>
        //                         <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
        //                             <span class="fs-7 fw-bolder text-dark">'.(!empty($data->usertime)?explode('=',$data->usertime)[2]:'-').'</span>
        //                         </td>
        //                         <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
        //                             '.($data->status_cetak==1?'<i class="bi bi-check text-success fs-1"></i>':'-').'
        //                         </td>
        //                         <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
        //                             '.($data->status_sj==1?'<i class="bi bi-check text-success fs-1"></i>':'-').'
        //                         </td>
        //                         <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
        //                             '.($data->validasi==1?'<i class="bi bi-check text-success fs-1"></i>':'-').'
        //                         </td>
        //                         <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
        //                             '.($data->status_marketplace==1?'<i class="bi bi-check text-success fs-1"></i>':'-').'
        //                         </td>
        //                         <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
        //                             '.($data->status_marketplace==1?'<span class="fs-7 fw-bolder badge badge-light-success">success</span>':'<button class="btn btn-sm btn-light-dark btn-hover-rise btn_detail" data-focus="0"><img alt="Logo" src="'.asset('assets/images/logo/shopee.png').'" class="h-20px"/></button>').'
        //                         </td>
        //                         <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
        //                             <a href="'.route('online.pemindahan.shopee.detail-index', base64_encode($page_detail_data)).'" class="btn btn-sm btn-primary" data-focus="0">
        //                             Detail
        //                             </a>
        //                         </td>
        //                     </tr>
        //                 ';
        //                 $no++;
        //             }

        //         } else {
        //             $view_tbBoady = '
        //                 <tr class="odd">
        //                     <td class="fw-bold text-center" colspan="10"> Data Tidak Ditemukan </td>
        //                 </tr>
        //             ';
        //         }
        //     } else {
        //         $view_tbBoady = '
        //             <tr class="odd">
        //             <td class="fw-bold text-center" colspan="10">'.$messageApi.'</td>
        //             </tr>
        //         ';
        //     }
        //     $view_content .= $view_tbBoady;
        //     $view_content .='
        //                 </tbody>
        //             </table>
        //         </div>
        //         ';
                
        // } else {
        //     if($statusApi == 1) {
        //         if ($data_all->total > 0){
        //             foreach($data_all->data as $data){
        //                 $page_detail_data = json_encode([
        //                     'nomor_dokumen' => $data->nomor_dokumen,
        //                     'filter' => [
        //                                     'search'        => $request->get('search'),
        //                                     'start_date'    => $request->get('start_date'),
        //                                     'end_date'      => $request->get('end_date'),
        //                                     'page'          => $request->get('page'),
        //                                     'per_page'      => $request->get('per_page'),
        //                                     'marketplace'   => $data->status_marketplace
        //                                 ]
        //                 ]);
        //                 $view_content .=
        //                     '
        //                     <div class="card card-flush mt-6" id="tabel">
        //                         <div class="card-body pt-5">
        //                             <div class="row mt-4">
        //                                 <div class="col-6 col-lg-6">
        //                                     <span class="fw-bold fs-7 text-gray-600 d-block">Nomor Dokumen:</span>
        //                                     <span class="fw-bolder text-dark mt-1 d-block">'.$data->nomor_dokumen.'</span>
        //                                     <span class="fw-bold fs-7 text-gray-600 mt-6 d-block">Tanggal:</span>
        //                                     <span class="fw-bolder text-dark mt-1 d-block">'.date('d/m/Y', strtotime($data->tanggal)).'</span>
        //                                 </div>
        //                             </div>
        //                             <div class="row mt-6">
        //                                 <div class="col-12">
        //                                     <div class="row">
        //                                         <div class="col-4">
        //                                             <span class="fw-bold fs-7 text-gray-600 d-block">Lokasi Awal:</span>
        //                                             <span class="fw-bolder text-dark">'.$data->lokasi_awal.'</span>
        //                                         </div>
        //                                         <div class="col-1">
        //                                             <i class="bi bi-arrow-right fs-1"></i>
        //                                         </div>
        //                                         <div class="col-4">
        //                                             <span class="fw-bold fs-7 text-gray-600 d-block">Lokasi Tujuan:</span>
        //                                             <span class="fw-boldest text-success text-uppercase">'.$data->lokasi_tujuan.'</span>
        //                                         </div>
        //                                     </div>
        //                                 </div>
        //                             </div>
        //                             <div class="row mt-6">
        //                                 <div class="col-4">
        //                                     <span class="fw-bold fs-7 text-gray-600 d-block">Sts Cetak:</span>
        //                                     '.($data->status_cetak==1?'<span class="badge badge-success"><i class="bi bi-check text-white fs-3"></i></span>':'-').'
        //                                 </div>
        //                                 <div class="col-4">
        //                                     <span class="fw-bold fs-7 text-gray-600 d-block">Sts in:</span>
        //                                     '.($data->status_in==1?'<span class="badge badge-success"><i class="bi bi-check text-white fs-3"></i></span>':'-').'
        //                                 </div>
        //                                 <div class="col-4">
        //                                     <span class="fw-bold fs-7 text-gray-600 d-block">Sts MKPlace:</span>
        //                                     '.($data->status_marketplace==1?'<span class="badge badge-success"><i class="bi bi-check text-white fs-3"></i></span>':'-').'
        //                                 </div>
        //                             </div>
        //                             <div class="separator my-5"></div>
        //                             <div class="text-end">
        //                                 <a href="'.route('online.pemindahan.shopee.detail-index', base64_encode($page_detail_data)).'" class="btn btn-light-dark btn-hover-rise btn_detail" data-focus="0">
        //                                 Update <img alt="Logo" src="'.asset('assets/images/logo/shopee.png').'" class="h-20px me-3"/>
        //                                 </a>
        //                             </div>
        //                         </div>
        //                     </div>';
        //             } 
        //         } else {
        //             $view_content =
        //             '
        //             <div class="card card-flush mt-6" id="tabel">
        //                 <div class="card-body pt-5 text-center">
        //                     <span class="fs-7 fw-bolder text-gray-800"> Data Tidak Ditemukan </span>
        //                 </div>
        //             </div>';
        //         }
        //     } else {
        //         $view_content =
        //             '
        //             <div class="card card-flush mt-6" id="tabel">
        //                 <div class="card-body pt-5 text-center">
        //                     <span class="fs-7 fw-bolder text-danger">'.$messageApi.'</span>
        //                 </div>
        //             </div>';
        //     }
        // }

        // if($statusApi == 1) {
        //     $page_view .='<ul class="pagination" data-current_page="'.$data_all->current_page.'">';
        //     foreach ($data_all->links as $data){
        //         if (strpos($data->label, 'Next') !== false){
        //         $page_view .='<li class="page-item next'.($data->url == null?'disabled':'').'">
        //                 <a role="button" data-page="'. (string)((int)($data_all->current_page) + 1) .'" class="page-link">
        //                     <i class="next"></i>
        //                 </a>
        //             </li>';
        //         } elseif (strpos($data->label, 'Previous') !== false) {
        //         $page_view .='<li class="page-item previous '.($data->url == null?'disabled':'').'">
        //                 <a role="button" data-page="'. (string)((int)($data_all->current_page) - 1) .'" class="page-link">
        //                     <i class="previous"></i>
        //                 </a>
        //             </li>';
        //         } elseif ($data->active == true) {
        //         $page_view .='<li class="page-item active '.($data->url == null?'disabled':'').'">
        //                 <a role="button" data-page="'. $data->label .'" class="page-link">'. $data->label .'</a>
        //             </li>';
        //         }elseif ($data->active == false){
        //             $page_view .='<li class="page-item '.($data->url == null?'disabled':'').'">
        //                 <a role="button" data-page="'. $data->label .'" class="page-link">'.$data->label.'</a>
        //             </li>';
        //         }
        //     }
        //     $page_view .='</ul>';
        // }

        // return response()->json([
        //     'status'    => $statusApi,
        //     'message'   => $messageApi,
        //     'table'     => $view_content,
        //     'pagination'=> $page_view
        // ] , 200);
    }

    public function detailPemindahan($param, Request $request){
        $param_data = json_decode(base64_decode($param));
        $responseApi = ApiService::OnlinePemindahanShopeeDetail(
            trim($param_data->nomor_dokumen),
            strtoupper(trim($request->session()->get('app_user_company_id')))
        );
        
        $statusApi = json_decode($responseApi)->status; 
        $messageApi = json_decode($responseApi)->message;

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

        $data_header = json_decode($responseApi)->data??'';
        $view = view('layouts.online.shopee.pemindahan.pemindahanDetail', [
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

        // $Agent = new Agent();
        // $view_table = '';
        // $view_footer = '';
        
        // if(!empty($statusApi) && $statusApi == 1) {
        //     // jika mobule maka card jika tidak maka kosong
        //     $view_table .='
        //     <div class="row mb-3 table_delete '.($Agent->isMobile()? 'bg-white': '').'">
        //         <div class="col-6">
        //             <div class="fw-bolder text-gray-400">Nomor Dokumen :</div>
        //             <div class="fs-3 fw-bolder text-gray-800">'.$data_header->nomor_dokumen.'</div>
        //         </div>
        //         <div class="col-6">
        //             <div class="fw-bolder text-gray-400">Tanggal :</div>
        //             <div class="fs-5 fw-bolder text-gray-800">'.date("d F Y", strtotime($data_header->tanggal)).'</div>
        //         </div>
        //         <div class="col-6 mt-5">
        //             <div class="fw-bolder text-gray-400">Dari :</div>
        //             <div class="fs-4 fw-bolder text-gray-800">'.$data_header->lokasi_awal->kode_lokasi.' - '.$data_header->lokasi_awal->nama_lokasi.'</div>
        //             <div class="fs-6 fw-bolder text-gray-800">'.$data_header->lokasi_awal->alamat1 .' '. $data_header->lokasi_awal->alamat2.'</div>
        //             <div class="fs-6 fw-bolder text-gray-800">'.$data_header->lokasi_awal->kota.'</div>
        //         </div>
        //         <div class="col-6 mt-5">
        //             <div class="fw-bolder text-gray-400">Ke :</div>
        //             <div class="fs-4 fw-bolder text-gray-800">'.$data_header->lokasi_tujuan->kode_lokasi.' - '.$data_header->lokasi_tujuan->nama_lokasi.'</div>
        //             <div class="fs-6 fw-bolder text-gray-800">'.$data_header->lokasi_tujuan->alamat1 .' '. $data_header->lokasi_tujuan->alamat2.'</div>
        //             <div class="fs-6 fw-bolder text-gray-800">'.$data_header->lokasi_tujuan->kota.'</div>
        //         </div>
        //         <div class="col-6 mt-5">
        //             <div class="fw-bolder text-gray-400">Keterangan :</div>
        //             <div class="fs-6 fw-bolder text-gray-800">'.(!empty($data_header->keterangan)?$data_header->keterangan:'-' ).'</div>
        //         </div>
        //         <div class="col-6 mt-5">
        //             <div class="fw-bolder text-gray-400">User :</div>
        //             <div class="fs-6 fw-bolder text-gray-800">'.(!empty($data_header->usertime)?explode('=',$data_header->usertime)[2]:'-' ).'</div>
        //         </div>
        //         <div class="col-12 mt-5">
        //             <div class="fw-bolder text-gray-400">Status :</div>
        //             <span class="badge badge-'.($data_header->status_cetak==1?'success':'danger').' p-2">
        //                 <div class="fs-5 fw-bolder text-white">Cetak</div>
        //             </span>
        //             <span class="badge badge-'.($data_header->status_in==1?'success':'danger').' p-2">
        //                 <div class="fs-5 fw-bolder text-white">In</div>
        //             </span>
        //             <span class="badge badge-'.($data_header->status_sj==1?'success':'danger').' p-2">
        //                 <div class="fs-5 fw-bolder text-white">SJ</div>
        //             </span>
        //             <span class="badge badge-'.((!empty($data_header->validasi) && $data_header->validasi == 1)?'success':'danger').' p-2">
        //                 <div class="fs-5 fw-bolder text-white">Validasi</div>
        //             </span>
        //             <span class="badge badge-'.($data_header->status_marketplace==1?'success':'danger').' p-2">
        //                 <div class="fs-5 fw-bolder text-white">Marketplace</div>
        //             </span>
        //         </div>
        //     </div>';
        // }

        // if($Agent->isDesktop()){
        //     $view_table .='
        //         <div class="table-responsive table_delete">
        //             <table class="table table-row-dashed table-row-gray-300 align-middle">
        //                 <thead class="border">
        //                     <tr class="fs-8 fw-bolder text-muted">
        //                         <th rowspan="2" class="w-20px text-center">No</th>
        //                         <th rowspan="2" class="w-50px text-center">Kode Part</th>
        //                         <th rowspan="2" class="w-20px text-center">Status Marketplace</th>
        //                         <th rowspan="2" class="w-50px text-center">Pindah</th>
        //                         <th colspan="3" class="w-50px text-center">Stock</th>
        //                         <th colspan="2" class="w-50px text-center">Action</th>
        //                     </tr>
        //                     <tr class="fs-8 fw-bolder text-muted">
        //                         <th class="w-50px text-center">SUMA</th>
        //                         <th class="w-50px text-center">Shopee</th>
        //                         <th class="w-50px text-center">Total</th>
        //                         <th class="w-50px text-center">Shopee</th>
        //                         <th class="w-50px text-center">Internal</th>
        //                     </tr>
        //                 </thead>
        //                 <tbody class="border">
        //                 ';
        //                 $view_tbBoady = '';
        //                 if(!empty($statusApi) && $statusApi == 1) {
        //                     if (count($data_detail) > 0) {
        //                         $no = 1;
        //                         foreach($data_detail as $data){
        //                             $data_update = base64_encode(json_encode([
        //                                 'nomor_dokumen' => $request->get('nomor_dokumen'),
        //                                 'kode_part'     => $data->part_number
        //                             ]));
        //                 $view_tbBoady .='
        //                     <tr>
        //                         <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
        //                             <span class="fs-7 fw-bolder text-dark">'.$no.'</span>
        //                         </td>
        //                         <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
        //                             <span class="fs-7 fw-boldest text-gray-800 d-block">'.strtoupper(trim($data->part_number)).'</span>
        //                             <span class="fs-7 fw-bolder text-gray-700 d-block">'.trim($data->nama_part).'</span>
        //                             <span class="fs-8 fw-bolder text-gray-600 d-block">'.(strtoupper(trim($data->product_id))??'-').'</span>
        //                         </td>
        //                         <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
        //                         '.($data->status_mp_header==1?'<span class="fs-7 fw-bolder badge badge-light-success">Sudah di Perbarui</span>':'-').'
        //                         </td>
        //                         <td class="ps-3 pe-3 text-end" style="vertical-align:top;">
        //                             <span class="fs-7 fw-bolder text-dark">'.$data->pindah.'</span>
        //                         </td>
        //                         <td class="ps-3 pe-3 text-end" style="vertical-align:top;">
        //                             <span class="fs-7 fw-bolder text-dark">'.$data->stock.'</span>
        //                         </td>
        //                         <td class="ps-3 pe-3 text-end" style="vertical-align:top;">
        //                             <span class="fs-7 fw-bolder text-dark">'.($data->stok_shopee??'<i class="bi bi-database-slash fs-1 text-danger"></i>').'</span>
        //                         </td>
        //                         <td class="ps-3 pe-3 text-end" style="vertical-align:top;">
        //                             <span class="fs-7 fw-bolder text-dark">'.($data->stok_update??'<i class="bi bi-database-slash fs-1 text-danger"></i>').'</span>
        //                         </td>
        //                         <td style="text-align:center;vertical-align:top;">
        //                         '.(($data->show==1)?'
        //                             <a href="#" class="btn btn-sm btn-light-dark btn-hover-rise btn_detail" data-focus="0" onclick="updateDetail(\''.$data_update.'\')">
        //                             <img alt="Logo" src="'.asset('assets/images/logo/shopee.png').'" class="h-20px"/>
        //                             </a>
        //                             ':'-').'
        //                         </td>
        //                         <td style="text-align:center;vertical-align:top;">
        //                         '.(($data->show==1)?'
        //                             <a href="#" class="btn btn-sm btn-danger btn_detail" data-focus="0" onclick="updateDetailInternal(\''.$data_update.'\')">
        //                                 <i class="fa fa-database" aria-hidden="true"></i>
        //                             </a>
        //                             ':'-').'
        //                         </td>
        //                     </tr>
        //                 ';
        //                 $no++;
        //             }
        //         } else {
        //             $view_tbBoady = '
        //                 <tr class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
        //                     <td class="fs-7 fw-bolder text-gray-800 text-center" colspan="6"> Data Tidak Ditemukan </td>
        //                 </tr>
        //             ';
        //         }
        //     } else {
        //         $view_tbBoady = '
        //             <tr class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
        //                 <td class="fs-7 fw-bolder text-danger" colspan="9"> '.($messageApi??'Terdapat kegagalan mohon coba lagi !').' </td>
        //             </tr>
        //         ';
        //     }
        //     $view_table .= $view_tbBoady;
        //     $view_table .='
        //             </tbody>
        //         </table>
        //     </div>
        //     ';
        // } else {
        //     if(!empty($statusApi) && $statusApi == 1) {
        //         if (count($data_detail) > 0){
        //             foreach($data_detail as $data){
        //                 $data_update = base64_encode(json_encode([
        //                     'nomor_dokumen' => $request->get('nomor_dokumen'),
        //                     'kode_part'     => $data->part_number
        //                 ]));
        //                 $view_table .=
        //                     '
        //                     <div class="card card-flush mt-6" id="tabel">
        //                         <div class="card-body pt-5">
        //                             <div class="row mt-4">
        //                                 <div class="col-12 col-lg-6">
        //                                     <span class="fw-bold fs-7 text-gray-600 d-block">Kode Part:</span>
        //                                     <span class="fw-bolder text-dark mt-1 d-block">'.$data->part_number.'</span>
        //                                     <span class="fw-bold fs-7 text-gray-600 mt-6 d-block">Nama Part:</span>
        //                                     <span class="fw-bolder text-dark mt-1 d-block">'.$data->nama_part.'</span>
        //                                 </div>
        //                             </div>
        //                             <div class="row mt-6">
        //                                 <div class="col-6">
        //                                     <div class="row">
        //                                         <span class="fw-bold fs-7 text-gray-600">Jumlah Pindah:</span>
        //                                         <span class="fs-7 fw-bolder text-dark">'.$data->pindah.'</span>
        //                                     </div>
        //                                 </div>
        //                                 <div class="col-6">
        //                                     <div class="row">
        //                                         <span class="fw-bold fs-7 text-gray-600">Status Marketplace:</span>
        //                                         <span class="fs-7 fw-bolder text-muted">'.($data->status_marketplace==1?'<span class="badge badge-success"><i class="bi bi-check text-white"></i></span>':'<span class="badge badge-secondary"><i class="bi bi-dash text-white fs-3"></i></span>').'</span>
        //                                     </div>
        //                                 </div>
        //                             </div>
        //                             <div class="separator my-5"></div>
        //                             <div class="text-end">
        //                                 <a href="#" class="btn btn-light-dark btn-hover-rise" data-focus="0" onclick="updateDetail(\''.$data_update.'\')">
        //                                 Update <img alt="Logo" src="'.asset('assets/images/logo/shopee.png').'" class="h-20px me-3"/>
        //                                 </a>
        //                             </div>
        //                         </div>
        //                     </div>';
        //             } 
        //         } else {
        //             $view_table =
        //             '
        //             <div id="daftar_table" class="tab-pane fade active show">
        //                 <div class="card card-flush mt-6" id="tabel">
        //                     <div class="card-body pt-5 text-center">
        //                         <span class="fs-7 fw-bolder text-gray-800"> Data Tidak Ditemukan </span>
        //                     </div>
        //                 </div>
        //             </div>';
        //         }
        //     } else {
        //         $view_table =
        //             '
        //             <div id="daftar_table" class="tab-pane fade active show">
        //                 <div class="card card-flush mt-6" id="tabel">
        //                     <div class="card-body pt-5 text-center">
        //                         <span class="fs-7 fw-bolder text-danger"> Terdapat kegagalan mohon coba lagi ! </span>
        //                     </div>
        //                 </div>
        //             </div>';
        //     }
        // }

        // return response()->json([
        //     'status' => $statusApi,
        //     // 'message' => $messageApi,
        //     'data' => $view_table
        // ]);
    }
    
    public function updateStockperDokumen(Request $request){
        $responseApi = ApiService::onlineuUpdateStockShopeePerDokumen(
            $request->no_dok,
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

        $view_respon = view('layouts.online.shopee.pemindahan.modal_respon_update', [
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

    public function updateStockperPart(Request $request){
        $responseApi = ApiService::onlineUpdateStockShopeePerPart(
            $request->nomor_dokumen,
            $request->kode_part,
            strtoupper(trim($request->session()->get('app_user_company_id')))
        );

        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if (empty($statusApi) || $statusApi == 0) {
            return response()->json([
                'status' => 0,
                'message' => $messageApi
            ]);
        }
        
        $data_all = json_decode($responseApi)->data;
        
        $view_respon = view('layouts.online.shopee.pemindahan.modal_respon_update', [
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
        $responseApi = ApiService::OnlinePemindahanShopeeUpdateStatusPerPartNumber(strtoupper(trim($request->get('nomor_dokumen'))),
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
