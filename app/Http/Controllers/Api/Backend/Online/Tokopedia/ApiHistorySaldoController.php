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

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            if(strtotime($start_date) > strtotime($end_date)) {
                return Response::responseWarning('Tanggal awal harus lebih kecil dari tanggal akhir');
            } else {
                $seconds_to_expire = strtotime($end_date) - strtotime($start_date);

                if($seconds_to_expire >= 7 * 86400) {
                    return Response::responseWarning('Rentang Tanggal harus kurang dari 7 hari');
                }
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

            $responseTokopedia = ServiceTokopedia::GetSaldoHistory(trim($token_tokopedia), $request->get('page'), $request->get('per_page'),
                                        $start_date, $end_date);
            $statusResponseTokopedia = (empty(json_decode($responseTokopedia)->header->error_code)) ? 1 : 0;

            if($statusResponseTokopedia == 1) {
                $dataApiTokopedia = json_decode($responseTokopedia)->data->saldo_history;

                $jumlah_data = 0;
                $nomor_invoice_internal = '';
                $data_faktur = new Collection();
                $data_history_saldo = new Collection();

                foreach($dataApiTokopedia as $data_invoice) {
                    $jumlah_data = (double)$jumlah_data + 1;

                    if((int)$data_invoice->type == 1001) {
                        if(trim($nomor_invoice_internal) == '') {
                            $nomor_invoice_internal = "'".substr($data_invoice->note, -27)."'";
                        } else {
                            $nomor_invoice_internal = $nomor_invoice_internal.','."'".substr($data_invoice->note, -27)."'";
                        }
                    }
                }

                if((double)$jumlah_data > 0) {
                    if($nomor_invoice_internal != '') {
                        $sql = "select  isnull(faktur.ket, '') as nomor_invoice,
                                        isnull(faktur.no_faktur, '') as nomor_faktur,
                                        isnull(faktur.total, 0) as total
                                from    faktur with (nolock)
                                where   faktur.companyid=? and
                                        faktur.ket in (".$nomor_invoice_internal.")";

                        $result = DB::select($sql, [ $request->get('companyid') ]);

                        foreach($result as $data_internal) {
                            $data_faktur->push((object) [
                                'nomor_invoice' => strtoupper(trim($data_internal->nomor_invoice)),
                                'nomor_faktur'  => strtoupper(trim($data_internal->nomor_faktur)),
                                'total'         => (double)$data_internal->total,
                            ]);
                        }
                    }

                    foreach($dataApiTokopedia as $data) {
                        $data_history_saldo->push((object) [
                            'deposit_id'        => (int)$data->deposit_id,
                            'type'              => (int)$data->type,
                            'class'             => strtoupper(trim($data->class)),
                            'create_time'       => trim($data->create_time),
                            'type_description'  => trim($data->type_description),
                            'note'              => trim($data->note),
                            'amount'            => (double)$data->amount,
                            'saldo'             => (double)$data->saldo,
                            'faktur'            => ((int)$data->type == 1001) ? $data_faktur->where('nomor_invoice', substr($data->note, -27))->values()->all() : []
                        ]);
                    }
                }
                return Response::responseSuccess('success', $data_history_saldo);
            } else {
                return Response::responseWarning('Gagal mengakses Api Tokopedia GetSaldoHistory. '.json_decode($responseTokopedia)->header->error_code.' '.
                                        json_decode($responseTokopedia)->header->reason);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
