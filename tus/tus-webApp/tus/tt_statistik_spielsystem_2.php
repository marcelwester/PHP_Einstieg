<?php
               // Statistiken Spielsystem 1 (Vierer Paarkreutz)
             
                              
               echo '<CENTER><TABLE WIDTH="40%" BORDER="0">';
                 echo '<TR>';
                 echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                 echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                 echo '<b>Statistiken</b>';
                 echo '</TD>';
                 echo '</TR>';
               echo '</TABLE></CENTER><BR>';
               
               // $teamid
               
               // Lesen der Spieler lt. Aufstellung
               // todo: Es müssen alle Spieler gelesen werden, die in der Saison mitgespielt haben !
               $sqlstr  = "select person_id,id,position,vorname,name from  tt_zwtab_person_typ_tus_mannschaft,tt_person where ";
               $sqlstr .= "person_id=id and ";
               $sqlstr .= "saison_id=".$saisonid." and ";
               $sqlstr .= "tus_mannschaft_id=".$teamid." and ";
               $sqlstr .= "persontyp_id=2 ";
               $sqlstr .= "order by position";
               $result=getResult($db,$sqlstr);
               
               
		   echo '<CENTER><TABLE WIDTH="70%" BORDER="0">';
		   echo '<TR>';
			   echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
			   	echo '<B>Nr.</B>';
			   echo '</TD>';
			   echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
			   	echo '<B>Spieler</B>';
			   echo '</TD>';
			   echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
			   	echo '<B>Statistik</B>';
			   echo '</TD>';
			   echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
			   	echo '<B>Bemerkung</B>';
			   echo '</TD>';
		   	   echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
			   	echo '<B>Ob. Pk</B>';
			   echo '</TD>';
