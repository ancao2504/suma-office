<?php

namespace App\Http\Controllers\App\Dashboard\Marketing;

use App\Helpers\App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;

class DashboardMarketingController extends Controller
{
    public function dashboardPencapaianPerLevel(Request $request) {
        $year = date('Y');

        if(!empty($request->get('year'))) {
            $year = $request->get('year');
        }

        $responseApi = Service::DashboardMarketingGroupPerLevel($year, $request->get('jenis_mkr'), $request->get('kode_mkr'),
                            strtoupper(trim($request->session()->get('app_user_role_id'))), strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            $data_filter = new Collection();
            $data_filter->push((object) [
                'year'          => $year,
                'jenis_mkr'     => $request->get('jenis_mkr'),
                'kode_mkr'      => $request->get('kode_mkr'),
            ]);

            return view('layouts.dashboard.marketing.dashboardpencapaianperlevel', [
                'title_menu'    => 'Dashboard Marketing Pencapaian Per-Level',
                'data_filter'   => $data_filter->first(),
                'total'         => $data->total,
                'handle'        => $data->handle,
                'non_handle'    => $data->non_handle,
                'tube'          => $data->tube,
                'oli'           => $data->oli,
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function dashboardPencapaianGrowth(Request $request) {
        $year = date('Y');

        if(!empty($request->get('year'))) {
            $year = $request->get('year');
        }

        $responseApi = Service::DashboardMarketingGrowth($year, $request->get('level_produk'), $request->get('kode_produk'),
                            $request->get('jenis_mkr'), $request->get('kode_mkr'), strtoupper(trim($request->session()->get('app_user_role_id'))),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            $data_filter = new Collection();
            $data_filter->push((object) [
                'year'          => $year,
                'level_produk'  => $request->get('level_produk'),
                'kode_produk'   => $request->get('kode_produk'),
                'jenis_mkr'     => $request->get('jenis_mkr'),
                'kode_mkr'      => $request->get('kode_mkr'),
            ]);

            return view('layouts.dashboard.marketing.dashboardpencapaiangrowth', [
                'title_menu'    => 'Dashboard Marketing Growth Pencapaian',
                'data_filter'   => $data_filter->first(),
                'total'         => $data->total,
                'marketing'     => $data->marketing
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function dashboardPencapaianPerProduk(Request $request) {
        $year = date('Y');
        $month = date('m');

        if(!empty($request->get('year'))) {
            $year = $request->get('year');
        }

        if(!empty($request->get('month'))) {
            $month = $request->get('month');
        }

        $responseApi = Service::DashboardMarketingGroupPerProduk($year, $month, $request->get('level_produk'), $request->get('kode_produk'),
                            $request->get('jenis_mkr'), $request->get('kode_mkr'), strtoupper(trim($request->session()->get('app_user_role_id'))),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            $data_product = $data;

            $data_filter = new Collection();
            $data_filter->push((object) [
                'year'          => $year,
                'month'         => $month,
                'level_produk'  => $request->get('level_produk'),
                'kode_produk'   => $request->get('kode_produk'),
                'jenis_mkr'     => $request->get('jenis_mkr'),
                'kode_mkr'      => $request->get('kode_mkr'),
            ]);

            $jumlah_data = 0;
            foreach($data as $data) {
                $jumlah_data = (double)$jumlah_data + 1;
            }

            return view('layouts.dashboard.marketing.dashboardpencapaianperproduk', [
                'title_menu'    => 'Dashboard Marketing Pencapaian Per-Produk',
                'data_filter'   => $data_filter->first(),
                'product_count' => (double)$jumlah_data * 135,
                'product'       => $data_product
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }
}
