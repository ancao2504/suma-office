<?php

namespace app\Http\Controllers\App\Reports;

use Illuminate\Support\Str;
use App\Helpers\App\Service;
use Illuminate\Http\Request;
use App\Exports\Konsumen;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
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
        $responseApiringban = OptionController::ukuranring()->getData();

        $responseApi_merekmotor = OptionController::merekmotor($request)->getData();
        $statusApi_merekmotor = $responseApi_merekmotor->status;

        $responseApi_typemotor = json_decode(OptionController::typemotor($request));
        $statusApi_typemotor = $responseApi_typemotor->status;

        if ($responseApiringban->status == 1 && $statusApi_merekmotor == 1 && $statusApi_typemotor == 1) {
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
                    'ring_ban'      =>  $responseApiringban->data,
                ]
            );
        } else {
            return redirect()->back()->with('failed', 'Maaf, terjadi kesalahan coba beberapa saat lagi');
        }
    }

    public function daftarKonsumen(Request $request)
    {
        try {
            $lokasi = session()->get('app_user_company');
            // ! cek lokasi,cabang agar sesuai yang di izinkan
            if(!empty($request->companyid) && !in_array($request->companyid, $lokasi->lokasi_valid->companyid)){
                return Response()->json([
                    'status'    => 0,
                    'message'   => 'Maaaf, Anda tidak memiliki akses ke company '.$request->companyid,
                    'data'      => ''
                ], 200);
            }
            if(empty($request->companyid)) {
                $request->merge(['companyid' => collect(collect($lokasi)->first()->lokasi)->first()->companyid]);
            }

            if(empty($request->kd_lokasi) || !in_array($request->kd_lokasi, $lokasi->lokasi_valid->kd_lokasi)){
                $request->merge(['kd_lokasi' => collect(collect($lokasi)->first()->lokasi)->first()->kd_lokasi[0]]);
            }

            $request->merge(['divisi' => (in_array($request->companyid,collect(collect($lokasi)->first()->lokasi)->pluck('companyid')->toArray()))?collect($lokasi)->first()->divisi:collect($lokasi)->skip(1)->take(1)->first()->divisi]);

            $responseApi = json_decode(Service::ReportKonsumenData($request));
            if ($responseApi->status == 1) {
                $view = view(
                    'layouts.report.konsumen.konsumen',
                    [
                        'title_menu' => 'Report Konsumen',
                        'data' => $responseApi->data->data,
                    ]
                );

                return Response()->json([
                    'status'    => 1,
                    'message'   => 'success',
                    'data'      => Str::between($view->with('data', $responseApi->data->data)->render(), '<!--begin::Card-->', '<!--end::Card-->'),
                    'old'       => (object)['request' => $request->except('_token')]
                ], 200);
            } else {
                return Response()->json([
                    'status'    => 0,
                    'message'   => 'Maaf, terjadi kesalahan. Silahkan coba lagi',
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

    public function exportDaftarKonsumen(Request $request){
        try {
            $responseApi = Service::exportDaftarKonsumen($request);
            if (json_decode($responseApi)->status == 1) {
                $data = json_decode($responseApi)->data;
                return Excel::download(new Konsumen($data), 'Konsumen.xlsx');
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
