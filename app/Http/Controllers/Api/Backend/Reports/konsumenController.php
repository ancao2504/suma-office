<?php

namespace app\Http\Controllers\Api\Backend\Reports;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class KonsumenController extends Controller
{
    public function data(Request $request)
    {
        try {
            $menu_aktif = [];

            //!  validasi
            $validator = Validator::make($request->all(), [
                'companyid' => 'required',
                'kd_lokasi' => 'required',
            ], [
                'companyid.required' => 'Companyid harus diisi',
                'kd_lokasi.required' => 'Kode lokasi harus diisi',
            ]);

            if ($validator->fails()) {
                return Response()->json([
                    'status'    => 0,
                    'message'   => $validator->errors()->first(),
                    'data'      => ''
                ], 200);
            }

            if(!in_array($request->devisi, ['honda','fdr'])){
                $request->merge(['devisi' => 'honda']);
                array_push($menu_aktif, 'divisi');
            }

            if($request->divisi == 'honda'){
                $request->merge(['db' => 'dbhonda.dbo.']);
            } else {
                $request->merge(['db' => 'dbsuma.dbo.']);
            }
            // ! end validasi

            $data = DB::table(DB::raw($request->db.'faktur'))
            ->lock('WITH(NOLOCK)')
            ->select(
                'konsumen.nama',
                'konsumen.tgl_lahir',
                'konsumen.alamat',
                'konsumen.telepon',
                'konsumen.jenis',
                'konsumen.type',
                'konsumen.merk',
                'part.kd_part',
                'part.ket',
                'part.jenis as ring',
                'faktur.tgl_faktur',
                DB::raw($request->divisi.' as divisi'),
                'faktur.CompanyId',
                'fakt_dtl.kd_lokasi'
            )
            ->JoinSub(function ($query) use ($request) {
                $query->select('fakt_dtl.no_faktur', 'fakt_dtl.companyid', 'fakt_dtl.kd_lokasi')
                ->from(DB::raw($request->db.'fakt_dtl'))
                ->whereIn('fakt_dtl.kd_lokasi', Arr::wrap($request->kd_lokasi));
            }, 'fakt_dtl', function ($join) {
                $join->on('faktur.no_faktur', '=', 'fakt_dtl.no_faktur')
                ->on('faktur.CompanyId', '=', 'fakt_dtl.companyid');
            })
            ->leftJoinSub(function ($query) use ($request){
                $query->select(
                'konsumen.companyid',
                'konsumen.no_faktur',
                'konsumen.tanggal',
                'konsumen.nik',
                'konsumen.nama',
                'konsumen.tempat_lahir',
                'konsumen.tgl_lahir',
                'konsumen.alamat',
                'konsumen.telepon',
                'konsumen.email',
                'konsumen.nopol',
                'konsumen.jenis',
                'konsumen.merk',
                'konsumen.type',
                'konsumen.tahun_motor',
                'konsumen.keterangan',
                'konsumen.mengetahui',
                'konsumen.keterangan_mengetahui',
                )->groupBy(
                'konsumen.companyid',
                'konsumen.no_faktur',
                'konsumen.tanggal',
                'konsumen.nik',
                'konsumen.nama',
                'konsumen.tempat_lahir',
                'konsumen.tgl_lahir',
                'konsumen.alamat',
                'konsumen.telepon',
                'konsumen.email',
                'konsumen.nopol',
                'konsumen.jenis',
                'konsumen.merk',
                'konsumen.type',
                'konsumen.tahun_motor',
                'konsumen.keterangan',
                'konsumen.mengetahui',
                'konsumen.keterangan_mengetahui')
                ->from(DB::raw($request->db.'konsumen'))
                ->where('konsumen.companyid', $request->companyid);
            }, 'konsumen', function ($join) {
                $join->on('faktur.no_faktur', '=', 'konsumen.no_faktur')
                ->on('faktur.CompanyId', '=', 'konsumen.companyid');
            })
            ->leftJoinSub(function ($query) use ($request){
                $query->select('*')
                ->from(DB::raw($request->db.'part'));
            }, 'part', function ($join) {
                $join->on('fakt_dtl.kd_part', '=', 'part.kd_part')
                ->on('faktur.CompanyId', '=', 'part.CompanyId');
            })
            ->leftJoinSub(function ($query) use ($request){
                $query->select('*')
                ->from(DB::raw($request->db.'sub'));
            }, 'sub', function ($join) {
                $join->on('part.kd_sub', '=', 'sub.kd_sub')
                ->on('faktur.CompanyId', '=', 'sub.CompanyId');
            })
            ->leftJoinSub(function ($query) use ($request){
                $query->select('*')
                ->from(DB::raw($request->db.'produk'));
            }, 'produk', function ($join) {
                $join->on('sub.kd_produk', '=', 'produk.kd_produk');
            })
            ->where('faktur.CompanyId', $request->companyid);

            if(!empty($request->tgl_transaksi)){
                if (count(Arr::wrap($request->tgl_transaksi)) == 1) {
                    $data = $data->whereMonth('faktur.tgl_faktur', date('m', strtotime($request->tgl_transaksi[0])))
                    ->whereYear('faktur.tgl_faktur', date('Y', strtotime($request->tgl_transaksi[0])));
                } else {
                    $data = $data->whereBetween('faktur.tgl_faktur', [$request->tgl_transaksi[0], $request->tgl_transaksi[1]]);
                }
            } else {
                $data = $data->whereMonth('faktur.tgl_faktur', date('m'))
                ->whereYear('faktur.tgl_faktur', date('Y'));
            }
            array_push($menu_aktif, 'tgl_faktur');

            if(!empty($request->jenis_motor)){
                $data = $data->where('konsumen.jenis', 'like', '%'.$request->jenis_motor.'%');
                array_push($menu_aktif, 'jenis_motor');
            }
            if (!empty($request->type_motor)) {
                $data = $data->where('konsumen.type', 'like', '%'.$request->type_motor.'%');
                array_push($menu_aktif, 'type_motor');
            }
            if (!empty($request->merk_motor)) {
                $data = $data->where('konsumen.merk', 'like', '%'.$request->merk_motor.'%');
                array_push($menu_aktif, 'merk_motor');
            }
            if (!empty($request->ukuran_ban)) {
                $data = $data->where('part.kd_part', 'like', '%' . $request->ukuran_ban . '%');
                array_push($menu_aktif, 'ukuran_ban');
            }
            if (!empty($request->ukuran_ring)) {
                $data = $data->where('part.jenis', 'like', '%' . $request->ukuran_ring . '%');
                array_push($menu_aktif, 'ukuran_ring');
            }
            if(!empty($request->tgl_lahir)){
                if (count(Arr::wrap($request->tgl_lahir)) == 1) {
                    $data = $data->whereMonth('konsumen.tgl_lahir', date('m', strtotime(Arr::wrap($request->tgl_lahir)[0])));
                } else {
                    $data = $data->whereRaw('MONTH(konsumen.tgl_lahir) BETWEEN ? AND ?', [date('m', strtotime($request->tgl_lahir[0])), date('m', strtotime($request->tgl_lahir[1]))])
                    ->whereRaw('DAY(konsumen.tgl_lahir) BETWEEN ? AND ?', [date('d', strtotime($request->tgl_lahir[0])), date('d', strtotime($request->tgl_lahir[1]))]);
                }
                array_push($menu_aktif, 'tgl_lahir');
            }
            if(!empty($request->filter)){
                foreach ($request->filter as $key => $value) {
                    if(in_array($key, ['tgl_faktur', 'jenis', 'type', 'merk', 'ring', 'kd_part', 'ket', 'nama', 'tgl_lahir', 'alamat', 'telepon']) && in_array($value, ['asc', 'desc'])){
                        match ($key) {
                            in_array($key, ['kd_part', 'ket'])  => $data = $data->orderBy('part.'.$key, $value),
                            'ring'  => $data = $data->orderBy('part.jenis', $value),
                            'tgl_lahir' => $data = $data->orderByRaw('MONTH(konsumen.tgl_lahir) '.$value.', DAY(konsumen.tgl_lahir) '.$value),
                            default => $data = $data->orderBy('konsumen.'.$key, $value),
                        };
                    }
                }
            }

            return Response()->json([
                'status'    => 1,
                'data'      => ['data' => $data->paginate($request->per_page), 'filter' => $menu_aktif],
                'message'   => 'success',
            ], 200);
        } catch (\Exception $e) {
            return Response()->json([
                'status'    => 0,
                'data'      => '',
                'message'   => 'error',
            ], 200);
        }
    }

    public function export(Request $request){
        try {
            

            // return Response()->json([
            //     'status'    => 1,
            //     'message'   => 'success',
            //     'data'      => $data
            // ], 200);
        } catch (\Exception $e) {
            return Response()->json([
                'status'    => 2,
                'message'   => 'Maaf, terjadi kesalahan. Silahkan coba lagi',
                'data'      => ''
            ], 200);
        }
    }
}
