<?php

namespace app\Http\Controllers\Api\Backend\Reports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ReturController extends Controller
{
    // public function data(Request $request)
    // {
    //     try {
    //         if(empty($request->page)){
    //             $request->merge(['page' => 1]);
    //         }

    //         if (!in_array($request->per_page, ['10', '50', '100', '500'])) {
    //             $request->merge(['per_page' => 10]);
    //         }

    //         $data = DB::table(function ($query) use ($request){
    //                 $query->select(
    //                     'retur.no_retur as no_dok_supp',
    //                     'rtoko.no_retur',
    //                     'klaim.no_dokumen as no_klaim',
    //                     'klaim.kd_part',
    //                     DB::raw("STUFF(
    //                         (SELECT ',' + a.no_produksi
    //                             FROM klaim_dtl AS a
    //                             WHERE a.no_dokumen = klaim.no_dokumen
    //                                 AND a.kd_part = klaim.kd_part
    //                                 AND a.companyid = klaim.companyid
    //                                 AND a.sts_klaim = klaim.sts_klaim
    //                                 AND a.sts_min = klaim.sts_min
    //                                 AND a.sts_stock = klaim.sts_stock
    //                             FOR XML PATH('')), 1, 1, ''
    //                     ) AS no_produksi"),
    //                     'klaim.tgl_dokumen as tgl_klaim',
    //                     'rtoko.tanggal as tgl_rtoko',
    //                     'retur.tglretur as tgl_retur',
    //                     'retur.tgl_jwb as tgl_jwb',
    //                     'klaim.kd_sales',
    //                     'klaim.kd_dealer',
    //                     'retur.kd_supp',
    //                     DB::raw("CASE WHEN klaim.sts_stock = 1 THEN 'Ganti Barang' WHEN klaim.sts_stock = 2 THEN 'Stock 0' WHEN klaim.sts_stock = 3 THEN 'Retur' ELSE null END AS sts_stock"),
    //                     DB::raw("CASE WHEN klaim.sts_min = 1 THEN 'IYA' ELSE 'TIDAK' END AS sts_min"),
    //                     DB::raw("CASE WHEN klaim.sts_klaim = 1 THEN 'IYA' ELSE 'TIDAK' END AS sts_klaim"),
    //                     DB::raw("CASE WHEN klaim.status_approve = 1 THEN 1 ELSE 0 END AS sts_approve"),
    //                     DB::raw("CASE WHEN klaim.status_end = 1 THEN 1 ELSE 0 END AS sts_selesai"),
    //                     DB::raw("CASE WHEN retur.ket is not null THEN SUBSTRING(retur.ket, CHARINDEX('|', retur.ket) + 1, LEN(retur.ket) - CHARINDEX('|', retur.ket)) WHEN rtoko.ket is not null THEN rtoko.ket WHEN klaim.keterangan is not null THEN klaim.keterangan ELSE null END AS keterangan"),
    //                     'klaim.qty_klaim'
    //                 )
    //                 ->from(function ($query) use ($request){
    //                     $query->select(
    //                         'klaim.no_dokumen',
    //                         'klaim.tgl_dokumen',
    //                         'klaim_dtl.kd_part',
    //                         'klaim.pc',
    //                         'klaim.kd_dealer',
    //                         'klaim.kd_sales',
    //                         // 'klaim_dtl.qty as qty_klaim',
    //                         DB::raw("sum([klaim_dtl].[qty]) as [qty_klaim]"),
    //                         'klaim_dtl.keterangan',
    //                         'klaim_dtl.sts_klaim',
    //                         'klaim_dtl.sts_min',
    //                         'klaim_dtl.sts_stock',
    //                         'klaim.status_approve',
    //                         'klaim.status_end',
    //                         'klaim.companyid'
    //                     )
    //                     ->from('klaim')
    //                     ->join('klaim_dtl', function ($join) {
    //                         $join->on('klaim_dtl.no_dokumen', '=', 'klaim.no_dokumen')
    //                             ->on('klaim_dtl.companyid', '=', 'klaim.companyid');
    //                     })
    //                     ->groupBy(
    //                         'klaim.no_dokumen',
    //                         'klaim.tgl_dokumen',
    //                         'klaim_dtl.kd_part',
    //                         'klaim.pc',
    //                         'klaim.kd_dealer',
    //                         'klaim.kd_sales',
    //                         'klaim_dtl.keterangan',
    //                         'klaim_dtl.sts_klaim',
    //                         'klaim_dtl.sts_min',
    //                         'klaim_dtl.sts_stock',
    //                         'klaim.status_approve',
    //                         'klaim.status_end',
    //                         'klaim.companyid'
    //                     )
    //                     ->where('klaim.companyid', $request->companyid);
    //                     if(!empty($request->tanggal)){
    //                         $query = $query->whereBetween(DB::raw('CONVERT(DATE, klaim.tgl_dokumen)'), $request->tanggal);
    //                     }
    //                     if(!empty($request->kd_dealer)){
    //                         $query = $query->where('klaim.kd_dealer', $request->kd_dealer);
    //                     }
    //                     if(!empty($request->kd_sales)){
    //                         $query = $query->where('klaim.kd_sales', $request->kd_sales);
    //                     }
    //                 }, 'klaim')
    //                 ->leftJoinSub(function ($query) use ($request){
    //                     $query->select(
    //                         'rtoko.no_retur',
    //                         'rtoko_dtl.no_klaim',
    //                         'rtoko_dtl.kd_part',
    //                         'rtoko_dtl.jumlah as qty_rtoko',
    //                         'rtoko.tanggal',
    //                         'rtoko.kd_dealer',
    //                         'rtoko.kd_sales',
    //                         'rtoko_dtl.ket',
    //                         'rtoko.CompanyId'
    //                     )
    //                     ->from('rtoko')
    //                     ->where('rtoko.companyid', $request->companyid)
    //                     ->join('rtoko_dtl', function ($join) {
    //                         $join->on('rtoko_dtl.no_retur', '=', 'rtoko.no_retur')
    //                             ->on('rtoko_dtl.CompanyId', '=', 'rtoko.CompanyId');
    //                     });
    //                 }, 'rtoko', function ($join) {
    //                     $join->on('rtoko.no_klaim', '=', 'klaim.no_dokumen')
    //                         ->on('rtoko.kd_part', '=', 'klaim.kd_part')
    //                         ->on('rtoko.CompanyId', '=', 'klaim.companyid');
    //                 })
    //                 ->leftJoinSub(function ($query) use ($request) {
    //                     $query->select(
    //                         'retur.no_retur',
    //                         'retur_dtl.no_klaim',
    //                         'retur.tglretur',
    //                         'retur.kd_supp',
    //                         'retur_dtl.kd_part',
    //                         'retur_dtl.jmlretur',
    //                         'retur_dtl.ket',
    //                         'retur_dtl.tgl_jwb',
    //                         'retur_dtl.qty_jwb',
    //                         'retur_dtl.ket_jwb',
    //                         'retur.CompanyId'
    //                     )
    //                     ->from('retur')
    //                     ->join('retur_dtl', function ($join) {
    //                         $join->on('retur_dtl.no_retur', '=', 'retur.no_retur')
    //                             ->on('retur_dtl.CompanyId', '=', 'retur.CompanyId');
    //                     })
    //                     ->where('retur.CompanyId', $request->companyid);

