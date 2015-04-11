<?php
/**
 * Google Showtime grabber
 * 
 * This file will grab the last showtimes of theatres nearby your zipcode.
 * Please make the URL your own! You can also add parameters to this URL: 
 * &date=0|1|2|3 => today|1 day|2 days|etc.. 
 * &start=10 gets the second page etc...
 * 
 * Please download the latest version of simple_html_dom.php on sourceForge:
 * http://sourceforge.net/projects/simplehtmldom/files/
 * 
 * @author Bas van Dorst <info@basvandorst.nl>
 * @version 0.1 
 * @package GoogleShowtime
 *
 * @modifyed by stephen byrne <gold.mine.labs@gmail.com>
 * @GoldMinelabs.com 
 */

require_once('simple_html_dom.php');
if(isset($_GET['near']))
{
	$near = str_replace(' ', '+',$_GET['near']);
}
else
{
	$near = 'kansas+city';
}
$curl = curl_init(); 
curl_setopt($curl, CURLOPT_URL, 'http://www.google.com/movies?near='.$near);  
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);  
$str = curl_exec($curl);  
curl_close($curl);  

$html = str_get_html($str);

print '<pre>';
foreach($html->find('#movie_results .theater') as $div) {
    // print theater and address info
	$theatername = $div->find('h2 a',0)->innertext;
    print "Theater: ".$theatername."\n";
	$theateraddress = $div->find('.info',0)->innertext;
	$theateraddress = strstr($theateraddress, ' - ', true);
    print "Address: ".$theateraddress."\n";
	$theaterphone = $div->find('.info',0)->innertext;
	$theaterphone = substr($theaterphone, strrpos($theaterphone,' - ') + 3);
	$theaterphone = strstr($theaterphone, '<', true);
	print "Phone:   ".$theaterphone."\n";
	include('GetTheaterInfo.php'); //writes theater to database
	
    // print all the movies with showtimes
    foreach($div->find('.movie') as $movie) {
		$movieName = urldecode($movie->find('.name a',0)->innertext);
        print "\tMovie:    ".$movieName."\n";
		$imdbid = $movie->find('.info',0)->innertext;
		$imdbid = substr($imdbid, strrpos($imdbid,' - ') + 3);
		$imdbid = substr($imdbid, strpos($imdbid,'/title/')+7);
		$imdbid = strstr($imdbid, '/', true); //IMDBid is solo'd out here
		print "\tIMDBid:   ".$imdbid."\n";
		$trailer = $movie->find('.info',0)->innertext;
		$trailer = substr($trailer, strrpos($trailer, ' - ',(strrpos($trailer, ' - ')-strlen($trailer)-1))+3);
		$trailer = substr($trailer, strpos($trailer, 'http://www.youtube.com/watch'));
		$trailer = strstr($trailer, '&', true);
		$trailer = str_replace('%3F','?',$trailer);
		$trailer = str_replace('%3D','=',$trailer);
		//Checking to make sure that we have a youtube link as the trailer. If not, we have a NULL trailer
		if (strpos($trailer, 'http://www.youtube.com/watch') === false) {
			$trailer = NULL;
		}
		print "\tTrailer:  ".$trailer."\n";
		print "\tInfo:     ".str_replace('&#8206;','',strstr($movie->find('.info',0)->innertext,' - ',true))."\n";
        $showtimes = $movie->find('.times',0)->innertext;
		//regex to get showtimes
		preg_match_all("/\d?\d:\d\d((pm)|(am)|(&#))/", $showtimes, $times);
		print "\tTime:     ".$showtimes."\n<br /><br />";
		
		//Saving the showtime as 3D if needed
		if (strpos($movieName, 'in 3D') === false) { $ddd = 0; } else { $ddd = 1; }
		
		//Save information to database
		include('GetMovieInfo.php');
    }
    print "\n\n";
}

// clean up memory
$html->clear();
?>