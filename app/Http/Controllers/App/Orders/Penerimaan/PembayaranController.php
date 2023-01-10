<?php

namespace App\Http\Controllers\app\Orders\Penerimaan;

use App\Helpers\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent as Agent;
use Intervention\Image\Facades\Image;

class PembayaranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $Agent = new Agent();
        if($Agent->isMobile()) {
            $device = 'Mobile';
        } else {
            $device = 'Desktop';
        }
        return view(
            'layouts.orders.penerimaan.pembayaran.pembayaran',
            [
                'title_menu'    => 'Penerimaan Pembayaran',
                'user'          => trim($request->session()->get('app_user_id')),
                'device'        => $device,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function daftarPembayaranDealer(Request $request)
    {
        $companyid = strtoupper(trim($request->session()->get('app_user_company_id')));
        $responseApi = ApiService::pembayaranDealerDaftar(
            trim($request->get('kd_dealer')),
            trim($companyid)
        );
        
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            return response()->json([
                'status' => 1,
                'message' => $messageApi,
                'data' => $data
            ]);
        } else {
            return response()->json(['status' => 0, 'message' => $messageApi]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $names_files = [];
        // jika ada gambar bukti pembayaran
        if(!empty($request->image)){
            foreach ($request->image as $key => $value) {
                $nama_file =  'Bukti_Penagihan_'.strtoupper(trim($request->session()->get('app_user_id'))).'_'.strtoupper(trim($request->session()->get('app_user_company_id'))).'_'. date('YmdHis'). '.' . $value->getClientOriginalExtension();
                $names_files[$key] = $nama_file;
                $directory = 'C:/xampp/htdocs/suma-pmo/public/assets/images/penagihan_pembayaran/';
                try {
                    $image_resize = Image::make(file_get_contents($value->getRealPath()));
                    $image_resize->resize(900, null , function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image_resize->save($directory.$nama_file);
                } catch (\Exception $e) {
                    foreach ($names_files as $key => $value) {
                        $file = $directory.$value;
                        file_exists($file) ? unlink($file) : '';
                    }
                    return redirect()->back()->withInput()->with('failed', 'Bukti Pembayaran Gagal diterima !');
                }
            }
        } 
        // kirim data ke api
        $responseApi = ApiService::PembayaranDealerSimpan(
            trim($request->get('kd_dealer')),
            trim($request->get('jenis_transaksi')),
            $request->get('total'),
            $request->get('detail'),
            $names_files,
            strtoupper(trim($request->session()->get('app_user_id'))),
            strtoupper(trim($request->session()->get('app_user_company_id'))),
        );
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;
        // $dataApi = json_decode($responseApi)->data;

        if ($statusApi == 1) {
            return redirect()->back()->with('successPembayaran', 'Bukti Pembayaran Berhasil Diterima !')->with('jml', $request->get('total'));
        } else {
            foreach ($names_files as $key => $value) {
                $file = $directory.$value;
                file_exists($file) ? unlink($file) : '';
            }
            return redirect()->back()->withInput()->with('failed',$messageApi)->with('detail',json_decode($request->get('detail')));
        }
    }
}
