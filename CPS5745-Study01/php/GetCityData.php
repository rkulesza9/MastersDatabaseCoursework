<?php

	function getCityData(){
		include "dbconfig.php";

		$conn = new mysqli($DB["server"], $DB["username"], $DB["password"], $DB["database"]);

		// Check connection
		if ($conn -> connect_errno) {
		  echo "Failed to connect to MySQL: " .$conn -> connect_error;
		  exit();
		}

		$query = "SELECT * FROM " . $DB['table'];
		$result = $conn->query($query);
		$cities = array();

		while($city = $result->fetch_assoc()){
			array_push($cities, $city);
		}

		return $cities;
	}

	function rawPointToFloatPair($data)
	{   
	    $res = unpack("lSRID/CByteOrder/lTypeInfo/dX/dY", $data);
	    return [$res['X'],$res['Y']];
	}

	function main(){
		if(isset($_POST["getCityData_flag"])){
			$city_data = getCityData();
			for($x = 0; $x < count($city_data); $x++){
				$city_data[$x]["location"] = implode(",",rawPointToFloatPair($city_data[$x]["location"]));
			}
			echo json_encode($city_data);
		}
	}
	main();
?>