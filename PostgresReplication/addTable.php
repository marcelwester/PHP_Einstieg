<?php
include "classes/__init.php";

$db=new dbPostgresConnect();
$db->setUser("vol");
$db->setPass("12years");
$db->setHost("127.0.0.1");
$db->setDB("test");


$rs=new DbPostgres($db->conn());

$c = new PostRep($db->conn());
// $c->setTables(array("company"));
$c->setTables(array("reptest1"));

//$c->createCommon();
$c->createTable();
$c->createTrigger();
echo $c->getSQL();

$db->close();
?>