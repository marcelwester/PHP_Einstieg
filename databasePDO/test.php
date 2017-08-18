<?php
include "classes/_init.php";

$local=new dbMysqlConnect();
$local->setUser("root");
$local->setPass("");
$local->setHost("127.0.0.1");
$local->setDB("marcel");
$local->setPort("3306");

$rs= new DBMS_res($local->conn());


$sql="select id,vorname,name from test";

$rs->prepare($sql);
$rs->execute();
while ($row=$rs->fetchRow()) {
    
    echo $row["vorname"]." - ".$row["name"]."\n";
}



$rs->query("start transaction");
$sql="insert into test (name,vorname) values (?,?)";
$rs->prepare($sql);

$rs->bindColumn(1, "WWW");
$rs->bindColumn(2, "YYY");
$rs->execute();

$rs->bindColumn(1, "AAA");
$rs->bindColumn(2, "BBB");
$rs->execute();
$rs->query("commit");







/*
$sql="insert into sys_values (name_s,val_s,descr_s,indx) values (?,?,?,?)";
$rs->prepare($sql);
$rs->bindColumn(1, "Volker Losch6");
$rs->bindColumn(2, "xxxx");
$rs->bindColumn(3, "dddd");
$rs->bindColumn(4, "80");
$rs->execute();


$sql="insert into sys_log (message,user) values (?,?)";
$rs->prepare($sql);
$rs->bindColumn(1, "Volker Losch1");
$rs->bindColumn(2, "xxxx");
$rs->execute();

$rs->prepare($sql);
$rs->bindColumn(1, "Volker Losch2");
$rs->bindColumn(2, "xxxx");
$rs->execute();

$sql =" select message,user from sys_log where user = ?";
$rs->prepare($sql);
$rs->bindColumn(1, "xxxx");
$rs->execute();

while ($row=$rs->fetchRow()) {
	echo $row["message"]." - ".$row["user"]."\n";
}

echo "\n Old Style\n";

$sql="insert into sys_log (message,user) values ('MESSAGE','USER')";
$rs->query($sql);

$sql="select message,user from sys_log where user='USER'";
$rs->query($sql);
while ($row=$rs->fetchRow()) {
	echo $row["message"]." - ".$row["user"]."\n";
}


echo "\nGet Array\n";
$sql =" select message,user from sys_log where user = ?";
$rs->prepare($sql);
$rs->bindColumn(1, "xxxx");
$rs->execute();
print_r($rs->getArray());
*/




$local->close();
echo "Ende\n";
