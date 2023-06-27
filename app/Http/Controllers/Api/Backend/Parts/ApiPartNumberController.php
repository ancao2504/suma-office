<?php

namespace App\Http\Controllers\Api\Backend\Parts;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;


class ApiPartNumberController extends Controller
{
    public function daftarPartNumber(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'user_id'   => 'required|string',
                'role_id'   => 'required|string',
                'companyid' => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Anda belum login");
            }

            $status_filter_produk = 0;

            if(!empty($request->get('level_produk')) && trim($request->get('level_produk')) != '') {
                $status_filter_produk = 1;
            }

            if(!empty($request->get('kode_produk')) && trim($request->get('kode_produk')) != '') {
                $status_filter_produk = 1;
            }

            $sql = DB::table('part')->lock('with (nolock)')
                    ->selectRaw("isnull(part.kd_part, '') as part_number")
                    ->where('part.companyid', $request->get('companyid'))
                    ->where('part.del_send', 0)
                    ->where('part.het', '>', 0)
                    ->orderBy('part.kd_part', 'asc');

            if((int)$status_filter_produk == 1) {
                $sql->leftJoin(DB::raw('sub with (nolock)'),
                    function($join) {
                        $join->on('sub.kd_sub', '=', 'part.kd_sub');
                    }
                );
                $sql->leftJoin(DB::raw('produk with (nolock)'),
                    function($join) {
                        $join->on('produk.kd_produk', '=', 'sub.kd_produk');
                    }
                );

                if(!empty($request->get('level_produk')) && trim($request->get('level_produk')) != '') {
                    if(strtoupper(trim($request->get('level_produk'))) == 'HANDLE') {
                        $sql->where('level', 'AHM')->where('kd_mkr', 'G');
                    } elseif(strtoupper(trim($request->get('level_produk'))) == 'NON_HANDLE') {
                        $sql->where('level', 'MPM')->where('kd_mkr', 'G');
                    } elseif(strtoupper(trim($request->get('level_produk'))) == 'TUBE') {
                        $sql->where('level', 'AHM')->where('kd_mkr', 'I');
                    } elseif(strtoupper(trim($request->get('level_produk'))) == 'OLI') {
                        $sql->where('level', 'AHM')->where('kd_mkr', 'J');
                    }
                }

                if(!empty($request->get('kode_produk')) && trim($request->get('kode_produk')) != '') {
                    $sql->where('produk.kd_produk', $request->get('kode_produk'));
                }
            }

            if(!empty($request->get('type_motor')) && trim($request->get('type_motor')) != '') {
                $sql->leftJoin(DB::raw('pvtm with (nolock)'),
                    function($join) {
                        $join->on('part.kd_part', '=', 'pvtm.kd_part')
                            ->on('part.companyid', '=', 'pvtm.companyid');
                    })
                    ->leftJoin(DB::raw('typemotor with (nolock)'),
                        function($join) {
                            $join->on('pvtm.typemkt', '=', 'typemotor.typemkt');
                        });

                $sql->where('typemotor.typemkt', $request->get('type_motor'));
            }

            if(!empty($request->get('part_number')) && $request->get('part_number') != '') {
                $sql->where('part.kd_part', 'like', $request->get('part_number').'%');
            }

            $sql = $sql->paginate($request->get('per_page') ?? 12);

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
            $list_part_number = '';
            $data_part_number = new Collection();

            foreach ($data as $record) {
                $jumlah_data = (double)$jumlah_data + 1;

                if (trim($list_part_number) == '') {
                    $list_part_number .= "'".trim($record->part_number)."'";
                } else {
                    $list_part_number .= ",'".trim($record->part_number)."'";
                }
            }

