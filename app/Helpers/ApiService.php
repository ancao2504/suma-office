<?php

namespace App\Helpers;

use App\Helpers\ApiRequest;

class ApiService
{

    public static function AuthLogin($email, $password)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'auth/login';
        $header = ['Authorization' => $credential];
        $body = [
            'email'     => $email,
            'password'  => $password,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function AccountProfile($user_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'account/profile';
        $header = ['Authorization' => $credential];
        $body = [
            'user_id'       => $user_id,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function AccountProfileSimpan($user_id, $name, $email, $telepon, $photo, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'account/profile/simpan';
        $header = ['Authorization' => $credential];
        $body = [
            'user_id'       => $user_id,
            'name'          => $name,
            'email'         => $email,
            'telepon'       => $telepon,
            'photo'         => $photo,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function AccountChangePassword($user_id, $email, $old_password, $new_password, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'account/profile/changepassword';
        $header = ['Authorization' => $credential];
        $body = [
            'user_id'       => $user_id,
            'email'         => $email,
            'old_password'  => $old_password,
            'new_password'  => $new_password,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function BackOrderDaftar($kode_sales, $kode_dealer, $part_number, $page, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'parts/backorder';
        $header = ['Authorization' => $credential];
        $body = [
            'kode_sales'    => $kode_sales,
            'kode_dealer'   => $kode_dealer,
            'part_number'   => $part_number,
            'page'          => $page,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function CartHeader($user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/cart';
        $header = ['Authorization' => $credential];
        $body = [
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function CartInformation($user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/cart/information';
        $header = ['Authorization' => $credential];
        $body = [
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function CartCheckOutCekAturanHarga($user_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/cart/checkout/cekaturanharga';
        $header = ['Authorization' => $credential];
        $body = [
            'user_id'       => $user_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function CartCheckOutProses($password, $password_confirm, $user_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/cart/checkout/proses';
        $header = ['Authorization' => $credential];
        $body = [
            'password'      => $password,
            'password_confirm'  => $password_confirm,
            'user_id'       => $user_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function CartCheckOutResult($role_id, $kode, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/cart/checkout/result';
        $header = ['Authorization' => $credential];
        $body = [
            'role_id'       => $role_id,
            'kode'          => $kode,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function CartDetail($user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/cart/detail';
        $header = ['Authorization' => $credential];
        $body = [
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function CartDeletePart($part_number, $user_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/cart/deletepart';
        $header = ['Authorization' => $credential];
        $body = [
            'part_number'   => $part_number,
            'user_id'       => $user_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function CartEditPart($part_number, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/cart/editpart';
        $header = ['Authorization' => $credential];
        $body = [
            'part_number'   => $part_number,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function CartHapusTemporary($user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/cart/deleteall';
        $header = ['Authorization' => $credential];
        $body = [
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function CartImportExcel($nama_file, $part_excel, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/cart/importexcel';
        $header = ['Authorization' => $credential];
        $body = [
            'nama_file'     => $nama_file,
            'part_excel'    => $part_excel,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function CartProsesExcel($file_excel, $perbandingan, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/cart/prosesexcel';
        $header = ['Authorization' => $credential];
        $body = [
            'file_excel'    => $file_excel,
            'perbandingan'  => $perbandingan,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function CartReset($user_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/cart/reset';
        $header = ['Authorization' => $credential];
        $body = [
            'user_id'       => $user_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function CartSimpanDraft($kode_sales, $kode_dealer, $back_order, $keterangan, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/cart/simpandraft';
        $header = ['Authorization' => $credential];
        $body = [
            'kode_sales'    => $kode_sales,
            'kode_dealer'   => $kode_dealer,
            'bo'            => $back_order,
            'keterangan'    => $keterangan,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function CartSimpanPart($part_number, $tpc, $jml_order, $harga, $discount, $discount_plus, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/cart/simpanpart';
        $header = ['Authorization' => $credential];
        $body = [
            'part_number'   => $part_number,
            'tpc'           => $tpc,
            'jml_order'     => $jml_order,
            'harga'         => $harga,
            'discount'      => $discount,
            'discount_plus' => $discount_plus,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DashboardDealerPenjualanBulanan($year, $month, $kode_dealer, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'dashboard/dealer/penjualanbulanan';
        $header = ['Authorization' => $credential];
        $body = [
            'year'          => $year,
            'month'         => $month,
            'kode_dealer'   => $kode_dealer,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DashboardManagementSalesByProduct($year, $month, $fields, $produk, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'dashboard/management/sales/salesbyproduct';
        $header = ['Authorization' => $credential];
        $body = [
            'year'          => $year,
            'month'         => $month,
            'fields'        => $fields,
            'produk'        => $produk,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DashboardManagementSalesByDate($year, $month, $fields, $produk, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'dashboard/management/sales/salesbydate';
        $header = ['Authorization' => $credential];
        $body = [
            'year'          => $year,
            'month'         => $month,
            'fields'        => $fields,
            'produk'        => $produk,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DashboardManagementStockByProduct($year, $month, $fields, $produk, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'dashboard/management/stock/stockbyproduct';
        $header = ['Authorization' => $credential];
        $body = [
            'year'          => $year,
            'month'         => $month,
            'fields'        => $fields,
            'produk'        => $produk,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DashboardMarketingGroupPerLevel($year, $jenis_mkr, $kode_mkr, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'dashboard/marketing/pencapaian/perlevel';
        $header = ['Authorization' => $credential];
        $body = [
            'year'          => $year,
            'jenis_mkr'     => $jenis_mkr,
            'kode_mkr'      => $kode_mkr,
            'role_id'       => $role_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DashboardMarketingGrowth($year, $level_produk, $kode_produk, $jenis_mkr, $kode_mkr, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'dashboard/marketing/pencapaian/growth';
        $header = ['Authorization' => $credential];
        $body = [
            'year'          => $year,
            'level_produk'  => $level_produk,
            'kode_produk'   => $kode_produk,
            'jenis_mkr'     => $jenis_mkr,
            'kode_mkr'      => $kode_mkr,
            'role_id'       => $role_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DashboardSalesmanPenjualanBulanan($year, $month, $role_salesman, $kode_sales_spv, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'dashboard/salesman/penjualanbulanan';
        $header = ['Authorization' => $credential];
        $body = [
            'year'              => $year,
            'month'             => $month,
            'role_salesman'     => $role_salesman,
            'kode_sales_spv'    => $kode_sales_spv,
            'user_id'           => $user_id,
            'role_id'           => $role_id,
            'companyid'         => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DashboardSalesmanPenjualanHarian($year, $month, $role_salesman, $kode_sales_spv, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'dashboard/salesman/penjualanharian';
        $header = ['Authorization' => $credential];
        $body = [
            'year'              => $year,
            'month'             => $month,
            'role_salesman'     => $role_salesman,
            'kode_sales_spv'    => $kode_sales_spv,
            'user_id'           => $user_id,
            'role_id'           => $role_id,
            'companyid'         => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DealerDaftar($page, $search, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'profile/dealer';
        $header = ['Authorization' => $credential];
        $body = [
            'page'      => $page,
            'search'    => $search,
            'user_id'   => $user_id,
            'role_id'   => $role_id,
            'companyid' => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DealerProfile($kode_dealer, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'profile/dealer/profile';
        $header = ['Authorization' => $credential];
        $body = [
            'kode_dealer' => $kode_dealer,
            'user_id'   => $user_id,
            'role_id'   => $role_id,
            'companyid' => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function FakturDaftar($year, $month, $kode_sales, $kode_dealer, $nomor_faktur, $page, $per_page, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/faktur';
        $header = ['Authorization' => $credential];
        $body = [
            'year'          => $year,
            'month'         => $month,
            'kode_sales'    => $kode_sales,
            'kode_dealer'   => $kode_dealer,
            'nomor_faktur'  => $nomor_faktur,
            'page'          => $page,
            'per_page'      => $per_page,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function FakturDetail($nomor_faktur, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/faktur/detail';
        $header = ['Authorization' => $credential];
        $body = [
            'nomor_faktur' => $nomor_faktur,
            'user_id'   => $user_id,
            'role_id'   => $role_id,
            'companyid' => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionCompany()
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'options/company';
        $header = ['Authorization' => $credential];
        $body = [];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionClassProduk()
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'options/classproduk';
        $header = ['Authorization' => $credential];
        $body = [];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionDealer($search, $page, $per_page, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'options/dealer';
        $header = ['Authorization' => $credential];
        $body = [
            'search'        => $search,
            'page'          => $page,
            'per_page'      => $per_page,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionDealerSalesman($kode_sales, $search, $page, $per_page, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'options/dealersalesman';
        $header = ['Authorization' => $credential];
        $body = [
            'kode_sales'    => $kode_sales,
            'search'        => $search,
            'page'          => $page,
            'per_page'      => $per_page,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionGroupProduk($level, $search, $page, $per_page)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'options/groupproduk';
        $header = ['Authorization' => $credential];
        $body = [
            'level'     => $level,
            'search'    => $search,
            'page'      => $page,
            'per_page'  => $per_page,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionPartNumber($part_number, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'options/partnumber';
        $header = ['Authorization' => $credential];
        $body = [
            'part_number'   => $part_number,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionProdukLevel()
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'options/levelproduk';
        $header = ['Authorization' => $credential];
        $body = [];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionSalesman($search, $page, $per_page, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'options/salesman';
        $header = ['Authorization' => $credential];
        $body = [
            'search'        => $search,
            'page'          => $page,
            'per_page'      => $per_page,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionSupervisor($search, $page, $per_page, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'options/supervisor';
        $header = ['Authorization' => $credential];
        $body = [
            'search'        => $search,
            'page'          => $page,
            'per_page'      => $per_page,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionSubProduk()
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'options/subproduk';
        $header = ['Authorization' => $credential];
        $body = [];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionTipeMotor($search, $page, $per_page)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'options/typemotor';
        $header = ['Authorization' => $credential];
        $body = [
            'search'        => $search,
            'page'          => $page,
            'per_page'      => $per_page,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionRoleUser()
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'options/roleuser';
        $header = ['Authorization' => $credential];
        $body = [];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PartDaftar($page, $type_motor, $group_level, $group_produk, $part_number, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'parts/partnumber';
        $header = ['Authorization' => $credential];
        $body = [
            'page'          => $page,
            'type_motor'    => $type_motor,
            'group_level'   => $group_level,
            'group_produk'  => $group_produk,
            'part_number'   => $part_number,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PartToCart($part_number, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'parts/partnumber/parttocart';
        $header = ['Authorization' => $credential];
        $body = [
            'part_number' => $part_number,
            'user_id'   => $user_id,
            'role_id'   => $role_id,
            'companyid' => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PartAddToCart($part_number, $jumlah_order, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'parts/partnumber/partaddtocart';
        $header = ['Authorization' => $credential];
        $body = [
            'part_number'   => $part_number,
            'jumlah_order'  => $jumlah_order,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PembayaranFakturDaftar(
        $year,
        $month,
        $kode_sales,
        $kode_dealer,
        $status_pembayaran,
        $nomor_faktur,
        $page,
        $user_id,
        $role_id,
        $companyid
    ) {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/pembayaranfaktur';
        $header = ['Authorization' => $credential];
        $body = [
            'year'              => $year,
            'month'             => $month,
            'kode_sales'        => $kode_sales,
            'kode_dealer'       => $kode_dealer,
            'status_pembayaran' => $status_pembayaran,
            'nomor_faktur'      => $nomor_faktur,
            'page'              => $page,
            'user_id'           => $user_id,
            'role_id'           => $role_id,
            'companyid'         => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PembayaranFakturDetailPerFaktur($nomor_faktur, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/pembayaranfaktur/detailperfaktur';
        $header = ['Authorization' => $credential];
        $body = [
            'nomor_faktur'      => $nomor_faktur,
            'user_id'           => $user_id,
            'role_id'           => $role_id,
            'companyid'         => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PembayaranFakturDetailPerBpk($nomor_bpk, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/pembayaranfaktur/detailperbpk';
        $header = ['Authorization' => $credential];
        $body = [
            'nomor_bpk'         => $nomor_bpk,
            'user_id'           => $user_id,
            'role_id'           => $role_id,
            'companyid'         => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PurchaseOrderFormDaftar($year, $month, $kode_sales, $kode_dealer, $page, $per_page, $role_id, $user_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/purchaseorderform';
        $header = ['Authorization' => $credential];
        $body = [
            'year'          => $year,
            'month'         => $month,
            'kode_sales'    => $kode_sales,
            'kode_dealer'   => $kode_dealer,
            'page'          => $page,
            'per_page'      => $per_page,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PurchaseOrderFormBatalApprove($nomor_pof, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/purchaseorderform/batalapprove';
        $header = ['Authorization' => $credential];
        $body = [
            'nomor_pof'     => $nomor_pof,
            'companyid'     => $companyid,
        ];

        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PurchaseOrderFormTransfer($nomor_pof, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/purchaseorderform/transfer';
        $header = ['Authorization' => $credential];
        $body = [
            'nomor_pof'     => $nomor_pof,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid,
        ];

        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PurchaseOrderFormEditDiscount($nomor_pof, $user_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/purchaseorderform/discount';
        $header = ['Authorization' => $credential];
        $body = [
            'nomor_pof'     => $nomor_pof,
            'user_id'       => $user_id,
            'companyid'     => $companyid,
        ];

        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PurchaseOrderFormUpdateTpc($nomor_pof, $kode_tpc, $user_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/purchaseorderform/tpc/update';
        $header = ['Authorization' => $credential];
        $body = [
            'nomor_pof'     => $nomor_pof,
            'kode_tpc'      => $kode_tpc,
            'user_id'       => $user_id,
            'companyid'     => $companyid,
        ];

        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PurchaseOrderFormUpdateDiscount($nomor_pof, $discount, $user_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/purchaseorderform/discount/update';
        $header = ['Authorization' => $credential];
        $body = [
            'nomor_pof'     => $nomor_pof,
            'discount'      => $discount,
            'user_id'       => $user_id,
            'companyid'     => $companyid,
        ];

        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PurchaseOrderFormDetail($nomor_pof, $user_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/purchaseorderform/detail';
        $header = ['Authorization' => $credential];
        $body = [
            'nomor_pof'     => $nomor_pof,
            'user_id'       => $user_id,
            'companyid'     => $companyid,
        ];

        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PurchaseOrderFormDetailTemp($nomor_pof, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/purchaseorderform/detail/temp';
        $header = ['Authorization' => $credential];
        $body = [
            'nomor_pof'     => $nomor_pof,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid,
        ];

        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PurchaseOrderFormDetailEditPart($nomor_pof, $part_number, $user_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/purchaseorderform/detail/partnumber';
        $header = ['Authorization' => $credential];
        $body = [
            'nomor_pof'     => $nomor_pof,
            'part_number'   => $part_number,
            'user_id'       => $user_id,
            'companyid'     => $companyid,
        ];

        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PurchaseOrderFormDetailSimpanPart($nomor_pof, $part_number, $jml_order, $harga, $discount, $user_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/purchaseorderform/detail/partnumber/simpan';
        $header = ['Authorization' => $credential];
        $body = [
            'nomor_pof'     => $nomor_pof,
            'part_number'   => $part_number,
            'jml_order'     => $jml_order,
            'harga'         => $harga,
            'discount'      => $discount,
            'user_id'       => $user_id,
            'companyid'     => $companyid,
        ];

        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PurchaseOrderFormDetailHapusPart($nomor_pof, $part_number, $user_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/purchaseorderform/detail/partnumber/hapus';
        $header = ['Authorization' => $credential];
        $body = [
            'nomor_pof'     => $nomor_pof,
            'part_number'   => $part_number,
            'user_id'       => $user_id,
            'companyid'     => $companyid,
        ];

        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PurchaseOrderFormFaktur($nomor_pof, $part_number, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/purchaseorderform/faktur';
        $header = ['Authorization' => $credential];
        $body = [
            'nomor_pof'     => $nomor_pof,
            'part_number'   => $part_number,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PurchaseOrderFormSimpan($nomor_pof, $kode_sales, $kode_dealer, $kode_tpc, $umur_pof, $bo, $approve, $keterangan, $user_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/purchaseorderform/simpan';
        $header = ['Authorization' => $credential];
        $body = [
            'nomor_pof'     => $nomor_pof,
            'kode_sales'    => $kode_sales,
            'kode_dealer'   => $kode_dealer,
            'kode_tpc'      => $kode_tpc,
            'umur_pof'      => $umur_pof,
            'bo'            => $bo,
            'approve'       => $approve,
            'keterangan'    => $keterangan,
            'user_id'       => $user_id,
            'companyid'     => $companyid,
        ];

        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PlanningVisitDaftar($tanggal, $kode_sales, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'visit/planningvisit';
        $header = ['Authorization' => $credential];
        $body = [
            'tanggal'       => $tanggal,
            'kode_sales'    => $kode_sales,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PlanningVisitSimpan($tanggal, $kode_sales, $kode_dealer, $keterangan, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'visit/planningvisit/simpan';
        $header = ['Authorization' => $credential];
        $body = [
            'tanggal'       => $tanggal,
            'kode_sales'    => $kode_sales,
            'kode_dealer'   => $kode_dealer,
            'keterangan'    => $keterangan,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PlanningVisitHapus($kode_visit, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'visit/planningvisit/hapus';
        $header = ['Authorization' => $credential];
        $body = [
            'kode_visit'    => $kode_visit,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function SettingClossingMarketing($companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'setting/clossingmkr';
        $header = ['Authorization' => $credential];
        $body = [
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function StockHarianOption($companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'parts/stockharian/option';
        $header = ['Authorization' => $credential];
        $body = [
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function StockHarianProses(
        $companyid,
        $role_id,
        $kode_class,
        $kode_produk,
        $kode_produk_level,
        $kode_sub,
        $frg,
        $kode_lokasi,
        $kode_rak,
        $option_stock_sedia,
        $nilai_stock_sedia
    ) {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'parts/stockharian/proses';
        $header = ['Authorization' => $credential];
        $body = [
            'companyid'             => $companyid,
            'role_id'               => $role_id,
            'kode_class'            => $kode_class,
            'kode_produk'           => $kode_produk,
            'kode_produk_level'     => $kode_produk_level,
            'kode_sub'              => $kode_sub,
            'frg'                   => $frg,
            'kode_lokasi'           => $kode_lokasi,
            'kode_rak'              => $kode_rak,
            'option_stock_sedia'    => $option_stock_sedia,
            'nilai_stock_sedia'     => $nilai_stock_sedia,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function TrackingOrderDaftar($year, $month, $kode_sales, $kode_dealer, $nomor_faktur, $page, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/trackingorder';
        $header = ['Authorization' => $credential];
        $body = [
            'year'          => $year,
            'month'         => $month,
            'kode_sales'    => $kode_sales,
            'kode_dealer'   => $kode_dealer,
            'nomor_faktur'  => $nomor_faktur,
            'page'          => $page,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function TrackingOrderDetail($nomor_faktur, $user_id, $role_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'orders/trackingorder/detail';
        $header = ['Authorization' => $credential];
        $body = [
            'nomor_faktur' => $nomor_faktur,
            'user_id'   => $user_id,
            'role_id'   => $role_id,
            'companyid' => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function UserDaftar($user_id, $role_filter, $page, $role_id)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'profile/users';
        $header = ['Authorization' => $credential];
        $body = [
            'user_id'       => $user_id,
            'role_filter'   => $role_filter,
            'page'          => $page,
            'role_id'       => $role_id,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function UserDetail($user_id)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'profile/users/detail';
        $header = ['Authorization' => $credential];
        $body = [
            'user_id'   => $user_id
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function UserSimpan($user_id, $role_id, $name, $jabatan, $telepon, $photo, $email, $password, $status_user, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'profile/users/simpan';
        $header = ['Authorization' => $credential];
        $body = [
            'user_id'   => $user_id,
            'role_id'   => $role_id,
            'name'      => $name,
            'jabatan'   => $jabatan,
            'telepon'   => $telepon,
            'photo'     => $photo,
            'email'     => $email,
            'password'  => $password,
            'status_user' => $status_user,
            'companyid' => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function ValidasiSalesman($kode_sales, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'validasi/salesman';
        $header = ['Authorization' => $credential];
        $body = [
            'kode_sales'    => $kode_sales,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function ValidasiDealer($kode_dealer, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'validasi/dealer';
        $header = ['Authorization' => $credential];
        $body = [
            'kode_dealer'   => $kode_dealer,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function ValidasiDealerSalesman($kode_sales, $kode_dealer, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'validasi/dealersalesman';
        $header = ['Authorization' => $credential];
        $body = [
            'kode_sales'    => $kode_sales,
            'kode_dealer'   => $kode_dealer,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function ValidasiPartNumber($part_number, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'validasi/partnumber';
        $header = ['Authorization' => $credential];
        $body = [
            'part_number'   => $part_number,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function ValidasiUserIdTidakTerdaftar($user_id, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'validasi/userid/tidakterdaftar';
        $header = ['Authorization' => $credential];
        $body = [
            'user_id'       => $user_id,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function ValidasiEmailTidakTerdaftar($email, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'validasi/email/tidakterdaftar';
        $header = ['Authorization' => $credential];
        $body = [
            'email'         => $email,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DiskonProdukDaftar($page, $per_page, $role_id, $search)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'setting/diskonproduk';
        $header = ['Authorization' => $credential];
        $body = [
            'page'          => trim($page ?? 1),
            'per_page'      => in_array(trim($per_page), [10, 25, 50, 100]) ? $per_page : 10,
            'role_id'       => trim($role_id),
            'search'        => trim($search),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }


    public static function ValidasiProduk($kd_produk)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'validasi/produk';
        $header = ['Authorization' => $credential];
        $body = [
            'kode_produk'            => $kd_produk,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }
    public static function ValidasiDiskonProduk($produk, $cabang)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'setting/diskonproduk/cekproduk';
        $header = ['Authorization' => $credential];
        $body = [
            'cabang'        => $cabang,
            'produk'   => $produk,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DiskonProdukSimpan($cabang, $produk, $disc_normal, $disc_max, $disc_plus_normal, $disc_plus_max, $umur_faktur, $user_id)
    {

        // cek produk pada funsction ValidasiProduk
        $validasiProduk = ApiService::ValidasiProduk($produk);
        $validasiProduk = json_decode($validasiProduk)->status;

        if ($validasiProduk == 1) {
            $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
            $request = 'setting/diskonproduk/simpan';
            $header = ['Authorization' => $credential];
            $body = [
                'cabang'            => trim($cabang),
                'produk'            => trim($produk),
                'disc_normal'       => trim($disc_normal),
                'disc_max'          => trim($disc_max),
                'disc_plus_normal'  => trim($disc_plus_normal),
                'disc_plus_max'     => trim($disc_plus_max),
                'umur_faktur'       => trim($umur_faktur),
                'user_id'           => trim($user_id),
            ];

            $response = ApiRequest::requestPost($request, $header, $body);
            return $response;
        } else {
            return json_encode(['status' => 0, 'message' => 'Produk tidak ditemukan']);
        }
    }

    public static function DiskonProdukHapus($cabang, $produk)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'setting/diskonproduk/hapus';
        $header = ['Authorization' => $credential];
        $body = [
            'cabang'        => trim($cabang),
            'produk'        => trim($produk),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DiskonProdukDealerDaftar($companyid, $page, $per_page, $role_id, $search)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'setting/diskonproduk/dealer';
        $header = ['Authorization' => $credential];
        $body = [
            'companyid'     => trim($companyid),
            'page'          => trim($page ?? 1),
            'per_page'      => in_array(trim($per_page), [10, 25, 50, 100]) ? $per_page : 10,
            'role_id'       => trim($role_id),
            'search'        => trim($search),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DiskonProdukDealerSimpan($produk, $dealer, $keterangan, $companyid, $user_id)
    {

        // cek produk pada funsction ValidasiProduk
        $validasiProduk = ApiService::ValidasiProduk($produk);
        $validasiProduk = json_decode($validasiProduk)->status;

        if ($validasiProduk == 1) {

            // cek dealer pada funsction ValidasiDealer
            $validasiDealer = ApiService::ValidasiDealer($dealer, $companyid);
            $validasiDealer = json_decode($validasiDealer)->status;

            if ($validasiDealer == 1) {

                $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
                $request = 'setting/diskonproduk/dealer/simpan';
                $header = ['Authorization' => $credential];
                $body = [
                    'produk'        => trim($produk),
                    'dealer'        => trim($dealer),
                    'keterangan'    => trim($keterangan),
                    'companyid'     => trim($companyid),
                    'user_id'       => trim($user_id),
                ];

                $response = ApiRequest::requestPost($request, $header, $body);
                return $response;
            } else {
                return json_encode(['status' => 0, 'message' => 'Dealer tidak ditemukan']);
            }
        } else {
            return json_encode(['status' => 0, 'message' => 'Produk tidak ditemukan']);
        }
    }

    public static function DiskonProdukDealerHapus($produk, $dealer, $companyid)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'setting/diskonproduk/dealer/hapus';
        $header = ['Authorization' => $credential];
        $body = [
            'produk'            => trim($produk),
            'dealer'            => trim($dealer),
            'companyid'            => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DiskonDealerDaftar($companyid, $page, $per_page, $role_id, $search)
    {
        $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
        $request = 'setting/harga/partnetto';
        $header = ['Authorization' => $credential];
        $body = [
            'page'          => trim($page ?? 1),
            'per_page'      => in_array(trim($per_page), [10, 25, 50, 100]) ? $per_page : 10,
            'companyid'     => trim($companyid),
            'role_id'       => trim($role_id),
            'search'        => trim($search),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }


    public static function DiskonDealerSimpan($part_number, $status, $harga, $companyid, $user_id)
    {
        // cek part
        $validasiPart = ApiService::ValidasiPartNumber($part_number, $companyid);
        $validasiPart = json_decode($validasiPart)->status;

        if ($validasiPart == 1) {

            $credential = 'Basic ' . base64_encode(config('constants.api_key.api_username') . ':' . config('constants.api_key.api_password'));
            $request = 'setting/harga/partnetto/simpan';
            $header = ['Authorization' => $credential];
            $body = [
                'part_number'        => trim($part_number),
                'status'        => trim($status),
                'harga'    => trim($harga),
                'companyid'     => trim($companyid),
                'user_id'       => trim($user_id),
            ];

            $response = ApiRequest::requestPost($request, $header, $body);
            return $response;
        } else {
            return json_encode(['status' => 0, 'message' => 'Part Number tidak ditemukan']);
        }
    }
}
