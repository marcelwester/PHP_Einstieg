<?php
include "inc.php";
sitename("user_tus_pass_popup.php",$_SESSION["groupid"]);
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php
focus();
if (priv("user") && isset($_REQUEST["userid"]))
{
	switch ($_REQUEST["action"])
	{
	case 'edit':
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
		echo '<FORM NAME="login" METHOD="POST" ACTION="user_tus_pass_popup.php?action=save&userid='.$_REQUEST["userid"].'&PHPSESSID='.session_id().'">';

		if ($_REQUEST["userid"]=="0") {
			$result["0"]["name"] = "";	
			$result["0"]["login"] = "";
			$result["0"]["email"] = "";
			$headline="Neuen Benutzer anlegen";
		} else {
			$sql = 'select userid,login,name,email,groupid,disable from sys_users where userid = '.$_REQUEST["userid"];
			$result = GetResult($db, $sql);
			$headline="Benutzerdaten ändern";
		}

		if (!isset($result["code"]))
		{
			foreach($result as $user)
			{
?>				<TABLE WIDTH="90%" ALIGN="CENTER">
					<TR BGCOLOR="#DDDDDD">
						<TD ALIGN="CENTER" COLSPAN="2" ALIGN="LEFT" BGCOLOR="#DDDDDD">
							<B><?php echo $headline; ?></B>
						</TD>
					</TR>
					<TR>
						<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
							Benutzername
						</TD>
						<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
							<?php
								if ($_REQUEST["userid"]!="0") {
									echo '<INPUT NAME="login" ID="login" TYPE="TEXT" SIZE="50" VALUE="'.$user["login"].'" readonly>';
									
								} else {
									echo '<INPUT NAME="login" ID="login" TYPE="TEXT" SIZE="50" VALUE="'.$user["login"].'">';
								}
							?>
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
						<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
							Gruppe
						</TD>
						<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">
							<?php
							$sqlstr = 'select count(*) from sys_users where groupid = '.$admin_id;
							$numadmins = GetResult($db, $sqlstr);
							if ($numadmins[0]["count(*)"] >= 5) {
								if ($user["groupid"]==$admin_id) {
									$sqlstr = "select groupid,name from sys_groups order by name";
								} else {
									$sqlstr = "select groupid,name from sys_groups where ".
											  " groupid<>".$admin_id." ".
											  " order by name";
								}
								$result1=getResult($db,$sqlstr);
								build_select($result1,"name","groupid","groupid","",1,$result["0"]["groupid"]);
								unset($result1);
							} else {
								$sqlstr = "select groupid,name from sys_groups order by name";
								$result1=getResult($db,$sqlstr);
								build_select($result1,"name","groupid","groupid","",1,$user["groupid"]);
								unset($result1);
							}
							?>
						</TD>
					</TR>


					<TR>
						<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">
							<INPUT NAME="pwd3" ID="pwd3" TYPE="hidden">
							<INPUT NAME="pwd4" ID="pwd4" TYPE="hidden">
							<input onClick="sendeditprofile(); return true;" type="submit" name="submitbtn" value="Speichern">
						</TD>
					</TR>
					<?php if ($_REQUEST["userid"]!="0") { ?>
						<TR>
							<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#FFFFFF">
								Falls das Passwort unverändert bleiben soll, lassen Sie die beiden Felder unausgefüllt.
							</TD>
						</TR>
					<?php } ?>
				</TABLE>
<?php
			}
		}
		echo '</FORM>';
		break;
	case 'save':
		$dummy["code"]="0";
		if ($_POST["name"]=="") {
			echo "<br><b>Keinen Namen eingetragen</b><br>";
			$dummy["code"]="-1";
		}
		if ($_POST["email"]=="") {
			echo "<br><b>Keine email Adresse eingetragen</b><br>";
			$dummy["code"]="-1";
		}
		if ($_POST["login"]=="") {
			echo "<br><b>Keinen Benutzernamen eingetragen</b><br>";
			$dummy["code"]="-1";
		}
		if ($_POST["groupid"]=="") {
			echo "<br><b>Keinen Gruppe eingetragen</b><br>";
			$dummy["code"]="-1";
		}
		

		if ($dummy["code"]=="0") {
			if ($_POST["pwd3"] == $_POST["pwd4"]) {
				if ($_REQUEST["userid"] != "0") {
					$sql = 'update sys_users set ';
					//$sql .= 'login = "'.$_POST["login"].'", ';
					$sql .= 'email = "'.$_POST["email"].'", ';
					$sql .= 'groupid ='.$_POST["groupid"].', ';
					if (strlen($_POST["pwd3"]) > 0 && $_POST["pwd3"] == $_POST["pwd4"])
						$sql .= "passwordmd5 = '".$_POST["pwd3"]."', ";
					$sql .= 'name = "'.$_POST["name"].'" ';
					$sql .= 'where userid = '.$_REQUEST["userid"];
		
				} else {
					if (strlen($_POST["pwd3"]) > 0 && $_POST["pwd3"] == $_POST["pwd4"]) {
						$sql = "insert into sys_users (login,email,name,passwordmd5,loginmd5) values (";
						$sql .= "'".$_POST["login"]."',";
						$sql .= "'".$_POST["email"]."',";
						$sql .= "'".$_POST["name"]."',";
						$sql .= "'".$_POST["pwd3"]."',";
						$sql .= "md5('".$_POST["pwd3"]."'))";
					} else {
						$sql = "insert into sys_users (login,email,name,loginmd5) values (";
						$sql .= "'".$_POST["login"]."',";
						$sql .= "'".$_POST["email"]."',";
						$sql .= "'".$_POST["name"]."',";
						$sql .= "md5('".$_POST["pwd3"]."'))";
					}
				}
				$dummy = doSQL($db, $sql);
			} else {
				$dummy["code"]="-1";
				echo "<br><b>Die eingebenen Passwörter stimmen nicht überein</b><br>";
			}
		}
			
      		mlog("Benutzerverwaltung: Ändern des Profiles von: ".$_REQUEST["userid"]);
      		if ($dummy["code"] == 0)
      		{
           		echo 'Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A>';
           		echo '<SCRIPT TYPE="text/javascript">';
                		echo 'opener.location.reload();';
                		echo 'setTimeout("window.close()",1000);';
           		echo '</SCRIPT>';
      		}
      		else
      		{
          		echo 'Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:window.history.back();">Hier klicken, um zurückzukehren</A>';
      		}


		break;




	}


}
else
	echo 'Entweder fehlt Ihnen die Berechtigung, diese Seite anzuzeigen, oder die Seite wurde nicht korrekt aufgerufen. Bitte wenden Sie sich an einen Administrator!';

closeConnect($db);
?>
</BODY>
</HTML>