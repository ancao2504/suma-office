@extends('layouts.main.index')
@section('title','Home')
@section('subtitle','Dashboard')
@section('container')
<form id="formDashboardManagementSales">
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Dashboard Marketing</span>
                <span class="text-muted fw-bolder fs-7">Pencapaian Per-Level {{ $year }}
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
                <span class="fw-boldest mb-2 text-dark">Level Total</span>
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div id="chartTotal" style="height: 500px; width: 100%;"></div>
            </div>
        </div>
    </div>
    <div class="card card-flush mt-4">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-boldest mb-2 text-dark">Level Handle</span>
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div id="chartHandle" style="height: 500px; width: 100%;"></div>
            </div>
        </div>
    </div>
    <div class="card card-flush mt-4">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Level Non-Handle</span>
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div id="chartNonHandle" style="height: 500px; width: 100%;"></div>
            </div>
        </div>
    </div>
    <div class="card card-flush mt-4">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Level Tube</span>
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div id="chartTube" style="height: 500px; width: 100%;"></div>
            </div>
        </div>
    </div>
    <div class="card card-flush mt-4">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Level Oli</span>
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div id="chartOli" style="height: 500px; width: 100%;"></div>
            </div>
        </div>
    </div>
</form>

<div class="modal fade" tabindex="-2" id="modalFilter">
    <div class="modal-dialog">
        <div class="modal-content" id="modalFilterContent">
            <form id="formFilter" name="formFilter" autofill="off" autocomplete="off" method="get" action="{{ route('dashboard.dashboard-marketing-pencapaian-perlevel') }}">
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

@push('scripts')
<script src="{{ asset('assets/js/suma/option/option.js') }}"></script>
<script src="{{ asset('assets/media/charts/amcharts/index.js') }}"></script>
<script src="{{ asset('assets/media/charts/amcharts/xy.js') }}"></script>
<script src="{{ asset('assets/media/charts/amcharts/Animated.js') }}"></script>

<script type="text/javascript">
    window.onload = function() {
        var marketing = '';

        @if($jenis_mkr != '')
            marketing = "{{$jenis_mkr}}";
        @endif

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
            $('#selectFilterJenisMkr').prop('selectedIndex', 0).change();
            $('#inputFilterKodeMkr').val('');
        });

        $('#selectFilterJenisMkr').change(function(){
            var selectFilterJenisMkr = $('#selectFilterJenisMkr').val();
            getJenisMkr(selectFilterJenisMkr);
            $('#inputFilterKodeMkr').val('');
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

    $(function chartTotal() {
        am5.ready(function() {
            var root = am5.Root.new("chartTotal");

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

            var data = {!!json_encode($total)!!};;

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
                    name: "Pencapaian",
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "pencapaian",
                    categoryXField: "bulan",
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
                    name: "Target",
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "target",
                    categoryXField: "bulan",
                    fill: am5.color("#e91e63"),
                    tooltip:am5.Tooltip.new(root, {
                        labelText:"[bold]{name} {categoryX}: Rp. {valueY}"
                    })
                })
            );

            series2.data.setAll(data);

            var series3 = chart.series.push(
                am5xy.LineSeries.new(root, {
                    name: "Prosentase",
                    xAxis: xAxis,
                    yAxis: yAxis2,
                    valueYField: "prosentase",
                    categoryXField: "bulan",
                    stroke: am5.color("#0a00b6"),
                    fill: am5.color("#0a00b6"),
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

    $(function chartHandle() {
        am5.ready(function() {
            var root = am5.Root.new("chartHandle");

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

            var data = {!!json_encode($handle)!!};;

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
                    name: "Pencapaian",
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "pencapaian",
                    categoryXField: "bulan",
                    fill: am5.color("#f48fb1"),
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
                    name: "Target",
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "target",
                    categoryXField: "bulan",
                    fill: am5.color("#d81b60"),
                    tooltip:am5.Tooltip.new(root, {
                        labelText:"[bold]{name} {categoryX}: Rp. {valueY}"
                    })
                })
            );

            series2.data.setAll(data);

            var series3 = chart.series.push(
                am5xy.LineSeries.new(root, {
                    name: "Prosentase",
                    xAxis: xAxis,
                    yAxis: yAxis2,
                    valueYField: "prosentase",
                    categoryXField: "bulan",
                    stroke: am5.color("#0a00b6"),
                    fill: am5.color("#0a00b6"),
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

    $(function chartNonHandle() {
        am5.ready(function() {
            var root = am5.Root.new("chartNonHandle");

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

            var data = {!!json_encode($non_handle)!!};;

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
                    name: "Pencapaian",
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "pencapaian",
                    categoryXField: "bulan",
                    fill: am5.color("#90caf9"),
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
                    name: "Target",
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "target",
                    categoryXField: "bulan",
                    fill: am5.color("#1e88e5"),
                    tooltip:am5.Tooltip.new(root, {
                        labelText:"[bold]{name} {categoryX}: Rp. {valueY}"
                    })
                })
            );

            series2.data.setAll(data);

            var series3 = chart.series.push(
                am5xy.LineSeries.new(root, {
                    name: "Prosentase",
                    xAxis: xAxis,
                    yAxis: yAxis2,
                    valueYField: "prosentase",
                    categoryXField: "bulan",
                    stroke: am5.color("#0a00b6"),
                    fill: am5.color("#0a00b6"),
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

    $(function chartTube() {
        am5.ready(function() {
            var root = am5.Root.new("chartTube");

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

            var data = {!!json_encode($tube)!!};;

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
                    name: "Pencapaian",
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "pencapaian",
                    categoryXField: "bulan",
                    fill: am5.color("#a5d6a7"),
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
                    name: "Target",
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "target",
                    categoryXField: "bulan",
                    fill: am5.color("#43a047"),
                    tooltip:am5.Tooltip.new(root, {
                        labelText:"[bold]{name} {categoryX}: Rp. {valueY}"
                    })
                })
            );

            series2.data.setAll(data);

            var series3 = chart.series.push(
                am5xy.LineSeries.new(root, {
                    name: "Prosentase",
                    xAxis: xAxis,
                    yAxis: yAxis2,
                    valueYField: "prosentase",
                    categoryXField: "bulan",
                    stroke: am5.color("#0a00b6"),
                    fill: am5.color("#0a00b6"),
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

    $(function chartOli() {
        am5.ready(function() {
            var root = am5.Root.new("chartOli");

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

            var data = {!!json_encode($oli)!!};;

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
                    name: "Pencapaian",
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "pencapaian",
                    categoryXField: "bulan",
                    fill: am5.color("#ffcc80"),
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
                    name: "Target",
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "target",
                    categoryXField: "bulan",
                    fill: am5.color("#fb8c00"),
                    tooltip:am5.Tooltip.new(root, {
                        labelText:"[bold]{name} {categoryX}: Rp. {valueY}"
                    })
                })
            );

            series2.data.setAll(data);

            var series3 = chart.series.push(
                am5xy.LineSeries.new(root, {
                    name: "Prosentase",
                    xAxis: xAxis,
                    yAxis: yAxis2,
                    valueYField: "prosentase",
                    categoryXField: "bulan",
                    stroke: am5.color("#0a00b6"),
                    fill: am5.color("#0a00b6"),
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
