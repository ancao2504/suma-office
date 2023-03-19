@extends('layouts.main.index')
@section('title','Tokopedia')
@section('subtitle','Serah Terima')
@section('container')
<div class="row g-0">
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Serah Terima</span>
                <span class="text-muted fw-bold fs-7">Daftar serah terima ekspedisi marketplace tokopedia</span>
                <span class="text-muted fw-bold fs-7 mt-2">Periode
                    <span class="text-dark fw-bolder fs-7">{{ date('d F Y', strtotime($data_filter->start_date)) }}</span> s/d
                    <span class="text-dark fw-bolder fs-7">{{ date('d F Y', strtotime($data_filter->end_date)) }}</span>
                </span>
            </h3>
        </div>
        <div class="card-header align-items-center border-0">
            <div class="align-items-start flex-column">
                <div class="input-group">
                    <input id="inputStartDate" name="start_date" class="form-control w-md-200px" placeholder="Dari Tanggal"
                        value="{{ $data_filter->start_date }}">
                    <span class="input-group-text">s/d</span>
                    <input id="inputEndDate" name="end_date" class="form-control w-md-200px" placeholder="Sampai Dengan"
                        value="{{ $data_filter->end_date }}">
                    <button id="btnFilterMasterData" class="btn btn-icon btn-primary" type="button">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="position-relative w-md-200px me-md-2">
                    <span class="svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                        </svg>
                    </span>
                    <input id="inputSearch" name="search" type="text" class="form-control ps-10" value="{{ $data_filter->search }}"
                        oninput="this.value = this.value.toUpperCase()" placeholder="Search">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bolder text-muted">
                            <th class="w-25px">No</th>
                            <th class="min-w-200px">No Dokumen</th>
                            <th class="min-w-150px">Ekspedisi</th>
                            <th class="min-w-150px text-center">Mulai</th>
                            <th class="min-w-150px text-center">Selesai</th>
                            <th class="min-w-100px text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data_serah_terima as $data)
                        <tr>
                            <td>
                                <span class="fs-7 fw-boldest text-gray-800">{{ ((($data_page->current_page * $data_page->per_page) - $data_page->per_page) + $loop->iteration) }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="d-flex justify-content-start flex-column">
                                        <span class="fs-7 fw-boldest text-gray-800">{{ strtoupper(trim($data->nomor_dokumen)) }}</span>
                                        <span class="fs-7 fw-bolder text-gray-600 d-block">{{ date('d F Y', strtotime($data->tanggal)) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fs-7 fw-boldest text-danger d-block">{{ trim($data->nama_ekspedisi) }}</span>
                                <span class="fs-7 fw-bolder text-muted d-block">{{ trim($data->kode_ekspedisi) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="fs-7 fw-boldest text-gray-800 d-block">{{ date('d F Y', strtotime($data->tanggal_mulai)) }}</span>
                                <span class="fs-7 fw-bolder text-gray-600 d-block">{{ trim($data->jam_mulai) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="fs-7 fw-boldest text-gray-800 d-block">{{ date('d F Y', strtotime($data->tanggal_selesai)) }}</span>
                                <span class="fs-7 fw-bolder text-gray-600 d-block">{{ trim($data->jam_selesai) }}</span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end flex-shrink-0">
                                    <a href="{{ route('online.serahterima.form.form', strtoupper(trim($data->nomor_dokumen))) }}" class="btn btn-icon btn-sm btn-primary" type="button">
                                        <i class="fa fa-check text-white"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="pt-12 pb-12">
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
@push('scripts')
<script>
    const data_filter = {
        'start_date': '{{ trim($data_filter->start_date) }}',
        'end_date': '{{ trim($data_filter->end_date) }}',
        'search': '{{ trim($data_filter->search) }}',
    }
    const data_user = {
        'role_id': '{{ trim($data_user->role_id) }}'
    }
    const data_page = {
        'start_record': '{{ $data_page->from }}'
    }
</script>
<script src="{{ asset('assets/js/suma/online/serahterima/daftar.js') }}?v={{ time() }}"></script>
@endpush
@endsection
