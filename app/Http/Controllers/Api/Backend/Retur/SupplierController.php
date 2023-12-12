<?php

namespace app\Http\Controllers\Api\Backend\Retur;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $rules = [
                'companyid' => 'required',
                'user_id' => 'required',
                'option' => 'required',
            ];
            $messages = [
                'companyid.required' => 'Companyid Tidak Boleh Kososng',
                'user_id.required' => 'User Id Tidak Boleh Kososng',
                'option.required' => 'Option Tidak Boleh Kososng',
            ];

            if (!empty($request->no_retur)) {
                $rules += [
                    'no_retur' => 'required|min:2',
                ];
                $messages += [
                    'no_retur.required' => 'Nomor Retur tidak boleh kosong',
                    'no_retur.min' => 'Nomor Retur Minimal 2 Karakter',
                ];
            }

            $validate = Validator::make($request->all(), $rules, $messages);
            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            if (empty($request->no_retur) && in_array('tamp', $request->option)) {
                $request->merge(['no_retur' => $request->user_id]);
            }

            if (!in_array($request->per_page, [10, 50, 100, 500])) {
                $request->replace(['per_page' => 10]);
            }


            $request->merge(['tb' => ['retur', 'retur_dtl']]);
            if (in_array('tamp', $request->option)) {
                $request->merge(['tb' => ['returtmp', 'retur_dtltmp']]);
            }

            $data = DB::table(function ($query) use ($request) {
                $query->select('*')
                    ->from($request->tb[0] . ' as retur')
                    ->where('retur.CompanyId', $request->companyid);
                if (in_array('tamp', $request->option)) {
                    $query->where('retur.Kd_Key', $request->user_id);
                }
            }, 'retur');

            // ! Data untuk Pagination
            if (in_array('page', $request->option)) {
                $data = $data
                    ->select(
                        'retur.no_retur',
                        'retur.tglretur',
                        'retur.kd_supp',
                        'retur.total'
                    );

                if (in_array('with_detail', $request->option)) {
                    $data = $data->join($request->tb[1] . ' as retur_dtl', function ($join) {
                        $join->on('retur_dtl.no_retur', '=', 'retur.no_retur')
                            ->on('retur_dtl.CompanyId', '=', 'retur.CompanyId');
                    });
                }

                if (!empty($request->no_retur)) {
                    $data = $data->where(function ($query) use ($request) {
                        $query->where('retur.no_retur', 'like', '%' . $request->no_retur . '%')
                            ->orWhere('retur.kd_supp', 'like', '%' . $request->no_retur . '%')
                            ->orWhere('retur_dtl.kd_part', 'like', '%' . $request->no_retur . '%')
                            ->orWhere('retur_dtl.no_klaim', 'like', '%' . $request->no_retur . '%');
                    });
                }

                $data = $data
                    // ! tampilkan hanya 2 tahun terakhir
                    ->whereBetween('retur.tglretur', [date('Y-m-d', strtotime('-2 year')), date('Y-m-d')])
                    ->groupBy('retur.no_retur', 'retur.tglretur', 'retur.kd_supp', 'retur.total')
                    ->orderBy('tglretur', 'desc')
                    ->orderBy('retur.no_retur', 'desc')
                    ->paginate($request->per_page);

                $dataQtyJwb = DB::table('jwb_claim')
                    ->select(
                        'no_retur',
                        DB::raw('sum(isnull(qty_jwb,0)) as qty_jwb')
                    )
                    ->whereIn('no_retur', collect($data->items())->pluck('no_retur')->toArray())
                    ->where('CompanyId', $request->companyid)
                    ->groupBy('no_retur')
                    ->get();

                $dataDetail = DB::table($request->tb[1])
                    ->select(
                        'no_retur',
                        'no_klaim',
                        'kd_part'
                    )
                    ->whereIn('no_retur', collect($data->items())->pluck('no_retur')->toArray())
                    ->where('CompanyId', $request->companyid)
                    ->groupBy('no_retur', 'no_klaim', 'kd_part')
                    ->get();

                collect($data->items())->map(function ($item) use ($dataDetail, $dataQtyJwb) {
                    $item->qty_jwb = collect($dataQtyJwb)
                        ->where('no_retur', $item->no_retur)
                        ->pluck('qty_jwb')
                        ->first();
                    $item->detail = $dataDetail
                        ->where('no_retur', $item->no_retur)
                        ->values();
                    return $item;
                });

                // ! Pemangilan 1 data
            } elseif (in_array('first', $request->option)) {
                $data = $data->first();


                // ! Pemnagilan 1 data dengan detail
            } elseif (in_array('with_detail', $request->option) || in_array('with_jwb', $request->option)) {
                if (!empty($request->no_retur) && in_array('with_jwb', $request->option)) {
                    $data = $data->where('retur.no_retur', $request->no_retur);
                }
                $data = $data->first();
                if (!empty($data)) {
                    $data_detail = DB::table(function ($query) use ($request) {
                            $query->select(
                                'no_klaim',
                                'tgl_claim',
                                'no_produksi',
                                'no_ps_klaim',
                                'kd_dealer',
                                'kd_part',
                                'kd_lokasi',
                                'jmlretur',
                                'qty_jwb',
                                'ket',
                                'ket_jwb',
                                'diterima',
                                'CompanyId',
                                'usertime',
                            )
                            ->from($request->tb[1] . ' as retur_dtl')
                            ->where('retur_dtl.CompanyId', $request->companyid);
                        if (!empty($request->no_retur) || in_array($request->option, ['tamp'])) {
                            $query->where('retur_dtl.no_retur', $request->no_retur);
                        }
                    }, 'retur_dtl')
                        ->leftJoinSub(function ($query) use ($request) {
                            $query->select('part.kd_part', 'part.ket', 'part.CompanyId', 'hrg_pokok','het')
                                ->from('part')
                                ->where('part.CompanyId', $request->companyid);
                        }, 'part', function ($join) {
                        $join->on('part.kd_part', '=', 'retur_dtl.kd_part')
                            ->on('part.CompanyId', '=', 'retur_dtl.CompanyId');
                    })
                    ->leftJoinSub(function ($query) use ($request) {
                        $query->select(
                                'rtoko_dtl.no_retur',
                                'rtoko_dtl.kd_part',
                                'rtoko_dtl.no_klaim',
                                'rtoko_dtl.ket',
                                'rtoko_dtl.status_end',
                                'rtoko_dtl.CompanyId'
                            )
                            ->from('rtoko_dtl')
                            ->where('rtoko_dtl.CompanyId', $request->companyid)
                            ->groupBy(
                                'rtoko_dtl.no_retur',
                                'rtoko_dtl.kd_part',
                                'rtoko_dtl.no_klaim',
                                'rtoko_dtl.ket',
                                'rtoko_dtl.status_end',
                                'rtoko_dtl.CompanyId'
                            );
                    }, 'rtoko_dtl', function ($join) {
                    $join->on('rtoko_dtl.no_retur', '=', 'retur_dtl.no_klaim')
                            ->on('rtoko_dtl.kd_part', '=', 'retur_dtl.kd_part')
                            ->on('rtoko_dtl.CompanyId', '=', 'retur_dtl.CompanyId');
                    })
                    ->select(
                        'retur_dtl.*',
                        'part.ket as nm_part',
                        'part.het',
                        'rtoko_dtl.ket as ket_klaim',
                        'rtoko_dtl.status_end'
                    )
                    ->orderBy('retur_dtl.usertime', 'asc')
                    ->get();

                        // ! jika ada data detail dengan Jawaban
                    if (in_array('with_jwb', $request->option)) {
                        $detail_jwb = DB::table('jwb_claim')
                        ->select(
                            '*'
                        )
                        ->where('jwb_claim.no_retur', $request->no_retur)
                        ->whereIn('jwb_claim.no_klaim', collect($data_detail)->pluck('no_klaim')->toArray())
                        ->whereIn('jwb_claim.kd_part', collect($data_detail)->pluck('kd_part')->toArray())
                        ->where('jwb_claim.CompanyId', $request->companyid)
                        ->orderBy('no_jwb', 'asc')
                        ->get();

                        foreach ($data_detail as $key => $value) {
                            $data_detail[$key]->detail_jwb = collect($detail_jwb)
                                ->where('no_klaim', $value->no_klaim)
                                ->where('kd_part', $value->kd_part)
                                ->values();
                            $data_detail[$key]->qty_jwb = collect($data_detail[$key]->detail_jwb)->where('sts_end', 1)->sum('qty_jwb');
                            $data_detail[$key]->ket_jwb =
                                collect($data_detail[$key]->detail_jwb)->where('keputusan', 'TERIMA')->where('sts_end', 1)->sum('qty_jwb') . ' TERIMA ' .
                                collect($data_detail[$key]->detail_jwb)->where('keputusan', 'TOLAK')->where('sts_end', 1)->sum('qty_jwb') . ' TOLAK ';
                        }
                    }

                    $dataProduksi =
                    DB::table('klaim_dtl')
                    ->select(
                        DB::raw('LTRIM(RTRIM(rtoko_dtl.no_retur)) as no_retur'),
                        DB::raw('LTRIM(RTRIM(klaim_dtl.kd_part)) as kd_part'),
                        DB::raw('LTRIM(RTRIM(klaim_dtl.no_produksi)) as no_produksi')
                    )
                    ->join('rtoko_dtl', function ($join) {
                        $join->on('rtoko_dtl.no_klaim', '=', 'klaim_dtl.no_dokumen')
                            ->on('rtoko_dtl.kd_part', '=', 'klaim_dtl.kd_part')
                            ->on('rtoko_dtl.CompanyId', '=', 'klaim_dtl.CompanyId');
                    })
                    ->whereIn('rtoko_dtl.no_retur', collect($data_detail)->pluck('no_klaim')->toArray())
                    ->whereIn('rtoko_dtl.kd_part', collect($data_detail)->pluck('kd_part')->toArray())
                    ->where('klaim_dtl.sts_klaim', 1)
                    ->where('rtoko_dtl.CompanyId', $request->companyid)
                    ->get();

                    $dataProduksi = collect($dataProduksi)->groupBy('no_retur')->map(function ($item) {
                        return collect($item)->groupBy('kd_part')->map(function ($item) {
                            return collect($item)->pluck('no_produksi')->toArray();
                        });
                    });

                    foreach ($data_detail as $key => $value) {
                        $data_detail[$key]->no_produksi_list = $dataProduksi[$value->no_klaim][$value->kd_part] ?? [];
                    }

                    $data->detail = $data_detail;
                }
            }
            return Response::responseSuccess('success', $data);
        } catch (\Exception $exception) {
            return Response::responseError(
                $request->get('user_id'),
                'API',
                route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $exception->getMessage(),
                $request->get('companyid')
            );
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
        try {
            $rules = [
                'companyid' => 'required',
                'user_id' => 'required',
            ];
            $messages = [
                'companyid.required' => 'Companyid Tidak Boleh Kososng',
                'user_id.required' => 'User Id Tidak Boleh Kososng',
            ];

            // ! ------------------------------------
            // ! Jika menambahkan validasi
            if ($request->no_retur == $request->user_id) {
                $rules += [
                    'kd_supp' => 'required',
                    'tgl_retur' => 'required',
                ];
                $messages += [
                    'kd_supp.required' => 'Kode Supplier Tidak Boleh Kososng',
                    'tgl_retur.required' => 'Tanggal Retur Tidak Boleh Kososng',
                ];
                if (!empty($request->kd_part)) {
                    $rules += [
                        'no_klaim' => 'required',
                    ];
                    $messages += [
                        'no_klaim.required' => 'No Klaim Tidak Boleh Kososng',
                    ];
                }
            }

            // ! megecek validasi dan menampilkan pesan error
            // ! --------------------------------------------
            $validate = Validator::make($request->all(), $rules, $messages);
            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            $simpan = DB::transaction(function () use ($request) {
                // ! ======================================================
                // ! Simpan Data Tamporeri
                // ! ======================================================
                if ($request->no_retur == $request->user_id) {
                    if (!empty($request->kd_part)) {
                        // ! cek apakah sudah pernah di klaim
                        $a = DB::table(function ($query) use ($request) {
                            $query->select(
                                    'rtoko.no_retur',
                                    'rtoko.kd_dealer',
                                    'rtoko_dtl.kd_part',
                                    'rtoko_dtl.Kd_lokasi',
                                    'rtoko_dtl.jumlah',
                                    'rtoko.tanggal'
                                )
                                ->from('rtoko')
                                ->where('rtoko.no_retur', $request->no_klaim)
                                ->where('rtoko.CompanyId', $request->companyid)
                                ->JoinSub(function ($query) use ($request) {
                                    $query->select('*')
                                        ->from('rtoko_dtl')
                                        ->where('rtoko_dtl.no_retur', $request->no_klaim)
                                        ->where('rtoko_dtl.kd_part', $request->kd_part)
                                        ->where('rtoko_dtl.CompanyId', $request->companyid);
                                }, 'rtoko_dtl', function ($join) {
                                    $join->on('rtoko_dtl.no_retur', '=', 'rtoko.no_retur');
                                });
                            }, 'rtoko')
                            ->joinSub(function ($query) use ($request) {
                                $query->select('kd_part', 'kd_suppa')
                                    ->from('part')
                                    ->where('kd_part', $request->kd_part)
                                    ->where('CompanyId', $request->companyid);
                            }, 'part', function ($join) {
                                $join->on('part.kd_part', '=', 'rtoko.kd_part');
                            })
                            ->first();

                        // ! Cek pastikan part yang di klaim memiliki kd_supp yang sama
                        $tampRetur = DB::table(function ($query) use ($request) {
                            $query->select(
                                    'retur_dtltmp.no_retur',
                                    'retur_dtltmp.kd_part'
                                )
                                ->from('retur_dtltmp')
                                ->where('retur_dtltmp.no_retur', $request->user_id)
                                ->where('retur_dtltmp.CompanyId', $request->companyid);
                            }, 'retur')
                            ->leftJoinSub(function ($query) use ($request) {
                                $query->select('kd_part', 'kd_suppa')
                                    ->from('part')
                                    ->where('part.CompanyId', $request->companyid);
                            }, 'part', function ($join) {
                                $join->on('part.kd_part', '=', 'retur.kd_part');
                            })
                            ->first();

                        if ($a->kd_suppa != ($tampRetur->kd_suppa ?? $request->kd_supp)) {
                            return (object) [
                                'status' => 0,
                                'data' => 'Supplier harus sama pada 1 dokumen, Part yang anda simpan memiliki supplier : <b>' . $a->kd_suppa . '</b> sedangkan supplier pada dokumen : <b>' . ($tampRetur->kd_suppa ?? $request->kd_supp) . '</b>'
                            ];
                        }

                        //! ubah status pada rtoko_dtl menjadi 1 dimana agar tidak bisa di klaim lagi
                        DB::table('rtoko_dtl')
                            ->where('rtoko_dtl.no_retur', $request->no_klaim)
                            ->where('rtoko_dtl.kd_part', $request->kd_part)
                            ->where('rtoko_dtl.CompanyId', $request->companyid)
                            ->update([
                                'status' => 1
                            ]);

                        //! simpan pada tabel retur_dtltmp
                        DB::table('retur_dtltmp')
                            ->updateOrInsert([
                                'retur_dtltmp.Kd_Key' => $request->user_id,
                                'retur_dtltmp.no_retur' => $request->user_id,
                                'retur_dtltmp.CompanyId' => $request->companyid,
                                'retur_dtltmp.no_klaim' => $request->no_klaim,
                                'retur_dtltmp.kd_part' => $request->kd_part,
                            ], [
                                'kd_dealer' => $a->kd_dealer,
                                'no_ps_klaim' => $request->no_ps,
                                'kd_lokasi' => $a->Kd_lokasi,
                                'jmlretur' => $a->jumlah,
                                'ket' => ($request->ket ?? null),
                                'diterima' => ($request->diterima ?? 0),
                                'no_produksi' => ($request->no_produksi ?? null),
                                'tgl_pemakaian' => $request->tgl_pemakaian,
                                'tgl_claim' => $a->tanggal,
                                'usertime' => (date('Y-m-d H:i:s') . '=' . $request->user_id)
                            ]);
                    }

                    $b = DB::table(function ($query) use ($request) {
                        $query
                            ->select(
                                    'retur_dtltmp.Kd_Key',
                                    'retur_dtltmp.no_retur',
                                    DB::raw('isnull(sum(retur_dtltmp.jmlretur), 0) as total')
                            )
                            ->from('retur_dtltmp')
                            ->where('retur_dtltmp.no_retur', $request->user_id)
                            ->where('retur_dtltmp.CompanyId', $request->companyid)
                            ->groupBy('retur_dtltmp.Kd_Key', 'retur_dtltmp.no_retur');
                    }, 'b')
                        ->first();

                    //! simpan pada tabel returtmp
                    DB::table('returtmp')
                        ->updateOrInsert([
                            'returtmp.Kd_Key' => $request->user_id,
                            'returtmp.no_retur' => $request->user_id,
                            'returtmp.CompanyId' => $request->companyid,
                        ], [
                            'Kd_supp' => ($tampRetur->kd_suppa ?? $request->kd_supp),
                            'tglretur' => $request->tgl_retur,
                            'total' => $b->total ?? 0,
                            'sts_jurnal' => ($request->sts_jurnal ?? 0),
                            'usertime' => (date('Y-m-d H:i:s') . '=' . $request->user_id)
                        ]);

                    return (object) [
                        'status' => 1,
                        'data' => ''
                    ];
                }

                $cekSupplierPart = DB::table(function ($query) use ($request){
                    $query->select(
                        'returtmp.kd_supp',
                        'retur_dtltmp.no_klaim',
                        'retur_dtltmp.kd_part',
                        'returtmp.companyid'
                    )
                    ->from('returtmp')
                    ->join('retur_dtltmp', function ($join) {
                        $join->on('retur_dtltmp.no_retur', '=', 'returtmp.no_retur')
                            ->on('retur_dtltmp.CompanyId', '=', 'returtmp.CompanyId');
                    })
                    ->where('returtmp.Kd_Key', $request->user_id)
                    ->where('returtmp.CompanyId', $request->companyid);
                }, 'retur')
                ->join('part', function ($join) {
                    $join->on('part.kd_part', '=', 'retur.kd_part')
                        ->on('part.CompanyId', '=', 'retur.CompanyId');
                })
                ->select(
                    'retur.no_klaim',
                    'retur.kd_supp as supp_retur',
                    'retur.kd_part as part_retur',
                    'part.kd_suppa as supp_part',
                    'part.kd_part as part_part'
                )
                ->whereRaw('retur.kd_supp <> part.kd_suppa')
                ->get();

                if (count($cekSupplierPart) > 0) {
                    return (object) [
                        'status' => 0,
                        'data' => 'Part yang di retur tidak sesuai dengan supplier yang di pilih <b>'. collect($cekSupplierPart)->pluck('supp_retur')->first()
                        .'</b><br>
                        <table class="table table-row-dashed table-row-gray-300 align-middle border mt-3">
                            <thead class="border">
                                <tr class="fs-8 fw-bolder text-muted text-center">
                                    <th>No Klaim</th>
                                    <th>Part Retur</th>
                                    <th>Supplier</th>
                                </tr>
                            </thead>
                            <tbody>
                                ' . collect($cekSupplierPart)->map(function ($item) {
                                return '
                                <tr class="fw-bolder fs-8 border">
                                    <td>' . $item->no_klaim . '</td>
                                    <td>' . $item->part_retur . '</td>
                                    <td>' . $item->supp_part . '</td>
                                </tr>';
                            })->implode('').'
                            </tbody>
                        </table>'
                    ];
                }

                // ! ======================================================
                // ! Simpan Data
                // ! ======================================================
                $simpan = DB::select("
                SET NOCOUNT ON;
                exec SP_Retur_Simpan1 ?, ?, ?", [
                    date('d-m-Y', strtotime($request->tgl_retur)),
                    $request->companyid,
                    $request->user_id
                ]);
                return (object) [
                    'status' => (int) $simpan[0]->status,
                    'data' => $simpan[0]->data
                ];
            });

            // ! jika true succes jika false terdapat validasi yang gagal
            if ($simpan->status == 1) {
                return Response::responseSuccess('success', $simpan->data);
            } elseif ($simpan->status == 0) {
                return Response::responseWarning($simpan->data, '');
            }
        } catch (\Exception $exception) {
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $exception->getMessage(),
                $request->get('companyid')
            );
        }
    }

    public function destroy(Request $request)
    {
        try {
            $rules = [
                'no_klaim' => 'required',
                'kd_part' => 'required',
            ];
            $messages = [
                'no_klaim.required' => 'No Klaim Tidak Boleh Kososng',
                'kd_part.required' => 'Kode Part Tidak Boleh Kososng',
            ];

            // ! ------------------------------------
            // ! megecek validasi dan menampilkan pesan error
            // ! ------------------------------------
            $validate = Validator::make($request->all(), $rules, $messages);
            if ($validate->fails()) {
                return Response::responseWarning($validate->errors()->first());
            }

            DB::transaction(function () use ($request) {
                DB::table('retur_dtltmp')
                    ->where('no_retur', $request->user_id)
                    ->where('no_klaim', $request->no_klaim)
                    ->where('kd_part', $request->kd_part)
                    ->where('CompanyId', $request->companyid)
                    ->delete();

                DB::table('rtoko_dtl')
                    ->where('no_retur', $request->no_klaim)
                    ->where('kd_part', $request->kd_part)
                    ->where('CompanyId', $request->companyid)
                    ->update([
                        'status' => 0
                    ]);

                //! cek apakah sudah tidak ada data pada retur_dtltmp jika tidak hapus data pada returtmp
                $cek = DB::table('retur_dtltmp')
                    ->where('no_retur', $request->user_id)
                    ->where('CompanyId', $request->companyid)
                    ->first();

                if (empty($cek)) {
                    DB::table('returtmp')
                        ->where('no_retur', $request->user_id)
                        ->where('CompanyId', $request->companyid)
                        ->delete();
                }
            });
            return response::responseSuccess('success', '');
        } catch (\Exception $exception) {
            return Response::responseError(
                $request->get('user_id'),
                'API',
                Route::getCurrentRoute()->action['controller'],
                $request->route()->getActionMethod(),
                $exception->getMessage(),
                $request->get('companyid')
            );
        };
    }
}
