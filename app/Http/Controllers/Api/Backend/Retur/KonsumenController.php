<?php

namespace app\Http\Controllers\Api\Backend\Retur;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;

class KonsumenController extends Controller
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

            $request->merge(['tb' => ['klaim','klaim_dtl']]);
            if(!empty($request->option[1]) && $request->option[1] == 'tamp'){
                $request->merge(['tb' => ['klaimTmp','klaim_dtlTmp']]);
            }

            $data = DB::table($request->tb[0])
                ->leftJoinSub(function($query) use ($request){
                    $query->select('kd_dealer', 'nm_dealer', 'alamat1', 'kota', 'CompanyId')
                    ->from('dealer')
                    ->where('CompanyId', $request->companyid);
                }, 'dealer', function($join) use ($request){
                    $join->on($request->tb[0].'.kd_dealer', '=', 'dealer.kd_dealer')
                    ->on($request->tb[0].'.companyid', '=', 'dealer.CompanyId');
                })
                ->leftJoinSub(function($query) use ($request){
                    $query->select('kd_sales', 'nm_sales', 'CompanyId')
                    ->from('salesman')
                    ->where('CompanyId', $request->companyid);
                }, 'salesman', function($join) use ($request){
                    $join->on($request->tb[0].'.kd_sales', '=', 'salesman.kd_sales')
                    ->on($request->tb[0].'.companyid', '=', 'salesman.CompanyId');
                })
                ->lock('with (nolock)')
                ->select($request->tb[0].'.no_dokumen', $request->tb[0].'.tgl_dokumen', $request->tb[0].'.tgl_entry', $request->tb[0].'.kd_sales', 'salesman.nm_sales', $request->tb[0].'.kd_dealer', 'dealer.nm_dealer', 'dealer.alamat1', 'dealer.kota',$request->tb[0].'.status_approve',$request->tb[0].'.status_end',$request->tb[0].'.pc')
                ->where($request->tb[0].'.companyid', $request->companyid);

            if(!empty($request->no_retur)){
                $data = $data->where($request->tb[0].'.no_dokumen', 'LIKE', '%'.$request->no_retur.'%');
            }

            if($request->option[0] == 'page'){
                $data = $data
                ->orderBy($request->tb[0].'.status_approve' , 'asc')
                ->orderBy($request->tb[0].'.tgl_dokumen', 'asc')
                ->orderBy($request->tb[0].'.no_dokumen', 'asc')
                ->paginate($request->per_page);

            } else if($request->option[0] == 'first'){
                $data = $data->first();

            } else if($request->option[0] == 'with_detail'){
                $data = $data->first();
                if(!empty($data)){
                    $data_detail = DB::table($request->tb[1])
                    ->lock('with (nolock)')
                    ->select(
                        $request->tb[1].'.no_dokumen',
                        $request->tb[1].'.kd_part',
                        'part.nm_part',
                        $request->tb[1].'.qty',
                        $request->tb[1].'.no_produksi',
                        $request->tb[1].'.tgl_ganti',
                        $request->tb[1].'.qty_ganti',
                        $request->tb[1].'.sts_stock',
                        $request->tb[1].'.sts_klaim',
                        $request->tb[1].'.sts_min',
                        $request->tb[1].'.keterangan',
                    )
                    ->selectRaw('(ISNULL(tbStLokasiRak.Stock,0) - (ISNULL(stlokasi.min,0) + ISNULL(stlokasi.in_transit,0) + ISNULL(part.kanvas,0) + ISNULL(part.in_transit,0))) as stock')
                    ->leftJoinSub(function($query) use ($request){
                        $query->select('kd_part', 'ket as nm_part', 'CompanyId','part.kanvas','part.in_transit')
                        ->from('part')
                        ->where('CompanyId', $request->companyid);
                    }, 'part', function($join) use ($request){
                        $join->on($request->tb[1].'.kd_part', '=', 'part.kd_part')
                        ->on($request->tb[1].'.companyid', '=', 'part.CompanyId');
                    })->JoinSub(function ($query) use ($request) {
                        $query->select('*')
                            ->from('company')
                            ->where('company.CompanyId', $request->companyid);
                    }, 'company', function ($join) {
                        $join->on('part.CompanyId', '=', 'company.CompanyId');
                    })
                    ->leftJoinSub(function($query) use ($request){
                        $query->select('stlokasi.kd_part','stlokasi.kd_lokasi','stlokasi.CompanyId','stlokasi.min','stlokasi.in_transit')
                        ->from('stlokasi')
                        ->where('stlokasi.CompanyId', $request->companyid);
                    }, 'stlokasi', function($join){
                        $join->on('part.kd_part', '=', 'stlokasi.kd_part')
                        ->on('company.kd_lokasi', '=', 'stlokasi.kd_lokasi')
                        ->on('part.CompanyId', '=', 'stlokasi.CompanyId');
                    })
                    ->leftJoinSUb(function ($query) use ($request){
                        $query->select('tbStLokasiRak.Kd_part','tbStLokasiRak.Kd_Lokasi','tbStLokasiRak.Kd_Rak','tbStLokasiRak.CompanyId','tbStLokasiRak.Stock')
                        ->from('tbStLokasiRak')
                        ->where('tbStLokasiRak.CompanyId',$request->companyid);
                    }, 'tbStLokasiRak', function($join){
                        $join->on('part.kd_part', '=', 'tbStLokasiRak.Kd_part')
                        ->on('stlokasi.kd_lokasi', '=', 'tbStLokasiRak.Kd_Lokasi')
                        ->on('company.kd_rak', '=', 'tbStLokasiRak.Kd_Rak')
                        ->on('part.CompanyId', '=', 'tbStLokasiRak.CompanyId');
                    });

                    if(!empty($request->option[1]) && $request->option[1] == 'tamp'){
                        $data_detail = $data_detail->where($request->tb[1].'.kd_key', $request->no_retur);
                    }
                    
                    $data_detail = $data_detail->where($request->tb[1].'.companyid', $request->companyid)
                    ->where($request->tb[1].'.no_dokumen', $request->no_retur)
                    ->get();
                    
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
            $rules = [];
            $messages = [];

            // ! ------------------------------------
            // ! Jika menambahkan validasi
            if($request->no_retur == $request->user_id){
                if(!empty($request->pc) && $request->pc == 1){
                    $rules += ['kd_cabang' => 'required'];
                    $messages += ['kd_cabang.required' => 'Kode Cabang Kososng'];
                } else {
                    $rules += ['kd_dealer' => 'required'];
                    $messages += ['kd_dealer.required' => 'kode Dealer Kososng'];
                }

                if(!empty($request->kd_part)){
                    $rules += [
                        'kd_part' => 'required',
                        'qty_retur' => 'required|numeric|min:1',
                        'sts_stock' => 'required',
                        'sts_klaim' => 'required',
                        'sts_min' => 'required',
                    ];
                    $messages += [
                        'kd_part.required' => 'Part Number Kososng',
                        'qty_retur.required' => 'QTY Claim Kososng',
                        'qty_retur.min' => 'QTY Pada Claim Minimal 1',
                        'sts_stock.required' => 'Status Stock Kososng',
                        'sts_klaim.required' => 'Status Retur Kososng',
                        'sts_min.required' => 'Status Min Kososng',
                    ];
                }
            }

            // ! megecek validasi dan menampilkan pesan error
            // ! ------------------------------------
            $validate = Validator::make($request->all(), $rules,$messages);
            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            $request->merge(['tb' => ['klaim','klaim_dtl']]);
            if($request->no_retur == $request->user_id){
                $request->merge(['tb' => ['klaimTmp','klaim_dtlTmp']]);
            }
            $simpan = DB::transaction(function () use ($request) {
                // ! ====================================
                // ! Part + Stock 
                // * Hanya dijalankan jika sts_stock = 1 (Ganti Barang)
                // ! ====================================

                if($request->no_retur != $request->user_id || ($request->no_retur == $request->user_id && !empty($request->sts_stock) && $request->sts_stock == '1')){
                    $validasi_stock = DB::table(function ($query) use ($request) {
                        $query->select('part.kd_part', 'part.ket as nm_part', 'part.het', 'part.hrg_pokok', 'part.kd_sub','part.CompanyId','part.kanvas','part.in_transit','part.min_gudang','part.min_htl')
                            ->from('part')
                            ->where('part.CompanyId', $request->companyid)
                            ->whereRaw("isnull(part.del_send, 0)=0")
                            ->whereRaw("isnull(part.het, 0) > 0");
                    }, 'part')
                    ->select('part.kd_part','klaim_dtlTmp.sts_stock','klaim_dtlTmp.qty')
                    ->selectRaw('(ISNULL(tbStLokasiRak.Stock,0) - (ISNULL(stlokasi.min,0) + ISNULL(stlokasi.in_transit,0) + ISNULL(part.kanvas,0) + ISNULL(part.in_transit,0))) as stock');

                    if($request->no_retur != $request->user_id){
                        $validasi_stock = $validasi_stock
                        ->JoinSub(function ($query) use ($request) {
                            $query->select('*')
                                ->from('klaim_dtlTmp')
                                ->where('klaim_dtlTmp.companyid', $request->companyid)
                                ->where('klaim_dtlTmp.no_dokumen', $request->user_id)
                                ->where('klaim_dtlTmp.kd_key', $request->user_id);
                        }, 'klaim_dtlTmp', function ($join) {
                            $join->on('part.CompanyId', '=', 'klaim_dtlTmp.companyid')
                            ->on('part.kd_part', '=', 'klaim_dtlTmp.kd_part');
                        });
                    }

                    if($request->no_retur == $request->user_id && !empty($request->kd_part)) {
                        $validasi_stock = $validasi_stock
                        ->JoinSub(function ($query) use ($request) {
                            $query->select(
                                DB::raw("'" . $request->kd_part . "' as kd_part"),
                                DB::raw("'" . $request->qty_retur . "' as qty"),
                                DB::raw("'" . $request->sts_stock . "' as sts_stock"),
                                DB::raw("'" . $request->companyid . "' as companyid")
                            );
                        }, 'klaim_dtlTmp', function ($join) {
                            $join->on('part.CompanyId', '=', 'klaim_dtlTmp.companyid')
                            ->on('part.kd_part', '=', 'klaim_dtlTmp.kd_part');
                        });
                    }

                    $validasi_stock = $validasi_stock
                    ->JoinSub(function ($query) use ($request) {
                        $query->select('*')
                            ->from('company')
                            ->where('company.CompanyId', $request->companyid);
                    }, 'company', function ($join) {
                        $join->on('part.CompanyId', '=', 'company.CompanyId');
                    })
                    ->leftJoinSub(function($query) use ($request){
                        $query->select('stlokasi.kd_part','stlokasi.kd_lokasi','stlokasi.CompanyId','stlokasi.min','stlokasi.in_transit')
                        ->from('stlokasi')
                        ->where('stlokasi.CompanyId', $request->companyid);
                    }, 'stlokasi', function($join){
                        $join->on('part.kd_part', '=', 'stlokasi.kd_part')
                        ->on('company.kd_lokasi', '=', 'stlokasi.kd_lokasi')
                        ->on('part.CompanyId', '=', 'stlokasi.CompanyId');
                    })
                    ->leftJoinSUb(function ($query) use ($request){
                        $query->select('tbStLokasiRak.Kd_part','tbStLokasiRak.Kd_Lokasi','tbStLokasiRak.Kd_Rak','tbStLokasiRak.CompanyId','tbStLokasiRak.Stock')
                        ->from('tbStLokasiRak')
                        ->where('tbStLokasiRak.CompanyId',$request->companyid);
                    }, 'tbStLokasiRak', function($join){
                        $join->on('part.kd_part', '=', 'tbStLokasiRak.Kd_part')
                        ->on('stlokasi.kd_lokasi', '=', 'tbStLokasiRak.Kd_Lokasi')
                        ->on('company.kd_rak', '=', 'tbStLokasiRak.Kd_Rak')
                        ->on('part.CompanyId', '=', 'tbStLokasiRak.CompanyId');
                    })->get();
                    


                    // ! ======================================================
                    // ! Validasi Stock
                    // ! ======================================================
                    $data_error = [];
                    collect($validasi_stock)->filter(function($value, $key) use (&$data_error){
                        if($value->sts_stock == 1 && $value->stock < $value->qty){
                            $data_error[] = [
                                    'kd_part'   => $value->kd_part,
                                    'qty'       => $value->qty,
                                    'stock'     => $value->stock,
                                    'keterangan'   => 'Stock tidak mencukupi'
                            ];
                        }
                    });

                    if(count($data_error) > 0){
                        return (object)[
                            'status'    => false,
                            'message'   => 'Validasi Stock Gagal',
                            'data'      => $data_error
                        ];
                    }
                }

                // ! ======================================================
                // ! Simpan Data Tamporeri
                // ! ======================================================
                if($request->no_retur == $request->user_id){
                    //! simpan pada tabel klaimTmp 
                    DB::table($request->tb[0])
                    ->updateOrInsert([
                        'no_dokumen'        => $request->no_retur,
                        'companyid'         => $request->companyid,
                    ],  [
                        'tgl_dokumen'       => $request->tgl_retur,
                        'tgl_entry'         => date('Y-m-d'),
                        'kd_sales'          => ($request->kd_sales??null),
                        'pc'                => ($request->pc??0),
                        'kd_dealer'         => (($request->pc==1)?$request->kd_cabang:$request->kd_dealer),
                        'status_approve'    => ($request->sts_approve??null),
                        'usertime'          => (date('Y-m-d H:i:s').'='.$request->user_id)
                    ]);

                    if(!empty($request->kd_part)){
                        $data_where_detail = [
                            'no_dokumen'    => $request->no_retur,
                            'CompanyId'     => $request->companyid,
                            'kd_key'        => $request->no_retur,
                            'kd_part'       => $request->kd_part,
                        ];
                        //! simpan pada tabel klaimtmp_dtl
                        DB::table($request->tb[1])
                        ->updateOrInsert($data_where_detail, [
                            'qty'           => $request->qty_retur,
                            'no_produksi'   => ($request->no_produksi??null),
                            'sts_stock'     => ($request->sts_stock??null),
                            'sts_klaim'     => ($request->sts_klaim??null),
                            'sts_min'       => ($request->sts_min??null),
                            'keterangan'    => ($request->ket??null),
                            'CompanyId'     => $request->companyid,
                            'usertime'      => (date('Y-m-d H:i:s').'='.$request->user_id)
                        ]);
                    }

                    return (object)[
                        'status'    => true,
                        'data'      => ''
                    ];
                }
                

                // ! ======================================================
                // ! Validasi apakah ada produk yang di retur
                // ! ======================================================
                $data_detail = DB::table('klaim_dtlTmp')
                ->select('no_dokumen','kd_part', 'qty', 'no_produksi', 'sts_stock', 'sts_klaim', 'sts_min', 'keterangan', 'companyid', 'usertime')
                ->where('kd_key', $request->user_id)
                ->where('no_dokumen', $request->user_id)
                ->where('companyid', $request->companyid)
                ->get();

                if(count($data_detail) == 0){
                    return (object)[
                        'status'    => false,
                        'message'   => 'Maaf anda tidak memiliki Produk yang di Retur',
                        'data'      => ''
                    ];
                }


                
                // ! ======================================================
                // ! Simpan Data
                // ! ======================================================
                $romawi = ['0', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
                // ! lihat kode retur
                $cek_jenis = DB::table('setting')->lock('with (nolock)')->select('*')->where('CompanyId', $request->companyid)->first();
                //! lihat kode retur terakhir
                $cek_number_terakhir = (DB::table('number')->lock('with (nolock)')->select('*')->where('CompanyId', $request->companyid)->where('jenis', $cek_jenis->retur)->orderBy('nomor', 'desc')->first()->nomor?? ($cek_jenis->retur.'00000/0/00'));
                // ! membuat number retur
                $number = $cek_jenis->retur . (string)sprintf("%05d", (int)substr($cek_number_terakhir, 2, 5) + 00001) . '/' . (string)$romawi[(int)date('m')] . '/' . substr(date('Y'), 2, 2);
                
                // ! tambah number
                DB::table('number')->insert([
                    'nomor'     => $number,
                    'jenis'     => $cek_jenis->retur,
                    'pakai'     => 1,
                    'CompanyId' => $request->companyid
                ]);
                $request->merge(['no_retur' => $number]);

                //! simpan pada tabel klaimTmp 
                DB::table($request->tb[0])
                ->updateOrInsert([
                    'no_dokumen'        => $request->no_retur,
                    'companyid'         => $request->companyid,
                ], (array)DB::table('klaimTmp')
                ->select('tgl_dokumen', 'tgl_entry', 'kd_sales', 'pc', 'kd_dealer', 'status_approve', 'usertime')
                ->where('no_dokumen', $request->user_id)
                ->where('companyid', $request->companyid)
                ->first());

                foreach($data_detail as $key => $value){
                    //! simpan pada tabel klaimtmp_dtl
                    DB::table($request->tb[1])
                    ->updateOrInsert([
                        'no_dokumen'    => $request->no_retur,
                        'kd_part'       => $value->kd_part,
                        'CompanyId'     => $request->companyid,
                    ], [
                        'qty'           => $value->qty,
                        'no_produksi'   => ($value->no_produksi??null),
                        'sts_stock'     => ($value->sts_stock??null),
                        'sts_klaim'     => ($value->sts_klaim??null),
                        'sts_min'       => ($value->sts_min??null),
                        'keterangan'    => ($value->keterangan??null),
                        'usertime'      => (date('Y-m-d H:i:s').'='.$request->user_id)
                    ]);
                }

                // ! hapus data tamporeri
                DB::table('klaimTmp')->where('no_dokumen', $request->user_id)->where('companyid', $request->companyid)->delete();
                DB::table('klaim_dtlTmp')->where('kd_key', $request->user_id)->where('companyid', $request->companyid)->delete();
                return (object)[
                    'status'    => true,
                    'data'      => ''
                ];
            });

            // ! jika true succes jika false terdapat validasi yang gagal
            if($simpan->status == true){
                return Response::responseSuccess('success', '');
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
            // ! Jika menambahkan validasi
            // ! ------------------------------------
            if(!empty($request->kd_part) && (!empty($request->no_retur) && $request->no_retur == $request->user_id)){
                $rules += ['kd_part' => 'required'];
                $messages += ['kd_part.required' => 'Part Number Tidak boleh kososng'];
            }

            // ! ------------------------------------
            // ! megecek validasi dan menampilkan pesan error
            // ! ------------------------------------
            $validate = Validator::make($request->all(), $rules,$messages);
            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            $request->merge(['tb' => ['klaim','klaim_dtl']]);
            if(!empty($request->no_retur) && $request->no_retur == $request->user_id){
                $request->merge(['tb' => ['klaimTmp','klaim_dtlTmp']]);
                $request->merge(['no_retur' => $request->user_id]);
            }

            if(!empty($request->kd_part) && $request->no_retur == $request->user_id){
                DB::transaction(function () use ($request) {
                    DB::table($request->tb[1])
                        ->where('no_dokumen', $request->no_retur)
                        ->where('kd_part', $request->kd_part)
                        ->delete();
                });

                return response::responseSuccess('success', '');
            } else if ($request->no_retur != $request->user_id){
                DB::transaction(
                    function () use ($request) {
                        if($request->no_retur == $request->user_id){
                            DB::table('number')
                                ->where('nomor', $request->no_retur)
                                ->delete();
                        }
                        DB::table($request->tb[0])
                            ->where('no_dokumen', $request->no_retur)
                            ->delete();
                        DB::table($request->tb[1])
                            ->where('no_dokumen', $request->no_retur)
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
