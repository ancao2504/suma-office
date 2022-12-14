window.onload = function () {
    var marketing = data.jenis_mkr;

    getJenisMkr(marketing);
    $('#inputFilterKodeMkr').val(data.kode_mkr);
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

    $('#inputFilterKodeProduk').on('click', function (e) {
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

    $('#inputFilterKodeMkr').on('click', function (e) {
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

$(function chartPencapaianPerProduk() {
    am5.ready(function () {
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

        var data = data_chart.product;

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

            series.bullets.push(function () {
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

            series.bullets.push(function () {
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
            series.events.on("datavalidated", function (ev) {
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
