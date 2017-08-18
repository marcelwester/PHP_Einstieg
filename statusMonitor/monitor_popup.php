<?php
include "inc.php";
include "head.php";
sitename("monitor_popup.php",$_SESSION["groupid"]);
echo '<center>';
if (isset($_SESSION["userid"]) || true) 
{
   if (!isset($_GET["logfileid"])) {
	   $mon_id=intval($_GET["monitorid"]);
	   // Anzeige der Description des Monitors
	   $sql="select descr from sys_monitor where id=".$mon_id;
	   if ($rs->query($sql)) {
	   	  if ($row=$rs->fetchRow()) {
	   	  	echo '<pre>'.$row["descr"].'</pre>';
	   	  }
	   } 
	
	   $sql="select id,message,toc_ts,status,logfile_id from sys_monitor_log where
	        mon_id=".$mon_id."
	        order by toc_ts desc";
	   
	   if ($_GET["all"]!="yes") {
	   		echo '<a href="'.$_SERVER["REQUEST_URI"].'&all=yes">Alle Daten anzeigen</a>';
	   		$sql="select id,message,toc_ts,status,logfile_id from sys_monitor_log where
	        mon_id=".$mon_id."
	        order by toc_ts desc limit ".$sysval->get("cnt_log_view");
	   }  
	   
	   // Anzeige der letzten 20 Logeinträge
/*
	   $sql="select id,message,toc_ts,status,logfile_id from sys_monitor_log where
	        mon_id=".$mon_id."
	        order by toc_ts desc limit ".$sysval->get("cnt_log_view");
	        */
	   if ($rs->query($sql)) {
	   	  echo '<table class="layout">';
	   	  echo '<tr>';
	   	  table_data("<b>Datum</b>");
	   	  table_data("<b>Status</b>");
	   	  table_data("<b>Message</b>");
	   	  table_data("<b>Logfile</b>");
	   	  while ($row=$rs->fetchRow()) {
	   	  	echo '<tr>';
	   	  	table_data(date("H:i:s d.m.Y",strtotime($row["toc_ts"])));
	   	  	$bgc=$sysval->get("color_unknown");
	   	  	if ($row["status"]==0) $bgc=$sysval->get("color_fail");
	   	  	if ($row["status"]==1) $bgc=$sysval->get("color_ok");
	   	  	table_data($row["status"],"center",$bgc);
	   	  	table_data($row["message"]);
	   	  	if (intval($row["logfile_id"])>0) {
	   	  		table_link("show", "monitor_popup.php?logfileid=".$row["logfile_id"]);
	   	  	} else {
	   	  		table_data("none");
	   	  	}
	   	  	echo '</tr>';
	   	  }
	   	  echo '</table>';
	   }
   } else {
   	 // Show Logfile
   	 echo '<br><INPUT TYPE="button" VALUE="Zur&uuml;ck" onClick="history.back();"><br><br>';
   	 $logfile_id=intval($_GET["logfileid"]);
   	 $sql="select toc_ts,content from sys_monitor_logfile where id=".$logfile_id;

   	 $rs->query($sql);
   	 if ($row=$rs->fetchRow()) {
 	    echo '<table class="layout" width="100%">';
 	       echo '<tr><td align="left" class="layout" width="100%">'; 
   	 			echo '<pre>'.$row["content"].'</pre>';
   	 		echo '</td></tr>';
   	 	echo '</table>';
   	 	
   	 }
   }
   
   echo '<br><a href="'.$_SERVER["REQUEST_URI"].'">Reload</a>';
   
   
   
   echo '<br><br><INPUT TYPE="button" VALUE="Abbrechen" onClick="parent.parent.GB_CURRENT.hide();">';
} else {
	echo $no_rights;
} 
echo '</center>';
echo '</body>';
echo '</html>';
close();
    


?>