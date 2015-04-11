<?php include "base.php"; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en-US" xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<title>Wallam - Movies</title>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
	<link rel="stylesheet" href="js/jquery.fancybox.css" type="text/css" media="screen" />
	<link rel="shortcut icon" type="image/x-icon" href="css/images/favicon.png" />
	<script type="text/javascript" src="js/jquery-1.10.1.min.js"></script>
	<script type="text/javascript" src="js/jquery-func.js"></script>
	<script type="text/javascript" src="js/jquery.fancybox.js"></script>
	<script type="text/javascript" src="js/jquery.fancybox.pack.js"></script>
	<script type="text/javascript" src="js/jquery.fancybox-media.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$(".fancybox").fancybox();
			$('.fancybox-media')
			.attr('rel', 'media-gallery')
			.fancybox({
				openEffect : 'none',
				closeEffect : 'none',
				prevEffect : 'none',
				nextEffect : 'none',
				padding	   : 0,
				arrows     : false,

				helpers : {
					media : {}
				}
			});
		});
	</script>
</head>
<body>
<?php include "header.php"; ?>


<div class="shell">
	<div class="container">
		<div class="main" style="padding: 0 5px 33px 5px;">
<?php
if(isset($_GET['id']))
{
	//Specific Movie page
	$sql = "SELECT * FROM Movies WHERE movieid = ?";
	$stmt = sqlsrv_query(&$conn, $sql, array( &$_GET['id'] ));
	if( $stmt === false) {
		die( print_r( sqlsrv_errors(), true) );
	}
	
	if(sqlsrv_has_rows($stmt) === true)
	{
		$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
		$mpaarating = "";
		if ($row['rating'] == 5)
			$mpaarating = "\t\t\t\t\t<div class=\"mpaa\" id=\"nc17\"></div>\n";
		if ($row['rating'] == 4)
			$mpaarating = "\t\t\t\t\t<div class=\"mpaa\" id=\"r\"></div>\n";
		if ($row['rating'] == 3)
			$mpaarating = "\t\t\t\t\t<div class=\"mpaa\" id=\"pg13\"></div>\n";
		if ($row['rating'] == 2)
			$mpaarating = "\t\t\t\t\t<div class=\"mpaa\" id=\"pg\"></div>\n";
		if ($row['rating'] == 1)
			$mpaarating = "\t\t\t\t\t<div class=\"mpaa\" id=\"g\"></div>\n";

		echo "\t\t\t\t<div class=\"post\">\n";
		echo "\t\t\t\t\t<h2><div style=\"color: red; float: left;\">".$row['title']." </div>\n";
		echo $mpaarating;
		echo "\t\t\t\t".$row['runtime']."min\n";
			echo "\t\t\t\t\t<div class=\"rating\" style=\"float: none; display: inline-block;\">\n";
			echo "\t\t\t\t\t\t<div class=\"stars\">\n";
			echo "\t\t\t\t\t\t\t<div class=\"stars-in\" style=\"width: ". round(($row['stars']/10)*60) ."px;\" title=\"".$row['stars']."\"></div>\n";
			echo "\t\t\t\t\t\t</div>\n";
			echo "\t\t\t\t\t</div>\n";
		echo "\t\t\t\t\t<date><div style=\"font-size: 18px;\">";
		if ($row['trailer'] != NULL)
			echo "<a class=\"fancybox-media\" rel=\"group\" href=\"".$row['trailer']."\">Trailer</a> -";
		if (isset($_SESSION['LoggedIn']) && isset($_SESSION['AccessLevel'])) {
			if ($_SESSION['LoggedIn'] === 1 && $_SESSION['AccessLevel'] >= 0)
				echo "  <a href=\"movies.php?edit=".$row['movieid']."\" title=\"Edit Movie\">Edit Movie</a>";
		}
		echo "</div></date></h2>\n";

		echo "\t\t\t\t\t<p>\n";
			echo "\t\t\t\t<div class=\"movie\" style=\"width: 206px;\">\n";
			echo "\t\t\t\t\t<div class=\"movie-image\" style=\"height: 317px; width: 206px;\">\n";
			if (file_exists("posters/".$row['poster'])) {
				echo "\t\t\t\t\t\t<img src=\"posters/".$row['poster']."\" alt=\"".$row['title']."\" style=\" height: 317px; width: 206px;\"/></a>\n";
			} else {
				echo "\t\t\t\t\t\t<img src=\"posters/unknown_movie_poster.png\" alt=\"".$row['title']."\" style=\" height: 317px; width: 206px;\"/></a>\n";
			}
			echo "\t\t\t\t\t</div>\n";
			echo "\t\t\t\t</div>\n";
		echo "\t\t\t\t\t</p>\n";
		echo "\t\t\t\t\t<h3>Plot</h3>\n";
		echo "\t\t\t\t\t".$row['description']."\n";
		echo "\t\t\t\t\t<h2>Cast</h2>\n";
		echo "\t\t\t\t\t<p>"."...Actor as John..."."</p>\n";
		echo "\t\t\t\t</div>\n";
	} else {
		if (empty($row['movieid']) || is_null($row['movieid']) || !isset($row['movieid'])) {
			echo "<post><h2>Error</h2><p>Unable to find the requested movie.</p></post>\n";
		}
	}
}
elseif (isset($_GET['new']))
{
	//New Movie Page (Unfinished)
	if ($_SESSION['LoggedIn'] === 1 && $_SESSION['AccessLevel'] >= 0)
	{
		if(!empty($_POST['title']) && !empty($_POST['text'])) //if they submitted a new blog post
		{
			//$sql = "INSERT INTO Blogs (userid, time, title, text) VALUES (?, ?, ?, ?)";
			//$stmt = sqlsrv_query(&$conn, $sql, array( $_SESSION['UserID'], date("m/d/Y H:i:s"), &$_POST['title'], &$_POST['text']));
			if($stmt)
			{
				echo "<h1>Success</h1>";
				echo "<p>Your changes were successfully saved. Please <a href=\"movies.php\">click here to return to the movie section</a>.</p>";
			}
			else
			{
				echo "<h1>Error</h1>";
				echo "<p>Sorry, an error occured while saving your changes. Please go back and try again.</p>";    
			}    	
		} else {
			echo "\t\t\t\t<div class=\"post\">\n";
			echo "\t\t\t\t\t<h2><a>Create a new blog post</a><date>Posting as: <i>".$_SESSION['Username']."</i></date></h2>\n";
			echo "\t\t\t\t\t<p>\n";
				echo "\t<form method=\"post\" name=\"blogform\" id=\"blogform\">\n";
				echo "\t\t<label for=\"title\">Title:</label><br />\n";
				echo "\t\t<textarea rows=\"1\" cols=\"117\" name=\"title\" form=\"blogform\" id=\"title\"></textarea><br />\n";
				echo "\t\t<label for=\"text\">Text:</label><br />\n";
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
}
elseif(isset($_GET['edit']))
{
	//Edit movie page
	if (isset($_SESSION['LoggedIn']) && isset($_SESSION['AccessLevel'])) {
		if ($_SESSION['LoggedIn'] === 1 && $_SESSION['AccessLevel'] >= 0)
		{
			$sql = "SELECT * FROM Movies WHERE movieid = ?";
			$stmt = sqlsrv_query(&$conn, $sql, array( &$_GET['edit'] ));
			if( $stmt === false) {
				die( print_r( sqlsrv_errors(), true) );
			}
			
			if(sqlsrv_has_rows($stmt) === true)
			{
				$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
				if(!empty($_POST['post'])) //if they submitted changes
				{
					//This is where the update statement goes
					$sql = "UPDATE Movies SET imdbid = ?, title = ?, stars = ?, release = ?, runtime = ?, description = ?, rating = ?, poster = ?, trailer = ?, videofile = ? WHERE movieid = ?";
					$stmt = sqlsrv_query(&$conn, $sql, array( &$_POST['imdbid'], &$_POST['title'], floatval(&$_POST['stars']), &$_POST['release'], intval(&$_POST['runtime']), &$_POST['description'], intval(&$_POST['rating']),
																&$_POST['poster'], &$_POST['trailer'], &$_POST['videofile'], $row['movieid']));
					//print_r(&$_POST);
					if($stmt)
					{
						echo "<h1>Success</h1>";
						echo "<p>Your changes were successfully saved. Please <a href=\"movies.php\">click here to return to the movie section</a>.</p>";
					}
					else
					{
						echo "<h1>Error</h1>";
						echo "<p>Sorry, an error occured while saving your changes. Please go back and try again.</p>";    
					}    	
				} else {
					echo "\t\t\t\t<div class=\"post\">\n";
					echo "\t\t\t\t\t<h2>Edit Movie: <a href=\"movies.php?id=".$row['movieid']."\" title=\"".$row['title']."\">".$row['title']."</a><date>User: <i>".$_SESSION['Username']."</i></date></h2>\n";
					echo "\t\t\t\t\t<p> <div style=\" font-size: 16px;\">\n";
							//Movie Poster
							echo "\t\t\t\t<div class=\"movie\" style=\"width: 206px;\">\n";
							echo "\t\t\t\t\t<div class=\"movie-image\" style=\"height: 317px; width: 206px;\">\n";
							echo "\t\t\t\t\t\t<img src=\"posters/".$row['poster']."\" alt=\"".$row['title']."\" style=\" height: 317px; width: 206px;\"/>\n";
							echo "\t\t\t\t\t</div>\n";
							echo "\t\t\t\t</div>\n";
						echo "\t<form method=\"post\" name=\"movieedit\" id=\"movieedit\">\n";
						echo "\t\t<label for=\"imdbid\">IMDBid:</label>\n";
						echo "\t\t<input type=\"text\" name=\"imdbid\" form=\"movieedit\" id=\"imdbid\" value=\"".trim($row['imdbid'])."\"><br />\n";
						echo "\t\t<label for=\"title\">Title:</label>\n";
						echo "\t\t<input type=\"text\" name=\"title\" form=\"movieedit\" id=\"title\" value=\"".trim($row['title'])."\"><br />\n";
						echo "\t\t<label for=\"stars\">Stars:</label>\n";
						echo "\t\t<input type=\"text\" name=\"stars\" form=\"movieedit\" id=\"stars\" value=\"".trim($row['stars'])."\"><br />\n"; //this needs to be changed
						echo "\t\t<label for=\"release\">Release Date:</label>\n";
						echo "\t\t<input type=\"text\" name=\"release\" form=\"movieedit\" id=\"release\" value=\"".$row['release']->format('Y-m-d')."\"><br />\n";
						echo "\t\t<label for=\"runtime\">Runtime (minutes):</label>\n";
						echo "\t\t<input type=\"text\" name=\"runtime\" form=\"movieedit\" id=\"runtime\" value=\"".trim($row['runtime'])."\"><br />\n";

						echo "\t\t<label for=\"rating\">MPAA Rating:</label>\n";
						echo "\t\t<select name=\"rating\" id=\"rating\">\n";
							echo "\t\t\t<option value=\"1\" ";
								if ($row['rating'] == 1) echo "selected";
							echo ">G</option>\n";
							echo "\t\t\t<option value=\"2\" ";
								if ($row['rating'] == 2) echo "selected";
							echo ">PG</option>\n";
							echo "\t\t\t<option value=\"3\" ";
								if ($row['rating'] == 3) echo "selected";
							echo ">PG-13</option>\n";
							echo "\t\t\t<option value=\"4\" ";
								if ($row['rating'] == 4) echo "selected";
							echo ">R</option>\n";
							echo "\t\t\t<option value=\"5\" ";
								if ($row['rating'] == 5) echo "selected";
							echo ">NC-17</option>\n";
						echo "\t\t</select><br/>\n";
						
						echo "\t\t<label for=\"poster\">Poster:</label>\n";
						echo "\t\t<input type=\"text\" name=\"poster\" form=\"movieedit\" id=\"poster\" value=\"".trim($row['poster'])."\"><br />\n";
						echo "\t\t<label for=\"trailer\">Trailer (YouTube URL):</label>\n";
						echo "\t\t<input type=\"text\" name=\"trailer\" form=\"movieedit\" id=\"trailer\" value=\"".trim($row['trailer'])."\"><br />\n";
						echo "\t\t<label for=\"videofile\">Video File:</label>\n";
						echo "\t\t<input type=\"text\" name=\"videofile\" form=\"movieedit\" id=\"videofile\" value=\"".trim($row['videofile'])."\"><br />\n";
						
						echo "\t\t<label for=\"description\">Description:</label>\n";
						echo "\t\t<textarea rows=\"5\" cols=\"70\" name=\"description\" form=\"movieedit\" id=\"description\">".trim($row['description'])."</textarea><br />\n";
						echo "\t\t<input type=\"submit\" name=\"post\" id=\"post\" value=\"Submit\" />\n";
						echo "\t</form>\n";

					echo "\t\t\t\t\t</div></p>\n";
					echo "\t\t\t\t</div>\n";
				}
			} else {
				if (empty($row['movieid']) || is_null($row['movieid']) || !isset($row['movieid'])) {
					echo "<post><h2>Error</h2><p>Unable to find the requested movie.</p></post>\n";
				}
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
//Main Movie page
	echo 'Search: <input type="text" id="search" onkeyup="return search();"/> ';
	echo ' <input type="checkbox" id="shown" > <label id="labelshown" for="shown" style="width: auto;">In Theaters</label>';
	if (isset($_SESSION['LoggedIn']) && isset($_SESSION['AccessLevel'])) {
		if ($_SESSION['LoggedIn'] === 1 && $_SESSION['AccessLevel'] >= 0)
		{
			echo "\t\t\t\t<h3 style=\"font-family: Franchise;text-align: right\"><a href=\"movies.php?new\" title=\"New Entry\">New Entry</a></h3>\n";
		} else {
			echo "\t\t\t\t<h3> </h3>\n";
		}
	} else {
		echo "\t\t\t\t<h3> </h3>\n";
	}
	$sql = "SELECT m.*,	CASE WHEN (m.movieid in (SELECT distinct sinn.movieid FROM Showtimes sinn Where time > ?)) THEN (select '1') else (select '0') END as showing FROM Movies m ORDER BY title ASC"; //Remember to get the reverse order as the CSS float property displays in reverse order.
	$stmt = sqlsrv_query(&$conn, $sql, array( date('m/d/Y') ));
	if( $stmt === false) {
		die( print_r( sqlsrv_errors(), true) );
	}

	if(sqlsrv_has_rows($stmt) === true)
	{
		while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
			echo "\t\t\t\t<div class=\"movie\"><div class=\"title\">".$row['title']."</div>\n";
			echo "\t\t\t\t\t<div class=\"movie-image\">\n";
			echo "\t\t\t\t\t\t<a href=\"movies.php?id=".$row['movieid']."\" title=\"".$row['title']."\"><span class=\"info\"><span class=\"name\">".$row['title']."</span></span>";
			if (file_exists("posters/".$row['poster'])) {
				echo "<img src=\"posters/".$row['poster']."\" alt=\"".$row['title']."\" /></a>\n";
			} else {
				echo "<img src=\"posters/unknown_movie_poster.png\" alt=\"".$row['title']."\" /></a>\n";
			}
			echo "\t\t\t\t\t</div>\n";
			echo "\t\t\t\t\t<div class=\"rating\">\n";
			echo "\t\t\t\t\t\t<p>RATING</p>\n";
			echo "\t\t\t\t\t\t<div class=\"stars\">\n";
			echo "\t\t\t\t\t\t\t<div class=\"stars-in\" style=\"width: ". round(($row['stars']/10)*60) ."px;\" title=\"".$row['stars']."\"></div>\n";
			echo "\t\t\t\t\t\t</div>\n";
	if (isset($_SESSION['LoggedIn']) && isset($_SESSION['AccessLevel'])) {
		if ($_SESSION['LoggedIn'] === 1 && $_SESSION['AccessLevel'] >= 1)
		{
			echo "\t\t\t\t\t\t<a href=\"#?".$row['videofile']."\"><span class=\"download\">D/L</span></a>\n";
		}
	}
			echo "\t\t\t\t\t\t<div class=\"showing\" style=\"visibility:hidden;\">".$row['showing']."</div>\n";
			echo "\t\t\t\t\t</div>\n";
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