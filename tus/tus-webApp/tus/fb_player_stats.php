<?php
// include "inc.php";
sitename("fb_player_stats.php",$_SESSION["groupid"]);

//$saisonid=135;
//$teamid=3;

echo '<CENTER><TABLE WIDTH="70%" BORDER="0">';
   echo '<TR>';
   echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
   echo '<B>Name</B>';
   echo '</TD>';
      echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
   echo '<B>INFO</B>';
   echo '</TD>';
   echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
   echo '<B>Anzahl Spiele</B>';
   echo '</TD>';
   echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
   echo '<B>mögliche Punkte</B>';
   echo '</TD>';
   echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
   echo '<B>geholte Punkte</B>';
   echo '</TD>';
   echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
   echo '<B>Erfolgsquote</B>';
   echo '</TD>';
   echo '</tr>';

$sqlstr="select p.vorname,p.name,count(*),p.id id from ".
         "fb_zwtab_spiele_person sp,fb_saison s,fb_person p ". 
         "where ".
         "sp.saison_id=s.id and ".
         "sp.person_id=p.id and ".
         "sp.team_id=".$teamid." and ".
         "s.id=".$saisonid.
         " group by p.vorname,p.name order by 3 desc,name";

$rs1=getResult($db,$sqlstr);


foreach ($rs1 as $player) {
	echo '<tr>';
		table_data($player["vorname"]." ".$player["name"]);
		$url='<a href="javascript:info('.$player["id"].','.$saisonid.');"><IMG SRC="images/info.gif" BORDER="0" ALT="Info"></a>';
		table_data($url);
		table_data($player["count(*)"]);

		// Siege pro person lesen
		$sqlstr="select sp.id,heim_tore,aus_tore,heim_id,aus_id from fb_zwtab_spiele_person zw,fb_spiele sp,fb_mannschaft h,fb_mannschaft g " .
				"where " .
				"zw.saison_id=".$saisonid." and " .
				"sp.id=zw.spiel_id and " .
				"h.id=sp.heim_id and " .
				"g.id=sp.aus_id and " .
				"zw.person_id=".$player["id"];
		$spiele=getResult($db,$sqlstr);

		
		$punkte=0;
		$tus_id=$teamid;


		foreach ($spiele as $spiel) {

			// Unentschieden
			if ($spiel["heim_tore"] == $spiel["aus_tore"]) { $punkte++; }
			// Heimsieg
		
			if ($spiel["heim_tore"] > $spiel["aus_tore"]) {
				if ($spiel["heim_id"] == $tus_id) {
					$punkte=($punkte + 3);
				}
			}
			//Auswaertssieg
			if ($spiel["heim_tore"] < $spiel["aus_tore"]) {
				if ($spiel["aus_id"] == $tus_id) {
					$punkte=($punkte + 3);
				}
			}
		}
		$spunkte=$player["count(*)"] * 3;
		table_data($player["count(*)"] * 3 );
		table_data($punkte);
		table_data(round($punkte / $spunkte * 100));
		
	echo '</tr>';
}
echo '</table>';
	




?>