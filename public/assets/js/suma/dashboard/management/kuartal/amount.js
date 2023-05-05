$(function chartYearToDate() {
    var year_selected = data_filter.year;
    var year_previous = parseFloat(year_selected) - 1;

    var colorPreviousYear = am5.color("#c4b5fd");
    var colorSelectedYear = am5.color("#7c3aed");
    var colorGrowth = am5.color("#22c55e");


    am5.ready(function () {
        var root = am5.Root.new("chartYearToDate");

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

        var data = data_chart.year_to_date;

        var xAxis = chart.xAxes.push(
            am5xy.CategoryAxis.new(root, {
                categoryField: "keterangan",
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
                name: year_previous.toString(),
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "previous",
                categoryXField: "keterangan",
                fill: colorPreviousYear,
                clustered: false,
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
                name: year_selected,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "selected",
                categoryXField: "keterangan",
                fill: colorSelectedYear,
                clustered: false,
                tooltip: am5.Tooltip.new(root, {
                    labelText: "[bold]{name} {categoryX}: Rp. {valueY}"
                })
            })
        );
        series2.columns.template.set("width", am5.percent(40));
        series2.data.setAll(data);

        var series3 = chart.series.push(
            am5xy.LineSeries.new(root, {
                name: "Growth",
                xAxis: xAxis,
                yAxis: yAxis2,
                valueYField: "growth",
                categoryXField: "keterangan",
                stroke: colorGrowth,
                fill: colorGrowth,
                tooltip: am5.Tooltip.new(root, {
                    pointerOrientation: "horizontal",
                    labelText: "[bold]{name} {categoryX}: {valueY}%"
                })
            })
        );

        series3.strokes.template.setAll({
            strokeWidth: 3
        });

        series3.data.setAll(data);

        series3.bullets.push(function () {
            return am5.Bullet.new(root, {
                sprite: am5.Circle.new(root, {
                    strokeWidth: 3,
                    stroke: series3.get("stroke"),
                    radius: 4,
                    fill: colorGrowth
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

$(function chartSemester() {
    var year_selected = data_filter.year;
    var year_previous = parseFloat(year_selected) - 1;

    var colorPreviousYear = am5.color("#fecdd3");
    var colorSelectedYear = am5.color("#f43f5e");
    var colorPreviousKontribusi = am5.color("#eab308");
    var colorSelectedKontribusi = am5.color("#22d3ee");


    am5.ready(function () {
        var root = am5.Root.new("chartSemester");

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

        var data = data_chart.semester;

        var xAxis = chart.xAxes.push(
            am5xy.CategoryAxis.new(root, {
                categoryField: "keterangan",
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
                name: year_previous.toString(),
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "previous",
                categoryXField: "keterangan",
                fill: colorPreviousYear,
                clustered: false,
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
                name: year_selected,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "selected",
                categoryXField: "keterangan",
                fill: colorSelectedYear,
                clustered: false,
                tooltip: am5.Tooltip.new(root, {
                    labelText: "[bold]{name} {categoryX}: Rp. {valueY}"
                })
            })
        );
        series2.columns.template.set("width", am5.percent(40));
        series2.data.setAll(data);

        var series3 = chart.series.push(
            am5xy.LineSeries.new(root, {
                name: "Kontribusi "+year_previous,
                xAxis: xAxis,
                yAxis: yAxis2,
                valueYField: "previous_kontribusi",
                categoryXField: "keterangan",
                stroke: colorPreviousKontribusi,
                fill: colorPreviousKontribusi,
                tooltip: am5.Tooltip.new(root, {
                    pointerOrientation: "horizontal",
                    labelText: "[bold]{name} {categoryX}: {valueY}%"
                })
            })
        );

        series3.strokes.template.setAll({
            strokeWidth: 3,
            strokeDasharray: [3, 3]
        });

        series3.data.setAll(data);

        series3.bullets.push(function () {
            return am5.Bullet.new(root, {
                sprite: am5.Circle.new(root, {
                    strokeWidth: 3,
                    stroke: series3.get("stroke"),
                    radius: 4,
                    fill: colorPreviousKontribusi
                })
            });
        });

        var series4 = chart.series.push(
            am5xy.LineSeries.new(root, {
                name: "Kontribusi "+year_selected,
                xAxis: xAxis,
                yAxis: yAxis2,
                valueYField: "selected_kontribusi",
                categoryXField: "keterangan",
                stroke: colorSelectedKontribusi,
                fill: colorSelectedKontribusi,
                tooltip: am5.Tooltip.new(root, {
                    pointerOrientation: "horizontal",
                    labelText: "[bold]{name} {categoryX}: {valueY}%"
                })
            })
        );

        series4.strokes.template.setAll({
            strokeWidth: 3,
        });

        series4.data.setAll(data);

        series4.bullets.push(function () {
            return am5.Bullet.new(root, {
                sprite: am5.Circle.new(root, {
                    strokeWidth: 2,
                    stroke: series4.get("fill"),
                    radius: 4,
                    fill: colorSelectedKontribusi
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

$(function chartPerBulan() {
    var year_selected = data_filter.year;
    var year_previous = parseFloat(year_selected) - 1;

    var colorPreviousYear = am5.color("#6ee7b7");
    var colorSelectedYear = am5.color("#0ea5e9");
    var colorPreviousKontribusi = am5.color("#facc15");
    var colorSelectedKontribusi = am5.color("#22d3ee");
    var colorGrowth = am5.color("#22c55e");

    am5.ready(function () {
        var root = am5.Root.new("chartDetailPerBulan");

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

        var data = data_chart.detail_per_bulan;

        var xAxis = chart.xAxes.push(
            am5xy.CategoryAxis.new(root, {
                categoryField: "month",
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
                name: year_previous.toString(),
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "previous",
                categoryXField: "month",
                fill: colorPreviousYear,
                clustered: false,
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
                name: year_selected,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "selected",
                categoryXField: "month",
                fill: colorSelectedYear,
                clustered: false,
                tooltip: am5.Tooltip.new(root, {
                    labelText: "[bold]{name} {categoryX}: Rp. {valueY}"
                })
            })
        );
        series2.columns.template.set("width", am5.percent(40));
        series2.data.setAll(data);

        var series3 = chart.series.push(
            am5xy.LineSeries.new(root, {
                name: "Kontribusi "+year_previous,
                xAxis: xAxis,
                yAxis: yAxis2,
                valueYField: "previous_kontribusi",
                categoryXField: "month",
                stroke: colorPreviousKontribusi,
                fill: colorPreviousKontribusi,
                tooltip: am5.Tooltip.new(root, {
                    pointerOrientation: "horizontal",
                    labelText: "[bold]{name} {categoryX}: {valueY}%"
                })
            })
        );

        series3.strokes.template.setAll({
            strokeWidth: 3,
            strokeDasharray: [3, 3]
        });

        series3.data.setAll(data);

        series3.bullets.push(function () {
            return am5.Bullet.new(root, {
                sprite: am5.Circle.new(root, {
                    strokeWidth: 2,
                    stroke: series3.get("stroke"),
                    radius: 4,
                    fill: colorPreviousKontribusi
                })
            });
        });

        var series4 = chart.series.push(
            am5xy.LineSeries.new(root, {
                name: "Kontribusi "+year_selected,
                xAxis: xAxis,
                yAxis: yAxis2,
                valueYField: "selected_kontribusi",
                categoryXField: "month",
                stroke: colorSelectedKontribusi,
                fill: colorSelectedKontribusi,
                tooltip: am5.Tooltip.new(root, {
                    pointerOrientation: "horizontal",
                    labelText: "[bold]{name} {categoryX}: {valueY}%"
                })
            })
        );

        series4.strokes.template.setAll({
            strokeWidth: 3,
        });

        series4.data.setAll(data);

        series4.bullets.push(function () {
            return am5.Bullet.new(root, {
                sprite: am5.Circle.new(root, {
                    strokeWidth: 2,
                    stroke: series4.get("fill"),
                    radius: 4,
                    fill: colorSelectedKontribusi
                })
            });
        });

        var series5 = chart.series.push(
            am5xy.LineSeries.new(root, {
                name: "Growth "+year_selected,
                xAxis: xAxis,
                yAxis: yAxis2,
                valueYField: "growth",
                categoryXField: "month",
                stroke: colorGrowth,
                fill: colorGrowth,
                tooltip: am5.Tooltip.new(root, {
                    pointerOrientation: "horizontal",
                    labelText: "[bold]{name} {categoryX}: {valueY}%"
                })
            })
        );

        series5.strokes.template.setAll({
            strokeWidth: 3,
        });

        series5.data.setAll(data);

        series5.bullets.push(function () {
            return am5.Bullet.new(root, {
                sprite: am5.Circle.new(root, {
                    strokeWidth: 2,
                    stroke: series5.get("fill"),
                    radius: 4,
                    fill: colorGrowth
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

$(function chartQuarter() {
    var year_selected = data_filter.year;
    var year_previous = parseFloat(year_selected) - 1;

    var colorPreviousYear = am5.color("#bae6fd");
    var colorSelectedYear = am5.color("#0ea5e9");
    var colorPreviousKontribusi = am5.color("#eab308");
    var colorSelectedKontribusi = am5.color("#22d3ee");
    var colorGrowth = am5.color("#4ade80");

    am5.ready(function () {
        var root = am5.Root.new("chartDetailQuarter");

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

        var data = data_chart.detail_quarter;

        var xAxis = chart.xAxes.push(
            am5xy.CategoryAxis.new(root, {
                categoryField: "keterangan",
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
                name: year_previous.toString(),
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "previous",
                categoryXField: "keterangan",
                fill: colorPreviousYear,
                clustered: false,
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
                name: year_selected,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "selected",
                categoryXField: "keterangan",
                fill: colorSelectedYear,
                clustered: false,
                tooltip: am5.Tooltip.new(root, {
                    labelText: "[bold]{name} {categoryX}: Rp. {valueY}"
                })
            })
        );
        series2.columns.template.set("width", am5.percent(40));
        series2.data.setAll(data);

        var series3 = chart.series.push(
            am5xy.LineSeries.new(root, {
                name: "Kontribusi "+year_previous,
                xAxis: xAxis,
                yAxis: yAxis2,
                valueYField: "previous_kontribusi",
                categoryXField: "keterangan",
                stroke: colorPreviousKontribusi,
                fill: colorPreviousKontribusi,
                tooltip: am5.Tooltip.new(root, {
                    pointerOrientation: "horizontal",
                    labelText: "[bold]{name} {categoryX}: {valueY}%"
                })
            })
        );

        series3.strokes.template.setAll({
            strokeWidth: 3,
            strokeDasharray: [3, 3]
        });

        series3.data.setAll(data);

        series3.bullets.push(function () {
            return am5.Bullet.new(root, {
                sprite: am5.Circle.new(root, {
                    strokeWidth: 3,
                    stroke: series3.get("stroke"),
                    radius: 4,
                    fill: colorPreviousKontribusi
                })
            });
        });

        var series4 = chart.series.push(
            am5xy.LineSeries.new(root, {
                name: "Kontribusi "+year_selected,
                xAxis: xAxis,
                yAxis: yAxis2,
                valueYField: "selected_kontribusi",
                categoryXField: "keterangan",
                stroke: colorSelectedKontribusi,
                fill: colorSelectedKontribusi,
                tooltip: am5.Tooltip.new(root, {
                    pointerOrientation: "horizontal",
                    labelText: "[bold]{name} {categoryX}: {valueY}%"
                })
            })
        );

        series4.strokes.template.setAll({
            strokeWidth: 3,
        });

        series4.data.setAll(data);

        series4.bullets.push(function () {
            return am5.Bullet.new(root, {
                sprite: am5.Circle.new(root, {
                    strokeWidth: 2,
                    stroke: series4.get("fill"),
                    radius: 4,
                    fill: colorSelectedKontribusi
                })
            });
        });

        var series5 = chart.series.push(
            am5xy.LineSeries.new(root, {
                name: "Growth "+year_selected,
                xAxis: xAxis,
                yAxis: yAxis2,
                valueYField: "growth",
                categoryXField: "keterangan",
                stroke: colorGrowth,
                fill: colorGrowth,
                tooltip: am5.Tooltip.new(root, {
                    pointerOrientation: "horizontal",
                    labelText: "[bold]{name} {categoryX}: {valueY}%"
                })
            })
        );

        series5.strokes.template.setAll({
            strokeWidth: 3,
        });

        series5.data.setAll(data);

        series5.bullets.push(function () {
            return am5.Bullet.new(root, {
                sprite: am5.Circle.new(root, {
                    strokeWidth: 2,
                    stroke: series5.get("fill"),
                    radius: 4,
                    fill: colorGrowth
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

$(function chartSummaryQuarter() {
    var year_selected = data_filter.year;
    var year_previous = parseFloat(year_selected) - 1;

    var colorPreviousYear = am5.color("#fecdd3");
    var colorSelectedYear = am5.color("#fb7185");
    var colorGrowth = am5.color("#22c55e");


    am5.ready(function () {
        var root = am5.Root.new("chartSummaryQuarter");

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

        var data = data_chart.summary_quarter;

        var xAxis = chart.xAxes.push(
            am5xy.CategoryAxis.new(root, {
                categoryField: "keterangan",
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
                name: year_previous.toString(),
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "previous",
                categoryXField: "keterangan",
                fill: colorPreviousYear,
                clustered: false,
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
                name: year_selected,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "selected",
                categoryXField: "keterangan",
                fill: colorSelectedYear,
                clustered: false,
                tooltip: am5.Tooltip.new(root, {
                    labelText: "[bold]{name} {categoryX}: Rp. {valueY}"
                })
            })
        );
        series2.columns.template.set("width", am5.percent(40));
        series2.data.setAll(data);

        var series3 = chart.series.push(
            am5xy.LineSeries.new(root, {
                name: "Growth",
                xAxis: xAxis,
                yAxis: yAxis2,
                valueYField: "growth",
                categoryXField: "keterangan",
                stroke: colorGrowth,
                fill: colorGrowth,
                tooltip: am5.Tooltip.new(root, {
                    pointerOrientation: "horizontal",
                    labelText: "[bold]{name} {categoryX}: {valueY}%"
                })
            })
        );

        series3.strokes.template.setAll({
            strokeWidth: 3
        });

        series3.data.setAll(data);

        series3.bullets.push(function () {
            return am5.Bullet.new(root, {
                sprite: am5.Circle.new(root, {
                    strokeWidth: 3,
                    stroke: series3.get("stroke"),
                    radius: 4,
                    fill: colorGrowth
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
