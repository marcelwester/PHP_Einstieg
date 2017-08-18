<?php
// Volker 12.02.2005 Anpassung Benutzerverwaltung:
//                   Benutzer werden nicht mehr gelöscht, sondern nur noch inaktiv

// Volker 24.10.2008 Umstellung auf Clientseitige md5 hashes

sitename("login.php",$_SESSION["groupid"]);



$action=$_GET["action"];
if (!isset($action))
    $action='login';
    
switch($action)
{
	case 'login':
		$_SESSION["challenge"]=simpleRandString(16);
?>
		<script language="javascript" src="js/md5.js"></script>
			<script language="javascript">
			<!--
				function doChallengeResponse() {
					str = document.login.challenge.value+MD5(document.login.pwd.value);
					document.login.response.value = MD5(str);
					document.login.pwd.value = "";
					document.login.login.value = MD5(document.login.username.value);"";
					document.login.username.value =""; 
					document.login.challenge.value = "";
				}

			// -->
		</script>
<?php		
		$actionShow="Anmelden";
		echo '<FORM NAME="login" METHOD="POST" ACTION="index.php?site=login&action=check&PHPSESSID='.session_id().'">';
		echo '<table class="layout" width="80%" align="center">';
				echo '<tr>';
					table_data("<h1><u>".$actionShow."</u></h1>");
				echo '</tr>';
			echo '</table>';
?>			
			<br><br><br>
			<TABLE WIDTH="80%" align="center" class="layout">
				<TR>
					<TD COLSPAN="2" ALIGN="LEFT" >
						<B>Bitte melden Sie sich mit Ihrem Benutzernamen und pers&ouml;nlichen Kennwort an !</B>
					</TD>
				</TR>
				<TR>
					<TD ALIGN="right" width="40%"  >
						Benutzername
					</TD>
					<TD ALIGN="left" width="60%" >
						<INPUT NAME="username" ID="username" TYPE="TEXT" SIZE="20">
					</TD>
				</TR>
				<TR>
					<TD ALIGN="right" width="40%"  >
						Passwort
					</TD>
					<TD ALIGN="left" width="60%" >
						<INPUT NAME="pwd" ID="pwd" TYPE="PASSWORD" SIZE="20">
					</TD>
				</TR>
				<TR>
					<TD COLSPAN="2" ALIGN="CENTER" >
						<input type="hidden" name="challenge" value="<?php echo $_SESSION["challenge"]; ?>">
						<input type="hidden" name="response"  value="" size=32>
						<input type="hidden" name="login" value="" >
						<input onClick="doChallengeResponse(); return true;" type="submit" name="submitbtn" value="Anmelden">
					</TD>
				</TR>
				
			</TABLE>
<?php
	echo '</FORM>';

	break;
	case 'check':
		echo '<br><br>';
		$actionShow="Anmeldung pr&uuml;fen";
		echo '<table class="layout" width="80%" align="center">';
				echo '<tr>';
					table_data("<h1><u>".$actionShow."</u></h1>");
				echo '</tr>';
		echo '</table>';



		// User anhand des md5 hashes des login in der Datenbank suchen
		$sql  = "select userid,passwordmd5 from sys_users where ";
		$sql .= "md5(login) = '".$_POST["login"]."'"; 
		$rs->query($sql);
		if ($result=$rs->fetchRow()) {
			$sql  = "select userid,login,groupid,name,vorname,email from sys_users where ";
			$sql .= "userid=".$result["userid"]." and ";
			$sql .= "disable=0 and ";
			$sql .= "sysdate() < valid_to  and ";
			// Password pruefen: md5(challenge + passwordmd5 = response vom client
			$sql .= "md5('".$_SESSION["challenge"].$result["passwordmd5"]."')='".$_POST["response"]."'";
			$rs -> query($sql);
		}


		echo '<table class="layout" width="80%" align="center">';
			echo '<tr><td align="center"';
				if ($result=$rs->fetchRow())
				{
					$_SESSION["groupid"] = $result["groupid"];
					$_SESSION["login"] = $result["login"];
					$_SESSION["userid"] = $result["userid"];
					$_SESSION["email"] = $result["email"];
					$_SESSION["username"] = $result["vorname"]." ".$result["name"];
					$sqlstr="update sys_users set last_login = now() where userid = ".$_SESSION["userid"];
					$rs->query($sqlstr);
					mlog("Login");
					sessionControl(session_id(),"login");
					$_SESSION["application"]=$APPLICATION;
					echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.location.href=\'index.php?site=aktion&PHPSESSID='.session_id().'\';">Anmeldung erfolgreich!</A>';
					echo '<SCRIPT TYPE="text/javascript">';
						echo 'setTimeout("window.location=\'index.php?site=aktion&showinfo=1&PHPSESSID='.session_id().'\'",1000);';
					echo '</SCRIPT>';
				} else {
					echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.location.href=\'index.php?site=login&\';">Anmeldung fehlgeschlagen!</A>';
					echo '<SCRIPT TYPE="text/javascript">';
						echo 'setTimeout("window.location=\'index.php?site=login\'",1000);';
					echo '</SCRIPT>';
				}
			echo '</td></tr>';
		echo '</table>'; 
		break;
		
	case 'logout':
		$actionShow="Abmelden";
		echo '<table class="layout" width="80%" align="center">';
				echo '<tr>';
					table_data("<h1><u>".$actionShow."</u></h1>");
				echo '</tr>';
		echo '</table>';
		
		mlog("Abmelden vom System");
		sessionControl(session_id(),"logout");
		session_destroy();
		echo '<table class="layout" width="80%" align="center">';
			echo '<tr><td align="center"';
				echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.location.href=\'index.php?site=login\';">Abmeldung erfolgreich!</A>';
				echo '<SCRIPT TYPE="text/javascript">';
					echo 'setTimeout("window.location=\'index.php?site=login\'",1000);';
				echo '</SCRIPT>';
			echo '</td></tr>';
		echo '</table>';
		break;
	case 'edituser':
		if (!priv("edituser")) {
		   echo "Fehlende Berechtigung";
		   exit;
		}
	?>
		<script language="javascript" src="js/md5.js"></script>
			<script language="javascript">
			<!--
				function sendeditprofile() {
					if (document.login.pwd1.value != "") {
						document.login.pwd3.value = MD5(document.login.pwd1.value);
						document.login.pwd4.value = MD5(document.login.pwd2.value);
						document.login.pwd1.value = ""; 
					    document.login.pwd2.value = "";
					} else {
						document.login.pwd1.value = ""; 
					    document.login.pwd2.value = "";
					    document.login.pwd3.value = ""; 
					    document.login.pwd4.value = "";
					}
				}
           // -->
		</script>

	 <?php	
	    back("aktion");
		$actionShow="Benutzerdaten &auml;ndern";
		echo '<table class="layout" width="80%" align="center">';
				echo '<tr>';
					table_data("<h1><u>".$actionShow."</u></h1>");
				echo '</tr>';
		echo '</table>';

		echo '<FORM NAME="login" METHOD="POST" ACTION="index.php?site=login&action=saveprofile&PHPSESSID='.session_id().'">';
		$sql = 'select * from sys_users where userid = '.$_SESSION["userid"];
		$rs->query($sql);
		
		if ($user = $rs->fetchRow()) {
			{
?>				<TABLE WIDTH="80%" class ="layout" align="center">
					<TR>
						<TD COLSPAN="2" ALIGN="center" >
							<B>Hier k&ouml;nnen Sie Ihre Benutzerdaten &auml;ndern.</B>
						</TD>
					</TR>
					<TR>
						<TD ALIGN="right" >
							Benutzername
						</TD>
						<TD ALIGN="left" >
							
							<INPUT  NAME="login" ID="login" TYPE="TEXT" SIZE="20" VALUE="<?php echo $user["login"]; ?>" readonly>
						</TD>
					</TR>
					<TR>
						<TD ALIGN="right" >
							Passwort
						</TD>
						<TD ALIGN="left" >
							<INPUT NAME="pwd1" ID="pwd1" TYPE="PASSWORD" SIZE="20">
						</TD>
					</TR>
					<TR>
						<TD ALIGN="right" >
							Passwort wiederholen
						</TD>
						<TD ALIGN="left" >
							<INPUT NAME="pwd2" ID="pwd2" TYPE="PASSWORD" SIZE="20">
						</TD>
					</TR>
					<TR>
						<TD COLSPAN="2" ALIGN="CENTER" >
							Falls Sie Ihr altes Passwort beibehalten wollen, lassen Sie die beiden Felder unausgef&uuml;llt.
						</TD>
					</TR>
					<TR>
						<TD ALIGN="right" >
							Emailadresse
						</TD>
						<TD ALIGN="left" >
							<INPUT NAME="email" ID="email" TYPE="TEXT" SIZE="20" VALUE="<?php echo $user["email"]; ?>">
						</TD>
					</TR>
					<TR>
						<TD ALIGN="right" >
							Vorname
						</TD>
						<TD ALIGN="left" >
							<INPUT NAME="vorname" ID="vornamename" TYPE="TEXT" SIZE="20" VALUE="<?php echo $user["vorname"]; ?>">
						</TD>
					</TR>
					<TR>
						<TD ALIGN="right" >
							Name
						</TD>
						<TD ALIGN="left" >
							<INPUT NAME="name" ID="name" TYPE="TEXT" SIZE="20" VALUE="<?php echo $user["name"]; ?>">
						</TD>
					</TR>
					<TR>
						<TD ALIGN="right" >
							Nickname
						</TD>
						<TD ALIGN="left" >
							<INPUT NAME="nickname" ID="nickname" TYPE="TEXT" SIZE="20" VALUE="<?php echo $user["nickname"]; ?>">
						</TD>
					</TR>
					
					<TR>
						<TD COLSPAN="2" ALIGN="CENTER" >
							<!-- <INPUT TYPE="submit" ACTION="submit()" VALUE="Speichern"> -->
							<INPUT NAME="pwd3" ID="pwd3" TYPE="hidden">
							<INPUT NAME="pwd4" ID="pwd4" TYPE="hidden">
							<input onClick="sendeditprofile(); return true;" type="submit" name="submitbtn" value="Speichern">
						</TD>
					</TR>

				</TABLE>
<?php
			}
		}
		echo '</FORM>';
		break;
		case 'saveprofile':
		$actionShow="Benutzerdaten speichern";
		echo '<table class="layout" width="80%" align="center">';
				echo '<tr>';
					table_data("<h1><u>".$actionShow."</u></h1>");
				echo '</tr>';
		echo '</table>';

			if ($_POST["pwd3"] == $_POST["pwd4"]) {
				$sql = 'update sys_users set ';
				$sql .= 'email = "'.$_POST["email"].'", ';
				if (strlen($_POST["pwd3"]) > 0 && $_POST["pwd3"] == $_POST["pwd4"]) {
					mlog("Passwort wurde von angemeldeten Benutzer geändert: ".$_SESSION["userid"]); 
					$sql .= "passwordmd5 ='".$_POST["pwd3"]."', ";
					//echo '<br>Das Passwort wird neu gespeichert !<br>';
				}
				$sql .= 'name = "'.$_POST["name"].'", ';
				$sql .= 'nickname = "'.$_POST["nickname"].'", ';
				$sql .= 'vorname = "'.$_POST["vorname"].'" ';
				$sql .= 'where userid = '.$_SESSION["userid"];
			}
			
			if ($rs->query($sql)!=false)  
			   $dummy["code"]="0";
			else 
			   $dummy["code"]="-1";
				
			
			
			if ($dummy["code"] == 0)
			{
				mlog("Benutzerprofil wurde von angemeldeten Benutzer gespeichert: ".$_SESSION["userid"]); 
				$ausgabe = 'Das Profil wurde erfolgreich geändert.';
				$_SESSION["username"] = $_POST["vorname"]." ".$_POST["name"];
			}
			else
			{
				echo "<br><b>Die eingebenen Passwörter stimmen nicht überein</b><br>";
				$ausgabe = 'Das Profil konnte nicht ge&auml;ndert werden.';
			}
	
			echo '<table class="layout" width="80%" align="center">';
			echo '<tr><td align="center"';
				echo '<br><A HREF="javascript:window.history.go(-1)">'.$ausgabe.'</A>';
				if ($dummy["code"] == 0) {
					echo '<SCRIPT TYPE="text/javascript">';
						echo 'setTimeout("window.location=\'index.php?site=aktion&PHPSESSID='.session_id().'\'",1000);';
					echo '</SCRIPT>';
				}
			echo '</td></tr>';
			echo '</table>';
		break;
	case 'loginkey':
		include "loginkey.php";	
	break;
		
	case 'request':
		echo '<FORM NAME="login" METHOD="POST" ACTION="index.php?site=login&action=saverequest&PHPSESSID='.session_id().'">';
?>
				<TABLE WIDTH="80%" class="layout" align="center">
					<TR >
						<TD COLSPAN="2" ALIGN="LEFT">
							<B>Hier können Sie einen Zugang beantragen.</B>
						</TD>
					</TR>
					<TR>
						<TD ALIGN="right" >
							gewünschter Benutzername:
						</TD>
						<TD ALIGN="left" >
							<INPUT NAME="login" ID="login" TYPE="TEXT" SIZE="50" VALUE="">
						</TD>
					</TR>
					<TR>
						<TD ALIGN="right" >
							Emailadresse :
						</TD>
						<TD ALIGN="left" >
							<INPUT NAME="email" ID="email" TYPE="TEXT" SIZE="50" VALUE="">
						</TD>
					</TR>
					<TR>
						<TD ALIGN="right" >
							Name:
						</TD>
						<TD ALIGN="left" >
							<INPUT NAME="name" ID="name" TYPE="TEXT" SIZE="50" VALUE="">
						</TD>
					</TR>
					<TR>
						<TD ALIGN="right" >
							Vorname:
						</TD>
						<TD ALIGN="left" >
							<INPUT NAME="vorname" ID="vorname" TYPE="TEXT" SIZE="50" VALUE="">
						</TD>
					</TR>
					<TR>
						<TD COLSPAN="2" ALIGN="CENTER" >
							<INPUT TYPE="submit"  VALUE="Beantragen">
						</TD>
					</TR>
				</TABLE>
<?php
	echo '</FORM>';

		break;
	case 'saverequest':
		$sql = 'insert into sys_requests (login, name,vorname, email, reason) values (';
		$sql .= '"'.$_POST["login"].'", "'.$_POST["name"].'", "'.$_POST["vorname"].'", "'.$_POST["email"].'", "'.$_POST["reason"].'"';
		$sql .= ')';

        if ($rs->query($sql)) $dummy=0;
		// $dummy = doSQL($db, $sql);


		if ($dummy["code"] == 0)
		{
			$ausgabe = 'Der Antrag wurde gespeichert.';
		}
		else
		{
			$ausgabe = 'Der Antrag konnte nicht gespeichert werden.<br>';
		}

		$subject = 'Antrag';
		$message = 'Es wurde ein Zugang beantragt';
		$sqlstr="select email from sys_users where groupid=10";
		$rs->query($sqlstr);
		
		//$resultmail=GetResult($db,$sqlstr);
		while ($to=$rs->fetchRow()) {
				mail($to["email"], $subject, $message,null,'-fnoreply@tus-ocholt.de');
		}

		echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.location.href=\'index.php?site=start&'.session_id().'\';">'.$ausgabe.'</A>';
		echo '<SCRIPT TYPE="text/javascript">';
			echo 'setTimeout("window.location=\'index.php?site=start&'.session_id().'\'",1000);';
		echo '</SCRIPT>';
		
		break;
}
?>