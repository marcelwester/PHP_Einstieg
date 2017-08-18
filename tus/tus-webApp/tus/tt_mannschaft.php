<?php


function getSpieler($id) {
	global $db;
	$sqlstr = "select name,vorname from tt_person where id=".$id;
	$result=getResult($db,$sqlstr);
	return ($result["0"]["name"].", ".$result["0"]["vorname"]);
}


function getDoppel ($id1,$id2,$saisonid,$tusid) {
	global $db;
	// Spiele der Saison lesen
	$sqlstr  = "select id,heim_id,aus_id from tt_spiele where ";
	$sqlstr .= "saison_id=".$saisonid." and ";
	$sqlstr .= $tusid." in (heim_id,aus_id) order by id";
	$result=getResult($db,$sqlstr);
	foreach ($result as $spiel) {
		// Prüfen, ob es ein Heim- oder Auswärtsspiel ist
		unset ($ocholt); unset($nonocholt);
		if ($spiel["heim_id"] == $tusid) {
			$ocholt="heim";
			$nonocholt="aus";
		}
		if ($spiel["aus_id"] == $tusid) {
			$ocholt="aus";
			$nonocholt="heim";
		}
		if (! isset($ocholt)) {
			echo "Heim- oder Auswärtsspiel konnte nicht zugeorndet werden !";
			exit;
		}
		// Saetze zu den Spielen lesen
		
		$doppel = $id1.",".$id2;
		
		// Anzahl der gewonnenen Spiele des Spielers lesen
		// Spiele, bei denen kein Gegnerisches Doppel  eingetragen ist, werden nicht gewertet 
		$sqlstr  = "select count(*) anz from tt_spiele_saetze where ";
		$sqlstr .= $ocholt."1_id in (".$doppel.") and ";
		$sqlstr .= $ocholt."2_id in (".$doppel.") and ";
		$sqlstr .= $ocholt."_saetze > ".$nonocholt."_saetze and ";
		$sqlstr .= $nonocholt."1_id<>0 and ".$nonocholt."2_id<>0 and ";
		$sqlstr .= "spiel_id=".$spiel["id"];
		$result1=getResult($db,$sqlstr);
		$siege += $result1["0"]["anz"];
		//echo "<br>".$sqlstr." ==> ".$result1["0"]["anz"]." ".$ocholt;

		// Anzahl der verlorenen Spiele des Spielers lesen
		$sqlstr  = "select count(*) anz from tt_spiele_saetze where ";
		$sqlstr .= $ocholt."1_id in (".$doppel.") and ";
		$sqlstr .= $ocholt."2_id in (".$doppel.") and ";
		$sqlstr .= $ocholt."_saetze < ".$nonocholt."_saetze and ";
		$sqlstr .= "spiel_id=".$spiel["id"];
		$result1=getResult($db,$sqlstr);
		$niederlagen += $result1["0"]["anz"];
	}
	//echo "$siege : $niederlagen";

	return array("siege" => $siege, "niederlagen" => $niederlagen);
	//return $siege.":".$niederlagen;
}
	

function getEinzel ($personid,$saisonid,$tusid,$pk) {
	global $db;	
	
	$siege=0;
	$niederlagen=0;
	
	// Spiele der Saison lesen
	$sqlstr  = "select id,heim_id,aus_id from tt_spiele where ";
	$sqlstr .= "saison_id=".$saisonid." and ";
	$sqlstr .= $tusid." in (heim_id,aus_id) order by id";
	$result=getResult($db,$sqlstr);
	foreach ($result as $spiel) {
		// Prüfen, ob es ein Heim- oder Auswärtsspiel ist
		unset ($ocholt); unset($nonocholt);
		if ($spiel["heim_id"] == $tusid) {
			$ocholt="heim";
			$nonocholt="aus";
		}
		if ($spiel["aus_id"] == $tusid) {
			$ocholt="aus";
			$nonocholt="heim";
		}
		if (! isset($ocholt)) {
			echo "Heim- oder Auswärtsspiel konnte nicht zugeorndet werden !";
			exit;
		}
	
		// Saetze zu den Spielen lesen
		
		// Anzahl der gewonnenen Spiele des Spielers lesen
		// Spiele, bei denen kein Gegner eingetragen ist, werden nicht gewertet
		$sqlstr  = "select count(*) anz from tt_spiele_saetze where ";
		$sqlstr .= $ocholt."1_id=".$personid." and ";
		$sqlstr .= $ocholt."_saetze > ".$nonocholt."_saetze and ";
		$sqlstr .= $nonocholt."1_id<>0 and ";
		$sqlstr .= "spiel_id=".$spiel["id"]." and ";
		$sqlstr .= "spiel_nr in (".$pk.")";
		$result1=getResult($db,$sqlstr);
		$siege += $result1["0"]["anz"];
		//echo "<br>".$sqlstr." ==> ".$result1["0"]["anz"]." ".$ocholt;

		// Anzahl der verlorenen Spiele des Spielers lesen
		$sqlstr  = "select count(*) anz from tt_spiele_saetze where ";
		$sqlstr .= $ocholt."1_id=".$personid." and ";
		$sqlstr .= $ocholt."_saetze < ".$nonocholt."_saetze and ";
		$sqlstr .= "spiel_id=".$spiel["id"]." and ";
		$sqlstr .= "spiel_nr in (".$pk.")";
		$result1=getResult($db,$sqlstr);
		$niederlagen += $result1["0"]["anz"];
	}
	return array("siege" => $siege, "niederlagen" => $niederlagen);
}

