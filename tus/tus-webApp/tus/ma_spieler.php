<?php

////
//// ma_spieler.php
//// letzte Änderung: Volker 19.03.2005
//// Kader editieren: Übernahme des Kaders von einer anderen Saison
////
//// letzte Änderung: Volker 27.02.2004
//// Kader editieren: Validierung of Spieler schon in fb_tore eingetragen ist.
////
//// letzte Änderung: Volker 16.02.2004
//// Darstellung Vor- und Nachname
////

include "inc.php";
sitename("ma_spieler.php",$_SESSION["groupid"]);
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php
 $saisonid = $_REQUEST["saisonid"];
 $teamid = $_REQUEST["teamid"];

if (priv("kader") && priv_team($teamid))
{

 // Saisoninfo lesen
 $sqlstr ="select liga,spielzeit,startdatum from fb_saison where id=$saisonid";
 $result = GetResult($db, $sqlstr);
 $liga=$result[0]["liga"];
 $spielzeit=$result[0]["spielzeit"];
 $startjahr=date("Y", strtotime($result["0"]["startdatum"]));

 
 // Mannschaft lesen
 $sqlstr = "select name from fb_tus_mannschaft where id=$teamid";
 $result = GetResult($db, $sqlstr);
 $mannschaft=$result[0]["name"];
 
 // Mannschaft lesen
 $sqlstr = "select kat from fb_mannschaft where id=$teamid";
 $result = GetResult($db, $sqlstr);
 $kat=$result[0]["kat"];
 
 // Persontypen lesen
 $sqlstr = "select id,name from fb_person_typ";
 $persontyp = GetResult($db, $sqlstr);


switch ($_REQUEST["action"])
{
        case 'start':
         // Persontypen lesen
          $sqlstr = "select id,name from fb_person_typ order by id";
          $persontyp = GetResult($db, $sqlstr);

	// Mannschaftskat laden
          $sqlstr = "select kategorie,name from fb_mannschaft_kat order by kategorie";
          $mannschaft_kat = GetResult($db, $sqlstr);
		

          echo '<TABLE WIDTH="100%" BORDER="0" >';
          echo '<TD COLSPAN="3" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
          echo '<B>Kader editieren '.$teamname.'</B><BR>'.$mannschaft.'<BR>'.$liga.' - '.$spielzeit.'<BR>';
          echo '</td>';
          echo '</TABLE>';
          echo '<br>';
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
	     build_select($mannschaft_kat,"name","kategorie","kategorie","1",1,$kat); 
	     echo '</td></tr>'; echo '</table>';     
             
             echo '<br>';
             echo '<Table WIDTH="100%" BORDER="1">';
             echo '<td ALIGN="CENTER">';
             echo '<INPUT type=hidden name="action" value="edit">';
             echo '<INPUT type=hidden name="teamid" value="'.$teamid.'">';
             echo '<INPUT type=hidden name="saisonid" value="'.$saisonid.'">';
             echo '<INPUT type=submit value="Daten editieren"></TR>';
             echo '</td></Table>';
          echo ' </FORM>';

			 echo '<br><br><br>';
			 echo '<center><h3>Kader übernehmen von:</h3></center>';
			 echo '<br>';
			 $sqlstr="select id,spielzeit,liga from fb_saison where kat='".$kat."' and id<>'.$saisonid.' order by startdatum desc";
			 $result=GetResult($db,$sqlstr);
			 $saison=array(); $i="0";
			 
			 foreach ($result as $row) {
			 	$saison[$i]["name"]= $row["spielzeit"]." - ".$row["liga"];
			 	$saison[$i]["id"] = $row["id"];
				$i++;			 	
			 }

			$sqlstr  = "select count(*) anz from fb_zwtab_person_typ_tus_mannschaft where saison_id=".$saisonid." and tus_mannschaft_id=".$teamid;
			$result=GetResult($db,$sqlstr);
			// Kader darf nur übernommen werden, wenn er vorher noch nicht gesetzt war.
			if ($result["0"]["anz"] == "0") {
	          echo '<FORM method="post" action="'.$PHP_SELF.'">';
				    echo '<center>';
				    	build_select($saison,"name","id","kader_from"); 
						echo '<br><br>';
		            echo '<INPUT type=hidden name="action" value="kader_uebernehmen">';
	              	echo '<INPUT type=hidden name="teamid" value="'.$teamid.'">';
	               echo '<INPUT type=hidden name="saisonid" value="'.$saisonid.'">';
	               echo '<INPUT type=submit value="Kader übernehmen"></TR>';
				    echo '</center>';
	          echo ' </FORM>';
			} else {
				 echo '<center>Kader übernehmen nicht möglich, da schon Kaderdaten vorhanden sind !</center>';
			}

        break;

		  case 'kader_uebernehmen':

		  		$sqlstr  = "select person_id,tus_mannschaft_id,persontyp_id,aktiv from fb_zwtab_person_typ_tus_mannschaft ";
		  		$sqlstr .= "where saison_id=".$_POST["kader_from"];
		  		$result=GetResult($db,$sqlstr);

				$result1["code"]="0";
				foreach ($result as $row) {
					if ($result1["code"]=="0") {
						$sqlstr  = "insert into fb_zwtab_person_typ_tus_mannschaft (person_id,tus_mannschaft_id,persontyp_id,saison_id,aktiv,archive_image) values (";
						$sqlstr .= $row["person_id"].",";
						$sqlstr .= $row["tus_mannschaft_id"].",";
						$sqlstr .= $row["persontyp_id"].",";
						$sqlstr .= $saisonid.",";
						$sqlstr .= "1,0)";
						$result1=doSQL($db,$sqlstr);
					}
				}
	         if ($result["code"] == 0)
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
		  
			

        case 'edit':
             // Personendaten der Mannschaft editieren

             $persontypid = $_REQUEST["persontypid"];
	    		 $kategorie = $_REQUEST["kategorie"];
        
             // Persontypen lesen
             $sqlstr = "select name from fb_person_typ where id=$persontypid";
             $result = GetResult($db, $sqlstr);
             $persontypname=$result["0"]["name"];

	     // Altersklasse laden
	     $sqlstr = "select min_alter,max_alter from fb_mannschaft_kat where kategorie='$kategorie'";
	     $result = GetResult($db,$sqlstr);
	     $min_alter=$result["0"]["min_alter"];
	     $max_alter=$result["0"]["max_alter"];

	     echo '<TABLE WIDTH="100%" BORDER="0" >';
             echo '<TD COLSPAN="3" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
             echo '<B>'.$persontypname.' '.$teamname.'</B><BR>'.$mannschaft.'<BR>'.$liga.' - '.$spielzeit.'<BR>';
             echo '</td>';
             echo '</TABLE>';
             echo '<br>';

       
             // Personen lesen, die schon zur Mannschaft gehören
             // Abfrage ist anhängig vom Personentyp
             $sqlstr = "select zw.person_id
                       from fb_zwtab_person_typ_tus_mannschaft zw,fb_person pe
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


             // Alle Spieler lesen
             $sqlstr = "select id,name,vorname,geburtsdatum from fb_person where id > 0 order by name";
             $person = GetResult($db,$sqlstr);


             // Spieler lesen, die schon in der Torschützenverwaltung stehen
             $sqlstr = "select distinct(fb_tore.spieler_id) from fb_tore,fb_spiele
                        where spiel_id=fb_spiele.id and  fb_spiele.saison_id=$saisonid";
             
               
             $torschuetze = GetResult($db,$sqlstr);
             $torschuetzeid = array();
             
             if (isset($torschuetze))
              foreach ($torschuetze as $row)
              {
              	array_push($torschuetzeid,$row["spieler_id"]);
                //echo "<br>".$row["spieler_id"];
              }

  
             echo '<FORM method="post" action="'.$PHP_SELF.'">';

             echo '<Table WIDTH="100%" BORDER="1">';
             echo '<tr><td ALIGN="CENTER"><b>'.$persontypname.'</b></td><td ALIGN="CENTER"><b>Status</b></td><td ALIGN="CENTER"><b>Hinzufügen</b></td><td ALIGN="CENTER"><b>Löschen</b></td></tr>';
             
             $i=0;
             $spaltenzahl=1;
             $sp=1;
             foreach ($person as $row)
             {
                $i ++;
 		$geburtsjahr = date("Y", strtotime($row["geburtsdatum"]));
		$alter = ($startjahr - $geburtsjahr);

 	if ((($alter >= $min_alter) && ($alter <= $max_alter)) || $kategorie =='ALLE') {
               if ($sp==1) echo "<tr>";
               if (in_array($row["id"],$teamspielerid)) {
                 // Spieler ist  zur Mannschaft zugewiesen
                    echo '<td><b>'.$row["name"].', '.$row["vorname"].'</b></td>';
                    echo '<td ALIGN="CENTER"><INPUT TYPE="CHECKBOX" NAME="dummy" checked="checked" DISABLED/></td>';
                    echo '<td ALIGN="CENTER"><INPUT TYPE="CHECKBOX" NAME="dummy" DISABLED /></td>';
                    // Spieler aus dem Kader loeschen
 /*
                    if (in_array($row["id"],$torschuetzeid))
                    {
                      echo '<td ALIGN="CENTER"><INPUT TYPE="CHECKBOX" NAME="dummy" DISABLED /></td>';
                    }   
                    else 
*/	
				    {
				      echo '<td ALIGN="CENTER"><INPUT TYPE="CHECKBOX" NAME="spieleriddel'.$i.'" value="'.$row["id"].'"  /></td>';
		    		}
                } else {
                 // Spieler ist nicht zur Mannschaft zugewiesen
                 echo '<td>'.$row["name"].', '.$row["vorname"].'</td>';
                 echo '<td ALIGN="CENTER"><INPUT TYPE="CHECKBOX" NAME="dummy" DISABLED/></td>';
                 echo '<td ALIGN="CENTER"><INPUT TYPE="CHECKBOX" NAME="spieleridadd'.$i.'" value="'.$row["id"].'" /></td>';
 		 echo '<td ALIGN="CENTER"><INPUT TYPE="CHECKBOX" NAME="dummy" DISABLED /></td>';
 		}
               if ($sp==$spaltenzahl) {
               	echo "</tr>";
                $sp=0;
               }
               $sp++;
 	}
             }

             echo '<INPUT type=hidden name="persontypid" value="'.$persontypid.'">';
             echo '<INPUT type=hidden name="teamid" value="'.$teamid.'">';
             echo '<INPUT type=hidden name="saisonid" value="'.$saisonid.'">';
             echo '<INPUT type=hidden name="count" value="'.$i.'">';
             echo '<INPUT type=hidden name="action" value="save">';
            echo '<TR><TD COLSPAN="5" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
            echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
            echo '&nbsp;&nbsp;<INPUT type=submit value="Speichern"></TR>';
            echo '</TABLE>';
          echo ' </FORM>';
         break;

        case 'save':
         // Anzahl der Datensätze
         $count = $_REQUEST["count"];
         $teamid = $_REQUEST["teamid"];
         $saisonid = $_REQUEST["saisonid"];
         $persontypid = $_REQUEST["persontypid"];

 
 	        
         for ($i = 1 ; $i <= $count; $i++)
         {
            $tmp="spielerid".$i;
	    // Spieler aus dem Kader Löschen
            if (isset($_REQUEST["spieleriddel".$i])) {
                $sqlstr="delete from fb_zwtab_person_typ_tus_mannschaft where person_id=".$_REQUEST["spieleriddel".$i]." and persontyp_id=$persontypid and saison_id=".$saisonid;
                $result = doSQL($db,$sqlstr);
                if ($result["code"] != 0) break;
            }
            // Spieler dem Kader hinzufügen
            if (isset($_REQUEST["spieleridadd".$i])) {
            	$sqlstr = "insert into fb_zwtab_person_typ_tus_mannschaft
                        (person_id,tus_mannschaft_id,persontyp_id,aktiv,saison_id) values
                        (".$_REQUEST["spieleridadd".$i].",$teamid,$persontypid,1,$saisonid)";
                $result = doSQL($db,$sqlstr);
                if ($result["code"] != 0) break;
            }
         }
		
         if ($result["code"] == 0)
         {
           mlog("Fussball: Kader(".$persontypid.") einer Mannschaft(".$teamid.") wurde in Saison ".$saisonid." gespeichert"); 
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