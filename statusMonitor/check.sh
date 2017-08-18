#!/bin/bash

. /home/oracle/.bashrc


cd /opt/htdocs/dev/statusMonitor

php check.php  1>>/var/log/statusMonitor.log 2>&1


exit 0
