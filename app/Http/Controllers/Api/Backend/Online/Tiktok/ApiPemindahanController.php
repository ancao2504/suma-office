<?php

namespace App\Http\Controllers\Api\Backend\Online\Tiktok;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use App\Helpers\Api\UpdateToken;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Helpers\Api\ServiceTiktok;
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
                                substring(isnull(pdh.usertime, ''), 25, 50) as users,
                                isnull(pdh.sts_ctk, 0) as status_cetak,
                                isnull(iif(isnull(pdh.tgl_sj, '')='', 0, 1), 0) as status_sj,
                                isnull(iif(isnull(pdh.tgl_in, '')='', 0, 1), 0) as status_validasi,
                                isnull(pdh.sts_mp, 0) as status_marketplace")
                    ->whereBetween('pdh.tanggal', [ $request->get('start_date'), $request->get('end_date') ])
                    ->whereRaw("(pdh.kd_lokasi1='".config('constants.api.tiktok.kode_lokasi')."' or
                            pdh.kd_lokasi2='".config('constants.api.tiktok.kode_lokasi')."')")
                    ->where('pdh.companyid', trim($request->get('companyid')))
                    ->orderBy('pdh.no_dokumen', 'desc');

            if(!empty($request->get('nomor_dokumen')) && trim($request->get('nomor_dokumen')) != '') {
                $sql->where('pdh.no_dokumen', 'like', '%'.trim($request->get('nomor_dokumen')).'%');
            }

            $result = $sql->paginate((empty($request->get('per_page'))) ? 10 : $request->get('per_page'));

            return Response::responseSuccess('success', $result);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function formPemindahan(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_dokumen' => 'required|string',
                'companyid'     => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih nomor dokumen terlebih dahulu");
            }

            $sql = "select	isnull(pdh.no_dokumen, '') as nomor_dokumen, isnull(pdh.tanggal, '') as tanggal,
                            isnull(pdh.kd_lokasi1, '') as kode_lokasi_awal, isnull(lokasi.ket, '') as nama_lokasi_awal,
                            isnull(lokasi.alamat1, '') as alamat_lokasi_awal, isnull(lokasi.kota, '') as kota_lokasi_awal,
                            isnull(pdh.kd_lokasi2, '') as kode_lokasi_tujuan, isnull(lokasi2.ket, '') as nama_lokasi_tujuan,
                            isnull(lokasi2.alamat1, '') as alamat_lokasi_tujuan, isnull(lokasi2.kota, '') as kota_lokasi_tujuan,
                            isnull(pdh.keterangan, '') as keterangan, substring(isnull(pdh.usertime, ''), 25, 50) as users,
                            isnull(pdh.sts_ctk, 0) as status_cetak, isnull(iif(isnull(pdh.tgl_sj, '')='', 0, 1), 0) as status_sj,
                            isnull(iif(isnull(pdh.tgl_in, '')='', 0, 1), 0) as status_validasi, isnull(pdh.sts_mp, 0) as status_mp_header
                    from
                    (
                        select	*
                        from	pdh with (nolock)
                        where	pdh.no_dokumen=? and
                                pdh.companyid=?
                    )	pdh
                            left join lokasi with (nolock) on pdh.kd_lokasi1=lokasi.kd_lokasi and
                                    pdh.companyid=lokasi.companyid
                            left join lokasi lokasi2 with (nolock) on pdh.kd_lokasi2=lokasi2.kd_lokasi and
                                    pdh.companyid=lokasi2.companyid";

            $result = DB::select($sql, [ $request->get('nomor_dokumen'), $request->get('companyid') ]);

            $jumlah_data = 0;
            $data_pemindahan = [];

            foreach($result as $data) {
                $jumlah_data = (double)$jumlah_data + 1;

                $status_update_marketplace = 0;
                if((int)$data->status_cetak == 1 && (int)$data->status_sj == 1 && (int)$data->status_validasi == 1 &&
                    (int)$data->status_mp_header ==  0) {
                        $status_update_marketplace = 1;
                    }

                $data_pemindahan = [
                    'nomor_dokumen'     => trim($data->nomor_dokumen),
                    'tanggal'           => trim($data->tanggal),
                    'lokasi'            => [
                        'awal'          => [
                            'kode'      => trim($data->kode_lokasi_awal),
                            'keterangan' => trim($data->nama_lokasi_awal),
                            'alamat'    => trim($data->alamat_lokasi_awal),
                            'kota'      => trim($data->kota_lokasi_awal),
                        ],
                        'tujuan'         => [
                            'kode'       => trim($data->kode_lokasi_tujuan),
                            'keterangan' => trim($data->nama_lokasi_tujuan),
                            'alamat'    => trim($data->alamat_lokasi_tujuan),
                            'kota'      => trim($data->kota_lokasi_tujuan),
                        ],
                    ],
                    'keterangan'        => trim($data->keterangan),
                    'users'             => trim($data->users),
                    'status'            => [
                        'cetak'         => (int)$data->status_cetak,
                        'sj'            => (int)$data->status_sj,
                        'validasi'      => (int)$data->status_validasi,
                        'marketplace'   => [
                            'update'    => (int)$data->status_mp_header,
                            'show'      => (int)$status_update_marketplace
                        ]
                    ]
                ];
            }

            if((double)$jumlah_data <= 0) {
                return Response::responseWarning('Data pemindahan tidak ditemukan');
            }

            return Response::responseSuccess('success', $data_pemindahan);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function formDetailPemindahan(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_dokumen' => 'required|string',
                'companyid'     => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih nomor dokumen terlebih dahulu");
            }

            $sql = "select	isnull(pdh.no_dokumen, '') as nomor_dokumen, isnull(pdh.kd_lokasi1, '') as kode_lokasi_awal,
                            isnull(pdh.kd_lokasi2, '') as kode_lokasi_tujuan, isnull(pdh_dtl.kd_part, '') as part_number,
                            isnull(part.ket, '') as nama_part, isnull(pdh_dtl.pindah, 0) as pindah,
                            iif(pdh.kd_lokasi1 = '".config('constants.api.tiktok.kode_lokasi')."',
                                isnull(pdh_dtl.sts_mp_awal, 0), isnull(pdh_dtl.sts_mp_tujuan, 0)) as status_mp_detail,
                            isnull(part.tiktok_id, 0) as product_id,
                            isnull(stlokasi.jumlah, 0) - isnull(stlokasi.in_transit, 0) as stock,
                            isnull(pdh.sts_ctk, 0) as status_cetak, isnull(iif(isnull(pdh.tgl_sj, '')='', 0, 1), 0) as status_sj,
                            isnull(iif(isnull(pdh.tgl_in, '')='', 0, 1), 0) as status_validasi, isnull(pdh.sts_mp, 0) as status_mp_header
                    from
                    (
                        select	*
                        from	pdh with (nolock)
                        where	pdh.no_dokumen=? and
                                pdh.companyid=?
                    )	pdh
                            left join pdh_dtl with (nolock) on pdh.no_dokumen=pdh_dtl.no_dokumen and
                                    pdh.companyid=pdh_dtl.companyid
                            left join part with (nolock) on pdh_dtl.kd_part=part.kd_part and
                                    pdh.companyid=part.companyid
                            left join stlokasi with (nolock) on pdh_dtl.kd_part=stlokasi.kd_part and
                                    pdh.companyid=stlokasi.companyid and stlokasi.kd_lokasi='".config('constants.api.tiktok.kode_lokasi')."'
                    where	isnull(pdh_dtl.pindah, 0) > 0
                    order by pdh_dtl.kd_part asc";

            $result = DB::select($sql, [ $request->get('nomor_dokumen'), $request->get('companyid') ]);

            $product_id = '';
            $data_part_tiktok = new Collection();
            $data_detail_pemindahan = new Collection();

            foreach($result as $data) {
                if(trim($data->product_id) != '') {
                    if(trim($product_id) == '') {
                        $product_id = '"'.trim($data->product_id).'"';
                    } else {
                        $product_id .= ',"'.trim($data->product_id).'"';
                    }
                }

                $status_update_marketplace = 0;
                $keterangan_update_marketplace = '';

                if((int)$data->status_cetak == 1 && (int)$data->status_sj == 1 && (int)$data->status_validasi == 1 &&
                    (int)$data->status_mp_header ==  0) {
                        $status_update_marketplace = 1;
                    } else {
                        $keterangan_update_marketplace = 'DATA BELUM DI VALIDASI';
                    }

                if((int)$data->status_mp_detail == 1) {
                    $keterangan_update_marketplace = 'DATA SUDAH PERNAH DI UPDATE KE MARKETPLACE';
                    $status_update_marketplace = 0;
                }

                $data_detail_pemindahan->push((object) [
                    'nomor_dokumen' => trim($data->nomor_dokumen),
                    'product_id'    => trim($data->product_id),
                    'part_number'   => strtoupper(trim($data->part_number)),
                    'nama_part'     => trim($data->nama_part),
                    'stock'         => (double)$data->stock,
                    'pindah'        => (double)$data->pindah,
                    'indicator'     => (trim($data->kode_lokasi_awal) == config('constants.api.tiktok.kode_lokasi')) ? 'DECREMENT' : 'INCREMENT',
                    'status_mp'     => [
                        'update'    => (int)$data->status_mp_detail,
                        'show'      => (int)$status_update_marketplace,
                        'keterangan' => strtoupper(trim($keterangan_update_marketplace)),
                    ]
                ]);
            }

            $authorization = $request->header('Authorization');
            $token = explode(" ", $authorization);
            $auth_token = trim($token[1]);

            $token_tiktok = '';

            $sql = DB::table('user_api_office')->lock('with (nolock)')
                    ->selectRaw("isnull(user_api_office.tiktok_token, '') as tiktok_token,
                                isnull(user_api_office.user_id, '') as user_id")
                    ->where('user_api_office.office_token', $auth_token)
                    ->orderByRaw("isnull(user_api_office.id, 0) desc")
                    ->first();

            if(empty($sql->tiktok_token) || trim($sql->tiktok_token) == '') {
                return Response::responseWarning('Token tiktok tidak ditemukan, lakukan logout kemudian login kembali');
            } else {
                $token_tiktok = $sql->tiktok_token;
            }

            // ==========================================================================
            // CEK KONEKSI API TIKTOK
            // ==========================================================================
            $responseTiktok = ServiceTiktok::GetAuthorizedShops(trim($token_tiktok));
            $statusServer = (json_decode($responseTiktok)->code == 0) ? 1 : 0;

            if($statusServer == 0) {
                $authorization = $request->header('Authorization');
                $token = explode(" ", $authorization);
                $auth_token = trim($token[1]);

                $responseUpdateToken = UpdateToken::tiktok($auth_token);

                if($responseUpdateToken->status == 1) {
                    $token_tiktok = $responseUpdateToken->data->token;
                } else {
                    return Response::responseWarning($responseUpdateToken->message);
                }
            }

            // ==========================================================================
            // AMBIL DATA TIKTOK
            // ==========================================================================
            $responseTiktok = ServiceTiktok::GetProductStock(trim($token_tiktok), trim($product_id));
            $statusResponseTiktok = (json_decode($responseTiktok)->code == 0) ? 1 : 0;

            if($statusResponseTiktok == 1) {
                $dataTiktok = json_decode($responseTiktok)->data;

                foreach($dataTiktok->product_stocks as $detailTiktok) {
                    $status_campaign_stock = [];
                    $status_creator_stock = [];
                    $productID = trim($detailTiktok->product_id);
                    $skuID = '';
                    $skuSeller = '';
                    $in_shop_stock = 0;
                    $commited_stock = 0;
                    $total_stock = 0;

                    foreach($detailTiktok->skus as $detailSku) {
                        $skuID = trim($detailSku->sku_id);
                        $skuSeller = trim($detailSku->seller_sku);
                        $total_stock = (double)$detailSku->total_available_stock;
                        $commited_stock = (double)$detailSku->total_committed_stock;
                        $in_shop_stock = (double)$detailSku->total_available_stock_distribution->in_shop_stock;

                        if(!empty($detailSku->total_available_stock_distribution->campaign_stock)) {
                            foreach($detailSku->total_available_stock_distribution->campaign_stock as $campaignStock) {
                                $status_campaign_stock[] = [
                                    'campaign_name'     => trim($campaignStock->campaign_name),
                                    'available_stock'   => (double)$campaignStock->available_stock
                                ];
                            }
                        }

                        if(!empty($detailSku->total_available_stock_distribution->creator_stock)) {
                            foreach($detailSku->total_available_stock_distribution->creator_stock as $creatorStock) {
                                $status_creator_stock[] = [
                                    'creator_name'      => trim($creatorStock->creator_name),
                                    'available_stock'   => (double)$creatorStock->available_stock
                                ];
                            }
                        }
                    }

                    $status = [];
                    $status = [
                        'in_shop_stock'     => (double)$in_shop_stock,
                        'campaign'          => $status_campaign_stock,
                        'creator'           => $status_creator_stock,
                        'commited_stock'    => (double)$commited_stock,
                    ];

                    $data_part_tiktok->push((object) [
                        'productID'     => (trim($productID) == '') ? '' : trim($productID),
                        'sku_id'        => (trim($skuID) == '') ? '' : trim($skuID),
                        'sku_seller'    => (trim($skuSeller) == '') ? '' : trim($skuSeller),
                        'stock'         => (trim($total_stock) == '') ? '' : trim($total_stock),
                        'status'        => $status,
                    ]);
                }
            } else {
                $data_part_tiktok->push((object) [
                    'productID'     => '',
                    'sku'           => '',
                    'stock'         => 0,
                    'status'        => 'Not Connected',
                ]);
            }

            $data_pemindahan = new Collection();
            foreach($data_detail_pemindahan as $detail) {
                $data_pemindahan->push((object) [
                    'nomor_dokumen' => strtoupper(trim($detail->nomor_dokumen)),
                    'product_id'    => strtoupper(trim($detail->product_id)),
                    'part_number'   => strtoupper(trim($detail->part_number)),
                    'nama_part'     => strtoupper(trim($detail->nama_part)),
                    'stock_suma'    => $detail->stock,
                    'pindah'        => $detail->pindah,
                    'indicator'     => $detail->indicator,
                    'status_mp'     => $detail->status_mp,
                    'marketplace'   => $data_part_tiktok
                                        ->where('productID', strtoupper(trim($detail->product_id)))
                                        ->first()
                ]);
            }

            return Response::responseSuccess('success', $data_pemindahan);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function updateStockPerPartNumber(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_dokumen' => 'required|string',
                'part_number'   => 'required|string',
                'companyid'     => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih nomor dokumen dan part number terlebih dahulu");
            }

            $kode_lokasi_awal = '';
            $kode_lokasi_tujuan = '';
            $status_mp_header = 0;
            $status_mp_detail = 0;
            $product_id = '';
            $data_pemindahan = new Collection();

            $sql = "select	isnull(pdh.no_dokumen, '') as nomor_dokumen,
                            isnull(pdh.kd_lokasi1, '') as kode_lokasi_awal,
                            isnull(pdh.kd_lokasi2, '') as kode_lokasi_tujuan,
                            isnull(pdh.sts_mp, '') as status_mp_header,
                            isnull(pdh_dtl.kd_part, '') as part_number,
                            isnull(part.tiktok_id, 0) as product_id,
                            isnull(pdh_dtl.pindah, 0) as pindah,
                            iif(pdh.kd_lokasi1 = '".config('constants.api.tiktok.kode_lokasi')."',
                                isnull(pdh_dtl.sts_mp_awal, 0), isnull(pdh_dtl.sts_mp_tujuan, 0)) as status_mp_detail
                    from
                    (
                        select	top 1 companyid, no_dokumen, kd_lokasi1,
                                kd_lokasi2, sts_mp
                        from	pdh with (nolock)
                        where	pdh.no_dokumen=? and pdh.companyid=? and
                                (pdh.kd_lokasi1 = '".config('constants.api.tiktok.kode_lokasi')."' or
                                pdh.kd_lokasi2 = '".config('constants.api.tiktok.kode_lokasi')."')
                    )	pdh
                            inner join pdh_dtl with (nolock) on pdh.no_dokumen=pdh_dtl.no_dokumen and
                                        pdh.companyid=pdh_dtl.companyid
                            left join part with (nolock) on pdh_dtl.kd_part=part.kd_part and
                                        pdh.companyid=part.companyid
                    where	pdh_dtl.kd_part=?";

            $result = DB::select($sql, [ strtoupper(trim($request->get('nomor_dokumen'))),
                        strtoupper(trim($request->get('companyid'))),
                        strtoupper(trim($request->get('part_number')))
                    ]);

            $jumlah_data = 0;
            foreach($result as $data) {
                $jumlah_data = (double)$jumlah_data + 1;

                $kode_lokasi_awal = strtoupper(trim($data->kode_lokasi_awal));
                $kode_lokasi_tujuan = strtoupper(trim($data->kode_lokasi_tujuan));
                $status_mp_header = (int)$data->status_mp_header;
                $product_id = '"'.strtoupper(trim($data->product_id)).'"';
                $status_mp_detail = (int)$data->status_mp_detail;

                $data_pemindahan->push((object) [
                    'nomor_dokumen'         => strtoupper(trim($data->nomor_dokumen)),
                    'kode_lokasi_awal'      => strtoupper(trim($data->kode_lokasi_awal)),
                    'kode_lokasi_tujuan'    => strtoupper(trim($data->kode_lokasi_tujuan)),
                    'part_number'           => strtoupper(trim($data->part_number)),
                    'product_id'            => strtoupper(trim($data->product_id)),
                    'pindah'                => (double)$data->pindah
                ]);
            }

            if((double)$jumlah_data <= 0) {
                return Response::responseWarning("Data part number di nomor pemindahan tidak terdaftar di nomor dokumen ini");
            }

            if((int)$status_mp_header == 1) {
                return Response::responseWarning("Nomor dokumen ini sudah pernah di update ke marketplace");
            } else {
                if((int)$status_mp_detail == 1) {
                    return Response::responseWarning("Nomor dokumen dengan part number ini sudah pernah di update ke marketplace");
                }
            }

            if(trim($product_id) == '') {
                return Response::responseWarning("Data product id belum terdaftar di database internal");
            }

            if(strtoupper(trim($kode_lokasi_awal)) == config('constants.api.tiktok.kode_lokasi') ||
                strtoupper(trim($kode_lokasi_tujuan)) == config('constants.api.tiktok.kode_lokasi')) {
                // ==========================================================================
                // AMBIL TOKEN TIKTOK
                // ==========================================================================
                $authorization = $request->header('Authorization');
                $token = explode(" ", $authorization);
                $auth_token = trim($token[1]);

                $token_tiktok = '';

                $sql = DB::table('user_api_office')->lock('with (nolock)')
                    ->selectRaw("isnull(user_api_office.tiktok_token, '') as tiktok_token,
                                isnull(user_api_office.user_id, '') as user_id")
                    ->where('user_api_office.office_token', $auth_token)
                    ->orderByRaw("isnull(user_api_office.id, 0) desc")
                    ->first();

                if(empty($sql->tiktok_token) || trim($sql->tiktok_token) == '') {
                    return Response::responseWarning('Token tiktok tidak ditemukan, lakukan logout kemudian login kembali');
                } else {
                    $token_tiktok = $sql->tiktok_token;
                }

                // ==========================================================================
                // CEK KONEKSI API TIKTOK
                // ==========================================================================
                $responseTiktok = ServiceTiktok::GetAuthorizedShops(trim($token_tiktok));
                $statusServer = (json_decode($responseTiktok)->code == 0) ? 1 : 0;

                if($statusServer == 0) {
                    $authorization = $request->header('Authorization');
                    $token = explode(" ", $authorization);
                    $auth_token = trim($token[1]);

                    $responseUpdateToken = UpdateToken::tiktok($auth_token);

                    if($responseUpdateToken->status == 1) {
                        $token_tiktok = $responseUpdateToken->data->token;
                    } else {
                        return Response::responseWarning($responseUpdateToken->message);
                    }
                }

                // ==========================================================================
                // GET DATA PRODUCT TIKTOK
                // ==========================================================================
                $responseTiktok = ServiceTiktok::GetProductStock(trim($token_tiktok), trim($product_id));
                $messageResponseTiktok = json_decode($responseTiktok)->message;
                $statusResponseTiktok = (json_decode($responseTiktok)->code == 0) ? 1 : 0;

                if($statusResponseTiktok == 1) {
                    $dataApi = json_decode($responseTiktok)->data->product_stocks;

                    $data_sku = [];

                    $product_id_tikok = '';
                    $part_number = '';

                    foreach($dataApi as $data) {
                        $data_stock_info = [];
                        $product_id_tikok = $data->product_id;

                        foreach($data->skus as $sku) {
                            $part_number = trim($sku->seller_sku);

                            foreach($sku->warehouse_stock_infos as $warehouse) {
                                $jumlah_stock = (double)$warehouse->available_stock;

                                foreach($data_pemindahan as $pindah) {
                                    if(trim($product_id_tikok) == trim($pindah->product_id)) {
                                        if(strtoupper(trim($pindah->kode_lokasi_awal)) == strtoupper(trim(config('constants.api.tiktok.kode_lokasi')))) {
                                            $jumlah_stock = (double)$jumlah_stock - (double)$pindah->pindah;
                                        }
                                        if(strtoupper(trim($pindah->kode_lokasi_tujuan)) == strtoupper(trim(config('constants.api.tiktok.kode_lokasi')))) {
                                            $jumlah_stock = (double)$jumlah_stock + (double)$pindah->pindah;
                                        }
                                    }

                                }
                                $data_stock_info[] = [
                                    'warehouse_id'      => $warehouse->warehouse_id,
                                    'available_stock'   => (double)$jumlah_stock
                                ];
                            }

                            $data_sku[] = [
                                'id'            => $sku->sku_id,
                                'stock_infos'   => $data_stock_info
                            ];
                        }

                        $responseTiktok = ServiceTiktok::UpdateStock($token_tiktok, $product_id_tikok, json_encode($data_sku));
                        $messageResponseTiktok = json_decode($responseTiktok)->message;
                        $statusResponseTiktok = (json_decode($responseTiktok)->code == 0) ? 1 : 0;

                        if($statusResponseTiktok == 0) {
                            return Response::responseWarning('Gagal mengupdate stock part number '.$part_number.' di marketplace Tiktok \n'. $messageResponseTiktok);
                        } else {
                            DB::transaction(function () use ($request, $part_number) {
                                DB::insert('exec sp_PdhLok_UpdateStsMP ?,?,?,?', [
                                    trim(strtoupper($request->get('nomor_dokumen'))),
                                    config('constants.api.tiktok.kode_lokasi'),
                                    trim(strtoupper($part_number)),
                                    trim(strtoupper($request->get('companyid')))
                                ]);
                            });

                            $data_update_status = [
                                'update_status' => [
                                    'error'     => '',
                                    'message'   => 'Data marketplace Tiktok part number '.$part_number.' berhasil diupdate'
                                ]
                            ];

                            return Response::responseSuccess('Data Berhasil Disimpan', $data_update_status);
                        }
                    }
                } else {
                    return Response::responseWarning('Gagal mengakses API Tiktok Marketplace '.$messageResponseTiktok);
                }
            } else {
                return Response::responseWarning('Lokasi awal atau tujuan bukan milik marketplace Tiktok');
            }
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
                    trim(strtoupper($request->get('nomor_dokumen'))), config('constants.api.tiktok.kode_lokasi'),
                    trim(strtoupper($request->get('part_number'))), trim(strtoupper($request->get('companyid')))
                ]);
            });

            return Response::responseSuccess('Data Berhasil Disimpan', null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function updateStockPerNomorDokumen(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_dokumen' => 'required|string',
                'companyid'     => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih nomor dokumen terlebih dahulu");
            }

            $product_id_tiktok = '';
            $kode_lokasi_awal = '';
            $kode_lokasi_tujuan = '';
            $status_mp_header = 0;
            $data_pemindahan = new Collection();

            $sql = "select	isnull(pdh.no_dokumen, '') as nomor_dokumen,
                            isnull(pdh.kd_lokasi1, '') as kode_lokasi_awal,
                            isnull(pdh.kd_lokasi2, '') as kode_lokasi_tujuan,
                            isnull(pdh.sts_mp, '') as status_mp_header,
                            isnull(pdh_dtl.kd_part, '') as part_number,
                            isnull(part.tiktok_id, 0) as product_id,
                            isnull(pdh_dtl.pindah, 0) as pindah,
                            iif(pdh.kd_lokasi1 = '".config('constants.api.tiktok.kode_lokasi')."',
                                isnull(pdh_dtl.sts_mp_awal, 0), isnull(pdh_dtl.sts_mp_tujuan, 0)) as status_mp_detail
                    from
                    (
                        select	top 1 companyid, no_dokumen, kd_lokasi1,
                                kd_lokasi2, sts_mp
                        from	pdh with (nolock)
                        where	pdh.no_dokumen=? and pdh.companyid=? and
                                (pdh.kd_lokasi1 = '".config('constants.api.tiktok.kode_lokasi')."' or
                                pdh.kd_lokasi2 = '".config('constants.api.tiktok.kode_lokasi')."')
                    )	pdh
                            inner join pdh_dtl with (nolock) on pdh.no_dokumen=pdh_dtl.no_dokumen and
                                        pdh.companyid=pdh_dtl.companyid
                            left join part with (nolock) on pdh_dtl.kd_part=part.kd_part and
                                        pdh.companyid=part.companyid";

            $result = DB::select($sql, [ strtoupper(trim($request->get('nomor_dokumen'))),
                        strtoupper(trim($request->get('companyid')))
                    ]);

            $jumlah_data = 0;
            foreach($result as $data) {
                $jumlah_data = (double)$jumlah_data + 1;

                $kode_lokasi_awal = strtoupper(trim($data->kode_lokasi_awal));
                $kode_lokasi_tujuan = strtoupper(trim($data->kode_lokasi_tujuan));
                $status_mp_header = (int)$data->status_mp_header;

                if(trim($product_id_tiktok) == '') {
                    $product_id_tiktok = '"'.strtoupper(trim($data->product_id)).'"';
                } else {
                    $product_id_tiktok .= ',"'.strtoupper(trim($data->product_id)).'"';
                }

                $data_pemindahan->push((object) [
                    'nomor_dokumen'         => strtoupper(trim($data->nomor_dokumen)),
                    'kode_lokasi_awal'      => strtoupper(trim($data->kode_lokasi_awal)),
                    'kode_lokasi_tujuan'    => strtoupper(trim($data->kode_lokasi_tujuan)),
                    'part_number'           => strtoupper(trim($data->part_number)),
                    'product_id'            => strtoupper(trim($data->product_id)),
                    'pindah'                => (double)$data->pindah
                ]);
            }

            if((double)$jumlah_data <= 0) {
                return Response::responseWarning("Data dokumen tidak terdaftar");
            }

            if((int)$status_mp_header == 1) {
                return Response::responseWarning("Nomor dokumen ini sudah pernah di update ke marketplace");
            }

            if(strtoupper(trim($kode_lokasi_awal)) == config('constants.api.tiktok.kode_lokasi') ||
                strtoupper(trim($kode_lokasi_tujuan)) == config('constants.api.tiktok.kode_lokasi')) {
                // ==========================================================================
                // AMBIL TOKEN TIKTOK
                // ==========================================================================
                $authorization = $request->header('Authorization');
                $token = explode(" ", $authorization);
                $auth_token = trim($token[1]);

                $token_tiktok = '';

                $sql = DB::table('user_api_office')->lock('with (nolock)')
                    ->selectRaw("isnull(user_api_office.tiktok_token, '') as tiktok_token,
                                isnull(user_api_office.user_id, '') as user_id")
                    ->where('user_api_office.office_token', $auth_token)
                    ->orderByRaw("isnull(user_api_office.id, 0) desc")
                    ->first();

                if(empty($sql->tiktok_token) || trim($sql->tiktok_token) == '') {
                    return Response::responseWarning('Token tiktok tidak ditemukan, lakukan logout kemudian login kembali');
                } else {
                    $token_tiktok = $sql->tiktok_token;
                }

                // ==========================================================================
                // CEK KONEKSI API TIKTOK
                // ==========================================================================
                $responseTiktok = ServiceTiktok::GetAuthorizedShops(trim($token_tiktok));
                $statusServer = (json_decode($responseTiktok)->code == 0) ? 1 : 0;

                if($statusServer == 0) {
                    $authorization = $request->header('Authorization');
                    $token = explode(" ", $authorization);
                    $auth_token = trim($token[1]);

                    $responseUpdateToken = UpdateToken::tiktok($auth_token);

                    if($responseUpdateToken->status == 1) {
                        $token_tiktok = $responseUpdateToken->data->token;
                    } else {
                        return Response::responseWarning($responseUpdateToken->message);
                    }
                }

                // ==========================================================================
                // GET DATA PRODUCT TIKTOK
                // ==========================================================================
                $responseTiktok = ServiceTiktok::GetProductStock(trim($token_tiktok), trim($product_id_tiktok));
                $messageResponseTiktok = json_decode($responseTiktok)->message;
                $statusResponseTiktok = (json_decode($responseTiktok)->code == 0) ? 1 : 0;

                if($statusResponseTiktok == 1) {
                    $dataApi = json_decode($responseTiktok)->data->product_stocks;

                    $data_success_update_stock = [];
                    $data_error_update_stock = [];
                    $data_error_update_status = '';

                    $product_tikok = '';
                    $part_number = '';
                    $jumlah_stock = 0;

                    $part_success_update = '';
                    $total_success_update_part = 0;
                    $total_error_update_part = 0;
                    $total_update_part = 0;

                    foreach($dataApi as $data) {
                        $total_update_part = (double)$total_update_part + 1;

                        $data_sku = [];
                        $data_stock_info = [];
                        $product_tikok = $data->product_id;

                        foreach($data->skus as $sku) {
                            foreach($sku->warehouse_stock_infos as $warehouse) {
                                $jumlah_stock = (double)$warehouse->available_stock;

                                foreach($data_pemindahan as $pindah) {
                                    if(trim($product_tikok) == trim($pindah->product_id)) {
                                        if(strtoupper(trim($pindah->kode_lokasi_awal)) == strtoupper(trim(config('constants.api.tiktok.kode_lokasi')))) {
                                            $jumlah_stock = (double)$jumlah_stock - (double)$pindah->pindah;
                                        }
                                        if(strtoupper(trim($pindah->kode_lokasi_tujuan)) == strtoupper(trim(config('constants.api.tiktok.kode_lokasi')))) {
                                            $jumlah_stock = (double)$jumlah_stock + (double)$pindah->pindah;
                                        }
                                    }

                                }
                                $data_stock_info[] = [
                                    'warehouse_id'      => $warehouse->warehouse_id,
                                    'available_stock'   => (double)$jumlah_stock
                                ];
                            }

                            $data_sku[] = [
                                'id'            => $sku->sku_id,
                                'stock_infos'   => $data_stock_info
                            ];
                        }

                        $responseTiktok = ServiceTiktok::UpdateStock($token_tiktok, $product_tikok, json_encode($data_sku));
                        $messageResponseTiktok = json_decode($responseTiktok)->message;
                        $statusResponseTiktok = (json_decode($responseTiktok)->code == 0) ? 1 : 0;

                        if($statusResponseTiktok == 1) {
                            $total_success_update_part = (double)$total_success_update_part + 1;

                            $data_success_update_stock[] = [
                                'product_id'    => $product_tikok,
                                'stock'         => $jumlah_stock
                            ];

                            if(trim($part_success_update) == '') {
                                $part_success_update = "'".$product_tikok."'";
                            } else {
                                $part_success_update .= ",'".$product_tikok."'";
                            }
                        } else {
                            $total_error_update_part = (double)$total_error_update_part + 1;

                            if(trim($data_error_update_status)) {
                                $data_error_update_status = 'Gagal mengupdate stock marketplace Tiktok untuk part number '.$part_number;
                            } else {
                                $data_error_update_status .= ', '.$part_number;
                            }

                            $data_error_update_stock[] = [
                                'product_id'    => $product_tikok,
                                'stock'         => $jumlah_stock
                            ];
                        }
                    }

                    if(trim($part_success_update) != '') {
                        DB::transaction(function () use ($request, $part_success_update) {
                            DB::insert('exec SP_PdhLok_UpdateStsMP_TiktokAll ?,?,?,?', [
                                trim(strtoupper($request->get('nomor_dokumen'))),
                                config('constants.api.tiktok.kode_lokasi'),
                                trim(strtoupper($request->get('companyid'))),
                                trim(strtoupper($part_success_update))
                            ]);
                        });
                    }

                    $information_result = [
                        'update'    => [
                            'stock'     => [
                                'success'   => [
                                    'jumlah'    => (double)$total_success_update_part,
                                    'data'      => $data_success_update_stock,
                                ],
                                'error'     => [
                                    'jumlah'    => (double)$total_error_update_part,
                                    'data'      => $data_error_update_stock
                                ],
                            ],
                            'status'        => [
                                'error'     => ($data_error_update_status == '') ? '' : $data_error_update_status,
                                'message'   => 'Berhasil mengubah '.$total_success_update_part.' item dan gagal mengubah '.$total_error_update_part.
                                                ' status product dari total '.$total_update_part.' product'
                            ]
                        ]
                    ];
                    return Response::responseSuccess('Data Berhasil Disimpan', $information_result);
                } else {
                    return Response::responseWarning('Gagal mengakses API Tiktok Marketplace '.$messageResponseTiktok);
                }
            } else {
                return Response::responseWarning('Lokasi awal atau tujuan bukan milik marketplace Tiktok');
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