sitename("tt_mannschaft.php",$_SESSION["groupid"]);

        $sql = 'select * from tt_tus_mannschaft where id = '.$_REQUEST["teamid"];
        $result = GetResult($db, $sql);

        $teamid = $_REQUEST["teamid"];
        $teamname = $result[0]["name"];
        $bild_id = $result[0]["bild"];

		if (strlen($_REQUEST["saisonid"]) > 0)
		{
			$saisonid = $_REQUEST["saisonid"];
		}
		else
		{
			$sql = 'select max(datum) from tt_spiele where heim_id = '.$teamid.' or aus_id = '.$teamid;
			$result_max_date = getResult($db, $sql);
			$sql = 'select saison_id from tt_spiele where datum = "'.$result_max_date[0]["max(datum)"].'" and (heim_id = '.$teamid.' or aus_id = '.$teamid.')';
			$result2 = getResult($db, $sql);
			$saisonid = $result2[0]["saison_id"];
			if (strlen($saisonid) == 0)
			{
				$sql = 'select max(saison_id) from tt_zwtab_mannschaft_saison where mannschaft_id  = '.$teamid;
				$result2 = getResult($db, $sql);
				$saisonid = $result2[0]["max(saison_id)"];
			}
		}

        $sql = 'select * from tt_saison where id = '.$saisonid;
        $result_saison = GetResult($db, $sql);
		$closed = $result_saison["0"]["closed"];
		
		// Falls image_id in fb_saisonimage ungleich 0 ist, wird das foto anstelle von $bild_id genommen
			$sqlstr  = "select image_id from tt_saison_image where ";
			$sqlstr .= "saison_id=".$saisonid." and ";
			$sqlstr .= "team_id=".$teamid;
			$result_image=getResult($db,$sqlstr);
			if (isset($result_image) && $result_image["0"]["image_id"]!=0) {
				$bild_id=$result_image["0"]["image_id"];
			}
			//echo $bild_id;
			unset($result_image);

		$submenu = array();
		$submenu["start"] = array("id" => 'start', "title" => 'Startseite', "link" => 'index.php?site=tt_team&action=start&teamid='.$teamid.'&saisonid='.$saisonid.'&ttmenu=yes&PHPSESSID='.session_id());
		if ($result_saison[0]["tabelle"] == 1)
			$submenu["table"] = array("id" => 'table', "title" => 'Tabelle', "link" => 'index.php?site=tt_team&action=table&teamid='.$teamid.'&saisonid='.$saisonid.'&ttmenu=yes&PHPSESSID='.session_id());
		$submenu["spiele"] = array("id" => 'spiele', "title" => 'Spielplan', "link" => 'index.php?site=tt_team&action=spiele&teamid='.$teamid.'&saisonid='.$saisonid.'&ttmenu=yes&PHPSESSID='.session_id());
		$submenu["kader"] = array("id" => 'kader', "title" => 'Aufstellung', "link" => 'index.php?site=tt_team&action=kader&teamid='.$teamid.'&saisonid='.$saisonid.'&ttmenu=yes&PHPSESSID='.session_id());
		if ($result_saison[0]["stats"] == 1)
			$submenu["stats"] = array("id" => 'stats', "title" => 'Statistiken', "link" => 'index.php?site=tt_team&action=stats&teamid='.$teamid.'&saisonid='.$saisonid.'&ttmenu=yes&PHPSESSID='.session_id());
		$submenu["kontakt"] = array("id" => 'kontakt', "title" => 'Kontakt', "link" => 'index.php?site=tt_team&action=kontakt&teamid='.$teamid.'&saisonid='.$saisonid.'&ttmenu=yes&PHPSESSID='.session_id());
