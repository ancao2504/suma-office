<?php

namespace App\Helpers\Api;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

class ServiceTiktok
{
    public static function GenerateSHA256($path, $queries) {
        $keys = array_keys($queries);
        sort($keys);

        $input = $path;
        foreach ($keys as $key) {
            $input .= $key . $queries[$key];
        }

        $input = config('constants.api.tiktok.app_secret').$input.config('constants.api.tiktok.app_secret');
        $signature = hash_hmac('sha256', $input, config('constants.api.tiktok.app_secret'));

        return $signature;
    }

    public static function GetRefreshToken($refresh_token) {
        $path = '/api/v2/token/refresh';
        $parameter = [
            'app_key'       => config('constants.api.tiktok.app_key'),
            'app_secret'    => config('constants.api.tiktok.app_secret'),
            'refresh_token' => $refresh_token,
            'grant_type'    => 'refresh_token'
        ];
        $response = Http::get(config('constants.api.tiktok.url.auth').$path)
                    ->withQueryParameters((array)$parameter)
                    ->body();

        return $response;
    }

    public static function GetAuthorizedShops($access_token) {
        $path = '/api/shop/get_authorized_shop';
        $timestamp = time();
        $header = [
            'Content-Type'          => 'application/json',
            'x-tts-access-token'    => $access_token
        ];
        $parameter = [
            'app_key'   => config('constants.api.tiktok.app_key'),
            'timestamp' => $timestamp
        ];
        $sign = ServiceTiktok::GenerateSHA256($path, $parameter);

        $parameter = array_merge($parameter, [
            'sign'          => $sign,
            'access_token'  => $access_token
        ]);

        $response = Http::withHeaders($header)
                    ->withQueryParameters((array)$parameter)
                    ->get(config('constants.api.tiktok.url.host').$path)
                    ->body();

        return $response;
    }

    public static function GetProductDetail($access_token, $product_id) {
        $path = '/api/products/details';
        $timestamp = time();
        $header = [
            'content-type'          => 'application/json',
            'x-tts-access-token'    => $access_token
        ];
        $parameter = [
            'app_key'       => config('constants.api.tiktok.app_key'),
            'shop_cipher'   => config('constants.api.tiktok.shop_cipher'),
            'shop_id'       => config('constants.api.tiktok.shop_id'),
            'product_id'    => $product_id,
            'timestamp'     => $timestamp,
        ];
        $sign = ServiceTiktok::GenerateSHA256($path, $parameter);

        $parameter = array_merge($parameter, [
            'sign'          => $sign,
            'access_token'  => $access_token
        ]);

        $response = Http::withHeaders($header)
                    ->withQueryParameters((array)$parameter)
                    ->get(config('constants.api.tiktok.url.host').$path)
                    ->body();

        return $response;
    }

    public static function GetProductStock($access_token, $list_product) {
        $path = '/api/products/stock/list';
        $timestamp = time();
        $header = [
            'content-type'          => 'application/json',
            'x-tts-access-token'    => $access_token
        ];
        $parameter = [
            'app_key'       => config('constants.api.tiktok.app_key'),
            'shop_cipher'   => config('constants.api.tiktok.shop_cipher'),
            'shop_id'       => config('constants.api.tiktok.shop_id'),
            'product_ids'   => "[".$list_product."]",
            'timestamp'     => $timestamp,
        ];
        $sign = ServiceTiktok::GenerateSHA256($path, $parameter);

        $parameter = array_merge($parameter, [
            'sign'          => $sign,
            'access_token'  => $access_token
        ]);

        $response = Http::withHeaders($header)
                    ->withQueryParameters((array)$parameter)
                    ->post(config('constants.api.tiktok.url.host').$path)
                    ->body();

        return $response;
    }

    public static function GetOrderList($access_token, $start_date, $end_date, $page_size,
                                $order_status, $sort_by, $sort_type, $cursor) {
        $path = '/api/orders/search';
        $timestamp = time();
        $header = [
            'content-type'          => 'application/json',
            'x-tts-access-token'    => $access_token
        ];
        $parameter = [
            'app_key'           => config('constants.api.tiktok.app_key'),
            'shop_cipher'       => config('constants.api.tiktok.shop_cipher'),
            'page_size'         => (int)$page_size,
            'create_time_from'  => (int)$start_date,
            'create_time_to'    => (int)$end_date,
            'sort_by'           => $sort_by,
            'sort_type'         => (int)$sort_type,
            'timestamp'         => $timestamp,
        ];
        if(trim($order_status) != '') {
            $parameter = array_merge($parameter, [
                'order_status' => (int)$order_status
            ]);
        }
        if(trim($cursor) != '') {
            $parameter = array_merge($parameter, [
                'cursor' => $cursor
            ]);
        }
        $sign = ServiceTiktok::GenerateSHA256($path, $parameter);

        $parameter = array_merge($parameter, [
            'sign'          => $sign,
            'access_token'  => $access_token
        ]);

        $response = Http::withHeaders($header)
                    ->withQueryParameters((array)$parameter)
                    ->post(config('constants.api.tiktok.url.host').$path)
                    ->body();

        return $response;
    }

