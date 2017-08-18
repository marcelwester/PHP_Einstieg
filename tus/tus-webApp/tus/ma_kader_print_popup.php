<?php
//// ma_kader_print_popup.php
////
//// letzte Änderung : Volker, 22.07.2004
//// was : Erstellung
////

include "inc.php";
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php
focus();  



          sitename("ma_kader_print_popup.php",$_SESSION["groupid"]);
if (priv("kader") || priv("kader_print")) {
        echo '<input type="image" title="Drucken" src="./images/Printer.gif" onClick="print();">';
        echo' <br>'; 
	$teamid = $_REQUEST["teamid"];
	$saisonid = $_REQUEST["saisonid"];
	
	$sqlstr="select spielzeit,liga,kat from fb_saison where id=".$saisonid;
	$result=GetResult($db,$sqlstr);
    $kat=$result["0"]["kat"];
    	
	$sqlstr="select name from fb_tus_mannschaft where id=".$teamid;
	$result1=GetResult($db,$sqlstr);
		
		
	echo '<div ALIGN="CENTER">';
	echo '<font size="+1">';
	echo $result1["0"]["name"].'<br>';
	echo $result["0"]["spielzeit"].'  '.$result["0"]["liga"];
	echo '</font><br>';
	echo 'Stand: '.date("d.m.Y");
	echo '</div>';
	echo '<br><br>';
	
        echo '<CENTER><TABLE WIDTH="100%" BORDER="0">';
                echo '<TR>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD" colspan="2">';
                                echo '<B>Name</B>';
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                echo '<B>Geb.</B>';
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                echo '<B>Pass-Nr.</B>';
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                echo '<B>Strasse</B>';
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                echo '<B>Ort</B>';
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                echo '<B>Telefon</B>';
                        echo '</TD>';
                    /*
                        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                echo '<B>email</B>';
                        echo '</TD>';
                    */
                echo '</TR>';
                        $sql_typ = 'select * from fb_person_typ order by id asc';
                        $result_typ = getResult($db, $sql_typ);
                        foreach ($result_typ as $typ)
                        {
                                echo '<TR><TD ALIGN="LEFT" COLSPAN="7" BGCOLOR="#FFFFFF"><B>'.$typ["name"].'</B></TD></TR>';
                                $sql_pers = 'select pe.name,pe.vorname,zw.person_id,
                                             pe.email,pe.tel,pe.geburtsdatum,pe.passnr,
                                             pe.strasse,pe.PLZ_Ort
                                               from fb_zwtab_person_typ_tus_mannschaft zw,fb_person pe
                                               where person_id > 0 and zw.person_id=pe.id
                                               and aktiv = 1 and tus_mannschaft_id = '.$teamid.' and persontyp_id = '.$typ["id"].'
                                               and saison_id='.$saisonid.' order by pe.name';

                                $result_pers = getResult($db, $sql_pers);
                                if (isset($result_pers[0]))
                                        foreach ($result_pers as $pers)
                                        {
                                               echo '<TR>';
                                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                                      echo $pers["name"].', '.$pers["vorname"];
                                                echo '</TD>';
                                                
                                           		if (($typ["id"]=="2" || $typ["id"]=="4") && $kat=="H") {;                                                
                                            		$check=playerCheck($pers["person_id"]);
                                            		if ($check=="0") {$checkcolor=$GRUEN; $checktext="GR";}
                                            		if ($check=="1") {$checkcolor=$GELB; $checktext="GE";}
                                            		if ($check=="2") {$checkcolor=$ROT;  $checktext="RO";}
                                            		if ($check=="3") {$checkcolor=$BLAU; $checktext="BL";}
                                            		
		
                                                	echo '<TD ALIGN="CENTER" BGCOLOR="'.$checkcolor.'">';
                                            		   echo $checktext;                                                
                                            		echo '</TD>';	
                                            	} else {
                                                  echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
   												  	echo "";
   												  echo '</TD>';	                                         		
                                            	}
                                            	
                                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                                        if (! isset($pers["geburtsdatum"]))
                                                        	echo '-';
                                                        else
                                                        	echo date("d.m.Y", strtotime($pers["geburtsdatum"]));
                                                echo '</TD>';
                                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                                                if ($pers["passnr"] == "")
                                                        	echo '-';
                                                        else
	                                                echo $pers["passnr"];
                                                echo '</TD>';
                                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                                                if ($pers["strasse"] == "")
                                                        	echo '-';
                                                        else
	                                                echo $pers["strasse"];
                                                echo '</TD>';
                                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                                                if ($pers["PLZ_Ort"] == "")
                                                        	echo '-';
                                                        else
	                                                echo $pers["PLZ_Ort"];
                                                echo '</TD>';


                                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                                        if (! isset($pers["tel"]))
                                                        	echo '-';
                                                        else
	                                                        echo $pers["tel"];
                                                echo '</TD>';
                      /*
                                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                                                if (! isset($pers["email"]))
                                                        	echo '-';
                                                        else
	                                                echo $pers["email"];
                                                echo '</TD>';
                     */
                                        echo '</TR>';
                                        }
                        }
        echo '</TABLE></CENTER>';

//        echo '<SCRIPT TYPE="text/javascript">';
//                echo 'setTimeout("window.print()",100);';
//        echo '</SCRIPT>';

} else {
	echo $no_rights;
}

