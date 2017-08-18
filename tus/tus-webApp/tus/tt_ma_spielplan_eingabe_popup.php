<?php
//// ma_spielplan_eingabe_popup.php
////
//// letzte Änderung : Volker, 03.07.2004
//// was : Erstellung
////
////

include "inc.php";
sitename("tt_ma_spielplan_eingabe_popup.php",$_SESSION["groupid"]);
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php
focus();
if (priv("spiele_add"))
{

$userid=$_SESSION["userid"];
$result=GetResult($db,"select name from sys_users where userid=$userid");
$username=$result["0"]["name"];


switch ($_REQUEST["action"])
{
 case 'add':
     $saisonid=$_REQUEST["saisonid"];

    
     // Spielzeit lesen
     $sqlstr ="select liga,spielzeit from tt_saison where id=$saisonid";
     $result = GetResult($db, $sqlstr);
     $liga=$result[0]["liga"];
     $spielzeit=$result[0]["spielzeit"];

     echo '<TABLE WIDTH="100%" BORDER="0" >';
     echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
     echo '<B>Neuen Spieltag eingeben</B><BR>'.$liga.' - '.$spielzeit.'<BR> - '.$username.' - </TD></TR>';
     echo '</TABLE>';

     
     echo '<FORM method="post" action="'.$PHP_SELF.'">';
    
       // Spieltag
		 $sqlstr="select distinct(grouped) from tt_spiele where saison_id=".$saisonid." order by grouped";
		 $result=GetResult($db,$sqlstr);
         $result["-1"]["grouped"]="- NEU -";
         echo "<B>Gruppierung: </B>";
         build_select($result,"grouped","grouped","grouped","",1,"- NEU -");
         echo '<INPUT TYPE="NUMBER" SIZE="20" NAME="newgrouped" VALUE="" />';
      

       // Mannschaften der Saison lesen
       $sql ="select id,name from tt_mannschaft, tt_zwtab_mannschaft_saison where
              saison_id=$saisonid and mannschaft_id=id order by name";
       $result=GetResult($db,$sql);

       echo '<table WIDTH="100%" ALIGN="CENTER">';
        echo '<tr>';
	echo '<td ALIGN="CENTER"><b>Datum</b></td>';
	echo '<td ALIGN="CENTER"><b>Uhrzeit</b></td>';
	echo '<td ALIGN="CENTER"><b>Heim</b></td>';
	echo '<td ALIGN="CENTER"><b>Gast</b></td>';
        echo '</tr>';
        $result["-1"]["name"]="kein";
	$result["-1"]["id"]="-1";
	     
       for ($i = 1; $i <= 10; $i++) {
       		echo '<tr>';
       		echo '<TD ALIGN="CENTER"><INPUT type=text name=datum'.$i.' size=14 value=""></TD>';
       		echo '<TD ALIGN="CENTER"><INPUT type=text name=zeit'.$i.' size=14 value=""></TD>';
       		
       		echo "<TD ALIGN=CENTER>";
         		build_select($result,"name","id","heimid".$i,"","1","-1");
       		echo "</TD>";
       		echo "<TD ALIGN=CENTER>";
       	  		build_select($result,"name","id","ausid".$i,"","1","-1");
       		echo "</TD>";
       		echo '</tr>';
       }
     
       echo '<INPUT type=hidden name="action" value="save">';
       echo '<INPUT type=hidden name="saisonid" value='.$saisonid.'>';
       echo '<TR></tr><TD COLSPAN="4" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
       echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
       echo '&nbsp;&nbsp;';
       echo '<INPUT type=submit value="Speichern">';
       echo '</TD></TR>';
      echo '</TABLE>';
     echo '</FORM>';
 break;

 case 'save':
      $saisonid=$_POST["saisonid"];
	  $grouped=$_POST["grouped"];
	  if ($grouped == "- NEU -") { 
	  	$grouped=$_POST["newgrouped"];
	  }
      
      // Daten Validieren	
  	for ($i=1; $i<=10; $i++) {
  	if (($_POST["ausid".$i] != "-1") && ($_POST["heimid".$i] != "-1")) {
  		$datum=$_POST["datum".$i];
  		$zeit=$_POST["zeit".$i];
  		$ts=ts2db($datum,$zeit);
	if ($ts == "-1") {
		echo "<br><b>Falsches Datumsformat in Spiel ".$i.": TT:MM:JJJJJ</b>";
  			$exit=1;
  		}
  		if ($ts == "-2")
  		{
       		echo "<br><b>Falsches Zeitformat in Spiel ".$i.": HH:MM</b>";
       		$exit=1;
  		}
  		
  		if ($_POST["ausid".$i] == $_POST["heimid".$i]) {
  			echo '<br><b>Fehler in Spiel '.$i.' </b>Eine Mannschaft kann nicht gegen sich selber spielen';
  			$exit=1;
  		}
  	}
    }

	if ($exit==0) {
	     for ($i=1; $i<=10; $i++) {	
      		if (($_POST["ausid".$i] != "-1") && ($_POST["heimid".$i] != "-1") && $exit==0) {
      			$datum=$_POST["datum".$i];
      			$zeit=$_POST["zeit".$i];
      			$ts=ts2db($datum,$zeit);
      			$sqlstr='insert into tt_spiele (datum,saison_id,grouped,heim_id,aus_id,gespielt) values ('
      				.$ts.','.$saisonid.','."'".$grouped."'".','
      				.$_POST["heimid".$i].','
      				.$_POST["ausid".$i].','
      				.'0)';
      			$result=doSQL($db,$sqlstr);
      			if ($result["code"]!="0") {
      				echo '<br><b>Datenbankfehler: Speicherung nicht möglich</b>';
      				print_r($result);
      				$exit=1;
      			}
      		}
      	     }
      	}
      
      if ($exit == 0)
      {
		   mlog("Tischtennis: Es wurde ein Spieltag neu eingetragen: ".$saisonid);	
           echo 'Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A>';
           echo '<SCRIPT TYPE="text/javascript">';
                echo 'opener.location.reload();';
                echo 'setTimeout("window.close()",1000);';
           echo '</SCRIPT>';
      }
      else
      {

           echo 'Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:window.history.back();">Hier klicken, um zurückzukehren</A>';
      }
 break;
}
closeConnect($db);
}
else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
?>
</BODY>
</HTML>