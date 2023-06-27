<?php

namespace App\Http\Controllers\Api\Backend\Visit;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;


class ApiPlanningVisitController extends Controller {

    public function daftarPlanningVisit(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'year'          => 'required',
                'month'         => 'required',
                'user_id'       => 'required',
                'role_id'       => 'required',
                'companyid'     => 'required'
            ]);

            if($validate->fails()) {
                return Response::responseWarning('Bulan dan tahun harus terisi');
            }

            $sql = DB::table('visit_date')->lock('with (nolock)')
                    ->selectRaw("isnull(visit_date.kd_visit, '') as kode_visit")
                    ->leftJoin(DB::raw('salesman with (nolock)'), function($join) {
                        $join->on('salesman.kd_sales', '=', 'visit_date.kd_sales')
                            ->on('salesman.companyid', '=', 'visit_date.companyid');
                    })
                    ->leftJoin(DB::raw('superspv with (nolock)'), function($join) {
                        $join->on('superspv.kd_spv', '=', 'salesman.spv')
                            ->on('superspv.companyid', '=', 'visit_date.companyid');
                    })
                    ->whereYear('visit_date.tanggal', $request->get('year'))
                    ->whereMonth('visit_date.tanggal', $request->get('month'))
                    ->where('visit_date.companyid', $request->get('companyid'))
                    ->orderBy('visit_date.tanggal', 'desc')
                    ->orderBy('visit_date.created_at', 'desc');

            if(strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                $sql->where('visit_date.kd_dealer', $request->get('user_id'));
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql->where('visit_date.kd_sales', $request->get('user_id'));
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql->where('superspv.nm_spv', $request->get('user_id'));
            }

            if(!empty($request->get('kode_sales')) && trim($request->get('kode_sales')) != '') {
                $sql->where('visit_date.kd_sales', $request->get('kode_sales'));
            }

            if(!empty($request->get('kode_dealer')) && trim($request->get('kode_dealer')) != '') {
                $sql->where('visit_date.kd_dealer', $request->get('kode_dealer'));
            }

            $sql = $sql->paginate($request->get('per_page') ?? 10);

            $result = collect($sql)->toArray();

            $current_page = $result['current_page'];
            $data = $result['data'];
            $first_page_url = $result['first_page_url'];
            $from = $result['from'];
            $last_page = $result['last_page'];
            $last_page_url = $result['last_page_url'];
            $links = $result['links'];
            $next_page_url = $result['next_page_url'];
            $path = $result['path'];
            $per_page = $result['per_page'];
            $prev_page_url = $result['prev_page_url'];
            $to = $result['to'];
            $total = $result['total'];

            $jumlah_data = 0;
            $data_kode_planning = '';
            $data_planning = new Collection();

            foreach ($data as $record) {
                $jumlah_data = (double)$jumlah_data + 1;

                if (trim($data_kode_planning) == '') {
                    $data_kode_planning .= "'".trim($record->kode_visit)."'";
                } else {
                    $data_kode_planning .= ",'".trim($record->kode_visit)."'";
                }
            }

            if((double)$jumlah_data > 0) {
                $sql = "select	isnull(visit_date.kd_visit, '') as kode_visit, isnull(visit_date.tanggal, '') as tanggal,
                                isnull(visit_date.kd_sales, '') as kode_sales, isnull(salesman.nm_sales, '') as nama_sales,
                                isnull(visit_date.kd_dealer, '') as kode_dealer, isnull(dealer.nm_dealer, '') as nama_dealer,
                                isnull(visit_date.keterangan, '') as keterangan_planning,
                                isnull(convert(varchar(8), visit.check_in, 114), '') as check_in,
                                isnull(convert(varchar(8), visit.check_out, 114), '') as check_out,
                                isnull(visit.keterangan, '') as keterangan_checkin,
                                isnull(visit.keterangan_lain, '') as keterangan_checkout
                        from
                        (
                            select	visit_date.companyid, visit_date.kd_visit, visit_date.tanggal, visit_date.kd_sales,
                                    visit_date.kd_dealer, visit_date.keterangan, visit_date.created_at
                            from	visit_date with (nolock)
                            where	visit_date.kd_visit in (".$data_kode_planning.") and
                                    visit_date.companyid=?
                        )	visit_date
                                left join dealer with (nolock) on visit_date.kd_dealer=dealer.kd_dealer and
                                            visit_date.companyid=dealer.companyid
                                left join salesman with (nolock) on visit_date.kd_sales=salesman.kd_sales and
                                            visit_date.companyid=salesman.companyid
                                left join visit with (nolock) on visit_date.kd_visit=visit.kd_visit and
                                            visit_date.companyid=visit.companyid
                        order by visit_date.tanggal desc, visit_date.created_at desc";

                $result = DB::select($sql, [ $request->get('companyid') ]);

                foreach($result as $data) {
                    $data_planning->push((object) [
                        'kode_visit'            => strtoupper(trim($data->kode_visit)),
                        'tanggal'               => trim($data->tanggal),
                        'kode_sales'            => trim($data->kode_sales),
                        'nama_sales'            => trim($data->nama_sales),
                        'kode_dealer'           => trim($data->kode_dealer),
                        'nama_dealer'           => trim($data->nama_dealer),
                        'check_in'              => trim($data->check_in),
                        'check_out'             => trim($data->check_out),
                        'keterangan_planning'   => trim($data->keterangan_planning),
                        'keterangan_checkin'    => trim($data->keterangan_checkin),
                        'keterangan_checkout'   => trim($data->keterangan_checkout),
                    ]);
                }
            }

            $data_planning_visit = [
                'current_page'  => $current_page,
                'data'          => $data_planning,
                'first_page_url' => $first_page_url,
                'from'          => $from,
                'last_page'     => $last_page,
                'last_page_url' => $last_page_url,
                'links'         => $links,
                'next_page_url' => $next_page_url,
                'path'          => $path,
                'per_page'      => $per_page,
                'prev_page_url' => $prev_page_url,
                'to'            => $to,
                'total'         => $total
            ];

            return Response::responseSuccess('success', $data_planning_visit);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function simpanPlanningVisit(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'tanggal'       => 'required|string',
                'kode_sales'    => 'required|string',
                'kode_dealer'   => 'required|string',
                'keterangan'    => 'required|string',
                'user_id'       => 'required|string',
                'role_id'       => 'required|string',
                'companyid'     => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning('Isi data planning visit secara lengkap');
            }

            $sql = DB::table('salesman')->lock('with (nolock)')
                    ->select('salesman.kd_sales','superspv.nm_spv')
                    ->leftJoin(DB::raw('superspv with (nolock)'), function($join) {
                        $join->on('superspv.kd_spv', '=', 'salesman.spv')
                            ->on('superspv.companyid', '=', 'salesman.companyid');
                    })->lock('with (nolock)')
                    ->where('salesman.kd_sales', $request->get('kode_sales'))
                    ->where('salesman.companyid', $request->get('companyid'))
                    ->first();

            if(empty($sql->kd_sales)) {
                return Response::responseWarning('Kode sales tidak terdaftar');
            } else {
                if(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                    if(strtoupper(trim($request->get('user_id'))) != strtoupper(trim($sql->nm_spv))) {
                        return Response::responseWarning('Kode sales yang anda entry bukan salesman anda');
                    }
                }
            }

            $sql = DB::table('salesk_dtl')->lock('with (nolock)')
                    ->where('salesk_dtl.kd_sales', $request->get('kode_sales'))
                    ->where('salesk_dtl.kd_dealer', $request->get('kode_dealer'))
                    ->where('salesk_dtl.companyid', $request->get('companyid'))
                    ->first();

            if(empty($sql->kd_dealer)) {
                return Response::responseWarning('Kode dealer tidak terdaftar atau bukan milik salesman '.strtoupper(trim($request->get('kode_sales'))));
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_PlanVisitSales_Simpan ?,?,?,?,?', [
                    $request->get('tanggal'), strtoupper(trim($request->get('kode_dealer'))), strtoupper(trim($request->get('kode_sales'))),
                    trim($request->get('keterangan')), strtoupper(trim($request->get('companyid')))
                ]);
            });

            return Response::responseSuccess('Data Planning Visit Berhasil Disimpan', null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function hapusPlanningVisit(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'kode_visit'    => 'required|string',
                'companyid'     => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning('Pilih kode visit terlebih dahulu');
            }

            $sql = DB::table('visit')->lock('with (nolock)')
                    ->where('kd_visit', $request->get('kode_visit'))
                    ->where('companyid', $request->get('companyid'))
                    ->first();

            if(!empty($sql->kd_visit)) {
                return Response::responseWarning('Planning visit yang telah dicheck in tidak bisa dihapus');
            }

            DB::transaction(function () use ($request) {
                DB::delete('exec SP_PlanVisitSales_Hapus ?,?', [
                    strtoupper(trim($request->get('kode_visit'))), strtoupper(trim($request->get('companyid')))
                ]);
            });

            return Response::responseSuccess('Data Planning Visit Berhasil Dihapus', null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
