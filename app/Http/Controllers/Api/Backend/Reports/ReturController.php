<?php

namespace app\Http\Controllers\Api\Backend\Reports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ReturController extends Controller
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

            $data = DB::table(function ($query) use ($request){
                    $query->select(
                        'klaim.no_dokumen',
                        'klaim.tgl_dokumen',
                        'klaim_dtl.kd_part',
                        'klaim.pc',
                        'klaim.kd_dealer',
                        'klaim.kd_sales',
                        'klaim_dtl.qty as qty_klaim',
                        'klaim_dtl.keterangan',
                        'klaim_dtl.sts_klaim',
                        'klaim_dtl.sts_min',
                        'klaim_dtl.sts_stock',
                        'klaim.companyid'
                    )
                    ->from('klaim')
                    ->join('klaim_dtl', function ($join) {
                        $join->on('klaim_dtl.no_dokumen', '=', 'klaim.no_dokumen')
                            ->on('klaim_dtl.companyid', '=', 'klaim.companyid');
                    });
                    if(!empty($request->tanggal)){
                        $query = $query->whereBetween(DB::raw('CONVERT(DATE, klaim.tgl_klaim)'), $request->tanggal);
                    }
                    if(!empty($request->kd_dealer)){
                        $query = $query->where('klaim.kd_dealer', $request->kd_dealer);
                    }
                    if(!empty($request->kd_sales)){
                        $query = $query->where('klaim.kd_sales', $request->kd_sales);
                    }
                }, 'klaim')
            ->select(
                'klaim.no_dokumen as no_klaim',
                'klaim.kd_part',
                'klaim.tgl_dokumen as tgl_klaim',
                'rtoko.tanggal as tgl_approve',
                'retur.tglretur as tgl_retur',
                'retur.tgl_jwb as tgl_jwb',
                DB::raw("CASE
                    WHEN klaim.sts_klaim = 1 THEN 'Supplier'
                    ELSE 'Tidak Klaim'
                END AS sts_klaim"),
                DB::raw("CASE
                    WHEN klaim.sts_stock = 1 THEN 'Ganti Barang'
                    WHEN klaim.sts_stock = 2 THEN 'Stock 0'
                    WHEN klaim.sts_stock = 3 THEN 'Retur'
                    ELSE null
                END AS sts_stock"),
                DB::raw("CASE
                    WHEN klaim.sts_min = 1 THEN 'Minimum'
                    ELSE 'Tidak'
                END AS sts_min"),
                'klaim.kd_dealer',
                'klaim.kd_sales',
                'retur.kd_supp',
                'klaim.qty_klaim',
                'rtoko.qty_rtoko',
                'retur.jmlretur as qty_retur',
                'retur.qty_jwb as qty_jwb',
                DB::raw("CASE
                    WHEN retur.ket is not null THEN SUBSTRING(retur.ket, CHARINDEX('|', retur.ket) + 1, LEN(retur.ket) - CHARINDEX('|', retur.ket))
                    WHEN rtoko.ket is not null THEN rtoko.ket
                    WHEN klaim.keterangan is not null THEN klaim.keterangan
                    ELSE null
                END AS keterangan"),
                'retur.ket_jwb as ket_jwb',
                'klaim.companyid'
            )
            ->leftJoinSub(function ($query) {
                $query->select(
                    'rtoko.no_retur',
                    'rtoko_dtl.no_klaim',
                    'rtoko_dtl.kd_part',
                    'rtoko_dtl.jumlah as qty_rtoko',
                    'rtoko.tanggal',
                    'rtoko.kd_dealer',
                    'rtoko.kd_sales',
                    'rtoko_dtl.ket',
                    'rtoko.CompanyId'
                )
                ->from('rtoko')
                ->join('rtoko_dtl', function ($join) {
                    $join->on('rtoko_dtl.no_retur', '=', 'rtoko.no_retur')
                        ->on('rtoko_dtl.CompanyId', '=', 'rtoko.CompanyId');
                });
            }, 'rtoko', function ($join) {
                $join->on('rtoko.no_klaim', '=', 'klaim.no_dokumen')
                    ->on('rtoko.kd_part', '=', 'klaim.kd_part')
                    ->on('rtoko.CompanyId', '=', 'klaim.companyid');
            })
            ->leftJoinSub(function ($query) use ($request) {
                $query->select(
                    'retur.no_retur',
                    'retur_dtl.no_klaim',
                    'retur.tglretur',
                    'retur.kd_supp',
                    'retur_dtl.kd_part',
                    'retur_dtl.jmlretur',
                    'retur_dtl.ket',
                    'retur_dtl.tgl_jwb',
                    'retur_dtl.qty_jwb',
                    'retur_dtl.ket_jwb',
                    'retur.CompanyId'
                )
                ->from('retur')
                ->join('retur_dtl', function ($join) {
                    $join->on('retur_dtl.no_retur', '=', 'retur.no_retur')
                        ->on('retur_dtl.CompanyId', '=', 'retur.CompanyId');
                });
                if(!empty($request->kd_supp)){
                    $query = $query->where('retur.kd_supp', $request->kd_supp);
                }
            }, 'retur', function ($join) {
                $join->on('retur.no_klaim', '=', 'rtoko.no_retur')
                    ->on('retur.kd_part', '=', 'klaim.kd_part')
                    ->on('retur.CompanyId', '=', 'klaim.companyid');
            })
            ->orderBy('klaim.no_dokumen', 'asc')
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
            $data = DB::table(function ($query) use ($request){
                $query->select(
                    'klaim.no_dokumen',
                    'klaim.tgl_dokumen',
                    'klaim_dtl.kd_part',
                    'klaim.pc',
                    'klaim.kd_dealer',
                    'klaim.kd_sales',
                    'klaim_dtl.qty as qty_klaim',
                    'klaim_dtl.keterangan',
                    'klaim_dtl.sts_klaim',
                    'klaim_dtl.sts_min',
                    'klaim_dtl.sts_stock',
                    'klaim.companyid'
                )
                ->from('klaim')
                ->join('klaim_dtl', function ($join) {
                    $join->on('klaim_dtl.no_dokumen', '=', 'klaim.no_dokumen')
                        ->on('klaim_dtl.companyid', '=', 'klaim.companyid');
                });
                if(!empty($request->tanggal)){
                    $query = $query->whereBetween(DB::raw('CONVERT(DATE, klaim.tgl_klaim)'), $request->tanggal);
                }
                if(!empty($request->kd_dealer)){
                    $query = $query->where('klaim.kd_dealer', $request->kd_dealer);
                }
                if(!empty($request->kd_sales)){
                    $query = $query->where('klaim.kd_sales', $request->kd_sales);
                }
            }, 'klaim')
            ->select(
                'klaim.no_dokumen as no_klaim',
                'klaim.kd_part',
                'klaim.tgl_dokumen as tgl_klaim',
                'rtoko.tanggal as tgl_approve',
                'retur.tglretur as tgl_retur',
                'retur.tgl_jwb as tgl_jwb',
                'klaim.kd_dealer',
                'klaim.kd_sales',
                'retur.kd_supp',
                DB::raw("CASE
                    WHEN klaim.sts_klaim = 1 THEN 'Supplier'
                    ELSE 'Tidak Klaim'
                END AS sts_klaim"),
                DB::raw("CASE
                    WHEN klaim.sts_stock = 1 THEN 'Ganti Barang'
                    WHEN klaim.sts_stock = 2 THEN 'Stock 0'
                    WHEN klaim.sts_stock = 3 THEN 'Retur'
                    ELSE null
                END AS sts_stock"),
                DB::raw("CASE
                    WHEN klaim.sts_min = 1 THEN 'Minimum'
                    ELSE 'Tidak'
                END AS sts_min"),
                DB::raw("CASE
                    WHEN retur.ket is not null THEN SUBSTRING(retur.ket, CHARINDEX('|', retur.ket) + 1, LEN(retur.ket) - CHARINDEX('|', retur.ket))
                    WHEN rtoko.ket is not null THEN rtoko.ket
                    WHEN klaim.keterangan is not null THEN klaim.keterangan
                    ELSE null
                END AS keterangan"),
                'klaim.qty_klaim',
                'retur.qty_jwb as qty_jwb'
            )
            ->leftJoinSub(function ($query) {
                $query->select(
                    'rtoko.no_retur',
                    'rtoko_dtl.no_klaim',
                    'rtoko_dtl.kd_part',
                    'rtoko_dtl.jumlah as qty_rtoko',
                    'rtoko.tanggal',
                    'rtoko.kd_dealer',
                    'rtoko.kd_sales',
                    'rtoko_dtl.ket',
                    'rtoko.CompanyId'
                )
                ->from('rtoko')
                ->join('rtoko_dtl', function ($join) {
                    $join->on('rtoko_dtl.no_retur', '=', 'rtoko.no_retur')
                        ->on('rtoko_dtl.CompanyId', '=', 'rtoko.CompanyId');
                });
            }, 'rtoko', function ($join) {
                $join->on('rtoko.no_klaim', '=', 'klaim.no_dokumen')
                    ->on('rtoko.kd_part', '=', 'klaim.kd_part')
                    ->on('rtoko.CompanyId', '=', 'klaim.companyid');
            })
            ->leftJoinSub(function ($query) use ($request) {
                $query->select(
                    'retur.no_retur',
                    'retur_dtl.no_klaim',
                    'retur.tglretur',
                    'retur.kd_supp',
                    'retur_dtl.kd_part',
                    'retur_dtl.jmlretur',
                    'retur_dtl.ket',
                    'retur_dtl.tgl_jwb',
                    'retur_dtl.qty_jwb',
                    'retur_dtl.ket_jwb',
                    'retur.CompanyId'
                )
                ->from('retur')
                ->join('retur_dtl', function ($join) {
                    $join->on('retur_dtl.no_retur', '=', 'retur.no_retur')
                        ->on('retur_dtl.CompanyId', '=', 'retur.CompanyId');
                });
                if(!empty($request->kd_supp)){
                    $query = $query->where('retur.kd_supp', $request->kd_supp);
                }
            }, 'retur', function ($join) {
                $join->on('retur.no_klaim', '=', 'rtoko.no_retur')
                    ->on('retur.kd_part', '=', 'klaim.kd_part')
                    ->on('retur.CompanyId', '=', 'klaim.companyid');
            })
            ->orderBy('klaim.no_dokumen', 'asc')
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
