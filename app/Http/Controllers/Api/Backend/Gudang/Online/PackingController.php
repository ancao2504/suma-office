<?php

namespace app\Http\Controllers\Api\Backend\Gudang\Online;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class PackingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'no_dok'    => [
                    'required',
                    Rule::exists('wh_time')->where('CompanyId', $request->companyid),
                ],
                'no_meja'   => 'required|exists:lokasi_pack,kd_lokpack',
                'kd_packer' => 'required',
            ],[
                'no_dok.required'        => 'Nomer WH tidak boleh kosong!',
                'no_dok.exists'         => 'Nomer WH tidak ditemukan!',
                'no_meja.required'      => 'Meja tidak boleh kosong!',
                'no_meja.exists'        => 'Meja tidak ditemukan!',
                'kd_packer.required'    => 'Packer tidak boleh kosong!',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            DB::transaction(function () use ($request) {
                DB::table('dbhonda.dbo.wh_time')
                ->where('no_dok', $request->no_dok)
                ->where('CompanyId', $request->companyid)
                ->update([
                    'kd_pack'       => $request->kd_packer,
                    'kd_lokpack'    => $request->no_meja,
                    'tanggal3'      => date('Y-m-d'),
                    'jam3'          => date('H:i:s'),
                ]);
            });

            return Response::responseSuccess('success', 'Data berhasil diSimpan!');
        } catch (\Throwable $exception) {
            return Response::responseError($request->get('user_id'), 'API', route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
