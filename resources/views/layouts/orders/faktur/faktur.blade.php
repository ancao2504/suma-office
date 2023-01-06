@extends('layouts.main.index')
@section('title','Orders')
@section('subtitle','Faktur')
@section('container')
<div class="row g-0">
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Faktur</span>
                <span class="text-muted fw-bold fs-7">Daftar faktur penjualan
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
                <div class="d-flex flex-grow mt-2">
                    @if(trim($data_filter->kode_sales) != '')
                    <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">SALESMAN : {{ strtoupper(trim($data_filter->kode_sales)) }}</span>
                    @endif
                    @if(trim($data_filter->kode_dealer) != '')
                    <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">DEALER : {{ strtoupper(trim($data_filter->kode_dealer)) }}</span>
                    @endif
                    @if(trim($data_filter->nomor_faktur) != '')
                    <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">FAKTUR : {{ strtoupper(trim($data_filter->nomor_faktur)) }}</span>
                    @endif
                </div>
                @endif
            </h3>
            <div class="card-toolbar">
                <button id="btnFilterMasterData" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFilter">
                    <i class="bi bi-funnel-fill fs-4 me-2"></i>Filter
                </button>
            </div>
        </div>
    </div>

    @if(strtoupper(trim($data_device->device)) == 'DESKTOP')
    <div class="card card-flush mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle">
                    <thead class="border">
                        <tr class="fs-8 fw-bolder text-muted">
                            <th class="w-50px ps-3 pe-3 text-center">No</th>
                            <th class="w-200px ps-3 pe-3 text-center">No Faktur</th>
                            <th class="w-50px ps-3 pe-3 text-center">AFO</th>
                            <th class="w-50px ps-3 pe-3 text-center">Cetak</th>
                            <th class="w-50px ps-3 pe-3 text-center">Sales</th>
                            <th class="w-50px ps-3 pe-3 text-center">Dealer</th>
                            <th class="w-50px ps-3 pe-3 text-center">TMKR</th>
                            <th class="min-w-150px ps-3 pe-3 text-center">Keterangan</th>
                            <th class="w-50px ps-3 pe-3 text-center">TPC</th>
                            <th class="w-50px ps-3 pe-3 text-center">TOP</th>
                            <th class="w-50px ps-3 pe-3 text-center">BO</th>
                            <th class="w-100px text-end ps-3 pe-3 text-center">Total</th>
                            <th class="w-50px ps-3 pe-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="border">
                        @forelse($data_faktur as $data)
                        <tr>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                <span class="fs-7 fw-bold text-gray-800">{{ ((($data_page->current_page * $data_page->per_page) - $data_page->per_page) + $loop->iteration) }}</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                <div class="row align-items-start">
                                    <a href="{{ route('orders.faktur.form', trim($data->nomor_faktur)) }}" class="fs-7 fw-boldest text-gray-800 text-hover-primary">{{ trim($data->nomor_faktur) }}</a>
                                    <span class="fs-7 fw-bolder text-muted">{{ date('d F Y', strtotime($data->tanggal)) }}</span>
                                </div>
                                <div class="row align-items-end">
                                    <div class="mt-6"></div>
                                </div>
                                <div class="row align-items-end">
                                    <span class="fs-7 fw-bolder text-gray-800">{{ trim($data->nomor_pof) }}</span>
                                    <div class="d-flex align-items-center flex-wrap">
                                        <span class="fs-8 fw-boldest text-info d-flex align-items-center">{{ trim($data->kode_beli) }}
                                            <span class="bullet bullet-dot bg-info ms-2 me-2"></span>
                                            @if($data->jenis_order == 'H')
                                            <span class="fs-8 fw-boldest text-danger">HOTLINE</span>
                                            @elseif($data->jenis_order == 'P')
                                            <span class="fs-8 fw-boldest text-info">PMO</span>
                                            @else
                                                @if($data->status_pof == 1)
                                                <span class="fs-8 fw-boldest text-primary">POF</span>
                                                @else
                                                <span class="fs-8 fw-boldest text-success">REGULER</span>
                                                @endif
                                            @endif
                                        </span>
                                    </div>
                                    <span class="fs-8 fw-boldest text-danger mt-6">@if(trim($data->usertime) == '') - @else {{ $data->usertime }} @endif</span>
                                </div>
                            </td>
                            <td class="ps-3 pe-3 text-center" style="text-align:center;vertical-align:top;">
                                @if((int)$data->approve_online == 1)
                                <i class="fa fa-check text-success"></i>
                                @endif
                            </td>
                            <td class="ps-3 pe-3 text-center" style="text-align:center;vertical-align:top;">
                                @if((int)$data->status_cetak == 1)
                                <i class="fa fa-check text-success"></i>
                                @endif
                            </td>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                <span class="fs-8 fw-boldest text-info text-uppercase">{{ trim($data->kode_sales) }}</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                <span class="fs-8 fw-boldest text-primary text-uppercase">{{ trim($data->kode_dealer) }}</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                <span class="fs-8 fw-boldest text-danger text-uppercase">{{ trim($data->kode_telemarketing) }}</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                <p class="fs-7 fw-bold text-gray-800">@if(trim($data->keterangan) == '') - @else {{ $data->keterangan }} @endif</p>
                            </td>
                            <td class="ps-3 pe-3 text-center" style="text-align:center;vertical-align:top;">
                                @if(trim($data->kode_tpc) == '14')
                                <span class="fs-8 fw-boldest text-info">14</span>
                                @else
                                <span class="fs-8 fw-boldest text-danger">20</span>
                                @endif
                            </td>
                            <td class="ps-3 pe-3 text-center" style="text-align:center;vertical-align:top;">
                                <span class="fs-8 fw-boldest text-primary">{{ number_format($data->umur_faktur) }}</span>
                            </td>
                            <td class="ps-3 pe-3 text-center" style="text-align:center;vertical-align:top;">
                                @if($data->bo == 'B')
                                <span class="fs-8 fw-boldest text-danger">BO</span>
                                @else
                                <span class="fs-8 fw-boldest text-warning">TIDAK BO</span>
                                @endif
                            </td>
                            <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                                <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data->total) }}</span>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                <a href="{{ route('orders.faktur.form', trim($data->nomor_faktur)) }}" class="btn btn-icon btn-primary btn-sm">
                                    <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="13" class="pt-12 pb-12">
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
    @else
    @forelse($data_faktur as $data)
    <div class="card card-flush mt-6">
        <div class="card-body ribbon ribbon-top ribbon-vertical pt-5">
            @if (trim($data->kode_tpc) == '14')
            <div class="ribbon-label fw-bold bg-primary">
                <i class="bi bi-percent fs-2 text-white"></i>
            </div>
            @else
            <div class="ribbon-label fw-bold bg-danger">
                <i class="bi bi-currency-dollar fs-2 text-white"></i>
            </div>
            @endif
            <div class="row mt-4">
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
                <div class="col-6">
                    <div class="row">
                        <span class="fw-bold fs-7 text-gray-600">Jenis Order:</span>
                        @if ($data->jenis_order == 'R')
                        <span class="fs-8 fw-boldest text-success text-uppercase">REGULER</span>
                        @else
                        <span class="fs-8 fw-boldest text-danger text-uppercase">HOTLINE</span>
                        @endif
                    </div>
                </div>
                <div class="col-6">
                    <div class="row">
                        <span class="fw-bold fs-7 text-gray-600">Status BO:</span>
                        @if ($data->bo == 'B')
                        <span class="fs-8 fw-boldest text-danger text-uppercase">BO</span>
                        @else
                        <span class="fs-8 fw-boldest text-warning text-uppercase">TIDAK BO</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row mt-6">
                <div class="col-6">
                    <div class="row">
                        <span class="fw-bold fs-7 text-gray-600">TOP:</span>
                        <div class="d-flex align-items-center flex-wrap mt-1">
                            <span class="fs-7 fw-bolder text-dark d-flex align-items-center">{{ trim($data->umur_faktur) }} Hari
                                <span class="bullet bullet-dot bg-danger ms-2 me-2"></span>
                                <span class="fw-bolder text-danger">{{ $data->tanggal_akhir_faktur }}</span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="row">
                        <span class="fw-bold fs-7 text-gray-600">Total:</span>
                        <span class="fw-boldest fs-7 text-danger mt-1">Rp. {{ number_format($data->total) }}</span>
                    </div>
                </div>
            </div>
            <div class="row mt-6">
                <span class="fw-bold fs-7 text-gray-600">Keterangan:</span>
                <span class="fw-bolder text-dark mt-1">@if(trim($data->keterangan) == '') - @else {{ trim($data->keterangan) }} @endif</span>
            </div>
            <div class="separator my-5"></div>
            <a href="{{ route('orders.faktur.form', trim($data->nomor_faktur)) }}" class="btn btn-primary mb-2" role="button">
                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i> Detail Faktur
            </a>
        </div>
    </div>
    @empty
    <div class="card card-flush mt-4">
        <div class="card-body d-flex flex-column justify-content-center pe-0 h-300px">
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
        </div>
    </div>
    @endforelse
    @endif
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

