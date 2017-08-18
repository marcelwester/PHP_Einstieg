<?php

////
//// fill_sys_keys.php
////

include "inc.php";
sitename("fbv_person_popup.php",$_SESSION["groupid"]);
if (priv("sys_keys"))
{

?>

<HTML>
<HEAD>
<?php
  echo '<TITLE>w w w . t u s - o c h o l t . d e </TITLE>';
?>
</HEAD>
<BODY>
<LINK REL="stylesheet" TYPE="text/css" HREF="style.css">


<?php
focus();
 
   // Füllen der Keytabelle mit Zufallswerten
   function generate_pw($laenge = 5, $klasse = 1) {
    $pw = "";
    for ($i = 0; $i < $laenge; $i++) {
	    switch (rand(0, $klasse)) {
	      case 0:
	        $pw .= chr(mt_rand(97, 122));     //Kleinbuchstaben
	      break;
	      case 1:
	        $pw .= chr(mt_rand(65, 90));      //Großbuchstaben
	      break;
	      case 2:
	        $pw .= chr(mt_rand(48, 57));      //Zahlen
	      break;
	   }
    }
    return $pw;
  }


for ($i=0; $i<10000; $i++) {
	generate_pw(32,2);
	$sqlstr="insert into sys_keys (val) values ('".generate_pw(32,2)."')";
	doSql($db,$sqlstr);
}
echo "Fertig";

}
else
	echo 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';


closeConnect($db);
?>
</BODY>
</HTML>