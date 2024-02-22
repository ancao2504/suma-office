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
                    IIF(klaim.pc = '0', dealer.nm_dealer, cabang.nm_cabang) as nm_dealer,
                    [klaim].[kd_sales],
                    [klaim].[kd_part],
                    klaim.qty_klaim,
                    ISNULL(jwb.qty_jwb,0) as qty_jwb,
                    klaim.tgl_klaim,
                    klaim.tgl_pakai,
                    klaim.pemakaian,
                    CASE WHEN retur.ket is not null THEN SUBSTRING(retur.ket, CHARINDEX('|', retur.ket) + 1, LEN(retur.ket) - CHARINDEX('|', retur.ket)) WHEN rtoko.ket is not null THEN rtoko.ket WHEN klaim.keterangan is not null THEN klaim.keterangan ELSE null END AS keterangan,
                    CASE WHEN klaim.sts_stock = 1 THEN 'Ganti Barang' WHEN klaim.sts_stock = 2 THEN 'Stock 0' WHEN klaim.sts_stock = 3 THEN 'Retur' ELSE null END AS sts_stock,
                    CASE WHEN klaim.sts_min = 1 THEN 'IYA' ELSE 'TIDAK' END AS sts_min,
                    CASE WHEN klaim.sts_klaim = 1 THEN 'IYA' ELSE 'TIDAK' END AS sts_klaim,
                    CASE WHEN klaim.status_approve = 1 THEN 1 ELSE 0 END AS sts_approve,
                    CASE WHEN rtoko.status_end = 1 THEN 1 ELSE 0 END AS sts_selesai
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
                        [klaim].[status_approve]
                    from [klaim]
                    inner join [klaim_dtl] on [klaim_dtl].[no_dokumen] = [klaim].[no_dokumen] and [klaim_dtl].[companyid] = [klaim].[companyid]
                    where [klaim].[companyid] = '$request->companyid' and
                    CONVERT(DATE, klaim_dtl.tgl_klaim) between '{$request->tanggal[0]}' and '{$request->tanggal[1]}'";
                    if (!empty($request->kd_dealer)){
                        $sql .= " and [klaim].[kd_dealer] = '$request->kd_dealer'";
                    }
                    if (!empty($request->kd_sales)){
                        $sql .= " and [klaim].[kd_sales] = '$request->kd_sales'";
                    }
                    if ($request->kd_jenis == 1) {
                        $sql .= " and [klaim].[pc] = '0'";
                    } else if ($request->kd_jenis == 2) {
                        $sql .= " and [klaim].[pc] = '1'";
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
                        [klaim].[status_approve]
                ) klaim
                left join (
                    select kd_dealer, nm_dealer from dealer where CompanyId = '$request->companyid'
                ) dealer on dealer.kd_dealer = klaim.kd_dealer
                left join (
                    select kd_cabang, nm_cabang from cabang where CompanyId = '$request->companyid'
                ) cabang on cabang.kd_cabang = klaim.kd_dealer
                inner join (
                    select
                        no_faktur,
                        kd_part
                    from
                        fakt_dtl
                    where CompanyId = '$request->companyid'
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
                        [rtoko_dtl].[status_end],
                        [rtoko].[CompanyId]
                    from [rtoko]
                    inner join [rtoko_dtl] on [rtoko_dtl].[no_retur] = [rtoko].[no_retur] and [rtoko_dtl].[CompanyId] = [rtoko].[CompanyId]
                    where [rtoko].[companyid] = '$request->companyid' and rtoko_dtl.no_klaim is not null
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
                    where [retur].[CompanyId] = '$request->companyid'
                ) as [retur] on [retur].[no_klaim] = [rtoko].[no_retur] and [retur].[kd_part] = [klaim].[kd_part]
                left join (
                    select	no_klaim,
                            kd_part,
                            no_retur,
                            sum(qty_jwb) as qty_jwb
                    from jwb_claim
                    where [jwb_claim].[CompanyId] = '$request->companyid' and jwb_claim.sts_end = '1'
                    group by	no_klaim,
                                kd_part,
                                no_retur
                ) as  jwb on rtoko.no_retur = jwb.no_klaim and klaim.kd_part = jwb.kd_part
            ";

            $data = DB::table(DB::raw("($sql) as a"))
            ->selectRaw(
                "
                    no_faktur,
                    no_retur,
                    no_klaim,
                    no_rtoko,
                    kd_dealer,
                    nm_dealer,
                    kd_sales,
                    kd_part,
                    qty_klaim,
                    qty_jwb,
                    tgl_klaim,
                    tgl_pakai,
                    pemakaian,
                    keterangan,
                    sts_stock,
                    sts_min,
                    sts_klaim,
                    sts_approve,
                    sts_selesai
                "
            )
            ->orderBy('kd_dealer', 'asc')
            ->orderBy('kd_sales', 'asc')
            ->orderBy('kd_part', 'asc')
            ->orderBy('tgl_klaim', 'asc')
            ->paginate($request->per_page);

            $dataPS = DB::table('jwb_claim')
            ->select("no_ps", "no_klaim", "kd_part")
            ->whereIn('no_klaim', collect($data->items())->pluck('no_rtoko')->unique()->values()->toArray())
            ->where('sts_end', 1)
            ->where('CompanyId', $request->companyid)
            ->groupBy('no_ps', 'no_klaim', 'kd_part')
            ->get();

            foreach($data->items() as $key => $value){
                $value->no_ps = $dataPS->where('no_klaim', $value->no_rtoko)->where('kd_part', $value->kd_part)->pluck('no_ps')->unique()->values()->toArray();
            }

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

            $sql = "
                select
                    klaim.no_faktur,
                    [retur].[no_retur],
                    klaim.no_dokumen as no_klaim,
                    rtoko.no_retur as no_rtoko,
                    klaim.kd_dealer,
                    IIF(klaim.pc = '0', dealer.nm_dealer, cabang.nm_cabang) as nm_dealer,
                    [klaim].[kd_sales],
                    [klaim].[kd_part],
                    klaim.qty_klaim,
                    ISNULL(jwb.qty_jwb,0) as qty_jwb,
                    klaim.tgl_klaim,
                    klaim.tgl_pakai,
                    klaim.pemakaian,
                    CASE WHEN retur.ket is not null THEN SUBSTRING(retur.ket, CHARINDEX('|', retur.ket) + 1, LEN(retur.ket) - CHARINDEX('|', retur.ket)) WHEN rtoko.ket is not null THEN rtoko.ket WHEN klaim.keterangan is not null THEN klaim.keterangan ELSE null END AS keterangan,
                    CASE WHEN klaim.sts_stock = 1 THEN 'Ganti Barang' WHEN klaim.sts_stock = 2 THEN 'Stock 0' WHEN klaim.sts_stock = 3 THEN 'Retur' ELSE null END AS sts_stock,
                    CASE WHEN klaim.sts_min = 1 THEN 'IYA' ELSE 'TIDAK' END AS sts_min,
                    CASE WHEN klaim.sts_klaim = 1 THEN 'IYA' ELSE 'TIDAK' END AS sts_klaim,
                    CASE WHEN klaim.status_approve = 1 THEN 1 ELSE 0 END AS sts_approve,
                    CASE WHEN rtoko.status_end = 1 THEN 1 ELSE 0 END AS sts_selesai
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
                        [klaim].[status_approve]
                    from [klaim]
                    inner join [klaim_dtl] on [klaim_dtl].[no_dokumen] = [klaim].[no_dokumen] and [klaim_dtl].[companyid] = [klaim].[companyid]
                    where [klaim].[companyid] = '$request->companyid' and
                    CONVERT(DATE, klaim_dtl.tgl_klaim) between '{$request->tanggal[0]}' and '{$request->tanggal[1]}'";
                    if (!empty($request->kd_dealer)){
                        $sql .= " and [klaim].[kd_dealer] = '$request->kd_dealer'";
                    }
                    if (!empty($request->kd_sales)){
                        $sql .= " and [klaim].[kd_sales] = '$request->kd_sales'";
                    }
                    if ($request->kd_jenis == 1) {
                        $sql .= " and [klaim].[pc] = '0'";
                    } else if ($request->kd_jenis == 2) {
                        $sql .= " and [klaim].[pc] = '1'";
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
                        [klaim].[status_approve]
                ) klaim
                left join (
                    select kd_dealer, nm_dealer from dealer where CompanyId = '$request->companyid'
                ) dealer on dealer.kd_dealer = klaim.kd_dealer
                left join (
                    select kd_cabang, nm_cabang from cabang where CompanyId = '$request->companyid'
                ) cabang on cabang.kd_cabang = klaim.kd_dealer
                inner join (
                    select
                        no_faktur,
                        kd_part
                    from
                        fakt_dtl
                    where CompanyId = '$request->companyid'
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
                        [rtoko_dtl].[status_end],
                        [rtoko].[CompanyId]
                    from [rtoko]
                    inner join [rtoko_dtl] on [rtoko_dtl].[no_retur] = [rtoko].[no_retur] and [rtoko_dtl].[CompanyId] = [rtoko].[CompanyId]
                    where [rtoko].[companyid] = '$request->companyid' and rtoko_dtl.no_klaim is not null
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
                    where [retur].[CompanyId] = '$request->companyid'
                ) as [retur] on [retur].[no_klaim] = [rtoko].[no_retur] and [retur].[kd_part] = [klaim].[kd_part]
                left join (
                    select	no_klaim,
                            kd_part,
                            no_retur,
                            sum(qty_jwb) as qty_jwb
                    from jwb_claim
                    where [jwb_claim].[CompanyId] = '$request->companyid' and jwb_claim.sts_end = '1'
                    group by	no_klaim,
                                kd_part,
                                no_retur
                ) as  jwb on rtoko.no_retur = jwb.no_klaim and klaim.kd_part = jwb.kd_part
            ";

            $data = DB::table(DB::raw("($sql) as a"))
            ->selectRaw(
                "
                    no_faktur,
                    no_retur,
                    no_klaim,
                    no_rtoko,
                    kd_dealer,
                    nm_dealer,
                    kd_sales,
                    kd_part,
                    qty_klaim,
                    qty_jwb,
                    tgl_klaim,
                    tgl_pakai,
                    pemakaian,
                    keterangan,
                    sts_stock,
                    sts_min,
                    sts_klaim,
                    sts_approve,
                    sts_selesai
                "
            )
            ->orderBy('kd_dealer', 'asc')
            ->orderBy('kd_sales', 'asc')
            ->orderBy('kd_part', 'asc')
            ->orderBy('tgl_klaim', 'asc')
            ->get();

            $dataPS = DB::table('jwb_claim')
            ->select("no_ps", "no_klaim", "kd_part")
            ->whereIn('no_klaim', collect($data)->pluck('no_rtoko')->unique()->values()->toArray())
            ->where('sts_end', 1)
            ->where('CompanyId', $request->companyid)
            ->groupBy('no_ps', 'no_klaim', 'kd_part')
            ->get();

            foreach($data as $key => $value){
                $value->no_ps = ltrim(implode(',', $dataPS->where('no_klaim', $value->no_rtoko)->where('kd_part', $value->kd_part)->pluck('no_ps')->unique()->values()->toArray()));
            }

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
