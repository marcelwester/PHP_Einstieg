<?php

class DoRep_local extends Init {
	
	private $rs_local_1 = NULL;
	private $rs_local_2 = NULL;
	private $rs_local_error = NULL;
	private $rep = Null;
	private $force = NULL; // $force=true : Konflikterkennung deaktiviert! Wird im constructor gesetzt
	
	private $bwoptimization = false;  
	
	
	public function __construct($local=NULL,$force=false) {
		$this->rs_local_1 = new DbPostgres($local);
		$this->rs_local_2 = new DbPostgres($local);
		$this->rs_local_log = new DbPostgres($local);
		$this->rep = new ReadStructure($local);
		
		
		// set myapp.kisrep = 0
		$sql ='set session "myapp.kisrep" = 0';
		$this->rs_local_1->query($sql);
		$this->rs_local_2->query($sql);
		
		// Konflikterkennung bei $force=true deaktiviert
		$this->force = $force;
		
	}
	
	private function reperror($repid,$dml="",$message="") {
		$sql="insert into $this->repadmin."."reperror (rep\$id,toc_ts,dml,message) values ($repid,current_timestamp,'".$dml."','".$message."')";
		echo "startlog\n";
		$this->doSQL($this->rs_local_log, $sql);
		echo "endlog\n";
	}
	
	private function replog($txid,$duration=0,$message=null) {
		$sql="insert into $this->repadmin."."replog (txid,toc_ts,message,duration) values (".$txid.",current_timestamp,'".$message."',".$duration.")";
		$this->doSQL($this->rs_local_log, $sql);
	}
	
	
	
	
	public function bwoptimization($bw=false) {
		$this->bwoptimization=$bw;
	}

	
	public function write($data) {
		
		
		$currenttxid="-1";
		foreach ($data["_txid"] as $txid) {
			if ($currenttxid == "-1") {
				$currenttxid=$txid;
				echo "new transaction: ".$currenttxid."\n";;
				$sql="start transaction";
				$this->rs_local_2->query($sql) or die;
				$sql="insert into ".$this->rep->repadmin.".repsend_done (txid) values (".$currenttxid.")";
				$this->rs_local_2->query($sql) or die;
			}
		
			if ($txid!=$currenttxid) {
				echo "end transaction: ".$currenttxid."\n";
				
				$sql="end transaction";
				$this->rs_local_2->query($sql) or die;

				$currenttxid=$txid;
				echo "new transaction: ".$currenttxid."\n";
				$sql="start transaction";
				$this->rs_local_2->query($sql) or die;
				
				$sql="insert into ".$this->rep->repadmin.".repsend_done (txid) values (".$currenttxid.")";
				$this->rs_local_2->query($sql) or die;
				
			}
		
			// Einzelne Replikationszeile
			$reprow=$data[$currenttxid];
			foreach ($reprow as $row) {

				// Insert
				if ($row["dml"]=="I") {
					$table=$row["table"];
						
					// Insert
					$sql ="insert into ".$this->schema.".".$table." (".$this->rep->getColumnsList($table).") values (";
						
					$val=null;$tmp="";
					foreach ($this->rep->getColumns($table) as $col) {
						$val.=$tmp;
						$remoteval=$row["data"]["val"][$col];
						//						echo "VAL: ".$remoteval."\n";
						if (!isset($remoteval)) {
							// Null spalte
							$val.="NULL";
						} else {
							$val.=$this->rep->getDataSeperator($table, $col);
								
							if ($this->rep->getColumnType($table, $col)=="bytea") $remoteval=base64_decode($remoteval);
								
							if ($this->rep->getDataNeedEscape($table, $col))
								$val.=pg_escape_bytea($remoteval);
							else
								$val.=$remoteval;
				
							$val.=$this->rep->getDataSeperator($table, $col);
							$tmp=",";
						}
					}
					$sql.=$val.")";
						
					$this->rs_local_2->query($sql) or die;
						
					// echo $sql."\n";
				} // ende Insert
				
				// Delete
				if ($row["dml"]=="D") {
					$MD5LOCAL="local";
					$MD5REMOTE=$row["md5"];
					$table=$row["table"];
					
					// PK Infomation und Zusammenbauen der WHERE Bedingung für MD5Summenbildung / Delete
					$WHERE="_X_";
					$PK=$this->rep->getPrimaryKey($table);
					$pklst=$this->rep->getPrimaryKeyList($table);
					
					$sqltmp="";
					$tmp="where ";
					foreach ($PK as $pk) {
						// es fehlt noch colseperator !!!!!
						$sqltmp.=$tmp.$pk."=".$row["pk"][$pk];
						$tmp=" and ";
					}
					$WHERE=$sqltmp;
						
					
					// read local MD5
					// for update ==> Lock für Zeile erzeugen
					if ($this->force==false) {
						$sql="select ".$this->rep->getMD5cols($table)." from ".$this->schema.".".$table." ".$WHERE." for update";
						if (!$this->rs_local_1->query($sql)) {
							exit(1);
						} else {
							if ($tmp=$this->rs_local_1->fetchRow()) {
								$MD5LOCAL=$tmp["md5"];
							}
						}
					}
					
					if ($this->force==false) {
						if ($MD5REMOTE != $MD5LOCAL) {
							echo "\n\nFehler:\nData not found ".$table." ".$WHERE."\n";
							echo "MD5REMOTE: ".$MD5REMOTE."\n";
							echo " MD5LOCAL: ".$MD5LOCAL."\n";
							exit;
					
						}
					}
					
					// delete
					$sql ="delete from  ".$this->schema.".".$table." ".$WHERE;
					$this->rs_local_2->query($sql) or die;

				} // ende Delete
				
					
				// Update
				if ($row["dml"]=="U") {
					$MD5LOCAL="local";
					$MD5REMOTE=$row["md5"];
					$table=$row["table"];
					
					// PK Infomation und Zusammenbauen der WHERE Bedingung für MD5Summenbildung / Delete
					$WHERE="_X_";
					$PK=$this->rep->getPrimaryKey($table);
					$pklst=$this->rep->getPrimaryKeyList($table);
		
					$sqltmp="";
					$tmp="where ";
					foreach ($PK as $pk) {
						// es fehlt noch colseperator !!!!!
						$sqltmp.=$tmp.$pk."=".$row["pk"][$pk];
						$tmp=" and ";
					}
					$WHERE=$sqltmp;
					
						
					// read local MD5
					// for update ==> Lock für Zeile erzeugen
					if ($this->force==false) {
						$sql="select ".$this->rep->getMD5cols($table)." from ".$this->schema.".".$table." ".$WHERE." for update";
						if (!$this->rs_local_1->query($sql)) {
							exit(1);
						} else {
							if ($tmp=$this->rs_local_1->fetchRow()) {
								$MD5LOCAL=$tmp["md5"];
							}
						}
					}
					
					
					
					if ($this->force==false) {
						if ($MD5REMOTE != $MD5LOCAL) {
							echo "\n\nFehler:\nData not found ".$table." ".$WHERE."\n";
							echo "MD5REMOTE: ".$MD5REMOTE."\n";
						    echo " MD5LOCAL: ".$MD5LOCAL."\n";
							exit;
						    
						}
					}
					
					
					// Update
					$sql ="update ".$this->schema.".".$table." set ";
					
					$val=null;$tmp="";
					foreach ($row["data"]["col"] as $col) {
						$val.=$tmp.$col."=";
						$remoteval=$row["data"]["val"][$col];
//						echo "VAL: ".$remoteval."\n";
						if (!isset($remoteval)) {
							// Null spalte
							$val.="NULL";
						} else {
							$val.=$this->rep->getDataSeperator($table, $col);
							
							if ($this->rep->getColumnType($table, $col)=="bytea") $remoteval=base64_decode($remoteval);
							
							if ($this->rep->getDataNeedEscape($table, $col)) 
								$val.=pg_escape_bytea($remoteval);
							else
								$val.=$remoteval;

							$val.=$this->rep->getDataSeperator($table, $col);
							$tmp=",";
						}
					}
					$sql.=$val." ".$WHERE;
					
					$this->rs_local_2->query($sql) or die;
					
					// echo $sql."\n";
				} // ende Update
			}
		
		
		}
		echo "end transaction: ".$currenttxid."\n";
		
		$sql="end transaction";
		$this->rs_local_2->query($sql) or die;
		
	}
	
