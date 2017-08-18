<?php

////
//// ttv_saison_popup.php
////
//// letzte Änderung : Volker, 09.04.2007 
//// was : Anzeige von externen Seiten (Spielplan, Tabelle, Statistiken)
////
//// letzte Änderung : Volker, 22.10.2004 
//// was : Spielsystem 
////
//// letzte Änderung : Volker, 27.02.2004 
//// was : Kategorie als Popup
////
//// letzte Änderung : Daniel, 23.02.2004 19:52
//// was : Auswahl, ob Tabelle für eine Saison angezeigt werden soll, oder nicht
////
//// Änderung : Daniel, 17.02.2004 16:35
//// was : Kategorie eingebaut (steht von anfang an fest)
////
//// Änderung : Daniel, 14.02.2004 18:18
//// was : Datei erstellt

include "inc.php";
sitename("ttv_saison_popup.php",$_SESSION["groupid"]);
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e - Saisonverwaltung</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">
<SCRIPT LANGUAGE="JavaScript">
<!--
        function viewImage(imageid)
        {
           if (imageid==0)
              var imgId = document.getElementsByName("foto")[0].value;
           else
              imgId = imageid;

           if (imgId == 0)
                    alert ('Bitte ein Bild zum Anzeigen auswählen !');
            else
                    window.open("showimage.php?id="+imgId,"viewImage","width=650, height=550, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
        }
-->
</SCRIPT>




<?php
focus();
if (priv("tt_saison"))
{
switch ($_REQUEST["action"])
{
	case 'add':
		echo '<BR><FORM METHOD="POST" ACTION="ttv_saison_popup.php?action=save&PHPSESSID='.session_id().'">';
		echo '<TABLE WIDTH="100%" BORDER="0">';
			echo '<TR>';
				echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Neue Saison anlegen</B>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Kategorie<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
				        $sqlstr = "select * from tt_mannschaft_kat order by name";
				        $result = GetResult($db,$sqlstr);
				        build_select($result,name,kategorie,kat);
				      	unset($result);
					//echo '<INPUT TYPE="TEXT" SIZE="10" NAME="kat" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Spielsystem<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
				        $sqlstr = "select id,name from tt_spielsystem order by name";
				        $result = GetResult($db,$sqlstr);
				        build_select($result,name,id,spielsystem);
				      	unset($result);
					//echo '<INPUT TYPE="TEXT" SIZE="10" NAME="kat" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Spielzeit<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="spielzeit" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Liga<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="liga" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Startdatum<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="12" NAME="startdate" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Tabelle anzeigen<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="CHECKBOX" NAME="tabelle" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Statistiken anzeigen<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="CHECKBOX" NAME="stats" />';
				echo '</TD>';
			echo '</TR>';

			// Anzeigen von fremden urls

			echo '<TR>';
				echo '<TD ALIGN="center" BGCOLOR="#DDDDDD" colspan="2">';
					echo '<B>Anzeigen von fremden url\'s<B>';
				echo '</TD>';
			echo '</TR>';

/*			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>externer Spielplan<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="CHECKBOX" NAME="ext_spielplan" />';
					echo '&nbsp;&nbsp;';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="ext_spielplan_url" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>externe Statistiken<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="CHECKBOX" NAME="ext_statistik" />';
					echo '&nbsp;&nbsp;';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="ext_statistik_url" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
*/


			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>externe Tabelle <B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="CHECKBOX" NAME="ext_tabelle" />';
					echo '&nbsp;&nbsp;';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="ext_tabelle_url" VALUE="" />';
				echo '</TD>';
			echo '</TR>';







			echo '<TR>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" COLSPAN="2">';
					echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
					echo '&nbsp;&nbsp;<INPUT TYPE="submit" VALUE="Anlegen">';
				echo '</TD>';
			echo '</TR>';
		echo '</TABLE>';
		echo '</FORM>';
		break;
	case 'edit':
		$sql = 'select * from tt_saison where id = '.$_REQUEST["saisonid"];
		$result = getResult($db, $sql);
		$saison = $result[0];

		echo '<BR><FORM METHOD="POST" ACTION="ttv_saison_popup.php?action=save&PHPSESSID='.session_id().'">';

		echo '<INPUT TYPE="HIDDEN" NAME="id" VALUE="'.$_REQUEST["saisonid"].'" />';

		echo '<TABLE WIDTH="100%" BORDER="0">';
			echo '<TR>';
				echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Saison bearbeiten</B>';
				echo '</TD>';
			echo '</TR>';
		
			if ($saison["closed"]=="1") {
				$edit='"disabled"';
				echo '<tr>';
					echo '<td align="center" colspan="2">';
						echo '<b>Saison wurde am '.$saison["closed_date"].' geschlossen !</b>'; 
					echo '</td>';
				echo '</tr>';
			} else {
				$edit="";
				$edit1="";
			}
		
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Kategorie<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="10" NAME="kat" VALUE="'.$saison["kat"].'" READONLY '.$edit.'/>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Saison schliessen</b><br>Es sind keine weiteren Eingaben mehr möglich';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="checkbox" SIZE="10" NAME="closed" VALUE="1"'.$edit.'/>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Spielsystem<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
				        if ($edit=="disabled") {
				        	echo $saison["spielsystem"];
				        } else {	
					        $sqlstr = "select id,name from tt_spielsystem order by name";
					        $result_spielsystem = GetResult($db,$sqlstr);
					        build_select($result_spielsystem,name,id,spielsystem,"",1,$saison["spielsystem"]);
					      	unset($result_spielsystem);
					    }
				echo '</TD>';
			echo '</TR>';
			
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Spielzeit<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="spielzeit" VALUE="'.$saison["spielzeit"].'" '.$edit.' />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Liga<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="liga" VALUE="'.$saison["liga"].'" '.$edit.' />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Startdatum<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="12" NAME="startdate" VALUE="'.date("d.m.Y", strtotime($saison["startdatum"])).'" '.$edit.'/>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Tabelle anzeigen ?<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					if ($saison["tabelle"] == 1)
						echo '<INPUT CHECKED TYPE="CHECKBOX" NAME="tabelle" '.$edit.'/>';
					else
						echo '<INPUT TYPE="CHECKBOX" NAME="tabelle" '.$edit.'/>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Statistiken anzeigen ?<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					if ($saison["stats"] == 1)
						echo '<INPUT CHECKED TYPE="CHECKBOX" NAME="stats" '.$edit.'/>';
					else
						echo '<INPUT TYPE="CHECKBOX" NAME="stats" '.$edit.'/>';
				echo '</TD>';
			echo '</TR>';
			// Anzeigen von fremden urls
			echo '<TR>';
				echo '<TD ALIGN="center" BGCOLOR="#DDDDDD" colspan="2">';
					echo '<B>Anzeigen von fremden url\'s<B>';
				echo '</TD>';
			echo '</TR>';

/*
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>externer Spielplan<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					if ($saison["ext_spielplan"] == 1)
						echo '<INPUT checked TYPE="CHECKBOX" NAME="ext_spielplan" '.$edit.'/>';
					else 
						echo '<INPUT TYPE="CHECKBOX" NAME="ext_spielplan" '.$edit.'/>';
					echo '&nbsp;&nbsp;';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="ext_spielplan_url" VALUE="'.$saison["ext_spielplan_url"].'" '.$edit.'/>';
				echo '</TD>';
			echo '</TR>';

			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>externe Statistiken<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					if ($saison["ext_statistik"] == 1)
						echo '<INPUT checked TYPE="CHECKBOX" NAME="ext_statistik" '.$edit.'/>';
					else 
						echo '<INPUT TYPE="CHECKBOX" NAME="ext_statistik" '.$edit.'/>';
					echo '&nbsp;&nbsp;';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="ext_statistik_url" VALUE="'.$saison["ext_statistik_url"].'" '.$edit.'/>';
				echo '</TD>';
			echo '</TR>';
*/
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>externe Tabelle <B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					if ($saison["ext_tabelle"] == 1)
						echo '<INPUT checked TYPE="CHECKBOX" NAME="ext_tabelle" '.$edit.'/>';
					else
						echo '<INPUT TYPE="CHECKBOX" NAME="ext_tabelle" '.$edit.'/>';
					echo '&nbsp;&nbsp;';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="ext_tabelle_url" VALUE="'.$saison["ext_tabelle_url"].'" '.$edit.'/>';
				echo '</TD>';
			echo '</TR>';
			
			echo '<TR>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" COLSPAN="2">';
					echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
					echo '&nbsp;&nbsp;<INPUT TYPE="submit" VALUE="Ändern" '.$edit.'>';
				echo '</TD>';
			echo '</TR>';
		echo '</TABLE>';
		echo '</FORM>';
		break;
	case 'save':

		if (isset($_POST["id"]))
			$id = $_POST["id"];

		if (isset($_POST["tabelle"]))
			$tabelle = 1;
		else
			$tabelle = 0;

		if (isset($_POST["stats"]))
			$stats = 1;
		else
			$stats = 0;

		//$old_foto = $_POST["foto_old"];
		//$foto = $_POST["foto"];

			$ext_spielplan_url=$_POST["ext_spielplan_url"];
			$ext_tabelle_url=$_POST["ext_tabelle_url"];
			$ext_statistik_url=$_POST["ext_statistik_url"];

			if (isset($_POST["ext_spielplan"])) $ext_spielplan="1"; else $ext_spielplan="0";
			if (isset($_POST["ext_tabelle"])) $ext_tabelle="1"; else $ext_tabelle="0";
			if (isset($_POST["ext_statistik"])) $ext_statistik="1"; else $ext_statistik="0";



		if (strlen($_POST["kat"]) == 1 && strlen($_POST["spielzeit"]) != 0 && strlen($_POST["liga"]) != 0 && strlen($_POST["startdate"]) != 0)
		{
			$kat = $_POST["kat"];
			$spielsystem = $_POST["spielsystem"];
			$spielzeit = $_POST["spielzeit"];
			$liga = $_POST["liga"];
			$startdate = ts2db($_POST["startdate"]);
	
			if (!isset($id))	// neu anlegen
			{
				$sqlstr ="insert into tt_saison (";
				$sqlstr.="spielzeit, ";
				$sqlstr.="kat, ";
				$sqlstr.="liga, ";
				$sqlstr.="startdatum, ";
				$sqlstr.="tabelle, ";
				$sqlstr.="image_id, ";
				$sqlstr.="stats, ";
				$sqlstr.="spielsystem,";
				$sqlstr.="ext_spielplan,";
				$sqlstr.="ext_spielplan_url,";
				$sqlstr.="ext_tabelle,";
				$sqlstr.="ext_tabelle_url,";
				$sqlstr.="ext_statistik,";
				$sqlstr.="ext_statistik_url";
				
				$sqlstr.=" ) values (";
				
				$sqlstr.="'".$spielzeit."',"; 
				$sqlstr.="'".$kat."',";
				$sqlstr.="'".$liga."',";
				$sqlstr.="'".$startdate."',";
				$sqlstr.=$tabelle.",";
				$sqlstr.="0,";
				$sqlstr.=$stats.",";
				$sqlstr.=$spielsystem.",";
				$sqlstr.=$ext_spielplan.",";
				$sqlstr.="'".$ext_spielplan_url."',";
				$sqlstr.=$ext_tabelle.",";
				$sqlstr.="'".$ext_tabelle_url."',";
				$sqlstr.=$ext_statistik.",";
				$sqlstr.="'".$ext_statistik_url."')";

				$result = doSQL($db,$sqlstr);
			}
			else			// Spalten updaten
			{
				$sql = 'update tt_saison set ';
				$sql .= 'spielzeit = "'.$spielzeit.'", ';
				$sql .= 'liga = "'.$liga.'", ';
				$sql .= 'kat = "'.$kat.'", ';
				$sql .= 'spielsystem = "'.$spielsystem.'", ';
				$sql .= 'tabelle = '.$tabelle.', ';
				$sql .= 'image_id = 0, ';
				$sql .= 'stats = '.$stats.', ';
				$sql .= 'startdatum = "'.$startdate.'",';
				$sql .= 'ext_spielplan = '.$ext_spielplan.',';
				$sql .= 'ext_spielplan_url = "'.$ext_spielplan_url.'",';
				$sql .= 'ext_tabelle = '.$ext_tabelle.',';
				$sql .= 'ext_tabelle_url = "'.$ext_tabelle_url.'",';
				$sql .= 'ext_statistik = '.$ext_statistik.',';
				$sql .= 'ext_statistik_url = "'.$ext_statistik_url.'" ';
				$sql .= ' where id = '.$id;

				$sql = ereg_replace('"NULL"', 'NULL', $sql);


				$result = doSQL($db,$sql);
				// Saison schliessen
				$closed=$_POST["closed"];
				if (isset($closed)) {
					mlog("Tischtennisverwaltung: Es wurde eine Saison geschlossen: ".$id);
				// Schließen der Saison
					$sqlstr  = "update tt_saison set ";
					$sqlstr .= "closed=1,closed_date=sysdate() where ";
					$sqlstr .= "id=".$id;	
					$result1 = doSQL($db,$sqlstr);
					
				// Kopieren des Mannschaftsfotos, wenn es noch nicht expliziet der Saison zugewiesen wurde
					$sqlstr  = "select mannschaft_id,bild from tt_zwtab_mannschaft_saison,tt_tus_mannschaft where ";
					$sqlstr .= "mannschaft_id=id and ";
					$sqlstr .= "saison_id=".$id;
					$result1 = getResult($db,$sqlstr);
					//print_r($result1);
					foreach ($result1 as $mannschaft) {
						$sqlstr  = "select image_id from tt_saison_image where ";
						$sqlstr .= "saison_id=".$id." and ";
						$sqlstr .= "team_id=".$mannschaft["mannschaft_id"];
						$result2=getResult($db,$sqlstr);
						//print_r($result2);
						$image_id=$result2["0"]["image_id"];
						if (isset($result2)) {
							if ($result2["0"]["image_id"]==0) {
								$sqlstr  = "update tt_saison_image set ";
								$sqlstr .= "image_id=".$mannschaft["bild"]." where ";
								$sqlstr .= "saison_id=".$id." and ";
								$sqlstr .= "team_id=".$mannschaft["mannschaft_id"];
								$image_id=$mannschaft["bild"];
								$result2=doSQL($db,$sqlstr);
							} else {
								// Foto wurde explizit der Saison zugeordnet. Foto wird nicht ins Archiv kopiert.
								$image_id=0;
							}
						} else {
							$sqlstr = "insert into tt_saison_image (saison_id,team_id,image_id) values (";
							$sqlstr.= $id.",";
							$sqlstr.= $mannschaft["mannschaft_id"].",";
							$sqlstr.= $mannschaft["bild"].")";
							$image_id=$mannschaft["bild"];
							$result2=doSQL($db,$sqlstr);
						}
						
						// Kopieren des Mannschaftsfotos, wenn es noch nicht im Archiv ist 
						// und setzen der ID des Mannschaftsfotos auf die archivierte ID
						if ($image_id !=0 ) {
							$image_id=image_copy_archiv($image_id);
							$sqlstr  = "update tt_saison_image set ";
							$sqlstr .= "image_id=".$image_id." where ";
							$sqlstr .= "saison_id=".$id." and ";
							$sqlstr .= "team_id=".$mannschaft["mannschaft_id"];
							$result2=doSQL($db,$sqlstr);	
						}
						unset($result2);
					}
					
					// Festschreiben der Spielerfotos
				// Es werden die derzeit aktuellen Fotos in die Saison geschrieben
				// Ist schon ein Foto vorhanden, so wird es nicht(!) überschrieben
					if($result1["code"]==0) {
						//Lesen der Spieler
						$sqlstr  = "select foto_id,person_id from tt_zwtab_person_typ_tus_mannschaft,tt_person where ";
						$sqlstr .= "tt_person.id=person_id and ";
						$sqlstr .= "saison_id=".$id;
						$result1=getResult($db,$sqlstr);
						foreach ($result1 as $spieler) {
							// Nur ausführen, wenn es auch ein Foto gibt
							if (isset($spieler["foto_id"])) {
								$sqlstr="select descr,datum,userid,size from sys_images where image_id=".$spieler["foto_id"];
								$image=GetResult($db,$sqlstr);
								if (isset($image)) {
									// Kopieren nur dann, wenn das Foto noch nicht im Archiv ist
									$sqlstr  = "select image_id from sys_images where ";
									$sqlstr .= "linked = ".$spieler["foto_id"]." and ";
									$sqlstr .= "kategorie=10";
									$result4 = getResult($db,$sqlstr);
									$newid=$result4["0"]["image_id"];
									if (!isset($newid)) {
										// Kopieren des Spielerfotos nach Kategorie 10
										// linked auf alte id setzen
										$sqlstr = "insert into sys_images (kategorie,descr,datum,userid,linked,size) ";
										$sqlstr .= "select 10,descr,datum,userid,".$spieler["foto_id"].",size from sys_images where image_id=".$spieler["foto_id"];
										$result3=doSQL($db,$sqlstr);
										$result4=getResult($db,"select last_insert_id() last");
										$newid=$result4["0"]["last"];
										$sqlstr  ="insert into sys_images_blob (image_id,bin_data) ";
										$sqlstr .="select ".$newid.",bin_data from sys_images_blob where image_id=".$spieler["foto_id"];
										$result5=doSQL($db,$sqlstr);
									}
									if ($result3["code"]==0) {	
										// Fotos des Spielers der Saison zuordnen
										// Es werden nur Fotos neu gesetzt wenn sie vorher nicht gesetzt waren
										$sqlstr  = "update tt_zwtab_person_typ_tus_mannschaft set ";
										$sqlstr .= "archive_image=".$newid." where ";
										$sqlstr .= "saison_id=".$id." and ";
										$sqlstr .= "person_id=".$spieler["person_id"]." and ";
										$sqlstr .= "archive_image=0";
										$result2=doSQL($db,$sqlstr);
									}
								}
							}
						}
					}
				}
				// Ende Saison schließen
			}
			mlog("Tischtennisverwaltung Speichern einer Saison: ".$id);
			if ($result["code"] == 0)
			{
				echo 'Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript: window.close();">Hier klicken, um das Fenster zu schließen</A>';
           			echo '<SCRIPT TYPE="text/javascript">';
                			echo 'opener.location.reload();';
                			echo 'setTimeout("window.close()",1000);';
           			echo '</SCRIPT>';
			
			}
			else
			{
				echo '<CENTER><BR><BR><BR>Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A></CENTER>';
			}
		} 
		else
		{
				echo '<CENTER><BR><BR><BR>Bitte füllen Sie das Formular komplett aus<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A></CENTER>';
		}
		break;
	case 'teams':
		$sql = 'select kat from tt_saison where id = '.$_REQUEST["saisonid"];
		$result_kat = getResult($db, $sql);		
		$kat = $result_kat[0]["kat"];

		echo '<BR><FORM METHOD="POST" ACTION="ttv_saison_popup.php?action=saveteams&PHPSESSID='.session_id().'">';
		echo '<INPUT TYPE="HIDDEN" NAME="saisonid" VALUE="'.$_REQUEST["saisonid"].'" />';
		echo '<TABLE WIDTH="100%" BORDER="0">';
			echo '<TR>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Mannschaften der Saison zuordnen</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>'.$kat.'</B>';
				echo '</TD>';
			echo '</TR>';

		$sql = 'select * from tt_mannschaft where kat = "'.$kat.'" order by name';
		$result_teams = getResult($db, $sql);

		$sql = 'select * from tt_zwtab_mannschaft_saison where saison_id = '.$_REQUEST["saisonid"];
		$result_zwtab = getResult($db, $sql);

		$i = 0;
		foreach ($result_teams as $row)
		{
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo $row["name"];
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					$checked = 0;
					if (isset($result_zwtab[0]))
						foreach ($result_zwtab as $zwtab)
							if ($row["id"] == $zwtab["mannschaft_id"])
								$checked = 1;

					$sql = 'select count(*) from tt_spiele where saison_id = '.$_REQUEST["saisonid"].' and (heim_id = '.$row["id"].' or aus_id = '.$row["id"].')';
					$result_played = getResult($db, $sql);
					$played = $result_played[0]["count(*)"];
					
					if ($played > 0 && $checked == 1)
					{
						echo '<INPUT TYPE="HIDDEN" NAME="teamid'.$i.'" VALUE="'.$row["id"].'" />';
						echo '<INPUT TYPE="CHECKBOX" NAME="diabled_teamid'.$i.'" VALUE="'.$row["id"].'" CHECKED DISABLED />';
					}
					elseif ($checked == 1)
						echo '<INPUT TYPE="CHECKBOX" NAME="teamid'.$i.'" VALUE="'.$row["id"].'" CHECKED />';
					else
						echo '<INPUT TYPE="CHECKBOX" NAME="teamid'.$i.'" VALUE="'.$row["id"].'" />';
				echo '</TD>';
			echo '</TR>';
			$i++;
		}
			echo '<TR>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" COLSPAN="2">';
					echo '<INPUT TYPE="HIDDEN" NAME="anzteams" VALUE="'.$i.'" />';
					echo '<INPUT TYPE="HIDDEN" NAME="kategorie" VALUE="'.$kat.'" />';
					echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
					echo '&nbsp;&nbsp;<INPUT TYPE="submit" VALUE="Zuordnen">';
				echo '</TD>';
			echo '</TR>';
		echo '</TABLE></FORM>';
		break;
	case 'saveteams':

		$saisonid = $_POST["saisonid"];
		$anzteams = $_POST["anzteams"];
		$kat = $_POST["kategorie"];

		$sql = 'delete from tt_zwtab_mannschaft_saison where saison_id = '.$saisonid;
		$result = doSQL($db,$sql);

		$ok = 'yes';
		for ($i = 0 ; $i <= $anzteams ; $i++)
		{
			$tmp="teamid".$i;
			if (isset($_REQUEST[$tmp]))
			{
				$sql = 'insert into tt_zwtab_mannschaft_saison (saison_id, mannschaft_id) ';
				$sql .= 'values ('.$saisonid.', '.$_POST["teamid".$i].')';

				$result = doSQL($db,$sql);
				if ($result["code"] != 0)
					$ok = 'no';
			}
		}

		mlog("Tischtennisverwaltung Speichern der Mannschaften in einer Saiosn Saison: ".$saisonid);
		if ($ok == 'yes')
		{
			echo 'Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript: window.close();">Hier klicken, um das Fenster zu schließen</A>';
        		echo '<SCRIPT TYPE="text/javascript">';
              			echo 'opener.location.reload();';
              			echo 'setTimeout("window.close()",1000);';
        		echo '</SCRIPT>';

		}
		else
		{
			echo '<CENTER><BR><BR><BR>Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:window.close();">Hier klicken, um das Fenster zu schließen</A></CENTER>';
		}
		break;
}
}
else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
closeConnect($db);
?>
</BODY>
</HTML>