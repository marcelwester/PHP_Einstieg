<?php
////
//// ttv_person.php
//// letzte Änderung : Daniel, 28.01.2004 23:05
//// was : Datei erstellt

	if (priv("tt_person"))
	{
	sitename("ttv_person.php",$_SESSION["groupid"]);
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
	function popup(personid)
	{
		var url;
		if (personid == 0)
<?php
			echo 'url = "ttv_person_popup.php?action=add&PHPSESSID='.session_id().'";';
		echo ' else ';
			echo 'url = "ttv_person_popup.php?action=edit&personid="+personid+"&PHPSESSID='.session_id().'";';
?>
		window.open(url,"spieler","width=650, height=370, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
	}
-->
</SCRIPT>

<?php
		$colspan_head = 2;
		echo '<CENTER><TABLE WIDTH="40%" BORDER="0">';
			echo '<TR>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<a href="javascript:popup(0);">';
					echo '<IMG SRC="images/new.jpg" BORDER="0" ALT="neu">';
					echo '</a>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<a href="javascript:popup(0);">';
					echo '<B>neue Person anlegen</B>';
					echo '</a>';
				echo '</TD>';
			echo '</TR>';
		echo '</TABLE></CENTER><BR>';

		echo '<CENTER><TABLE WIDTH="70%" BORDER="0">';
	        echo '<TR>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>ID</B>';
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>Name</B>';
	                echo '</TD>';
	                if ($_SESSION["groupid"] > 0)
					{
						echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>Aktion</B>';
	                	echo '</TD>';
					}
	        echo '</TR>';

			$sql_pers = 'select * from tt_person order by name';
			$result_pers = getResult($db, $sql_pers);
			foreach ($result_pers as $person)
			{
		        echo '<TR>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        echo $person["id"];
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        echo $person["name"].', '.$person["vorname"];
	                echo '</TD>';
					if ($_SESSION["groupid"] > 0)
					{
						echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
							if ($person["id"] > 0)
							{
								echo '<a href="javascript:popup('.$person["id"].');">';
									echo '<IMG SRC="images/edit.jpg" BORDER="0" ALT="Bearbeiten">';
								echo '</a>';
							}
							else
								echo '&nbsp;';
						echo '</TD>';
					}
		        echo '</TR>';
			}
		echo '</TABLE></CENTER>';
	}
	else
		echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
?>