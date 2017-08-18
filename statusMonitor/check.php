<?php

$nojavascript="yes";
include "inc.php";

include "almMessage.php";
include "classes/blackout.php";

$starttime=date('Y-m-d H:i:s');


$indx=0;
while (true) {
	unset($sysval);
	// SysValues benötigt einen exclusive DBMS Resource => die Resource sollte nicht in 
	// einer Schleife genutzt werden, in der die selbe Resource genutzt wird.
	$sysval = new SysValues($rs2);    
	$indx_del=$sysval->get("check_keep_days");
	
	$MESSAGEID=$sysval->get("alm_message_id");
	

	$sysval->get("check_timeout");
	$telegram=$sysval->get("telegram");
	$alm=$sysval->get("alm_manager");
	$mail=$sysval->get("mail");
	$SLEEP=intval(round(($sysval->get("check_timeout") / 1000)));
	if ($SLEEP==0) $SLEEP=5;
	$blackout=new Blackout($sysval->get("blackout"));
	
	echo "\n";
	echo "########################################################################\n";
	echo "Starttime check: ".$starttime."\n";
	echo "  config reload: ".date('Y-m-d H:i:s')."\n";
	echo "          check: ".$SLEEP." Sekunden\n";
	echo "Telegram 1:an 0:aus  : ".$telegram."\n";
	echo "    ALM  1:an 0:aus  : ".$alm."\n";
	echo "   Mail  1:an 0:aus  : ".$mail."\n";
	echo "          Keep days  : ".$sysval->get("keep_days")."\n";
	echo "    Check Keep days  : ".$sysval->get("check_keep_days")."\n";
	echo "           Blackout  : ".$blackout->text."\n";
	echo "########################################################################\n";
	
 
	// Aktive Monitor abfragen
	$sql="select m.id,m.name,m.descr,timeout,toc_ts,s.status,sysdate()   from sys_monitor m, sys_monitor_status s where
	      s.monitor_name=m.name and m.active=1";
	$rs->prepare($sql);
	
	
	$reload_config=true;
	while ($reload_config==true) {
		if ($sysval->get("check_reload",1)==1) {
			$reload_config=false;
			$sysval->set("check_reload",0);
			echo "\ncheck_reload ==> Config saved ==> Neuladen der Konfiguration\n";
		}
		
		if ($blackout->check()==false) {
			if ($rs->execute()) {
				while ($row=$rs->fetchRow()) {
					$sysdate=$row["sysdate()"];
					$toc_ts=$row["toc_ts"];
					$monitorid=$row["id"];
					$row["descr"]=utf8_decode($row["descr"]);
					$msg_descr="\n".$row["descr"]."\n";

								
					if ($row["status"]!=1) {
						if ($sysval->setError($row["name"], "fehler") != 0) {
							$msg="Fehler Statusmonitor: ".$row["name"]."\n";
							if ($telegram==1) almMessage($MESSAGEID, $msg.$msg_descr,"TELEGRAM",$monitorid);
							if ($alm==1) almMessage($MESSAGEID, $msg,"ALM");
							if ($mail==1) almMessage($MESSAGEID, $msg.$msg_descr,"MAIL",$monitorid);
							echo $msg."\n";
						}
					} else {
						if ((strtotime($sysdate) - strtotime($toc_ts)) > $row["timeout"]) {
							if ($sysval->setError($row["name"], "warnung")!= 0) {
								$msg ="Warnung Statusmonitor. Timeout ueberschritten: ".$row["name"].": ";
							    $msg.= (strtotime($sysdate) - strtotime($toc_ts))."\n";
							    if ($telegram==1) almMessage($MESSAGEID, $msg.$msg_descr,"TELEGRAM",$monitorid);
							    if ($alm==1) almMessage($MESSAGEID, $msg,"ALM");
							    if ($mail==1) almMessage($MESSAGEID, $msg.$msg_descr,"MAIL",$monitorid);
							    echo $msg."\n";
							}
									
						} else {
							if ($sysval->setError($row["name"], "ok") != 0) {
								$msg="Statusmonitor Fehler zuruecksetzen: ".$row["name"]." wieder Ok\n";
								if ($telegram==1) almMessage($MESSAGEID, $msg.$msg_descr,"TELEGRAM",$monitorid);
								if ($alm==1) almMessage($MESSAGEID, $msg,"ALM");
								if ($mail==1) almMessage($MESSAGEID, $msg.$msg_descr,"MAIL",$monitorid);
								echo $msg."\n";
							}
							
						}
							 
					} 
				}
		}
		if ($blackout->getStatus()==true ) {
			echo "\nend blackout ".date("d.m.Y H:i:s")."\n";
			$blackout->setStatus(false);
		}
		echo ".";
	} else {
		 // Wenn Blackout  
		if ($blackout->getStatus()==false ) {
			echo "\nstart blackout ".date("d.m.Y H:i:s")."\n";
			$blackout->setStatus(true);
		}
		echo "x";
	 }
	 $sysval->set("check_heartbeat", time());
	 sleep($SLEEP);
	 $indx++;
	 if ($indx>$indx_del) {
	 	$indx=0;
	 	echo "\nPurge Data older than ".$sysval->get("keep_days")." Days...\n";
	 	echo date("d.m.Y H:i:s")."\n";
	 	if (! $rs2->query("delete  from sys_monitor_log where toc_ts < now() - interval ".$sysval->get("keep_days")." DAY")) echo "\nFehler delete sys_monitor_log ...\n ";
	 	if (! $rs2->query("delete  from sys_monitor_logfile where toc_ts < now() - interval ".$sysval->get("keep_days")." DAY")) echo "\nFehler sys_monitor_logfile ...\n ";
	 }
	}
}
$local->close();



?>