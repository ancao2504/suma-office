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

            $sql = "
                select
                    klaim.no_faktur,
                    [retur].[no_retur],
                    klaim.no_dokumen as no_klaim,
                    rtoko.no_retur as no_rtoko,
                    klaim.kd_dealer,
                    dealer.nm_dealer,
                    [klaim].[kd_sales],
                    [klaim].[kd_part],
                    klaim.qty_klaim,
                    klaim.tgl_klaim,
                    klaim.tgl_pakai,
                    klaim.pemakaian,
                    CASE WHEN retur.ket is not null THEN SUBSTRING(retur.ket, CHARINDEX('|', retur.ket) + 1, LEN(retur.ket) - CHARINDEX('|', retur.ket)) WHEN rtoko.ket is not null THEN rtoko.ket WHEN klaim.keterangan is not null THEN klaim.keterangan ELSE null END AS keterangan,
                    CASE WHEN klaim.sts_stock = 1 THEN 'Ganti Barang' WHEN klaim.sts_stock = 2 THEN 'Stock 0' WHEN klaim.sts_stock = 3 THEN 'Retur' ELSE null END AS sts_stock,
                    CASE WHEN klaim.sts_min = 1 THEN 'IYA' ELSE 'TIDAK' END AS sts_min,
                    CASE WHEN klaim.sts_klaim = 1 THEN 'IYA' ELSE 'TIDAK' END AS sts_klaim,
                    CASE WHEN klaim.status_approve = 1 THEN 1 ELSE 0 END AS sts_approve,
                    CASE WHEN klaim.status_end = 1 THEN 1 ELSE 0 END AS sts_selesai
                from
                (
                    select
                        klaim_dtl.no_faktur,
                        [klaim].[no_dokumen],
                        [klaim_dtl].[kd_part],
                        [klaim].[pc],
                        [klaim].[kd_dealer],
                        [klaim].[kd_sales],
                        REPLACE(CONVERT(NVARCHAR(10), tgl_pakai, 105), '-', '/') as tgl_pakai,
                        REPLACE(CONVERT(NVARCHAR(10), tgl_klaim, 105), '-', '/') as tgl_klaim,
                        CONVERT(NVARCHAR(10),(DATEDIFF(DAY, klaim_dtl.tgl_pakai, klaim_dtl.tgl_klaim))) AS pemakaian,
                        sum([klaim_dtl].[qty]) as [qty_klaim],
                        [klaim_dtl].[keterangan],
                        [klaim_dtl].[sts_klaim],
                        [klaim_dtl].[sts_min],
                        [klaim_dtl].[sts_stock],
                        [klaim].[status_approve],
                        [klaim].[status_end]
                    from [klaim]
                    inner join [klaim_dtl] on [klaim_dtl].[no_dokumen] = [klaim].[no_dokumen] and [klaim_dtl].[companyid] = [klaim].[companyid]
                    where [klaim].[companyid] = '{$request->companyid}' and
                    CONVERT(DATE, klaim_dtl.tgl_klaim) between '{$request->tanggal[0]}' and '{$request->tanggal[1]}'";
                    if (!empty($request->kd_dealer)){
                        $sql .= " and [klaim].[kd_dealer] = '{$request->kd_dealer}'";
                    }
                    if (!empty($request->kd_sales)){
                        $sql .= " and [klaim].[kd_sales] = '{$request->kd_sales}'";
                    }
                    $sql .="
                    group by
                        klaim_dtl.no_faktur,
                        [klaim].[no_dokumen],
                        [klaim_dtl].[kd_part],
                        [klaim].[pc],
                        [klaim].[kd_dealer],
                        [klaim].[kd_sales],
                        REPLACE(CONVERT(NVARCHAR(10), tgl_pakai, 105), '-', '/'),
                        REPLACE(CONVERT(NVARCHAR(10), tgl_klaim, 105), '-', '/'),
                        CONVERT(NVARCHAR(10),(DATEDIFF(DAY, klaim_dtl.tgl_pakai, klaim_dtl.tgl_klaim))),
                        [klaim_dtl].[keterangan],
                        [klaim_dtl].[sts_klaim],
                        [klaim_dtl].[sts_min],
                        [klaim_dtl].[sts_stock],
                        [klaim].[status_approve],
                        [klaim].[status_end]
                ) klaim
                inner join (
                    select kd_dealer, nm_dealer from dealer where CompanyId = '{$request->companyid}'
                ) dealer on dealer.kd_dealer = klaim.kd_dealer
                inner join (
                    select
                        no_faktur,
                        kd_part
                    from
                        fakt_dtl
                    where CompanyId = '{$request->companyid}'
                ) as fakt_dtl on fakt_dtl.no_faktur = klaim.no_faktur and fakt_dtl.kd_part = klaim.kd_part
                left join
                (
                    select
                        [rtoko].[no_retur],
                        [rtoko_dtl].[no_klaim],
                        [rtoko_dtl].[kd_part],
                        [rtoko_dtl].[jumlah] as [qty_rtoko],
                        [rtoko].[tanggal],
                        [rtoko].[kd_dealer],
                        [rtoko].[kd_sales],
                        [rtoko_dtl].[ket],
                        [rtoko].[CompanyId]
                    from [rtoko]
                    inner join [rtoko_dtl] on [rtoko_dtl].[no_retur] = [rtoko].[no_retur] and [rtoko_dtl].[CompanyId] = [rtoko].[CompanyId]
                    where [rtoko].[companyid] = '{$request->companyid}'
                ) as [rtoko] on [rtoko].[no_klaim] = [klaim].[no_dokumen] and [rtoko].[kd_part] = [klaim].[kd_part]
                left join
                (
                    select
                        [retur].[no_retur],
                        [retur_dtl].[no_klaim],
                        [retur].[tglretur],
                        [retur].[kd_supp],
                        [retur_dtl].[kd_part],
                        [retur_dtl].[jmlretur],
                        [retur_dtl].[ket],
                        [retur_dtl].[tgl_jwb],
                        [retur_dtl].[qty_jwb],
                        [retur_dtl].[ket_jwb],
                        [retur].[CompanyId] from [retur]
                    inner join [retur_dtl] on [retur_dtl].[no_retur] = [retur].[no_retur] and [retur_dtl].[CompanyId] = [retur].[CompanyId]
                    where [retur].[CompanyId] = '{$request->companyid}'
                ) as [retur] on [retur].[no_klaim] = [rtoko].[no_retur] and [retur].[kd_part] = [klaim].[kd_part]
            ";

            $data = DB::table(DB::raw("($sql) as a"))
            ->selectRaw(
                '
                    no_faktur,
                    no_retur,
                    no_klaim,
                    no_rtoko,
                    kd_dealer,
                    nm_dealer,
                    kd_sales,
                    kd_part,
                    qty_klaim,
                    0 as qty_jwb,
                    tgl_klaim,
                    tgl_pakai,
                    pemakaian,
                    keterangan,
                    sts_stock,
                    sts_min,
                    sts_klaim,
                    sts_approve,
                    sts_selesai
                '
            )
            ->orderBy('kd_dealer', 'asc')
            ->orderBy('kd_sales', 'asc')
            ->orderBy('kd_part', 'asc')
            ->orderBy('tgl_klaim', 'asc')
            ->get();

            $dataJawab = DB::table('jwb_claim')
            ->selectRaw(
                '
                    no_klaim,
                    kd_part,
                    no_retur,
                    sum(qty_jwb) as qty_jwb
                '
            )
            ->whereIn('no_klaim', collect($data)->pluck('no_rtoko')->unique()->values()->toArray())
            ->where('sts_end', 1)
            ->where('CompanyId', $request->companyid)
            ->groupByRaw(
                '
                    no_klaim,
                    kd_part,
                    no_retur
                '
            )
            ->get();

            $dataFilter = collect($data)->groupBy('no_rtoko')->map(function($item){
                return $item->groupBy('kd_part');
            });

            $dataJawab->map(function($item) use ($dataFilter, $data) {
                if(!empty($dataFilter[$item->no_klaim][$item->kd_part])){
                    (int)$qtyJwbTamp = (int)$item->qty_jwb;
                    foreach($dataFilter[$item->no_klaim][$item->kd_part] as $key => $value){
                            if($qtyJwbTamp >= 0){
                                if((int)$qtyJwbTamp > (int)$value->qty_klaim){
                                    $data->where('no_retur', $value->no_retur)->where('no_faktur', $value->no_faktur)->where('no_klaim', $value->no_klaim)->where('kd_part', $value->kd_part)->first()->qty_jwb = (int)$value->qty_klaim;
                                    (int)$qtyJwbTamp = ((int)$qtyJwbTamp - (int)$value->qty_klaim);
                                } elseif((int)$qtyJwbTamp == (int)$value->qty_klaim){
                                    $data->where('no_retur', $value->no_retur)->where('no_faktur', $value->no_faktur)->where('no_klaim', $value->no_klaim)->where('kd_part', $value->kd_part)->first()->qty_jwb = (int)$value->qty_klaim;

                                    (int)$qtyJwbTamp = 0;
                                } elseif((int)$qtyJwbTamp < (int)$value->qty_klaim){
                                    $data->where('no_retur', $value->no_retur)->where('no_faktur', $value->no_faktur)->where('no_klaim', $value->no_klaim)->where('kd_part', $value->kd_part)->first()->qty_jwb = (int)$qtyJwbTamp;

                                    (int)$qtyJwbTamp = 0;
                                }
                            } else {
                                $data->where('no_retur', $value->no_retur)->where('no_faktur', $value->no_faktur)->where('no_klaim', $value->no_klaim)->where('kd_part', $value->kd_part)->first()->qty_jwb =  0;
                            }
                    }

                }
            });

            // buat pagination untuk $data
            $paginationData = new \Illuminate\Pagination\LengthAwarePaginator(
                ($data->slice(($request->page - 1) * $request->per_page, $request->per_page)),
                $data->count(),
                $request->per_page,
                $request->page
            );



            return Response()->json([
                'status'    => 1,
                'message'   => 'success',
                'data'      => $paginationData
            ], 200);
        } catch (\Exception $e) {
            return Response()->json([
                'status'    => 2,
                'message'   => 'Maaf, terjadi kesalahan. Silahkan coba lagi',
                'data'      => ''
            ], 200);
        }
    }

    // public function data(Request $request)
    // {
    //     try {
    //         if (empty($request->page)) {
    //             $request->merge(['page' => 1]);
    //         }

    //         if (!in_array($request->per_page, ['10', '50', '100', '500'])) {
    //             $request->merge(['per_page' => 10]);
    //         }

    //         $sql = "
    //             select
    //                 rtoko.no_retur,
    //                 rtoko.kd_dealer,
    //                 dealer.nm_dealer,
    //                 rtoko_dtl.kd_part,
    //                 klaim_dtl.qty as qty_klaim,
    //                 klaim_dtl.tgl_pakai,
    //                 klaim_dtl.tgl_klaim,
    //                 CONVERT(NVARCHAR(10),(DATEDIFF(DAY, klaim_dtl.tgl_pakai, klaim_dtl.tgl_klaim))) + ' Hari' AS pemakaian,
    //                 SUBSTRING(retur_dtl.ket, CHARINDEX('|', retur_dtl.ket) + 1, LEN(retur_dtl.ket)) as ket
    //             from (
    //                 select  *
    //                 from    rtoko
    //                 where   " . (!empty($request->kd_dealer) ? "kd_dealer = '{$request->kd_dealer}' and " : "") . "CompanyId = '{$request->companyid}'
    //             ) as rtoko
    //             inner join rtoko_dtl on rtoko_dtl.no_retur = rtoko.no_retur and
    //                                     rtoko_dtl.CompanyId = rtoko.CompanyId
    //             inner join (
    //                 select
    //                     no_dokumen,
    //                     kd_part,
    //                     sum(qty) as qty,
    //                     tgl_klaim,
    //                     tgl_pakai
    //                 from
    //                 klaim_dtl
    //                 where	companyid = '{$request->companyid}' and
    //                         sts_klaim = 1  and
    //                         tgl_klaim between '{$request->tanggal[0]}' and '{$request->tanggal[1]}'
    //                 group by
    //                     no_dokumen,
    //                     kd_part,
    //                     tgl_klaim,
    //                     tgl_pakai
    //             ) klaim_dtl on  rtoko_dtl.no_klaim = klaim_dtl.no_dokumen and
    //                             rtoko_dtl.kd_part = klaim_dtl.kd_part
    //             inner join retur_dtl on retur_dtl.no_klaim = rtoko_dtl.no_retur and
    //                                     retur_dtl.kd_part = rtoko_dtl.kd_part and
    //                                     retur_dtl.CompanyId = rtoko_dtl.CompanyId
    //             inner join dealer on dealer.kd_dealer = rtoko.kd_dealer and dealer.CompanyId = rtoko.CompanyId
    //         ";
    //         $data = DB::table(DB::raw("($sql) as a"))
    //             ->selectRaw("
    //             no_retur,
    //             kd_dealer,
    //             nm_dealer,
    //             kd_part,
    //             sum(qty_klaim) as qty_klaim,
    //             REPLACE(CONVERT(NVARCHAR(10), tgl_pakai, 105), '-', '/') as tgl_pakai,
    //             REPLACE(CONVERT(NVARCHAR(10), tgl_klaim, 105), '-', '/') as tgl_klaim,
    //             pemakaian,
    //             ket
    //         ")
    //             ->groupByRaw("
    //             no_retur,
    //             kd_dealer,
    //             nm_dealer,
    //             kd_part,
    //             tgl_pakai,
    //             tgl_klaim,
    //             pemakaian,
    //             ket
    //         ")
    //         ->orderBy("kd_dealer", "asc")
    //         ->paginate($request->per_page);

    //         return Response()->json([
    //             'status' => 1,
    //             'message' => 'success',
    //             'data' => $data
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return Response()->json([
    //             'status' => 2,
    //             'message' => 'Maaf, terjadi kesalahan. Silahkan coba lagi',
    //             'data' => ''
    //         ], 200);
    //     }
    // }

    public function export(Request $request){
        try {

            $sql = "
                select
                    klaim.no_faktur,
                    [retur].[no_retur],
                    klaim.no_dokumen as no_klaim,
                    rtoko.no_retur as no_rtoko,
                    klaim.kd_dealer,
                    dealer.nm_dealer,
                    [klaim].[kd_sales],
                    [klaim].[kd_part],
                    klaim.qty_klaim,
                    klaim.tgl_klaim,
                    klaim.tgl_pakai,
                    klaim.pemakaian,
                    CASE WHEN retur.ket is not null THEN SUBSTRING(retur.ket, CHARINDEX('|', retur.ket) + 1, LEN(retur.ket) - CHARINDEX('|', retur.ket)) WHEN rtoko.ket is not null THEN rtoko.ket WHEN klaim.keterangan is not null THEN klaim.keterangan ELSE null END AS keterangan,
                    CASE WHEN klaim.sts_stock = 1 THEN 'Ganti Barang' WHEN klaim.sts_stock = 2 THEN 'Stock 0' WHEN klaim.sts_stock = 3 THEN 'Retur' ELSE null END AS sts_stock,
                    CASE WHEN klaim.sts_min = 1 THEN 'IYA' ELSE 'TIDAK' END AS sts_min,
                    CASE WHEN klaim.sts_klaim = 1 THEN 'IYA' ELSE 'TIDAK' END AS sts_klaim,
                    CASE WHEN klaim.status_approve = 1 THEN 1 ELSE 0 END AS sts_approve,
                    CASE WHEN klaim.status_end = 1 THEN 1 ELSE 0 END AS sts_selesai
                from
                (
                    select
                        klaim_dtl.no_faktur,
                        [klaim].[no_dokumen],
                        [klaim_dtl].[kd_part],
                        [klaim].[pc],
                        [klaim].[kd_dealer],
                        [klaim].[kd_sales],
                        REPLACE(CONVERT(NVARCHAR(10), tgl_pakai, 105), '-', '/') as tgl_pakai,
                        REPLACE(CONVERT(NVARCHAR(10), tgl_klaim, 105), '-', '/') as tgl_klaim,
                        CONVERT(NVARCHAR(10),(DATEDIFF(DAY, klaim_dtl.tgl_pakai, klaim_dtl.tgl_klaim))) AS pemakaian,
                        sum([klaim_dtl].[qty]) as [qty_klaim],
                        [klaim_dtl].[keterangan],
                        [klaim_dtl].[sts_klaim],
                        [klaim_dtl].[sts_min],
                        [klaim_dtl].[sts_stock],
                        [klaim].[status_approve],
                        [klaim].[status_end]
                    from [klaim]
                    inner join [klaim_dtl] on [klaim_dtl].[no_dokumen] = [klaim].[no_dokumen] and [klaim_dtl].[companyid] = [klaim].[companyid]
                    where [klaim].[companyid] = '{$request->companyid}' and
                    CONVERT(DATE, klaim_dtl.tgl_klaim) between '{$request->tanggal[0]}' and '{$request->tanggal[1]}'";
                    if (!empty($request->kd_dealer)){
                        $sql .= " and [klaim].[kd_dealer] = '{$request->kd_dealer}'";
                    }
                    if (!empty($request->kd_sales)){
                        $sql .= " and [klaim].[kd_sales] = '{$request->kd_sales}'";
                    }
                    $sql .="
                    group by
                        klaim_dtl.no_faktur,
                        [klaim].[no_dokumen],
                        [klaim_dtl].[kd_part],
                        [klaim].[pc],
                        [klaim].[kd_dealer],
                        [klaim].[kd_sales],
                        REPLACE(CONVERT(NVARCHAR(10), tgl_pakai, 105), '-', '/'),
                        REPLACE(CONVERT(NVARCHAR(10), tgl_klaim, 105), '-', '/'),
                        CONVERT(NVARCHAR(10),(DATEDIFF(DAY, klaim_dtl.tgl_pakai, klaim_dtl.tgl_klaim))),
                        [klaim_dtl].[keterangan],
                        [klaim_dtl].[sts_klaim],
                        [klaim_dtl].[sts_min],
                        [klaim_dtl].[sts_stock],
                        [klaim].[status_approve],
                        [klaim].[status_end]
                ) klaim
                inner join (
                    select kd_dealer, nm_dealer from dealer where CompanyId = '{$request->companyid}'
                ) dealer on dealer.kd_dealer = klaim.kd_dealer
                inner join (
                    select
                        no_faktur,
                        kd_part
                    from
                        fakt_dtl
                    where CompanyId = '{$request->companyid}'
                ) as fakt_dtl on fakt_dtl.no_faktur = klaim.no_faktur and fakt_dtl.kd_part = klaim.kd_part
                left join
                (
                    select
                        [rtoko].[no_retur],
                        [rtoko_dtl].[no_klaim],
                        [rtoko_dtl].[kd_part],
                        [rtoko_dtl].[jumlah] as [qty_rtoko],
                        [rtoko].[tanggal],
                        [rtoko].[kd_dealer],
                        [rtoko].[kd_sales],
                        [rtoko_dtl].[ket],
                        [rtoko].[CompanyId]
                    from [rtoko]
                    inner join [rtoko_dtl] on [rtoko_dtl].[no_retur] = [rtoko].[no_retur] and [rtoko_dtl].[CompanyId] = [rtoko].[CompanyId]
                    where [rtoko].[companyid] = '{$request->companyid}'
                ) as [rtoko] on [rtoko].[no_klaim] = [klaim].[no_dokumen] and [rtoko].[kd_part] = [klaim].[kd_part]
                left join
                (
                    select
                        [retur].[no_retur],
                        [retur_dtl].[no_klaim],
                        [retur].[tglretur],
                        [retur].[kd_supp],
                        [retur_dtl].[kd_part],
                        [retur_dtl].[jmlretur],
                        [retur_dtl].[ket],
                        [retur_dtl].[tgl_jwb],
                        [retur_dtl].[qty_jwb],
                        [retur_dtl].[ket_jwb],
                        [retur].[CompanyId] from [retur]
                    inner join [retur_dtl] on [retur_dtl].[no_retur] = [retur].[no_retur] and [retur_dtl].[CompanyId] = [retur].[CompanyId]
                    where [retur].[CompanyId] = '{$request->companyid}'
                ) as [retur] on [retur].[no_klaim] = [rtoko].[no_retur] and [retur].[kd_part] = [klaim].[kd_part]
            ";

            $data = DB::table(DB::raw("($sql) as a"))
            ->selectRaw(
                '
                    no_faktur,
                    no_retur,
                    no_klaim,
                    no_rtoko,
                    kd_dealer,
                    nm_dealer,
                    kd_sales,
                    kd_part,
                    qty_klaim,
                    0 as qty_jwb,
                    tgl_klaim,
                    tgl_pakai,
                    pemakaian,
                    keterangan,
                    sts_stock,
                    sts_min,
                    sts_klaim,
                    sts_approve,
                    sts_selesai
                '
            )
            ->orderBy('kd_dealer', 'asc')
            ->orderBy('kd_sales', 'asc')
            ->orderBy('kd_part', 'asc')
            ->orderBy('tgl_klaim', 'asc')
            ->get();

            $dataJawab = DB::table('jwb_claim')
            ->selectRaw(
                '
                    no_klaim,
                    kd_part,
                    no_retur,
                    sum(qty_jwb) as qty_jwb
                '
            )
            ->whereIn('no_klaim', collect($data)->pluck('no_rtoko')->unique()->values()->toArray())
            ->where('sts_end', 1)
            ->where('CompanyId', $request->companyid)
            ->groupByRaw(
                '
                    no_klaim,
                    kd_part,
                    no_retur
                '
            )
            ->get();

            $dataFilter = collect($data)->groupBy('no_rtoko')->map(function($item){
                return $item->groupBy('kd_part');
            });

            $dataJawab->map(function($item) use ($dataFilter, $data) {
                if(!empty($dataFilter[$item->no_klaim][$item->kd_part])){
                    (int)$qtyJwbTamp = (int)$item->qty_jwb;
                    foreach($dataFilter[$item->no_klaim][$item->kd_part] as $key => $value){
                        if($qtyJwbTamp >= 0){
                            if((int)$qtyJwbTamp > (int)$value->qty_klaim){
                                $data->where('no_retur', $value->no_retur)->where('no_faktur', $value->no_faktur)->where('no_klaim', $value->no_klaim)->where('kd_part', $value->kd_part)->first()->qty_jwb = (int)$value->qty_klaim;
                                (int)$qtyJwbTamp = ((int)$qtyJwbTamp - (int)$value->qty_klaim);
                            } elseif((int)$qtyJwbTamp == (int)$value->qty_klaim){
                                $data->where('no_retur', $value->no_retur)->where('no_faktur', $value->no_faktur)->where('no_klaim', $value->no_klaim)->where('kd_part', $value->kd_part)->first()->qty_jwb = (int)$value->qty_klaim;

                                (int)$qtyJwbTamp = 0;
                            } elseif((int)$qtyJwbTamp < (int)$value->qty_klaim){
                                $data->where('no_retur', $value->no_retur)->where('no_faktur', $value->no_faktur)->where('no_klaim', $value->no_klaim)->where('kd_part', $value->kd_part)->first()->qty_jwb = (int)$qtyJwbTamp;

                                (int)$qtyJwbTamp = 0;
                            }
                        } else {
                            $data->where('no_retur', $value->no_retur)->where('no_faktur', $value->no_faktur)->where('no_klaim', $value->no_klaim)->where('kd_part', $value->kd_part)->first()->qty_jwb =  0;
                        }
                    }
                }
            });

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

    // public function export(Request $request)
    // {
    //     try {
    //     $sql = "
    //         select
    //             no_retur,
    //             kd_dealer,
    //             nm_dealer,
    //             kd_part,
    //             sum(qty_klaim) as qty_klaim,
    //             REPLACE(CONVERT(NVARCHAR(10), tgl_pakai, 105), '-', '/') as tgl_pakai,
    //             REPLACE(CONVERT(NVARCHAR(10), tgl_klaim, 105), '-', '/') as tgl_klaim,
    //             pemakaian,
    //             ket
    //         from (
    //             select
    //                 rtoko.no_retur,
    //                 rtoko.kd_dealer,
    //                 dealer.nm_dealer,
    //                 rtoko_dtl.kd_part,
    //                 klaim_dtl.qty as qty_klaim,
    //                 klaim_dtl.tgl_pakai,
    //                 klaim_dtl.tgl_klaim,
    //                 CONVERT(NVARCHAR(10),(DATEDIFF(DAY, klaim_dtl.tgl_pakai, klaim_dtl.tgl_klaim))) AS pemakaian,
    //                 SUBSTRING(retur_dtl.ket, CHARINDEX('|', retur_dtl.ket) + 1, LEN(retur_dtl.ket)) as ket
    //             from (
    //                 select  *
    //                 from    rtoko
    //                 where   " .(!empty($request->kd_dealer) ? "kd_dealer = '{$request->kd_dealer}' and " : "") . "CompanyId = '{$request->companyid}'
    //             ) as rtoko
    //             inner join rtoko_dtl on rtoko_dtl.no_retur = rtoko.no_retur and
    //                                     rtoko_dtl.CompanyId = rtoko.CompanyId
    //             inner join (
    //                 select
    //                     no_dokumen,
    //                     kd_part,
    //                     sum(qty) as qty,
    //                     tgl_klaim,
    //                     tgl_pakai
    //                 from
    //                 klaim_dtl
    //                 where	companyid = '{$request->companyid}' and
    //                         sts_klaim = 1  and
    //                         tgl_klaim between '{$request->tanggal[0]}' and '{$request->tanggal[1]}'
    //                 group by
    //                     no_dokumen,
    //                     kd_part,
    //                     tgl_klaim,
    //                     tgl_pakai
    //             ) klaim_dtl on  rtoko_dtl.no_klaim = klaim_dtl.no_dokumen and
    //                             rtoko_dtl.kd_part = klaim_dtl.kd_part
    //             inner join retur_dtl on retur_dtl.no_klaim = rtoko_dtl.no_retur and
    //                                     retur_dtl.kd_part = rtoko_dtl.kd_part and
    //                                     retur_dtl.CompanyId = rtoko_dtl.CompanyId
    //             inner join dealer on dealer.kd_dealer = rtoko.kd_dealer
    //         ) as a
    //         group by
    //             no_retur,
    //             kd_dealer,
    //             nm_dealer,
    //             kd_part,
    //             tgl_pakai,
    //             tgl_klaim,
    //             pemakaian,
    //             ket
    //         ";
    //     $data = DB::table(DB::raw("($sql) as b"))
    //         ->selectRaw("
    //             kd_dealer,
    //             nm_dealer,
    //             kd_part,
    //             qty_klaim,
    //             tgl_pakai,
    //             tgl_klaim,
    //             pemakaian,
    //             ket
    //         ")
    //         ->orderBy("kd_dealer", "asc")
    //         ->get();

    //     return Response()->json([
    //         'status' => 1,
    //         'message' => 'success',
    //         'data' => $data
    //     ], 200);

    //     } catch (\Exception $e) {
    //         return Response()->json([
    //             'status'    => 2,
    //             'message'   => 'Maaf, terjadi kesalahan. Silahkan coba lagi',
    //             'data'      => ''
    //         ], 200);
    //     }
    // }
}
