<?php

namespace App\Http\Controllers\Api\Backend\Retur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\Route;

class SupplierJawabPSController extends Controller
{
    public function daftarJawabPS(Request $request)
    {
        try {

            $filter_sql = "
                select header.*,filter.no_retur,filter.no_klaim  from (
                    select
                        jwb_claim.no_ps,
                        jwb_claim.tgl_ps,
                        jwb_claim.kd_part,
                        part.ket as nm_part,
                        jwb_claim.qty_jwb,
                        jwb_claim.ket
                    from jwb_claim
                    inner join part on jwb_claim.kd_part = part.kd_part and jwb_claim.CompanyId = part.CompanyId
                    where jwb_claim.no_retur is null and jwb_claim.CompanyId = '{$request->companyid}'
                ) as header
                left join (
                    select
                        jwb_claim.no_ps,
                        jwb_claim.tgl_ps,
                        jwb_claim.kd_part,
                        jwb_claim.no_retur,
                        jwb_claim.no_klaim
                    from jwb_claim
                    inner join rtoko on jwb_claim.no_klaim = rtoko.no_retur and jwb_claim.CompanyId = rtoko.CompanyId
                    where jwb_claim.CompanyId = '{$request->companyid}'
                        and jwb_claim.no_ps is not null
                        and jwb_claim.no_retur is not null
                        and jwb_claim.no_klaim is not null
                    group by jwb_claim.no_ps,
                            jwb_claim.tgl_ps,
                            jwb_claim.kd_part,
                            jwb_claim.no_retur,
                            jwb_claim.no_klaim,
                            rtoko.kd_dealer
                ) as filter on header.no_ps = filter.no_ps and header.kd_part = filter.kd_part
            ";

            $filter = DB::table(DB::raw("($filter_sql) as filter"));
            if(count($request->tanggal) == 2){
                $filter = $filter
                ->whereBetween('tgl_ps', [$request->tanggal[0], $request->tanggal[1]]);
            } elseif(count($request->tanggal) == 1) {
                $filter = $filter
                ->where('tgl_ps', $request->tanggal[0]);
            } else {
                $filter = $filter
                    ->whereBetween('tgl_ps', [
                        date('Y-m-d', strtotime('-2 months')), // Awal dari bulan 3 bulan yang lalu
                        date('Y-m-d'), // Akhir dari bulan saat ini
                    ]);
            }

            if(!empty($request->search) && !empty($request->search['value']) && in_array($request->search['field'], ['no_ps','kd_part','no_retur','no_klaim','kd_dealer'])) {
                $filter = $filter
                    ->where($request->search['field'], 'like', '%' . $request->search['value'] . '%');
            }
            $filter = $filter
                ->get();

            $header_sql_a = "
                select
                    jwb_claim.no_ps,
                    jwb_claim.tgl_ps,
                    jwb_claim.kd_part,
                    part.ket as nm_part,
                    jwb_claim.qty_jwb,
                    jwb_claim.ket,
                    SUBSTRING(jwb_claim.usertime,CHARINDEX('=', jwb_claim.usertime) + 1, 8) as usertime
                from jwb_claim
                inner join part on jwb_claim.kd_part = part.kd_part and jwb_claim.CompanyId = part.CompanyId
                where jwb_claim.no_retur is null and jwb_claim.CompanyId = '{$request->companyid}'
            ";

            $header = DB::table(DB::raw("($header_sql_a) as header"))
                ->whereIn('header.no_ps', $filter->pluck('no_ps')->filter()->unique()->toArray())
                ->whereIn('header.tgl_ps', $filter->pluck('tgl_ps')->filter()->unique()->toArray())
                ->whereIn('header.kd_part', $filter->pluck('kd_part')->filter()->unique()->toArray())
                ->orderByRaw('header.tgl_ps DESC, header.usertime DESC')
                ->get();

            $detail_sql = "
                select
                    no_ps,
                    tgl_ps,
                    jwb_claim.no_retur,
                    no_klaim,
                    kd_dealer,
                    kd_part,
                    kd_jwb,
                    no_jwb,
                    tgl_jwb,
                    qty_jwb,
                    ca,
                    alasan,
                    keputusan,
                    ket,
                    sts_end
                from jwb_claim
                inner join rtoko on jwb_claim.no_klaim = rtoko.no_retur and jwb_claim.CompanyId = rtoko.CompanyId
                where jwb_claim.CompanyId = '{$request->companyid}' and jwb_claim.no_retur is not null and no_klaim is not null
            ";

            $detail = (object)DB::table(DB::raw("($detail_sql) as detail"))
                ->orderByRaw('detail.tgl_jwb DESC')
                ->get();

            $header = collect($header)->filter(function ($item) use ($detail) {
                $a = $detail->where('no_ps', $item->no_ps)->where('tgl_ps', $item->tgl_ps)->where('kd_part', $item->kd_part);
                if ($detail->isNotEmpty()) {
                    $item->list_no_retur = [... $a->pluck('no_retur')->filter()->unique()->toArray()];
                    $item->list_no_klaim = [... $a->pluck('no_klaim')->filter()->unique()->toArray()];
                    $item->list_kd_dealer = [... $a->pluck('kd_dealer')->filter()->unique()->toArray()];
                    $item->qty_terpakai = $a->sum('qty_jwb');
                    if ($a->count() > 0){
                        $item->jawab = [... $a->toArray()];
                    } else {
                        $item->jawab = [];
                    }
                } else {
                    $item->list_no_retur = [];
                    $item->list_no_klaim = [];
                    $item->list_kd_dealer = [];
                    $item->jawab = [];
                }

                return $item;
            });

            return response::responseSuccess('success', $header);
        } catch (\Exception $exception) {
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $exception->getMessage(),
                $request->get('companyid')
            );
        };
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
                return Response::responseWarning($validate->errors()->first());
            }

            $simpan = DB::transaction(function () use ($request) {
                $part_exists = DB::table('part')
                    ->where('kd_part', $request->kd_part)
                    ->where('CompanyId', $request->companyid)
                    ->exists();

                if (!$part_exists) {
                    return (object)[
                        'status'    => 0,
                        'message'   => 'Part Number tidak ditemukan'
                    ];
                }

                $no_ps_exists = DB::table('jwb_claim')
                    ->where('no_ps', $request->no_ps)
                    ->where('CompanyId', $request->companyid)
                    ->where('kd_part', $request->kd_part)
                    ->exists();

                if ($no_ps_exists) {
                    return (object)[
                        'status'    => 0,
                        'message'   => 'Part Number dengan No Packing Sheet sudah ada'
                    ];
                }

                DB::table('jwb_claim')
                    ->insert([
                        'no_ps' => $request->no_ps,
                        'tgl_ps' => $request->tgl_ps,
                        'kd_part' => $request->kd_part,
                        'qty_jwb' => $request->qty,
                        'ket' => $request->ket,
                        'usertime' => date('d-m-Y=H:i:s') . '=' . $request->user_id,
                        'CompanyId' => $request->companyid
                    ]);

                return (object)[
                    'status'    => 1,
                    'message'   => "success"
                ];
            });

            if ($simpan->status == 1) {
                return Response::responseSuccess($simpan->message);
            }

            return Response::responseWarning($simpan->message);
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

    public function storePSDetail(Request $request)
    {
        try {
            $rules = [
                'no_retur'       => 'required',
                'jml'      => 'required|numeric|min:1',
                'alasan' => 'required|in:RETUR,CA',
                'keputusan' => 'required|in:TERIMA,TOLAK',
                // 'ket' => 'required',
            ];
            $messages = [
                'no_retur.required' => 'No Retur Toko Tidak Boleh Kososng',
                'jml.required' => 'Qty Ganti Tidak Boleh Kososng',
                'jml.min' => 'Qty Ganti Minimal 1 item',
                'alasan.required' => 'alasan Tidak Boleh Kososng',
                'alasan.in'  => 'alasan Tidak Valid',
                'keputusan.required' => 'keputusan Tidak Boleh Kososng',
                'keputusan.in'  => 'keputusan Tidak Valid',
                // 'ket.required' => 'keterangan Tidak Boleh Kososng',
            ];

            if ($request->alasan == 'CA') {
                $rules += ['ca' => 'required'];
                $messages += ['ca.required'  => 'Jumlah Uang Tidak Boleh Kososng'];
            }

            $validate = Validator::make($request->all(), $rules, $messages);

            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            $result = DB::transaction(function () use ($request) {
                $result = DB::select('EXEC SP_jwb_claim_PSsimpanTemp ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', array(
                    $request->kd_ps,
                    $request->tgl_ps,
                    $request->no_retur,
                    $request->no_ca,
                    $request->kd_part,
                    (int)$request->jml,
                    (float)$request->ca??0,
                    $request->alasan,
                    $request->keputusan,
                    $request->ket,
                    $request->companyid,
                    $request->get('user_id')
                ));

                return $result;
            });

            if (isset($result[0]) && is_object($result[0])) {
                $message = $result[0]->message;
                if ($message == 'success') {
                    return response::responseSuccess('success');
                } else {
                    return Response::responseWarning($message);
                }
            } else {
                return Response::responseWarning('Gagal menyimpan data');
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

    public function store(Request $request)
    {
        try {
            $rules = [
                'companyid'       => 'required',
                'user_id'      => 'required'
            ];
            $messages = [
                'companyid.required' => 'companyid Tidak Boleh Kososng',
                'user_id.required' => 'user_id Tidak Boleh Kososng',
            ];

            $validate = Validator::make($request->all(), $rules, $messages);

            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            // ! Simpan data yang akan di proses
            $cek_data = DB::table('jwb_claim')
                ->whereNotNull('no_retur')
                ->whereNotNull('no_klaim')
                ->whereRaw("SUBSTRING(usertime, LEN(usertime) - CHARINDEX('=', REVERSE(usertime)) + 2, LEN(usertime)) = ?", [$request->user_id])
                ->where('companyid', $request->companyid)
                ->whereRaw('(ISNULL(sts_end, 0) = 0)')
                ->get();

            if (count($cek_data) == 0) {
                return Response::responseWarning('Anda Belum Mengisikan Data');
            }

            // ! Cek No RETUR (TOKO) berdasarkan data yang akan di proses
            $cekNoReturToko = DB::table('rtoko')
                ->selectRaw('rtoko_dtl.*')
                ->join('rtoko_dtl', function ($join) {
                    $join->on('rtoko.no_retur', '=', 'rtoko_dtl.no_retur');
                    $join->on('rtoko.CompanyId', '=', 'rtoko_dtl.CompanyId');
                })
                ->whereIn('rtoko.no_retur', $cek_data->pluck('no_klaim')->unique()->toArray())
                ->where('rtoko.CompanyId', $request->companyid)
                ->get();

            if (count($cekNoReturToko) == 0) {
                return Response::responseWarning('No RETUR (TOKO) Pada Data Anda Tidak Ditemukan');
            }

            // ! Membandingkan No RETUR (TOKO) dengan data mana no RETUR (TOKO) yang tidak ada
            $noReturNotValid = array_diff($cekNoReturToko->pluck('no_retur')->unique()->toArray(), $cek_data->pluck('no_klaim')->unique()->toArray());

            if (count($noReturNotValid) > 0) {
                return Response::responseSuccess('No RETUR (TOKO) Pada Data Anda Tidak Valid', [
                    'no_retur' => $noReturNotValid
                ]);
            }

            // ! Cek No RETUR (SUPPLIER) berdasarkan data yang akan di proses
            $cekNoReturSup = DB::table('retur')
                ->selectRaw('retur_dtl.*')
                ->join('retur_dtl', function ($join) {
                    $join->on('retur.no_retur', '=', 'retur_dtl.no_retur');
                    $join->on('retur.CompanyId', '=', 'retur_dtl.CompanyId');
                })
                ->whereIn('retur.no_retur', $cek_data->pluck('no_retur')->unique()->toArray())
                ->where('retur.CompanyId', $request->companyid)
                ->get();

            if (count($cekNoReturSup) == 0) {
                return Response::responseWarning('No RETUR (SUPPLIER) Pada Data Anda Tidak Ditemukan');
            }

            // ! Membandingkan No RETUR (SUPPLIER) dengan data mana no RETUR (SUPPLIER) yang tidak ada
            $noReturNotValid = array_diff($cekNoReturSup->pluck('no_retur')->unique()->toArray(), $cek_data->pluck('no_retur')->unique()->toArray());

            if (count($noReturNotValid) > 0) {
                return Response::responseSuccess('No RETUR (SUPPLIER) Pada Data Anda Tidak Valid', [
                    'no_ca' => $noReturNotValid
                ]);
            }

            // ! Cek No SP berdasarkan data yang akan di proses
            $cekNoSP = DB::table('jwb_claim')
                ->selectRaw('jwb_claim.*')
                ->whereIn('no_ps', $cek_data->pluck('no_ps')->unique()->toArray())
                ->whereIn('tgl_ps', $cek_data->pluck('tgl_ps')->unique()->toArray())
                ->whereIn('kd_part', $cek_data->pluck('kd_part')->unique()->toArray())
                ->where('CompanyId', $request->companyid)
                ->whereNull('no_klaim')
                ->whereNull('no_retur')
                ->get();

            if (count($cekNoSP) == 0) {
                return Response::responseWarning('No SP Pada Data Anda Tidak Ditemukan');
            }

            // ! Membandingkan No SP dengan data mana no SP yang tidak ada
            $noReturNotValid = array_diff($cekNoSP->pluck('no_ps')->unique()->toArray(), $cek_data->pluck('no_ps')->unique()->toArray());

            if (count($noReturNotValid) > 0) {
                return Response::responseSuccess('No SP Pada Data Anda Tidak Valid', [
                    'no_ps' => $noReturNotValid
                ]);
            }

            $simpan = DB::transaction(function () use ($request, $cekNoReturSup) {
                $result = [];
                foreach ($cekNoReturSup->pluck('no_retur')->unique()->toArray() as $key => $value) {
                    $result = DB::select('
                    SET NOCOUNT ON;
                    exec SP_jwb_claim_PSsimpan ?, ?, ?, ?', [
                        $request->user_id,
                        $value,
                        $request->companyid,
                        date('d-m-Y')
                    ]);
                }

                return (object)[
                    'status'    => (int)$result[0]->status,
                    'message'   => $result[0]->message
                ];
            });

            if ($simpan->status == 1) {
                return Response::responseSuccess($simpan->message);
            } else {
                return Response::responseWarning($simpan->message);
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

    public function destroyPS(Request $request){
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

            // ! ------------------------------------
            // ! megecek validasi dan menampilkan pesan error
            // ! ------------------------------------
            $validate = Validator::make($request->all(), $rules, $messages);
            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            $delete = DB::transaction(function () use ($request) {
                $cek = DB::table('jwb_claim')
                ->where('no_ps', $request->no_ps)
                ->where('tgl_ps', $request->tgl_ps)
                ->where('kd_part', $request->kd_part)
                ->where('Companyid', $request->companyid)
                ->whereNotNull('no_retur')
                ->whereNotNull('no_klaim')
                ->get();

                if (count($cek) > 0) {
                    return (object)[
                        'status'    => 1,
                        'message'   => 'Maaf Sudah Terpakai Tidak Bisa di hapus',
                        'data'      => false
                    ];
                }

                DB::table('jwb_claim')
                    ->where('no_ps', $request->no_ps)
                    ->where('tgl_ps', $request->tgl_ps)
                    ->where('kd_part', $request->kd_part)
                    ->where('Companyid', $request->companyid)
                    ->where('qty_jwb', $request->qty_jwb)
                    ->delete();

                return (object)[
                    'status'    => 0,
                    'message'   => 'success',
                    'data'      => true
                ];
            });

            if ($delete->status == 0) {
                return Response::responseSuccess('success' );
            } else {
                return Response::responseWarning($delete->message);
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
        };
    }
    public function destroyPSDetail(Request $request){
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

            // ! ------------------------------------
            // ! megecek validasi dan menampilkan pesan error
            // ! ------------------------------------
            $validate = Validator::make($request->all(), $rules, $messages);
            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            $cek = DB::table('jwb_claim')
                ->selectRaw('no_ps, tgl_ps, kd_part, qty_jwb, no_retur, no_klaim, usertime, sts_end')
                ->where('no_jwb', $request->no_jwb)
                ->where('no_ps', $request->no_ps)
                ->where('tgl_ps', $request->tgl_ps)
                ->where('kd_part', $request->kd_part)
                ->where('qty_jwb', $request->qty_jwb)
                ->where('no_retur', $request->no_retur)
                ->where('no_klaim', $request->no_klaim)
                ->where('Companyid', $request->companyid)
                // ->whereRaw("SUBSTRING(usertime, LEN(usertime) - CHARINDEX('=', REVERSE(usertime)) + 2, LEN(usertime)) = ?", [$request->user_id])
                ->first();

            if (!$cek) {
                return Response::responseWarning('Maaf Terjadi kesalahan, silahkan coba lagi');
            }

            if ($cek->sts_end == 1) {
                return Response::responseWarning('Maaf Sudah Tidak Bisa di hapus');
            }

            if (explode('=', $cek->usertime)[2] != $request->user_id) {
                return Response::responseWarning('Maaf, tidak dapat dihapus. Hanya pengguna yang bersangkutan, yaitu USER: <b>' . explode('=', $cek->usertime)[2] . '</b>, yang dapat melakukan penghapusan.');
            }

            DB::transaction(function () use ($request) {
                DB::table('jwb_claim')
                    ->whereRaw("SUBSTRING(usertime, LEN(usertime) - CHARINDEX('=', REVERSE(usertime)) + 2, LEN(usertime)) = ?", [$request->user_id])
                    ->where('no_jwb', $request->no_jwb)
                    ->where('no_ps', $request->no_ps)
                    ->where('tgl_ps', $request->tgl_ps)
                    ->where('kd_part', $request->kd_part)
                    ->where('qty_jwb', $request->qty_jwb)
                    ->where('no_retur', $request->no_retur)
                    ->where('no_klaim', $request->no_klaim)
                    ->where('Companyid', $request->companyid)
                    ->whereRaw("ISNULL(sts_end, 0) = 0")
                    ->delete();
            });

            return response::responseSuccess('success' );
        } catch (\Exception $exception) {
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $exception->getMessage(),
                $request->get('companyid')
            );
        };
    }
}
