
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
sitename("adm_user.php",$_SESSION["groupid"]);
$baseurl="index.php?site=admin&admsite=user&PHPSESSID=".session_id();

if ($groupid==10 or die);

if (!isset($_GET["action"]))
	$action="start";
else 
	$action=$_GET["action"];
	
switch ($action) {
	case "start":
		echo '<a href="'.$baseurl.'&action=edit&userid=0">Neuen User anlegen</a>';
		$sql = "select login,userid,name,vorname,email,disable,groupid,nickname,last_login,valid_to,nologin,mobil from sys_users order by name,vorname";
		$rs->query($sql);
		echo '<table class="tablesorter" id="liste" >';
		echo '<thead>';
		echo '<tr>';
			echo '<th align="center">ID</th>';
			echo '<th align="center">Login</th>';
			echo '<th align="center">Name</th>';
			echo '<th align="center">email</th>';
			echo '<th align="center">Telegram ID</th>';
			echo '<th align="center">Letzte Anmeldung</th>';
			echo '<th align="center">gültig bis</th>';
			echo '<th align="center">Aktiv</th>';
			echo '<th align="center">Gruppe</th>';
			echo '<th align="center">L&ouml;schen</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
			while ($row=$rs->fetchRow()) {
				echo '<tr>';
				table_data('<a href="'.$baseurl."&action=edit&userid=".$row["userid"].'">'.$row["userid"].'</a>');
			
				table_data($row["login"]);
				table_data($row["name"].", ".$row["vorname"]);
				table_data($row["email"]);
				table_data($row["mobil"]);
				if (isset($row["last_login"])) 
					table_data(date('d.m.Y',strtotime($row["last_login"])));
				else
					table_data("&nbsp;");
				table_data(date('d.m.Y',strtotime($row["valid_to"])));
				if ($row["disable"]=="0") $tmp="ja"; else $tmp="nein";
				table_data($tmp);
			
				table_data($row["groupid"]);
				if ($row["userid"]!=1)
					table_data('<a href="'.$baseurl."&action=del&userid=".$row["userid"].'"><img src="img/del.jpg"></a>');
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
		$userid=intval($_GET["userid"]);
		$sql="delete from sys_users where userid=".$userid;
		if (! $rs->query($sql)) {
			echo "Fehler beim L&ouml;schen!";
			exit();
		}
		echo "User erfolgreich gel&ouml;scht.";
		$url=$baseurl;
		echo '<SCRIPT TYPE="text/javascript">';
		echo 'setTimeout("location.href=\''.$url.'\'",1000);';
		echo '</SCRIPT>';
		exit;
		
	break;
	case "del":
		$userid=intval($_GET["userid"]);
		$baseurl="index.php?site=admin&admsite=user&PHPSESSID=".session_id();
		$sql = "select ";
		$sql.= "login,name,vorname ";
		$sql.= "from sys_users ";
		$sql.= "where userid=".$userid;
	
		$rs->query($sql) or die(1);
		if ($row=$rs->fetchRow()) {
			echo '<br><br>';
			echo "User ".$row["login"]." (".$row["vorname"]." ".$row["name"].") wirklich l&ouml;schen?";
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
			table_link("L&ouml;schen",$baseurl."&action=yesdel&userid=".$userid);
			echo '</tr>';
			echo '</table>';
			echo '<br>';
			echo '<br>';
			echo '<br>';
		}
	break;
	case "edit":
		$userid=intval($_GET["userid"]);
		
		$baseurl="index.php?site=admin&admsite=user&PHPSESSID=".session_id();
		if ($_POST["action1"]=="save") {
			$dosql=1;
			// Passwort Pin Abfrage
			if ($_POST["password1"]!=$_POST["password2"]) {
				echo "<br><b>Fehler - Passwort stimmen nicht überein</b>";
				$dosql=0;
			}   
			if ($_POST["pin1"]!=$_POST["pin2"]) {
				echo "<br><b>Fehler - PIN stimmt nicht überein</b>";
				$dosql=0;
			}   
			
			if ($userid==0 && strlen($_POST["login"])<4) {echo "Fehler login weniger als 4 Zeichen";  $dosql=0;}	
			
			
			if ($_POST["password1"]!="") $pwd="passwordmd5=md5('".$_POST["password1"]."'),";
			if ($_POST["pin1"]!="") $pin="pin=".$_POST["pin1"].",";
			
			if ($dosql==1) {
				if (intval($userid)>0) $sql = "update sys_users set "; else $sql="insert into sys_users set ";
				$sql.= $pwd;
				$sql.= $pin;
				$varname="name"; $val=$_POST[$varname];  $sql.= $varname."='".addslashes($val)."',";
				$varname="vorname"; $val=$_POST[$varname];  $sql.= $varname."='".addslashes($val)."',";
				$varname="nickname"; $val=$_POST[$varname];  $sql.= $varname."='".addslashes($val)."',";
				$varname="email"; $val=$_POST[$varname];  $sql.= $varname."='".addslashes($val)."',";
				$varname="disable"; $val=$_POST[$varname];  $sql.= $varname."='".intval($val)."',";
				                                            $sql.= "groupid"."='".intval("1")."',";
				
				if ($userid==0) {$varname="login"; $val=$_POST[$varname];  $sql.= $varname."='".addslashes($val)."',";}
				
				
				$varname="mobil";
				if (preg_match('/^[0-9 ]+$/', $_POST[$varname]) || $_POST[$varname]=="") {
					$val=$_POST[$varname];  $sql.= $varname."='".addslashes($val)."',";
				} else {
					$sql.="Fehler Telegram chatid";
				}
				
				//$varname="disable"; $sql.= $varname."=".intval($_POST[$varname]).", ";
				$varname="nologin"; $sql.= $varname."=".intval($_POST[$varname]).", ";
				$varname="valid_to"; $sql.= $varname."='".convertdate($_POST[$varname])."' ";
				if (intval($userid)>0) $sql.="where userid=".$userid;
				
		
				if (!$rs->query($sql)) {
					echo "Die Daten konnten nicht gespeichert werden!";
				} else {
					// Neuen Benutzer der Defaultgruppe hunzufügen
					if (intval($userid)==0) {
						$lid=$rs->lId();
						$sql="insert into sys_user_group (userid,groupid) values (?,1)";
						$rs->prepare($sql);
						$rs->bindColumn(1,$lid);
						$rs->execute();
					}
					
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
		if (intval($userid!=0)) {
			$sql = "select ";
			$sql.= "login,name,vorname,email,groupid,disable,nickname,valid_to,nologin,mobil ";
			$sql.= "from sys_users ";
			$sql.= "where userid=".$userid;
			$rs->query($sql);
			if ($row=$rs->fetchRow() or die);
		} else {
		    // Neuen Benutzer anlegen
			//$row["valid_to"]=date("d.m.Y");
			$row["valid_to"]="01.01.2030";
		    $row["disable"]=0;
		    $userid=0;
		}
		
		
		echo '<FORM name="eingabe" method="post" action="'.$baseurl.'&action=edit&userid='.$userid.'">';
		echo '<table class="layout" width="80%" align="center">';
		echo '<tr>';
		echo '<td width="50%"></td>';
		echo '<td width="50%"></td>';
		echo '</tr>';	

		if ($userid==0) {
			echo '<tr>';
			$varname=login;
			if (isset($_POST[$varname])) $val=$_POST[$varname]; else $val=$row[$varname];
			table_data("Login:&nbsp;",right);
			table_input($varname,"40","left",$val,'style="font-size:90%" tabindex="1"');
			echo '</tr>';
		}
		
		echo '<tr>';
			$varname=vorname; 
			if (isset($_POST[$varname])) $val=$_POST[$varname]; else $val=$row[$varname]; 
			table_data("Vorname:&nbsp;",right);
			table_input($varname,"40","left",$val,'style="font-size:90%" tabindex="2"');
		echo '</tr>';
		
		echo '<tr>';
			$varname=name; 
			if (isset($_POST[$varname])) $val=$_POST[$varname]; else $val=$row[$varname]; 
			table_data("Nachname:&nbsp;",right);
			table_input($varname,"40","left",$val,'style="font-size:90%" tabindex="3"');
		echo '</tr>';
/*
		echo '<tr>';
			$varname=nickname; 
			if (isset($_POST[$varname])) $val=$_POST[$varname]; else $val=$row[$varname]; 
			table_data("Nickname:&nbsp;",right);
			table_input($varname,"40","left",$val,'style="font-size:90%" tabindex="2"');
		echo '</tr>';
*/
		
		echo '<tr>';
			$varname=email; 
			if (isset($_POST[$varname])) $val=$_POST[$varname]; else $val=$row[$varname]; 
			table_data("email:&nbsp;",right);
			table_input($varname,"40","left",$val,'style="font-size:90%" tabindex="4"');
		echo '</tr>';
		
		echo '<tr>';
		$varname=mobil;
		if (isset($_POST[$varname])) $val=$_POST[$varname]; else $val=$row[$varname];
		table_data("Telegram ChatID:&nbsp;",right);
		table_input($varname,"40","left",$val,'style="font-size:90%" tabindex="5"');
		echo '</tr>';
		
		echo '<tr>';
			$varname=valid_to; 
			if (isset($_POST[$varname])) $val=$_POST[$varname]; else $val=date('d.m.Y',strtotime($row[$varname])); 
			table_data("gültig bis:&nbsp;",right);
			table_input($varname,"40","left",$val,'style="font-size:90%" tabindex="6"');
		echo '</tr>';
		

		echo '<tr>';
			$varname=disable; 
			if (isset($_POST[$varname])) $val=$_POST[$varname]; else $val=$row[$varname];
			table_data("Aktiv:&nbsp;",right);
			if ($val=="0") $checked="checked"; else $checked="";
			$cnt='Aktiv:<input type="Radio" name="'.$varname.'" tabindex="5" value="0" '.$checked.'>';
			if ($val=="1") $checked="checked"; else $checked="";
			$cnt.='&nbsp;&nbsp;&nbsp;Deaktiviert:<input type="Radio" name="'.$varname.'" tabindex="7" value="1" '.$checked.'>';     
			echo '<td bgcolor="#FFFFFF" align="left">';
			   echo '<font size="-1">'.$cnt.'</font>';
			echo '</td>';
		echo '</tr>';
		
        if ($userid==0) {
			echo '<tr>';
				table_data("Alarmierungsgruppe:&nbsp;",right);
				table_data("_DEFAULT_",left);
			echo '</tr>';
        }
		/*
		echo '<tr>';
			$varname=nologin; 
			if (isset($_POST[$varname])) $val=$_POST[$varname]; else $val=$row[$varname];
			table_data("Passiver Benutzer:&nbsp;",right);
			if ($val=="1") $checked="checked"; else $checked="";
			$cnt='Ja:<input type="Radio" name="'.$varname.'" tabindex="5" value="1" '.$checked.'>';
			if ($val=="0") $checked="checked"; else $checked="";
			$cnt.='&nbsp;&nbsp;&nbsp;Nein:<input type="Radio" name="'.$varname.'" tabindex="5" value="0" '.$checked.'>';     
			echo '<td bgcolor="#FFFFFF" align="left">';
			   echo '<font size="-1">'.$cnt.'</font>';
			echo '</td>';
		echo '</tr>';
		*/
		echo '<tr>';
			echo '<td colspan="2" align="center">';
				$cnt="<b>Für die Speicherung ohne Passwortänderung Felder leer lassen.</b>";
				echo '<font size="-1">'.utf8_encode($cnt).'</font>';
			echo '</td>';
		echo '</tr>';
		
		echo '<tr>';
			$varname=password1; 
			if (isset($_POST[$varname])) $val=$_POST[$varname]; else $val=$row[$varname]; 
			table_data("Passwort:&nbsp;",right);
			table_inputpwd($varname,"40","left",$val,'style="font-size:90%" tabindex="8"');
		echo '</tr>';

		echo '<tr>';
			$varname=password2; 
			if (isset($_POST[$varname])) $val=$_POST[$varname]; else $val=$row[$varname]; 
			table_data("Password wiederholen:&nbsp;",right);
			table_inputpwd($varname,"40","left",$val,'style="font-size:90%" tabindex="9"');
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
}

 

?>