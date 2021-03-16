/* ------------- NAV > VIEW ------------- */
function clickLine(){
	var dataLoaded = DATA_LOADED;
	var selChart = $("input[name=customRadio]:checked").val();
	if(dataLoaded == "True"){
		if(selChart == "AW"){
			// Y: AvgWages , X: City
		drawCityAvgWageLineChart();
		}else if(selChart == "EP"){
			// Y: Population , X: City
			drawCityEstPopLineChart();

		} else {
			// $("#UnsupportedGraphType").modal();
			alertDanger("<strong>Error:</strong> Graph Type Not Allowed For Data Selected", "#graph1");
		}
	} else {
		// $("#DataNotLoaded").modal();
		alertDanger("<strong>Error:</strong> Please Load Data And Select From Data Selection Area First","#graph1");
	}
}
function clickPie(){
	var dataLoaded = DATA_LOADED;
	var selChart = $("input[name=customRadio]:checked").val();
	if(dataLoaded == "True"){
		if(selChart == "S"){
			// ???
			drawStatePieChart();
		} else {
			// $("#UnsupportedGraphType").modal();
			alertDanger("<strong>Error:</strong> Graph Type Not Allowed For Data Selected", "#graph1");
		}
	} else {
		// $("#DataNotLoaded").modal();
		alertDanger("<strong>Error:</strong> Please Load Data And Select From Data Selection Area First","#graph1");
		
	}
}
function clickBar(){
	var dataLoaded = DATA_LOADED;
	var selChart = $("input[name=customRadio]:checked").val();
	if(dataLoaded == "True"){
		if(selChart == "AW"){
			// Y: AvgWages , X: City
		drawCityAvgWageBarChart();
		}else if(selChart == "EP"){
			// Y: Population , X: City
		drawCityEstPopBarChart()
		} else if(selChart == "S"){
			// ???
			drawStateBarChart();
		} else {
			// $("#UnsupportedGraphType").modal();
			alertDanger("<strong>Error:</strong> Graph Type Not Allowed For Data Selected", "#graph1");
		}
	} else {
		// $("#DataNotLoaded").modal();
		alertDanger("<strong>Error:</strong> Please Load Data And Select From Data Selection Area First","#graph1");
		
	}
}
function clickMap(){
	var dataLoaded = DATA_LOADED;
	var selChart = $("input[name=customRadio]:checked").val();
	if(dataLoaded == "True"){
		// $("#UnsupportedGraphType").modal();
		alertDanger("<strong>Error:</strong> Graph Type Not Allowed For Data Selected", "#graph1");
	} else {
		// $("#DataNotLoaded").modal();
		alertDanger("<strong>Error:</strong> Please Load Data And Select From Data Selection Area First","#graph1");
		
	}
}

/* ------------- NAV > FILE ------------- */
function clickExitButton(){
	$.post("php/logout_from_db.php",
	{
		submit: "TRUE"
	},
	function(data,status){
		window.location.reload();
	});
}


function clickLogout(){
	var messageHTML = "";

	messageHTML = `<div class="row">
			<div class="col-md-12">
				<div class="alert alert-success alert-dismissible fade show" role="alert">
      			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		    		<span aria-hidden="true">&times;</span>
		  		</button>
		  		<strong>Logged Out</strong> See you again next time!

      		</div>
			</div>
		</div>`;

	$("#MessageArea").append(messageHTML);

	//actually log out
	setCookie("uid", "", -1);
	setCookie("username", "", -1);
	setCookie("name", "", -1);
	setCookie("gender", "", -1);

	disableSaveSettingsButton();
}


function clickLogin(){
	$.post("php/login_to_db.php", 
	{
		username: $("#db_username").val(),
		password: $("#db_password").val(),
		submit: "login_to_db_submit"
	},
	function(data,status){
		// alert(data);
		var json = JSON.parse(data);
		var messageHTML = "";

		if( json.status == "failure" ){

		messageHTML = `<div class="row">
  			<div class="col-md-12">
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
          			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			    		<span aria-hidden="true">&times;</span>
			  		</button>
			  		<strong>${json.error}</strong> ${json.error_description}

          		</div>
  			</div>
  		</div>`;

		} else if( json.status == "success"){

			messageHTML = `<div class="row">
      			<div class="col-md-12">
  					<div class="alert alert-success alert-dismissible fade show" role="alert">
	          			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				    		<span aria-hidden="true">&times;</span>
				  		</button>
				  		<strong>Login Successful</strong> Welcome, ${json.name}!

	          		</div>
      			</div>
      		</div>`;
		}

		$("#MessageArea").append(messageHTML);

		enableButton("#SaveSettings");
		enableButton("#SaveFilteredResults");

		//load settings
		loadSavedSettings();


	});
}


function changeFileUpload(e){
	CSV_BLOB = e.target.files[0];
}

function clickFileUploadButton(e){
	var it_broke = false;
	if(CSV_BLOB.name.endsWith(".csv")){
	    Papa.parse(CSV_BLOB,{
	    	skipEmptyLines: true,
	   		error: function(err, file, inputElem, reason){
	   			alertDanger("<strong>Error!</strong> The data is in wrong format. Only CSV file can be loaded!", "#TableArea");
	   			it_broke = true;
	   		},
	   		complete: function(results, file){
	   			if(!it_broke){
	   				alertSuccess(`<strong> ${results.data.length - 1} Rows </strong> Uploaded Successfully.`);

	   				
	   				LoadData(results.data, showOutlierRangeToUser = true);
	   			}
	   		}
	    });
	} else {
		alertDanger("<strong>Error!</strong> The data is in wrong format. Only CSV file can be loaded!", "#TableArea");
	}
}

