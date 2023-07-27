<?php

namespace app\Http\Controllers\Api\Backend\Retur;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;

class SupplierJawabController extends Controller
{
    public function store(Request $request)
    {
        try{
            $rules = [
                'no_retur' => 'required',
                'no_klaim' => 'required',
                'kd_part' => 'required',
                'qty_jwb' => 'required',
                'alasan' => 'required|in:RETUR,CA',
                'keputusan' => 'required|in:TERIMA,TOLAK',
            ];
            $messages = [
                'no_retur.required' => 'no_retur Tidak Boleh Kososng',
                'no_klaim.required' => 'no_klaim Tidak Boleh Kososng',
                'kd_part.required'  => 'kd_part Tidak Boleh Kososng',
                'qty_jwb.required' => 'Qty Jawab Tidak Boleh Kososng',
                'alasan.required' => 'Alasan Tidak Boleh Kososng',
                'alasan.in'  => 'Alasan Tidak Valid',
                'keputusan.required'  => 'Keputusan Tidak Boleh Kososng',
                'keputusan.in'  => 'Keputusan Tidak Valid',
            ];

            if($request->alasan == 'CA'){
                $rules += ['ca' => 'required'];
                $messages += ['ca.required'  => 'Jumlah Uang Tidak Boleh Kososng'];
            }

            // ! megecek validasi dan menampilkan pesan error
            // ! ------------------------------------
            $validate = Validator::make($request->all(), $rules,$messages);
            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            $simpan = DB::transaction(function () use ($request) {
                $no_jwb = DB::table('jwb_claim')
                ->where('no_retur', $request->no_retur)
                ->where('no_klaim', $request->no_klaim)
                ->where('kd_part', $request->kd_part)
                ->select(
                    DB::raw('isnull(max(no_jwb), 0) as no_jwb'),
                    DB::raw('isnull(sum(qty_jwb), 0) as qty_jwb'), 
                    DB::raw("isnull(sum(case when keputusan = 'TERIMA' then qty_jwb else null end), 0) as terima"),
                    DB::raw("isnull(sum(case when keputusan = 'TOLAK' then qty_jwb else null end), 0) as tolak")
                )
                ->first();

                if(!empty($request->keputusan) && $request->keputusan == 'TERIMA'){
                    $no_jwb->terima = (float)$no_jwb->terima + (float)$request->qty_jwb;
                } else if(!empty($request->keputusan) && $request->keputusan == 'TOLAK'){
                    $no_jwb->tolak = (float)$no_jwb->tolak + (float)$request->qty_jwb;
                }

                $retur = DB::table('rtoko_dtl')
                ->where('no_retur', $request->no_klaim)
                ->where('kd_part', $request->kd_part)
                ->where('CompanyId', $request->companyid)
                ->select('jumlah')
                ->first();

                if($request->qty_jwb > $retur->jumlah){

                    return (object) array('status' => 1, 'message' => 'Qty Jawab melebihi jumlah klaim');
                }
                
                if(((float)$request->qty_jwb + (float)$no_jwb->qty_jwb) > $retur->jumlah){

                    return (object) array('status' => 1, 'message' => 'Jawaban sudah memenuhi jumlah klaim');
                }

                // ! ======================================================
                // ! Simpan Data
                // ! ======================================================

                DB::table('jwb_claim')
                ->insert([
                    'no_jwb'            => $no_jwb->no_jwb + 1,
                    'no_retur'          => $request->no_retur,
                    'no_klaim'          => trim($request->no_klaim),
                    'kd_part'           => trim($request->kd_part),
                    'tgl_jwb'           => date('Y-m-d H:i:s'),
                    'qty_jwb'           => $request->qty_jwb,
                    'alasan'            => $request->alasan,
                    'ca'                => $request->ca,
                    'keputusan'         => $request->keputusan,
                    'ket'               => $request->ket,
                    'usertime'          => (string)(date('Y-m-d H:i:s').'='.$request->user_id),
                    'CompanyId'         => $request->companyid,
                ]);
                
                if(($request->qty_jwb + $no_jwb->qty_jwb) == $retur->jumlah){
                    // ! cek pada klaim apakah sudah terjawab semua jika iya maka retur status_end = 1
                    $klaim = DB::table(function($query) use ($request){
                        $query->select('rtoko_dtl.*', 'rtoko.tanggal', 'rtoko.kd_dealer', 'rtoko.kd_sales', 'rtoko.total', 'rtoko.sts_jurnal', 'rtoko.terbayar', 'rtoko.tgl_terima')
                        ->from('rtoko')
                        ->joinSub(function($query) use ($request){
                            $query->select('*')
                            ->from('rtoko_dtl')
                            ->where('no_retur', '=', $request->no_klaim)
                            ->where('CompanyId', '=', $request->companyid);
                        }, 'rtoko_dtl', function($join){
                            $join->on('rtoko.no_retur', '=', 'rtoko_dtl.no_retur')
                            ->on('rtoko.CompanyId', '=', 'rtoko_dtl.CompanyId');
                        })
                        ->where('rtoko.no_retur', '=', $request->no_klaim)
                        ->where('rtoko.CompanyId', '=', $request->companyid);
                    }, 'rtoko')
                    ->leftJoinSub(function($query) use ($request){
                        $query->select('*')
                        ->from('jwb_claim')
                        ->where('no_klaim', '=', $request->no_klaim)
                        ->where('CompanyId', '=', $request->companyid);
                    }, 'jwb_claim', function($join){
                        $join->on('rtoko.no_retur', '=', 'jwb_claim.no_klaim')
                        ->on('rtoko.kd_part', '=', 'jwb_claim.kd_part')
                        ->on('rtoko.CompanyId', '=', 'jwb_claim.CompanyId');
                    })
                    ->select('rtoko.no_retur', 'rtoko.kd_part', 'rtoko.jumlah', DB::raw('isnull(jwb_claim.qty_jwb,0) as qty_jwb'))
                    ->where('rtoko.jumlah', '<>', DB::raw('isnull(jwb_claim.qty_jwb,0)'))
                    ->get();

                    //! cek jika kosong maka klaim status_end = 1
                    if(count($klaim) == 0){
                        DB::table('klaim')
                        ->where('no_dokumen', $request->no_klaim)
                        ->where('companyid', $request->companyid)
                        ->update([
                            'status_end' => 1,
                        ]);
                    }
                }

                $jwb = (object)[
                    'qty_jwb' => ((float)$no_jwb->qty_jwb + (float)$request->qty_jwb),
                    'ket_jwb' => (($no_jwb->terima > 0 ? $no_jwb->terima.' TERIMA ' : '').($no_jwb->tolak > 0 ? $no_jwb->tolak.' TOLAK' : '')),
                ];

                DB::table('retur_dtl')
                ->where('no_retur', $request->no_retur)
                ->where('no_klaim', $request->no_klaim)
                ->where('kd_part', $request->kd_part)
                ->update([
                    'qty_jwb' => $jwb->qty_jwb,
                    'ket_jwb' => $jwb->ket_jwb,
                ]);

                return (object) array(
                    'status' => 0, 
                    'message' => 'success', 
                    'data' => (object)[
                                'qty_jwb' => $jwb->qty_jwb,
                                'ket_jwb' => $jwb->ket_jwb,
                            ]
                );
            });

            if($simpan->status == 0){
                return Response::responseSuccess($simpan->message, $simpan->data);
            }else{
                return Response::responseWarning($simpan->message);
            }

        }catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
