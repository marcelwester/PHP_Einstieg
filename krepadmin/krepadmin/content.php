


<?php
unset($includefile);
if(!isset($db))
	$menu='db_connection';
else
	$menu=$_REQUEST["menu"];

switch ($menu)
{
	case 'newsetup':
	   $includefile="ora_krep_new.php";
	break;

	case 'gensetup':
	   $includefile="ora_krep_gen.php";
	break;

	case 'db_connection':
	   $includefile="get_db_conn.php";
	break;

	case 'monitor':
	   $includefile="ora_krep_monitor.php";
	break;

	case 'admin':
	   $includefile="ora_krep_admin.php";
	break;

}

if (isset($includefile)) {
    echo '<td style="height:500px" VALIGN=TOP WIDTH="100%" ALIGN="CENTER" BORDER="1">';
           include $includefile;
    echo '</td>';
}

?>