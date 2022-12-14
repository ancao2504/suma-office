<?php

namespace App\Http\Controllers\app\Setting\CetakUlang;

use App\Helpers\ApiService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Jenssegers\Agent\Agent as Agent;
use Illuminate\Support\Facades\Date;

class CetakUlangController extends Controller
{
    public function index(Request $request) {
        $year = date('Y');
        $month = date('m');

        if(!empty($request->get('year'))) {
            $year = $request->get('year');
        }
        if(!empty($request->get('month'))) {
            $month = $request->get('month');
        }

        $responseApi = ApiService::SettingCetakUlangDaftar($year, $month, 'Faktur', $request->get('page'), $request->get('per_page'),
                        strtoupper(trim($request->session()->get('app_user_role_id'))),
                        strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            $data_page = new Collection();
            $data_page->push((object) [
                'search'        => $request->get('search'),
                'current_page'  => $data->from,
                'from'          => $data->from,
                'to'            => $data->to,
                'total'         => $data->total,
                'page'          => $data->current_page,
                'per_page'      => $data->per_page
            ]);

            $data_filter = new Collection();
            $data_filter->push((object) [
                'year'      => $year,
                'month'     => $month,
            ]);

            $Agent = new Agent();
            $device = 'Desktop';
            if($Agent->isMobile()) {
                $device = 'Mobile';
            }

            return view('layouts.settings.cetakulang.cetakulang', [
                'title_menu'        => 'Cetak Ulang',
                'device'            => $device,
                'data_page'         => $data_page->first(),
                'data_filter'       => $data_filter->first(),
                'data_cetak_ulang'  => $data,
            ]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function cekNomorDokumen(Request $request) {
        $responseApi = ApiService::SettingCetakUlangCekDokumen($request->get('nomor_dokumen'),
                        strtoupper(trim($request->get('jenis_transaksi'))),
                        strtoupper(trim($request->get('divisi'))),
                        strtoupper(trim($request->session()->get('app_user_role_id'))));
        return json_decode($responseApi, true);
    }

    public function simpanCetakUlang(Request $request) {
        $responseApi = ApiService::SettingCetakUlangSimpan(strtoupper(trim($request->get('nomor_dokumen'))),
                        strtoupper(trim($request->get('transaksi'))), strtoupper(trim($request->get('divisi'))),
                        strtoupper(trim($request->get('kode_cabang'))), strtoupper(trim($request->get('company_cabang'))),
                        $request->get('status_approve'), $request->get('status_edit'),
                        strtoupper(trim($request->get('alasan'))),
                        strtoupper(trim($request->session()->get('app_user_role_id'))),
                        strtoupper(trim($request->session()->get('app_user_company_id'))),
                        strtoupper(trim($request->session()->get('app_user_id'))));

        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            return redirect()->route('setting.setting-cetak-ulang')->with('success', $messageApi);
        } else {
            return redirect()->route('setting.setting-cetak-ulang')->with('failed', $messageApi);
        }
    }
}
