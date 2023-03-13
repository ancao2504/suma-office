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
        if(strtoupper(trim($request->session()->get('app_user_role_id'))) == 'MD_REQ_API') {
            return redirect()->back()->withInput()->with('failed', 'Anda tidak memiliki akses untuk membuka halaman ini');
        }
        $view = view('layouts.online.product.product', [
            'title_menu'    => 'Products'
        ]);

        if (!empty($request->get('part_number')) && $request->get('part_number') != '' && $request->ajax()) {
            
            $responseApi = ApiService::SearchProductMarketplaceByPartNumber(
                strtoupper(trim($request->get('part_number'))),
                strtoupper(trim($request->session()->get('app_user_company_id'))),
                $request->get('page')??1,
                in_array($request->get('per_page'), [10, 25, 50]) ? $request->get('per_page') : 10
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

    public function formProduct(Request $request, $part_number){
        if(strtoupper(trim($request->session()->get('app_user_role_id'))) == 'MD_REQ_API') {
            return redirect()->back()->withInput()->with('failed', 'Anda tidak memiliki akses untuk membuka halaman ini');
        }

        $view = view('layouts.online.product.form', [
            'title_menu'    => 'Products',
            'data_all'     => ''
        ]);

        return $view;
    }
}
