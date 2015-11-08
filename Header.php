<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/Main.css" />
	<link href="css/Tabcontent.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.js" ></script>
	<script type="text/javascript" src="js/tabcontent.js" ></script>
	<script type="text/javascript" >
	function confirmReq()
	{
		var ret = confirm("You Shirley Wanna Go Ahead?");
		return ret;
	}
	</script>
	
	<?php require_once('Worm.php'); ?>
	
	<script type="text/javascript" src="js/canvasjs.min.js"></script>
</head>
	<body>
		<div id="container" >
		<div id="header" ></div>
		<div id="navBar" >
			<div class='navtabs' ><a href='My_Predictions.php' > My Predictions </a></div>
			<div class='navtabs' ><a href='Round_Predictions.php' > Community Predictions </a></div>
			<div class='navtabs' ><a href='My_Leagues.php' > League Standings </a></div>
			<div class='navtabs' ><a href='Round_History.php' > My Rounds History </a></div>
		</div>
		<div id="navBar" >
			<div class='navtabs' ><a href='Stats.php' > Worms </a></div>
			<div class='navtabs' ><a href='Account.php' > My Account </a></div>
			<?php
				if( Ikts::isAdmin($user) )
				{
					echo "<div class='admintabs' ><a href='Admin.php' > Admin </a></div>";
				}
			?>
			<div class='navtabs' ><a href='Logout.php' > Logout (<?php echo $user; ?>) </a></div>
		</div>
		<div id="main" >
