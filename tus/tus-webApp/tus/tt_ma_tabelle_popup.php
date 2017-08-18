<?php
//// tt_ma_tabelle_popup.php
////
//// letzte Änderung : Volker, 07.03.2004
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
          sitename("tt_ma_tabelle_popup.php",$_SESSION["groupid"]);
	  $teamid = $_REQUEST["teamid"];
	  $saisonid = $_REQUEST["saisonid"];
	  
	  
	
	  $sqlstr = "select ma.name,sa.spielzeit,sa.liga from tt_saison sa,tt_mannschaft ma 
	             where sa.id=$saisonid and ma.id=$teamid";
	 
	  $result = GetResult($db,$sqlstr);
	  $teamname = $result["0"]["name"];
	  $liga = $result["0"]["liga"];
	  $spielzeit = $result["0"]["spielzeit"];
	  
	  $sqlstr1 = "select name,id from tt_mannschaft ma, tt_zwtab_mannschaft_saison zw where 
	  	      mannschaft_id=ma.id and saison_id=$saisonid";
	  $result1 = GetResult($db,$sqlstr1);
          $teamname=array();
	  foreach ($result1 as $row)
	  {
	    	$teamname[$row["id"]] = $row["name"];
	  }
	  unset($result1);
	  
	  

	  
	  echo '<TABLE WIDTH="100%" BORDER="0" >';
          echo '<TD COLSPAN="3" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
            echo '<B>Spielplan '.$teamname[$teamid].'</B><BR><BR>'.$liga.' - '.$spielzeit.'<BR>';
          echo '</td>';
         
          echo '</TABLE>';
          echo '<br>';
           echo '<input type="image" title="Drucken" src="./images/Printer.gif" onClick="print();">';
          echo' <br>'; 
        
          // Alle Spiele der Mannschaft lesen  
          $sqlstr = "select DATE_FORMAT(datum,'%d.%m.%Y') datum,datum datum_sort,
                     TIME_FORMAT(datum,'%H:%i') uhrzeit,heim_id,aus_id,spieltag,heim_tore,aus_tore,gespielt
                     from tt_spiele where saison_id=$saisonid and (heim_id=$teamid or aus_id=$teamid) order by datum_sort";
          $result = GetResult($db,$sqlstr);
          
          //Mannschaftsnamen lesen 
          $sqlstr1 = "select name,id from tt_mannschaft ma, tt_zwtab_mannschaft_saison zw where 
	  	      mannschaft_id=ma.id and saison_id=$saisonid";
	  $result1 = GetResult($db,$sqlstr1);
          $teamname=array();
	  foreach ($result1 as $row)
	  {
	    	$teamname[$row["id"]] = $row["name"];
	  }
	  unset($result1);
	
        
          
          echo '<TABLE WIDTH="100%" BORDER="0" >';
          	echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Nr.</B></TD><TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Datum</B></TD><TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Heim</B></TD><TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Gast</B></TD><TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Ergebnis</B></TD></TR>';
                $idx=1;
                foreach ($result as $match)
                {
                	echo '<tr>';
                	echo '<td ALIGN="CENTER">';
                		echo $idx++;
                	echo '</td>';         

                	echo '<td ALIGN="CENTER">';
                		echo $match["datum"].' - '.$match["uhrzeit"];
                	echo '</td>';         

          		
          		echo '<td ALIGN="CENTER">';
                		echo $teamname[$match["heim_id"]];
                	echo '</td>';         
          		
          		echo '<td ALIGN="CENTER">';
                		echo $teamname[$match["aus_id"]];
			echo '</td>';                         
          		
          		echo '<td ALIGN="CENTER">';
                		if ($match["gespielt"] == "1")
                			echo $match["heim_tore"].' : '. $match["aus_tore"];
                		else
                			echo "-";
                	echo '</td></tr>';	   	
		}
       	   echo '</table>';	
       	   
	echo '<br><br>';
	echo '<TABLE WIDTH="100%" BORDER="0" ALIGN="CENTER">';
	echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD" >';
	echo '<INPUT TYPE="button" VALUE="Schliessen" onClick="window.close();">';
	echo '</TD></TR>';
	echo '</table>';
 
closeConnect($db);
?>
</BODY>
</HTML>