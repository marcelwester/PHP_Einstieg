<?php


include "inc.php";
if (priv("ADMIN"))
{

  echo "import sys_images";
  $datei = fopen("dmp/sportwoche.sql", "r");
echo $datei;
  $i=0;
  while (!feof($datei)) {
	$buffer = fgets($datei);
	$i = $i + 1;
	$result=doSQL($db,$buffer);
	echo '<br>'.$i.' - '.strlen($buffer).print_r($result);
  }
   fclose($datei);

}
else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
?>
