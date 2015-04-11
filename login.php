<?php include "base.php"; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">  
<html xmlns="http://www.w3.org/1999/xhtml">    
<head>    
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />    
<title>User login</title>  
<link rel="stylesheet" href="style.css" type="text/css" />  
</head>    
<body>    
<div id="main">


<?php
error_reporting(E_ALL);
if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['Username']))
{
	 ?>

	 <h1>Member Area</h1>
     <p>Thanks for logging in! You are <b><?php echo$_SESSION['Username']?></b> and your email address is <b><?php echo $_SESSION['EmailAddress']?></b>.</p>
     
     <?php
}
elseif(!empty($_POST['username']) && !empty($_POST['password']))
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

		$_SESSION['UserID'] = $userid;
		$_SESSION['Username'] = $username;
		$_SESSION['EmailAddress'] = $email;
		$_SESSION['LoggedIn'] = 1;
		$_SESSION['AccessLevel'] = $accesslevel;

		echo "<h1>Success</h1>";
		echo "<p>We are now redirecting you to the member area.</p>";
		if(isset($_GET['return'])) {
			$returnurl = $_GET['return'].".php";
		} else {
			$returnurl = "index.php";
		}
		echo "<meta http-equiv='refresh' content='0;URL=".$returnurl."' />";
	}
	else
	{
		echo "<h1>Error</h1>";
		echo "<p>Incorrect Username/Password. Please <a href=\"\">click here to try again</a>.</p>";
	}
}
else
{
?>

	<h1>Member Login</h1>

	<p>Thanks for visiting! Please either login below, or <a href="register.php">click here to register</a>.</p>

<div id='loginform'>
	<form method="post" name="loginform" id="loginform">
	<fieldset>
		<label for="username">Username:</label><input type="text" name="username" id="username" /><br />
		<label for="password">Password:</label><input type="password" name="password" id="password" /><br />
		<input type="submit" name="login" id="login" value="Login" />
	</fieldset>
	</form>
</div>

<?php
}
echo "Session id: " . session_id();
?>

</div>
</body>
</html>