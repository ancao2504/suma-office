<?php

namespace App\Http\Controllers\App\Dashboard\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardManagementController extends Controller
{
    public function index(Request $request) {
        return redirect()->route('dashboard.dashboard-management-sales');
    }
}
