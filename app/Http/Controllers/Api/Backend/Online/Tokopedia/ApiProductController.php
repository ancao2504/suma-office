<?php

namespace App\Http\Controllers\Api\Backend\Online\Tokopedia;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use App\Helpers\Api\UpdateToken;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Helpers\Api\ServiceTokopedia;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class ApiProductController extends Controller
{
    public function daftarPartNumber(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'part_number'   => 'required',
                'companyid'     => 'required',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Isi data part number terlebih dahulu");
            }

            $length_part_number = Str::length($request->get('part_number'));

            if((int)$length_part_number < 5) {
                return Response::responseWarning("Data part number harus diisi minimal 5 karakter");
            }

            $sql = DB::table('stlokasi')->lock('with (nolock)')
                    ->selectRaw("isnull(stlokasi.kd_part, '') as part_number,
                                isnull(part.ket, '') as description,
                                isnull(part.tokopedia_id, 0) as product_id,
                                isnull(stlokasi.jumlah, 0) as stock,
                                isnull(part.het, 0) as het")
                    ->leftJoin(DB::raw('part with (nolock)'), function($join) {
                        $join->on('part.kd_part', '=', 'stlokasi.kd_part')
                            ->on('part.companyid', '=', 'stlokasi.companyid');
                    })
                    ->where('stlokasi.kd_part', 'like', $request->get('part_number').'%')
                    ->where('stlokasi.kd_lokasi', config('constants.api.tokopedia.kode_lokasi'))
                    ->where('stlokasi.companyid', $request->get('companyid'))
                    ->orderByRaw("stlokasi.kd_part asc");

            $sql = $sql->paginate(20);

            $result = collect($sql)->toArray();

            $current_page = $result['current_page'];
            $data = $result['data'];
            $first_page_url = $result['first_page_url'];
            $from = $result['from'];
            $last_page = $result['last_page'];
            $last_page_url = $result['last_page_url'];
            $links = $result['links'];
            $next_page_url = $result['next_page_url'];
            $path = $result['path'];
            $per_page = $result['per_page'];
            $prev_page_url = $result['prev_page_url'];
            $to = $result['to'];
            $total = $result['total'];

            $jumlah_data = 0;
            $product_id = '';
            $data_part_internal = new Collection();

            foreach($data as $data) {
                $jumlah_data = (double)$jumlah_data + 1;

                if(trim($product_id) == '') {
                    $product_id = (int)$data->product_id;
                } else {
                    $product_id .= ','.(int)$data->product_id;
                }

                $data_part_internal->push((object) [
                    'part_number'   => strtoupper(trim($data->part_number)),
                    'product_id'    => strtoupper(trim($data->product_id)),
                    'description'   => trim($data->description),
                    'het'           => (double)$data->het,
                    'stock'         => (double)$data->stock,
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

            // ==========================================================================
            // AMBIL DATA TOKOPEDIA
            // ==========================================================================
            $data_part_tokopedia = new Collection();
            $responseTokopedia = ServiceTokopedia::GetProductInfoByProductId(trim($token_tokopedia), trim($product_id));
            $statusResponseTokopedia = (empty(json_decode($responseTokopedia)->header->error_code)) ? 1 : 0;

            if($statusResponseTokopedia == 1) {
                $dataTokopedia = json_decode($responseTokopedia)->data;

                foreach($dataTokopedia as $data) {
                    $data_part_tokopedia->push((object) [
                        'product_id'    => $data->basic->productID,
                        'name'          => $data->basic->name,
                        'sku'           => $data->other->sku,
                        'stock'         => (empty($data->stock->value)) ? 0 : (double)$data->stock->value,
                        'price'         => (empty($data->price->value)) ? 0 : (double)$data->price->value,
                        'pictures'      => (empty($data->pictures[0]->ThumbnailURL)) ? '' : $data->pictures[0]->ThumbnailURL,

                    ]);
                }
            }

            $data_part_number = [];

            foreach($data_part_internal as $data) {
                $data_marketplace = $data_part_tokopedia
                                    ->where('product_id', $data->product_id)
                                    ->first();
                if($data_marketplace == '') {
                    $data_marketplace = [
                        'product_id'    => '',
                        'name'          => '',
                        'sku'           => '',
                        'stock'         => 0,
                        'price'         => 0,
                        'pictures'      => ''
                    ];
                }
                $data_part_number[] = [
                    'part_number'   => strtoupper(trim($data->part_number)),
                    'product_id'    => strtoupper(trim($data->product_id)),
                    'description'   => trim($data->description),
                    'het'           => (double)$data->het,
                    'stock'         => (double)$data->stock,
                    'images'        => config('constants.url.images').'/'.strtoupper(trim($data->part_number)).'.jpg',
                    'marketplace'   => $data_marketplace
                ];
            }

            $daftar_product = [
                'current_page'  => $current_page,
                'data'          => $data_part_number,
                'first_page_url' => $first_page_url,
                'from'          => $from,
                'last_page'     => $last_page,
                'last_page_url' => $last_page_url,
                'links'         => $links,
                'next_page_url' => $next_page_url,
                'path'          => $path,
                'per_page'      => $per_page,
                'prev_page_url' => $prev_page_url,
                'to'            => $to,
                'total'         => $total
            ];

            return Response::responseSuccess('success', $daftar_product);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function cekProductId(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'product_id'    => 'required',
                'companyid'     => 'required',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Isi product id terlebih dahulu");
            }

            $statusInternal = 0;
            $messageInternal = '';
            $messageTokopedia = '';

            $sql = DB::table('part')->lock('with (nolock)')
                    ->selectRaw("isnull(part.kd_part, '') as part_number,
                                isnull(part.tokopedia_id, 0) as product_id")
                    ->where('part.tokopedia_id', $request->get('product_id'))
                    ->where('part.companyid', $request->get('companyid'))
                    ->first();

            if(empty($sql->part_number)) {
                $statusInternal = 1;
                $messageInternal = 'Product Id masih belum terdaftar di database internal';
            } else {
                $statusInternal = 0;
                $messageInternal = 'Product Id sudah terdaftar pada part number '.strtoupper(trim($sql->part_number));
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

            // ==========================================================================
            // AMBIL DATA TOKOPEDIA
            // ==========================================================================
            $data_part_tokopedia = new Collection();
            $responseTokopedia = ServiceTokopedia::GetProductInfoByProductId(trim($token_tokopedia), trim($request->get('product_id')));
            $statusResponseTokopedia = (empty(json_decode($responseTokopedia)->header->error_code)) ? 1 : 0;

            if($statusResponseTokopedia == 1) {
                $dataTokopedia = json_decode($responseTokopedia)->data;

                foreach($dataTokopedia as $data) {
                    $data_part_tokopedia->push((object) [
                        'product_id'    => $data->basic->productID,
                        'name'          => $data->basic->name,
                        'sku'           => $data->other->sku,
                        'stock'         => (empty($data->stock->value)) ? 0 : (double)$data->stock->value,
                        'price'         => (empty($data->price->value)) ? 0 : (double)$data->price->value,
                        'pictures'      => (empty($data->pictures[0]->ThumbnailURL)) ? '' : $data->pictures[0]->ThumbnailURL,
                    ]);
                }
            } else {
                return Response::responseWarning('Pesan dari Tokopedia : '.json_decode($responseTokopedia)->header->reason);
            }

            $data = [
                'internal'      => [
                    'status'    => $statusInternal,
                    'message'   => $messageInternal,
                ],
                'marketplace'   => $data_part_tokopedia->first()
            ];

            return Response::responseSuccess('success', $data);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function updateProductID(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'part_number'   => 'required',
                'product_id'    => 'required',
                'companyid'     => 'required',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih part number dan isi product id terlebih dahulu");
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

            // ==========================================================================
            // AMBIL DATA TOKOPEDIA
            // ==========================================================================
            $data_part_tokopedia = new Collection();
            $responseTokopedia = ServiceTokopedia::GetProductInfoByProductId(trim($token_tokopedia), trim($request->get('product_id')));
            $statusResponseTokopedia = (empty(json_decode($responseTokopedia)->header->error_code)) ? 1 : 0;

            $sku_tokopedia = '';

            if($statusResponseTokopedia == 1) {
                $dataTokopedia = json_decode($responseTokopedia)->data;

                foreach($dataTokopedia as $data) {
                    $sku_tokopedia = strtoupper(trim($data->other->sku));

                    $data_part_tokopedia->push((object) [
                        'product_id'    => $data->basic->productID,
                        'name'          => $data->basic->name,
                        'sku'           => $data->other->sku,
                        'stock'         => (empty($data->stock->value)) ? 0 : (double)$data->stock->value,
                        'price'         => (empty($data->price->value)) ? 0 : (double)$data->price->value,
                        'pictures'      => (empty($data->pictures[0]->ThumbnailURL)) ? '' : $data->pictures[0]->ThumbnailURL,

                    ]);
                }
            } else {
                return Response::responseWarning('Pesan dari Tokopedia : '.json_decode($responseTokopedia)->header->reason);
            }

            if(strtoupper(trim($sku_tokopedia)) != trim(strtoupper($request->get('part_number')))) {
                return Response::responseWarning('Part number dengan SKU yang ada di Tokopedia tidak sama');
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_ProductIDTokopedia_Simpan ?,?,?', [
                    trim(strtoupper($request->get('part_number'))), trim(strtoupper($request->get('product_id'))),
                    trim(strtoupper($request->get('companyid')))
                ]);
            });

            return Response::responseSuccess('Data Berhasil Disimpan', null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
