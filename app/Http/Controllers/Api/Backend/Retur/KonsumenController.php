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
                ->lock('with (nolock)')
                ->select($request->tb[0].'.no_dokumen', $request->tb[0].'.tgl_dokumen', $request->tb[0].'.tgl_entry', $request->tb[0].'.kd_sales', 'salesman.nm_sales', $request->tb[0].'.kd_dealer', 'dealer.nm_dealer', 'dealer.alamat1', 'dealer.kota',$request->tb[0].'.status_approve',$request->tb[0].'.status_end',$request->tb[0].'.pc')
                ->where($request->tb[0].'.companyid', $request->companyid);

            if(!empty($request->no_retur)){
                $data = $data->where($request->tb[0].'.no_dokumen', 'LIKE', '%'.$request->no_retur.'%');
            }

            if(!in_array($request->role_id, ['MD_H3_MGMT']) && !in_array('tamp', $request->option)){
                $data = $data->where($request->tb[0].'.Kd_sales', $request->user_id);
            }

            if(in_array('page', $request->option)){
                $data = $data
                ->orderBy($request->tb[0].'.no_dokumen', 'desc')
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
                ->addSelect('klaim_dtlTmp.sts_stock','klaim_dtlTmp.qty','klaim_dtlTmp.no_produksi','klaim_dtlTmp.sts_klaim', 'klaim_dtlTmp.sts_min', 'klaim_dtlTmp.keterangan');

                // ! jika tombol Simpan

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
                $data_error = [];
                collect($validasi_stock)->filter(function($value, $key) use (&$data_error){
                    if($value->sts_min == 1 && $value->stock < $value->qty && $value->sts_stock == 1){
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

                if($request->no_retur == $request->user_id){
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
                    $request->merge(['hapus_tamp' => true]);
                }
                foreach($validasi_stock as $key => $value){
                    
                    // ! ======================================================
                    // ! MINIMUM
                    // ! ======================================================
                    if($value->sts_min == 1 && $request->role_id == 'MD_H3_MGMT'){
                        $ket = 'Klaim ' . trim($request->kd_dealer);
    
                        // ! cek apakah part sudah ada pada min_g
                        $exists = DB::table('min_g')
                        ->select('kd_part')
                        ->where('kd_part', '=', $value->kd_part)
                        ->where('min', '=', $value->qty)
                        ->where('ket', '=', $ket)
                        ->where('usermin', '=', $request->user_id)
                        ->where('tgl', '=', date('Y-m-d'))
                        ->exists();
    
                        if (!$exists) {
                            // ! jika tidak menemukan data yaang sama
                            DB::table('min_g')
                                ->insert([
                                    'kd_part' => $value->kd_part,
                                    'kd_lokasi' => $value->kd_lokasi,
                                    'min' => $value->qty,
                                    'ket' => $ket,
                                    'usermin' => $request->user_id,
                                    'pending' => 0,
                                    'tgl' => date('Y-m-d'),
                                    'CompanyId' => $request->companyid,
                                    'usertime' => ($request->user_id.date('m/d/Y'))
                                ]);
                        } else {
                            //! jika menemukan data yang sama maka ubah
                            DB::table('min_g')
                                ->where('kd_part', '=', $value->kd_part)
                                ->where('min', '=', $value->qty)
                                ->where('ket', '=', $ket)
                                ->where('usermin', '=', $request->user_id)
                                ->where('tgl', '=', date('Y-m-d'))
                                ->update([
                                    'kd_part' => $value->kd_part,
                                    'kd_lokasi' => $value->kd_lokasi,
                                    'min' => $value->qty,
                                    'ket' => $ket,
                                    'usermin' => $request->user_id,
                                    'tgl' => date('Y-m-d'),
                                    'CompanyId' => $request->companyid,
                                    'pending' => $request->Pending, //!
                                    'usertime' => ($request->user_id.date('m/d/Y'))
                                ]);
                        }
    
                        DB::table('stlokasi')
                        ->joinSub(function($query) use ($request,$value){
                            $query->select('CompanyId', 'Kd_Lokasi', 'Kd_part', DB::raw('sum(Min) as min'))
                            ->from('min_g')
                            ->where('CompanyId', $request->companyid)
                            ->where('Kd_part', $value->kd_part)
                            ->whereRaw('isnull(pending, 0) = 0')
                            ->groupBy('CompanyId', 'Kd_Lokasi', 'Kd_part');
                        }, 'a', function($join){
                            $join->on('stlokasi.CompanyId', '=', 'a.CompanyId')
                            ->on('stlokasi.kd_lokasi', '=', 'a.Kd_Lokasi')
                            ->on('stlokasi.kd_part', '=', 'a.Kd_part');
                        })
                        ->update(['stlokasi.min' => DB::raw('isnull(a.min, 0)')]);
                    }

                    //! simpan pada tabel klaim_dtl
                    DB::table('klaim_dtl')
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
                
                // ! ======================================================
                // ! INSERT RTOKO
                // ! ======================================================

                // ! filter cek adakah detail yang sts klaim == 1 atau klim ke supplier
                $data = collect($validasi_stock)->map(function($value, $key) use ($request){
                    if($value->sts_klaim == '1' && $request->role_id == 'MD_H3_MGMT') {
                        return [
                            'no_retur' => $request->no_retur,
                            'kd_part' => $value->kd_part,
                            'kd_lokasi' => $value->kd_lokasi,
                            'Kd_Rak' => $value->kd_rak,
                            'jumlah' => $value->qty,
                            'CompanyId' => $request->companyid,
                            'usertime' => (date('Y-m-d H:i:s').'='.$request->user_id)
                        ];
                    } else {
                        return null;
                    }
                })->filter()->values()->toArray();
                //! jika ada data yang di klaim maka tambahkan rtoko dan rtoko_dtl
                if(count($data) != 0){
                    DB::table('rtoko')->insert([
                        'no_retur' => $request->no_retur,
                        'tanggal' => date('Y-m-d'),
                        'kd_dealer' => (($request->pc==1)?$request->kd_cabang:$request->kd_dealer),
                        'kd_sales' => $request->kd_sales,
                        'sts_jurnal' => '0',
                        'Companyid' => $request->companyid,
                        'usertime' => (date('Y-m-d H:i:s').'='.$request->user_id),
                    ]);
                    DB::table('rtoko_dtl')->insert($data);
                }

                $header = DB::table($request->table[0])
                ->select(
                    DB::raw("'".$request->tgl_retur."' as tgl_dokumen"),
                    'tgl_entry', 
                    DB::raw("'".$request->kd_sales."' as kd_sales"),
                    DB::raw("'".($request->pc??0)."' as pc"),
                    DB::raw("'".(($request->pc==1)?$request->kd_cabang:$request->kd_dealer)."' as kd_dealer"),
                    DB::raw("case when '".$request->role_id."' = 'MD_H3_MGMT' then 1 else 0 end as status_approve"),
                    DB::raw("case when '".count($data)."' = 0 and '".$request->role_id."' = 'MD_H3_MGMT' then 1 else 0 end as status_end"),
                    'usertime'
                );
                if(in_array('klaim', $request->table)){
                    $header = $header->where('no_dokumen', $request->no_retur);
                } else {
                    $header = $header->where('no_dokumen', $request->user_id);
                }
                $header =$header->where('companyid', $request->companyid)
                ->first();

                //! simpan pada tabel klaim
                DB::table('klaim')
                ->updateOrInsert([
                    'no_dokumen'        => $request->no_retur,
                    'companyid'         => $request->companyid,
                ], (array)$header);

                if($request->hapus_tamp??false){
                    // ! hapus data tamporeri
                    DB::table('klaimTmp')->where('no_dokumen', $request->user_id)->where('companyid', $request->companyid)->delete();
                    DB::table('klaim_dtlTmp')->where('no_dokumen', $request->user_id)->where('companyid', $request->companyid)->delete();
                }

                return (object)[
                    'status'    => true,
                    'data'      => (object)[
                        'no_retur' => $request->no_retur,
                        'approve'  => $header->status_approve
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
            } else if ($request->no_retur != $request->user_id){
                DB::transaction(function () use ($request) {
                    DB::table('number')
                        ->where('nomor', $request->no_retur)
                        ->delete();
                    DB::table($request->tb[0])
                        ->where('no_dokumen', $request->no_retur)
                        ->delete();
                    DB::table($request->tb[1])
                        ->where('no_dokumen', $request->no_retur)
                        ->delete();
                });

                return response::responseSuccess('success', '');
            }
        }catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        };
    }
}
