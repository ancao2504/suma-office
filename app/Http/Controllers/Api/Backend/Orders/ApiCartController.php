<?php

namespace App\Http\Controllers\Api\Backend\Orders;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;


class ApiCartController extends Controller
{
    public function headerCart(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'user_id'   => 'required|string',
                'role_id'   => 'required|string',
                'companyid' => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Anda belum login");
            }

            $salesman = '';
            $dealer = '';
            $back_order = 'B';
            $keterangan = '';

            $sql = DB::table('cart_ordertmp')->lock('with (nolock)')
                ->selectRaw("isnull(cart_ordertmp.kd_key, '') as kode_key, isnull(cart_ordertmp.kd_cart, '') as kode_cart,
                                isnull(cart_ordertmp.kd_sales, '') as kode_sales, isnull(cart_ordertmp.kd_dealer, '') as kode_dealer,
                                isnull(cart_ordertmp.bo, '') as bo, isnull(cart_ordertmp.total, 0) as total,
                                isnull(cart_ordertmp.keterangan, '') as keterangan")
                ->leftJoin(DB::raw('cart_order_dtltmp with (nolock)'), function ($join) {
                    $join->on('cart_order_dtltmp.kd_key', '=', 'cart_ordertmp.kd_key')
                        ->on('cart_order_dtltmp.kd_cart', '=', 'cart_ordertmp.kd_cart')
                        ->on('cart_order_dtltmp.companyid', '=', 'cart_ordertmp.companyid');
                })
                ->where('cart_ordertmp.kd_key', trim($request->get('user_id')))
                ->where('cart_ordertmp.companyid', trim($request->get('companyid')))
                ->first();

            if (empty($sql->kode_cart)) {
                if (strtoupper(trim($request->get('role_id'))) == 'MD_H3_SM') {
                    $salesman = trim($request->get('user_id'));
                } elseif (strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                    $sql = DB::table('dealer')->lock('with (nolock)')
                        ->select('dealer.kd_sales')
                        ->where('dealer.kd_dealer', trim($request->get('user_id')))
                        ->where('dealer.companyid', trim($request->get('companyid')))
                        ->first();
                    $salesman = $sql->kd_sales;
                    $dealer = trim($request->get('user_id'));
                }
            } else {
                $salesman = trim($sql->kode_sales);
                $dealer = trim($sql->kode_dealer);
                $back_order = (empty($sql->bo) ? 'B' : $sql->bo);
                $keterangan = trim($sql->keterangan);
            }

            $data_cart = [
                'kode_cart'     => strtoupper(trim($request->get('user_id'))),
                'tanggal'       => date('j F Y'),
                'salesman'      => strtoupper(trim($salesman)),
                'dealer'        => strtoupper(trim($dealer)),
                'back_order'    => $back_order,
                'keterangan'    => trim($keterangan),
            ];

            return Response::responseSuccess('success', $data_cart);
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

    public function detailCart(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'user_id'   => 'required|string',
                'role_id'   => 'required|string',
                'companyid' => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Anda belum login");
            }

            $sql = "select	isnull(cart_ordertmp.kd_cart, '') as kode_cart, isnull(cart_ordertmp.kd_part, '') as part_number,
                            isnull(cart_ordertmp.ket, '') as nama_part, isnull(cart_ordertmp.kd_tpc, '') as kode_tpc,
                            isnull(cart_ordertmp.umur_faktur, 0) as umur_faktur, isnull(produk.nama, '') as produk,
                            isnull(cart_ordertmp.jml_order, 0) as jml_order, isnull(cart_ordertmp.harga, 0) as harga,
                            isnull(cart_ordertmp.disc1, 0) as disc1, isnull(cart_ordertmp.disc2, 0) as disc2,
                            isnull(cart_ordertmp.jumlah, 0) as jumlah, isnull(cart_ordertmp.hrg_satuan, 0) as harga_satuan,
                            isnull(cart_ordertmp.total, 0) as total, isnull(cart_ordertmp.het, 0) as het,
                            isnull(cart_ordertmp.hrg_pokok, 0) + round((isnull(cart_ordertmp.hrg_pokok, 0) * isnull(cart_ordertmp.ppn, 0)) / 100, 0) as harga_ppn,
                            isnull(bo.jumlah, 0) as bo, isnull(discp.discp_default, 0) as disc_max_produk,
                            isnull(cart_ordertmp.kd_file, '') as kode_file_company,
                            iif(isnull(cart_ordertmp.stock, 0) <= 0, 0,
                                iif(isnull(cart_ordertmp.jml_order, 0) > isnull(cart_ordertmp.stock, 0), isnull(cart_ordertmp.stock, 0), isnull(cart_ordertmp.jml_order, 0))
                            ) * isnull(cart_ordertmp.hrg_satuan, 0) as estimasi_harga
                        from
                        (
                            select	cart_ordertmp.companyid, cart_ordertmp.kd_key, cart_ordertmp.kd_cart, cart_ordertmp.kd_dealer,
                                    cart_ordertmp.total, cart_ordertmp.kd_part, part.ket, part.hrg_pokok, company.ppn,
                                    company.kd_file, cart_ordertmp.kd_tpc, cart_ordertmp.umur_faktur, cart_ordertmp.jml_order,
                                    cart_ordertmp.harga, cart_ordertmp.disc1, cart_ordertmp.disc2, cart_ordertmp.jumlah, part.het,
                                    cart_ordertmp.hrg_satuan, part.kd_sub, isnull(company.inisial, 0) as inisial_company,
                                    isnull(tbstlokasirak.stock, 0) -
                                        (isnull(stlokasi.min, 0) + isnull(part.kanvas, 0) + isnull(part.in_transit, 0) +
                                            isnull(part.min_gudang, 0) + isnull(part.min_htl, 0)) - isnull(minshare.qtymkr, 0) as stock
                            from
                            (
                                select	cart_ordertmp.companyid, cart_ordertmp.kd_key, cart_ordertmp.kd_cart, cart_ordertmp.kd_dealer,
                                        cart_ordertmp.total, cart_order_dtltmp.kd_part, cart_order_dtltmp.kd_tpc, cart_order_dtltmp.umur_faktur,
                                        cart_order_dtltmp.jml_order, cart_order_dtltmp.harga, cart_order_dtltmp.disc1, cart_order_dtltmp.disc2,
                                        cart_order_dtltmp.jumlah, cart_order_dtltmp.hrg_satuan
                                from
                                (
                                    select	cart_ordertmp.companyid, cart_ordertmp.kd_key, cart_ordertmp.kd_cart,
                                            cart_ordertmp.kd_dealer, cart_ordertmp.total
                                    from	cart_ordertmp with (nolock)
                                    where	cart_ordertmp.kd_key=? and cart_ordertmp.companyid=?
                                )	cart_ordertmp
                                        inner join cart_order_dtltmp with (nolock) on cart_ordertmp.kd_key=cart_order_dtltmp.kd_key and
                                                cart_ordertmp.kd_cart=cart_order_dtltmp.kd_cart and
                                                cart_ordertmp.companyid=cart_order_dtltmp.companyid
                            )	cart_ordertmp
                                    left join company with (nolock) on cart_ordertmp.companyid=company.companyid
                                    left join part with (nolock) on cart_ordertmp.kd_part=part.kd_part and
                                                cart_ordertmp.companyid=part.companyid
                                    left join stlokasi with (nolock) on cart_ordertmp.kd_part=stlokasi.kd_part and
                                                company.kd_lokasi=stlokasi.kd_lokasi and cart_ordertmp.companyid=stlokasi.companyid
                                    left join tbstlokasirak with (nolock) on cart_ordertmp.kd_part=tbstlokasirak.kd_part and
                                                company.kd_lokasi=tbstlokasirak.kd_lokasi and company.kd_rak=tbstlokasirak.kd_rak and
                                                cart_ordertmp.companyid=tbstlokasirak.companyid
                                    left join minshare with (nolock) on cart_ordertmp.kd_part=minshare.kd_part and
                                                cart_ordertmp.companyid=minshare.companyid
                        )	cart_ordertmp
                                left join sub with (nolock) on cart_ordertmp.kd_sub=sub.kd_sub
                                left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                                left join discp with (nolock) on produk.kd_produk=discp.kd_produk and iif(isnull(cart_ordertmp.inisial_company, 0)=1, 'RK', 'PC')=discp.cabang
                                left join bo with (nolock) on cart_ordertmp.kd_dealer=bo.kd_dealer and cart_ordertmp.kd_part=bo.kd_part and cart_ordertmp.companyid=bo.companyid
                        order by cart_ordertmp.kd_part asc";

            $result = DB::select($sql, [$request->get('user_id'), $request->get('companyid')]);
            $order_detail = new Collection();

            $kode_cart = '';
            $grand_total = 0;
            $estimasi_total = 0;

            foreach ($result as $data) {
                $kode_cart = trim($data->kode_cart);
                $grand_total = (float)$data->total;
                $estimasi_total = (float)$estimasi_total + (float)$data->estimasi_harga;

                $keterangan_discount = '';
                if (trim($data->kode_tpc) == '14') {
                    if ((float)$data->disc_max_produk > 0) {
                        if ((float)$data->disc1 > (float)$data->disc_max_produk) {
                            $keterangan_discount = ((float)$data->disc_max_produk > 0) ? 'DISCOUNT MAX PRODUK : ' . $data->disc_max_produk : '';
                        }
                        if ((float)$data->disc2 > (float)$data->disc_max_produk) {
                            $keterangan_discount = ((float)$data->disc_max_produk > 0) ? 'DISCOUNT MAX PRODUK : ' . $data->disc_max_produk : '';
                        }
                    }
                }

                $order_detail->push((object) [
                    'part_number'   => trim($data->part_number),
                    'nama_part'     => trim($data->nama_part),
                    'produk'        => trim($data->produk),
                    'kode_tpc'      => trim($data->kode_tpc),
                    'umur_faktur'   => trim($data->umur_faktur),
                    'jml_order'     => (float)$data->jml_order,
                    'harga_satuan'  => (float)$data->harga_satuan,
                    'het'           => (float)$data->het,
                    'disc1'         => (float)$data->disc1,
                    'disc2'         => (float)$data->disc2,
                    'jumlah'        => (float)$data->jumlah,
                    'image_part'    => trim(config('constants.api.url.images')) . '/' . trim($data->part_number) . '.jpg',
                    'keterangan_bo' => ((float)$data->bo > 0) ? 'SUDAH ADA DI BO SEJUMLAH : ' . $data->bo . ' PCS' : '',
                    'keterangan_harga' => ((float)$data->harga_ppn > (float)$data->harga_satuan) ? 'PENJUALAN RUGI' : '',
                    'keterangan_disc_produk' => $keterangan_discount,
                    'keterangan_disc_2kali'  => (trim($data->kode_file_company) == 'A') ? (($data->disc1 > 0 && $data->disc2 > 0) ? 'PART NUMBER DI DISKON 2X' : '') : '',
                    'keterangan_harga_tpc20' => (trim($data->kode_tpc) == '20') ? (($data->harga == $data->het) ? 'HARGA MASIH SAMA DENGAN HET' : '') : '',
                ]);
            }

            $data_detail_cart = [
                'kode_cart'     => trim($kode_cart),
                'grand_total'   => (float)$grand_total,
                'estimasi_total' => (float)$estimasi_total,
                'detail'        => $order_detail
            ];

            return Response::responseSuccess('success', $data_detail_cart);
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

    public function estimasiCart(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'user_id'   => 'required|string',
                'role_id'   => 'required|string',
                'companyid' => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Anda belum login");
            }

            $sql = "select	isnull(cart_ordertmp.companyid, '') as companyid, isnull(cart_ordertmp.kd_key, '') as kode_key,
                            isnull(cart_ordertmp.kd_dealer, '') as kode_dealer, count(cart_ordertmp.kd_key) as jumlah_item,
                            sum(isnull(cart_ordertmp.hrg_satuan, 0) *
                                iif(isnull(cart_ordertmp.jml_order, 0) > iif(isnull(cart_ordertmp.stock, 0) < 0, 0, isnull(cart_ordertmp.stock, 0)),
                                    iif(isnull(cart_ordertmp.stock, 0) < 0, 0, isnull(cart_ordertmp.stock, 0)),
                                        isnull(cart_ordertmp.jml_order, 0))) as total
                    from
                    (
                        select	cart_ordertmp.companyid, cart_ordertmp.kd_key,
                                cart_ordertmp.kd_dealer, cart_order_dtltmp.kd_part, cart_order_dtltmp.jml_order,
                                cart_order_dtltmp.hrg_satuan,
                                (isnull(tbstlokasirak.stock, 0) -
                                    (isnull(stlokasi.min, 0) + isnull(part.kanvas, 0) + isnull(part.min_gudang, 0) +
                                        isnull(part.in_transit, 0) + isnull(part.min_htl, 0))) - isnull(minshare.qtymkr, 0) as stock
                        from
                        (
                            select	cart_ordertmp.companyid, cart_ordertmp.kd_key, cart_ordertmp.kd_dealer
                            from	cart_ordertmp with (nolock)
                            where	cart_ordertmp.kd_key=? and cart_ordertmp.companyid=?
                        )	cart_ordertmp
                                left join company with (nolock) on cart_ordertmp.companyid=company.companyid
                                left join cart_order_dtltmp with (nolock) on cart_ordertmp.kd_key=cart_order_dtltmp.kd_key and
                                            cart_ordertmp.companyid=cart_order_dtltmp.companyid
                                left join part with (nolock) on cart_order_dtltmp.kd_part=part.kd_part and
                                            cart_ordertmp.companyid=part.companyid
                                left join stlokasi with (nolock) on cart_order_dtltmp.kd_part=stlokasi.kd_part and
                                            company.kd_lokasi=stlokasi.kd_lokasi and cart_ordertmp.companyid=stlokasi.companyid
                                left join tbstlokasirak with (nolock) on cart_order_dtltmp.kd_part=tbstlokasirak.kd_part and
                                            company.kd_lokasi=tbstlokasirak.kd_lokasi and company.kd_rak=tbstlokasirak.kd_rak and
                                            cart_ordertmp.companyid=tbstlokasirak.companyid
                                left join minshare with (nolock) on cart_order_dtltmp.kd_part=minshare.kd_part and
                                            cart_ordertmp.companyid=minshare.companyid
                    )	cart_ordertmp
                    group by cart_ordertmp.companyid, cart_ordertmp.kd_key, cart_ordertmp.kd_dealer ";

            $result = collect(DB::select($sql, [$request->get('user_id'), $request->get('companyid')]))->first();

            if (empty($result->kode_key)) {
                if (strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                    return Response::responseWarning('Cart Kosong');
                } else {
                    return Response::responseSuccess('Pilih Dealer Terlebih Dahulu', null);
                }
            } else {
                return Response::responseSuccess('success', $result);
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

    public function simpanCartDraft(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'kode_sales'    => 'required|string',
                'kode_dealer'   => 'required|string',
                'user_id'       => 'required|string',
                'role_id'       => 'required|string',
                'companyid'     => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Kolom salesman dan dealer tidak boleh kosong");
            }

            $back_order = 'B';

            if (empty($request->get('bo'))) {
                $back_order = 'B';
            } else {
                if ($request->get('bo') != 'B') {
                    if ($request->get('bo') != 'T') {
                        return Response::responseWarning("Kolom BO hanya dapat diisi BO atau Tidak BO");
                    }
                }
                $back_order = strtoupper(trim($request->get('bo')));
            }

            $disc_dealer = 0;
            $disc_plus_dealer = 0;

            $sql = DB::select('exec SP_Dealer_DiscPMO ?,?,?', [
                $request->get('user_id'), $request->get('role_id'), $request->get('companyid')
            ]);

            foreach ($sql as $data) {
                $disc_dealer = (float)$data->discount;
                $disc_plus_dealer = (float)$data->discount_plus;
            }

            DB::transaction(function () use ($request, $disc_dealer, $disc_plus_dealer, $back_order) {
                DB::insert('exec SP_CartOrderTmp_SimpanDraft ?,?,?,?,?,?,?,?', [
                    trim(strtoupper($request->get('user_id'))), strtoupper(trim($request->get('kode_sales'))),
                    strtoupper(trim($request->get('kode_dealer'))), strtoupper(trim($back_order)), $request->get('keterangan'),
                    (float)$disc_dealer, (float)$disc_plus_dealer, trim(strtoupper($request->get('companyid')))
                ]);
            });

            return Response::responseSuccess('Data Draft Berhasil Disimpan', null);
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

    public function resetCart(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'user_id'   => 'required|string',
                'companyid' => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Anda belum login");
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_CartOrderTmp_Reset ?,?', [
                    trim(strtoupper($request->get('user_id'))), trim(strtoupper($request->get('companyid')))
                ]);
            });

            return Response::responseSuccess("Data cart berhasil dikosongkan", null);
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

    public function hapusCartTemporaryAll(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'user_id'   => 'required|string',
                'role_id'   => 'required|string',
                'companyid' => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Anda belum login");
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_CartOrderTmp_DelAll ?,?,?', [
                    trim(strtoupper($request->get('user_id'))), trim(strtoupper($request->get('role_id'))),
                    trim(strtoupper($request->get('companyid')))
                ]);
            });

            return Response::responseSuccess("Data cart berhasil dihapus atau dikosongkan", null);
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

    public function editPartNumberCart(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'part_number'   => 'required|string',
                'user_id'       => 'required|string',
                'role_id'       => 'required|string',
                'companyid'     => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Pilih part number terlebih dahulu");
            }

            $sql = "select	top 1 isnull(cart_ordertmp.kd_key, '') as kode_key, isnull(cart_ordertmp.kd_cart, '') as kode_cart,
                            isnull(cart_order_dtltmp.kd_part, '') as part_number, isnull(part.ket, '') as nama_part,
                            isnull(produk.nama, '') as produk, isnull(cart_order_dtltmp.jml_order, 0) as jml_order,
                            isnull(cart_order_dtltmp.kd_tpc, '') as kode_tpc, isnull(cart_order_dtltmp.harga, 0) as harga,
                            isnull(part.het, 0) as het, isnull(cart_order_dtltmp.disc1, 0) as disc1,
                            isnull(cart_order_dtltmp.disc2, 0) as disc2, isnull(cart_order_dtltmp.jumlah, 0) as jumlah
                    from
                    (
                        select	top 1 cart_ordertmp.companyid, cart_ordertmp.kd_key, cart_ordertmp.kd_cart, cart_ordertmp.kd_dealer,
                                cart_ordertmp.total
                        from	cart_ordertmp with (nolock)
                        where	cart_ordertmp.kd_key=? and cart_ordertmp.companyid=?
                    )	cart_ordertmp
                            left join company with (nolock) on cart_ordertmp.companyid=company.companyid
                            left join cart_order_dtltmp with (nolock) on cart_ordertmp.kd_key=cart_order_dtltmp.kd_key and
                                        cart_ordertmp.kd_cart=cart_order_dtltmp.kd_cart and
                                        cart_ordertmp.companyid=cart_order_dtltmp.companyid
                            left join part with (nolock) on cart_order_dtltmp.kd_part=part.kd_part and cart_ordertmp.companyid=part.companyid
                            left join sub with (nolock) on part.kd_sub=sub.kd_sub
                            left join produk with (nolock) on sub.kd_produk=produk.kd_produk
                    where cart_order_dtltmp.kd_part=?";

            $result = collect(DB::select($sql, [$request->get('user_id'), $request->get('companyid'), $request->get('part_number')]))->first();
            $data_detail_cart = [];

            if (!empty($result->part_number)) {
                $data_detail_cart[] = [
                    'part_number'   => strtoupper(trim($result->part_number)),
                    'description'   => trim($result->nama_part),
                    'produk'        => trim($result->produk),
                    'jml_order'     => (float)$result->jml_order,
                    'het'           => (float)$result->het,
                    'harga'         => (float)$result->harga,
                    'disc1'         => (float)$result->disc1,
                    'disc2'         => (float)$result->disc2,
                    'total_detail'  => (float)$result->jumlah,
                    'images'        => trim(config('constants.api.url.images')) . '/' . strtoupper(trim($result->part_number)) . '.jpg',
                    'tpc'           => strtoupper(trim($result->kode_tpc)),
                    'user_id'       => strtoupper(trim($request->get('user_id'))),
                    'role_id'       => strtoupper(trim($request->get('role_id'))),
                ];
            } else {
                return Response::responseWarning("Part number " . trim($request->get('part_number') . " tidak terdaftar di cart anda, lakukan refresh halaman"));
            }

            return Response::responseSuccess('success', collect($data_detail_cart)->first());
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

    public function simpanPartNumberCart(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'part_number'   => 'required',
                'tpc'           => 'required',
                'jml_order'     => 'required',
                'harga'         => 'required',
                'discount'      => 'required',
                'discount_plus' => 'required',
                'user_id'       => 'required',
                'role_id'       => 'required',
                'companyid'     => 'required',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning('Isi data secara lengkap');
            }

            $disc_dealer = 0;
            $disc_plus = 0;

            if (strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                $sql = DB::select('exec SP_Dealer_DiscPMO ?,?,?', [
                    $request->get('user_id'), $request->get('role_id'), $request->get('companyid')
                ]);

                foreach ($sql as $data) {
                    $disc_dealer = (float)$data->discount;
                    $disc_plus = (float)$data->discount_plus;
                }
            } else {
                $disc_dealer = (float)$request->get('discount');
                $disc_plus = (float)$request->get('discount_plus');
            }
            DB::transaction(function () use ($request, $disc_dealer, $disc_plus) {
                DB::insert('exec SP_CartOrderTmp_InsPartNumber ?,?,?,?,?,?,?,?,?', [
                    trim(strtoupper($request->get('user_id'))), trim(strtoupper($request->get('part_number'))),
                    $request->get('tpc'), $request->get('jml_order'), $request->get('harga'),
                    $disc_dealer, $disc_plus, trim(strtoupper($request->get('role_id'))),
                    trim(strtoupper($request->get('companyid')))
                ]);
            });
            return Response::responseSuccess('Data Part Number Berhasil Disimpan', null);
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

    public function hapusPartNumberCart(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'part_number'   => 'required',
                'user_id'       => 'required|string',
                'companyid'     => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Pilih part number terlebih dahulu");
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_CartOrderTmp_DelPartNumber ?,?,?', [
                    trim(strtoupper($request->get('user_id'))), trim(strtoupper($request->get('part_number'))), trim(strtoupper($request->get('companyid')))
                ]);
            });
            return Response::responseSuccess('Part Number ' . trim($request->get('part_number')) . ' Berhasil Dihapus', null);
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

    public function importExcelCart(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'nama_file'     => 'required|string',
                'part_excel'    => 'required',
                'user_id'       => 'required|string',
                'role_id'       => 'required|string',
                'companyid'     => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Pilih file excel terlebih dahulu");
            }

            $sql_cart = DB::table('cart_ordertmp')->lock('with (nolock)')
                ->selectRaw("isnull(cart_ordertmp.kd_key, '') as kode_key,
                                isnull(cart_ordertmp.kd_sales, '') as kode_sales,
                                isnull(cart_ordertmp.kd_dealer, '') as kode_dealer")
                ->where('kd_key', $request->get('user_id'))
                ->where('companyid', $request->get('companyid'))
                ->first();

            if (empty($sql_cart->kode_key)) {
                if (strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                    $sql_dealer = DB::table('dealer')->lock('with (nolock)')
                        ->selectRaw("isnull(dealer.kd_dealer, '') as kode_dealer,
                                        isnull(dealer.kd_sales, '') as kode_sales")
                        ->where('dealer.kd_dealer', strtoupper(trim($request->get('user_id'))))
                        ->where('dealer.companyid', $request->get('companyid'))
                        ->first();

                    if (!empty($sql_dealer->kode_dealer)) {
                        $kode_sales = strtoupper(trim($sql_dealer->kode_sales));

                        $kode_dealer = '';
                        $disc_dealer = 0;
                        $disc_plus_dealer = 0;

                        $sql_setting = DB::select('exec SP_Dealer_DiscPMO ?,?,?', [
                            $request->get('user_id'), $request->get('role_id'), $request->get('companyid')
                        ]);

                        foreach ($sql_setting as $data) {
                            $kode_dealer = strtoupper(trim($data->kode_dealer));
                            $disc_dealer = (float)$data->discount;
                            $disc_plus_dealer = (float)$data->discount_plus;
                        }

                        DB::transaction(function () use ($request, $kode_sales, $kode_dealer, $disc_dealer, $disc_plus_dealer) {
                            DB::insert('exec SP_CartOrderTmp_SimpanDraft ?,?,?,?,?,?,?,?', [
                                trim(strtoupper($request->get('user_id'))), trim(strtoupper($kode_sales)), trim(strtoupper($kode_dealer)),
                                'B', '', (float)$disc_dealer, (float)$disc_plus_dealer, trim(strtoupper($request->get('companyid')))
                            ]);
                        });
                    } else {
                        return Response::responseWarning("Data dealer anda tidak ditemukan");
                    }
                } else {
                    return Response::responseWarning("Anda belum memilih data salesman dan dealer");
                }
            } else {
                if ($sql_cart->kode_sales == null || $sql_cart->kode_sales == '') {
                    return Response::responseWarning("Anda belum memilih data salesman");
                }
                if ($sql_cart->kode_dealer == null || $sql_cart->kode_dealer == '') {
                    return Response::responseWarning("Anda belum memilih data dealer");
                }
            }

            DB::transaction(function () use ($request) {
                DB::delete('exec SP_CartOrderTmp_ImportExcelDelete ?,?', [
                    trim(strtoupper($request->get('user_id'))), trim(strtoupper($request->get('companyid')))
                ]);

                DB::table('cart_ordertmp_excel')->insert($request->get('part_excel'));
            });

            return Response::responseSuccess('Data Excel Berhasil Diimport', null);
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

    public function prosesExcelCart(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'file_excel'    => 'required|string',
                'perbandingan'  => 'required|string',
                'user_id'       => 'required|string',
                'role_id'       => 'required|string',
                'companyid'     => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Gagal memproses data excel");
            }

            $kode_dealer = '';
            $disc_dealer = 0;
            $disc_plus_dealer = 0;

            $sql = DB::select('exec SP_Dealer_DiscPMO ?,?,?', [
                $request->get('user_id'), $request->get('role_id'), $request->get('companyid')
            ]);

            foreach ($sql as $data) {
                $kode_dealer = strtoupper(trim($data->kode_dealer));
                $disc_dealer = (float)$data->discount;
                $disc_plus_dealer = (float)$data->discount_plus;
                $umur_faktur = (float)$data->umur_faktur;
            }


            DB::transaction(function () use ($request, $kode_dealer, $disc_dealer, $disc_plus_dealer, $umur_faktur) {
                DB::insert('exec SP_CartOrderTmp_ImportExcelInsert ?,?,?,?,?,?,?,?,?', [
                    trim(strtoupper($request->get('user_id'))), $request->get('file_excel'), trim(strtoupper($kode_dealer)),
                    (float)$disc_dealer, (float)$disc_plus_dealer, $umur_faktur, $request->get('perbandingan'),
                    trim(strtoupper($request->get('role_id'))), trim(strtoupper($request->get('companyid')))
                ]);
            });

            $sql = DB::table('cart_ordertmp_excel')->lock('with (nolock)')
                ->selectRaw("isnull(kd_key, '') as kode_key, isnull(pn, '') as part_excel,
                                isnull(companyid, '') as companyid")
                ->where('cart_ordertmp_excel.kd_key', $request->get('user_id'))
                ->where('cart_ordertmp_excel.companyid', $request->get('companyid'))
                ->get();

            $jumlah_data = 0;
            $data_tidak_cocok = [];

            foreach ($sql as $data) {
                $jumlah_data = (float)$jumlah_data + 1;

                $data_tidak_cocok[] = [
                    'part_number'   => trim($data->part_excel),
                    'keterangan'    => 'Data yang dibandingkan tidak cocok'
                ];
            }

            DB::transaction(function () use ($request) {
                DB::delete('exec SP_CartOrderTmp_ImportExcelDelete ?,?', [
                    trim(strtoupper($request->get('user_id'))), trim(strtoupper($request->get('companyid')))
                ]);
            });

            if ($jumlah_data > 0) {
                return Response::responseSuccess('SUCCESS_WITH_MESSAGE', $data_tidak_cocok);
            } else {
                return Response::responseSuccess('Data Excel Berhasil Diimport', null);
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

    public function prosesCheckOutCart(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'user_id'       => 'required|string',
                'password'      => 'required|string',
                'password_confirm' => 'required|string',
                'companyid'     => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Kolom password dan konfirmasi password tidak boleh kosong");
            }

            if (trim($request->get('password')) != trim($request->get('password_confirm'))) {
                return Response::responseWarning("Kolom kata sandi dan ulangi kata sandi tidak sesuai");
            }

            $sql = DB::table('users')->lock('with (nolock)')
                ->select('user_id', 'password', 'role_id')
                ->where('user_id', $request->get('user_id'))
                ->where('companyid', $request->get('companyid'))
                ->first();

            if (empty($sql->user_id)) {
                return Response::responseWarning("User Id anda tidak ditemukan, coba kembali");
            } else {
                if (Hash::check($request->get('password'), $sql->password)) {
                    $role_id = strtoupper(trim($sql->role_id));

                    DB::transaction(function () use ($request, $role_id) {
                        DB::delete('exec SP_CartOrderTmp_ProsesDelete ?,?', [
                            trim(strtoupper($request->get('user_id'))), trim(strtoupper($request->get('companyid')))
                        ]);

                        DB::insert('exec SP_CartOrderTmp_ProsesJmlBaris ?,?,?', [
                            trim(strtoupper($request->get('user_id'))), trim(strtoupper($role_id)), trim(strtoupper($request->get('companyid')))
                        ]);
                    });

                    $daftar_data_faktur = new Collection();

                    $sql = DB::table('cart_order_dtltmp')->lock('with (nolock)')
                        ->selectRaw("cart_order_dtltmp.kd_key,
                                    isnull(sum(iif(cart_order_dtltmp.status_cart = 'OK', 1, 0)), 0) as jml_status_ok,
                                    isnull(sum(iif(cart_order_dtltmp.status_cart = 'BO', 1, 0)), 0) as jml_status_bo")
                        ->where('cart_order_dtltmp.kd_key', $request->get('user_id'))
                        ->where('cart_order_dtltmp.companyid', $request->get('companyid'))
                        ->groupBy('cart_order_dtltmp.kd_key')
                        ->get();

                    $jml_status_ok = 0;
                    $jml_status_bo = 0;

                    foreach ($sql as $data) {
                        $jml_status_ok = (float)$data->jml_status_ok;
                        $jml_status_bo = (float)$data->jml_status_bo;
                    }

                    if ($jml_status_ok == 0 && $jml_status_bo == 0) {
                        return Response::responseWarning("Tidak ada data yg diproses, coba periksa entry cart anda");
                    }

                    if ((float)$jml_status_bo > 0) {
                        DB::transaction(function () use ($request) {
                            DB::insert('exec SP_CartOrderTmp_CheckOutBO ?,?', [
                                trim(strtoupper($request->get('user_id'))), trim(strtoupper($request->get('companyid')))
                            ]);
                        });
                    }

                    if ((float)$jml_status_ok > 0) {
                        $sql = "select	row_number() over(partition by cart_order_dtltmp.kd_tpc, cart_order_dtltmp.kd_mkr,
                                                cart_order_dtltmp.umur_faktur, cart_order_dtltmp.disc1, cart_order_dtltmp.disc2
                                            order by cart_order_dtltmp.kd_tpc asc, cart_order_dtltmp.kd_mkr asc,
                                                cart_order_dtltmp.umur_faktur asc, cart_order_dtltmp.disc1 asc, cart_order_dtltmp.disc2 asc) as urutan,
                                        cart_ordertmp.kd_sales, cart_ordertmp.kd_dealer, cart_ordertmp.bo, cart_ordertmp.keterangan,
                                        cart_order_dtltmp.kd_tpc, cart_order_dtltmp.umur_faktur,
                                        cart_order_dtltmp.kd_part, cart_order_dtltmp.kd_lokasi, cart_order_dtltmp.kd_rak, cart_order_dtltmp.jml_order,
                                        cart_order_dtltmp.terlayani, cart_order_dtltmp.harga, cart_order_dtltmp.disc1, cart_order_dtltmp.disc2,
                                        cart_order_dtltmp.jumlah, cart_order_dtltmp.hrg_satuan, cart_order_dtltmp.het, cart_order_dtltmp.hrg_pokok,
                                        cart_order_dtltmp.kd_mkr, cart_order_dtltmp.jml_baris, cart_order_dtltmp.status_cart, cart_order_dtltmp.usertime
                                from
                                (
                                    select	cart_ordertmp.companyid, cart_ordertmp.kd_key, cart_ordertmp.kd_cart,
                                            cart_ordertmp.kd_sales, cart_ordertmp.kd_dealer,
                                            cart_ordertmp.bo, cart_ordertmp.keterangan
                                    from	cart_ordertmp
                                    where	cart_ordertmp.kd_key=? and
                                            cart_ordertmp.companyid=?
                                )	cart_ordertmp
                                        left join cart_order_dtltmp with (nolock) on cart_ordertmp.kd_key=cart_order_dtltmp.kd_key and
                                                    cart_ordertmp.kd_cart=cart_order_dtltmp.kd_cart and
                                                    cart_ordertmp.companyid=cart_order_dtltmp.companyid
                                where	cart_order_dtltmp.status_cart = 'OK'";

                        $result = DB::select($sql, [$request->get('user_id'), $request->get('companyid')]);

                        $kode_faktur = '';
                        $jml_baris_faktur = 0;
                        $data_order_ok = [];

                        foreach ($result as $data) {
                            $usertime = date('Y-m-d') . '=' . date('H:i:s') . ':' . date('u') . '=' . strtoupper(trim($request->get('user_id')));

                            if ((float)$data->urutan == 1) {
                                $kode_faktur = strtoupper(trim($request->get('user_id'))) . date('YmdHis') . Str::random(10);
                                $jml_baris_faktur = 0;
                            }

                            if ((float)$jml_baris_faktur + (float)$data->jml_baris > 11) {
                                $kode_faktur = strtoupper(trim($request->get('user_id'))) . date('YmdHis') . Str::random(10);
                                $jml_baris_faktur = 0;
                            }

                            $jml_baris_faktur = (float)$jml_baris_faktur + (float)$data->jml_baris;

                            $data_order_ok[] = [
                                'kd_key'        => strtoupper(trim($request->get('user_id'))),
                                'kd_faktur'     => strtoupper(trim($kode_faktur)),
                                'kd_sales'      => strtoupper(trim($data->kd_sales)),
                                'kd_dealer'     => strtoupper(trim($data->kd_dealer)),
                                'bo'            => strtoupper(trim($data->bo)),
                                'ket'           => strtoupper(trim($data->keterangan)),
                                'kd_tpc'        => strtoupper(trim($data->kd_tpc)),
                                'umur_faktur'   => strtoupper(trim($data->umur_faktur)),
                                'kd_part'       => strtoupper(trim($data->kd_part)),
                                'kd_lokasi'     => strtoupper(trim($data->kd_lokasi)),
                                'kd_rak'        => strtoupper(trim($data->kd_rak)),
                                'jml_order'     => strtoupper(trim($data->jml_order)),
                                'terlayani'     => strtoupper(trim($data->terlayani)),
                                'harga'         => strtoupper(trim($data->harga)),
                                'disc1'         => strtoupper(trim($data->disc1)),
                                'disc2'         => strtoupper(trim($data->disc2)),
                                'jumlah'        => strtoupper(trim($data->jumlah)),
                                'hrg_satuan'    => strtoupper(trim($data->hrg_satuan)),
                                'het'           => strtoupper(trim($data->het)),
                                'hrg_pokok'     => strtoupper(trim($data->hrg_pokok)),
                                'kd_mkr'        => strtoupper(trim($data->kd_mkr)),
                                'jml_baris'     => strtoupper(trim($data->jml_baris)),
                                'status_cart'   => strtoupper(trim($data->status_cart)),
                                'companyid'     => strtoupper(trim($request->get('companyid'))),
                                'usertime'      => strtoupper(trim($usertime))
                            ];
                        }

                        DB::transaction(function () use ($data_order_ok) {
                            DB::table('cart_ordertmp_proses')->insert($data_order_ok);
                        });

                        if (strtoupper(trim($role_id)) == 'D_H3') {
                            $sql = DB::table('cart_ordertmp_proses')->lock('with (nolock)')
                                ->selectRaw("isnull(companyid, '') as companyid, isnull(kd_key, '') as kode_key, isnull(kd_faktur, '') as kode_faktur,
                                            isnull(kd_sales, '') as kode_sales,  isnull(kd_dealer, '') as kode_dealer, isnull(kd_tpc, '') as kode_tpc,
                                            isnull(bo, '') as bo, isnull(umur_faktur, 0) as umur_faktur, isnull(ket, '') as keterangan, isnull(disc2, 0) as disc2,
                                            convert(varchar(10), dateadd(day, isnull(umur_faktur, 0), getdate()), 105) as tanggal_akhir_faktur,
                                            'R' as rh, 0 as discrp1, 'H3' as kode_beli, isnull(kd_lokasi, '') as kode_lokasi,
                                            convert(varchar(10), getdate(), 105) as tanggal_faktur,
                                            sum(isnull(jumlah, 0)) as total_faktur")
                                ->where('cart_ordertmp_proses.kd_key', $request->get('user_id'))
                                ->where('cart_ordertmp_proses.companyid', $request->get('companyid'))
                                ->groupByRaw("isnull(companyid, ''), isnull(kd_key, ''), isnull(kd_faktur, ''), isnull(kd_sales, ''),
                                            isnull(kd_dealer, ''), isnull(kd_tpc, ''), isnull(bo, ''), isnull(umur_faktur, 0),
                                            isnull(ket, ''), isnull(disc2, 0), isnull(kd_lokasi, '')")
                                ->orderByRaw("sum(cart_ordertmp_proses.jumlah) asc")
                                ->get();

                            foreach ($sql as $data) {
                                $daftar_data_faktur->push((object) [
                                    'kode_faktur' => $data->kode_faktur
                                ]);

                                $status_limit_piutang = 0;
                                $status_limit_sales = 0;
                                $sisa_limit_piutang = 0;
                                $sisa_limit_sales = 0;

                                $sql_dealer = DB::table('dealer')->lock('with (nolock)')
                                    ->selectRaw("isnull(dealer.kd_dealer, '') as kode_dealer,
                                                            isnull(dealer.limit_sales, 0) as limit_sales,
                                                            isnull(dealer.limit_piut, 0) as limit_piutang,
                                                            isnull(dealer.limit_piut, 0) -
                                                                (isnull(dealer.s_awal_b, 0) + isnull(dealer.jual_14, 0) + isnull(dealer.jual_20, 0) -
                                                                    isnull(dealer.extra, 0) - isnull(dealer.ca, 0) + isnull(dealer.da, 0) -
                                                                    isnull(dealer.insentif, 0) - isnull(dealer.t_bayar_b, 0) - isnull(dealer.titipan, 0)) as sisa_limit_piutang,
                                                            isnull(dealer.limit_sales, 0) - (isnull(dealer.jual_14, 0) + isnull(dealer.jual_20, 0)) as sisa_limit_sales")
                                    ->where('dealer.kd_dealer', strtoupper(trim($data->kode_dealer)))
                                    ->where('dealer.companyid', $request->get('companyid'))
                                    ->first();

                                if (empty($sql_dealer->kode_dealer)) {
                                    DB::transaction(function () use ($request) {
                                        DB::delete('exec SP_CartOrderTmp_ProsesDelete ?,?', [
                                            trim(strtoupper($request->get('user_id'))), trim(strtoupper($request->get('companyid')))
                                        ]);
                                    });

                                    return Response::responseWarning('Kode dealer tidak terdaftar');
                                } else {
                                    $status_limit_piutang = ((float)$sql_dealer->limit_piutang == 0) ? 0 : 1;
                                    $status_limit_sales = ((float)$sql_dealer->limit_sales == 0) ? 0 : 1;
                                    $sisa_limit_piutang = (float)$sql_dealer->sisa_limit_piutang;
                                    $sisa_limit_sales = (float)$sql_dealer->sisa_limit_sales;
                                }

                                $status_insert_bo = 0;

                                if ($status_limit_piutang == 1) {
                                    if ((float)$data->total_faktur > (float)$sisa_limit_piutang) {
                                        $status_insert_bo = 1;
                                    }
                                }

                                if ($status_limit_sales == 1) {
                                    if ((float)$data->total_faktur > (float)$sisa_limit_sales) {
                                        $status_insert_bo = 1;
                                    }
                                }

                                DB::transaction(function () use ($request, $data, $status_insert_bo) {
                                    DB::insert('exec SP_CartOrderTmp_CheckOutPrepare ?,?,?,?,?', [
                                        trim(strtoupper($request->get('user_id'))), trim($data->kode_faktur), trim($data->kode_dealer), $status_insert_bo, trim(strtoupper($request->get('companyid')))
                                    ]);
                                });

                                if ($status_insert_bo == 0) {
                                    $sql = DB::table('fakturtmp')->lock('with (nolock)')
                                        ->select('kd_key', 'no_pof')
                                        ->where('kd_key', $data->kode_faktur)
                                        ->where('no_faktur', $data->kode_faktur)
                                        ->where('companyid', $request->get('companyid'))
                                        ->first();

                                    $nomor_pof = '';

                                    if (!empty($sql->no_pof)) {
                                        $nomor_pof = strtoupper(trim($sql->no_pof));
                                    }

                                    DB::transaction(function () use ($request, $data, $nomor_pof) {
                                        DB::insert('exec SP_Faktur_Simpan_New7 ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
                                            trim($data->kode_faktur), strtoupper(trim($data->kode_faktur)), strtoupper(trim($data->kode_faktur)),
                                            trim($data->tanggal_faktur), strtoupper(trim($nomor_pof)), strtoupper(trim($data->kode_beli)),  strtoupper(trim($data->kode_sales)),
                                            strtoupper(trim($data->kode_dealer)), strtoupper(trim($data->keterangan)), (float)$data->disc2, (float)$data->umur_faktur,
                                            trim($data->tanggal_akhir_faktur), trim($data->kode_tpc), strtoupper(trim($data->rh)), strtoupper(trim($data->bo)),
                                            (float)$data->discrp1, '', '', '', '', '', '', '', strtoupper(trim($request->get('user_id'))),
                                            strtoupper(trim($request->get('companyid'))), 1, 1, '', 0, strtoupper(trim($data->kode_lokasi)), ''
                                        ]);
                                    });
                                }
                            }
                        } else {
                            $sql = DB::table('cart_ordertmp_proses')->lock('with (nolock)')
                                ->selectRaw("isnull(cart_ordertmp_proses.companyid, '') as companyid,
                                                isnull(cart_ordertmp_proses.kd_key, '') as kode_key,
                                                isnull(cart_ordertmp_proses.kd_faktur, '') as kode_faktur,
                                                isnull(cart_ordertmp_proses.kd_dealer, '') as kode_dealer")
                                ->where('cart_ordertmp_proses.kd_key', $request->get('user_id'))
                                ->where('cart_ordertmp_proses.companyid', $request->get('companyid'))
                                ->groupByRaw("cart_ordertmp_proses.companyid, cart_ordertmp_proses.kd_key,
                                            cart_ordertmp_proses.kd_faktur, cart_ordertmp_proses.kd_dealer")
                                ->get();

                            foreach ($sql as $data) {
                                $daftar_data_faktur->push((object) [
                                    'kode_faktur' => $data->kode_faktur
                                ]);

                                DB::transaction(function () use ($request, $data) {
                                    DB::insert('exec SP_CartOrderTmp_CheckOutPof ?,?,?,?', [
                                        trim(strtoupper($request->get('user_id'))), trim(strtoupper($data->kode_faktur)),
                                        trim(strtoupper($data->kode_dealer)), trim(strtoupper($request->get('companyid')))
                                    ]);
                                });
                            }
                        }
                    }

                    return Response::responseSuccess('Data Checkout Success', $daftar_data_faktur);
                } else {
                    return Response::responseWarning('Password anda entry tidak sesuai');
                }
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

    public function resultCheckOutCart(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'role_id'       => 'required|string',
                'kode'          => 'required',
                'companyid'     => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Kolom password dan ulangi password tidak boleh kosong");
            }

            $kode = json_decode($request->get('kode'), false);
            $kode_order = [];
            foreach ($kode as $data) {
                $kode_order[] = [
                    $data->kode_faktur
                ];
            }

            $sql = DB::table('cart_order')->lock('with (nolock)')
                ->selectRaw("isnull(cart_order.companyid, '') as companyid, isnull(cart_order.kd_cart, '') as kode_cart,
                                isnull(cart_order.kode, '') as kode, isnull(pof.no_pof, '') as nomor_pof,
                                isnull(faktur.no_faktur, '') as nomor_faktur")
                ->leftJoin(
                    DB::raw('pof with (nolock)'),
                    function ($join) {
                        $join->on('pof.no_order', '=', 'cart_order.kd_cart')
                            ->on('pof.companyid', '=', 'cart_order.companyid');
                    }
                )
                ->leftJoin(
                    DB::raw('faktur with (nolock)'),
                    function ($join) {
                        $join->on('faktur.no_pof', '=', 'pof.no_pof')
                            ->on('faktur.companyid', '=', 'cart_order.companyid');
                    }
                )
                ->where('cart_order.companyid', $request->get('companyid'))
                ->whereIn('cart_order.kode', $kode_order)
                ->get();

            $data_faktur = new Collection();
            $data_back_order = new Collection();
            $data_purchase_order = new Collection();

            $result_faktur = new Collection();
            $result_back_order = new Collection();
            $result_purchase_order = new Collection();

            $data_nomor_faktur = new Collection();
            $data_nomor_back_order = new Collection();
            $data_nomor_purchase_order = new Collection();

            $status_cek_faktur = 0;
            $status_back_order = 0;
            $status_purchase_order = 0;

            foreach ($sql as $data) {
                if (strtoupper(trim($data->nomor_faktur)) != '') {
                    $data_nomor_faktur->push((object) [
                        'kode_faktur' => strtoupper(trim($data->nomor_faktur))
                    ]);
                    $status_cek_faktur = 1;
                }
                if (strtoupper(trim($data->nomor_faktur)) == '') {
                    $data_nomor_back_order->push((object) [
                        'kode_cart' => strtoupper(trim($data->kode_cart))
                    ]);
                    $status_back_order = 1;
                }
                if (strtoupper(trim($data->nomor_pof)) != '') {
                    $data_nomor_purchase_order->push((object) [
                        'kode_pof' => strtoupper(trim($data->nomor_pof))
                    ]);
                    $status_purchase_order = 1;
                }
            }

            if (strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                if ($status_cek_faktur == 1) {
                    $nomor_urut_faktur = 0;
                    $data_kode_nomor_faktur = '';

                    foreach ($data_nomor_faktur as $data) {
                        $nomor_urut_faktur = (float)$nomor_urut_faktur + 1;

                        if ((float)$nomor_urut_faktur == 1) {
                            $data_kode_nomor_faktur .= "'" . $data->kode_faktur . "'";
                        } else {
                            $data_kode_nomor_faktur .= ',' . "'" . $data->kode_faktur . "'";
                        }
                    }

                    $sql = "select	isnull(faktur.companyid, '') as companyid, isnull(company.nama, '') as nama_company,
                                    isnull(company.alamat, '') as alamat_company, isnull(company.kota, '') as kota_company,
                                    isnull(company.telp, '') as telp_company, isnull(company.fax, '') as fax_company,
                                    isnull(faktur.no_faktur, '') as no_faktur, isnull(faktur.tgl_faktur, '') as tgl_faktur,
                                    isnull(faktur.kd_beli, '') as kode_beli, isnull(jns_beli.nama, '') as keterangan_beli,
                                    isnull(faktur.no_pof, '') as nomor_pof, isnull(faktur.kd_sales, '') as kode_sales,
                                    isnull(salesman.nm_sales, '') as nama_sales, isnull(faktur.kd_dealer, '') as kode_dealer,
                                    isnull(dealer.nm_dealer, '') as nama_dealer, isnull(dealer.alamat1, '') as alamat_dealer,
                                    isnull(dealer.kota, '') as kota_dealer, isnull(dealer.ktp_h, '') as ktp_dealer,
                                    isnull(dealer.npwp, '') as npwp_dealer, isnull(nm_dealersj, '') as nama_dealer_sj,
                                    isnull(dealer.alamat1sj, '') as alamat_dealer_sj, isnull(dealer.kotasj, '') as kota_dealer_sj,
                                    isnull(dealer.ketsj1, '') as keterangan_dealer_sj1, isnull(dealer.ketsj2, '') as keterangan_dealer_sj2,
                                    isnull(faktur.ket, '') as keterangan, isnull(faktur.kd_tpc, '') as kode_tpc,
                                    isnull(faktur.umur_faktur, 0) as umur_faktur, isnull(faktur.tgl_akhir_faktur, '') as tanggal_jatuh_tempo,
                                    isnull(faktur.bo, '') as bo, isnull(faktur.rh, '') as rh, isnull(faktur.disc2, 0) as disc2,
                                    isnull(faktur.discrp1, 0) as discrp1, isnull(faktur.total, 0) as total, isnull(fakt_dtl.kd_part, '') as part_number,
                                    isnull(part.ket, '') as nama_part, isnull(fakt_dtl.jml_order, 0) as jml_order, isnull(fakt_dtl.jml_jual, 0) as jml_jual,
                                    isnull(fakt_dtl.harga, 0) as harga, isnull(fakt_dtl.disc1, 0) as disc1, isnull(fakt_dtl.jumlah, 0) as jumlah
                            from
                            (
                                select	faktur.companyid, faktur.no_faktur, faktur.tgl_faktur, faktur.kd_beli,
                                        faktur.kd_sales, faktur.kd_dealer, faktur.no_pof, faktur.ket, faktur.kd_tpc,
                                        faktur.umur_faktur, faktur.tgl_akhir_faktur, faktur.bo, faktur.rh,
                                        faktur.disc2, faktur.discrp1, faktur.total
                                from	faktur with (nolock)
                                where	faktur.companyid=? and faktur.no_faktur in (" . $data_kode_nomor_faktur . ")
                            )	faktur
                                    left join company with (nolock) on faktur.companyid=company.companyid
                                    left join jns_beli with (nolock) on faktur.kd_beli=jns_beli.kd_beli and
                                                faktur.companyid=jns_beli.companyid
                                    left join salesman with (nolock) on faktur.kd_sales=salesman.kd_sales and
                                                faktur.companyid=salesman.companyid
                                    left join dealer with (nolock) on faktur.kd_dealer=dealer.kd_dealer and
                                                faktur.companyid=dealer.companyid
                                    left join fakt_dtl with (nolock) on faktur.no_faktur=fakt_dtl.no_faktur and
                                                faktur.companyid=fakt_dtl.companyid
                                    left join part with (nolock) on fakt_dtl.kd_part=part.kd_part and faktur.companyid=part.companyid
                            order by faktur.companyid asc, faktur.no_faktur asc, fakt_dtl.kd_part asc";

                    $result = DB::select($sql, [$request->get('companyid')]);
                    $result_faktur_detail = new Collection();
                    $jumlah_data_faktur = 0;
                    $nomor_urut = 0;
                    $nomor_faktur_temp = '';
                    foreach ($result as $data) {
                        $jumlah_data_faktur = (float)$jumlah_data_faktur + 1;

                        if (strtoupper(trim($nomor_faktur_temp)) != strtoupper(trim($data->no_faktur))) {
                            $nomor_urut = 0;
                        }

                        $result_faktur_detail->push((object) [
                            'no'                    => (float)$nomor_urut + 1,
                            'nomor_faktur'          => strtoupper(trim($data->no_faktur)),
                            'part_number'           => strtoupper(trim($data->part_number)),
                            'nama_part'             => strtoupper(trim($data->nama_part)),
                            'jml_order'             => (float)$data->jml_order,
                            'jml_jual'              => (float)$data->jml_jual,
                            'harga'                 => (float)$data->harga,
                            'disc_detail'           => (float)$data->disc1,
                            'jumlah'                => (float)$data->jumlah,
                            'image_part'            => trim(config('constants.api.url.images')) . '/' . trim($data->part_number) . '.jpg',
                        ]);

                        $nomor_faktur_temp = strtoupper(trim($data->no_faktur));

                        $data_faktur->push((object) [
                            'companyid'             => strtoupper(trim($data->companyid)),
                            'nama_company'          => strtoupper(trim($data->nama_company)),
                            'alamat_company'        => strtoupper(trim($data->alamat_company)),
                            'kota_company'          => strtoupper(trim($data->kota_company)),
                            'telp_company'          => strtoupper(trim($data->telp_company)),
                            'fax_company'           => strtoupper(trim($data->fax_company)),
                            'nomor_faktur'          => strtoupper(trim($data->no_faktur)),
                            'nomor_pof'             => strtoupper(trim($data->nomor_pof)),
                            'tgl_faktur'            => strtoupper(trim($data->tgl_faktur)),
                            'kode_beli'             => strtoupper(trim($data->kode_beli)),
                            'keterangan_beli'       => strtoupper(trim($data->keterangan_beli)),
                            'nomor_pof'             => strtoupper(trim($data->nomor_pof)),
                            'kode_sales'            => strtoupper(trim($data->kode_sales)),
                            'nama_sales'            => strtoupper(trim($data->nama_sales)),
                            'kode_dealer'           => strtoupper(trim($data->kode_dealer)),
                            'nama_dealer'           => strtoupper(trim($data->nama_dealer)),
                            'alamat_dealer'         => strtoupper(trim($data->alamat_dealer)),
                            'kota_dealer'           => strtoupper(trim($data->kota_dealer)),
                            'npwp_dealer'           => strtoupper(trim($data->npwp_dealer)),
                            'ktp_dealer'            => strtoupper(trim($data->ktp_dealer)),
                            'nama_dealer_sj'        => strtoupper(trim($data->nama_dealer_sj)),
                            'alamat_dealer_sj'      => strtoupper(trim($data->alamat_dealer_sj)),
                            'kota_dealer_sj'        => strtoupper(trim($data->kota_dealer_sj)),
                            'keterangan_dealer_sj1' => strtoupper(trim($data->keterangan_dealer_sj1)),
                            'keterangan_dealer_sj2' => strtoupper(trim($data->keterangan_dealer_sj2)),
                            'keterangan'            => strtoupper(trim($data->keterangan)),
                            'kode_tpc'              => strtoupper(trim($data->kode_tpc)),
                            'umur_faktur'           => strtoupper(trim($data->umur_faktur)),
                            'tanggal_jatuh_tempo'   => strtoupper(trim($data->tanggal_jatuh_tempo)),
                            'bo'                    => strtoupper(trim($data->bo)),
                            'rh'                    => strtoupper(trim($data->rh)),
                            'disc_header'           => (float)$data->disc2,
                            'disc_rupiah'           => (float)$data->discrp1,
                            'total'                 => (float)$data->total
                        ]);
                    }

                    $nomor_faktur = '';
                    if ((float)$jumlah_data_faktur > 0) {
                        foreach ($data_faktur as $data) {
                            if (strtoupper(trim($nomor_faktur)) != strtoupper(trim($data->nomor_faktur))) {
                                $sub_total = collect($result_faktur_detail
                                    ->where('nomor_faktur', $data->nomor_faktur)
                                    ->values()
                                    ->all())
                                    ->sum('jumlah');

                                $result_faktur->push((object) [
                                    'nomor_faktur'          => strtoupper(trim($data->nomor_faktur)),
                                    'nomor_pof'             => strtoupper(trim($data->nomor_pof)),
                                    'tgl_faktur'            => strtoupper(trim($data->tgl_faktur)),
                                    'company'               => (object)[
                                        'companyid'         => strtoupper(trim($data->companyid)),
                                        'nama_company'      => strtoupper(trim($data->nama_company)),
                                        'alamat_company'    => strtoupper(trim($data->alamat_company)),
                                        'kota_company'      => strtoupper(trim($data->kota_company)),
                                        'telp_company'      => strtoupper(trim($data->telp_company)),
                                        'fax_company'       => strtoupper(trim($data->fax_company)),
                                    ],
                                    'kode_beli'             => strtoupper(trim($data->kode_beli)),
                                    'keterangan_beli'       => strtoupper(trim($data->keterangan_beli)),
                                    'nomor_pof'             => strtoupper(trim($data->nomor_pof)),
                                    'salesman'              => (object)[
                                        'kode_sales'        => strtoupper(trim($data->kode_sales)),
                                        'nama_sales'        => strtoupper(trim($data->nama_sales)),
                                    ],
                                    'dealer'                    => (object)[
                                        'kode_dealer'           => strtoupper(trim($data->kode_dealer)),
                                        'nama_dealer'           => strtoupper(trim($data->nama_dealer)),
                                        'alamat_dealer'         => strtoupper(trim($data->alamat_dealer)),
                                        'kota_dealer'           => strtoupper(trim($data->kota_dealer)),
                                        'npwp_dealer'           => strtoupper(trim($data->npwp_dealer)),
                                        'ktp_dealer'            => strtoupper(trim($data->ktp_dealer)),
                                        'nama_dealer_sj'        => strtoupper(trim($data->nama_dealer_sj)),
                                        'alamat_dealer_sj'      => strtoupper(trim($data->alamat_dealer_sj)),
                                        'kota_dealer_sj'        => strtoupper(trim($data->kota_dealer_sj)),
                                        'keterangan_dealer_sj1' => strtoupper(trim($data->keterangan_dealer_sj1)),
                                        'keterangan_dealer_sj2' => strtoupper(trim($data->keterangan_dealer_sj2)),
                                    ],
                                    'keterangan'            => strtoupper(trim($data->keterangan)),
                                    'kode_tpc'              => strtoupper(trim($data->kode_tpc)),
                                    'umur_faktur'           => strtoupper(trim($data->umur_faktur)),
                                    'tanggal_jatuh_tempo'   => strtoupper(trim($data->tanggal_jatuh_tempo)),
                                    'bo'                    => strtoupper(trim($data->bo)),
                                    'rh'                    => strtoupper(trim($data->rh)),
                                    'sub_total'             => (float)$sub_total,
                                    'disc_header'           => (float)$data->disc_header,
                                    'disc_header_rupiah'    => ((float)$sub_total * (float)$data->disc_header) / 100,
                                    'disc_rupiah'           => (float)$data->disc_rupiah,
                                    'total'                 => (float)$data->total,
                                    'detail'                => collect($result_faktur_detail)
                                        ->where('nomor_faktur', $data->nomor_faktur)
                                        ->values()
                                        ->all()
                                ]);
                                $nomor_faktur = strtoupper(trim($data->nomor_faktur));
                            }
                        }
                    }
                }

                if ($status_back_order == 1) {
                    $nomor_urut_back_order = 0;
                    $data_kode_back_order = '';

                    foreach ($data_nomor_back_order as $data) {
                        $nomor_urut_back_order = (float)$nomor_urut_back_order + 1;

                        if ((float)$nomor_urut_back_order == 1) {
                            $data_kode_back_order .= "'" . $data->kode_cart . "'";
                        } else {
                            $data_kode_back_order .= ',' . "'" . $data->kode_cart . "'";
                        }
                    }

                    $sql = "select	isnull(cart_order.companyid, '') as companyid, isnull(company.nama, '') as nama_company,
                                    isnull(company.alamat, '') as alamat_company, isnull(company.kota, '') as kota_company,
                                    isnull(company.telp, '') as telp_company, isnull(company.fax, '') as fax_company,
                                    isnull(cart_order.kd_cart, '') as kode_cart, isnull(cart_order.tanggal, '') as tanggal_cart,
                                    isnull(cart_order.kd_sales, '') as kode_sales, isnull(salesman.nm_sales, '') as nama_sales,
                                    isnull(cart_order.kd_dealer, '') as kode_dealer, isnull(dealer.nm_dealer, '') as nama_dealer,
                                    isnull(dealer.alamat1, '') as alamat_dealer, isnull(dealer.kota, '') as kota_dealer,
                                    isnull(dealer.ktp_h, '') as ktp_dealer, isnull(dealer.npwp, '') as npwp_dealer,
                                    isnull(nm_dealersj, '') as nama_dealer_sj, isnull(dealer.alamat1sj, '') as alamat_dealer_sj,
                                    isnull(dealer.kotasj, '') as kota_dealer_sj, isnull(dealer.ketsj1, '') as keterangan_dealer_sj1,
                                    isnull(dealer.ketsj2, '') as keterangan_dealer_sj2, isnull(cart_order.keterangan, '') as keterangan,
                                    isnull(cart_order.bo, '') as bo, isnull(cart_order_dtl.kd_part, '') as part_number,
                                    isnull(part.ket, '') as nama_part, isnull(cart_order_dtl.jml_order, 0) as jml_order,
                                    isnull(cart_order_dtl.harga, 0) as harga, isnull(cart_order_dtl.disc1, 0) as disc1,
                                    isnull(cart_order_dtl.disc2, 0) as disc2, isnull(cart_order_dtl.jumlah, 0) as jumlah,
                                    isnull(cart_order_dtl.kd_tpc, '') as kode_tpc, isnull(cart_order_dtl.umur_faktur, 0) as umur_faktur,
                                    dateadd(day, cart_order_dtl.umur_faktur, cart_order.tanggal) as tanggal_jatuh_tempo,
                                    isnull(cart_order.total, 0) as total
                            from
                            (
                                select	cart_order.companyid, cart_order.kd_cart, cart_order.tanggal, cart_order.kd_sales,
                                        cart_order.kd_dealer, cart_order.bo, cart_order.keterangan,
                                        cart_order.total
                                from	cart_order with (nolock)
                                where	cart_order.companyid=? and
                                        cart_order.kd_cart in (" . $data_kode_back_order . ")
                            )	cart_order
                                    left join company with (nolock) on cart_order.companyid=company.companyid
                                    left join salesman with (nolock) on cart_order.kd_sales=salesman.kd_sales and
                                                cart_order.companyid=salesman.companyid
                                    left join dealer with (nolock) on cart_order.kd_dealer=dealer.kd_dealer and
                                                cart_order.companyid=dealer.companyid
                                    left join cart_order_dtl with (nolock) on cart_order.kd_cart=cart_order_dtl.kd_cart and
                                                cart_order.companyid=cart_order_dtl.companyid
                                    left join part with (nolock) on cart_order_dtl.kd_part=part.kd_part and
                                                cart_order.companyid=part.companyid
                            order by cart_order.companyid asc, cart_order.kd_cart asc, cart_order_dtl.kd_part asc";

                    $result = DB::select($sql, [$request->get('companyid')]);
                    $result_back_order_detail = new Collection();
                    $jumlah_data_back_order = 0;
                    $nomor_urut = 0;
                    $kode_cart_temp = '';

                    foreach ($result as $data) {
                        $jumlah_data_back_order = (float)$jumlah_data_back_order + 1;

                        if (strtoupper(trim($kode_cart_temp)) != strtoupper(trim($data->kode_cart))) {
                            $nomor_urut = 0;
                        }

                        $result_back_order_detail->push((object) [
                            'no'            => (float)$nomor_urut + 1,
                            'kode_cart'     => strtoupper(trim($data->kode_cart)),
                            'part_number'   => strtoupper(trim($data->part_number)),
                            'nama_part'     => strtoupper(trim($data->nama_part)),
                            'kode_tpc'      => (float)$data->kode_tpc,
                            'umur_faktur'   => (float)$data->umur_faktur,
                            'jtp_faktur'    => strtoupper(trim($data->tanggal_jatuh_tempo)),
                            'jml_order'     => (float)$data->jml_order,
                            'harga'         => (float)$data->harga,
                            'disc1'         => (float)$data->disc1,
                            'disc2'         => (float)$data->disc2,
                            'jumlah'        => (float)$data->jumlah,
                            'image_part'    => trim(config('constants.api.url.images')) . '/' . trim($data->part_number) . '.jpg',
                        ]);

                        $kode_cart_temp = strtoupper(trim($data->kode_cart));

                        $data_back_order->push((object) [
                            'companyid'             => strtoupper(trim($data->companyid)),
                            'nama_company'          => strtoupper(trim($data->nama_company)),
                            'alamat_company'        => strtoupper(trim($data->alamat_company)),
                            'kota_company'          => strtoupper(trim($data->kota_company)),
                            'telp_company'          => strtoupper(trim($data->telp_company)),
                            'fax_company'           => strtoupper(trim($data->fax_company)),
                            'kode_cart'             => strtoupper(trim($data->kode_cart)),
                            'tanggal_cart'          => strtoupper(trim($data->tanggal_cart)),
                            'kode_sales'            => strtoupper(trim($data->kode_sales)),
                            'nama_sales'            => strtoupper(trim($data->nama_sales)),
                            'kode_dealer'           => strtoupper(trim($data->kode_dealer)),
                            'nama_dealer'           => strtoupper(trim($data->nama_dealer)),
                            'alamat_dealer'         => strtoupper(trim($data->alamat_dealer)),
                            'kota_dealer'           => strtoupper(trim($data->kota_dealer)),
                            'npwp_dealer'           => strtoupper(trim($data->npwp_dealer)),
                            'ktp_dealer'            => strtoupper(trim($data->ktp_dealer)),
                            'nama_dealer_sj'        => strtoupper(trim($data->nama_dealer_sj)),
                            'alamat_dealer_sj'      => strtoupper(trim($data->alamat_dealer_sj)),
                            'kota_dealer_sj'        => strtoupper(trim($data->kota_dealer_sj)),
                            'keterangan_dealer_sj1' => strtoupper(trim($data->keterangan_dealer_sj1)),
                            'keterangan_dealer_sj2' => strtoupper(trim($data->keterangan_dealer_sj2)),
                            'keterangan'            => strtoupper(trim($data->keterangan)),
                            'bo'                    => strtoupper(trim($data->bo)),
                            'total'                 => (float)$data->total
                        ]);
                    }

                    $kode_cart = '';
                    if ((float)$jumlah_data_back_order > 0) {
                        foreach ($data_back_order as $data) {
                            if (strtoupper(trim($kode_cart)) != strtoupper(trim($data->kode_cart))) {
                                $result_back_order->push((object) [
                                    'kode_cart'             => strtoupper(trim($data->kode_cart)),
                                    'tanggal_cart'          => strtoupper(trim($data->tanggal_cart)),
                                    'company'               => (object)[
                                        'companyid'         => strtoupper(trim($data->companyid)),
                                        'nama_company'      => strtoupper(trim($data->nama_company)),
                                        'alamat_company'    => strtoupper(trim($data->alamat_company)),
                                        'kota_company'      => strtoupper(trim($data->kota_company)),
                                        'telp_company'      => strtoupper(trim($data->telp_company)),
                                        'fax_company'       => strtoupper(trim($data->fax_company)),
                                    ],
                                    'salesman'              => (object)[
                                        'kode_sales'        => strtoupper(trim($data->kode_sales)),
                                        'nama_sales'        => strtoupper(trim($data->nama_sales)),
                                    ],
                                    'dealer'                    => (object)[
                                        'kode_dealer'           => strtoupper(trim($data->kode_dealer)),
                                        'nama_dealer'           => strtoupper(trim($data->nama_dealer)),
                                        'alamat_dealer'         => strtoupper(trim($data->alamat_dealer)),
                                        'kota_dealer'           => strtoupper(trim($data->kota_dealer)),
                                        'npwp_dealer'           => strtoupper(trim($data->npwp_dealer)),
                                        'ktp_dealer'            => strtoupper(trim($data->ktp_dealer)),
                                        'nama_dealer_sj'        => strtoupper(trim($data->nama_dealer_sj)),
                                        'alamat_dealer_sj'      => strtoupper(trim($data->alamat_dealer_sj)),
                                        'kota_dealer_sj'        => strtoupper(trim($data->kota_dealer_sj)),
                                        'keterangan_dealer_sj1' => strtoupper(trim($data->keterangan_dealer_sj1)),
                                        'keterangan_dealer_sj2' => strtoupper(trim($data->keterangan_dealer_sj2)),
                                    ],
                                    'keterangan'            => strtoupper(trim($data->keterangan)),
                                    'bo'                    => strtoupper(trim($data->bo)),
                                    'total'                 => (float)$data->total,
                                    'detail'                => collect($result_back_order_detail)
                                        ->where('kode_cart', $data->kode_cart)
                                        ->values()
                                        ->all()
                                ]);
                                $kode_cart = strtoupper(trim($data->kode_cart));
                            }
                        }
                    }
                }
            } else {
                if ($status_purchase_order == 1) {
                    $nomor_urut_purchase_order = 0;
                    $data_kode_purchase_order = '';

                    foreach ($data_nomor_purchase_order as $data) {
                        $nomor_urut_purchase_order = (float)$nomor_urut_purchase_order + 1;

                        if ((float)$nomor_urut_purchase_order == 1) {
                            $data_kode_purchase_order .= "'" . $data->kode_pof . "'";
                        } else {
                            $data_kode_purchase_order .= ',' . "'" . $data->kode_pof . "'";
                        }
                    }

                    $sql = "select	isnull(pof.companyid, '') as companyid, isnull(company.nama, '') as nama_company,
                                    isnull(company.alamat, '') as alamat_company, isnull(company.kota, '') as kota_company,
                                    isnull(company.telp, '') as telp_company, isnull(company.fax, '') as fax_company,
                                    isnull(pof.no_pof, '') as nomor_pof, isnull(pof.tgl_pof, '') as tanggal_pof,
                                    isnull(pof.kd_sales, '') as kode_sales, isnull(salesman.nm_sales, '') as nama_sales,
                                    isnull(pof.kd_dealer, '') as kode_dealer, isnull(dealer.nm_dealer, '') as nama_dealer,
                                    isnull(dealer.alamat1, '') as alamat_dealer, isnull(dealer.kota, '') as kota_dealer,
                                    isnull(dealer.ktp_h, '') as ktp_dealer, isnull(dealer.npwp, '') as npwp_dealer,
                                    isnull(nm_dealersj, '') as nama_dealer_sj, isnull(dealer.alamat1sj, '') as alamat_dealer_sj,
                                    isnull(dealer.kotasj, '') as kota_dealer_sj, isnull(dealer.ketsj1, '') as keterangan_dealer_sj1,
                                    isnull(dealer.ketsj2, '') as keterangan_dealer_sj2, isnull(pof.ket, '') as keterangan,
                                    isnull(pof.kd_tpc, '') as kode_tpc, isnull(pof.umur_pof, 0) as umur_pof,
                                    isnull(pof.tgl_akhir_pof, '') as tanggal_akhir_pof, isnull(pof.bo, '') as bo,
                                    isnull(pof_dtl.kd_part, '') as part_number, isnull(part.ket, '') as nama_part,
                                    isnull(pof_dtl.jml_order, 0) as jml_order, isnull(pof_dtl.harga, 0) as harga,
                                    isnull(pof_dtl.disc1, 0) as disc_detail, isnull(pof_dtl.jumlah, 0) as jumlah,
                                    isnull(pof.disc, 0) as disc_header, isnull(pof.total, 0) as total,
                                    isnull(cart_order.kd_cart, '') as kode_cart
                            from
                            (
                                select	pof.companyid, pof.no_pof, pof.tgl_pof, pof.kd_sales, pof.kd_dealer,
                                        pof.kd_tpc, pof.umur_pof, pof.tgl_akhir_pof, pof.bo, pof.ket,
                                        pof.no_order, pof.disc, pof.total
                                from	pof with (nolock)
                                where	pof.companyid=? and
                                        pof.no_pof in (" . $data_kode_purchase_order . ")
                            )	pof
                                    left join company with (nolock) on pof.companyid=company.companyid
                                    left join cart_order with (nolock) on pof.no_order=cart_order.kd_cart and
                                                pof.companyid=cart_order.companyid
                                    left join salesman with (nolock) on pof.kd_sales=salesman.kd_sales and
                                                pof.companyid=salesman.companyid
                                    left join dealer with (nolock) on pof.kd_dealer=dealer.kd_dealer and
                                                pof.companyid=dealer.companyid
                                    left join pof_dtl with (nolock) on pof.no_pof=pof_dtl.no_pof and
                                                pof.companyid=pof_dtl.companyid
                                    left join part with (nolock) on pof_dtl.kd_part=part.kd_part and
                                                pof.companyid=part.companyid
                            order by pof.companyid asc, pof.no_pof asc, pof_dtl.kd_part asc";

                    $result = DB::select($sql, [$request->get('companyid')]);
                    $result_purchase_order_detail = new Collection();
                    $jumlah_data_purchase_order = 0;

                    foreach ($result as $data) {
                        $jumlah_data_purchase_order = (float)$jumlah_data_purchase_order + 1;

                        $result_purchase_order_detail->push((object) [
                            'nomor_pof'     => strtoupper(trim($data->nomor_pof)),
                            'part_number'   => strtoupper(trim($data->part_number)),
                            'nama_part'     => strtoupper(trim($data->nama_part)),
                            'jml_order'     => (float)$data->jml_order,
                            'harga'         => (float)$data->harga,
                            'disc_detail'   => (float)$data->disc_detail,
                            'jumlah'        => (float)$data->jumlah,
                            'image_part'    => trim(config('constants.api.url.images')) . '/' . trim($data->part_number) . '.jpg',
                        ]);

                        $data_purchase_order->push((object) [
                            'companyid'             => strtoupper(trim($data->companyid)),
                            'nama_company'          => strtoupper(trim($data->nama_company)),
                            'alamat_company'        => strtoupper(trim($data->alamat_company)),
                            'kota_company'          => strtoupper(trim($data->kota_company)),
                            'telp_company'          => strtoupper(trim($data->telp_company)),
                            'fax_company'           => strtoupper(trim($data->fax_company)),
                            'nomor_pof'             => strtoupper(trim($data->nomor_pof)),
                            'kode_cart'             => strtoupper(trim($data->kode_cart)),
                            'tanggal_pof'           => strtoupper(trim($data->tanggal_pof)),
                            'kode_sales'            => strtoupper(trim($data->kode_sales)),
                            'nama_sales'            => strtoupper(trim($data->nama_sales)),
                            'kode_dealer'           => strtoupper(trim($data->kode_dealer)),
                            'nama_dealer'           => strtoupper(trim($data->nama_dealer)),
                            'alamat_dealer'         => strtoupper(trim($data->alamat_dealer)),
                            'kota_dealer'           => strtoupper(trim($data->kota_dealer)),
                            'npwp_dealer'           => strtoupper(trim($data->npwp_dealer)),
                            'ktp_dealer'            => strtoupper(trim($data->ktp_dealer)),
                            'nama_dealer_sj'        => strtoupper(trim($data->nama_dealer_sj)),
                            'alamat_dealer_sj'      => strtoupper(trim($data->alamat_dealer_sj)),
                            'kota_dealer_sj'        => strtoupper(trim($data->kota_dealer_sj)),
                            'keterangan_dealer_sj1' => strtoupper(trim($data->keterangan_dealer_sj1)),
                            'keterangan_dealer_sj2' => strtoupper(trim($data->keterangan_dealer_sj2)),
                            'keterangan'            => strtoupper(trim($data->keterangan)),
                            'kode_tpc'              => strtoupper(trim($data->kode_tpc)),
                            'umur_pof'              => strtoupper(trim($data->umur_pof)),
                            'tanggal_akhir_pof'     => strtoupper(trim($data->tanggal_akhir_pof)),
                            'bo'                    => strtoupper(trim($data->bo)),
                            'disc_header'           => (float)$data->disc_header,
                            'total'                 => (float)$data->total,
                        ]);
                    }

                    $nomor_pof = '';
                    if ((float)$jumlah_data_purchase_order > 0) {
                        foreach ($data_purchase_order as $data) {
                            if (strtoupper(trim($nomor_pof)) != strtoupper(trim($data->nomor_pof))) {
                                $sub_total = collect($result_purchase_order_detail
                                    ->where('nomor_pof', $data->nomor_pof)
                                    ->values()
                                    ->all())->sum('jumlah');

                                $result_purchase_order->push((object) [
                                    'nomor_pof'             => strtoupper(trim($data->nomor_pof)),
                                    'tanggal_pof'           => strtoupper(trim($data->tanggal_pof)),
                                    'kode_cart'             => strtoupper(trim($data->kode_cart)),
                                    'company'               => (object)[
                                        'companyid'         => strtoupper(trim($data->companyid)),
                                        'nama_company'      => strtoupper(trim($data->nama_company)),
                                        'alamat_company'    => strtoupper(trim($data->alamat_company)),
                                        'kota_company'      => strtoupper(trim($data->kota_company)),
                                        'telp_company'      => strtoupper(trim($data->telp_company)),
                                        'fax_company'       => strtoupper(trim($data->fax_company)),
                                    ],
                                    'salesman'              => (object)[
                                        'kode_sales'        => strtoupper(trim($data->kode_sales)),
                                        'nama_sales'        => strtoupper(trim($data->nama_sales)),
                                    ],
                                    'dealer'                    => (object)[
                                        'kode_dealer'           => strtoupper(trim($data->kode_dealer)),
                                        'nama_dealer'           => strtoupper(trim($data->nama_dealer)),
                                        'alamat_dealer'         => strtoupper(trim($data->alamat_dealer)),
                                        'kota_dealer'           => strtoupper(trim($data->kota_dealer)),
                                        'npwp_dealer'           => strtoupper(trim($data->npwp_dealer)),
                                        'ktp_dealer'            => strtoupper(trim($data->ktp_dealer)),
                                        'nama_dealer_sj'        => strtoupper(trim($data->nama_dealer_sj)),
                                        'alamat_dealer_sj'      => strtoupper(trim($data->alamat_dealer_sj)),
                                        'kota_dealer_sj'        => strtoupper(trim($data->kota_dealer_sj)),
                                        'keterangan_dealer_sj1' => strtoupper(trim($data->keterangan_dealer_sj1)),
                                        'keterangan_dealer_sj2' => strtoupper(trim($data->keterangan_dealer_sj2)),
                                    ],
                                    'keterangan'            => strtoupper(trim($data->keterangan)),
                                    'kode_tpc'              => strtoupper(trim($data->kode_tpc)),
                                    'umur_pof'              => strtoupper(trim($data->umur_pof)),
                                    'tanggal_akhir_pof'     => strtoupper(trim($data->tanggal_akhir_pof)),
                                    'bo'                    => strtoupper(trim($data->bo)),
                                    'sub_total'             => (float)$sub_total,
                                    'disc_header'           => (float)$data->disc_header,
                                    'disc_header_rupiah'    => ((float)$sub_total * (float)$data->disc_header) / 100,
                                    'total'                 => (float)$data->total,
                                    'detail'                => collect($result_purchase_order_detail)
                                        ->where('nomor_pof', $data->nomor_pof)
                                        ->values()
                                        ->all()
                                ]);
                                $nomor_pof = strtoupper(trim($data->nomor_pof));
                            }
                        }
                    }
                }
            }

            $data_result = [
                'faktur'    => (strtoupper(trim($request->get('role_id'))) == 'D_H3') ? $result_faktur : [],
                'bo'        => (strtoupper(trim($request->get('role_id'))) == 'D_H3') ? $result_back_order : [],
                'pof'       => (strtoupper(trim($request->get('role_id'))) != 'D_H3') ? $result_purchase_order : [],
            ];
            return Response::responseSuccess('success', $data_result);
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

    public function cekAturanHargaCart(Request $request)
    {
        try {
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
        $validate = Validator::make($request->all(), [
            'user_id'       => 'required|string',
            'companyid'     => 'required|string',
        ]);

        if ($validate->fails()) {
            return Response::responseWarning("Kolom password dan ulangi password tidak boleh kosong");
        }

        $sql = "select	cart_ordertmp.companyid, cart_ordertmp.kd_key, cart_ordertmp.kd_cart,
                        sum(isnull(cart_ordertmp.terlayani, 0) * isnull(cart_ordertmp.hrg_satuan, 0)) as total
                from
                (
                    select	cart_ordertmp.companyid, cart_ordertmp.kd_key, cart_ordertmp.kd_cart,
                            cart_ordertmp.kd_part, cart_ordertmp.hrg_satuan, cart_ordertmp.jml_order,
                            cart_ordertmp.stock,
                            iif(isnull(cart_ordertmp.stock, 0) >= isnull(cart_ordertmp.jml_order, 0),
                                isnull(cart_ordertmp.jml_order, 0),
                                iif(isnull(cart_ordertmp.stock, 0) > 0, isnull(cart_ordertmp.stock, 0), 0)
                            ) as terlayani
                    from
                    (
                        select	cart_ordertmp.companyid, cart_ordertmp.kd_key, cart_ordertmp.kd_cart,
                                cart_ordertmp.kd_part, cart_ordertmp.jml_order, cart_ordertmp.hrg_satuan,
                                (isnull(tbstlokasirak.stock, 0) -
                                    (isnull(stlokasi.min, 0) + isnull(part.kanvas, 0) + isnull(part.min_gudang, 0) +
                                        isnull(part.in_transit, 0) + isnull(part.min_htl, 0))) - isnull(minshare.qtymkr, 0) as stock
                        from
                        (
                            select	cart_ordertmp.companyid, cart_ordertmp.kd_key, cart_ordertmp.kd_cart,
                                    cart_order_dtltmp.kd_part, cart_order_dtltmp.kd_lokasi, cart_order_dtltmp.kd_rak,
                                    cart_order_dtltmp.jml_order, cart_order_dtltmp.hrg_satuan
                            from
                            (
                                select	cart_ordertmp.companyid, cart_ordertmp.kd_key,
                                        cart_ordertmp.kd_cart
                                from	cart_ordertmp with (nolock)
                                where	cart_ordertmp.kd_key=? and
                                        cart_ordertmp.companyid=?
                            )	cart_ordertmp
                                    left join cart_order_dtltmp with (nolock) on cart_ordertmp.kd_key=cart_order_dtltmp.kd_key and
                                                cart_ordertmp.kd_cart=cart_order_dtltmp.kd_cart and
                                                cart_ordertmp.companyid=cart_order_dtltmp.companyid
                        )	cart_ordertmp
                                left join company with (nolock) on cart_ordertmp.companyid=company.companyid
                                left join part with (nolock) on cart_ordertmp.kd_part=part.kd_part and
                                            cart_ordertmp.companyid=part.companyid
                                left join stlokasi with (nolock) on cart_ordertmp.kd_part=stlokasi.kd_part and
                                            cart_ordertmp.kd_lokasi=stlokasi.kd_lokasi and
                                            cart_ordertmp.companyid=stlokasi.companyid
                                left join tbstlokasirak with (nolock) on cart_ordertmp.kd_part=tbstlokasirak.kd_part and
                                            cart_ordertmp.kd_lokasi=tbstlokasirak.kd_lokasi and
                                            cart_ordertmp.kd_rak=tbstlokasirak.kd_rak and
                                            cart_ordertmp.companyid=tbstlokasirak.companyid
                                left join minshare with (nolock) on cart_ordertmp.kd_part=minshare.kd_part and
                                            cart_ordertmp.companyid=minshare.companyid
                    )	cart_ordertmp
                    where	isnull(cart_ordertmp.stock, 0) > 0
                )	cart_ordertmp
                group by cart_ordertmp.companyid, cart_ordertmp.kd_key, cart_ordertmp.kd_cart";

        $result = DB::select($sql, [$request->get('user_id'), $request->get('companyid')]);

        $status = 0;
        $jumlah_data = 0;
        $total_cart = 0;

        foreach ($result as $data) {
            $jumlah_data = (float)$jumlah_data + 1;
            $total_cart = (float)$total_cart + (float)$data->total;
        }

        $message = '';

        if ((float)$jumlah_data <= 0) {
            $status = 0;
            $message = 'Data cart anda tidak ditemukan, ulangi proses anda dengan refresh halaman';
        } else {
            $status = 1;

            if ((float)$total_cart < 500000) {
                if (strtoupper(trim($request->get('role_id'))) == 'D_H3') {
                    $message = 'Harga order kurang dari 500 ribu, apakah anda tetap ingin melanjutkan transaksi ini ?';
                } else {
                    $message = 'STATUS_OK';
                }
            } else {
                $message = 'STATUS_OK';
            }
        }

        if ($status == 0) {
            return Response::responseError($message);
        } else {
            return Response::responseSuccess($message, null);
        }
    }
}