            if((double)$jumlah_data > 0) {
                $kode_dealer = '';
                $disc_dealer = 0;
                $disc_plus_dealer = 0;

                $sql = DB::select('exec SP_Dealer_DiscPMO ?,?,?', [
                            $request->get('user_id'), $request->get('role_id'), $request->get('companyid')
                        ]);

                foreach($sql as $data) {
                    $kode_dealer = strtoupper(trim($data->kode_dealer));
                    $disc_dealer = (double)$data->discount;
                    $disc_plus_dealer = (double)$data->discount_plus;
                }

                $sql = "select	isnull(part.kd_part, '') as part_number, isnull(part.ket, '') as nama_part,
                                isnull(part.frg, '') as frg, isnull(part.het, 0) as het, isnull(part.kelas, '') as kelas,
                                isnull(sub.kd_produk, '') as kode_produk, isnull(produk.nama, '') as nama_produk,
                                isnull(tbstlokasirak.stock, 0) -
                                    (isnull(stlokasi.min, 0) +
                                        isnull(part.kanvas, 0) + isnull(part.min_gudang, 0) + isnull(part.in_transit, 0) + isnull(part.min_htl, 0)) as stock,
                                iif(isnull(part.tpc20, '') <> 'Y', 1, 0) as status_discount,
                                cast(iif(isnull(part.tpc20, '')='Y', 0,
                                    iif(".(double)$disc_dealer." <= 0, 0,
                                        iif(isnull(discp.discp, 0) <= 0, ".(double)$disc_dealer.",
                                            iif(isnull(dealer_discp.kd_dealer, '')='',
                                                iif(".(double)$disc_dealer." > isnull(discp.discp_default, 0), isnull(discp.discp_default, 0), ".(double)$disc_dealer."),
                                                iif(".(double)$disc_dealer." > isnull(discp.discp, 0), isnull(discp.discp, 0), ".(double)$disc_dealer.")
                                            )
                                        )
                                    )
                                ) as decimal(5,2)) as discount,
                                cast(iif(isnull(part.tpc20, '')='Y', 0,
                                    iif(".(double)$disc_plus_dealer." <= 0, 0,
                                        iif(isnull(discp.discp_plus, 0) <= 0, ".(double)$disc_plus_dealer.",
                                            iif(isnull(dealer_discp.kd_dealer, '')='',
                                                iif(".(double)$disc_plus_dealer." > isnull(discp.discp_plus_default, 0), isnull(discp.discp_plus_default, 0), ".(double)$disc_plus_dealer."),
                                                iif(".(double)$disc_plus_dealer." > isnull(discp.discp_plus, 0), isnull(discp.discp_plus, 0), ".(double)$disc_plus_dealer.")
                                            )
                                        )
                                    )
                                ) as decimal(5,2)) as discount_plus,
                                cast(iif(isnull(part.tpc20, '')='Y',
                                iif(isnull(dealer_netto.kd_dealer, '') <> '',
                                    iif(isnull(dealer_netto.harga, 0) > 0, isnull(dealer_netto.harga, 0),
                                        iif(isnull(part.harga20, 0) > 0, isnull(part.harga20, 0), isnull(part.het, 0))
                                    ),
                                    iif(isnull(part.harga20, 0) > 0, isnull(part.harga20, 0), isnull(part.het, 0))
                                ),
                                isnull(part.het, 0) -
                                    round(((isnull(part.het, 0) *
                                        iif(".(double)$disc_dealer." <= 0, 0,
                                            iif(isnull(discp.discp, 0) <= 0, ".(double)$disc_dealer.",
                                                iif(isnull(dealer_discp.kd_dealer, '')='',
                                                    iif(".(double)$disc_dealer." > isnull(discp.discp_default, 0), isnull(discp.discp_default, 0), ".(double)$disc_dealer."),
                                                    iif(".(double)$disc_dealer." > isnull(discp.discp, 0), isnull(discp.discp, 0), ".(double)$disc_dealer.")
                                                )
                                            )
                                    )) / 100), 0) -
                                    round((((isnull(part.het, 0) -
                                        round(((isnull(part.het, 0) *
                                            iif(".(double)$disc_dealer." <= 0, 0,
                                                iif(isnull(discp.discp, 0) <= 0, ".(double)$disc_dealer.",
                                                    iif(isnull(dealer_discp.kd_dealer, '')='',
                                                        iif(".(double)$disc_dealer." > isnull(discp.discp_default, 0), isnull(discp.discp_default, 0), ".(double)$disc_dealer."),
                                                        iif(".(double)$disc_dealer." > isnull(discp.discp, 0), isnull(discp.discp, 0), ".(double)$disc_dealer.")
                                                    )
                                                )
                                            )) / 100), 0)) *
                                            iif(".(double)$disc_plus_dealer." <= 0, 0,
                                                iif(isnull(discp.discp_plus, 0) <= 0, ".(double)$disc_plus_dealer.",
                                                    iif(isnull(dealer_discp.kd_dealer, '')='',
                                                        iif(".(double)$disc_plus_dealer." > isnull(discp.discp_plus_default, 0), isnull(discp.discp_plus_default, 0), ".(double)$disc_plus_dealer."),
                                                        iif(".(double)$disc_plus_dealer." > isnull(discp.discp_plus, 0), isnull(discp.discp_plus, 0), ".(double)$disc_plus_dealer.")
                                                    )
                                                )
                                            )
                                    ) / 100), 0)
                                ) as decimal(13,0)) as harga_netto
                        from
                        (
                            select	part.companyid, part.kd_part, part.ket, part.frg, part.kelas,
                                    part.kd_sub, part.het, part.tpc20, part.harga20, part.kanvas,
                                    part.in_transit, part.min_gudang, part.min_htl
                            from	part with (nolock)
                            where	part.companyid=? and
                                    part.kd_part in (".$list_part_number.")
                        )	part
                                left join company with (nolock) on part.companyid=company.companyid
                                left join stlokasi with (nolock) on part.kd_part=stlokasi.kd_part and
                                            company.kd_lokasi=stlokasi.kd_lokasi and
                                            part.companyid=stlokasi.companyid
                                left join tbstlokasirak with (nolock) on part.kd_part=tbstlokasirak.kd_part and
                                            company.kd_lokasi=tbstlokasirak.kd_lokasi and
                                            company.kd_rak=tbstlokasirak.kd_rak and
                                            part.companyid=tbstlokasirak.companyid
                                left join sub with (nolock) on part.kd_sub=sub.kd_sub
                                left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                                left join discp with (nolock) on produk.kd_produk=discp.kd_produk and
                                            iif(isnull(company.inisial, 0)=1, 'RK', 'PC')=discp.cabang
                                left join dealer_discp with (nolock) on dealer_discp.kd_produk=produk.kd_produk and
                                            dealer_discp.companyid=part.companyid and dealer_discp.kd_dealer='".strtoupper(trim($kode_dealer))."'
                                left join dealer_netto with (nolock) on part.kd_part=dealer_netto.kd_part and
                                            dealer_netto.companyid=part.companyid and dealer_netto.kd_dealer='".strtoupper(trim($kode_dealer))."'
                        order by part.kd_part asc";

                $result = DB::select($sql, [ $request->get('companyid') ]);


                foreach($result as $data) {
                    $data_part_number->push((object) [
                        'part_number'   => strtoupper(trim($data->part_number)),
                        'nama_part'     => trim($data->nama_part),
                        'frg'           => strtoupper(trim($data->frg)),
                        'het'           => (double)$data->het,
                        'kelas'         => strtoupper(trim($data->kelas)),
                        'kode_produk'   => strtoupper(trim($data->kode_produk)),
                        'nama_produk'   => trim($data->nama_produk),
                        'stock'         => ((double)$data->stock > 0) ? 'AVAILABLE' : 'NOT AVAILABLE',
                        'status_discount'=> (int)$data->status_discount,
                        'discount'      => (double)$data->discount,
                        'discount_plus' => (double)$data->discount_plus,
                        'harga_netto'   => (double)$data->harga_netto
                    ]);
                }
            }