    //                     if(!empty($request->kd_supp)){
    //                         $query = $query->where('retur.kd_supp', $request->kd_supp);
    //                     }
    //                 }, 'retur', function ($join) {
    //                     $join->on('retur.no_klaim', '=', 'rtoko.no_retur')
    //                         ->on('retur.kd_part', '=', 'klaim.kd_part')
    //                         ->on('retur.CompanyId', '=', 'klaim.companyid');
    //                 })
    //                 ->where('klaim.status_end', '=', 1);
    //         }, 'klaim')
    //         ->select(
    //             'no_dok_supp',
    //             'no_retur',
    //             'no_klaim',
    //             'kd_part',
    //             'no_produksi',
    //             'tgl_klaim',
    //             'tgl_rtoko',
    //             'tgl_retur',
    //             'tgl_jwb',
    //             'kd_sales',
    //             'kd_dealer',
    //             'kd_supp',
    //             'sts_stock',
    //             'sts_min',
    //             'sts_klaim',
    //             'sts_approve',
    //             'sts_selesai',
    //             DB::raw("max(keterangan) as keterangan"),
    //             DB::raw("sum(qty_klaim) as qty_klaim")
    //         )
    //         ->groupBy(
    //             'no_dok_supp',
    //             'no_retur',
    //             'no_klaim',
    //             'kd_part',
    //             'no_produksi',
    //             'tgl_klaim',
    //             'tgl_rtoko',
    //             'tgl_retur',
    //             'tgl_jwb',
    //             'kd_sales',
    //             'kd_dealer',
    //             'kd_supp',
    //             'sts_stock',
    //             'sts_min',
    //             'sts_klaim',
    //             'sts_approve',
    //             'sts_selesai'
    //         )

