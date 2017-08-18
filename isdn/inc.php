<?php
include "classes/_init.php";

$local=new dbMysqlConnect();
$local->setUser("root");
$local->setPass("");
$local->setHost("127.0.0.1");
$local->setDB("isdn");
$local->setPort("3306");

$rs= new DBMS_res($local->conn());



?>
