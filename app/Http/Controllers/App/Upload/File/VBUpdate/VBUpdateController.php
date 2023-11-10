<?php

namespace app\Http\Controllers\App\Upload\File\VBUpdate;

use App\Helpers\App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class VBUpdateController extends Controller
{
    // buat metod upload file android
    function form(Request $request){
        $ResponApi = json_decode(Service::getVBVersion($request));
        if($ResponApi->status == 0){
            return redirect()->back()->with('error', 'Tejadi kesalahan, silahkan coba lagi');
        }
        return view('layouts.upload.file.vbupdate.form',[
                'title_menu'    => 'Upload File',
                'data'          => $ResponApi->data,
            ]);
    }
    function store(Request $request){
        $request->validate([
            'version' => 'required|string',
            'file'      => 'required',
        ],[
            'version.required'    => 'Version tidak boleh kosong',
            'version.string'      => 'Version harus berupa string',
            'file.required'    => 'File tidak boleh kosong'
        ]);

        try {
            $request->merge([
                'path_file' => 'https://suma.vb.honda.suma-honda.id/'.$request->file('file')->getClientOriginalName(),
            ]);

            $request->file('file')->move('../home/sumahond/suma.vb.honda', $request->file('file')->getClientOriginalName());

            $responseApi = json_decode(Service::SimpanVBVersion($request));
            if($responseApi->status == 0){
                return redirect()->back()->with('failed', 'Gagal upload file');
            }

            return redirect()->back()->with('success', 'Berhasil upload file');
        } catch (\Throwable $th) {
            return redirect()->back()->with('failed', 'Maaf, terjadi kesalahan');
        }
    }


    // destroy
    function destroy(Request $request){

        $validasi = Validator::make($request->all(), [
            'version'   => 'required|string'
        ],[
            'version.required'    => 'Version tidak boleh kosong',
        ]);

        if($validasi->fails()){
            return redirect()->back()->with('failed', $validasi->errors()->first());
        }

        $responseApi = json_decode(Service::deleteVBVersion($request));
        if($responseApi->status == 0){
            return redirect()->back()->with('failed', 'Gagal hapus file');
        }

        return redirect()->back()->with('success', 'Berhasil hapus file');
    }
}
