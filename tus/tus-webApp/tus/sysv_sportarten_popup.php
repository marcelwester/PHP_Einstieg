<?php

////
//// sysv_sportarten_popup.php
//// Sys Sportarten Verwaltung
//// Änderung : Daniel, 06.03.2005
//// was : Volker erstellt

include "inc.php";
sitename("sysv_sportarten_popup.php",$_SESSION["groupid"]);
focus();
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e - Saisonverwaltung</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">





<?php
if (priv("sysv_sportarten"))
{
switch ($_REQUEST["action"])
{
	case 'add':
		echo '<BR><FORM METHOD="POST" ACTION="sysv_sportarten_popup.php?action=save&PHPSESSID='.session_id().'">';
		echo '<TABLE WIDTH="100%" BORDER="0">';
			echo '<TR>';
				echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Neue Sportart anlegen</B>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Name der Sportart<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="name" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Im Menu anzeigen<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="CHECKBOX" NAME="show_menu" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Numerischer Wert für die Reihenfolge<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="12" NAME="reihenfolge" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" COLSPAN="2">';
					echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
					echo '&nbsp;&nbsp;<INPUT TYPE="submit" VALUE="Anlegen">';
				echo '</TD>';
			echo '</TR>';
		echo '</TABLE>';
		echo '</FORM>';
		break;
	case 'edit':

		$sql = 'select * from sys_sportart where id = '.$_REQUEST["sysid"];
		$result = getResult($db, $sql);
		$row = $result[0];

		echo '<BR><FORM METHOD="POST" ACTION="sysv_sportarten_popup.php?action=save&PHPSESSID='.session_id().'">';

		echo '<INPUT TYPE="HIDDEN" NAME="id" VALUE="'.$_REQUEST["sysid"].'" />';

		echo '<TABLE WIDTH="100%" BORDER="0">';
			echo '<TR>';
				echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Sportart bearbeiten</B>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Name der Sportart<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="name" VALUE="'.$row[name].'" />';
				echo '</TD>';
			echo '</TR>';
			
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Im Menu anzeigen<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					if ($row["show_menu"]==1) 
						echo '<INPUT TYPE="CHECKBOX" NAME="show_menu" checked />';
					else
						echo '<INPUT TYPE="CHECKBOX" NAME="show_menu" />';
				echo '</TD>';
			echo '</TR>';
			
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Numerischer Wert für die Reihenfolge<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="12" NAME="reihenfolge" VALUE="'.$row["reihenfolge"].'" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" COLSPAN="2">';
					echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
					echo '&nbsp;&nbsp;<INPUT TYPE="submit" VALUE="Speichern">';
				echo '</TD>';
			echo '</TR>';
		echo '</TABLE>';
		echo '</FORM>';
		break;
	case 'save':
		
			if (isset($_POST["id"]))
				$id = $_POST["id"];
			

			if ($_POST["name"]=="") 
				echo "<br> Sie müssen einen Namen für die Sportart vergeben!";
			
			if ($_POST["reihenfolge"]=="") {
				$reihenfolge="0";
			} else {
			   $reihenfolge=$_POST["reihenfolge"];
			}
				
			
			if (isset($_POST["show_menu"]))
				$show_menu="1";
			else
				$show_menu="0";

			if (!isset($id))	// neu anlegen
			{
				$sql = "insert into sys_sportart (name,show_menu,reihenfolge)";
				$sql .= "values ('".$_POST["name"]."', ".$show_menu.", ".$reihenfolge.")";
			}
			else			// alten updaten
			{
				$sql  = "update sys_sportart set ";
				$sql .= "name='".$_POST["name"]."',";
				$sql .= "show_menu=".$show_menu.",";
				$sql .= "reihenfolge=".$reihenfolge." ";
				$sql .= "where id=".$id;
			}

			$result = doSQL($db,$sql);
			if ($result["code"] == "0")
			{
				mlog("System-Verwaltung: Speichern einer Sportart: ".$id);
				echo 'Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript: window.close();">Hier klicken, um das Fenster zu schließen</A>';
           			echo '<SCRIPT TYPE="text/javascript">';
                			echo 'opener.location.reload();';
                			echo 'setTimeout("window.close()",1000);';
           			echo '</SCRIPT>';
			}
			else
			{
				echo '<br><center>Der Wert für die Reihenfolge muss numerisch und ganzzahlig sein !</center>';
				echo '<br><center>Es dürfen keine Sportartnamen doppelt vergeben sein !</center>';
				echo '<CENTER><BR><BR><BR>Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:opener.location.reload(); window.history.back();">Hier klicken, um zurückzukehren</A></CENTER>';
			}
		break;
	}
}
else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
closeConnect($db);
?>
</BODY>
</HTML>