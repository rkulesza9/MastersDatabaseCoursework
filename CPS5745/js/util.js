function drawTableRow(id, header, value){
	$(id).append(`<tr><th>${header}</th><td>${value}</td></tr>`);
}


function countDistinctRows(rows, dindex){
	var counts = {};
	for(var x = 0; x < rows.length; x++){
		var row = rows[x];
		if(row[dindex] in counts){
			counts[row[dindex]]+=1;
		} else {
			counts[row[dindex]] = 1;
		}
	}
	return counts;
}

function getRows(columns, ignore, isNumber=false){
	var rows = [];
	for(var a=0; a < TABLE_DATA.length; a++){
		var row = [];
		var rowFromTable = TABLE_DATA[a];
		var rowComplete = true;
		for(var b=0; b < rowFromTable.length; b++){
			var header = TABLE_HEADERS[b];
			if(columns.includes(header)){
				if(!ignore.includes(rowFromTable[b])){
					if(isNumber && !isNaN(parseFloat(rowFromTable[b]))){
						row.push(rowFromTable[b]);
					} 
					if(!isNumber){
						row.push(rowFromTable[b]);
					}
				} else {
					rowComplete = false;
				}
			}
		}
		if(rowComplete){
			rows.push(row);
			//console.log(`row has ${row.length} values.`);
		}
	}
	return rows;
}

function colToFloat(data, col){
	for(var x = 0; x < data.length; x++){
		data[x][col] = parseFloat(data[x][col]);
	}
	return data;
}

function groupAndAvg(dcolumn, gcolumn, data, rule=function(val){ return true; }){
	// 1. get unique set of columns
	// 2. get count for each unique column
	var cdr = countDistinctRows(data,dcolumn);
	var dcolumn_vals = Object.keys(cdr);

	// 3. get adjacent sum for each unique column (n)
	var dval_sums = {}
	for(var x = 0; x < data.length; x++){
		var row = data[x];
		var dcol = row[dcolumn];
		var gcol = row[gcolumn];
		if(rule(gcol)){
			if(dcol in dval_sums){
				dval_sums[dcol] += gcol;
			} else {
				dval_sums[dcol] = gcol;
			}
		} else {
			cdr[dcol] = cdr[dcol] - 1;
		}
	}

	// 3. create new array: distinct col, sum/count
	var newarray = [];
	for(var key in dval_sums){
		var row = [key, dval_sums[key]/cdr[key]];
		newarray.push(row);
	}

	return newarray;
}

function avgColumn(dcolumn, data, rule=function(val){ return true; }){

	var sum = 0;
	var count = data.length;
	for(var x = 0; x < data.length; x++){
		if(rule(data[dcolumn])){
			sum += data[dcolumn];
		} else {
			count = count - 1;
		}
	}

	return sum / count;
}

// convert json table data to array of table data (1 header row, X data rows) [CRASHES]
function jsonToArray(json){
	// first row = keys
	var data = [];
	var keys = Object.keys(json);
	data.push(keys);

	//rest of the rows
	for(var x = 0; x < data.length; x++){
		var row_json = data[x];
		var row_arr = [];
		for(var k = 0; k < keys.length; k++){
			var key = keys[k];
			var column_data = row_json[key];
			row_arr.push(column_data);
		}
		data.push(row_arr);
	}

	//return 
	return data;
}


// loads data array into globals
function LoadData(data, showOutlierRangeToUser=false){
	//console.log(data.length);
	var head = data[0];

	data.shift();

	TABLE_HEADERS = head;
	TABLE_DATA = data;

	TABLE_DATA_FMT = [];
	for(var x = 0; x < TABLE_DATA.length; x++){
		TABLE_DATA_FMT.push(Array.from(TABLE_DATA[x]));
	}

	formatColumnByAvg("AvgWages","red");
	formatColumnByAvg("EstimatedPopulation", "green");

	formatAndDrawTable(showOutlierRangeToUser = showOutlierRangeToUser);
	DATA_LOADED = "True";

	enableButton("#SaveSettings");
	enableButton("#SaveFilteredResults");
	if(SAVED_SETTINGS["uid"] == null){
		loadSavedSettings();
	}
}

