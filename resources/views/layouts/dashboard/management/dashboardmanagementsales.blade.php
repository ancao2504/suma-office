@extends('layouts.main.index')
@section('title','Home')
@section('subtitle','Dashboard')
@section('container')
    <form id="formDashboardManagementSales" action="{{ route('dashboard.dashboard-management-sales') }}" method="get">
        <div class="card card-flush">
            <div class="card-header align-items-center border-0 mt-4 mb-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Management Sales</span>
                    <span class="text-muted fw-bold fs-7">Dashboard Management Sales
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
                                <label class="form-label required">Data yang ditampilkan:</label>
                                <select id="selectFields" name="fields" class="form-select" data-control="select2" data-hide-search="true">
                                    <option value="QUANTITY" @if($fields == 'QUANTITY') {{"selected"}} @endif>Quantity</option>
                                    <option value="SELLING_PRICE_EX_PPN" @if($fields == 'SELLING_PRICE_EX_PPN') {{"selected"}} @endif>Selling Price (Exclude PPN)</option>
                                    <option value="SELLING_PRICE_IN_PPN" @if($fields == 'SELLING_PRICE_IN_PPN') {{"selected"}} @endif>Selling Price (Include PPN)</option>
                                </select>
                            </div>
                            <div class="mb-5">
                                <label class="form-label">Produk:</label>
                                <input type="text" id="inputProduk" name="produk" class="form-control" placeholder="Kode Produk" autocomplete="off"
                                    @if(isset($produk)) value="{{ $produk }}" @else value="{{ old('produk') }}"@endif>
                            </div>
                            <div class="mb-5">
                                <div class="d-flex align-items-center">
                                    <button id="btnFilterProses" class="btn btn-sm btn-primary me-2" type="submit">Terapkan</button>
                                    <a id="btnFilterReset" href="{{ route('dashboard.dashboard-management-sales') }}" class="btn btn-sm btn-danger me-2" role="button">Reset Filter</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-6 mt-4">
                <div class="card card-flush"
                    style="background-color: #ffff;background-image:url('{{ asset('assets/media/svg/shapes/wave-bg-dark.svg') }}');background-size: 1200px;">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <div class="d-flex align-items-center">
                                @if($fields != 'QUANTITY')
                                <span class="fs-4 fw-bolder text-gray-800 me-1 align-self-start">Rp.</span>
                                @endif
                                <span class="fs-2hx fw-bolder text-dark me-2 lh-1 ls-n2">{{ number_format($selling['selling_total']) }}</span>
                                @if($fields == 'QUANTITY')
                                <span class="fs-4 fw-bolder text-gray-800 me-1 align-self-start me-4">PCS</span>
                                @endif
                            </div>
                            <span class="text-gray-800 pt-1 fw-bolder fs-6">Grand Total</span>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-end pt-0 mt-4">
                        <div class="d-flex align-items-center fw-bolder fs-6 text-dark w-100 mt-auto mb-2">
                            <span class="me-4">Sales All</span>
                            @if(trim($selling['selling_total_status']) == 'NAIK')
                            <span class="badge badge-success fs-base">
                                <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor"></rect>
                                        <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor"></path>
                                    </svg>
                                </span>{{ str_replace('-','',number_format($selling['selling_total_prosentase'], 2)) }}%
                            </span>
                            @elseif(trim($selling['selling_total_status']) == 'TURUN')
                            <span class="badge badge-danger fs-base">
                                <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="11" y="18" width="13" height="2" rx="1" transform="rotate(-90 11 18)" fill="currentColor"></rect>
                                        <path d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z" fill="currentColor"></path>
                                    </svg>
                                </span>{{ str_replace('-','',number_format($selling['selling_total_prosentase'], 2)) }}%
                            </span>
                            @else
                            <span class="badge badge-primary fs-base">
                                <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect x="6" y="11" width="12" height="2" rx="1" fill="currentColor"/>
                                    </svg>
                                </span>{{ str_replace('-','',number_format($selling['selling_total_prosentase'], 2)) }}%
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if ($fields == 'QUANTITY')
            <div class="col-md-6 mt-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card card-flush p-4"
                            style="background-color: #009EF7;background-image:url('{{ asset('assets/media/svg/shapes/wave-bg-dark.svg') }}');background-size: 200px;height: 153px;">
                            <span class="text-white fw-bolder fs-6 me-4 mb-2">Pusat</span>
                            <table>
                                <thead>
                                    <tr>
                                        <th class="w-50px"></th>
                                        <th class="w-20px"></th>
                                        <th class="min-w-100px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($best_sales->pusat as $item)
                                    <tr>
                                        <td class="text-white fw-bolder fs-6">{{ $item->produk }}</td>
                                        <td class="text-white fw-bolder fs-6">:</td>
                                        <td class="text-white fw-bold">{{ number_format($item->total) }} PCS</td>
                                    </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-flush p-4"
                            style="background-color: #F1416C;background-image:url('{{ asset('assets/media/svg/shapes/wave-bg-dark.svg') }}');background-size: 200px;height: 153px;">
                            <span class="text-white fw-bolder fs-6 me-4 mb-2">PC</span>
                            <table>
                                <thead>
                                    <tr>
                                        <th class="w-50px"></th>
                                        <th class="w-20px"></th>
                                        <th class="min-w-100px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($best_sales->pc as $item)
                                    <tr>
                                        <td class="text-white fw-bolder fs-6">{{ $item->produk }}</td>
                                        <td class="text-white fw-bolder fs-6">:</td>
                                        <td class="text-white fw-bold">{{ number_format($item->total) }} PCS</td>
                                    </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-flush p-4"
                            style="background-color: #7239EA;background-image:url('{{ asset('assets/media/svg/shapes/wave-bg-dark.svg') }}');background-size: 200px;height: 153px;">
                            <span class="text-white fw-bolder fs-6 me-4 mb-2">Online</span>
                            <table>
                                <thead>
                                    <tr>
                                        <th class="w-50px"></th>
                                        <th class="w-20px"></th>
                                        <th class="min-w-100px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($best_sales->online as $item)
                                    <tr>
                                        <td class="text-white fw-bolder fs-6">{{ $item->produk }}</td>
                                        <td class="text-white fw-bolder fs-6">:</td>
                                        <td class="text-white fw-bold">{{ number_format($item->total) }} PCS</td>
                                    </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="col-md-6 mt-4">
                <div class="card card-flush"
                    style="background-color: #ffff;background-image:url('{{ asset('assets/media/svg/shapes/wave-bg-dark.svg') }}');background-size: 1200px;">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <div class="d-flex align-items-center">
                                <span class="fs-4 fw-bolder text-gray-800 me-1 align-self-start">Rp.</span>
                                <span class="fs-2hx fw-bolder text-dark me-2 lh-1 ls-n2">{{ number_format($margin['margin_total']) }}</span>
                                @if ($margin['margin_total_prosentase'] <= 0)
                                <span class="badge badge-danger fs-base ms-4">
                                    <span class="fs-2 fw-bolder text-white p-1">{{ number_format($margin['margin_total_prosentase'], 2) }}%</span>
                                </span>
                                @else
                                <span class="badge badge-success fs-base ms-4">
                                    <span class="fs-2 fw-bolder text-white p-1">{{ number_format($margin['margin_total_prosentase'], 2) }}%</span>
                                </span>
                                @endif
                            </div>
                            <span class="text-gray-800 pt-1 fw-bolder fs-6">Grand Total</span>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-end pt-0 mt-4">
                        <div class="d-flex align-items-center fw-bolder fs-6 text-dark w-100 mt-auto mb-2">
                            <span class="me-4">Margin</span>
                            @if(trim($margin['margin_total_status']) == 'NAIK')
                            <span class="badge badge-success fs-base">
                                <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor"></rect>
                                        <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor"></path>
                                    </svg>
                                </span>{{ str_replace('-','',number_format($margin['margin_total_status_prosentase'], 2)) }}%
                            </span>
                            @elseif(trim($margin['margin_total_status']) == 'TURUN')
                            <span class="badge badge-danger fs-base">
                                <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="11" y="18" width="13" height="2" rx="1" transform="rotate(-90 11 18)" fill="currentColor"></rect>
                                        <path d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z" fill="currentColor"></path>
                                    </svg>
                                </span>{{ str_replace('-','',number_format($margin['margin_total_status_prosentase'], 2)) }}%
                            </span>
                            @else
                            <span class="badge badge-primary fs-base">
                                <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect x="6" y="11" width="12" height="2" rx="1" fill="currentColor"/>
                                    </svg>
                                </span>{{ str_replace('-','',number_format($margin['margin_total_status_prosentase'], 2)) }}%
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="row">
            <div class="col-lg-6 mt-6">
                <div class="card card-flush h-xl-100">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder text-gray-800">Sales All</span>
                        </h3>
                    </div>
                    <div class="card-body pt-8">
                        <div class="mb-4" id="chartSalesAll" style="height: 400px;"></div>
                        <div class="row">
                            <div class="d-flex align-items-center mt-1">
                                <i class="fa fa-genderless text-primary fs-1 me-2"></i>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center">
                                        <span class="text-primary fw-bolder fs-6 me-4">
                                            Pusat : @if($fields != 'QUANTITY') Rp. @endif {{ number_format($selling['selling_detail']['selling_pusat']->total) }} @if($fields == 'QUANTITY') PCS @endif
                                        </span>
                                        @if(trim($selling['selling_detail']['selling_pusat']->status) == 'NAIK')
                                        <span class="badge badge-success fs-base">
                                            <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor"></rect>
                                                    <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor"></path>
                                                </svg>
                                            </span>{{ str_replace('-','',number_format($selling['selling_detail']['selling_pusat']->status_prosentase, 2)) }}%
                                        </span>
                                        @elseif(trim($selling['selling_detail']['selling_pusat']->status) == 'TURUN')
                                        <span class="badge badge-danger fs-base">
                                            <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="11" y="18" width="13" height="2" rx="1" transform="rotate(-90 11 18)" fill="currentColor"></rect>
                                                    <path d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z" fill="currentColor"></path>
                                                </svg>
                                            </span>{{ str_replace('-','',number_format($selling['selling_detail']['selling_pusat']->status_prosentase, 2)) }}%
                                        </span>
                                        @else
                                        <span class="badge badge-primary fs-base">
                                            <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect x="6" y="11" width="12" height="2" rx="1" fill="currentColor"/>
                                                </svg>
                                            </span>{{ str_replace('-','',number_format($selling['selling_detail']['selling_pusat']->status_prosentase, 2)) }}%
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-1">
                                <i class="fa fa-genderless text-danger fs-1 me-2"></i>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center">
                                        <span class="text-danger fw-bolder fs-6 me-4">
                                            Part Center : @if($fields != 'QUANTITY') Rp. @endif {{ number_format($selling['selling_detail']['selling_pc']->total) }} @if($fields == 'QUANTITY') PCS @endif
                                        </span>
                                        @if(trim($selling['selling_detail']['selling_pc']->status) == 'NAIK')
                                        <span class="badge badge-success fs-base">
                                            <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor"></rect>
                                                    <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor"></path>
                                                </svg>
                                            </span>{{ str_replace('-','',number_format($selling['selling_detail']['selling_pc']->status_prosentase, 2)) }}%
                                        </span>
                                        @elseif(trim($selling['selling_detail']['selling_pc']->status) == 'TURUN')
                                        <span class="badge badge-danger fs-base">
                                            <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="11" y="18" width="13" height="2" rx="1" transform="rotate(-90 11 18)" fill="currentColor"></rect>
                                                    <path d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z" fill="currentColor"></path>
                                                </svg>
                                            </span>{{ str_replace('-','',number_format($selling['selling_detail']['selling_pc']->status_prosentase, 2)) }}%
                                        </span>
                                        @else
                                        <span class="badge badge-primary fs-base">
                                            <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect x="6" y="11" width="12" height="2" rx="1" fill="currentColor"/>
                                                </svg>
                                            </span>{{ str_replace('-','',number_format($selling['selling_detail']['selling_pc']->status_prosentase, 2)) }}%
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-1">
                                <i class="fa fa-genderless text-info fs-1 me-2"></i>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center">
                                        <span class="text-info fw-bolder fs-6 me-4">
                                            Online : @if($fields != 'QUANTITY') Rp. @endif {{ number_format($selling['selling_detail']['selling_online']->total) }} @if($fields == 'QUANTITY') PCS @endif
                                        </span>
                                        @if(trim($selling['selling_detail']['selling_online']->status) == 'NAIK')
                                        <span class="badge badge-success fs-base">
                                            <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor"></rect>
                                                    <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor"></path>
                                                </svg>
                                            </span>{{ str_replace('-','',number_format($selling['selling_detail']['selling_online']->status_prosentase, 2)) }}%
                                        </span>
                                        @elseif(trim($selling['selling_detail']['selling_online']->status) == 'TURUN')
                                        <span class="badge badge-danger fs-base">
                                            <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="11" y="18" width="13" height="2" rx="1" transform="rotate(-90 11 18)" fill="currentColor"></rect>
                                                    <path d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z" fill="currentColor"></path>
                                                </svg>
                                            </span>{{ str_replace('-','',number_format($selling['selling_detail']['selling_online']->status_prosentase, 2)) }}%
                                        </span>
                                        @else
                                        <span class="badge badge-primary fs-base">
                                            <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect x="6" y="11" width="12" height="2" rx="1" fill="currentColor"/>
                                                </svg>
                                            </span>{{ str_replace('-','',number_format($selling['selling_detail']['selling_online']->status_prosentase, 2)) }}%
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if($fields == 'QUANTITY')
            <div class="col-lg-6 mt-6">
                <div class="card card-flush h-xl-100">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder text-gray-800">Comparison</span>
                        </h3>
                    </div>
                    <div class="card-body pt-8">
                        <div class="mb-4" id="chartComparison" style="height: 450px;"></div>
                    </div>
                </div>
            </div>
            @else
            <div class="col-lg-6 mt-6">
                <div class="card card-flush h-xl-100">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder text-gray-800">Gross Profit</span>
                        </h3>
                    </div>
                    <div class="card-body pt-8">
                        <div class="mb-4" id="chartGrossProfit" style="height: 400px;"></div>
                        <div class="row">
                            <div class="d-flex align-items-center mt-1">
                                <i class="fa fa-genderless text-primary fs-1 me-2"></i>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center">
                                        <span class="text-primary fw-bolder fs-6 me-4">Pusat : Rp. {{ number_format($margin['margin_detail']['margin_pusat']->margin) }} ({{ number_format($margin['margin_detail']['margin_pusat']->margin_prosentase, 2) }}%)</span>
                                        @if(trim($margin['margin_detail']['margin_pusat']->status) == 'NAIK')
                                        <span class="badge badge-success fs-base">
                                            <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor"></rect>
                                                    <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor"></path>
                                                </svg>
                                            </span>{{ str_replace('-','',number_format($margin['margin_detail']['margin_pusat']->status_prosentase, 2)) }}%
                                        </span>
                                        @elseif(trim($margin['margin_detail']['margin_pusat']->status) == 'TURUN')
                                        <span class="badge badge-danger fs-base">
                                            <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="11" y="18" width="13" height="2" rx="1" transform="rotate(-90 11 18)" fill="currentColor"></rect>
                                                    <path d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z" fill="currentColor"></path>
                                                </svg>
                                            </span>{{ str_replace('-','',number_format($margin['margin_detail']['margin_pusat']->status_prosentase, 2)) }}%
                                        </span>
                                        @else
                                        <span class="badge badge-primary fs-base">
                                            <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect x="6" y="11" width="12" height="2" rx="1" fill="currentColor"/>
                                                </svg>
                                            </span>{{ str_replace('-','',number_format($margin['margin_detail']['margin_pusat']->status_prosentase, 2)) }}%
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-1">
                                <i class="fa fa-genderless text-danger fs-1 me-2"></i>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center">
                                        <span class="text-danger fw-bolder fs-6 me-4">Part Center : Rp. {{ number_format($margin['margin_detail']['margin_pc']->margin) }} ({{ number_format($margin['margin_detail']['margin_pc']->margin_prosentase, 2) }}%)</span>
                                        @if(trim($margin['margin_detail']['margin_pc']->status) == 'NAIK')
                                        <span class="badge badge-success fs-base">
                                            <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor"></rect>
                                                    <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor"></path>
                                                </svg>
                                            </span>{{ str_replace('-','',number_format($margin['margin_detail']['margin_pc']->status_prosentase, 2)) }}%
                                        </span>
                                        @elseif(trim($margin['margin_detail']['margin_pc']->status) == 'TURUN')
                                        <span class="badge badge-danger fs-base">
                                            <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="11" y="18" width="13" height="2" rx="1" transform="rotate(-90 11 18)" fill="currentColor"></rect>
                                                    <path d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z" fill="currentColor"></path>
                                                </svg>
                                            </span>{{ str_replace('-','',number_format($margin['margin_detail']['margin_pc']->status_prosentase, 2)) }}%
                                        </span>
                                        @else
                                        <span class="badge badge-primary fs-base">
                                            <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect x="6" y="11" width="12" height="2" rx="1" fill="currentColor"/>
                                                </svg>
                                            </span>{{ str_replace('-','',number_format($margin['margin_detail']['margin_pc']->status_prosentase, 2)) }}%
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-1">
                                <i class="fa fa-genderless text-info fs-1 me-2"></i>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center">
                                        <span class="text-info fw-bolder fs-6 me-4">Online : Rp. {{ number_format($margin['margin_detail']['margin_online']->margin) }} ({{ number_format($margin['margin_detail']['margin_online']->margin_prosentase, 2) }}%)</span>
                                        @if(trim($margin['margin_detail']['margin_online']->status) == 'NAIK')
                                        <span class="badge badge-success fs-base">
                                            <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor"></rect>
                                                    <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor"></path>
                                                </svg>
                                            </span>{{ str_replace('-','',number_format($margin['margin_detail']['margin_online']->status_prosentase, 2)) }}%
                                        </span>
                                        @elseif(trim($margin['margin_detail']['margin_online']->status) == 'TURUN')
                                        <span class="badge badge-danger fs-base">
                                            <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="11" y="18" width="13" height="2" rx="1" transform="rotate(-90 11 18)" fill="currentColor"></rect>
                                                    <path d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z" fill="currentColor"></path>
                                                </svg>
                                            </span>{{ str_replace('-','',number_format($margin['margin_detail']['margin_online']->status_prosentase, 2)) }}%
                                        </span>
                                        @else
                                        <span class="badge badge-primary fs-base">
                                            <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect x="6" y="11" width="12" height="2" rx="1" fill="currentColor"/>
                                                </svg>
                                            </span>{{ str_replace('-','',number_format($margin['margin_detail']['margin_online']->status_prosentase, 2)) }}%
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <div class="row mt-6">
            <div class="col-lg-12">
                <div class="card card-flush h-xl-100">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder text-gray-800">Sales By Date</span>
                        </h3>
                    </div>

                    <div class="card-body pt-8">
                        <div id="chartSalesByDate" style="height: 500px; width: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-6">
            <div class="col-12">
                <div class="card card-flush">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder text-gray-800">Sales By Product</span>
                        </h3>
                    </div>

                    <div class="card-body pt-8">
                        @if(isset($produk))
                        <div id="chartSalesByProduct" style="height: 300px; width: 100%;"></div>
                        @else
                        <div id="chartSalesByProduct" style="height: 5500px; width: 100%;"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
    @push('scripts')
        <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
        <script src="{{ asset('assets/media/charts/amcharts/index.js') }}"></script>
        <script src="{{ asset('assets/media/charts/amcharts/percent.js') }}"></script>
        <script src="{{ asset('assets/media/charts/amcharts/xy.js') }}"></script>
        <script src="{{ asset('assets/media/charts/amcharts/Animated.js') }}"></script>
        <script src="{{ asset('assets/media/charts/amcharts/Micro.js') }}"></script>

        <script type="text/javascript">
            var btnFilterProses = document.querySelector("#btnFilterProses");
            var btnFilterReset = document.querySelector("#btnFilterReset");
            var targetBlockDashboardManagementSales = document.querySelector("#formDashboardManagementSales");

            var blockDashboardManagementSales = new KTBlockUI(targetBlockDashboardManagementSales, {
                message: '<div class="blockui-message" style="position: fixed;top: 50%;left: 50%;transform: translate(-50%, -50%);">'+
                            '<span class="spinner-border text-primary"></span> Loading...'+
                        '</div>'
            });
            btnFilterProses.addEventListener("click", function(e) {
                e.preventDefault();
                blockDashboardManagementSales.block();
                document.getElementById("formDashboardManagementSales").submit();
            });
            btnFilterReset.addEventListener("click", function(e) {
                e.preventDefault();
                blockDashboardManagementSales.block();
                document.getElementById("formDashboardManagementSales").submit();
            });

            @if($fields == 'QUANTITY')
                $(function chartComparison() {
                    am5.ready(function() {
                        var root = am5.Root.new("chartComparison");

                        root.setThemes([
                            am5themes_Animated.new(root)
                        ]);

                        var chart = root.container.children.push(am5xy.XYChart.new(root, {
                            panX: false,
                            panY: false,
                            layout: root.verticalLayout
                        }));

                        var legend = chart.children.push(
                            am5.Legend.new(root, {
                                centerX: am5.p50,
                                x: am5.p50
                            })
                        );

                        var data = {!!json_encode($comparison)!!};

                        var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
                            categoryField: "company",
                            renderer: am5xy.AxisRendererX.new(root, {
                                cellStartLocation: 0.1,
                                cellEndLocation: 0.9
                            }),
                            tooltip: am5.Tooltip.new(root, {})
                        }));

                        xAxis.data.setAll(data);

                        var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                            renderer: am5xy.AxisRendererY.new(root, {})
                        }));

                        function createSeries(name, fieldName, color) {
                            var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                                name: name,
                                xAxis: xAxis,
                                yAxis: yAxis,
                                valueYField: fieldName,
                                fill: color,
                                categoryXField: "company",
                                width: am5.percent(90),
                                tooltip: am5.Tooltip.new(root, {
                                    pointerOrientation: "horizontal",
                                    labelText: "[bold]{name} : [bold]Rp. {valueY}"
                                })
                            }));

                            series.data.setAll(data);

                            series.appear();

                            series.bullets.push(function () {
                                return am5.Bullet.new(root, {
                                locationY: 0,
                                sprite: am5.Label.new(root, {
                                    text: "{valueY}",
                                    fill: root.interfaceColors.get("alternativeText"),
                                    centerY: 0,
                                    centerX: am5.p50,
                                    populateText: true
                                })
                                });
                            });

                            legend.data.push(series);
                        }

                        createSeries("Bulan Yang Dipilih", "total_sekarang", am5.color("#F1416C"));
                        createSeries("Bulan Sebelumnya", "total_lalu", am5.color("#009EF7"));

                        var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                        }));
                        cursor.lineY.set("forceHidden", true);
                        cursor.lineX.set("forceHidden", true);

                        chart.appear(1000, 100);
                    });
                });
            @else
                $(function chartGrossProfit() {
                    am5.ready(function() {
                        var root = am5.Root.new("chartGrossProfit");

                        root.setThemes([
                            am5themes_Animated.new(root)
                        ]);

                        var chart = root.container.children.push(am5percent.PieChart.new(root, {
                            layout: root.verticalLayout,
                            innerRadius: am5.percent(40)
                        }));

                        var series0 = chart.series.push(am5percent.PieSeries.new(root, {
                            valueField: "margin_prosentase",
                            categoryField: "company",
                            alignLabels: true,
                        }));

                        var bgColor = root.interfaceColors.get("background");

                        series0.ticks.template.setAll({ forceHidden: true });
                        series0.labels.template.setAll({ forceHidden: true });
                        series0.slices.template.setAll({
                            stroke: bgColor,
                            strokeWidth: 2,
                            tooltipText:
                                "{category}: [bold]{value.formatNumber('0.00')}% (Prosentase Margin)"
                        });
                        series0.slices.template.states.create("hover", { scale: 0.95 });

                        var series1 = chart.series.push(am5percent.PieSeries.new(root, {
                            valueField: "margin",
                            categoryField: "company",
                            alignLabels: true
                        }));

                        series1.get("colors").set("colors", [
                            am5.color("#009EF7"),
                            am5.color("#F1416C"),
                            am5.color("#7239EA")
                        ]);

                        series1.slices.template.setAll({
                            stroke: bgColor,
                            strokeWidth: 2,
                            tooltipText:
                                "{category}: [bold]Rp. {value} (Amount margin)"
                        });


                        var data = {!!json_encode($gross_profit)!!};

                        series0.data.setAll(data);
                        series1.data.setAll(data);

                        var legend = chart.children.push(am5.Legend.new(root, {
                            centerX: am5.percent(50),
                            x: am5.percent(50),
                            marginTop: 15,
                            marginBottom: 15,
                        }));
                        legend.data.setAll(series0.dataItems);

                        series0.appear(1000, 100);
                        series1.appear(1000, 100);

                    });
                });
            @endif

            $(function chartSalesAll() {
                am5.ready(function() {
                    var root = am5.Root.new("chartSalesAll");

                    root.setThemes([
                        am5themes_Animated.new(root)
                    ]);

                    var chart = root.container.children.push(am5percent.PieChart.new(root, {
                        layout: root.verticalLayout
                    }));

                    var series = chart.series.push(am5percent.PieSeries.new(root, {
                        valueField: "total",
                        categoryField: "company"
                    }));

                    var bgColor = root.interfaceColors.get("background");

                    @if($fields == 'QUANTITY')
                    series.slices.template.setAll({
                        stroke: bgColor,
                        strokeWidth: 2,
                        tooltipText:
                            "{category}: [bold] {value} PCS"
                    });
                    @else
                    series.slices.template.setAll({
                        stroke: bgColor,
                        strokeWidth: 2,
                        tooltipText:
                            "{category}: [bold]Rp. {value} (Selling Price)"
                    });
                    @endif

                    series.slices.template.states.create("hover", { scale: 0.95 });

                    series.get("colors").set("colors", [
                        am5.color("#009EF7"),
                        am5.color("#F1416C"),
                        am5.color("#7239EA")
                    ]);

                    var data = {!!json_encode($sales_all)!!};
                    series.data.setAll(data);

                    var legend = chart.children.push(am5.Legend.new(root, {
                        centerX: am5.percent(50),
                        x: am5.percent(50),
                        marginTop: 15,
                        marginBottom: 15
                    }));

                    legend.data.setAll(series.dataItems);

                    series.appear(1000, 100);

                });
            });

            $(function chartSalesByDate() {
                am5.ready(function() {
                    var root = am5.Root.new("chartSalesByDate");

                    root.setThemes([
                        am5themes_Animated.new(root)
                    ]);

                    var chart = root.container.children.push(
                        am5xy.XYChart.new(root, {
                            panX: true,
                            panY: true,
                            wheelX: "panX",
                            wheelY: "zoomX",
                            layout: root.verticalLayout,
                            pinchZoomX:true
                        })
                    );


                    var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                        behavior: "none"
                    }));
                    cursor.lineY.set("visible", false);

                    var data = {!!json_encode($by_date)!!};

                    var xRenderer = am5xy.AxisRendererX.new(root, {});
                        xRenderer.grid.template.set("location", 0.5);
                        xRenderer.labels.template.setAll({
                            location: 0.5,
                            multiLocation: 0.5
                    });

                    var xAxis = chart.xAxes.push(
                        am5xy.CategoryAxis.new(root, {
                            categoryField: "tanggal",
                            renderer: xRenderer,
                            tooltip: am5.Tooltip.new(root, {})
                        })
                    );

                    xAxis.data.setAll(data);

                    var yAxis = chart.yAxes.push(
                        am5xy.ValueAxis.new(root, {
                            maxPrecision: 0,
                            renderer: am5xy.AxisRendererY.new(root, {
                            })
                        })
                    );

                    function createSeries(name, field, color) {
                        @if($fields == 'QUANTITY')
                        var series = chart.series.push(
                            am5xy.LineSeries.new(root, {
                                name: name,
                                xAxis: xAxis,
                                yAxis: yAxis,
                                valueYField: field,
                                stroke: color,
                                fill: color,
                                categoryXField: "tanggal",
                                tooltip: am5.Tooltip.new(root, {
                                    pointerOrientation: "horizontal",
                                    labelText: "[bold]{name}[/]\n[bold] Tanggal {categoryX} : {valueY} PCS"
                                })
                            })
                        );
                        @else
                        var series = chart.series.push(
                            am5xy.LineSeries.new(root, {
                                name: name,
                                xAxis: xAxis,
                                yAxis: yAxis,
                                valueYField: field,
                                stroke: color,
                                fill: color,
                                categoryXField: "tanggal",
                                tooltip: am5.Tooltip.new(root, {
                                    pointerOrientation: "horizontal",
                                    labelText: "[bold]{name}[/]\n[bold] Tanggal {categoryX} : Rp. {valueY}"
                                })
                            })
                        );
                        @endif


                        series.bullets.push(function() {
                            return am5.Bullet.new(root, {
                                sprite: am5.Circle.new(root, {
                                    radius: 5,
                                    fill: color
                                })
                            });
                        });

                        series.set("setStateOnChildren", true);
                        series.states.create("hover", {});

                        series.mainContainer.set("setStateOnChildren", true);
                        series.mainContainer.states.create("hover", {});

                        series.strokes.template.states.create("hover", {
                            strokeWidth: 4
                        });

                        series.data.setAll(data);
                        series.appear(1000);
                    }

                    createSeries("Pusat", "pusat", am5.color("#009EF7"));
                    createSeries("Part Center", "pc", am5.color("#F1416C"));
                    createSeries("Online", "online", am5.color("#7239EA"));

                    var legend = chart.children.push(
                        am5.Legend.new(root, {
                            centerX: am5.p50,
                            x: am5.p50
                        })
                    );

                    legend.itemContainers.template.states.create("hover", {});

                    legend.itemContainers.template.events.on("pointerover", function(e) {
                        e.target.dataItem.dataContext.hover();
                    });
                    legend.itemContainers.template.events.on("pointerout", function(e) {
                        e.target.dataItem.dataContext.unhover();
                    });

                    legend.data.setAll(chart.series.values);

                    chart.appear(1000, 100);

                });
            });

            $(function chartSalesByProduct() {
                am5.ready(function() {
                    var root = am5.Root.new("chartSalesByProduct");

                    root.setThemes([
                        am5themes_Animated.new(root)
                    ]);

                    var chart = root.container.children.push(am5xy.XYChart.new(root, {
                        panX: false,
                        panY: false,
                        layout: root.verticalLayout
                    }));

                    var legend = chart.children.push(am5.Legend.new(root, {
                        centerX: am5.p50,
                        x: am5.p50
                    }))

                    var data = {!!json_encode($by_product)!!};

                    var yAxis = chart.yAxes.push(am5xy.CategoryAxis.new(root, {
                        categoryField: "produk",
                        renderer: am5xy.AxisRendererY.new(root, {
                            inversed: true,
                            cellStartLocation: 0.1,
                            cellEndLocation: 0.9
                        })
                    }));

                    yAxis.data.setAll(data);

                    var xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {
                        renderer: am5xy.AxisRendererX.new(root, {}),
                        min: 0
                    }));

                    function createSeries(field, name, color) {
                        @if($fields == 'QUANTITY')
                        var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                            name: name,
                            xAxis: xAxis,
                            yAxis: yAxis,
                            valueXField: field,
                            categoryYField: "produk",
                            sequencedInterpolation: true,
                            fill: color,
                            tooltip: am5.Tooltip.new(root, {
                                pointerOrientation: "horizontal",
                                labelText: "[bold]{name}[/]\n{categoryY} : [bold] {valueX} PCS"
                            })
                        }));
                        @else
                        var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                            name: name,
                            xAxis: xAxis,
                            yAxis: yAxis,
                            valueXField: field,
                            categoryYField: "produk",
                            sequencedInterpolation: true,
                            fill: color,
                            tooltip: am5.Tooltip.new(root, {
                                pointerOrientation: "horizontal",
                                labelText: "[bold]{name}[/]\n{categoryY} : [bold]Rp. {valueX}"
                            })
                        }));
                        @endif

                        series.columns.template.setAll({
                            height: am5.p100
                        });

                        @if($fields == 'QUANTITY')
                        series.bullets.push(function() {
                            return am5.Bullet.new(root, {
                                locationX: 1,
                                locationY: 0.5,
                                sprite: am5.Label.new(root, {
                                    centerY: am5.p50,
                                    text: "{valueX} PCS",
                                    populateText: true
                                })
                            });
                        });
                        @else
                        series.bullets.push(function() {
                            return am5.Bullet.new(root, {
                                locationX: 1,
                                locationY: 0.5,
                                sprite: am5.Label.new(root, {
                                    centerY: am5.p50,
                                    text: "Rp. {valueX}",
                                    populateText: true
                                })
                            });
                        });
                        @endif

                        series.bullets.push(function() {
                            return am5.Bullet.new(root, {
                                locationX: 1,
                                locationY: 0.5,
                                sprite: am5.Label.new(root, {
                                    centerX: am5.p100,
                                    centerY: am5.p50,
                                    text: "{name}",
                                    fill: am5.color(0xffffff),
                                    populateText: true
                                })
                            });
                        });

                        series.data.setAll(data);
                        series.appear();

                        return series;
                    }

                    createSeries("pusat", "Pusat", am5.color("#009EF7"));
                    createSeries("pc", "Part Center", am5.color("#F1416C"));
                    createSeries("online", "Online", am5.color("#7239EA"));


                    var legend = chart.children.push(am5.Legend.new(root, {
                        centerX: am5.p50,
                        x: am5.p50
                    }));

                    legend.data.setAll(chart.series.values);

                    var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                    }));
                    cursor.lineY.set("forceHidden", true);
                    cursor.lineX.set("forceHidden", true);

                    chart.appear(1000, 100);

                });

            });
        </script>


    @endpush
@endsection
