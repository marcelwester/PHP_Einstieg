<?php
/*
 * Vorbereitung für die eigentliche der Replikation 
 * Lesen der Tabellen, Spalten und PKs
 */

class CheckRep extends DbPostgres {
	
	private $rs_local = NULL;
	private $rs_remote = NULL;
	private $tables = array();
	private $tables_remote = array();
	
	public $repadmin = "krepadmin";
	public $schema = "public";
	
	
	
	public function __construct($local=NULL,$remote=NULL) {
		$this->rs_local = new DbPostgres($local);
		$this->rs_remote = new DbPostgres($remote);
	}
	
	public function getStructure() {
		return $this->tables;
	}
	
	public function readStructure () {

		$error=0;
		$sql="select id,name,schema,repschema,type,adm from ".$this->repadmin.".repconfig where type='TABLE' and adm=0 order by name";
		
		// read and check config 
		if (! $this->rs_local->query($sql)) echo "Fehler SQL";
		if (! $this->rs_remote->query($sql)) echo "Fehler SQL";
		
		$LOCAL_TABLES = $this->rs_local->getArray();
		$REMOTE_TABLES = $this->rs_remote->getArray();
		
		$localcount=$this->rs_local->count();
		$remotecount= $this->rs_remote->count();
		
		if ($remotecount != $localcount) {
			echo "Fehler: Anzahl Tabellen im Replikationssetup unterschiedlich:\n";
			echo " local: ".$localcount."\n";
			echo "remote: ".$remotecount."\n";
			$error=1;
		}

		$cnt=$localcount;
		if ($remotecount > $localcount) $cnt=$remotecount;
		
		$indx=0;
		while ($indx < $cnt) {
			if (isset($LOCAL_TABLES["$indx"])) $local=$LOCAL_TABLES["$indx"];
			if (isset($REMOTE_TABLES["$indx"])) $remote=$REMOTE_TABLES["$indx"];
			if ($local["name"]!=$remote["name"]) {
				echo "Fehler Replikationskonfiguration ... unterschiedliche Tabellen!\n";
				echo "local: \n";
				print_r($local);
				echo "================\n";
				print_r($remote);
				$error=1;
				
			}
			$indx++;
		}
		
		
		echo "## Read local Table/Column Information ##\n";
		foreach ($LOCAL_TABLES as $table) {
			$sql="select table_name,column_name,udt_name from information_schema.columns where table_schema='".$this->schema."' and table_name='".$table["name"]."' order by table_name";
			if ($this->rs_local->query($sql)) {
				$COLUMNS=$this->rs_local->getArray();
				$this->tables[$table["name"]]["cols"]=$COLUMNS;
				// print_r($COLUMNS);
			}
			
		}
		
		echo "## Read remote Table/Column Information ##\n";
		foreach ($LOCAL_TABLES as $table) {
			$sql="select table_name,column_name,udt_name from information_schema.columns where table_schema='".$this->schema."' and table_name='".$table["name"]."' order by table_name";
			if ($this->rs_remote->query($sql)) {
				$COLUMNS=$this->rs_remote->getArray();
				$this->tables_remote[$table["name"]]["cols"]=$COLUMNS;
				// print_r($COLUMNS);
			}
		}
		
		echo "### compare local und remote columns.\n";
		// Es werden nur die lokalen Tabellen durchgegangen. Falls remote welche zusätzlich vorhanden sind, fällt das auf wenn der 
		// Pushjob remote gestartet wird
		foreach ($LOCAL_TABLES as $table) {
			if ($this->tables[$table["name"]]["cols"] != $this->tables_remote[$table["name"]]["cols"]) {
				echo "Fehler Spaltenliste bei Tabelle: ".$table["name"]."\n";
				exit;
			} 
		}		
		
	
		echo "## Read Primary Key Information ##\n";
		foreach ($LOCAL_TABLES as $table) {
				$sql="
				SELECT
				tc.table_name,c.column_name, c.udt_name
				FROM
				information_schema.table_constraints tc
				JOIN information_schema.constraint_column_usage AS ccu USING (constraint_schema, constraint_name)
				JOIN information_schema.columns AS c ON c.table_schema = tc.constraint_schema AND tc.table_name = c.table_name AND ccu.column_name = c.column_name
				where constraint_type = 'PRIMARY KEY' and tc.table_name = '".$table["name"]."' and c.table_schema='".$this->schema."'";
	
				if ($this->rs_local->query($sql)) {
					if ($this->rs_local->count()>0) {
						$PK_COLUMNS=$this->rs_local->getArray();
						$this->tables[$table["name"]]["pk"]=$PK_COLUMNS;
						//print_r($PK_COLUMNS);
					} else {
						echo "Fehler: Tabelle ".$table["name"]." hat keinen PK.\n";
						$error=1;
					}
				}
		}		
		
		
		if ($error!=0) {
			echo "Fehler Replikationssetup ... \n";
			exit(1);
		} else {
			echo "Replikationssetup Ok\n";
		}
		
		
		// print_r($this->tables);

		
		
		//print_r($LOCAL_TABLES);		
		
		

		//print_r($REMOTE_TABLES);
	}
	
	
}