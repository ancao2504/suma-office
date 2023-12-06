@extends('layouts.main.index')
@section('title', $title_menu)
@section('subtitle', $title_page)
@push('styles')
@endpush

@section('container')
<!--begin::Row-->
<div class="row gy-5 g-xl-8">
    <div class="card card-xl-stretch shadow">
        <div class="card-body">
            <h3>1. Informasi Dokumen</h3>
            <div class="mb-3 border rounded p-3">
                <div class="form-group row mb-2">
                    <label for="no_retur" class="col-sm-2 col-form-label">No Klaim</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="no_retur" name="no_retur" value="{{ $data->no_dokumen??session('app_user_id') }}" disabled>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label for="kd_sales" class="col-sm-2 col-form-label required">Kd Sales</label>
                    <div class="col-sm-4">
                        <select name="kd_sales" id="kd_sales" class="form-select form-control" data-control="select2" data-placeholder="Pilih kode Sales"
                        @if ((session('app_user_role_id') != 'MD_H3_MGMT') || (!empty($data) && $data->status_approve == 1) || (!empty($data) &&  (!empty($data->detail??null) || count($data->detail??[]) != 0)))
                            disabled
                        @endif
                        >
                            <option></option>
                            {!! $sales !!}
                        </select>
                        <div class="invalid-feedback" id="error_kd_sales"></div>
                    </div>
                    <label for="tgl_claim" class="col-sm-2 col-form-label required">Tanggal Dokumen</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="tgl_retur" name="tgl_retur" placeholder="Masukkan Tanggal" value="{{date('Y-m-d', strtotime(empty($data->tgl_dokumen)?date('Y-m-d'):$data->tgl_dokumen)) }}"
                        @if (!empty($data) &&  ($data->status_approve == 1 || ($tamp == false && $data->status_approve != 1 && session('app_user_role_id') != 'MD_H3_MGMT')))
                            disabled
                        @endif
                        >
                        <div class="invalid-feedback" id="error_tgl_claim"></div>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label for="jenis_konsumen" class="col-sm-2 col-form-label required">jenis Konsumen</label>
                    <div class="col-sm-4">
                        <select name="jenis_konsumen" id="jenis_konsumen" class="form-select form-control" data-placeholder="Pilih Jenis Konsumen"
                        @if ((!empty($data) &&  ($data->status_approve == 1 || session('app_user_role_id') != 'MD_H3_MGMT')) || (!empty($data->detail??null) || count($data->detail??[]) != 0))
                            disabled
                        @endif
                        >
                            <option value="0" @if (($data->pc??0) != 1) selected @endif>Dealer</option>
                            <option value="1" @if (($data->pc??0) == 1) selected @endif>Cabang</option>
                        </select>
                        <div class="invalid-feedback" id="error_jenis_konsumen"></div>
                    </div>
                </div>
                <div id="jenis_konsumen_dealer" @if (($data->pc??0) == 1) hidden @endif>
                    <div class="form-group row mb-2">
                        <label for="kd_dealer" class="col-sm-2 col-form-label required">Kd Dealer</label>
                        <div class="col-sm-4">
                            <div class="input-group mb-3 has-validation">
                                <button class="btn btn-primary list-dealer" type="button" @if ((!empty($data) &&  ($data->status_approve == 1 || session('app_user_role_id') != 'MD_H3_MGMT')) || (!empty($data->detail??null) || count($data->detail??[]) != 0))
                                    disabled
                                @endif><i class="bi bi-search"></i></button>
                                <input type="text" class="form-control" id="kd_dealer" name="kd_dealer" placeholder="Masukkan Kd Dealer" value="{{ ((($data->pc??0) != 1)?($data->kd_dealer??null):null) }}"
                                @if ((!empty($data) &&  ($data->status_approve == 1 || session('app_user_role_id') != 'MD_H3_MGMT')) || (!empty($data->detail??null) || count($data->detail??[]) != 0))
                                    disabled
                                @endif
                                >
                                <div class="invalid-feedback" id="error_kd_dealer"></div>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="nm_dealer" name="nm_dealer" value="{{ ((($data->pc??0) != 1)?($data->nm_dealer??null):null) }}" disabled>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="alamat1" class="col-sm-2 col-form-label">Alamat</label>
                        <div class="col-sm-4">
                            <textarea type="text" class="form-control" data-kt-autosize="true" id="alamat1" name="alamat1" disabled>{{ ((($data->pc??0) != 1)?($data->alamat1??null):null) }}</textarea>
                        </div>
                        <div class="col-sm-5 mt-3 mt-sm-0">
                            <input type="text" class="form-control" id="kotasj" name="kotasj" value="{{ ((($data->pc??0) != 1)?($data->kota??null):null) }}" disabled>
                        </div>
                    </div>
                </div>
                <div id="jenis_konsumen_cabang" @if (($data->pc??0) != 1) hidden @endif>
                    <div class="form-group row mb-2">
                        <label for="kd_cabang" class="col-sm-2 col-form-label required">kode Cabang</label>
                        <div class="col-sm-9">
                            <select name="kd_cabang" id="kd_cabang" class="form-select form-control" data-control="select2" data-placeholder="Pilih kode Cabang"
                            @if ((!empty($data) &&  ($data->status_approve == 1 || session('app_user_role_id') != 'MD_H3_MGMT')) || (!empty($data->detail??null) || count($data->detail??[]) != 0))
                                disabled
                            @endif
                            >
                                <option></option>
                                {!! $cabang !!}
                            </select>
                            <div class="invalid-feedback" id="error_kd_cabang"></div>
                        </div>
                    </div>
                </div>
            </div>
            @if (empty($data) || ($data->status_approve != 1 && session('app_user_role_id') == 'MD_H3_MGMT') || $tamp == true)
            <div class="mb-3">
                <a role="button" id="add_detail" class="btn btn-primary" >Tambah Detail</a>
            </div>
            @endif
            <div id="list_detail" class="table-responsive border rounded-3">
                <table id="datatable_classporduk" class="table table-row-dashed table-row-gray-300 align-middle border">
                    <thead class="border">
                        <tr class="fs-8 fw-bolder text-muted text-center">
                            <th rowspan="2" class="w-50px ps-3 pe-3">No</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3">No Faktur</th>
                            <th rowspan="2" class="min-w-100px ps-3 pe-3">part Number</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3">No Produksi</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3">Qty Retur</th>
                            <th rowspan="2" class="w-50px ps-3 pe-3">tgl Pakai</th>
                            <th rowspan="2" class="w-50px ps-3 pe-3">tgl Klaim</th>
                            <th colspan="3" class="w-50px ps-3 pe-3">status</th>
                            <th rowspan="2" class="w-auto ps-3 pe-3">Keterangan</th>
                            <th rowspan="2" class="w-50px ps-3 pe-3">tgl Ganti</th>
                            <th rowspan="2" class="w-50px ps-3 pe-3">Qty Ganti</th>
                            {{-- @if (empty($data) || ($data->status_approve != 1 && session('app_user_role_id') == 'MD_H3_MGMT'))
                            <th rowspan="2" class="min-w-150px ps-3 pe-3">Action</th>
                            @endif --}}
                            <th rowspan="2" class="min-w-150px ps-3 pe-3">Action</th>
                        </tr>
                        <tr class="fs-8 fw-bolder text-muted text-center">
                            <th class="w-50px ps-3 pe-3">Stock</th>
                            <th class="w-50px ps-3 pe-3">Minimum</th>
                            <th class="w-50px ps-3 pe-3">Klaim</th>
                        </tr>
                    </thead>
                    <tbody id="list-retur">
                        @if (empty($data->detail) || count($data->detail) == 0)
                            <tr class="fw-bolder fs-8 border text_not_data">
                                <td colspan="99" class="text-center">Tidak ada data</td>
                            </tr>
                        @else
                            @foreach ($data->detail as $detail)
                            @php
                                $dta_edt = json_encode((object)[
                                    'no_retur' => $detail->no_dokumen,
                                    'no_faktur' => $detail->no_faktur,
                                    'limit_jumlah' => $detail->limit_jumlah,
                                    'kd_part' => $detail->kd_part,
                                    'no_produksi' => $detail->no_produksi,
                                    'nm_part' => $detail->nm_part,
                                    'stock' => $detail->stock,
                                    'jumlah' => $detail->qty,
                                    'tgl_klaim' => $detail->tgl_klaim,
                                    'tgl_pakai' => $detail->tgl_pakai,
                                    'sts_stock' => $detail->sts_stock,
                                    'sts_klaim' => $detail->sts_klaim,
                                    'sts_min' => $detail->sts_min,
                                    'ket' => $detail->keterangan
                                ]);
                                $dta_del = json_encode((object)[
                                    'no_retur' => $detail->no_dokumen,
                                    'no_faktur' => $detail->no_faktur,
                                    'kd_part' => $detail->kd_part,
                                    'no_produksi' => $detail->no_produksi
                                ]);
                            @endphp

                            <tr class="fw-bolder fs-8 border" data-key="{{ ((string)$detail->no_faktur.(string)$detail->kd_part.(string)$detail->no_produksi) }}">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ ($detail->no_faktur??'-') }}</td>
                                <td>{{ ($detail->kd_part??'-') }}</td>
                                <td>{{ ($detail->no_produksi??'-') }}</td>
                                <td class="text-end">{{ number_format($detail->qty, 0, '.', ',')??'-' }}</td>
                                <td>{{ date('Y/m/d', strtotime($detail->tgl_pakai))??'-' }}</td>
                                <td>{{ date('Y/m/d', strtotime($detail->tgl_klaim))??'-' }}</td>
                                <td class="text-center">
                                    @if ($detail->sts_stock == 1)
                                        <span class="badge badge-light-primary">Ganti Barang</span>
                                    @elseif ($detail->sts_stock == 2)
                                        <span class="badge badge-light-primary">Stock 0</span>
                                    @elseif ($detail->sts_stock == 3)
                                        <span class="badge badge-light-primary">Retur</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($detail->sts_min == 1)
                                        <span class="badge badge-light-info">Minimum</span>
                                    @elseif ($detail->sts_min == 0)
                                        <span class="badge badge-light-info">Tidak</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($detail->sts_klaim == 1)
                                        <span class="badge badge-light-warning">klaim ke Supplier</span>
                                    @elseif ($detail->sts_klaim == 2)
                                        <span class="badge badge-light-warning">Tidak Melakukan Apapun</span>
                                    @endif
                                </td>
                                <td>{{ ($detail->keterangan??'-') }}</td>
                                <td>{{ ($detail->tgl_ganti?date('Y/m/d', strtotime($detail->tgl_ganti)):'-') }}</td>
                                <td>{{ ($detail->qty_ganti?number_format($detail->qty_ganti, 0, '.', ','):'-') }}</td>
                                <td class="text-center">
                                    @if (empty($data) || ($data->status_approve != 1 && session('app_user_role_id') == 'MD_H3_MGMT') || $tamp == true)
                                        <a role="button" data-bs-toggle="modal" href="#detail_modal" data-a="{{ base64_encode($dta_edt) }}" class="btn_dtl_edit btn-sm btn-icon btn-warning my-1"><i class="fas fa-edit text-dark"></i></a>
                                        <a role="button" data-a="{{ base64_encode($dta_del) }}" class="btn_dtl_delete btn-sm btn-icon btn-danger my-1"><i class="fas fa-trash text-white"></i></a>
                                    @endif
                                    {{-- <a role="button" data-bs-toggle="modal"
                                    data-bs-target="#jwb-form"
                                    class="btn-sm btn-icon btn-success text-white"><span class="bi bi-chat-right-dots"></span></a> --}}
                                </td>
                                {{-- @if ($detail->qty_jwb > 0)
                                    <td class="text-center">
                                        <a href="{{ route('retur.konsumen.form',['id' => base64_encode($detail->no_faktur)]) }} }}" class="btn-sm btn-icon btn-success d-inline-block mt-1 text-white"><span class="bi bi-box-arrow-up-right"></span></a>
                                    </td>
                                @endif --}}
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            @if (empty($data) || ($data->status_approve != 1 && session('app_user_role_id') == 'MD_H3_MGMT') || $tamp == true)
            <a role="button" class="btn btn-success text-white btn_simpan">Simpan @if (session('app_user_role_id') == 'MD_H3_MGMT')dan Approve @elseif (session('app_user_role_id') != 'MD_H3_MGMT')Pengajuan @endif</a>
            @endif
            <a href="{{(strtok(URL::previous(),'?') == strtok(URL::current(),'?'))?route('retur.konsumen.index'):URL::previous()}}" role="button" id="btn-back" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</div>
