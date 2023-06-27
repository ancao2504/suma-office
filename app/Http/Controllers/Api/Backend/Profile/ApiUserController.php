<?php

namespace App\Http\Controllers\Api\Backend\Profile;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;


class ApiUserController extends Controller
{
    public function daftarUser(Request $request) {
        try {
            if(strtoupper(trim($request->get('role_id'))) != "MD_H3_MGMT") {
                return Response::responseWarning('Anda tidak memiliki akses untuk membuka menu ini');
            }

            $sql = DB::table('users')->lock('with (nolock)')
                    ->orderBy('user_id', 'asc');

            if(!empty($request->get('role_filter'))) {
                $sql->where('role_id', $request->get('role_filter'));
            }

            if(!empty($request->get('user_id'))) {
                $sql->where('user_id', 'like', $request->get('user_id').'%');
            }

            $sql = $sql->paginate($request->get('per_page') ?? 10);

            return Response::responseSuccess('success', $sql);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function formUser(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'user_id'   => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih data user terlebih dahulu");
            }

            $sql = DB::table('users')->lock('with (nolock)')
                    ->where('user_id', $request->get('user_id'))
                    ->first();

            if(empty($sql->user_id)) {
                return Response::responseWarning("User Id tidak terdaftar");
            } else {
                return Response::responseSuccess('success', $sql);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function simpanUser(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'user_id'   => 'required|string',
                'role_id'   => 'required|string',
                'name'      => 'required|string',
                'jabatan'   => 'required|string',
                'telepon'   => 'required|string',
                'photo'     => 'required|string',
                'email'     => 'required|string',
                'status_user' => 'required|string',
                'companyid' => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Isi data user secara lengkap");
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_Users_Simpan ?,?,?,?,?,?,?,?,?,?,?', [
                    trim(strtoupper($request->get('user_id'))), trim(strtoupper($request->get('role_id'))),
                    trim($request->get('name')), trim($request->get('jabatan')), trim($request->get('telepon')),
                    trim($request->get('photo')), trim($request->get('email')), bcrypt(trim($request->get('password'))),
                    trim($request->get('password')), trim($request->get('status_user')), trim(strtoupper($request->get('companyid')))
                ]);
            });

            return Response::responseSuccess('Data Berhasil Disimpan', null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
