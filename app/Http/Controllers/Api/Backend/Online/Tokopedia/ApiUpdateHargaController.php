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

class ApiUpdateHargaController extends Controller
{
    public function daftarUpdateHarga(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'year'      => 'required',
                'month'     => 'required',
                'companyid' => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih tahun dan bulan terlebih dahulu");
            }

            $sql = DB::table('harga_tokopedia')
                    ->selectRaw("isnull(no_dokumen, '') as nomor_dokumen")
                    ->whereYear('harga_tokopedia.tanggal', $request->get('year'))
                    ->whereMonth('harga_tokopedia.tanggal', $request->get('month'))
                    ->where('harga_tokopedia.companyid', $request->get('companyid'))
                    ->paginate((empty($request->get('per_page'))) ? 10 : $request->get('per_page'));

            $jumlah_data = 0;
            $result = collect($sql)->toArray();

            $current_page = $result['current_page'];
            $data = $result['data'];
            $first_page_url = $result['first_page_url'];
            $from = $result['from'];
            $last_page = $result['last_page'];
            $last_page_url = $result['last_page_url'];
            $links = $result['links'];
            $next_page_url = $result['next_page_url'];
            $path = $result['path'];
            $per_page = $result['per_page'];
            $prev_page_url = $result['prev_page_url'];
            $to = $result['to'];
            $total = $result['total'];

            $nomor_dokumen = '';

            foreach($sql as $data) {
                $jumlah_data = (double)$jumlah_data + 1;

                if(trim($nomor_dokumen) == '') {
                    $nomor_dokumen = "'".trim($data->nomor_dokumen)."'";
                } else {
                    $nomor_dokumen .= ",'".trim($data->nomor_dokumen)."'";
                }
            }

            $data_update_harga = [];

            if((double)$jumlah_data > 0) {
                $sql = "select	isnull(harga_tokopedia.no_dokumen, '') as nomor_dokumen,
                                isnull(convert(varchar(10), harga_tokopedia.tanggal, 105), '') as tanggal,
                                isnull(harga_tokopedia.status, 0) as status_header,
                                isnull(count(hargadtl_tokopedia.no_dokumen), 0) as total_item,
                                isnull(terupdate.terupdate, 0) as total_update,
                                isnull(sum(isnull(hargadtl_tokopedia.selisih_het, 0)), 0) as total_selisih
                        from
                        (
                            select	*
                            from	harga_tokopedia with (nolock)
                            where	harga_tokopedia.no_dokumen in (".$nomor_dokumen.") and
                                    harga_tokopedia.companyid=?
                        )	harga_tokopedia
                                left join hargadtl_tokopedia with (nolock) on harga_tokopedia.no_dokumen=hargadtl_tokopedia.no_dokumen and
                                        harga_tokopedia.companyid=hargadtl_tokopedia.companyid
                                left join
                                (
                                    select	hargadtl_tokopedia.companyid,
                                            hargadtl_tokopedia.no_dokumen,
                                            count(hargadtl_tokopedia.no_dokumen) as terupdate
                                    from	hargadtl_tokopedia with (nolock)
                                    where	hargadtl_tokopedia.no_dokumen in (".$nomor_dokumen.") and
                                            hargadtl_tokopedia.companyid=? and
                                            isnull(hargadtl_tokopedia.status, 0)=1
                                    group by hargadtl_tokopedia.companyid,
                                            hargadtl_tokopedia.no_dokumen
                                )	terupdate on harga_tokopedia.no_dokumen=terupdate.no_dokumen and
                                        harga_tokopedia.companyid=terupdate.companyid
                        group by harga_tokopedia.companyid, harga_tokopedia.no_dokumen, harga_tokopedia.tanggal,
                                harga_tokopedia.status, terupdate.terupdate
                        order by harga_tokopedia.no_dokumen asc";

                $result = DB::select($sql, [ $request->get('companyid'), $request->get('companyid') ]);

                foreach($result as $data) {
                    $data_update_harga[] = [
                        'nomor_dokumen'     => strtoupper(trim($data->nomor_dokumen)),
                        'tanggal'           => trim($data->tanggal),
                        'status'            => (int)$data->status_header,
                        'item'              => (double)$data->total_item,
                        'update'            => (double)$data->total_update,
                        'selisih'           => (double)$data->total_selisih
                    ];
                }
            }

