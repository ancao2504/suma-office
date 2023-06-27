<?php

namespace App\Http\Controllers\Api\Backend\Orders;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;

class ApiTrackingOrderController extends Controller
{
    public function daftarTrackingOrder(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'year'      => 'required',
                'month'     => 'required',
                'role_id'   => 'required|string',
                'user_id'   => 'required|string',
                'companyid' => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Data tahun dan bulan harus terisi");
            }

            $sql = DB::table('faktur')->lock('with (nolock)')
                    ->selectRaw("isnull(faktur.no_faktur, '') as nomor_faktur")
                    ->leftJoin(DB::raw('salesman with (nolock)'), function($join) {
                        $join->on('salesman.kd_sales', '=', 'faktur.kd_sales')
                            ->on('salesman.companyid', '=', 'faktur.companyid');
                        })
                    ->leftJoin(DB::raw('superspv with (nolock)'), function($join) {
                        $join->on('superspv.kd_spv', '=', 'salesman.spv')
                            ->on('superspv.companyid', '=', 'faktur.companyid');
                        })
                    ->whereYear('faktur.tgl_faktur', $request->get('year'))
                    ->whereMonth('faktur.tgl_faktur', $request->get('month'))
                    ->where('faktur.companyid', trim($request->get('companyid')))
                    ->orderBy('faktur.tgl_faktur', 'desc')
                    ->orderBy('faktur.no_faktur', 'desc');


            if(!empty($request->get('nomor_faktur'))) {
                $sql->where('faktur.no_faktur', 'like', $request->get('nomor_faktur').'%');
            }

            if(strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                $sql->where('faktur.kd_dealer', $request->get('user_id'));
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql->where('faktur.kd_sales', $request->get('user_id'));
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql->where('superspv.nm_spv', $request->get('user_id'));
            } else {
                if(Str::contains(strtoupper(trim($request->get('role_id'))), 'OL')) {
                    $sql->whereRaw("left(faktur.no_faktur, 2)='OL'");
                }
            }

            if(!empty($request->get('kode_sales'))) {
                $sql->where('faktur.kd_sales', $request->get('kode_sales'));
            }

