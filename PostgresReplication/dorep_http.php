<?php
include "classes/__init.php";


/* Zeiten
 *    499 Transaktionen
 *    update company set toc_ts=current_timestamp where id>1000 and id <1500;
 *    force = false
 *    bwoptimization = true
 *    
 *    AC => 58 Sekunden
 *    OL Netzwerk (geroutet, 2 Hops mit Masquerading) Hostonly => Kisters Hausnetz (10.113.11.0/24) ==> 7 Sekunde
 *    lokales Netz (selbes subnet) ==> 1.5 Sekunden
 *    localhost (beide Server sind auf derselben Maschine) =>   weniger als 1 Sekunde (0.7-0.9)
 *    
 */



$local=new dbPostgresConnect();
$local->setUser("vol");
$local->setPass("12years");
$local->setHost("127.0.0.1");
$local->setDB("test");
$local->setPort("5432");
//$rs=new DbPostgres($local->conn());



$remote=new dbPostgresConnect();
$remote->setUser("vol");
$remote->setPass("12years");
$remote->setHost("127.0.0.1");
$remote->setDB("test");
$remote->setPort("5432");
$remoteurl="http://127.0.0.1/dev/PostgresReplication/remote.php";



/*
$remote=new dbPostgresConnect();
$remote->setUser("vol");
$remote->setPass("12years");
$remote->setHost("10.8.0.59");
$remote->setDB("test");
$remote->setPort("5432");
*/

/*
$remote=new dbPostgresConnect();
$remote->setUser("vol");
$remote->setPass("12years");
$remote->setHost("postgres-srv-2.kisters.de");   // AC
$remote->setDB("test");
$remote->setPort("5432");
$remoteurl="http://postgres-srv-2.kisters.de/PostgresReplication/remote.php";
*/



// Oracle schaffte ca. 10.000 verzögerte Transaktionen (also mind. 10.000 Zeilen) pro Minute


// check Replication
$r = new CheckRep($local->conn(),$remote->conn());
$r->readStructure();
$r=null;

// Start Replication

$rep = new DoRep_http($local->conn(),$remote->conn(),false);  // true ==> Daten werden ohne Konflikterkennung repliziert
$rep->setRemoteUrl($remoteurl);   // Remote URL setzen
$rep->setMaxCount(10000);   // Maximale Anzahl der Zeilen pro html request. Umfass eine einzelne txid mehr Zeilen wird sie trotzdem ausgeführt 
$rep->bwoptimization(true);   // Beim Update werden nur die geänderten Zeilen übertragen


while (true) {
	//$rep->getnotice();
	//exit;
	//if ($rep->check()==true) {
		$rep->push();
		$rep->getnotice();
	//}
	sleep(1);
}
	




?>