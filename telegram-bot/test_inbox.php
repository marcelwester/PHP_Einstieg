<?php

$MESSAGETEXT="Dies ist 
		eine Testmeldung
		ьber 
		mehrere Zeilen inlusive jeder Menge Umlaut дцья ƒ÷№я und einer Leerzeile 
		
		!";
$MESSAGEID=4444;

$url="http://127.0.0.1/dev/telegram-bot/inbox.php";

// Initialize cURL
$curl = curl_init();
// Set url
curl_setopt($curl,CURLOPT_URL, $url);
// Set POST Fields
curl_setopt($curl,CURLOPT_POSTFIELDS, "message=".base64_encode($MESSAGETEXT)."&errorid=".$MESSAGEID);

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//execute the post
$result = curl_exec($curl);
echo $result."\n";

//close the connection
curl_close($curl);



?>