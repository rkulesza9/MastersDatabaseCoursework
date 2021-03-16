<?php
function main(){
	$to = $_POST["to"];
	$subject = $_POST["subject"];
	$content = $_POST["content"];
	$headers = "From: kuleszar@kean.edu";

	mail($to,$subject, $content, $headers);
}


if(isset($_COOKIE['uid'])) main();

?>