    //         ->orderBy('no_klaim', 'asc')
    //         ->paginate($request->per_page);


    //         $dataJawab = [];

    //         $a = DB::table('jwb_claim')
    //         ->select(
    //             'no_retur',
    //             'no_klaim',
    //             'kd_part',
    //             'no_produksi',
    //             DB::raw("max(tgl_jwb) as tgl_jwb"),
    //             DB::raw("sum(ca) as ca"),
    //             'alasan',
    //             DB::raw("max(ket) as ket"),
    //             'keputusan',
    //             DB::raw("sum(qty_jwb) as qty_jwb")
    //         )
    //         ->whereIn('no_retur', collect($data->items())->pluck('no_dok_supp')->unique()->values()->toArray())
    //         ->where('sts_end', 1)
    //         ->where('CompanyId', $request->companyid)
    //         ->groupBy(
    //             'no_retur',
    //             'no_klaim',
    //             'kd_part',
    //             'alasan',
    //             'keputusan',
    //             'no_produksi'
    //         )
    //         ->get();
    //         $a->map(function($item){
    //             $item->no_produksi = explode(',', $item->no_produksi);
    //             return $item;
    //         });

    //         foreach ($a as $value) {
    //             foreach ($value->no_produksi as $item) {
    //                 $dataJawab[] = (object)[
    //                     'no_retur' => $value->no_retur,
    //                     'no_klaim' => $value->no_klaim,
    //                     'kd_part' => $value->kd_part,
    //                     'no_produksi' => $item,
    //                     'tgl_jwb' => $value->tgl_jwb,
    //                     'ca' => $value->ca/count($value->no_produksi),
    //                     'alasan' => $value->alasan,
    //                     'ket' => $value->ket,
    //                     'keputusan' => $value->keputusan,
    //                     'qty_jwb' => $value->qty_jwb/count($value->no_produksi)
    //                 ];
    //             }
    //         }

    //         $dataJawab = collect($dataJawab);
    //         foreach($data->items() as $value){
    //             if($value->sts_klaim == "TIDAK"){
    //                 $value->qty_klaim = 0;
    //             }
    //             $dataNoProduksi = explode(',', $value->no_produksi);
    //             foreach($dataNoProduksi as $item){
    //                 $x = $dataJawab->where('no_retur', $value->no_dok_supp)->where('no_klaim', $value->no_retur)->where('kd_part', $value->kd_part)->where('no_produksi', $item);

    //                 $value->qty_ganti_barang_terima = ($value->qty_ganti_barang_terima??0) + ($x->where('alasan', 'RETUR')->where('keputusan', 'TERIMA')->isEmpty() ? 0 : $x->where('alasan', 'RETUR')->where('keputusan', 'TERIMA')->first()->qty_jwb);
    //                 $value->qty_ganti_barang_tolak = ($value->qty_ganti_barang_tolak??0) + ($x->where('alasan', 'RETUR')->where('keputusan', 'TOLAK')->isEmpty() ? 0 : $x->where('alasan', 'RETUR')->where('keputusan', 'TOLAK')->first()->qty_jwb);
    //                 $value->qty_ganti_uang_terima = ($value->qty_ganti_uang_terima??0) + ($x->where('alasan', 'CA')->where('keputusan', 'TERIMA')->isEmpty() ? 0 : $x->where('alasan', 'CA')->where('keputusan', 'TERIMA')->first()->qty_jwb);
    //                 $value->qty_ganti_uang_tolak = ($value->qty_ganti_uang_tolak??0) + ($x->where('alasan', 'CA')->where('keputusan', 'TOLAK')->isEmpty() ? 0 : $x->where('alasan', 'CA')->where('keputusan', 'TOLAK')->first()->qty_jwb);
    //                 $value->total_ca = ($value->total_ca??0) + ($x->where('alasan', 'CA')->where('keputusan', 'TERIMA')->isEmpty() ? 0 : $x->where('alasan', 'CA')->where('keputusan', 'TERIMA')->first()->ca);
    //                 $value->qty_jwb = $value->qty_ganti_barang_terima + $value->qty_ganti_uang_terima + $value->qty_ganti_barang_tolak + $value->qty_ganti_uang_tolak;
    //             }
    //         }

