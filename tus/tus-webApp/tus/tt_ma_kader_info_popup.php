<?php

//// tt_ma_kader_info_popup.php
////
//// letzte Änderung: Daniel 29.03.2004
//// was: Ausgbae des Alters statt des Geburtstdatums 
////
//// letzte Änderung: Daniel 26.02.2004 18:35
//// was: Erstellung
////
session_start();
include "inc.php";
sitename("tt_ma_kader_info_popup.php",$_SESSION["groupid"]);
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php
    focus();
    $saisonid=$_REQUEST["saisonid"];
    $sql_person = 'select * from tt_person where id = '.$_REQUEST["personid"];
    $result_person = getResult($db, $sql_person);
    $person = $result_person[0];

    //echo '<BR><BR><FORM METHOD="POST" ACTION="fbv_person_popup.php?action=save&PHPSESSID='.session_id().'">';
    echo '<BR><BR>';
    //echo '<INPUT TYPE="HIDDEN" NAME="id" VALUE="'.$_REQUEST["personid"].'" />';
    echo '<TABLE WIDTH="100%" BORDER="0">';
            echo '<TR>';
                    echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                            echo '<B>Person Info</B>';
                    echo '</TD>';
            echo '</TR>';
            echo '<TR>';
                    echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                            echo '<B>Name<B>';
                    echo '</TD>';
                    echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                            echo $person["vorname"].' '.$person["name"];
                    echo '</TD>';
            echo '</TR>';
	if (isset($_SESSION["groupid"]) && $_SESSION["groupid"] > 0)
            {
            	echo '<TR>';
                    echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
						echo '<B>Telefon<B>';
                    echo '</TD>';
                    echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
						echo $person["tel"];
                    echo '</TD>';
            	echo '</TR>';
            	echo '<TR>';
                    echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
						echo '<B>Mobil<B>';
                    echo '</TD>';
                    echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
						echo $person["mtel"];
                    echo '</TD>';
            	echo '</TR>';
            	echo '<TR>';
                    echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
						echo '<B>Fax<B>';
                    echo '</TD>';
                    echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
						echo $person["fax"];
                    echo '</TD>';
            	echo '</TR>';

            }
	            echo '<TR>';
                    echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                            echo '<B>Email<B>';
                    echo '</TD>';
                    echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                            echo $person["email"];
                    echo '</TD>';
    	        echo '</TR>';
			
            echo '<TR>';
                    echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                            echo '<B>Alter<B>';
                    echo '</TD>';
                    echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                            if ($person["geburtsdatum"] != null)
                            {
                            	$current=explode(".",date("d.m.Y"));
                            	$birth=explode(".",date("d.m.Y", strtotime($person["geburtsdatum"])));
                            	$age=$current["2"] - $birth["2"] - 1;
                            	if ($current["1"] > $birth["1"]) 
                            	{
                            		$age ++;
                            	}
                            	if ($current["1"] == $birth["1"]) 
                            	 	if ($current["0"] >= $birth["0"]) 
                            			$age++;
                            	
                            	echo $age;
                            	//echo date("d.m.Y", strtotime($person["geburtsdatum"]));
                            }
                            else
                            {
                            	echo '-';
                            }
                    echo '</TD>';
            echo '</TR>';
/*            echo '<TR>';
                    echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                            echo '<B>Mitglied seit<B>';
                    echo '</TD>';
                    echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                            if ($person["mitglied_datum"] != null)
                            	echo date("d.m.Y", strtotime($person["mitglied_datum"]));
                    echo '</TD>';
            echo '</TR>';*/
           
           
			// Prüfen, ob saison geschlosen wurde und Foto_id zurückgeben
			$sqlstr  = "select archive_image from tt_zwtab_person_typ_tus_mannschaft where ";
			$sqlstr .= "person_id=".$person["id"]." and ";
			$sqlstr .= "saison_id=".$saisonid;
			$result_img = getResult($db,$sqlstr);
			if (isset($result_img["0"]["archive_image"]) && $result_img["0"]["archive_image"]!="0") {
				$person["foto_id"]=$result_img["0"]["archive_image"];
				//echo "*";
			}

           echo '<TR>';
                   echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                            echo '<B>Bemerkung<B>';
                            echo '</TD>';
                            echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                            echo $person["bemerkung"];
                   echo '</TD>';
           echo '</TR>';
                 
            echo '<TR>';
                    echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                            echo '<B>Foto<B>';
                    echo '</TD>';
                    echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                            if ($person["foto_id"] == null)
                                echo 'nicht vorhanden';
                            else
                            {
				echo '<IMG SRC="showimage2.php?id='.$person["foto_id"].'" WIDTH="200">';
				$sqlstr="select datum from sys_images where image_id=".$person["foto_id"];
				$result_datum=getResult($db,$sqlstr);
				echo '<br>Foto vom '.date('d.m.Y',strtotime($result_datum["0"]["datum"]));
				unset($result_datum);

				//echo '<IMG SRC="showimage2.php?id='.$person["foto_id"].'" WIDTH="200">';
                            }
                    echo '</TD>';
            echo '</TR>';
            echo '<TR>';
                    echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" COLSPAN="2">';
	         	if (isset($_REQUEST["descent"])) {
		  		echo '<INPUT TYPE="button" VALUE="Zurück" onClick="window.history.back();">';
		  		echo '&nbsp;&nbsp;&nbsp;';
		  	}
                        echo '<INPUT TYPE="button" VALUE="Ok" onClick="window.close();">';
                    echo '</TD>';
            echo '</TR>';
    echo '</TABLE>';

	// Statistik
	$sqlstr="select curdate() datum,id,hits,hits_current_day,DATE_FORMAT(toc,'%Y-%m-%d') toc from sys_counter_spielerinfo where sportart='tt' and id=".$_REQUEST["personid"];
	$result=GetResult($db,$sqlstr);
	//print_r($result);
	if (! isset($result["0"]["id"])) {
		$sqlstr="insert into sys_counter_spielerinfo (id,hits,hits_current_day,toc,sportart) values (".$_REQUEST["personid"].",1,1,sysdate(),'tt')";
	} else {
		$hits=$result["0"]["hits"];
		$hits++;
		if ($result["0"]["datum"] != $result["0"]["toc"]) {
			// Datum hat sich geändert ==> Damit müssen alle auf 0 gesetzt werden
			//$sqlstr="update sys_statistic_images set hits_current_day=0";
			//$dummy=doSQL($db,$sqlstr);
			$hits_current=1;
		} else {
			$hits_current=$result["0"]["hits_current_day"];
			$hits_current++;
		}
		$sqlstr="update sys_counter_spielerinfo set hits=".$hits.",hits_current_day=".$hits_current.",toc=sysdate() where id=".$_REQUEST["personid"]." and sportart='tt'";
	}
	$dummy=doSQL($db,$sqlstr);



	closeConnect($db);
?>
</BODY>
</HTML>