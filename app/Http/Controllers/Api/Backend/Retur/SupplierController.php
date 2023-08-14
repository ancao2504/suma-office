<?php

namespace app\Http\Controllers\Api\Backend\Retur;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $rules = [
                'companyid' => 'required',
                'user_id' => 'required',
                'option' => 'required',
            ];
            $messages = [
                'companyid.required' => 'Companyid Tidak Boleh Kososng',
                'user_id.required' => 'User Id Tidak Boleh Kososng',
                'option.required' => 'Option Tidak Boleh Kososng',
            ];

            if(!empty($request->no_retur)){
                $rules += [
                    'no_retur' => 'required|min:2',
                ];
                $messages += [
                    'no_retur.required' => 'Nomor Retur tidak boleh kosong',
                    'no_retur.min' => 'Nomor Retur Minimal 2 Karakter',
                ];
            }

            $validate = Validator::make($request->all(), $rules,$messages);
            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            
            if(empty($request->no_retur) && in_array('tamp',$request->option)){
                $request->merge(['no_retur' => $request->user_id]);
            }

            if(!in_array($request->per_page, [10,50,100,500])){
                $request->replace(['per_page' => 10]); 
            }

            
            $request->merge(['tb' => ['retur','retur_dtl']]);
            if(in_array('tamp', $request->option)){
                $request->merge(['tb' => ['returtmp','retur_dtltmp']]);
            }

            $data = DB::table(function($query) use ($request){
                $query->select('*')
                ->from($request->tb[0])
                ->where($request->tb[0].'.CompanyId', $request->companyid);
                
                if(!empty($request->no_retur)){
                    $query = $query->where(function($query) use ($request){
                        $query->where($request->tb[0].'.no_retur', 'LIKE', '%'.$request->no_retur.'%')
                        ->orWhere($request->tb[0].'.kd_supp', 'LIKE', '%'.$request->no_retur.'%');
                    });
                } else {
                    $query = $query->whereYear($request->tb[0].'.tglretur', '>=', date('Y')-3);
                }
            }, $request->tb[0]);

            if(in_array('page', $request->option)){
                $data = $data
                ->joinSub(function($query) use ($request){
                    $query->select(
                        'retur_dtl.no_retur',
                        DB::raw('isnull(sum(retur_dtl.jmlretur),0) as jmlretur'),
                        DB::raw('isnull(sum(retur_dtl.qty_jwb),0) as qty_jwb'),
                        'retur_dtl.CompanyId'
                    )
                    ->from($request->tb[1])
                    ->where('retur_dtl.CompanyId',$request->companyid)
                    ->groupBy('retur_dtl.no_retur', 'retur_dtl.CompanyId');
                }, 'detail', function($join){
                    $join->on('detail.no_retur', '=', 'retur.no_retur')
                    ->on('detail.CompanyId', '=', 'retur.CompanyId');
                })
                ->select(
                    $request->tb[0].'.*',
                    'detail.jmlretur',
                    'detail.qty_jwb'
                );

                $data = $data
                ->orderBy('tglretur', 'desc')
                ->orderBy('no_retur', 'desc')
                ->paginate($request->per_page);
            } else if(in_array('first', $request->option)){
                $data = $data->first();

            } else if(in_array('with_detail',$request->option) || in_array('with_jwb',$request->option)){
                $data = $data->first();
                if(!empty($data)){
                    $data_detail = DB::table(function ($query) use ($request) {
                        $query->select(
                                'no_klaim',
                                'tgl_claim',
                                'no_produksi',
                                'no_ps_klaim',
                                'kd_dealer',
                                'kd_part',
                                'kd_lokasi',
                                'jmlretur',
                                'qty_jwb',
                                'ket',
                                'ket_jwb',
                                'diterima',
                                'CompanyId'
                            )
                        ->from($request->tb[1])
                        ->where($request->tb[1].'.CompanyId', $request->companyid);
                    if(!empty($request->no_retur) || in_array($request->option, ['tamp'])){
                        $query->where($request->tb[1].'.no_retur', $request->no_retur);
                    }
                    }, $request->tb[1])
                    ->leftJoinSub(function($query) use ($request){
                        $query->select('part.kd_part', 'part.ket','part.CompanyId','hrg_pokok')
                        ->from('part')
                        ->where('part.CompanyId', $request->companyid);
                    }, 'part', function($join) use ($request){
                        $join->on('part.kd_part', '=', $request->tb[1].'.kd_part')
                        ->on('part.CompanyId', '=', $request->tb[1].'.CompanyId');
                    })
                    ->leftJoinSub(function($query) use ($request){
                        $query->select('rtoko_dtl.no_retur', 'rtoko_dtl.kd_part', 'rtoko_dtl.no_klaim', 'rtoko_dtl.ket','rtoko_dtl.status_end','rtoko_dtl.CompanyId')
                        ->from('rtoko_dtl')
                        ->where('rtoko_dtl.CompanyId', $request->companyid)
                        ->groupBy('rtoko_dtl.no_retur', 'rtoko_dtl.kd_part', 'rtoko_dtl.no_klaim','rtoko_dtl.ket', 'rtoko_dtl.status_end','rtoko_dtl.CompanyId');
                    }, 'rtoko_dtl', function($join) use ($request){
                        $join->on('rtoko_dtl.no_retur', '=', $request->tb[1].'.no_klaim')
                        ->on('rtoko_dtl.kd_part', '=', $request->tb[1].'.kd_part')
                        ->on('rtoko_dtl.CompanyId', '=', $request->tb[1].'.CompanyId');
                    })
                    ->select(
                        $request->tb[1].'.*',
                        'part.ket as nm_part',
                        'part.hrg_pokok',
                        'rtoko_dtl.ket as ket_klaim',
                        'rtoko_dtl.status_end'
                    )
                    ->orderBy($request->tb[1].'.no_klaim', 'desc')
                    ->get();
                    
                    if(in_array('with_jwb', $request->option)){
                        foreach ($data_detail as $key => $value) {
                            $data_detail[$key]->detail_jwb = DB::table('jwb_claim')
                            ->select(
                                '*',
                                DB::raw("'".$data_detail[$key]->status_end."' as status_end")
                                )
                            ->where('jwb_claim.no_retur', $request->no_retur)
                            ->where('jwb_claim.no_klaim', $value->no_klaim)
                            ->where('jwb_claim.kd_part', $value->kd_part)
                            ->where('jwb_claim.CompanyId', $request->companyid)
                            ->orderBy('no_jwb', 'asc')
                            ->get();

                            $detail_jwb = collect($data_detail[$key]->detail_jwb);

                            $data_detail[$key]->qty_jwb = $detail_jwb->sum('qty_jwb');

                            $data_detail[$key]->ket_jwb = 
                                $detail_jwb->where('keputusan','TERIMA')->sum('qty_jwb').' TERIMA ' .
                                $detail_jwb->where('keputusan','TOLAK')->sum('qty_jwb').' TOLAK ';
                        }
                    }

                    $data->detail = $data_detail;
                }
            }
            return Response::responseSuccess('success', $data);
        }catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function store(Request $request)
    {
        try{
            $rules = [
                'companyid' => 'required',
                'user_id' => 'required',
            ];
            $messages = [
                'companyid.required' => 'Companyid Tidak Boleh Kososng',
                'user_id.required' => 'User Id Tidak Boleh Kososng',
            ];

            // ! ------------------------------------
            // ! Jika menambahkan validasi
            if($request->no_retur == $request->user_id){
                $rules += [
                    'kd_supp' => 'required',
                    'tgl_retur' => 'required',
                ];
                $messages += [
                    'kd_supp.required' => 'Kode Supplier Tidak Boleh Kososng',
                    'tgl_retur.required' => 'Tanggal Retur Tidak Boleh Kososng',
                ];
                if(!empty($request->kd_part)){
                    $rules += [
                        'no_klaim' => 'required',
                    ];
                    $messages += [
                        'no_klaim.required' => 'No Klaim Tidak Boleh Kososng',
                    ];
                }
            }

            // ! megecek validasi dan menampilkan pesan error
            // ! ------------------------------------
            $validate = Validator::make($request->all(), $rules,$messages);
            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            $simpan = DB::transaction(function () use ($request) {
                // ! ======================================================
                // ! Simpan Data Tamporeri
                // ! ======================================================
                if($request->no_retur == $request->user_id){
                    if(!empty($request->kd_part)){

                        $a = DB::table(function ($query) use ($request) {
                            $query->select('rtoko.no_retur','rtoko.kd_dealer', 'rtoko_dtl.no_faktur', 'rtoko_dtl.Kd_lokasi', 'rtoko_dtl.jumlah', 'rtoko.tanggal','rtoko.CompanyId')
                            ->from('rtoko')
                            ->where('rtoko.no_retur', $request->no_klaim)
                            ->where('rtoko.CompanyId', $request->companyid)
                            ->JoinSub(function($query) use ($request){
                                $query->select('*')
                                ->from('rtoko_dtl')
                                ->where('rtoko_dtl.no_retur', $request->no_klaim)
                                ->where('rtoko_dtl.kd_part', $request->kd_part)
                                ->where('rtoko_dtl.CompanyId', $request->companyid);
                            }, 'rtoko_dtl', function($join){
                                $join->on('rtoko_dtl.no_retur', '=', 'rtoko.no_retur')
                                ->on('rtoko_dtl.CompanyId', '=', 'rtoko.CompanyId');
                            });
                        }, 'a')
                        ->first();

                        //! ubah status pada rtoko_dtl menjadi 1 dimana agar tidak bisa di klaim lagi
                        DB::table('rtoko_dtl')
                        ->where('rtoko_dtl.no_retur', $request->no_klaim)
                        ->where('rtoko_dtl.kd_part', $request->kd_part)
                        ->where('rtoko_dtl.CompanyId', $request->companyid)
                        ->update([
                            'status' => 1
                        ]);

                        //! simpan pada tabel retur_dtltmp
                        DB::table('retur_dtltmp')
                        ->updateOrInsert([
                            'retur_dtltmp.Kd_Key'        => $request->user_id,
                            'retur_dtltmp.no_retur'      => $request->user_id,
                            'retur_dtltmp.CompanyId'     => $request->companyid,
                            'retur_dtltmp.no_klaim'      => $request->no_klaim,
                            'retur_dtltmp.kd_part'       => $request->kd_part,
                        ], [
                            'kd_dealer'     =>  $a->kd_dealer,
                            'no_faktur'     =>  $a->no_faktur,
                            'no_ps_klaim'   =>  $request->no_ps,
                            'kd_lokasi'     =>  $a->Kd_lokasi,
                            'jmlretur'      =>  $a->jumlah,
                            'ket'           => ($request->ket??null),
                            'diterima'      => ($request->diterima??0),
                            'no_produksi'   => ($request->no_produksi??null),
                            'tgl_pemakaian' => $request->tgl_pemakaian,
                            'tgl_claim'     => $a->tanggal,
                            'usertime'      => (date('Y-m-d H:i:s').'='.$request->user_id)
                        ]);
                    }

                    $b = DB::table(function ($query) use ($request) {
                        $query
                        ->select('retur_dtltmp.Kd_Key', 'retur_dtltmp.no_retur', DB::raw('isnull(sum(retur_dtltmp.jmlretur), 0) as total'))
                        ->from('retur_dtltmp')
                        ->where('retur_dtltmp.no_retur', $request->user_id)
                        ->where('retur_dtltmp.CompanyId', $request->companyid)
                        ->groupBy('retur_dtltmp.Kd_Key', 'retur_dtltmp.no_retur');
                    },'b')
                    ->first();

                    //! simpan pada tabel returtmp 
                    DB::table('returtmp')
                    ->updateOrInsert([
                        'returtmp.Kd_Key'            => $request->user_id,
                        'returtmp.no_retur'          => $request->user_id,
                        'returtmp.CompanyId'         => $request->companyid,
                    ],  [
                        'Kd_supp'           => $request->kd_supp,
                        'tglretur'          => $request->tgl_retur,
                        'total'             => $b->total??0,
                        'sts_jurnal'        => ($request->sts_jurnal??0),
                        'usertime'          => (date('Y-m-d H:i:s').'='.$request->user_id)
                    ]);
                    
                    return (object)[
                        'status'    => 1,
                        'data'      => ''
                    ];
                }

                // ! ======================================================
                // ! Simpan Data
                // ! ======================================================
                $simpan = DB::select("
                SET NOCOUNT ON;
                exec SP_Retur_Simpan1 ?, ?, ?", [
                    date('d-m-Y', strtotime($request->tgl_retur)),
                    $request->companyid,
                    $request->user_id
                ]);
                
                return (object)[
                    'status'    => (int)$simpan[0]->status,
                    'data'      => $simpan[0]->data
                ];
            });

            // ! jika true succes jika false terdapat validasi yang gagal
            if($simpan->status == 1){
                return Response::responseSuccess('success', $simpan->data);
            } else if ($simpan->status == 0){
                return Response::responseWarning($simpan->data, '');
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
                'no_klaim' => 'required',
                'kd_part' => 'required',
            ];
            $messages = [
                'no_klaim.required' => 'No Klaim Tidak Boleh Kososng',
                'kd_part.required' => 'Kode Part Tidak Boleh Kososng',
            ];

            // ! ------------------------------------
            // ! megecek validasi dan menampilkan pesan error
            // ! ------------------------------------
            $validate = Validator::make($request->all(), $rules,$messages);
            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            DB::transaction(function () use ($request) {
                DB::table('retur_dtltmp')
                ->where('no_retur', $request->user_id)
                ->where('no_klaim', $request->no_klaim)
                ->where('kd_part', $request->kd_part)
                ->where('CompanyId', $request->companyid)
                ->delete();

                DB::table('rtoko_dtl')
                ->where('no_retur', $request->no_klaim)
                ->where('kd_part', $request->kd_part)
                ->where('CompanyId', $request->companyid)
                ->update([
                    'status' => 0
                ]);
            });
            return response::responseSuccess('success', '');
        }catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        };
    }
}
