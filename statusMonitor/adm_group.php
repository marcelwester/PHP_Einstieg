
<script type="text/javascript"> 
<!-- 
	var blah=0; 
	document.onkeydown=function(e){ 
	txt=document.eingabe; 
	if(!e)e=window.event; 
		code=(e.keyCode)?e.keyCode:e.which; 
	el=(e.srcElement)?e.srcElement:e.target; 
	if(el.tabIndex){ 
		tab=Number(el.tabIndex); 
	} 
	if(code==13){ 
		if(tab==(txt.length - 1)){ 
			document.eingabe.submit(); 
	} 
	else{ 
		for (var i=0;i<txt.length;i++){ 
			if(Number(txt[i].tabIndex)==(tab+1)){ 
				txt[i].focus(); 
			} 
		} 
	} 
	blah=1 
	return false; 
	} 
		else blah=0; 
	} 
  --> 
</script>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script> 
<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script> 
<?php
sitename("adm_group.php",$_SESSION["groupid"]);
$baseurl="index.php?site=admin&admsite=group&PHPSESSID=".session_id();

// Usergroupid
if ($groupid==10 or die);

if (!isset($_GET["action"]))
	$action="start";
else 
	$action=$_GET["action"];
	
switch ($action) {
	case "start":
		echo '<a href="'.$baseurl.'&action=edit&groupid=0">Neue Alamierungsgruppe anlegen</a>';
		$sql = "select groupid,name,descr,disable from sys_groups order by groupid";
		$rs->query($sql);
		echo '<table class="tablesorter" id="liste" >';
		echo '<thead>';
		echo '<tr>';
			// echo '<th align="center">ID</th>';
			echo '<th align="center">Name / Mitglieder bearbeiten</th>';
			echo '<th align="center">Beschreibung</th>';
			echo '<th align="center">Aktiv</th>';
			echo '<th align="center">Aktion</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
			while ($row=$rs->fetchRow()) {
				echo '<tr>';
				//table_data('<a href="'.$baseurl."&action=edit&groupid=".$row["groupid"].'">'.$row["groupid"].'</a>');
				table_data('<a href="'.$baseurl."&action=groupusers&groupid=".$row["groupid"].'">'.$row["name"]);
				table_data($row["descr"]);
				if ($row["disable"]=="0") $tmp="ja"; else $tmp="nein";
				table_data($tmp);

				if ($row["groupid"]!=1)
					table_data('<a href="'.$baseurl."&action=edit&groupid=".$row["groupid"].'"><img src="img/edit.jpg"></a>&nbsp;&nbsp;&nbsp;<a href="'.$baseurl."&action=del&groupid=".$row["groupid"].'"><img src="img/del.jpg"></a>');
				else
					table_data("&nbsp;");
			}
		echo '</tbody>';
		echo '</table>';
	    ?>
	    <script type="text/javascript">
		$(document).ready(function() 
			    { 
			$.tablesorter.addParser({
			    id: 'germandatetime',
			    is: function(s) {
			       return false;
			    },
			    format: function(s) {
			      var a = s.split('.');
			      a[1] = a[1].replace(/^[0]+/g,"");
			      tmp = a[2].split(' ');
			      a[2] =  tmp[0];
			      t = tmp[1].split(':');
			 
			      return new Date(a[2],a[1]-1,a[0],t[0],t[1],0).getTime();
			    },
			    type: 'numeric'
			});
					$("#liste").tablesorter( {sortList: [[1,0]],headers: 
			            {
			              3:{sorter: false},
			              4:{sorter: false}
			            }
			        } ); 
			    } 
			); 
		</script>
	<?php 
	break;
	case "yesdel":
		$groupid=intval($_GET["groupid"]);
		$sql="delete from sys_groups where groupid=".$groupid;
		if (! $rs->query($sql)) {
			echo "Fehler beim L&ouml;schen!";
			exit();
		}
		echo "Gruppe erfolgreich gel&ouml;scht.";
		$url=$baseurl;
		echo '<SCRIPT TYPE="text/javascript">';
		echo 'setTimeout("location.href=\''.$url.'\'",1000);';
		echo '</SCRIPT>';
		exit;
		
	break;
	case "del":
		$groupid=intval($_GET["groupid"]);
		$baseurl="index.php?site=admin&admsite=group&PHPSESSID=".session_id();
		$sql = "select ";
		$sql.= "name,descr ";
		$sql.= "from sys_groups ";
		$sql.= "where groupid=".$groupid;
	
		$rs->query($sql) or die(1);
		if ($row=$rs->fetchRow()) {
			echo '<br><br>';
			echo "Gruppe ".$row["name"]." (".$row["descr"].") wirklich l&ouml;schen?";
			echo '<br>';
			echo '<br>';
			echo '<br>';
			echo '<table class="layout" width="60%" align="center">';
			echo '<tr>';
			echo '<td width="50%"></td>';
			echo '<td width="50%"></td>';
			echo '</tr>';
			echo '<tr>';
			table_link("Abbrechen",$baseurl);
			table_link("L&ouml;schen",$baseurl."&action=yesdel&groupid=".$groupid);
			echo '</tr>';
			echo '</table>';
			echo '<br>';
			echo '<br>';
			echo '<br>';
		}
	break;
	case "edit":
		$groupid=intval($_GET["groupid"]);
		$baseurl="index.php?site=admin&admsite=group&PHPSESSID=".session_id();
		if ($_POST["action1"]=="save") {
			$dosql=1;
			if ($dosql==1) {
				
				if ($groupid!=0) {
						$sql="update sys_groups set name=?,descr=?,disable=? where groupid=?";
						$rs->prepare($sql);
						$varname="name"; $rs->bindColumn(1,$_POST[$varname]);
						$varname="descr"; $rs->bindColumn(2,$_POST[$varname]);
						$varname="disable"; $rs->bindColumn(3,$_POST[$varname]);
						$varname="disable"; $rs->bindColumn(4,$groupid);
				} else  {
						$sql="INSERT INTO sys_groups (name,descr,disable) values (?,?,?)";
						$rs->prepare($sql);
						$varname="name"; $rs->bindColumn(1,$_POST[$varname]);
						$varname="descr"; $rs->bindColumn(2,$_POST[$varname]);
						$varname="disable"; $rs->bindColumn(3,$_POST[$varname]);
				}
				
		
				if (!$rs->execute()) {
					echo "Die Daten konnten nicht gespeichert werden!";
				} else {
					
					mlog("Speichern von Benutzerdaten ".$userid);
		    		echo "Daten erfolgreich gespeichert.";
		    		$url=$baseurl;
		    		echo '<SCRIPT TYPE="text/javascript">';
	           			echo 'setTimeout("location.href=\''.$url.'\'",1000);';
	        		echo '</SCRIPT>';
	        		exit;
				}
			}	
		}
		
		
		// Nur bei edit
		if ($groupid!=0) {
			$sql = "select ";
			$sql.= "groupid,name,descr,disable ";
			$sql.= "from sys_groups ";
			$sql.= "where groupid=".$groupid;
			$rs->query($sql);
			if ($row=$rs->fetchRow() or die);
		} else {
		    // Neuen Benutzer anlegen
		    $row["disable"]=0;
		    $groupid=0;
		}
		
		
		echo '<FORM name="eingabe" method="post" action="'.$baseurl.'&action=edit&groupid='.$groupid.'">';
		echo '<table class="layout" width="80%" align="center">';
		echo '<tr>';
		echo '<td width="50%"></td>';
		echo '<td width="50%"></td>';
		echo '</tr>';	

		echo '<tr>';
			$varname=name;
			if (isset($_POST[$varname])) $val=$_POST[$varname]; else $val=$row[$varname];
			table_data("Name:&nbsp;",right);
			table_input($varname,"40","left",$val,'style="font-size:90%" tabindex="1"');
		echo '</tr>';
 
		
		echo '<tr>';
			$varname=descr;
			if (isset($_POST[$varname])) $val=$_POST[$varname]; else $val=$row[$varname]; 
			table_data("Beschreibung:&nbsp;",right);
			table_input($varname,"40","left",$val,'style="font-size:90%" tabindex="2"');
		echo '</tr>';
		
		echo '<tr>';
			$varname=disable; 
			if (isset($_POST[$varname])) $val=$_POST[$varname]; else $val=$row[$varname];
			table_data("Aktiv:&nbsp;",right);
			if ($val=="0") $checked="checked"; else $checked="";
			$cnt='Aktiv:<input type="Radio" name="'.$varname.'" tabindex="3" value="0" '.$checked.'>';
			if ($val=="1") $checked="checked"; else $checked="";
			$cnt.='&nbsp;&nbsp;&nbsp;Deaktiviert:<input type="Radio" name="'.$varname.'" tabindex="4" value="1" '.$checked.'>';     
			echo '<td bgcolor="#FFFFFF" align="left">';
			   echo '<font size="-1">'.$cnt.'</font>';
			echo '</td>';
		echo '</tr>';


		
		
		echo '</table>';
			echo '<br>';
		echo '<table class="layout" width="60%" align="center">';
		echo '<tr>';
		echo '<td width="50%"></td>';
		echo '<td width="50%"></td>';
		echo '</tr>';	
		echo '<tr>';
			table_link("Abbrechen",$baseurl);
			table_link("Speichern","javascript:document.eingabe.submit();");
		echo '</tr>';
		echo '</table>';
		echo '<input type="hidden" name="action1" value="save"/>';
		echo '</FORM>';
		echo '<script type="text/javascript">'; 
			echo 'document.eingabe.login.focus();';
		echo '</script>';
	break;
	
	case "groupusers":
		echo '<br>';
		echo '<table  width="20%" align="center">';
		echo '<tr>';
		echo '<td width="100%"></td>';
		echo '</tr>';
		echo '<tr>';
		$baseurl="index.php?site=admin&admsite=group&PHPSESSID=".session_id();
		table_link("<br><b>Zur&uuml;ck zur Guppenauswahl</b><br>&nbsp;",$baseurl);
		echo '</tr>';
		echo '</table>';
		echo '<br>';
		$groupid=intval($_GET["groupid"]);
		$baseurl="index.php?site=admin&admsite=group&action=groupusers&groupid=".$groupid."&PHPSESSID=".session_id();

		$sql="select name from sys_groups where groupid=?";
		$rs->prepare($sql);
		$rs->bindColumn(1, $groupid,PDO::PARAM_INT);
		$rs->execute();
		if ($row=$rs->fetchRow()) {
			echo "<h2>".$row["name"]."</h2>";
		}
		
		// Hinzufügen zu Gruppe
		if ($_POST["action_groupusers"]=="add") {
			if (isset($_POST["adduser"])) {
				$sql="insert into sys_user_group (userid,groupid) values (?,?)";
				$rs->prepare($sql);
				foreach ($_POST["adduser"] as $userid) {
					if (intval($userid)>0) {
						$rs->bindColumn(1, $userid,PDO::PARAM_INT);
						$rs->bindColumn(2, $groupid,PDO::PARAM_INT);
					    $rs->execute() or die;
					}
				}
			}
		}
		
		// Entfernen aus Gruppe
		if ($_POST["action_groupusers"]=="del") {
			if (isset($_POST["deluser"])) {
				$sql="delete from sys_user_group where userid=? and groupid=?";
				$rs->prepare($sql);
				foreach ($_POST["deluser"] as $userid) {
					if (intval($userid)>0) {
						$rs->bindColumn(1, $userid,PDO::PARAM_INT);
						$rs->bindColumn(2, $groupid,PDO::PARAM_INT);
					    $rs->execute() or die;
					}
				}
			}
		}
		
		
		
		
		
		
		
		// Lesen der Benutzer für die linke selectbox
		// Alle Benutzer lesen (groupid=1) abzüglich der, die schon in der Gruppe sind. 
		$sql="select concat(name,', ',vorname) name,userid from sys_users s where groupid=1 and userid>1 and userid not in 
   				  (select u.userid from sys_users u,sys_user_group sug where sug.groupid=? and sug.userid=u.userid)";
		$rs->prepare($sql);
		$rs->bindColumn(1, $groupid,PDO::PARAM_INT);
		$rs->execute() or die;
		$all_users=$rs->getArray();
		if (!isset($all_users)) {
			$all_users["0"]["name"]="- Keine Benutzer -";
			$all_users["0"]["userid"]=-1;
		}
		
		// groupmebers
		$sql="select concat(u.name,', ',u.vorname) name, u.userid from sys_users u,sys_user_group sug where sug.groupid=? and sug.userid=u.userid";
		$rs->prepare($sql);
		$rs->bindColumn(1, $groupid,PDO::PARAM_INT);
		$rs->execute() or die;
		$group_members=$rs->getArray();
		if (!isset($group_members)) {
			$group_members["0"]["name"]="- Keine Benutzer -";
			$group_members["0"]["userid"]=-1;
		}
		
		
		
		echo '<FORM method="post" action="'.$baseurl.'">';
		echo '<table width="80%">';
		echo '<tr>';
		  echo '<td align="center" width="40%">';
		     echo "Benutzer";
		     echo '<br>';
		     build_select($all_users,"name","userid","adduser[]","multiple",18,"");
		  echo '</td>';
		  echo '<td align="center" width="20%">';
		  	  echo '<input type="hidden" name="action_groupusers" value="">';
		  	  echo '<input type="image"  src="img/pfeilrechts.jpg" alt="add" onclick="this.form.action_groupusers.value=\'add\'">';
			  echo '<br><br><br>';
			  echo '<input type="image"  src="img/pfeillinks.jpg" alt="del" onclick="this.form.action_groupusers.value=\'del\'">';
		  echo '</td>';
		  echo '<td align="center" width="40%">';
		     echo "Benutzer in Gruppe";
		     echo '<br>'; 
		  	 build_select($group_members,"name","userid","deluser[]","multiple",18,"");
		  echo '</td>';
		echo '<tr>';
		echo '</table>';

	break;
	
	case 'add_user_group':
		echo "Neue Benutzer der Gruppe hinzuf&uuml;gen";
		$sql="select userid,name,vorname,disable from sys_users order by name";
		$rs->query($sql);
		while ($row=$rs->fetchRow()) {
			echo '<br>'.$row["userid"];
		}
		
	break;
	
	
	case "deluser_in_group":
		$deluserid=intval($_GET["userid"]);
		$groupid=intval($_GET["groupid"]);
		echo '<br><br>';
		$sql="select name,vorname from sys_users where userid=?";
		$rs->prepare($sql);
		$rs->bindColumn(1, $userid,PDO::PARAM_INT);
		$rs->execute();
		if ($row=$rs->fetchRow()) {
			echo '<b>'.$row["vorname"]." ".$row["name"];
		}
		echo "<br><br>wirklich aus der Gruppe l&ouml;schen ?</b>";
		echo '<br><br>';
		echo '<table>';
		echo '<tr>';
		table_link("Abbrechen",$baseurl."&action=groupusers");
		table_link("L&ouml;schen",$baseurl."&action=yesdel_user_in_group&groupid=".$groupid."&userid=".$deluserid);
		echo '</tr>';
		echo '</table>';
		echo '<br><br>';
	break;
	
	case "yesdel_user_in_group":
		$deluserid=intval($_GET["userid"]);
		$groupid=intval($_GET["groupid"]);
		$sql="delete from sys_user_group where userid=? and groupid=?";
		$rs->prepare($sql);
		$rs->bindColumn(1, $deluserid,PDO::PARAM_INT);
		$rs->bindColumn(2, $groupid,PDO::PARAM_INT);
		
		if (! $rs->execute()) {
			echo "Fehler beim L&ouml;schen!";
			echo $sql;
			exit();
		}
		echo "Benutzer erfolgreich aus der Gruppe gel&ouml;scht.";
		$url=$baseurl."&action=groupusers&groupid=".$groupid;
		echo '<SCRIPT TYPE="text/javascript">';
		echo 'setTimeout("location.href=\''.$url.'\'",1000);';
		echo '</SCRIPT>';
		exit;
		
	break;
}

 

?>