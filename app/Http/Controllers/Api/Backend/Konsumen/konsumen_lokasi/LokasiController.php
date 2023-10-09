<?php

namespace app\Http\Controllers\Api\Backend\Konsumen\Konsumen_lokasi;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class LokasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
                $compeny_default = DB::table(DB::raw('dbhonda.dbo.kode_company'))->where('kd_fdr', $request->companyid)->orWhere('kd_honda', $request->companyid)->first();    
                
                // ! jika dia efo maka hanya menyimpan data fdr tidak kd lokasi KB
                $fdr_kd_lokasi = DB::table(DB::raw('dbsuma.dbo.lokasi'))->where('CompanyId', strtoupper(trim($compeny_default->kd_fdr)));
                if(Str::contains(trim($request->username), 'EFO')) {
                    $fdr_kd_lokasi = $fdr_kd_lokasi->whereNotIn('kd_lokasi', ['KB']);
                } else {
                    $fdr_kd_lokasi = $fdr_kd_lokasi->whereNotIn('kd_lokasi', ['EFO', 'MO1']);
                }
                $fdr_kd_lokasi = $fdr_kd_lokasi->pluck('kd_lokasi')->toArray();

                // ! jika dia efo maka hanya menyimpan data fdr
                $company = new Collection();
                if(Str::contains(trim($request->username), 'EFO')) {
                    $company->put('fdr', (object)[
                        'divisi'    => 'fdr',
                        'lokasi'    => (array)[
                            strtoupper(trim($compeny_default->kd_fdr)) => (object)[
                                'companyid' => strtoupper(trim($compeny_default->kd_fdr)),
                                'kd_lokasi' => $fdr_kd_lokasi
                                ]
                        ]
                    ]);
                    $company->put('lokasi_valid', (object)[
                        'divisi'    => ['fdr'],
                        'companyid' => [strtoupper(trim($compeny_default->kd_fdr))],
                        'kd_lokasi' => $fdr_kd_lokasi,
                    ]);

                    // ! Jika Bukan efo maka menyimpan data honda dan fdr
                } else {
                    // ! jika bukan MD_H3_MGMT maka hanya bisa mengunakan lokasi default
                    // if(strtoupper(trim($request->role_id)) != 'MD_H3_MGMT'){ ganti jika tidak MD_H3_MGMT atau MD_H3_SPV_PC gunakan !in_array
                    if(!in_array(strtoupper(trim($request->role_id)), ['MD_H3_MGMT', 'MD_H3_SPV_PC'])) {

                        $company->put('honda', (object)[
                            'divisi'    => 'honda',
                            'lokasi'    => (array)[
                                strtoupper(trim($compeny_default->kd_honda)) => (object)[
                                            'companyid' => strtoupper(trim($compeny_default->kd_honda)),
                                            'kd_lokasi' => DB::table(DB::raw('dbhonda.dbo.lokasi'))->where('CompanyId', strtoupper(trim($compeny_default->kd_honda)))->pluck('kd_lokasi')->toArray()
                                ]
                            ]
                        ]);
                        $company->put('fdr', (object)[
                            'divisi'    => 'fdr',
                            'lokasi'    => (array)[
                                strtoupper(trim($compeny_default->kd_fdr)) => (object)[
                                    'companyid' => strtoupper(trim($compeny_default->kd_fdr)),
                                    'kd_lokasi' => $fdr_kd_lokasi
                                    ]
                            ]
                        ]);
                        $company->put('lokasi_valid', (object)[
                            'divisi'    => ['honda','fdr'],
                            'companyid' => [strtoupper(trim($compeny_default->kd_honda)),strtoupper(trim($compeny_default->kd_fdr))],
                            'kd_lokasi' => array_merge(DB::table(DB::raw('dbhonda.dbo.lokasi'))->where('CompanyId', strtoupper(trim($compeny_default->kd_honda)))->pluck('kd_lokasi')->toArray(), $fdr_kd_lokasi),
                        ]);
                    } 
                    else {
                        $data = DB::table('kode_company')
                        ->select('kd_honda', 'kd_fdr')
                        ->get();
                        $honda = [];
                        $fdr = [];
                        foreach($data as $key => $value) {
                            $honda[strtoupper(trim($value->kd_honda))] = (object)[
                                'companyid' => strtoupper(trim($value->kd_honda)),
                                'kd_lokasi' => DB::table(DB::raw('dbhonda.dbo.lokasi'))->where('CompanyId', strtoupper(trim($value->kd_honda)))->pluck('kd_lokasi')->toArray(),
                            ];

                            $fdr[strtoupper(trim($value->kd_fdr))] = (object)[
                                'companyid' => strtoupper(trim($value->kd_fdr)),
                                'kd_lokasi' => DB::table(DB::raw('dbsuma.dbo.lokasi'))->where('CompanyId', strtoupper(trim($value->kd_fdr)))->pluck('kd_lokasi')->toArray(),
                            ];
                        }

                        $company->put('honda', (object)[
                            'divisi'    => 'honda',
                            'lokasi'    => $honda
                        ]);
                        $company->put('fdr', (object)[
                            'divisi'    => 'fdr',
                            'lokasi'    => $fdr
                        ]);

                        $company->put('lokasi_valid', (object)[
                            'divisi'    => ['honda','fdr'],
                            'companyid' => array_merge(collect($honda)->pluck('companyid')->toArray(),collect($fdr)->pluck('companyid')->toArray()),
                            'kd_lokasi' => array_merge(collect($honda)->pluck('kd_lokasi')->flatten()->all(), collect($fdr)->pluck('kd_lokasi')->flatten()->all()),
                        ]);

                    }
                }
            
            return Response::responseSuccess('success', $company);
        } catch (\Throwable $exception) {
            return Response::responseError($request->get('user_id'), 'API', route::getCurrentRoute()->action['controller'],
                        $request->route()->getActionMethod(), $exception->getMessage(), $request->get('companyid'));
        }
    }
}
