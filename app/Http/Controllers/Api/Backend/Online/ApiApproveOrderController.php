<?php

namespace App\Http\Controllers\Api\Backend\Online;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use App\Helpers\Api\UpdateToken;
use App\Helpers\Api\ServiceShopee;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Helpers\Api\ServiceTokopedia;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class ApiApproveOrderController extends Controller
{
    public function daftarApproveOrder(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'companyid'     => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Anda Belum Login");
            }

            $sql = DB::table('faktur')->lock('with (nolock)')
                    ->selectRaw("isnull(faktur.no_faktur, '') as nomor_faktur,
                                isnull(faktur.tgl_faktur, '') as tanggal_faktur,
                                isnull(faktur.kd_sales, '') as kode_sales,
                                isnull(faktur.kd_dealer, '') as kode_dealer,
                                isnull(faktur.kd_ekspedisi, '') as kode_ekspedisi,
                                isnull(faktur.ket, '') as nomor_invoice,
                                isnull(faktur.total, 0) as total")
                    ->where('faktur.companyid', $request->get('companyid'))
                    ->whereRaw("isnull(faktur.approve_ol, 0)=0")
                    ->orderByRaw("faktur.tgl_faktur asc, faktur.no_faktur asc")
                    ->paginate($request->get('per_page') ?? 10);

            return Response::responseSuccess('success', $sql);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function formApproveTokopedia(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_invoice'     => 'required',
                'companyid'         => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih nomor invoice terlebih dahulu");
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
            $data_faktur = new Collection();
            $responseTokopedia = ServiceTokopedia::OrderGetSingleOrder(trim($token_tokopedia), trim($request->get('nomor_invoice')));
            $statusResponseTokopedia = (empty(json_decode($responseTokopedia)->header->error_code)) ? 1 : 0;

            if($statusResponseTokopedia == 1) {
                $dataTokopedia = json_decode($responseTokopedia)->data;

                $data_product_tokopedia = new Collection();
                $data_product_id = '';

                foreach($dataTokopedia->order_info->order_detail as $data) {
                    if(strtoupper(trim($data_product_id)) == '') {
                        $data_product_id = "'".$data->product_id."'";
                    } else {
                        $data_product_id .= ','."'".$data->product_id."'";
                    }

                    $data_product_tokopedia->push((object) [
                        'product_id'    => $data->product_id,
                        'sku'           => $data->sku,
                        'product_name'  => $data->product_name,
                        'quantity'      => $data->quantity,
                        'product_price' => $data->product_price,
                        'subtotal_price' => $data->subtotal_price,
                        'pictures'      => $data->product_picture
                    ]);
                }

                $data_tokopedia = new Collection();
                $data_tokopedia->push((object) [
                    'order_id'          => $dataTokopedia->order_id,
                    'nomor_invoice'     => $dataTokopedia->invoice_number,
                    'item_price'        => (double)$dataTokopedia->item_price,
                    'shipping_price'    => (double)$dataTokopedia->order_info->shipping_info->shipping_price,
                    'logistic'          => [
                        'name'          => $dataTokopedia->order_info->shipping_info->logistic_name,
                        'service'       => $dataTokopedia->order_info->shipping_info->logistic_service,
                    ],
                    'address'           => [
                        'district'      => $dataTokopedia->order_info->destination->address_district,
                        'city'          => $dataTokopedia->order_info->destination->address_city,
                        'province'      => $dataTokopedia->order_info->destination->address_province,
                        'postal'        => $dataTokopedia->order_info->destination->address_postal,
                    ],
                    'payment'           => [
                        'ref_number'    => $dataTokopedia->payment_info->payment_ref_num,
                        'date'          => $dataTokopedia->payment_info->payment_date,
                        'status'        => $dataTokopedia->payment_info->payment_status,
                    ],
                    'shipment_fulfillment'  => [
                        'payment_date_time' => $dataTokopedia->shipment_fulfillment->payment_date_time,
                        'accept_deadline'   => $dataTokopedia->shipment_fulfillment->accept_deadline,
                        'confirm_shipping_deadline' => $dataTokopedia->shipment_fulfillment->confirm_shipping_deadline,
                    ],
                    'status'            => [
                        'is_accepted'           => $dataTokopedia->shipment_fulfillment->is_accepted,
                        'is_confirm_shipping'   => $dataTokopedia->shipment_fulfillment->is_confirm_shipping,
                        'is_item_delivered'     => $dataTokopedia->shipment_fulfillment->is_item_delivered,
                    ],
                    'detail'            => $data_product_tokopedia
                ]);

                $status_faktur = 0;

                $sql = "select	isnull(faktur.no_faktur, '') as nomor_faktur, isnull(faktur.tgl_faktur, '') as tanggal_faktur,
                                isnull(faktur.kd_beli, '') as kode_beli, isnull(jns_beli.nama, '') as nama_beli,
                                isnull(faktur.no_pof, '') as nomor_pof, isnull(faktur.kd_sales, '') as kode_sales,
                                isnull(salesman.nm_sales, '') as nama_sales, isnull(faktur.kd_dealer, '') as kode_dealer,
                                isnull(dealer.nm_dealer, '') as nama_dealer, isnull(faktur.kd_ekspedisi, '') as kode_ekspedisi,
                                isnull(ekspedisi_online.nm_ekspedisi, '') as nama_ekspedisi, isnull(faktur.kd_tpc, '') as kode_tpc,
                                isnull(faktur.umur_faktur, 0) as umur_faktur, isnull(faktur.tgl_akhir_faktur, '') as tanggal_akhir_faktur,
                                isnull(faktur.rh, '') as rh, isnull(faktur.bo, '') as bo, isnull(faktur.ket, '') as keterangan,
                                isnull(fakt_dtl.kd_part, '') as part_number, isnull(part.ket, '') as nama_part,
                                isnull(fakt_dtl.kd_lokasi, '') as kode_lokasi, isnull(lokasi.ket, '') as nama_lokasi,
                                isnull(fakt_dtl.jml_order, 0) as jml_order, isnull(fakt_dtl.jml_jual, 0) as jml_jual,
                                isnull(fakt_dtl.harga, 0) as harga_detail, isnull(fakt_dtl.disc1, 0) as disc_detail,
                                isnull(fakt_dtl.jumlah, 0) as total_detail, isnull(faktur.disc2, 0) as disc_header,
                                isnull(faktur.discrp, 0) as disc_rp, isnull(faktur.discrp1, 0) as disc_rp1,
                                isnull(faktur.total, 0) as total_faktur, isnull(sj_dtl.no_sj, '') as nomor_sj,
                                isnull(serah_online_dtl.no_dok, '') as nomor_serah_terima
                        from
                        (
                            select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur, faktur.kd_beli,
                                    faktur.no_pof, faktur.kd_sales, faktur.kd_dealer, faktur.kd_ekspedisi,
                                    faktur.kd_tpc, faktur.umur_faktur, faktur.tgl_akhir_faktur,
                                    faktur.bo, faktur.rh, faktur.ket, faktur.disc2,
                                    faktur.discrp, faktur.discrp1, faktur.total
                            from	faktur with (nolock)
                            where	faktur.ket=? and faktur.companyid=?
                        )	faktur
                                left join jns_beli with (nolock) on faktur.kd_beli=jns_beli.kd_beli and
                                            faktur.companyid=jns_beli.companyid
                                left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and
                                            faktur.companyid=salesman.companyid
                                left join dealer with (nolock) on faktur.kd_dealer=dealer.kd_dealer and
                                            faktur.companyid=dealer.companyid
                                left join ekspedisi_online with (nolock) on faktur.kd_ekspedisi=ekspedisi_online.kd_ekspedisi
                                left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                            faktur.companyid=fakt_dtl.companyid
                                left join lokasi with (nolock) on fakt_dtl.kd_lokasi=lokasi.kd_lokasi and
                                            faktur.companyid=lokasi.companyid
                                left join part with (nolock) on fakt_dtl.kd_part=part.kd_part and
                                            faktur.companyid=part.companyid
                                left join sj_dtl with (nolock) on faktur.no_faktur=sj_dtl.no_faktur and
                                            faktur.companyid=sj_dtl.companyid
                                left join serah_online_dtl with (nolock) on sj_dtl.no_sj=serah_online_dtl.no_sj and
                                            faktur.companyid=serah_online_dtl.companyid
                        order by faktur.companyid asc, faktur.no_faktur asc";

                $result = DB::select($sql, [ $request->get('nomor_invoice'), $request->get('companyid') ]);

                $jumlah_faktur = 0;
                $data_faktur = new Collection();
                $data_faktur_temp = new Collection();
                $data_faktur_detail_temp = new Collection();

                foreach($result as $data) {
                    $jumlah_faktur = (double)$jumlah_faktur + 1;

                    $data_faktur_detail_temp->push((object) [
                        'pictures'      => trim(config('constants.app.url.images')).'/'.strtoupper(trim($data->part_number)).'.jpg',
                        'nomor_faktur'  => strtoupper(trim($data->nomor_faktur)),
                        'part_number'   => strtoupper(trim($data->part_number)),
                        'nama_part'     => strtoupper(trim($data->nama_part)),
                        'jml_order'     => (double)$data->jml_order,
                        'jml_jual'      => (double)$data->jml_jual,
                        'stock'         => 0,
                        'harga'         => (double)$data->harga_detail,
                        'disc_detail'   => (double)$data->disc_detail,
                        'total_detail'  => (double)$data->total_detail,
                        'keterangan'    => '',
                    ]);

                    $data_faktur_temp->push((object) [
                        'nomor_faktur'      => trim($data->nomor_faktur),
                        'tanggal'           => trim($data->tanggal_faktur),
                        'nomor_pof'         => trim($data->nomor_pof),
                        'kode_lokasi'       => trim($data->kode_lokasi),
                        'nama_lokasi'       => trim($data->nama_lokasi),
                        'kode_beli'         => trim($data->kode_beli),
                        'nama_beli'         => trim($data->nama_beli),
                        'kode_sales'        => trim($data->kode_sales),
                        'nama_sales'        => trim($data->nama_sales),
                        'kode_dealer'       => trim($data->kode_dealer),
                        'nama_dealer'       => trim($data->nama_dealer),
                        'kode_ekspedisi'    => trim($data->kode_ekspedisi),
                        'nama_ekspedisi'    => trim($data->nama_ekspedisi),
                        'kode_tpc'          => trim($data->kode_tpc),
                        'umur_faktur'       => (double)$data->umur_faktur,
                        'tanggal_akhir_faktur' => trim($data->tanggal_akhir_faktur),
                        'rh'                => strtoupper(trim($data->rh)),
                        'bo'                => strtoupper(trim($data->bo)),
                        'keterangan'        => trim($data->keterangan),
                        'disc_header'       => (double)$data->disc_header,
                        'disc_rp'           => (double)$data->disc_rp,
                        'disc_rp1'          => (double)$data->disc_rp1,
                        'total'             => (double)$data->total_faktur,
                        'nomor_sj'          => strtoupper(trim($data->nomor_sj)),
                        'nomor_serah_terima'=> strtoupper(trim($data->nomor_serah_terima)),
                    ]);
                }

                if((double)$jumlah_faktur > 0) {
                    $status_faktur = 1;
                    $nomor_faktur = '';
                    $sub_total = 0;

                    foreach($data_faktur_temp as $data) {
                        if(strtoupper(trim($nomor_faktur)) != strtoupper(trim($data->nomor_faktur))) {
                            $sub_total = $data_faktur_detail_temp
                                            ->where('nomor_faktur', strtoupper(trim($data->nomor_faktur)))
                                            ->sum('total_detail');

                            $data_faktur->push((object) [
                                'nomor_faktur'      => strtoupper(trim($data->nomor_faktur)),
                                'tanggal'           => trim($data->tanggal),
                                'nomor_pof'         => strtoupper(trim($data->nomor_pof)),
                                'lokasi'            => ((object)[
                                    'kode'          => strtoupper(trim($data->kode_lokasi)),
                                    'nama'          => strtoupper(trim($data->nama_lokasi))
                                ]),
                                'jenis_beli'        => ((object)[
                                    'kode'          => strtoupper(trim($data->kode_beli)),
                                    'keterangan'    => strtoupper(trim($data->nama_beli))
                                ]),
                                'salesman'          => ((object)[
                                    'kode'          => strtoupper(trim($data->kode_sales)),
                                    'nama'          => strtoupper(trim($data->nama_sales))
                                ]),
                                'dealer'            => ((object)[
                                    'kode'          => strtoupper(trim($data->kode_dealer)),
                                    'nama'          => strtoupper(trim($data->nama_dealer))
                                ]),
                                'ekspedisi'         => ((object)[
                                    'kode'          => strtoupper(trim($data->kode_ekspedisi)),
                                    'nama'          => strtoupper(trim($data->nama_ekspedisi))
                                ]),
                                'kode_tpc'          => trim($data->kode_tpc),
                                'jatuh_tempo'       => ((object)[
                                    'umur_faktur'   => (double)$data->umur_faktur,
                                    'tanggal'       => trim($data->tanggal_akhir_faktur)
                                ]),
                                'status'            => ((object)[
                                    'rh'            => strtoupper(trim($data->rh)),
                                    'bo'            => strtoupper(trim($data->bo)),
                                ]),
                                'keterangan'        => trim($data->keterangan),
                                'nomor_sj'          => strtoupper(trim($data->nomor_sj)),
                                'nomor_serah_terima'=> strtoupper(trim($data->nomor_serah_terima)),
                                'total'             => ((object)[
                                    'sub_total'     => (double)$sub_total,
                                    'disc_header'   => (double)$data->disc_header,
                                    'disc_header_rp' => round(((double)$sub_total * (double)$data->disc_header) / 100),
                                    'disc_rp'       => (double)$data->disc_rp,
                                    'disc_rp1'      => (double)$data->disc_rp1,
                                    'total'         => (double)$data->total,
                                ]),
                                'detail'            => $data_faktur_detail_temp
                                                        ->where('nomor_faktur', strtoupper(trim($data->nomor_faktur)))
                                                        ->values()
                                                        ->all()
                            ]);

                            $nomor_faktur = strtoupper(trim($data->nomor_faktur));
                        }

                        $jumlah_faktur = 0;
                        $total_faktur_amount = 0;

                        foreach($data_faktur as $data) {
                            $jumlah_faktur = (double)$jumlah_faktur + 1;
                            $total_faktur_amount = (double)$total_faktur_amount + (double)$data->total->total;
                        }
                    }
                }

                $jumlah_faktur = 0;
                $total_faktur_amount = 0;

                foreach($data_faktur as $data) {
                    $jumlah_faktur = (double)$jumlah_faktur + 1;
                    $total_faktur_amount = (double)$total_faktur_amount + (double)$data->total->total;
                }

                $data = [
                    'tokopedia'         => $data_tokopedia->first(),
                    'faktur'            => [
                        'status'        => (int)$status_faktur,
                        'jumlah_faktur' => (int)$jumlah_faktur,
                        'total_amount'  => (double)$total_faktur_amount,
                        'list'          => $data_faktur
                    ],
                ];

                return Response::responseSuccess('success', $data);
            } else {
                return Response::responseWarning(json_decode($responseTokopedia)->header->reason.' Error code : '.json_decode($responseTokopedia)->header->error_code);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function formApproveShopee(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_invoice'     => 'required',
                'companyid'         => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih nomor invoice terlebih dahulu");
            }

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

            if(empty($sql->shopee_token) || trim($sql->shopee_token) == '') {
                return Response::responseWarning('Token shopee tidak ditemukan, lakukan logout kemudian login kembali');
            } else {
                $token_shopee = $sql->shopee_token;
            }

            // ==========================================================================
            // CEK KONEKSI API Shopee
            // ==========================================================================
            $responseshopee = ServiceShopee::GetShopInfo(trim($token_shopee));
            $statusServer = (empty(json_decode($responseshopee)->error)) ? 1 : 0;

            if($statusServer == 0) {
                $authorization = $request->header('Authorization');
                $token = explode(" ", $authorization);
                $auth_token = trim($token[1]);

                $responseUpdateToken = UpdateToken::shopee($auth_token);

                if($responseUpdateToken->status == 1) {
                    $token_shopee = $responseUpdateToken->data->token;
                } else {
                    return Response::responseWarning($responseUpdateToken->message);
                }
            }

            // ==========================================================================
            // AMBIL DATA SHOPEE
            // ==========================================================================
            $data_faktur = new Collection();
            $responseShopee = ServiceShopee::GetOrderDetail(trim($token_shopee), trim($request->get('nomor_invoice')));
            $statusResponseShopee = (empty(json_decode($responseshopee)->error)) ? 1 : 0;

            if($statusResponseShopee == 0) {
                return Response::responseWarning(json_decode($responseShopee)->error);
            }

            $dataShopee = json_decode($responseShopee)->response;

            $data_shopee = new Collection();
            $data_product_shopee = new Collection();
            $data_product_id_marketplace = '';
            $item_price = 0;

            $nomor_invoice = '';
            $kode_ekspedisi = '';
            $nama_ekspedisi = '';

            foreach($dataShopee->order_list as $data) {
                $nomor_invoice = strtoupper(trim($data->order_sn));
                $nama_ekspedisi = strtoupper(trim($data->shipping_carrier));

                $responseChannelList = ServiceShopee::GetChannelList(trim($token_shopee));
                $statusResponseChannelList = (empty(json_decode($responseChannelList)->error)) ? 1 : 0;

                if($statusResponseChannelList == 1) {
                    $dataChannelList = json_decode($responseChannelList)->response;

                    foreach($dataChannelList->logistics_channel_list as $data_channel) {
                        if(strtoupper(trim($nama_ekspedisi)) == strtoupper(trim($data_channel->logistics_channel_name))) {
                            $kode_ekspedisi = strtoupper(trim($data_channel->logistics_channel_id));
                        }
                    }
                } else {
                    return Response::responseWarning(json_decode($responseShopee)->error);
                }

                foreach($data->item_list as $item) {
                    if(strtoupper(trim($data_product_id_marketplace)) == '') {
                        $data_product_id_marketplace = "'".$item->item_id."'";
                    } else {
                        $data_product_id_marketplace .= ','."'".$item->item_id."'";
                    }

                    $item_price = (double)$item_price + ((double)$item->model_discounted_price * (double)$item->model_quantity_purchased);

                    $data_product_shopee->push((object) [
                        'item_id'                   => $item->item_id,
                        'item_sku'                  => $item->item_sku,
                        'item_name'                 => $item->item_name,
                        'model_quantity_purchased'  => $item->model_quantity_purchased,
                        'model_discounted_price'    => $item->model_discounted_price,
                        'subtotal_price'            => (double)$item->model_discounted_price * (double)$item->model_quantity_purchased,
                        'pictures'                  => $item->image_info->image_url
                    ]);
                }

                $data_shopee->push((object) [
                    'order_id'          => $data->order_sn,
                    'nomor_invoice'     => $data->order_sn,
                    'item_price'        => (double)$item_price,
                    'shipping_price'    => (double)$data->actual_shipping_fee,
                    'logistics'         => (object)[
                        'id'            => $kode_ekspedisi,
                        'name'          => $data->shipping_carrier
                    ],
                    'address'           => (object)[
                        'full_address'  => $data->recipient_address->full_address,
                        'district'      => $data->recipient_address->district,
                        'city'          => $data->recipient_address->city,
                        'province'      => $data->recipient_address->state,
                        'postal'        => $data->recipient_address->zipcode,
                    ],
                    'payment'           => $data->payment_method,
                    'status'            => $data->order_status,
                    'detail'            => $data_product_shopee
                ]);
            }

            if(trim($nomor_invoice) == '') {
                return Response::responseWarning('Nomor invoice tidak terdaftar');
            }

            $status_faktur = 0;

            $sql = "select	isnull(faktur.no_faktur, '') as nomor_faktur, isnull(faktur.tgl_faktur, '') as tanggal_faktur,
                            isnull(faktur.kd_beli, '') as kode_beli, isnull(jns_beli.nama, '') as nama_beli,
                            isnull(faktur.no_pof, '') as nomor_pof, isnull(faktur.kd_sales, '') as kode_sales,
                            isnull(salesman.nm_sales, '') as nama_sales, isnull(faktur.kd_dealer, '') as kode_dealer,
                            isnull(dealer.nm_dealer, '') as nama_dealer, isnull(faktur.kd_ekspedisi, '') as kode_ekspedisi,
                            isnull(ekspedisi_online.nm_ekspedisi, '') as nama_ekspedisi, isnull(faktur.kd_tpc, '') as kode_tpc,
                            isnull(faktur.umur_faktur, 0) as umur_faktur, isnull(faktur.tgl_akhir_faktur, '') as tanggal_akhir_faktur,
                            isnull(faktur.rh, '') as rh, isnull(faktur.bo, '') as bo, isnull(faktur.ket, '') as keterangan,
                            isnull(fakt_dtl.kd_part, '') as part_number, isnull(part.ket, '') as nama_part,
                            isnull(fakt_dtl.kd_lokasi, '') as kode_lokasi, isnull(lokasi.ket, '') as nama_lokasi,
                            isnull(fakt_dtl.jml_order, 0) as jml_order, isnull(fakt_dtl.jml_jual, 0) as jml_jual,
                            isnull(fakt_dtl.harga, 0) as harga_detail, isnull(fakt_dtl.disc1, 0) as disc_detail,
                            isnull(fakt_dtl.jumlah, 0) as total_detail, isnull(faktur.disc2, 0) as disc_header,
                            isnull(faktur.discrp, 0) as disc_rp, isnull(faktur.discrp1, 0) as disc_rp1,
                            isnull(faktur.total, 0) as total_faktur, isnull(sj_dtl.no_sj, '') as nomor_sj,
                            isnull(serah_online_dtl.no_dok, '') as nomor_serah_terima
                    from
                    (
                        select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur, faktur.kd_beli,
                                faktur.no_pof, faktur.kd_sales, faktur.kd_dealer, faktur.kd_ekspedisi,
                                faktur.kd_tpc, faktur.umur_faktur, faktur.tgl_akhir_faktur,
                                faktur.bo, faktur.rh, faktur.ket, faktur.disc2,
                                faktur.discrp, faktur.discrp1, faktur.total
                        from	faktur with (nolock)
                        where	faktur.ket=? and faktur.companyid=?
                    )	faktur
                            left join jns_beli with (nolock) on faktur.kd_beli=jns_beli.kd_beli and
                                        faktur.companyid=jns_beli.companyid
                            left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and
                                        faktur.companyid=salesman.companyid
                            left join dealer with (nolock) on faktur.kd_dealer=dealer.kd_dealer and
                                        faktur.companyid=dealer.companyid
                            left join ekspedisi_online with (nolock) on faktur.kd_ekspedisi=ekspedisi_online.kd_ekspedisi
                            left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                        faktur.companyid=fakt_dtl.companyid
                            left join lokasi with (nolock) on fakt_dtl.kd_lokasi=lokasi.kd_lokasi and
                                        faktur.companyid=lokasi.companyid
                            left join part with (nolock) on fakt_dtl.kd_part=part.kd_part and
                                        faktur.companyid=part.companyid
                            left join sj_dtl with (nolock) on faktur.no_faktur=sj_dtl.no_faktur and
                                        faktur.companyid=sj_dtl.companyid
                            left join serah_online_dtl with (nolock) on sj_dtl.no_sj=serah_online_dtl.no_sj and
                                        faktur.companyid=serah_online_dtl.companyid
                    order by faktur.companyid asc, faktur.no_faktur asc";

            $result = DB::select($sql, [ strtoupper($nomor_invoice), $request->get('companyid') ]);

            $jumlah_faktur = 0;
            $data_faktur = new Collection();
            $data_faktur_temp = new Collection();
            $data_faktur_detail_temp = new Collection();

            foreach($result as $data) {
                $jumlah_faktur = (double)$jumlah_faktur + 1;

                $data_faktur_detail_temp->push((object) [
                    'pictures'      => trim(config('constants.app.url.images')).'/'.strtoupper(trim($data->part_number)).'.jpg',
                    'nomor_faktur'  => strtoupper(trim($data->nomor_faktur)),
                    'part_number'   => strtoupper(trim($data->part_number)),
                    'nama_part'     => strtoupper(trim($data->nama_part)),
                    'jml_order'     => (double)$data->jml_order,
                    'jml_jual'      => (double)$data->jml_jual,
                    'stock'         => 0,
                    'harga'         => (double)$data->harga_detail,
                    'disc_detail'   => (double)$data->disc_detail,
                    'total_detail'  => (double)$data->total_detail,
                    'keterangan'    => '',
                ]);

                $data_faktur_temp->push((object) [
                    'nomor_faktur'      => trim($data->nomor_faktur),
                    'tanggal'           => trim($data->tanggal_faktur),
                    'nomor_pof'         => trim($data->nomor_pof),
                    'kode_lokasi'       => trim($data->kode_lokasi),
                    'nama_lokasi'       => trim($data->nama_lokasi),
                    'kode_beli'         => trim($data->kode_beli),
                    'nama_beli'         => trim($data->nama_beli),
                    'kode_sales'        => trim($data->kode_sales),
                    'nama_sales'        => trim($data->nama_sales),
                    'kode_dealer'       => trim($data->kode_dealer),
                    'nama_dealer'       => trim($data->nama_dealer),
                    'kode_ekspedisi'    => trim($data->kode_ekspedisi),
                    'nama_ekspedisi'    => trim($data->nama_ekspedisi),
                    'kode_tpc'          => trim($data->kode_tpc),
                    'umur_faktur'       => (double)$data->umur_faktur,
                    'tanggal_akhir_faktur' => trim($data->tanggal_akhir_faktur),
                    'rh'                => strtoupper(trim($data->rh)),
                    'bo'                => strtoupper(trim($data->bo)),
                    'keterangan'        => trim($data->keterangan),
                    'disc_header'       => (double)$data->disc_header,
                    'disc_rp'           => (double)$data->disc_rp,
                    'disc_rp1'          => (double)$data->disc_rp1,
                    'total'             => (double)$data->total_faktur,
                    'nomor_sj'          => strtoupper(trim($data->nomor_sj)),
                    'nomor_serah_terima'=> strtoupper(trim($data->nomor_serah_terima))
                ]);
            }

            $nomor_faktur = '';
            $sub_total = 0;

            foreach($data_faktur_temp as $data) {
                if(strtoupper(trim($nomor_faktur)) != strtoupper(trim($data->nomor_faktur))) {
                    $sub_total = $data_faktur_detail_temp
                                    ->where('nomor_faktur', strtoupper(trim($data->nomor_faktur)))
                                    ->sum('total_detail');

                    $data_faktur->push((object) [
                        'nomor_faktur'      => strtoupper(trim($data->nomor_faktur)),
                        'tanggal'           => trim($data->tanggal),
                        'nomor_pof'         => strtoupper(trim($data->nomor_pof)),
                        'lokasi'            => ((object)[
                            'kode'          => strtoupper(trim($data->kode_lokasi)),
                            'nama'          => strtoupper(trim($data->nama_lokasi))
                        ]),
                        'jenis_beli'        => ((object)[
                            'kode'          => strtoupper(trim($data->kode_beli)),
                            'keterangan'    => strtoupper(trim($data->nama_beli))
                        ]),
                        'salesman'          => ((object)[
                            'kode'          => strtoupper(trim($data->kode_sales)),
                            'nama'          => strtoupper(trim($data->nama_sales))
                        ]),
                        'dealer'            => ((object)[
                            'kode'          => strtoupper(trim($data->kode_dealer)),
                            'nama'          => strtoupper(trim($data->nama_dealer))
                        ]),
                        'ekspedisi'         => ((object)[
                            'kode'          => strtoupper(trim($data->kode_ekspedisi)),
                            'nama'          => strtoupper(trim($data->nama_ekspedisi))
                        ]),
                        'kode_tpc'          => trim($data->kode_tpc),
                        'jatuh_tempo'       => ((object)[
                            'umur_faktur'   => (double)$data->umur_faktur,
                            'tanggal'       => trim($data->tanggal_akhir_faktur)
                        ]),
                        'status'            => ((object)[
                            'rh'            => strtoupper(trim($data->rh)),
                            'bo'            => strtoupper(trim($data->bo)),
                        ]),
                        'keterangan'        => trim($data->keterangan),
                        'nomor_sj'          => trim($data->nomor_sj),
                        'nomor_serah_terima'=> trim($data->nomor_serah_terima),
                        'total'             => ((object)[
                            'sub_total'     => (double)$sub_total,
                            'disc_header'   => (double)$data->disc_header,
                            'disc_header_rp' => round(((double)$sub_total * (double)$data->disc_header) / 100),
                            'disc_rp'       => (double)$data->disc_rp,
                            'disc_rp1'      => (double)$data->disc_rp1,
                            'total'         => (double)$data->total,
                        ]),
                        'detail'            => $data_faktur_detail_temp
                                                ->where('nomor_faktur', strtoupper(trim($data->nomor_faktur)))
                                                ->values()
                                                ->all()
                    ]);

                    $nomor_faktur = strtoupper(trim($data->nomor_faktur));
                }

                $jumlah_faktur = 0;
                $total_faktur_amount = 0;

                foreach($data_faktur as $data) {
                    $jumlah_faktur = (double)$jumlah_faktur + 1;
                    $total_faktur_amount = (double)$total_faktur_amount + (double)$data->total->total;
                }
            }

            $jumlah_faktur = 0;
            $total_faktur_amount = 0;

            foreach($data_faktur as $data) {
                $jumlah_faktur = (double)$jumlah_faktur + 1;
                $total_faktur_amount = (double)$total_faktur_amount + (double)$data->total->total;
            }

            $data = [
                'shopee'            => $data_shopee->first(),
                'faktur'            => [
                    'status'        => (int)$status_faktur,
                    'jumlah_faktur' => (int)$jumlah_faktur,
                    'total_amount'  => (double)$total_faktur_amount,
                    'list'          => $data_faktur
                ],
            ];

            return Response::responseSuccess('success', $data);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function formApproveInternal(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_faktur'  => 'required',
                'companyid'     => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih nomor faktur terlebih dahulu");
            }

            $sql = "select	isnull(faktur.no_faktur, '') as nomor_faktur, isnull(faktur.tgl_faktur, '') as tanggal_faktur,
                            isnull(faktur.kd_beli, '') as kode_beli, isnull(jns_beli.nama, '') as nama_beli,
                            isnull(faktur.no_pof, '') as nomor_pof, isnull(faktur.kd_sales, '') as kode_sales,
                            isnull(salesman.nm_sales, '') as nama_sales, isnull(faktur.kd_dealer, '') as kode_dealer,
                            isnull(dealer.nm_dealer, '') as nama_dealer, isnull(faktur.kd_ekspedisi, '') as kode_ekspedisi,
                            isnull(ekspedisi_online.nm_ekspedisi, '') as nama_ekspedisi, isnull(faktur.kd_tpc, '') as kode_tpc,
                            isnull(faktur.umur_faktur, 0) as umur_faktur, isnull(faktur.tgl_akhir_faktur, '') as tanggal_akhir_faktur,
                            isnull(faktur.rh, '') as rh, isnull(faktur.bo, '') as bo, isnull(faktur.ket, '') as keterangan,
                            isnull(fakt_dtl.kd_part, '') as part_number, isnull(part.ket, '') as nama_part,
                            isnull(fakt_dtl.kd_lokasi, '') as kode_lokasi, isnull(lokasi.ket, '') as nama_lokasi,
                            isnull(fakt_dtl.jml_order, 0) as jml_order, isnull(fakt_dtl.jml_jual, 0) as jml_jual,
                            isnull(fakt_dtl.harga, 0) as harga_detail, isnull(fakt_dtl.disc1, 0) as disc_detail,
                            isnull(fakt_dtl.jumlah, 0) as total_detail, isnull(faktur.disc2, 0) as disc_header,
                            isnull(faktur.discrp, 0) as disc_rp, isnull(faktur.discrp1, 0) as disc_rp1,
                            isnull(faktur.total, 0) as total_faktur, isnull(sj_dtl.no_sj, '') as nomor_sj,
                            isnull(serah_online_dtl.no_dok, '') as nomor_serah_terima
                    from
                    (
                        select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur, faktur.kd_beli,
                                faktur.no_pof, faktur.kd_sales, faktur.kd_dealer, faktur.kd_ekspedisi,
                                faktur.kd_tpc, faktur.umur_faktur, faktur.tgl_akhir_faktur,
                                faktur.bo, faktur.rh, faktur.ket, faktur.disc2,
                                faktur.discrp, faktur.discrp1, faktur.total
                        from	faktur with (nolock)
                        where	faktur.no_faktur=? and faktur.companyid=?
                    )	faktur
                            left join jns_beli with (nolock) on faktur.kd_beli=jns_beli.kd_beli and
                                        faktur.companyid=jns_beli.companyid
                            left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and
                                        faktur.companyid=salesman.companyid
                            left join dealer with (nolock) on faktur.kd_dealer=dealer.kd_dealer and
                                        faktur.companyid=dealer.companyid
                            left join ekspedisi_online with (nolock) on faktur.kd_ekspedisi=ekspedisi_online.kd_ekspedisi
                            left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                        faktur.companyid=fakt_dtl.companyid
                            left join lokasi with (nolock) on fakt_dtl.kd_lokasi=lokasi.kd_lokasi and
                                        faktur.companyid=lokasi.companyid
                            left join part with (nolock) on fakt_dtl.kd_part=part.kd_part and
                                        faktur.companyid=part.companyid
                            left join sj_dtl with (nolock) on faktur.no_faktur=sj_dtl.no_faktur and
                                        faktur.companyid=sj_dtl.companyid
                            left join serah_online_dtl with (nolock) on sj_dtl.no_sj=serah_online_dtl.no_sj and
                                        faktur.companyid=serah_online_dtl.companyid
                    order by faktur.companyid asc, faktur.no_faktur asc";

            $result = DB::select($sql, [ $request->get('nomor_faktur'), $request->get('companyid') ]);

            $jumlah_faktur = 0;
            $data_faktur = new Collection();
            $data_faktur_temp = new Collection();
            $data_faktur_detail_temp = new Collection();

            foreach($result as $data) {
                $jumlah_faktur = (double)$jumlah_faktur + 1;

                $data_faktur_detail_temp->push((object) [
                    'pictures'      => trim(config('constants.app.url.images')).'/'.strtoupper(trim($data->part_number)).'.jpg',
                    'nomor_faktur'  => strtoupper(trim($data->nomor_faktur)),
                    'part_number'   => strtoupper(trim($data->part_number)),
                    'nama_part'     => strtoupper(trim($data->nama_part)),
                    'jml_order'     => (double)$data->jml_order,
                    'jml_jual'      => (double)$data->jml_jual,
                    'stock'         => 0,
                    'harga'         => (double)$data->harga_detail,
                    'disc_detail'   => (double)$data->disc_detail,
                    'total_detail'  => (double)$data->total_detail,
                    'keterangan'    => '',
                ]);

                $data_faktur_temp->push((object) [
                    'nomor_faktur'      => trim($data->nomor_faktur),
                    'tanggal'           => trim($data->tanggal_faktur),
                    'nomor_pof'         => trim($data->nomor_pof),
                    'kode_lokasi'       => trim($data->kode_lokasi),
                    'nama_lokasi'       => trim($data->nama_lokasi),
                    'kode_beli'         => trim($data->kode_beli),
                    'nama_beli'         => trim($data->nama_beli),
                    'kode_sales'        => trim($data->kode_sales),
                    'nama_sales'        => trim($data->nama_sales),
                    'kode_dealer'       => trim($data->kode_dealer),
                    'nama_dealer'       => trim($data->nama_dealer),
                    'kode_ekspedisi'    => trim($data->kode_ekspedisi),
                    'nama_ekspedisi'    => trim($data->nama_ekspedisi),
                    'kode_tpc'          => trim($data->kode_tpc),
                    'umur_faktur'       => (double)$data->umur_faktur,
                    'tanggal_akhir_faktur' => trim($data->tanggal_akhir_faktur),
                    'rh'                => strtoupper(trim($data->rh)),
                    'bo'                => strtoupper(trim($data->bo)),
                    'keterangan'        => trim($data->keterangan),
                    'disc_header'       => (double)$data->disc_header,
                    'disc_rp'           => (double)$data->disc_rp,
                    'disc_rp1'          => (double)$data->disc_rp1,
                    'total'             => (double)$data->total_faktur,
                    'nomor_sj'          => strtoupper(trim($data->nomor_sj)),
                    'nomor_serah_terima'=> strtoupper(trim($data->nomor_serah_terima))
                ]);
            }

            $nomor_faktur = '';
            $sub_total = 0;

            foreach($data_faktur_temp as $data) {
                if(strtoupper(trim($nomor_faktur)) != strtoupper(trim($data->nomor_faktur))) {
                    $sub_total = $data_faktur_detail_temp
                                    ->where('nomor_faktur', strtoupper(trim($data->nomor_faktur)))
                                    ->sum('total_detail');

                    $data_faktur->push((object) [
                        'nomor_faktur'      => strtoupper(trim($data->nomor_faktur)),
                        'tanggal'           => trim($data->tanggal),
                        'nomor_pof'         => strtoupper(trim($data->nomor_pof)),
                        'lokasi'            => ((object)[
                            'kode'          => strtoupper(trim($data->kode_lokasi)),
                            'nama'          => strtoupper(trim($data->nama_lokasi))
                        ]),
                        'jenis_beli'        => ((object)[
                            'kode'          => strtoupper(trim($data->kode_beli)),
                            'keterangan'    => strtoupper(trim($data->nama_beli))
                        ]),
                        'salesman'          => ((object)[
                            'kode'          => strtoupper(trim($data->kode_sales)),
                            'nama'          => strtoupper(trim($data->nama_sales))
                        ]),
                        'dealer'            => ((object)[
                            'kode'          => strtoupper(trim($data->kode_dealer)),
                            'nama'          => strtoupper(trim($data->nama_dealer))
                        ]),
                        'ekspedisi'         => ((object)[
                            'kode'          => strtoupper(trim($data->kode_ekspedisi)),
                            'nama'          => strtoupper(trim($data->nama_ekspedisi))
                        ]),
                        'kode_tpc'          => trim($data->kode_tpc),
                        'jatuh_tempo'       => ((object)[
                            'umur_faktur'   => (double)$data->umur_faktur,
                            'tanggal'       => trim($data->tanggal_akhir_faktur)
                        ]),
                        'status'            => ((object)[
                            'rh'            => strtoupper(trim($data->rh)),
                            'bo'            => strtoupper(trim($data->bo)),
                        ]),
                        'keterangan'        => trim($data->keterangan),
                        'nomor_sj'          => trim($data->nomor_sj),
                        'nomor_serah_terima'=> trim($data->nomor_serah_terima),
                        'total'             => ((object)[
                            'sub_total'     => (double)$sub_total,
                            'disc_header'   => (double)$data->disc_header,
                            'disc_header_rp' => round(((double)$sub_total * (double)$data->disc_header) / 100),
                            'disc_rp'       => (double)$data->disc_rp,
                            'disc_rp1'      => (double)$data->disc_rp1,
                            'total'         => (double)$data->total,
                        ]),
                        'detail'            => $data_faktur_detail_temp
                                                ->where('nomor_faktur', strtoupper(trim($data->nomor_faktur)))
                                                ->values()
                                                ->all()
                    ]);

                    $nomor_faktur = strtoupper(trim($data->nomor_faktur));
                }
            }

            return Response::responseSuccess('success', $data_faktur);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }

    }

    public function prosesApproveMarketplace(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_invoice'     => 'required',
                'companyid'         => 'required|string',
                'user_id'           => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih nomor invoice terlebih dahulu");
            }

            $sql = DB::table('faktur')->lock('with (nolock)')
                    ->selectRaw("isnull(faktur.no_faktur, '') as nomor_faktur,
                            isnull(faktur.kd_ekspedisi, '') as kode_ekspedisi,
                            isnull(faktur.sts_rugi, 0) as status_penjualan_rugi")
                    ->where('faktur.ket', $request->get('nomor_invoice'))
                    ->where('faktur.companyid', $request->get('companyid'))
                    ->first();

            if(empty($sql->nomor_faktur) || trim($sql->nomor_faktur) == '') {
                return Response::responseWarning('Nomor faktur tidak terdaftar');
            }

            if(strtoupper(trim($sql->kode_ekspedisi)) == '') {
                return Response::responseWarning('Kode ekspedisi masih kosong');
            }

            if((int)$sql->status_penjualan_rugi == 1) {
                return Response::responseWarning('Faktur penjualan rugi, minta akses ke supervisor untuk meng-approve faktur penjualan rugi');
            }

            if(strtoupper(trim($sql->kode_ekspedisi)) == 'TKPDEXP') {
                return Response::responseWarning('Kode ekspedisi masih kurir rekomendasi dan belum divalidasi');
            }

            DB::transaction(function () use ($request) {
                DB::update('update  faktur
                            set     approve_ol=1, approve_user=?
                            where   faktur.ket=? and
                                    faktur.companyid=?',
                                    [
                                        date('d-m-Y').'='.date('H:i:s').'=SUMAOFFICE='.strtoupper(trim($request->get('user_id'))),
                                        $request->get('nomor_invoice'),
                                        $request->get('companyid')
                                    ]);
            });

            return Response::responseSuccess('Data Berhasil Di Approve', null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function prosesApproveInternal(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_faktur'  => 'required',
                'companyid'     => 'required|string',
                'user_id'       => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih nomor faktur terlebih dahulu");
            }

            $sql = DB::table('faktur')->lock('with (nolock)')
                    ->selectRaw("isnull(faktur.no_faktur, '') as nomor_faktur,
                            isnull(faktur.kd_ekspedisi, '') as kode_ekspedisi,
                            isnull(faktur.sts_rugi, 0) as status_penjualan_rugi")
                    ->where('faktur.no_faktur', $request->get('nomor_faktur'))
                    ->where('faktur.companyid', $request->get('companyid'))
                    ->first();

            if(empty($sql->nomor_faktur) || trim($sql->nomor_faktur) == '') {
                return Response::responseWarning('Nomor faktur tidak terdaftar');
            }

            if(strtoupper(trim($sql->kode_ekspedisi)) == '') {
                return Response::responseWarning('Kode ekspedisi masih kosong');
            }

            if((int)$sql->status_penjualan_rugi == 1) {
                return Response::responseWarning('Faktur penjualan rugi, minta akses ke supervisor untuk meng-approve faktur penjualan rugi');
            }

            if(strtoupper(trim($sql->kode_ekspedisi)) == 'TKPDEXP') {
                return Response::responseWarning('Kode ekspedisi masih kurir rekomendasi dan belum divalidasi');
            }

            DB::transaction(function () use ($request) {
                DB::update('update  faktur
                            set     approve_ol=1, approve_user=?
                            where   faktur.no_faktur=? and
                                    faktur.companyid=?',
                                    [
                                        date('d-m-Y').'='.date('H:i:s').'=SUMAOFFICE='.strtoupper(trim($request->get('user_id'))),
                                        $request->get('nomor_faktur'),
                                        $request->get('companyid')
                                    ]);
            });

            return Response::responseSuccess('Data Berhasil Di Approve', null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
