var options = {
  width: 360,
  height: 120,
  redFrom: 7,
  redTo: 10,
  yellowFrom: 3,
  yellowTo: 7,
  minorTicks: 5,
  max: 10
};
var chart = [];
var chartl = [];
var optionsl = {
  title: 'Load',
  legend: {
    position: 'bottom'
  },
  series: {
    0: {
      color: '#33cc33'
    },
    1: {
      color: '#ffff66'
    },
    2: {
      color: '#cc0000'
    }
  },
  hAxis: {
    format: 'H:mm:ss',
    textPosition: 'in'
  },
  vAxis: {
    minValue: 0
  },
  pointSize: 5,
  chartArea: {
    width: "90%"
  },
};
var dataa = [];
var datal = [];

function swap_sh(id, t, d) {
  if (d == 1) {
    $('#' + id + '_' + t).slideDown();
    $('#' + id + t + '_btns').hide();
    $('#' + id + t + '_btnh').show();
  }
  if (d == 0) {
    $('#' + id + '_' + t).slideUp();
    $('#' + id + t + '_btns').show();
    $('#' + id + t + '_btnh').hide();
  }
}

function get_laod() {
  $.ajax({
    type: "POST",
    url: "poll_load.php",
    success: function(resp) {
      var d = jQuery.parseJSON(resp);
      $.each(d, function(i, v) {
        drawChart(i, v['one'], v['five'], v['fifteen']);
        drawline(i, v['one'], v['five'], v['fifteen']);
      });
      setTimeout(function() {
        get_laodd();
      }, 500);
    }
  });
}

function get_laodd() {
  $.ajax({
    type: "POST",
    url: "poll_load.php",
    success: function(resp) {
      var d = jQuery.parseJSON(resp);
      var alld = [];
      $.each(d, function(i, v) {
        //console.log(i);
        alld.push(v['one']);
        $("#" + i + "_h").html(v['one'] + "," + v['five'] + "," + v['fifteen']);
        redrawChart(i, v['one'], v['five'], v['fifteen']);
        redrawline(i, v['one'], v['five'], v['fifteen']);
      });
      redrawallChart(alld);
      setTimeout(function() {
        get_laodd();
      }, 30000);
    }
  });
}

function drawChart(id, a, b, c) {
  a = parseFloat(a);
  b = parseFloat(b);
  c = parseFloat(c);
  dataa[id] = [
    ['Label', 'Value'],
    ['Load', a],
    ['5 min', b],
    ['15 min', c]
  ]
  dataa[id] = google.visualization.arrayToDataTable(dataa[id]);
  //console.log(id + "_g");
  chart[id] = new google.visualization.Gauge(document.getElementById(id + "_g"));
  chart[id].draw(dataa[id], options);
}

function redrawChart(id, a, b, c) {
  a = parseFloat(a);
  b = parseFloat(b);
  c = parseFloat(c);
  dataa[id].setValue(0, 1, a);
  dataa[id].setValue(1, 1, b);
  dataa[id].setValue(2, 1, c);
  chart[id].draw(dataa[id], options);
}

function drawline(id, a, b, c) {
  a = parseFloat(a);
  b = parseFloat(b);
  c = parseFloat(c);
  var ddd = new Date();
  //console.log(ddz);
  var poss = [ddd, a, b, c];
  datal[id].addRow(poss);
  chartl[id] = new google.visualization.LineChart(document.getElementById(id + "_l"));
  chartl[id].draw(datal[id], optionsl);
}

function redrawline(id, a, b, c) {
  a = parseFloat(a);
  b = parseFloat(b);
  c = parseFloat(c);
  var ddd = new Date();
  var poss = [ddd, a, b, c];
  datal[id].addRow(poss);
  if (datal[id].getNumberOfRows() > 10) {
    datal[id].removeRow(0);
  }
  chartl[id].draw(datal[id], optionsl);
}

function redrawallChart(ar) {
  var id = "a181a603769c1f98ad927e7367c7aa51";
  var ddd = new Date();
  //console.log(ddz);
  var poss = [ddd];
  $.each(ar, function(i, v) {
    a = parseFloat(v);
    poss.push(a);
  });
  datal[id].addRow(poss);
  if (datal[id].getNumberOfRows() > 10) {
    datal[id].removeRow(0);
  }
  chartl[id].draw(datal[id], optionsl);
}