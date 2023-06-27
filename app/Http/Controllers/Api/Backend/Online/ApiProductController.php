<?php

namespace App\Http\Controllers\Api\Backend\Online;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use App\Helpers\Api\UpdateToken;
use App\Helpers\Api\ServiceShopee;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Helpers\Api\ServiceTokopedia;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class ApiProductController extends Controller
{
    public function DaftarProduct(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'part_number'   => 'required|min:5|max:20',
                'companyid'     => 'required',
            ],[
                'part_number.required'  => 'Isi data part number terlebih dahulu',
                'part_number.min'       => 'Part number minimal 5 karakter',
                'part_number.max'       => 'Part number maksimal 20 karakter',
                'companyid.required'    => 'Isi data companyid terlebih dahulu',
            ]);

            if($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            $sql_data = DB::table('part')->lock('with (nolock)')
                    ->selectRaw("isnull(part.kd_part, '') as part_number,
                                isnull(part.shopee_id, 0) as product_id_shopee,
                                isnull(part.tokopedia_id, 0) as product_id_tokopedia")
                    ->where('part.kd_part', 'like', $request->get('part_number').'%')
                    ->where('part.companyid', $request->get('companyid'))
                    ->where(function($query) {
                        $query->where('part.shopee_id', '!=', 0)
                            ->orWhere('part.tokopedia_id', '!=', 0);
                    })
                    ->orderByRaw("part.kd_part asc");
            $sql_data = $sql_data->paginate($request->get('per_page', 10));
            $result = collect($sql_data)->toArray();
            if($result['total'] == 0) {
                return Response::responseWarning("Data part number tidak ditemukan");
            }
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
            $token_array = explode(" ", $authorization);
            $auth_token = trim($token_array[1]);

            $sql = DB::table('user_api_office')->lock('with (nolock)')
                    ->where('office_token', $auth_token)
                    ->first();
            if(empty($sql->user_id)) {
                return Response::responseWarning('Token Suma Office tidak ditemukan, lakukan logout kemudian login kembali');
            }
            $token = new \stdClass();
            $token->shopee = $sql->shopee_token;
            $token->tokopedia = $sql->tokopedia_token;

            //! ==========================================================================
            //! CEK KONEKSI API SHOPEE DAN TOKOPEDIA
            //! ==========================================================================
            $responseShopee = ServiceShopee::GetShopInfo(trim($token->shopee));
            $responseTokopedia = ServiceTokopedia::GetShopInfo(trim($token->tokopedia));
            $statusServerShopee = (empty(json_decode($responseShopee)->message) || json_decode($responseShopee)->message == "") ? 1 : 0;
            $statusServerTokopedia = (empty(json_decode($responseTokopedia)->message)) ? 1 : 0;

            $authorization = $request->header('Authorization');
            $token_array = explode(" ", $authorization);
            $auth_token = trim($token_array[1]);

            if($statusServerShopee == 0) {
                $responseUpdateToken =  UpdateToken::shopee($auth_token);
                if($responseUpdateToken->status == 0) {
                    return Response::responseWarning($responseUpdateToken->message);
                }
                $token->shopee = $responseUpdateToken->data->token;
            }

            if($statusServerTokopedia == 0) {
                $responseUpdateToken = UpdateToken::tokopedia($auth_token);
                if($responseUpdateToken->status == 0) {
                    return Response::responseWarning($responseUpdateToken->message);
                }
                $token->tokopedia = $responseUpdateToken->data->token;
            }

            if(collect($sql_data->items())
            ->where('product_id_shopee', '!=', 0)
            ->pluck('product_id_shopee')->count() > 50){
                return Response::responseWarning("Maaf hasil pencarian terlalubanyak, mohon lebih sepesifik");
            }
            if(collect($sql_data->items())
            ->where('product_id_tokopedia', '!=', 0)
            ->pluck('product_id_tokopedia')->count() > 50){
                return Response::responseWarning("Maaf hasil pencarian terlalubanyak, mohon lebih sepesifik");
            }
            //! ==========================================================================
            //! AMBIL DATA SHOPEE DAN TOKOPEDIA
            //! ==========================================================================
            $data_part = new \stdClass();
            $product_id_shopee = collect($sql_data->items())
                            ->where('product_id_shopee', '!=', 0)
                            ->pluck('product_id_shopee')
                            ->implode(',');
            $product_id_tokopedia = collect($sql_data->items())
                            ->where('product_id_tokopedia', '!=', 0)
                            ->pluck('product_id_tokopedia')
                            ->implode(',');
            $responseShopee = ServiceShopee::getItem(trim($token->shopee), trim($product_id_shopee));
            $responseTokopedia = ServiceTokopedia::GetProductInfoByProductId(trim($token->tokopedia), trim($product_id_tokopedia));

            $statusResponseShopee = (empty(json_decode($responseShopee)->error)) ? 1 : 0;
            $statusResponseTokopedia = (empty(json_decode($responseTokopedia)->header->error_code)) ? 1 : 0;

            if($statusResponseShopee == 1){
                $dataShopee = json_decode($responseShopee)->response;
                foreach($dataShopee->item_list as $data) {
                    $data_part->shopee[] = (object)[
                        'product_id'    => $data->item_id,
                        'name'          => $data->item_name,
                        'sku'           => $data->item_sku,
                        'stock'         => (empty($data->stock_info_v2->seller_stock[0]->stock)) ? 0 : (double)$data->stock_info_v2->seller_stock[0]->stock,
                        'price'         => (empty($data->price_info[0]->original_price)) ? 0 : (double)$data->price_info[0]->original_price,
                        'pictures'      => (empty($data->image->image_url_list)) ? '' : $data->image->image_url_list,
                    ];
                }
            }

            if($statusResponseTokopedia == 1){
                $dataTokopedia = json_decode($responseTokopedia)->data;
                foreach($dataTokopedia as $data) {
                    $data_part->tokopedia[] = (object)[
                        'product_id'    => $data->basic->productID,
                        'name'          => $data->basic->name,
                        'sku'           => $data->other->sku,
                        'stock'         => (empty($data->stock->value)) ? 0 : (double)$data->stock->value,
                        'price'         => (empty($data->price->value)) ? 0 : (double)$data->price->value,
                        'pictures'      => (empty($data->pictures[0]->ThumbnailURL)) ? '' : $data->pictures[0]->ThumbnailURL,
                    ];
                }
            }

            $data_part_internal = collect($sql_data->items())->toArray();

            $list_part = new \stdClass();
            foreach($data_part_internal as $data) {
                $list_part->{trim($data->part_number)} = new \stdClass();

                $list_part->{trim($data->part_number)}->part_number = trim($data->part_number);
                $list_part->{trim($data->part_number)}->product_id_shopee = $data->product_id_shopee;
                $list_part->{trim($data->part_number)}->product_id_tokopedia = $data->product_id_tokopedia;

                if($data->product_id_shopee != 0 && collect($data_part->shopee)->where('product_id', $data->product_id_shopee)->first() != ''){
                    $list_part->{trim($data->part_number)}->shopee = collect($data_part->shopee)->where('product_id', $data->product_id_shopee)->first();
                } elseif ($data->product_id_shopee == 0) {
                    $list_part->{trim($data->part_number)}->shopee = (object)['sku' => null, 'status' => 1,'messages' => 'Belum ada produk di shopee'];
                } else {
                    $list_part->{trim($data->part_number)}->shopee = (object)['sku' => null, 'status' => 0,'messages' => 'maaf terjadi kesalahan pada shopee'];
                }

                if($data->product_id_tokopedia != 0 && collect($data_part->tokopedia)->where('product_id', $data->product_id_tokopedia)->first() != ''){
                    $list_part->{trim($data->part_number)}->tokopedia = collect($data_part->tokopedia)->where('product_id', $data->product_id_tokopedia)->first();
                } elseif($data->product_id_tokopedia == 0) {
                    $list_part->{trim($data->part_number)}->tokopedia = (object)['sku' => null, 'status' => 1,'messages' => 'Belum ada produk di tokopedia'];
                } else {
                    $list_part->{trim($data->part_number)}->tokopedia = (object)['sku' => null, 'status' => 0, 'messages' => 'maaf terjadi kesalahan pada tokopedia'];
                }

            }
            $daftar_product->data = $list_part;
            return Response::responseSuccess('success', $daftar_product);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function DetailProduct(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'part_number'   => 'required',
                'companyid'     => 'required',
            ],[
                'part_number.required'  => 'Isi data part number terlebih dahulu',
                'companyid.required'    => 'Isi data companyid terlebih dahulu',
            ]);

            if($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            $sql_data = DB::table('part')->lock('with (nolock)')
                        ->selectRaw("isnull(part.kd_part, '') as part_number,
                                    isnull(part.shopee_id, 0) as product_id_shopee,
                                    isnull(part.tokopedia_id, 0) as product_id_tokopedia")
                        ->where('part.kd_part', trim($request->get('part_number')))
                        ->where('part.companyid', $request->get('companyid'))
                        ->first();
            // ! jika data tidak ditemukan
            if ($sql_data == null) {
                return Response::responseWarning('Data tidak ditemukan');
            }

            if($sql_data->product_id_shopee == 0 && $sql_data->product_id_tokopedia == 0){
                return Response::responseWarning('Data produk pada kedua marketplace belum ada, untuk mengunakan fitur ini harus ada produk minimal di salah satu marketplace');
            }

            $authorization = $request->header('Authorization');
            $token_array = explode(" ", $authorization);
            $auth_token = trim($token_array[1]);

            $sql = DB::table('user_api_office')->lock('with (nolock)')
                    ->where('office_token', $auth_token)
                    ->first();
            if(empty($sql->user_id)) {
                return Response::responseWarning('Token Suma Office tidak ditemukan, lakukan logout kemudian login kembali');
            }
            $token = new \stdClass();
            $token->shopee = $sql->shopee_token;
            $token->tokopedia = $sql->tokopedia_token;

            // ! ==========================================================================
            // ! CEK KONEKSI API SHOPEE DAN TOKOPEDIA
            // ! ==========================================================================
            $responseShopee = ServiceShopee::GetShopInfo(trim($token->shopee));
            $responseTokopedia = ServiceTokopedia::GetShopInfo(trim($token->tokopedia));
            $statusServerShopee = (empty(json_decode($responseShopee)->message)) ? 1 : 0;
            $statusServerTokopedia = (empty(json_decode($responseTokopedia)->message)) ? 1 : 0;

            $authorization = $request->header('Authorization');
            $token_array = explode(" ", $authorization);
            $auth_token = trim($token_array[1]);

            if($statusServerShopee == 0) {
                $responseUpdateToken =  UpdateToken::shopee($auth_token);
                if($responseUpdateToken->status == 0) {
                    return Response::responseWarning($responseUpdateToken->message);
                }
                $token->shopee = $responseUpdateToken->data->token;
            }

            if($statusServerTokopedia == 0) {
                $responseUpdateToken = UpdateToken::tokopedia($auth_token);
                if($responseUpdateToken->status == 0) {
                    return Response::responseWarning($responseUpdateToken->message);
                }
                $token->tokopedia = $responseUpdateToken->data->token;
            }

            // ! ==========================================================================
            // ! AMBIL DATA SHOPEE DAN TOKOPEDIA
            // ! ==========================================================================
            $responsData = (object)[
                'shopee' => (object)[],
                'tokopedia' => (object)[]
            ];
            $responsStatus = (object)[
                'shopee' => (object)[
                    'produk' => 0
                ],
                'tokopedia' => (object)[
                    'produk' => 0
                ]
            ];

            // ! AMBIL DATA KATEGORI SHOPEE
            $responsData->shopee->kategori = ServiceShopee::getCategory(trim($token->shopee));
            $responsStatus->shopee->kategori = (empty(json_decode($responsData->shopee->kategori)->error)) ? 1 : 0;

            $kategoriShopee = collect(json_decode($responsData->shopee->kategori)->response->category_list)
            ->sortBy('category_id')
            ->groupBy('parent_category_id');

            // ! GROUPING KATEGORI SHOPEE
            $kategoriShopee['0']->map(function($item) use ($kategoriShopee) {
                if($item->has_children == true){
                    $item->sub = $kategoriShopee[$item->category_id];
                    $item->sub->map(function($item2) use ($kategoriShopee) {
                        if($item2->has_children == true){
                            $item2->sub = $kategoriShopee[$item2->category_id];
                            $item2->sub->map(function($item3) use ($kategoriShopee) {
                                if($item3->has_children == true){
                                    $item3->sub = $kategoriShopee[$item3->category_id];
                                }
                            });
                        }
                    });
                }
            });

            //  ! HASIL GROUPING KATEGORI SHOPEE AMBIL HANYA KATEGORI MOTOR
            $kategoriShopee = $kategoriShopee['0']->where('category_id', 100641)->first();

            // ! AMBIL DATA KATEGORI TOKOPEDIA
            $responsData->tokopedia->kategori = ServiceTokopedia::GetAllCategories(trim($token->tokopedia));
            $responsStatus->tokopedia->kategori = (empty(json_decode($responsData->tokopedia->kategori)->header->error_code)) ? 1 : 0;

            // ! Hanaya kategori otomotif
            $kategoriTokopedia = collect(json_decode($responsData->tokopedia->kategori)->data->categories)->where('id','63')->first();
            // ! CEK product_id mana yang bernilai 0
            if($sql_data->product_id_tokopedia == 0){
                $responsData->shopee->produk = ServiceShopee::getItem(trim($token->shopee), trim($sql_data->product_id_shopee));
                $responsStatus->shopee->produk = (empty(json_decode($responsData->shopee->produk)->error)) ? 1 : 0;
                // return json_decode($responsData->shopee->produk);
            } elseif($sql_data->product_id_shopee == 0){
                $responsData->tokopedia->produk = ServiceTokopedia::GetProductInfoByProductId(trim($token->tokopedia), trim($sql_data->product_id_tokopedia));
                $responsStatus->tokopedia->produk = (empty(json_decode($responsData->tokopedia->produk)->header->error_code)) ? 1 : 0;
            }


            $data_part = (object)
                [
                    'marketplace'   => '',
                    'name'          => '',
                    'description'   => '',
                    'category_name' => '',
                    'brand_name'    => '',
                    'price'         => '',
                    'weight'        => '',
                    'dimension'     => (object)[
                        'length'    => '',
                        'width'     => '',
                        'height'    => '',
                    ],
                    'condition'     => '',
                    'images'        => [],
                    'logistic'      => [],
            ];

            // ! JIKA DATA TOKOPEDIA KOSONG MAKA AMBIL DATA SHOPEE
            if($responsStatus->shopee->produk == 1 && $responsStatus->tokopedia->produk == 0) {
                $dataShopee                 = json_decode($responsData->shopee->produk)->response;
                $data_part->marketplace     = 'shopee';

                $data_part->part_number     = $request->get('part_number');
                $data_part->name            = $dataShopee->item_list[0]->item_name;
                $data_part->description     = $dataShopee->item_list[0]->description;
                $data_part->category_id     = $dataShopee->item_list[0]->category_id;
                $data_part->category_name   = collect(json_decode($responsData->shopee->kategori)->response->category_list)->where('category_id', $dataShopee->item_list[0]->category_id)->first()->display_category_name;
                $data_part->price           = (empty($dataShopee->item_list[0]->price_info[0]->original_price)) ? 0 : (double)$dataShopee->item_list[0]->price_info[0]->original_price;
                $data_part->weight          = (empty($dataShopee->item_list[0]->weight)) ? 0 : (double)$dataShopee->item_list[0]->weight;
                if(!empty($dataShopee->item_list[0]->dimension)){
                    $data_part->dimension->length   = (empty($dataShopee->item_list[0]->dimension->length)) ? null : $dataShopee->item_list[0]->dimension->package_length;
                    $data_part->dimension->width    = (empty($dataShopee->item_list[0]->dimension->width)) ? null : $dataShopee->item_list[0]->dimension->package_width;
                    $data_part->dimension->height   = (empty($dataShopee->item_list[0]->dimension->height)) ? null : $dataShopee->item_list[0]->dimension->package_height;
                }
                $data_part->condition       = ($dataShopee->item_list[0]->condition == 'NEW') ? 'Baru' : 'Bekas';
                $data_part->images          = (empty($dataShopee->item_list[0]->image->image_url_list)) ? [] : $dataShopee->item_list[0]->image->image_url_list;
                $data_part->status          = $dataShopee->item_list[0]->item_status;
                $data_part->logistic        = collect($dataShopee->item_list[0]->logistic_info)->pluck('logistic_id')->toArray();
                $data_part->brand_name      = $dataShopee->item_list[0]->brand->original_brand_name;

                // ! AMBIL DATA SHOPEE KOSONG MAKA AMBIL DATA TOKOPEDIA
            } elseif ($responsStatus->shopee->produk == 0 && $responsStatus->tokopedia->produk == 1) {
                $dataTokopedia              = json_decode($responsData->tokopedia->produk)->data[0];
                $data_part->marketplace     = 'tokopedia';

                $data_part->part_number     = $request->get('part_number');
                $data_part->name            = $dataTokopedia->basic->name;
                $data_part->description     = $dataTokopedia->basic->shortDesc;
                $data_part->category_id     = collect($dataTokopedia->categoryTree)->last()->id;
                $data_part->category_name   = collect($dataTokopedia->categoryTree)->last()->name;
                $data_part->price           = (empty($dataTokopedia->price->value)) ? 0 : $dataTokopedia->price->value;
                $data_part->weight          = (empty($dataTokopedia->weight->value)) ? 0 : $dataTokopedia->weight->value;
                if(!empty($dataTokopedia->volume)){
                    $data_part->dimension->length   = (empty($dataTokopedia->volume->length)) ? null : $dataTokopedia->volume->length;
                    $data_part->dimension->width    = (empty($dataTokopedia->volume->width)) ? null : $dataTokopedia->volume->width;
                    $data_part->dimension->height   = (empty($dataTokopedia->volume->height)) ? null : $dataTokopedia->volume->height;
                }
                $data_part->condition       = ($dataTokopedia->basic->condition == 1)?'Baru':'Bekas';
                $data_part->status          = $dataTokopedia->basic->status;
                $data_part->images          = (collect($dataTokopedia->pictures)->count()==0) ? [] : collect($dataTokopedia->pictures)->pluck('OriginalURL')->toArray();

            } else {
                return Response::responseWarning('Pastikan memilih product yang setidaknya ada pada salah satu marketplace');
            }

            $logisticShopee = collect(json_decode(ServiceShopee::GetChannelList($token->shopee))->response->logistics_channel_list)
            ->where('enabled', true)
            ->sortBy('logistics_channel_id')
            ->groupBy('mask_channel_id');

            $logisticShopee['0']->map(function($item) use ($logisticShopee){
                foreach($logisticShopee as $key => $value){
                    if($key == $item->logistics_channel_id){
                        $item->detail = $value;
                    }
                }
            });

            $etalaseTokopedia = json_decode(ServiceTokopedia::GetShowcase($token->tokopedia))->data->etalase;

            // ! FORMAT DATA RETURN
            $data_return = (object)[
                'status' => 1,
                'data' => $data_part,
                'kategori' => (object)[
                    'shopee' => $kategoriShopee,
                    'tokopedia' => $kategoriTokopedia
                ],
                'logistic' => (object)[
                    'shopee' => $logisticShopee['0'],
                    'tokopedia' => ''
                ],
                'etalase' => (object)[
                    'shopee' => '',
                    'tokopedia' => $etalaseTokopedia
                ],
            ];

            // ! PANGGIL DATA RETURN
            return Response::responseSuccess('success', $data_return);

        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function AddProduct(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'companyid' => 'required',
                'image'     => 'required',
                'nama'      => 'required|max:70',
                'deskripsi' => 'required|min:20|max:2000',
                'harga'     => 'required',
                'stok'      => 'required|numeric|min:1',
                'min_order' => 'required',
                'berat'     => 'required',
                'ukuran'    => 'required',
                'sku'       => 'required',
                'kondisi'   => 'required',
                'kategori'  => 'required',
                'status'    => 'required',
                'logistic'  => 'required',
            ],[
                'companyid.required'    => 'Company ID tidak boleh kosong',
                'image.required'        => 'Gambar produk tidak boleh kosong',
                'nama.required'         => 'Nama produk tidak boleh kosong',
                'nama.max'              => 'Nama produk maksimal 70 karakter',
                'deskripsi.min'         => 'Deskripsi produk minimal 20 karakter',
                'deskripsi.max'         => 'Deskripsi produk maksimal 2000 karakter',
                'harga.required'        => 'Harga produk tidak boleh kosong',
                'stok.required'         => 'Stok produk tidak boleh kosong',
                'stok.numeric'          => 'Stok produk harus berupa angka',
                'stok.min'              => 'Stok produk minimal 1',
                'min_order.required'    => 'Minimal order produk tidak boleh kosong',
                'berat.required'        => 'Berat produk tidak boleh kosong',
                'ukuran.required'       => 'Ukuran produk tidak boleh kosong',
                'sku.required'          => 'SKU produk tidak boleh kosong',
                'kondisi.required'      => 'Kondisi produk tidak boleh kosong',
                'kategori.required'     => 'Kategori produk tidak boleh kosong',
                'status.required'       => 'Status produk tidak boleh kosong',
                'logistic.required'     => 'Logistic produk tidak boleh kosong',
            ]);

            if($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            $sql_data = DB::table('part')->lock('with (nolock)')
                        ->selectRaw("isnull(part.kd_part, '') as part_number,
                                    isnull(part.shopee_id, 0) as product_id_shopee,
                                    isnull(part.tokopedia_id, 0) as product_id_tokopedia")
                        ->where('part.kd_part', $request->get('sku'))
                        ->where('part.companyid', $request->get('companyid'))
                        ->first();

            if($sql_data == null){
                return Response::responseWarning("Maaf SKU yang ada masukkan tidak ada pada Part Number Internal");
            }

            // ! cek data apa yang digunakan
            $data_add = new \stdClass();
            if($sql_data->product_id_shopee == 0 && $sql_data->product_id_tokopedia != 0){
                $data_add->ket          = 'tokopedia';
                $data_add->product_id   = $sql_data->product_id_tokopedia;
            } else if ($sql_data->product_id_shopee != 0 && $sql_data->product_id_tokopedia == 0) {
                $data_add->ket          = 'shopee';
                $data_add->product_id   = $sql_data->product_id_shopee;
            } else {
                return Response::responseWarning("Maaf Part Number belum ada pada shopee dan tokopedia");
            }

            //! cek koneksi
            $authorization  = $request->header('Authorization');
            $token_array    = explode(" ", $authorization);
            $auth_token     = trim($token_array[1]);

            $sql = DB::table('user_api_office')->lock('with (nolock)')
                    ->where('office_token', $auth_token)
                    ->first();

            if(empty($sql->user_id)) {
                return Response::responseWarning('Token Suma Office tidak ditemukan, lakukan logout kemudian login kembali');
            }

            $token = new \stdClass();
            $token->shopee      = $sql->shopee_token;
            $token->tokopedia   = $sql->tokopedia_token;

            // ! jika data yang suda ada adalah shopee
            if($data_add->ket == 'shopee'){

                // ! merubah format tokopedia
                $image_tokped = [];
                foreach($request->get('image') as $key => $value){
                    $image_tokped[$key] = (object)['file_path' => $value];
                }

                // ! Menyiapkan data sebelum dikirim ke api
                $kirim_data =
                [
                (object)[
                        "Name"          => (string)$request->get('nama'),
                        "condition"     => (string)(($request->get('kondisi') == 1)? 'NEW' : 'USED'),
                        "Description"   => (string)$request->get('deskripsi'),
                        "sku"           => (string)$request->get('sku'),
                        "price"         => (int)$request->get('harga'),
                        "status"        => (string)(($request->get('status') == 1)? 'LIMITED' : 'EMPTY'),
                        "stock"         => (int)$request->get('stok'),
                        "min_order"     => (int)$request->get('min_order'),
                        "category_id"   => (int)$request->get('kategori'),
                        "price_currency"=> "IDR",
                        "weight"        => (float)$request->get('berat'),
                        "weight_unit"   => "GR",
                        "pictures"      => $image_tokped,
                    ]
                ];
                // ! Jika ukuran tidak kosong
                if(!empty(((object)$request->get('ukuran'))->tinggi) && !empty(((object)$request->get('ukuran'))->panjang) && !empty(((object)$request->get('ukuran'))->lebar)){
                    $kirim_data[0]->dimension = (object)[
                        "height"    => (float)((object)$request->get('ukuran'))->tinggi,
                        "length"    => (float)((object)$request->get('ukuran'))->panjang,
                        "width"     => (float)((object)$request->get('ukuran'))->lebar
                    ];
                }

                // ! Jika logistic tidak kosong
                if(!empty($request->get('etalase'))){
                    $kirim_data[0]->etalase = (object)[
                        "id" => (int)$request->get('etalase')
                    ];
                }

                // TODO : Tambah produk ke tokopedia
                // ! Proses tambah produk ke tokopedia
                $requestServerTokopedia = json_decode(ServiceTokopedia::createProducts($token->tokopedia, $kirim_data));

                if(!empty($requestServerTokopedia->data->fail_data) && $requestServerTokopedia->data->fail_data == 1){
                    return Response::responseWarning($requestServerTokopedia->data->failed_rows_data);
                }

                // TODO : Update tokopedia_id pada part internal
                // ! Proses update tokopedia_id ke part Internal
                try {
                    DB::transaction(function () use ($request, $requestServerTokopedia) {
                        DB::table('part')->lock('with (nolock)')
                            ->where('kd_part', $request->get('sku'))
                            ->where('CompanyId', $request->get('companyid'))
                            ->whereNull('tokopedia_id')
                            ->update([
                                'tokopedia_id' => $requestServerTokopedia->data->success_rows_data[0]->product_id
                        ]);
                    });

                    return Response::responseSuccess('<b>Berhasil</b> menambahkan produk ke '.(($data_add->ket == 'tokopedia')? '<b>Shopee</b>' : '<b>Tokopedia</b>'));

                } catch (\Exception $exception) {
                    return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), 'Gagal update tokopedia_id, tokopedia id yang akan diupdate = '. $requestServerTokopedia->data->success_rows_data[0]->product_id . 'pada part number = '. $request->get('sku'), $request->get('companyid'));
                }
            }

            // ! jika data yang sudah ada adalah tokopedia
            if ($data_add->ket == 'tokopedia'){
                // TODO : upload gambar ke shopee
                // ! Proses upload gambar ke shopee
                $image_upload_id = [];
                foreach($request->get('image') as $key => $value){
                    $requestServerShopee =  json_decode(ServiceShopee::uploadImage($value));
                    if(empty($requestServerShopee->error)){
                        $image_upload_id[] = $requestServerShopee->response->image_info->image_id;
                    }
                }
                if(collect($image_upload_id)->count() == 0){
                    return Response::responseWarning("Maaf, terjadi kesalahan saat upload gambar, mohon ulanggi proses tambah produk");
                }

                // ! Pesan warning digunakan untuk memberitahu user bahwa tidak semua gambar berhasil di upload
                $pesan_warning = null;
                if(collect($request->get('image'))->count() != collect($image_upload_id)->count()){
                    $pesan_warning = 'Tidak semua gambar berhasil di upload, maka akan terdapat gambar yang tidak lengkap, <b>solusi</b> : tambah gambar manual melalui '.(($data_add->ket == 'tokopedia')? '<b>Shopee</b>' : '<b>Tokopedia</b>');
                }

                // ! Menyiapkan data sebelum dikirim ke api
                $kirim_data =
                (object)[
                            "description"   => (string)$request->get('deskripsi'),
                            "item_name"     => (string)$request->get('nama'),
                            "category_id"   => (int)$request->get('kategori'),
                            "brand"         => (object)[
                                "brand_id"              => (int)$request->get('merek'),
                            ],
                            "logistic_info" => collect($request->get('logistic'))->map(function($item){
                                return (object)[
                                    "logistic_id"   => (int)((object)$item)->logistic_id,
                                    "enabled"       => (bool)((object)$item)->enabled,
                                ];
                            })->values()->toArray(),
                            "weight"        => (float)$request->get('berat') / 1000,
                            "item_status"   => (string)(($request->get('status')==1) ? 'NORMAL' : 'UNLIST'),
                            "image"         => (object)[
                                "image_id_list" => $image_upload_id
                            ],
                            "item_sku"          => (string)$request->get('sku'),
                            "condition"         => (string)($request->get('kondisi') == 1 ? 'NEW' : 'USED'),
                            "original_price"    => (float)$request->get('harga'),
                            "seller_stock"      =>  [
                                (object)[
                                    "stock"     =>  (int)$request->get('stok'),
                                ]
                            ],
                        ];

                // ! Jika ukuran tidak kosong
                if(!empty(((object)$request->get('ukuran'))->tinggi) && !empty(((object)$request->get('ukuran'))->panjang) && !empty(((object)$request->get('ukuran'))->lebar)){
                    $kirim_data->dimension = (object)[
                        "package_height"    => (int)((object)$request->get('ukuran'))->tinggi,
                        "package_length"    => (int)((object)$request->get('ukuran'))->panjang,
                        "package_width"     => (int)((object)$request->get('ukuran'))->lebar,
                    ];
                }

                // TODO : Tambah produk ke shopee
                // ! Proses tambah produk ke shopee
                $requestServerShopee = json_decode(ServiceShopee::addItem($token->shopee, $kirim_data));

                if(!empty($requestServerShopee->error)){
                    return Response::responseWarning($requestServerShopee->error. ', ' .$requestServerShopee->message);
                }

                // TODO : Update shopee_id pada part internal
                // ! Proses update shopee_id ke part Internal
                try {
                    DB::transaction(function () use ($request, $requestServerShopee) {
                        DB::table('part')->lock('with (nolock)')
                            ->where('kd_part', $request->get('sku'))
                            ->where('CompanyId', $request->get('companyid'))
                            ->whereNull('shopee_id')
                            ->update([
                                'shopee_id' => $requestServerShopee->response->item_id
                        ]);
                    });

                    return Response::responseSuccess('<b>Berhasil</b> menambahkan produk ke '.(($data_add->ket == 'tokopedia')? '<b>Shopee</b>' : '<b>Tokopedia</b>').(($pesan_warning==null)?'':', <b>Catatan</b> : '.$pesan_warning));

                } catch (\Exception $exception) {
                    return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), 'Gagal update shopee_id, shopee id yang akan diupdate = '. $requestServerShopee->response->item_id . 'pada part number = '. $request->get('sku'), $request->get('companyid'));
                }
            }

        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
