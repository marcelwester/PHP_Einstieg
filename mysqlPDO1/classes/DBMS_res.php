<?php
class DBMS_res {
	
	private $connection = NULL;
	private $result = NULL;
	private $counter=NULL;
	private $rs = NULL;
	private $sql = NULL;
	
	public function __construct($conn=NULL){
		$this->connection = $conn;
	}
	
	public function query($query) {
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
		if ($this->result=$this->connection->prepare($sql)) {
			$this->sql=$sql;
			return true;
		} else {
			echo "\nFehler SQL Statement:\n".$sql."\n";
			return false;
		}
	}

	public function execute () {
		if ($this->result->execute()) {
			return true;
		} else {
			echo "\nFehler Execute: ".$this->sql."\n";
			return false;
		}
	}
	
	public function bindColumn ($indx,$x,$pdo=PDO::PARAM_STR) {
		if ($this->result->bindParam($indx,$x,$pdo)) {
			return true;
		} else {
			echo "\nFehler SQL bind: ".$this->sql."\n";
			return false;
		}
	}
	
	public function fetch() {
		return $this->result->fetch(PDO::FETCH_BOUND);
	}
	
	public function getArray() {
		$x = 0;
		$return=null;
		while($datensaetze = $this->result->fetch(PDO::FETCH_ASSOC))
		{
			foreach($datensaetze as $key => $val)
				$return[$x][$key] = $val;
			$x++;
		}
		return $return;
	}
	
	
	public function fetchRow() {
		return $this->result->fetch(PDO::FETCH_ASSOC);
	}
	
	public function count() {
		return $this->counter;
	}
	
	
}
?>