function playerCheck ($personid) {
   global $saisonid,$teamid,$db;	
   // Lesen der letzten beiden Spiele der Saison
   $sqlstr  = "select id from fb_spiele where ";
   //$sqlstr .= "datum < sysdate() and ";
   $sqlstr .= "gespielt=1 and ";
   $sqlstr .= "saison_id=".$saisonid." and ";
   $sqlstr .= $teamid." in (heim_id,aus_id) ";
   $sqlstr .= "order by datum desc limit 2";
   //echo $sqlstr;
   $result=getResult($db,$sqlstr);
//   print_r($result);
   
   
   
   // Prüfen, ob der Spieler die beiden letzen Spiel gespielt hat
   // Letztes Spiel
   if (isset($result["0"]["id"])) {
	   $sqlstr  = "select id from fb_zwtab_spiele_person where ";
	   $sqlstr .= "spiel_id=".$result["0"]["id"]." and ";
	   $sqlstr .= "team_id=".$teamid." and ";
	   $sqlstr .= "person_id=".$personid;
	   $result1=getResult($db,$sqlstr);
	   if (isset($result1)) {
	   	  $last1="yes";
	   } else {
	   	  $last1="no";
	   }
   } else {
   	 // Es wurde in der Saison noch gar kein Spiel ausgetragen
   	 $last1="no";
   }

   // Vorletzes Spiel
   if (isset($result["1"]["id"])) {
	   $sqlstr  = "select id from fb_zwtab_spiele_person where ";
	   $sqlstr .= "spiel_id=".$result["1"]["id"]." and ";
	   $sqlstr .= "team_id=".$teamid." and ";
	   $sqlstr .= "person_id=".$personid;
	   $result1=getResult($db,$sqlstr);
	   if (isset($result1)) {
	   	  $last2="yes";
	   } else {
	   	  $last2="no";
	   }
   } else {
   	 // Es wurde erst ein Spiel ausgetragen
   	 $last2="no";
   }
   
   // Letzten beiden Spiele gespielt ==> Festgespielt
   if ($last1=="yes" && $last2=="yes") return "2";
   // Nur das letzte Spiel gespielt
   if ($last1=="yes" && $last2=="no") return "3";
   // Eines der letzten beiden Spiele gespielt
   if ($last1=="yes" || $last2=="yes") return "1";
   // Letzten beiden Spiel nicht gespielt ==> Spieler ist frei
   return "0";
}	  

	if ($kat=="H") {
	     echo '<TABLE WIDTH="100%" BORDER="0" ALIGN="CENTER">';
                echo '<TR>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="'.$GRUEN.'" colspan="2">';
                        	echo "Spieler hat die letzten beiden<br>Spiele nicht mitgespielt";
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="'.$BLAU.'" colspan="2">';
                        	echo "Spieler hat das letzte<br>Spiel mitgespielt";
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="'.$GELB.'" colspan="2">';
                        	echo "Spieler hat das vorletzte<br>Spiel mitgespielt";
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="'.$ROT.'" colspan="2">';
                        	echo "Spieler hat die beiden letzten Spiele mitgespielt";
                        echo '</TD>';
                echo '</TR>';
   		echo '</TABLE>';
	}

 
closeConnect($db);
?>
</BODY>
</HTML>