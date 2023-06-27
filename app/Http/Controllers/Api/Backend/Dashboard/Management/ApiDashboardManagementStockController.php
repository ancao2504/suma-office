<?php

namespace App\Http\Controllers\Api\Backend\Dashboard\Management;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;


class ApiDashboardManagementStockController extends Controller
{
    public function dashboardStockByProduct(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'year'      => 'required|string',
                'month'     => 'required|string',
                'fields'    => 'required|string',
                'user_id'   => 'required|string',
                'role_id'   => 'required|string',
                'companyid' => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih bulan dan tahun terlebih dahulu");
            }

            $tahun = (int)date('Y');
            $bulan = (int)date('m');

            if(!empty($request->get('year'))) {
                $tahun = (int)$request->get('year');
            }
            if(!empty($request->get('month'))) {
                $bulan = (int)$request->get('month');
            }

            $status_stockLalu = 0;

            $tanggal_clossing = date('Y-m-d');
            $sql = DB::table('stsclose')->lock('with (nolock)')
                    ->select('companyid','close_mkr')
                    ->where('companyid', $request->get('companyid'))
                    ->first();

            if(empty($sql->companyid)) {
                return Response::responseWarning("Data clossing company masih belum disetting");
            } else {
                $tanggal_clossing = trim($sql->close_mkr);
            }

            $tanggal_dipilih = $tahun.'-'. substr('0'.$bulan, -2).'-'.'01';


            if(date($tanggal_clossing) > date($tanggal_dipilih)) {
                $status_stockLalu = 1;
            }

            $part_tahunan = 0;
            $stlokasi_tahunan = 0;
            $stock_tahunan = 0;
            $hpp_tahunan = 0;
            $oo_tahunan = 0;

            if($status_stockLalu == 1) {
                $part_tahunan = 'part'.(int)$tahun;
                $stlokasi_tahunan = 'stlokasi'.(int)$tahun;
                $stock_tahunan = 'stc'.(int)$bulan;
                $hpp_tahunan = 'hpp'.(int)$bulan;
                $oo_tahunan = 'oo'.(int)$bulan;
            }

            $company_pusat = '';

            $sql = DB::table('company')->lock('with (nolock)')
                    ->select('companyid')
                    ->where('inisial', 1)
                    ->first();

            if(empty($sql->companyid)) {
                return Response::responseWarning("Inisial company pusat masih belum disetting, hubungi IT programmer");
            } else {
                $company_pusat = strtoupper(trim($sql->companyid));
            }

