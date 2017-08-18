<?php

include "classes/__init.php";
$local=new dbPostgresConnect();
$local->setUser("vol");
$local->setPass("12years");
$local->setHost("127.0.0.1");
$local->setDB("test");
$local->setPort("5434");
$rep = new DoRep_local($local->conn(),false);   // true => force=true (Keine Konflikterkennung)
//echo strlen($_POST["data"])." Bytes\n";


$action=($_GET["action"]);

switch ($action) {
	case "dorep":
		$rep->TimerStart();
		$data=unserialize(base64_decode($_POST["data"]));
		
		//print_r($data);
		$rep->write($data);
		echo "remote DBMS operation time: ".$rep->TimerEnd();
		
		$local->close();
	break;
	
	case "getnotice":
		$rep->getnotice();
		$local->close();
	break;
		
}


?>