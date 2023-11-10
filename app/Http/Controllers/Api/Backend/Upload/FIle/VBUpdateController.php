<?php

namespace App\Http\Controllers\Api\Backend\Upload\File;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

class VBUpdateController extends Controller
{
    function index(Request $request){
        $data = DB::table('program_exe');
        if($request->get('version')){
            $data = $data->where('version', 'like', '%'.$request->get('version').'%');
        }
        $data =$data
        ->orderBy('version','desc')
        ->get();

        return Response::responseSuccess('success', $data);
    }

    function store(Request $request){
        $validate = Validator::make($request->all(), [
            'version' => 'required|string',
            'path_file' => 'required|string',
        ],[
            'version.required'    => 'Version tidak boleh kosong',
            'version.string'      => 'Version harus berupa string',
            'path_file.required'    => 'Path file tidak boleh kosong',
            'path_file.string'      => 'Path file harus berupa string',
        ]);
        if ($validate->fails()) {
            return Response::responseWarning($validate->errors()->first());
        }

        try {
            DB::transaction(function () use ($request){
                DB::table('program_exe')
                ->updateOrInsert(
                    ['version' => $request->version],
                    [
                        'path_download'   => $request->path_file
                    ]
                );
            });

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
    function destroy(Request $request){
        $validate = Validator::make($request->all(), [
            'version' => 'required|string'
        ],[
            'version.required'    => 'Version tidak boleh kosong',
        ]);
        if ($validate->fails()) {
            return Response::responseWarning($validate->errors()->first());
        }

        try {
            DB::transaction(function () use ($request){
                DB::table('program_exe')
                ->where('version', $request->version)
                ->delete();
            });

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
