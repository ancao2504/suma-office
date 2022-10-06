<?php

namespace App\Http\Controllers\App\Visit;

use App\Http\Controllers\Controller;
use App\Helpers\ApiService;

use Illuminate\Http\Request;
use Jenssegers\Agent\Agent as Agent;

class PlanningVisitController extends Controller
{
    public function index(Request $request) {
        $tanggal = date('Y-m-d');

        if(!empty($request->get('date'))) {
            $tanggal = $request->get('date');
        }

        $responseApi = ApiService::PlanningVisitDaftar($tanggal, $request->get('kode_sales'),
                            strtoupper(trim($request->session()->get('app_user_id'))),
                            strtoupper(trim($request->session()->get('app_user_role_id'))),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            $data_planning = $data->data;

            $Agent = new Agent();
            $device = 'Desktop';
            if ($Agent->isMobile()) {
                $device = 'Mobile';
            }

            if($request->ajax()) {
                $view = view('layouts.visit.planningvisit.planningvisitlist', compact('data_planning'))->render();

                return response()->json([ 'html' => $view ]);
            }
            return view('layouts.visit.planningvisit.planningvisit', [
                'title_menu'    => 'Planning Visit',
                'device'        => $device,
                'role_id'       => trim($request->session()->get('app_user_role_id')),
                'date'          => $tanggal,
                'plan_visit'    => $data_planning,
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function savePlanningVisit(Request $request) {
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
            return redirect()->route('visit.planning-visit')->withInput()->with('success', $messageApi);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function deletePlanningVisit(Request $request) {
        $responseApi = ApiService::PlanningVisitHapus($request->get('kode_visit'),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        return json_decode($responseApi, true);
    }
}
