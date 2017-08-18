<?php
class SysValues {
	private $values = array();
	private $connection = NULL;
	private $rs = NULL;

	public function __construct($conn=NULL){
		$this->connection = $conn;
		$this->rs =  new DBMS_res($conn);
		
		$sql="select name_s,val_s from sys_values";
		$this->rs->query($sql);
		while ($row=$this->rs->fetchRow()) {
			$this->values[$row["name_s"]]=$row["val_s"];
		}
	}
	
	public  function get($val) {
		return $this->values["$val"]; 		
		
	}
	
	public  function set($valname,$val) {
		$sql="update sys_values set val_s='".$val."' where name_s='".$valname."'";
		//echo "\n".$sql."\n";
		if (!$this->rs->query($sql)) {
			alert("Allgemeiner Datenbankfehler update sys_values");
			exit;
		}
		$this->__construct($this->connection); 
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