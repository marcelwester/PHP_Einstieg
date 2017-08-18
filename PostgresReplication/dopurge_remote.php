<?php
include "classes/__init.php";


$local=new dbPostgresConnect();
$local->setUser("vol");
$local->setPass("12years");
$local->setHost("127.0.0.1");
$local->setDB("test");
$local->setPort("5434");
//$rs=new DbPostgres($local->conn());



$remote=new dbPostgresConnect();
$remote->setUser("vol");
$remote->setPass("12years");
$remote->setHost("127.0.0.1");
$remote->setDB("test");
$remote->setPort("5432");





// check Replication
$r = new CheckRep($local->conn(),$remote->conn());
$r->readStructure();
$r=null;

// Start Replication
$rep = new DoRep($local->conn(),$remote->conn(),false);


$rep->purge();
	

	




?>