<?php

namespace App\Http\Controllers\Api\Backend\Dashboard;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;


class ApiDashboardDealerController extends Controller
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

            $sql = "select	top 5 isnull(faktur.companyid, '') as companyid,
                            isnull(faktur.no_faktur, '') as nomor_faktur,
                            isnull(faktur.tgl_faktur, '') as tanggal_faktur,
                            isnull(faktur.tgl_jtp_spelling, '') as tanggal_jtp_spelling,
                            isnull(faktur.tgl_jtp, '') as tanggal_jtp,
                            isnull(faktur.total, 0) as total_faktur,
                            isnull(faktur.jml_bayar, 0) as total_pembayaran,
                            isnull(faktur.total, 0) - isnull(faktur.jml_bayar, 0) as sisa_pembayaran,
                            datediff(day, getdate(), faktur.tgl_jtp) as sisa_hari
                    from
                    (
                        select	faktur_total.companyid, faktur_total.no_faktur, faktur.tgl_faktur,
                                faktur_total.total, faktur_total.jml_bayar,
                                dateadd(day, isnull(dealer.jtp, 0),
                                    dateadd(day, isnull(faktur.jtp_khusus, 0),
                                        dateadd(day, isnull(faktur.umur_faktur, 0),
                                            cast(iif(isnull(faktur.tgl_sj, '')='',
                                                dateadd(day, 4, faktur.tgl_faktur),
                                                    faktur.tgl_sj) as date)))) as tgl_jtp_spelling,
                                dateadd(day, isnull(dealer.jtp, 0),
                                    dateadd(day, isnull(faktur.umur_faktur, 0),
                                        cast(iif(isnull(faktur.tgl_sj, '')='',
                                            dateadd(day, 4, faktur.tgl_faktur),
                                                faktur.tgl_sj) as date))) as tgl_jtp
                        from
                        (
                            select	faktur.companyid, faktur.no_faktur, faktur.total,
                                    sum(isnull(terimadtl.jumlah, 0)) as jml_bayar
                            from	faktur with (nolock)
                                        left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and faktur.companyid=salesman.companyid
                                        left join superspv with (nolock) on salesman.spv=superspv.kd_spv and faktur.companyid=superspv.companyid
                                        left join terimadtl with (nolock) on faktur.no_faktur=terimadtl.no_faktur and
                                                    faktur.companyid=terimadtl.companyid
                            where	faktur.companyid='".$request->get('companyid')."'";

            if(strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                $sql .= " and faktur.kd_dealer='".$request->get('user_id')."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql .= " and faktur.kd_sales='".$request->get('user_id')."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql .= " and superspv.nm_spv='".$request->get('user_id')."'";
            }

            if($request->get('kode_dealer') != '' || !empty($request->get('kode_dealer'))) {
                $sql .= " and faktur.kd_dealer='".$request->get('kode_dealer')."'";
            }

            $sql .= " group by faktur.companyid, faktur.no_faktur, faktur.total
                            having	isnull(faktur.total, 0) > sum(isnull(terimadtl.jumlah, 0))
                        )	faktur_total
                                left join faktur with (nolock) on faktur_total.no_faktur=faktur.no_faktur and
                                            faktur_total.companyid=faktur.companyid
                                left join dealer with (nolock) on faktur.kd_dealer=dealer.kd_dealer and
                                            faktur_total.companyid=dealer.companyid
                    )	faktur
                    order by faktur.tgl_jtp asc";

            $result_pembayaran = DB::select($sql);

            $sql = "select	isnull(company.companyid, '') as companyid, isnull(piutang.sisa_piutang, 0) as sisa_piutang,
                            isnull(campaign.poin_campaign, 0) as poin_campaign, isnull(limit_piutang.sisa_limit_piutang, 0) as sisa_limit_piutang,
                            isnull(limit_piutang.status_limit, 0) as status_limit_piutang, isnull(faktur_total.omset_penjualan, 0) as omset_penjualan,
                            isnull(faktur.total_order, 0) as total_order, isnull(faktur.total_jual, 0) as total_jual,
                            isnull(faktur.total_order_process, 0) as total_order_process, isnull(bo_total.total_bo_pcs, 0) as total_bo_pcs,
                            isnull(bo_item.total_item_bo, 0) as total_bo_item
                    from
                    (
                        select	company.companyid
                        from	company with (nolock)
                        where	company.companyid='".$request->get('companyid')."'
                    )	company
                    left join
                    (
                        select	faktur.companyid, sum(isnull(faktur.total, 0)) - sum(isnull(faktur.jml_bayar, 0)) as sisa_piutang
                        from
                        (
                            select	faktur.companyid, faktur.no_faktur, faktur.total, sum(isnull(terimadtl.jumlah, 0)) as jml_bayar,
                                    sum(faktur.total) - sum(isnull(terimadtl.jumlah, 0)) as piutang_pembayaran
                            from	faktur with (nolock)
                                        left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and
                                                    faktur.companyid=salesman.companyid
                                        left join superspv with (nolock) on salesman.spv=superspv.kd_spv and
                                                    faktur.companyid=superspv.companyid
                                        left join terimadtl with (nolock) on faktur.no_faktur=terimadtl.no_faktur and
                                                    faktur.companyid=terimadtl.companyid
                            where	faktur.companyid='".$request->get('companyid')."'";

            if(strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                $sql .= " and faktur.kd_dealer='".$request->get('user_id')."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql .= " and faktur.kd_sales='".$request->get('user_id')."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql .= " and superspv.nm_spv='".$request->get('user_id')."'";
            }

            if($request->get('kode_dealer') != '' || !empty($request->get('kode_dealer'))) {
                $sql .= " and faktur.kd_dealer='".$request->get('kode_dealer')."'";
            }


            $sql .= " group by faktur.companyid, faktur.no_faktur, faktur.total
                            having isnull(faktur.total, 0) > sum(isnull(terimadtl.jumlah, 0))
                        )	faktur
                        group by faktur.companyid
                    )	piutang on company.companyid=piutang.companyid
                    left join
                    (
                        select	camp.companyid, sum(isnull(camp_dtl.point, 0) * isnull(fakt_dtl.jml_jual, 0)) as 'poin_campaign'
                        from
                        (
                            select	camp.companyid, camp.no_camp, camp.tgl_prd1, camp.tgl_prd2
                            from	camp with (nolock)
                            where	camp.companyid='".$request->get('companyid')."' and camp.tgl_prd2 >= convert(varchar(10), getdate(), 120)
                        )	camp
                                inner join camp_dtl with (nolock) on camp.no_camp=camp_dtl.no_camp and
                                            camp.companyid=camp_dtl.companyid
                                inner join fakt_dtl with (nolock) on camp_dtl.kd_part=fakt_dtl.kd_part and
                                            camp.companyid=fakt_dtl.companyid
                                inner join faktur with (nolock) on fakt_dtl.no_faktur=faktur.no_faktur and
                                            camp.companyid=faktur.companyid and
                                            faktur.tgl_faktur between camp.tgl_prd1 and camp.tgl_prd2
                                left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and
                                            faktur.companyid=salesman.companyid
                                left join superspv with (nolock) on salesman.spv=superspv.kd_spv and
                                            faktur.companyid=superspv.companyid
                        where isnull(fakt_dtl.jml_jual, 0) > 0";

            if(strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                $sql .= " and faktur.kd_dealer='".$request->get('user_id')."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql .= " and faktur.kd_sales='".$request->get('user_id')."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql .= " and superspv.nm_spv='".$request->get('user_id')."'";
            }

            if($request->get('kode_dealer') != '' || !empty($request->get('kode_dealer'))) {
                $sql .= " and faktur.kd_dealer='".$request->get('kode_dealer')."'";
            }

            $sql .= " group by camp.companyid
                    )	campaign on company.companyid=campaign.companyid
                    left join
                    (
                        select	dealer.companyid, max(dealer.status_limit) as status_limit, sum(dealer.sisa_limit_piutang) as sisa_limit_piutang
                        from
                        (
                            select	dealer.companyid,
                                    case
                                        when isnull(dealer.limit_piut, 0)=1 Or isnull(dealer.limit_sales, 0)=1 then 'BLACK_LIST'
                                        when isnull(dealer.limit_piut, 0)=0 and isnull(dealer.limit_sales, 0)=0 then 'SISA_LIMIT_PIUTANG'
                                        when isnull(dealer.limit_piut, 0) <> 0 Or isnull(dealer.limit_sales, 0) <> 0 then 'SISA_LIMIT_PIUTANG'
                                        else 'SISA_LIMIT_SALES'
                                    end as status_limit,
                                    isnull(dealer.limit_piut, 0) - (isnull(dealer.s_awal_b, 0) + isnull(dealer.jual_14, 0) + isnull(dealer.jual_20, 0) - isnull(dealer.extra, 0) +
                                    isnull(dealer.da, 0) - isnull(dealer.ca, 0) - isnull(dealer.insentif, 0) - isnull(dealer.t_bayar_b, 0)) as sisa_limit_piutang
                            from	dealer with (nolock)
                                        left join salesman with (nolock) on dealer.kd_sales=salesman.kd_sales and dealer.companyid=salesman.companyid
                                        left join superspv with (nolock) on salesman.spv=superspv.kd_spv and dealer.companyid=superspv.companyid
                            where	dealer.companyid='".$request->get('companyid')."'";

            if(strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                $sql .= " and dealer.kd_dealer='".$request->get('user_id')."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql .= " and dealer.kd_sales='".$request->get('user_id')."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql .= " and superspv.nm_spv='".$request->get('user_id')."'";
            }

            if($request->get('kode_dealer') != '' || !empty($request->get('kode_dealer'))) {
                $sql .= " and dealer.kd_dealer='".$request->get('kode_dealer')."'";
            }

            $sql .= " )	dealer
                        group by dealer.companyid
                    )	limit_piutang on company.companyid=limit_piutang.companyid
                    left join
                    (
                        select	faktur.companyid, sum(faktur.total) as omset_penjualan
                        from	faktur with (nolock)
                                    left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and
                                                faktur.companyid=salesman.companyid
                                    left join superspv with (nolock) on salesman.spv=superspv.kd_spv and
                                                faktur.companyid=superspv.companyid
                        where	faktur.companyid='".$request->get('companyid')."' and
                                year(faktur.tgl_faktur)='".$request->get('year')."' and month(faktur.tgl_faktur)='".$request->get('month')."'";

            if(strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                $sql .= " and faktur.kd_dealer='".$request->get('user_id')."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql .= " and faktur.kd_sales='".$request->get('user_id')."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql .= " and superspv.nm_spv='".$request->get('user_id')."'";
            }

            if($request->get('kode_dealer') != '' || !empty($request->get('kode_dealer'))) {
                $sql .= " and faktur.kd_dealer='".$request->get('kode_dealer')."'";
            }

            $sql .= " group by faktur.companyid
                    )	faktur_total on company.companyid=faktur_total.companyid
                    left join
                    (
                        select	faktur.companyid, sum(isnull(fakt_dtl.jml_order, 0)) as total_order,
                                sum(isnull(fakt_dtl.jml_jual, 0)) as total_jual,
                                sum(iif(isnull(faktur.sts_ctk, 0)=1, fakt_dtl.jml_jual, 0)) as total_order_process
                        from
                        (
                            select	faktur.companyid, faktur.no_faktur, faktur.sts_ctk
                            from	faktur with (nolock)
                                        left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and
                                                    faktur.companyid=salesman.companyid
                                        left join superspv with (nolock) on salesman.spv=superspv.kd_spv and
                                                    faktur.companyid=superspv.companyid
                            where	faktur.companyid='".$request->get('companyid')."' and
                                    year(faktur.tgl_faktur)='".$request->get('year')."' and month(faktur.tgl_faktur)='".$request->get('month')."'";

            if(strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                $sql .= " and faktur.kd_dealer='".$request->get('user_id')."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql .= " and faktur.kd_sales='".$request->get('user_id')."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql .= " and superspv.nm_spv='".$request->get('user_id')."'";
            }

            if($request->get('kode_dealer') != '' || !empty($request->get('kode_dealer'))) {
                $sql .= " and faktur.kd_dealer='".$request->get('kode_dealer')."'";
            }

            $sql .= " )	faktur
                                left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                            faktur.companyid=fakt_dtl.companyid
                        group by faktur.companyid
                    )	faktur on company.companyid=faktur.companyid
                    left join
                    (
                        select	bo.companyid, sum(isnull(bo.jumlah, 0)) as total_bo_pcs
                        from	bo with (nolock)
                                    left join salesman with (nolock) on bo.kd_sales=salesman.kd_sales and
                                                bo.companyid=salesman.companyid
                                    left join superspv with (nolock) on salesman.spv=superspv.kd_spv and
                                                bo.companyid=superspv.companyid
                        where	bo.companyid='".$request->get('companyid')."'";

            if(strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                $sql .= " and bo.kd_dealer='".$request->get('user_id')."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql .= " and bo.kd_sales='".$request->get('user_id')."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql .= " and superspv.nm_spv='".$request->get('user_id')."'";
            }

            if($request->get('kode_dealer') != '' || !empty($request->get('kode_dealer'))) {
                $sql .= " and bo.kd_dealer='".$request->get('kode_dealer')."'";
            }

            $sql .= " group by bo.companyid
                    )	bo_total on company.companyid=bo_total.companyid
                    left join
                    (
                        select	bo.companyid, count(bo.kd_part) as total_item_bo
                        from
                        (
                            select	bo.companyid, bo.kd_part
                            from	bo with (nolock)
                                        left join salesman with (nolock) on bo.kd_sales=salesman.kd_sales and
                                                    bo.companyid=salesman.companyid
                                        left join superspv with (nolock) on salesman.spv=superspv.kd_spv and
                                                    bo.companyid=superspv.companyid
                            where	bo.companyid='".$request->get('companyid')."'";

            if(strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                $sql .= " and bo.kd_dealer='".$request->get('user_id')."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql .= " and bo.kd_sales='".$request->get('user_id')."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql .= " and superspv.nm_spv='".$request->get('user_id')."'";
            }

            if($request->get('kode_dealer') != '' || !empty($request->get('kode_dealer'))) {
                $sql .= " and bo.kd_dealer='".$request->get('kode_dealer')."'";
            }

            $sql .= " group by bo.companyid, bo.kd_part
                        )	bo
                        group by bo.companyid
                    )	bo_item on company.companyid=bo_total.companyid";

            $result_dashboard = DB::select($sql);
            $data_dasboard = [];

            foreach($result_dashboard as $data) {
                $data_dasboard[] = [
                    'companyid'         => $data->companyid,
                    'sisa_piutang'      => (double)$data->sisa_piutang,
                    'poin_campaign'     => (double)$data->poin_campaign,
                    'sisa_limit_piutang' => (double)$data->sisa_limit_piutang,
                    'status_limit_piutang' => (double)$data->status_limit_piutang,
                    'order_on_process'  => (double)$data->total_order_process,
                    'omset_penjualan'   => (double)$data->omset_penjualan,
                    'order'             => (double)$data->total_order,
                    'terlayani'         => (double)$data->total_jual,
                    'bo_pcs'            => (double)$data->total_bo_pcs,
                    'bo_item'           => (double)$data->total_bo_item,
                    'pembayaran_piutang' => $result_pembayaran
                ];
            }
            return Response::responseSuccess('success', collect($data_dasboard)->first());
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
