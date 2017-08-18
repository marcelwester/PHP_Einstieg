<?php
//// ma_spielplan_player_popup.php
////
//// letzte Änderung : Volker, 19.07.2006
//// was : Erstellung
////

include "inc.php";
sitename("ma_spielplan_player_popup.php",$_SESSION["groupid"]);
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

if  ($_REQUEST["action"]=='save') {
      $spielid=$_POST["spielid"];
      $teamid=$_POST["teamid"];
      $saisonid=$_POST["saisonid"]; 
      $add=$_POST["add"]; 
      $del=$_POST["del"]; 
        
	  $error="0";
      if ($_POST["actionPlayer"]=="add") {
	  	if (isset($add)) {
	        foreach ($add as $playerid) {
	        	$sqlstr  ="insert into fb_zwtab_spiele_person (person_id,spiel_id,team_id,saison_id) values (";
	        	$sqlstr .=$playerid.",";
	        	$sqlstr .=$spielid.",";
	        	$sqlstr .=$teamid.",";
	        	$sqlstr .=$saisonid.")";
 				$result=doSQL($db,$sqlstr);
	        	if ($result["code"]!="0") {
	        		$error="1";
	        	}
	        }

      	}
      } 
      if ($_POST["actionPlayer"]=="del") {
        if (isset($del)) {
	        foreach ($del as $playerid) {
				$sqlstr ="delete from fb_zwtab_spiele_person where ";
				$sqlstr.="person_id=".$playerid." and ";
				$sqlstr.="spiel_id=".$spielid." and ";
				$sqlstr.="team_id=".$teamid." and ";
				$sqlstr.="saison_id=".$saisonid;
	        	$result=doSQL($db,$sqlstr);
	        	if ($result["code"]!="0") {
	        		echo '<br>'.$sqlstr;
	        		$error="1";
	        	}
	        }
	       
		}
      }
      
     if ($error == 0)
      {
     	   mlog("Fussball: Speichern der Spieler eines Spieles: ".$spielid);	
      } else {
           echo 'Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:window.history.back();">Hier klicken, um zurückzukehren</A>';
      }
}

