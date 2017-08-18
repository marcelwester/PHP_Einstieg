<?php

////
//// ttv_saison_image_popup.php
////
//// letzte Änderung : Volker, 19.06.2005 
//// was : Erstellung
////

include "inc.php";
sitename("ttv_saison_image_popup.php",$_SESSION["groupid"]);
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
              var imgId = document.getElementsByName("imageid")[0].value;
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
if (priv("tt_saison"))
{
	switch ($_REQUEST["action"])
	{
		case 'add':
				echo '<TABLE WIDTH="100%" BORDER="0">';
					if (isset($_POST["teamid"]) && isset($_POST["saisonid"]) && !isset($_POST["imageid"])) 		
						echo '<BR><FORM METHOD="POST" ACTION="ttv_saison_image_popup.php?action=save&PHPSESSID='.session_id().'">';
					else
						echo '<BR><FORM METHOD="POST" ACTION="ttv_saison_image_popup.php?action=add&PHPSESSID='.session_id().'">';

					echo '<TR>';
						echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
							echo '<B>Mannschaftsfoto zur Saison zuordnen</B>';
						echo '</TD>';
					echo '</TR>';
					
					// Auswahl der TuS Mannschaft
					if (! isset($_POST["teamid"])) {
						echo '<TR>';
							echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF" width="50%">';
								echo '<B>TuS-Mannschaft<B>';
							echo '</TD>';
							echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" width="50%">';
							        $sqlstr = "select id,name from tt_tus_mannschaft order by name";
							        $result = GetResult($db,$sqlstr);
							        build_select($result,"name","id","teamid");
							      	unset($result);
							echo '</TD>';
						echo '</TR>';
					}
					
					// Auswahl der Saison paasend zur TuS Mannschaft
					if (! isset($_POST["saisonid"]) && isset($_POST["teamid"])) {
						$teamid=$_POST["teamid"];
						echo '<TR>';
							echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF" width="50%">';
								echo '<B>TuS-Mannschaft<B>';
							echo '</TD>';
							echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" width="50%">';
							        $sqlstr = "select id,name from tt_tus_mannschaft where id=".$teamid;
							        $result = GetResult($db,$sqlstr);
									echo '<b>'.$result["0"]["name"].'</b>';
							echo '</TD>';
						echo '</TR>';
						echo '<TR>';
							echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF" width="50%">';
								echo '<B>Saison<B>';
							echo '</TD>';
							echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" width="50%">';
							        $sqlstr  = "select id,liga,spielzeit from tt_saison,tt_zwtab_mannschaft_saison ";
							        $sqlstr .= "where closed=0 and saison_id=id and ";
							        $sqlstr .= "mannschaft_id =".$teamid." order by spielzeit,liga";
							        $result = GetResult($db,$sqlstr);
							        $i=0;
							        if (isset($result)) {
								        foreach ($result as $row) {
								        	$result[$i++]["sp_liga"]=$row["spielzeit"].", ".$row["liga"];
								        }
								        build_select($result,"sp_liga","id","saisonid");
								      	unset($result);
								     } else {
								     	echo "Keine offene Saison gefunden";
								     	$abbruch="1";
								     }
							echo '</TD>';
						echo '</TR>';
						echo '<input type="hidden" name="teamid" value="'.$teamid.'"/>';
					} 
					
					// Auswahl des Mannschaftsfotos
					if (isset($_POST["teamid"]) && isset($_POST["saisonid"]) && !isset($_POST["imageid"])) {		
						$teamid=$_POST["teamid"];
						$saisonid=$_POST["saisonid"];
						echo '<TR>';
							echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF" width="50%">';
								echo '<B>TuS-Mannschaft<B>';
							echo '</TD>';
							echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" width="50%">';
							        $sqlstr = "select id,name from tt_tus_mannschaft where id=".$teamid;
							        $result = GetResult($db,$sqlstr);
									echo '<b>'.$result["0"]["name"].'</b>';
							echo '</TD>';
						echo '</TR>';
						echo '<TR>';
							echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF" width="50%">';
								echo '<B>Saison<B>';
							echo '</TD>';
							echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" width="50%">';
							        $sqlstr  = "select id,liga,spielzeit from tt_saison ";
							        $sqlstr .= "where id=".$saisonid;
							        $result = GetResult($db,$sqlstr);
									echo '<b>'.$result["0"]["spielzeit"].", ".$result["0"]["liga"].'</b>';
							echo '</TD>';
						echo '</TR>';
						
						echo '<TR>';
							echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF" width="50%">';
								echo '<B>Mannschaftsfoto<B>';
							echo '</TD>';
	                    	echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                            	$sql_fotos = 'select image_id,linked,descr from sys_images where kategorie = 1 and linked = 0';
	                            	$result_fotos = getResult($db,$sql_fotos);
	                            echo '<select name="imageid">';
	                            echo '<option value="0" selected>keins</option>';
	                            foreach($result_fotos as $foto)
	                                    echo '<option value="'.$foto["image_id"].'">'.$foto["descr"].'</option>';
	                            echo '</select>';
	                            echo '&nbsp;<A HREF="javascript:viewImage(0);">';
	                            echo '<IMG SRC="images/view.jpg" HEIGHT="16" WIDTH="16" BORDER="0" ALT="Bild ansehen"></A>';
	                    	echo '</TD>';
						echo '</TR>';
						echo '<input type="hidden" name="teamid" value="'.$teamid.'"/>';
						echo '<input type="hidden" name="saisonid" value="'.$saisonid.'"/>';

					}

					
					echo '<tr><td colspan=2 align="center">';
					echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
					echo '&nbsp;&nbsp;&nbsp;';
						if (!isset($abbruch)) {
							if (isset($saisonid) && isset($teamid)) 
								echo '<input type="submit" value="Speichern" />';
							else
								echo '<input type="submit" value="Weiter" />';
						}
					echo '</center></td></tr>';
				echo '</form>';		
			echo '</table>';
		break;
	
		case 'edit':
			echo '<TABLE WIDTH="100%" BORDER="0">';
				echo '<BR><FORM METHOD="POST" ACTION="ttv_saison_image_popup.php?action=save&PHPSESSID='.session_id().'">';

				echo '<TR>';
					echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF" width="50%">';
						echo '<B>TuS-Mannschaft<B>';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" width="50%">';
					        $sqlstr = "select id,name from tt_tus_mannschaft where id=".$teamid;
					        $result = GetResult($db,$sqlstr);
							echo '<b>'.$result["0"]["name"].'</b>';
					echo '</TD>';
				echo '</TR>';
				echo '<TR>';
					echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF" width="50%">';
						echo '<B>Saison<B>';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" width="50%">';
					        $sqlstr  = "select id,liga,spielzeit from tt_saison ";
					        $sqlstr .= "where id=".$saisonid;
					        $result = GetResult($db,$sqlstr);
							echo '<b>'.$result["0"]["spielzeit"].", ".$result["0"]["liga"].'</b>';
					echo '</TD>';
				echo '</TR>';
				
				echo '<TR>';
					echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF" width="50%">';
						echo '<B>Mannschaftsfoto<B>';
					echo '</TD>';
                	echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                        	$sqlstr  = "select image_id from tt_saison_image where ";
                        	$sqlstr .= "team_id=".$teamid." and ";
                        	$sqlstr .= "saison_id=".$saisonid;
                        	$result2=GetResult($db,$sqlstr);
                        	$old_image_id=0;
                        	if(isset($result2)) {
                        		$old_image_id=$result2["0"]["image_id"];
                        	}
                        		
                        	$sql_fotos = 'select image_id,linked,descr from sys_images where (kategorie = 1 and linked = 0) or image_id='.$old_image_id;
                        	$result_fotos = getResult($db,$sql_fotos);
                        	$result_fotos["-1"]["image_id"]="0";
                        	$result_fotos["-1"]["descr"]="keins";
 							build_select($result_fotos,"descr","image_id","imageid","",1,$old_image_id);
                        echo '&nbsp;<A HREF="javascript:viewImage(0);">';
                        echo '<IMG SRC="images/view.jpg" HEIGHT="16" WIDTH="16" BORDER="0" ALT="Bild ansehen"></A>';
                	echo '</TD>';
				echo '</TR>';
				echo '<input type="hidden" name="teamid" value="'.$teamid.'"/>';
				echo '<input type="hidden" name="saisonid" value="'.$saisonid.'"/>';
				echo '<input type="hidden" name="old_image_id" value="'.$old_image_id.'"/>';
				echo '<tr><td colspan=2 align="center"><input type="submit" value="Speichern" /></center></td></tr>';
				echo '</form>';		
			echo '</table>';
		break;
	
		case 'save':
			if (isset($_POST["old_image_id"])) {
				
				$sqlstr  = "update tt_saison_image set ";
				$sqlstr .= "image_id=".$_POST["imageid"]." where ";
				$sqlstr .= "team_id=".$_POST["teamid"]." and ";
				$sqlstr .= "saison_id=".$_POST["saisonid"];
				$result=doSQL($db,$sqlstr);

				// Bilderverwaltung
				if ($_POST["old_image_id"] != $_POST["imageid"]) {
					if ($_POST["old_image_id"] != "0") {
						$sqlstr="update sys_images set linked=0 where image_id=".$_POST["old_image_id"];
						$result=doSQL($db,$sqlstr);
					}
					$sqlstr="update sys_images set linked=1 where image_id=".$_POST["imageid"];
					$result=doSQL($db,$sqlstr);
				}									
			} else {
				// Anlegen eines neuen Datensatzes
				$sqlstr  = "insert into tt_saison_image (team_id,saison_id,image_id) values (";
				$sqlstr .= $_POST["teamid"].",";
				$sqlstr .= $_POST["saisonid"].",";
				$sqlstr .= $_POST["imageid"].")";
				$result=doSQL($db,$sqlstr);
				
				if ($_POST["imageid"] !="0" ) {
					if ($result["code"]=="0") {
						$sqlstr="update sys_images set linked=1 where image_id=".$_POST["imageid"];
						$result1=doSQL($db,$sqlstr);
						// print_r($result);
					}
				}
			}
			
			if ($result["code"] == 0)
			{
				mlog("Tischtennisverwaltung: Neues Mannschaftsfoto zur Saison zugeordnet (team,saison,image): ".$_POST["teamid"].", ".$_POST["saisonid"].", ".$_POST["imageid"]);
				echo 'Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript: window.close();">Hier klicken, um das Fenster zu schließen</A>';
           			echo '<SCRIPT TYPE="text/javascript">';
                			echo 'opener.location.reload();';
                			echo 'setTimeout("window.close()",1000);';
           			echo '</SCRIPT>';
			}
			else
			{
				echo '<CENTER><BR><BR><BR>Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A></CENTER>';
				echo '<center> Prüfen Sie, ob der Eintrag nicht schon in der Liste vorhanden ist !</center>';
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