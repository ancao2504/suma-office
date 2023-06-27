<?php

namespace App\Http\Controllers\Api\Backend\Setting;

use Illuminate\Http\Request;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;


class ApiSettingCetakUlangController extends Controller
{
    public function daftarCetakUlang(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'year'      => 'required',
                'month'     => 'required',
                'page'      => 'required',
                'per_page'  => 'required',
                'role_id'   => 'required'
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Tahun, bulan, page, per-page, role id, user id harus terisi");
            }

            if (strtoupper(trim($request->get('role_id'))) != 'MD_H3_MGMT') {
                if (strtoupper(trim($request->get('role_id'))) != 'MD_H3_FIN') {
                    return Response::responseWarning('Anda tidak memiliki akses untuk membuka halaman ini');
                }
            }

            $honda = DB::table("dbhonda.dbo.edit_transaksi")->lock('with (nolock)')
                ->selectRaw("'HONDA' as divisi, isnull(edit_transaksi.no_transaksi, '') as no_faktur,
                            isnull(edit_transaksi.kd_cabang, '') as kode_cabang,
                            isnull(cabang.nm_cabang, '') as nama_cabang,
                            isnull(edit_transaksi.tanggal, '') as tanggal,
                            isnull(edit_transaksi.jenis, '') as jenis,
                            isnull(edit_transaksi.alasan, '') as alasan,
                            isnull(edit_transaksi.jml_edit, 0) as jml_edit,
                            isnull(edit_transaksi.companyid, '') as companyid,
                            isnull(edit_transaksi.usertime, '') as usertime,
                            isnull(edit_transaksi.time, '') as time")
                ->leftJoin(DB::raw('cabang with (nolock)'), function ($join) {
                    $join->on('cabang.kd_cabang', '=', 'edit_transaksi.kd_cabang')
                        ->on('cabang.companyid', '=', DB::raw("'PD'"));
                })
                ->whereYear('edit_transaksi.tanggal', $request->get('year'))
                ->whereMonth('edit_transaksi.tanggal', $request->get('month'));

            $fdr = DB::table("dbsuma.dbo.edit_transaksi")->lock('with (nolock)')
                ->selectRaw("'FDR' as divisi, isnull(edit_transaksi.no_transaksi, '') as no_faktur,
                            isnull(edit_transaksi.kd_cabang, '') as kode_cabang,
                            isnull(cabang.nm_cabang, '') as nama_cabang,
                            isnull(edit_transaksi.tanggal, '') as tanggal,
                            isnull(edit_transaksi.jenis, '') as jenis,
                            isnull(edit_transaksi.alasan, '') as alasan,
                            isnull(edit_transaksi.jml_edit, 0) as jml_edit,
                            isnull(edit_transaksi.companyid, '') as companyid,
                            isnull(edit_transaksi.usertime, '') as usertime,
                            isnull(edit_transaksi.time, '') as time")
                ->leftJoin(DB::raw('cabang with (nolock)'), function ($join) {
                    $join->on('cabang.kd_cabang', '=', 'edit_transaksi.kd_cabang')
                        ->on('cabang.companyid', '=', DB::raw("'PE'"));
                })
                ->whereYear('edit_transaksi.tanggal', $request->get('year'))
                ->whereMonth('edit_transaksi.tanggal', $request->get('month'));

            $combined = $honda->union($fdr);

            $data = DB::query()->fromSub($combined, 'cetakulang')->orderBy('time', 'desc');

            $result = $data->paginate($request->get('per_page'));

            return Response::responseSuccess('success', $result);
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

    public function cekNomorDokumen(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'divisi'            => 'required',
                'jenis_transaksi'   => 'required',
                'no_dokumen'        => 'required'
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Pilih divisi, jenis dokumen dan isi nomor dokumen terlebih dahulu");
            }

            if (strtoupper(trim($request->get('role_id'))) != 'MD_H3_MGMT') {
                if (strtoupper(trim($request->get('role_id'))) != 'MD_H3_FIN') {
                    return Response::responseWarning('Anda tidak memiliki akses untuk membuka halaman ini');
                }
            }

            $database = '';
            if (strtoupper(trim($request->get('divisi'))) == 'HONDA') {
                $database = 'dbhonda';
            } else {
                $database = 'dbsuma';
            }

            if (strtoupper(trim($request->get('jenis_transaksi'))) == 'FAKTUR') {
                if (strlen(strtoupper($request->get('no_dokumen'))) == 7) {
                    $sql = DB::table($database . '.dbo.faktur')->lock('with (nolock)')
                        ->selectRaw("isnull(faktur.no_faktur, '') as nomor_faktur")
                        ->leftJoin(DB::raw($database . '.dbo.stsclose' . ' with (nolock)'), function ($join) {
                            $join->on('stsclose.companyid', '=', 'faktur.companyid');
                        })
                        ->whereRaw('faktur.tgl_faktur > stsclose.close_mkr')
                        ->where('faktur.no_faktur', 'like', strtoupper($request->get('no_dokumen')) . '%')
                        ->first();
                } else {
                    $sql = DB::table($database . '.dbo.faktur')->lock('with (nolock)')
                        ->selectRaw("isnull(faktur.no_faktur, '') as nomor_faktur")
                        ->where('faktur.no_faktur', strtoupper($request->get('no_dokumen')))
                        ->first();
                }

                if (empty($sql->nomor_faktur)) {
                    return Response::responseWarning('Nomor faktur tidak ditemukan');
                } else {
                    $nomor_faktur = strtoupper(trim($sql->nomor_faktur));

                    $sql = DB::table($database . '.dbo.faktur')->lock('with (nolock)')
                        ->selectRaw("isnull(faktur.no_faktur, '') as nomor_faktur,
                                        isnull(faktur.kd_sales, '') as kode_sales,
                                        isnull(faktur.kd_dealer, '') as kode_dealer,
                                        isnull(faktur.ket, '') as keterangan,
                                        isnull(dealer.nm_dealer, '') as nama_dealer,
                                        isnull(company.kd_cabang, '') as kode_cabang,
                                        isnull(lokasi.kd_faktur, '') as kode_faktur_default,
                                        isnull(faktur.companyid, '') as companyid,
                                        isnull(company.inisial, 0) as kantor_pusat")
                        ->leftJoin(DB::raw($database . '.dbo.company' . ' with (nolock)'), function ($join) {
                            $join->on('company.companyid', '=', 'faktur.companyid');
                        })
                        ->leftJoin(DB::raw($database . '.dbo.dealer' . ' with (nolock)'), function ($join) {
                            $join->on('dealer.kd_dealer', '=', 'faktur.kd_dealer')
                                ->on('dealer.companyid', '=', 'faktur.companyid');
                        })
                        ->leftJoin(DB::raw($database . '.dbo.lokasi' . ' with (nolock)'), function ($join) {
                            $join->on('lokasi.kd_lokasi', '=', 'company.kd_lokasi')
                                ->on('lokasi.companyid', '=', 'faktur.companyid');
                        })
                        ->where('faktur.no_faktur', strtoupper($nomor_faktur))
                        ->first();

                    if (empty($sql->nomor_faktur)) {
                        return Response::responseWarning('Data faktur tidak ditemukan');
                    } else {
                        $informasi = '(SALESMAN: ' . strtoupper(trim($sql->kode_sales)) . '), ' . '(DEALER: ' . strtoupper(trim($sql->kode_dealer)) . ' - ' . trim($sql->nama_dealer) . ')';
                        if (trim($sql->keterangan) != '') {
                            $informasi .= ', (KETERANGAN: ' . trim($sql->keterangan) . ')';
                        }

                        $status_approve = 0;
                        if (substr(strtoupper(trim($sql->nomor_faktur)), 0, 2) != strtoupper(trim($sql->kode_faktur_default))) {
                            $status_approve = 1;
                        }

                        $data = [
                            'no_transaksi'  => strtoupper(trim($sql->nomor_faktur)),
                            'companyid'     => strtoupper(trim($sql->companyid)),
                            'kode_cabang'   => strtoupper(trim($sql->kode_cabang)),
                            'informasi'     => $informasi,
                            'status_approve' => (int)$status_approve,
                            'kantor_pusat'  => (int)$sql->kantor_pusat,
                        ];
                        return Response::responseSuccess('success', $data);
                    }
                }
            } elseif (strtoupper(trim($request->get('jenis_transaksi'))) == 'PEMINDAHAN') {
                if (strlen(strtoupper($request->get('no_dokumen'))) == 7) {
                    $sql = DB::table($database . '.dbo.pindah')->lock('with (nolock)')
                        ->selectRaw("isnull(pindah.no_slip, '') as nomor_slip")
                        ->leftJoin(DB::raw($database . '.dbo.stsclose' . ' with (nolock)'), function ($join) {
                            $join->on('stsclose.companyid', '=', 'pindah.companyid');
                        })
                        ->whereRaw('pindah.tgl > stsclose.close_mkr')
                        ->where('pindah.no_slip', 'like', strtoupper($request->get('no_dokumen')) . '%')
                        ->first();
                } else {
                    $sql = DB::table($database . '.dbo.pindah')->lock('with (nolock)')
                        ->selectRaw("isnull(pindah.no_slip, '') as nomor_slip")
                        ->where('pindah.no_slip', strtoupper($request->get('no_dokumen')))
                        ->first();
                }

                if (empty($sql->nomor_slip)) {
                    return Response::responseWarning('Nomor slip tidak ditemukan');
                } else {
                    $nomor_slip = strtoupper(trim($sql->nomor_slip));

                    $sql = DB::table($database . '.dbo.pindah')->lock('with (nolock)')
                        ->selectRaw("isnull(pindah.no_slip, '') as nomor_slip,
                                        isnull(pindah.kd_cabang, '') as kode_cabang,
                                        isnull(cabang.nm_cabang, '') as nama_cabang,
                                        isnull(pindah.companyid, '') as companyid,
                                        isnull(company.inisial, 0) as kantor_pusat")
                        ->leftJoin(DB::raw($database . '.dbo.company' . ' with (nolock)'), function ($join) {
                            $join->on('company.companyid', '=', 'pindah.companyid');
                        })
                        ->leftJoin(DB::raw($database . '.dbo.cabang' . ' with (nolock)'), function ($join) {
                            $join->on('cabang.kd_cabang', '=', 'pindah.kd_cabang')
                                ->on('cabang.companyid', '=', 'pindah.companyid');
                        })
                        ->where('pindah.no_slip', strtoupper($nomor_slip))
                        ->first();

                    if (empty($sql->nomor_slip)) {
                        return Response::responseWarning('Data slip pemindahan tidak ditemukan');
                    } else {
                        $informasi = strtoupper(trim($sql->kode_cabang)) . ' (' . trim($sql->nama_cabang) . ')';
                        $data = [
                            'no_transaksi'  => strtoupper(trim($sql->nomor_slip)),
                            'companyid'     => strtoupper(trim($sql->companyid)),
                            'kode_cabang'   => strtoupper(trim($sql->kode_cabang)),
                            'informasi'     => $informasi,
                            'status_approve' => 0,
                            'kantor_pusat'  => (int)$sql->kantor_pusat,
                        ];
                        return Response::responseSuccess('success', $data);
                    }
                }
            } elseif (strtoupper(trim($request->get('jenis_transaksi'))) == 'HOTLINE') {
                $sql = DB::table($database . '.dbo.hopc')->lock('with (nolock)')
                    ->selectRaw("isnull(hopc.no_po, '') as nomor_po")
                    ->where('hopc.no_po', strtoupper($request->get('no_dokumen')))
                    ->first();

                if (empty($sql->nomor_po)) {
                    return Response::responseWarning('Nomor PO Hotline tidak ditemukan');
                } else {
                    $nomor_po = strtoupper(trim($sql->nomor_po));

                    $sql = DB::table($database . '.dbo.hopc')->lock('with (nolock)')
                        ->selectRaw("isnull(hopc.no_po, '') as nomor_po,
                                        isnull(hopc.kd_cabang, '') as kode_cabang,
                                        isnull(cabang.nm_cabang, '') as nama_cabang,
                                        isnull(hopc.nm_kons, '') as nama_konsumen,
                                        isnull(hopc.companyid, '') as companyid")
                        ->leftJoin(DB::raw($database . '.dbo.cabang' . ' with (nolock)'), function ($join) {
                            $join->on('cabang.kd_cabang', '=', 'hopc.kd_cabang')
                                ->on('cabang.companyid', '=', 'hopc.companyid');
                        })
                        ->where('hopc.no_po', strtoupper($nomor_po))
                        ->first();

                    if (empty($sql->nomor_po)) {
                        return Response::responseWarning('Data PO Hotline tidak ditemukan');
                    } else {
                        $informasi = '(CABANG: ' . strtoupper(trim($sql->kode_cabang)) . ' - ' . trim($sql->nama_cabang) . ')';
                        if (trim($sql->nama_konsumen) != '') {
                            $informasi .= ', (KONSUMEN: ' . trim($sql->nama_konsumen) . ')';
                        }

                        $data = [
                            'no_transaksi'  => strtoupper(trim($sql->nomor_po)),
                            'companyid'     => strtoupper(trim($sql->companyid)),
                            'kode_cabang'   => strtoupper(trim($sql->kode_cabang)),
                            'informasi'     => $informasi,
                            'status_approve' => 0,
                            'kantor_pusat'  => 0,
                        ];
                        return Response::responseSuccess('success', $data);
                    }
                }
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

    public function simpanCetakUlang(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'divisi'            => 'required',
                'jenis_transaksi'   => 'required',
                'no_dokumen'        => 'required',
                'kode_cabang'       => 'required',
                'company_dokumen'   => 'required',
                'alasan'            => 'required',
                'companyid'         => 'required',
                'role_id'           => 'required',
                'user_id'           => 'required',
            ]);

            if ($validate->fails()) {
                return Response::responseWarning("Pilih divisi, jenis dokumen dan isi nomor dokumen terlebih dahulu");
            }

            if (strtoupper(trim($request->get('role_id'))) != 'MD_H3_MGMT') {
                if (strtoupper(trim($request->get('role_id'))) != 'MD_H3_FIN') {
                    return Response::responseWarning('Anda tidak memiliki akses untuk membuka halaman ini');
                }
            }

            $divisi = '';
            if (strtoupper(trim($request->get('divisi'))) == 'HONDA') {
                $divisi = 'dbhonda';
            } else {
                $divisi = 'dbsuma';
            }

            $jenis_dokumen = '';
            if (strtoupper(trim($request->get('jenis_transaksi'))) == 'FAKTUR') {
                $jenis_dokumen = 'Faktur';

                $sql = DB::table($divisi . '.dbo.faktur')->lock('with (nolock)')
                    ->select('no_faktur')
                    ->where('faktur.no_faktur', $request->get('no_dokumen'))
                    ->where('faktur.companyid', $request->get('company_dokumen'))
                    ->first();

                if (empty($sql->no_faktur)) {
                    return Response::responseWarning('Nomor faktur tidak terdaftar');
                }
            } elseif (strtoupper(trim($request->get('jenis_transaksi'))) == 'PEMINDAHAN') {
                $jenis_dokumen = 'Pemindahan Keluar';

                $sql = DB::table($divisi . '.dbo.pindah')->lock('with (nolock)')
                    ->select('no_slip')
                    ->where('pindah.no_slip', $request->get('no_dokumen'))
                    ->where('pindah.companyid', $request->get('company_dokumen'))
                    ->first();

                if (empty($sql->no_slip)) {
                    return Response::responseWarning('Nomor slip pemindahan tidak terdaftar');
                }
            } elseif (strtoupper(trim($request->get('jenis_transaksi'))) == 'HOTLINE') {
                $jenis_dokumen = 'Purchase Order Hotline';

                $sql = DB::table($divisi . '.dbo.hopc')->lock('with (nolock)')
                    ->select('no_po')
                    ->where('hopc.no_po', $request->get('no_dokumen'))
                    ->where('hopc.companyid', $request->get('company_dokumen'))
                    ->first();

                if (empty($sql->no_po)) {
                    return Response::responseWarning('Nomor purchase order hotline tidak terdaftar');
                }
            }

            DB::transaction(function () use ($request, $divisi, $jenis_dokumen) {
                DB::insert("exec " . trim($divisi) . ".dbo.SP_EditTransaksi_Simpan2 ?,?,?,?,?,?,?,?,?", [
                    trim(strtoupper($request->get('no_dokumen'))), $request->get('approve') ?? 1,
                    $request->get('edit') ?? 0, trim(strtoupper($request->get('kode_cabang'))),
                    trim(strtoupper($request->get('company_dokumen'))), trim($jenis_dokumen), strtoupper(trim($request->get('alasan'))),
                    strtoupper(trim($request->get('companyid'))), strtoupper(trim($request->get('user_id')))
                ]);
            });

            return Response::responseSuccess('Data Berhasil Disimpan', null);
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
}
