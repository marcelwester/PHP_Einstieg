<?php
$nojavascript="yes";
include "inc.php";
include "almMessage.php";

$MESSAGEID="0";
$MESSAGETEXT=utf8_encode("Dies ist eine Meldung  
		über 
		mehrere 
		Zeilen");

almMessage($MESSAGEID,$MESSAGETEXT,"TELEGRAM");


close();

?>