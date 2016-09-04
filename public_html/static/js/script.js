var topChartOptions = {
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
            text: 'درصد',
            enabled: false
        },
        labels: {
            enabled: false
        },
        gridLineWidth: 0,
        minorGridLineWidth: 0,
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
};

var randomChartOptions = {
    chart: {
        type: 'column',
        backgroundColor: '#FFFFFF'
    },
    credits: {
        enabled: false
    },
    title: {
        text: 'نتیجه نظرسنجی به تفکیک جنسیت'
    },
    xAxis: {
        type: 'category',
        labels: {
            enabled: true
        }
    },
    yAxis: {
        title: {
            enabled: false
        },
        labels: {
            enabled: false
        },
        gridLineWidth: 0,
        minorGridLineWidth: 0
    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:.1f}%'
            }
        }
    },

    tooltip: {
        // headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        headerFormat: '',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
    },

    series: [{
        name: 'جنسیت',
        colorByPoint: true,
        data: [{
            name: 'مرد',
            y: 56.33,
            drilldown: 'Male'
        }, {
            name: 'زن',
            y: 43.67,
            drilldown: 'Female'
        }]
    }],
    drilldown: {
        series: [{
            name: 'Male',
            id: 'Male',
            data: [
                [
                    'v11.0',
                    24.13
                ],
                [
                    'v8.0',
                    17.2
                ],
                [
                    'v9.0',
                    8.11
                ],
                [
                    'v10.0',
                    5.33
                ],
                [
                    'v6.0',
                    1.06
                ],
                [
                    'v7.0',
                    0.5
                ]
            ]
        }, {
            name: 'Female',
            id: 'Female',
            data: [
                [
                    'v40.0',
                    5
                ],
                [
                    'v41.0',
                    4.32
                ],
                [
                    'v42.0',
                    3.68
                ],
                [
                    'v39.0',
                    2.96
                ],
                [
                    'v36.0',
                    2.53
                ],
                [
                    'v43.0',
                    1.45
                ],
                [
                    'v31.0',
                    1.24
                ],
                [
                    'v35.0',
                    0.85
                ],
                [
                    'v38.0',
                    0.6
                ],
                [
                    'v32.0',
                    0.55
                ],
                [
                    'v37.0',
                    0.38
                ],
                [
                    'v33.0',
                    0.19
                ],
                [
                    'v34.0',
                    0.14
                ],
                [
                    'v30.0',
                    0.14
                ]
            ]
        }]
    }
};

$(function() {
    $('.top-chart').highcharts(topChartOptions);
    $('.random-chart').highcharts(randomChartOptions);

    $.ajax({
        type: 'post',
        url: '/',
        data: { 'random': 'random' },
        success: function(data) {
            data = data.msg;
            console.log(data);
            $('.random-chart').highcharts({
                chart: {
                    type: 'column'
                },
                title: {
                    text: data.title.title
                },
                subtitle: {
                    text: 'منبع: sarshomar.com'
                },
                xAxis: {
                    categories: [
                        data.title.title
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
                    shared: false,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: data.result
            });
        }
    });
});
