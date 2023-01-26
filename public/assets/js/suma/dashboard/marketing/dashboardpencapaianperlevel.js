$(document).ready(function () {
    // ===============================================================
    // Load Data
    // ===============================================================
    function loadMasterData(year = '', jenis_mkr = '', kode_mkr = '') {
        loading.block();
        window.location.href = window.location.origin + window.location.pathname + '?year=' + year.trim() + '&jenis_mkr=' + jenis_mkr.trim() + '&kode_mkr=' + kode_mkr.trim();
    }

    function getJenisMkr(selectFilterJenisMkr = '') {
        if (selectFilterJenisMkr == 'SUPERVISOR') {
            $("#labelKodeMkr").html("Supervisor");
            $("#btnFilterMarketing").prop("disabled", false);
        } else if (selectFilterJenisMkr == 'SALESMAN') {
            $("#labelKodeMkr").html("Salesman");
            $("#btnFilterMarketing").prop("disabled", false);
        } else {
            $("#labelKodeMkr").html("Marketing");
            $('#inputFilterKodeMkr').val('');
            $("#btnFilterMarketing").prop("disabled", true);
        }
    }

    // ===============================================================
    // Filter
    // ===============================================================
    $('#btnFilterMasterData').on('click', function (e) {
        e.preventDefault();

        $('#inputFilterYear').val(data_filter.year);
        $('#selectFilterJenisMkr').val(data_filter.jenis_mkr);
        $('#inputFilterKodeMkr').val(data_filter.kode_mkr);

        $('#modalFilter').modal('show');
    });

    $('#btnFilterProses').on('click', function (e) {
        e.preventDefault();

        var year = $('#inputFilterYear').val();
        var jenis_mkr = $('#selectFilterJenisMkr').val();
        var kode_mkr = $('#inputFilterKodeMkr').val();

        loading.block();
        loadMasterData(year, jenis_mkr, kode_mkr);
    });

    $('#btnFilterReset').on('click', function (e) {
        e.preventDefault();
        var dateObj = new Date();
        var year = dateObj.getUTCFullYear();

        loading.block();
        $.ajax({
            url: url.clossing_marketing,
            method: "get",
            success: function(response) {
                loading.release();
                if (response.status == false) {
                    Swal.fire({
                        text: response.message,
                        icon: "warning",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-warning"
                        }
                    });
                } else {
                    month = response.data.bulan_aktif;
                    year = response.data.tahun_aktif;

                    $('#inputFilterYear').val(year);
                    $('#selectFilterJenisMkr').prop('selectedIndex', 0).change();
                    $('#inputFilterKodeMkr').val('');
                }
            },
            error: function() {
                loading.release();
                Swal.fire({
                    text: 'Server tidak merespon, coba lagi',
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-danger"
                    }
                });
            }
        });
    });

    // ===============================================================
    // Filter MKR
    // ===============================================================
    $('#btnFilterMarketing').on('click', function (e) {
        e.preventDefault();
        var jenis_mkr = document.getElementById("selectFilterJenisMkr").value;

        if (jenis_mkr == "SALESMAN") {
            loadDataOptionSalesman();
            $('#formOptionSalesman').trigger('reset');
            $('#modalOptionSalesman').modal('show');
        } else if (jenis_mkr == "SUPERVISOR") {
            loadDataOptionSupervisor();
            $('#formOptionSupervisor').trigger('reset');
            $('#modalOptionSupervisor').modal('show');
        }
    });

    $('#inputFilterKodeMkr').on('click', function (e) {
        e.preventDefault();
        var jenis_mkr = document.getElementById("selectFilterJenisMkr").value;

        if (jenis_mkr == "SALESMAN") {
            loadDataOptionSalesman();
            $('#formOptionSalesman').trigger('reset');
            $('#modalOptionSalesman').modal('show');
        } else if (jenis_mkr == "SUPERVISOR") {
            loadDataOptionSupervisor();
            $('#formOptionSupervisor').trigger('reset');
            $('#modalOptionSupervisor').modal('show');
        }
    });

    $('body').on('click', '#optionSalesmanContentModal #selectedOptionSalesman', function (e) {
        e.preventDefault();
        $('#inputFilterKodeMkr').val($(this).data('kode_sales'));
        $('#modalOptionSalesman').modal('hide');
    });

    $('body').on('click', '#optionSupervisorContentModal #selectedOptionSupervisor', function (e) {
        e.preventDefault();
        $('#inputFilterKodeMkr').val($(this).data('kode_spv'));
        $('#modalOptionSupervisor').modal('hide');
    });

    $('#selectFilterJenisMkr').change(function () {
        var selectFilterJenisMkr = $('#selectFilterJenisMkr').val();
        getJenisMkr(selectFilterJenisMkr);
        $('#inputFilterKodeMkr').val('');
    });
});

