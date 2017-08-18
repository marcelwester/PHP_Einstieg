#!/bin/bash

SRC=asp-db-3.lst
DST=asp-db-5.lst



cat $SRC | while read LINE; do 
  src=`echo $LINE | awk  -F"," '{print $2}'`
  src_number=`echo $LINE | awk  -F"," '{print $1}'`
 # echo "========================================================================================="
  #echo "$src_number  -   $src"
  
  LINE_DST=`cat $DST | grep "${src_number},"` 
  dst=`echo $LINE_DST | awk  -F"," '{print $2}'`
  dst_number=`echo $LINE_DST | awk  -F"," '{print $1}'`

  #echo "$dst_number  -   $dst"
  
  
    #echo -n "alter database rename file '"
    #echo -n "$src"
    #echo -n "' to '"
    #echo -n "$dst"
    
    
    echo "$src"      
  
 done
