<?php
 include "classes/_init.php";
 
 $local=new dbMysqlConnect();
 $local->setUser("root");
 $local->setPass("");
 $local->setHost("127.0.0.1");
 $local->setDB("tippspiel");
 $local->setPort("3306");
 
 
 $rs= new DBMS_res($local->conn());
 
 
 $sql="select id,message,user,ts,ip from sys_log limit 10";
 
 $rs->query($sql);
 
 while ($row=$rs->fetchRow()) {
 	echo $row["id"]." - ";
 	echo $row["message"]." - ";
 	echo $row["ts"]." - ";
 	echo $row["user"]." - ";
 	echo $row["ip"];
 	echo "\n";
 }
 
 
 $local->close();
 
 ?>