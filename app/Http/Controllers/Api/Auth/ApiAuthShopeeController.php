<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use App\Helpers\Api\ServiceShopee;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class ApiAuthShopeeController extends Controller
{
    public function dataAuthShopee(Request $request) {
        try {
            $sql = DB::table('user_shopee_tokens')->lock('with (nolock)')
                    ->orderByRaw("isnull(user_shopee_tokens.id, 0) desc")
                    ->first();

            if(empty($sql->code)) {
                return Response::responseSuccess('access_code_not_found');
            } else {
                return Response::responseSuccess('success', $sql);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function generateAuthorizationToken() {
        $responseApi = ServiceShopee::AuthPartner();

        return Response::responseSuccess('success', [ 'url' => $responseApi ]);
    }

    public function simpanAccessToken(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'access_code'   => 'required',
                'user_id'       => 'required',
                'companyid'     => 'required',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Isi access code shopee terlebih dahulu");
            }

            $responseApi = ServiceShopee::getAccessToken($request->get('access_code'));
            $statusApi = (empty(json_decode($responseApi)->error)) ? 1 : 0;
            $messageApi = json_decode($responseApi)->message;

            if($statusApi == 1) {
                $dataApi = json_decode($responseApi);


                DB::transaction(function () use ($dataApi, $request) {
                    $authorization = $request->header('Authorization');
                    $token = explode(" ", $authorization);
                    $auth_token = trim($token[1]);

                    DB::insert('exec SP_UserApiShopee_SimpanToken ?,?,?,?,?,?,?', [
                        $auth_token, $dataApi->refresh_token, $dataApi->access_token,
                        time() + $dataApi->expire_in, trim($request->get('access_code')),
                        strtoupper(trim($request->get('user_id'))),
                        strtoupper(trim($request->get('companyid')))
                    ]);
                });

                return Response::responseSuccess('Data Authorization Shopee Berhasil Disimpan', $dataApi);
            } else {
                return Response::responseWarning('Shopee API : '.$messageApi);
            }

        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }


}
