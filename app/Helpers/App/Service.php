<?php

namespace App\Helpers\App;

use App\Helpers\App\ApiRequest;
use Illuminate\Support\Facades\Auth;


class Service
{
    public static function OauthToken($request)
    {
        $credential = 'Basic '.base64_encode(config('constants.app.key.username').':'.config('constants.app.key.password'));
        $url = 'oauth/token';
        $header = [ 'Authorization' => $credential ];
        $body = [
            'email'         => $request->email,
            'password'      => $request->password
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
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
        $request = 'backend/account/profile';
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
        $request = 'backend/account/profile/simpan';
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
        $request = 'backend/account/profile/changepassword';
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
        $request = 'backend/parts/backorder/daftar';
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
        $request = 'backend/orders/cart/header';
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
        $request = 'backend/orders/cart/estimasi';
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
        $request = 'backend/orders/cart/checkout/cekaturanharga';
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
        $request = 'backend/orders/cart/checkout/proses';
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
        $request = 'backend/orders/cart/checkout/result';
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
        $request = 'backend/orders/cart/detail';
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
        $request = 'backend/orders/cart/deletepart';
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
        $request = 'backend/orders/cart/editpart';
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
        $request = 'backend/orders/cart/deleteall';
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
        $request = 'backend/orders/cart/importexcel';
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
        $request = 'backend/orders/cart/prosesexcel';
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
        $request = 'backend/orders/cart/reset';
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
        $request = 'backend/orders/cart/simpandraft';
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
        $request = 'backend/orders/cart/simpanpart';
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
        $request = 'backend/dashboard/dealer/penjualanbulanan';
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
        $request = 'backend/dashboard/management/sales/byproduct';
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
        $request = 'backend/dashboard/management/sales/bydate';
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
        $request = 'backend/dashboard/management/stock/stockbyproduct';
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

    public static function DashboardManagementKuartal($year, $fields, $option_company, $companyid, $kabupaten,
                                $supervisor, $salesman, $produk, $user_id)
    {
        $request = 'backend/dashboard/management/kuartal/index';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'year'          => $year,
            'fields'        => $fields,
            'option_company'=> $option_company,
            'companyid'     => $companyid,
            'kabupaten'     => $kabupaten,
            'supervisor'    => $supervisor,
            'salesman'      => $salesman,
            'produk'        => $produk,
            'user_id'       => $user_id,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DashboardMarketingGroupPerProduk($year, $month, $level_produk, $kode_produk, $jenis_mkr, $kode_mkr, $role_id, $companyid)
    {
        $request = 'backend/dashboard/marketing/pencapaian/perproduk';
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
        $request = 'backend/dashboard/marketing/pencapaian/perlevel';
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
        $request = 'backend/dashboard/marketing/pencapaian/growth';
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
        $request = 'backend/dashboard/salesman/penjualanbulanan';
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
        $request = 'backend/dashboard/salesman/penjualanharian';
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

    public static function DashboardMarketplaceSalesByDate($year, $month, $companyid)
    {
        $request = 'backend/dashboard/marketplace/salesbydate';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'year'              => $year,
            'month'             => $month,
            'companyid'         => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DashboardMarketplaceSalesByLocation($year, $month, $companyid)
    {
        $request = 'backend/dashboard/marketplace/salesbylocation';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'year'              => $year,
            'month'             => $month,
            'companyid'         => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DealerDaftar($page, $per_page, $kode_dealer, $user_id, $role_id, $companyid)
    {
        $request = 'backend/profile/dealer/daftar';
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
        $request = 'backend/profile/dealer/form';
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
        $request = 'backend/orders/faktur';
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
        $request = 'backend/orders/faktur/form';
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

    public static function OptionCompany($search, $page, $per_page)
    {
        $request = 'backend/options/company';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'search'        => $search,
            'page'          => $page,
            'per_page'      => $per_page,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionClassProduk()
    {
        $request = 'backend/options/classproduk';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionDealer($search, $page, $per_page, $companyid)
    {
        $request = 'backend/options/dealer';
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
        $request = 'backend/options/dealersalesman';
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

    public static function OptionEkspedisiOnline()
    {
        $request = 'backend/options/ekspedisionline';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionGroupProduk($level, $search, $page, $per_page)
    {
        $request = 'backend/options/groupproduk';
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

    public static function OptionKabupaten($search, $page, $per_page)
    {
        $request = 'backend/options/kabupaten';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'search'    => $search,
            'page'      => $page,
            'per_page'  => $per_page
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionPartNumber($search, $page, $per_page, $companyid)
    {
        $request = 'backend/options/partnumber';
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
        $request = 'backend/options/levelproduk';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionSalesman($search, $page, $per_page, $companyid)
    {
        $request = 'backend/options/salesman';
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
        $request = 'backend/options/supervisor';
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
        $request = 'backend/options/subproduk';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionTipeMotor($search, $page, $per_page)
    {
        $request = 'backend/options/typemotor';
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
        $request = 'backend/options/roleuser';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OptionUpdateHarga($kode_lokasi, $page, $per_page, $search, $companyid)
    {
        $request = 'backend/options/updateharga';
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
        $request = 'backend/parts/partnumber/daftar';
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
        $request = 'backend/parts/partnumber/form/cart';
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
        $request = 'backend/parts/partnumber/form/cart/proses';
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

        $request = 'backend/orders/pembayaranfaktur';
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
        $request = 'backend/orders/pembayaranfaktur/detailperfaktur';
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
        $request = 'backend/orders/pembayaranfaktur/detailperbpk';
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
        $request = 'backend/orders/purchaseorderform';
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
        $request = 'backend/orders/purchaseorderform/batalapprove';
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
        $request = 'backend/orders/purchaseorderform/discount';
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
        $request = 'backend/orders/purchaseorderform/tpc/update';
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
        $request = 'backend/orders/purchaseorderform/discount/update';
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
        $request = 'backend/orders/purchaseorderform/detail';
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
        $request = 'backend/orders/purchaseorderform/detail/daftar';
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
        $request = 'backend/orders/purchaseorderform/detail/partnumber';
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
        $request = 'backend/orders/purchaseorderform/detail/partnumber/simpan';
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
        $request = 'backend/orders/purchaseorderform/detail/partnumber/hapus';
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
        $request = 'backend/orders/purchaseorderform/faktur';
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
        $request = 'backend/orders/purchaseorderform/simpan';
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
        $request = 'backend/visit/planningvisit/daftar';
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
        $request = 'backend/visit/planningvisit/simpan';
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
        $request = 'backend/visit/planningvisit/hapus';
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
        $request = 'backend/setting/default/clossingmkr';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function StockHarianOption($companyid)
    {
        $request = 'backend/parts/stockharian/option';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => $companyid
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function StockHarianProsesPerlokasi($companyid, $role_id, $kode_class, $kode_produk,
                    $kode_produk_level, $kode_sub, $frg, $kode_lokasi, $kode_rak,
                    $option_stock_sedia, $nilai_stock_sedia) {
        $request = 'backend/parts/stockharian/proses/perlokasi';
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

    public static function StockHarianProsesMarketplace($companyid, $role_id, $kode_class, $kode_produk,
                    $kode_produk_level, $kode_sub, $frg, $kode_lokasi, $kode_rak,
                    $option_stock_sedia, $nilai_stock_sedia) {
        $request = 'backend/parts/stockharian/proses/marketplace';
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
        $request = 'backend/orders/trackingorder';
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
        $request = 'backend/orders/trackingorder/form';
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
        $request = 'backend/profile/users/daftar';
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
        $request = 'backend/profile/users/form';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'user_id'   => $user_id
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function UserSimpan($user_id, $role_id, $name, $jabatan, $telepon, $photo, $email, $password, $status_user, $companyid)
    {
        $request = 'backend/profile/users/simpan';
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
        $request = 'backend/validasi/salesman';
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
        $request = 'backend/validasi/dealer';
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
        $request = 'backend/validasi/dealersalesman';
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
        $request = 'backend/validasi/partnumber';
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
        $request = 'backend/validasi/userid/tidakterdaftar';
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
        $request = 'backend/validasi/email/tidakterdaftar';
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
        $request = 'backend/setting/diskonproduk';
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
        $request = 'backend/validasi/produk';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'kode_produk'            => $kd_produk,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }
    public static function ValidasiDiskonProduk($produk, $cabang)
    {
        $request = 'backend/setting/diskonproduk/cekproduk';
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
        $validasiProduk = Service::ValidasiProduk($produk);
        $validasiProduk = json_decode($validasiProduk)->status;

        if ($validasiProduk == 1) {

            $request = 'backend/setting/diskonproduk/simpan';
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
        $request = 'backend/setting/diskonproduk/hapus';
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
        $request = 'backend/setting/diskonproduk/dealer';
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
        $validasiProduk = Service::ValidasiProduk($produk);
        $validasiProduk = json_decode($validasiProduk)->status;

        if ($validasiProduk == 1) {
            // cek dealer pada function ValidasiDealer
            $validasiDealer = Service::ValidasiDealer($dealer, $companyid);
            $validasiDealer = json_decode($validasiDealer)->status;

            if ($validasiDealer == 1) {
                $request = 'backend/setting/diskonproduk/dealer/simpan';
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
        $request = 'backend/setting/diskonproduk/dealer/hapus';
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
        $request = 'backend/setting/diskon/dealer';
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
        $validasiDealer = Service::ValidasiDealer($dealer, $companyid);
        $validasiDealer = json_decode($validasiDealer)->status;

        if ($validasiDealer == 1) {
            $request = 'backend/setting/diskon/dealer/simpan';
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
        $request = 'backend/setting/diskon/dealer/hapus';
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
        $request = 'backend/setting/harga/partnetto';
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
        $validasiPart = Service::ValidasiPartNumber($part_number, $companyid);
        $validasiPart = json_decode($validasiPart)->status;

        if ($validasiPart == 1) {
            $request = 'backend/setting/harga/partnetto/simpan';
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
        $request = 'backend/setting/harga/partnetto/dealer';
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
        // cek part
        $validasiPart = Service::ValidasiPartNumber($part_number, $companyid);
        $validasiPart = json_decode($validasiPart)->status;

        if ($validasiPart == 1) {

            // cek dealer
            $validasiDealer = Service::ValidasiDealer($dealer, $companyid);
            $validasiDealer = json_decode($validasiDealer)->status;

            if ($validasiDealer == 1)  {
                $request = 'backend/setting/harga/partnetto/dealer/simpan';
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
        $request = 'backend/setting/harga/partnetto/dealer/hapus';
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
        $request = 'backend/orders/penerimaan/sj';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_serah_terima'        => trim($nomor_serah_terima),
            'companyid'   => trim($companyid),
        ];

        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PenerimaanSuratJalanSimpan($nomor_sj, $tanggal, $jam, $companyid)
    {
        $request = 'backend/orders/penerimaan/sj/simpan';
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
        $request = 'backend/orders/penerimaan/sj/hapus';
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
        $request = 'backend/orders/penerimaan/sj/report';
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
        $request = 'backend/orders/penerimaan/pembayaran/daftar';
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
        $request = 'backend/orders/penerimaan/pembayaran/simpan';
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
        $request = 'backend/setting/cetakulang/daftar';
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
        $request = 'backend/setting/cetakulang/cekdokumen';
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
        $request = 'backend/setting/cetakulang/simpan';
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

    public static function PartImageDaftar($search, $page){
        $request = 'backend/parts/partnumber/image/list';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'search' => trim($search),
            'page'      => $page ?? 1,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlineSerahTerimaDaftar($page, $per_page, $start_date, $end_date, $search, $companyid)
    {
        $request = 'backend/online/serahterima/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'page'          => $page ?? 1,
            'per_page'      => $per_page ?? 10,
            'start_date'    => $start_date,
            'end_date'      => $end_date,
            'search'        => trim($search),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlineSerahTerimaForm($nomor_dokumen, $companyid)
    {
        $request = 'backend/online/serahterima/form';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => $nomor_dokumen,
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlineSerahTerimaRequestPickupPerNomorFaktur($nomor_faktur, $companyid)
    {
        $request = 'backend/online/serahterima/request/pickup';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_faktur'  => $nomor_faktur,
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlineSerahTerimaUpdateStatusPerNoFaktur($nomor_faktur, $companyid)
    {
        $request = 'backend/online/serahterima/update/status';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_faktur'  => $nomor_faktur,
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function OnlineApproveOrderDaftar($page, $per_page, $companyid)
    {
        $request = 'backend/online/order/approve/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'page'          => $page ?? 1,
            'per_page'      => $per_page ?? 10,
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function SearchProductMarketplaceByPartNumber($part_number, $companyid, $page, $per_page)
    {
        $request = 'backend/online/products/marketplace/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'part_number'   => trim($part_number),
            'companyid'     => trim($companyid),
            'page'          => $page,
            'per_page'      => $per_page,
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function DetailProductMarketplaceByPartNumber($part_number, $companyid)
    {
        $request = 'backend/online/products/marketplace/detail';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'part_number'   => trim($part_number),
            'companyid'     => trim($companyid)
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PemindahanMarketplaceDaftar($search,$start_date,$end_date,$companyid,$page,$per_page)
    {
        $request = 'backend/online/pemindahan/marketplace/daftar';
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

    public static function PemindahanMarketplaceDetail($nomor_dokumen ,$companyid)
    {
        $request = 'backend/online/pemindahan/marketplace/detail';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen'     => trim($nomor_dokumen),
            'companyid'         => trim($companyid)
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function UpdateStockMarketplace($nomor_dokumen, $kode_part, $companyid){
        $request = 'backend/online/pemindahan/marketplace/update/stock';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'kode_part'     => trim($kode_part),
            'companyid'     => trim($companyid)
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function PemindahanUpdateStatusMarketplacePerPartNumber($nomor_dokumen, $part_number, $companyid)
    {
        $request = 'backend/online/pemindahan/marketplace/update/statuspartnumber';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_dokumen' => trim($nomor_dokumen),
            'part_number'   => trim($part_number),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function addProductMarketplace($companyid, $image, $nama, $merek, $deskripsi, $harga, $stok, $min_order, $berat, $ukuran, $sku, $kondisi, $kategori, $status, $etalase, $logistic)
    {
        $request = 'backend/online/products/marketplace/add';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => trim($companyid),
            'image'         => $image,
            'nama'          => $nama,
            'merek'         => $merek,
            'deskripsi'     => $deskripsi,
            'harga'         => $harga,
            'stok'          => $stok,
            'min_order'     => $min_order,
            'berat'         => $berat,
            'ukuran'        => $ukuran,
            'sku'           => $sku,
            'kondisi'       => $kondisi,
            'kategori'      => $kategori,
            'status'        => $status,
            'etalase'       => $etalase,
            'logistic'      => $logistic,
        ];

        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function ApproveOrderMarketplaceDetail($nomor_faktur, $companyid)
    {
        $request = 'backend/online/order/approve/form/internal';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_faktur'   => trim($nomor_faktur),
            'companyid'     => trim($companyid),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function ApproveOrderMarketplaceProses($nomor_invoice, $companyid, $user_id)
    {
        $request = 'backend/online/order/approve/proses/marketplace';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_invoice'     => trim($nomor_invoice),
            'companyid'         => trim($companyid),
            'user_id'           => trim($user_id),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function ApproveOrderInternalProses($nomor_faktur, $companyid, $user_id)
    {
        $request = 'backend/online/order/approve/proses/internal';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nomor_faktur'      => trim($nomor_faktur),
            'companyid'         => trim($companyid),
            'user_id'           => trim($user_id),
        ];
        $response = ApiRequest::requestPost($request, $header, $body);
        return $response;
    }

    public static function ReturKonsumenDaftar($request)
    {
        $url = 'backend/retur/konsumen/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'role_id'       => strtoupper(trim($request->session()->get('app_user_role_id'))),
            'option'        => $request->option,
            'no_retur'      => trim($request->no_retur),
            'page'          => $request->page ?? 1,
            'per_page'      => $request->per_page ?? 10,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }
    public static function ReturSupplierDaftar($request)
    {
        $url = 'backend/retur/supplier/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'option'        => $request->option,
            'no_retur'      => trim($request->no_retur),
            'page'          => $request->page ?? 1,
            'per_page'      => $request->per_page ?? 10,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function ReturKonsumenSimpan($request)
    {
        $url = 'backend/retur/konsumen/simpan';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'role_id'       => strtoupper(trim($request->session()->get('app_user_role_id'))),
            'tamp'          => $request->tamp,
            'option'        => trim($request->option),
            // ! data Header
            'no_retur'      => $request->no_retur,
            'tgl_retur'     => $request->tgl_retur,
            'kd_sales'      => trim($request->kd_sales),
            'pc'            => $request->pc,
            'kd_dealer'     => trim($request->kd_dealer),
            'kd_cabang'     => trim($request->kd_cabang),
            // ! data detail
            'no_faktur'     => trim($request->no_faktur),
            'no_produksi'   => $request->no_produksi,
            'kd_part'       => trim($request->kd_part),
            'qty_retur'     => $request->qty_retur,
            'ket'           => $request->ket,
            'sts_stock'     => $request->sts_stock,
            'sts_klaim'     => $request->sts_klaim,
            'sts_min'       => $request->sts_minimum,
            'tgl_klaim'     => $request->tgl_klaim,
            'tgl_pakai'     => $request->tgl_pakai
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }
    public static function ReturSupplierSimpan($request)
    {
        $url = 'backend/retur/supplier/simpan';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'option'        => trim($request->option),
            // ! data Header
            'no_retur'      => $request->no_retur,
            'kd_supp'       => trim($request->kd_supp),
            'tgl_retur'     => $request->tgl_retur,
            // ! data detail
            'no_klaim'      => trim($request->no_klaim),
            'kd_part'       => trim($request->kd_part),
            'no_ps'         => trim($request->no_ps),
            'ket'           => $request->ket,
            'diterima'      => $request->diterima,
            'no_produksi'   => trim($request->no_produksi),
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }
    public static function ReturSupplierjawabSimpan($request)
    {
        $url = 'backend/retur/supplier/jawab/simpan';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'tamp'          => (boolean)$request->tamp,
            'no_retur'      => $request->no_retur,
            'no_klaim'      => trim($request->no_klaim),
            'kd_part'       => trim($request->kd_part),
            'no_produksi'   => $request->no_produksi,
            'qty_jwb'       => (float)$request->qty_jwb,
            'alasan'        => $request->alasan,
            'ca'            => (float)$request->ca,
            'keputusan'     => $request->keputusan,
            'ket'           => $request->ket,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }
    public static function ReturKonsumenDelete($request)
    {
        $url = 'backend/retur/konsumen/delete';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'no_retur'      => $request->no_retur,
            'no_faktur'     => $request->no_faktur,
            'kd_part'       => trim($request->kd_part),
            'no_produksi'   => $request->no_produksi,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }
    public static function ReturSupplierDelete($request)
    {
        $url = 'backend/retur/supplier/delete';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'no_klaim' => $request->no_klaim,
            'kd_part' => $request->kd_part,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function ReturSupplierJwbDelete($request)
    {
        $url = 'backend/retur/supplier/jawab/delete';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'no_retur' => $request->no_retur,
            'no_klaim' => $request->no_klaim,
            'kd_part' => $request->kd_part,
            'no_jwb'      => $request->no_jwb,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function ReportFakturData($request){
        $url = 'backend/report/faktur';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'tgl_faktur'    => $request->tgl_faktur,
            'kd_sales'      => $request->kd_sales,
            'kd_produk'     => $request->kd_produk,
            'page'          => $request->page ?? 1,
            'per_page'      => $request->per_page ?? 10,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function ExprotReportFaktur($request){
        $url = 'backend/report/faktur/export';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'tgl_faktur'    => $request->tgl_faktur,
            'kd_sales'      => $request->kd_sales,
            'kd_produk'     => $request->kd_produk,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function ReportReturData($request){
        $url = 'backend/report/retur';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'tanggal'     => $request->tanggal,
            'kd_sales'      => $request->kd_sales,
            'kd_dealer'     => $request->kd_dealer,
            'page'          => $request->page ?? 1,
            'per_page'      => $request->per_page ?? 10,
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function ReportPackingData($request){
        $url = 'backend/report/packing';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'tanggal'     => $request->tanggal,
            'no_meja'      => $request->no_meja,
            'kd_packer'     => $request->kd_packer,
            'jenis_data'    => $request->jenis_data,
            'group_by'      => $request->group_by,
            'page'          => $request->page ?? 1,
            'per_page'      => $request->per_page ?? 10,
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function ExprotReportRetur($request){
        $url = 'backend/report/retur/export';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'tanggal'     => $request->tanggal,
            'kd_sales'      => $request->kd_sales,
            'kd_dealer'     => $request->kd_dealer
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function ExprotReportPacking($request){
        $url = 'backend/report/packing/export';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'tanggal'     => $request->tanggal,
            'no_meja'      => $request->no_meja,
            'kd_packer'     => $request->kd_packer,
            'jenis_data'    => $request->jenis_data,
            'group_by'      => $request->group_by
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function ReportKonsumenData($request){
        $url = 'backend/report/konsumen/daftar';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'user_id'       => trim($request->session()->get('app_user_id')),
            'divisi'        => $request->get('divisi'),
            'companyid'     => $request->get('companyid'),
            'kd_lokasi'     => $request->get('kd_lokasi'),
            'tgl_transaksi' => $request->get('tgl_transaksi'),
            'tgl_lahir'     => $request->get('tgl_lahir'),
            'jenis_part'    => $request->get('jenis_part'),
            'kd_part'       => $request->get('kd_part'),
            'merek_motor'   => $request->get('merek_motor'),
            'tipe_motor'    => $request->get('tipe_motor'),
            'jenis_motor'   => $request->get('jenis_motor'),
            'page'          => $request->page ?? 1,
            'per_page'      => $request->per_page ?? 10,
            'filter'        => $request->get('filter'),
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function exportDaftarKonsumen($request){
        $url = 'backend/report/konsumen/daftar/export';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'user_id'       => trim($request->session()->get('app_user_id')),
            'divisi'        => $request->get('divisi'),
            'companyid'     => $request->get('companyid'),
            'kd_lokasi'     => $request->get('kd_lokasi'),
            'tgl_transaksi' => $request->get('tgl_transaksi'),
            'tgl_lahir'     => $request->get('tgl_lahir'),
            'jenis_part'    => $request->get('jenis_part'),
            'kd_part'       => $request->get('kd_part'),
            'merek_motor'   => $request->get('merek_motor'),
            'tipe_motor'    => $request->get('tipe_motor'),
            'jenis_motor'   => $request->get('jenis_motor'),
            'filter'        => $request->get('filter'),
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function dataSupplier($request){
        $url = 'backend/supplier';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'option'        => $request->option,
            'kd_supplier'   => $request->kd_supplier,
            'page'          => $request->page ?? 1,
            'per_page'      => $request->per_page ?? 10,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function dataSalesman($request){
        $url = 'backend/salesman';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'option'        => $request->option,
            'kd_spv'        => $request->kd_spv,
            'kd_sales'      => $request->kd_sales,
            'page'          => $request->page ?? 1,
            'per_page'      => $request->per_page ?? 10,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }
    public static function dataUkuranRing(){
        $url = 'backend/ukuranring';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function dataMerekmotor($request){
        $url = 'backend/merekmotor';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'option'        => $request->option,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function dataTypemotor($request){
        $url = 'backend/typemotor';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'option'        => $request->option,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function dataDealer($request){
        $url = 'backend/dealer';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'option'        => $request->option,
            'kd_sales'      => $request->kd_sales,
            'kd_dealer'     => $request->kd_dealer,
            'page'          => $request->page ?? 1,
            'per_page'      => $request->per_page ?? 10,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function dataFakturKonsumen($request){
        $url = 'backend/faktur/konsumen';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => ($request->companyid??strtoupper(trim($request->session()->get('app_user_company_id')))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'divisi'        => $request->divisi,
            'option'        => $request->option,
            'kd_lokasi'     => $request->kd_lokasi,
            'no_faktur'     => $request->no_faktur,
            'kd_sales'      => $request->kd_sales,
            'kd_dealer'     => $request->kd_dealer,
            'id_konsumen'   => $request->id_konsumen,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function dataKonsumen($request){
        $url = 'backend/option/konsumen';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'option'        => $request->option,
            'nik'           => $request->nik,
            'search'        => $request->search,
            'page'          => $request->page ?? 1,
            'per_page'      => $request->per_page ?? 10,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function dataFakturKlaim($request){
        $url = 'backend/faktur/klaim';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => ($request->companyid??strtoupper(trim($request->session()->get('app_user_company_id')))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'option'        => $request->option,
            'no_faktur'     => $request->no_faktur,
            'kd_sales'      => $request->kd_sales,
            'kd_dealer'     => $request->kd_dealer
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function dataPart($request){
        $url = 'backend/part';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'option'        => $request->option,
            'kd_part'       => $request->kd_part,
            'kd_sales'      => $request->kd_sales,
            'no_retur'      => $request->no_retur,
            'kd_dealer'     => $request->kd_dealer,
            'no_faktur'     => $request->no_faktur,
            'page'          => $request->page ?? 1,
            'per_page'      => $request->per_page ?? 10,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }
    public static function dataRetur($request){
        $url = 'backend/retur';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'option'        => $request->option,
            'no_retur'      => $request->no_retur,
            'page'          => $request->page ?? 1,
            'per_page'      => $request->per_page ?? 10,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function dataProduk($request){
        $url = 'backend/produk';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => strtoupper(trim($request->session()->get('app_user_company_id'))),
            'user_id'       => trim($request->session()->get('app_user_id')),
            'option'        => $request->option,
            'kd_produk'       => $request->kd_produk,
            'page'          => $request->page ?? 1,
            'per_page'      => $request->per_page ?? 10,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }


    public static function KonsumenDaftar($request)
    {
        $url = 'backend/konsumen';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'     => $request->companyid,
            'user_id'       => trim($request->session()->get('app_user_id')),
            'option'        => $request->option??'page',
            'id'            => $request->id,
            'divisi'        => $request->divisi,
            'search'        => $request->search,
            'by'            => $request->by,
            'kd_lokasi'     => $request->kd_lokasi,
            'page'          => $request->page ?? 1,
            'per_page'      => $request->per_page ?? 10,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function KonsumenSimpan($request)
    {
        $url = 'backend/konsumen/simpan';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'user_id'               => trim($request->session()->get('app_user_id')),
            'id'                    => $request->id,
            'divisi'                => $request->divisi,
            'companyid'             => $request->companyid,
            'kd_lokasi'             => $request->kd_lokasi,
            'nomor_faktur'          => $request->nomor_faktur,
            'nik'                   => $request->nik,
            'nama_pelanggan'        => $request->nama_pelanggan,
            'tempat_lahir'          => $request->tempat_lahir,
            'tanggal_lahir'         => $request->tanggal_lahir,
            'alamat'                => $request->alamat,
            'telepon'               => $request->telepon,
            'email'                 => $request->email,
            'nopol'                 => $request->nopol,
            'merk_motor'            => $request->merk_motor,
            'tipe_motor'            => $request->tipe_motor,
            'jenis_motor'           => $request->jenis_motor,
            'tahun_motor'           => $request->tahun_motor,
            'keterangan'            => $request->keterangan,
            'mengetahui'            => $request->mengetahui,
            'keterangan_mengetahui' => $request->keterangan_mengetahui,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function KonsumenHapus($request)
    {
        $url = 'backend/konsumen/hapus';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'user_id'               => trim($request->session()->get('app_user_id')),
            'id'                    => $request->id,
            'divisi'                => $request->divisi,
            'companyid'             => $request->companyid,
            'kd_lokasi'             => $request->kd_lokasi,
            'nomor_faktur'          => $request->nomor_faktur,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }
    public static function LokasiAll($request)
    {
        $url = 'backend/konsumen/lokasi';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'user_id'               => trim($request->session()->get('app_user_id')),
            'companyid'             => $request->session()->get('app_user_company_id'),
            'username'              => $request->session()->get('app_user_name'),
            'role_id'               => trim($request->session()->get('app_user_role_id')),
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function dataMejaPackingOnline(){
        $url = 'backend/gudang/paking/online/meja';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function dataPackerPackingOnline(){
        $url = 'backend/gudang/paking/online/packer';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function dataWH($request){
        $url = 'backend/gudang/wh';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid' => $request->session()->get('app_user_company_id'),
            'option'    => $request->option,
            'no_wh'     => $request->no_wh,
            'page'      => $request->page,
            'per_page'  => $request->per_page
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function PackingSimpan($request)
    {
        $url = 'backend/gudang/packing/online/simpan';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'          => $request->session()->get('app_user_company_id'),
            'no_dok'             => $request->no_dok,
            'kd_packer'          => $request->kd_packer,
            'no_meja'            => $request->no_meja,
            'sts_packing'        => $request->sts_packing
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function dataCabang($request)
    {
        $url = 'backend/cabang';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'companyid'          => $request->session()->get('app_user_company_id'),
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }

    public static function getPriceList($request)
    {
        $url = 'backend/uploadfile/pricelist';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nama_file'          => $request->nama_file,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }
    public static function uploadFilePriceList($request)
    {
        $url = 'backend/uploadfile/pricelist/simpan';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nama_file'          => $request->nama_file,
            'path_file'          => $request->path_file,
            'ukuran_file'        => $request->ukuran_file,
            'ket_file'           => $request->ket_file
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }
    public static function deleteFilePriceList($request)
    {
        $url = 'backend/uploadfile/pricelist/hapus';
        $header = ['Authorization' => session()->get('Authorization')];
        $body = [
            'nama_file'          => $request->nama_file,
            'tanggal'            => $request->tanggal,
        ];
        $response = ApiRequest::requestPost($url, $header, $body);
        return $response;
    }
}
