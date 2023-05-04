@extends('layouts.main.index')
@section('title','Home')
@section('subtitle','Dashboard')
@section('container')
<div class="row g-0">
    <div class="row g-0">
        <div class="card card-flush shadow">
            <div class="card-header align-items-center border-0 mt-4 mb-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Management Sales</span>
                    <span class="text-muted fw-bold fs-7">Dashboard Penjualan Tahun
                        <span class="text-dark fw-bolder fs-7">{{ $data_filter->year }}</span>
                    </span>
                </h3>
                <div class="card-toolbar">
                    <button id="btnFilterMasterData" class="btn btn-primary">
                        <i class="bi bi-funnel-fill fs-4 me-2"></i>Filter
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-0 mt-6">
        <div class="col-7">
            <div class="card card-flush shadow">
                <div class="card-header align-items-center border-0 mt-4 mb-4">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="fw-bolder mb-2 text-dark">Year To Date</span>
                        <span class="text-muted fw-bold fs-7">Penjualan Tahun
                            <span class="text-dark fw-bolder fs-7">{{ $data_filter->year }}</span>
                        </span>
                        <div class="mt-2">
                            @if($data_filter->fields == 'COST_PRICE')
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">COST PRICE</span>
                            @else
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">SELLING PRICE</span>
                            @endif
                            @if($data_filter->option_company == 'SEMUA_COMPANY')
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">SEMUA COMPANY</span>
                            @elseif($data_filter->option_company == 'SEMUA_CABANG')
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">SEMUA CABANG</span>
                            @elseif($data_filter->option_company == 'COMPANY_TERTENTU')
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">COMPANY TERTENTU</span>
                            @endif
                            @if($data_filter->option_company == 'COMPANY_TERTENTU')
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->companyid }}</span>
                            @endif
                            @if(!empty($data_filter->kabupaten))
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->kabupaten }}</span>
                            @endif
                            @if(!empty($data_filter->supervisor))
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->supervisor }}</span>
                            @endif
                            @if(!empty($data_filter->salesman))
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->salesman }}</span>
                            @endif
                            @if(!empty($data_filter->produk))
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->produk }}</span>
                            @endif
                        </div>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-4" id="chartYearToDate" style="height: 500px;"></div>
                </div>
            </div>
        </div>
        <div class="col-5">
            <div class="card card-flush shadow ms-4">
                <div class="card-header align-items-center border-0 mt-4 mb-4">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="fw-bolder mb-2 text-dark">Semester</span>
                        <span class="text-muted fw-bold fs-7">Data per-semester
                            <span class="text-dark fw-bolder fs-7">{{ $data_filter->year }}</span>
                        </span>
                        <div class="mt-2">
                            @if($data_filter->fields == 'COST_PRICE')
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">COST PRICE</span>
                            @else
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">SELLING PRICE</span>
                            @endif
                            @if($data_filter->option_company == 'SEMUA_COMPANY')
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">SEMUA COMPANY</span>
                            @elseif($data_filter->option_company == 'SEMUA_CABANG')
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">SEMUA CABANG</span>
                            @elseif($data_filter->option_company == 'COMPANY_TERTENTU')
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">COMPANY TERTENTU</span>
                            @endif
                            @if($data_filter->option_company == 'COMPANY_TERTENTU')
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->companyid }}</span>
                            @endif
                            @if(!empty($data_filter->kabupaten))
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->kabupaten }}</span>
                            @endif
                            @if(!empty($data_filter->supervisor))
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->supervisor }}</span>
                            @endif
                            @if(!empty($data_filter->salesman))
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->salesman }}</span>
                            @endif
                            @if(!empty($data_filter->produk))
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->produk }}</span>
                            @endif
                        </div>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-4" id="chartSemester" style="height: 500px;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-0 mt-6">
        <div class="card card-flush shadow">
            <div class="card-header align-items-center border-0 mt-4 mb-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Tahunan</span>
                    <span class="text-muted fw-bold fs-7">Penjualan Tahun
                        <span class="text-dark fw-bolder fs-7">{{ $data_filter->year }}</span>
                    </span>
                    <div class="mt-2">
                        @if($data_filter->fields == 'COST_PRICE')
                        <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">COST PRICE</span>
                        @else
                        <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">SELLING PRICE</span>
                        @endif
                        @if($data_filter->option_company == 'SEMUA_COMPANY')
                        <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">SEMUA COMPANY</span>
                        @elseif($data_filter->option_company == 'SEMUA_CABANG')
                        <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">SEMUA CABANG</span>
                        @elseif($data_filter->option_company == 'COMPANY_TERTENTU')
                        <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">COMPANY TERTENTU</span>
                        @endif
                        @if($data_filter->option_company == 'COMPANY_TERTENTU')
                        <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->companyid }}</span>
                        @endif
                        @if(!empty($data_filter->kabupaten))
                        <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->kabupaten }}</span>
                        @endif
                        @if(!empty($data_filter->supervisor))
                        <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->supervisor }}</span>
                        @endif
                        @if(!empty($data_filter->salesman))
                        <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->salesman }}</span>
                        @endif
                        @if(!empty($data_filter->produk))
                        <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->produk }}</span>
                        @endif
                    </div>
                </h3>
            </div>
            <div class="card-body">
                <div class="mb-4" id="chartDetailPerBulan" style="height: 500px;"></div>
            </div>
        </div>
    </div>
    <div class="row g-0 mt-6">
        <div class="col-6">
            <div class="card card-flush shadow me-2">
                <div class="card-header align-items-center border-0 mt-4 mb-4">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="fw-bolder mb-2 text-dark">Quarter</span>
                        <span class="text-muted fw-bold fs-7">Kuartal Tahun
                            <span class="text-dark fw-bolder fs-7">{{ $data_filter->year }}</span>
                        </span>
                        <div class="mt-2">
                            @if($data_filter->fields == 'COST_PRICE')
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">COST PRICE</span>
                            @else
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">SELLING PRICE</span>
                            @endif
                            @if($data_filter->option_company == 'SEMUA_COMPANY')
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">SEMUA COMPANY</span>
                            @elseif($data_filter->option_company == 'SEMUA_CABANG')
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">SEMUA CABANG</span>
                            @elseif($data_filter->option_company == 'COMPANY_TERTENTU')
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">COMPANY TERTENTU</span>
                            @endif
                            @if($data_filter->option_company == 'COMPANY_TERTENTU')
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->companyid }}</span>
                            @endif
                            @if(!empty($data_filter->kabupaten))
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->kabupaten }}</span>
                            @endif
                            @if(!empty($data_filter->supervisor))
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->supervisor }}</span>
                            @endif
                            @if(!empty($data_filter->salesman))
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->salesman }}</span>
                            @endif
                            @if(!empty($data_filter->produk))
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->produk }}</span>
                            @endif
                        </div>
                    </h3>
                </div>
                <div class="card-body">
                    <div id="chartDetailQuarter" style="height: 350px;"></div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card card-flush shadow ms-2">
                <div class="card-header align-items-center border-0 mt-4 mb-4">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="fw-bolder mb-2 text-dark">Quarter To Date</span>
                        <span class="text-muted fw-bold fs-7">Kuartal Berjalan Tahun
                            <span class="text-dark fw-bolder fs-7">{{ $data_filter->year }}</span>
                        </span>
                        <div class="mt-2">
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->fields }}</span>
                            @if(empty($data_filter->option_company) || $data_filter->option_company == '')
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">SEMUA COMPANY</span>
                            @elseif($data_filter->option_company == 'SEMUA_CABANG')
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">SEMUA CABANG</span>
                            @elseif($data_filter->option_company == 'COMPANY_TERTENTU')
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">COMPANY TERTENTU</span>
                            @endif
                            @if($data_filter->option_company == 'COMPANY_TERTENTU')
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->companyid }}</span>
                            @endif
                            @if(!empty($data_filter->kabupaten))
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->kabupaten }}</span>
                            @endif
                            @if(!empty($data_filter->supervisor))
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->supervisor }}</span>
                            @endif
                            @if(!empty($data_filter->salesman))
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->salesman }}</span>
                            @endif
                            @if(!empty($data_filter->produk))
                            <span class="badge badge-secondary fs-8 fw-boldest mt-2 me-2">{{ $data_filter->produk }}</span>
                            @endif
                        </div>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-4" id="chartSummaryQuarter" style="height: 350px;"></div>
                </div>
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
                        <label class="form-label required">Tahun:</label>
                        <input type="number" id="inputFilterYear" name="year" class="form-control" placeholder="Tahun" autocomplete="off"
                            @if(isset($data_filter->year)) value="{{ $data_filter->year }}" @else value="{{ old('year') }}"@endif>
                    </div>
                    <div class="fv-row mt-6">
                        <label class="form-label required">Jenis Company:</label>
                        <select id="selectFilterOptionCompany" name="option_company" class="form-select" data-hide-search="true">
                            <option value="SEMUA_COMPANY" @if($data_filter->option_company == 'SEMUA_COMPANY') {{"selected"}} @endif>Semua Company</option>
                            <option value="SEMUA_CABANG" @if($data_filter->option_company == 'SEMUA_CABANG') {{"selected"}} @endif>Semua Cabang</option>
                            <option value="COMPANY_TERTENTU" @if($data_filter->option_company == 'COMPANY_TERTENTU') {{"selected"}} @endif>Company Tertentu</option>
                        </select>
                    </div>
                    <div class="fv-row mt-6">
                        <label class="form-label required">Company ID:</label>
                        <div class="input-group">
                            <input id="inputFilterCompanyId" name="companyid" type="search" class="form-control" style="cursor: pointer;" placeholder="Semua Company" readonly
                                @if(isset($data_filter->companyid)) value="{{ $data_filter->companyid }}" @else value="{{ old('companyid') }}"@endif>
                            <button id="btnFilterCompanyId" name="btnFilterCompanyId" class="btn btn-icon btn-primary" type="button">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="fv-row mt-8">
                        <label class="form-label required">Data yang ditampilkan:</label>
                        <select id="selectFilterFields" name="fields" class="form-select" data-hide-search="true">
                            <option value="COST_PRICE" @if($data_filter->fields == 'COST_PRICE') {{"selected"}} @endif>Cost Price</option>
                            <option value="SELLING_PRICE" @if($data_filter->fields == 'SELLING_PRICE') {{"selected"}} @endif>Selling Price</option>
                        </select>
                    </div>
                    <div class="fv-row mt-8">
                        <label class="form-label required">Kabupaten:</label>
                        <div class="input-group">
                            <input id="inputFilterKabupaten" name="kabupaten" type="search" class="form-control" style="cursor: pointer;" placeholder="Semua Kabupaten" readonly
                                @if(isset($data_filter->kabupaten)) value="{{ $data_filter->kabupaten }}" @else value="{{ old('kabupaten') }}"@endif>
                            <button id="btnFilterKabupaten" name="btnFilterKabupaten" class="btn btn-icon btn-primary" type="button">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="fv-row mt-8">
                        <label class="form-label">Supervisor:</label>
                        <div class="input-group">
                            <input id="inputFilterSupervisor" name="supervisor" type="search" class="form-control" style="cursor: pointer;" placeholder="Semua Supervisor" readonly
                                @if(isset($data_filter->supervisor)) value="{{ $data_filter->supervisor }}" @else value="{{ old('supervisor') }}"@endif>
                            <button id="btnFilterSupervisor" name="btnFilterSupervisor" class="btn btn-icon btn-primary" type="button">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="fv-row mt-8">
                        <label class="form-label">Salesman:</label>
                        <div class="input-group">
                            <input id="inputFilterSalesman" name="salesman" type="search" class="form-control" style="cursor: pointer;" placeholder="Semua Salesman" readonly
                                @if(isset($data_filter->salesman)) value="{{ $data_filter->salesman }}" @else value="{{ old('salesman') }}"@endif>
                            <button id="btnFilterSalesman" name="btnFilterSalesman" class="btn btn-icon btn-primary" type="button">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
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
                        <button id="btnFilterProses" type="button" class="btn btn-primary">Terapkan</button>
                        <button id="btnFilterClose" name="btnClose" type="button" class="btn btn-light text-end" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@include('layouts.option.optioncompany')
