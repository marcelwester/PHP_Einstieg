<?php
$nojavascript="yes";
include "inc.php";
include "almMessage.php";

$MESSAGEID="0";
$MESSAGETEXT=utf8_encode("Dies ist eine Meldung  
		�ber 
		mehrere 
		Zeilen");

almMessage($MESSAGEID,$MESSAGETEXT,"TELEGRAM");


close();

?>