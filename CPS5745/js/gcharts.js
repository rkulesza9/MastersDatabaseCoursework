function drawTable(columns, rows){
    google.charts.load('current', {'packages':['table']});
    google.charts.setOnLoadCallback(function(){
      var data = new google.visualization.DataTable();
      
      columns.forEach(function(col){
        data.addColumn('string',col);

      });

      for(var row = 0; row < rows.length; row++){
        for ( var col = 0; col < rows[row].length; col++){
          rows[row][col] = `${rows[row][col]}`;
        }
      }
      
      data.addRows(rows);

      var table = new google.visualization.Table(document.getElementById('table_div'));

      google.visualization.events.addListener(table, 'ready', onTableReadyPage);
      google.visualization.events.addListener(table, 'page', onTableReadyPage);

      table.draw(data, {showRowNumber: true, allowHtml: true,  page: 'enable', pageSize: 6});

      //sliders
      //setAvgWagesSliderRange();
    });
}

function formatAndDrawTable(showOutlierRangeToUser = false){

  formatTableDataBeforeDraw(createAlert = showOutlierRangeToUser);
  drawTable(TABLE_HEADERS, TABLE_DATA_FMT);
}

function drawCityAvgWageLineChart(id="graph1"){
  $("#"+id).html("");
  
  var X = ["State", "AvgWages"];
  var Y = getRows(X, ["NULL", "NaN", "", null]);
  Y = colToFloat(Y, 1);
  Y = groupAndAvg(0, 1, Y);

  google.charts.load('current', {'packages' : ['line']});
  google.charts.setOnLoadCallback(function(){
    var data = new google.visualization.DataTable();
    data.addColumn('string', "State");
    data.addColumn('number', 'AvgWages');

    data.addRows(Y);
    var options = {
      chart : {
        title : "Average Wages",
        subtitle: "by state"
      }
    };

    updateChartHeight(id, options);
  
  var chart = new google.charts.Line(document.getElementById(id));
    chart.draw(data, google.charts.Line.convertOptions(options));
  });
  
}

function drawCityAvgWageBarChart(id="graph1"){
  $("#"+id).html("");
  
  var X = ["State", "AvgWages"];
  var Y = getRows(X, ["NULL", "NaN", "", null]);
  Y = colToFloat(Y, 1);
  Y = groupAndAvg(0, 1, Y);

  google.charts.load('current', {'packages' : ['Bar']});
  google.charts.setOnLoadCallback(function(){
    var data = new google.visualization.DataTable();
    data.addColumn('string', "State");
    data.addColumn('number', 'AvgWages');

    data.addRows(Y);
    var options = {
      chart : {
        title : "Average Wages",
        subtitle: "by state"
      }
    };

    updateChartHeight(id, options);
  
  var chart = new google.charts.Bar(document.getElementById(id));
    chart.draw(data, google.charts.Bar.convertOptions(options));
  });
}

function drawCityEstPopLineChart(id="graph1"){
  $("#"+id).html("");
  
  var X = ["State", "EstimatedPopulation"];
  var Y = getRows(X, ["NULL", "NaN", "", null]);

  Y = colToFloat(Y, 1);
  Y = groupAndSum(0, 1, Y);

  google.charts.load('current', {'packages' : ['line']});
  google.charts.setOnLoadCallback(function(){
    var data = new google.visualization.DataTable();
    data.addColumn('string', "State");
    data.addColumn('number', 'Est. Pop.');

    data.addRows(Y);
    var options = {
      chart : {
        title : "Estimated Population",
        subtitle: "by State"
      }
    };

    updateChartHeight(id, options);
  
  var chart = new google.charts.Line(document.getElementById(id));
    chart.draw(data, google.charts.Line.convertOptions(options));
  });
  
}

function drawCityEstPopBarChart(id="graph1"){
  $("#"+id).html("");
  
  var X = ["State", "EstimatedPopulation"];
  var Y = getRows(X, ["NULL", "NaN", "", null]);

  Y = colToFloat(Y, 1);
  Y = groupAndSum(0, 1, Y);

  google.charts.load('current', {'packages' : ['bar']});
  google.charts.setOnLoadCallback(function(){
    var data = new google.visualization.DataTable();
    data.addColumn('string', "State");
    data.addColumn('number','Est. Pop.');

    data.addRows(Y);
    var options = {
      chart : {
        title : "Estimated Population",
        subtitle: "by state"
      }
    };

    updateChartHeight(id, options);
  
  var chart = new google.charts.Bar(document.getElementById(id));
    chart.draw(data, google.charts.Bar.convertOptions(options));
  });
}

