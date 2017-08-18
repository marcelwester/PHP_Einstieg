<?php

//// tt_saetze_popup.php
////
//// letzte Änderung: Volker 25.12.2004 
//// was: Erstellung
////

session_start();
include "inc.php";
sitename("tt_saetze_popup.php",$_SESSION["groupid"]);

function get_spieler_name( $id,$mtyp) {
	global $db;
	if ($mtyp == "tus") {
		$sqlstr="select name,vorname from tt_person where id=".$id;
	} else {
		$sqlstr="select name,vorname from tt_ext_spieler where id=".$id;
	}
	$result=GetResult($db,$sqlstr);
	if (isset($result)) {
		if ($mtyp =="tus") {
			return '<a href="tt_ma_kader_info_popup.php?personid='.$id.'&descent=self">'.$result["0"]["vorname"]." ".$result["0"]["name"].'</a>';
		} else {
			return $result["0"]["vorname"]." ".$result["0"]["name"];
		}
	} else {
		return "-";
	}
}
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php
focus();
echo '<input type="image" title="Drucken" src="./images/Printer.gif" onClick="print();">';

$saisonid = $_REQUEST["saisonid"];
$tusid = $_REQUEST["tusid"];
$personid = $_REQUEST["personid"];

// Alle Spiele der Saison lesen
$sqlstr = "select id,heim_id,aus_id,datum,heim_tore,aus_tore from tt_spiele where saison_id=".$saisonid." and ".$tusid." in (heim_id,aus_id)";
$spiele = getResult($db,$sqlstr);

$siege=0; $niederlagen=0;	
$saetzeGewonnen=0; $saetzeVerloren=0;
$siegeDoppel=0; $niederlagenDoppel=0;	
$saetzeGewonnenDoppel=0; $saetzeVerlorenDoppel=0;
$spielanzahl=0;
$out = array();

