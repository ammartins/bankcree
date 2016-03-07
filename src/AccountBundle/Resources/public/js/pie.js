var chart1; // globally available

$(document).ready(function() {
    $('#myTabs a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
    });

    $('#months a').click(function (e) {
      e.preventDefault();
      // TODO ajax call to get the content for the month and reload the table :)
      // alert($(this).attr('href'));
    });

    var sdF   = [];
    var idx   = 0;
    var total = 0;

    for(var key in obj) {
        if (obj.hasOwnProperty(key)){
            if (obj[key]['total'] > 0) {
                total = parseInt(obj[key]['total']);
            }
        }
    }

    // total is 100 so sd[key] is percent
    // 100 - total
    // x   -  sd[key]
    for(var key in obj) {
        if (obj.hasOwnProperty(key) && parseInt(obj[key]['total']) <= 0){
            sdF[idx++] = { 'name': obj[key]['shortDescription'],
            'y' : (Math.floor((parseInt(obj[key]['total'])*100)/total))*-1 };
        }
    }

    chart1 = new Highcharts.Chart({
        chart: {
            renderTo: 'container2',
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Quantity'
        },
        series: [{
            data: sdF
        }]
    });

});
