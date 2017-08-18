<?php
class DBMS_res {
	
	private $connection = NULL;
	private $result = NULL;
	private $counter=NULL;
	private $rs = NULL;
	private $sql = NULL;
	private $stmt = Null;
	private $bindVal = null;
	private $sqltype = null;
	
	public function __construct($conn=NULL){
		$this->connection = $conn;
	}
	
	public function query($query) {
		$this->sqltype="query";
		if ($this->result=$this->connection->query($query)) {
			$this->counter=$this->result->rowCount();
			return true;
		} else {
			echo "\nFehler SQL Statement:\n".$query."\n";
			return false;
		}
		$this->counter=NULL;
	}
	
	
	public function prepare ($sql) {
		$this->sqltype="prepared";
		if ($this->stmt=$this->connection->prepare($sql)) {
			$this->sql=$sql;
			$this->bindVal=null;
			return $this->stmt;
		} else {
			echo "\nFehler Prepare  SQL Statement:\n".$sql."\n";
			return false;
		}
	}

	public function execute () {
		// Bind
		foreach ($this->bindVal as $val) {
			if ($this->stmt->bindParam($val["indx"],$_tmp[$val["indx"]],$_tmp["indx"][$_tmp[$val["pdotype"]]]))
				$_tmp[$val["indx"]]=$val["val"];
			else {
				echo "Fehler Bind ".$val["val"]." ".$val["indx"];
				exit ;
			}
		}
		
		if ($this->stmt->execute()) {
			return true;
		} else {
			echo "\nFehler Execute: ".$this->sql."\n";
			exit(1);
			return false;
		}
	}
	
	public function bindColumn ($indx,$x,$pdotype=PDO::PARAM_STR) {
		$this->bindVal[$indx]["indx"]=$indx;
		$this->bindVal[$indx]["val"]=$x;
		$this->bindVal[$indx]["pdotype"]=$pdotype;
	}
	
	public function fetch() {
		return $this->result->fetch(PDO::FETCH_BOUND);
	}
	
	public function lId() {
		return $this->connection->lastInsertId();
	}
	
	
	public function getArray() {
		$x = 0;
		$return=null;
		
		if ($this->sqltype=="query") {
			while($datensaetze = $this->result->fetch(PDO::FETCH_ASSOC))
			{
				foreach($datensaetze as $key => $val)
					$return[$x][$key] = $val;
				$x++;
			}
		}

		if ($this->sqltype=="prepared") {
			while($datensaetze = $this->stmt->fetch())
			{
				foreach($datensaetze as $key => $val)
					$return[$x][$key] = $val;
				$x++;
			}
		}
		
		
		return $return;
	}
	
	public function fetchRow() {
		if ($this->sqltype=="query") return $this->result->fetch(PDO::FETCH_ASSOC);
		if ($this->sqltype=="prepared") return $this->stmt->fetch();
	}
	
	public function count() {
		return $this->counter;
	}
	
	
}
?>