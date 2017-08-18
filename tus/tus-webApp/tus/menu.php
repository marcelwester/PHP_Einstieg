<NOSCRIPT><CENTER><FONT COLOR="RED"><B>Sie müssen JavaScript aktivieren, um dieses Menü zu benutzen zu können!</B></FONT></CENTER><BR></NOSCRIPT>
<?php
echo '<TABLE WIDTH="100%">';
        if ($_SESSION["groupid"] > 0)
        {
            echo '<TR>';
                    echo '<TD ALIGN="CENTER""><B>Administration</B></TD>';
            echo '</TR>';
            echo '<TR>';
                    echo '<TD BGCOLOR="'.$adm_link.'" onMouseOver="this.style.backgroundColor=\''.$adm_link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$adm_link.'\';" onClick="location.href=\'index.php?site=login&action=edituser&PHPSESSID='.session_id().'\'">';
                            echo '<CENTER>Mein Profil</CENTER>';
                    echo '</TD>';
            echo '</TR>';
			if (priv("news"))
			{

                echo '<TR>';
                        echo '<TD BGCOLOR="'.$adm_link.'" onMouseOver="this.style.backgroundColor=\''.$adm_link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$adm_link.'\';" onClick="location.href=\'index.php?site=news&action=edit&PHPSESSID='.session_id().'\'">';
                                echo '<CENTER>News ändern</CENTER>';
                        echo '</TD>';
                echo '</TR>';
			}
			if (priv("counter"))
			{
                echo '<TR>';
                        echo '<TD BGCOLOR="'.$adm_link.'" onMouseOver="this.style.backgroundColor=\''.$adm_link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$adm_link.'\';" onClick="location.href=\'index.php?site=counter&action=start&PHPSESSID='.session_id().'\'">';
                                echo '<CENTER>Counter</CENTER>';
                        echo '</TD>';
                echo '</TR>';
			}
			if (priv("visitors"))
			{
                echo '<TR>';
                        echo '<TD BGCOLOR="'.$adm_link.'" onMouseOver="this.style.backgroundColor=\''.$adm_link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$adm_link.'\';" onClick="location.href=\'index.php?site=visitors&action=start&PHPSESSID='.session_id().'\'">';
                                echo '<CENTER>Besucher-Statistik</CENTER>';
                        echo '</TD>';
                echo '</TR>';
			}
			if (priv("messagelog"))
			{
                echo '<TR>';
                        echo '<TD BGCOLOR="'.$adm_link.'" onMouseOver="this.style.backgroundColor=\''.$adm_link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$adm_link.'\';" onClick="location.href=\'index.php?site=mlog&page=1&PHPSESSID='.session_id().'\'">';
                                echo '<CENTER>Ereignisanzeige</CENTER>';
                        echo '</TD>';
                echo '</TR>';
			}

			if (priv("sponsoren"))
			{
                echo '<TR>';
                        echo '<TD BGCOLOR="'.$adm_link.'" onMouseOver="this.style.backgroundColor=\''.$adm_link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$adm_link.'\';" onClick="location.href=\'index.php?site=sponsoren&action=edit&PHPSESSID='.session_id().'\'">';
                                echo '<CENTER>Sponsoren</CENTER>';
                        echo '</TD>';
                echo '</TR>';
			}
			if (priv("forumedit"))
			{
                echo '<TR>';
                        echo '<TD BGCOLOR="'.$adm_link.'" onMouseOver="this.style.backgroundColor=\''.$adm_link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$adm_link.'\';" onClick="location.href=\'index.php?site=forumedit&action=list&PHPSESSID='.session_id().'\'">';
                                echo '<CENTER>Forum</CENTER>';
                        echo '</TD>';
                echo '</TR>';
			}
			if (priv("artikel"))
			{

                echo '<TR>';
                        echo '<TD BGCOLOR="'.$adm_link.'" onMouseOver="this.style.backgroundColor=\''.$adm_link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$adm_link.'\';" onClick="location.href=\'index.php?site=artikel&PHPSESSID='.session_id().'\'">';
                                echo '<CENTER>Artikel</CENTER>';
                        echo '</TD>';
                echo '</TR>';
			}
			if (priv("fbvimages"))
			{
                echo '<TR>';
                        echo '<TD BGCOLOR="'.$adm_link.'" onMouseOver="this.style.backgroundColor=\''.$adm_link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$adm_link.'\';" onClick="location.href=\'index.php?site=images&action=start&PHPSESSID='.session_id().'\'">';
                                echo '<CENTER>Bilder</CENTER>';
                        echo '</TD>';
                echo '</TR>';
			}
			if (priv("user"))
			{
                echo '<TR>';
                        echo '<TD BGCOLOR="'.$adm_link.'" onMouseOver="this.style.backgroundColor=\''.$adm_link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$adm_link.'\';" onClick="location.href=\'index.php?site=users&action=user&PHPSESSID='.session_id().'\'">';
                                echo '<CENTER>Benutzer / Gruppen / Rechte</CENTER>';
                        echo '</TD>';
                echo '</TR>';
			}
			if (priv("fbverw"))
			{
                echo '<TR>';
                        echo '<TD BGCOLOR="'.$adm_link.'" onMouseOver="this.style.backgroundColor=\''.$adm_link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$adm_link.'\';" onClick="location.href=\'index.php?site=fbverw&action=saison&PHPSESSID='.session_id().'\'">';
                                echo '<CENTER>Fussball</CENTER>';
                        echo '</TD>';
                echo '</TR>';
            		}
	      if (priv("ttverw"))
			{
                echo '<TR>';
                        echo '<TD BGCOLOR="'.$adm_link.'" onMouseOver="this.style.backgroundColor=\''.$adm_link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$adm_link.'\';" onClick="location.href=\'index.php?site=ttverw&action=saison&PHPSESSID='.session_id().'\'">';
                                echo '<CENTER>Tischtennis</CENTER>';
                        echo '</TD>';
                echo '</TR>';
    		}
	      if (priv("spielstaette"))
			{
                echo '<TR>';
                        echo '<TD BGCOLOR="'.$adm_link.'" onMouseOver="this.style.backgroundColor=\''.$adm_link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$adm_link.'\';" onClick="location.href=\'index.php?site=spielstaette&PHPSESSID='.session_id().'\'">';
                                echo '<CENTER>Spielstätten</CENTER>';
                        echo '</TD>';
                echo '</TR>';
    		}


         if (priv("sysv_sportarten"))
			{
                echo '<TR>';
                        echo '<TD BGCOLOR="'.$adm_link.'" onMouseOver="this.style.backgroundColor=\''.$adm_link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$adm_link.'\';" onClick="location.href=\'index.php?site=sportarten-verwaltung&action=start&PHPSESSID='.session_id().'\'">';
                                echo '<CENTER>Sportarten</CENTER>';
                        echo '</TD>';
                echo '</TR>';
         }
        	


            	
         if (priv("email"))
			{
                echo '<TR>';
                        echo '<TD BGCOLOR="'.$adm_link.'" onMouseOver="this.style.backgroundColor=\''.$adm_link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$adm_link.'\';" onClick="location.href=\'index.php?site=mailverteiler&action=start&PHPSESSID='.session_id().'\'">';
                                echo '<CENTER>Mailverteiler</CENTER>';
                        echo '</TD>';
                echo '</TR>';
            		}
        	}




        echo '<TR>';
                echo '<TD ALIGN="CENTER""><B>Allgemeines</B></TD>';
        echo'</TR>';

    	echo '<TR>';
            echo '<TD BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\'index.php?site=news&action=currentnews&PHPSESSID='.session_id().'\'">';
                   echo '<CENTER>Aktuelles</CENTER>';
            echo '</TD>';
        echo '</TR>';

        // Sites lesen und darstellen
       if (priv("sys_site")) 
           $sql1 = 'select * from sys_site order by idx asc';
       else
           $sql1 = 'select * from sys_site where show_menu=1 order by idx asc';
       
        
        $result1 = GetResult($db, $sql1);
        if (isset($result1))
        {
        	foreach ($result1 as $sys_site)
        	{
                	echo'<TR>';
                        	echo '<TD BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\'index.php?site=site&action=show&siteid='.$sys_site["id"].'&PHPSESSID='.session_id().'\'">';
                                	echo '<CENTER>'.$sys_site["name"].'</CENTER>';
                        	echo '</TD>';
                	echo '</TR>';
        	}
		}

        echo '<TR><TD BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\'index.php?site=spielstaette&action=list&PHPSESSID='.session_id().'\'">';
                    echo '<CENTER>Spielstätten</CENTER>';
                echo '</TD>';
        echo '</TR>';

        echo '<TR><TD BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\'index.php?site=guestbook&action=start&PHPSESSID='.session_id().'\'">';
                    echo '<CENTER>Gästebuch</CENTER>';
                echo '</TD>';
        echo '</TR>';

        echo '<TR><TD BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\'index.php?site=forum&forummenue=yes&action=start&PHPSESSID='.session_id().'\'">';
                    echo '<CENTER>Forum/Mitteilungen</CENTER>';
                echo '</TD>';
        echo '</TR>';
