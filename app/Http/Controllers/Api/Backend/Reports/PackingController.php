<?php

namespace app\Http\Controllers\Api\Backend\Reports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PackingController extends Controller
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

            if(empty($request->tanggal)){
                $request->merge(['tanggal' => [date('Y-m-d'), date('Y-m-d')]]);
            }

            if(!empty($request->group_by)){
                if($request->group_by == 2){
                    $request->merge(['group_by' => ['kd_lokpack']]);
                }elseif($request->group_by == 3){
                    $request->merge(['group_by' => ['kd_pack']]);
                }elseif($request->group_by == 4){
                    $request->merge(['group_by' => ['kd_lokpack', 'kd_pack']]);
                }
            }

            $data = '';
            if ($request->jenis_data == 2){
                // ! ==========================================
                // ! ========== Data Per dokumen ==============
                // ! ==========================================
                $data = DB::table(function ($query) use ($request) {
                    $query->select(
                        'no_dok',
                        'kd_dealer',
                        'tanggal3 as tanggal',
                        'kd_pack',
                        'kd_lokpack',
                        'jam3 as waktu_mulai',
                        'jam4 as waktu_selesai',
                        DB::raw("CONVERT(varchar(8), DATEADD(SECOND, DATEDIFF(SECOND, jam3, jam4), 0), 108) AS waktu_proses"),
                        'wh_time.CompanyId',
                    )
                    ->from('wh_time')
                    ->where('tanggal3', '!=', null)
                    ->whereBetween(DB::raw('CONVERT(DATE, tanggal3)'), $request->tanggal);

                    if(!empty($request->no_meja)){
                        $query = $query->where('kd_lokpack', $request->no_meja);
                    }
                    if(!empty($request->kd_packer)){
                        $query = $query->where('kd_pack', $request->kd_packer);
                    }
                }, 'wh_time')
                ->select(
                    'wh_time.no_dok',
                    DB::raw("COUNT(wh_dtl.no_faktur) as jumlah_faktur"),
                    'dealer.kd_dealer',
                    'wh_time.tanggal',
                    'wh_time.kd_pack',
                    'wh_time.kd_lokpack',
                    'wh_time.waktu_mulai',
                    'wh_time.waktu_selesai',
                    'wh_time.waktu_proses'
                )
                ->joinSub(function ($query) {
                    $query->select(
                        'no_dok',
                        'no_faktur',
                        'CompanyId'
                    )
                    ->from('wh_dtl')
                    ->groupBy('no_dok', 'no_faktur', 'CompanyId');
                }, 'wh_dtl', function ($join) {
                    $join->on('wh_time.no_dok', '=', 'wh_dtl.no_dok')
                        ->on('wh_time.CompanyId', '=', 'wh_dtl.CompanyId');
                })
                ->joinSub(function ($query) {
                    $query->select(
                        'kd_dealer',
                        'kd_area',
                        'CompanyId'
                    )
                    ->from('dealer')
                    ->where('kd_area', 'i8');
                }, 'dealer', function ($join) {
                    $join->on('wh_time.kd_dealer', '=', 'dealer.kd_dealer')
                        ->on('wh_time.CompanyId', '=', 'dealer.CompanyId');
                })
                ->leftJoin('faktur', function ($join) {
                    $join->on('wh_dtl.no_faktur', '=', 'faktur.no_faktur')
                        ->on('wh_time.CompanyId', '=', 'faktur.CompanyId');
                })
                ->orderBy('wh_time.tanggal', 'asc')
                ->orderBy('wh_time.waktu_mulai', 'asc')
                ->groupBy('wh_time.no_dok', 'dealer.kd_dealer', 'wh_time.tanggal', 'wh_time.kd_pack', 'wh_time.kd_lokpack', 'wh_time.waktu_mulai', 'wh_time.waktu_selesai', 'wh_time.waktu_proses')
                ->paginate($request->per_page);

                $dataDetail = collect(DB::table(function ($query) use ($data) {
                    $query->select(
                        'no_dok',
                        'no_faktur',
                        'CompanyId'
                    )
                    ->from('wh_dtl')
                    ->whereIn('no_dok', collect($data->items())->pluck('no_dok')->toArray());
                }, 'wh_dtl')
                ->leftJoin('fakt_dtl', function ($join) {
                    $join->on('wh_dtl.no_faktur', '=', 'fakt_dtl.no_faktur')
                        ->on('wh_dtl.CompanyId', '=', 'fakt_dtl.CompanyId');
                })
                ->leftJoin('part', function ($join) {
                    $join->on('fakt_dtl.kd_part', '=', 'part.kd_part')
                        ->on('fakt_dtl.CompanyId', '=', 'part.CompanyId');
                })
                ->select(
                    'wh_dtl.no_dok',
                    'fakt_dtl.kd_part',
                    'part.ket as nm_part',
                    'fakt_dtl.jml_order as jml_part'
                )
                ->get())->groupBy('no_dok');

                foreach ($data->items() as $key => $value) {
                    $data->items()[$key]->detail = $dataDetail[$value->no_dok];
                }

            } elseif ($request->jenis_data == 3){
                // ! ==========================================
                // ! ============== Group BY ==================
                // ! ==========================================
                $data = DB::table(function($query) use ($request){
                    $query->select(
                        DB::raw("count(no_dok) as jumlah_nodok"),
                        DB::raw("sum(jumlah_faktur) as jumlah_faktur"),
                        'kd_dealer',
                        'tanggal',
                        DB::raw("AVG(waktu_proses) AS waktu_proses")
                    )->from(function($query) use ($request){
                        $query->select(
                            'wh_time.no_dok',
                            DB::raw("COUNT(wh_dtl.no_faktur) as jumlah_faktur"),
                            'dealer.kd_dealer',
                            'wh_time.tanggal',
                            'wh_time.kd_pack',
                            'kd_lokpack',
                            DB::raw("AVG(DATEDIFF(SECOND, '00:00:00', waktu_proses)) as waktu_proses")
                        )->from(function ($query) use ($request){
                            $query->select(
                                'no_dok',
                                'kd_dealer',
                                'tanggal3 as tanggal',
                                'kd_pack',
                                'kd_lokpack',
                                'jam3 as waktu_mulai',
                                'jam4 as waktu_selesai',
                                DB::raw("CONVERT(varchar(8), DATEADD(SECOND, DATEDIFF(SECOND, jam3, jam4), 0), 108) AS waktu_proses"),
                                'wh_time.CompanyId',
                            )
                            ->from('wh_time')
                            ->where('tanggal3', '!=', null)
                            ->where('kd_lokpack', '!=', null)
                            ->whereBetween(DB::raw('CONVERT(DATE, tanggal3)'), $request->tanggal);
                            if(!empty($request->no_meja)){
                                $query = $query->where('kd_lokpack', $request->no_meja);
                            }
                            if(!empty($request->kd_packer)){
                                $query = $query->where('kd_pack', $request->kd_packer);
                            }
                        }, 'wh_time')
                        ->joinSub(function ($query) {
                            $query->select(
                                'no_dok',
                                'no_faktur',
                                'CompanyId'
                            )
                            ->from('wh_dtl')
                            ->groupBy('no_dok', 'no_faktur', 'CompanyId');
                        }, 'wh_dtl', function ($join) {
                            $join->on('wh_time.no_dok', '=', 'wh_dtl.no_dok')
                                ->on('wh_time.CompanyId', '=', 'wh_dtl.CompanyId');
                        })
                        ->joinSub(function ($query) {
                            $query->select(
                                'kd_dealer',
                                'kd_area',
                                'CompanyId'
                            )
                            ->from('dealer')
                            ->where('kd_area', 'i8');
                        }, 'dealer', function ($join) {
                            $join->on('wh_time.kd_dealer', '=', 'dealer.kd_dealer')
                                ->on('wh_time.CompanyId', '=', 'dealer.CompanyId');
                        })
                        ->leftJoin('faktur', function ($join) {
                            $join->on('wh_dtl.no_faktur', '=', 'faktur.no_faktur')
                                ->on('wh_time.CompanyId', '=', 'faktur.CompanyId');
                        })
                        ->groupBy('wh_time.no_dok', 'wh_time.tanggal', 'dealer.kd_dealer', 'wh_time.kd_pack', 'kd_lokpack');
                    }, 'wh_time')
                    ->groupBy('tanggal','kd_dealer');
                    foreach ($request->group_by as $value) {
                        $query = $query->groupBy($value)
                        ->addSelect($value);
                    }
                }, 'wh_time')
                ->select(
                    DB::raw("sum(jumlah_nodok) as jml_nodok"),
                    DB::raw("sum(jumlah_faktur) as jml_faktur"),
                    DB::raw("count(kd_dealer) as jml_dealer"),
                    'tanggal',
                    DB::raw("CONVERT(VARCHAR(8), DATEADD(SECOND, AVG(waktu_proses), '00:00:00'), 108) AS AVG_waktu_proses")
                )
                ->groupBy('tanggal')
                ->orderBy('tanggal', 'asc');
                foreach ($request->group_by as $value) {
                    $data = $data->groupBy($value)
                    ->addSelect($value)
                    ->orderBy($value, 'asc');
                }
                $data = $data->paginate($request->per_page);
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
            if(empty($request->tanggal)){
                $request->merge(['tanggal' => [date('Y-m-d'), date('Y-m-d')]]);
            }

            $data = '';
            if ($request->jenis_data == 2){
                // ! ==========================================
                // ! ========== Data Per dokumen ==============
                // ! ==========================================
                $data = DB::table(function ($query) use ($request) {
                    $query->select(
                        'no_dok',
                        'kd_dealer',
                        'tanggal3 as tanggal',
                        'kd_pack',
                        'kd_lokpack',
                        'jam3 as waktu_mulai',
                        'jam4 as waktu_selesai',
                        DB::raw("CONVERT(varchar(8), DATEADD(SECOND, DATEDIFF(SECOND, jam3, jam4), 0), 108) AS waktu_proses"),
                        'wh_time.CompanyId',
                    )
                    ->from('wh_time')
                    ->where('tanggal3', '!=', null)
                    ->whereBetween(DB::raw('CONVERT(DATE, tanggal3)'), $request->tanggal);

                    if(!empty($request->no_meja)){
                        $query = $query->where('kd_lokpack', $request->no_meja);
                    }
                    if(!empty($request->kd_packer)){
                        $query = $query->where('kd_pack', $request->kd_packer);
                    }
                }, 'wh_time')
                ->select(
                    'wh_time.no_dok',
                    DB::raw("COUNT(wh_dtl.no_faktur) as jumlah_faktur"),
                    'dealer.kd_dealer',
                    'wh_time.tanggal',
                    'wh_time.kd_pack',
                    'wh_time.kd_lokpack',
                    'wh_time.waktu_mulai',
                    'wh_time.waktu_selesai',
                    'wh_time.waktu_proses'
                )
                ->joinSub(function ($query) {
                    $query->select(
                        'no_dok',
                        'no_faktur',
                        'CompanyId'
                    )
                    ->from('wh_dtl')
                    ->groupBy('no_dok', 'no_faktur', 'CompanyId');
                }, 'wh_dtl', function ($join) {
                    $join->on('wh_time.no_dok', '=', 'wh_dtl.no_dok')
                        ->on('wh_time.CompanyId', '=', 'wh_dtl.CompanyId');
                })
                ->joinSub(function ($query) {
                    $query->select(
                        'kd_dealer',
                        'kd_area',
                        'CompanyId'
                    )
                    ->from('dealer')
                    ->where('kd_area', 'i8');
                }, 'dealer', function ($join) {
                    $join->on('wh_time.kd_dealer', '=', 'dealer.kd_dealer')
                        ->on('wh_time.CompanyId', '=', 'dealer.CompanyId');
                })
                ->leftJoin('faktur', function ($join) {
                    $join->on('wh_dtl.no_faktur', '=', 'faktur.no_faktur')
                        ->on('wh_time.CompanyId', '=', 'faktur.CompanyId');
                })
                ->orderBy('wh_time.tanggal', 'asc')
                ->orderBy('wh_time.waktu_mulai', 'asc')
                ->groupBy('wh_time.no_dok', 'dealer.kd_dealer', 'wh_time.tanggal', 'wh_time.kd_pack', 'wh_time.kd_lokpack', 'wh_time.waktu_mulai', 'wh_time.waktu_selesai', 'wh_time.waktu_proses')
                ->get();

            } elseif ($request->jenis_data == 3){
                // ! ==========================================
                // ! ============== Group BY ==================
                // ! ==========================================
                $data = DB::table(function($query) use ($request){
                    $query->select(
                        DB::raw("count(no_dok) as jumlah_nodok"),
                        DB::raw("sum(jumlah_faktur) as jumlah_faktur"),
                        'kd_dealer',
                        'tanggal',
                        DB::raw("AVG(waktu_proses) AS waktu_proses")
                    )->from(function($query) use ($request){
                        $query->select(
                            'wh_time.no_dok',
                            DB::raw("COUNT(wh_dtl.no_faktur) as jumlah_faktur"),
                            'dealer.kd_dealer',
                            'wh_time.tanggal',
                            'wh_time.kd_pack',
                            'kd_lokpack',
                            DB::raw("AVG(DATEDIFF(SECOND, '00:00:00', waktu_proses)) as waktu_proses")
                        )->from(function ($query) use ($request){
                            $query->select(
                                'no_dok',
                                'kd_dealer',
                                'tanggal3 as tanggal',
                                'kd_pack',
                                'kd_lokpack',
                                'jam3 as waktu_mulai',
                                'jam4 as waktu_selesai',
                                DB::raw("CONVERT(varchar(8), DATEADD(SECOND, DATEDIFF(SECOND, jam3, jam4), 0), 108) AS waktu_proses"),
                                'wh_time.CompanyId',
                            )
                            ->from('wh_time')
                            ->where('tanggal3', '!=', null)
                            ->where('kd_lokpack', '!=', null)
                            ->whereBetween(DB::raw('CONVERT(DATE, tanggal3)'), $request->tanggal);
                            if(!empty($request->no_meja)){
                                $query = $query->where('kd_lokpack', $request->no_meja);
                            }
                            if(!empty($request->kd_packer)){
                                $query = $query->where('kd_pack', $request->kd_packer);
                            }
                        }, 'wh_time')
                        ->joinSub(function ($query) {
                            $query->select(
                                'no_dok',
                                'no_faktur',
                                'CompanyId'
                            )
                            ->from('wh_dtl')
                            ->groupBy('no_dok', 'no_faktur', 'CompanyId');
                        }, 'wh_dtl', function ($join) {
                            $join->on('wh_time.no_dok', '=', 'wh_dtl.no_dok')
                                ->on('wh_time.CompanyId', '=', 'wh_dtl.CompanyId');
                        })
                        ->joinSub(function ($query) {
                            $query->select(
                                'kd_dealer',
                                'kd_area',
                                'CompanyId'
                            )
                            ->from('dealer')
                            ->where('kd_area', 'i8');
                        }, 'dealer', function ($join) {
                            $join->on('wh_time.kd_dealer', '=', 'dealer.kd_dealer')
                                ->on('wh_time.CompanyId', '=', 'dealer.CompanyId');
                        })
                        ->leftJoin('faktur', function ($join) {
                            $join->on('wh_dtl.no_faktur', '=', 'faktur.no_faktur')
                                ->on('wh_time.CompanyId', '=', 'faktur.CompanyId');
                        })
                        ->groupBy('wh_time.no_dok', 'wh_time.tanggal', 'dealer.kd_dealer', 'wh_time.kd_pack', 'kd_lokpack');
                    }, 'wh_time')
                    ->groupBy('tanggal','kd_dealer');
                    foreach ($request->group_by as $value) {
                        $query = $query->groupBy($value)
                        ->addSelect($value);
                    }
                }, 'wh_time')
                ->select(
                    DB::raw("sum(jumlah_nodok) as jml_nodok"),
                    DB::raw("sum(jumlah_faktur) as jml_faktur"),
                    DB::raw("count(kd_dealer) as jml_dealer"),
                    'tanggal'
                )
                ->groupBy('tanggal')
                ->orderBy('tanggal', 'asc');
                foreach ($request->group_by as $value) {
                    $data = $data->groupBy($value)
                    ->addSelect($value)
                    ->orderBy($value, 'asc');
                }
                $data = $data
                ->addSelect(DB::raw("CONVERT(VARCHAR(8), DATEADD(SECOND, AVG(waktu_proses), '00:00:00'), 108) AS AVG_waktu_proses"))
                ->get();
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
