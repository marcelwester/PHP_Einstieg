<?php
  // Index.php
	include "inc.php";
?>




</HEAD>

<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php

if (isset($_SESSION["repadmin"])) $db=oconnect();


//$action = $_REQUEST["action"];


//if (! isset($action))
//   $action = 0;


echo '<table WIDTH="100%" HEIGTH="100%" BORDER="0">';
        echo '<tr>';
        echo '<td WIDTH="20%" BGCOLOR='.$BGCOLOR1.' ALIGN="CENTER">';
                echo '<font size="+5"><i>K</i></font>';
        echo '</td>';
        echo '<td  BGCOLOR='.$BGCOLOR2.' ALIGN="CENTER">';
                echo '<FONT FACE="Arial" SIZE="6" COLOR='.$HEADCOLOR.'><i>KRepAdmin</i></FONT>';
        echo '</td>';
        echo '</tr>';

        // Anfang: Menü
        echo '<td VALIGN="TOP" WIDTH="20%">';
        echo '<table WIDTH="100%" ALIGN="CENTER" VALIGN="TOP" BORDER="0">';
                echo '<tr>';
                       echo '<td ALIGN="LEFT"></td>';
                echo '</tr>';

                echo '<tr></tr>';

					if (!isset($_SESSION["dbconnection"])) {
	                echo '<tr>';
	                        table_link_menu('<b>DB-Connection</b>','index.php?menu=db_connection&PHPSESSID='.session_id().'');
	                echo '</tr>';
					}

                echo '<tr>';
                        table_link_menu('<b>Neues Setup erstellen</b>','index.php?menu=newsetup&PHPSESSID='.session_id().'');
                echo '</tr>';

                echo '<tr>';
                        table_link_menu('<b>Setup aus Replikation generieren</b>','index.php?menu=gensetup&PHPSESSID='.session_id().'');
                echo '</tr>';

                echo '<tr>';
                        table_link_menu('<b>Monitoring</b>','index.php?menu=monitor&PHPSESSID='.session_id().'');
                echo '</tr>';

                echo '<tr>';
                        table_link_menu('<b>Administration</b>','index.php?menu=admin&PHPSESSID='.session_id().'');
                echo '</tr>';

        echo '</table>';
        echo '</td>';
        // Ende Menü

         // Anfang Seite
 	     include "content.php";

echo '</table>';

if (isset($db)) oclose($db);


exit;

?>


</BODY>
</HTML>