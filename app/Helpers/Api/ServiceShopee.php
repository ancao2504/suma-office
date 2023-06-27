<?php

namespace App\Helpers\Api;
use Illuminate\Support\Facades\Http;

class ServiceShopee {

    public static function AuthPartner() {
        $path = "/api/v2/shop/auth_partner";
        $redirectUrl = "https://www.baidu.com/";
        $timestamp = time();
        $baseString = (int)config('constants.api.shopee.partner_id').$path.$timestamp;
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $results = config('constants.api.shopee.url.host').$path.'?partner_id='.(int)config('constants.api.shopee.partner_id').
                    '&timestamp='.$timestamp.'&sign='.$sign.'&redirect='.$redirectUrl;
        return $results;
    }

    public static function GetAccessToken($access_code) {
        $path = "/api/v2/auth/token/get";
        $timestamp = time();
        $baseString = (int)config('constants.api.shopee.partner_id').$path.$timestamp;
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $parameter = '?partner_id='.(int)config('constants.api.shopee.partner_id').
                    '&timestamp='.$timestamp.'&sign='.$sign;
        $response = Http::post(config('constants.api.shopee.url.host').$path.$parameter, [
                        'code'          => trim($access_code),
                        'shop_id'       => (int)config('constants.api.shopee.shop_id'),
                        'partner_id'    => (int)config('constants.api.shopee.partner_id'),
                    ])
                    ->body();
        return $response;
    }

    public static function RefreshToken($refresh_token) {
        $path = "/api/v2/auth/access_token/get";
        $timestamp = time();
        $baseString = (int)config('constants.api.shopee.partner_id').$path.$timestamp;
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $parameter = '?partner_id='.(int)config('constants.api.shopee.partner_id').
                    '&timestamp='.$timestamp.'&sign='.$sign;

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->post(config('constants.api.shopee.url.host').$path.$parameter, [
                            'refresh_token' => trim($refresh_token),
                            'partner_id'    => (int)config('constants.api.shopee.partner_id'),
                            'shop_id'       => (int)config('constants.api.shopee.shop_id')
                    ])
                    ->body();
        return $response;
    }

    public static function AuthShop() {
        $path = "/api/v2/shop/auth_partner";
        $redirectUrl = "https://suma-honda.id/";
        $partnerId = config('constants.api.shopee.partner_id');
        $partnerkey = config('constants.api.shopee.partner_key');
        $timest = time();
        $baseString = $partnerId . $path . $timest;
        $sign = hash_hmac('sha256', $baseString, $partnerkey);
        $url = config('constants.api.shopee.url.host').$path;

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->get($url, [
                        'partner_id'    => $partnerId,
                        'timestamp'     => $timest,
                        'sign'          => $sign,
                        'redirect'      => $redirectUrl
                    ])->body();
        return $response;
    }

    public static function GetShopInfo($access_token) {
        $path = "/api/v2/shop/get_shop_info";
        $timestamp = time();
        $baseString = config('constants.api.shopee.partner_id').$path.$timestamp.$access_token.(int)config('constants.api.shopee.shop_id');
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $url = config('constants.api.shopee.url.host').$path;
        $parameter = [
            'partner_id'    => (int)config('constants.api.shopee.partner_id'),
            'timestamp'     => $timestamp,
            'sign'          => $sign,
            'access_token'  => $access_token,
            'shop_id'       => (int)config('constants.api.shopee.shop_id')
        ];
        $response = Http::withHeaders(['Content-Type' => 'application/json'])->get($url, $parameter)->body();
        return $response;
    }

    public static function GetChannelList($access_token) {
        $path = "/api/v2/logistics/get_channel_list";
        $timestamp = time();
        $baseString = config('constants.api.shopee.partner_id').$path.$timestamp.$access_token.(int)config('constants.api.shopee.shop_id');
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $response = Http::get(config('constants.api.shopee.url.host').$path, [
                        'partner_id'    => (int)config('constants.api.shopee.partner_id'),
                        'timestamp'     => $timestamp,
                        'sign'          => $sign,
                        'access_token'  => $access_token,
                        'shop_id'       => (int)config('constants.api.shopee.shop_id')
                    ])
                    ->body();
        return $response;
    }

