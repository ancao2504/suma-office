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
                'tamp'      => 'required|boolean',
            ];
            $messages = [
                'no_retur.required' => 'no_retur Tidak Boleh Kososng',
                'tamp.required' => 'tamp Tidak Boleh Kososng',
                'tamp.boolean' => 'tamp Tidak Valid',
            ];
            if((boolean)$request->tamp){
                $rules += [
                    'no_klaim' => 'required',
                    'kd_part' => 'required',
                    'qty_jwb' => 'required',
                    'alasan' => 'required|in:RETUR,CA',
                    'keputusan' => 'required|in:TERIMA,TOLAK',
                ];
                $messages += [
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
            }

            // ! megecek validasi dan menampilkan pesan error
            // ! ------------------------------------
            $validate = Validator::make($request->all(), $rules,$messages);
            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }
            
            $simpan = DB::transaction(function () use ($request) {
                // ! Jika tamp true maka akan simpan ke tamp
                if((boolean)$request->tamp){
                    // ! Mengetahui berapa yang sudah di JWB
                    // ! =====================================
                    $jwb = DB::table('jwb_claim')
                    ->select('*')
                    ->where('no_retur', $request->no_retur)
                    ->where('no_klaim', $request->no_klaim)
                    ->where('kd_part', $request->kd_part)
                    ->where('CompanyId', $request->companyid)
                    ->get();
                    // ! Mengetahui berapa yang di ajukan
                    // ! =====================================
                    $retur = DB::table('retur_dtl')
                    ->where('no_retur', $request->no_retur)
                    ->where('no_klaim', $request->no_klaim)
                    ->where('kd_part', $request->kd_part)
                    ->where('CompanyId', $request->companyid)
                    ->select('jmlretur')
                    ->first();
                    // ! Cek yang dijawab sekarang + sebelumnya apakah melebihi qty yang di ajukan
                    // ! =====================================
                    if(
                        (empty($request->no_jwb) && ((float)$request->qty_jwb + (float)(collect($jwb)->sum('qty_jwb')??0)) > (float)$retur->jmlretur) 
                        || 
                        (!empty($request->no_jwb) && ((float)(collect($jwb)->sum('qty_jwb')??0) - (float)$request->qty_jwb) > (float)$retur->jmlretur)
                    ){
                        return (object) array('status' => 0, 'message' => 'Jawaban melebihi jumlah klaim');
                    }
                    // ! data yangakan di simpan atau update
                    $data = [
                        'no_retur'          => $request->no_retur,
                        'no_klaim'          => trim($request->no_klaim),
                        'kd_part'           => trim($request->kd_part),
                        'tgl_jwb'           => date('Y-m-d H:i:s'),
                        'qty_jwb'           => $request->qty_jwb,
                        'alasan'            => $request->alasan,
                        'ca'                => ($request->alasan=="CA")?$request->ca:null,
                        'keputusan'         => $request->keputusan,
                        'ket'               => $request->ket,
                        'usertime'          => (string)(date('Y-m-d H:i:s').'='.$request->user_id),
                        'CompanyId'         => $request->companyid
                    ];
                    // ! cek jika no_jwb sudah ada
                    // ! =====================================
                    if( !empty($request->no_jwb) && collect($jwb)->where('no_jwb',$request->no_jwb)->isNotEmpty()){
                        $data += [
                            'no_jwb'            => $request->no_jwb,
                        ];
                        // ! Juka sudah ada maka update
                        // ! =====================================
                        DB::table('jwb_claim')
                        ->where('no_jwb', $request->no_jwb)
                        ->where('no_retur', $request->no_retur)
                        ->where('no_klaim', $request->no_klaim)
                        ->where('kd_part', $request->kd_part)
                        ->where('CompanyId', $request->companyid)
                        ->update($data);
                    } else {
                        $data += [
                            'no_jwb'            => ((collect($jwb)->max('no_jwb')??0) + 1),
                        ];
                        // ! Jika belum ada maka insert
                        // ! =====================================
                        DB::table('jwb_claim')
                        ->insert($data);
                    }
                    // ! Mengambilkembali jumlah jwb dab jml tolak atupun terima
                    // ! =====================================
                    $cek = DB::table('jwb_claim')
                    ->where('no_retur', $request->no_retur)
                    ->where('no_klaim', $request->no_klaim)
                    ->where('kd_part', $request->kd_part)
                    ->where('CompanyId', $request->companyid)
                    ->select(
                        DB::raw('isnull(sum(qty_jwb), 0) as qty_jwb'), 
                        DB::raw("isnull(sum(case when keputusan = 'TERIMA' then qty_jwb else null end), 0) as terima"),
                        DB::raw("isnull(sum(case when keputusan = 'TOLAK' then qty_jwb else null end), 0) as tolak")
                    )
                    ->first();
                    // ! Jika tidak mengalami masalah maka akan return success
                    // ! =====================================
                    $data = [
                        'qty'   => $cek->qty_jwb,
                        'ket'       => $cek->terima . ' TERIMA '.$cek->tolak.' TOLAK ',
                        'detail'    => $data
                    ];
                    return (object) array('status' => 1, 'message' => 'success', 'data' => $data);
                }
                // ! Jika inggin simpan Semua Jawaban dan menerapkan memo dna minimum
                // ! =========================================
                $simpan = DB::select('
                SET NOCOUNT ON;
                exec SP_jwb_claim_simpan ?, ?, ?, ?', [
                    $request->user_id,
                    $request->no_retur,
                    $request->companyid,
                    date('d-m-Y')
                ]);
                return (object)[
                    'status'    => (int)$simpan[0]->status,
                    'message'   => $simpan[0]->message,
                    'data'      => $simpan[0]->data
                ];
            });

            if($simpan->status == 1){
                return Response::responseSuccess($simpan->message,$simpan->data);
            }else{
                return Response::responseWarning($simpan->message);
            }

        }catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    
    public function destroy(Request $request)
    {
        try {
            $rules = [
                'no_retur' => 'required',
                'no_klaim' => 'required',
                'kd_part' => 'required',
                'no_jwb' => 'required',
            ];
            $messages = [
                'no_retur.required' => 'no_retur Todak boleh kosong',
                'no_klaim.required' => 'no_klaim Todak boleh kosong',
                'kd_part.required' => 'kd_part Todak boleh kosong',
                'no_jwb.required' => 'no_jwb Todak boleh kosong',
            ];

            // ! ------------------------------------
            // ! megecek validasi dan menampilkan pesan error
            // ! ------------------------------------
            $validate = Validator::make($request->all(), $rules,$messages);
            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }
            DB::transaction(function () use ($request) {
                DB::table('jwb_claim')
                ->where('no_retur', $request->no_retur)
                ->where('no_klaim', $request->no_klaim)
                ->where('kd_part', $request->kd_part)
                ->where('no_jwb', $request->no_jwb)
                ->where('CompanyId', $request->companyid)
                ->delete();
            });

            $cek = DB::table('jwb_claim')
            ->where('no_retur', $request->no_retur)
            ->where('no_klaim', $request->no_klaim)
            ->where('kd_part', $request->kd_part)
            ->where('CompanyId', $request->companyid)
            ->select(
                DB::raw('isnull(sum(qty_jwb), 0) as qty_jwb'), 
                DB::raw("isnull(sum(case when keputusan = 'TERIMA' then qty_jwb else null end), 0) as terima"),
                DB::raw("isnull(sum(case when keputusan = 'TOLAK' then qty_jwb else null end), 0) as tolak")
            )
            ->first();
            
            return response::responseSuccess('success', [
                'qty'   => $cek->qty_jwb,
                'ket'   => $cek->terima . ' TERIMA '.$cek->tolak.' TOLAK '
            ]);
        }catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        };
    }
}
