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

function deleteAllFromTableInDB($conn){
	$sql = "DELETE FROM Filtered_Results";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$stmt->close();
}

function insertRowIntoDB($conn, $row){
	$sql = "INSERT INTO Filtered_Results (RecordNumber, Zipcode, ZipCodeType, City, State, LocationType, Latitude, Longitude, Xaxis, Yaxis, Zaxis, WorldRegion, Country, LocationText, Location, Decommisioned, TaxReturnsFiled, EstimatedPopulation, TotalWages, AvgWages, Notes) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

	$stmt = $conn->prepare($sql);
	$stmt->bind_param("isssssdddddsssssiiids", $row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7],
												$row[8], $row[9], $row[10], $row[11], $row[12], $row[13], $row[14], 
												$row[15], $row[16], $row[17], $row[18], $row[19], $row[20] );

	$stmt->execute();
	$stmt->close();

	return "success";
}

function saveFilteredData($conn, $data){
	deleteAllFromTableInDB($conn);

	$status = "success";
	for($x = 0; $x < count($data); $x++){
		$row = $data[$x];
		$row_status = insertRowIntoDB($conn, $row);
		if($row_status != "success"){
			$status = "failure";
		}
	}

	return $status;
}

function main(){
	$conn = dbConnect();
	$data = json_decode($_POST["data"]);
	$status = saveFilteredData($conn, $data);
	echo $status;
}


if(isset($_COOKIE['uid'])) main();
?>