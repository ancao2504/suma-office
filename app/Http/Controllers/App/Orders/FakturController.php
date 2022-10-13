<?php

namespace App\Http\Controllers\App\Orders;

use App\Http\Controllers\Controller;
use App\Helpers\ApiService;

use Illuminate\Http\Request;
use Jenssegers\Agent\Agent as Agent;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class FakturController extends Controller
{
    public function index(Request $request) {
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
        $per_page = 10;

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

        $responseApi = ApiService::FakturDaftar($year, $month, $kode_sales, $kode_dealer, $request->get('nomor_faktur'),
                                        $request->get('page'), $per_page, strtoupper(trim($request->session()->get('app_user_id'))),
                                        strtoupper(trim($request->session()->get('app_user_role_id'))), strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            $data_page = new Collection();
            $data_page->push((object) [
                'from'  => $data->from,
                'to'    => $data->to,
                'total' => $data->total,
                'page'  => $data->current_page,
                'per_page' => $data->per_page
            ]);

            if($Agent->isMobile()) {
                $data_faktur = $data->data;

                if ($request->ajax()) {
                    $view = view('layouts.orders.faktur.mobile.fakturlist', compact('data_faktur'))->render();
                    return response()->json([ 'html' => $view ]);
                }
            } else {
                $data_faktur = new LengthAwarePaginator(array_values($data->data), $data->total, $data->per_page, $data->current_page,
                [ 'path' => route('orders.faktur'), 'query' => request()->query()  ]);
            }

            return view('layouts.orders.faktur.faktur', [
                'title_menu'    => 'Faktur',
                'device'        => $device,
                'year'          => $year,
                'month'         => $month,
                'role_id'       => strtoupper(trim($request->session()->get('app_user_role_id'))),
                'kode_sales'    => $kode_sales,
                'kode_dealer'   => $kode_dealer,
                'nomor_faktur'  => $request->get('nomor_faktur'),
                'page'          => $data_page->first(),
                'data_faktur'   => $data_faktur,
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function fakturView($nomor_faktur, Request $request) {
        $responseApi = ApiService::FakturDetail(strtoupper(trim($nomor_faktur)), strtoupper(trim($request->session()->get('app_user_id'))),
                                        strtoupper(trim($request->session()->get('app_user_role_id'))), strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            $Agent = new Agent();

            $device = 'Desktop';
            if ($Agent->isMobile()) {
                $device = 'Mobile';
            }

            return view('layouts.orders.faktur.fakturform', [
                'title_menu'    => 'Faktur',
                'device'        => $device,
                'nomor_faktur'  => $data->nomor_faktur,
                'nomor_pof'     => $data->nomor_pof,
                'tanggal_faktur' => $data->tanggal_faktur,
                'kode_sales'    => $data->kode_sales,
                'kode_dealer'   => $data->kode_dealer,
                'kode_tpc'      => $data->kode_tpc,
                'bo'            => $data->bo,
                'total_order'   => (double)$data->total_order,
                'total_jual'    => (double)$data->total_jual,
                'sub_total'     => (double)$data->sub_total,
                'disc_header'   => (double)$data->disc_header,
                'nominal_disc_header' => (double)$data->nominal_disc_header,
                'disc_rupiah'   => (double)$data->disc_rupiah,
                'grand_total'   => (double)$data->grand_total,
                'detail_faktur' => $data->detail_faktur
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }
}
