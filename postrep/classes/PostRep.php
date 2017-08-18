<?php
class PostRep extends DbPostgres {
	
	public $triggerPrefix = "rt\$";
	public $functionPrefix = "rf\$";
	public $repadmin = "krepadmin";
	public $schema = "public";
		
	public $tables = array();
		
	private $sql = "\n\n";  
	private $rs = NULL;
	
	public function __construct($conn=NULL){
		$this->connection = $conn;
		$this->rs = new DbPostgres($conn);
	}
		
	private function addSQL($s="") {
		$this->sql .= $s."\n";
	}
	
	private function cols($table) {
		$sql="select column_name from information_schema.columns where table_name='".$table."' and table_schema='".$this->schema."'";
		$this->rs->query($sql);
		$komma=NULL; $ret=array();
		while ($row=$this->rs->fetchRow()) {
			$ret["list"].=$komma.$row["column_name"];
			$ret["new"].=$komma."new.".$row["column_name"];
			$ret["old"].=$komma."old.".$row["column_name"];
			$komma=",";
		}
		return $ret;
	}

	private function addConfig($name,$type,$adm=1) {
		$this->addSQL("insert into ".$this->repadmin.".repconfig (name,type,repschema,schema,adm) values ('".$name."','".$type."','".$this->repadmin."','".$this->schema."',$adm);");
	}

	
	public function createTrigger() {
		$cols=array();
		foreach ($this->tables as $table) {
            $cols=$this->cols($table);
		 	
		// INSERT
            $this->addSQL('-- ***************** insert '.$table.'  *****************');
			$this->addSQL('create or replace function '.$this->functionPrefix.'I'.$table.'() RETURNS TRIGGER as $$');
			$this->addSQL('declare repid bigint;');
			$this->addSQL('BEGIN');
			$this->addSQL('-- all data from kisters replication, do not fire an insert');
			$this->addSQL("if current_setting('myapp.kisrep')::integer <> 1 THEN");
			$this->addSQL("select nextval('".$this->repadmin.".idgenerator') into repid;");
			$this->addSQL("insert into ".$this->repadmin.".".$table." (rep\$id,".$cols["list"].") values (repid,".$cols["new"].");");
			$this->addSQL("insert into ".$this->repadmin.".repjournal (rep\$id,dml,table_name,txid,toc_ts) values (repid,'I','".$table."',txid_current(),current_timestamp);");
			$this->addSQL('end if;');
			$this->addSQL('RETURN NEW;');
			$this->addSQL("END \$\$ LANGUAGE 'plpgsql';");
			$this->addConfig($this->functionPrefix.'I'.$table, "FUNCTION",0);
			$this->addSQL('drop trigger if exists '.$this->triggerPrefix.'I'.$table.' on '.$table.';');
			$this->addSQL('CREATE TRIGGER '.$this->triggerPrefix.'I'.$table.' BEFORE INSERT ON '.$table.' FOR EACH ROW EXECUTE PROCEDURE '.$this->functionPrefix.'I'.$table.'();');
			$this->addConfig($this->triggerPrefix.'I'.$table, "TRIGGER",0);
			$this->addSQL();
			$this->addSQL();

		// DELETE
			$this->addSQL('-- ***************** delete '.$table.'  *****************');
			$this->addSQL('create or replace function '.$this->functionPrefix.'D'.$table.'() RETURNS TRIGGER as $$');
			$this->addSQL('declare repid bigint;');
			$this->addSQL('BEGIN');
			$this->addSQL('-- all data from kisters replication, do not fire an delete');
			$this->addSQL("if current_setting('myapp.kisrep')::integer <> 1 THEN");
			$this->addSQL("select nextval('".$this->repadmin.".idgenerator') into repid;");
			$this->addSQL("insert into ".$this->repadmin.".".$table." (rep\$id,".$cols["list"].") values (repid,".$cols["old"].");");
			$this->addSQL("insert into ".$this->repadmin.".repjournal (rep\$id,dml,table_name,txid,toc_ts) values (repid,'D','".$table."',txid_current(),current_timestamp);");
			$this->addSQL('end if;');
			$this->addSQL('RETURN OLD;');
			$this->addSQL("END \$\$ LANGUAGE 'plpgsql';");
			$this->addConfig($this->functionPrefix.'D'.$table, "FUNCTION",0);
			$this->addSQL('drop trigger if exists '.$this->triggerPrefix.'D'.$table.' on '.$table.';');
			$this->addSQL('CREATE TRIGGER '.$this->triggerPrefix.'D'.$table.' BEFORE DELETE ON '.$table.' FOR EACH ROW EXECUTE PROCEDURE '.$this->functionPrefix.'D'.$table.'();');
			$this->addConfig($this->triggerPrefix.'D'.$table, "TRIGGER",0);
			$this->addSQL();
			$this->addSQL();

		// UPDATE
			$this->addSQL('-- ***************** update '.$table.'  *****************');
			$this->addSQL('create or replace function '.$this->functionPrefix.'U'.$table.'() RETURNS TRIGGER as $$');
			$this->addSQL('declare repid bigint;');
			$this->addSQL('BEGIN');
			$this->addSQL('-- all data from kisters replication, do not fire an update');
			$this->addSQL("if current_setting('myapp.kisrep')::integer <> 1 THEN");
			$this->addSQL("select nextval('".$this->repadmin.".idgenerator') into repid;");
			$this->addSQL("insert into ".$this->repadmin.".".$table." (rep\$id,".$cols["list"].") values (repid,".$cols["old"].");");
			$this->addSQL("insert into ".$this->repadmin.".repjournal (rep\$id,dml,table_name,txid,toc_ts) values (repid,'u','".$table."',txid_current(),current_timestamp);");
			$this->addSQL("select nextval('".$this->repadmin.".idgenerator') into repid;");
			$this->addSQL("insert into ".$this->repadmin.".".$table." (rep\$id,".$cols["list"].") values (repid,".$cols["new"].");");
			$this->addSQL("insert into ".$this->repadmin.".repjournal (rep\$id,dml,table_name,txid,toc_ts) values (repid,'U','".$table."',txid_current(),current_timestamp);");
			$this->addSQL('end if;');
			$this->addSQL('RETURN NEW;');
			$this->addSQL("END \$\$ LANGUAGE 'plpgsql';");
			$this->addConfig($this->functionPrefix.'U'.$table, "FUNCTION",0);
			$this->addSQL('drop trigger if exists '.$this->triggerPrefix.'U'.$table.' on '.$table.';');
			$this->addSQL('CREATE TRIGGER '.$this->triggerPrefix.'U'.$table.' BEFORE UPDATE ON '.$table.' FOR EACH ROW EXECUTE PROCEDURE '.$this->functionPrefix.'U'.$table.'();');
			$this->addConfig($this->triggerPrefix.'U'.$table, "TRIGGER",0);
			$this->addSQL();
			$this->addSQL();
		}
	}
		
