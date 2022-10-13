<?php

namespace App\Http\Controllers\App\Dashboard\Management;

use App\Helpers\ApiRequest;
use App\Http\Controllers\App\Dashboard\DashboardSalesmanController;
use App\Http\Controllers\Controller;
use App\Helpers\ApiService;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Cast\Double;

class DashboardManagementController extends Controller
{
    public function index(Request $request) {
        return redirect()->route('dashboard.dashboard-management-sales');
    }
}
