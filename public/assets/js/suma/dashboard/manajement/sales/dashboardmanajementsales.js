// dokumen ready

$(document).ready(function () {
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


            var data = {!!json_encode($gross_profit)!!
        };

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
});