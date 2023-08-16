<?php

namespace App\Http\Controllers\App\konsumen;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Helpers\App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\App\Option\OptionController;

class KonsumenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(empty(session()->get('app_user_company'))){
            $data = json_decode(service::LokasiAll($request));
            if($data->status == 0){
                return redirect()->back()->with('failed', 'Maaf, terjadi kesalahan coba beberapa saat lagi');
            }
            session()->put('app_user_company', $data->data);
        }
        $lokasi = session()->get('app_user_company');

        // ! cek lokasi,cabang agar sesuai yang di izinkan
        if(!empty($request->companyid) && !in_array($request->companyid, $lokasi->lokasi_valid->companyid)){
            return redirect()->back()->with('failed', 'Maaaf, Anda tidak memiliki akses ke company '.$request->companyid);
        }
        if(!empty($request->kd_lokasi) && !in_array($request->kd_lokasi, $lokasi->lokasi_valid->kd_lokasi)){
            return redirect()->back()->with('failed', 'Maaaf, Anda tidak memiliki akses ke lokasi '.$request->kd_lokasi);
        }

        // ! jika tidak ada lokasi,cabang yang dipilih maka ambil lokasi pertama yang diizinkan
        if(empty($request->companyid)){
            $request->merge(['companyid' => collect(collect($lokasi)->first()->lokasi)->first()->companyid]);
        }
        if(empty($request->kd_lokasi)){
            $request->merge(['kd_lokasi' => collect(collect($lokasi)->first()->lokasi)->first()->kd_lokasi[0]]);
        }
            
        $request->merge(['divisi' => (in_array($request->companyid,collect(collect($lokasi)->first()->lokasi)->pluck('companyid')->toArray()))?collect($lokasi)->first()->divisi:collect($lokasi)->skip(1)->take(1)->first()->divisi]);
        $responseApi = json_decode(Service::KonsumenDaftar($request));
        $statusApi = $responseApi->status;
        $messageApi =  $responseApi->message;

        if ($statusApi == 1) {
            return view(
                'layouts.konsumen.konsumen',
                [
                    'title_menu'    => 'Konsumen',
                    'data'          => json_decode($responseApi)->data,
                    'lokasi'        => $lokasi,
                ],
            );
        }
        return redirect()->back()->with('failed', $messageApi??'Maaf, terjadi kesalahan coba beberapa saat lagi');
    }

    public function create(Request $request)
    {
        $request->merge(['option' => 'select']);
        $responseApi_merekmotor = OptionController::merekmotor($request)->getData();
        $statusApi_merekmotor = $responseApi_merekmotor->status;
        $messageApi_merekmotor =  $responseApi_merekmotor->message;

        $responseApi_typemotor = json_decode(OptionController::typemotor($request));
        $statusApi_typemotor = $responseApi_typemotor->status;
        $messageApi_typemotor =  $responseApi_typemotor->message;

        if ($statusApi_merekmotor == 1 && $statusApi_typemotor == 1) {
            return view(
                'layouts.konsumen.create',
                [
                    'title_menu'        => 'Tambah Konsumen',
                    'merk_motor_list'   => $responseApi_merekmotor->data,
                    'type_motor'        => $responseApi_typemotor->data,
                ],
            );
        }

        return redirect()->back()->with('failed', 'Maaf, terjadi kesalahan coba beberapa saat lagi');
    }

    public function konsumenStore(Request $request){
        
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
            return Response()->json([
                'status' => 0,
                'message' => $validate->errors()->first(),
            ]);
        }

        $lokasi = session()->get('app_user_company');

        if(!empty($request->companyid) && !in_array($request->companyid, $lokasi->lokasi_valid->companyid)){
            return Response()->json([
                'status' => 2,
                'message' => 'Maaaf, Cabang tidak ditemukan, mohon cek kembali',
            ]);
        } 

        if(!empty($request->kd_lokasi) && !in_array($request->kd_lokasi, $lokasi->lokasi_valid->kd_lokasi)){
            return Response()->json([
                'status' => 2,
                'message' => 'Maaaf, Lokasi tidak ditemukan, mohon cek kembali',
            ]);
        }

        $responseApi = json_decode(service::KonsumenSimpan($request));
        $statusApi = $responseApi->status;
        $messageApi =  $responseApi->message;

        if($statusApi == 1){
            return Response()->json([
                'status'    => 1,
                'data'      => '',
                'message'   => $messageApi,
            ]);
        } else {
            return Response()->json([
                'status'    => 0,
                'data'      => '',
                'message'   => $messageApi,
            ]);
        }
    }

    public function konsumenEdit($id, Request $request){
        
        $request->merge(['option' => 'select']);
        $responseApi_merekmotor = OptionController::merekmotor($request)->getData();
        $statusApi_merekmotor = $responseApi_merekmotor->status;

        $responseApi_typemotor = json_decode(OptionController::typemotor($request));
        $statusApi_typemotor = $responseApi_typemotor->status;
        
        $lokasi = session()->get('app_user_company');

        // ! cek lokasi,cabang agar sesuai yang di izinkan
        if(!empty($request->companyid) && !in_array($request->companyid, $lokasi->lokasi_valid->companyid)){
            return redirect()->back()->with('failed', 'Maaaf, Anda tidak memiliki akses ke company '.$request->companyid);
        } 
        if(!empty($request->kd_lokasi) && !in_array($request->kd_lokasi, $lokasi->lokasi_valid->kd_lokasi)){
            return redirect()->back()->with('failed', 'Maaaf, Anda tidak memiliki akses ke lokasi '.$request->kd_lokasi);
        }
        // ! jika tidak ada lokasi,cabang yang dipilih maka ambil lokasi pertama yang diizinkan
        if(empty($request->companyid)){
            $request->merge(['companyid' => collect(collect($lokasi)->first()->lokasi)->first()->companyid]);
        }
        if(empty($request->kd_lokasi)){
            $request->merge(['kd_lokasi' => collect(collect($lokasi)->first()->lokasi)->first()->kd_lokasi[0]]);
        }
            
        $request->merge(['divisi' => (in_array($request->companyid,collect(collect($lokasi)->first()->lokasi)->pluck('companyid')->toArray()))?collect($lokasi)->first()->divisi:collect($lokasi)->skip(1)->take(1)->first()->divisi]);

        $request->merge(['id' => $id]);
        $request->merge(['option' => 'first']);
        $responseApi = Service::KonsumenDaftar($request);
        $statusApi = json_decode($responseApi)->status;

        if ($statusApi_merekmotor == 1 && $statusApi_typemotor == 1 && $statusApi == 1) {
            $data = json_decode($responseApi)->data;
            $data->divisi = $request->divisi;

            if ($data == null) {
                return redirect()->back()->with('failed', 'Maaf, data tidak ditemukan');
            }

            return view(
                'layouts.konsumen.create',
                [
                    'title_menu'        => 'Edit Konsumen',
                    'merk_motor_list'   => $responseApi_merekmotor->data,
                    'type_motor'        => $responseApi_typemotor->data,
                    'data'              => $data,
                ],
            );
        }

        return redirect()->back()->with('failed', 'Maaf, terjadi kesalahan coba beberapa saat lagi');
    }

    public function konsumenDelete(Request $request){
        
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
            return Response()->json([
                'status' => 0,
                'data' => '',
                'message' => $validate->errors()->first(),
            ]);
        }

        $lokasi = session()->get('app_user_company');

        if(!empty($request->companyid) && !in_array($request->companyid, $lokasi->lokasi_valid->companyid)){
            return Response()->json([
                'status' => 2,
                'message' => 'Maaaf, Cabang tidak valid, mohon cobalagi',
            ]);
        }

        if(!empty($request->kd_lokasi) && !in_array($request->kd_lokasi, $lokasi->lokasi_valid->kd_lokasi)){
            return Response()->json([
                'status' => 2,
                'message' => 'Maaaf, Lokasi tidak valid, mohon cobalagi',
            ]);
        }

        $responseApi = json_decode(service::KonsumenHapus($request));
        $statusApi = $responseApi->status;
        
        if($statusApi == 1){
            return Response()->json([
                'status'    => 1,
                'data'      => '',
                'message'   => 'Data konsumen dengan nomor faktur : <b>'. $request->no_faktur .'</b> berhasil dihapus',
            ]);
        } else {
            return Response()->json([
                'status'    => 0,
                'data'      => '',
                'message'   => 'Data konsumen dengan nomor faktur : <b>'. $request->no_faktur .'</b> gagal dihapus',
            ]);
        }
    }
}
