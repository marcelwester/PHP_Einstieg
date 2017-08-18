<?php



//// inc.php
//// letzte Änderung : Daniel, 16.03.2004 21:28
//// was : Rechte aus DB lesen



session_start();

///////////////
// DB CONNECT
///////////////

include "db_mysql.php";
$dbhost = 'localhost';
$dbname = 'tus';
$dbuser = 'root';
$dbpass = '';

if ($dbpass != NULL)
	$db = doConnect($dbhost,$dbuser,$dbpass);
else
	$db = doConnect($dbhost,$dbuser);
if ($db["code"] == 0)
{
        $db = $db["db"];
        $dummy = selectDB($dbname);
        if ($dummy["code"] == 0)
        {
                NULL;
        }
        else 
        {
                echo '<h1>Datenbank Auswahl - Fehler !</h1>';
        	exit;
        }
}
else
{
        echo '<h1>Datenbank Verbindung - Fehler !</h1>';
        echo '<h2>Seite ist überlastet, versuchen Sie es später nochmal...</h2>';
        exit;
}


$SESSIONTIMEOUT=1800;
if (isset($_SESSION["userid"])) {
	if (!sessionControl(session_id())) {
		unset($_SESSION);
		session_destroy();
	} else {
		$userid=$_SESSION["userid"];
		$groupid=$_SESSION["groupid"];
	}
} else {
	unset($groupid);
	unset($userid);
}
	

$DEBUG=1;
$DISPLAY_ERRORS=0;
$BIRTHDAYCHECK=0;
$LOGVISIT=1;

//1: Ausgabe von $result bei doSQL
$SQLDEBUG=0;

// Mauszeiger Firefox/ Mozilla und IE
if (stristr($_SERVER["HTTP_USER_AGENT"],"GECKO") || stristr($_SERVER["HTTP_USER_AGENT"],"FIREFOX")) {
	$hand="'pointer'";
} else {
	$hand="'hand'";
}


// Fehlerausgabe unterdrücken
if ($DISPLAY_ERRORS==1) {
	ini_set('display_errors','On');
} else {
	ini_set('display_errors','Off');
}

// Farben
$ROT="#FF0000";
$GRUEN="#00FF00";
$GELB="#FFFF00";
$BLAU="#0000FF";

# Pastell Farben für Forum
$yellow="#FFFF80";
$green="#B7FF6F";
$white="#FFFFFF";


////////////////////
/// RECHTEVERWALTUNG
////////////////////

// Group id für die Admin
$admin_id = 10; 

// Fehlermeldung falls man keine Rechte für eine Seite besitzt
$no_rights = 'Ihnen fehlt die Berechtigung, diese Seite anzuzeigen. Bitte wenden Sie sich an einen Administrator!';

// Berechtigungen aus der DB lesen

// Berechtigungen für den angemeldeten Benutzer aus DB lesen
// Admins bekommen automatisch alle Rechte

if ($DEBUG==1) {
	unset($_SESSION["userprivs"]);
	unset($_SESSION["mannschaftenid"]);
	unset($_SESSION["sportarten"]);
	unset($_SESSION["tt_mannschaftenid"]);
}

if (! isset($_SESSION["birthday_check"])) {
	if ($BIRTHDAYCHECK == "1") {
	    birthday_check();
    }
	$_SESSION["birthday_check"]="1";
}


if (! isset($_SESSION["userprivs"]) && (isset($userid))) {
	$sql='select name from sys_user_tus,sys_rights where userid='.$userid.' and rightid<>0 and rightid=id';
	$result = getResult($db, $sql);

	$_SESSION["userprivs"]=array();
	if (isset($result)) {
		foreach ($result as $row) {
			array_push($_SESSION["userprivs"],$row["name"]);
		}
	}
}


// TUSTeam Berechtigungen (Fusbball)
if (! isset($_SESSION["mannschaftenid"]) && (isset($userid))) {
	$_SESSION["mannschaftenid"] = array();
	$sql = 'select fb_teamid from sys_user_tus where fb_teamid<>0 and userid = '.$_SESSION["userid"];
	$result = getResult($db, $sql);
	if (isset($result[0]))
	{
		foreach ($result as $tus)
		{
			array_push($_SESSION["mannschaftenid"],$tus["fb_teamid"]);
		}
	}

	# Abgeschlossene Saisons lesen
	$sql = "select id from fb_saison where closed=1";
	$result = getResult($db, $sql);
	$_SESSION["fb_saisonarray"] = array();
	if (isset($result))
	{
		
		foreach ($result as $row)
		{
			array_push($_SESSION["fb_saisonarray"],$row["id"]);
		}
	}		
}