@include('layouts.option.optionkabupaten')
@include('layouts.option.optionsupervisor')
@include('layouts.option.optionsalesman')
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
    const data_default = {
        'companyid': '{{ trim($data_default->companyid) }}',
        'role_id': '{{ trim($data_default->role_id) }}',
        'user_id': '{{ trim($data_default->user_id) }}',
    }
    const data_filter = {
        'year': '{{ trim($data_filter->year) }}',
        'option_company': '{{ trim($data_filter->option_company) }}',
        'companyid': '{{ trim($data_filter->companyid) }}',
        'fields': '{{ trim($data_filter->fields) }}',
        'supervisor': '{{ trim($data_filter->supervisor) }}',
        'salesman': '{{ trim($data_filter->salesman) }}',
        'produk': '{{ trim($data_filter->produk) }}',
    }
    let data_chart = {
        'year_to_date': {!!json_encode($data_year_to_date)!!},
        'semester': {!!json_encode($data_semester)!!},
        'detail_per_bulan': {!!json_encode($data_detail_per_bulan)!!},
        'detail_quarter': {!!json_encode($data_detail_quarter)!!},
        'summary_quarter': {!!json_encode($data_summary_quarter)!!},
    }
</script>
<script src="{{ asset('assets/js/suma/dashboard/management/quarter/index.js') }}"></script>
<script src="{{ asset('assets/js/suma/dashboard/management/quarter/amount.js') }}"></script>
@endpush
@endsection
