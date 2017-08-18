<?php

////
//// artikel_popup.php
////
//// letzte Änderung : Volker, 31.03.2004 
//// was : Erstellung
////

include "inc.php";
?>

<HTML>
<HEAD>
<TITLE>w w w . t u s - o c h o l t . d e - Artikeleditor</TITLE>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">

<?php

if (priv("artikel"))
{
sitename("artikel_popup.php",$_SESSION["groupid"]);
switch ($_REQUEST["action"])
{
	case 'edit':
	    $artikelid=$_REQUEST["artikelid"];
	    $sqlstr="select id,show_artikel,images,content,name,descr,verfasser,toc from sys_artikel where id=$artikelid";
	    $result=GetResult($db,$sqlstr);	
	    $result=$result["0"];
	    
 
	    
	    echo '<FORM METHOD="POST" ACTION="artikel_popup.php?action=save&PHPSESSID='.session_id().'">';
                echo '<TABLE WIDTH="100%" BORDER="0">';
                        echo '<TR>';
                                echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                        echo '<B>Artikel editieren</B>';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Name<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="name" VALUE="'.$result["name"].'" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Beschreibung<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="descr" VALUE="'.$result["descr"].'" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Datum<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="12" NAME="datum" VALUE="'.date("d.m.Y", strtotime($result["toc"])).'" /> (bitte im Format TT.MM.JJJJ angeben !)';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Artikel Anzeigen<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        //ja_nein
					$quest["0"]["name"] = "NEIN"; $quest["0"]["id"] = "0"; $quest["1"]["name"] = "JA"; $quest["1"]["id"] = "1";
					build_select($quest,name,id,show_artikel,"",1,$result["show_artikel"]);
                                echo '</TD>';
                        echo '</TR>';
                       	echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Verwendete Artikelbilder<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                 	echo "- ".$result["images"]." -";
                                 	$images=$images." ".$result["images"];
                                echo '</TD>';
                        echo '</TR>';
                       	echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Freie Artikelbilder<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                 	$sqlstr="select image_id from sys_images where linked=0 and kategorie=6";
                                 	$result1=GetResult($db,$sqlstr);
                                 	echo "- ";
                                 	if (isset($result1))
                                 	{
                                 		foreach ($result1 as $row1)
                                 		{
                                 			echo "<#".$row1["image_id"]."#> ";
                                 		}
                                 	}
                                 	echo " -";
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Verfasser<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="verfasser" VALUE="'.$result["verfasser"].'" />';
                                echo '</TD>';
                        echo '</TR>';
                       
                       	echo '<TR>';
				echo '<TD COLSPAN="2" ALIGN="CENTER" CLASS="none">';
					echo '<TEXTAREA ROWS="17" COLS="70" NAME="content">';
					echo $result["content"];
					echo '</TEXTAREA>';
				echo '</TD>';
			echo '</TR>';
                        echo '<TR>';
				echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#FFFFFF" COLSPAN="2">';
					echo '<INPUT TYPE="hidden" NAME="artikelid" VALUE="'.$artikelid.'">';
					echo '<INPUT TYPE="hidden" NAME="images" VALUE="'.$images.'">';
					echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
					echo '&nbsp;&nbsp;<INPUT TYPE="submit" VALUE="Speichern">';
				echo '</TD>';
			echo '</TR>';
                echo '</TABLE>';
                echo '</FORM>';
	break;
	
	case 'add';
	    $userid=$_SESSION["userid"];
	    $sqlstr="select name from sys_users where userid=$userid";
	    $result1=GetResult($db,$sqlstr);
	    $verfasser=$result1["0"]["name"];

	      echo '<FORM METHOD="POST" ACTION="artikel_popup.php?action=save&PHPSESSID='.session_id().'">';
                echo '<TABLE WIDTH="100%" BORDER="0">';
                        echo '<TR>';
                                echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                        echo '<B>Neuen Artikel verfassen</B>';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Name<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="name" VALUE="" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Beschreibung<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="descr" VALUE="" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Datum<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="12" NAME="datum" VALUE="'.date("d.m.Y").'" /> (bitte im Format TT.MM.JJJJ angeben !)';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Verfasser<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<INPUT TYPE="TEXT" SIZE="50" NAME="verfasser" VALUE="'.$verfasser.'" />';
                                echo '</TD>';
                        echo '</TR>';
                        echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Artikel Anzeigen<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        //ja_nein
					$quest["0"]["name"] = "NEIN"; $quest["0"]["id"] = "0"; $quest["1"]["name"] = "JA"; $quest["1"]["id"] = "1";
					build_select($quest,name,id,show_artikel,"",1,"1");
                                echo '</TD>';
                        echo '</TR>';
                       	echo '<TR>';
                                echo '<TD ALIGN="LEFT" BGCOLOR="#FFFFFF">';
                                        echo '<B>Freie Artikelbilder<B>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                 	$sqlstr="select image_id from sys_images where linked=0 and kategorie=6";
                                 	$result1=GetResult($db,$sqlstr);
                                 	
                                 	if (isset($result1))
                                 	{
                                 		foreach ($result1 as $row1)
                                 		{
                                 			echo "<#".$row1["image_id"]."#> ";
                                 		}
                                 	}
                                echo '</TD>';
                        echo '</TR>';
                       	echo '<TR>';
				echo '<TD COLSPAN="2" ALIGN="CENTER" CLASS="none">';
					echo '<TEXTAREA ROWS="17" COLS="70" NAME="content">';
					echo '</TEXTAREA>';
				echo '</TD>';
			echo '</TR>';
                        echo '<TR>';
				echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#FFFFFF" COLSPAN="2">';
					echo '<INPUT TYPE="button" VALUE="Abbrechen" onClick="window.close();">';
					echo '&nbsp;&nbsp;<INPUT TYPE="submit" VALUE="Speichern">';
				echo '</TD>';
			echo '</TR>';
                echo '</TABLE>';
                echo '</FORM>';
          break;
          
          case 'save':
               	$name=$_POST["name"];
          	$descr=$_POST["descr"];
          	$datum=$_POST["datum"];
          	$show_artikel=$_POST["show_artikel"];
          	$content=$_POST["content"];
          	$oldimages=$_POST["images"];
          	$verfasser=$_POST["verfasser"];
          	
     		// Namen und Datum müssen ausgefüllt sein
     		if (($name=="") || ($datum=="") || ($descr=="") )
     		{
			echo 'Name, Datum, Beschreibung ausfüllen.<br><A HREF="javascript:window.history.back();">Hier klicken, um zurückzukehren</A>';
     			closeConnect($db);
     			exit;
     		}
     		          	
     		// Datumsformat prüfen:
     		$datum = ts2db($datum);
     		if (($datum == "-1")) 
		{       echo 'Falsches Datumsformat.<br><A HREF="javascript:window.history.back();">Hier klicken, um zurückzukehren</A>';  
         		closeConnect($db);
         		exit;
      		}
      		
               	# Imageid's für Bilderverwaltung suchen"
		$images=image_parse($_POST["content"]);
		if ($images["count"] >> 0) 
		{
			unset($images["count"]);
			foreach ($images as $imageid) {
				$im = $im." ".$imageid;
			}
		}
          
           	          		
          	//$content=addslashes($content);
          	
          	
          	if (! isset($_POST["artikelid"])) 
          	{
               		if (!isset($images["count"]))
               		{
               			$sqlstr="select image_id from sys_images where kategorie=6 and linked=0";
				$result1=GetResult($db,$sqlstr);
				$freeimages=array();
				foreach ($result1 as $freeid) 
				{
					array_push($freeimages,$freeid["image_id"]);	
				}	
		     		
		     		$exit=0;
				foreach ($images as $imageid) 
				{
					if  (! in_array($imageid,$freeimages))
					{
						echo "<br>Sie verwenden ein Bild, welches nicht zur Verfügung steht: ".$imageid;
						$exit=1;
         				}					
				}
				if ($exit==1) 
				{
					echo '<br><A HREF="javascript:window.history.back();">Hier klicken, um zurückzukehren</A>';  
         				closeConnect($db);
					exit;
				}
			
			}
               		$sqlstr = "insert into sys_artikel (show_artikel,images,content,name,descr,toc,verfasser) values
          			  ($show_artikel,'$im','$content','$name','$descr','$datum','$verfasser')";
          		$result=doSQL($db,$sqlstr);
		
			if ($result["code"]==0) 
			{
				foreach ($images as $imageid)
				{
					$sqlstr="update sys_images set linked=1 where image_id=$imageid";
					$result1=doSQL($db,$sqlstr);
				}
			}

		}
		else
		{
	
			// Alte Bilder
			$oldimages=explode(" ",trim($oldimages));
			$exit=0;
					
			// Bilderverwaltung prüfen, nur wenn image vorhanden sind, siehe oben unset($image["count"])
			if (! isset($images["count"])) 
			{
				$sqlstr="select image_id from sys_images where kategorie=6 and linked=0";
				$result1=GetResult($db,$sqlstr);
				$freeimages=array();
			     if (isset($result1))
				foreach ($result1 as $freeid) 
				{
					array_push($freeimages,$freeid["image_id"]);	
				}	
		
				foreach ($images as $imageid) 
				{
					
					if ((! in_array($imageid,$oldimages)) && (! in_array($imageid,$freeimages)))
					{
						echo "<br>Sie verwenden ein Bild, welches nicht zur Verfügung steht: ".$imageid;
						$exit=1;
         				}				
				}		
				
				if ($exit==1) 
				{
					echo '<br><A HREF="javascript:window.history.back();">Hier klicken, um zurückzukehren</A>';  
         				closeConnect($db);
					exit;
				}
			 }
			
			
			
			
			$sqlstr = "update sys_artikel set 
				show_artikel = $show_artikel,
				images='$im',
				content='$content',
				name='$name',
				descr='$descr',
				verfasser='$verfasser',
				toc='$datum' where id=".$_POST["artikelid"];
			$result=doSQL($db,$sqlstr);

			// Bilderverwaltung freigeben
			if ($result["code"]==0) 
			{
			      if (isset($oldimages))
				foreach ($oldimages as $imageid) {
					$sqlstr="update sys_images set linked=0 where image_id=$imageid";
					$result1=doSQL($db,$sqlstr);
				}
			}

			// Bilderverwaltung zuordnen
			if ($result["code"]==0) 
			{
			     if (isset($images))
				foreach ($images as $imageid)
				{
					$sqlstr="update sys_images set linked=1 where image_id=$imageid";
					$result1=doSQL($db,$sqlstr);
				}
			}
	
		}	
		
		
    		if ($result["code"] == 0)
      		{
           		mlog("Speichern eines Artikels: ".$_POST["artikelid"]);
           		echo 'Die Aktion wurde erfolgreich ausgeführt.<br><A HREF="javascript:opener.location.reload(); window.close();">Hier klicken, um das Fenster zu schließen</A>';
           		echo '<SCRIPT TYPE="text/javascript">';
                		echo 'opener.location.reload();';
                		echo 'setTimeout("window.close()",1000);';
           		echo '</SCRIPT>';
      		}

      		else
           		echo 'Die Aktion konnte nicht erfolgreich ausgeführt werden.<br><A HREF="javascript:window.history.back();">Hier klicken, um zurückzukehren</A>';
  
          	
          break;
}
}
else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';

closeConnect($db);
?>
</BODY>
</HTML>