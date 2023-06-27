<?php

namespace App\Http\Controllers\Api\Backend\Dashboard\Marketing;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;

class ApiDashboardMarketingController extends Controller
{
    public function dashboardPencapaianPerLevel(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'year'      => 'required|string',
                'role_id'   => 'required|string',
                'companyid' => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih data tahun terlebih dahulu");
            }

            $sql = "select	produk.group_level, produk.bulan,
                            cast(sum(isnull(produk.target, 0)) as decimal(13,0)) as target,
                            cast(sum(isnull(produk.faktur, 0)) as decimal(13,0)) as faktur,
                            cast(sum(isnull(produk.retur, 0)) as decimal(13,0)) as retur
                    from
                    (
                        select	produk.group_level, bulan.bulan, 0 as target, 0 as faktur, 0 as retur
                        from
                        (
                            select	'".strtoupper(trim($request->get('companyid')))."' as companyid, produk.kd_mkr, produk.level,
                                    max(produk.group_level) as group_level
                            from
                            (
                                select	produk.kd_produk, produk.kd_mkr, produk.level, produk.nourut,
                                        case
                                            when produk.level='AHM' and produk.kd_mkr='G' then 'KSJS'
                                            when produk.level='MPM' and produk.kd_mkr='G' then 'MPM'
                                            when produk.level='AHM' and produk.kd_mkr='I' then 'TUBE'
                                            when produk.level='AHM' and produk.kd_mkr='J' then 'OLI'
                                        else
                                            produk.kd_mkr
                                        end as group_level
                                from	produk with (nolock)
                            )	produk
                            group by produk.kd_mkr, produk.level
                        )	produk
                        left join
                        (
                            select '".strtoupper(trim($request->get('companyid')))."' as companyid, 1 as bulan union all
                            select '".strtoupper(trim($request->get('companyid')))."' as companyid, 2 as bulan union all
                            select '".strtoupper(trim($request->get('companyid')))."' as companyid, 3 as bulan union all
                            select '".strtoupper(trim($request->get('companyid')))."' as companyid, 4 as bulan union all
                            select '".strtoupper(trim($request->get('companyid')))."' as companyid, 5 as bulan union all
                            select '".strtoupper(trim($request->get('companyid')))."' as companyid, 6 as bulan union all
                            select '".strtoupper(trim($request->get('companyid')))."' as companyid, 7 as bulan union all
                            select '".strtoupper(trim($request->get('companyid')))."' as companyid, 8 as bulan union all
                            select '".strtoupper(trim($request->get('companyid')))."' as companyid, 9 as bulan union all
                            select '".strtoupper(trim($request->get('companyid')))."' as companyid, 10 as bulan union all
                            select '".strtoupper(trim($request->get('companyid')))."' as companyid, 11 as bulan union all
                            select '".strtoupper(trim($request->get('companyid')))."' as companyid, 12 as bulan
                        )	bulan on produk.companyid=bulan.companyid
                        union all
                        select	target_jual.group_level, target_jual.bulan,
                                sum(isnull(target_jual.target, 0)) as target,
                                0 as faktur, 0 as retur
                        from
                        (
                            select	target_jual.companyid, target_jual.tahun, target_jual.bulan,
                                    target_jual.kd_produk, target_jual.target,
                                    case
                                        when produk.level='AHM' and produk.kd_mkr='G' then 'KSJS'
                                        when produk.level='MPM' and produk.kd_mkr='G' then 'MPM'
                                        when produk.level='AHM' and produk.kd_mkr='I' then 'TUBE'
                                        when produk.level='AHM' and produk.kd_mkr='J' then 'OLI'
                                    else
                                        produk.kd_mkr
                                    end as group_level
                            from	target_jual with (nolock)
                                        inner join produk with (nolock) on target_jual.kd_produk=produk.kd_produk
                                        left join salesman with (nolock) on target_jual.kd_sales=salesman.kd_sales and
                                                    target_jual.companyid=salesman.companyid
                            where	target_jual.companyid='".strtoupper(trim($request->get('companyid')))."' and
                                    target_jual.tahun='".$request->get('year')."'";

            if(!empty(strtoupper(trim($request->get('kode_mkr')))) && strtoupper(trim($request->get('kode_mkr'))) != '') {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    $sql .= " and salesman.kd_sales='".strtoupper(trim($request->get('kode_mkr')))."'";
                } else if(strtoupper(trim($request->get('jenis_mkr'))) == 'SUPERVISOR') {
                    $sql .= " and salesman.spv='".strtoupper(trim($request->get('kode_mkr')))."'";
                }
            }

            $sql .= " )	target_jual
                        group by target_jual.group_level, target_jual.bulan
                        union all
                        select	faktur.group_level, faktur.bulan, 0 as target,
                                sum(isnull(faktur.total, 0)) as faktur, 0 as retur
                        from
                        (
                            select	faktur.companyid, month(faktur.tgl_faktur) as bulan, produk.kd_produk,
                                    case
                                        when produk.level='AHM' and produk.kd_mkr='G' then 'KSJS'
                                        when produk.level='MPM' and produk.kd_mkr='G' then 'MPM'
                                        when produk.level='AHM' and produk.kd_mkr='I' then 'TUBE'
                                        when produk.level='AHM' and produk.kd_mkr='J' then 'OLI'
                                    else
                                        produk.kd_mkr
                                    end as group_level, faktur.total
                            from
                            (
                                select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur, faktur.kd_part,
                                        iif(isnull(faktur_total.faktur_total, 0) <= 0,
                                            isnull(faktur.total, 0),
                                            isnull(faktur.total, 0) -
                                                round(((isnull(faktur.DiscRp, 0) / isnull(faktur_total.faktur_total, 0))), 0) -
                                                round(((isnull(faktur.DiscRp1, 0) / isnull(faktur_total.faktur_total, 0))), 0)
                                        ) as total
                                from
                                (
                                    select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur, fakt_dtl.kd_part,
                                            faktur.discrp, faktur.discrp1,
                                            isnull(fakt_dtl.jumlah, 0) -
                                                round((isnull(fakt_dtl.jumlah, 0) * isnull(faktur.disc2, 0)) / 100, 0) as 'total'
                                    from
                                    (
                                        select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur,
                                                faktur.disc2, faktur.discrp, faktur.discrp1
                                        from	faktur with (nolock)
                                                    left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and
                                                            faktur.companyid=salesman.companyid
                                        where	faktur.companyid='".strtoupper(trim($request->get('companyid')))."' and
                                                year(faktur.tgl_faktur)='".$request->get('year')."'";

            if(!empty(strtoupper(trim($request->get('kode_mkr')))) && strtoupper(trim($request->get('kode_mkr'))) != '') {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    $sql .= " and salesman.kd_sales='".strtoupper(trim($request->get('kode_mkr')))."'";
                } else if(strtoupper(trim($request->get('jenis_mkr'))) == 'SUPERVISOR') {
                    $sql .= " and salesman.spv='".strtoupper(trim($request->get('kode_mkr')))."'";
                }
            }

            $sql .= " )	faktur
                                            left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                                        faktur.companyid=fakt_dtl.companyid
                                    where	isnull(fakt_dtl.jml_jual, 0) > 0
                                )	faktur
                                left join
                                (
                                    select	faktur.companyid, faktur.no_faktur,
                                            count(fakt_dtl.no_faktur) as faktur_total
                                    from
                                    (
                                        select	faktur.companyid, faktur.no_faktur
                                        from	faktur with (nolock)
                                                    left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and
                                                            faktur.companyid=salesman.companyid
                                        where	faktur.companyid='".strtoupper(trim($request->get('companyid')))."' and
                                                year(faktur.tgl_faktur)='".$request->get('year')."'";

            if(!empty(strtoupper(trim($request->get('kode_mkr')))) && strtoupper(trim($request->get('kode_mkr'))) != '') {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    $sql .= " and salesman.kd_sales='".strtoupper(trim($request->get('kode_mkr')))."'";
                } else if(strtoupper(trim($request->get('jenis_mkr'))) == 'SUPERVISOR') {
                    $sql .= " and salesman.spv='".strtoupper(trim($request->get('kode_mkr')))."'";
                }
            }

            $sql .= " )	faktur
                                            left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                                    faktur.companyid=fakt_dtl.companyid
                                    where	isnull(fakt_dtl.jml_jual, 0) > 0
                                    group by faktur.companyid, faktur.no_faktur
                                )	faktur_total on faktur.no_faktur=faktur_total.no_faktur and faktur.companyid=faktur_total.companyid
                            )	faktur
                                    inner join part with (nolock) on faktur.kd_part=part.kd_part and faktur.companyid=part.companyid
                                    left join sub with (nolock) on part.kd_sub=sub.kd_sub
                                    left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                        )	faktur
                        group by faktur.group_level, faktur.bulan
                        union all
                        select	rtoko.group_level, month(rtoko.tanggal) as bulan, 0 as target, 0 as faktur,
                                sum(isnull(rtoko.total_retur, 0)) as retur
                        from
                        (
                            select	rtoko.companyid, produk.kd_produk, rtoko.tanggal,
                                    case
                                        when produk.level='AHM' and produk.kd_mkr='G' then 'KSJS'
                                        when produk.level='MPM' and produk.kd_mkr='G' then 'MPM'
                                        when produk.level='AHM' and produk.kd_mkr='I' then 'TUBE'
                                        when produk.level='AHM' and produk.kd_mkr='J' then 'OLI'
                                    else
                                        produk.kd_mkr
                                    end as group_level, isnull(rtoko_dtl.jumlah, 0) * isnull(part.hrg_pokok, 0) as total_retur
                            from
                            (
                                select	rtoko.companyid, rtoko.no_retur, rtoko.tanggal
                                from	rtoko with (nolock)
                                            left join salesman with (nolock) on rtoko.kd_sales=salesman.kd_sales and
                                                        rtoko.companyid=salesman.companyid
                                where	rtoko.companyid='".strtoupper(trim($request->get('companyid')))."' and
                                        year(rtoko.tanggal)='".$request->get('year')."'";

            if(!empty(strtoupper(trim($request->get('kode_mkr')))) && strtoupper(trim($request->get('kode_mkr'))) != '') {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    $sql .= " and salesman.kd_sales='".strtoupper(trim($request->get('kode_mkr')))."'";
                } else if(strtoupper(trim($request->get('jenis_mkr'))) == 'SUPERVISOR') {
                    $sql .= " and salesman.spv='".strtoupper(trim($request->get('kode_mkr')))."'";
                }
            }

            $sql .= " )	rtoko
                                    inner join rtoko_dtl with (nolock) on rtoko.no_retur=rtoko_dtl.no_retur and
                                                rtoko.companyid=rtoko_dtl.companyid
                                    left join part with (nolock) on rtoko_dtl.kd_part=part.kd_part and
                                                rtoko.companyid=part.companyid
                                    left join sub with (nolock) on part.kd_sub=sub.kd_sub
                                    left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                            where	isnull(rtoko_dtl.jumlah, 0) > 0
                        )	rtoko
                        group by rtoko.group_level, month(rtoko.tanggal)
                    )	produk
                    where       isnull(produk.target, 0) > 0 or isnull(produk.faktur, 0) > 0 or isnull(produk.retur, 0) > 0
                    group by    produk.group_level, produk.bulan
                    order by    produk.bulan asc, produk.group_level asc";

