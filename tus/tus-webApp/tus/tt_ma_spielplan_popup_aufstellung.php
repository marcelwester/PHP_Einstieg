<?php
//// tt_ma_spielplan_popup_aufstellung.php
//// letzte Änderung : Volker, 02.10.2004
//// was : Erstellung
////

include "inc.php";
sitename("tt_ma_spielplan_popup_aufstellung.php",$_SESSION["groupid"]);
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e</TITLE>
<meta http-equiv="cache-control" content="no-cache">
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<SCRIPT LANGUAGE="JavaScript">
<!--
        function ext_spieler(personid)
        {
	      <?php
              		echo 'window.open("tt_extspieler_popup.php?action=start&id="+personid+"&PHPSESSID='.session_id().'","extperson","width=400, height=200, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=no, status=no");';
              ?>
        }

        function add_spieler(spielid,teamid)
        {
	      <?php
              		echo 'window.open("tt_addspieler_popup.php?action=start&teamid="+teamid+"&spielid="+spielid+"&PHPSESSID='.session_id().'","extperson","width=400, height=200, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=no, status=no");';
              ?>
        }

        function add_ext_spieler(spielid,teamid)
        {
	      <?php
              		echo 'window.open("tt_addextspieler_popup.php?action=start&teamid="+teamid+"&spielid="+spielid+"&PHPSESSID='.session_id().'","extperson","width=400, height=250, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=no, status=no");';
              ?>
        }

-->
</SCRIPT>
<?php
focus();
// TuS Mannschaft identifizieren aus (Gast oder Heim)
$spielid=$_REQUEST["spielid"];

echo $spielid;

