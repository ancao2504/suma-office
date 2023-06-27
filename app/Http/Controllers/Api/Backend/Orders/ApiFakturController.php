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


class ApiFakturController extends Controller
{
    public function daftarFaktur(Request $request) {
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
                $sql = "select  isnull(faktur.no_faktur, '') as nomor_faktur, isnull(faktur.tgl_faktur, '') as tanggal,
                                isnull(faktur.kd_sales, '') as kode_sales, isnull(salesman.nm_sales, '') as nama_sales,
                                isnull(faktur.kd_dealer, '') as kode_dealer, isnull(dealer.nm_dealer, '') as nama_dealer,
                                isnull(faktur.kd_tmkr, '') as kode_telemarketing, isnull(faktur.kd_beli, '') as kode_beli,
                                isnull(faktur.kd_tpc, '') as kode_tpc, isnull(faktur.approve_ol, 0) as approve_online,
                                isnull(faktur.ket, '') as keterangan, isnull(faktur.umur_faktur, 0) as umur_faktur,
                                isnull(faktur.tgl_akhir_faktur, '') as tanggal_akhir_faktur,
                                isnull(faktur.no_pof, '') as nomor_pof,
                                isnull(faktur.bo, '') as bo, isnull(faktur.rh, '') as jenis_order,
                                isnull(faktur.total, 0) as total, iif(isnull(pof.no_pof, '')='', 0, 1) as status_pof,
                                isnull(faktur.sts_ctk, 0) as status_cetak,
                                substring(isnull(faktur.usertime, ''), 21, 50) as usertime
                        from
                        (
                            select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur, faktur.kd_sales, faktur.kd_dealer,
                                    faktur.kd_tmkr, faktur.kd_beli, faktur.kd_tpc, faktur.approve_ol, faktur.ket, faktur.umur_faktur,
                                    faktur.tgl_akhir_faktur, faktur.no_pof, faktur.bo, faktur.rh, faktur.sts_ctk, faktur.total,
                                    faktur.usertime
                            from	faktur with (nolock)
                            where	faktur.no_faktur in (".$data_no_faktur.") and
                                    faktur.companyid=?
                        )   faktur
                                left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and
                                            faktur.companyid=salesman.companyid
                                left join dealer with (nolock) on faktur.kd_dealer=dealer.kd_dealer and
                                            faktur.companyid=dealer.companyid
                                left join pof with (nolock) on faktur.no_pof=pof.no_pof and
                                            faktur.companyid=pof.companyid
                        order by faktur.tgl_faktur desc, faktur.no_faktur desc";

                $result = DB::select($sql, [ $request->get('companyid') ]);

                foreach($result as $data) {
                    $data_faktur->push((object) [
                        'nomor_faktur'      => strtoupper(trim($data->nomor_faktur)),
                        'nomor_pof'         => trim($data->nomor_pof),
                        'tanggal'           => trim($data->tanggal),
                        'kode_beli'         => trim($data->kode_beli),
                        'kode_beli'         => trim($data->kode_beli),
                        'kode_telemarketing' => trim($data->kode_telemarketing),
                        'kode_sales'        => trim($data->kode_sales),
                        'nama_sales'        => trim($data->nama_sales),
                        'kode_dealer'       => trim($data->kode_dealer),
                        'nama_dealer'       => trim($data->nama_dealer),
                        'keterangan'        => trim($data->keterangan),
                        'kode_tpc'          => trim($data->kode_tpc),
                        'umur_faktur'       => trim($data->umur_faktur),
                        'tanggal_akhir_faktur' => trim($data->tanggal_akhir_faktur),
                        'jenis_order'       => trim($data->jenis_order),
                        'bo'                => trim($data->bo),
                        'total'             => (double)$data->total,
                        'approve_online'    => (int)$data->approve_online,
                        'status_pof'        => (int)$data->status_pof,
                        'status_cetak'      => (int)$data->status_cetak,
                        'usertime'          => trim($data->usertime),
                    ]);
                }
            }

