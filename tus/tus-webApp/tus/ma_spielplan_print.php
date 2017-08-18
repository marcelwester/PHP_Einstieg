<?php
//// ma_spielplan_print.php
//// letzte Änderung : Volker, 11.02.2006
//// was : Erstellung
////

include "inc.php";
focus();
?>
<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e - Spielplan</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">
<?php
$saisonid=$_GET["saisonid"];

sitename("ma_spielplan_print.php",$_SESSION["groupid"]);

     // Spielzeit lesen
     $sqlstr ="select liga,spielzeit from fb_saison where id=".$saisonid;
     $result = GetResult($db, $sqlstr);
     $liga=$result[0]["liga"];
     $spielzeit=$result[0]["spielzeit"];

	
     echo '<TABLE WIDTH="80%" BORDER="0" align="center">';
     echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
     echo '<font size="+1"><B>Spielplan</B></font><BR><font size="+1">'.$liga.' - '.$spielzeit.'</font></TD></TR>';
     echo '</TABLE>';
          echo '<br>';
           echo '<input type="image" title="Drucken" src="./images/Printer.gif" onClick="print();">';
          echo' <br>'; 
		
        echo '<TABLE WIDTH="100%" BORDER="0">';
                echo '<TR>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                echo '<B>Datum</B>';
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                echo '<B>Heim</B>';
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                echo '<B>Auswärts</B>';
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                echo '<B>Tore</B>';
                        echo '</TD>';
                echo '</TR>';

		$sqlstr  = "select m.name,m.id from fb_mannschaft m,fb_zwtab_mannschaft_saison where ";
		$sqlstr .= "saison_id=".$saisonid." and ";
		$sqlstr .= "mannschaft_id=m.id";
		$mannschaften=getResult($db,$sqlstr);

        $sql3 = 'select * from fb_spiele where saison_id = '.$saisonid.' order by spieltag asc,datum asc';
        $result3 = GetResult($db, $sql3);
  		
  		$akt_spieltag=0;
        
        if (isset($result3[0]))
        foreach ($result3 as $row3) // fb_spiele
        {
                if ($row3["spieltag"] > $akt_spieltag)
                {
                        $akt_spieltag = $row3["spieltag"];
                        $sql_descr  = "select descr from fb_spieltag_descr where ";
                        $sql_descr .= "spieltag = ".$row3["spieltag"]." and ";
                        $sql_descr .= "saison_id = ".$saisonid;
                        $descr = getResult($db,$sql_descr);
                        echo '<TR><TD CLASS="NONE" ALIGN="LEFT" COLSPAN="'.$colspan_st.'" BGCOLOR="#FFFFFF"><A NAME="spieltag'.$row3["spieltag"].'"><B>'.$row3["spieltag"].'. Spieltag</B></A>';
                        echo '</TD></TR>';
                }
                echo '<TR>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                echo date("d.m.Y - H:i",strtotime($row3["datum"]));
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                foreach ($mannschaften as $team)
                                        if ($team["id"] == $row3["heim_id"]) {
                                        	echo $team["name"];
                                        }
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                foreach ($mannschaften as $team)
                                        if ($team["id"] == $row3["aus_id"]) {
                                        	echo $team["name"];
                                        }
                        echo '</TD>';
                        echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                if ($row3["gespielt"] == 1)
                                	echo $row3["heim_tore"].':'.$row3["aus_tore"];
                                else
                                    echo '&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp; ';
                        echo '</TD>';
		}
        echo '</TABLE>';
?>
</BODY>
</HTML>