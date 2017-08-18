<?php
   sitename("sportart.php",$_SESSION["groupid"]);
?>

	<SCRIPT LANGUAGE="JavaScript">
	<!--
	        
	        function popup_info(spielid)
	        {
	        	var url;
	        	<?php
	        	   echo 'url = "ma_spielplan_info_popup.php?spielid="+spielid+"&PHPSESSID='.session_id().'";';	
	        	?>
	        	window.open(url,"spiel","width=650, height=550, top=200, left=200, menubar=no, directories=no, resizeable=no, toolbar=no, scrollbars=yes, status=no");
	        }
	-->
	</SCRIPT>
<?php

		$tusteams = '';
		$sql = 'select id from fb_tus_mannschaft';
		$result = getResult($db,$sql);
		$tusteams_array = array();
		$x = 0;
		foreach($result as $team)
		{
			$tusteams .= $team["id"].',';
			$tusteams_array[$x] = $team["id"];
			$x++;
		}
		$tusteams = substr($tusteams, 0, strlen($tusteams)-1);

		$teams = array();
		$sql = 'select id, name from fb_mannschaft';
		$result = getResult($db,$sql);
		foreach($result as $team)
			if (in_array($team["id"], $tusteams_array))
				$teams[$team["id"]] = '<b>'.$team["name"].'</b>';
			else
				$teams[$team["id"]] = $team["name"];

		echo '<TABLE WIDTH="100%" BORDER="0">';
			echo '<TR>';
				echo '<TD VALIGN="TOP" CLASS="eins">';
					echo '<B>kommende Spiele</B><BR>';
					$sql = 'select id, datum , heim_id, aus_id from fb_spiele';
					$sql .= ' where datum > now() and gespielt = 0 and (heim_id in ('.$tusteams.') or aus_id in ('.$tusteams.'))';
					$sql .= ' order by datum asc limit 5';
					$result = getResult($db,$sql);
					if (isset($result[0]))
					{
						$height = count($result) * 26;
						echo '<TABLE HEIGHT="'.$height.'" WIDTH="100%" BORDER="0">';
						foreach($result as $match)
						{
							echo '<TR>';
								echo '<TD ALIGN="LEFT" CLASS="none">';
									echo '<A HREF="javascript:popup_info('.$match["id"].')">';
										echo $teams[$match["heim_id"]].' - '.$teams[$match["aus_id"]];
									echo '</A>';
								echo '</TD>';
								echo '<TD ALIGN="RIGHT" CLASS="none">';
									echo date("d.m.Y - H:i", strtotime($match["datum"]));
								echo '</TD>';
							echo '</TR>';
						}
						echo '</TABLE>';
					}
					else
						echo '<br><em> keine Einträge</em> ';
				echo '</TD>';
				echo '<TD CLASS="eins" VALIGN="TOP">';
					$sql = "select image_kl from fb_sponsoren where gueltig_ab < now() and gueltig_bis > now()"; 
					$result= GetResult($db,$sql);
					if ($result["code"] != 1)
					{
						srand ((float) microtime() * 10000000);
						$x = array_rand($result);
						echo '<B>zufälliger Sponsor</B><BR>';
						echo '<CENTER><a href="index.php?site=sponsoren&action=list&PHPSESSID='.session_id().'">';
							echo '<IMG SRC="showimage2.php?id='.$result[$x]["image_kl"].'" BORDER="0">';
						echo '</a></CENTER>';
					}
					else
						echo '<IMG SRC="images/tuswappen.jpg" BORDER="0" ALT="TuS Ocholt">';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD VALIGN="TOP" CLASS="eins">';
					echo '<B>absolvierte Spiele</B><BR>';
					$sql = 'select id, datum , heim_id, aus_id, heim_tore, aus_tore from fb_spiele';
					$sql .= ' where gespielt = 1 and (heim_id in ('.$tusteams.') or aus_id in ('.$tusteams.'))';
					$sql .= ' order by datum desc limit 5';
					$result = getResult($db,$sql);
					if (isset($result[0]))
					{
						$height = count($result) * 26;
						echo '<TABLE HEIGHT="'.$height.'" WIDTH="100%" BORDER="0">';
						foreach($result as $match)
						{
							echo '<TR>';
								echo '<TD ALIGN="LEFT" CLASS="none">';
									echo '<A HREF="javascript:popup_info('.$match["id"].')">';
										echo $teams[$match["heim_id"]].' - '.$teams[$match["aus_id"]];
									echo '</A>';
									echo ' ('.$match["heim_tore"].':'.$match["aus_tore"].')';
								echo '</TD>';
								echo '<TD ALIGN="RIGHT" CLASS="none">';
									echo date("d.m.Y - H:i", strtotime($match["datum"]));
								echo '</TD>';
							echo '</TR>';
						}
						echo '</TABLE>';
					}
					else
						echo '<br><em> keine Einträge</em> ';
				echo '</TD>';
				echo '<TD VALIGN="TOP" CLASS="eins">';
					echo '<B>neuste Spielberichte</B><BR>';
					$sql = 'select sb.spiel_id sp_id, sp.datum datum, sp.heim_id heim, sp.aus_id aus from fb_sp_bericht sb , fb_spiele sp ';
					$sql.= 'where sb.spiel_id = sp.id order by sb.date desc limit 5';
					$result = getResult($db,$sql);
					if (isset($result[0]))
					{
						$height = count($result) * 26;
						echo '<TABLE HEIGHT="'.$height.'" WIDTH="100%" BORDER="0">';
						foreach($result as $bericht)
						{
							echo '<TR>';
								echo '<TD ALIGN="LEFT" CLASS="none">';
									echo '<A HREF="javascript:popup_info('.$bericht["sp_id"].')">';
										echo $teams[$bericht["heim"]].' - '.$teams[$bericht["aus"]];
									echo '</A>';
								echo '</TD>';
								echo '<TD ALIGN="RIGHT" CLASS="none">';
									echo date("d.m.Y - H:i", strtotime($bericht["datum"]));
								echo '</TD>';
							echo '</TR>';
						}
						echo '</TABLE>';
					}
					if ($bla == 'nein')
						echo '<br><em>Keine Einträge</em> ';
				echo '</TD>';
			echo '</TR>';
/*
			echo '<TR>';
				echo '<TD COLSPAN="2" CLASS="eins">';
					echo '<BR>';
					$sql = "select * from sys_news"; 
					$result= GetResult($db,$sql);
					echo '<B><U>'.html_entity_decode($result[0]["header"]).'</B> ('.date("d.m.Y - H:i", strtotime($result[0]["date"])).')</U><BR><BR>';
					echo html_entity_decode($result[0]["content"]);
					echo '<BR>&nbsp;';
				echo '</TD>';
			echo '</TR>';
*/
		echo '</TABLE>';
?>