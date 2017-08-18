<?php
//// tt_ma_spielplan.php
//// letzte Änderung : Volker, 03.07.2004
//// was : - Ganzen Spieltag eintragen
//// letzte Änderung : Volker, 11.02.2004 21:20
//// was : - Edit für Torschützenverwaltung nur bei Spielen mit Ocholter Beteiligung
//// letzte Änderung : Volker, 07.02.2004 23:00
//// was : - Edit für Torschützenverwaltung eingebaut mit Link auf tt_ma_tore.php
//// letzte Änderung : Daniel, 07.02.2004 13:24
//// was : - Bei Spiele, welche noch nicht gespielt wurden, kein Ergebnis angezeigen
////
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
        
        function tabelle_popup(teamid)
        {
        	var url;
        	<?php
        	   echo 'url = "tt_ma_tabelle_popup.php?teamid="+teamid+"&saisonid='.$saisonid.'&PHPSESSID='.session_id().'";';	
        	?>
        	window.open(url,"spiel","width=650, height=550, top=200, left=200, menubar=no, directories=no, resizeable=no, toolbar=no, scrollbars=yes, status=no");
        }


        function popup_info(spielid)
        {
        	var url;
        	<?php
        	   echo 'url = "tt_ma_spielplan_info_popup.php?spielid="+spielid+"&PHPSESSID='.session_id().'";';	
        	?>
        	window.open(url,"spiel","width=700, height=670, top=30, left=50, menubar=no, directories=no, resizeable=no, toolbar=no, scrollbars=yes, status=no");
        }
