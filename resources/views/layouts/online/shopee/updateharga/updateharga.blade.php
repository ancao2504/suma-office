@extends('layouts.main.index')
@section('title','Shopee')
@section('subtitle','Update Harga')
@section('container')
<!--start::container-->
<div class="row g-0">
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Daftar Update Harga</span>
                <span class="text-muted fw-bold fs-7">Form update harga Shopee periode
                    <span class="text-dark fw-bolder fs-7">
                        @if($data_filter->month == 1) Januari
                        @elseif($data_filter->month == 2) Februari
                        @elseif($data_filter->month == 3) Maret
                        @elseif($data_filter->month == 4) April
                        @elseif($data_filter->month == 5) Mei
                        @elseif($data_filter->month == 6) Juni
                        @elseif($data_filter->month == 7) Juli
                        @elseif($data_filter->month == 8) Agustus
                        @elseif($data_filter->month == 9) September
                        @elseif($data_filter->month == 10) Oktober
                        @elseif($data_filter->month == 11) November
                        @elseif($data_filter->month == 12) Desember
                        @endif {{ $data_filter->year }}
                    </span>
                </span>
            </h3>
            <div class="card-toolbar">
                <img src="{{ asset('assets/images/logo/shopee_lg.png') }}" class="h-75px" />
            </div>
        </div>
        <div class="card-header align-items-center border-0">
            <div class="align-items-start flex-column">
                <div class="input-group">
                    <select id="selectFilterMonth" name="month" class="form-select">
                        <option value="1" @if($data_filter->month == 1) {{"selected"}} @endif>Januari</option>
                        <option value="2" @if($data_filter->month == 2) {{"selected"}} @endif>Februari</option>
                        <option value="3" @if($data_filter->month == 3) {{"selected"}} @endif>Maret</option>
                        <option value="4" @if($data_filter->month == 4) {{"selected"}} @endif>April</option>
                        <option value="5" @if($data_filter->month == 5) {{"selected"}} @endif>Mei</option>
                        <option value="6" @if($data_filter->month == 6) {{"selected"}} @endif>Juni</option>
                        <option value="7" @if($data_filter->month == 7) {{"selected"}} @endif>Juli</option>
                        <option value="8" @if($data_filter->month == 8) {{"selected"}} @endif>Agustus</option>
                        <option value="9" @if($data_filter->month == 9) {{"selected"}} @endif>September</option>
                        <option value="10" @if($data_filter->month == 10) {{"selected"}} @endif>Oktober</option>
                        <option value="11" @if($data_filter->month == 11) {{"selected"}} @endif>November</option>
                        <option value="12" @if($data_filter->month == 12) {{"selected"}} @endif>Desember</option>
                    </select>
                    <input type="number" id="inputFilterYear" name="year" class="form-control" placeholder="Tahun"
                        @if(isset($data_filter->year)) value="{{ $data_filter->year }}" @else value="{{ old('year') }}"@endif>
                    <button id="btnFilterProses" type="submit" class="btn btn-primary">Terapkan</button>
                </div>
            </div>
            <div class="card-toolbar">
                <button id="btnBuatDokumen" type="button" class="btn btn-light me-2" data-bs-toggle="modal" data-bs-target="#modalBuatDokumen">
                    Buat Dokumen
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle">
                    <thead class="border">
                        <tr class="fs-8 fw-bolder text-muted">
                            <th rowspan="2" class="w-50px ps-3 pe-3 text-center">No</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3 text-center">No Dokumen</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3 text-center">Tanggal</th>
                            <th rowspan="2" class="w-50px ps-3 pe-3 text-center">Status</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3 text-center">Jml Item</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3 text-center">Terupdate</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3 text-center">Jml Selisih</th>
                            <th colspan="2" class="w-100px ps-3 pe-3 text-center">Action</th>
                        </tr>
                        <tr class="fs-8 fw-bolder text-muted">
                            <th class="w-50px ps-3 pe-3 text-center">Update</th>
                            <th class="w-50px ps-3 pe-3 text-center">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="border">
                        @forelse ($data_update_harga as $data)
                        <tr>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:center;">
                                <span class="fs-7 fw-bolder text-gray-800">{{ ((($data_page->current_page * $data_page->per_page) - $data_page->per_page) + $loop->iteration) }}</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:center;">
                                <span class="fs-7 fw-bolder text-gray-800">{{ trim($data->nomor_dokumen) }}</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:center;">
                                <span class="fs-7 fw-bolder text-gray-800">{{ date('d/m/Y', strtotime($data->tanggal)) }}</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:center;">
                                @if($data->status == 1)
                                <i class="fa fa-check text-success"></i>
                                @else
                                <i class="fa fa-minus-circle text-gray-400"></i>
                                @endif
                            </td>
                            <td class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data->item) }}
                                    <span class="fs-7 fw-bolder text-gray-500 ms-2">Item</span>
                                </span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data->update) }}
                                    <span class="fs-7 fw-bolder text-gray-500 ms-2">Item</span>
                                </span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data->selisih) }}</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:center;">
                                @if($data->status == 0)
                                <button id="btnUpdateHarga" class="btn btn-icon btn-sm btn-secondary" type="button" data-nomor_dokumen="{{ trim($data->nomor_dokumen) }}">
                                    <img alt="Logo" src="{{ asset('assets/images/logo/shopee.png') }}" class="h-20px"/>
                                </button>
                                @else
                                -
                                @endif
                            </td>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:center;">
                                @php
                                    $data_filter->page = $data_page->current_page;
                                    $data_filter->per_page = $data_page->per_page;

                                    $data_param = 
                                    base64_encode(
                                        json_encode(
                                        (object)array(
                                            'nomor_dokumen' =>  trim($data->nomor_dokumen),
                                            'filter' => $data_filter
                                            )
                                    ));
                                @endphp
                                
                                <a href="{{ route('online.updateharga.shopee.form.form', $data_param)}}" class="btn btn-icon btn-sm btn-primary" type="button" data-nomor_dokumen="{{ trim($data->nomor_dokumen) }}">
                                    <i class="fa fa-check text-white"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="pt-12 pb-12">
                                <div class="row text-center pe-10">
                                    <span class="svg-icon svg-icon-muted">
                                        <svg class="h-100px w-100px" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M21.7 18.9L18.6 15.8C17.9 16.9 16.9 17.9 15.8 18.6L18.9 21.7C19.3 22.1 19.9 22.1 20.3 21.7L21.7 20.3C22.1 19.9 22.1 19.3 21.7 18.9Z" fill="currentColor"/>
                                            <path opacity="0.3" d="M11 20C6 20 2 16 2 11C2 6 6 2 11 2C16 2 20 6 20 11C20 16 16 20 11 20ZM11 4C7.1 4 4 7.1 4 11C4 14.9 7.1 18 11 18C14.9 18 18 14.9 18 11C18 7.1 14.9 4 11 4ZM8 11C8 9.3 9.3 8 11 8C11.6 8 12 7.6 12 7C12 6.4 11.6 6 11 6C8.2 6 6 8.2 6 11C6 11.6 6.4 12 7 12C7.6 12 8 11.6 8 11Z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                </div>
                                <div class="row text-center pt-8">
                                    <span class="fs-6 fw-bolder text-gray-500">-  Tidak ada data yang ditampilkan -</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start mt-8">
        <div class="dataTables_length">
            <label>
                <select id="selectPerPageMasterData" name="per_page" class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                    <option value="10" @if($data_page->per_page == '10') {{'selected'}} @endif>10</option>
                    <option value="25" @if($data_page->per_page == '25') {{'selected'}} @endif>25</option>
                    <option value="50" @if($data_page->per_page == '50') {{'selected'}} @endif>50</option>
                    <option value="100" @if($data_page->per_page == '100') {{'selected'}} @endif>100</option>
                </select>
            </label>
        </div>
        <div class="dataTables_info" id="selectPerPageMasterDataInfo" role="status" aria-live="polite">Showing <span id="startRecordMasterData">{{ $data_page->from }}</span> to {{ $data_page->to }} of {{ $data_page->total }} records</div>
    </div>
    <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end mt-8">
        <div class="dataTables_paginate paging_simple_numbers" id="paginationMasterData">
            <ul class="pagination">
                @foreach ($data_page->links as $link)
                <li class="page-item @if($link->active == true) active @endif
                    @if($link->url == '') disabled @endif
                    @if($data_page->current_page == $link->label) active @endif">
                    @if($link->active == true)
                    <span class="page-link">{{ $link->label }}</span>
                    @else
                    <a href="#" class="page-link" data-page="@if(trim($link->url) != ''){{ explode("?page=" , $link->url)[1] }}@endif"
                        @if(trim($link->url) == '') disabled @endif>
                        @if(Str::contains(strtolower($link->label), 'previous'))
                        <i class="previous"></i>
                        @elseif(Str::contains(strtolower($link->label), 'next'))
                        <i class="next"></i>
                        @else
                        {{ $link->label }}
                        @endif
                    </a>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
