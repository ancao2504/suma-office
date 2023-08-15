<?php

namespace App\Http\Controllers\Api\Backend\Online\Shopee;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use App\Helpers\Api\UpdateToken;
use App\Helpers\Api\ServiceShopee;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class ApiPemindahanController extends Controller
{
    public function daftarPemindahan(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'start_date'    => 'required',
                'end_date'      => 'required',
                'companyid'     => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Isi tanggal awal dan tanggal akhir terlebih dahulu");
            }

            $sql = DB::table('pdh')->lock('with (nolock)')
                    ->selectRaw("isnull(pdh.no_dokumen, '') as nomor_dokumen,
                                isnull(pdh.tanggal, '') as tanggal,
                                isnull(pdh.kd_lokasi1, '') as lokasi_awal,
                                isnull(pdh.kd_lokasi2, '') as lokasi_tujuan,
                                isnull(pdh.keterangan, '') as keterangan,
                                isnull(pdh.sts_ctk, 0) as status_cetak,
                                isnull(iif(isnull(pdh.tgl_sj, '')='', 0, 1), 0) as status_sj,
                                IIF(
                                    (
                                    isnull(iif(isnull(pdh.tgl_in, '')='', 0, 1), 0) = 0 AND
                                    isnull(iif(isnull(pdh.tgl_sj, '')='', 0, 1), 0) = 0 AND
                                    ISNULL(pdh.sts_ctk, 0) = 0 AND
                                    ISNULL(pdh.sts_in, 0) = 0
                                ), 0, 1) as validasi,
                                isnull(pdh.sts_mp, 0) as status_marketplace,
                                isnull(pdh.usertime, '') as usertime")
                    ->where('pdh.companyid', trim($request->get('companyid')))
                    ->whereBetween('pdh.tanggal', [ $request->get('start_date'), $request->get('end_date') ])
                    ->where(function($query) {
                        $query->where('pdh.kd_lokasi1', config('constants.shopee.kode_lokasi'))
                        ->orWhere('pdh.kd_lokasi2', config('constants.shopee.kode_lokasi'));
                    });

            if(!empty($request->get('nomor_dokumen')) && trim($request->get('nomor_dokumen')) != '') {
                $sql = $sql->where('pdh.no_dokumen', 'like','%'.trim($request->get('nomor_dokumen')).'%');
            }
            $sql = $sql->orderBy('pdh.no_dokumen', 'desc');

            $result = $sql->paginate((empty($request->get('per_page'))) ? 10 : $request->get('per_page'));
            return Response::responseSuccess('success', $result);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function detailPemindahan(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_dokumen'     => 'required|string',
                'companyid'         => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Nomor dokumen tidak ditemukan, Mohon kembali dan ulangi kembali");
            }

            $sql = "select	isnull(pdh.no_dokumen, '') as nomor_dokumen,
                            pdh.tanggal,
                            pdh.usertime,
                            pdh.keterangan,
                            isnull(pdh.kd_lokasi1, '') as kode_lokasi_awal,
                            isnull(pdh.kd_lokasi2, '') as kode_lokasi_tujuan,
                            isnull(pdh_dtl.kd_part, '') as part_number,
                            isnull(part.ket, '') as nama_part,
                            isnull(pdh_dtl.pindah, 0) as pindah,
                            isnull(
                                iif(pdh.kd_lokasi1='".config('constants.shopee.kode_lokasi')."' and pdh_dtl.sts_mp_awal=1, 1,
                                iif(pdh.kd_lokasi2='".config('constants.shopee.kode_lokasi')."' and pdh_dtl.sts_mp_tujuan=1, 1, 0)
                            ), 0) as status_mp_detail,
                            isnull(part.shopee_id, '') as product_id,
                            isnull(stlokasi.jumlah, 0) - isnull(stlokasi.in_transit, 0) as stock_suma,
                            isnull(pdh.sts_ctk, 0) as status_cetak,
                            isnull(iif(isnull(pdh.tgl_sj, '')='', 0, 1), 0) as status_sj,
                            IIF(
                                (
                                isnull(iif(isnull(pdh.tgl_in, '')='', 0, 1), 0) = 0 AND
                                isnull(iif(isnull(pdh.tgl_sj, '')='', 0, 1), 0) = 0 AND
                                ISNULL(pdh.sts_ctk, 0) = 0 AND
                                ISNULL(pdh.sts_in, 0) = 0
                            ), 0, 1) as status_validasi,
                            isnull(pdh.sts_mp, 0) as status_mp_header,
                            isnull(pdh.sts_in, 0) as status_in
                    from
                    (
                        select	*
                        from	pdh with (nolock)
                        where	pdh.no_dokumen=? and
                                pdh.companyid=? and
                                (pdh.kd_lokasi1 = '".config('constants.shopee.kode_lokasi')."' or pdh.kd_lokasi2 = '".config('constants.shopee.kode_lokasi')."')
                    )	pdh
                            left join pdh_dtl with (nolock) on pdh.no_dokumen=pdh_dtl.no_dokumen and
                                    pdh.companyid=pdh_dtl.companyid
                            left join part with (nolock) on pdh_dtl.kd_part=part.kd_part and
                                    pdh.companyid=part.companyid
                            left join stlokasi with (nolock) on pdh_dtl.kd_part=stlokasi.kd_part and
                                    pdh.companyid=stlokasi.companyid and stlokasi.kd_lokasi='".config('constants.shopee.kode_lokasi')."'
                    where	isnull(pdh_dtl.pindah, 0) > 0
                    order by pdh_dtl.kd_part asc";
            $sql = DB::select($sql, [ $request->get('nomor_dokumen'), $request->get('companyid')]);

            if(empty($sql)) {
                return Response::responseWarning("Nomor dokumen tidak ditemukan, Mohon kembali dan ulangi kembali");
            }

            // memisahkan detail dariheader pada $sql
            $data_detail = [];
            foreach($sql as $key => $value) {
                $data_detail[] = (object)[
                    'part_number'       => rtrim($value->part_number),
                    'nama_part'         => rtrim($value->nama_part),
                    'pindah'            => $value->pindah,
                    'status_mp'         => [
                        'detail'     => $value->status_mp_detail,
                        'keterangan' => ($value->status_mp_detail == 1)? 'DATA SUDAH PERNAH DI UPDATE KE MARKETPLACE' : (($value->status_validasi == 1)? 'DATA BELUM DI VALIDASI' : '-')
                    ],
                    'product_id'        => $value->product_id,
                    'stock_suma'        => $value->stock_suma,
                    'stock_update'      => null,
                    'marketplace'       => (object)[
                        'product_id'    => null,
                        'sku'           => null,
                        'stock'         => null,
                        'status'        => null,
                    ]
                ];
            }

            // membuat format response
            $data_header = (object)[
                'nomor_dokumen'     => rtrim($sql[0]->nomor_dokumen),
                'tanggal'           =>$sql[0]->tanggal,
                'status'            => (object)[
                    'cetak'         => $sql[0]->status_cetak,
                    'sj'            => $sql[0]->status_sj,
                    'validasi'      => $sql[0]->status_validasi,
                    'mp_header'     => $sql[0]->status_mp_header,
                    'in'            => $sql[0]->status_in,
                ],
                'lokasi_awal'       => (object)[
                    'kode_lokasi'   => rtrim($sql[0]->kode_lokasi_awal),
                    'nama_lokasi'   => '',
                    'alamat'        => (object)[
                        'alamat1'   => '',
                        'alamat2'   => ''
                    ],
                    'kota'          => ''
                ],
                'lokasi_tujuan'     => (object)[
                    'kode_lokasi'   => rtrim($sql[0]->kode_lokasi_tujuan),
                    'nama_lokasi'   => '',
                    'alamat'        => (object)[
                        'alamat1'   => '',
                        'alamat2'   => ''
                    ],
                    'kota'          => ''
                ],
                'keterangan'       => $sql[0]->keterangan,
                'usertime'          => $sql[0]->usertime,
                'detail'            => $data_detail
            ];

            // select detail lokasi
            $sql_lokasi_detail = "select kd_lokasi,ket as nama_lokasi,alamat1,alamat2,kota from lokasi where kd_lokasi = ? or kd_lokasi = ? and companyid = ?";
            $result = DB::select($sql_lokasi_detail, [$data_header->lokasi_awal->kode_lokasi, $data_header->lokasi_tujuan->kode_lokasi, $request->get('companyid')]);

            // mengisi detail lokasi
            $data_header->lokasi_awal->nama_lokasi = $result[0]->nama_lokasi;
            $data_header->lokasi_awal->alamat->alamat1 = $result[0]->alamat1;
            $data_header->lokasi_awal->alamat->alamat2 = $result[0]->alamat2;
            $data_header->lokasi_awal->kota = $result[0]->kota;
            $data_header->lokasi_tujuan->nama_lokasi = $result[1]->nama_lokasi;
            $data_header->lokasi_tujuan->alamat->alamat1 = $result[1]->alamat1;
            $data_header->lokasi_tujuan->alamat->alamat2 = $result[1]->alamat2;
            $data_header->lokasi_tujuan->kota = $result[1]->kota;

            $authorization = $request->header('Authorization');
            $token = explode(" ", $authorization);
            $auth_token = trim($token[1]);

            $token_shopee = '';

            $sql = DB::table('user_api_office')->lock('with (nolock)')
                    ->selectRaw("isnull(user_api_office.shopee_token, '') as shopee_token,
                                isnull(user_api_office.user_id, '') as user_id")
                    ->where('user_api_office.office_token', $auth_token)
                    ->orderByRaw("isnull(user_api_office.id, 0) desc")
                    ->first();

            if(empty($sql->user_id)) {
                return Response::responseWarning('Token Suma Office tidak ditemukan, lakukan logout kemudian login kembali');
            }

            $token_shopee = $sql->shopee_token;

            // cek koneksi
            $responseTokopedia = ServiceShopee::getShopInfo($token_shopee);
            $statusServer = (empty(json_decode($responseTokopedia)->error)) ? 1 : 0;

            if($statusServer == 0) {
                $authorization = $request->header('Authorization');
                $token = explode(" ", $authorization);
                $auth_token = trim($token[1]);

                $responseUpdateToken =  UpdateToken::shopee($auth_token);

                if($responseUpdateToken->status == 0) {
                    return Response::responseWarning($responseUpdateToken->message);
                }

                $token_shopee = $responseUpdateToken->data->token;
            }

            // ambil data stok pada api shopee
            $product_id_shopee = collect($data_header->detail)->where('product_id', '!=', 0)->pluck('product_id')->implode(',');
            $api_getitem_shopee = json_decode(ServiceShopee::getItem($token_shopee,$product_id_shopee));
            // dd($api_getitem_shopee, $data_header->detail);
            if (empty($api_getitem_shopee->error) || $api_getitem_shopee->error != ''){
                // total update pindah + stok shopee dari api
                foreach ($data_header->detail as $key => $value) {
                    if($value->product_id != '0'){
                        collect($api_getitem_shopee->response->item_list)->where('item_id', $value->product_id)->map(function($item) use ($value, $data_header) {
                            if($data_header->lokasi_tujuan->kode_lokasi == 'OS'){
                                $value->stock_update = ($item->stock_info_v2->seller_stock[0]->stock + (int)$value->pindah);
                            } else {
                                $value->stock_update = ($item->stock_info_v2->seller_stock[0]->stock - (int)$value->pindah);
                            }
                            $value->marketplace->product_id = $item->item_id;
                            $value->marketplace->sku = $item->item_sku;
                            $value->marketplace->stock = $item->stock_info_v2->seller_stock[0]->stock;
                            $value->marketplace->status = $item->item_status;
                        });
                    }else {
                        $value->marketplace->stock = null;
                        $value->stock_update = null;
                    }
                }
            } else {
                foreach ($data_header->detail as $key => $value) {
                        $value->marketplace->stock = null;
                        $value->stock_update = null;
                }
            }
            return Response::responseSuccess('success', $data_header);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function updateStockperDokumen(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_dokumen'     => 'required|string',
                'companyid'     => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Nomor dokumen, kode part tidak ditemukan, Mohon kembali kehalaman sebelumnya !");
            }

            $authorization = $request->header('Authorization');
            $token = explode(" ", $authorization);
            $auth_token = trim($token[1]);

            $token_shopee = '';

            $sql = DB::table('user_api_office')->lock('with (nolock)')
                    ->where('office_token', $auth_token)
                    ->first();

            if(empty($sql->user_id)) {
                return Response::responseWarning('Token Suma Office tidak ditemukan, lakukan logout kemudian login kembali');
            }

            $token_shopee = $sql->shopee_token;

                $sql_header = DB::table('pdh')->lock('with (nolock)')
                        ->selectRaw("isnull(pdh.no_dokumen, '') as nomor_dokumen,
                                    isnull(pdh.tanggal, '') as tanggal,
                                    isnull(pdh.kd_lokasi1, '') as lokasi_awal,
                                    isnull(pdh.kd_lokasi2, '') as lokasi_tujuan,
                                    isnull(pdh.keterangan, '') as keterangan,
                                    isnull(pdh.sts_ctk, 0) as status_cetak,
                                    isnull(pdh.sts_in, 0) as status_in,
                                    isnull(iif(isnull(pdh.tgl_sj, '')='', 0, 1), 0) as status_sj,
                                    isnull(pdh.sts_mp, 0) as status_marketplace,
                                    CASE WHEN isnull(pdh.tgl_in, 0) = 0 THEN 0 ELSE 1 END as validasi
                                    ")
                        ->whereRaw("(pdh.kd_lokasi1='".config('constants.shopee.kode_lokasi')."' or pdh.kd_lokasi2='".config('constants.shopee.kode_lokasi')."')")
                        ->where('pdh.no_dokumen', trim($request->get('nomor_dokumen')))
                        ->where('pdh.companyid', $request->get('companyid'))->first();

                // cek sql_header  apakah ada
                if (empty($sql_header)){
                    return Response::responseWarning("Nomor dokumen tidak ditemukan, Mohon cobalagi !");
                }
                if($sql_header->status_marketplace == 1){
                    return Response::responseWarning("Stok pada Nomor dokumen : ". trim($sql_header->nomor_dokumen) ." sudah di update ke marketplace !");
                }

                $sql_detail = DB::table('pdh_dtl')->lock('with (nolock)')
                    ->selectRaw("isnull(pdh_dtl.kd_part, '') as kode_part,
                                isnull(part.shopee_id, '') as product_id,
                                isnull(part.ket, '') as nama_part,
                                isnull(pdh_dtl.pindah, 0) as jumlah_pindah,
                                isnull(stlokasi.jumlah, 0) - isnull(stlokasi.in_transit, 0) as stock,
                                isnull(pdh_dtl.sts_mp_awal, 0) as status_mp_awal,isnull(pdh_dtl.sts_mp_tujuan, 0) as status_mp_tujuan")
                    ->join('part', function($join) use ($request) {
                        $join->on('part.kd_part', '=', 'pdh_dtl.kd_part')
                            ->where('part.companyid', '=', trim($request->get('companyid')));
                    })
                    ->join('stlokasi', function($join) use ($request) {
                        $join->on('pdh_dtl.kd_part', '=', 'stlokasi.kd_part')
                            ->where('stlokasi.companyid', '=', trim($request->get('companyid')))
                            ->where('stlokasi.kd_lokasi','=',config('constants.shopee.kode_lokasi'));
                    })
                    ->where('pdh_dtl.companyid', trim($request->get('companyid')))
                    ->where('pdh_dtl.no_dokumen', trim($request->get('nomor_dokumen')))
                    ->where('pdh_dtl.pindah', '>' , 0);

                    if ($sql_header->lokasi_awal == config('constants.shopee.kode_lokasi')) {
                        $sql_detail = $sql_detail->where(function($query) {
                            $query->where('pdh_dtl.sts_mp_awal', '<>', 1)
                            ->orWhereNull('pdh_dtl.sts_mp_awal');
                        });
                    } else {
                        $sql_detail = $sql_detail->where(function($query) {
                            $query->where('pdh_dtl.sts_mp_tujuan', '<>', 1)
                            ->orWhereNull('pdh_dtl.sts_mp_tujuan');
                        });
                    }

                    $sql_detail = $sql_detail->orderBy('pdh_dtl.kd_part', 'asc')
                    ->get();

            if(empty($sql_detail) || count($sql_detail) == 0) {
                return Response::responseWarning("Part tidak ditemukan, Kemungkinan semua part pada Dokumen : ". $request->get('nomor_dokumen') ." sudah di update ke marketplace !");
            }

            // cek koneksi
            $responseTokopedia = ServiceShopee::getShopInfo($token_shopee);
            $statusServer = (empty(json_decode($responseTokopedia)->error)) ? 1 : 0;

            if($statusServer == 0) {
                $authorization = $request->header('Authorization');
                $token = explode(" ", $authorization);
                $auth_token = trim($token[1]);

                $responseUpdateToken =  UpdateToken::shopee($auth_token);

                if($responseUpdateToken->status == 0) {
                    return Response::responseWarning($responseUpdateToken->message);
                }

                $token_shopee = $responseUpdateToken->data->token;
            }

            $part_masalah = [];
            $part_ready_update = new \stdClass();
            $part_sukses_update = [];

            foreach ($sql_detail as $key => $value) {
                if(empty($value->product_id)) {
                    $part_masalah[] = [
                        'kode_part' => trim($value->kode_part),
                        'product_id' => '',
                        'keterangan' => 'Produk id tidak ada pada database internal'
                    ];
                } else {
                    $part_ready_update->detail_product[trim($value->product_id)] = [
                        'kode_part' => trim($value->kode_part),
                        'product_id' => trim($value->product_id),
                        'jumlah_pindah' => $value->jumlah_pindah
                    ];
                    $part_ready_update->array_product_id[] = trim($value->product_id);
                }
            }

            if (empty($part_ready_update->array_product_id) || count($part_ready_update->array_product_id) == 0) {
                return Response::responseSuccess('', [ 'nomer_dokumen' => trim(strtoupper($sql_header->nomor_dokumen)), 'error_list' => $part_masalah, 'success_list' => []]);
            }

            // ambil data stok pada api shopee
            $api_getitem_shopee = json_decode(ServiceShopee::getItem($token_shopee,implode(',', $part_ready_update->array_product_id)));
            // if(!empty($api_getitem_shopee->error)){
            //     return Response::responseWarning('Maaf koneksi kepihak shopee terputus, Mohon untuk mencoba beberapa saat lagi.');
            // }

            if(trim($sql_header->lokasi_tujuan) == 'OS'){
                foreach ($api_getitem_shopee->response->item_list as $key => $value) {
                    $api_updateitem_shopee = json_decode(ServiceShopee::updateStockPerPart($token_shopee, $value->item_id,( $value->stock_info_v2->seller_stock[0]->stock + $part_ready_update->detail_product[$value->item_id]['jumlah_pindah'])));
                    if (!empty($api_updateitem_shopee->error)){
                        $part_masalah[] = [
                            'kode_part' => $part_ready_update->detail_product[$value->item_id]['kode_part'],
                            'product_id' => $value->item_id,
                            'keterangan' => $api_updateitem_shopee->message
                        ];
                    } else {
                        $part_sukses_update[] = [
                            'kode_part' => $part_ready_update->detail_product[$value->item_id]['kode_part'],
                            'product_id' => $value->item_id,
                            'keterangan' => 'Berhasil Di update ke shopee menjadi : '.$api_updateitem_shopee->response->success_list[0]->stock
                        ];
                    }
                }
            } else {
                foreach ($api_getitem_shopee->response->item_list as $key => $value) {
                    $api_updateitem_shopee = json_decode(ServiceShopee::updateStockPerPart($token_shopee, $value->item_id,( $value->stock_info_v2->seller_stock[0]->stock - $part_ready_update->detail_product[$value->item_id]['jumlah_pindah'])));
                    if (!empty($api_updateitem_shopee->error)){
                        $part_masalah[] = [
                            'kode_part' => $part_ready_update->detail_product[$value->item_id]['kode_part'],
                            'product_id' => $value->item_id,
                            'keterangan' => $api_updateitem_shopee->message
                        ];
                    } else {
                        $part_sukses_update[] = [
                            'kode_part' => $part_ready_update->detail_product[$value->item_id]['kode_part'],
                            'product_id' => $value->item_id,
                            'keterangan' => 'Berhasil Di update ke shopee menjadi : '.$api_updateitem_shopee->response->success_list[0]->stock
                        ];
                    }
                }
            }

            if(empty($part_sukses_update) || count($part_sukses_update) === 0){
                return Response::responseSuccess('Success', [ 'nomer_dokumen' => trim(strtoupper($sql_header->nomor_dokumen)), 'error_list' => $part_masalah, 'success_list' => []]);
            }

            $list_produkid_sukses_update = collect($part_sukses_update)->pluck('product_id')->implode(',');
            DB::transaction(function () use ($list_produkid_sukses_update, $sql_header, $request) {
                DB::insert('exec SP_PdhLok_UpdateStsMP_ShopeeAll ?,?,?,?', [
                    trim(strtoupper($sql_header->nomor_dokumen)),
                    config('constants.shopee.kode_lokasi'),
                    trim(strtoupper($request->get('companyid'))),
                    $list_produkid_sukses_update
                ]);
            });

            return Response::responseSuccess('Data Berhasil Disimpan', [ 'nomer_dokumen' => trim(strtoupper($sql_header->nomor_dokumen)), 'error_list' => $part_masalah, 'success_list' => $part_sukses_update]);

        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function updateStockperPart(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_dokumen'     => 'required|string',
                'kode_part' => 'required',
                'companyid'     => 'required|string'
            ]);
            if($validate->fails()) {
                return Response::responseWarning("Nomor dokumen, kode part tidak ditemukan, Mohon kembali kehalaman sebelumnya !");
            }

            $authorization = $request->header('Authorization');
            $token = explode(" ", $authorization);
            $auth_token = trim($token[1]);

            $pmo_user = DB::table('user_api_office')->lock('with (nolock)')
                        ->selectRaw("isnull(user_api_office.shopee_token, '') as shopee_token,
                                    isnull(user_api_office.user_id, '') as user_id")
                        ->where('user_api_office.office_token', $auth_token)
                        ->orderByRaw("isnull(user_api_office.id, 0) desc")
                        ->first();
            if(empty($pmo_user->user_id)) {
                return Response::responseWarning('Token Suma Office tidak ditemukan, lakukan logout kemudian login kembali');
            }

            $token_shopee = $pmo_user->shopee_token;

                $data = DB::table('pdh')
                    ->selectRaw("isnull(pdh.no_dokumen, '') as nomor_dokumen,
                                    isnull(pdh_dtl.kd_part, '') as kd_part,
                                    isnull(part.shopee_id, '') as product_id,
                                    isnull(pdh.kd_lokasi1, '') as kode_lokasi_awal,
                                    isnull(pdh.kd_lokasi2, '') as kode_lokasi_tujuan,
                                    isnull(pdh.sts_mp, '') as status_mp_header,
                                    iif(pdh.kd_lokasi1 = '".config('constants.shopee.kode_lokasi')."',isnull(pdh_dtl.sts_mp_awal, 0), isnull(pdh_dtl.sts_mp_tujuan, 0)) as status_mp_detail,
                                    isnull(pdh_dtl.pindah, 0) as jumlah_pindah")
                    ->joinSub(function($query) use ($request) {
                        $query->select("*")
                        ->from('pdh_dtl')
                            ->where('pdh_dtl.companyid',  trim($request->get('companyid')))
                            ->where('pdh_dtl.kd_part', trim($request->get('kode_part')))
                            ->where('pdh_dtl.pindah', '>' , 0);
                    }, 'pdh_dtl', function($join) {
                        $join->on('pdh_dtl.no_dokumen',  'pdh.no_dokumen');
                    })
                    ->joinSub(function($query) use ($request) {
                        $query->select("*")
                            ->from('part')
                            ->where('part.companyid',  trim($request->get('companyid')));
                    }, 'part', function($join) {
                        $join->on('part.kd_part', 'pdh_dtl.kd_part');
                    })
                    ->join('stlokasi', function($join) use ($request) {
                        $join->on('pdh_dtl.kd_part', '=', 'stlokasi.kd_part')
                            ->where('stlokasi.companyid', '=', trim($request->get('companyid')))
                            ->where('stlokasi.kd_lokasi','=','OS');
                    })
                    ->where('pdh.no_dokumen', trim($request->get('nomor_dokumen')))
                    ->whereRaw("(pdh.kd_lokasi1='OS' or pdh.kd_lokasi2='OS')")
                    ->where('pdh.companyid', $request->get('companyid'))->first();

            if(empty($data)) {
                return Response::responseWarning("Data part number tidak terdaftar di nomor dokumen ini");
            }
            if($data->status_mp_header == 1) {
                return Response::responseWarning("Nomor dokumen ini sudah pernah di update ke marketplace");
            }
            if(($data->status_mp_header == 1) || ($data->status_mp_detail == 1)) {
                return Response::responseWarning("part number dengan Nomor dokumen ini sudah pernah di update ke marketplace");
            }
            if(empty($data->product_id)) {
                return Response::responseWarning("Part number : ".trim($request->get('kode_part'))." belum memiliki Produk id pada internal, mohon tambahkan produk id terlebih dahulu!");
            }

            // cek koneksi
            $responseTokopedia = ServiceShopee::getShopInfo($token_shopee);
            $statusServer = (empty(json_decode($responseTokopedia)->error)) ? 1 : 0;

            if($statusServer == 0) {
                $authorization = $request->header('Authorization');
                $token = explode(" ", $authorization);
                $auth_token = trim($token[1]);

                $responseUpdateToken =  UpdateToken::shopee($auth_token);
                if($responseUpdateToken->status == 0) {
                    return Response::responseWarning($responseUpdateToken->message);
                }

                $token_shopee = $responseUpdateToken->data->token;
            }

            // ambil data stok pada api shopee
            $api_getitem_shopee = json_decode(ServiceShopee::getItem($token_shopee,$data->product_id));
            if(!empty($api_getitem_shopee->error)){
                return Response::responseWarning('Maaf koneksi kepihak shopee terputus, Mohon untuk mencoba beberapa saat lagi.');
            }

            if(trim($data->kode_lokasi_tujuan) == 'OS'){
                // update untuk penambahan stok di shopee
                $api_updateitem_shopee = json_decode(ServiceShopee::updateStockPerPart($token_shopee, $data->product_id,( $api_getitem_shopee->response->item_list[0]->stock_info_v2->seller_stock[0]->stock + $data->jumlah_pindah)));
            } else {
                // update untuk pengurangan stok di shopee
                $api_updateitem_shopee = json_decode(ServiceShopee::updateStockPerPart($token_shopee, $data->product_id,( $api_getitem_shopee->response->item_list[0]->stock_info_v2->seller_stock[0]->stock - $data->jumlah_pindah)));
            }
            if (!empty($api_updateitem_shopee->error)){
                return Response::responseWarning('Maaf Update stok ke Shopee gagal, Mohon untuk mencoba beberapa saat lagi.');
            }

            DB::transaction(function () use ($request, $data) {
                DB::insert('exec sp_PdhLok_UpdateStsMP ?,?,?,?', [
                    trim(strtoupper($data->nomor_dokumen)),
                    config('constants.shopee.kode_lokasi'),
                    trim(strtoupper($data->kd_part)),
                    trim(strtoupper($request->get('companyid')))
                ]);
            });

            $data_sucses = [];
            $data_sucses[] = [
                'kode_part'   =>  $data->kd_part,
                'product_id' => $data->product_id,
                'keterangan'=> 'Stok Berhasil di Update Menjadi : '. $api_updateitem_shopee->response->success_list[0]->stock
            ];
            return Response::responseSuccess('Data Berhasil Disimpan', [ 'nomer_dokumen' => trim(strtoupper($data->nomor_dokumen)), 'error_list' => [], 'success_list' => $data_sucses]);

        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function updateStatusPerPartNumber(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_dokumen' => 'required|string',
                'part_number'   => 'required|string',
                'companyid'     => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih nomor dokumen dan part number terlebih dahulu");
            }

            $sql = DB::table('pdh_dtl')->lock('with (nolock)')
                    ->selectRaw("isnull(pdh_dtl.no_dokumen, '') as nomor_dokumen,
                                isnull(pdh_dtl.kd_part, '') as part_number")
                    ->where('pdh_dtl.no_dokumen', $request->get('nomor_dokumen'))
                    ->where('pdh_dtl.kd_part', $request->get('part_number'))
                    ->where('pdh_dtl.companyid', $request->get('companyid'))
                    ->first();

            if(empty($sql->nomor_dokumen)) {
                return Response::responseWarning("Part number yang anda pilih tidak terdaftar di nomor dokumen ini");
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec sp_PdhLok_UpdateStsMP ?,?,?,?', [
                    trim(strtoupper($request->get('nomor_dokumen'))), config('constants.shopee.kode_lokasi'),
                    trim(strtoupper($request->get('part_number'))), trim(strtoupper($request->get('companyid')))
                ]);
            });

            return Response::responseSuccess('Data Part : '.trim(strtoupper($request->get('part_number'))).' status telah diupdate pada internal', null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