function enableButton(id){
	var uid = getCookie("uid");

	if(uid != "" && DATA_LOADED == "True"){	
		var class_value = $(id).attr("class");
		class_value = class_value.replace("disabled","");
		$(id).attr("class",class_value);
	}
}

function disableButton(id){
		var class_value = $("#SaveSettings").attr("class");
		if(class_value.includes("disabled")){

		} else {
			class_value = class_value += "disabled";
		}
		$(id).attr("class",class_value);
}


// if AvgWages Cell has value > than average column - number should be red otherwise black
/* 1. calculate average of AvgWages column
   2. replace occurrences of AvgWage where AvgWage > average with <span style='color:red;'></span>
   3. display table
*/
function getColumnAvg(col){
	var data = getRows([col],["NULL", "NaN", "", null]);

	var sum = 0;
	var count = data.length;
	var callback = function(value){ sum += parseFloat(value); };
	data.forEach(callback);

	return sum / count;
}

function formatColumnByAvg(col, color){
	var avgWages_index = -1;
	for(var x = 0; x < TABLE_HEADERS.length; x++){
		var header = TABLE_HEADERS[x];
		if(header == col){
			avgWages_index = x;
		}
	}

	var avgWage_avg = getColumnAvg(col);

	for(var row = 0; row < TABLE_DATA.length; row++){
		var row_data = TABLE_DATA[row];
		var row_avgWages_value = row_data[avgWages_index];
		
		if(parseFloat(row_avgWages_value) > parseFloat(avgWage_avg)){
			var row_avgWages_valueFmt = `<span style='color:${color};'><b>${row_avgWages_value}</b></span>`;
			TABLE_DATA_FMT[row][avgWages_index] = row_avgWages_valueFmt;
		}
	}
}

function resetFormatting(){
	TABLE_DATA_FMT = [];
	for(var x = 0; x < TABLE_DATA.length; x++){
		TABLE_DATA_FMT.push(Array.from(TABLE_DATA[x]));
	}
}
function formatColumnByValue(value, col, color){
	var avgWages_index = -1;
	for(var x = 0; x < TABLE_HEADERS.length; x++){
		var header = TABLE_HEADERS[x];
		if(header == col){
			avgWages_index = x;
		}
	}

	for(var row = 0; row < TABLE_DATA.length; row++){
		var row_data = TABLE_DATA[row];
		var row_avgWages_value = row_data[avgWages_index];
		
		if(parseFloat(row_avgWages_value) > parseFloat(value)){
			var row_avgWages_valueFmt = `<span style='color:${color};'><b>${row_avgWages_value}</b></span>`;
			TABLE_DATA_FMT[row][avgWages_index] = row_avgWages_valueFmt;
		}
	}
}

function filterRows(value, col_str, headers, data){
	var avgWages_index = -1;
	for(var x = 0; x < headers.length; x++){
		var header = headers[x];
		if(header == col_str){
			avgWages_index = x;
		}
	}

	var filtered_rows = [];
	for(var row = 0; row < data.length; row++){
		var row_data = data[row];
		var row_avgWages_value = row_data[avgWages_index];
		if(parseFloat(row_avgWages_value) > parseFloat(value)){
			filtered_rows.push(row_data);
		}
	}

	return filtered_rows;	
}

function groupAndSum(dcolumn, gcolumn, data, rule=function(val){ return true; }){
	// 1. get unique set of columns
	// 2. get count for each unique column
	// 3. get adjacent sum for each unique column (n)
	var dval_sums = {}
	for(var x = 0; x < data.length; x++){
		var row = data[x];
		var dcol = row[dcolumn];
		var gcol = row[gcolumn];
		if(rule(gcol)){
			if(dcol in dval_sums){
				dval_sums[dcol] += gcol;
			} else {
				dval_sums[dcol] = gcol;
			}
		}
	}

	var returndata = [];
	for(var key in dval_sums){
		returndata.push([key, dval_sums[key]]);
	}
	return returndata;
}

