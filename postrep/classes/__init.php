<?php
include "classes/DbPostgresConnect.php";
include "classes/DbPostgres.php";
include "classes/PostRep.php";
include "classes/DoRep.php";

$DEBUG=1;
$WRITE_ERRORFILE=0;

function error ($errornumber,$errortext) {
	global $DEBUG,$WRITE_ERRORFILE,$ERRORFILE;
	if ($WRITE_ERRORFILE==1) {
		$fp = fopen($ERRORFILE, 'a');
		fwrite($fp,date('Y-m-d H:i:s')."|".$errornumber."|".$errortext."\n");
		echo date('Y-m-d H:i:s')."|".$errornumber."|".$errortext."\n";
		fclose($fp);
	}

	if ($DEBUG==1) {
		echo "#".$errornumber.": ".$errortext;
	}
}


?>