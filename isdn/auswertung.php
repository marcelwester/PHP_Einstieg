<?php
include "inc.php";

$sql="select toc_ts,status_s,server_s from isdn_status order by toc_ts";

$rs->prepare($sql);
$rs->execute();

$current_status="aus";
$new_status=null;


echo "      Start         | gemeldet |        Ende         | gemeldet | Dauer\n";
echo "--------------------+----------+---------------------+----------+---------------\n";
while ($row=$rs->fetchRow()) {

	if ($row["status_s"]=="an") {$time_on=strtotime($row["toc_ts"]); $new_status="an"; $server_an=$row["server_s"]; }
	if ($row["status_s"]=="aus") {$time_off=strtotime($row["toc_ts"]);  $new_status="aus";$server_aus=$row["server_s"];}
	
	if ($current_status != $new_status) {
		if ($new_status=="aus") {
			echo date("d.m.Y H:i:s",$time_on). " | ";
			echo "  ".$server_an. "   | ";
			//echo "  bis  ". " | ";;
			echo date("d.m.Y H:i:s",$time_off). " | ";
			echo "  ".$server_aus. "   | ";
			//echo "    ==>    ";
			echo ($time_off - $time_on)." Sekunden";
			echo "\n";
			//$current_status=$new_status;
		}
		$current_status=$new_status;
	}
	
	
}

//echo "Ende\n";

$local->close();


?>