function getDistinctValues(column){
	var result = [];
	for(var x = 0; x < TABLE_DATA.length; x++){
		var row = TABLE_DATA[x];
		if(result.includes(row[column])){

		} else {
			result.push(row[column]);
		}
	}

	return result;
}

function combineOnFirstColumn(data1, data2){
	var dict1 = {};
	var dict2 = {};
	var dict3 = {};

	for(var x = 0; x < data1.length; x++){
		dict1[data1[x][0]] = data1[x].slice(1,data1[x].length);
	}
	for(var x = 0; x < data2.length; x++){
		dict2[data2[x][0]] = data2[x].slice(1,data2[x].length);
	}

	var keys1 = Object.keys(dict1);
	var keys2 = Object.keys(dict2);

	var inBoth = arrays_inBoth(keys1,keys2);
	var in1Not2 = arrays_in1Not2(keys1,keys2);
	var in2Not1 = arrays_in1Not2(keys2,keys1);

	for(var x = 0; x < inBoth.length; x++){
		dict3[inBoth[x]] = dict1[inBoth[x]].concat(dict2[inBoth[x]]);
	}
	for(var x = 0; x < in1Not2.length; x++){
		dict3[in1Not2[x]] = dict1[in1Not2[x]].concat([null]);
	}
	for(var x = 0; x < in2Not1.length; x++){
		dict3[in2Not1[x]] = [null].concat(dict2[in2Not1[x]]);
	}

	var result = [];
	var allkeys = Object.keys(dict3);
	for(var x = 0; x < allkeys.length; x++){
		result.push([allkeys[x]].concat(dict3[allkeys[x]]));
	}

	return result;
}

function arrays_in1Not2(arr1, arr2){
	var arr3 = [];
	for(var x = 0; x < arr1.length; x++){
		if(!arr2.includes(arr1[x])){
			arr3.push(arr1[x]);
		}
	}
	return arr3;
}

function arrays_inBoth(arr1, arr2){
	var arr3 = [];
	for(var x = 0; x < arr1.length; x++){
		if(arr2.includes(arr1[x])){
			arr3.push(arr1[x]);
		}
	}
	return arr3;
}

function sum(arr, index){
	var sum = 0;
	for(var x = 0; x < arr.length; x++){
		sum += arr[x][index];
	}

	return sum;
}

function normalize(arr, index, divideby=1000, roundTo=1000, fx=function(value){ return Math.round(roundTo * value / divideby) / roundTo; }){
	for(var x = 0; x < arr.length; x++){
		var row = arr[x];
		row[index] = fx(row[index]);
	}

	return arr;
}

function getMinimumValue(column){
	var data = getRows([column],["NULL", "NaN", "", null]);

	var min = parseFloat(data[0][0]);
	for(var x = 0; x < data.length; x++){
		var row = data[x];
		if(parseFloat(row[0]) < min) min = parseFloat(row[0]);
	}

	return min;
}
function getMaximumValue(column){
	var data = getRows([column],["NULL", "NaN", "", null]);

	var max = parseFloat(data[0][0]);
	for(var x = 0; x < data.length; x++){
		var row = data[x];
		if(parseFloat(row[0]) > max) max = parseFloat(row[0]);
	}

	return max;
}
function getAverageValue(column){
	var data = getRows([column],["NULL", "NaN", ""], null);

	var sum = 0;
	for(var x = 0; x < data.length; x++){
		var row = data[x];
		sum += parseFloat(row[0]);
	}

	//console.log(sum);

	return sum / data.length;
}