    public static function GetItem($access_token, $product_id) {
        $path = "/api/v2/product/get_item_base_info";
        $timest = time();
        $baseString = config('constants.api.shopee.partner_id') . $path . $timest.$access_token.config('constants.api.shopee.shop_id');
        $url = config('constants.api.shopee.url.host').$path;
        $parameter = [
            'item_id_list'  => $product_id,
            'access_token'  => $access_token,
            'partner_id'    => (int)config('constants.api.shopee.partner_id'),
            'shop_id'       => (int)config('constants.api.shopee.shop_id'),
            'sign'          => hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key')),
            'timestamp'     => $timest,
        ];
        $response = Http::withHeaders(['Content-Type' => 'application/json'])->get($url, $parameter)->body();
        return $response;
    }

    public static function UpdateStockPerPart($access_token, $product_id, $stock) {
        $path = "/api/v2/product/update_stock";
        $timest = time();
        $baseString = config('constants.api.shopee.partner_id').$path.$timest.$access_token.config('constants.api.shopee.shop_id');
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $shop_id = config('constants.api.shopee.shop_id');
        $partner_id = config('constants.api.shopee.partner_id');
        $url = config('constants.api.shopee.url.host').$path."?partner_id=".$partner_id."&timestamp=".$timest."&access_token=".$access_token."&shop_id=".$shop_id."&sign=".$sign;
        $data  = [
            "item_id" => (int)$product_id,
            "stock_list" => [
                [
                    "seller_stock" => [
                        [
                            "stock" => (int)$stock,
                        ]
                    ],
                ],
            ],
        ];
        $response = Http::withHeaders(['Content-Type' => 'application/json'])->post($url, $data)->body();
        return $response;
    }

    public static function ProductUpdatePrice($access_token, $data_update) {
        $path = "/api/v2/product/update_price";
        $timest = time();
        $baseString = config('constants.api.shopee.partner_id').$path.$timest.$access_token.config('constants.api.shopee.shop_id');
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $url = config('constants.api.shopee.url.host').$path."?partner_id=".config('constants.api.shopee.partner_id')."&timestamp=".$timest.
                "&access_token=".$access_token."&shop_id=".config('constants.api.shopee.shop_id')."&sign=".$sign;
        $data = [
            "item_id" => (int)$data_update->product_id,
            "price_list" => [
                [
                    "original_price" => (float)$data_update->new_price,
                ]
            ]
        ];
        $response = Http::withHeaders(["Content-Type" => "application/json"])->post($url, $data)->body();
        return $response;
    }

    public static function ShipOrder($access_token, $nomor_invoice, $address_id, $pickup_time_id, $tracking_number) {
        $path = "/api/v2/logistics/ship_order";
        $timestamp = time();
        $baseString = config('constants.api.shopee.partner_id').$path.$timestamp.$access_token.(int)config('constants.api.shopee.shop_id');
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $url = config('constants.api.shopee.url.host').$path."?partner_id=".config('constants.api.shopee.partner_id')."&timestamp=".$timestamp.
                "&access_token=".$access_token."&shop_id=".config('constants.api.shopee.shop_id')."&sign=".$sign;
        $data = [
            'order_sn'              => strtoupper(trim($nomor_invoice)),
            'pickup'                => (object)[
                'address_id'        => (int)$address_id,
                'pickup_time_id'    => $pickup_time_id,
                'tracking_number'   => $tracking_number,
            ]

        ];
        $response = Http::withHeaders(["Content-Type" => "application/json"])->post($url, $data)->body();
        return $response;
    }

    public static function GetOrderList($access_token, $field, $start_date, $end_date, $page_size, $cursor, $status) {
        $path = "/api/v2/order/get_order_list";
        $timestamp = time();
        $baseString = config('constants.api.shopee.partner_id').$path.$timestamp.$access_token.(int)config('constants.api.shopee.shop_id');
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $url = config('constants.api.shopee.url.host').$path."?partner_id=".config('constants.api.shopee.partner_id')."&timestamp=".$timestamp.
                "&access_token=".$access_token."&shop_id=".config('constants.api.shopee.shop_id')."&sign=".$sign;

        $parameter = "&time_range_field=".$field."&time_from=".$start_date."&time_to=".$end_date."&page_size=".$page_size.
                    ((empty($cursor) || $cursor == '') ? '' : "&cursor=".$cursor).
                    ((empty($status) || $status == '') ? '' : "&order_status=".$status);

        $response = Http::withHeaders(["Content-Type" => "application/json"])
                        ->get($url.$parameter)
                        ->body();
        return $response;
    }

