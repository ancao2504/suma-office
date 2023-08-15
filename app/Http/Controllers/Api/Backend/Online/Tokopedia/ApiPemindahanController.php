<?php

namespace App\Http\Controllers\Api\Backend\Online\Tokopedia;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use App\Helpers\Api\UpdateToken;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Helpers\Api\ServiceTokopedia;
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
                    ->whereRaw("(pdh.kd_lokasi1='".config('constants.api.tokopedia.kode_lokasi')."' or
                            pdh.kd_lokasi2='".config('constants.api.tokopedia.kode_lokasi')."')")
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
                            iif(pdh.kd_lokasi1 = '".config('constants.api.tokopedia.kode_lokasi')."',
                                isnull(pdh_dtl.sts_mp_awal, 0), isnull(pdh_dtl.sts_mp_tujuan, 0)) as status_mp_detail,
                            isnull(part.tokopedia_id, 0) as product_id,
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
                                    pdh.companyid=stlokasi.companyid and stlokasi.kd_lokasi='".config('constants.api.tokopedia.kode_lokasi')."'
                    where	isnull(pdh_dtl.pindah, 0) > 0
                    order by pdh_dtl.kd_part asc";

            $result = DB::select($sql, [ $request->get('nomor_dokumen'), $request->get('companyid') ]);

            $product_id = '';
            $data_part_tokopedia = new Collection();
            $data_detail_pemindahan = new Collection();

            foreach($result as $data) {
                if(trim($data->product_id) != '') {
                    if(trim($product_id) == '') {
                        $product_id = trim($data->product_id);
                    } else {
                        $product_id .= ','.trim($data->product_id);
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
                    'indicator'     => (trim($data->kode_lokasi_awal) == config('constants.api.tokopedia.kode_lokasi')) ? 'DECREMENT' : 'INCREMENT',
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

            $token_tokopedia = '';

            $sql = DB::table('user_api_office')->lock('with (nolock)')
                    ->selectRaw("isnull(user_api_office.tokopedia_token, '') as tokopedia_token,
                                isnull(user_api_office.user_id, '') as user_id")
                    ->where('user_api_office.office_token', $auth_token)
                    ->orderByRaw("isnull(user_api_office.id, 0) desc")
                    ->first();

            if(empty($sql->tokopedia_token) || trim($sql->tokopedia_token) == '') {
                return Response::responseWarning('Token tokopedia tidak ditemukan, lakukan logout kemudian login kembali');
            } else {
                $token_tokopedia = $sql->tokopedia_token;
            }

            // ==========================================================================
            // CEK KONEKSI API TOKOPEDIA
            // ==========================================================================
            $responseTokopedia = ServiceTokopedia::GetShopInfo(trim($token_tokopedia));
            $statusServer = (empty(json_decode($responseTokopedia)->message)) ? 1 : 0;

            if($statusServer == 0) {
                $authorization = $request->header('Authorization');
                $token = explode(" ", $authorization);
                $auth_token = trim($token[1]);

                $responseUpdateToken = UpdateToken::tokopedia($auth_token);

                if($responseUpdateToken->status == 1) {
                    $token_tokopedia = $responseUpdateToken->data->token;
                } else {
                    return Response::responseWarning($responseUpdateToken->message);
                }
            }

            // ==========================================================================
            // AMBIL DATA TOKOPEDIA
            // ==========================================================================
            $responseTokopedia = ServiceTokopedia::GetProductInfoByProductId(trim($token_tokopedia), trim($product_id));
            $statusResponseTokopedia = (empty(json_decode($responseTokopedia)->header->error_code)) ? 1 : 0;

            if($statusResponseTokopedia == 1) {
                $dataTokopedia = json_decode($responseTokopedia)->data;

                foreach($dataTokopedia as $detailTokped) {
                    $status = 'Not Connected';
                    if($detailTokped->basic->status == -2) {
                        $status = 'Banned';
                    } elseif($detailTokped->basic->status == -1) {
                        $status = 'Pending';
                    } elseif($detailTokped->basic->status == 0) {
                        $status = 'Deleted';
                    } elseif($detailTokped->basic->status == 1) {
                        $status = 'Archive';
                    } elseif($detailTokped->basic->status == 2) {
                        $status = 'Best (Feature Product)';
                    } elseif($detailTokped->basic->status == 3) {
                        $status = 'Inactive (Warehouse)';
                    }

                    $data_part_tokopedia->push((object) [
                        'productID'     => (empty($detailTokped->basic->productID)) ? '' : strtoupper(trim($detailTokped->basic->productID)),
                        'sku'           => (empty($detailTokped->other->sku)) ? '' : strtoupper(trim($detailTokped->other->sku)),
                        'stock'         => (empty($detailTokped->stock->value)) ? 0 : $detailTokped->stock->value,
                        'status'        => $status,
                    ]);
                }
            } else {
                $data_part_tokopedia->push((object) [
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
                    'marketplace'   => $data_part_tokopedia
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

            $nomor_dokumen = '';
            $kode_lokasi_awal = '';
            $kode_lokasi_tujuan = '';
            $status_mp_header = 0;
            $status_mp_detail = 0;
            $part_number = '';
            $product_id = '';
            $jumlah_pemindahan = 0;

            $sql = "select	isnull(pdh.no_dokumen, '') as nomor_dokumen,
                            isnull(pdh.kd_lokasi1, '') as kode_lokasi_awal,
                            isnull(pdh.kd_lokasi2, '') as kode_lokasi_tujuan,
                            isnull(pdh.sts_mp, '') as status_mp_header,
                            isnull(pdh_dtl.kd_part, '') as part_number,
                            isnull(part.tokopedia_id, 0) as product_id,
                            isnull(pdh_dtl.pindah, 0) as pindah,
                            iif(pdh.kd_lokasi1 = '".config('constants.api.tokopedia.kode_lokasi')."',
                                isnull(pdh_dtl.sts_mp_awal, 0), isnull(pdh_dtl.sts_mp_tujuan, 0)) as status_mp_detail
                    from
                    (
                        select	top 1 companyid, no_dokumen, kd_lokasi1,
                                kd_lokasi2, sts_mp
                        from	pdh with (nolock)
                        where	pdh.no_dokumen=? and pdh.companyid=? and
                                (pdh.kd_lokasi1 = '".config('constants.api.tokopedia.kode_lokasi')."' or
                                pdh.kd_lokasi2 = '".config('constants.api.tokopedia.kode_lokasi')."')
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

                $nomor_dokumen = strtoupper(trim($data->nomor_dokumen));
                $kode_lokasi_awal = strtoupper(trim($data->kode_lokasi_awal));
                $kode_lokasi_tujuan = strtoupper(trim($data->kode_lokasi_tujuan));
                $status_mp_header = (int)$data->status_mp_header;
                $part_number = strtoupper(trim($data->part_number));
                $product_id = strtoupper(trim($data->product_id));
                $jumlah_pemindahan = (double)$data->pindah;
                $status_mp_detail = (int)$data->status_mp_detail;
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

            if(strtoupper(trim($kode_lokasi_awal)) == config('constants.api.tokopedia.kode_lokasi') ||
                strtoupper(trim($kode_lokasi_tujuan)) == config('constants.api.tokopedia.kode_lokasi')) {
                // ==========================================================================
                // PROCEDURE UPDATE DATA TOKOPEDIA
                // ==========================================================================
                $authorization = $request->header('Authorization');
                $token = explode(" ", $authorization);
                $auth_token = trim($token[1]);

                $token_tokopedia = '';

                $sql = DB::table('user_api_office')->lock('with (nolock)')
                    ->selectRaw("isnull(user_api_office.tokopedia_token, '') as tokopedia_token,
                                isnull(user_api_office.user_id, '') as user_id")
                    ->where('user_api_office.office_token', $auth_token)
                    ->orderByRaw("isnull(user_api_office.id, 0) desc")
                    ->first();

                if(empty($sql->tokopedia_token) || trim($sql->tokopedia_token) == '') {
                    return Response::responseWarning('Token tokopedia tidak ditemukan, lakukan logout kemudian login kembali');
                } else {
                    $token_tokopedia = $sql->tokopedia_token;
                }

                // ==========================================================================
                // CEK KONEKSI API TOKOPEDIA
                // ==========================================================================
                $responseTokopedia = ServiceTokopedia::GetShopInfo(trim($token_tokopedia));
                $statusServer = (empty(json_decode($responseTokopedia)->message)) ? 1 : 0;

                if($statusServer == 0) {
                    $authorization = $request->header('Authorization');
                    $token = explode(" ", $authorization);
                    $auth_token = trim($token[1]);

                    $responseUpdateToken = UpdateToken::tokopedia($auth_token);

                    if($responseUpdateToken->status == 1) {
                        $token_tokopedia = $responseUpdateToken->data->token;
                    } else {
                        return Response::responseWarning($responseUpdateToken->message);
                    }
                }

                // ==========================================================================
                // UPDATE DATA STOCK TOKOPEDIA
                // ==========================================================================
                $data_parts[] = [
                    'product_id'    => (int)$product_id,
                    'stock_value'   => (double)$jumlah_pemindahan
                ];

                if(strtoupper($kode_lokasi_tujuan) == config('constants.api.tokopedia.kode_lokasi')) {
                    $responseUpdateStock = ServiceTokopedia::ProductUpdateStockIncrement(trim($token_tokopedia), $data_parts);
                } else {
                    $responseUpdateStock = ServiceTokopedia::ProductUpdateStockDecrement(trim($token_tokopedia), $data_parts);
                }
                $statusUpdateStock = (empty(json_decode($responseUpdateStock)->header->error_code)) ? 1 : 0;

                if($statusUpdateStock == 1) {
                    $resultUpdateStock = json_decode($responseUpdateStock)->data;

                    if((double)$resultUpdateStock->failed_rows > 0) {
                        foreach($resultUpdateStock->failed_rows_data as $data) {
                            return response()->json([ 'status' => 0, 'message' => 'Product Id '.strtoupper(trim($product_id)).', '.$data->message ]);
                        }
                    } else {
                        // ==========================================================================
                        // UPDATE DATA STATUS PRODUCT TOKOPEDIA
                        // ==========================================================================
                        $data_error_update_status = '';
                        $message_update_status = '';

                        $data_product_update_status = [
                            'product_id' => array_map('intval', explode(',', (int)$product_id))
                        ];

                        if(strtoupper($kode_lokasi_tujuan) == config('constants.api.tokopedia.kode_lokasi')) {
                            $responseUpdateStatus = ServiceTokopedia::ProductUpdateStatusActive(trim($token_tokopedia), $data_product_update_status);
                        } else {
                            $responseUpdateStatus = ServiceTokopedia::ProductUpdateStatusInActive(trim($token_tokopedia), $data_product_update_status);
                        }
                        $statusUpdateStatus = (empty(json_decode($responseUpdateStatus)->header->error_code)) ? 1 : 0;

                        if($statusUpdateStatus == 0) {
                            $data_error_update_status = 'Berhasil mengubah stock Tokopedia, tetapi gagal mengupdate status product. Lakukan update product secara manual. '.
                                json_decode($responseUpdateStatus)->header->error_code.' = '.json_decode($responseUpdateStatus)->header->reason;
                        } else {
                            $resultUpdateStatus = json_decode($responseUpdateStatus)->data;
                            $data_error_update_status = (empty($resultUpdateStatus->failed_rows_data)) ? '' : $resultUpdateStatus->failed_rows_data[0];
                            $message_update_status = 'Berhasil mengubah '.$resultUpdateStatus->succeed_rows.' status product dan gagal mengubah '.$resultUpdateStatus->failed_rows.
                                                    ' status product dari total '.$resultUpdateStatus->total_data.' product';
                        }
                    }
                } else {
                    return response()->json([ 'status' => 0, 'message' => 'Gagal mengupdate data part number '.strtoupper(trim($request->get('part_number'))).'. Coba lagi' ]);
                }
            }

            $data_update_status = [
                'update_status' => [
                    'error'     => $data_error_update_status,
                    'message'   => $message_update_status
                ]
            ];

            // ==========================================================================
            // UPDATE DATA STATUS PEMINDAHAN DATABASE INTERNAL
            // ==========================================================================
            DB::transaction(function () use ($request, $nomor_dokumen, $part_number) {
                DB::insert('exec sp_PdhLok_UpdateStsMP ?,?,?,?', [
                    trim(strtoupper($nomor_dokumen)), config('constants.api.tokopedia.kode_lokasi'),
                    trim(strtoupper($part_number)), trim(strtoupper($request->get('companyid')))
                ]);
            });

            return Response::responseSuccess('Data Berhasil Disimpan', $data_update_status);
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
                    trim(strtoupper($request->get('nomor_dokumen'))), config('constants.api.tokopedia.kode_lokasi'),
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

            $nomor_dokumen = '';
            $kode_lokasi_awal = '';
            $kode_lokasi_tujuan = '';
            $status_mp_header = 0;
            $data_product_update_stock = [];

            $sql = "select	isnull(pdh.no_dokumen, '') as nomor_dokumen,
                            isnull(pdh.kd_lokasi1, '') as kode_lokasi_awal,
                            isnull(pdh.kd_lokasi2, '') as kode_lokasi_tujuan,
                            isnull(pdh.sts_mp, '') as status_mp_header,
                            isnull(pdh_dtl.kd_part, '') as part_number,
                            isnull(part.tokopedia_id, 0) as product_id,
                            isnull(pdh_dtl.pindah, 0) as pindah,
                            iif(pdh.kd_lokasi1 = '".config('constants.api.tokopedia.kode_lokasi')."',
                                isnull(pdh_dtl.sts_mp_awal, 0), isnull(pdh_dtl.sts_mp_tujuan, 0)) as status_mp_detail
                    from
                    (
                        select	top 1 companyid, no_dokumen, kd_lokasi1,
                                kd_lokasi2, sts_mp
                        from	pdh with (nolock)
                        where	pdh.no_dokumen=? and pdh.companyid=? and
                                (pdh.kd_lokasi1 = '".config('constants.api.tokopedia.kode_lokasi')."' or
                                pdh.kd_lokasi2 = '".config('constants.api.tokopedia.kode_lokasi')."')
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

                $nomor_dokumen = strtoupper(trim($data->nomor_dokumen));
                $kode_lokasi_awal = strtoupper(trim($data->kode_lokasi_awal));
                $kode_lokasi_tujuan = strtoupper(trim($data->kode_lokasi_tujuan));
                $status_mp_header = (int)$data->status_mp_header;

                if(strtoupper(trim($data->kode_lokasi_awal)) == config('constants.api.tokopedia.kode_lokasi') ||
                    strtoupper(trim($data->kode_lokasi_tujuan)) == config('constants.api.tokopedia.kode_lokasi')) {
                    if(strtoupper(trim($data->product_id)) != '') {
                        if(strtoupper(trim($data->product_id)) != 0) {
                            if((int)$data->status_mp_detail == 0) {
                                $data_product_update_stock[] = [
                                    'product_id'    => (int)$data->product_id,
                                    'stock_value'   => (double)$data->pindah
                                ];
                            }
                        }
                    }
                }
            }

            if((double)$jumlah_data <= 0) {
                return Response::responseWarning("Data dokumen tidak terdaftar");
            }

            if((int)$status_mp_header == 1) {
                return Response::responseWarning("Nomor dokumen ini sudah pernah di update ke marketplace");
            }

            if(strtoupper(trim($kode_lokasi_awal)) == config('constants.api.tokopedia.kode_lokasi') ||
                strtoupper(trim($kode_lokasi_tujuan)) == config('constants.api.tokopedia.kode_lokasi')) {
                // ==========================================================================
                // PROCEDURE UPDATE DATA TOKOPEDIA
                // ==========================================================================
                $authorization = $request->header('Authorization');
                $token = explode(" ", $authorization);
                $auth_token = trim($token[1]);

                $token_tokopedia = '';

                $sql = DB::table('user_api_office')->lock('with (nolock)')
                    ->selectRaw("isnull(user_api_office.tokopedia_token, '') as tokopedia_token,
                                isnull(user_api_office.user_id, '') as user_id")
                    ->where('user_api_office.office_token', $auth_token)
                    ->orderByRaw("isnull(user_api_office.id, 0) desc")
                    ->first();

                if(empty($sql->tokopedia_token) || trim($sql->tokopedia_token) == '') {
                    return Response::responseWarning('Token tokopedia tidak ditemukan, lakukan logout kemudian login kembali');
                } else {
                    $token_tokopedia = $sql->tokopedia_token;
                }

                // ==========================================================================
                // CEK KONEKSI API TOKOPEDIA
                // ==========================================================================
                $responseShopInfo = ServiceTokopedia::GetShopInfo(trim($token_tokopedia));
                $statusServer = (empty(json_decode($responseShopInfo)->message)) ? 1 : 0;

                if($statusServer == 0) {
                    $authorization = $request->header('Authorization');
                    $token = explode(" ", $authorization);
                    $auth_token = trim($token[1]);

                    $responseUpdateToken = UpdateToken::tokopedia($auth_token);

                    if($responseUpdateToken->status == 1) {
                        $token_tokopedia = $responseUpdateToken->data->token;
                    } else {
                        return Response::responseWarning($responseUpdateToken->message);
                    }
                }

                // ==========================================================================
                // UPDATE DATA STOCK TOKOPEDIA
                // ==========================================================================
                $data_error_update_stock = [];
                $data_success_update_stock = [];
                $jumlah_data_error_update_stock = 0;
                $jumlah_data_success_update_stock = 0;
                $product_id_update_database = '';
                $product_id_update_status = '';
                $data_error_update_status = '';
                $data_product_update_status = [];
                $message_update_status = '';

                if(strtoupper($kode_lokasi_tujuan) == config('constants.api.tokopedia.kode_lokasi')) {
                    $responseUpdateStock = ServiceTokopedia::ProductUpdateStockIncrement(trim($token_tokopedia), $data_product_update_stock);
                } else {
                    $responseUpdateStock = ServiceTokopedia::ProductUpdateStockDecrement(trim($token_tokopedia), $data_product_update_stock);
                }
                $statusUpdateStock = (empty(json_decode($responseUpdateStock)->header->error_code)) ? 1 : 0;

                if($statusUpdateStock == 0) {
                    return response()->json([
                        'status'    => 0,
                        'message'   => 'Gagal mengakses API Increment/Decrement Tokopedia, Coba lagi atau hubungi IT Programmer. '.
                                        json_decode($responseUpdateStock)->header->error_code.' = '.json_decode($responseUpdateStock)->header->reason
                    ]);
                } else {
                    $resultUpdateStock = json_decode($responseUpdateStock)->data;

                    if((double)$resultUpdateStock->failed_rows > 0) {
                        foreach($resultUpdateStock->failed_rows_data as $data) {
                            $jumlah_data_error_update_stock = (double)$jumlah_data_error_update_stock + 1;

                            $data_error_update_stock[] = [
                                'product_id'    => $data->product_id,
                                'message'       => $data->message
                            ];
                        }
                    }

                    if((double)$resultUpdateStock->succeed_rows > 0) {
                        foreach($resultUpdateStock->succeed_rows_data as $data) {
                            $jumlah_data_success_update_stock = (double)$jumlah_data_success_update_stock + 1;

                            if(trim($product_id_update_status) == '') {
                                $product_id_update_status = (int)$data->productID;
                            } else {
                                $product_id_update_status .= ','.(int)$data->productID;
                            }

                            if(trim($product_id_update_database) == '') {
                                $product_id_update_database = "'".(int)$data->productID."'";
                            } else {
                                $product_id_update_database .= ','."'".(int)$data->productID."'";
                            }

                            $data_success_update_stock[] = [
                                'product_id'    => $data->productID,
                                'stock'         => (empty($data->stock)) ? 0 : $data->stock
                            ];
                        }
                    }

                    if(trim($product_id_update_status) != '') {
                        $data_product_update_status = [
                            'product_id' => array_map('intval', explode(',', $product_id_update_status))
                        ];

                        if(strtoupper($kode_lokasi_tujuan) == config('constants.api.tokopedia.kode_lokasi')) {
                            $responseUpdateStatus = ServiceTokopedia::ProductUpdateStatusActive(trim($token_tokopedia), $data_product_update_status);
                        } else {
                            $responseUpdateStatus = ServiceTokopedia::ProductUpdateStatusInActive(trim($token_tokopedia), $data_product_update_status);
                        }
                        $statusUpdateStatus = (empty(json_decode($responseUpdateStatus)->header->error_code)) ? 1 : 0;

                        if($statusUpdateStatus == 0) {
                            $data_error_update_status = 'Gagal mengupdate semua status product. '.
                                json_decode($responseUpdateStatus)->header->error_code.' = '.json_decode($responseUpdateStatus)->header->reason;
                        } else {
                            $resultUpdateStatus = json_decode($responseUpdateStatus)->data;
                            $data_error_update_status = (empty($resultUpdateStatus->failed_rows_data)) ? '' : $resultUpdateStatus->failed_rows_data[0];
                            $message_update_status = 'Berhasil mengubah '.$resultUpdateStatus->succeed_rows.' status product dan gagal mengubah '.$resultUpdateStatus->failed_rows.
                                                    ' status product dari total '.$resultUpdateStatus->total_data.' product';
                        }
                    }

                    if(trim($product_id_update_database) != '') {
                        DB::transaction(function () use ($request, $nomor_dokumen, $product_id_update_database) {
                            DB::insert('exec SP_PdhLok_UpdateStsMP_TokopediaAll ?,?,?,?', [
                                trim(strtoupper($nomor_dokumen)), config('constants.api.tokopedia.kode_lokasi'),
                                trim(strtoupper($request->get('companyid'))),
                                trim(strtoupper($product_id_update_database))
                            ]);
                        });
                    }

                    $information_result = [
                        'update'    => [
                            'stock'     => [
                                'success'   => [
                                    'jumlah'    => (double)$jumlah_data_success_update_stock,
                                    'data'      => $data_success_update_stock,
                                ],
                                'error'     => [
                                    'jumlah'    => (double)$jumlah_data_error_update_stock,
                                    'data'      => $data_error_update_stock
                                ],
                            ],
                            'status'        => [
                                'error'     => ($data_error_update_status == '') ? '' : $data_error_update_status,
                                'message'   => $message_update_status
                            ]
                        ]
                    ];
                }

            }

            return Response::responseSuccess('Data Berhasil Disimpan', $information_result);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
