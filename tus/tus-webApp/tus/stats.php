<?php
# Statistik.php
include "inc.php";

?>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php



	$sqlstr = "select toc,hits from sys_counter1 order by toc desc limit 10";
        $result = getResult($db,$sqlstr);
        
	echo '<TABLE WIDTH="70%" CLASS="none" align="center">';
	echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Tag</B></TD>';
	echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Besucher</B></TD></TR>';

	foreach($result as $row)
	{
		echo '<TR><TD ALIGN="CENTER" BGCOLOR="#FFFFFF">'.date("d.m.Y", strtotime($row["toc"])).'</TD>';
		echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">'.$row["hits"].'</TD></TR>';
	}

	$sql = "select value_i, value_d from sys_values where name='counter'";
	$result = getResult($db, $sql);
	$total = $result[0]["value_i"];
	$days = time() - strtotime($result["0"]["value_d"]);
	$days = floor(($days / 3600 / 24));
	$since = date("d.m.Y", strtotime($result[0]["value_d"]));


	echo '<TR><TD ALIGN="CENTER" BGCOLOR="#FFFFFF">Hits seit dem '.$since.' ('.$days.' Tage)</TD>';
	echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">'.$total.'</TD></TR>';

	echo '<TR><TD ALIGN="CENTER" BGCOLOR="#FFFFFF">Durschnitt pro Tag</TD>';
	echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">'.floor($total/$days).'</TD></TR>';

	echo '</TABLE>';
	
	echo '<br><br>';
	
# Sitestatistics
	echo "<h2>Seitenaufrufe seit dem 22.01.2005</h2>";
	$sqlstr="select site,alias,hits,hits_current_day from sys_statistic order by site";
	$result=GetResult($db,$sqlstr);
	echo '<TABLE WIDTH="70%" CLASS="none" ALIGN="CENTER">';
	echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Seite</B></TD>';
	echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Alias</B></TD>';
	echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Seiten-<br>aufrufe</B></TD>';
	echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Heute</B></TD></TR>';
	foreach ($result as $row) {
		echo '<tr>';
		echo '<td ALIGN="CENTER">';
			echo $row["site"];
		echo '</td>';	
		echo '<td ALIGN="CENTER">';
			if ($row["alias"]=="") 
				echo "-";
			else
				echo $row["alias"];
		echo '</td>';	
		echo '<td ALIGN="CENTER">';
			echo $row["hits"];
		echo '</td>';	
		echo '<td ALIGN="CENTER">';
			echo $row["hits_current_day"];
		echo '</td>';	
		echo '</tr>';
	}
	echo '</table>';
	echo '<br><br>';
# Spieleraufrufe Fussball
	echo "<h2>Fussball Spieler</h2>";
	$sqlstr  = "select p.id,p.name,p.vorname,i.hits,i.toc,i.hits_current_day,curdate() datum,DATE_FORMAT(toc,'%Y-%m-%d') toc1 ";
	$sqlstr .= "from sys_counter_spielerinfo i,fb_person p where ";
	$sqlstr .= "i.id=p.id and ";
	$sqlstr .= "i.sportart='fb' ";
	$sqlstr .= "order by hits";
        $result=getResult($db,$sqlstr);
	echo '<TABLE WIDTH="70%" CLASS="none" ALIGN="CENTER">';
	echo '<tr>';
		echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Name</B></TD>';	
		echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Seiten-<br>aufrufe</B></TD>';
		echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Heute</B></TD>';	
	        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Letzter Aufruf</B></TD>';	
	echo '</tr>';
	if (isset($result)) {
		foreach ($result as $row) {
			echo '<tr>';
			echo '<td ALIGN="CENTER">';
				echo $row["vorname"]."&nbsp;".$row["name"];
			echo '</td>';
			echo '<td ALIGN="CENTER">';
				echo $row["hits"];
			echo '</td>';
			echo '<td ALIGN="CENTER">';
				// Falls noch kein Bild seit dem Datumswechsel aufgerufen wurde 
				if ($row["datum"]!=$row["toc1"]) {
					echo '0';
				} else {
					echo $row["hits_current_day"];
				}
			echo '</td>';
			echo '<td ALIGN="CENTER">';
				echo $row["toc"];
			echo '</td>';
			echo '</tr>';
		}
	}
	echo '</table>';



# Imagestatistics
	echo "<h2>Bilderaufrufe seit dem 21.08.2004</h2>";
	$sqlstr="select id,name from sys_images_kat where id<>2";
	$result=GetResult($db,$sqlstr);
	if (isset($result)) {
		foreach ($result as $kat) {
			echo '<h3>'.$kat["name"].'</h3>';
			echo '<TABLE WIDTH="70%" CLASS="none" ALIGN="CENTER">';
			echo '<tr>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Beschreibung</B></TD>';	
				echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Seiten-<br>aufrufe</B></TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Heute</B></TD>';	
			        echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD"><B>Letzter Aufruf</B></TD>';	
			echo '</tr>';
			$sqlstr="select a.image_id,descr,toc,hits,hits_current_day,curdate() datum,DATE_FORMAT(toc,'%Y-%m-%d') toc1 from sys_statistic_images a, sys_images b where a.image_id=b.image_id  and kategorie=".$kat["id"]." order by hits";
			$result1=GetResult($db,$sqlstr);
			if (isset($result1)) {
				foreach ($result1 as $row1) {
					echo '<tr>';
					echo '<td ALIGN="CENTER">';
						echo '<a href="showimage2.php?id='.$row1["image_id"].'&nostats=1" target="Bilder">';
							echo $row1["descr"]." (".$row1["image_id"].")";
						echo '</a>';
					echo '</td>';
					echo '<td ALIGN="CENTER">';
						echo $row1["hits"];
					echo '</td>';
					echo '<td ALIGN="CENTER">';
						// Falls noch kein Bild seit dem Datumswechsel aufgerufen wurde 
						if ($row1["datum"]!=$row1["toc1"]) {
							echo '0';
						} else {
							echo $row1["hits_current_day"];
						}
					echo '</td>';
					echo '<td ALIGN="CENTER">';
						echo $row1["toc"];
					echo '</td>';
					echo '</tr>';
				}
			}
			echo '</table>';
		}
	}

closeConnect($db);
?>
</BODY>