// TUSTeam Berechtigungen (Tischtennis)
 if (! isset($_SESSION["tt_mannschaftenid"]) && (isset($userid))) {
	$_SESSION["tt_mannschaftenid"] = array();
	$sql = 'select tt_teamid from sys_user_tus where tt_teamid<>0 and userid = '.$_SESSION["userid"];
	$result = getResult($db, $sql);
	if (isset($result[0]))
	{
		foreach ($result as $tus)
		{
			array_push($_SESSION["tt_mannschaftenid"],$tus["tt_teamid"]);
		}
	}
	
	# Abgeschlossene Saisons lesen
	$sql = "select id from tt_saison where closed=1";
	$result = getResult($db, $sql);
	$_SESSION["tt_saisonarray"] = array();
	if (isset($result))
	{
		
		foreach ($result as $row)
		{
			array_push($_SESSION["tt_saisonarray"],$row["id"]);
		}
	}		
}


//Sportarten Berechtigungen
if (! isset($_SESSION["sportarten"]) && (isset($userid))) {
	$_SESSION["sportarten"] = array();
	$sql = 'select * from sys_user_tus where sportartid<>0 and userid = '.$_SESSION["userid"];
	$result = getResult($db, $sql);
	if (isset($result[0]))
	{
		foreach ($result as $sportart)
		{
			array_push($_SESSION["sportarten"],$sportart["sportartid"]);
		}
	}
}


// Allgemeine Berechtigung prüfen
function priv ($userpriv) {
	global $groupid,$admin_id;
	if (isset($_SESSION["userprivs"])) {
		// Administratoren duerfen alles
		if ($groupid==$admin_id) {
			return 1;
		}
		if (in_array($userpriv, $_SESSION["userprivs"])) {
			return 1;
		}
	}
}


// Berechtigungen für TuS-Mannschaften prüfen (Fussball)
function priv_team ($teamid) {
	global $groupid,$admin_id,$saisonid,$db;

	# Prüfen, ob die Saisonid schon geschlossen wurde
	if (isset($saisonid)) {
		if (in_array($saisonid, $_SESSION["fb_saisonarray"])) {return ;}
	}	

	if (isset($_SESSION["mannschaftenid"])) {
		// Administratoren duerfen alles
		if ($groupid==$admin_id) {return 1;}
		if (in_array($teamid, $_SESSION["mannschaftenid"])) {return 1;}
	}
}

// Berechtigungen für TuS-Mannschaften prüfen (Tischtennis)
function priv_tt_team ($teamid) {
	global $groupid,$admin_id,$saisonid;
	# Prüfen, ob die Saisonid schon geschlossen wurde

	if (isset($saisonid)) {
		if (in_array($saisonid, $_SESSION["tt_saisonarray"])) {return ;}
	}	

	if (isset($_SESSION["tt_mannschaftenid"])) {
		// Administratoren duerfen alles
		if ($groupid==$admin_id) {return 1;}
		if (in_array($teamid, $_SESSION["tt_mannschaftenid"])) {return 1;}
	}
}



// Berechtigungen für Sportarten prüfen
function priv_sportart ($sportartid) {
	global $groupid,$admin_id;
	if (isset($_SESSION["sportarten"])) {
		// Administratoren duerfen alles
		if ($groupid==$admin_id) {return 1;}
		if (in_array($sportartid, $_SESSION["sportarten"])) {return 1;}
	}
}





///////////////
// VARIABLEN
///////////////

// HTML Farben
$adm_link = '#FAC5C0';
$adm_link_over = '#F97769';
$link = '#DDDDDD';
$link_over = '#AAAAAA';

