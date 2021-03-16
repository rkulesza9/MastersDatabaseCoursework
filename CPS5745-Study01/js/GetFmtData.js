var GetFmtData = 
{
	requestCityData : function(handle) {
		var file = "php/GetCityData.php";
		var data = 
		{
			getCityData_flag : 1
		};

		$.post(file, data, function(data, status){
			var json = JSON.parse(data);
			handle(json);
		});
	},

	id_table : "#cities_table",

	fx_table : function(cities) {
		var table = `<table class="table table-hover table-bordered">
						<thead>`;
							
   		for(var header in cities[0]){
   			if(header == "location"){
   				table += `<th>Latitude</th><th>Longitude</th>`;
   			}
   			else {
   				table += `<th>${header.replaceAll("_"," ")}</th>`;
   			}
		}

		table += `</thead><tbody>`;

	    for(var x = 0; x < cities.length; x++){
	    	table += `<tr>`;
	    	for(var col in cities[x]){
	    		if(col == "location"){
	    			var latlon = cities[x][col].split(",");
	    			var lat = latlon[0];
	    			var lon = latlon[1];

	    			table += `<td>${lat}</td><td>${lon}</td>`;
	    		}else{
	    			table += `<td>${cities[x][col]}</td>`;
	    		}
	    	}
	    	table += `</tr>`;
	    }

	    table += `</tbody></table>`;

		$(GetFmtData.id_table).append(table);
	},

	// jquery
	id_map : "#cities_map",

	fx_map : function(cities) {

		var data_arr = [];
		data_arr.push(['Lat', 'Long', 'Name']);

		for(var x = 0; x < cities.length; x++){
			city = cities[x];
			var desc = `<h3>${city.city}</h3>
						<table>
							<tr><th>median home value</th>
								<td>${city.median_home_value}</td>
							</tr>
							<tr><th>avg market monthly rent</th>
								<td>${city.avg_market_monthly_rent}</td>
							</tr>
							<tr><th>Homeownership rate</th>
								<td>${city.Homeownership_rate}</td>
							</tr>
							<tr><th>population</th>
								<td>${city.population}</td>
							</tr>
						</table>`;

			var latlon = cities[x]["location"].split(",");
			var lat = parseFloat(latlon[0]);
			var lon = parseFloat(latlon[1]);

			data_arr.push([lat, lon, desc]);
		}

		console.log(data_arr);
	    var data = google.visualization.arrayToDataTable(data_arr);

	    var map = new google.visualization.Map($(GetFmtData.id_map)[0]);
	    map.draw(data, {
	      showTooltip: true,
	      showInfoWindow: true
	    });

	}
};