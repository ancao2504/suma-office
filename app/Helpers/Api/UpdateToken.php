<?php

namespace App\Helpers\Api;

use App\Helpers\Api\ServiceShopee;
use App\Helpers\Api\ServiceTokopedia;
use Illuminate\Support\Facades\DB;

class UpdateToken
{
    public static function tokopedia($auth_token) {
        $responseTokopedia = ServiceTokopedia::AuthToken();
        $statusApi = (empty(json_decode($responseTokopedia)->error)) ? 1 : 0;

        if($statusApi == 1) {
            $tokenTokopedia = json_decode($responseTokopedia)->access_token;
            $expiredTokopedia = json_decode($responseTokopedia)->expires_in;

            $tokenTokopedia = 'Bearer '.trim($tokenTokopedia);

            DB::transaction(function () use ($auth_token, $tokenTokopedia, $expiredTokopedia) {
                DB::insert('exec SP_UserApiTokopedia_SimpanToken ?,?,?', [
                    $auth_token, $tokenTokopedia, time() + $expiredTokopedia
                ]);
            });

            return (object)[
                'status'    => 1,
                'message'   => 'success',
                'data'      => (object)[
                    'token' => $tokenTokopedia
                ]
            ];
        } else {
            return (object)[
                'status'    => 0,
                'message'   => 'Gagal me-refresh token tokopedia, hubungi IT Programmer'
            ];
        }
    }

    public static function shopee($auth_token) {
        $sql = DB::table('user_api_office')->lock('with (nolock)')
                ->where('office_token', $auth_token)
                ->first();

        if(empty($sql->office_token)) {
            return (object)[
                'status'    => 0,
                'message'   => 'Token Suma office tidak ditemukan, lakukan re-login'
            ];
        }

        $user_id = strtoupper(trim($sql->user_id));
        $companyid = strtoupper(trim($sql->companyid));

        $sql_shopee_tokens = DB::table('user_shopee_tokens')->lock('with (nolock)')
                ->orderByRaw("isnull(user_shopee_tokens.id, 0) desc")
                ->first();

        if(empty($sql_shopee_tokens->code)) {
            return (object)[
                'status'    => 0,
                'message'   => 'Access code not found, hubungi IT Programmer'
            ];
        } else {
            if(empty($sql_shopee_tokens->access_token) || trim($sql_shopee_tokens->access_token) == '') {
                return (object)[
                    'status'    => 0,
                    'message'   => 'Access token not found, hubungi IT Programmer'
                ];
            } else {
                $responseshopee = ServiceShopee::GetShopInfo(trim($sql_shopee_tokens->access_token));
                $statusServer = (empty(json_decode($responseshopee)->error)) ? 1 : 0;

                if($statusServer == 1) {
                    DB::transaction(function () use ($auth_token, $sql_shopee_tokens) {
                        DB::update('update  user_api_office
                                    set     shopee_token = ?,
                                            shopee_expired = ?
                                    where   office_token = ?', [
                                                $sql_shopee_tokens->access_token, $sql_shopee_tokens->expire_in,
                                                $auth_token
                                            ]);
                    });

                    return (object)[
                        'status'    => 1,
                        'message'   => 'success',
                        'data'      => (object)[
                            'token' => $sql_shopee_tokens->access_token
                        ]
                    ];
                } else {
                    $responseApi = ServiceShopee::refreshToken($sql_shopee_tokens->refresh_token);
                    $statusApi = (empty(json_decode($responseApi)->error)) ? 1 : 0;

                    if($statusApi == 1) {
                        $dataApi = json_decode($responseApi);

                        DB::transaction(function () use ($auth_token, $dataApi, $sql_shopee_tokens, $user_id, $companyid) {
                            DB::insert('exec SP_UserApiShopee_SimpanToken ?,?,?,?,?,?,?', [
                                $auth_token, $dataApi->refresh_token, $dataApi->access_token,
                                time() + $dataApi->expire_in, $sql_shopee_tokens->code,
                                $user_id, $companyid
                            ]);
                        });

                        return (object)[
                            'status'    => 1,
                            'message'   => 'success',
                            'data'      => (object)[
                                'token' => $dataApi->access_token
                            ]
                        ];
                    } else {
                        return (object)[
                            'status'    => 0,
                            'message'   => 'Gagal me-refresh token shopee, hubungi IT Programmer'
                        ];
                    }
                }
            }
        }
    }
}
