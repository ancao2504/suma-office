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

    public static function EkspedisiDaftar()
    {
        $request = 'online/ekspedisi/shopee/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function EkspedisiSimpan($id, $shopeeId, $kode, $nama, $user_id)
    {
        $request = 'online/ekspedisi/shopee/simpan';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'id_internal' => strtoupper(trim($id)),
            'shopee_id'   => strtoupper(trim($shopeeId)),
            'kode'        => strtoupper(trim($kode)),
            'nama'        => strtoupper(trim($nama)),
            'user_id'     => strtoupper(trim($user_id)),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OrderDaftar($fields, $start_date, $end_date, $page_size, $cursor, $status, $companyid)
    {
        $request = 'online/orders/shopee/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'fields'        => $fields,
            'start_date'    => $start_date,
            'end_date'      => $end_date,
            'page_size'     => $page_size,
            'cursor'        => $cursor,
            'status'        => $status,
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OrderSingle($nomor_invoice, $companyid)
    {
        $request = 'online/orders/shopee/single';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_invoice' => $nomor_invoice,
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OrderForm($nomor_invoice, $companyid, $user_id)
    {
        $request = 'online/orders/shopee/form';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_invoice' => $nomor_invoice,
            'companyid'     => trim($companyid),
            'user_id'       => trim($user_id),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OrderProses($nomor_invoice, $tanggal, $companyid, $user_id)
    {
        $request = 'online/orders/shopee/proses';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_invoice' => $nomor_invoice,
            'tanggal'       => $tanggal,
            'companyid'     => trim($companyid),
            'user_id'       => trim($user_id),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OrderPickup($nomor_invoice, $companyid)
    {
        $request = 'online/orders/shopee/pickup';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_invoice' => $nomor_invoice,
            'companyid'     => trim($companyid)
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OrderCetakLabel($nomor_invoice, $companyid)
    {
        $request = 'online/orders/shopee/cetak-label';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_invoice' => $nomor_invoice,
            'companyid'     => trim($companyid)
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    // PEMINDAHAN
    public static function PemindahanDaftar($search,$start_date,$end_date,$companyid,$page,$per_page)
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
    public static function PemindahanDetail($nomor_dokumen ,$companyid)
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
    public static function UpdateStockPerDokumen($nomor_dokumen, $companyid){

        $request = 'online/pemindahan/shopee/update/stock/dokumen';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'companyid'     => trim($companyid)
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }
    public static function UpdateStockPerPart($nomor_dokumen, $kode_part, $companyid){

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
    public static function PemindahanUpdateStatusPerPartNumber($nomor_dokumen, $part_number, $companyid)
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

    // UPDATE HARGA
    public static function UpdateHargaDaftar($page, $per_page, $year, $month, $search, $companyid)
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
    public static function UpdateHargaDetail($nomor_dokumen, $companyid)
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
    public static function UpdateHargaPerNomorDokumen($nomor_dokumen, $companyid)
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
    public static function UpdateHargaPerPartNumber($nomor_dokumen, $part_number, $companyid)
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
    public static function UpdateHargaStatusPartNumber($nomor_dokumen, $part_number, $companyid)
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
    public static function BuatDokumenUpdateHarga($nomor_dokumen, $tanggal, $companyid, $user_id)
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

    // PRODUCT
    public static function SearchProductByPartNumber($part_number, $companyid)
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
    public static function CekProductId($product_id, $companyid)
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
    public static function UpdateShopeeidInPart($part_number, $product_id, $companyid)
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
}
