<?php

include "inc.php";

	
$rs =  new DBMS_res($conn);

$msg= new Message($conn);

while (true) {
$sql="select id from inbox_spool order by id";
$rs->query($sql) or die;
if ($rs->count()>0) {
	echo "\n";
	$sql="select s.id id,message,toc_in,chatid from inbox_spool s,inbox i where i.id=s.id order by i.id";
    $rs->query($sql) or die;
    foreach ($rs->getArray() as $row) {
	    $message=utf8_encode($row["toc_in"].": ".base64_decode($row["message"]));   
	    echo "send message... ".$row["id"]." - ";
	    echo "chatid: ".$row["chatid"]." \n";
	    if (intval($row["chatid"])!=0) {
		    if ($msg->send($row["chatid"],$message,$row["id"])) {
		    	$sql="delete from inbox_spool where id=".$row["id"];
		    	$rs->query($sql) or die;
		    	echo "\n".date("d.m.Y - H:i:s")."    ";
		    	echo "success !\n";
		    } else {
		    	echo "\n";
		    }
	    }
    }
}
sleep(2);
echo ".";
}



?>

