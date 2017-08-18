<?php

$nojavascript="yes";

include "inc.php";

include "almMessage.php";
include "classes/blackout.php";

$starttime=date('Y-m-d H:i:s');



$indx=0;
$sysval = new SysValues($rs2);

echo "start";


$sql="select id,toc_ts from sys_log_visits order by id";
$rs->prepare($sql);
$rs->execute();
while ($row=$rs->fetchRow()) {
	echo $row["toc_ts"]." ... \n" ;
	exit;
}


	
	
	
?>
