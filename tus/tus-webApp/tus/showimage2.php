<?php
	include "inc.php";
        sitename("showimage2.php",$_SESSION["groupid"]);
	$id = $_REQUEST["id"];

				$sql = "SELECT * FROM sys_images where image_id = ".$id;
				$result = getResult($db, $sql);
				$result = $result[0];
				
				$sql = "select bin_data from sys_images_blob where image_id = ".$id;
				$result1 = getResult($db, $sql);
				$result1 = $result1[0];
				
				if (isset($result["code"]))
					print_r($result);	// FEHLER
				else
				{
					header("Content-type: image/jpg");
					print $result1["bin_data"];
				
					if (!isset($_REQUEST["nostats"])) {
						$sqlstr="select curdate() datum,image_id,hits,hits_current_day,DATE_FORMAT(toc,'%Y-%m-%d') toc from sys_statistic_images where image_id=".$id;
						$result=GetResult($db,$sqlstr);
						//print_r($result);
						if (! isset($result["0"]["image_id"])) {
							$sqlstr="insert into sys_statistic_images (image_id,hits,hits_current_day,toc) values (".$id.",1,1,sysdate())";
						} else {
							$hits=$result["0"]["hits"];
							$hits++;
							if ($result["0"]["datum"] != $result["0"]["toc"]) {
								$hits_current=1;
							} else {
								$hits_current=$result["0"]["hits_current_day"];
								$hits_current++;
							}
							$sqlstr="update sys_statistic_images set hits=".$hits.",hits_current_day=".$hits_current.",toc=sysdate() where image_id=".$id;
						}
						$dummy=doSQL($db,$sqlstr);
										
						// Zusätzliche Statistik
							$sql="insert into sys_images_log (image_id,ip,ts) values (".$id.",'".$_SERVER["REMOTE_ADDR"]."',sysdate())";
							$dummy=doSQL($db,$sql);
					}
					unset($result);
					unset($dummy);
				}
								
?>