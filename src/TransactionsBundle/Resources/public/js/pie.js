$(document).ready(
    function () {
        $(".table").tablesorter({debug: false})

        // Data For Graphs
        var sdF = []
        var sdFP = []
        var idx = 0
        var total = 0

        for (key in objP) {
            total += parseInt(objP[key]);
        }
        for (key in objP) {
            if (objP.hasOwnProperty(key) && parseInt(objP[key]) < 0) {
                color = '#'+stringToHex(key)
                sdFP[idx++] = {
                    "name": key,
                    "y" : parseFloat(((parseInt(objP[key]))/total).toFixed(2)),
                    "value" : parseInt(objP[key]),
                    "color" : color,
                };
            }
        }

        idx = 0
        total = 0
        for (key in obj) {
            if (parseInt(obj[key].total) < 0) {
                total += parseInt(obj[key].total);
            }
        }
        for (key in obj) {
            if (obj.hasOwnProperty(key) && parseInt(obj[key].total) < 0) {
                color = '#'+stringToHex(obj[key][0]['name'])
                if (obj[key][0]['parent']) {
                    color = '#'+stringToHex(obj[key][0]['parent']['name'])
                }
                sdF[idx++] = {
                    "name": obj[key].shortDescription,
                    "y" : parseFloat(((parseInt(obj[key].total))/total).toFixed(2)),
                    "value" : parseInt(obj[key].total),
                    "color" : color,
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
                    name: 'Browsers',
                    data: sdFP,
                    size: '60%',
                    dataLabels: {
                        formatter: function () {
                            return this.y > 5 ? this.point.name : null;
                        },
                        color: '#000'
                    }
                }]
            }
        );

        chart1 = new Highcharts.Chart(
            {
                chart: {
                    renderTo: 'container',
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
                    name: 'Browsers',
                    data: sdF,
                    size: '60%',
                    dataLabels: {
                        formatter: function () {
                            return this.y > 5 ? this.point.name : null;
                        },
                        color: '#000'
                    }
                }]
            }
        );

        // ---------------------------------------------------------------------
        // graphData = [];
        // for (key in objM) {
        //     if (objM[key]['category']) {
        //         if (parseInt(objM[key]['cost']) < 0 && !objM[key]['savings']) {
        //             if (!graphData[objM[key]['category']]) {
        //                 graphData[objM[key]['category']] =
        //                 {
        //                     'name': objM[key]['category'],
        //                     data: [0,0,0,0,0,0,0.0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]
        //                 };
        //             }
        //             graphData[objM[key]['category']]['data'][objM[key]['dia']] = parseInt(objM[key]['cost']);
        //         }
        //     }
        // }
        //
        // endResult = [];
        // for (obj in graphData) {
        //     endResult.push(graphData[obj]);
        // }
        //
        // Highcharts.chart('container', {
        //     chart: {
        //         type: 'column'
        //     },
        //     title: {
        //         text: 'Daily Categories'
        //     },
        //     yAxis: {
        //         title: {
        //             text: 'Here'
        //         },
        //         stackLabels: {
        //             enabled: false,
        //             style: {
        //                 fontWeight: 'bold',
        //                 color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
        //             }
        //         }
        //     },
        //     tooltip: {
        //         headerFormat: '<b>{point.x}</b><br/>',
        //         pointFormat: '{series.name}: {point.y} <br/>Total: {point.stackTotal} '
        //     },
        //     plotOptions: {
        //         column: {
        //             stacking: 'normal',
        //             dataLabels: {
        //                 enabled: false,
        //                 color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
        //             }
        //         }
        //     },
        //     series: endResult
        // })

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
        function stringToHex(tmp)
        {
            var str = '',
                i = 0,
                tmp_len = tmp.length,
                c;

            for (; i < 2; i += 1) {
                c = tmp.charCodeAt(i);
                str += c.toString(16);
            }
            return str;
        }
    }
);
