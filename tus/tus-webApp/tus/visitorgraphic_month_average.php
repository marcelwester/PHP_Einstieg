<?php

include "inc.php";
if (! priv("visitors"))
{
	echo $no_rights;
	closeConnect($db);
	exit;
}

function ts2datetime ($ts) {
	$adatetime=(sscanf($ts,"%4d%2d%2d%2d%2d%2d"));
	$data  = $adatetime["0"];
	$data .= '-'.$adatetime["1"];
	$data .= '-'.$adatetime["2"];
	$data .= ' '.$adatetime["3"];
	$data .= ':'.$adatetime["4"];
	$data .= ':'.$adatetime["5"];
	return $data;
}



  header("Content-type: image/jpg");
  header('Content-Disposition: inline; filename=statistic.png');


    //header("filename=\"test.png\"");
    $xsize=600;
    $ysize=380;
    $dst=50;  // Abstand des Diagramms vom Rand
    $font="../fonts/arial.ttf";
    $fontsize=9;
    $anzx=10;
    $anzy=10;

    $anzx++;
    $anzy++;

    $im=ImageCreate($xsize,$ysize) or die ("Kann keinen neuen GD-Bild-Stream erzeugen");
    $xsize--;
    $ysize--;
#    $backgroundcolor = ImageColorAllocate($im,220,230,240);
    $backgroundcolor = ImageColorAllocate($im,220,230,240);
    $text_color = ImageColorAllocate($im,0,0,0);
    $bordercolor = ImageColorAllocate($im,0,0,0);
    $fillcolor = ImageColorAllocate($im,255,0,255);
    $graph1 = ImageColorAllocate($im,255,0,0);
	 $pixelcolor = ImageColorAllocate($im,0,0,255);

    // Rahmen
    ImageLine($im,0,0,0,$ysize,$bordercolor);
    ImageLine($im,0,$ysize,$xsize,$ysize,$bordercolor);
    ImageLine($im,$xsize,$ysize,$xsize,0,$bordercolor);
    ImageLine($im,$xsize,0,0,0,$bordercolor);

// Achsen
    Imageline($im,$dst,($ysize - $dst),$dst,$dst,$bordercolor);
    Imageline($im,$dst,($ysize - $dst),($xsize -$dst),($ysize - $dst),$bordercolor);


// Achsenskalierung
   $diffx = ( ($xsize - $dst -$dst) / ($anzx -1));
   $diffy = ( ($ysize - $dst -$dst) / ($anzy -1));
   $posx = ( $dst);
   $posy = ( $dst);

$action=$_GET["action"];

//$duration = " and duration < 20 ";
$duration = " ";

switch ($action) {
   case "tus":
		$sqlstr="select date_format(toc,'%Y-%m-%d') x,hits y from sys_counter1 order by x";
		$sqlstr="select date_format(toc,'%Y-%m-%d') x,avg(hits) y from sys_counter1 group by date_format(toc,'%Y-%m') order by x";
		
		
		$sqlstr1="select min(date_format(toc,'%Y-%m-%d')) min_date,max(date_format(toc,'%Y-%m-%d')) max_date from sys_counter1";
  	    $minmax=GetResult($db,$sqlstr1);

		$sqlstr1="select max(hits) hits from sys_counter1";
		$maxdatay=GetResult($db,$sqlstr1);
      if(isset($maxdatay)) {
			$maxdatay=$maxdatay["0"]["hits"];
      } else {
			$maxdatay=200;
      }
	   // Minimalen und Maximalen Zeitstempel ermitteln
	   $min_date=strtotime($minmax["0"]["min_date"]);
	   $max_date=strtotime($minmax["0"]["max_date"]);
   break;


}


$bereinigen=0;
$data=GetResult($db,$sqlstr);


$maxdatax=($max_date - $min_date);
$mindatay=0;

// Überschrift
imagettftext ($im,$fontsize +4 ,0,150,30,$text_color,$font,"Besucher-Statistik (Monatsdurchschnitt)");

