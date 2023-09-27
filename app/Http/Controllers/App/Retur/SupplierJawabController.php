<?php

namespace app\Http\Controllers\App\Retur;

use App\Helpers\App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SupplierJawabController extends Controller
{

    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form(Request $request)
    {
        $request->merge(['option' => ['with_jwb']]);
        $responseApiRetur = json_decode(Service::ReturSupplierDaftar($request));
        $statusApiRetur = $responseApiRetur->status??0;

        if ($statusApiRetur == 1) {
            $data = [
                'data' => $responseApiRetur->data,
                'title_menu' => 'Supplier Jawab',
                'title_page' => 'Jawab',
            ];
            
            return view('layouts.retur.supplier.jawab.form', $data);
        }else {
            return redirect()->back()->withInput()->with('failed', 'Maaf terjadi kesalahan, silahkan coba lagi');
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
        try {
            $rules = [
                'no_retur' => 'required',
                'tamp'      => 'required',
            ];
            $messages = [
                'no_retur.required' => 'Maaf terjadi kesalahan, silahkan coba lagi',
                'tamp.required' => 'Maaf terjadi kesalahan, silahkan coba lagi',
            ];
            if((boolean)$request->tamp){
                $rules += [
                    'no_klaim' => 'required',
                    'kd_part' => 'required',
                    'qty_jwb' => 'required',
                    'alasan' => 'required|in:RETUR,CA',
                    'keputusan' => 'required|in:TERIMA,TOLAK',
                ];
                $messages += [
                    'no_klaim.required' => 'Maaf terjadi kesalahan, silahkan coba lagi',
                    'kd_part.required'  => 'Maaf terjadi kesalahan, silahkan coba lagi',
                    'qty_jwb.required' => 'Qty Jawab Tidak Boleh Kososng',
                    'alasan.required' => 'Alasan Tidak Boleh Kososng',
                    'alasan.in'  => 'Alasan Tidak Valid',
                    'keputusan.required'  => 'Keputusan Tidak Boleh Kososng',
                    'keputusan.in'  => 'Keputusan Tidak Valid',
                ];

                if($request->alasan == 'CA'){
                    $rules += ['ca' => 'required'];
                    $messages += ['ca.required'  => 'Jumlah Uang Tidak Boleh Kososng'];
                }

                if ((boolean)$request->tamp && (int)$request->qty_jwb != (int)count(explode(',', $request->no_produksi))) {
                    return Response()->json([
                        'status'    => 0,
                        'message'   => 'Jumlah No Produksi yang dipilih tidak sesuai dengan Qty jawaban',
                        'data'      => ''
                    ], 200);
                }
            }
            $validate = Validator::make($request->all(), $rules,$messages);
            if ($validate->fails()) {
                return Response()->json([
                    'status'    => 0,
                    'message'   => $validate->errors()->first(),
                    'data'      => ''
                ]);
            }
            $responseApi = json_decode(Service::ReturSupplierjawabSimpan($request));
            $statusApi = $responseApi->status;
            $messageApi =  $responseApi->message;
            $data = $responseApi->data;

            if ($statusApi == 1) {
                return Response()->json([
                    'status'    => 1,
                    'message'   => 'Data berhasil disimpan',
                    'data'      => $data
                ], 200);
            }else {
                return Response()->json([
                    'status'    => 0,
                    'message'   => $messageApi,
                    'data'      => $data
                ], 200);
            }
        } catch (\Throwable $th) {
            return Response()->json([
                'status'    => 0,
                'message'   => 'Maaf terjadi kesalahan, silahkan coba lagi',
                'data'      => ''
            ], 200);
        }
    }

    public function destroy(Request $request)
    {
        $rules = [
            'no_retur' => 'required',
            'no_klaim' => 'required',
            'kd_part' => 'required',
            'no_jwb' => 'required',
        ];
        $messages = [
            'no_retur.required' => 'Maaf terjadi kesalahan, silahkan coba lagi',
            'no_klaim.required' => 'Maaf terjadi kesalahan, silahkan coba lagi',
            'kd_part.required' => 'Maaf terjadi kesalahan, silahkan coba lagi',
            'no_jwb.required' => 'Maaf terjadi kesalahan, silahkan coba lagi',
        ];

        $validate = Validator::make($request->all(), $rules,$messages);
        if ($validate->fails()) {
            return Response()->json([
                'status'    => 2,
                'message'   => $validate->errors()->first(),
                'data'      => ''
            ]);
        }

        $responseApi = json_decode(Service::ReturSupplierJwbDelete($request));
        $statusApi = $responseApi->status??0;

        if ($statusApi == 1) {
            return Response()->json([
                'status'    => 1,
                'message'   => 'Data berhasil dihapus',
                'data'      => $responseApi->data??''
            ], 200);
        }else {
            return Response()->json([
                'status'    => 0,
                'message'   => 'Terjadi kesalahan, silahkan cek jika data masih ada maka belum terhapus',
                'data'      => ''
            ], 200);
        }
    }
}
