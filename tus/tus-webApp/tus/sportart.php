<?php
sitename("sportart.php",$_SESSION["groupid"]);
switch($_REQUEST["action"]) {
	
	case 'show':
		$sportartid=$_REQUEST["sportart"];
		
		//&& (in_array($sportartid, $_SESSION["sportarten"])))
		if (priv("sportart") && priv_sportart($sportartid))
		{
                echo '<CENTER><TABLE WIDTH="40%" BORDER="0">';
                        echo '<TR>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<a href="index.php?site=sportart&action=edit&sportart='.$sportartid.'&PHPSESSID='.session_id().'">';
                                        echo '<IMG SRC="images/edit.jpg" BORDER="0" ALT="neu">';
                                        echo '</a>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#FFFFFF">';
                                        echo '<a href="index.php?site=sportart&action=edit&sportart='.$sportartid.'&PHPSESSID='.session_id().'">';
                                        echo '<B>Seite editieren</B>';
                                        echo '</a>';
                                echo '</TD>';
                        echo '</TR>';
                echo '</TABLE></CENTER><BR>';
		}
		
		$sqlstr = "select descr from sys_sportart where id = $sportartid and show_menu = 1";
		$result=GetResult($db,$sqlstr);
	   if (isset($result)) {
			echo image_replace($result["0"]["descr"]);
		
			$sqlstr="select name,email from sys_user_tus,sys_users where 
			         sportartid=$sportartid and sys_user_tus.userid=sys_users.userid";
			$result=GetResult($db,$sqlstr);
		}
		
		if (isset($result))
		{
			echo "<br>";
			echo "<br><b>Die Inhalte dieser Seite werden gepflegt von:</b>";
			foreach ($result as $user) 
			{
				echo "<br>";
				echo  '<a><A HREF="mailto:'.$user["email"].'">'.$user["name"].'</a>';
			}
		}
	
	break;

	case 'edit':
		$sportartid=$_REQUEST["sportart"];
		if (priv("sportart") && priv_sportart($sportartid))
		{
			$sql = "select name,descr from sys_sportart where id =$sportartid"; 
			$result= GetResult($db,$sql);
			echo '<FORM NAME="news" METHOD="POST" ACTION="index.php?site=sportart&action=save&PHPSESSID='.session_id().'">';
			echo '<TABLE WIDTH="90%" BORDER="0">';
				echo '<TR BGCOLOR="#DDDDDD">';
					echo '<TD COLSPAN="2" ALIGN="LEFT" BGCOLOR="#DDDDDD">';
						echo '<B>Hier können Sie die Seite für '.$result["0"]["name"].' ändern.</B>';
					echo '</TD>';
				echo '</TR>';
				$sqlstr="select image_id from sys_images where kategorie=7 and linked=0";
				$result1=GetResult($db,$sqlstr);
				if (isset($result1))
				{
					echo '<TR><TD COLSPAN="2" ALIGN="LEFT">';
					foreach ($result1 as $freeid) 
					{
				    		echo "<#";
				    		echo $freeid["image_id"];
				    		echo "#>";
				    		echo "&nbsp;&nbsp;";
					}
				}
				echo '<TR>';
				echo '<TD ALIGN="CENTER" CLASS="none">';
						echo '<TEXTAREA ROWS="20" COLS="100" NAME="descr">';
						echo $result[0]["descr"];
						echo '</TEXTAREA>';
					echo '</TD>';
				echo '</TR>';
				echo '<TR>';
					echo '<TD ALIGN="CENTER" COLSPAN="2" BGCOLOR="#DDDDDD">';
						echo '<INPUT TYPE="HIDDEN" NAME="sportart" VALUE="'.$sportartid.'" />';
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
	   $sportartid=$_POST["sportart"];
		if (priv("sportart") && priv_sportart($sportartid))
		{
		    //$state = addslashes($_POST["descr"]);
		    $state=$_POST["descr"];
			$sql = "update sys_sportart set descr ='".$state."' where id=".$sportartid;
			$result = doSQL($db, $sql);
		



		if ($result["code"] == 0)
               	{
               		# Imageid's für Bilderverwaltung suchen"
			$images=image_parse($_POST["descr"]);
						
			
			// Alle gelinkten Bilder zunächst freigeben
			$sqlstr="update sys_images set linked=0 where kategorie=7 and linked=$sportartid";
			$result1=doSQL($db,$sqlstr);
			
			$sqlstr="select image_id from sys_images where kategorie=7 and linked=0";
			$result1=GetResult($db,$sqlstr);
			if (isset($result1))
			{
				$freeimages=array();
				foreach ($result1 as $freeid) 
				{
					array_push($freeimages,$freeid["image_id"]);	
				}
			}
			$exit=0;
			
			
			if ($images["count"] > 0) 
			{
				unset($images["count"]);
				foreach ($images as $imageid) 
				{
					if  (! in_array($imageid,$freeimages))
					{
						echo "<br>Sie verwenden ein Bild, welches nicht zur Verfügung steht: ".$imageid;
						$exit=1;
       		  			}					
				}
			}
			
			if ($exit==1) 
			{
				echo '<br><A HREF="javascript:window.history.back();">Hier klicken, um zurückzukehren</A>';  
	         		closeConnect($db);
				exit;
			}
			
			if ($result1["code"] == 0)
			{
				foreach ($images as $imageid)
				{
					$sqlstr="update sys_images set linked=$sportartid where image_id=$imageid";
					$result1=doSQL($db,$sqlstr);
				}
			}

		}
			if ($result["code"] == 0)
			{
				mlog("Eine Sportartenseite wurde gespeichert: ".$sportartid);
				echo '<A HREF="index.php?site=sportart&action=show&sportart='.$sportartid.'&PHPSESSID='.session_id().'">Seite erfolgreich geändert!</A>';
				echo '<SCRIPT TYPE="text/javascript">';
					echo 'setTimeout("window.location=\'index.php?site=sportart&sportart='.$sportartid.'&action=show&PHPSESSID'.session_id().'\'",1000);';
				echo '</SCRIPT>';
			}
			else
			{
				echo '<A HREF="index.php?site=sportart&action=show&sportart='.$sportartid.'&PHPSESSID='.session_id().'">Seite konnte nicht erfolgreich geändert werden!</A>';
			}
		}
		else
			echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';
	break;
}



?>