<!--end::container-->

<div class="modal fade" tabindex="-2" id="modalBuatDokumen">
    <div class="modal-dialog">
        <div class="modal-content" id="modalBuatDokumenContent">
            <div class="modal-header">
                <h5 id="modalTitle" name="modalTitle" class="modal-title">Buat Dokumen</h5>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <span class="svg-icon svg-icon-muted svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path opacity="0.3" d="M6 19.7C5.7 19.7 5.5 19.6 5.3 19.4C4.9 19 4.9 18.4 5.3 18L18 5.3C18.4 4.9 19 4.9 19.4 5.3C19.8 5.7 19.8 6.29999 19.4 6.69999L6.7 19.4C6.5 19.6 6.3 19.7 6 19.7Z" fill="currentColor"/>
                            <path d="M18.8 19.7C18.5 19.7 18.3 19.6 18.1 19.4L5.40001 6.69999C5.00001 6.29999 5.00001 5.7 5.40001 5.3C5.80001 4.9 6.40001 4.9 6.80001 5.3L19.5 18C19.9 18.4 19.9 19 19.5 19.4C19.3 19.6 19 19.7 18.8 19.7Z" fill="currentColor"/>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body">
                <div class="fv-row">
                    <label class="form-label">Kode:</label>
                    <div class="input-group">
                        <input id="inputKodeUpdateHarga" name="kode" type="text" placeholder="Pilih Kode Update Harga" readonly
                            class="form-control" style="cursor: pointer;">
                        <button id="btnKodeUpdateHarga" name="btnKodeUpdateHarga" class="btn btn-icon btn-primary" type="button" role="button">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <div class="text-end">
                    <button id="btnCreateDocument" name="btnCreateDocument" class="btn btn-primary" type="button" role="button">Buat Dokumen</button>
                    <button id="btnFilterClose" name="btnClose" type="button" class="btn btn-light text-end" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="respon_container">
</div>

    @include('layouts.option.optionupdateharga')
@endsection

@push('scripts')
<script>
    const url = {
        'clossing_marketing': "{{ route('setting.default.clossing-marketing') }}",
        'daftar_update_harga': "{{ route('online.updateharga.shopee.daftar') }}",
        'buat_dokumen': "{{ route('online.updateharga.shopee.buat-dokumen') }}",
        'update_per_dokumen': "{{ route('online.updateharga.shopee.form.update.dokumen') }}",
    }
    const data_user = {
        'role_id': '{{ trim($data_user->role_id) }}'
    }
    const data_page = {
        'start_record': '{{ $data_page->from }}'
    }
    const data_filter = {
        'kode_lokasi': '{{ $data_filter->kode_lokasi }}'
    }
</script>
<script src="{{ asset('assets/js/suma/online/shopee/updateharga/daftar.js') }}?v={{ time() }}"></script>
@endpush
