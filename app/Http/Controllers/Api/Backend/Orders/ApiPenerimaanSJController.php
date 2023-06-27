<?php

namespace App\Http\Controllers\Api\Backend\Orders;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;


class ApiPenerimaanSJController extends Controller
{
    public function reportPenerimaanSJ(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'start_date'    => 'required|string',
                'end_date'      => 'required|string',
                'companyid'     => 'required|string'
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Isi data tanggal terlebih dahulu");
            }

            $sql = "select	isnull(sj.companyid, '') as companyid, isnull(sj.ditrm, 0) as status_terima,
                            isnull(convert(varchar(10), sj.tgl_trm, 120), '') as tanggal_terima, isnull(sj.jam_trm, '') as jam_terima,
                            isnull(sj.no_sj, '') as nomor_sj, isnull(convert(varchar(10), sj.tgl, 120), '') as tanggal_sj,
                            isnull(sj.kd_dealer, '') as kode_dealer, isnull(dealer.nm_dealersj, '') as nama_dealer,
                            isnull(dealer.alamat1sj, '') as alamat, isnull(dealer.kotasj, '') as kota,
                            isnull(serah_dtl.no_dok, '') as no_serah_terima, isnull(serah.kd_sopir, '') as kode_sopir,
                            isnull(serah.sopir, '') as nama_sopir
                    from
                    (
                        select	sj.companyid, sj.no_sj, sj.tgl, sj.jam, sj.kd_dealer,
                                sj.ditrm, sj.tgl_trm, sj.jam_trm
                        from	sj
                        where	sj.tgl_trm between ? and ? and sj.companyid=?
                    )	sj
                            left join dealer with (nolock) on sj.kd_dealer=dealer.kd_dealer and
                                    sj.companyid=dealer.companyid
                            left join serah_dtl with (nolock) on sj.no_sj=serah_dtl.no_sj and
                                    sj.companyid=serah_dtl.companyid
                            left join serah with (nolock) on serah_dtl.no_dok=serah.no_dok and
                                    sj.companyid=serah.companyid
                    where   sj.companyid is not null";

            if (!empty($request->get('driver')) && strtoupper(trim($request->get('driver'))) != '') {
                $sql .= " and serah.kd_sopir='" . strtoupper(trim($request->get('driver'))) . "'";
            }

            if (!empty($request->get('no_serah_terima')) && strtoupper(trim($request->get('no_serah_terima'))) != '') {
                $sql .= " and serah.no_dok='" . strtoupper(trim($request->get('no_serah_terima'))) . "'";
            }

            $sql .= " order by serah.no_dok asc, sj.tgl_trm asc, sj.jam_trm asc, sj.no_sj asc";

            $result = DB::select($sql, [$request->get('start_date'), $request->get('end_date'), $request->get('companyid')]);
            $data_penerimaan = new Collection();
            $data_surat_jalan = new Collection();

            foreach ($result as $data) {
                $data_surat_jalan->push((object) [
                    'no_serah_terima'   => strtoupper(trim($data->no_serah_terima)),
                    'nomor_sj'          => strtoupper(trim($data->nomor_sj)),
                    'tanggal_sj'        => strtoupper(trim($data->tanggal_sj)),
                    'kode_dealer'       => strtoupper(trim($data->kode_dealer)),
                    'nama_dealer'       => strtoupper(trim($data->nama_dealer)),
                    'alamat'            => strtoupper(trim($data->alamat)),
                    'kota'              => strtoupper(trim($data->kota)),
                    'status_terima'     => (int)$data->status_terima,
                    'tanggal_terima'    => strtoupper(trim($data->tanggal_terima)),
                    'jam_terima'        => strtoupper(trim($data->jam_terima)),
                ]);

                $data_penerimaan->push((object) [
                    'no_serah_terima'   => strtoupper(trim($data->no_serah_terima)),
                    'nomor_sj'          => strtoupper(trim($data->nomor_sj)),
                    'tanggal_sj'        => strtoupper(trim($data->tanggal_sj)),
                    'kode_dealer'       => strtoupper(trim($data->kode_dealer)),
                    'nama_dealer'       => strtoupper(trim($data->nama_dealer)),
                    'alamat'            => strtoupper(trim($data->alamat)),
                    'kota'              => strtoupper(trim($data->kota)),
                    'status_terima'     => (int)$data->status_terima,
                    'tanggal_terima'    => strtoupper(trim($data->tanggal_terima)),
                    'jam_terima'        => strtoupper(trim($data->jam_terima)),
                    'kode_sopir'        => strtoupper(trim($data->kode_sopir)),
                    'nama_sopir'        => strtoupper(trim($data->nama_sopir)),
                ]);
            }

            $kode_serah_terima = '';
            $data_serah_terima = [];
            foreach ($data_penerimaan as $data) {
                if (strtoupper(trim($kode_serah_terima)) != strtoupper(trim($data->no_serah_terima))) {
                    $data_serah_terima[] = [
                        'no_serah_terima'   => strtoupper(trim($data->no_serah_terima)),
                        'kode_sopir'        => strtoupper(trim($data->kode_sopir)),
                        'nama_sopir'        => strtoupper(trim($data->nama_sopir)),
                        'surat_jalan'       => $data_surat_jalan
                            ->where('no_serah_terima', strtoupper(trim($data->no_serah_terima)))
                            ->values()
                            ->all()
                    ];
                    $kode_serah_terima = strtoupper(trim($data->no_serah_terima));
                }
            }

            return Response::responseSuccess('success', $data_serah_terima);
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

