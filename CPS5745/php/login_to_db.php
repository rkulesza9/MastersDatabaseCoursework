<?php
	
	/* if started from commandline, wrap parameters to $_POST and $_GET */
	if (!isset($_SERVER["HTTP_HOST"])) {
	  parse_str($argv[1], $_GET);
	  parse_str($argv[1], $_POST);
	}

?>

<?php if (isset($_POST['submit'])) { 

	include "db.php";
	$login_to_db = array();
?>

<?php
	
	$mysqli = new mysqli($server, $username, $password);
	if($mysqli -> connect_errno){
		$login_to_db["status"] = "failure";
		$login_to_db["error"] = "MySQL Connection Error";
		$login_to_db["error_description"] = $mysqli -> connect_error;

		echo json_encode($login_to_db);
		exit();
	}

	$username = $_POST['username'];
	$password = $_POST['password'];

	$query = "select uid, name, gender from datamining.DV_User where login=? and password=?";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param('ss',$username, $password);
	$stmt->bind_result($uid, $name, $gender);
	$stmt->execute();
	$stmt->fetch();
	$stmt->close();

	if(isset($uid) == FALSE){
		$login_to_db["status"] = "failure";
		$login_to_db["error"] = "Login Error";
		$login_to_db["error_description"] = "Login info was not recognized.";

		echo json_encode($login_to_db);
		exit();
	}

	$login_to_db["status"] = "success";
	$login_to_db["uid"] = $uid;
	$login_to_db["name"] = $name;
	$login_to_db["gender"] = $gender;
	$login_to_db["username"] = $username;

	$one_day = 86400;
	setcookie("uid", $uid, time() + $one_day, "/");
	setcookie("name", $name, time() + $one_day, "/");
	setcookie("username", $username, time() + $one_day, "/");
	setcookie("gender", $gender, time() + $one_day, "/");
	// session_start();
	// $_SESSION["uid"] = $uid;
	// $_SESSION["username"] = $username;
	// $_SESSION["name"] = $name;

?>

<?php echo json_encode($login_to_db); } ?>