// Untermenü Forum
		if ($_REQUEST["forummenue"] == 'yes' || $_REQUEST["forumid"] > 0)
		{
			echo'<TR>';
	           	echo '<TD CLASS="none">';
					$sql1 = 'select name,id from sys_forum where show_menu=1 order by idx';
					$result1 = GetResult($db, $sql1);
			        echo '<TABLE WIDTH="100%" BORDER="0">';
			        foreach ($result1 as $row)
			        {
				        echo'<TR>';
						echo '<TD WIDTH="20" CLASS="none">&nbsp;</TD>';
						echo table_link($row["name"],"index.php?site=forum&action=start&forumid=".$row["id"]."&PHPSESSID=".session_id());
			        }
			        echo '</TABLE>';
	           	echo '</TD>';
	       	echo '</TR>';
    	}
// Untermenü Forum ENDE
		

        echo '<TR>';
            echo '<TD BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\'index.php?site=artikel_show&action=list\'">';
				echo '<CENTER>Artikel</CENTER>';
            echo '</TD>';
	echo '</TR>';

        echo '<TR>';
            echo '<TD BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\'index.php?site=fanartikel&action=list\'">';
				echo '<CENTER>Fanartikel</CENTER>';
            echo '</TD>';
	echo '</TR>';

	echo '<TR>';
        	echo '<TD BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\'index.php?site=sponsoren&action=list\'">';
            	echo '<CENTER>Sponsoren</CENTER>';
        	echo '</TD>';
        echo '</TR>';
        echo '<TR>';
            if ($_SESSION["groupid"] == 0)
            {
                echo '<TD BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\'index.php?site=login&action=login&PHPSESSID='.session_id().'\'">';
                        echo '<CENTER>Login</CENTER>';
                echo '</TD>';
        	}
            else
            {
                echo '<TD BGCOLOR="'.$adm_link.'" onMouseOver="this.style.backgroundColor=\''.$adm_link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$adm_link.'\';" onClick="location.href=\'index.php?site=login&action=logout&PHPSESSID='.session_id().'\'">';
                        echo '<CENTER>Logout</CENTER>';
                echo '</TD>';
        	}
        echo '</TR>';
        echo '<TR>';
                echo '<TD ALIGN="CENTER"><B>Sportarten</B></TD>';
        echo'</TR>';
        echo'<TR>';
           	echo '<TD BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\'index.php?site=fb_start&action=currentnews&fbmenu=yes&PHPSESSID='.session_id().'\'">';
                   	echo '<CENTER>Fussball</CENTER>';
           	echo '</TD>';
       	echo '</TR>';
