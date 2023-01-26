$(document).ready(function () {
    // ===============================================================
    // Load Data
    // ===============================================================
    function loadMasterData(year = '', month = '', level = '', produk = '', jenis_mkr = '', kode_mkr = '') {
        loading.block();
        window.location.href = window.location.origin + window.location.pathname + '?year=' + year.trim() + '&month=' + month.trim() +
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

        $('#selectFilterMonth').prop('selectedIndex', data_filter.month - 1).change();
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
        var month = $('#selectFilterMonth').val();
        var level_produk = $('#selectFilterLevelProduk').val();
        var kode_produk = $('#inputFilterKodeProduk').val();
        var jenis_mkr = $('#selectFilterJenisMkr').val();
        var kode_mkr = $('#inputFilterKodeMkr').val();

        loading.block();
        loadMasterData(year, month, level_produk, kode_produk, jenis_mkr, kode_mkr);
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

                    $('#selectFilterMonth').prop('selectedIndex', month - 1).change();
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
