<?php
// Skript zum Erzeugen der Replikationstabellen und Sored Procedures für den Replikationspropagator

$url='index.php?menu=gensetup&PHPSESSID='.session_id();

if (!isset($_GET["action"]))
	$action="start";
else
	$action=$_GET["action"];


switch ($action) {
	case 'start':
	   echo '<table align="center" width="50%" border="1">';
		echo '<form method="POST" action="'.$url.'&action=step1">';
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

         echo '<tr>';
		      echo '<td align="center" width="50%">';
		   	   echo '<b>Repliziertes Schema:</b>';
	   	   echo '</td>';
	 		   echo '<td align="center" width="50%">';
					echo '<b>'.$owner.'</b>';
					echo '<input type="hidden" name="owner" value="'.$owner.'" />';
	         echo '</td>';
	      echo '</tr>';

         echo '<tr>';
		      echo '<td align="center" width="50%">';
		   	   echo '<b>Propagator</b>';
	   	   echo '</td>';
	 		   echo '<td align="center" width="50%">';
					echo '<b>'.$propagator.'</b>';
					echo '<input type="hidden" name="propagator" value="'.$propagator.'" />';
	         echo '</td>';
	      echo '</tr>';

         echo '<tr>';
		      echo '<td align="center" width="50%">';
		   	   echo '<b>DB-Link (Destination)</b>';
	   	   echo '</td>';
	 		   echo '<td align="center" width="50%">';
					echo '<b>'.$dblink.'</b>';
					echo '<input type="hidden" name="dblink" value="'.$dblink.'" />';
	         echo '</td>';
	      echo '</tr>';


	      echo '<tr>';
	         echo '<td align="center" colspan="2">';
	            echo '<input type="submit" value="Weiter" />';
	         echo '</td>';
	      echo '</tr>';

		echo '</form>';
      echo '</table>';
	break;


   case 'step1':
	// Skript Generation
   	$owner=$_POST["owner"];
		$propagator=$_SESSION["repadmin"];
      $dblink=$_POST["dblink"];

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

		$filename="/tmp/KREP_".$propagator.".sql";
      $file = fopen ($filename, "wb");

	  	 // Löschen aller vorhandenen Replikationsobjecte des Propagators
		 // Stored Procedures
       $sqlstr="select object_name from all_objects where object_type='PROCEDURE' and owner='".$propagator."'";
       $result=getResult($db,$sqlstr);
       foreach ($result as $proc) {
         fputs($file,"DROP PROCEDURE ".$propagator.'."'.$proc["OBJECT_NAME"].'";'."\r\n");
       }
		 fputs($file,"\r\n");

       // Replikationstabellen
       $sqlstr="select table_name from all_tables where owner='".$propagator."'";
       $result=getResult($db,$sqlstr);
       foreach ($result as $droptable) {
			fputs($file,"DROP TABLE ".$propagator.'."'.$droptable["TABLE_NAME"].'";'."\r\n");
       }
		 fputs($file,"\r\n");

		 // Löschen der Trigger in dem zu replizierenden Schema
		 $sqlstr  = "select TRIGGER_NAME from all_triggers where owner='".$owner."' and ";
       $sqlstr .= 'TRIGGER_NAME like \'RD$%\' or TRIGGER_NAME like \'RI$%\' or TRIGGER_NAME like \'RU$%\'';

       $result=getResult($db,$sqlstr);
		 foreach ($result as $trigger) {
			fputs($file,"DROP TRIGGER ".$owner.'."'.$trigger["TRIGGER_NAME"].'";'."\r\n");
       }
		 fputs($file,"\r\n");



      // Package im Propagator Schema anlegen
	   fputs($file,'CREATE OR REPLACE PACKAGE '.$propagator.'."KIS_REP" AS'."\r\n");
	   fputs($file,'type curarr is table of integer;'."\r\n");

  	   fputs($file,'flags_d curarr;'."\r\n");
  	   fputs($file,'flags_i curarr;'."\r\n");
  	   fputs($file,'flags_u curarr;'."\r\n");

  	   fputs($file,'cursor_d curarr;'."\r\n");
  	   fputs($file,'cursor_i curarr;'."\r\n");
  	   fputs($file,'cursor_u curarr;'."\r\n");

  	   fputs($file,'dorep BOOLEAN := TRUE;'."\r\n");
  	   fputs($file,'maxtabs number := 1000;'."\r\n");

  	   fputs($file,'PROCEDURE set_rep (rep IN BOOLEAN);'."\r\n");
  	   fputs($file,'PROCEDURE init_rep;'."\r\n");
  	   fputs($file,'PROCEDURE exit_rep;'."\r\n");
	   fputs($file,'end;'."\r\n");
	   fputs($file,'/'."\r\n");


	   fputs($file,'CREATE OR REPLACE PACKAGE BODY '.$propagator.'."KIS_REP" AS'."\r\n");

  	   fputs($file,'PROCEDURE set_rep (rep IN BOOLEAN) IS'."\r\n");
  	   fputs($file,'BEGIN'."\r\n");
    	   fputs($file,'dorep := rep;'."\r\n");
  	   fputs($file,'END;'."\r\n");

  	   fputs($file,'PROCEDURE init_rep IS'."\r\n");
  	   fputs($file,'BEGIN'."\r\n");
    	   fputs($file,'flags_d := curarr(null);'."\r\n");
    	   fputs($file,'flags_i := curarr(null);'."\r\n");
    	   fputs($file,'flags_u := curarr(null);'."\r\n");

    	   fputs($file,'cursor_d := curarr(null);'."\r\n");
    	   fputs($file,'cursor_i := curarr(null);'."\r\n");
    	   fputs($file,'cursor_u := curarr(null);'."\r\n");

    	   fputs($file,'flags_d.extend (maxtabs);'."\r\n");
    	   fputs($file,'flags_i.extend (maxtabs);'."\r\n");
    	   fputs($file,'flags_u.extend (maxtabs);'."\r\n");

    	   fputs($file,'cursor_d.extend (maxtabs);'."\r\n");
    	   fputs($file,'cursor_i.extend (maxtabs);'."\r\n");
    	   fputs($file,'cursor_u.extend (maxtabs);'."\r\n");

    	   fputs($file,'for i in 1..maxtabs loop'."\r\n");
      	   fputs($file,'flags_d(i) := 0;'."\r\n");
      	   fputs($file,'flags_i(i) := 0;'."\r\n");
      	   fputs($file,'flags_u(i) := 0;'."\r\n");
    	   fputs($file,'end loop;'."\r\n");
  	   fputs($file,'END;'."\r\n");

  	   fputs($file,'PROCEDURE exit_rep IS'."\r\n");
  	   fputs($file,'BEGIN'."\r\n");
    	   fputs($file,'for i in 1..maxtabs loop'."\r\n");
      	   fputs($file,'if 1 = kis_rep.flags_i(i) then'."\r\n");
        	   fputs($file,'DBMS_SQL.CLOSE_CURSOR(kis_rep.cursor_i(i));'."\r\n");
      	   fputs($file,'end if;'."\r\n");
    	   fputs($file,'end loop;'."\r\n");
    	   fputs($file,'for i in 1..maxtabs loop'."\r\n");
      	   fputs($file,'if 1 = kis_rep.flags_u(i) then'."\r\n");
        	   fputs($file,'DBMS_SQL.CLOSE_CURSOR(kis_rep.cursor_u(i));'."\r\n");
      	   fputs($file,'end if;'."\r\n");
    	   fputs($file,'end loop;'."\r\n");
    	   fputs($file,'for i in 1..maxtabs loop'."\r\n");
      	   fputs($file,'if 1 = kis_rep.flags_d(i) then'."\r\n");
        	   fputs($file,'DBMS_SQL.CLOSE_CURSOR(kis_rep.cursor_d(i));'."\r\n");
      	   fputs($file,'end if;'."\r\n");
    	   fputs($file,'end loop;'."\r\n");
  	   fputs($file,'END;'."\r\n");
	   fputs($file,'END;'."\r\n");
	   fputs($file,'/'."\r\n");


      // Package im Owner Schema anlegen
	   fputs($file,'CREATE OR REPLACE PACKAGE '.$owner.'."KIS_REP" AS'."\r\n");
	   fputs($file,'type curarr is table of integer;'."\r\n");

  	   fputs($file,'flags_d curarr;'."\r\n");
  	   fputs($file,'flags_i curarr;'."\r\n");
  	   fputs($file,'flags_u curarr;'."\r\n");

  	   fputs($file,'cursor_d curarr;'."\r\n");
  	   fputs($file,'cursor_i curarr;'."\r\n");
  	   fputs($file,'cursor_u curarr;'."\r\n");

  	   fputs($file,'dorep BOOLEAN := TRUE;'."\r\n");
  	   fputs($file,'maxtabs number := 1000;'."\r\n");

  	   fputs($file,'PROCEDURE set_rep (rep IN BOOLEAN);'."\r\n");
  	   fputs($file,'PROCEDURE init_rep;'."\r\n");
  	   fputs($file,'PROCEDURE exit_rep;'."\r\n");
	   fputs($file,'end;'."\r\n");
	   fputs($file,'/'."\r\n");


	   fputs($file,'CREATE OR REPLACE PACKAGE BODY '.$owner.'."KIS_REP" AS'."\r\n");

  	   fputs($file,'PROCEDURE set_rep (rep IN BOOLEAN) IS'."\r\n");
  	   fputs($file,'BEGIN'."\r\n");
    	   fputs($file,'dorep := rep;'."\r\n");
  	   fputs($file,'END;'."\r\n");

  	   fputs($file,'PROCEDURE init_rep IS'."\r\n");
  	   fputs($file,'BEGIN'."\r\n");
    	   fputs($file,'flags_d := curarr(null);'."\r\n");
    	   fputs($file,'flags_i := curarr(null);'."\r\n");
    	   fputs($file,'flags_u := curarr(null);'."\r\n");

    	   fputs($file,'cursor_d := curarr(null);'."\r\n");
    	   fputs($file,'cursor_i := curarr(null);'."\r\n");
    	   fputs($file,'cursor_u := curarr(null);'."\r\n");

    	   fputs($file,'flags_d.extend (maxtabs);'."\r\n");
    	   fputs($file,'flags_i.extend (maxtabs);'."\r\n");
    	   fputs($file,'flags_u.extend (maxtabs);'."\r\n");

    	   fputs($file,'cursor_d.extend (maxtabs);'."\r\n");
    	   fputs($file,'cursor_i.extend (maxtabs);'."\r\n");
    	   fputs($file,'cursor_u.extend (maxtabs);'."\r\n");

    	   fputs($file,'for i in 1..maxtabs loop'."\r\n");
      	   fputs($file,'flags_d(i) := 0;'."\r\n");
      	   fputs($file,'flags_i(i) := 0;'."\r\n");
      	   fputs($file,'flags_u(i) := 0;'."\r\n");
    	   fputs($file,'end loop;'."\r\n");
  	   fputs($file,'END;'."\r\n");

  	   fputs($file,'PROCEDURE exit_rep IS'."\r\n");
  	   fputs($file,'BEGIN'."\r\n");
    	   fputs($file,'for i in 1..maxtabs loop'."\r\n");
      	   fputs($file,'if 1 = kis_rep.flags_i(i) then'."\r\n");
        	   fputs($file,'DBMS_SQL.CLOSE_CURSOR(kis_rep.cursor_i(i));'."\r\n");
      	   fputs($file,'end if;'."\r\n");
    	   fputs($file,'end loop;'."\r\n");
    	   fputs($file,'for i in 1..maxtabs loop'."\r\n");
      	   fputs($file,'if 1 = kis_rep.flags_u(i) then'."\r\n");
        	   fputs($file,'DBMS_SQL.CLOSE_CURSOR(kis_rep.cursor_u(i));'."\r\n");
      	   fputs($file,'end if;'."\r\n");
    	   fputs($file,'end loop;'."\r\n");
    	   fputs($file,'for i in 1..maxtabs loop'."\r\n");
      	   fputs($file,'if 1 = kis_rep.flags_d(i) then'."\r\n");
        	   fputs($file,'DBMS_SQL.CLOSE_CURSOR(kis_rep.cursor_d(i));'."\r\n");
      	   fputs($file,'end if;'."\r\n");
    	   fputs($file,'end loop;'."\r\n");
  	   fputs($file,'END;'."\r\n");
	   fputs($file,'END;'."\r\n");
	   fputs($file,'/'."\r\n");





	    // Tabellen erzeugen
		 // Konfigurationstabelle
       fputs($file,'CREATE TABLE '.$propagator.'."REP$REPOS_CONFIG" ("PARAMETER" VARCHAR2(255)'."\r\n");
	    fputs($file,'NOT NULL, "VALUE" VARCHAR(255))'."\r\n");
		 fputs($file,"/\r\n");

       $sqlstr = 'insert into '.$propagator.'.REP$REPOS_CONFIG (PARAMETER,VALUE) VALUES (';
       $sqlstr .= "'dblink',";
		 $sqlstr .= "'".$dblink."');";
       fputs($file,$sqlstr."\r\n");

       $sqlstr = 'insert into '.$propagator.'.REP$REPOS_CONFIG (PARAMETER,VALUE) VALUES (';
       $sqlstr .= "'owner',";
		 $sqlstr .= "'".$owner."');";
       fputs($file,$sqlstr."\r\n");

       $sqlstr = 'insert into '.$propagator.'.REP$REPOS_CONFIG (PARAMETER,VALUE) VALUES (';
       $sqlstr .= "'propagator',";
		 $sqlstr .= "'".$propagator."');";
       fputs($file,$sqlstr."\r\n");
       fputs($file,"commit; \r\n\r\n");

//		 fputs($file,'drop table '.$propagator.'.rep$journal;'."\r\n");
	    fputs($file,'create table '.$propagator.'.rep$journal ('."\r\n");
	    fputs($file,'REP$ID number not null,'."\r\n");
	    fputs($file,'REP$ACTION char(1) not null,'."\r\n");
	    fputs($file,'REP$NAME varchar2(30) not null,'."\r\n");
	    fputs($file,'primary key (REP$ID)) ;'."\r\n");
	    fputs($file,'grant insert on rep$journal to '.$owner.';'."\r\n");
	    fputs($file, "\r\n");
//	    fputs($file,'drop table '.$propagator.'.rep$log;'."\r\n");
	    fputs($file,'create table '.$propagator.'.rep$log ('."\r\n");
	    fputs($file,'REP$TIME DATE not null, '."\r\n");
	    fputs($file,'REP$ERRORS NUMBER not null,'."\r\n");
	    fputs($file,'REP$RECORDS NUMBER,'."\r\n");
  	    fputs($file,'DURATION NUMBER) ;'."\r\n");
	    fputs($file, "\r\n");
//	    fputs($file,'drop table '.$propagator.'.rep$error;'."\r\n");
	    fputs($file,'create table '.$propagator.'.rep$error ('."\r\n");
	    fputs($file,'REP$ID number not null,'."\r\n");
	    fputs($file,'REP$TIME DATE not null,'."\r\n");
	    fputs($file,'err_code number,'."\r\n");
	    fputs($file,'err_text varchar2(2000)) ;'."\r\n");
	    fputs($file, "\r\n");
       foreach ($tables as $table) {
//			fputs($file,'drop table '.$propagator.'.'.$table.';'."\r\n");
         fputs($file,'create table '.$propagator.'.'.$table.' storage (initial 64k next 64k) as select -1 rep$id,\'x\' rep$action,'.$table.'.* from '.$owner.'.'.$table.' where 0=1;'."\r\n");
         fputs($file,'alter table '.$propagator.'.'.$table.' add primary key (REP$ID,REP$ACTION);'."\r\n");
         fputs($file,'grant insert on '.$propagator.'.'.$table.' to '.$owner.';'."\r\n");
			fputs($file,"\r\n");
		}


      // Prozeduren erzeugen (unter Verwendung von Cursors)
      $idx=0;
      // Anfang: Prozeduren erzeugen
	include "ora_krep_stored_proc.php";
      // Ende: Prozeduren erzeugen


      // Sequence für Object ID  Vergabe
      fputs($file,'DROP SEQUENCE '.$propagator.'.REP$OjectSeq;'."\r\n");
		fputs($file,"\r\n");
		fputs($file,'CREATE SEQUENCE '.$propagator.'.REP$OjectSeq INCREMENT BY 1 START WITH '.++$idx.' MAXVALUE 1.0E28 MINVALUE  '.$idx.' NOCYCLE CACHE 20 ORDER'.";\r\n");
		fputs($file,"\r\n");

      // doRep Procedure erzeugen
	   fputs($file,'CREATE OR REPLACE PROCEDURE REP$DOREP as'."\r\n");
	   fputs($file,' cmd varchar2(32767);'."\r\n");
	   fputs($file,' err number;'."\r\n");
	   fputs($file,' cnt number;'."\r\n");
	   fputs($file,' err_num NUMBER;'."\r\n");
	   fputs($file,' err_msg VARCHAR2(2000);'."\r\n");
  	   fputs($file,' start_time date;'."\r\n");
	   fputs($file,'begin'."\r\n");
		fputs($file,' start_time := sysdate;');
      fputs($file,' err := 0;'."\r\n");
	   fputs($file,' cnt := 0;'."\r\n");
		fputs($file,' kis_rep.init_rep;'."\r\n");
	   fputs($file,''."\r\n");
	   fputs($file,' for i in (select rep$id id, rep$action action, rep$name name'."\r\n");
	   fputs($file,'             from rep$journal'."\r\n");
	   fputs($file,'            order by 1) loop'."\r\n");
	   fputs($file,'   cnt := cnt +1;'."\r\n");
	   fputs($file,'   begin'."\r\n");
	   fputs($file,'     cmd := \'begin "\'||lower(i.name)||\'"(\'||i.id||\',\'\'\'||i.action||\'\'\'); end;\';'."\r\n");
	   fputs($file,'--     dbms_output.put_line (cmd);'."\r\n");
	   fputs($file,'     execute immediate cmd;'."\r\n");
	   fputs($file,''."\r\n");
	   fputs($file,'     cmd := \'delete from \'||i.name||\' where rep$id=\'||i.id;'."\r\n");
	   fputs($file,'--     dbms_output.put_line (cmd);'."\r\n");
	   fputs($file,'     execute immediate cmd;'."\r\n");
	   fputs($file,''."\r\n");
	   fputs($file,'     delete from rep$journal'."\r\n");
	   fputs($file,'      where rep$id = i.id ;'."\r\n");
	   fputs($file,''."\r\n");
	   fputs($file,'     exception'."\r\n");
	   fputs($file,'     when others then'."\r\n");
	   fputs($file,'       err := err +1;'."\r\n");
	   fputs($file,'       err_num := SQLCODE;'."\r\n");
	   fputs($file,'       err_msg := SUBSTR(SQLERRM, 1, 2000);'."\r\n");
	   fputs($file,'       insert into rep$error values (i.id, sysdate, err_num, err_msg);'."\r\n");
	   fputs($file,'     end;'."\r\n");
	   fputs($file,' end loop;'."\r\n");
		fputs($file,' kis_rep.exit_rep;'."\r\n");
	   fputs($file,''."\r\n");
	   fputs($file,''."\r\n");
	   fputs($file,' delete from rep$log'."\r\n");
	   fputs($file,'  where rep$time < sysdate -30'."\r\n");
	   fputs($file,'    and rep$errors =0;'."\r\n");
	   fputs($file,''."\r\n");
	   fputs($file,' insert into rep$log values (sysdate, err, cnt,(sysdate-start_time)*60*1440);'."\r\n");
	   fputs($file,' commit;'."\r\n");
	   fputs($file,'end;'."\r\n");
	   fputs($file,'/'."\r\n");



		// Trigger und Sequence auf zu replizierendes Schema erzeugen
		// Sequence für ID Vergabe
      fputs($file,'DROP SEQUENCE '.$owner.'.REP$Seq;'."\r\n");
		fputs($file,"\r\n");
		fputs($file,'CREATE SEQUENCE '.$owner.'.REP$Seq INCREMENT BY 1 START WITH 1 MAXVALUE 1.0E28 MINVALUE 1 NOCYCLE CACHE 20 ORDER'.";\r\n");
		fputs($file,"\r\n");

		foreach ($tables as $table) {
			// Spaltenliste erzeugen
	      $sqlstr="select column_name from all_tab_columns where owner='".$owner."' and table_name='".$table."' order by column_id";
	      $cols=getResult($db,$sqlstr);
	      $komma="";
	      $collist="";
	      foreach ($cols as $col) {
		      $collist .= $komma.$col["COLUMN_NAME"];
		      $komma=",";
	      }

         //Insert-Trigger
	      fputs($file,'CREATE OR REPLACE TRIGGER '.$owner.'.RI$'.$table.' AFTER INSERT ON '.$owner.'.'.$table.' FOR EACH ROW'."\r\n");
			fputs($file,' begin '."\r\n");
         fputs($file, 'if kis_rep.dorep = TRUE then '."\r\n");

	      fputs($file,'insert into '.$propagator.'.rep$journal (rep$id,rep$action,rep$name) values(rep$seq.nextval,\'I\',\''.$table.'\');'."\r\n");
	      fputs($file,'insert into '.$propagator.'.'.$table.' (rep$id,rep$action,'."\r\n");
	      fputs($file,$collist."\r\n");
	      fputs($file,') values (rep$seq.currval,\'I\''."\r\n");
	      $cols=explode(",",$collist);
			$output="";
         foreach ($cols as $col) {
				$output .= ",:new.".$col."\r\n";
         }
	      fputs($file,$output."); \r\n end if; end; \r\n/\r\n");

//	      fputs($file,$output.")\r\n/\r\n");
	      fputs($file,''."\r\n");


	      //Update-Trigger
	      fputs($file,'CREATE OR REPLACE TRIGGER '.$owner.'.RU$'.$table.' AFTER UPDATE ON '.$owner.'.'.$table.' FOR EACH ROW'."\r\n");
			fputs($file,' begin '."\r\n");
         fputs($file, 'if kis_rep.dorep = TRUE then '."\r\n");
	      fputs($file,'insert into '.$propagator.'.rep$journal (rep$id,rep$action,rep$name) values(rep$seq.nextval,\'U\',\''.$table.'\');'."\r\n");
	      fputs($file,'insert into '.$propagator.'.'.$table.' (rep$id,rep$action,'."\r\n");
	      fputs($file,$collist."\r\n");
	      fputs($file,') values (rep$seq.currval,\'u\''."\r\n");
	      $cols=explode(",",$collist);
			$output="";
         foreach ($cols as $col) {
				$output .= ",:old.".$col."\r\n";
         }
	      fputs($file,$output.");\r\n");
	      fputs($file,'insert into '.$propagator.'.'.$table.' (rep$id,rep$action,'."\r\n");
	      fputs($file,$collist."\r\n");
	      fputs($file,') values (rep$seq.currval,\'U\''."\r\n");
	      $cols=explode(",",$collist);
			$output="";
         foreach ($cols as $col) {
				$output .= ",:new.".$col."\r\n";
         }
	      fputs($file,$output."); \r\n end if; end; \r\n/\r\n");
	      //fputs($file,$output.")\r\n/\r\n");
	      fputs($file,''."\r\n");

	      //Delete-Trigger
	      fputs($file,'CREATE OR REPLACE TRIGGER '.$owner.'.RD$'.$table.' AFTER DELETE ON '.$owner.'.'.$table.' FOR EACH ROW'."\r\n");
			fputs($file,' begin '."\r\n");
         fputs($file, 'if kis_rep.dorep = TRUE then '."\r\n");
	      fputs($file,'insert into '.$propagator.'.rep$journal (rep$id,rep$action,rep$name) values(rep$seq.nextval,\'D\',\''.$table.'\');'."\r\n");
	      fputs($file,'insert into '.$propagator.'.'.$table.' (rep$id,rep$action,'."\r\n");
	      fputs($file,$collist."\r\n");
	      fputs($file,') values (rep$seq.currval,\'D\''."\r\n");
	      $cols=explode(",",$collist);
			$output="";
         foreach ($cols as $col) {
				$output .= ",:old.".$col."\r\n";
         }
	      fputs($file,$output."); \r\n end if; end; \r\n/\r\n");
         //fputs($file,$output.")\r\n/\r\n");

	      fputs($file,''."\r\n");
	      fputs($file,''."\r\n");
      }
      fputs($file,"\r\n\r\n");



      fclose($file);

		echo '<h2>Skript wurde erfolgreich erzeugt:</h2>';
      echo '<br><br>';
      echo $filename;
   break;

   case 'cleaning':
		$owner='SDVS01';
	   $propagator='KREPUSER';
      $sqlstr="select object_name from all_objects where object_type='PROCEDURE' and owner='".$propagator."'";
      $result=getResult($db,$sqlstr);
      foreach ($result as $proc) {
      	echo "<br>drop PROCEDURE ".$propagator.'."'.$proc["OBJECT_NAME"].'";';
      }


   }
	oclose($db);
?>