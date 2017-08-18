<?php
class dbPostgresConnect {
	private $connection = NULL;

	private $user = NULL;
	private $passwd = NULL;
	private $host = NULL;
	private $db = NULL;
	private $port = 5432;
	

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
		$this->connection = new PDO('pgsql:host='.$this->host.';port='.$this->port.';dbname='.$this->db.';user='.$this->user.';password='.$this->passwd);
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