    public static function GetShipmentList($access_token, $page_size, $cursor) {
        $path = "/api/v2/order/get_shipment_list";
        $timestamp = time();
        $baseString = config('constants.api.shopee.partner_id').$path.$timestamp.$access_token.(int)config('constants.api.shopee.shop_id');
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $url = config('constants.api.shopee.url.host').$path."?partner_id=".config('constants.api.shopee.partner_id')."&timestamp=".$timestamp.
                "&access_token=".$access_token."&shop_id=".config('constants.api.shopee.shop_id')."&sign=".$sign;

        $parameter = "&page_size=".$page_size.((empty($cursor) || $cursor == '') ? '' : "&cursor=".$cursor);

        $response = Http::withHeaders(["Content-Type" => "application/json"])
                        ->get($url.$parameter)
                        ->body();
        return $response;
    }

    public static function GetOrderDetail($access_token, $list_invoice) {
        $path = "/api/v2/order/get_order_detail";
        $timestamp = time();
        $baseString = config('constants.api.shopee.partner_id').$path.$timestamp.$access_token.(int)config('constants.api.shopee.shop_id');
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $url = config('constants.api.shopee.url.host').$path."?partner_id=".config('constants.api.shopee.partner_id')."&timestamp=".$timestamp.
                "&access_token=".$access_token."&shop_id=".config('constants.api.shopee.shop_id')."&sign=".$sign;

        $response_optional_fields = 'buyer_user_id,buyer_username,estimated_shipping_fee,recipient_address,actual_shipping_fee,';
        $response_optional_fields .= 'recipient_address,note,note_update_time,actual_shipping_fee_confirmed,';
        $response_optional_fields .= 'shipping_carrier,payment_method,total_amount,invoice_data,checkout_shipping_carrier,';
        $response_optional_fields .= 'reverse_shipping_fee,package_list,item_list,pay_time,logistics_channel_id,tracking_info,logistics_channel_list';

        $parameter = "&order_sn_list=".$list_invoice."&response_optional_fields=".$response_optional_fields;

        $response = Http::withHeaders(["Content-Type" => "application/json"])
                    ->get($url.$parameter)
                    ->body();
        return $response;
    }

    public static function GetTrackingNumber($access_token, $nomor_invoice) {
        $path = "/api/v2/logistics/get_tracking_number";
        $timestamp = time();
        $baseString = config('constants.api.shopee.partner_id').$path.$timestamp.$access_token.(int)config('constants.api.shopee.shop_id');
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $url = config('constants.api.shopee.url.host').$path."?partner_id=".config('constants.api.shopee.partner_id')."&timestamp=".$timestamp.
                "&access_token=".$access_token."&shop_id=".config('constants.api.shopee.shop_id')."&sign=".$sign;
        $parameter = "&order_sn=".$nomor_invoice."&response_optional_fields=first_mile_tracking_number";

        $response = Http::get($url.$parameter)
                    ->body();
        return $response;
    }

    public static function GetShippingParameter($access_token, $nomor_invoice) {
        $path = "/api/v2/logistics/get_shipping_parameter";
        $timestamp = time();
        $baseString = config('constants.api.shopee.partner_id').$path.$timestamp.$access_token.(int)config('constants.api.shopee.shop_id');
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $url = config('constants.api.shopee.url.host').$path;
        $parameter = [
            'partner_id'    => (int)config('constants.api.shopee.partner_id'),
            'timestamp'     => $timestamp,
            'sign'          => $sign,
            'access_token'  => $access_token,
            'shop_id'       => (int)config('constants.api.shopee.shop_id'),
            'order_sn'      => strtoupper(trim($nomor_invoice))
        ];
        $response = Http::withHeaders(['Content-Type' => 'application/json'])->get($url, $parameter)->body();
        return $response;
    }

    public static function GetShippingDocumentParameter($access_token, $nomor_invoice) {
        $path = "/api/v2/logistics/get_shipping_document_parameter";
        $timestamp = time();
        $baseString = config('constants.api.shopee.partner_id').$path.$timestamp.$access_token.(int)config('constants.api.shopee.shop_id');
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $url = config('constants.api.shopee.url.host').$path."?partner_id=".config('constants.api.shopee.partner_id')."&timestamp=".$timestamp.
                "&access_token=".$access_token."&shop_id=".config('constants.api.shopee.shop_id')."&sign=".$sign;
        $parameter = [
            'order_list'    => [
                [
                    'order_sn'  => $nomor_invoice
                ]
            ]
        ];
        $response = Http::withHeaders(['Content-Type' => 'application/json'])->post($url, $parameter)->body();
        return $response;
    }

