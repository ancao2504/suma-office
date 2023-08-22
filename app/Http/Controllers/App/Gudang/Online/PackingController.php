<?php

namespace App\Http\Controllers\App\Gudang\Online;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Helpers\App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\App\Option\OptionController;

class PackingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form(Request $request)
    {
        $request->merge(['option' => 'select']);
        $responseApi_meja = OptionController::MejaPackingOnline($request)->getData();
        $statusApi_meja = $responseApi_meja->status;
        $messageApi_meja =  $responseApi_meja->message;

        if($statusApi_meja == 1){
            return view(
                'layouts.gudang.online.packing.form',
                [
                    'title_menu'    => 'Gudang Packing Online Form',
                    'meja'          => $responseApi_meja->data
                ],
            );
        }
        
        return redirect()->back()->with('failed', $messageApi_meja);
    }

    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'no_dok'    => 'required',
                'no_meja'   => 'required',
                'kd_packer' => 'required',
                'sts_packing'    => 'required'
            ],[
                'no_meja.required'      => 'Meja tidak boleh kosong!',
                'kd_packer.required'    => 'Packer tidak boleh kosong!',
                'no_dok.required'        => 'Nomer WH tidak boleh kosong!',
                'sts_packing.required'        => 'Status Tidak boleh kosong!',
            ]);

            if ($validate->fails()) {
                return Response()->json([
                    'status'    => 0,
                    'message'   => $validate->errors(),
                    'data'      => ''
                ], 200);
            }


            $responseApi = json_decode(Service::PackingSimpan($request));
            $messageApi =  $responseApi->message;

            if($responseApi->status == 1){
                $request->session()->put('no_meja', $request->no_meja);
                $request->session()->put('kd_packer', $request->kd_packer);

                return Response()->json([
                    'status'    => 1,
                    'message'   => $messageApi,
                    'data'      => $responseApi->data
                ], 200);
            }
            
            return Response()->json([
                'status'    => 0,
                'message'   => $messageApi,
                'data'      => ''
            ], 200);
        } catch (\Throwable $exception) {
            return Response()->json([
                'status'    => 2,
                'message'   => 'Maaf terjadi kesalahan pada Server, coba ulangi beberapa saat lagi!',
                'data'      => ''
            ], 200);
        }
    }

}
