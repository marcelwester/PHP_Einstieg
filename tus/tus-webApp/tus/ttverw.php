<?php

//// ttverw.php
//// letzte Änderung : Daniel, 14.02.2004 14:26
//// was : - erstellt
////

if (priv("ttverw"))
{
sitename("ttverw.php",$_SESSION["groupid"]);
$submenu = array(	"saison" => array("id" => 'saison', "title" => 'Saisons verwalten', "link" => 'index.php?site=ttverw&action=saison&PHPSESSID='.session_id()),
                    "saisonimage" => array("id" => 'saisonimage', "title" => 'Mannschaftsfotos', "link" => 'index.php?site=ttverw&action=saisonimage&PHPSESSID='.session_id()),
                    "person" => array("id" => 'person', "title" => 'Personen verwalten', "link" => 'index.php?site=ttverw&action=person&PHPSESSID='.session_id()),
                    "team" => array("id" => 'team', "title" => 'Mannschaften verwalten', "link" => 'index.php?site=ttverw&action=team&PHPSESSID='.session_id()),
                    "team_tus" => array("id" => 'team_tus', "title" => 'TUS Mannschaften verwalten', "link" => 'index.php?site=ttverw&action=team_tus&PHPSESSID='.session_id())
                 );
echo '<h3>Tischtennis - Verwaltung</h3>';
echo '<TABLE WIDTH="100%" BORDER="0">';
        echo '<TR>';
                foreach($submenu as $sm_row)
                {
                        if ($sm_row["id"] == $action)
                        {
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<B>'.$sm_row["title"].'</B>';
                                echo '</TD>';
                        }
                        else
                        {
                                echo '<TD ALIGN="CENTER" BGCOLOR="'.$adm_link.'" onMouseOver="this.style.backgroundColor=\''.$adm_link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$adm_link.'\';" onClick="location.href=\''.$sm_row["link"].'\'">';
                                        echo $sm_row["title"];
                                echo '</TD>';
                        }

                }
        echo '</TR>';
echo '</TABLE>';

echo '<TABLE WIDTH="100%" BORDER="0">';
        echo '<TR>';
                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF"><BR><BR>';

switch($action)
{
	case 'saison':
		include "ttv_saison.php";
		break;

	case 'saisonimage':
		include "ttv_saison_image.php";
		break;

	
	case 'person':
		include "ttv_person.php";
		break;
	
	case 'team':
		include "ttv_team.php";
		break;
	case 'team_tus':
	/// Spiel löschen
		if ($_REQUEST["do"] == del && priv(spiel_del))
		{
			echo 'Wollen sie wirklich löschen...';
		}
		elseif ($_REQUEST["do"] == del2 && priv("spiel_del"))
		{
			echo 'löschen...';
		}
		else
		{
    		include "ttv_team_tus.php";
    	}
        break;
}
                        echo '<BR>&nbsp;';
                echo '</TD>';
        echo '</TR>';
echo '</TABLE>';
}
else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
?>