<?php

namespace App\Http\Controllers\Api\Backend\Online\Tiktok;

use App\Helpers\Api\Response;
use App\Http\Controllers\Controller;
use App\Helpers\Api\ServiceTiktok;
use App\Helpers\Api\UpdateToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiOrderController extends Controller
{
    public function daftarOrder(Request $request) {
        $validate = Validator::make($request->all(), [
            'start_date'    => 'required',
            'end_date'      => 'required',
            'companyid'     => 'required',
        ]);

        if ($validate->fails()) {
            return Response::responseWarning("Start date, end date, order status, sort by, dan company Id harus terisi");
        }

        $start_date = $request->get('start_date').'T'.'00:00:00';
        $end_date = $request->get('end_date').'T'.'23:59:59';

        if(strtotime($start_date) > strtotime($end_date)) {
            return Response::responseWarning('Tanggal awal harus lebih kecil dari tanggal akhir');
        } else {
            $seconds_to_expire = strtotime($start_date) - strtotime($end_date);

            if($seconds_to_expire >= 15 * 86400) {
                return Response::responseWarning('Rentang Tanggal harus kurang dari 3 hari');
            }
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

        $responseTiktok = ServiceTiktok::GetOrderList($token_tiktok, strtotime($start_date), strtotime($end_date),
                                            20, $request->get('status'), 'CREATE_TIME',
                                            1, $request->get('cursor'));
        $statusServer = (json_decode($responseTiktok)->code == 0) ? 1 : 0;

        $status_next_page = false;
        $cursor_next_page = '';
        $list_invoice = '';
        $list_invoice_faktur = '';

        if($statusServer == 0) {
            return Response::responseWarning(json_decode($responseTiktok)->message, null);
        } else {
            $dataApi = json_decode($responseTiktok)->data;
            $status_next_page = $dataApi->more;
            $cursor_next_page = $dataApi->next_cursor;

            if(!empty($dataApi->order_list)) {
                foreach($dataApi->order_list as $data) {
                    if(trim($list_invoice) == '') {
                        $list_invoice = '"'.$data->order_id.'"';
                    } else {
                        $list_invoice .= ',"'.$data->order_id.'"';
                    }

                    if(trim($list_invoice_faktur) == '') {
                        $list_invoice_faktur = "'".$data->order_id."'";
                    } else {
                        $list_invoice_faktur .= ",'".$data->order_id."'";
                    }
                }
            } else {
                return Response::responseSuccess('success', [ 'status_next_page' => $status_next_page, 'cursor_next_page' => $cursor_next_page, 'order_list' => [] ]);
            }

            $data_invoice = [];
            $data_faktur = new Collection();

            if(trim($list_invoice) == '') {
                return Response::responseWarning(json_decode($responseTiktok)->message, null);
            } else {
                $responseTiktok = ServiceTiktok::GetOrderDetail($token_tiktok, $list_invoice);
                $statusServer = (json_decode($responseTiktok)->code == 0) ? 1 : 0;

                if($statusServer == 0) {
                    return Response::responseWarning(json_decode($responseTiktok)->message, null);
                } else {
                    $dataApi = json_decode($responseTiktok)->data;

                    $sql = "select  isnull(faktur.no_faktur, 0) as nomor_faktur,
                                    isnull(faktur.tgl_faktur, '') as tanggal_faktur,
                                    isnull(faktur.kd_lokasi, '') as kode_lokasi,
                                    isnull(faktur.kd_sales, '') as kode_sales,
                                    isnull(faktur.kd_dealer, '') as kode_dealer,
                                    isnull(faktur.kd_ekspedisi, '') as kode_ekspedisi,
                                    isnull(faktur.ket, '') as keterangan,
                                    isnull(faktur.total, 0) as total,
                                    isnull(sj_dtl.no_sj, '') as nomor_surat_jalan,
                                    isnull(serah_online_dtl.no_dok, '') as nomor_serah_terima
                            from
                            (
                                select  faktur.companyid, faktur.no_faktur, faktur.tgl_faktur,
                                        faktur.kd_sales, faktur.kd_dealer, faktur.kd_ekspedisi,
                                        faktur.ket, faktur.total,
                                        max(fakt_dtl.kd_lokasi) as kd_lokasi
                                from
                                (
                                    select  faktur.companyid, faktur.no_faktur, faktur.tgl_faktur,
                                            faktur.kd_sales, faktur.kd_dealer, faktur.kd_ekspedisi,
                                            faktur.ket, faktur.total
                                    from    faktur with (nolock)
                                    where   faktur.ket in (".$list_invoice_faktur.") and
                                            faktur.companyid=?
                                )  faktur
                                    left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                                faktur.companyid=fakt_dtl.companyid
                                group by faktur.companyid, faktur.no_faktur, faktur.tgl_faktur,
                                        faktur.kd_sales, faktur.kd_dealer, faktur.kd_ekspedisi,
                                        faktur.ket, faktur.total
                            )   faktur
                                    left join sj_dtl with (nolock) on faktur.no_faktur=sj_dtl.no_faktur and
                                                faktur.companyid=sj_dtl.companyid
                                    left join serah_online_dtl with (nolock) on sj_dtl.no_sj=serah_online_dtl.no_sj and
                                                faktur.companyid=serah_online_dtl.companyid";

                    $result = DB::select($sql, [ $request->get('companyid') ]);

                    foreach($result as $data) {
                        $data_faktur->push((object) [
                            'nomor_faktur'          => strtoupper(trim($data->nomor_faktur)),
                            'nomor_surat_jalan'     => strtoupper(trim($data->nomor_surat_jalan)),
                            'nomor_serah_terima'    => strtoupper(trim($data->nomor_serah_terima)),
                            'tanggal'               => trim($data->tanggal_faktur),
                            'kode_lokasi'           => trim($data->kode_lokasi),
                            'kode_sales'            => trim($data->kode_sales),
                            'kode_dealer'           => trim($data->kode_dealer),
                            'kode_ekspedisi'        => trim($data->kode_ekspedisi),
                            'keterangan'            => strtoupper(trim($data->keterangan)),
                            'total'                 => (double)$data->total,
                        ]);
                    }

                    foreach($dataApi->order_list as $data) {
                        $product_original_price = 0;
                        $product_sale_price = 0;
                        $seller_discount = 0;
                        $platform_discount = 0;
                        $country = '';
                        $province = '';
                        $city = '';
                        $sub_district = '';
                        $urban_community = '';

                        $create_time = Carbon::createFromTimestampMs((int)$data->create_time);
                        $update_time = Carbon::createFromTimestamp((int)$data->update_time);

                        foreach($data->district_info_list as $district) {
                            if(strtolower($district->address_level_name) == 'country') {
                                $country = trim($district->address_name);
                            }
                            if(strtolower($district->address_level_name) == 'province') {
                                $province = trim($district->address_name);
                            }
                            if(strtolower($district->address_level_name) == 'city') {
                                $city = trim($district->address_name);
                            }
                            if(strtolower($district->address_level_name) == 'sub-district') {
                                $sub_district = trim($district->address_name);
                            }
                            if(strtolower($district->address_level_name) == 'urban community') {
                                $urban_community = trim($district->address_name);
                            }
                        }
                        foreach($data->item_list as $item) {
                            $product_original_price = (double)$product_original_price + (double)$item->sku_original_price;
                            $product_sale_price = (double)$product_sale_price + (double)$item->sku_sale_price;
                            $seller_discount = (double)$seller_discount + (double)$item->sku_seller_discount;
                            $platform_discount = (double)$platform_discount + (double)$item->sku_platform_discount_total;;
                        }
                        $data_invoice[] = [
                            'order_id'                          => trim($data->order_id),
                            'order_status'                      => (int)$data->order_status,
                            'create_time'                       => $create_time,
                            'update_time'                       => $update_time,
                            'buyer' => [
                                'buyer_uid'                     => trim($data->buyer_uid),
                                'urban_community'               => trim($urban_community),
                                'sub_district'                  => trim($sub_district),
                                'city'                          => trim($city),
                                'province'                      => trim($province),
                                'country'                       => trim($country),
                            ],
                            'product' => [
                                'product_original_price'        => (double)$product_original_price,
                                'product_sale_price'            => (double)$product_sale_price,
                                'seller_discount'               => (double)$seller_discount,
                                'platform_discount'             => (double)$platform_discount,
                            ],
                            'delivery' => [
                                'delivery_option_id'            => (empty($data->delivery_option_id)) ? '' : trim($data->delivery_option_id),
                                'delivery_option'               => (empty($data->delivery_option)) ? '' : trim($data->delivery_option),
                                'delivery_option_description'   => (empty($data->delivery_option_description)) ? '' : trim($data->delivery_option_description)
                            ],
                            'payment' => [
                                'payment_method'                => (empty($data->payment_method)) ? '' : trim($data->payment_method),
                                'payment_method_name'           => (empty($data->payment_method_name)) ? '' : trim($data->payment_method_name),
                                'currency'                      => (empty($data->payment_info->currency)) ? '' : trim($data->payment_info->currency),
                                'original_shipping_fee'         => (empty($data->payment_info->original_shipping_fee)) ? 0 : (double)$data->payment_info->original_shipping_fee,
                                'platform_discount'             => (empty($data->payment_info->platform_discount)) ? 0 : (double)$data->payment_info->platform_discount,
                                'seller_discount'               => (empty($data->payment_info->seller_discount)) ? 0 : (double)$data->payment_info->seller_discount,
                                'shipping_fee'                  => (empty($data->payment_info->shipping_fee)) ? 0 : (double)$data->payment_info->shipping_fee,
                                'shipping_fee_platform_discount' => (empty($data->payment_info->shipping_fee_platform_discount)) ? 0 : (double)$data->payment_info->shipping_fee_platform_discount,
                                'shipping_fee_seller_discount'  => (empty($data->payment_info->shipping_fee_seller_discount)) ? 0 : (double)$data->payment_info->shipping_fee_seller_discount,
                                'sub_total'                     => (empty($data->payment_info->sub_total)) ? 0 : (double)$data->payment_info->sub_total,
                                'taxes'                         => (empty($data->payment_info->taxes)) ? 0 : (double)$data->payment_info->taxes,
                                'total_amount'                  => (empty($data->payment_info->total_amount)) ? 0 : (double)$data->payment_info->total_amount,
                            ],
                            'recipient_address' => [
                                'address_detail'                => trim($data->recipient_address->address_detail),
                                'address_line_list'             => $data->recipient_address->address_line_list,
                                'city'                          => trim($data->recipient_address->city),
                                'district'                      => trim($data->recipient_address->district),
                                'full_address'                  => trim($data->recipient_address->full_address),
                                'name'                          => trim($data->recipient_address->name),
                                'phone'                         => trim($data->recipient_address->phone),
                                'region'                        => trim($data->recipient_address->region),
                                'region_code'                   => trim($data->recipient_address->region_code),
                                'state'                         => trim($data->recipient_address->state),
                                'town'                          => trim($data->recipient_address->town),
                                'address_detail'                => trim($data->recipient_address->zipcode),
                            ],
                            'shipping'  => [
                                'shipping_provider_id'          => (empty($data->shipping_provider_id)) ? '' : trim($data->shipping_provider_id),
                                'shipping_provider'             => (empty($data->shipping_provider)) ? '' : trim($data->shipping_provider),
                                'tracking_number'               => (empty($data->tracking_number)) ? '' : trim($data->tracking_number),
                            ],
                            'faktur'                            => $data_faktur
                                                                    ->where('keterangan', trim($data->order_id))
                                                                    ->values()
                                                                    ->all()
                        ];
                    }

                    $data_order = [
                        'status_next_page'  => $status_next_page,
                        'cursor_next_page'  => $cursor_next_page,
                        'order_list'        => $data_invoice
                    ];

                    return Response::responseSuccess('success', $data_order);
                }
            }
        }
    }

    public function singleOrder(Request $request) {
        $validate = Validator::make($request->all(), [
            'nomor_invoice' => 'required',
            'companyid'     => 'required',
        ]);

        if ($validate->fails()) {
            return Response::responseWarning("Nomor invoice dan company Id harus terisi");
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

        $data_invoice = [];
        $data_faktur = new Collection();

        $responseTiktok = ServiceTiktok::GetOrderDetail($token_tiktok, '"'.trim($request->get('nomor_invoice')).'"');
        $statusServer = (json_decode($responseTiktok)->code == 0) ? 1 : 0;

        if($statusServer == 0) {
            return Response::responseWarning(json_decode($responseTiktok)->message, null);
        } else {
            $dataApi = json_decode($responseTiktok)->data;

            $sql = "select  isnull(faktur.no_faktur, 0) as nomor_faktur,
                            isnull(faktur.tgl_faktur, '') as tanggal_faktur,
                            isnull(faktur.kd_lokasi, '') as kode_lokasi,
                            isnull(faktur.kd_sales, '') as kode_sales,
                            isnull(faktur.kd_dealer, '') as kode_dealer,
                            isnull(faktur.kd_ekspedisi, '') as kode_ekspedisi,
                            isnull(faktur.ket, '') as keterangan,
                            isnull(faktur.total, 0) as total,
                            isnull(sj_dtl.no_sj, '') as nomor_surat_jalan,
                            isnull(serah_online_dtl.no_dok, '') as nomor_serah_terima
                    from
                    (
                        select  faktur.companyid, faktur.no_faktur, faktur.tgl_faktur,
                                faktur.kd_sales, faktur.kd_dealer, faktur.kd_ekspedisi,
                                faktur.ket, faktur.total,
                                max(fakt_dtl.kd_lokasi) as kd_lokasi
                        from
                        (
                            select  faktur.companyid, faktur.no_faktur, faktur.tgl_faktur,
                                    faktur.kd_sales, faktur.kd_dealer, faktur.kd_ekspedisi,
                                    faktur.ket, faktur.total
                            from    faktur with (nolock)
                            where   faktur.ket=? and
                                    faktur.companyid=?
                        )  faktur
                            left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                        faktur.companyid=fakt_dtl.companyid
                        group by faktur.companyid, faktur.no_faktur, faktur.tgl_faktur,
                                faktur.kd_sales, faktur.kd_dealer, faktur.kd_ekspedisi,
                                faktur.ket, faktur.total
                    )   faktur
                            left join sj_dtl with (nolock) on faktur.no_faktur=sj_dtl.no_faktur and
                                        faktur.companyid=sj_dtl.companyid
                            left join serah_online_dtl with (nolock) on sj_dtl.no_sj=serah_online_dtl.no_sj and
                                        faktur.companyid=serah_online_dtl.companyid";

            $result = DB::select($sql, [ trim($request->get('nomor_invoice')), $request->get('companyid') ]);

            foreach($result as $data) {
                $data_faktur->push((object) [
                    'nomor_faktur'          => strtoupper(trim($data->nomor_faktur)),
                    'nomor_surat_jalan'     => strtoupper(trim($data->nomor_surat_jalan)),
                    'nomor_serah_terima'    => strtoupper(trim($data->nomor_serah_terima)),
                    'tanggal'               => trim($data->tanggal_faktur),
                    'kode_lokasi'           => trim($data->kode_lokasi),
                    'kode_sales'            => trim($data->kode_sales),
                    'kode_dealer'           => trim($data->kode_dealer),
                    'kode_ekspedisi'        => trim($data->kode_ekspedisi),
                    'keterangan'            => strtoupper(trim($data->keterangan)),
                    'total'                 => (double)$data->total,
                ]);
            }

            foreach($dataApi->order_list as $data) {
                $product_original_price = 0;
                $product_sale_price = 0;
                $seller_discount = 0;
                $platform_discount = 0;
                $country = '';
                $province = '';
                $city = '';
                $sub_district = '';
                $urban_community = '';

                foreach($data->district_info_list as $district) {
                    if(strtolower($district->address_level_name) == 'country') {
                        $country = trim($district->address_name);
                    }
                    if(strtolower($district->address_level_name) == 'province') {
                        $province = trim($district->address_name);
                    }
                    if(strtolower($district->address_level_name) == 'city') {
                        $city = trim($district->address_name);
                    }
                    if(strtolower($district->address_level_name) == 'sub-district') {
                        $sub_district = trim($district->address_name);
                    }
                    if(strtolower($district->address_level_name) == 'urban community') {
                        $urban_community = trim($district->address_name);
                    }
                }
                foreach($data->item_list as $item) {
                    $product_original_price = (double)$product_original_price + (double)$item->sku_original_price;
                    $product_sale_price = (double)$product_sale_price + (double)$item->sku_sale_price;
                    $seller_discount = (double)$seller_discount + (double)$item->sku_seller_discount;
                    $platform_discount = (double)$platform_discount + (double)$item->sku_platform_discount_total;;
                }
                $data_invoice[] = [
                    'order_id'                          => trim($data->order_id),
                    'order_status'                      => (int)$data->order_status,
                    'create_time'                       => (int)$data->create_time,
                    'update_time'                       => (int)$data->update_time,
                    'buyer' => [
                        'buyer_uid'                     => trim($data->buyer_uid),
                        'urban_community'               => trim($urban_community),
                        'sub_district'                  => trim($sub_district),
                        'city'                          => trim($city),
                        'province'                      => trim($province),
                        'country'                       => trim($country),
                    ],
                    'delivery' => [
                        'delivery_option_id'            => trim($data->delivery_option_id),
                        'delivery_option'               => trim($data->delivery_option),
                        'delivery_option_description'   => trim($data->delivery_option_description)
                    ],
                    'product' => [
                        'product_original_price'        => (double)$product_original_price,
                        'product_sale_price'            => (double)$product_sale_price,
                        'seller_discount'               => (double)$seller_discount,
                        'platform_discount'             => (double)$platform_discount,
                    ],
                    'payment' => [
                        'payment_method'                => trim($data->payment_method),
                        'payment_method_name'           => trim($data->payment_method_name),
                        'currency'                      => trim($data->payment_info->currency),
                        'original_shipping_fee'         => (double)$data->payment_info->original_shipping_fee,
                        'platform_discount'             => (double)$data->payment_info->platform_discount,
                        'seller_discount'               => (double)$data->payment_info->seller_discount,
                        'shipping_fee'                  => (double)$data->payment_info->shipping_fee,
                        'shipping_fee_platform_discount' => (double)$data->payment_info->shipping_fee_platform_discount,
                        'shipping_fee_seller_discount'  => (double)$data->payment_info->shipping_fee_seller_discount,
                        'sub_total'                     => (double)$data->payment_info->sub_total,
                        'taxes'                         => (double)$data->payment_info->taxes,
                        'total_amount'                  => (double)$data->payment_info->total_amount,
                    ],
                    'recipient_address' => [
                        'address_detail'                => trim($data->recipient_address->address_detail),
                        'address_line_list'             => $data->recipient_address->address_line_list,
                        'city'                          => trim($data->recipient_address->city),
                        'district'                      => trim($data->recipient_address->district),
                        'full_address'                  => trim($data->recipient_address->full_address),
                        'name'                          => trim($data->recipient_address->name),
                        'phone'                         => trim($data->recipient_address->phone),
                        'region'                        => trim($data->recipient_address->region),
                        'region_code'                   => trim($data->recipient_address->region_code),
                        'state'                         => trim($data->recipient_address->state),
                        'town'                          => trim($data->recipient_address->town),
                        'address_detail'                => trim($data->recipient_address->zipcode),
                    ],
                    'shipping'  => [
                        'shipping_provider_id'          => trim($data->shipping_provider_id),
                        'shipping_provider'             => trim($data->shipping_provider),
                        'tracking_number'               => trim($data->tracking_number),
                    ],
                    'faktur'                            => $data_faktur
                                                            ->where('keterangan', trim($data->order_id))
                                                            ->values()
                                                            ->all()
                ];
            }

            $data_order = [
                'status_next_page'  => false,
                'cursor_next_page'  => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
                'order_list'        => $data_invoice
            ];

            return Response::responseSuccess('success', $data_order);
        }

    }
}

