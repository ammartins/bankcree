$(document).ready(function() {
  // Disaply Bar or Columns
  if (window.innerHeight > window.innerWidth) {
    gtype = 'bar';
  } else {
    gtype = 'column';
  }

  /***************************************************************************
  *               This is for the Year Net Worth per days                  *
  **************************************************************************/
  frr = [];
  for(key in yearTransac) {
    if (yearTransac[key].hasOwnProperty('createAt')) {
      frr[key] = parseInt(yearTransac[key].endsaldo);
    }
  }

  days = frr.length-1;

  while (days > 0) {
    if (frr[days] === undefined) {
      index = -1;
      while (frr[days-index] === undefined) {
        index--;
      }
      frr[days] = frr[days-index];
    }
    days--;
  }
  frr[0] = frr[1];

  netWorth = new Highcharts.Chart({
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
      }
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
  });
});
