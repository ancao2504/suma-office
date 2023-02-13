<?php

namespace App\Http\Controllers\app\Online\Shopee;

use App\Helpers\ApiService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Jenssegers\Agent\Agent;

class PemindahanShopeeController extends Controller
{
    public function index(){
        return view('layouts.online.shopee.shopee', [
            'title_menu'    => 'Update Stok Shopee'
        ]);
    }

    public function daftarPemindahan(Request $request){
        $responseApi = ApiService::OnlinePemindahanShopeeDaftar(
            $request->get('search'),
            $request->get('start_date'),
            $request->get('end_date'),
            'PD',
            $request->get('page'),
            in_array($request->get('per_page'), [10,25,50,100]) ? $request->get('per_page') : 10
        );

        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;
        $data_all = json_decode($responseApi)->data;

        $Agent = new Agent();
        $view_content = '';
        $page_view='';
        if($Agent->isDesktop()) {
            $view_content ='
                <div class="table-responsive" id="tabel">
                        <table class="table table-row-dashed table-row-gray-300 align-middle">
                            <thead class="border">
                            <tr class="fs-8 fw-bolder text-muted">
                            <th class="w-50px ps-3 pe-3 text-center">No</th>
                            <th class="w-100px ps-3 pe-3 text-center">No Dokumen</th>
                            <th class="w-50px ps-3 pe-3 text-center">Tanggal</th>
                            <th class="w-50px ps-3 pe-3 text-center">Lokasi Awal</th>
                            <th class="w-50px ps-3 pe-3 text-center">Lokasi Tujuan</th>
                            <th class="w-150px ps-3 pe-3 text-center">Keterangan</th>
                            <th class="w-50px ps-3 pe-3 text-center">Sts Cetak</th>
                            <th class="w-50px ps-3 pe-3 text-center">Sts In</th>
                            <th class="w-50px ps-3 pe-3 text-center">Sts SJ</th>
                            <th class="w-50px ps-3 pe-3 text-center">Sts Validasi</th>
                            <th class="w-50px ps-3 pe-3 text-center">Sts Marketplace</th>
                            <th class="w-150px ps-3 pe-3 text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody class="border">
                ';
                $view_tbBoady = '';
            if($statusApi == 1) {
                if ($data_all->total > 0){
                    $no = $data_all->from;
                    foreach($data_all->data as $data){
                        $page_detail_data = json_encode([
                            'nomor_dokumen' => $data->nomor_dokumen,
                            'filter' => [
                                            'search'        => $request->get('search'),
                                            'start_date'    => $request->get('start_date'),
                                            'end_date'      => $request->get('end_date'),
                                            'page'          => $request->get('page'),
                                            'per_page'      => $request->get('per_page'),
                                            'marketplace'   => $data->status_marketplace
                                        ]
                        ]);
                        $view_tbBoady .='
                            <tr class="fs-6 fw-bold text-gray-700" data-no="'.$data->nomor_dokumen.'">
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-gray-800">'.$no.'</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-gray-800">'.$data->nomor_dokumen.'</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-muted">'.date('d/m/Y', strtotime($data->tanggal)).'</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-muted">'.$data->lokasi_awal.'</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-muted">'.$data->lokasi_tujuan.'</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:'.(!empty($data->keterangan)?'left':'center').';vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-muted">'.(!empty($data->keterangan)?$data->keterangan:'-').'</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                    '.($data->status_cetak==1?'<i class="bi bi-check text-success fs-1"></i>':'-').'
                                </td>
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                    '.($data->status_in==1?'<i class="bi bi-check text-success fs-1"></i>':'-').'
                                </td>
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                    '.($data->status_sj==1?'<i class="bi bi-check text-success fs-1"></i>':'-').'
                                </td>
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                    '.($data->validasi==1?'<i class="bi bi-check text-success fs-1"></i>':'-').'
                                </td>
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                    '.($data->status_marketplace==1?'<i class="bi bi-check text-success fs-1"></i>':'-').'
                                </td>
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                    '.($data->status_marketplace==1?'':'<button class="btn btn-sm btn-light-dark btn-hover-rise d-inline mb-sm-3 mb-lg-0 btn_detail" data-focus="0"><img alt="Logo" src="http://localhost:2022/suma-pmo/public/assets/images/logo/shopee.png" class="h-20px"/></button>').'
                                    <a href="'.route('online.pemindahan.shopee.detail-index', base64_encode($page_detail_data)).'" class="btn btn-sm btn-primary d-inline ms-1" data-focus="0">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        ';
                        $no++;
                    }

                } else {
                    $view_tbBoady = '
                        <tr class="odd">
                            <td class="fw-bold text-center" colspan="10"> Data Tidak Ditemukan </td>
                        </tr>
                    ';
                }
            } else {
                $view_tbBoady = '
                    <tr class="odd">
                    <td class="fw-bold text-center" colspan="10">'.$messageApi.'</td>
                    </tr>
                ';
            }
            $view_content .= $view_tbBoady;
            $view_content .='
                        </tbody>
                    </table>
                </div>
                ';
                
        } else {
            if($statusApi == 1) {
                if ($data_all->total > 0){
                    foreach($data_all->data as $data){
                        $page_detail_data = json_encode([
                            'nomor_dokumen' => $data->nomor_dokumen,
                            'filter' => [
                                            'search'        => $request->get('search'),
                                            'start_date'    => $request->get('start_date'),
                                            'end_date'      => $request->get('end_date'),
                                            'page'          => $request->get('page'),
                                            'per_page'      => $request->get('per_page'),
                                            'marketplace'   => $data->status_marketplace
                                        ]
                        ]);
                        $view_content .=
                            '
                            <div class="card card-flush mt-6" id="tabel">
                                <div class="card-body pt-5">
                                    <div class="row mt-4">
                                        <div class="col-6 col-lg-6">
                                            <span class="fw-bold fs-7 text-gray-600 d-block">Nomor Dokumen:</span>
                                            <span class="fw-bolder text-dark mt-1 d-block">'.$data->nomor_dokumen.'</span>
                                            <span class="fw-bold fs-7 text-gray-600 mt-6 d-block">Tanggal:</span>
                                            <span class="fw-bolder text-dark mt-1 d-block">'.date('d/m/Y', strtotime($data->tanggal)).'</span>
                                        </div>
                                    </div>
                                    <div class="row mt-6">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-4">
                                                    <span class="fw-bold fs-7 text-gray-600 d-block">Lokasi Awal:</span>
                                                    <span class="fw-bolder text-dark">'.$data->lokasi_awal.'</span>
                                                </div>
                                                <div class="col-1">
                                                    <i class="bi bi-arrow-right fs-1"></i>
                                                </div>
                                                <div class="col-4">
                                                    <span class="fw-bold fs-7 text-gray-600 d-block">Lokasi Tujuan:</span>
                                                    <span class="fw-boldest text-success text-uppercase">'.$data->lokasi_tujuan.'</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-6">
                                        <div class="col-4">
                                            <span class="fw-bold fs-7 text-gray-600 d-block">Sts Cetak:</span>
                                            '.($data->status_cetak==1?'<span class="badge badge-success"><i class="bi bi-check text-white fs-3"></i></span>':'-').'
                                        </div>
                                        <div class="col-4">
                                            <span class="fw-bold fs-7 text-gray-600 d-block">Sts in:</span>
                                            '.($data->status_in==1?'<span class="badge badge-success"><i class="bi bi-check text-white fs-3"></i></span>':'-').'
                                        </div>
                                        <div class="col-4">
                                            <span class="fw-bold fs-7 text-gray-600 d-block">Sts MKPlace:</span>
                                            '.($data->status_marketplace==1?'<span class="badge badge-success"><i class="bi bi-check text-white fs-3"></i></span>':'-').'
                                        </div>
                                    </div>
                                    <div class="separator my-5"></div>
                                    <div class="text-end">
                                        <a href="'.route('online.pemindahan.shopee.detail-index', base64_encode($page_detail_data)).'" class="btn btn-light-dark btn-hover-rise btn_detail" data-focus="0">
                                        Update <img alt="Logo" src="http://localhost:2022/suma-pmo/public/assets/images/logo/shopee.png" class="h-20px me-3"/>
                                        </a>
                                    </div>
                                </div>
                            </div>';
                    } 
                } else {
                    $view_content =
                    '
                    <div class="card card-flush mt-6" id="tabel">
                        <div class="card-body pt-5 text-center">
                            <span class="fs-7 fw-bolder text-gray-800"> Data Tidak Ditemukan </span>
                        </div>
                    </div>';
                }
            } else {
                $view_content =
                    '
                    <div class="card card-flush mt-6" id="tabel">
                        <div class="card-body pt-5 text-center">
                            <span class="fs-7 fw-bolder text-danger">'.$messageApi.'</span>
                        </div>
                    </div>';
            }
        }

        if($statusApi == 1) {
            $page_view .='<ul class="pagination" data-current_page="'.$data_all->current_page.'">';
            foreach ($data_all->links as $data){
                if (strpos($data->label, 'Next') !== false){
                $page_view .='<li class="page-item next'.($data->url == null?'disabled':'').'">
                        <a role="button" data-page="'. (string)((int)($data_all->current_page) + 1) .'" class="page-link">
                            <i class="next"></i>
                        </a>
                    </li>';
                } elseif (strpos($data->label, 'Previous') !== false) {
                $page_view .='<li class="page-item previous '.($data->url == null?'disabled':'').'">
                        <a role="button" data-page="'. (string)((int)($data_all->current_page) - 1) .'" class="page-link">
                            <i class="previous"></i>
                        </a>
                    </li>';
                } elseif ($data->active == true) {
                $page_view .='<li class="page-item active '.($data->url == null?'disabled':'').'">
                        <a role="button" data-page="'. $data->label .'" class="page-link">'. $data->label .'</a>
                    </li>';
                }elseif ($data->active == false){
                    $page_view .='<li class="page-item '.($data->url == null?'disabled':'').'">
                        <a role="button" data-page="'. $data->label .'" class="page-link">'.$data->label.'</a>
                    </li>';
                }
            }
            $page_view .='</ul>';
        }

        return response()->json([
            'status'    => $statusApi,
            'message'   => $messageApi,
            'table'     => $view_content,
            'pagination'=> $page_view
        ] , 200);
    }


