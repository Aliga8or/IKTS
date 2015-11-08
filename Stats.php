<?php 
require_once('Connection.php');
require_once('Ikts.class.php');
require_once('Session.php');

Ikts::getGlobals();

$etab = "<div class='eList' >
		";
$etab .= "<table>
		 ";
$etab .= "<tr> <td class='errBlock' >* Click on Player names to remove/add them.</td> </tr>
			 ";
$etab .= "</table>
		 ";
$etab .= "</div>
		 ";

?>

<!--Page starts here-->

<?php
//session_start();
require_once("Header.php");
?>
				<div id="chartContainer" style="height: 700px; width: 100%;">
				</div>
<?php 
echo $etab;
?>
			</div>
		</div>
	</body>
</html>
