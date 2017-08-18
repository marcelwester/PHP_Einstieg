<?php

////
//// forumedit_popup.php
////
//// letzte Änderung : Volker, 22.02.2007 
//// was : Erstellung

include "inc.php";
sitename("forumedit_popup.php",$_SESSION["groupid"]);
focus();
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e - Forumverwaltung</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php
if (priv("forumedit"))
{
switch ($_REQUEST["action"])
{
	case 'add':
	 echo '<BR><FORM METHOD="POST" ACTION="forumedit_popup.php?action=save&PHPSESSID='.session_id().'">';
		echo '<TABLE WIDTH="100%" BORDER="0">';
			echo '<TR>';
				echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Neue Forum-Kategorie anlegen</B>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Name<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="name" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>IDX (Platz in der Auflistung)<br>Zahlenwert<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="4" NAME="idx" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Sichtbar<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<input type="checkbox" name="visible" value="1" checked>';  
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
   		echo "<br><b>Hinweis:</b> Eine neue Forum-Kategorie wird erst in der rechten Forum-Übericht sichtbar, wenn mindestens ein ungelöschter Eintrag vorliegt!";

		break;
	case 'edit':
		$sql = 'select name,idx,show_menu from sys_forum where id = '.$_REQUEST["forumid"];
		$result = getResult($db, $sql);
		if (isset($result)) {
			$row = $result[0];
	 	    echo '<BR><FORM METHOD="POST" ACTION="forumedit_popup.php?action=save&PHPSESSID='.session_id().'">';
			echo '<INPUT TYPE="HIDDEN" NAME="id" VALUE="'.$_REQUEST["forumid"].'" />';
	
			echo '<TABLE WIDTH="100%" BORDER="0">';
				echo '<TR>';
					echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
						echo '<B>Forum-Kategorie editieren</B>';
					echo '</TD>';
				echo '</TR>';
				echo '<TR>';
					echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
						echo '<B>Name<B>';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo '<INPUT TYPE="TEXT" SIZE="50" NAME="name" VALUE="'.$row["name"].'" />';
					echo '</TD>';
				echo '</TR>';
				echo '<TR>';
					echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
						echo '<B>IDX (Platz in der Auflistung, <br>Zahlenwert)<B>';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo '<INPUT TYPE="TEXT" SIZE="4" NAME="idx" VALUE="'.$row["idx"].'" />';
					echo '</TD>';
				echo '</TR>';
				echo '<TR>';
					echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
						echo '<B>Sichtbar<B>';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						if ($row["show_menu"]=="1") {
							echo '<input type="checkbox" name="visible" value="1" checked>';  
						} else {
							echo '<input type="checkbox" name="visible" value="0">';  
						}
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
		} else {
			echo "Kein Eintrag gefunden";
			echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
		}
	break;
	case 'save':
		$error=0;
		if (!isset($_POST["name"])) $error=1;
		if (!isset($_POST["idx"])) $error=1;
		
		if ($error==0) {
			$visible="0";
			if (isset($_POST["visible"])) $visible="1";

			if (isset($_POST["id"])) {
				$sqlstr  = "update sys_forum set ";
				$sqlstr .= "name='".$_POST["name"]."',";
				$sqlstr .= "idx=".$_POST["idx"].",";
				$sqlstr .= "show_menu=".$visible." ";
				$sqlstr .= "where id = ".$_POST["id"];
				$log="Forumverwaltung, ein Eintrag wurde gespeichert: ".$id;
			} else {
				$sqlstr  = "insert into sys_forum (name,idx,show_menu) values (";
				$sqlstr .= "'".$_POST["name"]."',";
				$sqlstr .= $_POST["idx"].",";
				$sqlstr .= $visible.")";
				$log="Neue Forum-Kategorie angelegt: ".$_POST["name"];
			}
			$result=doSQL($db,$sqlstr);
		}
		if ($result["code"] == 0 && $error=="0")
		{
			mlog($log);
			echo 'Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript: window.close();">Hier klicken, um das Fenster zu schließen</A>';
       			echo '<SCRIPT TYPE="text/javascript">';
            			echo 'opener.location.reload();';
            			echo 'setTimeout("window.close()",1000);';
       			echo '</SCRIPT>';
		}
		else
		{
			echo '<CENTER><BR><BR><BR>Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:opener.location.reload(); window.history.back();">Hier klicken, um zurückzukehren</A></CENTER>';
		}
		break;
}
}
else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
?>
</BODY>
</HTML>