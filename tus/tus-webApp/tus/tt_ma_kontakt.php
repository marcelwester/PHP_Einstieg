<?php


//// tt_ma_kontakt.php
//// letzte Änderung: Volker 01.05.2004
//// Erstellung
//// 

sitename("tt_ma_kontakt.php",$_SESSION["groupid"]);
$url="index.php?site=tt_team&action=kontakt&teamid=$teamid&saisonid=$saisonid&PHPSESSID=".session_id();
if (! isset($_GET["action1"]))
{
	$action1="show";
}
else
{
	$action1=$_GET["action1"];
}

switch ($action1)
{
case show:
	
        if (priv("kontakt") && priv_tt_team($teamid))
	{
           echo '<CENTER><TABLE WIDTH="40%" BORDER="0">';
                        echo '<TR>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<a href="$url&action1=edit">';
                                        echo '<IMG SRC="images/edit.jpg" BORDER="0" ALT="neu">';
                                        echo '</a>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<a href="'.$url.'&action1=edit">';
                                        echo '<B>Seite editieren</B>';
                                        echo '</a>';
                                echo '</TD>';
                        echo '</TR>';
                echo '</TABLE></CENTER><BR>';
	
        }
	$sqlstr="select kontakt from tt_tus_mannschaft where id=$teamid";
      	$result=GetResult($db,$sqlstr);
      	if (isset($result["0"]["kontakt"]))
      		echo $result["0"]["kontakt"];
      	
break;

case edit:
		if (priv("kontakt") && priv_tt_team($teamid))
		{
		$sqlstr="select kontakt from tt_tus_mannschaft where id=$teamid";
			$result=GetResult($db,$sqlstr);
		echo '<FORM NAME="news" METHOD="POST" ACTION="'.$url.'&action1=save">';
			echo '<TABLE WIDTH="90%" BORDER="0">';
				echo '<TR BGCOLOR="#DDDDDD">';
					echo '<TD COLSPAN="2" ALIGN="LEFT" BGCOLOR="#DDDDDD">';
						echo '<B>Kontakt editieren</B>';
					echo '</TD>';
				echo '</TR>';
				echo '<TR>';
				echo '<TD ALIGN="CENTER" CLASS="none">';
						echo '<TEXTAREA ROWS="20" COLS="100" NAME="kontakt">';
						if (isset($result[0]["kontakt"]))
							echo $result[0]["kontakt"];
						echo '</TEXTAREA>';
					echo '</TD>';
				echo '</TR>';
				echo '<TR>';
					echo '<TD ALIGN="CENTER" COLSPAN="2" BGCOLOR="#DDDDDD">';
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
	if (priv("kontakt") && priv_tt_team($teamid))
	{
	    
	    //$state = addcslashes($_POST["kontakt"],'",\'');
		$state = $_POST["kontakt"];
		$sql = "update tt_tus_mannschaft set kontakt ='".$state."' where id=".$teamid;
		$result = doSQL($db, $sql);
		
		if ($result["code"] == 0)
		{
			mlog("Tischtennis: Kontaktseite einer Mannschaft wurde geändert: ".$teamid);
			echo '<A HREF="'.$url.'">Seite erfolgreich geändert!</A>';
			echo '<SCRIPT TYPE="text/javascript">';
				echo 'setTimeout("window.location=\''.$url.'\'",1000);';
			echo '</SCRIPT>';
		}
		else
			echo '<A HREF="$url">Seite konnte nicht erfolgreich geändert werden!</A>';
		
	}
	else
		echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
break;


}
?>