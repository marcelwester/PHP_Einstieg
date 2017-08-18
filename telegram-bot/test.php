<?php

include "inc.php";

$rs =  new DBMS_res($conn);


$sql="select message_id,toc_ts,message from message_send order by toc_ts";
$rs->query($sql) or die ;

while ($row=$rs->fetchRow()) {
	$data=unserialize(base64_decode($row["message"]));
	echo '<pre>';
	  print_r($data);
	echo '</pre>';
}




echo "<hr>";

$sql="select id,message from inbox order by id"; 
$rs->query($sql);

while ($row=$rs->fetchRow()) {
	echo strlen($row["message"])." - ".$row["message"];
	echo '<br>';
	echo strlen(base64_decode($row["message"]))." - ".base64_decode($row["message"]);
	echo '<br><br>';
}







$local->close();
?>