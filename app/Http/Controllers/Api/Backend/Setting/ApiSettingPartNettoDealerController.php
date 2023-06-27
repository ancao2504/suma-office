<?php

namespace App\Http\Controllers\Api\Backend\Setting;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;


class ApiSettingPartNettoDealerController extends Controller
{
    public function daftarPartNettoDealer(Request $request) {
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

            $sql = DB::table('dealer_netto')->lock('with (nolock)')
                    ->selectRaw("isnull(dealer_netto.kd_part, '') as part_number, isnull(dealer_netto.kd_dealer, '') as kode_dealer,
                                isnull(part.het, 0) as het, isnull(part.harga20, 0) as harga_tpc_20,
                                isnull(dealer_netto.harga, 0) as harga_jual, isnull(dealer_netto.ket, '') as keterangan")
                    ->leftJoin(DB::raw('part with (nolock)'), function($join) {
                        $join->on('part.kd_part', '=', 'dealer_netto.kd_part')
                            ->on('part.companyid', '=', 'dealer_netto.companyid');
                    })
                    ->where('dealer_netto.companyid', $request->get('companyid'))
                    ->orderBy('dealer_netto.kd_part', 'asc');

            if(!empty($request->get('search')) && trim($request->get('search')) != '') {
                $sql->where('dealer_netto.kd_part', 'like', trim($request->get('search')));
            }

            $result = $sql->paginate($request->get('per_page') ?? 10);

            return Response::responseSuccess('success', $result);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function simpanPartNettoDealer(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'part_number'   => 'required|string',
                'dealer'        => 'required|string',
                'harga'         => 'required',
                'keterangan'    => 'required|string',
                'companyid'     => 'required|string',
                'user_id'       => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning('Data part number, dealer, harga, dan keterangan tidak boleh kosong');
            }

            $sql = DB::table('dealer')->lock('with (nolock)')
                    ->selectRaw("isnull(dealer.kd_dealer, '') as kode_dealer")
                    ->where('dealer.kd_dealer', strtoupper(trim($request->get('dealer'))))
                    ->where('dealer.companyid', strtoupper(trim($request->get('companyid'))))
                    ->first();

            if(empty($sql->kode_dealer)) {
                return Response::responseWarning("Dealer yang anda entry tidak terdaftar");
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
                DB::insert('exec SP_SettingPartNettoDealer_Simpan ?,?,?,?,?,?', [
                    trim(strtoupper($request->get('part_number'))), trim(strtoupper($request->get('dealer'))),
                    (double)$request->get('harga'), trim(strtoupper($request->get('keterangan'))),
                    trim(strtoupper($request->get('companyid'))), trim(strtoupper($request->get('user_id')))
                ]);
            });

            return Response::responseSuccess('Data Berhasil Disimpan', null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function hapusPartNettoDealer(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'part_number'   => 'required|string',
                'dealer'        => 'required|string',
                'companyid'     => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning('Data yang ingin dihapus terlebih dahulu');
            }

            DB::transaction(function () use ($request) {
                DB::delete('exec SP_SettingPartNettoDealer_Hapus ?,?,?', [
                    trim(strtoupper($request->get('part_number'))), trim(strtoupper($request->get('dealer'))),
                    trim(strtoupper($request->get('companyid')))
                ]);
            });

            return Response::responseSuccess('Data Berhasil Dihapus', null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
