<?php

namespace App\Http\Controllers\app\Online\Tokopedia;

use App\Helpers\ApiService;
use App\Helpers\ApiServiceTokopedia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent as Agent;

class PemindahanTokopediaController extends Controller
{
    public function daftarPemindahan(Request $request) {
        $start_date = Carbon::now()->startOfMonth()->format('Y-m-d');
        $end_date = Carbon::now()->format('Y-m-d');

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

        $responseApi = ApiService::SettingClossingMarketing(strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;

            $start_date = (empty($request->get('start_date'))) ? $dataApi->tanggal_aktif : $request->get('start_date');
            $end_date = (empty($request->get('end_date'))) ? Carbon::now()->format('Y-m-d') : $request->get('end_date');

            $responseApi = ApiService::OnlinePemindahanTokopediaDaftar($request->get('page'), $per_page,
                        $start_date, $end_date, $request->get('search'), strtoupper(trim($request->session()->get('app_user_company_id'))));
            $statusApi = json_decode($responseApi)->status;
            $messageApi =  json_decode($responseApi)->message;

            if($statusApi == 1) {
                $dataApi = json_decode($responseApi)->data;
                $dataPemindahan = json_decode($responseApi)->data->data;

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

                return view('layouts.online.tokopedia.pemindahan.pemindahan', [
                    'title_menu'        => 'Pemindahan Antar Lokasi',
                    'data_page'         => $data_page->first(),
                    'data_filter'       => $data_filter->first(),
                    'data_device'       => $data_device->first(),
                    'data_user'         => $data_user->first(),
                    'data_pemindahan'   => $dataPemindahan,
                ]);
            } else {
                return redirect()->back()->withInput()->with('failed', $messageApi);
            }
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function formPemindahan($nomor_dokumen, Request $request) {
        $responseApi = ApiService::OnlinePemindahanTokopediaForm($nomor_dokumen,
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi = json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;

            return view('layouts.online.tokopedia.pemindahan.pemindahanform', [
                'title_menu'    => 'Pemindahan Antar Lokasi',
                'data'          => $dataApi
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function formPemindahanDetail(Request $request) {
        $responseApi = ApiService::OnlinePemindahanTokopediaFormDetail($request->get('nomor_dokumen'),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi = json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;

            $nomor_urut = 0;
            $table_detail = '';

            foreach($dataApi as $data) {
                $nomor_urut = (double)$nomor_urut + 1;

                $table_detail .= '<tr>
                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                        <span class="fs-7 fw-bold text-gray-800">'.$nomor_urut.'</span>
                    </td>
                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                        <span class="fs-7 fw-boldest text-gray-800 d-block">'.strtoupper(trim($data->part_number)).'</span>
                        <span class="fs-8 fw-bolder text-gray-600 d-block">'.strtoupper(trim($data->product_id)).'</span>
                        <span class="fs-8 fw-boldest text-dark mt-10 d-block">MARKETPLACE:</span>
                        <span class="fs-8 fw-bold text-gray-600">
                            SKU :<span class="fs-7 fw-bolder text-danger ms-2">'.strtoupper(trim((empty($data->marketplace->sku)) ? '' : $data->marketplace->sku)).'</span>
                            <br>
                            ProductID :<span class="fs-7 fw-bolder text-danger ms-2">'.strtoupper(trim((empty($data->marketplace->productID)) ? '' : $data->marketplace->productID)).'</span>
                            <br>
                            Status :';

                if(strtoupper(trim((empty($data->marketplace->status)) ? '' : $data->marketplace->status)) == 'BANNED') {
                    $table_detail .= '<span class="fs-8 fw-boldest badge badge-danger ms-2">'.trim((empty($data->marketplace->status)) ? '' : $data->marketplace->status).'</span>';
                } elseif(strtoupper(trim((empty($data->marketplace->status)) ? '' : $data->marketplace->status)) == 'PENDING') {
                    $table_detail .= '<span class="fs-8 fw-boldest badge badge-danger ms-2">'.trim((empty($data->marketplace->status)) ? '' : $data->marketplace->status).'</span>';
                } elseif(strtoupper(trim((empty($data->marketplace->status)) ? '' : $data->marketplace->status)) == 'DELETED') {
                    $table_detail .= '<span class="fs-8 fw-boldest badge badge-danger ms-2">'.trim((empty($data->marketplace->status)) ? '' : $data->marketplace->status).'</span>';
                } elseif(strtoupper(trim((empty($data->marketplace->status)) ? '' : $data->marketplace->status)) == 'ARCHIVE') {
                    $table_detail .= '<span class="fs-8 fw-boldest badge badge-success ms-2">'.trim((empty($data->marketplace->status)) ? '' : $data->marketplace->status).'</span>';
                } elseif(strtoupper(trim((empty($data->marketplace->status)) ? '' : $data->marketplace->status)) == 'BEST (FEATURE PRODUCT)') {
                    $table_detail .= '<span class="fs-8 fw-boldest badge badge-success ms-2">'.trim((empty($data->marketplace->status)) ? '' : $data->marketplace->status).'</span>';
                } elseif(strtoupper(trim((empty($data->marketplace->status)) ? '' : $data->marketplace->status)) == 'INACTIVE (WAREHOUSE)') {
                    $table_detail .= '<span class="fs-8 fw-boldest badge badge-warning ms-2">'.trim((empty($data->marketplace->status)) ? '' : $data->marketplace->status).'</span>';
                } else {
                    $table_detail .= '<span class="fs-8 fw-boldest badge badge-danger ms-2">'.trim((empty($data->marketplace->status)) ? '' : $data->marketplace->status).'</span>';
                }

                $table_detail .= ' </span>
                        <div class="mt-4">';

                if(trim((empty($data->marketplace->sku)) ? '' : $data->marketplace->sku) != '') {
                    if(strtoupper(trim($data->part_number)) != strtoupper(trim((empty($data->marketplace->sku)) ? '' : $data->marketplace->sku))) {
                        $table_detail .= ' <span class="badge badge-danger fs-8 fw-boldest animation-blink">PART NUMBER DAN SKU TIDAK SAMA</span>';
                    }
                } else {
                    $table_detail .= '<span class="badge badge-danger fs-8 fw-boldest animation-blink">PART NUMBER DAN SKU TIDAK SAMA</span>';
                }

                if(trim((empty($data->marketplace->productID)) ? '' : $data->marketplace->productID) != '') {
                    if(strtoupper(trim($data->product_id)) != strtoupper(trim((empty($data->marketplace->productID)) ? '' : $data->marketplace->productID))) {
                        $table_detail .= '<span class="badge badge-danger fs-8 fw-boldest animation-blink mt-2">PRODUCT ID MASTER DAN MARKETPLACE TIDAK SAMA</span>';
                    }
                } else {
                    $table_detail .= '<span class="badge badge-danger fs-8 fw-boldest animation-blink mt-2">PRODUCT ID MASTER DAN MARKETPLACE TIDAK SAMA</span>';
                }

                $table_detail .= '
                        </div>
                    </td>
                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                        <span class="fs-7 fw-bolder text-gray-800">'.trim($data->nama_part).'</span>
                    </td>
                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">';

                if((int)$data->status_mp->update == 1) {
                    $table_detail .= '<i class="fa fa-check text-success"></i>';
                } else {
                    $table_detail .= '<i class="fa fa-minus-circle text-gray-400"></i>';
                }

                $table_detail .= '</td>
                    <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                        <span class="fs-7 fw-bolder text-gray-800">'.number_format($data->pindah).'</span>
                    </td>
                    <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                        <span class="fs-7 fw-bolder text-gray-800">'.number_format($data->stock_suma).'</span>
                    </td>
                    <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                        <span class="fs-7 fw-bolder text-gray-800">'.number_format(empty($data->marketplace->stock) ? 0 : $data->marketplace->stock).'</span>
                    </td>
                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">';

                if((int)$data->status_mp->update == 0) {
                    if(strtoupper(trim($data->indicator)) == 'INCREMENT') {
                        $table_detail .= '<span class="fs-7 fw-boldest text-success">
                                <i class="fa fa-arrow-up me-2 text-success" aria-hidden="true"></i>'.((double)empty($data->marketplace->stock) ? 0 : $data->marketplace->stock + (double)$data->pindah).'
                            </span>';
                    } else {
                        $table_detail .= '<span class="fs-7 fw-boldest text-danger">
                                <i class="fa fa-arrow-down me-2 text-danger" aria-hidden="true"></i>'.((double)empty($data->marketplace->stock) ? 0 : $data->marketplace->stock - (double)$data->pindah).'
                            </span>';
                    }
                }

                $table_detail .= '</td>
                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">';

                if((int)$data->status_mp->show == 1) {
                    $table_detail .= '<button id="btnUpdatePerPartNumber" class="btn btn-icon btn-sm btn-primary" type="button"
                            data-nomor_dokumen="'.strtoupper(trim($data->nomor_dokumen)).'"
                            data-part_number="'.strtoupper(trim($data->part_number)).'">
                            <i class="fa fa-check text-white"></i>
                        </button>';
                } else {
                    if(strtoupper(trim($data->status_mp->keterangan)) == 'DATA BELUM DI VALIDASI') {
                        $table_detail .= '<span class="fs-8 fw-boldest text-danger">'.strtoupper(trim($data->status_mp->keterangan)).'</span>';
                    } else {
                        $table_detail .= '<span class="fs-8 fw-boldest text-success">'.strtoupper(trim($data->status_mp->keterangan)).'</span>';
                    }
                }

                $table_detail .= '</td>
                    </tr>';
            }

            if(trim($table_detail) == '') {
                $table_detail .= ' <tr>
                        <td colspan="9" class="pt-12 pb-12">
                            <div class="row text-center pe-10">
                                <span class="svg-icon svg-icon-muted">
                                    <svg class="h-100px w-100px" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M21.7 18.9L18.6 15.8C17.9 16.9 16.9 17.9 15.8 18.6L18.9 21.7C19.3 22.1 19.9 22.1 20.3 21.7L21.7 20.3C22.1 19.9 22.1 19.3 21.7 18.9Z" fill="currentColor"/>
                                        <path opacity="0.3" d="M11 20C6 20 2 16 2 11C2 6 6 2 11 2C16 2 20 6 20 11C20 16 16 20 11 20ZM11 4C7.1 4 4 7.1 4 11C4 14.9 7.1 18 11 18C14.9 18 18 14.9 18 11C18 7.1 14.9 4 11 4ZM8 11C8 9.3 9.3 8 11 8C11.6 8 12 7.6 12 7C12 6.4 11.6 6 11 6C8.2 6 6 8.2 6 11C6 11.6 6.4 12 7 12C7.6 12 8 11.6 8 11Z" fill="currentColor"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="row text-center pt-8">
                                <span class="fs-6 fw-bolder text-gray-500">-  Tidak ada data yang ditampilkan -</span>
                            </div>
                        </td>
                    </tr>';
            }

            $table_header = '<div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle">
                    <thead class="border">
                        <tr class="fs-8 fw-bolder text-muted">
                            <th rowspan="2" class="w-50px ps-3 pe-3 text-center">No</th>
                            <th rowspan="2" class="w-200px ps-3 pe-3 text-center">Part Number</th>
                            <th rowspan="2" class="min-w-150px ps-3 pe-3 text-center">Nama Part</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3 text-center">Status</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3 text-center">Pindah</th>
                            <th colspan="3" class="w-150px ps-3 pe-3 text-center">Stock</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3 text-center">Action</th>
                        </tr>
                        <tr class="fs-8 fw-bolder text-muted">
                            <th class="w-100px ps-3 pe-3 text-center">Suma</th>
                            <th class="w-100px ps-3 pe-3 text-center">Tokopedia</th>
                            <th class="w-100px ps-3 pe-3 text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody class="border">'.$table_detail.'</tbody>
                </table>
            </div>';

            return ['status' => 1, 'message' => 'success', 'data' => $table_header];
        } else {
            return json_decode($responseApi, true);
        }
    }

    public function updateStockPerPartNumber(Request $request) {
        $validate = Validator::make($request->all(), [
            'nomor_dokumen' => 'required',
            'part_number'   => 'required',
        ]);

        if($validate->fails()) {
            return response()->json([ 'status' => 0, 'message' => 'Pilih data part number yang ingin di update terlebih dahulu' ]);
        }

        $responseApi = ApiService::OnlinePemindahanTokopediaUpdatePerPartNumber(strtoupper(trim($request->get('nomor_dokumen'))),
                                trim($request->get('part_number')), strtoupper(trim($request->session()->get('app_user_company_id'))));

        return json_decode($responseApi, true);
    }

    public function updateStockPerNomorDokumen(Request $request) {
        $validate = Validator::make($request->all(), [
            'nomor_dokumen' => 'required'
        ]);

        if($validate->fails()) {
            return redirect()->back()->withInput()->with('failed', 'Pilih nomor dokumen terlebih dahulu');
        }

        $responseApi = ApiService::OnlinePemindahanTokopediaUpdatePerNomorDokumen(strtoupper(trim($request->get('nomor_dokumen'))),
                                strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi = json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;

            $table_update_stock_header = '';
            $table_update_stock_detail = '';
            $table_update_status = '';

            foreach($dataApi->update->stock->success->data as $data) {
                $table_update_stock_detail .= '<tr>
                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                        <span class="fs-7 fw-bold text-gray-800">'.$data->product_id.'</span>
                    </td>
                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                        <span class="fs-7 fw-bold text-gray-800">Jumlah stock berhasil diupdate menjadi '.number_format($data->stock).'</span>
                    </td>
                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                        <span class="badge badge-success">Success</span>
                    </td>
                </tr>';
            }

            foreach($dataApi->update->stock->error->data as $data) {
                $table_update_stock_detail .= '<tr>
                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                        <span class="fs-7 fw-bold text-gray-800">'.$data->product_id.'</span>
                    </td>
                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                        <span class="fs-7 fw-bold text-gray-800">'.$data->message.'</span>
                    </td>
                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                        <span class="badge badge-danger">Failed</span>
                    </td>
                </tr>';
            }

            $table_update_stock_header .= '<div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle">
                    <thead class="border">
                        <tr class="fs-8 fw-bolder text-muted">
                            <th class="w-100px ps-3 pe-3 text-center">Product ID</th>
                            <th class="min-w-150px ps-3 pe-3 text-center">Keterangan</th>
                            <th class="w-100px ps-3 pe-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="border">'.$table_update_stock_detail.'</tbody>
                </table>';

            // ====================================================================================
            // UPDATE STATUS
            // ====================================================================================
            $table_update_status .= '<div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle">
                        <tbody class="border">
                            <tr>
                                <td class="ps-3 pe-3 w-75px" style="text-align:left;vertical-align:top;">
                                    <span class="fs-8 fw-bolder text-muted">Error</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                    <span class="fs-7 fw-bold text-gray-800">'.trim($dataApi->update->status->error).'</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-3 pe-3 w-75px" style="text-align:left;vertical-align:top;">
                                    <span class="fs-8 fw-bolder text-muted">Message</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                    <span class="fs-7 fw-bold text-gray-800">'.trim($dataApi->update->status->message).'</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>';

            $data = [
                'status'    => $statusApi,
                'message'   => $messageApi,
                'data'      => [
                    'update_stock'      => $table_update_stock_header,
                    'update_status'     => $table_update_status
                ]
            ];
            return $data;
        } else {
            return redirect()->back()->withInput()->with('failed', 'Server tidak merespon, coba lagi');
        }

    }
}