function drawStateBarChart(id="graph1"){
  $("#"+id).html("");
  
  var data = countDistinctRows(getRows(["State"],["NULL","NaN", "", null]), 0);

  var Y = [];
  for(var key in data){
    Y.push([key, data[key]]);;
  }

  google.charts.load('current', {'packages' : ['bar']});
  google.charts.setOnLoadCallback(function(){
    var data = new google.visualization.DataTable();
    data.addColumn('string', "State");
    data.addColumn('number','Count');

    data.addRows(Y);
    var options = {
      chart : {
        title : "State Count",
        subtitle: "by number of records"
      }
    };

    updateChartHeight(id, options);
  
  var chart = new google.charts.Bar(document.getElementById(id));
    chart.draw(data, google.charts.Bar.convertOptions(options));
  });
}

function drawStatePieChart(id="graph1"){
  $("#"+id).html("");
  
  var data = countDistinctRows(getRows(["State"],["NULL","NaN", "", null]), 0);

  var Y = [["State", "Count"]];
  for(var key in data){
    Y.push([key, data[key]]);;
  }

  google.charts.load('current', {'packages' : ['corechart']});
  google.charts.setOnLoadCallback(function(){
 var data = google.visualization.arrayToDataTable(Y);

        var options = {
          title: 'State by Count',
          pieHole: 0.4,
        };

        updateChartHeight(id, options);

        var chart = new google.visualization.PieChart(document.getElementById(id));
        chart.draw(data, options);
  });
}


// PROJECT 2 START
function drawNewChartGeo(id="graph1"){
  $("#"+id).html("");
  
  // show count, AvgWages, EstimatedPopulation on each state
  var stateCountDict = countDistinctRows(getRows(["State"],["NULL","NaN", "", null]), 0); 
  var stateCount = [];
  for(var key in stateCountDict){
    stateCount.push([key, stateCountDict[key]]);
  }
  
  var stateEstPop= getRows(["State", "EstimatedPopulation"], ["NULL", "NaN", "", null]);
  stateEstPop = colToFloat(stateEstPop, 1);
  stateEstPop = groupAndSum(0, 1, stateEstPop);

  var stateAvgWages = getRows(["State", "AvgWages"], ["NULL", "NaN", "", null]);
  stateAvgWages = colToFloat(stateAvgWages, 1);
  stateAvgWages = groupAndAvg(0, 1, stateAvgWages);

  var chartData = [];
  // check which data should be selected
  if($('#customRadio1').is(':checked')){
    chartData.push(["State", "AvgWages"]);
    
    for(var x = 0; x < stateAvgWages.length; x++){
      chartData.push(stateAvgWages[x]);
    }
  } else if($('#customRadio2').is(':checked')) {
    chartData.push(["State", "EstPop"]);
    
    //console.log(stateEstPop);
    for(var x = 0; x < stateEstPop.length; x++){
      chartData.push(stateEstPop[x]);
    }

  } else if($('#customRadio3').is(':checked')) {
    chartData.push(["State", "Count"]);
    
    for(var x = 0; x < stateCount.length; x++){
      chartData.push(stateCount[x]);
    }
  }

  //console.log(chartData);

  
  google.charts.load('current', {
    'packages':['geochart'],
    'mapsApiKey': 'AIzaSyBy2RSPNACOHZZh54r-MZas4XeRglKf_IA'
  });
  google.charts.setOnLoadCallback(function(){
    var data = google.visualization.arrayToDataTable(chartData);
    var options = {
      region: 'US',
      displayMode: 'regions',
      resolution: 'provinces'
    };
    var chart = new google.visualization.GeoChart(document.getElementById(id));
    chart.draw(data, options);
  }); 

}

