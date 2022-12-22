@extends('layouts.main.index')
@section('title','Orders')
@section('subtitle','Tracking Order')
@section('container')
    <div class="row g-0">
        <div class="card card-flush">
            <div class="card-header align-items-center border-0 mt-4 mb-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Tracking Order</span>
                    <span class="text-muted fw-bold fs-7">Daftar tracking order berdasarkan faktur penjualan bulan
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
                    @if(trim($data_filter->kode_sales) != '' || trim($data_filter->kode_dealer) != '' || trim($data_filter->nomor_faktur) != '')
                    <div class="d-flex align-items-center mt-4">
                        @if(trim($data_filter->kode_sales) != '')
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">SALESMAN : {{ strtoupper(trim($data_filter->kode_sales)) }}</span>
                        @endif
                        @if(trim($data_filter->kode_dealer) != '')
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">DEALER : {{ strtoupper(trim($data_filter->kode_dealer)) }}</span>
                        @endif
                        @if(trim($data_filter->nomor_faktur) != '')
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">FAKTUR : {{ strtoupper(trim($data_filter->nomor_faktur)) }}</span>
                        @endif
                    </div>
                    @endif
                </h3>
                <div class="card-toolbar">
                    <button id="btnFilterTrackingOrder" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFilter">
                        <i class="bi bi-funnel-fill fs-4 me-2"></i>Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    @foreach($data_tracking as $data)
