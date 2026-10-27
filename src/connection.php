<?php
$dbhost = "localhost";
$dbuser = "joe.maycock";
$dbpass = "CEUPQSG6";
$dbname = "joemaycock_assignment";

if(!$con = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{

	die("failed to connect!");
}
if(mysqli_connect_errno())
{
	die("failed to connect!");
}
?>