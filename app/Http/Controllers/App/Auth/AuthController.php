<?php

namespace app\Http\Controllers\App\Auth;

use App\Helpers\App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent as Agent;
use Illuminate\Support\Facades\Cookie;


class AuthController extends Controller {

    public function index() {
        return view('layouts.auth.login');
    }

    public function login(Request $request) {
        $agent = new Agent();
        $user_agent = $agent->browser();

        $responseApi = Service::OauthToken($request);
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $request->session()->flush();

            $dataApi = json_decode($responseApi)->data;
            $dataToken = $dataApi->office_token;

            $token = 'Bearer '.$dataApi->office_token;
            session()->put('Authorization', $token);

            $responseApi = Service::AuthLogin($request->get('email'), $request->get('password'), $request->get('remember_me'),
                                $user_agent, request()->ip(), $dataToken);
            $statusApi = json_decode($responseApi)->status;
            $messageApi =  json_decode($responseApi)->message;

            if ($statusApi == 1) {
                $data = json_decode($responseApi)->data;

                Cookie::queue(Cookie::forget('email'));
                Cookie::queue(Cookie::forget('password'));
                Cookie::queue(Cookie::forget('remember_me'));

                if($request->has('remember_me')) {
                    Cookie::queue('email', trim($request->get('email')), 1440);
                    Cookie::queue('password', trim($request->get('password')), 1440);
                    Cookie::queue('remember_me', trim($request->get('remember_me')), 1440);
                }

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
