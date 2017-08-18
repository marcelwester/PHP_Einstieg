<?php

////
//// fbv_team_popup.php
////
//// letzte Änderung : Volker, 27.02.2004 
//// was : Kategorie als Popup
////
//// letzte Änderung : Daniel, 17.02.2004 17:12
//// was : Mannschaften ändern und anlegen(Datei angelegt)

include "inc.php";
sitename("fbv_team_popup.php",$_SESSION["groupid"]);
focus();
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e - Mannschaftsverwaltung</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php
if (priv("fb_team"))
{
switch ($_REQUEST["action"])
{
	case 'add':
	 echo '<BR><FORM METHOD="POST" ACTION="fbv_team_popup.php?action=save&PHPSESSID='.session_id().'">';
		echo '<TABLE WIDTH="100%" BORDER="0">';
			echo '<TR>';
				echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Neue Mannschaft anlegen</B>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Kategorie<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
				        $sqlstr = "select * from fb_mannschaft_kat order by name";
				        $result = GetResult($db,$sqlstr);
				        build_select($result,name,kategorie,kat,"",1,$_REQUEST["kat"]);
				      	unset($result);
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
					echo '<B>Vereinsnummer<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="20" NAME="vereinsnummer" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Internet<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="internet" VALUE="" />';
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
		$sql = 'select * from fb_mannschaft where id = '.$_REQUEST["teamid"];
		$result = getResult($db, $sql);
		$team = $result[0];

		echo '<BR><FORM METHOD="POST" ACTION="fbv_team_popup.php?action=save&PHPSESSID='.session_id().'">';

		echo '<INPUT TYPE="HIDDEN" NAME="id" VALUE="'.$_REQUEST["teamid"].'" />';

		echo '<TABLE WIDTH="100%" BORDER="0">';
			echo '<TR>';
				echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Mannschaft bearbeiten</B>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Kategorie<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="10" NAME="kat" VALUE="'.$team["kat"].'" READONLY />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Name<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="name" VALUE="'.$team["name"].'" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Vereinsnummer<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="20" NAME="vereinsnummer" VALUE="'.$team["vereinsnummer"].'" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Internet<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="internet" VALUE="'.$team["internet"].'" />';
				echo '</TD>';
			echo '</TR>';			echo '<TR>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" COLSPAN="2">';
					echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
					echo '&nbsp;&nbsp;<INPUT TYPE="submit" VALUE="Ändern">';
				echo '</TD>';
			echo '</TR>';
		echo '</TABLE>';
		echo '</FORM>';
		break;
	case 'save':
		if (isset($_POST["id"]))
			$id = $_POST["id"];

		if (strlen($_POST["name"]) != 0)
		{
			$name = $_POST["name"];

			if (strlen($_POST["vereinsnummer"]) != 0)
				$vereinsnummer = $_POST["vereinsnummer"];
			else
				$vereinsnummer = 'NULL';

			if (strlen($_POST["internet"]) != 0)
				$internet = $_POST["internet"];
			else
				$internet = 'NULL';
                         
			$kat = $_POST["kat"];                         

			if (!isset($id))	// neu anlegen
			{
				$sql = 'insert into fb_mannschaft (name, vereinsnummer, internet,kat) ';
				$sql .= 'values ("'.$name.'", "'.$vereinsnummer.'", "'.$internet.'", "'.$kat.'")';
				$sql = ereg_replace('"NULL"', 'NULL', $sql);
				$result = doSQL($db,$sql);
			}
			else			// alten updaten
			{
				$sql = 'update fb_mannschaft set ';
				$sql .= 'name = "'.$name.'", ';
				$sql .= 'vereinsnummer = "'.$vereinsnummer.'", ';
				$sql .= 'internet = "'.$internet.'" ';
				$sql .= 'where id = '.$id;

				$sql = ereg_replace('"NULL"', 'NULL', $sql);
				$result = doSQL($db,$sql);
			}

			if ($result["code"] == 0)
			{
				mlog("Fussballverwaltung: Es wurde eine Mannschaft gespeichert: ".$id);
				echo 'Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript: window.close();">Hier klicken, um das Fenster zu schließen</A>';
           			echo '<SCRIPT TYPE="text/javascript">';
                			echo 'opener.location.reload();';
                			echo 'setTimeout("window.close()",1000);';
           			echo '</SCRIPT>';
			}
			else
			{
				echo '<CENTER><BR><BR><BR>Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A></CENTER>';
			}
		}
		break;
}
}
else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
?>
</BODY>
</HTML>