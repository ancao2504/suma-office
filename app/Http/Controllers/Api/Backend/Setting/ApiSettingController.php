<?php

namespace App\Http\Controllers\Api\Backend\Setting;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;


class ApiSettingController extends Controller
{
    public function settingCloseMarketing(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'companyid'     => 'required',
            ]);

            if($validate->fails()) {
                return Response::responseWarning('Anda Belum Login');
            }

            $sql = DB::table('stsclose')->lock('with (nolock)')
                ->selectRaw("isnull(companyid, '') as companyid, isnull(close_mkr, '') as close_mkr,
                            isnull(month(dateadd(day, 1, close_mkr)), 0) as bulan_aktif,
                            isnull(year(dateadd(day, 1, close_mkr)), 0) as tahun_aktif,
                            isnull(dateadd(day, 1, close_mkr), '') as tanggal_aktif")
                ->where('companyid', $request->get('companyid'))
                ->orderBy('companyid', 'asc')
                ->first();

            return Response::responseSuccess('success', $sql);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
