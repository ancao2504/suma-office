<?php

namespace App\Http\Controllers\Api\Backend\Setting;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;


class ApiSettingDiskonDealerController extends Controller
{
    public function daftarDiskonDealer(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'page'      => 'required',
                'per_page'  => 'required',
                'companyid' => 'required'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih data company terlebih dahulu");
            }

            if(strtoupper(trim($request->get('role_id'))) <> 'MD_H3_MGMT') {
                return Response::responseWarning('Anda tidak memiliki akses untuk membuka halaman ini');
            }

            $sql = DB::table('dealer_setting')->lock('with (nolock)')
                    ->selectRaw("isnull(dealer_setting.kd_dealer, '') as kode_dealer,
                            isnull(dealer_setting.disc_default, 0) as disc_default,
                            isnull(dealer_setting.disc_plus, 0) as disc_plus,
                            isnull(dealer_setting.umur_faktur, 0) as umur_faktur")
                    ->where('dealer_setting.companyid', $request->get('companyid'));

            if(!empty($request->get('search')) && $request->get('search') != '') {
                $sql->where('dealer_setting.kd_dealer', $request->get('search'));
            }

            $result = $sql->paginate($request->get('per_page'));

            return Response::responseSuccess('success', $result);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function simpanDiskonDealer(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'dealer'        => 'required',
                'disc_default'  => 'required',
                'disc_plus'     => 'required',
                'umur_faktur'   => 'required',
                'companyid'     => 'required',
                'user_id'       => 'required',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Isi data secara lengkap");
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_SettingDealerSetting_Simpan ?,?,?,?,?,?', [
                    trim(strtoupper($request->get('dealer'))), (double)$request->get('disc_default'),
                    (double)$request->get('disc_plus'), (double)$request->get('umur_faktur'),
                    trim(strtoupper($request->get('companyid'))), trim(strtoupper($request->get('user_id')))
                ]);
            });

            return Response::responseSuccess('Data Berhasil Disimpan', null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function hapusDiskonDealer(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'dealer'    => 'required|string',
                'companyid' => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih data dealer terlebih dahulu");
            }

            DB::transaction(function () use ($request) {
                DB::delete('exec SP_SettingDealerSetting_Hapus ?,?', [
                    trim(strtoupper($request->get('dealer'))), trim(strtoupper($request->get('companyid')))
                ]);
            });

            return Response::responseSuccess('Data Berhasil Dihapus', null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