<div class="modal fade" tabindex="-2" id="modalFilter">
    <div class="modal-dialog">
        <div class="modal-content" id="modalFilterContent">
            <div class="modal-header">
                <h5 id="modalTitle" name="modalTitle" class="modal-title">Filter Faktur</h5>
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
                        <input id="inputFilterSalesman" name="salesman" type="text" placeholder="Semua Salesman" readonly
                            class="form-control @if(trim($data_user->role_id) == 'D_H3' || trim($data_user->role_id) == 'MD_H3_SM') form-control-solid @endif"
                            @if(trim($data_user->role_id) != 'D_H3' && trim($data_user->role_id) != 'MD_H3_SM') style="cursor: pointer;" @endif
                            @if(isset($data_filter->kode_sales)) value="{{ $data_filter->kode_sales }}" @else value="{{ old('kode_sales') }}"@endif>
                        @if($data_user->role_id != 'MD_H3_SM')
                            @if($data_user->role_id != 'D_H3')
                            <button id="btnFilterPilihSalesman" name="btnFilterPilihSalesman" class="btn btn-icon btn-primary" type="button" role="button">
                                <i class="fa fa-search"></i>
                            </button>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="fv-row mt-8">
                    <label class="form-label">Dealer:</label>
                    <div class="input-group">
                        <input id="inputFilterDealer" name="dealer" type="text" placeholder="Semua Dealer" readonly
                            class="form-control @if(trim($data_user->role_id) == 'D_H3') form-control-solid @endif"
                            @if(trim($data_user->role_id) != 'D_H3') style="cursor: pointer;" @endif
                            @if(isset($data_filter->kode_dealer)) value="{{ $data_filter->kode_dealer }}" @else value="{{ old('kode_dealer') }}"@endif>
                        @if($data_user->role_id != 'D_H3')
                        <button id="btnFilterPilihDealer" name="btnFilterPilihDealer" class="btn btn-icon btn-primary" type="button" role="button">
                            <i class="fa fa-search"></i>
                        </button>
                        @endif
                    </div>
                </div>
                <div class="fv-row mt-8">
                    <label class="form-label">Nomor Faktur:</label>
                    <div class="input-group has-validation mb-2">
                        <input id="inputFilterNomorFaktur" name="nomor_faktur" type="text" class="form-control" placeholder="Semua Nomor Faktur"
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
        </div>
    </div>
</div>

@include('layouts.option.optionsalesman')
@include('layouts.option.optiondealer')
@include('layouts.option.optiondealersalesman')

@push('scripts')
    <script>
        const url = {
            'clossing_marketing': "{{ route('setting.default.clossing-marketing') }}",
        }
        const data_filter = {
            'year': '{{ trim($data_filter->year) }}',
            'month': '{{ trim($data_filter->month) }}',
            'salesman': '{{ trim($data_filter->kode_sales) }}',
            'dealer': '{{ trim($data_filter->kode_dealer) }}',
            'nomor_faktur': '{{ trim($data_filter->nomor_faktur) }}',
        }
        const data_user = {
            'role_id': '{{ trim($data_user->role_id) }}'
        }
        const data_page = {
            'start_record': '{{ $data_page->from }}'
        }
    </script>
    <script src="{{ asset('assets/js/suma/orders/faktur/faktur.js') }}?v={{ time() }}"></script>
@endpush
@endsection