    public static function CreateShippingDocument($access_token, $nomor_invoice, $shipping_document_type, $tracking_number) {
        $path = "/api/v2/logistics/create_shipping_document";
        $timestamp = time();
        $baseString = config('constants.api.shopee.partner_id').$path.$timestamp.$access_token.(int)config('constants.api.shopee.shop_id');
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $url = config('constants.api.shopee.url.host').$path."?partner_id=".config('constants.api.shopee.partner_id')."&timestamp=".$timestamp.
                "&access_token=".$access_token."&shop_id=".config('constants.api.shopee.shop_id')."&sign=".$sign;

        $parameter = [
            'order_list'    => [
                [
                    'order_sn'              => $nomor_invoice,
                    'shipping_document_type' => $shipping_document_type,
                    'tracking_number'       => $tracking_number
                ]
            ]
        ];
        $response = Http::withHeaders(["Content-Type" => "application/json"])
                    ->post($url, $parameter)
                    ->body();
        return $response;
    }

    public static function DownloadShippingDocument($access_token, $nomor_invoice, $shipping_document_type) {
        $path = "/api/v2/logistics/download_shipping_document";
        $timestamp = time();
        $baseString = config('constants.api.shopee.partner_id').$path.$timestamp.$access_token.(int)config('constants.api.shopee.shop_id');
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $url = config('constants.api.shopee.url.host').$path."?partner_id=".config('constants.api.shopee.partner_id')."&timestamp=".$timestamp.
                "&access_token=".$access_token."&shop_id=".config('constants.api.shopee.shop_id')."&sign=".$sign;

        $parameter = [
            'shipping_document_type'    => $shipping_document_type,
            'order_list'                => [
                [
                    'order_sn'              => $nomor_invoice
                ]
            ]
        ];

        $response = Http::withHeaders(["Content-Type" => "application/json"])
                    ->post($url, $parameter)
                    ->body();

        return (object)[
            'response'  => $response,
            'url'       => $url,
            'parameter' => $parameter
        ];
    }

    public static function GetShippingDocumentResult($access_token, $nomor_invoice) {
        $path = "/api/v2/logistics/get_shipping_document_result";
        $timestamp = time();
        $baseString = config('constants.api.shopee.partner_id').$path.$timestamp.$access_token.(int)config('constants.api.shopee.shop_id');
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $url = config('constants.api.shopee.url.host').$path."?partner_id=".config('constants.api.shopee.partner_id')."&timestamp=".$timestamp.
                "&access_token=".$access_token."&shop_id=".config('constants.api.shopee.shop_id')."&sign=".$sign;

        $parameter = [
            'order_list'    => [
                [
                    'order_sn'  => $nomor_invoice
                ]
            ]
        ];

        $response = Http::withHeaders(["Content-Type" => "application/json"])
                    ->post($url, $parameter)
                    ->body();

        return $response;
    }

    public static function GetCategory($access_token){
        $path = "/api/v2/product/get_category";
        $timestamp = time();
        $baseString = config('constants.api.shopee.partner_id').$path.$timestamp.$access_token.(int)config('constants.api.shopee.shop_id');
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $url = config('constants.api.shopee.url.host').$path."?partner_id=".config('constants.api.shopee.partner_id')."&timestamp=".$timestamp."&access_token=".$access_token."&shop_id=".config('constants.api.shopee.shop_id')."&sign=".$sign."&language=ID";

        $response = Http::withHeaders(["Content-Type" => "application/json"])
                    ->get($url)
                    ->body();

        return $response;
    }

    public static function GetWalletTransactionList($access_token, $page_no, $page_size, $create_time_from, $create_time_to){
        $path = "/api/v2/payment/get_wallet_transaction_list";
        $timestamp = time();
        $baseString = config('constants.api.shopee.partner_id').$path.$timestamp.$access_token.(int)config('constants.api.shopee.shop_id');
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $url = config('constants.api.shopee.url.host').$path."?partner_id=".config('constants.api.shopee.partner_id')."&timestamp=".$timestamp."&access_token=".$access_token."&shop_id=".config('constants.api.shopee.shop_id')."&sign=".$sign;
        $parameter = "&page_no=".$page_no."&page_size=".$page_size."&create_time_from=".$create_time_from."&create_time_to=".$create_time_to;

        $response = Http::withHeaders(["Content-Type" => "application/json"])
                    ->get($url.$parameter)
                    ->body();

        return $response;
    }