// Project 2

function clickLoadDBData1(){
	var uid = getCookie("uid");
	if(uid != ""){
		$.post("php/load_DB_Data1.php",
		{
			
		},
		function(data,status){
			data = JSON.parse(data);
			alertSuccess(`<strong> ${data.length - 1} Rows </strong> Downloaded Successfully.`);


			LoadData(data, showOutlierRangeToUser = true);

		});
	} else {
		setupSimpleModal("Load DB Data1", "Access Denied. You Are Not Logged In.");
		$("#SimpleModal").modal();
	}
}

function clickLoadDBData2(){
	var uid = getCookie("uid");
	if(uid != ""){
		$.post("php/load_DB_Data2.php",
		{
			
		},
		function(data,status){
			data = JSON.parse(data);
			alertSuccess(`<strong> ${data.length - 1} Rows </strong> Downloaded Successfully.`);

			
			LoadData(data, showOutlierRangeToUser = true);
		});
	} else {
		setupSimpleModal("Load DB Data2", "Access Denied. You Are Not Logged In.");
		$("#SimpleModal").modal();
	}
}

// PROJECT 2

//click on data selection radio button
function clickAvgWage(){
	if(DATA_LOADED){
		// in case new graphs are used
		$("#graph2").attr("class","");
		
		drawCityAvgWageLineChart(id="graph1");
		drawCityAvgWageBarChart(id="graph2");
	}
}

function clickEstPop(){
	if(DATA_LOADED){
		// in case new graphs are used
		$("#graph2").attr("class","");
		
		drawCityEstPopLineChart(id="graph1");
		drawCityEstPopBarChart(id="graph2");
	}
}

function clickState(){
	if(DATA_LOADED){
		// in case new graphs are used
		$("#graph2").attr("class","");
		
		drawStateBarChart(id="graph1");
		drawStatePieChart(id="graph2");
	}
}

// View > New Charts
function clickNewCharts(){
	if(DATA_LOADED){
		drawNewChartGeo(id="graph1");
		drawNewChartRadar(id="graph2");
	} else {
		alertDanger("<strong>Error:</strong> Please Load Data And Select From Data Selection Area First","#graph1");
	}
}

// sliders > AvgWages
// sliders > est pop
function changeSlider(){
	if(DATA_LOADED == "True"){
		formatAndDrawTable();
		if($("#graph4").html() != ""){
			drawAnalyticsLineChart();
		}
	}
}

// view > analytics
function clickAnalytics(){
	if(DATA_LOADED == "True") {
		drawAnalyticsLineChart();
	} else {
		alertDanger("<strong>Error:</strong> Please Load Data First","#graph4");
	}
}

//save settings button
function clickSaveSettings(){
	var uid = getCookie("uid");

	if(uid != "" && DATA_LOADED == "True"){	
		$.post("php/save_settings.php",{
			uid : getCookie("uid"),
			login : getCookie("username"),
			avgwages : $("#AvgWagesSlider").val(),
			estpop : $("#EstPopSlider").val()
		}, function(data,status){
			if(data == "success"){
				alertSuccess("User Settings Saved Successfully!", "#MessageArea");
			} else {
				alertDanger("User Settings were not Saved! Try Again!", "#MessageArea");
			}
		});
	}
}

//save filtered results
function clickSaveFilteredResults(){
	var uid = getCookie("uid");

	var AvgWages_filter = $("#AvgWagesSlider").val();
	var EstPop_filter = $("#EstPopSlider").val();
	
	var data = filterRows(AvgWages_filter, "AvgWages", TABLE_HEADERS, TABLE_DATA);
	data = filterRows(EstPop_filter, "EstimatedPopulation", TABLE_HEADERS, data);
	data = JSON.stringify(data);

	if(uid != "" && DATA_LOADED == "True"){	
		$("#SaveFilteredResults").html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`);
		$.post("php/save_filtered_results.php",{
			// array of filtered results to save
			data : data
		}, function(data,status){
			if(data == "success"){
				alertSuccess("Filtered Results Saved Successfully!", "#MessageArea");
			} else {
				alertDanger("Filtered Results were not Saved! Try Again!", "#MessageArea");
			}

			$("#SaveFilteredResults").html('Save Filtered Results');
		});
	}
}

function clickSendEmailMenuOption(){
	var uid = getCookie("uid");

	if(uid != ""){	
		setupEmailModal();
		$("#EmailModal").modal();
	} else {
		setupSimpleModal("Log In First","You must log in to use this feature");
		$("#SimpleModal").modal();
	}

}

function clickSendEmailButton() {

	if(!$("#SendEmailTo").val().includes("@")){
		alertDanger("<b>Email Not Sent</b> An invalid email address was applied.");
		return;
	}

	$.post("php/send_email.php",{
		to: $("#SendEmailTo").val(),
		subject: $("#SendEmailSubject").val(),
		content: $("#SendEmailContent").val()
	}, function(data, status){
		alertSuccess(`<b>Email Sent To:</b> ${$("#SendEmailTo").val()}`);
	});
}