<?php
$dbhost = "localhost";
$dbname = "wallam";
$dbuser = "test";
$dbpass = "test";

$connectionOptions = array("Database"=>"wallam", "UID"=>"test", "PWD"=>"test");

$conn = sqlsrv_connect("localhost", $connectionOptions);
if ($conn === false) {
	die(print_r( sqlsrv_errors(), true));
}

//Gets XML file
//Returns a string (XML file)
if (!function_exists('getimdbXML')){
function getimdbXML($imdbid) {
	$url = "http://www.myapifilms.com/imdb?idIMDB=".$imdbid."&format=XML";
	//$url = "http://mymovieapi.com/?id=".$imdbid."&type=xml";
	//echo $url."\n";
	return curl($url);
}
}

if (!function_exists('getXML')){
function getXML($title, $year) {
	//This is where you put title changes (for things like slashes and colons in movie titles
	$title = str_replace(" ", "%20", $title);
	$url = "http://www.omdbapi.com/?s=".$title."&y=".$year."&r=XML";
	$searchTitle = parseResults(curl($url), $title);
	$searchTitle = str_replace(" ", "%20", $searchTitle);
	$url = "http://www.omdbapi.com/?t=".$searchTitle."&y=".$year."&r=XML";
	//echo $url;
	return curl($url);
}
}

if (!function_exists('curl')){
function curl($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}
}

if (!function_exists('parseResults')){
function parseResults($xmll, $title) {
	$xml = new SimpleXMLElement($xmll);
	$closest = $title;
	$shortest = -1;
	foreach($xml->Movie as $movie) {
		//echo $movie['Title']." - ";
		$lev = levenshtein($title, $movie['Title']);
		if ($lev == 0) {
			$closest = $movie['Title'];
			$shortest = 0;
			break;
		}
		if ($lev <= $shortest || $shortest < 0) {
			$closest = $movie['Title'];
			$shortest = $lev;
		}
	}

	//print_r($xmll);
	echo $title." [".$closest."]";
	//die('test');
	return $closest;
}
}

if (!function_exists('parseimdbXML')){
function parseimdbXML($xmll, $title) {
	$xml = new SimpleXMLElement($xmll);
	if ($xml->code == "404") {
		global $year;
		echo "\tFilm not found:".$title."\n";
		//die("film not found");
		//echo "\tTrying omdbapi.com... \n";

		return 0; //parseXML(getXML($title, $year))
	}

	//Now we save data from the xml file
	$data['title'] = $xml->title;
	$data['year'] = $xml->year;
	$data['rated'] = $xml->rated;
	$data['released'] = (string)$xml->releaseDate;
	$data['runtime'] = $xml->runtime;
	$data['genre'] = $xml->genres;
	$data['director'] = $xml->directors;
	$data['writer'] = $xml->writers;
	$data['actors'] = $xml->actors;
	$data['plot'] = $xml->simplePlot;
	$data['poster'] = $xml->urlPoster;
	$data['imdbRating'] = $xml->rating;
	$data['imdbVotes'] = $xml->rating_count;
	$data['imdbID'] = $xml->idIMDB;

//print_r($data);

	if ((string)$data['imdbRating'] === '' || $data['imdbRating'] == NULL) { $movie['imdbRating'] = 0; }
	if ((string)$data['released'] === '' || $data['released'] == NULL) { $movie['released'] = NULL; }
	if ((string)$data['runtime'] === '' || $data['runtime'] == NULL) { $movie['runtime'] = "0 h 0 min"; }
	if ((string)$data['rated'] === '' || $data['rated'] == NULL) { $movie['rated'] = 0; }

if (!($data['released'] === '')) { //This is stupid... why do they send an invalid date?
	if (substr((string)$data['released'], 0, 4) == "0000")
		$data['released'] = "1900".substr((string)$data['released'], 4, 2).substr((string)$data['released'], 6, 2);
	if (substr((string)$data['released'], 4, 2) == "00")
		$data['released'] = substr((string)$data['released'], 0, 4)."01".substr((string)$data['released'], 6, 2);
	if (substr((string)$data['released'], 6, 2) == "00")
		$data['released'] = substr((string)$data['released'], 0, 4).substr((string)$data['released'], 4, 2)."01";
	
	$data['released'] = substr((string)$data['released'], 0, 4)."/".substr((string)$data['released'], 4, 2)."/".substr((string)$data['released'], 6, 2);
}

	return $data;
}
}

