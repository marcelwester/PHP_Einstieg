<?php


/*
$color["0"]="#ff0000";
$color["1"]="#0000ff";
$color["2"]="#00ff00";
$color["3"]="#00ffff";
$color["4"]="#ffffff";
$color["5"]="#ffff00";

$x=array();
$x["a"]=rand(0,5);


$x["farbe_100"]=$color[$x[a]];

echo json_encode($x);
*/

$nojavascript=true;

include "inc.php";

$mon_cl_ok=$sysval->get("color_ok");
$mon_cl_fail=$sysval->get("color_fail");
$mon_cl_timeout=$sysval->get("color_timeout");
$mon_cl_unknown=$sysval->get("color_unknown");

$sessid=$_GET["sessid"];

$sql="select rel from sys_reload where session_id=?";
$rs->prepare($sql);
$rs->bindColumn(1, $sessid);
$rs->execute();
//$sql="select rel from sys_reload where session_id='".$sessid."'";
//$rs->query($sql);
$reload=$rs->getArray();
$reload=intval($reload["0"]["rel"]);



$sql="update sys_reload set toc_ts=sysdate() where session_id=?";
if ($reload==1) {
	$sql="update sys_reload set rel=0,toc_ts=sysdate() where session_id=?";
}
$rs->bindColumn(1, $sessid);
$rs->execute();


$data=array();

$data["datum"] = date("d.m.Y H:i:s");
$data["check"] = "Ok";
if ((time()) - $sysval->get("check_heartbeat") > 120) {
	$data["check"] = "<b>######### ERROR: Checkscript timeout 120s #########</b>";
}  
$data["reload"] = $reload;

//$data["reload"]="yes";
// show monitors
$sql="select m.id,displayname,toc_ts,timeout,status,m.groups from  sys_monitor m LEFT JOIN sys_monitor_status s
			  on monitor_name=name where active=1 order by m.groups,m.indx,m.displayname";
$rs->query($sql);
$indx=1;
$cell_indx=100;
$group="";
while ($row=$rs->fetchRow()) {
	$bgcolor=$mon_cl_unknown;
	$timediff=0;
	if (isset($row["toc_ts"])) {
		$timediff= ( time() - strtotime(date($row["toc_ts"])));
		if ($timediff > $row["timeout"] ) $bgcolor=$mon_cl_timeout;
	}

	if (isset($row["toc_ts"]) && $row["status"]=="1" && $timediff<$row["timeout"]) $bgcolor=$mon_cl_ok;
		
	if (isset($row["status"]) && $row["status"]=="0") $bgcolor=$mon_cl_fail;

	// Array füllen
//	$cnt='<a href="javascript:monitor_popup('.$row["id"].');">';
//	$cnt.=$row["displayname"];
//	$cnt.='<font size="-2"><br>'.date("d.m.Y - H:i:s",strtotime($row["toc_ts"]))."</font>";
//	$cnt.='</a>';
	$data["farbe_".$cell_indx]=$bgcolor;
	$data["txt_".$cell_indx]=$row["displayname"];
	$data["datum_".$cell_indx]=date("d.m.Y - H:i:s",strtotime($row["toc_ts"]));
	$data["id_".$cell_indx]=($row["id"]);
	
	$cell_indx++;
}

echo json_encode($data);



?>