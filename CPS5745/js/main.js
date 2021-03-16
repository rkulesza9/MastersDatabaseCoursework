function main(){

	//CHECK IF USER IS LOGGED IN OR NOT
	if(getCookie("uid") != ""){
		var name = getCookie("name").replaceAll("+"," ");
		alertSuccess(`<Strong>Logged In</Strong> Welcome, ${name} `);
	}

	//THIS ALERT APPEARS BY DEFAULT
	// alertPrimary("<strong>Welcome!</strong> This is the message area.");

	//SETUP ON-CLICK LISTENERS
	//  nav > help > client
	$("#Client").click(function(){
		setupClientModal();
		$("#ClientModal").modal();
	});

	//  nav > help > info
	$("#Info").click(function(){
		$("#InfoModal").modal();
	});

	//  nav > Settings > User Info
	$("#UserInfo").click(function(){
		setupUserInfoModal();
		$("#UserInfoModal").modal();
	});

	//  nav > View > Line
	$("#Line").click(clickLine);

	//  nav > View > Pie 
	$("#Pie").click(clickPie);

	//  nav > View > Bar
	$("#Bar").click(clickBar);

	//  nav > View > map
	$("#Map").click(clickMap);

	//  nav > file > exit
	$("#exit_button").click(clickExitButton);

	//  nav > file > logout
	$("#logout_from_db_button").click(clickLogout);

	//  nav > file > login
	$("#login_to_db_submit").click(clickLogin);

	//  nav > file > upload csv file
	$("#uploadFileButton").click(clickFileUploadButton);

/* ---- PROJECT 2 START ---- */

	//  nav > file > Load DB Data1
	$("#LoadDBData1").click(clickLoadDBData1);

	//  nav > file > Load DB Data2
	$("#LoadDBData2").click(clickLoadDBData2);

	// data selection area > radio buttons
	$("#customRadio1").change(clickAvgWage);
	$("#customRadio2").change(clickEstPop);
	$("#customRadio3").change(clickState);

	// view > new charts
	$("#NewCharts").click(clickNewCharts);

	//SETUP CHANGE LISTENERS
	//  nav > file > upload csv file
	$("#fileToUpload").change(changeFileUpload);

	//sliders
	//sliders > avgwages
	$("#AvgWagesSlider").on("input change", changeSlider);

	//sliders > est pop
	$("#EstPopSlider").on("input change", changeSlider);

	//view > analytics
	$("#Analytics").click(clickAnalytics);

	//save settings button
	$("#SaveSettings").click(clickSaveSettings);

	//save filtered results
	$("#SaveFilteredResults").click(clickSaveFilteredResults);

	//Nav > Settings > Email Settings
	$("#SendEmailMenuOption").click(clickSendEmailMenuOption);

	//Modals > EmailModal > send email
	$("#SendEmailButton").click(clickSendEmailButton);

}


$(document).ready(main);