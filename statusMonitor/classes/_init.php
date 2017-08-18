<?php
/*
   // start MYSQL
   $mysql=new DbMYSQLConnect();
   $mysql->setUser("root");
   $mysql->setPass("");
   $mysql->setHost("localhost");
   $mysql->setDB("statusMonitor");
   $dbmysqlconn=$mysql->conn();
   // Usage: $rs = new dbMYSQL($dbmysqlconn)
  */ 
   
   $local=new dbMysqlConnect();
   $local->setUser("root");
   $local->setPass("");
   $local->setHost("127.0.0.1");
   $local->setDB("statusMonitor");
   $local->setPort("3306");
   
   $conn = null;
   $conn = $local->conn();
   
    // USAGE 
    // $rs =  new DBMS_res($conn); 
  
?>