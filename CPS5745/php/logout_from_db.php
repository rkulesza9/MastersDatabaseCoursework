<?php
	if(isset($_POST['submit'])){

		$one_day = 86400;
		setcookie("uid","", time() - $one_day , "/");
		setcookie("name","", time() - $one_day , "/");
		setcookie("username","", time() - $one_day , "/");
		setcookie("data-loaded","",time()-$one_day,"/");

	}
?>