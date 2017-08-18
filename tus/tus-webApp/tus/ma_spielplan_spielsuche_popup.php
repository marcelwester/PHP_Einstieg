<?php
//// ma_spielplan_spielsuche_popup.php
////
//// letzte Änderung : Volker, 25.02.2005
//// was : Erstellung
////
////

include "inc.php";
sitename("ma_spielplan_spielsuche_popup.php",$_SESSION["groupid"]);
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php
focus();
$teamid=$_GET["teamid"];

if (priv("spiele_edit") && priv_team($teamid)) 
{
	switch ($_REQUEST["action"])
	{
		case 'search':		
			$userid=$_SESSION["userid"];
			$saisonid=$_GET["saisonid"];
			
			// Spielzeit lesen
			$sqlstr ="select liga,spielzeit from fb_saison where id=$saisonid";
			$result = GetResult($db, $sqlstr);
			$liga=$result[0]["liga"];
			$spielzeit=$result[0]["spielzeit"];
		
			$result=GetResult($db,"select name from sys_users where userid=$userid");
			$username=$result["0"]["name"];
			
			echo '<TABLE WIDTH="100%" BORDER="0" >';
			echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
			echo '<B>Spiel suchen und eintragen</B><BR>'.$liga.' - '.$spielzeit.'<br>- '.$username.' -</TD></TR>';
			echo '</TABLE>';
		
		
		        // Mannschaften der Saison lesen
		        $sql ="select id,name from fb_mannschaft, fb_zwtab_mannschaft_saison where
		               saison_id=$saisonid and mannschaft_id=id order by name";
		        $result=GetResult($db,$sql);
			echo '<form name="search">';
			        echo '<table BORDER=0 ALIGN=LEFT WIDTH=100% ><TD align="center"><b>Heimmannschaft</b></TD><TD align="center"><b>Gastmannschaft</b></TD>';
				        echo "<tr><TD ALIGN=CENTER>";
				         	build_select($result,"name","id","heimid","","15","");
				        echo "</TD>";
				        echo "<TD ALIGN=CENTER>";
				         	build_select($result,"name","id","ausid","","15","");
				        echo "</TD>";
				        echo "</tr><tr>";
				        echo '<td align="center" colspan="2">';
				        	echo '<input type="button" default value="Suchen und eintragen" onClick="searchMatch()" />';
				        	echo '&nbsp; &nbsp; &nbsp; &nbsp;';
				        	echo '<input type="button" value="Beenden" onClick="opener.location.reload(); window.close()" />';
				        echo '</td>';
				        echo '</tr>';
						
				echo '</table>';
			echo '</form>';
			
			// Alle Spiele der Saison lesen
			$sqlstr = "select id,heim_id,aus_id from fb_spiele where saison_id=".$saisonid;
			$result=getResult($db,$sqlstr);
			//print_r($result);
			echo '<script type="text/javascript">';
				echo 'function searchMatch() {';
					//echo 'alert("Test");';
				  	echo 'var heim_id = new Array('.count($result).'); ';
					echo 'var aus_id = new Array('.count($result).'); ';
					echo 'var spiel_id = new Array('.count($result).'); ';
					// fill arrays
					$idx="0";
					foreach ($result as $match) {
						echo 'heim_id['.$idx.']='.$match["heim_id"].';'; 
						echo 'aus_id['.$idx.']='.$match["aus_id"].';';
						echo 'spiel_id['.$idx.']='.$match["id"].';';
						$idx++;
					}
					echo 'var x=0;';
					echo 'var find=0;';
					echo 'var spiel=0;';
					//echo 'alert(document.search.heimid.value + " " + document.search.ausid.value);';
					
					echo 'for (x=0; x<'.count($result).' && find==0; x++) {';
						echo 'if (heim_id[x] == document.search.heimid.value && aus_id[x] == document.search.ausid.value) {';
						  //echo 'alert("Spielnr " + spiel_id[x]);';
						  echo 'find=1; spiel=x;';
						echo '}';
					echo '}';
					
					echo 'if (find==0) {';
						echo 'alert("Spiel nicht gefunden !");';
					echo '} else {';
						echo 'popup(spiel_id[spiel]);';
					echo '}';
				
				echo '}';
		
		
			        echo 'function popup(spielid) {';
        			echo 'var url;';
                        		echo 'url = "ma_spielplan_popup.php?action=edit&spielid="+spielid+"&PHPSESSID='.session_id().'";';
                			echo 'window.open(url,"spiel_edit","width=700, height=560, top=100, left=50, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");';
	        		echo'}';

		
		
		
		
		
				
				echo 'function test() {';
					echo 'alert("Test");';
				echo '}';
			echo '</script>';
		break;
	}
}
else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';

closeConnect($db);
?>
</BODY>
</HTML>