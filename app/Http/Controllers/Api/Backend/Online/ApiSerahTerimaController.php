<?php

namespace App\Http\Controllers\Api\Backend\Online;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class ApiSerahTerimaController extends Controller
{
    public function daftarSerahTerima(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'start_date'    => 'required',
                'end_date'      => 'required',
                'companyid'     => 'required',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih tanggal awal dan tanggal akhir terlebih dahulu");
            }

            $sql = DB::table('lokasi')->lock('with (nolock)')
                    ->selectRaw("isnull(lokasi.kd_lokasi, '') as kode_lokasi")
                    ->where('lokasi.companyid', $request->get('companyid'))
                    ->whereRaw("isnull(lokasi.sts_mp_aktif, 0)=1")
                    ->get();

            $jumlah_lokasi = 0;
            $data_lokasi_online = '';

            foreach($sql as $data) {
                $jumlah_lokasi = (double)$jumlah_lokasi + 1;

                if(trim($data_lokasi_online) == '') {
                    $data_lokasi_online = "'".strtoupper(trim($data->kode_lokasi))."'";
                } else {
                    $data_lokasi_online = $data_lokasi_online.','."'".strtoupper(trim($data->kode_lokasi))."'";
                }
            }

            if((double)$jumlah_lokasi <= 0) {
                return Response::responseWarning("Tidak ada lokasi marketplace aktif yang terdaftar");
            }

            $sql = DB::table('serah_online')->lock('with (nolock)')
                    ->selectRaw("isnull(serah_online.no_dok, '') as nomor_dokumen")
                    ->leftJoin(DB::raw('serah_online_dtl with (nolock)'), function($join) {
                        $join->on('serah_online_dtl.no_dok', '=', 'serah_online.no_dok')
                            ->on('serah_online_dtl.companyid', '=', 'serah_online.companyid');
                    })
                    ->leftJoin(DB::raw('sj_dtl with (nolock)'), function($join) {
                        $join->on('sj_dtl.no_sj', '=', 'serah_online_dtl.no_sj')
                            ->on('sj_dtl.companyid', '=', 'serah_online.companyid');
                    })
                    ->leftJoin(DB::raw('fakt_dtl with (nolock)'), function($join) {
                        $join->on('fakt_dtl.no_faktur', '=', 'sj_dtl.no_faktur')
                            ->on('fakt_dtl.companyid', '=', 'serah_online.companyid');
                    })
                    ->whereBetween('serah_online.tanggal', [ $request->get('start_date'), $request->get('end_date') ])
                    ->where('serah_online.companyid', $request->get('companyid'))
                    ->whereRaw("fakt_dtl.kd_lokasi in (".$data_lokasi_online.")")
                    ->groupByRaw("serah_online.no_dok")
                    ->orderByRaw("serah_online.no_dok desc");

