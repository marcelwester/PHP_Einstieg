

<?php

//// tt_ma_tabelle.php
//// letzte Änderung : 09.04.2007
//// was : - Unterscheidung intern, extern
////

sitename("tt_ma_tabelle.php",$_SESSION["groupid"]);
// Einstellung für intern und extern pruefen
$sqlstr="select ext_tabelle, ext_tabelle_url from tt_saison where id=".$saisonid;
$result=getResult($db,$sqlstr);
if (isset($result)) {
	if ($result["0"]["ext_tabelle"]=="1") {
		echo '<div align="center">';
		echo "<h2>Tabelle anzeigen von:</h2>";
		echo '<a target="tabelle" href="'.$result["0"]["ext_tabelle_url"].'"><br>'.$result["0"]["ext_tabelle_url"];
		echo "</a>";
		echo "<br><br>Es wir ein neues Fenster geöffnet!";
		#echo '<iframe src="'.$result["0"]["ext_tabelle_url"].'" width="100%" height="400">Ihr Browser kann keine eingebetteten Frames anzeigen!</iframe>';
		echo '</div">';
	} else {
		include "tt_ma_tabelle_intern.php";	
	}
}
    
?>