<?php

namespace App\Http\Controllers\Api\Backend\Validasi;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;


class ApiValidasiController extends Controller
{
    public function validasiUserIdTidakTerdaftar(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'user_id'   => 'required',
                'companyid' => 'required'
            ]);

            if($validate->fails()) {
                return Response::responseWarning('User Id tidak boleh kosong');
            }

            $sql = DB::table('users')->lock('with (nolock)')
                    ->selectRaw("isnull(user_id, '') as user_id, isnull(name, '') as nama,
                                isnull(jabatan, '') as jabatan, isnull(telepon, '') as telepon,
                                isnull(email, '') as email")
                    ->where('user_id', $request->get('user_id'))
                    ->where('companyid', $request->get('companyid'))
                    ->first();

            if(empty($sql->user_id)) {
                return Response::responseSuccess('success', null);
            } else {
                return Response::responseWarning('User id sudah terdaftar');
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function validasiEmailTidakTerdaftar(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'email'     => 'required',
                'companyid' => 'required'
            ]);

            if($validate->fails()) {
                return Response::responseWarning('Email tidak boleh kosong');
            }

            $sql = DB::table('users')->lock('with (nolock)')
                    ->selectRaw("isnull(user_id, '') as user_id, isnull(name, '') as nama,
                                isnull(jabatan, '') as jabatan, isnull(telepon, '') as telepon,
                                isnull(email, '') as email")
                    ->where('email', $request->get('email'))
                    ->where('companyid', $request->get('companyid'))
                    ->first();

            if(empty($sql->user_id)) {
                return Response::responseSuccess('success', null);
            } else {
                return Response::responseWarning('Email sudah terdaftar');
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function validasiSalesman(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'kode_sales'    => 'required',
                'companyid'     => 'required'
            ]);

            if($validate->fails()) {
                return Response::responseWarning('Kode sales tidak boleh kosong');
            }

            $sql = DB::table('salesman')->lock('with (nolock)')
                    ->selectRaw("isnull(kd_sales, '') as kode_sales, isnull(nm_sales, '') as nama_sales")
                    ->where('kd_sales', $request->get('kode_sales'))
                    ->where('companyid', $request->get('companyid'))
                    ->first();

            if(empty($sql->kode_sales)) {
                return Response::responseWarning('Kode sales tidak terdaftar');
            } else {
                return Response::responseSuccess('success', $sql);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function validasiDealer(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'kode_dealer'   => 'required',
                'companyid'     => 'required'
            ]);

            if($validate->fails()) {
                return Response::responseWarning('Kode dealer tidak boleh kosong');
            }

            $sql = DB::table('dealer')->lock('with (nolock)')
                    ->selectRaw("isnull(kd_dealer, '') as kode_dealer, isnull(nm_dealer, '') as nama_dealer,
                                isnull(dealer.kd_sales, '') as kode_sales")
                    ->where('kd_dealer', $request->get('kode_dealer'))
                    ->where('companyid', $request->get('companyid'))
                    ->first();

            if(empty($sql->kode_dealer)) {
                return Response::responseWarning('Kode dealer tidak terdaftar');
            } else {
                return Response::responseSuccess('success', $sql);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function validasiDealerSalesman(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'kode_sales'    => 'required',
                'kode_dealer'   => 'required',
                'companyid'     => 'required'
            ]);

            if($validate->fails()) {
                return Response::responseWarning('Kode sales dan kode dealer tidak boleh kosong');
            }

            $sql = DB::table('salesk_dtl')->lock('with (nolock)')
                    ->selectRaw('salesk_dtl.kd_dealer as kode_dealer, dealer.nm_dealer as nama_dealer')
                    ->leftJoin(DB::raw('dealer with (nolock)'), function($join) {
                        $join->on('dealer.kd_dealer', '=', 'salesk_dtl.kd_dealer')
                            ->on('dealer.companyid', '=', 'salesk_dtl.companyid');
                    })
                    ->where('salesk_dtl.kd_sales', $request->get('kode_sales'))
                    ->where('salesk_dtl.kd_dealer', $request->get('kode_dealer'))
                    ->where('salesk_dtl.companyid', $request->get('companyid'))
                    ->first();

            if(empty($sql->kode_dealer)) {
                return Response::responseWarning('Kode dealer tidak terdaftar atau bukan milik area '.strtoupper(trim($request->get('kode_sales'))));
            } else {
                return Response::responseSuccess('success', $sql);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function validasiPartNumber(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'part_number'   => 'required',
                'companyid'     => 'required'
            ]);

            if($validate->fails()) {
                return Response::responseWarning('Part number tidak boleh kososng');
            }

            $sql = DB::table('part')->lock('with (nolock)')
                    ->selectRaw("isnull(part.kd_part, '') as part_number,
                                iif(isnull(part.bhs_pasar, '')='', isnull(part.ket, ''), isnull(part.bhs_pasar, '')) as description,
                                isnull(produk.nama, '') as produk,
                                isnull(part.het, 0) as het")
                    ->leftJoin(DB::raw('sub with (nolock)'), function($join) {
                        $join->on('sub.kd_sub', '=', 'part.kd_sub');
                    })
                    ->leftJoin(DB::raw('produk with (nolock)'), function($join) {
                        $join->on('produk.kd_produk', '=', 'sub.kd_produk');
                    })
                    ->where('part.kd_part', $request->get('part_number'))
                    ->where('part.companyid', $request->get('companyid'))
                    ->first();

            if(empty($sql->part_number)) {
                return Response::responseWarning('Part number tidak terdaftar');
            } else {
                return Response::responseSuccess('success', $sql);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function validasiProduk(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'kode_produk'   => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih kode produk terlebih dahulu");
            }

            $sql = DB::table('produk')->lock('with (nolock)')
                    ->selectRaw("isnull(produk.kd_produk, '') as kode_produk,
                                isnull(produk.nama, '') as nama_produk")
                    ->where('produk.kd_produk', $request->get('kode_produk'))
                    ->first();

            if(empty($sql->kode_produk)) {
                return Response::responseWarning("Kode produk tidak terdaftar");
            } else {
                return Response::responseSuccess('success', $sql);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
