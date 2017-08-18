<?php

  if ($_SESSION["groupid"] != 10) {
  	 echo $no_rights;
  	 exit;
  }

  if (!isset($_GET["action"])) 
	   $action="start";
  else
	   $action=$_GET["action"];

  sitename("adm_optionen.php",$_SESSION["groupid"]);
  $group="";
  switch ($action) {
  	case 'start':
	
		$url="index.php?site=admin&admsite=optionen&PHPSESSID=".session_id();
		
		$sql="select name_s,val_s,descr_s,typ,group_s from sys_values where auto_edit=1 order by group_s,indx";
		$rs->query($sql);
		$result=$rs->getArray();
		$k="";
		foreach ($result as $row) {
			$tmplst.=$k.$row["name_s"];
			$k=",";
		}

		$form = new Form($url,"EINGABE","post");
  		$form->init($tmplst);

		$form->addHTML('<table align="center">');
		foreach ($result as $row) {
			if ($row["group_s"] != $group) {
				$group=$row["group_s"];
				$form->addHTML('<tr>');
				$form->addHTML('<td class="menu" align="center" colspan="2"><b>'.utf8_encode($group).'</b></td>');
				$form->addHTML('</tr>');
			}
			
			$form->addHTML('<tr>');
				$form->addHTML('<td class="menu" align="right">'.utf8_encode($row["descr_s"]).'</td>');
				$form->addHTML('<td>');
				$form->addInput($row["name_s"], "20",$row["typ"],utf8_encode($row["val_s"]));
				$form->addHTML('</td>');
			$form->addHTML('</tr>');
		}
		$form->addHTML('<tr>');
		$form->addHTML('<td class="menu" colspan="2" align="center">');
		$form->addSubmit("Speichern");
		$form->addHTML('</td>');
		$form->addHTML('</tr>');
		$form->addHTML('</table>');				
  		
  		if ($form->isSent() & $form->resultValidate()) {
  			foreach ($result as $row) {
  				$sysval->set($row["name_s"], $form->getStr($row["name_s"]));
  			}
			echo '<br><br>Die Aktion wurde erfolgreich ausgef&uuml;hrt.';
        	// reload der Seite
        	$sql="update sys_reload set rel=1";
        	$rs->query($sql);
           	$sysval->set("check_reload",1);
           	
        	echo '<SCRIPT TYPE="text/javascript">';
        	echo 'setTimeout("location.href=\''.$url.'\'",1000);';
        	echo '</SCRIPT>';
        	 
  		} else {
			$form->show();
  			if (!$form->resultValidate()) {
				alert("Fehler beim Speichern!");
			}
	  	}
			
  	break;
  	
  }  

?>