<?php

  if ($_SESSION["groupid"] != 10) {
  	 echo $no_rights;
  	 exit;
  }

  if (!isset($_GET["action"])) 
	   $action="start";
  else
	   $action=$_GET["action"];

  sitename("adm_optionen.php",$_SESSION["groupid"]);
	   
  switch ($action) {
  	case 'start':
	
		$url="index.php?site=admin&admsite=info&PHPSESSID=".session_id();
		echo "<h2>Info<h2>";
			echo '<div align="left"><font size="-0.5"><pre>';
			echo '
MONITOR=videocheck
STATUS=1    // 1=OK, 0=Fehler
					
# Mit Logfile
curl -F "file=@${LOGFILE}" "http://192.168.113.134/statusMonitor/mon_upload.php?mon=${MONITOR}&status=${STATUS}"

# Mit Logmessage
curl -X POST --data "log=Dies ist ein test2" "http://192.168.113.134/statusMonitor/mon_upload.php?mon=${MONITOR}&status=${STATUS}"

# Nur Status
curl "http://192.168.113.134/statusMonitor/mon_upload.php?mon=${MONITOR}&status=${STATUS}"';
			echo '</pre></font></div>';
					
			
  	break;
  	
  }  

?>