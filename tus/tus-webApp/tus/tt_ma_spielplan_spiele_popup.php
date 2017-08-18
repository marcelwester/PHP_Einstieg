<?php
//// tt_ma_spielplan_spiele_popup.php
////
//// letzte Änderung: Volker 11.10.2004
//// Erstellung
////

include "inc.php";
sitename("tt_ma_spielplan_spiele_popup.php",$_SESSION["groupid"]);
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php

function convert_null($val) {
	if (!isset($val)) {
		$val=0;
	}
	return $val;
}


focus();
if (priv("spiele_edit"))
{

focus();
// TuS Mannschaft identifizieren aus (Gast oder Heim)
$spielid=$_REQUEST["spielid"];
$sqlstr="select saison_id,tus.id from tt_spiele sp,tt_tus_mannschaft tus where tus.id in (sp.aus_id,sp.heim_id) and sp.id=".$spielid;
$result=GetResult($db,$sqlstr);
$tus_teamid=$result["0"]["id"];
$saisonid=$result["0"]["saison_id"];

	$userid=$_SESSION["userid"];
	$result=GetResult($db,"select name from sys_users where userid=$userid");
	$username=$result["0"]["name"];

	// Spielzeit lesen
     	$sqlstr ="select liga,spielzeit,spielsystem from tt_saison where id=$saisonid";
     	$result = GetResult($db, $sqlstr);
     	$liga=$result[0]["liga"];
     	$spielzeit=$result[0]["spielzeit"];
	$spielsystem=$result[0]["spielsystem"];





switch ($_REQUEST["action"])
{
	case 'edit':
    		
     		echo '<TABLE WIDTH="100%">';
     		echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
     		echo '<B>Spiel - Aufstellung</B><BR>'.$liga.' - '.$spielzeit.'<BR> - '.$username.' - </TD></TR>';
     		echo '</TABLE>';

     		// Lesen der Begegnung und Kategorie der Mannschaft lesen
     		$sqlstr="select ma.id,ma.name,kat from tt_mannschaft ma,tt_spiele sp where ";
     		$sqlstr.="heim_id=ma.id and sp.id=".$spielid;
     		$result=GetResult($db,$sqlstr);
		$heim=$result["0"]["name"];
		$heimid=$result["0"]["id"];
     		$kat=$result["0"]["kat"];
     		
     		$sqlstr="select ma.id,ma.name from tt_mannschaft ma,tt_spiele sp where ";
     		$sqlstr.="aus_id=ma.id and sp.id=".$spielid;
     		$result=GetResult($db,$sqlstr);
     		$aus=$result["0"]["name"];
  		$ausid=$result["0"]["id"];
     		
		// Prüfen ob die Heimmannschaft eine TuS Mannschaft ist
		$sqlstr="select id from tt_tus_mannschaft where id=".$heimid;
		$result=GetResult($db,$sqlstr);
		if (isset($result["0"]["id"])) {
			// Die Heimmannschaft ist eine TuS Mannschaft
			$heimmannschaft="tus";
		} else {
     			$heimmannschaft="nontus";
     		}

		// Prüfen ob die Gastannschaft eine TuS Mannschaft ist
		$sqlstr="select id from tt_tus_mannschaft where id=".$ausid;
		$result=GetResult($db,$sqlstr);
		if (isset($result["0"]["id"])) {
			// Die Heimmannschaft ist eine TuS Mannschaft
			$ausmannschaft="tus";
		} else {
     			$ausmannschaft="nontus";
     		}

		// Lesen der Aufstellung der Heimmanschaft
		if ($heimmannschaft=="tus") {
			$sqlstr  = "select name,vorname,spieler_id,position,team_id from tt_zw_tt_person_tt_spiele,tt_person where ";
			$sqlstr .= "spieler_id = tt_person.id and ";
			$sqlstr .= "spiel_id=".$spielid." and ";
			$sqlstr .= "team_id=".$heimid;
			$sqlstr .= " order by position";
			$heimteam=GetResult($db,$sqlstr);
		} else {
			$sqlstr  = "select name,vorname,spieler_id,position,a.team_id from tt_zw_ext_spieler_tt_spiele a,tt_ext_spieler where ";
			$sqlstr .= "spieler_id = tt_ext_spieler.id and ";
			$sqlstr .= "spiel_id=".$spielid." and ";
			$sqlstr .= "a.team_id=".$heimid;
			$sqlstr .= " order by position";
			$heimteam=GetResult($db,$sqlstr);
     		}
     	
		// Lesen der Aufstellung der Gastmanschaft
		if ($ausmannschaft=="tus") {
			$sqlstr  = "select name,vorname,spieler_id,position,team_id from tt_zw_tt_person_tt_spiele,tt_person where ";
			$sqlstr .= "spieler_id = tt_person.id and ";
			$sqlstr .= "spiel_id=".$spielid." and ";
			$sqlstr .= "team_id=".$ausid;
			$sqlstr .= " order by position";
			$austeam=GetResult($db,$sqlstr);
		} else {
			$sqlstr  = "select name,vorname,spieler_id,position,a.team_id from tt_zw_ext_spieler_tt_spiele a,tt_ext_spieler where ";
			$sqlstr .= "spieler_id = tt_ext_spieler.id and ";
			$sqlstr .= "spiel_id=".$spielid." and ";
			$sqlstr .= "a.team_id=".$ausid;
			$sqlstr .= " order by position";
			$austeam=GetResult($db,$sqlstr);
     		}
     		
     		$i=0;
     		foreach ($heimteam as $row) {
     			$heimteam[$i]["anzeige"]=$row["name"].", ".$row["vorname"];
     			$i++;
     		}
     		$i=0;
     		foreach ($austeam as $row) {
     			$austeam[$i]["anzeige"]=$row["name"].", ".$row["vorname"];
     			$i++;
     		}
		$heimteam["-1"]["anzeige"]="-";
		$heimteam["-1"]["spieler_id"]="0";
		$austeam["-1"]["anzeige"]="-";
		$austeam["-1"]["spieler_id"]="0";

     		
//     		print_r($heimteam);
//     		echo '<br><br>';
//     		print_r($austeam);
     		
     		
     		//echo '<center><h2><u>'.$heim.'</u>&nbsp; - &nbsp;<u>'.$aus.'</u></h2></center>';


		echo '<br>';
		echo '<table width="100%">';
		echo '<tr>';
			echo '<td bgcolor="#DDDDDD" width="5%" align="center"><b>Nr</b></td>';
			echo '<td bgcolor="#DDDDDD" width="35%" colspan="2" align="center"><b>'.$heim.'</b></td>';
			echo '<td bgcolor="#DDDDDD" width="35%" colspan="2" align="center"><b>'.$aus.'</b></td>';
			echo '<td bgcolor="#DDDDDD" width="10%" align="center"><b>Heim</b></td>';
			echo '<td bgcolor="#DDDDDD" width="10%" align="center"><b>Gast</b></td>';
		echo '</tr>';
		
		
	$ergebnis ["-1"]["anzeige"]="-";
	$ergebnis ["-1"]["wert"]="-1";
	$ergebnis ["0"]["anzeige"]="0";
	$ergebnis ["0"]["wert"]="0";
	$ergebnis ["1"]["anzeige"]="1";
	$ergebnis ["1"]["wert"]="1";
	$ergebnis ["2"]["anzeige"]="2";
	$ergebnis ["2"]["wert"]="2";
	$ergebnis ["3"]["anzeige"]="3";
	$ergebnis ["3"]["wert"]="3";
	
	// Prüfen, ob das Ergebnis schon mal gespeichert wurde:
	$sqlstr="select count(*) anz from tt_spiele_saetze where spiel_id=".$spielid;
	$result=GetResult($db,$sqlstr);
	// Spiele müssen abghängig von der Kategorie der Mannschaft gespeichert werden
	switch ($spielsystem) {
		case 1;
			// Sechser-Paarkreutz
			include "tt_spielsystem_1_edit.php";
		break;	
		case 2;
			// Vierer Paarkreutz
			include "tt_spielsystem_2_edit.php";
		break;
		case 3;
			// Werner Scheffler System
			include "tt_spielsystem_3_edit.php";
		break;
		case 4;
			// Dreier Paarkreutz
			include "tt_spielsystem_4_edit.php";
		break;

	}
		

    break;

    case 'save':
    
  // echo '<br>heimid '.$_POST["heimid"];
  // echo '<br>ausid '.$_POST["ausid"];
  // echo '<br>heimmannschaft '.$_POST["heimmannschaft"];
  // echo '<br>ausmannschaft '.$_POST["ausmannschaft"];
   
   $einzelspieler=$_POST["einzelspieler"];
  
   $heimsaetze=$_POST["heimsaetze"];
   $aussaetze=$_POST["aussaetze"];
   $error=0;
   switch ($spielsystem) {
	case 1;
		// Sechser-Paarkreutz
		include "tt_spielsystem_1_save.php";
	break;	
	case 2;
		// Vierer Paarkreutz
		include "tt_spielsystem_2_save.php";
	break;
	case 3;
		// Werner Scheffler System
		include "tt_spielsystem_3_save.php";
	break;
	case 4;
		// Dreier Paarkreutz
		include "tt_spielsystem_4_save.php";
	break;

   }
   
   

     if ($error == 0)
      {
           mlog("Tischtennis: Es wurden die Sätze zu einem Spiel gespeichert: ".$spielid);
           echo 'Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A>';
           echo '<SCRIPT TYPE="text/javascript">';
                echo 'opener.location.reload();';
                echo 'setTimeout("window.close()",1000);';
           echo '</SCRIPT>';
      }

      else
           echo 'Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:window.history.back();">Hier klicken, um zurückzukehren</A>';
           //echo 'Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A>';

      //echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A>';

       break;
}

}
else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';


closeConnect($db);
?>
</BODY>
</HTML>