// Untermenü Fussball
		if ($_REQUEST["fbmenu"] == 'yes')
		{
			echo'<TR>';
	           	echo '<TD CLASS="none">';
					$sql1 = 'select * from fb_tus_mannschaft where show_menu=1 order by reihenfolge';
					$result1 = GetResult($db, $sql1);
			        echo '<TABLE WIDTH="100%" BORDER="0">';
			        foreach ($result1 as $sbteam)
			        {
					$sql="select current_saison from fb_tus_mannschaft where id=".$sbteam["id"];
					$result4=GetResult($db,$sql);
					if ($result4["0"]["current_saison"] == 0) {
						$sql = 'select max(saison_id) from fb_zwtab_mannschaft_saison where mannschaft_id  = '.$sbteam["id"];
						$result3 = getResult($db, $sql);
						if ($result3[0]["max(saison_id)"] > 0)
						{
					        $sql = 'select max(datum) from fb_spiele where  heim_id = '.$sbteam["id"].' or aus_id = '.$sbteam["id"];
					        $result_max_date = GetResult($db, $sql);
					        $sql = 'select saison_id from fb_spiele where  datum = "'.$result_max_date[0]["max(datum)"].'" and (heim_id = '.$sbteam["id"].' or aus_id = '.$sbteam["id"].')';
					        $result2 = GetResult($db, $sql);
							if ($result2[0]["saison_id"] > 0)
								$saisonid = $result2[0]["saison_id"];
							else
								$saisonid = $result3[0]["max(saison_id)"];
						}
					} else {
						$saisonid=$result4["0"]["current_saison"];
					}
				
				        echo'<TR>';
					echo '<TD WIDTH="20" CLASS="none">&nbsp;</TD>';
				        echo '<TD BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\'index.php?site=team&action=start&teamid='.$sbteam["id"].'&saisonid='.$saisonid.'&fbmenu=yes&PHPSESSID='.session_id().'\'">';
				        echo '<CENTER>'.$sbteam["name"].'</CENTER>';
				        echo '</TD>';
				        echo '</TR>';
				        unset($saisonid);
					    
			        }
			        echo '</TABLE>';
	           	echo '</TD>';
	       	echo '</TR>';
    	}
