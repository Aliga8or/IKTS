<?php 
require_once('Connection.php');
require_once('Ikts.class.php');
require_once('Session.php');

Ikts::getGlobals();

$player = $_SESSION['player'];
$round = Ikts::$curr_round;

if( isset($_POST['submit']) )
{
	$query = "select * from main where player='".$player."' and round='".$round."' ";
	$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
	while($main = mysql_fetch_assoc($result))
	{
		$mid = $main['mid'];
		$predA = $_POST[$mid.'A'];
		$predB = $_POST[$mid.'B'];
		
		if( isset($_POST['banker']) )
		{
			if( $_POST['banker'] == $mid )
			{
				$banker= 1;
			}
			else
			{
				$banker = 0;
			}
		}
		else
		{
			$banker = 0;
		}
		
		Ikts::getPrediction($mid, $predA, $predB, $banker);
	}
}

$ttab = "<div class='eList' >
		";
$ttab .= "<form name='mainForm' method='POST' action='My_Predictions.php'>
		 ";
$ttab .= "<table>
		 ";
$ttab .= "<tr class='tabHeader' > <td colspan=3 >Round ".$round."</td> <td><img src='img/banker_on.png' width='".IMG_SIZE."' height='".IMG_SIZE."' ></td> </tr>
			 ";

$col = 0;
$query = "select * from main where player='".$player."' and round='".$round."' ";
$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
while($main = mysql_fetch_assoc($result))
{
	$query1 = "select * from fixture where fid='".$main['fixture']."' ";
	$result1 = mysql_query($query1) or die ("Error in query: $query1. ".mysql_error());
	if($fixture = mysql_fetch_assoc($result1))
	{
		$ttab .= "<tr class='row".($col%2)."'>
				 ";
		
		$ttab .= "<td>".$fixture['teamA']."</td>
				 ";
		$ttab .= "<td>
					<input name='".$main['mid']."A' type='text' value='".$main['predA']."' > - 
					<input name='".$main['mid']."B' type='text' value='".$main['predB']."' >
				  </td>
				 ";
		$ttab .= "<td>".$fixture['teamB']."</td>
				 ";
		$checked = "";
		if($main['banker'])
		{
			$checked = "checked";
		}
		
		$default = "";
		if($col == 0)
		{
			$default = "checked";
		}
		
		$ttab .= "<td><input name='banker' type='radio' value='".$main['mid']."' ".$checked." ".$default." ></td>
				 ";
		
		$ttab .= "</tr>
				 ";
	}
	
	$col++;
}

$ttab .= "<tr><td colspan=4>
				<button type='submit' name='submit' value='submit' style='background-color:inherit; border:0; cursor:pointer;' >
					<img src='img/submit.png' width='40' height='40' />
				</button>
		</td></tr>
		";
$ttab .= "</table>
		 ";
$ttab .= "</form>
		 ";
$ttab .= "</div>
		 ";
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
