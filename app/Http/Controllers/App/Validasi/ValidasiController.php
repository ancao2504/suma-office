<?php

namespace App\Http\Controllers\App\Validasi;

use App\Helpers\ApiService;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ValidasiController extends Controller
{
    public function validasiSalesman(Request $request)
    {
        $responseApi = ApiService::validasiSalesman(
            strtoupper($request->get('kode_sales')),
            strtoupper(trim($request->session()->get('app_user_company_id')))
        );
        return json_decode($responseApi, true);
    }

    public function validasiDealer(Request $request)
    {
        $responseApi = ApiService::validasiDealer(
            strtoupper($request->get('kode_dealer')),
            strtoupper(trim($request->session()->get('app_user_company_id')))
        );
        return json_decode($responseApi, true);
    }

    public function validasiDealerSalesman(Request $request)
    {
        $responseApi = ApiService::validasiDealerSalesman(
            strtoupper($request->get('kode_sales')),
            strtoupper($request->get('kode_dealer')),
            strtoupper(trim($request->session()->get('app_user_company_id')))
        );
        return json_decode($responseApi, true);
    }

    public function validasiPartNumber(Request $request)
    {
        $responseApi = ApiService::validasiPartNumber(
            strtoupper($request->get('part_number')),
            strtoupper(trim($request->session()->get('app_user_company_id')))
        );
        return json_decode($responseApi, true);
    }

    public function validasiProduk(Request $request)
    {
        $responseApi = ApiService::ValidasiProduk(strtoupper(trim($request->kd_produk)));
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
