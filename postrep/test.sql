

-- ***************** insert company  *****************
create or replace function rf$Icompany() RETURNS TRIGGER as $$
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
drop trigger rt$Icompany on company;
CREATE TRIGGER rt$Icompany BEFORE INSERT ON company FOR EACH ROW EXECUTE PROCEDURE rf$Icompany();


-- ***************** delete company  *****************
create or replace function rf$Dcompany() RETURNS TRIGGER as $$
declare repid bigint;
BEGIN
-- all data from kisters replication, do not fire an delete
if current_setting('myapp.kisrep')::integer <> 1 THEN
select nextval('krepadmin.idgenerator') into repid;
insert into krepadmin.company (rep$id,id,name,age,address,salary) values (repid,old.id,old.name,old.age,old.address,old.salary);
insert into krepadmin.repjournal (rep$id,dml,table_name,txid,toc_ts) values (repid,'D','company',txid_current(),current_timestamp);
end if;
RETURN OLD;
END $$ LANGUAGE 'plpgsql';
drop trigger rt$Dcompany on company;
CREATE TRIGGER rt$Dcompany BEFORE DELETE ON company FOR EACH ROW EXECUTE PROCEDURE rf$Dcompany();


-- ***************** update company  *****************
create or replace function rf$Ucompany() RETURNS TRIGGER as $$
declare repid bigint;
BEGIN
-- all data from kisters replication, do not fire an update
if current_setting('myapp.kisrep')::integer <> 1 THEN
select nextval('krepadmin.idgenerator') into repid;
insert into krepadmin.company (rep$id,id,name,age,address,salary) values (repid,old.id,old.name,old.age,old.address,old.salary);
insert into krepadmin.repjournal (rep$id,dml,table_name,txid,toc_ts) values (repid,'u','company',txid_current(),current_timestamp);
insert into krepadmin.company (rep$id,id,name,age,address,salary) values (repid,new.id,new.name,new.age,new.address,new.salary);
insert into krepadmin.repjournal (rep$id,dml,table_name,txid,toc_ts) values (repid,'U','company',txid_current(),current_timestamp);
end if;
RETURN OLD;
END $$ LANGUAGE 'plpgsql';
drop trigger rt$Ucompany on company;
CREATE TRIGGER rt$Ucompany BEFORE UPDATE ON company FOR EACH ROW EXECUTE PROCEDURE rf$Ucompany();




