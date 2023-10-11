<?php

namespace App\Http\Controllers\Api\Backend\Online\Tiktok;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use App\Helpers\Api\UpdateToken;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Helpers\Api\ServiceTiktok;
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
                    ->whereRaw("ekspedisi_online_detail.jenis_marketplace='TIKTOK' and
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

            $token_tiktok = '';

            $sql = DB::table('user_api_office')->lock('with (nolock)')
                    ->selectRaw("isnull(user_api_office.tiktok_token, '') as tiktok_token,
                                isnull(user_api_office.user_id, '') as user_id")
                    ->where('user_api_office.office_token', $auth_token)
                    ->orderByRaw("isnull(user_api_office.id, 0) desc")
                    ->first();

            if(empty($sql->tiktok_token) || trim($sql->tiktok_token) == '') {
                return Response::responseWarning('Token tiktok tidak ditemukan, lakukan logout kemudian login kembali');
            } else {
                $token_tiktok = $sql->tiktok_token;
            }

            // ==========================================================================
            // CEK KONEKSI API TIKTOK
            // ==========================================================================
            $responseTiktok = ServiceTiktok::GetAuthorizedShops(trim($token_tiktok));
            $statusServer = (empty(json_decode($responseTiktok)->message)) ? 1 : 0;

            if($statusServer == 0) {
                $authorization = $request->header('Authorization');
                $token = explode(" ", $authorization);
                $auth_token = trim($token[1]);

                $responseUpdateToken = UpdateToken::tiktok($auth_token);

                if($responseUpdateToken->status == 1) {
                    $token_tiktok = $responseUpdateToken->data->token;
                } else {
                    return Response::responseWarning($responseUpdateToken->message);
                }
            }

            $data_ekspedisi = [];

            $responseTiktok = ServiceTiktok::GetShippingProvider(trim($token_tiktok));
            $statusResponseTiktok = (empty(json_decode($responseTiktok)->header->error_code)) ? 1 : 0;

            if($statusResponseTiktok == 1) {
                $dataTiktok = json_decode($responseTiktok)->data;

                foreach($dataTiktok->delivery_option_list as $data) {
                    $shipping_provider_list = [];

                    foreach($data->shipping_provider_list as $list) {
                        $shipping_provider_list[] = [
                            'shipping_provider_id'  => $list->shipping_provider_id,
                            'shipping_provider_name'=> $list->shipping_provider_name,
                            'internal'              => $data_ekspedisi_internal
                                                        ->where('marketplace_id', $list->shipping_provider_id)
                                                        ->first()
                        ];
                    }

                    $data_ekspedisi[] = [
                        'delivery_option_id'    => $data->delivery_option_id,
                        'delivery_option_name'  => trim($data->delivery_option_name),
                        'shipping_provider_list'=> $shipping_provider_list
                    ];
                }

                $list = [
                    'aktif' => collect($data_ekspedisi)->sortBy('delivery_option_id')->toArray(),
                    'list'  => collect($data_ekspedisi)->sortBy('delivery_option_id')->toArray(),
                ];

                return Response::responseSuccess('success', $list);
            } else {
                return Response::responseWarning(json_decode($responseTiktok)->header->reason);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function simpanEkspedisi(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'tiktok_id'  => 'required',
                'kode'          => 'required',
                'nama'          => 'required',
                'user_id'       => 'required',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Id, shopee id, kode, nama, dan user id harus terisi");
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_EkspedisiOnline_SimpanDetailEkspedisi ?,?,?,?,?,?', [
                    $request->get('id_internal'), 'TIKTOK', trim($request->get('tiktok_id')),
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
