<?php

  if ($_SESSION["groupid"] != 10) {
  	 echo $no_rights;
  	 exit;
  }

  if (!isset($_GET["action"])) 
	   $action="start";
  else
	   $action=$_GET["action"];

sitename("adm_usermon.php",$_SESSION["groupid"]);
$baseurl="index.php?site=admin&admsite=usermon&PHPSESSID=".session_id();



  switch ($action) {
  	case 'start':
		echo "<br><b>&Uuml;bersicht<br>Monitor - Gruppe - Alarmierung</b><br>";

		// Lesen aller aktiven Monitore und deren Alarmierungsempfänger
		$sql="select m.name monitor,m.active monitoractive,g.name gruppe,g.disable groupdisable,u.disable userdisable, concat(u.name,', ',u.vorname) name
			  from sys_monitor m,sys_monitor_group mg,sys_user_group ug,sys_groups g, sys_users u
			  where
			  m.id=mg.monitorid and
			  mg.groupid=ug.groupid and
			  ug.groupid=g.groupid and
			  ug.userid=u.userid and
			  u.userid>1
			  order by m.name,gruppe";
		$rs->prepare($sql);
		$rs->execute();
		$t=new Table("alm", array("Monitor","Gruppe","Benutzer"));

		while ($row=$rs->fetchRow()) {
			$t->startRow();
			
			$userdisable=false; $groupdisable=false; $monitordisable=false;

			$cnt=$row["monitor"];
			if ($row["monitoractive"]==0) {$cnt='<font color="#AAAAAA">'.$row["monitor"].'</font>'; $monitordisable=true; $groupdisable=true; $userdisable=true; }
			$t->addCol($cnt);
			
			$cnt=$row["gruppe"];
			if ($row["groupdisable"]==1 || $monitordisable==true ) {$cnt='<font color="#AAAAAA">'.$row["gruppe"].'</font>'; $userdisable=true; }
			$t->addCol($cnt);
			
			
			$cnt=$row["name"];
			if ($row["userdisable"]==1 || $userdisable==true || $monitordisable==true) $cnt='<font color="#AAAAAA">'.$row["name"].'</font>';
			$t->addCol($cnt);
			
			$t->endRow();
		}
		$t->endTable();
		$t->showTable();
	break;
  }  

?>