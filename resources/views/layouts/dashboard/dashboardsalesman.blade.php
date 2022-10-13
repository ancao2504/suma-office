@extends('layouts.main.index')
@section('title','Home')
@section('subtitle','Dashboard')
@section('container')
    <form id="formDashboardSalesman" action="{{ route('dashboard.dashboard-salesman') }}" method="get">
        <div class="card card-flush">
            <div class="card-header align-items-center border-0 mt-4 mb-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Sales Performance</span>
                    <span class="text-muted fw-bold fs-7">Dashboard Salesman
                        @if($month == 1) Januari
                        @elseif($month == 2) Februari
                        @elseif($month == 3) Maret
                        @elseif($month == 4) April
                        @elseif($month == 5) Mei
                        @elseif($month == 6) Juni
                        @elseif($month == 7) Juli
                        @elseif($month == 8) Agustus
                        @elseif($month == 9) September
                        @elseif($month == 10) Oktober
                        @elseif($month == 11) November
                        @elseif($month == 12) Desember
                        @endif {{ $year }}
                    </span>
                </h3>
                <div class="card-toolbar">
                    <button class="btn btn-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="bi bi-funnel-fill fs-4 me-2"></i>Filter
                    </button>

                    <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_6244763d95a3a" style="">
                        <div class="px-7 py-5">
                            <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                        </div>
                        <div class="separator border-gray-200"></div>
                        <div class="px-7 py-5">
                            <div class="mb-5">
                                <label class="form-label required">Bulan:</label>
                                <select id="selectMonth" name="month" class="form-select" data-control="select2" data-hide-search="true">
                                    <option value="1" @if($month == 1) {{"selected"}} @endif>Januari</option>
                                    <option value="2" @if($month == 2) {{"selected"}} @endif>Februari</option>
                                    <option value="3" @if($month == 3) {{"selected"}} @endif>Maret</option>
                                    <option value="4" @if($month == 4) {{"selected"}} @endif>April</option>
                                    <option value="5" @if($month == 5) {{"selected"}} @endif>Mei</option>
                                    <option value="6" @if($month == 6) {{"selected"}} @endif>Juni</option>
                                    <option value="7" @if($month == 7) {{"selected"}} @endif>Juli</option>
                                    <option value="8" @if($month == 8) {{"selected"}} @endif>Agustus</option>
                                    <option value="9" @if($month == 9) {{"selected"}} @endif>September</option>
                                    <option value="10" @if($month == 10) {{"selected"}} @endif>Oktober</option>
                                    <option value="11" @if($month == 11) {{"selected"}} @endif>November</option>
                                    <option value="12" @if($month == 12) {{"selected"}} @endif>Desember</option>
                                </select>
                            </div>
                            <div class="mb-5">
                                <label class="form-label required">Tahun:</label>
                                <input type="text" id="inputYear" name="year" class="form-control" placeholder="Tahun" autocomplete="off"
                                    @if(isset($year)) value="{{ $year }}" @else value="{{ old('year') }}"@endif>
                            </div>
                            <div class="mb-5">
                                <label class="form-label required">Role:</label>
                                <select id="selectRoleSalesman" name="role_salesman" class="form-select" data-control="select2" data-hide-search="true" onchange="getRoleSalesman(this);">
                                    <option value="" @if($role_salesman == 'SEMUA') {{"selected"}} @endif>SEMUA</option>
                                    <option value="SALESMAN" @if($role_salesman == 'SALESMAN') {{"selected"}} @endif>SALESMAN</option>
                                    <option value="SUPERVISOR" @if($role_salesman == 'SUPERVISOR') {{"selected"}} @endif>SUPERVISOR</option>
                                </select>
                            </div>
                            <div class="mb-5">
                                <label class="form-label" id="labelKodeSalesSpv">Salesman:</label>
                                <input type="text" id="inputKodeSalesSpv" name="kode_sales_spv" class="form-control" placeholder="Semua Salesman" autocomplete="off"
                                    @if(isset($kode_sales_spv)) value="{{ $kode_sales_spv }}" @else value="{{ old('kode_sales_spv') }}"@endif>
                            </div>
                            <div class="mb-5">
                                <div class="d-flex align-items-center">
                                    <button id="btnFilterProses" class="btn btn-sm btn-primary me-2" type="submit">Terapkan</button>
                                    <a id="btnFilterReset" href="{{ route('dashboard.dashboard-salesman') }}" class="btn btn-sm btn-danger me-2" role="button">Reset Filter</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row gy-5 g-xl-10 mt-2 mb-4">
            <div class="col-sm-6 col-xl-3 mb-xl-10">
                <div class="card h-lg-100">
                    <div class="card-body d-flex justify-content-between align-items-start flex-column">
                        <div class="mb-4">
                            <div class="d-flex flex-center w-60px h-60px rounded-3 bg-light-info bg-opacity-90">
                                <span class="svg-icon svg-icon-info svg-icon-3x">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M14 3V21H10V3C10 2.4 10.4 2 11 2H13C13.6 2 14 2.4 14 3ZM7 14H5C4.4 14 4 14.4 4 15V21H8V15C8 14.4 7.6 14 7 14Z" fill="currentColor"></path>
                                        <path d="M21 20H20V8C20 7.4 19.6 7 19 7H17C16.4 7 16 7.4 16 8V20H3C2.4 20 2 20.4 2 21C2 21.6 2.4 22 3 22H21C21.6 22 22 21.6 22 21C22 20.4 21.6 20 21 20Z" fill="currentColor"></path>
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="d-flex flex-column mb-6">
                            <div class="d-flex align-items-center">
                                <span class="fs-7 fw-bold text-gray-500 me-1 align-self-start">Rp.</span>
                                <span class="fs-1 fw-bolder text-gray-800 me-2 lh-1 ls-n2">{{ number_format($target_amount_total) }}</span>
                            </div>
                            <div class="mt-2">
                                <span class="text-gray-400 pt-1 fw-bold fs-6">Target</span>
                            </div>
                        </div>
                        @if (trim($target_amount_keterangan) == 'BERTAHAN')
                        <span class="badge badge-primary fs-base">
                            <span class="svg-icon svg-icon-2 svg-icon-white ms-n1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect x="6" y="11" width="12" height="2" rx="1" fill="currentColor"/>
                                </svg>
                            </span>{{ str_replace('-','',number_format($target_amount_prosentase, 2)) }}%
                        </span>
                        @elseif (trim($target_amount_keterangan) == 'NAIK')
                        <span class="badge badge-success fs-base">
                            <span class="svg-icon svg-icon-2 svg-icon-white ms-n1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor"></rect>
                                    <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor"></path>
                                </svg>
                            </span>{{ str_replace('-','',number_format($target_amount_prosentase, 2)) }}%
                        </span>
                        @else
                        <span class="badge badge-danger fs-base">
                            <span class="svg-icon svg-icon-2 svg-icon-white ms-n1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="11" y="18" width="13" height="2" rx="1" transform="rotate(-90 11 18)" fill="currentColor"></rect>
                                    <path d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z" fill="currentColor"></path>
                                </svg>
                            </span>{{ str_replace('-','',number_format($target_amount_prosentase, 2)) }}%
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-xl-10">
                <div class="card h-lg-100">
                    <div class="card-body d-flex justify-content-between align-items-start flex-column">
                        <div class="mb-4">
                            <div class="d-flex flex-center w-60px h-60px rounded-3 bg-light-success bg-opacity-90">
                                <span class="svg-icon svg-icon-success svg-icon-3x">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M18.041 22.041C18.5932 22.041 19.041 21.5932 19.041 21.041C19.041 20.4887 18.5932 20.041 18.041 20.041C17.4887 20.041 17.041 20.4887 17.041 21.041C17.041 21.5932 17.4887 22.041 18.041 22.041Z" fill="currentColor"/>
                                        <path opacity="0.3" d="M6.04095 22.041C6.59324 22.041 7.04095 21.5932 7.04095 21.041C7.04095 20.4887 6.59324 20.041 6.04095 20.041C5.48867 20.041 5.04095 20.4887 5.04095 21.041C5.04095 21.5932 5.48867 22.041 6.04095 22.041Z" fill="currentColor"/>
                                        <path opacity="0.3" d="M7.04095 16.041L19.1409 15.1409C19.7409 15.1409 20.141 14.7409 20.341 14.1409L21.7409 8.34094C21.9409 7.64094 21.4409 7.04095 20.7409 7.04095H5.44095L7.04095 16.041Z" fill="currentColor"/>
                                        <path d="M19.041 20.041H5.04096C4.74096 20.041 4.34095 19.841 4.14095 19.541C3.94095 19.241 3.94095 18.841 4.14095 18.541L6.04096 14.841L4.14095 4.64095L2.54096 3.84096C2.04096 3.64096 1.84095 3.04097 2.14095 2.54097C2.34095 2.04097 2.94096 1.84095 3.44096 2.14095L5.44096 3.14095C5.74096 3.24095 5.94096 3.54096 5.94096 3.84096L7.94096 14.841C7.94096 15.041 7.94095 15.241 7.84095 15.441L6.54096 18.041H19.041C19.641 18.041 20.041 18.441 20.041 19.041C20.041 19.641 19.641 20.041 19.041 20.041Z" fill="currentColor"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="d-flex flex-column mb-6">
                            <div class="d-flex align-items-center">
                                <span class="fs-7 fw-bold text-gray-500 me-1 align-self-start">Rp.</span>
                                <span class="fs-1 fw-bolder text-gray-800 me-2 lh-1 ls-n2">{{ number_format($penjualan_amount_total) }}</span>
                            </div>
                            <div class="mt-2">
                                <span class="text-gray-400 pt-1 fw-bold fs-6">Penjualan</span>
                            </div>
                        </div>
                        @if (trim($penjualan_amount_keterangan) == 'BERTAHAN')
                        <span class="badge badge-primary fs-base">
                            <span class="svg-icon svg-icon-2 svg-icon-white ms-n1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect x="6" y="11" width="12" height="2" rx="1" fill="currentColor"/>
                                </svg>
                            </span>{{ str_replace('-','',number_format($penjualan_amount_prosentase, 2)) }}%
                        </span>
                        @elseif (trim($penjualan_amount_keterangan) == 'NAIK')
                        <span class="badge badge-success fs-base">
                            <span class="svg-icon svg-icon-2 svg-icon-white ms-n1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor"></rect>
                                    <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor"></path>
                                </svg>
                            </span>{{ str_replace('-','',number_format($penjualan_amount_prosentase, 2)) }}%
                        </span>
                        @else
                        <span class="badge badge-danger fs-base">
                            <span class="svg-icon svg-icon-2 svg-icon-white ms-n1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="11" y="18" width="13" height="2" rx="1" transform="rotate(-90 11 18)" fill="currentColor"></rect>
                                    <path d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z" fill="currentColor"></path>
                                </svg>
                            </span>{{ str_replace('-','',number_format($penjualan_amount_prosentase, 2)) }}%
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-xl-10">
                <div class="card h-lg-100">
                    <div class="card-body d-flex justify-content-between align-items-start flex-column">
                        <div class="mb-4">
                            <div class="d-flex flex-center w-60px h-60px rounded-3 bg-light-danger bg-opacity-90">
                                <span class="svg-icon svg-icon-danger svg-icon-3x">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.5" d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z" fill="currentColor"/>
                                        <path d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z" fill="currentColor"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="d-flex flex-column mb-6">
                            <div class="d-flex align-items-center">
                                <span class="fs-7 fw-bold text-gray-500 me-1 align-self-start">Rp.</span>
                                <span class="fs-1 fw-bolder text-gray-800 me-2 lh-1 ls-n2">{{ number_format($retur_amount_total) }}</span>
                            </div>
                            <div class="mt-2">
                                <span class="text-gray-400 pt-1 fw-bold fs-6">Retur</span>
                            </div>
                        </div>
                        @if (trim($retur_amount_keterangan) == 'BERTAHAN')
                        <span class="badge badge-primary fs-base">
                            <span class="svg-icon svg-icon-2 svg-icon-white ms-n1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect x="6" y="11" width="12" height="2" rx="1" fill="currentColor"/>
                                </svg>
                            </span>{{ str_replace('-','',number_format($retur_amount_prosentase, 2)) }}%
                        </span>
                        @elseif (trim($retur_amount_keterangan) == 'NAIK')
                        <span class="badge badge-success fs-base">
                            <span class="svg-icon svg-icon-2 svg-icon-white ms-n1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor"></rect>
                                    <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor"></path>
                                </svg>
                            </span>{{ str_replace('-','',number_format($retur_amount_prosentase, 2)) }}%
                        </span>
                        @else
                        <span class="badge badge-danger fs-base">
                            <span class="svg-icon svg-icon-2 svg-icon-white ms-n1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="11" y="18" width="13" height="2" rx="1" transform="rotate(-90 11 18)" fill="currentColor"></rect>
                                    <path d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z" fill="currentColor"></path>
                                </svg>
                            </span>{{ str_replace('-','',number_format($retur_amount_prosentase, 2)) }}%
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-xl-10">
                <div class="card h-lg-100">
                    <div class="card-body d-flex justify-content-between align-items-start flex-column">
                        <div class="mb-4">
                            <div class="d-flex flex-center w-60px h-60px rounded-3 bg-light-primary bg-opacity-90">
                                <span class="svg-icon svg-icon-primary svg-icon-3x">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M3.20001 5.91897L16.9 3.01895C17.4 2.91895 18 3.219 18.1 3.819L19.2 9.01895L3.20001 5.91897Z" fill="currentColor"/>
                                        <path opacity="0.3" d="M13 13.9189C13 12.2189 14.3 10.9189 16 10.9189H21C21.6 10.9189 22 11.3189 22 11.9189V15.9189C22 16.5189 21.6 16.9189 21 16.9189H16C14.3 16.9189 13 15.6189 13 13.9189ZM16 12.4189C15.2 12.4189 14.5 13.1189 14.5 13.9189C14.5 14.7189 15.2 15.4189 16 15.4189C16.8 15.4189 17.5 14.7189 17.5 13.9189C17.5 13.1189 16.8 12.4189 16 12.4189Z" fill="currentColor"/>
                                        <path d="M13 13.9189C13 12.2189 14.3 10.9189 16 10.9189H21V7.91895C21 6.81895 20.1 5.91895 19 5.91895H3C2.4 5.91895 2 6.31895 2 6.91895V20.9189C2 21.5189 2.4 21.9189 3 21.9189H19C20.1 21.9189 21 21.0189 21 19.9189V16.9189H16C14.3 16.9189 13 15.6189 13 13.9189Z" fill="currentColor"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="d-flex flex-column mb-6">
                            <div class="d-flex align-items-center">
                                <span class="fs-7 fw-bold text-gray-500 me-1 align-self-start">Rp.</span>
                                <span class="fs-1 fw-bolder text-gray-800 me-2 lh-1 ls-n2">{{ number_format($omset_amount_total) }}</span>
                            </div>
                            <div class="mt-2">
                                <span class="text-gray-400 pt-1 fw-bold fs-6">Total Omzet</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center flex-column mt-3 w-100">
                            <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                @if ($prosentase_amount_total >= 120)
                                <span class="fw-boldest fs-6 text-info">
                                    @if($omset_amount_total > $target_amount_total)
                                    +{{ number_format($omset_amount_total - $target_amount_total) }}
                                    @else
                                    +{{ number_format($target_amount_total - $omset_amount_total) }}
                                    @endif
                                </span>
                                @elseif ($prosentase_amount_total < 120 && $prosentase_amount_total >= 110)
                                <span class="fw-boldest fs-6 text-primary">
                                    @if($omset_amount_total > $target_amount_total)
                                    +{{ number_format($omset_amount_total - $target_amount_total) }}
                                    @else
                                    +{{ number_format($target_amount_total - $omset_amount_total) }}
                                    @endif
                                </span>
                                @elseif ($prosentase_amount_total < 110 && $prosentase_amount_total >= 100)
                                <span class="fw-boldest fs-6 text-success">
                                    @if($omset_amount_total > $target_amount_total)
                                    +{{ number_format($omset_amount_total - $target_amount_total) }}
                                    @else
                                    +{{ number_format($target_amount_total - $omset_amount_total) }}
                                    @endif
                                </span>
                                @elseif ($prosentase_amount_total < 100 && $prosentase_amount_total >= 90)
                                <span class="fw-boldest fs-6 text-warning">-{{ number_format($target_amount_total - $omset_amount_total) }}</span>
                                @else
                                <span class="fw-boldest fs-6 text-danger">
                                    @if ($omset_amount_total > $target_amount_total)
                                    +{{ number_format($omset_amount_total - $target_amount_total) }}
                                    @else
                                    -{{ number_format($target_amount_total - $omset_amount_total) }}
                                    @endif
                                </span>
                                @endif
                                <span class="fw-bolder fs-6 text-dark-400">{{ number_format($prosentase_amount_total, 2) }}%</span>
                            </div>
                            @if ($prosentase_amount_total >= 120)
                            <div class="h-8px mx-3 w-100 bg-light-info rounded">
                                <div class="bg-info rounded h-8px" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            @elseif ($prosentase_amount_total < 120 && $prosentase_amount_total >= 110)
                            <div class="h-8px mx-3 w-100 bg-light-primary rounded">
                                <div class="bg-primary rounded h-8px" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            @elseif ($prosentase_amount_total < 110 && $prosentase_amount_total >= 100)
                            <div class="h-8px mx-3 w-100 bg-light-success rounded">
                                <div class="bg-success rounded h-8px" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            @elseif ($prosentase_amount_total < 100 && $prosentase_amount_total >= 90)
                            <div class="h-8px mx-3 w-100 bg-light-warning rounded">
                                <div class="bg-warning rounded h-8px" role="progressbar" style="width: {{ number_format($prosentase_amount_total) }}%;" aria-valuenow="{{ number_format($prosentase_amount_total) }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            @else
                            <div class="h-8px mx-3 w-100 bg-light-danger rounded">
                                <div class="bg-danger rounded h-8px" role="progressbar" style="width: {{ number_format($prosentase_amount_total) }}%;" aria-valuenow="{{ number_format($prosentase_amount_total) }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <div class="col-xl-4">
                <div class="card card-flush h-xl-200">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder text-gray-800">Pencapaian</span>
                            <span class="text-gray-400 mt-1 fw-bold fs-6">Group Per-Level</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartPenjualanPerLevel" style="height: 350px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <div class="card card-flush h-xl-200">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder text-gray-800">Performa Penjualan</span>
                            <span class="text-gray-400 mt-1 fw-bold fs-6">
                                @if ($month == 1) Januari
                                @elseif ($month == 2) Februari
                                @elseif ($month == 3) Maret
                                @elseif ($month == 4) April
                                @elseif ($month == 5) Mei
                                @elseif ($month == 6) Juni
                                @elseif ($month == 7) Juli
                                @elseif ($month == 8) Agustus
                                @elseif ($month == 9) September
                                @elseif ($month == 10) Oktober
                                @elseif ($month == 11) November
                                @elseif ($month == 12) Desember
                                @endif 2022</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="chartPerformaPenjualanHarian" style="height: 350px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-5">
            <div class="card card-flush h-xl-100">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder text-gray-800">Omzet & Target Sales</span>
                        <span class="text-gray-400 mt-1 fw-bold fs-6">Target dan penjualan per-produk</span>
                    </h3>
                </div>
                @if (strtoupper(trim($device)) == 'DESKTOP')
                <div class="card-body pt-3 pb-4">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                            <thead>
                                <tr class="fs-7 fw-bolder text-gray-400 border-bottom-0">
                                    <th class="p-0 pb-3 min-w-50px text-start">PRODUK</th>
                                    <th class="p-0 pb-3 min-w-50px text-end">OMSET (Rp.)</th>
                                    <th class="p-0 pb-3 min-w-50px text-end">TARGET (Rp.)</th>
                                    <th class="p-0 pb-3 w-150px text-end">PROSENTASE</th>
                                    <th class="p-0 pb-3 w-150px text-end">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detail_omset as $data)
                                <tr>
                                    <td>
                                        @if ($data->level == 'ZZZ')
                                        <div class="d-flex justify-content-start flex-column">
                                            <span class="text-danger fw-bolder fs-6">{{ trim($data->nama_produk) }}</span>
                                        </div>
                                        @else
                                        <div class="d-flex justify-content-start flex-column">
                                            <span class="text-gray-700 fw-bolder fs-6">{{ trim($data->nama_produk) }}</span>
                                            <span class="text-gray-400 fw-bold d-block fs-7">{{ trim($data->kode_produk) }}</span>
                                        </div>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($data->level == 'ZZZ')
                                        <span class="text-danger fw-bolder fs-6">{{ number_format($data->omset) }}</span>
                                        @else
                                        <span class="text-gray-600 fw-bolder fs-6">{{ number_format($data->omset) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($data->level == 'ZZZ')
                                        <span class="text-danger fw-bolder fs-6">{{ number_format($data->target) }}</span>
                                        @else
                                        <span class="text-gray-600 fw-bolder fs-6">{{ number_format($data->target) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($data->prosentase >= 120)
                                        <span class="badge badge-info fs-base">{{ number_format($data->prosentase, 2) }}%</span>
                                        @elseif ($data->prosentase < 120 && $data->prosentase >= 110)
                                        <span class="badge badge-primary fs-base">{{ number_format($data->prosentase, 2) }}%</span>
                                        @elseif ($data->prosentase < 110 && $data->prosentase >= 100)
                                        <span class="badge badge-success fs-base">{{ number_format($data->prosentase, 2) }}%</span>
                                        @elseif ($data->prosentase < 100 && $data->prosentase >= 90)
                                        <span class="badge badge-warning fs-base">{{ number_format($data->prosentase, 2) }}%</span>
                                        @else
                                        <span class="badge badge-danger fs-base">{{ number_format($data->prosentase, 2) }}%</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if (strtoupper(trim($data->keterangan_selisih)) == 'BERTAHAN')
                                        <span class="svg-icon svg-icon-2x svg-icon-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <rect x="6" y="11" width="12" height="2" rx="1" fill="currentColor"/>
                                            </svg>
                                        </span>
                                        @elseif (strtoupper(trim($data->keterangan_selisih)) == 'NAIK')
                                        <span class="svg-icon svg-icon-2x svg-icon-success">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor"></rect>
                                                <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor"></path>
                                            </svg>
                                        </span>
                                        @else
                                        <span class="svg-icon svg-icon-2x svg-icon-danger">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <rect opacity="0.5" x="11" y="18" width="13" height="2" rx="1" transform="rotate(-90 11 18)" fill="currentColor"></rect>
                                                <path d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z" fill="currentColor"></path>
                                            </svg>
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>

            @if (strtoupper(trim($device)) == 'MOBILE')
            @foreach ($detail_omset as $data)
            <div class="card card-flush h-xl-100">
                <div class="d-flex p-5">
                    <div class="symbol symbol-50px me-5">
                        @if (strtoupper(trim($data->keterangan_selisih)) == 'BERTAHAN')
                        <span class="symbol-label bg-light-primary">
                            <span class="svg-icon svg-icon-2x svg-icon-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect x="6" y="11" width="12" height="2" rx="1" fill="currentColor"/>
                                </svg>
                            </span>
                        </span>
                        @elseif (strtoupper(trim($data->keterangan_selisih)) == 'NAIK')
                        <span class="symbol-label bg-light-success">
                            <span class="svg-icon svg-icon-2x svg-icon-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor"></rect>
                                    <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor"></path>
                                </svg>
                            </span>
                        </span>
                        @else
                        <span class="symbol-label bg-light-danger">
                            <span class="svg-icon svg-icon-2x svg-icon-danger">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="11" y="18" width="13" height="2" rx="1" transform="rotate(-90 11 18)" fill="currentColor"></rect>
                                    <path d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z" fill="currentColor"></path>
                                </svg>
                            </span>
                        </span>
                        @endif
                    </div>
                    <div class="d-flex">
                        <div class="row">
                            <span class="text-dark text-hover-primary fs-6 fw-bolder">{{ trim($data->nama_produk) }}</span>
                            <span class="text-muted fw-bold">{{ trim($data->kode_produk) }}</span>
                            <div class="col-md-6">
                                <div class="row">
                                    <span class="text-muted fw-bold mt-4 fs-7">Target:</span>
                                    <span class="text-dark text-hover-primary fs-6 fw-bolder">Rp. {{ number_format($data->target) }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <span class="text-muted fw-bold mt-4 fs-7">Omset:</span>
                                    <span class="text-dark text-hover-primary fs-6 fw-bolder">Rp. {{ number_format($data->omset) }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <span class="text-muted fw-bold fs-7 mt-4">Prosentase:</span>
                                    <div class="d-flex">
                                        @if ($data->prosentase >= 120)
                                        <span class="badge badge-info fs-base">{{ number_format($data->prosentase, 2) }}%</span>
                                        @elseif ($data->prosentase < 120 && $data->prosentase >= 110)
                                        <span class="badge badge-primary fs-base">{{ number_format($data->prosentase, 2) }}%</span>
                                        @elseif ($data->prosentase < 110 && $data->prosentase >= 100)
                                        <span class="badge badge-success fs-base">{{ number_format($data->prosentase, 2) }}%</span>
                                        @elseif ($data->prosentase < 100 && $data->prosentase >= 90)
                                        <span class="badge badge-warning fs-base">{{ number_format($data->prosentase, 2) }}%</span>
                                        @else
                                        <span class="badge badge-danger fs-base">{{ number_format($data->prosentase, 2) }}%</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </form>


    @push('scripts')
        <script src="{{ asset('assets/media/charts/amcharts/index.js') }}"></script>
        <script src="{{ asset('assets/media/charts/amcharts/xy.js') }}"></script>
        <script src="{{ asset('assets/media/charts/amcharts/radar.js') }}"></script>
        <script src="{{ asset('assets/media/charts/amcharts/Animated.js') }}"></script>

        <script type="text/javascript">
            var btnFilterProses = document.querySelector("#btnFilterProses");
            var btnFilterReset = document.querySelector("#btnFilterReset");
            var targetBlockDashboardSalesman = document.querySelector("#formDashboardSalesman");

            var blockDashboardSalesman = new KTBlockUI(targetBlockDashboardSalesman, {
                message: '<div class="blockui-message" style="position: fixed;top: 50%;left: 50%;transform: translate(-50%, -50%);">'+
                            '<span class="spinner-border text-primary"></span> Loading...'+
                        '</div>'
            });
            btnFilterProses.addEventListener("click", function(e) {
                e.preventDefault();
                blockDashboardSalesman.block();
                document.getElementById("formDashboardSalesman").submit();
            });
            btnFilterReset.addEventListener("click", function(e) {
                e.preventDefault();
                blockDashboardSalesman.block();
                document.getElementById("formDashboardSalesman").submit();
            });

            $(function chartPerformaPenjualanGroupLevel() {
                am5.ready(function() {
                    var root = am5.Root.new("chartPenjualanPerLevel");

                    root.setThemes([
                        am5themes_Animated.new(root)
                    ]);

                    var chart = root.container.children.push(am5radar.RadarChart.new(root, {
                        innerRadius: am5.percent(20),
                        startAngle: -90,
                        endAngle: 180
                    }));


                    var data = [{
                        category: "OLI",
                        value: {!!json_encode(number_format($detail_group_per_level->oli, 2))!!},
                        full: 100,
                        columnSettings: {
                            fill: am5.color("#7239EA")
                        }
                        }, {
                        category: "TUBE",
                        value: {!!json_encode(number_format($detail_group_per_level->tube, 2))!!},
                        full: 100,
                        columnSettings: {
                            fill: am5.color("#F1416C")
                        }
                        }, {
                        category: "NON-HANDLE",
                        value: {!!json_encode(number_format($detail_group_per_level->non_handle, 2))!!},
                        full: 100,
                        columnSettings: {
                            fill: am5.color("#009EF7")
                        }
                        }, {
                        category: "HANDLE",
                        value: {!!json_encode(number_format($detail_group_per_level->handle, 2))!!},
                        full: 100,
                        columnSettings: {
                            fill: am5.color("#50CD89")
                        }
                    }];

                    var cursor = chart.set("cursor", am5radar.RadarCursor.new(root, {
                    }));

                    cursor.lineY.set("visible", false);

                    var xRenderer = am5radar.AxisRendererCircular.new(root, {
                    });

                    xRenderer.labels.template.setAll({
                        radius: 10
                    });

                    xRenderer.grid.template.setAll({
                        forceHidden: true
                    });

                    var xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {
                        renderer: xRenderer,
                        min: 0,
                        max: 100,
                        strictMinMax: true,
                        numberFormat: "#'%'",
                        tooltip: am5.Tooltip.new(root, {})
                    }));


                    var yRenderer = am5radar.AxisRendererRadial.new(root, {
                        minGridDistance: 20
                    });

                    yRenderer.labels.template.setAll({
                        centerX: am5.p100,
                        fontWeight: "500",
                        fontSize: 18,
                        templateField: "columnSettings"
                    });

                    yRenderer.grid.template.setAll({
                        forceHidden: true
                    });

                    var yAxis = chart.yAxes.push(am5xy.CategoryAxis.new(root, {
                        categoryField: "category",
                        renderer: yRenderer
                    }));

                    yAxis.data.setAll(data);

                    var series1 = chart.series.push(am5radar.RadarColumnSeries.new(root, {
                        xAxis: xAxis,
                        yAxis: yAxis,
                        clustered: false,
                        valueXField: "full",
                        categoryYField: "category",
                        fill: root.interfaceColors.get("alternativeBackground")
                    }));

                    series1.columns.template.setAll({
                        width: am5.p100,
                        fillOpacity: 0.08,
                        strokeOpacity: 0,
                        cornerRadius: 20
                    });

                    series1.data.setAll(data);

                    var series2 = chart.series.push(am5radar.RadarColumnSeries.new(root, {
                        xAxis: xAxis,
                        yAxis: yAxis,
                        clustered: false,
                        valueXField: "value",
                        categoryYField: "category"
                    }));

                    series2.columns.template.setAll({
                        width: am5.p100,
                        strokeOpacity: 0,
                        tooltipText: "[bold]{category} : {valueX}%",
                        cornerRadius: 20,
                        templateField: "columnSettings"
                    });

                    series2.data.setAll(data);

                    series1.appear(1000);
                    series2.appear(1000);
                    chart.appear(1000, 100);

                });
            });

            $(function chartPerformaPenjualanHarian() {
                am5.ready(function() {
                    var root = am5.Root.new("chartPerformaPenjualanHarian");

                    root.setThemes([
                        am5themes_Animated.new(root)
                    ]);

                    var chart = root.container.children.push(am5xy.XYChart.new(root, {
                        panX: true,
                        panY: true,
                        wheelX: "panX",
                        wheelY: "zoomX",
                        layout: root.verticalLayout,
                        pinchZoomX:true
                    }));

                    var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                        behavior: "none"
                    }));
                    cursor.lineY.set("visible", false);

                    var data = [
                        @foreach($detail_daily as $data)
                        {
                            day: {{ $data->day }},
                            value: {{ $data->total }},
                            strokeSettings: {
                                stroke: am5.color("#3DC77D")
                            },
                            fillSettings: {
                                fill: am5.color("#3DC77D")
                            },
                            bulletSettings: {
                                fill: am5.color("#3DC77D")
                            }
                        },
                        @endforeach
                    ];

                    var xRenderer = am5xy.AxisRendererX.new(root, {});
                        xRenderer.grid.template.set("location", 0.5);
                        xRenderer.labels.template.setAll({
                        location: 0.5,
                        multiLocation: 0.5
                    });

                    var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
                        categoryField: "day",
                        renderer: xRenderer,
                        tooltip: am5.Tooltip.new(root, {})
                    }));

                    xAxis.data.setAll(data);

                    var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                        maxPrecision: 0,
                        renderer: am5xy.AxisRendererY.new(root, {})
                    }));

                    var series = chart.series.push(am5xy.LineSeries.new(root, {
                        xAxis: xAxis,
                        yAxis: yAxis,
                        valueYField: "value",
                        categoryXField: "day",
                        fill: am5.color("#3DC77D"),
                        tooltip: am5.Tooltip.new(root, {
                            labelText: "[bold]Tanggal {categoryX} : Rp. {valueY}",
                            dy:-5
                        })
                    }));

                    series.strokes.template.setAll({
                        templateField: "strokeSettings",
                        strokeWidth: 2
                    });

                    series.fills.template.setAll({
                        visible: true,
                        fillOpacity: 0.5,
                        templateField: "fillSettings"
                    });


                    series.bullets.push(function() {
                        return am5.Bullet.new(root, {
                            sprite: am5.Circle.new(root, {
                            templateField: "bulletSettings",
                            radius: 5
                            })
                        });
                    });

                    series.data.setAll(data);
                    series.appear(1000);

                    chart.appear(1000, 100);

                });
            });

            window.onload = function() {
                getRoleSalesman();
            }

            function getRoleSalesman() {
                var roleSalesman = document.getElementById("selectRoleSalesman").value;

                if(roleSalesman == "SALESMAN") {
                    $('#labelKodeSalesSpv').html("Salesman");
                    document.getElementById('inputKodeSalesSpv').placeholder='Semua Salesman';
                    document.getElementById("inputKodeSalesSpv").readOnly = false;
                } else if(roleSalesman == "SUPERVISOR") {
                    $('#labelKodeSalesSpv').html("Supervisor");
                    document.getElementById('inputKodeSalesSpv').placeholder='Semua Supervisor';
                    document.getElementById("inputKodeSalesSpv").readOnly = false;
                } else {
                    $('#labelKodeSalesSpv').html("Salesman");
                    document.getElementById('inputKodeSalesSpv').placeholder='Semua Sales & SPV';
                    document.getElementById("inputKodeSalesSpv").readOnly = true;
                }
            }

            $('#selectRoleSalesman').change(function(){
                var selectRole = $('#selectRoleSalesman').val();

                if(selectRole == '') {
                    $('#inputKodeSalesSpv').val('');
                }
            });
        </script>
    @endpush
@endsection
