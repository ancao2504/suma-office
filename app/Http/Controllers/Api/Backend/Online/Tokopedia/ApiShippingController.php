<?php

namespace App\Http\Controllers\Api\Backend\Online\Tokopedia;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use App\Helpers\Api\UpdateToken;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Helpers\Api\ServiceTokopedia;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class ApiShippingController extends Controller
{
    public static function prosesPickup(Request $request) {
        $validate = Validator::make($request->all(), [
            'nomor_invoice' => 'required',
            'companyid'     => 'required',
        ]);

        if($validate->fails()) {
            return Response::responseWarning("Pilih data faktur atau invoice terlebih dahulu");
        }

        $sql = DB::table('faktur')->lock('with (nolock)')
                ->selectRaw("isnull(faktur.no_faktur, '') as nomor_faktur")
                ->where('faktur.ket', $request->get('nomor_invoice'))
                ->where('faktur.companyid', $request->get('companyid'))
                ->first();

        if(empty($sql->nomor_faktur)) {
            return Response::responseWarning('Nomor invoice tidak terdaftar di data faktur internal');
        }

        $authorization = $request->header('Authorization');
        $token = explode(" ", $authorization);
        $auth_token = trim($token[1]);

        $sql = DB::table('user_api_office')->lock('with (nolock)')
            ->selectRaw("isnull(user_api_office.tokopedia_token, '') as tokopedia_token,
                        isnull(user_api_office.user_id, '') as user_id")
            ->where('user_api_office.office_token', $auth_token)
            ->orderByRaw("isnull(user_api_office.id, 0) desc")
            ->first();

        if(empty($sql->tokopedia_token) || trim($sql->tokopedia_token) == '') {
            return Response::responseWarning('Token tokopedia tidak ditemukan, lakukan logout kemudian login kembali');
        } else {
            $token_tokopedia = $sql->tokopedia_token;
        }

        // ==========================================================================
        // CEK KONEKSI API TOKOPEDIA
        // ==========================================================================
        $responseTokopedia = ServiceTokopedia::GetShopInfo(trim($token_tokopedia));
        $statusServer = (empty(json_decode($responseTokopedia)->message)) ? 1 : 0;

        if($statusServer == 0) {
            $responseUpdateToken = UpdateToken::tokopedia($auth_token);

            if($responseUpdateToken->status == 1) {
                $token_tokopedia = $responseUpdateToken->data->token;
            } else {
                return Response::responseWarning($responseUpdateToken->message);
            }
        }

        $responseTokopedia = ServiceTokopedia::OrderGetSingleOrder(trim($token_tokopedia), trim($request->get('nomor_invoice')));
        $statusResponseTokopedia = (empty(json_decode($responseTokopedia)->header->error_code)) ? 1 : 0;

        if($statusResponseTokopedia == 1) {
            $dataTokopedia = json_decode($responseTokopedia)->data;

            $order_id = $dataTokopedia->order_id;
            $data_req_pickup = [
                'order_id'  => (int)$order_id,
                'shop_id'   => (int)config('constants.api.tokopedia.shop_id')
            ];

            $responseTokopedia = ServiceTokopedia::OrderRequestPickup(trim($token_tokopedia), $data_req_pickup);
            $statusResponseTokopedia = (empty(json_decode($responseTokopedia)->header->error_code)) ? 1 : 0;

            if($statusResponseTokopedia == 1) {
                DB::transaction(function () use ($request) {
                    DB::insert('exec SP_Faktur_ReqPickup ?,?', [
                        strtoupper(trim($request->get('nomor_invoice'))), strtoupper(trim($request->get('companyid'))),
                    ]);
                });
                return Response::responseSuccess('Data Berhasil Disimpan dan me-request pickup Tokopedia');
            } else {
                return Response::responseWarning('Tokopedia API : '.json_decode($responseTokopedia)->header->reason);
            }
        } else {
            return Response::responseWarning('Gagal mengambil data order id Tokopedia, coba lagi. '.json_decode($responseTokopedia)->header->reason);
        }
    }

    public function prosesCetakLabel(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_invoice' => 'required',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih nomor invoice terlebih dahulu");
            }

            $authorization = $request->header('Authorization');
            $token = explode(" ", $authorization);
            $auth_token = trim($token[1]);

            $token_tokopedia = '';

            $sql = DB::table('user_api_office')->lock('with (nolock)')
                    ->selectRaw("isnull(user_api_office.tokopedia_token, '') as tokopedia_token,
                                isnull(user_api_office.user_id, '') as user_id")
                    ->where('user_api_office.office_token', $auth_token)
                    ->orderByRaw("isnull(user_api_office.id, 0) desc")
                    ->first();

            if(empty($sql->tokopedia_token) || trim($sql->tokopedia_token) == '') {
                return Response::responseWarning('Token tokopedia tidak ditemukan, lakukan logout kemudian login kembali');
            } else {
                $token_tokopedia = $sql->tokopedia_token;
            }

            // ==========================================================================
            // CEK KONEKSI API TOKOPEDIA
            // ==========================================================================
            $responseTokopedia = ServiceTokopedia::GetShopInfo(trim($token_tokopedia));
            $statusServer = (empty(json_decode($responseTokopedia)->message)) ? 1 : 0;

            if($statusServer == 0) {
                $authorization = $request->header('Authorization');
                $token = explode(" ", $authorization);
                $auth_token = trim($token[1]);

                $responseUpdateToken = UpdateToken::tokopedia($auth_token);

                if($responseUpdateToken->status == 1) {
                    $token_tokopedia = $responseUpdateToken->data->token;
                } else {
                    return Response::responseWarning($responseUpdateToken->message);
                }
            }

            $responseTokopedia = ServiceTokopedia::OrderGetSingleOrder(trim($token_tokopedia), trim($request->get('nomor_invoice')));
            $statusResponseTokopedia = (empty(json_decode($responseTokopedia)->header->error_code)) ? 1 : 0;

            if($statusResponseTokopedia == 1) {
                $dataTokopedia = json_decode($responseTokopedia)->data;

                $order_id = $dataTokopedia->order_id;

                $responseTokopedia = ServiceTokopedia::OrderGetShippingLabel(trim($token_tokopedia), (int)$order_id);
                $statusResponseTokopedia = (empty(json_decode($responseTokopedia)->header->error_code)) ? 1 : 0;

                if($statusResponseTokopedia == 1) {
                    return Response::responseSuccess('success', $responseTokopedia);
                } else {
                    return Response::responseWarning('Gagal memproses cetak label tokopedia, coba lagi');
                }
            } else {
                return Response::responseWarning('Gagal mengambil data order id Tokopedia, coba lagi. '.json_decode($responseTokopedia)->header->reason);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
