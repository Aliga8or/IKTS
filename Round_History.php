<?php 
require_once('Connection.php');
require_once('Ikts.class.php');
require_once('Session.php');

Ikts::getGlobals();

$player = $_SESSION['player'];
$ttab = "";

for($round = Ikts::$curr_round - 1; $round >=1; $round--)
{
	$ttab .= "<div class='eList' >
			";
	$ttab .= "<table>
			 ";
	$ttab .= "<tr class='tabHeader' ><td colspan=5 > Round ".$round." </td> <td></td> </tr>
			 ";

	$query = "select * from main where player='".$player."' and round='".$round."' and confirmed=1 ";
	$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
	while($main = mysql_fetch_assoc($result))
	{
		$query1 = "select * from fixture where fid='".$main['fixture']."' ";
		$result1 = mysql_query($query1) or die ("Error in query: $query1. ".mysql_error());
		if($fixture = mysql_fetch_assoc($result1))
		{
		
				$ttab .= "<tr class='row".$main['pts']."' >
						 ";
		
				$ttab .= "<td>".$fixture['teamA']."</td>
						 ";
				$ttab .= "<td>".$main['predA']."-".$main['predB']."</td>
						 ";
				$ttab .= "<td>".$fixture['teamB']."</td>
						 ";
				if($main['banker'])
				{
					$ttab .= "<td><img src='img/banker_on.png' width='".IMG_SIZE."' height='".IMG_SIZE."' ></td>
							 ";
				}
				else
				{
					$ttab .= "<td><img src='img/banker_off.png' width='".IMG_SIZE."' height='".IMG_SIZE."' style='opacity: 0.4; filter: alpha(opacity=40);' ></td>
							 ";
				}
			
				if($main['risk'])
				{
					$ttab .= "<td><img src='img/risk_on.png' width='".IMG_SIZE."' height='".IMG_SIZE."' ></td>
							 ";
				}
				else
				{
					$ttab .= "<td><img src='img/risk_off.png' width='".IMG_SIZE."' height='".IMG_SIZE."' style='opacity: 0.4; filter: alpha(opacity=40);' ></td>
							 ";
				}
			
				$ttab .= "<td>".$main['eff_pts']."</td>
							 ";
							 
				$ttab .= "<tr class='row1' ><td></td> <td> (".$fixture['scoreA']."-".$fixture['scoreB'].") </td> <td colspan=4 ></td> </tr>"
					  ;
		}
	}
	
	$rd_total = 0;
	$query = "select * from score where player='".$player."' and round='".$round."' ";
	$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
	if($score = mysql_fetch_assoc($result))
	{
		$rd_total = $score['pts'];
	}
	
	$ttab .= "<tr class='row0' > <td colspan=4 ></td> <td > Total: </td>  <td > ".$rd_total." </td></tr>"
				  ;

	$ttab .= "</table>
			 ";
	$ttab .= "</div>
			 ";
}
?>

<!--Page starts here-->

<?php
//session_start();
require_once("Header.php");

echo $ttab;
?>
			</div>
		</div>
	</body>
</html>
