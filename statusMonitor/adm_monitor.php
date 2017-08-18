<?php

  if ($_SESSION["groupid"] != 10) {
  	 echo $no_rights;
  	 exit;
  }

  if (!isset($_GET["action"])) 
	   $action="start";
  else
	   $action=$_GET["action"];

sitename("adm_resource.php",$_SESSION["groupid"]);
$baseurl="index.php?site=admin&admsite=monitor&PHPSESSID=".session_id();



  switch ($action) {
  	case 'start':
		echo '<br><a href="'.$baseurl.'&action=add">Neuen Monitor anlegen</a><br><br>';
  		$sql="select id,name,displayname,descr,indx,timeout,active from sys_monitor order by name";
   		$rs->query($sql);
   		$t = new Table("resource", array("Name","Beschreibung","Aktiv","Aktion"));
   		while ($row=$rs->fetchRow()) {
   			$t->startRow();

   			$t->addCol($row["name"],'align="left"');
   			$t->addCol($row["descr"],'align="left"');
   			$cnt="nein";
   			if (intval($row["active"])==1) $cnt="ja"; 
   			$t->addCol($cnt,'align="center"');
   			
   			$cnt='<a href="'.$baseurl."&action=edit&id=".$row["id"].'">';
   			$cnt.='<IMG SRC=img/edit.jpg>';
   			$cnt.='</a>';
   			$cnt.='&nbsp;&nbsp;';
   			$cnt.='<a href="'.$baseurl."&action=del&id=".$row["id"].'">';
   			$cnt.='<IMG SRC=img/del.jpg>';
   			$cnt.='</a>';
   			$cnt.='&nbsp;&nbsp;';
   			$t->addCol($cnt,'align="center"');
   			$t->endRow();
   		}
   		$t->endTable();
   		$t->showTable();
  	break;
  	
  	case 'add':
    	$f = new Form($baseurl."&action=add", "eingabe", "POST");
  		$f->init("name,descr,indx,groups,timeout,displayname");

  		$f->addHTML('<br><table align="center">');
  	  	$f->addHTML("<tr>");
  	  	$f->addHTML('<td align="right">Name: </td>');
 	  	$f->addHTML('<td align="left">');
  	  	$f->addInput("name", 60,"text",$row["name"]);
  	  	$f->addHTML('</td>');
  	  	$f->addHTML("</tr>");

  	  	$f->addHTML("<tr>");
  	  	$f->addHTML('<td align="right">Gruppierung: </td>');
  	  	$f->addHTML('<td align="left">');
  	  	$f->addInput("groups", 32,"text",$row["groups"]);
  	  	$f->addHTML('</td>');
  	  	$f->addHTML("</tr>");
  	  	
  	  	$f->addHTML("<tr>");
  	  	$f->addHTML('<td align="right">Reihenfolge in Gruppe: </td>');
  	  	$f->addHTML('<td align="left">');
  	  	$f->addInput("indx", 3,"number",intval($row["indx"]));
  	  	$f->addHTML('</td>');
  	  	$f->addHTML("</tr>");
  	  	  	  	
  	  	
  	  	$f->addHTML("<tr>");
  	  	$f->addHTML('<td align="right">Name in Anzeige: </td>');
  	  	$f->addHTML('<td align="left">');
  	  	$f->addInput("displayname", 60,"text",$row["displayname"]);
  	  	$f->addHTML('</td>');
  	  	$f->addHTML("</tr>");
  	  	
  	  	$f->addHTML("<tr>");
  	  	$f->addHTML('<td align="right">Beschreibung: </td>');
  	  	$f->addHTML('<td align="left">');
  	  	$f->addTextArea("descr",$row["descr"],60,5);
  	  	$f->addHTML('</td>');
  	  	$f->addHTML("</tr>");

  	  	$f->addHTML("<tr>");
  	  	$f->addHTML('<td align="right">Timeout in Sekunden: </td>');
  	  	$f->addHTML('<td align="left">');
  	  	$f->addInput("timeout", 3,"number",0);
  	  	$f->addHTML('</td>');
  	  	$f->addHTML("</tr>");


  	  	$f->addHTML("<tr>");
  	  	$f->addHTML('<td align="right">Aktiv: </td>');
  	  	$f->addHTML('<td align="left">');
 	  	$checked="CHECKED";
  	  	$f->addCheckBox("active",$checked);
  	  	$f->addHTML('</td>');
  	  	$f->addHTML("</tr>");

  	  	$f->addHTML("<tr>");
  	  	$f->addHTML('<td align="right">Alarmierungsgruppe: </td>');
  	  	$f->addHTML('<td align="left"> - DEFAULT -</td>');
  	  	
  	  	
  	  	
  	  	$f->addHTML("</table>");
		$f->addHTML('<br>'); 	  	
  	  	
  	  	if (!$f->isSent() || ! $f->resultValidate()  ) {	  	  	
	  	  	$f->show();
	  	  	echo '<table class="layout" width="60%" align="center">';
			echo '<tr>';
			echo '<td width="50%"></td>';
			echo '<td width="50%"></td>';
			echo '</tr>';	
			echo '<tr>';
				table_link("Abbrechen",$baseurl);
				table_link("Speichern","javascript:document.eingabe.submit();");
			echo '</tr>';
			echo '</table><br>';
			echo '<script type="text/javascript">'; 
				echo 'document.eingabe.name.focus();';
			echo '</script>';
		} else {
			$sql="insert into sys_monitor (name,indx,timeout,groups,displayname,descr,active) values (?,?,?,?,?,?,?)";
			$rs->prepare($sql);
			$rs->bindColumn(1,$f->getStr("name") );
			$rs->bindColumn(2,$f->getNumber("indx") );
			$rs->bindColumn(3,$f->getNumber("timeout") );
			$rs->bindColumn(4,$f->getStr("groups") );
			$rs->bindColumn(5,$f->getStr("displayname") );
			$rs->bindColumn(6,$f->getStr("descr") );
			$rs->bindColumn(7,intval($f->getStr("active")));
			
			if ($rs->execute()) {
				$id=$rs->lId();
				// Eintrag in sys_monitor_status
				$sql="insert into sys_monitor_status (toc_ts,status,monitor_name) values (sysdate(),?,?)";
				$rs->prepare($sql);
				$rs->bindColumn(1,0);
				$rs->bindColumn(2,$f->getStr("name") );
				$rs->execute();
				
				// Hinzufügen zur defaultgruppe
				$sql="insert into sys_monitor_group (monitorid,groupid) values (?,1)";
				$rs->prepare($sql);
				$rs->bindColumn(1,$id);
				$rs->execute();
				
				// reload der Seite
				$sql="update sys_reload set rel=1";
				$rs->query($sql);
				echo "Daten erfolgreich gespeichert.";
				$url=$baseurl;
	    		echo '<SCRIPT TYPE="text/javascript">';
           			echo 'setTimeout("location.href=\''.$url.'\'",1000);';
        		echo '</SCRIPT>';
			} else {
				echo "Fehler Beim Speichern der Daten.";
			}
			
		}
  	break;
  	
  	case 'del':
		$id=intval($_GET["id"]);
  		if ($_GET["confirm"]=="yes") {
  			$sql="select id,name,descr from sys_monitor where id=?";
  			$rs->prepare($sql);
  			$rs->bindColumn(1, $id);
  			$rs->execute();
  			if ($row=$rs->fetchRow()) {
  				$name=$row["name"];
  				$sql="delete from sys_monitor_status where monitor_name='".$name."'";
  				$rs->query($sql);
  			}
  			
  			$sql="delete from sys_monitor_group where monitorid=?";
  			$rs->prepare($sql);
  			$rs->bindColumn(1, $id);
  			$rs->execute();
  				
  			
  			$sql="delete from sys_monitor where id=?";
  			$rs->prepare($sql);
  			$rs->bindColumn(1, $id);
			if ($rs->execute()) {
				echo "Datensatz erfolgreich gelöscht.";
				$sql="update sys_reload set rel=1";
				$rs->query($sql);
				$url=$baseurl;
	    		echo '<SCRIPT TYPE="text/javascript">';
           			echo 'setTimeout("location.href=\''.$url.'\'",1000);';
        		echo '</SCRIPT>';
			} else {
				echo "Fehler Beim Löschen der Daten.";
			}
  			
  		} else {
	  		$sql="select id,name,descr from sys_monitor where id=".$id;
	  	  	$rs->query($sql);
	  	  	if ($row=$rs->fetchRow()) {
	  	  		echo '<br>'.$row["name"];
	  	  		echo '<br>'.$row["descr"];
	  	  		echo '<br><h2>Wirklich löschen ?</h2>';
	  		  	echo '<table class="layout" width="60%" align="center">';
				echo '<tr>';
				echo '<td width="50%"></td>';
				echo '<td width="50%"></td>';
				echo '</tr>';	
				echo '<tr>';
					table_link("Abbrechen",$baseurl);
					table_link("Löschen",$baseurl."&action=del&id=".$row["id"]."&confirm=yes");
				echo '</tr>';
				echo '</table>';	
				echo '<br><br><br>';
	  	  	
	  	  	}
  		}
  	break;
  		
   
  	case 'edit':
  	  	$id=intval($_GET["id"]);
  		$f = new Form($baseurl."&action=edit&id=".$id, "eingabe", "POST");
  		$f->init("name,descr,indx,groups,timeout,displayname,active");
  		
  		// Lesen aller Alarmierungsgruppen
  		$sql="select name,groupid from sys_groups order by groupid";
  		$rs->query($sql);
  		$GROUPS=$rs->getArray();
		
  		// Lesen der aktiven Alarmierungsgruppen
  		$sql="select groupid from sys_monitor_group where monitorid=?";
  		$rs->prepare($sql);
  		$rs->bindColumn(1, intval($_GET["id"]),PDO::PARAM_INT);
  		$rs->execute();
  		$GROUPMEMBER=$rs->getArray();
  		
  		
  	  	$sql="select id,name,descr,indx,groups,active,displayname,timeout from sys_monitor where id=".$id;
  	  	$rs->query($sql);
  	  	if ($row=$rs->fetchRow()) {
  	  		
  	  		$f->addHTML('<br><table align="center">');
  	  		$f->addHTML("<tr>");
  	  		$f->addHTML('<td align="right">Name: </td>');
  	  		$f->addHTML('<td align="left">');
  	  		$f->addInput("name", 60,"text",$row["name"]);
  	  		$f->addHTML('</td>');
  	  		$f->addHTML("</tr>");
  	  		
  	  		$f->addHTML("<tr>");
  	  		$f->addHTML('<td align="right">Gruppierung: </td>');
  	  		$f->addHTML('<td align="left">');
  	  		$f->addInput("groups", 32,"text",$row["groups"]);
  	  		$f->addHTML('</td>');
  	  		$f->addHTML("</tr>");
  	  		
  	  		$f->addHTML("<tr>");
  	  		$f->addHTML('<td align="right">Reihenfolge in Gruppe: </td>');
  	  		$f->addHTML('<td align="left">');
  	  		$f->addInput("indx", 3,"number",intval($row["indx"]));
  	  		$f->addHTML('</td>');
  	  		$f->addHTML("</tr>");
  	  		
  	  		
  	  		$f->addHTML("<tr>");
  	  		$f->addHTML('<td align="right">Name in Anzeige: </td>');
  	  		$f->addHTML('<td align="left">');
  	  		$f->addInput("displayname", 60,"text",$row["displayname"]);
  	  		$f->addHTML('</td>');
  	  		$f->addHTML("</tr>");
  	  		
  	  		$f->addHTML("<tr>");
  	  		$f->addHTML('<td align="right">Beschreibung: </td>');
  	  		$f->addHTML('<td align="left">');
  	  		$f->addTextArea("descr",$row["descr"],60,5);
  	  		$f->addHTML('</td>');
  	  		$f->addHTML("</tr>");
  	  		
  	  		$f->addHTML("<tr>");
  	  		$f->addHTML('<td align="right">Timeout in Sekunden: </td>');
  	  		$f->addHTML('<td align="left">');
  	  		$f->addInput("timeout", 3,"number",intval($row["timeout"]));
  	  		$f->addHTML('</td>');
  	  		$f->addHTML("</tr>");

  	  		$f->addHTML("<tr>");
  	  		$f->addHTML('<td align="right">Aktiv: </td>');
  	  		$f->addHTML('<td align="left">');
  	  		$checked="";
  	  		if (intval($row["active"])==1) $checked="CHECKED";
  	  		$f->addCheckBox("active",$checked);
  	  		$f->addHTML('</td>');
  	  		$f->addHTML("</tr>");

  	  		
  	  		// Gruppenauswahl
  	  		$f->addHTML("<tr>");
  	  		$f->addHTML('<td align="right" valign="top">Alarmierungsgruppen: </td>');
  	  		$f->addHTML('<td align="left">');
  	  			foreach ($GROUPS as $group) {
  	  				$checked="";
					if (isset($GROUPMEMBER)) {
	  	  				foreach ($GROUPMEMBER as $member) {
							if ($group["groupid"]==$member["groupid"]) $checked="CHECKED";
	  	  				}
					} 
  	  				$f->addCheckBox("ALMGRP[]",$checked,$group["groupid"]);
  	  				
  	  				$f->addHTML("&nbsp;&nbsp;".$group["name"]."<br>");
  	  			}
  	  		$f->addHTML('</td>');
  	  		$f->addHTML("</tr>");
  	  		
  	  		
  	  		
  	  		$f->addHTML("</table>");
	  	  	
	  	  	
	  	  	if (!$f->isSent() || ! $f->resultValidate()  ) {
		  	  	$f->show();
		  	  	echo '<table class="layout" width="60%" align="center">';
				echo '<tr>';
				echo '<td width="50%"></td>';
				echo '<td width="50%"></td>';
				echo '</tr>';	
				echo '<tr>';
					table_link("Abbrechen",$baseurl);
					table_link("Speichern","javascript:document.eingabe.submit();");
				echo '</tr>';
				echo '</table><br>';
				echo '<script type="text/javascript">'; 
					echo 'document.eingabe.name.focus();';
				echo '</script>';
			} else {
				// update
				$sql="update sys_monitor set name=?,indx=?,timeout=?,active=?,groups=?,displayname=?,descr=? where id=?";
				$rs->prepare($sql);
				$rs->bindColumn(1, $f->getStr("name"));
				$rs->bindColumn(2,$f->getNumber("indx") );
				$rs->bindColumn(3,$f->getNumber("timeout") );
				$rs->bindColumn(4,$f->getNumber("active") );
				$rs->bindColumn(5,$f->getStr("groups") );
				$rs->bindColumn(6,$f->getStr("displayname") );
				$rs->bindColumn(7,$f->getStr("descr") );
				$rs->bindColumn(8,$id);
				
				
				//if ($rs->query($sql)) {
				if ($rs->execute()) {
					// Alarmierungsgruppen
					$sql="delete from sys_monitor_group where monitorid=?";
					$rs->prepare($sql);
					$rs->bindColumn(1,$id,PDO::PARAM_INT);
					$rs->execute();

					if ($_POST["ALMGRP"]) {
						$sql="insert into sys_monitor_group (monitorid,groupid) values (?,?)";
						$rs->prepare($sql);
						foreach ($_POST["ALMGRP"] as $almgrp) {
							$rs->bindColumn(1,$id,PDO::PARAM_INT);
							$rs->bindColumn(2,$almgrp,PDO::PARAM_INT);
							$rs->execute() or die ;
						}
					}
					
					
					echo "Daten erfolgreich gespeichert.";
					$sql="update sys_reload set rel=1";
					$rs->query($sql);
					$url=$baseurl;
		    		echo '<SCRIPT TYPE="text/javascript">';
	           			echo 'setTimeout("location.href=\''.$url.'\'",1000);';
	        		echo '</SCRIPT>';
				} else {
					echo "Fehler Beim Speichern der Daten.";
				}
				
			}
  	  	}
    break;
  }  

?>