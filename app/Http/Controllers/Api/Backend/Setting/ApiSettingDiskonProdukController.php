<?php

namespace App\Http\Controllers\Api\Backend\Setting;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;


class ApiSettingDiskonProdukController extends Controller
{
    public function daftarDiskonProduk(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'page'      => 'required',
                'per_page'  => 'required'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih data cabang terlebih dahulu");
            }

            if(trim($request->get('role_id')) != 'MD_H3_MGMT') {
                return Response::responseWarning('Anda tidak memiliki akses untuk membuka halaman ini');
            }

            $sql = DB::table('discp')->lock('with (nolock)')
            ->selectRaw("isnull(discp.cabang, '') as cabang, isnull(discp.kd_produk, '') as kode_produk,
                        isnull(produk.nama, '') as nama_produk, isnull(discp.discp_default, 0) as disc_normal,
                        isnull(discp.discp, 0) as disc_max, isnull(discp.discp_plus_default, 0) as disc_plus_normal,
                        isnull(discp.discp_plus, 0) as disc_plus_max, isnull(discp.umur_faktur, 0) as umur_faktur")
            ->leftJoin(DB::raw('produk with (nolock)'), function($join) {
                $join->on('produk.kd_produk', '=', 'discp.kd_produk');
            });

            if(!empty($request->get('search')) && $request->get('search') != '') {
                $sql->where('discp.kd_produk', $request->get('search'));
            }

            $result = $sql->paginate($request->get('per_page'));

            return Response::responseSuccess('success', $result);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function cekKodeProdukCabang(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'cabang'    => 'required',
                'produk'    => 'required'
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Pilih data cabang terlebih dahulu");
            }

            $sql = DB::table('discp')->lock('with (nolock)')
                ->selectRaw("isnull(discp.cabang, '') as cabang, isnull(discp.kd_produk, '') as kode_produk,
                                    isnull(produk.nama, '') as nama_produk, isnull(discp.discp, 0) as disc_max,
                                    isnull(discp.discp_default, 0) as disc_normal, isnull(discp.discp_plus, 0) as disc_plus_max,
                                    isnull(discp.discp_plus_default, 0) as disc_plus_normal, isnull(discp.umur_faktur, 0) as umur_faktur")
                ->leftJoin(DB::raw('produk with (nolock)'), function ($join) {
                    $join->on('produk.kd_produk', '=', 'discp.kd_produk');
                })
                ->where('discp.cabang', $request->get('cabang'))
                ->where('discp.kd_produk', $request->get('produk'))
                ->first();

            if (empty($sql->cabang)) {
                return Response::responseSuccess('success', null);
            } else {
                return Response::responseSuccess("Diskon produk sudah terdaftar", $sql);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function simpanDiskonProduk(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'cabang'        => 'required|string',
                'produk'        => 'required|string',
                'disc_normal'   => 'required',
                'disc_max'      => 'required',
                'disc_plus_normal' => 'required',
                'disc_plus_max' => 'required',
                'umur_faktur'   => 'required',
                'user_id'       => 'required',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Isi data secara lengkap");
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_SettingDiscProduk_Simpan ?,?,?,?,?,?,?,?', [
                    trim(strtoupper($request->get('cabang'))), trim(strtoupper($request->get('produk'))),
                    (double)$request->get('disc_normal'), (double)$request->get('disc_max'),
                    (double)$request->get('disc_plus_normal'), (double)$request->get('disc_plus_max'),
                    (double)$request->get('umur_faktur'), trim(strtoupper($request->get('user_id')))
                ]);
            });

            return Response::responseSuccess('Data Berhasil Disimpan', null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function hapusDiskonProduk(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'cabang'    => 'required|string',
                'produk'    => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih data produk terlebih dahulu");
            }

            DB::transaction(function () use ($request) {
                DB::delete('exec SP_SettingDiscProduk_Hapus ?,?', [
                    trim(strtoupper($request->get('cabang'))), trim(strtoupper($request->get('produk')))
                ]);
            });

            return Response::responseSuccess('Data Berhasil Dihapus', null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
