<?php
               // Statistiken Spielsystem 4 (3er Spielsystem)
                   
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
			   	echo '<B>Gesamt</B>';
			   echo '</TD>';
		   echo '</TR>';	

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
					$rs = getEinzel($row["person_id"],$saisonid,$teamid,"1,2,3,4,5,6,7,8,9");
					if ($rs["siege"]!="0" || $rs["niederlagen"]!="0") {
						echo $rs["siege"]." : ".$rs["niederlagen"];
						$siege += $rs["siege"];
						$niederlagen += $rs["niederlagen"];
						//$obpk_siege += $rs["siege"];
						//$obpk_niederlagen += $rs["niederlagen"];
					} else
						echo "&nbsp;";

					$gesamt_siege += $siege;
					$gesamt_niederlagen += $niederlagen;

				echo '</TD>';
		}
		
			echo '<tr>';
				echo '<td align="right" colspan="3"><b>Gesamt:&nbsp; </b></td>';
				echo '<td align="center">';
					echo $gesamt_siege." : ".$gesamt_niederlagen;
				echo '</td>';
			echo '</tr>';
		echo '</TABLE>';			
                echo '<br><br>';
  
         	echo '</center>';

?>