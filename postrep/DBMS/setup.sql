create schema krepadmin;

-- sequence für rep$id
create sequence krepadmin.idgenerator
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;

select nextval('krepadmin.idgenerator');



create table krepadmin.repjournal (
   rep$id bigserial not null,
   dml varchar(1),
   table_name varchar(128),
   txid bigint, 
   toc_ts timestamp
);
   

-- create user table 
CREATE TABLE COMPANY(
   ID INT PRIMARY KEY     NOT NULL,
   NAME           TEXT    NOT NULL,
   AGE            INT     NOT NULL,
   ADDRESS        CHAR(50),
   SALARY         REAL
);

CREATE TABLE SYS_IMAGES(
   ID INT PRIMARY KEY     NOT NULL,
   NAME           VARCHAR(255)    NOT NULL,
   blob         bytea

);



-- create shadow table
DROP TABLE krepadmin.COMPANY;
CREATE TABLE KREPADMIN.COMPANY(
   rep$id         BIGINT  NOT NULL,
   ID 			  INT     NOT NULL,
   NAME           TEXT    NOT NULL,
   AGE            INT     NOT NULL,
   ADDRESS        CHAR(50),
   SALARY         REAL
);



-- create update, insert and delete trigger

-- INSERT
create or replace function fi_COMPANY() RETURNS TRIGGER as $$
declare repid bigint;
 BEGIN                                      
    -- all data from kisters replication, do not fire an insert       
   if current_setting('myapp.kisrep')::integer <> 1 THEN
        
        select nextval('krepadmin.idgenerator') into repid;
        
        insert into krepadmin.company (rep$id,id,name,age,address,salary) values (repid,new.id,new.name,new.age,new.address,new.salary);
        insert into krepadmin.repjournal (rep$id,dml,table_name,txid,toc_ts) values (repid,'I','company',txid_current(),current_timestamp);
  end if;                                                          
  RETURN NEW;                                                      
END $$ LANGUAGE 'plpgsql'; 
CREATE TRIGGER tri_company BEFORE INSERT ON company FOR EACH ROW EXECUTE PROCEDURE fi_COMPANY();

-- DELETE
create or replace function fd_COMPANY() RETURNS TRIGGER as $$
declare repid bigint;
 BEGIN                                      
    -- all data from kisters replication, do not fire an insert       
   if current_setting('myapp.kisrep')::integer <> 1 THEN
        
        select nextval('krepadmin.idgenerator') into repid;
        
        insert into krepadmin.company (rep$id,id,name,age,address,salary) values (repid,old.id,old.name,old.age,old.address,old.salary);
        insert into krepadmin.repjournal (rep$id,dml,table_name,txid,toc_ts) values (repid,'D','company',txid_current(),current_timestamp);
  end if;                                                          
  RETURN OLD;                                                      
END $$ LANGUAGE 'plpgsql'; 
CREATE TRIGGER trd_company BEFORE DELETE ON company FOR EACH ROW EXECUTE PROCEDURE fd_COMPANY();


-- UPDATE
create or replace function fu_COMPANY() RETURNS TRIGGER as $$
declare repid bigint;
 BEGIN                                      
    -- all data from kisters replication, do not fire an insert       
   if current_setting('myapp.kisrep')::integer <> 1 THEN
        -- save old value
        select nextval('krepadmin.idgenerator') into repid;
        insert into krepadmin.company (rep$id,id,name,age,address,salary) values (repid,old.id,old.name,old.age,old.address,old.salary);
        insert into krepadmin.repjournal (rep$id,dml,table_name,txid,toc_ts) values (repid,'u','company',txid_current(),current_timestamp);
        -- write new value
        select nextval('krepadmin.idgenerator') into repid;
        insert into krepadmin.company (rep$id,id,name,age,address,salary) values (repid,new.id,new.name,new.age,new.address,new.salary);
        insert into krepadmin.repjournal (rep$id,dml,table_name,txid,toc_ts) values (repid,'U','company',txid_current(),current_timestamp);
  end if;                                                          
  RETURN OLD;                                                      
END $$ LANGUAGE 'plpgsql'; 
CREATE TRIGGER tru_company BEFORE UPDATE ON company FOR EACH ROW EXECUTE PROCEDURE fu_COMPANY();



-- TEST
insert into company values (1,'Kisters',15,'Stau 75',700);
insert into company values (2,'Kisters',25,'Stau 75',800);
insert into company values (3,'Kisters',35,'Stau 75',900);
insert into company values (4,'Kisters',45,'Stau 75',600);
insert into company values (5,'Kisters',55,'Stau 75',500);
insert into company values (6,'Kisters',65,'Stau 75',400);
insert into company values (7,'Kisters',75,'Stau 75',300);

 
 start transaction;
 
 select * from krepadmin.repjournal;
 select * from krepadmin.company;
 
 select * from company;
 delete from company where id=1;

# Anzeigen der Transactionen mit betroffenen Datensätzen  
select txid,count(txid) from krepadmin.repjournal where dml<>'u' group by txid order by 2 desc;
 
 
 
 delete from company;
 delete from krepadmin.company;
 delete from krepadmin.repjournal;
 
 # Testdatei für Import erzeugen
 export indx=11000
 while [ "1" ]; do  
   echo "insert into company values ($indx,'Kisters',$indx,'Stau 75',$indx);" >> company1.sql
   indx=`expr $indx + 1`
   # echo -n "$indx - "
 done
 
 # Ausführen
 psql -U vol -d test -f company.sql 
 
 
 update company set name='KISTERS NEU' where id>10000;
 
 
 
