<?php

namespace App\Http\Controllers\Api\Backend\Dashboard\Management;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class ApiDashboardManagementSalesController extends Controller
{
    public function dashboardSalesByProduct(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'year'      => 'required|string',
                'month'     => 'required|string',
                'fields'    => 'required|string',
                'user_id'   => 'required|string',
                'role_id'   => 'required|string',
                'companyid' => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning('Pilih bulan, tahun, dan fields terlebih dahulu');
            }

            $tahun_dipilih = (int)date('Y');
            $bulan_dipilih = (int)date('m');

            if (!empty($request->get('year'))) {
                $tahun_dipilih = (int)$request->get('year');
            }
            if (!empty($request->get('month'))) {
                $bulan_dipilih = (int)$request->get('month');
            }

            $tahun_lalu = (int)$tahun_dipilih;
            $bulan_lalu = (int)$bulan_dipilih;

            $table_tahunan = 'omsetsales'.$tahun_dipilih;
            $cost_price_bulanan = 'faktur_costprice'.$bulan_dipilih;
            $amount_bulanan = 'faktur_amount'.$bulan_dipilih;
            $quantity_bulanan = 'faktur_qty'.$bulan_dipilih;

            $table_tahunan_lalu = 'omsetsales'.$tahun_lalu;
            $cost_price_bulanan_lalu = 'faktur_costprice'.$bulan_lalu;
            $amount_bulanan_lalu = 'faktur_amount'.$bulan_lalu;
            $quantity_bulanan_lalu = 'faktur_qty'.$bulan_lalu;

            if ((int)$bulan_dipilih == 1) {
                $tahun_lalu = (int)$tahun_dipilih - 1;
                $bulan_lalu = 12;

                $table_tahunan_lalu = 'omsetsales'.$tahun_lalu;
                $cost_price_bulanan_lalu = 'faktur_costprice'.$bulan_lalu;
                $amount_bulanan_lalu = 'faktur_amount'.$bulan_lalu;
                $quantity_bulanan_lalu = 'faktur_qty'.$bulan_lalu;
            } else {
                $bulan_lalu = (int)$bulan_dipilih - 1;

                $cost_price_bulanan_lalu = 'faktur_costprice'.$bulan_lalu;
                $amount_bulanan_lalu = 'faktur_amount'.$bulan_lalu;
                $quantity_bulanan_lalu = 'faktur_qty'.$bulan_lalu;
            }

            $status_proses_history = 0;
            $company_pusat = '';
            $tanggal_clossing = date('Y-m-d');
            $tahun_berjalan = date('Y');
            $bulan_berjalan = date('m');
            $akhir_bulan = date('Y-m-d');

