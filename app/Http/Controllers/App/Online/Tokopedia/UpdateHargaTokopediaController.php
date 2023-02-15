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

class UpdateHargaTokopediaController extends Controller
{
    public function daftarUpdateHarga(Request $request) {
        $year = date('Y');
        $month = date('m');

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

        $responseApi = ApiService::OnlineUpdateHargaTokopediaDaftar($request->get('page'), $per_page, $year, $month,
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
                'kode_lokasi'   => config('constants.tokopedia.kode_lokasi')
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

            return view ('layouts.online.tokopedia.updateharga.updateharga', [
                'title_menu'        => 'Update Harga Tokopedia',
                'data_page'         => $data_page->first(),
                'data_filter'       => $data_filter->first(),
                'data_device'       => $data_device->first(),
                'data_user'         => $data_user->first(),
                'data_update_harga' => $dataUpdateHarga,
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function buatDokumen(Request $request) {
        $responseApi = ApiService::OnlineUpdateHargaBuatDokumen($request->get('kode'), date('Y-m-d'),
                        strtoupper(trim($request->session()->get('app_user_company_id'))),
                        strtoupper(trim($request->session()->get('app_user_id'))));
        return json_decode($responseApi, true);
    }

    public function formUpdateHarga($nomor_dokumen, Request $request) {
        $responseApi = ApiService::OnlineUpdateHargaTokopediaForm($nomor_dokumen,
                        strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;

            return view('layouts.online.tokopedia.updateharga.updatehargaform', [
                'title_menu'    => 'Update Harga Tokopedia',
                'data'          => $dataApi
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function formUpdateHargaDetail(Request $request) {
        $responseApi = ApiService::OnlineUpdateHargaTokopediaForm($request->get('nomor_dokumen'),
                        strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;

        if($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;

            $table_detail = '';
            $table_header = '';
            $nomor_urut = 0;
            $jumlah_data = 0;

            foreach($dataApi->detail as $data) {
                $nomor_urut = (double)$nomor_urut + 1;
                $jumlah_data = (double)$jumlah_data + 1;

                $table_detail .= '<tr>
                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                        <span class="fs-7 fw-bold text-gray-800">'.number_format($nomor_urut).'</span>
                    </td>
                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                        <span class="fs-7 fw-boldest text-gray-800 d-block">'.strtoupper(trim($data->part_number)).'</span>
                        <span class="fs-8 fw-bolder text-gray-600 d-block">'.strtoupper(trim($data->nama_part)).'</span>
                        <span class="fs-8 fw-bolder text-gray-400 mt-4 d-block">Product ID :</span>';

                if(trim($data->product_id) == '') {
                    $table_detail .= '<span class="fs-8 fw-boldest text-danger">(Product ID masih kosong)</span>';
                } else {
                    $table_detail .= '<span class="fs-8 fw-boldest text-gray-800">'.strtoupper(trim($data->product_id)).'</span>';
                }

                $table_detail .= '</td>
                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">';

                if((int)$data->update == 1) {
                    $table_detail .= '<i class="fa fa-check text-success"></i>';
                } else {
                    $table_detail .= '<i class="fa fa-minus-circle text-gray-400"></i>';
                }

                $table_detail .= '</td>
                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">';

                if((int)$data->update == 1) {
                    $table_detail .= '<span class="fs-7 fw-boldest text-success">Data sudah diupdate</span>';
                } else {
                    $table_detail .= '<span class="fs-7 fw-boldest text-danger">'.$data->keterangan.'</span>';
                }


                $table_detail .= '</td>';

                $table_detail .= '<td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                        <span class="fs-7 fw-bolder text-gray-800">'.number_format($data->het_lama).'</span>
                    </td>
                    <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                        <span class="fs-7 fw-bolder text-gray-800">'.number_format($data->het_baru).'</span>
                    </td>
                    <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                        <span class="fs-7 fw-bolder text-gray-800">'.number_format($data->selisih).'</span>
                    </td>
                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">';

                if(strtoupper(trim($data->status)) == 'NAIK') {
                    $table_detail .= '<span class="fs-7 fw-boldest text-success">
                            <i class="fa fa-arrow-up me-2 text-success" aria-hidden="true"></i>'.number_format($data->prosentase, 2).'%
                        </span>';
                } else {
                    $table_detail .= '<span class="fs-7 fw-boldest text-danger">
                            <i class="fa fa-arrow-down me-2 text-danger" aria-hidden="true"></i>'.number_format($data->prosentase, 2).'%
                        </span>';
                }

                $table_detail .= '</td>
                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">';

                if((int)$data->update == 0) {
                    $table_detail .= '<button id="btnUpdatePerPartNumber" class="btn btn-icon btn-sm btn-secondary" type="button"
                            data-nomor_dokumen="'.strtoupper(trim($dataApi->nomor_dokumen)).'"
                            data-part_number="'.strtoupper(trim($data->part_number)).'">
                            <img src="'.asset('assets/images/logo/tokopedia_lg.png').'" class="h-30px" />
                        </button>';
                }

                $table_detail .= '</td>
                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">';

                if((int)$data->update == 0) {
                    $table_detail .= '<button id="btnUpdateStatusPerPartNumber" class="btn btn-icon btn-sm btn-danger" type="button"
                            data-nomor_dokumen="'.strtoupper(trim($dataApi->nomor_dokumen)).'"
                            data-part_number="'.strtoupper(trim($data->part_number)).'">
                            <i class="fa fa-database" aria-hidden="true"></i>
                    </button>';
                }

                $table_detail .= '</td>
                </tr>';
            }

            if((double)$jumlah_data <= 0) {
                $table_detail .= '<tr>
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

            $table_header .= '<div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle">
                        <thead class="border">
                            <tr class="fs-8 fw-bolder text-muted">
                                <th rowspan="2" class="w-50px ps-3 pe-3 text-center">No</th>
                                <th rowspan="2" class="w-200px ps-3 pe-3 text-center">Part Number</th>
                                <th rowspan="2" class="w-50px ps-3 pe-3 text-center">Status</th>
                                <th rowspan="2" class="min-w-100px ps-3 pe-3 text-center">Keterangan</th>
                                <th colspan="4" class="w-100px ps-3 pe-3 text-center">HET</th>
                                <th colspan="2" class="w-100px ps-3 pe-3 text-center">Action</th>
                            </tr>
                            <tr class="fs-8 fw-bolder text-muted">
                                <th class="w-100px ps-3 pe-3 text-center">Lama</th>
                                <th class="w-100px ps-3 pe-3 text-center">Baru</th>
                                <th class="w-100px ps-3 pe-3 text-center">Selisih</th>
                                <th class="w-100px ps-3 pe-3 text-center">Status</th>
                                <th class="w-50px ps-3 pe-3 text-center">Marketplace</th>
                                <th class="w-50px ps-3 pe-3 text-center">Internal</th>
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

    public function updateHargaPerPartNumber(Request $request) {
        $responseApi = ApiService::OnlineUpdateHargaTokopediaUpdatePerPartNumber(strtoupper(trim($request->get('nomor_dokumen'))),
                                trim($request->get('part_number')), strtoupper(trim($request->session()->get('app_user_company_id'))));

        return json_decode($responseApi, true);
    }

    public function updateHargaStatusPerPartNumber(Request $request) {
        $responseApi = ApiService::OnlineUpdateHargaTokopediaUpdateStatusPartNumber(strtoupper(trim($request->get('nomor_dokumen'))),
                trim($request->get('part_number')), strtoupper(trim($request->session()->get('app_user_company_id'))));

        return json_decode($responseApi, true);
    }

    public function updateHargaPerNomorDokumen(Request $request) {
        $responseApi = ApiService::OnlineUpdateHargaTokopediaUpdatePerNomorDokumen(strtoupper(trim($request->get('nomor_dokumen'))),
                                strtoupper(trim($request->session()->get('app_user_company_id'))));
        return json_decode($responseApi, true);
    }
}
