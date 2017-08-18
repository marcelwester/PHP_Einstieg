<?php

function add_push_job() {
	$sqlexec ='declare ';
	$sqlexec .=' j number; ';
	$sqlexec .='begin ';
	$sqlexec .=' dbms_job.submit (job=>j, what=>\'begin rep$dorep; end;\', next_date=>sysdate, interval=>\'sysdate+2/1440\',broken=>TRUE);';
	$sqlexec .=' commit;';
	$sqlexec .='end;';
   $result=doSQL($db,$sqlexec);
   if ($result)
     $ret .= "<br>Fehler beim des Push Jobs: ";
   else
     $ret .= "<br>Push Job erfolgreich erstellt ";

   $sqlstr="select job from dba_jobs where what='begin rep$dorep; end;'";
   $result=getResult($db,$sqlstr);
   if (isset($result)) {
   	$ret .= $result["0"]["JOB"];
		if (isset($result["1"]["JOB"]))
      	$ret .= "<br>Achtung es gibt mehr als einen Push Job";
   }
}



function drop_rep_object ($object) {
global $db;
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
	$ret="";
	if (isset($owner) && isset($propagator)) {
	   // Delete-Trigger löschen
	   $sqlexec="DROP TRIGGER ".$owner.'."RD$'.$object.'"';
	   $result=doSQL($db,$sqlexec);
	   if ($result)
	     $ret .= "<br>Fehler beim Löschen von Trigger ".$owner.'.RD$'.$object;
	   else
	     $ret .= "<br>Trigger ".$owner.'.RD$'.$object."  gelöscht";


	   // Insert-Trigger löschen
	   $sqlexec="DROP TRIGGER ".$owner.'."RI$'.$object.'"';
	   $result=doSQL($db,$sqlexec);
	   if ($result)
	     $ret .= "<br>Fehler beim Löschen von Trigger ".$owner.'.RI$'.$object;
	   else
	     $ret .= "<br>Trigger ".$owner.'.RI$'.$object."  gelöscht";


	   // Update-Trigger löschen
	   $sqlexec="DROP TRIGGER ".$owner.'."RU$'.$object.'"';
	   $result=doSQL($db,$sqlexec);
	   if ($result)
	     $ret .= "<br>Fehler beim Löschen von Trigger ".$owner.'.RU$'.$object;
	   else
	     $ret .= "<br>Trigger ".$owner.'.RU$'.$object."  gelöscht";


	   // Stored Procedure löschen
	   $sqlexec="DROP PROCEDURE ".$propagator.'."'.strtolower($object).'"';
	   $result=doSQL($db,$sqlexec);
	   if ($result)
	     $ret .= "<br>Fehler beim Löschen von Stored Procedure ".$propagator.'.'.$strtolower($object);
	   else
	     $ret .= "<br>Stored Procedure ".$propagator.'.'.strtolower($object)."  gelöscht";


	   // Schattentabelle löschen
	   $sqlexec="DROP TABLE ".$propagator.'."'.$object.'"';
	   $result=doSQL($db,$sqlexec);
	   if ($result)
	     $ret .= "<br>Fehler beim Löschen von Schattentabelle ".$propagator.'.'.$strtolower($object);
	   else
	     $ret .= "<br>Schattentabelle ".$propagator.'.'.strtolower($object)."  gelöscht";


	} else {
   	$ret .= "<br>Konfiguration nicht vollständig";
   }
   return $ret;
}