if (!function_exists('parseXML')){
function parseXML($xmll) {
	//Hmmmm... probably best to not do this
	//Can return an array with the values...which is what it currently does.

//defaults
	$movie['title']='';
	$movie['year']='';
	$movie['rated']=0;
	$movie['released']="1 Jan 1970";
	$movie['runtime']="0 h 0 min";
	$movie['genre']='';
	$movie['director']='';
	$movie['writer']='';
	$movie['actors']='';
	$movie['plot']='';
	$movie['poster']='';
	$movie['imdbRating']=0;
	$movie['imdbVotes']=0;
	$movie['imdbID']='';
	$movie['type']='';

	$xml = new SimpleXMLElement($xmll);
	$movie['title']=$xml->movie['title'];
	$movie['year']=$xml->movie['year'];
	$movie['rated']=$xml->movie['rated'];
	$movie['released']=$xml->movie['released'];
	$movie['runtime']=$xml->movie['runtime'];
	$movie['genre']=$xml->movie['genre'];
	$movie['director']=$xml->movie['director'];
	$movie['writer']=$xml->movie['writer'];
	$movie['actors']=$xml->movie['actors'];
	$movie['plot']=$xml->movie['plot'];
	$movie['poster']=$xml->movie['poster'];
	$movie['imdbRating']=$xml->movie['imdbRating'];
	$movie['imdbVotes']=$xml->movie['imdbVotes'];
	$movie['imdbID']=$xml->movie['imdbID'];
	$movie['type']=$xml->movie['type'];

	if ((string)$movie['imdbRating'] === 'N/A') { $movie['imdbRating'] = 0; }
	if ((string)$movie['released'] === 'N/A') { $movie['released'] = NULL; }
	if ((string)$movie['runtime'] === 'N/A') { $movie['runtime'] = "0 h 0 min"; }
	if ((string)$movie['rated'] === 'N/A') { $movie['rated'] = 0; }

	$runtime = explode(" ", $movie['runtime']);
	$movie['runtime'] = ($runtime[0]*60) + $runtime[2];


	return $movie;
}
}

if (!function_exists('getImage')){
function getImage($url, $fileName) {
	$save = "C:\\inetpub\\wwwroot\\wallam\\posters\\".$fileName.".png";
	if (isset($url)) copy($url, $save);
}
}

//STARTS HERE
//echo "Starting....\n";

//Getting theaterid
$sql = "SELECT theaterid FROM Theaters WHERE name = ?";
$stmt = sqlsrv_query(&$conn, $sql, array( $theatername ));
if( $stmt === false) {
	die( print_r( sqlsrv_errors(), true) );
}
if(sqlsrv_has_rows($stmt) === true)
{
	//Theater is in the database already
	$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
	$theaterid = $row['theaterid'];
} else {
	$theaterid = NULL;
}

//Check to see if we already have the movie in the database. Don't want to call APIs if not needed
$sql = "SELECT movieid, imdbid, title, description FROM Movies WHERE imdbid = ?";
$stmt = sqlsrv_query(&$conn, $sql, array( $imdbid ));
if( $stmt === false) {
	die( print_r( sqlsrv_errors(), true) );
}
	
