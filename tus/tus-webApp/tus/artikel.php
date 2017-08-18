<?php
////
//// artikel.php
//// letzte Änderung : Volker, 31.03.2004 //// was : Datei erstellt
sitename("artikel.php",$_SESSION["groupid"]);





if (priv("artikel"))
{

?>

<SCRIPT LANGUAGE="JavaScript">
<!--
	function popup(artikelid, action)
	{
		var url;
		

<?php
			echo 'url = "artikel_popup.php?action="+action+"&artikelid="+artikelid+"&PHPSESSID='.session_id().'";';
?>
			var heigth = 580;
		window.open(url,"artikel","width=600, height="+heigth+", top=150, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
	}
-->
</SCRIPT>

<?php

	$colspan_head = 2;
	if ($_SESSION["groupid"] > 0)
	{
		echo '<CENTER><TABLE WIDTH="40%"  BORDER="0">';
			echo '<TR>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<a href="javascript:popup(0,\'add\');">';
					echo '<IMG SRC="images/new.jpg" BORDER="0" ALT="neu">';
					echo '</a>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<a href="javascript:popup(0,\'add\');">';
					echo '<B>neuen Artikel verfassen</B>';
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
	                        echo '<B>Datum</B>';
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>Beschreibung</B>';
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>Anzeigen</B>';
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
	                        echo '<B>Bilder</B>';
	                echo '</TD>';
	                
			echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD" COLSPAN="2">';
	                        echo '<B>Aktion</B>';
	                echo '</TD>';
			
	        echo '</TR>';

			$sql = 'select id,name,images,descr,show_artikel,toc from sys_artikel order by toc desc,name';
			$result = getResult($db, $sql);
		if (isset ($result))
			foreach ($result as $row)
			{
		        echo '<TR>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        echo $row["id"];
	                echo '</TD>';
                        table_link($row["name"],"index.php?site=artikel_show&action=show&artikelid=".$row["id"]);
	                //echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        //echo $row["name"];
	                //echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        echo date("d.m.Y", strtotime($row["toc"]));
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        echo $row["descr"];
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        if($row["show_artikel"]==1)
	                        	echo "Ja";
	                        else
	                        	echo "Nein";
	                echo '</TD>';
	                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                        if ($row["images"] == "") 
	                        	echo "-";
	                        else
	                        	echo $row["images"];
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

