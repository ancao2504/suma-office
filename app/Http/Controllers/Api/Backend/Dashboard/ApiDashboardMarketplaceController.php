<?php

namespace App\Http\Controllers\Api\Backend\Dashboard;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;

class ApiDashboardMarketplaceController extends Controller
{
    private $kode_marketplace = "('OB','OK','OL','OP','OS','OT')";

    public function salesByLocation(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'year'      => 'required|string',
                'month'     => 'required|string',
                'companyid' => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning('Pilih bulan dan tahun terlebih dahulu');
            }

            $tahun_dipilih = (int)date('Y');
            $bulan_dipilih = (int)date('m');

            $tahun_lalu = (int)date('Y');
            $bulan_lalu = (int)date('m');

            if (!empty($request->get('year'))) {
                $tahun_dipilih = (int)$request->get('year');
            }
            if (!empty($request->get('month'))) {
                $bulan_dipilih = (int)$request->get('month');
            }

            if((int)$bulan_dipilih == 1) {
                $bulan_lalu = 12;
                $tahun_lalu = (int)$tahun_dipilih - 1;
            } else {
                $bulan_lalu = (int)$bulan_dipilih - 1;
                $tahun_lalu = (int)$tahun_dipilih;
            }

            $sql = "select	isnull(lokasi.kd_lokasi, '') as kode_lokasi,
                            isnull(lokasi.ket, '') as keterangan,
                            cast(isnull(faktur.total, 0) as decimal(13, 0)) as total,
                            cast(isnull(faktur_lalu.total_lalu, 0) as decimal(13, 0)) as total_lalu,
                            cast(iif(isnull(faktur_lalu.total_lalu, 0) <= 0, 0,
                                ((isnull(faktur.total, 0) - isnull(faktur_lalu.total_lalu, 0)) / isnull(faktur_lalu.total_lalu, 0)) * 100
                            ) as decimal(5, 2)) as prosentase
                    from
                    (
                        select	lokasi.companyid, lokasi.kd_lokasi, lokasi.ket
                        from	lokasi with (nolock)
                        where	lokasi.kd_faktur='OL' and
                                lokasi.companyid='".$request->get('companyid')."'
                    )	lokasi
                    left join
                    (
                        select	faktur.companyid, faktur.kd_lokasi, sum(faktur.total) as total
                        from
                        (
                            select	faktur.companyid, faktur.no_faktur, fakt_dtl.kd_lokasi, faktur.total
                            from
                            (
                                select	faktur.companyid, faktur.no_faktur, faktur.total
                                from	faktur with (nolock)
                                where	left(faktur.no_faktur, 2)='OL' and
                                        year(faktur.tgl_faktur)='".$tahun_dipilih."' and
                                        month(faktur.tgl_faktur)='".$bulan_dipilih."' and
                                        companyid='".$request->get('companyid')."'
                            )	faktur
                                    left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                                faktur.companyid=fakt_dtl.companyid
                            where	isnull(fakt_dtl.jml_jual, 0) > 0 and
                                    fakt_dtl.kd_lokasi in ".$this->kode_marketplace."
                            group by faktur.companyid, faktur.no_faktur, fakt_dtl.kd_lokasi, faktur.total
                        )	faktur
                        group by faktur.companyid, faktur.kd_lokasi
                    )	faktur on lokasi.kd_lokasi=faktur.kd_lokasi and lokasi.companyid=faktur.companyid
                    left join
                    (
                        select	faktur.companyid, faktur.kd_lokasi,
                                sum(faktur.total) as total_lalu
                        from
                        (
                            select	faktur.companyid, faktur.no_faktur, fakt_dtl.kd_lokasi, faktur.total
                            from
                            (
                                select	faktur.companyid, faktur.no_faktur, faktur.total
                                from	faktur with (nolock)
                                where	left(faktur.no_faktur, 2)='OL' and
                                        year(faktur.tgl_faktur)='".$tahun_lalu."' and
                                        month(faktur.tgl_faktur)='".$bulan_lalu."' and
                                        companyid='".$request->get('companyid')."'
                            )	faktur
                                    left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                                faktur.companyid=fakt_dtl.companyid
                            where	isnull(fakt_dtl.jml_jual, 0) > 0 and
                                    fakt_dtl.kd_lokasi in ".$this->kode_marketplace."
                            group by faktur.companyid, faktur.no_faktur, fakt_dtl.kd_lokasi, faktur.total
                        )	faktur
                        group by faktur.companyid, faktur.kd_lokasi
                    )	faktur_lalu on lokasi.kd_lokasi=faktur_lalu.kd_lokasi and lokasi.companyid=faktur_lalu.companyid
                    order by lokasi.companyid asc, lokasi.kd_lokasi asc";

