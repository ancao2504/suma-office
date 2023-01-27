<?php

namespace App\Http\Controllers\app\Online\Shopee;

use App\Helpers\ApiService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PemindahanShopeeController extends Controller
{
    public function daftarPemindahanStok(Request $request){
        $responseApi = ApiService::dafrarPemindahanStok(
            $request->get('search'),
            $request->get('tanggal'),
            $request->get('page'), 
            $request->get('per_page')
        );
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        $view_table = '';
        if($statusApi == 1) {
                $data = json_decode($responseApi)->data;
                $view_table ='
                <div class="table-responsive">
                    <table id="kt_project_users_table" class="table table-row-dashed table-row-gray-300 align-middle">
                        <thead class="border">
                            <tr class="fw-bolder text-muted text-center">
                                <th style="width: 0px;">No</th>
                                <th style="width: 0px;">No Dokumen</th>
                                <th style="width: 0px;">Tanggal</th>
                                <th style="width: 0px;">Dari Lokasi</th>
                                <th style="width: 0px;">Ke Lokasi</th>
                                <th style="width: 0px;">Keterangan</th>
                                <th style="width: 0px;">User</th>
                                <th style="width: 0px;">Cetak</th>
                                <th style="width: 0px;">Sts In</th>
                                <th class="min-w-60px" style="width: 0px;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fs-6 border">
                ';
                $view_tbBoady = '';
            if ($data->total > 0){
                $no = $data->from;
                foreach($data->data as $data){
                    $data_dtl = base64_encode(
                        json_encode(
                            array(
                                $data->no_dokumen => $data->detail
                            )
                        )
                    );
                    $view_tbBoady .='
                                <tr class="fs-6 fw-bold text-gray-700 klikdokumen"
                                    data-dtl='.$data_dtl.'>
                                    <td class="text-center">'.$no.'</td>
                                    <td>'.$data->no_dokumen.'</td>
                                    <td>'.date('d/m/Y', strtotime($data->tanggal)).'</td>
                                    <td>'.$data->dari_lokasi.'</td>
                                    <td>'.$data->ke_lokasi.'</td>
                                    <td>'.$data->keterangan.'</td>
                                    <td>'.preg_replace('/[^a-zA-Z]+/', '', $data->usertime).'</td>
                                    <td class="text-center">
                                        <div class="form-check form-check-custom form-check-solid form-check-md d-flex align-items-center">
                                            <input class="form-check-input" type="checkbox" value="1" id="filter_select_all" checked>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-check-custom form-check-solid form-check-md d-flex align-items-center">
                                            <input class="form-check-input" type="checkbox" value="1" id="filter_select_all" checked>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-icon btn-primary mt-1 btn-edit" data-array="">
                                            <span class="bi bi-pencil"></span>
                                        </button>
                                    </td>
                                </tr>
                    ';
                    $no++;
                }
            } else {
                $view_tbBoady = '
                    <tr class="odd">
                        <td class="fw-bold" colspan="6">Tidak ada data</td>
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


        return view('layouts.online.shopee.shopee', [
            'title_menu'    => 'Update Stok Shopee',
            'table'         => $view_table,
        ]);
    }
}
