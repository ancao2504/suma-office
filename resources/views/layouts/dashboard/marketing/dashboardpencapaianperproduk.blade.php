@extends('layouts.main.index')
@section('title','Home')
@section('subtitle','Dashboard')
@section('container')
<div class="card card-flush">
    <div class="card-header align-items-center border-0 mt-4 mb-4">
        <h3 class="card-title align-items-start flex-column">
            <span class="fw-bolder mb-2 text-dark">Dashboard Marketing</span>
            <span class="text-muted fw-bolder fs-7">Pencapaian Per-Produk {{ $year }}
            </span>
        </h3>
        <div class="card-toolbar">
            <button id="btnFilter" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFilter">
                <i class="bi bi-funnel-fill fs-4 me-2"></i>Filter
            </button>
        </div>
    </div>
</div>
<div class="card card-flush mt-4">
    <div class="card-header align-items-center border-0 mt-4">
        <h3 class="card-title align-items-start flex-column">
            <span class="fw-boldest mb-2 text-dark">Data Pencapaian Per-Produk</span>
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div id="chartPencapaianPerProduk" style="height: 1000px; width: 100%;"></div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-2" id="modalFilter">
    <div class="modal-dialog">
        <div class="modal-content" id="modalFilterContent">
            <form id="formFilter" name="formFilter" autofill="off" autocomplete="off" method="get" action="{{ route('dashboard.dashboard-marketing-pencapaian-perproduk') }}">
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
                        <label class="form-label">Level Produk:</label>
                        <select id="selectFilterLevelProduk" name="level_produk" class="form-select" data-placeholder="Semua Level Produk" data-allow-clear="true">
                            <option value="" @if($level_produk != 'HANDLE' && $level_produk != 'NON_HANDLE' && $level_produk != 'TUBE' && $level_produk != 'OLI') selected @endif>Semua Level Produk</option>
                            <option value="HANDLE" @if($level_produk == 'HANDLE') selected @endif>Handle</option>
                            <option value="NON_HANDLE" @if($level_produk == 'NON_HANDLE') selected @endif>Non-Handle</option>
                            <option value="TUBE" @if($level_produk == 'TUBE') selected @endif>Tube</option>
                            <option value="OLI" @if($level_produk == 'OLI') selected @endif>Oli</option>
                        </select>
                    </div>
                    <div class="fv-row mt-8">
                        <label class="form-label">Produk:</label>
                        <div class="input-group">
                            <input id="inputFilterKodeProduk" name="kode_produk" type="search" class="form-control" placeholder="Semua Produk" readonly
                                @if(isset($kode_produk)) value="{{ $kode_produk }}" @else value="{{ old('kode_produk') }}"@endif>
                            <button id="btnFilterProduk" name="btnFilterProduk" class="btn btn-icon btn-primary" type="button"
                                data-toggle="modal" data-target="#produkSearchModalForm">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="fv-row mt-8">
                        <label class="form-label">Salesman:</label>
                        <select id="selectFilterJenisMkr" name="jenis_mkr" class="form-select" data-placeholder="Semua Jenis Marketing" data-allow-clear="true">
                            <option value="" @if($jenis_mkr != 'SALESMAN' && $jenis_mkr != 'SUPERVISOR') selected @endif>Semua Marketing</option>
                            <option value="SALESMAN" @if($jenis_mkr == 'SALESMAN') selected @endif>SALESMAN</option>
                            <option value="SUPERVISOR" @if($jenis_mkr == 'SUPERVISOR') selected @endif>SUPERVISOR</option>
                        </select>
                    </div>
                    <div class="fv-row mt-8">
                        <label id="labelKodeMkr" class="form-label">Kode Marketing:</label>
                        <div class="input-group">
                            <input id="inputFilterKodeMkr" name="kode_mkr" type="search" class="form-control" placeholder="Semua Marketing" readonly
                                @if(isset($kode_mkr)) value="{{ $kode_mkr }}" @else value="{{ old('kode_mkr') }}"@endif>
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
                        <button id="btnFilterProses" type="submit" class="btn btn-primary">Terapkan</button>
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
<script src="{{ asset('assets/js/suma/option/option.js') }}"></script>
<script src="{{ asset('assets/media/charts/amcharts/index.js') }}"></script>
<script src="{{ asset('assets/media/charts/amcharts/xy.js') }}"></script>
<script src="{{ asset('assets/media/charts/amcharts/Animated.js') }}"></script>

