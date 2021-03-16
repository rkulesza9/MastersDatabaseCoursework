<?php 

function make_connection(){
	include 'dbconfig.php';

	$conn = new mysqli($db_server, $db_username, $db_password, $db_database);

	if($conn->connect_errno){
		echo "[ 'status' : 'error', 'description' : ${$conn -> connect_error}";
		exit();
	}

	return $conn;
}

function get_data($conn){
	$query = "describe vDV_Data1";
	$result = $conn->query($query);

	$data = [];
	while($row = $result->fetch_assoc()){
		$data[] = $row;
	}

	return $data;
}

function reformat_data($data){
	$exclude = ["RecordNumber", "Latitude", "Longitude", "Xaxis", "Yaxis", "Zaxis"];
	// look for type contains int or float
	$arr = [];
	for($x = 0; $x < count($data); $x++){

		if(strpos($data[$x]["Type"], "float") !== false || strpos($data[$x]["Type"], "int") !== false){
			if(!in_array($data[$x]["Field"], $exclude)) $arr[] = $data[$x]["Field"];
		}
	}

	return $arr;
}

function main(){

	$conn = make_connection();
	$data = get_data($conn);
	$data = reformat_data($data);
	echo json_encode($data);
}

main();
?>