function setupClientModal(){
	$("#Client-js").html("");
	drawTableRow("#Client-js","name", navigator.appName);
	drawTableRow("#Client-js","version", navigator.appVersion);
	drawTableRow("#Client-js","type");
	drawTableRow("#Client-js","cookie-enabled", navigator.cookieEnabled);
	drawTableRow("#Client-js","Java-enabled", navigator.javaEnabled());
}

function setupUserInfoModal(){
	$("#UserInfo-js").html("");

	if(getCookie("uid") != "") {
		drawTableRow("#UserInfo-js","uid", getCookie("uid"));
		drawTableRow("#UserInfo-js","login", getCookie("username"));
		drawTableRow("#UserInfo-js","name", getCookie("name").replaceAll("+"," "));
		drawTableRow("#UserInfo-js","gender", getCookie("gender"));
	} else {
		drawTableRow("#UserInfo-js","You Are Not Logged In", "");
	}
}

function setupSimpleModal(title, msg){
	$("#SimpleModal-js").html("");
	$("#SimpleModalTitle").html(title);

	drawTableRow("#SimpleModal-js",msg, "");
}

function setupSimpleLoadingModal(title){
	$("#SimpleModal-js").html(`
		<div class="spinner-border text-primary" role="status">
		  <span class="sr-only">Loading...</span>
		</div>		
	`);
	$("#SimpleModalTitle").html(title);

	$("#SimpleModal").modal();
}

function setupEmailModal(){
	$("#SendEmailSubject").val(`${getCookie("username")}'s DV preference`);

	loadSavedSettings(function(){
		var avgWages = getRows(["AvgWages"], ["NULL", "NaN", "", null], isNumber=true);
		avgWages = colToFloat(avgWages,0);
		var avg = avgColumn(0, avgWages);

		var content = "";
		content += `Settings:
		uid: ${SAVED_SETTINGS.uid}
		login: ${SAVED_SETTINGS.login}
		AvgWages (slider): ${SAVED_SETTINGS.AvgWages}
		EstimatedPopulation (slider): ${SAVED_SETTINGS.EstPop}
		datetime: ${SAVED_SETTINGS.datetime}

		Average Value of AvgWages: ${avg}`;
		
		$("#SendEmailContent").val(content);
	});
}