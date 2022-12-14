<?php

namespace App\Http\Controllers\App\Orders;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Helpers\ApiService;

use Illuminate\Http\Request;
use Jenssegers\Agent\Agent as Agent;

class PembayaranFakturController extends Controller
{
    public function index() {
        return redirect()->route('orders.pembayaran-faktur-belum-terbayar');
    }

    public function pembayaranFakturBelumTerbayar(Request $request) {
        $kode_sales = '';
        $kode_dealer = '';

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
            $responseApi = ApiService::PembayaranFakturDaftar(date('Y'), date('m'), trim($kode_sales), trim($kode_dealer), 'BELUM_LUNAS',
                        $request->get('nomor_faktur'), $request->get('page'), strtoupper(trim($request->session()->get('app_user_id'))),
                        strtoupper(trim($request->session()->get('app_user_role_id'))), strtoupper(trim($request->session()->get('app_user_company_id'))));

            $statusApi = json_decode($responseApi)->status;
            $messageApi =  json_decode($responseApi)->message;
        }else{
            $statusApi = 1;
            $messageApi =  '';
        }

        if($statusApi == 1) {
            if(!empty($request->get('salesman')) || !empty($request->get('dealer')) || !empty($request->get('nomor_faktur'))) {
                $data = json_decode($responseApi)->data;
            } else {
                $data = (object)[
                    'data' => [],
                ];
            }
            $data_pembayaran = $data->data;

            if ($request->ajax()) {
                $view = view('layouts.orders.pembayaranfaktur.pembayaranfakturlist', compact('data_pembayaran'))->render();
                return response()->json([ 'html' => $view ]);
            }

            $Agent = new Agent();

            $device = 'Desktop';
            if ($Agent->isMobile()) {
                $device = 'Mobile';
            }

            return view('layouts.orders.pembayaranfaktur.pembayaranfakturbelumterbayar', [
                'title_menu'        => 'Belum Terbayar',
                'device'            => $device,
                'role_id'           => strtoupper(trim($request->session()->get('app_user_role_id'))),
                'kode_sales'        => trim($kode_sales),
                'kode_dealer'       => trim($kode_dealer),
                'nomor_faktur'      => $request->get('nomor_faktur'),
                'data_pembayaran'   => $data_pembayaran
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function pembayaranFakturTerbayar(Request $request) {
        $year = date('Y');
        $month = date('m');

        if(!empty($request->get('year'))) {
            $year = $request->get('year');
        }
        if(!empty($request->get('month'))) {
            $month = $request->get('month');
        }

        $kode_sales = '';
        $kode_dealer = '';

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


        if(!empty($request->get('month')) || !empty($request->get('year'))) {
            $responseApi = ApiService::PembayaranFakturDaftar($year, $month, trim($kode_sales), trim($kode_dealer), 'LUNAS',
                        $request->get('nomor_faktur'), $request->get('page'), strtoupper(trim($request->session()->get('app_user_id'))),
                        strtoupper(trim($request->session()->get('app_user_role_id'))), strtoupper(trim($request->session()->get('app_user_company_id'))));
            $statusApi = json_decode($responseApi)->status;
            $messageApi =  json_decode($responseApi)->message;
        }else{
            $statusApi = 1;
            $messageApi =  '';
        }

        if($statusApi == 1) {
            if(!empty($request->get('month'))||!empty($request->get('year'))) {
                $data = json_decode($responseApi)->data;
            } else {
                $data = (object)[
                    'data' => [],
                ];
            }
            $data_pembayaran = $data->data;

            if($request->ajax()) {
                $view = view('layouts.orders.pembayaranfaktur.pembayaranfakturlist', compact('data_pembayaran'))->render();
                return response()->json([ 'html' => $view ]);
            }

            $Agent = new Agent();

            $device = 'Desktop';
            if ($Agent->isMobile()) {
                $device = 'Mobile';
            }

            return view('layouts.orders.pembayaranfaktur.pembayaranfakturterbayar', [
                'title_menu'        => 'Terbayar',
                'device'            => $device,
                'year'              => $year,
                'month'             => $month,
                'role_id'           => strtoupper(trim($request->session()->get('app_user_role_id'))),
                'kode_sales'        => trim($kode_sales),
                'kode_dealer'       => trim($kode_dealer),
                'nomor_faktur'      => trim($request->get('nomor_faktur')),
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
                    $view_detail_desktop .= '<tr>
                                <td class="text-center">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">';

                    if($result->status_realisasi == 1) {
                        $view_detail_desktop .= '<input class="form-check-input widget-9-check" type="checkbox" value="1" checked disabled>';
                    } else {
                        $view_detail_desktop .= '<input class="form-check-input widget-9-check" type="checkbox" value="1" disabled>';
                    }

                    $view_detail_desktop .= '</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex justify-content-start flex-column">
                                            <a href="#" id="viewPembayaranPerNomorBpk" type="button" data-bs-toggle="modal" data-bs-target="#modalPembayaranPerNomorBpk"
                                                data-kode="'.strtoupper(trim($result->nomor_bpk)).'"
                                                class="text-dark fw-bolder text-hover-primary fs-7">'.strtoupper(trim($result->nomor_bpk)).'</a>
                                            <span class="text-muted fw-bold text-muted d-block fs-7">'.strtoupper(trim($result->tanggal_input)).'</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-start flex-column">
                                        <span class="text-dark fw-bolder fs-7">'.strtoupper(trim($result->nomor_giro)).'</span>
                                    </div>
                                </td>
                                <td><span class="badge badge-light-info fs-7 fw-bolder">'.strtoupper(trim($result->nama_bank)).'</span></td>
                                <td>
                                    <div class="d-flex justify-content-start flex-column">';

                    if($result->status_realisasi == 1) {
                        $view_detail_desktop .= '<span class="text-success text-end fw-bolder fs-7">Rp. '.number_format((double)$result->jumlah_pembayaran).'</span>';
                    } else {
                        $view_detail_desktop .= '<span class="text-danger text-end fw-bolder fs-7">Rp. '.number_format((double)$result->jumlah_pembayaran).'</span>';
                    }

                    $view_detail_desktop .= '</div>
                                    </td>
                                </tr>';
                } else {
                    $view_detail_mobile .= '<div id="viewPembayaranPerNomorBpk" type="button" data-bs-toggle="modal" data-bs-target="#modalPembayaranPerNomorBpk"
                                    data-kode="'.strtoupper(trim($result->nomor_bpk)).'"
                                    class="fv-row mb-4 rounded border-gray-300 border-2 border-gray-300 border-dashed px-7 py-3 mb-4">
                                <div class="fv-row mt-4 mb-4 fv-plugins-icon-container">
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex justify-content-start flex-column">
                                            <span class="text-dark fw-bolder text-hover-primary fs-7">'.strtoupper(trim($result->nomor_bpk)).'</a>
                                            <span class="text-muted fw-bold text-muted d-block fs-7">'.strtoupper(trim($result->tanggal_input)).'</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="fv-row mb-7 fv-plugins-icon-container">
                                    <div class="d-flex justify-content-start flex-column">
                                        <span class="text-dark fw-bolder fs-7 me-2 mt-2">'.strtoupper(trim($result->nomor_giro)).'
                                            <span class="badge badge-light-info fs-7 fw-bolder">('.strtoupper(trim($result->nama_bank)).')</span>
                                        </span>';

                    if($result->status_realisasi == 1) {
                        $view_detail_mobile .= '<span class="text-success fw-bolder fs-7">Rp. '.number_format((double)$result->jumlah_pembayaran).'</span>';
                    } else {
                        $view_detail_mobile .= '<span class="text-danger fw-bolder fs-7">Rp. '.number_format((double)$result->jumlah_pembayaran).'</span>';
                    }

                    $view_detail_mobile .= '</div>';

                    if($result->status_realisasi == 1) {
                        $view_detail_mobile .= '<div class="form-check form-check-sm form-check-custom form-check-solid mt-8">
                                            <input class="form-check-input widget-9-check" type="checkbox" value="1" checked disabled>
                                            <span class="text-dark fw-bold fs-7 ms-2">Realisasi</span>
                                        </div>';
                    } else {
                        $view_detail_mobile .= '<div class="form-check form-check-sm form-check-custom form-check-solid mt-8">
                                            <input class="form-check-input widget-9-check" type="checkbox" value="1"  disabled>
                                            <span class="text-dark fw-bold fs-7 ms-2">Realisasi</span>
                                        </div>';
                    }
                    $view_detail_mobile .= '</div>
                                    </div>';
                    }
            }

            if(strtoupper(trim($device)) == 'DESKTOP') {
                $view_header_desktop = '<div class="table-responsive">
                                <table class="table table-row-dashed table-row-gray-300">
                                <thead>
                                    <tr class="fw-bold text-muted">
                                        <th class="min-w-5px">Status</th>
                                        <th class="min-w-100px">No. Bukti</th>
                                        <th class="min-w-100px">No. Giro</th>
                                        <th class="min-w-80px">Bank</th>
                                        <th class="min-w-80px text-end">Jml Bayar</th>
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
                    $view_detail_desktop .= '<tr>
                            <td><span class="text-dark fw-bold fs-7">'.strtoupper(trim($result->nomor_faktur)).'</span></td>
                            <td><span class="text-dark fw-bold fs-7">'.strtoupper(trim($result->tanggal_faktur)).'</span></td>';

                    if((double)$result->total_faktur > (double)$result->total_pembayaran_faktur) {
                        $view_detail_desktop .= '<td class="text-end"><span class="text-dark fw-bold fs-7">Rp. '.number_format((double)$result->total_faktur).'</span></td>
                                            <td class="text-end"><span class="text-danger fw-bolder fs-7">Rp. '.number_format((double)$result->total_pembayaran_faktur).'</span></td>';
                    } else {
                        $view_detail_desktop .= '<td class="text-end"><span class="text-dark fw-bold fs-7">Rp. '.number_format((double)$result->total_faktur).'</span></td>
                                            <td class="text-end"><span class="text-success fw-bolder fs-7">Rp. '.number_format((double)$result->total_pembayaran_faktur).'</span></td>';
                    }

                    $view_detail_desktop .= '</tr>';
                } else {
                    $view_detail_mobile .= '<div class="fv-row mb-4 rounded border-gray-300 border-2 border-gray-300 border-dashed px-7 py-3 mb-4">
                            <div class="fv-row mt-4 fv-plugins-icon-container">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex justify-content-start flex-column">
                                        <span class="text-dark fw-bolder fs-6">'.strtoupper(trim($result->nomor_faktur)).'</a>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">'.strtoupper(trim($result->tanggal_faktur)).'</span>
                                    </div>
                                </div>
                            </div>
                            <div class="fv-row mb-7 fv-plugins-icon-container">
                                <div class="d-flex justify-content-start flex-column">
                                <div class="row">';

                    if((double)$result->total_faktur > (double)$result->total_pembayaran_faktur) {
                        $view_detail_mobile .= '<div class="col-md-6 mt-4">
                                <span class="text-muted fw-bold text-muted d-block fs-7">Total Faktur :</span>
                                <span class="text-dark fw-bold fs-6">Rp. '.number_format((double)$result->total_faktur).'</a>
                            </div>
                            <div class="col-md-6 mt-4">
                                <span class="text-muted fw-bold text-muted d-block fs-7">Total Pembayaran :</span>
                                <span class="text-danger fw-bold fs-6">Rp. '.number_format((double)$result->total_pembayaran_faktur).'</a>
                            </div>';
                    } else {
                        $view_detail_mobile .= '<div class="col-md-6 mt-4">
                                <span class="text-muted fw-bold text-muted d-block fs-7">Total Faktur :</span>
                                <span class="text-dark fw-bold fs-6">Rp. '.number_format((double)$result->total_faktur).'</a>
                            </div>
                            <div class="col-md-6 mt-4">
                                <span class="text-muted fw-bold text-muted d-block fs-7">Total Pembayaran :</span>
                                <span class="text-success fw-bold fs-6">Rp. '.number_format((double)$result->total_pembayaran_faktur).'</a>
                            </div>';
                    }

                    $view_detail_mobile .= '</div>
                                </div>
                            </div>
                        </div>';
                }
            }

            if(strtoupper(trim($device)) == 'DESKTOP') {
                $view_header_desktop = '<div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300">
                        <thead>
                            <tr class="fw-bolder text-muted">
                                <th class="min-w-50px">Nomor Faktur</th>
                                <th class="min-w-50px">Tgl Faktur</th>
                                <th class="min-w-50px text-end">Total</th>
                                <th class="min-w-50px text-end">Jml Bayar</th>
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
