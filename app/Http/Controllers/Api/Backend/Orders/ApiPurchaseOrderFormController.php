<?php

namespace App\Http\Controllers\Api\Backend\Orders;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;


class ApiPurchaseOrderFormController extends Controller
{
    public function daftarPurchaseOrderForm(Request $request) {
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

            $sql = DB::table('pof')->lock('with (nolock)')
                    ->selectRaw("isnull(pof.no_pof, '') as nomor_pof")
                    ->leftJoin(DB::raw('salesman with (nolock)'), function($join) {
                        $join->on('salesman.kd_sales', '=', 'pof.kd_sales')
                            ->on('salesman.companyid', '=', 'pof.companyid');
                        })
                    ->leftJoin(DB::raw('superspv with (nolock)'), function($join) {
                        $join->on('superspv.kd_spv', '=', 'salesman.spv')
                            ->on('superspv.companyid', '=', 'pof.companyid');
                        })
                    ->whereYear('pof.tgl_pof', $request->get('year'))
                    ->whereMonth('pof.tgl_pof', $request->get('month'))
                    ->where('pof.companyid', trim($request->get('companyid')))
                    ->orderBy('pof.companyid', 'desc')
                    ->orderBy('pof.approve', 'asc')
                    ->orderBy('pof.tgl_pof', 'desc')
                    ->orderBy('pof.jam', 'desc')
                    ->orderBy('pof.no_pof', 'desc');

            if(strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                $sql->where('pof.kd_dealer', $request->get('user_id'));
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql->where('pof.kd_sales', $request->get('user_id'));
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql->where('superspv.nm_spv', $request->get('user_id'));
            }

            if(!empty($request->get('kode_dealer'))) {
                $sql->where('pof.kd_dealer', $request->get('kode_dealer'));
            }

            if(!empty($request->get('kode_sales'))) {
                $sql->where('pof.kd_sales', $request->get('kode_sales'));
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
            $data_no_pof = '';
            $data_purchase_order = new Collection();

            foreach ($data as $record) {
                $jumlah_data = (double)$jumlah_data + 1;

                if(trim($data_no_pof) == '') {
                    $data_no_pof .= "'".trim($record->nomor_pof)."'";
                } else {
                    $data_no_pof .= ",'".trim($record->nomor_pof)."'";
                }
            }

            if((double)$jumlah_data > 0) {
                $sql = "select	isnull(pof.companyid, '') as companyid, isnull(pof.no_pof, '') as nomor_pof,
                                isnull(pof.tgl_pof, '') as tanggal_pof, isnull(pof.jam, '') as jam,
                                isnull(pof.approve, 0) as approve, isnull(pof.pmo, 0) as pmo,
                                isnull(pof.pmo_web, 0) as pmo_web, isnull(pof.no_order, '') as no_order,
                                isnull(pof.kd_sales, '') as kode_sales, isnull(salesman.nm_sales, '') as nama_sales,
                                isnull(pof.kd_dealer, '') as kode_dealer, isnull(dealer.nm_dealer, '') as nama_dealer,
                                isnull(pof.ket, '') as keterangan, isnull(pof.kd_tpc, '') as kode_tpc,
                                isnull(pof.umur_pof, 0) as umur_pof, isnull(pof.tgl_akhir_pof, '') as tanggal_akhir_pof,
                                isnull(pof.bo, '') as bo, isnull(pof.sts_fakt, 0) as status_faktur,
                                sum(isnull(pof_dtl.jml_order, 0)) as total_order,
                                sum(isnull(pof_dtl.terlayani, 0)) as total_terlayani
                        from
                        (
                            select	pof.companyid, pof.no_pof, pof.tgl_pof, pof.tgl_entry, pof.jam,
                                    pof.approve, pof.pmo, pof.pmo_web, pof.kd_sales, pof.kd_dealer,
                                    pof.ket, pof.kd_tpc, pof.umur_pof, pof.tgl_akhir_pof, pof.bo,
                                    pof.sts_fakt, pof.on_faktur, pof.no_order
                            from	pof with (nolock)
                            where	pof.no_pof in (".$data_no_pof.") and pof.companyid=?
                        )	pof
                                left join salesman with (nolock) on pof.kd_sales=salesman.kd_sales and
                                            pof.companyid=salesman.companyid
                                left join dealer with (nolock) on pof.kd_dealer=dealer.kd_dealer and
                                            pof.companyid=dealer.companyid
                                left join pof_dtl with (nolock) on pof.no_pof=pof_dtl.no_pof and
                                            pof.companyid=pof_dtl.companyid
                        group by pof.companyid, pof.no_pof, pof.tgl_pof, pof.tgl_entry, pof.jam,
                                    pof.approve, pof.pmo, pof.pmo_web, pof.kd_sales, pof.kd_dealer,
                                    pof.ket, pof.kd_tpc, pof.umur_pof, pof.tgl_akhir_pof, pof.bo,
                                    pof.sts_fakt, pof.on_faktur, pof.no_order, salesman.nm_sales,
                                    dealer.nm_dealer
                        order by pof.companyid desc, pof.approve asc, pof.tgl_pof desc, pof.jam desc, pof.no_pof desc";

                $result = DB::select($sql, [ $request->get('companyid') ]);

                foreach($result as $data) {
                    $data_purchase_order->push((object) [
                        'nomor_pof'         => strtoupper(trim($data->nomor_pof)),
                        'tanggal'           => trim($data->tanggal_pof),
                        'jam'               => trim($data->jam),
                        'approve'           => trim($data->approve),
                        'pmo'               => trim($data->pmo),
                        'pmo_web'           => trim($data->pmo_web),
                        'no_order'          => trim($data->no_order),
                        'kode_sales'        => trim($data->kode_sales),
                        'nama_sales'        => trim($data->nama_sales),
                        'kode_dealer'       => trim($data->kode_dealer),
                        'nama_dealer'       => trim($data->nama_dealer),
                        'keterangan'        => trim($data->keterangan),
                        'kode_tpc'          => trim($data->kode_tpc),
                        'umur_pof'          => trim($data->umur_pof),
                        'tanggal_akhir_pof' => trim($data->tanggal_akhir_pof),
                        'bo'                => trim($data->bo),
                        'status_faktur'     => trim($data->status_faktur),
                        'total_order'       => (double)$data->total_order,
                        'total_terlayani'   => (double)$data->total_terlayani,
                    ]);
                }
            }

            $purchase_order = [
                'current_page'  => $current_page,
                'data'          => $data_purchase_order,
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

            return Response::responseSuccess('success', $purchase_order);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function daftarPurchaseOrderFormDetail(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_pof' => 'required|string',
                'user_id'   => 'required|string',
                'companyid' => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih nomor purchase order terlebih dahulu");
            }

            $sql = "select	isnull(poftmp.companyid, '') as companyid, isnull(poftmp.no_pof, '') as nomor_pof, isnull(poftmp.kd_tpc, '') as kode_tpc,
                            isnull(poftmp.disc, 0) as disc_header, isnull(poftmp.total, 0) as total, isnull(pof_dtltmp.kd_part, '') as part_number,
                            isnull(part.ket, '') as nama_part, isnull(produk.kd_produk, '') as kode_produk, isnull(produk.nama, '') as produk,
                            isnull(pof_dtltmp.jml_order, 0) as jml_order, isnull(pof_dtltmp.terlayani, 0) as terlayani,
                            isnull(pof_dtltmp.harga, 0) as harga, isnull(part.het, 0) as het, isnull(pof_dtltmp.disc1, 0) as disc_detail,
                            isnull(pof_dtltmp.jumlah, 0) as total_detail, isnull(bo.jumlah, 0) as jumlah_bo,
                            isnull(discp.discp_default, 0) as disc_produk, isnull(part.hrg_pokok, 0) as harga_pokok,
                            isnull(poftmp.sts_fakt, 0) as status_faktur, isnull(poftmp.approve, 0) as approve
                    from
                    (
                        select	top 1 poftmp.companyid, poftmp.kd_key, poftmp.no_pof, poftmp.kd_dealer, poftmp.kd_tpc,
                                poftmp.disc, poftmp.total, poftmp.sts_fakt, poftmp.approve
                        from	poftmp
                        where	poftmp.kd_key=? and poftmp.no_pof=? and poftmp.companyid=?
                    )	poftmp
                            left join company with (nolock) on poftmp.companyid=company.companyid
                            inner join pof_dtltmp with (nolock) on poftmp.kd_key=pof_dtltmp.kd_key and
                                            poftmp.no_pof=pof_dtltmp.no_pof and poftmp.companyid=pof_dtltmp.companyid
                            left join part with (nolock) on pof_dtltmp.kd_part=part.kd_part and poftmp.companyid=part.companyid
                            left join sub with (nolock) on part.kd_sub=sub.kd_sub
                            left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                            left join discp with (nolock) on produk.kd_produk=discp.kd_produk and
                                        iif(isnull(company.inisial, 0)=1, 'RK', 'PC')=discp.cabang
                            left join bo with (nolock) on pof_dtltmp.kd_part=bo.kd_part and poftmp.kd_dealer=bo.kd_dealer and
                                        poftmp.companyid=bo.companyid
                    order by pof_dtltmp.usertime desc";

            $result = DB::select($sql, [ $request->get('user_id'), $request->get('nomor_pof'), $request->get('companyid') ]);

            $total_data = 0;
            $nomor_pof = 'XX';
            $total_order = 0;
            $total_terlayani = 0;
            $sub_total = 0;
            $disc_header = 0;
            $grand_total = 0;
            $approve = 0;
            $status_faktur = 0;
            $data_pof_detail = new Collection();

            foreach($result as $data) {
                $total_data = $total_data + 1;

                $nomor_pof = trim($data->nomor_pof);
                $disc_header = $data->disc_header;
                $grand_total = $data->total;
                $approve = $data->approve;
                $status_faktur = $data->status_faktur;
                $total_order = $total_order + $data->jml_order;
                $total_terlayani = $total_terlayani + $data->terlayani;
                $sub_total = $sub_total + $data->total_detail;

                $keterangan_bo = '';
                $keterangan_harga = '';
                $keterangan_disc_produk = '';
                $keterangan_penjualan_rugi = '';
                $keterangan_discount_2x = '';
                $keterangan_discount_tpc20 = '';

                if((double)$data->jumlah_bo > 0) {
                    $keterangan_bo = 'SUDAH ADA DI BO '.number_format($data->jumlah_bo).' PCS';
                }

                if((double)$data->disc_produk > 0) {
                    if((double)$data->disc_header > 0 && (double)$data->disc_header > (double)$data->disc_produk) {
                        $keterangan_disc_produk = 'DISKON PRODUK '.$data->kode_produk.' MAKSIMAL '.number_format($data->disc_produk, 2);
                    }
                }

                if($data->kode_tpc == '14') {
                    if((double)$data->disc_header > 0 && (double)$data->disc_detail > 0) {
                        $keterangan_discount_2x = "PART NUMBER DI DISCOUNT 2x";
                    }
                }

                if($data->kode_tpc == '20') {
                    if((double)$data->disc_header > 0 || (double)$data->disc_detail > 0) {
                        $keterangan_discount_tpc20 = "ADA DISCOUNT DI TPC 20";
                    }
                }

                if($data->kode_tpc == '14') {
                    $harga_netto = (double)$data->het - (((double)$data->het * (double)$data->disc_detail) / 100) - (((double)$data->het * (double)$data->disc_header) / 100);

                    if((double)$harga_netto < (double)$data->harga_pokok) {
                        $keterangan_penjualan_rugi = 'PENJUALAN RUGI';
                    }
                } else {
                    $harga_netto = (double)$data->harga;

                    if((double)$harga_netto < (double)$data->harga_pokok) {
                        $keterangan_penjualan_rugi = 'PENJUALAN RUGI';
                    }
                }

                if($data->kode_tpc == '20') {
                    if((double)$data->het == (double)$data->harga) {
                        $keterangan_harga = 'HARGA MASIH SAMA DENGAN HET';
                    } elseif((double)$data->het < (double)$data->harga) {
                        $keterangan_harga = 'HARGA LEBIH BESAR DARI HET';
                    }
                }

                $data_pof_detail->push((object) [
                    'nomor_pof'     => trim($data->nomor_pof),
                    'part_number'   => trim($data->part_number),
                    'nama_part'     => trim($data->nama_part),
                    'jml_order'     => (double)$data->jml_order,
                    'terlayani'     => (double)$data->terlayani,
                    'het'           => (double)$data->het,
                    'harga'         => (double)$data->harga,
                    'disc_detail'   => (double)$data->disc_detail,
                    'total_detail'  => (double)$data->total_detail,
                    'image_part'    => trim(config('constants.api.url.images')).'/'.trim($data->part_number).'.jpg',
                    'keterangan_bo' => trim($keterangan_bo),
                    'keterangan_disc_produk' => trim($keterangan_disc_produk),
                    'keterangan_harga' => trim($keterangan_harga),
                    'keterangan_penjualan_rugi' => trim($keterangan_penjualan_rugi),
                    'keterangan_disc_2x' => trim($keterangan_discount_2x),
                    'keterangan_disc_tpc20' => $keterangan_discount_tpc20
                ]);
            }

            $data_pof = new Collection();
            $data_pof->push((object) [
                'nomor_pof'         => $nomor_pof,
                'approve'           => (double)$approve,
                'status_faktur'     => (double)$status_faktur,
                'total_order'       => (double)$total_order,
                'total_terlayani'   => (double)$total_terlayani,
                'sub_total'         => (double)$sub_total,
                'disc_header'       => (double)$disc_header,
                'grand_total'       => (double)$grand_total,
                'data_pof_detail'   => $data_pof_detail
            ]);

            return Response::responseSuccess('success', $data_pof->first());
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function detailPurchaseOrderForm(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_pof' => 'required|string',
                'role_id'   => 'required|string',
                'user_id'   => 'required|string',
                'companyid' => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning('Pilih nomor purchase order terlebih dahulu');
            }

            $sql = DB::table('pof')->lock('with (nolock)')
                    ->select('pof.no_pof','pof.kd_sales','pof.kd_dealer','superspv.nm_spv')
                    ->leftJoin(DB::raw('salesman with (nolock)'), function($join) {
                        $join->on('salesman.kd_sales', '=', 'pof.kd_sales')
                            ->on('salesman.companyid', '=', 'pof.companyid');
                        })
                    ->leftJoin(DB::raw('superspv with (nolock)'), function($join) {
                        $join->on('superspv.kd_spv', '=', 'salesman.spv')
                            ->on('superspv.companyid', '=', 'pof.companyid');
                        })
                    ->where('pof.no_pof', $request->get('nomor_pof'))
                    ->where('pof.companyid', $request->get('companyid'));


            if(strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                $sql->where('pof.kd_dealer', $request->get('user_id'));
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $sql->where('pof.kd_sales', $request->get('user_id'));
            } elseif(strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                $sql->where('superspv.nm_spv', $request->get('user_id'));
            }

            $result = $sql->first();

            if(empty($result->no_pof)) {
                return Response::responseWarning('Nomor Pof yang anda cari tidak ditemukan');
            } else {
                DB::transaction(function () use ($request) {
                    DB::insert('exec SP_PofDtlTransferData ?,?,?', [
                        trim(strtoupper($request->get('user_id'))), trim(strtoupper($request->get('nomor_pof'))),
                        trim(strtoupper($request->get('companyid')))
                    ]);
                });
            }

            $sql = DB::table('poftmp')->lock('with (nolock)')
                    ->selectRaw("isnull(poftmp.companyid, '') as companyid, isnull(poftmp.no_pof, '') as nomor_pof,
                                isnull(poftmp.tgl_pof, '') as tanggal_pof, isnull(poftmp.kd_sales, '') as kode_sales,
                                isnull(poftmp.kd_dealer, '') as kode_dealer, isnull(poftmp.kd_tpc, '') as kode_tpc,
                                isnull(poftmp.umur_pof, 0) as umur_pof, isnull(poftmp.bo, '') as bo,
                                isnull(poftmp.ket, '') as keterangan, isnull(poftmp.disc, 0) as disc_header,
                                isnull(poftmp.total, 0) as total, isnull(poftmp.approve, 0) as approve,
                                isnull(poftmp.sts_fakt, 0) as status_faktur,
                                isnull(poftmp.appr_usr,'') as approve_user")
                    ->where('poftmp.kd_key', $request->get('user_id'))
                    ->where('poftmp.no_pof', $request->get('nomor_pof'))
                    ->first();

            if(empty($sql->nomor_pof)) {
                return Response::responseWarning('Nomor Pof tidak terisi ke tabel temporary, coba kembali');
            } else {
                return Response::responseSuccess('success', $sql);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function editDiscountPurchaseOrderForm(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_pof'     => 'required|string',
                'user_id'       => 'required|string',
                'companyid'     => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih data purchase order terlebih dahulu");
            }

            $sql = DB::table('poftmp')->lock('with (nolock)')
                    ->selectRaw("isnull(poftmp.no_pof, '') as nomor_pof, isnull(poftmp.disc, 0) as discount,
                                isnull(poftmp.kd_tpc, '') as kode_tpc")
                    ->where('poftmp.kd_key', trim($request->get('user_id')))
                    ->where('poftmp.no_pof', trim($request->get('nomor_pof')))
                    ->where('poftmp.companyid', trim($request->get('companyid')))
                    ->first();

            if(empty($sql->nomor_pof)) {
                return Response::responseWarning("Nomor pof yang anda pilih belum masuk ke dalam temporary, lakukan refresh halaman");
            } else {
                if((double)$sql->kode_tpc == 20) {
                    return Response::responseWarning("Data purchase order ini terdaftar di TPC 20, TPC 20 tidak dapat mengisi diskon");
                } else {
                    $data = [
                        'nomor_pof'     => strtoupper(trim($sql->nomor_pof)),
                        'tpc'           => strtoupper(trim($sql->kode_tpc)),
                        'discount'      => (double)$sql->discount,
                    ];
                }

                return Response::responseSuccess('success', $data);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function updateTpcPurchaseOrderForm(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_pof'     => 'required|string',
                'kode_tpc'      => 'required|string',
                'user_id'       => 'required|string',
                'companyid'     => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih nomor pof dan kode tpc terlebih dahulu");
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_PofTmp_UpdateTpc ?,?,?,?', [
                    trim(strtoupper($request->get('user_id'))), trim(strtoupper($request->get('nomor_pof'))),
                    $request->get('kode_tpc'), trim(strtoupper($request->get('companyid')))
                ]);
            });

            return Response::responseSuccess('Data TPC berhasil diubah', null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function updateDiscountPurchaseOrderForm(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_pof'     => 'required|string',
                'discount'      => 'required',
                'user_id'       => 'required|string',
                'companyid'     => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih nomor pof dan isi jumlah diskon terlebih dahulu");
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_PofDtlTmp_UpdateDiscount ?,?,?,?', [
                    trim(strtoupper($request->get('user_id'))), trim(strtoupper($request->get('nomor_pof'))),
                    (double)$request->get('discount'), trim(strtoupper($request->get('companyid')))
                ]);
            });

            return Response::responseSuccess('Data diskon berhasil diubah', null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function partPurchaseOrderFormPartEdit(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_pof'     => 'required|string',
                'part_number'   => 'required|string',
                'user_id'       => 'required|string',
                'companyid'     => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih part number purchase order terlebih dahulu");
            }

            $sql = DB::table('poftmp')->lock('with (nolock)')
                    ->selectRaw("isnull(pof_dtltmp.kd_part, '') as part_number, isnull(part.ket, '') as nama_part,
                                isnull(produk.nama, '') as produk, isnull(pof_dtltmp.jml_order, 0) as jml_order,
                                isnull(pof_dtltmp.harga, 0) as harga, isnull(pof_dtltmp.disc1, 0) as disc_detail,
                                isnull(pof_dtltmp.jumlah, 0) as total, isnull(poftmp.kd_tpc, '') as kode_tpc")
                    ->leftJoin(DB::raw('pof_dtltmp with (nolock)'), function($join) {
                        $join->on('poftmp.kd_key', '=', 'pof_dtltmp.kd_key')
                            ->on('poftmp.no_pof', '=', 'pof_dtltmp.no_pof')
                            ->on('poftmp.companyid', '=', 'pof_dtltmp.companyid');
                        })
                    ->leftJoin(DB::raw('part with (nolock)'), function($join) {
                        $join->on('part.kd_part', '=', 'pof_dtltmp.kd_part')
                            ->on('part.companyid', '=', 'poftmp.companyid');
                        })
                    ->leftJoin(DB::raw('sub with (nolock)'), function($join) {
                        $join->on('sub.kd_sub', '=', 'part.kd_sub');
                        })
                    ->leftJoin(DB::raw('produk with (nolock)'), function($join) {
                        $join->on('produk.kd_produk', '=', 'sub.kd_produk');
                        })
                    ->where('poftmp.kd_key', trim($request->get('user_id')))
                    ->where('poftmp.no_pof', trim($request->get('nomor_pof')))
                    ->where('pof_dtltmp.kd_part', trim($request->get('part_number')))
                    ->where('poftmp.companyid', trim($request->get('companyid')))
                    ->first();

            if(empty($sql->part_number)) {
                return Response::responseWarning("Part number yang anda pilih tidak terdaftar di nomor pof ini, lakukan refresh halaman");
            } else {
                $data = [
                    'part_number'   => strtoupper(trim($sql->part_number)),
                    'nama_part'     => strtoupper(trim($sql->nama_part)),
                    'produk'        => strtoupper(trim($sql->produk)),
                    'tpc'           => strtoupper(trim($sql->kode_tpc)),
                    'jml_order'     => (double)$sql->jml_order,
                    'harga'         => (double)$sql->harga,
                    'disc_detail'   => (double)$sql->disc_detail,
                    'total'         => (double)$sql->total,
                ];
                return Response::responseSuccess('success', $data);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function partPurchaseOrderFormPartSimpan(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_pof'     => 'required|string',
                'part_number'   => 'required|string',
                'jml_order'     => 'required',
                'harga'         => 'required',
                'discount'      => 'required',
                'user_id'       => 'required|string',
                'companyid'     => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih part number purchase order terlebih dahulu");
            }

            if(empty($request->get('jml_order')) || $request->get('jml_order') == '' || $request->get('jml_order') <= 0) {
                return Response::responseWarning("Jumlah order harus lebih besar dari 0 (nol)");
            }

            if(empty($request->get('harga')) || $request->get('harga') == '' || $request->get('harga') <= 0) {
                return Response::responseWarning("Harga jual harus lebih besar dari 0 (nol)");
            }

            $part_number = '';
            $nama_part = '';

            $sql = DB::table('part')->lock('with (nolock)')
                    ->selectRaw("isnull(part.kd_part, '') as part_number,
                                isnull(part.ket, '') as nama_part")
                    ->where('part.kd_part', $request->get('part_number'))
                    ->where('part.companyid', $request->get('companyid'))
                    ->first();

            if(empty($sql->part_number)) {
                return Response::responseWarning("Part number yang anda entry tidak terdaftar");
            } else {
                $part_number = strtoupper(trim($sql->part_number));
                $nama_part = strtoupper(trim($sql->nama_part));
            }

            $sql = DB::table('poftmp')->lock('with (nolock)')
                    ->selectRaw("isnull(poftmp.no_pof, '') as nomor_pof, isnull(poftmp.tgl_pof, '') as tanggal_pof,
                                isnull(poftmp.tgl_entry, '') as tanggal_entry, isnull(poftmp.jam, '') as jam_entry,
                                isnull(poftmp.kd_dealer, '') as kode_dealer, isnull(poftmp.kd_sales, '') as kode_sales,
                                isnull(poftmp.umur_pof, 0) as umur_pof, isnull(poftmp.tgl_akhir_pof, '') as tanggal_akhir_pof,
                                isnull(poftmp.kd_tpc, '') as kode_tpc, isnull(poftmp.bo, '') as bo, isnull(poftmp.disc, 0) as discount,
                                isnull(poftmp.total, 0) as total, isnull(poftmp.sts_fakt, 0) as sts_faktur,
                                isnull(poftmp.approve, 0) as approve, isnull(poftmp.appr_usr, '') as approve_user,
                                isnull(poftmp.ket, '') as keterangan")
                    ->where('poftmp.kd_key', trim($request->get('user_id')))
                    ->where('poftmp.no_pof', trim($request->get('nomor_pof')))
                    ->where('poftmp.companyid', trim($request->get('companyid')))
                    ->first();

            if(empty($sql->nomor_pof)) {
                return Response::responseWarning("Nomor pof yang anda pilih belum masuk ke dalam temporary, lakukan refresh halaman");
            } else {
                DB::transaction(function () use ($request, $sql, $part_number, $nama_part) {
                    DB::insert('exec SP_PofDtlTmpInsNew ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
                        trim(strtoupper($request->get('user_id'))), trim(strtoupper($request->get('nomor_pof'))), date('d-m-Y', strtotime($sql->tanggal_pof)),
                        date('d-m-Y', strtotime($sql->tanggal_entry)), $sql->jam_entry, $sql->kode_dealer, $sql->kode_sales, (double)$sql->umur_pof, date('d-m-Y', strtotime($sql->tanggal_akhir_pof)),
                        $sql->kode_tpc, $sql->bo, (double)$sql->discount, (double)$sql->total, $sql->sts_faktur, $sql->approve, $sql->approve_user, $sql->keterangan,
                        '','','','','', $part_number, $nama_part, (double)$request->get('jml_order'), (double)$request->get('harga'), (double)$request->get('discount'),
                        ((double)$request->get('harga') * (double)$request->get('jml_order')) - round(((((double)$request->get('harga') * (double)$request->get('jml_order')) * (double)$request->get('discount')) / 100), 0),
                        0, 0, trim(strtoupper($request->get('user_id'))), trim(strtoupper($request->get('companyid')))
                    ]);
                });

                return Response::responseSuccess('Part number '.strtoupper(trim($request->get('part_number'))).' berhasil diproses', null);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function partPurchaseOrderFormPartHapus(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_pof'     => 'required|string',
                'part_number'   => 'required|string',
                'user_id'       => 'required|string',
                'companyid'     => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih part number purchase order yang akan dihapus terlebih dahulu");
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_PofDtlTmpDelNew ?,?,?,?', [
                    trim(strtoupper($request->get('user_id'))), trim(strtoupper($request->get('nomor_pof'))),
                    strtoupper(trim($request->get('part_number'))), trim(strtoupper($request->get('companyid')))
                ]);
            });

            return Response::responseSuccess('Part number '.strtoupper(trim($request->get('part_number'))).' berhasil dihapus', null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function fakturPurchaseOrderForm(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_pof' => 'required|string',
                'part_number' => 'required|string',
                'companyid' => 'required|string'
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Data tahun dan bulan harus terisi");
            }

            $sql = DB::table('faktur')->lock('with (nolock)')
                    ->selectRaw("isnull(faktur.no_faktur, '') as nomor_faktur,
                                    isnull(convert(varchar(10), cast(faktur.tgl_faktur as date), 105), '') as tanggal,
                                 isnull(fakt_dtl.jml_jual, 0) as jml_item")
                    ->leftJoin(DB::raw('fakt_dtl with (nolock)'), function($join) {
                        $join->on('fakt_dtl.no_faktur', '=', 'faktur.no_faktur')
                            ->on('fakt_dtl.companyid', '=', 'faktur.companyid');
                        })
                    ->where('fakt_dtl.no_pof', trim($request->get('nomor_pof')))
                    ->where('fakt_dtl.kd_part', trim($request->get('part_number')))
                       ->where('faktur.companyid', trim($request->get('companyid')))
                       ->where('fakt_dtl.jml_jual', '>', 0)
                    ->orderBy('faktur.no_faktur', 'asc')
                    ->get();

            $data_pof_terlayani = [];
            $jumlah_data = 0;

            foreach($sql as $data) {
                $jumlah_data = $jumlah_data + 1;
                $data_pof_terlayani[] = [
                    'nomor_faktur'  => $data->nomor_faktur,
                    'tanggal'       => $data->tanggal,
                    'jml_item'      => $data->jml_item,
                ];
            }

            if($jumlah_data == 0) {
                return Response::responseWarning('Data tidak ditemukan');
            } else {
                return Response::responseSuccess('success', $data_pof_terlayani);
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function simpanPurchaseOrderForm(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_pof'     => 'required|string',
                'kode_sales'    => 'required|string',
                'kode_dealer'   => 'required|string',
                'kode_tpc'      => 'required|string',
                'umur_pof'      => 'required|string',
                'bo'            => 'required|string',
                'approve'       => 'required|string',
                'user_id'       => 'required|string',
                'companyid'     => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Kode sales, kode dealer, kode tpc, umur_pof, dan status bo tidak boleh kosong");
            }

            $user_level = '';
            $sql = DB::table('tbuser')
                    ->select('user_level')
                    ->where('user_id', $request->get('user_id'))
                    ->first();

            if(!empty($sql->user_level)) {
                $user_level = strtoupper(trim($sql->user_level));
            }

            $sql = DB::table('poftmp')->lock('with (nolock)')
                    ->selectRaw("isnull(company.kd_file, '') as kode_file, isnull(poftmp.no_pof, '') as nomor_pof,
                                isnull(poftmp.kd_tpc, '') as kode_tpc, isnull(pof_dtltmp.kd_part, '') as part_number,
                                isnull(pof_dtltmp.jml_order, 0) as jml_order, isnull(pof_dtltmp.harga, 0) as harga,
                                isnull(pof_dtltmp.disc1, 0) as disc_detail, isnull(poftmp.disc, 0) as disc_header,
                                isnull(part.hrg_netto, 0) as hrg_netto,
                                isnull(pof_dtltmp.harga, 0) -
                                    round(((isnull(pof_dtltmp.harga, 0) * isnull(pof_dtltmp.disc1, 0)) / 100), 0) -
                                        round(((isnull(pof_dtltmp.harga, 0) * isnull(poftmp.disc, 0)) / 100), 0) as harga_netto,
                                isnull(part.het, 0) as het, isnull(part.hrg_pokok, 0) as harga_pokok, isnull(discp.discp_default, 0) as disc_produk")
                    ->leftJoin(DB::raw('company with (nolock)'), function($join) {
                        $join->on('company.companyid', '=', 'poftmp.companyid');
                        })
                    ->leftJoin(DB::raw('pof_dtltmp with (nolock)'), function($join) {
                        $join->on('pof_dtltmp.kd_key', '=', 'poftmp.kd_key')
                            ->on('pof_dtltmp.no_pof', '=', 'poftmp.no_pof')
                            ->on('pof_dtltmp.companyid', '=', 'poftmp.companyid');
                        })
                    ->leftJoin(DB::raw('part with (nolock)'), function($join) {
                        $join->on('part.kd_part', '=', 'pof_dtltmp.kd_part')
                            ->on('part.companyid', '=', 'poftmp.companyid');
                        })
                    ->leftJoin(DB::raw('sub with (nolock)'), function($join) {
                        $join->on('sub.kd_sub', '=', 'part.kd_sub');
                        })
                    ->leftJoin(DB::raw('produk with (nolock)'), function($join) {
                        $join->on('produk.kd_produk', '=', 'sub.kd_produk');
                        })
                    ->leftJoin(DB::raw('discp with (nolock)'), function($join) {
                        $join->on('produk.kd_produk', '=', 'discp.kd_produk')
                            ->on(DB::raw("iif(isnull(company.inisial, 0)=1, 'RK', 'PC')"), '=', 'discp.cabang');
                        })
                    ->where('poftmp.kd_key', $request->get('user_id'))
                    ->where('poftmp.no_pof', $request->get('nomor_pof'))
                    ->where('poftmp.companyid', $request->get('companyid'))
                    ->get();

            $jumlah_data = 0;
            foreach($sql as $data) {
                $jumlah_data = (double)$jumlah_data + 1;

                if(trim($data->kode_tpc) == '14') {
                    if(strtoupper(trim($user_level)) != 'SUPERVISOR') {
                        if(strtoupper(trim($data->kode_file)) == 'A') {
                            if((double)$data->disc_header > 0 && (double)$data->disc_detail) {
                                return Response::responseWarning("Part number ".strtoupper(trim($data->part_number))." di diskon 2x");
                            }

                            if((double)$data->disc_header > 0) {
                                if((double)$data->disc_produk > 0) {
                                    if((double)$data->disc_header > (double)$data->disc_produk) {
                                        return Response::responseWarning("Part number ".strtoupper(trim($data->part_number))." diskon maksimal ".$data->disc_produk.". Diskon saat ini ".$data->disc_header);
                                    }
                                }
                            } else {
                                if((double)$data->disc_produk > 0) {
                                    if((double)$data->disc_detail > (double)$data->disc_produk) {
                                        return Response::responseWarning("Part number ".strtoupper(trim($data->part_number))." diskon maksimal ".$data->disc_produk.". Diskon saat ini ".$data->disc_detail);
                                    }
                                }
                            }
                        }

                        if((double)$data->hrg_netto > (double)$data->het) {
                            return Response::responseWarning("Part number ".strtoupper(trim($data->part_number))." harga melebihi harga HET");
                        }

                        if((double)$data->harga_pokok > (double)$data->harga_netto) {
                            return Response::responseWarning("Part number ".strtoupper(trim($data->part_number))." penjualan rugi, anda tidak memiliki akses menjual rugi");
                        }
                    }

                } else {
                    if(strtoupper(trim($user_level)) != 'SUPERVISOR') {
                        if((double)$data->harga_pokok > (double)$data->harga) {
                            return Response::responseWarning("Part number ".strtoupper(trim($data->part_number))." penjualan rugi, anda tidak memiliki akses menjual rugi");
                        }
                    }
                }
            }

            if($jumlah_data <= 0) {
                return Response::responseWarning("Data detail purchase order tidak ditemukan di temporary, lakukan refresh halaman");
            }

            $sql = DB::table('poftmp')->lock('with (nolock)')
                        ->selectRaw("isnull(poftmp.no_pof, '') as nomor_pof, isnull(poftmp.tgl_pof, '') as tanggal_pof,
                                    isnull(poftmp.tgl_entry, '') as tanggal_entry, isnull(poftmp.jam, '') as jam_entry,
                                    isnull(poftmp.kd_dealer, '') as kode_dealer, isnull(poftmp.kd_sales, '') as kode_sales,
                                    isnull(poftmp.umur_pof, 0) as umur_pof, isnull(poftmp.tgl_akhir_pof, '') as tanggal_akhir_pof,
                                    isnull(poftmp.kd_tpc, '') as kode_tpc, isnull(poftmp.bo, '') as bo, isnull(poftmp.disc, 0) as discount,
                                    isnull(poftmp.total, 0) as total, isnull(poftmp.sts_fakt, 0) as sts_faktur,
                                    isnull(poftmp.approve, 0) as approve, isnull(poftmp.appr_usr, '') as approve_user,
                                    isnull(poftmp.ket, '') as keterangan")
                        ->where('poftmp.kd_key', trim($request->get('user_id')))
                        ->where('poftmp.no_pof', trim($request->get('nomor_pof')))
                        ->where('poftmp.companyid', trim($request->get('companyid')))
                        ->first();

            if(empty($sql->nomor_pof)) {
                return Response::responseWarning("Data purchase order tidak ditemukan di temporary, lakukan refresh halaman");
            } else {
                $tanggal_entry = Carbon::createFromFormat('Y-m-d', $sql->tanggal_entry);
                $umur_pof = (double)$sql->umur_pof;
                $tanggal_akhir_pof = $tanggal_entry->addDays($umur_pof);

                DB::transaction(function () use ($request, $sql, $tanggal_akhir_pof) {
                    DB::insert('exec SP_Pof_Simpan_New ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
                        trim(strtoupper($request->get('user_id'))), trim(strtoupper($request->get('nomor_pof'))), trim(strtoupper($request->get('nomor_pof'))),
                        date('d-m-Y', strtotime($sql->tanggal_pof)), date('d-m-Y', strtotime($sql->tanggal_entry)), $sql->jam_entry, $sql->kode_dealer, $sql->kode_sales,
                        (int)$request->get('umur_pof'), date('d-m-Y', strtotime($tanggal_akhir_pof)), (int)$request->get('kode_tpc'),
                        $request->get('bo'), (double)$sql->discount, (double)$sql->total,
                        (int)$sql->sts_faktur, strtoupper(trim($request->get('user_id'))), trim(strtoupper($request->get('companyid'))), trim($request->get('keterangan')),
                        '','','','','','',''
                    ]);
                });

                if($request->get('approve') == 2) {
                    DB::transaction(function () use ($request) {
                        DB::insert('exec SP_Pof_Approve ?,?,?', [
                            trim(strtoupper($request->get('nomor_pof'))), strtoupper(trim($request->get('user_id'))),
                            trim(strtoupper($request->get('companyid')))
                        ]);
                    });
                }

                if($request->get('approve') == 2) {
                    return Response::responseSuccess('Data Berhasil Disimpan dan Di Approve', null);
                } else {
                    return Response::responseSuccess('Data Berhasil Disimpan', null);
                }
            }
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function batalApprovePurchaseOrderForm(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'nomor_pof'     => 'required|string',
                'companyid'     => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih nomor purchase order form terlebih dahulu");
            }

            $sql = DB::table('pof')->lock('with (nolock)')
                    ->selectRaw("isnull(pof.no_pof, '') as nomor_pof, isnull(pof.sts_fakt, 0) as status_faktur_pof,
                                isnull(pof_dtl.kd_part, '') as part_number, isnull(pof_dtl.sts_fakt, 0) as status_faktur_part")
                    ->leftJoin(DB::raw('pof_dtl with (nolock)'), function($join) {
                        $join->on('pof_dtl.no_pof', '=', 'pof.no_pof')
                            ->on('pof_dtl.companyid', '=', 'pof.companyid');
                        })
                    ->where('pof.no_pof', $request->get('nomor_pof'))
                    ->where('pof.companyid', $request->get('companyid'))
                    ->get();

            foreach($sql as $data) {
                if((int)$data->status_faktur_part == 1) {
                    return Response::responseWarning("Ada beberapa part number yg sudah di proses faktur, Nomor pof tidak bisa dibatalkan");
                }

                if((int)$data->status_faktur_pof == 1) {
                    return Response::responseWarning("Nomor pof sudah di proses faktur, Nomor pof tidak bisa dibatalkan");
                }
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_Pof_BatalApprove ?,?', [
                    trim(strtoupper($request->get('nomor_pof'))), trim(strtoupper($request->get('companyid')))
                ]);
            });
            return Response::responseSuccess("Status Approve Berhasil Di Batalkan");
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
