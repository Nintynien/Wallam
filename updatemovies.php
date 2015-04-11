<?php
include 'base.php';

echo "Starting....\n";
$files = scandir('C:\\inetpub\\wwwroot\\Movies\\content\\linked\\');
$i = 0;

//for each file in the directory
while (isset($files[$i])) {
	$fileExt = strtoupper(substr($files[$i], strrpos($files[$i], '.')+1));
	if ($fileExt === "AVI" || $fileExt === "MKV" || $fileExt === "MP4") {
		$file = removeQuality($files[$i]);
		$year = getYear($file);
		if (isset($year)) $file = removeX($file, $year);
		$movieName = str_replace(".", " ", $file);
		//$xmlfile = getimdbXML('Chick Magnet', '2011');
		$xmlfile = getimdbXML($movieName, $year);

		$movieInfo = parseimdbXML($xmlfile, $movieName);
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
$sql = "INSERT INTO Movies (imdbid, title, stars, release, runtime, description, rating, poster, trailer, movie) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$stmt = sqlsrv_query(&$conn, $sql, array( 	(string) $movieInfo['imdbID'], 
									(string) $movieInfo['title'], 
									(float) $movieInfo['imdbRating'], 
									(string) $movieInfo['released'], 
									$runtime, 
									(string) $movieInfo['plot'], 
									$rating, 
									$file.'.png', 
									null, $files[$i]));

			if($stmt)
			{
				echo " - success!\n";
			}
			else
			{
				echo " - failed\n";
				print_r( $movieInfo);
				die( print_r( sqlsrv_errors(), true) );
			} 
	}
	$i++;
}
echo "Finished...";

//Strips file name of commonly used variables
//Returns file name
function removeQuality($fileName) {
	$fileName = str_replace(".avi", "", $fileName);
	$fileName = str_replace(".mkv", "", $fileName);
	$fileName = str_replace(".mp4", "", $fileName);
	$fileName = str_replace(".1080p", "", $fileName);
	$fileName = str_replace(".720p", "", $fileName);
	$fileName = str_replace(".SD", "", $fileName);
	$fileName = str_replace(".CAM", "", $fileName);

	return $fileName;
}

//Finds movie year from file name (if there is one)
//Returns year or NULL
function getYear($fileName) {
	$year = NULL;
	$i = strrpos($fileName, ".");
	$length = strlen($fileName);
	$ending = substr($fileName, $i+1, $length-$i);
	
	if (is_numeric($ending)) {
		if ($ending > 1920 && $ending < date('Y')+1) {
			$year = $ending;
		}
	}
	return $year;
}

//Removes a provided string from the file name (also removes the period before the string)
//Returns a string
function removeX($fileName, $remove) {
	$fileName = str_replace(".".$remove, "", $fileName);
	return $fileName;
}

//Gets XML file
//Returns a string (XML file)
function getimdbXML($title, $year) {
	$title = str_replace(" ", "%20", $title);
	$url = "http://imdbapi.org/?title=".$title."&type=xml&limit=5";
	$url .= (isset($year)) ? "&year=".$year."&yg=1" : "&yg=0";
	//echo $url."\n";
	return curl($url);
}

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

function curl($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}
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

function parseimdbXML($xmll, $title) {
	$xml = new SimpleXMLElement($xmll);
	if ($xml->code == "404") {
		global $year;
		echo "\tFilm not found:".$title."\n";
		echo "\tTrying omdbapi.com... \n";

		return parseXML(getXML($title, $year));
	}
	$levs[] = array();
	$closest = $title;
	$shortest = -1;
	$index = 0;
	foreach($xml->item as $movie) {
		//echo $movie->title." - ";
		$lev = levenshtein($title, $movie->title);
		$levs[$index] = $lev;
		if ($lev == 0) {
			$closest = $movie->title;
			$shortest = 0;
			break;
		}
		if ($lev <= $shortest || $shortest < 0) {
			$closest = $movie->title;
			$shortest = $lev;
		}
		$index++;
	}

	$movieindex = array_keys($levs, min($levs)); //This is the index for the closest match
	//echo $movieindex[0]."\n";

	echo $title." [".$closest."]";
	//Now we save data from the closest match
	$data['title'] = $xml->item[$movieindex[0]]->title;
	$data['year'] = $xml->item[$movieindex[0]]->year;
	$data['rated'] = $xml->item[$movieindex[0]]->rated;
	$data['released'] = (string)$xml->item[$movieindex[0]]->release_date;
	$data['runtime'] = $xml->item[$movieindex[0]]->runtime->item;
	$data['genre'] = $xml->item[$movieindex[0]]->genres;
	$data['director'] = $xml->item[$movieindex[0]]->directors;
	$data['writer'] = $xml->item[$movieindex[0]]->writers;
	$data['actors'] = $xml->item[$movieindex[0]]->actors;
	$data['plot'] = $xml->item[$movieindex[0]]->plot_simple;
	$data['poster'] = $xml->item[$movieindex[0]]->poster;
	$data['imdbRating'] = $xml->item[$movieindex[0]]->rating;
	$data['imdbVotes'] = $xml->item[$movieindex[0]]->rating_count;
	$data['imdbID'] = $xml->item[$movieindex[0]]->imdb_id;

//echo "[".(string)$data['released']."]".$data['released']."\n";
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

function getImage($url, $fileName) {
	$save = "C:\\inetpub\\wwwroot\\wallam\\posters\\".$fileName.".png";
	if (isset($url)) copy($url, $save);
}



?>