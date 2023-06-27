<?php

namespace App\Http\Controllers\Api\Backend\Dashboard\Management;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;

class ApiDashboardManagementKuartalController extends Controller
{
    public function dashboardSalesKuartal(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'year'              => 'required|string',
                'fields'            => 'required|string',
                'option_company'    => 'required|string',
                'user_id'           => 'required|string',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning('Pilih bulan, tahun, dan filter company terlebih dahulu');
            }

            if(strtoupper(trim($request->get('option_company'))) == 'COMPANY_TERTENTU') {
                if(empty($request->get('companyid')) || strtoupper(trim($request->get('companyid'))) == '') {
                    return Response::responseWarning('Opsi company tertentu di wajibkan untuk mengisi data company terlebih dahulu');
                }
            }

            $sql = DB::table('company')->lock('with (nolock)')
                    ->selectRaw("isnull(company.companyid, '') as companyid,
                                year(dateadd(day, 1, stsclose.close_mkr)) as tahun_berjalan")
                    ->leftJoin(DB::raw('stsclose with (nolock)'), function($join) {
                        $join->on('stsclose.companyid', '=', 'company.companyid');
                    })
                    ->whereRaw("isnull(company.inisial, 0)=1")
                    ->first();

            $tahun_berjalan = $sql->tahun_berjalan;

            if((int)$request->get('year') > (int)$tahun_berjalan) {
                return Response::responseWarning('Periode tahun yang dipilih tidak boleh melebihi transaksi tahun berjalan');
            }

            DB::transaction(function () use ($request) {
                DB::insert('exec SP_LaporanPenjualanKuartal ?,?,?,?,?,?,?,?,?', [
                    strtoupper(trim($request->get('user_id'))), strtoupper(trim($request->get('year'))), strtoupper(trim($request->get('fields'))),
                    strtoupper(trim($request->get('option_company'))), strtoupper(trim($request->get('companyid'))),
                    strtoupper(trim($request->get('kabupaten'))), strtoupper(trim($request->get('supervisor'))),
                    strtoupper(trim($request->get('salesman'))), strtoupper(trim($request->get('produk')))
                ]);
            });


            $sql = DB::table('laporan_penjualan_kuartal')->lock('with (nolock)')
                    ->selectRaw("isnull(kabupaten, '') as kabupaten, isnull(tahun, 0) as tahun, isnull(tahun_lalu, 0) as tahun_lalu,
                                isnull(faktur_1, 0) as faktur_1, isnull(faktur_2, 0) as faktur_2, isnull(faktur_3, 0) as faktur_3,
                                isnull(faktur_4, 0) as faktur_4, isnull(faktur_5, 0) as faktur_5, isnull(faktur_6, 0) as faktur_6,
                                isnull(faktur_7, 0) as faktur_7, isnull(faktur_8, 0) as faktur_8, isnull(faktur_9, 0) as faktur_9,
                                isnull(faktur_10, 0) as faktur_10, isnull(faktur_11, 0) as faktur_11, isnull(faktur_12, 0) as faktur_12,
                                isnull(faktur_total, 0) as faktur_total,
                                isnull(faktur_lalu_1, 0) as faktur_lalu_1, isnull(faktur_lalu_2, 0) as faktur_lalu_2, isnull(faktur_lalu_3, 0) as faktur_lalu_3,
                                isnull(faktur_lalu_4, 0) as faktur_lalu_4, isnull(faktur_lalu_5, 0) as faktur_lalu_5, isnull(faktur_lalu_6, 0) as faktur_lalu_6,
                                isnull(faktur_lalu_7, 0) as faktur_lalu_7, isnull(faktur_lalu_8, 0) as faktur_lalu_8, isnull(faktur_lalu_9, 0) as faktur_lalu_9,
                                isnull(faktur_lalu_10, 0) as faktur_lalu_10, isnull(faktur_lalu_11, 0) as faktur_lalu_11, isnull(faktur_lalu_12, 0) as faktur_lalu_12,
                                isnull(faktur_lalu_total, 0) as faktur_lalu_total,
                                isnull(faktur_kuartal_1, 0) as faktur_kuartal_1, isnull(faktur_kuartal_2, 0) as faktur_kuartal_2,
                                isnull(faktur_kuartal_3, 0) as faktur_kuartal_3, isnull(faktur_kuartal_4, 0) as faktur_kuartal_4,
                                isnull(faktur_lalu_kuartal_1, 0) as faktur_lalu_kuartal_1, isnull(faktur_lalu_kuartal_2, 0) as faktur_lalu_kuartal_2,
                                isnull(faktur_lalu_kuartal_3, 0) as faktur_lalu_kuartal_3, isnull(faktur_lalu_kuartal_4, 0) as faktur_lalu_kuartal_4,
                                isnull(faktur_semester_1, 0) as faktur_semester_1, isnull(faktur_semester_2, 0) as faktur_semester_2,
                                isnull(faktur_lalu_semester_1, 0) as faktur_lalu_semester_1, isnull(faktur_lalu_semester_2, 0) as faktur_lalu_semester_2,
                                isnull(faktur_ytd_1, 0) as faktur_ytd_1, isnull(faktur_ytd_2, 0) as faktur_ytd_2, isnull(faktur_ytd_3, 0) as faktur_ytd_3,
                                isnull(faktur_ytd_4, 0) as faktur_ytd_4, isnull(faktur_ytd_5, 0) as faktur_ytd_5, isnull(faktur_ytd_6, 0) as faktur_ytd_6,
                                isnull(faktur_ytd_7, 0) as faktur_ytd_7, isnull(faktur_ytd_8, 0) as faktur_ytd_8, isnull(faktur_ytd_9, 0) as faktur_ytd_9,
                                isnull(faktur_ytd_10, 0) as faktur_ytd_10, isnull(faktur_ytd_11, 0) as faktur_ytd_11, isnull(faktur_ytd_12, 0) as faktur_ytd_12,
                                isnull(faktur_lalu_ytd_1, 0) as faktur_lalu_ytd_1, isnull(faktur_lalu_ytd_2, 0) as faktur_lalu_ytd_2, isnull(faktur_lalu_ytd_3, 0) as faktur_lalu_ytd_3,
                                isnull(faktur_lalu_ytd_4, 0) as faktur_lalu_ytd_4, isnull(faktur_lalu_ytd_5, 0) as faktur_lalu_ytd_5, isnull(faktur_lalu_ytd_6, 0) as faktur_lalu_ytd_6,
                                isnull(faktur_lalu_ytd_7, 0) as faktur_lalu_ytd_7, isnull(faktur_lalu_ytd_8, 0) as faktur_lalu_ytd_8, isnull(faktur_lalu_ytd_9, 0) as faktur_lalu_ytd_9,
                                isnull(faktur_lalu_ytd_10, 0) as faktur_lalu_ytd_10, isnull(faktur_lalu_ytd_11, 0) as faktur_lalu_ytd_11, isnull(faktur_lalu_ytd_12, 0) as faktur_lalu_ytd_12,
                                isnull(faktur_kuartal_ytd_1, 0) as faktur_kuartal_ytd_1, isnull(faktur_kuartal_ytd_2, 0) as faktur_kuartal_ytd_2, isnull(faktur_kuartal_ytd_3, 0) as faktur_kuartal_ytd_3, isnull(faktur_kuartal_ytd_4, 0) as faktur_kuartal_ytd_4,
                                isnull(faktur_lalu_kuartal_ytd_1, 0) as faktur_lalu_kuartal_ytd_1, isnull(faktur_lalu_kuartal_ytd_2, 0) as faktur_lalu_kuartal_ytd_2, isnull(faktur_lalu_kuartal_ytd_3, 0) as faktur_lalu_kuartal_ytd_3, isnull(faktur_lalu_kuartal_ytd_4, 0) as faktur_lalu_kuartal_ytd_4")
                    ->where('kd_key', strtoupper(trim($request->get('user_id'))))
                    ->orderBy('kabupaten', 'asc')
                    ->first();

            $dashboard_penjualan_kuartal = new Collection();

            $dashboard_penjualan_kuartal->push((object) [
                'year'      => [
                    'selected'  => strtoupper(trim($sql->tahun)),
                    'previous'  => strtoupper(trim($sql->tahun_lalu)),
                ],
                'semester'  => [
                    [
                        'keterangan'    => 'Semester 1',
                        'selected'      => [
                            'value'     => (double)$sql->faktur_semester_1,
                            'kontribusi'=> ((double)$sql->faktur_total <= 0 || (double)$sql->faktur_semester_1 <= 0) ? 0 :
                                            ((double)$sql->faktur_semester_1 / (double)$sql->faktur_total) * 100
                        ],
                        'previous'      => [
                            'value'     => (double)$sql->faktur_lalu_semester_1,
                            'kontribusi'=> ((double)$sql->faktur_lalu_total <= 0 || (double)$sql->faktur_lalu_semester_1 <= 0) ? 0 :
                                            ((double)$sql->faktur_lalu_semester_1 / (double)$sql->faktur_lalu_total) * 100
                        ],
                    ],  [
                        'keterangan'    => 'Semester 2',
                        'selected'      => [
                            'value'     => (double)$sql->faktur_semester_2,
                            'kontribusi'=> ((double)$sql->faktur_total <= 0 || (double)$sql->faktur_semester_2 <= 0) ? 0 :
                                            ((double)$sql->faktur_semester_2 / (double)$sql->faktur_total) * 100
                        ],
                        'previous'      => [
                            'value'     => (double)$sql->faktur_lalu_semester_2,
                            'kontribusi'=> ((double)$sql->faktur_lalu_total <= 0 || (double)$sql->faktur_lalu_semester_2 <= 0) ? 0 :
                                            ((double)$sql->faktur_lalu_semester_2 / (double)$sql->faktur_lalu_total) * 100
                        ],
                    ],
                ],
                'quarter'   => [
                    'summary'   => [
                        [
                            'keterangan'=> 'Q1',
                            'selected'  => (double)$sql->faktur_kuartal_ytd_1,
                            'previous'  => (double)$sql->faktur_lalu_kuartal_ytd_1,
                            'growth'    => ((double)$sql->faktur_kuartal_ytd_1 <= 0 || (double)$sql->faktur_lalu_kuartal_ytd_1 <= 0) ? 0 :
                                            (((double)$sql->faktur_kuartal_ytd_1 - (double)$sql->faktur_lalu_kuartal_ytd_1) / (double)$sql->faktur_lalu_kuartal_ytd_1) * 100,
                        ], [
                            'keterangan'=> 'Q1-Q2',
                            'selected'  => (double)$sql->faktur_kuartal_ytd_2,
                            'previous'  => (double)$sql->faktur_lalu_kuartal_ytd_2,
                            'growth'    => ((double)$sql->faktur_kuartal_ytd_2 <= 0 || (double)$sql->faktur_lalu_kuartal_ytd_2 <= 0) ? 0 :
                                            (((double)$sql->faktur_kuartal_ytd_2 - (double)$sql->faktur_lalu_kuartal_ytd_2) / (double)$sql->faktur_lalu_kuartal_ytd_2) * 100,
                        ], [
                            'keterangan'=> 'Q1-Q3',
                            'selected'  => (double)$sql->faktur_kuartal_ytd_3,
                            'previous'  => (double)$sql->faktur_lalu_kuartal_ytd_3,
                            'growth'    => ((double)$sql->faktur_kuartal_ytd_3 <= 0 || (double)$sql->faktur_lalu_kuartal_ytd_3 <= 0) ? 0 :
                                            (((double)$sql->faktur_kuartal_ytd_3 - (double)$sql->faktur_lalu_kuartal_ytd_3) / (double)$sql->faktur_lalu_kuartal_ytd_3) * 100,
                        ], [
                            'keterangan'=> 'Q1-Q4',
                            'selected'  => (double)$sql->faktur_kuartal_ytd_4,
                            'previous'  => (double)$sql->faktur_lalu_kuartal_ytd_4,
                            'growth'    => ((double)$sql->faktur_kuartal_ytd_4 <= 0 || (double)$sql->faktur_lalu_kuartal_ytd_4 <= 0) ? 0 :
                                                (((double)$sql->faktur_kuartal_ytd_4 - (double)$sql->faktur_lalu_kuartal_ytd_4) / (double)$sql->faktur_lalu_kuartal_ytd_4) * 100,
                        ],
                    ],
                    'quarter'   => [
                        [
                            'keterangan'    => 'Q1',
                            'selected'      => (double)$sql->faktur_kuartal_1,
                            'previous'      => (double)$sql->faktur_lalu_kuartal_1,
                            'growth'        => ((double)$sql->faktur_kuartal_1 <= 0 || (double)$sql->faktur_lalu_kuartal_1 <= 0) ? 0 :
                                                (((double)$sql->faktur_kuartal_1 - (double)$sql->faktur_lalu_kuartal_1) / (double)$sql->faktur_lalu_kuartal_1) * 100,
                            'kontribusi'    => [
                                'selected'  => ((double)$sql->faktur_total <= 0 || (double)$sql->faktur_kuartal_1 <= 0) ? 0 : ((double)$sql->faktur_kuartal_1 / (double)$sql->faktur_total) * 100,
                                'previous'  => ((double)$sql->faktur_lalu_total <= 0 || (double)$sql->faktur_lalu_kuartal_1 <= 0) ? 0 : ((double)$sql->faktur_lalu_kuartal_1 / (double)$sql->faktur_lalu_total) * 100,
                            ],
                        ], [
                            'keterangan'    => 'Q2',
                            'selected'      => (double)$sql->faktur_kuartal_2,
                            'previous'      => (double)$sql->faktur_lalu_kuartal_2,
                            'growth'        => ((double)$sql->faktur_kuartal_2 <= 0 || (double)$sql->faktur_lalu_kuartal_2 <= 0) ? 0 :
                                                (((double)$sql->faktur_kuartal_2 - (double)$sql->faktur_lalu_kuartal_2) / (double)$sql->faktur_lalu_kuartal_2) * 100,
                            'kontribusi'    => [
                                'selected'  => ((double)$sql->faktur_total <= 0 || (double)$sql->faktur_kuartal_2 <= 0) ? 0 : ((double)$sql->faktur_kuartal_2 / (double)$sql->faktur_total) * 100,
                                'previous'  => ((double)$sql->faktur_lalu_total <= 0 || (double)$sql->faktur_lalu_kuartal_2 <= 0) ? 0 : ((double)$sql->faktur_lalu_kuartal_2 / (double)$sql->faktur_lalu_total) * 100,
                            ],
                        ], [
                            'keterangan'    => 'Q3',
                            'selected'      => (double)$sql->faktur_kuartal_3,
                            'previous'      => (double)$sql->faktur_lalu_kuartal_3,
                            'growth'        => ((double)$sql->faktur_kuartal_3 <= 0 || (double)$sql->faktur_lalu_kuartal_3 <= 0) ? 0 :
                                                (((double)$sql->faktur_kuartal_3 - (double)$sql->faktur_lalu_kuartal_3) / (double)$sql->faktur_lalu_kuartal_3) * 100,
                            'kontribusi'    => [
                                'selected'  => ((double)$sql->faktur_total <= 0 || (double)$sql->faktur_kuartal_3 <= 0) ? 0 : ((double)$sql->faktur_kuartal_3 / (double)$sql->faktur_total) * 100,
                                'previous'  => ((double)$sql->faktur_lalu_total <= 0 || (double)$sql->faktur_lalu_kuartal_3 <= 0) ? 0 : ((double)$sql->faktur_lalu_kuartal_3 / (double)$sql->faktur_lalu_total) * 100,
                            ],
                        ], [
                            'keterangan'    => 'Q4',
                            'selected'      => (double)$sql->faktur_kuartal_4,
                            'previous'      => (double)$sql->faktur_lalu_kuartal_4,
                            'growth'        => ((double)$sql->faktur_kuartal_4 <= 0 || (double)$sql->faktur_lalu_kuartal_4 <= 0) ? 0 :
                                                (((double)$sql->faktur_kuartal_4 - (double)$sql->faktur_lalu_kuartal_4) / (double)$sql->faktur_lalu_kuartal_4) * 100,
                            'kontribusi'    => [
                                'selected'  => ((double)$sql->faktur_total <= 0 || (double)$sql->faktur_kuartal_4 <= 0) ? 0 : ((double)$sql->faktur_kuartal_4 / (double)$sql->faktur_total) * 100,
                                'previous'  => ((double)$sql->faktur_lalu_total <= 0 || (double)$sql->faktur_lalu_kuartal_4 <= 0) ? 0 : ((double)$sql->faktur_lalu_kuartal_4 / (double)$sql->faktur_lalu_total) * 100,
                            ],
                        ],
                    ]
                ],
                'year_to_date' => [
                    [
                        'month'     => 'Januari',
                        'selected'  => (double)$sql->faktur_ytd_1,
                        'previous'  => (double)$sql->faktur_lalu_ytd_1,
                        'growth'    => ((double)$sql->faktur_ytd_1 <= 0 || (double)$sql->faktur_lalu_ytd_1 <= 0) ? 0 :
                                        (((double)$sql->faktur_ytd_1 - (double)$sql->faktur_lalu_ytd_1) / (double)$sql->faktur_lalu_ytd_1) * 100,
                    ], [
                        'month'     => 'Februari',
                        'selected'  => (double)$sql->faktur_ytd_2,
                        'previous'  => (double)$sql->faktur_lalu_ytd_2,
                        'growth'    => ((double)$sql->faktur_ytd_2 <= 0 || (double)$sql->faktur_lalu_ytd_2 <= 0) ? 0 :
                                        (((double)$sql->faktur_ytd_2 - (double)$sql->faktur_lalu_ytd_2) / (double)$sql->faktur_lalu_ytd_2) * 100,
                    ], [
                        'month'     => 'Maret',
                        'selected'  => (double)$sql->faktur_ytd_3,
                        'previous'  => (double)$sql->faktur_lalu_ytd_3,
                        'growth'    => ((double)$sql->faktur_ytd_3 <= 0 || (double)$sql->faktur_lalu_ytd_3 <= 0) ? 0 :
                                        (((double)$sql->faktur_ytd_3 - (double)$sql->faktur_lalu_ytd_3) / (double)$sql->faktur_lalu_ytd_3) * 100,
                    ], [
                        'month'     => 'April',
                        'selected'  => (double)$sql->faktur_ytd_4,
                        'previous'  => (double)$sql->faktur_lalu_ytd_4,
                        'growth'    => ((double)$sql->faktur_ytd_4 <= 0 || (double)$sql->faktur_lalu_ytd_4 <= 0) ? 0 :
                                        (((double)$sql->faktur_ytd_4 - (double)$sql->faktur_lalu_ytd_4) / (double)$sql->faktur_lalu_ytd_4) * 100,
                    ], [
                        'month'     => 'Mei',
                        'selected'  => (double)$sql->faktur_ytd_5,
                        'previous'  => (double)$sql->faktur_lalu_ytd_5,
                        'growth'    => ((double)$sql->faktur_ytd_5 <= 0 || (double)$sql->faktur_lalu_ytd_5 <= 0) ? 0 :
                                        (((double)$sql->faktur_ytd_5 - (double)$sql->faktur_lalu_ytd_5) / (double)$sql->faktur_lalu_ytd_5) * 100,
                    ], [
                        'month'     => 'Juni',
                        'selected'  => (double)$sql->faktur_ytd_6,
                        'previous'  => (double)$sql->faktur_lalu_ytd_6,
                        'growth'    => ((double)$sql->faktur_ytd_6 <= 0 || (double)$sql->faktur_lalu_ytd_6 <= 0) ? 0 :
                                        (((double)$sql->faktur_ytd_6 - (double)$sql->faktur_lalu_ytd_6) / (double)$sql->faktur_lalu_ytd_6) * 100,
                    ], [
                        'month'     => 'Juli',
                        'selected'  => (double)$sql->faktur_ytd_7,
                        'previous'  => (double)$sql->faktur_lalu_ytd_7,
                        'growth'    => ((double)$sql->faktur_ytd_7 <= 0 || (double)$sql->faktur_lalu_ytd_7 <= 0) ? 0 :
                                        (((double)$sql->faktur_ytd_7 - (double)$sql->faktur_lalu_ytd_7) / (double)$sql->faktur_lalu_ytd_7) * 100,
                    ], [
                        'month'     => 'Agustus',
                        'selected'  => (double)$sql->faktur_ytd_8,
                        'previous'  => (double)$sql->faktur_lalu_ytd_8,
                        'growth'    => ((double)$sql->faktur_ytd_8 <= 0 || (double)$sql->faktur_lalu_ytd_8 <= 0) ? 0 :
                                        (((double)$sql->faktur_ytd_8 - (double)$sql->faktur_lalu_ytd_8) / (double)$sql->faktur_lalu_ytd_8) * 100,
                    ], [
                        'month'     => 'September',
                        'selected'  => (double)$sql->faktur_ytd_9,
                        'previous'  => (double)$sql->faktur_lalu_ytd_9,
                        'growth'    => ((double)$sql->faktur_ytd_9 <= 0 || (double)$sql->faktur_lalu_ytd_9 <= 0) ? 0 :
                                        (((double)$sql->faktur_ytd_9 - (double)$sql->faktur_lalu_ytd_9) / (double)$sql->faktur_lalu_ytd_9) * 100,
                    ], [
                        'month'     => 'Oktober',
                        'selected'  => (double)$sql->faktur_ytd_10,
                        'previous'  => (double)$sql->faktur_lalu_ytd_10,
                        'growth'    => ((double)$sql->faktur_ytd_10 <= 0 || (double)$sql->faktur_lalu_ytd_10 <= 0) ? 0 :
                                        (((double)$sql->faktur_ytd_10 - (double)$sql->faktur_lalu_ytd_10) / (double)$sql->faktur_lalu_ytd_10) * 100,
                    ], [
                        'month'     => 'November',
                        'selected'  => (double)$sql->faktur_ytd_11,
                        'previous'  => (double)$sql->faktur_lalu_ytd_11,
                        'growth'    => ((double)$sql->faktur_ytd_11 <= 0 || (double)$sql->faktur_lalu_ytd_11 <= 0) ? 0 :
                                        (((double)$sql->faktur_ytd_11 - (double)$sql->faktur_lalu_ytd_11) / (double)$sql->faktur_lalu_ytd_11) * 100,
                    ], [
                        'month'     => 'Desember',
                        'selected'  => (double)$sql->faktur_ytd_12,
                        'previous'  => (double)$sql->faktur_lalu_ytd_12,
                        'growth'    => ((double)$sql->faktur_ytd_12 <= 0 || (double)$sql->faktur_lalu_ytd_12 <= 0) ? 0 :
                                        (((double)$sql->faktur_ytd_12 - (double)$sql->faktur_lalu_ytd_12) / (double)$sql->faktur_lalu_ytd_12) * 100,
                    ],
                ],
                'detail'    => [
                    [
                        'month'     => 'Januari',
                        'selected'  => (double)$sql->faktur_1,
                        'previous'  => (double)$sql->faktur_lalu_1,
                        'growth'    => ((double)$sql->faktur_1 <= 0 || (double)$sql->faktur_lalu_1 <= 0) ? 0 :
                                        (((double)$sql->faktur_1 - (double)$sql->faktur_lalu_1) / (double)$sql->faktur_lalu_1) * 100,
                        'kontribusi'=> [
                            'selected'  => ((double)$sql->faktur_1 <= 0 || (double)$sql->faktur_total <= 0) ? 0 : ((double)$sql->faktur_1 / (double)$sql->faktur_total) * 100,
                            'previous'  => ((double)$sql->faktur_lalu_1 <= 0 || (double)$sql->faktur_lalu_total <= 0) ? 0 : ((double)$sql->faktur_lalu_1 / (double)$sql->faktur_lalu_total) * 100,
                        ]
                    ], [
                        'month'     => 'Februari',
                        'selected'  => (double)$sql->faktur_2,
                        'previous'  => (double)$sql->faktur_lalu_2,
                        'growth'    => ((double)$sql->faktur_2 <= 0 || (double)$sql->faktur_lalu_2 <= 0) ? 0 :
                                        (((double)$sql->faktur_2 - (double)$sql->faktur_lalu_2) / (double)$sql->faktur_lalu_2) * 100,
                        'kontribusi'=> [
                            'selected'  => ((double)$sql->faktur_2 <= 0 || (double)$sql->faktur_total <= 0) ? 0 : ((double)$sql->faktur_2 / (double)$sql->faktur_total) * 100,
                            'previous'  => ((double)$sql->faktur_lalu_2 <= 0 || (double)$sql->faktur_lalu_total <= 0) ? 0 : ((double)$sql->faktur_lalu_2 / (double)$sql->faktur_lalu_total) * 100,
                        ]
                    ], [
                        'month'     => 'Maret',
                        'selected'  => (double)$sql->faktur_3,
                        'previous'  => (double)$sql->faktur_lalu_3,
                        'growth'    => ((double)$sql->faktur_3 <= 0 || (double)$sql->faktur_lalu_3 <= 0) ? 0 :
                                        (((double)$sql->faktur_3 - (double)$sql->faktur_lalu_3) / (double)$sql->faktur_lalu_3) * 100,
                        'kontribusi'=> [
                            'selected'  => ((double)$sql->faktur_3 <= 0 || (double)$sql->faktur_total <= 0) ? 0 : ((double)$sql->faktur_3 / (double)$sql->faktur_total) * 100,
                            'previous'  => ((double)$sql->faktur_lalu_3 <= 0 || (double)$sql->faktur_lalu_total <= 0) ? 0 : ((double)$sql->faktur_lalu_3 / (double)$sql->faktur_lalu_total) * 100,
                        ]
                    ], [
                        'month'     => 'April',
                        'selected'  => (double)$sql->faktur_4,
                        'previous'  => (double)$sql->faktur_lalu_4,
                        'growth'    => ((double)$sql->faktur_4 <= 0 || (double)$sql->faktur_lalu_4 <= 0) ? 0 :
                                        (((double)$sql->faktur_4 - (double)$sql->faktur_lalu_4) / (double)$sql->faktur_lalu_4) * 100,
                        'kontribusi'=> [
                            'selected'  => ((double)$sql->faktur_4 <= 0 || (double)$sql->faktur_total <= 0) ? 0 : ((double)$sql->faktur_4 / (double)$sql->faktur_total) * 100,
                            'previous'  => ((double)$sql->faktur_lalu_4 <= 0 || (double)$sql->faktur_lalu_total <= 0) ? 0 : ((double)$sql->faktur_lalu_4 / (double)$sql->faktur_lalu_total) * 100,
                        ]
                    ], [
                        'month'     => 'Mei',
                        'selected'  => (double)$sql->faktur_5,
                        'previous'  => (double)$sql->faktur_lalu_5,
                        'growth'    => ((double)$sql->faktur_5 <= 0 || (double)$sql->faktur_lalu_5 <= 0) ? 0 :
                                        (((double)$sql->faktur_5 - (double)$sql->faktur_lalu_5) / (double)$sql->faktur_lalu_5) * 100,
                        'kontribusi'=> [
                            'selected'  => ((double)$sql->faktur_5 <= 0 || (double)$sql->faktur_total <= 0) ? 0 : ((double)$sql->faktur_5 / (double)$sql->faktur_total) * 100,
                            'previous'  => ((double)$sql->faktur_lalu_5 <= 0 || (double)$sql->faktur_lalu_total <= 0) ? 0 : ((double)$sql->faktur_lalu_5 / (double)$sql->faktur_lalu_total) * 100,
                        ]
                    ], [
                        'month'     => 'Juni',
                        'selected'  => (double)$sql->faktur_6,
                        'previous'  => (double)$sql->faktur_lalu_6,
                        'growth'    => ((double)$sql->faktur_6 <= 0 || (double)$sql->faktur_lalu_6 <= 0) ? 0 :
                                        (((double)$sql->faktur_6 - (double)$sql->faktur_lalu_6) / (double)$sql->faktur_lalu_6) * 100,
                        'kontribusi'=> [
                            'selected'  => ((double)$sql->faktur_6 <= 0 || (double)$sql->faktur_total <= 0) ? 0 : ((double)$sql->faktur_6 / (double)$sql->faktur_total) * 100,
                            'previous'  => ((double)$sql->faktur_lalu_6 <= 0 || (double)$sql->faktur_lalu_total <= 0) ? 0 : ((double)$sql->faktur_lalu_6 / (double)$sql->faktur_lalu_total) * 100,
                        ]
                    ], [
                        'month'     => 'Juli',
                        'selected'  => (double)$sql->faktur_7,
                        'previous'  => (double)$sql->faktur_lalu_7,
                        'growth'    => ((double)$sql->faktur_7 <= 0 || (double)$sql->faktur_lalu_7 <= 0) ? 0 :
                                        (((double)$sql->faktur_7 - (double)$sql->faktur_lalu_7) / (double)$sql->faktur_lalu_7) * 100,
                        'kontribusi'=> [
                            'selected'  => ((double)$sql->faktur_7 <= 0 || (double)$sql->faktur_total <= 0) ? 0 : ((double)$sql->faktur_7 / (double)$sql->faktur_total) * 100,
                            'previous'  => ((double)$sql->faktur_lalu_7 <= 0 || (double)$sql->faktur_lalu_total <= 0) ? 0 : ((double)$sql->faktur_lalu_7 / (double)$sql->faktur_lalu_total) * 100,
                        ]
                    ], [
                        'month'     => 'Agustus',
                        'selected'  => (double)$sql->faktur_8,
                        'previous'  => (double)$sql->faktur_lalu_8,
                        'growth'    => ((double)$sql->faktur_8 <= 0 || (double)$sql->faktur_lalu_8 <= 0) ? 0 :
                                        (((double)$sql->faktur_8 - (double)$sql->faktur_lalu_8) / (double)$sql->faktur_lalu_8) * 100,
                        'kontribusi'=> [
                            'selected'  => ((double)$sql->faktur_8 <= 0 || (double)$sql->faktur_total <= 0) ? 0 : ((double)$sql->faktur_8 / (double)$sql->faktur_total) * 100,
                            'previous'  => ((double)$sql->faktur_lalu_8 <= 0 || (double)$sql->faktur_lalu_total <= 0) ? 0 : ((double)$sql->faktur_lalu_8 / (double)$sql->faktur_lalu_total) * 100,
                        ]
                    ], [
                        'month'     => 'September',
                        'selected'  => (double)$sql->faktur_9,
                        'previous'  => (double)$sql->faktur_lalu_9,
                        'growth'    => ((double)$sql->faktur_9 <= 0 || (double)$sql->faktur_lalu_9 <= 0) ? 0 :
                                        (((double)$sql->faktur_9 - (double)$sql->faktur_lalu_9) / (double)$sql->faktur_lalu_9) * 100,
                        'kontribusi'=> [
                            'selected'  => ((double)$sql->faktur_9 <= 0 || (double)$sql->faktur_total <= 0) ? 0 : ((double)$sql->faktur_9 / (double)$sql->faktur_total) * 100,
                            'previous'  => ((double)$sql->faktur_lalu_9 <= 0 || (double)$sql->faktur_lalu_total <= 0) ? 0 : ((double)$sql->faktur_lalu_9 / (double)$sql->faktur_lalu_total) * 100,
                        ]
                    ], [
                        'month'     => 'Oktober',
                        'selected'  => (double)$sql->faktur_10,
                        'previous'  => (double)$sql->faktur_lalu_10,
                        'growth'    => ((double)$sql->faktur_10 <= 0 || (double)$sql->faktur_lalu_10 <= 0) ? 0 :
                                        (((double)$sql->faktur_10 - (double)$sql->faktur_lalu_10) / (double)$sql->faktur_lalu_10) * 100,
                        'kontribusi'=> [
                            'selected'  => ((double)$sql->faktur_10 <= 0 || (double)$sql->faktur_total <= 0) ? 0 : ((double)$sql->faktur_10 / (double)$sql->faktur_total) * 100,
                            'previous'  => ((double)$sql->faktur_lalu_10 <= 0 || (double)$sql->faktur_lalu_total <= 0) ? 0 : ((double)$sql->faktur_lalu_10 / (double)$sql->faktur_lalu_total) * 100,
                        ]
                    ], [
                        'month'     => 'November',
                        'selected'  => (double)$sql->faktur_11,
                        'previous'  => (double)$sql->faktur_lalu_11,
                        'growth'    => ((double)$sql->faktur_11 <= 0 || (double)$sql->faktur_lalu_11 <= 0) ? 0 :
                                        (((double)$sql->faktur_11 - (double)$sql->faktur_lalu_11) / (double)$sql->faktur_lalu_11) * 100,
                        'kontribusi'=> [
                            'selected'  => ((double)$sql->faktur_11 <= 0 || (double)$sql->faktur_total <= 0) ? 0 : ((double)$sql->faktur_11 / (double)$sql->faktur_total) * 100,
                            'previous'  => ((double)$sql->faktur_lalu_11 <= 0 || (double)$sql->faktur_lalu_total <= 0) ? 0 : ((double)$sql->faktur_lalu_11 / (double)$sql->faktur_lalu_total) * 100,
                        ]
                    ], [
                        'month'     => 'Desember',
                        'selected'  => (double)$sql->faktur_12,
                        'previous'  => (double)$sql->faktur_lalu_12,
                        'growth'    => ((double)$sql->faktur_12 <= 0 || (double)$sql->faktur_lalu_12 <= 0) ? 0 :
                                        (((double)$sql->faktur_12 - (double)$sql->faktur_lalu_12) / (double)$sql->faktur_lalu_12) * 100,
                        'kontribusi'=> [
                            'selected'  => ((double)$sql->faktur_12 <= 0 || (double)$sql->faktur_total <= 0) ? 0 : ((double)$sql->faktur_12 / (double)$sql->faktur_total) * 100,
                            'previous'  => ((double)$sql->faktur_lalu_12 <= 0 || (double)$sql->faktur_lalu_total <= 0) ? 0 : ((double)$sql->faktur_lalu_12 / (double)$sql->faktur_lalu_total) * 100,
                        ]
                    ],
                ],
            ]);

           return Response::responseSuccess('success', $dashboard_penjualan_kuartal->first());
        } catch (\Exception $exception) {
            return Response::responseError(
                $request->get('user_id'), 'API', Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid')
            );
        }
    }
}
