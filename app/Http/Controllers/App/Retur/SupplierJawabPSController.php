<?php

namespace App\Http\Controllers\app\Retur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\App\Service;

class SupplierJawabPSController extends Controller
{
    public function indexPS(Request $request)
    {
        try {
            if ($request->ajax()) {
                $responseApi = json_decode(Service::ReturSupplierJawabDaftarPS($request));
                $statusApi = $responseApi->status;
                $data = $responseApi->data;

                if ($statusApi == 1) {
                    return Response()->json([
                        'status'    => 1,
                        'message'   => 'success',
                        'data'      => $data
                    ], 200);
                } else {
                    return Response()->json([
                        'status'    => 0,
                        'message'   => $responseApi?->message || 'failed',
                        'data'      => $data
                    ], 200);
                }

            } else {

                if (!in_array($request->per_page, [10, 50, 100, 500])) {
                    $request->merge(['per_page' => 10]);
                }

                return view('layouts.retur.supplier.jawab.indexPS',[
                    'title_menu' => 'Supplier Jawab',
                    'title_page' => 'Jawab'
                ]);

            }
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('failed', 'Maaf terjadi kesalahan, silahkan coba lagi');
        }
    }

    public function storePS(Request $request)
    {
        try {
            $rules = [
                'no_ps'       => 'required',
                'tgl_ps'      => 'required',
            ];
            $messages = [
                'no_ps.required' => 'no_ps Tidak Boleh Kososng',
                'tgl_ps.required' => 'tgl_ps Tidak Boleh Kososng'
            ];

            $validate = Validator::make($request->all(), $rules, $messages);

            if ($validate->fails()) {
                return Response()->json([
                    'status'    => 0,
                    'message'   => $validate->errors()->first(),
                    'data'      => ''
                ]);
            }

            $responseApi = json_decode(Service::ReturSupplierjawabSimpanPS($request));
            $statusApi = $responseApi?->status ?? 0;

            if ($statusApi == 1) {
                return Response()->json([
                    'status'    => 1,
                    'message'   => 'Data berhasil disimpan',
                    'data'      => true
                ], 200);
            } else {
                return Response()->json([
                    'status'    => 0,
                    'message'   => ($responseApi?->message ?? 'Gagal menyimpan data'),
                    'data'      => false
                ], 200);
            }
        } catch (\Throwable $th) {
            return Response()->json([
                'status'    => 0,
                'message'   => 'Maaf terjadi kesalahan, silahkan coba lagi',
                'data'      => ''
            ], 200);
        }
    }

    public function storePSDetail(Request $request){
        try {
            $rules = [
                'no_retur'  => 'required',
                'jml'       => 'required',
                'alasan'    => 'required|in:RETUR,CA',
                'keputusan' => 'required|in:TERIMA,TOLAK',
                // 'ket'       => 'required',
            ];

            $messages = [
                'no_retur.required'     => 'No Retur Tidak Boleh Kososng',
                'jml.required'          => 'Qty Ganti Tidak Boleh Kososng',
                'alasan.required'       => 'alasan Tidak Boleh Kososng',
                'alasan.in'             => 'alasan Tidak Valid',
                'keputusan.required'    => 'keputusan Tidak Boleh Kososng',
                'keputusan.in'          => 'keputusan Tidak Valid',
                // 'ket.required'          => 'keterangan Tidak Boleh Kososng',
            ];

            if ($request->alasan == 'CA') {
                $rules += ['ca' => 'required'];
                $messages += ['ca.required'  => 'Jumlah Uang Tidak Boleh Kososng'];
            }

            $validate = Validator::make($request->all(), $rules, $messages);

            if ($validate->fails()) {
                return Response()->json([
                    'status'    => 0,
                    'message'   => $validate->errors()->first(),
                    'data'      => ''
                ]);
            }

            // return Service::ReturSupplierjawabSimpanPSDetail($request);
            $responseApi = json_decode(Service::ReturSupplierjawabSimpanPSDetail($request));
            $statusApi = $responseApi?->status ?? 0;

            if ($statusApi == 1) {
                return Response()->json([
                    'status'    => 1,
                    'message'   => 'Data berhasil disimpan',
                    'data'      => true
                ], 200);
            } else {
                return Response()->json([
                    'status'    => 0,
                    'message'   => ($responseApi?->message ?? 'Gagal menyimpan data'),
                    'data'      => false
                ], 200);
            }

        } catch (\Throwable $th) {
            return Response()->json([
                'status'    => 0,
                'message'   => 'Maaf terjadi kesalahan, silahkan coba lagi',
                'data'      => ''
            ], 200);
        }
    }

