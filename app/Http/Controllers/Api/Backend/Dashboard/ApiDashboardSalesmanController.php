<?php

namespace App\Http\Controllers\Api\Backend\Dashboard;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;


class ApiDashboardSalesmanController extends Controller
{
    public function dashboardPenjualanBulanan(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'year'      => 'required|string',
                'month'     => 'required|string',
                'user_id'   => 'required|string',
                'role_id'   => 'required|string',
                'companyid' => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih bulan dan tahun terlebih dahulu");
            }

            $tahun_sekarang = (int)date('Y');
            $bulan_sekarang = (int)date('m');

            if(!empty($request->get('year'))) {
                $tahun_sekarang = (int)$request->get('year');
            }
            if(!empty($request->get('month'))) {
                $bulan_sekarang = (int)$request->get('month');
            }

            $tahun_lalu = $tahun_sekarang;
            $bulan_lalu = $bulan_sekarang;

            if($bulan_sekarang == 1) {
                $tahun_lalu = $bulan_sekarang - 1;
                $bulan_lalu = 12;
            } else {
                $bulan_lalu = $bulan_sekarang - 1;
            }


            $sql = "select	isnull(omset.companyid, '') as companyid, isnull(omset.kd_produk, '') as kode_produk,
                            isnull(omset.nama_produk, '') as nama_produk, isnull(omset.level, '') as level, isnull(omset.kd_mkr, '') as kode_mkr,
                            isnull(omset.keterangan_level, '') as keterangan_level, isnull(omset.nourut, 0) as nomor_urut,
                            sum(isnull(omset.target_sekarang_amount, 0)) as target_sekarang_amount,
                            sum(isnull(omset.target_lalu_amount, 0)) as target_lalu_amount,
                            sum(isnull(omset.faktur_sekarang_amount, 0)) as faktur_sekarang_amount,
                            sum(isnull(omset.faktur_lalu_amount, 0)) as faktur_lalu_amount,
                            sum(isnull(omset.retur_sekarang_amount, 0)) as retur_sekarang_amount,
                            sum(isnull(omset.retur_lalu_amount, 0)) as retur_lalu_amount,
                            sum(isnull(omset.omset_sekarang_amount, 0)) as omset_sekarang_amount,
                            sum(isnull(omset.omset_lalu_amount, 0)) as omset_lalu_amount,
                            sum(iif(isnull(omset.target_sekarang_amount, 0) <= 0, 0,
                                round(((isnull(omset.omset_sekarang_amount, 0) / isnull(omset.target_sekarang_amount, 0)) * 100), 0))) as 'prosentase_sekarang_amount',
                            sum(iif(isnull(omset.target_lalu_amount, 0) <= 0, 0,
                                round(((isnull(omset.omset_lalu_amount, 0) / isnull(omset.target_lalu_amount, 0)) * 100), 0))) as 'prosentase_lalu_amount'
                    from
                    (
                        select	salesman.companyid, salesman.kd_sales, salesman.spv, produk.nourut, produk.kd_produk,
                                produk.nama as nama_produk, produk.level, produk.keterangan_level, produk.kd_mkr,
                                target.target_sekarang_amount, target.target_lalu_amount,
                                faktur.faktur_sekarang_amount, faktur.faktur_lalu_amount,
                                retur.retur_sekarang_amount, retur.retur_lalu_amount,
                                isnull(faktur.faktur_sekarang_amount, 0) - isnull(retur.retur_sekarang_amount, 0) as 'omset_sekarang_amount',
                                isnull(faktur.faktur_lalu_amount, 0) - isnull(retur.retur_lalu_amount, 0) as 'omset_lalu_amount'
                        from
                        (
                            select	'report' as report, salesman.companyid, salesman.kd_sales, salesman.spv
                            from	salesman with (nolock)
                                        left join superspv with (nolock) on salesman.spv=superspv.kd_spv and
                                                    salesman.companyid=superspv.companyid
                            where	salesman.companyid='".strtoupper(trim($request->get('companyid')))."'";

            if(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql .= " and salesman.kd_sales='".trim($request->get('user_id'))."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql .= " and salesman.spv='".trim($request->get('user_id'))."'";
            }

            if(!empty($request->get('kode_mkr')) && $request->get('kode_mkr') != '') {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    $sql .= " and salesman.kd_sales='".trim($request->get('kode_mkr'))."'";
                } elseif(strtoupper(trim($request->get('jenis_mkr'))) == 'SUPERVISOR') {
                    $sql .= " and salesman.spv='".trim($request->get('kode_mkr'))."'";
                }
            }

            $sql .= " )	salesman
                        left join
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
                            from	produk with (nolock)
                            where   produk.kd_produk is not null";

            if(!empty($request->get('kode_produk')) && $request->get('kode_produk') != '') {
                $sql .= " and produk.kd_produk='".$request->get('kode_produk')."'";
            }

            $sql .= " )	produk on salesman.report=produk.report
                        left join
                        (
                            select	iif(isnull(target_sekarang.companyid, '')='', target_lalu.companyid, target_sekarang.companyid) as companyid,
                                    iif(isnull(target_sekarang.kd_sales, '')='', target_lalu.kd_sales, target_sekarang.kd_sales) as kd_sales,
                                    iif(isnull(target_sekarang.kd_produk, '')='', target_lalu.kd_produk, target_sekarang.kd_produk) as kd_produk,
                                    isnull(target_sekarang.target_sekarang_amount, 0) as target_sekarang_amount,
                                    isnull(target_lalu.target_lalu_amount, 0) as target_lalu_amount
                            from
                            (
                                select	target_jual.companyid, target_jual.kd_sales,
                                        iif(isnull(target_jual.kd_produk, '')='', 'LLL', target_jual.kd_produk) as kd_produk,
                                        sum(target_jual.target) as 'target_sekarang_amount'
                                from	target_jual with (nolock)
                                            left join salesman with (nolock) on target_jual.kd_sales=salesman.kd_sales and
                                                        target_jual.companyid=salesman.companyid
                                            left join superspv with (nolock) on salesman.spv=superspv.kd_spv and
                                                        target_jual.companyid=superspv.companyid
                                where	target_jual.companyid='".strtoupper(trim($request->get('companyid')))."' and
                                        target_jual.tahun='".$tahun_sekarang."' and
                                        target_jual.bulan='".$bulan_sekarang."'";

            if(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql .= " and target_jual.kd_sales='".trim($request->get('user_id'))."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql .= " and salesman.spv='".trim($request->get('user_id'))."'";
            }

            if(!empty($request->get('kode_mkr')) && $request->get('kode_mkr') != '') {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    $sql .= " and target_jual.kd_sales='".trim($request->get('kode_mkr'))."'";
                } elseif(strtoupper(trim($request->get('jenis_mkr'))) == 'SUPERVISOR') {
                    $sql .= " and salesman.spv='".trim($request->get('kode_mkr'))."'";
                }
            }

            $sql .= " group by target_jual.companyid, target_jual.kd_sales, target_jual.kd_produk
                            )	target_sekarang
                            full outer join
                            (
                                select	target_jual.companyid, target_jual.kd_sales,
                                        iif(isnull(target_jual.kd_produk, '')='', 'LLL', target_jual.kd_produk) as kd_produk,
                                        sum(target_jual.target) as 'target_lalu_amount'
                                from	target_jual with (nolock)
                                            left join salesman with (nolock) on target_jual.kd_sales=salesman.kd_sales and
                                                        target_jual.companyid=salesman.companyid
                                where	target_jual.companyid='".strtoupper(trim($request->get('companyid')))."' and
                                        target_jual.tahun='".$tahun_lalu."' and
                                        target_jual.bulan='".$bulan_lalu."'";

            if(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql .= " and target_jual.kd_sales='".trim($request->get('user_id'))."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql .= " and salesman.spv='".trim($request->get('user_id'))."'";
            }

            if(!empty($request->get('kode_mkr')) && $request->get('kode_mkr') != '') {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    $sql .= " and target_jual.kd_sales='".trim($request->get('kode_mkr'))."'";
                } elseif(strtoupper(trim($request->get('jenis_mkr'))) == 'SUPERVISOR') {
                    $sql .= " and salesman.spv='".trim($request->get('kode_mkr'))."'";
                }
            }

            $sql .= " group by target_jual.companyid, target_jual.kd_sales, target_jual.kd_produk
                            )	target_lalu on target_sekarang.kd_sales=target_lalu.kd_sales and
                                    target_sekarang.kd_produk=target_lalu.kd_produk and
                                    target_sekarang.companyid=target_lalu.companyid
                        )	target on salesman.kd_sales=target.kd_sales and produk.kd_produk=target.kd_produk and
                                        salesman.companyid=target.companyid
                        left join
                        (
                            select	iif(isnulL(faktur_sekarang.companyid, '')='', faktur_lalu.companyid, faktur_sekarang.companyid) as companyid,
                                    iif(isnulL(faktur_sekarang.kd_sales, '')='', faktur_lalu.kd_sales, faktur_sekarang.kd_sales) as kd_sales,
                                    iif(isnulL(faktur_sekarang.kd_produk, '')='', faktur_lalu.kd_produk, faktur_sekarang.kd_produk) as kd_produk,
                                    isnull(faktur_sekarang.faktur_sekarang_amount, 0) as faktur_sekarang_amount,
                                    isnull(faktur_lalu.faktur_lalu_amount, 0) as faktur_lalu_amount
                            from
                            (
                                select	faktur.companyid, faktur.kd_sales, iif(isnull(produk.kd_produk, '')='', 'LLL', produk.kd_produk) as kd_produk,
                                        sum(faktur.total) as faktur_sekarang_amount
                                from
                                (
                                    select	faktur.companyid, faktur.no_faktur, faktur.kd_sales, faktur.tgl_faktur,
                                            faktur.kd_part, faktur.jml_jual,
                                            isnull(faktur.total, 0) -
                                                iif(isnull(faktur_total.total_faktur, 0) <= 0, 0, round(((isnull(faktur.DiscRp, 0) / isnull(faktur_total.total_faktur, 0))), 0)) -
                                                    iif(isnull(faktur_total.total_faktur, 0) <= 0, 0, round(((isnull(faktur.DiscRp1, 0) / isnull(faktur_total.total_faktur, 0))), 0)) as 'total'
                                    from
                                    (
                                        select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur, faktur.kd_sales,
                                                faktur.discrp, faktur.discrp1, fakt_dtl.kd_part, fakt_dtl.jml_jual,
                                                isnull(fakt_dtl.jumlah, 0) - round((isnull(fakt_dtl.jumlah, 0) * isnull(faktur.disc2, 0)) / 100, 0) as 'total'
                                        from
                                        (
                                            select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur, faktur.kd_sales,
                                                    faktur.disc2, faktur.discrp, faktur.discrp1
                                            from	faktur with (nolock)
                                                        left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and faktur.companyid=salesman.companyid
                                            where   faktur.companyid='".strtoupper(trim($request->get('companyid')))."' and
                                                    year(faktur.tgl_faktur)='".$tahun_sekarang."' and
                                                    month(faktur.tgl_faktur)='".$bulan_sekarang."'";

            if(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql .= " and faktur.kd_sales='".trim($request->get('user_id'))."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql .= " and salesman.spv='".trim($request->get('user_id'))."'";
            }

            if(!empty($request->get('kode_mkr')) && $request->get('kode_mkr') != '') {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    $sql .= " and faktur.kd_sales='".trim($request->get('kode_mkr'))."'";
                } elseif(strtoupper(trim($request->get('jenis_mkr'))) == 'SUPERVISOR') {
                    $sql .= " and salesman.spv='".trim($request->get('kode_mkr'))."'";
                }
            }

            $sql .= " )	faktur
                                                left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and faktur.companyid=fakt_dtl.companyid
                                        where isnull(fakt_dtl.jml_jual, 0) > 0
                                    )	faktur
                                    left join
                                    (
                                        select	faktur.companyid, faktur.no_faktur, count(fakt_dtl.no_faktur) as 'total_faktur'
                                        from
                                        (
                                            select	faktur.companyid, faktur.no_faktur
                                            from	faktur with (nolock)
                                                        left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and faktur.companyid=salesman.companyid
                                                        left join superspv with (nolock) on salesman.spv=superspv.kd_spv and faktur.companyid=superspv.companyid
                                            where   faktur.companyid='".strtoupper(trim($request->get('companyid')))."' and
                                                    year(faktur.tgl_faktur)='".$tahun_sekarang."' and
                                                    month(faktur.tgl_faktur)='".$bulan_sekarang."'";

            if(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql .= " and faktur.kd_sales='".trim($request->get('user_id'))."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql .= " and salesman.spv='".trim($request->get('user_id'))."'";
            }

            if(!empty($request->get('kode_mkr')) && $request->get('kode_mkr') != '') {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    $sql .= " and faktur.kd_sales='".trim($request->get('kode_mkr'))."'";
                } elseif(strtoupper(trim($request->get('jenis_mkr'))) == 'SUPERVISOR') {
                    $sql .= " and salesman.spv='".trim($request->get('kode_mkr'))."'";
                }
            }

            $sql .= " )	faktur
                                                inner join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and faktur.companyid=fakt_dtl.companyid
                                        where isnull(fakt_dtl.jml_jual, 0) > 0
                                        group by faktur.companyid, faktur.no_faktur
                                    )	faktur_total on faktur.no_faktur=faktur_total.no_faktur and faktur.companyid=faktur_total.companyid
                                )	faktur
                                        left join part with (nolock) on faktur.kd_part=part.kd_part and faktur.companyid=part.companyid
                                        left join sub with (nolock) on part.kd_sub=sub.kd_sub
                                        left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                                group by faktur.companyid, year(faktur.tgl_faktur), month(faktur.tgl_faktur), faktur.kd_sales,
                                        iif(isnull(produk.kd_produk, '')='', 'LLL', produk.kd_produk)
                            )	faktur_sekarang
                            full outer join
                            (
                                select	faktur.companyid, faktur.kd_sales, iif(isnull(produk.kd_produk, '')='', 'LLL', produk.kd_produk) as kd_produk,
                                        sum(faktur.total) as faktur_lalu_amount
                                from
                                (
                                    select	faktur.companyid, faktur.no_faktur, faktur.kd_sales, faktur.tgl_faktur,
                                            faktur.kd_part, faktur.jml_jual,
                                            isnull(faktur.total, 0) -
                                                iif(isnull(faktur_total.total_faktur, 0) <= 0, 0, round(((isnull(faktur.DiscRp, 0) / isnull(faktur_total.total_faktur, 0))), 0)) -
                                                    iif(isnull(faktur_total.total_faktur, 0) <= 0, 0, round(((isnull(faktur.DiscRp1, 0) / isnull(faktur_total.total_faktur, 0))), 0)) as 'total'
                                    from
                                    (
                                        select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur, faktur.kd_sales,
                                                faktur.discrp, faktur.discrp1, fakt_dtl.kd_part, fakt_dtl.jml_jual,
                                                isnull(fakt_dtl.jumlah, 0) - round((isnull(fakt_dtl.jumlah, 0) * isnull(faktur.disc2, 0)) / 100, 0) as 'total'
                                        from
                                        (
                                            select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur, faktur.kd_sales,
                                                    faktur.disc2, faktur.discrp, faktur.discrp1
                                            from	faktur with (nolock)
                                                        left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and faktur.companyid=salesman.companyid
                                                        left join superspv with (nolock) on salesman.spv=superspv.kd_spv and faktur.companyid=superspv.companyid
                                            where   faktur.companyid='".strtoupper(trim($request->get('companyid')))."' and
                                                    year(faktur.tgl_faktur)='".$tahun_lalu."' and
                                                    month(faktur.tgl_faktur)='".$bulan_lalu."'";

            if(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql .= " and faktur.kd_sales='".trim($request->get('user_id'))."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql .= " and salesman.spv='".trim($request->get('user_id'))."'";
            }

            if(!empty($request->get('kode_mkr')) && $request->get('kode_mkr') != '') {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    $sql .= " and faktur.kd_sales='".trim($request->get('kode_mkr'))."'";
                } elseif(strtoupper(trim($request->get('jenis_mkr'))) == 'SUPERVISOR') {
                    $sql .= " and salesman.spv='".trim($request->get('kode_mkr'))."'";
                }
            }

            $sql .= " )	faktur
                                                left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and faktur.companyid=fakt_dtl.companyid
                                        where isnull(fakt_dtl.jml_jual, 0) > 0
                                    )	faktur
                                    left join
                                    (
                                        select	faktur.companyid, faktur.no_faktur, count(fakt_dtl.no_faktur) as 'total_faktur'
                                        from
                                        (
                                            select	faktur.companyid, faktur.no_faktur
                                            from	faktur with (nolock)
                                                        left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and faktur.companyid=salesman.companyid
                                                        left join superspv with (nolock) on salesman.spv=superspv.kd_spv and faktur.companyid=superspv.companyid
                                            where   faktur.companyid='".strtoupper(trim($request->get('companyid')))."' and
                                                    year(faktur.tgl_faktur)='".$tahun_lalu."' and
                                                    month(faktur.tgl_faktur)='".$bulan_lalu."'";

            if(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql .= " and faktur.kd_sales='".trim($request->get('user_id'))."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql .= " and salesman.spv='".trim($request->get('user_id'))."'";
            }

            if(!empty($request->get('kode_mkr')) && $request->get('kode_mkr') != '') {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    $sql .= " and faktur.kd_sales='".trim($request->get('kode_mkr'))."'";
                } elseif(strtoupper(trim($request->get('jenis_mkr'))) == 'SUPERVISOR') {
                    $sql .= " and salesman.spv='".trim($request->get('kode_mkr'))."'";
                }
            }

            $sql .= " )	faktur
                                                inner join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and faktur.companyid=fakt_dtl.companyid
                                        where isnull(fakt_dtl.jml_jual, 0) > 0
                                        group by faktur.companyid, faktur.no_faktur
                                    )	faktur_total on faktur.no_faktur=faktur_total.no_faktur and faktur.companyid=faktur_total.companyid
                                )	faktur
                                        left join part with (nolock) on faktur.kd_part=part.kd_part and faktur.companyid=part.companyid
                                        left join sub with (nolock) on part.kd_sub=sub.kd_sub
                                        left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                                group by faktur.companyid, year(faktur.tgl_faktur), month(faktur.tgl_faktur), faktur.kd_sales,
                                        iif(isnull(produk.kd_produk, '')='', 'LLL', produk.kd_produk)
                            )	faktur_lalu on faktur_sekarang.kd_sales=faktur_lalu.kd_sales and faktur_sekarang.kd_produk=faktur_lalu.kd_produk and
                                            faktur_sekarang.companyid=faktur_lalu.companyid
                        )	faktur on salesman.kd_sales=faktur.kd_sales and produk.kd_produk=faktur.kd_produk and salesman.companyid=faktur.companyid
                        left join
                        (
                            select	iif(isnull(retur_sekarang.companyid, '')='', retur_lalu.companyid, retur_sekarang.companyid) as companyid,
                                    iif(isnull(retur_sekarang.kd_sales, '')='', retur_lalu.kd_sales, retur_sekarang.kd_sales) as kd_sales,
                                    iif(isnull(retur_sekarang.kd_produk, '')='', retur_lalu.kd_produk, retur_sekarang.kd_produk) as kd_produk,
                                    isnull(retur_sekarang.retur_sekarang_amount, 0) as retur_sekarang_amount,
                                    isnull(retur_lalu.retur_lalu_amount, 0) as retur_lalu_amount
                            from
                            (
                                select	rtoko.companyid, rtoko.kd_sales, iif(isnull(produk.kd_produk, '')='', 'LLL', produk.kd_produk) as kd_produk,
                                        sum(isnull(rtoko_dtl.jumlah, 0) * isnull(part.hrg_pokok, 0)) as 'retur_sekarang_amount'
                                from
                                (
                                    select	rtoko.companyid, rtoko.no_retur, rtoko.kd_sales, rtoko.tanggal
                                    from	rtoko with (nolock)
                                                left join salesman with (nolock) on rtoko.kd_sales=salesman.kd_sales and rtoko.companyid=salesman.companyid
                                    where   rtoko.companyid='".strtoupper(trim($request->get('companyid')))."' and
                                            year(rtoko.tanggal)='".$tahun_sekarang."' and
                                            month(rtoko.tanggal)='".$bulan_sekarang."'";

            if(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql .= " and rtoko.kd_sales='".trim($request->get('user_id'))."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql .= " and salesman.spv='".trim($request->get('user_id'))."'";
            }

            if(!empty($request->get('kode_mkr')) && $request->get('kode_mkr') != '') {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    $sql .= " and rtoko.kd_sales='".trim($request->get('kode_mkr'))."'";
                } elseif(strtoupper(trim($request->get('jenis_mkr'))) == 'SUPERVISOR') {
                    $sql .= " and salesman.spv='".trim($request->get('kode_mkr'))."'";
                }
            }

            $sql .= " )	rtoko
                                        left join rtoko_dtl with (nolock) on rtoko.no_retur=rtoko_dtl.no_retur and rtoko.companyid=rtoko_dtl.companyid
                                        left join part with (nolock) on rtoko_dtl.kd_part=part.kd_part and rtoko.companyid=part.companyid
                                        left join sub with (nolock) on part.kd_sub=sub.kd_sub
                                        left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                                where	isnull(rtoko_dtl.jumlah, 0) > 0
                                group by rtoko.companyid, rtoko.kd_sales, year(rtoko.tanggal), month(rtoko.tanggal),
                                        iif(isnull(produk.kd_produk, '')='', 'LLL', produk.kd_produk)
                            )	retur_sekarang
                            full outer join
                            (
                                select	rtoko.companyid, rtoko.kd_sales, iif(isnull(produk.kd_produk, '')='', 'LLL', produk.kd_produk) as kd_produk,
                                        sum(isnull(rtoko_dtl.jumlah, 0) * isnull(part.hrg_pokok, 0)) as 'retur_lalu_amount'
                                from
                                (
                                    select	rtoko.companyid, rtoko.no_retur, rtoko.kd_sales, rtoko.tanggal
                                    from	rtoko with (nolock)
                                                left join salesman with (nolock) on rtoko.kd_sales=salesman.kd_sales and rtoko.companyid=salesman.companyid
                                                left join superspv with (nolock) on salesman.spv=superspv.kd_spv and rtoko.companyid=superspv.companyid
                                    where   rtoko.companyid='".strtoupper(trim($request->get('companyid')))."' and
                                            year(rtoko.tanggal)='".$tahun_lalu."' and
                                            month(rtoko.tanggal)='".$bulan_lalu."'";

            if(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql .= " and rtoko.kd_sales='".trim($request->get('user_id'))."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql .= " and salesman.spv='".trim($request->get('user_id'))."'";
            }

            if(!empty($request->get('kode_mkr')) && $request->get('kode_mkr') != '') {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    $sql .= " and rtoko.kd_sales='".trim($request->get('kode_mkr'))."'";
                } elseif(strtoupper(trim($request->get('jenis_mkr'))) == 'SUPERVISOR') {
                    $sql .= " and salesman.spv='".trim($request->get('kode_mkr'))."'";
                }
            }

            $sql .= " )	rtoko
                                        left join rtoko_dtl with (nolock) on rtoko.no_retur=rtoko_dtl.no_retur and rtoko.companyid=rtoko_dtl.companyid
                                        left join part with (nolock) on rtoko_dtl.kd_part=part.kd_part and rtoko.companyid=part.companyid
                                        left join sub with (nolock) on part.kd_sub=sub.kd_sub
                                        left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                                where	isnull(rtoko_dtl.jumlah, 0) > 0
                                group by rtoko.companyid, rtoko.kd_sales, year(rtoko.tanggal), month(rtoko.tanggal),
                                        iif(isnull(produk.kd_produk, '')='', 'LLL', produk.kd_produk)
                            )	retur_lalu on retur_sekarang.kd_sales=retur_lalu.kd_sales and retur_sekarang.kd_produk=retur_lalu.kd_produk and
                                        retur_sekarang.companyid=retur_lalu.companyid
                        )	retur on salesman.kd_sales=retur.kd_sales and produk.kd_produk=retur.kd_produk and salesman.companyid=retur.companyid
                    )	omset
                            left join salesman with (nolock) on omset.kd_sales=salesman.kd_sales and omset.companyid=salesman.companyid
                            left join superspv with (nolock) on salesman.spv=superspv.kd_spv and omset.companyid=superspv.companyid
                    group by omset.companyid, omset.kd_produk, omset.nama_produk, omset.nourut, omset.kd_mkr, omset.level, omset.keterangan_level
                    order by omset.companyid asc, omset.kd_mkr asc, omset.keterangan_level asc, omset.nourut asc";

            $result = DB::select($sql);

            $target_sekarang_amount_total = 0;
            $target_lalu_amount_total = 0;

            $penjualan_sekarang_amount_total = 0;
            $penjualan_lalu_amount_total = 0;

            $retur_sekarang_amount_total = 0;
            $retur_lalu_amount_total = 0;

            $omset_sekarang_amount_total = 0;
            $omset_lalu_amount_total = 0;

            $total_target_produk_handle = 0;
            $total_target_produk_non_handle = 0;
            $total_target_produk_tube = 0;
            $total_target_produk_oli = 0;

            $total_omset_produk_handle = 0;
            $total_omset_produk_non_handle = 0;
            $total_omset_produk_tube = 0;
            $total_omset_produk_oli = 0;

            $keterangan_level = '';
            $total_target_amount_sekarang_per_level = 0;
            $total_omset_amount_sekarang_per_level = 0;

            $total_target_amount_lalu_per_level = 0;
            $total_omset_amount_lalu_per_level = 0;

            $prosentase_per_produk_sekarang_per_level = 0;
            $prosentase_per_produk_lalu_per_level = 0;

            $selisih_per_level = 0;
            $selisih_prosentase_per_level = 0;
            $keterangan_selisih_perlevel = 'BERTAHAN';
            $keterangan_per_produk = 'BERTAHAN';

            $data_omset_detail = new Collection();

            foreach($result as $data) {
                $target_sekarang_amount_total = (double)$target_sekarang_amount_total + $data->target_sekarang_amount;
                $penjualan_sekarang_amount_total = (double)$penjualan_sekarang_amount_total + $data->faktur_sekarang_amount;
                $retur_sekarang_amount_total = (double)$retur_sekarang_amount_total + $data->retur_sekarang_amount;
                $omset_sekarang_amount_total = (double)$omset_sekarang_amount_total + $data->omset_sekarang_amount;

                $target_lalu_amount_total = (double)$target_lalu_amount_total + $data->target_lalu_amount;
                $penjualan_lalu_amount_total = (double)$penjualan_lalu_amount_total + $data->faktur_lalu_amount;
                $retur_lalu_amount_total = (double)$retur_lalu_amount_total + $data->retur_lalu_amount;
                $omset_lalu_amount_total = (double)$omset_lalu_amount_total + $data->omset_lalu_amount;

                if(strtoupper(trim($data->keterangan_level)) == 'KSJS') {
                    $total_target_produk_handle = $total_target_produk_handle + $data->target_sekarang_amount;
                    $total_omset_produk_handle = $total_omset_produk_handle + $data->faktur_sekarang_amount;
                } elseif(strtoupper(trim($data->keterangan_level)) == 'MPM') {
                    $total_target_produk_non_handle = $total_target_produk_non_handle + $data->target_sekarang_amount;
                    $total_omset_produk_non_handle = $total_omset_produk_non_handle + $data->faktur_sekarang_amount;
                } elseif(strtoupper(trim($data->keterangan_level)) == 'TUBE') {
                    $total_target_produk_tube = $total_target_produk_tube + $data->target_sekarang_amount;
                    $total_omset_produk_tube = $total_omset_produk_tube + $data->faktur_sekarang_amount;
                } elseif(strtoupper(trim($data->keterangan_level)) == 'OLI') {
                    $total_target_produk_oli = $total_target_produk_oli + $data->target_sekarang_amount;
                    $total_omset_produk_oli = $total_omset_produk_oli + $data->faktur_sekarang_amount;
                }

                $prosentase_per_produk_sekarang = ((double)$data->target_sekarang_amount <= 0) ? 0 : ((double)$data->omset_sekarang_amount / (double)$data->target_sekarang_amount) * 100;
                $prosentase_per_produk_lalu = ((double)$data->target_lalu_amount <= 0) ? 0 : ((double)$data->omset_lalu_amount / (double)$data->target_lalu_amount) * 100;
                $keterangan_per_produk = ($prosentase_per_produk_sekarang == $prosentase_per_produk_lalu) ? 'BERTAHAN' : (($prosentase_per_produk_sekarang > $prosentase_per_produk_lalu) ? 'NAIK' : 'TURUN');
                $selisih = ((double)$prosentase_per_produk_sekarang - (double)$prosentase_per_produk_lalu);
                $selisih_prosentase = ((double)$prosentase_per_produk_sekarang >= (double)$prosentase_per_produk_lalu) ? (double)$prosentase_per_produk_sekarang - (double)$prosentase_per_produk_lalu : (double)$prosentase_per_produk_lalu - (double)$prosentase_per_produk_sekarang;
                $keterangan_selisih = ((double)$selisih > 0) ? 'NAIK' : (((double)$selisih < 0) ? 'TURUN' : 'BERTAHAN');

                if($keterangan_level <> '') {
                    if(strtoupper(trim($keterangan_level)) <> strtoupper(trim($data->keterangan_level))) {
                        $prosentase_per_produk_sekarang_per_level = ((double)$total_target_amount_sekarang_per_level <= 0) ? 0 : ((double)$total_omset_amount_sekarang_per_level / (double)$total_target_amount_sekarang_per_level) * 100;
                        $prosentase_per_produk_lalu_per_level = ((double)$total_target_amount_lalu_per_level <= 0) ? 0 : ((double)$total_omset_amount_lalu_per_level / (double)$total_target_amount_lalu_per_level) * 100;

                        $selisih_per_level = ((double)$prosentase_per_produk_sekarang_per_level - (double)$prosentase_per_produk_lalu_per_level);
                        $selisih_prosentase_per_level = ((double)$prosentase_per_produk_sekarang_per_level >= (double)$prosentase_per_produk_lalu_per_level) ? (double)$prosentase_per_produk_sekarang_per_level - (double)$prosentase_per_produk_lalu_per_level : (double)$prosentase_per_produk_lalu_per_level - (double)$prosentase_per_produk_sekarang_per_level;
                        $keterangan_selisih_perlevel = ((double)$selisih_per_level > 0) ? 'NAIK' : (((double)$selisih_per_level < 0) ? 'TURUN' : 'BERTAHAN');

                        $data_omset_detail->push((object) [
                            'kode_produk'   => '',
                            'nama_produk'   => 'TOTAL '.trim($keterangan_level),
                            'target'        => (double)$total_target_amount_sekarang_per_level,
                            'omset'         => (double)$total_omset_amount_sekarang_per_level,
                            'prosentase'    => ((double)$total_target_amount_sekarang_per_level <= 0) ? 0 : ((double)$total_omset_amount_sekarang_per_level / (double)$total_target_amount_sekarang_per_level) * 100,
                            'level'         => 'ZZZ',
                            'keterangan'    => trim($keterangan_per_produk),
                            'prosentase_selisih' => (double)$selisih_prosentase_per_level,
                            'keterangan_selisih' => trim($keterangan_selisih_perlevel),
                        ]);

                        $keterangan_level = trim($data->keterangan_level);
                        $total_target_amount_sekarang_per_level = 0;
                        $total_omset_amount_sekarang_per_level = 0;
                        $total_target_amount_lalu_per_level = 0;
                        $total_omset_amount_lalu_per_level = 0;
                    }
                } else {
                    $keterangan_level = trim($data->keterangan_level);
                }

                $total_target_amount_sekarang_per_level = (double)$total_target_amount_sekarang_per_level + $data->target_sekarang_amount;
                $total_omset_amount_sekarang_per_level = (double)$total_omset_amount_sekarang_per_level + $data->omset_sekarang_amount;
                $total_target_amount_lalu_per_level = (double)$total_target_amount_lalu_per_level + $data->target_lalu_amount;
                $total_omset_amount_lalu_per_level = (double)$total_omset_amount_lalu_per_level + $data->omset_lalu_amount;

                $data_omset_detail->push((object) [
                    'kode_produk'   => trim($data->kode_produk),
                    'nama_produk'   => trim($data->nama_produk),
                    'target'        => (double)$data->target_sekarang_amount,
                    'omset'         => (double)$data->omset_sekarang_amount,
                    'prosentase'    => ((double)$data->target_sekarang_amount <= 0) ? 0 : ((double)$data->omset_sekarang_amount / (double)$data->target_sekarang_amount) * 100,
                    'level'         => trim($data->keterangan_level),
                    'keterangan'    => trim($keterangan_per_produk),
                    'prosentase_selisih' => (double)$selisih_prosentase,
                    'keterangan_selisih' => trim($keterangan_selisih),
                ]);
            }
            //HASIL TOTAL PRODUK TERAKHIR
            $data_omset_detail->push((object) [
                'kode_produk'   => '',
                'nama_produk'   => 'TOTAL '.trim($keterangan_level),
                'target'        => (double)$total_target_amount_sekarang_per_level,
                'omset'         => (double)$total_omset_amount_sekarang_per_level,
                'prosentase'    => ((double)$total_target_amount_sekarang_per_level <= 0) ? 0 : ((double)$total_omset_amount_sekarang_per_level / (double)$total_target_amount_sekarang_per_level) * 100,
                'level'         => 'ZZZ',
                'keterangan'    => trim($keterangan_per_produk),
                'prosentase_selisih' => (double)$selisih_prosentase_per_level,
                'keterangan_selisih' => trim($keterangan_selisih_perlevel),
            ]);

            $prosentase_target_amount_total = ((double)$target_lalu_amount_total <= 0) ? 0 : ((((double)$target_sekarang_amount_total - (double)$target_lalu_amount_total) / (double)$target_lalu_amount_total) * 100);
            $prosentase_penjualan_amount_total = ((double)$penjualan_lalu_amount_total <= 0) ? 0 : ((((double)$penjualan_sekarang_amount_total - (double)$penjualan_lalu_amount_total) / (double)$penjualan_lalu_amount_total) * 100);
            $prosentase_retur_amount_total = ((double)$retur_lalu_amount_total <= 0) ? 0 : ((((double)$retur_sekarang_amount_total - (double)$retur_lalu_amount_total) / (double)$retur_lalu_amount_total) * 100);
            $prosentase_omset_amount_total = ((double)$omset_lalu_amount_total <= 0) ? 0 : ((((double)$omset_sekarang_amount_total - (double)$omset_lalu_amount_total) / (double)$omset_lalu_amount_total) * 100);

            $keterangan_target_amount_total = ($prosentase_target_amount_total == 0) ? 'BERTAHAN' : (($prosentase_target_amount_total > 0) ? 'NAIK' : 'TURUN');
            $keterangan_penjualan_amount_total = ($prosentase_penjualan_amount_total == 0) ? 'BERTAHAN' : (($prosentase_penjualan_amount_total > 0) ? 'NAIK' : 'TURUN');
            $keterangan_retur_amount_total = ($prosentase_retur_amount_total == 0) ? 'BERTAHAN' : (($prosentase_retur_amount_total > 0) ? 'NAIK' : 'TURUN');
            $keterangan_omset_amount_total = ($prosentase_omset_amount_total == 0) ? 'BERTAHAN' : (($prosentase_omset_amount_total > 0) ? 'NAIK' : 'TURUN');

            $prosentase_amount_sekarang_total = ((double)$target_sekarang_amount_total <= 0) ? 0 : ((double)$penjualan_sekarang_amount_total / (double)$target_sekarang_amount_total) * 100;
            $prosentase_amount_lalu_total = ((double)$target_lalu_amount_total <= 0) ? 0 : ((double)$penjualan_lalu_amount_total / (double)$target_lalu_amount_total) * 100;

            $selisih_total = ((double)$prosentase_amount_sekarang_total - (double)$prosentase_amount_lalu_total);
            $selisih_prosentase_total = ((double)$prosentase_amount_sekarang_total >= (double)$prosentase_amount_lalu_total) ? (double)$prosentase_amount_sekarang_total - (double)$prosentase_amount_lalu_total : (double)$prosentase_amount_lalu_total - (double)$prosentase_amount_sekarang_total;
            $keterangan_selisih_total = ((double)$selisih_total > 0) ? 'NAIK' : (((double)$selisih_total < 0) ? 'TURUN' : 'BERTAHAN');

            //HASIL GRAND TOTAL
            $data_omset_detail->push((object) [
                'kode_produk'   => '',
                'nama_produk'   => 'GRAND TOTAL',
                'target'        => (double)$target_sekarang_amount_total,
                'omset'         => (double)$omset_sekarang_amount_total,
                'prosentase'    => ((double)$target_sekarang_amount_total <= 0) ? 0 : ((double)$penjualan_sekarang_amount_total / (double)$target_sekarang_amount_total) * 100,
                'level'         => 'ZZZ',
                'keterangan'    => trim($keterangan_selisih_total),
                'prosentase_selisih' => (double)$selisih_prosentase_total,
                'keterangan_selisih' => trim($keterangan_selisih_total),
            ]);

            $total_target_produk_handle = $total_target_produk_handle + $data->target_sekarang_amount;
            $total_omset_produk_handle = $total_omset_produk_handle + $data->faktur_sekarang_amount;

            $data_dashboard[] = [
                'title_menu'                        => 'Dashboard',
                'month'                             => $bulan_sekarang,
                'year'                              => $tahun_sekarang,
                'role_id'                           => $request->get('role_id'),
                'jenis_mkr'                         => $request->get('jenis_mkr'),
                'kode_mkr'                          => $request->get('kode_mkr'),
                'target_amount_total'               => (double)$target_sekarang_amount_total,
                'target_amount_keterangan'          => $keterangan_target_amount_total,
                'target_amount_prosentase'          => (double)$prosentase_target_amount_total,
                'penjualan_amount_total'            => (double)$penjualan_sekarang_amount_total,
                'penjualan_amount_keterangan'       => $keterangan_penjualan_amount_total,
                'penjualan_amount_prosentase'       => (double)$prosentase_penjualan_amount_total,
                'retur_amount_total'                => (double)$retur_sekarang_amount_total,
                'retur_amount_keterangan'           => $keterangan_retur_amount_total,
                'retur_amount_prosentase'           => (double)$prosentase_retur_amount_total,
                'omset_amount_total'                => (double)$omset_sekarang_amount_total,
                'omset_amount_keterangan'           => $keterangan_omset_amount_total,
                'omset_amount_prosentase'           => (double)$prosentase_omset_amount_total,
                'prosentase_amount_total'           => (double)$prosentase_amount_sekarang_total,
                'detail_group_per_level'            => [
                    'handle'        => ((double)$total_target_produk_handle <= 0) ? 0 : ((double)$total_omset_produk_handle / (double)$total_target_produk_handle) * 100,
                    'non_handle'    => ((double)$total_target_produk_non_handle <= 0) ? 0 : ((double)$total_omset_produk_non_handle / (double)$total_target_produk_non_handle) * 100,
                    'tube'          => ((double)$total_target_produk_tube <= 0) ? 0 : ((double)$total_omset_produk_tube / (double)$total_target_produk_tube) * 100,
                    'oli'           => ((double)$total_target_produk_oli <= 0) ? 0 : ((double)$total_omset_produk_oli / (double)$total_target_produk_oli) * 100,
                ],
                'detail_omset'                      => $data_omset_detail
            ];

            return Response::responseSuccess('success', collect($data_dashboard)->first());
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function dashboardPenjualanHarian(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'year'      => 'required|string',
                'month'     => 'required|string',
                'user_id'   => 'required|string',
                'role_id'   => 'required|string',
                'companyid' => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih bulan dan tahun terlebih dahulu");
            }

            $sql = "select	day(faktur.tgl_faktur) as 'day',
                            sum(faktur.total) as 'total'
                    from	faktur with (nolock)
                                left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and faktur.companyid=salesman.companyid
                                left join superspv with (nolock) on salesman.spv=superspv.kd_spv and faktur.companyid=superspv.companyid
                    where	faktur.companyid=? and year(faktur.tgl_faktur)=? and
                            month(faktur.tgl_faktur)=?";

            if(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql .= " and faktur.kd_sales='".trim($request->get('user_id'))."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == "MD_H3_KORSM") {
                $sql .= " and salesman.spv='".trim($request->get('user_id'))."'";
            } else {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    $sql .= " and faktur.kd_sales='".trim($request->get('kode_mkr'))."'";
                } elseif(strtoupper(trim($request->get('jenis_mkr'))) == 'SUPERVISOR') {
                    $sql .= " and salesman.spv='".trim($request->get('kode_mkr'))."'";
                }
            }

            $sql .= " group by day(faktur.tgl_faktur)
                    order by day(faktur.tgl_faktur) asc ";

            $result = DB::select($sql, [ trim(trim($request->get('companyid'))), $request->get('year'), $request->get('month') ]);

            $data_chart = new Collection();

            foreach($result as $data) {
                $data_chart->push((object) [
                    'day'   => $data->day,
                    'total' => $data->total,
                ]);
            }

            return Response::responseSuccess('success', $data_chart);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

}
