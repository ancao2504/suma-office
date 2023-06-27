<?php

namespace App\Http\Controllers\Api\Backend\Profile;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;


class ApiDealerController extends Controller
{
    public function daftarDealer(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'user_id'   => 'required|string',
                'role_id'   => 'required|string',
                'companyid' => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Anda belum login");
            }

            if(strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                return Response::responseWarning(strtoupper(trim(config('constants.api.access.disabled'))));
            }

            $sql = DB::table('dealer')->lock('with (nolock)')
                    ->selectRaw("isnull(dealer.kd_dealer, '') as kode_dealer, isnull(dealer.nm_dealer, '') as nama_dealer,
                                isnull(dealer.status, '') as status, isnull(dealer.sts, '') as sts,
                                isnull(dealer.kabupaten, '') as kabupaten,
                                case
                                    when limit_piut=1 Or limit_sales=1 then 'BLACK_LIST'
                                    when limit_piut <> 0 Or limit_sales <> 0 then 'LIMIT_PIUTANG'
                                else
                                    'LIMIT_SALES'
                                end as status_limit")
                    ->leftJoin(DB::raw('salesman with (nolock)'), function($join) {
                            $join->on('dealer.kd_sales', '=', 'salesman.kd_sales')
                                ->on('dealer.companyid', '=', 'salesman.companyid');
                        })
                    ->leftJoin(DB::raw('superspv with (nolock)'), function($join) {
                            $join->on('salesman.spv', '=', 'superspv.kd_spv')
                                ->on('dealer.companyid', '=', 'superspv.companyid');
                        })
                    ->where('dealer.companyid', trim($request->get('companyid')))
                    ->orderBy('dealer.kd_dealer', 'asc');

            if(!empty($request->get('kode_dealer')) && trim($request->get('kode_dealer')) != '') {
                $sql->where('dealer.kd_dealer', 'like', $request->get('kode_dealer').'%');
            }

            if(trim($request->get('role_id')) == 'MD_H3_KORSM') {
                $sql->where('superspv.nm_spv', trim($request->get('user_id')));
            } elseif(trim($request->get('role_id')) == 'MD_H3_SM') {
                $sql->where('dealer.kd_sales', trim($request->get('user_id')));
            }

            $sql = $sql->paginate($request->get('per_page') ?? 10);

            return Response::responseSuccess("success", $sql);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function formDealer(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'kode_dealer' => 'required|string',
                'user_id'   => 'required|string',
                'role_id'   => 'required|string',
                'companyid' => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih kode dealer terlebih dahulu");
            }

            $sql = "select	isnull(dealer.kd_dealer, '') as kode_dealer, isnull(dealer.nm_dealer, '') as nama_dealer,
                            isnull(dealer.cabang, '') as cabang, isnull(dealer.kd_sales, '') as kode_sales,
                            isnull(salesman.nm_sales, '') as nama_sales, isnull(dealer.kd_area, '') as kode_area,
                            isnull(area.nm_area, '') as nama_area, isnull(dealer.ktp_h, '') as ktp,
                            isnull(dealer.npwp, '') as npwp, isnull(dealer.alamat1, '') as alamat,
                            isnull(dealer.kabupaten, '') as kabupaten, isnull(dealer.karesidenan, '') as karesidenan,
                            isnull(dealer.kota, '') as kota, isnull(dealer.telpon, '') as telepon, isnull(dealer.email, '') as email,
                            isnull(dealer.nm_dealersj, '') as nama_dealer_sj, isnull(dealer.alamat1sj, '') as alamat_dealer_sj,
                            isnull(dealer.kotasj, '') as kota_dealer_sj, isnull(dealer.sts, '') as sts, isnull(dealer.status, '') as status,
                            case
                                when limit_piut=1 Or limit_sales=1 then 0
                                when limit_piut <> 0 Or limit_sales <> 0 then
                                    isnull(limit_piut, 0) - (isnull(s_awal_b, 0) + isnull(jual_14, 0) + isnull(jual_20, 0) - isnull(extra, 0) +
                                        isnull(da, 0) - isnull(ca, 0) - isnull(insentif, 0) - isnull(t_bayar_b, 0))
                                else
                                    (isnull(limit_sales, 0) - (isnull(jual_14, 0) + isnull(jual_20, 0)))
                            end as sisa_limit,
                            case
                                when limit_piut=1 Or limit_sales=1 then 'BLACK_LIST'
                                when limit_piut <> 0 Or limit_sales <> 0 then 'LIMIT_PIUTANG'
                            else
                                'LIMIT_SALES'
                            end as status_limit,
                            case
                                when limit_piut=1 Or limit_sales=1 then 0
                                when limit_piut <> 0 Or limit_sales <> 0 then
                                    isnull(limit_piut, 0) - (isnull(s_awal_b, 0) + isnull(jual_14, 0) + isnull(jual_20, 0) - isnull(extra, 0) +
                                        isnull(da, 0) - isnull(ca, 0) - isnull(insentif, 0) - isnull(t_bayar_b, 0))
                                else
                                    (isnull(limit_sales, 0) - (isnull(jual_14, 0) + isnull(jual_20, 0)))
                            end as sisa_limit,
                            case
                                when limit_piut=1 Or limit_sales=1 then 'BLACK LIST'
                                when limit_piut <> 0 Or limit_sales <> 0 then 'LIMIT PIUTANG'
                            else
                                'LIMIT SALES'
                            end as keterangan_limit,
                            isnull(dealer.latitude, 0) as latitude,
                            isnull(dealer.longitude, 0) as longitude
                    from
                    (
                        select	top 1 dealer.companyid, dealer.kd_dealer, dealer.nm_dealer, dealer.cabang, dealer.kd_sales, dealer.kd_area,
                                dealer.ktp_h, dealer.npwp, dealer.alamat1, dealer.kabupaten, dealer.karesidenan, dealer.kota, dealer.telpon,
                                dealer.email, dealer.nm_dealersj, dealer.alamat1sj, dealer.kotasj, dealer.sts, dealer.status, dealer.limit_piut,
                                dealer.s_awal_b, dealer.jual_14, dealer.jual_20, dealer.extra, dealer.da, dealer.ca, dealer.insentif,
                                dealer.t_bayar_b, dealer.limit_sales, dealer.latitude, dealer.longitude
                        from	dealer with (nolock)
                        where	dealer.kd_dealer=? and dealer.companyid=? ";

                if(strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                    $sql .= " and dealer.kd_dealer='".trim($request->get('user_id'))."'";
                } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                    $sql .= " and dealer.kd_sales='".trim($request->get('user_id'))."'";
                }

                $sql .= " )	dealer
                                inner join salesman with (nolock) on dealer.kd_sales=salesman.kd_sales and dealer.companyid=salesman.companyid
                                inner join superspv with (nolock) on salesman.spv=superspv.kd_spv and dealer.companyid=superspv.companyid
                                left join area with (nolock) on dealer.kd_area=area.kd_area and dealer.companyid=area.companyid
                                left join users with (nolock) on users.user_id=dealer.kd_dealer and dealer.companyid=users.companyid";

                if(trim($request->get('role_id')) == 'MD_H3_KORSM') {
                    $sql .= " where superspv.nm_spv='".trim($request->get('user_id'))."'";
                }

                $result = collect(DB::select($sql, [ $request->get('kode_dealer'), $request->get('companyid') ]))->first();

                if(empty($result->kode_dealer) || $result->kode_dealer = "") {
                    return Response::responseWarning("Kode dealer tidak terdaftar atau tidak ditemukan");
                } else {
                    $data = [];
                    $data[] = [
                        'kode_dealer'   => trim($request->get('kode_dealer')),
                        'nama_dealer'   => trim($result->nama_dealer),
                        'cabang'        => trim($result->cabang),
                        'kode_sales'    => trim($result->kode_sales),
                        'nama_sales'    => trim($result->nama_sales),
                        'kode_area'     => trim($result->kode_area),
                        'nama_area'     => trim($result->nama_area),
                        'npwp'          => trim($result->npwp),
                        'ktp'           => trim($result->ktp),
                        'alamat'        => trim($result->alamat),
                        'kabupaten'     => trim($result->kabupaten),
                        'karesidenan'   => trim($result->karesidenan),
                        'kota'          => trim($result->kota),
                        'telepon'       => trim($result->telepon),
                        'email'         => trim($result->email),
                        'sts'           => trim($result->sts),
                        'status'        => trim($result->status),
                        'nama_dealer_sj' => trim($result->nama_dealer_sj),
                        'alamat_dealer_sj' => trim($result->alamat_dealer_sj),
                        'kota_dealer_sj' => trim($result->kota_dealer_sj),
                        'keterangan_limit' => trim($result->keterangan_limit),
                        'sisa_limit'    => trim($result->sisa_limit),
                        'status_limit'  => trim($result->status_limit),
                        'latitude'      => trim($result->latitude),
                        'longitude'     => trim($result->longitude),
                    ];

                    return Response::responseSuccess("success", collect($data)->first());
                }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
