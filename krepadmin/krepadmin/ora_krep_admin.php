<?php

$url='index.php?menu=admin&PHPSESSID='.session_id();

if (!isset($_GET["action"]))
	$action="info";
else
	$action=$_GET["action"];



echo '<table width="100%" align="center">';
	echo '<tr>';
		table_link_menu('<b>Neue Tabelle hinzufügen</b>',$url."&action=askaddTable");
		table_link_menu('<b>Tabelle löschen</b>',$url."&action=delTable");
		table_link_menu('<b>Logs löschen</b>',$url."&action=delLogs");
		table_link_menu('<b>Validierung</b>',$url."&action=valid");
		table_link_menu('<b>PushJob</b>',$url."&action=pushjob");
		table_link_menu('<b>Replikation löschen</b>',$url."&action=delRep");
   echo '</tr>';
echo '</table>';

switch ($action) {
	case 'info':
   break;

	case 'pushjob':
		$sqlstr  = "select job,TO_CHAR(last_date,'".$DATEFORMAT."') L,";
		$sqlstr .= "TO_CHAR(next_date,'".$DATEFORMAT."') N,";
    	$sqlstr .= "broken from dba_jobs where what='".'begin rep$dorep; end;'."'";
      $result=getResult($db,$sqlstr);
		$result=$result["0"];
		echo '<h2>Push Job</h2>';
		echo '<table width="50%">';
			echo '<tr>';
				echo '<td align="center" colspan="2">';
					echo '<b>DBA_JOB</b>';
            echo '</td>';
         echo '</tr>';
      	echo '<tr>';
				table_data("Last_Date:");
            table_data($result["L"]);
         echo '</tr>';
      	echo '<tr>';
				table_data("Next_Date:");
            table_data($result["N"]);
         echo '</tr>';
      	echo '<tr>';
				table_data("Broken:");
            table_data($result["BROKEN"]);
         echo '</tr>';
			echo '<tr>';
			echo '<td align="center" colspan="2">';
				echo '<b>Job Status ändern</b>';
         echo '</td>';
         echo '</tr>';
         echo '<tr>';
				table_data("Status ändern");
				if ($result["BROKEN"]=="N")
	            table_link_menu("BROKEN=Y",$url."&action=change_job&job=TRUE&jobid=".$result["JOB"]);
				if ($result["BROKEN"]=="Y")
	            table_link_menu("BROKEN=N",$url."&action=change_job&job=FALSE&jobid=".$result["JOB"]);
            if (!isset($result["BROKEN"]))
  	            table_link_menu("Push Job erstellen",$url."&action=add_job".$result["JOB"]);

         echo '</tr>';
      echo '</table>';
   break;

	case 'add_job':
		// Lesen der Konfiguration aus der Datenbank
      $sqlstr='select PARAMETER,VALUE from REP$REPOS_CONFIG';
      $result=getResult($db,$sqlstr);
      if (!isset($result)) {
         echo '<br>Keine Replikation gefunden! ';
      } else {
         foreach ($result as $val) {
            if ($val["PARAMETER"]=="propagator") $propagator=$val["VALUE"];
            if ($val["PARAMETER"]=="dblink") $dblink=$val["VALUE"];
            if ($val["PARAMETER"]=="owner") $owner=$val["VALUE"];
         }
      }

	   $sqlexec ='declare ';
	   $sqlexec .=' j number; ';
	   $sqlexec .='begin ';
	   $sqlexec .=' dbms_job.submit (job=>j, what=>\'begin rep$dorep; end;\', next_date=>sysdate, interval=>\'sysdate+2/1440\');';
	   $sqlexec .=' commit;';
	   $sqlexec .='end;';
	   $result1=doSQL($db,$sqlexec);

	   if ($result)
	     echo "<br>Fehler beim des Push Jobs: ";
	   else
	     echo "<br>Push Job erfolgreich erstellt ";

	   $sqlstr="select job from dba_jobs where what='begin rep$dorep; end;'";
	   $result=getResult($db,$sqlstr);
	   if (isset($result)) {
	      $ret .= $result["0"]["JOB"];
	      if (isset($result["1"]["JOB"]))
	         echo "<br>Achtung es gibt mehr als einen Push Job";
	   }

	   if (!isset($result1)) {
	      echo '<br>Aktion wurde erfolgreich ausgeführt !';
	      wclose("");
	   } else {
	      wclose("back");
	   }
   break;


	case 'change_job':
      $sqlexec="begin dbms_job.broken(".$_GET["jobid"].",".$_GET["job"]."); end;";
      $result=doSQL($db,$sqlexec);
      if (!isset($result)) {
			echo '<br>Aktion wurde erfolgreich ausgeführt !';
         wclose("");
      } else {
			wclose("back");
      }
   break;

   case 'delLogs':
		echo '<h2>Leeren von Replikationstabellen/Logs</h2>';
     ?>
      <b>Folgende Logs/Tabellen können gelöscht werden:</b>
      <br>
		<table width="50%" class="none">
			<form method="POST" action="<?php echo $url."&action=dodelLogs"; ?> "/>
	         <tr>
	            <td align="center"><b>Logs/Tabellen</b></td>
	            <td align="center"><b>Leeren</b></td>
	         </tr>
	         <tr>
	            <td>Fehlerprotokoll</td>
	            <td align="center"><input type="checkbox" name="errorlog"/></td>
	         </tr>
	         <tr>
	            <td>Ausführungsprotokoll</td>
	            <td align="center"><input type="checkbox" name="pushlog"/></td>
	         </tr>
	         <tr>
	            <td>Replikationstabellen inkl. Journal</td>
	            <td align="center"><input type="checkbox" name="reptables"/></td>
	         </tr>
	         <tr>
	            <td align="center" colspan="2"><input type="submit" value="Leeren starten"/></td>
	         </tr>
			</form>
		</table>
      <?php
	break;

   case 'dodelLogs':
		if (isset($_POST["reptables"])) {
			echo '<br>Journal löschen';
         echo '<table width="70%">';
            echo '<tr>';
               echo '<td align="center"><b>Tabelle</b></td>';
               echo '<td align="center"><b>#</b></td>';
               echo '<td align="center"><b>del</b></td>';
            echo '</tr>';
               echo '<tr>';
                  $sqlstr="select count(*) anz from rep\$journal" ;
                  $result2=getResult($db,$sqlstr);
                     table_data("REP\$JOURNAL","left");
                     table_data(($result2["0"]["ANZ"]));
                     if ($result2["0"]["ANZ"]!=0) {
                        $sqlstr="delete from REP\$JOURNAL";
                        $result3=doSQL($db,$sqlstr);
                        if (!isset($result3))
                           table_data("Ok");
                        else
                           table_data("Fehler");
                     } else {
                           table_data("-");
                     }
               echo '</tr>';
         echo '</table>';
			echo '<br><br><br>';

      	echo '<br>Replikationstabellen löschen';
         $sqlstr='select TABLE_NAME from USER_TABLES where TABLE_NAME not like \'REP$%\' order by TABLE_NAME';
         $result=getResult($db,$sqlstr);
         if (!isset($result)) {
            echo '<h2>Keine Tabellen gefunden !</h2>';
         } else {
            echo '<table width="70%">';
               echo '<tr>';
                  echo '<td align="center"><b>Tabelle</b></td>';
                  echo '<td align="center"><b>#</b></td>';
                  echo '<td align="center"><b>del</b></td>';
               echo '</tr>';
               foreach ($result as $table) {
                  echo '<tr>';
                     $sqlstr="select count(*) anz from ".$table["TABLE_NAME"]." where REP\$ACTION<>'u'" ;
                     $result2=getResult($db,$sqlstr);
                        table_data($table["TABLE_NAME"],"left");
                        table_data(($result2["0"]["ANZ"]));
								if ($result2["0"]["ANZ"]!=0) {
	                        $sqlstr="delete from ".$table["TABLE_NAME"];
	                        $result3=doSQL($db,$sqlstr);
	                        if (!isset($result3))
	                           table_data("Ok");
	                        else
	                           table_data("Fehler");
								} else {
									   table_data("-");
                        }
                  echo '</tr>';
               }
            echo '</table>';
         }
      }

      if (isset($_POST["errorlog"])) {
      	echo '<br>Errorlog löschen';
         echo '<table width="70%">';
            echo '<tr>';
               echo '<td align="center"><b>Tabelle</b></td>';
               echo '<td align="center"><b>#</b></td>';
               echo '<td align="center"><b>del</b></td>';
            echo '</tr>';
               echo '<tr>';
                  $sqlstr="select count(*) anz from rep\$error" ;
                  $result2=getResult($db,$sqlstr);
                     table_data("REP\$ERROR","left");
                     table_data(($result2["0"]["ANZ"]));
                     if ($result2["0"]["ANZ"]!=0) {
                        $sqlstr="delete from REP\$ERROR";
                        $result3=doSQL($db,$sqlstr);
                        if (!isset($result3))
                           table_data("Ok");
                        else
                           table_data("Fehler");
                     } else {
                           table_data("-");
                     }
               echo '</tr>';
         echo '</table>';
			echo '<br><br><br>';
      }

      if (isset($_POST["pushlog"])) {
      	echo '<br>Ausführungslog löschen';
         echo '<table width="70%">';
            echo '<tr>';
               echo '<td align="center"><b>Tabelle</b></td>';
               echo '<td align="center"><b>#</b></td>';
               echo '<td align="center"><b>del</b></td>';
            echo '</tr>';
               echo '<tr>';
                  $sqlstr="select count(*) anz from rep\$log" ;
                  $result2=getResult($db,$sqlstr);
                     table_data("REP\$LOG","left");
                     table_data(($result2["0"]["ANZ"]));
                     if ($result2["0"]["ANZ"]!=0) {
                        $sqlstr="delete from REP\$LOG";
                        $result3=doSQL($db,$sqlstr);
                        if (!isset($result3))
                           table_data("Ok");
                        else
                           table_data("Fehler");
                     } else {
                           table_data("-");
                     }
               echo '</tr>';
         echo '</table>';
			echo '<br><br><br>';
      }
   break;

   case 'valid':
		echo '<h2>Validierung der Konfiguration</h2>';
     ?>
      <b>Es werden folgende Überprüfungen durchgeführt:</b>
      <br>
		<table width="50%" class="none"><tr><td>
      <ol start="1" type="1">
	         <li>Trigger auf dem zu replizierenden Schema</li>
	         <li>Stored Procedures des Propagators</li>
	         <li>Schattentabellen für die Replikation</li>
  	         <li>Struktur der Schattentabellen (Änderungen am PK werden nicht berücksichtigt)</li>
	         <li>Datenbanklink</li>
       </ol>
       </td></tr></table>
      <br>Die Überprüfung kann mehrere Minuten in Anspruch nehmen.
      <br>Soll die Überprüfung jetzt durchgeführt werden ?
		<br>
      <br>
      <?php
      ja_nein($url."&action=dovalid",$url);
	break;

	case 'dovalid':
		// Lesen der Konfiguration aus der Datenbank
      $sqlstr='select PARAMETER,VALUE from REP$REPOS_CONFIG';
      $result=getResult($db,$sqlstr);
      if (!isset($result)) {
         echo '<br>Keine Replikation gefunden! ';
      } else {
         foreach ($result as $val) {
            if ($val["PARAMETER"]=="propagator") $propagator=$val["VALUE"];
            if ($val["PARAMETER"]=="dblink") $dblink=$val["VALUE"];
            if ($val["PARAMETER"]=="owner") $owner=$val["VALUE"];
         }
      }


		// Lesen aller Replikationstabellen des Propagators
      $sqlstr='select TABLE_NAME from USER_TABLES where TABLE_NAME not like \'REP$%\' order by TABLE_NAME';
      $result=getResult($db,$sqlstr);
		$tables_propagator=array();
      foreach ($result as $row) {
			array_push($tables_propagator,$row["TABLE_NAME"]);
      }

		// Lesen der Trigger im SourceSchema
		$sqlstr  = "select trigger_name,status,table_name from all_triggers where ";
      $sqlstr .= "owner='".$owner."' and ";
		$sqlstr .= '(trigger_name like \'RD$%\' or trigger_name like \'RI$%\' or trigger_name like \'RU$%\')';
      $result=getResult($db,$sqlstr);
		$triggers=array();    // Beinhaltet alle Trigger
		$trigger_base=array();   // Beinhaltet nur die Namensstamm der Trigger ohne RD$, RI$ und RU$
      foreach ($result as $row) {
      	if (!in_array(substr($row["TRIGGER_NAME"],3),$trigger_base)) {
         	array_push($trigger_base,substr($row["TRIGGER_NAME"],3));
         }
			array_push($triggers,$row["TRIGGER_NAME"]);
         if ($row["STATUS"]!='ENABLED') {
				$error .= '<br> Trigger '.$row["TRIGGER_NAME"].' on '.$row["TABLENAME_NAME"].' is not ENABLED';
         }
      }

		// Lesen der Stored Procedures
		$sqlstr  = "select object_name,status from dba_objects where ";
      $sqlstr .= "owner='".$propagator."' and object_type='PROCEDURE' ";
		$sqlstr .= 'order by object_name';

      $result=getResult($db,$sqlstr);
		$procedures=array();
      foreach ($result as $row) {
			array_push($procedures,$row["OBJECT_NAME"]);
         if ($row["STATUS"]!='VALID') {
				$error .= '<br> PROCEDURE '.$propagator.'.'.$row["OBJECT_NAME"].' is not VALID';
         }
      }

		// Prüfung starten

		// DB Link prüfen
      $sqlstr="select TO_CHAR(sysdate,'".$DATEFORMAT."') TIME from dual@".$dblink;
      $result=getResult($db,$sqlstr);
		if (!isset($result)) {
		  	$error .= '<br> Fehler Datenbanklink: '.$dblink;
      }

		// Prüfen, ob es von jedem Trigger Delete, Insert und Update gibt.
		foreach ($trigger_base as $row) {
      	if (!in_array('RD$'.$row,$triggers))
         	$error .= '<br> Trigger RD$'.$row.' nicht vorhanden';
      	if (!in_array('RI$'.$row,$triggers))
         	$error .= '<br> Trigger RI$'.$row.' nicht vorhanden';
      	if (!in_array('RU$'.$row,$triggers))
         	$error .= '<br> Trigger RU$'.$row.' nicht vorhanden';
      }

      // Prüfen, ob es von jedem Trigger eine Schattentabelle und umgekehrt gibt.
		foreach ($trigger_base as $row) {
			if (!in_array($row,$tables_propagator))
         	$error .= '<br> Schattentabelle für Trigger '.$owner.'.'.$row.' nicht vorhanden';
      }
		foreach ($tables_propagator as $row) {
			if (!in_array($row,$trigger_base))
         	$error .= '<br> Trigger für Schattentabelle '.$propagator.'.'.$row.' nicht vorhanden';
      }

      // Prüfen, ob es von jeder Schattentabelle eine Stored Procedure gibt
		foreach ($tables_propagator as $row) {
			if (!in_array(strtolower($row),$procedures))
         	$error .= '<br> Stored Procedure für Schattentabelle '.$propagator.'.'.$row.' nicht vorhanden';
      }


		// Vergleich der Spalten der Source-Tabellen und Schattentabellen
      foreach ($tables_propagator as $table) {
			$sqlstr  = "select column_name,data_type,data_length from all_tab_columns where ";
			$sqlstr .= "owner='".$owner."' and ";
         $sqlstr .= "table_name='".$table."' order by column_name";
         $cols_owner=GetResult($db,$sqlstr);

			$sqlstr  = "select column_name,data_type,data_length from all_tab_columns where ";
			$sqlstr .= "owner='".$propagator."' and ";
         $sqlstr .= "table_name='".$table."' and column_name not like 'REP\$%' order by column_name";
         $cols_propagator=GetResult($db,$sqlstr);

         $idx=0;
         foreach ($cols_owner as $col) {
         	if ($col["COLUMN_NAME"] != $cols_propagator[$idx]["COLUMN_NAME"])
					$error .= '<br> Spalte '.$col["COLUMN_NAME"].' in Tabelle '.$table.' nicht in '.$propagator.'.'.$table.' an gleicher Stelle vorhanden';

         	if ($col["COLUMN_TYPE"] != $cols_propagator[$idx]["COLUMN_TYPE"])
					$error .= '<br> Type von '.$col["COLUMN_NAME"].' in Tabelle '.$table.' in '.$propagator.'.'.$table.' ist an gleicher Stelle unterschiedlich';

         	if ($col["DATA_LENGTH"] != $cols_propagator[$idx]["DATA_LENGTH"])
					$error .= '<br> Spaltenlänge von '.$col["COLUMN_NAME"].' in Tabelle '.$table.' in '.$propagator.'.'.$table.' ist an gleicher Stelle unterschiedlich';

				$idx++;
         }

      }

		if ($error=="") {
      	echo '<h2>Keine Fehler gefunden</h2>';
      } else {
			echo '<h2>Folgende Auffälligkeiten wurden in der Konfiguration gefunden: </h2>';
			echo $error;
      }
   break;



   case 'delRep':
		echo '<h2>Replikations-Setup löschen</h2>';
      echo clean_replikation('ask');
      echo '<br><br>Soll das Setup wirklich gelöscht werden ?<br>';
      ja_nein($url."&action=dodelRep",$url);
	break;

   case 'dodelRep':
		echo '<h2>Replikations-Setup wird gelöscht</h2>';
      echo clean_replikation('clean');
	break;




	case 'askaddTable':
		echo '<h3>Tabellen der Replikation hinzufügen</h3>';
		// Lesen der Konfiguration
	   $sqlstr='select PARAMETER,VALUE from REP$REPOS_CONFIG';
	   $result=getResult($db,$sqlstr);
	   if (!isset($result)) {
	     echo '<br>Keine Replikation gefunden! ';
	   } else {
	     foreach ($result as $val) {
	        if ($val["PARAMETER"]=="propagator") $propagator=$val["VALUE"];
	        if ($val["PARAMETER"]=="dblink") $dblink=$val["VALUE"];
	        if ($val["PARAMETER"]=="owner") $owner=$val["VALUE"];
	     }
	   }

		// Lesen aller Tabellen, die noch nich in der Replikation sind


      $sqlstr  = "select TABLE_NAME from ALL_TABLES where OWNER='".$owner."' and ";
      $sqlstr .= "TABLE_NAME not in (select TABLE_NAME from USER_TABLES)";
      $result=getResult($db,$sqlstr);
		echo '<form name="TABLESELECT"  action="'.$url.'&action=addTable" method="POST" />';
	      echo '<table width="40%">';
	         echo '<tr>';
	            echo '<td align="center">';
	               echo "Replikationsobjecte hinzufügen";
	            echo '</td>';
	         echo '</tr>';
	         echo '<tr>';
	            echo '<td align="center" width="50%">';
	               build_select($result,"TABLE_NAME","TABLE_NAME","tables[]","multiple",10);
	            echo '</td>';
	         echo '</tr>';
	         echo '<tr>';
	            echo '<td align="center" width="50%">';
	               echo '<input type="submit" value="Tabellen hinzufügen" />';
	            echo '</td>';
	         echo '</tr>';
	      echo '</table>';
      echo '</form>';
echo '<br><br><br>';
		echo '<form name="TABLELIST" action="'.$url.'&action=addTablelist" method="POST" />';
	      echo '<table width="40%">';
	         echo '<tr>';
	            echo '<td align="center">';
	               echo "Replikationsobjecte hinzufügen";
	            echo '</td>';
	         echo '</tr>';
	         echo '<tr>';
	            echo '<td align="center" width="50%">';
						echo '<textarea cols="32" rows="10" name="tablelist">';
                  echo '</textarea>';
	            echo '</td>';
	         echo '</tr>';
	         echo '<tr>';
	            echo '<td align="center" width="50%">';
	               echo '<input type="submit" value="Tabellen hinzufügen" />';
	            echo '</td>';
	         echo '</tr>';
	      echo '</table>';
      echo '</form>';




   break;

	case 'addTable':
		$tables=$_POST["tables"];
		foreach ($tables as $table) {
	      echo add_rep_object($table);
      }
   break;

	case 'addTablelist':
		$tablelist=$_POST["tablelist"];
		$tablelist=explode("\r\n",$tablelist);
		foreach ($tablelist as $table) {
	      echo add_rep_object($table);
      }
   break;


	case 'delTable':
		echo '<h3>Tabelle aus Replikation löschen</h3>';
		// Lesen aller Replikationstabellen des Propagators
      $sqlstr='select TABLE_NAME from USER_TABLES where TABLE_NAME not like \'REP$%\' order by TABLE_NAME';
      $result=getResult($db,$sqlstr);

		echo '<form action="'.$url.'&action=askdelTable" method="POST" />';
	      echo '<table width="40%">';
	         echo '<tr>';
	            echo '<td align="center">';
	               echo "Replikationsobjecte löschen";
	            echo '</td>';
	         echo '</tr>';
	         echo '<tr>';
	            echo '<td align="center" width="50%">';
	               build_select($result,"TABLE_NAME","TABLE_NAME","tables[]","multiple",10);
	            echo '</td>';
	         echo '</tr>';
	         echo '<tr>';
	            echo '<td align="center" width="50%">';
	               echo '<input type="submit" value="Objekte löschen" />';
	            echo '</td>';
	         echo '</tr>';
	      echo '</table>';
      echo '<form>';
   break;

	case 'askdelTable':
		$tables=$_POST["tables"];
		if (! isset($tables)) {
      	echo "<br>Keine Objekte ausgewählt";
      } else {
			echo '<form action="'.$url.'&action=dodelTable" method="POST" />';
         	echo "<br><b>Sollen folgende Replikationsobjekte wirklich gelöscht werden ?</b>";
            echo '<br>';
				$idx=0;
            foreach ($tables as $table) {
            	echo '<br>'.$table;
					echo '<input type="hidden" name="tables['.$idx++.']" value="'.$table.'" />';
            }
            echo '<br>';
            echo '<br>';
            echo '<input type="submit" value="Diese Objekte löschen" />';
         echo '</form>';
      }
	break;

	case 'dodelTable':
		$tables=$_POST["tables"];
		if (! isset($tables)) {
      	echo "<br>Keine Objekte ausgewählt";
      } else {
			echo '<h2>Objekte löschen:</h2>';
         foreach ($tables as $table) {
	         echo drop_rep_object($table);
			}
      }
	break;



}


?>