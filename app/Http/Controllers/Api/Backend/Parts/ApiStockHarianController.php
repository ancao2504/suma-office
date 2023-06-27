<?php

namespace App\Http\Controllers\Api\Backend\Parts;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;


class ApiStockHarianController extends Controller
{
    
    public function indexLaporanStockHarian(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'companyid' => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Anda belum login");
            }

            $sql = "select	*
                    from
                    (
                        select	1 as no, 'CLASS_PRODUK' as jenis,
                                row_number() over(order by nama asc) as urut,
                                isnull(kd_class, '') as kode, isnull(nama, '') as nama
                        from	classprod
                        union all
                        select	2 as no, 'PRODUK' as jenis,
                                row_number() over(order by nama asc) as urut,
                                isnull(kd_produk, '') as kode, isnull(nama, '') as nama
                        from	produk
                        union all
                        select	3 as no, 'LEVEL_PRODUK' as jenis,
                                row_number() over(order by level asc) as urut,
                                isnull(level, '') as kode, isnull(level, '') as nama
                        from	produk
                        group by produk.level
                        union all
                        select	4 as no, 'SUB_PRODUK' as jenis,
                                row_number() over(order by nama asc) as urut,
                                isnull(kd_sub, '') as kode, isnull(nama, '') as nama
                        from	sub
                        union all
                        select	5 as no, 'LOKASI' as jenis,
                                row_number() over(order by kd_lokasi asc) as urut,
                                isnull(kd_lokasi, '') as kode, isnull(ket, '') as nama
                        from	lokasi
						where	lokasi.companyid='" . $request->get('companyid') . "'
                    )	options
                    order by options.no asc, options.urut asc";

            $result = DB::select($sql);

            $class_produk = [];
            $group_produk = [];
            $produk_level = [];
            $sub_produk = [];
            $lokasi = [];

            foreach ($result as $result) {
                if ($result->jenis == 'CLASS_PRODUK') {
                    $class_produk[] = [
                        'kode_class'    => strtoupper(trim($result->kode)),
                        'keterangan'    => strtoupper(trim($result->nama)),
                    ];
                }

                if ($result->jenis == 'PRODUK') {
                    $group_produk[] = [
                        'kode_produk'   => strtoupper(trim($result->kode)),
                        'keterangan'    => strtoupper(trim($result->nama)),
                    ];
                }

                if ($result->jenis == 'LEVEL_PRODUK') {
                    $produk_level[] = [
                        'level'         => strtoupper(trim($result->kode)),
                    ];
                }

                if ($result->jenis == 'SUB_PRODUK') {
                    $sub_produk[] = [
                        'kode_sub'      => strtoupper(trim($result->kode)),
                        'keterangan'    => strtoupper(trim($result->nama)),
                    ];
                }

                if ($result->jenis == 'LOKASI') {
                    $lokasi[] = [
                        'kode_lokasi'   => strtoupper(trim($result->kode)),
                        'keterangan'    => strtoupper(trim($result->nama)),
                    ];
                }
            }

            $companyId = '';
            $kodeLokasi = '';
            $kodeRak = '';

            $sql = DB::table('company')
                ->select('companyid', 'kd_lokasi', 'kd_rak')
                ->where('companyid', trim($request->get('companyid')))
                ->first();

            if (!empty($sql->companyid)) {
                $companyId = strtoupper(trim($sql->companyid));
                $kodeLokasi = strtoupper(trim($sql->kd_lokasi));
                $kodeRak = strtoupper(trim($sql->kd_rak));
            }

            $options_data = new Collection();
            $options_data->push((object) [
                'companyid'     => $companyId,
                'kode_lokasi'   => $kodeLokasi,
                'kode_rak'      => $kodeRak,
                'class_produk'  => $class_produk,
                'class_produk'  => $class_produk,
                'class_produk'  => $class_produk,
                'group_produk'  => $group_produk,
                'produk_level'  => $produk_level,
                'sub_produk'    => $sub_produk,
                'lokasi'        => $lokasi
            ]);

            return Response::responseSuccess('success', collect($options_data)->first());
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

    public function prosesStockPerlokasi(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'kode_lokasi'       => 'required|string',
                'kode_rak'          => 'required|string',
                'option_stock_sedia' => 'required',
                'nilai_stock_sedia' => 'required|numeric',
                'companyid'         => 'required|string',
                'role_id'           => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning('Kolom kode lokasi, kode rak, dan nilai stock sedia tidak boleh kosong');
            }

