<?php

//// letzte Änderung : Volker, 13.10.2007
//// was : Datei erstellt

sitename("spielstaette.php",$_SESSION["groupid"]);


// Spielstaetten nach Mannschaften anzeigen (grauer MenueEintrag)

if ($_REQUEST["action"]=="list") {
	echo "<h2><u>Spielstätten für Heimspiele</u></h2>";
	
	echo "<h3><b><u>Fussball</u></b></h3>";
	$sqlstr  = "select m.name mname,s.name sname,strasse,ort,tel ";
	$sqlstr .= "from spielstaette s,fb_tus_mannschaft m where ";
	$sqlstr .= "spielstaette_id=s.id and ";
	$sqlstr .= "show_menu=1 "; 
	$sqlstr .= "order by reihenfolge";
	$result = getResult($db, $sqlstr);
	
	echo '<CENTER><TABLE WIDTH="70%" BORDER="0">';
	        echo '<TR>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>Mannschaft</B>';
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>Name</B>';
	                echo '</TD>';
			echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                        	echo '<B>Strasse</B>';
                	echo '</TD>';
			echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                        	echo '<B>Ort</B>';
                	echo '</TD>';
	        echo '</TR>';

		foreach ($result as $row)
		{
		        echo '<TR>';
		                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        	echo $row["mname"];
		                        echo "&nbsp;";
		                echo '</TD>';
		                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        	echo $row["sname"];
		                        echo "&nbsp;";
		                echo '</TD>';
		                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
		                        echo $row["strasse"];
		                        echo "&nbsp;";
		                echo '</TD>';
		                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo $row["ort"];
					echo "&nbsp;";
		                echo '</TD>';
		        echo '</TR>';
		}
	echo '</TABLE></CENTER>';
	
	
	
	echo '<br><br>';
	
	
	
	echo "<h3><b><u>Tischtennis</u></b></h3>";
	$sqlstr  = "select m.name mname,s.name sname,strasse,ort,tel ";
	$sqlstr .= "from spielstaette s,tt_tus_mannschaft m where ";
	$sqlstr .= "spielstaette_id=s.id and ";
	$sqlstr .= "show_menu=1 "; 
	$sqlstr .= "order by reihenfolge";
	$result = getResult($db, $sqlstr);
	
	echo '<CENTER><TABLE WIDTH="70%" BORDER="0">';
	        echo '<TR>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>Mannschaft</B>';
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>Name</B>';
	                echo '</TD>';
			echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                        	echo '<B>Strasse</B>';
                	echo '</TD>';
			echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                        	echo '<B>Ort</B>';
                	echo '</TD>';
	        echo '</TR>';

		foreach ($result as $row)
		{
		        echo '<TR>';
		                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        	echo $row["mname"];
		                        echo "&nbsp;";
		                echo '</TD>';
		                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        	echo $row["sname"];
		                        echo "&nbsp;";
		                echo '</TD>';
		                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
		                        echo $row["strasse"];
		                        echo "&nbsp;";
		                echo '</TD>';
		                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo $row["ort"];
					echo "&nbsp;";
		                echo '</TD>';
		        echo '</TR>';
		}
	echo '</TABLE></CENTER>';


}

// Spielstaetten editieren (rosa MenueEintrag)
if (!isset($_REQUEST["action"])) {
	if (priv("spielstaette"))
	{
	?>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
		function popup(id)
		{
			var url;
			if (id == 0)
	<?php
				echo 'url = "spielstaette_edit_popup.php?action=add&PHPSESSID='.session_id().'";';
			echo ' else ';
				echo 'url = "spielstaette_edit_popup.php?action=edit&spielstaetteid="+id+"&PHPSESSID='.session_id().'";';
	?>
			window.open(url,"forum","width=500, height=300, top=300, left=300, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
		}
	-->
	</SCRIPT>
	
	<?php
	
		if ($_SESSION["groupid"] > 0)
		{
			echo '<CENTER><TABLE WIDTH="40%" BORDER="0">';
				echo '<TR>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo '<a href="javascript:popup(0);">';
						echo '<IMG SRC="images/new.jpg" BORDER="0" ALT="neu">';
						echo '</a>';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo '<a href="javascript:popup(0);">';
						echo '<B>neue Spielstätte</B>';
						echo '</a>';
					echo '</TD>';
				echo '</TR>';
			echo '</TABLE></CENTER><BR>';
		}
	
		echo '<CENTER><TABLE WIDTH="70%" BORDER="0">';
		        echo '<TR>';
		                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
		                        echo '<B>ID</B>';
		                echo '</TD>';
		                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
		                        echo '<B>Name</B>';
		                echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        	echo '<B>Strasse</B>';
	                	echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        	echo '<B>Ort</B>';
	                	echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        	echo '<B>Telefon</B>';
	                	echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        	echo '<B>Aktiv</B>';
	                	echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        	echo '<B>Bearbeiten</B>';
	                	echo '</TD>';
		        echo '</TR>';
	
				$sql = 'select id,name,strasse,ort,tel,aktiv from spielstaette order by id';
				$result = getResult($db, $sql);
				foreach ($result as $row)
				{
			        echo '<TR>';
		                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
		                        echo $row["id"];
		                        echo "&nbsp;";
		                echo '</TD>';
		                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
		                        echo $row["name"];
		                        echo "&nbsp;";
		                echo '</TD>';
		                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
		                        echo $row["strasse"];
		                echo '</TD>';
		                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo $row["ort"];
					echo "&nbsp;";
		                echo '</TD>';
		                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo $row["tel"];
					echo "&nbsp;";
		                echo '</TD>';
	       	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					if ($row["aktiv"] == 1)
						echo "ja";
					else
						echo "nein";
		                echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<a href="javascript:popup('.$row["id"].');">';
						echo '<IMG SRC="images/edit.jpg" BORDER="0" ALT="Bearbeiten">';
					echo '</a>';
				echo '</TD>';
			        echo '</TR>';
				}
		echo '</TABLE></CENTER>';
	}
	else
		echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
}
?>