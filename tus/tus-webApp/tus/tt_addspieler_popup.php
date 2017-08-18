<?php

////
//// tt_addspieler_popup.php
////
//// Änderung: Volker 06.10.2004
//// Erstellung


include "inc.php";
sitename("tt_addspieler_popup.php.php",$_SESSION["groupid"]);


if (priv("spiele_edit"))
{
 
?>

<HTML>
<HEAD>
<?php
  echo '<TITLE>w w w . t u s - o c h o l t . d e -  zusätzlicher Spieler</TITLE>';
?>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">


<?php
focus();   
switch ($_REQUEST["action"])
{
        case 'start':
                $sqlstr = 'select name,vorname,id from tt_person order by name';
                $result = getResult($db, $sqlstr);
                array(person);
                $i=0;
                foreach ($result as $row) {
			$person[$i]["id"]=$row[id];
			$person[$i]["name"]=$row["name"].', '.$row["vorname"];
			$i++;
		}
		unset($result);                	
                
                
                echo '<FORM METHOD="POST" ACTION="tt_addspieler_popup.php?action=save&PHPSESSID='.session_id().'">';

                echo '<INPUT TYPE="HIDDEN" NAME="spielid" VALUE="'.$_REQUEST["spielid"].'" />';
		echo '<INPUT TYPE="HIDDEN" NAME="teamid" VALUE="'.$_REQUEST["teamid"].'" />';

                echo '<TABLE WIDTH="100%" BORDER="0">';
                        echo '<TR>';
                                echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                        echo '<B>Person hinzufügen</B>';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
				echo '<td align="center">';
					build_select($person,"name","id","spielerid","",6,"");
				echo '</td>';
                        echo '</TR>';                     
 
                        echo '<TR>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" COLSPAN="2">';
					
                                        echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
                                        echo '&nbsp;&nbsp;<INPUT TYPE="submit" VALUE="Hinzufügen">';
                                echo '</TD>';
                        echo '</TR>';
                echo '</TABLE>';
                echo '</FORM>';
                break;
        case 'save':
		$sqlstr = "insert into  tt_zw_tt_person_tt_spiele (spiel_id,spieler_id,team_id) values (";
		$sqlstr.= $_POST["spielid"].",";
		$sqlstr.= $_POST["spielerid"].",";
		$sqlstr.= $_POST["teamid"].")";
		$result=doSQL($db,$sqlstr);                        
                echo $sqlstr;
                if ($result["code"] == 0) 
                {        
                        echo '<CENTER><BR><BR><BR>Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A></CENTER>';
                        echo '<SCRIPT TYPE="text/javascript">';
                       	echo 'opener.location.reload();';
                              	echo 'setTimeout("window.close()",500);';
                        echo '</SCRIPT>';
                 }
                 else
                 {
                        echo '<CENTER><BR><BR><BR>Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A></CENTER>';
                 }
	}
}
else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
closeConnect($db);
?>
</BODY>
</HTML>