            $sql = "select	isnull(part.companyid, '') as companyid, isnull(part.nama_company, '') as nama_company,
                            isnull(part.alamat_company, '') as alamat_company, isnull(part.kota_company, '') as kota_company,
                            isnull(part.kd_part, '') as part_number, isnull(part.ket, '') as nama_part,
                            iif(isnull(part.frg, '')='', 'R', isnull(part.frg, '')) as frg,
                            cast(isnull(part.het, 0) as decimal(13, 0)) as het, isnull(part.kd_lokasi, '') as kode_lokasi,
                            cast(isnull(part.stock, 0) as decimal(13, 0)) as stock
                    from
                    (
                        select	part.companyid, company.nama as nama_company, company.alamat as alamat_company,
                                company.kota as kota_company, part.kd_part, part.ket, part.frg, part.het, stlokasi.kd_lokasi,
                                isnull(tbstlokasirak.stock, 0) -
                                    (isnull(stlokasi.min, 0) + isnull(stlokasi.in_transit, 0) +
                                    iif(isnull(company.kd_lokasi, '')='" . $request->get('kode_lokasi') . "', isnull(part.kanvas, 0) + isnull(part.min_gudang, 0) +
                                        isnull(part.in_transit, 0) + isnull(part.min_htl, 0), 0)) as stock
                        from
                        (
                            select	part.companyid, part.kd_part, part.kd_sub, part.ket, part.frg, part.het,
                                    part.kanvas, part.min_gudang, part.in_transit, part.min_htl
                            from	part with (nolock)
                            where	part.companyid='" . strtoupper(trim($request->get('companyid'))) . "'";

            if (!empty(trim($request->get('frg'))) && trim($request->get('frg')) <> '') {
                $sql .= " and part.frg='" . trim($request->get('frg')) . "'";
            }

            $sql .= " )	part
                                left join company with (nolock) on part.companyid=company.companyid
                                left join sub with (nolock) on part.kd_sub=sub.kd_sub
                                left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                                left join classprod with (nolock) on produk.kd_class=classprod.kd_class
                                inner join stlokasi with (nolock) on part.kd_part=stlokasi.kd_part and
                                            '" . trim($request->get('kode_lokasi')) . "'=stlokasi.kd_lokasi and part.companyid=stlokasi.companyid
                                inner join tbstlokasirak with (nolock) on part.kd_part=tbstlokasirak.kd_part and
                                            '" . trim($request->get('kode_lokasi')) . "'=tbstlokasirak.kd_lokasi and
                                            '" . trim($request->get('kode_rak')) . "'=tbstlokasirak.kd_rak and
                                                    part.companyid=tbstlokasirak.companyid
                        where	isnull(tbstlokasirak.stock, 0) -
                                (isnull(stlokasi.min, 0) + isnull(stlokasi.in_transit, 0) +
                                    iif(isnull(company.kd_lokasi, '')='" . $request->get('kode_lokasi') . "', isnull(part.kanvas, 0) + isnull(part.min_gudang, 0) +
                                        isnull(part.in_transit, 0) + isnull(part.min_htl, 0), 0)) " . trim($request->get('option_stock_sedia')) . " " . trim($request->get('nilai_stock_sedia')) . " ";

            if (!empty(trim($request->get('kode_class'))) && trim($request->get('kode_class')) <> '') {
                $sql .= " and classprod.kd_class='" . trim($request->get('kode_class')) . "'";
            }

            if (!empty(trim($request->get('kode_produk'))) && trim($request->get('kode_produk')) <> '') {
                $sql .= " and produk.kd_produk='" . trim($request->get('kode_produk')) . "'";
            }

            if (!empty(trim($request->get('kode_produk_level'))) && trim($request->get('kode_produk_level')) <> '') {
                $sql .= " and produk.level='" . trim($request->get('kode_produk_level')) . "'";
            }

            if (!empty(trim($request->get('kode_sub'))) && trim($request->get('kode_sub')) <> '') {
                $sql .= " and sub.kd_sub='" . trim($request->get('kode_sub')) . "'";
            }

            $sql .= " )	    part
                            where   isnull(part.stock, 0) " . trim($request->get('option_stock_sedia')) . " " . trim($request->get('nilai_stock_sedia')) . "
                    order by part.kd_part asc";

            $result = DB::select($sql);

            $companyId = '';
            $nama_company = '';
            $alamat_company = '';
            $kota_company = '';
            $data_stock = [];

            foreach ($result as $data) {
                $companyId = strtoupper(trim($data->companyid));
                $nama_company = strtoupper(trim($data->nama_company));
                $alamat_company = strtoupper(trim($data->alamat_company));
                $kota_company = strtoupper(trim($data->kota_company));

                $stock = '';

                if (strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                    $stock = ((float)$data->stock > 0 ? 'Available' : 'Not Available');
                } elseif (strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                    $stock = ((float)$data->stock > 0 ? 'Available' : 'Not Available');
                } elseif (strtoupper(trim($request->get('role_id'))) == 'MD_H3_KORSM') {
                    $stock = ((float)$data->stock > 0 ? 'Available' : 'Not Available');
                } else {
                    $stock = (float)$data->stock;
                }

                $data_stock[] = [
                    'part_number'   => strtoupper(trim($data->part_number)),
                    'nama_part'     => strtoupper(trim($data->nama_part)),
                    'frg'           => strtoupper(trim($data->frg)),
                    'het'           => (float)$data->het,
                    'lokasi'        => strtoupper(trim($data->kode_lokasi)),
                    'stock'         => $stock,
                ];
            }

            $stock_harian = new Collection();
            $stock_harian->push((object) [
                'companyid'     => $companyId,
                'nama_company'  => $nama_company,
                'alamat_company' => $alamat_company,
                'kota_company'  => $kota_company,
                'data_stock'    => $data_stock,
            ]);

            return Response::responseSuccess('success', collect($stock_harian)->first());
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

    public function prosesStockMarketplace(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'option_stock_sedia' => 'required',
                'nilai_stock_sedia' => 'required|numeric',
                'companyid'         => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning('Kolom kode lokasi, kode rak, dan nilai stock sedia tidak boleh kosong');
            }

            $sql = "select	isnull(part.companyid, '') as companyid, isnull(company.nama, '') as nama_company,
                            isnull(company.alamat, '') as alamat_company, isnull(company.kota, '') as kota_company,
                            isnull(part.kd_part, '') as part_number, isnull(part.ket, '') as nama_part,
                            iif(isnull(part.frg, '')='', 'R', isnull(part.frg, '')) as frg,
                            isnull(part.het, 0) as het,
                            cast(isnull(stlokasi.stock_ob, 0) as decimal(13, 0)) as stock_ob,
                            cast(isnull(stlokasi.stock_ok, 0) as decimal(13, 0)) as stock_ok,
                            cast(isnull(stlokasi.stock_ol, 0) as decimal(13, 0)) as stock_ol,
                            cast(isnull(stlokasi.stock_op, 0) as decimal(13, 0)) as stock_op,
                            cast(isnull(stlokasi.stock_os, 0) as decimal(13, 0)) as stock_os,
                            cast(isnull(stlokasi.stock_ot, 0) as decimal(13, 0)) as stock_ot,
                            cast(isnull(stlokasi.stock_ob, 0) * isnull(part.het, 0) as decimal(13, 0)) as nilai_stock_ob,
                            cast(isnull(stlokasi.stock_ok, 0) * isnull(part.het, 0) as decimal(13, 0)) as nilai_stock_ok,
                            cast(isnull(stlokasi.stock_ol, 0) * isnull(part.het, 0) as decimal(13, 0)) as nilai_stock_ol,
                            cast(isnull(stlokasi.stock_op, 0) * isnull(part.het, 0) as decimal(13, 0)) as nilai_stock_op,
                            cast(isnull(stlokasi.stock_os, 0) * isnull(part.het, 0) as decimal(13, 0)) as nilai_stock_os,
                            cast(isnull(stlokasi.stock_ot, 0) * isnull(part.het, 0) as decimal(13, 0)) as nilai_stock_ot
                    from
                    (
                        select	stlokasi.companyid, stlokasi.kd_part, isnull(stlokasi.stock_ob, 0) as stock_ob,
                                isnull(stlokasi.stock_ok, 0) as stock_ok, isnull(stlokasi.stock_ol, 0) as stock_ol,
                                isnull(stlokasi.stock_op, 0) as stock_op, isnull(stlokasi.stock_os, 0) as stock_os,
                                isnull(stlokasi.stock_ot, 0) as stock_ot
                        from
                        (
                            select	stlokasi.companyid, stlokasi.kd_part,
                                    sum(iif(stlokasi.kd_lokasi = 'OB', stlokasi.jumlah, 0)) as stock_ob,
                                    sum(iif(stlokasi.kd_lokasi = 'OK', stlokasi.jumlah, 0)) as stock_ok,
                                    sum(iif(stlokasi.kd_lokasi = 'OL', stlokasi.jumlah, 0)) as stock_ol,
                                    sum(iif(stlokasi.kd_lokasi = 'OP', stlokasi.jumlah, 0)) as stock_op,
                                    sum(iif(stlokasi.kd_lokasi = 'OS', stlokasi.jumlah, 0)) as stock_os,
                                    sum(iif(stlokasi.kd_lokasi = 'OT', stlokasi.jumlah, 0)) as stock_ot
                            from	stlokasi with (nolock)
                            where	stlokasi.companyid='" . $request->get('companyid') . "' and
                                    stlokasi.kd_lokasi in ('OB','OK','OL','OP','OS','OT')
                            group by stlokasi.companyid, stlokasi.kd_part
                        )	stlokasi
                        where	isnull(stlokasi.stock_ob, 0) " . $request->get('option_stock_sedia') . " " . $request->get('nilai_stock_sedia') . " or
                                isnull(stlokasi.stock_ok, 0) " . $request->get('option_stock_sedia') . " " . $request->get('nilai_stock_sedia') . " or
                                isnull(stlokasi.stock_ol, 0) " . $request->get('option_stock_sedia') . " " . $request->get('nilai_stock_sedia') . " or
                                isnull(stlokasi.stock_op, 0) " . $request->get('option_stock_sedia') . " " . $request->get('nilai_stock_sedia') . " or
                                isnull(stlokasi.stock_os, 0) " . $request->get('option_stock_sedia') . " " . $request->get('nilai_stock_sedia') . " or
                                isnull(stlokasi.stock_ot, 0) " . $request->get('option_stock_sedia') . " " . $request->get('nilai_stock_sedia') . "
                    )	stlokasi
                            inner join company with (nolock) on stlokasi.companyid=company.companyid
                            left join part with (nolock) on stlokasi.kd_part=part.kd_part and
                                        stlokasi.companyid=part.companyid
                            left join sub with (nolock) on part.kd_sub=sub.kd_sub
                            left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                            left join classprod with (nolock) on produk.kd_class=classprod.kd_class
                    where   isnull(part.kd_part, '') <> '' ";

            if (!empty(trim($request->get('kode_class'))) && trim($request->get('kode_class')) <> '') {
                $sql .= " and classprod.kd_class='" . trim($request->get('kode_class')) . "'";
            }

            if (!empty(trim($request->get('kode_produk'))) && trim($request->get('kode_produk')) <> '') {
                $sql .= " and produk.kd_produk='" . trim($request->get('kode_produk')) . "'";
            }

            if (!empty(trim($request->get('kode_produk_level'))) && trim($request->get('kode_produk_level')) <> '') {
                $sql .= " and produk.level='" . trim($request->get('kode_produk_level')) . "'";
            }

            if (!empty(trim($request->get('kode_sub'))) && trim($request->get('kode_sub')) <> '') {
                $sql .= " and sub.kd_sub='" . trim($request->get('kode_sub')) . "'";
            }

            $sql .= " order by stlokasi.kd_part asc";

            $result = DB::select($sql);

            $companyId = '';
            $nama_company = '';
            $alamat_company = '';
            $kota_company = '';
            $data_stock = [];

            foreach ($result as $data) {
                $companyId = strtoupper(trim($data->companyid));
                $nama_company = strtoupper(trim($data->nama_company));
                $alamat_company = strtoupper(trim($data->alamat_company));
                $kota_company = strtoupper(trim($data->kota_company));

                $data_stock[] = [
                    'part_number'   => strtoupper(trim($data->part_number)),
                    'nama_part'     => strtoupper(trim($data->nama_part)),
                    'frg'           => strtoupper(trim($data->frg)),
                    'het'           => (float)$data->het,
                    'stock_ob'      => (float)$data->stock_ob,
                    'nilai_stock_ob' => (float)$data->nilai_stock_ob,
                    'stock_ok'      => (float)$data->stock_ok,
                    'nilai_stock_ok' => (float)$data->nilai_stock_ok,
                    'stock_ol'      => (float)$data->stock_ol,
                    'nilai_stock_ol' => (float)$data->nilai_stock_ol,
                    'stock_op'      => (float)$data->stock_op,
                    'nilai_stock_op' => (float)$data->nilai_stock_op,
                    'stock_os'      => (float)$data->stock_os,
                    'nilai_stock_os' => (float)$data->nilai_stock_os,
                    'stock_ot'      => (float)$data->stock_ot,
                    'nilai_stock_ot' => (float)$data->nilai_stock_ot,
                    'stock_total'   => (float)$data->stock_ob + (float)$data->stock_ok +
                        (float)$data->stock_ol + (float)$data->stock_op +
                        (float)$data->stock_os + (float)$data->stock_ot,
                    'nilai_stock_total' => (float)$data->nilai_stock_ob + (float)$data->nilai_stock_ok +
                        (float)$data->nilai_stock_ol + (float)$data->nilai_stock_op +
                        (float)$data->nilai_stock_os + (float)$data->nilai_stock_ot
                ];
            }

            $stock_harian = new Collection();
            $stock_harian->push((object) [
                'companyid'     => $companyId,
                'nama_company'  => $nama_company,
                'alamat_company' => $alamat_company,
                'kota_company'  => $kota_company,
                'data_stock'    => $data_stock,
            ]);

            return Response::responseSuccess('success', collect($stock_harian)->first());
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
