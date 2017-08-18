<?php
//// upload.php
//// letzte Änderung : Volker, 11.05.2005
//// was : Erstellung
////

include "inc.php";
sitename("upload.php",$_SESSION["groupid"]);
focus();
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<SCRIPT LANGUAGE="JavaScript">
<!--
        function viewImage(imageid)
        {
              imgId = imageid;
              window.open("showimage.php?id="+imgId,"viewImage","width=650, height=550, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
        }
-->
</SCRIPT>
<?php
if (priv("image_upload") && priv("spiele_edit"))
{
$spielid=$_REQUEST["spielid"];
$kat=$_REQUEST["kat"];
$userid=$_SESSION["userid"];
$result=GetResult($db,"select name from sys_users where userid=$userid");
$username=$result["0"]["name"];

switch ($_REQUEST["action"])
{
 case 'start':
     $sqlstr ="select saison_id,datum,heim_id,aus_id  from fb_spiele where id=$spielid";
     $result = GetResult($db, $sqlstr);
     $datum=$result["0"]["datum"];
	 $heimid=$result["0"]["heim_id"];
	 $ausid=$result["0"]["aus_id"];
	 $saisonid=$result["0"]["saison_id"];

     // Spielzeit lesen
     $sqlstr ="select liga,spielzeit from fb_saison where id=$saisonid";
     $result = GetResult($db, $sqlstr);
     $liga=$result[0]["liga"];
     $spielzeit=$result[0]["spielzeit"];

     $sqlstr = "select name from fb_mannschaft where id=".$heimid;
     $result = getResult($db,$sqlstr);
     $heim = $result["0"]["name"];

     $sqlstr = "select name from fb_mannschaft where id=".$ausid;
     $result = getResult($db,$sqlstr);
     $aus = $result["0"]["name"];

	  
     echo '<TABLE WIDTH="100%" BORDER="0" >';
     echo '<TR><TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
     echo '<B>Bilder hochladen</B><BR>'.$liga.' - '.$spielzeit.'<BR> - '.$username.' - ';
     echo '<br><br>';
     echo '<b>'.$datum.'&nbsp;&nbsp;&nbsp;'.$heim.' - '.$aus.'</b>';
     echo '<br><br>';
     echo '</TD></TR>';
     echo '</TABLE>';

     echo '<FORM METHOD="POST" ENCTYPE="multipart/form-data" ACTION="upload.php?action=upload&spielid='.$spielid.'&kat='.$kat.'&PHPSESSID='.session_id().'">';
			echo '<TABLE WIDTH="100%" BORDER="0">';

			echo '<TR>';
				echo '<TD ALIGN="center" BGCOLOR="#FFFFFF">';
					echo '<B>Bildbeschreibung</B>';
					echo '<br>';
					echo '<INPUT TYPE="TEXT" SIZE="63" NAME="descr" ID="descr" VALUE="'.$heim.' - '.$aus.', '.$datum.'" />';
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
	//echo "Anzahl: ".$anzahl;
	$error=0;
	for ($i=0; $i < $anzahl; $i++) {
		$binfile=$_FILES["filename".$i]["tmp_name"];
    	if (isset($binfile) && fileSize($binfile) > 0) {
			$data = addslashes(fread(fopen($binfile, "r"), filesize($binfile)));
			$sql = 'INSERT INTO sys_images (descr,linked, kategorie,datum,userid,size) VALUES (';
			$sql .= "'$descr',$spielid,$kat,sysdate(),";
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
	 	mlog("Fussball: Es wurden Fotos zu einem Spiel hochgeladen: ". $spielid);
	    echo '<br><A onMouseOver="this.style.cursor='.$hand.';" onClick="window.history.go(-2);">Bilder erfolgreich hochgeladen !</A>';
		echo '<SCRIPT TYPE="text/javascript">';
//			echo 'setTimeout("window.history.go(-2);",1000);';
        	echo 'opener.location.reload();';
            echo 'setTimeout("window.close()",1000);';
		echo '</SCRIPT>';
	} else {
		echo "<br>Fehler beim Upload ...";
		echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.history.back();">Es ist ein Fehler beim Kopieren des Bildes aufgetreten !</A>';
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