$(function chartTotal() {
    am5.ready(function () {
        var root = am5.Root.new("chartTotal");

        root.setThemes([am5themes_Animated.new(root)]);

        var chart = root.container.children.push(
            am5xy.XYChart.new(root, {
                panX: true,
                panY: true,
                wheelX: "panX",
                wheelY: "zoomX",
                layout: root.verticalLayout,
                pinchZoomX: true
            })
        );

        var data = data_chart.total;

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
                tooltip: am5.Tooltip.new(root, {
                    labelText: "[bold]{name} {categoryX}: Rp. {valueY}"
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
                tooltip: am5.Tooltip.new(root, {
                    labelText: "[bold]{name} {categoryX}: Rp. {valueY}"
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
                tooltip: am5.Tooltip.new(root, {
                    pointerOrientation: "horizontal",
                    labelText: "[bold]{name} {categoryX}: {valueY}%"
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
    am5.ready(function () {
        var root = am5.Root.new("chartHandle");

        root.setThemes([am5themes_Animated.new(root)]);

        var chart = root.container.children.push(
            am5xy.XYChart.new(root, {
                panX: true,
                panY: true,
                wheelX: "panX",
                wheelY: "zoomX",
                layout: root.verticalLayout,
                pinchZoomX: true
            })
        );

        var data = data_chart.handle;

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
                tooltip: am5.Tooltip.new(root, {
                    labelText: "[bold]{name} {categoryX}: Rp. {valueY}"
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
                tooltip: am5.Tooltip.new(root, {
                    labelText: "[bold]{name} {categoryX}: Rp. {valueY}"
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
                tooltip: am5.Tooltip.new(root, {
                    pointerOrientation: "horizontal",
                    labelText: "[bold]{name} {categoryX}: {valueY}%"
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
    am5.ready(function () {
        var root = am5.Root.new("chartNonHandle");

        root.setThemes([am5themes_Animated.new(root)]);

        var chart = root.container.children.push(
            am5xy.XYChart.new(root, {
                panX: true,
                panY: true,
                wheelX: "panX",
                wheelY: "zoomX",
                layout: root.verticalLayout,
                pinchZoomX: true
            })
        );

        var data = data_chart.non_handle;

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
                tooltip: am5.Tooltip.new(root, {
                    labelText: "[bold]{name} {categoryX}: Rp. {valueY}"
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
                tooltip: am5.Tooltip.new(root, {
                    labelText: "[bold]{name} {categoryX}: Rp. {valueY}"
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
                tooltip: am5.Tooltip.new(root, {
                    pointerOrientation: "horizontal",
                    labelText: "[bold]{name} {categoryX}: {valueY}%"
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
    am5.ready(function () {
        var root = am5.Root.new("chartTube");

        root.setThemes([am5themes_Animated.new(root)]);

        var chart = root.container.children.push(
            am5xy.XYChart.new(root, {
                panX: true,
                panY: true,
                wheelX: "panX",
                wheelY: "zoomX",
                layout: root.verticalLayout,
                pinchZoomX: true
            })
        );

        var data = data_chart.tube;

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
                tooltip: am5.Tooltip.new(root, {
                    labelText: "[bold]{name} {categoryX}: Rp. {valueY}"
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
                tooltip: am5.Tooltip.new(root, {
                    labelText: "[bold]{name} {categoryX}: Rp. {valueY}"
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
                tooltip: am5.Tooltip.new(root, {
                    pointerOrientation: "horizontal",
                    labelText: "[bold]{name} {categoryX}: {valueY}%"
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
    am5.ready(function () {
        var root = am5.Root.new("chartOli");

        root.setThemes([am5themes_Animated.new(root)]);

        var chart = root.container.children.push(
            am5xy.XYChart.new(root, {
                panX: true,
                panY: true,
                wheelX: "panX",
                wheelY: "zoomX",
                layout: root.verticalLayout,
                pinchZoomX: true
            })
        );

        var data = data_chart.oli;

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
                tooltip: am5.Tooltip.new(root, {
                    labelText: "[bold]{name} {categoryX}: Rp. {valueY}"
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
                tooltip: am5.Tooltip.new(root, {
                    labelText: "[bold]{name} {categoryX}: Rp. {valueY}"
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
                tooltip: am5.Tooltip.new(root, {
                    pointerOrientation: "horizontal",
                    labelText: "[bold]{name} {categoryX}: {valueY}%"
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
