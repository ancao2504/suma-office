<?php

namespace App\Http\Controllers\Api\Backend\Options;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

// maxsimal waktu eksekusi 5 menit
ini_set('max_execution_time', 300);
class ApiOptionsController extends Controller
{
    public function optionCompany(Request $request)
    {
        try {
            $sql = DB::table('company')->lock('with (nolock)')
                ->selectRaw("isnull(companyid, '') as companyid, isnull(ket, '') as keterangan")
                ->orderBy('companyid', 'asc');

            if(!empty($request->get('search')) && trim($request->get('search')) != '') {
                $sql->where('companyid', 'like', '%'.$request->get('search').'%')
                    ->orWhere('ket', 'like', '%'.$request->get('search').'%');
            }

            $sql = $sql->paginate($request->get('per_page') ?? 10);
            return Response::responseSuccess('success', $sql);
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

    public function optionClassProduk(Request $request)
    {
        try {
            $sql = DB::table('classprod')->lock('with (nolock)')
                ->selectRaw("isnull(kd_class, '') as kode_class, isnull(nama, '') as keterangan")
                ->orderBy('nama', 'asc')
                ->get();
            return Response::responseSuccess('success', $sql);
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

    public function optionDealer(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'companyid'     => 'required',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning('Anda Belum Login');
            }

            $sql = DB::table('dealer')->lock('with (nolock)')
                ->selectRaw('dealer.kd_dealer as kode_dealer, dealer.nm_dealer as nama_dealer')
                ->where('dealer.companyid', $request->get('companyid'))
                ->orderBy('dealer.kd_dealer', 'asc');

            if (!empty($request->get('search')) && $request->get('search') != '') {
                $sql->where('dealer.kd_dealer', 'like', $request->get('search').'%');
            }

            if (!empty($request->get('per_page'))) {
                $sql = $sql->paginate($request->get('per_page'));
            } else {
                $sql = $sql->paginate(10);
            }
            return Response::responseSuccess('success', $sql);
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

    public function optionDealerSalesman(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'kode_sales'    => 'required',
                'companyid'     => 'required',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning('Pilih data salesman terlebih dahulu');
            }

            $sql = DB::table('salesk_dtl')->lock('with (nolock)')
                ->selectRaw('salesk_dtl.kd_dealer as kode_dealer, dealer.nm_dealer as nama_dealer')
                ->leftJoin(DB::raw('dealer with (nolock)'), function ($join) {
                    $join->on('dealer.kd_dealer', '=', 'salesk_dtl.kd_dealer')
                        ->on('dealer.companyid', '=', 'salesk_dtl.companyid');
                })
                ->where('salesk_dtl.kd_sales', $request->get('kode_sales'))
                ->where('salesk_dtl.companyid', $request->get('companyid'))
                ->orderBy('salesk_dtl.kd_dealer', 'asc');

            if (!empty($request->get('search')) && $request->get('search') != '') {
                $sql->where('salesk_dtl.kd_dealer', 'like', $request->get('search').'%');
            }

            if (!empty($request->get('per_page'))) {
                $sql = $sql->paginate($request->get('per_page'));
            } else {
                $sql = $sql->paginate(10);
            }

            return Response::responseSuccess('success', $sql);
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

    public function optionGroupProduk(Request $request)
    {
        try {
            $sql = DB::table('produk')->lock('with (nolock)')
                ->selectRaw("isnull(kd_produk, '') as kode_produk, isnull(nama, '') as keterangan")
                ->orderBy('nama', 'asc');

            if (!empty($request->get('level')) && $request->get('level') != '') {
                if (strtoupper(trim($request->get('level'))) == 'HANDLE') {
                    $sql->where('level', 'AHM')->where('kd_mkr', 'G');
                } elseif (strtoupper(trim($request->get('level'))) == 'NON_HANDLE') {
                    $sql->where('level', 'MPM')->where('kd_mkr', 'G');
                } elseif (strtoupper(trim($request->get('level'))) == 'TUBE') {
                    $sql->where('level', 'AHM')->where('kd_mkr', 'I');
                } elseif (strtoupper(trim($request->get('level'))) == 'OLI') {
                    $sql->where('level', 'AHM')->where('kd_mkr', 'J');
                }
            }

            if (!empty($request->get('search')) && $request->get('search') != '') {
                $sql->where('produk.kd_produk', 'like', $request->get('search') . '%')
                    ->orWhere('produk.nama', 'like', $request->get('search') . '%');
            }

            if (!empty($request->get('per_page'))) {
                $sql = $sql->paginate($request->get('per_page'));
            } else {
                $sql = $sql->paginate(10);
            }

            return Response::responseSuccess('success', $sql);
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

    public function optionKabupaten(Request $request)
    {
        try {
            $sql = DB::table('kabupaten')->lock('with (nolock)')
                ->selectRaw("isnull(kd_kabupaten, '') as kode, isnull(nama_kabupaten, '') as keterangan")
                ->orderBy('kd_kabupaten', 'asc');

            if(!empty($request->get('search')) && trim($request->get('search')) != '') {
                $sql->where('kd_kabupaten', 'like', '%'.$request->get('search').'%')
                    ->orWhere('nama_kabupaten', 'like', '%'.$request->get('search').'%');
            }

            $sql = $sql->paginate($request->get('per_page') ?? 10);
            return Response::responseSuccess('success', $sql);
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

    public function optionPartNumber(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'companyid' => 'required',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning('Data company kosong');
            }

            $sql = DB::table('part')->lock('with (nolock)')
                ->selectRaw("isnull(part.kd_part, '') as part_number,
                            isnull(part.ket, '') as description,
                            isnull(produk.kd_produk, '') as produk,
                            isnull(part.het, 0) as het")
                ->leftJoin(DB::raw('sub with (nolock)'), function ($join) {
                    $join->on('sub.kd_sub', '=', 'part.kd_sub');
                })
                ->leftJoin(DB::raw('produk with (nolock)'), function ($join) {
                    $join->on('produk.kd_produk', '=', 'sub.kd_produk');
                })
                ->whereRaw("isnull(part.del_send, 0)=0")
                ->whereRaw("isnull(part.het, 0) > 0")
                ->where('part.companyid', $request->get('companyid'))
                ->orderBy('part.kd_part', 'asc');

            if(!empty($request->get('search') && trim($request->get('search')) != '')) {
                $sql->where('part.kd_part', 'like', $request->get('search').'%');
            }

            if (!empty($request->get('per_page'))) {
                $sql = $sql->paginate($request->get('per_page'));
            } else {
                $sql = $sql->paginate(10);
            }
            return Response::responseSuccess('success', $sql);
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

    public function optionRoleUser(Request $request)
    {
        try {
            $sql = DB::table('role')->lock('with (nolock)')
                ->selectRaw("isnull(role_id, '') as role_id, isnull(deskripsi_role, '') as keterangan")
                ->orderBy('role_id', 'asc')
                ->get();
            return Response::responseSuccess('success', $sql);
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

    public function optionSalesman(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'companyid' => 'required',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning('Anda Belum Login');
            }

            $sql = DB::table('salesman')->lock('with (nolock)')
                ->selectRaw('salesman.kd_sales as kode_sales, salesman.nm_sales as nama_sales')
                ->where('companyid', $request->get('companyid'))
                ->orderBy('kd_sales', 'asc');

            if (!empty($request->get('search')) && $request->get('search') != '') {
                $sql->where('kd_sales', 'like', $request->get('search') . '%');
            }

            if (!empty($request->get('per_page'))) {
                $sql = $sql->paginate($request->get('per_page'));
            } else {
                $sql = $sql->paginate(10);
            }

            return Response::responseSuccess('success', $sql);
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

    public function optionSupervisor(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'companyid' => 'required',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning('Anda Belum Login');
            }

            $sql = DB::table('superspv')->lock('with (nolock)')
                ->selectRaw("isnull(superspv.kd_spv, '') as kode_spv, isnull(superspv.nm_spv, '') as nama_spv")
                ->where('companyid', $request->get('companyid'))
                ->orderBy('superspv.kd_spv', 'asc');

            if (!empty($request->get('search')) && $request->get('search') != '') {
                $sql->where('kd_spv', 'like', $request->get('search') . '%');
                $sql->orWhere('nm_spv', 'like', $request->get('search') . '%');
            }

            if (!empty($request->get('per_page'))) {
                $sql = $sql->paginate($request->get('per_page'));
            } else {
                $sql = $sql->paginate(10);
            }

            return Response::responseSuccess('success', $sql);
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

    public function optionSubProduk(Request $request)
    {
        try {
            $sql = DB::table('sub')->lock('with (nolock)')
                ->selectRaw("isnull(kd_sub, '') as kode_sub, isnull(nama, '') as keterangan")
                ->orderBy('nama', 'asc')
                ->get();
            return Response::responseSuccess('success', $sql);
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

    public function optionLevelProduk(Request $request)
    {
        try {
            $sql = DB::table('produk')->lock('with (nolock)')
                ->selectRaw("isnull(level, '') as level")
                ->groupBy('level')
                ->orderBy('level', 'asc')
                ->get();
            return Response::responseSuccess('success', $sql);
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

    public function optionTypeMotor(Request $request)
    {
        try {
            $sql = DB::table(DB::raw('dbhonda.dbo.typemotor'))->lock('with (nolock)')
                ->selectRaw("isnull(typemkt, '') as kode, isnull(ket, '') as keterangan")
                ->orderBy('typemkt', 'asc');

            if (!empty($request->get('search')) && $request->get('search') != '') {
                $sql->where('typemkt', 'like', $request->get('search').'%')
                    ->orWhere('ket', 'like', $request->get('search').'%');
            }

            if (!empty($request->get('per_page'))) {
                $sql = $sql->paginate($request->get('per_page'));
            } else {
                $sql = $sql->paginate(10);
            }
            return Response::responseSuccess('success', $sql);
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

    public function optionUpdateHarga(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'kode_lokasi'   => 'required',
            ]);

            if($validate->fails()) {
                return Response::responseWarning("Pilih kode lokasi terlebih dahulu");
            }

            $sql = DB::table('selisih2')->lock('with (nolock)')
                    ->selectRaw("isnull(convert(varchar(10), selisih2.tgl, 105),'') as tanggal,
                                isnull(ltrim(rtrim(selisih2.usertime)), '') as kode,
                                isnull(ltrim(rtrim(selisih2.companyid)), '') as companyid")
                    ->where('selisih2.companyid',$request->get('companyid'));

            if(strtoupper(trim($request->get('kode_lokasi'))) == strtoupper(trim(config('constants.api.tokopedia.kode_lokasi')))) {
                $sql->whereRaw("isnull(selisih2.sts_ol, 0)=0");
            } elseif(strtoupper(trim($request->get('kode_lokasi'))) == strtoupper(trim(config('constants.api.shopee.kode_lokasi')))) {
                $sql->whereRaw("isnull(selisih2.sts_os, 0)=0");
            }

            $sql->groupByRaw("companyid, tgl, usertime")
                    ->orderByRaw("companyid asc, tgl desc, usertime desc");

            if(!empty($request->get('search')) && trim($request->get('search')) != '') {
                $sql->where(function ($query) use ($request){
                    $query->where('tgl', 'like', '%'.$request->get('search').'%')
                          ->orWhere('usertime', 'like', '%'.$request->get('search').'%');
                });
            }

            if (!empty($request->get('per_page'))) {
                $sql = $sql->paginate($request->get('per_page'));
            } else {
                $sql = $sql->paginate(10);
            }

            return Response::responseSuccess('success', $sql);
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

    public function optionEkspedisiOnline(Request $request)
    {
        try {
            $sql = DB::table('ekspedisi_online')->lock('with (nolock)')
                    ->selectRaw("isnull(rtrim(ekspedisi_online.kd_ekspedisi), '') as kode_ekspedisi,
                                isnull(ekspedisi_online.nm_ekspedisi, '') as nama_ekspedisi")
                    ->orderByRaw("ekspedisi_online.kd_ekspedisi asc")
                    ->get();

            return Response::responseSuccess('success', $sql);
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

    // ! Dari suma sby
    public function dataKonsumen(Request $request){
        try{
            // ! Validasi ---------------------------------------------
            $rules = array(
                'option' => 'in:first,select,page',
            );
            $messages = array(
                'option.in' => 'Maaf, pilihan option tidak tersedia',
            );

            if($request->option == 'first'){
                $rules += [
                    'nik' => 'required',
                ];
                $messages += [
                    'nik.required' => 'Maaf, NIK tidak ditemukan',
                ];
            }

            $validate = Validator::make($request->all(), $rules, $messages);

            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            if (!in_array($request->per_page, ['10', '50', '100'])) {
                $request->merge(['per_page' => '10']);
            }
            // ! End Validasi -----------------------------------------
            $data = DB::table(DB::raw('dbhonda.dbo.nik_konsumen'))
                    ->lock('with (nolock)')
                    ->select('nik_konsumen.nik','nik_konsumen.nama','nik_konsumen.tempat_lahir','nik_konsumen.tgl_lahir','nik_konsumen.telepon','nik_konsumen.alamat','nik_konsumen.email','konsumen.nopol')
                    ->leftJoinSub(function($query) use ($request){
                        $query->select('nik', 'nopol', 'tanggal')
                        ->from(DB::raw('dbhonda.dbo.konsumen'))
                        ->union(DB::table(DB::raw('dbsuma.dbo.konsumen'))->select('nik', 'nopol', 'tanggal'))
                        ->where('nik', $request->nik);
                    }, 'konsumen', function($join){
                        $join->on('nik_konsumen.nik', '=', 'konsumen.nik');
                    })

                    ->orderByDesc('konsumen.tanggal');

            if($request->option == 'first'){
                $data = $data->where('nik_konsumen.nik', 'like', '%'.$request->nik.'%')->first();
                // $data->nopol = DB::table('dbhonda.dbo.konsumen')
                //                 ->select('nopol','tanggal')
                //                 ->union(DB::table('dbsuma.dbo.konsumen')->select('nopol','tanggal'))
                //                 ->where('nik', $data->nik)
                //                 ->orderByDesc('tanggal')
                //                 ->first()->nopol;
            } else if($request->option == 'select'){
                $data = $data->get();
            } else if($request->option == 'page'){
                if(!empty($request->search)){
                    $data = $data
                    ->orWhere('nik_konsumen.nik', 'like', '%'.$request->search.'%')
                    ->orWhere('nik_konsumen.nama', 'like', '%'.$request->search.'%')
                    ->orWhere('nik_konsumen.telepon', 'like', '%'.$request->search.'%')
                    ->orWhere('nik_konsumen.alamat', 'like', '%'.$request->search.'%')
                    ->orWhere('nik_konsumen.email', 'like', '%'.$request->search.'%')
                    ->orWhere('konsumen.nopol', 'like', '%'.$request->search.'%');
                }
                $data = $data->paginate($request->per_page);
            }

            return Response::responseSuccess('success', $data);
        } catch(\Exception $e){
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $e->getMessage(),
                $request->get('companyid')
            );
        }
    }
    public function dataSupplier(Request $request){
        try{
            // ! Validasi ---------------------------------------------
            $rules = array(
                'option' => 'in:first,select',
            );
            $messages = array(
                'option.in' => 'Maaf, pilihan option tidak tersedia',
            );

            if($request->option == 'first'){
                $rules['kd_supplier'] = 'required';
                $messages['kd_supplier.required'] = 'Maaf, kode supplier tidak boleh kosong';
            }

            $validate = Validator::make($request->all(), $rules, $messages);

            if ($validate->fails()) {

                return Response::responseWarning($validate->errors()->first());
            }

            if (!in_array($request->per_page, ['10', '50', '100'])) {
                $request->merge(['per_page' => '10']);
            }
            // ! End Validasi -----------------------------------------

            $data = DB::table('supplier')
            ->select('kd_supp as kd_supplier', 'nama as nm_supplier')
            ->where('CompanyId', $request->companyid);

            if(!empty($request->kd_sipplier)) {
                $data = $data->where('kd_supp', $request->kd_supplier);
            }

            if($request->option == 'first'){
                $data = $data->first();
            } else if($request->option == 'select'){
                $data = $data->orderBy('kd_supp', 'asc')
                ->get();
            } else if($request->option == 'page'){
                $data = $data->orderBy('kd_supp', 'asc')
                ->paginate($request->per_page);
            }

            return Response::responseSuccess('success', $data);
        } catch(\Exception $e){
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $e->getMessage(),
                $request->get('companyid')
            );
        }
    }
    public function dataSalesman(Request $request){
        try{
            // ! Validasi ---------------------------------------------
            $rules = array(
                'option' => 'in:first,select,page',
            );
            $messages = array(
                'option.in' => 'Maaf, pilihan option tidak tersedia',
            );

            if($request->option == 'first'){
                $rules['kd_sales'] = 'required';
                $messages['kd_sales.required'] = 'Maaf, kd_sales tidak boleh kosong';
            }

            $validate = Validator::make($request->all(), $rules, $messages);

            if ($validate->fails()) {

                return Response::responseWarning($validate->errors()->first());
            }

            if (!in_array($request->per_page, ['10', '50', '100'])) {
                $request->merge(['per_page' => '10']);
            }
            // ! End Validasi -----------------------------------------

            $data = DB::table('salesman')->where('CompanyId', $request->companyid);

            if(!empty($request->kd_spv)) {
                $data = $data->where('spv', $request->kd_spv);
            }

            if(!empty($request->kd_sales)) {
                $data = $data->where('kd_sales', $request->kd_sales);
            }

            if($request->option == 'first'){
                $data = $data->first();
            } else if($request->option == 'select'){
                $data = $data->orderBy('kd_sales', 'asc')
                ->get();
            } else if($request->option == 'page'){
                $data = $data->orderBy('kd_sales', 'asc')
                ->paginate($request->per_page);
            }

            return Response::responseSuccess('success', $data);
        } catch(\Exception $e){
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $e->getMessage(),
                $request->get('companyid')
            );
        }
    }
    public function dataUkuranRing(Request $request){
        try{

            $data = DB::table('dbsuma.dbo.part')->lock('with (nolock)')->select('jenis')->where('jenis', '!=', '')->orderBy('jenis', 'asc')->distinct()->get();

            return Response::responseSuccess('success', $data);
        } catch(\Exception $e){
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $e->getMessage(),
                $request->get('companyid')
            );
        }
    }

    public function dataDealer(Request $request){
        try {
            // ! validasi ---------------------------------------------------------------
            $rules = array(
                'option' => 'in:first,select,page',
            );
            $messages = array(
                'option.in' => 'Maaf, pilihan option tidak tersedia',
            );

            if($request->option == 'first'){
                $rules['kd_dealer'] = 'required';
                $messages['kd_dealer.required'] = 'Maaf, kode Dealer tidak boleh kosong';
            }

            $validate = Validator::make($request->all(), $rules, $messages);

            if ($validate->fails()) {

                return Response::responseWarning($validate->errors()->first());
            }

            if (!in_array($request->per_page, ['10', '50', '100'])) {
                $request->merge(['per_page' => '10']);
            }
            // ! end validasi ----------------------------------------------------------

            $data = DB::table('dealer')
                ->lock('with (nolock)')->select('kd_dealer', 'nm_dealer', 'alamat1', 'kotasj')
                ->where('kd_sales', $request->kd_sales)
                ->where('CompanyId', $request->companyid);

            if (!empty($request->kd_sales)) {
                $data = $data->where('kd_dealer',  'LIKE', '%' . $request->kd_dealer . '%');
            }

            if (!empty($request->kd_dealer)) {
                $data = $data->where('kd_dealer',  'LIKE', '%' . $request->kd_dealer . '%');
            }
            if ($request->option == 'first') {
                $data = $data->first();
            } else if ($request->option == 'select') {
                $data = $data->get();
            } else if ($request->option == 'page') {
                $data = $data->paginate($request->per_page);
            }
            return Response::responseSuccess('success', $data);
        } catch (\Exception $e) {
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $e->getMessage(),
                $request->get('companyid')
            );
        }
    }

    public function dataFakturKonsumen(Request $request) {
        try {
            // ! Validasi ---------------------------------------------
            $validate = Validator::make($request->all(), [
                'no_faktur' => 'min:5',
            ],[
                'no_faktur.min' => 'No Faktur minimal 5 karakter',
            ]);

            if ($validate->fails()) {

                return Response::responseWarning($validate->errors()->first());
            }

            if (!in_array($request->per_page, ['10', '50', '100', '500'])) {
                $request->merge(['per_page' => '10']);
            }

            if($request->divisi == 'fdr'){
                $database = 'dbsuma.dbo.';
            } else {
                $database = 'dbhonda.dbo.';
            }
            // ! End Validasi -----------------------------------------

            $data = DB::table(DB::raw($database . 'faktur'))
                ->lock('with (nolock)')->select('faktur.no_faktur', 'faktur.tgl_faktur', 'faktur.disc2','faktur.total')
                ->where('faktur.CompanyId', $request->companyid)
                ->leftjoinSub(function($query) use ($request, $database) {
                    $query->select('konsumen.id','konsumen.companyid','konsumen.no_faktur','konsumen.kd_lokasi')
                    ->from(DB::raw($database . 'konsumen WITH (NOLOCK)'))
                    ->where('konsumen.CompanyId', $request->companyid);
                    // !agar saat edit mengecualikan faktur ini agar tidak terkena faktur sudah di gunakan
                    if (!empty($request->id_konsumen)) {
                        $query = $query->where('konsumen.id', '!=', $request->id_konsumen);
                    }
                    if (!empty($request->no_faktur)) {
                    $query = $query->where('konsumen.no_faktur', 'LIKE', '%'.$request->no_faktur . '%');
                    }
                    if (!empty($request->kd_lokasi)) {
                    $query = $query->where('konsumen.kd_lokasi', $request->kd_lokasi);
                    }
                }, 'konsumen', function ($join) {
                $join->on('faktur.no_faktur', '=', 'konsumen.no_faktur');
                $join->on('faktur.CompanyId', '=', 'konsumen.companyid');
            })->addSelect('konsumen.id');

            if (!empty($request->kd_sales)) {
                $data = $data->where('faktur.kd_sales', $request->kd_sales);
            }
            if (!empty($request->kd_dealer)) {
                $data = $data->where('faktur.kd_dealer', $request->kd_dealer);
            }
            if (!empty($request->no_faktur)) {
                $data = $data->where('faktur.no_faktur', 'LIKE', '%'.$request->no_faktur . '%');
            }

            if($request->option == 'first'){
                $data = $data
                ->first();
            } else if($request->option == 'page'){
                $data = $data
                ->paginate($request->per_page);
            }

            return Response::responseSuccess('success' , $data);
        } catch (\Exception $e) {
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $e->getMessage(),
                $request->get('companyid')
            );
        }
    }

    public function dataPart(Request $request){
        try{
            // ! ---------------------------------------------
            // ! Validasi
            $rules = [];
            $messages = [];
            if (!empty($request->no_faktur)) {
                $rules += [
                    'no_faktur' => 'min:5',
                ];
                $messages += [
                    'no_faktur.min' => 'No Faktur minimal 5 karakter',
                ];
            }

            $validate = Validator::make($request->all(), $rules,$messages);
            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            if(!in_array($request->per_page, ['10', '50', '100'])){
                $request->merge(['per_page' => '10']);
            }
            // ! End Validasi
            // ! -----------------------------------------

            $data = DB::table(function ($query) use ($request) {
                $query->select('part.kd_part', 'part.ket as nm_part', 'part.het', 'part.hrg_pokok', 'part.kd_sub','part.CompanyId','part.kanvas','part.in_transit','part.min_gudang','part.min_htl')
                    ->from('part')
                    ->where('part.CompanyId', $request->companyid)
                    ->whereRaw("isnull(part.del_send, 0)=0")
                    ->whereRaw("isnull(part.het, 0) > 0");
                if (!empty($request->kd_part)) {
                    $query = $query->where('part.kd_part', 'LIKE', '%'.$request->kd_part . '%');
                }
                return $query;
            }, 'part')
            ->select('part.kd_part', 'part.nm_part', 'part.het', 'part.hrg_pokok', 'part.kd_sub');

            // ! ------------------------------------
            // ! Jika di join dengan faktur
            if (!empty($request->no_faktur)) {
                $data = $data->JoinSub(function ($query) use ($request) {
                    //! sub join dengan faktur
                    $query->select('fakt_dtl.no_faktur','fakt_dtl.kd_part', 'fakt_dtl.jml_jual', 'fakt_dtl.harga', 'fakt_dtl.disc1','faktur.kd_sales','faktur.CompanyId')
                    ->from('faktur')
                    ->joinSub(function ($query) use ($request) {
                        $query->select('fakt_dtl.no_faktur','fakt_dtl.kd_part', 'fakt_dtl.jml_jual', 'fakt_dtl.harga', 'fakt_dtl.disc1','fakt_dtl.CompanyId')
                            ->from('fakt_dtl')
                            ->where('fakt_dtl.CompanyId', $request->companyid)
                            ->where('fakt_dtl.no_faktur', 'LIKE', '%'.$request->no_faktur . '%');
                    }, 'fakt_dtl', function ($join) {
                        $join->on('faktur.no_faktur', '=', 'fakt_dtl.no_faktur')
                            ->on('faktur.CompanyId', '=', 'fakt_dtl.CompanyId');
                    })
                    ->where('faktur.CompanyId', $request->companyid);
                    if (!empty($request->kd_sales)) {
                        $query = $query->where('faktur.kd_sales', $request->kd_sales);
                    }
                    $query = $query->where('faktur.no_faktur', 'LIKE', '%'.$request->no_faktur . '%');
                }, 'faktur', function ($join) {
                    $join->on('part.kd_part', '=', 'faktur.kd_part')
                        ->on('part.CompanyId', '=', 'faktur.CompanyId');
                });

                //! select tambahan
                $data = $data->selectRaw('faktur.no_faktur, faktur.jml_jual, faktur.harga, faktur.disc1, faktur.kd_sales');
            }

            if(!empty($request->no_retur)){
                $data = $data->JoinSub(function ($query) use ($request) {
                    $query->select('rtoko_dtl.no_retur','rtoko_dtl.kd_part', 'rtoko_dtl.jumlah','rtoko_dtl.no_klaim', 'rtoko_dtl.ket' ,'rtoko_dtl.CompanyId')
                    ->from('rtoko_dtl')
                    ->where('rtoko_dtl.no_retur', 'LIKE', '%'.$request->no_retur . '%')
                    ->where('rtoko_dtl.CompanyId', $request->companyid)
                    ->whereRaw("isnull(rtoko_dtl.status, 0)=0");
                }, 'rtoko_dtl', function ($join) {
                    $join->on('part.kd_part', '=', 'rtoko_dtl.kd_part')
                        ->on('part.CompanyId', '=', 'rtoko_dtl.CompanyId');
                });

                //! ganti select default
                $data = $data->select('part.kd_part', 'part.nm_part','rtoko_dtl.jumlah','rtoko_dtl.ket')
                ->groupBy('part.kd_part', 'part.nm_part','rtoko_dtl.jumlah','rtoko_dtl.ket');
            }

            // ! ------------------------------------
            // ! Jika data first atau get (paginate)
            // ! ------------------------------------
            if(!empty($request->option[1]) && $request->option[1] == 'with_stock'){
                $data = $data->select('part.kd_part','part.nm_part')
                ->selectRaw('(ISNULL(tbStLokasiRak.Stock,0) - (ISNULL(stlokasi.min,0) + ISNULL(stlokasi.in_transit,0) + ISNULL(part.kanvas,0) + ISNULL(part.in_transit,0))) as stock')
                ->JoinSub(function ($query) use ($request) {
                    $query->select('*')
                        ->from('company')
                        ->where('company.CompanyId', $request->companyid);
                }, 'company', function ($join) {
                    $join->on('part.CompanyId', '=', 'company.CompanyId');
                })
                ->leftJoinSub(function($query) use ($request){
                    $query->select('stlokasi.kd_part','stlokasi.kd_lokasi','stlokasi.CompanyId','stlokasi.min','stlokasi.in_transit')
                    ->from('stlokasi')
                    ->where('stlokasi.CompanyId', $request->companyid);
                }, 'stlokasi', function($join){
                    $join->on('part.kd_part', '=', 'stlokasi.kd_part')
                    ->on('company.kd_lokasi', '=', 'stlokasi.kd_lokasi')
                    ->on('part.CompanyId', '=', 'stlokasi.CompanyId');
                })
                ->leftJoinSUb(function ($query) use ($request){
                    $query->select('tbStLokasiRak.Kd_part','tbStLokasiRak.Kd_Lokasi','tbStLokasiRak.Kd_Rak','tbStLokasiRak.CompanyId','tbStLokasiRak.Stock')
                    ->from('tbStLokasiRak')
                    ->where('tbStLokasiRak.CompanyId',$request->companyid);
                }, 'tbStLokasiRak', function($join){
                    $join->on('part.kd_part', '=', 'tbStLokasiRak.Kd_part')
                    ->on('stlokasi.kd_lokasi', '=', 'tbStLokasiRak.Kd_Lokasi')
                    ->on('company.kd_rak', '=', 'tbStLokasiRak.Kd_Rak')
                    ->on('part.CompanyId', '=', 'tbStLokasiRak.CompanyId');
                });
            }

            if($request->option[0] == 'first'){
                $data = $data->first();
            }elseif($request->option[0] == 'page'){
                $data = $data->paginate($request->per_page);
            }

            if(!empty($request->no_retur) && $request->option[0] == 'page'){
                $dataNoProduk = collect(
                    DB::table(function ($query) use ($request) {
                        $query->select('rtoko_dtl.no_klaim','rtoko_dtl.CompanyId')
                        ->from('rtoko_dtl')
                        ->where('rtoko_dtl.no_retur', 'LIKE', '%'.$request->no_retur . '%')
                        ->where('rtoko_dtl.CompanyId', $request->companyid)
                        ->whereRaw("isnull(rtoko_dtl.status, 0)=0")
                        ->groupBy('rtoko_dtl.no_klaim','rtoko_dtl.CompanyId');
                    }, 'rtoko_dtl')
                    ->join('klaim_dtl', function($join){
                        $join->on('rtoko_dtl.no_klaim', '=', 'klaim_dtl.no_dokumen')
                        ->on('rtoko_dtl.CompanyId', '=', 'klaim_dtl.CompanyId');
                    })
                    ->where('klaim_dtl.sts_klaim',  '1')
                    ->select('klaim_dtl.kd_part','klaim_dtl.no_produksi')
                    ->get()
                )->groupBy('kd_part');

                foreach($data->items() as $key => $value){
                    if(!empty($dataNoProduk[$value->kd_part])){
                        $data->items()[$key]->no_produksi = $dataNoProduk[$value->kd_part]->pluck('no_produksi');
                    } else {
                        $data->items()[$key]->no_produksi = [];
                    }
                }
            }

            return Response::responseSuccess('success', $data);
        } catch(\Exception $e){
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $e->getMessage(),
                $request->get('companyid')
            );
        }
    }

    public function dataRetur(Request $request){
        try{
            // ! ---------------------------------------------
            // ! Validasi
            $rules = [];
            $messages = [];
            if (!empty($request->no_retur)) {
                $rules += [
                    'no_retur' => 'min:2',
                ];
                $messages += [
                    'no_retur.min' => 'No Klaim minimal 2 karakter',
                ];
            }

            $validate = Validator::make($request->all(), $rules,$messages);
            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            if(!in_array($request->per_page, ['10', '50', '100'])){
                $request->merge(['per_page' => '10']);
            }
            // ! End Validasi
            // ! -----------------------------------------

            $data = DB::table(function ($query) use ($request) {
                $query->select('*')
                    ->from('rtoko')
                    ->joinSub(function ($query) use ($request) {
                        $query->select('rtoko_dtl.no_retur as no_dokumen','rtoko_dtl.status','rtoko_dtl.kd_part')
                            ->from('rtoko_dtl')
                            ->whereRaw("isnull(rtoko_dtl.status, 0)=0")
                            ->where('rtoko_dtl.CompanyId', $request->companyid)
                            ->groupBy('rtoko_dtl.no_retur','rtoko_dtl.status','rtoko_dtl.kd_part');
                    }, 'rtoko_dtl', function ($join) {
                        $join->on('rtoko.no_retur', '=', 'rtoko_dtl.no_dokumen');
                    })
                    ->where('rtoko.CompanyId', $request->companyid);
                    if (!empty($request->no_retur)) {
                        $query = $query->where(function($query) use ($request){
                            $query->where('rtoko.no_retur', 'LIKE', '%'.$request->no_retur . '%')
                            ->orWhere('rtoko.kd_dealer', 'LIKE', '%'.$request->no_retur . '%')
                            ->orWhere('rtoko.tanggal', 'LIKE', '%'.$request->no_retur . '%')
                            ->orWhere('rtoko_dtl.kd_part', 'LIKE', '%'.$request->no_retur . '%');
                        });
                    }
                    return $query;
            }, 'retur')
            ->select('retur.no_retur','retur.tanggal','kd_dealer');

            // ! ------------------------------------
            // ! Jika data first atau get (paginate)
            // ! ------------------------------------
            if($request->option[0] == 'first'){
                $data = $data->orderBy('retur.tanggal', 'desc')->first();
            }elseif($request->option[0] == 'page'){
                $data = $data
                ->orderBy('retur.tanggal', 'desc')
                ->orderBy('retur.no_retur', 'desc')
                ->groupBy('retur.no_retur','retur.tanggal','retur.kd_dealer')
                ->paginate($request->per_page);


                $detail = collect(DB::table(function ($query) use ($request, $data) {
                    $query->select('rtoko_dtl.no_retur','rtoko_dtl.kd_part')
                    ->from('rtoko_dtl')
                    ->where('rtoko_dtl.CompanyId', $request->companyid)
                    ->whereRaw("isnull(rtoko_dtl.status, 0)=0")
                    ->whereIn('rtoko_dtl.no_retur', collect($data->items())->pluck('no_retur')->toArray());
                }, 'rtoko_dtl')
                ->select('rtoko_dtl.no_retur','rtoko_dtl.kd_part')
                ->get())->groupBy('no_retur');

                foreach($data->items() as $key => $value){
                    $data->items()[$key]->detail = $detail[$value->no_retur]->pluck('kd_part');
                }
            }

            return Response::responseSuccess('success', $data);
        } catch(\Exception $e){
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $e->getMessage(),
                $request->get('companyid')
            );
        }
    }

    public function dataProduk(Request $request){
        try {
            $data = DB::table('produk')->lock('with (nolock)')->select('kd_produk', 'nama as nm_produk');
            if(!empty($request->kd_produk)) {
                $data = $data->where('kd_produk', $request->kd_produk);
            }

            if($request->option == 'first'){
                $data = $data->first();
            } else if($request->option == 'select'){
                $data = $data->orderBy('kd_produk', 'asc')
                ->get();
            } else if($request->option == 'page'){
                $data = $data->paginate($request->per_page);
            }

            return Response::responseSuccess('success', $data);
        } catch (\Exception $e) {
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $e->getMessage(),
                $request->get('companyid')
            );
        }
    }

    public function dataMerekmotor(Request $request){
        try{
            // ! Validasi ---------------------------------------------
            $rules = array(
                'option' => 'in:first,select',
            );
            $messages = array(
                'option.in' => 'Maaf, pilihan option tidak tersedia',
            );

            $validate = Validator::make($request->all(), $rules, $messages);

            if ($validate->fails()) {

                return Response::responseWarning($validate->errors()->first());
            }
            // ! End Validasi -----------------------------------------

            $data = DB::table(DB::raw('dbsuma.dbo.konsumen_motor'))->lock('with (nolock)')->select('MerkMotor')->distinct();

            if($request->option == 'first'){
                $data = $data->first();
            } else if($request->option == 'select'){
                $data = $data
                ->get();
            }

            return Response::responseSuccess('success', $data);
        } catch(\Exception $e){
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $e->getMessage(),
                $request->get('companyid')
            );
        }
    }

    public function dataTypemotor(Request $request){
        try{
            // ! Validasi ---------------------------------------------
            $rules = array(
                'option' => 'in:first,select',
            );
            $messages = array(
                'option.in' => 'Maaf, pilihan option tidak tersedia',
            );

            $validate = Validator::make($request->all(), $rules, $messages);

            if ($validate->fails()) {

                return Response::responseWarning($validate->errors()->first());
            }
            // ! End Validasi -----------------------------------------

            $data = DB::table(DB::raw('dbsuma.dbo.konsumen_motor'))
            ->lock('with (nolock)')
            ->select('TypeMotor', 'MerkMotor')
            ->distinct();

            if($request->option == 'first'){
                $data = $data->first();
            } else if($request->option == 'select'){
                $data = collect($data->get())->groupBy('MerkMotor');
            }

            return Response::responseSuccess('success', $data);
        } catch(\Exception $e){
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $e->getMessage(),
                $request->get('companyid')
            );
        }
    }

    public function dataMejaPackingOnline(Request $request){
        try{
            $data = DB::table('dbhonda.dbo.lokasi_pack')->select('kd_lokpack', 'keterangan')->get();

            return Response::responseSuccess('success', $data);
        } catch(\Exception $e){
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $e->getMessage(),
                $request->get('companyid')
            );
        }
    }

    public function dataPackerPackingOnline(Request $request){
        try{
            $data = DB::table('dbhonda.dbo.wh_time')
            ->select('wh_time.kd_pack','karyawan.nama as nm_pack')
            ->joinSub(function ($query){
                $query->select('kd_dealer', 'nm_dealer')
                    ->from('dbhonda.dbo.dealer')
                    ->where('kd_area', 'i8');
            }, 'dealer', function ($join) {
                $join->on('wh_time.kd_dealer', '=', 'dealer.kd_dealer');
            })
            ->leftjoin('karyawan' , function($join){
                $join->on('karyawan.kode', '=', 'wh_time.kd_pack')
                ->on('karyawan.CompanyId', '=', 'wh_time.CompanyId');
            })
            ->whereNotNull('wh_time.kd_pack')
            ->where('wh_time.kd_pack', '!=', '')
            ->where('karyawan.kode', '!=', '')
            ->distinct()
            ->orderBy('nama', 'asc')
            ->get();

            return Response::responseSuccess('success', $data);
        } catch(\Exception $e){
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $e->getMessage(),
                $request->get('companyid')
            );
        }
    }

    public function dataWH(Request $request){
        try{
            if (!in_array($request->per_page, [10, 50, 100, 500])) {
                $request->merge(['per_page' => 10]);
            }

            $data = DB::table('wh_time')
            ->lock('with (nolock)')
            ->select(
                'wh_time.no_dok',
                'dealer.nm_dealer',
                'faktur.ket',
                'faktur.kd_ekspedisi',
                'wh_time.tanggal3 as tgl_start',
                'wh_time.jam3 as jam_start',
                'wh_time.tanggal4 as tgl_finish',
                'wh_time.jam4 as jam_finish',
                DB::raw('
                CASE
                    WHEN tanggal3 IS NULL AND jam3 IS NULL THEN \'<span class="badge badge-light-danger">Belum diproses</span>\'
                    WHEN tanggal3 IS NOT NULL AND jam3 IS NOT NULL AND tanggal4 IS NULL AND jam4 IS NULL THEN \'<span class="badge badge-light-warning">Proses</span>\'
                    WHEN tanggal3 IS NOT NULL AND jam3 IS NOT NULL AND tanggal4 IS NOT NULL AND jam4 IS NOT NULL THEN \'<span class="badge badge-light-success">Selesai</span>\'
                END AS status
            ')
            )
            ->joinSub(function ($query) use ($request) {
                $query->select('no_dok', 'no_faktur')
                    ->from('wh_dtl')
                    ->where('CompanyId', $request->companyid)
                    ->groupBy('no_dok', 'no_faktur');
            }, 'wh_dtl', function ($join) {
                $join->on('wh_time.no_dok', '=', 'wh_dtl.no_dok');
            })
            ->joinSub(function ($query) use ($request) {
                $query->select('kd_dealer', 'nm_dealer')
                    ->from('dealer')
                    ->where('kd_area', 'i8')
                    ->where('CompanyId', $request->companyid);
            }, 'dealer', function ($join) {
                $join->on('wh_time.kd_dealer', '=', 'dealer.kd_dealer');
            })
            ->leftJoinSub(function ($query) use ($request) {
                $query->select('no_faktur', 'ket', 'kd_ekspedisi')
                ->from('faktur')
                ->where('CompanyId', $request->companyid);
            }, 'faktur', function ($join) {
                $join->on('wh_dtl.no_faktur', '=', 'faktur.no_faktur');
            });

            if (!empty($request->no_wh)) {
                $data = $data->where('wh_time.no_dok', 'LIKE', '%' . $request->no_wh . '%');
            }

            $data = $data->where('wh_time.CompanyId', $request->companyid);

            if ($request->option == 'first') {
                $data = $data->first();
            } elseif ($request->option == 'page') {
                $data = $data
                ->where(function($query){
                    $query->whereNull('wh_time.tanggal3')->orWhereDate('wh_time.tanggal3', date('Y-m-d'));
                })
                ->where(function($query){
                    $query->whereNull('wh_time.jam3')->orWhereTime('wh_time.jam3', '<=', date('H:i:s'));
                })
                ->orderBy('wh_time.tanggal4', 'asc')
                ->orderBy('wh_time.tanggal3', 'desc')
                ->groupBy('wh_time.no_dok', 'dealer.nm_dealer', 'faktur.ket', 'faktur.kd_ekspedisi','wh_time.tanggal3','wh_time.jam3','wh_time.tanggal4','wh_time.jam4')
                ->paginate($request->per_page);
            }

            return Response::responseSuccess('success', $data);
        } catch(\Exception $e){
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $e->getMessage(),
                $request->get('companyid')
            );
        }
    }

    public function dataCabang(Request $request){
        try{
            $data = DB::table('cabang')
            ->select('kd_cabang','nm_cabang')
            ->where('CompanyId', $request->companyid)
            ->where('inisial',0)
            ->get();

            return Response::responseSuccess('success', $data);
        } catch(\Exception $e){
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $e->getMessage(),
                $request->get('companyid')
            );
        }
    }
    // !
}
