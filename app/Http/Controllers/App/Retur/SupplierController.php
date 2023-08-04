<?php

namespace app\Http\Controllers\App\Retur;

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
            $request->merge(['per_page' => 10]);
        }

        $request->merge(['option' => ['page']]);
        $responseApi = json_decode(Service::ReturSupplierDaftar($request));
        $statusApi = $responseApi->status??0;

        if ($statusApi == 1) {
            return view(
                'layouts.retur.supplier.index',
                [
                    'old_request' => (object)[
                        'no_retur' => $request->no_retur ?? '',
                        'per_page' => $request->per_page ?? 10,
                    ],
                    'data' => $responseApi->data,
                    'title_menu' => 'Retur Supplier',
                ]
            );
        }else {
            return redirect()->back()->withInput()->with('failed', 'Maaf terjadi kesalahan, silahkan coba lagi');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form(Request $request)
    {
        $request->merge(['option' => ['with_detail','tamp']]);
        $responseApiRetur = json_decode(Service::ReturSupplierDaftar($request));
        $statusApiRetur = $responseApiRetur->status??0;

        $request->merge(['option' => 'select']);
        $responseApi_supplier = OptionController::supplier($request)->getData();
        $statusApi_supplier = $responseApi_supplier->status??0;

        if ($statusApi_supplier == 1 && $statusApiRetur == 1) {
            $data = [
                'supplier' => $responseApi_supplier->data,
                'data' => $responseApiRetur->data,
                'title_menu' => 'Retur Supplier',
                'title_page' => 'Tambah',
            ];
            
            return view('layouts.retur.supplier.form', $data);
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
            if(in_array($request->ket, ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'K', 'L', 'M', 'N', 'O', 'P'])){
                match ($request->ket) {
                    'A' => $request->merge(['ket' => 'Karat/Korosi']),
                    'B' => $request->merge(['ket' => 'Permukaan Cacat (Jamur, Gores, dll)']),
                    'C' => $request->merge(['ket' => 'Bengkok/Berubah Bentuk']),
                    'D' => $request->merge(['ket' => 'Patah/Pecah/Sobek']),
                    'E' => $request->merge(['ket' => 'Sub Part Tidak lengkap']),
                    'F' => $request->merge(['ket' => 'Arus Mati (Electric)']),
                    'G' => $request->merge(['ket' => 'Bocor (Liquid)']),
                    'H' => $request->merge(['ket' => 'Dimensi Tidak Sesuai Spek']),
                    'K' => $request->merge(['ket' => 'Jumlah Part Kurang']),
                    'L' => $request->merge(['ket' => 'Jumlah Part Lebih']),
                    'M' => $request->merge(['ket' => 'Fisik Part Beda']),
                    'N' => $request->merge(['ket' => 'Label Beda']),
                    'O' => $request->merge(['ket' => 'Packaging rusak']),
                    'P' => $request->merge(['ket' => 'Tidak Order']),
                };
            }

            $responseApi = json_decode(Service::ReturSupplierSimpan($request));
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
        ];
        $messages = [
            'no_retur.required' => 'No Retur Tidak Boleh Kososng',
        ];

        $validate = Validator::make($request->all(), $rules,$messages);
        if ($validate->fails()) {
            return Response()->json([
                'status'    => 1,
                'message'   => $validate->errors()->first(),
                'data'      => ''
            ]);
        }

        $responseApi = json_decode(Service::ReturSupplierDelete($request));
        $statusApi = $responseApi->status??0;

        if ($statusApi == 1) {
            return Response()->json([
                'status'    => 1,
                'message'   => 'Data berhasil dihapus',
                'data'      => $responseApi->data
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
