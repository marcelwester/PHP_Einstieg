<?php
// Erstellen der Stored Procedures für krep_new und krep_gen
             foreach ($tables as $table) {
       $idx++;

	// Prüfen, ob in dre Tabelle BLOB- oder CLOB-Spalten sind.
        $sqlstr  = "select count(*) ANZ from all_tab_columns where owner='".$owner."' and table_name='".$table."' and data_type in ";
        $sqlstr .= "('BLOB','CLOB')";
        $cols=getResult($db,$sqlstr);
        if ($cols["0"]["ANZ"] == 0) {
        // Anfang: Erstellen der Stored Procedure (mit Cursor)
 	                // Spaltenliste lesen
	                  $sqlstr="select column_name from all_tab_columns where owner='".$owner."' and table_name='".$table."' order by column_id";
	                  $cols=getResult($db,$sqlstr);

	                                  // PK Spaltenliste erzeugen
	                        $sqlstr  = "select column_name from all_constraints ac, all_cons_columns acc where ";
	                        $sqlstr .= "ac.owner='".$owner."' and ";
	                        $sqlstr .= "ac.table_name='".$table."' and ";
	                        $sqlstr .= "ac.constraint_type='P' and ";
	                        $sqlstr .= "ac.owner=acc.owner and ";
	                        $sqlstr .= "ac.table_name=acc.table_name and ";
	                        $sqlstr .= "ac.constraint_name=acc.constraint_name";
	                        $pkcols=getResult($db,$sqlstr);

	                        fputs($file,"CREATE OR REPLACE PROCEDURE \"".strtolower($table)."\" (id in number, action in char) as "."\r\n");
	                        fputs($file,"rec1 ".$table."%ROWTYPE;"."\r\n");
	                        fputs($file,"rec2 ".$table."%ROWTYPE;"."\r\n");
	                        fputs($file,"ignore INTEGER;"."\r\n");
	                        fputs($file,"begin"."\r\n");
	                          fputs($file,"if 'I' = action then"."\r\n");
	                        fputs($file,"if 0 = kis_rep.flags_i(".$idx.") then"."\r\n");
	                          fputs($file,"kis_rep.cursor_i(".$idx.") := DBMS_SQL.OPEN_CURSOR;"."\r\n");
	                          fputs($file,"kis_rep.flags_i(".$idx.") := 1;"."\r\n");
	                          fputs($file,"DBMS_SQL.PARSE(kis_rep.cursor_i(".$idx."), 'insert into ".$table."@".$dblink." ('"."\r\n");
	                                    $komma="";
	                                    foreach ($cols as $col) {
	                                  fputs($file,"||'".$komma.$col["COLUMN_NAME"]."'"."\r\n");
	                        $komma=",";
	                    }
	                                    fputs($file,"||') values ('"."\r\n");
	                                    $komma="";
	                                    foreach ($cols as $col) {
	                                  fputs($file,"||'".$komma.":".$col["COLUMN_NAME"]."'"."\r\n");
	                        $komma=",";
	                    }
	                                    fputs($file,"||')' , DBMS_SQL.native);"."\r\n");
	                    fputs($file,"end if;"."\r\n");
	                    fputs($file,"\r\n");
	                    fputs($file,"select * into rec1 from ".$table." where rep\$id = id;"."\r\n");
	                                    fputs($file,"\r\n");
	                                    foreach ($cols as $col) {
	                             fputs($file,"DBMS_SQL.BIND_VARIABLE(kis_rep.cursor_i(".$idx."), ':".$col["COLUMN_NAME"]."', rec1.".$col["COLUMN_NAME"].");"."\r\n");
	                    }
	                                    fputs($file,"ignore := DBMS_SQL.EXECUTE(kis_rep.cursor_i(".$idx."));"."\r\n");
	                    fputs($file,"\r\n");

	                          fputs($file,"elsif 'U' = action then"."\r\n");
	                        fputs($file,"if 0 = kis_rep.flags_u(".$idx.") then"."\r\n");
	                          fputs($file,"kis_rep.cursor_u(".$idx.") := DBMS_SQL.OPEN_CURSOR;"."\r\n");
	                          fputs($file,"kis_rep.flags_u(".$idx.") := 1;"."\r\n");
	                          fputs($file,"DBMS_SQL.PARSE(kis_rep.cursor_u(".$idx."), 'update ".$table."@".$dblink." set '"."\r\n");
	                                    $komma="";
	                                    foreach ($cols as $col) {
	                                  fputs($file,"||'".$komma.$col["COLUMN_NAME"]." =:".$col["COLUMN_NAME"]."'"."\r\n");
	                        $komma=",";
	                    }
	                                    fputs($file,"||' where '"."\r\n");
	                                    $komma="";
	                    foreach ($pkcols as $pkcol) {
	                                  fputs($file,"||'".$komma.$pkcol["COLUMN_NAME"]."=:"."old_".$pkcol["COLUMN_NAME"]."'"."\r\n");
	                        $komma=" and ";
	                    }
	                                    fputs($file," , DBMS_SQL.native);"."\r\n");
	                    fputs($file,"end if;"."\r\n");
	                    fputs($file,"\r\n");
	                                    fputs($file,"select * into rec1 from ".$table." where rep\$id=id and rep\$action='U';"."\r\n");
	                                    fputs($file,"select * into rec2 from ".$table." where rep\$id=id and rep\$action='u';"."\r\n");
	                                    fputs($file,"\r\n");
	                                    foreach ($cols as $col) {
	                                                  fputs($file,"DBMS_SQL.BIND_VARIABLE(kis_rep.cursor_u(".$idx."), ':".$col["COLUMN_NAME"]."', rec1.".$col["COLUMN_NAME"].");"."\r\n");
	                                    }
	                                    fputs($file,"\r\n");
	                    foreach ($pkcols as $pkcol) {
	                                                  fputs($file,"DBMS_SQL.BIND_VARIABLE(kis_rep.cursor_u(".$idx."), ':old_".$pkcol["COLUMN_NAME"]."', rec2.".$pkcol["COLUMN_NAME"].");"."\r\n");
	                    }
	                    fputs($file,"\r\n");
	                    fputs($file,"ignore := DBMS_SQL.EXECUTE(kis_rep.cursor_u(".$idx."));"."\r\n");
	                                    fputs($file,"\r\n");

	                          fputs($file,"elsif 'D' = action then"."\r\n");
	                        fputs($file,"if 0 = kis_rep.flags_d(".$idx.") then"."\r\n");
	                             fputs($file,"kis_rep.cursor_d(".$idx.") := DBMS_SQL.OPEN_CURSOR;"."\r\n");
	                             fputs($file,"kis_rep.flags_d(".$idx.") := 1;"."\r\n");
	                             fputs($file,"DBMS_SQL.PARSE(kis_rep.cursor_d(".$idx."), 'delete from ".$table."@".$dblink." where '"."\r\n");
	                             $komma="";
	                             foreach ($pkcols as $pkcol) {
	                                 fputs($file,"||'".$komma.$pkcol["COLUMN_NAME"]."=:".$pkcol["COLUMN_NAME"]."'"."\r\n");
	                                 $komma=" and ";
	                             }
	                             fputs($file,", DBMS_SQL.native);"."\r\n");
	                            fputs($file,"end if;"."\r\n");
	                            fputs($file,"select * into rec1 from ".$table." where rep\$id=id ;"."\r\n");
	                 fputs($file,"\r\n");
	                 foreach ($pkcols as $pkcol) {
	                     fputs($file,"DBMS_SQL.BIND_VARIABLE(kis_rep.cursor_d(".$idx."),':".$pkcol["COLUMN_NAME"]."', rec1.".$pkcol["COLUMN_NAME"].");"."\r\n");
	                 }
	                 fputs($file,"\r\n");
	                 fputs($file,"ignore := DBMS_SQL.EXECUTE(kis_rep.cursor_d(".$idx."));"."\r\n");
	                          fputs($file,"end if;"."\r\n");
	                  fputs($file,"end;"."\r\n");
	         fputs($file,"/"."\r\n");


                 } else {
                   // Erstellen der Stored Procedure ohne Cursor
                   echo '<br>Erstellen der Stored Procedure ohne Cursor für Tabelle: '.$table.'<br>';
	                         fputs($file,'CREATE OR REPLACE PROCEDURE "'.strtolower($table).'" (id in number, action in char) as'."\r\n");
	               fputs($file,'begin'."\r\n");
	               fputs($file,"if 'I' = action then"."\r\n");
	                  $sqlstr="select column_name from all_tab_columns where owner='".$owner."' and table_name='".$table."' order by column_id";
	                  $cols=getResult($db,$sqlstr);
	                  $komma="";
	                  $collist="";
	                  foreach ($cols as $col) {
	                     $collist .= $komma.$col["COLUMN_NAME"];
	               $komma=",";
	                  }
	                  fputs($file,"insert into ".$table."@".$dblink." "."\r\n");
	                  fputs($file,"(".$collist.")\r\n");
	                  fputs($file," (select ".$collist." \r\n");
	                  fputs($file," from ".$table.' where rep$id=id);'."\r\n\r\n");
	                         fputs($file,"elsif 'U' = action then \r\n");
	                 fputs($file,"update ".$table."@".$dblink." set "."\r\n");
	            fputs($file,"(".$collist.")\r\n");
	            fputs($file," = (select "."\r\n");
	            fputs($file,$collist."\r\n");
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
	            fputs($file," from ".$table.' where rep$id=id and rep$action=\'U\') where '."\r\n");
	                                 fputs($file,"(".$pkcollist.") in (select \r\n");
	            fputs($file,$pkcollist." from ".$table.' where rep$id=id and rep$action=\'u\');'."\r\n\r\n");
	                         fputs($file,"elsif 'D' = action then \r\n");
	                                 fputs($file,"delete from ".$table."@".$dblink." where ( \r\n");
	                                 fputs($file,$pkcollist." ) in (select \r\n");
	            fputs($file,$pkcollist." from ".$table.' where rep$id=id);'."\r\n");
	         fputs($file,"end if; \r\n");
	         fputs($file,"end; \r\n");
	         fputs($file,"/\r\n\r\n");

                 }
         }

       // idx Parameter in REP$REPOS_CONFIG schreiben
       $sqlstr = 'insert into '.$propagator.'.REP$REPOS_CONFIG (PARAMETER,VALUE) VALUES (';
       $sqlstr .= "'idx',";
		 $sqlstr .= "'".$idx."');";
       fputs($file,$sqlstr."\r\n");
       fputs($file,"commit;"."\r\n");
?>
