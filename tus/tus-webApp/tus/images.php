<?php

if (priv("fbvimages"))
{
sitename("images.php",$_SESSION["groupid"]);
//// images.php
//// Änderung : Volker, 01.07.2007 
//// was : - Bug behoben. Hochladen von Dateien ging nur mit REGISTER_GLOBALS_ON
////
//// Änderung : Volker, 11.03.2007 
//// was : - Löschen von mehreren Bildern auf einmal möglich 
////
//// Änderung : Volker, 28.04.2005 
//// was : - Limitierung der Anzeige auf 15 Bilder  
////
//// Änderung : Volker, 23.07.2004 
//// was : - Anzeige der Geometrie 
////
//// Änderung : Volker, 21.07.2004 
//// was : - Kopieren von Bildern
////
//// Änderung : Volker, 21.02.2004 
//// was : - Darstellung der Sponsoren mit + für Großes Logo und * für Aktuell gültig.
////
//// Änderung : Volker, 18.02.2004 15:55
//// was : - Es können zu kleinen Sponsoren Bildern grosse Logos zugelinked werden
////	   - Aufruf von image_popup.php aus SponsorenBildern der Kategorie 4
////	
//// Änderung : Daniel, 14.02.2004 15:55
//// was : - Nur nicht zugeordnete Bilder löschbar
////
//// Änderung : Daniel, 10.02.2004 19:12
//// was : - Kategorien eingebaut

switch($action)
{
	case 'start':
?>
<script language="JavaScript">
<!--

function popup(imageid)
{
                var url;
        <?php
           echo 'url = "images_popup.php?action=edit&imageid="+imageid+"&PHPSESSID='.session_id().'";';
        ?>

        window.open(url,"spiel","width=550, height=450, top=200, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
}

function popup_images_upload(kat)
{
                var url;
        <?php
           echo 'url = "images_upload_popup.php?action=start&kat="+kat+"&PHPSESSID='.session_id().'";';
        ?>

        window.open(url,"upload","width=650, height=600, top=100, left=200, menubar=no, directories=no, resizeable=yes, toolbar=no, scrollbars=yes, status=no");
}

function enableDelete(chk_id, del_id, emp_id)
{
	var elementChk = document.getElementById(chk_id);
	var elementDel = document.getElementById(del_id);
	var elementEmp = document.getElementById(emp_id);

	if (elementChk.checked == true)
	{
		elementEmp.style.display = 'none';
		elementDel.style.display = 'block';
	}
	else
	{
		elementDel.style.display = 'none';
		elementEmp.style.display = 'block';
	}
}

function Delete()
{



}

-->
</script>
<?php

		if (isset($_REQUEST["kat"]))
			$akt_kategorie = $_REQUEST["kat"];
		else
			$akt_kategorie = 1;

		$sql_kat = 'select id,name,geometry from sys_images_kat order by idx';
		$result_kat = getResult($db,$sql_kat);
		
		$kategorie = array();
		foreach ($result_kat as $kat)
		{
			$kategorie[$kat["id"]]["id"] = $kat["id"];
			$kategorie[$kat["id"]]["name"] = $kat["name"];
			$kategorie[$kat["id"]]["link"] = 'index.php?site=images&action=start&kat='.$kat["id"].'&PHPSSESSID='.session_id();
			if ($kat["id"]==$akt_kategorie) {
				$geometry=$kat["geometry"];
			}
		}

		echo '<TABLE WIDTH="100%" BORDER="0">';
			echo '<TR>';
				foreach($kategorie as $kat_row)
				{
				        if ($kat_row["id"] == $akt_kategorie)
				        {
				                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
				                        echo '<B>'.$kat_row["name"].'</B>';
				                echo '</TD>';
				        }
				        else
				        {
				                echo '<TD ALIGN="CENTER" BGCOLOR="'.$adm_link.'" onMouseOver="this.style.backgroundColor=\''.$adm_link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$adm_link.'\';" onClick="location.href=\''.$kat_row["link"].'\'">';
				                        echo $kat_row["name"];
				                echo '</TD>';
				        }
				}
			echo '</TR>';
		echo '</TABLE><BR>';

      // Anzeigelimit
      if (isset($_REQUEST["limit"])) {
      	$limit=$_REQUEST["limit"];
			if ($limit=="0") {
				$limit_sql="";
			} else {
				$limit_sql="limit ".$limit;
			}  
      } else {
      	$limit=15;
      	$limit_sql="limit ".$limit;
      }


		if (priv("image_upload") || priv("images")) {
echo '<a href="javascript:popup_images_upload('.$akt_kategorie.');">Mehrere Bilder auf <br>einmal hochladen</a><br><br>';
			echo '<FORM METHOD="POST" ENCTYPE="multipart/form-data" ACTION="index.php?site=images&action=upload&PHPSESSID='.session_id().'">';
			echo '<TABLE WIDTH="100%" BORDER="0">';
			echo '<TR>';
				echo '<TD COLSPAN="3" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Neues Bild hochladen</B>';
					echo '<br>Grösse: '.$geometry.'';
				echo '</TD>';
			echo '</TR>';

			echo '<TR>';
				echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
					echo '<B>Bildbeschreibung<B>';
					echo '<br><br>';
					echo '<B>Datei auswählen<B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="TEXT" SIZE="63" NAME="descr" ID="descr" VALUE="" />';
					echo '<INPUT TYPE="HIDDEN" NAME="hidden_kat" VALUE="'.$akt_kategorie.'" />';
					echo '<INPUT TYPE="HIDDEN" NAME="limit" VALUE="'.$limit.'" />';
					echo '<br>';
					echo '<INPUT TYPE="FILE" NAME="filename" />';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
					echo '<INPUT TYPE="submit" VALUE="Hochladen">';
				echo '</TD>';
			echo '</TR>';		
			echo '</TABLE>';
			echo '</FORM>';
		
		}

       

		$sql1 = 'select image_id,kategorie,descr,linked,DATE_FORMAT(datum,\'%d.%m.%Y\') datumf,name,sys_images.userid,size from sys_images,sys_users where sys_users.userid=sys_images.userid and linked = 0 and kategorie = '.$akt_kategorie.' order by datum desc,descr '.$limit_sql;
		$result1 = getResult($db,$sql1);

		$sql2 = 'select image_id,kategorie,descr,linked,DATE_FORMAT(datum,\'%d.%m.%Y\') datumf,name,sys_images.userid,size from sys_images,sys_users where sys_users.userid=sys_images.userid and linked != 0 and kategorie = '.$akt_kategorie.' order by datum desc,descr '.$limit_sql;
		$result2 = getResult($db,$sql2);

		if ($limit=="0") {
				echo '<input type="button" onclick="self.location.href=\'index.php?site=images&action=start&kat='.$akt_kategorie.'&limit=15\'" value="Maximal 15 Bilder anzeigen">';
		} else {
			echo '<input type="button" onclick="self.location.href=\'index.php?site=images&action=start&kat='.$akt_kategorie.'&limit=0\'" value="Alle Bilder anzeigen">';
		}
		echo '<br><br>';
		
		
		echo '<TABLE WIDTH="100%" BORDER="0">';
			echo '<TR>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>ID</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Bildbeschreibung</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Kopieren</B>';
				echo '</TD>';
				
				echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Grösse</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Datum</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Benutzer</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>zugeordnet ?</B>';
				echo '</TD>';
				echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
					echo '<B>Kategorie</B>';
				echo '</TD>';
				if (priv("image_del") || priv("images")) {
					echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD" COLSPAN="2">';
						echo '<B>löschen</B>';
					echo '</TD>';
				}
			echo '</TR>';

		if (isset($result1[0]))
		{
			echo '<form name="Delete" method="post" action="index.php?site=images&action=delete&id='.$row["image_id"].'&PHPSESSID='.session_id().'">';
			foreach ($result1 as $row)
			{
				echo '<TR>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo $row["image_id"];
						echo '&nbsp;<A HREF="showimage.php?id='.$row["image_id"].'" TARGET="blank"><IMG SRC="images/view.jpg" HEIGHT="16" WIDTH="16" BORDER="0" ALT="Bild ansehen"></A>';
						echo '&nbsp;&nbsp;';
						echo '<a href="javascript:popup('.$row["image_id"].');">';
						echo '<IMG SRC="images/edit.jpg" BORDER="0" ALT="Bearbeiten">';
						echo '</a>';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo $row["descr"];
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo "-";
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo round($row["size"]/1000,0).'k';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo $row["datumf"];
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo $row["name"];
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo 'nein';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo $kategorie[$row["kategorie"]]["name"];
					echo '</TD>';
           	                        
                       if (priv("images") || ($_SESSION["userid"]==$row["userid"] && priv("image_del"))) {
							echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
								echo '<INPUT TYPE="CHECKBOX" NAME="del[]" VALUE="'.$row["image_id"].'"  ID="chk'.$row["image_id"].'" onClick="enableDelete(this.id,\'del'.$row["image_id"].'\',\'emp'.$row["image_id"].'\')" />';
							echo '</TD>';
							echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
								echo '<DIV ID="emp'.$row["image_id"].'" STYLE="display:block;">';
									echo '<IMG SRC="images/empty.gif" BORDER="0" HEIGHT="16" WIDTH="16">';
								echo '</DIV>';
								echo '<DIV ID="del'.$row["image_id"].'" STYLE="display:none;">';
									#echo '<A HREF="index.php?site=images&action=del&id='.$row["image_id"].'&PHPSESSID='.session_id().'">';
									echo '<a href="javascript:document.Delete.submit();">';
									
									echo '<IMG SRC="images/del.gif" BORDER="0" HEIGHT="16" WIDTH="16">';
									echo '</A>';
								echo '</DIV>';
							echo '</TD>';
					   }
				echo '</TR>';
			}
			echo '</form>';
		}

		echo '<TR>';
			echo '<TD COLSPAN="8" ALIGN="CENTER" BGCOLOR="#FFFFFF">';
				echo '&nbsp;';
			echo '</TD>';
		echo '</TR>';

		if (isset($result2[0]))
		{
			foreach ($result2 as $row)
			{
				echo '<TR>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo $row["image_id"];
						echo '&nbsp;<A HREF="showimage.php?id='.$row["image_id"].'" TARGET="blank"><IMG SRC="images/view.jpg" HEIGHT="16" WIDTH="16" BORDER="0" ALT="Bild ansehen"></A>';
						echo '&nbsp;&nbsp;';
						echo '<a href="javascript:popup('.$row["image_id"].');">';
						echo '<IMG SRC="images/edit.jpg" BORDER="0" ALT="Bearbeiten">';
						echo '</a>';

					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo $row["descr"];
					echo '</TD>';
					
					if (priv("image_copy")||priv("images")) {
						table_link("Kopieren","index.php?site=images&action=copy&kat=".$row["kategorie"]."&id=".$row["image_id"]."&PHPSESSID=".session_id());
					} else {
						echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
							echo '-';
						echo '</TD>';
					}
					
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo round($row["size"]/1000,0).'k';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo $row["datumf"];
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo $row["name"];
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo 'ja';
					echo '</TD>';
					echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
						echo $kategorie[$row["kategorie"]]["name"];
					echo '</TD>';
					echo '<TD ALIGN="CENTER" COLSPAN="2" BGCOLOR="#FFFFFF">';
						echo '&nbsp;';
					echo '</TD>';
				echo '</TR>';
			}
		}
		echo '</TABLE><BR><BR>';
	break;

	case 'copy':
		if (priv("image_copy") || priv("images")) {
			echo "<br>Bild wird kopiert...<br>";
			$kat = $_REQUEST["kat"];
			$id = $_REQUEST["id"];
			
			$sqlstr = "insert into sys_images (kategorie,descr,datum,userid,linked,size) ";
			$sqlstr .= "select kategorie,descr,datum,userid,0,size from sys_images where image_id=".$id;
			$result=doSQL($db,$sqlstr);
			$result1=getResult($db,"select last_insert_id() last");
			$newid=$result1["0"]["last"];
			$sqlstr  ="insert into sys_images_blob (image_id,bin_data) ";
			$sqlstr .="select ".$newid.",bin_data from sys_images_blob where image_id=".$id;
			$result2=doSQL($db,$sqlstr);
/*
			$sqlstr="select kategorie,bin_data,descr,datum,userid,size from sys_images where image_id=".$id;
			$result=GetResult($db,$sqlstr);
			if (isset($result)) {
				$sqlstr = "insert into sys_images (kategorie,bin_data,descr,datum,userid,size) values (";
				$sqlstr .= $kat.",";
				$sqlstr .= "'".addslashes($result["0"]["bin_data"])."',";
				$sqlstr .= "'".$result["0"]["descr"]."',";
				$sqlstr .= "'".$result["0"]["datum"]."',";
				$sqlstr .= $result["0"]["userid"].",";
				$sqlstr .= $result["0"]["size"].")";
				$result=doSQL($db,$sqlstr);
*/				
				if ($result["code"]=="0" && $result2["code"]=="0") {
   				        echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.history.back();">Das Bild wurde erfolgreich kopiert !</A>';
					echo '<SCRIPT TYPE="text/javascript">';
					echo 'setTimeout("window.history.back();",1000);';
					echo '</SCRIPT>';
				} else {
					echo "<br>Fehler beim Kopieren ...";
					echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.history.back();">Es ist ein Fehler beim Kopieren des Bildes aufgetreten !</A>';
				}
//			}

		} else {
			echo $no_rights;
		}
	
	break;


	case 'upload':
		if (priv("image_upload") || priv("images")) {
 			$filename=$_POST["filename"];
			$binFile = $_FILES["filename"]["tmp_name"];
			$txtDescription = $_POST["descr"];
			$hidden_kat = $_POST["hidden_kat"];
			if (isset($binFile))
			{
				$data = addslashes(fread(fopen($binFile, "r"), filesize($binFile)));
				$strDescription = addslashes(nl2br($txtDescription));
	
				$sql = 'INSERT INTO sys_images (descr, linked, kategorie,datum,userid,size) VALUES (';
				$sql .= "'$strDescription',0,'$hidden_kat',sysdate(),";
				$sql .=$_SESSION["userid"].",".filesize($binFile);
				$sql .=")";
				$result = doSQL($db, $sql);

				$sqlstr  = "insert into sys_images_blob (image_id,bin_data) values (";
				$sqlstr .= "last_insert_id(),";
				$sqlstr .= "'$data')";
				$result1 = doSQL($db, $sqlstr);
				


				if ($result["code"] == 0 && $result1["code"] == 0)
				{
					mlog("Ein Bild wurde hochgeladen. Bilder-Kategorie: ".$hidden_kat);
					echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.history.back();">Das Bild wurde erfolgreich hochgeladen !</A>';
					echo '<SCRIPT TYPE="text/javascript">';
					echo 'setTimeout("window.history.back();",1000);';
					echo '</SCRIPT>';
				}
				else
				{
					echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.location.href=\'index.php?site=images&action=start&'.session_id().'\';">Es ist ein Fehler beim Hochladen des Bildes aufgetreten !</A>';
					//print_r($result);
					//echo '<br>'.$sql;
				}
			} else {
				echo "Error upload File";
			}
		} else {
			echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
		}
		
	break;
	case 'del':
		$imageid = $_REQUEST["id"];
		$linked = $_REQUEST["linked"];
		
		$result=GetResult($db,"select userid from sys_images where image_id=$imageid");
		
		
		if (priv("images") || ($_SESSION["userid"]==$result["0"]["userid"] && priv("image_del"))) {
			$sql = 'DELETE from sys_images where image_id = '.$imageid;
			$result = doSQL($db, $sql);
			$sql = 'DELETE from sys_images_blob where image_id = '.$imageid;
			$result1 = doSQL($db, $sql);
			
			mlog("Bilderverwaltung: Image mit ID ".$imageid." wurde gelöscht.");
			if ($result["code"] == 0 && $result1["code"] == 0 )
			{
				echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.location.href=\'index.php?site=images&action=start&'.session_id().'\';">Das Bild wurde erfolgreich entfernt !</A>';
				echo '<SCRIPT TYPE="text/javascript">';
					echo 'setTimeout("window.history.back();",1000);';
				echo '</SCRIPT>';
			}
			else
				echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.location.href=\'index.php?site=images&action=start&'.session_id().'\';">Es ist ein Fehler beim Löschen des Bildes aufgetreten !</A>';
		}
		else
		{
			echo "<br> Sie haben keine Berechtigung dieses Bild zu löschen !<br>";
			echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.location.href=\'index.php?site=images&action=start&'.session_id().'\';">Es ist ein Fehler beim Löschen des Bildes aufgetreten !</A>';
		}
		break;
		
		
		case 'delete':
			$deleteID= $_POST["del"];
			$error=0;	
			//$linked = $_REQUEST["linked"];
	
			foreach ($deleteID as $imageid) {
				$result=GetResult($db,"select userid from sys_images where image_id=$imageid");
				if (priv("images") || ($_SESSION["userid"]==$result["0"]["userid"] && priv("image_del"))) {
					$sql = 'DELETE from sys_images where linked=0 and image_id = '.$imageid;
					$result = doSQL($db, $sql);
					$sql = 'DELETE from sys_images_blob where image_id = '.$imageid;
					$result1 = doSQL($db, $sql);
			
					echo "<br>Löschen von Imageid: ".$imageid;
					if ($result["code"] == 0 && $result1["code"] == 0 )
					{
						mlog("Bilderverwaltung: Image mit ID ".$imageid." wurde gelöscht.");
						echo " OK";
					}
					else
						echo 'Es ist ein Fehler beim Löschen des Bildes aufgetreten !';
						$error="1";
				}
				else
				{
					echo 'Sie haben keine Berechtigung dieses Bild zu löschen !';
					$error="1";
				}
			}

			if ($error != "0")
			{
				echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.location.href=\'index.php?site=images&action=start&'.session_id().'\';"><br><br>Bild(er) wurden erfolgreich entfernt !</A>';
				echo '<SCRIPT TYPE="text/javascript">';
					echo 'setTimeout("window.history.back();",1000);';
				echo '</SCRIPT>';
			}
			else
				echo '<A onMouseOver="this.style.cursor='.$hand.';" onClick="window.location.href=\'index.php?site=images&action=start&'.session_id().'\';"><br><br>Es ist ein Fehler beim Löschen des Bildes aufgetreten !</A>';

		break;

}
}
else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
?>