<?php

namespace app\Http\Controllers\App\Claim;

use App\Helpers\App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\App\Option\OptionController;
class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!in_array($request->per_page, [10,50,100,500])){
            $request->merge([
                'per_page' => 10
            ]);
        }
        // $request->merge(['option' => 'page']);
        // $responseApi = Service::ReturKonsumenDaftar($request);
        // $statusApi = json_decode($responseApi)->status;
        // $messageApi =  json_decode($responseApi)->message;

        // if ($statusApi == 1) {
            return view(
                'layouts.claim.supplier.index',
                [
                    // 'old_request' => (object)[
                    //     'no_retur' => $request->no_retur ?? '',
                    //     'per_page' => $request->per_page ?? 10,
                    // ],
                    // 'data' => json_decode($responseApi)->data,
                    'title_menu' => 'Claim Supplier',
                ]
            );
        // }else {
        //     return redirect()->back()->withInput()->with('failed', $messageApi);
        // }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form(Request $request)
    {
        $request->merge(['option' => 'select']);
        $responseApi = OptionController::salesman($request)->getData();
        $statusApi = $responseApi->status;
        $messageApi =  $responseApi->message;

        if ($statusApi == 1) {
            return view(
                'layouts.retur.konsumen.form',
                [
                    'sales' => $responseApi->data,
                    'title_menu' => 'Retur Konsumen Edit',
                ]
            );
        }else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
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
        $rules = [];
        $messages = [];

        // ! ------------------------------------
        // ! Jika menambahkan validasi

        if(!empty($request->no_retur)){
            $rules += [
                'kd_dealer' => 'required',
            ];
            $messages += [
                'kd_dealer.required' => 'kode Dealer Kososng',
            ];
        } else {
            $rules += [
                'kd_dealer' => 'required',
                'kd_part' => 'required',
                'qty_claim' => 'required|numeric|min:1',
                'sts' => 'required',
                'tgl_claim' => 'required',
                'tgl_terima' => 'required',
            ];
            $messages += [
                'kd_dealer.required' => 'kode Dealer Kososng',
                'kd_part.required' => 'Part Number Kososng',
                'qty_claim.required' => 'QTY Claim Kososng',
                'qty_claim.min' => 'QTY Pada Claim Minimal 1',
                'tgl_claim.required' => 'Tanggal Claim Kososng',
                'tgl_terima.required' => 'Tanggal Terima Kososng',
                'sts.required' => 'Status Kososng',
            ];
        }

        // ! megecek validasi dan menampilkan pesan error
        // ! ------------------------------------
        $validate = Validator::make($request->all(), $rules,$messages);
        if ($validate->fails()) {
            return Response()->json([
                'status'    => 0,
                'message'   => $validate->errors()->first(),
                'data'      => ''
            ]);
        }

        $responseApi = Service::ReturKonsumenSimpan($request);
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            return Response()->json([
                'status'    => 1,
                'message'   => 'Data berhasil disimpan',
                'data'      => json_decode($responseApi)->data
            ], 200);
        }else {
            return Response()->json([
                'status'    => 0,
                'message'   => $messageApi,
                'data'      => ''
            ], 200);
        }
    }

    public function storeDtl(Request $request)
    {
        $rules = [
            'kd_part' => 'required',
            'qty_claim' => 'required|numeric|min:1',
            'sts' => 'required',
        ];
        $messages = [
            'kd_part.required' => 'Kode Part Kososng',
            'qty_claim.required' => 'QTY Claim Kososng',
            'qty_claim.min' => 'QTY Claim Minimal 1',
            'sts.required' => 'Status Kososng',
        ];

        // ! ------------------------------------
        // ! Jika menambahkan validasi
        // ! ------------------------------------

        if(!empty($request->no_faktur)){
            $rules += [
                'no_faktur' => 'required|min:5',
            ];
            $messages += [
                'no_faktur.required' => 'No Faktur Tidak Bisa Kosong',
                'no_faktur.min' => 'No Faktur minimal 5 karakter',
            ];
        }

        // ! ------------------------------------
        // ! megecek validasi dan menampilkan pesan error
        // ! ------------------------------------
        $validate = Validator::make($request->all(), $rules,$messages);
        if ($validate->fails()) {
            return Response()->json([
                'status'    => 1,
                'message'   => $validate->errors()->first(),
                'data'      => ''
            ]);
        }

        $responseApi = Service::ReturKonsumenDtlSimpan($request);
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            return Response()->json([
                'status'    => 1,
                'message'   => 'Data berhasil disimpan',
                'data'      => json_decode($responseApi)->data
            ], 200);
        }else {
            return Response()->json([
                'status'    => 0,
                'message'   => $messageApi,
                'data'      => ''
            ], 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $request->merge(['option' => 'with_detail']);
        $responseApiRetur = Service::ReturKonsumenDaftar($request);
        $statusApiRetur = json_decode($responseApiRetur)->status;
        $messageApiRetur =  json_decode($responseApiRetur)->message;

        $request->merge(['option' => 'select']);
        $responseApiSales = OptionController::salesman($request)->getData();
        $statusApiSales = $responseApiSales->status;
        $messageApiSales =  $responseApiSales->message;

        if ($statusApiRetur == 1 && $statusApiSales == 1) {
            return view(
                'layouts.retur.konsumen.edit',
                [
                    'sales' => $responseApiSales->data,
                    'data'  => json_decode($responseApiRetur)->data,
                    'title_menu' => 'Retur Konsumen Edit',
                ]
            );
        }else if($statusApiRetur == 0){
            return redirect()->route('retur.konsumen.index')->with('failed', $messageApiRetur);
        } else if($statusApiSales == 0){
            return redirect()->route('retur.konsumen.index')->with('failed', $messageApiSales);
        }
    }

    public function destroy(Request $request)
    {
        $rules = [
            'no_retur' => 'required',
        ];
        $messages = [
            'no_retur.required' => 'No Retur Tidak Boleh Kososng',
        ];

        if(!empty($request->kd_part)){
            $rules += [
                'kd_part' => 'required|min:5',
            ];
            $messages += [
                'kd_part.required' => 'No Faktur Tidak Bisa Kosong',
                'kd_part.min' => 'No Faktur minimal 5 karakter',
            ];
        }

        $validate = Validator::make($request->all(), $rules,$messages);
        if ($validate->fails()) {
            return Response()->json([
                'status'    => 1,
                'message'   => $validate->errors()->first(),
                'data'      => ''
            ]);
        }

        $responseApi = Service::ReturKonsumenDelete($request);
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            return Response()->json([
                'status'    => 1,
                'message'   => 'Data berhasil disimpan',
                'data'      => json_decode($responseApi)->data
            ], 200);
        }else {
            return Response()->json([
                'status'    => 0,
                'message'   => $messageApi,
                'data'      => ''
            ], 200);
        }
    }
}
