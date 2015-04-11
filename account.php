<?php include "base.php"; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en-US" xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<title>Wallam - Account</title>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
	<!--[if IE 6]>
		<link rel="stylesheet" href="css/ie6.css" type="text/css" media="all" />
	<![endif]-->
	<link rel="shortcut icon" type="image/x-icon" href="css/images/favicon.ico" />
	
	<script src="js/jquery-1.4.2.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.jcarousel.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/functions.js" type="text/javascript" charset="utf-8"></script>
	
</head>
<body>
<?php include "header.php"; ?>

<div class="shell">
	<div class="container">
		<div class="main">
<?php
if(isset($_GET['user']))
{
	$sql = "SELECT * FROM Users WHERE username = ?";
	$stmt = sqlsrv_query(&$conn, $sql, array( $_GET['user']));
	if( $stmt === false) {
		die( print_r( sqlsrv_errors(), true) );
	}

	if(sqlsrv_has_rows($stmt) === true)
	{
		$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
		echo "\t\t\t\t<div class=\"post\">\n";
		echo "\t\t\t\t\t<h2><a href=\"account.php?id=".$row['userid']."\" title=\"".$row['username']."\">".$row['username']."</a><date>Member Since: ".$row['creation']->format('F j, Y')."</date></h2>\n";
		echo "\t\t\t\t\t<p>Access level: ".$row['accesslevel']."</p>\n";
		echo "\t\t\t\t\t<p>E-mail: <i>Hidden</i></p>\n";
		echo "\t\t\t\t</div>\n";
	} else {
		if (empty($row['userid']) || is_null($row['userid']) || !isset($row['userid'])) {
			echo "<post><h2>Error</h2><p>Unable to find the requested username.</p></post>";
		}
	}
}
elseif (isset($_GET['id']))
{
	$sql = "SELECT * FROM Users WHERE userid = ?";
	$stmt = sqlsrv_query(&$conn, $sql, array( $_GET['id']));
	if( $stmt === false) {
		die( print_r( sqlsrv_errors(), true) );
	}

	if(sqlsrv_has_rows($stmt) === true)
	{
		$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
		echo "\t\t\t\t<div class=\"post\">\n";
		echo "\t\t\t\t\t<h2><a href=\"account.php?id=".$row['userid']."\" title=\"".$row['username']."\">".$row['username']."</a><date>Member Since: ".$row['creation']->format('F j, Y')."</date></h2>\n";
		echo "\t\t\t\t\t<p>Access level: ".$row['accesslevel']."</p>\n";
		echo "\t\t\t\t\t<p>E-mail: <i>Hidden</i></p>\n";
		echo "\t\t\t\t</div>\n";
	} else {
		if (empty($row['userid']) || is_null($row['userid']) || !isset($row['userid'])) {
			echo "<post><h2>Error</h2><p>Unable to find the requested user id.</p></post>";
		}
	}
}
else
{
	if (isset($_SESSION['Username'])) {
	$sql = "SELECT * FROM Users WHERE username = ?";
	$stmt = sqlsrv_query(&$conn, $sql, array( $_SESSION['Username']));
	if( $stmt === false) {
		die( print_r( sqlsrv_errors(), true) );
	}

	if(sqlsrv_has_rows($stmt) === true)
	{
		$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
		echo "\t\t\t\t<div class=\"post\">\n";
		echo "\t\t\t\t\t<h2><a href=\"account.php?id=".$row['userid']."\" title=\"".$row['username']."\">".$row['username']."</a><date>Member Since: ".$row['creation']->format('F j, Y')."</date></h2>\n";
		echo "\t\t\t\t\t<p>Access level: ".$row['accesslevel']."</p>\n";
		echo "\t\t\t\t\t<p>E-mail: ".$row['email']."</p>\n";
		echo "\t\t\t\t</div>\n";
	}
	} else {
		echo "<post><h2>Error</h2><p>You must be logged in to view this page.</p></post>";
	}
}
?>
			<div class="cl">&nbsp;</div>
		</div>
	</div>
</div>


<?php include "footer.php"; ?>
</body>
</html>