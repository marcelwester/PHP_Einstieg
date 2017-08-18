<?php
/*
 * Created on 28.10.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

if (isset($_SESSION["userid"])) {
	echo '<table class="layout" width="80%" align="center">';
	echo '<tr>';
	   echo '<td class="layout" align="center">';
	   	 echo '<h1><u>Aktion</u></h1>';
	   echo '</td>';
	echo '</tr>';
	echo '<tr><td class="layout" align="center">';
		echo '<table class="layout" width="90%">';
					echo '<tr>';
						table_link("<h1>Monitore</h1>","index.php?site=monitor&PHPSESSID=".session_id());
					echo '</tr>';
					

					echo '<tr>';
						table_link("<h1>Info</h1>","index.php?site=info&PHPSESSID=".session_id());
					echo '</tr>';
					
					if ($groupid==10) {					
						echo '<tr>';
							table_link("<h1>Ereignisanzeige</h1>","index.php?site=mlog&PHPSESSID=".session_id());
						echo '</tr>';
					}
					if ($groupid==10) {					
						echo '<tr>';
							table_link("<h1>Administration</h1>","index.php?site=admin&PHPSESSID=".session_id());
						echo '</tr>';
					}
					
					
					echo '<tr>';
						table_link("<h1>Profil &auml;ndern</h1>","index.php?site=login&action=edituser&PHPSESSID=".session_id());
					echo '</tr>';

					echo '<tr>';
						table_link("<h1>Logout</h1>","index.php?site=login&action=logout&PHPSESSID=".session_id());
					echo '</tr>';

		echo '</table>';
	echo '</td></tr>';
echo '</table>';

}

?>