            if(!empty($request->get('kode_dealer'))) {
                $sql->where('faktur.kd_dealer', $request->get('kode_dealer'));
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
            $data_tracking = new Collection();

            foreach ($data as $record) {
                $jumlah_data = (double)$jumlah_data + 1;

                if (trim($data_no_faktur) == '') {
                    $data_no_faktur .= "'".trim($record->nomor_faktur)."'";
                } else {
                    $data_no_faktur .= ",'".trim($record->nomor_faktur)."'";
                }
            }

            if((double)$jumlah_data > 0) {
                $sql = "select	isnull(faktur.no_faktur, '') as nomor_faktur, isnull(faktur.tgl_faktur, '') as tanggal,
                                isnull(faktur.kd_sales, '') as kode_sales, isnull(salesman.nm_sales, '') as nama_sales,
                                isnull(faktur.kd_dealer, '') as kode_dealer, isnull(dealer.nm_dealer, '') as nama_dealer,
                                isnull(faktur.ket, '') as keterangan, isnull(faktur.total, 0) as total,
                                case
                                    when isnull(sj.ditrm, 0)=1 then 6
                                    when isnull(serah_dtl.no_dok, '') <> '' then 5
                                    when isnull(sj.sts_ctk, 0)=1 then 4
                                    when isnull(sj.no_sj, '') <> '' then 3
                                    when isnull(faktur.sts_ctk, 0)=1 then 2
                                else 1
                                end + 1 as status_progress,
                                case
                                    when isnull(sj.ditrm, 0)=1 then 6
                                    when isnull(serah_dtl.no_dok, '') <> '' then 5
                                    when isnull(sj.sts_ctk, 0)=1 then 4
                                    when isnull(sj.no_sj, '') <> '' then 3
                                    when isnull(faktur.sts_ctk, 0)=1 then 2
                                else 1
                                end as status_pengiriman
                        from
                        (
                            select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur, faktur.kd_sales, faktur.kd_dealer,
                                    faktur.sts_ctk, faktur.ket, faktur.total
                            from	faktur with (nolock)
                            where	faktur.no_faktur in (".$data_no_faktur.") and
                                    faktur.companyid=?
                        )	faktur
                                left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and
                                            faktur.companyid=salesman.companyid
                                left join superspv with (nolock) on salesman.spv=superspv.kd_spv and
                                            faktur.companyid=superspv.companyid
                                left join dealer with (nolock) on faktur.kd_dealer=dealer.kd_dealer and
                                            faktur.companyid=dealer.companyid
                                left join sj_dtl with (nolock) on faktur.no_faktur=sj_dtl.no_faktur and
                                            faktur.companyid=sj_dtl.companyid
                                left join sj with (nolock) on sj_dtl.no_sj=sj.no_sj and
                                            faktur.companyid=sj.companyid
                                left join serah_dtl with (nolock) on sj.no_sj=serah_dtl.no_sj and
                                            faktur.companyid=serah_dtl.companyid
                        order by faktur.companyid asc, faktur.no_faktur desc";

                $result = DB::select($sql, [ $request->get('companyid') ]);

                foreach($result as $data) {
                    $data_tracking->push((object) [
                        'nomor_faktur'      => strtoupper(trim($data->nomor_faktur)),
                        'tanggal'           => trim($data->tanggal),
                        'kode_sales'        => trim($data->kode_sales),
                        'nama_sales'        => trim($data->nama_sales),
                        'kode_dealer'       => trim($data->kode_dealer),
                        'nama_dealer'       => trim($data->nama_dealer),
                        'keterangan'        => trim($data->keterangan),
                        'total'             => (double)$data->total,
                        'status_progress'   => trim($data->status_progress),
                        'status_pengiriman' => trim($data->status_pengiriman),
                    ]);
                }
            }

            $data_tracking_order = [
                'current_page'  => $current_page,
                'data'          => $data_tracking,
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

            return Response::responseSuccess('success', $data_tracking_order);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function detailTrackingOrder(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_faktur' => 'required|string',
                'role_id'   => 'required|string',
                'user_id'   => 'required|string',
                'companyid' => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih nomor faktur terlebih dahulu");
            }

            $sql = "select	isnull(faktur.no_faktur, '') as nomor_faktur, isnull(faktur.no_pof, '') as nomor_pof,
                            isnull(faktur.tgl_faktur, '') as tanggal_faktur, isnull(faktur.kd_sales, '') as kode_sales,
                            isnull(faktur.nm_sales, '') as nama_sales, isnull(faktur.kd_dealer, '') as kode_dealer,
                            isnull(dealer.nm_dealer, '') as nama_dealer, isnull(faktur.kd_tpc, '') as kode_tpc,
                            isnull(faktur.bo, '') as bo, isnull(faktur.disc2, 0) as disc_header, isnull(faktur.discrp, 0) as disc_rupiah,
                            isnull(faktur.total, 0) as total, isnull(faktur.kd_part, '') as part_number,
                            isnull(part.ket, '') as nama_part, isnull(faktur.jml_jual, 0) as jml_jual,
                            isnull(faktur.harga, 0) as harga, isnull(faktur.disc1, 0) as disc_detail,
                            isnull(faktur.jumlah, 0) as jumlah, isnull(faktur.het, 0) as het,
                            isnull(faktur.usertime, '') as usertime, isnull(faktur.sts_ctk, 0) as faktur_sts_cetak,
                            isnull(faktur.cetak, '') as faktur_cetak, isnull(convert(varchar(10), wh_time.tanggal3, 120), '') as tanggal_packing,
                            isnull(wh_time.jam3, '') as jam_packing, isnull(wh_time.kd_lokpack, '') as kode_lokasi_packing,
                            isnull(lokasi_pack.keterangan, '') as nama_lokasi_packing, isnull(sj_dtl.no_sj, '') as nomor_sj,
                            isnull(sj.usertime, '') as sj_usertime, isnull(sj.sts_ctk, '') as sj_sts_cetak,
                            isnull(sj.cetak, '') as sj_cetak, isnull(serah_dtl.no_dok, '') as nomor_serah_terima,
                            isnull(serah.no_polisi, '') as nomor_polisi, isnull(mobil.nm_mobil, '') as nama_mobil,
                            isnull(serah.sopir, '') as sopir, isnull(serah.usertime, '') as usertime_serah_terima,
                            isnull(sj.ditrm, 0) as status_toko_terima_faktur, isnull(sj.tgl_trm, '') as tanggal_toko_terima_faktur
                    from
                    (
                        select	faktur.companyid, faktur.no_faktur, faktur.no_pof, faktur.tgl_faktur,
                                faktur.kd_sales, faktur.nm_sales, faktur.kd_dealer, faktur.kd_tpc,
                                faktur.bo, faktur.disc2, faktur.discrp, faktur.total, fakt_dtl.kd_part,
                                fakt_dtl.jml_jual, faktur.sts_ctk, faktur.cetak, fakt_dtl.harga,
                                fakt_dtl.disc1, fakt_dtl.jumlah, fakt_dtl.het, fakt_dtl.usertime
                        from
                        (
                            select	faktur.companyid, faktur.no_faktur, faktur.no_pof, faktur.tgl_faktur,
                                    faktur.kd_dealer, faktur.kd_sales, salesman.nm_sales,
                                    faktur.kd_tpc, faktur.bo, faktur.disc2, faktur.discrp,
                                    faktur.total, faktur.sts_ctk, faktur.cetak
                            from	faktur with (nolock)
                                        left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and
                                                    faktur.companyid=salesman.companyid
                                        left join superspv with (nolock) on salesman.spv=superspv.kd_spv and
                                                    faktur.companyid=superspv.companyid
                            where	faktur.no_faktur=? and faktur.companyid=?";

            if(strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                $sql .= " and faktur.kd_dealer='".trim($request->get('user_id'))."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql .= " and faktur.kd_sales='".trim($request->get('user_id'))."'";
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql .= " and superspv.nm_spv='".trim($request->get('user_id'))."'";
            } else {
                if(Str::contains(strtoupper(trim($request->get('role_id'))), 'OL')) {
                    $sql .= " and left(faktur.no_faktur, 2)='OL'";
                }
            }

            $sql .= " )	faktur
                                left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                                faktur.companyid=fakt_dtl.companyid
                        where   isnull(fakt_dtl.jml_jual, 0) > 0
                    )	faktur
                            left join dealer with (nolock) on faktur.kd_dealer=dealer.kd_dealer and faktur.companyid=dealer.companyid
                            left join part with (nolock) on faktur.kd_part=part.kd_part and faktur.companyid=part.companyid
                            left join wh_dtl with (nolock) on faktur.no_faktur=wh_dtl.no_faktur and faktur.companyid=wh_dtl.companyid
                            left join wh_time with (nolock) on wh_dtl.no_dok=wh_time.no_dok and faktur.companyid=wh_time.companyid
                            left join lokasi_pack with (nolock) on wh_time.kd_lokpack=lokasi_pack.kd_lokpack and faktur.companyid=lokasi_pack.companyid
                            left join sj_dtl with (nolock) on faktur.no_faktur=sj_dtl.no_faktur and faktur.companyid=sj_dtl.companyid
                            left join sj with (nolock) on sj_dtl.no_sj=sj.no_sj and faktur.companyid=sj.companyid
                            left join serah_dtl with (nolock) on sj_dtl.no_sj=serah_dtl.no_sj and faktur.companyid=serah_dtl.companyid
                            left join serah with (nolock) on serah_dtl.no_dok=serah.no_dok and faktur.companyid=serah.companyid
                            left join mobil with (nolock) on serah.no_polisi=mobil.nopol and faktur.companyid=mobil.companyid
                    order by faktur.usertime asc";

            $result = DB::select($sql, [ trim($request->get('nomor_faktur')), trim($request->get('companyid')) ]);

            $nomor_faktur = 'XX';
            $nomor_pof = 'XX';
            $tanggal_faktur = 'XXXX-XX-XX';
            $kode_sales = 'XX';
            $nama_sales = 'XX';
            $kode_dealer = 'XX';
            $nama_dealer = 'XX';
            $kode_tpc = 'XX';
            $bo = 'X';
            $total_jual = 0;
            $sub_total = 0;
            $disc_rupiah = 0;
            $disc_header = 0;
            $grand_total = 0;
            $data_faktur_detail = new Collection();
            $data_detail_pengiriman = new Collection();
            $jumlah_data = 0;

            foreach($result as $data) {
                $jumlah_data = (int)$jumlah_data + 1;

                $nomor_faktur = trim($data->nomor_faktur);
                $nomor_pof = trim($data->nomor_pof);
                $tanggal_faktur = trim($data->tanggal_faktur);
                $kode_sales = trim($data->kode_sales);
                $nama_sales = trim($data->nama_sales);
                $kode_dealer = trim($data->kode_dealer);
                $nama_dealer = trim($data->nama_dealer);
                $kode_tpc = trim($data->kode_tpc);
                $bo = trim($data->bo);
                $disc_header = $data->disc_header;
                $disc_rupiah = $data->disc_rupiah;
                $grand_total = $data->total;
                $total_jual = $total_jual + $data->jml_jual;
                $sub_total = $sub_total + $data->jumlah;

                $status_progress = '';
                if((int)$data->faktur_sts_cetak == 1) {
                    if(trim($data->nomor_sj) != '') {
                        if((int)$data->sj_sts_cetak == 1) {
                            if(trim($data->nomor_serah_terima) != '') {
                                if((int)$data->status_toko_terima_faktur == 1) {
                                    $status_progress = 'TOKO_TERIMA_FAKTUR';
                                }
                            } else {
                                $status_progress = 'SERAH_TERIMA';
                            }
                        } else {
                            $status_progress = 'SURAT_JALAN';
                        }
                    } else {
                        $status_progress = 'GUDANG';
                    }
                } else {
                    $status_progress = 'FAKTUR';
                }

                $data_detail_pengiriman->push((object) [
                    'nomor_faktur'              => trim($data->nomor_faktur),
                    'status_cetak_faktur'       => (int)$data->faktur_sts_cetak,
                    'usertime_cetak_faktur'     => ((int)$data->faktur_sts_cetak == 0) ? '' : trim($data->faktur_cetak),
                    'lokasi_packing'            => strtoupper(trim($data->kode_lokasi_packing)).' - '.strtoupper(trim($data->nama_lokasi_packing)),
                    'usertime_packing'          => strtoupper(trim($data->tanggal_packing))." = ".strtoupper(trim($data->jam_packing)),
                    'nomor_surat_jalan'         => trim($data->nomor_sj),
                    'status_cetak_surat_jalan'  => (int)$data->sj_sts_cetak,
                    'usertime_surat_jalan'      => trim($data->sj_usertime),
                    'usertime_cetak_surat_jalan' => ((int)$data->sj_sts_cetak == 0) ? '' : trim($data->sj_cetak),
                    'nomor_serah_terima'        => trim($data->nomor_serah_terima),
                    'kendaraan_serah_terima'    => (trim($data->nama_mobil == '')) ? trim($data->nomor_polisi) : trim($data->nomor_polisi).' - '.trim($data->nama_mobil),
                    'sopir_serah_terima'        => trim($data->sopir),
                    'usertime_serah_terima'     => trim($data->usertime_serah_terima),
                    'status_toko_terima'        => trim($data->status_toko_terima_faktur),
                    'tanggal_terima_toko'       => trim($data->tanggal_toko_terima_faktur),
                    'status_progress'           => trim($status_progress),
                ]);

                $data_faktur_detail->push((object) [
                    'nomor_faktur'  => trim($data->nomor_faktur),
                    'part_number'   => trim($data->part_number),
                    'nama_part'     => trim($data->nama_part),
                    'jml_jual'      => (double)$data->jml_jual,
                    'harga'         => (double)$data->harga,
                    'disc_detail'   => (double)$data->disc_detail,
                    'total_detail'  => (double)$data->jumlah,
                    'het'           => (double)$data->het,
                    'image_part'    => trim(config('constants.url.images')).'/'.trim($data->part_number).'.jpg'
                ]);
            }

            $data_tacking = new Collection();
            $data_tacking->push((object) [
                'nomor_faktur'  => $nomor_faktur,
                'nomor_pof'     => $nomor_pof,
                'tanggal_faktur' => $tanggal_faktur,
                'kode_sales'    => $kode_sales,
                'nama_sales'    => $nama_sales,
                'kode_dealer'   => $kode_dealer,
                'nama_dealer'   => $nama_dealer,
                'kode_tpc'      => $kode_tpc,
                'bo'            => $bo,
                'total_jual'    => (double)$total_jual,
                'sub_total'     => (double)$sub_total,
                'disc_header'   => (double)$disc_header,
                'nominal_disc_header' => ((double)$sub_total * (double)$disc_header) / 100,
                'disc_rupiah'   => (double)$disc_rupiah,
                'grand_total'   => (double)$grand_total,
                'detail_faktur' => $data_faktur_detail,
                'detail_pengiriman' => $data_detail_pengiriman->first()
            ]);

            if($jumlah_data > 0) {
                return Response::responseSuccess('success', $data_tacking->first());
            } else {
                return Response::responseWarning('Data tidak ditemukan');
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