foreach ($spiele as $spiel) {
	
	// Prüfen, ob es ein Heim- oder Auswärtsspiel ist
	unset ($ocholt); unset($nonocholt);
	if ($spiel["heim_id"] == $tusid) {
		$ocholt="heim";
		$nonocholt="aus";
	}
	if ($spiel["aus_id"] == $tusid) {
		$ocholt="aus";
		$nonocholt="heim";
	}

	if (! isset($ocholt)) {
		echo "Heim- oder Auswärtsspiel konnte nicht zugeorndet werden !";
		exit;
	}

// Einzel	
	// Prüfen, ob der Spieler überhaupt mitgespielt hat
	$sqlstr  = "select count(*) anz from tt_spiele_saetze where ";
	$sqlstr .= $personid." in (".$ocholt."1_id,".$ocholt."2_id) and ";
	$sqlstr .= "spiel_id=".$spiel["id"];
	$result1 = getResult($db,$sqlstr);
	$idx=0;
	if ($result1["0"]["anz"] > 0) {
		$spielanzahl ++;
		// Anzahl der gewonnenen Einzelspiele des Spielers lesen
		// Spiele, bei denen kein Gegner eingetragen ist, werden nicht gewertet
		$sqlstr  = "select count(*) anz from tt_spiele_saetze where ";
		$sqlstr .= $ocholt."1_id=".$personid." and ".$ocholt."2_id=0 and ";
		$sqlstr .= $ocholt."_saetze > ".$nonocholt."_saetze and ";
		$sqlstr .= $nonocholt."1_id<>0 and ";
		$sqlstr .= "spiel_id=".$spiel["id"];
		$result1=getResult($db,$sqlstr);
		$siege += $result1["0"]["anz"];
		
		// Anzahl der verlorenen Spiele des Spielers lesen
		$sqlstr  = "select count(*) anz from tt_spiele_saetze where ";
		$sqlstr .= $ocholt."1_id=".$personid." and ".$ocholt."2_id=0 and ";
		$sqlstr .= $ocholt."_saetze < ".$nonocholt."_saetze and ";
		$sqlstr .= "spiel_id=".$spiel["id"];
		$result1=getResult($db,$sqlstr);
		$niederlagen += $result1["0"]["anz"];
		
		// Satzverhältnis lesen
		$sqlstr  = "select sum(".$ocholt."_saetze) saetzeplus,sum(".$nonocholt."_saetze) saetzeminus from tt_spiele_saetze where ";
		$sqlstr .= $ocholt."1_id=".$personid." and ".$ocholt."2_id=0 and ";
		$sqlstr .= $nonocholt."1_id<>0 and ";
		$sqlstr .= "spiel_id=".$spiel["id"]." and ";
		$sqlstr .= "heim_saetze>-1 and aus_saetze>-1";
		$result1=getResult($db,$sqlstr);
		if (isset($result1)) {
			$saetzeGewonnen += $result1["0"]["saetzeplus"];
			$saetzeVerloren += $result1["0"]["saetzeminus"];
		}

		// Anzahl der gewonnenen Doppelspiele des Spielers lesen
		// Spiele, bei denen kein Gegner eingetragen ist, werden nicht gewertet
		$sqlstr  = "select count(*) anz from tt_spiele_saetze where ";
		$sqlstr .= $personid." in (".$ocholt."1_id,".$ocholt."2_id) and ".$ocholt."2_id>0 and ";
		$sqlstr .= $ocholt."_saetze > ".$nonocholt."_saetze and ";
		$sqlstr .= $nonocholt."1_id<>0 and ";
		$sqlstr .= "spiel_id=".$spiel["id"];
		$result1=getResult($db,$sqlstr);
		$siegeDoppel += $result1["0"]["anz"];
		
		// Anzahl der verlorenen Spiele des Spielers lesen
		$sqlstr  = "select count(*) anz from tt_spiele_saetze where ";
		$sqlstr .= $personid." in (".$ocholt."1_id,".$ocholt."2_id) and ".$ocholt."2_id>0 and ";
		$sqlstr .= $ocholt."_saetze < ".$nonocholt."_saetze and ";
		$sqlstr .= "spiel_id=".$spiel["id"];
		$result1=getResult($db,$sqlstr);
		$niederlagenDoppel += $result1["0"]["anz"];
                
		
		// Satzverhältnis lesen
		$sqlstr  = "select sum(".$ocholt."_saetze) saetzeplus,sum(".$nonocholt."_saetze) saetzeminus from tt_spiele_saetze where ";
		$sqlstr .= $personid." in (".$ocholt."1_id,".$ocholt."2_id) and ".$ocholt."2_id>0 and ";
		$sqlstr .= $nonocholt."1_id<>0 and ";
		$sqlstr .= "spiel_id=".$spiel["id"]." and ";
		$sqlstr .= "heim_saetze>-1 and aus_saetze>-1";
		$result1=getResult($db,$sqlstr);
		if (isset($result1)) {
			$saetzeGewonnenDoppel += $result1["0"]["saetzeplus"];
			$saetzeVerlorenDoppel += $result1["0"]["saetzeminus"];
		}
		
	// Spiele lesen
		// Prüfen ob die Heimmannschaft eine TuS Mannschaft ist
		$sqlstr="select id from tt_tus_mannschaft where id=".$spiel["heim_id"];
		$result=GetResult($db,$sqlstr);
		if (isset($result["0"]["id"])) {
			// Die Heimmannschaft ist eine TuS Mannschaft
			$heimmannschaft="tus";
		} else {
     			$heimmannschaft="nontus";
     		}

		// Prüfen ob die Gastannschaft eine TuS Mannschaft ist
		$sqlstr="select id from tt_tus_mannschaft where id=".$spiel["aus_id"];
		$result=GetResult($db,$sqlstr);
		if (isset($result["0"]["id"])) {
			// Die Heimmannschaft ist eine TuS Mannschaft
			$ausmannschaft="tus";
		} else {
     			$ausmannschaft="nontus";
     		}


		array_push($out,'<tr>');
		$sqlstr="select name from tt_mannschaft where id=".$spiel["heim_id"];
		$result=getResult($db,$sqlstr);
		$heim=$result["0"]["name"];

		$sqlstr="select name from tt_mannschaft where id=".$spiel["aus_id"];
		$result=getResult($db,$sqlstr);
		$aus=$result["0"]["name"];
		array_push($out,'<td align="center" colspan="4">');
			array_push($out,'<b>'.$heim.' - '.$aus.'</b>');
			array_push($out,"<br>".date("d.m.Y - H:i", strtotime($spiel["datum"])));
			array_push($out,"<br>".$spiel["heim_tore"]." : ".$spiel["aus_tore"]);
		array_push($out,'</td></tr>');

		$sqlstr  = "select spiel_nr,heim1_id,heim2_id,aus1_id,aus2_id,heim_saetze,aus_saetze from tt_spiele_saetze where ";
		$sqlstr .= "spiel_id =".$spiel["id"]." and ".$personid." in (".$ocholt."1_id,".$ocholt."2_id) ";
		$sqlstr .= "order by spiel_nr";	
		$result1 = getResult($db,$sqlstr);
		foreach ($result1 as $satz) {
		// Ausgabe erstellen
			$ausgabe["datum"] = $spiel["datum"];
			$ausgabe["heim1"] = get_spieler_name($satz["heim1_id"],$heimmannschaft);
			if ($satz["heim2_id"] > 0) 
				$ausgabe["heim2"] = " / ".get_spieler_name($satz["heim2_id"],$heimmannschaft);
			else
				$ausgabe["heim2"]="";
				
			$ausgabe["aus1"] = get_spieler_name($satz["aus1_id"],$ausmannschaft);
			if ($satz["aus2_id"] > 0) 
				$ausgabe["aus2"] = " / ".get_spieler_name($satz["aus2_id"],$ausmannschaft);
			else
				$ausgabe["aus2"]="";
			
			array_push($out,'<tr>');
				if ($satz["heim_saetze"]>-1 && $satz["aus_saetze"]>-1) {
					array_push($out,'<td align="center">');
						array_push($out,$satz["spiel_nr"]);
					array_push($out,'</td>');
					array_push($out,'<td align="center">');
						array_push($out,$ausgabe["heim1"].$ausgabe["heim2"]);
					array_push($out,'</td>');
		
					array_push($out,'<td align="center">');
						array_push($out,$ausgabe["aus1"].$ausgabe["aus2"]);
					array_push($out,'</td>');
					
					array_push($out,'<td align="center">');
							array_push($out,$satz["heim_saetze"]." : ".$satz["aus_saetze"]);
					array_push($out,'</td>');
				}
			array_push($out,'</tr>');	
		}
		array_push($out,'<td align="center" colspan="4">');
			array_push($out,'&nbsp;');
		array_push($out,'</td></tr>');
	}
}	

 // Spielzeit lesen
