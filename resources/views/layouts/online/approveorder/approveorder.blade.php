@extends('layouts.main.index')
@section('title', 'Orders')
@section('subtitle', 'Approve Order')
@section('container')
<div class="row g-0">
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Approve Order</span>
                <span class="text-muted fw-bold fs-7">Form approve order marketplace</span>
            </h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fs-7 fw-bolder text-muted bg-light">
                            <th class="ps-3 pe-3 w-200px text-start">Nomor Faktur</th>
                            <th class="ps-3 pe-3 w-100px text-start">Salesman</th>
                            <th class="ps-3 pe-3 w-100px text-start">Dealer</th>
                            <th class="ps-3 pe-3 min-w-150px text-start">Nomor Invoice</th>
                            <th class="ps-3 pe-3 w-150px text-center">Ekspedisi</th>
                            <th class="ps-3 pe-3 w-100px text-end">Total</th>
                            <th class="ps-3 pe-3 w-50px text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data_faktur as $data)
                        <tr>
                            <td class="ps-3 pe-3" style="text-align:left;vertical-align:center;">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-4">
                                        @if(strtoupper(trim($data->kode_dealer)) == config('constants.tokopedia.kode_dealer'))
                                        <span class="symbol-label bg-light-success">
                                            <span class="svg-icon svg-icon-2x svg-icon-success">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z" fill="currentColor"></path>
                                                    <path d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z" fill="currentColor"></path>
                                                </svg>
                                            </span>
                                        </span>
                                        @elseif(strtoupper(trim($data->kode_dealer)) == config('constants.shopee.kode_dealer'))
                                        <span class="symbol-label bg-light-warning">
                                            <span class="svg-icon svg-icon-2x svg-icon-warning">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z" fill="currentColor"></path>
                                                    <path d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z" fill="currentColor"></path>
                                                </svg>
                                            </span>
                                        </span>
                                        @else
                                        <span class="symbol-label bg-light-danger">
                                            <span class="svg-icon svg-icon-2x svg-icon-danger">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z" fill="currentColor"></path>
                                                    <path d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z" fill="currentColor"></path>
                                                </svg>
                                            </span>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="d-flex justify-content-start flex-column">
                                        <span class="fs-7 fw-bolder text-dark mb-1">{{ trim($data->nomor_faktur) }}</span>
                                        <span class="fs-7 fw-bold text-muted d-block">{{ date('d F Y', strtotime($data->tanggal_faktur)) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:left;vertical-align:center;">
                                <span class="fs-7 fw-bolder text-gray-800 mb-1">{{ trim($data->kode_sales) }}</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:left;vertical-align:center;">
                                <span class="fs-7 fw-bolder text-gray-800 mb-1">{{ trim($data->kode_dealer) }}</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:left;vertical-align:center;">
                                <span class="fs-7 fw-bolder text-gray-800 mb-1">{{ trim($data->nomor_invoice) }}</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:center;">
                                @if(strtoupper(trim($data->kode_dealer)) == config('constants.tokopedia.kode_dealer'))
                                <span class="fs-8 fw-boldest badge badge-light-success">{{ $data->kode_ekspedisi }}</span>
                                @elseif(strtoupper(trim($data->kode_dealer)) == config('constants.shopee.kode_dealer'))
                                <span class="fs-8 fw-boldest badge badge-light-warning">{{ $data->kode_ekspedisi }}</span>
                                @else
                                <span class="fs-8 fw-boldest badge badge-light-danger">{{ $data->kode_ekspedisi }}</span>
                                @endif
                            </td>
                            <td class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                <span class="fs-7 fw-bolder text-gray-800 mb-1">{{ number_format($data->total) }}</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:right;vertical-align:center;">
                                @if(strtoupper(trim($data->kode_dealer)) == config('constants.tokopedia.kode_dealer'))
                                <a href="{{ route('online.orders.approveorder.form.tokopedia', trim($data->nomor_invoice)) }}" role="button" class="btn btn-sm btn-icon btn-success">
                                    <i class="fa fa-check text-white"></i>
                                </a>
                                @elseif(strtoupper(trim($data->kode_dealer)) == config('constants.shopee.kode_dealer'))
                                <a href="{{ route('online.orders.approveorder.form.shopee', trim($data->nomor_invoice)) }}" role="button" class="btn btn-sm btn-icon btn-warning">
                                    <i class="fa fa-check text-white"></i>
                                </a>
                                @else
                                <a href="{{ route('online.orders.approveorder.form.internal', trim($data->nomor_faktur)) }}" role="button" class="btn btn-sm btn-icon btn-danger">
                                    <i class="fa fa-check text-white"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="pt-12 pb-12">
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
        </div>
    </div>
</div>
@endsection
