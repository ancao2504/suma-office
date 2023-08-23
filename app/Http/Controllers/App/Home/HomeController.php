<?php

namespace App\Http\Controllers\App\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index(Request $request) {
        if(Str::contains(strtoupper(trim($request->session()->get('app_user_role_id'))), 'OL')) {
            if(in_array($request->session()->get('app_user_role_id'), ['MD_OL_ADMIN','MD_OL_SPV'])){
                return view('layouts.home.homeonline', [
                    'title_menu'    => 'Home',
                ]);
            } elseif (in_array($request->session()->get('app_user_role_id'), ['MD_WH_OL'])){
                return view('layouts.home.homewhonline', [
                    'title_menu'    => 'Home',
                ]);
            }
        } else {
            if(Str::contains(strtoupper(trim($request->session()->get('app_user_role_id'))), 'REQ')) {
                return view('layouts.home.homeonline', [
                    'title_menu'    => 'Home',
                ]);
            } else {
                return view('layouts.home.home', [
                    'title_menu'    => 'Home',
                ]);
            }
        }
    }
}
