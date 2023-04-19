@extends('layouts.main.index')
@section('title','Home')
@section('subtitle','Dashboard')
@section('container')
<form id="formDashboardManagementSales">
    <div class="card card-flush shadow">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Dashboard Marketing</span>
                <span class="text-muted fw-bold fs-7">Growth Pencapaian
                    <span class="text-dark fw-bolder fs-7">{{ $data_filter->year }}</span>
                </span>
            </h3>
            <div class="card-toolbar">
                <button id="btnFilterMasterData" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFilter">
                    <i class="bi bi-funnel-fill fs-4 me-2"></i>Filter
                </button>
            </div>
        </div>
    </div>
    <div class="card card-flush shadow mt-4">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Grand Total</span>
                <div class="d-flex align-items-center">
                    @if($data_filter->jenis_mkr == "")
                    <span class="badge badge-secondary fs-8 fw-boldest me-2">MKR : ALL</span>
                    @else
                    <span class="badge badge-secondary fs-8 fw-boldest me-2">MKR : {{ $data_filter->jenis_mkr }}</span>
                    @endif
                    @if($data_filter->level_produk == "")
                    <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : ALL</span>
                    @else
                    <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : {{ $data_filter->level_produk }}</span>
                    @endif
                    @if($data_filter->kode_produk != "")
                    <span class="badge badge-secondary fs-8 fw-boldest me-2">PRODUK : {{ $data_filter->kode_produk }}</span>
                    @endif
                    @if($data_filter->kode_mkr != "")
                    <span class="badge badge-secondary fs-8 fw-boldest me-2">{{ $data_filter->jenis_mkr }} : {{ $data_filter->kode_mkr }}</span>
                    @endif
                </div>
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div id="chartGrandTotal" style="height: 500px; width: 100%;"></div>
            </div>
        </div>
    </div>
    <div class="card card-flush shadow mt-4">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Total Marketing</span>
                <div class="d-flex align-items-center">
                    @if($data_filter->jenis_mkr == "")
                    <span class="badge badge-secondary fs-8 fw-boldest me-2">MKR : ALL</span>
                    @else
                    <span class="badge badge-secondary fs-8 fw-boldest me-2">MKR : {{ $data_filter->jenis_mkr }}</span>
                    @endif
                    @if($data_filter->level_produk == "")
                    <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : ALL</span>
                    @else
                    <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : {{ $data_filter->level_produk }}</span>
                    @endif
                    @if($data_filter->kode_produk != "")
                    <span class="badge badge-secondary fs-8 fw-boldest me-2">PRODUK : {{ $data_filter->kode_produk }}</span>
                    @endif
                    @if($data_filter->kode_mkr != "")
                    <span class="badge badge-secondary fs-8 fw-boldest me-2">{{ $data_filter->jenis_mkr }} : {{ $data_filter->kode_mkr }}</span>
                    @endif
                </div>
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div id="chartTotalMarketing" style="height: 500px; width: 100%;"></div>
            </div>
        </div>
    </div>
