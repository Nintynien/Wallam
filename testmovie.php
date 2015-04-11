<?php
include('base.php');
//Manual Moving Adding

$movieName = "R.I.P.D.";
$imdbid = 'tt0790736';
$trailer = 'http://www.youtube.com/watch?v=0k5Z9zEJCZo';
include('GetMovieInfo.php');

$movieName = "Pacific Rim";
$imdbid = 'tt1663662';
$trailer = 'http://www.youtube.com/watch?v=baw1FAzKuCo';
include('GetMovieInfo.php');

echo 'OK';

?>