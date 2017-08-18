<?php

////
//// group_popup.php
////
//// Änderung : Daniel, 12.03.2004 17:27
//// was : Datei erstellt

include "inc.php";
sitename("group_popup.php",$_SESSION["groupid"]);
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e - Gruppenverwaltung</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php
if (priv("group"))
{
switch ($_REQUEST["action"])
{
	case 'edit':
		$sql = 'select * from sys_groups where groupid = '.$_REQUEST["grpid"];
		$result = getResult($db,$sql);
		$group = $result[0];
		echo '<BR><FORM METHOD="POST" ACTION="group_popup.php?action=save&PHPSESSID='.session_id().'">';
		echo '<TABLE WIDTH="100%" BORDER="0">';
			echo '<TR>';
				echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Gruppe bearbeiten</B>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Nummer<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" CLASS="none">';
					echo '<INPUT TYPE="HIDDEN" NAME="oldgroupid" VALUE="'.$group["groupid"].'" />';
					echo '<INPUT TYPE="TEXT" SIZE="10" NAME="grpid" VALUE="'.$group["groupid"].'" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Bezeichnung<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" CLASS="none">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="name" VALUE="'.$group["name"].'" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Beschreibung<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" CLASS="none">';
					echo '<TEXTAREA ROWS="10" COLS="37" NAME="descr">';
					echo $group["descr"];
					echo '</TEXTAREA>';
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
	case 'new':
		$sql = 'select groupid from sys_groups';
		$result = getResult($db,$sql);

		echo '<BR><FORM METHOD="POST" ACTION="group_popup.php?action=save&PHPSESSID='.session_id().'">';
		echo '<TABLE WIDTH="100%" BORDER="0">';
			echo '<TR>';
				echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>neue Gruppe anlegen</B>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Nummer<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" CLASS="none">';
					echo '<INPUT TYPE="HIDDEN" NAME="oldgroupid" VALUE="0" />';
					echo '<INPUT TYPE="TEXT" SIZE="10" NAME="grpid" VALUE="" />';
					echo ' (bereits vergeben :';
					foreach ($result as $group)
						echo ' '.$group["groupid"];
					echo ')';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Bezeichnung<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" CLASS="none">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="name" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Beschreibung<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" CLASS="none">';
					echo '<TEXTAREA ROWS="10" COLS="37" NAME="descr">';
					echo '</TEXTAREA>';
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
		$grpid = $_POST["grpid"];
		$oldgroupid = $_POST["oldgroupid"];
		$name = $_POST["name"];
		$descr = $_POST["descr"];

		if (strlen($descr) != 0 && strlen($name) != 0 && strlen($grpid) != 0)
		{
			if ($oldgroupid == 0)	// neu anlegen
			{
				$sql = 'insert into sys_groups (groupid, name, descr) ';
				$sql .= 'values ('.$grpid.', "'.$name.'", "'.$descr.'")';
				$result = doSQL($db,$sql);
			}
			else		// alten updaten
			{
				$sql = 'update sys_groups set ';
				$sql .= 'groupid = '.$grpid.', ';
				$sql .= 'name = "'.$name.'", ';
				$sql .= 'descr = "'.$descr.'" ';
				$sql .= 'where groupid = '.$oldgroupid;

				$result = doSQL($db,$sql);
			}
			if ($result["code"] == 0) {
				mlog("Gruppenverwaltung: Speichern eines Eintrages");
				echo '<CENTER><BR><BR><BR>Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A></CENTER>';
			}
			else
			{
				echo '<CENTER><BR><BR><BR>Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:window.close();">Hier klicken, um das Fenster zu schließen</A></CENTER>';
				echo '<br>'.$sql.'<br>';
				echo '<pre>';
				print_r($result);
				echo '</pre>';
			}
		} 
		else
			echo '<CENTER><BR><BR><BR>Bitte füllen Sie das Formular komplett aus<br><A HREF="javascript:window.history.back();">Hier klicken, um zurück zu gehen</A></CENTER>';
		break;
}
}
else
	echo $no_rights;
?>
</BODY>
</HTML>