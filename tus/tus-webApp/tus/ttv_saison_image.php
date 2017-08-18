<?php
////
//// ttv_saison_image.php
//// letzte Änderung : Volker, 19.06.2005 
//// was : Erstellung



if (priv("tt_saison"))
{
sitename("ttv_saison.php",$_SESSION["groupid"]);
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
	function popup(saisonid,teamid)
	{
		var url;
		if (saisonid == 0 && teamid == 0)
<?php
			echo 'url = "ttv_saison_image_popup.php?action=add&PHPSESSID='.session_id().'";';
		echo ' else ';
			echo 'url = "ttv_saison_image_popup.php?action=edit&saisonid="+saisonid+"&teamid="+teamid+"&PHPSESSID='.session_id().'";';
?>
		var heigth = 250;
		window.open(url,"saison","width=600, height="+heigth+", top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
	}
-->
</SCRIPT>

<?php
	
	$_url = 'index.php?site=ttverw&action=saisonimage&PHPSESSID='.session_id();
	if (!isset($_GET["closed"])) 
		$closed="0";
	else
		$closed=$_GET["closed"];
	
	
	if ($_SESSION["groupid"] > 0)
	{
		echo '<CENTER><TABLE WIDTH="40%" BORDER="0">';
			echo '<TR>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<a href="javascript:popup(0,0);">';
					echo '<IMG SRC="images/new.jpg" BORDER="0" ALT="neu">';
					echo '</a>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<a href="javascript:popup(0,0);">';
					echo '<B>neue Zuordnung anlegen</B>';
					echo '</a>';
				echo '</TD>';
			echo '</TR>';

		echo '</TABLE></CENTER><BR>';
	}

	echo '<CENTER><TABLE WIDTH="70%" BORDER="0">';
	        echo '<TR>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>Spielzeit</B>';
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>Liga</B>';
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>Saison Beginn</B>';
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>TuS<br>Mannschaft</B>';
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>Foto</B>';
	                echo '</TD>';
	            
	                if ($_SESSION["groupid"] > 0)
					{
						echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD" COLSPAN="2">';
	                        echo '<B>Aktion</B>';
	                	echo '</TD>';
					}
	        echo '</TR>';

			$sql = 'select team_id,saison_id,a.image_id,spielzeit,liga,startdatum from tt_saison_image a,tt_saison b where closed=0 and saison_id=id';
			$result = getResult($db, $sql);
			foreach ($result as $saison)
			{
		        echo '<TR>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        echo $saison["spielzeit"];
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        echo $saison["liga"];
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        echo date("d.m.Y", strtotime($saison["startdatum"]));
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        $sqlstr="select name from tt_tus_mannschaft where id=".$saison["team_id"];
	                        $result1=getResult($db,$sqlstr);
	                        echo $result1["0"]["name"];
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						if ($saison["image_id"] != 0) 
							echo $saison["image_id"];
						else
						   	echo "keins";
	                echo '</TD>';
					if ($_SESSION["groupid"] > 0)
					{
						echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
							echo '<a href="javascript:popup('.$saison["saison_id"].','.$saison["team_id"].');">';
								echo '<IMG SRC="images/edit.jpg" BORDER="0" ALT="Bearbeiten">';
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