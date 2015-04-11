<?php
//Google Analytics
echo "
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-43347305-1', 'skylerbock.com');
  ga('send', 'pageview');

</script>";

echo "<div id=\"alltop\">\n";
echo "\t<div id=\"header\">\n";
echo "\t\t\t<a href=\"index.php\" title=\"Wallam\">\n";
echo "\t\t\t\t\t<div id=\"logo\">Wallam</div>\n";
echo "\t\t\t\t\t<div id=\"slogan\">theater</div>\n";
echo "\t\t\t</a>\n";
echo "\t\t\t<div id=\"account\">\n";



if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['Username'])) //user is logged in
{
				echo "\t\t\t\tWelcome, ".$_SESSION['Username']."\n";
				echo "\t\t\t\t<li><a href=\"logout.php?return=".basename($_SERVER['PHP_SELF'], ".php")."\" title=\"Logout\">Logout</a></li>\n";
				echo "\t\t\t\t<li><a href=\"account.php\" title=\"Account\">Account</a></li>\n";
}
elseif(!empty($_POST['username']) && !empty($_POST['password'])) //user wants to login
{
	//Need to apply encryption to password to compare to database
	$sql = "SELECT * FROM Users WHERE username = ? AND password = ?";
	$stmt = sqlsrv_query(&$conn, $sql, array( &$_POST['username'], &$_POST['password']));
	if( $stmt === false) {
		die( print_r( sqlsrv_errors(), true) );
	}

	if(sqlsrv_has_rows($stmt) === true)
	{
		$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
		$userid = $row['userid'];
		$username = $row['username'];
		$email = $row['email'];
		$accesslevel = $row['accesslevel'];

		$_SESSION['Username'] = $username;
		$_SESSION['EmailAddress'] = $email;
		$_SESSION['LoggedIn'] = 1;
		$_SESSION['AccessLevel'] = $accesslevel;

		echo "<meta http-equiv='refresh' content='0' />";
	}
	else
	{
		echo "<meta http-equiv='refresh' content='0;url=login.php' />";
	}
}
else //offer to login
{
				echo "\t\t\t\tNew to Wallam? <a href=\"login.php?return=".basename($_SERVER['PHP_SELF'], ".php")."\" title=\"Login\">Login</a> or <a href=\"register.php\" title=\"Register\">Register</a>\n";
}





echo "\t\t\t</div>\n";
echo "\t\t\t<div id=\"navigation\">\n";
echo "\t\t\t\t<ul>\n";
echo "\t\t\t\t\t<li><a href=\"home.php\" title=\"Home\" ". ((basename($_SERVER['PHP_SELF'], ".php") === "home") ? "class=\"activetext\"" : NULL ) ."><span>Home</span></a></li>\n";
echo "\t\t\t\t\t<li><a href=\"blog.php\" title=\"Blog\" ". ((basename($_SERVER['PHP_SELF'], ".php") === "blog") ? "class=\"activetext\"" : NULL ) ."><span>Blog</span></a></li>\n";
echo "\t\t\t\t\t<li><a href=\"movies.php\" title=\"Movies\" ". ((basename($_SERVER['PHP_SELF'], ".php") === "movies") ? "class=\"activetext\"" : NULL ) ."><span>Movies</span></a></li>\n";
echo "\t\t\t\t\t<li><a href=\"showtimes.php\" title=\"Showtimes\" ". ((basename($_SERVER['PHP_SELF'], ".php") === "showtimes") ? "class=\"activetext\"" : NULL ) ."><span>Showtimes</span></a></li>\n";
echo "\t\t\t\t\t<li><a href=\"contactus.php\" title=\"Contact Us\" ". ((basename($_SERVER['PHP_SELF'], ".php") === "contactus") ? "class=\"activetext\"" : NULL ) ."><span>Contact Us</span></a></li>\n";
echo "\t\t\t\t</ul>\n";
echo "\t\t\t</div>\n";
echo "\t</div>\n";
echo "</div>\n";

?>