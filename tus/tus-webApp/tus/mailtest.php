<?

$xMessage="Irgendwer";
$checkday="24.12.2001";
$person["name"]="NAME";
$person["vorname"]="vorname";


  $eol="\r\n";
  $mime_boundary=md5(time());
 
  # Common Headers
  $headers .= 'From: MyName<'.$fromaddress.'>'.$eol;
  $headers .= 'Reply-To: MyName<'.$fromaddress.'>'.$eol;
  $headers .= 'Return-Path: MyName<'.$fromaddress.'>'.$eol;    // these two to set reply address
  $headers .= "Message-ID: <".$now." TheSystem@".$_SERVER['SERVER_NAME'].">".$eol;
  $headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters

  # Boundry for marking the split & Multitype Headers
  $headers .= 'MIME-Version: 1.0'.$eol;
  $headers .= "Content-Type: multipart/related; boundary=\"".$mime_boundary."\"".$eol;



	$subject = 'www.tus-ocholt.de - Geburtstags Erinnerung';
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "From: www.tus-ocholt.de<webmaster@tus-ocholt.de>\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$message = $xMessage." am ".$checkday.". hat ".$person["vorname"].' '.$person["name"]." Geburtstag !";
					
	$adr="vlosch@gmx.net";
	mail($adr, $subject, $message, $headers);
	echo "sending mail";
?>

	