// Überschriften / Dateinamen
$contentHeaders = array(
 						"news"  => array(
                                            "file" => "news.php",
                                            "currentnews" => "Aktuelles",
                                            "edit" => "News bearbeiten",
                                            "save" => "News speichern"
                                        ),
                        "login" => array(
                                            "file" => "login.php",
                                            "login" => "Anmeldung",
                                            "check" => "Überprüfung der Anmeldedaten",
                                            "logout" => "Abmelden",
                                            "edituser" => "Benutzerprofil bearbeiten",
                                            "saveprofile" => "Benutzerprofil speichern",
                                            "request" => "Anmeldung beantragen",
                                            "saverequest" => "Antrag speichern"
                                        ),
                        "users" => array(
                                            "file" => "users.php",
                                            "start" => "Benutzerverwaltung",
                                            "user" => "Benutzer",
                                            "changeuser" => "Benutzer ändern",
                                            "authrequest" => "Antrag genehmigen",
                                            "delrequest" => "Antrag entfernen",
                                            "deluser" => "Benutzer löschen",
                                            "groups" => "Gruppen",
                                            "rights" => "Rechte"
                                        ),
                        "forum" => array(
                                            "file" => "forum.php",
                                            "start" => "Forum",
                                            "view" => "Beitrag ansehen",
                                            "write" => "Beitrag verfassen",
                                            "save" => "Beitrag speichern",
                                            "del" => "Beitrag entfernen"
                                       ),
                        "team" => array(
                                            "file" => "mannschaft.php",
                                            "start" => "Startseite Fussballmannschaft",
                                            "table" => "Tabelle",
                                            "spiele" => "Spielplan",
                                            "kader" => "Kader",
                                            "stats" => "Statistiken"
                                         ),
                        "tt_allgemein" => array(
                                            "file" => "tt_allgemein.php",
                                            "start" => "Tischtennis",
                                         ),
                        "tt_team" => array(
                                            "file" => "tt_mannschaft.php",
                                            "start" => "Startseite Tischtennis",
                                            "table" => "Tabelle",
                                            "spiele" => "Spielplan",
                                            "kader" => "Kader",
                                            "stats" => "Statistiken"
                                         ),
                        "match" => array(
                                            "file" => "spiele.php",
                                            "add" => "Spiel hinzufügen",
                                            "edit" => "Spiel bearbeiten",
                                            "save" => "Spiel speichern",
                                            "del" => "Kader"
                                         ),
                        "images" => array(
                                            "file" => "images.php",
                                            "start" => "Bilderverwaltung",
                                            "add" => "Bild hinzufügen",
                                            "upload" => "Bild hochladen",
                                            "del" => "Bild entfernen"
                                         ),
                        "fbverw" => array(
                                            "file" => "fbverw.php",
                                            "saison" => "Saisons verwalten",
                                            "person" => "Personen verwalten",
                                            "team" => "Mannschaften verwalten",
                                            "team_tus" => "TuS Mannschaften verwalten"
                                        ),
                        "ttverw" => array(
                                            "file" => "ttverw.php",
                                            "saison" => "Saisons verwalten",
                                            "person" => "Personen verwalten",
                                            "team" => "Mannschaften verwalten",
                                            "team_tus" => "TuS Mannschaften verwalten"
                                        ),
                        "spielstaette" => array(
                            		    "file" => "spielstaette.php",
 		                        ),
                        "sponsoren" => array(
                                             "file" => "sponsoren.php",
                                             "list" => "Sponsoren",
                                             "edit" => "Sponsoren verwalten"
                                            ),
								"sportart" => array(
                                             "file" => "sportart.php",
                                             "show" => "Angebot an Sportarten"
                                            ),
								"site" => array(
                                             "file" => "sys_site.php",
                                             "show" => "&nbsp;"
                                            ),
                        "fb_start" => array(
                                             "file" => "fb_start.php",
                                             "currentnews" => "Fussball"
                                            ),                                                    
                        "tt_start" => array(
                                             "file" => "tt_start.php",
                                             "currentnews" => "Tischtennis"
                                            ),                                                    
                        "artikel" => array(
                                             "file" => "artikel.php",
                                             "start" => "Artikel verfassen"
                                            ),                                            
                     
                        "guestbook" => array(
                                             "file" => "guestbook.php",
                                             "start" => "Gästebuch",
                                             "del" => "Eintrag löschen",
                                             "save" => "Eintrag speichern"
                                            ),
                        "counter" => array(
                                             "file" => "counter.php",
                                             "start" => "Counter"
                                            ),
                        "visitors" => array(
                                             "file" => "visitors.php",
                                             "start" => "Besucher-Statistik"
                                            ),
                        "mlog" => array(
						                     "file" => "messagelog.php",
						                     "start" => "Ereignisanzeige"
						                    ),
                        "mailverteiler" => array(
                                             "file" => "mailverteiler.php",
                                             "start" => "Mail"
                                            ),
                        "artikel_show" => array(
                                             "file" => "artikel_show.php",
                                             "list" => "Artikel"
                                            ),
                        "fanartikel" => array(
                                             "file" => "fanartikel.php",
                                             "list" => "Fanartikel"
                                            ),
                        "bauvorhaben" => array(
                                             "file" => "bauvorhaben.php",
                                             "list" => "Bauvorhaben"
                                            ),
                        "forumedit" => array(
                                             "file" => "forumedit.php",
                                             "list" => "Forum-Verwaltung"
                                            ),
                        "sportarten-verwaltung" => array(
                                             "file" => "sysv_sportarten.php",
                                             "list" => "Sportartenverwaltung"
                                            ),
                       "umfrage_fanartikel" => array(
                                             "file" => "umfrage_fanartikel.php",
                                             "list" => "Umfrage"
                                            )
                       
                        );

