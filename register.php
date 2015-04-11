<?php include "base.php"; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">  
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  

<title>Register</title>
<link rel="stylesheet" href="style.css" type="text/css" />
</head>  
<body>  
<div id="main">
<?php
if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['Username']))
{
?>
	<h1>Already registered</h1>
	<p>You're already logged in (registered)!</p>
	<p>You are <b><?php echo$_SESSION['Username']?></b> and your email address is <b><?php echo $_SESSION['EmailAddress']?></b>.</p>

<?php
}
elseif(!empty($_POST['username']) && !empty($_POST['password'])) //if they submitted registration details
{
	//$username = mysql_real_escape_string($_POST['username']);
	//$password = md5(mysql_real_escape_string($_POST['password']));
	//$email = mysql_real_escape_string($_POST['email']);

	//$checkusername = mysql_query("SELECT * FROM users WHERE Username = '".$username."'");

	$sql = "SELECT * FROM Users WHERE username = ?";
	$stmt = sqlsrv_query(&$conn, $sql, array( &$_POST['username']));
	if( $stmt === false) {
		die( print_r( sqlsrv_errors(), true) );
	}

	if(sqlsrv_has_rows($stmt) === true)
	{
		echo "<h1>Error</h1>";
		echo "<p>Sorry, that username is taken. Please <a href=\"register.php\">go back</a> and try again.</p>";
	}
	else
	{
		$sql = "INSERT INTO Users (username, password, salt, creation, email) VALUES (?, ?, ?, ?, ?)";
		$stmt = sqlsrv_query(&$conn, $sql, array( &$_POST['username'], &$_POST['password'], '1234567890123456', date("m/d/Y"), &$_POST['email']));
		if($stmt)
		{
			echo "<h1>Success</h1>";
			echo "<p>Your account was successfully created. Please <a href=\"index.php\">click here to login</a>.</p>";
		}
		else
		{
			echo "<h1>Error</h1>";
			echo "<p>Sorry, your registration failed. Please go back and try again.</p>";    
		}    	
	}
}
else
{
	?>

	<h1>Register</h1>
	<h2>WARNING: This site doesn't currently use encryption. Please use a simple password ('dog','test','abc', etc.) that you would never use.</h2>

	<p>Please enter your details below to register.</p>

	<form method="post" action="register.php" name="registerform" id="registerform">
	<fieldset>
		<label for="username">Username:</label><input type="text" name="username" id="username" /><br />
		<label for="password">Password:</label><input type="password" name="password" id="password" /><br />
		<label for="email">Email Address:</label><input type="text" name="email" id="email" /><br />
		<input type="submit" name="register" id="register" value="Register" />
	</fieldset>
	</form>

<?php
}
?>

</div>
</body>
</html>