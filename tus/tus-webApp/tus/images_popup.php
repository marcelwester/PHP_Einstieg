<?php
//// images_popup.php
////
//// letzte Änderung : Volker, 01.05.2004
//// was : Erstellung

include "inc.php";
sitename("images_popup.php",$_SESSION["groupid"]);
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php
$imageid=$_REQUEST["imageid"];
$result=GetResult($db,"select userid from sys_images where image_id=$imageid");

//if (priv("images") || ($_SESSION["userid"]==$result["0"]["userid"] && priv("image_del"))) 
if (priv("images") || $_SESSION["userid"]==$result["0"]["userid"] || priv("image_edit")) 
{


switch ($_REQUEST["action"])
{
 case 'edit':
     	$sqlstr="select descr,kategorie,linked,idx from sys_images where image_id=$imageid";
     	$result=GetResult($db,$sqlstr);
     	$result=$result["0"];
        echo '<BR><BR><FORM METHOD="POST" ACTION="images_popup.php?action=save&imageid='.$imageid.'&PHPSESSID='.session_id().'">';
        	echo '<TABLE WIDTH="100%" BORDER="0">';
                        echo '<TR>';
                                echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                        echo '<B>Bilddaten bearbeiten</B>';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Beschreibung<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="descr" VALUE="'.$result["descr"].'" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Kategorie<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        if ($result["linked"]==0 || ! isset($result["linked"]))
                                        {
                                              	$sqlstr="select id,name from sys_images_kat order by idx";
                                        	$result1=GetResult($db,$sqlstr);
                                        	build_select($result1,"name","id","kategorie","","1",$result["kategorie"]);
                                        }
                                        else
                                        {
                                              	$sqlstr="select id,name from sys_images_kat where id=".$result["kategorie"];
                                        	$result1=GetResult($db,$sqlstr);
                                              	echo $result1["0"]["name"];
                                        }
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Reihenfolge</B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="3" NAME="idx" VALUE="'.$result["idx"].'" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR><TD  ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                        	echo '<B>Foto</B>';
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                     		echo '<IMG SRC="showimage2.php?id='.$imageid.'" WIDTH="200">';
                        echo '</TD></TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" COLSPAN="2">';
                                        //$_SESSION["edit_kader"] = 0;
                                        echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
                                        echo '&nbsp;&nbsp;<INPUT TYPE="submit" VALUE="Speichern">';
                                echo '</TD>';
                        echo '</TR>';

               echo '</TABLE>';
         echo '</FORM>';
           
 break;


 case 'save':
      $imageid=$_REQUEST["imageid"];
      $kategorie=$_POST["kategorie"];
      $descr=$_POST["descr"];
      $idx=trim($_POST["idx"]);
     
      // Check Number input
      if (preg_match("/\D/", $idx))
      {
      	echo "<br>Falsche Eingabe bei Reihenfolge: Nur Ganzzahlig Werte sind erlaubt !";
      	echo '<br>Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:window.history.back();">Hier klicken, um zurückzukehren</A>';
        closeConnect($db);
        exit;
      }
     
      if (isset($kategorie)) 
      {  
      	$sqlstr="update sys_images set descr='$descr',kategorie=$kategorie,idx=$idx where image_id=$imageid";
      } 
      else
      {
      	$sqlstr="update sys_images set descr='$descr',idx=$idx where image_id=$imageid";
      }
      $result=doSQL($db,$sqlstr);      
      
      if ($result["code"] == 0)
      {
           mlog("Bilderverwaltung: Es wurde die Beschreibung eines Bildes gespeichert: ".$imageid);
           echo 'Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A>';
           echo '<SCRIPT TYPE="text/javascript">';
                echo 'opener.location.reload();';
                echo 'setTimeout("window.close()",1000);';
           echo '</SCRIPT>';
      }

      else
           echo 'Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:window.history.back();">Hier klicken, um zurückzukehren</A>';
       break;
}
closeConnect($db);
}
else
{
	   echo 'Ihnen fehlt die Berechtigung diese Seite anzuzeigen.<br>';
           echo '<br>Sie können nur Bilder bearbeiten, die Sie selbst hochgeladen haben !';
           echo '<br><br><A HREF="javascript: window.close();">Hier klicken, um das Fenster zu schließen</A>';
           echo '<SCRIPT TYPE="text/javascript">';
                echo 'opener.location.reload();';
                #echo 'setTimeout("window.close()",3000);';
           echo '</SCRIPT>';
}
?>
</BODY>
</HTML>