<div class="card card-flush mt-6">
    <div class="card-body ribbon ribbon-top ribbon-vertical pt-6">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <span class="fw-bold fs-7 text-gray-600">Nomor Faktur:</span>
                    <span class="fw-bolder text-dark mt-1">{{ trim($data->nomor_faktur) }}</span>
                </div>
                <div class="row mt-6">
                    <span class="fw-bold fs-7 text-gray-600">Tanggal Faktur:</span>
                    <span class="fw-bolder text-dark mt-1">{{ date('j F Y', strtotime($data->tanggal)) }}</span>
                </div>
                <div class="row mt-6">
                    <span class="fw-bold fs-7 text-gray-600">Salesman:</span>
                    <div class="d-flex align-items-center flex-wrap mt-1">
                        <span class="fs-7 fw-boldest text-info d-flex align-items-center">{{ $data->kode_sales }}
                            <span class="bullet bullet-dot bg-info ms-2 me-2"></span>
                            <span class="fw-bolder text-dark">{{ $data->nama_sales }}</span>
                        </span>
                    </div>
                </div>
                <div class="row mt-6">
                    <span class="fw-bold fs-7 text-gray-600">Dealer:</span>
                    <div class="d-flex align-items-center flex-wrap mt-1">
                        <span class="fs-7 fw-boldest text-primary d-flex align-items-center">{{ $data->kode_dealer }}
                            <span class="bullet bullet-dot bg-primary ms-2 me-2"></span>
                            <span class="fw-bolder text-dark">{{ $data->nama_dealer }}</span>
                        </span>
                    </div>
                </div>
                <div class="row mt-6">
                    <span class="fw-bold fs-7 text-gray-600">Keterangan:</span>
                    <span class="fw-bolder text-dark mt-1">@if(trim($data->keterangan) == '') - @else {{ trim($data->keterangan) }} @endif</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="timeline mt-4">
                    <div class="timeline-item align-items-center mb-7">
                        <div class="timeline-line w-40px mt-6 mb-n12"></div>
                        <div class="timeline-icon" style="margin-left: 11px">
                            <span class="svg-icon svg-icon-2 @if($data->status_pengiriman >= 2) svg-icon-success @elseif($data->status_pengiriman + 1 == 2) svg-icon-info @endif">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10ZM6.39999 9.89999C6.99999 8.19999 8.40001 6.9 10.1 6.4C10.6 6.2 10.9 5.7 10.7 5.1C10.5 4.6 9.99999 4.3 9.39999 4.5C7.09999 5.3 5.29999 7 4.39999 9.2C4.19999 9.7 4.5 10.3 5 10.5C5.1 10.5 5.19999 10.6 5.39999 10.6C5.89999 10.5 6.19999 10.2 6.39999 9.89999ZM14.8 19.5C17 18.7 18.8 16.9 19.6 14.7C19.8 14.2 19.5 13.6 19 13.4C18.5 13.2 17.9 13.5 17.7 14C17.1 15.7 15.8 17 14.1 17.6C13.6 17.8 13.3 18.4 13.5 18.9C13.6 19.3 14 19.6 14.4 19.6C14.5 19.6 14.6 19.6 14.8 19.5Z" fill="currentColor"></path>
                                    <path d="M16 12C16 14.2 14.2 16 12 16C9.8 16 8 14.2 8 12C8 9.8 9.8 8 12 8C14.2 8 16 9.8 16 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10Z" fill="currentColor"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="timeline-content m-0">
                            <span class="fs-8 fw-boldest @if($data->status_pengiriman >= 2) text-success @elseif($data->status_pengiriman + 1 == 2) text-info @endif text-uppercase">
                                @if($data->status_pengiriman >= 2) FINISH @elseif($data->status_pengiriman + 1 == 2) ON-PROGRESS @endif
                            </span>
                            <span class="fs-7 fw-bolder d-block text-gray-500">Cetak Faktur</span>
                        </div>

                    </div>
                    <div class="timeline-item align-items-center mb-7">
                        <div class="timeline-line w-40px mt-6 mb-n12"></div>
                        <div class="timeline-icon" style="margin-left: 11px">
                            <span class="svg-icon svg-icon-2 @if($data->status_pengiriman >= 3) svg-icon-success @elseif($data->status_pengiriman + 1 == 3) svg-icon-info @endif">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10ZM6.39999 9.89999C6.99999 8.19999 8.40001 6.9 10.1 6.4C10.6 6.2 10.9 5.7 10.7 5.1C10.5 4.6 9.99999 4.3 9.39999 4.5C7.09999 5.3 5.29999 7 4.39999 9.2C4.19999 9.7 4.5 10.3 5 10.5C5.1 10.5 5.19999 10.6 5.39999 10.6C5.89999 10.5 6.19999 10.2 6.39999 9.89999ZM14.8 19.5C17 18.7 18.8 16.9 19.6 14.7C19.8 14.2 19.5 13.6 19 13.4C18.5 13.2 17.9 13.5 17.7 14C17.1 15.7 15.8 17 14.1 17.6C13.6 17.8 13.3 18.4 13.5 18.9C13.6 19.3 14 19.6 14.4 19.6C14.5 19.6 14.6 19.6 14.8 19.5Z" fill="currentColor"></path>
                                    <path d="M16 12C16 14.2 14.2 16 12 16C9.8 16 8 14.2 8 12C8 9.8 9.8 8 12 8C14.2 8 16 9.8 16 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10Z" fill="currentColor"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="timeline-content m-0">
                            <span class="fs-8 fw-boldest @if($data->status_pengiriman >= 3) text-success @elseif($data->status_pengiriman + 1 == 3) text-info @endif text-uppercase">
                                @if($data->status_pengiriman >= 3) FINISH @elseif($data->status_pengiriman + 1 == 3) ON-PROGRESS @endif
                            </span>
                            <span class="fs-7 fw-bolder d-block text-gray-500">Gudang mempersiapkan barang</span>
                            <span class="fs-7 fw-bolder text-gray-500"></span>
                        </div>
                    </div>
                    <div class="timeline-item align-items-center mb-7">
                        <div class="timeline-line w-40px mt-6 mb-n12"></div>
                        <div class="timeline-icon" style="margin-left: 11px">
                            <span class="svg-icon svg-icon-2 @if($data->status_pengiriman >= 4) svg-icon-success @elseif($data->status_pengiriman + 1 == 4) svg-icon-info @endif">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10ZM6.39999 9.89999C6.99999 8.19999 8.40001 6.9 10.1 6.4C10.6 6.2 10.9 5.7 10.7 5.1C10.5 4.6 9.99999 4.3 9.39999 4.5C7.09999 5.3 5.29999 7 4.39999 9.2C4.19999 9.7 4.5 10.3 5 10.5C5.1 10.5 5.19999 10.6 5.39999 10.6C5.89999 10.5 6.19999 10.2 6.39999 9.89999ZM14.8 19.5C17 18.7 18.8 16.9 19.6 14.7C19.8 14.2 19.5 13.6 19 13.4C18.5 13.2 17.9 13.5 17.7 14C17.1 15.7 15.8 17 14.1 17.6C13.6 17.8 13.3 18.4 13.5 18.9C13.6 19.3 14 19.6 14.4 19.6C14.5 19.6 14.6 19.6 14.8 19.5Z" fill="currentColor"></path>
                                    <path d="M16 12C16 14.2 14.2 16 12 16C9.8 16 8 14.2 8 12C8 9.8 9.8 8 12 8C14.2 8 16 9.8 16 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10Z" fill="currentColor"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="timeline-content m-0">
                            <span class="fs-8 fw-boldest @if($data->status_pengiriman >= 4) text-success @elseif($data->status_pengiriman + 1 == 4) text-info @endif text-uppercase">
                                @if($data->status_pengiriman >= 4) FINISH @elseif($data->status_pengiriman + 1 == 4) ON-PROGRESS @endif
                            </span>
                            <span class="fs-7 fw-bolder d-block text-gray-500">Cetak surat jalan</span>
                            <span class="fs-7 fw-bolder text-gray-500"></span>
                        </div>
                    </div>
                    <div class="timeline-item align-items-center mb-7">
                        <div class="timeline-line w-40px mt-6 mb-n12"></div>
                        <div class="timeline-icon" style="margin-left: 11px">
                            <span class="svg-icon svg-icon-2 @if($data->status_pengiriman >= 5) svg-icon-success @elseif($data->status_pengiriman + 1 == 5) svg-icon-info @endif">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10ZM6.39999 9.89999C6.99999 8.19999 8.40001 6.9 10.1 6.4C10.6 6.2 10.9 5.7 10.7 5.1C10.5 4.6 9.99999 4.3 9.39999 4.5C7.09999 5.3 5.29999 7 4.39999 9.2C4.19999 9.7 4.5 10.3 5 10.5C5.1 10.5 5.19999 10.6 5.39999 10.6C5.89999 10.5 6.19999 10.2 6.39999 9.89999ZM14.8 19.5C17 18.7 18.8 16.9 19.6 14.7C19.8 14.2 19.5 13.6 19 13.4C18.5 13.2 17.9 13.5 17.7 14C17.1 15.7 15.8 17 14.1 17.6C13.6 17.8 13.3 18.4 13.5 18.9C13.6 19.3 14 19.6 14.4 19.6C14.5 19.6 14.6 19.6 14.8 19.5Z" fill="currentColor"></path>
                                    <path d="M16 12C16 14.2 14.2 16 12 16C9.8 16 8 14.2 8 12C8 9.8 9.8 8 12 8C14.2 8 16 9.8 16 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10Z" fill="currentColor"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="timeline-content m-0">
                            <span class="fs-8 fw-boldest @if($data->status_pengiriman >= 5) text-success @elseif($data->status_pengiriman + 1 == 5) text-info @endif text-uppercase">
                                @if($data->status_pengiriman >= 5) FINISH @elseif($data->status_pengiriman + 1 == 5) ON-PROGRESS @endif
                            </span>
                            <span class="fs-7 fw-bolder d-block text-gray-500">Serah terima dengan ekspedisi</span>
                            <span class="fs-7 fw-bolder text-gray-500"></span>
                        </div>
                    </div>
                    <div class="timeline-item align-items-center">
                        <div class="timeline-line w-40px"></div>
                        <div class="timeline-icon" style="margin-left: 11px">
                            <span class="svg-icon svg-icon-2 @if($data->status_pengiriman >= 6) svg-icon-success @elseif($data->status_pengiriman + 1 == 6) svg-icon-info @endif">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10ZM6.39999 9.89999C6.99999 8.19999 8.40001 6.9 10.1 6.4C10.6 6.2 10.9 5.7 10.7 5.1C10.5 4.6 9.99999 4.3 9.39999 4.5C7.09999 5.3 5.29999 7 4.39999 9.2C4.19999 9.7 4.5 10.3 5 10.5C5.1 10.5 5.19999 10.6 5.39999 10.6C5.89999 10.5 6.19999 10.2 6.39999 9.89999ZM14.8 19.5C17 18.7 18.8 16.9 19.6 14.7C19.8 14.2 19.5 13.6 19 13.4C18.5 13.2 17.9 13.5 17.7 14C17.1 15.7 15.8 17 14.1 17.6C13.6 17.8 13.3 18.4 13.5 18.9C13.6 19.3 14 19.6 14.4 19.6C14.5 19.6 14.6 19.6 14.8 19.5Z" fill="currentColor"></path>
                                    <path d="M16 12C16 14.2 14.2 16 12 16C9.8 16 8 14.2 8 12C8 9.8 9.8 8 12 8C14.2 8 16 9.8 16 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10Z" fill="currentColor"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="timeline-content m-0">
                            <span class="fs-8 fw-boldest @if($data->status_pengiriman >= 6) text-success @elseif($data->status_pengiriman + 1 == 6) text-info @endif text-uppercase">
                                @if($data->status_pengiriman >= 6) FINISH @elseif($data->status_pengiriman + 1 == 6) ON-PROGRESS @endif
                            </span>
                            <span class="fs-7 fw-bolder d-block text-gray-500">Toko atau ekspedisi terima barang</span>
                            <span class="fs-7 fw-bolder text-gray-500"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="separator my-5"></div>
        <a href="{{ route('orders.trackingorder.view', trim($data->nomor_faktur)) }}" class="btn btn-primary" id="viewPof" role="button">
            <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i> Detail Tracking
        </a>
    </div>
