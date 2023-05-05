<?php

namespace App\Http\Controllers\App\Dashboard\Management;

use App\Http\Controllers\Controller;
use App\Helpers\ApiService;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardManagementKuartalController extends Controller
{
    public function index(Request $request) {
        $year = (empty($request->get('year'))) ? date('Y') : $request->get('year');
        $fields = (empty($request->get('fields'))) ? 'SELLING_PRICE' : $request->get('fields');
        $option_company = (empty($request->get('option_company'))) ? 'COMPANY_TERTENTU' : $request->get('option_company');
        $companyid = '';

        if(strtoupper(trim($option_company)) == 'COMPANY_TERTENTU') {
            $companyid = (empty($request->get('companyid'))) ? strtoupper(trim($request->session()->get('app_user_company_id'))) : $request->get('companyid');
        }

        if(strtoupper(trim($request->session()->get('app_user_role_id'))) != 'MD_H3_MGMT') {
            return redirect()->back()->withInput()->with('failed', 'Anda tidak dapat mengakses halaman ini');
        }

        $responseApi = ApiService::DashboardManagementKuartal($year, strtoupper(trim($fields)), strtoupper(trim($option_company)),
                            strtoupper(trim($companyid)), strtoupper(trim($request->get('kabupaten'))),
                            strtoupper(trim($request->get('supervisor'))), strtoupper(trim($request->get('salesman'))),
                            strtoupper(trim($request->get('produk'))), strtoupper(trim($request->session()->get('app_user_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;

            $data_default = new Collection();
            $data_default->push((object) [
                'companyid' => strtoupper(trim($request->session()->get('app_user_company_id'))),
                'role_id'   => strtoupper(trim($request->session()->get('app_user_role_id'))),
                'user_id'   => strtoupper(trim($request->session()->get('app_user_id'))),
            ]);

            $data_filter = new Collection();
            $data_filter->push((object) [
                'year'              => $year,
                'option_company'    => $option_company,
                'companyid'         => $companyid,
                'fields'            => $fields,
                'kabupaten'         => $request->get('kabupaten'),
                'supervisor'        => $request->get('supervisor'),
                'salesman'          => $request->get('salesman'),
                'produk'            => $request->get('produk'),
            ]);

            $data_year_to_date = [];
            foreach($dataApi->year_to_date as $data_ytd) {
                $data_year_to_date[] = [
                    'keterangan'        => trim($data_ytd->month),
                    'selected'          => (double)$data_ytd->selected,
                    'previous'          => (double)$data_ytd->previous,
                    'growth'            => (double)$data_ytd->growth
                ];
            }

            $data_semester = [];
            foreach($dataApi->semester as $data_detail_semester) {
                $data_semester[] = [
                    'keterangan'    => trim($data_detail_semester->keterangan),
                    'selected'      => (double)$data_detail_semester->selected->value,
                    'previous'      => (double)$data_detail_semester->previous->value,
                    'selected_kontribusi' => (double)$data_detail_semester->selected->kontribusi,
                    'previous_kontribusi' => (double)$data_detail_semester->previous->kontribusi,
                ];
            }

            $data_summary_quarter = [];
            foreach($dataApi->quarter->summary as $data_sum_quarter) {
                $data_summary_quarter[] = [
                    'keterangan'        => trim($data_sum_quarter->keterangan),
                    'selected'          => (double)$data_sum_quarter->selected,
                    'previous'          => (double)$data_sum_quarter->previous,
                    'growth'            => (double)$data_sum_quarter->growth
                ];
            }

            $data_detail_quarter = [];
            foreach($dataApi->quarter->quarter as $data_quarter) {
                $data_detail_quarter[] = [
                    'keterangan'        => trim($data_quarter->keterangan),
                    'selected'          => (double)$data_quarter->selected,
                    'previous'          => (double)$data_quarter->previous,
                    'growth'            => (double)$data_quarter->growth,
                    'selected_kontribusi' => (double)$data_quarter->kontribusi->selected,
                    'previous_kontribusi' => (double)$data_quarter->kontribusi->previous,
                ];
            }

            $data_detail_per_bulan = [];
            foreach($dataApi->detail as $data_per_bulan) {
                $data_detail_per_bulan[] = [
                    'month'             => trim($data_per_bulan->month),
                    'selected'          => (double)$data_per_bulan->selected,
                    'previous'          => (double)$data_per_bulan->previous,
                    'growth'            => (double)$data_per_bulan->growth,
                    'selected_kontribusi' => (double)$data_per_bulan->kontribusi->selected,
                    'previous_kontribusi' => (double)$data_per_bulan->kontribusi->previous,
                ];
            }

            return view('layouts.dashboard.management.dashboardmanagementkuartal', [
                'title_menu'            => 'Dashboard Management Kuartal',
                'data_default'          => $data_default->first(),
                'data_filter'           => $data_filter->first(),
                'data'                  => $dataApi,
                'data_year_to_date'     => $data_year_to_date,
                'data_semester'         => $data_semester,
                'data_summary_quarter'  => $data_summary_quarter,
                'data_detail_quarter'   => $data_detail_quarter,
                'data_detail_per_bulan' => $data_detail_per_bulan,
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }
}
