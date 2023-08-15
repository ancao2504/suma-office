<?php

namespace App\Http\Controllers\Api\Backend\Online\Shopee;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use App\Helpers\Api\UpdateToken;
use App\Helpers\Api\ServiceShopee;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
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

            $sql_data = DB::table('stlokasi')->lock('with (nolock)')
                    ->selectRaw("isnull(stlokasi.kd_part, '') as part_number,
                                isnull(part.ket, '') as description,
                                isnull(part.shopee_id, 0) as product_id,
                                isnull(stlokasi.jumlah, 0) as stock,
                                isnull(part.het, 0) as het")
                    ->rightJoin(DB::raw('part with (nolock)'), function($join) {
                        $join->on('part.kd_part', '=', 'stlokasi.kd_part')
                            ->on('part.companyid', '=', 'stlokasi.companyid');
                    })
                    ->where('stlokasi.kd_part', 'like', $request->get('part_number').'%')
                    ->where('stlokasi.kd_lokasi', config('constants.api.shopee.kode_lokasi'))
                    ->where('stlokasi.companyid', $request->get('companyid'))
                    ->orderByRaw("stlokasi.kd_part asc");

            $sql_data = $sql_data->paginate(20);
            $result = collect($sql_data)->toArray();

            $daftar_product = (object)[
                'current_page'      => $result['current_page'],
                'data'              => [],
                'first_page_url'    => $result['first_page_url'],
                'from'              => $result['from'],
                'last_page'         => $result['last_page'],
                'last_page_url'     => $result['last_page_url'],
                'links'             => $result['links'],
                'next_page_url'     => $result['next_page_url'],
                'path'              => $result['path'],
                'per_page'          => $result['per_page'],
                'prev_page_url'     => $result['prev_page_url'],
                'to'                => $result['to'],
                'total'             => $result['total']
            ];

            $authorization = $request->header('Authorization');
            $token = explode(" ", $authorization);
            $auth_token = trim($token[1]);

            $sql = DB::table('user_api_office')->lock('with (nolock)')
                    ->where('office_token', $auth_token)
                    ->first();
            if(empty($sql->user_id)) {
                return Response::responseWarning('Token Suma Office tidak ditemukan, lakukan logout kemudian login kembali');
            }

            $token_shopee = $sql->shopee_token;

            // ==========================================================================
            // CEK KONEKSI API SHOPEE
            // ==========================================================================
            $responseShopee = ServiceShopee::GetShopInfo(trim($token_shopee));

            $statusServer = (empty(json_decode($responseShopee)->message) || json_decode($responseShopee)->message == "") ? 1 : 0;
            if($statusServer == 0) {
                $authorization = $request->header('Authorization');
                $token = explode(" ", $authorization);
                $auth_token = trim($token[1]);

                $responseUpdateToken =  UpdateToken::shopee($auth_token);
                if($responseUpdateToken->status == 0) {
                    return Response::responseWarning($responseUpdateToken->message);
                }

                $token_shopee = $responseUpdateToken->data->token;
            }

            // ==========================================================================
            // AMBIL DATA SHOPEE
            // ==========================================================================
            $data_part_shopee = new Collection();
            $product_id = collect($sql_data->items())
                            ->where('product_id', '!=', 0)
                            ->pluck('product_id')
                            ->implode(',');

            $responseShopee = ServiceShopee::getItem(trim($token_shopee), trim($product_id));

            $statusResponseShopee = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

            if($statusResponseShopee == 1) {
                $dataShopee = json_decode($responseShopee)->response;

                foreach($dataShopee->item_list as $data) {
                    $data_part_shopee->push((object) [
                        'product_id'    => $data->item_id,
                        'name'          => $data->item_name,
                        'sku'           => $data->item_sku,
                        'stock'         => (empty($data->stock_info_v2->seller_stock[0]->stock)) ? 0 : (double)$data->stock_info_v2->seller_stock[0]->stock,
                        'price'         => (empty($data->price_info[0]->original_price)) ? 0 : (double)$data->price_info[0]->original_price,
                        'pictures'      => (empty($data->image->image_url_list)) ? '' : $data->image->image_url_list,
                    ]);
                }

            }

            $data_part_internal = collect($sql_data->items())->toArray();
            foreach($data_part_internal as $data) {
                $data_marketplace = $data_part_shopee
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
                $daftar_product->data[] = (object)[
                    'part_number'   => strtoupper(trim($data->part_number)),
                    'product_id'    => strtoupper(trim($data->product_id)),
                    'description'   => trim($data->description),
                    'het'           => (double)$data->het,
                    'stock'         => (double)$data->stock,
                    'images'        => config('constants.url.images').'/'.strtoupper(trim($data->part_number)).'.jpg',
                    'marketplace'   => $data_marketplace
                ];
            }

            return Response::responseSuccess('success', $daftar_product);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function cekProductId(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'product_id'    => 'required|numeric|digits_between:1,20',
                'companyid'     => 'required',
            ],[
                'product_id.required'   => 'Isi product id terlebih dahulu',
                'companyid.required'    => 'Maaf terdapat kesalahan, mohon reload halaman',
                'product_id.digits_between' => 'Product id yang anda masukkan terlalu panjang',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            $sql = DB::table('part')->lock('with (nolock)')
                    ->selectRaw("isnull(part.kd_part, '') as part_number,
                                isnull(part.shopee_id, '') as product_id")
                    ->where('part.shopee_id', $request->get('product_id'))
                    ->where('part.companyid', $request->get('companyid'))
                    ->first();

            $data_respon = (object)[
                'internal'      => (object)[
                    'status'    => 0,
                    'message'   => '',
                ],
                'marketplace'   => (object)[]
            ];

            if(empty($sql->product_id)) {
                $data_respon->internal->status = 1;
                $data_respon->internal->message = 'Product Id masih belum terdaftar di database internal';
            } else {
                $data_respon->internal->status = 0;
                $data_respon->internal->message = 'Product Id sudah terdaftar pada part number '.strtoupper(trim($sql->part_number));
            }

            $authorization = $request->header('Authorization');
            $token = explode(" ", $authorization);
            $auth_token = trim($token[1]);

            $sql = DB::table('user_api_office')->lock('with (nolock)')
                    ->where('office_token', $auth_token)
                    ->first();

            if(empty($sql->user_id)) {
                return Response::responseWarning('Token Suma Office tidak ditemukan, lakukan logout kemudian login kembali');
            }

            $token_shopee = $sql->shopee_token;

            // ==========================================================================
            // CEK KONEKSI API SHOPEE
            // ==========================================================================
            $responseShopee = ServiceShopee::GetShopInfo(trim($token_shopee));
            $statusServer = (empty(json_decode($responseShopee)->message)) ? 1 : 0;

            if($statusServer == 0) {
                $responseUpdateToken =  UpdateToken::shopee($auth_token);
                if($responseUpdateToken->status == 0) {
                    return Response::responseWarning($responseUpdateToken->message);
                }

                $token_shopee = $responseUpdateToken->data->token;
            }

            // ==========================================================================
            // AMBIL DATA SHOPEE
            // ==========================================================================
            $responseShopee = ServiceShopee::getItem(trim($token_shopee), trim($request->get('product_id')));
            $statusResponseShopee = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

            if($statusResponseShopee == 0) {
                return Response::responseWarning('Pesan dari Shopee : '.json_decode($responseShopee)->message);
            }
            if(collect(json_decode($responseShopee)->response)->count() === 0) {
                return Response::responseWarning('Product Id tidak ditemukan di Shopee');
            }
            $dataShopee = json_decode($responseShopee)->response->item_list;

            foreach($dataShopee as $data) {
                $data_respon->marketplace = (object)[
                    'product_id'    => $data->item_id,
                    'name'          => $data->item_name,
                    'sku'           => $data->item_sku,
                    'stock'         => (empty($data->stock_info_v2->summary_info->total_available_stock)) ? 0 : (double)$data->stock_info_v2->summary_info->total_available_stock,
                    'price'         => (empty($data->price_info->original_price)) ? 0 : (double)$data->price_info->original_price,
                    'pictures'      => (empty($data->image->image_url_list)) ? '' : $data->image->image_url_list,
                ];
            }

            return Response::responseSuccess('success', $data_respon);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function updateProductID(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'part_number'   => 'required',
                'product_id'    => 'required|numeric|digits_between:1,20',
                'companyid'     => 'required',
            ],[
                'part_number.required'  => 'Pilih part number terlebih dahulu',
                'product_id.required'   => 'Isi product id terlebih dahulu',
                'companyid.required'    => 'Maaf terdapat kesalahan, mohon reload halaman',
                'product_id.digits_between' => 'Product id yang diinputkan terlalu panjang',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            $authorization = $request->header('Authorization');
            $token = explode(" ", $authorization);
            $auth_token = trim($token[1]);

            $sql = DB::table('user_api_office')->lock('with (nolock)')
                    ->where('office_token', $auth_token)
                    ->first();

            if(empty($sql->user_id)) {
                return Response::responseWarning('Token Suma Office tidak ditemukan, lakukan logout kemudian login kembali');
            }

            $token_shopee = $sql->shopee_token;

            // ==========================================================================
            // CEK KONEKSI API SHOPEE
            // ==========================================================================
            $responseShopee = ServiceShopee::GetShopInfo(trim($token_shopee));
            $statusServer = (empty(json_decode($responseShopee)->message)) ? 1 : 0;

            if($statusServer == 0) {
                $responseUpdateToken =  UpdateToken::shopee($auth_token);
                if($responseUpdateToken->status == 0) {
                    return Response::responseWarning($responseUpdateToken->message);
                }

                $token_shopee = $responseUpdateToken->data->token;
            }

            // ==========================================================================
            // AMBIL DATA SHOPEE
            // ==========================================================================
            $data_part_shopee = new Collection();
            $responseShopee = ServiceShopee::getItem(trim($token_shopee), trim($request->get('product_id')));
            $statusResponseShopee = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

            if($statusResponseShopee == 0) {
                return Response::responseWarning('Pesan dari Shopee : '.json_decode($responseShopee)->message);
            }
            if(collect(json_decode($responseShopee)->response)->count() === 0) {
                return Response::responseWarning('Product Id tidak ditemukan di Shopee');
            }
            $dataShopee = json_decode($responseShopee)->response->item_list;
            foreach($dataShopee as $data) {
                $sku_shopee = strtoupper(trim($data->item_sku));

                $data_part_shopee->push((object) [
                    'product_id'    => $data->item_id,
                    'name'          => $data->item_name,
                    'sku'           => $data->item_sku,
                    'stock'         => (empty($data->stock_info_v2->summary_info->total_available_stock)) ? 0 : (double)$data->stock_info_v2->summary_info->total_available_stock,
                    'price'         => (empty($data->price_info->original_price)) ? 0 : (double)$data->price_info->original_price,
                    'pictures'      => (empty($data->image->image_url_list)) ? '' : $data->image->image_url_list
                ]);
            }

            if(strtoupper(trim($sku_shopee)) != trim(strtoupper($request->get('part_number')))) {
                return Response::responseWarning('Part number dengan SKU yang ada di Shopee tidak sama');
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_ProductIDShopee_Simpan ?,?,?', [
                    trim(strtoupper($request->get('part_number'))),
                    trim(strtoupper($request->get('product_id'))),
                    trim(strtoupper($request->get('companyid')))
                ]);
            });

            return Response::responseSuccess('Data Berhasil Disimpan', null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function getBrandList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ],[
            'category_id.required' => 'Kategori harus diisi',
        ]);

        if ($validator->fails()) {
            return Response::responseWarning($validator->errors()->first());
        }

        // cek koneksi
        $authorization  = $request->header('Authorization');
        $token_array    = explode(" ", $authorization);
        $auth_token     = trim($token_array[1]);

        $sql = DB::table('user_api_office')->lock('with (nolock)')
                ->where('office_token', $auth_token)
                ->first();

        if(empty($sql->user_id)) {
            return Response::responseWarning('Token Suma Office tidak ditemukan, lakukan logout kemudian login kembali');
        }
        $token = $sql->shopee_token;

        $responsApi = json_decode(ServiceShopee::getBrandList($token, $request->offset??0, $request->category_id));
        if(!empty($responsApi->error)){
            return Response::responseWarning($responsApi->error. ', ' .$responsApi->message);
        }

        $data = (object)[
            'brand_list' => [],
            'page_info' => (object)[
                    'has_next_page' => $responsApi->response->has_next_page,
                    'next_page' => $responsApi->response->next_offset,
                ]
            ];

        $data->brand_list = collect($responsApi->response->brand_list)->map(function($item){
            return (object)[
                "brand_id"  => $item->brand_id,
                "name"      => $item->display_brand_name,
            ];
        });

        return Response::responseSuccess('Berhasil', $data);
    }
}
