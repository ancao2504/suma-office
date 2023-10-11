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

        $sql = DB::table('user_shopee_tokens')->lock('with (nolock)')
                ->orderByRaw("isnull(user_shopee_tokens.id, 0) desc")
                ->first();

        if(empty($sql->code)) {
            return (object)[
                'status'    => 0,
                'message'   => 'Access code marketplace shopee not found, hubungi IT Programmer'
            ];
        } else {
            if(empty($sql->access_token) || trim($sql->access_token) == '') {
                return (object)[
                    'status'    => 0,
                    'message'   => 'Access token marketplace shopee not found, hubungi IT Programmer'
                ];
            } else {
                $responseshopee = ServiceShopee::GetShopInfo(trim($sql->access_token));
                $statusServer = (empty(json_decode($responseshopee)->error)) ? 1 : 0;

                if($statusServer == 1) {
                    DB::transaction(function () use ($auth_token, $sql) {
                        DB::update('update  user_api_office
                                    set     shopee_token = ?,
                                            shopee_expired = ?
                                    where   office_token = ?', [
                                                $sql->access_token, $sql->expire_in,
                                                $auth_token
                                            ]);
                    });

                    return (object)[
                        'status'    => 1,
                        'message'   => 'success',
                        'data'      => (object)[
                            'token' => $sql->access_token
                        ]
                    ];
                } else {
                    $responseApi = ServiceShopee::refreshToken($sql->refresh_token);
                    $statusApi = (empty(json_decode($responseApi)->error)) ? 1 : 0;

                    if($statusApi == 1) {
                        $dataApi = json_decode($responseApi);

                        DB::transaction(function () use ($auth_token, $dataApi, $sql, $user_id, $companyid) {
                            DB::insert('exec SP_UserApiShopee_SimpanToken ?,?,?,?,?,?,?', [
                                $auth_token, $dataApi->refresh_token, $dataApi->access_token,
                                time() + $dataApi->expire_in, $sql->code,
                                strtoupper(trim($user_id)), strtoupper(trim($companyid))
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

    public static function tiktok($auth_token) {
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

        $sql = DB::table('user_tiktok_tokens')->lock('with (nolock)')
                ->orderByRaw("isnull(user_tiktok_tokens.id, 0) desc")
                ->first();

        if(empty($sql->access_token)) {
            return (object)[
                'status'    => 0,
                'message'   => 'Access code marketplace tiktok not found, hubungi IT Programmer'
            ];
        } else {
            if(empty($sql->access_token) || trim($sql->access_token) == '') {
                return (object)[
                    'status'    => 0,
                    'message'   => 'Access token marketplace tiktok not found, hubungi IT Programmer'
                ];
            } else {
                $responseTiktok = ServiceTiktok::GetAuthorizedShops(trim($sql->access_token));
                $statusServer = (json_decode($responseTiktok)->code == 0) ? 1 : 0;

                if($statusServer == 1) {
                    DB::transaction(function () use ($auth_token, $sql) {
                        DB::update('update  user_api_office
                                    set     tiktok_token = ?,
                                            tiktok_expired = ?
                                    where   office_token = ?', [
                                                $sql->access_token, $sql->access_token_expire_in,
                                                $auth_token
                                            ]);
                    });

                    return (object)[
                        'status'    => 1,
                        'message'   => 'success',
                        'data'      => (object)[
                            'token' => $sql->access_token
                        ]
                    ];
                } else {
                    $responseTiktok = ServiceTiktok::GetRefreshToken($sql->refresh_token);
                    $statusServer = (json_decode($responseTiktok)->code == 0) ? 1 : 0;

                    if($statusServer == 1) {
                        $dataApi = json_decode($responseTiktok)->data;

                        DB::transaction(function () use ($dataApi, $user_id, $companyid) {
                            DB::insert('exec SP_UserApiTiktok_SimpanToken ?,?,?,?,?,?,?,?,?,?', [
                                $dataApi->access_token, $dataApi->access_token_expire_in,
                                $dataApi->refresh_token, $dataApi->refresh_token_expire_in,
                                $dataApi->open_id, $dataApi->seller_name,
                                $dataApi->seller_base_region, $dataApi->access_token,
                                $dataApi->access_token, $dataApi->user_type,
                                strtoupper(trim($user_id)), strtoupper(trim($companyid))
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
