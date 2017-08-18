<?php

class DoRep_http extends Init {
	
	private $rs_local_1 = NULL;
	private $rs_local_2 = NULL;
	private $rs_local_error = NULL;
	private $rs_remote_1 = NULL;
	private $rs_remote_2 = NULL;
	private $rep = Null;
	private $force = NULL; // $force=true : Konflikterkennung deaktiviert! Wird im constructor gesetzt
	private $data = array();
	private $remoteurl = null;
	private $maxcount=100;
	
	
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
	
	private function send($data) {
		//$data_to_post=http_build_query($data);
		
		//$form_url = "http://127.0.0.1/PostgresReplication/remote.php?action=dorep";
		//$form_url = "http://postgres-srv-2.kisters.de/PostgresReplication/remote.php?action=dorep";
		
		
		$form_url=$this->remoteurl."?action=dorep";
		
		// Initialize cURL
		$curl = curl_init();
			
		// Set the options
		curl_setopt($curl,CURLOPT_URL, $form_url);
			
		// This sets the number of fields to post
		//curl_setopt($curl,CURLOPT_POST, sizeof($data));
			
		// This is the fields to post in the form of an array.
		$cnt=null;
		
		$cnt=serialize($data);
		$cnt=base64_encode($cnt);
		echo "Datasize: ".strlen($cnt)."\n";
		
		
		curl_setopt($curl,CURLOPT_POSTFIELDS, "data=".$cnt);
		
		//curl_setopt($curl,CURLOPT_POSTFIELDS, "data=".base64_encode(serialize($data)));
		   // ==> 	$data=unserialize(base64_decode($_POST["data"])); print_r($data)
	
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		//execute the post
		$result = curl_exec($curl);
        echo "\n".$result."\n";
				
		//close the connection
		curl_close($curl);
		
	} 
	
	public function setRemoteUrl($url=null) {
		$this->remoteurl=$url;
	}

	public function setMaxCount($cnt=null) {
		// Maximale Anzahl der Zeilen pro html request. 
		$this->maxcount=$cnt;
	}
	
	public function bwoptimization($bw=false) {
		$this->bwoptimization=$bw;
	}

	public function check() {   // Obsolete, Daten werden per http gelesen
		$sql="select txid from ".$this->repadmin.".repsend_done order by txid";
		$this->rs_remote_1->query($sql) or die;
		$idlst=null; $tmp=null;
		while ($row=$this->rs_remote_1->fetchRow()) {
			$sql="start transaction";
			$this->rs_local_1->query($sql) or die;
			$sql="delete from ".$this->repadmin.".repsend where txid=".$row["txid"];
			$this->rs_local_1->query($sql) or die;
			$sql="update ".$this->repadmin.".repjournal set done=true where txid=".$row["txid"];
			$this->rs_local_1->query($sql) or die;
			$sql="end transaction";
			$this->rs_local_1->query($sql) or die;
			$idlst.=$tmp.$row["txid"];
			$tmp=",";
		}
		if ($idlst!=null) {
			$sql="delete from ".$this->repadmin.".repsend_done where txid IN (".$idlst.")";
			$this->rs_remote_1->query($sql) or die;
		}
		
		$sql="select txid from ".$this->repadmin.".repsend order by txid";
		$this->rs_local_1->query($sql) or die;
		if ($this->rs_local_1->count()>0) {
			return false;
		} else {
			return true;
		}
	}
	
	public function getnotice() {

		// check auf Zeilen in repsend
		$sql="select txid  from ".$this->repadmin.".repsend"; 
		$this->rs_local_1->query($sql) or die;
	    if ($this->rs_local_1->count()==0) {
	    	return;
	    }	
	
		
		$form_url=$this->remoteurl."?action=getnotice";
		
		// Initialize cURL
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $form_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		//execute the post
		$result = curl_exec($curl);
		
		//print_r(unserialize($result));
		foreach (unserialize($result) as $row) {
			$sql="start transaction";
			$this->rs_local_1->query($sql) or die;
			$sql="delete from ".$this->repadmin.".repsend where txid=".$row["txid"];
			$this->rs_local_1->query($sql) or die;
			$sql="update ".$this->repadmin.".repjournal set done=true where txid=".$row["txid"];
			$this->rs_local_1->query($sql) or die;
			$sql="end transaction";
			$this->rs_local_1->query($sql) or die;
		}
		
		//close the connection
		curl_close($curl);
	}
	