///////////////
// Funktionen
///////////////

function build_select($result, $field_show, $field_val, $name, $multiple = "", $size = 1, $std = "") {
  //echo $name;
  echo "<select name=\"$name\" size=$size $multiple>";
  foreach ($result as $row)
  {
   if ($std == $row[$field_val]) {
     echo "<option selected value=\"" . $row[$field_val] . "\">";
   } else {
     echo "<option value=\"" . $row[$field_val] . "\">";
   }
   echo $row[$field_show];
   echo "</option>\n";
  }
  echo "</select>";
}

function ts2db ($datum,$zeit = null)
{
	// Funktion zum Umwandeln des Zeitstempelformat vom Webformat ins DB Format
	// Datum
        if (strlen($datum) < 10)
        {
                $pieces = explode('.', $datum);
                if (strlen($pieces[0]) < 2)
                        $pieces[0] = '0'.$pieces[0];
                if (strlen($pieces[1]) < 2)
                        $pieces[1] = '0'.$pieces[1];
                if (strlen($pieces[2]) < 4)
                        if ($pieces[2] > 40)
                                $pieces[2] = '19'.$pieces[2];
                        else
                                $pieces[2] = '20'.$pieces[2];
                $datum = $pieces[0].'.'.$pieces[1].'.'.$pieces[2];
        }

        if (ereg("([0-3]{1})([0-9]{1}).([0-1]{1})([0-9]{1}).([0-9]{1})([0-9]{1})([0-9]{1})([0-9]{1})",$datum,$regs))
        {
                //echo "$regs[3]$regs[2]$regs[1]";
                $datum = "$regs[5]$regs[6]$regs[7]$regs[8]$regs[3]$regs[4]$regs[1]$regs[2]";
        }
        else
        {
                return "-1";
        }

	//Zeit
        if ($zeit != null)
        {
                if (ereg("([0-2]{1})([0-9]{1}):([0-5]{1})([0-9]{1})",$zeit,$regs))
                {
                        //echo "$regs[1]$regs[2]$regs[3]$regs[4]";
                        $zeit = "$regs[1]$regs[2]$regs[3]$regs[4]";
                        $zeit = $zeit."00";
                }
                else
                {
                        return "-2";
                }
                return $datum.$zeit;
        }
        else
                return $datum;
}

function sitename ($name,$groupid)
{
global $DEBUG,$admin_id,$db,$LOGVISIT;
	$sqlstr="select curdate() datum,id,hits,hits_current_day,DATE_FORMAT(toc,'%Y-%m-%d') toc from sys_statistic where site='".$name."'";
	$result=GetResult($db,$sqlstr);
	//print_r($result);
	if (! isset($result["0"]["id"])) {
		$sqlstr="insert into sys_statistic (site,hits,hits_current_day,toc) values ('".$name."',1,1,sysdate())";
	} else {
		$hits=$result["0"]["hits"];
		$hits++;
		if ($result["0"]["datum"] != $result["0"]["toc"]) {
			// Datum hat sich geändert ==> Damit müssen alle auf 0 gesetzt werden
			$sqlstr="update sys_statistic set toc=sysdate(),hits_current_day=0";
			$dummy=doSQL($db,$sqlstr);
			$hits_current=1;
		} else {
			$hits_current=$result["0"]["hits_current_day"];
			$hits_current++;
		}
		$sqlstr="update sys_statistic set hits=".$hits.",hits_current_day=".$hits_current.",toc=sysdate() where id=".$result["0"]["id"];
	}
	$dummy=doSQL($db,$sqlstr);
	
	if ($LOGVISIT == "1") {
		//echo "<br>Remote: ".$_SERVER["REMOTE_ADDR"];
		//echo "<br>Remote: ".$_SERVER["REQUEST_URI"];
		//echo "<br>Remote: ".$name;
		$sqlstr = "insert into sys_log_visits (ip,site,url,toc_ts) values (";
		$sqlstr .= "'".$_SERVER["REMOTE_ADDR"]."',";
		$sqlstr .= "'".$name."',";
		$sqlstr .= "'".$_SERVER["REQUEST_URI"]."',";
		$sqlstr .= "sysdate())";
	}
	
	$dummy=doSQL($db,$sqlstr);
	unset($result);
	unset($dummy);
	if (($groupid == $admin_id) && ($DEBUG == 1))
	    echo "<br><b>sitename: ".$name."</b><br>";
	
}


