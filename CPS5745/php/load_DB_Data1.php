<?php

function dbConnect(){
	include "db.php";

	$mysqli = new mysqli($server,$username,$password,"datamining");

	// Check connection
	if ($mysqli -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
	  return false;
	}

	return $mysqli;
}

function getDataFromData1($conn){
	$query = "SELECT * FROM vDV_Data1";
	$result = $conn->query($query);

	$data = array();
	while($row = $result->fetch_assoc()){
		$data[] = $row;
	}

	return $data;
}

function unassociateArray($arr){
	$result = [];
	$headers = array_keys($arr[0]);
	array_push($result, $headers);

	for($x = 0; $x < count($arr); $x++){
		$row = $arr[$x];
		$row_result = [];
		foreach($row as $header => $value){
			array_push($row_result, $value);
		}
		array_push($result, $row_result);
	}

	return $result;
}

function main(){
	$conn = dbConnect();
	$data = getDataFromData1($conn);
	$data = unassociateArray($data);
	$json = json_encode($data);

	echo $json;
}


if(isset($_COOKIE['uid'])) main();
?>