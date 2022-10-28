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
            $request->get('no_st'),
            $companyid,
        );

        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            return response()->json(
                [
                    'status' => 1,
                    'data' => $data,
                ]
            );
        } else {
            return response()->json([
                'status' => 0,
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
        $no_sj = json_decode($request->get('no_sj'));
        if (is_array($no_sj)) {
            $no_sj = $no_sj;
        } else {
            $no_sj = array(
                (object) array(
                    'no_sj' => $request->get('no_sj'),
                )
            );
        }

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

        function uploadImage($request, $no_sj)
        {
            $nama_file = trim(str_replace('/', '', $request->get('no_st'))) . '_' . trim(str_replace('/', '', $no_sj));
            $image = Image::make($request->file('foto'));
            $image->resize(600, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $image->save('assets/images/sj/' . $nama_file . '.jpg');
        }

        $no_sj_err = [];
        foreach ($no_sj as $key => $value) {
            $responseApi = ApiService::PenerimaanSuratJalanSimpan(
                trim($value->no_sj),
                trim($request->get('tgl_terima')),
                trim($request->get('jam_terima')),
                trim($companyid),
            );
            $statusApi = json_decode($responseApi)->status;

            if ($statusApi == 0) {
                $no_sj_err[] = $value->no_sj;
            } else {
                if ($request->hasFile('foto')) {
                    uploadImage($request, $value->no_sj);
                }
            }
        }

        if (count($no_sj_err) > 0) {
            $statusApi = 1;
            $messageApi =  'Surat Jalan Berhasil Diterima, No Surat Jalan : ' . implode(', ', $no_sj_err) . ' Gagal Diterima';
        } else {
            $statusApi = 1;
            $messageApi =  'Surat Jalan Berhasil Diterima';
        }

        if ($statusApi == 1) {
            return redirect()->back()->with('success', $messageApi);
        }
        // else {
        //     return redirect()->back()->withInput()->with('failed', $messageApi);
        // }
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
            $nama_file = trim(str_replace('/', '', $request->get('no_st'))) . '_' . trim(str_replace('/', '', $request->get('no_sj')));

            if (file_exists('assets/images/sj/' . $nama_file . '.jpg')) {
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