	public function createTable () {
		foreach ($this->tables as $table) {
			$this->addSQL('-- ***************** create shadow table for '.$table.'  *****************');
			$this->addSQL("DROP TABLE ".$this->repadmin.".".$table.";");
			$this->addSQL("CREATE TABLE ".$this->repadmin.".".$table." (");
			$this->addSQL("rep\$id   bigint NOT NULL " );

			$sql="select column_name,data_type,udt_name,character_maximum_length,is_nullable from information_schema.columns where table_name='".$table."' and table_schema='".$this->schema."'";
			$this->rs->query($sql);
			while ($row=$this->rs->fetchRow()) {
				$cnt=""; 
				switch ($row["data_type"]) {
					case "character":
						$cnt=$row["column_name"]."     ".$row["udt_name"]."(".$row["character_maximum_length"].")";
					break;;
					case "character varying":
						$cnt=$row["column_name"]."     ".$row["udt_name"]."(".$row["character_maximum_length"].")";
					break;;
				} 
				if ($cnt=="") $cnt=$row["column_name"]."    ".$row["udt_name"];
				if ($row["is_nullable"]=='NO') $cnt.="    NOT NULL";
				$this->addSQL(",".$cnt);
			}
			$this->addSQL(");");
			$this->addConfig($table, "TABLE",0);
			$this->addSQL();
			$this->addSQL();
		}
	}
	
	public function createCommon () {
		
		$this->addSQL("-- *****************  create schema ".$this->repadmin." *****************");
		$this->addSQL("create schema ".$this->repadmin.";");
		$this->addSQL();
		$this->addSQL();
		
		$this->addSQL("-- ***************** create table ".$this->repadmin.".repconfig *****************");
		$this->addSQL("drop table if exists ".$this->repadmin.".repconfig;");
		$this->addSQL("create table ".$this->repadmin.".repconfig (");
		$this->addSQL("id bigserial PRIMARY KEY not null,");
		$this->addSQL("name varchar(255),");
		$this->addSQL("schema varchar(255),");
		$this->addSQL("repschema varchar(255),");
		$this->addSQL("type varchar(255),");
		$this->addSQL("adm int);");
		$this->addConfig('repconfig', "TABLE");
		$this->addSQL();
		$this->addSQL();		
		
		$this->addSQL("-- ***************** create sequence für rep\$id *****************");
		$this->addSQL("drop sequence if exists ".$this->repadmin.".idgenerator;");
		$this->addSQL("create sequence ".$this->repadmin.".idgenerator");
		$this->addSQL("INCREMENT 1");
		$this->addSQL("MINVALUE 1");
		$this->addSQL("MAXVALUE 9223372036854775807");
		$this->addSQL("START 1");
		$this->addSQL("CACHE 1;");
		$this->addConfig('idgenerator', "SEQUENCE");
		$this->addSQL();
		$this->addSQL();
		
		
		$this->addSQL("-- ***************** create repjournal *****************");
		$this->addSQL("drop table if exists ".$this->repadmin.".repjournal;");
		$this->addSQL("create table ".$this->repadmin.".repjournal (");
		$this->addSQL("rep\$id bigserial not null,");
		$this->addSQL("dml varchar(1),");
		$this->addSQL("table_name varchar(128),");
		$this->addSQL("txid bigint,");
		$this->addSQL("toc_ts timestamp);");
		$this->addConfig('repjournal', "TABLE");
		$this->addSQL();
		$this->addSQL();
	}
	
	public function cleanRep() {
		$sql="select id,name,schema,repschema,type from ".$this->repadmin.".repconfig where name <>'repconfig' order by name desc";
		$this->rs->query($sql);
		
		while ($row=$this->rs->fetchRow()) {
			switch ($row["type"]) {
				case "TABLE":
					$this->addSQL("drop table if exists ".$row["repschema"].".".$row["name"].";");
				break;;
				case "SEQUENCE":
					$this->addSQL("drop sequence if exists ".$row["repschema"].".".$row["name"].";");
				break;;
				case "FUNCTION":
					// durch cascade werden die abhängigen Trigger mit gelöscht
					$this->addSQL("drop function if exists  ".$row["schema"].".".$row["name"]."() cascade;");
				break;;
			}
		}
		$this->addSQL("drop schema if exists ".$this->repadmin." cascade;");
	}
	
	public function getSQL() {
		return $this->sql."\n\n";
	}
} 
?>