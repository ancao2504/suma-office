<?php

namespace App\Http\Controllers\Api\Backend\Online\Shopee;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use App\Helpers\Api\UpdateToken;
use App\Helpers\Api\ServiceShopee;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class ApiEkspedisiController extends Controller
{
    public function daftarEkspedisi(Request $request) {
        try {
            $sql = DB::table('ekspedisi_online_detail')->lock('with (nolock)')
                    ->selectRaw("isnull(ekspedisi_online_detail.id, 0) as id,
                            isnull(ekspedisi_online_detail.marketplace_id, '') as marketplace_id,
                            isnull(ekspedisi_online_detail.kd_ekspedisi, '') as kode_ekpedisi,
                            isnull(ekspedisi_online.nm_ekspedisi, '') as nama_ekspedisi,
                            isnull(ekspedisi_online_detail.keterangan, '') as keterangan")
                    ->leftJoin(DB::raw('ekspedisi_online with (nolock)'), function ($join) {
                        $join->on('ekspedisi_online.kd_ekspedisi', '=', 'ekspedisi_online_detail.kd_ekspedisi');
                    })
                    ->whereRaw("ekspedisi_online_detail.jenis_marketplace='SHOPEE' and
                                isnull(ekspedisi_online_detail.marketplace_id, '') <> ''")
                    ->orderByRaw("ekspedisi_online_detail.marketplace_id asc")
                    ->get();

            $data_ekspedisi_internal = new Collection();

            foreach($sql as $data) {
                $data_ekspedisi_internal->push((object) [
                    'id'                => strtoupper(trim($data->id)),
                    'marketplace_id'    => strtoupper(trim($data->marketplace_id)),
                    'kode_ekspedisi'    => strtoupper(trim($data->kode_ekpedisi)),
                    'nama_ekspedisi'    => trim($data->nama_ekspedisi),
                    'keterangan'        => trim($data->keterangan),
                ]);
            }

            $authorization = $request->header('Authorization');
            $token = explode(" ", $authorization);
            $auth_token = trim($token[1]);

            $token_shopee = '';

            $sql = DB::table('user_api_office')->lock('with (nolock)')
                    ->selectRaw("isnull(user_api_office.shopee_token, '') as shopee_token,
                                isnull(user_api_office.user_id, '') as user_id")
                    ->where('user_api_office.office_token', $auth_token)
                    ->orderByRaw("isnull(user_api_office.id, 0) desc")
                    ->first();

            if(empty($sql->shopee_token) || trim($sql->shopee_token) == '') {
                return Response::responseWarning('Token shopee tidak ditemukan, lakukan logout kemudian login kembali');
            } else {
                $token_shopee = $sql->shopee_token;
            }

            // ==========================================================================
            // CEK KONEKSI API SHOPEE
            // ==========================================================================
            $responseShopee = ServiceShopee::GetShopInfo(trim($token_shopee));
            $statusServer = (empty(json_decode($responseShopee)->message)) ? 1 : 0;

            if($statusServer == 0) {
                $authorization = $request->header('Authorization');
                $token = explode(" ", $authorization);
                $auth_token = trim($token[1]);

                $responseUpdateToken = UpdateToken::shopee($auth_token);

                if($responseUpdateToken->status == 1) {
                    $token_shopee = $responseUpdateToken->data->token;
                } else {
                    return Response::responseWarning($responseUpdateToken->message);
                }
            }


            $data_ekspedisi = [];
            $data_ekspedisi_aktif = [];

            $responseShopee = ServiceShopee::GetChannelList(trim($token_shopee));
            $statusResponseShopee = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

            if($statusResponseShopee == 1) {
                $dataShopee = json_decode($responseShopee)->response;

                foreach($dataShopee->logistics_channel_list as $data) {
                    $data_ekspedisi[] = [
                        'logistics_channel_id'      => (int)$data->logistics_channel_id,
                        'logistics_channel_name'    => trim($data->logistics_channel_name),
                        'logistics_description'     => trim($data->logistics_description),
                        'cod_enabled'               => $data->cod_enabled,
                        'enabled'                   => $data->enabled,
                        'internal'                  => $data_ekspedisi_internal
                                                        ->where('marketplace_id', $data->logistics_channel_id)
                                                        ->first()
                    ];

                    if($data->enabled == true) {
                        $data_ekspedisi_aktif[] = [
                            'logistics_channel_id'      => (int)$data->logistics_channel_id,
                            'logistics_channel_name'    => trim($data->logistics_channel_name),
                            'logistics_description'     => trim($data->logistics_description),
                            'cod_enabled'               => $data->cod_enabled,
                            'enabled'                   => $data->enabled,
                            'internal'                  => $data_ekspedisi_internal
                                                        ->where('marketplace_id', $data->logistics_channel_id)
                                                        ->first()
                        ];
                    }
                }

                $list = [
                    'aktif' => collect($data_ekspedisi_aktif)->sortBy('logistics_channel_id')->toArray(),
                    'list'  => collect($data_ekspedisi)->sortBy('logistics_channel_id')->toArray(),
                ];

                return Response::responseSuccess('success', $list);
            } else {
                return Response::responseWarning(json_decode($responseShopee)->error);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function simpanEkspedisi(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'shopee_id' => 'required',
                'kode'      => 'required',
                'nama'      => 'required',
                'user_id'   => 'required',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Id, shopee id, kode, nama, dan user id harus terisi");
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_EkspedisiOnline_SimpanDetailEkspedisi ?,?,?,?,?,?', [
                    trim($request->get('id_internal')), 'SHOPEE', trim($request->get('shopee_id')),
                    strtoupper(trim($request->get('kode'))), strtoupper(trim($request->get('nama'))),
                    strtoupper(trim($request->get('user_id'))),
                ]);
            });

            return Response::responseSuccess("Data Berhasil Disimpan");
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
