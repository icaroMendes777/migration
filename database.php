<?php



/*

==================================

RETIRAR EM PRODUÇÃO!!!!!!!!

==================================

*/


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);




$servername = "localhost";
$username = "root";
$password = "MyNewPass";
$dbname = "wp1";



// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);



if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

