function getFakeData(){
	return [["Something","Something2","Something3"],JSON.parse('[{"group":["A3","B7",""],"current":{"count":50}},{"group":["A1","B2",""],"current":{"count":40}},{"group":["A1","B1",""],"current":{"count":30}},{"group":["A2","B5",""],"current":{"count":30}},{"group":["A1","B3","C2"],"current":{"count":15}},{"group":["A3","B8",""],"current":{"count":15}},{"group":["A1","B3","C1"],"current":{"count":10}},{"group":["A2","B6",""],"current":{"count":10}},{"group":["ZS","BS",""],"current":{"count":50}},{"group":["ZS","BF",""],"current":{"count":50}}]')];

}

function getDataFromDB(){
	$.post("php/GetSunburstData.php", {
		GetSunburstData : "true",
		states : JSON.stringify(STATES_SELECTED),
		quant : QUANT

	}, function(data, status){
		var data_arr = JSON.parse(data);
		LABELS = data_arr[0];
		DATA = data_arr[1];
		DATA_LOADED = true;
		drawChart();
	});
}

function drawChart(){
	
    if(DATA_LOADED){
        var selected_path = [];

        var chart = sunburst(LABELS)
            .width(CHART_WIDTH)
            .height(CHART_HEIGHT);

        selected_path = controller(LABELS, DATA, 100, chart, selected_path);
        d3.selectAll("#controller").on("change", function change() {
                selected_path = controller(LABELS, selected_path, 100, chart, selected_path);
        });
    }  
}