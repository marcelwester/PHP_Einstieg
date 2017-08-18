<?php
////
//// ttv_saison.php
//// letzte Änderung : Volker, 18.07.2004 
//// was : Anzeige der zugehörigen TuS Mannschaft

//// letzte Änderung : Daniel, 14.02.2004 17:35
//// was : Datei erstellt

if (priv("tt_saison"))
{
sitename("ttv_saison.php",$_SESSION["groupid"]);

?>

<SCRIPT LANGUAGE="JavaScript">
<!--
	function popup(saisonid, action)
	{
		var url;
		if (saisonid == 0)
<?php
			echo 'url = "ttv_saison_popup.php?action="+action+"&PHPSESSID='.session_id().'";';
		echo ' else ';
			echo 'url = "ttv_saison_popup.php?action="+action+"&saisonid="+saisonid+"&PHPSESSID='.session_id().'";';
?>
		if (action == "teams")
			var heigth = 500;
		else
			var heigth = 450;
		window.open(url,"saison","width=650, height="+heigth+", top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
	}
-->
</SCRIPT>

<?php

	$colspan_head = 2;
	
	$_url = 'index.php?site=ttverw&action=saison&PHPSESSID='.session_id();
	
	if (!isset($_GET["closed"])) 
		$closed="0";
	else
		$closed=$_GET["closed"];
	
	

	if ($_SESSION["groupid"] > 0)
	{
		echo '<CENTER><TABLE WIDTH="40%" BORDER="0">';
			echo '<TR>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<a href="javascript:popup(0,\'add\');">';
					echo '<IMG SRC="images/new.jpg" BORDER="0" ALT="neu">';
					echo '</a>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<a href="javascript:popup(0,\'add\');">';
					echo '<B>neue Saison anlegen</B>';
					echo '</a>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" colspan="2">';
					if ($closed=="1") {
						echo '<a href="'.$_url.'&closed=0">';
						echo '<B>Nur die aktuellen Saisons anzeigen</B>';	
					} else {
						echo '<a href="'.$_url.'&closed=1">';
						echo '<B>Zusätzlich die abgeschlossenen Saisons anzeigen</B>';	
					}
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
	                        echo '<B>Spielzeit</B>';
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>Liga</B>';
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>Kategorie</B>';
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>TuS<br>Mannschaft</B>';
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>Saison Beginn</B>';
	                echo '</TD>';
	            
	                if ($_SESSION["groupid"] > 0)
					{
						echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD" COLSPAN="2">';
	                        echo '<B>Aktion</B>';
	                	echo '</TD>';
					}
	        echo '</TR>';

			$sql = 'select * from tt_saison where closed=0 order by startdatum desc, kat desc';
			$result = getResult($db, $sql);
			foreach ($result as $saison)
			{
		        echo '<TR>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        echo $saison["id"];
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        echo $saison["spielzeit"];
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        echo $saison["liga"];
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        echo $saison["kat"];
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        $sqlstr1='select name from tt_tus_mannschaft,tt_zwtab_mannschaft_saison where id=mannschaft_id and saison_id='.$saison["id"]." order by name";
	                        $result1=GetResult($db,$sqlstr1);
	                        if (isset($result1)) 
	                        	foreach ($result1 as $mannschaft) {
	                        		echo $mannschaft["name"].'<br>';
	                        	}
	                        else
	                        	echo "-";
	                echo '</TD>';

	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        echo date("d.m.Y", strtotime($saison["startdatum"]));
	                echo '</TD>';
					if ($_SESSION["groupid"] > 0)
					{
						echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
							echo '<a href="javascript:popup('.$saison["id"].',\'edit\');">';
								echo '<IMG SRC="images/edit.jpg" BORDER="0" ALT="Bearbeiten">';
							echo '</a>';
						echo '</TD>';
						echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
							echo '<a href="javascript:popup('.$saison["id"].',\'teams\');">';
								echo '<IMG SRC="images/tore.jpg" BORDER="0" ALT="Mannschaften zuweisen">';
							echo '</a>';
						echo '</TD>';
					}
		        echo '</TR>';
			}
			
			
			if ($closed == "1") {
				echo '<tr>';
					echo '<td BGCOLOR="#DDDDDD" colspan="8" align="center">';
						echo '<b>Abgeschlossene Saison</b>';		
					echo '</td>';
				echo '</tr>';
			
				$sql = 'select * from tt_saison where closed=1 order by kat,startdatum desc, kat desc';
				$result = getResult($db, $sql);
				foreach ($result as $saison)
				{
			        echo '<TR>';
		                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
		                        echo $saison["id"];
		                echo '</TD>';
		                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
		                        echo $saison["spielzeit"];
		                echo '</TD>';
		                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
		                        echo $saison["liga"];
		                echo '</TD>';
		                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
		                        echo $saison["kat"];
		                echo '</TD>';
		                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
		                        $sqlstr1='select name from tt_tus_mannschaft,tt_zwtab_mannschaft_saison where id=mannschaft_id and saison_id='.$saison["id"];
		                        $result1=GetResult($db,$sqlstr1);
		                        if (isset($result1)) 
		                        	echo $result1["0"]["name"];
		                        else
		                        	echo "-";
		                echo '</TD>';
		                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
		                        if ($saison["image_id"]==0) 
		                        	echo "keins";
		                        else
		                               	echo $saison["image_id"];
		                echo '</TD>';
		                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
		                        echo date("d.m.Y", strtotime($saison["startdatum"]));
		                echo '</TD>';
						if ($_SESSION["groupid"] > 0)
						{
							echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
								echo '<a href="javascript:popup('.$saison["id"].',\'edit\');">';
									echo '<IMG SRC="images/edit.jpg" BORDER="0" ALT="Bearbeiten">';
								echo '</a>';
							echo '</TD>';
						}
			        echo '</TR>';
				}
			}
			
			
	echo '</TABLE></CENTER>';
}
else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
?>