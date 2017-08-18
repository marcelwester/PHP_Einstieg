<?php

////
//// users.php
////
//// Änderung : Daniel, 16.03.2004 21:28
//// was : Rechte / Gruppen verwalten
// Volker 12.02.2005
// Disable-Flag eingefügt: Benutzer werde nicht mehr gelöscht nur noch inaktiv gesetzt.
sitename("users.php",$_SESSION["groupid"]);
if (priv("user"))
{
?>

<?php
$submenu = array( "users" => array("id" => 'user', "title" => 'Benutzer', "link" => 'index.php?site=users&action=user&PHPSESSID='.session_id()),
				"groups" => array("id" => 'groups', "title" => 'Gruppen', "link" => 'index.php?site=users&action=groups&PHPSESSID='.session_id()),
				"rights" => array("id" => 'rights', "title" => 'Rechte', "link" => 'index.php?site=users&action=rights&PHPSESSID='.session_id()),
				"logins" => array("id" => 'login', "title" => 'Fehlanmeldungen', "link" => 'index.php?site=users&action=login&PHPSESSID='.session_id())
				);

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
	echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF"><BR><BR>';

switch($action)
{
	case "user":
	if (priv("user"))
	{
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
	function popup_user_tus(userid)
	{
		var url;
		<?php
		echo 'url = "user_tus_popup.php?action=edit&userid="+userid+"&PHPSESSID='.session_id().'";';
		?>
		window.open(url,"info","width=600, height=350, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
	}

	function popup_pass(userid)
	{
		var url;
		<?php
		echo 'url = "user_tus_pass_popup.php?action=edit&userid="+userid+"&PHPSESSID='.session_id().'";';
		?>
		window.open(url,"info","width=600, height=350, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
	}

-->
</SCRIPT>

<?php
			echo '<CENTER><TABLE WIDTH="40%" BORDER="0">';
	                        echo '<TR>';
	                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                                        echo '<a href="javascript:popup_pass(0);">';
	                                        echo '<IMG SRC="images/new.jpg" BORDER="0" ALT="neu">';
	                                        echo '</a>';
	                                echo '</TD>';
	                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                                        echo '<a href="javascript:popup_pass(0);">';
	                                        echo '<B>Neuen Benutzer anlegen</B>';
	                                        echo '</a>';
	                                echo '</TD>';
	                        echo '</TR>';

	                        echo '<TR>';
					if (isset($_GET["disable"])) {
						if ($_GET["disable"] == "1") {
							$disable="1";
							$disable_url="0";
						} else {
							$disable = "0";
							$disable_url="1";
						}
					} else {
						$disable =  "0";
						$disable_url="1";
					}
					
			                $url = "index.php?site=users&action=user&disable=".$disable_url."&PHPSESSID=".session_id();

	                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                                        echo '<a href="'.$url.'">';
	                                        echo '<IMG SRC="images/del.gif" BORDER="0" ALT="neu">';
	                                        echo '</a>';
	                                echo '</TD>';
	                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	                                        echo '<a href="'.$url.'">';
	                                        if ($disable == "1") 
	                                        	echo '<B>Aktive Benutzer anzeigen</B>';
	                                        else
	                                        	echo '<B>Nicht aktive Benutzer anzeigen</B>';
	                                        echo '</a>';
	                                echo '</TD>';
	                        echo '</TR>';
                        echo '</TABLE></CENTER><BR>';

			if ($disable=="1") {
				echo '<h2><u>Inaktive Benutzer</u></h2>';
			}

?>


		<TABLE WIDTH="90%" BORDER="0">
			<TR BGCOLOR="#DDDDDD">
				<TD COLSPAN="10" ALIGN="LEFT" BGCOLOR="#AAAAAA">
					<B>Alle angelegten Benutzer :</B>
				</TD>
			</TR>
			<TR>
				<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">
					<B>ID</B>
				</TD>
				<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">
					<B>Benutzername</B>
				</TD>
				<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">
					<B>Profil</B>
				</TD>
				<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">
					<B>Name</B>
				</TD>
				<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">
					<B>email</B>
				</TD>
				<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">
					<B>last login</B>
				</TD>
				<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">
					<B>Gruppe</B>
				</TD>
				<TD COLSPAN="4" ALIGN="CENTER" BGCOLOR="#DDDDDD">
					<B>Aktion</B>
				</TD>
			</TR>
			<FORM ID="gruppe" NAME="gruppe">
<?php

		$sql1 = 'select userid,login,password,u.name,email,u.groupid,last_login,disable,g.name groupname' .
				' from sys_users u,sys_groups g' .
				' where ' .
				' disable='.$disable.' and '.
				' u.groupid=g.groupid '.
				' order by login';
		$result1 = GetResult($db, $sql1);
		foreach($result1 as $user)
		{
			echo '<TR>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo $user["userid"];
				echo '</TD>';

				if ($groupid==$admin_id) {
					table_link($user["login"],"index.php?site=users&action=chroot&chrootuserid=".$user["userid"]."&PHPSESSID=".session_id());
				} else {
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo $user["login"];
					echo '</TD>';
				}

				table_link("ändern","javascript:popup_pass(".$user["userid"].");");

				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<A HREF="mailto:'.$user["email"].'">'.$user["name"].'</a>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo $user["email"];
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo date("d.m.Y - H:i", strtotime($user["last_login"]));
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo $user["groupname"];
				echo '</TD>';
				echo '<TD ALIGN="CENTER">';
					if ($user["groupid"] != $admin_id)
					{
						echo '<a href="javascript:popup_user_tus('.$user["userid"].');">';
							echo '<IMG SRC="images/edit.jpg" BORDER="0" ALT="Berechtigungen">';
						echo '</a>';
					}
					else
						echo '<IMG SRC="images/empty.gif" BORDER="0" HEIGHT="16" WIDTH="16">';
				echo '</TD>';
                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                        echo '<INPUT TYPE="CHECKBOX" ID="chk'.$user["userid"].'" onClick="enableDelete(this.id,\'del'.$user["userid"].'\',\'emp'.$user["userid"].'\')" />';
                echo '</TD>';
                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                        echo '<DIV ID="emp'.$user["userid"].'" STYLE="display:block;">';
                                echo '<IMG SRC="images/empty.gif" BORDER="0" HEIGHT="16" WIDTH="16">';
                        echo '</DIV>';
                        echo '<DIV ID="del'.$user["userid"].'" STYLE="display:none;">';
                                echo '<A HREF="index.php?site=users&action=deluser&disable='.$disable.'&userid='.$user["userid"].'&PHPSESSID='.session_id().'">';
                                echo '<IMG SRC="images/del.gif" BORDER="0" HEIGHT="16" WIDTH="16">';
                                echo '</A>';
                        echo '</DIV>';
                echo '</TD>';
		

			echo '</TR>';
			$x++;
		}

		// REQUEST nur anzeigen, wenn disable=0 
		if ($disable == "0") {
		?>
					</FORM>
				</TABLE>
				<BR><BR>
				<TABLE WIDTH="90%" BORDER="0">
					<TR BGCOLOR="#DDDDDD">
						<TD COLSPAN="7" ALIGN="LEFT" BGCOLOR="#AAAAAA">
							<B>Alle vorliegenden Anträge :</B>
						</TD>
					</TR>
		<?php
				$sql3 = 'select requestid,login,email,name,reason from sys_requests';
				$result3 = GetResult($db, $sql3);
				if (isset($result3[0]))
				{
					echo'<TR>';
						echo'<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
							echo'<B>Name</B>';
						echo'</TD>';
						echo'<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
							echo'<B>email</B>';
						echo'</TD>';
						echo'<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
							echo'<B>login</B>';
						echo'</TD>';
						echo'<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
							echo'<B>Begründung</B>';
						echo'</TD>';
						echo'<TD COLSPAN="3" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
							echo'<B>Aktion</B>';
						echo'</TD>';
					echo'</TR>';
		
					foreach ($result3 as $request)
					{
						echo'<TR>';
							echo'<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
								echo $request["name"];
							echo'</TD>';
							echo'<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
								echo $request["email"];
							echo'</TD>';
							echo'<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
								echo $request["login"];
							echo'</TD>';
							echo'<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
								echo $request["reason"];
							echo'</TD>';
							echo '<TD ALIGN="CENTER" BGCOLOR="'.$adm_link.'" onMouseOver="this.style.backgroundColor=\''.$adm_link.'_over\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$adm_link.'\';" onClick="location.href=\'index.php?site=users&action=authrequest&requestid='.$request["requestid"].'&PHPSESSID='.session_id().'\'">';
								echo'genehmigen';
							echo'</TD>';
			                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
			                        echo '<INPUT TYPE="CHECKBOX" ID="chkrq'.$request["requestid"].'" onClick="enableDelete(this.id,\'delrq'.$request["requestid"].'\',\'emprq'.$request["requestid"].'\')" />';
			                echo '</TD>';
			                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
			                        echo '<DIV ID="emprq'.$request["requestid"].'" STYLE="display:block;">';
			                                echo '<IMG SRC="images/empty.gif" BORDER="0" HEIGHT="16" WIDTH="16">';
			                        echo '</DIV>';
			                        echo '<DIV ID="delrq'.$request["requestid"].'" STYLE="display:none;">';
			                                echo '<A HREF="index.php?site=users&action=delrequest&requestid='.$request["requestid"].'&PHPSESSID='.session_id().'">';
			                                echo '<IMG SRC="images/del.gif" BORDER="0" HEIGHT="16" WIDTH="16">';
			                                echo '</A>';
			                        echo '</DIV>';
			                echo '</TD>';				echo'</TR>';
					}
				}
				else
				{
					echo'<TR>';
						echo'<TD COLSPAN="4" ALIGN="CENTER" BGCOLOR="#FFFFFF">';
							echo 'Es liegen keine Anträge vor.';
						echo'</TD>';
					echo'</TR>';
				}
		?>
				</TABLE>
		<?php
		// ENDE IF DISABLE
		}

	}
	else
		echo $no_rights;
	break;

// ++++ chroot start ++++
    case "chroot":
	    if ($groupid==$admin_id) {
		  $chrootuserid=$_GET["chrootuserid"];
		  $sqlstr = "select name,login,userid from sys_users where userid = ".$chrootuserid;
		  $result=getResult($db,$sqlstr);
		  if (isset($result["0"]["name"])) {
		    echo "<h2>Sie wollen wirklich in das Profil von <u>".$result["0"]["name"].
		         "</u> wechseln ? </h2>";
		    echo '<table align="center" witdth="20%"';
		    	echo '<tr>';
		    		table_link("<h2>&nbsp;&nbsp;ja&nbsp;&nbsp;</h2>","index.php?site=users&action=dochroot&chrootuserid=".$chrootuserid."&PHPSESSID=".session_id());
		    		table_link("<h2>&nbsp;nein&nbsp;</h2>","index.php?site=users&action=user&PHPSESSID=".session_id());
		    	echo '</tr>';	
		    echo '</table>';
		  } else {
		  	echo "Fehler kein Eintrag für userid: ".$chrootuserid. " gefunden!";
		  }
		  
	    } else {
	      echo $no_rights;
	    }
    break;

    case "dochroot":
	    if ($groupid==$admin_id) {
		  
		  $chrootuserid=$_GET["chrootuserid"];
		  $sqlstr = "select name,login,userid,groupid from sys_users where userid = ".$chrootuserid;
		  $result=getResult($db,$sqlstr);
		  if (isset($result["0"]["name"])) {
			// Setzen der Userid
		    $_SESSION["userid"]=$result["0"]["userid"];
		    $_SESSION["groupid"]=$result["0"]["groupid"];
		    $_SESSION["login"]=$result["0"]["login"];
		    
		    // Zurücksetzen der Berechtigungen, damit Sie neu ausgelesen werden können
		   	unset($_SESSION["userprivs"]);
			unset($_SESSION["mannschaftenid"]);
			unset($_SESSION["sportarten"]);
			unset($_SESSION["tt_mannschaftenid"]);
		
		    echo "<h2>Wechsel in das Profil von <u>".$result["0"]["name"].
		         "</u> durchgeführt </h2>";
		    echo '<table align="center" witdth="20%"';
		    	echo '<tr>';
		    		table_link("<h2>&nbsp;&nbsp;<b><u>Startseite laden</u></b>&nbsp;&nbsp;</h2>","index.php?PHPSESSID=".session_id());
		    	echo '</tr>';	
		    echo '</table>';
		    mlog("chroot in das Profil von ".$result["0"]["name"]."(".$result["0"]["userid"].") durchgeführt");
		  } else {
		  	echo "Fehler kein Eintrag für userid: ".$chrootuserid. " gefunden!";
		  }
		  
	    } else {
	      echo $no_rights;
	    }
    break;
    // ++++ chroot end ++++

	case "deluser":
	if (priv("user"))
	{
		$ausgabe = 'Der Benutzer konnte nicht gelöscht werden.';

		$sql = 'select count(*) from sys_users where groupid = '.$admin_id;
		$numadmins = GetResult($db, $sql);
		$sql = 'select * from sys_users where userid = '.$_REQUEST["userid"];
		$userdetails = GetResult($db, $sql);

		if ($numadmins[0]["count(*)"] == 1 && $userdetails[0]["groupid"] == $admin_id)
			$ausgabe = 'Der Benutzer konnte nicht gelöscht werden, da mindestens ein Adminitrator eingetragen sein muss.';
		elseif ($_SESSION["userid"] == $_REQUEST["userid"])
			$ausgabe = 'Sie können sich nicht selber löschen. Bitte wenden Sie sich an einen Administrator, damit dieser Ihren Benutzer löschen kann.';
		else
		{
			//$sql = 'delete from sys_users where userid = '.$_REQUEST["userid"];
			if (isset($_GET["disable"])) 
				if ($_GET["disable"]=="1")
					$disable="0";
				else
					$disable="1";
			else
				$disable=1;
				
			$sql = 'update sys_users set disable='.$disable.' where userid = '.$_REQUEST["userid"];
			
			$dummy = DoSQL($db,$sql);
			if ($dummy["code"] == 0)
			{
				$ausgabe = 'Aktion wurde erfolgreich ausgeführt !';
				mlog("Benutzerverwaltung: User Status von: ".$_REQUEST["userid"]." wurde geändert: disable=".$disable);
			}
			else
				print_r($dummy);
		}
		echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.location.href=\'index.php?site=users&action=user&PHPSESSID='.session_id().'\';">'.$ausgabe.'</A>';
		echo '<SCRIPT TYPE="text/javascript">';
			echo 'setTimeout("window.location=\'index.php?site=users&action=user&PHPSESSID='.session_id().'\'",2000);';
		echo '</SCRIPT>';
	}
	else
		echo $no_rights;
	break;
	
	
	case "authrequest":
	if (priv("user")) {
	// Passwort generieren
		$string = "Dies ist der Text, der verschlüsselt wird, damit wir sichere Passwoerter generieren können."; 
		$length = 5; 
		$string = md5($string); 
		$strLength = strlen($string); 
		srand ((double) microtime() * 1000000); 
		$begin = rand(0,($strLength-$length-1)); 
		$password = substr($string, $begin, $length); 
	// Passwort generieren ENDE

// Überprüfung ob NAME schon vorhanden !!

		$sql1 = 'select * from sys_requests where requestid = '.$_REQUEST["requestid"];
		$result1 = GetResult($db,$sql1);

		$sql2  = 'insert into sys_users (login, passwordmd5,loginmd5, name, email, groupid)';
		$sql2  .= ' values ("'.$result1[0]["login"].'", md5("'.$password.'"), md5("'.$result1[0]["login"].'"), "'.$result1[0]["name"].'", "'.$result1[0]["email"].'", 5)';
		$dummy = DoSQL($db,$sql2);

		if ($dummy["code"] == 0)
		{
			$sql4 = 'delete from sys_requests where requestid = '.$_REQUEST["requestid"];
			$dummy4 = doSQL($db,$sql4);
			$ausgabe = 'Der Antrag wurde genehmigt.';

			$to  = $result1[0]["name"].' <'.$result1[0]["email"].'>';
			$subject = 'www.tus-ocholt.de - Zugang';
			$message = '<html><head><title>www.tus-ocholt.de - Zugang</title></head><body>';
			$message .= 'Hallo '.$result1[0]["name"].'!<br>Ihr Zugang "'.$result1[0]["login"].'" für die Seite www.tus-ocholt.de wurde erfolgreich eingerichtet.<br>';
			$message .= 'Das Passwort für den Zugang "'.$result1[0]["login"].'" lautet "'.$password.'". Sie können das Passwort nach der Anmeldung über "Mein Profil" ändern.<br>(Dies ist eine vom System erstellte Email, bitte beantworten Sie sie nicht!)';
			$message .= '</body></html>';

			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
			$headers .= "From: www.tus-ocholt.de <webmaster@tus-ocholt.de>\r\n";

			mail($to, $subject, $message, $headers);
			mlog("Es wurde ein Zugang genehmigt. Benutzername: ".$result1[0]["login"]);
		}
		else
		{
			$ausgabe = 'Der Antrag konnte nicht genehmigt werden.';
			//print_r($dummy);
		}

		echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.location.href=\'index.php?site=users&action=user&PHPSESSID='.session_id().'\';">'.$ausgabe.'</A>';
		echo '<SCRIPT TYPE="text/javascript">';
			echo 'setTimeout("window.location=\'index.php?site=users&action=user&PHPSESSID='.session_id().'\'",2000);';
		echo '</SCRIPT>';
	}
	else
		echo $no_rights;
	break;
	
	
	case "delrequest":
	if (priv("user"))
	{
		$sql = 'delete from sys_requests where requestid = '.$_REQUEST["requestid"];
		$dummy = DoSQL($db,$sql);
		if ($dummy["code"] == 0)
		{
			mlog("Es wurde ein Antrag gelöscht");
			$ausgabe = 'Der Antrag wurde erfolgreich entfernt.';
		}
		else
		{
			$ausgabe = 'Der Antrag konnte nicht entfernt werden.';
			//print_r($dummy);
		}
		echo '<A HREF="index.php?site=users&action=user&PHPSESSID='.session_id().'">'.$ausgabe.'</A>';
		echo '<SCRIPT TYPE="text/javascript">';
			echo 'setTimeout("window.location=\'index.php?site=users&action=user&PHPSESSID='.session_id().'\'",2000);';
		echo '</SCRIPT>';
	}
	else
		echo $no_rights;
	break;
	
	
	case "delgroup":
		if (priv("user"))
		{
			$sql = 'select count(*) from sys_users where groupid = '.$_REQUEST["groupid"];
			$result = getResult($db,$sql);
			if ($result[0]["count(*)"] == 0)
			{
				$sql = 'delete from sys_groups where groupid = '.$_REQUEST["groupid"];
				$result = doSQL($db, $sql);
				if ($result["code"] == 0)
					$ausgabe = 'Die Gruppe wurde erfolgreich gelöscht.';
				else
				{
					$ausgabe = 'Die Gruppe konnte nicht gelöscht werden.<br>';
					$ausgabe .= $result["msg1"].'<br>'.$result["msg2"];
				}
			}
			else
			{
				$ausgabe = 'Es sind dieser Gruppe noch Benutzer zugeorndet. Bitte entfernen Sie die Zuordnung zuerst.';
			}
			echo '<A HREF="index.php?site=users&action=groups&PHPSESSID='.session_id().'">'.$ausgabe.'</A>';
			echo '<SCRIPT TYPE="text/javascript">';
				echo 'setTimeout("window.location=\'index.php?site=users&action=groups&PHPSESSID='.session_id().'\'",2000);';
			echo '</SCRIPT>';
		}
		else
			echo $no_rights;
		break;
	case "groups":
	if (priv("user"))
	{
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
	function popup_group(groupid)
	{
		var url;
		if (groupid == 0)
		<?php
			echo 'url = "group_popup.php?action=new&PHPSESSID='.session_id().'";';
		?>
		else
		<?php
			echo 'url = "group_popup.php?action=edit&grpid="+groupid+"&PHPSESSID='.session_id().'";';
		?>
		window.open(url,"group","width=450, height=450, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
	}
-->
</SCRIPT>
		<CENTER><TABLE WIDTH="40%" BORDER="0">
			<TR>
				<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
					<a href="javascript:popup_group(0);">
					<IMG SRC="images/new.jpg" BORDER="0" ALT="neu">
					</a>
				</TD>
				<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
					<a href="javascript:popup_group(0);">
					<B>neue Gruppe anlegen</B>
					</a>
				</TD>
			</TR>
		</TABLE></CENTER><BR>

		<TABLE WIDTH="90%" BORDER="0">
			<TR BGCOLOR="#FFFFFF">
				<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">
					<B>Nummer</B>
				</TD>
				<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">
					<B>Name</B>
				</TD>
				<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">
					<B>Beschreibung</B>
				</TD>
				<TD ALIGN="CENTER" COLSPAN="3" BGCOLOR="#DDDDDD">
					<B>Aktion</B>
				</TD>
			</TR>
<?php
		$sql = 'select * from sys_groups';
		$result = GetResult($db, $sql);
		foreach ($result as $group)
		{
			echo'<TR>';
				echo'<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo $group["groupid"];
				echo'</TD>';
				echo'<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo $group["name"];
				echo'</TD>';
				echo'<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo $group["descr"];
				echo'</TD>';
				echo'<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<a href="javascript:popup_group('.$group["groupid"].');">';
					echo '<IMG SRC="images/edit.jpg" BORDER="0" ALT="Bearbeiten">';
					echo '</a>';
				echo'</TD>';
                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                        echo '<INPUT TYPE="CHECKBOX" ID="chk'.$group["groupid"].'" onClick="enableDelete(this.id,\'del'.$group["groupid"].'\',\'emp'.$group["groupid"].'\')" />';
                echo '</TD>';
                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                        echo '<DIV ID="emp'.$group["groupid"].'" STYLE="display:block;">';
                                echo '<IMG SRC="images/empty.gif" BORDER="0" HEIGHT="16" WIDTH="16">';
                        echo '</DIV>';
                        echo '<DIV ID="del'.$group["groupid"].'" STYLE="display:none;">';
                                echo '<A HREF="index.php?site=users&action=delgroup&groupid='.$group["groupid"].'&PHPSESSID='.session_id().'">';
                                echo '<IMG SRC="images/del.gif" BORDER="0" HEIGHT="16" WIDTH="16">';
                                echo '</A>';
                        echo '</DIV>';
                echo '</TD>';
			echo'</TR>';
		}
		echo '</TABLE>';
	}
	else
		echo $no_rights;
		break;
	case "rights":
	if (priv("user"))
	{
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
	function popup_rights(rightid)
	{
		var url;
		if (rightid == 0)
		<?php
			echo 'url = "rights_popup.php?action=new&PHPSESSID='.session_id().'";';
		?>
		else
		<?php
			echo 'url = "rights_popup.php?action=edit&rightid="+rightid+"&PHPSESSID='.session_id().'";';
		?>
		window.open(url,"rights","width=450, height=450, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
	}
-->
</SCRIPT>
		<CENTER><TABLE WIDTH="40%" BORDER="0">
			<TR>
				<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
					<a href="javascript:popup_rights(0);">
					<IMG SRC="images/new.jpg" BORDER="0" ALT="neu">
					</a>
				</TD>
				<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
					<a href="javascript:popup_rights(0);">
					<B>neue Berechtigung anlegen</B>
					</a>
				</TD>
			</TR>
		</TABLE></CENTER><BR>

		<TABLE WIDTH="90%" BORDER="0">
			<TR BGCOLOR="#FFFFFF">
				<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">
					<B>ID</B>
				</TD>
				<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">
					<B>Kategorie</B>
				</TD>
				<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">
					<B>Bezeichnung</B>
				</TD>
				<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">
					<B>Beschreibung</B>
				</TD>
				<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">
					<B>Bearbeiten</B>
				</TD>
			</TR>
<?php
		$sql = 'select * from sys_rights order by kategorie,name';
		$result = GetResult($db, $sql);
		foreach ($result as $right)
		{
			echo'<TR>';
				echo'<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo $right["id"];
				echo'</TD>';
				echo'<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo $right["kategorie"];
				echo'</TD>';
				echo'<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo $right["name"];
				echo'</TD>';
				echo'<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo $right["descr"];
				echo'</TD>';
				echo'<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<a href="javascript:popup_rights('.$right["id"].');">';
					echo '<IMG SRC="images/edit.jpg" BORDER="0" ALT="Bearbeiten">';
					echo '</a>';
				echo'</TD>';
			echo'</TR>';
		}
		echo '</TABLE>';
	}
	else
		echo $no_rights;
	break;
		
		
	case 'login':
			$sqlstr="select id,login,ts,failed,ip from sys_deny order by login";
			$result=GetResult($db,$sqlstr);

			echo '<TABLE WIDTH="90%" BORDER="0">';
			echo '<TR BGCOLOR="#DDDDDD">';
				echo '<TD COLSPAN="9" ALIGN="LEFT" BGCOLOR="#AAAAAA">';
					echo '<B>Fehlgeschlagene Einlogversuche</B>';
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Benutzername</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Datum</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>IP</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Versuche</B>';
				echo '</TD>';
				echo '<TD COLSPAN="1" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Regulärer<br>Benutzername</B>';
				echo '</TD>';
				echo '<TD COLSPAN="1" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Gesperrt</B>';
				echo '</TD>';
				echo '<TD COLSPAN="1" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Aktion</B>';
				echo '</TD>';
			echo '</TR>';

			$sqlstr="select d.id,d.login,d.ts,d.failed,d.ip from sys_deny d,sys_users u " .
					" where d.login=u.login ".
					" order by login ";
			$result=GetResult($db,$sqlstr);
			if (isset($result)) {
				foreach ($result as $row) {
					echo '<tr>';
					echo '<td>'.$row["login"].'</td>';
					$ts=$row["ts"];
					echo '<td ALIGN="CENTER">'.date("d.m.Y - H:i", strtotime($ts)).'</td>';
					echo '<td ALIGN="CENTER">'.$row["ip"].'</td>';
					echo '<td ALIGN="CENTER">'.$row["failed"].'</td>';
					$sqlstr1="select userid anz from sys_users where login='".$row["login"]."'";
					$result1=GetResult($db,$sqlstr1);
					if (isset($result1))
						echo '<td ALIGN="CENTER">ja</td>'; 				
					else
						echo '<td ALIGN="CENTER">nein</td>'; 				
				
					if ($row["failed"]>=4) {
						echo '<td ALIGN="CENTER" BGCOLOR="'.$adm_link.'">Ja</td>';
					} else {
						echo '<td ALIGN="CENTER">Nein</td>';
					}
					
					$link_del='index.php?site=users&action=loginreset&id='.$row["id"].'&PHPSESSID='.session_id();
					table_link("zurücksetzen",$link_del);
					
					echo '</tr>';
				}
			}
		break;
		case 'loginreset':
			$denyid=$_GET["id"];
			$sqlstr="delete from sys_deny where id=$denyid";
			$result=doSQL($db,$sqlstr);
			if ($result["code"]==0) {
				echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.location.href=\'index.php?site=users&action=login&PHPSESSID='.session_id().'\';">Sperrung wurde aufgehoben. Eintrag gelöscht.</A>';
				echo '<SCRIPT TYPE="text/javascript">';
					echo 'setTimeout("window.location=\'index.php?site=users&action=login&PHPSESSID='.session_id().'\'",2000);';
				echo '</SCRIPT>';
			} else {
				echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.location.href=\'index.php?site=users&action=login&PHPSESSID='.session_id().'\';">Sperrung konnte nicht aufgehoben werden</A>';
			}
			
			echo "<br>Zurücksetzen eines benutzers $denyid";
			
		break;
		
}
			echo '<BR>&nbsp;';
		echo '</TD>';
	echo '</TR>';
echo '</TABLE>';
}
else
	echo $no_rights;
?>