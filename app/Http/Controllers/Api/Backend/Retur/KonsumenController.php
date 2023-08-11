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
            $rules = [
                'option' => 'required',
                'companyid' => 'required',
            ];
            $messages = [
                'option.required' => 'Option tidak boleh kosong',
                'companyid.required' => 'Companyid tidak boleh kosong',
            ];

            if(!empty($request->no_retur)){
                $rules += [
                    'no_retur' => 'required|min:5',
                ];
                $messages += [
                    'no_retur.required' => 'Nomor Retur tidak boleh kosong',
                    'no_retur.min' => 'Nomor Retur Minimal 5 Karakter',
                ];
            }

            $validate = Validator::make($request->all(), $rules,$messages);
            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            
            if(empty($request->no_retur) && in_array('tamp', $request->option)){
                $request->merge(['no_retur' => $request->user_id]);
            }

            if(!in_array($request->per_page, [10,50,100,500])){
                $request->replace(['per_page' => 10]); 
            }

            $request->merge(['tb' => ['klaim','klaim_dtl']]);
            if(in_array('tamp', $request->option)){
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
                ->leftJoinSub(function($query) use ($request){
                    $query->select('no_retur','no_klaim', 'CompanyId')
                    ->from('rtoko_dtl')
                    ->where('CompanyId', $request->companyid);
                }, 'rtoko', function($join) use ($request){
                    $join->on($request->tb[0].'.no_dokumen', '=', 'rtoko.no_klaim')
                    ->on($request->tb[0].'.companyid', '=', 'rtoko.CompanyId');
                })
                ->lock('with (nolock)')
                ->select($request->tb[0].'.no_dokumen','rtoko.no_retur', $request->tb[0].'.tgl_dokumen', $request->tb[0].'.tgl_entry', $request->tb[0].'.kd_sales', 'salesman.nm_sales', $request->tb[0].'.kd_dealer', 'dealer.nm_dealer', 'dealer.alamat1', 'dealer.kota',$request->tb[0].'.status_approve',$request->tb[0].'.status_end',$request->tb[0].'.pc')
                ->where($request->tb[0].'.companyid', $request->companyid);

            if(!empty($request->no_retur)){
                $data = $data->where($request->tb[0].'.no_dokumen', 'LIKE', '%'.$request->no_retur.'%');
            }

            if(!in_array($request->role_id, ['MD_H3_MGMT']) && !in_array('tamp', $request->option)){
                $data = $data->where($request->tb[0].'.Kd_sales', $request->user_id);
            }

            if(in_array('page', $request->option)){
                $data = $data
                ->groupBy($request->tb[0].'.no_dokumen','rtoko.no_retur', $request->tb[0].'.tgl_dokumen', $request->tb[0].'.tgl_entry', $request->tb[0].'.kd_sales', 'salesman.nm_sales', $request->tb[0].'.kd_dealer', 'dealer.nm_dealer', 'dealer.alamat1', 'dealer.kota',$request->tb[0].'.status_approve',$request->tb[0].'.status_end',$request->tb[0].'.pc',$request->tb[0].'.usertime')
                ->where($request->tb[0].'.companyid', $request->companyid)
                ->orderBy($request->tb[0].'.status_approve', 'asc')
                ->orderBy($request->tb[0].'.status_end', 'asc')
                ->orderBy($request->tb[0].'.no_dokumen', 'desc')
                ->orderBy($request->tb[0].'.usertime', 'asc')
                ->orderBy($request->tb[0].'.tgl_dokumen', 'desc')
                ->paginate($request->per_page);

            } else if(in_array('first', $request->option)){
                $data = $data->first();

            } else if(in_array('with_detail', $request->option)){
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

            $simpan = DB::transaction(function () use ($request) {

                if ($request->no_retur != $request->user_id) {
                    $request->merge(['table' => ['klaim','klaim_dtl']]);
                } else {
                    $request->merge(['table' => ['klaimTmp','klaim_dtlTmp']]);
                }
                // ! ======================================================
                // ! Simpan Data Tamporeri
                // ! ======================================================
                if($request->tamp == 'true'){

                    //! simpan pada tabel klaimTmp 
                    DB::table($request->table[0])
                    ->updateOrInsert([
                        'no_dokumen'        => $request->no_retur,
                        'companyid'         => $request->companyid,
                    ],  [
                        'tgl_dokumen'       => $request->tgl_retur,
                        'tgl_entry'         => date('Y-m-d'),
                        'kd_sales'          => ($request->kd_sales??null),
                        'pc'                => ($request->pc??0),
                        'kd_dealer'         => (($request->pc==1)?$request->kd_cabang:$request->kd_dealer),
                        'status_approve'    => ($request->sts_approve??0),
                        'usertime'          => (date('Y-m-d H:i:s').'='.$request->user_id)
                    ]);

                    if(!empty($request->kd_part)){
                        //! simpan pada tabel klaimtmp_dtl
                        DB::table($request->table[1])
                        ->updateOrInsert([
                            'no_dokumen'    => $request->no_retur,
                            'companyid'     => $request->companyid,
                            'kd_part'       => $request->kd_part,
                        ], [
                            'qty'           => $request->qty_retur,
                            'no_produksi'   => ($request->no_produksi??null),
                            'sts_stock'     => ($request->sts_stock??0),
                            'sts_klaim'     => ($request->sts_klaim??0),
                            'sts_min'       => ($request->sts_min??0),
                            'keterangan'    => ($request->ket??null),
                            'usertime'      => (date('Y-m-d H:i:s').'='.$request->user_id)
                        ]);
                    }
                    return (object)[
                        'status'    => true,
                        'data'      => ''
                    ];
                }

                // ! ======================================================
                // ! Simpan Data
                // ! ======================================================
                $validasi_stock = DB::table(function ($query) use ($request) {
                    $query->select('part.kd_part', 'part.ket as nm_part', 'part.het', 'part.hrg_pokok', 'part.kd_sub','part.CompanyId','part.kanvas','part.in_transit','part.min_gudang','part.min_htl')
                        ->from('part')
                        ->where('part.CompanyId', $request->companyid)
                        ->whereRaw("isnull(part.del_send, 0)=0")
                        ->whereRaw("isnull(part.het, 0) > 0");
                }, 'part')
                ->select('part.kd_part','part.het')
                ->selectRaw('(ISNULL(tbStLokasiRak.Stock,0) - (ISNULL(stlokasi.min,0) + ISNULL(stlokasi.in_transit,0) + ISNULL(part.kanvas,0) + ISNULL(part.in_transit,0))) as stock')
                ->addSelect('company.kd_rak','company.kd_lokasi')
                ->addSelect('klaim_dtlTmp.sts_stock','klaim_dtlTmp.qty','klaim_dtlTmp.no_produksi','klaim_dtlTmp.sts_klaim', 'klaim_dtlTmp.sts_min', 'klaim_dtlTmp.keterangan')
                ->addSelect('klaim_dtlTmp.no_dokumen', 'klaim_dtlTmp.companyid');

                // ! ======================================================
                // ! Validasi apakah ada produk yang di Klaim
                // ! ======================================================

                $data_detail = DB::table($request->table[1])
                ->select('*')
                ->where('no_dokumen', $request->no_retur)
                ->where('companyid', $request->companyid);

                if(!$data_detail->exists()){
                    return (object)[
                        'status'    => false,
                        'message'   => 'Tidak ada produk yang di Klaim',
                        'data'      => ''
                    ];
                }

                $validasi_stock = $validasi_stock
                ->JoinSub($data_detail, 'klaim_dtlTmp', function ($join) {
                    $join->on('part.CompanyId', '=', 'klaim_dtlTmp.companyid')
                    ->on('part.kd_part', '=', 'klaim_dtlTmp.kd_part');
                })
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
                $data_error = collect($validasi_stock)
                ->where('sts_min',1)
                ->where('sts_stock',1)
                // ->where('stock','<','qty')
                ->filter(function($value, $key){
                    return (float)$value->stock < (float)$value->qty;
                })
                ->map(function($value, $key){
                    return [
                        'kd_part'   => $value->kd_part,
                        'qty'       => $value->qty,
                        'stock'     => $value->stock,
                        'keterangan'   => 'Stock tidak mencukupi'
                    ];
                })->toArray();
                if(count($data_error) > 0){
                    return (object)[
                        'status'    => false,
                        'message'   => 'Validasi Stock Gagal',
                        'data'      => (array)$data_error
                    ];
                }

                if($request->no_retur == $request->user_id){
                    $romawi = ['0', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

                    //! lihat kode retur terakhir
                    $cek_number_terakhir = DB::table('klaim')
                    ->select('no_dokumen')
                    ->where('companyid', $request->companyid)
                    ->whereYear('tgl_dokumen', date('Y'))
                    ->orderBy('usertime', 'desc')
                    ->first()->no_dokumen??'KL00000/I/00';

                    // ! membuat number retur
                    $number = 'KL' . (string)sprintf("%05d", (int)substr($cek_number_terakhir, 2, 5) + 00001) . '/' . (string)$romawi[(int)date('m')] . '/' . substr(date('Y'), 2, 2);

                    $request->merge(['no_retur' => $number]);
                    $request->merge(['hapus_tamp' => true]);
                }
                
                $header = DB::table($request->table[0])
                ->select(
                    DB::raw("'".$request->no_retur."' as no_dokumen"),
                    DB::raw("'".$request->tgl_retur."' as tgl_dokumen"),
                    'tgl_entry', 
                    DB::raw("'".$request->kd_sales."' as kd_sales"),
                    DB::raw("'".($request->pc??0)."' as pc"),
                    DB::raw("'".(($request->pc==1)?$request->kd_cabang:$request->kd_dealer)."' as kd_dealer"),
                    DB::raw("case when '".$request->role_id."' = 'MD_H3_MGMT' then 1 else 0 end as status_approve"),
                    DB::raw("case when ((".collect($validasi_stock)->where('sts_klaim','1')->count()." = 0 and ".$request->pc." = 0) or (".$request->pc." = 1)) and '".$request->role_id."' = 'MD_H3_MGMT' then 1 else 0 end as status_end"),
                    'usertime',
                    DB::raw("'".$request->companyid."' as companyid")
                );
                if(in_array('klaim', $request->table)){
                    $header = $header->where('no_dokumen', $request->no_retur);
                } else {
                    $header = $header->where('no_dokumen', $request->user_id);
                }
                $header =$header->where('companyid', $request->companyid)
                ->first();

                if(
                    DB::table('klaim')
                    ->where('no_dokumen', $request->no_retur)
                    ->where('companyid', $request->companyid)
                    ->doesntExist()
                ){
                    // ! simpan pada tabel klaim_dtl
                    DB::table('klaim_dtl')
                    ->insert(collect($validasi_stock)->map(function($value, $key) use ($request){
                        return [
                            'no_dokumen'    => $request->no_retur,
                            'kd_part'       => $value->kd_part,
                            'CompanyId'     => $request->companyid,
                            'qty'           => $value->qty,
                            'no_produksi'   => ($value->no_produksi??null),
                            'sts_stock'     => ($value->sts_stock??null),
                            'sts_klaim'     => ($value->sts_klaim??null),
                            'sts_min'       => ($value->sts_min??null),
                            'keterangan'    => ($value->keterangan??null),
                            'usertime'      => (date('Y-m-d H:i:s').'='.$request->user_id)
                        ];
                    })->toArray());

                    // ! simpan pada tabel klaim
                    DB::table('klaim')
                    ->insert((array)$header);
                } else {
                    // ! Jika sudah ada data pada tabel klaim maka update dimana jika yang menginput adalah MD_H3_MGMT maka status_approve = 1
                    DB::table('klaim')
                    ->where('no_dokumen', $request->no_retur)
                    ->where('companyid', $request->companyid)
                    ->update((array)$header);
                }
                
                if($request->role_id == 'MD_H3_MGMT'){
                    // ! terdapat minimum, input ke rtoko
                    $simpan = DB::select("
                    SET NOCOUNT ON;
                    exec [SP_RToko_Simpan1] ?, ?, ?, ?, ?, ?, ?, ?", [
                        (string)$request->user_id,
                        (string)$request->no_retur,
                        (string)date('d-m-Y'),
                        (string)$request->kd_sales,
                        (string)$request->kd_dealer,
                        $request->pc,
                        0,
                        (string)$request->companyid
                    ]);

                    if($request->pc == 0){
                        $request->merge(['no_retur' => $simpan[0]->no_retur]);
                    }
                }

                if($request->hapus_tamp??false){
                    // ! hapus data tamporeri
                    DB::table('klaimTmp')->where('no_dokumen', $request->user_id)->where('companyid', $request->companyid)->delete();
                    DB::table('klaim_dtlTmp')->where('no_dokumen', $request->user_id)->where('companyid', $request->companyid)->delete();
                }

                return (object)[
                    'status'    => true,
                    'data'      => (object)[
                        'no_retur' => $request->no_retur,
                        'approve'  => $header->status_approve,
                    ]
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
            }

            DB::transaction(function () use ($request) {
                DB::table($request->tb[0])
                    ->where('no_dokumen', $request->no_retur)
                    ->delete();
                DB::table($request->tb[1])
                    ->where('no_dokumen', $request->no_retur)
                    ->delete();
            });
            return response::responseSuccess('success', '');
        }catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        };
    }
}
