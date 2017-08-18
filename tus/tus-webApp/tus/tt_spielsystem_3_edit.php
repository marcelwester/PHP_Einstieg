<?php
// Werner-Scheffler		
		if ($result["0"]["anz"]==14) {
			// Spiel wurde schon erfolgreich gespeichert
			echo "";
		} else {
			// Aufstellungen der Mannschaften lesen:
			if ($heimmannschaft=="tus") {
				$sqlstr  = "select spieler_id,position from tt_zw_tt_person_tt_spiele where ";
				$sqlstr .= "spiel_id=".$spielid." and ";
				$sqlstr .= "team_id=".$heimid." order by position";
			} else {
				$sqlstr  = "select spieler_id,position from tt_zw_ext_spieler_tt_spiele where ";
				$sqlstr .= "spiel_id=".$spielid." and ";
				$sqlstr .= "team_id=".$heimid." order by position";
			}
			$resultheim=GetResult($db,$sqlstr);
			
			if ($ausmannschaft=="tus") {
				$sqlstr  = "select spieler_id,position from tt_zw_tt_person_tt_spiele where ";
				$sqlstr .= "spiel_id=".$spielid." and ";
				$sqlstr .= "team_id=".$ausid." order by position";
			} else {
				$sqlstr  = "select spieler_id,position from tt_zw_ext_spieler_tt_spiele where ";
				$sqlstr .= "spiel_id=".$spielid." and ";
				$sqlstr .= "team_id=".$ausid." order by position";
			}
			$resultaus=GetResult($db,$sqlstr);

			// Prüfen der Aufstellung: Es wüssen jeweils mindestens 4 Spieler vorhanden sein
			if ((!isset($resultheim["3"]["spieler_id"]) || !isset($resultaus["3"]["spieler_id"])) && !isset($_POST["force"])) {
				echo '</table>';
				echo "<br>Überprüfen Sie die Aufstellung. Es müssen beim ersten Aufruf dieser Seite von jeder Mannschaft mind. 4 Spieler aufgestellt sein.";
			        //echo '<br><br>Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:window.close();">Hier klicken, das Fenster zu schließen</A>';
				echo '<br><br>';
				echo '<form method="post" action="tt_ma_spielplan_spiele_popup.php?action=edit&spielid='.$spielid.'&PHPSESSID='.session_id().'">';		
		            		echo '<INPUT TYPE="button"  VALUE="Abbrechen" onClick="window.close();">';
            				echo '&nbsp;&nbsp;';
            				echo '<INPUT type=submit value="Zur Spieleingabe" name="force">';
            			
            			echo '</form>';
				closeConnect($db);
				exit;
			}

			// Spiel wurde noch nicht oder fehlerhaft gespeichert
			$sqlstr="delete from tt_spiele_saetze where spiel_id=".$spielid;
			$result=doSQL($db,$sqlstr);

			// Doppelbegegnungen:
			for ($i=1;$i<=2; $i++) {
				$sqlstr  = "insert into tt_spiele_saetze (spiel_id,spiel_nr,heim1_id,heim2_id,aus1_id,aus2_id) values (";
				$sqlstr .= $spielid.",";
				$sqlstr .= $i.",0,0,0,0)";
				$result=doSQL($db,$sqlstr);
			}

			// Einzelbegegnungen

			// Eintragen der Paarkreutze in die tt_spiele_saetze
			$pre_sqlstr= "insert into tt_spiele_saetze (spiel_id,spiel_nr,heim1_id,aus1_id) values (";
			$sqlstr = $pre_sqlstr.$spielid.",3,".convert_null($resultheim["0"]["spieler_id"]).",".convert_null($resultaus["1"]["spieler_id"]).")";
			$result=doSQL($db,$sqlstr);
			$sqlstr = $pre_sqlstr.$spielid.",4,".convert_null($resultheim["1"]["spieler_id"]).",".convert_null($resultaus["0"]["spieler_id"]).")";
			$result=doSQL($db,$sqlstr);
			$sqlstr = $pre_sqlstr.$spielid.",5,".convert_null($resultheim["2"]["spieler_id"]).",".convert_null($resultaus["3"]["spieler_id"]).")";
			$result=doSQL($db,$sqlstr);
			$sqlstr = $pre_sqlstr.$spielid.",6,".convert_null($resultheim["3"]["spieler_id"]).",".convert_null($resultaus["2"]["spieler_id"]).")";
			$result=doSQL($db,$sqlstr);
			
			//$sqlstr = $pre_sqlstr.$spielid.",8,".convert_null($resultheim["4"]["spieler_id"]).",".convert_null($resultaus["5"]["spieler_id"]).")";
			//$result=doSQL($db,$sqlstr);
			//$sqlstr = $pre_sqlstr.$spielid.",9,".convert_null($resultheim["5"]["spieler_id"]).",".convert_null($resultaus["4"]["spieler_id"]).")";
			//$result=doSQL($db,$sqlstr);
			
			$sqlstr = $pre_sqlstr.$spielid.",7,".convert_null($resultheim["0"]["spieler_id"]).",".convert_null($resultaus["0"]["spieler_id"]).")";
			$result=doSQL($db,$sqlstr);
			$sqlstr = $pre_sqlstr.$spielid.",8,".convert_null($resultheim["1"]["spieler_id"]).",".convert_null($resultaus["1"]["spieler_id"]).")";
			$result=doSQL($db,$sqlstr);
			$sqlstr = $pre_sqlstr.$spielid.",9,".convert_null($resultheim["2"]["spieler_id"]).",".convert_null($resultaus["2"]["spieler_id"]).")";
			$result=doSQL($db,$sqlstr);
			$sqlstr = $pre_sqlstr.$spielid.",10,".convert_null($resultheim["3"]["spieler_id"]).",".convert_null($resultaus["3"]["spieler_id"]).")";
			$result=doSQL($db,$sqlstr);

			$sqlstr = $pre_sqlstr.$spielid.",11,".convert_null($resultheim["2"]["spieler_id"]).",".convert_null($resultaus["0"]["spieler_id"]).")";
			$result=doSQL($db,$sqlstr);
			$sqlstr = $pre_sqlstr.$spielid.",12,".convert_null($resultheim["0"]["spieler_id"]).",".convert_null($resultaus["2"]["spieler_id"]).")";
			$result=doSQL($db,$sqlstr);
			$sqlstr = $pre_sqlstr.$spielid.",13,".convert_null($resultheim["1"]["spieler_id"]).",".convert_null($resultaus["3"]["spieler_id"]).")";
			$result=doSQL($db,$sqlstr);
			$sqlstr = $pre_sqlstr.$spielid.",14,".convert_null($resultheim["3"]["spieler_id"]).",".convert_null($resultaus["1"]["spieler_id"]).")";
			$result=doSQL($db,$sqlstr);
			
			//$sqlstr = $pre_sqlstr.$spielid.",14,".convert_null($resultheim["4"]["spieler_id"]).",".convert_null($resultaus["4"]["spieler_id"]).")";
			//$result=doSQL($db,$sqlstr);
			//$sqlstr = $pre_sqlstr.$spielid.",15,".convert_null($resultheim["5"]["spieler_id"]).",".convert_null($resultaus["5"]["spieler_id"]).")";
			//$result=doSQL($db,$sqlstr);

			// Letzten beiden Doppelbegegnungen (Wenn bis dahin keine Entscheidung gefallen ist)
			//$sqlstr  = "insert into tt_spiele_saetze (spiel_id,spiel_nr,heim1_id,heim2_id,aus1_id,aus2_id) values (";
			//$sqlstr .= $spielid.",";
			//$sqlstr .= "11,0,0,0,0)";
			//$result=doSQL($db,$sqlstr);

			//$sqlstr  = "insert into tt_spiele_saetze (spiel_id,spiel_nr,heim1_id,heim2_id,aus1_id,aus2_id) values (";
			//$sqlstr .= $spielid.",";
			//$sqlstr .= "12,0,0,0,0)";
			//$result=doSQL($db,$sqlstr);

	}
	echo '<FORM method="post" action="tt_ma_spielplan_spiele_popup.php?action=save&spielid='.$spielid.'&PHPSESSID='.session_id().'">';		
		
	// Doppelspiele:

		for ($i=1; $i<=2; $i++) {
			echo '<tr>';
				$sqlstr  = "select heim1_id,heim2_id,aus1_id,aus2_id,heim_saetze,aus_saetze from tt_spiele_saetze where ";
				$sqlstr .= "spiel_id=".$spielid." and spiel_nr=".$i;
				$result=GetResult($db,$sqlstr);
				$result=$result["0"];
				echo '<td align="center">'.($i).'</td>';
				echo '<input type="hidden" name="spielnr['.$i.']" value="['.$i.'"]>';
				echo '<td align="center">';
					build_select($heimteam,"anzeige","spieler_id","einzelspieler[".$i."][heimmannschaft1]","",1,$result["heim1_id"]);
				echo '</td>';	
				echo '<td align="center">';
					build_select($heimteam,"anzeige","spieler_id","einzelspieler[".$i."][heimmannschaft2]","",1,$result["heim2_id"]);
				echo '</td>';	
				echo '<td align="center">';
					build_select($austeam,"anzeige","spieler_id","einzelspieler[".$i."][ausmannschaft1]","",1,$result["aus1_id"]);
				echo '</td>';	
				echo '<td align="center">';
					build_select($austeam,"anzeige","spieler_id","einzelspieler[".$i."][ausmannschaft2]","",1,$result["aus2_id"]);
				echo '</td>';	
				echo '<td align="center">';
					build_select($ergebnis,"anzeige","wert","heimsaetze[".$i."]","",1,$result["heim_saetze"]);
				echo '</td>';	
				echo '<td align="center">';
					build_select($ergebnis,"anzeige","wert","aussaetze[".$i."]","",1,$result["aus_saetze"]);
				echo '</td>';	
			echo '</tr>';
		}

	// Einzelspiele:
		echo '<tr>';
			echo '<td  align="center" colspan="7"><br></td>';
		echo '</tr>';

		for ($i=3; $i<=14; $i++) {
			echo '<tr>';
				$sqlstr  = "select heim1_id,heim2_id,aus1_id,aus2_id,heim_saetze,aus_saetze from tt_spiele_saetze where ";
				$sqlstr .= "spiel_id=".$spielid." and spiel_nr=".$i;
				$result=GetResult($db,$sqlstr);
				$result=$result["0"];

				echo '<td align="center">'.($i).'</td>';
				echo '<input type="hidden" name="spielnr['.$i.']" value="['.$i.'"]>';
				echo '<td align="center" colspan="2">';
					build_select($heimteam,"anzeige","spieler_id","einzelspieler[".$i."][heimmannschaft]","",1,$result["heim1_id"]);
				echo '</td>';	
				echo '<td align="center" colspan="2">';
					build_select($austeam,"anzeige","spieler_id","einzelspieler[".$i."][ausmannschaft]","",1,$result["aus1_id"]);
				echo '</td>';	
				echo '<td align="center">';
					build_select($ergebnis,"anzeige","wert","heimsaetze[".$i."]","",1,$result["heim_saetze"]);
				echo '</td>';	
				echo '<td align="center">';
					build_select($ergebnis,"anzeige","wert","aussaetze[".$i."]","",1,$result["aus_saetze"]);
				echo '</td>';	
		
			echo '</tr>';
		}

		echo '<tr>';
			echo '<td  align="center" colspan="7"><br></td>';
		echo '</tr>';

		echo '<tr>';
			echo '<td align="center" colspan="7">';
				echo '<INPUT TYPE="hidden" NAME="heimid" VALUE="'.$heimid.'">';
				echo '<INPUT TYPE="hidden" NAME="ausid" VALUE="'.$ausid.'">';
				echo '<INPUT TYPE="hidden" NAME="heimmannschaft" VALUE="'.$heimmannschaft.'">';
				echo '<INPUT TYPE="hidden" NAME="ausmannschaft" VALUE="'.$ausmannschaft.'">';					
	            		echo '<INPUT TYPE="button" VALUE="Beenden" onClick="window.close();">';
            			echo '&nbsp;&nbsp;';
            			echo '<INPUT type=submit value="Speichern">';
			echo '</td>';
		echo '</tr>';

		
		echo '</table>';
	echo '</form>';		
	//echo 'Spiele mit dem Ergebnis "- : -" werden in der Spielanzeige ausgeblendet'; 
?>