$(function() {
    $('.top-chart').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'کدام روش را برای رسیدن به وزن ایده آل مناسبتر میدانید؟'
        },
        subtitle: {
            text: 'منبع: sarshomar.com'
        },
        xAxis: {
            categories: [
                'روش های رسیدن به وزن ایده آل'
            ]
        },
        yAxis: {
            min: 0,
            max: 100,
            title: {
                text: 'درصد'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' + '<td style="padding:0"><b>{point.y:.1f} %</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'ورزش و رژیم همزمان',
            data: [60]
        }, {
        	name: 'ورزش منظم',
        	data: [20]
        }, {
        	name: 'عمل جراحی',
        	data: [20]
        }, {
        	name: 'داروهای لاغری',
        	data: [0]
        }]
    });
});