            $daftar_part_number = [
                'current_page'  => $current_page,
                'data'          => $data_part_number,
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

            return Response::responseSuccess("success", $daftar_part_number);

        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function formCartPartNumber(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'part_number'   => 'required|string',
                'user_id'       => 'required|string',
                'role_id'       => 'required|string',
                'companyid'     => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih part number terlebih dahulu");
            }

            $kode_dealer = '';
            $disc_dealer = 0;
            $disc_plus_dealer = 0;

            $sql = DB::select('exec SP_Dealer_DiscPMO ?,?,?', [
                        $request->get('user_id'), $request->get('role_id'), $request->get('companyid')
                    ]);

            foreach($sql as $data) {
                $kode_dealer = strtoupper(trim($data->kode_dealer));
                $disc_dealer = (double)$data->discount;
                $disc_plus_dealer = (double)$data->discount_plus;
            }

            $sql = "select	isnull(part.companyid, '') as companyid, isnull(part.kd_part, '') as part_number,
                            isnull(part.ket, '') as nama_part, isnull(part.kd_produk, '') as kode_produk,
                            isnull(part.nama_produk, '') as nama_produk, isnull(part.frg, '') as frg,
                            isnull(part.het, 0) as het, isnull(part.stock, 0) as stock,
                            isnull(part.jml_bo, 0) as jumlah_bo, isnull(pvtm.typemkt, '') as kode_motor,
                            isnull(typemotor.ket, '') as keterangan_motor, isnull(part.status_discount, 0) as status_discount,
                            isnull(part.discount, 0) as discount, isnull(part.discount_plus, 0) as discount_plus, isnull(part.harga_netto, 0) as harga_netto
                    from
                    (
                        select	part.companyid, part.kd_part, part.ket, produk.kd_produk,
                                produk.nama as nama_produk, part.frg, part.het, discp.discp_default,
                                isnull(tbstlokasirak.stock, 0) -
                                    (isnull(stlokasi.min, 0) + isnull(part.kanvas, 0) + isnull(part.min_gudang, 0) +
                                        isnull(part.in_transit, 0) + isnull(part.min_htl, 0)) as stock, isnull(bo.jumlah, 0) as jml_bo,
                                iif(isnull(part.tpc20, '') <> 'Y', 1, 0) as status_discount,
                                cast(iif(isnull(part.tpc20, '')='Y', 0,
                                    iif(".$disc_dealer." <= 0, 0,
                                        iif(isnull(discp.discp, 0) <= 0, ".$disc_dealer.",
                                            iif(isnull(dealer_discp.kd_dealer, '')='',
                                                iif(".$disc_dealer." > isnull(discp.discp_default, 0), isnull(discp.discp_default, 0), ".$disc_dealer."),
                                                iif(".$disc_dealer." > isnull(discp.discp, 0), isnull(discp.discp, 0), ".$disc_dealer.")
                                            )
                                        )
                                    )
                                ) as decimal(5,2)) as discount,
                                cast(iif(isnull(part.tpc20, '')='Y', 0,
                                    iif(".$disc_plus_dealer." <= 0, 0,
                                        iif(isnull(discp.discp_plus, 0) <= 0, ".$disc_plus_dealer.",
                                            iif(isnull(dealer_discp.kd_dealer, '')='',
                                                iif(".$disc_plus_dealer." > isnull(discp.discp_plus_default, 0), isnull(discp.discp_plus_default, 0), ".$disc_plus_dealer."),
                                                iif(".$disc_plus_dealer." > isnull(discp.discp_plus, 0), isnull(discp.discp_plus, 0), ".$disc_plus_dealer.")
                                            )
                                        )
                                    )
                                ) as decimal(5,2)) as discount_plus,
                                cast(iif(isnull(part.tpc20, '')='Y',
                                    iif(isnull(dealer_netto.kd_dealer, '') <> '',
                                        iif(isnull(dealer_netto.harga, 0) > 0, isnull(dealer_netto.harga, 0),
                                            iif(isnull(part.harga20, 0) > 0, isnull(part.harga20, 0), isnull(part.het, 0))
                                        ),
                                        iif(isnull(part.harga20, 0) > 0, isnull(part.harga20, 0), isnull(part.het, 0))
                                    ),
                                    isnull(part.het, 0) -
                                        round(((isnull(part.het, 0) *
                                            iif(".$disc_dealer." <= 0, 0,
                                                iif(isnull(discp.discp, 0) <= 0, ".$disc_dealer.",
                                                    iif(isnull(dealer_discp.kd_dealer, '')='',
                                                        iif(".$disc_dealer." > isnull(discp.discp_default, 0), isnull(discp.discp_default, 0), ".$disc_dealer."),
                                                        iif(".$disc_dealer." > isnull(discp.discp, 0), isnull(discp.discp, 0), ".$disc_dealer.")
                                                    )
                                                )
                                            )) / 100), 0) -
                                        round((((isnull(part.het, 0) -
                                            round(((isnull(part.het, 0) *
                                                iif(".$disc_dealer." <= 0, 0,
                                                    iif(isnull(discp.discp, 0) <= 0, ".$disc_dealer.",
                                                        iif(isnull(dealer_discp.kd_dealer, '')='',
                                                            iif(".$disc_dealer." > isnull(discp.discp_default, 0), isnull(discp.discp_default, 0), ".$disc_dealer."),
                                                            iif(".$disc_dealer." > isnull(discp.discp, 0), isnull(discp.discp, 0), ".$disc_dealer.")
                                                        )
                                                    )
                                                )) / 100), 0)) *
                                                    iif(".$disc_plus_dealer." <= 0, 0,
                                                        iif(isnull(discp.discp_plus, 0) <= 0, ".$disc_plus_dealer.",
                                                            iif(isnull(dealer_discp.kd_dealer, '')='',
                                                                iif(".$disc_plus_dealer." > isnull(discp.discp_plus_default, 0), isnull(discp.discp_plus_default, 0), ".$disc_plus_dealer."),
                                                                iif(".$disc_plus_dealer." > isnull(discp.discp_plus, 0), isnull(discp.discp_plus, 0), ".$disc_plus_dealer.")
                                                            )
                                                        )
                                                    )
                                        ) / 100), 0)
                                ) as decimal(13,0)) as harga_netto
                        from
                        (
                            select	part.companyid, part.kd_part, part.ket, part.kd_sub, part.frg,
                                    part.het, part.kanvas, part.in_transit, part.min_gudang, part.min_htl,
                                    part.tpc20, part.harga20
                            from	part with (nolock)
                            where	part.kd_part=? and part.companyid=?
                        )	part
                                inner join company with (nolock) on part.companyid=company.companyid
                                left join sub with (nolock) on part.kd_sub=sub.kd_sub
                                left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                                left join stlokasi with (nolock) on part.kd_part=stlokasi.kd_part and
                                            company.kd_lokasi=stlokasi.kd_lokasi and part.companyid=stlokasi.companyid
                                left join tbstlokasirak with (nolock) on part.kd_part=tbstlokasirak.kd_part and
                                            company.kd_lokasi=tbstlokasirak.kd_lokasi and company.kd_rak=tbstlokasirak.kd_rak and
                                            part.companyid=tbstlokasirak.companyid
                                left join discp with (nolock) on produk.kd_produk=discp.kd_produk and
                                            iif(isnull(company.inisial, 0)=1, 'RK', 'PC')=discp.cabang
                                left join dealer_discp with (nolock) on discp.kd_produk=dealer_discp.kd_produk and
                                            part.companyid=dealer_discp.companyid and dealer_discp.kd_dealer='".$kode_dealer."'
                                left join dealer_netto with (nolock) on part.kd_part=dealer_netto.kd_part and
                                            part.companyid=dealer_netto.companyid and dealer_netto.kd_dealer='".$kode_dealer."'
                                left join bo with (nolock) on part.kd_part=bo.kd_part and part.companyid=bo.companyid and
                                            bo.kd_dealer='".$kode_dealer."' and isnull(bo.jumlah, 0) > 0
                    )	part
                            left join pvtm with (nolock) on part.kd_part=pvtm.kd_part and part.companyid=pvtm.companyid
                            left join typemotor with (nolock) on pvtm.typemkt=typemotor.typemkt
                    order by pvtm.typemkt asc";

            $result = DB::select($sql, [ trim($request->get('part_number')), trim($request->get('companyid')) ]);

            $data_part = new Collection();
            $data_type_motor = new Collection();

            foreach($result as $data) {
                $data_type_motor->push((object) [
                    'part_number'       => trim($data->part_number),
                    'keterangan_motor'  => strtoupper(trim($data->kode_motor)).' - '.strtoupper(trim($data->keterangan_motor)),
                ]);

                $keterangan_bo = '';
                if((double)$data->jumlah_bo > 0) {
                    $keterangan_bo = "Sudah ada di BO sejumlah ".$data->jumlah_bo." PCS";
                }

                $data_part->push((object) [
                    'part_number'   => trim($data->part_number),
                    'nama_part'     => trim($data->nama_part),
                    'produk'        => trim($data->nama_produk),
                    'frg'           => trim($data->frg),
                    'het'           => (double)$data->het,
                    'discount'      => (double)$data->discount,
                    'discount_plus' => (double)$data->discount_plus,
                    'harga_netto'   => (double)$data->harga_netto,
                    'stock'         => (double)$data->stock > 0 ? 'Available' : 'Not Available',
                    'image_part'    => trim(config('constants.api.url.images')).'/'.strtoupper(trim($data->part_number)).'.jpg',
                    'keterangan_bo' => $keterangan_bo
                ]);
            }

            $detail_part = [];
            foreach($data_part as $data) {
                $detail_part[] = [
                    'part_number'   => trim($data->part_number),
                    'nama_part'     => trim($data->nama_part),
                    'produk'        => trim($data->produk),
                    'frg'           => trim($data->frg),
                    'het'           => (double)$data->het,
                    'discount'      => (double)$data->discount,
                    'discount_plus' => (double)$data->discount_plus,
                    'harga_netto'   => (double)$data->harga_netto,
                    'stock'         => trim($data->stock),
                    'image_part'    => trim($data->image_part),
                    'keterangan_bo' => trim($data->keterangan_bo),
                    'type_motor'    => $data_type_motor
                                        ->where('part_number', trim($request->get('part_number')))
                                        ->values()
                                        ->all()
                ];
            }

            return Response::responseSuccess('success', collect($detail_part)->first());
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function prosesCartPartNumber(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'part_number'   => 'required|string',
                'jumlah_order'  => 'required',
                'user_id'       => 'required|string',
                'role_id'       => 'required|string',
                'companyid'     => 'required|string',
            ]);

