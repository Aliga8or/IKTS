<?php 
require_once('Connection.php');
require_once('Ikts.class.php');
require_once('Session.php');

Ikts::getGlobals();

$max_round = 0;
$query = "select max(round) as round from score ";
$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
if($max = mysql_fetch_assoc($result))
{
	$max_round = $max['round'];
}

$player = $_SESSION['player'];

$ttab = "";

for( $round = $max_round; $round >= 1; $round-- )
{
	$ttab .= "<div class='eList' >
			";
	$ttab .= "<table>
			 ";
	$ttab .= "<tr class='tabHeader' > <td>Round ".$round."</td> </tr>
			 ";
	$ttab .= "</table>
			 ";
	$ttab .= "</div>
		 ";
		 
			 
	$ttab .= "<div class='eList' >
			";
	$ttab .= "<table>
			 ";
	$ttab .= "<tr class='tabHeader' > <td></td> <td>#</td> <td>Player</td> <td>Round</td> <td>Total</td> </tr>
			 ";

	$col = 0;
	$query = "select * from score where round='".$round."' order by agg_rank";
	$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
	while($score = mysql_fetch_assoc($result))
	{
		if($score['trans'] > 0)
		{
			$trans = $score['trans'];
			$arrow = "green";
		}
		elseif($score['trans'] < 0)
		{
			$trans = -1 * $score['trans'];
			$arrow = "red";
		}
		else
		{
			$trans = $score['trans'];
			$arrow = "grey";
		}
		
		$ttab .= "<tr class='row".($col%2)."' >
				 ";
	
		$ttab .= "<td>(".$trans.") <img src='img/".$arrow.".png' width='".IMG_SIZE."' height='".IMG_SIZE."' > </td>
				 ";
		$ttab .= "<td>".$score['agg_rank']."</td>
				 ";
		$ttab .= "<td>".$score['player']."</td>
				 ";
		$ttab .= "<td>".$score['pts']."</td>
				 ";
		$ttab .= "<td>".$score['agg_pts']."</td>
				 ";
	
		$ttab .= "</tr>
				 ";
		
		$col ++;
	}

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
