<?php

namespace App\Helpers\Api;

use Illuminate\Support\Facades\Http;

class ServiceTokopedia
{

    public static function AuthToken()
    {
        $path = 'token?grant_type=client_credentials';
        $requestHeader = ['Authorization' => 'Basic ' . config('constants.api.tokopedia.token')];
        $requestBody = [];

        $responseApi = Http::withHeaders($requestHeader)
            ->post(config('constants.api.tokopedia.url.account') . '/' . $path, $requestBody)
            ->body();
        return $responseApi;
    }

    public static function GetShopInfo($auth_token)
    {
        $path = 'v1/shop/fs/' . config('constants.api.tokopedia.fs_id') . '/shop-info';
        $requestHeader = ['Authorization' => $auth_token];
        $requestBody = [
            'shop_id'   => config('constants.api.tokopedia.shop_id')
        ];
        $responseApi = Http::withHeaders($requestHeader)
            ->get(config('constants.api.tokopedia.url.base_url') . '/' . $path, $requestBody)
            ->body();

        return $responseApi;
    }

    public static function GetProductInfoByProductId($auth_token, $product_id)
    {
        $path = 'inventory/v1/fs/' . config('constants.api.tokopedia.fs_id') . '/product/info';
        $requestHeader = ['Authorization' => $auth_token];
        $requestBody = [
            'product_id'   => $product_id
        ];
        $responseApi = Http::withHeaders($requestHeader)
            ->get(config('constants.api.tokopedia.url.base_url') . '/' . $path, $requestBody)
            ->body();

        return $responseApi;
    }

    public static function GetProductInfoByPartNumber($auth_token, $part_number)
    {
        $path = 'inventory/v1/fs/' . config('constants.api.tokopedia.fs_id') . '/product/info';
        $requestHeader = ['Authorization' => $auth_token];
        $requestBody = [
            'sku'   => $part_number
        ];
        $responseApi = Http::withHeaders($requestHeader)
            ->get(config('constants.api.tokopedia.url.base_url') . '/' . $path, $requestBody)
            ->body();

        return $responseApi;
    }

    public static function ProductUpdateStockDecrement($auth_token, $parameter_raw)
    {
        $path = 'inventory/v2/fs/' . config('constants.api.tokopedia.fs_id') . '/stock/decrement?shop_id=' . config('constants.api.tokopedia.shop_id');
        $requestHeader = ['Authorization' => $auth_token];
        $requestBody = $parameter_raw;

        $responseApi = Http::withHeaders($requestHeader)
            ->post(config('constants.api.tokopedia.url.base_url') . '/' . $path, $requestBody)
            ->body();

        return $responseApi;
    }

    public static function ProductUpdateStockIncrement($auth_token, $parameter_raw)
    {
        $path = 'inventory/v2/fs/' . config('constants.api.tokopedia.fs_id') . '/stock/increment?shop_id=' . config('constants.api.tokopedia.shop_id');
        $requestHeader = ['Authorization' => $auth_token];
        $requestBody = $parameter_raw;

        $responseApi = Http::withHeaders($requestHeader)
            ->post(config('constants.api.tokopedia.url.base_url') . '/' . $path, $requestBody)
            ->body();

        return $responseApi;
    }

    public static function ProductUpdateStatusActive($auth_token, $parameter_raw)
    {
        $path = 'v1/products/fs/' . config('constants.api.tokopedia.fs_id') . '/active?shop_id=' . config('constants.api.tokopedia.shop_id');
        $requestHeader = ['Authorization' => $auth_token];
        $requestBody = $parameter_raw;

        $responseApi = Http::withHeaders($requestHeader)
            ->post(config('constants.api.tokopedia.url.base_url') . '/' . $path, $requestBody)
            ->body();

        return $responseApi;
    }

    public static function ProductUpdateStatusInActive($auth_token, $parameter_raw)
    {
        $path = 'v1/products/fs/' . config('constants.api.tokopedia.fs_id') . '/inactive?shop_id=' . config('constants.api.tokopedia.shop_id');
        $requestHeader = ['Authorization' => $auth_token];
        $requestBody = $parameter_raw;

        $responseApi = Http::withHeaders($requestHeader)
            ->post(config('constants.api.tokopedia.url.base_url') . '/' . $path, $requestBody)
            ->body();

        return $responseApi;
    }

    public static function ProductUpdatePriceOnly($auth_token, $parameter_raw)
    {
        $path = 'inventory/v1/fs/' . config('constants.api.tokopedia.fs_id') . '/price/update?shop_id=' . config('constants.api.tokopedia.shop_id');
        $requestHeader = ['Authorization' => $auth_token];
        $requestBody = $parameter_raw;

        $responseApi = Http::withHeaders($requestHeader)
            ->post(config('constants.api.tokopedia.url.base_url') . '/' . $path, $requestBody)
            ->body();

        return $responseApi;
    }

    public static function OrderGetAllOrder($auth_token, $page, $per_page, $from_date, $to_date, $status)
    {
        $path = 'v2/order/list';
        $requestHeader = ['Authorization' => $auth_token];
        $requestBody = [
            'fs_id'         => config('constants.api.tokopedia.fs_id'),
            'page'          => $page,
            'per_page'      => $per_page,
            'from_date'     => $from_date,
            'to_date'       => $to_date,
            'status'        => $status
        ];

        $responseApi = Http::withHeaders($requestHeader)
            ->get(config('constants.api.tokopedia.url.base_url') . '/' . $path, $requestBody)
            ->body();

        return $responseApi;
    }

