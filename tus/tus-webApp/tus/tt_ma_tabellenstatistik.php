<?php

function tab_pos($db,$mannschaftid,$saisonid,$spieltag) {
    $tabelle = array();

    $sql1 = 'select mannschaft_id from tt_zwtab_mannschaft_saison where saison_id = '.$saisonid;
    $result1 = GetResult($db, $sql1);

    $sql2 = 'select * from tt_mannschaft';
    $result2 = GetResult($db, $sql2);

    $x = 0;
    foreach ($result1 as $row1)        // tt_zwtab_mannschaft_saison
    {
            foreach($result2 as $row2)        // tt_mannschaft
            {
                    if ($row2["id"] == $row1["mannschaft_id"])
                    {
                            $tabelle[$x] = array("id" => $row2["id"]);
                            $tabelle[$x]["name"] = $row2["name"];
                            $tabelle[$x]["punkte"] = 0;
                            $tabelle[$x]["spiele"] = 0;
                            $tabelle[$x]["tore"] = 0;
                            $tabelle[$x]["gegentore"] = 0;
                            $tabelle[$x]["siege"] = 0;
                            $tabelle[$x]["niederlagen"] = 0;
                    }
            }
            $x++;
    }
    $anzteams = $x;

////////////////////////
//// Tabelle füllen
////////////////////////

    $sql3 = 'select * from tt_spiele where saison_id = '.$saisonid.' and gespielt = 1 and spieltag <= '.$spieltag;
    $result3 = GetResult($db, $sql3);
    if (isset($result3[0]))
    foreach ($result3 as $row3) // tt_spiele
    {
            $y = 0;
            while ($y < $anzteams)
            {
                    if ($tabelle[$y]["id"] == $row3["heim_id"])                ////////// Heimmannschaft
                    {
                            $tabelle[$y]["spiele"]++;
                            $tabelle[$y]["tore"] += $row3["heim_tore"];
                            $tabelle[$y]["gegentore"] += $row3["aus_tore"];
                            if ($row3["heim_tore"] > $row3["aus_tore"]) // Sieg
                            {
                                    $tabelle[$y]["punkte"] += 3;
                                    $tabelle[$y]["siege"]++;
                            }
                            if ($row3["heim_tore"] < $row3["aus_tore"]) // Niederlage
                                    $tabelle[$y]["niederlagen"]++;
                            if ($row3["heim_tore"] == $row3["aus_tore"]) // Unentschieden
                                    $tabelle[$y]["punkte"]++;
                    }
                    if ($tabelle[$y]["id"] == $row3["aus_id"])                ////////// Auswärtsmannschaft
                    {
                            $tabelle[$y]["spiele"]++;
                            $tabelle[$y]["tore"] += $row3["aus_tore"];
                            $tabelle[$y]["gegentore"] += $row3["heim_tore"];
                            if ($row3["heim_tore"] < $row3["aus_tore"]) // Sieg
                            {
                                    $tabelle[$y]["punkte"] += 3;
                                    $tabelle[$y]["siege"]++;
                            }
                            if ($row3["heim_tore"] > $row3["aus_tore"]) // Niederlage
                                    $tabelle[$y]["niederlagen"]++;
                            if ($row3["heim_tore"] == $row3["aus_tore"]) // Unentschieden
                                    $tabelle[$y]["punkte"]++;
                    }
                    $y++;
            }
    }

////////////////////////
//// Tabelle sortieren
////////////////////////

    do
    {
            $getauscht = 'nein';
            $y = 0;
            while ($y < $anzteams-1)
            {
                    if ($tabelle[$y]["punkte"] < $tabelle[$y+1]["punkte"])
                    {
                            $dummy = $tabelle[$y];
                            $tabelle[$y] = $tabelle[$y+1];
                            $tabelle[$y+1] = $dummy;
                            $getauscht = 'ja';
                    }
                    if ($tabelle[$y]["punkte"] == $tabelle[$y+1]["punkte"])
                    {
                            if ($tabelle[$y]["tore"]-$tabelle[$y]["gegentore"] < $tabelle[$y+1]["tore"]-$tabelle[$y+1]["gegentore"])
                            {
                                    $dummy = $tabelle[$y];
                                    $tabelle[$y] = $tabelle[$y+1];
                                    $tabelle[$y+1] = $dummy;
                                    $getauscht = 'ja';
                            }
                            if ($tabelle[$y]["tore"]-$tabelle[$y]["gegentore"] == $tabelle[$y+1]["tore"]-$tabelle[$y+1]["gegentore"])
                            {
                                    if ($tabelle[$y]["tore"] < $tabelle[$y+1]["tore"])
                                    {
                                            $dummy = $tabelle[$y];
                                            $tabelle[$y] = $tabelle[$y+1];
                                            $tabelle[$y+1] = $dummy;
                                            $getauscht = 'ja';
                                    }
                            }
                    }
                    $y++;
            }
    }while($getauscht == 'ja');

// Mannschaftsid in der Tabelle suchen 
           $x=1;
           foreach($tabelle as $team) {
              	if ($team["id"] == $mannschaftid) {
           		return $x;
           	}
           	$x++;
           }
}


