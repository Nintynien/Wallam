<?php include "base.php"; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en-US" xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<title>Wallam - Blog</title>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
	<link rel="shortcut icon" type="image/x-icon" href="css/images/favicon.png" />
</head>
<body>
<?php include "header.php"; ?>


<div class="shell">
	<div class="container">
		<div class="main">
<?php
if(isset($_GET['id']))
{
	$sql = "SELECT * FROM Blogs WHERE blogid = ?";
	$stmt = sqlsrv_query(&$conn, $sql, array( &$_GET['id'] ));
	if( $stmt === false) {
		die( print_r( sqlsrv_errors(), true) );
	}
	
	if(sqlsrv_has_rows($stmt) === true)
	{
		$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
		echo "\t\t\t\t<div class=\"post\">\n";
		echo "\t\t\t\t\t<h2><a href=\"blog.php?id=".$row['blogid']."\" title=\"".$row['title']."\">".$row['title']."</a><date>".$row['time']->format('F j, Y')."</date></h2>\n";
		echo "\t\t\t\t\t<p>".nl2br($row['text'])."</p>\n";
		echo "\t\t\t\t</div>\n";
	} else {
		if (empty($row['blogid']) || is_null($row['blogid']) || !isset($row['blogid'])) {
			echo "<post><h2>Error</h2><p>Unable to find the requested blog posting.</p></post>";
		}
	}
}
elseif (isset($_GET['new']))
{
	if (isset($_SESSION['LoggedIn']) && isset($_SESSION['AccessLevel'])) {
		if ($_SESSION['LoggedIn'] === 1 && $_SESSION['AccessLevel'] >= 0)
		{
			if(!empty($_POST['title']) && !empty($_POST['text'])) //if they submitted a new blog post
			{
				$sql = "INSERT INTO Blogs (userid, title, text) VALUES (?, ?, ?)";
				$stmt = sqlsrv_query(&$conn, $sql, array( $_SESSION['UserID'], &$_POST['title'], &$_POST['text']));
				if($stmt)
				{
					echo "<h1>Success</h1>";
					echo "<p>Your post was successfully created. Please <a href=\"blog.php\">click here to return to the blog section</a>.</p>";
				}
				else
				{
					echo "<h1>Error</h1>";
					echo "<p>Sorry, an error occured while saving your post. Please go back and try again.</p>";    
					print_r( sqlsrv_errors(), true);
				}    	
			} else {
				echo "\t\t\t\t<div class=\"post\">\n";
				echo "\t\t\t\t\t<h2><a>Create a new blog post</a><date>Posting as: <i>".$_SESSION['Username']."</i></date></h2>\n";
				echo "\t\t\t\t\t<p>\n";
					echo "\t<form method=\"post\" name=\"blogform\" id=\"blogform\">\n";
					echo "\t\t<label for=\"title\" style=\"width: auto;\">Title:</label><br />\n";
					echo "\t\t<textarea rows=\"1\" cols=\"117\" name=\"title\" form=\"blogform\" id=\"title\"></textarea><br />\n";
					echo "\t\t<label for=\"text\" style=\"width: auto;\">Text:</label><br />\n";
					echo "\t\t<textarea rows=\"10\" cols=\"117\" name=\"text\" form=\"blogform\" id=\"text\"></textarea><br />\n";
					echo "\t\t<input type=\"submit\" name=\"post\" id=\"post\" value=\"Submit\" />\n";
					echo "\t</form>\n";

				echo "\t\t\t\t\t</p>\n";
				echo "\t\t\t\t</div>\n";
			}
		} else {
				echo "\t\t\t\t<div class=\"post\">\n";
				echo "\t\t\t\t\t<h2>Error</h2>\n";
				echo "\t\t\t\t\t<p>You don't have access to this page. Make sure you are logged in and have the right privileges.</p>\n";
				echo "\t\t\t\t</div>\n";
		}
	} else {
			echo "\t\t\t\t<div class=\"post\">\n";
			echo "\t\t\t\t\t<h2>Error</h2>\n";
			echo "\t\t\t\t\t<p>You are not logged in. Please log in.</p>\n";
			echo "\t\t\t\t</div>\n";
	}
}
else
{
	if (isset($_SESSION['LoggedIn']) && isset($_SESSION['AccessLevel'])) {
		if ($_SESSION['LoggedIn'] === 1 && $_SESSION['AccessLevel'] >= 0)
		{
			echo "\t\t\t\t<h3 style=\"font-family: Franchise;text-align: right\"><a href=\"blog.php?new\" title=\"New Entry\">New Entry</a></h3>\n";
		}
	}
	$sql = "SELECT TOP(5) * FROM Blogs ORDER BY time DESC";
	$stmt = sqlsrv_query(&$conn, $sql);
	if( $stmt === false) {
		die( print_r( sqlsrv_errors(), true) );
	}

	if(sqlsrv_has_rows($stmt) === true)
	{
		while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
			echo "\t\t\t\t<div class=\"post\">\n";
			echo "\t\t\t\t\t<h2><a href=\"blog.php?id=".$row['blogid']."\" title=\"".$row['title']."\">".$row['title']."</a><date title=\"".$row['time']->format('F j, Y h:i:s A')."\">".$row['time']->format('F j, Y')."</date></h2>\n";
			echo "\t\t\t\t\t<p>".nl2br($row['text'])."</p>\n";
			echo "\t\t\t\t</div>\n";
		}
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