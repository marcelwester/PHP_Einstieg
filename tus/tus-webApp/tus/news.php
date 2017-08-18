<?php
sitename("news.php",$_SESSION["groupid"]);
// news.php
//
// letzte Änderung : Daniel, 01.03.2004
// - Datei erstellt
//
?>
  	<SCRIPT LANGUAGE="JavaScript">
	<!--
	        
	        function fb_spiel_info(spielid)
	        {
	        	var url;
	        	url = "ma_spielplan_info_popup.php?spielid="+spielid;	        	
	        	window.open(url,"spiel","width=700, height=670, top=30, left=50, menubar=no, directories=no, resizeable=no, toolbar=no, scrollbars=yes, status=no");
	        }
	        
 	        function tt_spiel_info(spielid)
	        {
	        	var url;
	        	url = "tt_ma_spielplan_info_popup.php?spielid="+spielid;
	        	window.open(url,"spiel","width=700, height=670, top=30, left=50, menubar=no, directories=no, resizeable=no, toolbar=no, scrollbars=yes, status=no");
	        }

	-->
	</SCRIPT>


<?php
function preview($content) {
	global $db;

// Fussball
	$search='#fbpreview#';
	$content=explode($search,$content);
	if (isset($content["1"])) {
		$sqlstr="select id,name from spielstaette where aktiv=1";
		$st=getResult($db,$sqlstr);
	
		$sqlstr  = "select s.id,s.spielstaette_id alt_spielstaette,t.spielstaette_id org_spielstaette,s.datum,t.name heim,m.name gast from ";
		$sqlstr .= "fb_spiele s,fb_tus_mannschaft t,fb_mannschaft m,fb_saison sa where ";
		$sqlstr .= "s.saison_id=sa.id and ";
		$sqlstr .= "sa.turnier=0 and ";
		$sqlstr .= "s.heim_id=t.id and ";
		$sqlstr .= "s.gespielt=0 and ";
		$sqlstr .= "s.datum>sysdate() and ";
		$sqlstr .= "s.aus_id=m.id ";
		$sqlstr .= "order by datum,t.name limit 8; ";
		$result=getResult($db,$sqlstr);

	   $ret="";
	  $ret .=  '<h2><u>Fussball - nächste Heimspiele</u></h2>';
		$ret .=  '<table>';
			$ret .=  '<tr>';
				$ret .=  '<td align="center"><b>Datum</b></td>';
	 			$ret .=  '<td align="center"><b>Spiel</b></td>';
	 			$ret .=  '<td align="center"><b>Spielort</b></td>';
			$ret .=  '</tr>';
		foreach ($result as $row) {
			$ret .=  '<tr>';
				$ret .= '<td>';
					$ret .=  date("d.m.Y - H:i",strtotime($row["datum"]))." (".convert_weekday(date("D",strtotime($row["datum"]))).")";
				$ret .=  '</td>';
				$ret .=  '<td align="center">';
					$ret .=  '&nbsp;&nbsp;<a HREF="javascript:fb_spiel_info('.$row["id"].')">';
						$ret .=  $row["heim"]." - ".$row["gast"];
					$ret .=  '</a>&nbsp;&nbsp;';
				$ret .=  '</td>';
				$ret .=  '<td>';
					if ($row["alt_spielstaette"]>0) 
						$ret .=  $st[($row["alt_spielstaette"]-1)]["name"];
					elseif ($row["org_spielstaette"]>0)
						$ret .=  $st[($row["org_spielstaette"]-1)]["name"];
					elseif ("1")
						$ret .=  "&nbsp;";
				$ret .=  '</td>';
			$ret .=  '</tr>';
		}
		$ret .=  '</table>';
	
		$ret=implode($ret,$content);
	} else {
		$ret=$content["0"];
	}


// Tischtennis
	$search='#ttpreview#';
	$content=explode($search,$ret);
	if (isset($content["1"])) {
		$sqlstr="select id,name from spielstaette where aktiv=1";
		$st=getResult($db,$sqlstr);
	
		$sqlstr  = "select s.id,s.spielstaette_id alt_spielstaette,t.spielstaette_id org_spielstaette,s.datum,t.name heim,m.name gast from ";
		$sqlstr .= "tt_spiele s,tt_tus_mannschaft t,tt_mannschaft m where ";
		$sqlstr .= "s.heim_id=t.id and ";
		$sqlstr .= "s.datum>sysdate() and ";
		$sqlstr .= "s.gespielt=0 and ";
		$sqlstr .= "s.aus_id=m.id ";
		$sqlstr .= "order by datum,t.name limit 8; ";
		$result=getResult($db,$sqlstr);

	   $ret="";
	  $ret .=  '<h2><u>Tischtennis - nächste Heimspiele</u></h2>';
		$ret .=  '<table>';
			$ret .=  '<tr>';
				$ret .=  '<td align="center"><b>Datum</b></td>';
	 			$ret .=  '<td align="center"><b>Spiel</b></td>';
	 			$ret .=  '<td align="center"><b>Spielort</b></td>';
			$ret .=  '</tr>';
		foreach ($result as $row) {
			$ret .=  '<tr>';
				$ret .= '<td>';
					$ret .=  date("d.m.Y - H:i",strtotime($row["datum"]))." (".convert_weekday(date("D",strtotime($row["datum"]))).")";
				$ret .=  '</td>';
				$ret .=  '<td align="center">';
					$ret .=  '&nbsp;&nbsp;<a HREF="javascript:tt_spiel_info('.$row["id"].')">';
						$ret .=  $row["heim"]." - ".$row["gast"];
					$ret .=  '</a>&nbsp;&nbsp;';
				$ret .=  '</td>';
				$ret .=  '<td>';
					if ($row["alt_spielstaette"]>0) 
						$ret .=  $st[($row["alt_spielstaette"]-1)]["name"];
					elseif ($row["org_spielstaette"]>0)
						$ret .=  $st[($row["org_spielstaette"]-1)]["name"];
					elseif ("1")
						$ret .=  "&nbsp;";
				$ret .=  '</td>';
			$ret .=  '</tr>';
		}
		$ret .=  '</table>';
	
		$ret=implode($ret,$content);
	} else {
		$ret=$content["0"];
	}
	return $ret;
}



