$(document).ready(
    function () {
        var sdF = []
        var graph = 0;
        var parentData = []
        var transactionsData = []

        for (key in objP) {
            sdF[key] = []
            sdF[key].push(objP[key])
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

        for (trans in obj) {
            cena = obj[trans]
            if (cena[0].parent && newTrans(cena[0].parent.name, transactionsData)) {
                transactionsData.push({
                    name: cena[0].parent.name,
                    id: cena[0].parent.name,
                    data: []
                })
            }
        }

        for (g in transactionsData) {
            for (trans in obj) {
                if (obj[trans][0].parent && obj[trans][0].parent.name === transactionsData[g].name) {
                    item = []
                    item.push(obj[trans].shortDescription)
                    item.push(obj[trans].total*-1)
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

        Highcharts.chart('container', {
            chart: {
                type: 'pie'
            },
            title: {
                text: ''
            },
            subtitle: {
                text: ''
            },
            plotOptions: {
                series: {
                    dataLabels: {
                        enabled: true,
                        format: '{point.name}: {point.y:.1f}%'
                    }
                }
            },

            tooltip: {
                pointFormat: "<b>Spent %: {point.percentage:.2f}%</b>"
            },

            series: [
                {
                    name: "Browsers",
                    colorByPoint: true,
                    data: parentData,
                    size: '60%'
                }
            ],
            drilldown: {
                series: transactionsData
            }
        });
    }
);
