<?php

namespace App\Http\Controllers\Api\Backend\Orders;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;


class ApiPembayaranFakturController extends Controller
{
    public function daftarPembayaranFaktur(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'status_pembayaran' => 'required|string',
                'user_id'       => 'required|string',
                'role_id'       => 'required|string',
                'companyid'     => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Pilih status pembayaran terlebih dahulu");
            }

            if (strtoupper(trim($request->get('status_pembayaran'))) == 'LUNAS') {
                $validate = Validator::make($request->all(), [
                    'year'          => 'required|string',
                    'month'         => 'required|string',
                ]);

                if ($validate->fails()) {
                    return Response::responseWarning("Pilih bulan dan tahun faktur terlebih dahulu");
                }
            } else {
                if(strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                    $validate = Validator::make($request->all(), [
                        'kode_sales'    => 'required|string',
                        'kode_dealer'   => 'required|string',
                    ]);

                    if ($validate->fails()) {
                        return Response::responseWarning("Pilih kode salesman dan dealer terlebih dahulu");
                    }
                } else {
                    if(strtoupper(trim($request->get('status_pembayaran'))) != 'LUNAS') {
                        if(empty($request->get('kode_sales')) || $request->get('kode_sales') == '') {
                            if(empty($request->get('kode_dealer')) || $request->get('kode_dealer') == '') {
                                return Response::responseWarning("Kode salesman atau kode dealer harus terisi");
                            }
                        }
                    }
                }
            }

            $sql = DB::table('faktur')->lock('with (nolock)')
                    ->selectRaw("isnull(faktur.no_faktur, '') as nomor_faktur")
                    ->leftJoin(DB::raw('dealer with (nolock)'), function ($join) {
                        $join->on('dealer.kd_dealer', '=', 'faktur.kd_sales')
                            ->on('dealer.companyid', '=', 'faktur.companyid');
                    })
                    ->leftJoin(DB::raw('salesman with (nolock)'), function ($join) {
                        $join->on('salesman.kd_sales', '=', 'faktur.kd_sales')
                            ->on('salesman.companyid', '=', 'faktur.companyid');
                    })
                    ->leftJoin(DB::raw('superspv with (nolock)'), function ($join) {
                        $join->on('superspv.kd_spv', '=', 'salesman.spv')
                            ->on('superspv.companyid', '=', 'faktur.companyid');
                    })
                    ->where('faktur.companyid', $request->get('companyid'));

            if (strtoupper(trim($request->get('status_pembayaran'))) == 'LUNAS') {
                $sql->whereYear('faktur.tgl_faktur', $request->get('year'))
                    ->whereMonth('faktur.tgl_faktur', $request->get('month'))
                    ->whereRaw("isnull(faktur.total, 0) <= isnull(faktur.terbayar, 0)")
                    ->orderBy('faktur.no_faktur', 'asc');
            } else {
                $sql->whereRaw("isnull(faktur.total, 0) > isnull(faktur.terbayar, 0)")
                    ->orderByRaw("dateadd(day, isnull(dealer.jtp, 0),
                        dateadd(day, isnull(faktur.umur_faktur, 0),
                            cast(iif(isnull(faktur.tgl_sj, '')='', dateadd(day, 4, faktur.tgl_faktur), faktur.tgl_sj) as date))) asc,
                        faktur.no_faktur asc");
            }

            if (strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                $sql->where('faktur.kd_dealer', $request->get('user_id'));
            } elseif (strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql->where('faktur.kd_sales', $request->get('user_id'));
            } elseif (strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql->where('superspv.nm_spv', $request->get('user_id'));
            }

            if (!empty($request->get('kode_sales'))) {
                $sql->where('faktur.kd_sales', $request->get('kode_sales'));
            }

            if (!empty($request->get('kode_dealer'))) {
                $sql->where('faktur.kd_dealer', $request->get('kode_dealer'));
            }

            if (!empty($request->get('nomor_faktur'))) {
                $sql->where('faktur.no_faktur', 'like', $request->get('nomor_faktur') . '%');
            }

            $sql = $sql->paginate($request->get('per_page') ?? 10);

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

            $jumlah_data = 0;
            $data_no_faktur = '';
            $data_faktur = new Collection();

