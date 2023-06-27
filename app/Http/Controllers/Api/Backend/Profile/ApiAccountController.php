<?php

namespace App\Http\Controllers\Api\Backend\Profile;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;


class ApiAccountController extends Controller
{
    public function dataAccount(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'user_id'       => 'required|string',
                'companyid'     => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Anda Belum Login");
            }

            $sql = DB::table('users')->lock('with (nolock)')
                    ->selectRaw("isnull(users.user_id, '') as user_id, isnull(users.name, '') as name,
                                isnull(users.telepon, '') as telepon, isnull(users.email, '') as email,
                                isnull(users.photo, '') as photo")
                    ->where('user_id', strtoupper(trim($request->get('user_id'))))
                    ->where('companyid', strtoupper(trim($request->get('companyid'))))
                    ->first();

            if(empty($sql->user_id)) {
                return Response::responseWarning("User id tidak ditemukan");
            } else {
                return Response::responseSuccess("success", $sql);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function simpanAccount(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'user_id'       => 'required|string',
                'name'          => 'required|string',
                'email'         => 'required|string',
                'telepon'       => 'required|string',
                'companyid'     => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Isi data identitas anda secara lengkap");
            }

            $photo = '';
            if(empty($request->get('photo'))) {
                $sql = DB::table('users')->lock('with (nolock)')
                    ->selectRaw("isnull(users.user_id, '') as user_id, isnull(users.name, '') as name,
                                isnull(users.telepon, '') as telepon, isnull(users.email, '') as email,
                                isnull(users.photo, '') as photo")
                    ->where('user_id', strtoupper(trim($request->get('user_id'))))
                    ->where('companyid', strtoupper(trim($request->get('companyid'))))
                    ->first();

                $photo = $sql->photo;
            } else {
                $photo = $request->get('photo');
            }

            DB::transaction(function () use ($request, $photo) {
                DB::insert('exec SP_AccountPMO_Simpan ?,?,?,?,?,?', [
                    trim(strtoupper($request->get('user_id'))), $request->get('name'),
                    $request->get('telepon'), $request->get('email'), $photo,
                    $request->get('companyid')
                ]);
            });

            return Response::responseSuccess("Data Profile Anda Berhasil Disimpan", null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function simpanChangePassword(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'user_id'       => 'required|string',
                'email'         => 'required|string',
                'old_password'  => 'required|string',
                'new_password'  => 'required|string',
                'companyid'     => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Isi data password anda");
            }

            $sql = DB::table('users')->lock('with (nolock)')
                    ->selectRaw("isnull(users.user_id, '') as user_id,
                            isnull(users.password, '') as password")
                    ->where('user_id', $request->get('user_id'))
                    ->where('email', $request->get('email'))
                    ->where('companyid', $request->get('companyid'))
                    ->first();

            $old_password = '';
            if(empty($sql->user_id)) {
                return Response::responseWarning("Data users anda tidak ditemukan");
            } else {
                $old_password = $sql->password;
            }

            if(Hash::check($request->get('old_password'), $old_password)) {
                DB::transaction(function () use ($request) {
                    DB::insert('exec SP_AccountPMO_ChangePassword ?,?,?,?', [
                        trim(strtoupper($request->get('user_id'))), $request->get('email'),
                        bcrypt($request->get('new_password')), $request->get('companyid')
                    ]);
                });
                return Response::responseSuccess('Data Password Berhasil Diubah');
            } else {
                return Response::responseWarning("Password lama yang anda entry salah");
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