function add_rep_object ($table) {
global $db;
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

	// Anfang: Erstellen der Schattentabelle
	   $sqlexec='create table '.$propagator.'.'.$table.' storage (initial 64k next 64k) as select -1 rep$id,\'x\' rep$action,'.$table.'.* from '.$owner.'.'.$table.' where 0=1';
	   $result=doSQL($db,$sqlexec);
	   if ($result)
	     $ret .= "<br>Fehler beim Erstellen der Tabelle ".$owner.'.'.$table;
	   else
	     $ret .= "<br>Tabelle ".$owner.'.'.$table." angelegt";

	   $sqlexec='alter table '.$propagator.'.'.$table.' add primary key (REP$ID,REP$ACTION)';
	   $result=doSQL($db,$sqlexec);
	   if ($result)
	     $ret .= "<br>Fehler beim Anlegen des PK ".$owner.'.'.$table;
	   else
	     $ret .= "<br>PK für ".$owner.'.'.$table."  erfolgreich angelegt";

	   $sqlexec='grant insert on '.$propagator.'.'.$table.' to '.$owner;
	   $result=doSQL($db,$sqlexec);
	   if ($result)
	     $ret .= "<br>Grant to ".$owner." on ".$propagator.'.'.$table.' fehlgeschlagen';
	   else
	     $ret .= "<br>Grant to ".$owner." on ".$propagator.'.'.$table.' ausgeführt';
	// Ende Erstellen der Schattentabelle

/*
	// Anfang: Erstellen der Stored Procedure (Alte Methode ohne Cursor)
  		  $sqlexec ='CREATE OR REPLACE PROCEDURE "'.strtolower($table).'" (id in number, action in char) as'." ";
	     $sqlexec .='begin'." ";
	     $sqlexec .="if 'I' = action then"." ";
	         $sqlstr="select column_name from all_tab_columns where owner='".$owner."' and table_name='".$table."' order by column_id";
	         $cols=getResult($db,$sqlstr);
	         $komma="";
	         $collist="";
	         foreach ($cols as $col) {
	            $collist .= $komma.$col["COLUMN_NAME"];
               $komma=",";
	         }
	        $sqlexec .="insert into ".$table."@".$dblink." "." ";
	        $sqlexec .="(".$collist.") ";
	        $sqlexec .=" (select ".$collist."  ";
	        $sqlexec .=" from ".$table.' where rep$id=id);'."  ";
		  $sqlexec .="elsif 'U' = action then  ";
           $sqlexec .="update ".$table."@".$dblink." set "." ";
           $sqlexec .="(".$collist.") ";
           $sqlexec .=" = (select "." ";
           $sqlexec .=$collist." ";
				//pkliste der  Tabelle erzeugen
            $sqlstr  = "select column_name from all_constraints ac, all_cons_columns acc where ";
				$sqlstr .= "ac.owner='".$owner."' and ";
  				$sqlstr .= "ac.table_name='".$table."' and ";
            $sqlstr .= "ac.constraint_type='P' and ";
            $sqlstr .= "ac.owner=acc.owner and ";
				$sqlstr .= "ac.table_name=acc.table_name and ";
				$sqlstr .= "ac.constraint_name=acc.constraint_name";
				$cols=getResult($db,$sqlstr);
	         $komma=""; $pkcollist="";
				foreach ($cols as $col) {
	            $pkcollist .= $komma.$col["COLUMN_NAME"];
               $komma=",";
            }
           $sqlexec .=" from ".$table.' where rep$id=id and rep$action=\'U\') where '." ";
			  $sqlexec .="(".$pkcollist.") in (select  ";
           $sqlexec .=$pkcollist." from ".$table.' where rep$id=id and rep$action=\'u\');'."  ";
		  $sqlexec .="elsif 'D' = action then  ";
			  $sqlexec .="delete from ".$table."@".$dblink." where (  ";
			  $sqlexec .=$pkcollist." ) in (select  ";
           $sqlexec .=$pkcollist." from ".$table.' where rep$id=id);'." ";
        $sqlexec .="end if;  ";
        $sqlexec .="end; ";

  	      $result=doSQL($db,$sqlexec);
	      if ($result)
	        $ret .= "<br>Fehler beim Erstellen der Stored Procedure ".$propagator.'.'.$table;
	      else
	        $ret .= "<br>Stored Procedure ".$propagator.'.'.$table." erfolgreich erstellt";

	// Ende: Erstellen der Stored Procedure
*/



	        $sqlstr  = "select count(*) ANZ from all_tab_columns where owner='".$owner."' and table_name='".$table."' and data_type in ";
	        $sqlstr .= "('BLOB','CLOB')";
		$cols=getResult($db,$sqlstr);
            	if ($cols["0"]["ANZ"] > 0) {
	       	// Anfang: Erstellen der Stored Procedure (ohne Cursor)
               	echo '<br><b>Die Tabelle '.$owner.'.'.$table.' enthält binäre Spalten: '.$cols["0"]["ANZ"].'</b>';
	                 $sqlexec ='CREATE OR REPLACE PROCEDURE "'.strtolower($table).'" (id in number, action in char) as'." ";
	                 $sqlexec .='begin'." ";
	                 $sqlexec .="if 'I' = action then"." ";
	                     $sqlstr="select column_name from all_tab_columns where owner='".$owner."' and table_name='".$table."' order by column_id";
	                     $cols=getResult($db,$sqlstr);
	                     $komma="";
	                     $collist="";
	                     foreach ($cols as $col) {
	                        $collist .= $komma.$col["COLUMN_NAME"];
	                        $komma=",";
	                     }
	                    $sqlexec .="insert into ".$table."@".$dblink." "." ";
	                    $sqlexec .="(".$collist.") ";
	                    $sqlexec .=" (select ".$collist."  ";
	                    $sqlexec .=" from ".$table.' where rep$id=id);'."  ";
	                 $sqlexec .="elsif 'U' = action then  ";
	                    $sqlexec .="update ".$table."@".$dblink." set "." ";
	                    $sqlexec .="(".$collist.") ";
	                    $sqlexec .=" = (select "." ";
	                    $sqlexec .=$collist." ";
	                     //pkliste der  Tabelle erzeugen
	                     $sqlstr  = "select column_name from all_constraints ac, all_cons_columns acc where ";
	                     $sqlstr .= "ac.owner='".$owner."' and ";
	                     $sqlstr .= "ac.table_name='".$table."' and ";
	                     $sqlstr .= "ac.constraint_type='P' and ";
	                     $sqlstr .= "ac.owner=acc.owner and ";
	                     $sqlstr .= "ac.table_name=acc.table_name and ";
	                     $sqlstr .= "ac.constraint_name=acc.constraint_name";
	                     $cols=getResult($db,$sqlstr);
	                     $komma=""; $pkcollist="";
	                     foreach ($cols as $col) {
	                        $pkcollist .= $komma.$col["COLUMN_NAME"];
	                        $komma=",";
	                     }
	                    $sqlexec .=" from ".$table.' where rep$id=id and rep$action=\'U\') where '." ";
	                    $sqlexec .="(".$pkcollist.") in (select  ";
	                    $sqlexec .=$pkcollist." from ".$table.' where rep$id=id and rep$action=\'u\');'."  ";
	                 $sqlexec .="elsif 'D' = action then  ";
	                    $sqlexec .="delete from ".$table."@".$dblink." where (  ";
	                    $sqlexec .=$pkcollist." ) in (select  ";
	                    $sqlexec .=$pkcollist." from ".$table.' where rep$id=id);'." ";
	                 $sqlexec .="end if;  ";
	                 $sqlexec .="end; ";

	                  $result=doSQL($db,$sqlexec);
	                  if ($result)
	                    $ret .= "<br>Fehler beim Erstellen der Stored Procedure ".$propagator.'.'.$table;
	                  else
	                    $ret .= "<br>Stored Procedure ".$propagator.'.'.$table." erfolgreich erstellt";

						// Ende: Erstellen der Stored Procedure (ohne Cursor)
               } else {
	                  // Anfang: Erstellen der Stored Procedure(mit Cursor)

	                  // idx aus Datenbanktabelle lesen
	                  $sqlstr="select VALUE from REP\$REPOS_CONFIG where PARAMETER='idx'";
	                  $result=getResult($db,$sqlstr);
	                  if (isset($result["0"]["VALUE"])) {
	                     $idx=$result["0"]["VALUE"];
	                     $idx++;
	                     $sqlstr="update REP\$REPOS_CONFIG set value='".$idx."' where parameter='idx'";
	                     $result=doSQL($db,$sqlstr);
	                  } else {
	                     return "Fehler beim Erstellen der Stored Procedure";
	                  }


	                  // collist der Tabelle erzeugen
	                  $sqlstr="select column_name from all_tab_columns where owner='".$owner."' and table_name='".$table."' order by column_id";
	                  $cols=getResult($db,$sqlstr);


	                  //pkliste der  Tabelle erzeugen
	                  $sqlstr  = "select column_name from all_constraints ac, all_cons_columns acc where ";
	                  $sqlstr .= "ac.owner='".$owner."' and ";
	                  $sqlstr .= "ac.table_name='".$table."' and ";
	                  $sqlstr .= "ac.constraint_type='P' and ";
	                  $sqlstr .= "ac.owner=acc.owner and ";
	                  $sqlstr .= "ac.table_name=acc.table_name and ";
	                  $sqlstr .= "ac.constraint_name=acc.constraint_name";
	                  $pkcols=getResult($db,$sqlstr);



	                     $sqlexec  ="CREATE OR REPLACE PROCEDURE \"".strtolower($table)."\" (id in number, action in char) as "." ";
	                     $sqlexec .="rec1 ".$table."%ROWTYPE;"." ";
	                     $sqlexec .="rec2 ".$table."%ROWTYPE;"." ";
	                     $sqlexec .="ignore INTEGER;"." ";
	                     $sqlexec .="begin"." ";
	                     $sqlexec .="if 'I' = action then"." ";
	                     $sqlexec .="if 0 = kis_rep.flags_i(".$idx.") then"." ";
	                     $sqlexec .="kis_rep.cursor_i(".$idx.") := DBMS_SQL.OPEN_CURSOR;"." ";
	                     $sqlexec .="kis_rep.flags_i(".$idx.") := 1;"." ";
	                     $sqlexec .="DBMS_SQL.PARSE(kis_rep.cursor_i(".$idx."), 'insert into ".$table."@".$dblink." ('"." ";
	                     $komma="";
	                     foreach ($cols as $col) {
	                        $sqlexec .="||'".$komma.$col["COLUMN_NAME"]."'"." ";
	                        $komma=",";
	                     }
	                     $sqlexec .="||') values ('"." ";
	                     $komma="";
	                     foreach ($cols as $col) {
	                        $sqlexec .="||'".$komma.":".$col["COLUMN_NAME"]."'"." ";
	                        $komma=",";
	                     }
	                     $sqlexec .="||')' , DBMS_SQL.native);"." ";
	                     $sqlexec .="end if;"." ";
	                     //$sqlexec .=" ";
	                     $sqlexec .="select * into rec1 from ".$table." where rep\$id = id;"." ";
	                     //$sqlexec .=" ";
	                     foreach ($cols as $col) {
	                       $sqlexec .="DBMS_SQL.BIND_VARIABLE(kis_rep.cursor_i(".$idx."), ':".$col["COLUMN_NAME"]."', rec1.".$col["COLUMN_NAME"].");"." ";
	                     }
	                     $sqlexec .="ignore := DBMS_SQL.EXECUTE(kis_rep.cursor_i(".$idx."));"." ";
	                     //$sqlexec .=" ";

	                     $sqlexec .="elsif 'U' = action then"." ";
	                     $sqlexec .="if 0 = kis_rep.flags_u(".$idx.") then"." ";
	                     $sqlexec .="kis_rep.cursor_u(".$idx.") := DBMS_SQL.OPEN_CURSOR;"." ";
	                     $sqlexec .="kis_rep.flags_u(".$idx.") := 1;"." ";
	                     $sqlexec .="DBMS_SQL.PARSE(kis_rep.cursor_u(".$idx."), 'update ".$table."@".$dblink." set '"." ";
	                     $komma="";
	                     foreach ($cols as $col) {
	                        $sqlexec .="||'".$komma.$col["COLUMN_NAME"]." =:".$col["COLUMN_NAME"]."'"." ";
	                        $komma=",";
	                     }
	                     $sqlexec .="||' where '"." ";
	                     $komma="";
	                     foreach ($pkcols as $pkcol) {
	                        $sqlexec .="||'".$komma.$pkcol["COLUMN_NAME"]."=:"."old_".$pkcol["COLUMN_NAME"]."'"." ";
	                        $komma=" and ";
	                     }
	                     $sqlexec .=" , DBMS_SQL.native);"." ";
	                     $sqlexec .="end if;"." ";
	                     //$sqlexec .=" ";
	                     $sqlexec .="select * into rec1 from ".$table." where rep\$id=id and rep\$action='U';"." ";
	                     $sqlexec .="select * into rec2 from ".$table." where rep\$id=id and rep\$action='u';"." ";
	                     //$sqlexec .=" ";
	                     foreach ($cols as $col) {
	                        $sqlexec .="DBMS_SQL.BIND_VARIABLE(kis_rep.cursor_u(".$idx."), ':".$col["COLUMN_NAME"]."', rec1.".$col["COLUMN_NAME"].");"." ";
	                     }
	                     //$sqlexec .=" ";
	                     foreach ($pkcols as $pkcol) {
	                        $sqlexec .="DBMS_SQL.BIND_VARIABLE(kis_rep.cursor_u(".$idx."), ':old_".$pkcol["COLUMN_NAME"]."', rec2.".$pkcol["COLUMN_NAME"].");"." ";
	                     }
	                     //$sqlexec .=" ";
	                     $sqlexec .="ignore := DBMS_SQL.EXECUTE(kis_rep.cursor_u(".$idx."));"." ";
	                     //$sqlexec .=" ";


	                     $sqlexec .="elsif 'D' = action then"." ";
	                     $sqlexec .="if 0 = kis_rep.flags_d(".$idx.") then"." ";
	                       $sqlexec .="kis_rep.cursor_d(".$idx.") := DBMS_SQL.OPEN_CURSOR;"." ";
	                       $sqlexec .="kis_rep.flags_d(".$idx.") := 1;"." ";
	                       $sqlexec .="DBMS_SQL.PARSE(kis_rep.cursor_d(".$idx."), 'delete from ".$table."@".$dblink." where '"." ";
	                       $komma="";
	                       foreach ($pkcols as $pkcol) {
	                           $sqlexec .="||'".$komma.$pkcol["COLUMN_NAME"]."=:".$pkcol["COLUMN_NAME"]."'"." ";
	                           $komma=" and ";
	                       }
	                       $sqlexec .=", DBMS_SQL.native);"." ";
	                     $sqlexec .="end if;"." ";
	                     $sqlexec .="select * into rec1 from ".$table." where rep\$id=id ;"." ";
	                     //$sqlexec .=" ";
	                     foreach ($pkcols as $pkcol) {
	                     $sqlexec .="DBMS_SQL.BIND_VARIABLE(kis_rep.cursor_d(".$idx."),':".$pkcol["COLUMN_NAME"]."', rec1.".$pkcol["COLUMN_NAME"].");"." ";
	                     }
	                     //$sqlexec .=" ";
	                     $sqlexec .="ignore := DBMS_SQL.EXECUTE(kis_rep.cursor_d(".$idx."));"." ";
	                     $sqlexec .="end if;"." ";
	                     $sqlexec .="end;";

	                     $result=doSQL($db,$sqlexec);
	                     if ($result)
	                       $ret .= "<br>Fehler beim Erstellen der Stored Procedure ".$propagator.'.'.$table;
	                     else
	                       $ret .= "<br>Stored Procedure ".$propagator.'.'.$table." erfolgreich erstellt (idx: ".$idx.")";

		            // Ende: Erstellen der Stored Procedure(mit Cursor)
                }


	// Anfang: Erstellen der Trigger
		// Spalten Liste erzeugen
	      $sqlstr="select column_name from all_tab_columns where owner='".$owner."' and table_name='".$table."' order by column_id";
	      $cols=getResult($db,$sqlstr);
	      $komma="";
	      $collist="";
	      foreach ($cols as $col) {
		      $collist .= $komma.$col["COLUMN_NAME"];
		      $komma=",";
	      }

         //Insert-Trigger
	      $sqlexec ='CREATE OR REPLACE TRIGGER '.$owner.'.RI$'.$table.' AFTER INSERT ON '.$owner.'.'.$table.' FOR EACH ROW'." ";
			$sqlexec .=' begin '." ";
         $sqlexec .='if kis_rep.dorep = TRUE then '." ";
	      $sqlexec .='insert into '.$propagator.'.rep$journal (rep$id,rep$action,rep$name) values(rep$seq.nextval,\'I\',\''.$table.'\');'." ";
	      $sqlexec .='insert into '.$propagator.'.'.$table.' (rep$id,rep$action,'." ";
	      $sqlexec .=$collist." ";
	      $sqlexec .=') values (rep$seq.currval,\'I\''." ";
	      $cols=explode(",",$collist);
			$output="";
         foreach ($cols as $col) {
				$output .= ",:new.".$col." ";
         }
			$sqlexec .= $output.'); ';
         $sqlexec .= ' end if; end;';
  	      $result=doSQL($db,$sqlexec);
	      if ($result)
	        $ret .= "<br>Fehler beim Erstellen INSERT Triggers ".$owner.'.'.$table;
	      else
	        $ret .= "<br>INSERT Trigger ".$owner.'.'.$table." erfolgreich erstellt";


			// Update-Trigger
	      $sqlexec ='CREATE OR REPLACE TRIGGER '.$owner.'.RU$'.$table.' AFTER UPDATE ON '.$owner.'.'.$table.' FOR EACH ROW'." ";
			$sqlexec .=' begin '." ";
         $sqlexec .='if kis_rep.dorep = TRUE then '." ";
	      $sqlexec .='insert into '.$propagator.'.rep$journal (rep$id,rep$action,rep$name) values(rep$seq.nextval,\'U\',\''.$table.'\');'." ";
	      $sqlexec .='insert into '.$propagator.'.'.$table.' (rep$id,rep$action,'." ";
	      $sqlexec .=$collist." ";
	      $sqlexec .=') values (rep$seq.currval,\'u\''." ";
	      $cols=explode(",",$collist);
			$output="";
         foreach ($cols as $col) {
				$output .= ",:old.".$col." ";
         }
	      $sqlexec .=$output."); ";
	      $sqlexec .='insert into '.$propagator.'.'.$table.' (rep$id,rep$action,'." ";
	      $sqlexec .=$collist." ";
	      $sqlexec .=') values (rep$seq.currval,\'U\''." ";
	      $cols=explode(",",$collist);
			$output="";
         foreach ($cols as $col) {
				$output .= ",:new.".$col." ";
         }
			$sqlexec .= $output.'); ';
         $sqlexec .= ' end if; end;';
  	      $result=doSQL($db,$sqlexec);
	      if ($result)
	        $ret .= "<br>Fehler beim Erstellen UPDATE Triggers ".$owner.'.'.$table;
	      else
	        $ret .= "<br>UPDATE Trigger ".$owner.'.'.$table." erfolgreich erstellt";



	      //Delete-Trigger
	      $sqlexec ='CREATE OR REPLACE TRIGGER '.$owner.'.RD$'.$table.' AFTER DELETE ON '.$owner.'.'.$table.' FOR EACH ROW'." ";
			$sqlexec .=' begin '." ";
         $sqlexec .='if kis_rep.dorep = TRUE then '." ";
	      $sqlexec .='insert into '.$propagator.'.rep$journal (rep$id,rep$action,rep$name) values(rep$seq.nextval,\'D\',\''.$table.'\');'." ";
	      $sqlexec .='insert into '.$propagator.'.'.$table.' (rep$id,rep$action,'." ";
	      $sqlexec .=$collist." ";
	      $sqlexec .=') values (rep$seq.currval,\'D\''." ";
	      $cols=explode(",",$collist);
			$output="";
         foreach ($cols as $col) {
				$output .= ",:old.".$col." ";
         }
			$sqlexec .= $output.'); ';
         $sqlexec .= ' end if; end;';
  	      $result=doSQL($db,$sqlexec);
	      if ($result)
	        $ret .= "<br>Fehler beim Erstellen DELETE Triggers ".$owner.'.'.$table;
	      else
	        $ret .= "<br>DELETE Trigger ".$owner.'.'.$table." erfolgreich erstellt";



 	// Ende: Erstellen der Trigger



   	return $ret;
}