switch($action)
{
	case 'edit':
		if (priv("news"))
		{
			$sql = "select * from sys_news"; 
			$result= GetResult($db,$sql);
			echo '<FORM NAME="news" METHOD="POST" ACTION="index.php?site=news&action=save&PHPSESSID='.session_id().'">';
			echo '<TABLE WIDTH="90%" BORDER="0">';
				echo '<TR BGCOLOR="#DDDDDD">';
					echo '<TD COLSPAN="2" ALIGN="LEFT" BGCOLOR="#DDDDDD">';
						echo '<B>Hier können Sie die Nachricht auf der Hauptseite ändern.</B>';
					echo '</TD>';
				echo '</TR>';
				echo '<TR>';
					echo '<TD>';
						echo 'Überschrift';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" CLASS="none">';
						echo '<INPUT TYPE="TEXT" SIZE="80" NAME="header" VALUE="'.$result[0]["header"].'" />';
					echo '</TD>';
				echo '</TR>';
				echo '<TR>';
					echo '<TD>';
						echo 'Inhalt:<br><br>#fbpreview#<br>#ttpreview#';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" CLASS="none">';
						echo '<TEXTAREA ROWS="10" COLS="60" NAME="content">';
						echo $result[0]["content"];
						echo '</TEXTAREA>';
					echo '</TD>';
				echo '</TR>';
				echo '<TR>';
					echo '<TD ALIGN="CENTER" COLSPAN="2" BGCOLOR="#DDDDDD">';
						echo '<INPUT TYPE="SUBMIT" VALUE="News ändern" />';
					echo '</TD>';
				echo '</TR>';
			echo '</TABLE>';
			echo '</FORM>';
		}
		else
			echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
		break;
	case 'save':
		if (priv("news"))
		{
			$sql = 'update sys_news set header = "'.htmlentities($_POST["header"]).'", content = "'.htmlentities($_POST["content"]).'", date = now()';
			$result = doSQL($db, $sql);
			if ($result["code"] == 0)
			{
				mlog("Die Startseite wurde gespeichert");
				echo '<A HREF="index.php?site=news&action=currentnews&PHPSESSID='.session_id().'">News erfolgreich geändert!</A>';
				echo '<SCRIPT TYPE="text/javascript">';
					echo 'setTimeout("window.location=\'index.php?site=news&action=currentnews&PHPSESSID'.session_id().'\'",1000);';
				echo '</SCRIPT>';
			}
			else
				echo '<A HREF="index.php?site=news&action=currentnews&'.SID.'">News konnte nicht erfolgreich geändert werden!</A>';
		}
		else
			echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
		break;
	case 'currentnews':
		echo '<TABLE WIDTH="90%" BORDER="0">';
			echo '<TR>';
				echo '<TD COLSPAN="2" BGCOLOR="#DDDDDD" ALIGN="CENTER">';
					echo '<B>Willkommen auf den Internetseiten des TuS Ocholt !</B>';
				echo '<TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD CLASS="eins">';
					echo '<BR>';
					$sql = "select * from sys_news"; 
					$result= GetResult($db,$sql);
					echo '<B><U>'.html_entity_decode($result[0]["header"]).'</B> ('.date("d.m.Y - H:i", strtotime($result[0]["date"])).')</U><BR><BR>';
					
					
				// Anzeigen der Startseite
					echo html_entity_decode(preview($result[0]["content"]));
					
					
				
					echo '<BR>&nbsp;';
				echo '</TD>';
				echo '<TD>';
					echo '<IMG SRC="images/tus_gr.jpg" BORDER="0" ALT="TuS Ocholt">';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
			echo '<TD COLSPAN="2" BGCOLOR="#DDDDDD" ALIGN="Left">Letzte Artikel</TD>';
			echo '</TR>';
			echo '<TR><TD COLSPAN="2">';
			
				$sqlstr="select descr,id,toc from sys_artikel where show_artikel=1 order by toc desc limit 3";
				$result=GetResult($db,$sqlstr);
				if (isset($result))
				{
				echo '<table WIDTH="100%" class="none" border="0">';
					
					foreach ($result as $artikel) 
					{
						echo '<TR>';
						echo '<TD class="none" ALIGN="LEFT">';
							echo '<a HREF="index.php?site=artikel_show&action=show&artikelid='.$artikel["id"].'">';
					  	   	echo $artikel["descr"];
					  		echo '</a>';
					  	echo '</TD>';
					  	echo '<TD class="none" ALIGN="RIGHT">';
					  		echo date("d.m.Y",strtotime($artikel["toc"]));
					  	echo '</TD>';
					  	echo '</TR>';
					  	
					}
					
				echo '</table>';
				}
						
				
			echo '</TD></TR>';
			
			
			echo '<TR>';
				echo '<TD COLSPAN="2" ALIGN="CENTER">';
					echo '<BR><U>Seiteninhalte :</U><BR>&copy; 2003-'.date('Y').' TuS Ocholt e.V.<BR><BR>';
					echo '<U>Programmierung der Webseiten :</U><BR>';
					//echo '<A HREF="mailto:&#118;&#108;&#111;&#115;&#99;&#104;&#64;&#103;&#109;&#120;&#46;&#110;&#101;&#116;">Volker Losch</a> und ';
					echo '<A HREF="mailto:vol&#64;tus-ocholt.de">Volker Losch</a> und ';
					echo '<A HREF="mailto:&#98;&#117;&#117;&#104;&#64;&#102;&#114;&#101;&#105;&#122;&#101;&#105;&#116;&#107;&#97;&#109;&#101;&#108;&#46;&#100;&#101;">Daniel Sager</a>';
					echo '<BR>&nbsp;';
				echo '<TD>';
			echo '</TR>';
		echo '</TABLE>';
		break;
}
?>