<?php
class DbPostgres {
	
	private $connection = NULL;
	private $result = NULL;
	private $counter=NULL;
	private $rs = NULL;
	
	public function __construct($conn=NULL){
		$this->connection = $conn;
	}
	
	public function query($query) {
		if ($this->result=$this->connection->query($query)) {
			$this->counter=$this->result->rowCount();
			return true;
		} else {
			echo "Fehler SQL Statement:".$query;
			return false;
		}
		$this->counter=NULL;
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