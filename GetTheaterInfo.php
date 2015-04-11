<?php
$connectionOptions = array("Database"=>"wallam", "UID"=>"test", "PWD"=>"test");

$conn = sqlsrv_connect("localhost", $connectionOptions);
if ($conn === false) {
	die(print_r( sqlsrv_errors(), true));
}


			//Write to Database
			$sql = "
IF (?  not in (SELECT name FROM Theaters WHERE name = ?))
INSERT INTO Theaters (name, address, phone) VALUES (?, ?, ?)";
			$stmt = sqlsrv_query(&$conn, $sql, array($theatername, $theatername, $theatername, $theateraddress, $theaterphone));

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
sqlsrv_close($conn);
?>