if  ($_REQUEST["action"]=='save' || $_REQUEST["action"]=='edit') {     
      // Uebergabeparameter
      $spielid = $_REQUEST["spielid"];
      $teamid = $_REQUEST["teamid"];
      if (isset($_REQUEST["player"])) {
      	$player = $_REQUEST["player"];
      } else {
      	$player = "kader";
      }

   
      $sqlstr = "select name,datum,saison_id from fb_mannschaft m,fb_spiele s where heim_id=m.id and s.id=".$spielid;
      $result = GetResult($db, $sqlstr);
      $heimname = $result["0"]["name"];
      $datum = $result["0"]["datum"];
      $saisonid = $result["0"]["datum"];

      $sqlstr = "select name from fb_mannschaft m,fb_spiele s where aus_id=m.id and s.id=".$spielid;
      $result = GetResult($db, $sqlstr);
      $ausname = $result["0"]["name"];
 
      // Saison lesen
      $sqlstr ="select sp.bemerkung,saison_id,liga,spielzeit,closed from fb_saison sa,fb_spiele sp ";
      $sqlstr.="where sa.id=sp.saison_id and sp.id=$spielid";
      $result = GetResult($db, $sqlstr);
      $liga=$result[0]["liga"];
      $spielzeit=$result[0]["spielzeit"];
      $saisonid=$result[0]["saison_id"];
      $spielbemerkung=$result["0"]["bemerkung"];
      if (isset($result["0"]["closed"])) {
      	$closed=$result["0"]["closed"];
      } else {
      	$closed=0;
      }

      echo '<TABLE WIDTH="100%" BORDER="0" >';
      echo '<TD COLSPAN="3" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	  echo '<B>Spielerauswahl</B><BR>'.$liga.' - '.$spielzeit.'<BR>';
      if ($gespielt == "1") {
   		echo date("d.m.Y - H:i", strtotime($datum)).'<br>';	
        echo $heimname.' - '.$ausname.'<BR>'.$heimtore.' - '.$austore.'</td>';
      } else {
        echo '<BR>'.$heimname.' - '.$ausname.'<BR>'.date("d.m.Y - H:i", strtotime($datum)).'</td>';
      }
      echo '</TABLE>';
      echo '<br>';
      echo 'Mehrere Spieler können durch Halten der "Strg" Taste und Anklicken mit der Maus selektiert werden'; 
      echo '<br><br>';
      unset($result);
	  //Lesen der Spieler für die linke selectbox 
	  if ($player=="kader") {
	  	  // alle spieler des kaders abzüglich der schon gespeicherten 
	      // schon gespeicherte Spieler lesen und als csv String in ids speichern
	      $sqlstr= 'select person_id from fb_zwtab_spiele_person where ';
	      $sqlstr.= ' team_id='.$teamid.' ';
	      $sqlstr.= ' and spiel_id='.$spielid.' ';
		  $result=getResult($db,$sqlstr);
		  if (isset($result)) {
		  	$komma="";
		  	foreach ($result as $row) {
		  		$ids.=$komma.$row["person_id"];
		  		$komma=",";
		  	}
		  } else {
			$ids="0";
		  }

	      $sqlstr = 'select CONCAT(pe.name,", ",pe.vorname) name,zw.person_id ';
	      $sqlstr.= 'from fb_zwtab_person_typ_tus_mannschaft zw,fb_person pe ';
	      $sqlstr.= 'where person_id > 0 and zw.person_id=pe.id ';
	      $sqlstr.= 'and aktiv = 1 and tus_mannschaft_id = '.$teamid.' ';
	      $sqlstr.= 'and persontyp_id in (2,4) ';
	      $sqlstr.= 'and saison_id='.$saisonid.' ';
	      $sqlstr.= 'and person_id not in ('.$ids.') ';
	      $sqlstr.= ' order by name';
	      
      } else {
	  	  // alle spieler von fb_person abzüglich der schon gespeicherten 
	      // schon gespeicherte Spieler lesen und als csv String in ids speichern
	      $sqlstr= 'select person_id from fb_zwtab_spiele_person where ';
	      $sqlstr.= ' team_id='.$teamid.' ';
	      $sqlstr.= ' and spiel_id='.$spielid.' ';
		  $result=getResult($db,$sqlstr);
		  if (isset($result)) {
		  	$komma="";
		  	foreach ($result as $row) {
		  		$ids.=$komma.$row["person_id"];
		  		$komma=",";
		  	}
		  } else {
			$ids="0";
		  }

	      $sqlstr = 'select CONCAT(pe.name,", ",pe.vorname) name,id person_id from fb_person pe ';
	      $sqlstr.= 'where pe.id not in ('.$ids.') ';
	      $sqlstr.= ' order by name';
	  }
      $resultleft = getResult($db, $sqlstr);
	  if (!isset($resultleft)) {
	  	$resultleft["0"]["name"]="Kein Spieler";
	  	$resultright["0"]["person_id"]="-1";
	  }
	  
	  //Lesen der Spieler für die rechte selectbox 
	  $sqlstr = 'select CONCAT(pe.name,", ",pe.vorname) name, zw.person_id ';
	  $sqlstr.= 'from fb_person pe,fb_zwtab_spiele_person zw where ';
	  $sqlstr.= 'zw.spiel_id='.$spielid.' ';
	  $sqlstr.= 'and zw.team_id='.$teamid.' ';
	  $sqlstr.= 'and zw.person_id=pe.id ';
	  $sqlstr.= 'order by name ';
      $resultright = getResult($db, $sqlstr);
	  if (!isset($resultright)) {
	  	$resultright["0"]["name"]="Kein Spieler";
	  	$resultright["0"]["person_id"]="-1";
	  }
   
    
    echo '<FORM method="post" action="ma_spielplan_player_popup.php?action=save&player='.$player.'&PHPSESSID='.session_id().'">';
	  echo '<table WIDTH="70%" BORDER="0" CLASS="none" align="center">';
	  	echo '<tr>';
	  		echo '<td class="none" align="center" colspan="3">';
				// Spielerauswahl
				echo 'Filter: ';
            	$url = 'ma_spielplan_player_popup.php?action=edit&spielid='.$spielid.'&teamid='.$teamid.'&PHPSESSID='.session_id();
				echo '<SELECT onChange="window.location.href=\''.$url.'&fbmenu=yes&player=\'+this.value;">';
			    		if ($player=="kader") {
				    		echo '<OPTION SELECTED VALUE="Kader">Kader</OPTION>';
				    		echo '<OPTION VALUE="alle">Alle Spieler</OPTION>';
				    	} else {
				    		echo '<OPTION VALUE="kader">Kader</OPTION>';
				    		echo '<OPTION SELECTED VALUE="Alle Spieler">Alle</OPTION>';
				    	}
		        echo '</SELECT>';
	  	echo '</tr>';
	  	
	  	echo '<tr>';
	  		echo '<td class="none" align="center">';
				echo '<b>Spieler</b>';
	  		echo '</td>';
	  		echo '<td class="none" align="center">';
				echo '';
	  		echo '</td>';
	  		echo '<td class="none" align="center">';
				echo '<b>mitgespielt</b>';
	  		echo '</td>';
	  	
	  	
	  	echo '<tr>';
	  		echo '<td class="none" align="center">';
				build_select($resultleft,"name","person_id","add[]","multiple",18,"");					  			
	  		echo '</td>';

	  		echo '<td class="none" align="center">';
	  			  if ($closed != "1") {
		  			  echo '<input type="hidden" name="spielid" value="'.$spielid.'">';    
		  			  echo '<input type="hidden" name="teamid" value="'.$teamid.'">';
		  			  echo '<input type="hidden" name="saisonid" value="'.$saisonid.'">';
		  			  echo '<input type="hidden" name="actionPlayer" value="">';
		  			  echo '<input type="image" src="images/pfeilrechts.jpg" alt="add" onclick="this.form.actionPlayer.value=\'add\'">';
		  			  echo '<br><br><br>';
		  			  echo '<input type="image" name="del_player" src="images/pfeillinks.jpg" alt="del" onclick="this.form.actionPlayer.value=\'del\'">';
	  			  }
	  		echo '</td>';
	  		
	  		// schon ausgewählte Spieler
	  	    $i=0;
	  	    foreach ($resultright as $row) {
				if ($i < 9)
					$blank="&nbsp;&nbsp;";
				else
					$blank="";
					
				$resultright[$i]["name"]=$blank.($i+1).". ".$row["name"];
				$i++;
	  	    }
	  		echo '<td class="none" align="center">';
	  			build_select($resultright,"name","person_id","del[]","multiple",18,"");					  			
	  		echo '</td>';
		echo '</tr>';	  		
	  echo '</table>';
	  echo '<br><br>';
      echo '<center><INPUT TYPE="button" VALUE="Beenden" onClick="window.close();"></center>';
	echo '</form>';         
}
}
else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';

closeConnect($db);

?>
</BODY>
</HTML>