<?php

////
//// tt_extspieler_popup.php
////
//// Änderung: Volker 06.10.2004
//// Erstellung


include "inc.php";
sitename("tt_extspieler_popup.php",$_SESSION["groupid"]);


if (priv("spiele_edit"))
{
 
?>

<HTML>
<HEAD>
<?php
  echo '<TITLE>w w w . t u s - o c h o l t . d e - externe Spieler</TITLE>';
?>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">


<?php
focus();   
switch ($_REQUEST["action"])
{
        case 'start':
                $sqlstr = 'select name,vorname from tt_ext_spieler where id = '.$_REQUEST["id"];
                $result = getResult($db, $sqlstr);
                $result = $result[0];
                echo '<BR><BR><FORM METHOD="POST" ACTION="tt_extspieler_popup.php?action=save&PHPSESSID='.session_id().'">';

                echo '<INPUT TYPE="HIDDEN" NAME="id" VALUE="'.$_REQUEST["id"].'" />';

                echo '<TABLE WIDTH="100%" BORDER="0">';
                        echo '<TR>';
                                echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                        echo '<B>Person bearbeiten</B>';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Vorname<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="40" NAME="vorname" VALUE="'.$result["vorname"].'" />';
                                echo '</TD>';
                        echo '</TR>';                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Name<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="40" NAME="name" VALUE="'.$result["name"].'" />';
                                echo '</TD>';
                        echo '</TR>';
 
                        echo '<TR>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" COLSPAN="2">';
					
                                        echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
                                        echo '&nbsp;&nbsp;<INPUT TYPE="submit" VALUE="Ändern">';
                                echo '</TD>';
                        echo '</TR>';
                echo '</TABLE>';
                echo '</FORM>';
                break;
        case 'save':
		
		$sqlstr = "update tt_ext_spieler set ";
		$sqlstr.= "name='".$_POST["name"]."',";
		$sqlstr.= "vorname='".$_POST["vorname"]."' ";
		$sqlstr.= "where id=".$_REQUEST["id"];
		$result=doSQL($db,$sqlstr);                        
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