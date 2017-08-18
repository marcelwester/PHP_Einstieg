<?php
include "inc.php";
if ($_SESSION["userid"]!="10") {
	echo 'Fehlende Berechtigung';
	exit;
}


function bingo_box () {
	$b=array();
	$i=array();
	$n=array();
	$g=array();
	$o=array();
	for ($j=1; $j<=5; $j++) {
		// b
		$x=rand(1,15);
		while (in_array($x,$b)) {
//			echo '*';
			$x=rand(1,15);
		}
		array_push($b,$x);

		// i
		$x=rand(16,30);
		while (in_array($x,$i)) {
//			echo '*';
			$x=rand(16,30);
		}
		array_push($i,$x);

		// n
		$x=rand(31,45);
		while (in_array($x,$n)) {
//			echo '*';
			$x=rand(31,45);
		}
		array_push($n,$x);

		// g
		$x=rand(46,60);
		while (in_array($x,$g)) {
//			echo '*';
			$x=rand(46,60);
		}
		array_push($g,$x);

		// o
		$x=rand(61,75);
		while (in_array($x,$o)) {
//			echo '*';
			$x=rand(61,75);
		}
		array_push($o,$x);
	}	
	
	$result=array();
	for ($j=0; $j<=4; $j++) {
		array_push($result,$b[$j]);
		array_push($result,$i[$j]);
		array_push($result,$n[$j]);
		array_push($result,$g[$j]);
		array_push($result,$o[$j]);
	}
	return $result;

}

function bingo_table ()
{
global $font,$table_height;
	// get bingo values
	$b=bingo_box();
		echo '<table width="90%" align="center" border="2" cellpadding="0">';
		echo '<b><tr height="'.$table_height.'">';
			echo '<td bgcolor="#AAAAAA" width="20%" align="center"><font size="'.$font.'"><b>B</b></font></td>';
			echo '<td bgcolor="#AAAAAA" width="20%" align="center"><font size="'.$font.'"><b>I</b></font></td>';
			echo '<td bgcolor="#AAAAAA" width="20%" align="center"><font size="'.$font.'"><b>N</b></font></td>';
			echo '<td bgcolor="#AAAAAA" width="20%" align="center"><font size="'.$font.'"><b>G</b></font></td>';
			echo '<td bgcolor="#AAAAAA" width="20%" align="center"><font size="'.$font.'"><b>O</b></font></td>';
			echo '</tr>';
			echo '<tr height="'.$table_height.'">';
			$cr=0;
			$komma=""; $a="";
			foreach ($b as $row) {
				
				if ($cr==5) {
					echo '</tr><tr height="'.$table_height.'">';
					$cr=0;
				}
				$cr++;
				echo '<td align="center">';
					echo '<font size="'.$font.'">';
					echo $row.' ';
					echo '</font>';
				echo '</td>';
				$a .=$komma.$row;
				$komma=",";
				
			}
			$fp=fopen("../../phptmp/bingo1.lst", "a");
				fputs($fp,$a);
				fputs($fp,"\n");
			fclose($fp);
		echo '</tr>';
		echo '</table>';
}



$font="+3";
$table_height="50";
$font1="+3";
$anzahl=10;


for ($i=1; $i<=$anzahl; $i++) {
	if ($i>1) echo '<div style="page-break-before: always">';
		echo '<table width="100%" align="center" border="0" cellpadding="20">';
			echo '<tr height="'.$table_height.'">';
				echo '<td align="center">';
					echo '<font size="'.$font1.'" face="Milano LET"><u><b>Bürger für Bürger</b></u></font>';
				echo '</td>';
				echo '<td align="center">';
					echo '<font size="'.$font1.'" face="Milano LET"><u><b>Bürger für Bürger</b></u></font>';
				echo '</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td align="center">';
					bingo_table();
				echo '</td>';
		
				echo '<td align="center">';
					bingo_table();
				echo '</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td align="center">';
					echo '<font size="-2">';
		 				echo 'Es besteht kein Gewinnanspruch (der Rechtsweg ist ausgeschlossen)';
					echo '</font>';
				echo '</td>';
				echo '<td align="center">';
					echo '<font size="-2">';
		 				echo 'Es besteht kein Gewinnanspruch (der Rechtsweg ist ausgeschlossen)';
					echo '</font>';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr height="36">';
			echo '</tr>';
			
			echo '<tr height="'.$table_height.'">';
				echo '<td align="center">';
					echo '<font size="'.$font1.'" face="Milano LET"><u><b>Bürger für Bürger</b></u></font>';
				echo '</td>';
				echo '<td align="center">';
					echo '<font size="'.$font1.'" face="Milano LET"><u><b>Bürger für Bürger</b></u></font>';
				echo '</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td align="center">';
					bingo_table();
				echo '</td>';
		
				echo '<td align="center">';
					bingo_table();
				echo '</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td align="center">';
					echo '<font size="-2">';
		 				echo 'Es besteht kein Gewinnanspruch (der Rechtsweg ist ausgeschlossen)';
					echo '</font>';
				echo '</td>';
				echo '<td align="center">';
					echo '<font size="-2">';
		 				echo 'Es besteht kein Gewinnanspruch (der Rechtsweg ist ausgeschlossen)';
					echo '</font>';
				echo '</td>';
			echo '</tr>';
		echo '</table>';
	if ($i>1) echo '</div>';
}

?>