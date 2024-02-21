<?php

namespace app\Http\Controllers\Api\Backend\Retur;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

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


            if(empty($request->no_retur) && in_array('tamp', $request->option)){
                $request->merge(['no_retur' => $request->user_id]);
            }

            $request->merge(['tb' => ['klaim','klaim_dtl']]);
            if(in_array('tamp', $request->option)){
                $request->merge(['tb' => ['klaimTmp','klaim_dtlTmp']]);
            }

            $data = DB::table($request->tb[0] . ' as klaim')
                ->leftJoinSub(function($query) use ($request){
                    $query->select(
                        'kd_dealer',
                        'nm_dealer',
                        'alamat1',
                        'kota',
                        'CompanyId'
                    )
                    ->from('dealer')
                    ->where('CompanyId', $request->companyid);
                }, 'dealer', function($join){
                    $join->on('klaim.kd_dealer', '=', 'dealer.kd_dealer')
                    ->on('klaim.companyid', '=', 'dealer.CompanyId');
                })
                ->leftJoinSub(function($query) use ($request){
                    $query->select('kd_sales', 'nm_sales', 'CompanyId')
                    ->from('salesman')
                    ->where('CompanyId', $request->companyid);
                }, 'salesman', function($join){
                    $join->on('klaim.kd_sales', '=', 'salesman.kd_sales')
                    ->on('klaim.companyid', '=', 'salesman.CompanyId');
                })
                ->leftJoinSub(function($query) use ($request){
                    $query->select('no_retur','no_klaim', 'CompanyId')
                    ->from('rtoko_dtl')
                    ->where('CompanyId', $request->companyid);
                }, 'rtoko', function($join){
                    $join->on('klaim.no_dokumen', '=', 'rtoko.no_klaim')
                    ->on('klaim.companyid', '=', 'rtoko.CompanyId');
                })
                ->lock('with (nolock)')
                ->select(
                    'klaim.no_dokumen',
                    'rtoko.no_retur',
                    'klaim.tgl_dokumen',
                    'klaim.tgl_entry',
                    'klaim.kd_sales',
                    'salesman.nm_sales',
                    'klaim.kd_dealer',
                    'dealer.nm_dealer',
                    'dealer.alamat1',
                    'dealer.kota',
                    'klaim.status_approve',
                    'klaim.status_end','klaim.pc'
                );

            if(!empty($request->no_retur) && !in_array('tamp', $request->option)){
                $data = $data->where(function($query) use ($request){
                    $query->where('klaim.no_dokumen', 'LIKE', '%'.$request->no_retur.'%')
                    ->orWhere('rtoko.no_retur', 'LIKE', '%'.$request->no_retur.'%')
                    ->orWhere('salesman.kd_sales', 'LIKE', '%'.$request->no_retur.'%')
                    ->orWhere('dealer.kd_dealer', 'LIKE', '%'.$request->no_retur.'%');
                });
            } elseif(in_array('tamp', $request->option)) {
                $data = $data->where('klaim.no_dokumen', $request->no_retur);
            }

            if(in_array($request->role_id, ['MD_H3_SM']) && !in_array('tamp', $request->option)){
                $data = $data->where('klaim.Kd_sales', $request->user_id);
            }
            // ! Data klaim pagination
            if(in_array('page', $request->option)){

                $data = $data
                ->groupBy(
                    'klaim.no_dokumen',
                    'rtoko.no_retur',
                    'klaim.tgl_dokumen',
                    'klaim.tgl_entry',
                    'klaim.kd_sales',
                    'salesman.nm_sales',
                    'klaim.kd_dealer',
                    'dealer.nm_dealer',
                    'dealer.alamat1',
                    'dealer.kota',
                    'klaim.status_approve',
                    'klaim.status_end',
                    'klaim.pc',
                    'klaim.usertime'
                )
                ->orderByRaw(
                    'status_approve asc,
                    status_end asc,
                    tgl_dokumen desc,
                    klaim.no_dokumen desc'
                )
                ->paginate($request->per_page);

                // ! 1 data klaim
            } elseif(in_array('first', $request->option)){
                $data = $data->first();

                // ! 1 data klaim dengan detail
            } elseif(in_array('with_detail', $request->option)){
                $data = $data->first();
                if(!empty($data)){
                    $data_detail = DB::table(function ($query) use ($request) {
                        $query->selectRaw('
                        klaim_dtl.qty,
                        klaim_dtl.no_produksi,
                        klaim_dtl.tgl_ganti,
                        klaim_dtl.tgl_klaim,
                        klaim_dtl.tgl_pakai,
                        klaim_dtl.qty_ganti,
                        klaim_dtl.sts_stock,
                        klaim_dtl.sts_klaim,
                        klaim_dtl.sts_min,
                        klaim_dtl.keterangan,
                        klaim_dtl.kd_part,
                        klaim_dtl.no_dokumen,
                        klaim_dtl.no_faktur,
                        klaim_dtl.usertime,
                        klaim_dtl.companyid
                        ')
                        ->from($request->tb[1] . ' as klaim_dtl')
                        ->where('klaim_dtl.companyid', $request->companyid)
                        ->where('klaim_dtl.no_dokumen', $request->no_retur);
                    }, 'klaim_dtl')
                    ->selectRaw(
                        'klaim_dtl.no_dokumen,
                        klaim_dtl.no_faktur,
                        fakt_dtl.jml_jual as limit_jumlah,
                        klaim_dtl.kd_part,
                        part.nm_part,
                        klaim_dtl.qty,
                        isnull(jwb_claim.qty_jwb,0) as qty_jwb,
                        klaim_dtl.no_produksi,
                        klaim_dtl.tgl_ganti,
                        klaim_dtl.tgl_klaim,
                        klaim_dtl.tgl_pakai,
                        klaim_dtl.qty_ganti,
                        klaim_dtl.sts_stock,
                        klaim_dtl.sts_klaim,
                        klaim_dtl.sts_min,
                        klaim_dtl.keterangan,
                        (ISNULL(tbStLokasiRak.Stock,0) - (ISNULL(stlokasi.min,0) + ISNULL(stlokasi.in_transit,0) + ISNULL(part.kanvas,0) + ISNULL(part.in_transit,0))) as stock,
                        klaim_dtl.usertime'
                    )
                    ->leftJoinSub(function ($query) use ($request, $data) {
                        $query->selectRaw('
                                kd_part,
                                sum(qty_jwb) as qty_jwb
                            ')
                            ->from('jwb_claim')
                            ->where('no_klaim', $data->no_retur)
                            ->where('companyid', $request->companyid)
                            ->where('sts_end', 1)
                            ->groupBy(
                                'kd_part'
                            );
                    }, 'jwb_claim', function ($join) {
                        $join->on('jwb_claim.kd_part', 'klaim_dtl.kd_part');
                    })
                    ->leftJoinSub(function($query) use ($request){
                        $query->select(
                            'kd_part',
                            'ket as nm_part',
                            'CompanyId',
                            'part.kanvas',
                            'part.in_transit'
                        )
                        ->from('part')
                        ->where('CompanyId', $request->companyid);
                    }, 'part', function($join){
                        $join->on('klaim_dtl.kd_part', '=', 'part.kd_part')
                        ->on('klaim_dtl.companyid', '=', 'part.CompanyId');
                    })
                    ->join('company', function ($join) {
                        $join->on('part.CompanyId', '=', 'company.CompanyId');
                    })
                    ->leftJoin('fakt_dtl', function ($join) {
                        $join->on('klaim_dtl.no_faktur', '=', 'fakt_dtl.no_faktur')
                            ->on('klaim_dtl.kd_part', '=', 'fakt_dtl.kd_part')
                            ->on('klaim_dtl.companyid', '=', 'fakt_dtl.CompanyId');
                    })
                    ->leftJoinSub(function($query) use ($request){
                        $query->select(
                            'stlokasi.kd_part',
                            'stlokasi.kd_lokasi',
                            'stlokasi.CompanyId',
                            'stlokasi.min',
                            'stlokasi.in_transit'
                        )
                        ->from('stlokasi')
                        ->where('stlokasi.CompanyId', $request->companyid);
                    }, 'stlokasi', function($join){
                        $join->on('part.kd_part', '=', 'stlokasi.kd_part')
                        ->on('company.kd_lokasi', '=', 'stlokasi.kd_lokasi')
                        ->on('part.CompanyId', '=', 'stlokasi.CompanyId');
                    })
                    ->leftJoinSUb(function ($query) use ($request){
                        $query->select(
                            'tbStLokasiRak.Kd_part',
                            'tbStLokasiRak.Kd_Lokasi',
                            'tbStLokasiRak.Kd_Rak',
                            'tbStLokasiRak.CompanyId',
                            'tbStLokasiRak.Stock'
                        )
                        ->from('tbStLokasiRak')
                        ->where('tbStLokasiRak.CompanyId',$request->companyid);
                    }, 'tbStLokasiRak', function($join){
                        $join->on('part.kd_part', '=', 'tbStLokasiRak.Kd_part')
                        ->on('stlokasi.kd_lokasi', '=', 'tbStLokasiRak.Kd_Lokasi')
                        ->on('company.kd_rak', '=', 'tbStLokasiRak.Kd_Rak')
                        ->on('part.CompanyId', '=', 'tbStLokasiRak.CompanyId');
                    })
                    ->orderBy('klaim_dtl.usertime', 'asc')
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

            if($request->tamp == 1){
                if(!empty($request->pc) && $request->pc == 1){
                    $rules += ['kd_cabang' => 'required'];
                    $messages += ['kd_cabang.required' => 'Kode Cabang Kososng'];
                } else {
                    $rules += ['kd_dealer' => 'required'];
                    $messages += ['kd_dealer.required' => 'kode Dealer Kososng'];
                }

                $rules += [
                    'kd_part' => 'required',
                    'qty_retur' => 'required|numeric|min:1',
                    'sts_stock' => 'required',
                    'sts_klaim' => 'required',
                    'sts_min' => 'required'
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

            // ! megecek validasi dan menampilkan pesan error
            // ! ------------------------------------
            $validate = Validator::make($request->all(), $rules,$messages);
            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            $simpan = DB::transaction(function () use ($request) {

                // ! jika no_retur == user_id maka tabel temporeri
                if ($request->no_retur != $request->user_id) {
                    $request->merge(['table' => ['klaim','klaim_dtl']]);
                } else {
                    $request->merge(['table' => ['klaimTmp','klaim_dtlTmp']]);
                }

                // ! ======================================================
                // ! Simpan Data Tamporeri
                if($request->tamp == 1){
                    return $this->storeTmp($request);
                }
                // ! ======================================================
                // ! Simpan Data Master
                return $this->storeMaster($request);
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

    public function storeTmp($request)
    {
        // ! get data detail dari tabel klaim_dtlTmp
        $Detail = DB::table($request->table[0].' as klaim')
        ->Join($request->table[1] . ' as klaim_dtl', function ($join) {
            $join->on('klaim_dtl.no_dokumen', '=', 'klaim.no_dokumen')
                ->on('klaim_dtl.companyid', '=', 'klaim.companyid');
        })
        ->selectRaw('klaim_dtl.*, klaim.kd_dealer')
        ->where('klaim.no_dokumen', $request->no_retur)
        ->where('klaim.companyid', $request->companyid)
        ->get();

        // ! Cek dan memastikan qty part klaim tidak melebihi qty part pada faktur
        $cekJmlQtyPart = DB::table(function ($query) use ($request, $Detail) {
            $query->selectRaw(
                    'sum(qty)' . ' + ' . $request->qty_retur . ' as qty_klaim,
                    no_faktur,
                    kd_part'
                )
                ->from($request->table[1])
                ->where('no_dokumen', $request->no_retur)
                ->where('no_faktur', $request->no_faktur)
                ->where('kd_part', $request->kd_part);

                $dataSama = collect($Detail)
                ->where('no_faktur', $request->no_faktur)
                ->where('kd_part', $request->kd_part)
                ->where('no_produksi', strtoupper($request->no_produksi))
                ->first();

                //! cek pada detail apakah ada data dengan no_dokumen, no_faktur, kd_part, no_produksi yang sama, maka sama dengan inggin update data, maka dikcualikan
                if ($dataSama && $request->no_retur == $dataSama->no_dokumen && $request->no_faktur == $dataSama->no_faktur && $request->no_produksi == $dataSama->no_produksi) {
                    $query = $query->where('no_produksi', '!=', strtoupper($request->no_produksi));
                }

                $query = $query->where('companyid', $request->companyid)
                ->groupBy(
                    'no_faktur',
                    'kd_part'
                );
        }, 'klaim_dtl')
        ->selectRaw(
            'fakt_dtl.no_faktur,
            fakt_dtl.tgl_faktur,
            fakt_dtl.kd_dealer,
            fakt_dtl.kd_part,
            isnull(qty_klaim,0) as qty_klaim,
            jml_jual as qty_faktur'
        )
        ->rightJoinSub(function ($query) use ($request) {
            $query->selectRaw('
                    fakt_dtl.no_faktur,
                    faktur.tgl_faktur,
                    fakt_dtl.kd_part,
                    faktur.kd_dealer,
                    fakt_dtl.jml_jual
                ')
                ->from('fakt_dtl')
                ->join('faktur', function ($join) {
                    $join->on('faktur.no_faktur', '=', 'fakt_dtl.no_faktur')
                        ->on('faktur.companyid', '=', 'fakt_dtl.companyid');
                })
                ->where('fakt_dtl.no_faktur', $request->no_faktur)
                ->where('fakt_dtl.kd_part', $request->kd_part)
                ->where('fakt_dtl.companyid', $request->companyid);
        }, 'fakt_dtl', function ($join) {
            $join->on('fakt_dtl.no_faktur', '=', 'klaim_dtl.no_faktur')
                ->on('fakt_dtl.kd_part', '=', 'klaim_dtl.kd_part');
        })
        ->get();

        if ($cekJmlQtyPart->count() > 0 && $request->pc == 0) {
            $cekJmlQtyPart = $cekJmlQtyPart->first();
            if ($request->tgl_pakai < $cekJmlQtyPart->tgl_faktur) {
                return (object)[
                    'status'    => false,
                    'message'   => 'Tanggal Pakai Harus Lebih Dari Tanggal Faktur',
                    'data'      => ''
                ];
            }
            // ! Jika qty part klaim melebihi qty part pada faktur maka tampilkan pesan Peringatan
            if ($cekJmlQtyPart->qty_klaim > $cekJmlQtyPart->qty_faktur) {
                return (object)[
                    'status'    => false,
                    'message'   => 'Jumlah Retur Melebihi Qty Part pada Faktur',
                    'data'      => ''
                ];
            }

            // ! Jika kode dealer pada faktur tidak sama dengan kode dealer pada klaim maka tampilkan pesan Peringatan
            if (
                // ! cek jika data detail sudah ada maka memastikan memiliki dealer yang sama
                ($Detail->count() > 0 && $cekJmlQtyPart->kd_dealer != $Detail->first()->kd_dealer)
                ||
                // ! cek jika tidak ada detail maka cek dengan request memastikan dealer faktur sama seperti request
                ($Detail->count() == 0 && $request->kd_dealer != $cekJmlQtyPart->kd_dealer)
                ) {
                return (object)[
                    'status'    => false,
                    'message'   => 'Kode Dealer Tidak Sama dengan Kode Dealer pada Faktur, 1 Dokumen Hanya Bisa di Klaim oleh 1 Dealer',
                    'data'      => ''
                ];
            }
        }

        // ! CEK pada Klaim sebelumnya apa sudah ada part dari faktur yang sama
        $cekPartFaktur =
        DB::table(function ($query) use ($request) {
            $query->selectRaw('
                    no_dokumen,
                    no_faktur,
                    kd_part,
                    companyid,
                    sum(qty) as qty
                ')
                ->from('klaim_dtl')
                ->where('no_faktur', $request->no_faktur)
                ->where('kd_part', $request->kd_part)
                ->where('companyid', $request->companyid)
                ->groupBy(
                    'no_dokumen',
                    'no_faktur',
                    'kd_part',
                    'companyid'
                );
        }, 'klaim_dtl')
        ->join('klaim', function ($join) {
            $join->on('klaim.no_dokumen', '=', 'klaim_dtl.no_dokumen')
                ->on('klaim.companyid', '=', 'klaim_dtl.companyid');
        })
        ->leftJoin('rtoko_dtl', function ($join) {
            $join->on('rtoko_dtl.no_klaim', '=', 'klaim.no_dokumen')
                ->on('rtoko_dtl.companyid', '=', 'klaim.companyid');
        });
        if ($request->no_retur != $request->user_id) {
            $cekPartFaktur = $cekPartFaktur->where('klaim_dtl.no_dokumen','!=', $request->no_retur);
        }
        $cekPartFaktur = $cekPartFaktur->select(
            'klaim.no_dokumen',
            'rtoko_dtl.no_retur',
            'klaim_dtl.no_faktur',
            'klaim_dtl.kd_part',
            'klaim_dtl.qty',
            'klaim.status_approve',
            'klaim.status_end'
        )
        ->get();

        if ($cekPartFaktur->count() > 0) {
            // ! Pesan info jika part dari faktur yang sama sudah ada pada klaim sebelumnya
            $info =
            'Part dari No Faktur yang sama Sudah Ada pada Klaim Sebelumnya pada
            <br>No Klaim : <b>' . $cekPartFaktur->first()->no_dokumen .'</b>'.
            ((!empty($cekPartFaktur->first()->no_retur) && ($request->role_id == 'MD_H3_MGMT' || $request->role_id == 'MD_H3_KORSM'))?'
                <br>No Retur : <b>'.$cekPartFaktur->first()->no_retur.'</b>'
            :
                '').
            '<br>'.($cekPartFaktur->first()->status_approve != 1 ? '
                <br>Keterangan :
                    <br> <b>Belum di Approve</b>'
            :
                ($cekPartFaktur->first()->status_end != 1 ?
                    '<br>Keterangan :
                        <br> <b>Proses Klaim Belum Selesai</b>'
                :
                    '<br>Keterangan :
                        <br> <b>Klaim Sudah Selesai</b>'));
        }

        //! CEk apakah no_dokumen sudah ada pada tabel klaimTmp
        if(DB::table($request->table[0])
        ->where('no_dokumen', $request->no_retur)
        ->exists()){
            // ! Update data pada tabel klaimTmp
            DB::table($request->table[0])
            ->where ('no_dokumen', $request->no_retur)
            ->update([
                'tgl_dokumen'       => $request->tgl_retur
            ]);

        } else {
            // ! Insert data pada tabel klaimTmp
            DB::table($request->table[0])
            ->insert([
                'no_dokumen'        => $request->no_retur,
                'companyid'         => $request->companyid,
                'tgl_dokumen'       => $request->tgl_retur,
                'tgl_entry'         => date('Y-m-d'),
                'kd_sales'          => ($request->kd_sales??null),
                'pc'                => ($request->pc??0),
                'kd_dealer'         => (($request->pc==1)?$request->kd_cabang:$request->kd_dealer),
                'status_approve'    => ($request->sts_approve??0),
                'usertime'          => (date('Y-m-d H:i:s').'='.$request->user_id)
            ]);
        }

        // ! CEk apakah sudah ada pada tabel klaim_dtlTmp
        if (collect($Detail)
        ->where('no_faktur', $request->no_faktur)
        ->where('kd_part', $request->kd_part)
        ->where('no_produksi', strtoupper($request->no_produksi))
        ->count() > 0) {

            // ! Update data pada tabel klaim_dtlTmp
            DB:: table($request->table[1])
            ->where('companyid', $request->companyid)
            ->where('no_dokumen', $request->no_retur)
            ->where('no_faktur', $request->no_faktur)
            ->where('kd_part', $request->kd_part)
            ->where('no_produksi', strtoupper($request->no_produksi))
            ->update([
                    'qty'               => $request->qty_retur,
                    'sts_stock'         => ($request->sts_stock??2),
                    'sts_klaim'         => ($request->sts_klaim??1),
                    'sts_min'           => ($request->sts_min??1),
                    'tgl_klaim'         => ($request->tgl_klaim??null),
                    'tgl_pakai'         => ($request->tgl_pakai??null),
                    'keterangan'        => ($request->ket??null)
            ]);
        } else {
            // ! Insert data pada tabel klaim_dtlTmp
            DB::table($request->table[1])
            ->insert([
                'no_dokumen'        => $request->no_retur,
                'no_faktur'         => $request->no_faktur,
                'kd_part'           => $request->kd_part,
                'no_produksi'       => strtoupper($request->no_produksi),
                'qty'               => $request->qty_retur,
                'sts_stock'         => ($request->sts_stock??2),
                'sts_klaim'         => ($request->sts_klaim??1),
                'sts_min'           => ($request->sts_min??1),
                'tgl_klaim'         => ($request->tgl_klaim??null),
                'tgl_pakai'         => ($request->tgl_pakai??null),
                'keterangan'        => ($request->ket??null),
                'companyid'         => $request->companyid,
                'usertime'          => (date('Y-m-d=H:i:s').'='.$request->user_id)
            ]);
        }

        // ! parameter index untuk mengambil data temporeri dan mengambil data detail
        if ($request->no_retur != $request->user_id) {
            $request->merge(['option' => ['with_detail']]);
        } else{
            $request->merge(['option' => ['tamp','with_detail']]);
        }
        return (object)[
            'status'    => true,
            'data'      => (object)[
                // ! Memangil data dari index dan mengambil data detail untuk update pada view
                'detail' => $this->index($request)->original['data']->detail,
                'warning' => $info??null
            ],
        ];
    }

    public function storeMaster($request)
    {
        $data_detail = DB::table($request->table[1])
        ->select('*')
        ->where('no_dokumen', $request->no_retur)
        ->where('companyid', $request->companyid);

        // ! jika tidak ada data pada tabel klaim_dtlTmp maka tampilkan pesan peringatan
        if(!$data_detail->exists()){
            return (object)[
                'status'    => false,
                'message'   => 'Tidak ada produk yang di Klaim',
                'data'      => ''
            ];
        }

        // ! get semua data pada tabel klaim_dtlTmp join part, company, stlokasi, tbStLokasiRak
        $validasi_stock = DB::table(function ($query) use ($request) {
            $query->select(
                    'part.kd_part',
                    'part.ket as nm_part',
                    'part.het',
                    'part.hrg_pokok',
                    'part.kd_sub',
                    'part.CompanyId',
                    'part.kanvas',
                    'part.in_transit',
                    'part.min_gudang',
                    'part.min_htl',
                    'part.del_send'
                )
                ->from('part')
                ->where('part.CompanyId', $request->companyid)
                // ->whereRaw("isnull(part.del_send, 0)=0")
                ->whereRaw("isnull(part.het, 0) > 0");
        }, 'part')
        ->selectRaw('
            part.kd_part,
            part.del_send,
            part.het,
            (ISNULL(tbStLokasiRak.Stock,0) - (ISNULL(stlokasi.min,0) + ISNULL(stlokasi.in_transit,0) + ISNULL(part.kanvas,0) + ISNULL(part.in_transit,0))) as stock,
            company.kd_rak,
            company.kd_lokasi,
            klaim_dtlTmp.sts_stock,
            klaim_dtlTmp.qty,
            klaim_dtlTmp.no_produksi,
            klaim_dtlTmp.sts_klaim,
            klaim_dtlTmp.sts_min,
            klaim_dtlTmp.keterangan,
            klaim_dtlTmp.no_dokumen,
            fakt_dtl.no_faktur,
            fakt_dtl.jml_jual,
            klaim_dtlTmp.tgl_klaim,
            klaim_dtlTmp.tgl_pakai,
            klaim_dtlTmp.companyid
        ')
        ->JoinSub($data_detail, 'klaim_dtlTmp', function ($join) {
            $join->on('part.CompanyId', '=', 'klaim_dtlTmp.companyid')
            ->on('part.kd_part', '=', 'klaim_dtlTmp.kd_part');
        })
        ->joinSub(function ($query) use ($request) {
            $query->select(
                    'no_faktur',
                    'kd_part',
                    'jml_jual'
                )
                ->from('fakt_dtl')
                ->where('fakt_dtl.companyid', $request->companyid);
        }, 'fakt_dtl', function ($join) {
            $join->on('part.kd_part', '=', 'fakt_dtl.kd_part')
                ->on('klaim_dtlTmp.no_faktur', '=', 'fakt_dtl.no_faktur');
        })
        ->join('company', function ($join) {
            $join->on('part.CompanyId', '=', 'company.CompanyId');
        })
        ->leftJoin('stlokasi', function ($join) {
            $join->on('part.kd_part', '=', 'stlokasi.kd_part')
                ->on('company.kd_lokasi', '=', 'stlokasi.kd_lokasi')
                ->on('part.CompanyId', '=', 'stlokasi.CompanyId');
        })
        ->leftJoin('tbStLokasiRak', function ($join) {
            $join->on('part.kd_part', '=', 'tbStLokasiRak.Kd_part')
                ->on('stlokasi.kd_lokasi', '=', 'tbStLokasiRak.Kd_Lokasi')
                ->on('company.kd_rak', '=', 'tbStLokasiRak.Kd_Rak')
                ->on('part.CompanyId', '=', 'tbStLokasiRak.CompanyId');
        })
        ->get();

        // ! get data dari validasi stock diatas di group by kd_part
        $data_requestA = collect($validasi_stock)
        ->groupBy('kd_part')
        ->toArray();

        $data_error = [];
        foreach($data_requestA as $key => $value){
            $a = collect($value);
            $pesan = [];
            foreach($a as $key2 => $item){
                // ! cek apakah part sudah di hapus dari supplier
                if($item->sts_stock != 1 && $item->del_send > 0){
                    array_push($pesan, 'Part Sudah Tidak Di Supplay oleh supplier');
                }

                // ! cek apakah qty klaim melebihi jumlah jual pada faktur
                if($item->qty > $item->jml_jual){
                    array_push($pesan, 'Qty Klaim Melebihi Jumlah Jual Pada Faktur');
                }

                // ! cek apakah jumlah jual pada faktur 0
                if($item->jml_jual == 0){
                    array_push($pesan, 'Jumlah Jual 0 Pada Faktur');
                }

                // ! cek stock
                if($item->sts_stock == 1 && $item->stock < $item->qty){
                    array_push($pesan, 'Stock Tidak Mencukupi');
                }
            }
            // ! jika terdapat pesan error maka masukan ke dalam array $data_error
            if(count($pesan) > 0){
                array_push($data_error, [
                    'no_produksi'   => implode('<br>', $a->pluck('no_produksi')->toArray()),
                    'no_faktur'     => implode('<br>', $a->pluck('no_faktur')->unique()->toArray()),
                    'kd_part'       => $key,
                    'qty'           => $a->sum('qty'),
                    'jml_jual'      => $value[0]->jml_jual,
                    'stock'         => $value[0]->stock,
                    'keterangan'    => $pesan
                ]);
            }
        }

        // ! get data dari validasi stock diatas yang sts_min = 1 dan sts_stock = 1 di group by kd_part
        // ! sts_min = 1 artinya part tersebut akan di minimum
        // ! sts_stock = 1 artinya part Ganti Baranag
        $data_requestB = collect($validasi_stock)
        ->groupBy('kd_part')
        ->where('sts_min', 1)
        ->where('sts_stock', 1)
        ->toArray();

        foreach($data_requestB as $key => $value){
            $b = collect($value);
            $pesanB = [];

            // ! cek apakah stock mencukupi
            if($b->sum('qty') > $value[0]->stock){
                $pesanB[] = ['Stock tidak mencukupi'];
            }

            // ! jika terdapat pesan error maka masukan ke dalam array $data_error
            if(count($pesanB) > 0){
                $data_error[] = [
                    'no_produksi'   => implode('<br>', $b->pluck('no_produksi')->toArray()),
                    'no_faktur'     => implode('<br>', $b->pluck('no_faktur')->unique()->toArray()),
                    'kd_part'       => $key,
                    'qty'           => $b->sum('qty'),
                    'jml_jual'      => $value[0]->jml_jual,
                    'stock'         => $value[0]->stock,
                    'keterangan'    => $pesanB
                ];
            }
        }

        // ! jika $data_error lebih dari 0 maka tampilkan pesan error
        if(count($data_error) > 0){
            return (object)[
                'status'    => false,
                'message'   => 'Peringatan Terdapat Kesalahan',
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

        // ! membuat data yang akan di simpan pada tabel klaim
        $header = DB::table($request->table[0])
        ->selectRaw(
            "'$request->no_retur' as no_dokumen,
            '$request->tgl_retur' as tgl_dokumen,
            tgl_entry,
            '$request->kd_sales' as kd_sales,
            '".($request->pc??0)."' as pc,
            '".(($request->pc==1)?$request->kd_cabang:$request->kd_dealer)."' as kd_dealer,
            case when ('$request->role_id' = 'MD_H3_MGMT' or '$request->role_id' = 'MD_H3_KORSM') then 1 else 0 end as status_approve,
            case when ((".collect($validasi_stock)->where('sts_klaim','1')->count()." = 0 and $request->pc = 0) or ($request->pc = 1)) and ('$request->role_id' = 'MD_H3_MGMT' or '$request->role_id' = 'MD_H3_KORSM') then 1 else 0 end as status_end,
            usertime,
            '$request->companyid' as companyid"
        );

        // ! jika
        if(in_array('klaim', $request->table)){
            $header = $header->where('no_dokumen', $request->no_retur);
        } else {
            $header = $header->where('no_dokumen', $request->user_id);
        }
        $header = $header->where('companyid', $request->companyid)
        ->first();

        if(
            // ! cek apakah tidak ada data pada tabel klaim
            DB::table('klaim')
            ->where('no_dokumen', $request->no_retur)
            ->where('companyid', $request->companyid)
            ->doesntExist()
        ){
            // ! Jika tidak ada data pada tabel klaim maka insert
            DB::table('klaim_dtl')
            ->insert(collect($validasi_stock)
            ->map(function($value) use ($request){
                return [
                    'no_dokumen'    => $request->no_retur,
                    'no_faktur'     => $value->no_faktur,
                    'kd_part'       => $value->kd_part,
                    'CompanyId'     => $request->companyid,
                    'qty'           => $value->qty,
                    'no_produksi'   => $value->no_produksi,
                    'sts_stock'     => ($value->sts_stock??null),
                    'sts_klaim'     => ($value->sts_klaim??null),
                    'sts_min'       => ($value->sts_min??null),
                    'tgl_klaim'     => ($value->tgl_klaim??null),
                    'tgl_pakai'     => ($value->tgl_pakai??null),
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

        if($request->role_id == 'MD_H3_MGMT' || $request->role_id == 'MD_H3_KORSM'){
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
            DB::table('klaimTmp')
            ->where('no_dokumen', $request->user_id)
            ->where('companyid', $request->companyid)
            ->delete();

            DB::table('klaim_dtlTmp')
            ->where('no_dokumen', $request->user_id)
            ->where('companyid', $request->companyid)
            ->delete();
        }

        return (object)[
            'status'    => true,
            'data'      => (object)[
                'no_retur' => $request->no_retur,
                'approve'  => $header->status_approve,
            ]
        ];
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
                $rules += [
                    'kd_part' => 'required',
                    'no_produksi' => 'required',
                ];
                $messages += [
                    'kd_part.required' => 'Part Number Tidak boleh kososng',
                    'no_produksi.required' => 'NO Produksi Tidak boleh kososng'
                ];
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

            // ! hapus detail
            if(!empty($request->kd_part)){
                DB::transaction(function () use ($request) {
                    DB::table($request->tb[1])
                        ->where('no_dokumen', $request->no_retur)
                        ->where('no_faktur', $request->no_faktur)
                        ->where('kd_part', $request->kd_part)
                        ->where('no_produksi', $request->no_produksi)
                        ->where('companyid', $request->companyid)
                        ->delete();
                });

                // ! jika data detail kosong maka hapus data header
                if(
                    DB::table($request->tb[1])
                    ->where('no_dokumen', $request->no_retur)
                    ->where('companyid', $request->companyid)
                    ->doesntExist()
                ) {
                    DB::table($request->tb[0])
                        ->where('no_dokumen', $request->no_retur)
                        ->where('companyid', $request->companyid)
                        ->delete();
                }

                return response::responseSuccess('success', '');
            }

            // ! hapus data dan detail
            DB::transaction(function () use ($request) {
                DB::table($request->tb[0])
                    ->where('no_dokumen', $request->no_retur)
                    ->where('companyid', $request->companyid)
                    ->delete();
                DB::table($request->tb[1])
                    ->where('no_dokumen', $request->no_retur)
                    ->where('companyid', $request->companyid)
                    ->delete();
            });
            return response::responseSuccess('success', '');
        }catch (\Exception $exception) {
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $exception->getMessage(),
                $request->get('companyid')
            );
        };
    }
}
