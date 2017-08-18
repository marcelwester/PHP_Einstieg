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






/*
$c = new PostRep($remote->conn());
$c->tables = array("company","department");
$c->createCommon();
$c->createTable();
$c->createTrigger();
//$c->cleanRep();
echo $c->getSQL();
exit;
*/

// check Replication
$r = new CheckRep($local->conn(),$remote->conn());
$r->readStructure();
$r=null;

// read Structure
/*
echo "Structure\n";
$rep = new ReadStructure($local->conn());

foreach ($rep->getTables() as $table) {
	echo "\n".$table.": ";
	echo $rep->getPrimaryKeyList($table)."\n";
	foreach ($rep->getColumns($table) as $col) {
		echo "   ".$col."   -   ";
		echo $rep->getColumnType($table, $col)." - ";
		echo $rep->getDataSeperator($table, $col)."\n";
	}
	
}
*/

//print_r($rep->getColumns("company"));

//print_r($r->getStructure());

// Start Replication
$rep = new DoRep($local->conn(),$remote->conn());
$rep->bwoptimization(true);   // Beim Update werden nur die geänderten Zeilen übertragen

while (true) {
	$rep->push();
	sleep(1);
}
	




?>