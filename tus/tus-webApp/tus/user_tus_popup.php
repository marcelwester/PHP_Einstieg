<?php
include "inc.php";
sitename("user_tus_popup.php",$_SESSION["groupid"]);
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
			$sql = 'select * from sys_users where userid = '.$_REQUEST["userid"];
			$result = GetResult($db, $sql);

			$sql = 'select userid,fb_teamid,tt_teamid,sportartid,rightid from sys_user_tus';
			$result1 = getResult($db, $sql);


			echo '<FORM NAME="user_tus" METHOD="POST" ACTION="user_tus_popup.php?action=save&userid='.$_REQUEST["userid"].'&PHPSESSID='.session_id().'">';
			echo '<CENTER><TABLE WIDTH="90%" BORDER="0">';
				echo '<TR>';
					echo '<TD COLSPAN="3" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
						echo 'Benutzer : <b>'.$result[0]["login"].'</b> ('.$result[0]["name"].')';
					echo '</TD>';
				echo '</TR>';

				echo '<TR>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
						echo '<B>Rechte</B>';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
						echo '<B>Beschreibung</B>';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
						echo '<B>X</B>';
					echo '</TD>';
				echo '</TR>';

				$sql = 'select id,name,descr,kategorie from sys_rights order by kategorie,name';
				$result = getResult($db, $sql);

		            if (isset($result)) 
				foreach ($result as $tus)
				{
					
					if ($kategorie_memory!=$tus["kategorie"]) {
						$kategorie_memory=$tus["kategorie"];
						echo '<tr><td colspan=3>';
							echo '<b>'.$tus["kategorie"].'</b>';
						echo '</tr></td>';
					}
					echo '<TR>';
						echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
							echo $tus["name"];
						echo '</TD>';
						echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
							echo $tus["descr"];
						echo '</TD>';
						echo '</TD>';
						echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
							$set = 0;
							if (isset($result1))
								foreach($result1 as $zwtab)
								{
									if ($zwtab["userid"] == $_REQUEST["userid"] && $zwtab["rightid"] == $tus["id"])
									{
										echo '<INPUT TYPE="CHECKBOX" NAME="right_'.$tus["id"].'" CHECKED />';
										$set = 1;
									}
								}
							if ($set == 0)
								echo '<INPUT TYPE="CHECKBOX" NAME="right_'.$tus["id"].'" />';
						echo '</TD>';
					echo '</TR>';
				}




				echo '<TR>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
						echo '<B>Fusball Mannschaft</B>';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
						echo '<B>X</B>';
					echo '</TD>';
				echo '</TR>';

				$sql = 'select * from fb_tus_mannschaft order by name asc';
				$result = getResult($db, $sql);


		            if (isset($result)) 
				foreach ($result as $tus)
				{
					echo '<TR>';
						echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
							echo $tus["name"];
						echo '</TD>';
						echo '</TD>';
						echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
							$set = 0;
							if (isset($result1))
								foreach($result1 as $zwtab)
								{
									if ($zwtab["userid"] == $_REQUEST["userid"] && $zwtab["fb_teamid"] == $tus["id"])
									{
										echo '<INPUT TYPE="CHECKBOX" NAME="fb_'.$tus["id"].'" CHECKED />';
										$set = 1;
									}
								}
							if ($set == 0)
								echo '<INPUT TYPE="CHECKBOX" NAME="fb_'.$tus["id"].'" />';
						echo '</TD>';
					echo '</TR>';
				}

				echo '<TR>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
						echo '<B>Tischtennis Mannschaft</B>';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
						echo '<B>X</B>';
					echo '</TD>';
				echo '</TR>';

				$sql = 'select * from tt_tus_mannschaft order by name asc';
				$result = getResult($db, $sql);


		            if (isset($result)) 
				foreach ($result as $tus)
				{
					echo '<TR>';
						echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
							echo $tus["name"];
						echo '</TD>';
						echo '</TD>';
						echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
							$set = 0;
							if (isset($result1))
								foreach($result1 as $zwtab)
								{
									if ($zwtab["userid"] == $_REQUEST["userid"] && $zwtab["tt_teamid"] == $tus["id"])
									{
										echo '<INPUT TYPE="CHECKBOX" NAME="tt_'.$tus["id"].'" CHECKED />';
										$set = 1;
									}
								}
							if ($set == 0)
								echo '<INPUT TYPE="CHECKBOX" NAME="tt_'.$tus["id"].'" />';
						echo '</TD>';
					echo '</TR>';
				}





				echo '<br><br>';
				echo '<TR>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
						echo '<B>Sportart</B>';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
						echo '<B>X</B>';
					echo '</TD>';
				echo '</TR>';

				$sql = 'select id,name from sys_sportart order by name asc';
				$result = getResult($db, $sql);

				$sql = 'select * from sys_user_tus';
				$result1 = getResult($db, $sql);
			if (isset($result))
				foreach ($result as $sport)
				{
					echo '<TR>';
						echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
							echo $sport["name"];
						echo '</TD>';
						echo '</TD>';
						echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
							$set = 0;
							if (isset($result1))
								foreach($result1 as $zwtab)
								{
									if ($zwtab["userid"] == $_REQUEST["userid"] && $zwtab["sportartid"] == $sport["id"])
									{
										echo '<INPUT TYPE="CHECKBOX" NAME="sp_chk_'.$sport["id"].'" CHECKED />';
										$set = 1;
									}
								}
							if ($set == 0)
								echo '<INPUT TYPE="CHECKBOX" NAME="sp_chk_'.$sport["id"].'" />';
						echo '</TD>';
					echo '</TR>';
				}



				echo '<TR>';
					echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo '<INPUT TYPE="BUTTON" VALUE="Abbrechen" onClick="window.close();" />';
						echo ' <INPUT TYPE="SUBMIT" VALUE="Speichern" />';
					echo '</TD>';
				echo '</TR>';
			echo '</TABLE></CENTER>';
			echo '</FORM>';

			break;
		case 'save':
			$sql = 'delete from sys_user_tus where userid = '.$_REQUEST["userid"];
			$result = doSQL($db, $sql);

			// Mannschaftszuordnung (Fussball)
			$sql = 'select * from fb_tus_mannschaft order by name asc';
			$result = getResult($db, $sql);
			foreach ($result as $tus)
			{
				if (isset($_POST["fb_".$tus["id"]]))
				{
					$sql = 'insert into sys_user_tus (userid, fb_teamid) values ('.$_REQUEST["userid"].', '.$tus["id"].')';
					$res = doSQL($db, $sql);
				}
			}

			// Mannschaftszuordnung (Fussball)
			$sql = 'select * from tt_tus_mannschaft order by name asc';
			$result = getResult($db, $sql);
			
			foreach ($result as $tus)
			{
				if (isset($_POST["tt_".$tus["id"]]))
				{
					$sql = 'insert into sys_user_tus (userid, tt_teamid) values ('.$_REQUEST["userid"].', '.$tus["id"].')';
					$res = doSQL($db, $sql);
				}
			}

			// Sportartzuordnung 
			$sql = 'select id,name from sys_sportart order by name asc';
			$result = getResult($db, $sql);
			foreach ($result as $sport)
			{
				if (isset($_POST["sp_chk_".$sport["id"]]))
				{
			                 
					$sql = 'insert into sys_user_tus (userid, sportartid) values ('.$_REQUEST["userid"].', '.$sport["id"].')';
					$res = doSQL($db, $sql);
				}
			}

			// Rechtezurodnung 
			$sql = 'select id,name from sys_rights order by name asc';
			$result = getResult($db, $sql);
			foreach ($result as $right)
			{
				if (isset($_POST["right_".$right["id"]]))
				{
					$sql = 'insert into sys_user_tus (userid, rightid) values ('.$_REQUEST["userid"].', '.$right["id"].')';
					$res = doSQL($db, $sql);
				}
			}
			
			
			
			mlog("Benutzerverwaltung: Speichern von Benutzerrechten für: ".$_REQUEST["userid"]);
			echo '<CENTER><BR><BR><A HREF="javascript:window.close()">Erfolgreich gespeichert !<br>Dieses Fenster schließt sich automatisch</A>';
			echo '<SCRIPT TYPE="text/javascript">';
				echo 'setTimeout("window.close()",1000);';
			echo '</SCRIPT></CENTER>';
			break;
	}
	closeConnect($db);
}
else
	echo 'Entweder fehlt Ihnen die Berechtigung, diese Seite anzuzeigen, oder die Seite wurde nicht korrekt aufgerufen. Bitte wenden Sie sich an einen Administrator!';

?>
</BODY>
</HTML>