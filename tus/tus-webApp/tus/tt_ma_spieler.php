<?php

////
//// tt_ma_spieler.php
//// letzte Änderung: Volker 29.09.2004
//// Grund: Neu erstellt
////
////

include "inc.php";
sitename("tt_ma_spieler.php",$_SESSION["groupid"]);
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php
if (priv("kader") && priv_tt_team($teamid))
{
 $saisonid = $_REQUEST["saisonid"];
 $teamid = $_REQUEST["teamid"];

 // Saisoninfo lesen
 $sqlstr ="select liga,spielzeit,startdatum from tt_saison where id=$saisonid";
 $result = GetResult($db, $sqlstr);
 $liga=$result[0]["liga"];
 $spielzeit=$result[0]["spielzeit"];
 $startjahr=date("Y", strtotime($result["0"]["startdatum"]));

 
 // Mannschaft lesen
 $sqlstr = "select name from tt_tus_mannschaft where id=$teamid";
 $result = GetResult($db, $sqlstr);
 $mannschaft=$result[0]["name"];
 
 // Mannschaft lesen
 $sqlstr = "select kat from tt_mannschaft where id=$teamid";
 $result = GetResult($db, $sqlstr);
 $kat=$result[0]["kat"];
 
 // Persontypen lesen
 $sqlstr = "select id,name from tt_person_typ";
 $persontyp = GetResult($db, $sqlstr);

 echo '<TABLE WIDTH="100%" BORDER="0" >';
 echo '<TD COLSPAN="3" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
 echo '<B>Kader editieren '.$teamname.'</B><BR>'.$mannschaft.'<BR>'.$liga.' - '.$spielzeit.'<BR>';
 echo '</td>';
 echo '</TABLE>';
 echo '<br>';


switch ($_REQUEST["action"])
{
        case 'start':
         // Persontypen lesen
          $sqlstr = "select id,name from tt_person_typ order by id";
          $persontyp = GetResult($db, $sqlstr);

	// Mannschaftskat laden
          $sqlstr = "select kategorie,name from tt_mannschaft_kat order by kategorie";
          $mannschaft_kat = GetResult($db, $sqlstr);
		

          echo '<FORM method="post" action="'.$PHP_SELF.'">';
             echo '<Table WIDTH="100%" BORDER="1">';
             echo '<tr><td ALIGN="CENTER">';
             	echo '<b>Persontypauswahl</b>';
             echo '</td><td>';
             build_select($persontyp,"name","id","persontypid");
	     echo '</td></tr>';
	     
	     echo '<tr><td ALIGN="CENTER">'; echo '<b>Filter Altersklasse</b>'; 
	     echo '</td><td>'; 
	     $mannschaft_kat["-1"]["name"]="Kein Filter";
	     $mannschaft_kat["-1"]["kategorie"]="ALLE";
	     build_select($mannschaft_kat,"name","kategorie","kategorie","1",1,$kat); echo 
	     '</td></tr>'; echo '</table>';     
             
             echo '<br>';
             echo '<Table WIDTH="100%" BORDER="1">';
             echo '<td ALIGN="CENTER">';
             echo '<INPUT type=hidden name="action" value="edit">';
             echo '<INPUT type=hidden name="teamid" value="'.$teamid.'">';
             echo '<INPUT type=hidden name="saisonid" value="'.$saisonid.'">';
             echo '<INPUT type=submit value="Daten editieren"></TR>';
             echo '</td></Table>';
          echo ' </FORM>';

        break;


        case 'edit':
             // Personendaten der Mannschaft editieren

             $persontypid = $_REQUEST["persontypid"];
	     		 $kategorie = $_REQUEST["kategorie"];
        
             // Persontypen lesen
             $sqlstr = "select name from tt_person_typ where id=$persontypid";
             $result = GetResult($db, $sqlstr);
             $persontypname=$result["0"]["name"];

	     // Altersklasse laden
	     $sqlstr = "select min_alter,max_alter from tt_mannschaft_kat where kategorie='$kategorie'";
	     $result = GetResult($db,$sqlstr);
	     $min_alter=$result["0"]["min_alter"];
	     $max_alter=$result["0"]["max_alter"];

	     echo '<center><h2>'.$persontypname.'</h2></center>';
            
       
             // Personen lesen, die schon zur Mannschaft gehören
             // Abfrage ist anhängig vom Personentyp
             $sqlstr = "select zw.person_id
                       from tt_zwtab_person_typ_tus_mannschaft zw,tt_person pe
                       where saison_id=$saisonid and aktiv=1 and persontyp_id=$persontypid and tus_mannschaft_id=$teamid
                       and pe.id=zw.person_id
                       order by name";
             $teamspieler = GetResult($db,$sqlstr);

             $teamspielerid=array();
              if (isset ($teamspieler))
                foreach ($teamspieler as $row)
                {
                  array_push($teamspielerid,$row["person_id"]);
                }
	    		 unset($teamspieler); 	

             // Alle Spieler lesen
             // Hier muss noch zwischen Damen und Herren unterschieden werden !!!!!
             if ($kategorie=="D") $geschlecht=" and geschlecht='w' ";
 				 // Im Herren Bereich dürfen auch Damen mitspielen
             if ($kategorie=="H") $geschlecht=" ";
             $sqlstr = "select id,name,vorname,geburtsdatum from tt_person where id > 0 ".$geschlecht." order by name";

             $person = GetResult($db,$sqlstr);

             echo '<FORM method="post" action="'.$PHP_SELF.'">';

             echo '<Table WIDTH="100%" BORDER="1">';
             echo '<tr><td ALIGN="CENTER"><b>'.$persontypname.'</b></td><td ALIGN="CENTER"><b>Status</b></td><td ALIGN="CENTER"><b>Pos</b></td></tr>';
             
             $i=0;
             foreach ($person as $row)
             {
             $geburtsjahr = date("Y", strtotime($row["geburtsdatum"]));
				 $alter = ($startjahr - $geburtsjahr);
				 

		// Spieler werden nur angezeigt, wenn der Filter passt
	 	if ((($alter >= $min_alter) && ($alter <= $max_alter)) || $kategorie =='ALLE') {
			// Alle Spieler IDs merken, damit auch nur die vom Filter 
			// angezeigten Spieler bei save zunächst gelöscht werden
			echo '<INPUT type=hidden name="filter['.$i.']" value="'.$row["id"].'">';
			// Pruefen ob der Spieler bereits zur Mannschaft gehört:
			if (in_array($row["id"],$teamspielerid)) {
				$is_spieler="1";
			} else {
				unset($is_spieler);
			}
			
			echo '<tr>';
				echo '<td align="center">';
					if (isset($is_spieler)) {
						echo '<b>';	
						echo $row["name"].', '.$row["vorname"];
						echo '</b>';
					} else {
						echo $row["name"].', '.$row["vorname"];
					}
				echo '</td>';

				echo '<td align="center">';
					if (isset($is_spieler)) {
						echo '<INPUT TYPE="CHECKBOX" NAME="kader['.$i.'][person_id]" value="'.$row["id"].'" checked />';
					} else {
						echo '<INPUT TYPE="CHECKBOX" NAME="kader['.$i.'][person_id]" value="'.$row["id"].'"  />';
					}
				echo '</td>';

				echo '<td align="center">';
					if (isset($is_spieler)) {
						$sqlstr ="select position from tt_zwtab_person_typ_tus_mannschaft where ";
						$sqlstr.="person_id=".$row["id"]." and ";
						$sqlstr.="persontyp_id=".$persontypid." and ";
						$sqlstr.="saison_id=".$saisonid." and ";
						$sqlstr.="tus_mannschaft_id=".$teamid." and ";
						$sqlstr.="aktiv=1";
						$position=GetResult($db,$sqlstr);
					} else {
						$position["0"]["position"]="";
					}
					echo '<INPUT TYPE="text" size="2" NAME="kader['.$i.'][position]" value="'.$position["0"]["position"].'" />';
				echo '</td>';
			echo '</tr>';
			$i++;
		}
	     }


             echo '<INPUT type=hidden name="persontypid" value="'.$persontypid.'">';
             echo '<INPUT type=hidden name="teamid" value="'.$teamid.'">';
             echo '<INPUT type=hidden name="saisonid" value="'.$saisonid.'">';
             //echo '<INPUT type=hidden name="count" value="'.$i.'">';
             echo '<INPUT type=hidden name="action" value="save">';
            echo '<TR><TD COLSPAN="5" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
            echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
            echo '&nbsp;&nbsp;<INPUT type=submit value="Speichern"></TR>';
            echo '</TABLE>';
          echo ' </FORM>';
         break;

        case 'save':
	      // Anzahl der Datensätze
             // $count = $_REQUEST["count"];
            $teamid = $_REQUEST["teamid"];
            $saisonid = $_REQUEST["saisonid"];
            $persontypid = $_REQUEST["persontypid"];
	    $kader=$_POST["kader"];
	    $filter=$_POST["filter"];
	    
	    // Eingaben prüfen:	
	    $parse_error="0";
	    foreach ($kader as $row) {
			if (isset($row["person_id"])) {
				if ($row["position"]!='') {
					if (!ereg('([0-9]{1,2})',$row["position"])) {
						echo '<br><b>Falsches Format in Position: </b>'.$row["position"];
						$parse_error="1";
					}
				}
			}
 	    }


	    if ($parse_error==0) {
	    	// Kader der Saison löschen (alle Spieler, die dem Filter entsprachen)
            	foreach	($filter as $person_id) {
            		$sqlstr  ="delete from tt_zwtab_person_typ_tus_mannschaft where ";
            		$sqlstr .="persontyp_id=".$persontypid." and ";
            		$sqlstr .="saison_id=".$saisonid." and ";
	        	$sqlstr .="tus_mannschaft_id=".$teamid." and ";
	        	$sqlstr .="person_id=".$person_id;
        		$result=doSQL($db,$sqlstr);
        	}

	    	foreach ($kader as $row) {
			if (isset($row["person_id"])) {
				if ($row["position"]=='') $row["position"]=0;
				$sqlstr ="insert into tt_zwtab_person_typ_tus_mannschaft (person_id,tus_mannschaft_id,persontyp_id,saison_id,position,aktiv) ";
				$sqlstr.="values ( ";
				$sqlstr.=$row["person_id"].",";
				$sqlstr.=$teamid.",";
				$sqlstr.=$persontypid.",";
				$sqlstr.=$saisonid.",";
				$sqlstr.=$row["position"].",";
				$sqlstr.="1)";
		        	$result=doSQL($db,$sqlstr);
			}
 	    	}
 	   }
	
	 if ($result["code"] == 0 && $parse_error == 0 )
         {
           	echo 'Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A>';
           	echo '<SCRIPT TYPE="text/javascript">';
	                echo 'opener.location.reload();';
        	        echo 'setTimeout("window.close()",1000);';
           	echo '</SCRIPT>';
         }

      else
         echo 'Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:window.history.back();">Hier klicken, um zurückzukehren</A>';
        

    break;

    case 'info';
     echo "Spielerinfo";
    break;
}
closeConnect($db);
}
else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
?>
</BODY>
</HTML>