            foreach ($data as $record) {
                $jumlah_data = (double)$jumlah_data + 1;

                if (trim($data_no_faktur) == '') {
                    $data_no_faktur .= "'".trim($record->nomor_faktur)."'";
                } else {
                    $data_no_faktur .= ",'".trim($record->nomor_faktur)."'";
                }
            }

            if((double)$jumlah_data > 0) {
                $sql = "select	isnull(faktur.companyid, '') as companyid, left(faktur.no_faktur, 7) as urut_faktur,
                                isnull(faktur.no_faktur, '') as nomor_faktur, isnull(faktur.tgl_faktur, '') as tanggal_faktur,
                                isnull(faktur.kd_sales, '') as kode_sales, isnull(faktur.kd_dealer, '') as kode_dealer,
                                isnull(faktur.tgl_jtp, '') as tanggal_jtp, isnull(faktur.total, 0) as total_faktur,
                                isnull(faktur.ket, '') as keterangan_faktur, isnull(pembayaran.jml_bayar, 0) as total_pembayaran,
                                iif(isnull(faktur.total, 0) > isnull(pembayaran.jml_bayar, 0), 'BELUM_LUNAS', 'LUNAS') as status,
                                iif(isnull(faktur.total, 0) > isnull(pembayaran.jml_bayar, 0), 'Belum Lunas', 'Lunas') as keterangan,
                                iif(datediff(day, faktur.tgl_jtp, getdate()) > 0, 'LEBIH', 'KURANG') as status_sisa_hari,
                                iif(datediff(day, faktur.tgl_jtp, getdate()) > 0,
                                datediff(day, faktur.tgl_jtp, getdate()),
                                    datediff(day, getdate(), faktur.tgl_jtp)) as sisa_hari
                        from
                        (
                            select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur,
                                    faktur.kd_sales, faktur.kd_dealer, faktur.total, faktur.ket,
                                    dateadd(day, isnull(dealer.jtp, 0),
                                        dateadd(day, isnull(faktur.jtp_khusus, 0),
                                            dateadd(day, isnull(faktur.umur_faktur, 0),
                                                cast(iif(isnull(faktur.tgl_sj, '')='', dateadd(day, 4, faktur.tgl_faktur), faktur.tgl_sj) as date)))) as tgl_jtp_spelling,
                                    dateadd(day, isnull(dealer.jtp, 0),
                                        dateadd(day, isnull(faktur.umur_faktur, 0),
                                            cast(iif(isnull(faktur.tgl_sj, '')='', dateadd(day, 4, faktur.tgl_faktur), faktur.tgl_sj) as date))) as tgl_jtp
                            from
                            (
                                select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur,
                                        faktur.kd_sales, faktur.kd_dealer, faktur.total,
                                        faktur.jtp_khusus, faktur.umur_faktur, faktur.tgl_sj,
                                        faktur.ket
                                from	faktur with (nolock)
                                where	faktur.companyid=? and
                                        faktur.no_faktur in (".$data_no_faktur.")
                            )	faktur
                                    left join dealer with (nolock) on faktur.kd_dealer=dealer.kd_dealer and
                                                faktur.companyid=dealer.companyid
                                    left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and
                                                faktur.companyid=salesman.companyid
                        )	faktur
                        left join
                        (
                            select	faktur.companyid, faktur.no_faktur,
                                    sum(terimadtl.jumlah) as jml_bayar
                            from
                            (
                                select	faktur.companyid, faktur.no_faktur
                                from	faktur with (nolock)
                                where	faktur.companyid=? and
                                        faktur.no_faktur in (".$data_no_faktur.")
                            )	faktur
                                    left join terimadtl with (nolock) on faktur.no_faktur=terimadtl.no_faktur and
                                                faktur.companyid=terimadtl.companyid
                            where isnull(terimadtl.jumlah, 0) > 0
                            group by faktur.companyid, faktur.no_faktur
                        )	pembayaran on faktur.no_faktur=pembayaran.no_faktur and faktur.companyid=pembayaran.companyid";

                if(strtoupper(trim($request->get('status_pembayaran'))) == 'LUNAS') {
                    $sql .= " order by faktur.no_faktur asc";
                } else {
                    $sql .= " order by faktur.tgl_jtp asc, faktur.no_faktur asc";
                }

                $result = DB::select($sql, [ $request->get('companyid'), $request->get('companyid') ]);

                foreach($result as $data) {
                    $data_faktur->push((object) [
                        'companyid'         => strtoupper(trim($data->companyid)),
                        'urut_faktur'       => strtoupper(trim($data->urut_faktur)),
                        'nomor_faktur'      => strtoupper(trim($data->nomor_faktur)),
                        'tanggal_faktur'    => strtoupper(trim($data->tanggal_faktur)),
                        'keterangan_faktur' => trim($data->keterangan_faktur),
                        'kode_sales'        => strtoupper(trim($data->kode_sales)),
                        'kode_dealer'       => strtoupper(trim($data->kode_dealer)),
                        'tanggal_jtp'       => strtoupper(trim($data->tanggal_jtp)),
                        'total_faktur'      => (double)$data->total_faktur,
                        'total_pembayaran'  => (double)$data->total_pembayaran,
                        'status'            => strtoupper(trim($data->status)),
                        'keterangan'        => strtoupper(trim($data->keterangan)),
                        'status_sisa_hari'  => strtoupper(trim($data->status_sisa_hari)),
                        'sisa_hari'         => (double)$data->sisa_hari,
                    ]);
                }
            }