            $sql = "select	isnull(produk.kd_produk, '') as kode_produk,
                            isnull(produk.nama, '') as nama_produk, isnull(produk.level, '') as level, isnull(produk.kd_mkr, '') as kode_mkr,
                            isnull(produk.keterangan_level, '') as keterangan_level, isnull(produk.nourut, 0) as nomor_urut,
                            isnull(packing.packing_pcs, 0) as stock_packing, isnull(packing.packing_amount, 0) as amount_packing,
                            isnull(on_order.on_order_pcs, 0) as stock_on_order, isnull(on_order_amount, 0) as amount_on_order,
                            isnull(stlokasi.stock_qty_pusat, 0) as stock_qty_pusat, isnull(stlokasi.stock_qty_pc, 0) as stock_qty_pc, isnull(stlokasi.stock_qty_online, 0) as stock_qty_online,
                            isnull(stlokasi.stock_amount_pusat, 0) as stock_amount_pusat, isnull(stlokasi.stock_amount_pc, 0) as stock_amount_pc, isnull(stlokasi.stock_amount_online, 0) as stock_amount_online,
                            isnull(stlokasi.fast_moving_qty_pusat, 0) as fast_moving_qty_pusat, isnull(stlokasi.fast_moving_qty_pc, 0) as fast_moving_qty_pc, isnull(stlokasi.fast_moving_qty_online, 0) as fast_moving_qty_online,
                            isnull(stlokasi.fast_moving_amount_pusat, 0) as fast_moving_amount_pusat, isnull(stlokasi.fast_moving_amount_pc, 0) as fast_moving_amount_pc, isnull(stlokasi.fast_moving_amount_online, 0) as fast_moving_amount_online,
                            isnull(stlokasi.slow_moving_qty_pusat, 0) as slow_moving_qty_pusat, isnull(stlokasi.slow_moving_qty_pc, 0) as slow_moving_qty_pc, isnull(stlokasi.slow_moving_qty_online, 0) as slow_moving_qty_online,
                            isnull(stlokasi.slow_moving_amount_pusat, 0) as slow_moving_amount_pusat, isnull(stlokasi.slow_moving_amount_pc, 0) as slow_moving_amount_pc, isnull(stlokasi.slow_moving_amount_online, 0) as slow_moving_amount_online,
                            isnull(stlokasi.current_qty_pusat, 0) as current_qty_pusat, isnull(stlokasi.current_qty_pc, 0) as current_qty_pc, isnull(stlokasi.current_qty_online, 0) as current_qty_online,
                            isnull(stlokasi.current_amount_pusat, 0) as current_amount_pusat, isnull(stlokasi.current_amount_pc, 0) as current_amount_pc, isnull(stlokasi.current_amount_online, 0) as current_amount_online,
                            isnull(stlokasi.non_current_qty_pusat, 0) as non_current_qty_pusat, isnull(stlokasi.non_current_qty_pc, 0) as non_current_qty_pc, isnull(stlokasi.non_current_qty_online, 0) as non_current_qty_online,
                            isnull(stlokasi.non_current_amount_pusat, 0) as non_current_amount_pusat, isnull(stlokasi.non_current_amount_pc, 0) as non_current_amount_pc, isnull(stlokasi.non_current_amount_online, 0) as non_current_amount_online,
                            isnull(stlokasi.others_qty_pusat, 0) as others_qty_pusat, isnull(stlokasi.others_qty_pc, 0) as others_qty_pc, isnull(stlokasi.others_qty_online, 0) as others_qty_online,
                            isnull(stlokasi.others_amount_pusat, 0) as others_amount_pusat, isnull(stlokasi.others_amount_pc, 0) as others_amount_pc, isnull(stlokasi.others_amount_online, 0) as others_amount_online
                    from
                    (
                        select	'report' as report, produk.kd_produk, produk.nama, produk.kd_mkr, produk.nourut,
                                case
                                    when produk.level='AHM' and produk.kd_mkr='G' then 'KSJS'
                                    when produk.level='MPM' and produk.kd_mkr='G' then 'MPM'
                                    when produk.level='AHM' and produk.kd_mkr='I' then 'LSJS'
                                    when produk.level='AHM' and produk.kd_mkr='J' then 'NPM'
                                else
                                    produk.kd_mkr
                                end As 'level',
                                case
                                    when produk.level='AHM' and produk.kd_mkr='G' then 'KSJS'
                                    when produk.level='MPM' and produk.kd_mkr='G' then 'MPM'
                                    when produk.level='AHM' and produk.kd_mkr='I' then 'TUBE'
                                    when produk.level='AHM' and produk.kd_mkr='J' then 'OLI'
                                else
                                    produk.kd_mkr
                                end as 'keterangan_level'
                        from	produk
                        where   produk.kd_produk is not null";

            if(!empty($request->get('produk')) && $request->get('produk') != '') {
                $sql .= " and produk.kd_produk='".trim($request->get('produk'))."'";
            }

            if(strtoupper(trim($request->get('level'))) == 'HANDLE') {
                $sql .= " and produk.level='AHM' and produk.kd_mkr='G'";
            } elseif(strtoupper(trim($request->get('level'))) == 'NON_HANDLE') {
                $sql .= " and produk.level='MPM' and produk.kd_mkr='G'";
            } elseif(strtoupper(trim($request->get('level'))) == 'TUBE') {
                $sql .= " and produk.level='AHM' and produk.kd_mkr='I'";
            } elseif(strtoupper(trim($request->get('level'))) == 'OLI') {
                $sql .= " and produk.level='AHM' and produk.kd_mkr='J'";
            }

            $sql .= " )	produk
                    left join
                    (";

            if($status_stockLalu == 0) {
                $sql .= " select	iif(isnull(sub.kd_produk, '')='', 'LLL', sub.kd_produk) as kd_produk,
                                    sum(iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0), 0)) as stock_qty_pusat,
                                    sum(iif(isnull(part.fs, '')='F', iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as fast_moving_qty_pusat,
                                    sum(iif(isnull(part.fs, '') <> 'F', iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as slow_moving_qty_pusat,
                                    sum(iif(isnull(part.cno, '')='C', iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as current_qty_pusat,
                                    sum(iif(isnull(part.cno, '')='N', iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as non_current_qty_pusat,
                                    sum(iif(isnull(part.cno, '')='O', iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as others_qty_pusat,
                                    sum(iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(part.hrg_pokok, 0), 0)) as stock_amount_pusat,
                                    sum(iif(isnull(part.fs, '')='F', iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(part.hrg_pokok, 0), 0), 0)) as fast_moving_amount_pusat,
                                    sum(iif(isnull(part.fs, '') <> 'F', iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(part.hrg_pokok, 0), 0), 0)) as slow_moving_amount_pusat,
                                    sum(iif(isnull(part.cno, '')='C', iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(part.hrg_pokok, 0), 0), 0)) as current_amount_pusat,
                                    sum(iif(isnull(part.cno, '')='N', iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(part.hrg_pokok, 0), 0), 0)) as non_current_amount_pusat,
                                    sum(iif(isnull(part.cno, '')='O', iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(part.hrg_pokok, 0), 0), 0)) as others_amount_pusat,
                                    sum(iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0), 0)) as stock_qty_pc,
                                    sum(iif(isnull(part.fs, '')='F', iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as fast_moving_qty_pc,
                                    sum(iif(isnull(part.fs, '') <> 'F', iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as slow_moving_qty_pc,
                                    sum(iif(isnull(part.cno, '')='C', iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as current_qty_pc,
                                    sum(iif(isnull(part.cno, '')='N', iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as non_current_qty_pc,
                                    sum(iif(isnull(part.cno, '')='O', iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as others_qty_pc,
                                    sum(iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(part.hrg_pokok, 0), 0)) as stock_amount_pc,
                                    sum(iif(isnull(part.fs, '')='F', iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(part.hrg_pokok, 0), 0), 0)) as fast_moving_amount_pc,
                                    sum(iif(isnull(part.fs, '') <> 'F', iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(part.hrg_pokok, 0), 0), 0)) as slow_moving_amount_pc,
                                    sum(iif(isnull(part.cno, '')='C', iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(part.hrg_pokok, 0), 0), 0)) as current_amount_pc,
                                    sum(iif(isnull(part.cno, '')='N', iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(part.hrg_pokok, 0), 0), 0)) as non_current_amount_pc,
                                    sum(iif(isnull(part.cno, '')='O', iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(part.hrg_pokok, 0), 0), 0)) as others_amount_pc,
                                    sum(iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0), 0)) as stock_qty_online,
                                    sum(iif(isnull(part.fs, '')='F', iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as fast_moving_qty_online,
                                    sum(iif(isnull(part.fs, '') <> 'F', iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as slow_moving_qty_online,
                                    sum(iif(isnull(part.cno, '')='C', iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as current_qty_online,
                                    sum(iif(isnull(part.cno, '')='N', iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as non_current_qty_online,
                                    sum(iif(isnull(part.cno, '')='O', iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as others_qty_online,
                                    sum(iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(part.hrg_pokok, 0), 0)) as stock_amount_online,
                                    sum(iif(isnull(part.fs, '')='F', iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(part.hrg_pokok, 0), 0), 0)) as fast_moving_amount_online,
                                    sum(iif(isnull(part.fs, '') <> 'F', iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(part.hrg_pokok, 0), 0), 0)) as slow_moving_amount_online,
                                    sum(iif(isnull(part.cno, '')='C', iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(part.hrg_pokok, 0), 0), 0)) as current_amount_online,
                                    sum(iif(isnull(part.cno, '')='N', iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(part.hrg_pokok, 0), 0), 0)) as non_current_amount_online,
                                    sum(iif(isnull(part.cno, '')='O', iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(part.hrg_pokok, 0), 0), 0)) as others_amount_online
                            from
                            (
                                select	company.companyid,
                                        iif(isnull(company.inisial, 0)=1,
                                            iif(isnull(company.kd_faktur, '')=isnull(lokasi.kd_faktur, ''), 1, 0), 0) as status_pusat,
                                        iif(isnull(company.inisial, 0)=1,
                                            iif(isnull(company.kd_faktur, '') <> isnull(lokasi.kd_faktur, ''), 1, 0), 0) as status_online,
                                        iif(isnull(company.inisial, 0)=0, 1, 0) as status_pc,
                                        stlokasi.kd_part, sum(isnull(stlokasi.jumlah, 0)) as jumlah
                                from	company with (nolock)
                                            left join stlokasi with (nolock) on company.companyid=stlokasi.companyid
                                            left join lokasi with (nolock) on stlokasi.kd_lokasi=lokasi.kd_lokasi and
                                                        stlokasi.companyid=lokasi.companyid
                                where	isnull(stlokasi.jumlah, 0) > 0
                                group by company.companyid,
                                        iif(isnull(company.inisial, 0)=1, iif(isnull(company.kd_faktur, '')=isnull(lokasi.kd_faktur, ''), 1, 0), 0),
                                        iif(isnull(company.inisial, 0)=1, iif(isnull(company.kd_faktur, '') <> isnull(lokasi.kd_faktur, ''), 1, 0), 0),
                                        iif(isnull(company.inisial, 0)=0, 1, 0),
                                        stlokasi.kd_part
                            )	stlokasi
                                    left join part with (nolock) on stlokasi.kd_part=part.kd_part and '".$company_pusat."'=part.companyid
                                    left join sub with (nolock) on part.kd_sub=sub.kd_sub
                                    left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                            where   stlokasi.companyid is not null";

                if(!empty($request->get('produk')) && $request->get('produk') != '') {
                    $sql .= " and produk.kd_produk='".$request->get('produk')."'";
                }

                if(strtoupper(trim($request->get('level'))) == 'HANDLE') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='G'";
                } elseif(strtoupper(trim($request->get('level'))) == 'NON_HANDLE') {
                    $sql .= " and produk.level='MPM' and produk.kd_mkr='G'";
                } elseif(strtoupper(trim($request->get('level'))) == 'TUBE') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='I'";
                } elseif(strtoupper(trim($request->get('level'))) == 'OLI') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='J'";
                }

                $sql .= " group by iif(isnull(sub.kd_produk, '')='', 'LLL', sub.kd_produk)";
            } else {
                $sql .= " select	iif(isnull(sub.kd_produk, '')='', 'LLL', sub.kd_produk) as kd_produk,
                                    sum(iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0), 0)) as stock_qty_pusat,
                                    sum(iif(isnull(part.fs, '')='F', iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as fast_moving_qty_pusat,
                                    sum(iif(isnull(part.fs, '') <> 'F', iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as slow_moving_qty_pusat,
                                    sum(iif(isnull(part.cno, '')='C', iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as current_qty_pusat,
                                    sum(iif(isnull(part.cno, '')='N', iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as non_current_qty_pusat,
                                    sum(iif(isnull(part.cno, '')='O', iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as others_qty_pusat,
                                    sum(iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(".$part_tahunan.".".$hpp_tahunan.", 0), 0)) as stock_amount_pusat,
                                    sum(iif(isnull(part.fs, '')='F', iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(".$part_tahunan.".".$hpp_tahunan.", 0), 0), 0)) as fast_moving_amount_pusat,
                                    sum(iif(isnull(part.fs, '') <> 'F', iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(".$part_tahunan.".".$hpp_tahunan.", 0), 0), 0)) as slow_moving_amount_pusat,
                                    sum(iif(isnull(part.cno, '')='C', iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(".$part_tahunan.".".$hpp_tahunan.", 0), 0), 0)) as current_amount_pusat,
                                    sum(iif(isnull(part.cno, '')='N', iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(".$part_tahunan.".".$hpp_tahunan.", 0), 0), 0)) as non_current_amount_pusat,
                                    sum(iif(isnull(part.cno, '')='O', iif(isnull(stlokasi.status_pusat, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(".$part_tahunan.".".$hpp_tahunan.", 0), 0), 0)) as others_amount_pusat,
                                    sum(iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0), 0)) as stock_qty_pc,
                                    sum(iif(isnull(part.fs, '')='F', iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as fast_moving_qty_pc,
                                    sum(iif(isnull(part.fs, '') <> 'F', iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as slow_moving_qty_pc,
                                    sum(iif(isnull(part.cno, '')='C', iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as current_qty_pc,
                                    sum(iif(isnull(part.cno, '')='N', iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as non_current_qty_pc,
                                    sum(iif(isnull(part.cno, '')='O', iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as others_qty_pc,
                                    sum(iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(".$part_tahunan.".".$hpp_tahunan.", 0), 0)) as stock_amount_pc,
                                    sum(iif(isnull(part.fs, '')='F', iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(".$part_tahunan.".".$hpp_tahunan.", 0), 0), 0)) as fast_moving_amount_pc,
                                    sum(iif(isnull(part.fs, '') <> 'F', iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(".$part_tahunan.".".$hpp_tahunan.", 0), 0), 0)) as slow_moving_amount_pc,
                                    sum(iif(isnull(part.cno, '')='C', iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(".$part_tahunan.".".$hpp_tahunan.", 0), 0), 0)) as current_amount_pc,
                                    sum(iif(isnull(part.cno, '')='N', iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(".$part_tahunan.".".$hpp_tahunan.", 0), 0), 0)) as non_current_amount_pc,
                                    sum(iif(isnull(part.cno, '')='O', iif(isnull(stlokasi.status_pc, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(".$part_tahunan.".".$hpp_tahunan.", 0), 0), 0)) as others_amount_pc,
                                    sum(iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0), 0)) as stock_qty_online,
                                    sum(iif(isnull(part.fs, '')='F', iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as fast_moving_qty_online,
                                    sum(iif(isnull(part.fs, '') <> 'F', iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as slow_moving_qty_online,
                                    sum(iif(isnull(part.cno, '')='C', iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as current_qty_online,
                                    sum(iif(isnull(part.cno, '')='N', iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as non_current_qty_online,
                                    sum(iif(isnull(part.cno, '')='O', iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0), 0), 0)) as others_qty_online,
                                    sum(iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(".$part_tahunan.".".$hpp_tahunan.", 0), 0)) as stock_amount_online,
                                    sum(iif(isnull(part.fs, '')='F', iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(".$part_tahunan.".".$hpp_tahunan.", 0), 0), 0)) as fast_moving_amount_online,
                                    sum(iif(isnull(part.fs, '') <> 'F', iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(".$part_tahunan.".".$hpp_tahunan.", 0), 0), 0)) as slow_moving_amount_online,
                                    sum(iif(isnull(part.cno, '')='C', iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(".$part_tahunan.".".$hpp_tahunan.", 0), 0), 0)) as current_amount_online,
                                    sum(iif(isnull(part.cno, '')='N', iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(".$part_tahunan.".".$hpp_tahunan.", 0), 0), 0)) as non_current_amount_online,
                                    sum(iif(isnull(part.cno, '')='O', iif(isnull(stlokasi.status_online, 0)=1, isnull(stlokasi.jumlah, 0) * isnull(".$part_tahunan.".".$hpp_tahunan.", 0), 0), 0)) as others_amount_online
                            from
                            (
                                select	company.companyid,
                                        iif(isnull(company.inisial, 0)=1,
                                            iif(isnull(company.kd_faktur, '')=isnull(lokasi.kd_faktur, ''), 1, 0), 0) as status_pusat,
                                        iif(isnull(company.inisial, 0)=1,
                                            iif(isnull(company.kd_faktur, '') <> isnull(lokasi.kd_faktur, ''), 1, 0), 0) as status_online,
                                        iif(isnull(company.inisial, 0)=0, 1, 0) as status_pc,
                                        ".$stlokasi_tahunan.".kd_part, sum(isnull(".$stlokasi_tahunan.".".$stock_tahunan.", 0)) as jumlah
                                from	company with (nolock)
                                            left join ".$stlokasi_tahunan." with (nolock) on company.companyid=".$stlokasi_tahunan.".companyid
                                            left join lokasi with (nolock) on ".$stlokasi_tahunan.".kd_lokasi=lokasi.kd_lokasi and
                                                                                ".$stlokasi_tahunan.".companyid=lokasi.companyid
                                where	isnull(".$stlokasi_tahunan.".".$stock_tahunan.", 0) > 0
                                group by company.companyid,
                                        iif(isnull(company.inisial, 0)=1, iif(isnull(company.kd_faktur, '')=isnull(lokasi.kd_faktur, ''), 1, 0), 0),
                                        iif(isnull(company.inisial, 0)=1, iif(isnull(company.kd_faktur, '') <> isnull(lokasi.kd_faktur, ''), 1, 0), 0),
                                        iif(isnull(company.inisial, 0)=0, 1, 0),
                                        ".$stlokasi_tahunan.".kd_part
                            )	stlokasi
                                    left join ".$part_tahunan." with (nolock) on stlokasi.kd_part=".$part_tahunan.".kd_part and stlokasi.companyid=".$part_tahunan.".companyid
                                    left join part with (nolock) on stlokasi.kd_part=part.kd_part and '".$company_pusat."'=part.companyid
                                    left join sub with (nolock) on part.kd_sub=sub.kd_sub
                                    left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                            where   stlokasi.companyid is not null";

                if(!empty($request->get('produk')) && $request->get('produk') != '') {
                    $sql .= " and produk.kd_produk='".$request->get('produk')."'";
                }

                if(strtoupper(trim($request->get('level'))) == 'HANDLE') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='G'";
                } elseif(strtoupper(trim($request->get('level'))) == 'NON_HANDLE') {
                    $sql .= " and produk.level='MPM' and produk.kd_mkr='G'";
                } elseif(strtoupper(trim($request->get('level'))) == 'TUBE') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='I'";
                } elseif(strtoupper(trim($request->get('level'))) == 'OLI') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='J'";
                }

                $sql .= " group by iif(isnull(sub.kd_produk, '')='', 'LLL', sub.kd_produk)";
            }

            $sql .= " )	stlokasi on produk.kd_produk=stlokasi.kd_produk
                    left join
                    (
                        select	produk.kd_produk, sum(isnull(pack_dtl.harga, 0) * isnull(pack_dtl.jmlterima, 0)) as packing_amount,
                                sum(isnull(pack_dtl.jmlterima, 0)) as packing_pcs
                        from
                        (
                            select	packing.companyid, packing.no_ps
                            from	packing with (nolock)
                            where	packing.companyid='".$company_pusat."' and
                                    year(packing.tgl_terima)='".$tahun."' and month(packing.tgl_terima)='".$bulan."'
                        )	packing
                                left join pack_dtl with (nolock) on packing.no_ps=pack_dtl.no_ps and
                                            packing.companyid=pack_dtl.companyid
                                left join part on pack_dtl.kd_part=part.kd_part and packing.companyid=part.companyid
                                left join sub on part.kd_sub=sub.kd_sub
                                left join produk on sub.kd_produk=produk.kd_produk
                        where   packing.companyid is not null";

            if(!empty($request->get('produk')) && $request->get('produk') != '') {
                $sql .= " and produk.kd_produk='".$request->get('produk')."'";
            }

            if(strtoupper(trim($request->get('level'))) == 'HANDLE') {
                $sql .= " and produk.level='AHM' and produk.kd_mkr='G'";
            } elseif(strtoupper(trim($request->get('level'))) == 'NON_HANDLE') {
                $sql .= " and produk.level='MPM' and produk.kd_mkr='G'";
            } elseif(strtoupper(trim($request->get('level'))) == 'TUBE') {
                $sql .= " and produk.level='AHM' and produk.kd_mkr='I'";
            } elseif(strtoupper(trim($request->get('level'))) == 'OLI') {
                $sql .= " and produk.level='AHM' and produk.kd_mkr='J'";
            }

            $sql .= " group by produk.kd_produk
                    )   packing on produk.kd_produk=packing.kd_produk
                    left join
                    (";

            if($status_stockLalu == 0) {
                $sql .= " select    iif(isnull(sub.kd_produk, '')='', 'LLL', sub.kd_produk) as kd_produk,
                                    sum(isnull(part.on_order_pcs, 0)) as on_order_pcs,
                                    sum(isnull(part.on_order_amount, 0)) as on_order_amount
                        from
                        (
                            select	part.companyid, part.kd_part, iif(isnull(part.kd_sub, '')='', 'LLL', part.kd_sub) as 'kd_sub', part.hrg_pokok, part.stock as 'stock',
                                    isnull(part.stock, 0) * isnull(part.hrg_pokok, 0) As 'amount',
                                    iif((isnull(part.on_order, 0) + isnull(part.on_order_l, 0)) - isnull(part.kum_mb, 0) < 0, 0,
                                        (isnull(part.on_order, 0) + isnull(part.on_order_l, 0)) - isnull(part.kum_mb, 0)) as 'on_order_pcs',
                                    iif((isnull(part.on_order, 0) + isnull(part.on_order_l, 0)) - isnull(part.kum_mb, 0) < 0, 0,
                                        (isnull(part.on_order, 0) + isnull(part.on_order_l, 0)) - isnull(part.kum_mb, 0)) * isnull(part.hrg_pokok, 0) as 'on_order_amount'
                            from	part
                            where	part.companyid='".$company_pusat."'
                        )	part
                                left join sub on part.kd_sub=sub.kd_sub
                                left join produk on sub.kd_produk=produk.kd_produk
                        where   part.companyid is not null";

                if(!empty($request->get('produk')) && $request->get('produk') != '') {
                    $sql .= " and produk.kd_produk='".$request->get('produk')."'";
                }

                if(strtoupper(trim($request->get('level'))) == 'HANDLE') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='G'";
                } elseif(strtoupper(trim($request->get('level'))) == 'NON_HANDLE') {
                    $sql .= " and produk.level='MPM' and produk.kd_mkr='G'";
                } elseif(strtoupper(trim($request->get('level'))) == 'TUBE') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='I'";
                } elseif(strtoupper(trim($request->get('level'))) == 'OLI') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='J'";
                }

                $sql .= " group by iif(isnull(sub.kd_produk, '')='', 'LLL', sub.kd_produk)";
            } else {
                $sql .= " select	iif(isnull(sub.kd_produk, '')='', 'LLL', sub.kd_produk) as kd_produk,
                                sum(isnull(stlokasi.on_order, 0)) as on_order_pcs,
                                sum(isnull(stlokasi.on_order, 0) * isnull(".$part_tahunan.".".$hpp_tahunan.", 0)) as on_order_amount
                        from
                        (
                            select	".$stlokasi_tahunan.".companyid, ".$stlokasi_tahunan.".kd_part,
                                    isnull(".$stlokasi_tahunan.".".$oo_tahunan.", 0) as on_order
                            from	".$stlokasi_tahunan."
                            where	".$stlokasi_tahunan.".companyid='".$company_pusat."'
                        )	stlokasi
                                left join ".$part_tahunan." on stlokasi.kd_part=".$part_tahunan.".kd_part and '".$company_pusat."'=".$part_tahunan.".companyid
                                left join part on stlokasi.kd_part=part.kd_part and '".$company_pusat."'=part.companyid
                                left join sub on part.kd_sub=sub.kd_sub
                                left join produk on sub.kd_produk=produk.kd_produk
                        where   stlokasi.companyid is not null";

                if(!empty($request->get('produk')) && $request->get('produk') != '') {
                    $sql .= " and produk.kd_produk='".$request->get('produk')."'";
                }

                if(strtoupper(trim($request->get('level'))) == 'HANDLE') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='G'";
                } elseif(strtoupper(trim($request->get('level'))) == 'NON_HANDLE') {
                    $sql .= " and produk.level='MPM' and produk.kd_mkr='G'";
                } elseif(strtoupper(trim($request->get('level'))) == 'TUBE') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='I'";
                } elseif(strtoupper(trim($request->get('level'))) == 'OLI') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='J'";
                }

                $sql .= " group by iif(isnull(sub.kd_produk, '')='', 'LLL', sub.kd_produk)";
            }

            $sql .= " ) on_order on produk.kd_produk=on_order.kd_produk
                    order by produk.kd_mkr asc, produk.keterangan_level asc, produk.nourut asc";

            $result = DB::select($sql);

            $data_stock_by_product = [];
            $total_pusat = 0;
            $total_pc = 0;
            $total_online = 0;

            $total_fast_moving_pusat = 0;
            $total_fast_moving_pc = 0;
            $total_fast_moving_online = 0;

            $total_slow_moving_pusat = 0;
            $total_slow_moving_pc = 0;
            $total_slow_moving_online = 0;

            $total_current_pusat = 0;
            $total_current_pc = 0;
            $total_current_online = 0;

            $total_noncurrent_pusat = 0;
            $total_noncurrent_pc = 0;
            $total_noncurrent_online = 0;

            $total_others_pusat = 0;
            $total_others_pc = 0;
            $total_others_online = 0;

            $total_on_order = 0;
            $total_packing_sheet = 0;

            foreach($result as $data) {
                $pusat = 0;
                $pc = 0;
                $online = 0;

                if(strtoupper(trim($request->get('fields'))) == 'QUANTITY') {
                    $pusat = (double)$data->stock_qty_pusat;
                    $pc = (double)$data->stock_qty_pc;
                    $online = (double)$data->stock_qty_online;

                    $total_on_order = (double)$total_on_order + (double)$data->stock_on_order;
                    $total_packing_sheet = (double)$total_packing_sheet + (double)$data->stock_packing;

                    $total_pusat = (double)$total_pusat + (double)$data->stock_qty_pusat;
                    $total_pc = (double)$total_pc + (double)$data->stock_qty_pc;
                    $total_online = (double)$total_online + (double)$data->stock_qty_online;

                    $total_fast_moving_pusat = (double)$total_fast_moving_pusat + (double)$data->fast_moving_qty_pusat;
                    $total_fast_moving_pc = (double)$total_fast_moving_pc + (double)$data->fast_moving_qty_pc;
                    $total_fast_moving_online = (double)$total_fast_moving_online + (double)$data->fast_moving_qty_online;

                    $total_slow_moving_pusat = (double)$total_slow_moving_pusat + (double)$data->slow_moving_qty_pusat;
                    $total_slow_moving_pc = (double)$total_slow_moving_pc + (double)$data->slow_moving_qty_pc;
                    $total_slow_moving_online = (double)$total_slow_moving_online + (double)$data->slow_moving_qty_online;

                    $total_slow_moving_pusat = (double)$total_slow_moving_pusat + (double)$data->slow_moving_qty_pusat;
                    $total_slow_moving_pc = (double)$total_slow_moving_pc + (double)$data->slow_moving_qty_pc;
                    $total_slow_moving_online = (double)$total_slow_moving_online + (double)$data->slow_moving_qty_online;

                    $total_current_pusat = (double)$total_current_pusat + (double)$data->current_qty_pusat;
                    $total_current_pc = (double)$total_current_pc + (double)$data->current_qty_pc;
                    $total_current_online = (double)$total_current_online + (double)$data->current_qty_online;

                    $total_noncurrent_pusat = (double)$total_noncurrent_pusat + (double)$data->non_current_qty_pusat;
                    $total_noncurrent_pc = (double)$total_noncurrent_pc + (double)$data->non_current_qty_pc;
                    $total_noncurrent_online = (double)$total_noncurrent_online + (double)$data->non_current_qty_online;

                    $total_others_pusat = (double)$total_others_pusat + (double)$data->others_qty_pusat;
                    $total_others_pc = (double)$total_others_pc + (double)$data->others_qty_pc;
                    $total_others_online = (double)$total_others_online + (double)$data->others_qty_online;
                } else {
                    $pusat = (double)$data->stock_amount_pusat;
                    $pc = (double)$data->stock_amount_pc;
                    $online = (double)$data->stock_amount_online;

                    $total_on_order = (double)$total_on_order + (double)$data->amount_on_order;
                    $total_packing_sheet = (double)$total_packing_sheet + (double)$data->amount_packing;

                    $total_pusat = (double)$total_pusat + (double)$data->stock_amount_pusat;
                    $total_pc = (double)$total_pc + (double)$data->stock_amount_pc;
                    $total_online = (double)$total_online + (double)$data->stock_amount_online;

                    $total_fast_moving_pusat = (double)$total_fast_moving_pusat + (double)$data->fast_moving_amount_pusat;
                    $total_fast_moving_pc = (double)$total_fast_moving_pc + (double)$data->fast_moving_amount_pc;
                    $total_fast_moving_online = (double)$total_fast_moving_online + (double)$data->fast_moving_amount_online;

                    $total_slow_moving_pusat = (double)$total_slow_moving_pusat + (double)$data->slow_moving_amount_pusat;
                    $total_slow_moving_pc = (double)$total_slow_moving_pc + (double)$data->slow_moving_amount_pc;
                    $total_slow_moving_online = (double)$total_slow_moving_online + (double)$data->slow_moving_amount_online;

                    $total_slow_moving_pusat = (double)$total_slow_moving_pusat + (double)$data->slow_moving_amount_pusat;
                    $total_slow_moving_pc = (double)$total_slow_moving_pc + (double)$data->slow_moving_amount_pc;
                    $total_slow_moving_online = (double)$total_slow_moving_online + (double)$data->slow_moving_amount_online;

                    $total_current_pusat = (double)$total_current_pusat + (double)$data->current_amount_pusat;
                    $total_current_pc = (double)$total_current_pc + (double)$data->current_amount_pc;
                    $total_current_online = (double)$total_current_online + (double)$data->current_amount_online;

                    $total_noncurrent_pusat = (double)$total_noncurrent_pusat + (double)$data->non_current_amount_pusat;
                    $total_noncurrent_pc = (double)$total_noncurrent_pc + (double)$data->non_current_amount_pc;
                    $total_noncurrent_online = (double)$total_noncurrent_online + (double)$data->non_current_amount_online;

                    $total_others_pusat = (double)$total_others_pusat + (double)$data->others_amount_pusat;
                    $total_others_pc = (double)$total_others_pc + (double)$data->others_amount_pc;
                    $total_others_online = (double)$total_others_online + (double)$data->others_amount_online;
                }

                $data_stock_by_product[] = [
                    'produk'    => strtoupper(trim($data->kode_produk)),
                    'pusat'     => (double)$pusat,
                    'pc'        => (double)$pc,
                    'online'    => (double)$online,
                ];
            }
            $data_fast_moving = [
                'fs'        => 'Fast Moving',
                'pusat'     => (double)$total_fast_moving_pusat,
                'pc'        => (double)$total_fast_moving_pc,
                'online'    => (double)$total_fast_moving_online,
            ];
            $data_slow_moving = [
                'fs'        => 'Slow Moving',
                'pusat'     => (double)$total_slow_moving_pusat,
                'pc'        => (double)$total_slow_moving_pc,
                'online'    => (double)$total_slow_moving_online,
            ];
            $data_current = [
                'cno'       => 'Current',
                'pusat'     => (double)$total_current_pusat,
                'pc'        => (double)$total_current_pc,
                'online'    => (double)$total_current_online,
            ];
            $data_non_current = [
                'cno'       => 'Non Current',
                'pusat'     => (double)$total_noncurrent_pusat,
                'pc'        => (double)$total_noncurrent_pc,
                'online'    => (double)$total_noncurrent_online,
            ];
            $data_others = [
                'cno'       => 'Others',
                'pusat'     => (double)$total_others_pusat,
                'pc'        => (double)$total_others_pc,
                'online'    => (double)$total_others_online,
            ];
            $data_total_pusat = [
                'company'   => 'Pusat',
                'total'     => (double)$total_pusat,
            ];
            $data_total_pc = [
                'company'   => 'Part Center',
                'total'     => (double)$total_pc,
            ];
            $data_total_online = [
                'company'   => 'Online',
                'total'     => (double)$total_online,
            ];

            $data_packing_oo = [
                'keterangan'    => 'Pembelian',
                'packing'       => (double)$total_packing_sheet,
                'on_order'      => (double)$total_on_order,
            ];

            $data_dashboard = [
                'total_stock'   => $total_pusat + $total_pc + $total_online,
                'pembelian'     => [ $data_packing_oo ],
                'company'       => [ $data_total_pusat, $data_total_pc, $data_total_online ],
                'fs'            => [ $data_fast_moving, $data_slow_moving ],
                'cno'           => [ $data_current, $data_non_current, $data_others ],
                'product'       => $data_stock_by_product
            ];


            return Response::responseSuccess('success', $data_dashboard);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
