<?php

//// letzte Änderung : Volker, 13.10.2007
//// was : Datei erstellt





sitename("spielstaettelist.php",$_SESSION["groupid"]);


	
	$sqlstr  = "select m.name mname,s.name sname,strasse,ort,tel ";
	$sqlstr .= "from spielstaette s,fb_tus_mannschaft m where ";
	$sqlstr .= "spielstaette_id=s.id order by idx";
	$result = getResult($db, $sql);
	
	print_r($result);

	echo '<CENTER><TABLE WIDTH="70%" BORDER="0">';
	        echo '<TR>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>ID</B>';
	                echo '</TD>';
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
		        echo '</TR>';
		}
	echo '</TABLE></CENTER>';
?>