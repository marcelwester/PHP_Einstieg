#!/bin/bash

. /home/zfa/.bashrc

STATUS_URL="http://127.0.0.1/statusMonitor/mon_upload.php"
STATUS_MONITOR="waage_net"
ERROR_FILE="/tmp/netcheck_error"

NETCHECK=/opt/zfa/skripte/netcheck.lst
HOST=`/bin/hostname`
LOCKFILE=/opt/zfa/skripte/netcheck.lck

if [ -f $LOCKFILE ]; then
   echo "$LOCKFILE exists"
   find ${LOCKFILE} -mmin 60  | while read LINE; do
      rm -f $LINE
      $ALM_MESSAGE ${MESSAGEID} "$HOST - $LOCKFILE deleted"
   done
   exit
fi

touch ${LOCKFILE}




if [ ! -f $NETCHECK ]; then
   $ALM_MESSAGE ${MESSAGEID} "$HOST - $NETCHECK nicht gefunden"
   rm -f ${LOCKFILE}
   exit 1
fi


function check {
  # 1st check
  ping -c 2 $1 #1>/dev/null 2>/dev/null
  if [ "$?" == 0 ]; then
     return 0
  fi

  ping -c 2 $1 #1>/dev/null 2>/dev/null
  if [ "$?" == 0 ]; then
     return 0
  fi

  sleep 30

  ping -c 2 $1 #1>/dev/null 2>/dev/null
  if [ "$?" == 0 ]; then
     return 0
  fi

  ping -c 2 $1 #1>/dev/null 2>/dev/null
  if [ "$?" == 0 ]; then
     return 0
  fi

  return 1
  }
  
  
if [ -f "${ERROR_FILE}" ]; then
   rm -f "${ERROR_FILE}"
fi

cat $NETCHECK |grep -v '^[ |/t]*#' |grep -v '^$' |while read LINE; do
  check $LINE
  if [ "$?" != "0" ]; then
     # ERROR
    echo "Fehler Netzwerk: ${LINE}" >> ${ERROR_FILE}
  fi
done



if [ -f "${ERROR_FILE}" ]; then
   # ERROR
   curl -F "file=@${ERROR_FILE}" "${STATUS_URL}?mon=${STATUS_MONITOR}&status=0" 1>/dev/null 2>/dev/null
else
   # SUCCESS
   curl "${STATUS_URL}?mon=${STATUS_MONITOR}&status=1" 1>/dev/null 2>/dev/null
fi

rm -f ${LOCKFILE}

exit 0
  