</div>
@endforeach


    <div class="row">
        <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start mt-8">
            <div class="dataTables_length">
                <label>
                    <select id="selectPerPage" name="per_page" class="form-select form-select-sm" data-control="select2" data-hide-search="true"
                        onchange="this.form.submit()">
                        <option value="10" @if($data_page->per_page == '10') {{'selected'}} @endif>10</option>
                        <option value="25" @if($data_page->per_page == '25') {{'selected'}} @endif>25</option>
                        <option value="50" @if($data_page->per_page == '50') {{'selected'}} @endif>50</option>
                        <option value="100" @if($data_page->per_page == '100') {{'selected'}} @endif>100</option>
                    </select>
                </label>
            </div>
            <div class="dataTables_info" id="selectPerPageDealerInfo" role="status" aria-live="polite">Showing <span id="startRecordSalesman">{{ $data_page->from }}</span> to {{ $data_page->to }} of {{ $data_page->total }} records</div>
        </div>
        <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end mt-8">
            <div class="dataTables_paginate paging_simple_numbers" id="kt_datatable_example_5_paginate">
                <ul class="pagination">
                    @foreach ($data_page->links as $link)
                    <li class="page-item @if($link->active == true) active @endif
                        @if($link->url == '') disabled @endif
                        @if($data_page->current_page == $link->label) active @endif">
                        @if($link->active == true)
                        <span class="page-link">{{ $link->label }}</span>
                        @else
                        <a href="#" class="page-link" data-page="{{ $link->url }}">
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

    <div class="modal fade" tabindex="-1" id="modalFilter">
        <div class="modal-dialog">
            <div class="modal-content" id="modalContentFilter">
                <form id="formFilter" name="formFilter" autofill="off" autocomplete="off" method="get" action="{{ route('orders.trackingorder.daftar') }}">
                    <div class="modal-header">
                        <h5 id="modalTitle" name="modalTitle" class="modal-title">Filter Tracking Order</h5>
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
                            <label class="form-label required">Bulan:</label>
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
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label required">Tahun:</label>
                            <input type="number" id="inputFilterYear" name="year" class="form-control" placeholder="Tahun"
                                @if(isset($data_filter->year)) value="{{ $data_filter->year }}" @else value="{{ old('year') }}"@endif>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label">Salesman:</label>
                            <div class="input-group">
                                <input id="inputFilterSalesman" name="salesman" type="text" class="form-control" style="cursor: pointer;" placeholder="Semua Salesman" readonly
                                    @if(isset($data_filter->kode_sales)) value="{{ $data_filter->kode_sales }}" @else value="{{ old('kode_sales') }}"@endif>
                                @if($data_user->role_id != 'MD_H3_SM')
                                    @if($data_user->role_id != 'D_H3')
                                    <button id="btnFilterPilihSalesman" name="btnFilterPilihSalesman" class="btn btn-icon btn-primary" type="button"
                                        data-toggle="modal" data-target="#salesmanSearchModal">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label">Dealer:</label>
                            <div class="input-group">
                                <input id="inputFilterDealer" name="dealer" type="search" class="form-control" style="cursor: pointer;" placeholder="Semua Dealer" readonly
                                    @if(isset($data_filter->kode_dealer)) value="{{ $data_filter->kode_dealer }}" @else value="{{ old('kode_dealer') }}"@endif>
                                @if($data_user->role_id != 'D_H3')
                                <button id="btnFilterPilihDealer" name="btnFilterPilihDealer" class="btn btn-icon btn-primary" type="button"
                                    data-toggle="modal" data-target="#dealerSearchModal">
                                    <i class="fa fa-search"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label">Nomor Faktur:</label>
                            <div class="input-group has-validation mb-2">
                                <input id="inputFilterNomorFaktur" name="nomor_faktur" type="search" class="form-control" placeholder="Semua Nomor Faktur"
                                    @if(isset($data_filter->nomor_faktur)) value="{{ $data_filter->nomor_faktur }}" @else value="{{ old('nomor_faktur') }}"@endif>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button id="btnFilterReset" class="btn btn-danger" role="button">Reset Filter</button>
                        <div class="text-end">
                            <button id="btnFilterProses" type="submit" class="btn btn-primary">Terapkan</button>
                            <button id="btnFilterClose" name="btnClose" type="button" class="btn btn-light text-end" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('layouts.option.optionsalesman')
    @include('layouts.option.optiondealer')

    @push('scripts')
        <script src="{{ asset('assets/js/suma/orders/trackingorder/trackingorder.js') }}?v={{ time() }}"></script>
        <script src="{{ asset('assets/js/suma/option/option.js') }}"></script>
        <script type="text/javascript">
            const url = {
                'tracking_order': "{{ route('orders.trackingorder.daftar') }}",
                'setting_clossing_marketing': "{{ route('setting.setting-clossing-marketing') }}",
            }
            const data_filter = {
                'month': '{{ $data_filter->month }}',
                'year': '{{ $data_filter->year }}',
                'kode_sales': '{{ $data_filter->kode_sales }}',
                'kode_dealer': '{{ $data_filter->kode_dealer }}',
                'nomor_faktur': '{{ $data_filter->nomor_faktur }}',
            }
            const data_page = {
                'start_record': '{{ $data_page->from }}'
            }
        </script>
    @endpush
@endsection