//		   	   echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
//			   	echo '<B>Mit. Pk</B>';
//			   echo '</TD>';
		   	   echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
			   	echo '<B>Unt. Pk</B>';
			   echo '</TD>';
		   	   echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
			   	echo '<B>Gesamt</B>';
			   echo '</TD>';
		   echo '</TR>';	
               	

		$obpk_siege=0; $obpk_niederlagen=0;
		//$mipk_siege=0; $mipk_niederlagen=0;
		$unpk_siege=0; $unpk_niederlagen=0;
		$gesamt_siege=0; $gesamt_niederlagen=0;
               	$aufstellungsspieler="0";
		foreach ($result as $row) {
			$siege=0;
			$niederlagen=0;
			$aufstellungsspieler .= ",".$row["person_id"];
			echo '<TR>';
				echo '<TD align="center">';
					echo $row["position"];
				echo '</TD>';
				echo '<TD>';
					echo '<a href="javascript:info('.$row["person_id"].','.$saisonid.');">';
						echo $row["name"].", ".$row["vorname"];
					echo '</a>';
				echo '</TD>';
                                echo '<TD ALIGN="CENTER">';
                                  echo '<a href="javascript:info_spiele('.$row["person_id"].');">';
                                  	echo '<IMG SRC="images/info.gif" BORDER="0" ALT="Info">';
                                  echo '</a>';
				echo '</td>';

				echo '<TD align="center">';
					echo "&nbsp;";
				echo '</TD>';
				echo '<TD align="center">';
					$rs = getEinzel($row["person_id"],$saisonid,$teamid,"3,4,7,8");
					if ($rs["siege"]!="0" || $rs["niederlagen"]!="0") {
						echo $rs["siege"]." : ".$rs["niederlagen"];
						$siege += $rs["siege"];
						$niederlagen += $rs["niederlagen"];
						$obpk_siege += $rs["siege"];
						$obpk_niederlagen += $rs["niederlagen"];
					} else
						echo "&nbsp;";
				echo '</TD>';
/*
				echo '<TD align="center">';
					$rs=getEinzel($row["id"],$saisonid,$teamid,"6,7,12,13");
					if ($rs["siege"]!="0" || $rs["niederlagen"]!="0") {
						echo $rs["siege"]." : ".$rs["niederlagen"];
						$siege += $rs["siege"];
						$niederlagen += $rs["niederlagen"];
						$mipk_siege += $rs["siege"];
						$mipk_niederlagen += $rs["niederlagen"];
					} else
						echo "&nbsp;";

				echo '</TD>';
*/
				echo '<TD align="center">';
					$rs=getEinzel($row["id"],$saisonid,$teamid,"5,6,9,10");
					if ($rs["siege"]!="0" || $rs["niederlagen"]!="0") {
						echo $rs["siege"]." : ".$rs["niederlagen"];
						$siege += $rs["siege"];
						$niederlagen += $rs["niederlagen"];
						$unpk_siege += $rs["siege"];
						$unpk_niederlagen += $rs["niederlagen"];
					} else
						echo "&nbsp;";
				echo '</TD>';
				echo '<TD align="center">';
					echo "$siege : $niederlagen";
					$gesamt_siege += $siege;
					$gesamt_niederlagen += $niederlagen;
				echo '</TD>';
			echo '</TR>';
		}
		
		// Lesen der Ersatzspieler
		// 1. Ersatzspieler der Heimspiele lesen
		$sqlstr  = "select distinct(heim1_id) from tt_spiele_saetze,tt_spiele where ";
		$sqlstr .= "spiel_id=id and heim_id=".$teamid." and saison_id=".$saisonid." and ";
		$sqlstr .= "heim1_id not in (".$aufstellungsspieler.")";
		$resultheim=getResult($db,$sqlstr);
				
		// 2. Ersatzspieler der Auswärtspiele lesen
		$sqlstr  = "select distinct(aus1_id) from tt_spiele_saetze,tt_spiele where ";
		$sqlstr .= "spiel_id=id and aus_id=".$teamid." and saison_id=".$saisonid." and ";
		$sqlstr .= "aus1_id not in (".$aufstellungsspieler.")";
		$resultaus=getResult($db,$sqlstr);
		
		$ersatz = array();
		foreach ($resultheim as $row) {
			if (!in_array ($row["heim1_id"],$ersatz)) {
				array_push($ersatz,$row["heim1_id"]);
			}
		}
		
		foreach ($resultaus as $row) {
			if (!in_array ($row["aus1_id"],$ersatz)) {
				array_push($ersatz,$row["aus1_id"]);
			}
		}
		
		// Statistiken der Ersatzspieler
		foreach ($ersatz as $spielerid) {
			$siege=0;
			$niederlagen=0;
			echo '<TR>';
				echo '<TD>';
					echo '&nbsp;';
				echo '</TD>';
				echo '<TD>';
					echo '<a href="javascript:info('.$spielerid.','.$saisonid.');">';
						echo getSpieler($spielerid);
					echo '</a>';
				echo '</TD>';
                                echo '<TD ALIGN="CENTER">';
                                  echo '<a href="javascript:info_spiele('.$spielerid.');">';
                                  	echo '<IMG SRC="images/info.gif" BORDER="0" ALT="Info">';
                                  echo '</a>';
                                echo '</td>';
				echo '<TD align="center">';
					echo "ERSATZ";
				echo '</TD>';
				echo '<TD align="center">';
					$rs = getEinzel($spielerid,$saisonid,$teamid,"3,4,7,8");
					if ($rs["siege"]!="0" || $rs["niederlagen"]!="0") {
						echo $rs["siege"]." : ".$rs["niederlagen"];
						$siege += $rs["siege"];
						$niederlagen += $rs["niederlagen"];
						$obpk_siege += $rs["siege"];
						$obpk_niederlagen += $rs["niederlagen"];
					} else
						echo "&nbsp;";
				echo '</TD>';
/*
				echo '<TD align="center">';
					$rs=getEinzel($spielerid,$saisonid,$teamid,"6,7,12,13");
					if ($rs["siege"]!="0" || $rs["niederlagen"]!="0") {
						echo $rs["siege"]." : ".$rs["niederlagen"];
						$siege += $rs["siege"];
						$niederlagen += $rs["niederlagen"];
						$mipk_siege += $rs["siege"];
						$mipk_niederlagen += $rs["niederlagen"];
					} else
						echo "&nbsp;";

				echo '</TD>';
*/
				echo '<TD align="center">';
					$rs=getEinzel($spielerid,$saisonid,$teamid,"5,6,9,10");
					if ($rs["siege"]!="0" || $rs["niederlagen"]!="0") {
						echo $rs["siege"]." : ".$rs["niederlagen"];
						$siege += $rs["siege"];
						$niederlagen += $rs["niederlagen"];
						$unpk_siege += $rs["siege"];
						$unpk_niederlagen += $rs["niederlagen"];
					} else
						echo "&nbsp;";
				echo '</TD>';
				echo '<TD align="center">';
					echo "$siege : $niederlagen";
					$gesamt_siege += $siege;
					$gesamt_niederlagen += $niederlagen;
				echo '</TD>';
			echo '</TR>';
		}

			echo '<tr>';
				echo '<td align="right" colspan="4"><b>Gesamt:&nbsp; </b></td>';
				echo '<td align="center">';
					echo $obpk_siege." : ".$obpk_niederlagen;
				echo '</td>';
