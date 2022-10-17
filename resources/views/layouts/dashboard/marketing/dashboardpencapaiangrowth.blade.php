@extends('layouts.main.index')
@section('title','Home')
@section('subtitle','Dashboard')
@section('container')
<form id="formDashboardManagementSales">
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Dashboard Marketing</span>
                <span class="text-muted fw-bolder fs-7">Target & Pencapaian Per-Level {{ $year }}
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
                <span class="fw-boldest mb-2 text-dark">Grand Total</span>
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div id="chartGrandTotal" style="height: 500px; width: 100%;"></div>
            </div>
        </div>
    </div>
    <div class="card card-flush mt-4">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-boldest mb-2 text-dark">Total Marketing</span>
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
            <form id="formFilter" name="formFilter" autofill="off" autocomplete="off" method="get" action="{{ route('dashboard.dashboard-marketing-pencapaian-growth') }}">
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
                        <label class="form-label">Jenis Marketing:</label>
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
<script src="{{ asset('assets/js/suma/option/option.js') }}?time={{ time() }}"></script>
<script src="{{ asset('assets/media/charts/amcharts/index.js') }}"></script>
<script src="{{ asset('assets/media/charts/amcharts/xy.js') }}"></script>
<script src="{{ asset('assets/media/charts/amcharts/Animated.js') }}"></script>