<!--end::Row-->

@if (empty($data) || ($data->status_approve != 1 && session('app_user_role_id') == 'MD_H3_MGMT') || $tamp == true)

<!-- Modal warning -->
<div class="modal fade" id="warning_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-3" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-md-down">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Warning</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
</div>


<!-- Modal Detail -->
<div class="modal fade" id="detail_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-2" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-xl-down">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="detail_modal">Tambah Detail</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="" id="form_detail">
                <h3>2. Informasi Produk</h3>
                <div class="col-xl-12 border rounded mb-3 p-2">
                    <div class="form-group row mb-2">
                        <label for="kd_part" class="col-sm-2 col-form-label required">No Faktur</label>
                        <div class="col-sm-4">
                            <div class="input-group mb-3 has-validation">
                                <button class="btn btn-primary list-faktur" type="button"><i class="bi bi-search"></i></button>
                                <input type="text" class="form-control" id="no_faktur" name="no_faktur" placeholder="No Faktur" value="" style="text-transform: uppercase;" required>
                                <div class="invalid-feedback" id="error_no_faktur"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="kd_part" class="col-sm-2 col-form-label required">Part Number</label>
                        <div class="col-sm-4">
                            <div class="input-group mb-3 has-validation">
                                <button class="btn btn-primary list-part" type="button"><i class="bi bi-search"></i></button>
                                <input type="text" class="form-control" id="kd_part" name="kd_part" placeholder="Part Number" value="" style="text-transform: uppercase;" required>
                                <div class="invalid-feedback" id="error_kd_part"></div>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <input type="text" class="form-control bg-secondary" id="nm_part" name="nm_part" placeholder="Nama Part" value="" readonly>
                        </div>
                        <div class="col-sm-1 mt-3 mt-sm-0">
                            <input type="text" class="form-control bg-secondary" id="stock" name="stock" placeholder="stock" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="qty_retur" class="col-sm-2 col-form-label required">Jumlah</label>
                        <div class="col-sm-4">
                            <input type="number" class="form-control" id="qty_retur" name="qty_retur" min="1" placeholder="Masukkan QTY Retur" value="1" required>
                            <div class="invalid-feedback" id="error_qty_retur"></div>
                        </div>
                        <label for="no_produksi" class="col-sm-2 col-form-label required">No Produksi</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="no_produksi" name="no_produksi" placeholder="No Produksi" value="" style="text-transform: uppercase;" required>
                            <div class="invalid-feedback" id="error_no_produksi"></div>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="tgl_claim" class="col-sm-2 col-form-label required">Tanggal Klaim</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="tgl_klaim" name="tgl_klaim" placeholder="Masukkan Tanggal Klaim" value="{{date('Y-m-d')}}">
                            <div class="invalid-feedback" id="error_tgl_klaim"></div>
                        </div>
                        <label for="tgl_claim" class="col-sm-2 col-form-label required">Tanggal Pakai</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="tgl_pakai" name="tgl_pakai" placeholder="Masukkan Tanggal Pakai" value="{{date('Y-m-d')}}">
                            <div class="invalid-feedback" id="error_tgl_pakai"></div>
                        </div>
                    </div>
                </div>
                <h3>3. Informasi Klaim</h3>
                <div class="mb-3 border rounded p-2">
                    <div class="form-group row mb-2">
                        <label for="ket" class="col-sm-2 col-form-label required">Keterangan</label>
                        <div class="col-sm-9">
                            <textarea type="text" class="form-control" data-kt-autosize="true" id="ket" name="ket" rows="3"></textarea>
                            <div class="invalid-feedback" id="error_ket"></div>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="sts_stock" class="col-sm-2 col-form-label required">Status Stock</label>
                        <div class="col-sm-4">
                            <select name="sts_stock" id="sts_stock" class="form-select form-control" required>
                                <option value="">Pilih Status Stock</option>
                                <option value="1">Ganti Barang</option>
                                <option value="2">Stock 0</option>
                                <option value="3">Retur</option>
                            </select>
                            <div class="invalid-feedback" id="error_sts_stock"></div>
                        </div>
                    </div>

                    <div class="form-group row mb-2">
                        <label for="sts_minimum" class="col-sm-2 col-form-label required">Status Minimum</label>
                        <div class="col-sm-4">
                            <select name="sts_minimum" id="sts_minimum" class="form-select form-control" required>
                                <option value="">Pilih Status Minimum</option>
                            </select>
                            <div class="invalid-feedback" id="error_sts_minimum"></div>
                        </div>
                    </div>

                    <div class="form-group row mb-2">
                        <label for="sts_klaim" class="col-sm-2 col-form-label required">Status Klaim</label>
                        <div class="col-sm-4">
                            <select name="sts_klaim" id="sts_klaim" class="form-select form-control" required>
                                <option value="">Pilih Status Retur</option>
                            </select>
                            <div class="invalid-feedback" id="error_sts_klaim"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <a role="button" class="btn btn-primary text-white btn_simpan_tmp">Simpan</a>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
