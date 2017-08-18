<?php
// Gästebuch
// Daniel, 05.03.2004
// Volker, 30.01.2005 Unterscheidung nach angemeldetem Benutzer
// Volker, 12.02.2005 Einträge werden nicht mehr gelöscht, sondern nur noch als gelöscht markiert
//                    Benutzer mit dem Recht guestbook können sich gelöschte Einträge ansehen lassen
// Volker, 13.03.2006 Schließen und Freigeben des Gästebuches mit dem Recht "guestbook"
//                    


sitename("guestbook.php",$_SESSION["groupid"]);



$sqlstr="select value_i from sys_values where name='guestbook_closed'";
$result=GetResult($db,$sqlstr);
if ($result["0"]["value_i"]=="1") {
	$sqlstr = "select value_d,value_s from sys_values where name='guestbook_closed'";
	$result=getResult($db,$sqlstr);
	echo "<h2>Gästebuch wurde am ".date("d.m.Y \u\m H:i", strtotime($result["0"]["value_d"]))." geschlossen</h2>";
	echo '<br>';
	if ($result["0"]["value_d"] != "") {
		echo '<br>';
		echo $result["0"]["value_s"];
	}
	echo '<br>';
	if (priv("guestbook")) {
		echo "<br><br><br>";
  		echo '<input type="Button" name="" value="Gästebuch wieder freigeben" onclick="location.href=\'index.php?site=guestbook&action=guestbook_open&PHPSESSID='.session_id().'\'">';
  		echo "<br><br><br>";
		echo '<hr noshade size="1">';
	} else {
   		exit;
   	}
}



