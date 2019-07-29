$(document).ready(
    function () {
        var sdF = []
        var sdFP = []
        var idx = 0
        var total = 0

        for (key in objP) {
            sdF[key] = []
            sdF[key].push(objP[key])
        }

        for (key in obj) {
            if (obj[key][0]['parent']) {
                sdF[obj[key][0]['parent']['name']].push(obj[key])
                continue
            }
            sdF[obj[key][0]['name']].push(obj[key])
        }

        console.log(sdF)

        var graph = 0;
        var browserData = []
        var versionsData = []
        for (cat in sdF) {
            for (key in sdF[cat]) {
                if (key == 0) {
                    browserData.push({
                        name: cat,
                        y: sdF[cat][key],
                        color: '#'+stringToHex(cat)
                    });
                }
            }
            /*
             * Index 0 is total for this Category
             * All other indexes are a breakdown of the costs
             */
        }

        console.log(browserData)


        // Highcharts.chart('container', {
        //     chart: {type: 'pie'},
        //     title: {text: 'Browser market share, January, 2018'},
        //     subtitle: {text: 'Source: <a href="http://statcounter.com" target="_blank">statcounter.com</a>'},
        //     plotOptions: {pie: {shadow: false,center: ['50%', '50%']}},
        //     tooltip: {valueSuffix: '%'},
        //     series: [{
        //         name: 'Browsers',
        //         data: browserData,
        //         size: '60%',
        //         dataLabels: {
        //             formatter: function () {
        //                 return this.y > 5 ? this.point.name : null;
        //             },
        //             color: '#ffffff',
        //             distance: -30
        //         }
        //     }, {
        //         name: 'Versions',
        //         data: versionsData,
        //         size: '80%',
        //         innerSize: '60%',
        //         dataLabels: {
        //             formatter: function () {
        //                 // display only if larger than 1
        //                 return this.y > 1 ? '<b>' + this.point.name + ':</b> ' +
        //                     this.y + '%' : null;
        //             }
        //         },
        //         id: 'versions'
        //     }],
        //     responsive: {
        //         rules: [{
        //             condition: {maxWidth: 400},
        //             chartOptions: {series: [{}, {id: 'versions',dataLabels: {enabled: false}}]}
        //         }]
        //     }
        // });


        // idx = 0
        // total = 0
        // for (key in obj) {
        //     if (parseInt(obj[key].total) < 0) {
        //         total += parseInt(obj[key].total);
        //     }
        // }
        // for (key in obj) {
        //     if (obj.hasOwnProperty(key) && parseInt(obj[key].total) < 0) {
        //         color = '#'+stringToHex(obj[key][0]['name'])
        //         if (obj[key][0]['parent']) {
        //             color = '#'+stringToHex(obj[key][0]['parent']['name'])
        //         }
        //         sdF[idx++] = {
        //             "name": obj[key].shortDescription,
        //             "y" : parseFloat(((parseInt(obj[key].total))/total).toFixed(2)),
        //             "value" : parseInt(obj[key].total),
        //             "color" : color,
        //         };
        //     }
        // }

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
