<?php
//// preview.php
////
//// letzte Änderung : Volker, 15.10.2007
//// was : Erstellung

include "inc.php";
sitename("preview.php",$_SESSION["groupid"]);
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php
   echo "Preview";
?>
  	<SCRIPT LANGUAGE="JavaScript">
	<!--
	        
	        function fb_spiel_info(spielid)
	        {
	        	var url;
	        	url = "ma_spielplan_info_popup.php?spielid="+spielid;	        	
	        	window.open(url,"spiel","width=700, height=670, top=30, left=50, menubar=no, directories=no, resizeable=no, toolbar=no, scrollbars=yes, status=no");
	        }
	        
 	        function tt_spiel_info(spielid)
	        {
	        	var url;
	        	url = "tt_ma_spielplan_info_popup.php?spielid="+spielid;
	        	window.open(url,"spiel","width=700, height=670, top=30, left=50, menubar=no, directories=no, resizeable=no, toolbar=no, scrollbars=yes, status=no");
	        }

	-->
	</SCRIPT>
<?php
	// Alle Spielstaetten lesen:
	$sqlstr="select id,name from spielstaette where aktiv=1";
	$st=getResult($db,$sqlstr);

	$sqlstr  = "select s.id,s.spielstaette_id alt_spielstaette,t.spielstaette_id org_spielstaette,s.datum,t.name heim,m.name gast from ";
	$sqlstr .= "fb_spiele s,fb_tus_mannschaft t,fb_mannschaft m where ";
	$sqlstr .= "s.heim_id=t.id and ";
	$sqlstr .= "s.datum>sysdate() and ";
	$sqlstr .= "s.aus_id=m.id ";
	$sqlstr .= "order by datum,t.name limit 8; ";
	$result=getResult($db,$sqlstr);
   echo '<h2><u>Fussball - nächste Heimspiele</u></h2>';
	echo '<table>';
		echo '<tr>';
			echo '<td align="center"><b>Datum</b></td>';
 			echo '<td align="center"><b>Spiel</b></td>';
 			echo '<td align="center"><b>Spielort</b></td>';
		echo '</tr>';
	foreach ($result as $row) {
		echo '<tr>';
			echo'<td>';
				echo date("d.m.Y - H:i",strtotime($row["datum"]))." (".convert_weekday(date("D",strtotime($row["datum"]))).")";
			echo '</td>';
			echo '<td align="center">';
				echo '&nbsp;&nbsp;<a HREF="javascript:fb_spiel_info('.$row["id"].')">';
					echo $row["heim"]." - ".$row["gast"];
				echo '</a>&nbsp;&nbsp;';
			echo '</td>';
			echo '<td>';
				if ($row["alt_spielstaette"]>0) 
					echo $st[($row["alt_spielstaette"]-1)]["name"];
				elseif ($row["org_spielstaette"]>0)
					echo $st[($row["org_spielstaette"]-1)]["name"];
				elseif ("1")
					echo "&nbsp;";
			echo '</td>';
		echo '</tr>';
	}
	echo '</table>';
?>


<?php
	$sqlstr  = "select s.id,s.spielstaette_id alt_spielstaette,t.spielstaette_id org_spielstaette,s.datum,t.name heim,m.name gast from ";
	$sqlstr .= "tt_spiele s,tt_tus_mannschaft t,tt_mannschaft m,spielstaette st where ";
	$sqlstr .= "s.heim_id=t.id and ";
	$sqlstr .= "s.datum>sysdate() and ";
	$sqlstr .= "s.aus_id=m.id ";
	$sqlstr .= "order by datum,t.name limit 8; ";
	$result=getResult($db,$sqlstr);
   echo '<h2><u>Tischtennis - nächste Heimspiele</u></h2>';
	echo '<table>';
		echo '<tr>';
			echo '<td align="center"><b>Datum</b></td>';
 			echo '<td align="center"><b>Spiel</b></td>';
 			echo '<td align="center"><b>Spielort</b></td>';
		echo '</tr>';
	foreach ($result as $row) {
		echo '<tr>';
			echo'<td>';
				echo date("d.m.Y - H:i",strtotime($row["datum"]))." (".convert_weekday(date("D",strtotime($row["datum"]))).")";
			echo '</td>';
			echo '<td>';
				echo '&nbsp;&nbsp;<a HREF="javascript:tt_spiel_info('.$row["id"].')">';
					echo $row["heim"]." - ".$row["gast"];
				echo '</a>&nbsp;&nbsp;';
			echo '</td>';
			echo '<td>';
				if ($row["alt_spielstaette"]>0) 
					echo $st[($row["alt_spielstaette"]-1)]["name"];
				elseif ($row["org_spielstaette"]>0)
					echo $st[($row["org_spielstaette"]-1)]["name"];
				elseif ("1")
					echo "&nbsp;";
			echo '</td>';
		echo '</tr>';
	}
	echo '</table>';

	
?>



</BODY>
</HTML>