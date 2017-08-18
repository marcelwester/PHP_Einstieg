<?php

/*
 * # Class ReadStructure

Input: rep$id => Tabelle


Vorher Laden in eine Klasse
Tabellen
Spalten 
Spaltentypen
PK

public functions der Klasse: 
getTables => Array
getColumn => Array["Tablename"]
getColumnType => STRING["Tablename"]["Columnname"]
getPK =>  Array["Tablename"]
getColumnList => STRING["Tablename"] Kommasepariert
getPKList =>  STRING["Tablename"] Kommasepariert
 */

//class ReadStructure extends DbPostgres {

class ReadStructure extends Init {
	private $rs_local_1 = NULL;
	private $rs_local_2 = NULL;

	
	
	private $tables = array();    // Array of types 
	private $columns = array();   // Array x[table_name] of Columns
	private $column_types = array();  // String x[table_name][columns_name] of columns Type
	private $table_pk = array(); // Array x[table_name] of PK Columns
	
	
	
	public function __construct($local=NULL) {
		$this->rs_local_1 = new DbPostgres($local);
		$this->rs_local_2 = new DbPostgres($local);
		
		
		$error=0;
		$sql="select id,name,schema,repschema,type,adm from ".$this->repadmin.".repconfig where type='TABLE' and adm=0 order by name";

		// read tables
		if (! $this->rs_local_1->query($sql)) {
			echo "Fehler SQL.\n";
		} else {
			$tmp=array();
			while ($row=$this->rs_local_1->fetchRow()) {
				array_push($tmp,$row["name"]);
			}
			$this->tables = $tmp;
		}
		
		// read columntypes
		foreach ($this->tables as $table) {
			$sql="select table_name,column_name,udt_name from information_schema.columns where table_schema='".$this->schema."' and table_name='".$table."' order by column_name";
			if ($this->rs_local_1->query($sql)) {
				$this->columns[$table] = array();
				$this->column_types[$table] = array();
				while($c = $this->rs_local_1->fetchRow()) {
					array_push($this->columns[$table],$c["column_name"] );
					$this->column_types[$table][$c["column_name"]] = $c["udt_name"];
				}
			}
		}
		
		// read pks
		foreach ($this->tables as $table) {
			$sql="
				SELECT
				tc.table_name,c.column_name, c.udt_name
				FROM
				information_schema.table_constraints tc
				JOIN information_schema.constraint_column_usage AS ccu USING (constraint_schema, constraint_name)
				JOIN information_schema.columns AS c ON c.table_schema = tc.constraint_schema AND tc.table_name = c.table_name AND ccu.column_name = c.column_name
				where constraint_type = 'PRIMARY KEY' and tc.table_name = '".$table."' and c.table_schema='".$this->schema."' order by c.column_name";
			if ($this->rs_local_1->query($sql)) {
				$this->table_pk[$table] = array();
				while($pk = $this->rs_local_1->fetchRow()) {
					array_push($this->table_pk[$table],$pk["column_name"] );
				}
			}
		
		}
		
	}
	
	public function getTables() {
		return $this->tables;
	}
	
	public function getColumns($table) {
		return $this->columns[$table];
	}

	public function getColumnsList($table) {
		return $this->Collist($this->columns[$table]);
	}
	
	public function getColumnsListBlobMD5($table) {
		/// SpaltenListe mit md5() bei Blobspalten 
		$tmp=""; $collist=null;
		foreach ($this->columns[$table] as $col) {
			$coltype=$this->column_types[$table][$col];
			switch ($coltype) {
				case "bytea":
					$collist.=$tmp."md5(".$col.") $col";
				break;
				default:
					$collist.=$tmp.$col;
				break;
			}
			$tmp=",";
		}
	    return $collist;
	}
	
	
	
	public function getPrimaryKey($table) {
		return $this->table_pk[$table];
	}

	public function getPrimaryKeyList($table) {
		return $this->Collist($this->table_pk[$table]);
	}
	
	
	public function getColumnType($table,$column) {
		return $this->column_types[$table][$column];
	}
	
	public function getMD5cols($table) {
		$tmp="";
		$ret="md5(";
		foreach ($this->columns[$table] as $col) {
			$type=false;
			if ($this->column_types[$table][$col] == "int4") $type="numeric";
			if ($this->column_types[$table][$col] == "float4") $type="numeric";
			if ($this->column_types[$table][$col] == "timestamp") $type="timestamp";
				
			switch($type) {
				case "numeric":
					$ret.=$tmp."coalesce(to_char("."$col,'9999999999999999999'),'0')";
				break;
				
				case "timestamp":
					$ret.=$tmp."coalesce(to_char("."$col,'HH12:MI:SS:US'),'1970-01-01')";
				break;
				
				default:
					$ret.=$tmp."coalesce(".$col.",'')";
					
			}
			$tmp='||';
		}
		$ret.=")";
		return $ret;
	}
	
	
	private function collist($cols) {
		$ret="";
		$seperator="";
		foreach ($cols as $col) {
			$ret.=$seperator.$col;
			$seperator=",";
		}
		return $ret;
	}
	
    public function getDataSeperator($table,$column) {
    	$ret="";
    	$udt_name = $this->column_types[$table][$column];
    	
    	if ($udt_name=="text") $ret="'";
    	if ($udt_name=="bpchar") $ret="'";
    	if ($udt_name=="varchar") $ret="'";
    	if ($udt_name=="bytea") $ret="'";
    	if ($udt_name=="timestamp") $ret="'";
    	return $ret;
    }
	
    public function getDataNeedEscape($table,$column) {
    	$ret=false;
    	$udt_name = $this->column_types[$table][$column];
    	 
    	//if ($udt_name=="text") $ret=true;
    	if ($udt_name=="bpchar") $ret=true;
    	//if ($udt_name=="varchar") $ret=true;
    	if ($udt_name=="bytea") $ret=true;
    	return $ret;
    }
    
	
	
}
?>