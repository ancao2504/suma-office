@extends('layouts.main.index')
@section('title','Home')
@section('subtitle','Dashboard')
@section('container')
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Management Stock</span>
                <span class="text-muted fw-bold fs-7">Dashboard Management Stock
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
                    </h3>
                </div>

                <div class="card-body pt-8">
                    @if(isset($produk))
                    <div id="chartStockByProduct" style="height: 500px; width: 100%;"></div>
                    @else
                    <div id="chartStockByProduct" style="height: 5500px; width: 100%;"></div>
                    @endif
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
                                <input id="inputFilterKodeProduk" name="produk" type="search" class="form-control" placeholder="Semua Produk" readonly
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
            var btnFilterProses = document.querySelector("#btnFilterProses");
            btnFilterProses.addEventListener("click", function(e) {
                e.preventDefault();
                blockIndex.block();
                document.getElementById("formFilter").submit();
            });

            $(document).ready(function() {
                $('#btnFilterReset').on('click', function(e) {
                    e.preventDefault();
                    var dateObj = new Date();
                    var year = dateObj.getUTCFullYear();
                    $('#inputFilterYear').val(year);
                    $('#selectFilterMonth').prop('selectedIndex', {{ $month }} - 1).change();
                    $('#selectFilterLevelProduk').prop('selectedIndex', 0).change();
                    $('#inputFilterKodeProduk').val('');
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
            });

            $(function chartStockAll() {
                {
                    am5.ready(function() {
                        var root = am5.Root.new("chartStockAll");

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
                                "{category}: [bold]Rp. {value} (Amount)"
                        });
                        @endif

                        series.slices.template.states.create("hover", { scale: 0.95 });

                        series.get("colors").set("colors", [
                            am5.color("#009EF7"),
                            am5.color("#F1416C"),
                            am5.color("#7239EA")
                        ]);

                        var data = {!!json_encode($total)!!};
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
                }
            });

            $(function chartPembelian() {
                am5.ready(function() {
                    var root = am5.Root.new("chartPembelian");

                    root.setThemes([
                        am5themes_Animated.new(root)
                    ]);

                    var chart = root.container.children.push(am5xy.XYChart.new(root, {
                        layout: root.verticalLayout
                    }));

                    var legend = chart.children.push(
                        am5.Legend.new(root, {
                            centerX: am5.p50,
                            x: am5.p50
                        })
                    );

                    var data = {!!json_encode($pembelian)!!};

                    var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
                        categoryField: "keterangan",
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

                    function makeSeries(name, fieldName, color) {
                        @if($fields == 'QUANTITY')
                        var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                            name: name,
                            xAxis: xAxis,
                            yAxis: yAxis,
                            valueYField: fieldName,
                            fill: color,
                            categoryXField: "keterangan",
                            width: am5.percent(90),
                            tooltip: am5.Tooltip.new(root, {
                                pointerOrientation: "horizontal",
                                labelText: "[bold]{name} : [bold]{valueY} PCS"
                            })
                        }));
                        @else
                        var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                            name: name,
                            xAxis: xAxis,
                            yAxis: yAxis,
                            valueYField: fieldName,
                            fill: color,
                            categoryXField: "keterangan",
                            width: am5.percent(90),
                            tooltip: am5.Tooltip.new(root, {
                                pointerOrientation: "horizontal",
                                labelText: "[bold]{name} : [bold]Rp. {valueY}"
                            })
                        }));
                        @endif

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

                    makeSeries("Packing", "packing", am5.color("#009EF7"));
                    makeSeries("On Order", "on_order", am5.color("#F1416C"));

                    var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                    }));
                    cursor.lineY.set("forceHidden", true);
                    cursor.lineX.set("forceHidden", true);

                    chart.appear(1000, 100);
                });
            });

            $(function chartFS() {
                am5.ready(function() {
                    var root = am5.Root.new("chartFS");

                    root.setThemes([
                        am5themes_Animated.new(root)
                    ]);

                    var chart = root.container.children.push(am5xy.XYChart.new(root, {
                        layout: root.verticalLayout
                    }));

                    var legend = chart.children.push(
                        am5.Legend.new(root, {
                            centerX: am5.p50,
                            x: am5.p50
                        })
                    );

                    var data = {!!json_encode($fs)!!};

                    var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
                        categoryField: "fs",
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

                    function makeSeries(name, fieldName, color) {
                        @if($fields == 'QUANTITY')
                        var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                            name: name,
                            xAxis: xAxis,
                            yAxis: yAxis,
                            valueYField: fieldName,
                            fill: color,
                            categoryXField: "fs",
                            width: am5.percent(90),
                            tooltip: am5.Tooltip.new(root, {
                                pointerOrientation: "horizontal",
                                labelText: "[bold]{name} : [bold]{valueY} PCS"
                            })
                        }));
                        @else
                        var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                            name: name,
                            xAxis: xAxis,
                            yAxis: yAxis,
                            valueYField: fieldName,
                            fill: color,
                            categoryXField: "fs",
                            width: am5.percent(90),
                            tooltip: am5.Tooltip.new(root, {
                                pointerOrientation: "horizontal",
                                labelText: "[bold]{name} : [bold]Rp. {valueY}"
                            })
                        }));
                        @endif

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

                    makeSeries("Pusat", "pusat", am5.color("#009EF7"));
                    makeSeries("Part Center", "pc", am5.color("#F1416C"));
                    makeSeries("Online", "online", am5.color("#7239EA"));

                    var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                    }));
                    cursor.lineY.set("forceHidden", true);
                    cursor.lineX.set("forceHidden", true);

                    chart.appear(1000, 100);
                });
            });

            $(function chartCNO() {
                am5.ready(function() {

                    var root = am5.Root.new("chartCNO");

                    root.setThemes([
                        am5themes_Animated.new(root)
                    ]);

                    var chart = root.container.children.push(am5xy.XYChart.new(root, {
                        layout: root.verticalLayout
                    }));

                    var legend = chart.children.push(
                        am5.Legend.new(root, {
                            centerX: am5.p50,
                            x: am5.p50
                        })
                    );

                    var data = {!!json_encode($cno)!!};

                    var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
                        categoryField: "cno",
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

                    function makeSeries(name, fieldName, color) {
                        @if($fields == 'QUANTITY')
                        var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                            name: name,
                            xAxis: xAxis,
                            yAxis: yAxis,
                            valueYField: fieldName,
                            fill: color,
                            categoryXField: "cno",
                            width: am5.percent(90),
                            tooltip: am5.Tooltip.new(root, {
                                pointerOrientation: "horizontal",
                                labelText: "[bold]{name} : [bold]{valueY} PCS"
                            })
                        }));
                        @else
                        var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                            name: name,
                            xAxis: xAxis,
                            yAxis: yAxis,
                            valueYField: fieldName,
                            fill: color,
                            categoryXField: "cno",
                            width: am5.percent(90),
                            tooltip: am5.Tooltip.new(root, {
                                pointerOrientation: "horizontal",
                                labelText: "[bold]{name} : [bold]Rp. {valueY}"
                            })
                        }));
                        @endif

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

                    makeSeries("Pusat", "pusat", am5.color("#009EF7"));
                    makeSeries("Part Center", "pc", am5.color("#F1416C"));
                    makeSeries("Online", "online", am5.color("#7239EA"));

                    var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                    }));
                    cursor.lineY.set("forceHidden", true);
                    cursor.lineX.set("forceHidden", true);

                    chart.appear(1000, 100);
                });
            });

            $(function chartStockByProduct() {
                am5.ready(function() {
                    var root = am5.Root.new("chartStockByProduct");

                    root.setThemes([
                        am5themes_Animated.new(root)
                    ]);

                    var chart = root.container.children.push(am5xy.XYChart.new(root, {
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
