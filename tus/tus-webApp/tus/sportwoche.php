<?php
//// sportwoche.php
////
//// letzte Änderung : Volker, 01.07.2007
//// was : Erstellung
////

include "inc.php";
focus();
sitename("sportwoche.php",$_SESSION["groupid"]);
$sqlstr = "select value_s from sys_values where name='sportwoche_ip'";
$result= getResult($db,$sqlstr);
if (isset($result)) {
   $currentIp=$result["0"]["value_s"];	
}

?>
<html>
<head>
  <title>www.tus-ocholt.de</title>
</head>
<frameset>
  <?php
   echo '<frame src="http://'.$currentIp.'">';
  ?>

  <noframes>
    <center>
    <h1>TuS Ocholt Sportwoche</h1>
    <p>Ihr Browser unterst&uuml;tzt <b>keine</b> FRAMES. Schalten Sie die FRAME-Unterst&uuml;tzung ein. </p>
    <?php
    	echo '<p>Oder klicken Sie auf <a href="http://'.$currentIp.'">Tus - Ocholt Sportwoche</a>';
    ?>
    </center>
  </noframes>
</frameset>
</html>

<?php
   closeConnect($db);
?>