    public function store(Request $request){
        try {
            $responseApi = json_decode(Service::ReturSupplierjawabPSSimpan($request));
            $statusApi = $responseApi?->status ?? 0;

            if ($statusApi == 1) {
                return Response()->json([
                    'status'    => 1,
                    'message'   => 'Data berhasil disimpan',
                    'data'      => $responseApi->data ?? true
                ], 200);
            } else {
                return Response()->json([
                    'status'    => 0,
                    'message'   => ($responseApi?->message ?? 'Gagal menyimpan data'),
                    'data'      => false
                ], 200);
            }
        } catch (\Throwable $th) {
            return Response()->json([
                'status'    => 0,
                'message'   => 'Maaf terjadi kesalahan, silahkan coba lagi',
                'data'      => ''
            ], 200);
        }
    }

    public function destroyPS(Request $request)
    {
        try {
            $rules = [
                'no_ps' => 'required',
                'tgl_ps' => 'required',
                'kd_part' => 'required',
                'qty_jwb' => 'required',
            ];
            $messages = [
                'no_ps.required' => 'Maaf Terjadi kesalahan, silahkan coba lagi',
                'tgl_ps.required' => 'Maaf Terjadi kesalahan, silahkan coba lagi',
                'kd_part.required' => 'Maaf Terjadi kesalahan, silahkan coba lagi',
                'qty_jwb.required' => 'Maaf Terjadi kesalahan, silahkan coba lagi',
            ];

            $validate = Validator::make($request->all(), $rules, $messages);
            if ($validate->fails()) {
                return Response()->json([
                    'status'    => 2,
                    'message'   => $validate->errors()->first(),
                    'data'      => false
                ]);
            }
            $responseApi = json_decode(Service::ReturSupplierJwbPSDelete($request));
            $statusApi = $responseApi->status ?? 0;

            if ($statusApi == 1) {
                return Response()->json([
                    'status'    => 1,
                    'message'   => 'Data berhasil dihapus',
                    'data'      => true
                ], 200);
            } else {
                return Response()->json([
                    'status'    => 0,
                    'message'   => ($responseApi?->message ?? 'Gagal menyimpan data'),
                    'data'      => false
                ], 200);
            }

        } catch (\Throwable $th) {
            return Response()->json([
                'status'    => 0,
                'message'   => 'Maaf terjadi kesalahan, silahkan coba lagi',
                'data'      => false
            ], 200);
        }
    }

    public function destroyPSDetail(Request $request)
    {
        try {
            $rules = [
                'no_ps' => 'required',
                'tgl_ps' => 'required',
                'kd_part' => 'required',
                'qty_jwb' => 'required',
                'no_retur' => 'required',
                'no_klaim' => 'required',
            ];
            $messages = [
                'no_ps.required' => 'Maaf Terjadi kesalahan, silahkan coba lagi',
                'tgl_ps.required' => 'Maaf Terjadi kesalahan, silahkan coba lagi',
                'kd_part.required' => 'Maaf Terjadi kesalahan, silahkan coba lagi',
                'qty_jwb.required' => 'Maaf Terjadi kesalahan, silahkan coba lagi',
                'no_retur.required' => 'Maaf Terjadi kesalahan, silahkan coba lagi',
                'no_klaim.required' => 'Maaf Terjadi kesalahan, silahkan coba lagi',
            ];

            $validate = Validator::make($request->all(), $rules, $messages);
            if ($validate->fails()) {
                return Response()->json([
                    'status'    => 2,
                    'message'   => $validate->errors()->first(),
                    'data'      => false
                ]);
            }

            $responseApi = json_decode(Service::ReturSupplierJwbPSDetailDelete($request));
            $statusApi = $responseApi->status ?? 0;

            if ($statusApi == 1) {
                return Response()->json([
                    'status'    => 1,
                    'message'   => 'Data berhasil dihapus',
                    'data'      => true
                ], 200);
            } else {
                return Response()->json([
                    'status'    => 0,
                    'message'   => $responseApi->message??'Terjadi kesalahan, silahkan cek jika data masih ada maka belum terhapus',
                    'data'      => false
                ], 200);
            }

        } catch (\Throwable $th) {
            return Response()->json([
                'status'    => 0,
                'message'   => 'Maaf terjadi kesalahan, silahkan coba lagi',
                'data'      => false
            ], 200);
        }
    }
}