#
#
#
#  Beginn des Hauptteils
#
#
#
#
    $saisonid=$_GET["saisonid"];
    $mannschaftid=$_GET["teamid"];
    include "inc.php";
    

    
    header("Content-type: image/jpg");
    $xsize=620;
    $ysize=430;
    $dst=60;  // Abstand des Diagramms vom Rand
    $font="../fonts/arial.ttf";
    
    $im=ImageCreate($xsize,$ysize) or die ("Kann keinen neuen GD-Bild-Stream erzeugen");
    $xsize--;
    $ysize--;
    $backgroundcolor = ImageColorAllocate($im,220,230,240);
    $text_color = ImageColorAllocate($im,0,0,0);
    $bordercolor= ImageColorAllocate($im,0,0,0);
    $fillcolor= ImageColorAllocate($im,255,0,255);
    
// Rahmen
    ImageLine($im,0,0,0,$ysize,$bordercolor);
    ImageLine($im,0,$ysize,$xsize,$ysize,$bordercolor);
    ImageLine($im,$xsize,$ysize,$xsize,0,$bordercolor);
    ImageLine($im,$xsize,0,0,0,$bordercolor);

// Achsen
    Imageline($im,$dst,($ysize - $dst),$dst,$dst,$bordercolor);   
    Imageline($im,$dst,($ysize - $dst),($xsize -$dst),($ysize - $dst),$bordercolor);   

// Beschriftung
   imagettftext ($im,12,0,(($xsize / 2)-20),($ysize - $dst + 40 ),$text_color,$font,"Spieltag");
   imagettftext ($im,12,90,($dst - 25),(($ysize / 2)+20),$text_color,$font,"Tabellenplatz");
   
   $sqlstr="select count(*) anz from tt_zwtab_mannschaft_saison where saison_id=$saisonid";
   $result=GetResult($db,$sqlstr);
   $anz=$result["0"]["anz"];
   $diffx = ( ($xsize - $dst -$dst) / ($anz -1));
   $diffy = ( ($ysize - $dst -$dst) / ($anz -1));
   $posx = ($xsize - $dst);
   $posy = ($ysize - $dst);


   $sqlstr="select name from tt_mannschaft where id=$mannschaftid";
   $mannschaft=GetResult($db,$sqlstr);
   $mannschaft=$mannschaft["0"]["name"];
   imagettftext ($im,16,0,30,30,$text_color,$font,$mannschaft);
   $sqlstr="select count(*) anz from tt_zwtab_mannschaft_saison where saison_id=$saisonid";
   $result=GetResult($db,$sqlstr);
   $anzy=$result["0"]["anz"];
   $diffy = ( ($ysize - $dst -$dst) / ($anzy -1));
   $posy = ($ysize - $dst);
   
   $sqlstr="select max(spieltag) spieltag from tt_spiele where saison_id=$saisonid and gespielt=1";
   $result=GetResult($db,$sqlstr);
   $anzx=$result["0"]["spieltag"];
   $diffx = ( ($xsize - $dst -$dst) / ($anzx ));
   $posx = ($xsize - $dst - $diffx);


   
   // x- Achse Spieltage
   for ($i = $anzx; $i > 0; $i--) {
      	ImageLine($im,$posx,($ysize - $dst),$posx,($ysize - $dst + 5),$bordercolor);
   	imagettftext ($im,8,0,($posx + 2 ),($ysize - $dst + 15),$text_color,$font,"$i");
   	$posx=($posx - $diffx);

   }
   
   // y- Achse Tabellenplatz
   for ($i = $anzy; $i > 0; $i--) {
      	ImageLine($im,$dst,$posy,($dst -5),$posy,$bordercolor);
   	imagettftext ($im,8,0,$dst-15,$posy,$text_color,$font,"$i");
   	$posy=($posy - $diffy);
   }
  
  
   // zurücksetzen der Positionen
   $posy = ($ysize - $dst);
   $posx = ($xsize - $dst - $diffx);
   

   
    for ($i = $anzx; $i > 0; $i--) {
   	// Tabellenplatz
   	#ImageFilledRectangle($im,50,50,100,100,$bordercolor);
   	$x1=($posx + 3 );
   	//tab_pos($db,$mannschaftid,$saisonid,1)
   
   	$y1=(($ysize - $dst) - ($diffy * ($anz - tab_pos($db,$mannschaftid,$saisonid,$i) )) - 2 );
   	//$y1=(($ysize - $dst) - ($diffy * ($anz - $platz[$i] )) - 2 );
   	$x2=($posx + $diffx - 3);
   	$y2=($ysize - $dst);
   	/*
   	echo "<br>: $x1";
   	echo "<br>: $y1";
   	echo "<br>: $x2";
   	echo "<br>: $y2";
   	*/
   	ImageFilledRectangle($im,$x1,$y1,$x2,$y2,$fillcolor);
   	$posx=($posx - $diffx);
   	$posy=($posy - $diffy);
    }
    //ImageString($im,1,5,5,"Ein TestString",$text_color);
    ImagePNG($im);
    ImageDestroy($im);
    dbClose($db);	
?>