            $result = DB::select($sql);

            $data_dashboard = new Collection;

            foreach($result as $data) {
                $data_dashboard->push((object) [
                    'kode_lokasi'   => strtoupper(trim($data->kode_lokasi)),
                    'keterangan'    => strtoupper(trim($data->keterangan)),
                    'bulan_dipilih' => [
                        'tahun'     => $tahun_dipilih,
                        'bulan'     => $bulan_dipilih,
                        'total'     => (double)$data->total,
                    ],
                    'bulan_lalu'    => [
                        'tahun'     => $tahun_lalu,
                        'bulan'     => $bulan_lalu,
                        'total'     => (double)$data->total_lalu,
                    ],
                    'prosentase'    => (double)$data->prosentase
                ]);
            }

            return Response::responseSuccess('success', $data_dashboard);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid')
            );
        }
    }

    public function salesByDate(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'year'      => 'required|string',
                'month'     => 'required|string',
                'companyid' => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning('Pilih bulan dan tahun terlebih dahulu');
            }

            $sql = "select	day(faktur.tgl_faktur) as day,
                            sum(iif(faktur.kd_lokasi = 'OB', isnull(faktur.total, 0), 0)) as amount_ob,
                            sum(iif(faktur.kd_lokasi = 'OK', isnull(faktur.total, 0), 0)) as amount_ok,
                            sum(iif(faktur.kd_lokasi = 'OL', isnull(faktur.total, 0), 0)) as amount_ol,
                            sum(iif(faktur.kd_lokasi = 'OP', isnull(faktur.total, 0), 0)) as amount_op,
                            sum(iif(faktur.kd_lokasi = 'OS', isnull(faktur.total, 0), 0)) as amount_os,
                            sum(iif(faktur.kd_lokasi = 'OT', isnull(faktur.total, 0), 0)) as amount_ot
                    from
                    (
                        select	faktur.companyid, fakt_dtl.kd_lokasi, faktur.tgl_faktur,
                                faktur.no_faktur, faktur.total
                        from
                        (
                            select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur,
                                    faktur.total
                            from	faktur with (nolock)
                            where	left(faktur.no_faktur, 2)='OL' and
                                    year(faktur.tgl_faktur)=? and
                                    month(faktur.tgl_faktur)=? and
                                    faktur.companyid=?
                        )	faktur
                                inner join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                            faktur.companyid=fakt_dtl.companyid
                        where	isnull(fakt_dtl.jml_jual, 0) > 0 and
                                fakt_dtl.kd_lokasi in ".$this->kode_marketplace."
                        group by faktur.companyid, fakt_dtl.kd_lokasi, faktur.tgl_faktur,
                                faktur.no_faktur, faktur.total
                    )	faktur
                    group by day(faktur.tgl_faktur)
                    having  sum(iif(faktur.kd_lokasi = 'OB', isnull(faktur.total, 0), 0)) > 0 or
                            sum(iif(faktur.kd_lokasi = 'OK', isnull(faktur.total, 0), 0)) > 0 or
                            sum(iif(faktur.kd_lokasi = 'OL', isnull(faktur.total, 0), 0)) > 0 or
                            sum(iif(faktur.kd_lokasi = 'OP', isnull(faktur.total, 0), 0)) > 0 or
                            sum(iif(faktur.kd_lokasi = 'OS', isnull(faktur.total, 0), 0)) > 0 or
                            sum(iif(faktur.kd_lokasi = 'OT', isnull(faktur.total, 0), 0)) > 0
                    order by day(faktur.tgl_faktur) asc";

            $result = DB::select($sql, [ $request->get('year'), $request->get('month'), $request->get('companyid') ]);

            $data_dashboard = [];

            foreach($result as $data) {
                $data_dashboard[] = [
                    'day'       => (int)$data->day,
                    'amount_ob' => (double)$data->amount_ob,
                    'amount_ok' => (double)$data->amount_ok,
                    'amount_ol' => (double)$data->amount_ol,
                    'amount_op' => (double)$data->amount_op,
                    'amount_os' => (double)$data->amount_os,
                    'amount_os' => (double)$data->amount_os,
                ];
            }

            return Response::responseSuccess('success', $data_dashboard);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid')
            );
        }
    }
}
