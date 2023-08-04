@extends('layouts.main.index')
@section('title', $title_menu)
@section('subtitle', $title_page)
@push('styles')
@endpush

@section('container')
{{-- @dd($data) --}}
<!--begin::Row-->
<div class="row gy-5 g-xl-8">
    <div class="card card-xl-stretch shadow">
        <div class="card-body">
            <h3>1. Informasi Dokumen</h3>
            <div class="mb-3 border rounded p-3">
                <div class="form-group row mb-2">
                    <label for="no_retur" class="col-sm-2 col-form-label">No Dokumen</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="no_retur" name="no_retur" value="{{ session('app_user_id') }}" disabled>
                    </div>
                    <label for="tgl_claim" class="col-sm-2 col-form-label">Tanggal Retur</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="tgl_retur" name="tgl_retur" placeholder="Masukkan Tanggal" value="{{date('Y-m-d', strtotime(empty($data->tglretur)?date('Y-m-d'):$data->tglretur)) }}" required>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label for="kd_supp" class="col-sm-2 col-form-label">Kode Supplier</label>
                    <div class="col-sm-6">
                        <select name="kd_supp" id="kd_supp" class="form-select form-control" data-control="select2" data-placeholder="Pilih kode Supplier">
                            <option></option>
                            {!! $supplier !!}
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <a role="button" id="add_detail" class="btn btn-primary" data-bs-toggle="modal" href="#detail_modal">Tambah Detail</a>
            </div>
            {{-- @dd($data->detail) --}}
            <div id="list_detail" class="table-responsive border rounded-3">
                <table id="datatable_classporduk" class="table table-row-dashed table-row-gray-300 align-middle border">
                    <thead class="border">
                        <tr class="fs-8 fw-bolder text-muted text-center">
                            <th rowspan="2" class="w-50px ps-3 pe-3">No</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3">No Klaim</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3">Tanggal Klaim</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3">Part Number</th>
                            <th rowspan="2" class="w-auto ps-3 pe-3">Nama Part</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3">Qty</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3">keterangan</th>
                            <th rowspan="2" class="w-auto ps-3 pe-3">Action</th>
                        </tr>
                        <tr class="fs-8 fw-bolder text-muted text-center">
                        </tr>
                    </thead>
                    <tbody id="list-retur">
                        @if (empty($data->detail) || count($data->detail) == 0)
                            <tr class="fw-bolder fs-8 border text_not_data">
                                <td colspan="9" class="text-center">Tidak ada data</td>
                            </tr>
                        @else
                            @foreach ($data->detail as $detail)
                            @php
                                $dta_edt = json_encode((object)[
                                    'no_klaim' => $detail->no_klaim,
                                    'tgl_claim' => $detail->tgl_claim,
                                    'no_produksi' => $detail->no_produksi,
                                    'kd_part' => $detail->kd_part,
                                    'nm_part' => $detail->nm_part,
                                    'ket' => $detail->ket,
                                    'jumlah' => $detail->jmlretur,
                                    'diterima' => $detail->diterima,
                                    'no_ps' => $detail->no_ps_klaim,
                                ]);
                                $dta_del = json_encode((object)[
                                    'no_klaim' => $detail->no_klaim,
                                    'kd_part' => $detail->kd_part
                                ]);
                            @endphp

                            <tr class="fw-bolder fs-8 border" data-key="{{ ($detail->no_klaim.$detail->kd_part) }}">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ ($detail->no_klaim??'-') }}</td>
                                <td>{{ date('Y/m/d', strtotime($detail->tgl_claim))??'-' }}</td>
                                <td>{{ ($detail->kd_part??'-') }}</td>
                                <td>{{ ($detail->nm_part??'-') }}</td>
                                <td class="text-end">{{ number_format($detail->jmlretur, 0, '.', ',')??'-' }}</td>
                                <td>{{ ($detail->ket??'-') }}</td>
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
            <a role="button" class="btn btn-success text-white btn_simpan">Simpan Pengajuan</a>
            <a href="{{ Route('retur.supplier.index') }}" id="btn-back" class="btn btn-secondary">Kembali</a>
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
                        {{-- <label for="no_dus" class="col-sm-2 col-form-label">Nomor DUS</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="no_dus" name="no_dus" placeholder="Masukkan No DUS" value="" required>
                        </div> --}}
                    </div>
                    <div class="form-group row mb-2">
                        <label for="no_klaim" class="col-sm-2 col-form-label required">No Klaim</label>
                        <div class="col-sm-4">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="no_klaim" name="no_klaim" placeholder="No Klaim" value="" required>
                                <button class="btn btn-primary list-klaim" type="button">Pilih</button>
                            </div>
                        </div>
                        <label for="tgl_claim" class="col-sm-2 col-form-label">Tanggal Klaim</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="tgl_claim" name="tgl_claim" placeholder="Tanggal Klaim" value="" disabled>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="kd_part" class="col-sm-2 col-form-label required">Part Number</label>
                        <div class="col-sm-4">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="kd_part" name="kd_part" placeholder="Part Number" value="">
                                <button class="btn btn-primary list-part" type="button">Pilih</button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="nm_part" name="nm_part" placeholder="Nama Part" value="" disabled>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="qty_klaim" class="col-sm-2 col-form-label">Jumlah</label>
                        <div class="col-sm-4">
                            <input type="number" class="form-control" id="qty_klaim" name="qty_klaim" min="1" placeholder="Jumlah Klaim" value="" disabled>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="no_produksi" class="col-sm-2 col-form-label">No Produksi</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="no_produksi" name="no_produksi" placeholder="No Produk" value="">
                        </div>
                        <label for="no_ps" class="col-sm-2 col-form-label">No Packing Sheet</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="no_ps" name="no_ps" placeholder="Masukkan No Packing Sheet" value="" required>
                        </div>
                    </div>
                </div>
                <h3>3. Informasi Retur</h3>
                <div class="mb-3 border rounded p-2">
                    <div class="form-group row mb-2">
                        <label for="kode_claim" class="col-sm-2 col-form-label">Kode Claim</label>
                        <div class="col-sm-10">
                            <div class="row">
                                <div class="col-6">
                                    <label for="kode_claim_kualitas" class="col col-form-label">Claim Kualitas</label>
                                    <select name="kode_claim_kualitas" id="kode_claim_kualitas" class="form-select form-control" data-placeholder="Pilih kode Claim">
                                        <option value="">Pilih Kode Claim</option>
                                        <option value="A">(A) Karat/Korosi</option>
                                        <option value="B">(B) Permukaan Cacat (Jamur, Gores, dll)</option>
                                        <option value="C">(C) Bengkok/Berubah Bentuk</option>
                                        <option value="D">(D) Patah/Pecah/Sobek</option>
                                        <option value="E">(E) Sub Part Tidak lengkap</option>
                                        <option value="F">(F) Arus Mati (Electric)</option>
                                        <option value="G">(G) Bocor (Liquid)</option>
                                        <option value="H">(H) Dimensi Tidak Sesuai Spek</option>
                                        <option value="I">Lainya</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="kode_claim_non_kualitas" class="col col-form-label">Claim Non Kualitas</label>
                                        <select name="kode_claim_non_kualitas" id="kode_claim_non_kualitas" class="form-select form-control" data-placeholder="Pilih kode Claim">
                                            <option value="">Pilih Kode Claim</option>
                                            <option value="K">(K) Jumlah Part Kurang</option>
                                            <option value="L">(L) Jumlah Part Lebih</option>
                                            <option value="M">(M) Fisik Part Beda</option>
                                            <option value="N">(N) Label Beda</option>
                                            <option value="O">(O) Packaging rusak</option>
                                            <option value="P">(P) Tidak Order</option>
                                            <option value="Q">lainya</option>
                                        </select>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="col-sm-10">
                                        <textarea type="text" class="form-control" data-kt-autosize="true" id="ket" name="ket" rows="3" hidden></textarea>
                                    </div>
                                </div>
                                <span class="required">Isi salah satu dari kategori claim C3 diatas</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="diterima" class="col-sm-2 col-form-label">Diterima Oleh</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="diterima" name="diterima" placeholder="Masukkan Diterima Oleh" value="" required>
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
<div class="modal fade" tabindex="-1" id="klaim-list">
</div>
<!--end::Modal Part data-->

@endsection

@push('scripts')
<!-- script tambanhan -->
<script>
    
    const old = {
        kd_supp: @json(($data->kd_supp??'')),
    };
    
</script>
<script language="JavaScript" src="{{ asset('assets/js/suma/retur/supplier/getKlaim.js') }}?v={{ time() }}"></script>
<script language="JavaScript" src="{{ asset('assets/js/suma/retur/supplier/getpart.js') }}?v={{ time() }}"></script>
<script language="JavaScript" src="{{ asset('assets/js/suma/retur/supplier/form.js') }}?v={{ time() }}"></script>
@endpush