<?php

////
//// ma_spielplan_spieltag_descr_popup.php
////
//// Änderung: Volker 23.01.2005
//// 


include "inc.php";
sitename("ma_spielplan_spieltag_descr_popup.php",$_SESSION["groupid"]);


if (priv("spiele_edit") && priv_team($_REQUEST["teamid"])) 
{
 
?>

<HTML>
<HEAD>
<?php
  echo '<TITLE>w w w . t u s - o c h o l t . d e -  Spieltag Kommentar</TITLE>';
?>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">


<?php
focus();   
$saisonid=$_REQUEST["saisonid"];
$spieltag=$_REQUEST["spieltag"];

switch ($_REQUEST["action"])
{
case 'start':


		$sqlstr  = "select descr from fb_spieltag_descr where ";
		$sqlstr .= "saison_id=".$saisonid." and ";
		$sqlstr .= "spieltag=".$spieltag;
		$result= getResult($db,$sqlstr);
		if (isset($result["0"]["descr"])) {
			$des = $result["0"]["descr"];
		} else {
			$des="";	
		}

                echo '<FORM METHOD="POST" ACTION="ma_spielplan_spieltag_descr_popup.php?action=save&saisonid='.$saisonid.'&teamid='.$_REQUEST["teamid"].'&spieltag='.$spieltag.'&PHPSESSID='.session_id().'">';
                echo '<TABLE WIDTH="100%" BORDER="0">';
                        echo '<TR>';
                                echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                        echo '<B>Spieltag Kommentar</B>';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
				echo '<td align="center">';
					echo '<input type="text" name="descr" size="50" value="'.$des.'"/>';
				echo '</td>';
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
		mlog("Fussball: Spieltagkommentar wurde eingetragen: ".$spieltag);
		$sqlstr  = "delete from fb_spieltag_descr where ";
		$sqlstr .= "saison_id=".$saisonid." and ";
		$sqlstr .= "spieltag=".$spieltag;
		$result=doSQL($db,$sqlstr);                        
		if ($_POST["descr"]!="") {
			$sqlstr  = "insert into  fb_spieltag_descr (saison_id,spieltag,descr) values (";
			$sqlstr .= $saisonid.",";
			$sqlstr .= $spieltag.",";
			$sqlstr .= "'".$_POST["descr"]."')";
			$result=doSQL($db,$sqlstr);                        
		}
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
                        echo '<CENTER><BR><BR><BR>Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="window.history.back();">Hier klicken, um das Fenster zu schließen</A></CENTER>';
                        echo '<br> Es sind keien Sodnerzeichen wie \' erlaubt';
                 }
	}
}
else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
closeConnect($db);
?>
</BODY>
</HTML>