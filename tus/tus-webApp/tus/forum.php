<?php
// forum
// Volker, 26.12.2006 Erstellung (als Kopie vom Gästebuch)
//                    

sitename("forum.php",$_SESSION["groupid"]);

if (!isset($_REQUEST["forumid"])) {
	$sqlstr  = "select count(*),max(datum),name,f.id from sys_forum_content c,sys_forum f where ";
	$sqlstr .= "forum_id=f.id and del=0 and show_menu=1 group by name order by idx";
	$result=getResult($db,$sqlstr);
	//
	// Forum Übersicht
	//
	echo '<TABLE WIDTH="60%" BORDER="0">';
		echo '<TR>';
			echo '<TD VALIGN="TOP" BGCOLOR="#DDDDDD" colspan="5" align="center">';
				echo '<h2>Übersicht</h2>';
			echo '</TD>';
		echo '</TR>';
		echo '<TR>';
			echo '<TD VALIGN="center"  ALIGN="CENTER">';
				echo '<B>Name</B>';
			echo '</TD>';
			echo '<TD VALIGN="center"  ALIGN="CENTER">';
				echo '<B>Letzter<br>Eintrag</B>';
			echo '</TD>';
			echo '<TD VALIGN="center"  ALIGN="CENTER">';
				echo '<B>Einträge<br>gesamt</B>';
			echo '</TD>';
			echo '<TD VALIGN="center"  ALIGN="CENTER">';
				echo '<B>Einträge<br>gestern</B>';
			echo '</TD>';
			echo '<TD VALIGN="center"  ALIGN="CENTER">';
				echo '<B>Einträge<br>heute</B>';
			echo '</TD>';
		
		echo '</TR>';
		if (isset($result)) {
			foreach ($result as $row) {
				echo '<TR>';
					$url="index.php?site=forum&action=start&forumid=".$row["id"]."&PHPSESSID=".session_id();
					table_link($row["name"],$url,"center");
					echo '<TD VALIGN="TOP" ALIGN="CENTER">';
						echo date("d.m.Y - H:i", strtotime($row["max(datum)"])).' Uhr';	
					echo '</TD>';
					echo '<TD VALIGN="TOP" ALIGN="CENTER">';
						echo $row["count(*)"];
					echo '</TD>';
					$currentDay=getdate();
					echo '<TD VALIGN="TOP" ALIGN="CENTER" bgcolor="'.$yellow.'">';
						$sqlstr  = "select count(*) anz from sys_forum_content where ";
						$sqlstr .= "del=0 and datum > '".date("Y-m-d", $currentDay["0"] - 86401)."' ";
						$sqlstr .= "and datum < '".date("Y-m-d", $currentDay["0"])."' ";
						$sqlstr .= "and forum_id=".$row[id];
						$dayCount = getResult($db,$sqlstr);
						echo $dayCount["0"]["anz"];
					echo '</TD>';
					echo '<TD VALIGN="TOP" ALIGN="CENTER" bgcolor="'.$green.'">';
						$sqlstr  = "select count(*) anz from sys_forum_content where ";
						$sqlstr .= "del=0 and datum>'".date("Y-m-d", $currentDay["0"] )."' ";
						$sqlstr .= "and forum_id=".$row[id];
						$dayCount = getResult($db,$sqlstr);
						echo $dayCount["0"]["anz"];
					echo '</TD>';
				echo '</TR>';
			}
		}
	echo '</table>';
	closeConnect($db);
	exit;
	
} else {
	$forumid=$_REQUEST["forumid"];
}


if (isset($_SESSION["userid"]) && priv("fo_official_message")) {
	$official="1";
	$READONLY_COLOR="#FFFFFF";
	$colspan++;
} else {
	unset($result);
	$official="0";
	$READONLY_COLOR="#FFFFFF";
}


$sqlstr="select value_i from sys_values where name='forum_closed'";
$result=GetResult($db,$sqlstr);
if ($result["0"]["value_i"]=="1") {
	$sqlstr = "select value_d,value_s from sys_values where name='forum_closed'";
	$result=getResult($db,$sqlstr);
	echo "<h2>Forum wurde am ".date("d.m.Y \u\m H:i", strtotime($result["0"]["value_d"]))." geschlossen</h2>";
	echo '<br>';
	if ($result["0"]["value_d"] != "") {
		echo '<br>';
		echo $result["0"]["value_s"];
	}
	echo '<br>';
	if (priv("forum")) {
		echo "<br><br><br>";
  		echo '<input type="Button" name="" value="Forum wieder freigeben" onclick="location.href=\'index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=forum_open&PHPSESSID='.session_id().'\'">';
  		echo "<br><br><br>";
		echo '<hr noshade size="1">';
	} else {
   		exit;
   	}
}

$sqlstr = "select name from sys_forum  where id=".$forumid. " and show_menu=1";
$result=getResult($db,$sqlstr);
if (!isset($result)) {
	echo "unbekannte forumid übergeben";
	exit;
}

echo "<h2><u>".$result["0"]["name"]."</u></h2>";

