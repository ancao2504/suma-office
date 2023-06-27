<?php

namespace App\Http\Controllers\Api\Backend\Online\Shopee;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use App\Helpers\Api\UpdateToken;
use App\Helpers\Api\ServiceShopee;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class ApiHistorySaldoController extends Controller
{
    public function daftarHistorySaldo(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'page'          => 'required',
                'per_page'      => 'required',
                'start_date'    => 'required',
                'end_date'      => 'required',
                'companyid'     => 'required',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Page, per page, from date, to date harus terisi");
            }

            $start_date = $request->get('start_date').'T'.'00:00:00';
            $end_date = $request->get('end_date').'T'.'23:59:59';

            if(strtotime($start_date) > strtotime($end_date)) {
                return Response::responseWarning('Tanggal awal harus lebih kecil dari tanggal akhir');
            } else {
                $seconds_to_expire = strtotime($end_date) - strtotime($start_date);

                if($seconds_to_expire >= 15 * 86400) {
                    return Response::responseWarning('Rentang Tanggal harus kurang dari 15 hari');
                }
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
            // CEK KONEKSI API SHOPEE
            // ==========================================================================
            $responseShopee = ServiceShopee::GetShopInfo(trim($token_shopee));
            $statusServer = (empty(json_decode($responseShopee)->message)) ? 1 : 0;

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

            $responseShopee = ServiceShopee::GetWalletTransactionList(trim($token_shopee), $request->get('page'), $request->get('per_page'),
                                    strtotime($start_date), strtotime($end_date));
            $statusResponseShopee = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

            if($statusResponseShopee == 1) {
                $dataShopee = json_decode($responseShopee)->response;

                return Response::responseSuccess('success', $dataShopee->transaction_list);
            } else {
                return Response::responseWarning('Gagal mengakses API GetWalletTransactionList. '.json_decode($responseShopee)->error.', '.json_decode($responseShopee)->message);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function detailHistorySaldo(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'order_sn'      => 'required',
                'companyid'     => 'required',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Pilih nomor invoice shopee terlebih dahulu");
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
            // CEK KONEKSI API SHOPEE
            // ==========================================================================
            $responseShopee = ServiceShopee::GetShopInfo(trim($token_shopee));
            $statusServer = (empty(json_decode($responseShopee)->message)) ? 1 : 0;

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

            $sql = DB::table('faktur')->lock('with (nolock)')
                    ->selectRaw("isnull(faktur.no_faktur, '') as nomor_faktur,
                                isnull(faktur.ket, '') as nomor_invoice,
                                isnull(faktur.total, 0) as total")
                    ->where('faktur.companyid', $request->get('companyid'))
                    ->where('faktur.ket', $request->get('order_sn'))
                    ->get();

            $responseShopee = ServiceShopee::GetEscrowDetail(trim($token_shopee), $request->get('order_sn'));
            $statusResponseShopee = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

            $dataSaldoShopee = new Collection();

            if($statusResponseShopee == 1) {
                $dataShopee = json_decode($responseShopee)->response;

                $dataSaldoShopee = [
                    'order_sn'                      => strtoupper(trim($dataShopee->order_sn)),
                    'original_cost_of_goods_sold'   => (double)$dataShopee->order_income->original_cost_of_goods_sold,
                    'service_fee'                   => (double)$dataShopee->order_income->service_fee,
                    'commission_fee'                => (double)$dataShopee->order_income->commission_fee,
                    'delivery_seller_protection_fee_premium_amount' => (double)$dataShopee->order_income->delivery_seller_protection_fee_premium_amount,
                    'admin_amount'                  => (double)$dataShopee->order_income->service_fee + (double)$dataShopee->order_income->commission_fee +
                                                        (double)$dataShopee->order_income->delivery_seller_protection_fee_premium_amount,
                    'escrow_amount'                 => (double)$dataShopee->order_income->escrow_amount,
                    'faktur'                        => $sql
                ];

                return Response::responseSuccess('success', $dataSaldoShopee);
            } else {
                return Response::responseWarning('Gagal mengakses API GetEscrowDetail. '.json_decode($responseShopee)->error.', '.json_decode($responseShopee)->message);
            }

        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function listDetailHistorySaldo(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'page'          => 'required',
                'start_date'    => 'required',
                'end_date'      => 'required',
                'companyid'     => 'required',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Page, per page, from date, to date harus terisi");
            }

            $start_date = $request->get('start_date').'T'.'00:00:00';
            $end_date = $request->get('end_date').'T'.'23:59:59';

            if(strtotime($start_date) > strtotime($end_date)) {
                return Response::responseWarning('Tanggal awal harus lebih kecil dari tanggal akhir');
            } else {
                $seconds_to_expire = strtotime($end_date) - strtotime($start_date);

                if($seconds_to_expire >= 15 * 86400) {
                    return Response::responseWarning('Rentang Tanggal harus kurang dari 15 hari');
                }
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
            // CEK KONEKSI API SHOPEE
            // ==========================================================================
            $responseShopee = ServiceShopee::GetShopInfo(trim($token_shopee));
            $statusServer = (empty(json_decode($responseShopee)->message)) ? 1 : 0;

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

            $responseShopee = ServiceShopee::GetWalletTransactionList(trim($token_shopee), $request->get('page'), 10,
                                    strtotime($start_date), strtotime($end_date));
            $statusResponseShopee = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

            $data_history_saldo = new Collection();
            $data_detail_history_saldo = new Collection();
            $data_faktur = new Collection();
            $list_nomor_invoice = '';

            if($statusResponseShopee == 1) {
                $dataSaldoShopee = json_decode($responseShopee)->response;

                $jumlah_data_saldo_shopee = 0;

                foreach($dataSaldoShopee->transaction_list as $data) {
                    $jumlah_data_saldo_shopee = (double)$jumlah_data_saldo_shopee + 1;

                    if(strtoupper(trim($data->order_sn)) != '') {
                        if(strtoupper(trim($list_nomor_invoice)) == '') {
                            $list_nomor_invoice = "'".strtoupper(trim($data->order_sn))."'";
                        } else {
                            $list_nomor_invoice .= ",'".strtoupper(trim($data->order_sn))."'";
                        }
                    }

                    $order_sn = strtoupper(trim($data->order_sn));

                    $data_history_saldo->push((object) [
                        'order_sn'          => strtoupper(trim($data->order_sn)),
                        'create_time'       => $data->create_time,
                        'status'            => trim($data->status),
                        'transaction_type'  => trim($data->transaction_type),
                        'reason'            => trim($data->reason),
                        'wallet_type'       => trim($data->wallet_type),
                        'amount'            => (double)$data->amount,
                        'current_balance'   => (double)$data->current_balance,
                    ]);

                    $responseShopee = ServiceShopee::GetEscrowDetail(trim($token_shopee), $order_sn);
                    $statusResponseShopee = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

                    if($statusResponseShopee == 1) {
                        $dataSaldoDetail = json_decode($responseShopee)->response;

                        $data_detail_history_saldo->push((object) [
                            'order_sn'                      => strtoupper(trim($dataSaldoDetail->order_sn)),
                            'original_cost_of_goods_sold'   => (double)$dataSaldoDetail->order_income->original_cost_of_goods_sold,
                            'service_fee'                   => (double)$dataSaldoDetail->order_income->service_fee,
                            'commission_fee'                => (double)$dataSaldoDetail->order_income->commission_fee,
                            'delivery_seller_protection_fee_premium_amount' => (double)$dataSaldoDetail->order_income->delivery_seller_protection_fee_premium_amount,
                            'admin_amount'                  => (double)$dataSaldoDetail->order_income->service_fee + (double)$dataSaldoDetail->order_income->commission_fee +
                                                                (double)$dataSaldoDetail->order_income->delivery_seller_protection_fee_premium_amount,
                            'escrow_amount'                 => (double)$dataSaldoDetail->order_income->escrow_amount
                        ]);
                    }
                }

                if((double)$jumlah_data_saldo_shopee > 0) {
                    $sql = DB::table('faktur')->lock('with (nolock)')
                            ->selectRaw("isnull(faktur.no_faktur, '') as nomor_faktur,
                                        isnull(faktur.tgl_faktur, '') as tanggal_faktur,
                                        isnull(faktur.ket, '') as order_sn,
                                        isnull(faktur.total, 0) as total")
                            ->where('faktur.companyid', $request->get('companyid'))
                            ->whereRaw("faktur.ket in (".$list_nomor_invoice.")")
                            ->get();

                    $jumlah_data_faktur = 0;

                    foreach($sql as $data) {
                        $jumlah_data_faktur = (double)$jumlah_data_faktur + 1;

                        $data_faktur->push((object) [
                            'order_sn'          => strtoupper(trim($data->order_sn)),
                            'nomor_faktur'      => strtoupper(trim($data->nomor_faktur)),
                            'tanggal_faktur'    => trim($data->tanggal_faktur),
                            'total'             => (double)$data->total,
                        ]);
                    }
                }

                $data_saldo_shopee = new Collection();

                foreach($data_history_saldo as $data) {
                    $data_saldo_shopee->push((object) [
                        'order_sn'          => strtoupper(trim($data->order_sn)),
                        'create_time'       => $data->create_time,
                        'status'            => trim($data->status),
                        'transaction_type'  => trim($data->transaction_type),
                        'reason'            => trim($data->reason),
                        'wallet_type'       => trim($data->wallet_type),
                        'amount'            => (double)$data->amount,
                        'current_balance'   => (double)$data->current_balance,
                        'saldo_detail'      => $data_detail_history_saldo
                                                ->where('order_sn', strtoupper(trim($data->order_sn)))
                                                ->first(),
                        'faktur'            => $data_faktur
                                                ->where('order_sn', strtoupper(trim($data->order_sn)))
                                                ->values()
                                                ->all(),
                    ]);
                }

                return Response::responseSuccess('success', $data_saldo_shopee);
            } else {
                return Response::responseWarning('Gagal mengakses API GetWalletTransactionList. '.json_decode($responseShopee)->error.', '.json_decode($responseShopee)->message);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
