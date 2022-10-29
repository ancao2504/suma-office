var btnFilterProses = document.querySelector("#btnFilterProses");
btnFilterProses.addEventListener("click", function (e) {
    e.preventDefault();
    loading.block();
    document.getElementById("formFilter").submit();
});

$(function chartPerformaPenjualanGroupLevel() {
    am5.ready(function () {
        var root = am5.Root.new("chartPenjualanPerLevel");

        root.setThemes([
            am5themes_Animated.new(root)
        ]);

        var chart = root.container.children.push(am5radar.RadarChart.new(root, {
            innerRadius: am5.percent(20),
            startAngle: -90,
            endAngle: 180
        }));


        var data = [{
            category: "OLI",
            value: data_chart.detail_group_per_level.oli,
            full: 100,
            columnSettings: {
                fill: am5.color("#7239EA")
            }
        }, {
            category: "TUBE",
            value: data_chart.detail_group_per_level.tube,
            full: 100,
            columnSettings: {
                fill: am5.color("#F1416C")
            }
        }, {
            category: "NON-HANDLE",
            value: data_chart.detail_group_per_level.non_handle,
            full: 100,
            columnSettings: {
                fill: am5.color("#009EF7")
            }
        }, {
            category: "HANDLE",
            value: data_chart.detail_group_per_level.handle,
            full: 100,
            columnSettings: {
                fill: am5.color("#50CD89")
            }
        }];

        var cursor = chart.set("cursor", am5radar.RadarCursor.new(root, {
        }));

        cursor.lineY.set("visible", false);

        var xRenderer = am5radar.AxisRendererCircular.new(root, {
        });

        xRenderer.labels.template.setAll({
            radius: 10
        });

        xRenderer.grid.template.setAll({
            forceHidden: true
        });

        var xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {
            renderer: xRenderer,
            min: 0,
            max: 100,
            strictMinMax: true,
            numberFormat: "#'%'",
            tooltip: am5.Tooltip.new(root, {})
        }));


        var yRenderer = am5radar.AxisRendererRadial.new(root, {
            minGridDistance: 20
        });

        yRenderer.labels.template.setAll({
            centerX: am5.p100,
            fontWeight: "500",
            fontSize: 18,
            templateField: "columnSettings"
        });

        yRenderer.grid.template.setAll({
            forceHidden: true
        });

        var yAxis = chart.yAxes.push(am5xy.CategoryAxis.new(root, {
            categoryField: "category",
            renderer: yRenderer
        }));

        yAxis.data.setAll(data);

        var series1 = chart.series.push(am5radar.RadarColumnSeries.new(root, {
            xAxis: xAxis,
            yAxis: yAxis,
            clustered: false,
            valueXField: "full",
            categoryYField: "category",
            fill: root.interfaceColors.get("alternativeBackground")
        }));

        series1.columns.template.setAll({
            width: am5.p100,
            fillOpacity: 0.08,
            strokeOpacity: 0,
            cornerRadius: 20
        });

        series1.data.setAll(data);

        var series2 = chart.series.push(am5radar.RadarColumnSeries.new(root, {
            xAxis: xAxis,
            yAxis: yAxis,
            clustered: false,
            valueXField: "value",
            categoryYField: "category"
        }));

        series2.columns.template.setAll({
            width: am5.p100,
            strokeOpacity: 0,
            tooltipText: "[bold]{category} : {valueX}%",
            cornerRadius: 20,
            templateField: "columnSettings"
        });

        series2.data.setAll(data);

        series1.appear(1000);
        series2.appear(1000);
        chart.appear(1000, 100);

    });
});

$(function chartPerformaPenjualanHarian() {
    am5.ready(function () {
        var root = am5.Root.new("chartPerformaPenjualanHarian");

        root.setThemes([
            am5themes_Animated.new(root)
        ]);

        var chart = root.container.children.push(am5xy.XYChart.new(root, {
            panX: true,
            panY: true,
            wheelX: "panX",
            wheelY: "zoomX",
            layout: root.verticalLayout,
            pinchZoomX: true
        }));

        var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
            behavior: "none"
        }));
        cursor.lineY.set("visible", false);

        var data = data_chart.detail_daily;

        var xRenderer = am5xy.AxisRendererX.new(root, {});
        xRenderer.grid.template.set("location", 0.5);
        xRenderer.labels.template.setAll({
            location: 0.5,
            multiLocation: 0.5
        });

        var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
            categoryField: "day",
            renderer: xRenderer,
            tooltip: am5.Tooltip.new(root, {})
        }));

        xAxis.data.setAll(data);

        var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
            maxPrecision: 0,
            renderer: am5xy.AxisRendererY.new(root, {})
        }));

        var series = chart.series.push(am5xy.LineSeries.new(root, {
            xAxis: xAxis,
            yAxis: yAxis,
            valueYField: "value",
            categoryXField: "day",
            fill: am5.color("#3DC77D"),
            tooltip: am5.Tooltip.new(root, {
                labelText: "[bold]Tanggal {categoryX} : Rp. {valueY}",
                dy: -5
            })
        }));

        series.strokes.template.setAll({
            templateField: "strokeSettings",
            strokeWidth: 2
        });

        series.fills.template.setAll({
            visible: true,
            fillOpacity: 0.5,
            templateField: "fillSettings"
        });


        series.bullets.push(function () {
            return am5.Bullet.new(root, {
                sprite: am5.Circle.new(root, {
                    templateField: "bulletSettings",
                    radius: 5
                })
            });
        });

        series.data.setAll(data);
        series.appear(1000);

        chart.appear(1000, 100);

    });
});

window.onload = function () {
    var marketing = data_chart.jenis_mkr
    getJenisMkr(marketing);
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

$('#selectFilterJenisMkr').change(function () {
    var selectFilterJenisMkr = $('#selectFilterJenisMkr').val();
    getJenisMkr(selectFilterJenisMkr);
    $('#inputFilterKodeMkr').val('');
});

$(document).ready(function () {
    $('#btnFilter').on('click', function (e) {
        e.preventDefault();
        $('#modalFilter').modal('show');
    });

    $('#btnFilterReset').on('click', function (e) {
        e.preventDefault();
        var dateObj = new Date();
        var year = dateObj.getUTCFullYear();
        $('#inputFilterYear').val(year);
        $('#selectFilterMonth').prop('selectedIndex', data_chart.month - 1).change();
        $('#selectFilterJenisMkr').prop('selectedIndex', 0).change();
        $('#inputFilterKodeMkr').val('');
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
});