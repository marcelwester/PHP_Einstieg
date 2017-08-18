<?php

include "inc.php";
sitename("set_ip.php",$_SESSION["groupid"]);

$action=$_GET["action"];
$urlbase="http://www.tus-ocholt.de/set_ip.php";
switch($action) {
	case 'show':
		$sqlstr="select value_s from sys_values where name='sportwoche_ip'";
		$result=getResult($db,$sqlstr);
		$currentIP = $result["0"]["value_s"];
		
		if ($_SERVER[REMOTE_ADDR]!=$currentIP) {
		  echo '<br>'.$_SERVER[REMOTE_ADDR];
		} else {
		  echo "ok";
		}
	break;

	case 'request':
		$sqlstr="select id from sys_keys where used=0 limit 100";
		$result=getResult($db,$sqlstr);
		$currentID=$result[rand(0,99)]["id"];
		echo $currentID;
		$sqlstr = "update sys_values set value_i=".$currentID." where name='sportwoche_ip'";
		doSQL($db,$sqlstr);
		mlog ("request angefordert");
	break;	

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
		
	case 'setip':
		$key=$_GET["key"];
		$sqlstr ="select val,v.value_i from sys_keys k,sys_values v where ";
		$sqlstr.="v.name='sportwoche_ip' and ";
		$sqlstr.="v.value_i=k.id and v.value_i>0";
		$result=getResult($db,$sqlstr); 
		// Value zurücksetzen um weitere Versuche zu unterbinden
		$sqlstr="update sys_values set value_i=-1 where name='sportwoche_ip'";
		doSQL($db,$sqlstr);
		if (isset($result)) {
			$currentKEY=$result["0"]["val"];
			if ($currentKEY==$key) {
			   // Key entwerten
			   $keyID=$result["0"]["value_i"];
			   $sqlstr="update sys_keys set used=1 where id=".$keyID;
			   $result=doSQL($db,$sqlstr);
			   // IP akutalisieren
			   $sqlstr="update sys_values set value_s='".$_SERVER[REMOTE_ADDR]."' where name='sportwoche_ip'";
			   $result=doSQL($db,$sqlstr);
			   mlog("SportwochenIP auf ".$_SERVER[REMOTE_ADDR]." mit keyID ".$keyID." aktualisiert");
			} else {
				mlog("Fehler Akualisierung SportwochenIP, Keys stimmen nicht überein");
			}
			
		} else {
			echo "Fehler";	
			mlog("Fehler Aktualisierung SportwochenIP, keyID fehlerhaft");
		}
		
	break;

}
closeConnect($db);
?>