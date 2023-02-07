<?php

namespace App\Helpers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use App\Helpers\ApiRequestTokopedia;

class ApiServiceTokopedia {

    public static function AuthToken() {
        $requestApi = 'token?grant_type=client_credentials';
        $requestHeader = [ 'Authorization' => 'Basic '.config('constants.tokopedia.token') ];
        $requestBody = [];
        $responseApi = ApiRequestTokopedia::requestAccount($requestApi, $requestHeader, $requestBody);
        return $responseApi;
    }

    public static function GetProductInfoByProductId($product_id) {
        $requestApi = 'inventory/v1/fs/'.config('constants.tokopedia.fs_id').'/product/info';
        $requestHeader = [ 'Authorization' => 'Bearer '.session()->get('authorization_tokopedia') ];
        $requestBody = [
            'product_id'   => $product_id
        ];
        $responseApi = ApiRequestTokopedia::requestGet($requestApi, $requestHeader, $requestBody);
        return $responseApi;
    }

    public static function GetProductInfoByPartNumber($part_number) {
        $requestApi = 'inventory/v1/fs/'.config('constants.tokopedia.fs_id').'/product/info';
        $requestHeader = [ 'Authorization' => 'Bearer '.session()->get('authorization_tokopedia') ];
        $requestBody = [
            'sku'   => $part_number
        ];
        $responseApi = ApiRequestTokopedia::requestGet($requestApi, $requestHeader, $requestBody);
        return $responseApi;
    }

    public static function ProductUpdateStockDecrement($parameter_raw) {
        $requestApi = 'inventory/v2/fs/'.config('constants.tokopedia.fs_id').'/stock/decrement?shop_id='.config('constants.tokopedia.shop_id');
        $requestHeader = [ 'Authorization' => 'Bearer '.session()->get('authorization_tokopedia') ];
        $requestBody = $parameter_raw;
        $responseApi = ApiRequestTokopedia::requestPost($requestApi, $requestHeader, $requestBody);
        return $responseApi;
    }

    public static function ProductUpdateStockIncrement($parameter_raw) {
        $requestApi = 'inventory/v2/fs/'.config('constants.tokopedia.fs_id').'/stock/increment?shop_id='.config('constants.tokopedia.shop_id');
        $requestHeader = [ 'Authorization' => 'Bearer '.session()->get('authorization_tokopedia') ];
        $requestBody = $parameter_raw;
        $responseApi = ApiRequestTokopedia::requestPost($requestApi, $requestHeader, $requestBody);
        return $responseApi;
    }
}
