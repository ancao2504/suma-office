<?php

namespace App\Http\Controllers\Api\Backend\Setting;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;


class ApiSettingPartNettoController extends Controller
{
    public function daftarPartNetto(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'page'      => 'required',
                'per_page'  => 'required',
                'role_id'   => 'required|string',
                'companyid' => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Anda Belum Login");
            }

            if(strtoupper(trim($request->get('role_id'))) != 'MD_H3_MGMT') {
                return Response::responseWarning("Anda tidak memiliki akses untuk membuka halaman ini");
            }

            $sql = DB::table('part')->lock('with (nolock)')
                    ->selectRaw("isnull(part.kd_part, '') as part_number, isnull(part.ket, '') as keterangan,
                                isnull(part.tpc20, '') as tpc20, isnull(part.harga20, 0) as harga")
                    ->where('part.companyid', $request->get('companyid'))
                    ->where('part.tpc20', 'Y')
                    ->orderBy('part.kd_part', 'asc');

            if(!empty($request->get('search')) && trim($request->get('search')) != '') {
                $sql->where('part.kd_part', 'like', trim($request->get('search')));
            }

            $result = $sql->paginate($request->get('per_page') ?? 10);

            return Response::responseSuccess('success', $result);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function simpanPartNetto(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'part_number' => 'required|string',
                'status'    => 'required|string',
                'harga'     => 'required',
                'companyid' => 'required|string',
                'user_id'   => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning('Pilih data part number terlebih dahulu');
            }

            $sql = DB::table('part')->lock('with (nolock)')
                    ->selectRaw("isnull(part.kd_part, '') as part_number, isnull(part.ket, '') as keterangan,
                                isnull(part.tpc20, '') as status_tpc20, isnull(part.harga20, 0) as harga,
                                isnull(part.hrg_pokok, 0) as harga_pokok")
                    ->where('part.kd_part', strtoupper(trim($request->get('part_number'))))
                    ->where('part.companyid', strtoupper(trim($request->get('companyid'))))
                    ->first();

            if(empty($sql->part_number)) {
                return Response::responseWarning("Part number yang anda entry tidak terdaftar");
            } else {
                if((double)$request->get('harga') < (double)$sql->harga_pokok) {
                    return Response::responseWarning("Penjualan Rugi, harga yang di entry kurang dari harga pokok");
                }
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_SettingPartNetto_Simpan ?,?,?,?,?', [
                    trim(strtoupper($request->get('part_number'))), trim(strtoupper($request->get('status'))),
                    (double)$request->get('harga'), trim(strtoupper($request->get('companyid'))),
                    trim(strtoupper($request->get('user_id')))
                ]);
            });

            return Response::responseSuccess('Data Berhasil Disimpan', null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
