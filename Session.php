<?php 
Ikts::getGlobals();

date_default_timezone_set('UTC');
if(strtotime('now') >= Ikts::deadline(Ikts::$curr_round) )
{
	Ikts::closeRound(Ikts::$curr_round);       
}

session_start();
if( !( isset($_SESSION['player']) ) )
{
	$host = $_SERVER['HTTP_HOST'];
	$page = "index.php";
	header("Location: http://$host/$folder$page");
}
else
{
	$user = $_SESSION['player'];
}
?>
