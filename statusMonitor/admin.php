<?php 
sitename("admin.php",$_SESSION["groupid"]);
if (!priv())
{
	echo $no_rights;	
} else {

back("aktion");	
$menu=array();
$menu["0"]["descr"]="Monitor";
$menu["0"]["admsite"]="monitor";
$admsite["monitor"]="adm_monitor.php";

$menu["1"]["descr"]="Benutzer-<br>verwaltung";
$menu["1"]["admsite"]="user";
$admsite["user"]="adm_user.php";

$menu["2"]["descr"]="Alarmierungs-<br>Gruppen";
$menu["2"]["admsite"]="group";
$admsite["group"]="adm_group.php";

$menu["3"]["descr"]="Monitor/Gruppe<br>Alarmierung";
$menu["3"]["admsite"]="usermon";
$admsite["usermon"]="adm_usermon.php";



$menu["4"]["descr"]="Optionen";
$menu["4"]["admsite"]="optionen";
$admsite["optionen"]="adm_options.php";

$menu["5"]["descr"]="Info";
$menu["5"]["admsite"]="info";
$admsite["info"]="adm_info.php";

$colspan=6;

echo '<table class="menu" width="80%" align="center">';
echo '<tr>';
$x=round(100/$colspan);
for ($i=0; $i<$colspan; $i++) {
	echo '<td width="'.$x.'%"></td>';
} 
echo '</tr>';

  //menu
  foreach ($menu as $m) {
  	if ($m["admsite"]==$_GET["admsite"]) {
  		table_data($m["descr"]);
  	} else {
  		table_link($m["descr"],"index.php?site=admin&admsite=".$m["admsite"]."&PHPSESSID=".session_id());
  	}
  }
echo '</tr>';
echo '<tr>';
  echo '<td align="center" colspan="'.$colspan.'" border="2">';
  	if (isset($admsite[$_GET["admsite"]]))
     	include $admsite[$_GET["admsite"]];
  echo '</td>';
echo '</table>';



}
?>