    public static function GetOrderListNew($access_token, $start_date, $end_date, $page_size,
                                $order_status, $sort_by, $sort_type, $cursor) {
        $path = '/order/202309/orders/search';
        $timestamp = time();
        $parameter = [
            'app_key'           => config('constants.api.tiktok.app_key'),
            'shop_cipher'       => config('constants.api.tiktok.shop_cipher'),
            'page_size'         => (int)$page_size,
            'sort_order'        => 'create_time',
            'page_token'        => 'ASC',
            'version'           => 202309,
            'timestamp'         => $timestamp,
        ];
        if(trim($cursor) != '') {
            $parameter = array_merge($parameter, [
                'cursor' => $cursor
            ]);
        }
        $sign = ServiceTiktok::GenerateSHA256($path, $parameter);

        $parameter_data = '?app_key='.config('constants.api.tiktok.app_key').
                            '&shop_cipher='.config('constants.api.tiktok.shop_cipher').
                            '&page_size='.(int)$page_size.
                            '&sort_order=create_time'.
                            '&page_token=ASC'.
                            '&version='.'202309'.
                            '&timestamp='.$timestamp.
                            '&sign='.$sign.
                            '&access_token='.$access_token;

        $headers = array(
            'content-type: application/json',
            'x-tts-access-token: '.$access_token,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, config('constants.api.tiktok.url.host').$path.$parameter_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        return $response;
    }

    public static function GetOrderDetail($access_token, $list_invoice) {
        $path = '/api/orders/detail/query';
        $timestamp = time();
        $header = [
            'content-type'          => 'application/json',
            'x-tts-access-token'    => $access_token
        ];
        $parameter = [
            'app_key'       => config('constants.api.tiktok.app_key'),
            'shop_cipher'   => config('constants.api.tiktok.shop_cipher'),
            'shop_id'       => config('constants.api.tiktok.shop_id'),
            'order_id_list' => "[".$list_invoice."]",
            'timestamp'     => $timestamp,
        ];
        $sign = ServiceTiktok::GenerateSHA256($path, $parameter);

        $parameter = array_merge($parameter, [
            'sign'          => $sign,
            'access_token'  => $access_token
        ]);

        $response = Http::withHeaders($header)
                    ->withQueryParameters((array)$parameter)
                    ->post(config('constants.api.tiktok.url.host').$path)
                    ->body();

        return $response;
    }

    public static function GetShippingProvider($access_token) {
        $path = '/api/logistics/shipping_providers';
        $timestamp = time();
        $header = [
            'content-type'          => 'application/json',
            'x-tts-access-token'    => $access_token
        ];
        $parameter = [
            'app_key'       => config('constants.api.tiktok.app_key'),
            'shop_cipher'   => config('constants.api.tiktok.shop_cipher'),
            'shop_id'       => config('constants.api.tiktok.shop_id'),
            'timestamp'     => $timestamp,
        ];
        $sign = ServiceTiktok::GenerateSHA256($path, $parameter);

        $parameter = array_merge($parameter, [
            'sign'          => $sign,
            'access_token'  => $access_token
        ]);

        $response = Http::withHeaders($header)
                    ->withQueryParameters((array)$parameter)
                    ->get(config('constants.api.tiktok.url.host').$path)
                    ->body();

        return $response;
    }

    public static function UpdateStock($access_token, $product_id, $skus) {
        $path = '/api/products/stocks';
        $timestamp = time();
        $header = [
            'content-type'          => 'application/json',
            'x-tts-access-token'    => $access_token
        ];
        $parameter = [
            'app_key'           => config('constants.api.tiktok.app_key'),
            'shop_cipher'       => config('constants.api.tiktok.shop_cipher'),
            'product_id'        => $product_id,
            'skus'              => $skus,
            'timestamp'         => $timestamp,
        ];
        $sign = ServiceTiktok::GenerateSHA256($path, $parameter);

        $parameter = array_merge($parameter, [
            'sign'          => $sign,
            'access_token'  => $access_token
        ]);

        $response = Http::withHeaders($header)
                    ->withQueryParameters((array)$parameter)
                    ->put(config('constants.api.tiktok.url.host').$path)
                    ->body();

        return $response;
    }
}
