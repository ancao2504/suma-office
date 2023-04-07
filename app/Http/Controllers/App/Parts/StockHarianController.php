<?php

namespace App\Http\Controllers\App\Parts;

use App\Helpers\ApiService;
use App\Http\Controllers\App\Exports\ExcelStockHarianController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StockHarianController extends Controller
{
    public function index(Request $request) {
        $kodeLokasi = '';
        $kodeRak = '';
        $classProduk = [];
        $groupProduk = [];
        $groupLevel = [];
        $subProduk = [];
        $lokasi = [];

        $responseApi = ApiService::StockHarianOption(trim($request->session()->get('app_user_company_id')));
        $messageApi = json_decode($responseApi)->message;
        $statusApi = json_decode($responseApi)->status;

        if($statusApi == 1) {
            $kodeLokasi = json_decode($responseApi)->data->kode_lokasi;
            $kodeRak = json_decode($responseApi)->data->kode_rak;
            $classProduk = json_decode($responseApi)->data->class_produk;
            $groupProduk = json_decode($responseApi)->data->group_produk;
            $groupLevel = json_decode($responseApi)->data->produk_level;
            $subProduk = json_decode($responseApi)->data->sub_produk;
            $lokasi = json_decode($responseApi)->data->lokasi;

            return view ('layouts.parts.stockharian.stockharian', [
                'title_menu'    => 'Stock Harian',
                'class_produk'  => $classProduk,
                'group_produk'  => $groupProduk,
                'sub_produk'    => $subProduk,
                'group_level'   => $groupLevel,
                'lokasi'        => $lokasi,
            ])->with([
                'kode_lokasi'       => $kodeLokasi,
                'kode_rak'          => $kodeRak,
                'select_stock_sedia' => '>',
                'nilai_stock_sedia' => 0
            ]);

        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function prosesPrintReport(Request $request) {
        $validate = Validator::make($request->all(), [
            'kode_lokasi'       => 'required|string',
            'kode_rak'          => 'required|string',
            'nilai_stock_sedia' => 'required|numeric'
        ]);

        if($validate->fails()) {
            return redirect()->back()->withInput()->with('failed', 'Kolom kode lokasi, kode rak, dan nilai stock sedia tidak boleh kosong');
        }

        if (strtoupper(trim($request->get('kode_lokasi'))) == 'ALLONLINE') {
            $responseApi = ApiService::StockHarianProsesMarketplace(strtoupper(trim($request->session()->get('app_user_company_id'))),
                            strtoupper(trim($request->session()->get('app_user_role_id'))),
                            strtoupper(trim($request->get('kode_class'))), strtoupper(trim($request->get('kode_produk'))),
                            strtoupper(trim($request->get('kode_produk_level'))), strtoupper(trim($request->get('kode_sub'))),
                            strtoupper(trim($request->get('frg'))), strtoupper(trim($request->get('kode_lokasi'))),
                            strtoupper(trim($request->get('kode_rak'))), strtoupper(trim($request->get('option_stock_sedia'))),
                            strtoupper(trim($request->get('nilai_stock_sedia'))));
        } else {
            $responseApi = ApiService::StockHarianProsesPerlokasi(strtoupper(trim($request->session()->get('app_user_company_id'))),
                            strtoupper(trim($request->session()->get('app_user_role_id'))),
                            strtoupper(trim($request->get('kode_class'))), strtoupper(trim($request->get('kode_produk'))),
                            strtoupper(trim($request->get('kode_produk_level'))), strtoupper(trim($request->get('kode_sub'))),
                            strtoupper(trim($request->get('frg'))), strtoupper(trim($request->get('kode_lokasi'))),
                            strtoupper(trim($request->get('kode_rak'))), strtoupper(trim($request->get('option_stock_sedia'))),
                            strtoupper(trim($request->get('nilai_stock_sedia'))));
        }

        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $nama_company = json_decode($responseApi)->data->nama_company;
            $alamat_company = json_decode($responseApi)->data->alamat_company;
            $kota_company = json_decode($responseApi)->data->kota_company;
            $data = json_decode($responseApi)->data->data_stock;
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }

        if (strtoupper(trim($request->get('kode_lokasi'))) == 'ALLONLINE') {
            return view ('reports.stockharian.stockharianmarketplace', compact('data'), [
                'nama_company'      => $nama_company,
                'alamat_company'    => $alamat_company,
                'kota_company'      => $kota_company
            ]);
        } else {
            return view ('reports.stockharian.stockharianperlokasi', compact('data'), [
                'nama_company'      => $nama_company,
                'alamat_company'    => $alamat_company,
                'kota_company'      => $kota_company
            ]);
        }
    }

    public function prosesExportExcel(Request $request) {
        $validate = Validator::make($request->all(), [
            'kode_lokasi'       => 'required|string',
            'kode_rak'          => 'required|string',
            'nilai_stock_sedia' => 'required|numeric'
        ]);

        if($validate->fails()) {
            return redirect()->back()->withInput()->with('failed', 'Kolom kode lokasi, kode rak, dan nilai stock sedia tidak boleh kosong');
        }

        $companyId = trim($request->session()->get('app_user_company_id'));
        $roleId = trim($request->session()->get('app_user_role_id'));
        $kode_class = trim($request->get('kode_class'));
        $kode_produk = trim($request->get('kode_produk'));
        $kode_produk_level = trim($request->get('kode_produk_level'));
        $kode_sub = trim($request->get('kode_sub'));
        $frg = trim($request->get('frg'));
        $kode_lokasi = trim($request->get('kode_lokasi'));
        $kode_rak = trim($request->get('kode_rak'));
        $option_stock_sedia = trim($request->get('option_stock_sedia'));
        $nilai_stock_sedia = trim($request->get('nilai_stock_sedia'));

        $nama_file = 'Stock Harian Suma Honda'.' '.date('Y-m-d His').'.xlsx';

        return (new ExcelStockHarianController((String) $companyId, (String) $roleId,
                (String) $kode_class, (String) $kode_produk, (String) $kode_produk_level,
                (String) $kode_sub, (String) $frg, (String) $kode_lokasi, (String) $kode_rak,
                (String) $option_stock_sedia, (String) $nilai_stock_sedia))
            ->download($nama_file);
    }
}
