<?php

////
//// ttv_team_popup.php
//// 
//// letzte Änderung : Volker, 19.07.2004
//// was : current_saison 
//// letzte Änderung : Volker, 04.03
//// was : Bilderverwaltung ==> Bilder wieder linked und wieder freigeben 
//// 
//// letzte Änderung : Volker, 29.02
//// was : Speicherung u. Auswahl der TuS Mannschaft mit Kategorieauswahl
//// 
//// letzte Änderung : Daniel, 17.02.2004 17:58
//// was : Datei angelegt

include "inc.php";
sitename("ttv_team_tus_popup.php",$_SESSION["groupid"]);
focus();
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e - TuS Mannschaftsverwaltung</TITLE>

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

</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php

if (priv("tt_tusteam"))
{
switch ($_REQUEST["action"])
{
	case 'add':
               
		// Wenn Kategorie noch nicht gewählt wurde, wird sie auf H gesetzt 
		if (! isset($_REQUEST["kat"]))
		   $_REQUEST["kat"] = "H";
		 
		 
		
		echo '<BR><FORM METHOD="POST" ACTION="ttv_team_tus_popup.php?action=save&PHPSESSID='.session_id().'">';
		echo '<TABLE WIDTH="100%" BORDER="0">';
			echo '<TR>';
				echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Neue TuS Mannschaft anlegen</B>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
			echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Kategorie<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					$url = 'ttv_team_tus_popup.php?action=add&PHPSESSID='.session_id();
					echo '<SELECT onChange="window.location.href=\''.$url.'&kat=\'+this.value;">';
						$sql = 'select kategorie,name from tt_mannschaft_kat order by name';
						$result = GetResult($db, $sql);
						foreach ($result as $row)
						{
    							if ($_REQUEST["kat"] == $row["kategorie"])
    							   echo '<OPTION SELECTED VALUE="'.$row["kategorie"].'">'.$row["name"].'</OPTION>';
    							else
    							   echo '<OPTION VALUE="'.$row["kategorie"].'">'.$row["name"].'</OPTION>';
    						}
					echo '</SELECT>';
				echo '</TD>';
			echo '</TR>';
		        
		        echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Name<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="name" VALUE="" />';
				echo '</TD>';
			echo '</TR>';
		
	
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>zugehörige Mannschaft<B>';
				echo '</TD>';
                    echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                      
                            $sql_teams = 'select id, name from tt_mannschaft 
                                          where kat=\''.$_REQUEST["kat"].'\' order by name';
                            $result_teams = getResult($db,$sql_teams);
                            echo '<select name="team">';
                            echo '<option value="0" selected>keine</option>';
                            foreach($result_teams as $team)
                                    echo '<option value="'.$team["id"].'">'.$team["name"].'</option>';
                            echo '</select>';
                    echo '</TD>';
			echo '</TR>';
            echo '<TR>';
                    echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                            echo '<B>Mannschaftsfoto<B>';
                    echo '</TD>';
                    echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                            $sql_fotos = 'select * from sys_images where kategorie = 1 and linked = 0';
                            $result_fotos = getResult($db,$sql_fotos);
                            echo '<select name="foto">';
                            echo '<option value="0" selected>keins</option>';
                            foreach($result_fotos as $foto)
                                    echo '<option value="'.$foto["image_id"].'">'.$foto["descr"].'</option>';
                            echo '</select>';
                            echo '&nbsp;<A HREF="javascript:viewImage(0);">';
                            echo '<IMG SRC="images/view.jpg" HEIGHT="16" WIDTH="16" BORDER="0" ALT="Bild ansehen"></A>';
                    echo '</TD>';
            echo '</TR>';

    		echo '<TR>';
			echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
				echo '<B>Im Menü anzeigen<B>';
			echo '</TD>';
			echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
				echo '<input type="Checkbox" name="show_menu" value="1" checked>';
			echo '</TD>';
		echo '</TR>';

		        echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Reihenfolge im Menü (idx)<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="10" NAME="reihenfolge" VALUE="10" />';
				echo '</TD>';
			echo '</TR>';

	       	echo '<TR>'; 
	       	echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">'; 
	       	echo '<B>Spielstätte<B>'; 
	       	echo '</TD>'; 
		// Spielstätte
	       	echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">'; 
                    $sqlstr = 'select id,name from spielstaette where aktiv = 1 order by id';
	            $st = getResult($db,$sqlstr);
		    $st["-1"]["id"]="0";	       	
		    $st["-1"]["name"]="keine Angabe";
	       	    build_select($st,"name","id","spielstaetteid","","1","0"); 
	       	    unset($st);
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
		$sql = 'select * from tt_tus_mannschaft where id = '.$_REQUEST["teamid"];
		$result = getResult($db, $sql);
		$team = $result[0];
                $old_foto = $team["bild"]; 
               
                 
		echo '<BR><FORM METHOD="POST" ACTION="ttv_team_tus_popup.php?action=save&PHPSESSID='.session_id().'">';

		echo '<INPUT TYPE="HIDDEN" NAME="id" VALUE="'.$_REQUEST["teamid"].'" />';

		echo '<TABLE WIDTH="100%" BORDER="0">';
			echo '<TR>';
				echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>TuS Mannschaft bearbeiten</B>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Name<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="50" NAME="name" VALUE="'.$team["name"].'" />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>zugehörige Mannschaft<B>';
				echo '</TD>';
                    echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                            $result_kat=GetResult($db,"select kat from tt_mannschaft where id=".$_REQUEST["teamid"]);
                            $sql_teams = "select id, name from tt_mannschaft where kat='".$result_kat["0"]["kat"]."'";
                            $result_teams = getResult($db,$sql_teams);
                            echo '<select name="team">';
                            echo '<option value="0" selected>keine</option>';
                            foreach($result_teams as $team2)
                            	if ($team2["id"] == $team["id"])
                                    echo '<option selected value="'.$team2["id"].'">'.$team2["name"].'</option>';
                                else
                                	echo '<option value="'.$team2["id"].'">'.$team2["name"].'</option>';
                            echo '</select>';
                    
                    echo '</TD>';
			echo '</TR>';
            echo '<TR>';
                    echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                            echo '<B>Mannschaftsfoto<B>';
            echo '</TD>';
            echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                    echo '<INPUT TYPE="HIDDEN" NAME="foto_old" VALUE="'.$team["bild"].'" />';
                    $sql_fotos = 'select image_id,descr,linked from sys_images where kategorie = 1';
                    $result_fotos = getResult($db,$sql_fotos);
                    echo '<select name="foto">';
                    if ($team["bild"] == null)
                            echo '<option selected value="0" >keins</option>';
                    else
                            echo '<option value="0" >keins</option>';
                    foreach($result_fotos as $foto)
                            if ($foto["image_id"] == $team["bild"] || $foto["linked"] == 0)
                                    if ($foto["image_id"] == $team["bild"])
                                            echo '<option selected value="'.$foto["image_id"].'">'.$foto["descr"].'</option>';
                                    else
                                            echo '<option value="'.$foto["image_id"].'">'.$foto["descr"].'</option>';
                    echo '</select>';
                    echo '&nbsp;<A HREF="javascript:viewImage(0);">';
                    echo '<IMG SRC="images/view.jpg" HEIGHT="16" WIDTH="16" BORDER="0" ALT="Bild ansehen"></A>';


            echo '</TD>';
            echo '</TR>';
       		echo '<TR>';
			echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
				echo '<B>Im Menü anzeigen<B>';
			echo '</TD>';
			echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
				if ($team["show_menu"]==1)
					echo '<input type="Checkbox" name="show_menu" value="1" checked>';
				else
					echo '<input type="Checkbox" name="show_menu" value="1" unchecked>';
			echo '</TD>';
		echo '</TR>';

	       	echo '<TR>'; 
	       	echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">'; 
	       	echo '<B>Reihenfolge im Menü (idx)<B>'; 
	       	echo '</TD>'; 
	       	echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">'; 
	       	echo '<INPUT TYPE="TEXT" SIZE="10" NAME="reihenfolge" VALUE="'.$team["reihenfolge"].'" />'; 
	       	echo '</TD>'; 
	       	echo '</TR>'; 
	       	echo '<TR>'; 
	       	echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">'; 
	       	echo '<B>Aktuelle Saison<B>'; 
	       	echo '</TD>'; 
	       	echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">'; 
	       	$sqlstr="select id,spielzeit,liga from tt_saison,tt_zwtab_mannschaft_saison 
	       	where saison_id=id and mannschaft_id=".$_REQUEST["teamid"]." order by spielzeit,liga"; 
	       	$saison=GetResult($db,$sqlstr); 
	       	if (isset($saison)) { 
	       		$i=0;
	       		foreach ($saison as $row) {
	       			$saison[$i]["name"]=$row["spielzeit"].' '.$row["liga"];
	       			$i++;
	       		}
		   	$saison["-1"]["id"]="0";	       	
		       	$saison["-1"]["name"]="automatisch";
	       		build_select($saison,"name","id","saison","","1",$team["current_saison"]); 
	       	} else { 
	       		echo "-"; 
	       	} 
	       	echo '</TD>'; 
	       	echo '</TR>';
	       	echo '<TR>'; 
	       	echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">'; 
	       	echo '<B>Spielstätte<B>'; 
	       	echo '</TD>'; 
	       	echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">'; 
                    $sqlstr = 'select id,name from spielstaette where aktiv=1 order by id';
	            $st = getResult($db,$sqlstr);
		    $st["-1"]["id"]="0";	       	
		    $st["-1"]["name"]="keine Angabe";
	       	    build_select($st,"name","id","spielstaetteid","","1",$team["spielstaette_id"]); 
	       	    unset($st);
	       	//echo '<INPUT TYPE="TEXT" SIZE="60" NAME="spielstaette" VALUE="'.$team["spielstaette"].'" />'; 
	       	echo '</TD>'; 

	       	echo '</TR>'; 


   
            echo '<TR>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" COLSPAN="2">';
					echo '<INPUT type=hidden name="old_foto" value="'.$old_foto.'">';
					echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
					echo '&nbsp;&nbsp;<INPUT TYPE="submit" VALUE="Speichern">';
				echo '</TD>';
			echo '</TR>';
		echo '</TABLE>';
		echo '</FORM>';
		break;
	case 'save':
		if (isset($_POST["id"]))
			$id = $_POST["id"];

			$name = $_POST["name"];
                        $team = $_POST["team"];
                        $foto = $_POST["foto"];
                        $old_foto = $_POST["old_foto"];
       			$idx = $_POST["reihenfolge"];	                 
			$show_menu = $_POST["show_menu"];
                        $saison = $_POST["saison"];
                        $spielstaetteid = $_POST["spielstaetteid"]; 
                        
                        if (!isset($show_menu)) $show_menu=0;
                        //echo "<br>".$old_foto;
                        //echo "<br>";
                        
                        
                        if ($foto=="")
			     $foto = "NULL";
			        
			if (!isset($id))	// neu anlegen
			{
			    if ($team != 0)
                            {                     
			        $sql = "insert into tt_tus_mannschaft (id,name,bild,reihenfolge,show_menu,spielstaette_id) 
				        values ($team,'$name',$foto,$idx,$show_menu,$spielstaetteid)";
				//echo $sql;
				$result = doSQL($db,$sql);
			    }
			    else
			    {
			    	echo "<br><b>Sie müssen eine gültige Mannschaft zuordnen";
			    	$result["code"] = 1;
			    }
			}
			else			// alten updaten
			{
				$sql = 'update tt_tus_mannschaft set ';
				$sql .= 'name = "'.$name.'", ';
				$sql .= 'id ='.$team.', ';
				$sql .= 'reihenfolge='.$idx.', ';
				$sql .= 'show_menu='.$show_menu.', ';
				$sql .= 'bild ='.$foto.', ';
				$sql .= 'current_saison ='.$saison.', ';
				$sql .= 'spielstaette_id ='.$spielstaetteid.' ';
				$sql .= 'where id = '.$id;
				
				$result = doSQL($db,$sql);
			}
			
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

			mlog("Tischtennisverwaltung: Speichern einer TuS-Mannschaft: ".$id);
			if ($result["code"] == 0)
			{
				echo 'Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A>';
           			echo '<SCRIPT TYPE="text/javascript">';
                			echo 'opener.location.reload();';
                			echo 'setTimeout("window.close()",1000);';
           			echo '</SCRIPT>';
			}
			else
			{
				echo '<CENTER><BR><BR><BR>Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A></CENTER>';
				echo '<BR>'.$sql.'<BR>';
				echo '<pre>';
				//print_r($result);
				echo '</pre>';
			}
		
		break;
}
}
else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
?>
</BODY>
</HTML>