/*
				echo '<td align="center">';
					echo $mipk_siege." : ".$mipk_niederlagen;
				echo '</td>';
*/
				echo '<td align="center">';
					echo $unpk_siege." : ".$unpk_niederlagen;
				echo '</td>';
				echo '<td align="center">';
					echo $gesamt_siege." : ".$gesamt_niederlagen;
				echo '</td>';
			echo '</tr>';
		echo '</TABLE>';			
                echo '<br><br>';
         	
         	// Doppel
         	// Spieler lesen
         	// 1. Doppel der Heimspiele lesen
         	$sqlstr  = "select heim1_id id1,heim2_id id2 from tt_spiele_saetze,tt_spiele where ";
         	$sqlstr .= "spiel_id=id and ";
         	$sqlstr .= "saison_id=".$saisonid." and ";
         	$sqlstr .= "heim_id=".$teamid." and ";
         	$sqlstr .= "heim1_id>0 and heim2_id>0";
         	$resultheim = getResult($db,$sqlstr);
         	
         	// 1. Doppel der Auswärtsspielespiele lesen
         	$sqlstr  = "select aus1_id id1,aus2_id id2 from tt_spiele_saetze,tt_spiele where ";
         	$sqlstr .= "spiel_id=id and ";
         	$sqlstr .= "saison_id=".$saisonid." and ";
         	$sqlstr .= "aus_id=".$teamid." and ";
         	$sqlstr .= "aus1_id>0 and aus2_id>0";
         	$resultaus = getResult($db,$sqlstr);
         	
         	// Selektieren der Spielerpaare
		$doppelspieler=array(array());
		$idx=0;
		foreach ($resultheim as $row) {
			// Prüfen, ob das Spielerpaar schon im array ist
			$is_in=0;
			foreach ($doppelspieler as $row1) {
				if ($row1["id1"]==$row["id1"] && $row1["id2"]==$row["id2"]) {
					$is_in=1;
					//break;
				}
				if ($row1["id1"]==$row["id2"] && $row1["id2"]==$row["id1"]) {
					$is_in=1;
					//break;
				}
			}

			if ($is_in == 0) {
				$doppelspieler[$idx]["id1"]=$row["id1"];
				$doppelspieler[$idx]["id2"]=$row["id2"];
				$idx++;
			}
		}

		foreach ($resultaus as $row) {
			// Prüfen, ob das Spielerpaar schon im array ist
			$is_in=0;
			foreach ($doppelspieler as $row1) {
				if ($row1["id1"]==$row["id1"] && $row1["id2"]==$row["id2"]) {
					$is_in=1;
					break;
				}
				if ($row1["id1"]==$row["id2"] && $row1["id2"]==$row["id1"]) {
					$is_in=1;
					break;
				}
			}

			if ($is_in == 0) {
				$doppelspieler[$idx]["id1"]=$row["id1"];
				$doppelspieler[$idx]["id2"]=$row["id2"];
				$idx++;
			}
		}
			

		// Ausgabe der Tabelle für die DoppelspielerStatistik
	        echo '<TABLE WIDTH="70%" BORDER="0">';
		        echo '<TR>';
			echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD" width="85%">';
			   	echo '<B>Doppel</B>';
			echo '</TD>';
			echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD" width="15%">';
				echo '<B>Gesamt</B>';
			echo '</TD>';
		
			$siege=0;
			$niederlagen=0;     		

			$ausgabe=array(array());
			$idx=0;
			foreach ($doppelspieler as $row) {
				$rs=getDoppel($row["id1"],$row["id2"],$saisonid,$teamid);
				$ausgabe[$idx]["bilanz"] = ($rs["siege"] - $rs["niederlagen"] + 10000 );  // +10000 Damit der Wert positiv ist
				$ausgabe[$idx]["siege"] = $rs["siege"];
				$ausgabe[$idx]["spieler1"] = getSpieler($row["id1"]);
				$ausgabe[$idx]["spieler2"] = getSpieler($row["id2"]);
				$ausgabe[$idx]["niederlagen"] = $rs["niederlagen"];
				$ausgabe[$idx]["id1"]=$row["id1"];
				$ausgabe[$idx]["id2"]=$row["id2"];
				$idx++;
			}
			//Absteigendes Sortieren des Array
			// Sortierung nach idx: Das Doppel mit der besten Bilanz steht oben
			array_multisort($ausgabe,SORT_DESC);

			$siege=0;
			$niederlagen=0;     		
	     		foreach ($ausgabe as $row) {
	     			echo '<tr>';
	     				echo '<td>';
	     					echo '<a href="javascript:info('.$row["id1"].','.$saisonid.');">';
	     						echo $row["spieler1"];
	     					echo '<a>';
	     					echo ' / ';
	     					echo '<a href="javascript:info('.$row["id2"].','.$saisonid.');">';
	     						echo $row["spieler2"];
	     					echo '<a>';
	     				echo '</td>';
					echo '<td align="center">';
							echo $row["siege"]." : ".$row["niederlagen"];
							$siege += $row["siege"];
							$niederlagen += $row["niederlagen"];
					echo '</td>';							
	     			echo '</tr>';
	     		}
			echo '<tr>';
				echo '<td align="right"">';
					echo '<b>Gesamt:</b>&nbsp;';
				echo '</td>';
				echo '<td align="center"">';
					echo $siege.': '.$niederlagen;
				echo '</td>';
			echo '<tr>';
		echo '</table>';		
         	echo '</center>';

?>