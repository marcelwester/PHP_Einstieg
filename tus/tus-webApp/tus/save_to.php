<?php
 $downloaddir="../../download/";
 $filename=$_GET["filename"];
 $filetype=$_GET["filetype"];
 

        
        if (!isset($filetype)) header("Content-Type: application/download\n");
        if ($filetype=="audio") header("Content-Type: audio/basic");
        if ($filetype=="pdf")  header("Content-Type: application/pdf");
        if ($filetype=="wmv")  {
        	header("Content-Type: video/wmv");
        	$downloaddir.="video/";	
        }

 		$filesize = filesize($downloaddir.$filename);

 if ($filesize) {
        header("Content-Length: ".$filesize);
        header("Content-Disposition: attachment; filename=\"".$filename."\"");
        $fn=fopen($downloaddir.$filename , "rb");
        fpassthru($fn);
 }
 include "inc.php";
 sitename("save_to.php",$_SESSION["groupid"]);
  
 // download protokollieren
 $sqlstr="insert into sys_download (filename,ip,ts) values ('".$filename."','".$_SERVER["REMOTE_ADDR"]."',sysdate())";
 doSQL($db,$sqlstr);
 closeConnect($db);
?>
 