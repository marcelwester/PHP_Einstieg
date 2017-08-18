<?php


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

//echo '<center><h1>Bingo</h1></center>';
for ($anzahl=0; $anzahl<30; $anzahl++) {
	echo '<div style="page-break-before: always">';
		for ($s=1; $s<=3; $s++) {
		echo '<table width="100%" align="center" border="0" cellpadding="20">';
		echo '<tr>';
		for ($i=1; $i<=3; $i++) {
			$b=bingo_box();
			//sort($b);
			unset($dummy);
			$dummy=array();
			echo '<td align="center">';
			echo '<b>Spiel '.$i.'</b>';
			echo '<table width="100%" align="center" border="2" cellpadding="0">';
			echo '<b><tr>';
			echo '<td bgcolor="#505050" width="20%" align="center"><b>B</b></td>';
			echo '<td bgcolor="#AAAAAA" width="20%" align="center"><b>I</b></td>';
			echo '<td bgcolor="#AAAAAA" width="20%" align="center"><b>N</b></td>';
			echo '<td bgcolor="#AAAAAA" width="20%" align="center"><b>G</b></td>';
			echo '<td bgcolor="#AAAAAA" width="20%" align="center"><b>O</b></td>';
			echo '</tr>';
			echo '<tr height="28">';
			$cr=0;
			$fp=fopen("/tmp/bingo1.lst", "a");
			$komma=""; $a="";
			foreach ($b as $row) {
				
				if ($cr==5) {
					echo '</tr><tr height="28">';
					$cr=0;
				}
				$cr++;
				echo '<td align="center" >';
					echo '<font size="+1">';
					echo $row.' ';
					echo '</font>';
				echo '</td>';
				$a .=$komma.$row;
				$komma=",";
				
				/*
				if (in_array($row,$dummy)) {
					echo 'Fehler: Zahl schon enthalten';
				}
				array_push($dummy,$row);
				*/
			}
			fputs($fp,$a);
			fputs($fp,"\n");
			echo '</tr>';
			echo '</table>';
			echo '</td>';
		
		}
		fclose($fp);
		echo '</tr>';
		echo '</table>';
		
		echo '<center>';
		echo '<font size="-2">';
		echo 'Die Teilnahme an dem BINGO Spiel ist kostenfrei. Es besteht kein Gewinnanspruch (der Rechtsweg ist ausgeschlossen).';
		if ($s != 3) {
			echo '<br><br><br><br><br><br><br><br><br>';
		}
		echo '</font>';
		echo '</center>';
	echo '<div>';
}
//

}
?>