@extends('layouts.main.index')
@section('title','Home')
@section('subtitle','Dashboard')
@section('container')
    <form id="formDashboardManagementStock" action="{{ route('dashboard.dashboard-management-stock') }}" method="get">
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
                    <button class="btn btn-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="bi bi-funnel-fill fs-4 me-2"></i>Filter
                    </button>

                    <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_6244763d95a3a" style="">
                        <div class="px-7 py-5">
                            <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                        </div>
                        <div class="separator border-gray-200"></div>
                        <div class="px-7 py-5">
                            <div class="mb-5">
                                <label class="form-label required">Bulan:</label>
                                <select id="selectMonth" name="month" class="form-select" data-control="select2" data-hide-search="true">
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
                            <div class="mb-5">
                                <label class="form-label required">Tahun:</label>
                                <input type="text" id="inputYear" name="year" class="form-control" placeholder="Tahun" autocomplete="off"
                                    @if(isset($year)) value="{{ $year }}" @else value="{{ old('year') }}"@endif>
                            </div>
                            <div class="mb-5">
                                <label class="form-label required">Data yang ditampilkan:</label>
                                <select id="selectFields" name="fields" class="form-select" data-control="select2" data-hide-search="true">
                                    <option value="QUANTITY" @if($fields == 'QUANTITY') {{"selected"}} @endif>Quantity</option>
                                    <option value="AMOUNT" @if($fields == 'AMOUNT') {{"selected"}} @endif>Amount</option>
                                </select>
                            </div>
                            <div class="mb-5">
                                <label class="form-label">Produk:</label>
                                <input type="text" id="inputProduk" name="produk" class="form-control" placeholder="Kode Produk" autocomplete="off"
                                    @if(isset($produk)) value="{{ $produk }}" @else value="{{ old('produk') }}"@endif>
                            </div>
                            <div class="mb-5">
                                <div class="d-flex align-items-center">
                                    <button id="btnFilterProses" class="btn btn-sm btn-primary me-2" type="submit">Terapkan</button>
                                    <a id="btnFilterReset" href="{{ route('dashboard.dashboard-dealer') }}" class="btn btn-sm btn-danger me-2" role="button">Reset Filter</a>
                                </div>
                            </div>
                        </div>
                    </div>
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
                        <div id="chartStockByProduct" style="height: 300px; width: 100%;"></div>
                        @else
                        <div id="chartStockByProduct" style="height: 5500px; width: 100%;"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
    @push('scripts')
        <script src="{{ asset('assets/media/charts/amcharts/index.js') }}"></script>
        <script src="{{ asset('assets/media/charts/amcharts/percent.js') }}"></script>
        <script src="{{ asset('assets/media/charts/amcharts/xy.js') }}"></script>
        <script src="{{ asset('assets/media/charts/amcharts/Animated.js') }}"></script>
        <script src="{{ asset('assets/media/charts/amcharts/Micro.js') }}"></script>

        <script type="text/javascript">
            var btnFilterProses = document.querySelector("#btnFilterProses");
            var btnFilterReset = document.querySelector("#btnFilterReset");
            var targetBlockDashboardManagementStock = document.querySelector("#formDashboardManagementStock");

            var blockDashboardManagementStock = new KTBlockUI(targetBlockDashboardManagementStock, {
                message: '<div class="blockui-message" style="position: fixed;top: 50%;left: 50%;transform: translate(-50%, -50%);">'+
                            '<span class="spinner-border text-primary"></span> Loading...'+
                        '</div>'
            });
            btnFilterProses.addEventListener("click", function(e) {
                e.preventDefault();
                blockDashboardManagementStock.block();
                document.getElementById("formDashboardManagementStock").submit();
            });
            btnFilterReset.addEventListener("click", function(e) {
                e.preventDefault();
                blockDashboardManagementStock.block();
                document.getElementById("formDashboardManagementStock").submit();
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
