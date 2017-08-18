<?php

include "inc.php";
sitename("set_ip_slave.php",$_SESSION["groupid"]);

$action=$_GET["action"];
$urlbase="http://www.tus-ocholt.de/set_ip_master.php";
switch($action) {
	case 'requestsetip':
		$url=$urlbase."?action=request";
		$file = @fopen ($url,"r");
	
		if (trim($file) == "") {
			echo "2. Service out of order";
		} else {
			$i=0;
			while (!feof($file)) {
				$zeile[$i] = fgets($file);
				$i++;
			}
			fclose($file);
		}
		if ($i==1) {
			$currentID=$zeile[0];
			$sqlstr="select val from sys_keys where id=".$currentID;
			$result=getResult($db,$sqlstr);
			$key=$result["0"]["val"];
		
			$url =$urlbase."?action=setip&key=".$key;
			$file = @fopen ($url,"r");
			if (trim($file) == "") {
				echo "3. Service out of order";
			} else {
				$i=0;
				while (!feof($file)) {
					$zeile[$i] = fgets($file);
					$i++;
				}
				fclose($file);
			}
		}
		
	break;

}
closeConnect($db);
?>