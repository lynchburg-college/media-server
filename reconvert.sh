#!/bin/bash

  pathAvailable=/var/www/content/available

  # Find all MP4 files, and see if they need a webm or ogg
 for file in $pathAvailable/*.mp4
 do

  base=`basename $file '.mp4'`
  echo $base

  checkFile=$pathAvailable/$base.webm
  if [ ! -s $checkFile ]
  then
   echo Creating $checkFile
   /usr/bin/avconv -i $file -y -acodec libvorbis -aq 5 -ac 2 -qmax 25 -threads 2 $checkFile
  fi


  checkFile=$pathAvailable/$base.ogv
  if [ ! -s $checkFile ]
  then
   echo Creating $checkFile
  /usr/bin/avconv -i $file -y -acodec libvorbis -ac 2 -ab 96k -ar 44100 -b 3900k  $checkFile
  fi

 done


k
