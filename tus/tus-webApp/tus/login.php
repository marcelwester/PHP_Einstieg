<?php
// Volker 12.02.2005 Anpassung Benutzerverwaltung:
//                   Benutzer werden nicht mehr gelöscht, sondern nur noch inaktiv

// Volker 24.10.2008 Umstellung auf Clientseitige md5 hashes

sitename("login.php",$_SESSION["groupid"]);
switch($action)
{
	case 'login':
		$_SESSION["challenge"]=simpleRandString(16);
?>
		<script language="javascript" src="md5.js"></script>
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
		echo '<FORM NAME="login" METHOD="POST" ACTION="index.php?site=login&action=check&PHPSESSID='.session_id().'">';
?>			<TABLE WIDTH="90%">
				<TR BGCOLOR="#DDDDDD">
					<TD COLSPAN="2" ALIGN="LEFT" BGCOLOR="#DDDDDD">
						<B>Bitte melden Sie sich mit Ihrem Benutzernamen und persönlichen Kennwort an !</B>
					</TD>
				</TR>
				<TR>
					<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
						Benutzername
					</TD>
					<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
						<INPUT NAME="username" ID="username" TYPE="TEXT" SIZE="50">
					</TD>
				</TR>
				<TR>
					<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
						Passwort
					</TD>
					<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
						<INPUT NAME="pwd" ID="pwd" TYPE="PASSWORD" SIZE="50">
					</TD>
				</TR>
				<TR>
					<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">
						<input type="hidden" name="challenge" value="<?php echo $_SESSION["challenge"]; ?>">
						<input type="hidden" name="response"  value="" size=32>
						<input type="hidden" name="login" value="" >
						<input onClick="doChallengeResponse(); return true;" type="submit" name="submitbtn" value="Anmelden">
					</TD>
				</TR>
				<TR>
					<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#FFFFFF">
						Der interne Bereich ist nur für bestimmte Personen vorgesehen um die Seite zu aktualisieren und zu pflegen.<BR>
						Um einen Zugang zu erhalten, stellen Sie bitte hier einen Antrag : <A HREF="index.php?site=login&action=request&PHPSESSID=<?php echo session_id(); ?>">Formular</A>
					</TD>
				</TR>
			</TABLE>
		</FORM>
<?php
	break;
	case 'check':
		echo '<br><br>';
		

/*
		print_r($_POST);
		echo '<br><br>';
	
		print_r($_GET);
		echo '<br><br>';
		print_r($_SESSION);
*/		
		
		// User anhand des md5 hashes des login in der Datenbank suchen
		$sql  = "select userid,passwordmd5 from sys_users where ";
		$sql .= "md5(login) = '".$_POST["login"]."'"; 
		$result = GetResult($db, $sql);
		if (isset($result["0"]["userid"])) {
			$sql  = "select userid,login,groupid,name from sys_users where ";
			$sql .= "userid=".$result["0"]["userid"]." and ";
			$sql .= "disable=0 and ";
			// Password pruefen: md5(challenge + passwordmd5 = response vom client
			$sql .= "md5('".$_SESSION["challenge"].$result["0"]["passwordmd5"]."')='".$_POST["response"]."'";
			unset($result);
			$result = GetResult($db, $sql);
		}
		
		if (isset($result[0]["userid"]))
		{
			$_SESSION["groupid"] = $result[0]["groupid"];
			$_SESSION["login"] = $result[0]["login"];
			$_SESSION["userid"] = $result[0]["userid"];
			$_SESSION["username"] = $result[0]["name"];
			$sqlstr="update sys_users set last_login = now() where userid = ".$_SESSION["userid"];
			$result=doSQL($db,$sqlstr);
			mlog("Login");
			sessionControl(session_id(),"login");
			echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.location.href=\'index.php?site=news&action=currentnews&'.session_id().'\';">Anmeldung erfolgreich!</A>';
			echo '<SCRIPT TYPE="text/javascript">';
				echo 'setTimeout("window.location=\'index.php?site=news&action=currentnews&PHPSESSID='.session_id().'\'",1000);';
			echo '</SCRIPT>';
		} else {
			echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.location.href=\'index.php?site=login&action=login&'.SID.'\';">Anmeldung fehlgeschlagen!</A>';			
		} 
		break;
	case 'logout':
		mlog("Abmelden vom System");
		sessionControl(session_id(),"logout");
		session_destroy();
		echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.location.href=\'index.php?site=site&action=show&siteid=3\';">Abmeldung erfolgreich!</A>';
		echo '<SCRIPT TYPE="text/javascript">';
			echo 'setTimeout("window.location=\'index.php?site=news&action=currentnews\'",1000);';
		echo '</SCRIPT>';
		break;
	case 'edituser':
	?>
		<script language="javascript" src="md5.js"></script>
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
		echo '<FORM NAME="login" METHOD="POST" ACTION="index.php?site=login&action=saveprofile&PHPSESSID='.session_id().'">';
		$sql = 'select * from sys_users where userid = '.$_SESSION["userid"];
		$result = GetResult($db, $sql);

		if (!isset($result["code"]))
		{
			foreach($result as $user)
			{
?>				<TABLE WIDTH="90%">
					<TR BGCOLOR="#DDDDDD">
						<TD COLSPAN="2" ALIGN="LEFT" BGCOLOR="#DDDDDD">
							<B>Hier können Sie Ihre Benutzerdaten ändern.</B>
						</TD>
					</TR>
					<TR>
						<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
							Benutzername
						</TD>
						<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">
							
							<INPUT  NAME="login" ID="login" TYPE="TEXT" SIZE="50" VALUE="<?php echo $user["login"]; ?>" readonly>
						</TD>
					</TR>
					<TR>
						<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
							Passwort
						</TD>
						<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
							<INPUT NAME="pwd1" ID="pwd1" TYPE="PASSWORD" SIZE="50">
						</TD>
					</TR>
					<TR>
						<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
							Passwort wiederholen
						</TD>
						<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
							<INPUT NAME="pwd2" ID="pwd2" TYPE="PASSWORD" SIZE="50">
						</TD>
					</TR>
					<TR>
						<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
							Emailadresse
						</TD>
						<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
							<INPUT NAME="email" ID="email" TYPE="TEXT" SIZE="50" VALUE="<?php echo $user["email"]; ?>">
						</TD>
					</TR>
					<TR>
						<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
							Name
						</TD>
						<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
							<INPUT NAME="name" ID="name" TYPE="TEXT" SIZE="50" VALUE="<?php echo $user["name"]; ?>">
						</TD>
					</TR>
					<TR>
						<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">
							<!-- <INPUT TYPE="submit" ACTION="submit()" VALUE="Speichern"> -->
							<INPUT NAME="pwd3" ID="pwd3" TYPE="hidden">
							<INPUT NAME="pwd4" ID="pwd4" TYPE="hidden">
							<input onClick="sendeditprofile(); return true;" type="submit" name="submitbtn" value="Speichern">
						</TD>
					</TR>
					<TR>
						<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#FFFFFF">
							Falls Sie Ihr altes Passwort beibehalten wollen, lassen Sie die beiden Felder unausgefüllt.
						</TD>
					</TR>
				</TABLE>
<?php
			}
		}
		echo '</FORM>';
		break;
		case 'saveprofile':
/*
		print_r($_POST);
		exit;
*/
			if ($_POST["pwd3"] == $_POST["pwd4"]) {
				$sql = 'update sys_users set ';
				// $sql .= 'login = "'.$_POST["login"].'", ';
				$sql .= 'email = "'.$_POST["email"].'", ';
				if (strlen($_POST["pwd3"]) > 0 && $_POST["pwd3"] == $_POST["pwd4"]) {
					mlog("Passwort wurde von angemeldeten Benutzer geändert: ".$_SESSION["userid"]); 
					//$sql .= 'password = password("'.$_POST["pwd1"].'"), ';
					$sql .= "passwordmd5 ='".$_POST["pwd3"]."', ";
					echo '<br>Das Passwort wurde neu gespeichert !<br>';
				}
				$sql .= 'name = "'.$_POST["name"].'" ';
				$sql .= 'where userid = "'.$_SESSION["userid"].'"';
				$dummy = doSQL($db, $sql);
			} else {
				$dummy["code"]="-1";
			}
			if ($dummy["code"] == 0)
			{
				mlog("Benutzerprofil wurde von angemeldeten Benutzer gespeichert: ".$_SESSION["userid"]); 
				$ausgabe = 'Das Profil wurde erfolgreich geändert.';
				$_SESSION["username"] = $_POST["name"];
			}
			else
			{
				echo "<br><b>Die eingebenen Passwörter stimmen nicht überein</b><br>";
				$ausgabe = 'Das Profil konnte nicht geändert werden.';
			}
		
			echo '<br><A HREF="javascript:window.history.go(-1)">'.$ausgabe.'</A>';
//			echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.location.href=\'index.php?site=news&action=currentnews&'.session_id().'\';">'.$ausgabe.'</A>';
			if ($dummy["code"] == 0) {
				echo '<SCRIPT TYPE="text/javascript">';
					echo 'setTimeout("window.location=\'index.php?site=news&action=currentnews&'.session_id().'\'",1000);';
				echo '</SCRIPT>';
			}
		break;

	case 'request':
		echo '<FORM NAME="login" METHOD="POST" ACTION="index.php?site=login&action=saverequest&PHPSESSID='.session_id().'">';
?>
				<TABLE WIDTH="90%">
					<TR BGCOLOR="#DDDDDD">
						<TD COLSPAN="2" ALIGN="LEFT" BGCOLOR="#DDDDDD">
							<B>Hier können Sie einen Zugang beantragen.</B>
						</TD>
					</TR>
					<TR>
						<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
							gewünschter Benutzername
						</TD>
						<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
							<INPUT NAME="login" ID="login" TYPE="TEXT" SIZE="50" VALUE="">
						</TD>
					</TR>
					<TR>
						<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
							Emailadresse :
						</TD>
						<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
							<INPUT NAME="email" ID="email" TYPE="TEXT" SIZE="50" VALUE="">
						</TD>
					</TR>
					<TR>
						<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
							Name
						</TD>
						<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
							<INPUT NAME="name" ID="name" TYPE="TEXT" SIZE="50" VALUE="">
						</TD>
					</TR>
					<TR>
						<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
							Begründung
						</TD>
						<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
							<INPUT NAME="reason" ID="reason" TYPE="TEXT" SIZE="80" VALUE="">
						</TD>
					</TR>
					<TR>
						<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">
							<INPUT TYPE="submit" ACTION="submit()" VALUE="Beantragen">
						</TD>
					</TR>
					<TR>
						<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#FFFFFF">
							Ein Administrator kann anhand der angegebenen Begründung entscheiden, ob der Zugang erstellt wird. In diesem Fall erhalten Sie eine Email mit Ihren Zugangsdaten.
						</TD>
					</TR>
				</TABLE>
			</FORM>
<?php
		break;
	case 'saverequest':
		$sql = 'insert into sys_requests (login, name, email, reason) values (';
		$sql .= '"'.$_POST["login"].'", "'.$_POST["name"].'", "'.$_POST["email"].'", "'.$_POST["reason"].'"';
		$sql .= ')';

		$dummy = doSQL($db, $sql);

		if ($dummy["code"] == 0)
		{
			$ausgabe = 'Der Antrag wurde gespeichert.';
		}
		else
		{
			$ausgabe = 'Der Antrag konnte nicht gespeichert werden.<br>';
		}

		$subject = 'www.tus-ocholt.de - Antrag';
		$message = 'Es wurde ein Zugang beantragt';
		$sqlstr="select email from sys_users where groupid=10";
		$resultmail=GetResult($db,$sqlstr);
		if (isset($resultmail)) {
			foreach ($resultmail as $to) {
				mail($to["email"], $subject, $message,null,'-fnoreply@tus-ocholt.de');
			}
		}

		echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.location.href=\'index.php?site=news&action=currentnews&'.session_id().'\';">'.$ausgabe.'</A>';
		echo '<SCRIPT TYPE="text/javascript">';
			echo 'setTimeout("window.location=\'index.php?site=news&action=currentnews&'.session_id().'\'",1000);';
		echo '</SCRIPT>';
		
		break;
}
?>