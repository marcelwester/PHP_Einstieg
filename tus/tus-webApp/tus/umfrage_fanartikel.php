<?php
sitename("umfrage_fanartikel.php",$_SESSION["groupid"]);
// umfrage_fanartikel.php
//
// letzte Änderung : Volker, 25.07.2004
// - Datei erstellt
//

$fanartikel=array(
                        "Anstecknadel",
                        "Autowimpel",
                        "Basecap",
                        "Feuerzeug",
                        "Kaffeebecher",
                        "Schlüsselanhänger");

switch($action)
{

        case 'start':
                //unset($_SESSION["umfrage"]);
echo '<h1>Die Umfrage ist beendet !</h1>';
                echo '<table width="60%" ALIGN="CENTER">';
                echo '<tr><td BGCOLOR="#DDDDDD">';
                echo '<div ALIGN="CENTER"><font size="+1">Umfrage</font></div>';
                echo '</td></tr>';
                echo '<tr><td>';
                echo '<br>';
                echo 'Der TuS Ocholt Fanschal fand bei Euch regen Zuspruch.';
                echo ' Dafür möchten wir uns an dieser Stelle recht herzlich bedanken!';
                echo '<br>Für diese Saison würden wir Euch gerne etwas Neues anbieten.';
                echo '<br>Es stehen mehrere Möglichkeiten zur Auswahl.<br>Klickt einfach auf Euren Favoriten ...';
                echo '</td></tr>';
                echo '</table>';
                echo '<br>';
                echo '<table width="20%" ALIGN="CENTER">';

                foreach ($fanartikel as $row) {
                        echo '<tr>';
                                //table_link($row,"index.php?site=umfrage_fanartikel&artikel=".$row."&action=save&PHPSESSID=".session_id());
                                echo '<td ALIGN="CENTER">'.$row.'</td>';
                        echo '</tr>';
                }

                echo '</table>';
                echo '<br>';

                $sqlstr="select count(*) anz from sys_umfrage";
                        $result=GetResult($db,$sqlstr);
                        $result=GetResult($db,$sqlstr);
                $gesamt=$result["0"]["anz"];
                echo '<br>';
                echo '<table width="40%" ALIGN="CENTER">';
                echo '<tr><td BGCOLOR="#DDDDDD" align="CENTER" colspan="2">';
                        echo '<font size="+1">Auswertung</font>';
                        if (priv("umfrage")) echo '<br>Abgegebene Stimmen:'.$result["0"]["anz"];
                echo '</td></tr>';

                echo '<tr>';
                echo '<td align="CENTER"><b>Artikel<b></td>';
                echo '<td align="CENTER"><b>Stimmen<b></td>';
                echo '</tr>';
                $sqlstr="select artikel,count(artikel) anz from sys_umfrage group by artikel order by anz desc,artikel";
                $result=GetResult($db,$sqlstr);
                foreach ($result as $row) {
                        echo '<tr>';
                                echo '<td align="CENTER">';
                                        echo $row["artikel"];
                                echo '</td>';
                                echo '<td align="CENTER">';
                                        if (priv("umfrage"))
                                                echo $row["anz"].' ('.round($row["anz"]/$gesamt*100,2).'%)';
                                        else
                                                echo round($row["anz"]/$gesamt*100,2).'%';
                                echo '</td>';
                        echo '</tr>';
                }
                echo '</table>';

                echo '<br>';
                //echo '<table width="60%" ALIGN="CENTER">';
                //echo '<tr><td align="CENTER">';
                        echo 'Bei Fragen steht Euch <a HREF="mailto:mirco.stieg@ewetel.net">Mirco Stieg</a> gerne zur Verfügung';
                        echo '<br><b>Die Umfrage ist beendet</b>';
                //echo '</td></tr>';
                //echo '</table>';

                break;
        case 'save':
                /*if (in_array($_REQUEST["artikel"],$fanartikel)) {
                        if (! isset($_SESSION["umfrage"])) {
                                $sqlstr  = "insert into sys_umfrage (ip,artikel,datum) values (";
                                $sqlstr .= "'".$_SERVER["REMOTE_ADDR"]."',";
                                $sqlstr .= "'".$_REQUEST["artikel"]."',";
                                $sqlstr .= "sysdate()".")";
                                $_SESSION["umfrage"]="1";
                                $result=doSQL($db,$sqlstr);
                                if ($result["code"]=="0") {
                                        echo "<br>Stime für <b>".$_REQUEST["artikel"]."</b> wurde gespeichert";
                                }
                        } else {
                                echo "<br>Je Sitzung nur eine Stimme.";
                        }
                } else {
                        echo '<br>Ungültiger Artikel';
                }
                */
                echo '<br>Die Umfrage ist  beendet';
                echo '<br><A HREF="javascript:window.history.back();">Hier klicken, um zurückzukehren</A>';
                echo '<SCRIPT TYPE="text/javascript">';
                        echo 'setTimeout("window.history.back()",1000);';
                   echo '</SCRIPT>';

                break;
}
?>