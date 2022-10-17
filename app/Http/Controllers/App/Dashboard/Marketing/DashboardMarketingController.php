<?php

namespace App\Http\Controllers\App\Dashboard\Marketing;

use App\Helpers\ApiRequest;
use App\Http\Controllers\App\Dashboard\DashboardSalesmanController;
use App\Http\Controllers\Controller;
use App\Helpers\ApiService;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Cast\Double;
use PhpParser\Node\Stmt\Return_;

class DashboardMarketingController extends Controller
{
    public function dashboardPencapaianPerLevel(Request $request) {
        $year = date('Y');

        if(!empty($request->get('year'))) {
            $year = $request->get('year');
        }

        $responseApi = ApiService::DashboardMarketingGroupPerLevel($year, $request->get('jenis_mkr'), $request->get('kode_mkr'),
                            strtoupper(trim($request->session()->get('app_user_role_id'))), strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            return view('layouts.dashboard.marketing.dashboardpencapaianperlevel', [
                'title_menu'    => 'Dashboard Marketing Pencapaian Per-Level',
                'year'          => $year,
                'jenis_mkr'     => $request->get('jenis_mkr'),
                'kode_mkr'      => $request->get('kode_mkr'),
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

        $responseApi = ApiService::DashboardMarketingGrowth($year, $request->get('level_produk'), $request->get('kode_produk'),
                            $request->get('jenis_mkr'), $request->get('kode_mkr'), strtoupper(trim($request->session()->get('app_user_role_id'))),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            return view('layouts.dashboard.marketing.dashboardpencapaiangrowth', [
                'title_menu'    => 'Dashboard Marketing Growth Pencapaian',
                'year'          => $year,
                'level_produk'  => $request->get('level_produk'),
                'kode_produk'   => $request->get('kode_produk'),
                'jenis_mkr'     => $request->get('jenis_mkr'),
                'kode_mkr'      => $request->get('kode_mkr'),
                'total'         => $data->total,
                'marketing'     => $data->marketing
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }

    }
}
