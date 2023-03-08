<?php

namespace App\Helpers;

use App\Helpers\ApiRequest;

class ApiServiceShopee
{
    public static function Authorization()
    {
        $request = 'auth/shopee/token/access';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function AuthorizationGenerateLink()
    {
        $request = 'auth/shopee/token/access/generate';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function AuthorizationSimpan($access_code, $companyid, $user_id)
    {
        $request = 'auth/shopee/token/access/simpan';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'access_code'   => trim($access_code),
            'companyid'     => trim($companyid),
            'user_id'       => trim($user_id)
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlineUpdateHargaShopeeDaftar($page, $per_page, $year, $month, $search, $companyid)
    {
        $request = 'online/updateharga/shopee/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'page'      => $page,
            'per_page'  => $per_page,
            'year'      => $year,
            'month'     => $month,
            'search'    => $search,
            'companyid' => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlineUpdateHargaShopeeBuatDokumen($nomor_dokumen, $tanggal, $companyid, $user_id)
    {
        $request = 'online/updateharga/shopee/buatdokumen';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'kode'      => trim($nomor_dokumen),
            'tanggal'   => trim($tanggal),
            'companyid' => trim($companyid),
            'user_id'   => trim($user_id)
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlineUpdateHargaShopeeForm($nomor_dokumen, $companyid)
    {
        $request = 'online/updateharga/shopee/form';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'companyid'     => trim($companyid)
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlineUpdateHargaShopeeUpdatePerPartNumber($nomor_dokumen, $part_number, $companyid)
    {
        $request = 'online/updateharga/shopee/update/partnumber';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'part_number'   => trim($part_number),
            'companyid'     => trim($companyid)
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlineUpdateHargaShopeeUpdateStatusPartNumber($nomor_dokumen, $part_number, $companyid)
    {
        $request = 'online/updateharga/shopee/update/statuspartnumber';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'part_number'   => trim($part_number),
            'companyid'     => trim($companyid)
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlineUpdateHargaShopeeUpdatePerNomorDokumen($nomor_dokumen, $companyid)
    {
        $request = 'online/updateharga/shopee/update/dokumen';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'companyid'     => trim($companyid)
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlineProductShopeeSearchPartNumber($part_number, $companyid)
    {
        $request = 'online/products/shopee/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'part_number'   => trim($part_number),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlineProductShopeeCekProductId($product_id, $companyid)
    {
        $request = 'online/products/shopee/cek/productid';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'product_id'    => trim($product_id),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlineProductShopeeUpdateProductId($part_number, $product_id, $companyid)
    {
        $request = 'online/products/shopee/update';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'part_number'   => trim($part_number),
            'product_id'    => trim($product_id),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlinePemindahanShopeeDetail($nomor_dokumen ,$companyid)
    {
        $request = 'online/pemindahan/shopee/detail';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen'        => trim($nomor_dokumen),
            'companyid'     => trim($companyid)
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlinePemindahanShopeeDaftar($search,$start_date,$end_date,$companyid,$page,$per_page)
    {
        $request = 'online/pemindahan/shopee/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($search),
            'start_date'    => trim($start_date),
            'end_date'      => trim($end_date),
            'companyid'     => trim($companyid),
            'page'          => $page ?? 1,
            'per_page'      => $per_page ?? 10,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function onlineuUpdateStockShopeePerDokumen($nomor_dokumen, $companyid){

        $request = 'online/pemindahan/shopee/update/stock/dokumen';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'companyid'     => trim($companyid)
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function onlineUpdateStockShopeePerPart($nomor_dokumen, $kode_part, $companyid){

        $request = 'online/pemindahan/shopee/update/stock/part';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'kode_part'     => trim($kode_part),
            'companyid'     => trim($companyid)
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlinePemindahanShopeeUpdateStatusPerPartNumber($nomor_dokumen, $part_number, $companyid)
    {
        $request = 'online/pemindahan/shopee/update/statuspartnumber';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'part_number'   => trim($part_number),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }
}
