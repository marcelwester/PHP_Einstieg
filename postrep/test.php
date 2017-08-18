<?php
include "classes/__init.php";

$db=new dbPostgresConnect();
$db->setUser("vol");
$db->setPass("12years");
$db->setHost("127.0.0.1");
$db->setDB("test");


$rs=new DbPostgres($db->conn());


/*
$sql="select id,name,address from company limit 10";
$rs->query($sql);

while ($row=$rs->fetchRow()) {
	echo $row["name"]."\n";
}

echo $rs->count();
echo "\n";

echo "\n";

$sql="select column_name from information_schema.columns where table_name='company' and table_schema='public'";
$rs->query($sql);
while ($row=$rs->fetchRow()) {
	echo $row["column_name"]."\n";
}
echo "\n";
echo "\n";
*/

$c = new PostRep($db->conn());
$c->tables = array("company","sys_images");

$c->createCommon();
$c->createTable();
$c->createTrigger();
echo $c->getSQL();

$db->close();
?>