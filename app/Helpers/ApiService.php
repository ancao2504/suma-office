<?php

namespace App\Helpers;

use App\Helpers\ApiRequest;

class ApiService
{
    public static function OauthToken()
    {
        $credential = 'Basic '.base64_encode(config('constants.api_key.api_username').':'.config('constants.api_key.api_password'));
        $request = 'oauth/token';
        $header = [ 'Authorization' => $credential ];
        $body = [];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function AuthLogin($email, $password, $remember_me, $user_agent, $ip_address, $token)
    {
        $request = 'auth/login';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'email'         => $email,
            'password'      => $password,
            'remember_me'   => $remember_me,
            'user_agent'    => $user_agent,
            'ip_address'    => $ip_address,
            'token'         => $token,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function AccountProfile($user_id, $companyid)
    {
        $request = 'account/profile';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'user_id'       => $user_id,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function AccountProfileSimpan($user_id, $name, $email, $telepon, $photo, $companyid)
    {
        $request = 'account/profile/simpan';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'account/profile/changepassword';
        $header = ['Authorization' => session()->get('Authorization')];
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

    public static function BackOrderDaftar($page, $per_page, $kode_sales, $kode_dealer, $part_number, $user_id, $role_id, $companyid)
    {
        $request = 'parts/backorder/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'page'          => $page,
            'per_page'      => $per_page,
            'kode_sales'    => $kode_sales,
            'kode_dealer'   => $kode_dealer,
            'part_number'   => $part_number,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    // ============================================================================
    // Cart Index
    // ============================================================================
    public static function CartHeader($user_id, $role_id, $companyid)
    {
        $request = 'orders/cart/header';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function CartEstimasi($user_id, $role_id, $companyid)
    {
        $request = 'orders/cart/estimasi';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'orders/cart/checkout/cekaturanharga';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'user_id'       => $user_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function CartCheckOutProses($password, $password_confirm, $user_id, $companyid)
    {
        $request = 'orders/cart/checkout/proses';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'orders/cart/checkout/result';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'orders/cart/detail';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'orders/cart/deletepart';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'orders/cart/editpart';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'orders/cart/deleteall';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'orders/cart/importexcel';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'orders/cart/prosesexcel';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'orders/cart/reset';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'user_id'       => $user_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function CartSimpanDraft($kode_sales, $kode_dealer, $back_order, $keterangan, $user_id, $role_id, $companyid)
    {
        $request = 'orders/cart/simpandraft';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'orders/cart/simpanpart';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'dashboard/dealer/penjualanbulanan';
        $header = ['Authorization' => session()->get('Authorization')];
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

    public static function DashboardManagementSalesByProduct($year, $month, $fields, $level, $produk, $user_id, $role_id, $companyid)
    {
        $request = 'dashboard/management/sales/byproduct';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'year'          => $year,
            'month'         => $month,
            'fields'        => $fields,
            'level'         => $level,
            'produk'        => $produk,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DashboardManagementSalesByDate($year, $month, $fields, $level, $produk, $user_id, $role_id, $companyid)
    {
        $request = 'dashboard/management/sales/bydate';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'year'          => $year,
            'month'         => $month,
            'fields'        => $fields,
            'level'         => $level,
            'produk'        => $produk,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DashboardManagementStockByProduct($year, $month, $fields, $level, $produk, $user_id, $role_id, $companyid)
    {
        $request = 'dashboard/management/stock/stockbyproduct';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'year'          => $year,
            'month'         => $month,
            'fields'        => $fields,
            'level'         => $level,
            'produk'        => $produk,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DashboardMarketingGroupPerProduk($year, $month, $level_produk, $kode_produk, $jenis_mkr, $kode_mkr, $role_id, $companyid)
    {
        $request = 'dashboard/marketing/pencapaian/perproduk';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'year'          => $year,
            'month'         => $month,
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

    public static function DashboardMarketingGroupPerLevel($year, $jenis_mkr, $kode_mkr, $role_id, $companyid)
    {
        $request = 'dashboard/marketing/pencapaian/perlevel';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'dashboard/marketing/pencapaian/growth';
        $header = ['Authorization' => session()->get('Authorization')];
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

    public static function DashboardSalesmanPenjualanBulanan($year, $month, $jenis_mkr, $kode_mkr, $user_id, $role_id, $companyid)
    {
        $request = 'dashboard/salesman/penjualanbulanan';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'year'              => $year,
            'month'             => $month,
            'jenis_mkr'         => $jenis_mkr,
            'kode_mkr'          => $kode_mkr,
            'user_id'           => $user_id,
            'role_id'           => $role_id,
            'companyid'         => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DashboardSalesmanPenjualanHarian($year, $month, $jenis_mkr, $kode_mkr, $user_id, $role_id, $companyid)
    {
        $request = 'dashboard/salesman/penjualanharian';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'year'              => $year,
            'month'             => $month,
            'jenis_mkr'         => $jenis_mkr,
            'kode_mkr'          => $kode_mkr,
            'user_id'           => $user_id,
            'role_id'           => $role_id,
            'companyid'         => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DealerDaftar($page, $per_page, $kode_dealer, $user_id, $role_id, $companyid)
    {
        $request = 'profile/dealer/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'page'          => $page ?? 1,
            'per_page'      => $per_page ?? 10,
            'kode_dealer'   => $kode_dealer ?? '',
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DealerForm($kode_dealer, $user_id, $role_id, $companyid)
    {
        $request = 'profile/dealer/form';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'kode_dealer' => $kode_dealer,
            'user_id'   => $user_id,
            'role_id'   => $role_id,
            'companyid' => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function FakturDaftar($page, $per_page, $year, $month, $kode_sales, $kode_dealer, $nomor_faktur, $user_id, $role_id, $companyid)
    {
        $request = 'orders/faktur';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'page'          => $page,
            'per_page'      => $per_page,
            'year'          => $year,
            'month'         => $month,
            'kode_sales'    => $kode_sales,
            'kode_dealer'   => $kode_dealer,
            'nomor_faktur'  => $nomor_faktur,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function FakturForm($nomor_faktur, $user_id, $role_id, $companyid)
    {
        $request = 'orders/faktur/form';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'options/company';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionClassProduk()
    {
        $request = 'options/classproduk';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionDealer($search, $page, $per_page, $companyid)
    {
        $request = 'options/dealer';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'options/dealersalesman';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'options/groupproduk';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'level'     => $level,
            'search'    => $search,
            'page'      => $page,
            'per_page'  => $per_page,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionPartNumber($search, $page, $per_page, $companyid)
    {
        $request = 'options/partnumber';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'search'    => $search,
            'page'      => $page,
            'per_page'  => $per_page,
            'companyid' => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionProdukLevel()
    {
        $request = 'options/levelproduk';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionSalesman($search, $page, $per_page, $companyid)
    {
        $request = 'options/salesman';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'options/supervisor';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'options/subproduk';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionTipeMotor($search, $page, $per_page)
    {
        $request = 'options/typemotor';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'options/roleuser';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionUpdateHarga($kode_lokasi, $page, $per_page, $search, $companyid)
    {
        $request = 'options/updateharga';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'kode_lokasi' => $kode_lokasi,
            'page'      => $page,
            'per_page'  => $per_page,
            'search'    => $search,
            'companyid' => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PartNumberDaftar($page, $per_page, $type_motor, $level_produk, $kode_produk, $part_number, $user_id, $role_id, $companyid)
    {
        $request = 'parts/partnumber/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'page'          => $page ?? 1,
            'per_page'      => $per_page ?? 12,
            'type_motor'    => $type_motor,
            'level_produk'  => $level_produk,
            'kode_produk'   => $kode_produk,
            'part_number'   => $part_number,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PartNumberFormCart($part_number, $user_id, $role_id, $companyid)
    {
        $request = 'parts/partnumber/form/cart';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'part_number' => $part_number,
            'user_id'   => $user_id,
            'role_id'   => $role_id,
            'companyid' => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PartNumberTambahCart($part_number, $jumlah_order, $user_id, $role_id, $companyid)
    {
        $request = 'parts/partnumber/form/cart/proses';
        $header = ['Authorization' => session()->get('Authorization')];
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

    public static function PembayaranFakturDaftar($page, $per_page, $year, $month, $kode_sales, $kode_dealer,
        $status_pembayaran, $nomor_faktur, $user_id, $role_id, $companyid) {

        $request = 'orders/pembayaranfaktur';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'page'              => $page,
            'per_page'          => $per_page,
            'year'              => $year,
            'month'             => $month,
            'kode_sales'        => $kode_sales,
            'kode_dealer'       => $kode_dealer,
            'status_pembayaran' => $status_pembayaran,
            'nomor_faktur'      => $nomor_faktur,
            'user_id'           => $user_id,
            'role_id'           => $role_id,
            'companyid'         => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PembayaranFakturDetailPerFaktur($nomor_faktur, $user_id, $role_id, $companyid)
    {
        $request = 'orders/pembayaranfaktur/detailperfaktur';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'orders/pembayaranfaktur/detailperbpk';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_bpk'         => $nomor_bpk,
            'user_id'           => $user_id,
            'role_id'           => $role_id,
            'companyid'         => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PurchaseOrderFormDaftar($page, $per_page, $year, $month, $kode_sales, $kode_dealer, $role_id, $user_id, $companyid)
    {
        $request = 'orders/purchaseorderform';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'page'          => $page,
            'per_page'      => $per_page,
            'year'          => $year,
            'month'         => $month,
            'kode_sales'    => $kode_sales,
            'kode_dealer'   => $kode_dealer,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PurchaseOrderFormBatalApprove($nomor_pof, $companyid)
    {
        $request = 'orders/purchaseorderform/batalapprove';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_pof'     => $nomor_pof,
            'companyid'     => $companyid,
        ];

        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PurchaseOrderFormEditDiscount($nomor_pof, $user_id, $companyid)
    {
        $request = 'orders/purchaseorderform/discount';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'orders/purchaseorderform/tpc/update';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'orders/purchaseorderform/discount/update';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_pof'     => $nomor_pof,
            'discount'      => $discount,
            'user_id'       => $user_id,
            'companyid'     => $companyid,
        ];

        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PurchaseOrderFormDetail($nomor_pof, $user_id, $role_id, $companyid)
    {
        $request = 'orders/purchaseorderform/detail';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_pof'     => $nomor_pof,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid,
        ];

        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PurchaseOrderFormDetailDaftar($nomor_pof, $user_id, $companyid)
    {
        $request = 'orders/purchaseorderform/detail/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_pof'     => $nomor_pof,
            'user_id'       => $user_id,
            'companyid'     => $companyid,
        ];

        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PurchaseOrderFormDetailEditPart($nomor_pof, $part_number, $user_id, $companyid)
    {
        $request = 'orders/purchaseorderform/detail/partnumber';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'orders/purchaseorderform/detail/partnumber/simpan';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'orders/purchaseorderform/detail/partnumber/hapus';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'orders/purchaseorderform/faktur';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'orders/purchaseorderform/simpan';
        $header = ['Authorization' => session()->get('Authorization')];
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

    public static function PlanningVisitDaftar($page, $per_page, $year, $month, $kode_sales, $kode_dealer, $user_id, $role_id, $companyid)
    {
        $request = 'visit/planningvisit/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'page'          => $page,
            'per_page'      => $per_page,
            'year'          => $year,
            'month'         => $month,
            'kode_sales'    => $kode_sales,
            'kode_dealer'   => $kode_dealer,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PlanningVisitSimpan($tanggal, $kode_sales, $kode_dealer, $keterangan, $user_id, $role_id, $companyid)
    {
        $request = 'visit/planningvisit/simpan';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'visit/planningvisit/hapus';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'kode_visit'    => $kode_visit,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function SettingClossingMarketing($companyid)
    {
        $request = 'setting/default/clossingmkr';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function StockHarianOption($companyid)
    {
        $request = 'parts/stockharian/option';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'parts/stockharian/proses';
        $header = ['Authorization' => session()->get('Authorization')];
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

    public static function TrackingOrderDaftar($page, $per_page, $year, $month, $kode_sales, $kode_dealer, $nomor_faktur, $user_id, $role_id, $companyid)
    {
        $request = 'orders/trackingorder';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'page'          => $page,
            'per_page'      => $per_page,
            'year'          => $year,
            'month'         => $month,
            'kode_sales'    => $kode_sales,
            'kode_dealer'   => $kode_dealer,
            'nomor_faktur'  => $nomor_faktur,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function TrackingOrderForm($nomor_faktur, $user_id, $role_id, $companyid)
    {
        $request = 'orders/trackingorder/form';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_faktur' => $nomor_faktur,
            'user_id'   => $user_id,
            'role_id'   => $role_id,
            'companyid' => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function UserDaftar($page, $per_page, $role, $user_id, $role_id)
    {
        $request = 'profile/users/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'page'          => $page,
            'per_page'      => $per_page,
            'role'          => $role,
            'user_id'       => $user_id,
            'role_id'       => $role_id,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function UserForm($user_id)
    {
        $request = 'profile/users/form';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'user_id'   => $user_id
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function UserSimpan($user_id, $role_id, $name, $jabatan, $telepon, $photo, $email, $password, $status_user, $companyid)
    {
        $request = 'profile/users/simpan';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'validasi/salesman';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'kode_sales'    => $kode_sales,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function ValidasiDealer($kode_dealer, $companyid)
    {
        $request = 'validasi/dealer';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'kode_dealer'   => $kode_dealer,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function ValidasiDealerSalesman($kode_sales, $kode_dealer, $companyid)
    {
        $request = 'validasi/dealersalesman';
        $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'validasi/partnumber';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'part_number'   => $part_number,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function ValidasiUserIdTidakTerdaftar($user_id, $companyid)
    {
        $request = 'validasi/userid/tidakterdaftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'user_id'       => $user_id,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function ValidasiEmailTidakTerdaftar($email, $companyid)
    {
        $request = 'validasi/email/tidakterdaftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'email'         => $email,
            'companyid'     => $companyid,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DiskonProdukDaftar($companyid, $page, $per_page, $role_id, $search)
    {
        $request = 'setting/diskonproduk';
        $header = ['Authorization' => session()->get('Authorization')];
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


    public static function ValidasiProduk($kd_produk)
    {
        $request = 'validasi/produk';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'kode_produk'            => $kd_produk,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }
    public static function ValidasiDiskonProduk($produk, $cabang)
    {
        $request = 'setting/diskonproduk/cekproduk';
        $header = ['Authorization' => session()->get('Authorization')];
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

            $request = 'setting/diskonproduk/simpan';
            $header = ['Authorization' => session()->get('Authorization')];
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
        $request = 'setting/diskonproduk/hapus';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'cabang'        => trim($cabang),
            'produk'        => trim($produk),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DiskonProdukDealerDaftar($companyid, $page, $per_page, $role_id, $search)
    {
        $request = 'setting/diskonproduk/dealer';
        $header = ['Authorization' => session()->get('Authorization')];
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
        // cek produk pada function ValidasiProduk
        $validasiProduk = ApiService::ValidasiProduk($produk);
        $validasiProduk = json_decode($validasiProduk)->status;

        if ($validasiProduk == 1) {
            // cek dealer pada function ValidasiDealer
            $validasiDealer = ApiService::ValidasiDealer($dealer, $companyid);
            $validasiDealer = json_decode($validasiDealer)->status;

            if ($validasiDealer == 1) {
                $request = 'setting/diskonproduk/dealer/simpan';
                $header = ['Authorization' => session()->get('Authorization')];
                $body = [
                    'produk'        => trim($produk),
                    'dealer'        => trim($dealer),
                    'keterangan'    => $keterangan,
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
        $request = 'setting/diskonproduk/dealer/hapus';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'produk'            => trim($produk),
            'dealer'            => trim($dealer),
            'companyid'         => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DiskonDealerDaftar($page, $per_page, $role_id, $companyid, $search)
    {
        $request = 'setting/diskon/dealer';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'page'          => trim($page ?? 1),
            'per_page'      => in_array(trim($per_page), [10, 25, 50, 100]) ? $per_page : 10,
            'role_id'       => trim($role_id),
            'companyid'     => trim($companyid),
            'search'        => trim($search),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DiskonDealerSimpan($dealer, $disc_default, $disc_plus, $umur_faktur, $companyid, $user_id)
    {
        // cek dealer
        $validasiDealer = ApiService::ValidasiDealer($dealer, $companyid);
        $validasiDealer = json_decode($validasiDealer)->status;

        if ($validasiDealer == 1) {
            $request = 'setting/diskon/dealer/simpan';
            $header = ['Authorization' => session()->get('Authorization')];
            $body = [
                'dealer'            => trim($dealer),
                'disc_default'      => trim($disc_default),
                'disc_plus'         => trim($disc_plus),
                'umur_faktur'       => trim($umur_faktur),
                'companyid'         => trim($companyid),
                'user_id'           => trim($user_id),
            ];
            $response = ApiRequest::requestPost($request, $header, $body);
            return $response;
        } else {
            return json_encode(['status' => 0, 'message' => 'Dealer tidak ditemukan']);
        }
    }

    public static function DiskonDealerHapus($dealer, $companyid)
    {
        $request = 'setting/diskon/dealer/hapus';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'dealer'            => trim($dealer),
            'companyid'         => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }



    public static function HargaNettoPartDaftar($page, $per_page, $companyid, $role_id, $search)
    {
        $request = 'setting/harga/partnetto';
        $header = ['Authorization' => session()->get('Authorization')];
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


    public static function HargaNettoPartSimpan($part_number, $status, $harga, $companyid, $user_id)
    {
        // cek part
        $validasiPart = ApiService::ValidasiPartNumber($part_number, $companyid);
        $validasiPart = json_decode($validasiPart)->status;

        if ($validasiPart == 1) {
            $request = 'setting/harga/partnetto/simpan';
            $header = ['Authorization' => session()->get('Authorization')];
            $body = [
                'part_number'   => trim($part_number),
                'status'        => trim($status),
                'harga'         => trim($harga),
                'companyid'     => trim($companyid),
                'user_id'       => trim($user_id),
            ];

            $response = ApiRequest::requestPost($request, $header, $body);
            return $response;
        } else {
            return json_encode(['status' => 0, 'message' => 'Part Number tidak ditemukan']);
        }
    }

    public static function HargaNettoPartDealerDaftar($page, $per_page, $role_id, $companyid, $search)
    {
        $request = 'setting/harga/partnetto/dealer';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'page'          => trim($page ?? 1),
            'per_page'      => in_array(trim($per_page), [10, 25, 50, 100]) ? $per_page : 10,
            'role_id'       => trim($role_id),
            'companyid'     => trim($companyid),
            'search'        => trim($search),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function HargaNettoPartDealerSimpan($part_number, $dealer, $harga, $keterangan, $companyid, $user_id)
    {
        // dd($part_number, $dealer, $harga, $keterangan, $companyid, $user_id);
        // cek part
        $validasiPart = ApiService::ValidasiPartNumber($part_number, $companyid);
        $validasiPart = json_decode($validasiPart)->status;

        if ($validasiPart == 1) {

            // cek dealer
            $validasiDealer = ApiService::ValidasiDealer($dealer, $companyid);
            $validasiDealer = json_decode($validasiDealer)->status;

            if ($validasiDealer == 1)  {
                $request = 'setting/harga/partnetto/dealer/simpan';
                $header = ['Authorization' => session()->get('Authorization')];
                $body = [
                    'part_number'   => trim($part_number),
                    'dealer'        => trim($dealer),
                    'harga'         => trim($harga),
                    'keterangan'    => $keterangan,
                    'companyid'     => trim($companyid),
                    'user_id'       => trim($user_id),
                ];

                $response = ApiRequest::requestPost($request, $header, $body);
                return $response;
            } else {
                return json_encode(['status' => 0, 'message' => 'Dealer tidak ditemukan']);
            }
        } else {
            return json_encode(['status' => 0, 'message' => 'Part Number tidak ditemukan']);
        }
    }


    public static function HargaNettoPartDealerHapus($part_number, $dealer, $companyid)
    {
        $request = 'setting/harga/partnetto/dealer/hapus';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'part_number'       => trim($part_number),
            'dealer'            => trim($dealer),
            'companyid'         => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function CekPenerimaanSuratJalan($nomor_serah_terima, $companyid)
    {
        $request = 'orders/penerimaan/sj';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_serah_terima'        => trim($nomor_serah_terima),
            'companyid'   => trim($companyid),
        ];

        // dd($nomor_sj, $companyid);
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PenerimaanSuratJalanSimpan($nomor_sj, $tanggal, $jam, $companyid)
    {
        $request = 'orders/penerimaan/sj/simpan';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_sj'   => trim($nomor_sj),
            'tanggal'        => trim($tanggal),
            'jam'         => trim($jam),
            'companyid'     => trim($companyid),
        ];

        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PenerimaanSuratJalanHapus($nomor_sj, $companyid)
    {
        $request = 'orders/penerimaan/sj/hapus';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_sj'       => trim($nomor_sj),
            'companyid'      => trim($companyid),
        ];

        $response = ApiRequest::requestPost($request, $header, $body);

        return $response;
    }

    public static function PenerimaanSuratJalanReport($start_date, $end_date, $companyid, $no_serah_terima, $driver)
    {
        $request = 'orders/penerimaan/sj/report';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'start_date'           => date('Y-m-d', strtotime(trim($start_date))),
            'end_date'           => date('Y-m-d', strtotime(trim($end_date))),
            'companyid'         => trim($companyid),
            'no_serah_terima'   => trim($no_serah_terima),
            'driver'            => strtoupper(trim($driver)),
        ];

        $response = ApiRequest::requestPost($request, $header, $body);

        return $response;
    }


    public static function pembayaranDealerDaftar($kd_dealer, $companyid)
    {
        $request = 'orders/penerimaan/pembayaran/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'dealer'        => trim($kd_dealer),
            'cabang'        => strtoupper(trim($companyid)),
        ];

        $response = ApiRequest::requestPost($request, $header, $body);

        return $response;
    }

    public static function PembayaranDealerSimpan($kd_dealer,$jenis_transaksi, $total, $detail,$file_names, $user_id, $companyid)
    {
        $request = 'orders/penerimaan/pembayaran/simpan';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'kd_dealer'         => trim($kd_dealer),
            'jenis_transaksi'   => trim($jenis_transaksi),
            'total'             => trim($total),
            'detail'            => $detail,
            'file_names'        => $file_names,
            'user_id'           => trim($user_id),
            'cabang'            => strtoupper(trim($companyid)),
        ];

        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function SettingCetakUlangDaftar($year, $month, $jenis, $page, $per_page, $role_id, $companyid)
    {
        $request = 'setting/cetakulang/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'year'      => trim($year),
            'month'     => trim($month),
            'jenis'     => trim($jenis),
            'page'      => $page ?? 1,
            'per_page'  => $per_page ?? 10,
            'role_id'   => strtoupper(trim($role_id)),
            'companyid' => strtoupper(trim($companyid)),
        ];

        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function SettingCetakUlangCekDokumen($nomor_dokumen, $jenis_transaksi, $divisi, $role_id)
    {
        $request = 'setting/cetakulang/cekdokumen';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'no_dokumen'        => trim($nomor_dokumen),
            'jenis_transaksi'   => trim($jenis_transaksi),
            'divisi'            => trim($divisi),
            'role_id'           => strtoupper(trim($role_id))
        ];

        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function SettingCetakUlangSimpan($nomor_dokumen, $jenis_transaksi, $divisi, $kode_cabang, $company_dokumen, $approve, $edit, $alasan, $role_id, $companyid, $user_id)
    {
        $request = 'setting/cetakulang/simpan';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'no_dokumen'        => trim($nomor_dokumen),
            'jenis_transaksi'   => trim($jenis_transaksi),
            'divisi'            => trim($divisi),
            'kode_cabang'       => trim($kode_cabang),
            'company_dokumen'   => trim($company_dokumen),
            'approve'           => trim($approve),
            'edit'              => trim($edit),
            'alasan'            => trim($alasan),
            'user_id'           => trim($user_id),
            'role_id'           => strtoupper(trim($role_id)),
            'companyid'         => strtoupper(trim($companyid)),
        ];

        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlinePemindahanTokopediaDaftar($page, $per_page, $start_date, $end_date, $nomor_dokumen, $companyid)
    {
        $request = 'online/pemindahan/tokopedia/daftar';
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

    public static function OnlinePemindahanTokopediaForm($nomor_dokumen, $companyid)
    {
        $request = 'online/pemindahan/tokopedia/form';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlinePemindahanTokopediaFormDetail($nomor_dokumen, $companyid)
    {
        $request = 'online/pemindahan/tokopedia/form/detail';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlinePemindahanTokopediaUpdatePerPartNumber($nomor_dokumen, $part_number, $companyid)
    {
        $request = 'online/pemindahan/tokopedia/form/update/partnumber';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'part_number'   => trim($part_number),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlinePemindahanTokopediaUpdateStatusPerPartNumber($nomor_dokumen, $part_number, $companyid)
    {
        $request = 'online/pemindahan/tokopedia/form/update/statuspartnumber';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'part_number'   => trim($part_number),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlinePemindahanTokopediaUpdatePerNomorDokumen($nomor_dokumen, $companyid)
    {
        $request = 'online/pemindahan/tokopedia/form/update/dokumen';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlineUpdateHargaTokopediaDaftar($page, $per_page, $year, $month, $search, $companyid)
    {
        $request = 'online/updateharga/tokopedia/daftar';
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

    public static function OnlineUpdateHargaBuatDokumen($nomor_dokumen, $tanggal, $companyid, $user_id)
    {
        $request = 'online/updateharga/tokopedia/buatdokumen';
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

    public static function OnlineUpdateHargaTokopediaForm($nomor_dokumen, $companyid)
    {
        $request = 'online/updateharga/tokopedia/form';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'companyid'     => trim($companyid)
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

    public static function OnlineUpdateHargaTokopediaUpdatePerPartNumber($nomor_dokumen, $part_number, $companyid)
    {
        $request = 'online/updateharga/tokopedia/update/partnumber';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'part_number'   => trim($part_number),
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

    public static function OnlineUpdateHargaTokopediaUpdateStatusPartNumber($nomor_dokumen, $part_number, $companyid)
    {
        $request = 'online/updateharga/tokopedia/update/statuspartnumber';
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

    public static function OnlineUpdateHargaTokopediaUpdatePerNomorDokumen($nomor_dokumen, $companyid)
    {
        $request = 'online/updateharga/tokopedia/update/dokumen';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
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

    public static function OnlineProductTokopediaSearchPartNumber($part_number, $companyid)
    {
        $request = 'online/products/tokopedia/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'part_number'   => trim($part_number),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlineProductTokopediaCekProductId($product_id, $companyid)
    {
        $request = 'online/products/tokopedia/cek/productid';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'product_id'    => trim($product_id),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlineProductTokopediaUpdateProductId($part_number, $product_id, $companyid)
    {
        $request = 'online/products/tokopedia/update';
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
