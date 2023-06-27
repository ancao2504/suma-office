<?php

namespace App\Http\Controllers\Api\Backend\Online\Tokopedia;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use App\Helpers\Api\UpdateToken;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Helpers\Api\ServiceTokopedia;
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
                    ->whereRaw("ekspedisi_online_detail.jenis_marketplace='TOKOPEDIA' and
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

            $data_ekspedisi = [];
            $data_ekspedisi_aktif = [];

            $responseTokopedia = ServiceTokopedia::LogisticGetShipmentInfo(trim($token_tokopedia));
            $statusResponseTokopedia = (empty(json_decode($responseTokopedia)->header->error_code)) ? 1 : 0;

            if($statusResponseTokopedia == 1) {
                $dataTokopedia = json_decode($responseTokopedia)->data;

                foreach($dataTokopedia as $data) {
                    $data_ekspedisi[] = [
                        'shipper_id'    => (int)$data->shipper_id,
                        'shipper_code'  => strtoupper(trim($data->shipper_code)),
                        'shipper_name'  => trim($data->shipper_name),
                        'shipper_desc'  => trim($data->shipper_desc),
                        'logo'          => trim($data->logo),
                        'internal'      => $data_ekspedisi_internal
                                            ->where('marketplace_id', $data->shipper_id)
                                            ->first()
                    ];
                }

                $responseTokopedia = ServiceTokopedia::LogisticGetShipmentActiveInfo(trim($token_tokopedia));
                $statusResponseTokopedia = (empty(json_decode($responseTokopedia)->header->error_code)) ? 1 : 0;

                if($statusResponseTokopedia == 1) {
                    $dataTokopedia = json_decode($responseTokopedia)->data;

                    foreach($dataTokopedia->Shops as $shop) {
                        foreach($shop->ShipmentInfos as $data) {
                            $data_ekspedisi_aktif[] = [
                                'ShipmentID'    => (int)$data->ShipmentID,
                                'ShipmentCode'  => strtoupper(trim($data->ShipmentCode)),
                                'ShipmentName'  => trim($data->ShipmentName),
                                'ShipmentImage' => trim($data->ShipmentImage),
                                'internal'      => $data_ekspedisi_internal
                                                    ->where('marketplace_id', $data->ShipmentID)
                                                    ->first()
                            ];
                        }

                    }
                }

                $list = [
                    'aktif' => collect($data_ekspedisi_aktif)->sortBy('ShipmentID')->toArray(),
                    'list'  => collect($data_ekspedisi)->sortBy('shipper_id')->toArray(),
                ];

                return Response::responseSuccess('success', $list);
            } else {
                return Response::responseWarning(json_decode($responseTokopedia)->header->reason);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function simpanEkspedisi(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'tokopedia_id'  => 'required',
                'kode'          => 'required',
                'nama'          => 'required',
                'user_id'       => 'required',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Id, shopee id, kode, nama, dan user id harus terisi");
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_EkspedisiOnline_SimpanDetailEkspedisi ?,?,?,?,?,?', [
                    $request->get('id_internal'), 'TOKOPEDIA', trim($request->get('tokopedia_id')),
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
