<?php
include "classes/DbPostgresConnect.php";
include "classes/DbPostgres.php";
include "classes/PostRep.php";
include "classes/CheckRep.php";
include "classes/ReadStructure.php";
include "classes/DoRep.php";
include "classes/DoRep_http.php";
include "classes/DoRep_local.php";

date_default_timezone_set('Europe/Berlin');

//error_reporting("E_NONE");

//error_reporting("E_ALL & ~E_DEPRECATED & ~E_STRICT");

$DEBUG=1;
$WRITE_ERRORFILE=0;


class Init {
	public $triggerPrefix = "rt\$";
	public $functionPrefix = "rf\$";
	public $repadmin = "krepadmin";
	public $schema = "public";
	private $timer = null;
	
	public function doSQL($conn,$sql) {
		$conn->query("start transaction");
		if (! $conn->query($sql)) {
			echo "Fehler SQL Statement - ".$sql;
		}
		$conn->query("end transaction");
	}
	
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