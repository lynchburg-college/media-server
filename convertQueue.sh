#!/bin/bash

# How many simultaneous conversion processes are allowed
maxActive=6

# This ensures that an empty directory won't cause problems
shopt -s nullglob

# Get a list of all pending files
pendingFiles=/var/www/content/incoming/*.in

# Try to convert each of them
{
for source in $pendingFiles
do
  
  # How many conversion processes are running?
  active=$(ps -A | grep "avconv" | wc -l)

  if [ $active -lt $maxActive ]
  then
    echo "$(date) : Converting $source"
    bash -c "/var/www/convert.sh $source &"
  else
    echo "$(date) : $active conversions running.  Skipping $source"
  fi
 
  # Take a tiny nap
  sleep 1s

done

} | tee /var/www/convertQueue.log