</form>

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
                        <label class="form-label required">Tahun:</label>
                        <input type="number" id="inputFilterYear" name="year" class="form-control" placeholder="Tahun"
                            @if(isset($data_filter->year)) value="{{ $data_filter->year }}" @else value="{{ old('year') }}"@endif>
                    </div>
                    <div class="fv-row mt-8">
                        <label class="form-label">Level Produk:</label>
                        <select id="selectFilterLevelProduk" name="level_produk" class="form-select" data-placeholder="Semua Level Produk" data-allow-clear="true">
                            <option value="" @if($data_filter->level_produk != 'HANDLE' && $data_filter->level_produk != 'NON_HANDLE' && $data_filter->level_produk != 'TUBE' && $data_filter->level_produk != 'OLI') selected @endif>Semua Level Produk</option>
                            <option value="HANDLE" @if($data_filter->level_produk == 'HANDLE') selected @endif>Handle</option>
                            <option value="NON_HANDLE" @if($data_filter->level_produk == 'NON_HANDLE') selected @endif>Non-Handle</option>
                            <option value="TUBE" @if($data_filter->level_produk == 'TUBE') selected @endif>Tube</option>
                            <option value="OLI" @if($data_filter->level_produk == 'OLI') selected @endif>Oli</option>
                        </select>
                    </div>
                    <div class="fv-row mt-8">
                        <label class="form-label">Produk:</label>
                        <div class="input-group">
                            <input id="inputFilterKodeProduk" name="kode_produk" type="search" class="form-control" style="cursor: pointer;" placeholder="Semua Produk" readonly
                                @if(isset($data_filter->kode_produk)) value="{{ $data_filter->kode_produk }}" @else value="{{ old('kode_produk') }}"@endif>
                            <button id="btnFilterProduk" name="btnFilterProduk" class="btn btn-icon btn-primary" type="button"
                                data-toggle="modal" data-target="#produkSearchModalForm">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="fv-row mt-8">
                        <label class="form-label">Jenis Marketing:</label>
                        <select id="selectFilterJenisMkr" name="jenis_mkr" class="form-select" data-placeholder="Semua Jenis Marketing" data-allow-clear="true">
                            <option value="" @if($data_filter->jenis_mkr != 'SALESMAN' && $data_filter->jenis_mkr != 'SUPERVISOR') selected @endif>Semua Marketing</option>
                            <option value="SALESMAN" @if($data_filter->jenis_mkr == 'SALESMAN') selected @endif>SALESMAN</option>
                            <option value="SUPERVISOR" @if($data_filter->jenis_mkr == 'SUPERVISOR') selected @endif>SUPERVISOR</option>
                        </select>
                    </div>
                    <div class="fv-row mt-8">
                        <label id="labelKodeMkr" class="form-label">Kode Marketing:</label>
                        <div class="input-group">
                            <input id="inputFilterKodeMkr" name="kode_mkr" type="search" class="form-control" style="cursor: pointer;" placeholder="Semua Marketing" readonly
                                @if(isset($data_filter->kode_mkr)) value="{{ $data_filter->kode_mkr }}" @else value="{{ old('kode_mkr') }}"@endif>
                            <button id="btnFilterMarketing" name="btnFilterMarketing" class="btn btn-icon btn-primary" type="button"
                                data-toggle="modal" data-target="#tipeMotorSearchModal">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button id="btnFilterReset" class="btn btn-danger" role="button">Reset Filter</button>
                    <div class="text-end">
                        <button id="btnFilterProses" type="button" class="btn btn-primary">Terapkan</button>
                        <button id="btnFilterClose" name="btnClose" type="button" class="btn btn-light text-end" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@include('layouts.option.optionsalesman')
@include('layouts.option.optionsupervisor')
@include('layouts.option.optiongroupproduk')

@push('scripts')
<script src="{{ asset('assets/js/suma/option/option.js') }}?time={{ time() }}"></script>
<script src="{{ asset('assets/media/charts/amcharts/index.js') }}"></script>
<script src="{{ asset('assets/media/charts/amcharts/xy.js') }}"></script>
<script src="{{ asset('assets/media/charts/amcharts/Animated.js') }}"></script>

<script type="text/javascript">
    const url = {
        'clossing_marketing': "{{ route('setting.default.clossing-marketing') }}",
    }
    let data_chart = {
        'total':{!!json_encode($total)!!},
        'marketing':{!!json_encode($marketing)!!},
    }
    const data_filter = {
        'year': "{{ $data_filter->year }}",
        'level_produk': "{{ $data_filter->level_produk }}",
        'kode_produk': "{{ $data_filter->kode_produk }}",
        'jenis_mkr': "{{ $data_filter->jenis_mkr }}",
        'kode_mkr': "{{ $data_filter->kode_mkr }}",
    }
</script>
<script src="{{ asset('assets/js/suma/dashboard/marketing/dashboardpencapaiangrowth.js') }}?time={{ time() }}"></script>
@endpush
@endsection