            if($validate->fails()) {
                return Response::responseWarning('Pilih part number dan isi jumlah order terlebih dahulu');
            }

            if(strtoupper(trim($request->get('role_id'))) != 'D_H3') {
                $sql = DB::table('cart_ordertmp')->lock('with (nolock)')
                        ->selectRaw("isnull(cart_ordertmp.kd_key, '') as kode_key,
                                isnull(cart_ordertmp.kd_dealer, '') as kode_dealer")
                        ->where('cart_ordertmp.kd_key', $request->get('user_id'))
                        ->where('cart_ordertmp.companyid', $request->get('companyid'))
                        ->first();

                if(empty($sql->kode_dealer) || $sql->kode_dealer == '') {
                    return Response::responseWarning('PILIH_DEALER', null);
                }
            }

            $kode_sales = '';
            $kode_dealer = '';
            $statusBo = 'B';
            $keterangan = '';
            $disc_dealer = 0;
            $disc_plus = 0;

            if(strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                $kode_sales = strtoupper(trim($request->get('user_id')));
            } else {
                if(strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                    $kode_dealer = strtoupper(trim($request->get('user_id')));

                    $sql = DB::table('dealer')
                            ->select('dealer.kd_dealer','dealer.kd_sales')
                            ->where('dealer.kd_dealer', $kode_dealer)
                            ->where('dealer.companyid', $request->get('companyid'))
                            ->first();

                    if(!empty($sql->kd_dealer)) {
                        $kode_sales = $sql->kd_sales;
                    }
                }
            }

            $sql = DB::table('cart_ordertmp')->lock('with (nolock)')
                    ->selectRaw("isnull(cart_ordertmp.kd_cart, '') as kode_cart, isnull(cart_ordertmp.kd_sales, '') as kode_sales,
                                isnull(cart_ordertmp.kd_dealer, '') as kode_dealer, isnull(cart_ordertmp.bo, '') as bo,
                                isnull(cart_ordertmp.keterangan, '') as keterangan")
                    ->join(DB::raw('cart_order_dtltmp with (nolock)'),
                        function($join) {
                            $join->on('cart_order_dtltmp.kd_cart', '=', 'cart_ordertmp.kd_cart')
                                ->on('cart_order_dtltmp.companyid', '=', 'cart_ordertmp.companyid');
                    })
                    ->where('cart_ordertmp.kd_cart', strtoupper(trim($request->get('user_id'))))
                    ->where('cart_ordertmp.companyid', strtoupper(trim($request->get('companyid'))))
                    ->first();

            if(!empty($sql->nomor_order)) {
                if(empty($kode_sales)) {
                    $kode_sales = strtoupper(trim($sql->kode_sales));
                }
                if(empty($kode_dealer)) {
                    $kode_dealer = strtoupper(trim($sql->kode_dealer));
                }
                $statusBo = strtoupper(trim($sql->bo));
                $keterangan = trim($sql->keterangan);
            }

            $sql = DB::select('exec SP_Dealer_DiscPMO ?,?,?', [
                $request->get('user_id'), $request->get('role_id'), $request->get('companyid')
            ]);

            foreach($sql as $data) {
                $kode_dealer = strtoupper(trim($data->kode_dealer));
                $disc_dealer = (double)$data->discount;
                $disc_plus = (double)$data->discount_plus;
                $umur_faktur = (double)$data->umur_faktur;
            }

            DB::transaction(function () use ($request, $kode_sales, $kode_dealer, $statusBo, $disc_dealer, $disc_plus, $umur_faktur, $keterangan) {
                DB::insert('exec SP_CartOrderTmp_PartNumberListIns ?,?,?,?,?,?,?,?,?,?,?,?', [
                    trim(strtoupper($request->get('user_id'))), trim(strtoupper($request->get('user_id'))), trim(strtoupper($kode_sales)),
                    trim(strtoupper($kode_dealer)), $statusBo, $keterangan, strtoupper(trim($request->get('part_number'))),
                    $request->get('jumlah_order'), $disc_dealer, $disc_plus, $umur_faktur, strtoupper(trim($request->get('companyid')))
                ]);
            });
            $message = "Part number ".strtoupper(trim($request->get('part_number')))." berhasil ditambahkan di keranjang anda";
            return Response::responseSuccess($message, null);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function PartNumberImageList(Request $request){
        // $data = DB::table('mspart')
        //     ->lock('with (nolock)')->select('*', DB::raw("'http://localhost:2022/suma-pmo/public/assets/images/parts/' + RTRIM(mspart.kd_part) + '.png' as url"))
        //     ->orderBy('kd_part', 'asc')
        //     ->paginate(24); jika terdapat $request search beri whare kd_part = $request->search

        $data = DB::table('mspart')
            ->lock('with (nolock)')->select('*');
        if($request->search){
            $data = $data->where('kd_part', 'like', $request->search.'%');
        }
        $data = $data->orderBy('kd_part', 'asc')->paginate(24);

        return Response::responseSuccess("success", $data);
    }
}
