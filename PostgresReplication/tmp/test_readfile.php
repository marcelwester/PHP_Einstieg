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



$id=null;
$name=null;


$cnt = file_get_contents('/tmp/kill');

echo strlen($cnt);

$id=1;
$sql="select max(id) id from sys_images";
$rs->query($sql);
if ($row=$rs->fetchRow()) {
	$id=$row["id"];
}

$id++;

// Speichern eines Blobs
$sql="insert into sys_images (id,name,blob) values (".$id.",'tar php','".pg_escape_bytea($cnt)."')";
$rs->query($sql);

exit;


//$sql="SET bytea_output=escape";
//$rs->query($sql);

$sql="select id,name,blob from sys_images  where id=41";
$rs->query($sql);


while ($row=$rs->fetchRow()) {
	//if (is_resource($row["blob"])) $cnt=fgets($row["blob"]);
	$cnt=null;
	while (!feof($row["blob"])) {
		$cnt.=fgetc($row["blob"]);
	}
	
	//echo "DATA: ".$row["id"]." ".$row["name"]." - ".$cnt."\n";
	echo strlen($cnt);
	$sql="insert into sys_images (id,name,blob) values (43,'Kopie von 41','".pg_escape_bytea($cnt)."')";
	$rs->query($sql);	
}

exit;





$cnt='ÄÖÜ ß $$ \\';
$cnt=pg_escape_bytea($cnt);
echo "\n".strlen($cnt);

$cnt=pg_unescape_bytea($cnt);
echo "\n".strlen($cnt);


exit;


$sql="insert into sys_images (id,name,blob) values (?,?,?)";
$rs->prepare($sql);
$rs->bindColumn(1, 37);
$rs->bindColumn(2, $cnt);
$rs->bindColumn(3, $cnt,true);
$rs->execute();

// echo $cnt;
// $sql = "insert into sys_images (id,name,blob) values (4,'#Name','".$cnt."')";
// $rs->query($sql);



$local->close();
?>