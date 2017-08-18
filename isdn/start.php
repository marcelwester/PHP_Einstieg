<?php
include "inc.php";


$sql="insert into isdn_status (toc_ts,status_s,server_s) values (?,?,?)";
$rs->prepare($sql);

/*
$rs->bindColumn(1, "Volker Losch6");
$rs->bindColumn(2, "xxxx");
$rs->bindColumn(3, "dddd");
$rs->bindColumn(4, "80");
$rs->execute();
*/

//Thu May 19 16:21:01 CEST 2016
$format="D M H:i:s T Y";
$t=strtotime("Thu May 19 16:21:01 CEST 2016");
//echo date("d.m.Y H:i:s",$t);

//echo strlen("Thu May 19 15:57:01 CEST 2016");


		

$current_status=false;
$new_status=false;
$DATUM=null;

$filename="/opt/htdocs/dev/isdn/source/checkISDN_rac3.log";
$server="rac3";

//$filename="/opt/htdocs/dev/isdn/source/checkISDN_rac4.log";
//$server="rac4";
$datum=true;
$file = fopen ($filename,'r');
while (!feof($file)) {
	$line= fgets($file);
	if ($line == "ISDN down\n") $new_status=false;
	if ($line == "ISDN up\n") $new_status=true;
	if ((strtotime($line)==true) & $_datum==true) {
		//$DATUM=date("d.m.Y H:i:s",strtotime($line));
		$DATUM=date("Y-m-d H:i:s",strtotime($line));
		//echo $line." - ".$DATUM."\n";
		$_datum=false;
	}
	if ($new_status!=$current_status) {
		if ($new_status==true) $status="an";
		if ($new_status==false) $status="aus";
		$current_status=$new_status;
		echo "$DATUM: ".$status."\n";
		
		$rs->bindColumn(1, $DATUM);
		$rs->bindColumn(2, $status);
		$rs->bindColumn(3, $server);
		$rs->execute();
		
		
	}
	// Nächste Zeile ist eine Datumszeile
	if ($line=="===============================================================\n") {$_datum=true;}
	//echo $line;
}

fclose ($file);

$local->close();
echo "Ende\n";

?>