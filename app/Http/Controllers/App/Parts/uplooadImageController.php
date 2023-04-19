<?php

namespace App\Http\Controllers\app\Parts;

use App\Helpers\ApiService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class uplooadImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $responApi = ApiService::PartImageDaftar($request->get('search')??'', $request->get('page')??1);

        $view = view('layouts.parts.uploadimage.uploadimage',[
            'title_menu'    => 'Upload Gambar part',
            'search'       => $request->get('search')??'',
        ]);
        $dataApi = json_decode($responApi)->data;
        if (json_decode($responApi)->status == 1) {
            if ($request->ajax()) {
                if($dataApi->current_page <= $dataApi->to){
                    return response()->json([
                        'status'    => 1,
                        'message'   => 'success',
                        'data' => Str::between($view->with('dataApi', json_decode($responApi))->render(), '<!--start::container-->', '<!--end::container-->') 
                    ]);
                }

                return response()->json([
                    'status'    => 2,
                    'message'   => 'success',
                    'data'      => $dataApi->current_page
                ]);
                
            } else {
                return $view->with('dataApi', json_decode($responApi));
            }
        } else {
            if ($request->ajax()) {
                return response()->json([
                    'status'    => 0,
                    'message'   => json_decode($responApi)->message,
                    'data'      => ''
                ]);
            } else {
                return $view->with('dataApi', json_decode($responApi));
            }
        }
    }

    public function store(Request $request)
    {
        foreach ($request->file as $key => $value) {
            $this->validate($request, [
                'file.' . $key => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ],[
                'file.' . $key . '.required' => 'Gambar tidak boleh kosong',
                'file.' . $key . '.image' => 'File yang diupload harus berupa gambar',
                'file.' . $key . '.mimes' => 'File yang diupload harus berupa gambar dengan format jpeg, png, jpg',
                'file.' . $key . '.max' => 'Ukuran file yang diupload maksimal 2 MB'
            ]);
            $nama_file =  trim(pathinfo($value->getClientOriginalName(), PATHINFO_FILENAME)) . '.png';
            // $tujuan_upload = config('app.app_images_url')."/parts/";
            $value->move('images/parts', $nama_file);
        }
        // return redirect()->back()->with('success', 'Data Berhasil Diupload');
        // redirect dan membawa $request->search
        return redirect()->route('parts.uploadimage.form-input', ['search' => $request->search])->with('success', 'Data Berhasil Diupload');
        
    }

}
