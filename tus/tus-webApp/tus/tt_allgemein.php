<?php
sitename("tt_allgemein.php",$_SESSION["groupid"]);
switch($_REQUEST["action"]) {
	
	case 'start':
		if (priv("tt_allgemein"))
		{
                echo '<CENTER><TABLE WIDTH="40%" BORDER="0">';
                        echo '<TR>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<a href="index.php?site=tt_allgemein&action=edit&PHPSESSID='.session_id().'">';
                                        echo '<IMG SRC="images/edit.jpg" BORDER="0" ALT="neu">';
                                        echo '</a>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<a href="index.php?site=tt_allgemein&action=edit&PHPSESSID='.session_id().'">';
                                        echo '<B>Seite editieren</B>';
                                        echo '</a>';
                                echo '</TD>';
                        echo '</TR>';
                echo '</TABLE></CENTER><BR>';
		} 
		
		$sqlstr = "select descr from text_container where name='tt_allgemein'";
		$result=GetResult($db,$sqlstr);

	   if (isset($result)) {
			echo $result["0"]["descr"];
		}
	
	break;

	case 'edit':
		if (priv("tt_allgemein"))
		{
			$sql = "select descr from text_container where name='tt_allgemein'";
			$result= GetResult($db,$sql);
			echo '<FORM NAME="tt_allgemein" METHOD="POST" ACTION="index.php?site=tt_allgemein&ttmenu=yes&action=save&PHPSESSID='.session_id().'">';
			echo '<TABLE WIDTH="90%" BORDER="0">';
				echo '<TR BGCOLOR="#DDDDDD">';
					echo '<TD COLSPAN="2" ALIGN="LEFT" BGCOLOR="#DDDDDD">';
						echo '<B>Seite editiern</B>';
					echo '</TD>';
				echo '</TR>';

				echo '<TR>';
				echo '<TD ALIGN="CENTER" CLASS="none">';
						echo '<TEXTAREA ROWS="20" COLS="100" NAME="descr">';
						echo $result[0]["descr"];
						echo '</TEXTAREA>';
					echo '</TD>';
				echo '</TR>';
				echo '<TR>';
					echo '<TD ALIGN="CENTER" COLSPAN="2" BGCOLOR="#DDDDDD">';
						echo '<INPUT TYPE="SUBMIT" VALUE="Speichern" />';
					echo '</TD>';
				echo '</TR>';
			echo '</TABLE>';
			echo '</FORM>';
		}
		else
			echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
	break;

	case 'save':
		if (priv("tt_allgemein"))
		{
		   //$state = addcslashes($_POST["descr"],'",\'');
			$state = $_POST["descr"];
			$sql = "update text_container set descr ='".$state."' where name='tt_allgemein'";
			$result = doSQL($db, $sql);
		
			if ($result["code"] == 0)
			{
				echo '<A HREF="index.php?site=tt_allgemein&ttmenu=yes&action=start&PHPSESSID='.session_id().'">Seite erfolgreich geändert!</A>';
				echo '<SCRIPT TYPE="text/javascript">';
					echo 'setTimeout("window.location=\'index.php?site=tt_allgemein&ttmenu=yes&action=start&PHPSESSID'.session_id().'\'",1000);';
				echo '</SCRIPT>';
			}
			else
			{
				echo 'Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:window.history.back();">Hier klicken, um zurückzukehren</A>';
			}
		}
		else
			echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
	break;
}

?>