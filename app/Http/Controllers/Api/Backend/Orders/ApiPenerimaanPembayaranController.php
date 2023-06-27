<?php

namespace App\Http\Controllers\Api\Backend\Orders;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class ApiPenerimaanPembayaranController extends Controller
{
    public function pembayaranDealerDaftar(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'dealer'    => 'required|string',
                'cabang'    => 'required|string',
            ], [
                'dealer.required'    => 'Isi terlebih dahulu Dealer',
                'cabang.required'       => 'Terjadi kesalahan pada Sistem',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            $cart_collector_dtl = DB::table('cart_collector_dtl')
                ->select('cart_collector_dtl.no_faktur', 'cart_collector_dtl.status', DB::raw('SUM(cart_collector_dtl.jumlah) as jumlah'))
                ->groupBy('cart_collector_dtl.no_faktur', 'cart_collector_dtl.status');

            $data = DB::table('faktur')->lock('with (nolock)')
                ->leftJoinSub($cart_collector_dtl, 'cart_collector_dtl', function ($join) {
                    $join->on('cart_collector_dtl.no_faktur', '=', 'faktur.no_faktur');
                })
                ->select(
                    'faktur.no_faktur',
                    'faktur.tgl_faktur',
                    DB::raw('ISNULL(faktur.total,0) as jumlah'),
                    DB::raw('ISNULL(faktur.terbayar,0) + (CASE WHEN cart_collector_dtl.status = 0 THEN cart_collector_dtl.jumlah ELSE 0 END) as terbayar'),
                    DB::raw('ISNULL(faktur.total,0) - ISNULL(faktur.terbayar,0) - (CASE WHEN cart_collector_dtl.status = 0 THEN cart_collector_dtl.jumlah ELSE 0 END)  as sisa'),
                    'faktur.kd_dealer',
                    'faktur.kd_mkr'
                )
                ->where('faktur.CompanyId', strtoupper(trim($request->cabang)))
                ->where('faktur.kd_dealer', strtoupper(trim($request->dealer)))
                ->whereRaw('isnull(faktur.total, 0) > (isnull(faktur.terbayar, 0) + (CASE WHEN cart_collector_dtl.status = 0 THEN cart_collector_dtl.jumlah ELSE 0 END))')
                ->orderBy('faktur.tgl_faktur', 'asc')
                ->get();

            return Response::responseSuccess('success', $data);
        } catch (\Exception $exception) {
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $exception->getMessage(),
                $request->get('companyid')
            );
        }
    }

    public function simpanPembayaran(Request $request)
    {
        // dd($request->all());
        try {
            $validate = Validator::make($request->all(), [
                'kd_dealer'     => 'required|string',
                'cabang'        => 'required|string',
                'user_id'        => 'required|string',
                'detail'        => 'required|json'
            ], [
                'kd_dealer.required'    => 'Isi terlebih dahulu Dealer',
                'cabang.required'       => 'Terjadi kesalahan pada Sistem',
                'user_id.required'       => 'Terjadi kesalahan pada Sistem',
                'detail'                => 'Terjadi kesalahan dalam memproses Pembayaran coba lagi'
            ]);

            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            // bulan romawi
            $bulan_romawi = array(
                '01' => 'I',
                '02' => 'II',
                '03' => 'III',
                '04' => 'IV',
                '05' => 'V',
                '06' => 'VI',
                '07' => 'VII',
                '08' => 'VIII',
                '09' => 'IX',
                '10' => 'X',
                '11' => 'XI',
                '12' => 'XII',
            );

            // jenis transaksi
            if ($request->jenis_transaksi == 'T') {
                $kode = 'TN';
                $no_bpk_terakhir = DB::table('number')->where('CompanyId', $request->cabang)->where('jenis', 'TN')->orderBy('nomor', 'desc')->first();
            } else if ($request->jenis_transaksi == 'G') {
                $kode = 'BG';
                $no_bpk_terakhir = DB::table('number')->where('CompanyId', $request->cabang)->where('jenis', 'BG')->orderBy('nomor', 'desc')->first();
            } else {
                return Response::responseWarning('Maaf terdapat kesalahan pada Sistem !');
            }

            $no_bpk_baru = substr($no_bpk_terakhir->nomor ?? '000000', 0, 6);
            // jika nomor bpk sudah mencapai 1000000 maka akan di reset
            if ($no_bpk_baru == '1000000') {
                return Response::responseWarning('Maaf Number BPK sudah penuh hubunggi Pihak IT!');
            } else {
                $no_bpk_baru++;
                $no_bpk_baru = sprintf("%06s", $no_bpk_baru);
                $NO_BPK = $no_bpk_baru . '/' . $kode . '/' . $bulan_romawi[date('m')] . '/' . date('y');
            }
            $usertime = date('Y-m-d=H:i:s') . '=' . $request->user_id;
            DB::beginTransaction();
            try {
                DB::table('number')->insert([
                    'nomor'    => $NO_BPK,
                    'jenis'    => $kode,
                    'pakai'    => 1,
                    'CompanyId' => $request->cabang,
                ]);

                DB::table('cart_collector')->insert([
                    'no_bpk'    => $NO_BPK,
                    'kd_dealer' => $request->kd_dealer,
                    'total'     => str_replace(',', '', $request->total),
                    'tanggal'   => date('Y-m-d'),
                    'bukti_bayar' => json_encode($request->file_names??[]),
                    'usertime'  => $usertime,
                    'CompanyId' => $request->cabang,
                ]);

                $data_detail = array();
                foreach (json_decode($request->detail) as $i => $value) {
                    $data_detail[$i] = array(
                        'no_bpk'        => $NO_BPK,
                        'no_faktur'     => $value->no_faktur,
                        'tgl_faktur'    => date('Y-m-d', strtotime($value->tgl_faktur)),
                        'kd_dealer'     => $value->dealer,
                        'jumlah'        => str_replace(',', '', $value->jumlah),
                        'usertime'      => $usertime,
                        'CompanyId'     => $request->cabang,
                        'status'        => 0
                    );
                }
                DB::table('cart_collector_dtl')->insert($data_detail);

                DB::commit();
                return Response::responseSuccess('Data Berhasil Disimpan', $data_detail);
            } catch (\Exception $exception) {
                DB::rollback();
                return Response::responseWarning("Data Gagal Disimpan");
            }
        } catch (\Exception $exception) {
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $exception->getMessage(),
                $request->get('companyid')
            );
        }
    }
}
