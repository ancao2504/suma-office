@extends('layouts.main.index')
@section('title','Home')
@section('subtitle','Dashboard')
@section('container')
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Management Stock</span>
                <span class="text-muted fw-bold fs-7">Dashboard Management Stock
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
            </h3>
            <div class="card-toolbar">
                <button id="btnFilterMasterData" class="btn btn-primary">
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
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">@if(strtoupper($data_filter->fields) == 'AMOUNT') Amount @else Quantity @endif</span>
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
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">@if(strtoupper($data_filter->fields) == 'AMOUNT') Amount @else Quantity @endif</span>
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
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">@if(strtoupper($data_filter->fields) == 'AMOUNT') Amount @else Quantity @endif</span>
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
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">@if(strtoupper($data_filter->fields) == 'AMOUNT') Amount @else Quantity @endif</span>
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
                            <span class="badge badge-secondary fs-8 fw-boldest me-2">@if(strtoupper($data_filter->fields) == 'AMOUNT') Amount @else Quantity @endif</span>
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
                    <div id="chartStockByProduct" style="height: 1000px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-2" id="modalFilter">
        <div class="modal-dialog">
            <div class="modal-content" id="modalFilterContent">
                <form id="formFilter" name="formFilter" autofill="off" autocomplete="off" method="get" action="{{ route('dashboard.management.stock') }}">
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
                            <select id="selectFields" name="fields" class="form-select" data-hide-search="true">
                                <option value="QUANTITY" @if($data_filter->fields == 'QUANTITY') {{"selected"}} @endif>Quantity</option>
                                <option value="AMOUNT" @if($data_filter->fields == 'AMOUNT') {{"selected"}} @endif>Amount</option>
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
                                <input id="inputFilterProduk" name="produk" type="search" class="form-control" style="cursor: pointer;" placeholder="Semua Produk" readonly
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
        <script src="{{ asset('assets/js/suma/option/option.js') }}?time={{ time() }}"></script>
        <script src="{{ asset('assets/media/charts/amcharts/index.js') }}"></script>
        <script src="{{ asset('assets/media/charts/amcharts/percent.js') }}"></script>
        <script src="{{ asset('assets/media/charts/amcharts/xy.js') }}"></script>
        <script src="{{ asset('assets/media/charts/amcharts/Animated.js') }}"></script>
        <script src="{{ asset('assets/media/charts/amcharts/Micro.js') }}"></script>

        <script type="text/javascript">
            let data_chart = {
                'total': {!!json_encode($total)!!},
                'pembelian': {!!json_encode($pembelian)!!},
                'fs': {!!json_encode($fs)!!},
                'cno': {!!json_encode($cno)!!},
                'product': {!!json_encode($product)!!},
            }
            const data_filter = {
                'year': '{{ trim($data_filter->year) }}',
                'month': '{{ trim($data_filter->month) }}',
                'fields': '{{ trim($data_filter->fields) }}',
                'level': '{{ trim($data_filter->level) }}',
                'produk': '{{ trim($data_filter->produk) }}',
            }
        </script>
        <script src="{{ asset('assets/js/suma/dashboard/management/stock/index.js') }}?time={{ time() }}"></script>
        @if($data_filter->fields == 'QUANTITY')
        <script src="{{ asset('assets/js/suma/dashboard/management/stock/quantity.js') }}?time={{ time() }}"></script>
        @else
        <script src="{{ asset('assets/js/suma/dashboard/management/stock/amount.js') }}?time={{ time() }}"></script>
        @endif
    @endpush
@endsection
