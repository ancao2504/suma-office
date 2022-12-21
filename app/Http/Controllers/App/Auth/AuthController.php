<?php

namespace App\Http\Controllers\App\Auth;

use App\Helpers\ApiService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller {

    public function index() {
        return view('layouts.auth.login');
    }

    public function login(Request $request) {
        $responseApi = ApiService::AuthLogin($request->get('email'), $request->get('password'), $request->get('remember_me'));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            $request->session()->flush();

            session()->put('authenticated', trim($data->companyid).trim($data->user_id).trim($data->email));
            session()->put('app_user_id', trim($data->user_id));
            session()->put('app_user_name', trim($data->name));
            session()->put('app_user_jabatan', trim($data->jabatan));
            session()->put('app_user_role_id', trim($data->role_id));
            session()->put('app_user_telepon', trim($data->phone));
            session()->put('app_user_email', trim($data->email));
            session()->put('app_user_photo', trim($data->photo));
            session()->put('app_user_company_id', trim($data->companyid));

            return redirect()->route('home.index');
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function logout(Request $request) {
        $request->session()->flush();

        Auth::logout();
        return redirect()->route('auth.index');
    }

    public function disableaccess() {
        return view('layouts.access.disableaccess', [ 'title_menu' => 'Access Disable' ]);
    }
}
