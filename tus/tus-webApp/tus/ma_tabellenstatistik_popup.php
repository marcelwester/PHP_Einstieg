<?php
include "inc.php";
focus();
sitename("ma_tabellenstatistik_popup.php",$_SESSION["groupid"]);
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">
<?php

$saisonid=$_GET["saisonid"];
$teamid=$_GET["teamid"];
$sqlstr = "select liga,spielzeit from fb_saison where id=".$saisonid;
$result=getResult($db,$sqlstr);


echo '<table width="100%"';
   echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                            echo '<B>Spieltagstatistik</B>';
                            echo '<br>'.$result["0"]["liga"]." ".$result["0"]["spielzeit"];
   echo '</TD></TR>';
                 
   echo '<tr><td ALIGN="CENTER">';

    echo '<IMG SRC="ma_tabellenstatistik.php?saisonid='.$saisonid.'&teamid='.$teamid.'" BORDER="0" ALT="Spieltagstatistik">';
   echo '</td></tr>';

   echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD" >';
	echo '<INPUT TYPE="button" VALUE="Schliessen" onClick="window.close();">';
   echo '</TD></TR>';
   
echo '</table>';
closeConnect($db);
?>