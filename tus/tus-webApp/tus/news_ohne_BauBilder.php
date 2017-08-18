<?php
sitename("news.php",$_SESSION["groupid"]);
// news.php
//
// letzte Änderung : Daniel, 01.03.2004
// - Datei erstellt
//

switch($action)
{
	case 'edit':
		if (priv("news"))
		{
			$sql = "select * from sys_news"; 
			$result= GetResult($db,$sql);
			echo '<FORM NAME="news" METHOD="POST" ACTION="index.php?site=news&action=save&PHPSESSID='.session_id().'">';
			echo '<TABLE WIDTH="90%" BORDER="0">';
				echo '<TR BGCOLOR="#DDDDDD">';
					echo '<TD COLSPAN="2" ALIGN="LEFT" BGCOLOR="#DDDDDD">';
						echo '<B>Hier können Sie die Nachricht auf der Hauptseite ändern.</B>';
					echo '</TD>';
				echo '</TR>';
				echo '<TR>';
					echo '<TD>';
						echo 'Überschrift';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" CLASS="none">';
						echo '<INPUT TYPE="TEXT" SIZE="80" NAME="header" VALUE="'.$result[0]["header"].'" />';
					echo '</TD>';
				echo '</TR>';
				echo '<TR>';
					echo '<TD>';
						echo 'Inhalt';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" CLASS="none">';
						echo '<TEXTAREA ROWS="10" COLS="60" NAME="content">';
						echo $result[0]["content"];
						echo '</TEXTAREA>';
					echo '</TD>';
				echo '</TR>';
				echo '<TR>';
					echo '<TD ALIGN="CENTER" COLSPAN="2" BGCOLOR="#DDDDDD">';
						echo '<INPUT TYPE="SUBMIT" VALUE="News ändern" />';
					echo '</TD>';
				echo '</TR>';
			echo '</TABLE>';
			echo '</FORM>';
		}
		else
			echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
		break;
	case 'save':
		if (priv("news"))
		{
			$sql = 'update sys_news set header = "'.htmlentities($_POST["header"]).'", content = "'.htmlentities($_POST["content"]).'", date = now()';
			$result = doSQL($db, $sql);
			if ($result["code"] == 0)
			{
				mlog("Die Startseite wurde gespeichert");
				echo '<A HREF="index.php?site=news&action=currentnews&PHPSESSID='.session_id().'">News erfolgreich geändert!</A>';
				echo '<SCRIPT TYPE="text/javascript">';
					echo 'setTimeout("window.location=\'index.php?site=news&action=currentnews&PHPSESSID'.session_id().'\'",1000);';
				echo '</SCRIPT>';
			}
			else
				echo '<A HREF="index.php?site=news&action=currentnews&'.SID.'">News konnte nicht erfolgreich geändert werden!</A>';
		}
		else
			echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
		break;
	case 'currentnews':
		echo '<TABLE WIDTH="90%" BORDER="0">';
			echo '<TR>';
				echo '<TD COLSPAN="2" BGCOLOR="#DDDDDD" ALIGN="CENTER">';
					echo '<B>Willkommen auf den Internetseiten des TuS Ocholt !</B>';
				echo '<TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD CLASS="eins">';
					echo '<BR>';
					$sql = "select * from sys_news"; 
					$result= GetResult($db,$sql);
					echo '<B><U>'.html_entity_decode($result[0]["header"]).'</B> ('.date("d.m.Y - H:i", strtotime($result[0]["date"])).')</U><BR><BR>';
					echo html_entity_decode($result[0]["content"]);
					echo '<BR>&nbsp;';
				echo '</TD>';
				echo '<TD>';
					echo '<IMG SRC="images/tus_gr.jpg" BORDER="0" ALT="TuS Ocholt">';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
			echo '<TD COLSPAN="2" BGCOLOR="#DDDDDD" ALIGN="Left">Letzte Artikel</TD>';
			echo '</TR>';
			echo '<TR><TD COLSPAN="2">';
			
				$sqlstr="select descr,id,toc from sys_artikel where show_artikel=1 order by toc desc limit 3";
				$result=GetResult($db,$sqlstr);
				if (isset($result))
				{
				echo '<table WIDTH="100%" class="none" border="0">';
					
					foreach ($result as $artikel) 
					{
						echo '<TR>';
						echo '<TD class="none" ALIGN="LEFT">';
							echo '<a HREF="index.php?site=artikel_show&action=show&artikelid='.$artikel["id"].'">';
					  	   	echo $artikel["descr"];
					  		echo '</a>';
					  	echo '</TD>';
					  	echo '<TD class="none" ALIGN="RIGHT">';
					  		echo date("d.m.Y",strtotime($artikel["toc"]));
					  	echo '</TD>';
					  	echo '</TR>';
					  	
					}
					
				echo '</table>';
				}
						
				
			echo '</TD></TR>';
			
			
			echo '<TR>';
				echo '<TD COLSPAN="2" ALIGN="CENTER">';
					echo '<BR><U>Seiteninhalte :</U><BR>&copy; 2003-'.date('Y').' TuS Ocholt e.V.<BR><BR>';
					echo '<U>Programmierung der Webseiten :</U><BR>';
					echo '<A HREF="mailto:&#118;&#108;&#111;&#115;&#99;&#104;&#64;&#101;&#119;&#101;&#116;&#101;&#108;&#46;&#110;&#101;&#116;">Volker Losch</a> und ';
					echo '<A HREF="mailto:&#98;&#117;&#117;&#104;&#64;&#102;&#114;&#101;&#105;&#122;&#101;&#105;&#116;&#107;&#97;&#109;&#101;&#108;&#46;&#100;&#101;">Daniel Sager</a>';
					echo '<BR>&nbsp;';
				echo '<TD>';
			echo '</TR>';
		echo '</TABLE>';
		break;
}
?>