<?php
include "inc.php";

$xsize=220;
$ysize=100;
$step=10;
$im=ImageCreate($xsize,$ysize) or die ("Kann keinen neuen GD-Bild-Stream erzeugen");
$xsize--;
$ysize--;
$backgroundcolor = ImageColorAllocate($im,220,230,240);
$text_color = ImageColorAllocate($im,125,125,125);
$bordercolor= ImageColorAllocate($im,125,125,125);
$fillcolor= ImageColorAllocate($im,255,0,255);
$font="../fonts/elephant.ttf";  

$_SESSION["secimage"]=simpleRandString(5);

if (!isset($_GET["action"])) $_GET["action"]=""; 

if ($_GET["action"]=="") {
	echo '<center>';
	   echo '<img src="secimage.php?action=draw" alt="" border="0">';
	   echo '<br><br>';
	   echo '<input type="button" value="Neue Zeichenfolge laden" onclick="window.location.reload()">';
	echo '</center>';
	
} else {
  ImageLine($im,0,$ysize,$xsize,$ysize,$bordercolor);
  ImageLine($im,$xsize,$ysize,$xsize,0,$bordercolor);

   // verticale lines
   for ($i = 1; $i < $xsize;  $i= $i + $step) {
      	ImageLine($im,$i,1,$i,$ysize-1,$bordercolor);
   }

   for ($i = 1; $i < $ysize;  $i= $i + $step) {
      	ImageLine($im,1,$i,$xsize-1,$i,$bordercolor);
   }

   $str=$_SESSION["secimage"];	
   $strlen=strlen($str);
   $strarray=array();
   
	for($i=0; $i<$strlen; $i++) {
	   $strarray[$i] = $str{$i};
	} 
    $x=0;
   	foreach ($strarray as $a) {
   		$y=rand(30,90);
   		$size=rand(20,30);
   		$rot=rand(-30,30);
   		imagettftext ($im,$size,$rot,($x*35)+10,$y,$text_color,$font,$a);
   		$x++;
   	}
   
    ImagePNG($im);
    ImageDestroy($im);

}    
?>
