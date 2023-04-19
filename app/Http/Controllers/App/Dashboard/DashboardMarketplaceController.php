<?php

namespace App\Http\Controllers\App\Dashboard;

use App\Http\Controllers\Controller;
use App\Helpers\ApiService;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Jenssegers\Agent\Agent as Agent;

class DashboardMarketplaceController extends Controller
{
    public function dashboardMarketplace(Request $request) {
        $year = date('Y');
        $month = date('m');

        if(!empty($request->get('year'))) {
            $year = $request->get('year');
        }
        if(!empty($request->get('month'))) {
            $month = $request->get('month');
        }

        $data_filter = new Collection();
        $data_filter->push((object) [
            'year'      => $year,
            'month'     => $month,
        ]);

        $responseApi = ApiService::DashboardMarketplaceSalesByDate($year, $month,
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data_sales_by_date = json_decode($responseApi)->data;

            $responseApi = ApiService::DashboardMarketplaceSalesByLocation($year, $month,
                        strtoupper(trim($request->session()->get('app_user_company_id'))));
            $statusApi = json_decode($responseApi)->status;
            $messageApi =  json_decode($responseApi)->message;

            if($statusApi == 1) {
                $data_sales_by_amount = json_decode($responseApi)->data;

                $data_sales_by_location = [];

                foreach($data_sales_by_amount as $data) {
                    $data_sales_by_location[] = [
                        'kode_lokasi'   => $data->kode_lokasi,
                        'total'         => (double)$data->bulan_dipilih->total
                    ];
                }

                return view('layouts.dashboard.dashboardmarketplace', [
                    'title_menu'            => 'Dashboard Marketplace',
                    'data_filter'           => $data_filter->first(),
                    'data_sales_by_location'=> $data_sales_by_location,
                    'data_sales_by_amount'  => $data_sales_by_amount,
                    'data_sales_by_date'    => $data_sales_by_date,
                ]);
            } else {
                return redirect()->back()->withInput()->with('failed', $messageApi);
            }
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }
}
