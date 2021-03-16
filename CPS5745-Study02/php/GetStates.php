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
	$query = "select distinct(state) from vDV_Data1";
	$result = $conn->query($query);

	$data = [];
	while($row = $result->fetch_assoc()){
		$data[] = $row["State"];
	}

	return $data;
}

function main(){

	$conn = make_connection();
	$data = get_data($conn);
	echo json_encode($data);
}

main();
?>