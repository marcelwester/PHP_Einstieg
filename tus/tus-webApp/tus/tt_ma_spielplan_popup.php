<?php
//// tt_ma_spielplan_popup.php
//// letzte Änderung : Volker, 10.09.2004
//// was : Speicherung Spielbericht geändert
////
//// letzte Änderung : Volker, 06.03.2004
//// was : Bemerkung eingefügt
////
//// letzte Änderung : Volker, 15.02.2004
//// was : trim Funktion auf $austore,$heimtore und $spieltag angewendet
//// closeConnect($db) am Ende gesetzt
////
//// Änderung : Volker, 07.02.2004
//// was : Formatänderungen, Datumseingabe / Abfrage / Attribut gespielt wir gesetzt
////

include "inc.php";
sitename("tt_ma_spielplan_popup.php",$_SESSION["groupid"]);
focus();
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<SCRIPT LANGUAGE="JavaScript">
<!--
        function viewImage(imageid)
        {
              imgId = imageid;
              window.open("showimage.php?id="+imgId,"viewImage","width=650, height=550, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
        }
-->
</SCRIPT>
<?php
if (priv("spiele_add") || priv("spiele_edit"))
{

$userid=$_SESSION["userid"];
$result=GetResult($db,"select name from sys_users where userid=$userid");
$username=$result["0"]["name"];


switch ($_REQUEST["action"])
{
 case 'add':
     $saisonid=$_REQUEST["saisonid"];
     // Spielzeit lesen
     $sqlstr ="select liga,spielzeit from tt_saison where id=$saisonid";
     $result = GetResult($db, $sqlstr);
     $liga=$result[0]["liga"];
     $spielzeit=$result[0]["spielzeit"];

     echo '<TABLE WIDTH="100%" BORDER="0" >';
     echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
     echo '<B>Neues Spiel eingeben</B><BR>'.$liga.' - '.$spielzeit.'<BR> - '.$username.' - </TD></TR>';
     echo '</TABLE>';

     echo '<FORM method="post" action="'.$PHP_SELF.'">';
       // Spieltag
/*
          echo "<B>Spieltag: </B>";
          echo '<INPUT TYPE="NUMBER" SIZE="2" NAME="tag" VALUE="" />';
          echo "<br></br>";
*/
  	echo '<br>';
   	echo '<table  align="left">';
   	echo '<tr>';	
       // Datum und Uhrzeit
           echo '<TD align="right" class="none">Datum </TD><TD class="none"><INPUT type=text name=datum size=14 value="'.date('d.m.Y').'"></TD>';
        echo '</tr><tr>';
           echo '<TD align="right" class="none">Uhrzeit </TD><TD class="none"><INPUT type=text name=zeit size=14 value="00:00"></TD>';
        echo '</tr><tr>';
           echo '<TD align="right" class="none">Gruppierung </TD><TD class="none"><INPUT type=text name=grouped size=14 value=""></TD>';
	echo '</tr>';
	echo '</table>';

       echo '<BR></BR>';

       // Mannschaften der Saison lesen
       $sql ="select id,name from tt_mannschaft, tt_zwtab_mannschaft_saison where
              saison_id=$saisonid and mannschaft_id=id order by name";
       $result=GetResult($db,$sql);

       echo "<table BORDER=0 ALIGN=LEFT WIDTH=100% ><TD>Heimmannschaft</TD><TD>Gastmannschaft</TD>";
        
        echo "<tr><TD ALIGN=CENTER>";
         	build_select($result,"name","id","heimid","","15","");
        echo "</TD>";
        echo "<TD ALIGN=CENTER>";
         	build_select($result,"name","id","ausid","","15","");
        echo "</TD></TR>";

        echo "<tr><TD ALIGN=CENTER>";
        	echo '<INPUT TYPE="TEXT" SIZE="1" NAME="heimtore" VALUE="">';
        echo "</TD><TD ALIGN=CENTER>";
        	echo '<INPUT TYPE="TEXT" SIZE="1" NAME="austore" VALUE="">';
        echo "</TD></TR>";

/*  
        $sql_fotos = 'select image_id, descr from sys_images where kategorie = 9 and linked = 0 order by descr';
        $result_fotos = getResult($db,$sql_fotos);

         echo '<br><b>Verfügbare Bilder</b><BR>';

    	if (isset($result_fotos[0]))
        	foreach ($result_fotos as $foto)
        	{
        		echo '<INPUT TYPE="CHECKBOX" NAME="image_'.$foto["image_id"].'"> '.$foto["descr"];
        		echo '&nbsp;<A HREF="javascript:viewImage('.$foto["image_id"].');">';
        		echo '<IMG SRC="images/view.jpg" HEIGHT="16" WIDTH="16" BORDER="0" ALT="Bild ansehen"></A><BR>';
        	}
        else
        	echo '<em>keine</em>';
*/
       ?>

         <INPUT type=hidden name="action" value="save_add">
         <INPUT type=hidden name="saisonid" value="<?php echo $saisonid; ?>"
           <TR></tr><TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">
            <INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">
            &nbsp;&nbsp;
            <INPUT type=submit value="Spiel anlegen">
            </TD></TR>
         </TABLE>
       </FORM>
       <?php

     break;

 case 'save_add':
      $saisonid=$_REQUEST["saisonid"];
      $heimid=$_POST["heimid"];
      $ausid=$_POST["ausid"];
      $heimtore=trim($_POST["heimtore"]);
      $austore=trim($_POST["austore"]);
      //$spieltag=trim($_POST["tag"]);
      $datum=$_POST["datum"];
      $zeit=$_POST["zeit"];

      $result["code"]= 0;

      if ($heimid==$ausid)
      {
        echo  "<b>Eine Mannschaft kann nicht gegen sich selbst spielen !</b><BR>";
        $result["code"] = "-1";
      }

      // Datum- und Zeiteingabe prüfen und konvertieren
      $ts=ts2db($datum,$zeit);
      //echo $ts;
      if ($ts == "-1") {
           $result["code"] = "-1";
           echo "<b>Falsches Datumsformat: TT:MM:JJJJJ</b><br>";
      }
      if ($ts == "-2")
      {
           $result["code"] = "-1";
           echo "<b>Falsches Zeitformat: HH:MM</b><br>";
      }


      // Ergebniseintraege pruefen
      if ((preg_match("/\D/", $austore)) || (preg_match("/\D/", $heimtore)) || (preg_match("/\D/", $spieltag)))
      {
         $result["code"] = "-1";
         echo "<b>Falsches Format bei Ergebniseingabe/Spieltag</b><br>";
      }

      // Gruppierung
      if ($_POST["grouped"]=="") {
      	$grouped="null";
      } else {
      	$grouped="'".$_POST["grouped"]."'";
      }
      
      if ($result["code"] == 0)
      {
        if ( ($austore == "") || ($heimtore == "") )
        {
          // Spielergebnis wurde nicht eingetragen
          $sqlstr="insert into tt_spiele (saison_id,heim_id,aus_id,datum,userid,toc,gespielt,grouped)
          values  ($saisonid,$heimid,$ausid,'$ts',$userid,now(),0,$grouped)";

        }
        else
        {
          // Spielergebnis wurde eingetragen
          $sqlstr="insert into tt_spiele (saison_id,heim_id,aus_id,heim_tore,aus_tore,datum,spieltag,userid,toc,gespielt)
          values  ($saisonid,$heimid,$ausid,$heimtore,$austore,'$ts',0,$userid,now(),1,$grouped)";
        }
        //echo "<br>".$sqlstr;
        $result=doSQL($db,$sqlstr);
       
      }

      if ($result["code"] == 0)
      {
		   mlog("Tischtennis: Es uwrde ein neues Spiel gespeichert");
           echo 'Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A>';
           echo '<SCRIPT TYPE="text/javascript">';
                echo 'opener.location.reload();';
                echo 'setTimeout("window.close()",1000);';
           echo '</SCRIPT>';
      }
      else
      {

           echo 'Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:window.history.back();">Hier klicken, um zurückzukehren</A>';
      }
 break;

 case 'edit':
       // Uebergabeparameter
          $spielid = $_REQUEST["spielid"];

       //  $saisonid ="1";
       // Saison lesen
          $sqlstr ="select saison_id,liga,spielzeit from tt_saison sa,tt_spiele sp
                    where sa.id=sp.saison_id and sp.id=$spielid";
          $result = GetResult($db, $sqlstr);
          $liga=$result[0]["liga"];
          $spielzeit=$result[0]["spielzeit"];
          $saisonid=$result[0]["saison_id"];


       // Letzte Änderung lesen (name,toc)
          $TS = "DATE_FORMAT(toc,'%d.%m.%Y') toc_datum, TIME_FORMAT(toc,'%H:%i:%s') toc_zeit";
          $sqlstr = "select su.name,$TS from tt_spiele sp,sys_users su where sp.userid=su.userid and sp.id=$spielid";
          $result = GetResult($db, $sqlstr);
          $lastedituser=$result[0]["name"];
          $toc_datum=$result[0]["toc_datum"];
          $toc_zeit=$result[0]["toc_zeit"];

          //echo "Letzte Änderung von ".$lastedituser." am ".$toc_datum." - ".$toc_zeit."";
          echo '<FORM method="post" action="'.$PHP_SELF.'">';

       // Heimmannschaft
          $TS = "DATE_FORMAT(datum,'%d.%m.%Y') datum, TIME_FORMAT(datum,'%H:%i') zeit";
          $sql = "select sp.spielstaette_id,sp.grouped,sp.bemerkung,ma.id,ma.name,heim_tore,aus_tore,$TS,spieltag,gespielt
                  from tt_mannschaft ma,tt_spiele sp
                  where heim_id=ma.id and sp.id=$spielid";
          $result = GetResult($db, $sql);
          $heimid=$result[0]["id"];
          $heimname=$result[0]["name"];
          $heimtore=$result[0]["heim_tore"];
          $austore=$result[0]["aus_tore"];
          $datum=$result[0]["datum"];
          $zeit=$result[0]["zeit"];
          $spieltag=$result[0]["spieltag"];
          $gespielt=$result[0]["gespielt"];
          $bemerkung=$result[0]["bemerkung"];
          $grouped=$result[0]["grouped"];
          $spielstaetteid=$result[0]["spielstaette_id"];
       //Gastmannschaft
          $sql = "select ma.id,ma.name from tt_mannschaft ma,tt_spiele sp where aus_id=ma.id and sp.id=$spielid";
          $result = GetResult($db, $sql);
          $ausid=$result[0]["id"];
          $ausname=$result[0]["name"];
          
       // Pruefen, ob eine TuS - Mannschaft beteiligt ist:
          $sqlstr = "select count(*) tus from tt_tus_mannschaft where ";
          $sqlstr .= "id in (".$heimid.",".$ausid.")";
          $result = getResult($db,$sqlstr);
          $tusteam=0;
          if ($result["0"]["tus"]=="1") $tusteam=1;

      echo '<TABLE WIDTH="100%" BORDER="0" >';
      echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
      echo '<B>Spieldaten editieren</B><BR>'.$liga.' - '.$spielzeit.'<BR> - '.$username.' - </TD></TR>';
      echo '</TABLE>';



   	echo '<table  align="left">';
         echo '<TR><TD align="right" class="none"><b>Datum</b></TD> <TD class="none"><INPUT type=text name=datum size=14 value="'.$datum.'">';
      // Spielstätte
         if ($tusteam==1) {
            echo '&nbsp;&nbsp;<b>Spielstätte:</b> ';
            $sqlstr = 'select id,name from spielstaette where aktiv = 1 order by id';
            $st = getResult($db,$sqlstr);
            $st["-1"]["id"]="0";
            $st["-1"]["name"]="automatisch";
            build_select($st,"name","id","spielstaetteid","","1",$spielstaetteid);
            unset($st);
         } else {
            echo '<input type="hidden" name="spielstaetteid" value="0"/>';
         }

        echo '</TD></TR>';
        echo '<TR><TD align="right" class="none"><b>Uhrzeit</b></TD> <TD class="none"><INPUT type=text name=zeit size=14 value="'.$zeit.'"></TD></TR>';
        echo '<TR><TD align="right" class="none"><b>Gruppierung</b></TD> <TD class="none"><INPUT type=text name=grouped size=14 value="'.$grouped.'"></TD></TR>';
	echo '</table>';
	
	echo '<br><br><br><br><br><br>';
         
         if ($gespielt == "1")
         {
           echo '<BR><INPUT type=radio name=gespielt value=0 >noch nicht gespielt     ';
           echo '<INPUT type=radio name=gespielt value=1 checked>gespielt';
         }
         else
         {
           echo '<BR><INPUT type=radio name=gespielt value=0 checked>noch nicht gespielt';
           echo '<INPUT type=radio name=gespielt value=1>gespielt';
         }

// Bilder
    
	$sql_fotos = 'select image_id, descr, linked from sys_images where kategorie = 9 and (linked = 0 or linked = '.$spielid.') order by descr';
        $result_fotos = getResult($db,$sql_fotos);

         echo '<br><b>Verfügbare Bilder</b><BR>';
       	if (isset($result_fotos[0]))
        	foreach ($result_fotos as $foto)
        	{
        		$checked = '';
        		if ($foto["linked"] == $spielid)
       				$checked = 'CHECKED';

        		echo '<INPUT TYPE="CHECKBOX" '.$checked.' NAME="image_'.$foto["image_id"].'"> '.$foto["descr"];
        		echo '&nbsp;<A HREF="javascript:viewImage('.$foto["image_id"].');">';
        		echo '<IMG SRC="images/view.jpg" HEIGHT="16" WIDTH="16" BORDER="0" ALT="Bild ansehen"></A><BR>';
        	}
		else
	    	echo '<em>keine</em>';


	echo '<BR><b>Bemerkung</b>: ';
	echo '<INPUT type=text name=bemerkung size=80 value="'.$bemerkung.'">';
        echo '<BR><BR>';  

      // Spielbericht lesen
         $sql="select date,spiel_id,name,spiel_bericht,edit_id from tt_sp_bericht,sys_users su where spiel_id=$spielid and verfasser_id=su.userid";
         $result=GetResult($db,$sql);
         if (isset($result)) {
         	$verfasser=$result["0"]["name"];
         	$spielbericht=$result["0"]["spiel_bericht"];
	 	$spielberichtid=$result["0"]["spiel_id"];	
      	 	echo '<INPUT type=hidden name=spielberichtid value='.$spielberichtid.'>';
      	 	if ($result["0"]["edit_id"]!="0") {
         		$sql="select name from sys_users where userid=".$result["0"]["edit_id"];
         		$result1=GetResult($db,$sql);
         		$edit="Letzte Speicherung von ".$result1["0"]["name"]." (".date("d.m.Y - H:i",strtotime($result["0"]["date"])).")" ;
         		
         	}
         	
      	 }
      // Ergebnisse lesen
         $sql ="select id,name from tt_mannschaft, tt_zwtab_mannschaft_saison where
                saison_id=$saisonid and mannschaft_id=id";
         $result=GetResult($db,$sql);


	

         echo '<table BORDER=0 ALIGN=LEFT ><TR><TD BGCOLOR="#DDDDDD" ALIGN="CENTER"><B>Heimmannschaft</B></TD><TD BGCOLOR="#DDDDDD" ALIGN="CENTER"><B>Gastmannschaft</B></TD></TR>';
           echo "<TR><TD ALIGN=CENTER>";
              build_select($result,"name","id","heimid","","1",$heimid);
           echo "</TD>";
           echo "<TD ALIGN=CENTER>";
              build_select($result,"name","id","ausid","","1",$ausid);
           echo "</TD></TR>";

          echo "<TR><TD ALIGN=CENTER>";
           echo '<INPUT TYPE="TEXT" SIZE="1" NAME="heimtore" VALUE="'.$heimtore.'"';
          echo "</TD><TD ALIGN=CENTER>";
           echo '<INPUT TYPE="TEXT" SIZE="1" NAME="austore" VALUE="'.$austore.'"';
          echo "</TD></TR>";

          echo '<TR><TD COLSPAN="2">';
          echo "<BR><b>Verfasser: ".$verfasser."</b>&nbsp;&nbsp;&nbsp;&nbsp;".$edit."</BR>";
            echo '<textarea name="spielbericht" cols="75" rows="9" >'.$spielbericht.'</textarea>';
          echo '</TD></TR>';
        ?>

         <INPUT type=hidden name=action value=save_edit>
         <INPUT type=hidden name="spielid" value="<?php echo $spielid; ?>"

         <TR><TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">
            <INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">
             &nbsp;&nbsp;
            <INPUT type=submit value="Spiel speichern">
         </TD></TR>
         </TABLE>
       </FORM>
      <?php

       break;
 case 'save_edit':
      $spielid = $_POST["spielid"];
      if (! isset($spielid))
      {
        $spielid=$_REQUEST["spielid"];
      }
      $heimid=$_POST["heimid"];
      $ausid=$_POST["ausid"];
      $heimtore=trim($_POST["heimtore"]);
      $austore=trim($_POST["austore"]);
      //$spieltag=$_POST["tag"];
      $datum=$_POST["datum"];
      $zeit=$_POST["zeit"];
      $spielbericht=$_POST["spielbericht"];
      $gespielt=$_POST["gespielt"];
      $bemerkung=$_POST["bemerkung"];
      $spielberichtid=$_POST["spielberichtid"];
      $spielstaetteid=$_POST["spielstaetteid"];
	

// Bilder
    $sql_fotos = 'update sys_images set linked = 0 where kategorie = 9 and linked = '.$spielid;
    $result_fotos = doSQL($db,$sql_fotos);

    $sql_fotos = 'select image_id from sys_images where kategorie = 9 and linked = 0 order by descr';
    $result_fotos = getResult($db,$sql_fotos);

    if (isset($result_fotos))
    {
	foreach($result_fotos as $foto)
	{
		if (isset($_POST["image_".$foto["image_id"]]))
		{
	    	$sql_fotos = 'update sys_images set linked = '.$spielid.' where image_id = '.$foto["image_id"];
    		$result_fotos2 = doSQL($db,$sql_fotos);
    	}
	}
    } 
    
    
      $result["code"] = 0;
      // Datum- und Zeiteingabe prüfen und konvertieren
      $ts=ts2db($datum,$zeit);
      //echo $ts;
      if ($ts == "-1") {
           $result["code"] = "-1";
           echo "<b>Falsches Datumsformat: TT:MM:JJJJJ</b><br>";
      }
      if ($ts == "-2")
      {
           $result["code"] = "-1";
           echo "<b>Falsches Zeitformat: HH:MM</b><br>";
      }


      // Ergebniseintraege pruefen
      if ((preg_match("/\D/", $austore)) || (preg_match("/\D/", $heimtore)))
      {
         $result["code"] = "-1";
         echo "<b>Falsches Format bei Ergebniseingabe</b><br>";
         echo "##".$austore."##";
         echo "##".$heimtore."##";
      }


      if ($heimid==$ausid)
      {
        echo  "<b>Eine Mannschaft kann nicht gegen sich selbst spielen !</b><BR>";
        $result["code"] = "-1";
      }

      // Gruppierung
      if ($_POST["grouped"]=="") {
      	$grouped="null";
      } else {
      	$grouped="'".$_POST["grouped"]."'";
      }

      // Speichern
      if ($result["code"] == 0)
      {
        $sqlstr="update tt_spiele set heim_id=$heimid,aus_id=$ausid,heim_tore=$heimtore,
                 aus_tore=$austore,datum='$ts',userid=$userid,toc=now(),
                 gespielt=$gespielt,bemerkung='$bemerkung',grouped=$grouped,spielstaette_id=$spielstaetteid
                where id=$spielid";
        //echo $sqlstr;
        $result=doSQL($db,$sqlstr);


        // Spielbericht speichern
        if (($spielbericht != "") && $result["code"]==0)
        {
        $userid=$_SESSION["userid"];
		  if (! isset($spielberichtid)) {
          $sate = $spielbericht;
          $sqlstr="insert into tt_sp_bericht (spiel_bericht,spiel_id,verfasser_id, date) values
                   ('$state',$spielid,$userid,now())";
        } else {
          $sqlstr  = "update tt_sp_bericht set ";
          $sqlstr .="spiel_bericht='$spielbericht',";
          $sqlstr .="edit_id=".$userid.",";
          $sqlstr .="date=sysdate() ";
          $sqlstr .="where spiel_id=".$spielid;
        }		
          $result=doSQL($db,$sqlstr);
        }
        else
        {
           // Wenn nichts im Spielberichtfeld steht, soll es auch gelöscht werden, für den Fall,
           // dass es vorher einen gab
          $sqlstr="delete from tt_sp_bericht where spiel_id=$spielid";
          $result1=doSQL($db,$sqlstr);
        }
      }
      if ($result["code"] == 0)
      {
           mlog ("Tischtennis: Ein Spiel wurde gespeichert: ".$spielid);
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
closeConnect($db);
}
else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
?>
</BODY>
</HTML>