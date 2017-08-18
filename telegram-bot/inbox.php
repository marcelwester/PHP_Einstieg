<?php
   include "inc.php";

   
   $errorid=null;
   $message=null;
   $rs=null;
   
   
   $errorid=intval($_POST["errorid"]);
   //base64_decode
   $message=$_POST["message"];
   
   $chatid=$_POST["chatid"];

   if ($errorid==0) exit(1);
   
   $rs =  new DBMS_res($conn);
   $sql="insert into inbox (error_id,message,toc_in,chatid) values (?,?,sysdate(),?)";
   $rs->prepare($sql) or die;
   $rs->bindColumn(1, $errorid);
   $rs->bindColumn(2, $message);
   $rs->bindColumn(3, $chatid);
   
   if ($rs->execute()) {
   	  $lid=$rs->lId();
   	  $sql="insert into inbox_spool (id,toc) values ($lid,sysdate())";
   	  if ($rs->query($sql)) {
   	  	echo "Ok";
   	  } else {
   	  	echo "failed";
   	  }	  
   } else {
   	echo "failed";
   }

   
   // close db Connection
   $local->close();
   
   
   
?>