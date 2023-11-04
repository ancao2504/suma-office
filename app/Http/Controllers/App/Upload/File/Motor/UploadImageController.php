<?php

namespace app\Http\Controllers\App\Upload\File\Motor;

use App\Helpers\App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class UploadImageController extends Controller
{
    // buat metod upload file android
    function form(Request $request){
        $ResponApiMaster = json_decode(Service::getTypeMotor());
        $ResponApiDetail = json_decode(Service::getTypeMotorDetail());
        if($ResponApiMaster->status == 0 && $ResponApiDetail->status == 0){
            return redirect()->back()->with('error', 'Tejadi kesalahan, silahkan coba lagi');
        }
        return view('layouts.upload.file.motor.form',[
            'title_menu'    => 'Upload Image',
            'kd_master'          => $ResponApiMaster->data,
            'kd_detail'          => $ResponApiDetail->data
        ]);
    }

    function storeMaster(Request $request){
        $request->validate([
            'm_kd_master' => 'required|string',
            'm_nama' => 'required|string',
            'm_jenis' => 'required|string',
            'm_logo' => 'required|array',
            'm_logo.*' => 'mimes:png,jpg,jpeg',
            'm_gambar' => 'required|array',
            'm_gambar.*' => 'mimes:png,jpg,jpeg'
        ],[
            'm_kd_master.required'    => 'Kode master tidak boleh kosong',
            'm_kd_master.string'      => 'Kode master harus berupa string',
            'm_nama.required'         => 'Nama tidak boleh kosong',
            'm_nama.string'           => 'Nama harus berupa string',
            'm_jenis.required'        => 'Jenis tidak boleh kosong',
            'm_jenis.string'          => 'Jenis harus berupa string',
            'm_logo.required'         => 'Logo tidak boleh kosong',
            'm_logo.array'            => 'Logo tidak boleh kosong',
            'm_logo.*.mimes'          => 'Logo harus berupa gambar png, jpg, jpeg',
            'm_gambar.required'       => 'Gambar tidak boleh kosong',
            'm_gambar.array'          => 'Gambar tidak boleh kosong',
            'm_gambar.*.mimes'        => 'Gambar harus berupa gambar png, jpg, jpeg'
        ]);

        try {
            $path = (object)[
                'logo' => [],
                'gambar' => []
            ];
            foreach ($request->m_logo as $key => $value) {
                $path->logo[] = $value->getClientOriginalName();
            }
            foreach ($request->m_gambar as $key => $value) {
                $path->gambar[] = $value->getClientOriginalName();
            }

            foreach ($request->m_logo as $key => $value) {
                $value->move('images/upload/motor/master/logo', $value->getClientOriginalName());
            }
            foreach ($request->m_gambar as $key => $value) {
                $value->move('images/upload/motor/master', $value->getClientOriginalName());
            }

            $request->merge([
                'm_logo_path' => $path->logo,
                'm_gambar_path' => $path->gambar
            ]);

            $responseApi = json_decode(Service::uploadFileMotorMaster($request));

            if($responseApi->status == 0){
                foreach ($path->logo as $key => $value) {
                    if (File::exists(public_path('images/upload/motor/master/logo/'.$value))) {
                        File::delete(public_path('images/upload/motor/master/logo/'.$value));
                    }
                }
                foreach ($path->gambar as $key => $value) {
                    if (File::exists(public_path('images/upload/motor/master/'.$value))) {
                        File::delete(public_path('images/upload/motor/master/'.$value));
                    }
                }
                return redirect()->back()->with('error', 'Gagal upload file');
            }

            return redirect()->back()->with('success', 'Berhasil Menyimpan Data Master');

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal upload file');
        }


    }
    function storeDetail(Request $request){
        $validator = Validator::make($request->all(),[
            'd_kd_master' => 'required|string',
            'd_kd_detail' => 'required|array',
        ],[
            'd_kd_master.required'    => 'Kode master tidak boleh kosong',
            'd_kd_master.string'      => 'Kode master harus berupa string',
            'd_kd_detail.required'    => 'Kode detail tidak boleh kosong',
            'd_kd_detail.array'       => 'Kode detail tidak boleh kosong'
        ]);

        if (!$request->has('d_kd_detail')) {
            $validator->errors()->add('d_kd_detail', 'Kode detail tidak boleh kosong');
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $responseApi = json_decode(Service::uploadFileMotorDetail($request));

            if($responseApi->status == 0){
                return redirect()->back()->with('error', 'Gagal upload file');
            }

            return redirect()->back()->with('success', 'Berhasil Menyimpan Data Detail');

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal upload file');
        }
    }
    function storeDetailImage(Request $request){
        $request->validate([
            'di_kd_detail' => 'required|string',
            'di_gambar' => 'required|array',
            'di_gambar.*' => 'mimes:png,jpg,jpeg'
        ],[
            'di_kd_detail.required'    => 'Kode detail tidak boleh kosong',
            'di_kd_detail.string'      => 'Kode detail harus berupa string',
            'di_gambar.required'       => 'Gambar tidak boleh kosong',
            'di_gambar.array'          => 'Gambar tidak boleh kosong',
            'di_gambar.*.mimes'        => 'Gambar harus berupa gambar png, jpg, jpeg'
        ]);

        try {
            $path = (object)[
                'gambar' => []
            ];

            foreach ($request->di_gambar as $key => $value) {
                $path->gambar[] = $value->getClientOriginalName();
            }
            foreach ($request->di_gambar as $key => $value) {
                $value->move('images/upload/motor', $value->getClientOriginalName());
            }

            $request->merge([
                'di_gambar_path' => $path->gambar
            ]);

            $responseApi = json_decode(Service::uploadFileMotorDetailImage($request));
            if($responseApi->status == 0){
                foreach ($path->gambar as $key => $value) {
                    if (File::exists(public_path('images/upload/motor/'.$value))) {
                        File::delete(public_path('images/upload/motor/'.$value));
                    }
                }
                return redirect()->back()->with('error', 'Gagal upload file');
            }

            return redirect()->back()->with('success', 'Berhasil Menyimpan Data Detail');

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal upload file');
        }
    }
}
