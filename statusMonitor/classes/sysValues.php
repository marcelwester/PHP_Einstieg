<?php
class SysValues {
	private $values = array();
    
	private $rslocal=null;
	
	public function __construct($rs2){
		$this->rslocal=$rs2;
		$sql="select name_s,val_s from sys_values";
		$this->rslocal->query($sql);
		while ($row=$this->rslocal->fetchRow()) {
			$this->values[$row["name_s"]]=$row["val_s"];
		}
	}
	
	public  function get($val,$reload=0) {
		if ($reload==1) self::__construct($this->rslocal);
		return $this->values["$val"]; 		
		
	}
	
	public  function set($valname,$val) {
		$sql="update sys_values set val_s='".$val."' where name_s='".$valname."'";
		if (!$this->rslocal->query($sql)) {
			alert("Allgemeiner Datenbankfehler update sys_values");
			exit;
		}
		$this->__construct($this->rslocal); 
	}
	

	public  function setError($valname,$val) {
		$sql="select val_s from sys_values where name_s='".$valname."'";
		if (!$this->rslocal->query($sql)) {
			echo "DBMS Fehler\n";
		} else {
			if ($row=$this->rslocal->fetchRow())  {
				if ($row["val_s"]==$val) {
					return 0;
				} else {
					$sql="update sys_values set val_s='".$val."' where name_s='".$valname."'";
					if (!$this->rslocal->query($sql)) {
						echo "DBMS Fehler\n";
					}
					return 1;
				}
			} else {
				$sql="insert into  sys_values set val_s='".$val."',name_s='".$valname."'";
				if (!$this->rslocal->query($sql)) {
					echo "DBMS Fehler\n";
				}
				return 1;
			}
		} 
	}
	
	
	
	
	
}