$(document).ready(
    function () {
        $(".table").tablesorter({debug: false});
        // Data For Graphs
        var sdF = [];
        var idx = 0;
        var total = 0;

        // Disaply Bar or Columns
        if (window.innerHeight > window.innerWidth) {
            gtype = 'bar';
        } else {
            gtype = 'column';
        }

    /***************************************************************************
     *                        This is for the Pie Chart                        *
     **************************************************************************/
        // Calculate Total Expenses
        for (key in obj) {
            if (obj.hasOwnProperty(key)) {
                if (obj[key].total > 0) {
                    total += parseInt(obj[key].total);
                }
            }
        }

        // To avoid strange infinity on the y value
        if (total <= 0) {
            total = 1;
        }

        // total is 100 so sd[key] is percent
        // 100 - total
        // x   -  sd[key]
        for (key in obj) {
            if (obj.hasOwnProperty(key) && parseInt(obj[key].total) < 0) {
                sdF[idx++] = {
                    "name": obj[key].shortDescription,
                    "y" : (((parseInt(obj[key].total)*100)/total))*-1,
                    "value" : parseInt(obj[key].total)
                };
            }
        }

        chart1 = new Highcharts.Chart(
            {
                chart: {
                    renderTo: 'container2',
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie',
                },
                tooltip: {
                    pointFormat: "<b>Spent %: {point.percentage:.2f}%</b>"
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: "{point.name} {point.value}€",
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        },
                        showInLegend: true
                    }
                },
                title: {
                    text: ''
                },
                series: [{
                    data: sdF
                }]
            }
        );

    /***************************************************************************
     *                        This is for the Pie Chart                        *
     **************************************************************************/
        graphData = [];
        for (key in objM) {
            if (objM[key][0]['categories']) {
                if (!graphData[objM[key][0]['categories']['name']]) {
                    graphData[objM[key][0]['categories']['name']] =
                    {
                        'name': objM[key][0]['categories']['name'],
                        data: [0,0,0,0,0,0,0.0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]
                    };
                }
                if (parseInt(objM[key]['cost']) < 0) {
                    graphData[objM[key][0]['categories']['name']]['data'][objM[key]['dia']] = parseInt(objM[key]['cost']);
                }
            }
        }

        endResult = [];
        for (obj in graphData) {
            endResult.push(graphData[obj]);
        }

        Highcharts.chart('container', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Daily Categories'
            },
            yAxis: {
                title: {
                    text: 'Here'
                },
                stackLabels: {
                    enabled: false,
                    style: {
                        fontWeight: 'bold',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                    }
                }
            },
            tooltip: {
                headerFormat: '<b>{point.x}</b><br/>',
                pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
            },
            plotOptions: {
                column: {
                    stacking: 'normal',
                    dataLabels: {
                        enabled: false,
                        color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                    }
                }
            },
            series: endResult
        })
    }
);
