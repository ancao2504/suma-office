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
            // $rules = [];
            // $messages = [];
            // if(!empty($request->option)){
            //     $rules += [
            //         'no_retur' => 'required|min:5',
            //     ];
            //     $messages += [
            //         'no_retur.required' => 'Nomor Retur tidak boleh kosong',
            //         'no_retur.min' => 'Nomor Retur Minimal 5 Karakter',
            //     ];
            // }

            // $validate = Validator::make($request->all(), $rules,$messages);
            // if ($validate->fails()) {
            //     return Response::responseWarning($validate->errors()->first());
            // }

            
            if(empty($request->no_retur) && (!empty($request->option[1]) && $request->option[1] == 'tamp')){
                $request->merge(['no_retur' => $request->user_id]);
            }

            if(!in_array($request->per_page, [10,50,100,500])){
                $request->replace(['per_page' => 10]); 
            }

            
            $request->merge(['tb' => ['retur','retur_dtl']]);
            if(!empty($request->option[1]) && $request->option[1] == 'tamp'){
                $request->merge(['tb' => ['returtmp','retur_dtltmp']]);
            }

            $data = DB::table($request->tb[0])
            ->lock('with (nolock)')->select('*')
            ->where($request->tb[0].'.CompanyId', $request->companyid);

            if(!empty($request->no_retur)){
                $data = $data->where($request->tb[0].'.no_retur', 'LIKE', '%'.$request->no_retur.'%');
            }

            if(!empty($request->option[1]) && $request->option[1] == 'tamp'){
                $data = $data->where($request->tb[0].'.Kd_Key', $request->user_id);
            }

            if($request->option[0] == 'page'){
                $data = $data
                ->orderBy('tglretur', 'desc')
                ->paginate($request->per_page);

            } else if($request->option[0] == 'first'){
                $data = $data->first();

            } else if($request->option[0] == 'with_detail' || $request->option[0] == 'with_jwb'){
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
                        if(!empty($request->option[1]) && $request->option[1] == 'tamp'){
                            $query->where($request->tb[1].'.kd_key', $request->user_id);
                        }
                        if(!empty($request->no_retur)){
                            $query->where($request->tb[1].'.no_retur', $request->no_retur);
                        }
                    }, $request->tb[1])
                    ->leftJoinSub(function($query) use ($request){
                        $query->select('part.kd_part', 'part.ket','part.CompanyId')
                        ->from('part')
                        ->where('part.CompanyId', $request->companyid);
                    }, 'part', function($join) use ($request){
                        $join->on('part.kd_part', '=', $request->tb[1].'.kd_part')
                        ->on('part.CompanyId', '=', $request->tb[1].'.CompanyId');
                    })
                    ->select(
                        $request->tb[1].'.*',
                        'part.ket as nm_part',
                    )
                    ->orderBy($request->tb[1].'.no_klaim', 'desc')
                    ->get();
                    
                    if($request->option[0] == 'with_jwb'){
                        foreach ($data_detail as $key => $value) {
                            $data_detail[$key]->detail_jwb = DB::table('jwb_claim')
                            ->select('*')
                            ->where('jwb_claim.no_retur', $request->no_retur)
                            ->where('jwb_claim.no_klaim', $value->no_klaim)
                            ->where('jwb_claim.kd_part', $value->kd_part)
                            ->where('jwb_claim.CompanyId', $request->companyid)
                            ->orderBy('no_jwb', 'asc')
                            ->get();
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
            // $rules = [];
            // $messages = [];

            // // ! ------------------------------------
            // // ! Jika menambahkan validasi
            // if($request->no_retur == $request->user_id){
            //     if(!empty($request->pc) && $request->pc == 1){
            //         $rules += ['kd_cabang' => 'required'];
            //         $messages += ['kd_cabang.required' => 'Kode Cabang Kososng'];
            //     } else {
            //         $rules += ['kd_dealer' => 'required'];
            //         $messages += ['kd_dealer.required' => 'kode Dealer Kososng'];
            //     }

            //     if(!empty($request->kd_part)){
            //         $rules += [
            //             'kd_part' => 'required',
            //             'qty_retur' => 'required|numeric|min:1',
            //             'sts_stock' => 'required',
            //             'sts_klaim' => 'required',
            //             'sts_min' => 'required',
            //         ];
            //         $messages += [
            //             'kd_part.required' => 'Part Number Kososng',
            //             'qty_retur.required' => 'QTY Claim Kososng',
            //             'qty_retur.min' => 'QTY Pada Claim Minimal 1',
            //             'sts_stock.required' => 'Status Stock Kososng',
            //             'sts_klaim.required' => 'Status Retur Kososng',
            //             'sts_min.required' => 'Status Min Kososng',
            //         ];
            //     }
            // }

            // // ! megecek validasi dan menampilkan pesan error
            // // ! ------------------------------------
            // $validate = Validator::make($request->all(), $rules,$messages);
            // if ($validate->fails()) {
            //     return Response::responseWarning($validate->errors()->first());
            // }

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
                        ->where('retur_dtltmp.Kd_Key', $request->user_id)
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
                        'status'    => true,
                        'data'      => ''
                    ];
                }

                // ! ======================================================
                // ! Simpan Data
                // ! ======================================================
                $a = DB::table('retur')
                ->lock('with (nolock)')
                ->select(DB::raw("isnull(max(substring(no_retur, 1, charindex('/', no_retur) - 1)), 0) as number"))
                ->whereYear('tglretur', date('Y'))
                ->where('CompanyId', $request->companyid)
                ->first();

                $no_retur = ($a->number + 1)."/C/A/".date('Y');

                $data_header_tamp = DB::table('returtmp')
                ->lock('with (nolock)')
                ->select('*')
                ->where('returtmp.Kd_Key', $request->user_id)
                ->where('returtmp.no_retur', $request->user_id)
                ->where('returtmp.CompanyId', $request->companyid)
                ->first();

                
                //! simpan pada tabel retur
                DB::table('retur')
                ->insert([
                    'no_retur'          => $no_retur,
                    'tglretur'          => $data_header_tamp->tglretur,
                    'Kd_supp'           => $data_header_tamp->kd_supp,
                    'sts_jurnal'        => ($data_header_tamp->sts_jurnal??0),
                    'total'             => $data_header_tamp->total,
                    'CompanyId'         => $data_header_tamp->CompanyId,
                    'usertime'          => (string)(date('Y-m-d H:i:s').'='.$request->user_id)
                ]);

                $data_detail_tamp = DB::table('retur_dtltmp')
                ->lock('with (nolock)')
                ->select(
                    DB::raw("'".$no_retur."' as no_retur")
                    , 'no_klaim', 'kd_dealer', 'no_faktur', 'kd_part', 'kd_lokasi', 'jmlretur', 'ket', 'diterima', 'no_produksi', 'tgl_pemakaian', 'tgl_claim', 'tgl_jwb', 'qty_jwb', 'ket_jwb', 'no_ps_klaim', 'jml_ganti', 'kd_min', 'qty_min', 'no_memo', 'qty_memo', 'add_proc', 'del_proc', 'CompanyId', 'usertime')
                ->where('retur_dtltmp.Kd_Key', $request->user_id)
                ->where('retur_dtltmp.no_retur', $request->user_id)
                ->where('retur_dtltmp.CompanyId', $request->companyid)
                ->get();

                $data_detail_tamp = json_decode(json_encode($data_detail_tamp), true);

                //! simpan pada tabel retur_dtl
                DB::table('retur_dtl')
                ->insert($data_detail_tamp);

                // ! hapus data tamporeri
                DB::table('returtmp')->where('kd_key', $request->user_id)->where('companyid', $request->companyid)->delete();
                DB::table('retur_dtltmp')->where('kd_key', $request->user_id)->where('companyid', $request->companyid)->delete();

                return (object)[
                    'status'    => true,
                    'data'      => $no_retur
                ];
            });

            // ! jika true succes jika false terdapat validasi yang gagal
            if($simpan->status == true){
                return Response::responseSuccess('success', $simpan->data);
            } else if ($simpan->status == false){
                return Response::responseWarning($simpan->message, $simpan->data);
            }
            
        }catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function destroy(Request $request)
    {
        try {
            $rules = ['no_retur' => 'required'];
            $messages = ['no_retur.required' => 'No Retur Tidak Boleh Kososng'];

            // ! ------------------------------------
            // ! megecek validasi dan menampilkan pesan error
            // ! ------------------------------------
            $validate = Validator::make($request->all(), $rules,$messages);
            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            if(!empty($request->kd_part) && !empty($request->no_klaim) && $request->no_retur == $request->user_id){
                DB::transaction(function () use ($request) {
                    DB::table('retur_dtltmp')
                    ->where('retur_dtltmp.Kd_Key', $request->user_id)
                    ->where('retur_dtltmp.no_retur', $request->user_id)
                    ->where('retur_dtltmp.CompanyId', $request->companyid)
                    ->where('retur_dtltmp.no_klaim', $request->no_klaim)
                    ->where('kd_part', $request->kd_part)
                    ->delete();

                    DB::table('rtoko_dtl')
                    ->where('rtoko_dtl.no_retur', $request->no_klaim)
                    ->where('rtoko_dtl.kd_part', $request->kd_part)
                    ->where('rtoko_dtl.CompanyId', $request->companyid)
                    ->update([
                        'status' => 0
                    ]);

                    // ! update total sesuai dengan data detail yang ada 
                    $b = DB::table(function ($query) use ($request) {
                        $query
                        ->select('retur_dtltmp.Kd_Key', 'retur_dtltmp.no_retur', DB::raw('isnull(sum(retur_dtltmp.jmlretur), 0) as total'))
                        ->from('retur_dtltmp')
                        ->where('retur_dtltmp.Kd_Key', $request->user_id)
                        ->where('retur_dtltmp.no_retur', $request->user_id)
                        ->where('retur_dtltmp.CompanyId', $request->companyid)
                        ->groupBy('retur_dtltmp.Kd_Key', 'retur_dtltmp.no_retur');
                    },'b')
                    ->first();

                    DB::table('returtmp')
                    ->where('kd_key', $request->user_id)
                    ->where('no_retur', $request->user_id)
                    ->where('companyid', $request->companyid)
                    ->update([
                        'total'             => $b->total??0,
                    ]);
                });

                return response::responseSuccess('success', '');
            } else if ($request->no_retur != $request->user_id){
                DB::transaction(
                    function () use ($request) {
                        DB::table('retur')
                            ->where('no_retur', $request->no_retur)
                            ->delete();

                        DB::table('rtoko_dtl')
                        ->JoinSub(function($query) use ($request){
                            $query->select('*')
                            ->from('retur_dtl')
                            ->where('retur_dtl.no_retur', $request->no_retur);
                        }, 'retur_dtl', function($join){
                            $join->on('rtoko_dtl.no_retur', '=', 'retur_dtl.no_klaim')
                            ->on('rtoko_dtl.kd_part', '=', 'retur_dtl.kd_part');
                        })
                        ->update([
                            'rtoko_dtl.status' => 0
                        ]);

                        DB::table('retur_dtl')
                            ->where('no_retur', $request->no_retur)
                            ->delete();

                        DB::table('jwb_claim')
                            ->where('no_retur', $request->no_retur)
                            ->delete();

                        
                    }
                );

                return response::responseSuccess('success', '');
            }
        }catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        };
    }
}
