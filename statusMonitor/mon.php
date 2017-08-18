<?php
include "inc.php";
include "head.php";
sitename("monitor_popup.php",$_SESSION["groupid"]);
$fullscreen=true;
echo '<center>';


echo '<h2>- <a href="index.php" target="adm">'.$sysval->get("title").'</a> -</h2>';

echo '<b><div id="datum">'.date("d.m.Y H:i:s").'</div></b>';

include "monitor.php";

$str=md5($_SERVER["UNIQUE_ID"].microtime());

$sql="delete from sys_reload where DATE_ADD(toc_ts,INTERVAL 5 Minute) < sysdate()";
$rs->query($sql) or die;

/*
$sql="insert  into sys_reload (session_id,rel,toc_ts) values ('".$str."',0,sysdate())";
$rs->query($sql) or die;
*/

$sql="insert  into sys_reload (session_id,rel,toc_ts) values (?,0,sysdate())";
$rs->prepare($sql);
$rs->bindColumn(1, $str);
$rs->execute($sql) or die;

$SCRIPT='
<script TYPE="text/javascript">
function table_cnt (id,txt,datum) {
	var ret;
	ret=\'<a href="javascript:monitor_popup(\' + id + \');">\';
	ret = ret + txt;
	ret = ret + \'<font size="-2"><br>\' + datum + \'</font></a>\';
	return ret;
 
};	 	
</script>		
';

echo $SCRIPT;

// Aktualisierung
?>
<script TYPE="text/javascript">
function start(){
	//zahl++;
	getJSON('status_json.php?sessid=<?php echo $str; ?>', function(data) {
	if (data.reload == 1) {
		window.location.reload();
	}	
<?php 
     
     echo "document.getElementById('datum').innerHTML = data.datum;";
     echo "document.getElementById('check').innerHTML = data.check;";
     for ($i=100; $i<$cell_indx; $i++) {
     	echo "document.getElementById('cell_".$i."').style.backgroundColor = data.farbe_".$i.";";
     	//echo "document.getElementById('cell_".$i."').innerHTML = data.text_".$i.";";
     	echo "document.getElementById('cell_".$i."').innerHTML = table_cnt(data.id_".$i.",data.txt_".$i.",data.datum_".$i.");";
     }

?>
	}, function(status) {
		alert('###### Fehler ######.');
	});

	setTimeout('start()',<?php echo $sysval->get("display_refresh"); ?>);
}
	start();
</script>
<?php

echo '</center>';
// page reload
/*
echo '<SCRIPT TYPE="text/javascript">';
	echo 'setTimeout("location.href=\'mon.php\'",120000);';
echo '</SCRIPT>';
*/
echo "<br><br><br>";
echo "   Intervall: ".round($sysval->get("display_refresh")/1000)." sek";
echo "<br>   Blackout: ".$sysval->get("blackout")." Uhr";
echo "<br>   Ladezeit: ".date("d.m.Y H:i:s");
echo '<br>Checkscript Alarmierung: <div style="display: inline;" id="check">-</div>';

echo '</body>';
echo '</html>';
close();
?>
