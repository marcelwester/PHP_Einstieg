<?php
////
//// ttv_team_tus.php
//// letzte Änderung : Daniel, 14.02.2004 17:35
//// was : Datei erstellt

if (priv("tt_tusteam"))
{
sitename("ttv_team_tus.php",$_SESSION["groupid"]);
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
	function popup(teamid)
	{
		var url;
		if (teamid == 0)
<?php
			echo 'url = "ttv_team_tus_popup.php?action=add&PHPSESSID='.session_id().'";';
		echo ' else ';
			echo 'url = "ttv_team_tus_popup.php?action=edit&teamid="+teamid+"&PHPSESSID='.session_id().'";';
?>
		window.open(url,"saison","width=650, height=300, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
	}
-->
</SCRIPT>

<?php

	$colspan_head = 2;

	if ($_SESSION["groupid"] > 0)
	{
		echo '<CENTER><TABLE WIDTH="40%" BORDER="0">';
			echo '<TR>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<a href="javascript:popup(0);">';
					echo '<IMG SRC="images/new.jpg" BORDER="0" ALT="neu">';
					echo '</a>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<a href="javascript:popup(0);">';
					echo '<B>neue TuS Mannschaft anlegen</B>';
					echo '</a>';
				echo '</TD>';
			echo '</TR>';
		echo '</TABLE></CENTER><BR>';
	}

	echo '<CENTER><TABLE WIDTH="70%" BORDER="0">';
	        echo '<TR>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>ID</B>';
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>Name</B>';
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>Spiel-<br>stätte</B>';
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>Menu</B>';
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>IDX</B>';
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>Foto</B>';
	                echo '</TD>';
	                if ($_SESSION["groupid"] > 0)
					{
						echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>Aktion</B>';
	                	echo '</TD>';
					}
	        echo '</TR>';

			$sql = 'select * from tt_tus_mannschaft order by name';
			$result = getResult($db, $sql);
			foreach ($result as $team)
			{
		        echo '<TR>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        echo $team["id"];
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        echo $team["name"];
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        echo $team["spielstaette_id"];
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        if ($team["show_menu"]==1)
	                        	echo "Ja";
	                        else
	                        	echo "Nein";
	                         
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        echo $team["reihenfolge"];
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        if ($team["bild"] != null)
	                        	echo '<A HREF="showimage.php?id='.$team["bild"].'" TARGET="blank"><IMG SRC="images/view.jpg" HEIGHT="16" WIDTH="16" BORDER="0" ALT="Bild ansehen"></A>';
	                        else
	                        	echo 'keins';
	                echo '</TD>';

					if ($_SESSION["groupid"] > 0)
					{
						echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
							echo '<a href="javascript:popup('.$team["id"].');">';
								echo '<IMG SRC="images/edit.jpg" BORDER="0" ALT="Bearbeiten">';
							echo '</a>';
						echo '</TD>';
					}
		        echo '</TR>';
			}
	echo '</TABLE></CENTER>';
}
else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
?>