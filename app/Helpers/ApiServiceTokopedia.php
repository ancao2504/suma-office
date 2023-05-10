<?php

namespace App\Helpers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use App\Helpers\ApiRequestTokopedia;

class ApiServiceTokopedia {
    public static function EkspedisiDaftar()
    {
        $request = 'backend/online/ekspedisi/tokopedia/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function EkspedisiSimpan($id, $tokopedia_id, $kode, $nama, $user_id)
    {
        $request = 'backend/online/ekspedisi/tokopedia/simpan';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'id_internal'   => strtoupper(trim($id)),
            'tokopedia_id'  => strtoupper(trim($tokopedia_id)),
            'kode'          => strtoupper(trim($kode)),
            'nama'          => strtoupper(trim($nama)),
            'user_id'       => strtoupper(trim($user_id)),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PemindahanDaftar($page, $per_page, $start_date, $end_date, $nomor_dokumen, $companyid)
    {
        $request = 'backend/online/pemindahan/tokopedia/daftar';
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
        $request = 'backend/online/pemindahan/tokopedia/form';
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
        $request = 'backend/online/pemindahan/tokopedia/form/detail';
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
        $request = 'backend/online/pemindahan/tokopedia/form/update/partnumber';
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
        $request = 'backend/online/pemindahan/tokopedia/form/update/statuspartnumber';
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
        $request = 'backend/online/pemindahan/tokopedia/form/update/dokumen';
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
        $request = 'backend/online/updateharga/tokopedia/daftar';
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
        $request = 'backend/online/updateharga/tokopedia/buatdokumen';
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
        $request = 'backend/online/updateharga/tokopedia/form';
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
        $request = 'backend/online/updateharga/tokopedia/update/partnumber';
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
        $request = 'backend/online/updateharga/tokopedia/update/statuspartnumber';
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
        $request = 'backend/online/updateharga/tokopedia/update/dokumen';
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
        $request = 'backend/online/products/tokopedia/daftar';
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
        $request = 'backend/online/products/tokopedia/cek/productid';
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
        $request = 'backend/online/products/tokopedia/update';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'part_number'   => trim($part_number),
            'product_id'    => trim($product_id),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OrderDaftar($page, $per_page, $start_date, $end_date, $status, $companyid)
    {
        $request = 'backend/online/orders/tokopedia/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'page'          => $page ?? 1,
            'per_page'      => $per_page ?? 10,
            'start_date'    => $start_date,
            'end_date'      => $end_date,
            'status'        => $status,
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OrderSingle($nomor_invoice, $companyid)
    {
        $request = 'backend/online/orders/tokopedia/single';
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
        $request = 'backend/online/orders/tokopedia/form';
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
        $request = 'backend/online/orders/tokopedia/proses';
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
        $request = 'backend/online/shipping/tokopedia/pickup';
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
        $request = 'backend/online/shipping/tokopedia/cetak-label';
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
        $request = 'backend/online/historysaldo/tokopedia/daftar';
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