function table_link($text,$href) {
global $link,$link_over,$hand;
   echo '<TD ALIGN="CENTER" BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\''.$href.'\'">';
	echo $text;
   echo '</TD>';
}


function table_data($text,$opt="center") {
global $link,$link_over,$hand;
   echo '<TD ALIGN="'.$opt.'">';
	echo $text;
   echo '</TD>';
}

function janein_link($text,$href) {
global $link,$link_over,$hand;
   echo '<TD WIDTH="50" ALIGN="CENTER" BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor='.$hand.';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\''.$href.'\'">';
	echo $text;
   echo '</TD>';
}

// Textfunktionen
function image_parse ($var)
{
   unset($images);
   $images = array();
	$count=0;
	for ($i = 0; $i <= strlen($var); $i++) {
		if (substr($var,$i,2) == "<#") {
			$begin_tag = $i;
		}

		if ((substr($var,$i,2) == "#>") && (isset($begin_tag))) {
			$end_tag = $i;
		}

		if (isset($begin_tag) && isset($end_tag)) {
			$tmp=sscanf(substr($var,$begin_tag,(($end_tag+2)-$begin_tag)),"<#%d#>");
			array_push($images,$tmp["0"]);
			$count++;
			unset($begin_tag);
			unset($end_tag);
		}
	}
	$images["count"]=$count;
	return $images;
}

function image_replace($var)
{
	$images=image_parse($var);
	$count=0;
	$old_pos=0;
	$newstring="";

	if ($images["count"] >> 0) {
		reset($images);
		for ($i = 0; $i <= strlen($var); $i++) {
			if (substr($var,$i,2) == "<#") {
				$begin_tag = $i;
			}

			if ((substr($var,$i,2) == "#>") && (isset($begin_tag))) {
				$end_tag = $i;
			}

			if (isset($begin_tag) && isset($end_tag)) {
				$link='<IMG SRC="showimage2.php?id='.current($images).'">';
				//$link='-href="showimage.php?id='.current($images).'"-';
				$count++;
				next($images);

				$newstring = $newstring.substr($var,$old_pos,($begin_tag-$old_pos)).$link;
				//echo "<br>".$newstring." I: $i";
				$old_pos=$end_tag + 2;
				$i++;
				unset($end_tag);
				unset($begin_tag);
			}
		}
   		$newstring=$newstring.substr($var,$old_pos);
		return $newstring;
	}
	else
	{
		return $var;
	}
}




function focus() {
	echo '<SCRIPT TYPE="text/javascript">';
     		echo 'window.focus();';
	echo '</SCRIPT>';
}


function sys_counter() {
	global $db;
	$sqlstr = "select count(*) anz from sys_counter1 where toc=curdate()";
	$result=getResult($db,$sqlstr);
	if ($result["0"]["anz"]==0) {
		$sqlstr = "insert into sys_counter1 (toc,hits) values (sysdate(),1)";
	} else {
		$sqlstr = "update sys_counter1 set hits=(hits + 1) where toc=curdate()";
	}
	$result=doSQL($db,$sqlstr);
	
	$sqlstr = "update sys_values set value_i=value_i+1 where name='counter'";
	$result = doSQL($db,$sqlstr);
}

// MessageLog
function mlog($message) {
	global $db,$username;
	$sqlstr  = "insert into sys_log (message,user,ts,ip) values (";
	$sqlstr .= "'".$message."','";
	$sqlstr .= $_SESSION["username"]."',sysdate(),'".$_SERVER[REMOTE_ADDR]."')";
	$result=doSQL($db,$sqlstr);
	unset ($result);
}

