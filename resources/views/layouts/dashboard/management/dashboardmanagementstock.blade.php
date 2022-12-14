@extends('layouts.main.index')
@section('title','Home')
@section('subtitle','Dashboard')
@section('container')
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Management Stock</span>
                <span class="text-muted fw-boldest fs-7">Dashboard Management Stock
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
                <button id="btnFilter" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFilter">
                    <i class="bi bi-funnel-fill fs-4 me-2"></i>Filter
                </button>
            </div>
        </div>
    </div>

    <div class="row mt-8">
        <div class="col-lg-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder text-gray-800">Stock All</span>
                        <div class="d-flex align-items-center mt-2">
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">{{ $fields }}</span>
                            @if($level == "")
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : ALL</span>
                            @else
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : {{ $level }}</span>
                            @endif
                            @if($produk != "")
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">PRODUK : {{ $produk }}</span>
                            @endif
                        </div>
                    </h3>
                </div>

                <div class="card-body pt-8">
                    <div id="chartStockAll" style="height: 350px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder text-gray-800">Pembelian</span>
                        <div class="d-flex align-items-center mt-2">
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">{{ $fields }}</span>
                            @if($level == "")
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : ALL</span>
                            @else
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : {{ $level }}</span>
                            @endif
                            @if($produk != "")
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">PRODUK : {{ $produk }}</span>
                            @endif
                        </div>
                    </h3>
                </div>

                <div class="card-body pt-8">
                    <div id="chartPembelian" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-6">
        <div class="col-lg-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder text-gray-800">FS</span>
                        <div class="d-flex align-items-center mt-2">
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">{{ $fields }}</span>
                            @if($level == "")
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : ALL</span>
                            @else
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : {{ $level }}</span>
                            @endif
                            @if($produk != "")
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">PRODUK : {{ $produk }}</span>
                            @endif
                        </div>
                    </h3>
                </div>

                <div class="card-body pt-8">
                    <div id="chartFS" style="height: 350px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder text-gray-800">CNO</span>
                        <div class="d-flex align-items-center mt-2">
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">{{ $fields }}</span>
                            @if($level == "")
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : ALL</span>
                            @else
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : {{ $level }}</span>
                            @endif
                            @if($produk != "")
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">PRODUK : {{ $produk }}</span>
                            @endif
                        </div>
                    </h3>
                </div>

                <div class="card-body pt-8">
                    <div id="chartCNO" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-6">
        <div class="col-lg-12">
            <div class="card card-flush">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder text-gray-800">Sales By Product</span>
                        <div class="d-flex align-items-center mt-2">
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">{{ $fields }}</span>
                            @if($level == "")
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : ALL</span>
                            @else
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">LEVEL : {{ $level }}</span>
                            @endif
                            @if($produk != "")
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">PRODUK : {{ $produk }}</span>
                            @endif
                        </div>
                    </h3>
                </div>

                <div class="card-body pt-8">
                    <div id="chartStockByProduct" style="height: 1000px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-2" id="modalFilter">
        <div class="modal-dialog">
            <div class="modal-content" id="modalFilterContent">
                <form id="formFilter" name="formFilter" autofill="off" autocomplete="off" method="get" action="{{ route('dashboard.dashboard-management-stock') }}">
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
                            <input type="text" id="inputFilterYear" name="year" class="form-control" placeholder="Tahun" autocomplete="off"
                                @if(isset($year)) value="{{ $year }}" @else value="{{ old('year') }}"@endif>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label required">Bulan:</label>
                            <select id="selectFilterMonth" name="month" class="form-select" data-hide-search="true">
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
                        <div class="fv-row mt-8">
                            <label class="form-label required">Data yang ditampilkan:</label>
                            <select id="selectFields" name="fields" class="form-select" data-hide-search="true">
                                <option value="QUANTITY" @if($fields == 'QUANTITY') {{"selected"}} @endif>Quantity</option>
                                <option value="AMOUNT" @if($fields == 'AMOUNT') {{"selected"}} @endif>Amount</option>
                            </select>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label">Level Produk:</label>
                            <select id="selectFilterLevelProduk" name="level" class="form-select" data-placeholder="Semua Level Produk" data-allow-clear="true">
                                <option value="" @if($level != 'HANDLE' && $level != 'NON_HANDLE' && $level != 'TUBE' && $level != 'OLI') selected @endif>Semua Level Produk</option>
                                <option value="HANDLE" @if($level == 'HANDLE') selected @endif>Handle</option>
                                <option value="NON_HANDLE" @if($level == 'NON_HANDLE') selected @endif>Non-Handle</option>
                                <option value="TUBE" @if($level == 'TUBE') selected @endif>Tube</option>
                                <option value="OLI" @if($level == 'OLI') selected @endif>Oli</option>
                            </select>
                        </div>
                        <div class="fv-row mt-8">
                            <label class="form-label">Produk:</label>
                            <div class="input-group">
                                <input id="inputFilterKodeProduk" name="produk" type="search" class="form-control" style="cursor: pointer;" placeholder="Semua Produk" readonly
                                    @if(isset($produk)) value="{{ $produk }}" @else value="{{ old('produk') }}"@endif>
                                <button id="btnFilterProduk" name="btnFilterProduk" class="btn btn-icon btn-primary" type="button"
                                    data-toggle="modal" data-target="#produkSearchModalForm">
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
        <script src="{{ asset('assets/js/suma/option/option.js') }}?time={{ time() }}"></script>
        <script src="{{ asset('assets/media/charts/amcharts/index.js') }}"></script>
        <script src="{{ asset('assets/media/charts/amcharts/percent.js') }}"></script>
        <script src="{{ asset('assets/media/charts/amcharts/xy.js') }}"></script>
        <script src="{{ asset('assets/media/charts/amcharts/Animated.js') }}"></script>
        <script src="{{ asset('assets/media/charts/amcharts/Micro.js') }}"></script>

        <script type="text/javascript">
            let data_chart = {
                'month': {{ $month }},
                'total': {!!json_encode($total)!!},
                'pembelian':{!!json_encode($pembelian)!!},
                'fs':{!!json_encode($fs)!!},
                'cno':{!!json_encode($cno)!!},
                'product':{!!json_encode($product)!!},
            }
        </script>
        <script src="{{ asset('assets/js/suma/dashboard/management/stock/dashboardmanagementstock.js') }}?time={{ time() }}"></script>
        @if($fields == 'QUANTITY')
            <script src="{{ asset('assets/js/suma/dashboard/management/stock/dashboardmanagementstockQuantity.js') }}?time={{ time() }}"></script>
        @else
            <script src="{{ asset('assets/js/suma/dashboard/management/stock/dashboardmanagementstockAmount.js') }}?time={{ time() }}"></script>
        @endif
    @endpush
@endsection
