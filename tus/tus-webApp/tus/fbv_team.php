<?php

//// fbv_team.php
////
//// letzte Änderung : Volker, 22.01.2006 
//// was : Kategorie Auswahl als Text aus Tabelle fb_mannschaft_kat 
////
//// letzte Änderung : Volker, 27.02.2004 
//// was : Kategorie als Popup
////
//// letzte Änderung : Daniel, 14.02.2004 17:20
//// was : Datei erstellt

if (priv("fb_team"))
{
sitename("fbv_team.php",$_SESSION["groupid"]);
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
	function popup(teamid,kat)
	{
		var url;
		if (teamid == 0)
<?php
			echo 'url = "fbv_team_popup.php?action=add&kat="+kat+"&PHPSESSID='.session_id().'";';
		echo ' else ';
			echo 'url = "fbv_team_popup.php?action=edit&teamid="+teamid+"&PHPSESSID='.session_id().'";';
?>
		window.open(url,"saison","width=650, height=300, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
	}
-->
</SCRIPT>

<?php
	$colspan_head = 2;

	if (isset($_REQUEST["kat"]))
		$kat = $_REQUEST["kat"];
	else
		$kat = 'H';

	$sql = 'select kat,fb_mannschaft_kat.name from fb_mannschaft,fb_mannschaft_kat where kat=kategorie group by kat';
	$result_kat = getResult($db, $sql);

	$katSelect = '<SELECT NAME="KAT" ID="KAT" onChange="window.location.href=\'index.php?site=fbverw&action=team&PHPSESSID='.session_id().'&kat=\'+this.value;">';
	foreach($result_kat as $katrow)
		if ($katrow["kat"] == $kat)
			$katSelect .= '<OPTION SELECTED VALUE="'.$katrow["kat"].'">'.$katrow["name"].'</OPTION>';
		else
			$katSelect .= '<OPTION VALUE="'.$katrow["kat"].'">'.$katrow["name"].'</OPTION>';
	$katSelect .= '</SELECT>';

	echo '<CENTER><B>Kategorie : </B>'.$katSelect.'<BR></CENTER>';

	if ($_SESSION["groupid"] > 0)
	{
		echo '<CENTER><TABLE WIDTH="40%" BORDER="0">';
			echo '<TR>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<a href="javascript:popup(0,'."'$kat'".');">';
					echo '<IMG SRC="images/new.jpg" BORDER="0" ALT="neu">';
					echo '</a>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<a href="javascript:popup(0,'."'$kat'".');">';
					echo '<B>neue Mannschaft anlegen</B>';
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
	                if ($_SESSION["groupid"] > 0)
					{
						echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>Aktion</B>';
	                	echo '</TD>';
					}
	        echo '</TR>';

			$sql = 'select * from fb_mannschaft where kat = "'.$kat.'" order by name';
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