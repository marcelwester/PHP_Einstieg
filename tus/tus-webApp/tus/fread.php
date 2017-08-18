<?php

include "inc.php";

/*
$rowid=GetResult($db,"select image_id from sys_images");
foreach ($rowid as $row) {
	echo '<br>'.$row["image_id"]. " - ";
	$sql="select bin_data from sys_images where image_id=".$row["image_id"];
	$result=GetResult($db,$sql);
	if ($result["code"]!=0) {
		echo '<br>';
		print_r($result);
		echo '<br>';
	}
	$size=strlen($result["0"]["bin_data"]);
	$sql="update sys_images set size=$size where image_id=".$row["image_id"];
	$result=doSql($db,$sql);
	if ($result["code"]!=0) {
		echo '<br>';
		print_r($result);
		echo '<br>';
	}
	echo $size;
	
	
}


exit;
$sqlstr=array();
$map=array();

$j=0;
for ($i=25; $i<60; $i++) {
	$map[$i]=10002+$j;
	$sqlstr="update fb_tore set assist_id=$i where assist_id=".$map[$i];
	$result=doSQL($db,$sqlstr);
	echo "<br>".$sqlstr;
	echo "<br>".$result["code"];
	echo "<br>".$result["message2"];
	$j++;
}

print_r($map);


*/

function getfilelist($path)
{
 $f=array(); 
 $handle = opendir($path);
 while($filename = readdir($handle)) {
     if ($filename!='.' && $filename!='..') { 	
     	array_push($f,$filename); 
   	echo "<br>$filename";
     }
 }
 unset($handle);
   return $f;
} 


//$path='../../phptmp/Sponsoren/Kleine_Ansichten/';
$path='../../phptmp/test/';
$a=getfilelist($path);
$kat=6;






foreach ($a as $row)
{
  $data = addslashes(fread(fopen($path.$row, "r"), filesize($path.$row)));
  $sqlstr = "insert into sys_images (kategorie,bin_data,descr) values ($kat,'$data','Erntefest Tauziehen')";
  $result = doSQL($db,$sqlstr);
  if ($result["code"] == "0") 
    echo "<br>Ok".$path.$row;
  else
    echo "<br>Fehler: ".$path.$row;
}




/*
exit;	
 
 
 $sqlstr="source '../../phptmp/usr_www79_2.sql'";
 $result=GetResult($db,$sqlstr);
 echo "<br>Ergebnis: ".$result["code"];
 echo "<br>Fehler: ".$result["message2"];

   	
exit; 	
 

  $fp = fopen("../../phptmp/data_inserts.sql", "r");
  while ($line = fgets($fp, 100000)) {
  if ((! (substr($line,0,1) == '#')) && (strlen(trim($line)) >>3 ))
   {
   	#echo "<br>$line";
   	$line1=addslashes($line);
   	$result=doSQL($db,$line1);
   	echo "<br>".$result["code"];
  	echo "<br>".$line1;
  	exit;
   	
  }
  }
  fclose($fp);
*/
closeConnect($db);
?>
