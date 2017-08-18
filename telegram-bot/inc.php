<?php
include "classes/_init.php";

$local=new dbMysqlConnect();
$local->setUser("root");
$local->setPass("");
$local->setHost("127.0.0.1");
$local->setDB("telegram_bot");
$local->setPort("3306");

$conn= $local->conn();
// usage $rs =  new DBMS_res($conn);

$_file="/var/www/telegram/status";




?>