switch($action)
{

	case 'forum_open':
		if (priv("forum")) {
			echo "Forum wieder freigeben ... ";
			$sqlstr = "update sys_values set value_i=0 where name='forum_closed'";
			$result=doSQL($db,$sqlstr);
  			echo '<br><br>';
  			if ($result["code"] == 0)
			{
				mlog("forum wurde freigegeben.");
				echo '<A HREF="index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=start&PHPSESSID='.session_id().'">Forum erfolgreich freigegeben !</A>';
				echo '<SCRIPT TYPE="text/javascript">';
					echo 'setTimeout("window.location=\'index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=start&PHPSESSID='.session_id().'\'",1000);';
				echo '</SCRIPT>';
			}
			else
				echo '<A HREF="javascript:window.history.back()">Es ist ein Fehler aufgetreten. Das Forum konnte nicht freigegeben werden !</A>';
		} else {
			echo "Fehlende Berechtigung";
		}
	break;

	case 'edit':
		$sqlstr  = "select * from sys_forum_content  where ";
		$sqlstr .= "id=".$_GET["id"]." and ";
		$sqlstr .= "user_id=".$_SESSION["userid"];
		$result=getResult($db,$sqlstr);
		if (isset($result["0"])) {
			echo '<FORM NAME="GBOOK" METHOD="POST" ACTION="index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=save_edit&id='.$result["0"]["id"].'&PHPSESSID='.session_id().'">';
				echo '<TABLE WIDTH="90%">';
					echo '<TR BGCOLOR="#DDDDDD">';
						echo '<TD COLSPAN="1" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
							echo '<B>Forumeintrag ändern</B>';
						echo '</TD>';
					echo '</TR>';
					echo '<TR>';
						echo '<TD ALIGN="CENTER" >';
							echo '<TEXTAREA ROWS="7" COLS="60" NAME="content">';
								echo $result["0"]["content"];
							echo '</TEXTAREA>';
						echo '</TD>';
					echo '</TR>';
					echo '<TR>';
						echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
							echo '<INPUT TYPE="submit" ACTION="submit()" VALUE="Änderung speichern">';
							echo '&nbsp;&nbsp;&nbsp;';
							echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="self.location.href=\'index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=start&PHPSESSID='.session_id().'\'">';
						echo '</TD>';
					echo '</TR>';
				echo '</TABLE>';
			echo '</FORM>';
		} else {
			echo "<h2>Sie besitzen nicht die Berechtigung, diesen Datensatz zu ändern.</h2>";
		}
	break;
	
	case 'save_edit':
		$id=$_GET["id"];
		//$content = addslashes($_POST["content"]);
		$content = $_POST["content"];
		// Prüfen, ob der Datensatz geschrieben werden darf:
		$sqlstr  = "select count(*) anz from sys_forum_content  ";
		$sqlstr .= " where id=".$id." and user_id=".$_SESSION["userid"];
		$result=getResult($db,$sqlstr);
		if ($result["0"]["anz"]=="1") {
			$sqlstr  = "update sys_forum_content  ";
			$sqlstr .= "set content='".$content."'";
			$sqlstr .= " where id=".$id." and user_id=".$_SESSION["userid"];
			$result=doSQL($db,$sqlstr);
			if ($result["code"]=="0") {
				$link='index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=start&PHPSESSID='.session_id();
				echo '<a href="'.$link.'"><h3>Änderung wurden erfolgreich gespeichert</h3></a>';
			    echo '<SCRIPT TYPE="text/javascript">';
    	            echo 'setTimeout("self.location.href=\''.$link.'\'",1000);';
	        	echo '</SCRIPT>';
	        	mlog("Forumeintrag wurde nachträglich bearbeitet: ".$_REQUEST["id"]);
			} else {
				echo 'Fehler Beim Speichern der Änderungen';
			}
		} else {
			echo 'Fehler Beim Speichern der Änderungen';
		}
	break;

	case 'close':
        if (priv("forum")) {
			echo '<h2><u>Schließen des Forum (komplett)</u></h2>';
			echo '<FORM NAME="CLOSE_ACKNOLEDGE" METHOD="POST" ACTION="index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=close_ack&id='.$result["0"]["id"].'&PHPSESSID='.session_id().'">';
								echo '<TABLE WIDTH="90%">';
					echo '<TR BGCOLOR="#DDDDDD">';
						echo '<TD COLSPAN="1" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
							echo '<h2>Begründung</H2><b>Dieser Text wird anstelle des Forum angezeigt (max 250 Zeichen)!</b>';
						echo '</TD>';
					echo '</TR>';
					echo '<TR>';
						echo '<TD ALIGN="CENTER" >';
							echo '<TEXTAREA ROWS="7" COLS="60" NAME="content">';
							echo '</TEXTAREA>';
						echo '</TD>';
					echo '</TR>';
					echo '<TR>';
						echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
							echo '<INPUT TYPE="submit" ACTION="submit()" VALUE="Forum schliessen">';
							echo '&nbsp;&nbsp;&nbsp;';
							echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="self.location.href=\'index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=start&PHPSESSID='.session_id().'\'">';
						echo '</TD>';
					echo '</TR>';
				echo '</TABLE>';
			echo '<FORM>';
		} else {
			echo "Fehlende Berechtigung";
		}
	break;

	case 'close_ack':
        if (priv("forum")) {
		   echo '<br>Forum wird geschlossen... <br><br>';
		   $sqlstr = "delete from sys_values where name='forum_closed'";
		   $result=doSQL($db,$sqlstr);
		   
		   $sqlstr  = "insert into sys_values (name,value_i,value_s,value_d) values (";
		   $sqlstr .= "'forum_closed',";
		   $sqlstr .= "1,";
		   $sqlstr .= "'".$_POST["content"]."',";
		   $sqlstr .= "sysdate())";
		   $result=doSQL($db,$sqlstr);
  		
  			if ($result["code"] == 0)
			{
				mlog("Forum wurde geschlossen. ".$_POST["content"]);
				echo '<A HREF="index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=start&PHPSESSID='.session_id().'">Forum erfolgreich geschlossen !</A>';
				echo '<SCRIPT TYPE="text/javascript">';
					echo 'setTimeout("window.location=\'index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=start&PHPSESSID='.session_id().'\'",1000);';
				echo '</SCRIPT>';
			}
			else
				echo '<A HREF="javascript:window.history.back()">Es ist ein Fehler aufgetreten. Das Forum konnte nicht geschlossen werden !</A>';
		} else {
			echo "Fehlende Berechtigung";
		}
	break;

	
	case 'start':
		// For display all guestboook pages
		if (isset($_REQUEST["more"])) {
				$more="&more=1";
		}
		
		if (isset($_REQUEST["page"]))
			$page = $_REQUEST["page"];
		else
			$page = 1;

		$limit = ($page - 1) * 15;

		if (priv(forum))
			$colspan = 2;
		else
			$colspan = 1;

		// Anzeige von gelöschten Einträgen
        if (priv("forum")) {
    	echo '<br>';
			echo '<table width="40%" align="center">';
				echo '<tr>';
	                if ($_GET["del"]=="1") {
						echo table_link('Gelöschte Einträge nicht anzeigen','index.php?del=0&site=forum&forumid='.$forumid.'&forummenue=yes&action=start&PHPSESSID='.session_id());
	                	$del=1;
    		        } else { 
						echo table_link('Gelöschte Einträge anzeigen','index.php?del=1&site=forum&forumid='.$forumid.'&forummenue=yes&action=start&PHPSESSID='.session_id());
            			$del=0;
            		} 
				echo '</tr>';
				echo '<tr>';
					echo table_link('Forum schliessen','index.php?del=1&site=forum&forumid='.$forumid.'&forummenue=yes&action=close&PHPSESSID='.session_id());
				echo '</tr>';
			echo '</table>';
        } else {
        	$del=0;
        }

		if ($del=="1") 
			$sql = 'select count(*) from sys_forum_content where parent_id=0 and forum_id='.$forumid;
		else
			$sql = 'select count(*) from sys_forum_content  where parent_id=0 and del=0 and forum_id='.$forumid;
		
		$count = getResult($db,$sql);
		$count = $count[0][0];
		if ($count%15 == 0)
			$count = floor($count / 15);
		else
			$count = floor($count / 15) + 1;

		if ($count > 1)
		{
			echo '<BR><BR>Seite : ';
			$indx = 3;
			for ($x = 1; $x <= $count; $x++)
			{
				if ($x == $page)
				{
					echo ' <B>'.$x.'</B>';
				}
				else
				{
					echo ' <A HREF="index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=start'.$more.'&del='.$del.'&page='.$x.'&PHPSESSID='.session_id().'">';
					echo $x;
					echo '</A>';
				}
				if (($x == $indx) && !isset($more) ) {
					echo '&nbsp;. . .&nbsp;<A HREF="index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=start&more=1&del='.$del.'&PHPSESSID='.session_id().'"> weitere Seiten </A>';
					break;
				}
			}
		}

		echo '<BR><BR><A HREF="#write">Nachricht schreiben</A><BR><BR>';
        echo '<table align ="center">';
		echo '  <tr>';
		echo '   <td bgcolor='.$green.'>';
		echo '       Eintrag von heute';
		echo '   </td>';
		echo '   <td bgcolor='.$yellow.'>';
		echo '       Eintrag von gestern';
		echo '   </td>';
		echo '   <td bgcolor='.$white.'>';
		echo '       Eintrag von vorgestern und älter';
		echo '   </td>';
		echo '  </tr>';
		echo '</table>';
		echo '<br>';
		if ($del=="1") 
			$sql = 'select * from sys_forum_content  where parent_id=0 and forum_id='.$forumid.' order by toc_ts desc limit '.$limit.', 15';
		else
			$sql = 'select * from sys_forum_content  where parent_id=0 and del=0 and forum_id='.$forumid.' order by toc_ts desc limit '.$limit.', 15';
		
		$result = getResult($db,$sql);

		if (isset($result[0]))
			foreach($result as $entry)
			{
				if (strlen($entry["email"]) > 0)
					$name = '<A HREF="mailto:'.$entry["email"].'">'.$entry["autor"].'</A>';
				else
					$name = htmlentities($entry["autor"]);

				if (strlen($entry["inet"]) > 0)
					$name .= ' (<A HREF="'.$entry["inet"].'" TARGET="new window">Homepage</A>)';

				if (strlen($entry["ip"]) > 0 && priv("forum"))
					$name .= ' (IP : '.$entry["ip"].') ';

				echo '<TABLE WIDTH="80%" BORDER="0">';
					echo '<TR BGCOLOR="#DDDDDD">';
						if ($entry["user_id"]==0) {
							echo '<TD COLSPAN="'.$colspan.'" ALIGN="LEFT" BGCOLOR="#DDDDDD">';
								echo 'Nachricht von '.$name.' (am '.date("d.m.Y \u\m H:i", strtotime($entry["datum"])).' Uhr) :';
						} else {
							echo '<TD COLSPAN="'.$colspan.'" ALIGN="LEFT" BGCOLOR='.$adm_link.'>';
								echo 'Nachricht von '.$name.' (am '.date("d.m.Y \u\m H:i", strtotime($entry["datum"])).' Uhr - von angemeldetem Benutzer):';
						}
						# Neue Anwort schreiben (add_response)
						echo '&nbsp;&nbsp;&nbsp;Antwort&nbsp;';
						$url="index.php?site=forum&action=show_response&forumid=".$forumid."&PHPSESSID=".session_id()."&entryID=".$entry["id"]."#write";
						echo '<a href='.$url.'>schreiben</a>';

						# Link für Antworten lesen
						if ($del==0) {
							$sqlstr = "select max(datum) datum, count(*) anz from sys_forum_content where del=0 and (id=".$entry["id"]." or parent_id=".$entry["id"].")";
							$url="index.php?site=forum&del=0&action=show_response&forumid=".$forumid."&PHPSESSID=".session_id()."&entryID=".$entry["id"];
						} else {
							$sqlstr = "select max(datum) datum, count(*) anz from sys_forum_content where (id=".$entry["id"]." or parent_id=".$entry["id"].")";
							$url="index.php?site=forum&del=1&action=show_response&forumid=".$forumid."&PHPSESSID=".session_id()."&entryID=".$entry["id"];
						}
						$result1 = getResult($db,$sqlstr);
						$farbdatum="";
						if ($result1["0"]["anz"] > 1) {
							echo '&nbsp;&nbsp;/&nbsp;&nbsp;';
							echo '<a href='.$url.'>lesen ('.($result1["0"]["anz"]-1).')</a>';
							$farbdatum=$result["0"]["datum"];
						}
						echo '</TD>';
						
						// Farbliche Markierung der Einträge
						$color=$white;
						if (date("d.m.Y", strtotime($result1["0"]["datum"])) == date("d.m.Y")) {
							$color=$green;
						} 
						$currentDay=getdate();
						if (date("d.m.Y", (strtotime($result1["0"]["datum"]))) == date("d.m.Y", $currentDay["0"] - 86400)) {
							$color=$yellow;
						} 
						echo '<td width="1%" bgcolor="'.$color.'">';
							echo '&nbsp;';
						echo '</td>';
					echo '</TR>';
					echo '<TR>';
					

						if (priv("forum") && $del=="1" && $entry["del"]=="1") 
							echo '<TD CLASS="eins" bgcolor="#FFFF80" colspan="2">';
						else 
							echo '<TD CLASS="eins" colspan="2">';
							if ($entry["user_id"]>0)
								echo ereg_replace("\n", '<br>', $entry["content"]);
							else
								echo ereg_replace("\n", '<br>', htmlentities($entry["content"]));
						echo '</TD>';


						if ($official=="1") {
							if ($_SESSION["userid"]==$entry["user_id"]) {
							 	echo '<td align="center" style="width: 20px;">';
							 		if ($entry["del"]!="1") {
							 			echo '<a href="index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=edit&id='.$entry["id"].'&PHPSESSID='.session_id().'">';
							 				echo '<IMG SRC="images/edit.jpg" ALT="Bearbeiten" BORDER="0" HEIGHT="16" WIDTH="16">';
							 			echo '</a>';
							 		} else 
							 			echo '-';
								echo '</td>';
							}
						}



						if (priv("forum"))
						{
							echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" style="width: 20px;">';
								if ($del=="1" && $entry["del"] == "1") 
									echo '-';
								else 
									echo '<INPUT TYPE="CHECKBOX" ID="chk'.$entry["id"].'" onClick="enableDelete(this.id,\'del'.$entry["id"].'\',\'emp'.$entry["id"].'\')" />';
								echo '<DIV ID="emp'.$entry["id"].'" STYLE="display:block;">';
									echo '<IMG SRC="images/empty.gif" BORDER="0" HEIGHT="16" WIDTH="16">';
								echo '</DIV>';
								echo '<DIV ID="del'.$entry["id"].'" STYLE="display:none;">';
									echo '<A HREF="index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=del&id='.$entry["id"].'&PHPSESSID='.session_id().'">';
 										echo '<IMG SRC="images/del.gif" BORDER="0" ALT="Löschen" HEIGHT="16" WIDTH="16">';
									echo '</A>';
								echo '</DIV>';
							echo '</TD>';
						}
					echo '</TR>';
				echo '</TABLE><BR>';
			}
		echo '<BR><A NAME="write"></A>';

		// Dient zum Überspringen der Securityabfrage
		if ($official=="1") {
			echo '<FORM NAME="FORUM" METHOD="POST" ACTION="index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=saveack&PHPSESSID='.session_id().'">';
		} else {
			echo '<FORM NAME="FORUM" METHOD="POST" ACTION="index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=save&PHPSESSID='.session_id().'">';
		}
	
		if ($official=="1") {
			$sqlstr="select name,email from sys_users where userid=".$_SESSION["userid"];
			$result=getResult($db,$sqlstr);
		}
		
		// Dient zum Überspringen der Securityabfrage
		if ($official=="1") {
			$_SESSION["secimage"]=simpleRandString(5);
			echo '<INPUT TYPE="hidden" SIZE="10" NAME="sec" VALUE="'.$_SESSION["secimage"].'" />';
		}	



		echo '<TABLE WIDTH="80%" BORDER="0">';
			echo '<TR BGCOLOR="#DDDDDD">';
				echo '<TD COLSPAN="2" ALIGN="LEFT" BGCOLOR="#DDDDDD">';
					echo '<B>Hier können Sie eine Nachricht in unserem Forum hinterlassen.</B>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD CLASS="eins">';
					echo 'Ihr Name *';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" $BGCOLOR="'.$READONLY_COLOR.'" CLASS="none">';
					if ($official=="1") {
						echo '<INPUT TYPE="text" SIZE="80" NAME="name" VALUE="'.$result["0"]["name"].'" READONLY />';
					} else {
						echo '<INPUT TYPE="TEXT" SIZE="80" NAME="name" VALUE="" />';
					}
					
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD>';
					echo 'Ihre Emailadresse';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" CLASS="none">';
					if ($official=="1") {
						echo '<INPUT TYPE="text" SIZE="80" NAME="email" VALUE="'.$result["0"]["email"].'" READONLY />';
					} else {
						echo '<INPUT TYPE="TEXT" SIZE="80" NAME="email" VALUE="" />';
					}
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD>';
					echo 'Ihre Homepage';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" CLASS="none">';
					echo '<INPUT TYPE="TEXT" SIZE="80" NAME="hp" VALUE="http://www." />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD>';
					echo 'Ihre Nachricht *<br><br>(HTML möglich)';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" CLASS="none">';
					echo '<TEXTAREA ROWS="7" COLS="60" NAME="content">';
					echo '</TEXTAREA>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="CENTER" COLSPAN="2" BGCOLOR="#DDDDDD">';
					echo '<INPUT TYPE="hidden" name="official" value="'.$official.'"/>';
					echo '<INPUT TYPE="SUBMIT" VALUE="Nachricht speichern" />';
				echo '</TD>';
			echo '<TR>';
				echo '<TD ALIGN="CENTER" COLSPAN="2" BGCOLOR="#FFFFFF">';
					echo '- Mit * gekennzeichnete Felder sind Pflichtfelder !';
					echo '<br>- Keine diskriminierenden, rassenfeindlichen oder perversen Äußerungen !';
					echo '<br>- Administratoren werden Beiträge löschen, die gegen gewisse Regeln verstoßen.';
				echo '</TD>';
			echo '</TR>';
		echo '</TABLE>';
		echo '</FORM>';
		break;


	case 'show_response':
		// For display all forum pages
		
		if (!isset($_GET["entryID"])) {
			echo "Keine ID übergeben";
			exit;
		} else {
			$entryid=$_GET["entryID"];
		}
		
		

		if (priv(forum))
			$colspan = 2;
		else
			$colspan = 1;

		// Anzeige von gelöschten Einträgen
        if (priv("forum")) {
    	echo '<br>';
			echo '<table width="40%" align="center">';
				echo '<tr>';
	                if ($_GET["del"]=="1") {
						echo table_link('Gelöschte Einträge nicht anzeigen','index.php?del=0&site=forum&forumid='.$forumid.'&forummenue=yes&action=show_response&PHPSESSID='.session_id().'&entryID='.$entryid);
	                	$del=1;
    		        } else { 
						echo table_link('Gelöschte Einträge anzeigen','index.php?del=1&site=forum&forumid='.$forumid.'&forummenue=yes&action=show_response&PHPSESSID='.session_id().'&entryID='.$entryid);
            			$del=0;
            		} 
				echo '</tr>';
				echo '<tr>';
					echo table_link('Forum schliessen','index.php?del=1&site=forum&forumid='.$forumid.'&forummenue=yes&action=close&PHPSESSID='.session_id().'&entryID='.$entryid);
				echo '</tr>';
			echo '</table>';
        } else {
        	$del=0;
        }


		echo '<BR><BR><A HREF="#write">Weitere Antwort schreiben</A>';
		echo '<BR><BR><A HREF="index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=start&PHPSESSID='.session_id().'">Zurück zur Übersicht</A>';
		echo '<BR>';
	
        
        echo '<BR>';
                
		if ($del=="1") 
			$sql = 'select * from sys_forum_content  where parent_id='.$entryid.' or id='.$entryid.' and forum_id='.$forumid.' order by datum desc' ;
		else
		$sql = 'select * from sys_forum_content  where (parent_id='.$entryid.' or id='.$entryid.') and del=0 and forum_id='.$forumid.' order by datum desc';
		$result = getResult($db,$sql);

		if (isset($result[0]))
			foreach($result as $entry)
			{
				if (strlen($entry["email"]) > 0)
					$name = '<A HREF="mailto:'.$entry["email"].'">'.$entry["autor"].'</A>';
				else
					$name = $entry["autor"];

				if (strlen($entry["inet"]) > 0)
					$name .= ' (<A HREF="'.$entry["inet"].'" TARGET="new window">Homepage</A>)';

				if (strlen($entry["ip"]) > 0 && priv("forum"))
					$name .= ' (IP : '.$entry["ip"].') ';

				echo '<TABLE WIDTH="80%" BORDER="0">';
					echo '<TR BGCOLOR="#DDDDDD">';
						if ($entry["user_id"]==0) {
							echo '<TD COLSPAN="'.$colspan.'" ALIGN="LEFT" BGCOLOR="#DDDDDD">';
								echo 'Nachricht von '.$name.' (am '.date("d.m.Y \u\m H:i", strtotime($entry["datum"])).' Uhr) :';
								
						} else {
							echo '<TD COLSPAN="'.$colspan.'" ALIGN="LEFT" BGCOLOR='.$adm_link.'>';
								echo 'Nachricht von '.$name.' (am '.date("d.m.Y \u\m H:i", strtotime($entry["datum"])).' Uhr - von angemeldetem Benutzer):';
						}
						echo '</TD>';

					echo '</TR>';
					echo '<TR>';
					
						if (priv("forum") && $del=="1" && $entry["del"]=="1") 
							echo '<TD CLASS="eins" bgcolor="#FFFF80">';
						else 
							echo '<TD CLASS="eins">';
							echo ereg_replace("\n", '<br>', $entry["content"]);
						echo '</TD>';


						if ($official=="1") {
							if ($_SESSION["userid"]==$entry["user_id"]) {
							 	echo '<td align="center" style="width: 20px;">';
							 		if ($entry["del"]!="1") {
							 			echo '<a href="index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=edit&id='.$entry["id"].'&PHPSESSID='.session_id().'">';
							 				echo '<IMG SRC="images/edit.jpg" ALT="Bearbeiten" BORDER="0" HEIGHT="16" WIDTH="16">';
							 			echo '</a>';
							 		} else 
							 			echo '-';
								echo '</td>';
							}
						}



						if (priv("forum"))
						{
							echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF" style="width: 20px;">';
								if ($del=="1" && $entry["del"] == "1") 
									echo '-';
								else 
									echo '<INPUT TYPE="CHECKBOX" ID="chk'.$entry["id"].'" onClick="enableDelete(this.id,\'del'.$entry["id"].'\',\'emp'.$entry["id"].'\')" />';
									
								echo '<DIV ID="emp'.$entry["id"].'" STYLE="display:block;">';
									echo '<IMG SRC="images/empty.gif" BORDER="0" HEIGHT="16" WIDTH="16">';
								echo '</DIV>';
								echo '<DIV ID="del'.$entry["id"].'" STYLE="display:none;">';
									echo '<A HREF="index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=del&id='.$entry["id"].'&PHPSESSID='.session_id().'&entryID='.$entryid.'">';
									echo '<IMG SRC="images/del.gif" BORDER="0" ALT="Löschen" HEIGHT="16" WIDTH="16">';
									echo '</A>';
								echo '</DIV>';
							echo '</TD>';
						}
					echo '</TR>';
				echo '</TABLE><BR>';
			}

		echo '<BR><A NAME="write"></A>';
		
		if ($official=="1") {
			$url="index.php?site=forum&action=saveack&forumid=".$forumid."&PHPSESSID=".session_id()."&entryID=".$entryid;
		} else {
			$url="index.php?site=forum&action=save&forumid=".$forumid."&PHPSESSID=".session_id()."&entryID=".$entryid;
		}
		
		echo '<FORM NAME="FORUM" METHOD="POST" ACTION="'.$url.'">';
			if ($official=="1") {
				$_SESSION["secimage"]=simpleRandString(5);
				echo '<INPUT TYPE="hidden" SIZE="10" NAME="sec" VALUE="'.$_SESSION["secimage"].'" />';
			}	
		
		if ($official=="1") {
			$sqlstr="select name,email from sys_users where userid=".$_SESSION["userid"];
			$result=getResult($db,$sqlstr);
		}


		echo '<TABLE WIDTH="80%" BORDER="0">';
			echo '<TR BGCOLOR="#DDDDDD">';
				echo '<TD COLSPAN="2" ALIGN="LEFT" BGCOLOR="#DDDDDD">';
					echo '<B>Hier können Sie eine Antwort in unserem Forum hinterlassen.</B>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD CLASS="eins">';
					echo 'Ihr Name *';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" $BGCOLOR="'.$READONLY_COLOR.'" CLASS="none">';
					if ($official=="1") {
						echo '<INPUT TYPE="text" SIZE="80" NAME="name" VALUE="'.$result["0"]["name"].'" READONLY />';
						//echo '<INPUT TYPE="hidden" SIZE="80" NAME="name" VALUE="'.$result["0"]["name"].'" />';
					} else {
						echo '<INPUT TYPE="TEXT" SIZE="80" NAME="name" VALUE="" />';
					}
					
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD>';
					echo 'Ihre Emailadresse';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" CLASS="none">';
					if ($official=="1") {
						echo '<INPUT TYPE="text" SIZE="80" NAME="email" VALUE="'.$result["0"]["email"].'" READONLY />';
						//echo '<INPUT TYPE="hidden" SIZE="80" NAME="email" VALUE="'.$result["0"]["email"].'" />';
					} else {
						echo '<INPUT TYPE="TEXT" SIZE="80" NAME="email" VALUE="" />';
					}
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD>';
					echo 'Ihre Homepage';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" CLASS="none">';
					echo '<INPUT TYPE="TEXT" SIZE="80" NAME="hp" VALUE="http://www." />';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD>';
					echo 'Ihre Nachricht *<br><br>(HTML möglich)';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" CLASS="none">';
					echo '<TEXTAREA ROWS="7" COLS="60" NAME="content">';
					echo '</TEXTAREA>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="CENTER" COLSPAN="2" BGCOLOR="#DDDDDD">';
					echo '<INPUT TYPE="hidden" name="official" value="'.$official.'"/>';
					echo '<INPUT TYPE="SUBMIT" VALUE="Nachricht speichern" />';
				echo '</TD>';
			echo '<TR>';
				echo '<TD ALIGN="CENTER" COLSPAN="2" BGCOLOR="#FFFFFF">';
					echo '- Mit * gekennzeichnete Felder sind Pflichtfelder !';
					echo '<br>- Keine diskriminierenden, rassenfeindlichen oder perversen Äußerungen !';
					echo '<br>- Administratoren werden Beiträge löschen, die gegen gewisse Regeln verstoßen.';
				echo '</TD>';
			echo '</TR>';
		echo '</TABLE>';
		echo '</FORM>';
		break;
		
	case 'save':
        if (isset($_GET["entryID"])) {
        	$entryid_url="&entryID=".$_GET["entryID"];
        } else {
        	$entryid_url="";
        }
	    $url="index.php?site=forum&action=saveack&forumid=".$forumid."&PHPSESSID=".session_id().$entryid_url;


 
	    // Zwischenspeichern der POST-Informationen in der Session
	    $_SESSION["postforumdata"]=$_POST;

	    echo '<center><iframe src="secimage.php?&PHPSESSID='.session_id().'"  height="170" name="sec">';
		  echo '<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen:';
		echo '</iframe></center>';

		echo '<FORM NAME="FORUM" METHOD="POST" ACTION="'.$url.'">';
		echo "<b>Bitte geben Sie  die oben im Feld stehenden Buchstaben und Zahlen ein.</b> ";
		echo "<br><b>Falls Sie sich in Gross- und Kleinschreibung der Buchstaben nicht sicher sind,</b> ";
		echo "<br><b>laden Sie einfach eine neue Zeichenfolge.";
		
	    echo '<br><INPUT TYPE="TEXT" SIZE="10" NAME="sec" VALUE="" />';
	    
	    echo '<br><br><INPUT TYPE="SUBMIT" VALUE="Forumeintrag speichern" />';

	break;;
	case 'saveack':    
	    if ($_POST["sec"]!=$_SESSION["secimage"]) {
  			echo '<h2>Falsche Eingabe - Wiederhohlung der Eingabe</h2>';
		    echo '<center><iframe src="secimage.php"  height="170" name="sec">';
			  echo '<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen:';
			echo '</iframe></center>';
	        if (isset($_GET["entryID"])) {
	        	$entryid_url="&entryID=".$_GET["entryID"];
	        } else {
	        	$entryid_url="";
	        }
		    $url="index.php?site=forum&action=saveack&forumid=".$forumid."&PHPSESSID=".session_id().$entryid_url;
			echo '<FORM NAME="FORUM" METHOD="POST" ACTION="'.$url.'">';
			echo "<b>Bitte gib die oben im Feld stehenden Buchstaben und Zahlen ein:</b> ";
		    echo '<br><INPUT TYPE="TEXT" SIZE="10" NAME="sec" VALUE="" />';
		    echo '<br><br><INPUT TYPE="SUBMIT" VALUE="Forumeintrag speichern" />';
		    
	    } else {

	    	if ($_POST["official"]!="1") 
	    		$_POST=$_SESSION["postforumdata"];

			$name = strip_tags($_POST["name"]);
			$email = $_POST["email"];
			$official = $_POST["official"];

			if (!isset($_GET["entryID"])) {
				$entryid=0;
			} else {
				$entryid = $_GET["entryID"];
			}
			
			if ($official == "1" && isset($_SESSION["userid"]) && priv("fo_official_message")) {
				mlog("Ein angemeldeter Benutzer hat einen Forumeintrag gespeichert");
				$userid=$_SESSION["userid"];
			} else {
				$userid="0";
			}
			
			if (strlen($email) == 0)
				$email = 'NULL';
			$inet = $_POST["hp"];
			if (strlen($inet) == 0 ||$inet == 'http://www.')
				$inet = 'NULL';
			$msg = $_POST["content"];
	
			if (strlen($name) > 0 && strlen($msg) > 0)
			{
				//$content = addslashes($msg);
				$content = $msg;
	
	
				$sql = 'insert into sys_forum_content  (datum,toc_ts, autor, email, inet, content, ip,user_id,del,forum_id,parent_id) values (';
				$sql .= 'now(),now(), "'.$name.'", "'.$email.'", "'.$inet.'", "'.$content.'", "'.$_SERVER[REMOTE_ADDR].'",'.$userid.',0,'.$forumid.','.$entryid.')';
				$sql = ereg_replace('"NULL"', 'NULL', $sql);
				$result = doSQL($db, $sql);
				
				if ($entryid<>0) {
					$sql = "update	sys_forum_content set toc_ts=sysdate() where id=".$entryid;
					$result1=doSQL($db, $sql);
				}
				
				
				if ($result["code"] == 0)
				{
					if ($entryid==0) {
						echo '<A HREF="index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=start&PHPSESSID='.session_id().'">Nachricht erfolgreich gespeichert !</A>';
						echo '<SCRIPT TYPE="text/javascript">';
							echo 'setTimeout("window.location=\'index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=start&PHPSESSID='.session_id().'\'",1000);';
						echo '</SCRIPT>';
					} else {
						echo '<A HREF="index.php?site=forum&forumid='.$forumid.'&forummenue=yes&entryID='.$entryid.'&action=show_response&PHPSESSID='.session_id().'">Nachricht erfolgreich gespeichert !</A>';
						echo '<SCRIPT TYPE="text/javascript">';
							echo 'setTimeout("window.location=\'index.php?site=forum&forumid='.$forumid.'&forummenue=yes&entryID='.$entryid.'&action=show_response&PHPSESSID='.session_id().'\'",1000);';
						echo '</SCRIPT>';
					}
				}
				else
				{
					$steps=-1;
					if ($official!="1")  $steps=-2;
					echo '<A HREF="javascript:window.history.go('.$steps.')">Nachricht konnte nicht gespeichert werden !</A>';
					echo '<br><em>Vielleicht haben Sie in Ihrer Nachricht unzulässige Sonderzeichen wie \ oder " verwendet !?</em>';
				}
			}
			else
			{
				$steps=-1;
				if ($official!="1")  $steps=-2;

				echo 'Sie haben entweder keinen Namen oder keine Nachricht eingegeben !';
				echo '<br>Hinweis: Im Namen sind keine HTML-Tags wie < > usw. erlaubt !';
				echo '<br><A HREF="javascript:window.history.go('.$steps.')">zurück</A>';
			}
		}
		break;
	case 'del':
		if (priv("forum"))
		{

			$sql = 'update sys_forum_content  set del=1 where id = '.$_REQUEST["id"].' or parent_id='.$_REQUEST["id"];
			$result = doSQL($db,$sql);
			if ($result["code"] == 0)
			{
				// prüfen, ob es ein Nebeneintrag(Antwort) war
				// wenn ja muss toc_ts des Haupteintrages zurückgesetzt werden (==>Sortierung)
				if (isset($_GET["entryID"])) {
					$entryid=$_GET["entryID"];
					$sql = "select max(datum) maxdatum from sys_forum_content where del=0 and (id=".$entryid." or parent_id=".$entryid.")";
					$result=getResult($db,$sql);
					$maxdatum=$result["0" ]["maxdatum"];
					$sql = "update sys_forum_content set toc_ts='".$maxdatum."' where id = ".$entryid;
					$result=doSQL($db,$sql);
				}
				mlog("Forumeintrag wurde gelöscht: ".$_REQUEST["id"]);
				echo '<A HREF="index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=start&PHPSESSID='.session_id().'">Nachricht erfolgreich gelöscht !</A>';
				echo '<SCRIPT TYPE="text/javascript">';
					if (isset($entryid) && $entryid!=$_REQUEST["id"]) {
						echo 'setTimeout("window.location=\'index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=show_response&entryID='.$entryid.'&PHPSESSID='.session_id().'\'",1000);';	
					} else {
						echo 'setTimeout("window.location=\'index.php?site=forum&forumid='.$forumid.'&forummenue=yes&action=start&PHPSESSID='.session_id().'\'",1000);';
					}

				echo '</SCRIPT>';
			}
			else
				echo '<A HREF="javascript:window.history.back()">Nachricht konnte nicht gelöscht werden !</A>';
		}
	break;
}
?>
