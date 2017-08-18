<?php
sitename("messagelog.php",$_SESSION["groupid"]);
if (priv("messagelog"))
{
    // Datum setzen, falls noch nicht üebergeben
    if (!isset($_GET["datum"])) 
    	$_GET["datum"]=date('d.m.Y');
    	
	$sqlstr = "select date_format(ts,'%d.%m.%Y') datum,count(*) anz from sys_log group by datum order by ts desc";
	$result = getResult($db,$sqlstr);
    $url = 'index.php?site=mlog&PHPSESSID='.session_id();
	echo "<b>Datumsauswahl:</b> ";
	echo '<select onChange="window.location.href=\''.$url.'&datum=\'+this.value;">';
		foreach ($result as $row) {
			if ($_GET["datum"] == $row["datum"]) {
				echo "<option selected value=\"" . $row["datum"] . "\">";
			} else {
				echo "<option value=\"" . $row["datum"] . "\">";
			}
			echo $row["datum"]." (".$row["anz"].")";
			echo '</option>';
		}
	echo '</select>';

	echo '<br><br>';
	echo '<TABLE WIDTH="80%" BORDER="0">';
        echo '<TR>';
                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                        echo '<B>Datum</B>';
                echo '</TD>';
                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                        echo '<B>User</B>';
                echo '</TD>';
                echo '<TD ALIGN="CENTER" BGCOLOR="#DDDDDD">';
                        echo '<B>Aktion</B>';
                echo '</TD>';
		echo '</TR>';
	
		$sqlstr = "select DATE_FORMAT(ts,'%H:%i:%s %d.%m.%Y') ts,user,message from sys_log where date_format(ts,'%d.%m.%Y') = '".$_GET["datum"]."' order by id desc";
		$result=getResult($db,$sqlstr);
		
		if (!isset($result)) {
			echo '<tr><td colspan="3" align="center"><b>Keine Ereignisse von heute ...</b></td></tr>';
		} else {
			foreach ($result as $row) {
				echo '<tr>';
					echo '<td align="center">';
						echo $row["ts"];
					echo '</td>';
					echo '<td align="center">';
						echo $row["user"];
					echo '</td>';
					echo '<td align="center">';
						echo $row["message"];
					echo '</td>';
				echo '</tr>';
			}
		}
   echo '</table>';
	
}
else
	echo $no_rights;
?>