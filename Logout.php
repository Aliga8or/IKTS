<?php 
require_once('Connection.php');

session_start();
if( ( isset($_SESSION['player']) ) )
{
	session_destroy();
}
$host = $_SERVER['HTTP_HOST'];
$page = "index.php";
header("Location: http://$host/$folder$page");
?>