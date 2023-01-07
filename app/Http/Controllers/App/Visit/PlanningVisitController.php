<?php

namespace App\Http\Controllers\App\Visit;

use App\Http\Controllers\Controller;
use App\Helpers\ApiService;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Jenssegers\Agent\Agent as Agent;

class PlanningVisitController extends Controller
{
    public function daftarPlanningVisit(Request $request) {
        $Agent = new Agent();
        $device = 'Desktop';
        if($Agent->isMobile()) {
            $device = 'Mobile';
        }

        $kode_sales = '';
        $kode_dealer = '';
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

        if(!empty($request->get('year'))) {
            $year = $request->get('year');
        }
        if(!empty($request->get('month'))) {
            $month = $request->get('month');
        }

        if(strtoupper(trim($request->session()->get('app_user_role_id'))) == "MD_H3_SM") {
            $kode_sales = trim($request->session()->get('app_user_id'));
        } else {
            $kode_sales = $request->get('salesman');
        }

        $responseApi = ApiService::PlanningVisitDaftar($request->get('page'), $per_page,
                            $year, $month, $kode_sales, $request->get('dealer'),
                            strtoupper(trim($request->session()->get('app_user_id'))),
                            strtoupper(trim($request->session()->get('app_user_role_id'))),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            $data_planning = $data->data;

            $data_page = new Collection();
            $data_page->push((object) [
                'from'          => $data->from,
                'to'            => $data->to,
                'total'         => $data->total,
                'current_page'  => $data->current_page,
                'per_page'      => $data->per_page,
                'links'         => $data->links
            ]);

            $data_filter = new Collection();
            $data_filter->push((object) [
                'year'          => $year,
                'month'         => $month,
                'kode_sales'    => $kode_sales,
                'kode_dealer'   => $kode_dealer
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

            return view('layouts.visit.planningvisit.planningvisit', [
                'title_menu'    => 'Planning Visit',
                'data_device'   => $data_device->first(),
                'data_user'     => $data_user->first(),
                'data_filter'   => $data_filter->first(),
                'data_page'     => $data_page->first(),
                'data_planning_visit' => $data_planning,
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function simpanPlanningVisit(Request $request) {
        $responseApi = ApiService::PlanningVisitSimpan($request->get('tanggal'),
                            strtoupper(trim($request->get('salesman'))),
                            strtoupper(trim($request->get('dealer'))),
                            trim($request->get('keterangan')),
                            strtoupper(trim($request->session()->get('app_user_id'))),
                            strtoupper(trim($request->session()->get('app_user_role_id'))),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            return redirect()->route('visit.planning.daftar')->withInput()->with('success', $messageApi);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function hapusPlanningVisit(Request $request) {
        $responseApi = ApiService::PlanningVisitHapus($request->get('kode_visit'),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        return json_decode($responseApi, true);
    }
}