switch($action)
{
	case 'guestbook_open':
		if (priv("guestbook")) {
			echo "Gästebuchg wieder freigeben ... ";
			$sqlstr = "update sys_values set value_i=0 where name='guestbook_closed'";
			$result=doSQL($db,$sqlstr);
  			echo '<br><br>';
  			if ($result["code"] == 0)
			{
				mlog("Gästebuch wurde freigegeben.");
				echo '<A HREF="index.php?site=guestbook&action=start&PHPSESSID='.session_id().'">Gästebuch erfolgreich freigegeben !</A>';
				echo '<SCRIPT TYPE="text/javascript">';
					echo 'setTimeout("window.location=\'index.php?site=guestbook&action=start&PHPSESSID='.session_id().'\'",1000);';
				echo '</SCRIPT>';
			}
			else
				echo '<A HREF="javascript:window.history.back()">Es ist ein Fehler aufgetreten. Das Gästebuch konnte nicht freigegeben werden !</A>';
		} else {
			echo "Fehlende Berechtigung";
		}
	break;

	case 'edit':
		$sqlstr  = "select * from sys_guestbook where ";
		$sqlstr .= "id=".$_GET["id"]." and ";
		$sqlstr .= "user_id=".$_SESSION["userid"];
		$result=getResult($db,$sqlstr);
		if (isset($result["0"])) {
			echo '<FORM NAME="GBOOK" METHOD="POST" ACTION="index.php?site=guestbook&action=save_edit&id='.$result["0"]["id"].'&PHPSESSID='.session_id().'">';
				echo '<TABLE WIDTH="90%">';
					echo '<TR BGCOLOR="#DDDDDD">';
						echo '<TD COLSPAN="1" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
							echo '<B>Gästebucheintrag ändern</B>';
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
							echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="self.location.href=\'index.php?site=guestbook&action=start&PHPSESSID='.session_id().'\'">';
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
		$sqlstr  = "select count(*) anz from sys_guestbook ";
		$sqlstr .= " where id=".$id." and user_id=".$_SESSION["userid"];
		$result=getResult($db,$sqlstr);
		if ($result["0"]["anz"]=="1") {
			$sqlstr  = "update sys_guestbook ";
			$sqlstr .= "set content='".$content."'";
			$sqlstr .= " where id=".$id." and user_id=".$_SESSION["userid"];
			$result=doSQL($db,$sqlstr);
			if ($result["code"]=="0") {
				$link='index.php?site=guestbook&action=start&PHPSESSID='.session_id();
				echo '<a href="'.$link.'"><h3>Änderung wurden erfolgreich gespeichert</h3></a>';
			    echo '<SCRIPT TYPE="text/javascript">';
    	            echo 'setTimeout("self.location.href=\''.$link.'\'",1000);';
	        	echo '</SCRIPT>';
	        	mlog("Gästebucheintrag wurde nachträglich bearbeitet: ".$_REQUEST["id"]);
			} else {
				echo 'Fehler Beim Speichern der Änderungen';
			}
		} else {
			echo 'Fehler Beim Speichern der Änderungen';
		}
	break;

	case 'close':
        if (priv("guestbook")) {
			echo '<h2><u>Schließen des Gästebuches</u></h2>';
			echo '<FORM NAME="CLOSE_ACKNOLEDGE" METHOD="POST" ACTION="index.php?site=guestbook&action=close_ack&id='.$result["0"]["id"].'&PHPSESSID='.session_id().'">';
								echo '<TABLE WIDTH="90%">';
					echo '<TR BGCOLOR="#DDDDDD">';
						echo '<TD COLSPAN="1" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
							echo '<h2>Begründung</H2><b>Dieser Text wird anstelle des Gästebuchs angezeigt (max 250 Zeichen)!</b>';
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
							echo '<INPUT TYPE="submit" ACTION="submit()" VALUE="Gästebuch schliessen">';
							echo '&nbsp;&nbsp;&nbsp;';
							echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="self.location.href=\'index.php?site=guestbook&action=start&PHPSESSID='.session_id().'\'">';
						echo '</TD>';
					echo '</TR>';
				echo '</TABLE>';
			echo '<FORM>';
		} else {
			echo "Fehlende Berechtigung";
		}
	break;

	case 'close_ack':
        if (priv("guestbook")) {
		   echo '<br>Gästebuch wird geschlossen... <br><br>';
		   $sqlstr = "delete from sys_values where name='guestbook_closed'";
		   $result=doSQL($db,$sqlstr);
		   
		   $sqlstr  = "insert into sys_values (name,value_i,value_s,value_d) values (";
		   $sqlstr .= "'guestbook_closed',";
		   $sqlstr .= "1,";
		   $sqlstr .= "'".$_POST["content"]."',";
		   $sqlstr .= "sysdate())";
		   $result=doSQL($db,$sqlstr);
  		
  			if ($result["code"] == 0)
			{
				mlog("Gästebuch wurde geschlossen. ".$_POST["content"]);
				echo '<A HREF="index.php?site=guestbook&action=start&PHPSESSID='.session_id().'">Gästebuch erfolgreich geschlossen !</A>';
				echo '<SCRIPT TYPE="text/javascript">';
					echo 'setTimeout("window.location=\'index.php?site=guestbook&action=start&PHPSESSID='.session_id().'\'",1000);';
				echo '</SCRIPT>';
			}
			else
				echo '<A HREF="javascript:window.history.back()">Es ist ein Fehler aufgetreten. Das Gästebuch konnte nicht geschlossen werden !</A>';
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

		if (priv(guestbook))
			$colspan = 2;
		else
			$colspan = 1;

		// Anzeige von gelöschten Einträgen
        if (priv("guestbook")) {
    	echo '<br>';
			echo '<table width="40%" align="center">';
				echo '<tr>';
	                if ($_GET["del"]=="1") {
						echo table_link('Gelöschte Einträge nicht anzeigen','index.php?del=0&site=guestbook&action=start&PHPSESSID='.session_id());
	                	$del=1;
    		        } else { 
						echo table_link('Gelöschte Einträge anzeigen','index.php?del=1&site=guestbook&action=start&PHPSESSID='.session_id());
            			$del=0;
            		} 
				echo '</tr>';
				echo '<tr>';
					echo table_link('Gästebuch schliessen','index.php?del=1&site=guestbook&action=close&PHPSESSID='.session_id());
				echo '</tr>';
			echo '</table>';
        } else {
        	$del=0;
        }

		if ($del=="1") 
			$sql = 'select count(*) from sys_guestbook';
		else
			$sql = 'select count(*) from sys_guestbook where del=0';
		
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
					echo ' <A HREF="index.php?site=guestbook&action=start'.$more.'&del='.$del.'&page='.$x.'&PHPSESSID='.session_id().'">';
					echo $x;
					echo '</A>';
				}
				if (($x == $indx) && !isset($more) ) {
					echo '&nbsp;. . .&nbsp;<A HREF="index.php?site=guestbook&action=start&more=1&del='.$del.'&PHPSESSID='.session_id().'"> weitere Seiten </A>';
					break;
				}
			}
		}

		echo '<BR><BR><A HREF="#write">Nachricht schreiben</A><BR>';
                
                echo '<BR>';


		if (isset($_SESSION["userid"]) && priv("gb_official_message")) {
			$official="1";
			$READONLY_COLOR="#FFFFFF";
			$colspan++;
		} else {
			unset($result);
			$official="0";
			$READONLY_COLOR="#FFFFFF";
		}
	
                
		if ($del=="1") 
			$sql = 'select * from sys_guestbook order by datum desc limit '.$limit.', 15';
		else
			$sql = 'select * from sys_guestbook where del=0 order by datum desc limit '.$limit.', 15';


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

				if (strlen($entry["ip"]) > 0 && priv("guestbook"))
					$name .= ' (IP : '.$entry["ip"].') ';

				echo '<TABLE WIDTH="80%" BORDER="0">';
					echo '<TR BGCOLOR="#DDDDDD">';
						if ($entry["user_id"]==0) {
							echo '<TD COLSPAN="'.$colspan.'" ALIGN="LEFT" BGCOLOR="#DDDDDD">';
								echo 'Nachricht von '.$name.' (am '.date("d.m.Y \u\m H:i", strtotime($entry["datum"])).' Uhr) :';
							echo '</TD>';
						} else {
							echo '<TD COLSPAN="'.$colspan.'" ALIGN="LEFT" BGCOLOR='.$adm_link.'>';
								echo 'Nachricht von '.$name.' (am '.date("d.m.Y \u\m H:i", strtotime($entry["datum"])).' Uhr - von angemeldetem Benutzer):';
							echo '</TD>';
						}
					echo '</TR>';
					echo '<TR>';
					
						if (priv("guestbook") && $del=="1" && $entry["del"]=="1") 
							echo '<TD CLASS="eins" bgcolor="#FFFF80">';
						else 
							echo '<TD CLASS="eins">';
						
							//echo ereg_replace("\n", '<br>', $entry["content"]);
							if ($entry["user_id"]>0)
								echo ereg_replace("\n", '<br>', $entry["content"]);
							else
								echo ereg_replace("\n", '<br>', htmlentities($entry["content"]));
							
						echo '</TD>';
						

						if ($official=="1") {
							if ($_SESSION["userid"]==$entry["user_id"]) {
							 	echo '<td align="center" style="width: 20px;">';
							 		if ($entry["del"]!="1") {
							 			echo '<a href="index.php?site=guestbook&action=edit&id='.$entry["id"].'&PHPSESSID='.session_id().'">';
							 				echo '<IMG SRC="images/edit.jpg" ALT="Bearbeiten" BORDER="0" HEIGHT="16" WIDTH="16">';
							 			echo '</a>';
							 		} else 
							 			echo '-';
								echo '</td>';
							}
						}



						if (priv("guestbook"))
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
									echo '<A HREF="index.php?site=guestbook&action=del&id='.$entry["id"].'&PHPSESSID='.session_id().'">';
									echo '<IMG SRC="images/del.gif" BORDER="0" ALT="Löschen" HEIGHT="16" WIDTH="16">';
									echo '</A>';
								echo '</DIV>';
							echo '</TD>';
						}
					echo '</TR>';
				echo '</TABLE><BR>';
			}

		if ($official=="1") {
			echo '<BR><A NAME="write"></A><FORM NAME="GBOOK" METHOD="POST" ACTION="index.php?site=guestbook&action=saveack&PHPSESSID='.session_id().'">';			
		} else {
			echo '<BR><A NAME="write"></A><FORM NAME="GBOOK" METHOD="POST" ACTION="index.php?site=guestbook&action=save&PHPSESSID='.session_id().'">';
		}
		
		
		if ($official=="1") {
			$sqlstr="select name,email from sys_users where userid=".$_SESSION["userid"];
			$result=getResult($db,$sqlstr);
		}


		echo '<TABLE WIDTH="80%" BORDER="0">';
			echo '<TR BGCOLOR="#DDDDDD">';
				echo '<TD COLSPAN="2" ALIGN="LEFT" BGCOLOR="#DDDDDD">';
					echo '<B>Hier können Sie eine Nachricht in unserem Gästebuch hinterlassen.</B>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD CLASS="eins">';
					echo 'Ihr Name *';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" $BGCOLOR="'.$READONLY_COLOR.'" CLASS="none">';
					if ($official=="1") {
						echo '<INPUT TYPE="hidden" SIZE="10" NAME="sec" VALUE="'.$_SESSION["secimage"].'" />';
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
					if ($official=="1") { 
						echo '<INPUT TYPE="text" SIZE="80" NAME="name" VALUE="'.$result["0"]["name"].'" READONLY />';
						$_SESSION["secimage"]=simpleRandString(5);
						echo '<INPUT TYPE="hidden" SIZE="10" NAME="sec" VALUE="'.$_SESSION["secimage"].'" />';
					}

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
	    $url="index.php?site=guestbook&action=saveack&PHPSESSID=".session_id();

	    // Zwischenspeichern der POST-Informationen in der Session
	    $_SESSION["postguestbookdata"]=$_POST;

	    echo '<center><iframe src="secimage.php?&PHPSESSID='.session_id().'"  height="170" name="sec">';
		  echo '<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen:';
		echo '</iframe></center>';

		echo '<FORM NAME="GUESTBOOK" METHOD="POST" ACTION="'.$url.'">';
		echo "<b>Bitte geben Sie  die oben im Feld stehenden Buchstaben und Zahlen ein.</b> ";
		echo "<br><b>Falls Sie sich in Gross- und Kleinschreibung der Buchstaben nicht sicher sind,</b> ";
		echo "<br><b>laden Sie einfach eine neue Zeichenfolge.";
		
	    echo '<br><INPUT TYPE="TEXT" SIZE="10" NAME="sec" VALUE="" />';
	    
	    echo '<br><br><INPUT TYPE="SUBMIT" VALUE="Gästebucheintrag speichern" />';

	break;;



		
	case 'saveack':
	    if ($_POST["sec"]!=$_SESSION["secimage"]) {
	    	echo '<h2>Falsche Eingabe - Wiederhohlung der Eingabe</h2>';
		    echo '<center><iframe src="secimage.php"  height="170" name="sec">';
			  echo '<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen:';
			echo '</iframe></center>';
		    $url="index.php?site=guestbook&action=saveack&PHPSESSID=".session_id();
			echo '<FORM NAME="GUESTBOOK" METHOD="POST" ACTION="'.$url.'">';
			echo "<b>Bitte gib die oben im Feld stehenden Buchstaben und Zahlen ein:</b> ";
		    echo '<br><INPUT TYPE="TEXT" SIZE="10" NAME="sec" VALUE="" />';
		    echo '<br><br><INPUT TYPE="SUBMIT" VALUE="Gästebucheintrag speichern" />';
		    
	    } else {
		    if ($_POST["official"]!="1") 
	    	    $_POST=$_SESSION["postguestbookdata"];
	    	    
			$name = strip_tags($_POST["name"]);
			$email = $_POST["email"];
			$official = $_POST["official"];
			
			if ($official == "1" && isset($_SESSION["userid"]) && priv("gb_official_message")) {
				mlog("Ein angemeldeter Benutzer hat einen Gästebucheintrag gespeichert");
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
				$sql = 'insert into sys_guestbook (datum, autor, email, inet, content, ip,user_id,del) values (';
				$sql .= 'now(), "'.$name.'", "'.$email.'", "'.$inet.'", "'.$content.'", "'.$_SERVER[REMOTE_ADDR].'",'.$userid.',0)';
				$sql = ereg_replace('"NULL"', 'NULL', $sql);
				$result = doSQL($db, $sql);
				
	
				
				
				if ($result["code"] == 0)
				{
					echo '<A HREF="index.php?site=guestbook&action=start&PHPSESSID='.session_id().'">Nachricht erfolgreich gespeichert !</A>';
					echo '<SCRIPT TYPE="text/javascript">';
						echo 'setTimeout("window.location=\'index.php?site=guestbook&action=start&PHPSESSID='.session_id().'\'",1000);';
					echo '</SCRIPT>';
				}
				else
				{
					$steps=-1;
					if ($official!="1")  $steps=-2;
					echo '<A HREF="javascript:window.history.go('.$steps.'))">Nachricht konnte nicht gespeichert werden !</A>';
					echo '<br><em>Vielleicht haben Sie in Ihrer Nachricht unzulässige Sonderzeichen wie \ oder " verwendet !?</em>';
				}
			}
			else
			{
				echo 'Sie haben entweder keinen Namen oder keine Nachricht eingegeben !';
				echo '<br>Hinweis: Im Namen sind keine HTML-Tags wie < > usw. erlaubt !';
				$steps=-1;
				if ($official!="1")  $steps=-2;
				
				echo '<br><A HREF="javascript:window.history.go('.$steps.')">zurück</A>';
			}
	    }
		break;
	case 'del':
		if (priv("guestbook"))
		{
			//$sql = 'delete from sys_guestbook where id = '.$_REQUEST["id"];
			$sql = 'update sys_guestbook set del=1 where id = '.$_REQUEST["id"];
			$result = doSQL($db,$sql);
			if ($result["code"] == 0)
			{
				mlog("Gästebucheintrag wurde gelöscht: ".$_REQUEST["id"]);
				echo '<A HREF="index.php?site=guestbook&action=start&PHPSESSID='.session_id().'">Nachricht erfolgreich gelöscht !</A>';
				echo '<SCRIPT TYPE="text/javascript">';
					echo 'setTimeout("window.location=\'index.php?site=guestbook&action=start&PHPSESSID='.session_id().'\'",1000);';
				echo '</SCRIPT>';
			}
			else
				echo '<A HREF="javascript:window.history.back()">Nachricht konnte nicht gelöscht werden !</A>';
		}
	break;
}
?>