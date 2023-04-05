<?php

namespace App\Http\Controllers\app\Online;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiService;
use App\Helpers\ApiServiceShopee;
use Facade\FlareClient\Http\Response;
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
        // dd(json_decode($responseApi));
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

    public function updateProduct(Request $request)
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
        ]);
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
        return $responseApi;
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
