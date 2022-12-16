<?php

namespace App\Http\Controllers\App\Home;

use App\Http\Controllers\Controller;
use App\Helpers\ApiService;

use Illuminate\Http\Request;
use Jenssegers\Agent\Agent as Agent;

class HomeController extends Controller
{
    public function index() {
        return view('layouts.home.home', [
            'title_menu'    => 'Home',
        ]);
    }
}