echo '<table class="none">';
echo '<tr><td align="center" class="none">Mannschaft :</center></td>';
echo '<td align="center" class="none">Saison :</center></td></tr>';

echo '<tr>';
echo '<td class="none" align="center">';
//		Mannschaftsauswahl
		$url = 'index.php?site=tt_team&action=start&PHPSESSID='.session_id();
		echo '<SELECT onChange="window.location.href=\''.$url.'&ttmenu=yes&teamid=\'+this.value;">';
		$sql = 'select * from tt_tus_mannschaft where show_menu=1 order by reihenfolge asc';
		$result = GetResult($db, $sql);
	    foreach ($result as $team)
	    {
	    	if ($team["id"] == $teamid)
	    		echo '<OPTION SELECTED VALUE="'.$team["id"].'">'.$team["name"].'</OPTION>';
	    	else
	    		echo '<OPTION VALUE="'.$team["id"].'">'.$team["name"].'</OPTION>';
	    }
        echo '</SELECT>';
echo '</td>';
echo '<td class="none" align="center">';

//		Saisonauswahl
        $url = 'index.php?site=tt_team&action='.$action.'&teamid='.$teamid.'&PHPSESSID='.session_id();
        echo '<SELECT onChange="window.location.href=\''.$url.'&ttmenu=yes&saisonid=\'+this.value;">';
	        $sql = 'select saison_id from tt_zwtab_mannschaft_saison where mannschaft_id = '.$_REQUEST["teamid"];
    	    $result = GetResult($db, $sql);

    	    foreach ($result as $saison)
    	    {
    	    	$sql2 = 'select * from tt_saison where id = '.$saison["saison_id"];
    	    	$result2 = getResult($db, $sql2);
    	    	if ($result2[0]["id"] == $saisonid)
    	    		echo '<OPTION SELECTED VALUE="'.$saison["saison_id"].'">'.$result2[0]["spielzeit"].' ('.$result2[0]["liga"].')</OPTION>';
    	    	else
    	    		echo '<OPTION VALUE="'.$saison["saison_id"].'">'.$result2[0]["spielzeit"].' ('.$result2[0]["liga"].')</OPTION>';
    	    }
        echo '</SELECT>';
echo '</td>';
echo '</tr>';
echo '</table>'; 
        echo '<BR>';

        echo '<TABLE WIDTH="100%" BORDER="0">';
                echo '<TR>';
                        foreach($submenu as $sm_row)
                        {
                                if ($sm_row["id"] == $action)
                                {
                                        echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                                echo '<B>'.$sm_row["title"].'</B>';
                                        echo '</TD>';
                                }
                                else
                                {
                                        echo '<TD ALIGN="CENTER" BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\''.$sm_row["link"].'\'">';
                                                echo $sm_row["title"];
                                        echo '</TD>';
                                }

                        }
                echo '</TR>';
        echo '</TABLE>';
        echo '<TABLE WIDTH="100%" BORDER="0">';
                echo '<TR>';
                        echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF"><BR><BR>';