function clean_replikation ($action) {
	global $db;
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
	switch ($action) {
	   case 'ask':
	      // Tabellen aus der bestehenden Replikation lesen
	      // Die Tabellen werden anhand der gefundenen UpdateTrigger auf dem Source Schema identifiziert
	      $sqlstr  = "select trigger_name from all_triggers where ";
	      $sqlstr .= "owner='".$owner."' and ";
	      $sqlstr .= 'trigger_name like \'RU$%\'';
	      $result=getResult($db,$sqlstr);
	      unset($tables);
	      $tables=array();
	      foreach ($result as $row) {
	         $tables[$i++]=substr($row["TRIGGER_NAME"],3);
	      }

	      // Prüfen, ob die Schattentabellen alle leer sind
	      $ret="";
	      foreach ($tables as $table) {
	         $sqlstr = "select count(*) anz from ".$propagator.".".$table;
	         $result=getResult($db,$sqlstr);
	         if ($result["0"]["ANZ"] > 0) {
	            $ret .= "<br>Schattentabelle ".$propagator.".".$table." hat noch ".$result["0"]["ANZ"]." Zeilen.";
	         }
	      }
	      if ($ret=="")
	            $ret = "<br>Alle Schattentabellen sind leer.";
	      return $ret;

	   break;

	   case 'clean':
			 $ret="";
	       // Löschen aller vorhandenen Replikationsobjecte des Propagators
	       // Stored Procedures
	       $sqlstr="select object_name from all_objects where object_type='PROCEDURE' and owner='".$propagator."'";
	       $result=getResult($db,$sqlstr);
			 $count=0; $count_error=0;
	       foreach ($result as $proc) {
				$sqlexec="DROP PROCEDURE ".$propagator.'."'.$proc["OBJECT_NAME"].'"';
				$result=doSQL($db,$sqlexec);
				if ($result)
            	$count_error++;
				else
	            $count++;
          }
			 $ret .= "<br>Es wurden ".$count." Stored Procedures gelöscht";
          $ret .= "<br>".$count_error." Fehler beim Löschen der Stored Procedures";
          $ret .= "<br>";


	       // Replikationstabellen
	       $sqlstr="select table_name from all_tables where owner='".$propagator."'";
	       $result=getResult($db,$sqlstr);
			 $count=0; $count_error=0;	       foreach ($result as $droptable) {
	         $sqlexec="DROP TABLE ".$propagator.'."'.$droptable["TABLE_NAME"].'"';
				$result=doSQL($db,$sqlexec);
				if ($result)
            	$count_error++;
				else
	            $count++;

	       }
			 $ret .= "<br>Es wurden ".$count." Schattentabellen gelöscht";
          $ret .= "<br>".$count_error." Fehler beim Löschen der Schattentabellen".'"';
          $ret .= "<br>";




	       // Löschen der Trigger in dem zu replizierenden Schema
	       $sqlstr  = "select TRIGGER_NAME from all_triggers where owner='".$owner."' and ";
	       $sqlstr .= 'TRIGGER_NAME like \'RD$%\' or TRIGGER_NAME like \'RI$%\' or TRIGGER_NAME like \'RU$%\'';
	       $result=getResult($db,$sqlstr);
			 $count=0; $count_error=0;
	       foreach ($result as $trigger) {
	         $sqlexec="DROP TRIGGER ".$owner.'."'.$trigger["TRIGGER_NAME"].'"';
				$result=doSQL($db,$sqlexec);
				if ($result)
            	$count_error++;
				else
	            $count++;
	       }
			 $ret .= "<br>Es wurden ".$count." Replikationstrigger gelöscht";
          $ret .= "<br>".$count_error." Fehler beim Löschen der Replikationstrigger";
          $ret .= "<br>";

          return $ret;
	   break;
   }
}




?>