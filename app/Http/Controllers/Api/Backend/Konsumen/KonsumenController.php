<?php

namespace app\Http\Controllers\Api\Backend\Konsumen;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Arr;

class KonsumenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'divisi'    => 'required',
                'companyid' => 'required',
                'kd_lokasi' => 'required',
            ],[
                'divisi.required'       => 'Divisi tidak boleh kosong',
                'companyid.required'    => 'Companyid tidak boleh kosong',
                'kd_lokasi.required'    => 'Lokasi tidak boleh kosong',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            if(!in_array($request->per_page, [10,50,100,500])){
                $request->merge(['per_page' => 10]);
            }

            if($request->divisi == 'fdr'){
                $request->merge(['db' => 'dbsuma.dbo.']);
            } else {
                $request->merge(['db' => 'dbhonda.dbo.']);
            }

            $data = DB::table(DB::raw($request->db.'konsumen'))
            ->lock('with (nolock)')
            ->select(
                DB::raw("isnull(konsumen.id, '') as id"),
                DB::raw("isnull(convert(varchar, konsumen.tanggal, 106), '') as tanggal"),
                DB::raw("isnull(konsumen.nama, '') as nama"),
                DB::raw("isnull(convert(varchar, konsumen.tgl_lahir, 106), '') as tgl_lahir"),
                DB::raw("isnull(konsumen.nik, '') as nik"),
                DB::raw("isnull(konsumen.nopol, '') as nopol"),
                DB::raw("isnull(konsumen.no_faktur, '') as no_faktur"),
                DB::raw("isnull(konsumen.companyid, '') as companyid"),
                DB::raw("isnull(konsumen.keterangan, '') as keterangan"),
                DB::raw("isnull(konsumen.kd_lokasi, '') as kd_lokasi")
            );

            if ($request->divisi == 'honda') {
                $data = $data->addSelect(DB::raw("'Honda' as divisi"));
            } else if ($request->divisi == 'fdr') {
                $data = $data->addSelect(DB::raw("'FDR' as divisi"));
            }

            if($request->option == 'page'){
                $data = $data->where('konsumen.companyid', '=', trim($request->companyid))
                ->whereIn('konsumen.kd_lokasi',  Arr::wrap($request->kd_lokasi));

                if (!empty($request->search)) {
                    match ($request->by){
                        'id'        => $data = $data->where('konsumen.id', 'like', '%' . $request->search . '%'),
                        'nama'      => $data = $data->where('konsumen.nama', 'like', '%' . $request->search . '%'),
                        'nik'       => $data = $data->where('konsumen.nik', 'like', '%' . $request->search . '%'),
                        'nopol'     => $data = $data->where('konsumen.nopol', 'like', '%' . $request->search . '%'),
                        'no_faktur' => $data = $data->where('konsumen.no_faktur', 'like', '%' . $request->search . '%'),
                        default     => $data = $data->where('konsumen.no_faktur', 'like', '%' . $request->search . '%'),
                    };
                } else {
                    $data = $data->whereYear('konsumen.tanggal', date('Y'));
                }

                $data = $data->orderBy('konsumen.tanggal', 'desc')
                ->orderBy('konsumen.usertime', 'desc')
                ->paginate($request->per_page);
            } else if ($request->option == 'first'){
                $data = $data->select('*')
                ->leftJoinSub(function ($query) use ($request) {
                    $query->select('faktur.no_faktur', 'faktur.total')
                        ->from(DB::raw($request->db.'faktur'))
                        ->where('faktur.CompanyId', '=', trim($request->companyid));
                }, 'faktur', 'faktur.no_faktur', '=', 'konsumen.no_faktur')
                ->where('konsumen.id', '=', $request->id)
                ->where('konsumen.companyid', '=', trim($request->companyid))
                ->where('konsumen.kd_lokasi',  $request->kd_lokasi)
                ->first();
            }

            return Response::responseSuccess('success', $data);
        } catch (\Throwable $exception) {
            return Response::responseError($request->get('user_id'), 'API', route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }

    public function konsumenStore(Request $request)
    {
        $validate = Validator::make($request->all(),
            [
                'nomor_faktur'          => 'required',
                'divisi'                => 'required|in:honda,fdr',
                'nama_pelanggan'        => 'required',
                'tempat_lahir'          => 'required',
                'tanggal_lahir'         => 'required',
                'alamat'                => 'required',
                'telepon'               => 'required',
                'nopol'                 => 'required',
                'merk_motor'            => 'required',
                'tipe_motor'            => 'required',
                'jenis_motor'           => 'required',
                'companyid'             => 'required',
                'kd_lokasi'             => 'required',
            ],
            [
                'nomor_faktur.required'         => 'Nomor Faktur tidak boleh kosong',
                'divisi.required'               => 'Divisi diatas harus dipilih',
                'divisi.in'                     => 'Divisi tidak valid',
                'nama_pelanggan.required'       => 'Nama Pelanggan tidak boleh kosong',
                'tempat_lahir.required'         => 'Tempat Lahir tidak boleh kosong',
                'tanggal_lahir.required'        => 'Tanggal Lahir tidak boleh kosong',
                'alamat.required'               => 'Alamat tidak boleh kosong',
                'telepon.required'              => 'Telepon tidak boleh kosong',
                'nopol.required'                => 'No Polisi tidak boleh kosong',
                'merk_motor.required'           => 'Merk Motor tidak boleh kosong',
                'tipe_motor.required'           => 'Tipe Motor tidak boleh kosong',
                'jenis_motor.required'          => 'Jenis Motor tidak boleh kosong',
                'companyid.required'            => 'Cabang tidak boleh kosong',
                'kd_lokasi.required'            => 'Lokasi tidak boleh kosong',
            ]
        );

        if ($validate->fails()) {
            return Response::responseWarning($validate->errors()->first());
        }

        if($request->divisi == 'honda'){
            $request->merge([
                'db' => 'dbhonda.dbo.'
            ]);
        } else if($request->divisi == 'fdr'){
            $request->merge([
                'db' => 'dbsuma.dbo.'
            ]);
        }

        $result = DB::table(DB::raw($request->db.'faktur'))
        ->lock('with (nolock)')
        ->select('faktur.no_faktur')
        ->joinsub(function ($query) use ($request) {
            $query->select('fakt_dtl.no_faktur', 'fakt_dtl.kd_lokasi', 'fakt_dtl.CompanyId')
                ->from(DB::raw($request->db.'fakt_dtl'))
                ->where('fakt_dtl.CompanyId', '=', trim($request->companyid))
                ->where('fakt_dtl.kd_lokasi', '=', trim($request->kd_lokasi))
                ->groupBy('fakt_dtl.no_faktur', 'fakt_dtl.kd_lokasi', 'fakt_dtl.CompanyId');
        }, 'fakt_dtl', 'faktur.no_faktur', '=', 'fakt_dtl.no_faktur')
        ->leftJoinSub(function ($query) use ($request) {
            $query->select('konsumen.id', 'konsumen.no_faktur')
                ->from(DB::raw($request->db.'konsumen'))
                ->where('konsumen.CompanyId', '=', trim($request->companyid));
                // ! agar saat simpan dengan faktur dengan id konsumen sama seperti sebelumnya tidak error saat edit
                if(!empty($request->id)){
                    $query = $query->where('konsumen.id', '!=', trim($request->id));
                }

            $query = $query->where('konsumen.no_faktur', '=', trim($request->nomor_faktur))
                ->where('konsumen.kd_lokasi', '=', trim($request->kd_lokasi));
        }, 'konsumen', 'faktur.no_faktur', '=', 'konsumen.no_faktur')
        ->addSelect('konsumen.id')
        ->where('faktur.CompanyId', trim($request->companyid))
        ->where('faktur.no_faktur', trim($request->nomor_faktur))
        ->first();

        if(empty($result)){
            return Response::responseWarning('Nomor Faktur tidak ditemukan');
        }

        if(!empty($result->id)){
            return Response::responseWarning('Nomor Faktur sudah digunakan konsumen lain');
        }

        DB::transaction(function () use ($request) {
            DB::insert('exec sp_konsumen_simpan ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
                trim($request->id),
                trim($request->divisi),
                trim(strtoupper($request->nomor_faktur)),
                trim(strtoupper($request->nama_pelanggan)),
                trim(strtoupper($request->nik)),
                trim(strtoupper($request->tempat_lahir)),
                trim(strtoupper($request->tanggal_lahir)),
                trim(strtoupper($request->alamat)),
                trim(strtoupper($request->telepon)),
                trim($request->email),
                trim(strtoupper($request->nopol)),
                trim(strtoupper($request->jenis_motor)),
                trim(strtoupper($request->merk_motor)),
                trim(strtoupper($request->tipe_motor)),
                trim(strtoupper($request->tahun_motor)),
                trim(strtoupper($request->keterangan)),
                trim(strtoupper($request->mengetahui)),
                trim(strtoupper($request->keterangan_mengetahui)),
                trim(strtoupper($request->companyid)),
                trim(strtoupper(date('Y-m-d=H:i:s').'#'. $request->user_id)),
                trim(strtoupper($request->kd_lokasi)),
            ]);

        });

        return Response::responseSuccess('Konsumen'. ($request->id ? ' berhasil diubah' : ' baru berhasil ditambahkan'));
    }

    public function konsumenDelete(Request $request)
    {
        try {
            $delete = DB::transaction(function () use ($request) {
                DB::insert('exec sp_konsumen_hapus ?,?,?,?', [
                    trim(strtoupper($request->id)),
                    trim(strtoupper($request->companyid)),
                    trim(strtoupper($request->kd_lokasi)),
                    trim(strtoupper($request->divisi)),
                ]);
            });

            if (is_null($delete)) {
                return Response::responseSuccess('success');
            } else {
                return Response::responseWarning('failed');
            }

        } catch (\Throwable $exception) {
            return Response::responseError($request->get('user_id'), 'API', route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
