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
                        'karyawan.nama as nm_packing',
                        'kd_pack',
                        'kd_lokpack',
                        'jam3 as waktu_mulai',
                        'jam4 as waktu_selesai',
                        DB::raw("CONVERT(varchar(8), DATEADD(SECOND, DATEDIFF(SECOND, jam3, jam4), 0), 108) AS waktu_proses"),
                        'wh_time.CompanyId',
                    )
                    ->from('wh_time')
                    ->leftjoin('karyawan', function ($join) {
                        $join->on('wh_time.kd_pack', '=', 'karyawan.kode')
                            ->on('wh_time.CompanyId', '=', 'karyawan.CompanyId');
                    })
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
                    DB::raw("COUNT(wh_dtl.no_faktur) as jml_faktur"),
                    'dealer.kd_dealer',
                    DB::raw("sum([wh_dtl].jml_item) as jml_item"),
                    DB::raw("sum([wh_dtl].jml_pcs) as jml_pcs"),
                    'wh_time.tanggal',
                    'wh_time.nm_packing',
                    'wh_time.kd_lokpack',
                    'wh_time.waktu_mulai',
                    'wh_time.waktu_selesai',
                    'wh_time.waktu_proses'
                )
                ->joinSub(function ($query) {
                    $query->select(
                        'wh_dtl.no_dok',
                        'wh_dtl.no_faktur',
                        DB::raw("count(fakt_dtl.kd_part) as jml_item"),
                        DB::raw("sum(fakt_dtl.jml_jual) as jml_pcs"),
                        'wh_dtl.CompanyId'
                    )
                    ->from('wh_dtl')
                    ->join('fakt_dtl', function ($join) {
                        $join->on('fakt_dtl.no_faktur', '=', 'wh_dtl.no_faktur')
                            ->on('fakt_dtl.CompanyId', '=', 'wh_dtl.CompanyId');
                    })
                    ->groupBy('wh_dtl.no_dok', 'wh_dtl.no_faktur', 'wh_dtl.CompanyId');
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
                ->groupBy('wh_time.no_dok', 'dealer.kd_dealer', 'wh_time.tanggal', 'wh_time.nm_packing', 'wh_time.kd_lokpack', 'wh_time.waktu_mulai', 'wh_time.waktu_selesai', 'wh_time.waktu_proses')
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
                    'wh_dtl.no_faktur',
                    'fakt_dtl.kd_part',
                    'part.ket as nm_part',
                    'fakt_dtl.jml_order as jml_part'
                )
                ->get())->groupBy('no_dok');

                foreach ($dataDetail as $key => $value) {
                    $dataDetail[$key] = $value
                    ->sortBy('no_faktur')
                    ->groupBy('no_faktur');
                }
                
                foreach ($data->items() as $key => $value) {
                    $data->items()[$key]->detail = $dataDetail[$value->no_dok];
                }

            } elseif ($request->jenis_data == 3){
                // ! ==========================================
                // ! ============== Group BY ==================
                // ! ==========================================
                $data = DB::table(function($query) use ($request){
                        $query->select(
                            'wh_time.companyid',
                            'wh_time.tanggal',
                            DB::raw("count(wh_time.no_dok) as jml_dok"),
                            DB::raw("sum(jumlah_faktur.jml_faktur) as jml_faktur"),
                            DB::raw("sum(item.jml_item) as jml_item"),
                            DB::raw("sum(item.jml_pcs) as jml_pcs"),
                            DB::raw("avg(wh_time.waktu_proses) as rata2")
                        )
                        ->from(function($query) use ($request){
                            $query->select(
                                'wh_time.no_dok',
                                'wh_time.kd_dealer',
                                'wh_time.tanggal3 as tanggal',
                                'wh_time.kd_pack',
                                'wh_time.kd_lokpack',
                                'wh_time.jam3 as waktu_mulai',
                                'wh_time.jam4 as waktu_selesai',
                                // DB::raw("DATEDIFF(SECOND, '00:00:00',CONVERT(varchar(8),DATEADD(SECOND, DATEDIFF(SECOND, jam3, jam4), 0),108)) AS waktu_proses"),
                                DB::raw("datediff(second, cast(tanggal3 +' ' + jam3 as datetime), cast(tanggal4 +' ' + jam4 as datetime)) as waktu_proses"),
                                'wh_time.CompanyId'
                            )
                            ->from(function($query) use ($request){ 
                                $query->select(
                                    '*'
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
                            ->leftjoin('dealer', function ($join) {
                                $join->on('wh_time.kd_dealer', '=', 'dealer.kd_dealer')
                                    ->on('wh_time.CompanyId', '=', 'dealer.CompanyId');
                            })
                            ->where('dealer.kd_area', 'i8');
                        }, 'wh_time')
                        ->leftjoin('karyawan', function ($join) {
                            $join->on('wh_time.kd_pack', '=', 'karyawan.kode')
                                ->on('wh_time.CompanyId', '=', 'karyawan.CompanyId');
                        })
                        ->leftjoinSub(function ($query) use ($request){
                            $query->select(
                                'wh_time.CompanyId',
                                'wh_time.no_dok',
                                DB::raw("count(wh_dtl.no_dok) as jml_faktur")
                            )
                            ->from(function ($query) use ($request) {
                                $query->select(
                                    '*'
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
                            ->leftjoin('wh_dtl', function ($join) {
                                $join->on('wh_time.no_dok', '=', 'wh_dtl.no_dok')
                                    ->on('wh_time.CompanyId', '=', 'wh_dtl.CompanyId');
                            })
                            ->leftjoin('dealer', function ($join) {
                                $join->on('wh_time.kd_dealer', '=', 'dealer.kd_dealer')
                                    ->on('wh_time.CompanyId', '=', 'dealer.CompanyId');
                            })
                            ->where('dealer.kd_area', 'i8')
                            ->groupBy('wh_time.CompanyId', 'wh_time.no_dok');
                        }, 'jumlah_faktur', function ($join) {
                            $join->on('wh_time.CompanyId', '=', 'jumlah_faktur.CompanyId')
                                ->on('wh_time.no_dok', '=', 'jumlah_faktur.no_dok');
                        })
                        ->leftjoinSub(function ($query) use ($request){
                            $query->select(
                                'wh_time.CompanyId',
                                'wh_time.no_dok',
                                DB::raw("count(wh_dtl.no_dok) as jml_item"),
                                DB::raw("sum(fakt_dtl.jml_jual) as jml_pcs")
                            )
                            ->from(function ($query) use ($request) {
                                $query->select(
                                    '*'
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
                            ->leftjoin('wh_dtl', function ($join) {
                                $join->on('wh_time.no_dok', '=', 'wh_dtl.no_dok')
                                    ->on('wh_time.CompanyId', '=', 'wh_dtl.CompanyId');
                            })
                            ->leftjoin('dealer', function ($join) {
                                $join->on('wh_time.kd_dealer', '=', 'dealer.kd_dealer')
                                    ->on('wh_time.CompanyId', '=', 'dealer.CompanyId');
                            })
                            ->leftjoin('fakt_dtl', function ($join) {
                                $join->on('wh_dtl.no_faktur', '=', 'fakt_dtl.no_faktur')
                                    ->on('wh_time.CompanyId', '=', 'fakt_dtl.CompanyId');
                            })
                            ->where('dealer.kd_area', 'i8')
                            ->where('fakt_dtl.jml_jual', '>', 0)
                            ->groupBy('wh_time.CompanyId', 'wh_time.no_dok');
                        }, 'item', function ($join) {
                            $join->on('wh_time.CompanyId', '=', 'item.CompanyId')
                                ->on('wh_time.no_dok', '=', 'item.no_dok');
                        })
                        ->groupBy('wh_time.CompanyId', 'wh_time.tanggal');
                        foreach ($request->group_by as $value) {
                            $query = $query->groupBy($value)
                            ->addSelect('wh_time.'.$value);
                            if($value == 'kd_pack'){
                                $query = $query
                                ->groupBy('karyawan.nama')
                                ->addSelect('karyawan.nama as nm_pack');
                            }
                        }
                }, 'wh_time')
                ->select(
                    'tanggal',
                    'jml_dealer',
                    'jml_dok',
                    'jml_faktur',
                    'jml_item',
                    'jml_pcs',
                    DB::raw("convert(varchar(8), dateadd(second, rata2, '00:00:00'), 108) as rata2_waktu_proses")
                )
                ->leftjoinSub(function ($query) use ($request){
                    $query->select(
                        'wh_time.CompanyId',
                        DB::raw("sum(wh_time.jumlah_data) as jml_dealer")
                    )
                    ->from(function ($query) use ($request) {
                        $query->select(
                            'wh_time.CompanyId',
                            'wh_time.kd_dealer',
                            DB::raw("1 as jumlah_data")
                        )
                        ->from(function ($query) use ($request) {
                            $query->select(
                                '*'
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
                        ->leftjoin('dealer', function ($join) {
                            $join->on('wh_time.kd_dealer', '=', 'dealer.kd_dealer')
                                ->on('wh_time.CompanyId', '=', 'dealer.CompanyId');
                        })
                        ->where('dealer.kd_area', 'i8')
                        ->groupBy('wh_time.CompanyId', 'wh_time.kd_dealer');
                        foreach ($request->group_by as $value) {
                            $query = $query->groupBy($value)
                            ->addSelect('wh_time.'.$value);
                        }
                    }, 'wh_time')
                    ->groupBy('wh_time.CompanyId');
                    foreach ($request->group_by as $value) {
                        $query = $query->groupBy($value)
                        ->addSelect('wh_time.'.$value);
                    }
                }, 'jumlah_dealer', function ($join) use ($request) {
                    $join->on('wh_time.CompanyId', '=', 'jumlah_dealer.CompanyId');
                        foreach ($request->group_by as $value) {
                            $join = $join->on('wh_time.'.$value, '=', 'jumlah_dealer.'.$value);
                        }
                });
                foreach ($request->group_by as $value) {
                    $data  = $data->addSelect('wh_time.'.$value);
                    if($value == 'kd_pack'){
                        $data = $data->addSelect('wh_time.nm_pack');
                    }
                }
                $data  = $data->paginate($request->per_page);
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
                        'karyawan.nama as nm_packing',
                        'kd_pack',
                        'kd_lokpack',
                        'jam3 as waktu_mulai',
                        'jam4 as waktu_selesai',
                        DB::raw("CONVERT(varchar(8), DATEADD(SECOND, DATEDIFF(SECOND, jam3, jam4), 0), 108) AS waktu_proses"),
                        'wh_time.CompanyId',
                    )
                    ->from('wh_time')
                    ->leftjoin('karyawan', function ($join) {
                        $join->on('wh_time.kd_pack', '=', 'karyawan.kode')
                            ->on('wh_time.CompanyId', '=', 'karyawan.CompanyId');
                    })
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
                    'wh_time.tanggal',
                    DB::raw("COUNT(wh_dtl.no_faktur) as jml_faktur"),
                    DB::raw("sum([wh_dtl].jml_item) as jml_item"),
                    DB::raw("sum([wh_dtl].jml_pcs) as jml_pcs"),
                    'dealer.kd_dealer',
                    'wh_time.nm_packing',
                    'wh_time.kd_lokpack',
                    'wh_time.waktu_mulai',
                    'wh_time.waktu_selesai',
                    'wh_time.waktu_proses'
                )
                ->joinSub(function ($query) {
                    $query->select(
                        'wh_dtl.no_dok',
                        'wh_dtl.no_faktur',
                        DB::raw("count(fakt_dtl.kd_part) as jml_item"),
                        DB::raw("sum(fakt_dtl.jml_jual) as jml_pcs"),
                        'wh_dtl.CompanyId'
                    )
                    ->from('wh_dtl')
                    ->join('fakt_dtl', function ($join) {
                        $join->on('fakt_dtl.no_faktur', '=', 'wh_dtl.no_faktur')
                            ->on('fakt_dtl.CompanyId', '=', 'wh_dtl.CompanyId');
                    })
                    ->groupBy('wh_dtl.no_dok', 'wh_dtl.no_faktur', 'wh_dtl.CompanyId');
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
                ->groupBy('wh_time.no_dok', 'dealer.kd_dealer', 'wh_time.tanggal', 'wh_time.nm_packing', 'wh_time.kd_lokpack', 'wh_time.waktu_mulai', 'wh_time.waktu_selesai', 'wh_time.waktu_proses')
                ->get();

            } elseif ($request->jenis_data == 3){
                // ! ==========================================
                // ! ============== Group BY ==================
                // ! ==========================================
                $data = DB::table(function($query) use ($request){
                        $query->select(
                            'wh_time.companyid',
                            'wh_time.tanggal',
                            DB::raw("count(wh_time.no_dok) as jml_dok"),
                            DB::raw("sum(jumlah_faktur.jml_faktur) as jml_faktur"),
                            DB::raw("sum(item.jml_item) as jml_item"),
                            DB::raw("sum(item.jml_pcs) as jml_pcs"),
                            DB::raw("avg(wh_time.waktu_proses) as rata2")
                        )
                        ->from(function($query) use ($request){
                            $query->select(
                                'wh_time.no_dok',
                                'wh_time.kd_dealer',
                                'wh_time.tanggal3 as tanggal',
                                'wh_time.kd_pack',
                                'wh_time.kd_lokpack',
                                'wh_time.jam3 as waktu_mulai',
                                'wh_time.jam4 as waktu_selesai',
                                // DB::raw("DATEDIFF(SECOND, '00:00:00',CONVERT(varchar(8),DATEADD(SECOND, DATEDIFF(SECOND, jam3, jam4), 0),108)) AS waktu_proses"),
                                DB::raw("datediff(second, cast(tanggal3 +' ' + jam3 as datetime), cast(tanggal4 +' ' + jam4 as datetime)) as waktu_proses"),
                                'wh_time.CompanyId'
                            )
                            ->from(function($query) use ($request){ 
                                $query->select(
                                    '*'
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
                            ->leftjoin('dealer', function ($join) {
                                $join->on('wh_time.kd_dealer', '=', 'dealer.kd_dealer')
                                    ->on('wh_time.CompanyId', '=', 'dealer.CompanyId');
                            })
                            ->where('dealer.kd_area', 'i8');
                        }, 'wh_time')
                        ->leftjoin('karyawan', function ($join) {
                            $join->on('wh_time.kd_pack', '=', 'karyawan.kode')
                                ->on('wh_time.CompanyId', '=', 'karyawan.CompanyId');
                        })
                        ->leftjoinSub(function ($query) use ($request){
                            $query->select(
                                'wh_time.CompanyId',
                                'wh_time.no_dok',
                                DB::raw("count(wh_dtl.no_dok) as jml_faktur")
                            )
                            ->from(function ($query) use ($request) {
                                $query->select(
                                    '*'
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
                            ->leftjoin('wh_dtl', function ($join) {
                                $join->on('wh_time.no_dok', '=', 'wh_dtl.no_dok')
                                    ->on('wh_time.CompanyId', '=', 'wh_dtl.CompanyId');
                            })
                            ->leftjoin('dealer', function ($join) {
                                $join->on('wh_time.kd_dealer', '=', 'dealer.kd_dealer')
                                    ->on('wh_time.CompanyId', '=', 'dealer.CompanyId');
                            })
                            ->where('dealer.kd_area', 'i8')
                            ->groupBy('wh_time.CompanyId', 'wh_time.no_dok');
                        }, 'jumlah_faktur', function ($join) {
                            $join->on('wh_time.CompanyId', '=', 'jumlah_faktur.CompanyId')
                                ->on('wh_time.no_dok', '=', 'jumlah_faktur.no_dok');
                        })
                        ->leftjoinSub(function ($query) use ($request){
                            $query->select(
                                'wh_time.CompanyId',
                                'wh_time.no_dok',
                                DB::raw("count(wh_dtl.no_dok) as jml_item"),
                                DB::raw("sum(fakt_dtl.jml_jual) as jml_pcs")
                            )
                            ->from(function ($query) use ($request) {
                                $query->select(
                                    '*'
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
                            ->leftjoin('wh_dtl', function ($join) {
                                $join->on('wh_time.no_dok', '=', 'wh_dtl.no_dok')
                                    ->on('wh_time.CompanyId', '=', 'wh_dtl.CompanyId');
                            })
                            ->leftjoin('dealer', function ($join) {
                                $join->on('wh_time.kd_dealer', '=', 'dealer.kd_dealer')
                                    ->on('wh_time.CompanyId', '=', 'dealer.CompanyId');
                            })
                            ->leftjoin('fakt_dtl', function ($join) {
                                $join->on('wh_dtl.no_faktur', '=', 'fakt_dtl.no_faktur')
                                    ->on('wh_time.CompanyId', '=', 'fakt_dtl.CompanyId');
                            })
                            ->where('dealer.kd_area', 'i8')
                            ->where('fakt_dtl.jml_jual', '>', 0)
                            ->groupBy('wh_time.CompanyId', 'wh_time.no_dok');
                        }, 'item', function ($join) {
                            $join->on('wh_time.CompanyId', '=', 'item.CompanyId')
                                ->on('wh_time.no_dok', '=', 'item.no_dok');
                        })
                        ->groupBy('wh_time.CompanyId', 'wh_time.tanggal');
                        foreach ($request->group_by as $value) {
                            $query = $query->groupBy($value)
                            ->addSelect('wh_time.'.$value);
                            if($value == 'kd_pack'){
                                $query = $query
                                ->groupBy('karyawan.nama')
                                ->addSelect('karyawan.nama as nm_pack');
                            }
                        }
                }, 'wh_time')
                ->select(
                    'tanggal',
                    'jml_dealer',
                    'jml_dok',
                    'jml_faktur',
                    'jml_item',
                    'jml_pcs'
                )
                ->leftjoinSub(function ($query) use ($request){
                    $query->select(
                        'wh_time.CompanyId',
                        DB::raw("sum(wh_time.jumlah_data) as jml_dealer")
                    )
                    ->from(function ($query) use ($request) {
                        $query->select(
                            'wh_time.CompanyId',
                            'wh_time.kd_dealer',
                            DB::raw("1 as jumlah_data")
                        )
                        ->from(function ($query) use ($request) {
                            $query->select(
                                '*'
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
                        ->leftjoin('dealer', function ($join) {
                            $join->on('wh_time.kd_dealer', '=', 'dealer.kd_dealer')
                                ->on('wh_time.CompanyId', '=', 'dealer.CompanyId');
                        })
                        ->where('dealer.kd_area', 'i8')
                        ->groupBy('wh_time.CompanyId', 'wh_time.kd_dealer');
                        foreach ($request->group_by as $value) {
                            $query = $query->groupBy($value)
                            ->addSelect('wh_time.'.$value);
                        }
                    }, 'wh_time')
                    ->groupBy('wh_time.CompanyId');
                    foreach ($request->group_by as $value) {
                        $query = $query->groupBy($value)
                        ->addSelect('wh_time.'.$value);
                    }
                }, 'jumlah_dealer', function ($join) use ($request) {
                    $join->on('wh_time.CompanyId', '=', 'jumlah_dealer.CompanyId');
                        foreach ($request->group_by as $value) {
                            $join = $join->on('wh_time.'.$value, '=', 'jumlah_dealer.'.$value);
                        }
                });
                foreach ($request->group_by as $value) {
                    $data  = $data->addSelect('wh_time.'.$value);
                    if($value == 'kd_pack'){
                        $data = $data->addSelect('wh_time.nm_pack');
                    }
                }
                $data  = $data
                ->addSelect(DB::raw("convert(varchar(8), dateadd(second, rata2, '00:00:00'), 108) as rata2_waktu_proses"))
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
