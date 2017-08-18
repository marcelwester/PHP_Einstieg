<?php
include "inc.php";
include "head.php";

// New Counter
if (!isset($_SESSION["count"])) {
	//sys_counter();
	$_SESSION["count"]=1;
}


	

echo '<body text="#000000" bgcolor="'.$gobal_bgcolor.'" link="#FF0000" alink="#FF0000" vlink="#FF0000">';
$site=$_REQUEST["site"];
if (!isset($site)) $site="aktion";
	include "sites.php";

if ($DEBUG=="1") echo $SITE["$site"];
	sitename($SITE["$site"]);

echo "<center><h1><u>".$sysval->get("title")."</u></h1></center>";
if (isset($_SESSION["userid"]))
   echo "<center><h2>- ".$_SESSION["username"]." -</h2></center>";

   
if (isset($_SESSION["userid"])) {
	include $SITE["$site"];
} else
   include $SITE["login"];
   


close();

?>



</body>
</html>


