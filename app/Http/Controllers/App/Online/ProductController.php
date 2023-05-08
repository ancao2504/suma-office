<?php

namespace App\Http\Controllers\app\Online;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiService;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function daftarProduct(Request $request)
    {
        if(!empty($request->get('param'))){
            $filter = json_decode(base64_decode($request->get('param')));
            $request->merge([
                'part_number'   => $filter->part_number,
                'page'          => $filter->page,
                'per_page'      => $filter->per_page
            ]);
        }

        $view = view('layouts.online.product.product', [
            'title_menu'    => 'Products',
            'filter' => (object)[
                'part_number'   => $request->get('part_number'),
                'page'          => $request->get('page'),
                'per_page'      => $request->get('per_page')
            ]
        ]);

        // ! Cek apakah ada part_number yang diinputkan
        //  * jika ada maka diasumsikan dari ajax requset dari ajax
        if (!empty($request->get('part_number')) && $request->get('part_number') != '') {

            $responseApi = ApiService::SearchProductMarketplaceByPartNumber(
                strtoupper(trim($request->get('part_number'))),
                strtoupper(trim($request->session()->get('app_user_company_id'))),
                $request->get('page')??1,
                in_array($request->get('per_page'), [10, 25, 50]) ? $request->get('per_page') : 10
            );

            $statusApi = json_decode($responseApi)->status;
            if ($statusApi == 1) {
                if($request->ajax()){
                    return response()->json([
                        'status'    => 1,
                        'message'   => 'success',
                        'data'      => Str::between($view->with('data_all', json_decode($responseApi)->data)->render(), '<!--start::container-->', '<!--end::container-->')
                    ]);
                } else {
                    return $view->with('data_all', json_decode($responseApi)->data);
                }
            } else {
                // ! jika terdapat error dari api
                if($request->ajax()){
                    return response()->json([
                        'status'    => 0,
                        'message'   => json_decode($responseApi)->message,
                        'data'      =>  ''
                    ]);
                } else {
                    return $view->with('data_all', (object)[
                        'status'    => 0,
                        'message'   => json_decode($responseApi)->message,
                        'data'      => []
                    ]);
                }
            }

        }else {
            return $view->with('data_all', (object)[
                'status'    => 0,
                'message'   => 'Belum ada data yang dicari',
                'data'      => []
            ]);
        }
    }

    public function formProduct(Request $request, $param)
    {
        $param_data = json_decode(base64_decode($param));

        $request->merge([
            'part_number'   => $param_data->part_number,
        ]);

        // hubungkan dengan server
        $responseApi = ApiService::DetailProductMarketplaceByPartNumber(
            strtoupper(trim($request->part_number)),
            strtoupper(trim($request->session()->get('app_user_company_id')))
        );

        if (json_decode($responseApi)->status == 0) {
            return redirect()->back()->withInput()->with('failed', json_decode($responseApi)->message);
        }

        $view = view('layouts.online.product.form', [
            'title_menu'    => 'Products',
            'filter_header' => $param_data->filter,
            'data_all'      => json_decode($responseApi)->data->data,
            'kategori'      => json_decode($responseApi)->data->kategori,
            'logistic'      => json_decode($responseApi)->data->logistic,
            'etalase'       => json_decode($responseApi)->data->etalase,
        ]);

        return $view;
    }

    public function addProduct(Request $request)
    {
        $request->validate([
            'image'         => 'required',
            'nama'          => 'required',
            'deskripsi'     => 'required',
            'harga'         => 'required',
            'stok'          => 'required',
            'min_order'     => 'required',
            'berat'         => 'required',
            'ukuran'        => 'required',
            'sku'           => 'required',
            'kondisi'       => 'required',
            'kategori'      => 'required',
            'status'        => 'required',
            'logistic'      => 'required',
        ],[
            'image.required'        => 'Gambar tidak boleh kosong',
            'nama.required'         => 'Nama tidak boleh kosong',
            'deskripsi.required'    => 'Deskripsi tidak boleh kosong',
            'harga.required'        => 'Harga tidak boleh kosong',
            'stok.required'         => 'Stok tidak boleh kosong',
            'min_order.required'    => 'Min Order tidak boleh kosong',
            'berat.required'        => 'Berat tidak boleh kosong',
            'ukuran.required'       => 'Ukuran tidak boleh kosong',
            'sku.required'          => 'SKU tidak boleh kosong',
            'kondisi.required'      => 'Kondisi tidak boleh kosong',
            'kategori.required'     => 'Kategori tidak boleh kosong',
            'status.required'       => 'Status tidak boleh kosong',
            'logistic.required'     => 'Logistic tidak boleh kosong',
        ]);

        if($request->marketplace_update == 'tokopedia'){
            // ! cek apakah domain user yang mengakses adalah suma-honda.id jika bukan maka akan keluar pesan
            // ! karena image yang bisa di upload pada tokopedia harus dengan url image yang diakses secara public
            foreach(json_decode($request->image) as $key => $value){
                if(!str_contains($request->instance()->getHost(), 'suma-honda.id')){
                    return Response()->json([
                        'status'    => 0,
                        'message'   => "Maaf untuk <b>Tokopedia</b> harus dengan Website PMO online yaitu : <a href='https://www.suma-honda.id' target='_blank'>suma-honda.id</a>",
                        'data'      => ''
                    ]);
                }
            }

            // TODO : upload gambar ke tokopedia$image_upload_id = [];
            $image_tokped = [];
            $image_tamp_lokal = [];
            foreach(json_decode($request->image) as $key => $value){
                $file = file_get_contents($value);
                $file_name = config('app.app_images_url').'images/temp/'.$request->get('sku').'_'.$key.'.jpg';
                file_put_contents($file_name, $file);
                $image_tokped[$key] = url($file_name);
                $image_tamp_lokal[$key] = $file_name;
            }

            $request->merge([
                'image' => json_encode($image_tokped)
            ]);
        }

        $responseApi = ApiService::addProductMarketplace(
            strtoupper(trim($request->session()->get('app_user_company_id'))),
            json_decode($request->image),
            $request->nama,
            $request->merek,
            $request->deskripsi,
            $request->harga,
            $request->stok,
            $request->min_order,
            $request->berat,
            $request->ukuran,
            $request->sku,
            $request->kondisi,
            $request->kategori,
            $request->status,
            $request->etalase,
            collect($request->logistic)->where('logistic_id', '!=', '0')
        );

        if($request->marketplace_update == 'tokopedia'){
            // ! Hapus foto jika sudah di kirim ke tokopedia atau gagal
            foreach($image_tamp_lokal as $key => $value){
                if(file_exists($value)){
                    unlink($value);
                }
            }
        }

        if (json_decode($responseApi)->status == 0) {
            return Response()->json([
                'status'    => 0,
                'message'   => json_decode($responseApi)->message,
                'data'      => ''
            ]);
        }

        return Response()->json([
            'status'    => 1,
            'message'   => json_decode($responseApi)->message,
            'data'      => json_decode($responseApi)->data
        ]);
    }
}