    public static function getBrandList($access_token, $offset, $category_id){
        $path = "/api/v2/product/get_brand_list";
        $timestamp = time();
        $baseString = config('constants.api.shopee.partner_id').$path.$timestamp.$access_token.(int)config('constants.api.shopee.shop_id');
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $url = config('constants.api.shopee.url.host').$path;

        $response = Http::withHeaders(["Content-Type" => "application/json"])
                    ->get($url,[
                        'offset'        => $offset,
                        'page_size'     => 20,
                        'category_id'   => $category_id,
                        'status'        => 1,
                        'language'      => 'ID',

                        'partner_id'    => config('constants.api.shopee.partner_id'),
                        'timestamp'     => $timestamp,
                        'access_token'  => $access_token,
                        'shop_id'       => config('constants.api.shopee.shop_id'),
                        'sign'          => $sign,
                    ])
                    ->body();

        return $response;
    }

    public static function getAttributes($access_token, $category_id){
        $path = "/api/v2/product/get_attributes";
        $timestamp = time();
        $baseString = config('constants.api.shopee.partner_id').$path.$timestamp.$access_token.(int)config('constants.api.shopee.shop_id');
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $url = config('constants.api.shopee.url.host').$path;

        $response = Http::withHeaders(["Content-Type" => "application/json"])
                    ->get($url,[
                        'category_id'   => $category_id,
                        'language'      => 'ID',

                        'partner_id'    => config('constants.api.shopee.partner_id'),
                        'timestamp'     => $timestamp,
                        'access_token'  => $access_token,
                        'shop_id'       => config('constants.api.shopee.shop_id'),
                        'sign'          => $sign,
                    ])
                    ->body();

        return $response;
    }

    public static function uploadImage($data) {
        $path = "/api/v2/media_space/upload_image";
        $timestamp = time();
        $baseString = config('constants.api.shopee.partner_id').$path.$timestamp;
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $url = config('constants.api.shopee.url.host').$path."?partner_id=".config('constants.api.shopee.partner_id')."&timestamp=".$timestamp."&sign=".$sign;

        $response = Http::attach('image', file_get_contents($data), basename($data), [
                'Content-Type' => 'multipart/form-data'
            ])
            ->post($url)
            ->body();

        return $response;
    }

    public static function GetEscrowList($access_token, $page_no, $page_size, $release_time_from, $release_time_to){
        $path = "/api/v2/payment/get_escrow_list";
        $timestamp = time();
        $baseString = config('constants.api.shopee.partner_id').$path.$timestamp.$access_token.(int)config('constants.api.shopee.shop_id');
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $url = config('constants.api.shopee.url.host').$path."?partner_id=".config('constants.api.shopee.partner_id')."&timestamp=".$timestamp."&access_token=".$access_token."&shop_id=".config('constants.api.shopee.shop_id')."&sign=".$sign;
        $parameter = "&page_no=".$page_no."&page_size=".$page_size."&release_time_from=".$release_time_from."&release_time_to=".$release_time_to;

        $response = Http::withHeaders(["Content-Type" => "application/json"])
                    ->get($url.$parameter)
                    ->body();

        return $response;
    }

    public static function addItem($access_token, $data) {
        $path = "/api/v2/product/add_item";
        $timestamp = time();
        $baseString = config('constants.api.shopee.partner_id').$path.$timestamp.$access_token.config('constants.api.shopee.shop_id');
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $url = config('constants.api.shopee.url.host').$path.
        "?partner_id=".(int)config('constants.api.shopee.partner_id').
        "&timestamp=".$timestamp.
        "&access_token=".(string)$access_token.
        "&shop_id=".(int)config('constants.api.shopee.shop_id').
        "&sign=".(string)$sign;

        $response = Http::withHeaders(["Content-Type" => "application/json"])
                    ->post($url, (array)$data)
                    ->body();

        return $response;
    }

    public static function GetEscrowDetail($access_token, $nomor_invoice){
        $path = "/api/v2/payment/get_escrow_detail";
        $timestamp = time();
        $baseString = config('constants.api.shopee.partner_id').$path.$timestamp.$access_token.(int)config('constants.api.shopee.shop_id');
        $sign = hash_hmac('sha256', $baseString, config('constants.api.shopee.partner_key'));
        $url = config('constants.api.shopee.url.host').$path."?partner_id=".config('constants.api.shopee.partner_id')."&timestamp=".$timestamp."&access_token=".$access_token."&shop_id=".config('constants.api.shopee.shop_id')."&sign=".$sign;

        $parameter = '&order_sn='.$nomor_invoice;

        $response = Http::withHeaders(["Content-Type" => "application/json"])
                    ->get($url.$parameter)
                    ->body();
        return $response;
    }
}