// Image_copy

function image_copy_archiv ($imageid) {
	global $db;
	// Kopieren nur dann, wenn das Foto noch nicht im Archiv ist
	// Die ursprünglich ID wird dabei in linked gespeichert
	// So kann erkannt werden, ob das Foto schon im Archiv ist
	$sqlstr  = "select image_id from sys_images where ";
	$sqlstr .= "linked = ".$imageid." and ";
	$sqlstr .= "kategorie=10";
	$result4 = getResult($db,$sqlstr);
	$newid=$result4["0"]["image_id"];
	unset($result4);
	if (isset($newid)) {
		// Das Bild befindet sich schon im Archiv die entsprechende ID wird zurückgegeben
		echo '<br><b>Foto war schon im Archiv vorhanden</b><br>';
		return $newid;
	} else {
		// Lesen der Bilddaten
		echo '<br><b>Foto wird ins Archiv kopiert</b><br>';
		//$sqlstr = "select descr,datum,userid,size from sys_images where image_id=".$imageid;
		//$image=getResult($db,$sqlstr);
		// Kopieren des Spielerfotos nach Kategorie 10
		// linked auf alte id setzen
		$sqlstr = "insert into sys_images (kategorie,descr,datum,userid,linked,size) ";
		$sqlstr .= "select 10,descr,datum,userid,".$imageid.",size from sys_images where image_id=".$imageid;
		$result3=doSQL($db,$sqlstr);
		$result4=getResult($db,"select last_insert_id() last");
		$newid=$result4["0"]["last"];
	   // Kopieren des Fotos in sys_images_blobs
  	   $sqlstr  ="insert into sys_images_blob (image_id,bin_data) ";
		$sqlstr .="select ".$newid.",bin_data from sys_images_blob where image_id=".$imageid;
		$result5=doSQL($db,$sqlstr);
      
		unset($image);
		unset($result3);
		unset($result4);
		unset($result5);      
		
		echo '<br><br>';
		return $newid;
	}
}

function simpleRandString ($len = 8, $list = '23456789ABDEFGHJKMNQRTYabdefghijkmnqrty') {
	$str = '';
	if (is_numeric ($len) && !empty ($list)) {
		mt_srand ((double) microtime () * 1000000);
		while (strlen ($str) < $len) {
			$str .= $list[mt_rand (0, strlen ($list)-1)];
		}
	}
	return $str;
}


function convert_weekday($day) {
	switch($day) {
		case 'Mon':
			return 'Mon.';
		break;
		case 'Tue':
			return 'Die.';
		break;
		case 'Wed':
			return 'Mit.';
		break;
		case 'Thu':
			return 'Don.';
		break;
		case 'Fri':
			return 'Fr.';
		break;
		case 'Sat':
			return 'Sam.';
		break;
		case 'Sun':
			return 'Son.';
		break;
	}
	return $day;
}
			

