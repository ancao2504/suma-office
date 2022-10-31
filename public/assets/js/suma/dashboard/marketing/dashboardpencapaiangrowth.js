window.onload = function () {
    var marketing = '{{$jenis_mkr}}';

    getJenisMkr(marketing);
}

var btnFilterProses = document.querySelector("#btnFilterProses");
btnFilterProses.addEventListener("click", function (e) {
    e.preventDefault();
    loading.block();
    document.getElementById("formFilter").submit();
});

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

$(document).ready(function () {
    $('#btnFilter').on('click', function (e) {
        e.preventDefault();
        $('#modalFilter').modal('show');
    });

    $('#btnFilterReset').on('click', function (e) {
        e.preventDefault();
        var dateObj = new Date();
        var year = dateObj.getUTCFullYear();
        $('#inputYear').val(year);
        $('#selectFilterLevelProduk').prop('selectedIndex', 0).change();
        $('#inputFilterKodeProduk').val('');
        $('#selectFilterJenisMkr').prop('selectedIndex', 0).change();
        $('#inputFilterKodeMkr').val('');
    });

    $('#btnFilterProduk').on('click', function (e) {
        e.preventDefault();

        var selectFilterLevelProduk = $('#selectFilterLevelProduk').val();
        loadDataProduk(1, 10, '', selectFilterLevelProduk);
        $('#searchProdukForm').trigger('reset');
        $('#produkSearchModal').modal('show');
    });

    $('body').on('click', '#produkContentModal #selectProduk', function (e) {
        e.preventDefault();
        $('#inputFilterKodeProduk').val($(this).data('kode_produk'));
        $('#produkSearchModal').modal('hide');
    });

    $('#selectFilterLevelProduk').change(function () {
        $('#inputFilterKodeProduk').val('');
    });

    $('#btnFilterMarketing').on('click', function (e) {
        e.preventDefault();
        var jenis_mkr = document.getElementById("selectFilterJenisMkr").value;

        if (jenis_mkr == "SALESMAN") {
            loadDataSalesman();
            $('#searchSalesmanForm').trigger('reset');
            $('#salesmanSearchModal').modal('show');
        } else if (jenis_mkr == "SUPERVISOR") {
            loadDataSupervisor();
            $('#searchSupervisorForm').trigger('reset');
            $('#supervisorSearchModal').modal('show');
        }
    });

    $('body').on('click', '#salesmanContentModal #selectSalesman', function (e) {
        e.preventDefault();
        $('#inputFilterKodeMkr').val($(this).data('kode_sales'));
        $('#salesmanSearchModal').modal('hide');
    });

    $('body').on('click', '#supervisorContentModal #selectSupervisor', function (e) {
        e.preventDefault();
        $('#inputFilterKodeMkr').val($(this).data('kode_spv'));
        $('#supervisorSearchModal').modal('hide');
    });

    $('#selectFilterJenisMkr').change(function () {
        var selectFilterJenisMkr = $('#selectFilterJenisMkr').val();
        getJenisMkr(selectFilterJenisMkr);
        $('#inputFilterKodeMkr').val('');
    });
});

$(function chartGrandTotal() {
    am5.ready(function () {
        var root = am5.Root.new("chartGrandTotal");

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
                name: "2021",
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "tahun_lalu",
                categoryXField: "bulan",
                fill: am5.color("#2196f3"),
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
                name: "2022",
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "tahun_sekarang",
                categoryXField: "bulan",
                fill: am5.color("#ec407a"),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "[bold]{name} {categoryX}: Rp. {valueY}"
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

$(function chartTotalMarketing() {
    am5.ready(function () {
        var root = am5.Root.new("chartTotalMarketing");

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

        var data = data_chart.marketing;

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
                name: "2022",
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "tahun_sekarang",
                categoryXField: "marketing",
                fill: am5.color("#ec407a"),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "[bold]{name} {categoryX}: Rp. {valueY}"
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