            $result = DB::select($sql);

            $data_ksjs = [];
            $data_mpm = [];
            $data_tube = [];
            $data_oli = [];
            $data_produk = new Collection();

            foreach($result as $data) {
                $bulan = '';

                if((double)$data->bulan == 1) {
                    $bulan = 'Jan';
                } elseif((double)$data->bulan == 2) {
                    $bulan = 'Feb';
                } elseif((double)$data->bulan == 3) {
                    $bulan = 'Mar';
                } elseif((double)$data->bulan == 4) {
                    $bulan = 'Apr';
                } elseif((double)$data->bulan == 5) {
                    $bulan = 'Mei';
                } elseif((double)$data->bulan == 6) {
                    $bulan = 'Jun';
                } elseif((double)$data->bulan == 7) {
                    $bulan = 'Jul';
                } elseif((double)$data->bulan == 8) {
                    $bulan = 'Agt';
                } elseif((double)$data->bulan == 9) {
                    $bulan = 'Sep';
                } elseif((double)$data->bulan == 10) {
                    $bulan = 'Okt';
                } elseif((double)$data->bulan == 11) {
                    $bulan = 'Nov';
                } elseif((double)$data->bulan == 12) {
                    $bulan = 'Des';
                }

                $data_produk->push((object) [
                    'produk'        => strtoupper(trim($data->group_level)),
                    'bulan'         => (double)$data->bulan,
                    'target'        => (double)$data->target,
                    'pencapaian'    => (double)$data->faktur - (double)$data->retur
                ]);

                if(strtoupper(trim($data->group_level)) == 'KSJS') {
                    $data_ksjs[] = [
                        'bulan'         => $bulan,
                        'target'        => (double)$data->target,
                        'pencapaian'    => (double)$data->faktur - (double)$data->retur,
                        'prosentase'    => ((double)$data->target <= 0) ? 0 : (((double)$data->faktur - (double)$data->retur) / (double)$data->target) * 100
                    ];
                } elseif(strtoupper(trim($data->group_level)) == 'MPM') {
                    $data_mpm[] = [
                        'bulan'         => $bulan,
                        'target'        => (double)$data->target,
                        'pencapaian'    => (double)$data->faktur - (double)$data->retur,
                        'prosentase'    => ((double)$data->target <= 0) ? 0 : (((double)$data->faktur - (double)$data->retur) / (double)$data->target) * 100
                    ];
                } elseif(strtoupper(trim($data->group_level)) == 'TUBE') {
                    $data_tube[] = [
                        'bulan'         => $bulan,
                        'target'        => (double)$data->target,
                        'pencapaian'    => (double)$data->faktur - (double)$data->retur,
                        'prosentase'    => ((double)$data->target <= 0) ? 0 : (((double)$data->faktur - (double)$data->retur) / (double)$data->target) * 100
                    ];
                } elseif(strtoupper(trim($data->group_level)) == 'OLI') {
                    $data_oli[] = [
                        'bulan'         => $bulan,
                        'target'        => (double)$data->target,
                        'pencapaian'    => (double)$data->faktur - (double)$data->retur,
                        'prosentase'    => ((double)$data->target <= 0) ? 0 : (((double)$data->faktur - (double)$data->retur) / (double)$data->target) * 100
                    ];
                }
            }

