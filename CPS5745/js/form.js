function select_state(id, label){
	var states = getDistinctValues(TABLE_HEADERS.indexOf("State"));

	var start = `<div class='input-group pb-2'>
		<div class=input-group-prepend'>
			<span class='input-group-text'>${label}</span>
		</div>
		<select class='custom-select' id='${id}'>`;
	var end = "</select></div>";
	var options = "";
	for(var x = 0; x < states.length; x++){
		var state = states[x];
		var option = `<option value='${x}'>${state}</option>`;
		options += option;
	}

	return start+options+end;
}

function button_primary(id, text){
	return `<div class='pb-2'><button type='button' id='${id}' class='btn btn-primary'> ${text} </button></div>`;
}