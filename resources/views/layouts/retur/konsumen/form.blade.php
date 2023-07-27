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
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label for="kd_sales" class="col-sm-2 col-form-label required">Kd Sales</label>
                    <div class="col-sm-4">
                        <select name="kd_sales" id="kd_sales" class="form-select form-control" data-control="select2" data-placeholder="Pilih kode Sales">
                            <option></option>
                            {!! $sales !!}
                        </select>
                    </div>
                    <label for="tgl_claim" class="col-sm-2 col-form-label required">Tanggal Retur</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="tgl_retur" name="tgl_retur" placeholder="Masukkan Tanggal" value="{{date('Y-m-d', strtotime(empty($data->tgl_dokumen)?date('Y-m-d'):$data->tgl_dokumen)) }}" required>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label for="jenis_konsumen" class="col-sm-2 col-form-label required">jenis Konsumen</label>
                    <div class="col-sm-4">
                        <select name="jenis_konsumen" id="jenis_konsumen" class="form-select form-control" data-placeholder="Pilih Jenis Konsumen">
                            <option value="0" @if (($data->pc??0) != 1) selected @endif>Dealer</option>
                            <option value="1" @if (($data->pc??0) == 1) selected @endif>Cabang</option>
                        </select>
                    </div>
                </div>
                <div id="jenis_konsumen_dealer" @if (($data->pc??0) == 1) hidden @endif>
                    <div class="form-group row mb-2">
                        <label for="kd_dealer" class="col-sm-2 col-form-label required">Kd Dealer</label>
                        <div class="col-sm-4">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="kd_dealer" name="kd_dealer" placeholder="Masukkan Kd Dealer" value="{{ ((($data->pc??0) != 1)?($data->kd_dealer??null):null) }}" required>
                                <button class="btn btn-primary list-dealer" type="button">Pilih</button>
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
                            <select name="kd_cabang" id="kd_cabang" class="form-select form-control" data-control="select2" data-placeholder="Pilih kode Cabang">
                                <option></option>
                                {!! $cabang !!}
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <a role="button" id="add_detail" class="btn btn-primary" data-bs-toggle="modal" href="#detail_modal">Tambah Detail</a>
            </div>

            <div id="list_detail" class="table-responsive border rounded-3">
                <table id="datatable_classporduk" class="table table-row-dashed table-row-gray-300 align-middle border">
                    <thead class="border">
                        <tr class="fs-8 fw-bolder text-muted text-center">
                            <th rowspan="2" class="w-50px ps-3 pe-3">No</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3">part Number</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3">Qty Retur</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3">No Produksi</th>
                            <th rowspan="2" class="w-50px ps-3 pe-3">tgl Ganti</th>
                            <th rowspan="2" class="w-50px ps-3 pe-3">Qty Ganti</th>
                            <th colspan="3" class="w-50px ps-3 pe-3">status</th>
                            <th rowspan="2" class="min-w-150px ps-3 pe-3">Keterangan</th>
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
                                <td colspan="13" class="text-center">Tidak ada data</td>
                            </tr>
                        @else
                            @foreach ($data->detail as $detail)
                            @php
                                $dta_edt = json_encode((object)[
                                    'no_retur' => $detail->no_dokumen,
                                    'no_produksi' => $detail->no_produksi,
                                    'kd_part' => $detail->kd_part,
                                    'nm_part' => $detail->nm_part,
                                    'stock' => $detail->stock,
                                    'jumlah' => $detail->qty,
                                    'sts_stock' => $detail->sts_stock,
                                    'sts_klaim' => $detail->sts_klaim,
                                    'sts_min' => $detail->sts_min,
                                    'ket' => $detail->keterangan
                                ]);
                                $dta_del = json_encode((object)[
                                    'no_retur' => $detail->no_dokumen,
                                    'kd_part' => $detail->kd_part
                                ]);
                            @endphp

                            <tr class="fw-bolder fs-8 border" data-key="{{ ($detail->kd_part??'') }}">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ ($detail->kd_part??'-') }}</td>
                                <td class="text-end">{{ number_format($detail->qty, 0, '.', ',')??'-' }}</td>
                                <td>{{ ($detail->no_produksi??'-') }}</td>
                                <td>{{ ($detail->tgl_ganti??'-') }}</td>
                                <td>{{ ($detail->qty_ganti?number_format($detail->qty_ganti, 0, '.', ','):'-') }}</td>
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
                                <td class="text-center">
                                    <a role="button" data-bs-toggle="modal" href="#detail_modal" data-a="{{ base64_encode($dta_edt) }}" class="btn_dtl_edit btn-sm btn-icon btn-warning my-1"><i class="fas fa-edit text-dark"></i></a>
                                    <a role="button" data-a="{{ base64_encode($dta_del) }}" class="btn_dtl_delete btn-sm btn-icon btn-danger my-1" data-bs-toggle="modal" data-bs-target="#delet-retur"><i class="fas fa-trash text-white"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <a role="button" class="btn btn-success text-white btn_simpan">Simpan @if (session('app_user_role_id') == 'MD_H3_MGMT')dan Approve @elseif (session('app_user_role_id') != 'MD_H3_MGMT')Pengajuan @endif</a>
            <a href="{{ Route('retur.konsumen.index') }}" id="btn-back" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</div>
