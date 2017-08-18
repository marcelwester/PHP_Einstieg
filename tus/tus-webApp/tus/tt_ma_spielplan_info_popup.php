<?php
include "inc.php";
sitename("tt_ma_spielplan_info_popup.php",$_SESSION["groupid"]);
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e</TITLE>

<SCRIPT LANGUAGE="JavaScript">
<!--
        function spielerInfo(spielerid,saisonid)
        {
                var url;
        <?php
                echo 'url = "tt_ma_kader_info_popup.php?action=info&personid="+spielerid+"&saisonid="+saisonid+"&PHPSESSID='.session_id().'";';
        ?>
                window.open(url,"info","width=450, height=500, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
        }
-->
</SCRIPT>

</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php
$spielid=$_REQUEST["spielid"];

focus();

function get_spieler_name( $id,$mtyp) {
	global $db,$saisonid;
	if ($mtyp == "tus") {
		$sqlstr="select name,vorname from tt_person where id=".$id;
	} else {
		$sqlstr="select name,vorname from tt_ext_spieler where id=".$id;
	}
	$result=GetResult($db,$sqlstr);
	if (isset($result)) {
		if ($mtyp =="tus") {
			return '<a href="javascript:spielerInfo('.$id.','.$saisonid.');">'.$result["0"]["vorname"]." ".$result["0"]["name"].'</a>';
		} else {
			return $result["0"]["vorname"]." ".$result["0"]["name"];
		}
	} else {
		return "-";
	}
}




// TuS Mannschaft identifizieren aus (Gast oder Heim)
$sqlstr="select bemerkung,saison_id,tus.id,heim_tore,aus_tore,gespielt,datum from tt_spiele sp,tt_tus_mannschaft tus where tus.id in (sp.aus_id,sp.heim_id) and sp.id=".$spielid;
$result=GetResult($db,$sqlstr);
$tus_teamid=$result["0"]["id"];
$saisonid=$result["0"]["saison_id"];
$heim_punkte=$result["0"]["heim_tore"];
$aus_punkte=$result["0"]["aus_tore"];
$gespielt=$result["0"]["gespielt"];
$datum=$result["0"]["datum"];
$bemerkung=$result["0"]["bemerkung"];

    		 // Spielzeit lesen
     		$sqlstr ="select liga,spielzeit from tt_saison where id=$saisonid";
     		$result = GetResult($db, $sqlstr);
     		$liga=$result[0]["liga"];
     		$spielzeit=$result[0]["spielzeit"];

     		// Lesen der Begegnung und Kategorie der Mannschaft lesen
     		$sqlstr="select ma.id,ma.name,kat from tt_mannschaft ma,tt_spiele sp where ";
     		$sqlstr.="heim_id=ma.id and sp.id=".$spielid;
     		$result=GetResult($db,$sqlstr);
		$heim=$result["0"]["name"];
		$heimid=$result["0"]["id"];
     		$kat=$result["0"]["kat"];
     		
     		$sqlstr="select ma.id,ma.name from tt_mannschaft ma,tt_spiele sp where ";
     		$sqlstr.="aus_id=ma.id and sp.id=".$spielid;
     		$result=GetResult($db,$sqlstr);
     		$aus=$result["0"]["name"];
  		$ausid=$result["0"]["id"];

     		
		// Prüfen ob die Heimmannschaft eine TuS Mannschaft ist
		$sqlstr="select id from tt_tus_mannschaft where id=".$heimid;
		$result=GetResult($db,$sqlstr);
		if (isset($result["0"]["id"])) {
			// Die Heimmannschaft ist eine TuS Mannschaft
			$heimmannschaft="tus";
		} else {
     			$heimmannschaft="nontus";
     		}

		// Prüfen ob die Gastmannschaft eine TuS Mannschaft ist
		$sqlstr="select id from tt_tus_mannschaft where id=".$ausid;
		$result=GetResult($db,$sqlstr);
		if (isset($result["0"]["id"])) {
			// Die Heimmannschaft ist eine TuS Mannschaft
			$ausmannschaft="tus";
		} else {
     			$ausmannschaft="nontus";
     		}

			 //Spielstaette lesen, wenn es ein Heimspiel ist
			 $sqlstr  = "select s.name from tt_tus_mannschaft m,spielstaette s where ";
			 $sqlstr .= "m.id=".$heimid." and ";
			 $sqlstr .= "s.id=m.spielstaette_id";
			 $result=getResult($db,$sqlstr);
			 if (isset($result)) {
			 	 $spielstaette="<br>".$result["0"]["name"];
			 	 // Pruefen, ob die Spielstaette nicht im Spiel ueberschrieben wurde
			 	 $sqlstr  = "select s.name from tt_spiele m,spielstaette s where ";
				 $sqlstr .= "m.id=".$spielid." and ";
				 $sqlstr .= "s.id=m.spielstaette_id";
				 $result=getResult($db,$sqlstr);
				 if (isset($result)) {
				 	 $spielstaette="<br>".$result["0"]["name"];
				 }
			 }


     		echo '<TABLE WIDTH="100%">';
     		echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
     		echo '<B>Spiel</B><BR>'.$liga.' - '.$spielzeit.'<br><br>'.date("d.m.Y - H:i", strtotime($datum));
     		echo $spielstaette.'</TD></TR>';
     		echo '</TABLE>';


     		echo '<center><h2><u>'.$heim.'</u>&nbsp;&nbsp;-&nbsp;&nbsp;<u>'.$aus.'</u></h2></center>';	
if ($gespielt==0) {
	echo '<center><h3>'.date("d.m.Y - H:i", strtotime($datum)).'</h3></center>';
		if (isset($bemerkung)) echo '<br>'.$bemerkung;

	  	$sqlstr = 'select distinct(id) from tt_spiele where';
	  	$sqlstr .= ' (heim_id='.$heimid.' or aus_id='.$heimid.') and ';
	  	$sqlstr .= ' (heim_id='.$ausid.' or aus_id='.$ausid.') and gespielt=1 order by datum desc';
	  	$result=GetResult($db,$sqlstr);
	  	if (isset($result)) {
			echo '<table align="CENTER" width="100%">';
			echo '<tr>';
				echo '<TD COLSPAN="6" ALIGN="CENTER" BGCOLOR="#DDDDDD"><b>Vergangene Aufeinandertreffen</b></TD>';
			
			echo '</tr>';
			echo '<tr>';
				echo '<td align="CENTER"><b>Spielzeit</b></td>';
				echo '<td align="CENTER"><b>Liga</b></td>';
				echo '<td align="CENTER"><b>Datum</b></td>';
				echo '<td align="CENTER"><b>Heim</b></td>';
				echo '<td align="CENTER"><b>Gast</b></td>';
				echo '<td align="CENTER"><b>Ergebnis</b></td>';
				//echo '<td align="CENTER"><b>Bemerkung</b></td>';
			echo '</tr>';

	  		foreach ($result as $match) {
	  			echo '<tr>';
	  			$sqlstr  = 'select datum,saison_id,spielzeit,liga,heim_id, aus_id, heim_tore, aus_tore, datum, bemerkung from ';	
	  			$sqlstr .= 'tt_spiele sp,tt_saison sa where ';
	  			$sqlstr .= 'sp.saison_id=sa.id and ';
	  			$sqlstr .= 'sp.id='.$match["id"];
	  			$result1 = GetResult($db,$sqlstr);
				
				$sqlstr = 'select spielzeit,liga from tt_saison where id='.$result1["0"]["saison_id"];
				$result2=GetResult($db,$sqlstr);
				echo '<td align="CENTER">';
					echo $result2["0"]["spielzeit"];
				echo '</td>';
				echo '<td align="CENTER">';
					echo $result2["0"]["liga"];
				echo '</td>';


				echo '<td align="CENTER">';
					//echo date("d.m.Y - H:i",strtotime($result1["0"]["datum"]));
					echo date("d.m.Y",strtotime($result1["0"]["datum"]));
					//echo $result1["0"]["datum"];
				echo '</td>';
				
				
				$sqlstr = 'select name from tt_mannschaft where id = '.$result1["0"]["heim_id"];
				$result2 = GetResult($db,$sqlstr);
				echo '<td align="CENTER">';
					echo $result2["0"]["name"];
				echo '</td>';
				
				$sqlstr = 'select name from tt_mannschaft where id = '.$result1["0"]["aus_id"];
				$result2 = GetResult($db,$sqlstr);
				echo '<td align="CENTER">';
					echo $result2["0"]["name"];
				echo '</td>';
				
				echo '<td align="CENTER">';
					echo $result1["0"]["heim_tore"];
					echo ' : ';
					echo $result1["0"]["aus_tore"];
				echo '</td>';
				

				
				echo '</tr>';
	  		}
			echo '</table>';	  		
	  			
	  	} else {
	  		echo "<br>Keine vorherigen Begegnungen der Mannschaften gefunden";
	  	}
	  	
	
        echo '<br>';
        echo '<TABLE WIDTH="100%" BORDER="0" ALIGN="CENTER">';
  	echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD" >';
  	echo '<INPUT TYPE="button" VALUE="Schliessen" onClick="window.close();">';
  	echo '</TD></TR>';
  	echo '</table>';
	closeConnect($db);
	exit;
}
		echo '<center><h2>'.$heim_punkte.'&nbsp;&nbsp;:&nbsp;&nbsp;'.$aus_punkte.'</h2></center>';	
     		echo '<br>';
     	

	// Fotos zum Spiel
    	$sql_fotos = 'select image_id, descr from sys_images where kategorie = 9 and linked = '.$spielid.' order by idx,image_id';
   		$result_fotos = getResult($db,$sql_fotos);

		if (isset($result_fotos[0]))
		{
			$buttons_in_a_row = 7;

			echo '<TABLE WIDTH="100%" BORDER="0"><TR>';
			$url = 'tt_ma_spielplan_info_popup.php?spielid='.$spielid.'&PHPSESSID='.session_id();
			if (!isset($_REQUEST["img_id"]))
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF"><B>';
			else
				echo '<TD ALIGN="CENTER" BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\''.$url.'\'">';

			echo 'Infos</B>';
			echo '</TD>';

			$numrows = ceil(count($result_fotos) / $buttons_in_a_row);

			for ($row=0; $row < $numrows; $row++)
			{
				if ($row > 0)
				{
					echo '<TR>';
					$limit = $buttons_in_a_row;
				}
				else
					$limit = $buttons_in_a_row - 1;

				for ($x=0; $x < $limit; $x++)
				{
					if ($row > 0)
						$index = $x+$row*$buttons_in_a_row -1;
					else
						$index = $x+$row*$buttons_in_a_row;
					if (isset($result_fotos[$index]))
					{
						if ($result_fotos[$index]["image_id"] == $_REQUEST["img_id"])
							echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF"><B>';
						else
						{
							$url = 'tt_ma_spielplan_info_popup.php?spielid='.$spielid.'&img_id='.$result_fotos[$index]["image_id"].'&PHPSESSID='.session_id();
							echo '<TD ALIGN="CENTER" BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\''.$url.'\'">';
						}
		
						echo ($index+1).'. Foto</B>';
						echo '</TD>';
					}
					else
						echo '<TD></TD>';
				}
				echo '</TR>';
			}
			echo '</TABLE><BR>';
		}

		if (isset($bemerkung)) echo '<br>'.$bemerkung;

		if (isset($_REQUEST["img_id"]))
		{
			echo '<TABLE WIDTH="100%" BORDER="0" >';
			echo '<TR><TD ALIGN="CENTER">';
			echo '<IMG SRC="showimage2.php?id='.$_REQUEST["img_id"].'" />';
			echo '</TD></TR>';
			echo '</TABLE>';
		}
		else
		{


     		$sqlstr  = "select spiel_nr,heim1_id,heim2_id,aus1_id,aus2_id,heim_saetze,aus_saetze from tt_spiele_saetze where ";
     		$sqlstr .= "heim_saetze>-1 and aus_saetze>-1 and spiel_id=".$spielid." order by spiel_nr";
     		$result=GetResult($db,$sqlstr);
	if (isset($result)) {
     		echo '<table width="100%" align="center">';
     			echo '<tr><td BGCOLOR="#DDDDDD" colspan="5" align="center"><b>Ergebnisse</b></td></tr>';
     			echo '<tr>';
     				echo '<td align="center"><b>Nr.</b></td>';
     				echo '<td align="center"><b>'.$heim.'</b></td>';
     				echo '<td align="center"><b>'.$aus.'</b></td>';
     				echo '<td align="center"><b>Sätze</b></td>';
     				echo '<td align="center"><b>Stand</b></td>';
     			echo '</tr>';
     			
     			$idx=1; $heim_p=0; $aus_p=0; $heim_s=0; $aus_s=0;
     			foreach ($result as $row) {
     				echo '<tr>';
     					echo '<td>'.$idx++.'</td>';
     					echo '<td align="center">';
     						echo get_spieler_name($row["heim1_id"],$heimmannschaft);
     						if ($row["heim2_id"] > 0) {
     							echo " / ".get_spieler_name($row["heim2_id"],$heimmannschaft);
     						}
     					echo '</td>';
     					echo '<td align="center">';
     						echo get_spieler_name($row["aus1_id"],$ausmannschaft);
     						if ($row["aus2_id"] > 0) {
     							echo " / ".get_spieler_name($row["aus2_id"],$ausmannschaft);
     						}
     					echo '</td>';
     					echo '<td align="center">';
     						echo $row["heim_saetze"].':'.$row["aus_saetze"];
     					echo '</td>';
     					
     					$heim_s += $row["heim_saetze"];
     					$aus_s += $row["aus_saetze"];
     					
     					if ($row["heim_saetze"] > $row["aus_saetze"]) {
     						$heim_p++;
     					} else {
     						$aus_p++;
     					}
     					echo '<td align="center">';
     						echo $heim_p.':'.$aus_p;
     					echo '</td>';
     				echo '</tr>';
     			}
     			echo '<tr><td colspan="5"><br></td></tr>';
     			echo '<tr>';
     				echo '<td colspan="2" class="none" border="0"></td>';
     				echo '<td align="right"><b>Endstand</b></td>';
				echo '<td align="center"><b>';
     					echo $heim_s.':'.$aus_s;
     				echo '</b></td>';
				echo '<td align="center"><b>';
     					echo $heim_p.':'.$aus_p;
     				echo '</b></td>';
     			echo '</tr>';
     		echo '</table>';
	}     			
     		

           // Spielbericht lesen
           $sqlstr = "select spiel_bericht,verfasser_id from tt_sp_bericht where spiel_id=$spielid";
           $result = GetResult($db,$sqlstr);
           if (isset($result["0"][spiel_bericht]))
           {
				$sqlstr="select name from sys_users where userid=".$result["0"]["verfasser_id"];
				$result1=GetResult($db,$sqlstr);

				echo '<br><br>';
				echo '<TABLE WIDTH="100%" BORDER="0" >';
				echo '<TR><TD BGCOLOR="#DDDDDD" ALIGN="CENTER"><B>SPIELBERICHT</B><br>- '.$result1["0"]["name"].' -</TD></TR>';
				echo '<tr><td>';
				echo $result["0"]["spiel_bericht"];
				echo '</tr></td>';
				echo '</table>';
           }
        }

        echo '<br>';
        echo '<TABLE WIDTH="100%" BORDER="0" ALIGN="CENTER">';
  	echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD" >';
  	echo '<INPUT TYPE="button" VALUE="Schliessen" onClick="window.close();">';
  	echo '</TD></TR>';
  	echo '</table>';

closeConnect($db);
?>
</BODY>
</HTML>