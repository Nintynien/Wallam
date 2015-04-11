<?php
session_start();

$dbhost = "localhost";
$dbname = "wallam";
$dbuser = "test";
$dbpass = "test";

$connectionOptions = array("Database"=>"wallam", "UID"=>"test", "PWD"=>"test");

$conn = sqlsrv_connect("localhost", $connectionOptions);
if ($conn === false) {
	die(print_r( sqlsrv_errors(), true));
}
?>