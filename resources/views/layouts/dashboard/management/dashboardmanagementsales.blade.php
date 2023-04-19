@extends('layouts.main.index')
@section('title','Home')
@section('subtitle','Dashboard')
@section('container')
<div class="card card-flush shadow">
    <div class="card-header align-items-center border-0 mt-4 mb-4">
        <h3 class="card-title align-items-start flex-column">
            <span class="fw-bolder mb-2 text-dark">Management Sales</span>
            <span class="text-muted fw-bold fs-7">Dashboard Management Sales
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
            <div class="d-flex align-items-center mt-4">
                @if($data_filter->fields == 'QUANTITY')
                <span class="badge badge-secondary fs-8 fw-boldest me-2">Quantity</span>
                @elseif ($data_filter->fields == 'SELLING_PRICE_EX_PPN')
                <span class="badge badge-secondary fs-8 fw-boldest me-2">Selling Price (Exclude PPN)</span>
                @elseif ($data_filter->fields == 'SELLING_PRICE_IN_PPN')
                <span class="badge badge-secondary fs-8 fw-boldest me-2">Selling Price (Include PPN)</span>
                @endif
                @if($data_filter->level == "")
                <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : ALL</span>
                @else
                <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : {{ $data_filter->level }}</span>
                @endif
                @if($data_filter->produk != "")
                <span class="badge badge-secondary fs-8 fw-boldest me-2">PRODUK : {{ $data_filter->produk }}</span>
                @endif
            </div>
        </h3>
        <div class="card-toolbar">
            <button id="btnFilterMasterData" class="btn btn-primary">
                <i class="bi bi-funnel-fill fs-4 me-2"></i>Filter
            </button>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-6 mt-4">
        <div class="card card-flush shadow"
            style="background-color: #ffff;background-image:url('{{ asset('assets/media/svg/shapes/wave-bg-dark.svg') }}');background-size: 1200px;">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <div class="d-flex align-items-center">
                        @if($data_filter->fields != 'QUANTITY')
                        <span class="fs-4 fw-bolder text-gray-800 me-1 align-self-start">Rp.</span>
                        @endif
                        <span class="fs-2hx fw-bolder text-dark me-2 lh-1 ls-n2">{{ number_format($selling['selling_total']) }}</span>
                        @if($data_filter->fields == 'QUANTITY')
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

    @if ($data_filter->fields == 'QUANTITY')
    <div class="col-md-6 mt-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card card-flush shadow p-4"
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
                <div class="card card-flush shadow p-4"
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
                <div class="card card-flush shadow p-4"
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
        <div class="card card-flush shadow"
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
        <div class="card card-flush shadow h-xl-100">
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder text-gray-800">Sales All</span>
                    <div class="d-flex align-items-center mt-2">
                        @if($data_filter->fields == 'QUANTITY')
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">Quantity</span>
                        @elseif ($data_filter->fields == 'SELLING_PRICE_EX_PPN')
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">Selling Price (Exclude PPN)</span>
                        @elseif ($data_filter->fields == 'SELLING_PRICE_IN_PPN')
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">Selling Price (Include PPN)</span>
                        @endif
                        @if($data_filter->level == "")
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : ALL</span>
                        @else
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : {{ $data_filter->level }}</span>
                        @endif
                        @if($data_filter->produk != "")
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">PRODUK : {{ $data_filter->produk }}</span>
                        @endif
                    </div>
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
                                    Pusat : @if($data_filter->fields != 'QUANTITY') Rp. @endif {{ number_format($selling['selling_detail']['selling_pusat']->total) }} @if($data_filter->fields == 'QUANTITY') PCS @endif
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
                                    Part Center : @if($data_filter->fields != 'QUANTITY') Rp. @endif {{ number_format($selling['selling_detail']['selling_pc']->total) }} @if($data_filter->fields == 'QUANTITY') PCS @endif
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
                                    Online : @if($data_filter->fields != 'QUANTITY') Rp. @endif {{ number_format($selling['selling_detail']['selling_online']->total) }} @if($data_filter->fields == 'QUANTITY') PCS @endif
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
    @if($data_filter->fields == 'QUANTITY')
    <div class="col-lg-6 mt-6">
        <div class="card card-flush shadow h-xl-100">
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder text-gray-800">Comparison</span>
                    <div class="d-flex align-items-center mt-2">
                        @if($data_filter->fields == 'QUANTITY')
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">Quantity</span>
                        @elseif ($data_filter->fields == 'SELLING_PRICE_EX_PPN')
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">Selling Price (Exclude PPN)</span>
                        @elseif ($data_filter->fields == 'SELLING_PRICE_IN_PPN')
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">Selling Price (Include PPN)</span>
                        @endif
                        @if($data_filter->level == "")
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : ALL</span>
                        @else
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : {{ $data_filter->level }}</span>
                        @endif
                        @if($data_filter->produk != "")
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">PRODUK : {{ $data_filter->produk }}</span>
                        @endif
                    </div>
                </h3>
            </div>
            <div class="card-body pt-8">
                <div class="mb-4" id="chartComparison" style="height: 450px;"></div>
            </div>
        </div>
    </div>
    @else
    <div class="col-lg-6 mt-6">
        <div class="card card-flush shadow h-xl-100">
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder text-gray-800">Gross Profit</span>
                    <div class="d-flex align-items-center mt-2">
                        @if($data_filter->fields == 'QUANTITY')
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">Quantity</span>
                        @elseif ($data_filter->fields == 'SELLING_PRICE_EX_PPN')
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">Selling Price (Exclude PPN)</span>
                        @elseif ($data_filter->fields == 'SELLING_PRICE_IN_PPN')
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">Selling Price (Include PPN)</span>
                        @endif
                        @if($data_filter->level == "")
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : ALL</span>
                        @else
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : {{ $data_filter->level }}</span>
                        @endif
                        @if($data_filter->produk != "")
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">PRODUK : {{ $data_filter->produk }}</span>
                        @endif
                    </div>
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
        <div class="card card-flush shadow h-xl-100">
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder text-gray-800">Sales By Date</span>
                    <div class="d-flex align-items-center mt-2">
                        @if($data_filter->fields == 'QUANTITY')
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">Quantity</span>
                        @elseif ($data_filter->fields == 'SELLING_PRICE_EX_PPN')
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">Selling Price (Exclude PPN)</span>
                        @elseif ($data_filter->fields == 'SELLING_PRICE_IN_PPN')
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">Selling Price (Include PPN)</span>
                        @endif
                        @if($data_filter->level == "")
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : ALL</span>
                        @else
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : {{ $data_filter->level }}</span>
                        @endif
                        @if($data_filter->produk != "")
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">PRODUK : {{ $data_filter->produk }}</span>
                        @endif
                    </div>
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
        <div class="card card-flush shadow">
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder text-gray-800">Sales By Product</span>
                    <div class="d-flex align-items-center mt-2">
                        @if($data_filter->fields == 'QUANTITY')
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">Quantity</span>
                        @elseif ($data_filter->fields == 'SELLING_PRICE_EX_PPN')
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">Selling Price (Exclude PPN)</span>
                        @elseif ($data_filter->fields == 'SELLING_PRICE_IN_PPN')
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">Selling Price (Include PPN)</span>
                        @endif
                        @if($data_filter->level == "")
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : ALL</span>
                        @else
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : {{ $data_filter->level }}</span>
                        @endif
                        @if($data_filter->produk != "")
                        <span class="badge badge-secondary fs-8 fw-boldest me-2">PRODUK : {{ $data_filter->produk }}</span>
                        @endif
                    </div>
                </h3>
            </div>

            <div class="card-body pt-8">
                <div id="chartSalesByProduct" style="height: 1000px; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-2" id="modalFilter">
    <div class="modal-dialog">
        <div class="modal-content" id="modalFilterContent">
            <form id="formFilter" name="formFilter" autofill="off" autocomplete="off" method="get" action="#">
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
                        <select id="selectFilterMonth" name="month" class="form-select" data-hide-search="true">
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
                        <input type="number" id="inputFilterYear" name="year" class="form-control" placeholder="Tahun" autocomplete="off"
                            @if(isset($data_filter->year)) value="{{ $data_filter->year }}" @else value="{{ old('year') }}"@endif>
                    </div>
                    <div class="fv-row mt-8">
                        <label class="form-label required">Data yang ditampilkan:</label>
                        <select id="selectFilterFields" name="fields" class="form-select" data-hide-search="true">
                            <option value="QUANTITY" @if($data_filter->fields == 'QUANTITY') {{"selected"}} @endif>Quantity</option>
                            <option value="SELLING_PRICE_EX_PPN" @if($data_filter->fields == 'SELLING_PRICE_EX_PPN') {{"selected"}} @endif>Selling Price (Exclude PPN)</option>
                            <option value="SELLING_PRICE_IN_PPN" @if($data_filter->fields == 'SELLING_PRICE_IN_PPN') {{"selected"}} @endif>Selling Price (Include PPN)</option>
                        </select>
                    </div>
                    <div class="fv-row mt-8">
                        <label class="form-label">Level Produk:</label>
                        <select id="selectFilterLevelProduk" name="level" class="form-select" data-placeholder="Semua Level Produk" data-allow-clear="true">
                            <option value="" @if($data_filter->level != 'HANDLE' && $data_filter->level != 'NON_HANDLE' && $data_filter->level != 'TUBE' && $data_filter->level != 'OLI') selected @endif>Semua Level Produk</option>
                            <option value="HANDLE" @if($data_filter->level == 'HANDLE') selected @endif>Handle</option>
                            <option value="NON_HANDLE" @if($data_filter->level == 'NON_HANDLE') selected @endif>Non-Handle</option>
                            <option value="TUBE" @if($data_filter->level == 'TUBE') selected @endif>Tube</option>
                            <option value="OLI" @if($data_filter->level == 'OLI') selected @endif>Oli</option>
                        </select>
                    </div>
                    <div class="fv-row mt-8">
                        <label class="form-label">Produk:</label>
                        <div class="input-group">
                            <input id="inputFilterKodeProduk" name="produk" type="search" class="form-control" style="cursor: pointer;" placeholder="Semua Produk" readonly
                                @if(isset($data_filter->produk)) value="{{ $data_filter->produk }}" @else value="{{ old('produk') }}"@endif>
                            <button id="btnFilterProduk" name="btnFilterProduk" class="btn btn-icon btn-primary" type="button">
                                <i class="fa fa-search"></i>
                            </button>
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