            $data_pembayaran = [
                'current_page'  => $current_page,
                'data'          => $data_faktur,
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

            return Response::responseSuccess('success', $data_pembayaran);
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

    public function detailPembayaranPerFaktur(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_faktur'  => 'required|string',
                'user_id'       => 'required|string',
                'role_id'       => 'required|string',
                'companyid'     => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Pilih nomor faktur terlebih dahulu");
            }

            $sql = "select	isnull(faktur.companyid, '') as companyid, isnull(faktur.no_faktur, '') as nomor_faktur,
                            isnull(faktur.tgl_faktur, '') as tanggal_faktur, isnull(faktur.kd_sales, '') as kode_sales,
                            isnull(salesman.nm_sales, '') as nama_sales, isnull(faktur.kd_dealer, '') as kode_dealer,
                            isnull(dealer.nm_dealer, '') as nama_dealer, isnull(faktur.total, 0) as total_faktur,
                            isnull(terima.no_bpk, '') as nomor_bpk, isnull(terima.tanggal, '') as tanggal_bpk,
                            isnull(terima.tunai_giro, '') as tunai_giro, isnull(terima.tgl_reg, '') as tanggal_register,
                            isnull(terima.tgl_real, '') as tanggal_realisasi, isnull(terima.nm_bank, '') as nama_bank,
                            isnull(terima.no_giro, '') as nomor_giro, isnull(terima.jt_tempo, '') as tanggal_jtp_giro,
                            isnull(terima.realisasi, 0) as status_realisasi, isnull(terimadtl.jumlah, 0) as jumlah_pembayaran,
                            isnull(faktur.ket, '') as keterangan
                    from
                    (
                        select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur, faktur.kd_sales,
                                faktur.kd_dealer, faktur.ket, faktur.total
                        from	faktur with (nolock)
                        where	faktur.no_faktur=? and faktur.companyid=?
                    )	faktur
                            left join dealer with (nolock) on faktur.kd_dealer=dealer.kd_dealer and
                                        faktur.companyid=dealer.companyid
                            left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and
                                        faktur.companyid=salesman.companyid
                            left join superspv with (nolock) on salesman.spv=superspv.kd_spv and
                                        faktur.companyid=superspv.companyid
                            left join terimadtl with (nolock) on faktur.no_faktur=terimadtl.no_faktur and
                                        faktur.companyid=terimadtl.companyid
                            left join terima with (nolock) on terimadtl.no_bpk=terima.no_bpk and
                                        faktur.companyid=terima.companyid";

            if (strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                $sql .= " where faktur.kd_dealer='".$request->get('user_id')."'";
            } elseif (strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql .= " where faktur.kd_sales='".$request->get('user_id')."'";
            } elseif (strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql .= " where superspv.nm_spv='".$request->get('user_id')."'";
            }

            $sql .= " order by terima.no_bpk asc";

            $result = DB::select($sql, [ $request->get('nomor_faktur'), $request->get('companyid') ]);


            $data_pembayaran = new Collection();
            $data_detail = [];

            $nomor_faktur = 'XX00000/X/XX';
            $tanggal_faktur = date('Y-m-d');
            $kode_sales = 'GXX';
            $nama_sales = 'XXX';
            $kode_dealer = 'XXX';
            $nama_dealer = 'XXX';
            $keterangan = 'XXX';
            $total_faktur = 0;
            $total_pembayaran = 0;
            $jumlah_data = 0;

            foreach ($result as $data) {
                $jumlah_data = (double)$jumlah_data + 1;

                $nomor_faktur = strtoupper(trim($data->nomor_faktur));
                $tanggal_faktur = trim($data->tanggal_faktur);
                $kode_sales = strtoupper(trim($data->kode_sales));
                $nama_sales = strtoupper(trim($data->nama_sales));
                $kode_dealer = strtoupper(trim($data->kode_dealer));
                $nama_dealer = strtoupper(trim($data->nama_dealer));
                $keterangan = trim($data->keterangan);
                $total_faktur = (float)$data->total_faktur;
                $total_pembayaran = (float)$total_pembayaran + (float)$data->jumlah_pembayaran;

                $data_detail[] = [
                    'nomor_bpk'         => strtoupper(trim($data->nomor_bpk)),
                    'tanggal_input'     => trim($data->tanggal_bpk),
                    'tunai_giro'        => strtoupper(trim($data->tunai_giro)),
                    'tanggal_register'  => trim($data->tanggal_register),
                    'tanggal_realisasi' => trim($data->tanggal_realisasi),
                    'nama_bank'         => strtoupper(trim($data->nama_bank)),
                    'nomor_giro'        => strtoupper(trim($data->nomor_giro)),
                    'tanggal_jtp_giro'  => trim($data->tanggal_jtp_giro),
                    'status_realisasi'  => (int)$data->status_realisasi,
                    'jumlah_pembayaran' => (float)$data->jumlah_pembayaran,
                ];
            }

            $data_pembayaran->push((object) [
                'nomor_faktur'      => $nomor_faktur,
                'tanggal_faktur'    => $tanggal_faktur,
                'kode_sales'        => $kode_sales,
                'nama_sales'        => $nama_sales,
                'kode_dealer'       => $kode_dealer,
                'nama_dealer'       => $nama_dealer,
                'keterangan'        => $keterangan,
                'total_faktur'      => (float)$total_faktur,
                'total_pembayaran'  => (float)$total_pembayaran,
                'detail_pembayaran' => $data_detail
            ]);

            if ((double)$jumlah_data > 0) {
                return Response::responseSuccess('success', $data_pembayaran->first());
            } else {
                return Response::responseWarning('Data tidak ditemukan');
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

    public function detailPembayaranPerBpk(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_bpk'     => 'required|string',
                'user_id'       => 'required|string',
                'role_id'       => 'required|string',
                'companyid'     => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Pilih nomor bukti penerimaan kas terlebih dahulu");
            }

            $sql = DB::table('terima')->lock('with (nolock)')
                ->selectRaw("isnull(terima.companyid, '') as companyid, isnull(terima.no_bpk, '') as nomor_bpk,
                                isnull(terima.tanggal, '') as tanggal_bpk, isnull(terima.kd_dealer, '') as kode_dealer,
                                isnull(dealer.nm_dealer, '') as nama_dealer, isnull(faktur.kd_sales, '') as kode_sales,
                                isnull(salesman.nm_sales, '') as nama_sales, isnull(terima.tunai_giro, '') as tunai_giro,
                                isnull(terima.no_giro, '') as nomor_giro, isnull(terima.jt_tempo, '') as tanggal_jtp_giro,
                                isnull(terima.acc_bank, '') as account_bank, isnull(terima.nm_bank, '') as nama_bank,
                                isnull(terima.total, 0) as total_pembayaran, isnull(terimadtl.no_faktur, '') as nomor_faktur,
                                isnull(terimadtl.tgl_faktur, '') as tanggal_faktur, isnull(faktur.total, 0) as total_faktur,
                                isnull(terimadtl.jumlah, 0) as jumlah_pembayaran_faktur, isnull(terima.realisasi, 0) as status_realisasi")
                ->leftJoin(DB::raw('dealer with (nolock)'), function ($join) {
                    $join->on('dealer.kd_dealer', '=', 'terima.kd_dealer')
                        ->on('dealer.companyid', '=', 'terima.companyid');
                })
                ->leftJoin(DB::raw('terimadtl with (nolock)'), function ($join) {
                    $join->on('terimadtl.no_bpk', '=', 'terima.no_bpk')
                        ->on('terimadtl.companyid', '=', 'terima.companyid');
                })
                ->leftJoin(DB::raw('faktur with (nolock)'), function ($join) {
                    $join->on('faktur.no_faktur', '=', 'terimadtl.no_faktur')
                        ->on('faktur.companyid', '=', 'terima.companyid');
                })
                ->leftJoin(DB::raw('salesman with (nolock)'), function ($join) {
                    $join->on('salesman.kd_sales', '=', 'faktur.kd_sales')
                        ->on('salesman.companyid', '=', 'terima.companyid');
                })
                ->leftJoin(DB::raw('superspv with (nolock)'), function ($join) {
                    $join->on('superspv.kd_spv', '=', 'salesman.spv')
                        ->on('superspv.companyid', '=', 'terima.companyid');
                })
                ->where('terima.no_bpk', $request->get('nomor_bpk'))
                ->where('terima.companyid', $request->get('companyid'));

            if (strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                $sql->where('terima.kd_dealer', $request->get('user_id'));
            } elseif (strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql->where('faktur.kd_sales', $request->get('user_id'));
            } elseif (strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql->where('superspv.nm_spv', $request->get('user_id'));
            }
            $result = $sql->get();

            $data_pembayaran = new Collection();
            $data_detail = [];

            $nomor_bpk = 'XX00000/X/XX';
            $tanggal_input = date('Y-m-d');
            $kode_sales = 'GXX';
            $nama_sales = 'XXX';
            $kode_dealer = 'XXX';
            $nama_dealer = 'XXX';
            $tunai_giro = 'XXX';
            $nomor_giro = 'XXX';
            $tanggal_jtp_giro = date('Y-m-d');
            $account_bank = 'XXX';
            $nama_bank = 'XXX';
            $status_realisasi = 0;
            $total_pembayaran = 0;
            $total_data = 0;

            foreach ($result as $data) {
                $nomor_bpk = strtoupper(trim($data->nomor_bpk));
                $tanggal_input = trim($data->tanggal_bpk);
                $kode_sales = strtoupper(trim($data->kode_sales));
                $nama_sales = strtoupper(trim($data->nama_sales));
                $kode_dealer = strtoupper(trim($data->kode_dealer));
                $nama_dealer = strtoupper(trim($data->nama_dealer));
                $tunai_giro = strtoupper(trim($data->tunai_giro));
                $nomor_giro = strtoupper(trim($data->nomor_giro));
                $tanggal_jtp_giro = strtoupper(trim($data->tanggal_jtp_giro));
                $account_bank = strtoupper(trim($data->account_bank));
                $nama_bank = strtoupper(trim($data->nama_bank));
                $status_realisasi = (int)$data->status_realisasi;
                $total_pembayaran = (float)$data->total_pembayaran;
                $total_data = $total_data + 1;

                $data_detail[] = [
                    'nomor_faktur'      => strtoupper(trim($data->nomor_faktur)),
                    'tanggal_faktur'    => trim($data->tanggal_faktur),
                    'total_faktur'      => (float)$data->total_faktur,
                    'total_pembayaran_faktur' => (float)$data->jumlah_pembayaran_faktur,
                ];
            }

            $data_pembayaran->push((object) [
                'nomor_bukti'       => $nomor_bpk,
                'tanggal_input'     => $tanggal_input,
                'kode_sales'        => $kode_sales,
                'nama_sales'        => $nama_sales,
                'kode_dealer'       => $kode_dealer,
                'nama_dealer'       => $nama_dealer,
                'tunai_giro'        => $tunai_giro,
                'nomor_giro'        => $nomor_giro,
                'tanggal_jtp_giro'  => $tanggal_jtp_giro,
                'account_bank'      => $account_bank,
                'nama_bank'         => $nama_bank,
                'status_realisasi'  => (int)$status_realisasi,
                'total_pembayaran'  => (float)$total_pembayaran,
                'detail_pembayaran' => $data_detail
            ]);

            if ($total_data > 0) {
                return Response::responseSuccess('success', $data_pembayaran->first());
            } else {
                return Response::responseWarning('Data tidak ditemukan');
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
