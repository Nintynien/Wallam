<?php 
	include "base.php";
	$_SESSION = array();
	session_destroy();
	if(isset($_GET['return'])) {
		$returnurl = $_GET['return'].".php";
	} else {
		$returnurl = "index.php";
	}
	echo "<meta http-equiv='refresh' content='0;URL=".$returnurl."' />";
?>