<!--end::Row-->

<!-- Modal warning -->
<div class="modal fade" id="warning_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-3" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-fullscreen-md-down">
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
                        <label for="no_produksi" class="col-sm-2 col-form-label">No Produksi</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="no_produksi" name="no_produksi" placeholder="Masukkan No Produksi" value="" required>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="kd_part" class="col-sm-2 col-form-label required">Part Number</label>
                        <div class="col-sm-4">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="kd_part" name="kd_part" placeholder="Part Number" value="" required>
                                <button class="btn btn-primary list-part" type="button">Pilih</button>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class="form-control bg-secondary" id="nm_part" name="nm_part" placeholder="Nama Part" value="" readonly>
                        </div>
                        <div class="col-sm-1 mt-3 mt-sm-0">
                            <input type="text" class="form-control bg-secondary" id="stock" name="stock" placeholder="stock" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="qty_retur" class="col-sm-2 col-form-label">Jumlah</label>
                        <div class="col-sm-4">
                            <input type="number" class="form-control" id="qty_retur" name="qty_retur" min="1" placeholder="Masukkan QTY Retur" value="1" required>
                        </div>
                    </div>
                </div>
                <h3>3. Informasi Klaim</h3>
                <div class="mb-3 border rounded p-2">
                    <div class="form-group row mb-2">
                        <label for="ket" class="col-sm-2 col-form-label">Keterangan</label>
                        <div class="col-sm-9">
                            <textarea type="text" class="form-control" data-kt-autosize="true" id="ket" name="ket" rows="3"></textarea>
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
                        </div>
                    </div>
                    
                    <div class="form-group row mb-2">
                        <label for="sts_minimum" class="col-sm-2 col-form-label required">Status Minimum</label>
                        <div class="col-sm-4">
                            <select name="sts_minimum" id="sts_minimum" class="form-select form-control" required>
                                <option value="">Pilih Status Minimum</option>
                                {{-- <option value="1">Minimum</option>
                                <option value="0">Tidak Minimum</option> --}}
                            </select>
                        </div>
                    </div>

                    <div class="form-group row mb-2">
                        <label for="sts_klaim" class="col-sm-2 col-form-label required">Status Klaim</label>
                        <div class="col-sm-4">
                            <select name="sts_klaim" id="sts_klaim" class="form-select form-control" required>
                                <option value="">Pilih Status Retur</option>
                                {{-- <option value="1">klaim ke Supplier</option>
                                <option value="2">Tidak</option> --}}
                                {{-- <option value="3">Memo Koreksi</option> --}}
                            </select>
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

@endsection

@push('scripts')
<!-- script tambanhan -->
<script>
    
    const old = {
        kd_cabang: @json(((($data->pc??0) == 1)?$data->kd_dealer:'')),
        kd_sales: @json(($data->kd_sales??'')),
    };
    
</script>
<script language="JavaScript" src="{{ asset('assets/js/suma/retur/konsumen/getDealer.js') }}?v={{ time() }}"></script>
<script language="JavaScript" src="{{ asset('assets/js/suma/retur/konsumen/getpart.js') }}?v={{ time() }}"></script>
<script language="JavaScript" src="{{ asset('assets/js/suma/retur/konsumen/form.js') }}?v={{ time() }}"></script>
@endpush