    public static function OrderGetSingleOrder($auth_token, $nomor_invoice)
    {
        $path = 'v2/fs/' . config('constants.api.tokopedia.fs_id') . '/order';
        $requestHeader = ['Authorization' => $auth_token];
        $requestBody = [
            'invoice_num'   => $nomor_invoice
        ];

        $responseApi = Http::withHeaders($requestHeader)
            ->get(config('constants.api.tokopedia.url.base_url') . '/' . $path, $requestBody)
            ->body();

        return $responseApi;
    }

    public static function OrderAcceptOrder($auth_token, $order_id)
    {
        $path = 'v1/order/' . $order_id . '/fs/' . config('constants.api.tokopedia.fs_id') . '/ack';
        $requestHeader = ['Authorization' => $auth_token];
        $requestBody = [];

        $responseApi = Http::withHeaders($requestHeader)
            ->post(config('constants.api.tokopedia.url.base_url') . '/' . $path, $requestBody)
            ->body();

        return $responseApi;
    }

    public static function OrderRequestPickup($auth_token, $parameter_raw)
    {
        $path = 'inventory/v1/fs/' . config('constants.api.tokopedia.fs_id') . '/pick-up';
        $requestHeader = ['Authorization' => $auth_token];
        $requestBody = $parameter_raw;

        $responseApi = Http::withHeaders($requestHeader)
            ->post(config('constants.api.tokopedia.url.base_url') . '/' . $path, $requestBody)
            ->body();

        return $responseApi;
    }

    public static function OrderGetShippingLabel($auth_token, $order_id)
    {
        $path = 'v1/order/' . $order_id . '/fs/' . config('constants.api.tokopedia.fs_id') . '/shipping-label';
        $requestHeader = ['Authorization' => $auth_token];
        $requestBody = [
            'printed'   => 1
        ];

        $responseApi = Http::withHeaders($requestHeader)
            ->get(config('constants.api.tokopedia.url.base_url') . '/' . $path, $requestBody)
            ->body();

        return $responseApi;
    }

    public static function LogisticGetShipmentInfo($auth_token)
    {
        $path = 'v2/logistic/fs/' . config('constants.api.tokopedia.fs_id') . '/info';
        $requestHeader = ['Authorization' => $auth_token];
        $requestBody = [
            'shop_id'   => config('constants.api.tokopedia.shop_id')
        ];

        $responseApi = Http::withHeaders($requestHeader)
            ->get(config('constants.api.tokopedia.url.base_url') . '/' . $path, $requestBody)
            ->body();

        return $responseApi;
    }

    public static function LogisticGetShipmentActiveInfo($auth_token)
    {
        $path = 'v1/logistic/fs/' . config('constants.api.tokopedia.fs_id') . '/active-info';
        $requestHeader = ['Authorization' => $auth_token];
        $requestBody = [
            'shop_id'   => config('constants.api.tokopedia.shop_id')
        ];

        $responseApi = Http::withHeaders($requestHeader)
            ->get(config('constants.api.tokopedia.url.base_url') . '/' . $path, $requestBody)
            ->body();

        return $responseApi;
    }

    public static function GetSaldoHistory($auth_token, $page, $per_page, $from_date, $to_date)
    {
        $path = 'v1/fs/' . config('constants.api.tokopedia.fs_id') . '/shop/' . config('constants.api.tokopedia.shop_id') . '/saldo-history';
        $requestHeader = ['Authorization' => $auth_token];
        $requestBody = [
            'shop_id'   => config('constants.api.tokopedia.shop_id'),
            'from_date' => $from_date,
            'to_date'   => $to_date,
            'page'      => $page,
            'per_page'  => $per_page
        ];
        $responseApi = Http::withHeaders($requestHeader)
            ->get(config('constants.api.tokopedia.url.base_url') . '/' . $path, $requestBody)
            ->body();

        return $responseApi;
    }

    public static function GetAllCategories($auth_token)
    {
        $path = 'inventory/v1/fs/' . config('constants.api.tokopedia.fs_id') . '/product/category';
        $requestHeader = ['Authorization' => $auth_token];
        $requestBody = [
            'fs_id'         => config('constants.api.tokopedia.fs_id')
        ];
        $responseApi = Http::withHeaders($requestHeader)
            ->get(config('constants.api.tokopedia.url.base_url') . '/' . $path, $requestBody)
            ->body();

        return $responseApi;
    }

    public static function GetShowcase($auth_token)
    {
        $path = 'inventory/v1/fs/' . config('constants.api.tokopedia.fs_id') . '/product/etalase';
        $requestHeader = ['Authorization' => $auth_token];
        $requestBody = [
            'shop_id'   => config('constants.api.tokopedia.shop_id')
        ];
        $responseApi = Http::withHeaders($requestHeader)
            ->get(config('constants.api.tokopedia.url.base_url') . '/' . $path, $requestBody)
            ->body();

        return $responseApi;
    }

    public static function createProducts($auth_token, $parameter_raw)
    {
        $path = 'v3/products/fs/' . config('constants.api.tokopedia.fs_id') . '/create?shop_id=' . config('constants.api.tokopedia.shop_id');
        $requestHeader = ['Authorization' => $auth_token];
        $requestBody = [
            "products" => $parameter_raw
        ];

        $responseApi = Http::withHeaders($requestHeader)
            ->post(config('constants.api.tokopedia.url.base_url') . '/' . $path, $requestBody)
            ->body();

        return $responseApi;
    }

}
