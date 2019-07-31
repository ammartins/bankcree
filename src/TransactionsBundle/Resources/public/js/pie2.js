$(document).ready(
    function () {
        var sdF = []
        var graph = 0;
        var parentData = []
        var transactionsData = []

        for (key in objPPM) {
            sdF[key] = []
            sdF[key].push(objPPM[key])
        }

        for (cat in sdF) {
            for (key in sdF[cat]) {
                if (key == 0) {
                    parentData.push({
                        name: cat,
                        y: (sdF[cat][key]*-1),
                        drilldown: cat
                    });
                }
            }
        }

        for (trans in objPM) {
            cena = objPM[trans]
            if (cena[0].parent && newTrans(cena[0].parent.name, transactionsData)) {
                transactionsData.push({
                    name: cena[0].parent.name,
                    id: cena[0].parent.name,
                    data: []
                })
            }
        }

        for (g in transactionsData) {
            for (trans in objPM) {
                if (objPM[trans][0].parent && objPM[trans][0].parent.name === transactionsData[g].name) {
                    item = []
                    item.push(objPM[trans].shortDescription)
                    item.push(objPM[trans].total*-1)
                    transactionsData[g].data.push(item)
                }
            }
        }

        function newTrans(parent, data)
        {
            for (t in data) {
                if (data[t].name === parent) {
                    return false;
                }
            }
            return true;
        }

        Highcharts.chart('container2', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Previous Month'
            },
            subtitle: {
                text: ''
            },
            plotOptions: {
                series: {
                    dataLabels: {
                        enabled: true,
                        format: '{point.name}: {point.y:.1f}'
                    }
                }
            },

            tooltip: {
                pointFormat: "<b>{point.percentage:.2f}%</b>"
            },

            series: [
                {
                    name: "",
                    colorByPoint: true,
                    data: parentData
                }
            ],
            drilldown: {
                series: transactionsData
            }
        });
    }
);
