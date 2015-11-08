<?php
require_once('Connection.php');
require_once('Ikts.class.php');
	
$err_msg = "";

if( isset( $_POST['submit'] ) )
{	
	$query = "select * from player where player='".$_POST['player']."' ";
	$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
	if($player = mysql_fetch_assoc($result))
	{
		if( $_POST['pass'] == $player['pass'] )
		{
			session_start();	
			$_SESSION['player'] = $player['player'];
			$_SESSION['admin'] = $player['admin'];
			
			$host = $_SERVER['HTTP_HOST'];
			$page = "My_Predictions.php";
			header("Location: http://$host/$folder$page");
		}
		else
		{
			$err_msg = "You messed up your Magic Words ".$player['player'];
		}
	}
	else
	{
		$err_msg = "You do not exist in the System";
	}
}
?>
<!--Page starts here-->

<?php
require_once("Header.php");

?>
				<div class='eList' >
					<form name="loginForm" method="POST" action="index.php" >
						<table>
							<tr>
								<td>Player</td> <td><input name="player" type="text" /></td>
							</tr>
							<tr>
								<td>Magic Words</td> <td><input name="pass" type="password" /></td>
							</tr>
							<tr>
								<td align='center' colspan='2' >
									<button type='submit' name='submit' value='submit' style='background-color:inherit; border:0; cursor:pointer;' >
										<img src='img/login.png' width='40' height='40' />
									</button>
								</td>
							</tr>
							<tr class='errBlock' >
								<td colspan='3' >
								<?php echo $err_msg; ?>
								</td>
							</tr>
						</table>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>
