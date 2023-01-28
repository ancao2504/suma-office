<?php

namespace App\Http\Controllers\app\Online\Tokopedia;

use App\Helpers\ApiService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PemindahanTokopediaController extends Controller
{
    public function daftarPemindahan(Request $request){
        // dd($request->all());
        $responseApi = ApiService::OnlinePemindahanShopeeDaftar(
            $request->get('search'),
            $request->get('start_date'),
            $request->get('end_date'),
            'PD',
            $request->get('page'),
            $request->get('per_page'),
        );
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        $view_table = '';
        $page_view='';
        if($statusApi == 1) {
                $data_all = json_decode($responseApi)->data;
                $view_table ='
                <div class="table-responsive">
                    <table id="kt_project_users_table" class="table table-row-dashed table-row-gray-300 align-middle">
                        <thead class="border">
                            <tr class="fw-bolder text-muted text-center">
                            <th style="width: 0px;">No</th>
                            <th style="width: 0px;">No Dokumen</th>
                            <th style="width: 0px;">Tanggal</th>
                            <th style="width: 0px;">Lokasi Awal</th>
                            <th style="width: 0px;">Lokasi Tujuan</th>
                            <th style="width: 0px;">Keterangan</th>
                            <th style="width: 0px;">Sts Cetak</th>
                            <th style="width: 0px;">Sts In</th>
                            <th style="width: 0px;">Sts Marketplace</th>
                            <th class="min-w-60px" style="width: 0px;">Action</th>
                        </tr>
                    </thead>
                        <tbody class="fs-6 border">
                ';
                $view_tbBoady = '';
            if ($data_all->total > 0){
                $no = $data_all->from;
                foreach($data_all->data as $data){
                    $view_tbBoady .='
                                <tr class="fs-6 fw-bold text-gray-700 klikdokumen">
                                    <td class="text-center">'.$no.'</td>
                                    <td>'.$data->nomor_dokumen.'</td>
                                    <td>'.date('d/m/Y', strtotime($data->tanggal)).'</td>
                                    <td>'.$data->lokasi_awal.'</td>
                                    <td>'.$data->lokasi_tujuan.'</td>
                                    <td>'.$data->keterangan.'</td>
                                    <td class="text-center">
                                        '.($data->status_cetak==1?'<span class="badge badge-success"><i class="bi bi-check text-white fs-3"></i></span>':'<span class="badge badge-secondary"><i class="bi bi-dash text-white fs-3"></i></span>').'
                                    </td>
                                    <td class="text-center">
                                        '.($data->status_in==1?'<span class="badge badge-success"><i class="bi bi-check text-white fs-3"></i></span>':'<span class="badge badge-secondary"><i class="bi bi-dash text-white fs-3"></i></span>').'
                                    </td>
                                    <td class="text-center">
                                        '.($data->status_marketplace==1?'<span class="badge badge-success"><i class="bi bi-check text-white fs-3"></i></span>':'<span class="badge badge-secondary"><i class="bi bi-dash text-white fs-3"></i></span>').'
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-icon btn-primary mt-1 d-lg-inline btn-edit" data-key="'.$data->nomor_dokumen.'">
                                            <span class="bi bi-pencil"></span>
                                        </button>
                                        <button class="btn btn-sm btn-primary d-lg-inline btn-update">Update</button>
                                    </td>
                                </tr>
                    ';
                    $no++;
                }
            } else {
                $view_tbBoady = '
                    <tr class="odd">
                        <td class="fw-bold text-center" colspan="10">Tidak ada data</td>
                    </tr>
                ';
            }
            $view_table .= $view_tbBoady;
            $view_table .='
                    </tbody>
                </table>
            </div>
            ';

            $page_view .='<ul class="pagination">';
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


        return view('layouts.online.shopee.shopee', [
            'title_menu'    => 'Update Stok Shopee',
            'table'         => $view_table,
            'pagination'    => $page_view
        ]);
    }

    
    public function detailPemindahan(Request $request){
        $responseApi = ApiService::OnlinePemindahanShopeeDetail(
            $request->get('nomer_dokumen'),
            'PD'
        );
        
        $statusApi = json_decode($responseApi)->status;

        
        $view_table = '';
        $page_view='';
        if($statusApi == 1) {
            $data_all = json_decode($responseApi)->data;
                $view_table ='
                <div class="table-responsive border rounded px-3 my-3">
                    <table class="table table-sm table-striped">
                        <thead class="border-bottom-2">
                            <tr>
                                <th>No</th>
                                <th>Part Number</th>
                                <th>Nama Part</th>
                                <th>Jumlah Pindah</th>
                                <th>Sts Marketplace</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="border">
                ';

                $view_tbBoady = '';
            if ($data_all->total > 0){
                $no = $data_all->from;
                foreach($data_all->data as $data){
                    // $data->jumlah_pindah
                    if ($data->jumlah_pindah > 0){
                        $view_tbBoady .='
                            <tr>
                                <td class="text-center">'.$no.'</td>
                                <td>'.$data->kode_part.'</td>
                                <td>'.$data->nama_part.'</td>
                                <td class="text-end">'.$data->jumlah_pindah.'</td>
                                <td class="text-center">
                                    '.($data->status_marketplace==1?'<span class="badge badge-success"><i class="bi bi-check text-white"></i></span>':'<span class="badge badge-secondary"><i class="bi bi-dash text-white fs-3"></i></span>').'
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary btn-update">Update</button>
                                </td>
                            </tr>
                        ';
                        $no++;
                    }
                }
            } else {
                $view_tbBoady = '
                    <tr class="odd">
                        <td class="fw-bold text-center" colspan="6">Tidak ada data</td>
                    </tr>
                ';
            }
            $view_table .= $view_tbBoady;
            $view_table .='
                    </tbody>
                </table>
            </div>
            ';
        }


        return response()->json([
            'status' => $statusApi,
            'data' => $view_table
        ]);
    }
}
