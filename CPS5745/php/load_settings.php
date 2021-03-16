<?php

function dbConnect(){
	include "db.php";

	$mysqli = new mysqli($server,$username,$password,"2020F_kuleszar");

	// Check connection
	if ($mysqli -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
	  return false;
	}

	return $mysqli;
}

function loadUserSettings($conn, $uid){
	$query = "SELECT uid, login, AvgWages, EstimatedPopulation, datetime FROM User_Settings WHERE uid=?";
	$stmt = $conn->prepare($query);
	$stmt->bind_param("i", $uid);
	$stmt->execute();
	$stmt->bind_result($uid, $login, $AvgWages, $EstimatedPopulation, $datetime);
	$stmt->fetch();
	$stmt->close();

	$data = [];
	$data["uid"] = $uid;
	$data["login"] = $login;
	$data["AvgWages"] = $AvgWages;
	$data["EstPop"] = $EstimatedPopulation;
	$data["datetime"] = $datetime;

	return $data;
}

function main(){
	$conn = dbConnect();
	$data = loadUserSettings($conn, $_POST['uid']);
	echo json_encode($data);
}


if(isset($_COOKIE['uid'])) main();
?>