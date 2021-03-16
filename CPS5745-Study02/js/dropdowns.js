function createStateCheckboxList(){

	$.post("php/GetStates.php", {

	}, function(data, status){
		// parse json
		var json = JSON.parse(data);
		console.log(json);

		// populate select(id) with options
		for(var x = 0; x < json.length; x++){
			var inline = "inline";

			createOption("state-select", json[x], json[x], inline, function(){
				//get if selected > add to STATES_SELECTED

				// i need to use name, i need to look through each and do this for each one (check if state exists before inserting / removing) 
				var checkboxes = $(`[name=statecheckboxes]`);
				for(var x2 = 0; x2 < checkboxes.length; x2++){
					var checkbox = checkboxes[x2];
					if($(checkbox).is(":checked")){
						if(!STATES_SELECTED.includes($(checkbox).val())){
							STATES_SELECTED.push($(checkbox).val());
						}
					} else {
						var index = STATES_SELECTED.indexOf($(checkbox).val());
						STATES_SELECTED.splice(index,index + 1);
					}
				}
				//get if unselected > rmv from STATES_SELECTED

				getDataFromDB();
			});
		}
	});
}

function createQuantSelect(){
	id = "#quant-select";

	$.post("php/getQuantityColumns.php",{

	}, function(data, status){
		// parse json
		var json = JSON.parse(data);

		// populate select(id) with options
		var options = "";
		for(var x = 0; x < json.length; x ++){
			var option = "";
			if(json[x] == QUANT){
				option = `<option value="${json[x]}" selected>${json[x]}</option>`

			} else {
				option = `<option value="${json[x]}">${json[x]}</option>`
			}
			options += option;
		}

		$(id).append(`
			<select class='custom-select' id='quant-select2' >
				${options}
			</select>`);

		$(id).change(function(){
			var dropdown = $("#quant-select2");
			var children = dropdown.children();
			for(var x  = 0; x < children.length; x++){
				var child = $(children[x]);
				if(child.is(":selected")){
					QUANT = child.val();
				}
			}

			getDataFromDB();
		});
	});
}

function createOption(id, value, label, inline, onChange){
	$("#"+id).append(`<div class="form-check-${inline}">
  <input class="form-check-input" type="checkbox" name="statecheckboxes" value="${value}" id="${value}">
  <label class="form-check-label" for="${value}">
    ${label}
  </label>
</div>`);
	$(`#${value}`).change(onChange);
}

// <input class='form-check-input' type='checkbox' value='' id='asdf'>
// <label class="form-check-label" for="asdf">
// 	NJ
// </label>