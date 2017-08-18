<?php
//// mailverteiler.php
//// letzte Änderung: Volker 08.07.2004
//// Änderung: Erstellung
//// 
sitename("mailverteiler.php",$_SESSION["groupid"]);

if  (priv("email")) {
	$action = $_REQUEST["action"];

    
	switch ($action)
	{
		case 'start':
   	        	$sqlstr='select userid,name,email from sys_users where disable=0 order by name';
   	        	$result=GetResult($db,$sqlstr);
   	        	//print_r($result);
   	        
                	echo '<FORM NAME="MAIL" METHOD="POST" ACTION="index.php?site=mailverteiler&action=send&PHPSESSID='.session_id().'">';
   	           	echo '<TABLE WIDTH="70%" BORDER="0">';
                        echo '<TR>';
                                echo '<TD COLSPAN="2" ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                        echo '<b>Email versenden an</b>';
                                echo '</TD>';
                        echo '</TR>';
                	echo '<TR>';
                		echo '<TD ALIGN="CENTER">';
                			echo 'Empfängeradressen für jeden sichtbar:';
                		echo '</TD>';
                		echo '<TD ALIGN="CENTER">';
                        	        echo '<INPUT TYPE="CHECKBOX" NAME="cc" checked />';
                                echo '</TD></TR>';
                        echo '</TR>';
                 	echo '</TABLE>';
			echo '<br>';

 			echo '<TABLE WIDTH="70%" BORDER="0">';
                        echo '<TR>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                        echo '<b>Name</b>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                        echo '<b>email</b>';
                                echo '</TD>';
                                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                                        echo '<b>X</b>';
                                echo '</TD>';
                        echo '</TR>';
                	if (isset($result)) {
                		foreach ($result as $row) {
                			echo '<TR><TD ALIGN="CENTER">';
                                        	echo $row["name"];
                                	echo '</TD>';
                                	echo '<TD ALIGN="CENTER">';
	                                        echo $row["email"];
        	                        echo '</TD>';
                	                echo '<TD ALIGN="CENTER">';
                        	                echo '<INPUT TYPE="CHECKBOX" NAME="mail_'.$row["userid"].'" checked />';
                                	echo '</TD></TR>';
                         	}
                	}
			echo '<TR>';
			echo '<TD COLSPAN="3" ALIGN="CENTER" BGCOLOR="#FFFFFF">';
				echo '<INPUT TYPE="BUTTON" VALUE="Abbrechen" onClick="window.close();" />';
				echo '&nbsp;&nbsp;&nbsp;&nbsp;';
				echo '<INPUT TYPE="SUBMIT" VALUE="Mail" DEFAULT />';
  				echo '</TD>';
			echo '</TR>';

                	echo '</TABLE><BR>';
			echo '</FORM>';


	break;
		
	case "send":
		$sqlstr='select userid,name,email from sys_users  where disable=0 order by name';
        $result=GetResult($db,$sqlstr);

		mlog("Der Mailverteiler wurde aufgerufen");
		$cc=$_POST["cc"];
		foreach ($result as $row) {
			if ($row["userid"]==$_SESSION["userid"]) {
				$email=$row["email"];
			}
		}

		echo '<br>Falls Sie nicht automatisch zu Ihrem email Programm weitergeleitet werden, klicken Sie auf den Link:<br><br>';
		$url='mailto:'.$email.'?subject=Mail vom TuS';
		foreach ($result as $row) {
			if (isset($_REQUEST["mail_".$row["userid"]])) {
				if (isset($cc)) 
					$url=$url.'&cc='.$row["email"];
				else
					$url=$url.'&bcc='.$row["email"];
				
			}
		}
		
		echo '<a><A HREF="'.$url.'">Senden</a>';
		echo '<SCRIPT TYPE="text/javascript">';
			echo 'url="'.$url.'";';
			echo 'setTimeout("window.open(url)",1000);';
		echo '</SCRIPT></CENTER>';				  
		
	break;
	}
	
	
} else {
	echo '<br>'.$no_rights;	
}
?>