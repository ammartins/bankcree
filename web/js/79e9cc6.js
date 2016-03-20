var chart1; // globally available

$(document).ready(function() {
    $('#daily tr').click(function() {
      alert('Sucess');
    });

    $('#myTabs a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
    });

    $('#months a').click(function (e) {
      e.preventDefault();
      // TODO ajax call to get the content for the month and reload the table :)
      $.ajax({
        url: "/",
        data: { currentMonth: $(this).attr('href') }
      }).done(function() {
        console.log('Fix this later by getting the table redesigned :)');
      });
    });

    var sdF   = [];
    var frr   = [ 0,0,0,0,0,0,0,0,0,0,
                  0,0,0,0,0,0,0,0,0,0,
                  0,0,0,0,0,0,0,0,0,0,0];
    var idx   = 0;
    var total = 0;

    // Disaply Bar or Columns
    if (window.innerHeight > window.innerWidth) {
        gtype = 'bar';
    } else {
        gtype = 'column';
    }

    for(var key in obj) {
        if (obj.hasOwnProperty(key)){
            if (obj[key]['total'] > 0)
            {
                total = parseInt(obj[key]['total']);
            }
        }
    }

    // total is 100 so sd[key] is percent
    // 100 - total
    // x   -  sd[key]
    for(var key in obj) {
        if (obj.hasOwnProperty(key) && parseInt(obj[key]['total']) <= 0)
        {
            sdF[idx++] = { 'name': obj[key]['shortDescription'],
            'y' : (Math.floor((parseInt(obj[key]['total'])*100)/total))*-1 };
        }
    }

    idx = 0;
    for(var key in objD) {
        if (objD.hasOwnProperty(key))
        {
            while (objD[key]['days'] != idx)
            {
                frr[idx++] = 0;
            }
            frr[idx++] = parseInt(objD[key]['amount']);
        }
    }

    chart2 = new Highcharts.Chart({
        chart: {
            renderTo: 'container',
            type: gtype
        },
        title: {
            text: 'Income'
        },
        xAxis: {
            categories: [
                1,2,3,4,5,6,7,8,9,10,
                11,12,13,14,15,16,17,18,19,20,
                21,22,23,24,25,26,27,28,29,30,31
            ]
        },
        yAxis: {
            title: {
                text: 'Income'
            }
        },
        series: [{
            name: 'Total',
            data: frr
        }]
    });

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