if(sqlsrv_has_rows($stmt) === true)
{
	//Movie is in the database already
	$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
	//echo "Skipping '".$movieName."'. Movie is in the database with the following information: \n";
	//print_r($row);
	//echo "<br />";
	
	//Try to update items that are empty or null
	$xmlfile = getimdbXML($imdbid);
	$movieInfo = parseimdbXML($xmlfile, $movieName);
	if ($row['description'] == '')
	{
		//update this column
		$sql = "UPDATE Movies set description = ? where imdbid = ?";
		$stmt = sqlsrv_query(&$conn, $sql, array((string) $movieInfo['plot'],(string) $imdbid));
	}
	
} else {
	//echo "Adding '".$movieName."' to the database. <br />";
	//Movie not in database yet, get everything!

$xmlfile = getimdbXML($imdbid);
//print_r($xmlfile);
$movieInfo = parseimdbXML($xmlfile, $movieName);
$file = (string) $movieInfo['imdbID'];
//print_r($movieInfo['actors']['item'][0]);
getImage($movieInfo['poster'], $file);

$runtime = filter_var($movieInfo['runtime'], FILTER_SANITIZE_NUMBER_INT);
$movieInfo['runtime'] = $runtime;

			if (!isset($movieInfo['rated'])) $movieInfo['rated']="";
			$rating = 0;
			if ((string)$movieInfo['rated'] === "G")  $rating = 1;
			if ((string)$movieInfo['rated'] === "PG")  $rating = 2;
			if ((string)$movieInfo['rated'] === "PG-13" || (string)$movieInfo['rated'] === "PG_13")  $rating = 3;
			if ((string)$movieInfo['rated'] === "R")  $rating = 4;
			if ((string)$movieInfo['rated'] === "NC-17" || (string)$movieInfo['rated'] === "NC_17")  $rating = 5;
			$movieInfo['rated'] = $rating;

			//print_r($movieInfo);
			//Write to Database
			$sql = "
IF (?  not in (SELECT imdbid FROM Movies WHERE imdbid = ?))
INSERT INTO Movies (imdbid, title, stars, release, runtime, description, rating, poster, trailer, videofile) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$stmt = sqlsrv_query(&$conn, $sql, array( 	(string) $movieInfo['imdbID'],(string) $movieInfo['imdbID'],
									(string) $movieInfo['imdbID'], 
									(string) $movieInfo['title'], 
									(float) $movieInfo['imdbRating'], 
									(string) $movieInfo['released'], 
									$runtime, 
									(string) $movieInfo['plot'], 
									$rating, 
									$file.'.png', 
									$trailer, NULL));

			if($stmt)
			{
				//echo "SQL Query Successful!\n";
			}
			else
			{
				echo "SQL Query Failed\n";
				print_r($movieInfo);
				die( print_r( sqlsrv_errors(), true) );
			} 
	//echo "Finished...";
}

//Getting movieid
$sql = "SELECT movieid FROM Movies WHERE imdbid = ?";
$stmt = sqlsrv_query(&$conn, $sql, array( $imdbid ));
if( $stmt === false) {
	die( print_r( sqlsrv_errors(), true) );
}
	
if(sqlsrv_has_rows($stmt) === true)
{
	//Movie is in the database
	$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
	$movieid = $row['movieid'];
} else {
	$movieid = NULL;
}
if ($theaterid != NULL AND $movieid != NULL){
	//Adding showtimes to database
	foreach ($times[0] as $time) {
		$time = date("m/d/Y")." ".str_replace('&#','pm',$time);
		echo $movieName." @ ".$time."\n";
		
		//Write Showtime to Database
		$sql = "INSERT INTO Showtimes (theaterid, movieid, ddd, time) VALUES (?, ?, ?, ?)";
		$stmt = sqlsrv_query(&$conn, $sql, array($theaterid, $movieid, $ddd, $time));

		if($stmt)
		{
			//echo "SQL Query Successful!\n";
		}
		else
		{
			//echo "SQL Query Failed\n";
			$errors = sqlsrv_errors();
			if (strpos($errors[0]['message'], "Cannot insert duplicate key") === false)
			{
				die( print_r( sqlsrv_errors(), true) );
			} else {
				//echo "Skipping... already in database";
			}
		}
	}
}
sqlsrv_close($conn);
?>