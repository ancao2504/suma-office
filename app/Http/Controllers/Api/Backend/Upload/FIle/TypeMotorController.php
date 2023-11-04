<?php

namespace App\Http\Controllers\Api\Backend\Upload\File;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

class TypeMotorController extends Controller
{
    function master(){
        return 'master';
        $data = DB::table('typemotor_ms')
        ->select('kd_type', 'nama', 'jenis')
        ->orderBy('kd_type', 'asc')
        ->get();

        return Response::responseSuccess('success', $data);
    }
    function detail(){
        $data = DB::table('typemotor')
        ->select('kd_type', 'typemkt', 'ket')
        ->orderBy('typemkt', 'asc')
        ->get();

        return Response::responseSuccess('success', $data);
    }

    function store(Request $request){
        try {
            if($request->option == 'master'){
                $data = DB::table('typemotor_ms')->where('kd_type', $request->m_kd_master)->get();
                if (count($data) > 0) {
                    DB::table('typemotor_ms')
                    ->where('kd_type', $request->m_kd_master)
                    ->update(
                        [
                            'nama'      => $request->m_nama,
                            'jenis'     => $request->m_jenis,
                            'logo'      => json_encode(array_values(array_unique(array_merge(json_decode($data[0]->gambar), $request->m_logo_path)))),
                            'gambar'    => json_encode(array_values(array_unique(array_merge(json_decode($data[0]->gambar), $request->m_gambar_path))))
                        ]
                    );
                } else {
                    DB::table('typemotor_ms')
                    ->insert(
                        [
                            'kd_type'   => $request->m_kd_master,
                            'nama'      => $request->m_nama,
                            'jenis'     => $request->m_jenis,
                            'logo'      => json_encode($request->m_logo_path),
                            'gambar'    => json_encode($request->m_gambar_path)
                        ]
                    );
                }
            }elseif ($request->option == 'detail') {
                DB::table('typemotor')
                ->whereIn('typemkt', $request->d_kd_detail)
                ->update(
                    [
                        'kd_type'   => $request->d_kd_master
                    ]
                );
            } elseif ($request->option == 'detail_image') {
                $data = DB::table('typemotor_img')->where('typemkt', $request->di_kd_detail)->get();
                if(count($data) > 0){
                    DB::table('typemotor_img')
                    ->where('typemkt', $request->di_kd_detail)
                    ->update(
                        [
                            'gambar'    => json_encode(array_values(array_unique(array_merge(json_decode($data[0]->gambar), $request->di_gambar_path))))
                        ]
                    );
                }else{
                    DB::table('typemotor_img')
                    ->insert(
                        [
                            'typemkt'   => $request->di_kd_detail,
                            'gambar'    => json_encode($request->di_gambar_path)
                        ]
                    );
                }
            }

            return Response::responseSuccess('success', '');
        } catch (\Exception $exception) {
            return Response::responseError(
                $request->get('user_id'),
                'API',
                route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $exception->getMessage(),
                $request->get('companyid')??null,
            );
        }
    }
}