            $month = 0;
            $data_total = [];

            foreach($data_produk as $data) {
                if((double)$month != (double)$data->bulan) {
                    if((double)$data->bulan == 1) {
                        $bulan = 'Jan';
                    } elseif((double)$data->bulan == 2) {
                        $bulan = 'Feb';
                    } elseif((double)$data->bulan == 3) {
                        $bulan = 'Mar';
                    } elseif((double)$data->bulan == 4) {
                        $bulan = 'Apr';
                    } elseif((double)$data->bulan == 5) {
                        $bulan = 'Mei';
                    } elseif((double)$data->bulan == 6) {
                        $bulan = 'Jun';
                    } elseif((double)$data->bulan == 7) {
                        $bulan = 'Jul';
                    } elseif((double)$data->bulan == 8) {
                        $bulan = 'Agt';
                    } elseif((double)$data->bulan == 9) {
                        $bulan = 'Sep';
                    } elseif((double)$data->bulan == 10) {
                        $bulan = 'Okt';
                    } elseif((double)$data->bulan == 11) {
                        $bulan = 'Nov';
                    } elseif((double)$data->bulan == 12) {
                        $bulan = 'Des';
                    }

                    $target_total = collect($data_produk->where('bulan', $data->bulan)->values()->all())->sum('target');
                    $pencapaian_total = collect($data_produk->where('bulan', $data->bulan)->values()->all())->sum('pencapaian');

                    $data_total[] = [
                        'bulan'         => $bulan,
                        'target'        => (double)$target_total,
                        'pencapaian'    => (double)$pencapaian_total,
                        'prosentase'    => ((double)$target_total <= 0) ? 0 : (((double)$pencapaian_total / (double)$target_total) * 100)
                    ];
                    $month = (double)$data->bulan;
                }
            }

            $data_dashboard = [
                'total'         => $data_total,
                'handle'        => $data_ksjs,
                'non_handle'    => $data_mpm,
                'tube'          => $data_tube,
                'oli'           => $data_oli
            ];

            return Response::responseSuccess('success', $data_dashboard);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function dashboardPerbandinganGrowthPerTahun(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'year'      => 'required',
                'role_id'   => 'required|string',
                'companyid' => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih data tahun terlebih dahulu");
            }

            $tahun_pilih = $request->get('year');
            $tahun_lalu = $request->get('year') - 1;

            $sql = "select	isnull(salesman.companyid, '') as companyid, isnull(salesman.kd_sales, '') as kode_sales, isnull(salesman.spv, '') as spv,
                            isnull(faktur.faktur_baru1, 0) - isnull(rtoko.retur_baru1, 0) as pencapaian_baru1,
                            isnull(faktur.faktur_baru2, 0) - isnull(rtoko.retur_baru2, 0) as pencapaian_baru2,
                            isnull(faktur.faktur_baru3, 0) - isnull(rtoko.retur_baru3, 0) as pencapaian_baru3,
                            isnull(faktur.faktur_baru4, 0) - isnull(rtoko.retur_baru4, 0) as pencapaian_baru4,
                            isnull(faktur.faktur_baru5, 0) - isnull(rtoko.retur_baru5, 0) as pencapaian_baru5,
                            isnull(faktur.faktur_baru6, 0) - isnull(rtoko.retur_baru6, 0) as pencapaian_baru6,
                            isnull(faktur.faktur_baru7, 0) - isnull(rtoko.retur_baru7, 0) as pencapaian_baru7,
                            isnull(faktur.faktur_baru8, 0) - isnull(rtoko.retur_baru8, 0) as pencapaian_baru8,
                            isnull(faktur.faktur_baru9, 0) - isnull(rtoko.retur_baru9, 0) as pencapaian_baru9,
                            isnull(faktur.faktur_baru10, 0) - isnull(rtoko.retur_baru10, 0) as pencapaian_baru10,
                            isnull(faktur.faktur_baru11, 0) - isnull(rtoko.retur_baru11, 0) as pencapaian_baru11,
                            isnull(faktur.faktur_baru12, 0) - isnull(rtoko.retur_baru12, 0) as pencapaian_baru12,
                            isnull(faktur.faktur_lalu1, 0) - isnull(rtoko.retur_lalu1, 0) as pencapaian_lalu1,
                            isnull(faktur.faktur_lalu2, 0) - isnull(rtoko.retur_lalu2, 0) as pencapaian_lalu2,
                            isnull(faktur.faktur_lalu3, 0) - isnull(rtoko.retur_lalu3, 0) as pencapaian_lalu3,
                            isnull(faktur.faktur_lalu4, 0) - isnull(rtoko.retur_lalu4, 0) as pencapaian_lalu4,
                            isnull(faktur.faktur_lalu5, 0) - isnull(rtoko.retur_lalu5, 0) as pencapaian_lalu5,
                            isnull(faktur.faktur_lalu6, 0) - isnull(rtoko.retur_lalu6, 0) as pencapaian_lalu6,
                            isnull(faktur.faktur_lalu7, 0) - isnull(rtoko.retur_lalu7, 0) as pencapaian_lalu7,
                            isnull(faktur.faktur_lalu8, 0) - isnull(rtoko.retur_lalu8, 0) as pencapaian_lalu8,
                            isnull(faktur.faktur_lalu9, 0) - isnull(rtoko.retur_lalu9, 0) as pencapaian_lalu9,
                            isnull(faktur.faktur_lalu10, 0) - isnull(rtoko.retur_lalu10, 0) as pencapaian_lalu10,
                            isnull(faktur.faktur_lalu11, 0) - isnull(rtoko.retur_lalu11, 0) as pencapaian_lalu11,
                            isnull(faktur.faktur_lalu12, 0) - isnull(rtoko.retur_lalu12, 0) as pencapaian_lalu12
                    from
                    (
                        select	salesman.companyid, salesman.spv, salesman.kd_sales
                        from
                        (
                            select	salesman.companyid, salesman.kd_sales, salesman.spv
                            from	salesman with (nolock)
                            where	salesman.companyid='".$request->get('companyid')."'";

            if(!empty($request->get('kode_mkr')) && trim($request->get('kode_mkr') != '')) {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    $sql .= " and salesman.kd_sales='".$request->get('kode_mkr')."'";
                }
            }

            $sql .= " )	salesman
                                left join superspv with (nolock) on salesman.spv=superspv.kd_spv and
                                        salesman.companyid=superspv.companyid";

            if(!empty($request->get('kode_mkr')) && trim($request->get('kode_mkr') != '')) {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SUPERVISOR') {
                    $sql .= " and superspv.kd_spv='".$request->get('kode_mkr')."'";
                }
            }