switch($action)
{
        case 'start':

				$sql = 'select datum, heim_id, aus_id from tt_spiele ';
				$sql .= 'where datum > now() and gespielt = 0 and (heim_id = '.$teamid.' or aus_id = '.$teamid.') ';
				$sql .= 'order by datum asc limit 1';
				$result = getResult($db,$sql);

				if (isset($result[0]))
				{
					$teams = array();
					$sql = 'select id, name from tt_mannschaft';
					$result1 = getResult($db,$sql);
					foreach($result1 as $team)
					if ($team["id"] == $teamid)
						$teams[$team["id"]] = '<b>'.$team["name"].'</b>';
					else
						$teams[$team["id"]] = $team["name"];

					$laufschrift  = '<br><marquee scrollamount="2" scrolldelay="3">';
					$laufschrift .= '+ + + <B>nächstes Spiel : ';
					$laufschrift .= $teams[$result[0]["heim_id"]].' - '.$teams[$result[0]["aus_id"]].' ('.date("d.m.Y - H:i", strtotime($result[0]["datum"])).')';
					$laufschrift .= '</B> + + +';
					$laufschrift .= '</marquee><BR>';
					echo $laufschrift;
				}

                if ($bild_id != 0) {
                        $datum = getResult($db,"select datum from sys_images where image_id=".$bild_id);
                        echo '<center><IMG SRC="showimage2.php?id='.$bild_id.'" /></center>';
						echo '<center>Foto vom '.date("d.m.Y",strtotime($datum["0"]["datum"])).'</center>';               
            	}
               
       	if ($closed == 0) {
	       	$sqlstr="select name,email,sys_users.userid from sys_user_tus,sys_users where 
			         tt_teamid=$teamid and sys_user_tus.userid=sys_users.userid";
			$result=GetResult($db,$sqlstr);
			if (isset($result))
			{
				echo "<br>";
				echo "<br><b>Die Inhalte der $teamname werden gepflegt von:</b>";
				foreach ($result as $user) 
				{
					$sqlstr  = "select count(*) anz from sys_user_tus,sys_rights where ";
					$sqlstr .= "userid=".$user["userid"]." and ";
					$sqlstr .= "rightid = sys_rights.id and";
					$sqlstr .= "(name = 'spiele_add' or ";
					$sqlstr .= "name = 'spiele_del' or ";
					$sqlstr .= "name = 'spiele_edit' or ";
					$sqlstr .= "name = 'kader' or ";
					$sqlstr .= "name = 'kontakt')";
					$result1=GetResult($db,$sqlstr);
					if ($result1["0"]["anz"] != "0") 
						echo  '<br><a><A HREF="mailto:'.$user["email"].'">'.$user["name"].'</a>';
					
				}
			}
		}

               
               
                break;
        case 'table':
				include "tt_ma_tabelle.php";
                break;

        case 'spiele':

			/// Spiel löschen
				if ($_REQUEST["do"] == del && priv("spiele_del"))
				{
					$spielid = $_REQUEST["spielid"];

					$sql = 'DELETE from tt_sp_bericht where spiel_id = '.$spielid;
					$result = doSQL($db, $sql);

					$sql = 'DELETE from tt_spiele_saetze where spiel_id = '.$spielid;
					$result = doSQL($db, $sql);
					
					$sql = 'DELETE from tt_zw_ext_spieler_tt_spiele where spiel_id = '.$spielid;
					$result = doSQL($db, $sql);
					$sql = 'DELETE from tt_zw_tt_person_tt_spiele where spiel_id = '.$spielid;
					$result = doSQL($db, $sql);

					$sql = 'DELETE from tt_spiele where id = '.$spielid;
					$result = doSQL($db, $sql);

					$sql = 'update sys_images set linked=0 where linked='.$spielid.' and kategorie=9';
					$result = doSQL($db, $sql);

					if ($result["code"] == 0)
					{
						echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.history.back();"><CENTER>Das Spiel wurde erfolgreich entfernt !</CENTER></A>';
						echo '<SCRIPT TYPE="text/javascript">';
							echo 'setTimeout("window.history.back();",1000);';
						echo '</SCRIPT>';
					}
					else
						echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.history.back()"><CENTER>Es ist ein Fehler beim Löschen des Spiels aufgetreten !</CENTER></A>';
				}
				else
				{
				
			         	if ($saisonid == "")
                                   		exit;
                  			include "tt_ma_spielplan.php";
				}
                break;

        case 'kader':
        		include "tt_ma_kader.php";
                break;
        case 'stats':

                if ($saisonid == "")
                  exit;

		?>
		<SCRIPT LANGUAGE="JavaScript">
		<!--
    		function info(spielerid,saisonid)
        	{
                	var url;
        	<?php
                	echo 'url = "tt_ma_kader_info_popup.php?action=info&personid="+spielerid+"&saisonid="+saisonid+"&PHPSESSID='.session_id().'";';
        	?>
                	window.open(url,"info","width=450, height=500, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
        	}
		
    		function info_spiele(spielerid)
        	{
                	var url;
        	<?php
                	echo 'url = "tt_saetze_popup.php?&personid="+spielerid+"&saisonid='.$saisonid.'&tusid='.$teamid.'&PHPSESSID='.session_id().'";';
        	?>
                	window.open(url,"info","width=700, height=500, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
        	}
		-->
		</SCRIPT>
		<?php
                
		
		// Statistiken der Spielsysteme
		$sqlstr = "select spielsystem from tt_saison where id=".$saisonid;
		$result=GetResult($db,$sqlstr);
		$spielsystem=$result["0"]["spielsystem"];

		switch ($spielsystem) {
			case "1":
				include "tt_statistik_spielsystem_1.php";             
			break;
			
			case "2":
				include "tt_statistik_spielsystem_2.php";             
			break;

			case "3":
				include "tt_statistik_spielsystem_3.php";             
			break;

			case "4":
				include "tt_statistik_spielsystem_4.php";             
			break;
		}
               
         break;
        case 'kontakt':
        	include "tt_ma_kontakt.php";
	break;
}
                                echo '<BR>&nbsp;';
                        echo '</TD>';
                echo '</TR>';
        echo '</TABLE>';
?>