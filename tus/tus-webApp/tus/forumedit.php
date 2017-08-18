<?php

//// letzte Änderung : Volker, 22.02.2007
//// was : Datei erstellt

if (priv("forumedit"))
{
sitename("forumedit.php",$_SESSION["groupid"]);
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
	function popup(id)
	{
		var url;
		if (id == 0)
<?php
			echo 'url = "forumedit_popup.php?action=add&PHPSESSID='.session_id().'";';
		echo ' else ';
			echo 'url = "forumedit_popup.php?action=edit&forumid="+id+"&PHPSESSID='.session_id().'";';
?>
		window.open(url,"forum","width=650, height=300, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
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
					echo '<B>neue Forum-Kategorie</B>';
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
                        echo '<B>IDX</B>';
                	echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                        echo '<B>sichtbar</B>';
                	echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                        echo '<B>Bearbeiten</B>';
                	echo '</TD>';
	        echo '</TR>';

			$sql = 'select id,name,idx,show_menu from sys_forum order by idx';
			$result = getResult($db, $sql);
			foreach ($result as $row)
			{
		        echo '<TR>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        echo $row["id"];
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        echo $row["name"];
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        echo $row["idx"];
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        if ($row["show_menu"]==1) echo "ja";
	                        if ($row["show_menu"]==0) echo "nein";
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
?>