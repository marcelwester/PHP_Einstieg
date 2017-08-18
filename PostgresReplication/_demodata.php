<?php
include "classes/__init.php";

// NULL Spalten
// select id,coalesce(NULLIF(toc_ts,'2016-09-01 15:30:00'),'1970-01-01 00:00:00') x from company;
//
//

$local=new dbPostgresConnect();
$local->setUser("vol");
$local->setPass("12years");
$local->setHost("127.0.0.1");
$local->setDB("test");
$local->setPort("5432");

$rs = new DbPostgres($local->conn());

$x=rand ( 0 , 100000 );


$indx=0;
for ($i=0; $i<100000;$i++) {
	$x=rand ( 0 , 100000 );
	$y=rand ( 0 , 100000 );
	//$tx=1000000;
	if ($indx==0 || $indx==$tx) {
		if ($indx!=0) {
			$sql="end transaction";
			$rs->query($sql) or die;
			echo " ".$indx;
		}
		$indx=0;
		$tx=rand(100,1400);
		echo "\nnew transaction\n";
		$sql="start transaction";
		$rs->query($sql) or die;
		
	}
	
	$indx++;
	
	$str1=md5($x);
	$str2=md5($y);
	$sql="update reptest1 set name='".$str1."',vorname='".$str2."',toc_ts=current_timestamp where id=".$i;
	
	//$sql="insert into reptest1 (id,name,vorname,alter,toc_ts) values (".$i.",'".$str1."','".$str2."',".($i+2).",current_timestamp)";
	$rs->query($sql) or die;
	echo ".";
}
$sql="end transaction";
$rs->query($sql) or die;

echo "\n";
$local->close();

