<?php

////
//// rights_popup.php
////
//// Änderung : Daniel, 16.03.2004 20:47
//// was : Datei erstellt

include "inc.php";
sitename("rights_popup.php",$_SESSION["groupid"]);
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e - Rechteverwaltung</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php
if (priv("rights"))
{
switch ($_REQUEST["action"])
{
	case 'edit':
		$sql = 'select * from sys_rights where id = '.$_REQUEST["rightid"];
		$result = getResult($db,$sql);
		$right = $result[0];
		echo '<BR><FORM METHOD="POST" ACTION="rights_popup.php?action=save&PHPSESSID='.session_id().'">';
		echo '<TABLE WIDTH="100%" BORDER="0">';
			echo '<TR>';
				echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Berechtigung bearbeiten</B>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Name</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" CLASS="none">';
					echo '<INPUT TYPE="HIDDEN" NAME="rightid" VALUE="'.$right["id"].'" />';
					echo '<INPUT TYPE="TEXT" SIZE="18" NAME="name" VALUE="'.$right["name"].'" />';
				echo '</TD>';
			echo '</TR>';

			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Kategorie</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" CLASS="none">';
					$sql = 'select distinct(kategorie) from sys_rights order by kategorie';
					$result = getResult($db, $sql);
					build_select($result, 'kategorie', 'kategorie', 'kategorie', '', 1,$right["kategorie"]);
				echo '</TD>';
			echo '</TR>';

			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Beschreibung</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" CLASS="none">';
					echo '<TEXTAREA ROWS="5" COLS="37" NAME="descr">';
					echo $right["descr"];
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
		echo '<BR><FORM METHOD="POST" ACTION="rights_popup.php?action=save&PHPSESSID='.session_id().'">';
		echo '<TABLE WIDTH="100%" BORDER="0">';
			echo '<TR>';
				echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>neue Berechtigung anlegen</B>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Name</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" CLASS="none">';
					echo '<INPUT TYPE="HIDDEN" NAME="rightid" VALUE="0" />';
					echo '<INPUT TYPE="TEXT" SIZE="18" NAME="name" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
			
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Kategorie</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" CLASS="none">';
					$sql = 'select distinct(kategorie) from sys_rights order by kategorie';
					$result = getResult($db, $sql);
					build_select($result, 'kategorie', 'kategorie', 'kategorie', '', 1);
				echo '</TD>';
			echo '</TR>';
						echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Beschreibung</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" CLASS="none">';
					echo '<TEXTAREA ROWS="5" COLS="37" NAME="descr">';
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
		$rightid = $_POST["rightid"];
		$name = $_POST["name"];
		$descr = $_POST["descr"];
		$kategorie = $_POST["kategorie"];
		//$group = $_POST["group"];
		$group = 10;   // Obsolet
		
		if (strlen($descr) != 0 && strlen($name) != 0 && strlen($group) != 0)
		{
			if ($rightid == 0)	// neu anlegen
			{
				$sql = 'insert into sys_rights (name, descr, grp,kategorie) ';
				$sql .= 'values ("'.$name.'", "'.$descr.'", '.$group.',"'.$kategorie.'")';
				$result = doSQL($db,$sql);
				if ($result["code"] == 0)  mlog("Benutzerrechteverwaltung: Insert eines Eintrages.");
			}
			else		// alten updaten
			{
				$sql = 'update sys_rights set ';
				$sql .= 'grp = '.$group.', ';
				$sql .= 'name = "'.$name.'", ';
				$sql .= 'kategorie = "'.$kategorie.'", ';
				$sql .= 'descr = "'.$descr.'" ';
				$sql .= 'where id = '.$rightid;
				$result = doSQL($db,$sql);
				if ($result["code"] == 0)  mlog("Benutzerrechteverwaltung: Update eines Eintrages: ".$rightid);
			}
			if ($result["code"] == 0) 
			{
				
				echo '<CENTER><BR><BR><BR>Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A></CENTER>';
		           	echo '<SCRIPT TYPE="text/javascript">';
                			echo 'opener.location.reload();';
                			echo 'setTimeout("window.close()",1000);';
           			echo '</SCRIPT>';
			}
			else
			{
				echo '<CENTER><BR><BR><BR>Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:window.close();">Hier klicken, um das Fenster zu schließen</A></CENTER>';
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