// Untermenü Fussball ENDE

// Anfang: Entwicklung 18.09.2004: Anzeige von Tischtennis Menu nur mit Berechtigung von ttverw

        echo'<TR>';
           	echo '<TD BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\'index.php?site=tt_start&action=currentnews&ttmenu=yes&PHPSESSID='.session_id().'\'">';
                   	echo '<CENTER>Tischtennis</CENTER>';
           	echo '</TD>';
       	echo '</TR>';

// Untermenü Tischtennis
		if ($_REQUEST["ttmenu"] == 'yes')
		{
			echo'<TR>';
	           	echo '<TD CLASS="none">';
					$sql1 = 'select * from tt_tus_mannschaft where show_menu=1 order by reihenfolge';
					$result1 = GetResult($db, $sql1);
			        echo '<TABLE WIDTH="100%" BORDER="0">';

					  if (priv("tt_allgemein")) {	
				        echo'<TR>';
							  echo '<TD WIDTH="20" CLASS="none">&nbsp;</TD>';
					        echo '<TD BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\'index.php?site=tt_allgemein&action=start&ttmenu=yes&PHPSESSID='.session_id().'\'">';
						        echo '<CENTER>Allgemeines</CENTER>';
					        echo '</TD>';
				        echo '</TR>';
				     }

			        foreach ($result1 as $sbteam)
			        {
								$sql="select current_saison from tt_tus_mannschaft where id=".$sbteam["id"];
								$result4=GetResult($db,$sql);
								if ($result4["0"]["current_saison"] == 0) {
									$sql = 'select max(saison_id) from tt_zwtab_mannschaft_saison where mannschaft_id  = '.$sbteam["id"];
									$result3 = getResult($db, $sql);
									if ($result3[0]["max(saison_id)"] > 0)
									{
								        $sql = 'select max(datum) from tt_spiele where  heim_id = '.$sbteam["id"].' or aus_id = '.$sbteam["id"];
								        $result_max_date = GetResult($db, $sql);
								        $sql = 'select saison_id from tt_spiele where  datum = "'.$result_max_date[0]["max(datum)"].'" and (heim_id = '.$sbteam["id"].' or aus_id = '.$sbteam["id"].')';
								        $result2 = GetResult($db, $sql);
										if ($result2[0]["saison_id"] > 0)
											$saisonid = $result2[0]["saison_id"];
										else
											$saisonid = $result3[0]["max(saison_id)"];
									}
								} else {
									$saisonid=$result4["0"]["current_saison"];
								}
							        echo'<TR>';
										  echo '<TD WIDTH="20" CLASS="none">&nbsp;</TD>';
								        echo '<TD BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\'index.php?site=tt_team&action=start&teamid='.$sbteam["id"].'&saisonid='.$saisonid.'&ttmenu=yes&PHPSESSID='.session_id().'\'">';
									        echo '<CENTER>'.$sbteam["name"].'</CENTER>';
								        echo '</TD>';
							        echo '</TR>';
							        unset($saisonid);
								    
			        }
			        echo '</TABLE>';
	           	echo '</TD>';
	       	echo '</TR>';
    	}
// Untermenü Tischtennis ENDE

// Ende: Entwicklung 18.09.2004: Anzeige von Tischtennis Menu nur mit Berechtigung von ttverw


        // sportarten im menü ausgeben
		$sql1 = 'select * from sys_sportart where show_menu = 1 order by reihenfolge asc';
        $result1 = GetResult($db, $sql1);

        if (isset($result1))
        {
        	foreach ($result1 as $sportart)
        	{
                	echo'<TR>';
                        	echo '<TD BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\'index.php?site=sportart&action=show&sportart='.$sportart["id"].'&PHPSESSID='.session_id().'\'">';
                                	echo '<CENTER>'.$sportart["name"].'</CENTER>';
                        	echo '</TD>';
                	echo '</TR>';
        	}
		}

        if ($_SESSION["groupid"] > 0)
        {
                echo '<TR>';
                        echo '<TD BGCOLOR="#FFFFFF">';
                                echo '<CENTER><EM>eingeloggt als '.$_SESSION["login"].'('.$_SESSION["groupid"].')</CENTER></EM>';
                                echo '<CENTER><EM>automatische Abmeldung:</CENTER></EM>';
                                echo '<CENTER><EM>'.date("d.m.Y - H:i:s", time() + $SESSIONTIMEOUT).'</CENTER></EM>';
                                
                        echo '</TD>';
                echo '</TR>';
        }
echo '</TABLE>';
?>