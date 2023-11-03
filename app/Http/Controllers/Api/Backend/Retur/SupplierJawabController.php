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
                    $simpan = DB::select('
                    SET NOCOUNT ON;
                    exec SP_jwb_claim_simpanTemp ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', [
                        $request->no_retur,
                        trim($request->no_klaim),
                        trim($request->kd_part),
                        date('Y-m-d H:i:s'),
                        $request->qty_jwb,
                        $request->alasan,
                        ($request->alasan=="CA")?$request->ca:0,
                        $request->keputusan,
                        $request->ket??null,
                        $request->companyid,
                        $request->user_id
                    ]);

                    if($simpan[0]->status == 0){
                        return (object)[
                            'status'    => (int)$simpan[0]->status,
                            'message'   => $simpan[0]->message,
                            'data'      => ''
                        ];
                    }

                    // ! Mengambilkembali jumlah jwb dan jml tolak atupun terima
                    // ! =====================================
                    $cek = DB::table('jwb_claim')
                    ->where('no_retur', $request->no_retur)
                    ->where('no_klaim', $request->no_klaim)
                    ->where('kd_part', $request->kd_part)
                    ->where('CompanyId', $request->companyid)
                    ->select(
                        'CompanyId',
                        'alasan',
                        'ca',
                        'kd_part',
                        'keputusan',
                        'ket',
                        'no_jwb',
                        'no_klaim',
                        'no_retur',
                        'qty_jwb',
                        'tgl_jwb',
                        'usertime',
                        'sts_end',
                    )->get();
                    // ! Jika tidak mengalami masalah maka akan return success
                    // ! =====================================
                    $data = [
                        'qty'   => collect($cek)->sum('qty_jwb'),
                        'ket'    => collect($cek)->where('keputusan', 'TERIMA')->sum('qty_jwb').' TERIMA '.collect($cek)->where('keputusan', 'TOLAK')->sum('qty_jwb').' TOLAK ',
                        'detail_jwb'    => collect($cek)->map(function($item){
                            return [
                                'CompanyId' => $item->CompanyId,
                                'alasan' => $item->alasan,
                                'ca' => $item->ca,
                                'kd_part' => $item->kd_part,
                                'keputusan' => $item->keputusan,
                                'ket' => $item->ket,
                                'no_jwb' => $item->no_jwb,
                                'no_klaim' => $item->no_klaim,
                                'no_retur' => $item->no_retur,
                                'qty_jwb' => $item->qty_jwb,
                                'tgl_jwb' => $item->tgl_jwb,
                                'usertime' => $item->usertime,
                                'sts_end' => $item->sts_end,
                            ];
                        })->sortBy('no_jwb')->values()->all()
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
