#!/bin/bash

cat convert.tmp | while read LINE; do
echo -n "alter database rename file '"
echo -n $LINE
echo "';"
done


