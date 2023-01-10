<?php

namespace App\Http\Controllers\app\Parts;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class uplooadImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view(
            'layouts.parts.uploadimage.uploadimage',
            [
                'title_menu'    => 'Upload Gambar part',
            ]
        );
    }

    public function store(Request $request)
    {
        foreach ($request->file as $key => $value) {
            $this->validate($request, [
                'file.' . $key => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            $nama_file =  trim(pathinfo($value->getClientOriginalName(), PATHINFO_FILENAME)) . '.png';
            $tujuan_upload = 'C:\xampp\htdocs\suma-pmo\public\assets\images\parts';
            $value->move($tujuan_upload, $nama_file);
        }

        return redirect()->back()->with('success', 'Data Berhasil Diupload');
    }

    public function daftarImagePart()
    {
        $data = DB::table('mspart')
            ->lock('with (nolock)')->select('*', DB::raw("'http://localhost:2022/suma-pmo/public/assets/images/parts/' + RTRIM(mspart.kd_part) + '.png' as url"))
            ->orderBy('kd_part', 'asc')
            ->paginate(24);
        return response()->json($data, 200);
    }

}
