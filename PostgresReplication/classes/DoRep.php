<?php

class DoRep extends Init {
	
	private $rs_local_1 = NULL;
	private $rs_local_2 = NULL;
	private $rs_local_error = NULL;
	private $rs_remote_1 = NULL;
	private $rs_remote_2 = NULL;
	private $rep = Null;
	private $force = NULL; // $force=true : Konflikterkennung deaktiviert! Wird im constructor gesetzt
	
	private $bwoptimization = false;  
	
	
	public function __construct($local=NULL,$remote=NULL,$force=false) {
		$this->rs_local_1 = new DbPostgres($local);
		$this->rs_local_2 = new DbPostgres($local);
		$this->rs_local_log = new DbPostgres($local);
		$this->rs_remote_1 = new DbPostgres($remote);
		$this->rs_remote_2 = new DbPostgres($remote);
		$this->rep = new ReadStructure($local);
		
		
		// set myapp.kisrep = 0
		$sql ='set session "myapp.kisrep" = 0';
		$this->rs_remote_1->query($sql);
		$this->rs_remote_2->query($sql);
		
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

	
	public function push() {
		// read journal
		// Sortierung nach Transactionid eventuell nicht sinnvoll, besser eigenen Schlüssel einführen.
		$sql="select rep\$id,rep\$id_u,dml,table_name,txid,toc_ts from ".$this->repadmin.".repjournal where done=false order by txid,rep\$id";
		//echo $sql."\n";
			
		if ($this->rs_local_1->query($sql)) {
			$txid=-1; $repid="0"; $repid_u="0"; $dml='';
			while ($row=$this->rs_local_1->fetchRow()) {
				$repid=$row["rep\$id"];
				$repid_u=$row["rep\$id_u"];
				$dml=$row["dml"];
				
				if ($txid!=$row["txid"]) {
					if ($txid!=-1) {
						$sql="end transaction";
						if ($this->rs_remote_1->query($sql)) {
							$this->rs_local_2->query($sql);
							echo "End Transaction: ".$txid."\n";
							$duration=$this->rep->TimerEnd();
							echo "Duration: ".$duration."\n";
							$this->replog($txid,$duration);							
						} else {
							echo "ERROR End Transaction: ".$txid."\n";
							$duration=$this->rep->TimerEnd();
							echo "Duration: ".$duration."\n";
							$this->reperror($repid,"X","duration=".$duration." "."txid=".$txid);
						}
					}
					$txid=$row["txid"];
					$sql="start transaction";
					if ($this->rs_remote_1->query($sql)) {
						$this->rs_local_2->query($sql);
						echo "Start new Transaction: ".$txid."\n";
						$this->rep->TimerStart();
					}
				}
				
				
				
				//INSERT
				if ($row["dml"]=='I') {
					//echo "## Insert \n";
					//echo $row["table_name"]."  ";
					$sql_local="select ".$this->rep->getColumnsList($row["table_name"])." from ".$this->repadmin.".".$row["table_name"]." where rep\$id=".$repid;
					if ($this->rs_local_2->query($sql_local)) {
						$LOCAL=$this->rs_local_2->getArray();
						$sql_remote ="insert into ".$this->schema.".".$row["table_name"]." ";
						$sql_remote.="(".$this->rep->getColumnsList($row["table_name"]).") values (";
						$val=null;$tmp="";
						foreach ($this->rep->getColumns($row["table_name"]) as $col) {
							if (!isset($LOCAL["0"][$col])) {
								$val.=$tmp."NULL";
							} else {
								$val.=$tmp.$this->rep->getDataSeperator($row["table_name"], $col);
								if (is_resource($LOCAL["0"][$col])) {
									$cnt=null;
									while (!feof($LOCAL["0"][$col])) {
										$cnt.=fgetc($LOCAL["0"][$col]);
									}
								    $val.=pg_escape_bytea($cnt);
								} else {
									if ($this->rep->getDataNeedEscape($row["table_name"], $col)) 
										$val.=pg_escape_bytea($LOCAL["0"][$col]);
									else 
										$val.=$LOCAL["0"][$col];
								}
								$val.=$this->rep->getDataSeperator($row["table_name"], $col);
								$tmp=",";
							}
						}
						$sql_remote.=$val.")";
						
						// Insert data row to remote database
						if (! $this->rs_remote_1->query($sql_remote)) {
							echo "Error insert data \n";
							$this->reperror($repid,"I");
							$this->rs_remote_1->query("rollback");
							exit(1);
						} else {
							// Ok 
						}
						//echo $sql_local."\n";
						//echo $sql_remote."\n";
					} else {
						echo "ERROR\n";
						exit(1);
					}
				} // Ende INSERT

				
				// DELETE
				if ($row["dml"]=='D') {
					//echo "DELETE ".$repid."\n";
					$MD5LOCAL="local";
					$MD5REMOTE="remote";
							
					// Check for conflicts 
					// read local MD5 from shadowtable
					if ($this->force==false) {
						$sql="select ".$this->rep->getMD5cols($row["table_name"])." from ".$this->repadmin.".".$row["table_name"]." where rep\$id=".$repid;
						if (!$this->rs_local_2->query($sql)) {
							exit(1);
						} else {
							if ($tmp=$this->rs_local_2->fetchRow()) {
								$MD5LOCAL=$tmp["md5"];
							}
						}
					}
					
					
					// PK Infomation und Zusammenbauen der WHERE Bedingung für MD5Summenbildung / Delete
					$PK=$this->rep->getPrimaryKey($row["table_name"]);
					$pklst=$this->rep->getPrimaryKeyList($row["table_name"]);
					$sql="select ".$pklst." from ".$this->repadmin.".".$row["table_name"]." where rep\$id=".$repid;
					$WHERE="";
					if (! $this->rs_local_2->query($sql)) {
						echo "Fehler SQL.\n";
						exit(1);
					} else {
						if ($row1=$this->rs_local_2->fetchRow()) {
							$sqltmp="";
							$tmp="where ";
							foreach ($PK as $pk) {
								// es fehlt noch colseperator !!!!!
								$sqltmp.=$tmp.$pk."=".$row1[$pk];
								$tmp=" and ";
							}
						}
						$WHERE=$sqltmp;
					}

					// $WHERE = "where id=3 and id1=4"
					
					
					// read remote MD5 
					// for update ==> Lock für Zeile erzeugen
					if ($this->force==false) {
						$sql="select ".$this->rep->getMD5cols($row["table_name"])." from ".$this->schema.".".$row["table_name"]." ".$WHERE." for update";
						if (!$this->rs_remote_1->query($sql)) {
							exit(1);
						} else {
							if ($tmp=$this->rs_remote_1->fetchRow()) {
								$MD5REMOTE=$tmp["md5"];
							}
						}
					}
						
					if ($MD5LOCAL==$MD5REMOTE || $this->force==true) {
						//echo "md5sum ok.\n";
						$sql="delete from ".$this->schema.".".$row["table_name"]." ".$WHERE;
						if (!$this->rs_remote_1->query($sql)) {
							exit(1);
						}
					} else {
						echo "no data found ... ".$repid."\n";
						$this->reperror($repid,"U","no data found");
						exit(1);
					}
				} // Ende DELETE
				
				
				// UPDATE
				if ($row["dml"]=='U') {
					// echo "Update ".$repid."\n";
					$MD5LOCAL="local";
					$MD5REMOTE="remote";
						
					// Check for conflicts
					// read local MD5 and from shadowtable
					if ($this->force==false) {
						$sql="select ".$this->rep->getMD5cols($row["table_name"])." from ".$this->repadmin.".".$row["table_name"]." where rep\$id=".$repid_u;
						if (!$this->rs_local_2->query($sql)) {
							exit(1);
						} else {
							if ($tmp=$this->rs_local_2->fetchRow()) {
								$MD5LOCAL=$tmp["md5"];
							}
						}
					}
						
						
					// PK Infomation und Zusammenbauen der WHERE Bedingung für MD5Summenbildung / Update
					// WHERE Bedingung wird aus den "vorher" Daten zusammengebaut (falls der PK geändert wurde) !!
					$PK=$this->rep->getPrimaryKey($row["table_name"]);
					$pklst=$this->rep->getPrimaryKeyList($row["table_name"]);
					$sql="select ".$pklst." from ".$this->repadmin.".".$row["table_name"]." where rep\$id=".$repid_u;
					$WHERE="";
					if (! $this->rs_local_2->query($sql)) {
						echo "Fehler SQL.\n";
						exit(1);
					} else {
						if ($row1=$this->rs_local_2->fetchRow()) {
							$sqltmp="";
							$tmp="where ";
							foreach ($PK as $pk) {
								// es fehlt noch colseperator !!!!!
								$sqltmp.=$tmp.$pk."=".$row1[$pk];
								$tmp=" and ";
							}
						}
						$WHERE=$sqltmp;
					}
					
					// $WHERE = "where id=3 and id1=4"
						
						
					// read remote MD5
					if ($this->force==false) {
						$sql="select ".$this->rep->getMD5cols($row["table_name"])." from ".$this->schema.".".$row["table_name"]." ".$WHERE." for update";
						if (!$this->rs_remote_1->query($sql)) {
							exit(1);
						} else {
							if ($tmp=$this->rs_remote_1->fetchRow()) {
								$MD5REMOTE=$tmp["md5"];
							}
						}
					}
					// Start Update
					if ($MD5LOCAL == $MD5REMOTE || $this->force==true) {
						//echo "md5sum ok.\n";
						
						$ColumnList=$this->rep->getColumnsList($row["table_name"]);  // Kommaseparierte Liste für sql
						$Columns=$this->rep->getColumns($row["table_name"]);     // Array
						if ($this->bwoptimization == true) {
							// bwoptimization
							$NewColumnList=null;	$NewColumns=array();
								
							// Es werden nur die Spalten aktualisiert, die sich auch geändert haben, um Bandbreite zu sparen
							// Lesen der Daten	anhand der rep$id
							// prüfen ob in der Liste ein blob enthalten ist ==> ersetzen durch md5()
							$sql="select ".$this->rep->getColumnsListBlobMD5($row["table_name"])." from ".$this->repadmin.".".$row["table_name"]." where rep\$id=".$repid." or rep\$id=".$repid_u;
							// Es müssen genau zwei Datensätze sein. 
							$this->rs_local_2->query($sql);
							if ($this->rs_local_2->count()!=2) {
								echo "Daten inkonsistent. bwoptimization: ".$sql."\n";
							} else {
								$DATA=$this->rs_local_2->getArray();
								$TMP=$Columns; $tmp=null;
								foreach ($TMP as $col) {
									if ($DATA["0"][$col] == $DATA["1"][$col]) {
										// Daten haben sich nicht geändert
										//echo "$col no change.\n";
									} else {
										array_push($NewColumns,$col);
										$NewColumnList.=$tmp.$col;
										$tmp=",";
									}
								}
							}
							
							if ($NewColumnList == null ) {
								echo "Keine Aenderungen in der Zeile !!!. Gesamter Datensatz wird repliziert.\n";
							} else {
								$ColumnList=$NewColumnList;
								$Columns=$NewColumns;
							}
						}
							
						
						// Update
						$sql_local="select ".$ColumnList." from ".$this->repadmin.".".$row["table_name"]." where rep\$id=".$repid;
						if ($this->rs_local_2->query($sql_local)) {
							$LOCAL=$this->rs_local_2->getArray();
							$sql_remote ="update ".$this->schema.".".$row["table_name"]." set ";

							$val=null;$tmp="";
							foreach ($Columns as $col) {
								$val.=$tmp.$col."=";
								if (!isset($LOCAL["0"][$col])) {
									// Null spalte
									$val.="NULL";
								} else {
									$val.=$this->rep->getDataSeperator($row["table_name"], $col);
									if (is_resource($LOCAL["0"][$col])) {
										$cnt=null;
										while (!feof($LOCAL["0"][$col])) {
											$cnt.=fgetc($LOCAL["0"][$col]);
										}
										$val.=pg_escape_bytea($cnt);
									} else {
										if ($this->rep->getDataNeedEscape($row["table_name"], $col))
											$val.=pg_escape_bytea($LOCAL["0"][$col]);
										else
											$val.=$LOCAL["0"][$col];
									}
									$val.=$this->rep->getDataSeperator($row["table_name"], $col);
									$tmp=",";
								}
							}
							$sql_remote.=$val." ".$WHERE;
						    

						  // echo $sql_remote."\n";
						   
						   
						   
						    // Update data row to remote database
							if (! $this->rs_remote_1->query($sql_remote)) {
								echo "Error update data \n";
								$this->reperror($repid,"U");
								$this->rs_remote_1->query("rollback");
								exit(1);
							} else {
								// Ok
							}
							//echo $sql_local."\n";
							//echo $sql_remote."\n";
						} else {
							echo "ERROR\n";
							$this->reperror($repid,"U");
							exit(1);
						}

					} else {
						echo "no data found ... ".$repid."\n";
						$this->reperror($repid,"U","no data found");
						exit(1);
					}
						
				} // Ende UPDATE
				
				
				// update row from repjournal
				$sql="select 1";
				if ($row["dml"]=='D') {
					//$sql="delete from ".$this->repadmin.".repjournal where rep\$id=".$repid;
					$sql="update ".$this->repadmin.".repjournal set done=true where rep\$id=".$repid;
				}

				if ($row["dml"]=='I') {
					//$sql="delete from ".$this->repadmin.".repjournal where rep\$id=".$repid;
					$sql="update ".$this->repadmin.".repjournal set done=true where rep\$id=".$repid;
				}
				
				
				if ($row["dml"]=='U') {
					$repid_u=$repid;
					$sql="select rep\$id_u from ".$this->repadmin.".repjournal where rep\$id=".$repid;
					if (!$this->rs_local_2->query($sql)) {
						exit(1);
					} else {
						if ($row1=$this->rs_local_2->fetchRow()) {
							$repid_u=$row1["rep\$id_u"];
						}
					}
					//$sql="delete from ".$this->repadmin.".repjournal where rep\$id=".$repid." or rep\$id=".$repid_u;
					$sql="update ".$this->repadmin.".repjournal set done=true where rep\$id=".$repid." or rep\$id=".$repid_u;
				}

				if (!$this->rs_local_2->query($sql)) {
					exit(1);
				}
				// Ende: delete row from repjournal

			}  // Ende while ($row=$this->rs_local_1->fetchRow())
			if ($txid!="-1") {
				$sql="end transaction";
				if ($this->rs_remote_1->query($sql)) {
					$this->rs_local_2->query($sql);
					if ($txid!="-1") echo "End Transaction: ".$txid."\n";
					$duration=$this->rep->TimerEnd();
					echo "Duration: ".$duration."\n";
					$this->replog($txid,$duration);
				} else {
					echo "ERROR End Transaction: ".$txid."\n";
					$this->rs_local_2->query("rollback");
					$duration=$this->rep->TimerEnd();
					echo "Duration: ".$duration."\n";
					$this->reperror($repid,"X","duration=".$duration." "."txid=".$txid);
				}
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