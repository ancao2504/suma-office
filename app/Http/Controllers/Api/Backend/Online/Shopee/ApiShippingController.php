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

class ApiShippingController extends Controller
{
    public function metodePengiriman(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_invoice'     => 'required'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Nomor invoice tidak boleh kosong");
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
            $statusServer = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

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

            $responseShopee = ServiceShopee::GetShippingParameter(trim($token_shopee), $request->get('nomor_invoice'));
            $statusResponseShopee = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

            if($statusResponseShopee == 1) {
                $dataShopee = json_decode($responseShopee)->response;

                return Response::responseSuccess('success', $dataShopee);
            } else {
                return Response::responseWarning(json_decode($responseShopee)->error);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function prosesPickup(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_invoice'     => 'required',
                'address_id'        => 'required',
                'pickup_time_id'    => 'required',
                'companyid'         => 'required',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Isi data proses pickup secara lengkap");
            }

            $sql = DB::table('faktur')->lock('with (nolock)')
                ->selectRaw("isnull(faktur.no_faktur, '') as nomor_faktur")
                ->where('faktur.ket', $request->get('nomor_invoice'))
                ->where('faktur.companyid', $request->get('companyid'))
                ->first();

            if(empty($sql->nomor_faktur)) {
                return Response::responseWarning('Nomor invoice tidak terdaftar di data faktur internal');
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
            $statusServer = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

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

            $responseShopee = ServiceShopee::GetTrackingNumber(trim($token_shopee), $request->get('nomor_invoice'));
            $statusResponseShopee = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

            if($statusResponseShopee == 1) {
                $dataTrackingParameter = json_decode($responseShopee)->response;
                $tracking_number = $dataTrackingParameter->tracking_number;

                $responseShopee = ServiceShopee::ShipOrder(trim($token_shopee), trim($request->get('nomor_invoice')),
                                        $request->get('address_id'), $request->get('pickup_time_id'), $tracking_number);
                $statusResponseShopee = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

                if($statusResponseShopee == 1) {
                    DB::transaction(function () use ($request) {
                        DB::insert('exec SP_Faktur_ReqPickup ?,?', [
                            strtoupper(trim($request->get('nomor_invoice'))), strtoupper(trim($request->get('companyid'))),
                        ]);
                    });
                    return Response::responseSuccess('Data Berhasil Disimpan dan me-request pickup Shopee');
                } else {
                    return Response::responseWarning('Gagal melakukan request API ShipOrder. '.json_decode($responseShopee)->message);
                }
            } else {
                return Response::responseWarning('Gagal melakukan request API GetTrackingNumber. '.json_decode($responseShopee)->message);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function prosesBuatDokumenPengiriman(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_invoice' => 'required',
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
            // CEK KONEKSI API SHOPEE
            // ==========================================================================
            $responseShopee = ServiceShopee::GetShopInfo(trim($token_shopee));
            $statusServer = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

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

            $responseShopee = ServiceShopee::GetTrackingNumber(trim($token_shopee), $request->get('nomor_invoice'));
            $statusResponseShopee = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

            if($statusResponseShopee == 1) {
                $dataApiShopee = json_decode($responseShopee)->response;
                $tracking_number = $dataApiShopee->tracking_number;

                $responseShopee = ServiceShopee::GetShippingDocumentParameter(trim($token_shopee), $request->get('nomor_invoice'));
                $statusResponseShopee = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

                if($statusResponseShopee == 1) {
                    $dataApiShopee = json_decode($responseShopee)->response;

                    $type_document = '';
                    foreach($dataApiShopee->result_list as $data) {
                        $type_document = $data->suggest_shipping_document_type;
                    }

                    $responseShopee = ServiceShopee::CreateShippingDocument(trim($token_shopee), $request->get('nomor_invoice'), $type_document, $tracking_number);
                    $statusResponseShopee = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

                    if($statusResponseShopee == 1) {
                        $responseShopee = ServiceShopee::DownloadShippingDocument(trim($token_shopee), $request->get('nomor_invoice'), $type_document);
                        $statusResponseShopee = (empty(json_decode($responseShopee->response)->error)) ? 1 : 0;

                        if($statusResponseShopee == 1) {
                            return Response::responseSuccess('success', [
                                'url'           => $responseShopee->url,
                                'parameter'     => $responseShopee->parameter,
                             ]);
                        } else {
                            return Response::responseWarning('Gagal melakukan request API DownloadShippingDocument. '.json_decode($responseShopee->response)->message);
                        }
                    } else {
                        $statusErrorResponseShopee = (empty(json_decode($responseShopee)->response->result_list[0]->fail_message)) ? 0 : 1;

                        if($statusErrorResponseShopee == 1) {
                            return Response::responseWarning('Gagal membuat dokumen pengiriman. '.json_decode($responseShopee)->response->result_list[0]->fail_message);
                        } else {
                            return Response::responseWarning('Gagal melakukan request API CreateShippingDocument. '.json_decode($responseShopee)->message);
                        }
                    }
                } else {
                    return Response::responseWarning('Gagal melakukan request API GetShippingDocumentParameter. '.json_decode($responseShopee)->message);
                }
            } else {
                return Response::responseWarning('Gagal melakukan request API GetTrackingNumber. '.json_decode($responseShopee)->message);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function prosesCetakLabel(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_invoice' => 'required',
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
            // CEK KONEKSI API SHOPEE
            // ==========================================================================
            $responseShopee = ServiceShopee::GetShopInfo(trim($token_shopee));
            $statusServer = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

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

            $statusCetakLabel = 0;

            $responseShopee = ServiceShopee::GetShippingDocumentResult(trim($token_shopee), $request->get('nomor_invoice'));
            $statusResponseShopee = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

            if($statusResponseShopee == 1) {
                $dataApiShopee = json_decode($responseShopee)->response;

                foreach($dataApiShopee->result_list as $data) {
                    if(strtoupper(trim($data->status)) == 'READY') {
                        $statusCetakLabel = 1;
                    }
                }
            } else {
                $errorMessage = json_decode($responseShopee)->error;

                if($errorMessage == 'logistics.shipping_document_should_print_first') {
                    $statusCetakLabel = 0;
                } else {
                    return Response::responseWarning('Gagal melakukan request API GetShippingDocumentResult. '.json_decode($responseShopee)->message);
                }
            }

            if((int)$statusCetakLabel == 0) {
                $responseShopee = ServiceShopee::GetTrackingNumber(trim($token_shopee), $request->get('nomor_invoice'));
                $statusResponseShopee = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

                if($statusResponseShopee == 1) {
                    $dataApiShopee = json_decode($responseShopee)->response;
                    $tracking_number = $dataApiShopee->tracking_number;

                    $responseShopee = ServiceShopee::GetShippingDocumentParameter(trim($token_shopee), $request->get('nomor_invoice'));
                    $statusResponseShopee = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

                    if($statusResponseShopee == 1) {
                        $dataApiShopee = json_decode($responseShopee)->response;

                        $type_document = '';
                        foreach($dataApiShopee->result_list as $data) {
                            $type_document = $data->suggest_shipping_document_type;
                        }

                        $responseShopee = ServiceShopee::CreateShippingDocument(trim($token_shopee), $request->get('nomor_invoice'), $type_document, $tracking_number);
                        $statusResponseShopee = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

                        if($statusResponseShopee == 1) {
                            $responseShopee = ServiceShopee::DownloadShippingDocument(trim($token_shopee), $request->get('nomor_invoice'), $type_document);
                            $statusResponseShopee = (empty(json_decode($responseShopee->response)->error)) ? 1 : 0;

                            if($statusResponseShopee == 1) {
                                return Response::responseSuccess('success', [
                                    'url'           => $responseShopee->url,
                                    'parameter'     => $responseShopee->parameter,
                                 ]);
                            } else {
                                return Response::responseWarning('Gagal melakukan request API DownloadShippingDocument. '.json_decode($responseShopee->response)->message);
                            }
                        } else {
                            $statusErrorResponseShopee = (empty(json_decode($responseShopee)->response->result_list[0]->fail_message)) ? 0 : 1;

                            if($statusErrorResponseShopee == 1) {
                                return Response::responseWarning('Gagal membuat dokumen pengiriman. '.json_decode($responseShopee)->response->result_list[0]->fail_message);
                            } else {
                                return Response::responseWarning('Gagal melakukan request API CreateShippingDocument. '.json_decode($responseShopee)->message);
                            }
                        }
                    } else {
                        return Response::responseWarning('Gagal melakukan request API GetShippingDocumentParameter. '.json_decode($responseShopee)->message);
                    }
                } else {
                    return Response::responseWarning('Gagal melakukan request API GetTrackingNumber. '.json_decode($responseShopee)->message);
                }
            }

            if((int)$statusCetakLabel == 1) {
                $responseShopee = ServiceShopee::GetShippingDocumentParameter(trim($token_shopee), $request->get('nomor_invoice'));
                $statusResponseShopee = (empty(json_decode($responseShopee)->error)) ? 1 : 0;

                if($statusResponseShopee == 1) {
                    $dataApiShopee = json_decode($responseShopee)->response;

                    $type_document = '';
                    foreach($dataApiShopee->result_list as $data) {
                        $type_document = $data->suggest_shipping_document_type;
                    }

                    $responseShopee = ServiceShopee::DownloadShippingDocument(trim($token_shopee), $request->get('nomor_invoice'), $type_document);
                    $statusResponseShopee = (empty(json_decode($responseShopee->response)->error)) ? 1 : 0;

                    if($statusResponseShopee == 1) {
                        return Response::responseSuccess('success', [
                            'url'           => $responseShopee->url,
                            'parameter'     => $responseShopee->parameter,
                            'response'      => $this->convert_from_latin1_to_utf8_recursively($responseShopee->response),
                         ]);
                    } else {
                        return Response::responseWarning('Gagal melakukan request API DownloadShippingDocument. '.json_decode($responseShopee->response)->message);
                    }
                } else {
                    return Response::responseWarning('Gagal melakukan request API GetShippingDocumentParameter. '.json_decode($responseShopee)->message);
                }
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public static function convert_from_latin1_to_utf8_recursively($dat) {
            if (is_string($dat)) {
                return utf8_encode($dat);
            } elseif (is_array($dat)) {
                $ret = [];
                foreach ($dat as $i => $d) $ret[ $i ] = self::convert_from_latin1_to_utf8_recursively($d);

                return $ret;
            } elseif (is_object($dat)) {
                foreach ($dat as $i => $d) $dat->$i = self::convert_from_latin1_to_utf8_recursively($d);

                return $dat;
            } else {
                return $dat;
            }
    }
}
