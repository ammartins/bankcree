$(document).ready(
    function () {
        $(".table").tablesorter({debug: false});

        // Data For Graphs
        var sdF = [];
        var idx = 0;
        var total = 0;

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
                            format: "{point.name} {point.value}",
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

        // ---------------------------------------------------------------------
        graphData = [];
        for (key in objM) {
            if (objM[key]['category']) {
                if (parseInt(objM[key]['cost']) < 0 && !objM[key]['savings']) {
                    if (!graphData[objM[key]['category']]) {
                        graphData[objM[key]['category']] =
                        {
                            'name': objM[key]['category'],
                            data: [0,0,0,0,0,0,0.0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]
                        };
                    }
                    graphData[objM[key]['category']]['data'][objM[key]['dia']] = parseInt(objM[key]['cost']);
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
                pointFormat: '{series.name}: {point.y} <br/>Total: {point.stackTotal} '
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

        // ---------------------------------------------------------------------------------------
        // recurringData = [0]
        // currentMonthData = [0]
        // previouMonth = [0]
        //
        // for (var n = 1; n < 32; n++) {
        //     recurringData[n] = 0
        //     currentMonthData[n] = 0
        //     previouMonth[n] = 0
        // }
        //
        // for (key in objDS) {
        //     if (objDS.hasOwnProperty(key)) {
        //         currentMonthData[objDS[key]['day']] = parseInt(objDS[key]['endsaldo'])
        //     }
        // }
        //
        // // This is not nice but ya ...
        // for (key in currentMonthData) {
        //     if (currentMonthData[key] == 0 && key > 0) {
        //         currentMonthData[key] = currentMonthData[key-1]
        //     }
        // }
        //
        // for (key in objPM) {
        //     previouMonth[objPM[key]['day']] = parseInt(objPM[key]['endsaldo'])
        // }
        //
        // // This is not nice but ya ...
        // for (key in previouMonth) {
        //     if (previouMonth[key] == 0 && key > 0) {
        //         previouMonth[key] = previouMonth[key-1]
        //     }
        // }
        //
        // for (key in objR) {
        //     recurringData[objR[key]['day']] = parseInt(objR[key]['median']*-1)
        // }
        //
        // Highcharts.chart('container3', {
        //     title: {
        //         text: 'Available Saldo'
        //     },
        //     yAxis: {
        //         title: {
        //             text: ''
        //         }
        //     },
        //     plotOptions: {
        //         area: {
        //             pointStart: 1,
        //             marker: {
        //                 enabled: false,
        //                 symbol: 'circle',
        //                 radius: 2,
        //                 states: {
        //                     hover: {
        //                         enabled: true
        //                     }
        //                 }
        //             }
        //         }
        //     },
        //     series: [{
        //         name: 'Current Month',
        //         data: currentMonthData
        //     },{
        //         name: 'Previous Month',
        //         data: previouMonth
        //     }],
        // });
    }
);