@include('layouts.option.optiongroupproduk')

@push('scripts')
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
    <script src="{{ asset('assets/media/charts/amcharts/index.js') }}"></script>
    <script src="{{ asset('assets/media/charts/amcharts/percent.js') }}"></script>
    <script src="{{ asset('assets/media/charts/amcharts/xy.js') }}"></script>
    <script src="{{ asset('assets/media/charts/amcharts/Animated.js') }}"></script>
    <script src="{{ asset('assets/media/charts/amcharts/Micro.js') }}"></script>

    <script>
        const url = {
            'clossing_marketing': "{{ route('setting.default.clossing-marketing') }}",
        }
        const data_filter = {
            'year': '{{ trim($data_filter->year) }}',
            'month': '{{ trim($data_filter->month) }}',
            'fields': '{{ trim($data_filter->fields) }}',
            'level': '{{ trim($data_filter->level) }}',
            'produk': '{{ trim($data_filter->produk) }}',
        }
    </script>
    @if($data_filter->fields == 'QUANTITY')
    <script>
        let data_chart = {
            'comparison': {!!json_encode($comparison)!!},
            'sales_all': {!!json_encode($sales_all)!!},
            'by_date': {!!json_encode($by_date)!!},
            'by_product': {!!json_encode($by_product)!!},
        }
    </script>
    <script src="{{ asset('assets/js/suma/dashboard/management/sales/quantity.js') }}?time={{ time() }}"></script>
    @else
    <script>
        let data_chart = {
            'gross_profit': {!!json_encode($gross_profit)!!},
            'sales_all': {!!json_encode($sales_all)!!},
            'by_date': {!!json_encode($by_date)!!},
            'by_product': {!!json_encode($by_product)!!},
         }
    </script>
    <script src="{{ asset('assets/js/suma/dashboard/management/sales/amount.js') }}?time={{ time() }}"></script>
    @endif

    <script src="{{ asset('assets/js/suma/dashboard/management/sales/index.js') }}?time={{ time() }}"></script>
@endpush
@endsection
