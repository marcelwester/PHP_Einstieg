<?php
function almMessage($MESSAGEID,$MESSAGETEXT,$MESSAGETYPE="ALM",$MONITORID=1) {
        global $sysval,$rs_alm;

        if ($MESSAGETYPE=="TELEGRAM") {
                // Send Message to Telegramserver
        	    echo "\nsend Telegrammessage ...\n";
        	
                $errorid=$MESSAGEID;
                $msg=

                $url=$sysval->get("telegram_url");
                echo "\ntelegram_url: ".$url."\n";
                //$url="http://127.0.0.1/dev/telegram-bot/inbox.php";

                // Lesen der zu benachrichtigenden User
                $sql="select mobil chatid,name,vorname from sys_users where disable=0 and valid_to>sysdate() and userid>1 and userid in
                       (select userid from sys_user_group u,sys_monitor_group m,sys_groups g where
                		   m.groupid=u.groupid and
                		   m.groupid=g.groupid and 
                		   g.disable=0 and
                		  m.monitorid=?)";
                $rs_alm->prepare($sql);
                $rs_alm->bindColumn(1, $MONITORID,PDO::PARAM_INT);
                
                
                
                if ($rs_alm->execute()) {
                	while ($row=$rs_alm->fetchRow()) {
                  			if (intval($row["chatid"])!=0) {
                                	    $CHATID=intval($row["chatid"]);
                                        // Initialize cURL
                                        $curl = curl_init();
                                        // Set url
                                        curl_setopt($curl,CURLOPT_URL, $url);
                                        // Set POST Fields
                                        curl_setopt($curl,CURLOPT_POSTFIELDS, "errorid=".$MESSAGEID."&chatid=".$CHATID."&message=".base64_encode($MESSAGETEXT));
                                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                        //execute the post
                                        $result = curl_exec($curl);
                                        echo date("d.m.Y - H:i:s")."    ";
                                        echo "chatid: ".$CHATID." - ";
                                        echo $row["vorname"]." ".$row["name"]." - ";
                                        echo $result."\n";
                                        //close the connection
                                        curl_close($curl);
                                } else {
                                        echo "Invalid chatid: ".$chatid."\n";
                                }
                        }
                }
                
        }
        
        if ($MESSAGETYPE=="ALM") {
        	echo "\nsend ALMmessage ...\n";
        
        	$filename=date('U')."_".$MESSAGEID.".xml";
        	$file=$sysval->get("tmppath")."/".$filename;
        	$message="
                <message>
                <message_id></message_id>
                <system></system>
                <service></service>
                <sub_service></sub_service>
                <exchange_id>".$MESSAGEID."</exchange_id>
                <value>FAIL</value>
                <time>".date('Y-m-d H:i:s')."</time>
                <comment>".$MESSAGEID." ".$MESSAGETEXT." </comment>
                <authentication>0</authentication>
                <authentication_code></authentication_code>
                </message>
                ";
        
        	if (file_put_contents($file, $message) ) {
        		if (rename($sysval->get("tmppath")."/".$filename,$sysval->get("alertpath")."/".$filename)) {
        			return 0;
        		} else {
        			echo "Fehler beim umbenennen der Datei: ".$sysval->get("tmppath")."/".$filename." => ".$sysval->get("alertpath")."/".$filename;
        
        		}
        	} else {
        		echo "Fehler beim Anlegen der Datei";
        	}
        }

        if ($MESSAGETYPE=="MAIL") {
        	echo "\nsend Mail ...\n";
        	
        	$mail_header  ='MIME-Version: 1.0' . "\n";
        	//$mail_header .="Content-type: text/plain; charset=iso-8859-1\n";
        	$mail_header .="Content-type: text/plain; charset=utf8\n";
        	$mail_header .='From: "'.$sysval->get("title").'" <'.$sysval->get("mail_sender").'>' . "\n";
        	        	 
        	
        	// Lesen der zu benachrichtigenden User
            $sql="select email,name,vorname from sys_users where disable=0 and valid_to>sysdate() and userid>1 and userid in
                  (select userid from sys_user_group u,sys_monitor_group m,sys_groups g where
            		   m.groupid=u.groupid and
              		   m.groupid=g.groupid and 
             		   g.disable=0 and
               		   m.monitorid=?)";
        	$rs_alm->prepare($sql);
        	$rs_alm->bindColumn(1, $MONITORID,PDO::PARAM_INT);

        	if ($rs_alm->execute()) {
        		while ($row=$rs_alm->fetchRow()) {
        		   echo "Sending email to ".$row["email"]."\n";
        		   mail($row["email"],"Alert",utf8_encode($MESSAGETEXT."\n".$sysval->get("mail_txt")),$mail_header);
        		}
        	}
        
        }
        
        
        
      }
?>
