<?php

namespace app\Http\Controllers\Api\Backend\Reports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PackingController extends Controller
{
    public function data(Request $request)
    {
        try {
            if(empty($request->page)){
                $request->merge(['page' => 1]);
            }

            if (!in_array($request->per_page, ['10', '50', '100', '500'])) {
                $request->merge(['per_page' => 10]);
            }

            $data = DB::table(function ($query) use ($request) {
                $query->select(
                    'no_dok',
                    'kd_dealer',
                    'tanggal3 as tanggal',
                    'kd_pack',
                    'kd_lokpack',
                    'jam3 as waktu_mulai',
                    'jam4 as waktu_selesai',
                    DB::raw("CONVERT(varchar(8), DATEADD(SECOND, DATEDIFF(SECOND, jam3, jam4), 0), 108) AS waktu_proses"),
                    'wh_time.CompanyId',
                )
                ->from('wh_time')
                ->where('tanggal3', '!=', null);
                if(!empty($request->tanggal)){
                    $query = $query->whereBetween(DB::raw('CONVERT(DATE, tanggal3)'), $request->tanggal);
                }
                if(!empty($request->no_meja)){
                    $query = $query->where('kd_lokpack', $request->no_meja);
                }
                if(!empty($request->kd_packing)){
                    $query = $query->where('kd_pack', $request->kd_packing);
                }
            }, 'wh_time')
            ->select(
                'wh_time.no_dok',
                DB::raw("COUNT(wh_dtl.no_faktur) as jumlah_faktur"),
                'dealer.kd_dealer',
                'wh_time.tanggal',
                'wh_time.kd_pack',
                'wh_time.kd_lokpack',
                'wh_time.waktu_mulai',
                'wh_time.waktu_selesai',
                'wh_time.waktu_proses'
            )
            ->joinSub(function ($query) {
                $query->select(
                    'no_dok',
                    'no_faktur',
                    'CompanyId'
                )
                ->from('wh_dtl')
                ->groupBy('no_dok', 'no_faktur', 'CompanyId');
            }, 'wh_dtl', function ($join) {
                $join->on('wh_time.no_dok', '=', 'wh_dtl.no_dok')
                    ->on('wh_time.CompanyId', '=', 'wh_dtl.CompanyId');
            })
            ->joinSub(function ($query) {
                $query->select(
                    'kd_dealer',
                    'kd_area',
                    'CompanyId'
                )
                ->from('dealer')
                ->where('kd_area', 'i8');
            }, 'dealer', function ($join) {
                $join->on('wh_time.kd_dealer', '=', 'dealer.kd_dealer')
                    ->on('wh_time.CompanyId', '=', 'dealer.CompanyId');
            })
            ->leftJoin('faktur', function ($join) {
                $join->on('wh_dtl.no_faktur', '=', 'faktur.no_faktur')
                    ->on('wh_time.CompanyId', '=', 'faktur.CompanyId');
            })
            ->orderBy('wh_time.tanggal', 'asc')
            ->orderBy('wh_time.waktu_mulai', 'asc')
            ->groupBy('wh_time.no_dok', 'dealer.kd_dealer', 'wh_time.tanggal', 'wh_time.kd_pack', 'wh_time.kd_lokpack', 'wh_time.waktu_mulai', 'wh_time.waktu_selesai', 'wh_time.waktu_proses')
            ->paginate($request->per_page);

            return Response()->json([
                'status'    => 1,
                'message'   => 'success',
                'data'      => $data
            ], 200);
        } catch (\Exception $e) {
            return Response()->json([
                'status'    => 2,
                'message'   => 'Maaf, terjadi kesalahan. Silahkan coba lagi',
                'data'      => ''
            ], 200);
        }
    }

    public function export(Request $request){
        try {
            $data = DB::table(function ($query) use ($request) {
                $query->select(
                    'no_dok',
                    'kd_dealer',
                    'tanggal3 as tanggal',
                    'kd_pack',
                    'kd_lokpack',
                    'jam3 as waktu_mulai',
                    'jam4 as waktu_selesai',
                    DB::raw("CONVERT(varchar(8), DATEADD(SECOND, DATEDIFF(SECOND, jam3, jam4), 0), 108) AS waktu_proses"),
                    'wh_time.CompanyId',
                )
                ->from('dbhonda.dbo.wh_time')
                ->where('tanggal3', '!=', null);
                if(!empty($request->tanggal)){
                    $query = $query->whereBetween(DB::raw('CONVERT(DATE, tanggal3)'), $request->tanggal);
                }
                if(!empty($request->no_meja)){
                    $query = $query->where('kd_lokpack', $request->no_meja);
                }
                if(!empty($request->kd_packing)){
                    $query = $query->where('kd_pack', $request->kd_packing);
                }
            }, 'wh_time')
            ->select(
                'wh_time.no_dok',
                DB::raw("COUNT(wh_dtl.no_faktur) as jumlah_faktur"),
                'dealer.kd_dealer',
                'wh_time.tanggal',
                'wh_time.kd_pack',
                'wh_time.kd_lokpack',
                'wh_time.waktu_mulai',
                'wh_time.waktu_selesai',
                'wh_time.waktu_proses'
            )
            ->joinSub(function ($query) {
                $query->select(
                    'no_dok',
                    'no_faktur',
                    'CompanyId'
                )
                ->from('dbhonda.dbo.wh_dtl')
                ->groupBy('no_dok', 'no_faktur', 'CompanyId');
            }, 'wh_dtl', function ($join) {
                $join->on('wh_time.no_dok', '=', 'wh_dtl.no_dok')
                    ->on('wh_time.CompanyId', '=', 'wh_dtl.CompanyId');
            })
            ->joinSub(function ($query) {
                $query->select(
                    'kd_dealer',
                    'kd_area',
                    'CompanyId'
                )
                ->from('dbhonda.dbo.dealer')
                ->where('kd_area', 'i8');
            }, 'dealer', function ($join) {
                $join->on('wh_time.kd_dealer', '=', 'dealer.kd_dealer')
                    ->on('wh_time.CompanyId', '=', 'dealer.CompanyId');
            })
            ->leftJoin('dbhonda.dbo.faktur', function ($join) {
                $join->on('wh_dtl.no_faktur', '=', 'faktur.no_faktur')
                    ->on('wh_time.CompanyId', '=', 'faktur.CompanyId');
            })
            ->orderBy('wh_time.tanggal', 'asc')
            ->orderBy('wh_time.waktu_mulai', 'asc')
            ->groupBy('wh_time.no_dok', 'dealer.kd_dealer', 'wh_time.tanggal', 'wh_time.kd_pack', 'wh_time.kd_lokpack', 'wh_time.waktu_mulai', 'wh_time.waktu_selesai', 'wh_time.waktu_proses')
            ->get();

            return Response()->json([
                'status'    => 1,
                'message'   => 'success',
                'data'      => $data
            ], 200);
        } catch (\Exception $e) {
            return Response()->json([
                'status'    => 2,
                'message'   => 'Maaf, terjadi kesalahan. Silahkan coba lagi',
                'data'      => ''
            ], 200);
        }
    }
}