    public function detailPemindahan($id){
        return view('layouts.online.shopee.shopeeDetail', [
            'title_menu'    => 'Update Stok Shopee',
            'filter_header'         => json_decode(base64_decode($id))
        ]);
    }

    
    public function detailPemindahanDaftar(Request $request){
        response()->json([
            'data' => $request->all(),
        ]);
        $responseApi = ApiService::OnlinePemindahanDetail(
            $request->get('nomor_dokumen'),
            'PD'
        );
        
        $statusApi = json_decode($responseApi)->status; $messageApi = json_decode($responseApi)->message;
        $data_header = json_decode($responseApi)->data??'';
        $data_detail = $data_header->detail??'';

        $Agent = new Agent();
        $view_table = '';
        $view_footer = '';
        
        if(!empty($statusApi) && $statusApi == 1) {
            // jika mobule maka card jika tidak maka kosong
        $view_table .='
            <div class="row mb-3 table_delete '.($Agent->isMobile()? 'bg-white py-3': '').'">
                <div class="col-12">
                    <div class="fw-bolder text-gray-400">Nomor Dokumen :</div>
                    <div class="fs-3 fw-bolder text-gray-800">'.$data_header->nomor_dokumen.'</div>
                    <div class="fw-bolder text-gray-500">'.date("d F Y", strtotime($data_header->tanggal)).'</div>
                </div>
                <div class="col-6 mt-3">
                    <div class="fw-bolder text-gray-400">Dari :</div>
                    <div class="fs-4 fw-bolder text-gray-800">'.$data_header->lokasi_awal->kode_lokasi.' - '.$data_header->lokasi_awal->nama_lokasi.'</div>
                    <div class="fs-5 fw-bolder text-gray-800">'.$data_header->lokasi_awal->alamat1 .' '. $data_header->lokasi_awal->alamat2.'</div>
                    <div class="fs-5 fw-bolder text-gray-800">'.$data_header->lokasi_awal->kota.'</div>
                </div>
                <div class="col-6 mt-3">
                    <div class="fw-bolder text-gray-400">Ke :</div>
                    <div class="fs-4 fw-bolder text-gray-800">'.$data_header->lokasi_tujuan->kode_lokasi.' - '.$data_header->lokasi_tujuan->nama_lokasi.'</div>
                    <div class="fs-5 fw-bolder text-gray-800">'.$data_header->lokasi_tujuan->alamat1 .' '. $data_header->lokasi_tujuan->alamat2.'</div>
                    <div class="fs-5 fw-bolder text-gray-800">'.$data_header->lokasi_tujuan->kota.'</div>
                </div>
                <div class="col-12 mt-3">
                    <div class="fw-bolder text-gray-400">Keterangan :</div>
                    <div class="fs-5 fw-bolder text-gray-800">'.(!empty($data_header->keterangan)?$data_header->keterangan:'-' ).'</div>
                </div>
                <div class="col-12 mt-3">
                    <div class="fw-bolder text-gray-400">Status :</div>
                    <span class="badge badge-'.($data_header->status_cetak==1?'success':'danger').' p-2">
                        <div class="fs-5 fw-bolder text-white">Cetak</div>
                    </span>
                    <span class="badge badge-'.($data_header->status_in==1?'success':'danger').' p-2">
                        <div class="fs-5 fw-bolder text-white">In</div>
                    </span>
                    <span class="badge badge-'.($data_header->status_sj==1?'success':'danger').' p-2">
                        <div class="fs-5 fw-bolder text-white">SJ</div>
                    </span>
                    <span class="badge badge-'.((!empty($data_header->validasi) && $data_header->validasi == 1)?'success':'danger').' p-2">
                        <div class="fs-5 fw-bolder text-white">Validasi</div>
                    </span>
                    <span class="badge badge-'.($data_header->status_marketplace==1?'success':'danger').' p-2">
                        <div class="fs-5 fw-bolder text-white">Marketplace</div>
                    </span>
                </div>
            </div>';
        }

        if($Agent->isDesktop()){
            $view_table .='
                <div class="table-responsive table_delete">
                    <table class="table table-row-dashed table-row-gray-300 align-middle">
                        <thead class="border">
                            <tr class="fs-8 fw-bolder text-muted">
                                <th rowspan="2" class="w-50px ps-3 pe-3 text-center">No</th>
                                <th rowspan="2" class="w-50px ps-3 pe-3 text-center">Kode Part</th>
                                <th rowspan="2" class="w-200px ps-3 pe-3 text-center">Nama Part</th>
                                <th rowspan="2" class="w-50px ps-3 pe-3 text-center">Pindah</th>
                                <th colspan="3" class="w-50px ps-3 pe-3 text-center">Stock</th>
                                <th rowspan="2" class="w-50px ps-3 pe-3 text-center">Status Marketplace</th>
                                <th rowspan="2" class="w-50px ps-3 pe-3 text-center">Action</th>
                            </tr>
                            <tr class="fs-8 fw-bolder text-muted">
                                <th class="w-50px ps-3 pe-3 text-center">SUMA</th>
                                <th class="w-50px ps-3 pe-3 text-center">Shopee</th>
                                <th class="w-50px ps-3 pe-3 text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody class="border">
                        ';
                        $view_tbBoady = '';
                        if(!empty($statusApi) && $statusApi == 1) {
                            if (count($data_detail) > 0) {
                                $no = 1;
                                foreach($data_detail as $data){
                                    $data_update = base64_encode(json_encode([
                                        'nomor_dokumen' => $request->get('nomor_dokumen'),
                                        'kode_part'     => $data->kode_part
                                    ]));
                        $view_tbBoady .='
                            <tr>
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-dark">'.$no.'</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-dark">'.$data->kode_part.'</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-dark">'.$data->nama_part.'</span>
                                </td>
                                <td class="ps-3 pe-3 text-end" style="vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-dark">'.$data->jumlah_pindah.'</span>
                                </td>
                                <td class="ps-3 pe-3 text-end" style="vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-dark">'.$data->stock.'</span>
                                </td>
                                <td class="ps-3 pe-3 text-end" style="vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-dark">'.($data->stok_shopee??'<i class="bi bi-database-slash fs-1 text-danger"></i>').'</span>
                                </td>
                                <td class="ps-3 pe-3 text-end" style="vertical-align:top;">
                                    <span class="fs-7 fw-bolder text-dark">'.($data->stok_update??'<i class="bi bi-database-slash fs-1 text-danger"></i>').'</span>
                                </td>
                                <td class="ps-3 pe-3 text-center" style="vertical-align:top;">
                                    '.($data->status_marketplace==1?'<span class="fs-7 fw-bolder badge badge-light-success">Sudah di Perbarui</span>':'-').'
                                </td>
                                '.($data->show==0?'
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                    <a href="#" class="btn btn-sm btn-light-dark btn-hover-rise btn_detail" data-focus="0" onclick="updateDetail(\''.$data_update.'\')">
                                    Update <img alt="Logo" src="http://localhost:2022/suma-pmo/public/assets/images/logo/shopee.png" class="h-20px me-3"/>
                                    </a>
                                </td>
                                ':'').'
                            </tr>
                        ';
                        $no++;
                    }
                } else {
                    $view_tbBoady = '
                        <tr class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                            <td class="fs-7 fw-bolder text-gray-800 text-center" colspan="6"> Data Tidak Ditemukan </td>
                        </tr>
                    ';
                }
            } else {
                $view_tbBoady = '
                    <tr class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                        <td class="fs-7 fw-bolder text-danger" colspan="9"> Terdapat kegagalan mohon coba lagi ! </td>
                    </tr>
                ';
            }
            $view_table .= $view_tbBoady;
            $view_table .='
                    </tbody>
                </table>
            </div>
            ';
        } else {
            if(!empty($statusApi) && $statusApi == 1) {
                if (count($data_detail) > 0){
                    foreach($data_detail as $data){
                        $data_update = base64_encode(json_encode([
                            'nomor_dokumen' => $request->get('nomor_dokumen'),
                            'kode_part'     => $data->kode_part
                        ]));
                        $view_table .=
                            '
                            <div class="card card-flush mt-6" id="tabel">
                                <div class="card-body pt-5">
                                    <div class="row mt-4">
                                        <div class="col-12 col-lg-6">
                                            <span class="fw-bold fs-7 text-gray-600 d-block">Kode Part:</span>
                                            <span class="fw-bolder text-dark mt-1 d-block">'.$data->kode_part.'</span>
                                            <span class="fw-bold fs-7 text-gray-600 mt-6 d-block">Nama Part:</span>
                                            <span class="fw-bolder text-dark mt-1 d-block">'.$data->nama_part.'</span>
                                        </div>
                                    </div>
                                    <div class="row mt-6">
                                        <div class="col-6">
                                            <div class="row">
                                                <span class="fw-bold fs-7 text-gray-600">Jumlah Pindah:</span>
                                                <span class="fs-7 fw-bolder text-dark">'.$data->jumlah_pindah.'</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="row">
                                                <span class="fw-bold fs-7 text-gray-600">Status Marketplace:</span>
                                                <span class="fs-7 fw-bolder text-muted">'.($data->status_marketplace==1?'<span class="badge badge-success"><i class="bi bi-check text-white"></i></span>':'<span class="badge badge-secondary"><i class="bi bi-dash text-white fs-3"></i></span>').'</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="separator my-5"></div>
                                    <div class="text-end">
                                        <a href="#" class="btn btn-light-dark btn-hover-rise" data-focus="0" onclick="updateDetail(\''.$data_update.'\')">
                                        Update <img alt="Logo" src="http://localhost:2022/suma-pmo/public/assets/images/logo/shopee.png" class="h-20px me-3"/>
                                        </a>
                                    </div>
                                </div>
                            </div>';
                    } 
                } else {
                    $view_table =
                    '
                    <div id="daftar_table" class="tab-pane fade active show">
                        <div class="card card-flush mt-6" id="tabel">
                            <div class="card-body pt-5 text-center">
                                <span class="fs-7 fw-bolder text-gray-800"> Data Tidak Ditemukan </span>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                $view_table =
                    '
                    <div id="daftar_table" class="tab-pane fade active show">
                        <div class="card card-flush mt-6" id="tabel">
                            <div class="card-body pt-5 text-center">
                                <span class="fs-7 fw-bolder text-danger"> Terdapat kegagalan mohon coba lagi ! </span>
                            </div>
                        </div>
                    </div>';
            }
        }

        return response()->json([
            'status' => $statusApi,
            // 'message' => $messageApi,
            'data' => $view_table
        ]);
    }
    

    public function updateStockperDokumen(Request $request){
        $responseApi = ApiService::updateStockperDokumen(
            $request->no_dok,
            'PD'
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

        $modal_respown = '
            <div class="modal fade" id="modal_respown" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modal_respownLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_respownLabel">Informasi Update</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="document.getElementById(\'modal_respown\').remove()" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <div class="fs-5 fw-bolder text-gray-800">'.$data_all->nomer_dokumen.'</div>
                        <div class="table-responsive table_delete">
                            <table class="table table-row-dashed table-row-gray-300 align-middle">
                                <thead class="border">
                                    <tr class="fs-8 fw-bolder text-muted">
                                        <th class="w-20px ps-3 pe-3 text-center">No</th>
                                        <th class="w-30px ps-3 pe-3 text-center">Part</th>
                                        <th class="w-200px ps-3 pe-3 text-center">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="border">';
        $no = 1;
        if(!empty($data_all->data_error) && count($data_all->data_error) > 0){
            foreach($data_all->data_error as $data){
                $modal_respown .= '<tr>
                                        <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                            <span class="fs-7 fw-bolder text-dark">'.$no.'</span>
                                        </td>
                                        <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                            <span class="fs-7 fw-bolder text-dark">'.$data->kode_part.'</span>
                                        </td>
                                        <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                            <span class="fs-7 fw-bolder text-danger">'.$data->keterangan.'</span>
                                        </td>
                                    </tr>';
                $no++;
            }
        } 
        if(!empty($data_all->data_sukses) && count($data_all->data_sukses) > 0){
            foreach($data_all->data_sukses as $data){
                $modal_respown .= '<tr>
                                        <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                            <span class="fs-7 fw-bolder text-dark">'.$no.'</span>
                                        </td>
                                        <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                            <span class="fs-7 fw-bolder text-dark">'.$data->kode_part.'</span>
                                        </td>
                                        <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                            <span class="fs-7 fw-bolder text-success">'.$data->keterangan.'</span>
                                        </td>
                                    </tr>';
                $no++;
            }
        }
            $modal_respown .= '</tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="document.getElementById(\'modal_respown\').remove()">Close</button>
                    </div>
                    </div>
                </div>
            </div>
        ';

        return response()->json([
            'status' => $statusApi,
            'message' => $messageApi,
            'data' => $data_all,
            'modal_respown' => $modal_respown
        ]);
    }

    public function updateStockperPart(Request $request){
        $responseApi = ApiService::updateStockperPart(
            $request->nomor_dokumen,
            $request->kode_part,
            'PD'
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
    }
}
