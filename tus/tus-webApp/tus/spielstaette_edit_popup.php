<?php

////
//// spielstaette_edit_popup.php
////
//// letzte Änderung : Volker, 13.10.2007 
//// was : Erstellung

include "inc.php";
sitename("spielstaette_edit_popup.php",$_SESSION["groupid"]);
focus();
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e - Forumverwaltung</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php
if (priv("spielstaette"))
{
switch ($_REQUEST["action"])
{
	case 'add':
	 echo '<BR><FORM METHOD="POST" ACTION="spielstaette_edit_popup.php?action=save&PHPSESSID='.session_id().'">';
		echo '<TABLE WIDTH="100%" BORDER="0">';
			echo '<TR>';
				echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Neue Spielstätte anlegen</B>';
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
					echo '<B>Strasse<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="strasse" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Ort<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="ort" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Telefon<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="tel" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Aktiv<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<input type="checkbox" name="aktiv" value="1" checked>';  
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
		$sql = 'select id,name,strasse,ort,tel,aktiv from spielstaette where id = '.$_REQUEST["spielstaetteid"];
		$result = getResult($db, $sql);
		if (isset($result)) {
			$row = $result[0];
	 	    echo '<BR><FORM METHOD="POST" ACTION="spielstaette_edit_popup.php?action=save&PHPSESSID='.session_id().'">';
			echo '<INPUT TYPE="HIDDEN" NAME="id" VALUE="'.$_REQUEST["forumid"].'" />';
	
			echo '<TABLE WIDTH="100%" BORDER="0">';
				echo '<TR>';
					echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
						echo '<B>Spielstätte editieren</B>';
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
						echo '<B>Strasse<B>';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo '<INPUT TYPE="TEXT" SIZE="50" NAME="strasse" VALUE="'.$row["strasse"].'" />';
					echo '</TD>';
				echo '</TR>';
				echo '<TR>';
					echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
						echo '<B>Ort<B>';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo '<INPUT TYPE="TEXT" SIZE="50" NAME="ort" VALUE="'.$row["ort"].'" />';
					echo '</TD>';
				echo '</TR>';
				echo '<TR>';
					echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
						echo '<B>Telefon<B>';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo '<INPUT TYPE="TEXT" SIZE="50" NAME="tel" VALUE="'.$row["tel"].'" />';
					echo '</TD>';
				echo '</TR>';
				echo '<TR>';
					echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
						echo '<B>Aktiv<B>';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						if ($row["aktiv"]=="1") {
							echo '<input type="checkbox" name="aktiv" value="1" checked>';  
						} else {
							echo '<input type="checkbox" name="aktiv" value="0">';  
						}
					echo '</TD>';
				echo '</TR>';
				echo '<INPUT TYPE="hidden" NAME="id" VALUE="'.$row["id"].'" />';
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
		if (isset($_POST["aktiv"])) {
		    $aktiv=1;
		} else {
		    $aktiv=0;
	 	}
		  
		if ($error==0) {
			if (isset($_POST["id"])) {
				$sqlstr  = "update spielstaette set ";
				$sqlstr .= "name='".$_POST["name"]."',";
				$sqlstr .= "strasse='".$_POST["strasse"]."',";
				$sqlstr .= "ort='".$_POST["ort"]."',";
				$sqlstr .= "tel='".$_POST["tel"]."',";
				$sqlstr .= "aktiv=".$aktiv." ";
				$sqlstr .= "where id = ".$_POST["id"];
				$log="Spielstätte, ein Eintrag wurde gespeichert: ".$_POST["id"]." - ".$_POST["name"];
			} else {
				$sqlstr  = "insert into spielstaette (name,strasse,ort,tel,aktiv) values (";
				$sqlstr .= "'".$_POST["name"]."',";
				$sqlstr .= "'".$_POST["strasse"]."',";
				$sqlstr .= "'".$_POST["ort"]."',";
				$sqlstr .= "'".$_POST["tel"]."',";
				$sqlstr .= "".$aktiv.")";
				$log="Neue Spielstaette angelegt: ".$_POST["name"];
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