<?php

namespace app\Http\Controllers\App\Reports;

use Illuminate\Support\Arr;
use App\Helpers\App\Service;
use Illuminate\Http\Request;
use App\Exports\Retur\Konsumen;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\App\Option\OptionController;

class KonsumenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(empty(session()->get('app_user_company'))){
            $data = json_decode(service::LokasiAll($request));
            if($data->status == 0){
                return redirect()->back()->with('failed', 'Maaf, terjadi kesalahan coba beberapa saat lagi');
            }
            session()->put('app_user_company', $data->data);
        }

        $request->merge(['option' => 'select']);
        $responseApiSales = OptionController::salesman($request)->getData();

        $responseApi_merekmotor = OptionController::merekmotor($request)->getData();
        $statusApi_merekmotor = $responseApi_merekmotor->status;

        $responseApi_typemotor = json_decode(OptionController::typemotor($request));
        $statusApi_typemotor = $responseApi_typemotor->status;

        if ($responseApiSales->status == 1 && $statusApi_merekmotor == 1 && $statusApi_typemotor == 1) {
            return view(
                'layouts.report.konsumen.konsumen',
                [
                    'title_menu' => 'Report Konsumen',
                    'bulan' => [
                        '01' => 'Januari',
                        '02' => 'Februari',
                        '03' => 'Maret',
                        '04' => 'April',
                        '05' => 'Mei',
                        '06' => 'Juni',
                        '07' => 'Juli',
                        '08' => 'Agustus',
                        '09' => 'September',
                        '10' => 'Oktober',
                        '11' => 'November',
                        '12' => 'Desember'
                    ],
                    'lokasi'    =>  collect(session()->get('app_user_company'))->except('lokasi_valid')->all(),
                    'merk_motor'    =>  $responseApi_merekmotor->data,
                    'type_motor'    =>  $responseApi_typemotor->data,
                ]
            );
        } else {
            return redirect()->back()->with('failed', $responseApiSales->message);
        }
    }

    public function data(Request $request)
    {
        try {
            $responseApi = Service::ReportReturKonsumenData($request);
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
            $responseApi = Service::ExprotReportReturKonsumen($request);
            if (json_decode($responseApi)->status == 1) {
                $data = json_decode($responseApi)->data;
                return Excel::download(new Konsumen($data), 'Retur_Konsumen.xlsx');
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
