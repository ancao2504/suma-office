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
            $rules = [];
            $messages = [];
            if(!empty($request->option == 'first')||!empty($request->option == 'get')){
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


            if(!in_array($request->per_page, [10,50,100,500])){
                $request->replace(['per_page' => 10]); 
            }

            $data = DB::table('rtoko')
                ->leftJoinSub(function($query) use ($request){
                    $query->select('kd_dealer', 'nm_dealer', 'alamat1', 'kota', 'CompanyId')
                    ->from('dealer')
                    ->where('CompanyId', $request->companyid);
                }, 'dealer', function($join){
                    $join->on('rtoko.kd_dealer', '=', 'dealer.kd_dealer')
                    ->on('rtoko.CompanyId', '=', 'dealer.CompanyId');
                })
                ->leftJoinSub(function($query) use ($request){
                    $query->select('kd_sales', 'nm_sales', 'CompanyId')
                    ->from('salesman')
                    ->where('CompanyId', $request->companyid);
                }, 'salesk', function($join){
                    $join->on('rtoko.kd_sales', '=', 'salesk.kd_sales')
                    ->on('rtoko.CompanyId', '=', 'salesk.CompanyId');
                })
                ->lock('with (nolock)')
                ->select('rtoko.no_retur', 'rtoko.tanggal', 'rtoko.total', 'rtoko.terbayar', 'rtoko.tgl_terima', 'rtoko.kd_sales', 'salesk.nm_sales', 'rtoko.kd_dealer', 'dealer.nm_dealer', 'dealer.alamat1', 'dealer.kota')
                ->where('rtoko.CompanyId', $request->companyid);

            if(!empty($request->no_retur)){
                $data = $data->where('rtoko.no_retur', 'LIKE', '%'.$request->no_retur.'%');
            }

            if($request->option == 'page'){
                $data = $data->orderByDesc('rtoko.tanggal')
                ->orderByDesc('rtoko.no_retur')->paginate($request->per_page);

            } else if($request->option == 'first'){
                $data = $data->first();

            } else if($request->option == 'with_detail'){
                $data = $data->first();

                $data_detail = DB::table('rtoko_dtl')
                ->lock('with (nolock)')
                ->select(
                    'rtoko_dtl.no_faktur',
                    'rtoko_dtl.tgl_faktur',
                    'rtoko_dtl.kd_part',
                    'rtoko_dtl.kd_lokasi',
                    'rtoko_dtl.qty_faktur',
                    'rtoko_dtl.jumlah',
                    'rtoko_dtl.harga',
                    'rtoko_dtl.nilai',
                    'rtoko_dtl.status',
                    'rtoko_dtl.ket',
                    'rtoko_dtl.disc',
                )
                ->where('rtoko_dtl.CompanyId', $request->companyid)
                ->where('rtoko_dtl.no_retur', $request->no_retur)
                ->get();

                $data->detail = $data_detail;
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

            if(!empty($request->no_retur)){
                $rules += [
                    'kd_dealer' => 'required',
                ];
                $messages += [
                    'kd_dealer.required' => 'kode Dealer Kososng',
                ];
            } else {
                $rules += [
                    'kd_dealer' => 'required',
                    'kd_part' => 'required',
                    'qty_claim' => 'required|numeric|min:1',
                    'sts' => 'required',
                    'tgl_claim' => 'required',
                    'tgl_terima' => 'required',
                ];
                $messages += [
                    'kd_dealer.required' => 'kode Dealer Kososng',
                    'kd_part.required' => 'Part Number Kososng',
                    'qty_claim.required' => 'QTY Claim Kososng',
                    'qty_claim.min' => 'QTY Pada Claim Minimal 1',
                    'tgl_claim.required' => 'Tanggal Claim Kososng',
                    'tgl_terima.required' => 'Tanggal Terima Kososng',
                    'sts.required' => 'Status Kososng',
                ];
            }

            // ! megecek validasi dan menampilkan pesan error
            // ! ------------------------------------
            $validate = Validator::make($request->all(), $rules,$messages);
            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }
            
            if(!empty($request->no_faktur) && !empty($request->no_faktur)){
                // cek no_faktur pada tb faktur where kd_sales dan CopanyId sama dengan inputan
                $cek_no_faktur =
                DB::table('faktur')
                ->lock('with (nolock)')->select('faktur.no_faktur', 'faktur.tgl_faktur', 'faktur.disc2')
                ->where('faktur.no_faktur', $request->no_faktur)
                // ->where('faktur.kd_sales', $request->kd_sales)
                ->where('faktur.CompanyId', $request->companyid)
                ->first();
            
                if (!$cek_no_faktur) {
                    return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'], $request->route()->getActionMethod(), 'Maaf Faktur yang anda masukkan salah !', $request->get('companyid'));
                }
                
                // cek kd_part ada pada faktur_dtl where part.companyid dan faktur_dtl.kd_part inputan
                $cek_kd_part =
                    DB::table('fakt_dtl')
                    ->join('part', 'fakt_dtl.kd_part', 'part.kd_part')
                    ->lock('with (nolock)')->select('fakt_dtl.kd_part', 'part.ket', 'fakt_dtl.jml_jual', 'fakt_dtl.harga', 'fakt_dtl.disc1')
                    ->where('fakt_dtl.no_faktur', $request->no_faktur)
                    ->where('part.CompanyId', $request->companyid)
                    ->where('fakt_dtl.kd_part',  $request->kd_part)
                    ->first();

                if (!$cek_kd_part) {
                    return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'], $request->route()->getActionMethod(), 'Maaf Part Number yang anda masukkan/pilih tidak terdaftar pada Faktur yang anda pilih !', $request->get('companyid'));
                }

                // if($cek_kd_part->jml_jual <= 0){
                //     return response()->json(['message' => 'Maaf Part Number yang dipilih jumlah jual bernilai 0 !'], 404);
                // }
                
                $ttl = $request->qty_claim * (float)str_replace(',', '', $request->harga);
                $disc01 = $ttl * ($cek_kd_part->disc1 / 100);
                $disc02 = ($disc01 == 0) ? $ttl * ((float)$request->disc / 100) : ($ttl - $disc01) * ((float)$request->disc / 100);
                $nilai = $ttl - $disc01 - $disc02;
            }
            // if((int)$request->qty_claim > (int)$cek_kd_part->jml_jual && (int)$request->qty_claim < 0){
            //     return response()->json(['message' => 'Maaf QTY Claim Tidak bisa melebihi QTY Faktur !'], 404);
            // }

            // if(!in_array($request->sts, ['RETUR', 'GANTI BARANG', 'CLAIM ke Supplier'])){
            //     return response()->json(['message' => 'Maaf Status yang anda Pilih tidak ada pada daftar status !'], 404);
            // }

            if (empty($request->no_retur)){
                $cek_jenis = DB::table('setting')->lock('with (nolock)')->select('*')->where('CompanyId', $request->companyid)->first();
                $cek_number_terakhir = DB::table('number')->lock('with (nolock)')->select('*')->where('CompanyId', $request->companyid)->where('jenis', $cek_jenis->retur)->get()->last()?->nomor;
                $cek_number_terakhir = $cek_number_terakhir? $cek_number_terakhir = $cek_number_terakhir : $cek_number_terakhir = 'RD00000/0/00';
                $romawi = ['0', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
                $number = $cek_jenis->retur . (string)sprintf("%05d", (int)substr($cek_number_terakhir, 2, 5) + 00001) . '/' . (string)$romawi[(int)date('m')] . '/' . substr(date('Y'), 2, 2);

                // ! ----------------------------------------
                // ! Cek jika kososng maka variabel null
                // ! ----------------------------------------
                if(empty($cek_no_faktur)){
                    $cek_no_faktur = null;
                }
                if(empty($cek_kd_part)){
                    $cek_kd_part = null;
                }
                if(empty($nilai)){
                    $nilai = null;
                }

                DB::transaction(function () use ($request, $cek_no_faktur, $cek_kd_part, $cek_jenis, $number, $nilai) {
                    $cari_companybycabang = DB::table('company')->lock('with (nolock)')->select('*')->where('CompanyId', $request->companyid)->first();
                    DB::table('number')->insert([
                        'nomor' => $number,
                        'jenis' => $cek_jenis->retur,
                        'pakai' => 1,
                        'CompanyId' => $request->companyid
                    ]);

                    DB::table('rtoko')->insert(
                        [
                            'no_retur' => $number,
                            'tanggal' => date('Y-m-d', strtotime($request->tgl_claim)),
                            'kd_dealer' => $request->kd_dealer,
                            'kd_sales' => $request->kd_sales,
                            'total' => $nilai??null,
                            'sts_jurnal' => 0,
                            'terbayar' => 0,
                            'tgl_terima' => date('Y-m-d', strtotime($request->tgl_terima)),
                            'usertime' => date('Y-m-d=H:i:s') . '=' . trim($request->user_id),
                            'CompanyId' => $request->companyid,
                        ]
                    );

                    DB::table('rtoko_dtl')->insert(
                        [
                            'no_retur' => $number,
                            'kd_part' => $request->kd_part,
                            'kd_lokasi' => $cari_companybycabang->kd_lokasi,
                            'Kd_Rak' =>  $cari_companybycabang->kd_rak,
                            'harga' => (float)str_replace(',', '', $request->harga),
                            'jumlah' => (float)$request->qty_claim,
                            'disc' => (float)$request->disc,
                            'nilai' => $nilai,
                            'usertime' => date('Y-m-d=H:i:s') . '=' . ($request->user_id),
                            'CompanyId' => $request->companyid,
                            'no_faktur' => $request->no_faktur,
                            'tgl_faktur' => $cek_no_faktur->tgl_faktur??null,
                            'qty_faktur' => $cek_kd_part->jml_jual??null,
                            'ket' => $request->ket,
                            'status' => $request->sts,
                        ]
                    );
                });
                
                return Response::responseSuccess('success', $number);
            } else {
                DB::transaction(function () use ($request) {
                    DB::table('rtoko')
                        ->where('no_retur', $request->no_retur)
                        ->update([
                            'kd_sales' => $request->kd_sales??null,
                            'kd_dealer' => $request->kd_dealer??null,
                            'tanggal' => $request->tgl_claim,
                            'tgl_terima' => $request->tgl_terima,
                        ]);
                });
                
                return Response::responseSuccess('success', '');
            }
        }catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function storeDtl(Request $request){
        try {
            $rules = [
                'kd_part' => 'required',
                'qty_claim' => 'required|numeric|min:1',
                'sts' => 'required',
            ];
            $messages = [
                'kd_part.required' => 'Kode Part Kososng',
                'qty_claim.required' => 'QTY Claim Kososng',
                'qty_claim.min' => 'QTY Claim Minimal 1',
                'sts.required' => 'Status Kososng',
            ];

            // ! ------------------------------------
            // ! Jika menambahkan validasi
            // ! ------------------------------------

            if(!empty($request->no_faktur)){
                $rules += [
                    'no_faktur' => 'required|min:5',
                ];
                $messages += [
                    'no_faktur.required' => 'No Faktur Tidak Bisa Kosong',
                    'no_faktur.min' => 'No Faktur minimal 5 karakter',
                ];
            }

            $validate = Validator::make($request->all(), $rules, $messages);

            if ($validate->fails()) {
                
                return Response::responseWarning($validate->errors()->first());
            }
            
            if(!empty($request->no_faktur)){
                $cek_kd_part =
                    DB::table('fakt_dtl')
                    ->join('part', 'fakt_dtl.kd_part', 'part.kd_part')
                    ->lock('with (nolock)')->select('fakt_dtl.kd_part', 'part.ket', 'fakt_dtl.jml_jual', 'fakt_dtl.harga', 'fakt_dtl.disc1')
                    ->where('fakt_dtl.no_faktur', $request->no_faktur)
                    ->where('part.CompanyId', $request->companyid)
                    ->where('fakt_dtl.kd_part',  $request->kd_part)
                    ->first();

                if (!$cek_kd_part) {
                    return Response::responseWarning('Maaf Part yang anda masukkan/pilih tidak terdaftar pada Faktur yang anda pilih !');
                }

                
                $ttl = $request->qty_claim * (float)str_replace(',', '', $request->harga);
                $disc01 = $ttl * ($cek_kd_part->disc1 / 100);
                $disc02 = ($disc01 == 0) ? $ttl * ((float)$request->disc / 100) : ($ttl - $disc01) * ((float)$request->disc / 100);
                $nilai = $ttl - $disc01 - $disc02;
            }

            
            if(empty($request->no_faktur)){
                $cek_kd_part = null;
                $nilai = null;
            }

            // if ($cek_kd_part->jml_jual < 0){
            //     return response()->json(['message' => 'Maaf Part Number yang dipilih jumlah jual bernilai 0 !'], 404);
            // }

            // if ((int)$request->qty_claim > (int)$cek_kd_part->jml_jual  && (int)$request->qty_claim < 0){
            //     return response()->json(['message' => 'Maaf QTY Claim Tidak bisa melebihi QTY Faktur !'], 404);
            // }
            
            // if (!in_array($request->sts, ['RETUR', 'GANTI BARANG', 'CLAIM AHM'])){
            //     return response()->json(['message' => 'Maaf Status yang anda Pilih tidak ada pada daftar status !'], 404);
            // }

            $data_detail = DB::table('rtoko')
            ->join('rtoko_dtl', 'rtoko.no_retur', 'rtoko_dtl.no_retur')
            ->lock('with (nolock)')->select('*')
            ->where('rtoko.no_retur', $request->no_retur)
            ->where('rtoko_dtl.kd_part', $request->kd_part)
            ->where('rtoko.CompanyId', $request->companyid)
            ->first();

            if (!$data_detail) {
                DB::transaction(function () use ($request, $cek_kd_part, $nilai) {
                    $cari_companybycabang = DB::table('company')->lock('with (nolock)')->select('*')->where('CompanyId', $request->companyid)->first();

                    $retur = DB::table('rtoko')
                    ->lock('with (nolock)')->select('*')
                    ->where('CompanyId', $request->companyid)
                    ->where('no_retur', $request->no_retur)
                    ->first();

                    DB::table('rtoko')
                        ->where('no_retur', $request->no_retur)
                        ->update(
                            [
                                'total' => $retur->total + $nilai,
                            ]
                        );

                    DB::table('rtoko_dtl')
                        ->insert(
                            [
                                'no_retur' => $request->no_retur,
                                'kd_part' => $request->kd_part,
                                'kd_lokasi' => $cari_companybycabang->kd_lokasi,
                                'Kd_Rak' =>  $cari_companybycabang->kd_rak,
                                'harga' => (float)str_replace('.', '', $request->harga),
                                'jumlah' => (float)$request->qty_claim,
                                'disc' => (float)$request->disc,
                                'nilai' => $nilai,
                                'usertime' => date('Y-m-d=H:i:s') . '=' . ($request->user_id),
                                // 'add_proc' => null,
                                // 'del_proc' => null,
                                'CompanyId' => $request->companyid,
                                'no_faktur' => $request->no_faktur,
                                'tgl_faktur' => $retur->tgl_faktur??null,
                                'qty_faktur' => $cek_kd_part->jml_jual??null,
                                'ket' => $request->ket,
                                'status' => $request->sts,
                            ]
                        );
                });
            } else if ($data_detail) {
                DB::transaction(function () use ($request, $data_detail, $cek_kd_part, $nilai) {
    
                    DB::table('rtoko')
                        ->where('no_retur', $request->no_retur)
                        ->update(
                            [
                                'total' => $data_detail->total - $data_detail->nilai + $nilai,
                            ]
                        );
                    DB::table('rtoko_dtl')
                        ->where('no_retur', $request->no_retur)
                        ->where('kd_part', $request->kd_part)
                        ->update(
                            [
                                'kd_part' => $request->kd_part,
                                'no_faktur' => $request->no_faktur,
                                'tgl_faktur' => $data_detail->tgl_faktur,
                                'qty_faktur' => $cek_kd_part->jml_jual??null,
                                'jumlah' => (float)$request->qty_claim,
                                'harga' => (float)str_replace(',', '', $request->harga),
                                'disc' => (float)$request->disc,
                                'nilai' => $nilai,
                                'ket' => $request->ket,
                                'status' => $request->sts,
                            ]
                        );
                });
            }

            return response::responseSuccess('success', '');
        }catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function destroy(Request $request)
    {
        $rules = [
            'no_retur' => 'required',
        ];
        $messages = [
            'no_retur.required' => 'No Retur Tidak Boleh Kososng',
        ];

        // ! ------------------------------------
        // ! Jika menambahkan validasi
        // ! ------------------------------------
        if(!empty($request->kd_part)){
            $rules += [
                'kd_part' => 'required|min:5',
            ];
            $messages += [
                'kd_part.required' => 'No Faktur Tidak Bisa Kosong',
                'kd_part.min' => 'No Faktur minimal 5 karakter',
            ];
        }

        // ! ------------------------------------
        // ! megecek validasi dan menampilkan pesan error
        // ! ------------------------------------
        $validate = Validator::make($request->all(), $rules,$messages);
        if ($validate->fails()) {
            return Response::responseWarning($validate->errors()->first());
        }

        try {
            if(!empty($request->kd_part)){
                $data_detail = DB::table('rtoko')
                    ->join('rtoko_dtl', 'rtoko.no_retur', 'rtoko_dtl.no_retur')
                    ->lock('with (nolock)')->select('*')
                    ->where('rtoko.no_retur', $request->no_retur)
                    ->where('rtoko_dtl.kd_part', $request->kd_part)
                    ->where('rtoko.CompanyId', $request->companyid)
                    ->first();

                if (!$data_detail) {
                    return Response::responseWarning('Data Yang Di Hapus Tidak Ditemukan !');
                }

                DB::transaction(function () use ($request, $data_detail) {
                    DB::table('rtoko')
                        ->where('no_retur', $request->no_retur)
                        ->update(
                            [
                                'total' => $data_detail->total - $data_detail->nilai,
                            ]
                        );
                    DB::table('rtoko_dtl')
                        ->where('no_retur', $request->no_retur)
                        ->where('kd_part', $request->kd_part)
                        ->delete();
                });

                return response::responseSuccess('success', '');
            } else if (empty($request->kd_part)){
                DB::transaction(
                    function () use ($request) {
                        DB::table('number')
                            ->where('nomor', $request->no_retur)
                            ->delete();
                        DB::table('rtoko')
                            ->where('no_retur', $request->no_retur)
                            ->delete();
                        DB::table('rtoko_dtl')
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
