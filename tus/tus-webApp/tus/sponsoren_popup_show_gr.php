<?php
//// sponsoren_popup.php
////
//// letzte Änderung: Volker 12.02.2004
//// was: Meldung wenn imageid=0
////
//// letzte Änderung: Volker 19.02.2004
//// was: Erstellung
//// Zu jedem kleinen Logo kann ein Grosses Logo gelinked werden. 
//// In der Tabelle fb_Sponsoren sind lediglich zusätzlich Informationen zur image_id
//// in der Tabelle sys_images abgelegt. Es besteht eine 1:1 Beziehung zwischen sys_images und fb_sponsoren

include "inc.php";
sitename("sponsoren_popup_show_gr.php",$_SESSION["groupid"]);
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e - Sponsoren Visitenkarte</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php

  $imageid = $_REQUEST["imageid"];

  if ($imageid == "0")
  {
  	echo '<TABLE WITH="100%" BORDER="1" ALIGN="CENTER">';
  	echo '<TR><TD><B> Leider kein Bild vorhanden </B></TD></TR>';
  	echo '<TR><TD ALIGN="CENTER" BGCOLOR="#FFFFFF" >';
  	echo '<INPUT TYPE="button" VALUE="Schliessen" onClick="window.close();">';
  	echo '</TD></TR>';
  	echo '</table>';
  }
  else
  { 
   	echo '<IMG SRC="showimage2.php?id='.$imageid.'" WIDTH=650 >';
  	echo '<br>';
 	echo '<TABLE WIDTH="100%" BORDER="0" ALIGN="CENTER">';
  	echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD" >';
  	echo '<INPUT TYPE="button" VALUE="Schliessen" onClick="window.close();">';
  	echo '</TD></TR>';
  	echo '</table>';
  }
closeConnect($db);
?>
</BODY>
</HTML>