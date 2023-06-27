<?php

namespace App\Http\Controllers\App\Validasi;

use App\Helpers\App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ValidasiController extends Controller
{
    public function validasiSalesman(Request $request)
    {
        $responseApi = Service::validasiSalesman(
            strtoupper($request->get('kode_sales')),
            strtoupper(trim($request->session()->get('app_user_company_id')))
        );
        return json_decode($responseApi, true);
    }

    public function validasiDealer(Request $request)
    {
        $responseApi = Service::validasiDealer(
            strtoupper($request->get('kode_dealer')),
            strtoupper(trim($request->session()->get('app_user_company_id')))
        );
        return json_decode($responseApi, true);
    }

    public function validasiDealerSalesman(Request $request)
    {
        $responseApi = Service::validasiDealerSalesman(
            strtoupper($request->get('kode_sales')),
            strtoupper($request->get('kode_dealer')),
            strtoupper(trim($request->session()->get('app_user_company_id')))
        );
        return json_decode($responseApi, true);
    }

    public function validasiPartNumber(Request $request)
    {
        $responseApi = Service::validasiPartNumber(
            strtoupper($request->get('part_number')),
            strtoupper(trim($request->session()->get('app_user_company_id')))
        );
        return json_decode($responseApi, true);
    }

    public function validasiProduk(Request $request)
    {
        $responseApi = Service::ValidasiProduk(strtoupper(trim($request->kd_produk)));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            return response()->json([
                'status' => 1,
                'data' => $data,
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => $messageApi,
            ]);
        }
    }
}