            if(!empty($request->get('search')) && trim($request->get('search')) != '') {
                $sql->where('serah_online.no_dok', 'like', trim($request->get('search')).'%')
                    ->orWhere('serah_online.kd_ekspedisi', 'like', trim($request->get('search')).'%');
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
            $data_no_serah_terima = '';
            $data_serah_terima = new Collection();

            foreach ($data as $record) {
                $jumlah_data = (double)$jumlah_data + 1;

                if (trim($data_no_serah_terima) == '') {
                    $data_no_serah_terima .= "'".trim($record->nomor_dokumen)."'";
                } else {
                    $data_no_serah_terima .= ",'".trim($record->nomor_dokumen)."'";
                }
            }

            if((double)$jumlah_data > 0) {
                $sql = "select  isnull(serah_online.no_dok, '') as nomor_dokumen,
                                isnull(serah_online.tanggal, '') as tanggal,
                                isnull(serah_online.kd_ekspedisi, '') as kode_ekspedisi,
                                isnull(ekspedisi_online.nm_ekspedisi, '') as nama_ekspedisi,
                                isnull(serah_online.rit, 0) as rit,
                                isnull(serah_online.keterangan, '') as keterangan,
                                isnull(serah_online.jml_koli, 0) as jml_koli,
                                isnull(serah_online.tgl_mulai, '') as tanggal_mulai,
                                isnull(serah_online.jam_mulai, '') as jam_mulai,
                                isnull(serah_online.tgl_selesai, '') as tanggal_selesai,
                                isnull(serah_online.jam_selesai, '') as jam_selesai
                        from
                        (
                            select  *
                            from    serah_online with (nolock)
                            where   serah_online.no_dok in (".$data_no_serah_terima.") and
                                    serah_online.companyid=?
                        )   serah_online
                                left join ekspedisi_online with (nolock) on
                                        serah_online.kd_ekspedisi=ekspedisi_online.kd_ekspedisi
                        order by serah_online.no_dok desc";

                $result = DB::select($sql, [ $request->get('companyid') ]);

                foreach($result as $data) {
                    $data_serah_terima->push((object) [
                        'nomor_dokumen'     => strtoupper(trim($data->nomor_dokumen)),
                        'tanggal'           => trim($data->tanggal),
                        'kode_ekspedisi'    => trim($data->kode_ekspedisi),
                        'nama_ekspedisi'    => trim($data->nama_ekspedisi),
                        'rit'               => trim($data->rit),
                        'keterangan'        => trim($data->keterangan),
                        'jml_koli'          => trim($data->jml_koli),
                        'tanggal_mulai'     => trim($data->tanggal_mulai),
                        'jam_mulai'         => trim($data->jam_mulai),
                        'tanggal_selesai'   => trim($data->tanggal_selesai),
                        'jam_selesai'       => trim($data->jam_selesai),
                    ]);
                }
            }

            $data_serah_terima_online = [
                'current_page'  => $current_page,
                'data'          => $data_serah_terima,
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

            return Response::responseSuccess('success', $data_serah_terima_online);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function formSerahTerima(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_dokumen'     => 'required',
                'companyid'         => 'required',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih nomor dokumen terlebih dahulu");
            }

            $sql = "select	isnull(serah_online.no_dok, '') as nomor_dokumen,
                            isnull(serah_online.tanggal, '') as tanggal,
                            isnull(serah_online.kd_ekspedisi, '') as kode_ekspedisi,
                            isnull(ekspedisi_online.nm_ekspedisi, '') as nama_ekspedisi,
                            isnull(serah_online.rit, 0) as rit,
                            isnull(serah_online.keterangan, '') as keterangan_serah_terima,
                            isnull(serah_online.jml_koli, 0) as jml_koli,
                            isnull(serah_online.tgl_mulai, '') as tanggal_mulai,
                            isnull(serah_online.jam_mulai, '') as jam_mulai,
                            isnull(serah_online.tgl_selesai, '') as tanggal_selesai,
                            isnull(serah_online.jam_selesai, '') as jam_selesai,
                            isnull(serah_online_dtl.no_sj, '') as nomor_sj,
                            isnull(faktur.no_faktur, '') as nomor_faktur,
                            isnull(faktur.ket, '') as keterangan_faktur,
                            isnull(fakt_dtl.kd_lokasi, '') as kode_lokasi,
                            isnull(serah_online_dtl.jumlah, 0) as jml_koli,
                            isnull(faktur.sts_reqpickup, 0) as status_mp_detail,
                            isnull(lokasi.sts_mp_aktif, 0) as status_mp_aktif
                    from
                    (
                        select	*
                        from	serah_online
                        where	serah_online.no_dok=? and
                                serah_online.companyid=?
                    )	serah_online
                            inner join serah_online_dtl with (nolock) on serah_online.no_dok=serah_online_dtl.no_dok and
                                        serah_online.companyid=serah_online_dtl.companyid
                            left join ekspedisi_online with (nolock) on serah_online.kd_ekspedisi=ekspedisi_online.kd_ekspedisi
                            left join sj_dtl with (nolock) on serah_online_dtl.no_sj=sj_dtl.no_sj and
                                        serah_online.companyid=sj_dtl.companyid
                            left join faktur with (nolock) on sj_dtl.no_faktur=faktur.no_faktur and
                                        serah_online.companyid=faktur.companyid
                            left join fakt_dtl with (nolock) on sj_dtl.no_faktur=fakt_dtl.no_faktur and
                                        serah_online.companyid=fakt_dtl.companyid
                            left join lokasi with (nolock) on fakt_dtl.kd_lokasi=lokasi.kd_lokasi and
                                        serah_online.companyid=lokasi.companyid
                    group by serah_online.no_dok, serah_online.tanggal, serah_online.kd_ekspedisi,
                            ekspedisi_online.nm_ekspedisi, serah_online.rit,
                            serah_online.keterangan, serah_online.jml_koli, serah_online.tgl_mulai,
                            serah_online.jam_mulai, serah_online.tgl_selesai, serah_online.jam_selesai,
                            serah_online_dtl.no_sj, serah_online_dtl.jumlah, lokasi.sts_mp_aktif,
                            faktur.no_faktur, faktur.ket, fakt_dtl.kd_lokasi, faktur.sts_reqpickup
                    order by faktur.no_faktur asc";

            $result = DB::select($sql, [ $request->get('nomor_dokumen'), $request->get('companyid') ]);

            $jumlah_data = 0;
            $data_detail = new Collection();
            $data_header_serah_terima = new Collection();
            $data_detail_serah_terima = new Collection();

            foreach($result as $data) {
                $jumlah_data = (double)$jumlah_data + 1;

                $marketplace = '';
                if(strtoupper(trim(config('constants.api.tokopedia.kode_lokasi'))) == strtoupper(trim($data->kode_lokasi))) {
                    $marketplace = 'TOKOPEDIA';
                } elseif(strtoupper(trim(config('constants.api.shopee.kode_lokasi'))) == strtoupper(trim($data->kode_lokasi))) {
                    $marketplace = 'SHOPEE';
                }

                $data_detail_serah_terima->push((object) [
                    'nomor_dokumen'     => strtoupper(trim($data->nomor_dokumen)),
                    'nomor_sj'          => strtoupper(trim($data->nomor_sj)),
                    'nomor_faktur'      => strtoupper(trim($data->nomor_faktur)),
                    'kode_lokasi'       => strtoupper(trim($data->kode_lokasi)),
                    'marketplace'       => strtoupper(trim($marketplace)),
                    'nomor_invoice'     => strtoupper(trim($data->keterangan_faktur)),
                    'jumlah_koli'       => (double)$data->jml_koli,
                    'status_mp_detail'  => (int)$data->status_mp_detail,
                    'status_mp_aktif'   => (int)$data->status_mp_aktif,
                ]);

                $data_detail->push((object) [
                    'nomor_dokumen'     => strtoupper(trim($data->nomor_dokumen)),
                    'tanggal'           => trim($data->tanggal),
                    'kode_ekspedisi'    => trim($data->kode_ekspedisi),
                    'nama_ekspedisi'    => trim($data->nama_ekspedisi),
                    'rit'               => (double)$data->rit,
                    'keterangan_serah_terima' => trim($data->keterangan_serah_terima),
                    'jml_koli'          => (double)$data->jml_koli,
                    'tanggal_mulai'     => trim($data->tanggal_mulai),
                    'jam_mulai'         => trim($data->jam_mulai),
                    'tanggal_selesai'   => trim($data->tanggal_selesai),
                    'jam_selesai'       => trim($data->jam_selesai),
                    'nomor_sj'          => strtoupper(trim($data->nomor_sj)),
                    'nomor_faktur'      => strtoupper(trim($data->nomor_faktur)),
                    'nomor_invoice'     => strtoupper(trim($data->keterangan_faktur)),
                    'status_mp_detail'  => (int)$data->status_mp_detail,
                    'status_mp_aktif'   => (int)$data->status_mp_aktif,
                ]);
            }

            if((double)$jumlah_data <= 0) {
                return Response::responseWarning("Nomor dokumen yang anda pilih tidak terdaftar");
            }

            $nomor_dokumen = '';

            foreach($data_detail as $data) {
                if(strtoupper(trim($data->nomor_dokumen)) != strtoupper(trim($nomor_dokumen))) {
                    $data_header_serah_terima->push((object) [
                        'nomor_dokumen'     => strtoupper(trim($data->nomor_dokumen)),
                        'tanggal'           => trim($data->tanggal),
                        'ekspedisi'         => [
                            'kode'          => trim($data->kode_ekspedisi),
                            'nama'          => trim($data->nama_ekspedisi),
                        ],
                        'rit'               => (double)$data->rit,
                        'keterangan'        => trim($data->keterangan_serah_terima),
                        'jml_koli'          => (double)$data->jml_koli,
                        'mulai'             => [
                            'tanggal'       => trim($data->tanggal_mulai),
                            'jam'           => trim($data->jam_mulai),
                        ],
                        'selesai'           => [
                            'tanggal'       => trim($data->tanggal_selesai),
                            'jam'           => trim($data->jam_selesai),
                        ],
                        'detail'            => $data_detail_serah_terima
                                                ->where('nomor_dokumen', strtoupper(trim($data->nomor_dokumen)))
                                                ->values()
                                                ->all()
                    ]);
                    $nomor_dokumen = strtoupper(trim($data->nomor_dokumen));
                }
            }

            return Response::responseSuccess('success', $data_header_serah_terima->first());
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function updateStatusSerahTerima(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_faktur'  => 'required',
                'companyid'     => 'required',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih data dokumen dan surat jalan terlebih dahulu");
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_Faktur_UpdateStsReqPickup ?,?', [
                    strtoupper(trim($request->get('nomor_faktur'))), strtoupper(trim($request->get('companyid'))),
                ]);
            });

            return Response::responseSuccess('Status request pickup internal berhasil disimpan');
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }

    }
}
