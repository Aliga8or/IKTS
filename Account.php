<?php
require_once('Connection.php');
require_once('Ikts.class.php');
require_once('Session.php');
	
$err_msg = "";

if( isset( $_POST['submit'] ) )
{	
	$query = "select * from player where player='".$_SESSION['player']."' and pass='".$_POST['oldPass']."' ";
	$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
	if($player = mysql_fetch_assoc($result))
	{
		$query = "update player set pass='".$_POST['newPass']."' where player='".$_SESSION['player']."' ";
		mysql_query($query) or die ("Error in query: $query. ".mysql_error());
		$err_msg = "Done. ";
	}
	else
	{
		$err_msg = "You don't remember the old one, do you? ";
	}
}
?>
<!--Page starts here-->

<?php
require_once("Header.php");

?>
				<div class='eList' >
					<form name="loginForm" method="POST" action="Account.php" >
						<table>
							<tr>
								<td>Old Magic Words</td> <td><input name="oldPass" type="password" /></td>
							</tr>
							<tr>
								<td>New Magic Words</td> <td><input name="newPass" type="password" /></td>
							</tr>
							<tr>
								<td align='center' colspan='2' >
									<button type='submit' name='submit' value='submit' style='background-color:inherit; border:0; cursor:pointer;' >
										<img src='img/submit.png' width='40' height='40' />
									</button>
								</td>
							</tr>
							<tr class='errBlock' >
								<td align='center' colspan='3' >
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
