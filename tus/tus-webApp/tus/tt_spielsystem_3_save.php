<?php
// Werner-Scheffler
   for ($i=1; $i<=14; $i++) {
   	if ($error==0) {
   		// löschen des gespeicherten Spieles
   		$sqlstr  = "delete from tt_spiele_saetze where ";
   		$sqlstr .= "spiel_id=".$spielid." and ";
   		$sqlstr .= "spiel_nr=".$i;
   		$result=doSQL($db,$sqlstr);
		if ($result["code"]!=0) $error=1;
	}
   	
   	if ($error==0) {
	   	// Einfügen der neuen Aufstellung
   		switch ($i) {
   		case ($i<=1 || $i==2 ):
   			$sqlstr  = "insert into tt_spiele_saetze (spiel_id,spiel_nr,heim1_id,heim2_id,aus1_id,aus2_id,heim_saetze,aus_saetze) values (";
	   		$sqlstr .= $spielid.",";
   			$sqlstr .= $i.",";
   			$sqlstr .= $einzelspieler[$i]["heimmannschaft1"].",";
	   		$sqlstr .= $einzelspieler[$i]["heimmannschaft2"].",";
   			$sqlstr .= $einzelspieler[$i]["ausmannschaft1"].",";
			$sqlstr .= $einzelspieler[$i]["ausmannschaft2"].",";
			$sqlstr .= $heimsaetze[$i].",";
			$sqlstr .= $aussaetze[$i].")";
			$result=doSQL($db,$sqlstr);
			if ($result["code"]!=0) $error=1;
		break;
   		case ($i>2 && $i<=14):
   			$sqlstr  = "insert into tt_spiele_saetze (spiel_id,spiel_nr,heim1_id,heim2_id,aus1_id,aus2_id,heim_saetze,aus_saetze) values (";
   			$sqlstr .= $spielid.",";
   			$sqlstr .= $i.",";
   			$sqlstr .= $einzelspieler[$i]["heimmannschaft"].",";
   			$sqlstr .= "0,";
   			$sqlstr .= $einzelspieler[$i]["ausmannschaft"].",";
			$sqlstr .= "0,";
			$sqlstr .= $heimsaetze[$i].",";
			$sqlstr .= $aussaetze[$i].")";
			$result=doSQL($db,$sqlstr);
			if ($result["code"]!=0) $error=1;
		break;
		}
	}
   }
?>