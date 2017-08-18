<?php
include "classes/DbMysqlConnect.php";
include "classes/DBMS_res.php";
include "classes/SysValues.php";
include "classes/Message.php";

date_default_timezone_set('Europe/Berlin');

//error_reporting("E_NONE");

error_reporting("E_ALL & ~E_DEPRECATED & ~E_STRICT");

$DEBUG=0;
$WRITE_ERRORFILE=1;
$ERROR_FILE='/tmp/telegram.log';


class Init {
	private $timer = null;
	public function TimerStart() {
//		$this->timer=date("U");
		$this->timer=microtime(true);
	}
	
	public function TimerEnd() {
		//return ($this->timer=date("U") - $this->timer);
		return round(($this->timer=microtime(true) - $this->timer),2);
	}
}




function error ($errornumber,$errortext) {
	global $DEBUG,$WRITE_ERRORFILE,$ERRORFILE;
	if ($WRITE_ERRORFILE==1) {
		$fp = fopen($ERRORFILE, 'a');
		fwrite($fp,date('Y-m-d H:i:s')."|".$errornumber."|".$errortext."\n");
		echo date('Y-m-d H:i:s')."|".$errornumber."|".$errortext."\n";
		fclose($fp);
	}

	if ($DEBUG==1) {
		echo "#".$errornumber.": ".$errortext;
	}
}


?>