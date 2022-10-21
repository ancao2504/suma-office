// document ready
$(document).ready(function () {
    $(function chartComparison() {
        am5.ready(function () {
            var root = am5.Root.new("chartComparison");

            root.setThemes([
                am5themes_Animated.new(root)
            ]);

            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: false,
                panY: false,
                layout: root.verticalLayout
            }));

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    centerX: am5.p50,
                    x: am5.p50
                })
            );

            var data = {!!json_encode($comparison)!!
        };

        var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
            categoryField: "company",
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

        function createSeries(name, fieldName, color) {
            var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                name: name,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: fieldName,
                fill: color,
                categoryXField: "company",
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

        createSeries("Bulan Yang Dipilih", "total_sekarang", am5.color("#F1416C"));
        createSeries("Bulan Sebelumnya", "total_lalu", am5.color("#009EF7"));

        var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
        }));
        cursor.lineY.set("forceHidden", true);
        cursor.lineX.set("forceHidden", true);

        chart.appear(1000, 100);
    });
});
});