            $data_faktur_penjualan = [
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

            return Response::responseSuccess('success', $data_faktur_penjualan);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function formFaktur(Request $request) {
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
                            isnull(dealer.nm_dealer, '') as nama_dealer, isnull(dealer.nm_dealersj, '') as nama_dealer_sj,
                            isnull(dealer.alamat1sj, '') as alamat_dealer_sj, isnull(dealer.kotasj, '') as kota_dealer_sj,
                            isnull(dealer.ketsj1, '') as keterangan1_dealer_sj, isnull(dealer.ketsj2, '') as keterangan2_dealer_sj,
                            isnull(faktur.kd_tpc, '') as kode_tpc, isnull(faktur.kd_beli, '') as kode_beli,
                            isnull(jns_beli.nama, '') as keterangan_beli, isnull(faktur.umur_faktur, 0) as umur_faktur,
                            isnull(faktur.rh, '') as rh, isnull(faktur.tgl_akhir_faktur, '') as tanggal_akhir_faktur,
                            isnull(faktur.bo, '') as bo, isnull(faktur.disc2, 0) as disc_header,
                            isnull(faktur.discrp, 0) as discount_rupiah, isnull(faktur.total, 0) as total,
                            isnull(faktur.kd_part, '') as part_number, isnull(part.ket, '') as nama_part,
                            isnull(faktur.jml_order, 0) as jml_order, isnull(faktur.jml_jual, 0) as jml_jual,
                            isnull(faktur.harga, 0) as harga, isnull(faktur.disc1, 0) as disc_detail,
                            isnull(faktur.jumlah, 0) as jumlah, isnull(faktur.het, 0) as het, isnull(faktur.usertime, '') as usertime,
                            iif(isnull(pof.no_pof, '')='', 0, 1) as status_pof, isnull(faktur.ket, '') as keterangan_faktur
                    from
                    (
                        select	faktur.companyid, faktur.no_faktur, faktur.no_pof, faktur.tgl_faktur, faktur.kd_beli,
                                faktur.rh, faktur.umur_faktur, faktur.tgl_akhir_faktur, faktur.ket,
                                faktur.kd_sales, faktur.nm_sales, faktur.kd_dealer, faktur.kd_tpc, faktur.bo, faktur.disc2,
                                faktur.discrp, faktur.total, fakt_dtl.kd_part, fakt_dtl.jml_order, fakt_dtl.jml_jual,
                                fakt_dtl.harga, fakt_dtl.disc1, fakt_dtl.jumlah, fakt_dtl.het, fakt_dtl.usertime
                        from
                        (
                            select	faktur.companyid, faktur.no_faktur, faktur.no_pof, faktur.tgl_faktur, faktur.kd_beli,
                                    faktur.kd_sales, salesman.nm_sales, faktur.kd_dealer,  faktur.kd_tpc, faktur.bo,
                                    faktur.rh, faktur.umur_faktur, faktur.tgl_akhir_faktur, faktur.ket,
                                    faktur.disc2, faktur.discrp, faktur.total
                            from	faktur with (nolock)
                                        left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and faktur.companyid=salesman.companyid
                                        left join superspv with (nolock) on salesman.spv=superspv.kd_spv and faktur.companyid=superspv.companyid
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
                    )	faktur
                            left join pof with (nolock) on faktur.no_pof=pof.no_pof and faktur.companyid=pof.companyid
                            left join jns_beli with (nolock) on faktur.kd_beli=jns_beli.kd_beli and faktur.companyid=jns_beli.companyid
                            left join dealer with (nolock) on faktur.kd_dealer=dealer.kd_dealer and faktur.companyid=dealer.companyid
                            left join part with (nolock) on faktur.kd_part=part.kd_part and faktur.companyid=part.companyid
                    order by faktur.usertime asc";

            $result = DB::select($sql, [ trim($request->get('nomor_faktur')), trim($request->get('companyid')) ]);

            $nomor_faktur = '';
            $nomor_pof = '';
            $tanggal_faktur = '';
            $kode_sales = '';
            $nama_sales = '';
            $kode_dealer = '';
            $nama_dealer = '';
            $nama_dealer_sj = '';
            $alamat_dealer_sj = '';
            $kota_dealer_sj = '';
            $keterangan1_dealer_sj = '';
            $keterangan2_dealer_sj = '';
            $kode_tpc = '';
            $rh = '';
            $bo = '';
            $kode_beli = '';
            $keterangan_beli = '';
            $umur_faktur = '';
            $tanggal_akhir_faktur = '';
            $keterangan_faktur = '';
            $status_pof = 0;
            $total_order = 0;
            $total_jual = 0;
            $sub_total = 0;
            $disc_header = 0;
            $discount_rupiah = 0;
            $grand_total = 0;
            $total_data = 0;
            $data_faktur_detail = [];

            foreach($result as $data) {
                $nomor_faktur = trim($data->nomor_faktur);
                $nomor_pof = trim($data->nomor_pof);
                $tanggal_faktur = trim($data->tanggal_faktur);
                $kode_sales = trim($data->kode_sales);
                $nama_sales = trim($data->nama_sales);
                $kode_dealer = trim($data->kode_dealer);
                $nama_dealer = trim($data->nama_dealer);
                $nama_dealer_sj = trim($data->nama_dealer_sj);
                $alamat_dealer_sj = trim($data->alamat_dealer_sj);
                $kota_dealer_sj = trim($data->kota_dealer_sj);
                $keterangan1_dealer_sj = trim($data->keterangan1_dealer_sj);
                $keterangan2_dealer_sj = trim($data->keterangan2_dealer_sj);
                $kode_tpc = trim($data->kode_tpc);
                $bo = trim($data->bo);
                $rh = trim($data->rh);
                $kode_beli = trim($data->kode_beli);
                $keterangan_beli = trim($data->keterangan_beli);
                $umur_faktur = trim($data->umur_faktur);
                $tanggal_akhir_faktur = trim($data->tanggal_akhir_faktur);
                $keterangan_faktur = trim($data->keterangan_faktur);
                $status_pof = $data->status_pof;
                $disc_header = $data->disc_header;
                $discount_rupiah = $data->discount_rupiah;
                $grand_total = $data->total;
                $total_order = $total_order + $data->jml_order;
                $total_jual = $total_jual + $data->jml_jual;
                $sub_total = $sub_total + $data->jumlah;
                $total_data = (double)$total_data + 1;

                $data_faktur_detail[] = [
                    'nomor_faktur'  => trim($data->nomor_faktur),
                    'part_number'   => trim($data->part_number),
                    'nama_part'     => trim($data->nama_part),
                    'jml_order'     => (double)$data->jml_order,
                    'jml_jual'      => (double)$data->jml_jual,
                    'harga'         => (double)$data->harga,
                    'disc_detail'   => (double)$data->disc_detail,
                    'total_detail'  => (double)$data->jumlah,
                    'het'           => (double)$data->het,
                    'image_part'    => trim(config('constants.api.url.images')).'/'.trim($data->part_number).'.jpg'
                ];
            }

            $data_faktur = new Collection();
            $data_faktur->push((object) [
                'nomor_faktur'  => $nomor_faktur,
                'tanggal_faktur' => $tanggal_faktur,
                'nomor_pof'     => $nomor_pof,
                'status_pof'    => $status_pof,
                'kode_sales'    => $kode_sales,
                'nama_sales'    => $nama_sales,
                'kode_dealer'   => $kode_dealer,
                'nama_dealer'   => $nama_dealer,
                'dealer_sj'     => [
                    'nama_dealer'   => $nama_dealer_sj,
                    'alamat'        => $alamat_dealer_sj,
                    'kota'          => $kota_dealer_sj,
                    'keterangan1'   => $keterangan1_dealer_sj,
                    'keterangan2'   => $keterangan2_dealer_sj,
                ],
                'kode_beli'         => $kode_beli,
                'keterangan_beli'   => $keterangan_beli,
                'umur_faktur'       => $umur_faktur,
                'tanggal_akhir_faktur' => $tanggal_akhir_faktur,
                'keterangan'    => $keterangan_faktur,
                'kode_tpc'      => $kode_tpc,
                'bo'            => $bo,
                'rh'            => $rh,
                'total_order'   => (double)$total_order,
                'total_jual'    => (double)$total_jual,
                'sub_total'     => (double)$sub_total,
                'disc_header'   => (double)$disc_header,
                'nominal_disc_header' => ((double)$sub_total * (double)$disc_header) / 100,
                'disc_rupiah'   => (double)$discount_rupiah,
                'grand_total'   => (double)$grand_total,
                'detail_faktur' => $data_faktur_detail,
            ]);

            if((double)$total_data > 0) {
                return Response::responseSuccess('success', $data_faktur->first());
            } else {
                return Response::responseWarning('Data tidak ditemukan');
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
