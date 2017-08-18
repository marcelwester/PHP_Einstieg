<?php
////

////
//// sysv_sportarten.php
//// Sys Sportarten Verwaltung
//// Änderung : Daniel, 06.03.2005
//// was : Volker erstellt



if (priv("sysv_sportarten"))
{
sitename("sysv_sportarten.php",$_SESSION["groupid"]);
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
	function popup(sysid, action)
	{
		var url;
		if (sysid == 0)
<?php
			echo 'url = "sysv_sportarten_popup.php?action="+action+"&PHPSESSID='.session_id().'";';
		echo ' else ';
			echo 'url = "sysv_sportarten_popup.php?action="+action+"&sysid="+sysid+"&PHPSESSID='.session_id().'";';
?>
		var heigth = 350;
		window.open(url,"sys","width=650, height="+heigth+", top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
	}
-->
</SCRIPT>

<?php
	$colspan_head = 2;

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
					echo '<B>neue Sportart anlegen</B>';
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
	                        echo '<B>Anzeigen</B>';
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>IDX</B>';
	                echo '</TD>';
  						 echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD" COLSPAN="2">';
	                        echo '<B>Aktion</B>';
	                echo '</TD>';
	        echo '</TR>';

			$sql = 'select id,name,show_menu,reihenfolge from sys_sportart order by show_menu desc,reihenfolge,name';
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
	                        if ($row["show_menu"]=="1") 
	                        	echo "ja";
	                        else
	                        	echo "nein";
	                echo '</TD>';

	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        echo $row["reihenfolge"];
	                echo '</TD>';

						 echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
							echo '<a href="javascript:popup('.$row["id"].',\'edit\');">';
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