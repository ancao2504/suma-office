<?php

namespace App\Http\Controllers\Api\Auth;


use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use App\Helpers\Api\UpdateToken;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;

class ApiAuthController extends Controller
{
    public function registerToken(Request $request)
    {
		try {
            $ip_address = $request->ip();
            $token = base64_encode(sha1($request->email . time() . $request->password));
            $expired_at = time() + 24 * 60 * 60;

            DB::transaction(function () use ($ip_address, $token, $expired_at) {
                DB::insert('insert into user_api_office (ip_address, office_token, office_expired) values (?,?,?)',
                    [ $ip_address, $token, $expired_at ]);
            });

            session()->put('Authorization', $token);

            $sql = DB::table('user_api_office')
                    ->where('office_token', $token)
                    ->first();

            return Response::responseSuccess('success', $sql);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('email'), 'API', Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(), $exception->getMessage(), 'XXX');
        }
    }

    public function login(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'email'     => 'required|string',
                'password'  => 'required|string',
                'token'     => 'required|string'
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Divisi, Email dan password tidak boleh kosong");
            }

            $sql = DB::table('users')->lock('with (nolock)')
                // ->joinSub(function ($query) {
                //     $query->select('*')
                //         ->from('kode_company');
                // }, 'kode_company', function ($join) {
                //     $join->on('users.companyid', '=', 'kode_company.kd_honda')
                //         ->orOn('users.companyid', '=', 'kode_company.kd_fdr');
                // })
                ->orWhere('email', $request->get('email'))
                ->orWhere('user_id', $request->get('email'))
                ->first();

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember_me')) || Auth::attempt(['user_id' => $request->email, 'password' => $request->password], $request->get('remember_me'))) {
                if ((Hash::check(trim($request->get('password')), $sql->password, [])) == true) {
                    DB::transaction(function () use ($request, $sql) {
                        DB::update('exec SP_UserApioffice_Simpan ?,?,?,?,?,?', [
                            $request->get('token'), strtoupper(trim($sql->user_id)), trim($sql->email),
                            (empty($request->get('user_agent'))) ? '' : $request->get('user_agent'),
                            (empty($request->get('ip_address'))) ? '' : $request->get('ip_address'),
                            strtoupper(trim($sql->companyid))
                        ]);
                    });

                    $status_tokopedia = '';
                    $status_shopee = '';

                    if(strtoupper(trim($sql->role_id)) == 'MD_H3_MGMT' || strtoupper(trim($sql->role_id)) == 'MD_H3_FIN' ||
                        Str::contains(strtoupper(trim($sql->role_id)), 'OL')) {
                        // ====================================================================================
                        // Access Token Tokopedia
                        // ====================================================================================
                        $responseTokopedia = UpdateToken::tokopedia($request->get('token'));
                        if($responseTokopedia->status == 0) {
                            $status_tokopedia = $responseTokopedia->message;
                        }

                        // ====================================================================================
                        // Access Token Shopee
                        // ====================================================================================
                        $responseShopee = UpdateToken::shopee($request->get('token'));
                        if($responseShopee->status == 0) {
                            $status_shopee = $responseShopee->message;
                        }
                    }


                    $data_user = new Collection();
                    $data_user->push((object) [
                        'user_id'   => strtoupper(trim($sql->user_id)),
                        'role_id'   => strtoupper(trim($sql->role_id)),
                        'name'      => trim($sql->name),
                        'jabatan'   => trim($sql->jabatan),
                        'phone'     => trim($sql->telepon),
                        'email'     => trim($sql->email),
                        'password'  => trim($sql->password),
                        'photo'     => trim($sql->photo),
                        'tokopedia' => trim($status_tokopedia),
                        'shopee'    => trim($status_shopee),
                        'companyid' => strtoupper(trim($sql->companyid)),
                    ]);
                    return Response::responseSuccess("success", $data_user->first());
                } else {
                    return Response::responseWarning("Kombinasi email dan password tidak sesuai");
                }
            } else {
                return Response::responseWarning("Kombinasi email dan password tidak sesuai");
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('email'), 'API', Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(), $exception->getMessage(), 'XXX');
        }
    }

    public function logout(Request $request)
    {
    }
}
