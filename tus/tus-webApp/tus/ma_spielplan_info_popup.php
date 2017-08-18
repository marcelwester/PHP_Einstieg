<?php
include "inc.php";
sitename("ma_spielplan_info_popup.php",$_SESSION["groupid"]);
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e</TITLE>

<SCRIPT LANGUAGE="JavaScript">
<!--
        function spielerInfo(spielerid,saisonid)
        {
                var url;
        <?php
                echo 'url = "ma_kader_info_popup.php?action=info&personid="+spielerid+"&saisonid="+saisonid+"&PHPSESSID='.session_id().'";';
        ?>
                window.open(url,"info","width=450, height=500, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
        }
-->
</SCRIPT>

</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php
focus();
	  	  $spielid = $_REQUEST["spielid"];
	      $teamid = $_REQUEST["teamid"];
	      
          $sqlstr = "select sp.saison_id,sp.bemerkung,sp.datum,sp.gespielt,sp.heim_tore,sp.aus_tore,sp.heim_id,sp.aus_id,ma.name,ma.id,gespielt
                     from fb_mannschaft ma, fb_spiele sp where
                    (sp.heim_id=ma.id or sp.aus_id=ma.id) and sp.id=$spielid";
          //echo '<br>'.$sqlstr;

          $result = GetResult($db, $sqlstr);
          $mannschaft=$result["0"]["name"];
          $mannschaftid=$result["0"]["id"];
          $gespielt=$result["0"]["gespielt"];
          $heimid=$result["0"]["heim_id"];
          $ausid=$result["0"]["aus_id"];
          $heimtore=$result["0"]["heim_tore"];
          $austore=$result["0"]["aus_tore"];
          $gespielt=$result["0"]["gespielt"];
          $datum=$result["0"]["datum"];
          $bemerkung=$result["0"]["bemerkung"];
	      $saisonid=$result["0"]["saison_id"];



          $sqlstr = "select name from fb_mannschaft where id = $heimid";
          $result = GetResult($db, $sqlstr);
          $heimname = $result["0"]["name"];

          $sqlstr = "select name from fb_mannschaft where id = $ausid";
          $result = GetResult($db, $sqlstr);
          $ausname = $result["0"]["name"];

			 //Spielstaette lesen, wenn es ein Heimspiel ist
			 $sqlstr  = "select s.name from fb_tus_mannschaft m,spielstaette s where ";
			 $sqlstr .= "m.id=".$heimid." and ";
			 $sqlstr .= "s.id=m.spielstaette_id";
			 $result=getResult($db,$sqlstr);
			 if (isset($result)) {
			 	 $spielstaette=" - ".$result["0"]["name"];
			 	 // Pruefen, ob die Spielstaette nicht im Spiel ueberschrieben wurde
			 	 $sqlstr  = "select s.name from fb_spiele m,spielstaette s where ";
				 $sqlstr .= "m.id=".$spielid." and ";
				 $sqlstr .= "s.id=m.spielstaette_id";
				 $result=getResult($db,$sqlstr);
				 if (isset($result)) {
				 	 $spielstaette=" - ".$result["0"]["name"];
				 }
			 }

       // Saison lesen
          $sqlstr ="select sp.bemerkung,saison_id,liga,spielzeit from fb_saison sa,fb_spiele sp
                    where sa.id=sp.saison_id and sp.id=$spielid";
          $result = GetResult($db, $sqlstr);
          $liga=$result[0]["liga"];
          $spielzeit=$result[0]["spielzeit"];
          $saisonid=$result[0]["saison_id"];
          $spielbemerkung=$result["0"]["bemerkung"];

          echo '<TABLE WIDTH="100%" BORDER="0" >';
          echo '<TD COLSPAN="3" ALIGN="CENTER" BGCOLOR="#DDDDDD">';


          echo '<B>Spielinfo</B><BR>'.$liga.' - '.$spielzeit.'<BR>';
          if ($gespielt == "1") {
			    echo date("d.m.Y - H:i", strtotime($datum)).'<br>';	
            echo $heimname.' - '.$ausname.'<BR>'.$heimtore.' - '.$austore.'</td>';
          } else {
            echo '<BR>'.$heimname.' - '.$ausname.'<BR>'.date("d.m.Y - H:i", strtotime($datum));
            echo $spielstaette.'</td>';
          }
          echo '</TABLE>';
          echo '<br>';


	  // Wenn das Spiel noch nicht ausgetragen wurde, soll nach eventuell vorhandenen alten Begegnungen gesucht werden 
	  if ($gespielt == "0") {
	  	$sqlstr = 'select distinct(id) from fb_spiele where';
	  	$sqlstr .= ' (heim_id='.$heimid.' or aus_id='.$heimid.') and ';
	  	$sqlstr .= ' (heim_id='.$ausid.' or aus_id='.$ausid.') and gespielt=1 order by datum desc';
	  	$result=GetResult($db,$sqlstr);
	  	if (isset($result)) {
			echo '<table align="CENTER" width="100%">';
			echo '<tr>';
				echo '<TD COLSPAN="7" ALIGN="CENTER" BGCOLOR="#DDDDDD"><b>Vergangene Aufeinandertreffen</b></TD>';
			
			echo '</tr>';
			echo '<tr>';
				echo '<td align="CENTER"><b>Spielzeit</b></td>';
				echo '<td align="CENTER"><b>Liga</b></td>';
				echo '<td align="CENTER"><b>Datum</b></td>';
				echo '<td align="CENTER"><b>Heim</b></td>';
				echo '<td align="CENTER"><b>Gast</b></td>';
				echo '<td align="CENTER"><b>Ergebnis</b></td>';
				echo '<td align="CENTER"><b>INFO</b></td>';
			echo '</tr>';

			

	  		foreach ($result as $match) {
	  			echo '<tr>';
	  			$sqlstr  = 'select sp.id,datum,saison_id,spielzeit,liga,heim_id, aus_id, heim_tore, aus_tore, datum, bemerkung from ';	
	  			$sqlstr .= 'fb_spiele sp,fb_saison sa where ';
	  			$sqlstr .= 'sp.saison_id=sa.id and ';
	  			$sqlstr .= 'sp.id='.$match["id"];
	  			$result1 = GetResult($db,$sqlstr);
				
				$heimid=$result1["0"]["heim_id"];
				$ausid=$result1["0"]["aus_id"];
				
				$sqlstr = 'select spielzeit,liga from fb_saison where id='.$result1["0"]["saison_id"];
				$result2=GetResult($db,$sqlstr);
				echo '<td align="CENTER">';
					echo $result2["0"]["spielzeit"];
				echo '</td>';
				echo '<td align="CENTER">';
					echo $result2["0"]["liga"];
				echo '</td>';

				echo '<td align="CENTER">';
					//echo date("d.m.Y - H:i",strtotime($result1["0"]["datum"]));
					echo date("d.m.Y",strtotime($result1["0"]["datum"]));
					//echo $result1["0"]["datum"];
				echo '</td>';
		
				
				$sqlstr = 'select name from fb_mannschaft where id = '.$result1["0"]["heim_id"];
				$result2 = GetResult($db,$sqlstr);
				echo '<td align="CENTER">';
					echo $result2["0"]["name"];
				echo '</td>';
				
				$sqlstr = 'select name from fb_mannschaft where id = '.$result1["0"]["aus_id"];
				$result2 = GetResult($db,$sqlstr);
				echo '<td align="CENTER">';
					echo $result2["0"]["name"];
				echo '</td>';
				
				// Ergebnis validieren, wenn teamid übergeben wurde:
				$color=$white;
				// Pruefe, ob das Spiel mit Ocholter Beteiligung stattfindet
				if (!isset($teamid)) {
					$sqlstr ="select id from fb_tus_mannschaft where id in (".$heimid.",".$ausid.")";
					$tusmannschaft=getResult($db,$sqlstr);
					if (isset($tusmannschaft)) {
						$teamid=$tusmannschaft["0"]["id"];
					}
				}
				
				
				if (isset($teamid)) {
					// Heimspiel
					if ($heimid	== $teamid) {
						if ($result1["0"]["heim_tore"] > $result1["0"]["aus_tore"]) {$color=$GRUEN;	}
						if ($result1["0"]["heim_tore"] < $result1["0"]["aus_tore"]) {$color=$ROT;	}
					} 
					if ($ausid	== $teamid) {
						if ($result1["0"]["heim_tore"] < $result1["0"]["aus_tore"]) {$color=$GRUEN;	}
						if ($result1["0"]["heim_tore"] > $result1["0"]["aus_tore"]) {$color=$ROT;	}
					}
					if ($result1["0"]["heim_tore"] == $result1["0"]["aus_tore"]) {$color=$GELB;	}

				}

				

				echo '<td align="CENTER" bgcolor='.$color.'>';
					echo $result1["0"]["heim_tore"];
					echo ' : ';
					echo $result1["0"]["aus_tore"];
				echo '</td>';
				echo '<td align="CENTER">';
                    echo '<a href="ma_spielplan_info_popup.php?descent=0&spielid='.$result1["0"]["id"].'">';
                        echo '<IMG SRC="images/info.gif" BORDER="0" ALT="Info">';
                    echo '</a>';
				echo '</td>';
			

				
				echo '</tr>';
	  		}
			echo '</table>';	  		
	  			
	  	} else {
	  		echo "<br>Keine vorherigen Begegnungen der Mannschaften gefunden";
	  	}
	  	
	  
	  } else {




    	$sql_fotos = 'select image_id, descr from sys_images where kategorie = 3 and linked = '.$spielid.' order by idx,image_id';
   		$result_fotos = getResult($db,$sql_fotos);


		if (isset($result_fotos[0]))
		{
			$buttons_in_a_row = 7;

			echo '<TABLE WIDTH="100%" BORDER="0"><TR>';
			if (isset($_REQUEST["descent"])) 
				
				$urldesc='&descent='.($_REQUEST["descent"] +1 );
			else
				$urldesc='';

			$url = 'ma_spielplan_info_popup.php?spielid='.$spielid.$urldesc.'&PHPSESSID='.session_id();
			if (!isset($_REQUEST["img_id"]))
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF"><B>';
			else
				echo '<TD ALIGN="CENTER" BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\''.$url.'\'">';

			echo 'Infos</B>';
			echo '</TD>';

			if (count($result_fotos) == $buttons_in_a_row) {
				$offset=-1;
			} else {
				$offset=0;
			}
			
			$numrows = ceil((count($result_fotos) + 1) / ($buttons_in_a_row + $offset));
			
			for ($row=0; $row < $numrows; $row++)
			{
				if ($row > 0)
				{
					echo '<TR>';
					$limit = $buttons_in_a_row;
				}
				else
					$limit = $buttons_in_a_row - 1 ;

				for ($x=0; $x < $limit; $x++)
				{
					if ($row > 0)
						$index = $x+$row*$buttons_in_a_row -1;
					else
						$index = $x+$row*$buttons_in_a_row;
					if (isset($result_fotos[$index]))
					{
						if ($result_fotos[$index]["image_id"] == $_REQUEST["img_id"])
							echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF"><B>';
						else
						{
								if (isset($_REQUEST["descent"])) 
									$urldesc='&descent='.($_REQUEST["descent"] +1 );
								else
									$urldesc='';
								
							$url = 'ma_spielplan_info_popup.php?spielid='.$spielid.'&img_id='.$result_fotos[$index]["image_id"].$urldesc.'&PHPSESSID='.session_id();
							echo '<TD ALIGN="CENTER" BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\''.$url.'\'">';
						}
		
						echo ($index+1).'. Foto</B>';
						echo '</TD>';
					}
					else
						echo '<TD></TD>';
				}
				echo '</TR>';
			}
			echo '</TABLE><BR>';
		}

		if (isset($_REQUEST["img_id"]))
		{
			echo '<TABLE WIDTH="100%" BORDER="0" >';
			echo '<TR><TD ALIGN="CENTER">';
			echo '<IMG SRC="showimage2.php?id='.$_REQUEST["img_id"].'" />';
			echo '</TD></TR>';
			echo '</TABLE>';
		}
		else
		{
		  // Bemerkung anzeigen, wenn eine vorhanden ist
	          if (! (($spielbemerkung=="") or (! isset($spielbemerkung))))
	             echo "<br><b>Bemerkung</b>: ".$spielbemerkung."<br><br>";

	           // Daten aus fb_tore laden
	           $sqlstr1 = "select spieler_id,assist_id,minute,mannschaft_id,bemerkung from fb_tore  where spiel_id=$spielid order by minute,id";
	           $result1 = GetResult($db,$sqlstr1);

	           $heimtore="0";
	           $austore="0";
	           if (count($result1) > 0)
		   {
		     echo '<TABLE WIDTH="100%" CLASS="none">';
	             echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Schütze</B></TD>';
	             echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Vorlage</B></TD>';
	             echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Min.</B></TD>';
	             echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Mannschaft</B></TD>';
	             echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Tore</B></TD>';
	             echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Bemerkung</B></TD></TR>';

	             foreach ($result1 as $tor)
	              {
	           	// Schütze
	           	echo '<tr><td ALIGN="CENTER">';
	           	  $sqlstr = 'select name,vorname from fb_person where id='.$tor["spieler_id"];
	           	  $result = GetResult($db,$sqlstr);
	                  if (! isset($result))
	                     echo "-";
	                  else
	                  {
	                     echo '<a href="javascript:spielerInfo('.$tor["spieler_id"].','.$saisonid.');">';
	                     echo $result[0]["vorname"].' '.$result[0]["name"];
	                     echo '</a>';
	                  }
	           	echo '</td>';

	           	// Assist
	           	echo '<td ALIGN="CENTER">';
	           	  $sqlstr = 'select name,vorname from fb_person where id='.$tor["assist_id"];
	           	  $result = GetResult($db,$sqlstr);
	                  if (! isset($result))
	                     echo "-";
	                  else
	                  {
	                     echo '<a href="javascript:spielerInfo('.$tor["assist_id"].','.$saisonid.');">';
	                     echo $result[0]["vorname"].' '.$result[0]["name"];
	                     echo '</a>';
	                  }
	           	echo '</td>';

	           	// Minute
	           	echo '<td ALIGN="CENTER">';
	                  if ($tor["minute"]!=0) 
	                  	echo $tor["minute"].'.';
	                  else
	                  	echo '-';
	                  	
	           	echo '</td>';

	           	// Mannschaft
	           	echo '<td ALIGN="CENTER">';
	           	  $sqlstr = 'select name from fb_mannschaft where id='.$tor["mannschaft_id"];
	           	  $result = GetResult($db,$sqlstr);
	                  echo $result["0"]["name"];
	           	echo '</td>';

	                // Spielstand 
	                if ($tor["mannschaft_id"]==$heimid)
	           	    $heimtore++;
	           	if ($tor["mannschaft_id"]==$ausid)
	           	    $austore++;
	           	echo '<td ALIGN="CENTER">';
	           	  echo $heimtore.':'.$austore;
	           	echo '</td>';

	          	// Bemerkung
	           	echo '<td ALIGN="CENTER">';
	           	  if ($tor["bemerkung"] == "")
	           	     $tor["bemerkung"]=" - ";
	                  echo $tor["bemerkung"];
	           	echo '</td>';

	           	echo '</tr>';

	 	     }
	    	     echo '</table>';
	           }
	           echo '<br>';

	           // Spielbericht lesen
	           $sqlstr = "select id,spiel_bericht,verfasser_id from fb_sp_bericht where spiel_id=$spielid";
	           $result = GetResult($db,$sqlstr);
	        
	           	
	           if (isset($result["0"][spiel_bericht]))
	           {
					$sqlstr="select name from sys_users where userid=".$result["0"]["verfasser_id"];
					$result1=GetResult($db,$sqlstr);
					echo '<TABLE WIDTH="100%" BORDER="0" >';
					echo '<TR><TD BGCOLOR="#DDDDDD" ALIGN="CENTER"><B>Spielbericht</B><br>- '.$result1["0"]["name"].' -</TD></TR>';
					echo '<tr><td>';
					echo $result["0"]["spiel_bericht"];
					echo '</tr></td>';
					echo '</table>';
	           }
	        }

        echo '<br>';
}
    echo '<TABLE WIDTH="100%" BORDER="0" ALIGN="CENTER">';
  	echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD" >';
  	if (isset($_REQUEST["descent"])) {
  		$history = -($_REQUEST["descent"] + 1 );
  		echo '<INPUT TYPE="button" VALUE="Zurück" onClick="window.history.go('.$history.');">';
  		echo '&nbsp;&nbsp;&nbsp;';
  	}
  	echo '<INPUT TYPE="button" VALUE="Schliessen" onClick="window.close();">';
  	echo '</TD></TR>';
  	echo '</table>';

closeConnect($db);
?>
</BODY>
</HTML>