<?php

////
//// fbv_saison_popup.php
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
sitename("fbv_saison_popup.php",$_SESSION["groupid"]);
focus();
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
if (priv("fb_saison"))
{
switch ($_REQUEST["action"])
{
	case 'add':
		echo '<BR><FORM METHOD="POST" ACTION="fbv_saison_popup.php?action=save&PHPSESSID='.session_id().'">';
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
				        $sqlstr = "select * from fb_mannschaft_kat order by name";
				        $result = GetResult($db,$sqlstr);
				        build_select($result,name,kategorie,kat);
				        unset($result);
					//echo '<INPUT TYPE="TEXT" SIZE="10" NAME="kat" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Spielzeit</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="spielzeit" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Liga</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="liga" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Staffelid</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="staffelid" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Startdatum</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="12" NAME="startdate" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Tabelle anzeigen</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="CHECKBOX" NAME="tabelle" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Statistiken anzeigen</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="CHECKBOX" NAME="stats" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Turnier</B>';
					echo '<br>Spiele erscheinen nicht in Vorankündigungen';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="CHECKBOX" NAME="turnier" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';


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
		$sql = 'select * from fb_saison where id = '.$_REQUEST["saisonid"];
		$result = getResult($db, $sql);
		$saison = $result[0];

		echo '<BR><FORM METHOD="POST" ACTION="fbv_saison_popup.php?action=save&PHPSESSID='.session_id().'">';

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
					echo '<B>Kategorie</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="1" NAME="kat" VALUE="'.$saison["kat"].'" READONLY '.$edit.'/>';
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
					echo '<B>Spielzeit</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="spielzeit" VALUE="'.$saison["spielzeit"].'" '.$edit.' />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Liga</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="liga" VALUE="'.$saison["liga"].'" '.$edit.' />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Staffelid</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="staffelid" VALUE="'.$saison["staffelid"].'" '.$edit.' />';
				echo '</TD>';
			echo '</TR>';

			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Startdatum</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="12" NAME="startdate" VALUE="'.date("d.m.Y", strtotime($saison["startdatum"])).'" '.$edit.'/>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Tabelle anzeigen ?</B>';
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
					echo '<B>Statistiken anzeigen ?</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					if ($saison["stats"] == 1)
						echo '<INPUT CHECKED TYPE="CHECKBOX" NAME="stats" '.$edit.'/>';
					else
						echo '<INPUT TYPE="CHECKBOX" NAME="stats" '.$edit.'/>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Turnier</B>';
					echo '<br>Spiele erscheinen nicht in Vorankündigungen';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					if ($saison["turnier"] == 1)
						echo '<INPUT CHECKED TYPE="CHECKBOX" NAME="turnier" '.$edit.'/>';
					else
						echo '<INPUT TYPE="CHECKBOX" NAME="turnier" '.$edit.'/>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" COLSPAN="2">';
					echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
					if ($edit=="") {
						echo '&nbsp;&nbsp;<INPUT TYPE="submit" VALUE="Ändern" '.$edit.'>';
					}
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

		if (isset($_POST["turnier"]))
			$turnier = 1;
		else
			$turnier = 0;

		if (strlen($_POST["kat"]) == 1 && strlen($_POST["spielzeit"]) != 0 && strlen($_POST["liga"]) != 0 && strlen($_POST["startdate"]) != 0)
		{
			$kat = $_POST["kat"];
			$spielzeit = $_POST["spielzeit"];
			$liga = $_POST["liga"];
			$staffelid = $_POST["staffelid"];
			$startdate = ts2db($_POST["startdate"]);

			if (!isset($id))	// neu anlegen
			{
				$sql = 'insert into fb_saison (spielzeit, kat, liga, startdatum, tabelle, image_id, stats,turnier) ';
				$sql .= 'values ("'.$spielzeit.'", "'.$kat.'", "'.$liga.'", "'.$startdate.'", '.$tabelle.', 0, '.$stats.','.$turnier.')';
				$result = doSQL($db,$sql);
			}
			else			// alten updaten
			{
				$sql = 'update fb_saison set ';
				$sql .= 'spielzeit = "'.$spielzeit.'", ';
				$sql .= 'liga = "'.$liga.'", ';
				$sql .= 'staffelid = "'.$staffelid.'", ';
				$sql .= 'kat = "'.$kat.'", ';
				$sql .= 'tabelle = '.$tabelle.', ';
				$sql .= 'image_id = 0, ';
				$sql .= 'stats = '.$stats.', ';
				$sql .= 'turnier = '.$turnier.', ';
				$sql .= 'startdatum = "'.$startdate.'"';
				$sql .= 'where id = '.$id;
				$sql = ereg_replace('"NULL"', 'NULL', $sql);
				$result = doSQL($db,$sql);
				
				// Saison schliessen
				$closed=$_POST["closed"];
				if (isset($closed)) {
					mlog("Fussballverwaltung: Es wurde eine Saison geschlossen: ".$id);
				// Schließen der Saison
					$sqlstr  = "update fb_saison set ";
					$sqlstr .= "closed=1,closed_date=sysdate() where ";
					$sqlstr .= "id=".$id;	
					$result1 = doSQL($db,$sqlstr);
					
				// Kopieren des Mannschaftsfotos, wenn es noch nicht expliziet der Saison zugewiesen wurde
					$sqlstr  = "select mannschaft_id,bild from fb_zwtab_mannschaft_saison,fb_tus_mannschaft where ";
					$sqlstr .= "mannschaft_id=id and ";
					$sqlstr .= "saison_id=".$id;
					$result1 = getResult($db,$sqlstr);
					//print_r($result1);
					foreach ($result1 as $mannschaft) {
						$sqlstr  = "select image_id from fb_saison_image where ";
						$sqlstr .= "saison_id=".$id." and ";
						$sqlstr .= "team_id=".$mannschaft["mannschaft_id"];
						$result2=getResult($db,$sqlstr);
						//print_r($result2);
						$image_id=$result2["0"]["image_id"];
						if (isset($result2)) {
							if ($result2["0"]["image_id"]==0) {
								$sqlstr  = "update fb_saison_image set ";
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
							$sqlstr = "insert into fb_saison_image (saison_id,team_id,image_id) values (";
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
							$sqlstr  = "update fb_saison_image set ";
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
						$sqlstr  = "select foto_id,person_id from fb_zwtab_person_typ_tus_mannschaft,fb_person where ";
						$sqlstr .= "fb_person.id=person_id and ";
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
										$sqlstr  = "update fb_zwtab_person_typ_tus_mannschaft set ";
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



		/*
			if ($result["code"] == 0)
			{
				if ($old_foto != $foto)
				{
					$sqlstr = "update sys_images set linked = 1 where image_id=$foto";
					$result1 = doSQL($db,$sqlstr);
					if ($old_foto != "0")
					{
						$sqlstr = "update sys_images set linked = 0 where image_id=$old_foto";
						$result1 = doSQL($db,$sqlstr);
						//echo "<br> Foto  wurde wieder freigegeben<br>";
					}  
				}
			}
		*/
			if ($result["code"] == 0)
			{
				mlog("Fussballverwaltung: Es wurde eine Saison gespeichert: ".$id);
				echo 'Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript: window.close();">Hier klicken, um das Fenster zu schließen</A>';
           			echo '<SCRIPT TYPE="text/javascript">';
                			echo 'opener.location.reload();';
                			echo 'setTimeout("window.close()",1000);';
           			echo '</SCRIPT>';
			
			}
			else
			{
				echo '<CENTER><BR><BR><BR>Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A></CENTER>';
				echo '<pre>';
				print_r($result);
				echo '</pre>';
			}
		} 
		else
		{
				echo '<CENTER><BR><BR><BR>Bitte füllen Sie das Formular komplett aus<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A></CENTER>';
				echo '<pre>';
				print_r($result);
				echo '</pre>';
		}
		break;
	case 'teams':
		$sql = 'select kat from fb_saison where id = '.$_REQUEST["saisonid"];
		$result_kat = getResult($db, $sql);		
		$kat = $result_kat[0]["kat"];

		echo '<BR><FORM METHOD="POST" ACTION="fbv_saison_popup.php?action=saveteams&PHPSESSID='.session_id().'">';
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

		$sql = 'select * from fb_mannschaft where kat = "'.$kat.'" order by name';
		$result_teams = getResult($db, $sql);

		$sql = 'select * from fb_zwtab_mannschaft_saison where saison_id = '.$_REQUEST["saisonid"];
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

					$sql = 'select count(*) from fb_spiele where saison_id = '.$_REQUEST["saisonid"].' and (heim_id = '.$row["id"].' or aus_id = '.$row["id"].')';
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

		$sql = 'delete from fb_zwtab_mannschaft_saison where saison_id = '.$saisonid;
		$result = doSQL($db,$sql);

		$ok = 'yes';
		for ($i = 0 ; $i <= $anzteams ; $i++)
		{
			$tmp="teamid".$i;
			if (isset($_REQUEST[$tmp]))
			{
				$sql = 'insert into fb_zwtab_mannschaft_saison (saison_id, mannschaft_id) ';
				$sql .= 'values ('.$saisonid.', '.$_POST["teamid".$i].')';

				$result = doSQL($db,$sql);
				if ($result["code"] != 0)
					$ok = 'no';
			}
		}

		if ($ok == 'yes')
		{
			mlog("Fussballverwaltung: Es wurden Mannschaften einer Saison gespeichert: ".$saisonid);
			echo 'Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript: window.close();">Hier klicken, um das Fenster zu schließen</A>';
        		echo '<SCRIPT TYPE="text/javascript">';
              			echo 'opener.location.reload();';
              			echo 'setTimeout("window.close()",1000);';
        		echo '</SCRIPT>';

		}
		else
		{
			echo '<CENTER><BR><BR><BR>Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:window.close();">Hier klicken, um das Fenster zu schließen</A></CENTER>';
			echo '<pre>';
			print_r($result);
			echo '</pre>';
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