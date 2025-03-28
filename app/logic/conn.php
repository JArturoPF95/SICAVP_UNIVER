<?php


/*
$servername = "sicavp.univer-gdl.info";
$username = "univer_sicavp";
$password = "TXGIsCQehq0y";
$nameDB = "univer_sicavp";
*/
/** TIC NG */

$servername = "127.0.0.1";
$username = "root";
$password = "";
$nameDB = "sicavp_univer";


// Create connection
$mysqli = new mysqli($servername, $username, $password, $nameDB);
$mysqli->set_charset("utf8");

// Check connection
if ($mysqli->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

//echo "Connected successfully";
