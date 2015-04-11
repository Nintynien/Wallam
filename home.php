<?php include "base.php"; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en-US" xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<title>Wallam</title>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
	<!--[if IE 6]>
		<link rel="stylesheet" href="css/ie6.css" type="text/css" media="all" />
	<![endif]-->
	<link rel="shortcut icon" type="image/x-icon" href="css/images/favicon.png" />
	
	<script src="js/jquery-1.4.2.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.jcarousel.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/functions.js" type="text/javascript" charset="utf-8"></script>
	
</head>
<body>

<?php include "header.php"; ?>

<div class="shell">
	<div class="container">
		
		<div class="slider">
			<div class="slider-holder">
				<ul>
<?php
	$sql = "SELECT TOP 4 Features.*, title, description FROM Features inner join Movies on Movies.movieid = Features.movieid ORDER BY CreateDate DESC";

	$stmt = sqlsrv_query(&$conn, $sql, array(date('m/d/y',strtotime("last Saturday"))) );
	if( $stmt === false) {
		die( print_r( sqlsrv_errors(), true) );
	}

	if(sqlsrv_has_rows($stmt) === true)
	{
		while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
			//echo "\t\t\t\t\t<li><img src=\"backdrops/".$row['backdrop']."\"/><a href=\"movies.php?id=".$row['movieid']."\"><title>".$row['title']."</title><plot>".$row['description']."</plot></a></li>\n";
			echo "\t\t\t\t\t<li><img style=\"background-image:url('backdrops/".$row['backdrop']."')\" src=\"backdrops/slide.png\"/><a href=\"movies.php?id=".$row['movieid']."\"><title>".$row['title']."</title><plot>".$row['description']."</plot></a></li>\n";
		}
	}
?>
				</ul>
			</div>
			
			<a href="#" title="Previous" class="slider-prev notext">prev</a>
			<a href="#" title="Next" class="slider-next notext">next</a>
			
			<div class="slider-nav">
				<ul>
				    <li><a href="#" title="1" >1</a></li>
				    <li><a href="#" title="2" >2</a></li>
				    <li><a href="#" title="3"  class="active">3</a></li>
				    <li><a href="#" title="4" >4</a></li>
				</ul>
			</div>
		</div>
		
		<div class="main">
	<!--
			<div class="cols">
				<div class="col">
					<div class="post">
						<h2><a href="#" title="#">Who Are We?</a></h2>
						<p><strong>Lorem Ipsum is simply dummy text of the printing and typesetting industry</strong>. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, <a href="#"  title="free website css templates">free website css templates</a> unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
					</div>
				</div>
			
				<div class="col">
					<div class="post">
						<h2><a href="#" title="#">What We Do?</a></h2>
						<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. <strong>Lorem Ipsum has been the industry's standard dummy</strong> text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. <a href="#" title="free website css templates">free website css templates</a> with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing </p>
					</div>
				</div>
				
				<div class="col-last">
					<div class="post">
						<h2><a href="#" title="#">Latest Project</a></h2>
						<p><strong>Lorem Ipsum is simply dummy text of the printing and typesetting industry</strong>. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, <a href="#" title="free website css templates">free website css templates</a> unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
					</div>
				</div>
				
				<div class="cl">&nbsp;</div>
			</div>
	-->
			<div class="content">
				<div class="post">
					<h2>Blogs</h2>
<?php
	$sql = "SELECT TOP(3) * FROM Blogs ORDER BY time DESC";
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
?>
				</div>
			</div>
			
			<div class="sidebar">
				<div class="post">
					<h2>Recently Updated</h2>
<?php
	$sql = "SELECT TOP(5) * FROM Movies ORDER BY UpdateDate DESC";
	$stmt = sqlsrv_query(&$conn, $sql);
	if( $stmt === false) {
		die( print_r( sqlsrv_errors(), true) );
	}

	if(sqlsrv_has_rows($stmt) === true)
	{
		while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
			echo "\t\t\t\t\t<h3><a href=\"movies.php?id=".$row['movieid']."\" title=\"".$row['title']."\">".$row['title']."</a><date>".$row['UpdateDate']->format('M j, Y')."</date></h3>\n";
		}
	}
?>
				</div>
			</div>
			
			<div class="cl">&nbsp;</div>
		</div>
		
	</div>
</div>


<?php include "footer.php"; ?>
</body>
</html>