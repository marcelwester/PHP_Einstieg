<?php
function usage () {
	echo "\n";
	echo "certcheck.php <DAYS> <CERTFILE>\n\n";
	exit(1);
}


if (!isset($argv["2"])) usage();
if (!isset($argv["1"])) usage();
if (!is_numeric($argv["1"])) usage();



date_default_timezone_set('Europe/Berlin');


$DAY_LIMIT=$argv["1"];
$certfile=$argv["2"];


if (!file_exists($certfile)) {
	echo "$certfile does not exist !\n";
	exit(1);
}
$dump=file_get_contents($certfile);
$cert = openssl_x509_parse($dump);

$endtime=$cert["validTo_time_t"];
$current=strtotime("now");

$exp_day=round(($endtime-$current)/86400);

echo "Days until expire: ".$exp_day." ... CN=".$cert["subject"]["CN"]." ... ";
if ($exp_day <= $DAY_LIMIT) { 
	echo date("d.m.Y H:i:s",$endtime)."  ";
	echo "########### ERROR ###########\n";
	print_r($cert["subject"]);
	echo "\n";
	exit(1);
} else {
	echo "Ok\n";
	exit(0);
}
	

?>