            $sql .= " group by salesman.companyid, salesman.spv, salesman.kd_sales
                    )	salesman
                    left join
                    (
                        select	faktur.companyid, faktur.kd_sales,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_pilih."' and month(faktur.tgl_faktur)='1', isnull(faktur.total, 0), 0)) as faktur_baru1,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_pilih."' and month(faktur.tgl_faktur)='2', isnull(faktur.total, 0), 0)) as faktur_baru2,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_pilih."' and month(faktur.tgl_faktur)='3', isnull(faktur.total, 0), 0)) as faktur_baru3,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_pilih."' and month(faktur.tgl_faktur)='4', isnull(faktur.total, 0), 0)) as faktur_baru4,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_pilih."' and month(faktur.tgl_faktur)='5', isnull(faktur.total, 0), 0)) as faktur_baru5,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_pilih."' and month(faktur.tgl_faktur)='6', isnull(faktur.total, 0), 0)) as faktur_baru6,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_pilih."' and month(faktur.tgl_faktur)='7', isnull(faktur.total, 0), 0)) as faktur_baru7,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_pilih."' and month(faktur.tgl_faktur)='".$request->get('month')."', isnull(faktur.total, 0), 0)) as faktur_baru8,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_pilih."' and month(faktur.tgl_faktur)='9', isnull(faktur.total, 0), 0)) as faktur_baru9,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_pilih."' and month(faktur.tgl_faktur)='10', isnull(faktur.total, 0), 0)) as faktur_baru10,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_pilih."' and month(faktur.tgl_faktur)='11', isnull(faktur.total, 0), 0)) as faktur_baru11,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_pilih."' and month(faktur.tgl_faktur)='12', isnull(faktur.total, 0), 0)) as faktur_baru12,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_lalu."' and month(faktur.tgl_faktur)='1', isnull(faktur.total, 0), 0)) as faktur_lalu1,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_lalu."' and month(faktur.tgl_faktur)='2', isnull(faktur.total, 0), 0)) as faktur_lalu2,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_lalu."' and month(faktur.tgl_faktur)='3', isnull(faktur.total, 0), 0)) as faktur_lalu3,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_lalu."' and month(faktur.tgl_faktur)='4', isnull(faktur.total, 0), 0)) as faktur_lalu4,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_lalu."' and month(faktur.tgl_faktur)='5', isnull(faktur.total, 0), 0)) as faktur_lalu5,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_lalu."' and month(faktur.tgl_faktur)='6', isnull(faktur.total, 0), 0)) as faktur_lalu6,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_lalu."' and month(faktur.tgl_faktur)='7', isnull(faktur.total, 0), 0)) as faktur_lalu7,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_lalu."' and month(faktur.tgl_faktur)='".$request->get('month')."', isnull(faktur.total, 0), 0)) as faktur_lalu8,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_lalu."' and month(faktur.tgl_faktur)='9', isnull(faktur.total, 0), 0)) as faktur_lalu9,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_lalu."' and month(faktur.tgl_faktur)='10', isnull(faktur.total, 0), 0)) as faktur_lalu10,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_lalu."' and month(faktur.tgl_faktur)='11', isnull(faktur.total, 0), 0)) as faktur_lalu11,
                                sum(iif(year(faktur.tgl_faktur)='".$tahun_lalu."' and month(faktur.tgl_faktur)='12', isnull(faktur.total, 0), 0)) as faktur_lalu12
                        from
                        (
                            select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur, faktur.kd_sales, faktur.kd_part,
                                    iif(isnull(faktur_total.faktur_total, 0) <= 0,
                                        isnull(faktur.total, 0),
                                        isnull(faktur.total, 0) -
                                            round(((isnull(faktur.DiscRp, 0) / isnull(faktur_total.faktur_total, 0))), 0) -
                                            round(((isnull(faktur.DiscRp1, 0) / isnull(faktur_total.faktur_total, 0))), 0)
                                    ) as total
                            from
                            (
                                select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur, faktur.kd_sales,
                                        fakt_dtl.kd_part, faktur.discrp, faktur.discrp1,
                                        isnull(fakt_dtl.jumlah, 0) -
                                            round((isnull(fakt_dtl.jumlah, 0) * isnull(faktur.disc2, 0)) / 100, 0) as 'total'
                                from
                                (
                                    select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur, faktur.kd_sales,
                                            faktur.disc2, faktur.discrp, faktur.discrp1
                                    from	faktur with (nolock)
                                                left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and
                                                        faktur.companyid=salesman.companyid
                                                left join superspv with (nolock) on salesman.spv=superspv.kd_spv and
                                                        faktur.companyid=superspv.companyid
                                    where	faktur.companyid='".$request->get('companyid')."' and
                                            faktur.tgl_faktur between '".$tahun_lalu."-01-01' and '".$tahun_pilih."-12-31'";

            if(!empty($request->get('kode_mkr')) && trim($request->get('kode_mkr') != '')) {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    $sql .= " and faktur.kd_sales='".$request->get('kode_mkr')."'";
                } elseif(strtoupper(trim($request->get('jenis_mkr'))) == 'SUPERVISOR') {
                    $sql .= " and salesman.spv='".$request->get('kode_mkr')."'";
                }
            }

            $sql .= " )	faktur
                                        left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                                faktur.companyid=fakt_dtl.companyid
                                where	isnull(fakt_dtl.jml_jual, 0) > 0
                            )	faktur
                            left join
                            (
                                select	faktur.companyid, faktur.no_faktur, count(fakt_dtl.no_faktur) as faktur_total
                                from
                                (
                                    select	faktur.companyid, faktur.no_faktur
                                    from	faktur with (nolock)
                                                left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and
                                                        faktur.companyid=salesman.companyid
                                                left join superspv with (nolock) on salesman.spv=superspv.kd_spv and
                                                        faktur.companyid=superspv.companyid
                                    where	faktur.companyid='".$request->get('companyid')."' and
                                            (isnull(faktur.discrp, 0) > 0 or isnull(faktur.discrp1, 0) > 0) and
                                            faktur.tgl_faktur between '".$tahun_lalu."-01-01' and '".$tahun_pilih."-12-31'";

            if(!empty($request->get('kode_mkr')) && trim($request->get('kode_mkr') != '')) {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    $sql .= " and faktur.kd_sales='".$request->get('kode_mkr')."'";
                } elseif(strtoupper(trim($request->get('jenis_mkr'))) == 'SUPERVISOR') {
                    $sql .= " and salesman.spv='".$request->get('kode_mkr')."'";
                }
            }

            $sql .= " )	faktur
                                        left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                                faktur.companyid=fakt_dtl.companyid
                                where	isnull(fakt_dtl.jml_jual, 0) > 0
                                group by faktur.companyid, faktur.no_faktur
                            )	faktur_total on faktur.no_faktur=faktur_total.no_faktur and faktur.companyid=faktur_total.companyid
                                    inner join part with (nolock) on faktur.kd_part=part.kd_part and faktur.companyid=part.companyid
                                    left join sub with (nolock) on part.kd_sub=sub.kd_sub
                                    left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                            where faktur.companyid is not null";

            if(strtoupper(trim($request->get('level_produk'))) == 'HANDLE') {
                $sql .= " and produk.level='AHM' and produk.kd_mkr='G'";
            } elseif(strtoupper(trim($request->get('level_produk'))) == 'NON_HANDLE') {
                $sql .= " and produk.level='MPM' and produk.kd_mkr='G'";
            } elseif(strtoupper(trim($request->get('level_produk'))) == 'TUBE') {
                $sql .= " and produk.level='AHM' and produk.kd_mkr='I'";
            } elseif(strtoupper(trim($request->get('level_produk'))) == 'OLI') {
                $sql .= " and produk.level='AHM' and produk.kd_mkr='J'";
            }

            if(!empty($request->get('kode_produk')) && trim($request->get('kode_produk') != '')) {
                $sql .= " and produk.kd_produk='".trim($request->get('kode_produk'))."'";
            }

            $sql .= " )	faktur
                        group by faktur.companyid, faktur.kd_sales
                    )	faktur on salesman.companyid=faktur.companyid and salesman.kd_sales=faktur.kd_sales
                    left join
                    (
                        select	rtoko.companyid, rtoko.kd_sales,
                                sum(iif(year(rtoko.tanggal)='".$tahun_pilih."' and month(rtoko.tanggal)='1', isnull(rtoko.total_retur, 0), 0)) as retur_baru1,
                                sum(iif(year(rtoko.tanggal)='".$tahun_pilih."' and month(rtoko.tanggal)='2', isnull(rtoko.total_retur, 0), 0)) as retur_baru2,
                                sum(iif(year(rtoko.tanggal)='".$tahun_pilih."' and month(rtoko.tanggal)='3', isnull(rtoko.total_retur, 0), 0)) as retur_baru3,
                                sum(iif(year(rtoko.tanggal)='".$tahun_pilih."' and month(rtoko.tanggal)='4', isnull(rtoko.total_retur, 0), 0)) as retur_baru4,
                                sum(iif(year(rtoko.tanggal)='".$tahun_pilih."' and month(rtoko.tanggal)='5', isnull(rtoko.total_retur, 0), 0)) as retur_baru5,
                                sum(iif(year(rtoko.tanggal)='".$tahun_pilih."' and month(rtoko.tanggal)='6', isnull(rtoko.total_retur, 0), 0)) as retur_baru6,
                                sum(iif(year(rtoko.tanggal)='".$tahun_pilih."' and month(rtoko.tanggal)='7', isnull(rtoko.total_retur, 0), 0)) as retur_baru7,
                                sum(iif(year(rtoko.tanggal)='".$tahun_pilih."' and month(rtoko.tanggal)='".$request->get('month')."', isnull(rtoko.total_retur, 0), 0)) as retur_baru8,
                                sum(iif(year(rtoko.tanggal)='".$tahun_pilih."' and month(rtoko.tanggal)='9', isnull(rtoko.total_retur, 0), 0)) as retur_baru9,
                                sum(iif(year(rtoko.tanggal)='".$tahun_pilih."' and month(rtoko.tanggal)='10', isnull(rtoko.total_retur, 0), 0)) as retur_baru10,
                                sum(iif(year(rtoko.tanggal)='".$tahun_pilih."' and month(rtoko.tanggal)='11', isnull(rtoko.total_retur, 0), 0)) as retur_baru11,
                                sum(iif(year(rtoko.tanggal)='".$tahun_pilih."' and month(rtoko.tanggal)='12', isnull(rtoko.total_retur, 0), 0)) as retur_baru12,
                                sum(iif(year(rtoko.tanggal)='".$tahun_lalu."' and month(rtoko.tanggal)='1', isnull(rtoko.total_retur, 0), 0)) as retur_lalu1,
                                sum(iif(year(rtoko.tanggal)='".$tahun_lalu."' and month(rtoko.tanggal)='2', isnull(rtoko.total_retur, 0), 0)) as retur_lalu2,
                                sum(iif(year(rtoko.tanggal)='".$tahun_lalu."' and month(rtoko.tanggal)='3', isnull(rtoko.total_retur, 0), 0)) as retur_lalu3,
                                sum(iif(year(rtoko.tanggal)='".$tahun_lalu."' and month(rtoko.tanggal)='4', isnull(rtoko.total_retur, 0), 0)) as retur_lalu4,
                                sum(iif(year(rtoko.tanggal)='".$tahun_lalu."' and month(rtoko.tanggal)='5', isnull(rtoko.total_retur, 0), 0)) as retur_lalu5,
                                sum(iif(year(rtoko.tanggal)='".$tahun_lalu."' and month(rtoko.tanggal)='6', isnull(rtoko.total_retur, 0), 0)) as retur_lalu6,
                                sum(iif(year(rtoko.tanggal)='".$tahun_lalu."' and month(rtoko.tanggal)='7', isnull(rtoko.total_retur, 0), 0)) as retur_lalu7,
                                sum(iif(year(rtoko.tanggal)='".$tahun_lalu."' and month(rtoko.tanggal)='".$request->get('month')."', isnull(rtoko.total_retur, 0), 0)) as retur_lalu8,
                                sum(iif(year(rtoko.tanggal)='".$tahun_lalu."' and month(rtoko.tanggal)='9', isnull(rtoko.total_retur, 0), 0)) as retur_lalu9,
                                sum(iif(year(rtoko.tanggal)='".$tahun_lalu."' and month(rtoko.tanggal)='10', isnull(rtoko.total_retur, 0), 0)) as retur_lalu10,
                                sum(iif(year(rtoko.tanggal)='".$tahun_lalu."' and month(rtoko.tanggal)='11', isnull(rtoko.total_retur, 0), 0)) as retur_lalu11,
                                sum(iif(year(rtoko.tanggal)='".$tahun_lalu."' and month(rtoko.tanggal)='12', isnull(rtoko.total_retur, 0), 0)) as retur_lalu12
                        from
                        (
                            select	rtoko.companyid, produk.kd_produk, rtoko.tanggal, rtoko.kd_sales,
                                    isnull(rtoko_dtl.jumlah, 0) * isnull(part.hrg_pokok, 0) as total_retur
                            from
                            (
                                select	rtoko.companyid, rtoko.no_retur, rtoko.tanggal, salesman.kd_sales
                                from	rtoko with (nolock)
                                            left join salesman with (nolock) on rtoko.kd_sales=salesman.kd_sales and
                                                        rtoko.companyid=salesman.companyid
                                            left join superspv with (nolock) on salesman.spv=superspv.kd_spv and
                                                        rtoko.companyid=superspv.companyid
                                where	rtoko.companyid='".$request->get('companyid')."' and rtoko.tanggal between '".$tahun_lalu."-01-01' and '".$tahun_pilih."-12-31'";

            if(!empty($request->get('kode_mkr')) && trim($request->get('kode_mkr') != '')) {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    $sql .= " and rtoko.kd_sales='".$request->get('kode_mkr')."'";
                } elseif(strtoupper(trim($request->get('jenis_mkr'))) == 'SUPERVISOR') {
                    $sql .= " and salesman.spv='".$request->get('kode_mkr')."'";
                }
            }

            $sql .= " )	rtoko
                                    inner join rtoko_dtl with (nolock) on rtoko.no_retur=rtoko_dtl.no_retur and
                                            rtoko.companyid=rtoko_dtl.companyid
                                    left join part with (nolock) on rtoko_dtl.kd_part=part.kd_part and
                                            rtoko.companyid=part.companyid
                                    left join sub with (nolock) on part.kd_sub=sub.kd_sub
                                    left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                            where	isnull(rtoko_dtl.jumlah, 0) > 0";

            if(strtoupper(trim($request->get('level_produk'))) == 'HANDLE') {
                $sql .= " and produk.level='AHM' and produk.kd_mkr='G'";
            } elseif(strtoupper(trim($request->get('level_produk'))) == 'NON_HANDLE') {
                $sql .= " and produk.level='MPM' and produk.kd_mkr='G'";
            } elseif(strtoupper(trim($request->get('level_produk'))) == 'TUBE') {
                $sql .= " and produk.level='AHM' and produk.kd_mkr='I'";
            } elseif(strtoupper(trim($request->get('level_produk'))) == 'OLI') {
                $sql .= " and produk.level='AHM' and produk.kd_mkr='J'";
            }

            if(!empty($request->get('kode_produk')) && trim($request->get('kode_produk') != '')) {
                $sql .= " and produk.kd_produk='".trim($request->get('kode_produk'))."'";
            }

            $sql .= " )	rtoko
                        group by rtoko.companyid, rtoko.kd_sales
                    )	rtoko on salesman.companyid=rtoko.companyid and salesman.kd_sales=rtoko.kd_sales
                    order by salesman.companyid asc, salesman.spv asc, salesman.kd_sales asc";

            $result = DB::select($sql);
            $data_result = new Collection();

            foreach($result as $data) {
                $month = 0;
                $bulan = '';
                $total_baru = 0;
                $total_lalu = 0;
                $growth = 0;

                for($i = 1; $i <= 12; $i++) {
                    $month = $i;

                    if($i == 1) {
                        $bulan = 'Jan';
                        $total_baru = (double)$data->pencapaian_baru1;
                        $total_lalu = (double)$data->pencapaian_lalu1;
                        $growth = ((double)$data->pencapaian_lalu1 <= 0) ? 0 : (((double)$data->pencapaian_baru1 - (double)$data->pencapaian_lalu1) / (double)$data->pencapaian_lalu1) * 100;
                    } elseif($i == 2) {
                        $bulan = 'Feb';
                        $total_baru = (double)$data->pencapaian_baru2;
                        $total_lalu = (double)$data->pencapaian_lalu2;
                        $growth = ((double)$data->pencapaian_lalu2 <= 0) ? 0 : (((double)$data->pencapaian_baru2 - (double)$data->pencapaian_lalu2) / (double)$data->pencapaian_lalu2) * 100;
                    } elseif($i == 3) {
                        $bulan = 'Mar';
                        $total_baru = (double)$data->pencapaian_baru3;
                        $total_lalu = (double)$data->pencapaian_lalu3;
                        $growth = ((double)$data->pencapaian_lalu3 <= 0) ? 0 : (((double)$data->pencapaian_baru3 - (double)$data->pencapaian_lalu3) / (double)$data->pencapaian_lalu3) * 100;
                    } elseif($i == 4) {
                        $bulan = 'Apr';
                        $total_baru = (double)$data->pencapaian_baru4;
                        $total_lalu = (double)$data->pencapaian_lalu4;
                        $growth = ((double)$data->pencapaian_lalu4 <= 0) ? 0 : (((double)$data->pencapaian_baru4 - (double)$data->pencapaian_lalu4) / (double)$data->pencapaian_lalu4) * 100;
                    } elseif($i == 5) {
                        $bulan = 'Mei';
                        $total_baru = (double)$data->pencapaian_baru5;
                        $total_lalu = (double)$data->pencapaian_lalu5;
                        $growth = ((double)$data->pencapaian_lalu5 <= 0) ? 0 : (((double)$data->pencapaian_baru5 - (double)$data->pencapaian_lalu5) / (double)$data->pencapaian_lalu5) * 100;
                    } elseif($i == 6) {
                        $bulan = 'Jun';
                        $total_baru = (double)$data->pencapaian_baru6;
                        $total_lalu = (double)$data->pencapaian_lalu6;
                        $growth = ((double)$data->pencapaian_lalu6 <= 0) ? 0 : (((double)$data->pencapaian_baru6 - (double)$data->pencapaian_lalu6) / (double)$data->pencapaian_lalu6) * 100;
                    } elseif($i == 7) {
                        $bulan = 'Jul';
                        $total_baru = (double)$data->pencapaian_baru7;
                        $total_lalu = (double)$data->pencapaian_lalu7;
                        $growth = ((double)$data->pencapaian_lalu7 <= 0) ? 0 : (((double)$data->pencapaian_baru7 - (double)$data->pencapaian_lalu7) / (double)$data->pencapaian_lalu7) * 100;
                    } elseif($i == 8) {
                        $bulan = 'Agt';
                        $total_baru = (double)$data->pencapaian_baru8;
                        $total_lalu = (double)$data->pencapaian_lalu8;
                        $growth = ((double)$data->pencapaian_lalu8 <= 0) ? 0 : (((double)$data->pencapaian_baru8 - (double)$data->pencapaian_lalu8) / (double)$data->pencapaian_lalu8) * 100;
                    } elseif($i == 9) {
                        $bulan = 'Sep';
                        $total_baru = (double)$data->pencapaian_baru9;
                        $total_lalu = (double)$data->pencapaian_lalu9;
                        $growth = ((double)$data->pencapaian_lalu9 <= 0) ? 0 : (((double)$data->pencapaian_baru9 - (double)$data->pencapaian_lalu9) / (double)$data->pencapaian_lalu9) * 100;
                    } elseif($i == 10) {
                        $bulan = 'Okt';
                        $total_baru = (double)$data->pencapaian_baru10;
                        $total_lalu = (double)$data->pencapaian_lalu10;
                        $growth = ((double)$data->pencapaian_lalu10 <= 0) ? 0 : (((double)$data->pencapaian_baru10 - (double)$data->pencapaian_lalu10) / (double)$data->pencapaian_lalu10) * 100;
                    } elseif($i == 11) {
                        $bulan = 'Nov';
                        $total_baru = (double)$data->pencapaian_baru11;
                        $total_lalu = (double)$data->pencapaian_lalu11;
                        $growth = ((double)$data->pencapaian_lalu11 <= 0) ? 0 : (((double)$data->pencapaian_baru11 - (double)$data->pencapaian_lalu11) / (double)$data->pencapaian_lalu11) * 100;
                    } elseif($i == 12) {
                        $bulan = 'Des';
                        $total_baru = (double)$data->pencapaian_baru12;
                        $total_lalu = (double)$data->pencapaian_lalu12;
                        $growth = ((double)$data->pencapaian_lalu12 <= 0) ? 0 : (((double)$data->pencapaian_baru12 - (double)$data->pencapaian_lalu12) / (double)$data->pencapaian_lalu12) * 100;
                    }

                    $data_result->push((object) [
                        'supervisor'        => strtoupper(trim($data->spv)),
                        'salesman'          => strtoupper(trim($data->kode_sales)),
                        'month'             => (double)$month,
                        'bulan'             => trim($bulan),
                        'tahun_sekarang'    => (double)$total_baru,
                        'tahun_lalu'        => (double)$total_lalu,
                        'growth'            => (double)$growth
                    ]);
                }
            }

            $kode_marketing = '';
            $data_marketing = new Collection();
            foreach($data_result as $data) {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    if(strtoupper(trim($kode_marketing)) != strtoupper(trim($data->salesman))) {
                        $total_sekarang = collect($data_result
                                        ->where('salesman', strtoupper(trim($data->salesman))))
                                        ->sum('tahun_sekarang');
                        $total_lalu = collect($data_result
                                        ->where('salesman', strtoupper(trim($data->salesman))))
                                        ->sum('tahun_lalu');
                        $growth = ((double)$total_lalu <= 0) ? 0 : (((double)$total_sekarang - (double)$total_lalu) / (double)$total_lalu) * 100;

                        if((double)$total_sekarang > 0) {
                            $data_marketing->push((object) [
                                'marketing'         => strtoupper(trim($data->salesman)),
                                'tahun_sekarang'    => (double)$total_sekarang,
                                'tahun_lalu'        => (double)$total_lalu,
                                'growth'            => $growth,
                            ]);
                        }
                        $kode_marketing = strtoupper(trim($data->salesman));
                    }
                } else {
                    if(strtoupper(trim($kode_marketing)) != strtoupper(trim($data->supervisor))) {
                        $total_sekarang = collect($data_result
                                        ->where('supervisor', strtoupper(trim($data->supervisor))))
                                        ->sum('tahun_sekarang');
                        $total_lalu = collect($data_result
                                        ->where('supervisor', strtoupper(trim($data->supervisor))))
                                        ->sum('tahun_lalu');
                        $growth = ((double)$total_lalu <= 0) ? 0 : (((double)$total_sekarang - (double)$total_lalu) / (double)$total_lalu) * 100;

                        if((double)$total_sekarang > 0) {
                            $data_marketing->push((object) [
                                'marketing'         => strtoupper(trim($data->supervisor)),
                                'tahun_sekarang'    => (double)$total_sekarang,
                                'tahun_lalu'        => (double)$total_lalu,
                                'growth'            => $growth,
                            ]);
                        }
                        $kode_marketing = strtoupper(trim($data->supervisor));
                    }
                }
            }

            $month = 0;
            $data_total = new Collection();
            for($i = 1; $i <= 12; $i++) {
                $month = $i;

                if($i == 1) {
                    $bulan = 'Jan';
                } elseif($i == 2) {
                    $bulan = 'Feb';
                } elseif($i == 3) {
                    $bulan = 'Mar';
                } elseif($i == 4) {
                    $bulan = 'Apr';
                } elseif($i == 5) {
                    $bulan = 'Mei';
                } elseif($i == 6) {
                    $bulan = 'Jun';
                } elseif($i == 7) {
                    $bulan = 'Jul';
                } elseif($i == 8) {
                    $bulan = 'Agt';
                } elseif($i == 9) {
                    $bulan = 'Sep';
                } elseif($i == 10) {
                    $bulan = 'Okt';
                } elseif($i == 11) {
                    $bulan = 'Nov';
                } elseif($i == 12) {
                    $bulan = 'Des';
                }
                $total_sekarang = collect($data_result
                            ->where('month', $month))
                            ->sum('tahun_sekarang');
                $total_lalu = collect($data_result
                            ->where('month', $month))
                            ->sum('tahun_lalu');
                $growth = ((double)$total_lalu <= 0) ? 0 : (((double)$total_sekarang - (double)$total_lalu) / (double)$total_lalu) * 100;

                if((double)$total_sekarang > 0) {
                    $data_total->push((object) [
                        'month'             => $month,
                        'bulan'             => trim($bulan),
                        'tahun_sekarang'    => (double)$total_sekarang,
                        'tahun_lalu'        => (double)$total_lalu,
                        'growth'            => $growth,
                    ]);
                }
            }

            $data_dashboard = [
                'total'     => $data_total,
                'marketing' => $data_marketing->sortBy('marketing')->values()->all()
            ];

            return Response::responseSuccess('success', $data_dashboard);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function dashboardPencapaianPerProduk(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'year'      => 'required',
                'month'     => 'required',
                'companyid' => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih data tahun terlebih dahulu");
            }

            $sql = "select	isnull(produk.kd_produk, '') as produk,
                            cast(isnull(target_jual.target, 0) as decimal(13,0)) as target,
                            cast(isnull(faktur.faktur, 0) - isnull(rtoko.retur, 0) as decimal(13,0)) as pencapaian,
                            iif(isnull(target_jual.target, 0) <= 0, 0,
                                ((isnull(faktur.faktur, 0) - isnull(rtoko.retur, 0)) / isnull(target_jual.target, 0)) * 100) as prosentase
                    from
                    (
                        select	'".strtoupper(trim($request->get('companyid')))."' as companyid,
                                produk.kd_produk, produk.nama, produk.level,
                                produk.kd_mkr, produk.nourut
                        from	produk with (nolock)
                        where   produk.kd_produk is not null";

            if(!empty($request->get('level_produk')) && $request->get('level_produk') != '') {
                if(strtoupper(trim($request->get('level_produk'))) == 'HANDLE') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='G'";
                } elseif(strtoupper(trim($request->get('level_produk'))) == 'NON_HANDLE') {
                    $sql .= " and produk.level='MPM' and produk.kd_mkr='G'";
                } elseif(strtoupper(trim($request->get('level_produk'))) == 'TUBE') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='I'";
                } elseif(strtoupper(trim($request->get('level_produk'))) == 'OLI') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='J'";
                }
            }

            if(!empty($request->get('kode_produk')) && $request->get('kode_produk') != '') {
                $sql .= " and produk.kd_produk='".strtoupper(trim($request->get('kode_produk')))."'";
            }

            $sql .= " )	produk
                    left join
                    (
                        select	target_jual.companyid, target_jual.kd_produk, sum(isnull(target_jual.target, 0)) as target
                        from	target_jual with (nolock)
                                    left join produk with (nolock) on target_jual.kd_produk=produk.kd_produk
                                    left join salesman with (nolock) on target_jual.kd_sales=salesman.kd_sales and
                                                target_jual.companyid=salesman.companyid
                        where	target_jual.tahun='".$request->get('year')."' and target_jual.bulan='".$request->get('month')."' and
                                target_jual.companyid='".strtoupper(trim($request->get('companyid')))."'";

            if(!empty($request->get('kode_mkr')) && $request->get('kode_mkr') != '') {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    $sql .= " and target_jual.kd_sales='".strtoupper(trim($request->get('kode_mkr')))."'";
                } elseif(strtoupper(trim($request->get('jenis_mkr'))) == 'SUPERVISOR') {
                    $sql .= " and salesman.spv='".strtoupper(trim($request->get('kode_mkr')))."'";
                }
            }

            if(!empty($request->get('level_produk')) && $request->get('level_produk') != '') {
                if(strtoupper(trim($request->get('level_produk'))) == 'HANDLE') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='G'";
                } elseif(strtoupper(trim($request->get('level_produk'))) == 'NON_HANDLE') {
                    $sql .= " and produk.level='MPM' and produk.kd_mkr='G'";
                } elseif(strtoupper(trim($request->get('level_produk'))) == 'TUBE') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='I'";
                } elseif(strtoupper(trim($request->get('level_produk'))) == 'OLI') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='J'";
                }
            }

            if(!empty($request->get('kode_produk')) && $request->get('kode_produk') != '') {
                $sql .= " and target_jual.kd_produk='".strtoupper(trim($request->get('kode_produk')))."'";
            }

            $sql .= " group by target_jual.companyid, target_jual.kd_produk
                    )	target_jual on produk.kd_produk=target_jual.kd_produk and produk.companyid=target_jual.companyid
                    left join
                    (
                        select	faktur.companyid, produk.kd_produk,
                                sum(iif(isnull(faktur_total.faktur_total, 0) <= 0,
                                    isnull(faktur.total, 0),
                                    isnull(faktur.total, 0) -
                                        round(((isnull(faktur.DiscRp, 0) / isnull(faktur_total.faktur_total, 0))), 0) -
                                        round(((isnull(faktur.DiscRp1, 0) / isnull(faktur_total.faktur_total, 0))), 0)
                                )) as faktur
                        from
                        (
                            select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur, faktur.kd_sales,
                                    faktur.spv, fakt_dtl.kd_part, faktur.discrp, faktur.discrp1,
                                    isnull(fakt_dtl.jumlah, 0) -
                                        round((isnull(fakt_dtl.jumlah, 0) * isnull(faktur.disc2, 0)) / 100, 0) as 'total'
                            from
                            (
                                select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur, faktur.kd_sales,
                                        faktur.disc2, faktur.discrp, faktur.discrp1, salesman.spv
                                from	faktur with (nolock)
                                            left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and
                                                    faktur.companyid=salesman.companyid
                                where	faktur.companyid='".strtoupper(trim($request->get('companyid')))."' and
                                        year(faktur.tgl_faktur)='".$request->get('year')."' and month(faktur.tgl_faktur)='".$request->get('month')."'";

            if(!empty($request->get('kode_mkr')) && $request->get('kode_mkr') != '') {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    $sql .= " and faktur.kd_sales='".strtoupper(trim($request->get('kode_mkr')))."'";
                } elseif(strtoupper(trim($request->get('jenis_mkr'))) == 'SUPERVISOR') {
                    $sql .= " and salesman.spv='".strtoupper(trim($request->get('kode_mkr')))."'";
                }
            }

            $sql .= " )	faktur
                                    left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                            faktur.companyid=fakt_dtl.companyid
                            where	isnull(fakt_dtl.jml_jual, 0) > 0
                        )	faktur
                        left join
                        (
                            select	faktur.companyid, faktur.no_faktur, count(fakt_dtl.no_faktur) as faktur_total
                            from
                            (
                                select	faktur.companyid, faktur.no_faktur, salesman.kd_sales
                                from	faktur with (nolock)
                                            left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and
                                                    faktur.companyid=salesman.companyid
                                where	faktur.companyid='".strtoupper(trim($request->get('companyid')))."' and
                                        (isnull(faktur.discrp, 0) > 0 or isnull(faktur.discrp1, 0) > 0) and
                                        year(faktur.tgl_faktur)='".$request->get('year')."' and month(faktur.tgl_faktur)='".$request->get('month')."'";

            if(!empty($request->get('kode_mkr')) && $request->get('kode_mkr') != '') {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    $sql .= " and faktur.kd_sales='".strtoupper(trim($request->get('kode_mkr')))."'";
                } elseif(strtoupper(trim($request->get('jenis_mkr'))) == 'SUPERVISOR') {
                    $sql .= " and salesman.spv='".strtoupper(trim($request->get('kode_mkr')))."'";
                }
            }

            $sql .= " )	faktur
                                    left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                            faktur.companyid=fakt_dtl.companyid
                            where	isnull(fakt_dtl.jml_jual, 0) > 0
                            group by faktur.companyid, faktur.no_faktur
                        )	faktur_total on faktur.no_faktur=faktur_total.no_faktur and faktur.companyid=faktur_total.companyid
                                left join part with (nolock) on faktur.kd_part=part.kd_part and faktur.companyid=part.companyid
                                left join sub with (nolock) on part.kd_sub=sub.kd_sub
                                left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                    where   faktur.companyid is not null";

            if(!empty($request->get('level_produk')) && $request->get('level_produk') != '') {
                if(strtoupper(trim($request->get('level_produk'))) == 'HANDLE') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='G'";
                } elseif(strtoupper(trim($request->get('level_produk'))) == 'NON_HANDLE') {
                    $sql .= " and produk.level='MPM' and produk.kd_mkr='G'";
                } elseif(strtoupper(trim($request->get('level_produk'))) == 'TUBE') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='I'";
                } elseif(strtoupper(trim($request->get('level_produk'))) == 'OLI') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='J'";
                }
            }

            if(!empty($request->get('kode_produk')) && trim($request->get('kode_produk') != '')) {
                $sql .= " and produk.kd_produk='".trim($request->get('kode_produk'))."'";
            }

            $sql .= " group by faktur.companyid, produk.kd_produk
                    )	faktur on produk.kd_produk=faktur.kd_produk and produk.companyid=faktur.companyid
                    left join
                    (
                        select	rtoko.companyid, produk.kd_produk,
                                sum(isnull(rtoko_dtl.jumlah, 0) * isnull(part.hrg_pokok, 0)) as retur
                        from
                        (
                            select	rtoko.companyid, rtoko.no_retur, rtoko.tanggal, salesman.kd_sales
                            from	rtoko with (nolock)
                                        left join salesman with (nolock) on rtoko.kd_sales=salesman.kd_sales and
                                                    rtoko.companyid=salesman.companyid
                            where	rtoko.companyid='".strtoupper(trim($request->get('companyid')))."' and
                                    year(rtoko.tanggal)='".$request->get('year')."' and month(rtoko.tanggal)='".$request->get('month')."'";

            if(!empty($request->get('kode_mkr')) && $request->get('kode_mkr') != '') {
                if(strtoupper(trim($request->get('jenis_mkr'))) == 'SALESMAN') {
                    $sql .= " and rtoko.kd_sales='".strtoupper(trim($request->get('kode_mkr')))."'";
                } elseif(strtoupper(trim($request->get('jenis_mkr'))) == 'SUPERVISOR') {
                    $sql .= " and salesman.spv='".strtoupper(trim($request->get('kode_mkr')))."'";
                }
            }

            $sql .= " )	rtoko
                                inner join rtoko_dtl with (nolock) on rtoko.no_retur=rtoko_dtl.no_retur and
                                        rtoko.companyid=rtoko_dtl.companyid
                                left join part with (nolock) on rtoko_dtl.kd_part=part.kd_part and
                                        rtoko.companyid=part.companyid
                                left join sub with (nolock) on part.kd_sub=sub.kd_sub
                                left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                        where	isnull(rtoko_dtl.jumlah, 0) > 0";

            if(!empty($request->get('level_produk')) && $request->get('level_produk') != '') {
                if(strtoupper(trim($request->get('level_produk'))) == 'HANDLE') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='G'";
                } elseif(strtoupper(trim($request->get('level_produk'))) == 'NON_HANDLE') {
                    $sql .= " and produk.level='MPM' and produk.kd_mkr='G'";
                } elseif(strtoupper(trim($request->get('level_produk'))) == 'TUBE') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='I'";
                } elseif(strtoupper(trim($request->get('level_produk'))) == 'OLI') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='J'";
                }
            }

            if(!empty($request->get('kode_produk')) && trim($request->get('kode_produk') != '')) {
                $sql .= " and produk.kd_produk='".trim($request->get('kode_produk'))."'";
            }

            $sql .= " group by rtoko.companyid, produk.kd_produk
                    )	rtoko on produk.kd_produk=rtoko.kd_produk and produk.companyid=rtoko.companyid
                    order by produk.kd_mkr asc, produk.nourut asc";

            $result = DB::select($sql);
            $data_dashboard = [];

            foreach($result as $data) {
                $data_dashboard[] = [
                    'produk'        => strtoupper(trim($data->produk)),
                    'pencapaian'    => (double)$data->pencapaian,
                    'target'        => (double)$data->target,
                    'prosentase'    => (double)$data->prosentase
                ];
            }

            return Response::responseSuccess('success', $data_dashboard);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
