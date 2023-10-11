<?php

namespace App\Helpers\App;

use App\Helpers\App\ApiRequest;


class ServiceTiktok {
    public static function EkspedisiDaftar()
    {
        $request = 'backend/online/ekspedisi/tiktok/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function EkspedisiSimpan($id, $tiktok_id, $kode, $nama, $user_id)
    {
        $request = 'backend/online/ekspedisi/tiktok/simpan';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'id_internal'   => strtoupper(trim($id)),
            'tiktok_id'  => strtoupper(trim($tiktok_id)),
            'kode'          => strtoupper(trim($kode)),
            'nama'          => strtoupper(trim($nama)),
            'user_id'       => strtoupper(trim($user_id)),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PemindahanDaftar($page, $per_page, $start_date, $end_date, $nomor_dokumen, $companyid)
    {
        $request = 'backend/online/pemindahan/tiktok/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'page'          => $page ?? 1,
            'per_page'      => $per_page ?? 10,
            'start_date'    => $start_date,
            'end_date'      => $end_date,
            'nomor_dokumen' => trim($nomor_dokumen),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PemindahanForm($nomor_dokumen, $companyid)
    {
        $request = 'backend/online/pemindahan/tiktok/form';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PemindahanFormDetail($nomor_dokumen, $companyid)
    {
        $request = 'backend/online/pemindahan/tiktok/form/detail';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PemindahanUpdatePerPartNumber($nomor_dokumen, $part_number, $companyid)
    {
        $request = 'backend/online/pemindahan/tiktok/form/update/partnumber';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'part_number'   => trim($part_number),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PemindahanUpdateStatusPerPartNumber($nomor_dokumen, $part_number, $companyid)
    {
        $request = 'backend/online/pemindahan/tiktok/form/update/statuspartnumber';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'part_number'   => trim($part_number),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PemindahanUpdatePerNomorDokumen($nomor_dokumen, $companyid)
    {
        $request = 'backend/online/pemindahan/tiktok/form/update/dokumen';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function UpdateHargaDaftar($page, $per_page, $year, $month, $search, $companyid)
    {
        $request = 'backend/online/updateharga/tiktok/daftar';
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

    public static function UpdateHargaBuatDokumen($nomor_dokumen, $tanggal, $companyid, $user_id)
    {
        $request = 'backend/online/updateharga/tiktok/buatdokumen';
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

    public static function UpdateHargaForm($nomor_dokumen, $companyid)
    {
        $request = 'backend/online/updateharga/tiktok/form';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'companyid'     => trim($companyid)
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }


    public static function UpdateHargaUpdatePerPartNumber($nomor_dokumen, $part_number, $companyid)
    {
        $request = 'backend/online/updateharga/tiktok/update/partnumber';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'part_number'   => trim($part_number),
            'companyid'     => trim($companyid)
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }



    public static function UpdateHargaUpdateStatusPartNumber($nomor_dokumen, $part_number, $companyid)
    {
        $request = 'backend/online/updateharga/tiktok/update/statuspartnumber';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'part_number'   => trim($part_number),
            'companyid'     => trim($companyid)
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }



    public static function UpdateHargaUpdatePerNomorDokumen($nomor_dokumen, $companyid)
    {
        $request = 'backend/online/updateharga/tiktok/update/dokumen';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'companyid'     => trim($companyid)
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }



    public static function ProductSearchPartNumber($part_number, $companyid)
    {
        $request = 'backend/online/products/tiktok/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'part_number'   => trim($part_number),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function ProductCekProductId($product_id, $companyid)
    {
        $request = 'backend/online/products/tiktok/cek/productid';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'product_id'    => trim($product_id),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }



    public static function ProductUpdateProductId($part_number, $product_id, $companyid)
    {
        $request = 'backend/online/products/tiktok/update';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'part_number'   => trim($part_number),
            'product_id'    => trim($product_id),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OrderDaftar($page, $per_page, $start_date, $end_date, $status, $cursor, $companyid)
    {
        $request = 'backend/online/orders/tiktok/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'page'          => $page ?? 1,
            'per_page'      => $per_page ?? 10,
            'start_date'    => $start_date,
            'end_date'      => $end_date,
            'status'        => $status,
            'cursor'        => $cursor,
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OrderSingle($nomor_invoice, $companyid)
    {
        $request = 'backend/online/orders/tiktok/single';
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
        $request = 'backend/online/orders/tiktok/form';
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
        $request = 'backend/online/orders/tiktok/proses';
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
        $request = 'backend/online/shipping/tiktok/pickup';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_invoice' => $nomor_invoice,
            'companyid'     => trim($companyid)
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OrderUpdateKurir($nomor_invoice, $companyid)
    {
        $request = 'backend/online/orders/tiktok/update-kurir';
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
        $request = 'backend/online/shipping/tiktok/cetak-label';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_invoice' => $nomor_invoice,
            'companyid'     => trim($companyid)
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function HistorySaldo($page, $per_page, $start_date, $end_date, $companyid)
    {
        $request = 'backend/online/historysaldo/tiktok/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'page'          => $page ?? 1,
            'per_page'      => $per_page ?? 100,
            'start_date'    => $start_date,
            'end_date'      => $end_date,
            'companyid'     => trim($companyid)
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }
}
