#!/bin/bash

. /home/nsw/.bashrc

echo "delete  from sys_monitor_log where toc_ts < now() - interval 3 MONTH ;" | mysql -u root statusMonitor
echo "delete  from sys_monitor_logfile where toc_ts < now() - interval 3 MONTH ;" |mysql -u root statusMonitor

exit
