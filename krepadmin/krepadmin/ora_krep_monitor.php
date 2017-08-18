<?php

$url='index.php?menu=monitor&PHPSESSID='.session_id();

if (!isset($_GET["action"]))
	$action="info";
else
	$action=$_GET["action"];



echo '<table width="100%" align="center">';
	echo '<tr>';
		table_link_menu('<b>&nbsp;&nbsp;&nbsp;Info&nbsp;&nbsp;&nbsp;</b>',$url."&action=info");
		table_link_menu('<b>Replikationsjournal</b>',$url."&action=journal");
		table_link_menu('<b>Replikationstabellen</b>',$url."&action=transactions");
      table_link_menu('<b>Protokoll</b>',$url."&action=protocol");
      table_link_menu('<b>Replikationsfehler</b>',$url."&action=errors");

   echo '</tr>';
echo '</table>';
switch ($action) {
	case 'tablelist';
		$sqlstr="select OBJECT_NAME from dba_objects where object_type='TABLE' and owner='SDVS01'";
      $result=getResult($db,$sqlstr);
		foreach ($result as $row) {
      	echo '<br>grant select on '.$row["OBJECT_NAME"]." to SDVS_STATS;";
      }
		foreach ($result as $row) {
        	echo '<br>create public synonym '.$row["OBJECT_NAME"]." for SDVS_STATS01.".$row["OBJECT_NAME"].";" ;
      }



   break;
   case 'info':
		echo '<h2>Replikations Monitor</h2>';
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
	      echo '<h3>Zu replizierendes Schema: '.$owner.'</h3>';
	      echo '<h3>Replikationspropagator: '.$propagator.'</h3>';
	      echo '<h3>Verwendeter Datenbanklink: '.$dblink.'</h3>';
	      echo '<br><br>';
	      // Tabellen aus dem Replikation lesen
	      $sqlstr='select TABLE_NAME from USER_TABLES where TABLE_NAME not like \'REP$%\' order by TABLE_NAME';
	      $result=getResult($db,$sqlstr);
	      if (!isset($result)) {
	         echo '<h3>Keine Tabellen gefunden !</h3>';
	      } else {
	         echo '<h3>Replikationstabellen: </h3>';
	         $nextrow="";
	         echo '<textarea cols="32" rows="20" READONLY>';
	         foreach ($result as $table) {
	            echo $nextrow.$table["TABLE_NAME"];
	            $nextrow="\n";
	         }
	         echo '</textarea>';
	      }
      }
   break;

   case 'transactions':
		echo '<h3>Anzeige der ausstehenden Transaktionen</h3>';
      $sqlstr='select TABLE_NAME from USER_TABLES where TABLE_NAME not like \'REP$%\' order by TABLE_NAME';
      $result=getResult($db,$sqlstr);
      if (!isset($result)) {
      	echo '<h2>Keine Tabellen gefunden !</h2>';
      } else {
			echo '<table width="70%">';
         	echo '<tr>';
            	echo '<td align="center"><b>Tabelle</b></td>';
              	echo '<td align="center"><b>#</b></td>';
            echo '</tr>';
	         foreach ($result as $table) {
					echo '<tr>';
	            	$sqlstr="select count(*) anz from ".$table["TABLE_NAME"]." where REP\$ACTION<>'u'" ;
   	            $result2=getResult($db,$sqlstr);
							table_data($table["TABLE_NAME"],"left");
							table_data(($result2["0"]["ANZ"]));
					echo '</tr>';
            }
			echo '</table>';
      }
	break;

	case 'errors':
		$count_per_page=20;
		if (isset($_GET["page"])) {
      	$page=$_GET["page"];
      } else {
			$page=1;
      }

		$sqlstr="select count(*) ANZ from REP\$ERROR";
      $result=getResult($db,$sqlstr);
		$count=$result["0"]["ANZ"];
		if ($count%$count_per_page == 0)
			$count = floor($count / $count_per_page);
		else
			$count = floor($count / $count_per_page) + 1;

		echo '<h3>Anzeige der aufgelaufenen Replikationsfehler</h3>';

	   if ($count > 1)
	      {
	         echo '<BR><BR>Seite : ';
	         for ($x = 1; $x <= $count; $x++)
	         {
	            if ($x == $page)
	            {
	               echo ' <B>'.$x.'</B>';
	            }
	            else
	            {
	               echo ' <A HREF="'.$url.'&action=errors&page='.$x.'">';
	               echo $x;
	               echo '</A>';
	            }
	         }
	   }

      $sqlstr='select REP$ID ID,TO_CHAR(REP$TIME,\''.$DATEFORMAT.'\') TIME,ERR_CODE,ERR_TEXT from REP$ERROR order by REP$ID,TIME';
      $result=getResult($db,$sqlstr,(($page-1)*$count_per_page),$count_per_page);
      if (!isset($result)) {
      	echo '<h2>Keine Fehler gefunden !</h2>';
      } else {
			echo '<table width="70%">';
         	echo '<tr>';
            	table_data("ID");
              	table_data("TIME");
            	table_data("ERR_CODE");
              	table_data("ERR_TEXT");

            echo '</tr>';
	         foreach ($result as $table) {
					echo '<tr>';
							table_data($table["ID"]);
							table_data($table["TIME"]);
							table_data($table["ERR_CODE"]);
							table_data($table["ERR_TEXT"]);
					echo '</tr>';
            }
			echo '</table>';
      }
   break;

	case 'journal':
		echo '<h3>Anzeige der Journal-Tabelle für die Replikation</h3>';
		$count_per_page=30;
		if (isset($_GET["page"])) {
      	$page=$_GET["page"];
      } else {
			$page=1;
      }

		$sqlstr="select count(*) ANZ from REP\$JOURNAL";
      $result=getResult($db,$sqlstr);
		$count=$result["0"]["ANZ"];
		if ($count%$count_per_page == 0)
			$count = floor($count / $count_per_page);
		else
			$count = floor($count / $count_per_page) + 1;


	   if ($count > 1)
	      {
	         echo '<BR><BR>Seite : ';
	         for ($x = 1; $x <= $count; $x++)
	         {
	            if ($x == $page)
	            {
	               echo ' <B>'.$x.'</B>';
	            }
	            else
	            {
	               echo ' <A HREF="'.$url.'&action=journal&page='.$x.'">';
	               echo $x;
	               echo '</A>';
	            }
	         }
	   }

      $sqlstr='select REP$ID ID,REP$ACTION ACTION,REP$NAME NAME from REP$JOURNAL order by REP$ID';
      $result=getResult($db,$sqlstr,(($page-1)*$count_per_page),$count_per_page);
      if (!isset($result)) {
      	echo '<h2>Keine Einträge gefunden !</h2>';
      } else {
			echo '<table width="70%">';
         	echo '<tr>';
            	table_data("ID");
              	table_data("DML");
            	table_data("Tabelle");

            echo '</tr>';
	         foreach ($result as $table) {
					echo '<tr>';
                     table_data($table["ID"]);
							table_data($table["ACTION"]);
							$href="<a href='".$url."&action=stc&table=".$table["NAME"]."'>".$table["NAME"]."</a>";
							table_data($href);
					echo '</tr>';
            }
			echo '</table>';
      }
   break;


	case 'stc':
      $table=$_GET["table"];
		echo '<h3>Show Table Content</h3>';
		echo $table;
      echo '<br><br>';
		$sqlstr="select * from ".$table." order by rep\$id";
		$result=getResult($db,$sqlstr);

		if (isset($result)) {
      	echo '<table width="100%">';
				echo '<tr>';
	            foreach (array_keys($result["0"]) as $header) {
	               echo '<td align="center"><b>'.$header.'</b></td>';
	            }
				echo '</tr>';
				foreach ($result as $row) {
            	echo '<tr>';
	               foreach (array_keys($row) as $header) {
	                  table_data($row[$header]);
	               }
               echo '</tr>';
            }
         echo '</table>';
      }
   break;

	case 'protocol':
		$count_per_page=30;
		if (isset($_GET["page"])) {
      	$page=$_GET["page"];
      } else {
			$page=1;
      }

		$sqlstr="select count(*) ANZ from REP\$LOG order by REP\$TIME desc";
      $result=getResult($db,$sqlstr);
		$count=$result["0"]["ANZ"];
		if ($count%$count_per_page == 0)
			$count = floor($count / $count_per_page);
		else
			$count = floor($count / $count_per_page) + 1;

		echo '<h3>Replikations-Protokoll</h3>';

	   if ($count > 1)
	      {
	         echo '<BR><BR>Seite : ';
	         for ($x = 1; $x <= $count; $x++)
	         {
	            if ($x == $page)
	            {
	               echo ' <B>'.$x.'</B>';
	            }
	            else
	            {
	               echo ' <A HREF="'.$url.'&action=protocol&page='.$x.'">';
	               echo $x;
	               echo '</A>';
	            }
	         }
	   }
		echo '<br><br>';
      $sqlstr='select TO_CHAR(REP$TIME,\''.$DATEFORMAT.'\') TIME,REP$ERRORS ERRORS,REP$RECORDS RECORDS,DURATION from REP$LOG order by REP$TIME desc';
      $result=getResult($db,$sqlstr,(($page-1)*$count_per_page),$count_per_page);
      if (!isset($result)) {
      	echo '<h2>Keine Einträge gefunden !</h2>';
      } else {
			echo '<table width="70%">';
         	echo '<tr>';
            	table_data("FINISH-TIME");
            	table_data("DURATION");
              	table_data("ERRORS");
            	table_data("RECORDS");


            echo '</tr>';
	         foreach ($result as $table) {
					echo '<tr>';
							table_data($table["TIME"]);
							table_data(round($table["DURATION"]));
							table_data($table["ERRORS"]);
							table_data($table["RECORDS"]);
					echo '</tr>';
            }
			echo '</table>';
      }
   break;

}


?>