            $data_update_harga = [
                'current_page'  => $current_page,
                'data'          => $data_update_harga,
                'first_page_url' => $first_page_url,
                'from'          => $from,
                'last_page'     => $last_page,
                'last_page_url' => $last_page_url,
                'links'         => $links,
                'next_page_url' => $next_page_url,
                'path'          => $path,
                'per_page'      => $per_page,
                'prev_page_url' => $prev_page_url,
                'to'            => $to,
                'total'         => $total
            ];

            return Response::responseSuccess('success', $data_update_harga);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function buatDokumen(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'kode'      => 'required',
                'tanggal'   => 'required',
                'companyid' => 'required',
                'user_id'   => 'required',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih kode update harga terlebih dahulu");
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_UpdateHarga_BuatDokumenTokopedia ?,?,?,?', [
                    trim(strtoupper($request->get('kode'))), $request->get('tanggal'),
                    trim(strtoupper($request->get('companyid'))), trim(strtoupper($request->get('user_id')))
                ]);
            });

            return Response::responseSuccess('Data Dokumen Berhasil Dibuat', null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function formUpdateHarga(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_dokumen' => 'required',
                'companyid'     => 'required'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih nomor dokumen terlebih dahulu");
            }

            $sql = "select	isnull(harga_tokopedia.no_dokumen, '') as nomor_dokumen,
                            isnull(harga_tokopedia.tanggal, '') as tanggal,
                            isnull(harga_tokopedia.status, 0) as status_header,
                            isnull(hargadtl_tokopedia.kd_part, '') as part_number,
                            isnull(part.tokopedia_id, 0) as product_id,
                            isnull(part.ket, '') as nama_part,
                            isnull(hargadtl_tokopedia.status, 0) as status_detail,
                            isnull(hargadtl_tokopedia.keterangan, '') as keterangan,
                            isnull(hargadtl_tokopedia.het_lama, 0) as het_lama,
                            isnull(hargadtl_tokopedia.het_baru, 0) as het_baru,
                            isnull(hargadtl_tokopedia.selisih_het, 0) as selisih_het
                    from
                    (
                        select	*
                        from	harga_tokopedia with (nolock)
                        where	harga_tokopedia.no_dokumen=? and
                                harga_tokopedia.companyid=?
                    )	harga_tokopedia
                            inner join hargadtl_tokopedia with (nolock) on harga_tokopedia.no_dokumen=hargadtl_tokopedia.no_dokumen and
                                        harga_tokopedia.companyid=hargadtl_tokopedia.companyid
                            left join part with (nolock) on hargadtl_tokopedia.kd_part=part.kd_part and
                                        harga_tokopedia.companyid=part.companyid
                            left join stlokasi with (nolock) on hargadtl_tokopedia.kd_part=stlokasi.kd_part and
                                        harga_tokopedia.companyid=stlokasi.companyid and
                                        stlokasi.kd_lokasi='".config('constants.tokopedia.kode_lokasi')."'
                    order by hargadtl_tokopedia.kd_part asc";

            $result = DB::select($sql, [ $request->get('nomor_dokumen'), $request->get('companyid') ]);

            $jumlah_data = 0;
            $nomor_dokumen = '';
            $tanggal = '';
            $status_header = 0;
            $data_detail = [];

            foreach($result as $data) {
                $jumlah_data = (double)$jumlah_data + 1;

                $nomor_dokumen = strtoupper(trim($data->nomor_dokumen));
                $tanggal = trim($data->tanggal);
                $status_header = (int)$data->status_header;

                $status_kenaikan = ((double)$data->het_baru > (double)$data->het_lama) ? 'NAIK' : 'TURUN';
                $selisih = ((double)$data->het_baru > (double)$data->het_lama) ? (double)$data->het_baru - (double)$data->het_lama : (double)$data->het_lama - (double)$data->het_baru;
                $prosentase = 0;
                if((double)$data->het_baru > (double)$data->het_lama) {
                    $prosentase = ((double)$data->het_lama <= 0) ? 0 : (((double)$data->het_baru - (double)$data->het_lama) / (double)$data->het_lama) * 100;
                } else {
                    $prosentase = ((double)$data->het_lama <= 0) ? 0 : (((double)$data->het_lama - (double)$data->het_baru) / (double)$data->het_lama) * 100;
                }

                $data_detail[] = [
                    'part_number'   => strtoupper(trim($data->part_number)),
                    'product_id'    => strtoupper(trim($data->product_id)),
                    'nama_part'     => strtoupper(trim($data->nama_part)),
                    'update'        => (int)$data->status_detail,
                    'keterangan'    => trim($data->keterangan),
                    'het_lama'      => (double)$data->het_lama,
                    'het_baru'      => (double)$data->het_baru,
                    'selisih'       => (double)$selisih,
                    'status'        => $status_kenaikan,
                    'prosentase'    => (double)$prosentase
                ];
            }

            if((double)$jumlah_data <= 0) {
                return Response::responseWarning("Nomor dokumen yang anda pilih tidak terdaftar");
            }

            $data_update_harga = [
                'nomor_dokumen'     => $nomor_dokumen,
                'tanggal'           => $tanggal,
                'status_header'     => $status_header,
                'detail'            => $data_detail
            ];

            return Response::responseSuccess('success', $data_update_harga);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function updateHargaPerPartNumber(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_dokumen' => 'required',
                'part_number'   => 'required',
                'companyid'     => 'required'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih nomor dokumen dan part number terlebih dahulu");
            }

            $sql = "select	isnull(hargadtl_tokopedia.no_dokumen, '') as nomor_dokumen,
                            isnull(hargadtl_tokopedia.kd_part, '') as part_number,
                            isnull(part.tokopedia_id, 0) as product_id,
                            isnull(hargadtl_tokopedia.status, 0) as status,
                            isnull(hargadtl_tokopedia.het_lama, 0) as het_lama,
                            isnull(hargadtl_tokopedia.het_baru, 0) as het_baru
                    from
                    (
                        select	top 1 *
                        from	hargadtl_tokopedia with (nolock)
                        where	hargadtl_tokopedia.no_dokumen=? and
                                hargadtl_tokopedia.kd_part=? and
                                hargadtl_tokopedia.companyid=?
                    )	hargadtl_tokopedia
                            left join part with (nolock) on hargadtl_tokopedia.kd_part=part.kd_part and
                                        hargadtl_tokopedia.companyid=part.companyid";

            $result = DB::select($sql, [ $request->get('nomor_dokumen'), $request->get('part_number'), $request->get('companyid') ]);

            $jumlah_data = 0;
            $product_id = '';
            $new_price = 0;
            $status = 0;

            foreach($result as $data) {
                $jumlah_data = (double)$jumlah_data + 1;

                if(trim($data->product_id) != '') {
                    if(trim($data->product_id) != 0) {
                        $status = (int)$data->status;
                        $product_id = (int)$data->product_id;
                        $new_price = (double)$data->het_baru;
                    }
                }
            }

            if((double)$jumlah_data <= 0) {
                return Response::responseWarning("Part number yang anda pilih tidak terdaftar di nomor dokumen ini");
            }

            if((double)$status == 1) {
                return Response::responseWarning("Part number yang anda pilih sudah pernah diupdate sebelumnya");
            }

            if(trim($product_id) == '') {
                return Response::responseWarning("Data Product ID marketplace masih kosong");
            }

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
            // PROSES UPDATE HARGA API TOKOPEDIA
            // ==========================================================================
            $data_update_harga[] = [
                'product_id'    => (int)$product_id,
                'new_price'     => (double)$new_price,
            ];
            $responseUpdateHarga = ServiceTokopedia::ProductUpdatePriceOnly(trim($token_tokopedia), $data_update_harga);
            $statusUpdateHarga = (empty(json_decode($responseUpdateHarga)->header->error_code)) ? 1 : 0;

            if($statusUpdateHarga == 0) {
                return response()->json([
                    'status'    => 0,
                    'message'   => 'Gagal mengakses API Update Price Only Tokopedia, Coba lagi atau hubungi IT Programmer. '.
                                        json_decode($responseUpdateHarga)->header->error_code.' = '.json_decode($responseUpdateHarga)->header->reason
                ]);
            } else {
                $resultUpdateHarga = json_decode($responseUpdateHarga)->data;

                if((double)$resultUpdateHarga->failed_rows > 0) {
                    foreach($resultUpdateHarga->failed_rows_data as $data) {
                        return response()->json([ 'status' => 0, 'message' => 'Product Id '.strtoupper(trim($product_id)).', '.$data->message ]);
                    }
                }
            }

            // ==========================================================================
            // UPDATE DATA STATUS PEMINDAHAN DATABASE INTERNAL
            // ==========================================================================
            DB::transaction(function () use ($request) {
                DB::insert('exec SP_UpdateHarga_TokopediaUpdateSts ?,?,?', [
                    trim(strtoupper($request->get('nomor_dokumen'))), trim(strtoupper($request->get('part_number'))),
                    trim(strtoupper($request->get('companyid')))
                ]);
            });

            return Response::responseSuccess('Data Berhasil Disimpan', null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function updateHargaStatusPerPartNumber(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_dokumen' => 'required|string',
                'part_number'   => 'required|string',
                'companyid'     => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih nomor dokumen dan part number terlebih dahulu");
            }

            $sql = DB::table('hargadtl_tokopedia')->lock('with (nolock)')
                    ->selectRaw("isnull(hargadtl_tokopedia.no_dokumen, '') as nomor_dokumen,
                                isnull(hargadtl_tokopedia.kd_part, '') as part_number")
                    ->where('hargadtl_tokopedia.no_dokumen', $request->get('nomor_dokumen'))
                    ->where('hargadtl_tokopedia.kd_part', $request->get('part_number'))
                    ->where('hargadtl_tokopedia.companyid', $request->get('companyid'))
                    ->first();

            if(empty($sql->nomor_dokumen)) {
                return Response::responseWarning("Part number yang anda pilih tidak terdaftar di nomor dokumen ini");
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_UpdateHarga_TokopediaUpdateSts ?,?,?', [
                    trim(strtoupper($request->get('nomor_dokumen'))), trim(strtoupper($request->get('part_number'))),
                    trim(strtoupper($request->get('companyid')))
                ]);
            });

            return Response::responseSuccess('Data Berhasil Disimpan', null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function updateHargaPerNomorDokumen(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_dokumen' => 'required',
                'companyid'     => 'required'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih nomor dokumen terlebih dahulu");
            }

            $sql = "select	isnull(hargadtl_tokopedia.no_dokumen, '') as nomor_dokumen,
                            isnull(hargadtl_tokopedia.kd_part, '') as part_number,
                            isnull(part.tokopedia_id, 0) as product_id,
                            isnull(hargadtl_tokopedia.status, 0) as status,
                            isnull(hargadtl_tokopedia.het_lama, 0) as het_lama,
                            isnull(hargadtl_tokopedia.het_baru, 0) as het_baru
                    from
                    (
                        select	*
                        from	hargadtl_tokopedia with (nolock)
                        where	hargadtl_tokopedia.no_dokumen=? and
                                hargadtl_tokopedia.companyid=? and
                                isnull(hargadtl_tokopedia.status, 0)=0
                    )	hargadtl_tokopedia
                            left join part with (nolock) on hargadtl_tokopedia.kd_part=part.kd_part and
                                        hargadtl_tokopedia.companyid=part.companyid ";

            $result = DB::select($sql, [ $request->get('nomor_dokumen'), $request->get('companyid') ]);

            $jumlah_data = 0;
            $data_update_harga = [];
            $data_collection_update_harga = new Collection();

            foreach($result as $data) {
                $jumlah_data = (double)$jumlah_data + 1;

                if(trim($data->product_id) != '') {
                    if(trim($data->product_id) != 0) {
                        $data_collection_update_harga->push((object) [
                            'product_id'    => (int)$data->product_id,
                            'new_price'     => (double)$data->het_baru,
                        ]);

                        $data_update_harga[] = [
                            'product_id'    => (int)$data->product_id,
                            'new_price'     => (double)$data->het_baru,
                        ];
                    }
                }
            }

            if((double)$jumlah_data <= 0) {
                return Response::responseWarning("Part number yang anda pilih tidak terdaftar di nomor dokumen ini");
            }

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
            // PROSES UPDATE HARGA API TOKOPEDIA
            // ==========================================================================
            $data_error_update_harga = new Collection();
            $jumlah_data_error_update_harga = 0;
            $jumlah_data_success_update_harga = 0;
            $product_id_update_database = '';
            $query_data_error_update_harga = '';

            $responseUpdateHarga = ServiceTokopedia::ProductUpdatePriceOnly(trim($token_tokopedia), $data_update_harga);
            $statusUpdateHarga = (empty(json_decode($responseUpdateHarga)->header->error_code)) ? 1 : 0;

            if($statusUpdateHarga == 0) {
                return response()->json([
                    'status'    => 0,
                    'message'   => 'Gagal mengakses API Update Price Only Tokopedia, Coba lagi atau hubungi IT Programmer. '.
                                        json_decode($responseUpdateHarga)->header->error_code.' = '.json_decode($responseUpdateHarga)->header->reason
                ]);
            } else {
                $resultUpdateHarga = json_decode($responseUpdateHarga)->data;

                $jumlah_data_success_update_harga = (double)$resultUpdateHarga->succeed_rows;

                if((double)$resultUpdateHarga->failed_rows > 0) {
                    foreach($resultUpdateHarga->failed_rows_data as $data) {
                        $jumlah_data_error_update_harga = (double)$jumlah_data_error_update_harga + 1;

                        $data_error_update_harga->push((object) [
                            'product_id'    => $data->product_id,
                            'message'       => $data->message
                        ]);

                        if(trim($query_data_error_update_harga) == '') {
                            $query_data_error_update_harga = "select '".$data->product_id."' as product_id, '".$data->message."' as keterangan";
                        } else {
                            $query_data_error_update_harga .= "union all select '".$data->product_id."' as product_id, '".$data->message."' as keterangan";
                        }
                    }
                }

                foreach($data_collection_update_harga as $data_update) {
                    $status_success = 1;

                    $data_update_product_id = strtoupper(trim($data_update->product_id));

                    foreach($data_error_update_harga as $data_error) {
                        if(strtoupper(trim($data_update_product_id)) == strtoupper(trim($data_error->product_id))) {
                            $status_success = 0;
                        }
                    }

                    if($status_success == 1) {
                        if(trim($product_id_update_database) == '') {
                            $product_id_update_database = "'".(int)$data_update_product_id."'";
                        } else {
                            $product_id_update_database .= ','."'".(int)$data_update_product_id."'";
                        }
                    }
                }
            }
            // ==========================================================================
            // UPDATE DATA STATUS PEMINDAHAN DATABASE INTERNAL
            // ==========================================================================
            DB::transaction(function () use ($request, $product_id_update_database, $jumlah_data_error_update_harga, $query_data_error_update_harga) {
                DB::insert('exec SP_UpdateHarga_TokopediaUpdateSts_All ?,?,?,?', [
                    trim(strtoupper($request->get('nomor_dokumen'))), trim(strtoupper(config('constants.tokopedia.kode_lokasi'))),
                    trim(strtoupper($request->get('companyid'))), trim(strtoupper($product_id_update_database))
                ]);


                if((double)$jumlah_data_error_update_harga > 0) {
                    $sql = "update	hargadtl_tokopedia
                            set		keterangan=isnull(tokopedia.keterangan, '')
                            from
                            (
                                select	isnull(stlokasi.kd_part, '') as kd_part,
                                        isnull(tokopedia.product_id, '') as product_id,
                                        isnull(tokopedia.keterangan, '') as keterangan
                                from
                                (
                                    ".$query_data_error_update_harga."
                                )	tokopedia
                                        inner join part with (nolock) on tokopedia.product_id=part.tokopedia_id and
                                                    part.companyid='".trim(strtoupper($request->get('companyid')))."'
                            )	tokopedia
                            where	hargadtl_tokopedia.no_dokumen='".trim(strtoupper($request->get('nomor_dokumen')))."' and
                                    hargadtl_tokopedia.companyid='".trim(strtoupper($request->get('companyid')))."' and
                                    hargadtl_tokopedia.kd_part=tokopedia.kd_part";

                    DB::statement($sql);
                }
            });

            $information_result = [
                'update'    => [
                    'harga'         => [
                        'success'   => [
                            'jumlah'    => (double)$jumlah_data_success_update_harga
                        ],
                        'error'     => [
                            'jumlah'    => (double)$jumlah_data_error_update_harga,
                            'data'      => $data_error_update_harga
                        ],
                    ]
                ]
            ];

            return Response::responseSuccess('Data Berhasil Disimpan', $information_result);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
