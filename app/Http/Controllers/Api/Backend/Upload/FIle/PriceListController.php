<?php

namespace App\Http\Controllers\Api\Backend\Upload\File;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

class PriceListController extends Controller
{
    function store(Request $request){
        $validate = Validator::make($request->all(), [
            'nama_file' => 'required|string',
            'path_file' => 'required|string',
            'ket_file'  => 'required|string'
        ],[
            'nama_file.required'    => 'Nama file tidak boleh kosong',
            'nama_file.string'      => 'Nama file harus berupa string',
            'path_file.required'    => 'Path file tidak boleh kosong',
            'path_file.string'      => 'Path file harus berupa string',
            'ket_file.required'     => 'Keterangan file tidak boleh kosong',
            'ket_file.string'       => 'Keterangan file harus berupa string'
        ]);
        if ($validate->fails()) {
            return Response::responseWarning($validate->errors()->first());
        }

        try {
            DB::table('pricelist')->updateOrInsert(
                ['nama_file' => $request->nama_file],
                [
                    'tanggal'       => date('Y-m-d H:i:s'),
                    'nama_file'     => $request->nama_file,
                    'lokasi_file'   => $request->path_file,
                    'keterangan'    => $request->ket_file
                ]
            );

            return Response::responseSuccess('success', '');
        } catch (\Exception $exception) {
            return Response::responseError(
                $request->get('user_id'),
                'API',
                route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $exception->getMessage(),
                $request->get('companyid')??null,
            );
        }

        
    }
}
