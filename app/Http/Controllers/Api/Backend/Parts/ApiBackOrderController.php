<?php

namespace App\Http\Controllers\Api\Backend\Parts;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;


class ApiBackOrderController extends Controller
{
    public function daftarBackOrder(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'user_id'   => 'required|string',
                'role_id'   => 'required|string',
                'companyid' => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Anda belum login");
            }

            $sql = DB::table('bo')->lock('with (nolock)')
                    ->selectRaw("isnull(bo.kd_part, '') as part_number, isnull(part.ket, '') as nama_part,
                                isnull(bo.kd_sales, '') as kode_sales, isnull(bo.kd_dealer, '') as kode_dealer,
                                isnull(bo.kd_tpc, '') as kode_tpc, isnull(bo.jumlah, 0) as jumlah_bo, isnull(part.het, 0) as het,
                                isnull(bo.disc1, 0) as discount_1, isnull(bo.disc2, 0) as discount_2,
                                iif(isnull(bo.kd_tpc, '')='20', 0, 1) as status_discount,
                                cast(iif(isnull(bo.kd_tpc, '')='20', isnull(bo.hrg_netto, 0),
                                    (isnull(bo.het, 0) - round((isnull(bo.het, 0) * isnull(bo.disc1, 0)) / 100, 0)) -
                                        round((isnull(bo.het, 0) * isnull(bo.disc2, 0)) / 100, 0)) as decimal(13,0)) as harga")
                    ->leftJoin(DB::raw('part with (nolock)'), function($join) {
                            $join->on('part.kd_part', '=', 'bo.kd_part')
                                ->on('part.companyid', '=', 'bo.companyid');
                        })->lock('with (nolock)')
                    ->leftJoin(DB::raw('salesman with (nolock)'), function($join) {
                            $join->on('salesman.kd_sales', '=', 'bo.kd_sales')
                                ->on('salesman.companyid', '=', 'bo.companyid');
                        })->lock('with (nolock)')
                    ->leftJoin(DB::raw('superspv with (nolock)'), function($join) {
                            $join->on('superspv.kd_spv', '=', 'salesman.spv')
                                ->on('superspv.companyid', '=', 'bo.companyid');
                        })->lock('with (nolock)')
                    ->where('bo.companyid', strtoupper(trim($request->get('companyid'))))
                    ->where('bo.jumlah', '>', 0)
                    ->orderBy('bo.kd_dealer', 'asc')
                    ->orderBy('bo.kd_part', 'asc');

            if(strtoupper(trim($request->get('role_id'))) == "D_H3") {
                $sql->where('bo.kd_dealer', '=', strtoupper(trim($request->get('user_id'))));
            } elseif(strtoupper(trim($request->get('role_id'))) == "MD_H3_SM") {
                $sql->where('bo.kd_sales', '=', strtoupper(trim($request->get('user_id'))));
            } elseif(strtoupper(trim($request->get('role_id'))) == "MD_H3_KORSM") {
                $sql->where('spv.nm_spv', '=', strtoupper(trim($request->get('user_id'))));
            }

            if(!empty($request->get('kode_sales'))) {
                $sql->where('bo.kd_sales', '=', $request->get('kode_sales'));
            }

            if(!empty($request->get('kode_dealer'))) {
                $sql->where('bo.kd_dealer', '=', $request->get('kode_dealer'));
            }

            if(!empty($request->get('part_number'))) {
                $sql->where('bo.kd_part', 'like', $request->get('part_number').'%');
            }

            $sql = $sql->paginate($request->get('per_page') ?? 12);

            return Response::responseSuccess('success', $sql);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        } ;
    }
}
