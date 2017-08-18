<?php
class Blackout {
	private $blackout=null;
	private $active=true;
	private $start=null;
	private $end=null;
	private $status=null;
	public $text="";
	
	public function __construct($b){
		$this->blackout = $b;
		// Blackout
		$tmp=explode("-",$this->blackout);
		$start=$tmp["0"];
		$end=$tmp["1"];
		$start=str_replace(":", "", $start);
		$end=str_replace(":", "", $end);
		if (! is_numeric($start)) $this->active=false;
		if (! is_numeric($end)) $this->active=false;
        if (strlen($start)>4) $this->active=false;
	    if (strlen($end)>4) $this->active=false;
		
		if ($this->active==true) {
			if (intval($start)>=0 &&
					intval($start) <=2400 &&
					intval($end)>=0 &&
					intval($end)<=2400) {
				$this->start=$start;
				$this->end=$end;
				$this->active=true;
				$this->text=$this->blackout;
		    } else {
				$this->active=false;
			}
		}
	    if ($this->active==false)	$this->text="deaktiviert";
	 }
	 

	 public function setStatus($b=null) {
	 	$this->status=$b;
	 }
	 
	 public function getStatus() {
	 	return $this->status;
	 }
	 
	 
	 public function check() {
	 	$ts=intval(date("Hi"));
	 	//echo intval($this->start)." ... ".intval($this->end)." ... ".$ts."  \n";
	 	// return true, wenn der Zeitstempel innerhalb des blackouts ist.  
	 	if ($this->active==false) return false; // Blackout deaktiviert
	 	
	 	// Wenn es über Mitternacht geht
	 	if (intval($this->start) > intval($this->end)) {
	 		if ($ts > intval($this->start)) return true;
	 		if ($ts < intval($this->end)) return true;
	 	} else {
     		if ($ts > intval($this->start) & $ts<intval($this->end)) return true;
	 	}
	 	return false;
	 }
}