<script type="text/javascript">
    window.onload = function() {
        var marketing = '{{$jenis_mkr}}';
        var levelProduk = '{{$level_produk}}';

        getJenisMkr(marketing);
    }

    var btnFilterProses = document.querySelector("#btnFilterProses");
    btnFilterProses.addEventListener("click", function(e) {
        e.preventDefault();
        blockIndex.block();
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

    $('#selectFilterJenisMkr').change(function(){
        var selectFilterJenisMkr = $('#selectFilterJenisMkr').val();
        getJenisMkr(selectFilterJenisMkr);
    });

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
    });

    $(function chartGrandTotal() {
        am5.ready(function() {
            var root = am5.Root.new("chartGrandTotal");

            root.setThemes([am5themes_Animated.new(root)]);

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

            var data = {!!json_encode($total)!!};

            var xAxis = chart.xAxes.push(
                am5xy.CategoryAxis.new(root, {
                    categoryField: "bulan",
                    renderer: am5xy.AxisRendererX.new(root, {}),
                    tooltip: am5.Tooltip.new(root, {})
                })
            );

            var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                        behavior: "none"
            }));
            cursor.lineY.set("visible", false);

            xAxis.data.setAll(data);

            var yAxis = chart.yAxes.push(
                am5xy.ValueAxis.new(root, {
                    min: 0,
                    extraMax: 0.1,
                    renderer: am5xy.AxisRendererY.new(root, {})
                })
            );

            var yAxis2 = chart.yAxes.push(
                am5xy.ValueAxis.new(root, {
                    maxDeviation: 0.3,
                    syncWithAxis: yAxis,
                    renderer: am5xy.AxisRendererY.new(root, { opposite: true })
                })
            );

            var series1 = chart.series.push(
                am5xy.ColumnSeries.new(root, {
                    name: "2021",
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "tahun_lalu",
                    categoryXField: "bulan",
                    fill: am5.color("#2196f3"),
                    tooltip:am5.Tooltip.new(root, {
                        labelText:"[bold]{name} {categoryX}: Rp. {valueY}"
                    })
                })
            );

            series1.columns.template.setAll({
                tooltipY: am5.percent(10),
                templateField: "columnSettings"
            });

            series1.data.setAll(data);

            var series2 = chart.series.push(
                am5xy.ColumnSeries.new(root, {
                    name: "2022",
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "tahun_sekarang",
                    categoryXField: "bulan",
                    fill: am5.color("#ec407a"),
                    tooltip:am5.Tooltip.new(root, {
                        labelText:"[bold]{name} {categoryX}: Rp. {valueY}"
                    })
                })
            );

            series2.data.setAll(data);

            var series3 = chart.series.push(
                am5xy.LineSeries.new(root, {
                    name: "Growth",
                    xAxis: xAxis,
                    yAxis: yAxis2,
                    valueYField: "growth",
                    categoryXField: "bulan",
                    stroke: am5.color("#283593"),
                    fill: am5.color("#283593"),
                    tooltip:am5.Tooltip.new(root, {
                        pointerOrientation:"horizontal",
                        labelText:"[bold]{name} {categoryX}: {valueY}%"
                    })
                })
            );

            series3.data.setAll(data);

            series3.bullets.push(function () {
                return am5.Bullet.new(root, {
                    sprite: am5.Circle.new(root, {
                        strokeWidth: 3,
                        stroke: series3.get("stroke"),
                        radius: 5,
                        fill: root.interfaceColors.get("background")
                    })
                });
            });

            chart.set("cursor", am5xy.XYCursor.new(root, {}));

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    centerX: am5.p50,
                    x: am5.p50
                })
            );
            legend.data.setAll(chart.series.values);

            chart.appear(1000, 100);
            series1.appear();

        });
    });

    $(function chartTotalMarketing() {
        am5.ready(function() {
            var root = am5.Root.new("chartTotalMarketing");

            root.setThemes([am5themes_Animated.new(root)]);

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

            var data = {!!json_encode($marketing)!!};;

            var xAxis = chart.xAxes.push(
                am5xy.CategoryAxis.new(root, {
                    categoryField: "marketing",
                    renderer: am5xy.AxisRendererX.new(root, {}),
                    tooltip: am5.Tooltip.new(root, {})
                })
            );

            var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                        behavior: "none"
            }));
            cursor.lineY.set("visible", false);

            xAxis.data.setAll(data);

            var yAxis = chart.yAxes.push(
                am5xy.ValueAxis.new(root, {
                    min: 0,
                    extraMax: 0.1,
                    renderer: am5xy.AxisRendererY.new(root, {})
                })
            );

            var yAxis2 = chart.yAxes.push(
                am5xy.ValueAxis.new(root, {
                    maxDeviation: 0.3,
                    syncWithAxis: yAxis,
                    renderer: am5xy.AxisRendererY.new(root, { opposite: true })
                })
            );

            var series1 = chart.series.push(
                am5xy.ColumnSeries.new(root, {
                    name: "2021",
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "tahun_lalu",
                    categoryXField: "marketing",
                    fill: am5.color("#4caf50"),
                    tooltip:am5.Tooltip.new(root, {
                        labelText:"[bold]{name} {categoryX}: Rp. {valueY}"
                    })
                })
            );

            series1.columns.template.setAll({
                tooltipY: am5.percent(10),
                templateField: "columnSettings"
            });

            series1.data.setAll(data);

            var series2 = chart.series.push(
                am5xy.ColumnSeries.new(root, {
                    name: "2022",
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "tahun_sekarang",
                    categoryXField: "marketing",
                    fill: am5.color("#ec407a"),
                    tooltip:am5.Tooltip.new(root, {
                        labelText:"[bold]{name} {categoryX}: Rp. {valueY}"
                    })
                })
            );

            series2.data.setAll(data);

            var series3 = chart.series.push(
                am5xy.LineSeries.new(root, {
                    name: "Growth",
                    xAxis: xAxis,
                    yAxis: yAxis2,
                    valueYField: "growth",
                    categoryXField: "marketing",
                    stroke: am5.color("#283593"),
                    fill: am5.color("#283593"),
                    tooltip:am5.Tooltip.new(root, {
                        pointerOrientation:"horizontal",
                        labelText:"[bold]{name} {categoryX}: {valueY}%"
                    })
                })
            );

            series3.data.setAll(data);

            series3.bullets.push(function () {
                return am5.Bullet.new(root, {
                    sprite: am5.Circle.new(root, {
                        strokeWidth: 3,
                        stroke: series3.get("stroke"),
                        radius: 5,
                        fill: root.interfaceColors.get("background")
                    })
                });
            });

            chart.set("cursor", am5xy.XYCursor.new(root, {}));

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    centerX: am5.p50,
                    x: am5.p50
                })
            );
            legend.data.setAll(chart.series.values);

            chart.appear(1000, 100);
            series1.appear();

        });
    });

</script>
@endpush
@endsection
