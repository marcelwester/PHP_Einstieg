<SCRIPT LANGUAGE="JavaScript">
<!--
        
        function tabelle_popup(teamid)
        {
        	var url;
        	<?php
        	   echo 'url = "ma_tabelle_popup.php?teamid="+teamid+"&saisonid='.$saisonid.'&PHPSESSID='.session_id().'";';	
        	?>
        	window.open(url,"spiel","width=650, height=550, top=200, left=200, menubar=no, directories=no, resizeable=no, toolbar=no, scrollbars=yes, status=no");
        }

        function tabelle_statistik(teamid)
        {
        	var url;
        	<?php
        	   echo 'url = "ma_tabellenstatistik_popup.php?teamid="+teamid+"&saisonid='.$saisonid.'&PHPSESSID='.session_id().'";';	
        	?>
        	window.open(url,"statistik","width=650, height=500, top=200, left=200, menubar=no, directories=no, resizeable=no, toolbar=no, scrollbars=no, status=no");
        }




-->
</SCRIPT>


<?php

//// ma_tabelle.php
//// letzte Änderung : Daniel, 07.02.2004 13:19
//// was : - Spiele, welche noch nicht gespielt wurden werden nicht berücksichtigt.
////


////////////////////////
//// Tabelle anlegen
////////////////////////
sitename("ma_tabelle.php",$_SESSION["groupid"]);
    $tabelle = array();

    $sql1 = 'select mannschaft_id from fb_zwtab_mannschaft_saison where saison_id = '.$saisonid;
    $result1 = GetResult($db, $sql1);

    $sql2 = 'select * from fb_mannschaft';
    $result2 = GetResult($db, $sql2);

    $x = 0;
    foreach ($result1 as $row1)        // fb_zwtab_mannschaft_saison
    {
            foreach($result2 as $row2)        // fb_mannschaft
            {
                    if ($row2["id"] == $row1["mannschaft_id"])
                    {
                            $tabelle[$x] = array("id" => $row2["id"]);
                            $tabelle[$x]["name"] = $row2["name"];
                            $tabelle[$x]["punkte"] = 0;
                            $tabelle[$x]["spiele"] = 0;
                            $tabelle[$x]["tore"] = 0;
                            $tabelle[$x]["gegentore"] = 0;
                            $tabelle[$x]["siege"] = 0;
                            $tabelle[$x]["niederlagen"] = 0;
                    }
            }
            $x++;
    }
    $anzteams = $x;

////////////////////////
//// Tabelle füllen
////////////////////////

    $sql3 = 'select * from fb_spiele where saison_id = '.$saisonid.' and gespielt = 1';;
    $result3 = GetResult($db, $sql3);
    if (isset($result3[0]))
    foreach ($result3 as $row3) // fb_spiele
    {
            $y = 0;
            while ($y < $anzteams)
            {
                    if ($tabelle[$y]["id"] == $row3["heim_id"])                ////////// Heimmannschaft
                    {
                            $tabelle[$y]["spiele"]++;
                            $tabelle[$y]["tore"] += $row3["heim_tore"];
                            $tabelle[$y]["gegentore"] += $row3["aus_tore"];
                            if ($row3["heim_tore"] > $row3["aus_tore"]) // Sieg
                            {
                                    $tabelle[$y]["punkte"] += 3;
                                    $tabelle[$y]["siege"]++;
                            }
                            if ($row3["heim_tore"] < $row3["aus_tore"]) // Niederlage
                                    $tabelle[$y]["niederlagen"]++;
                            if ($row3["heim_tore"] == $row3["aus_tore"]) // Unentschieden
                                    $tabelle[$y]["punkte"]++;
                    }
                    if ($tabelle[$y]["id"] == $row3["aus_id"])                ////////// Auswärtsmannschaft
                    {
                            $tabelle[$y]["spiele"]++;
                            $tabelle[$y]["tore"] += $row3["aus_tore"];
                            $tabelle[$y]["gegentore"] += $row3["heim_tore"];
                            if ($row3["heim_tore"] < $row3["aus_tore"]) // Sieg
                            {
                                    $tabelle[$y]["punkte"] += 3;
                                    $tabelle[$y]["siege"]++;
                            }
                            if ($row3["heim_tore"] > $row3["aus_tore"]) // Niederlage
                                    $tabelle[$y]["niederlagen"]++;
                            if ($row3["heim_tore"] == $row3["aus_tore"]) // Unentschieden
                                    $tabelle[$y]["punkte"]++;
                    }
                    $y++;
            }
    }

