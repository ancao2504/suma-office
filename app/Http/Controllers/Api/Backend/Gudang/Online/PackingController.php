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
                'sts_packing'    => 'required'
            ],[
                'no_dok.required'        => 'Nomer WH tidak boleh kosong!',
                'no_dok.exists'         => 'Nomer WH tidak ditemukan!',
                'no_meja.required'      => 'Meja tidak boleh kosong!',
                'no_meja.exists'        => 'Meja tidak ditemukan!',
                'kd_packer.required'    => 'Packer tidak boleh kosong!',
                'sts_packing.required'        => 'Status Tidak boleh kosong!'
            ]);

            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            if($request->sts_packing == 'mulai'){
                DB::transaction(function () use ($request) {
                    DB::table('wh_time')
                    ->where('no_dok', $request->no_dok)
                    ->where('CompanyId', $request->companyid)
                    ->update([
                        'kd_pack'       => $request->kd_packer,
                        'kd_lokpack'    => $request->no_meja,
                        'tanggal3'      => DB::raw("CONVERT(VARCHAR(10), GETDATE(), 120)"),
                        'jam3'          => DB::raw('CONVERT(VARCHAR(8), GETDATE(), 108)'),
                    ]);
                });
                
                $data = DB::table('wh_time')
                ->where('no_dok', $request->no_dok)
                ->where('CompanyId', $request->companyid)
                ->select('tanggal3', 'jam3')
                ->first();

                return Response::responseSuccess('success', (object)[
                    'tgl_start' => $data->tanggal3,
                    'jam_start' => $data->jam3
                ]);
            } elseif($request->sts_packing == 'selesai') {
                DB::transaction(function () use ($request) {
                    DB::table('wh_time')
                    ->where('no_dok', $request->no_dok)
                    ->where('CompanyId', $request->companyid)
                    ->update([
                        'tanggal4'      => DB::raw("CONVERT(VARCHAR(10), GETDATE(), 120)"),
                        'jam4'          => DB::raw('CONVERT(VARCHAR(8), GETDATE(), 108)'),
                    ]);
                });
                return Response::responseSuccess('Proses Packing Selesai!', '');
            } else {
                DB::transaction(function () use ($request) {
                    DB::table('wh_time')
                    ->where('no_dok', $request->no_dok)
                    ->where('CompanyId', $request->companyid)
                    ->update([
                        'kd_pack'       => $request->kd_packer,
                        'kd_lokpack'    => $request->no_meja,
                        'tanggal3'      => DB::raw("CONVERT(VARCHAR(10), GETDATE(), 120)"),
                        'jam3'          => DB::raw('CONVERT(VARCHAR(8), GETDATE(), 108)'),
                        'tanggal4'      => null,
                        'jam4'          => null,
                    ]);
                });

                $data = DB::table('wh_time')
                ->where('no_dok', $request->no_dok)
                ->where('CompanyId', $request->companyid)
                ->select('tanggal3', 'jam3')
                ->first();

                return Response::responseSuccess('success', (object)[
                    'tgl_start' => $data->tanggal3,
                    'jam_start' => $data->jam3
                ]);
            }
        } catch (\Throwable $exception) {
            return Response::responseError($request->get('user_id'), 'API', route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