// Beschriftungen der Achsen
   // x - Achse
   $posx = ( $dst);
   $posy = ( $dst);

   // x- Achse
   for ($i = 0; $i < $anzx; $i++) {
            imageLine($im,$posx,($ysize - $dst),$posx,($ysize - $dst + 5),$bordercolor);

				// Relative Position
				$rposx=$posx-$dst;

            // Berechnung des Zeitstempels für die Position:
				$current= ($min_date + ($maxdatax *	($rposx / ($xsize -$dst - $dst))));
	           	imagettftext ($im,$fontsize,45,($posx - 20 ),($ysize - $dst + 45),$text_color,$font,date('d.m.y',$current));

           	$posx=($posx + $diffx);

   }

   // y- Achse
   for ($i = 0; $i < $anzy; $i++) {
    		  // Relative Position
			  $rposy=$posy-$dst;
            // Berechnung des Wertes für die Position:
				$current= round($maxdatay-($mindatay + ($maxdatay *	($rposy / ($ysize -$dst - $dst)))),2);
				$current=round($current);
           imageLine($im,$dst,$posy,($dst-5),$posy,$bordercolor);
           if ($current<10) imagettftext ($im,$fontsize,0,$dst-20,$posy,$text_color,$font,$current);
           if ($current>9 && $current<100) imagettftext ($im,$fontsize,0,$dst-25,$posy,$text_color,$font,$current);
           if ($current>99) imagettftext ($im,$fontsize,0,$dst-28,$posy,$text_color,$font,$current);
           
           $posy=($posy + $diffy);
   }

   // Nullpunkte des Grafen setzen
   $x0=$dst;
   $y0=$ysize-$dst;
   $x=$x0;
   $y=$y0;


   $yunit=($ysize - $dst -$dst) / $maxdatay;

	// Zeichnen des Graphen
	$draw=0;
	if (isset($data)) {
	   foreach ($data as $dat) {
	      $datediff=(strtotime($dat["x"]) - $min_date);

	      $xnext=$x0 +  ($datediff/$maxdatax)*($xsize - $dst - $dst) ;
	      $ynext=($y0 - ($dat["y"]* $yunit) );
	      if ($draw=="1") {
	         //echo '<br>'.$x.' - '.$y.' - '.$xnext.' - '.$ynext;
	         ImageLine($im,$x,$y,$xnext,$ynext,$graph1);
	         Imagesetpixel ( $im, $x, $y, $pixelcolor );
	         Imagesetpixel ( $im, $x, $y-1, $pixelcolor );
	         Imagesetpixel ( $im, $x, $y+1, $pixelcolor );
	         Imagesetpixel ( $im, $x-1, $y, $pixelcolor );
	         Imagesetpixel ( $im, $x+1, $y, $pixelcolor );
	         Imagesetpixel ( $im, $x-1, $y-1, $pixelcolor );
	         Imagesetpixel ( $im, $x+1, $y-1, $pixelcolor );
	         Imagesetpixel ( $im, $x-1, $y+1, $pixelcolor );
	         Imagesetpixel ( $im, $x+1, $y+1, $pixelcolor );
	      }
	      $draw=1;
	      $x=$xnext; $y=$ynext;
	   }
	   Imagesetpixel ( $im, $x, $y, $pixelcolor );
	   Imagesetpixel ( $im, $x, $y-1, $pixelcolor );
	   Imagesetpixel ( $im, $x, $y+1, $pixelcolor );
	   Imagesetpixel ( $im, $x-1, $y, $pixelcolor );
	   Imagesetpixel ( $im, $x+1, $y, $pixelcolor );
	   Imagesetpixel ( $im, $x-1, $y-1, $pixelcolor );
	   Imagesetpixel ( $im, $x+1, $y-1, $pixelcolor );
	   Imagesetpixel ( $im, $x-1, $y+1, $pixelcolor );
	   Imagesetpixel ( $im, $x+1, $y+1, $pixelcolor );
	}
   ImagePNG($im);
   ImageDestroy($im);

   closeConnect($db);

?>