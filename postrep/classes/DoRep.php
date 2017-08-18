<?php
class DoRep extends DbPostgres {
	
	private $rs_local = NULL;
	private $rs_remote = NULL;
	
	public $repadmin = "krepadmin";
	public $schema = "public";
	
	
	
	public function __construct($local=NULL,$remote=NULL) {
		$this->rs_local = new DbPostgres($local);
		$this->rs_remote = new DbPostgres($remote);
	}
	
	public function readStructure () {
		$sql="select id,name,schema,repschema,type,adm from ".$this->repadmin.".repconfig where type='TABLE' and adm=0 order by name";
		
		// read and check config 
		if (! $this->rs_local->query($sql)) echo "Fehler SQL";
		if (! $this->rs_remote->query($sql)) echo "Fehler SQL";
		
		$LOCAL_TABLES = $this->rs_local->getArray();
		$REMOTE_TABLES = $this->rs_remote->getArray();
		
		
		$indx=0;
		while ($indx >= 0) {
			if (isset($LOCAL_TABLES["$indx"])) $local=$LOCAL_TABLES["$indx"];
			if (isset($REMOTE_TABLES["$indx"])) $remote=$REMOTE_TABLES["$indx"];
			print_r($local);
			echo "--------------\n";
			print_r($remote);
			echo "=====================================\n";
			$indx++;
		}
		
		
		print_r(array_diff_uassoc($REMOTE_TABLES,$LOCAL_TABLES),1);
		print_r(array_diff_uassoc($LOCAL_TABLES,$REMOTE_TABLES),1);
		
		if (array_diff($LOCAL_TABLES,$REMOTE_TABLES) || array_diff($REMOTE_TABLES,$LOCAL_TABLES)) {
			echo "ERROR check local and remote tables!";
			exit;
		}
		
		//print_r($LOCAL_TABLES);		
		
		echo "###########################################\n";

		//print_r($REMOTE_TABLES);
	}
	
	
}