	public function push() {
		$data=array();
		$data["_txid"]=array();
		// read journal

		$sql="select txid from ".$this->rep->repadmin.".repsend";
		$this->rs_local_1->query($sql) or die;
		if ($this->rs_local_1->count()>0) {
			return false;
		}
		
		
		// Sortierung nach rep$id gruppiert nach txid und Rückgabe der Anzahl der Zeilen
		$TXID=""; $tmp=""; $txid=0;
		$count=0;
		$sql="select min(rep\$id),txid,count(txid) anz from krepadmin.repjournal where done=false and dml<>'u' group by txid order by 1";
		$this->rs_local_1->query($sql) or die;
		while ($row=$this->rs_local_1->fetchRow()) {
			if ($count<=$this->maxcount) {
				// Sicherstellen, dass die Transactionsid stetig steigt .. wichtig für kommende sql Abfrage ... oder by txid,rep\$id 
				if ($txid<$row["txid"]) {
					$TXID.=$tmp.$row["txid"];
					$tmp=",";
					$count=($count + $row["anz"]);
				}
			} else {
				echo "Maximale Anzahl der Zeilen erreicht ! ".$this->maxcount."\n";
				break;
			} 
		}

		if ($TXID!="") {
			//$sql="select rep\$id,rep\$id_u,dml,table_name,txid,toc_ts from ".$this->repadmin.".repjournal where done=false and dml<>'u' order by txid,rep\$id";
			$sql="select rep\$id,rep\$id_u,dml,table_name,txid,toc_ts from ".$this->repadmin.".repjournal where txid in (".$TXID.") and dml<>'u' order by txid,rep\$id";
			if ($this->rs_local_1->query($sql)) {
				$txid=-1; $repid="0"; $repid_u="0"; $dml=''; $indx=0;
				while ($row=$this->rs_local_1->fetchRow()) {
					$repid=$row["rep\$id"];
					$repid_u=$row["rep\$id_u"];
					$dml=$row["dml"];
					
					// Transaktion starten für lokales logging der gesendeten Daten
					$sql="start transaction";
					$this->rs_local_2->query($sql) or die;
	
					
					if ($txid!=$row["txid"]) {
						if ($txid!=-1) {
								echo "End Transaction: ".$txid."\n";
								$duration=$this->rep->TimerEnd();
								echo "Duration: ".$duration."\n";
								$this->replog($txid,$duration);							
						}
						
							
						$txid=$row["txid"];
						
						$sql="insert into ".$this->repadmin.".repsend (txid) values (".$txid.")";
						$this->rs_local_2->query($sql) or die;
							
						array_push($data["_txid"],$txid);
						$indx=0;
						$data[$txid]=array();
						echo "Start Transaction: ".$txid."\n";;
						$this->rep->TimerStart();
					}
					
					// Insert
					if ($row["dml"]=='I') {
						$data[$txid][$indx]["table"] = $row["table_name"];
						$data[$txid][$indx]["dml"] = 'I';
						$ColumnList=$this->rep->getColumnsList($row["table_name"]);  // Kommaseparierte Liste für sql
						$Columns=$this->rep->getColumns($row["table_name"]);     // Array
						$sql_local="select ".$ColumnList." from ".$this->repadmin.".".$row["table_name"]." where rep\$id=".$repid;
						if ($this->rs_local_2->query($sql_local)) {
							$LOCAL=$this->rs_local_2->getArray();
	
							
							// DatenArray mit Werten füllen 
							foreach ($Columns as $col) {
								$cnt=null;
								// Inhalt
								if (!isset($LOCAL["0"][$col])) {
									// Null spalte
									$cnt=null;
								} else {
									if (is_resource($LOCAL["0"][$col])) {
										while (!feof($LOCAL["0"][$col])) {
											$cnt.=fgetc($LOCAL["0"][$col]);
										}
										$cnt=base64_encode($cnt);
									} else {
										$cnt=$LOCAL["0"][$col];
									}
								}
								$data[$txid][$indx]["data"]["val"][$col]=$cnt;
							}
						
						}
					} // Ende Insert
					
					// Delete
					if ($row["dml"]=='D') {
						$data[$txid][$indx]["table"] = $row["table_name"];
						$data[$txid][$indx]["dml"] = 'D';
					
						// echo "Update ".$repid."\n";
						$MD5LOCAL="local";
						
						// Check for conflicts
						// read local MD5 and from shadowtable
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
						
						$data[$txid][$indx]["md5"] = $MD5LOCAL;
							
						// PK Infomation und Zusammenbauen der WHERE Bedingung für MD5Summenbildung / Update
						// WHERE Bedingung wird aus den "vorher" Daten zusammengebaut (falls der PK geändert wurde) !!
						$PK=$this->rep->getPrimaryKey($row["table_name"]);
						$pklst=$this->rep->getPrimaryKeyList($row["table_name"]);
						$sql="select ".$pklst." from ".$this->repadmin.".".$row["table_name"]." where rep\$id=".$repid;
						if (! $this->rs_local_2->query($sql)) {
							echo "Fehler SQL.\n";
							exit(1);
						} else {
							if ($row1=$this->rs_local_2->fetchRow()) {
								foreach ($PK as $pk) {
									$data[$txid][$indx]["pk"][$pk]=$row1[$pk];
								}
							}
						}
					} // Ende Delete
					
					
					// UPDATE
					if ($row["dml"]=='U') {
					
						$data[$txid][$indx]["table"] = $row["table_name"];
						$data[$txid][$indx]["dml"] = 'U';
						
						// echo "Update ".$repid."\n";
						$MD5LOCAL="local";
					
							
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
							
						$data[$txid][$indx]["md5"] = $MD5LOCAL;					
								
						// PK Infomation und Zusammenbauen der WHERE Bedingung für MD5Summenbildung / Update
						// WHERE Bedingung wird aus den "vorher" Daten zusammengebaut (falls der PK geändert wurde) !!
						$PK=$this->rep->getPrimaryKey($row["table_name"]);
						$pklst=$this->rep->getPrimaryKeyList($row["table_name"]);
						$sql="select ".$pklst." from ".$this->repadmin.".".$row["table_name"]." where rep\$id=".$repid_u;
						if (! $this->rs_local_2->query($sql)) {
							echo "Fehler SQL.\n";
							exit(1);
						} else {
							if ($row1=$this->rs_local_2->fetchRow()) {
								foreach ($PK as $pk) {
									$data[$txid][$indx]["pk"][$pk]=$row1[$pk];
								}
							}
						}
						
							
							
						// Start Update
						$ColumnList=$this->rep->getColumnsList($row["table_name"]);  // Kommaseparierte Liste für sql
						$Columns=$this->rep->getColumns($row["table_name"]);     // Array
						if ($this->bwoptimization == true) {
							// bwoptimization ==> Verkleineruing der Spaltenanzahl wenn Updatewert vorher und nachher gleich sind 
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
	
							$val=null;$tmp="";$i=0;
							// DatenArray mit Werten füllen 
							foreach ($Columns as $col) {
								$cnt=null;
								// Spaltenname
								$data[$txid][$indx]["data"]["col"][$i]=$col;
								// Inhalt
								if (!isset($LOCAL["0"][$col])) {
									// Null spalte
									$cnt=null;
								} else {
									if (is_resource($LOCAL["0"][$col])) {
										while (!feof($LOCAL["0"][$col])) {
											$cnt.=fgetc($LOCAL["0"][$col]);
										}
										$cnt=base64_encode($cnt);
									} else {
										$cnt=$LOCAL["0"][$col];
									}
								}
								$data[$txid][$indx]["data"]["val"][$col]=$cnt;
								$i++;
							}
						}
	
					}
					
				 // Ende UPDATE
	
					
					if ($row["dml"]!='u') $indx++;
				}  // Ende while ($row=$this->rs_local_1->fetchRow())
			} // Ende if ($TXID!="")
			
			
			if ($txid!="-1") {
				echo "End Transaction: ".$txid."\n";
				$duration=$this->rep->TimerEnd();
				echo "Duration: ".$duration."\n";
				$this->replog($txid,$duration);
				
	
				// senden per http_post
				$this->rep->TimerStart();
				echo "\nSend Data ... \n";
				$this->send($data);
				$duration=$this->rep->TimerEnd();
				echo "Duration: ".$duration."\n";

				// End Transaction log sent transactions in repsend
				$sql="end transaction";
				if ($this->rs_local_2->query($sql)) {
					// End Transaction
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