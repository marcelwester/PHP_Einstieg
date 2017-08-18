<?php
////
//// artikel_show.php
////
//// letzte Änderung : Volker, 04.04.2004 
//// was : Erstellung
////
sitename("artikel_show.php",$_SESSION["groupid"]);
switch ($_REQUEST["action"]) {
	case 'list':
		$sql = 'select id,name,descr,toc,verfasser from sys_artikel where show_artikel=1 order by toc desc';
		$result = getResult($db, $sql);
		//print_r($result);
		if (isset ($result))
		{
			echo '<table WIDTH="70%">';
			echo '<tr><td><b>Artikel</b></td><td><b>Beschreibung</b></td><td><b>Datum</b></td><td><b>Verfasser</b></td></tr>';
			foreach ($result as $artikel) {
				echo '</tr>';		
				table_link($artikel["name"],"index.php?site=artikel_show&action=show&artikelid=".$artikel["id"]);
				//echo '<td>'.$artikel["name"].'</td>';
				echo '<td>'.$artikel["descr"].'</td>';
				//echo '<td>'.$artikel["toc"].'</td>';
				echo '<td>'.date("d.m.Y", strtotime($artikel["toc"])).'</td>';
				echo '<td>'.$artikel["verfasser"].'</td>';
				echo '</tr>';		
			}
			echo '</table>';
		}
	break;

	case 'show':
		
		echo '<br><A HREF="index.php?site=artikel_show&action=list">Zurück zur Übersicht</A><br><br><br>';
		$artikelid=$_REQUEST["artikelid"];
		$sql = 'select content,id,name,images,descr,show_artikel,toc from sys_artikel where id='.$artikelid;
		$result = getResult($db, $sql);
		if (isset ($result))
		{
			echo image_replace($result["0"]["content"]);
		}
	break;
}


?>