            $sql = DB::table('company')->lock('with (nolock)')
                ->selectRaw("isnull(company.companyid, '') as companyid,
                            isnull(stsclose.close_mkr, '') as clossing_marketing,
                            year(dateadd(day, 1, stsclose.close_mkr)) as tahun_berjalan,
                            month(dateadd(day, 1, stsclose.close_mkr)) as bulan_berjalan,
                            eomonth(dateadd(day, 1, stsclose.close_mkr)) akhir_bulan")
                ->leftJoin(DB::raw('stsclose with (nolock)'), function($join) {
                        $join->on('stsclose.companyid', '=', 'company.companyid');
                    })
                ->whereRaw("isnull(company.inisial, 0) = 1")
                ->first();

            if (empty($sql->companyid)) {
                return Response::responseWarning("Inisial company pusat masih belum disetting, hubungi IT programmer");
            } else {
                $company_pusat = strtoupper(trim($sql->companyid));
                $tanggal_clossing = $sql->clossing_marketing;
                $tahun_berjalan = $sql->tahun_berjalan;
                $bulan_berjalan = $sql->bulan_berjalan;
                $akhir_bulan = $sql->akhir_bulan;
            }

            if(strtotime($tanggal_clossing) > strtotime($tahun_dipilih.'-'.substr('0'.$bulan_dipilih, -2).'-'.'01')) {
                $status_proses_history = 1;
            } else {
                if(strtotime($akhir_bulan) <= strtotime($tahun_dipilih.'-'.substr('0'.$bulan_dipilih, -2).'-'.'01')) {
                    return Response::responseWarning('Data transaksi masih berjalan pada bulan '.$bulan_berjalan.' '.$tahun_berjalan);
                }
            }

            $sql = "select	isnull(produk.nourut, 0) as nomor_urut, isnull(produk.kd_produk, '') as kode_produk,
                            isnull(produk.nama, '') as nama_produk, isnull(produk.kd_mkr, '') as kode_mkr,
                            isnull(produk.level, '') as level, isnull(produk.keterangan_level, '') as keterangan_level,
                            cast(sum(isnull(faktur.pusat_selling_price_ex_ppn, 0)) as decimal(18,0)) as pusat_sell_price_ex_ppn,
                            cast(sum(isnull(faktur.pusat_selling_price_in_ppn, 0)) as decimal(18,0)) as pusat_sell_price_in_ppn,
                            cast(sum(isnull(faktur_lalu.pusat_selling_price_ex_ppn, 0)) as decimal(18,0)) as pusat_sell_price_ex_ppn_lalu,
                            cast(sum(isnull(faktur_lalu.pusat_selling_price_in_ppn, 0)) as decimal(18,0)) as pusat_sell_price_in_ppn_lalu,
                            cast(sum(isnull(faktur.pusat_cost_price, 0)) as decimal(18,0)) as pusat_cost_price,
                            cast(sum(isnull(faktur_lalu.pusat_cost_price, 0)) as decimal(18,0)) as pusat_cost_price_lalu,
                            cast(sum(isnull(faktur.pusat_quantity, 0)) as decimal(18,0)) as pusat_quantity,
                            cast(sum(isnull(faktur_lalu.pusat_quantity, 0)) as decimal(18,0)) as pusat_quantity_lalu,
                            cast(sum(isnull(faktur.pc_selling_price_ex_ppn, 0)) as decimal(18,0)) as pc_sell_price_ex_ppn,
                            cast(sum(isnull(faktur.pc_selling_price_in_ppn, 0)) as decimal(18,0)) as pc_sell_price_in_ppn,
                            cast(sum(isnull(faktur_lalu.pc_selling_price_ex_ppn, 0)) as decimal(18,0)) as pc_sell_price_ex_ppn_lalu,
                            cast(sum(isnull(faktur_lalu.pc_selling_price_in_ppn, 0)) as decimal(18,0)) as pc_sell_price_in_ppn_lalu,
                            cast(sum(isnull(faktur.pc_cost_price, 0)) as decimal(18,0)) as pc_cost_price,
                            cast(sum(isnull(faktur_lalu.pc_cost_price, 0)) as decimal(18,0)) as pc_cost_price_lalu,
                            cast(sum(isnull(faktur.pc_quantity, 0)) as decimal(18,0)) as pc_quantity,
                            cast(sum(isnull(faktur_lalu.pc_quantity, 0)) as decimal(18,0)) as pc_quantity_lalu ,
                            cast(sum(isnull(faktur.online_selling_price_ex_ppn, 0)) as decimal(18,0)) as online_sell_price_ex_ppn,
                            cast(sum(isnull(faktur.online_selling_price_in_ppn, 0)) as decimal(18,0)) as online_sell_price_in_ppn,
                            cast(sum(isnull(faktur_lalu.online_selling_price_ex_ppn, 0)) as decimal(18,0)) as online_sell_price_ex_ppn_lalu,
                            cast(sum(isnull(faktur_lalu.online_selling_price_in_ppn, 0)) as decimal(18,0)) as online_sell_price_in_ppn_lalu,
                            cast(sum(isnull(faktur.online_cost_price, 0)) as decimal(18,0)) as online_cost_price,
                            cast(sum(isnull(faktur_lalu.online_cost_price, 0)) as decimal(18,0)) as online_cost_price_lalu,
                            cast(sum(isnull(faktur.online_quantity, 0)) as decimal(18,0)) as online_quantity,
                            cast(sum(isnull(faktur_lalu.online_quantity, 0)) as decimal(18,0)) as online_quantity_lalu
                    from
                    (
                        select	'Report' as report, produk.kd_produk, produk.nama, produk.kd_mkr, produk.nourut,
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

            if (strtoupper(trim($request->get('level'))) == 'HANDLE') {
                $sql .= " and produk.level='AHM' and produk.kd_mkr='G'";
            } elseif (strtoupper(trim($request->get('level'))) == 'NON_HANDLE') {
                $sql .= " and produk.level='MPM' and produk.kd_mkr='G'";
            } elseif (strtoupper(trim($request->get('level'))) == 'TUBE') {
                $sql .= " and produk.level='AHM' and produk.kd_mkr='I'";
            } elseif (strtoupper(trim($request->get('level'))) == 'OLI') {
                $sql .= " and produk.level='AHM' and produk.kd_mkr='J'";
            }

            if(!empty($request->get('produk')) && trim($request->get('produk')) != '') {
                $sql .= " and produk.kd_produk='".$request->get('produk')."'";
            }

            $sql .= " )	produk
                    left join
                    (";

            if((int)$status_proses_history == 0) {
                $sql .= " select    faktur.kd_produk,
                                    sum(iif(faktur.status = 'PUSAT', isnull(faktur.quantity, 0), 0)) as pusat_quantity,
                                    sum(iif(faktur.status = 'PUSAT', isnull(faktur.selling_price_ex_ppn, 0), 0)) as pusat_selling_price_ex_ppn,
                                    sum(iif(faktur.status = 'PUSAT', isnull(faktur.selling_price_in_ppn, 0), 0)) as pusat_selling_price_in_ppn,
                                    sum(iif(faktur.status = 'PUSAT', isnull(faktur.cost_price, 0), 0)) as pusat_cost_price,
                                    sum(iif(faktur.status = 'PC', isnull(faktur.quantity, 0), 0)) as pc_quantity,
                                    sum(iif(faktur.status = 'PC', isnull(faktur.selling_price_ex_ppn, 0), 0)) as pc_selling_price_ex_ppn,
                                    sum(iif(faktur.status = 'PC', isnull(faktur.selling_price_in_ppn, 0), 0)) as pc_selling_price_in_ppn,
                                    sum(iif(faktur.status = 'PC', isnull(faktur.cost_price, 0), 0)) as pc_cost_price,
                                    sum(iif(faktur.status = 'ONLINE', isnull(faktur.quantity, 0), 0)) as online_quantity,
                                    sum(iif(faktur.status = 'ONLINE', isnull(faktur.selling_price_ex_ppn, 0), 0)) as online_selling_price_ex_ppn,
                                    sum(iif(faktur.status = 'ONLINE', isnull(faktur.selling_price_in_ppn, 0), 0)) as online_selling_price_in_ppn,
                                    sum(iif(faktur.status = 'ONLINE', isnull(faktur.cost_price, 0), 0)) as online_cost_price
                        from
                        (
                            select	faktur.status, produk.kd_produk,";

                if(strtoupper(trim($request->get('fields'))) == 'SELLING_PRICE_IN_PPN') {
                    $sql .= "sum(isnull(faktur.cost_price, 0) + round(((isnull(faktur.cost_price, 0) * isnull(faktur.ppn, 0)) / 100), 0)) as cost_price,";
                } else {
                    $sql .= "sum(isnull(faktur.cost_price, 0)) as cost_price,";
                }

                $sql .= " sum(isnull(faktur.jml_jual, 0)) as quantity,
                                    sum(round((isnull(faktur.total, 0) -
                                        round(iif(isnull(faktur.discrp, 0) <= 0, 0, isnull(faktur.discrp, 0) / isnull(faktur_total.total_faktur, 0)), 0) -
                                        round(iif(isnull(faktur.discrp1, 0) <= 0, 0, isnull(faktur.discrp1, 0) / isnull(faktur_total.total_faktur, 0)), 0))
                                    / ((100 + isnull(faktur.ppn, 0)) / convert(decimal(5, 2), 100)), 0)) as selling_price_ex_ppn,
                                    sum(isnull(faktur.total, 0) -
                                        round(iif(isnull(faktur.discrp, 0) <= 0, 0, isnull(faktur.discrp, 0) / isnull(faktur_total.total_faktur, 0)), 0) -
                                        round(iif(isnull(faktur.discrp1, 0) <= 0, 0, isnull(faktur.discrp1, 0) / isnull(faktur_total.total_faktur, 0)), 0)) as selling_price_in_ppn
                            from
                            (
                                select	faktur.companyid, faktur.no_faktur, faktur.discrp, faktur.discrp1, fakt_dtl.kd_part, fakt_dtl.kd_lokasi,
                                        isnull(fakt_dtl.jumlah, 0) as jumlah, company.ppn, fakt_dtl.jml_jual,
                                        isnull(fakt_dtl.hrg_pokok, 0) * isnull(fakt_dtl.jml_jual, 0) as cost_price,
                                        isnull(fakt_dtl.jumlah, 0) - round((isnull(fakt_dtl.jumlah, 0) * isnull(faktur.disc2, 0)) / 100, 0) as total,
                                        iif(isnull(company.inisial, 0)=1,
                                            iif(ltrim(rtrim(company.kd_faktur)) = ltrim(rtrim(lokasi.kd_faktur)), 'PUSAT', 'ONLINE'),
                                        'PC') as status
                                from
                                (
                                    select	faktur.companyid, faktur.no_faktur, faktur.disc2, faktur.discrp, faktur.discrp1, faktur.total
                                    from	faktur with (nolock)
                                    where	year(faktur.tgl_faktur)='".$tahun_dipilih."' and
                                            month(faktur.tgl_faktur)='".$bulan_dipilih."'
                                )	faktur
                                        left join company with (nolock) on faktur.companyid=company.companyid
                                        left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                                    faktur.companyid=fakt_dtl.companyid
                                        left join lokasi with (nolock) on fakt_dtl.kd_lokasi=lokasi.kd_lokasi and
                                            faktur.companyid=lokasi.companyid
                                where isnull(fakt_dtl.jml_jual, 0) > 0
                            )	faktur
                            left join
                            (
                                select	faktur.companyid, faktur.no_faktur, count(fakt_dtl.no_faktur) as 'total_faktur'
                                from
                                (
                                    select	faktur.companyid, faktur.no_faktur
                                    from	faktur with (nolock)
                                    where	year(faktur.tgl_faktur)='".$tahun_dipilih."' and
                                            month(faktur.tgl_faktur)='".$bulan_dipilih."' and
                                            (isnull(faktur.discrp, 0) > 0 or isnull(faktur.discrp1, 0) > 0)
                                )	faktur
                                        left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                                    faktur.companyid=fakt_dtl.companyid
                                where isnull(fakt_dtl.jml_jual, 0) > 0
                                group by faktur.companyid, faktur.no_faktur
                            )	faktur_total on faktur.no_faktur=faktur_total.no_faktur and faktur.companyid=faktur_total.companyid
                                    inner join company with (nolock) on faktur.companyid=company.companyid
                                    left join part with (nolock) on faktur.kd_part=part.kd_part and '".$company_pusat."'=part.companyid
                                    left join sub with (nolock) on part.kd_sub=sub.kd_sub
                                    left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                            where   faktur.companyid is not null";

                if (strtoupper(trim($request->get('level'))) == 'HANDLE') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='G'";
                } elseif (strtoupper(trim($request->get('level'))) == 'NON_HANDLE') {
                    $sql .= " and produk.level='MPM' and produk.kd_mkr='G'";
                } elseif (strtoupper(trim($request->get('level'))) == 'TUBE') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='I'";
                } elseif (strtoupper(trim($request->get('level'))) == 'OLI') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='J'";
                }

                if(!empty($request->get('produk')) && trim($request->get('produk')) != '') {
                    $sql .= " and produk.kd_produk='".$request->get('produk')."'";
                }

                $sql .= " group by faktur.status, produk.kd_produk
                    )   faktur
                    group by faktur.kd_produk";
            } else {
                $sql .= " select    faktur.kd_produk,
                                    sum(iif(faktur.status = 'PUSAT', isnull(faktur.quantity, 0), 0)) as pusat_quantity,
                                    sum(iif(faktur.status = 'PUSAT', isnull(faktur.selling_price_ex_ppn, 0), 0)) as pusat_selling_price_ex_ppn,
                                    sum(iif(faktur.status = 'PUSAT', isnull(faktur.selling_price_in_ppn, 0), 0)) as pusat_selling_price_in_ppn,
                                    sum(iif(faktur.status = 'PUSAT', isnull(faktur.cost_price, 0), 0)) as pusat_cost_price,
                                    sum(iif(faktur.status = 'PC', isnull(faktur.quantity, 0), 0)) as pc_quantity,
                                    sum(iif(faktur.status = 'PC', isnull(faktur.selling_price_ex_ppn, 0), 0)) as pc_selling_price_ex_ppn,
                                    sum(iif(faktur.status = 'PC', isnull(faktur.selling_price_in_ppn, 0), 0)) as pc_selling_price_in_ppn,
                                    sum(iif(faktur.status = 'PC', isnull(faktur.cost_price, 0), 0)) as pc_cost_price,
                                    sum(iif(faktur.status = 'ONLINE', isnull(faktur.quantity, 0), 0)) as online_quantity,
                                    sum(iif(faktur.status = 'ONLINE', isnull(faktur.selling_price_ex_ppn, 0), 0)) as online_selling_price_ex_ppn,
                                    sum(iif(faktur.status = 'ONLINE', isnull(faktur.selling_price_in_ppn, 0), 0)) as online_selling_price_in_ppn,
                                    sum(iif(faktur.status = 'ONLINE', isnull(faktur.cost_price, 0), 0)) as online_cost_price
                        from
                        (
                            select    iif(isnull(company.inisial, 0)=1,
                                        iif(ltrim(rtrim(company.kd_faktur)) = ltrim(rtrim(lokasi.kd_faktur)), 'PUSAT', 'ONLINE'),
                                    'PC') as status,
                                    produk.kd_produk, sum(isnull(".$table_tahunan.".".$amount_bulanan.", 0)) as selling_price_in_ppn,
                                    sum(round(isnull(".$table_tahunan.".".$amount_bulanan.", 0) / ((100 + isnull(company.ppn, 0)) / convert(decimal(5, 2), 100)), 0)) as selling_price_ex_ppn,
                                    sum(isnull(".$table_tahunan.".".$quantity_bulanan.", 0)) as quantity,";

                if(strtoupper(trim($request->get('fields'))) == 'SELLING_PRICE_IN_PPN') {
                    $sql .= "sum(isnull(".$table_tahunan.".".$cost_price_bulanan.", 0) + round(((isnull(".$table_tahunan.".".$cost_price_bulanan.", 0) * isnull(company.ppn, 0)) / 100), 0)) as cost_price ";
                } else {
                    $sql .= "sum(isnull(".$table_tahunan.".".$cost_price_bulanan.", 0)) as cost_price ";
                }

                $sql .= " from	".$table_tahunan." with (nolock)
                                        inner join company with (nolock) on ".$table_tahunan.".companyid=company.companyid
                                        left join lokasi with (nolock) on ".$table_tahunan.".kd_lokasi=lokasi.kd_lokasi and
                                                    ".$table_tahunan.".companyid=lokasi.companyid
                                        left join salesman with (nolock) on ".$table_tahunan.".kd_sales=salesman.kd_sales and
                                                    ".$table_tahunan.".companyid=salesman.companyid
                                        left join superspv with (nolock) on salesman.spv=superspv.kd_spv and
                                                    ".$table_tahunan.".companyid=superspv.companyid
                                        left join part with (nolock) on ".$table_tahunan.".kd_part=part.kd_part and
                                                    '".$company_pusat."'=part.companyid
                                        left join sub with (nolock) on part.kd_sub=sub.kd_sub
                                        left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                            where   ".$table_tahunan.".companyid is not null";

                if (strtoupper(trim($request->get('level'))) == 'HANDLE') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='G'";
                } elseif (strtoupper(trim($request->get('level'))) == 'NON_HANDLE') {
                    $sql .= " and produk.level='MPM' and produk.kd_mkr='G'";
                } elseif (strtoupper(trim($request->get('level'))) == 'TUBE') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='I'";
                } elseif (strtoupper(trim($request->get('level'))) == 'OLI') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='J'";
                }

                if(!empty($request->get('produk')) && trim($request->get('produk')) != '') {
                    $sql .= " and produk.kd_produk='".$request->get('produk')."'";
                }

                $sql .= " group by iif(isnull(company.inisial, 0)=1,
                                    iif(ltrim(rtrim(company.kd_faktur)) = ltrim(rtrim(lokasi.kd_faktur)), 'PUSAT', 'ONLINE'),
                                'PC'), produk.kd_produk
                    )   faktur
                    group by faktur.kd_produk";
            }

            $sql .= " )	faktur on produk.kd_produk=faktur.kd_produk
                    left join
                    (
                        select  faktur.kd_produk,
                                sum(iif(faktur.status = 'PUSAT', isnull(faktur.quantity, 0), 0)) as pusat_quantity,
                                sum(iif(faktur.status = 'PUSAT', isnull(faktur.selling_price_ex_ppn, 0), 0)) as pusat_selling_price_ex_ppn,
                                sum(iif(faktur.status = 'PUSAT', isnull(faktur.selling_price_in_ppn, 0), 0)) as pusat_selling_price_in_ppn,
                                sum(iif(faktur.status = 'PUSAT', isnull(faktur.cost_price, 0), 0)) as pusat_cost_price,
                                sum(iif(faktur.status = 'PC', isnull(faktur.quantity, 0), 0)) as pc_quantity,
                                sum(iif(faktur.status = 'PC', isnull(faktur.selling_price_ex_ppn, 0), 0)) as pc_selling_price_ex_ppn,
                                sum(iif(faktur.status = 'PC', isnull(faktur.selling_price_in_ppn, 0), 0)) as pc_selling_price_in_ppn,
                                sum(iif(faktur.status = 'PC', isnull(faktur.cost_price, 0), 0)) as pc_cost_price,
                                sum(iif(faktur.status = 'ONLINE', isnull(faktur.quantity, 0), 0)) as online_quantity,
                                sum(iif(faktur.status = 'ONLINE', isnull(faktur.selling_price_ex_ppn, 0), 0)) as online_selling_price_ex_ppn,
                                sum(iif(faktur.status = 'ONLINE', isnull(faktur.selling_price_in_ppn, 0), 0)) as online_selling_price_in_ppn,
                                sum(iif(faktur.status = 'ONLINE', isnull(faktur.cost_price, 0), 0)) as online_cost_price
                        from
                        (
                            select	iif(isnull(company.inisial, 0)=1,
                                        iif(ltrim(rtrim(company.kd_faktur)) = ltrim(rtrim(lokasi.kd_faktur)), 'PUSAT', 'ONLINE'),
                                    'PC') as status,
                                    produk.kd_produk, sum(isnull(".$table_tahunan_lalu.".".$amount_bulanan_lalu.", 0)) as selling_price_in_ppn,
                                    sum(round(isnull(".$table_tahunan_lalu.".".$amount_bulanan_lalu.", 0) / ((100 + isnull(company.ppn, 0)) / convert(decimal(5, 2), 100)), 0)) as selling_price_ex_ppn,
                                    sum(isnull(".$table_tahunan_lalu.".".$quantity_bulanan_lalu.", 0)) as quantity,";

            if(strtoupper(trim($request->get('fields'))) == 'SELLING_PRICE_IN_PPN') {
                $sql .= "sum(isnull(".$table_tahunan_lalu.".".$cost_price_bulanan_lalu.", 0) + round(((isnull(".$table_tahunan_lalu.".".$cost_price_bulanan_lalu.", 0) * isnull(company.ppn, 0)) / 100), 0)) as cost_price ";
            } else {
                $sql .= "sum(isnull(".$table_tahunan_lalu.".".$cost_price_bulanan_lalu.", 0)) as cost_price ";
            }

            $sql .= " from	".$table_tahunan_lalu." with (nolock)
                                        inner join company with (nolock) on ".$table_tahunan_lalu.".companyid=company.companyid
                                        left join lokasi with (nolock) on ".$table_tahunan_lalu.".kd_lokasi=lokasi.kd_lokasi and
                                                    ".$table_tahunan_lalu.".companyid=lokasi.companyid
                                        left join salesman with (nolock) on ".$table_tahunan_lalu.".kd_sales=salesman.kd_sales and
                                                    ".$table_tahunan_lalu.".companyid=salesman.companyid
                                        left join superspv with (nolock) on salesman.spv=superspv.kd_spv and
                                                    ".$table_tahunan_lalu.".companyid=superspv.companyid
                                        left join part with (nolock) on ".$table_tahunan_lalu.".kd_part=part.kd_part and
                                                    '".$company_pusat."'=part.companyid
                                        left join sub with (nolock) on part.kd_sub=sub.kd_sub
                                        left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                            where   ".$table_tahunan_lalu.".companyid is not null";

            if (strtoupper(trim($request->get('level'))) == 'HANDLE') {
                $sql .= " and produk.level='AHM' and produk.kd_mkr='G'";
            } elseif (strtoupper(trim($request->get('level'))) == 'NON_HANDLE') {
                $sql .= " and produk.level='MPM' and produk.kd_mkr='G'";
            } elseif (strtoupper(trim($request->get('level'))) == 'TUBE') {
                $sql .= " and produk.level='AHM' and produk.kd_mkr='I'";
            } elseif (strtoupper(trim($request->get('level'))) == 'OLI') {
                $sql .= " and produk.level='AHM' and produk.kd_mkr='J'";
            }

            if(!empty($request->get('produk')) && trim($request->get('produk')) != '') {
                $sql .= " and produk.kd_produk='".$request->get('produk')."'";
            }

            $sql .= " group by iif(isnull(company.inisial, 0)=1,
                                    iif(ltrim(rtrim(company.kd_faktur)) = ltrim(rtrim(lokasi.kd_faktur)), 'PUSAT', 'ONLINE'),
                                'PC'), produk.kd_produk
                        )   faktur
                        group by    faktur.kd_produk
                    )	faktur_lalu on produk.kd_produk=faktur_lalu.kd_produk
                    group by produk.nourut, produk.kd_produk, produk.nama, produk.kd_mkr, produk.level, produk.keterangan_level
                    order by produk.kd_mkr asc, produk.keterangan_level asc, produk.nourut asc";

            $result = DB::select($sql);

            $sales_by_product = [];
            $best_sales_product_pusat = [];
            $best_sales_product_pc = [];
            $best_sales_product_online = [];

            $selling_pusat = 0;
            $selling_pc = 0;
            $selling_online = 0;

            $selling_pusat_total = 0;
            $selling_pc_total = 0;
            $selling_online_total = 0;

            $selling_pusat_total_lalu = 0;
            $selling_pc_total_lalu = 0;
            $selling_online_total_lalu = 0;

            $cost_price_pusat = 0;
            $cost_price_pc = 0;
            $cost_price_online = 0;
            $cost_price_total = 0;

            $cost_price_pusat_lalu = 0;
            $cost_price_pc_lalu = 0;
            $cost_price_online_lalu = 0;

            $selling_price_pusat = 0;
            $selling_price_pc = 0;
            $selling_price_online = 0;

            $selling_price_pusat_lalu = 0;
            $selling_price_pc_lalu = 0;
            $selling_price_online_lalu = 0;

            foreach ($result as $data) {
                $selling_pusat = 0;
                $selling_pc = 0;
                $selling_online = 0;

                if (strtoupper(trim($request->get('fields'))) == 'SELLING_PRICE_EX_PPN') {
                    $selling_price_pusat = (float)$selling_price_pusat + (float)$data->pusat_sell_price_ex_ppn;
                    $selling_price_pc = (float)$selling_price_pc + (float)$data->pc_sell_price_ex_ppn;
                    $selling_price_online = (float)$selling_price_online + (float)$data->online_sell_price_ex_ppn;

                    $selling_price_pusat_lalu = (float)$selling_price_pusat_lalu + (float)$data->pusat_sell_price_ex_ppn_lalu;
                    $selling_price_pc_lalu = (float)$selling_price_pc_lalu + (float)$data->pc_sell_price_ex_ppn_lalu;
                    $selling_price_online_lalu = (float)$selling_price_online_lalu + (float)$data->online_sell_price_ex_ppn_lalu;

                    $selling_pusat = (float)$data->pusat_sell_price_ex_ppn;
                    $selling_pc = (float)$data->pc_sell_price_ex_ppn;
                    $selling_online = (float)$data->online_sell_price_ex_ppn;

                    $selling_pusat_total = (float)$selling_pusat_total + (float)$data->pusat_sell_price_ex_ppn;
                    $selling_pc_total = (float)$selling_pc_total + (float)$data->pc_sell_price_ex_ppn;
                    $selling_online_total = (float)$selling_online_total + (float)$data->online_sell_price_ex_ppn;

                    $selling_pusat_total_lalu = (float)$selling_pusat_total_lalu + (float)$data->pusat_sell_price_ex_ppn_lalu;
                    $selling_pc_total_lalu = (float)$selling_pc_total_lalu + (float)$data->pc_sell_price_ex_ppn_lalu;
                    $selling_online_total_lalu = (float)$selling_online_total_lalu + (float)$data->online_sell_price_ex_ppn_lalu;
                } elseif (strtoupper(trim($request->get('fields'))) == 'SELLING_PRICE_IN_PPN') {
                    $selling_price_pusat = (float)$selling_price_pusat + (float)$data->pusat_sell_price_in_ppn;
                    $selling_price_pc = (float)$selling_price_pc + (float)$data->pc_sell_price_in_ppn;
                    $selling_price_online = (float)$selling_price_online + (float)$data->online_sell_price_in_ppn;

                    $selling_price_pusat_lalu = (float)$selling_price_pusat_lalu + (float)$data->pusat_sell_price_in_ppn_lalu;
                    $selling_price_pc_lalu = (float)$selling_price_pc_lalu + (float)$data->pc_sell_price_in_ppn_lalu;
                    $selling_price_online_lalu = (float)$selling_price_online_lalu + (float)$data->online_sell_price_in_ppn_lalu;

                    $selling_pusat = (float)$data->pusat_sell_price_in_ppn;
                    $selling_pc = (float)$data->pc_sell_price_in_ppn;
                    $selling_online = (float)$data->online_sell_price_in_ppn;

                    $selling_pusat_total = (float)$selling_pusat_total + (float)$data->pusat_sell_price_in_ppn;
                    $selling_pc_total = (float)$selling_pc_total + (float)$data->pc_sell_price_in_ppn;
                    $selling_online_total = (float)$selling_online_total + (float)$data->online_sell_price_in_ppn;

                    $selling_pusat_total_lalu = (float)$selling_pusat_total_lalu + (float)$data->pusat_sell_price_in_ppn_lalu;
                    $selling_pc_total_lalu = (float)$selling_pc_total_lalu + (float)$data->pc_sell_price_in_ppn_lalu;
                    $selling_online_total_lalu = (float)$selling_online_total_lalu + (float)$data->online_sell_price_in_ppn_lalu;
                } elseif (strtoupper(trim($request->get('fields'))) == 'COST_PRICE') {
                    $selling_price_pusat = (float)$selling_price_pusat + (float)$data->pusat_sell_price_ex_ppn;
                    $selling_price_pc = (float)$selling_price_pc + (float)$data->pc_sell_price_ex_ppn;
                    $selling_price_online = (float)$selling_price_online + (float)$data->online_sell_price_ex_ppn;

                    $selling_price_pusat_lalu = (float)$selling_price_pusat_lalu + (float)$data->pusat_sell_price_ex_ppn_lalu;
                    $selling_price_pc_lalu = (float)$selling_price_pc_lalu + (float)$data->pc_sell_price_ex_ppn_lalu;
                    $selling_price_online_lalu = (float)$selling_price_online_lalu + (float)$data->online_sell_price_ex_ppn_lalu;

                    $selling_pusat = (float)$data->pusat_cost_price;
                    $selling_pc = (float)$data->pc_cost_price;
                    $selling_online = (float)$data->online_cost_price;

                    $selling_pusat_total = (float)$selling_pusat_total + (float)$data->pusat_cost_price;
                    $selling_pc_total = (float)$selling_pc_total + (float)$data->pc_cost_price;
                    $selling_online_total = (float)$selling_online_total + (float)$data->online_cost_price;

                    $selling_pusat_total_lalu = (float)$selling_pusat_total_lalu + (float)$data->pusat_cost_price_lalu;
                    $selling_pc_total_lalu = (float)$selling_pc_total_lalu + (float)$data->pc_cost_price_lalu;
                    $selling_online_total_lalu = (float)$selling_online_total_lalu + (float)$data->online_cost_price_lalu;
                } else {
                    $selling_pusat = (float)$data->pusat_quantity;
                    $selling_pc = (float)$data->pc_quantity;
                    $selling_online = (float)$data->online_quantity;

                    $selling_pusat_total = (float)$selling_pusat_total + (float)$data->pusat_quantity;
                    $selling_pc_total = (float)$selling_pc_total + (float)$data->pc_quantity;
                    $selling_online_total = (float)$selling_online_total + (float)$data->online_quantity;

                    $selling_pusat_total_lalu = (float)$selling_pusat_total_lalu + (float)$data->pusat_quantity_lalu;
                    $selling_pc_total_lalu = (float)$selling_pc_total_lalu + (float)$data->pc_quantity_lalu;
                    $selling_online_total_lalu = (float)$selling_online_total_lalu + (float)$data->online_quantity_lalu;
                }

                $cost_price_pusat = (float)$cost_price_pusat + (float)$data->pusat_cost_price;
                $cost_price_pc = (float)$cost_price_pc + (float)$data->pc_cost_price;
                $cost_price_online = (float)$cost_price_online + (float)$data->online_cost_price;

                $cost_price_pusat_lalu = (float)$cost_price_pusat_lalu + (float)$data->pusat_cost_price_lalu;
                $cost_price_pc_lalu = (float)$cost_price_pc_lalu + (float)$data->pc_cost_price_lalu;
                $cost_price_online_lalu = (float)$cost_price_online_lalu + (float)$data->online_cost_price_lalu;

                $sales_by_product[] = [
                    'kode'      => strtoupper(trim($data->kode_produk)),
                    'produk'    => strtoupper(trim($data->nama_produk)),
                    'pusat'     => (float)$selling_pusat,
                    'pc'        => (float)$selling_pc,
                    'online'    => (float)$selling_online,
                ];

                $best_sales_product_pusat[] = [
                    'produk'    => strtoupper(trim($data->kode_produk)),
                    'total'     => (float)$selling_pusat
                ];

                $best_sales_product_pc[] = [
                    'produk'    => strtoupper(trim($data->kode_produk)),
                    'total'     => (float)$selling_pc
                ];

                $best_sales_product_online[] = [
                    'produk'    => strtoupper(trim($data->kode_produk)),
                    'total'     => (float)$selling_online
                ];
            }

            $cost_price_total = (float)$cost_price_pusat + (float)$cost_price_pc + (float)$cost_price_online;

            $selling_total = (float)$selling_pusat_total + (float)$selling_pc_total + (float)$selling_online_total;
            $selling_total_lalu = (float)$selling_pusat_total_lalu + (float)$selling_pc_total_lalu + (float)$selling_online_total_lalu;

            $margin_pusat = (strtoupper(trim($request->get('fields'))) == 'QUANTITY') ? 0 : (float)$selling_price_pusat - (float)$cost_price_pusat;
            $margin_pc = (strtoupper(trim($request->get('fields'))) == 'QUANTITY') ? 0 : (float)$selling_price_pc - (float)$cost_price_pc;
            $margin_online = (strtoupper(trim($request->get('fields'))) == 'QUANTITY') ? 0 : (float)$selling_price_online - (float)$cost_price_online;
            $margin_total = (float)$margin_pusat + (float)$margin_pc + (float)$margin_online;

            $margin_pusat_lalu = (strtoupper(trim($request->get('fields'))) == 'QUANTITY') ? 0 : (float)$selling_price_pusat_lalu - (float)$cost_price_pusat_lalu;
            $margin_pc_lalu = (strtoupper(trim($request->get('fields'))) == 'QUANTITY') ? 0 : (float)$selling_price_pc_lalu - (float)$cost_price_pc_lalu;
            $margin_online_lalu = (strtoupper(trim($request->get('fields'))) == 'QUANTITY') ? 0 : (float)$selling_price_online_lalu - (float)$cost_price_online_lalu;
            $margin_total_lalu = (float)$margin_pusat_lalu + (float)$margin_pc_lalu + (float)$margin_online_lalu;

            $margin_prosentase_pusat = ((float)$cost_price_pusat <= 0) ? 0 : ($margin_pusat / (float)$cost_price_pusat) * 100;
            $margin_prosentase_pc = ((float)$cost_price_pc <= 0) ? 0 : ($margin_pc / (float)$cost_price_pc) * 100;
            $margin_prosentase_online = ((float)$cost_price_online <= 0) ? 0 : ($margin_online / (float)$cost_price_online) * 100;
            $margin_prosentase_total = ((float)$cost_price_total <= 0) ? 0 : ($margin_total / (float)$cost_price_total) * 100;


            $data_compare_pusat = [
                'company'           => 'Pusat',
                'total_sekarang'    => (float)$selling_pusat_total,
                'total_lalu'        => (float)$selling_pusat_total_lalu,
            ];
            $data_compare_pc = [
                'company'           => 'Part Center',
                'total_sekarang'    => (float)$selling_pc_total,
                'total_lalu'        => (float)$selling_pc_total_lalu,
            ];
            $data_compare_online = [
                'company'           => 'Online',
                'total_sekarang'    => (float)$selling_online_total,
                'total_lalu'        => (float)$selling_online_total_lalu,
            ];

            $data_gross_profit_pusat = [
                'company'           => 'Pusat',
                'margin'            => (float)$margin_pusat,
                'margin_prosentase' => (float)$margin_prosentase_pusat,
                'status'            => ((float)$margin_pusat > $margin_pusat_lalu) ? 'NAIK' : (((float)$margin_pusat < $margin_pusat_lalu) ? 'TURUN' : 'BERTAHAN'),
                'status_prosentase' => ((float)$margin_pusat_lalu <= 0) ? 0 : (((float)$margin_pusat - (float)$margin_pusat_lalu) / (float)$margin_pusat_lalu) * 100
            ];
            $data_gross_profit_pc = [
                'company'           => 'Part Center',
                'margin'            => (float)$margin_pc,
                'margin_prosentase' => (float)$margin_prosentase_pc,
                'status'            => ((float)$margin_pc > $margin_pc_lalu) ? 'NAIK' : (((float)$margin_pc < $margin_pc_lalu) ? 'TURUN' : 'BERTAHAN'),
                'status_prosentase' => ((float)$margin_pc_lalu <= 0) ? 0 : (((float)$margin_pc - (float)$margin_pc_lalu) / (float)$margin_pc_lalu) * 100
            ];
            $data_gross_profit_online = [
                'company'           => 'Online',
                'margin'            => (float)$margin_online,
                'margin_prosentase' => (float)$margin_prosentase_online,
                'status'            => ((float)$margin_online > $margin_online_lalu) ? 'NAIK' : (((float)$margin_online < $margin_online_lalu) ? 'TURUN' : 'BERTAHAN'),
                'status_prosentase' => ((float)$margin_online_lalu <= 0) ? 0 : (((float)$margin_online - (float)$margin_online_lalu) / (float)$margin_online_lalu) * 100
            ];

            $data_selling_pusat = [
                'company'       => 'Pusat',
                'selling'        => (strtoupper(trim($request->get('fields'))) == 'QUANTITY') ? 'Quantity' : 'Selling Price',
                'total'         => (float)$selling_pusat_total,
                'status'        => ((float)$selling_pusat_total > $selling_pusat_total_lalu) ? 'NAIK' : (((float)$selling_pusat_total < $selling_pusat_total_lalu) ? 'TURUN' : 'BERTAHAN'),
                'status_prosentase' => ((float)$selling_pusat_total_lalu <= 0) ? 0 : (((float)$selling_pusat_total - (float)$selling_pusat_total_lalu) / (float)$selling_pusat_total_lalu) * 100
            ];
            $data_selling_pc = [
                'company'       => 'Part Center',
                'selling'        => (strtoupper(trim($request->get('fields'))) == 'QUANTITY') ? 'Quantity' : 'Selling Price',
                'total'         => (float)$selling_pc_total,
                'status'        => ((float)$selling_pc_total > $selling_pc_total_lalu) ? 'NAIK' : (((float)$selling_pc_total < $selling_pc_total_lalu) ? 'TURUN' : 'BERTAHAN'),
                'status_prosentase' => ((float)$selling_pc_total_lalu <= 0) ? 0 : (((float)$selling_pc_total - (float)$selling_pc_total_lalu) / (float)$selling_pc_total_lalu) * 100
            ];
            $data_selling_online = [
                'company'       => 'Online',
                'selling'        => (strtoupper(trim($request->get('fields'))) == 'QUANTITY') ? 'Quantity' : 'Selling Price',
                'total'         => (float)$selling_online_total,
                'status'        => ((float)$selling_online_total > $selling_online_total_lalu) ? 'NAIK' : (((float)$selling_online_total < $selling_online_total_lalu) ? 'TURUN' : 'BERTAHAN'),
                'status_prosentase' => ((float)$selling_online_total_lalu <= 0) ? 0 : (((float)$selling_online_total - (float)$selling_online_total_lalu) / (float)$selling_online_total_lalu) * 100
            ];

            $gross_profit = [$data_gross_profit_pusat, $data_gross_profit_pc, $data_gross_profit_online];
            $comparison = [$data_compare_pusat, $data_compare_pc, $data_compare_online];
            $selling = [$data_selling_pusat, $data_selling_pc, $data_selling_online];

            $data_selling_total = [
                'selling_total'                   => (float)$selling_total,
                'selling_total_status'            => ((float)$selling_total > (float)$selling_total_lalu) ? 'NAIK' : (((float)$selling_total < $selling_total_lalu) ? 'TURUN' : 'BERTAHAN'),
                'selling_total_status_prosentase' => ((float)$selling_total_lalu <= 0) ? 0 : (((float)$selling_total - (float)$selling_total_lalu) / (float)$selling_total_lalu) * 100,
                'selling_detail'                  => $selling,
            ];
            $data_margin_total = [
                'margin_total'                      => (float)$margin_total,
                'margin_total_prosentase'           => (float)$margin_prosentase_total,
                'margin_total_status'               => ((float)$margin_total > (float)$margin_total_lalu) ? 'NAIK' : (((float)$margin_total < $margin_total_lalu) ? 'TURUN' : 'BERTAHAN'),
                'margin_total_status_prosentase'    => ((float)$margin_total_lalu <= 0) ? 0 : (((float)$margin_total - (float)$margin_total_lalu) / (float)$margin_total_lalu) * 100,
                'margin_detail'                     => $gross_profit,
            ];
            $data_best_sales_product = [
                'pusat'     => collect($best_sales_product_pusat)->take(4)->sortByDesc('total')->values()->all(),
                'pc'        => collect($best_sales_product_pc)->take(4)->sortByDesc('total')->values()->all(),
                'online'    => collect($best_sales_product_online)->take(4)->sortByDesc('total')->values()->all(),
            ];

            $data_dashboard = [
                'selling'        => $data_selling_total,
                'margin'        => $data_margin_total,
                'comparison'    => $comparison,
                'best_sales'    => $data_best_sales_product,
                'product'       => $sales_by_product,
            ];

            return Response::responseSuccess('success', $data_dashboard);
        } catch (\Exception $exception) {
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $exception->getMessage(),
                $request->get('companyid')
            );
        }
    }

    public function dashboardSalesByDate(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'year'      => 'required|string',
                'month'     => 'required|string',
                'fields'    => 'required|string',
                'user_id'   => 'required|string',
                'role_id'   => 'required|string',
                'companyid' => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Pilih bulan dan tahun terlebih dahulu");
            }

            $tahun_dipilih = (int)date('Y');
            $bulan_dipilih = (int)date('m');

            if (!empty($request->get('year'))) {
                $tahun_dipilih = (int)$request->get('year');
            }
            if (!empty($request->get('month'))) {
                $bulan_dipilih = (int)$request->get('month');
            }

            $company_pusat = '';

            $sql = DB::table('company')->lock('with (nolock)')
                ->select('companyid')
                ->where('inisial', 1)
                ->first();

            if (empty($sql->companyid)) {
                return Response::responseWarning("Inisial company pusat masih belum disetting, hubungi IT programmer");
            } else {
                $company_pusat = strtoupper(trim($sql->companyid));
            }

            $statusFilterProduk = false;

            if (strtoupper(trim($request->get('level'))) == 'HANDLE') {
                $statusFilterProduk = true;
            } elseif (strtoupper(trim($request->get('level'))) == 'NON_HANDLE') {
                $statusFilterProduk = true;
            } elseif (strtoupper(trim($request->get('level'))) == 'TUBE') {
                $statusFilterProduk = true;
            } elseif (strtoupper(trim($request->get('level'))) == 'OLI') {
                $statusFilterProduk = true;
            }
            if (!empty($request->get('produk')) || $request->get('produk') != '') {
                $statusFilterProduk = true;
            }

            if($statusFilterProduk == true) {
                $sql = "select	day(faktur.tgl_faktur) as tanggal,";

                if(strtoupper(trim($request->get('fields'))) == 'SELLING_PRICE_EX_PPN') {
                    $sql .= " cast(sum(iif(isnull(company.inisial, 0)=0, 0,
                            iif(company.kd_faktur=left(faktur.no_faktur, 2),
                                isnull(round(isnull(faktur.total, 0) / ((100 + isnull(company.ppn, 0)) / convert(decimal(5, 2), 100)), 0), 0), 0))) as decimal(18, 0)) as amount_pusat,
                        cast(sum(iif(isnull(company.inisial, 0)=0, 0,
                            iif(company.kd_faktur <> left(faktur.no_faktur, 2),
                                isnull(round(isnull(faktur.total, 0) / ((100 + isnull(company.ppn, 0)) / convert(decimal(5, 2), 100)), 0), 0), 0))) as decimal(18, 0)) as amount_online,
                        cast(sum(iif(isnull(company.inisial, 0)=1, 0,
                            isnull(round(isnull(faktur.total, 0) / ((100 + isnull(company.ppn, 0)) / convert(decimal(5, 2), 100)), 0), 0))) as decimal(18, 0)) as amount_pc";
                } elseif(strtoupper(trim($request->get('fields'))) == 'SELLING_PRICE_IN_PPN' || strtoupper(trim($request->get('fields'))) == 'COST_PRICE') {
                    $sql .= " cast(sum(iif(isnull(company.inisial, 0)=0, 0,
                            iif(company.kd_faktur=left(faktur.no_faktur, 2),
                                isnull(faktur.total, 0), 0))) as decimal(18, 0)) as amount_pusat,
                        cast(sum(iif(isnull(company.inisial, 0)=0, 0,
                            iif(company.kd_faktur <> left(faktur.no_faktur, 2),
                                isnull(faktur.total, 0), 0))) as decimal(18, 0)) as amount_online,
                        cast(sum(iif(isnull(company.inisial, 0)=1, 0,
                            isnull(faktur.total, 0))) as decimal(18, 0)) as amount_pc";
                } else {
                    $sql .= " cast(sum(iif(isnull(company.inisial, 0)=0, 0,
                            iif(company.kd_faktur=left(faktur.no_faktur, 2),
                                isnull(faktur.jml_jual, 0), 0))) as decimal(18, 0)) as amount_pusat,
                        cast(sum(iif(isnull(company.inisial, 0)=0, 0,
                            iif(company.kd_faktur <> left(faktur.no_faktur, 2),
                                isnull(faktur.jml_jual, 0), 0))) as decimal(18, 0)) as amount_online,
                        cast(sum(iif(isnull(company.inisial, 0)=1, 0,
                            isnull(faktur.jml_jual, 0))) as decimal(18, 0)) as amount_pc";
                }

                $sql .= " from
                        (
                            select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur, faktur.kd_part, faktur.jml_jual,";

                if(strtoupper(trim($request->get('fields'))) == 'COST_PRICE') {
                    $sql .= "isnull(faktur.hrg_pokok, 0) * isnull(faktur.jml_jual, 0) as total";
                } else {
                    $sql .= "isnull(faktur.total, 0) -
                            round(iif(isnull(faktur.discrp, 0) <= 0, 0,
                                ((isnull(faktur.discrp, 0) / isnull(faktur_total.total_faktur, 0)))), 0) -
                            round(iif(isnull(faktur.discrp1, 0) <= 0, 0,
                                ((isnull(faktur.discrp1, 0) / isnull(faktur_total.total_faktur, 0)))), 0) as total";
                }

                $sql .= " from
                            (
                                select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur, faktur.disc2,
                                        faktur.discrp, faktur.discrp1, fakt_dtl.kd_part, fakt_dtl.jml_jual,
                                        isnull(fakt_dtl.hrg_pokok, 0) as hrg_pokok, isnull(fakt_dtl.jumlah, 0) jumlah,
                                        isnull(fakt_dtl.jumlah, 0) -
                                            round((isnull(fakt_dtl.jumlah, 0) * isnull(faktur.disc2, 0)) / 100, 0) as total
                                from
                                (
                                    select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur,
                                            faktur.disc2, faktur.discrp, faktur.discrp1, faktur.total
                                    from	faktur with (nolock)
                                    where	year(faktur.tgl_faktur)='".$tahun_dipilih."' and
                                            month(faktur.tgl_faktur)='".$bulan_dipilih."'
                                )	faktur
                                        left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                                    faktur.companyid=fakt_dtl.companyid
                                where isnull(fakt_dtl.jml_jual, 0) > 0
                            )	faktur
                            left join
                            (
                                select	faktur.companyid, faktur.no_faktur,
                                        count(fakt_dtl.no_faktur) as total_faktur
                                from
                                (
                                    select	faktur.companyid, faktur.no_faktur
                                    from	faktur with (nolock)
                                    where	year(faktur.tgl_faktur)='".$tahun_dipilih."' and
                                            month(faktur.tgl_faktur)='".$bulan_dipilih."' and
                                            (isnull(faktur.discrp, 0) > 0 or isnull(faktur.discrp1, 0) > 0)
                                )	faktur
                                        left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                                    faktur.companyid=fakt_dtl.companyid
                                where isnull(fakt_dtl.jml_jual, 0) > 0
                                group by faktur.companyid, faktur.no_faktur
                            )	faktur_total on faktur.no_faktur=faktur_total.no_faktur and faktur.companyid=faktur_total.companyid
                        )	faktur
                                left join company with (nolock) on faktur.companyid=company.companyid
                                left join part with (nolock) on faktur.kd_part=part.kd_part and '".$company_pusat."'=part.companyid
                                left join sub with (nolock) on part.kd_sub=sub.kd_sub
                                left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                        where   faktur.companyid is not null";

                if (!empty($request->get('produk')) || $request->get('produk') != '') {
                    $sql .= " and produk.kd_produk='" . $request->get('produk') . "'";
                }

                if (strtoupper(trim($request->get('level'))) == 'HANDLE') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='G'";
                } elseif (strtoupper(trim($request->get('level'))) == 'NON_HANDLE') {
                    $sql .= " and produk.level='MPM' and produk.kd_mkr='G'";
                } elseif (strtoupper(trim($request->get('level'))) == 'TUBE') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='I'";
                } elseif (strtoupper(trim($request->get('level'))) == 'OLI') {
                    $sql .= " and produk.level='AHM' and produk.kd_mkr='J'";
                }

                $sql .= " group by day(faktur.tgl_faktur)
                        order by day(faktur.tgl_faktur) asc";
            } else {
                if(strtoupper(trim($request->get('fields'))) == 'QUANTITY') {
                    $sql = "select	day(faktur.tgl_faktur) as 'tanggal',
                                    sum(iif(isnull(company.inisial, 0)=0, 0,
                                        iif(company.kd_faktur=left(faktur.no_faktur, 2), isnull(fakt_dtl.jml_jual, 0), 0))) as amount_pusat,
                                    sum(iif(isnull(company.inisial, 0)=0, 0,
                                        iif(company.kd_faktur <> left(faktur.no_faktur, 2), isnull(fakt_dtl.jml_jual, 0), 0))) as amount_online,
                                    sum(iif(isnull(company.inisial, 0)=1, 0, isnull(fakt_dtl.jml_jual, 0))) as amount_pc
                            from
                            (
                                select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur
                                from	faktur with (nolock)
                                where	year(faktur.tgl_faktur)='".$tahun_dipilih."' and month(faktur.tgl_faktur)='".$bulan_dipilih."'
                            )	faktur
                                    inner join company with (nolock) on faktur.companyid=company.companyid
                                    left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                                faktur.companyid=fakt_dtl.companyid
                            where isnull(fakt_dtl.jml_jual, 0) > 0
                            group by day(faktur.tgl_faktur)
                            order by day(faktur.tgl_faktur) asc";
                } elseif(strtoupper(trim($request->get('fields'))) == 'COST_PRICE') {
                    $sql = "select	day(faktur.tgl_faktur) as tanggal,
                                    sum(iif(isnull(company.inisial, 0)=0,
                                        isnull(fakt_dtl.jml_jual, 0) * isnull(fakt_dtl.hrg_pokok, 0), 0)
                                    ) as amount_pc,
                                    sum(iif(isnull(company.inisial, 0)=1,
                                            iif(left(faktur.no_faktur, 2)=company.kd_faktur, isnull(fakt_dtl.jml_jual, 0) * isnull(fakt_dtl.hrg_pokok, 0), 0)
                                    , 0))   as amount_pusat,
                                    sum(iif(isnull(company.inisial, 0)=1,
                                            iif(left(faktur.no_faktur, 2) <> company.kd_faktur, isnull(fakt_dtl.jml_jual, 0) * isnull(fakt_dtl.hrg_pokok, 0), 0)
                                    , 0))   as amount_online
                            from
                            (
                                select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur
                                from	faktur with (nolock)
                                where	year(faktur.tgl_faktur)='".$tahun_dipilih."' and month(faktur.tgl_faktur)='".$bulan_dipilih."'
                            )	faktur
                                    inner join company with (nolock) on faktur.companyid=company.companyid
                                    left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                                faktur.companyid=fakt_dtl.companyid
                            group by day(faktur.tgl_faktur)
                            order by day(faktur.tgl_faktur) asc";
                } else {
                    $sql = "select	day(faktur.tgl_faktur) as tanggal,";

                    if(strtoupper(trim($request->get('fields'))) == 'SELLING_PRICE_EX_PPN') {
                        $sql .= "sum(iif(isnull(company.inisial, 0)=1,
                                    iif(left(faktur.no_faktur, 2)=company.kd_faktur,
                                        isnull(round(isnull(faktur.total, 0) / ((100 + isnull(company.ppn, 0)) / convert(decimal(5, 2), 100)), 0), 0), 0), 0)) as amount_pusat,
                            sum(iif(isnull(company.inisial, 0)=1,
                                    iif(left(faktur.no_faktur, 2) <> company.kd_faktur,
                                        isnull(round(isnull(faktur.total, 0) / ((100 + isnull(company.ppn, 0)) / convert(decimal(5, 2), 100)), 0), 0), 0), 0)) as amount_online,
                            sum(iif(isnull(company.inisial, 0)=0,
                                isnull(round(isnull(faktur.total, 0) / ((100 + isnull(company.ppn, 0)) / convert(decimal(5, 2), 100)), 0), 0), 0)) as amount_pc";
                    } elseif(strtoupper(trim($request->get('fields'))) == 'SELLING_PRICE_IN_PPN') {
                        $sql .= "sum(iif(isnull(company.inisial, 0)=1,
                                    iif(left(faktur.no_faktur, 2)=company.kd_faktur, faktur.total, 0), 0)) as amount_pusat,
                            sum(iif(isnull(company.inisial, 0)=1,
                                    iif(left(faktur.no_faktur, 2) <> company.kd_faktur, faktur.total, 0), 0)) as amount_online,
                            sum(iif(isnull(company.inisial, 0)=0, faktur.total, 0)) as amount_pc";
                    }

                    $sql .= " from	faktur with (nolock)
                                        left join company with (nolock) on faktur.companyid=company.companyid
                            where	year(faktur.tgl_faktur)='".$tahun_dipilih."' and
                                    month(faktur.tgl_faktur)='".$bulan_dipilih."'
                            group by day(faktur.tgl_faktur)
                            order by day(faktur.tgl_faktur) asc";
                }
            }

            $result = DB::select($sql);

            $data_tanggal = [];

            foreach ($result as $data) {
                $data_tanggal[] = [
                    'tanggal'   => (float)$data->tanggal,
                    'pusat'     => (float)$data->amount_pusat,
                    'pc'        => (float)$data->amount_pc,
                    'online'    => (float)$data->amount_online,
                ];
            }

            return Response::responseSuccess('success', $data_tanggal);
        } catch (\Exception $exception) {
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $exception->getMessage(),
                $request->get('companyid')
            );
        }
    }
}