    //         return Response()->json([
    //             'status'    => 1,
    //             'message'   => 'success',
    //             'data'      => $data
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return Response()->json([
    //             'status'    => 2,
    //             'message'   => 'Maaf, terjadi kesalahan. Silahkan coba lagi',
    //             'data'      => ''
    //         ], 200);
    //     }
    // }

    public function data(Request $request)
    {
        try {
            if (empty($request->page)) {
                $request->merge(['page' => 1]);
            }

            if (!in_array($request->per_page, ['10', '50', '100', '500'])) {
                $request->merge(['per_page' => 10]);
            }

            $sql = "
                select
                    rtoko.no_retur,
                    rtoko.kd_dealer,
                    dealer.nm_dealer,
                    rtoko_dtl.kd_part,
                    klaim_dtl.qty as qty_klaim,
                    klaim_dtl.tgl_pakai,
                    klaim_dtl.tgl_klaim,
                    CONVERT(NVARCHAR(10),(DATEDIFF(DAY, klaim_dtl.tgl_pakai, klaim_dtl.tgl_klaim))) + ' Hari' AS pemakaian,
                    SUBSTRING(retur_dtl.ket, CHARINDEX('|', retur_dtl.ket) + 1, LEN(retur_dtl.ket)) as ket
                from (
                    select  *
                    from    rtoko
                    where   " . (!empty($request->kd_dealer) ? "kd_dealer = '{$request->kd_dealer}' and " : "") . "CompanyId = '{$request->companyid}'
                ) as rtoko
                inner join rtoko_dtl on rtoko_dtl.no_retur = rtoko.no_retur and
                                        rtoko_dtl.CompanyId = rtoko.CompanyId
                inner join (
                    select
                        no_dokumen,
                        kd_part,
                        sum(qty) as qty,
                        tgl_klaim,
                        tgl_pakai
                    from
                    klaim_dtl
                    where	companyid = '{$request->companyid}' and
                            sts_klaim = 1  and
                            tgl_klaim between '{$request->tanggal[0]}' and '{$request->tanggal[1]}'
                    group by
                        no_dokumen,
                        kd_part,
                        tgl_klaim,
                        tgl_pakai
                ) klaim_dtl on  rtoko_dtl.no_klaim = klaim_dtl.no_dokumen and
                                rtoko_dtl.kd_part = klaim_dtl.kd_part
                inner join retur_dtl on retur_dtl.no_klaim = rtoko_dtl.no_retur and
                                        retur_dtl.kd_part = rtoko_dtl.kd_part and
                                        retur_dtl.CompanyId = rtoko_dtl.CompanyId
                inner join dealer on dealer.kd_dealer = rtoko.kd_dealer
            ";
            $data = DB::table(DB::raw("($sql) as a"))
                ->selectRaw("
                no_retur,
                kd_dealer,
                nm_dealer,
                kd_part,
                sum(qty_klaim) as qty_klaim,
                REPLACE(CONVERT(NVARCHAR(10), tgl_pakai, 105), '-', '/') as tgl_pakai,
                REPLACE(CONVERT(NVARCHAR(10), tgl_klaim, 105), '-', '/') as tgl_klaim,
                pemakaian,
                ket
            ")
                ->groupByRaw("
                no_retur,
                kd_dealer,
                nm_dealer,
                kd_part,
                tgl_pakai,
                tgl_klaim,
                pemakaian,
                ket
            ")
            ->orderBy("kd_dealer", "asc")
            ->paginate($request->per_page);

            return Response()->json([
                'status' => 1,
                'message' => 'success',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return Response()->json([
                'status' => 2,
                'message' => 'Maaf, terjadi kesalahan. Silahkan coba lagi',
                'data' => ''
            ], 200);
        }
    }

    // public function export(Request $request){
    //     try {
    //         $data = DB::table(function ($query) use ($request){
    //                 $query->select(
    //                     'retur.no_retur as no_dok_supp',
    //                     'rtoko.no_retur',
    //                     'klaim.no_dokumen as no_klaim',
    //                     'klaim.kd_part',
    //                     DB::raw("STUFF(
    //                         (SELECT ',' + a.no_produksi
    //                             FROM klaim_dtl AS a
    //                             WHERE a.no_dokumen = klaim.no_dokumen
    //                                 AND a.kd_part = klaim.kd_part
    //                                 AND a.companyid = klaim.companyid
    //                                 AND a.sts_klaim = klaim.sts_klaim
    //                                 AND a.sts_min = klaim.sts_min
    //                                 AND a.sts_stock = klaim.sts_stock
    //                             FOR XML PATH('')), 1, 1, ''
    //                     ) AS no_produksi"),
    //                     'klaim.tgl_dokumen as tgl_klaim',
    //                     'rtoko.tanggal as tgl_rtoko',
    //                     'retur.tglretur as tgl_retur',
    //                     'retur.tgl_jwb as tgl_jwb',
    //                     'klaim.kd_sales',
    //                     'klaim.kd_dealer',
    //                     'retur.kd_supp',
    //                     DB::raw("CASE WHEN klaim.sts_stock = 1 THEN 'Ganti Barang' WHEN klaim.sts_stock = 2 THEN 'Stock 0' WHEN klaim.sts_stock = 3 THEN 'Retur' ELSE null END AS sts_stock"),
    //                     DB::raw("CASE WHEN klaim.sts_min = 1 THEN 'IYA' ELSE 'TIDAK' END AS sts_min"),
    //                     DB::raw("CASE WHEN klaim.sts_klaim = 1 THEN 'IYA' ELSE 'TIDAK' END AS sts_klaim"),
    //                     DB::raw("CASE WHEN klaim.status_approve = 1 THEN 1 ELSE 0 END AS sts_approve"),
    //                     DB::raw("CASE WHEN klaim.status_end = 1 THEN 1 ELSE 0 END AS sts_selesai"),
    //                     DB::raw("CASE WHEN retur.ket is not null THEN SUBSTRING(retur.ket, CHARINDEX('|', retur.ket) + 1, LEN(retur.ket) - CHARINDEX('|', retur.ket)) WHEN rtoko.ket is not null THEN rtoko.ket WHEN klaim.keterangan is not null THEN klaim.keterangan ELSE null END AS keterangan"),
    //                     'klaim.qty_klaim'
    //                 )
    //                 ->from(function ($query) use ($request){
    //                     $query->select(
    //                         'klaim.no_dokumen',
    //                         'klaim.tgl_dokumen',
    //                         'klaim_dtl.kd_part',
    //                         'klaim.pc',
    //                         'klaim.kd_dealer',
    //                         'klaim.kd_sales',
    //                         // 'klaim_dtl.qty as qty_klaim',
    //                         DB::raw("sum([klaim_dtl].[qty]) as [qty_klaim]"),
    //                         'klaim_dtl.keterangan',
    //                         'klaim_dtl.sts_klaim',
    //                         'klaim_dtl.sts_min',
    //                         'klaim_dtl.sts_stock',
    //                         'klaim.status_approve',
    //                         'klaim.status_end',
    //                         'klaim.companyid'
    //                     )
    //                     ->from('klaim')
    //                     ->join('klaim_dtl', function ($join) {
    //                         $join->on('klaim_dtl.no_dokumen', '=', 'klaim.no_dokumen')
    //                             ->on('klaim_dtl.companyid', '=', 'klaim.companyid');
    //                     })
    //                     ->groupBy(
    //                         'klaim.no_dokumen',
    //                         'klaim.tgl_dokumen',
    //                         'klaim_dtl.kd_part',
    //                         'klaim.pc',
    //                         'klaim.kd_dealer',
    //                         'klaim.kd_sales',
    //                         'klaim_dtl.keterangan',
    //                         'klaim_dtl.sts_klaim',
    //                         'klaim_dtl.sts_min',
    //                         'klaim_dtl.sts_stock',
    //                         'klaim.status_approve',
    //                         'klaim.status_end',
    //                         'klaim.companyid'
    //                     )
    //                     ->where('klaim.companyid', $request->companyid);
    //                     if(!empty($request->tanggal)){
    //                         $query = $query->whereBetween(DB::raw('CONVERT(DATE, klaim.tgl_dokumen)'), $request->tanggal);
    //                     }
    //                     if(!empty($request->kd_dealer)){
    //                         $query = $query->where('klaim.kd_dealer', $request->kd_dealer);
    //                     }
    //                     if(!empty($request->kd_sales)){
    //                         $query = $query->where('klaim.kd_sales', $request->kd_sales);
    //                     }
    //                 }, 'klaim')
    //                 ->leftJoinSub(function ($query) use ($request){
    //                     $query->select(
    //                         'rtoko.no_retur',
    //                         'rtoko_dtl.no_klaim',
    //                         'rtoko_dtl.kd_part',
    //                         'rtoko_dtl.jumlah as qty_rtoko',
    //                         'rtoko.tanggal',
    //                         'rtoko.kd_dealer',
    //                         'rtoko.kd_sales',
    //                         'rtoko_dtl.ket',
    //                         'rtoko.CompanyId'
    //                     )
    //                     ->from('rtoko')
    //                     ->where('rtoko.companyid', $request->companyid)
    //                     ->join('rtoko_dtl', function ($join) {
    //                         $join->on('rtoko_dtl.no_retur', '=', 'rtoko.no_retur')
    //                             ->on('rtoko_dtl.CompanyId', '=', 'rtoko.CompanyId');
    //                     });
    //                 }, 'rtoko', function ($join) {
    //                     $join->on('rtoko.no_klaim', '=', 'klaim.no_dokumen')
    //                         ->on('rtoko.kd_part', '=', 'klaim.kd_part')
    //                         ->on('rtoko.CompanyId', '=', 'klaim.companyid');
    //                 })
    //                 ->leftJoinSub(function ($query) use ($request) {
    //                     $query->select(
    //                         'retur.no_retur',
    //                         'retur_dtl.no_klaim',
    //                         'retur.tglretur',
    //                         'retur.kd_supp',
    //                         'retur_dtl.kd_part',
    //                         'retur_dtl.jmlretur',
    //                         'retur_dtl.ket',
    //                         'retur_dtl.tgl_jwb',
    //                         'retur_dtl.qty_jwb',
    //                         'retur_dtl.ket_jwb',
    //                         'retur.CompanyId'
    //                     )
    //                     ->from('retur')
    //                     ->join('retur_dtl', function ($join) {
    //                         $join->on('retur_dtl.no_retur', '=', 'retur.no_retur')
    //                             ->on('retur_dtl.CompanyId', '=', 'retur.CompanyId');
    //                     })
    //                     ->where('retur.CompanyId', $request->companyid);

    //                     if(!empty($request->kd_supp)){
    //                         $query = $query->where('retur.kd_supp', $request->kd_supp);
    //                     }
    //                 }, 'retur', function ($join) {
    //                     $join->on('retur.no_klaim', '=', 'rtoko.no_retur')
    //                         ->on('retur.kd_part', '=', 'klaim.kd_part')
    //                         ->on('retur.CompanyId', '=', 'klaim.companyid');
    //                 })
    //                 ->where('klaim.status_end', '=', 1);
    //         }, 'klaim')
    //         ->select(
    //             'no_dok_supp',
    //             'no_retur',
    //             'no_klaim',
    //             'kd_part',
    //             'no_produksi',
    //             'tgl_klaim',
    //             'tgl_rtoko',
    //             'tgl_retur',
    //             'tgl_jwb',
    //             'kd_sales',
    //             'kd_dealer',
    //             'kd_supp',
    //             'sts_stock',
    //             'sts_min',
    //             'sts_klaim',
    //             'sts_approve',
    //             'sts_selesai',
    //             DB::raw("max(keterangan) as keterangan"),
    //             DB::raw("sum(qty_klaim) as qty_klaim")
    //         )
    //         ->groupBy(
    //             'no_dok_supp',
    //             'no_retur',
    //             'no_klaim',
    //             'kd_part',
    //             'no_produksi',
    //             'tgl_klaim',
    //             'tgl_rtoko',
    //             'tgl_retur',
    //             'tgl_jwb',
    //             'kd_sales',
    //             'kd_dealer',
    //             'kd_supp',
    //             'sts_stock',
    //             'sts_min',
    //             'sts_klaim',
    //             'sts_approve',
    //             'sts_selesai'
    //         )

    //         ->orderBy('no_klaim', 'asc')
    //         ->get();

    //         $dataJawab = [];

    //         $a = DB::table('jwb_claim')
    //         ->select(
    //             'no_retur',
    //             'no_klaim',
    //             'kd_part',
    //             'no_produksi',
    //             DB::raw("max(tgl_jwb) as tgl_jwb"),
    //             DB::raw("sum(ca) as ca"),
    //             'alasan',
    //             DB::raw("max(ket) as ket"),
    //             'keputusan',
    //             DB::raw("sum(qty_jwb) as qty_jwb")
    //         )
    //         ->whereIn('no_retur', collect($data)->pluck('no_dok_supp')->unique()->values()->toArray())
    //         ->where('sts_end', 1)
    //         ->where('CompanyId', $request->companyid)
    //         ->groupBy(
    //             'no_retur',
    //             'no_klaim',
    //             'kd_part',
    //             'alasan',
    //             'keputusan',
    //             'no_produksi'
    //         )
    //         ->get();
    //         $a->map(function($item){
    //             $item->no_produksi = explode(',', $item->no_produksi);
    //             return $item;
    //         });

    //         foreach ($a as $value) {
    //             foreach ($value->no_produksi as $item) {
    //                 $dataJawab[] = (object)[
    //                     'no_retur' => $value->no_retur,
    //                     'no_klaim' => $value->no_klaim,
    //                     'kd_part' => $value->kd_part,
    //                     'no_produksi' => $item,
    //                     'tgl_jwb' => $value->tgl_jwb,
    //                     'ca' => $value->ca/count($value->no_produksi),
    //                     'alasan' => $value->alasan,
    //                     'ket' => $value->ket,
    //                     'keputusan' => $value->keputusan,
    //                     'qty_jwb' => $value->qty_jwb/count($value->no_produksi)
    //                 ];
    //             }
    //         }

    //         $dataJawab = collect($dataJawab);
    //         foreach($data as $value){
    //             if($value->sts_klaim == "TIDAK"){
    //                 $value->qty_klaim = 0;
    //             }
    //             $dataNoProduksi = explode(',', $value->no_produksi);
    //             foreach($dataNoProduksi as $item){
    //                 $x = $dataJawab->where('no_retur', $value->no_dok_supp)->where('no_klaim', $value->no_retur)->where('kd_part', $value->kd_part)->where('no_produksi', $item);
    //                 $value->qty_ganti_barang_terima = ($value->qty_ganti_barang_terima??0) + ($x->where('alasan', 'RETUR')->where('keputusan', 'TERIMA')->isEmpty() ? 0 : $x->where('alasan', 'RETUR')->where('keputusan', 'TERIMA')->first()->qty_jwb);
    //                 $value->qty_ganti_barang_tolak = ($value->qty_ganti_barang_tolak??0) + ($x->where('alasan', 'RETUR')->where('keputusan', 'TOLAK')->isEmpty() ? 0 : $x->where('alasan', 'RETUR')->where('keputusan', 'TOLAK')->first()->qty_jwb);
    //                 $value->qty_ganti_uang_terima = ($value->qty_ganti_uang_terima??0) + ($x->where('alasan', 'CA')->where('keputusan', 'TERIMA')->isEmpty() ? 0 : $x->where('alasan', 'CA')->where('keputusan', 'TERIMA')->first()->qty_jwb);
    //                 $value->qty_ganti_uang_tolak = ($value->qty_ganti_uang_tolak??0) + ($x->where('alasan', 'CA')->where('keputusan', 'TOLAK')->isEmpty() ? 0 : $x->where('alasan', 'CA')->where('keputusan', 'TOLAK')->first()->qty_jwb);
    //                 $value->total_ca = ($value->total_ca??0) + ($x->where('alasan', 'CA')->where('keputusan', 'TERIMA')->isEmpty() ? 0 : $x->where('alasan', 'CA')->where('keputusan', 'TERIMA')->first()->ca);
    //                 $value->qty_jwb = $value->qty_ganti_barang_terima + $value->qty_ganti_uang_terima + $value->qty_ganti_barang_tolak + $value->qty_ganti_uang_tolak;
    //             }
    //         }

    //         $data = $data->map(function($item){
    //             return [
    //                 'No Dokumen' => $item->no_klaim,
    //                 'Kode Part' => $item->kd_part,
    //                 'Tgl Klaim' => $item->tgl_klaim,
    //                 'Tgl Klaim SPV' => $item->tgl_rtoko,
    //                 'Tgl Retur Supplier' => $item->tgl_retur,
    //                 'Tgl Jawab terakhir' => $item->tgl_jwb,
    //                 'Kode Salse' => $item->kd_sales,
    //                 'Kode Dealer' => $item->kd_dealer,
    //                 'Kode Supplier' => $item->kd_supp,
    //                 'Status Stock' => $item->sts_stock,
    //                 'Status Minimum' => $item->sts_min,
    //                 'Status Klaim' => $item->sts_klaim,
    //                 'Status Approve SPV' => $item->sts_approve == 1 ? 'Sudah Approve' : 'Belum Approve',
    //                 'Setatus Retur Selesai' => $item->sts_selesai == 1 ? 'Sudah Selesai' : 'Belum Selesai',
    //                 'keterangan' => $item->keterangan,
    //                 'QTY Klaim' => $item->qty_klaim??0,
    //                 'QTY Dijawab' => $item->qty_jwb??0,
    //                 'QTY Ganti Barang Diterima' => $item->qty_ganti_barang_terima??0,
    //                 'QTY Ganti Barang Ditolak' => $item->qty_ganti_barang_tolak??0,
    //                 'QTY Ganti Uang Terima' => $item->qty_ganti_uang_terima??0,
    //                 'QTY Ganti Uang Tolak' => $item->qty_ganti_uang_tolak??0,
    //                 'Total Ganti Uang' => $item->total_ca??0
    //             ];
    //         });

    //         return Response()->json([
    //             'status'    => 1,
    //             'message'   => 'success',
    //             'data'      => $data
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return Response()->json([
    //             'status'    => 2,
    //             'message'   => 'Maaf, terjadi kesalahan. Silahkan coba lagi',
    //             'data'      => ''
    //         ], 200);
    //     }
    // }

    public function export(Request $request)
    {
        try {
        $sql = "
            select
                no_retur,
                kd_dealer,
                nm_dealer,
                kd_part,
                sum(qty_klaim) as qty_klaim,
                REPLACE(CONVERT(NVARCHAR(10), tgl_pakai, 105), '-', '/') as tgl_pakai,
                REPLACE(CONVERT(NVARCHAR(10), tgl_klaim, 105), '-', '/') as tgl_klaim,
                pemakaian,
                ket
            from (
                select
                    rtoko.no_retur,
                    rtoko.kd_dealer,
                    dealer.nm_dealer,
                    rtoko_dtl.kd_part,
                    klaim_dtl.qty as qty_klaim,
                    klaim_dtl.tgl_pakai,
                    klaim_dtl.tgl_klaim,
                    CONVERT(NVARCHAR(10),(DATEDIFF(DAY, klaim_dtl.tgl_pakai, klaim_dtl.tgl_klaim))) AS pemakaian,
                    SUBSTRING(retur_dtl.ket, CHARINDEX('|', retur_dtl.ket) + 1, LEN(retur_dtl.ket)) as ket
                from (
                    select  *
                    from    rtoko
                    where   " .(!empty($request->kd_dealer) ? "kd_dealer = '{$request->kd_dealer}' and " : "") . "CompanyId = '{$request->companyid}'
                ) as rtoko
                inner join rtoko_dtl on rtoko_dtl.no_retur = rtoko.no_retur and
                                        rtoko_dtl.CompanyId = rtoko.CompanyId
                inner join (
                    select
                        no_dokumen,
                        kd_part,
                        sum(qty) as qty,
                        tgl_klaim,
                        tgl_pakai
                    from
                    klaim_dtl
                    where	companyid = '{$request->companyid}' and
                            sts_klaim = 1  and
                            tgl_klaim between '{$request->tanggal[0]}' and '{$request->tanggal[1]}'
                    group by
                        no_dokumen,
                        kd_part,
                        tgl_klaim,
                        tgl_pakai
                ) klaim_dtl on  rtoko_dtl.no_klaim = klaim_dtl.no_dokumen and
                                rtoko_dtl.kd_part = klaim_dtl.kd_part
                inner join retur_dtl on retur_dtl.no_klaim = rtoko_dtl.no_retur and
                                        retur_dtl.kd_part = rtoko_dtl.kd_part and
                                        retur_dtl.CompanyId = rtoko_dtl.CompanyId
                inner join dealer on dealer.kd_dealer = rtoko.kd_dealer
            ) as a
            group by
                no_retur,
                kd_dealer,
                nm_dealer,
                kd_part,
                tgl_pakai,
                tgl_klaim,
                pemakaian,
                ket
            ";
        $data = DB::table(DB::raw("($sql) as b"))
            ->selectRaw("
                kd_dealer,
                nm_dealer,
                kd_part,
                qty_klaim,
                tgl_pakai,
                tgl_klaim,
                pemakaian,
                ket
            ")
            ->orderBy("kd_dealer", "asc")
            ->get();

        return Response()->json([
            'status' => 1,
            'message' => 'success',
            'data' => $data
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