<script type="text/javascript">
    window.onload = function() {
        var marketing = '{{$jenis_mkr}}';

        getJenisMkr(marketing);
    }

    var btnFilterProses = document.querySelector("#btnFilterProses");
    btnFilterProses.addEventListener("click", function(e) {
        e.preventDefault();
        loading.block();
        document.getElementById("formFilter").submit();
    });

    function getJenisMkr(selectFilterJenisMkr = '') {
        if(selectFilterJenisMkr == 'SUPERVISOR') {
            $("#labelKodeMkr").html("Supervisor");
            $("#btnFilterMarketing").prop("disabled", false);
        } else if(selectFilterJenisMkr == 'SALESMAN') {
            $("#labelKodeMkr").html("Salesman");
            $("#btnFilterMarketing").prop("disabled", false);
        } else {
            $("#labelKodeMkr").html("Marketing");
            $('#inputFilterKodeMkr').val('');
            $("#btnFilterMarketing").prop("disabled", true);
        }
    }

    $(document).ready(function() {
        $('#btnFilter').on('click', function(e) {
            e.preventDefault();
            $('#modalFilter').modal('show');
        });

        $('#btnFilterReset').on('click', function(e) {
            e.preventDefault();
            var dateObj = new Date();
            var year = dateObj.getUTCFullYear();
            $('#inputYear').val(year);
            $('#selectFilterLevelProduk').prop('selectedIndex', 0).change();
            $('#inputFilterKodeProduk').val('');
            $('#selectFilterJenisMkr').prop('selectedIndex', 0).change();
            $('#inputFilterKodeMkr').val('');
        });

        $('#btnFilterProduk').on('click', function(e) {
            e.preventDefault();

            var selectFilterLevelProduk = $('#selectFilterLevelProduk').val();
            loadDataProduk(1, 10, '', selectFilterLevelProduk);
            $('#searchProdukForm').trigger('reset');
            $('#produkSearchModal').modal('show');
        });

        $('body').on('click', '#produkContentModal #selectProduk', function(e) {
            e.preventDefault();
            $('#inputFilterKodeProduk').val($(this).data('kode_produk'));
            $('#produkSearchModal').modal('hide');
        });

        $('#selectFilterLevelProduk').change(function(){
            $('#inputFilterKodeProduk').val('');
        });

        $('#btnFilterMarketing').on('click', function(e) {
            e.preventDefault();
            var jenis_mkr = document.getElementById("selectFilterJenisMkr").value;

            if(jenis_mkr == "SALESMAN") {
                loadDataSalesman();
                $('#searchSalesmanForm').trigger('reset');
                $('#salesmanSearchModal').modal('show');
            } else if(jenis_mkr == "SUPERVISOR") {
                loadDataSupervisor();
                $('#searchSupervisorForm').trigger('reset');
                $('#supervisorSearchModal').modal('show');
            }
        });

        $('body').on('click', '#salesmanContentModal #selectSalesman', function(e) {
            e.preventDefault();
            $('#inputFilterKodeMkr').val($(this).data('kode_sales'));
            $('#salesmanSearchModal').modal('hide');
        });

        $('body').on('click', '#supervisorContentModal #selectSupervisor', function(e) {
            e.preventDefault();
            $('#inputFilterKodeMkr').val($(this).data('kode_spv'));
            $('#supervisorSearchModal').modal('hide');
        });

        $('#selectFilterJenisMkr').change(function(){
            var selectFilterJenisMkr = $('#selectFilterJenisMkr').val();
            getJenisMkr(selectFilterJenisMkr);
            $('#inputFilterKodeMkr').val('');
        });
    });

    $(function chartPencapaianPerProduk() {
        am5.ready(function() {
            var root = am5.Root.new("chartPencapaianPerProduk");

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

            var data = {!!json_encode($product)!!};

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

                series.columns.template.setAll({
                    height: am5.p100
                });

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

                var cellSize = 70;
                series.events.on("datavalidated", function(ev) {
                    var series = ev.target;
                    var chart = series.chart;
                    var xAxis = chart.xAxes.getIndex(0);

                    // Calculate how we need to adjust chart height
                    var chartHeight = series.data.length * cellSize + xAxis.height() + chart.get("paddingTop", 0) + chart.get("paddingBottom", 0);

                    // Set it on chart's container
                    chart.root.dom.style.height = chartHeight + "px";
                });


                series.data.setAll(data);
                series.appear();

                return series;
            }

            createSeries("pencapaian", "Pencapaian", am5.color("#009EF7"));
            createSeries("target", "Target", am5.color("#F1416C"));


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