////////////////////////
//// Tabelle sortieren
////////////////////////

    do
    {
            $getauscht = 'nein';
            $y = 0;
            while ($y < $anzteams-1)
            {
                    if ($tabelle[$y]["punkte"] < $tabelle[$y+1]["punkte"])
                    {
                            $dummy = $tabelle[$y];
                            $tabelle[$y] = $tabelle[$y+1];
                            $tabelle[$y+1] = $dummy;
                            $getauscht = 'ja';
                    }
                    if ($tabelle[$y]["punkte"] == $tabelle[$y+1]["punkte"])
                    {
                            if ($tabelle[$y]["tore"]-$tabelle[$y]["gegentore"] < $tabelle[$y+1]["tore"]-$tabelle[$y+1]["gegentore"])
                            {
                                    $dummy = $tabelle[$y];
                                    $tabelle[$y] = $tabelle[$y+1];
                                    $tabelle[$y+1] = $dummy;
                                    $getauscht = 'ja';
                            }
                            if ($tabelle[$y]["tore"]-$tabelle[$y]["gegentore"] == $tabelle[$y+1]["tore"]-$tabelle[$y+1]["gegentore"])
                            {
                                    if ($tabelle[$y]["tore"] < $tabelle[$y+1]["tore"])
                                    {
                                            $dummy = $tabelle[$y];
                                            $tabelle[$y] = $tabelle[$y+1];
                                            $tabelle[$y+1] = $dummy;
                                            $getauscht = 'ja';
                                    }
                            }
                    }
                    $y++;
            }
    }while($getauscht == 'ja');



////////////////////////
//// Tabelle ausgeben
////////////////////////

    echo '<TABLE WIDTH="100%" BORDER="0">';
            echo '<TR>';
                    echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                            echo '<B>#</B>';
                    echo '</TD>';
                    echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                            echo '<B>Mannschaft<sup>1</sup></B>';
                    echo '</TD>';
                    echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                            echo '<B>Spiele<sup>2</sup></B>';
                    echo '</TD>';
                    echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                            echo '<B>Punkte</B>';
                    echo '</TD>';
                    echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                            echo '<B>Diff.</B>';
                    echo '</TD>';
                    echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                            echo '<B>Tore</B>';
                    echo '</TD>';
                    echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                            echo '<B>S-U-N</B>';
                    echo '</TD>';
            echo '</TR>';

            $x=1;
            foreach($tabelle as $team)
            {
                    echo '<TR>';
                            echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                    echo $x.'.';
                            echo '</TD>';
                            echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                    echo '<a href="javascript:tabelle_popup('.$team["id"].');">';
                                     echo $team["name"];
                                    echo '</a>';
                            echo '</TD>';
                            echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                    echo '<a href="javascript:tabelle_statistik('.$team["id"].');">';
                                     echo $team["spiele"];
                                    echo '</a>';

                            echo '</TD>';
                            echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                    echo $team["punkte"];
                            echo '</TD>';
                            echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                    echo $team["tore"]-$team["gegentore"];
                            echo '</TD>';
                            echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                    echo $team["tore"].':'.$team["gegentore"];
                            echo '</TD>';
                            echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                    $unentschieden = $team["spiele"] - $team["siege"];
                                    $unentschieden = $unentschieden - $team["niederlagen"];
                                    echo $team["siege"].'-'.$unentschieden.'-'.$team["niederlagen"];
                            echo '</TD>';
                    echo '</TR>';
                    $x++;
            }
    echo '</TABLE>';
    echo "<br>";
    echo "<br><sup>1</sup>Spielplan der Mannschaft";
    echo "<br><sup>2</sup>Tabellenstatistik über die Spieltage";
    

    
?>