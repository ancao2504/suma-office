<?php

namespace App\Http\Controllers\app\Orders\Penerimaan;

use App\Helpers\ApiService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;

class SuratJalanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view(
            'layouts.orders.penerimaan.suratjalan',
            [
                'title_menu'    => 'Surat Jalan',
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function CekPenerimaanSJ(Request $request)
    {
        $companyid = strtoupper(trim($request->session()->get('app_user_company_id')));
        $responseApi = ApiService::CekPenerimaanSuratJalan(
            $request->get('nomor_sj'),
            $companyid,
        );

        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            if ($messageApi == 'BELUM_TERIMA') {
                return response()->json([
                    'status' => 0,
                    'message' => 'Surat Jalan belum diterima',
                    'data_sj' => $data,
                ]);
            } else {
                return response()->json([
                    'status' => 1,
                    'message' => 'Surat Jalan sudah diterima',
                    'data_sj' => $data,
                ]);
            }
        } else {
            return response()->json([
                'status' => 404,
                'message' => $messageApi,
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd(config('constants.app.app_images_url_SJ'));
        $this->validate(
            $request,
            [
                'no_sj' => 'required',
                'tgl_terima' => 'required',
                'jam_terima' => 'required',
                'foto' => 'image'
            ],
            [
                'no_sj.required' => 'Surat Jalan Kososng',
                'tgl_terima.required' => 'Tanggal Terima Kososng',
                'jam_terima.required' => 'Jam Terima Kososng',
                'foto' => 'Foto harus berupa Foto atau gambar',
            ]
        );

        $companyid = strtoupper(trim($request->session()->get('app_user_company_id')));

        $responseApi = ApiService::PenerimaanSuratJalanSimpan(
            trim($request->get('no_sj')),
            trim($request->get('tgl_terima')),
            trim($request->get('jam_terima')),
            trim($companyid),
        );

        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            if ($request->hasFile('foto')) {
                $nama_file = str_replace('/', '', $request->no_sj);
                $request->file('foto')->move('assets/images/sj/', $nama_file . '.jpg');

                $image = Image::make('assets/images/sj/' . $nama_file . '.jpg');
                $image->resize(600, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $image->save('assets/images/sj/' . $nama_file . '.jpg');
            }
            return redirect()->back()->with('success', $messageApi);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $companyid = strtoupper(trim($request->session()->get('app_user_company_id')));

        $responseApi = ApiService::PenerimaanSuratJalanHapus(
            trim($request->get('no_sj')),
            trim($companyid),
        );

        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            // hapus foto
            $nama_file = str_replace('/', '', $request->no_sj);
            if (file_exists(
                'assets/images/sj/' . $nama_file . '.jpg'
            )) {
                unlink('assets/images/sj/' . $nama_file . '.jpg');
            }
            return redirect()->back()->with('success', $messageApi);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function filter()
    {
        return view(
            'layouts.orders.penerimaan.filtersuratjalan',
            [
                'title_menu'    => 'Report Surat Jalan',
            ]
        );
    }

    public function report(Request $request)
    {
        $this->validate(
            $request,
            [
                'start_date' => 'required',
                'end_date' => 'required',
            ],
            [
                'required' => 'Tanggal Awal dan Tanggal Akhir harus diisi',
            ]
        );

        $companyid = strtoupper(trim($request->session()->get('app_user_company_id')));

        $responseApi = ApiService::PenerimaanSuratJalanReport(
            trim($request->get('start_date')),
            trim($request->get('end_date')),
            $companyid,
            trim($request->get('no_serah_terima')),
            trim($request->get('driver')),
        );

        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            return view(
                'reports.suratjalan.reportsuratjalan',
                [
                    'title_menu'    => 'Report Surat Jalan',
                    'data_report'   => $data,
                    'nama_company'      => 'PT KHARISMA SUMA JAYA SAKTI',
                    'alamat_company'    => 'JL RUNGKUT INDUSTRI III / 20',
                    'kota_company'      => 'SURABAYA',
                    'periode'           => date('d-F-Y', strtotime($request->get('start_date'))) . ' s/d ' . date('d-F-Y', strtotime($request->get('end_date'))),
                ]
            );
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }
}
