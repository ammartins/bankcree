var chart1; // globally available

$(document).ready(function() {
  // Data For Graphs
  var sdF         = [];
  var frr         = [ 0,0,0,0,0,0,0,0,0,0,
                      0,0,0,0,0,0,0,0,0,0,
                      0,0,0,0,0,0,0,0,0,0,0];
  var perMonth    = [ 0,0,0,0,0,0,0,0,0,0,
                      0,0];
  var perMonth1   = [ 0,0,0,0,0,0,0,0,0,0,
                      0,0];
  var idx         = 0;
  var total       = 0;

  // Disaply Bar or Columns
  if (window.innerHeight > window.innerWidth) {
      gtype = 'bar';
  } else {
      gtype = 'column';
  }

  // Calculate Total Expenses
  for(var key in obj) {
      if (obj.hasOwnProperty(key)){
          if (obj[key]['total'] > 0)
          {
              total = parseInt(obj[key]['total']);
          }
      }
  }
  // To avoid strange infinity on the y value
  if ( total <= 0) { total = 1; }

  // total is 100 so sd[key] is percent
  // 100 - total
  // x   -  sd[key]
  for(var key in obj) {
      if (obj.hasOwnProperty(key) && parseInt(obj[key]['total']) <= 0)
      {
          sdF[idx++] = {
              'name': obj[key]['shortDescription'],
              'y' : (Math.floor((parseInt(obj[key]['total'])*100)/total))*-1
          };
      }
  }

  // This is for the Year graphMonthYear
  idx = 0;
  for(var key in objM) {
      if (objM.hasOwnProperty(key))
      {
          if ( objM.hasOwnProperty(key) ) {
            perMonth[idx++] = parseInt(objM[key]['amount']);
          }
      }
  }

  idx = 0;
  for(var key in objM2) {
      if (objM2.hasOwnProperty(key))
      {
          //console.log(key);
          if ( objM2.hasOwnProperty(key) ) {
            perMonth1[idx++] = parseInt(objM2[key]['amount']);
          }
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

  chart1 = new Highcharts.Chart({
      chart: {
          renderTo: 'container2',
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false,
          type: 'pie',
      },
      tooltip: {
              pointFormat: '<b>Total %: {point.y:.1f}% : Spent %: {point.percentage:.1f}%</b>'
      },
      plotOptions: {
          pie: {
              allowPointSelect: true,
              cursor: 'pointer',
              dataLabels: {
                  enabled: false
              },
              showInLegend: true
          }
      },
      title: {
          text: 'Quantity'
      },
      series: [{
          data: sdF
      }]
  });

  chart2 = new Highcharts.Chart({
      chart: {
          renderTo: 'container',
          type: gtype
      },
      title: {
          text: 'Daily Expensives'
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

  chart3 = new Highcharts.Chart({
      chart: {
          renderTo: 'container3',
          type: gtype
      },
      title: {
          text: 'Monthly Expensives'
      },
      xAxis: {
          categories: [
              "Jan","Fev","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"
          ]
      },
      yAxis: {
          title: {
              text: 'Income'
          }
      },
      series: [{
          name: 'Total Current Year',
          data: perMonth
      }, {
          name: 'Total Previous Year',
          data: perMonth1
      }]
  });
});
