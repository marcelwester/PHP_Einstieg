<?php
include "classes/__init.php";

$db=new dbPostgresConnect();
$db->setUser("vol");
$db->setPass("12years");
$db->setHost("127.0.0.1");
$db->setDB("test");




/*
CREATE TABLE public.reptest1
(
  id integer NOT NULL,
  name varchar(255),
  vorname varchar(255),
  alter integer ,
  toc_ts timestamp without time zone,
  CONSTRAINT reptest1_pkey PRIMARY KEY (id)
);
 */

$rs=new DbPostgres($db->conn());

$c = new PostRep($db->conn());
$c->setTables(array("company","sys_images","spiele","reptest1"));

$c->createCommon();
$c->createTable();
$c->createTrigger();
echo $c->getSQL();

$db->close();
?>