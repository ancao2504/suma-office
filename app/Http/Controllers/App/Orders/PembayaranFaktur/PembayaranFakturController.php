<?php

namespace App\Http\Controllers\App\Orders\PembayaranFaktur;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Helpers\ApiService;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Jenssegers\Agent\Agent as Agent;

class PembayaranFakturController extends Controller
{
    public function index() {
        return redirect()->route('orders.pembayaranfaktur.daftar-belum-terbayar');
    }

    public function pembayaranFakturBelumTerbayar(Request $request) {
        $kode_sales = '';
        $kode_dealer = '';

        $Agent = new Agent();
        $device = 'Desktop';
        if ($Agent->isMobile()) {
            $device = 'Mobile';
        }

        $per_page = 10;
        if(!empty($request->get('per_page')) && $request->get('per_page') != '') {
            if($request->get('per_page') == 10 || $request->get('per_page') == 25 || $request->get('per_page') == 50 || $request->get('per_page') == 100) {
                $per_page = $request->get('per_page');
            } else {
                $per_page = 10;
            }
        }

        if(strtoupper(trim($request->session()->get('app_user_role_id'))) == "D_H3") {
            $responseApi = ApiService::ValidasiDealer(strtoupper(trim($request->session()->get('app_user_id'))), strtoupper(trim($request->session()->get('app_user_company_id'))));
            $statusApi = json_decode($responseApi)->status;
            $messageApi =  json_decode($responseApi)->message;

            if($statusApi == 1) {
                $kode_dealer = strtoupper(trim(json_decode($responseApi)->data->kode_dealer));
                $kode_sales = strtoupper(trim(json_decode($responseApi)->data->kode_sales));
            } else {
                return redirect()->back()->withInput()->with('failed', $messageApi);
            }
        } elseif(strtoupper(trim($request->session()->get('app_user_role_id'))) == "MD_H3_SM") {
            $kode_sales = trim($request->session()->get('app_user_id'));
        }

        if($kode_sales == '') {
            if(!empty($request->get('salesman'))) {
                $kode_sales = trim($request->get('salesman'));
            }
        }

        if($kode_dealer == '') {
            if(!empty($request->get('dealer'))) {
                $kode_dealer = trim($request->get('dealer'));
            }
        }

        if(!empty($request->get('salesman')) || !empty($request->get('dealer'))) {
            $responseApi = ApiService::PembayaranFakturDaftar($request->get('page'), $per_page, date('Y'), date('m'),
                                trim($kode_sales), trim($kode_dealer), 'BELUM_LUNAS', $request->get('nomor_faktur'),
                                strtoupper(trim($request->session()->get('app_user_id'))),
                                strtoupper(trim($request->session()->get('app_user_role_id'))),
                                strtoupper(trim($request->session()->get('app_user_company_id'))));
            $statusApi = json_decode($responseApi)->status;
            $messageApi =  json_decode($responseApi)->message;
        } else {
            $statusApi = 1;
            $messageApi =  '';
        }

        if($statusApi == 1) {
            $data_pembayaran = new Collection();
            if(!empty($request->get('salesman')) || !empty($request->get('dealer'))) {
                $data = json_decode($responseApi)->data;
                $data_pembayaran = $data->data;
            }

            $data_page = new Collection();
            $data_page->push((object) [
                'from'          => (empty($data->from)) ? 0 : $data->from,
                'to'            => (empty($data->from)) ? 0 : $data->to,
                'total'         => (empty($data->from)) ? 0 : $data->total,
                'current_page'  => (empty($data->from)) ? 0 : $data->current_page,
                'per_page'      => (empty($data->from)) ? 0 : $data->per_page,
                'links'         => (empty($data->links)) ? [] : $data->links,
            ]);

            $data_filter = new Collection();
            $data_filter->push((object) [
                'kode_sales'    => $kode_sales,
                'kode_dealer'   => $kode_dealer,
                'nomor_faktur'  => $request->get('nomor_faktur'),
            ]);

            $data_user = new Collection();
            $data_user->push((object) [
                'user_id'       => strtoupper(trim($request->session()->get('app_user_id'))),
                'role_id'       => strtoupper(trim($request->session()->get('app_user_role_id'))),
            ]);

            $data_device = new Collection();
            $data_device->push((object) [
                'device'        => $device
            ]);

            return view('layouts.orders.pembayaranfaktur.pembayaranfakturbelumterbayar', [
                'title_menu'        => 'Belum Terbayar',
                'data_device'       => $data_device->first(),
                'data_page'         => $data_page->first(),
                'data_filter'       => $data_filter->first(),
                'data_user'         => $data_user->first(),
                'data_pembayaran'   => $data_pembayaran
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function pembayaranFakturTerbayar(Request $request) {
        $year = date('Y');
        $month = date('m');
        $kode_sales = '';
        $kode_dealer = '';

        $Agent = new Agent();
        $device = 'Desktop';
        if ($Agent->isMobile()) {
            $device = 'Mobile';
        }

        $per_page = 10;
        if(!empty($request->get('per_page')) && $request->get('per_page') != '') {
            if($request->get('per_page') == 10 || $request->get('per_page') == 25 || $request->get('per_page') == 50 || $request->get('per_page') == 100) {
                $per_page = $request->get('per_page');
            } else {
                $per_page = 10;
            }
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

        if(strtoupper(trim($request->session()->get('app_user_role_id'))) == "D_H3") {
            $responseApi = ApiService::ValidasiDealer(strtoupper(trim($request->session()->get('app_user_id'))),
                                strtoupper(trim($request->session()->get('app_user_company_id'))));
            $statusApi = json_decode($responseApi)->status;
            $messageApi =  json_decode($responseApi)->message;

            if($statusApi == 1) {
                $kode_dealer = strtoupper(trim(json_decode($responseApi)->data->kode_dealer));
                $kode_sales = strtoupper(trim(json_decode($responseApi)->data->kode_sales));
            } else {
                return redirect()->back()->withInput()->with('failed', $messageApi);
            }
        } elseif(strtoupper(trim($request->session()->get('app_user_role_id'))) == "MD_H3_SM") {
            $kode_sales = trim($request->session()->get('app_user_id'));
        }

        if($kode_sales == '') {
            if(!empty($request->get('salesman'))) {
                $kode_sales = trim($request->get('salesman'));
            }
        }

        if($kode_dealer == '') {
            if(!empty($request->get('dealer'))) {
                $kode_dealer = trim($request->get('dealer'));
            }
        }

        if(!empty($request->get('month')) || !empty($request->get('year'))) {
            $responseApi = ApiService::PembayaranFakturDaftar($request->get('page'), $per_page, $year, $month,
                            trim($kode_sales), trim($kode_dealer), 'LUNAS', $request->get('nomor_faktur'),
                            strtoupper(trim($request->session()->get('app_user_id'))),
                            strtoupper(trim($request->session()->get('app_user_role_id'))),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
            $statusApi = json_decode($responseApi)->status;
            $messageApi =  json_decode($responseApi)->message;
        } else {
            $statusApi = 1;
            $messageApi =  '';
        }

        if($statusApi == 1) {
            $data_pembayaran = new Collection();

            if(!empty($request->get('month')) || !empty($request->get('year'))) {
                $data = json_decode($responseApi)->data;
                $data_pembayaran = $data->data;
            }

            $data_page = new Collection();
            $data_page->push((object) [
                'from'          => (empty($data->from)) ? 0 : $data->from,
                'to'            => (empty($data->from)) ? 0 : $data->to,
                'total'         => (empty($data->from)) ? 0 : $data->total,
                'current_page'  => (empty($data->from)) ? 0 : $data->current_page,
                'per_page'      => (empty($data->from)) ? 0 : $data->per_page,
                'links'         => (empty($data->links)) ? [] : $data->links,
            ]);

            $data_filter = new Collection();
            $data_filter->push((object) [
                'year'          => $year,
                'month'         => $month,
                'kode_sales'    => $kode_sales,
                'kode_dealer'   => $kode_dealer,
                'nomor_faktur'  => $request->get('nomor_faktur'),
            ]);

            $data_user = new Collection();
            $data_user->push((object) [
                'user_id'       => strtoupper(trim($request->session()->get('app_user_id'))),
                'role_id'       => strtoupper(trim($request->session()->get('app_user_role_id'))),
            ]);

            $data_device = new Collection();
            $data_device->push((object) [
                'device'        => $device
            ]);

            return view('layouts.orders.pembayaranfaktur.pembayaranfakturterbayar', [
                'title_menu'        => 'Terbayar',
                'data_device'       => $data_device->first(),
                'data_page'         => $data_page->first(),
                'data_filter'       => $data_filter->first(),
                'data_user'         => $data_user->first(),
                'data_pembayaran'   => $data_pembayaran
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function pembayaranFakturDetailPerFaktur(Request $request) {
        $responseApi = ApiService::PembayaranFakturDetailPerFaktur(strtoupper(trim($request->get('nomor_faktur'))), strtoupper(trim($request->session()->get('app_user_id'))),
                        strtoupper(trim($request->session()->get('app_user_role_id'))), strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;

        if($statusApi == 1) {
            $data_header = json_decode($responseApi)->data;

            $view = '';
            $view_header_desktop = '';
            $view_detail_desktop = '';
            $view_detail_mobile = '';
            $data_detail = $data_header->detail_pembayaran;

            $Agent = new Agent();

            $device = 'Desktop';
            if ($Agent->isMobile()) {
                $device = 'Mobile';
            }

            foreach($data_detail as $result) {
                if(strtoupper(trim($device)) == 'DESKTOP') {

                    $view_detail_desktop .= '<tr class="fw-bolder text-gray-700 fs-6">
                            <td class="text-center">';

                    if($result->status_realisasi == 1) {
                        $view_detail_desktop .= '<i class="fa fa-check text-success"></i>';
                    }

                    $view_detail_desktop .= '</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="d-flex justify-content-start flex-column">
                                        <a href="#" id="formPembayaranNomorBPK" type="button" data-kode="'.strtoupper(trim($result->nomor_bpk)).'"
                                            class="fw-bolder text-gray-700 fs-6 text-hover-primary">'.strtoupper(trim($result->nomor_bpk)).'</a>
                                        <span class="text-gray-600 fw-bold text-muted d-block fs-7">'.date('d F Y', strtotime($result->tanggal_input)).'</span>
                                    </div>
                                </div>
                            </td>
                            <td>'.strtoupper(trim($result->nama_bank)).'</td>
                            <td>'.strtoupper(trim($result->nomor_giro)).'</td>
                            <td class="fs-6 text-dark fw-bolder text-end">'.number_format((double)$result->jumlah_pembayaran).'</td>
                        </tr>';
                } else {
                    $view_detail_mobile .= '<div id="formPembayaranNomorBPK" type="button" data-bs-toggle="modal" data-bs-target="#modalPembayaranBpk"
                                    data-kode="'.strtoupper(trim($result->nomor_bpk)).'"
                                    class="fv-row mb-4 rounded border-gray-300 border-2 border-gray-300 border-dashed px-7 py-3 mb-4">
                                <div class="fv-row mt-4 mb-4">
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex justify-content-start flex-column">
                                            <span class="text-dark fw-bolder text-hover-primary fs-6">'.strtoupper(trim($result->nomor_bpk)).'</a>
                                            <span class="text-gray-600 fw-bold text-muted d-block fs-6">'.date('d F Y', strtotime($result->tanggal_input)).'</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="fv-row">
                                    <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap">
                                        <span class="pe-2">'.strtoupper(trim($result->nomor_giro)).'</span>
                                        <span class="fs-7 text-danger d-flex align-items-center">
                                        <span class="bullet bullet-dot bg-danger me-2"></span>'.strtoupper(trim($result->nama_bank)).'</span>
                                    </div>
                                </div>
                                <div class="fv-row text-dark fw-bold fs-6 mt-4">Rp. '.number_format((double)$result->jumlah_pembayaran).'</div>';

                    if($result->status_realisasi == 1) {
                        $view_detail_mobile .= '<div class="fv-row mt-4 mb-4 align-items-center fw-bold text-gray-800">
                                            <i class="fa fa-check text-success me-2"></i>Realisasi
                                        </div>';
                    } else {
                        $view_detail_mobile .= '<div class="fv-row mt-4 mb-4 align-items-center fw-bold text-gray-800">
                                            <i class="fa fa-remove text-danger me-2"></i>Realisasi
                                        </div>';
                    }
                    $view_detail_mobile .= '</div>';
                }
            }

            if(strtoupper(trim($device)) == 'DESKTOP') {
                $view_header_desktop = '<div class="table-responsive border-bottom mb-14">
                    <table class="table">
                        <thead>
                            <tr class="border-bottom fs-8 fw-boldest text-muted text-uppercase">
                                <th class="w-10px">Realisasi</th>
                                <th class="min-w-175px">Nomor BPK</th>
                                <th class="w-70px">Bank</th>
                                <th class="w-100px">Giro</th>
                                <th class="w-200px text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>'.$view_detail_desktop.'</tbody>
                    </table>
                </div>';
            }

            if(strtoupper(trim($device)) == 'DESKTOP') {
                $view = $view_header_desktop;
            } else {
                $view = $view_detail_mobile;
            }

            $data_faktur = [
                'nomor_faktur'      => strtoupper(trim($data_header->nomor_faktur)),
                'tanggal_faktur'    => strtoupper(trim($data_header->tanggal_faktur)),
                'keterangan'        => trim($data_header->keterangan),
                'kode_sales'        => strtoupper(trim($data_header->kode_sales)),
                'nama_sales'        => strtoupper(trim($data_header->nama_sales)),
                'kode_dealer'       => strtoupper(trim($data_header->kode_dealer)),
                'nama_dealer'       => strtoupper(trim($data_header->nama_dealer)),
                'total_faktur'      => strtoupper(trim($data_header->total_faktur)),
                'total_pembayaran'  => strtoupper(trim($data_header->total_pembayaran)),
                'view_detail'       => $view,
            ];

            $data = [ 'status' => 1, 'message' => 'success', 'data' => $data_faktur ];

            return $data;
        } else {
            return json_decode($responseApi, true);
        }
    }

    public function pembayaranFakturDetailPerBpk(Request $request) {
        $responseApi = ApiService::PembayaranFakturDetailPerBpk(strtoupper(trim($request->get('nomor_bpk'))), strtoupper(trim($request->session()->get('app_user_id'))),
                        strtoupper(trim($request->session()->get('app_user_role_id'))), strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;

        if($statusApi == 1) {
            $data_header = json_decode($responseApi)->data;

            $view = '';
            $view_header_desktop = '';
            $view_detail_desktop = '';
            $view_detail_mobile = '';
            $data_detail = $data_header->detail_pembayaran;

            $Agent = new Agent();

            $device = 'Desktop';
            if ($Agent->isMobile()) {
                $device = 'Mobile';
            }

            foreach($data_detail as $result) {
                if(strtoupper(trim($device)) == 'DESKTOP') {
                    $view_detail_desktop .= '<tr class="fw-bolder text-gray-700 fs-6 border-bottom">
                            <td>'.strtoupper(trim($result->nomor_faktur)).'</td>
                            <td>'.date('d F Y', strtotime($result->tanggal_faktur)).'</td>';

                    if((double)$result->total_faktur > (double)$result->total_pembayaran_faktur) {
                        $view_detail_desktop .= '<td class="text-end" class="text-end text-danger fw-boldest">Rp. '.number_format((double)$result->total_pembayaran_faktur).'</td>';
                    } else {
                        $view_detail_desktop .= '<td class="text-end" class="text-end text-dark fw-boldest">Rp. '.number_format((double)$result->total_pembayaran_faktur).'</td>';
                    }

                    $view_detail_desktop .= '</tr>';
                } else {
                    $view_detail_mobile .= '<div class="fv-row mb-4 rounded border-gray-300 border-2 border-gray-300 border-dashed px-7 py-3 mb-4">
                            <div class="fv-row mt-4 fv-plugins-icon-container">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex justify-content-start flex-column">
                                        <span class="text-dark fw-bolder fs-6">'.strtoupper(trim($result->nomor_faktur)).'</a>
                                        <span class="text-gray-600 fw-bold text-muted d-block fs-6">'.date('d F Y', strtotime($result->tanggal_faktur)).'</span>
                                    </div>
                                </div>
                            </div>
                            <div class="fv-row mt-4 mb-4 fv-plugins-icon-container">
                                <span class="text-dark fw-bold fs-6">Rp. '.number_format((double)$result->total_pembayaran_faktur).'</span>
                            </div>
                        </div>';
                }
            }

            if(strtoupper(trim($device)) == 'DESKTOP') {
                $view_header_desktop = '<div class="table-responsive border-bottom mb-14">
                        <table class="table">
                            <thead>
                                <tr class="border-bottom fs-8 fw-boldest text-muted text-uppercase">
                                    <th class="min-w-175px">Nomor Faktur</th>
                                    <th class="w-200px">Tanggal Faktur</th>
                                    <th class="w-200px text-end">Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody>'.$view_detail_desktop.'</tbody>
                        </table>
                    </div>';
            }

            if(strtoupper(trim($device)) == 'DESKTOP') {
                $view = $view_header_desktop;
            } else {
                $view = $view_detail_mobile;
            }

            $status_realisasi = '';
            if((int)$data_header->status_realisasi == 1) {
                $status_realisasi = '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input widget-9-check" type="checkbox" value="1" checked disabled>
                    <span class="fw-bold fs-7 text-gray-800 ms-2">Realisasi</span>
                </div>';
            } else {
                $status_realisasi = '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input widget-9-check" type="checkbox" value="1" disabled>
                    <span class="fw-bold fs-7 text-gray-800 ms-2">Realisasi</span>
                </div>';
            }

            $data_bpk = [
                'nomor_bukti'       => strtoupper(trim($data_header->nomor_bukti)),
                'tanggal_input'     => strtoupper(trim($data_header->tanggal_input)),
                'kode_sales'        => strtoupper(trim($data_header->kode_sales)),
                'nama_sales'        => strtoupper(trim($data_header->nama_sales)),
                'kode_dealer'       => strtoupper(trim($data_header->kode_dealer)),
                'nama_dealer'       => strtoupper(trim($data_header->nama_dealer)),
                'tunai_giro'        => strtoupper(trim($data_header->tunai_giro)),
                'nomor_giro'        => strtoupper(trim($data_header->nomor_giro)),
                'tanggal_jtp_giro'  => strtoupper(trim($data_header->tanggal_jtp_giro)),
                'account_bank'      => strtoupper(trim($data_header->account_bank)),
                'nama_bank'         => strtoupper(trim($data_header->nama_bank)),
                'status_realisasi'  => $status_realisasi,
                'total_pembayaran'  => (double)$data_header->total_pembayaran,
                'view_detail'       => $view,
            ];
            $data = [ 'status' => 1, 'message' => 'success', 'data' => $data_bpk ];

            return $data;
        } else {
            return json_decode($responseApi, true);
        }
    }
}
