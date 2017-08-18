<?php
session_start();

//$user = "system";
//$pass = "manager";
//$tns = "brb1.world";

// Mauszeiger Firefox/ Mozilla und IE
if (stristr($_SERVER["HTTP_USER_AGENT"],"GECKO") || stristr($_SERVER["HTTP_USER_AGENT"],"FIREFOX")) {
	$hand="'pointer'";
} else {
	$hand="'hand'";
}



if (isset($_SESSION["repadmin"])) {
	$tns=$_SESSION["tns"];
	$user=$_SESSION["repadmin"];
   $pass=$_SESSION["repadminpwd"];
	include "db_ora.php";
   include "ora_krep_inc.php";
}



$DEBUG=0;

// Fehlerausgabe unterdrücken
if ($DEBUG==1) {
	ini_set('display_errors','On');
} else {
	ini_set('display_errors','Off');
}

$DATEFORMAT='dd.MM.YY - HH24:Mi:SS';

function decho ($text) {
        global $DEBUG;
        if ($DEBUG==1) echo $text;
}



// Layout
//

        //$HEADCOLOR='#FFFF00';            // gelb
        //$HEADCOLOR='#000000';                // schwarz
        $link = '#DDDDDD';                        // grau
        $link1 = '#FFFFFF';                        // grau
        $link_over = '#AAAAAA';                // dunkelgrau
        //$link = '#FFFF00';                        // gelb
        //$link_over = '#0000FF';   // blau
        $BGCOLOR1 = '#AAAAAA';
        $BGCOLOR2 = '#FFFFFF';



function table_link_menu($text,$href,$align="CENTER") {
global $link,$link_over,$hand;
   echo '<TD ALIGN="'.$align.'" BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\''.$href.'\'">';
                       echo $text;
        echo '</TD>';
}

function table_link_data($text,$href,$align="CENTER") {
global $link1,$link_over,$hand;
   echo '<TD ALIGN="'.$align.'" BGCOLOR="'.$link1.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link1.'\';" onClick="location.href=\''.$href.'\'">';
                       echo $text;
        echo '</TD>';
}




function table_data($data,$align1="center",$align2="center"	) {
if (! isset($align2)) $align2=$align1;

    if ( (! isset($data)) || (trim($data)=="")) {
                  $data="-";
                  echo '<td ALIGN="'.$align2.'">'.$data.'</td>';
            }
            else
                  echo '<td ALIGN="'.$align1.'">'.$data.'</td>';
}

function table_link_data_function($text,$href,$align="CENTER") {
global $link1,$link_over,$hand;

   echo '<TD ALIGN="'.$align.'" BGCOLOR="'.$link1.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link1.'\';" onClick='.$href.'>';
                        echo "$text";
        echo '</TD>';
}

function ja_nein ($href1,$href2) {
global $link,$link_over,$hand;
	echo '<table style="width:200px">';
		echo '<tr>';
		   echo '<TD width="50%" ALIGN="center" BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\''.$href1.'\'">';
         	echo "Ja";
         echo '</TD>';
		   echo '<TD width="50%" ALIGN="center" BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\''.$href2.'\'">';
	           echo "Nein";
	      echo '</TD>';
      echo '</tr>';
   echo '</table>';
}

function print_site() {
         echo '<input type="image" title="Drucken" src="./images/Printer.gif" onClick="print();">';
}

function build_select($result, $field_show,$field_val, $name, $multiple = "", $size = 1, $std = "") {
  echo "<select name=\"$name\" size=$size $multiple>";
  foreach ($result as $row)
  {
   if ($std == $row[$field_val]) {
     echo "<option selected value=\"" . $row[$field_val] . "\">";
   } else {
     echo "<option value=\"" . $row[$field_val] . "\">";
   }
   echo $row[$field_show];
   echo "</option>\n";
  }
  echo "</select>";
}



// Navigation
//
function wclose($action,$url="") {
switch ($action) {
case "reload_parent":
      echo '<CENTER><BR><BR><BR>Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A></CENTER>';
      echo '<SCRIPT TYPE="text/javascript">';
        echo 'opener.location.reload();';
        echo 'setTimeout("window.close()",1000);';
      echo '</SCRIPT>';
break;

case "auto_back_1":
      echo '<CENTER><BR><BR><BR>Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A></CENTER>';
      echo '<SCRIPT TYPE="text/javascript">';
        //echo 'opener.location.reload();';
        echo 'setTimeout("window.history.go(-1);",1000);';
      echo '</SCRIPT>';
break;

case "auto_load":
      echo '<CENTER><BR><BR><BR>Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript:window.open(\''.$url.'\',\'\');">Weiter</A></CENTER>';
      echo '<SCRIPT TYPE="text/javascript">';
      echo 'setTimeout("window.open(\''.$url.'\',\'\');",1000);';
      echo '</SCRIPT>';
break;



case "back":
   echo '<CENTER><BR><BR><BR>Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:window.history.back()">Hier klicken, zurückzukehren</A></CENTER>';
break;

default:
 echo '<SCRIPT TYPE="text/javascript">';
  echo 'setTimeout("window.history.back();",1000);';
 echo '</SCRIPT>';
break;
}
}
?>