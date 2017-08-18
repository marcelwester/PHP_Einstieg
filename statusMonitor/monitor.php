<style>
	a:link { font-weight:normal; color:black; text-decoration:none; }
    a:hover { font-weight:bold; color:black; text-decoration:none; }
</style>

<SCRIPT LANGUAGE="JavaScript">
<!--
        
        function monitor_popup(monitor_id)
        {
        	var url;
        	url = "../../monitor_popup.php?monitorid="+monitor_id+"&PHPSESSID=<?php echo session_id(); ?>";	        	
        	GB_showCenter('Monitor',url, 480,800);
        }

-->
</SCRIPT>
<?php
sitename("monitor.php",$_SESSION["groupid"]);

$mon_row_cnt=$sysval->get("monitor_row_cnt");
$mon_cl_ok=$sysval->get("color_ok");
$mon_cl_fail=$sysval->get("color_fail");
$mon_cl_timeout=$sysval->get("color_timeout");
$mon_cl_unknown=$sysval->get("color_unknown");

echo $mon_timeout;

if (isset($_SESSION["userid"]) || $fullscreen ) {
	if (! $fullscreen) back("aktion");
	echo '<center>';
		echo '<table width="80%">';
		echo '<tr>';
			$width=round(100 / $mon_row_cnt);
			for ($i=1; $i<=$mon_row_cnt; $i++) {
				echo '<td width="'.$width.'%"></td>';
			}	
		echo '</tr>';
		
		// show monitors
		$sql="select m.id,displayname,toc_ts,timeout,status,m.groups from  sys_monitor m LEFT JOIN sys_monitor_status s
			  on monitor_name=name where active=1 order by m.groups,m.indx,m.displayname";
		$rs->query($sql);
		$indx=1;
		$cell_indx=100;
		$group="";
		while ($row=$rs->fetchRow()) {
			if ($group!=$row["groups"]) {
				$group=$row["groups"];
				if ($indx>1) echo '</tr>';
				echo '<tr><td class="menu" colspan="'.$mon_row_cnt.'" align="center">';
				   echo '<b>'.$group.'</b>';
				echo '</td>';
				echo '</tr>';
				$indx=1;   
			}
			if ($indx==1) echo '<tr>';
			$bgcolor=$mon_cl_unknown;
			$timediff=0;
			if (isset($row["toc_ts"])) {
				$timediff= ( time() - strtotime(date($row["toc_ts"])));
				if ($timediff > $row["timeout"] ) $bgcolor=$mon_cl_timeout;
			}

			if (isset($row["toc_ts"]) && $row["status"]=="1" && $timediff<$row["timeout"]) $bgcolor=$mon_cl_ok;
			
			if (isset($row["status"]) && $row["status"]!="1") $bgcolor=$mon_cl_fail;
			
			echo '<td class="menu" id="cell_'.$cell_indx++.'" bgcolor="'.$bgcolor.'">';
			   echo '<a href="javascript:monitor_popup('.$row["id"].');">';
			   echo $row["displayname"];
			   echo '<font size="-2"><br>'.date("d.m.Y - H:i:s",strtotime($row["toc_ts"]))."</font>";
			   echo '</a>';
			echo '</td>';
			$indx++;
			if ($indx>$mon_row_cnt) {
				echo '</tr>';
				$indx=1;
			}
		}
		
		echo '</table>';
	echo '</center>';
	if (! $fullscreen) back("aktion");
} else {
	echo "keine Berechtigung";
}
?>