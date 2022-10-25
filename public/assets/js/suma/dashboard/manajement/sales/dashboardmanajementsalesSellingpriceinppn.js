$(function chartGrossProfit() {
    am5.ready(function () {
        var root = am5.Root.new("chartGrossProfit");

        root.setThemes([
            am5themes_Animated.new(root)
        ]);

        var chart = root.container.children.push(am5percent.PieChart.new(root, {
            layout: root.verticalLayout,
            innerRadius: am5.percent(40)
        }));

        var series0 = chart.series.push(am5percent.PieSeries.new(root, {
            valueField: "margin_prosentase",
            categoryField: "company",
            alignLabels: true,
        }));

        var bgColor = root.interfaceColors.get("background");

        series0.ticks.template.setAll({ forceHidden: true });
        series0.labels.template.setAll({ forceHidden: true });
        series0.slices.template.setAll({
            stroke: bgColor,
            strokeWidth: 2,
            tooltipText:
                "{category}: [bold]{value.formatNumber('0.00')}% (Prosentase Margin)"
        });
        series0.slices.template.states.create("hover", { scale: 0.95 });

        var series1 = chart.series.push(am5percent.PieSeries.new(root, {
            valueField: "margin",
            categoryField: "company",
            alignLabels: true
        }));

        series1.get("colors").set("colors", [
            am5.color("#009EF7"),
            am5.color("#F1416C"),
            am5.color("#7239EA")
        ]);

        series1.slices.template.setAll({
            stroke: bgColor,
            strokeWidth: 2,
            tooltipText:
                "{category}: [bold]Rp. {value} (Amount margin)"
        });


        var data = data_nonqty.gross_profit;

        series0.data.setAll(data);
        series1.data.setAll(data);

        var legend = chart.children.push(am5.Legend.new(root, {
            centerX: am5.percent(50),
            x: am5.percent(50),
            marginTop: 15,
            marginBottom: 15,
        }));
        legend.data.setAll(series0.dataItems);

        series0.appear(1000, 100);
        series1.appear(1000, 100);

    });
});

$(function chartSalesAll() {
    am5.ready(function () {
        var root = am5.Root.new("chartSalesAll");

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

        series.slices.template.setAll({
            stroke: bgColor,
            strokeWidth: 2,
            tooltipText:
                "{category}: [bold]Rp. {value} (Selling Price)"
        });

        series.slices.template.states.create("hover", { scale: 0.95 });

        series.get("colors").set("colors", [
            am5.color("#009EF7"),
            am5.color("#F1416C"),
            am5.color("#7239EA")
        ]);

        var data = data_nonqty.sales_all;
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

        var data = data_nonqty.by_date;

        var xRenderer = am5xy.AxisRendererX.new(root, {});
        xRenderer.grid.template.set("location", 0.5);
        xRenderer.labels.template.setAll({
            location: 0.5,
            multiLocation: 0.5
        });

        var xAxis = chart.xAxes.push(
            am5xy.CategoryAxis.new(root, {
                categoryField: "tanggal",
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
                    categoryXField: "tanggal",
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

        createSeries("Pusat", "pusat", am5.color("#009EF7"));
        createSeries("Part Center", "pc", am5.color("#F1416C"));
        createSeries("Online", "online", am5.color("#7239EA"));

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

$(function chartSalesByProduct() {
    am5.ready(function () {
        var root = am5.Root.new("chartSalesByProduct");

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

        var data = data_nonqty.by_product;

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