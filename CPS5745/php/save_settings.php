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

function saveUserSettings($conn, $uid, $login, $avgwages, $estpop){
	$query = "CALL setUserSettingsFor(?,?,?,?)";
	$stmt = $conn->prepare($query);
	$stmt->bind_param("isdd", $uid, $login, $avgwages, $estpop);
	$stmt->execute();
	$stmt->close();

	return "success";
}

function main(){
	$conn = dbConnect();
	$status = saveUserSettings($conn, $_POST['uid'], $_POST['login'], $_POST['avgwages'], $_POST['estpop']);
	echo $status;
}


if(isset($_COOKIE['uid'])) main();
?>