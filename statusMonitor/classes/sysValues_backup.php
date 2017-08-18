<?php
class SysValues {
	private $values = array();
    
	private $rslocal=null;
	
	public function __construct($rs){
		$sql="select name_s,val_s from sys_values";
		$rs->query($sql);
		while ($row=$rs->fetchRow()) {
			$this->values[$row["name_s"]]=$row["val_s"];
		}
	}
	
	public  function get($val,$reload=0,$rs=null) {
		if ($reload==1) self::__construct($rs);
		return $this->values["$val"]; 		
		
	}
	
	public  function set($rs,$valname,$val) {
		$sql="update sys_values set val_s='".$val."' where name_s='".$valname."'";
		if (!$rs->query($sql)) {
			alert("Allgemeiner Datenbankfehler update sys_values");
			exit;
		}
		$this->__construct($rs); 
	}
	

	public  function setError($rs,$valname,$val) {
		$sql="select val_s from sys_values where name_s='".$valname."'";
		if (!$rs->query($sql)) {
			echo "DBMS Fehler\n";
		} else {
			if ($row=$rs->fetchRow())  {
				if ($row["val_s"]==$val) {
					return 0;
				} else {
					$sql="update sys_values set val_s='".$val."' where name_s='".$valname."'";
					if (!$rs->query($sql)) {
						echo "DBMS Fehler\n";
					}
					return 1;
				}
			} else {
				$sql="insert into  sys_values set val_s='".$val."',name_s='".$valname."'";
				if (!$rs->query($sql)) {
					echo "DBMS Fehler\n";
				}
				return 1;
			}
		} 
	}
	
	
	
	
	
}