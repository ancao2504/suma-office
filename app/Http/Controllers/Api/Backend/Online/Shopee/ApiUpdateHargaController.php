<?php

namespace app\Http\Controllers\Api\Backend\Online\shopee;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use App\Helpers\Api\UpdateToken;
use App\Helpers\Api\ServiceShopee;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
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

            $sql = DB::table('harga_shopee')
                    ->selectRaw("isnull(no_dokumen, '') as nomor_dokumen")
                    ->whereYear('harga_shopee.tanggal', $request->get('year'))
                    ->whereMonth('harga_shopee.tanggal', $request->get('month'))
                    ->where('harga_shopee.companyid', $request->get('companyid'))
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
                $sql = "select	isnull(harga_shopee.no_dokumen, '') as nomor_dokumen,
                                isnull(convert(varchar(10), harga_shopee.tanggal, 105), '') as tanggal,
                                isnull(harga_shopee.status, 0) as status_header,
                                isnull(count(hargadtl_shopee.no_dokumen), 0) as total_item,
                                isnull(terupdate.terupdate, 0) as total_update,
                                isnull(sum(isnull(hargadtl_shopee.selisih_het, 0)), 0) as total_selisih
                        from
                        (
                            select	*
                            from	harga_shopee with (nolock)
                            where	harga_shopee.no_dokumen in (".$nomor_dokumen.") and
                                    harga_shopee.companyid=?
                        )	harga_shopee
                                left join hargadtl_shopee with (nolock) on harga_shopee.no_dokumen=hargadtl_shopee.no_dokumen and
                                        harga_shopee.companyid=hargadtl_shopee.companyid
                                left join
                                (
                                    select	hargadtl_shopee.companyid,
                                            hargadtl_shopee.no_dokumen,
                                            count(hargadtl_shopee.no_dokumen) as terupdate
                                    from	hargadtl_shopee with (nolock)
                                    where	hargadtl_shopee.no_dokumen in (".$nomor_dokumen.") and
                                            hargadtl_shopee.companyid=? and
                                            isnull(hargadtl_shopee.status, 0)=1
                                    group by hargadtl_shopee.companyid,
                                            hargadtl_shopee.no_dokumen
                                )	terupdate on harga_shopee.no_dokumen=terupdate.no_dokumen and
                                        harga_shopee.companyid=terupdate.companyid
                        group by harga_shopee.companyid, harga_shopee.no_dokumen, harga_shopee.tanggal,
                                harga_shopee.status, terupdate.terupdate
                        order by harga_shopee.no_dokumen asc";

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
                DB::insert('exec SP_UpdateHarga_BuatDokumenShopee ?,?,?,?', [
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

            $sql = "select	isnull(harga_shopee.no_dokumen, '') as nomor_dokumen,
                            isnull(harga_shopee.tanggal, '') as tanggal,
                            isnull(harga_shopee.status, 0) as status_header,
                            isnull(hargadtl_shopee.kd_part, '') as part_number,
                            part.shopee_id as product_id,
                            isnull(part.ket, '') as nama_part,
                            isnull(hargadtl_shopee.status, 0) as status_detail,
                            isnull(hargadtl_shopee.keterangan, '') as keterangan,
                            isnull(hargadtl_shopee.het_lama, 0) as het_lama,
                            isnull(hargadtl_shopee.het_baru, 0) as het_baru,
                            isnull(hargadtl_shopee.selisih_het, 0) as selisih_het
                    from
                    (
                        select	*
                        from	harga_shopee with (nolock)
                        where	harga_shopee.no_dokumen=? and
                                harga_shopee.companyid=?
                    )	harga_shopee
                            inner join hargadtl_shopee with (nolock) on harga_shopee.no_dokumen=hargadtl_shopee.no_dokumen and
                                        harga_shopee.companyid=hargadtl_shopee.companyid
                            left join part with (nolock) on hargadtl_shopee.kd_part=part.kd_part and
                                        harga_shopee.companyid=part.companyid
                    order by hargadtl_shopee.kd_part asc";

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
                    $prosentase = (((double)$data->het_baru - (double)$data->het_lama) / (double)$data->het_lama) * 100;
                } else {
                    $prosentase = (((double)$data->het_lama - (double)$data->het_baru) / (double)$data->het_lama) * 100;
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

            $sql = DB::table('hargadtl_shopee')->lock('with (nolock)')
                    ->selectRaw("isnull(hargadtl_shopee.no_dokumen, '') as nomor_dokumen,
                                isnull(hargadtl_shopee.kd_part, '') as part_number")
                    ->where('hargadtl_shopee.no_dokumen', $request->get('nomor_dokumen'))
                    ->where('hargadtl_shopee.kd_part', $request->get('part_number'))
                    ->where('hargadtl_shopee.companyid', $request->get('companyid'))
                    ->first();

            if(empty($sql->nomor_dokumen)) {
                return Response::responseWarning("Part number yang anda pilih tidak terdaftar di nomor dokumen ini");
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_UpdateHarga_ShopeeUpdateSts ?,?,?', [
                    trim(strtoupper($request->get('nomor_dokumen'))), trim(strtoupper($request->get('part_number'))),
                    trim(strtoupper($request->get('companyid')))
                ]);

                DB::table('hargadtl_shopee')
                    ->where('no_dokumen', trim(strtoupper($request->get('nomor_dokumen'))))
                    ->where('companyid', trim(strtoupper($request->get('companyid'))))
                    ->where('kd_part', trim(strtoupper($request->get('part_number'))))
                    ->update([
                        'keterangan' => 'Diupdate Manual Internal'
                ]);
            });

            return Response::responseSuccess('Data Berhasil Disimpan', null);
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

            $sql = "select	isnull(hargadtl_shopee.no_dokumen, '') as nomor_dokumen,
                            isnull(hargadtl_shopee.kd_part, '') as part_number,
                            part.shopee_id as product_id,
                            isnull(hargadtl_shopee.status, 0) as status,
                            isnull(hargadtl_shopee.het_lama, 0) as het_lama,
                            isnull(hargadtl_shopee.het_baru, 0) as het_baru
                    from
                    (
                        select	top 1 *
                        from	hargadtl_shopee with (nolock)
                        where	hargadtl_shopee.no_dokumen=? and
                                hargadtl_shopee.kd_part=? and
                                hargadtl_shopee.companyid=?
                    )	hargadtl_shopee
                            left join part with (nolock) on hargadtl_shopee.kd_part=part.kd_part and
                                                            part.companyid=hargadtl_shopee.companyid
                            left join part with (nolock) on hargadtl_shopee.kd_part=part.kd_part and
                                        part.kd_lokasi='".config('constants.api.shopee.kode_lokasi')."' and
                                        stlokasi.companyid=hargadtl_shopee.companyid";

            $result = DB::select($sql, [ $request->get('nomor_dokumen'), $request->get('part_number'), $request->get('companyid') ]);

            $jumlah_data = 0;
            $product_id = '';
            $new_price = 0;
            $status = 0;

            foreach($result as $data) {
                $jumlah_data = (double)$jumlah_data + 1;

                if(trim($data->product_id) != '') {
                    $status = (int)$data->status;
                    $product_id = (int)$data->product_id;
                    $new_price = (double)$data->het_baru;
                }
            }

            if((double)$jumlah_data <= 0) {
                return Response::responseWarning("Part number yang anda pilih tidak terdaftar di nomor dokumen ini");
            }

            if((double)$status == 1) {
                return Response::responseWarning("Part number yang anda pilih sudah pernah diupdate sebelumnya");
            }

            if(empty($product_id)) {
                return Response::responseWarning("Product Id pada Part Number : <strong>".$result[0]->part_number."</strong> masih kosong");
            }

            // ==========================================================================
            // PROCEDURE UPDATE DATA SHOPEE
            // ==========================================================================
            $authorization = $request->header('Authorization');
            $token = explode(" ", $authorization);
            $auth_token = trim($token[1]);

            $token_shopee = '';

            $sql = DB::table('user_api_office')->lock('with (nolock)')
                    ->where('office_token', $auth_token)
                    ->first();

            if(empty($sql->user_id)) {
                return Response::responseWarning('Token Suma Office tidak ditemukan, lakukan logout kemudian login kembali');
            } else {
                if(empty($sql->shopee_token)){
                    return Response::responseWarning('Token shopee tidak ditemukan, lakukan logout kemudian login kembali');
                }
                $token_shopee = $sql->shopee_token;
            }

            // ==========================================================================
            // CEK KONEKSI API SHOPEE
            // ==========================================================================
            $responseShopInfo = ServiceShopee::getShopInfo(trim($token_shopee));
            $statusServer = (empty(json_decode($responseShopInfo)->error)) ? 1 : 0;

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
            // ==========================================================================
            // PROSES UPDATE HARGA API SHOPEE
            // ==========================================================================
            $data_update_harga = (object)[
                'product_id'    => (int)$product_id,
                'new_price'     => (int)$new_price,
            ];

            // Menentukan variabel collection
            $error_list = [];
            $success_list = [];

            $responseUpdateHarga = ServiceShopee::ProductUpdatePrice($token_shopee, $data_update_harga);
            if(!empty($product_id) || $product_id != '') {
                $statusUpdateHarga = empty(json_decode($responseUpdateHarga)->error)? 1 : 0;
                $statusUpdatemessage = json_decode($responseUpdateHarga)->message;

                if($statusUpdateHarga == 0) {
                    $error_list[] = (object)[
                        'kode_part'     => trim(strtoupper($request->get('part_number'))),
                        'product_id'    => $product_id,
                        'keterangan'    => $statusUpdatemessage
                    ];
                } else {
                    $success_list[] = (object)[
                        'kode_part'     => trim(strtoupper($request->get('part_number'))),
                        'product_id'    => $product_id,
                        'keterangan'    => 'Berhasil diubah menjadi : '.json_decode($responseUpdateHarga)->response->success_list[0]->original_price
                    ];
                }
            } else {
                $error_list[] = (object)[
                    'kode_part'     => trim(strtoupper($request->get('part_number'))),
                    'product_id'    => '',
                    'keterangan'    => 'Produk id belum terdaftar di internal'
                ];
            }

                // ==========================================================================
                // UPDATE DATA STATUS PEMINDAHAN DATABASE INTERNAL
                // ==========================================================================
                DB::transaction(function () use ($request, $error_list) {

                    if(count($error_list) == 0) {
                        DB::insert('exec SP_UpdateHarga_ShopeeUpdateSts ?,?,?', [
                            trim(strtoupper($request->get('nomor_dokumen'))), trim(strtoupper($request->get('part_number'))),
                            trim(strtoupper($request->get('companyid')))
                        ]);
                    } else {
                        foreach($error_list as $data) {
                            DB::table('hargadtl_shopee')
                                ->where('no_dokumen', trim(strtoupper($request->get('nomor_dokumen'))))
                                ->where('companyid', trim(strtoupper($request->get('companyid'))))
                                ->where('kd_part', trim(strtoupper($data->kode_part)))
                                ->update([
                                    'keterangan' => $data->keterangan
                                ]);
                        }
                    }
                });

            return Response::responseSuccess('', [ 'nomer_dokumen' => trim(strtoupper($request->get('nomor_dokumen'))), 'error_list' => $error_list, 'success_list' => $success_list]);
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

            $sql = "select	isnull(hargadtl_shopee.no_dokumen, '') as nomor_dokumen,
                            isnull(hargadtl_shopee.kd_part, '') as part_number,
                            part.shopee_id as product_id,
                            isnull(hargadtl_shopee.status, 0) as status,
                            isnull(hargadtl_shopee.het_lama, 0) as het_lama,
                            isnull(hargadtl_shopee.het_baru, 0) as het_baru
                    from
                    (
                        select	*
                        from	hargadtl_shopee with (nolock)
                        where	hargadtl_shopee.no_dokumen=? and
                                hargadtl_shopee.companyid=? and
                                isnull(hargadtl_shopee.status, 0)=0
                    )	hargadtl_shopee
                            left join part with (nolock) on hargadtl_shopee.kd_part=part.kd_part and
                                                            part.companyid=hargadtl_shopee.companyid
                            left join stlokasi with (nolock) on hargadtl_shopee.kd_part=stlokasi.kd_part and
                                        stlokasi.kd_lokasi='".config('constants.api.shopee.kode_lokasi')."' and
                                        stlokasi.companyid=hargadtl_shopee.companyid";

            $result = DB::select($sql, [ $request->get('nomor_dokumen'), $request->get('companyid') ]);

            if(collect($result)->count() <= 0) {
                return Response::responseWarning("Part number yang anda pilih tidak terdaftar di nomor dokumen ini");
            }

            // ==========================================================================
            // PROCEDURE UPDATE DATA SHOPEE
            // ==========================================================================
            $authorization = $request->header('Authorization');
            $token = explode(" ", $authorization);
            $auth_token = trim($token[1]);

            $token_shopee = '';

            $sql = DB::table('user_api_office')->lock('with (nolock)')
                    ->where('office_token', $auth_token)
                    ->first();

            if(empty($sql->user_id)) {
                return Response::responseWarning('Token Suma Office tidak ditemukan, lakukan logout kemudian login kembali');
            } else {
                if(empty($sql->shopee_token)){
                    return Response::responseWarning('Token shopee tidak ditemukan, lakukan logout kemudian login kembali');
                }
                $token_shopee = $sql->shopee_token;
            }

            // ==========================================================================
            // CEK KONEKSI API SHOPEE
            // ==========================================================================
            $responseShopInfo = ServiceShopee::GetShopInfo(trim($token_shopee));
            $statusServer = (empty(json_decode($responseShopInfo)->message)) ? 1 : 0;

            if($statusServer == 0) {
                $responseUpdateToken =  UpdateToken::shopee($auth_token);
                if($responseUpdateToken->status == 0) {
                    return Response::responseWarning($responseUpdateToken->message);
                }

                $token_shopee = $responseUpdateToken->data->token;
            }

            // Menentukan variabel collection
            $error_list = [];
            $success_list = [];
            // ==========================================================================
            // PROSES UPDATE HARGA API SHOPEE
            // ==========================================================================
            foreach ($result as $key => $data) {
                if(!empty($data->product_id) || $data->product_id !== '') {
                    $responseUpdateHarga = ServiceShopee::ProductUpdatePrice(trim($token_shopee), (object)[
                        'kode_part'     => trim($data->part_number),
                        'product_id'    => (int)$data->product_id,
                        'new_price'     => (int)$data->het_baru,
                    ]);

                    $statusUpdateHarga = (empty(json_decode($responseUpdateHarga)->error)) ? 1 : 0;
                    $statusUpdatemessage = json_decode($responseUpdateHarga)->message;

                    if($statusUpdateHarga == 0) {
                        $error_list[] = (object)[
                            'kode_part'     => trim($data->part_number),
                            'product_id'    => (int)$data->product_id,
                            'keterangan'    => $statusUpdatemessage
                        ];
                    } else {
                        $success_list[] = (object)[
                            'kode_part'     => trim($data->part_number),
                            'product_id'    => (int)$data->product_id,
                            'keterangan'    => 'Berhasil diubah menjadi : ' . json_decode($responseUpdateHarga)->response->success_list[0]->original_price
                        ];
                    }
                } else {
                    $error_list[] = (object)[
                        'kode_part'     => trim($data->part_number),
                        'product_id'    => '',
                        'keterangan'    => 'Produk id belum terdaftar di internal'
                    ];
                }
            }
            // ==========================================================================
            // UPDATE DATA STATUS PEMINDAHAN DATABASE INTERNAL
            // ==========================================================================
            DB::transaction(function () use ($request, $success_list, $error_list) {
                if(count($success_list) > 0) {
                    DB::insert('exec SP_UpdateHarga_ShopeeUpdateSts_All ?,?,?,?', [
                        trim(strtoupper($request->get('nomor_dokumen'))), trim(strtoupper(config('constants.api.shopee.kode_lokasi'))),
                        trim(strtoupper($request->get('companyid'))), collect($success_list)->pluck('product_id')->implode(',')
                    ]);
                }

                if(count($error_list) > 0) {
                    foreach($error_list as $data) {
                        DB::table('hargadtl_shopee')
                            ->where('no_dokumen', trim(strtoupper($request->get('nomor_dokumen'))))
                            ->where('companyid', trim(strtoupper($request->get('companyid'))))
                            ->where('kd_part', trim(strtoupper($data->kode_part)))
                            ->update([
                                'keterangan' => $data->keterangan
                            ]);
                    }
                }
                if(count($success_list) > 0) {
                    foreach($success_list as $data) {
                        DB::table('hargadtl_shopee')
                            ->where('no_dokumen', trim(strtoupper($request->get('nomor_dokumen'))))
                            ->where('companyid', trim(strtoupper($request->get('companyid'))))
                            ->where('kd_part', trim(strtoupper($data->kode_part)))
                            ->update([
                                'keterangan' => $data->keterangan
                            ]);
                    }
                }
            });

            return Response::responseSuccess('', [ 'nomer_dokumen' => trim(strtoupper($request->get('nomor_dokumen'))), 'error_list' => $error_list, 'success_list' => $success_list]);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
