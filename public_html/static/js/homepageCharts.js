 var topChartOptions =
  {
   chart: {type: 'column',backgroundColor: 'rgba(255, 255, 255, 0)',verticalAlign: 'center'},
   title: {text: ""},
   xAxis:{categories:{{result.categories | raw}}},
   yAxis: {min: 0,title: {enabled: false},labels: {enabled: false},gridLineWidth: 0,minorGridLineWidth: 0},
   plotOptions: {column: {pointPadding: 0,groupPadding: 0.15,},series: {dataLabels: {enabled: false,format: '{point.y:f}%'}},inside: true},
   legend: {enabled: false},
   credits: {enabled: false},
   tooltip: {enabled: false},
   series: {{result.basic | raw}}
  };

  var categories = {{male_female_chart.categories|raw}};
  var newChartOptions =
  {
   chart: {type: 'bar',backgroundColor: 'rgba(255, 255, 255, 0)'},
   title: {text: ''},
   xAxis: [{categories: categories,reversed: false,labels: {enabled: true,step: 1},opposite: true}],
   yAxis: {title: {enabled: false},labels: {enabled: false},gridLineWidth: 0,minorGridLineWidth: 0},
   plotOptions: {series: {stacking: 'normal',dataLabels: {enabled: false,format: '{point.y:f}'}},bar: {pointPadding: 0,groupPadding: 0.01,}},
   credits: {enabled: false},
   tooltip: {formatter: function () {return '<b>' + this.series.name + ' Age ' + this.point.category + '</b><br/>' + ' Population ' + Highcharts.numberFormat(Math.abs(this.point.y), 0); }},
   series: {{male_female_chart.series|raw}}
  };

  $(function() {
   $('#random-chart .chart > div').highcharts(topChartOptions);
   $('#age-chart').highcharts(newChartOptions);

   // world map
   $('#world-map').highcharts('Map', {
    chart: {
     backgroundColor: 'transparent',
    },
    credits:{
     enabled: false
    },
    title: {
     text: ''
    },
    legend: {enabled: false},
    mapNavigation: {
     enabled: true,
     enableButtons: false,
     buttonOptions: {
      verticalAlign: 'bottom'
     }
    },
    colorAxis: {
     min: 0
    },
    series: [{
     data: worldData,
     mapData: Highcharts.maps['custom/world'],
     joinBy: 'hc-key',
     name: 'Sarshomar Society',
     states: {
      hover: {
       color: '#a4edba'
      }
     },
     dataLabels: {
      enabled: false,
      format: '{point.name}'
     }
    }]
   });


   // iran map
   $('#iran-map').highcharts('Map', {
    chart: {
     backgroundColor: 'transparent',
    },
    credits:{
     enabled: false
    },
    title : {
     text : ''
    },

    legend: {enabled: false},
    mapNavigation: {
     enabled: true,
     enableButtons: false,
     buttonOptions: {
      verticalAlign: 'bottom'
     }
    },
    colorAxis: {
     min: 0
    },

    series : [{
     data : iranData,
     mapData: Highcharts.maps['countries/ir/ir-all'],
     joinBy: 'hc-key',
     name: 'Random data',
     states: {
      hover: {
       color: '#a4edba'
      }
     },
     dataLabels: {
      enabled: false,
      format: '{point.name}'
     }
    }]
   });

  });