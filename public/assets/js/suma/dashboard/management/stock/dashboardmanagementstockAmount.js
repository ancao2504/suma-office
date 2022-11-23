
$(function chartStockAll() {
    {
        am5.ready(function () {
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

            series.slices.template.setAll({
                stroke: bgColor,
                strokeWidth: 2,
                tooltipText:
                    "{category}: [bold]Rp. {value} (Amount)"
            });

            series.slices.template.states.create("hover", { scale: 0.95 });

            series.get("colors").set("colors", [
                am5.color("#009EF7"),
                am5.color("#F1416C"),
                am5.color("#7239EA")
            ]);

            var data = data_chart.total;
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
    am5.ready(function () {
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

        var data = data_chart.pembelian;

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
    am5.ready(function () {
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

        var data = data_chart.fs;

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
    am5.ready(function () {

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

        var data = data_chart.cno;

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
    am5.ready(function () {
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