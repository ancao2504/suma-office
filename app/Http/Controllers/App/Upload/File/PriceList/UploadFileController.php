<?php

namespace app\Http\Controllers\App\Upload\File\PriceList;

use App\Helpers\App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class UploadFileController extends Controller
{
    // buat metod upload file android
    function form(){
        return view('layouts.upload.file.pricelist.form',[
                'title_menu'    => 'Upload File'
            ]);
    }
    function store(Request $request){
        $request->validate([
            'nama_file' => 'required|string',
            'file'      => 'required|mimes:xls,xlsx',
            'ket_file'  => 'required|string'
        ],[
            'nama_file.required'    => 'Nama file tidak boleh kosong',
            'nama_file.string'      => 'Nama file harus berupa string',
            'file.required'         => 'File tidak boleh kosong',
            'file.mimes'            => 'File harus berupa xls atau xlsx',
            'ket_file.required'     => 'Keterangan file tidak boleh kosong',
            'ket_file.string'       => 'Keterangan file harus berupa string'
        ]);

        $extensi = $request->file('file')->getClientOriginalExtension();
        try {
            $request->file('file')->move('images/upload', $request->nama_file.'.'.$extensi);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal upload file');
        }
        
        $request->merge([
            'path_file' => asset('images/upload/'.$request->nama_file.'.'.$extensi)
        ]);

        $responseApi = json_decode(Service::uploadFilePriceList($request));
        if($responseApi->status == 0){
            return redirect()->back()->with('error', 'Gagal upload file');
        }

        return redirect()->back()->with('success', 'Berhasil upload file');
    }
}
