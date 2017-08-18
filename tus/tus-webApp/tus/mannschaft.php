<?php

sitename("mannschaft.php",$_SESSION["groupid"]);
        $teamid = $_REQUEST["teamid"];
		if (strlen($_REQUEST["saisonid"]) > 0)
		{
			$saisonid = $_REQUEST["saisonid"];
		}
		else
		{
			$sql = 'select max(datum) from fb_spiele where heim_id = '.$teamid.' or aus_id = '.$teamid;
			$result_max_date = getResult($db, $sql);
			$sql = 'select saison_id from fb_spiele where datum = "'.$result_max_date[0]["max(datum)"].'" and (heim_id = '.$teamid.' or aus_id = '.$teamid.')';
			$result2 = getResult($db, $sql);
			$saisonid = $result2[0]["saison_id"];
			if (strlen($saisonid) == 0)
			{
				$sql = 'select max(saison_id) from fb_zwtab_mannschaft_saison where mannschaft_id  = '.$teamid;
				$result2 = getResult($db, $sql);
				$saisonid = $result2[0]["max(saison_id)"];
			}
		}


?>

<SCRIPT LANGUAGE="JavaScript">
<!--
	function fbv_saison_image_popup(saisonid,teamid )
	{
		var url;
<?php
		echo 'url = "fbv_saison_image_popup.php?action=edit&saisonid="+saisonid+"&teamid="+teamid+"&PHPSESSID='.session_id().'";';
?>
		var heigth = 250;
		window.open(url,"saison","width=600, height="+heigth+", top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
	}

-->
</SCRIPT>

<?php

        $sql = 'select * from fb_tus_mannschaft where id = '.$_REQUEST["teamid"];
        $result = GetResult($db, $sql);

        $teamname = $result[0]["name"];
        $bild_id = $result[0]["bild"];

        $sql  = 'select * from fb_saison fb ,fb_zwtab_mannschaft_saison zw where id = '.$saisonid;
        $sql .= ' and saison_id=id ';
        $sql .= ' and mannschaft_id='.$teamid;
        $result_saison = GetResult($db, $sql);
		$closed = $result_saison["0"]["closed"];
		
		// Falls image_id in fb_saison_image ungleich 0 ist wird das foto anstelle von $bild_id genommen
			$sqlstr  = "select image_id from fb_saison_image where ";
			$sqlstr .= "saison_id=".$saisonid." and ";
			$sqlstr .= "team_id=".$teamid;
			$result_image=getResult($db,$sqlstr);
			if (isset($result_image) && $result_image["0"]["image_id"]!=0) {
				$bild_id=$result_image["0"]["image_id"];
			}
//			echo $bild_id;
			unset($result_image);

		$submenu = array();
		$submenu["start"] = array("id" => 'start', "title" => 'Startseite', "link" => 'index.php?site=team&action=start&teamid='.$teamid.'&saisonid='.$saisonid.'&fbmenu=yes&PHPSESSID='.session_id());
		if ($result_saison[0]["tabelle"] == 1)
			$submenu["table"] = array("id" => 'table', "title" => 'Tabelle', "link" => 'index.php?site=team&action=table&teamid='.$teamid.'&saisonid='.$saisonid.'&fbmenu=yes&PHPSESSID='.session_id());
		$submenu["spiele"] = array("id" => 'spiele', "title" => 'Spielplan', "link" => 'index.php?site=team&action=spiele&teamid='.$teamid.'&saisonid='.$saisonid.'&fbmenu=yes&PHPSESSID='.session_id());
		$submenu["kader"] = array("id" => 'kader', "title" => 'Kader', "link" => 'index.php?site=team&action=kader&teamid='.$teamid.'&saisonid='.$saisonid.'&fbmenu=yes&PHPSESSID='.session_id());
		if ($result_saison[0]["stats"] == 1)
			$submenu["stats"] = array("id" => 'stats', "title" => 'Statistiken', "link" => 'index.php?site=team&action=stats&teamid='.$teamid.'&saisonid='.$saisonid.'&fbmenu=yes&PHPSESSID='.session_id());
		$submenu["kontakt"] = array("id" => 'kontakt', "title" => 'Kontakt', "link" => 'index.php?site=team&action=kontakt&teamid='.$teamid.'&saisonid='.$saisonid.'&fbmenu=yes&PHPSESSID='.session_id());

echo '<table class="none">';
echo '<tr><td align="center" class="none">Mannschaft :</center></td>';
echo '<td align="center" class="none">Saison :</center></td></tr>';

echo '<tr>';
echo '<td class="none" align="center">';
//		Mannschaftsauswahl
		$url = 'index.php?site=team&action=start&PHPSESSID='.session_id();
		echo '<SELECT onChange="window.location.href=\''.$url.'&fbmenu=yes&teamid=\'+this.value;">';
		$sql = 'select * from fb_tus_mannschaft where show_menu=1 order by reihenfolge asc';
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
        $url = 'index.php?site=team&action='.$action.'&teamid='.$teamid.'&PHPSESSID='.session_id();
        echo '<SELECT onChange="window.location.href=\''.$url.'&fbmenu=yes&saisonid=\'+this.value;">';
	        $sql = 'select saison_id from fb_zwtab_mannschaft_saison where mannschaft_id = '.$_REQUEST["teamid"];
    	    $result = GetResult($db, $sql);

    	    foreach ($result as $saison)
    	    {
    	    	$sql2 = 'select * from fb_saison where id = '.$saison["saison_id"];
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


				$sql = 'select datum, heim_id, aus_id from fb_spiele ';
				$sql .= 'where datum > now() and gespielt = 0 and (heim_id = '.$teamid.' or aus_id = '.$teamid.') ';
				$sql .= 'order by datum asc limit 1';
				$result = getResult($db,$sql);

			    if (priv("fb_saison_image") && priv_team($teamid)) {
					echo '<br>';
						echo '<center><a href="javascript:fbv_saison_image_popup('.$saisonid.','.$teamid.');">';
						echo '<B>Mannschaftsfoto auswählen</B>';
						echo '</a></center>';
					echo '<br>';
				 }


				if (isset($result[0]))
				{
					$teams = array();
					$sql = 'select id, name from fb_mannschaft';
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
			    		 fb_teamid=$teamid and sys_user_tus.userid=sys_users.userid and sys_users.disable=0";
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
				include "ma_tabelle.php";
                break;

        case 'spiele':

			/// Spiel löschen
				if ($_REQUEST["do"] == del && priv("spiele_del"))
				{
					$spielid = $_REQUEST["spielid"];

					$sql = 'DELETE from fb_sp_bericht where spiel_id = '.$spielid;
					$result = doSQL($db, $sql);

					$sql = 'DELETE from fb_tore where spiel_id = '.$spielid;
					$result = doSQL($db, $sql);

					$sql = 'DELETE from fb_spiele where id = '.$spielid;
					$result = doSQL($db, $sql);

					$sql = 'update sys_images set linked=0 where linked='.$spielid.' and kategorie=3';
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
                  			include "ma_spielplan.php";
				}
                break;

        case 'kader':
        		include "ma_kader.php";
                break;
        case 'stats':
		?>
		<SCRIPT LANGUAGE="JavaScript">
		<!--
    		function info(spielerid,saisonid)
        	{
                	var url;
        	<?php
                	echo 'url = "ma_kader_info_popup.php?action=info&personid="+spielerid+"&saisonid="+saisonid+"&PHPSESSID='.session_id().'";';
        	?>
                	window.open(url,"info","width=450, height=500, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
        	}
		-->
		</SCRIPT>
		<?php
                
                if ($saisonid == "")
                  exit;
             
			
				
					               
               // Statistiken
               $sqlstr1  = " select person.id,person.name,person.vorname,count(*) tore from fb_tore tore, fb_spiele spiele,fb_person person,fb_zwtab_person_typ_tus_mannschaft f where ";
               $sqlstr1 .= " tore.spiel_id=spiele.id and spiele.saison_id=$saisonid and tore.spieler_id=person.id and person.id>0 ";
               $sqlstr1 .= " and f.saison_id=spiele.saison_id and person.id=f.person_id ";
               $sqlstr1 .= " and f.persontyp_id in (2,4) and f.tus_mannschaft_id=".$_REQUEST["teamid"];
               $sqlstr1 .= " group by person.id order by tore desc,name";
							
               $result1 = GetResult($db,$sqlstr1);
                          
               
               // Assists
               $sqlstr2  = " select person.id,person.name,person.vorname,count(*) assist from fb_tore tore, fb_spiele spiele,fb_person person,fb_zwtab_person_typ_tus_mannschaft f where ";
               $sqlstr2 .= " tore.spiel_id=spiele.id and spiele.saison_id=$saisonid and tore.assist_id=person.id and person.id>0 ";
               $sqlstr2 .= " and f.saison_id=spiele.saison_id and person.id=f.person_id ";
               $sqlstr2 .= " and f.persontyp_id in (2,4) and f.tus_mannschaft_id=".$_REQUEST["teamid"];
               $sqlstr2 .= " group by person.id order by assist desc,name";
            
               $result2 = GetResult($db,$sqlstr2);
               
                              
               echo '<CENTER><TABLE WIDTH="40%" BORDER="0">';
                 echo '<TR>';
                 echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                 echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                 echo '<b>Torschützen</b>';
                 echo '</TD>';
                 echo '</TR>';
               echo '</TABLE></CENTER><BR>';
               
               if (isset($result1)) 
               {
                 echo '<CENTER><TABLE WIDTH="70%" BORDER="0">';
                   echo '<TR>';
                   echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                   echo '<B>Name</B>';
                   echo '</TD>';
                   echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                   echo '<B>INFO</B>';
                   echo '</TD>';
                   echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                   echo '<B>Anzahl</B>';
                   echo '</TD>';
                   echo '</TR>';
                   foreach ($result1 as $tor)
                   {
                      echo '<TR><TD ALIGN="CENTER">';
                       echo $tor["vorname"].' '.$tor["name"];
                      echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                        echo '<a href="javascript:info('.$tor["id"].','.$saisonid.');">';
                         echo '<IMG SRC="images/info.gif" BORDER="0" ALT="Info">';
                        echo '</a>';
                      echo '</TD>';
                      echo '<TD ALIGN="CENTER">';
                       echo $tor["tore"];
                      echo '</TD></TR>';
                   }
                 echo '</TABLE>';  
               }
               echo '<BR><BR><BR><BR>';
                
                
               echo '<CENTER><TABLE WIDTH="40%" BORDER="0">';
                 echo '<TR>';
                 echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                 echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                 echo '<b>Vorlagengeber</b>';
                 echo '</TD>';
                 echo '</TR>';
               echo '</TABLE></CENTER><BR>';
               
               if (isset($result2)) 
               {
                 echo '<CENTER><TABLE WIDTH="70%" BORDER="0">';
                   echo '<TR>';
                   echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                   echo '<B>Name</B>';
                   echo '</TD>';
                   echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                   echo '<B>INFO</B>';
                   echo '</TD>';
                   echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                   echo '<B>Anzahl</B>';
                   echo '</TD>';
                   foreach ($result2 as $assist)
                   {
                      echo '<TR><TD ALIGN="CENTER">';
                       echo $assist["vorname"].' '.$assist["name"];
                      echo '</TD>';
                      echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                         echo '<a href="javascript:info('.$assist["id"].','.$saisonid.');">';
                         echo '<IMG SRC="images/info.gif" BORDER="0" ALT="Info">';
                        echo '</a>';
                      echo '</TD>';
                     
                      echo '<TD ALIGN="CENTER">';
                       echo $assist["assist"];
                      echo '</TD></TR>';
                   }
                 echo '</TABLE>';  
                }

				// Spielstatistik
	     		// Wird nur angezeigt, wenn zu jedem gespielten Spiel auch 
	     		// Einträge in fb_zwtab_spiele_person vorhanden sind 
				$sqlstr="select id from fb_spiele where " .
						"saison_id=".$saisonid." and " .
						"gespielt=1 and " .
						$teamid." in (heim_id,aus_id)";
				$result=getResult($db,$sqlstr); 
				$playerstats=1;
				if (isset($result)) {
					foreach ($result as $spiel) {
						$sqlstr = "select count(*) anz from fb_zwtab_spiele_person where ";
						$sqlstr.= "team_id=".$teamid." and ";
						$sqlstr.= "spiel_id=".$spiel["id"];
						$result1=getResult($db,$sqlstr);
						if ($result1["0"]["anz"]=="0") {
							$playerstats=0;
							break;
						
						}	
					}
				} else {
					$playerstats=0;
				}
				if ($playerstats==1) {
	               echo '<br><br><br><br>';
	               echo '<CENTER><TABLE WIDTH="40%" BORDER="0">';
	                 echo '<TR>';
	                 echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                 echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                 echo '<b>Spiel-Statistik</b>';
	                 echo '</TD>';
	                 echo '</TR>';
	               echo '</TABLE></CENTER><BR>';
				  include "fb_player_stats.php";	              
	              echo "<br><br>";
				}



         break;
        case 'kontakt':
        	include "ma_kontakt.php";
	break;
}
                                echo '<BR>&nbsp;';
                        echo '</TD>';
                echo '</TR>';
        echo '</TABLE>';
?>