</div>

<!--begin::Modal Dealer data-->
<div class="modal fade" tabindex="-1" id="dealer-list">
</div>
<!--end::Modal Dealer data-->

<!--begin::Modal Part data-->
<div class="modal fade" tabindex="-1" id="part-list">
</div>
<!--end::Modal Part data-->

<!--begin::Modal Part data-->
<div class="modal fade" tabindex="-1" id="faktur-list" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-2" aria-labelledby="staticBackdropLabel" aria-hidden="true">
</div>
<!--end::Modal Part data-->
@endif


<!--begin::Modal Jawab data-->
{{-- <div class="modal fade" id="jwb-form" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-3" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Jawab</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 row align-items-start">
                    <div class="col-md-12">
                        <h3 class="text-start">Jawaban dari Gudang</h3>
                    </div>
                </div>
                <div class="table-responsive border rounded-3">
                    <table id="datatable_jwb" class="table table-row-dashed table-row-gray-300 align-middle border">
                        <thead class="border">
                            <tr class="fs-8 fw-bolder text-muted text-center">
                                <th rowspan="2" class="w-50px ps-3 pe-3">No</th>
                                <th rowspan="2" class="min-w-100px ps-3 pe-3">part Number</th>
                                <th colspan="2" class="w-50px ps-3 pe-3">qty</th>
                                <th rowspan="2" class="w-auto ps-3 pe-3">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="fw-bolder fs-8 border">
                                <td colspan="99" class="text-center">Tidak ada data</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mb-3 row align-items-start mt-10">
                    <div class="col-md-12">
                        <h3 class="text-start">Form Jawab</h3>
                    </div>
                </div>
                <div class="mb-3 row align-items-center border rounded p-3">
                    <div class="col-md-12 mb-5">
                        <a role="button" class="btn btn-primary" id="auto_jwb">Isi Otomatis</a>
                    </div>
                    <div class="col-md-6">
                        <label for="quantity" class="form-label">Quantity Jawab</label>
                        <input type="number" class="form-control" id="quantity" name="quantity[]" placeholder="Enter quantity" min="1">
                    </div>
                    <div class="col-md-6">
                        <label for="description" class="form-label">Keterangan</label>
                        <select class="form-select" id="description" name="description[]">
                            <option value="Terima Ganti barang">Terima Ganti barang</option>
                            <option value="Terima Ganti uang">Terima Ganti uang</option>
                            <option value="Tolak">Tolak</option>
                        </select>
                    </div>
                    <div class="col-md-12 mt-5 text-end">
                        <button type="button" class="btn btn-primary" id="add_jwb">Add</button>
                    </div>

                </div>
                <div class="mb-3 row align-items-start mt-10">
                    <div class="col-md-12">
                        <h3 class="text-start">Jawaban Ke Konsumen</h3>
                    </div>
                </div>
                <div class="table-responsive border rounded-3">
                    <table id="datatable_jwb" class="table table-row-dashed table-row-gray-300 align-middle border">
                        <thead class="border">
                            <tr class="fs-8 fw-bolder text-muted text-center">
                                <th rowspan="2" class="w-50px ps-3 pe-3">No</th>
                                <th rowspan="2" class="min-w-100px ps-3 pe-3">part Number</th>
                                <th colspan="2" class="w-50px ps-3 pe-3">qty</th>
                                <th rowspan="2" class="w-auto ps-3 pe-3">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="fw-bolder fs-8 border">
                                <td colspan="99" class="text-center">Tidak ada data</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> --}}
<!--end::Modal Jawab data-->

@endsection

@push('scripts')
<!-- script tambanhan -->
<script>
    const old = {
        kd_cabang: @json(((($data->pc??0) == 1)?$data->kd_dealer:'')),
        @if ($data->kd_sales??false)
            kd_sales:@json(($data->kd_sales??''))
        @elseif (session('app_user_role_id')=='MD_H3_SM')
            kd_sales:@json(session('app_user_id'))
        @endif
    };
</script>

<script language="JavaScript" src="{{ asset('assets/js/suma/retur/konsumen/form.js') }}?v={{ time() }}"></script>
@if (empty($data) || ($data->status_approve != 1 && session('app_user_role_id') == 'MD_H3_MGMT') || $tamp == true)
<script language="JavaScript" src="{{ asset('assets/js/suma/retur/konsumen/getDealer.js') }}?v={{ time() }}"></script>
<script language="JavaScript" src="{{ asset('assets/js/suma/retur/konsumen/getPart.js') }}?v={{ time() }}"></script>
<script language="JavaScript" src="{{ asset('assets/js/suma/retur/konsumen/getFaktur.js') }}?v={{ time() }}"></script>
@endif
@endpush
