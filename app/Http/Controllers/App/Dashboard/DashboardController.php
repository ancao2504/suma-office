<?php

namespace App\Http\Controllers\App\Dashboard;

use App\Http\Controllers\App\Dashboard\DashboardSalesmanController;
use App\Http\Controllers\Controller;
use App\Helpers\ApiService;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Cast\Double;

class DashboardController extends Controller
{
    public function index(Request $request) {
        // if(strtoupper(trim($request->session()->get('app_user_role_id'))) == 'D_H3') {
        //     return redirect()->route('dashboard.dashboard-dealer');
        // } elseif(strtoupper(trim($request->session()->get('app_user_role_id'))) == 'MD_H3_SM') {
        //     return redirect()->route('dashboard.dashboard-salesman');
        // } elseif(strtoupper(trim($request->session()->get('app_user_role_id'))) == 'MD_H3_KORSM') {
        //     return redirect()->route('dashboard.dashboard-salesman');
        // } elseif(strtoupper(trim($request->session()->get('app_user_role_id'))) == 'MD_H3_MGMT') {
        //     return redirect()->route('dashboard.dashboard-management');
        // } else {
        //     return redirect()->route('dashboard.dashboard-profile');
        // }
        return redirect()->route('home.index');
    }
}
