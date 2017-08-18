<?php
/*
 * Created on 15.03.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

date_default_timezone_set('Europe/Berlin');

$DEBUG=1;
$LOGVISIT=1;

$DISPLAY_ERRORS=1;
$WRITE_ERRORFILE=1;
$ERRORFILE="/tmp/statusMonitor_error.log";


$START_TRANSACTION="START TRANSACTION";
$COMMIT="COMMIT";
$ROLLBACK="ROLLBACK";

// Sessiontimeout in Sekunden
$SESSIONTIMEOUT=1800;

// constants
//$link='#FF6003';
$link='#BBBBBB';
$link_over='#DDFD00';
$gobal_bgcolor="#DDDDDD";

include "classes/_loader.php";


 
$no_rights="Fehlende Berechtigung ...";

?>