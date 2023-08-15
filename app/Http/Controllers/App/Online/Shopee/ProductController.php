<?php

namespace app\Http\Controllers\App\Online\Shopee;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\App\ServiceShopee;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function daftarPartNumber(Request $request)
    {
        $view = view('layouts.online.shopee.product.product', [
            'title_menu'    => 'Products'
        ]);

        if (!empty($request->get('part_number')) && $request->get('part_number') != '' && $request->ajax()) {

            $responseApi = ServiceShopee::SearchProductByPartNumber(
                strtoupper(trim($request->get('part_number'))),
                strtoupper(trim($request->session()->get('app_user_company_id')))
            );

            $statusApi = json_decode($responseApi)->status;
            if ($statusApi == 1) {
                return response()->json([
                    'status'    => 1,
                    'message'   => 'success',
                    'data'      => Str::between($view->with('data_all', json_decode($responseApi)->data)->render(), '<!--start::container-->', '<!--end::container-->')
                ]);
            } else {
                return response()->json([
                    'status'    => 0,
                    'message'   => json_decode($responseApi)->message,
                    'data'      =>  ''
                ]);
            }
        } else {
            return $view->with('data_all', (object)[
                'status'    => 0,
                'message'   => 'Belum ada data yang dicari',
                'data'      => []
            ]);
        }
    }

    public function cekProductId(Request $request)
    {
        $responseApi = ServiceShopee::CekProductId(
            strtoupper(trim($request->get('product_id'))),
            strtoupper(trim($request->session()->get('app_user_company_id')))
        );
        $statusApi = json_decode($responseApi)->status;

        if ($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;

            $view = view('layouts.online.shopee.product.edit_info', [
                'title_menu'    => 'Products',
                'dataApi'       => $dataApi
            ]);

            return [
                'status'    => 1,
                'message'   => 'success',
                'data'      => $view->render()
            ];
        } else {
            return json_decode($responseApi, true);
        }
    }

    public function updateProductId(Request $request)
    {
        $responseApi = ServiceShopee::UpdateShopeeidInPart(
            strtoupper(trim($request->get('part_number'))),
            strtoupper(trim($request->get('product_id'))),
            strtoupper(trim($request->session()->get('app_user_company_id')))
        );
        return json_decode($responseApi, true);
    }

    public function brandList(Request $request){
        $request->validate([
            'category_id' => 'required'
        ], [
            'category_id.required' => 'Category ID tidak boleh kosong'
        ]);

        $responseApi = ServiceShopee::brandList($request->category_id, $request->offset);
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
