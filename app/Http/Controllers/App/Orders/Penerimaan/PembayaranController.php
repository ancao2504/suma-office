<?php

namespace App\Http\Controllers\app\Orders\Penerimaan;

use App\Helpers\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PembayaranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view(
            'layouts.orders.penerimaan.pembayaran.pembayaran',
            [
                'title_menu'    => 'Penerimaan Pembayaran',
                'user'          => trim($request->session()->get('app_user_id')),
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
        $responseApi = ApiService::PembayaranDealerSimpan(
            trim($request->get('kd_dealer')),
            trim($request->get('jenis_transaksi')),
            $request->get('total'),
            $request->get('detail'),
            strtoupper(trim($request->session()->get('app_user_id'))),
            strtoupper(trim($request->session()->get('app_user_company_id'))),
        );
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            return redirect()->back()->with('success', $messageApi);
        } else {
            return redirect()->back()->with('failed', $messageApi);
        }
    }
}