// this needs to  work with data arrays
function getOutlierRange(rows) {
	var someArray = [];

	for(var row = 0; row < rows.length; row++){
		var row_data = rows[row];
		for(var x = 0; x < row_data.length; x++){
			var col_value = row_data[x];

			if(!isNaN(parseFloat(col_value))) someArray.push(parseFloat(col_value));
		}
	}

    // Copy the values, rather than operating on references to existing values
    var values = someArray;

    // Then sort
    values.sort( function(a, b) {

    		if(isNaN(a) || isNaN(b)) //console.log("not a number: "+a+" "+b);
            return a - b;
         });

    /* Then find a generous IQR. This is generous because if (values.length / 4) 
     * is not an int, then really you should average the two elements on either 
     * side to find q1.
     */     
    var q1 = values[Math.floor((values.length / 4))];
    // Likewise for q3. 
    var q3 = values[Math.ceil((values.length * (3 / 4)))];
    var iqr = q3 - q1;

    // Then find min and max values
    var maxValue = q3 + iqr*1.5;
    var minValue = q1 - iqr*1.5;

    // Then return
    return [minValue, maxValue];
}

function highlightOutlierRows(createAlert=false){

	var rows = getRows(["AvgWages"],["NULL", "NaN", "", null], isNumber=true);
	var range_outlier = getOutlierRange(rows);

	if(createAlert) alertSuccess(`<b>Outlier Range:</b> outlier < ${range_outlier[0]} or outlier > ${range_outlier[1]}`, '#MessageArea');

	for(var x = 0; x < TABLE_DATA_FMT.length; x++){
		var row = TABLE_DATA_FMT[x];
		var cindex = getColIndex("AvgWages");

		if(parseFloat(TABLE_DATA[x][cindex]) < parseFloat(range_outlier[0]) || parseFloat(TABLE_DATA[x][cindex]) > parseFloat(range_outlier[1])){

			for(var y = 0; y < row.length; y++){
				TABLE_DATA_FMT[x][y] = `<span class='highlight-parent-yellow'>${TABLE_DATA_FMT[x][y]}</span>`;

			}
		}

	}
}

function formatTableDataBeforeDraw(createAlert=false){
	resetFormatting();
	formatColumnByValue($("#AvgWagesSlider").val(), "AvgWages", "red");
	formatColumnByValue($("#EstPopSlider").val(), "EstimatedPopulation", "green");
	highlightOutlierRows(createAlert = createAlert);
}

function getColIndex(col){
	var avgWages_index = -1;
	for(var x = 0; x < TABLE_HEADERS.length; x++){
		var header = TABLE_HEADERS[x];
		if(header == col){
			avgWages_index = x;
		}
	}
 
 	return avgWages_index;
}

function getNaNRows(columns){
	var rows = [];
	for(var a=0; a < TABLE_DATA.length; a++){
		var row = [];
		var rowFromTable = TABLE_DATA[a];
		var rowComplete = true;
		for(var b=0; b < rowFromTable.length; b++){
			var header = TABLE_HEADERS[b];
			if(columns.includes(header)){
				if(isNaN(parseFloat(rowFromTable[header]))){
					row.push(rowFromTable[b]);
				} else {
					rowComplete = false;
				}
			}
		}
		if(rowComplete){
			rows.push(row);
			//console.log(`row has ${row.length} values.`);
		}
	}
	return rows;
}

function removeValuesInRange(data, columns, range_start="any", range_end="any"){
	for(var x = 0; x < data.length; x++){
		var row = data[x];
		for(var a = 0; a < columns.length; a++){
			var col = columns[a];

			var value = row[col];
			if(range_start == "any" && range_end == "any"){
				// is within range
			} else if(range_start == "any" && value <= range_end){
				// is within range
			} else if(range_end == "any" && value >= range_start) {
				// is within range
			} else if(range_start <= value && value <= range_end) {
				// is within range
			} else {
				data[x][col] = null;
			}

		}
	}
}

function updateChartHeight(chartID, options, height=500){
	options["width"] = $("#"+chartID).css("width");
	options["height"] = height;
}

function sigmoid(z, k=1){
	return 1 / (1 + Math.exp(-z/k));
}

function loadSavedSettings(fx = function(){}){
	$.post("php/load_settings.php", {
		uid : getCookie("uid")
	}, function(data, status){
		SAVED_SETTINGS = JSON.parse(data);
		//console.log(SAVED_SETTINGS);

		setAvgWagesSliderRange();
		setEstPopSliderRange();

		fx();
	});
}