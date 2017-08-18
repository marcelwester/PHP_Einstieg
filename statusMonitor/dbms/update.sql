

ALTER TABLE `statusMonitor`.`sys_values` ADD COLUMN `group_s` VARCHAR(45) NOT NULL DEFAULT ' Anzeige ';

insert into sys_values values('alertpath', '/tmp/status/alm', 'Pfad f&uuml;r Alarmmeldungen', 1, 160, 'text', 'Alarmmanager');
insert into sys_values values('alm_manager', '0', 'Alarmmanager-Anbindung,  1: an, 0: aus ', 1, 145, 'number', 'Alarmmanager');
insert into sys_values values('alm_message_id', '1000015', 'Alarmmanager Messageid', 1, 165, 'text', 'Alarmmanager');
insert into sys_values values('check_timeout', '2000', 'Status alle n Millisekunden pr&uuml;fen f&uuml;r Alarmierung', 1, 140, 'number', 'Alarmierung');
insert into sys_values values('cnt_log_view', '20', 'Anzeige von n letzten Logeintr&auml;gen', 1, 120, 'number', ' Anzeige ');
insert into sys_values values('color_fail', '#FF0000', 'Farbe des Monitors bei Fehler', 1, 104, 'text', ' Anzeige ');
insert into sys_values values('color_ok', '#00FF00', 'Farbe des Monitors wenn alles Ok ist', 1, 100, 'text', ' Anzeige ');
insert into sys_values values('color_timeout', '#FFFF00', 'Farbe des Monitors bei &uuml;berschrittenem Timeout', 1, 102, 'text', ' Anzeige ');
insert into sys_values values('color_unknown', '#FFFFFF', 'Farbe Monitor bei unknown', 1, 105, 'text', ' Anzeige ');
insert into sys_values values('DEBUG', '0', 'Debug 0: aus, 1: an ', 1, 130, 'number', ' Anzeige ');
insert into sys_values values('display_refresh', '1000', 'Refresh der Anzeige in Millisekunden', 1, 140, 'number', ' Anzeige ');
insert into sys_values values('monitor_row_cnt', '3', 'Anzahl Monitore nebeneinander', 1, 110, 'number', ' Anzeige ');
insert into sys_values values('telegram', '1', 'Anbindung telegram: 1: an, 0: aus', 1, 115, 'number', 'Telegram');
insert into sys_values values('telegram_url', 'http://127.0.0.1/dev/telegram-bot/inbox.php', 'URL f&uuml;r telegram Server', 1, 120, 'text', 'Telegram');
insert into sys_values values('title', 'Status Monitor', 'Titel der Webseite', 1, 90, 'text', ' Anzeige ');
insert into sys_values values('tmppath', '/tmp/status/spool', 'tmp Pfad f&uuml;r Alarmmeldungen', 1, 150, 'text', 'Alarmmanager');



CREATE TABLE `statusMonitor`.`sys_reload` (
  `session_id` VARCHAR(32) NOT NULL,
  `rel` INTEGER UNSIGNED NOT NULL,
  `toc_ts` DATETIME NOT NULL,
  PRIMARY KEY (`session_id`)
);


-- Mail 
insert into sys_values values('mail', '1', 'Anbindung Mailserver: 1: an, 0: aus', 1, 10, 'number', 'Mail');
insert into sys_values values('mail_sender', 'vol@kisters.de', 'Mail Absenderaddresse', 1, 40, 'text', 'Mail');
insert into sys_values values('mail_txt', 'http://10.8.0.98/dev/statusMonitor/mon.php', 'Zus&auml;tzlicher Text in Mailnachricht', 1, 40, 'text', 'Mail');
insert into sys_values values('check_reload', '0', 'Neuladen der Konfiguration beim check.php', 0, 0, 'number', 'SYS');

insert into sys_values values('keep_days', '90', 'Daten &auml;lter als n Tage werden gel&ouml;scht', 1, 10, 'number', 'Daten');
insert into sys_values values('check_keep_days', '1000', 'L&ouml;schjob alle n check Durchl&auml;ufe', 1, 20, 'number', 'Daten');
insert into sys_values values('blackout', '20:00-06:00', 'Blackout: Zwischen den Uhrzeiten werden keinen Daten gesendet <br>Beispiel: 20:00-06:00<br>Aus:0', 1, 20, 'text', 'Alarmierung');

insert into sys_values values('check_heartbeat', '0', 'Haertbeat vom Checkscript', 0, 0, 'text', 'SYS');


-- Groups
CREATE TABLE sys_groups (
  `groupid` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(128) NOT NULL,
  `descr` TEXT,
  `disable` TINYINT,
  PRIMARY KEY (`groupid`)
)
ENGINE = InnoDB;

CREATE TABLE sys_user_group (
  `userid` INTEGER UNSIGNED NOT NULL,
  `groupid` INTEGER UNSIGNED NOT NULL
)
ENGINE = InnoDB;

insert into sys_groups values (1,"default","Defaultgruppe",0);
insert into sys_user_group (userid) (select userid from sys_users);
update  sys_user_group set groupid=1;

ALTER TABLE `statusMonitor`.`sys_user_group` MODIFY COLUMN `userid` INTEGER UNSIGNED NOT NULL DEFAULT 0,
 MODIFY COLUMN `groupid` INTEGER UNSIGNED NOT NULL DEFAULT 0,
 ADD PRIMARY KEY(`userid`, `groupid`);
 

CREATE TABLE sys_monitor_group (
  `monitorid` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `groupid` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(`monitorid`, `groupid`)
)
ENGINE = InnoDB;
insert into sys_monitor_group (monitorid,groupid) (select id,1 from sys_monitor);