var _max;
$(function() {
    $('.top-chart').highcharts({
        chart: {
            type: 'column',
            // events: {
            //     load: function() {
            //         var check = $('.top-chart').highcharts();
            //         check.yAxis[0].setExtremes(null, check.axes[1].dataMax);
            //     }
            // }
        },
        exporting: {
            enabled: false
        },
        credits: {
            enabled: false
        },
        title: {
            text: 'کدام روش را برای رسیدن به وزن ایده آل مناسبتر میدانید؟'
        },
        xAxis: {
            labels: {
                enabled: false
            },
            categories: [
                'روش های رسیدن به وزن ایده آل'
            ]
        },
        yAxis: {
            min: 0,
            title: {
                text: 'درصد'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' + '<td style="padding:0"><b>{point.y:.1f} %</b></td></tr>',
            footerFormat: '</table>',
            shared: false,
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
            data: [30]
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

    // $.ajax({
    //     type : 'post',
    //     url : '/',
    //     data :{'random' : 'random'},
    //     success : function (data) {
    //         console.log(data);
    //         $('.random-chart').highcharts({
    //             chart: {
    //                 type: 'column'
    //             },
    //             title: {
    //                 text: data.title
    //             },
    //             subtitle: {
    //                 text: 'منبع: sarshomar.com'
    //             },
    //             xAxis: {
    //                 categories: [
    //                     data.title
    //                 ]
    //             },
    //             yAxis: {
    //                 min: 0,
    //                 max: 100,
    //                 title: {
    //                     text: 'درصد'
    //                 }
    //             },
    //             tooltip: {
    //                 headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
    //                 pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' + '<td style="padding:0"><b>{point.y:.1f} %</b></td></tr>',
    //                 footerFormat: '</table>',
    //                 shared: false,
    //                 useHTML: true
    //             },
    //             plotOptions: {
    //                 column: {
    //                     pointPadding: 0.2,
    //                     borderWidth: 0
    //                 }
    //             },
    //             series: data.result
    //         });
    //     }
    // });
});
