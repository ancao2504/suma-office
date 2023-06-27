<?php

namespace app\Http\Controllers\App\Reports;

use App\Exports\Faktur;
use App\Helpers\App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\App\Option\OptionController;

class FakturController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->merge(['option' => 'select']);
        $responseApiSales = OptionController::salesman($request)->getData();
        $responseApiProduk = OptionController::produk($request)->getData();

        if ($responseApiSales->status == 1 && $responseApiProduk->status == 1) {
            return view(
                'layouts.report.faktur',
                [
                    'title_menu' => 'Report Faktur',
                    'sales' => $responseApiSales->data,
                    'produk' => $responseApiProduk->data
                ]
            );
        } else if($responseApiSales->status == 0 || $responseApiProduk->status == 0){
            if (json_decode($responseApiSales)->status == 0) {
                return redirect()->back()->with('failed', json_decode($responseApiSales)->message);
            } else if (json_decode($responseApiProduk)->status == 0) {
                return redirect()->back()->with('failed', json_decode($responseApiProduk)->message);
            }
        } else {
            return redirect()->back()->with('failed', 'Maaf, terjadi kesalahan. Silahkan coba lagi');
        }
    }

    public function data(Request $request)
    { 
        try {
            $responseApi = Service::ReportFakturData($request);
            if (json_decode($responseApi)->status == 1) {
                $data = json_decode($responseApi)->data;
                
                return Response()->json([
                    'status'    => 1,
                    'message'   => 'success',
                    'data'      => $data
                ], 200);
            } else {
                return Response()->json([
                    'status'    => 0,
                    'message'   => json_decode($responseApi)->message,
                    'data'      => ''
                ], 200);
            }
        } catch (\Exception $e) {
            return Response()->json([
                'status'    => 2,
                'message'   => 'Maaf, terjadi kesalahan. Silahkan coba lagi',
                'data'      => ''
            ], 200);
        }
    }

    
    public function export(Request $request)
    {
        try {
            $responseApi = Service::ExprotReportFaktur($request);
            if (json_decode($responseApi)->status == 1) {
                $data = json_decode($responseApi)->data;
                return Excel::download(new Faktur($data), 'Faktur.xlsx');
            } else {
                return Response()->json([
                    'status'    => 0,
                    'message'   => json_decode($responseApi)->message,
                    'data'      => ''
                ], 200);
            }
        } catch (\Exception $e) {
            return Response()->json([
                'status'    => 2,
                'message'   => 'Maaf, terjadi kesalahan. Silahkan coba lagi',
                'data'      => ''
            ], 200);
        }
    }

}
