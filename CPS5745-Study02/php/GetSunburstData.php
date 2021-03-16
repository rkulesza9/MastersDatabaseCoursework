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

function get_data_for_state($conn, $state, $quant){
	$data = [];

	$query =  "select State, City, Zipcode, $quant from vDV_Data1 where state=?";
	$stmt = $conn->prepare($query);
	$stmt->bind_param('s', $state);
	$stmt->execute();
	$stmt->bind_result($state, $city, $zipcode, $estpop);
	
	while($stmt->fetch()){
		$row = ["State" => $state, "City" => $city, "Zipcode" => $zipcode, "$quant" => $estpop];
		$data[] = $row;
	}

	return $data;

}

function states_passed_as_argument(){
	return $_POST['states'] != "[]";
}

function get_data_for_states($conn,$quant){
	$states = $_POST['states'];
	$states = json_decode($states);

	$data = [];
	if(count($states) > 0){
		for($x = 0; $x < count($states); $x++){

			$data = array_merge($data, get_data_for_state($conn, $states[$x], $quant));
		}
	}

	return $data;
}

// state data > already prepared > not assoc array > does not require reformat
function get_data($conn, $quant){
	$data = [];

	$query = "select State, City, Zipcode, $quant from vDV_Data1";
	$result = $conn->query($query);

	while($row = $result->fetch_assoc()){
		$data[] = $row;
	}

	return $data;
}

// [{"group":["A3","B7",""],"current":{"count":50}}]
// [{"group":["State", "City", ""], "current":{"count":EstimatedPopulation}}]
function reformat_data($data_assoc, $quant){
	$headers = array_keys($data_assoc[0]);
	$data = [$headers];
	$data_rows = [];

	for($x = 0; $x < count($data_assoc); $x++){
		$row = $data_assoc[$x];
		$data_row = [];
		$data_row["group"] = [];
		$data_row["current"] = [];
		for($x2 = 0; $x2 < count($headers); $x2++){
			$field = $headers[$x2];

			if($field != "$quant"){
				array_push($data_row["group"], $row[$field]);
			} else {
				$data_row["current"]["count"] = $row[$field];
			}
		}
		array_push($data_rows, $data_row);
	}
	
	array_push($data, $data_rows);
	unset($data[0][count($data[0])-1]);
	return $data;
}

function main(){

	$conn = make_connection();

	if(states_passed_as_argument()){
		$data = get_data_for_states($conn, $_POST['quant']);
	} else {
		$data = get_data($conn, $_POST['quant']);
	}

	$data = reformat_data($data, $_POST['quant']);
	echo json_encode($data);
}

if(isset($_POST["GetSunburstData"])) main();
?>