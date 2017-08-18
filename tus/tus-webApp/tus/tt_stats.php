<?php
# Statistik.php
include "inc.php";

?>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php

$saisonid=1;
$tusid=1;
$personid=1;
$pk="4,5,10,11";

	$siege=0;
	$niederlagen=0;
	// Spiele der Saison lesen
	$sqlstr  = "select id,heim_id,aus_id from tt_spiele where ";
	$sqlstr .= "saison_id=".$saisonid." and ";
	$sqlstr .= $tusid." in (heim_id,aus_id) order by id";
	$result=getResult($db,$sqlstr);
	foreach ($result as $spiel) {
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
	
		// Saetze zu den Spielen lesen
		
		// Anzahl der gewonnenen Spiele des Spielers lesen
		$sqlstr  = "select count(*) anz from tt_spiele_saetze where ";
		$sqlstr .= $ocholt."1_id=".$personid." and ";
		$sqlstr .= $ocholt."_saetze > ".$nonocholt."_saetze and ";
		$sqlstr .= "spiel_id=".$spiel["id"]." and ";
		$sqlstr .= "spiel_nr in (".$pk.")";
		$result1=getResult($db,$sqlstr);
		$siege += $result1["0"]["anz"];
		echo "<br>".$sqlstr." ==> ".$result1["0"]["anz"]." ".$ocholt;

		// Anzahl der verlorenen Spiele des Spielers lesen
		$sqlstr  = "select count(*) anz from tt_spiele_saetze where ";
		$sqlstr .= $ocholt."1_id=".$personid." and ";
		$sqlstr .= $ocholt."_saetze < ".$nonocholt."_saetze and ";
		$sqlstr .= "spiel_id=".$spiel["id"]." and ";
		$sqlstr .= "spiel_nr in (".$pk.")";
		$result1=getResult($db,$sqlstr);
		$niederlagen += $result1["0"]["anz"];
	}
	echo "$siege : $niederlagen";

closeConnect($db);
?>
</BODY>