<?php
class dbOracleConnect {
	private $connection = NULL;

	private $user = NULL;
	private $passwd = NULL;
	private $host = NULL;
	private $db = "ORCL";
	private $port = 1521;
	

	public function __construct() {
		// default constructer
		
	}

	function __destruct() {
		$this->close();
	}

	public function setUser ($x) {
		$this->user=$x;
	}

	public function setPass ($x) {
		$this->passwd=$x;
	}

	public function setHost ($x) {
		$this->host=$x;
	}

	public function setDB ($x) {
		$this->db=$x;
	}

	public function setPort ($x) {
		$this->port=$x;
	}
	
	
	public function conn() {
		$tns= "
		(DESCRIPTION =
          (ADDRESS_LIST =
            (ADDRESS = (PROTOCOL = TCP)(HOST = ".$this->host.")(PORT = ".$this->port."))
          )
            (CONNECT_DATA =
            (SERVICE_NAME = ".$this->db.")
          )
        )";
		
		try{
			$this->connection = new PDO("oci:dbname=".$tns,$this->user,$this->passwd);
		}catch(PDOException $e){
			echo ($e->getMessage());
		}
		
		
		if (!$this->connection) {
			error(1,"DB-connection fehlgeschlagen user: ".$this->user);
			die;
		}
		return $this->connection;
	}

	public function close() {
		if (is_resource($this->connection))
			$this->connection = null;
	}

}