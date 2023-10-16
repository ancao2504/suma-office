<?php

namespace app\Http\Controllers\App\Upload\File\PriceList;

use App\Helpers\App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UploadFileController extends Controller
{
    // buat metod upload file android
    function form(Request $request){
        $ResponApi = json_decode(Service::getPriceList($request));
        if($ResponApi->status == 0){
            return redirect()->back()->with('error', 'Tejadi kesalahan, silahkan coba lagi');
        }
        return view('layouts.upload.file.pricelist.form',[
                'title_menu'    => 'Upload File',
                'data'          => $ResponApi->data,
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
            $request->merge([
                'ukuran_file' => round(($request->file('file')->getSize() / 1024), 2)
            ]);
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

    // destroy
    function destroy(Request $request){

        $validasi = Validator::make($request->all(), [
            'nama_file' => 'required|string',
            'tanggal'   => 'required'
        ],[
            'nama_file.required'    => 'Nama file tidak boleh kosong',
            'nama_file.string'      => 'Nama file harus berupa string',
            'tanggal.required'      => 'Maaaf, terjadi kesalahan, silahkan coba lagi'
        ]);

        if($validasi->fails()){
            return redirect()->back()->with('error', $validasi->errors()->first());
        }

        $responseApi = json_decode(Service::deleteFilePriceList($request));
        if($responseApi->status == 0){
            return redirect()->back()->with('error', 'Gagal hapus file');
        }

        $path = preg_replace('/.*\/images\//', 'images/', $responseApi->data);

        if(file_exists($path)){
            unlink($path);
        }

        return redirect()->back()->with('success', 'Berhasil hapus file');
    }
}
