<?php

namespace App\Http\Controllers\Api\Backend\Online;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class ApiStockController extends Controller
{
    public function daftarStockPartNumber(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'companyid'  => 'required',
            ]);

            if($validate->fails()) {
                return Response::responseWarning('Anda Belum Login');
            }

            $length_part_number = Str::length($request->get('part_number'));

            if((int)$length_part_number < 5) {
                return Response::responseWarning("Data part number harus diisi minimal 5 karakter");
            }

            $sql = DB::table('stlokasi')->lock('with (nolock)')
                    ->selectRaw("isnull(stlokasi.kd_part, '') as part_number")
                    ->where('stlokasi.companyid', $request->get('companyid'))
                    ->whereRaw("stlokasi.kd_lokasi in ('OB','OL','OP','OS','OT','OK')")
                    ->orderByRaw("stlokasi.kd_part asc");

            if(!empty($request->get('part_number')) && trim($request->get('part_number')) != '') {
                $sql->where('stlokasi.kd_part', 'like', $request->get('part_number').'%');
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
            $data_part_number = '';
            $list_part_number = new Collection();

            foreach ($data as $record) {
                $jumlah_data = (double)$jumlah_data + 1;

                if (trim($data_part_number) == '') {
                    $data_part_number .= "'".strtoupper(trim($record->part_number))."'";
                } else {
                    $data_part_number .= ",'".strtoupper(trim($record->part_number))."'";
                }
            }

            $data_stock_internal = new Collection();

            if((double)$jumlah_data > 0) {
                $sql = "select	isnull(stlokasi.kd_part, '') as part_number,
                                isnull(part.ket, '') as nama_part,
                                isnull(part.het, 0) as het,
                                sum(iif(stlokasi.kd_lokasi='OB', isnull(stlokasi.jumlah, 0), 0)) as stock_bukalapak,
                                sum(iif(stlokasi.kd_lokasi='OL', isnull(stlokasi.jumlah, 0), 0)) as stock_tokopedia,
                                sum(iif(stlokasi.kd_lokasi='OP', isnull(stlokasi.jumlah, 0), 0)) as stock_paket,
                                sum(iif(stlokasi.kd_lokasi='OS', isnull(stlokasi.jumlah, 0), 0)) as stock_shopee,
                                sum(iif(stlokasi.kd_lokasi='OT', isnull(stlokasi.jumlah, 0), 0)) as stock_tiktok,
                                sum(iif(stlokasi.kd_lokasi='OK', isnull(stlokasi.jumlah, 0), 0)) as stock_camboja,
                                sum(iif(stlokasi.kd_lokasi='OB', isnull(part.het, 0) * isnull(stlokasi.jumlah, 0), 0)) as het_bukalapak,
                                sum(iif(stlokasi.kd_lokasi='OL', isnull(part.het, 0) * isnull(stlokasi.jumlah, 0), 0)) as het_tokopedia,
                                sum(iif(stlokasi.kd_lokasi='OP', isnull(part.het, 0) * isnull(stlokasi.jumlah, 0), 0)) as het_paket,
                                sum(iif(stlokasi.kd_lokasi='OS', isnull(part.het, 0) * isnull(stlokasi.jumlah, 0), 0)) as het_shopee,
                                sum(iif(stlokasi.kd_lokasi='OT', isnull(part.het, 0) * isnull(stlokasi.jumlah, 0), 0)) as het_tiktok,
                                sum(iif(stlokasi.kd_lokasi='OK', isnull(part.het, 0) * isnull(stlokasi.jumlah, 0), 0)) as het_camboja
                        from
                        (
                            select	stlokasi.companyid, stlokasi.kd_lokasi, stlokasi.kd_part,
                                    stlokasi.jumlah
                            from	stlokasi with (nolock)
                            where	stlokasi.companyid=? and
                                    stlokasi.kd_part in (".$data_part_number.") and
                                    stlokasi.kd_lokasi in ('OB','OL','OP','OS','OT','OK')
                        )	stlokasi
                                left join part with (nolock) on stlokasi.kd_part=part.kd_part and
                                        stlokasi.companyid=part.companyid
                        group by stlokasi.companyid, stlokasi.kd_part, part.ket, part.het
                        order by stlokasi.companyid asc, stlokasi.kd_part asc";

                $result = DB::select($sql, [ $request->get('companyid') ]);

                foreach($result as $data) {
                    $list_part_number->push((object) [
                        'part_number'   => strtoupper(trim($data->part_number)),
                        'nama_part'     => trim($data->nama_part),
                        'het'           => (double)$data->het,
                        'total'         => [
                            'bukalapak' => [
                                'stock' => (double)$data->stock_bukalapak,
                                'amount'=> (double)$data->het_bukalapak
                            ],
                            'tokopedia' => [
                                'stock' => (double)$data->stock_tokopedia,
                                'amount'=> (double)$data->het_tokopedia
                            ],
                            'shopee' => [
                                'stock' => (double)$data->stock_shopee,
                                'amount'=> (double)$data->het_shopee
                            ],
                            'paket' => [
                                'stock' => (double)$data->stock_paket,
                                'amount'=> (double)$data->het_paket
                            ],
                            'tiktok' => [
                                'stock' => (double)$data->stock_tiktok,
                                'amount'=> (double)$data->het_tiktok
                            ],
                            'camboja' => [
                                'stock' => (double)$data->stock_camboja,
                                'amount'=> (double)$data->het_camboja
                            ],
                        ],
                    ]);
                }


                $data_stock_internal = [
                    'current_page'  => $current_page,
                    'data'          => $list_part_number,
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
            }

            return Response::responseSuccess('success', $data_stock_internal);
        } catch (\Exception $exception) {
            return Response::responseError($request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
