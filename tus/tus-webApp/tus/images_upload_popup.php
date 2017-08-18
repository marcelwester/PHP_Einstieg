<?php
//// upload.php
//// letzte Änderung : Volker, 11.05.2005
//// was : Erstellung
////

include "inc.php";
sitename("images_upload_popup.php",$_SESSION["groupid"]);

focus();
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">


<?php
if (priv("image_upload") )
{
$kat=$_REQUEST["kat"];
$userid=$_SESSION["userid"];
$result=GetResult($db,"select name from sys_users where userid=$userid");
$username=$result["0"]["name"];




switch ($_REQUEST["action"])
{
 case 'start':
	 $sql = "select name from sys_images_kat where id=".$kat;
	 $kat_name=getResult($db,$sql);
	 $kat_name=$kat_name[0]["name"];
     echo '<TABLE WIDTH="100%" BORDER="0" >';
     echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
     echo '<B>Bilder hochladen</B><BR><b>'.$kat_name.'</b><br> - '.$username.' - ';
     echo '<br><br>';
     echo '<br><br>';
     echo '</TD></TR>';
     echo '</TABLE>';

     echo '<FORM METHOD="POST" ENCTYPE="multipart/form-data" ACTION="images_upload_popup.php?action=upload&spielid='.$spielid.'&kat='.$kat.'&PHPSESSID='.session_id().'">';
			echo '<TABLE WIDTH="100%" BORDER="0">';

			echo '<TR>';
				echo '<TD ALIGN="center" BGCOLOR="#FFFFFF">';
					echo '<B>Bildbeschreibung</B>';
					echo '<br>';
					echo '<INPUT TYPE="TEXT" SIZE="63" NAME="descr" ID="descr" VALUE="" />';
					$sqlstr = 'select geometry from sys_images_kat where id = '.$kat;
					$result = getResult($db,$sqlstr);
					if (isset($result)) {
						echo '<br><b>Größe:</b> '.$result["0"]["geometry"];
					}
				echo '</TD>';
			echo '</TR>';
			echo '<TR>';
				echo '<TD align="center">';
					for ($i=0; $i<15; $i++) {
						echo '<br><INPUT TYPE="FILE" SIZE="70" NAME="filename'.$i.'" id="filename'.$i.'" VALUE="" />';
					}
				echo '</TD>';
			echo '</tr>';
			echo '<tr>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<input type="hidden" name="anzahl" value="'.$i.'">';
					echo '<INPUT TYPE="submit" VALUE="Hochladen">';
				echo '</TD>';
			echo '</TR>';		
			echo '</TABLE>';
		echo '</FORM>';
 break;

 case 'upload':
	$descr=$_POST["descr"];
	$anzahl=$_POST["anzahl"];
	$error=0;
	
	for ($i=0; $i < $anzahl; $i++) {
		$binfile=$_FILES["filename".$i]["tmp_name"];
    	if (isset($binfile) && fileSize($binfile) > 0) {
			$data = addslashes(fread(fopen($binfile, "r"), filesize($binfile)));
			$sql = 'INSERT INTO sys_images (descr, linked, kategorie,datum,userid,size) VALUES (';
			$sql .= "'$descr',0,$kat,sysdate(),";
			$sql .=$_SESSION["userid"].",".filesize($binfile);
			$sql .=")";
			$result = doSQL($db, $sql);
			
			$sqlstr  = "insert into sys_images_blob (image_id,bin_data) values (";
			$sqlstr .= "last_insert_id(),";
			$sqlstr .= "'$data')";
			$result1 = doSQL($db, $sqlstr);			
			
			echo '<br>'.$_FILES["filename".$i]["name"];
			if ($result["code"]==0 && $result1["code"] == 0) {
				echo ' Datei erfolgreich hochgeladen';
			} else {
				echo ' Fehler ';
				$error=1;
			}
		}	 	
	}

	if ($error=="0") {
	 	mlog("Bilderverwaltung: Es wurden $anzahl Fotos zu Kategorie $kat hochgeladen. ");
	    echo '<br><A onMouseOver="this.style.cursor='.$hand.';" onClick="window.history.go(-2);">Bilder erfolgreich hochgeladen !</A>';
		echo '<SCRIPT TYPE="text/javascript">';
        	echo 'opener.location.reload();';
            echo 'setTimeout("window.close()",1000);';
		echo '</SCRIPT>';
	} else {
		echo "<br>Fehler beim Kopieren ...";
		echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.history.back();">Es ist ein Fehler beim Kopieren der Bilder aufgetreten !</A>';
	}
 break;
}
closeConnect($db);
}
else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
?>
</BODY>
</HTML>