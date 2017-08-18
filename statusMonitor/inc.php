<?php
/*
 * Created on 15.03.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

session_start();

include "constants.php";


//date_default_timezone_set('Europe/Berlin');


# Debugausgaben
$deb = new debug(1);

$rs=getrs();
$rs2=getrs();
// usage in almMessage
$rs_alm=getrs();


// if (isset($_GET)) sqlinjection($_GET);
// if (isset($_POST)) sqlinjection($_POST);

# sysvalues
$sysval = new SysValues($rs);

$DEBUG=$sysval->get("DEBUG");


if (isset($_SESSION["userid"])) {
	if (!sessionControl(session_id())) {
		if ($standalone!="true") {
			unset($_SESSION);
			session_destroy();
			session_start();
		}
	} else {
		$userid=$_SESSION["userid"];
		$groupid=$_SESSION["groupid"];
		if ($standalone!="true") {
			if ($_SESSION["application"]!=$APPLICATION) {
				unset($groupid);
				unset($userid);
				session_destroy();
				session_start();
			}
		}
	}
} else {
	unset($groupid);
	unset($userid);
}

// Fehlerausgabe unterdr<FC>cken
if ($DISPLAY_ERRORS==1) {
        ini_set('display_errors','On');
} else {
        ini_set('display_errors','Off');
}

$mousepointer="hand";
if (strpos($_SERVER["HTTP_USER_AGENT"],"MSIE")) {$mousepointer="hand";}
if (strpos($_SERVER["HTTP_USER_AGENT"],"Gecko")) {$mousepointer="pointer";}


function _mysql_escape_string($str) {
	global $conn;
	return $conn->quote($str);
	
}


function getrs () {
	//global $dbMYSQL,$conn;
	global $conn;
	return new DBMS_res($conn);
	//return new dbMYSQL($dbmysqlconn);
}

function close () {
	global $conn;
	unset($conn);
}

function table_link($text,$href,$colspan="1") {
global $link,$link_over,$mousepointer;
   echo '<TD colspan="'.$colspan.'" class="menu" ALIGN="CENTER" BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor=\''.$mousepointer.'\';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="location.href=\''.$href.'\'">';
		echo utf8_encode($text);
   echo '</TD>';
}

function table_link1($text,$href,$colspan="1") {
global $link,$link_over,$mousepointer;
   echo '<TD colspan="'.$colspan.'" class="menu" ALIGN="CENTER" BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor=\''.$mousepointer.'\';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="'.$href.'">';
		echo utf8_encode($text);
   echo '</TD>';
}

function table_inputpwd($name,$size,$align,$default,$opt) {
   echo '<TD ALIGN="'.$align.'">';
	 echo '<input '.$opt.' type="password" size="'.$size.'" name="'.$name.'" value="'.$default.'"/>';
   echo '</TD>';
}

function table_input($name,$size,$align,$default="",$tabindex=0) {
   echo '<TD bgcolor="#FFFFFF" ALIGN="'.$align.'">';
	 echo '<input type="input" size="'.$size.'" name="'.$name.'" value="'.$default.'" tabindex="'.$tabindex.'"/>';
   echo '</TD>';
}



function table_data($text,$opt="center",$bgc="",$clspan="1") {
global $link,$link_over,$hand;
   echo '<TD class="menu" colspan="'.$clspan.'" ALIGN="'.$opt.'" BGCOLOR="'.$bgc.'">';
	 if ($text=="" || $text==null) 
	 	echo "&nbsp;";
	 else
   		echo utf8_encode($text);
   echo '</TD>';
   
}

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
   echo utf8_encode($row[$field_show]);
   echo "</option>\n";
  }
  echo "</select>";
}

function sitename ($name)
{
global $DEBUG,$db,$LOGVISIT;
	$cs1=getrs ();
	$sqlstr="select curdate() datum,id,hits,hits_current_day,DATE_FORMAT(toc,'%Y-%m-%d') toc from sys_statistic where site='".$name."'";
	$cs1->query($sqlstr);

	if ($rs=$cs1->fetchRow()) {
		$hits=$rs["hits"];
		$hits++;
		if ($rs["datum"] != $rs["toc"]) {
			// Datum hat sich geändert ==> Damit müssen alle auf 0 gesetzt werden
			$sqlstr="update sys_statistic set toc=sysdate(),hits_current_day=0";
			$cs1->query($sqlstr);
			$hits_current=1;
		} else {
			$hits_current=$rs["hits_current_day"];
			$hits_current++;
		}
		$sqlstr="update sys_statistic set hits=".$hits.",hits_current_day=".$hits_current.",toc=sysdate() where id=".$rs["id"];
	} else {
		$sqlstr="insert into sys_statistic (site,hits,hits_current_day,toc) values ('".$name."',1,1,sysdate())";
	}
	$cs1->query($sqlstr);
	
	if ($LOGVISIT == "1") {
		$sqlstr = "insert into sys_log_visits (ip,site,url,toc_ts) values (";
		$sqlstr .= "'".$_SERVER["REMOTE_ADDR"]."',";
		$sqlstr .= "'".$name."',";
		$sqlstr .= "'".$_SERVER["REQUEST_URI"]."',";
		$sqlstr .= "sysdate())";
	}
	$cs1->query($sqlstr);
	
	if ($DEBUG == 1)
	    echo "<br><b>sitename: ".$name."</b><br>";
	
}

function error ($errornumber,$errortext) {
	global $DEBUG,$WRITE_ERRORFILE,$ERRORFILE;
	
	if ($WRITE_ERRORFILE==1) {
		$fp = fopen($ERRORFILE, 'a');
        fwrite($fp,date('Y-m-d H:i:s')."|".$errornumber."|".$errortext."\n");
        fclose($fp);				
	}


	if ($DEBUG==1) {
		echo "#".$errornumber.": ".$errortext;
	}
}

function decho ($text) {
	global $DEBUG;
	if ($DEBUG==2) {
		echo "<br>".$text."</br>";
	}
}

function sys_counter() {
	global $db;
	$cs1=new db($db);
	$sqlstr = "select count(*) anz from sys_counter1 where toc=curdate()";
	$cs1->query($sqlstr);
	
	if ($rs=$cs1->fetchRow()) {
		if ($rs["anz"]==0) {
			$sqlstr = "insert into sys_counter1 (toc,hits) values (sysdate(),1)";
		} else {
			$sqlstr = "update sys_counter1 set hits=(hits + 1) where toc=curdate()";
		}
		$cs1->query($sqlstr);
	};
	$sqlstr = "update sys_values set value_i=value_i+1 where name='counter'";
	$cs1->query($sqlstr);
	unset($cs1);
}

function back($site) {
	echo '<br><br>';
	echo '<table class="layout" width="80%" align="center">';
		echo '<tr>';
			table_link("<h2>Zur&uuml;ck</h2>","index.php?site=".$site."&PHPSESSID=".session_id());
		echo '</tr>';
	echo '</table>';
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

// MessageLog
function mlog($message) {
	global $rs,$username;
	$sqlstr  = "insert into sys_log (message,user,ts,ip) values (";
	$sqlstr .= "'".$message."','";
	$sqlstr .= $_SESSION["username"]."',sysdate(),'".$_SERVER[REMOTE_ADDR]."')";
	$rs->query($sqlstr);
	
}

function sessionControl ($sessionid,$action="check") {
	global $rs,$SESSIONTIMEOUT;
	if ($action=="check") {
        // abgelaufene Sessions loeschen
        $t=(time() - $SESSIONTIMEOUT);
        $sqlstr="delete from sys_session where seconds<".$t;
		$rs->query($sqlstr);
		
		$sqlstr="select seconds from sys_session where sessionid='".$sessionid."'";
		$rs->query($sqlstr);
		if ($rs->fetchRow()) {
			$sqlstr  = "update sys_session set toc=sysdate(),seconds=".time()." ";
			$sqlstr .= "where sessionid='".$sessionid."'";
			if (!$rs->query($sqlstr)) {
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
			if (!$rs->query($sqlstr)) {
				return false;
			}
	}

	if ($action=="logout") { 
		$sqlstr ="delete from sys_session where sessionid='".$sessionid."'";
		$rs->query($sqlstr);
	}
}

function priv() {
	global $_SESSION;
	if (isset($_SESSION["userid"])) {
		return true;
	} else {
		return false;
	}
}

function deb($text="<br>### DEBUG ###<br>",$cmd="") {
	echo $text;
	if ($cmd=="exit") exit;
}


function sqlinjection ($arr) {
/*
	$keyword = array("UPDATE","DELETE ","INSERT","UNION","DROP","SELECT","TRUNCATE");
	foreach (array_keys($arr) as $data) {
		
		foreach ($keyword as $k) {
			if (stristr($arr[$data],$k)) {
			    mlog("Alert SQL-Injection: ".addslashes($arr[$data]));
				alert("Weitere Verarbeitung abgebrochen. Ihre IP wurde gespeichert. Wenden Sie sich an einen Administrator.");							    
			    exit;
			    
			}
	
		}
	}
*/
return false;
	
}


function convertdate($d) {
	# Erwarte Datum im Format dd.mm.YYYY
	$dateArray=explode(".",$d);
	if (checkdate($dateArray[1],$dateArray[0],$dateArray[2])) 
	  return $dateArray[2]."-".$dateArray[1]."-".$dateArray[0];
	else
	  return false;  
}

function alert ($text) {
	echo '<SCRIPT LANGUAGE="JavaScript">';
	  echo 'alert("'.$text.'");';								
	echo '</SCRIPT>';
}


?>
