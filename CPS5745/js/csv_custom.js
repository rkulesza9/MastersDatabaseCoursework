function parseCSV(data_csv){
	// make new line consistent
	data_csv = data_csv.replace("\r\n","\n"); //convert window new line to linux new line
	data_csv = data_csv.replace("\r", "\n"); //convert older mac new line to linux new line

	// parse rows
	var rows = data_csv.split("\n");
	var headers_arr = rows[0].split(",");

	var rows_arr = [];
	for(var x = 1; x < rows.length; x++){
		var rows_one = rows[x];
		if(rows_one.length > 0){
			rows_one = rows[x].split(",");
			rows_arr.push(rows_one);
		}
	}

	var json = {
		headers : headers_arr,
		data : rows_arr,
		columns : headers_arr.length,
		rows : rows_arr.length
	};

	json = isValidCSV(json);

	return json;
}

function isValidCSV(json){
	var inconsistentRows = false;

	for(var x = 0; x < json.rows; x++){
		if(json.data[x].length != json.columns){
			inconsistentRows = true;
			break;
		}
	}

	if(inconsistentRows || json.rows == 0){
		json.status = "Error";
		json.status_description = "Invalid CSV Formatting.";
	} else {
		json.status = "Success";
		json.status_description = "Valid CSV Formatting.";
	}

	return json;
}