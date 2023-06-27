<?php

namespace App\Http\Controllers\Api\Backend\Reports;

use App\Exports\Faktur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class FakturController extends Controller
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

            $data = DB::table('faktur')
            ->select('faktur.companyid', 'faktur.no_faktur', 'faktur.tgl_faktur', 'faktur.spv', 'faktur.kd_sales', 'faktur.kd_dealer', 'dealer.nm_dealer', 'dealer.kota', 'produk.kd_produk', 'sub.kd_sub', 'faktur.kd_part', 'faktur.jml_order', 'faktur.jml_jual', DB::raw('iif(isnull(faktur_total.faktur_total, 0) <= 0,isnull(faktur.total, 0),isnull(faktur.total, 0) - round(((isnull(faktur.DiscRp, 0) / isnull(faktur_total.faktur_total, 0))), 0) - round(((isnull(faktur.DiscRp1, 0) / isnull(faktur_total.faktur_total, 0))), 0)) as total'))
            ->from(function ($query) use ($request) {
                $query->select('faktur.companyid', 'faktur.no_faktur', 'faktur.tgl_faktur', 'faktur.kd_sales', 'faktur.kd_dealer', 'faktur.spv', 'fakt_dtl.kd_part', 'faktur.discrp', 'faktur.discrp1', 'fakt_dtl.jml_order', 'fakt_dtl.jml_jual', DB::raw('isnull(fakt_dtl.jumlah, 0) - round((isnull(fakt_dtl.jumlah, 0) * isnull(faktur.disc2, 0)) / 100, 0) as total'))
                ->from(function ($query) use ($request) {
                    $query->select('faktur.companyid', 'faktur.no_faktur', 'faktur.tgl_faktur', 'faktur.kd_sales', 'faktur.kd_dealer', 'faktur.disc2', 'faktur.discrp', 'faktur.discrp1', 'salesman.spv')
                    ->from('faktur')
                    ->leftJoin('salesman', function ($join) {
                        $join->on('faktur.kd_sales', '=', 'salesman.kd_sales')
                            ->on('faktur.CompanyId', '=', 'salesman.CompanyId');
                    })
                    ->where('faktur.companyid', $request->companyid);
                    if (!empty($request->tgl_faktur)) {
                        $query = $query->whereBetween(DB::raw('convert(date, faktur.tgl_faktur)'), $request->tgl_faktur);
                    }
                    if (!empty($request->kd_sales)) {
                        $query = $query->where('faktur.kd_sales', $request->kd_sales);
                    }
                }, 'faktur')
                ->leftJoin('fakt_dtl', function ($join) {
                    $join->on('faktur.no_faktur', '=', 'fakt_dtl.no_faktur')
                        ->on('faktur.CompanyId', '=', 'fakt_dtl.CompanyId');
                })
                ->where('fakt_dtl.jml_jual', '>', 0);
            }, 'faktur')
            ->leftJoinSub(function ($query) use ($request){
                $query->select('faktur.companyid', 'faktur.no_faktur', DB::raw('count(fakt_dtl.no_faktur) as faktur_total'))
                ->from(function ($query) use ($request){
                    $query->select('faktur.companyid', 'faktur.no_faktur', 'salesman.kd_sales')
                    ->from('faktur')
                    ->leftJoin('salesman', function ($join) {
                        $join->on('faktur.kd_sales', '=', 'salesman.kd_sales')
                            ->on('faktur.CompanyId', '=', 'salesman.CompanyId');
                    })
                    ->where('faktur.companyid', $request->companyid);
                    if (!empty($request->tgl_faktur)) {
                        $query = $query->whereBetween(DB::raw('convert(date, faktur.tgl_faktur)'), $request->tgl_faktur);
                    }
                    if (!empty($request->kd_sales)) {
                        $query = $query->where('faktur.kd_sales', $request->kd_sales);
                    }
                    $query = $query->where(function ($query) {
                        $query->where('faktur.discrp', '>', 0)
                            ->orWhere('faktur.discrp1', '>', 0);
                    });
                }, 'faktur')
                ->leftJoin('fakt_dtl', function ($join) {
                    $join->on('faktur.no_faktur', '=', 'fakt_dtl.no_faktur')
                        ->on('faktur.CompanyId', '=', 'fakt_dtl.CompanyId');
                })
                ->where('fakt_dtl.jml_jual', '>', 0)
                ->groupBy('faktur.companyid', 'faktur.no_faktur');
            }, 'faktur_total', function ($join) {
                $join->on('faktur.no_faktur', '=', 'faktur_total.no_faktur')
                    ->on('faktur.CompanyId', '=', 'faktur_total.CompanyId');
            })
            ->leftJoin('dealer', function ($join) {
                $join->on('faktur.kd_dealer', '=', 'dealer.kd_dealer')
                    ->on('faktur.CompanyId', '=', 'dealer.CompanyId');
            })
            ->leftJoin('part', function ($join) {
                $join->on('faktur.kd_part', '=', 'part.kd_part')
                    ->on('faktur.CompanyId', '=', 'part.CompanyId');
            })
            ->leftJoin('sub', function ($join) {
                $join->on('part.kd_sub', '=', 'sub.kd_sub');
            })
            ->leftJoin('produk', function ($join) {
                $join->on('sub.kd_produk', '=', 'produk.kd_produk');
            })
            ->whereNotNull('faktur.companyid');
            if (!empty($request->kd_produk)) {
                $data = $data->where('produk.kd_produk', $request->kd_produk);
            }
            $data = $data->orderBy('faktur.tgl_faktur', 'asc')
            ->orderBy('faktur.no_faktur', 'asc')->paginate($request->per_page);

            
            return Response()->json([
                'status'    => 1,
                'message'   => 'success',
                'data'      => $data
            ], 200);
        } catch (\Exception $e) {
            return Response()->json([
                'status'    => 0,
                'message'   => $e,
                'data'      => ''
            ], 200);
        }
    }

    
    public function export(Request $request)
    {
        try {
            $request->only([
                'tgl_faktur',
                'kd_sales',
                'kd_produk'
            ]);

            $data = DB::table('faktur')
            ->select('faktur.companyid', 'faktur.no_faktur', 'faktur.tgl_faktur', 'faktur.spv', 'faktur.kd_sales', 'faktur.kd_dealer', 'dealer.nm_dealer', 'dealer.kota', 'produk.kd_produk', 'sub.kd_sub', 'faktur.kd_part', 'faktur.jml_order', 'faktur.jml_jual', DB::raw('iif(isnull(faktur_total.faktur_total, 0) <= 0,isnull(faktur.total, 0),isnull(faktur.total, 0) - round(((isnull(faktur.DiscRp, 0) / isnull(faktur_total.faktur_total, 0))), 0) - round(((isnull(faktur.DiscRp1, 0) / isnull(faktur_total.faktur_total, 0))), 0)) as total'))
            ->from(function ($query) use ($request) {
                $query->select('faktur.companyid', 'faktur.no_faktur', 'faktur.tgl_faktur', 'faktur.kd_sales', 'faktur.kd_dealer', 'faktur.spv', 'fakt_dtl.kd_part', 'faktur.discrp', 'faktur.discrp1', 'fakt_dtl.jml_order', 'fakt_dtl.jml_jual', DB::raw('isnull(fakt_dtl.jumlah, 0) - round((isnull(fakt_dtl.jumlah, 0) * isnull(faktur.disc2, 0)) / 100, 0) as total'))
                ->from(function ($query) use ($request) {
                    $query->select('faktur.companyid', 'faktur.no_faktur', 'faktur.tgl_faktur', 'faktur.kd_sales', 'faktur.kd_dealer', 'faktur.disc2', 'faktur.discrp', 'faktur.discrp1', 'salesman.spv')
                    ->from('faktur')
                    ->leftJoin('salesman', function ($join) {
                        $join->on('faktur.kd_sales', '=', 'salesman.kd_sales')
                            ->on('faktur.CompanyId', '=', 'salesman.CompanyId');
                    })
                    ->where('faktur.companyid', $request->companyid);
                    if (!empty($request->tgl_faktur)) {
                        $query = $query->whereBetween(DB::raw('convert(date, faktur.tgl_faktur)'), $request->tgl_faktur);
                    }
                    if (!empty($request->kd_sales)) {
                        $query = $query->where('faktur.kd_sales', $request->kd_sales);
                    }
                }, 'faktur')
                ->leftJoin('fakt_dtl', function ($join) {
                    $join->on('faktur.no_faktur', '=', 'fakt_dtl.no_faktur')
                        ->on('faktur.CompanyId', '=', 'fakt_dtl.CompanyId');
                })
                ->where('fakt_dtl.jml_jual', '>', 0);
            }, 'faktur')
            ->leftJoinSub(function ($query) use ($request){
                $query->select('faktur.companyid', 'faktur.no_faktur', DB::raw('count(fakt_dtl.no_faktur) as faktur_total'))
                ->from(function ($query) use ($request){
                    $query->select('faktur.companyid', 'faktur.no_faktur', 'salesman.kd_sales')
                    ->from('faktur')
                    ->leftJoin('salesman', function ($join) {
                        $join->on('faktur.kd_sales', '=', 'salesman.kd_sales')
                            ->on('faktur.CompanyId', '=', 'salesman.CompanyId');
                    })
                    ->where('faktur.companyid', $request->companyid);
                    if (!empty($request->tgl_faktur)) {
                        $query = $query->whereBetween(DB::raw('convert(date, faktur.tgl_faktur)'), $request->tgl_faktur);
                    }
                    if (!empty($request->kd_sales)) {
                        $query = $query->where('faktur.kd_sales', $request->kd_sales);
                    }
                    $query = $query->where(function ($query) {
                        $query->where('faktur.discrp', '>', 0)
                            ->orWhere('faktur.discrp1', '>', 0);
                    });
                }, 'faktur')
                ->leftJoin('fakt_dtl', function ($join) {
                    $join->on('faktur.no_faktur', '=', 'fakt_dtl.no_faktur')
                        ->on('faktur.CompanyId', '=', 'fakt_dtl.CompanyId');
                })
                ->where('fakt_dtl.jml_jual', '>', 0)
                ->groupBy('faktur.companyid', 'faktur.no_faktur');
            }, 'faktur_total', function ($join) {
                $join->on('faktur.no_faktur', '=', 'faktur_total.no_faktur')
                    ->on('faktur.CompanyId', '=', 'faktur_total.CompanyId');
            })
            ->leftJoin('dealer', function ($join) {
                $join->on('faktur.kd_dealer', '=', 'dealer.kd_dealer')
                    ->on('faktur.CompanyId', '=', 'dealer.CompanyId');
            })
            ->leftJoin('part', function ($join) {
                $join->on('faktur.kd_part', '=', 'part.kd_part')
                    ->on('faktur.CompanyId', '=', 'part.CompanyId');
            })
            ->leftJoin('sub', function ($join) {
                $join->on('part.kd_sub', '=', 'sub.kd_sub');
            })
            ->leftJoin('produk', function ($join) {
                $join->on('sub.kd_produk', '=', 'produk.kd_produk');
            })
            ->whereNotNull('faktur.companyid');
            if (!empty($request->kd_produk)) {
                $data = $data->where('produk.kd_produk', $request->kd_produk);
            }
            $data = $data->orderBy('faktur.tgl_faktur')
            ->orderBy('faktur.no_faktur', 'asc')->get();

            return Response()->json([
                'status'    => 1,
                'message'   => 'success',
                'data'      => $data
            ], 200);
                
        } catch (\Exception $e) {
            return Response()->json([
                'status'    => 0,
                'message'   => 'Maaf, terjadi kesalahan. Silahkan coba lagi',
                'data'      => ''
            ], 200);
        }
    }

}
