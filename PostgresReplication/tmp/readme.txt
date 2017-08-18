addTable.php ==> Einzelne Tabellen neu definieren
dropTable.php ==> Eilzelne Tabellen aus der Replikation nehmen
dorep.php ==> Pushjob starten
dopurge.php ==> Purgejob starten 
createrep.php ==> GEsamte Replikation (neu)aufsetzen


# Replikation in einer Session abschalten: 
#  postgresql.conf
==> myapp.kisrep = 1
 
select current_setting('myapp.kisrep');
set session "myapp.kisrep" = 0;
set session "myapp.kisrep" = 1;

# Verzögerte Transaktionen (deftrandest)
select count(distinct(txid)) from krepadmin.repjournal;

# Replikationsfehler
select * from krepadmin.reperror;





# Tabelle ändern
alter table company add column x varchar(100);
alter table company drop column x ;

# Tabelle Hinzufügen
CREATE TABLE public.reptest1
(
  id integer NOT NULL,
  name character varying(255),
  vorname character varying(255),
  alter integer,
  toc_ts timestamp without time zone,
  CONSTRAINT reptest1_pkey PRIMARY KEY (id)
);

start transaction;
insert into reptest1 values (1,'Y','W',30,current_timestamp);
insert into reptest1 values (2,'Y','W',30,current_timestamp);
insert into reptest1 values (3,'Y','W',30,current_timestamp);
insert into reptest1 values (4,'Y','W',30,current_timestamp);
insert into reptest1 values (5,'Y','W',30,current_timestamp);
insert into reptest1 values (6,'Y','W',30,current_timestamp);
insert into reptest1 values (7,'Y','W',30,current_timestamp);
insert into reptest1 values (8,'Y','W',30,current_timestamp);
insert into reptest1 values (9,'Y','W',30,current_timestamp);
insert into reptest1 values (10,'Y','W',30,current_timestamp);
insert into reptest1 values (11,'Y','W',30,current_timestamp);
insert into reptest1 values (12,'Y','W',30,current_timestamp);
end transaction;


# Class ReadStructure

Input: rep$id => Tabelle


Vorher Laden in eine Klasse
Tabellen
Spalten 
Spaltentypen
PK

public functions der Klasse: 
getTables => Array
getColumn(tablename) => Array
getColumnType(tablename,columnname) => STRING
getPK =>  Array["Tablename"]
getColumnList(tablename) => STRING
getPKList =>  STRING["Tablename"] Kommasepariert


set session "myapp.kisrep" = 0;


truncate table company;
start transaction;
insert into public.company (id,name,age,address,salary) values (1,'Erwin',65,'irgendwo',100);
insert into public.company (id,name,age,address,salary) values (2,'Ingo',65,'irgendwo',200);
insert into public.company (id,name,age,address,salary) values (3,'Volker',65,'irgendwo',300);
insert into public.company (id,name,age,address,salary) values (4,'Heinz',65,'irgendwo',400);
insert into public.company (id,name,age,address,salary) values (5,'Karl',65,'irgendwo',150);
insert into public.company (id,name,age,address,salary) values (6,'Günther',65,'irgendwo',300);
insert into public.company (id,name,age,address,salary) values (7,'Werner',65,'irgendwo',600);
insert into public.company (id,name,age,address,salary) values (8,'Inge',65,'irgendwo',700);
insert into public.company (id,name,age,address,salary) values (9,'Mr x',65,'Schulweg',700);
insert into public.company (id,name,age,address,salary) values (10,'Mr x',65,null,null);

update  company set address='Industriestrasse' where id=10;

update  company set address='XXX' where id=10;

insert into sys_images (id,name,blob) values (1,'Volker','TESTBLOB');
insert into sys_images (id,name,blob) values (2,'XXX','TESTBLOB2');


update company set address='Bahnweg' where id=3;


bigint: 
9223372036854775807
9999999999999999999


id=100
while [ "1" ]; do
  echo "insert into  public.company (id,name,age,address,salary) values (1,'Erwin',65,'irgendwo',100);


start transaction;
update company set toc_ts=current_timestamp where id>500 and id<1505 ;
delete from spiele where id>7;
insert into spiele values (8,10,12,6);
insert into spiele values (9,10,12,6);
insert into spiele values (10,10,12,6);
insert into spiele values (11,10,12,6);
update company set toc_ts=current_timestamp where id>1500 and id<1605 ;
update spiele set aus=25 where id=8;
delete from spiele where id=9;
delete from company where id<500;
commit;









select md5(to_char(id,'9999999999999999999')||to_char(heim,'9999999999999999999')||to_char(aus,'9999999999999999999')||to_char(id1,'9999999999999999999')) from public.spiele where id=3 and id1=4;