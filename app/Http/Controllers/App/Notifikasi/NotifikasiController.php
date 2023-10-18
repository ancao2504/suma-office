<?php

namespace app\Http\Controllers\App\Notifikasi;

use App\Helpers\App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotifikasiController extends Controller
{
    function form(Request $request){
        // $request->merge(['option' => 'select']);
        $ResponApiUser = json_decode(Service::optionUser($request));
        $ResponApiRoleUser = json_decode(Service::optionRoleUser($request));
        // $ResponApiSales = json_decode(Service::dataSalesman($request));
        // $ResponApiDealer = json_decode(Service::dataDealer($request));
        if((empty($ResponApiRoleUser) || $ResponApiRoleUser->status == 0) || (empty($ResponApiUser) || $ResponApiUser->status == 0)){
            return redirect()->route('home.index')->with('failed', 'Maaf, terjadi kesalahan. Silahkan coba lagi');
        }

        $ResponApiSales = collect($ResponApiUser->data)->where('role_id', 'MD_H3_SM')->values()->all();
        $ResponApiDealer = collect($ResponApiUser->data)->where('role_id', 'D_H3')->values()->all();

        return view('layouts.notifikasi.form',[
                'title_menu'    => 'Notifikasi',
                'data'          => (object)[
                    'salesman'  => $ResponApiSales,
                    'dealer'    => $ResponApiDealer,
                    'role_id' => $ResponApiRoleUser->data,
                    'user' => $ResponApiUser->data
                ]
            ]);
    }

    function send(Request $request){

        // $ResponApi = json_decode(Service::sendNotifikasi($request));
        // if($ResponApi->status == 1){
        //     return redirect()->route('notifikasi.index')->with('success', 'Notifikasi berhasil dikirim');
        // }else{
        //     return redirect()->route('notifikasi.index')->with('failed', 'Notifikasi gagal dikirim');
        // }
    }
}
