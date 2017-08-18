<?php
// Usage: 
// mon_upload.php?mon=odb2_backup_orcl2&status=1

/* Aufruf mit curl aus der bash heraus

# ohne logfile
CMD=`curl  http://dev2-vol/dev/statusMonitor/mon_upload.php?mon=videocheck&status=1`
ls *.* |while read LINE; do
	CMD=`curl -F "file=@$LINE" "http://dev1-vol/dev/statusMonitor/mon_upload.php?mon=odb2_backup_orcl2&status=1"`
	if [ "$CMD" = "0" ]; then
	echo "Ok"
	else
		echo "ERROR"
		fi
		done
		-- Beispieldatei aus Datenbank lesen:
		-- select bin1 from rawdata where id =1544 into dumpfile '/tmp/1544.dmp';
		*/

	// MONITOR=waage_net
	// curl -F "file=@${LOGFILE}" "http://192.168.2.8/statusMonitor/mon_upload.php?mon=${MONITOR}&status=${STATUS}"
	// curl "http://127.0.0.1/dev/statusMonitor/mon_upload.php?mon=${MONITOR}&status=${STATUS}"
	// curl -X POST --data "log=Dies ist ein test2" "http://127.0.0.1/dev/statusMonitor/mon_upload.php?mon=${MONITOR}&status=1"
	
	

$nojavascript=true;
include "inc.php";
sitename("mon_upload.php",$_SESSION["groupid"]);

$_SESSION["userid"]="service";


if (isset($_SESSION["userid"])) 
{
   $status= intval($_GET["status"]);
   
   $sql="select id from sys_monitor_status where monitor_name=?";
   $rs->prepare($sql);
   $rs->bindColumn(1, $_GET["mon"]);
   $rs->execute();
   
   if ($rs->execute()) {
	   	$sql=""; $id=0;
	   	if ($row=$rs->fetchRow()) {
	   		$id=intval($row["id"]);
	   		$sql="update sys_monitor_status set toc_ts=sysdate(),status=? where id=?";
	   		// Set Monitor status
	   		$rs->prepare($sql);
	   		$rs->bindColumn(1, $status,PDO::PARAM_INT);
	   		$rs->bindColumn(2, $id,PDO::PARAM_INT);
	   		if ($rs->execute()) {
	   			echo "Ok";
	   		}  else {
	   			echo "failed";
	   		}
	   	} else {
	   		//$sql="insert into sys_monitor_status set toc_ts=sysdate(),status=?,monitor_name=?";
	   		echo "Fehler: Monitor nicht gefunden... ".$_GET["mon"]."\n";
	   		error(999,"StatusMonitor: Monitor ".$_GET["mon"]." not found");
	   	}
	   	
	    // Logging
	   	$sql="select id from sys_monitor where name=?";
	    $rs->prepare($sql);
	    $rs->bindColumn(1, $_GET["mon"]);
	    
	    $rs->execute();
	    $id=0;
	    if ($tmp=$rs->fetchRow()) { 
	        $id = intval($tmp["id"]);
   		}
	    if ($id!=0) {
	    	$sql="insert into sys_monitor_log set toc_ts=sysdate(),mon_id=?,status=?,message=?";
	    	$rs->prepare($sql);
	    	$rs->bindColumn(1, $id,PDO::PARAM_INT);
	    	$rs->bindColumn(2, $status,PDO::PARAM_INT);
	    	$rs->bindColumn(3, "status auf ".$status." gesetzt: ".$_SERVER["REMOTE_ADDR"]);
	    	if (!$rs->execute()) {
	    		echo "<br>Fehler Logging";
	    	}
	    	$lid=$rs->lId();
		 
	    	// save log  
	    	$bindata=null;
	    	if (isset($_POST["log"])) {
	    		$bindata .= $_POST["log"];
	    	}
	    	
	     
		    // save logfile 
	    	if (isset($_FILES['file']['tmp_name'])) {
	    	   $binfile=$_FILES['file']['tmp_name'];
	    	   $bindata .= fread(fopen($binfile, "r"), filesize($binfile));
	    	}

	    	if ($bindata) {
	    		$sql="insert into sys_monitor_logfile set toc_ts=sysdate(),mon_id=?,log_id=?,content=?";
	    		$rs->prepare($sql);
	    		$rs->bindColumn(1, $id,PDO::PARAM_INT);
	    		$rs->bindColumn(2, $lid,PDO::PARAM_INT);
	    		$rs->bindColumn(3, $bindata,PDO::PARAM_LOB);
	    		 
	    		
		    	   if (!$rs->execute()) {
		    	   		echo "Fehler beim Speichern der Logdatei.\n";	
		    	   } else {
		    	   	 // set logfile id in sys_monitor_log
		    	   	 $sql="update sys_monitor_log set logfile_id=".$rs->lId()." where id=".$lid;
		    	   	 $rs->query($sql);
		    	   }
	    	}
	    }
   }
} else {
	echo $no_rights;
} 
close();
    


?>