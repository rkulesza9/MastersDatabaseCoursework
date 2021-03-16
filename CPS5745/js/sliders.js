function setAvgWagesSliderRange(){
	var minimum = getMinimumValue("AvgWages");
	var maximum = getMaximumValue("AvgWages");
	$("#AvgWagesSlider").attr("min", minimum);
	$("#AvgWagesSlider").attr("max", maximum);

	if(SAVED_SETTINGS["AvgWages"] == null){
		var startValue = getAverageValue("AvgWages");
		$("#AvgWagesSlider").val(startValue);
	} else {
		var startValue = SAVED_SETTINGS["AvgWages"];
		$("#AvgWagesSlider").val(startValue);
	}

}

function setEstPopSliderRange(){
	var minimum = getMinimumValue("EstimatedPopulation");
	var maximum = getMaximumValue("EstimatedPopulation");

	$("#EstPopSlider").attr("min", minimum);
	$("#EstPopSlider").attr("max", maximum);

	if(SAVED_SETTINGS["EstPop"] == null){
		var average = getAverageValue("EstimatedPopulation");
		$("#EstPopSlider").val(average);
	} else {
		var startValue = SAVED_SETTINGS["EstPop"];
		$("#EstPopSlider").val(startValue);
	}
}