function drawNewChartRadar(id="graph2"){
  $("#"+id).html("");

  var stateCountDict = countDistinctRows(getRows(["State"],["NULL","NaN", "", null]), 0); 
  var stateCount = [];
  for(var key in stateCountDict){
    stateCount.push([key, stateCountDict[key]]);
  }

  var stateCount_sum = sum(stateCount, 1);
  var stateCount = normalize(stateCount, 1, stateCount_sum);
  
  var stateEstPop= getRows(["State", "EstimatedPopulation"], ["NULL", "NaN", "", null]);
  stateEstPop = colToFloat(stateEstPop, 1);
  stateEstPop = groupAndSum(0, 1, stateEstPop);

  var stateEstPop_sum = sum(stateEstPop, 1);
  var stateEstPop = normalize(stateEstPop, 1, stateEstPop_sum);
  
  var stateAvgWages = getRows(["State", "AvgWages"], ["NULL", "NaN", "", null]);
  stateAvgWages = colToFloat(stateAvgWages, 1);
  stateAvgWages = groupAndAvg(0, 1, stateAvgWages);
  
  var stateAvgWages_sum = sum(stateAvgWages, 1);
  var stateAvgWages = normalize(stateAvgWages, 1, stateAvgWages_sum);

  var stateTaxReturnsFiled = getRows(["State", "TaxReturnsFiled"], ["NULL", "NaN", "", null]);
  stateTaxReturnsFiled = colToFloat(stateTaxReturnsFiled, 1);
  stateTaxReturnsFiled = groupAndSum(0, 1, stateTaxReturnsFiled);

  var stateTaxReturnsFiled_sum = sum(stateTaxReturnsFiled, 1);
  var stateTaxReturnsFiled = normalize(stateTaxReturnsFiled, 1, stateTaxReturnsFiled_sum);

  var headers = ["x", "Count", "Est. Population", "Avg Wages", "Tax Returns Filed"];

  //i need to combine these datasets on same state
  var data = combineOnFirstColumn(stateCount, stateEstPop);
  data = combineOnFirstColumn(data, stateAvgWages);
  data = combineOnFirstColumn(data, stateTaxReturnsFiled);

  var cols = [headers].concat(data);
  //console.log(cols);

  var chart = bb.generate({
    data: {
      x: "x",
      columns: cols,
      type: "radar", // for ESM specify as: radar()
      labels: true
    },
    radar: {
      axis: {
        max: 0.3
      },
      level: {
        depth: 4
      },
      direction: {
        clockwise: true
      }
    },
    bindto: "#" + id
  });


}

function drawAnalyticsLineChart(id="graph4"){
  $("#"+id).html("");
  
  var X = ["State", "EstimatedPopulation", "AvgWages"];
  var Y = getRows(X, ["NULL", "NaN", "", null]);

  Y = colToFloat(Y, 1);
  Y = colToFloat(Y, 2);

  // only grab red/green data
  var estPop = groupAndSum(0, 1, Y, rule=function(val){
    return val >= $("#EstPopSlider").val();
  });

  //var estPop_sum = sum(estPop, 1);
  //estPop = normalize(estPop, 1, fx=function(val){ //console.log(sigmoid(val));  return sigmoid(val, k=estPop_sum); });

  var avgWages = groupAndAvg(0,2,Y,rule=function(val){
    return val >= $("#AvgWagesSlider").val();
  });

  //var avgWages_sum = sum(avgWages, 1);
  //avgWages = normalize(avgWages, 1, fx=function(val){ //console.log(sigmoid(val)); return sigmoid(val); });
  // ($("#AvgWagesSlider").val(), "AvgWages", "red");
  // formatColumnByValue($("#EstPopSlider").val(),
  Y = combineOnFirstColumn(estPop, avgWages);

  google.charts.load('current', {'packages' : ['line']});
  google.charts.setOnLoadCallback(function(){
    var data = new google.visualization.DataTable();
    data.addColumn('string', "State");
    data.addColumn('number', 'Estimated Population');
    data.addColumn('number', 'Average Wages')

    data.addRows(Y);
    var options = {
      chart : {
        title : "Estimated Population / Average Wages",
        subtitle: "by State"
      },

      colors: ['green', 'red']
    };

    updateChartHeight(id, options);
  
  var chart = new google.charts.Line(document.getElementById(id));
    chart.draw(data, google.charts.Line.convertOptions(options));
  });
}