	public function getnotice() {
		$sql="select txid from ".$this->repadmin.".repsend_done where noticed=false order by txid";
		$NOTICE=array();
		if ($this->rs_local_1->query($sql)) {
			if ( $this->rs_local_1->count()>0) {
				$NOTICE=$this->rs_local_1->getArray();
				echo serialize($NOTICE);
	
			}
		}
	}	
	
	public function purge() {
		// read replog;
		$sql="select rep\$id,table_name,dml,j.txid from ".$this->repadmin.".replog l,".$this->repadmin.".repjournal j 
			  where l.txid=j.txid and done=true order by l.txid,j.rep\$id";

		
		if ($this->rs_local_1->query($sql)) {
			$txid=-1; $repid="0"; $repid_u="0"; $dml='';
			while ($row=$this->rs_local_1->fetchRow()) {
			
				if ($txid!=$row["txid"]) {
					if ($txid!=-1) {
						$sql="end transaction";
						if ($this->rs_local_2->query($sql)) {
							echo "End Transaction: ".$txid."\n";
							$duration=$this->rep->TimerEnd();
							echo "Duration: ".$duration."\n";
						} else {
							echo "ERROR End Transaction: ".$txid."\n";
							$duration=$this->rep->TimerEnd();
							echo "Duration: ".$duration."\n";
						}
					}
					$txid=$row["txid"];
					$sql="start transaction";
					if ($this->rs_local_2->query($sql)) {
						echo "Start new Transaction: ".$txid."\n";
						$this->rep->TimerStart();						
						// delete row from replog
						$sql="delete from ".$this->repadmin.".replog where txid=".$row["txid"];
						$this->rs_local_2->query($sql);
						
						$sql="delete from ".$this->repadmin.".repjournal where txid=".$row["txid"];
						$this->rs_local_2->query($sql);
						

					}
				}
				
				
				// delete rows for txid in all shadowtables and
				if ($row["dml"]!="I") { 
					$sql="delete from ".$this->repadmin.".".$row["table_name"]." where rep\$id=".$row["rep\$id"];
					if (! $this->rs_local_2->query($sql)) {
						echo "Fehler SQL. ".$sql."\n";
						exit(1);
					} else {
						// echo $sql."\n";
					}
				}
			} // ende while

			if ($txid!="-1") {
				$sql="end transaction";
				if ($this->rs_local_2->query($sql)) {
					if ($txid!="-1") echo "End Transaction: ".$txid."\n";
					$duration=$this->rep->TimerEnd();
					echo "Duration: ".$duration."\n";
				} else {
					echo "ERROR End Transaction: ".$txid."\n";
					$this->rs_local_2->query("rollback");
					$duration=$this->rep->TimerEnd();
					echo "Duration: ".$duration."\n";
				}
			}
		}
	}
	
}