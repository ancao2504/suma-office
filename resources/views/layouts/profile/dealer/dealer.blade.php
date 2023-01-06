@extends('layouts.main.index')
@section('title','Profile')
@section('subtitle','Dealer')
@section('container')
<div class="row g-0">
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Dealer</span>
                <span class="text-muted fw-bold fs-7">Daftar dealer suma honda</span>
                <div class="d-flex flex-wrap mt-2">
                    @if(isset($data_filter->kode_dealer) && trim($data_filter->kode_dealer) != '')
                    <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">FILTER : {{ trim($data_filter->kode_dealer) }}</span>
                    @endif
                </div>
            </h3>
        </div>
        <div class="card-body pt-0">
            <div class="d-flex align-items-center position-relative my-1">
                <span class="svg-icon svg-icon-1 position-absolute ms-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                    </svg>
                </span>
                <input id="searchFilterDealer" name="search" type="text" class="form-control ps-14" placeholder="Search Kode Dealer"
                    value="@if(isset($data_filter->kode_dealer)){{ trim($data_filter->kode_dealer) }}@endif">
                <br>
                <div class="d-flex align-items-center">
                    <button id="btnFilterProses" class="btn btn-sm btn-primary m-2" type="submit" role="button">Cari</button>
                    <a id="btnFilterReset" href="{{ route('profile.dealer.daftar') }}" class="btn btn-sm btn-danger" role="button">Reset</a>
                </div>
            </div>
            @forelse($data_dealer as $data)
            <div class="d-flex align-items-center mt-10">
                <span class="symbol symbol-50px me-5">
                    @if ($data->status_limit == 'LIMIT_PIUTANG')
                    <span class="symbol-label fs-5 fw-bolder bg-light-success text-success">{{ trim($data->kode_dealer) }}</span>
                    @elseif($data->status_limit == 'LIMIT_SALES')
                    <span class="symbol-label fs-5 fw-bolder bg-light-warning text-warning">{{ trim($data->kode_dealer) }}</span>
                    @else
                    <span class="symbol-label fs-5 fw-bolder bg-light-danger text-danger">{{ trim($data->kode_dealer) }}</span>
                    @endif
                </span>
                <div class="flex-grow-1">
                    <div class="flex-grow-1">
                        <a href="{{ route('profile.dealer.form', trim($data->kode_dealer)) }}" class="text-dark fw-bolder text-hover-primary fs-6">{{ trim($data->nama_dealer) }}</a>
                        <span class="text-muted d-block fw-bold">{{ trim($data->kabupaten) }}</span>
                        @if ($data->sts == 'CHANNEL')
                            <span class="badge badge-light-primary fw-bolder badge-sm">CHANNEL</span>
                        @else
                            <span class="badge badge-light-danger fw-bolder badge-sm">NON-CHANNEL</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('profile.dealer.form', trim($data->kode_dealer)) }}" class="btn btn-icon btn-primary me-2 mb-2" role="button">
                    <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                </a>
            </div>
            <div class="separator my-5"></div>
            @empty
            <div class="row text-center pt-12 pe-10">
                <span class="svg-icon svg-icon-muted">
                    <svg class="h-100px w-100px" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M21.7 18.9L18.6 15.8C17.9 16.9 16.9 17.9 15.8 18.6L18.9 21.7C19.3 22.1 19.9 22.1 20.3 21.7L21.7 20.3C22.1 19.9 22.1 19.3 21.7 18.9Z" fill="currentColor"/>
                        <path opacity="0.3" d="M11 20C6 20 2 16 2 11C2 6 6 2 11 2C16 2 20 6 20 11C20 16 16 20 11 20ZM11 4C7.1 4 4 7.1 4 11C4 14.9 7.1 18 11 18C14.9 18 18 14.9 18 11C18 7.1 14.9 4 11 4ZM8 11C8 9.3 9.3 8 11 8C11.6 8 12 7.6 12 7C12 6.4 11.6 6 11 6C8.2 6 6 8.2 6 11C6 11.6 6.4 12 7 12C7.6 12 8 11.6 8 11Z" fill="currentColor"/>
                    </svg>
                </span>
            </div>
            <div class="row text-center pt-8 pb-12">
                <span class="fs-6 fw-bolder text-gray-500">-  Tidak ada data yang ditampilkan -</span>
            </div>
            @endforelse
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start mt-8">
            <div class="dataTables_length">
                <label>
                    <select id="selectPerPageDealer" name="per_page" class="form-select form-select-sm" data-control="select2" data-hide-search="true"
                        onchange="this.form.submit()">
                        <option value="10" @if($data_page->per_page == '10') {{'selected'}} @endif>10</option>
                        <option value="25" @if($data_page->per_page == '25') {{'selected'}} @endif>25</option>
                        <option value="50" @if($data_page->per_page == '50') {{'selected'}} @endif>50</option>
                        <option value="100" @if($data_page->per_page == '100') {{'selected'}} @endif>100</option>
                    </select>
                </label>
            </div>
            <div class="dataTables_info" id="selectPerPageDealerInfo" role="status" aria-live="polite">Showing <span id="startRecordDealer">{{ $data_page->from }}</span> to {{ $data_page->to }} of {{ $data_page->total }} records</div>
        </div>
        <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end mt-8">
            <div class="dataTables_paginate paging_simple_numbers" id="paginationDealer">
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
</div>
@push('scripts')
    <script type="text/javascript">
        const data_page = {
            'start_record': '{{ $data_page->from }}'
        }
    </script>
    <script src="{{ asset('assets/js/suma/profile/dealer.js') }}?v={{ time() }}"></script>
@endpush
@endsection
