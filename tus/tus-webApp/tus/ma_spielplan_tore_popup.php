<?php
//// ma_spielplan_tore_popup.php
////
//// letzte Änderung : Volker, 11.02.2004
//// was : Es werden nur Spieler (persontyp_id in (2,4) in der Dropdownlistbox angezeigt
////
//// letzte Änderung: Volker 23.02.2005
//// Möglichkeit, dass beie Mannschaften TuS Mannschaften sind eingebaut ($o2id)
////
//// letzte Änderung: Volker 26.02.2004
//// Löschen aller Einträge eingebaut
////
//// letzte Änderung: Volker 16.02.2004
//// Darstellung Vor- und Nachname
////
//// letzte Änderung : Volker, 11.02.2004
//// was : Es werden nur Spieler (persontyp_id=2) in der Dropdownlistbox angezeigt
//// letzte Änderung : Volker, 08.02.2004
//// was : Validierung der Torschützen mit dem Ergebnis
////

include "inc.php";
sitename("ma_spielplan_tore_popup.php",$_SESSION["groupid"]);
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php
focus();
if (priv("spiele_edit"))
{

$userid=$_SESSION["userid"];
$result=GetResult($db,"select name from sys_users where userid=$userid");
$username=$result["0"]["name"];


switch ($_REQUEST["action"])
{

 case 'edit':
       // Uebergabeparameter
          $spielid = $_REQUEST["spielid"];
       // echo '<br><b>Torschützen eingeben</b>';

       // Es können nur Torschützen für Ocholter Mannschaften eingeben werden.

       // Ocholter Mannschaft identifizieren
          $sqlstr = "select sp.heim_tore,sp.aus_tore,sp.heim_id,sp.aus_id,ma.name,ma.id,gespielt
                     from fb_mannschaft ma, fb_spiele sp,fb_tus_mannschaft tuma where
                    (sp.heim_id=ma.id or sp.aus_id=ma.id) and ma.id=tuma.id and sp.id=$spielid";
          //echo '<br>'.$sqlstr;
          $result = GetResult($db, $sqlstr);

	  $o2id="0";
	  if (isset($result["1"]["id"])) {
	  	echo '<br>Zwei TuS Mannschaften spielen gegeneienander !';
	  	$o2id=$result["1"]["id"];
	  }	
	  
	  
          $mannschaft=$result["0"]["name"];
          $mannschaftid=$result["0"]["id"];
          $gespielt=$result["0"]["gespielt"];
          $heimid=$result["0"]["heim_id"];
          $ausid=$result["0"]["aus_id"];
          $heimtore=$result["0"]["heim_tore"];
          $austore=$result["0"]["aus_tore"];

          $sqlstr = "select name from fb_mannschaft where id = $heimid";
          $result = GetResult($db, $sqlstr);
          $heimname = $result["0"]["name"];

          $sqlstr = "select name from fb_mannschaft where id = $ausid";
          $result = GetResult($db, $sqlstr);
          $ausname = $result["0"]["name"];
	
       // Saison lesen
          $sqlstr ="select saison_id,liga,spielzeit from fb_saison sa,fb_spiele sp
                    where sa.id=sp.saison_id and sp.id=$spielid";
          $result = GetResult($db, $sqlstr);
          $liga=$result[0]["liga"];
          $spielzeit=$result[0]["spielzeit"];
          $saisonid=$result[0]["saison_id"];

          $error="0";
          if ($gespielt == "0")
          {
              echo "<br><b>Spiel wurde noch nicht eingetragen</b>";
              $error="-1";
              echo '<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A>';
          }

          if ($mannschaft == "")
          {
              echo "<br><b>Keine TUS-Ocholt Mannschaft beteiligt</b>";
              $error="-1";
              echo '<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A>';
          }


          if ($error == "0")
          {

           echo '<TABLE WIDTH="100%" BORDER="0" >';
           echo '<TD COLSPAN="3" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
           echo '<B>Torschützen eintragen</B><BR>'.$liga.' - '.$spielzeit.'<BR> <BR>';
           echo $heimname.' - '.$ausname.'<BR>'.$heimtore.' - '.$austore.'</td>';
           echo '</TABLE>';

           // Anzahl der geschossenen Tore ermitteln
           $sqlstr = "select sum(heim_tore + aus_tore) tore from fb_spiele where id = $spielid";
           $result = GetResult($db,$sqlstr);
           $tore = $result["0"]["tore"];

           // Spielerkader laden
           // Prüfen, ob beide Mannschaften TuS - Mannschaften sind
           if ($o2id=="0") {
	           $sqlstr = "select pe.id,pe.name,pe.vorname from fb_person pe,fb_zwtab_person_typ_tus_mannschaft zw where
	                      saison_id=$saisonid and zw.person_id=pe.id and zw.tus_mannschaft_id=$mannschaftid
	                      and zw.persontyp_id in (2,4) order by pe.name,pe.vorname";
	   } else {
	           $sqlstr = "select pe.id,pe.name,pe.vorname from fb_person pe,fb_zwtab_person_typ_tus_mannschaft zw where
	                      saison_id=$saisonid and zw.person_id=pe.id and (zw.tus_mannschaft_id=$mannschaftid or zw.tus_mannschaft_id=$o2id)
	                      and zw.persontyp_id in (2,4) order by pe.name,pe.vorname";
	   }	   	
           
           $r1 = GetResult($db,$sqlstr);
           // Unbekannten User einfügen
           $r2["-1"]["name"]="unbekannt";
           $r2["-1"]["id"]="0";
           $result = array_merge($r2,$r1);
           unset($r1);
           unset($r2);
           
           // Namen und Vornamen als ein Array Feld für build_select zusammencatten
           $_result = $result;
           $n=0;
           foreach ($result as $i)
           {
               $result[$n][name]=$_result[$n]["name"].', '.$_result[$n]["vorname"];
               $n++;
           }


           echo '<BR>';
           echo '<Table WIDTH="100%" Border="0">';
           echo '<TR><TD BGCOLOR="#DDDDDD" ALIGN="CENTER"><B>Schütze</B></TD><TD BGCOLOR="#DDDDDD" ALIGN="CENTER"><B>Assist</B></TD><TD BGCOLOR="#DDDDDD" ALIGN="CENTER"><B>Minute</TD><TD BGCOLOR="#DDDDDD" ALIGN="CENTER"><B>Mannschaft</TD><TD BGCOLOR="#DDDDDD" ALIGN="CENTER"><B>Bemerkung<B></TD></TR>';

           echo '<FORM method="post" action="'.$PHP_SELF.'">';

           // Daten aus fb_tore laden
           $sqlstr = "select spieler_id,assist_id,minute,mannschaft_id,bemerkung from fb_tore  where spiel_id=$spielid order by minute";
           $result1 = GetResult($db,$sqlstr);

           for ($i = 1 ; $i <= $tore; $i++)
           {
             // Schütze
             echo '<TR>';
             echo '<TD ALIGN="CENTER">';
              $j=$i - 1;
              build_select($result,"name","id","spieler_id_".$i,"","1",$result1[$j]["spieler_id"]);
             echo '</TD>';
             // Assist
             echo '<TD ALIGN="CENTER">';
              build_select($result,"name","id","assist_id_".$i,"","1",$result1[$j]["assist_id"]);
             echo '</TD>';

             // Minute
             echo '<TD ALIGN="CENTER">';
             if (! isset($result1[$j]["minute"]))
               $result1[$j]["minute"] = "0";
             echo '<INPUT TYPE="TEXT" SIZE="1" NAME="minute_'.$i.'" VALUE="'.$result1[$j]["minute"].'"';
             echo '</TD>';

             // Mannschaft
             $ms["0"]["id"] = $heimid;
             $ms["0"]["name"] = $heimname;
             $ms["1"]["id"] = $ausid;
             $ms["1"]["name"] = $ausname;
             echo '<TD ALIGN="CENTER">';
              build_select($ms,"name","id","mannschaft_id_".$i,"","1",$result1[$j]["mannschaft_id"]);
             echo '</TD>';

             // Bemerkung
             echo '<TD ALIGN="LEFT">';
             echo '<INPUT TYPE="TEXT" SIZE="20" NAME="bemerkung_'.$i.'" VALUE="'.$result1[$j]["bemerkung"].'"';
             echo '</TD>';
            echo '</TR>';
           }
         echo '<INPUT type=hidden name="action" value="save">';
         echo '<INPUT type=hidden name="tore" value="'.$tore.'">';
         echo '<INPUT type=hidden name="heimid" value="'.$heimid.'">';
         echo '<INPUT type=hidden name="ausid" value="'.$ausid.'">';
         echo '<INPUT type=hidden name="heimtore" value="'.$heimtore.'">';
         echo '<INPUT type=hidden name="austore" value="'.$austore.'">';
         echo '<INPUT type=hidden name="spielid" value="'.$spielid.'">';
         echo '<TR></TR>';
         echo '<TR><TD COLSPAN=5 ALIGN="LEFT">';
         echo '<b>Daten löschen</b>: ';
         echo '<INPUT TYPE="CHECKBOX" NAME="DELETE" value="DELETE" />';
         echo '<TR></TR>';
         echo '<TR><TD COLSPAN="5" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
         echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
         echo '&nbsp;&nbsp;<INPUT type=submit value="Speichern"></TD></TR>';
         echo '</TABLE>';
       echo ' </FORM>';
      echo '</table>';
     }
    break;
 case 'save':
      $tore=$_POST["tore"];
      $spielid=$_POST["spielid"];
      $heimid=$_POST["heimid"];
      $ausid=$_POST["ausid"];
      $heimtore=$_POST["heimtore"];
      $austore=$_POST["austore"];
      $delete=$_POST["DELETE"];
      
      
      // Alle eingetragenen Tore aus der DB loeschen
      $sqlstr = "delete from fb_tore where spiel_id=$spielid";
      $result = doSQL($db,$sqlstr);


      if ($delete != "DELETE")
      {
        $result["code"]=0;
        for ($i=1; $i<=$tore; $i++)
        {
          // Prüfen der Minuteneingabe
          $minute = $_POST["minute_".$i];
          if (preg_match("/\D/", $minute))
          {
           $result["code"] = "-1";
           echo '<b>Falsches Format bei Minuteneingabe bei Tor Nr. '.$i.'</b><br>';
          }

          if ($minute == "")
          {
             $result["code"] = "-1";
             echo '<b>Keine Minuteneingabe bei Tor Nr. '.$i.'</b><br>';
          }

        }


        // Tore eintragen
       if ($result["code"] == 0)
       {
        for ($i=1; $i<=$tore; $i++)
        {
          $spielerid = $_POST["spieler_id_".$i];
          $assistid = $_POST["assist_id_".$i];
          $minute = $_POST["minute_".$i];
          $mannschaftid = $_POST["mannschaft_id_".$i];
          $bemerkung= $_POST["bemerkung_".$i];
          $state = addcslashes($bemerkung,'",\'');

          $sqlstr = "insert into fb_tore (spiel_id,spieler_id,assist_id,minute,mannschaft_id,bemerkung) values
                  ($spielid,$spielerid,$assistid,$minute,$mannschaftid,'$state')";
          $result = doSQL($db,$sqlstr);

        }
      }
     
    
    
    
    
       // Validierung der Eingabe
       if ($result["code"] == 0)
       {

         // Geschossene Tore der Heimmanschschaft
         $sqlstr = "select count(*) heimtore from fb_tore where spiel_id=$spielid and mannschaft_id=$heimid";
         $result=GetResult($db,$sqlstr);
         $_heimtore=$result["0"]["heimtore"];
         // Geschossene Tore der Gastmannschaft
         $sqlstr = "select count(*) austore from fb_tore where spiel_id=$spielid and mannschaft_id=$ausid";
         $result=GetResult($db,$sqlstr);
         $_austore=$result["0"]["austore"];


         if (($heimtore != $_heimtore) || ($austore != $_austore))
         {
            echo "<br><b>Die Daten wurden erfolgreich gespeichert.
                  <br>Warnung: Die Torschützen stimmen nicht mit dem Ergebnis überein !</b>";
            $result["code"]="1";
         }
        }
     }
     
     //$result["code"]=1;
     if ($result["code"] == 0)
      {
     	   mlog("Fussball: Speichern der Torschützen: ".$spielid);	
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