function birthday_check () {
	global $db;
	$x="1";
	$xMessage="Morgen";
	
	//$x="2";
	//$xMessage="Übermorgen";
	
	
	
	// Sicherstellen, dass das Skript nur einmal pro Tag ausgeführt wird. 
	$sqlstr="select datum from sys_birthday_check_ctrl where datum='".date("Y-m-d")."'";
	$result=getResult($db,$sqlstr);
	if (isset($result["0"])) {
		return 0;
	}
	$sqlstr  = "insert into sys_birthday_check_ctrl (datum,gesendet) values ";
	$sqlstr .= "(sysdate(),sysdate())";	
	doSQL($db,$sqlstr);  
	
	
	$checkday=date("d.m",mktime(0, 0, 0, date("m")  , date("d")+$x, date("Y")));
	
	// Erstellen der Liste der Personen, die "uebermorgen" Geburtstag haben
	$sqlstr="select id,geburtsdatum,name,vorname,email from fb_person where geburtsdatum is not null";
	$result=getResult($db,$sqlstr);
	foreach ($result as $row) {
		$geburtstag=date("d.m",strtotime($row["geburtsdatum"]));
		if ($geburtstag==$checkday) { 
			if (!isset($geburtstagskinder)) {$geburtstagskinder=array(); }
			array_push($geburtstagskinder,$row);
		}
	}
	
	if (isset($geburtstagskinder)) {
		foreach ($geburtstagskinder as $person) {
			// pruefen, in welcher aktuellen (noch nicht geschlossenen) Saison die Person spielt
			$sqlstr  = "select distinct(fb_zwtab_person_typ_tus_mannschaft.saison_id) from fb_zwtab_person_typ_tus_mannschaft,fb_saison where ";
			$sqlstr .= "fb_zwtab_person_typ_tus_mannschaft.saison_id=fb_saison.id and ";
			$sqlstr .= "fb_saison.closed = 0 and ";
			$sqlstr .= "fb_zwtab_person_typ_tus_mannschaft.person_id = ";
			$sqlstr .= $person["id"];
			$result=getResult($db,$sqlstr);
			unset($email);
			if (isset($result)) {
				foreach ($result as $saison) {
					// Alle Spieler außer dem "Geburtstagskind" aus dem Kader lesen
					$sqlstr  = "select name,vorname,email,fb_person.id from fb_person,fb_zwtab_person_typ_tus_mannschaft where ";
					$sqlstr .= "fb_person.id=fb_zwtab_person_typ_tus_mannschaft.person_id and ";
					$sqlstr .= "fb_zwtab_person_typ_tus_mannschaft.saison_id=".$saison["saison_id"]." and ";
					$sqlstr .= "email is not null and ";
					$sqlstr .= "fb_person.id<>".$person["id"]." group by email";
					$result1=getResult($db,$sqlstr);
					
					if (isset($result1)) {
						if (!isset($email)) { $email =array(); }
						foreach ($result1 as $row1) {
							if (!in_array($row1["email"],$email)) {
								array_push($email,$row1["email"]);
							}
						}
					}
				}
			}
//			echo $person["vorname"].' '.$person["name"];
//			echo '<br>=========================================<br>';
			if (isset($email)) {
				foreach ($email as $adr) {
					$subject = 'www.tus-ocholt.de - Geburtstags Erinnerung';
//					$headers  = "MIME-Version: 1.0\r\n";
//					$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
//					$headers .= "From: www.tus-ocholt.de <webmaster@tus-ocholt.de>\r\n";
					$message = $xMessage." am ".$checkday.". hat ".$person["vorname"].' '.$person["name"]." Geburtstag !";
					
					//$adr="vlosch@ewetel.net";
//					mail($adr, $subject, $message, $headers);
					mail($adr, $subject, $message);
				}
				$sqlstr  = "insert into sys_birthday_check (person_id,toc_ts,empfaenger,message) values (";
				$sqlstr .= $person["id"].",";
				$sqlstr .= "sysdate(),";
				$sqlstr .= "'".implode(",",$email)."',";
				$sqlstr .= "'".$message."')";
				doSQL($db,$sqlstr);
	
  			    $sqlstr  = "insert into sys_log (message,user,ts,ip) values (";
				$sqlstr .= "'".$message."','SYSTEM',sysdate(),";
				$sqlstr .= "'".$_SERVER[REMOTE_ADDR]."')";
				doSQL($db,$sqlstr);
			}
			
			
			
		}
	}
	return 0;
}

function sessionControl ($sessionid,$action="check") {
	global $db,$SESSIONTIMEOUT;
	if ($action=="check") {
        // abgelaufene Sessions loeschen
        $t=(time() - $SESSIONTIMEOUT);
        $sqlstr="delete from sys_session where seconds<".$t;
		$rs=doSQL($db,$sqlstr);
		
		$sqlstr="select seconds from sys_session where sessionid='".$sessionid."'";
		$rs=getResult($db,$sqlstr);
		if (isset($rs)) {
			$sqlstr  = "update sys_session set toc=sysdate(),seconds=".time()." ";
			$sqlstr .= "where sessionid='".$sessionid."'";
			$rs=doSQL($db,$sqlstr);
			if ($rs["code"]!= 0) {
				return false;
			}
			return true;
		} else {
		  // Session abgelaufen
		  return false;
		}
	} 

	if ($action=="login") {
			$sqlstr  = "insert into sys_session (sessionid,toc,seconds) values (";
			$sqlstr .= "'".$sessionid."',";
			$sqlstr .= "sysdate(),";
			$sqlstr .= " ".time().")";
			$rs=doSQL($db,$sqlstr);
			if ($rs["code"]!= 0) {
				return false;
			}
	}

	if ($action=="logout") { 
		$sqlstr ="delete from sys_session where sessionid='".$sessionid."'";
		$rs=doSQL($db,$sqlstr);
	}
}

?>
