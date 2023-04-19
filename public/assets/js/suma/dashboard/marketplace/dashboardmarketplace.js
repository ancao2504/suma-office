$(function chartSalesByPercentage() {
    am5.ready(function () {
        var root = am5.Root.new("chartSalesByPercentage");

        root.setThemes([
            am5themes_Animated.new(root)
        ]);

        var chart = root.container.children.push(am5percent.PieChart.new(root, {
            layout: root.verticalLayout
        }));

        var series = chart.series.push(am5percent.PieSeries.new(root, {
            valueField: "total",
            categoryField: "kode_lokasi"
        }));

        var bgColor = root.interfaceColors.get("background");

        series.slices.template.setAll({
            stroke: bgColor,
            strokeWidth: 2,
            tooltipText:
                "[bold]{category} : Rp. {value} (Selling Price)"
        });

        series.slices.template.states.create("hover", { scale: 0.95 });

        series.get("colors").set("colors", [
            am5.color("#F1416C"),
            am5.color("#7239EA"),
            am5.color("#50CD89"),
            am5.color("#009EF7"),
            am5.color("#FFC700"),
            am5.color("#181C32"),
        ]);

        var data = data_chart.sales_by_location;

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
});

$(function chartSalesByDate() {
    am5.ready(function () {
        var root = am5.Root.new("chartSalesByDate");

        root.setThemes([
            am5themes_Animated.new(root)
        ]);

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

        var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
            behavior: "none"
        }));
        cursor.lineY.set("visible", false);

        var data = data_chart.sales_by_date;

        var xRenderer = am5xy.AxisRendererX.new(root, {});
        xRenderer.grid.template.set("location", 0.5);
        xRenderer.labels.template.setAll({
            location: 0.5,
            multiLocation: 0.5
        });

        var xAxis = chart.xAxes.push(
            am5xy.CategoryAxis.new(root, {
                categoryField: "day",
                renderer: xRenderer,
                tooltip: am5.Tooltip.new(root, {})
            })
        );

        xAxis.data.setAll(data);

        var yAxis = chart.yAxes.push(
            am5xy.ValueAxis.new(root, {
                maxPrecision: 0,
                renderer: am5xy.AxisRendererY.new(root, {
                })
            })
        );

        function createSeries(name, field, color) {
            var series = chart.series.push(
                am5xy.LineSeries.new(root, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: field,
                    stroke: color,
                    fill: color,
                    categoryXField: "day",
                    tooltip: am5.Tooltip.new(root, {
                        pointerOrientation: "horizontal",
                        labelText: "[bold]{name}[/]\n[bold] Tanggal {categoryX} : Rp. {valueY}"
                    })
                })
            );


            series.bullets.push(function () {
                return am5.Bullet.new(root, {
                    sprite: am5.Circle.new(root, {
                        radius: 5,
                        fill: color
                    })
                });
            });

            series.set("setStateOnChildren", true);
            series.states.create("hover", {});

            series.mainContainer.set("setStateOnChildren", true);
            series.mainContainer.states.create("hover", {});

            series.strokes.template.states.create("hover", {
                strokeWidth: 4
            });

            series.data.setAll(data);
            series.appear(1000);
        }

        createSeries("OB", "amount_ob", am5.color("#F1416C"));
        createSeries("OK", "amount_ok", am5.color("#7239EA"));
        createSeries("OL", "amount_ol", am5.color("#50CD89"));
        createSeries("OP", "amount_op", am5.color("#009EF7"));
        createSeries("OS", "amount_os", am5.color("#FFC700"));
        createSeries("OT", "amount_ot", am5.color("#181C32"));

        var legend = chart.children.push(
            am5.Legend.new(root, {
                centerX: am5.p50,
                x: am5.p50
            })
        );

        legend.itemContainers.template.states.create("hover", {});

        legend.itemContainers.template.events.on("pointerover", function (e) {
            e.target.dataItem.dataContext.hover();
        });
        legend.itemContainers.template.events.on("pointerout", function (e) {
            e.target.dataItem.dataContext.unhover();
        });

        legend.data.setAll(chart.series.values);

        chart.appear(1000, 100);

    });
});

$(document).ready(function () {
    $('#btnSearch').on('click', function (e) {
        loading.block();
    });
});
