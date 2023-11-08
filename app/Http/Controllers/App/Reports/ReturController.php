<?php

namespace app\Http\Controllers\App\Reports;

use App\Helpers\App\Service;
use Illuminate\Http\Request;
use App\Exports\Retur;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\App\Option\OptionController;

class ReturController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $request->merge(['option' => 'select']);
        // $responseApiSales = OptionController::salesman($request)->getData();
        // if ($responseApiSales->status == 1) {
            return view(
                'layouts.report.retur',
                [
                    'title_menu' => 'Report Retur',
                    // 'sales' => $responseApiSales->data
                ]
            );
        // } else {
        //     return redirect()->back()->with('failed', $responseApiSales->message);
        // }
    }

    public function data(Request $request)
    {
        try {
            $responseApi = Service::ReportReturData($request);
            if (json_decode($responseApi)->status == 1) {
                return Response()->json([
                    'status'    => 1,
                    'message'   => 'success',
                    'data'      => json_decode($responseApi)->data,
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

    public function export(Request $request){
        try {
            $responseApi = Service::ExprotReportRetur($request);
            if (json_decode($responseApi)->status == 1) {
                $data = json_decode($responseApi)->data;
                return Excel::download(new Retur($data), 'Retur_Konsumen.xlsx');
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