$sqlstr="select saison_id,tus.id from tt_spiele sp,tt_tus_mannschaft tus where tus.id in (sp.aus_id,sp.heim_id) and sp.id=".$spielid;
$result=GetResult($db,$sqlstr);
$tus_teamid=$result["0"]["id"];
$saisonid=$result["0"]["saison_id"];
if (priv("spiele_edit") && priv_tt_team($tus_teamid) )
{

	$userid=$_SESSION["userid"];
	$result=GetResult($db,"select name from sys_users where userid=$userid");
	$username=$result["0"]["name"];

	switch ($_REQUEST["action"])
	{
	case 'start':
    		 // Spielzeit lesen
     		$sqlstr ="select liga,spielzeit from tt_saison where id=$saisonid";
     		$result = GetResult($db, $sqlstr);
     		$liga=$result[0]["liga"];
     		$spielzeit=$result[0]["spielzeit"];

     		echo '<TABLE WIDTH="100%" BORDER="0" >';
     		echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
     		echo '<B>Spiel - Aufstellung</B><BR>'.$liga.' - '.$spielzeit.'<BR> - '.$username.' - </TD></TR>';
     		echo '</TABLE>';

     		// Lesen der Begegnung
     		$sqlstr="select ma.id,ma.name from tt_mannschaft ma,tt_spiele sp where ";
     		$sqlstr.="heim_id=ma.id and sp.id=".$spielid;
     		$result=GetResult($db,$sqlstr);
		$heim=$result["0"]["name"];
		$heimid=$result["0"]["id"];
     		
     		$sqlstr="select ma.id,ma.name from tt_mannschaft ma,tt_spiele sp where ";
     		$sqlstr.="aus_id=ma.id and sp.id=".$spielid;
     		$result=GetResult($db,$sqlstr);
     		$aus=$result["0"]["name"];
  		$ausid=$result["0"]["id"];
     		echo '<center><h2><u>'.$heim.'</u>&nbsp; - &nbsp;<u>'.$aus.'</u></h2></center>';
		echo '';
		echo '<table width="100%" allign="center">';
		echo '<tr><td colspan="2" align="center" bgcolor="#DDDDDD"><b>Einzelspieler</b></td></tr></tr>';
	
	// Auslesen der einzelnen Spieler
		// Prüfen, ob die Aufstellung schon mal gespeichert wurde:
		$sqlstr="select aufstellung from tt_spiele where id=".$spielid;
		$result=GetResult($db,$sqlstr);
		if ($result["0"]["aufstellung"]==1) {
			$aufstellung=1;
			echo '<br>Aufstellung (gesetzt)';
		} else {
			$aufstellung=0;
			echo '<br>Aufstellung (neu)';
		}
		echo '&nbsp;'.$spielid;
		echo '<font size="0.75">';
			echo '<br>Mit Position 0 wird ein Spieler wieder aus der Aufstellung entfernt'; 
		echo '</font>';
		// Heimmannschaft
		   // Prüfen ob die heimmannschaft eine TuS Mannschaft ist
		$sqlstr="select id from tt_tus_mannschaft where id=".$heimid;
		$result=GetResult($db,$sqlstr);
		if (isset($result["0"]["id"])) {
			// Die Heimmannschaft ist eine TuS Mannschaft
			$heimmannschaft="tus";
			   // Kader der Heimmanschaft lesen
			//$sqlstr="select name,vorname,position from tt_zwtab_person_typ_tus_mannschaft zw..";
	
	
			if ($aufstellung=="0") {
                        	// Aufstellung als Vorschlag aus dem Kader lesen
                        	$sqlstr = 'select pe.name,pe.vorname,zw.person_id,zw.position
                                	   from tt_zwtab_person_typ_tus_mannschaft zw,tt_person pe
                                   	   where person_id > 0 and zw.person_id=pe.id
                                   	   and aktiv = 1 and tus_mannschaft_id = '.$heimid.' and persontyp_id = 2
                                   	   and saison_id='.$saisonid.' order by zw.position,pe.name';
		        } else {
                        	// Gespeicherte Aufstellung lesen
                        	$sqlstr = 'select name,vorname,tt_person.id person_id,position from tt_zw_tt_person_tt_spiele,tt_person where
                        		   tt_person.id=spieler_id and spiel_id='.$spielid.' and team_id = '.$heimid.' order by position';
                       }
       		       $resultheim=GetResult($db,$sqlstr);
                                   
			//print_r($resultheim);
		} else {
			$heimmannschaft="nontus";
			// Die Heimmannschaft ist KEINE TuS Mannschaft
			    // Kader der "externen" Mannschaft lesen
			if ($aufstellung=="0") {
				$sqlstr="select name,vorname,id person_id,0 position from tt_ext_spieler where team_id=".$heimid." order by name";
			} else {
				$sqlstr ="select name,vorname,position,id person_id from tt_ext_spieler,tt_zw_ext_spieler_tt_spiele where ";
				$sqlstr.="spieler_id=id and spiel_id=".$spielid." order by position,name";
			}	
			$resultheim=GetResult($db,$sqlstr);
			//print_r($resultheim);
		}
		
		
		//echo '<br>Gastmannschaft<br>';
		// Gastmannschaft
		   // Prüfen ob die Gastmannschaft eine TuS Mannschaft ist
		$sqlstr="select id from tt_tus_mannschaft where id=".$ausid;
		$result=GetResult($db,$sqlstr);
		if (isset($result["0"]["id"])) {
			// Die Gastmannschaft ist eine TuS Mannschaft
			$ausmannschaft="tus";
			   // Kader der Gastmanschaft lesen
			//$sqlstr="select name,vorname,position from tt_zwtab_person_typ_tus_mannschaft zw..";

			if ($aufstellung=="0") {
                        	// Aufstellung als Vorschlag aus dem Kader lesen
                        	$sqlstr = 'select pe.name,pe.vorname,zw.person_id,zw.position
                                	   from tt_zwtab_person_typ_tus_mannschaft zw,tt_person pe
                                   	   where person_id > 0 and zw.person_id=pe.id
                                   	   and aktiv = 1 and tus_mannschaft_id = '.$ausid.' and persontyp_id = 2
                                           and saison_id='.$saisonid.' order by zw.position,pe.name';
			} else {
                        	// Gespeicherte Aufstellung lesen
                        	$sqlstr = 'select name,vorname,tt_person.id person_id,position from tt_zw_tt_person_tt_spiele,tt_person where
                        		   tt_person.id=spieler_id and spiel_id='.$spielid.' and team_id = '.$ausid.' order by position';
			}
			
			$resultaus=GetResult($db,$sqlstr);
		} else {
			// Die Gastmannschaft ist KEINE TuS Mannschaft
			$ausmannschaft="nontus";
			    // Kader der "externen" Mannschaft lesen
			if ($aufstellung=="0") {
				$sqlstr="select name,vorname,id person_id,0 position from tt_ext_spieler where team_id=".$ausid." order by position,name";
			} else {
				$sqlstr ="select name,vorname,position,id person_id from tt_ext_spieler,tt_zw_ext_spieler_tt_spiele where ";
				$sqlstr.="spieler_id=id and spiel_id=".$spielid." order by position,name";
			}
			$resultaus=GetResult($db,$sqlstr);
			//print_r($resultaus);
		}
		
		
		echo '<tr>';
			echo '<td align="center" width="50%">';
				echo '<b>'.$heim.'</b>';
			echo '</td>';
			echo '<td align="center" width="50%">';
				echo '<b>'.$aus.'</b>';
			echo '</td>';
		echo '</tr>';
		
	   echo '<FORM method="post" action="tt_ma_spielplan_popup_aufstellung.php?action=save&spielid='.$spielid.'&PHPSESSID='.session_id().'">';		
		echo '<tr>';
		// Heimmannschaft
		echo '<td valign="top">';
			echo '<table width="100%">';
				$idx=0;
				foreach ($resultheim as $row) {
					echo '<tr>';
						echo '<td align="center">';
							echo '<input type="text" size="1" maxlength="2" value="'.$row["position"].'" name="heim['.$idx.'][position]" >';
							echo '<input type="hidden" value="'.$row["person_id"].'" name="heim['.$idx.'][person_id]" >';
						echo '</td>';
						echo '<td align="center">';
							if ($heimmannschaft=="nontus") {
				                         	echo '&nbsp;&nbsp;';
				                                echo '<a href="javascript:ext_spieler('.$row["person_id"].');">';
 				                                       //echo '<IMG SRC="images/edit.jpg" BORDER="0" ALT="Bearbeiten">';
 				                                       echo $row["name"].', '.$row["vorname"];
                	        			        echo '</a>';
                	        			} else {
                	        				echo $row["name"].', '.$row["vorname"];	
                	        			}
						echo '</td>';
					echo '</tr>';
					$idx++;
				}

				if ($heimmannschaft=="tus") {
					echo '<tr>';
						echo '<td colspan="2" Align="center">';
							echo '<b><a href="javascript:add_spieler('.$spielid.','.$heimid.');">Zusätzlicher Spieler</a></b>';
						echo '</td>';
					echo '</tr>';
				}	
				
				if ($heimmannschaft=="nontus") {
					echo '<tr>';
						echo '<td colspan="2" Align="center">';
							echo '<b><a href="javascript:add_ext_spieler('.$spielid.','.$heimid.');">Zusätzlicher Spieler</a></b>';
						echo '</td>';
					echo '</tr>';
				}	


			echo '</table>';
		echo '</td>';

		// Gastmannschaft
		echo '<td valign="top">';
			echo '<table width="100%">';
				$idx=0;
				foreach ($resultaus as $row) {
					echo '<tr>';
						echo '<td align="center">';
							echo '<input type="text" size="1" maxlength="2" value="'.$row["position"].'" name="aus['.$idx.'][position]" >';
							echo '<input type="hidden" value="'.$row["person_id"].'" name="aus['.$idx.'][person_id]" >';
						echo '</td>';
						echo '<td align="center">';
							if ($ausmannschaft=="nontus") {
				                         	echo '&nbsp;&nbsp;';
				                                echo '<a href="javascript:ext_spieler('.$row["person_id"].');">';
 				                                       //echo '<IMG SRC="images/edit.jpg" BORDER="0" ALT="Bearbeiten">';
 				                                       echo $row["name"].', '.$row["vorname"];
                	        			        echo '</a>';
                	        			} else {
                	        				echo $row["name"].', '.$row["vorname"];	
                	        			}
						echo '</td>';
					echo '</tr>';
					$idx++;
				}
				if ($ausmannschaft=="tus") {
					echo '<tr>';
						echo '<td colspan="2" Align="center">';
							echo '<b><a href="javascript:add_spieler('.$spielid.','.$ausid.');">Zusätzlicher Spieler</a></b>';
						echo '</td>';
					echo '</tr>';
				}	
				if ($ausmannschaft=="nontus") {
					echo '<tr>';
						echo '<td colspan="2" Align="center">';
							echo '<b><a href="javascript:add_ext_spieler('.$spielid.','.$ausid.');">Zusätzlicher Spieler</a></b>';
						echo '</td>';
					echo '</tr>';
				}	
			
			echo '</table>';
		echo '</td>';
		
		echo '</tr>';
		echo '<tr>';
			echo '<td align="center" colspan="2">';
				echo '<INPUT TYPE="hidden" NAME="heimid" VALUE="'.$heimid.'">';
				echo '<INPUT TYPE="hidden" NAME="ausid" VALUE="'.$ausid.'">';
				echo '<INPUT TYPE="hidden" NAME="heimmannschaft" VALUE="'.$heimmannschaft.'">';
				echo '<INPUT TYPE="hidden" NAME="ausmannschaft" VALUE="'.$ausmannschaft.'">';					
	            		echo '<INPUT TYPE="button" VALUE="Beenden" onClick="opener.location.reload();window.close();">';
            			echo '&nbsp;&nbsp;';
            			echo '<INPUT type=submit value="Speichern">';
			echo '</td>';
		echo '</tr>';
		echo '</table>';
	   echo '</form>';
	
	break;

	case 'save':
	  //$spielid;
	  $error=0;
	  echo '<br>Daten werden gespeichert bitte warten ...<br>';

	  // Speichern der Aufstellungen
	  $heimmannschaft=$_POST["heimmannschaft"];
	  $ausmannschaft=$_POST["ausmannschaft"];
	  $heimid=$_POST["heimid"];
	  $ausid=$_POST["ausid"];
  	// Arrays:
	  $heim=$_POST["heim"];
      	  $aus=$_POST["aus"];
      	  
        // Anfang: Prüfen, ob ein Spieler gelöscht werden soll, der schon in der tt_sspiele_saetze enthalten ist
          // Heimmannschaft
          $komma=""; $del="";
          foreach ($heim as $row) {
          	if ($row["position"]==0) {
          		$del .= $komma.$row["person_id"];
          		$komma=",";
          	}
          }
          if ($komma==",") {
           	$sqlstr="select spiel_nr from tt_spiele_saetze where (heim1_id in (".$del.") or heim2_id in (".$del.")) and spiel_id=".$spielid;
          	$result=GetResult($db,$sqlstr);
          	if (isset($result)) {
          		echo "<br><b>Heimmannschaft:<br>Wenn Spieler aus der Aufstellung gelöscht werden sollen, dann dürfen sie in keinem Satz des Spiels eingetragen sein<br> ";
			$error=1;
	  	}
	  }
          // Ausmannschaft
 	  $komma=""; $del="";
          foreach ($aus as $row) {
          	if ($row["position"]==0) {
          		$del .= $komma.$row["person_id"];
          		$komma=",";
          	}
          }
          if ($komma==",") {
          	$sqlstr="select spiel_nr from tt_spiele_saetze where (aus1_id in (".$del.") or aus2_id in (".$del.")) and spiel_id=".$spielid;
          	$result=GetResult($db,$sqlstr);
          	if (isset($result)) {
          		echo "<br><b>Auswärtsmannschaft:<br>Wenn Spieler aus der Aufstellung gelöscht werden sollen, dann dürfen sie in keinem Satz des Spiels eingetragen sein<br> ";
			$error=1;
	  	}
	  }
	  unset($result);
        // Ende: Prüfen, ob ein Spieler gelöscht werden soll, der schon in der tt_sspiele_saetze enthalten ist

	  if ($error==0) {
	  	
	  	// Aufstellung der Heimmannschaft speichern 
	  	if ($heimmannschaft=="tus") {
	  		// Löschen der alten Aufstellung
	  		$sqlstr  = "delete from tt_zw_tt_person_tt_spiele where ";
	  		$sqlstr .= "team_id=".$_POST["heimid"]." and ";
	  		$sqlstr .= "spiel_id=".$spielid;
	  		$result=doSQL($db,$sqlstr);
	  	
	  		foreach ($heim as $row) {
	  			if (ereg('([0-9]{1,2})',$row["position"])) {
	  			      if ($row["position"]!="0") {
	  				$sqlstr  = "insert into tt_zw_tt_person_tt_spiele (spiel_id,spieler_id,position,team_id) values (";
	  				$sqlstr .= $spielid.",";
	  				$sqlstr .= $row["person_id"].",";
	  				$sqlstr .= $row["position"].",";
	  				$sqlstr .= $heimid.")";
	  				$result=doSQL($db,$sqlstr);
	  			      }
	  			}
	  		}
	  	} else {
	  		// Löschen der alten Aufstellung
	  		$sqlstr  = "delete from tt_zw_ext_spieler_tt_spiele where ";
	  		$sqlstr .= "team_id=".$_POST["heimid"]." and ";
	  		$sqlstr .= "spiel_id=".$spielid;
	  		$result=doSQL($db,$sqlstr);
	  		foreach ($heim as $row) {
	  			if (ereg('([0-9]{1,2})',$row["position"])) {
	  			      if ($row["position"]!="0") {
	  				$sqlstr  = "insert into tt_zw_ext_spieler_tt_spiele (spiel_id,spieler_id,position,team_id) values (";
	  				$sqlstr .= $spielid.",";
	  				$sqlstr .= $row["person_id"].",";
	  				$sqlstr .= $row["position"].",";
	  				$sqlstr .= $heimid.")";
	  				$result=doSQL($db,$sqlstr);
	  			      }
	  				
	  			}
	  		}
	  	}
	  }


	  if ($error==0) {
	  	// Aufstellung der Gastmannschaft speichern 
	  	if ($ausmannschaft=="tus") {
	  		// Löschen der alten Aufstellung
	  		$sqlstr  = "delete from tt_zw_tt_person_tt_spiele where ";
	  		$sqlstr .= "team_id=".$_POST["ausid"]." and ";
	  		$sqlstr .= "spiel_id=".$spielid;
	  		$result=doSQL($db,$sqlstr);
	  	
	  		foreach ($aus as $row) {
	  			if (ereg('([0-9]{1,2})',$row["position"])) {
	  			      if ($row["position"]!="0") {
	  				$sqlstr  = "insert into tt_zw_tt_person_tt_spiele (spiel_id,spieler_id,position,team_id) values (";
	  				$sqlstr .= $spielid.",";
	  				$sqlstr .= $row["person_id"].",";
	  				$sqlstr .= $row["position"].",";
	  				$sqlstr .= $ausid.")";
	  				$result=doSQL($db,$sqlstr);
	  			      }
	  			}
	  		}
	  	} else {
	  		// Löschen der alten Aufstellung
	  		$sqlstr  = "delete from tt_zw_ext_spieler_tt_spiele where ";
	  		$sqlstr .= "team_id=".$_POST["ausid"]." and ";
	  		$sqlstr .= "spiel_id=".$spielid;
	  		$result=doSQL($db,$sqlstr);
	  		foreach ($aus as $row) {
	  			if (ereg('([0-9]{1,2})',$row["position"])) {
	  			      if ($row["position"]!="0") {
	  				$sqlstr  = "insert into tt_zw_ext_spieler_tt_spiele (spiel_id,spieler_id,position,team_id) values (";
	  				$sqlstr .= $spielid.",";
	  				$sqlstr .= $row["person_id"].",";
	  				$sqlstr .= $row["position"].",";
	  				$sqlstr .= $ausid.")";
	  				$result=doSQL($db,$sqlstr);
	  			      }
	  			}
	  		}
	  	}
	  }


	  // Setzen der Aufstellungs Flags in tt_spiele
	  if ($error==0) {
	  	$sqlstr="update tt_spiele set aufstellung=1 where id=".$spielid;
	  	$result=doSQL($db,$sqlstr);
	  }

	  echo '<br>';	
	  if ($error == 0)
          {
          	mlog("Tischtennis: Es wurde die Aufstellung zu einem Spiel gespeichert: ".$spielid);
          	echo 'Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript:window.history.back();">Hier klicken, um zurückzukehren</A>';
           	echo '<SCRIPT TYPE="text/javascript">';
                	//echo 'opener.location.reload();';
                	echo 'setTimeout("window.history.back()",500);';
           	echo '</SCRIPT>';
          }

          else
           	 echo 'Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:window.history.back();">Hier klicken, um zurückzukehren</A>';



	break;

	}

} else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';

closeConnect($db);
?>
</BODY>
</HTML>