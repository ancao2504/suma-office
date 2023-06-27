<?php

namespace app\Http\Controllers\Api\Backend\Reports\Retur;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class KonsumenController extends Controller
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

            $data = DB::table('rtoko')
                    ->join('rtoko_dtl', function ($join) {
                        $join->on('rtoko.no_retur', '=', 'rtoko_dtl.no_retur')
                            ->on('rtoko.CompanyId', '=', 'rtoko_dtl.CompanyId');
                    })->select('rtoko.no_retur', 'rtoko_dtl.kd_part', 'rtoko_dtl.jumlah as qty_claim', 'rtoko_dtl.ket', 'rtoko_dtl.status');

            $data->leftJoinSub(function ($query) use ($request) {
                $query->select('faktur.no_faktur', 'faktur.kd_sales', 'faktur.kd_dealer', 'faktur.CompanyId', 'fakt_dtl.kd_part','fakt_dtl.jml_jual')
                    ->from('faktur')
                    ->join('fakt_dtl', 'faktur.no_faktur', '=', 'fakt_dtl.no_faktur')
                    ->where('faktur.CompanyId', $request->companyid);
                    if(!empty($request->no_faktur)){
                        $query = $query->where('faktur.no_faktur', $request->no_faktur);
                    }
                    if(!empty($request->kd_part)){
                        $query = $query->where('fakt_dtl.kd_part', $request->kd_part);
                    }
                    if(!empty($request->kd_sales)){
                        $query = $query->where('faktur.kd_sales', $request->kd_sales);
                    }
                    if(!empty($request->kd_dealer)){
                        $query = $query->where('faktur.kd_dealer', $request->kd_dealer);
                    }
            }, 'faktur', function ($join) {
                $join->on('rtoko.CompanyId', '=', 'faktur.CompanyId')
                    ->on('rtoko_dtl.no_faktur', '=', 'faktur.no_faktur')
                    ->on('rtoko_dtl.kd_part', '=', 'faktur.kd_part');
            });
            $data->addSelect(
                DB::raw('rtoko_dtl.no_faktur'),
                DB::raw('ISNULL(rtoko.kd_sales, ISNULL(faktur.kd_sales, NULL)) AS kd_sales'),
                DB::raw('ISNULL(rtoko.kd_dealer, ISNULL(faktur.kd_dealer, NULL)) AS kd_dealer'),
                DB::raw('ISNULL(rtoko_dtl.qty_faktur, ISNULL(faktur.jml_jual, NULL)) AS qty_dikirim')
            )
            ->where('rtoko.CompanyId', $request->companyid);

            if(!empty($request->no_faktur)){
                $data = $data->where('rtoko_dtl.no_faktur', $request->no_faktur);
            }
            if(!empty($request->kd_part)){
                $data = $data->where('rtoko_dtl.kd_part', $request->kd_part);
            }
            if(!empty($request->kd_sales)){
                $data = $data->where('rtoko.kd_sales', $request->kd_sales);
            }
            if(!empty($request->kd_dealer)){
                $data = $data->where('rtoko.kd_dealer', $request->kd_dealer);
            }
            if(!empty($request->tgl_claim)){
                $data = $data->whereBetween(DB::raw('CONVERT(DATE, rtoko.tanggal)'), $request->tgl_claim);
            }
            if(!empty($request->tgl_terima)){
                $data = $data->whereBetween(DB::raw('CONVERT(DATE, rtoko.tgl_terima)'), $request->tgl_terima);
            }
            if(!empty($request->sts)){
                $data = $data->where('rtoko_dtl.status', $request->sts);
            }

            $data = $data->orderBy('rtoko.tanggal', 'asc')
            ->paginate($request->per_page);
            
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
            $request->only(['tgl_claim', 'tgl_terima', 'kd_sales', 'kd_dealer', 'no_faktur', 'kd_part', 'sts']);

            $data = DB::table('rtoko')
            ->join('rtoko_dtl', function ($join) {
                $join->on('rtoko.no_retur', '=', 'rtoko_dtl.no_retur')
                    ->on('rtoko.CompanyId', '=', 'rtoko_dtl.CompanyId');
            })->select('rtoko.no_retur', 'rtoko.tanggal as tgl_retur', 'rtoko_dtl.no_faktur', 'faktur.tgl_faktur');

            $data->leftJoinSub(function ($query) use ($request) {
                $query->select('faktur.no_faktur', 'faktur.tgl_faktur','faktur.kd_sales', 'faktur.kd_dealer', 'faktur.CompanyId', 'fakt_dtl.kd_part','fakt_dtl.jml_jual')
                    ->from('faktur')
                    ->join('fakt_dtl', 'faktur.no_faktur', '=', 'fakt_dtl.no_faktur')
                    ->where('faktur.CompanyId', $request->companyid);
                    if(!empty($request->no_faktur)){
                        $query = $query->where('faktur.no_faktur', $request->no_faktur);
                    }
                    if(!empty($request->kd_part)){
                        $query = $query->where('fakt_dtl.kd_part', $request->kd_part);
                    }
                    if(!empty($request->kd_sales)){
                        $query = $query->where('faktur.kd_sales', $request->kd_sales);
                    }
                    if(!empty($request->kd_dealer)){
                        $query = $query->where('faktur.kd_dealer', $request->kd_dealer);
                    }
            }, 'faktur', function ($join) {
                $join->on('rtoko.CompanyId', '=', 'faktur.CompanyId')
                    ->on('rtoko_dtl.no_faktur', '=', 'faktur.no_faktur')
                    ->on('rtoko_dtl.kd_part', '=', 'faktur.kd_part');
            })
            ->addSelect(
                DB::raw('ISNULL(rtoko.kd_sales, ISNULL(faktur.kd_sales, NULL)) AS kd_sales'),
                DB::raw('ISNULL(rtoko.kd_dealer, ISNULL(faktur.kd_dealer, NULL)) AS kd_dealer')
            )
            ->addSelect('rtoko_dtl.kd_part', 'rtoko_dtl.jumlah as qty_claim')
            ->addSelect(DB::raw('ISNULL(rtoko_dtl.qty_faktur, ISNULL(faktur.jml_jual, null)) AS qty_dikirim'))
            ->addSelect('rtoko_dtl.ket', 'rtoko_dtl.status')
            ->where('rtoko.CompanyId', $request->companyid);

            if(!empty($request->no_faktur)){
                $data = $data->where('rtoko_dtl.no_faktur', $request->no_faktur);
            }
            if(!empty($request->kd_part)){
                $data = $data->where('rtoko_dtl.kd_part', $request->kd_part);
            }
            if(!empty($request->kd_sales)){
                $data = $data->where('rtoko.kd_sales', $request->kd_sales);
            }
            if(!empty($request->kd_dealer)){
                $data = $data->where('rtoko.kd_dealer', $request->kd_dealer);
            }
            if(!empty($request->tgl_claim)){
                $data = $data->whereBetween(DB::raw('CONVERT(DATE, rtoko.tanggal)'), $request->tgl_claim);
            }
            if(!empty($request->tgl_terima)){
                $data = $data->whereBetween(DB::raw('CONVERT(DATE, rtoko.tgl_terima)'), $request->tgl_terima);
            }
            if(!empty($request->sts)){
                $data = $data->where('rtoko_dtl.status', $request->sts);
            }

            $data = $data->orderBy('rtoko.tanggal', 'asc')->get();

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
