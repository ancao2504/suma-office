<?php

namespace App\Http\Controllers\Api\Backend\Online;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use App\Helpers\Api\UpdateToken;
use App\Helpers\Api\ServiceShopee;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Helpers\Api\ServiceTokopedia;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class ApiPemindahanController extends Controller
{
    public function DaftarPemindahan(Request $request)
    {
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
                                isnull(iif(isnull(pdh.tgl_in, '')='', 0, 1), 0) as validasi,
                                isnull(pdh.sts_mp, 0) as status_marketplace,
                                isnull(pdh.usertime, '') as usertime")
                    ->whereRaw("(pdh.kd_lokasi1='".config('constants.tokopedia.kode_lokasi')."' or pdh.kd_lokasi1='".config('constants.shopee.kode_lokasi')."') and (pdh.kd_lokasi2='".config('constants.tokopedia.kode_lokasi')."' or pdh.kd_lokasi2='".config('constants.shopee.kode_lokasi')."')")
                    ->whereBetween('pdh.tanggal', [ $request->get('start_date'), $request->get('end_date') ])
                    ->where('pdh.companyid', trim($request->get('companyid')));

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

    public function detailPemindahan(Request $request)
    {
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
                            isnull(pdh_dtl.sts_mp_awal, 0) as status_mp_awal,
                            isnull(pdh_dtl.sts_mp_tujuan, 0) as status_mp_tujuan,
                            isnull(part.shopee_id, '') as product_id_shopee,
                            isnull(part.tokopedia_id, '') as product_id_tokopedia,
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
                                (pdh.kd_lokasi1 = '".config('constants.shopee.kode_lokasi')."' or pdh.kd_lokasi1 = '".config('constants.tokopedia.kode_lokasi')."') and (pdh.kd_lokasi2 = '".config('constants.shopee.kode_lokasi')."' or pdh.kd_lokasi2 = '".config('constants.tokopedia.kode_lokasi')."')
                    )	pdh
                            left join pdh_dtl with (nolock) on pdh.no_dokumen=pdh_dtl.no_dokumen and
                                    pdh.companyid=pdh_dtl.companyid
                            left join part with (nolock) on pdh_dtl.kd_part=part.kd_part and
                                    pdh.companyid=part.companyid
                            left join stlokasi with (nolock) on pdh_dtl.kd_part=stlokasi.kd_part and
                                    pdh.companyid=stlokasi.companyid and stlokasi.kd_lokasi='".config('constants.shopee.kode_lokasi')."' and stlokasi.kd_lokasi='".config('constants.tokopedia.kode_lokasi')."'
                    where	isnull(pdh_dtl.pindah, 0) > 0
                    order by pdh_dtl.kd_part asc";
            $sql = DB::select($sql, [ $request->get('nomor_dokumen'), $request->get('companyid')]);

            if(empty($sql)) {
                return Response::responseWarning("Nomor dokumen tidak ditemukan, Mohon kembali dan ulangi kembali");
            }

            // ! memisahkan detail dariheader pada $sql
            $data_detail = [];
            foreach($sql as $key => $value) {
                $data_detail[] = (object)[
                    'part_number'       => rtrim($value->part_number),
                    'nama_part'         => rtrim($value->nama_part),
                    'pindah'            => $value->pindah,
                    'status_mp'         => [
                        'status_mp_awal'    => $value->status_mp_awal,
                        'status_mp_tujuan'  => $value->status_mp_tujuan,
                        'keterangan'        => ($value->status_mp_awal == 1 && $value->status_mp_tujuan == 1)? 'DATA SUDAH PERNAH DI UPDATE KE MARKETPLACE' : (($value->status_validasi == 1)? 'DATA BELUM DI VALIDASI' : '-')
                    ],
                    'stock_suma'        => $value->stock_suma,
                    'stock_update'      => (object)[
                        'shopee'            => null,
                        'tokopedia'         => null
                    ],
                    'marketplace'       => (object)[
                        'shopee'        => (object)[
                            'product_id'    => $value->product_id_shopee,
                            'sku'           => null,
                            'stock'         => null,
                            'status'        => null,
                        ],
                        'tokopedia'     => (object)[
                            'product_id'    => $value->product_id_tokopedia,
                            'sku'           => null,
                            'stock'         => null,
                            'status'        => null,
                        ]
                    ]
                ];
            }

            // ! membuat format response
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

            // ! select detail lokasi
            $sql_lokasi_detail = "select kd_lokasi,ket as nama_lokasi,alamat1,alamat2,kota from lokasi where kd_lokasi = ? or kd_lokasi = ? and companyid = ?";
            $result = DB::select($sql_lokasi_detail, [$data_header->lokasi_awal->kode_lokasi, $data_header->lokasi_tujuan->kode_lokasi, $request->get('companyid')]);

            // ! mengisi detail lokasi
            $data_header->lokasi_awal->nama_lokasi = $result[0]->nama_lokasi;
            $data_header->lokasi_awal->alamat->alamat1 = $result[0]->alamat1;
            $data_header->lokasi_awal->alamat->alamat2 = $result[0]->alamat2;
            $data_header->lokasi_awal->kota = $result[0]->kota;
            $data_header->lokasi_tujuan->nama_lokasi = $result[1]->nama_lokasi;
            $data_header->lokasi_tujuan->alamat->alamat1 = $result[1]->alamat1;
            $data_header->lokasi_tujuan->alamat->alamat2 = $result[1]->alamat2;
            $data_header->lokasi_tujuan->kota = $result[1]->kota;

            $authorization = $request->header('Authorization');
            $auth_token = explode(" ", $authorization);
            $auth_token = trim($auth_token[1]);

            $token = (object)[
                'shopee'    => '',
                'tokopedia' => '',
            ];

            $sql = DB::table('user_api_office')->lock('with (nolock)')
                    ->selectRaw("isnull(user_api_office.shopee_token, '') as shopee_token,
                                isnull(user_api_office.tokopedia_token, '') as tokopedia_token,
                                isnull(user_api_office.user_id, '') as user_id")
                    ->where('user_api_office.office_token', $auth_token)
                    ->orderByRaw("isnull(user_api_office.id, 0) desc")
                    ->first();

            if(empty($sql->user_id)) {
                return Response::responseWarning('Token Suma Office tidak ditemukan, lakukan logout kemudian login kembali');
            }

            $token->shopee = $sql->shopee_token;
            $token->tokopedia = $sql->tokopedia_token;

            // ! cek koneksi
            $responseShopee = ServiceShopee::getShopInfo($token->shopee);
            $statusServerShopee = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

            $responseTokopedia = ServiceTokopedia::GetShopInfo(trim($token->tokopedia));
            $statusServerTokopedia = (empty(json_decode($responseTokopedia)->message)) ? 1 : 0;

            if($statusServerShopee == 0 || $statusServerTokopedia == 0) {
                $responseUpdateToken =  UpdateToken::shopee($auth_token);
                $responseUpdateToken = UpdateToken::tokopedia($auth_token);

                if($responseUpdateToken->status == 0) {
                    return Response::responseWarning($responseUpdateToken->message);
                }
                if($responseUpdateToken->status == 0) {
                    return Response::responseWarning($responseUpdateToken->message);
                }

                $token->tokopedia = $responseUpdateToken->data->token;
                $token->shopee = $responseUpdateToken->data->token;
            }

            // ! ambil data product id shopee dan tokopedia
            foreach ($data_header->detail as $key => $value) {
                if($value->marketplace->shopee->product_id != '0'){
                    $product_id_shopee[] = $value->marketplace->shopee->product_id;
                }
                if($value->marketplace->tokopedia->product_id != '0'){
                    $product_id_tokopedia[] = $value->marketplace->tokopedia->product_id;
                }
            }
            // ! ==========================================================================
            // ! AMBIL DATA SHOPEE DAN TOKOPEDIA
            // ! ==========================================================================
            $api_getitem_shopee = json_decode(ServiceShopee::getItem($token->shopee,trim(implode(',', $product_id_shopee))));
            $responseTokopedia = json_decode(ServiceTokopedia::GetProductInfoByProductId(trim($token->tokopedia), trim(implode(',', $product_id_tokopedia))));

            if (!empty($api_getitem_shopee->error) || !empty($responseTokopedia->header->error_code) || empty($responseTokopedia->data)) {
                return Response::responseWarning('Marketplace tidak terhubung, silahkan cek koneksi internet dan coba lagi');
            }

            if (empty($api_getitem_shopee->error) && empty($responseTokopedia->header->error_code)) {
                foreach ($data_header->detail as $key => $value) {
                    // ! SHOPEE
                    if($value->marketplace->shopee->product_id != '0'){
                        collect($api_getitem_shopee->response->item_list)->where('item_id', $value->marketplace->shopee->product_id)->map(function($item) use ($value, $data_header) {
                            if($data_header->lokasi_tujuan->kode_lokasi == config('constants.shopee.kode_lokasi')){
                                $value->stock_update->shopee = ($item->stock_info_v2->seller_stock[0]->stock + (int)$value->pindah);
                            } else {
                                $value->stock_update->shopee = ($item->stock_info_v2->seller_stock[0]->stock - (int)$value->pindah);
                            }
                            $value->marketplace->shopee->product_id = $item->item_id;
                            $value->marketplace->shopee->sku = $item->item_sku;
                            $value->marketplace->shopee->stock = $item->stock_info_v2->seller_stock[0]->stock;
                            $value->marketplace->shopee->status = $item->item_status;
                        });
                    }else {
                        $value->marketplace->shopee->stock = null;
                        $value->stock_update->shopee = null;
                    }

                    // ! TOKOPEDIA
                    if($value->marketplace->tokopedia->product_id != '0'){
                        collect($responseTokopedia->data)->where('basic.productID', $value->marketplace->tokopedia->product_id)->map(function($item) use ($value, $data_header) {
                            if($data_header->lokasi_tujuan->kode_lokasi == config('constants.tokopedia.kode_lokasi')){
                                $value->stock_update->tokopedia = (((empty($item->stock->value)) ? 0 : $item->stock->value) + (int)$value->pindah);
                            } else {
                                $value->stock_update->tokopedia = (((empty($item->stock->value)) ? 0 : $item->stock->value) - (int)$value->pindah);
                            }

                            $value->marketplace->tokopedia->product_id = (empty($item->basic->productID)) ? '' : strtoupper(trim($item->basic->productID));
                            $value->marketplace->tokopedia->sku = (empty($item->other->sku)) ? '' : strtoupper(trim($item->other->sku));
                            $value->marketplace->tokopedia->stock = (empty($item->stock->value)) ? 0 : $item->stock->value;

                            if($item->basic->status == -2) {
                                $value->marketplace->tokopedia->status = 'Banned';
                            } elseif($item->basic->status == -1) {
                                $value->marketplace->tokopedia->status = 'Pending';
                            } elseif($item->basic->status == 0) {
                                $value->marketplace->tokopedia->status = 'Deleted';
                            } elseif($item->basic->status == 1) {
                                $value->marketplace->tokopedia->status = 'Archive';
                            } elseif($item->basic->status == 2) {
                                $value->marketplace->tokopedia->status = 'Best (Feature Product)';
                            } elseif($item->basic->status == 3) {
                                $value->marketplace->tokopedia->status = 'Inactive (Warehouse)';
                            }
                        });
                    } else {
                        $value->marketplace->tokopedia->stock = null;
                        $value->stock_update->tokopedia = null;
                    }
                }
            } else {
                foreach ($data_header->detail as $key => $value) {
                    $value->marketplace->shopee->stock = null;
                    $value->stock_update->shopee = null;

                    $value->marketplace->tokopedia->stock = null;
                    $value->stock_update->tokopedia = null;
                }
            }

            return Response::responseSuccess('success', $data_header);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function updateStock(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_dokumen'     => 'required|string',
                'companyid'     => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Nomor dokumen, kode part tidak ditemukan, Mohon kembali kehalaman sebelumnya !");
            }

            $authorization = $request->header('Authorization');
            $auth_token = explode(" ", $authorization);
            $auth_token = trim($auth_token[1]);

            $token = (object)[
                'shopee' => '',
                'tokopedia' => ''
            ];

            $sql = DB::table('user_api_office')->lock('with (nolock)')
                    ->where('office_token', $auth_token)
                    ->first();

            if(empty($sql->user_id)) {
                return Response::responseWarning('Token Suma Office tidak ditemukan, lakukan logout kemudian login kembali');
            }

            $token->shopee = $sql->shopee_token;
            $token->tokopedia = $sql->tokopedia_token;
            // TODO: Get Data Header
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
                        ->where('pdh.companyid', $request->get('companyid'))
                        ->whereRaw("(pdh.kd_lokasi1='".config('constants.shopee.kode_lokasi')."' or pdh.kd_lokasi1='".config('constants.tokopedia.kode_lokasi')."')")
                        ->whereRaw("(pdh.kd_lokasi2='".config('constants.shopee.kode_lokasi')."' or pdh.kd_lokasi2='".config('constants.tokopedia.kode_lokasi')."')")
                        ->where('pdh.no_dokumen', trim($request->get('nomor_dokumen')))
                        ->first();
                // ! jika nomor dokumen tidak ditemukan
                if (empty($sql_header)){
                    return Response::responseWarning("Nomor dokumen tidak ditemukan, Mohon cobalagi !");
                }
                // ! jika status sudah di update ke marketplace
                if($sql_header->status_marketplace == 1){
                    return Response::responseWarning("Stok pada Nomor dokumen : ". trim($sql_header->nomor_dokumen) ." sudah di update ke marketplace !");
                }

                // TODO: Get Data Detail
                $sql_detail = DB::table('pdh_dtl')->lock('with (nolock)')
                    ->selectRaw("isnull(pdh_dtl.kd_part, '') as kode_part,
                                isnull(part.shopee_id, '') as product_id_shopee,
                                isnull(part.tokopedia_id, '') as product_id_tokopedia,
                                isnull(part.ket, '') as nama_part,
                                isnull(pdh_dtl.pindah, 0) as jumlah_pindah,
                                isnull(pdh_dtl.sts_mp_awal, 0) as status_mp_awal,isnull(pdh_dtl.sts_mp_tujuan, 0) as status_mp_tujuan")
                    ->join('part', function($join) use ($request) {
                        $join->on('part.kd_part', '=', 'pdh_dtl.kd_part')
                            ->where('part.companyid', '=', trim($request->get('companyid')));
                    })
                    ->where('pdh_dtl.companyid', trim($request->get('companyid')))
                    ->where('pdh_dtl.no_dokumen', trim($request->get('nomor_dokumen')));

                    // ! jika kode part tidak kosong maka user update per part
                    // ! jika kode part kosong maka user update semua part atau 1 dokumen
                    if($request->get('kode_part') != null){
                        $sql_detail = $sql_detail->where('pdh_dtl.kd_part', trim($request->get('kode_part')));
                    }

                    $sql_detail = $sql_detail
                    ->where('pdh_dtl.pindah', '>' , 0)
                    ->whereRaw("(pdh_dtl.sts_mp_awal <> 1 or pdh_dtl.sts_mp_awal is null or pdh_dtl.sts_mp_tujuan <> 1 or pdh_dtl.sts_mp_tujuan is null)")
                    ->orderBy('pdh_dtl.kd_part', 'asc')
                    ->get();

            // ! jika part tidak ditemukan
            if(empty($sql_detail) || count($sql_detail) == 0) {
                return Response::responseWarning("Part tidak ditemukan, Kemungkinan semua part pada Dokumen : ". $request->get('nomor_dokumen') ." sudah di update ke marketplace !");
            }

            // TODO: cek koneksi Shopee dan Tokopedia
            $responseShopee = ServiceShopee::getShopInfo($token->shopee);
            $statusServerShopee = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

            $responseTokopedia = ServiceTokopedia::GetShopInfo(trim($token->tokopedia));
            $statusServerTokopedia = (empty(json_decode($responseTokopedia)->message)) ? 1 : 0;

            if($statusServerShopee == 0 || $statusServerTokopedia == 0) {
                $responseUpdateToken =  UpdateToken::shopee($auth_token);
                $responseUpdateToken = UpdateToken::tokopedia($auth_token);

                if($responseUpdateToken->status == 0) {
                    return Response::responseWarning($responseUpdateToken->message);
                }
                if($responseUpdateToken->status == 0) {
                    return Response::responseWarning($responseUpdateToken->message);
                }

                $token->tokopedia = $responseUpdateToken->data->token;
                $token->shopee = $responseUpdateToken->data->token;
            }

            // ! Variabel untuk menampung data yang akan di update
            $part_error = (object)[
                'shopee'    => [],
                'tokopedia' => []
            ];
            $part_ready_update = (object)[
                'shopee'    => [],
                'tokopedia' => []
            ];
            $part_sukses_update = (object)[
                'shopee'    => [],
                'tokopedia' => []
            ];

            foreach ($sql_detail as $key => $value) {
                // ! melihat status pada detail apakah sudah pernah di update
                if (trim($sql_header->lokasi_awal) == config('constants.shopee.kode_lokasi')){
                    $statusUpdateShopee = $value->status_mp_awal;
                    $statusUpdateTokopedia = $value->status_mp_tujuan;
                } else {
                    $statusUpdateShopee = $value->status_mp_tujuan;
                    $statusUpdateTokopedia = $value->status_mp_awal;
                }

                // ! Memilah mana yang belum ada product id pada database internal jika product id kosong maka akan di masukan ke array part_masalah
                // ! jika tidak kosong maka akan di masukan ke array part_ready_update

                // ! Shopee
                if($statusUpdateShopee == 1) {
                    // ! jika sudah pernah di update maka akan di masukan ke array part_sukses_update
                    $part_sukses_update->shopee[] = (object)[
                        'kode_part'     => trim($value->kode_part),
                        'product_id'    => trim($value->product_id_shopee),
                        'keterangan'    => 'Product ID Shopee sudah Pernah di update'
                    ];
                } else {
                    if(empty($value->product_id_shopee)) {
                        $part_error->shopee[] = (object)[
                            'kode_part'     => trim($value->kode_part),
                            'product_id'    => '',
                            'keterangan'    => 'Product ID Shopee di Internal tidak ditemukan'
                        ];
                    } else {
                        $part_ready_update->shopee[] = (object)[
                            'kode_part'     => trim($value->kode_part),
                            'product_id'    => trim($value->product_id_shopee),
                            'jumlah_pindah' => $value->jumlah_pindah
                        ];
                    }
                }

                // ! Tokopedia
                if($statusUpdateTokopedia == 1) {
                    // ! jika sudah pernah di update maka akan di masukan ke array part_sukses_update
                    $part_sukses_update->tokopedia[] = (object)[
                        'kode_part'     => trim($value->kode_part),
                        'product_id'    => trim($value->product_id_tokopedia),
                        'keterangan'    => 'Product ID Tokopedia sudah Pernah di update'
                    ];
                } else {
                    if(empty($value->product_id_tokopedia)){
                        $part_error->tokopedia[] = (object)[
                            'kode_part'     => trim($value->kode_part),
                            'product_id'    => '',
                            'keterangan'    => 'Product ID Tokopedia di Internal tidak ditemukan'
                        ];
                    } else {
                        $part_ready_update->tokopedia[] = (object)[
                            'kode_part'     => trim($value->kode_part),
                            'product_id'    => trim($value->product_id_tokopedia),
                            'jumlah_pindah' => $value->jumlah_pindah
                        ];
                    }
                }
            }

            // ! jika tidak ada produk yang ready untuk di update maka akan mengembalikan response
            // ! arti tidak ready berarti product id kosong pada database internal pengecekan berada diatas
            if (
                    (empty($part_ready_update->shopee) && empty($part_ready_update->tokopedia)) ||
                    (count($part_ready_update->shopee) == 0 && count($part_ready_update->tokopedia) == 0)
                ) {
                return Response::responseSuccess('', [ 'nomer_dokumen' => trim(strtoupper($sql_header->nomor_dokumen)), 'error_list' => $part_error, 'success_list' => []]);
            }

            // ! cek jika status detail shopee sudah 1 maka sudah diupdate pada shopee tidak perlu di update lagi maka akan melewati update pada shopee
            // ! Sudah diganti dengan keterangan sudah pernah diupdate pada shopee yaitu program diatas
            if($statusUpdateShopee != 1) {
                // TODO: ambil data stok pada api shopee
                $api_getitem_shopee = json_decode(ServiceShopee::getItem(
                    $token->shopee,
                    collect($part_ready_update->shopee)->pluck('product_id')->implode(','),
                ));
                if(!empty($api_getitem_shopee->error)){
                    return Response::responseWarning('Maaf koneksi kepihak shopee terputus, Mohon untuk mencoba beberapa saat lagi.');
                }

                // ! SHOPEE
                foreach ($api_getitem_shopee->response->item_list as $key => $value) {
                    // TODO: update stok pada api shopee
                    $api_updateitem_shopee = json_decode(ServiceShopee::updateStockPerPart($token->shopee, $value->item_id,(
                        (trim($sql_header->lokasi_tujuan) == config('constants.shopee.kode_lokasi'))?
                            ( $value->stock_info_v2->seller_stock[0]->stock + collect($part_ready_update->shopee)->where('product_id', $value->item_id)->first()->jumlah_pindah):
                            ( $value->stock_info_v2->seller_stock[0]->stock - collect($part_ready_update->shopee)->where('product_id', $value->item_id)->first()->jumlah_pindah)
                        )));

                    // ! jika terjadi error pada saat update stok pada api shopee maka akan di masukan ke array part_masalah
                    // ! jika tidak terjadi error maka akan di masukan ke array part_sukses_update
                    if (!empty($api_updateitem_shopee->error)){
                        $part_error->shopee[] = (object)[
                            'kode_part'     => collect($part_ready_update->shopee)->where('product_id', $value->item_id)->first()->kode_part,
                            'product_id'    => $value->item_id,
                            'keterangan'    => $api_updateitem_shopee->message
                        ];
                    } else {
                        $part_sukses_update->shopee[] = (object)[
                            'kode_part' => collect($part_ready_update->shopee)->where('product_id', $value->item_id)->first()->kode_part,
                            'product_id' => $value->item_id,
                            'keterangan' => 'Berhasil Di update ke shopee menjadi : '.$api_updateitem_shopee->response->success_list[0]->stock
                        ];
                    }
                }
                if(!empty($part_sukses_update->shopee) || count($part_sukses_update->shopee) > 0) {
                    if( $request->get('kode_part') == null ) {
                        // TODO: Update status pada database internal
                        // ! Shopee update status all
                        if (!empty($part_sukses_update->shopee)) {
                            DB::transaction(function () use ($part_sukses_update, $sql_header, $request) {
                                DB::insert('exec SP_PdhLok_UpdateStsMP_ShopeeAll ?,?,?,?', [
                                    trim(strtoupper($sql_header->nomor_dokumen)),
                                    config('constants.shopee.kode_lokasi'),
                                    trim(strtoupper($request->get('companyid'))),
                                    trim(collect($part_sukses_update->shopee)->pluck('product_id')->implode(',')),
                                ]);
                            });
                        }
                    } else {
                        // ! Shopee update status per Part
                        if (!empty($part_sukses_update->shopee)) {
                            DB::transaction(function () use ($request) {
                                DB::insert('exec sp_PdhLok_UpdateStsMP ?,?,?,?', [
                                    trim(strtoupper(trim($request->get('nomor_dokumen')))),
                                    config('constants.shopee.kode_lokasi'),
                                    trim(strtoupper($request->get('kode_part'))),
                                    trim(strtoupper($request->get('companyid')))
                                ]);
                            });
                        }
                    }
                }
            }

            if($statusUpdateTokopedia != 1) {
                // ! TOKOPEDIA
                // ! merubah ke format tokopedia
                $part_update_tokopedia = [];
                foreach ($part_ready_update->tokopedia as $key => $value) {
                    $part_update_tokopedia[] = (object)[
                        'product_id'    => (int)$value->product_id,
                        'stock_value'   => (double)$value->jumlah_pindah
                    ];
                }

                // ! update increment/decrement stock tokopedia
                if(trim($sql_header->lokasi_tujuan) == config('constants.tokopedia.kode_lokasi')) {
                    $responseUpdateStock = ServiceTokopedia::ProductUpdateStockIncrement(trim($token->tokopedia), $part_update_tokopedia);
                } else {
                    $responseUpdateStock = ServiceTokopedia::ProductUpdateStockDecrement(trim($token->tokopedia), $part_update_tokopedia);
                }

                $statusUpdateStock = (empty(json_decode($responseUpdateStock)->header->error_code)) ? 1 : 0;

                if($statusUpdateStock == 0) {
                    return response()->json([
                        'status'    => 0,
                        'message'   => 'Gagal mengakses API Increment/Decrement Tokopedia, Coba lagi atau hubungi IT Programmer. '.
                                        json_decode($responseUpdateStock)->header->error_code.' = '.json_decode($responseUpdateStock)->header->reason
                    ]);
                } else {
                    // ! jika $statusUpdateStock diatas lolos maka jika data tidak ada maka diganti dengan object kosong
                    $resultUpdateStock = empty(json_decode($responseUpdateStock)->data)? (object)[] : json_decode($responseUpdateStock)->data;

                    // ! jika pengecekan diatas menghasilkan object kosong maka akan di masukan ke array part_error dan ditampilkan
                    if(collect($resultUpdateStock)->count() == 0) {
                        $part_error->tokopedia[] = (object)[
                            'kode_part'     => '-',
                            'product_id'    => '-',
                            'keterangan'    => json_decode($responseUpdateStock)->message
                        ];
                    } else {
                        if((double)$resultUpdateStock->failed_rows > 0) {
                            foreach($resultUpdateStock->failed_rows_data as $value) {
                                $part_error->tokopedia[] = (object)[
                                    'kode_part'     => collect($part_ready_update->tokopedia)->where('product_id', $value->product_id)->first()->kode_part,
                                    'product_id'    => $value->product_id,
                                    'keterangan'    => $value->message
                                ];
                            }
                        }

                        if((double)$resultUpdateStock->succeed_rows > 0) {
                            foreach($resultUpdateStock->succeed_rows_data as $value) {
                                $part_sukses_update->tokopedia[] = (object)[
                                    'kode_part' => collect($part_ready_update->tokopedia)->where('product_id', $value->productID)->first()->kode_part,
                                    'product_id' => $value->productID,
                                    'keterangan' => 'Berhasil Di update ke Tokopedia menjadi : '. (empty($value->stock)? 0 : $value->stock)
                                ];
                            }
                        }
                    }
                }

                if(!empty($part_sukses_update->tokopedia) || count($part_sukses_update->tokopedia) > 0) {
                    if( $request->get('kode_part') == null ) {
                        // TODO: Update status pada database internal
                        // ! Tokopedia update status all
                        if (!empty($part_sukses_update->tokopedia)) {
                            DB::transaction(function () use ($part_sukses_update, $sql_header, $request) {
                                DB::insert('exec SP_PdhLok_UpdateStsMP_TokopediaAll ?,?,?,?', [
                                    trim(strtoupper($sql_header->nomor_dokumen)),
                                    config('constants.tokopedia.kode_lokasi'),
                                    trim(strtoupper($request->get('companyid'))),
                                    trim(collect($part_sukses_update->tokopedia)->pluck('product_id')->implode(',')),
                                ]);
                            });
                        }
                    } else {
                        // ! Tokopedia update status per Part
                        if (!empty($part_sukses_update->tokopedia)) {
                            DB::transaction(function () use ($request) {
                                DB::insert('exec sp_PdhLok_UpdateStsMP ?,?,?,?', [
                                    trim(strtoupper(trim($request->get('nomor_dokumen')))),
                                    config('constants.tokopedia.kode_lokasi'),
                                    trim(strtoupper($request->get('kode_part'))),
                                    trim(strtoupper($request->get('companyid')))
                                ]);
                            });
                        }
                    }
                }
            }

            // ! Jika semua part pada dokumen tidak berhasil di update maka akan di kembalikan response error
            if(
                (empty($part_sukses_update->shopee) && empty($part_sukses_update->tokopedia)) ||
                (count($part_sukses_update->shopee) == 0 && count($part_sukses_update->tokopedia) == 0)
                ) {
                return Response::responseSuccess('Success', [ 'nomer_dokumen' => trim(strtoupper($sql_header->nomor_dokumen)), 'error_list' => $part_error, 'success_list' => []]);
            }

            // ! Jika terdapat part pada dokumen berhasil di update maka akan di kembalikan response success
            return Response::responseSuccess('Data Berhasil Disimpan', [ 'nomer_dokumen' => trim(strtoupper($sql_header->nomor_dokumen)), 'error_list' => $part_error, 'success_list' => $part_sukses_update]);

        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function updateStatusPerPartNumber(Request $request)
    {
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

                DB::insert('exec sp_PdhLok_UpdateStsMP ?,?,?,?', [
                    trim(strtoupper($request->get('nomor_dokumen'))), config('constants.tokopedia.kode_lokasi'),
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
