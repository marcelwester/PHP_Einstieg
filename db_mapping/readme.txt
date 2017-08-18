set pagesize 200
set linesize 200

select 'alter database rename file '''||name||''' to '''||name||''';' from v$datafile order by name;

select file#||','||name from v$datafile order by file#;
