<?php
sitename("sys_site.php",$_SESSION["groupid"]);
if (! isset($action))
   $action=$_REQUEST["action"];
   
switch($action) {
	
	case 'show':
		if (!isset($siteid))
			$siteid=$_REQUEST["siteid"];
		
		if (priv("sys_site"))
		{
                echo '<CENTER><TABLE WIDTH="40%" BORDER="0">';
                        echo '<TR>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<a href="index.php?site=site&action=edit&siteid='.$siteid.'&PHPSESSID='.session_id().'">';
                                        echo '<IMG SRC="images/edit.jpg" BORDER="0" ALT="neu">';
                                        echo '</a>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<a href="index.php?site=site&action=edit&siteid='.$siteid.'&PHPSESSID='.session_id().'">';
                                        echo '<B>Seite editieren</B>';
                                        echo '</a>';
                                echo '</TD>';
                        echo '</TR>';
                echo '</TABLE></CENTER><BR>';
		}
		$sqlstr = "select descr from sys_site where id = $siteid";
		$result=GetResult($db,$sqlstr);
		echo $result["0"]["descr"];

	break;

	case 'edit':
		$siteid=$_REQUEST["siteid"];
		if (priv("sys_site"))
		{
			$sql = "select name,descr,show_menu from sys_site where id =$siteid"; 
			$result= GetResult($db,$sql);
			echo '<FORM NAME="news" METHOD="POST" ACTION="index.php?site=site&action=save&PHPSESSID='.session_id().'">';
			echo '<TABLE WIDTH="90%" BORDER="0">';
				echo '<TR BGCOLOR="#DDDDDD">';
					echo '<TD COLSPAN="2" ALIGN="LEFT" BGCOLOR="#DDDDDD">';
						echo '<B>Hier können Sie die Seite für '.$result["0"]["name"].' ändern.</B>';
						echo '&nbsp;&nbsp;&nbsp;<b>Eintrag sichtbar:</b> ';
						if ($result["0"]["show_menu"] == "1") 
							echo '&nbsp;&nbsp;&nbsp;<input type="checkbox" name="visible" value="1" checked>';  
						else
							echo '&nbsp;&nbsp;&nbsp;<input type="checkbox" name="visible" value="1" unchecked>';  
					echo '</TD>';
				echo '</TR>';
				echo '<TR>';
				echo '<TD ALIGN="CENTER" CLASS="none">';
						echo '<TEXTAREA ROWS="20" COLS="100" NAME="descr">';
						echo $result[0]["descr"];
						echo '</TEXTAREA>';
					echo '</TD>';
				echo '</TR>';
				echo '<TR>';
					echo '<TD ALIGN="CENTER" COLSPAN="2" BGCOLOR="#DDDDDD">';
						echo '<INPUT TYPE="HIDDEN" NAME="siteid" VALUE="'.$siteid.'" />';
						echo '<INPUT TYPE="SUBMIT" VALUE="Speichern" />';
					echo '</TD>';
				echo '</TR>';
			echo '</TABLE>';
			echo '</FORM>';
		}
		else
			echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
	break;

	case 'save':
		if (priv("sys_site"))
		{
	
			$siteid=$_POST["siteid"];
			$state=$_POST["descr"];
			if (isset($_POST["visible"])) 
				$visible=1;
			else
				$visible=0;
				
				
			$sql = "update sys_site set descr ='".$state."',show_menu=".$visible." where id=".$siteid;
			
			$result = doSQL($db, $sql);
		
			if ($result["code"] == 0)
			{
				mlog("System-Verwaltung: Eine Systemseite wurde geändert (Impressum, Termine, Vorstand ... ".$siteid);
				echo '<A HREF="index.php?site=site&action=show&siteid='.$siteid.'&PHPSESSID='.session_id().'">Seite erfolgreich geändert!</A>';
				echo '<SCRIPT TYPE="text/javascript">';
					echo 'setTimeout("window.location=\'index.php?site=site&siteid='.$siteid.'&action=show&PHPSESSID'.session_id().'\'",1000);';
				echo '</SCRIPT>';
			}
			else
				echo '<A HREF="index.php?site=site&siteid='.$siteid.'&action=show&PHPSESSID'.session_id().'">Seite konnte nicht erfolgreich geändert werden!</A>';
		}
		else
			echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
	break;
}



?>