$sqlstr ="select liga,spielzeit from tt_saison where id=".$saisonid;
$result = GetResult($db, $sqlstr);
$liga=$result[0]["liga"];
$spielzeit=$result[0]["spielzeit"];

$sqlstr="select name,vorname from tt_person where id = ".$personid;
$result=GetResult($db,$sqlstr);


// Ausgabe starten

echo '<TABLE WIDTH="100%">';
echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
echo '<B>Spielerstatistik</B><BR>'.$liga.' - '.$spielzeit.'<br>';
echo  $result["0"]["vorname"].' '.$result["0"]["name"].'</td></tr>';
echo '</TABLE>';
echo '<br><br>';

echo '<table align="center" width="60%">';
	echo '<tr>';
		echo '<td colspan="2" BGCOLOR="#DDDDDD" align="center">';
			echo 'Allgemein';		
		echo '</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center" width="50%">';
			echo 'Absolvierte Begegnungen';
		echo '</td>';
		echo '<td align="center" width="50%">';
			echo $spielanzahl;
		echo '</td>';
	echo '</tr>';

	echo '<tr>';
		echo '<td colspan="2" BGCOLOR="#DDDDDD" align="center">';
			echo 'Einzelspiele';		
		echo '</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center">';
			echo 'Spiele';
		echo '</td>';
		echo '<td align="center">';
			echo $siege.':'.$niederlagen;
		echo '</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center">';
			echo 'Sätze';
		echo '</td>';
		echo '<td align="center">';
			echo $saetzeGewonnen.':'.$saetzeVerloren;
		echo '</td>';
	echo '</tr>';
	
	echo '<tr>';
		echo '<td colspan="2" BGCOLOR="#DDDDDD" align="center">';
			echo 'Doppelspiele';		
		echo '</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center">';
			echo 'Spiele';
		echo '</td>';
		echo '<td align="center">';
			echo $siegeDoppel.':'.$niederlagenDoppel;
		echo '</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center">';
			echo 'Sätze';
		echo '</td>';
		echo '<td align="center">';
			echo $saetzeGewonnenDoppel.':'.$saetzeVerlorenDoppel;
		echo '</td>';
	echo '</tr>';
echo '</table>';
echo '<br><br>';
echo '<table align="center" width="100%">';
	echo '<tr>';
		echo '<td colspan="4" BGCOLOR="#DDDDDD" align="center">';
			echo '<b>Begegnungen</b>';		
		echo '</td>';
	echo '</tr>';

	foreach ($out as $row) {
		echo $row;
	}
echo '</table>';

echo '<br>';
echo '<TABLE WIDTH="100%" BORDER="0" ALIGN="CENTER">';
echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD" >';
if (isset($_REQUEST["descent"])) {
	echo '<INPUT TYPE="button" VALUE="Zurück" onClick="window.history.back();">';
	echo '&nbsp;&nbsp;&nbsp;';
}
echo '<INPUT TYPE="button" VALUE="Schliessen" onClick="window.close();">';
echo '</TD></TR>';
echo '</table>';


closeConnect($db);

?>
</BODY>
</HTML>