<?php
if (priv_tt_team($teamid))
{
?>
        function popup(spielid)
        {
                var url;
                if (spielid == 0)
        <?php
                        echo 'url = "tt_ma_spielplan_popup.php?action=add&saisonid='.$saisonid.'&PHPSESSID='.session_id().'";';
                echo ' else ';
                        echo 'url = "tt_ma_spielplan_popup.php?action=edit&spielid="+spielid+"&PHPSESSID='.session_id().'";';
        ?>

                window.open(url,"spiel_edit","width=700, height=550, top=200, left=50, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
        }

        function popup_aufstellung(spielid)
        {
                var url;
        <?php
                        echo 'url = "tt_ma_spielplan_popup_aufstellung.php?action=start&spielid="+spielid+"&PHPSESSID='.session_id().'";';
        ?>

                window.open(url,"spiel_edit","width=700, height=550, top=200, left=50, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
        }



        function popup_tore(spielid)
        {
                 var url;
                 <?php
                      echo 'url = "tt_ma_spielplan_spiele_popup.php?action=edit&spielid="+spielid+"&PHPSESSID='.session_id().'";';
                 ?>
                 window.open(url,"tore","width=750, height=650, top=20, left=20, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
                 //window.open(url,"tore","menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");

        }
        
        function eingabe()
        {
                 var url;
                 <?php
                      echo 'url = "tt_ma_spielplan_eingabe_popup.php?action=add&saisonid='.$saisonid.'&spieltag=1&PHPSESSID='.session_id().'";';
                 ?>
                 window.open(url,"tore","width=750, height=450, top=200, left=20, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
        }

<?php
}
?>
-->
</SCRIPT>

<?php
sitename("tt_ma_spielplan.php",$_SESSION["groupid"]);
 
        if (priv("spiele_add") && priv_tt_team($teamid))
        {
                $colspan_st = 6;
                $colspan_act = 1;
                echo '<CENTER><TABLE WIDTH="40%" BORDER="0">';
                        echo '<TR>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<a href="javascript:popup(0);">';
                                        echo '<IMG SRC="images/new.jpg" BORDER="0" ALT="neu">';
                                        echo '</a>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<a href="javascript:popup(0);">';
                                        echo '<B>neues Spiel eintragen</B>';
                                        echo '</a>';
                                echo '</TD>';
                        echo '</TR>';
		                echo '<TR>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<a href="javascript:eingabe();">';
                                        echo '<IMG SRC="images/new.jpg" BORDER="0" ALT="neu">';
                                        echo '</a>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<a href="javascript:eingabe();">';
                                        echo '<B>Spieltag eintragen</B>';
                                        echo '</a>';
                                echo '</TD>';
                        echo '</TR>';

                echo '</TABLE></CENTER><BR>';
        }
        else
        {
                $colspan_st = 5;
        }
        if (priv("spiele_del") && priv_tt_team($teamid))
        {
                $colspan_st = 8;
                $colspan_act = 3;
        }

        $sql1 = 'select mannschaft_id from tt_zwtab_mannschaft_saison where saison_id = '.$saisonid;
        $result1 = GetResult($db, $sql1);

        $sql2 = 'select * from tt_mannschaft';
        $result2 = GetResult($db, $sql2);

        $x = 0;
        foreach ($result1 as $row1)        // tt_zwtab_mannschaft_saison
        {
                foreach($result2 as $row2)        // tt_mannschaft
                {
                        if ($row2["id"] == $row1["mannschaft_id"])
                        {
                                $mannschaften[$x] = array("id" => $row2["id"]);
                                $mannschaften[$x]["name"] = $row2["name"];
                        }
                }
                $x++;
        }
        $anzteams = $x;

/*
		$sql_st = 'select spieltag from tt_spiele where saison_id = '.$saisonid.' group by spieltag';
		$result_st = getResult($db,$sql_st);
		echo '<CENTER><B>Spieltage</B> : ';
		if (isset($result_st[0]))
		foreach ($result_st as $st)
			echo ' <A HREF="#spieltag'.$st["spieltag"].'">'.$st["spieltag"].'</A>';
		echo '</CENTER><BR>';
*/

        echo '<TABLE WIDTH="100%" BORDER="0">';
                echo '<TR>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                echo '<B>Datum</B>';
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                echo '<B>Heim</B>';
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                echo '<B>Spiele</B>';
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                echo '<B>Auswärts</B>';
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                echo '<B>Anlagen</B>';
                        echo '</TD>';
                        if (priv("spiele_edit") && priv_tt_team($teamid))
                        //if ($_SESSION["groupid"] >= $rechte["spiele"] && ($_SESSION["groupid"] == $admin_id || in_array($teamid, $_SESSION["tusteams"])))
	                    {
	                   		echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD" COLSPAN="'.$colspan_act.'">';
	            				echo '<B>Aktion</B>';
	            			echo '</TD>';
	                    }
                echo '</TR>';

        $sql3 = 'select * from tt_spiele where saison_id = '.$saisonid.' order by grouped,datum asc';
        $result3 = GetResult($db, $sql3);

        // Ocholter Mannschaft identifizieren zwecks Torschützenedit
        $sql6 = 'select id from tt_tus_mannschaft';
        $result6 = GetResult($db,$sql6);

        //$akt_spieltag = 0;
        if (isset($result3[0]))
        foreach ($result3 as $row3) // tt_spiele
        {
                if ($grouped != $row3["grouped"]) {
                	echo '<tr>';
                	echo '<td colspan="6" align="left">';
                		echo '<b>'.$row3["grouped"].'</b>';
                	echo '</td>';
                	echo '<tr>';
                	$grouped=$row3["grouped"];
                }

                echo '<TR>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                echo date("d.m.Y - H:i",strtotime($row3["datum"]));
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                foreach ($mannschaften as $team)
                                        if ($team["id"] == $row3["heim_id"]) {
                                        	echo '<a href="javascript:tabelle_popup('.$team["id"].');">';
                                        	echo $team["name"];
                                        	echo '</a>';
                                        }
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                if ($row3["gespielt"] == 1)
                                echo $row3["heim_tore"].':'.$row3["aus_tore"];
                                                else
                                                        echo '- - -';
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                foreach ($mannschaften as $team)
                                        if ($team["id"] == $row3["aus_id"]) {
                                        	echo '<a href="javascript:tabelle_popup('.$team["id"].');">';
                                        	echo $team["name"];
                                        	echo '</a>';
                                        }
                        echo '</TD>';

                        $anlage = '';
                        $sql4 = 'select count(*) from tt_spiele_saetze where spiel_id = '.$row3["id"];
                        $result4 = getResult($db, $sql4);
                        if ($result4[0]["count(*)"] > 0)
                                $anlage = 'SI';

                        $sql5 = 'select count(*) from tt_sp_bericht where spiel_id = '.$row3["id"];
                        $result5 = getResult($db, $sql5);
                        if ($result5[0]["count(*)"] > 0)
                        {
                                if (strlen($anlage) > 0)
                                        $anlage .= ' & SB';
                                else
                                        $anlage = 'SB';
                        }
                        $sql7 = 'select count(*) from sys_images where kategorie=9 and linked = '.$row3["id"];
                        $result7 = getResult($db, $sql7);
                        if ($result7[0]["count(*)"] > 0)
                        {
                                if (strlen($anlage) > 0)
                                        $anlage .= ' & F';
                                else
                                        $anlage = 'F';
                        }
                        
                        if ($row3["gespielt"]==0) {
                        	 $sql8 = 'select count(*) from tt_spiele where';
                        	$sql8 .= ' (heim_id='.$row3["heim_id"].' or aus_id='.$row3["heim_id"].') and ';
                        	$sql8 .= ' (heim_id='.$row3["aus_id"].' or aus_id='.$row3["aus_id"].') and gespielt=1';
                        	$result8 = getResult($db, $sql8);
                        	if ($result8[0]["count(*)"] > 0)
                        	{
                                	if (strlen($anlage) > 0)
                                        	$anlage .= ' & V';
                                	else
                                        	$anlage = 'V';
                        	}
                        }
                        
                        
                        
                        
                        if (strlen($anlage) > 0)
                        {
                                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD" onMouseOver="this.style.backgroundColor=\'#AAAAAA\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\'#DDDDDD\';" onClick="location.href=\'javascript:popup_info('.$row3["id"].');\'">';
                                echo $anlage;
                                echo '</TD>';
                        }
                        else
                        {
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo 'keine';
                                echo '</TD>';
                        }
                        //if ($_SESSION["groupid"] >= $rechte["spiele"] && ($_SESSION["groupid"] == $admin_id || in_array($teamid, $_SESSION["tusteams"])))
                        if (priv("spiele_edit") && priv_tt_team($teamid))
                        {
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                // Bemerkung mit * kennzeichnen, wenn eine vorhanden ist	         
                                if (! (($row3["bemerkung"]=="") or (! isset($row3["bemerkung"]))))
                                  	echo "*";
                                  	
                                echo '<a href="javascript:popup('.$row3["id"].');">';
                                        echo '<IMG SRC="images/edit.jpg" BORDER="0" ALT="Bearbeiten">';
                                echo '</a>';
  				
                                // Prüfen ob Ocholter Mannschaft beteiligt ist
                                $tusspiel="0";
                                foreach ($result6 as $tus_mannschaft)
                                {
                                  if (($row3["heim_id"] == $tus_mannschaft["id"]) || ($row3["aus_id"] == $tus_mannschaft["id"]))
                                      $tusspiel="1";
                                }
                                if ($tusspiel == "1" && priv("spiele_edit") && priv_tt_team($teamid))
                                {
	                                  echo '&nbsp;&nbsp;';
	                                  echo '<a href="javascript:popup_aufstellung('.$row3["id"].');">';
	                                  echo '<IMG SRC="images/tt_1.jpg" BORDER="0" ALT="Aufstellung">';
	                                  echo '</a>';
                                	  if ($row3["aufstellung"]==1) {
                                	  	echo '&nbsp;&nbsp;';
	                                  	echo '<a href="javascript:popup_tore('.$row3["id"].');">';
	                                  	echo '<IMG SRC="images/tore.jpg" BORDER="0" ALT="Spiele">';
	                                  	echo '</a>';
	                                  }
                                }
                                echo '</TD>';
                                        }
                                        if (priv("spiele_del") && priv_tt_team($teamid))
                                        {
                                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                                        echo '<INPUT TYPE="CHECKBOX" ID="chk'.$row3["id"].'" onClick="enableDelete(this.id,\'del'.$row3["id"].'\',\'emp'.$row3["id"].'\')" />';
                                                echo '</TD>';
                                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                                        echo '<DIV ID="emp'.$row3["id"].'" STYLE="display:block;">';
                                                                echo '<IMG SRC="images/empty.gif" BORDER="0" HEIGHT="16" WIDTH="16">';
                                                        echo '</DIV>';
                                                        echo '<DIV ID="del'.$row3["id"].'" STYLE="display:none;">';
                                                                echo '<A HREF="index.php?site=tt_team&action=spiele&do=del&spielid='.$row3["id"].'&teamid='.$_REQUEST["teamid"].'&saisonid='.$_REQUEST["saisonid"].'&PHPSESSID='.session_id().'">';
                                                                echo '<IMG SRC="images/del.gif" BORDER="0" HEIGHT="16" WIDTH="16">';
                                                                echo '</A>';
                                                        echo '</DIV>';
                                                echo '</TD>';
                                        }
                echo '</TR>';
        }
        echo '</TABLE>';
        echo '<CENTER>SB = Spielbericht; SI = Spielinfo; F = Fotos; V = Vorbericht</CENTER>';
?>
