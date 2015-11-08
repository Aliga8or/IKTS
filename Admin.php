<?php 
require_once('Connection.php');
require_once('Ikts.class.php');
require_once('Session.php');

Ikts::getGlobals();

$err_msg = "";
$round = Ikts::$curr_round - 1;

if( isset($_POST['submit']) )
{
	$query = "select * from fixture where round='".$round."' ";
	$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
	while($fixture = mysql_fetch_assoc($result))
	{
		$fid = $fixture['fid'];
		$scoreA = $_POST[$fid.'A'];
		$scoreB = $_POST[$fid.'B'];
		
		Ikts::updateFixture($fid, $scoreA, $scoreB );
		$err_msg = "Fixtures successfully updated for round ".$round."";
	}
}

if( isset($_POST['calc']) )
{
	Ikts::updateGame($_POST['rd']);
	$err_msg = "GAME Updated for round ".$_POST['rd'].". Check Community Predictions and My Leagues.";
}

if( isset($_POST['surrogate']) )
{
	$query = "select * from main where player='".$_POST['friend']."' and round='".Ikts::$curr_round."' ";
	$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
	while($main = mysql_fetch_assoc($result))
	{
		$mid = $main['mid'];
		$fixture = $main['fixture'];
		$predA = $_POST[$fixture.'A'];
		$predB = $_POST[$fixture.'B'];
		
		if( isset($_POST['banker']) )
		{
			if( $_POST['banker'] == $fixture )
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
	$err_msg = "".$_POST['friend']."'s ass saved. Not sure? Enter again.";
}

$etab = "<div class='eList' >
		";
$etab .= "<table>
		 ";
$etab .= "<tr class='errBlock' > <td >".$err_msg."</td> </tr>
			 ";
$etab .= "</table>
		 ";
$etab .= "</div>
		 ";


$ttab = "<div class='eList' >
		";
$ttab .= "<form name='mainForm' method='POST' action='Admin.php'>
		 ";
$ttab .= "<table>
		 ";
$ttab .= "<tr class='tabHeader' > <td colspan=3 >Round ".$round."</td> </tr>
			 ";

$col = 0;
$query = "select * from fixture where round='".$round."' ";
$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
while($fixture = mysql_fetch_assoc($result))
{
	$ttab .= "<tr class='row".($col%2)."'>
			 ";
	
	$ttab .= "<td>".$fixture['teamA']."</td>
			 ";
	$ttab .= "<td>
				<input name='".$fixture['fid']."A' type='text' value='".$fixture['scoreA']."' > - 
				<input name='".$fixture['fid']."B' type='text' value='".$fixture['scoreB']."' >
			  </td>
			 ";
	$ttab .= "<td>".$fixture['teamB']."</td>
			 ";
	
	$ttab .= "</tr>
			 ";
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

		 
$ctab = "<div class='eList' >
		";
$ctab .= "<form name='calcForm' method='POST' action='Admin.php'>
		 ";
$ctab .= "<table>
		 ";
$ctab .= "<tr class='tabHeader' > <td colspan=2 >Calculate & Update Points</td> </tr>
			 ";
$ctab .= "<tr class='row1'><td> Round: <select name='rd'> 
		 ";
		 
$query = "select distinct round from fixture where round <= '".$round."' order by round desc";
$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
while($rd = mysql_fetch_assoc($result))
{
	$ctab .= "<option value='".$rd['round']."' >".$rd['round']."</option>
			 ";
}
$ctab .= "</select> </td>
		 ";
$ctab .= "<td>
				<button type='submit' name='calc' value='calc' style='background-color:inherit; border:0; cursor:pointer;' >
					<img src='img/calculate.png' width='40' height='40' />
				</button>
		</td></tr>
		";
$ctab .= "<tr class='errBlock' > <td colspan=2>* Calculate only after you have correctly submitted the round Fixture scores.</td> </tr>
			 ";
$ctab .= "</table>
		 ";
$ctab .= "</form>
		 ";
$ctab .= "</div>
		 ";
		 

$ftab = "<div class='eList' >
		";
$ftab .= "<form name='friendForm' method='POST' action='Admin.php'>
		 ";
$ftab .= "<table>
		 ";
$ftab .= "<tr class='tabHeader' > <td colspan=4 >Enter Predictions for a Lazy/Busy Friend for Round ".Ikts::$curr_round."</td> </tr>
			 ";
$ftab .= "<tr class='row1'><td> Friend: <select name='friend'> 
		 ";

$query = "select player from player";
$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
while($fr = mysql_fetch_assoc($result))
{
	$ftab .= "<option value='".$fr['player']."' >".$fr['player']."</option>
			 ";
}
$ftab .= "</select> </td> </tr>
		 ";

$checked = "checked";
$col = 0;
$query = "select * from fixture where round='".Ikts::$curr_round."' ";
$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
while($fixture = mysql_fetch_assoc($result))
{
	$ftab .= "<tr class='row".($col%2)."'>
			 ";
	
	$ftab .= "<td>".$fixture['teamA']."</td>
			 ";
	$ftab .= "<td>
				<input name='".$fixture['fid']."A' type='text' > - 
				<input name='".$fixture['fid']."B' type='text' >
			  </td>
			 ";
	$ftab .= "<td>".$fixture['teamB']."</td>
			 ";
	$ftab .= "<td><input name='banker' type='radio' value='".$fixture['fid']."' ".$checked." ></td>
				 ";
	if( $checked == "checked" )
	{
		$checked = "";
	}
	
	$ftab .= "</tr>
			 ";
	$col++;
}

$ftab .= "<tr><td colspan=4>
				<button type='submit' name='surrogate' value='surrogate' style='background-color:inherit; border:0; cursor:pointer;' >
					<img src='img/submit.png' width='40' height='40' />
				</button>
		</td></tr>
		";

$ftab .= "</table>
		 ";
$ftab .= "</form>
		 ";
$ftab .= "</div>
		 ";
?>

<!--Page starts here-->

<?php
//session_start();
require_once("Header.php");

echo $etab;
echo $ttab;
echo $ctab;
echo $ftab;
?>
			</div>
		</div>
	</body>
</html>