    public function cekPenerimaanSJ(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_serah_terima'    => 'required|string',
                'companyid'             => 'required|string'
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Isi data secara lengkap");
            }

            $nomor_serah_terima = 'XXX';
            $lenghtText = (float)strlen($request->get('nomor_serah_terima'));

            if ((float)$lenghtText <= 5) {
                $sql = DB::table('no_wh')->select('no_wh.cserah')
                    ->where('CompanyId', '=', $request->get('companyid'))
                    ->first();

                if (empty($sql->cserah)) {
                    return Response::responseWarning("Kode serah terima belum disetting");
                }
                $nomor_serah_terima = strtoupper(trim($sql->cserah)) . substr('00000' . $request->get('nomor_serah_terima'), -5);
            } else {
                $nomor_serah_terima = strtoupper(trim($request->get('nomor_serah_terima')));
            }

            $sql = DB::table('serah')->lock('with (nolock)')
                ->selectRaw("isnull(serah.companyid, '') as companyid, isnull(serah.no_dok, '') as no_dok")
                ->where('serah.companyid', strtoupper(trim($request->get('companyid'))))
                ->where('serah.no_dok', 'like', strtoupper(trim($nomor_serah_terima)) . '%')
                ->whereRaw("cast(serah.tanggal as date) >= dateadd(month, -2, getdate()) ")
                ->first();

            if (empty($sql->no_dok)) {
                return Response::responseWarning("Nomor serah terima tidak ditemukan");
            } else {
                $nomor_dokumen = $sql->no_dok;
                $sql = "select	isnull(serah.companyid, '') as companyid, isnull(serah.no_sj, '') as no_sj,
                                isnull(sj.tgl, '') as tanggal_sj, isnull(sj.kd_dealer, '') as kode_dealer,
                                isnull(serah.nm_dealer, '') as nama_dealer, isnull(serah.alamat1, '') as alamat,
                                isnull(serah.kota, '') as kota, isnull(sj.ditrm, 0) as diterima,
                                isnull(sj.tgl_trm, '') as tanggal_terima, isnull(sj.jam_trm, 0) as jam_terima,
                                replace(isnull(sj.no_sj, ''), '/', '') as images
                        from
                        (
                            select	serah_dtl.companyid, serah_dtl.no_dok, serah_dtl.no_sj,
                                    serah_dtl.nm_dealer, serah_dtl.alamat1, serah_dtl.kota
                            from	serah_dtl
                            where	serah_dtl.no_dok=? and serah_dtl.companyid=?
                        )	serah
                                left join sj on serah.no_sj=sj.no_sj and serah.companyid=sj.companyid
                        order by sj.tgl_trm asc, sj.jam_trm asc";

                $result = DB::select($sql, [$nomor_dokumen, $request->get('companyid')]);
                $data_diterima = [];
                $data_belum_terima = [];

                foreach ($result as $data) {
                    if ((float)$data->diterima == 1) {
                        $data_diterima[] = [
                            'no_sj'         => strtoupper(trim($data->no_sj)),
                            'tanggal_sj'    => trim($data->tanggal_sj),
                            'kode_dealer'   => strtoupper(trim($data->kode_dealer)),
                            'nama_dealer'   => strtoupper(trim($data->nama_dealer)),
                            'alamat'        => strtoupper(trim($data->alamat)),
                            'kota'          => strtoupper(trim($data->kota)),
                            'tanggal_terima' => trim($data->tanggal_terima),
                            'jam_terima'    => trim($data->jam_terima)
                        ];
                    } else {
                        $data_belum_terima[] = [
                            'no_sj'         => strtoupper(trim($data->no_sj)),
                            'tanggal_sj'    => trim($data->tanggal_sj),
                            'kode_dealer'   => strtoupper(trim($data->kode_dealer)),
                            'nama_dealer'   => strtoupper(trim($data->nama_dealer)),
                            'alamat'        => strtoupper(trim($data->alamat)),
                            'kota'          => strtoupper(trim($data->kota)),
                        ];
                    }
                }

                $data = [
                    'no_serah_terima'   => strtoupper(trim($nomor_dokumen)),
                    'diterima'          => $data_diterima,
                    'belum_terima'      => $data_belum_terima,
                ];
                return Response::responseSuccess('success', $data);
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

    public function simpanPenerimaanSJ(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_sj'      => 'required|string',
                'tanggal'       => 'required|string',
                'jam'           => 'required|string',
                'companyid'     => 'required|string'
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Isi data secara lengkap");
            }

            $sql = DB::table('sj')->lock('with (nolock)')
                ->selectRaw("isnull(sj.companyid, '') as companyid, isnull(sj.no_sj, '') as no_sj")
                ->where('sj.no_sj', strtoupper(trim($request->get('nomor_sj'))))
                ->where('sj.companyid', strtoupper(trim($request->get('companyid'))))
                ->first();

            if (empty($sql->no_sj)) {
                return Response::responseWarning("Nomor surat jalan tidak ditemukan");
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_TerimaSJ_Update ?,?,?', [
                    trim(strtoupper($request->get('nomor_sj'))), trim($request->get('tanggal')),
                    trim($request->get('jam'))
                ]);
            });

            return Response::responseSuccess('Data Berhasil Disimpan', null);
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

    public function hapusPenerimaanSJ(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_sj'      => 'required|string',
                'companyid'     => 'required|string'
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Isi data secara lengkap");
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_TerimaSJ_HapusStatus ?,?', [
                    trim(strtoupper($request->get('nomor_sj'))), trim(strtoupper($request->get('companyid')))
                ]);
            });

            return Response::responseSuccess('Data Berhasil Dihapus', null);
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
