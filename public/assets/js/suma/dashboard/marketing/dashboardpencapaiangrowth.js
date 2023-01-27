$(document).ready(function () {
    // ===============================================================
    // Load Data
    // ===============================================================
    function loadMasterData(year = '', level = '', produk = '', jenis_mkr = '', kode_mkr = '') {
        loading.block();
        window.location.href = window.location.origin + window.location.pathname + '?year=' + year.trim() +
            '&level_produk=' + level.trim() + '&kode_produk=' + produk.trim() + '&jenis_mkr=' + jenis_mkr.trim() + '&kode_mkr=' + kode_mkr.trim();
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
        $('#selectFilterLevelProduk').val(data_filter.level_produk);
        $('#inputFilterKodeProduk').val(data_filter.kode_produk);
        $('#selectFilterJenisMkr').val(data_filter.jenis_mkr);
        $('#inputFilterKodeMkr').val(data_filter.kode_mkr);

        $('#modalFilter').modal('show');
    });

    $('#btnFilterProses').on('click', function (e) {
        e.preventDefault();

        var year = $('#inputFilterYear').val();
        var level_produk = $('#selectFilterLevelProduk').val();
        var kode_produk = $('#inputFilterKodeProduk').val();
        var jenis_mkr = $('#selectFilterJenisMkr').val();
        var kode_mkr = $('#inputFilterKodeMkr').val();

        loading.block();
        loadMasterData(year, level_produk, kode_produk, jenis_mkr, kode_mkr);
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
                    $('#selectFilterLevelProduk').prop('selectedIndex', 0).change();
                    $('#inputFilterKodeProduk').val('');
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
    // Filter Produk
    // ===============================================================
    $('#selectFilterLevelProduk').change(function () {
        $('#inputFilterKodeProduk').val('');
    });

    $('#inputFilterKodeProduk').on('click', function (e) {
        e.preventDefault();
        var selectFilterLevelProduk = $('#selectFilterLevelProduk').val();
        loadDataOptionProduk(1, 10, selectFilterLevelProduk, '');
        $('#formOptionGroupProduk').trigger('reset');
        $('#modalOptionGroupProduk').modal('show');
    });

    $('#btnFilterProduk').on('click', function (e) {
        e.preventDefault();
        var selectFilterLevelProduk = $('#selectFilterLevelProduk').val();
        loadDataOptionProduk(1, 10, selectFilterLevelProduk, '');
        $('#formOptionGroupProduk').trigger('reset');
        $('#modalOptionGroupProduk').modal('show');
    });

    $('body').on('click', '#optionProdukContentModal #selectedOptionProduk', function (e) {
        e.preventDefault();
        $('#inputFilterKodeProduk').val($(this).data('kode_produk'));
        $('#modalOptionGroupProduk').modal('hide');
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
