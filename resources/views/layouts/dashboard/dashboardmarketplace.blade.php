@extends('layouts.main.index')
@section('title','Home')
@section('subtitle','Dashboard')
@section('container')
<form id="formDashboardMarketplace" action="{{ route('dashboard.marketplace.marketplace') }}" method="get">
    <div class="row g-0">
        <div class="card card-flush shadow">
            <div class="card-header align-items-center border-0 mt-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Online Marketplace</span>
                    <span class="text-muted fw-bold fs-7">Dashboard Online Marketplace
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
                </h3>
            </div>
            <div class="card-body">
                <div class="col-md-6">
                    <div class="align-items-start flex-column">
                        <div class="input-group">
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
                            <input type="text" id="inputYear" name="year" class="form-control" placeholder="Tahun" autocomplete="off"
                                @if(isset($data_filter->year)) value="{{ $data_filter->year }}" @else value="{{ old('year') }}"@endif>
                            <button id="btnSearch" class="btn btn-icon btn-primary" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-0">
        <div class="col-md-4 mt-4">
            <div class="row g-0">
                <div class="card card-flush shadow" style="background-color: #ffff;
                        background-image:url('{{ asset('assets/media/svg/shapes/wave-bg-dark.svg') }}');background-size: 1200px;">
                    @php
                        $total_faktur_dipilih = 0;
                        $total_faktur_lalu = 0;
                        $total_prosentase = 0;
                    @endphp

                    @foreach ($data_sales_by_amount as $data_faktur)
                    @php
                        $total_faktur_dipilih = (double)$total_faktur_dipilih + (double)$data_faktur->bulan_dipilih->total;
                        $total_faktur_lalu = (double)$total_faktur_lalu + (double)$data_faktur->bulan_lalu->total;
                    @endphp
                    @endforeach

                    @php
                         $total_prosentase = (empty($total_faktur_lalu) || $total_faktur_lalu == 0) ? 0 : (($total_faktur_dipilih - $total_faktur_lalu) / $total_faktur_lalu) * 100;
                    @endphp
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <div class="d-flex align-items-center">
                                <span class="fs-4 fw-bolder text-gray-800 me-1 align-self-start">Rp.</span>
                                <span class="fs-2hx fw-bolder text-dark me-2 lh-1 ls-n2">{{ number_format($total_faktur_dipilih) }}</span>
                            </div>
                            <span class="text-gray-800 pt-1 fw-bolder fs-6">Grand Total</span>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-end pt-0 mt-4">
                        <div class="d-flex align-items-center fw-bolder fs-6 text-dark w-100 mt-auto mb-2">
                            <span class="me-4">Sales All</span>
                            <span class="badge badge-danger fs-base">
                                <span class="svg-icon svg-icon-5 svg-icon-white ms-n1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="11" y="18" width="13" height="2" rx="1" transform="rotate(-90 11 18)" fill="currentColor"></rect>
                                        <path d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z" fill="currentColor"></path>
                                    </svg>
                                </span>{{ number_format($total_prosentase, 2) }} %
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-0 mt-4">
                <div class="card card-flush shadow h-xl-100">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="fw-bolder mb-2 text-dark">By Amount Total</span>
                            <span class="text-muted fw-bold fs-7">
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
                        </h3>
                    </div>
                    <div class="card-body p-2">
                        <div class="table-responsive ps-8 pe-8" style="max-height: 500px; overflow: auto;">
                            <table id="tableAmount" class="table table-fixed table-row-bordered">
                                <tbody>
                                    @foreach($data_sales_by_amount as $data)
                                    <tr>
                                        <td>
                                            <div class="d-flex">
                                                <div class="symbol symbol-40px me-4">
                                                    @if(strtoupper(trim($data->kode_lokasi)) == 'OB')
                                                    <div class="symbol-label fs-3 fw-bold bg-danger text-inverse-danger">{{ strtoupper(trim($data->kode_lokasi)) }}</div>
                                                    @elseif(strtoupper(trim($data->kode_lokasi)) == 'OK')
                                                    <div class="symbol-label fs-3 fw-bold bg-info text-inverse-info">{{ strtoupper(trim($data->kode_lokasi)) }}</div>
                                                    @elseif(strtoupper(trim($data->kode_lokasi)) == 'OL')
                                                    <div class="symbol-label fs-3 fw-bold bg-success text-inverse-success">{{ strtoupper(trim($data->kode_lokasi)) }}</div>
                                                    @elseif(strtoupper(trim($data->kode_lokasi)) == 'OP')
                                                    <div class="symbol-label fs-3 fw-bold bg-primary text-inverse-primary">{{ strtoupper(trim($data->kode_lokasi)) }}</div>
                                                    @elseif(strtoupper(trim($data->kode_lokasi)) == 'OS')
                                                    <div class="symbol-label fs-3 fw-bold bg-warning text-inverse-warning">{{ strtoupper(trim($data->kode_lokasi)) }}</div>
                                                    @elseif(strtoupper(trim($data->kode_lokasi)) == 'OT')
                                                    <div class="symbol-label fs-3 fw-bold bg-dark text-inverse-dark">{{ strtoupper(trim($data->kode_lokasi)) }}</div>
                                                    @endif
                                                </div>
                                                <div class="d-flex flex-row-fluid flex-wrap">
                                                    <div class="flex-grow-1 me-2">
                                                        <span class="text-gray-800 fs-7 fw-boldest">{{ trim($data->keterangan) }}</span>
                                                        <span class="text-gray-600 fw-bolder d-block fs-7">Rp. {{ number_format($data->bulan_dipilih->total) }}</span>

                                                        <div class="align-items-center">
                                                            @if((double)$data->prosentase > 0)
                                                            <span class="badge badge-light-success fs-8 fw-boldest">
                                                                <span class="svg-icon svg-icon-5 svg-icon-success ms-n1">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                        <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor"></rect>
                                                                        <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor"></path>
                                                                    </svg>
                                                                </span>{{ $data->prosentase }} %
                                                            </span>
                                                            @elseif((double)$data->prosentase == 0)
                                                            <span class="badge badge-light-primary fs-8 fw-boldest">
                                                                <span class="svg-icon svg-icon-5 svg-icon-primary ms-n1">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                        <rect x="6" y="11" width="12" height="2" rx="1" fill="currentColor"/>
                                                                    </svg>
                                                                </span>{{ $data->prosentase }} %
                                                            </span>
                                                            @else
                                                            <span class="badge badge-light-danger fs-8 fw-boldest">
                                                                <span class="svg-icon svg-icon-5 svg-icon-danger ms-n1">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                        <rect opacity="0.5" x="11" y="18" width="13" height="2" rx="1" transform="rotate(-90 11 18)" fill="currentColor"></rect>
                                                                        <path d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z" fill="currentColor"></path>
                                                                    </svg>
                                                                </span>{{ $data->prosentase }} %
                                                            </span>
                                                            @endif

                                                            <span class="text-muted fs-8 fw-bold ms-1">(Dari Bulan Lalu)</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8 mt-4">
            <div class="card card-flush shadow h-xl-100 ms-4">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="fw-bolder mb-2 text-dark">By Percentage</span>
                        <span class="text-muted fw-bold fs-7">
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
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-4" id="chartSalesByPercentage" style="height: 600px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-0">
        <div class="col-lg-12 mt-6">
            <div class="card card-flush shadow h-xl-100">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="fw-bolder mb-2 text-dark">By Date</span>
                        <span class="text-muted fw-bold fs-7">
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
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-4" id="chartSalesByDate" style="height: 450px;"></div>
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
<script>
    let data_chart = {
        'sales_by_location': {!!json_encode($data_sales_by_location)!!},
        'sales_by_date': {!!json_encode($data_sales_by_date)!!},
     }
</script>

<script src="{{ asset('assets/js/suma/dashboard/marketplace/dashboardmarketplace.js') }}?time={{ time() }}"></script>

@endpush
@endsection
