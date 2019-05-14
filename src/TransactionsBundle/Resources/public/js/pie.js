$(document).ready(
    function () {
        $(".table").tablesorter({debug: false});
        // Data For Graphs
        var sdF = [];
        var perMonth = [ 0,0,0,0,0,0,0,0,0,0,0,0];
        var perMonth1 = [ 0,0,0,0,0,0,0,0,0,0,0,0];
        var months = [
        "Jan","Fev","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"
        ];
        var idx = 0;
        var total = 0;
        var key;

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
     *               This is for the Month Net Worth per days                  *
     **************************************************************************/
        frr = [];
        console.log(objD);
        for (key in objD) {
            if (objD[key].hasOwnProperty('days')) {
                frr[objD[key].days] = parseInt(objD[key].amount);
            }
        }

        days = frr.length-1;

        while (days > 0) {
            if (frr[days] === undefined) {
                i = -1;
                while (frr[days-i] === undefined) {
                    i--;
                }
                frr[days] = frr[days-i];
            }
            days--;
        }
        frr[0] = frr[1];

        netWorth = new Highcharts.Chart(
            {
                chart: {
                    renderTo: 'container',
                },
                title: {
                    text: 'Net Worth'
                },
                xAxis: {
                    categories: frr.keys
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Income'
                    },
                    plotBands: [
                    {
                        from: 499, to: 500, color: 'red'
                    },
                    {
                        from: 999, to: 1000, color: 'orange'
                    },
                    {
                        from: 1999, to: 2000, color: 'green'
                    },
                    ]
                },
                plotOptions: {
                    area: {
                        fillColor: {
                            linearGradient: {
                                x1: 0,
                                y1: 0,
                                x2: 0,
                                y2: 1
                            },
                            stops: [
                            [0, Highcharts.getOptions().colors[0]],
                            [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                            ]
                        },
                        marker: {
                            radius: 2
                        },
                        lineWidth: 1,
                        states: {
                            hover: {
                                lineWidth: 1
                            }
                        },
                        threshold: null
                    }
                },
                series: [{
                    type: 'area',
                    name: 'Total',
                    data: frr
                }]
            }
        );
    }
);
