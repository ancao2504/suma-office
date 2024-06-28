<?php

namespace app\Http\Controllers\Api\Backend\Setting;

use App\Helpers\Api\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiSettingApproveHargaRugiController extends Controller
{
    public function ApproveHargaRugi(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'user_id'      => 'required',
                'nomor_faktur'      => 'required',
                'companyid'     => 'required',
                'option'        => 'required|string|in:get,approve,cancel',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Data nomor faktur harus terisi");
            }

            if ($request->option == 'get') {
                $result = DB::table('faktur')
                    ->lock('with (nolock)')
                    ->select('no_faktur','CompanyId', DB::raw('isnull(sts_rugi, 0) as sts_rugi'),'approve_rugi')
                    ->where('no_faktur', $request->nomor_faktur)
                    ->where('CompanyId', $request->companyid)
                    ->first();
            }else if ($request->option == 'approve') {
                $result = DB::table('faktur')
                ->lock('with (nolock)')
                ->where('no_faktur', $request->nomor_faktur)
                ->where('CompanyId', $request->companyid)
                ->update([
                    'sts_rugi' => 1,
                    'approve_rugi' => (Carbon::now()->format('Y-m-d=H:i:s=').$request->user_id),
                ]);
            }else if ($request->option == 'cancel') {
                $result = DB::table('faktur')
                ->lock('with (nolock)')
                ->where('no_faktur', $request->nomor_faktur)
                ->where('CompanyId', $request->companyid)
                ->update([
                    'sts_rugi' => 0,
                    'approve_rugi' => (Carbon::now()->format('Y-m-d=H:i:s=').$request->user_id),
                ]);
            }


            return Response::responseSuccess('success', $result);
        } catch (\Exception $exception) {
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $exception->getMessage(),
                $request->get('companyid')
            );
        }
    }
}
