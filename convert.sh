

if [ $# -ne 1 ]; then

 echo "Usage : convert.sh [file to convert]"

else

  pathLog=/var/www/content/log
  pathConverting=/var/www/content/converting
  pathAvailable=/var/www/content/available
  
  # Rename the file to indicate that we are converting it
  source=${1%.*}
  mv "$1" "$source"

  # Build the poster immediately
  /var/www/poster.sh "$source"

  # Get the base name 
  mediaName=`basename $source`

  # Create log files
  logStatus=$pathConverting/$mediaName.status
  logInfo=$pathLog/$mediaName.info

  # Get information about the original file
  $fileInfo = $(/usr/bin/avprobe "$source" 2> "$logInfo")

  #H.264
  ext=.mp4
  name="MP4(h264/aac)"
  dest=$pathConverting/$mediaName$ext
  logDetails=$pathLog/$mediaName$ext.log
  echo "Task 1 of 3 : Converting To H.264" > $logStatus
  #if grep --quiet "Video: h264" $logInfo 
  #then 
  #   if grep --quiet "Audio: aac" $logInfo 
  #   then
  #     echo "Format is already H264/aac.  Copying.." >> $logDetails
  #     cp "$source" "$dest"
  #   fi
  #fi
  if [ ! -s "$dest" ] 
  then
     echo "$(date) : Converting to MP4(h264/aac)." >> $logDetails
     /usr/bin/avconv -i $source -dstw 1024 -nostats -vcodec libx264 -b:v 400k -maxrate 1000k -bufsize 1000k -deinterlace -threads 0 -acodec libvo_aacenc -b:a 96k $dest 1>> $logDetails 2>&1
     echo "$(date) : Done. " >> $logDetails
  fi
  if [ -s "$dest" ] 
  then
      echo "$(date) : Copying to $pathAvailable. " >> $logDetails
      mv $dest $pathAvailable
  fi


  # OGV
  ext=.ogv
  dest=$pathConverting/$mediaName$ext
  logDetails=$pathLog/$mediaName$ext.log
  echo "Task 2 of 3 : Converting To OGV" > $logStatus
  if grep --quiet "Video: theora" $logInfo 
  then
     if grep --quiet "Audio: vorbis" $logInfo 
     then
       echo "Format is already OGV(theora,vorbis).  Copying.." >> $logDetails
       cp "$source" "$dest"
     fi
  fi
  if [ ! -s "$dest" ] 
  then
      echo "$(date) : Converting to OGV(theora,vorbis)" >> $logDetails
      /usr/bin/avconv -i $source -nostats -acodec libvorbis -aq 5 -ac 2 -qmax 25 -threads 2  $dest 1>> $logDetails 2>&1
      echo "$(date) : Done. " >> $logDetails
  fi
  if [ -s "$dest" ] 
  then
      echo "$(date) : Copying to $pathAvailable. " >> $logDetails
      mv $dest $pathAvailable
  fi


  #WEBM
  ext=.webm
  dest=$pathConverting/$mediaName$ext
  logDetails=$pathLog/$mediaName$ext.log
  echo "Task 3 of 3 : Converting To WEBM" > $logStatus
  if grep --quiet "Video: vp8" $logInfo 
  then
     if grep --quiet "Audio: vorbis" $logInfo 
     then
       echo "Format is already WEBM(vp8/vorbis).  Copying.." >> $logDetails
       cp "$source" "$dest"
     fi
  fi
  if [ ! -s "$dest" ] 
  then
      echo "$(date) : Converting to WEBM(vp8/vorbis)" >> $logDetails
      /usr/bin/avconv -i $source -nostats -acodec libvorbis -aq 5 -ac 2 -qmax 25 -threads 2  $dest 1>> $logDetails 2>&1
      echo "$(date) : Done. " >> $logDetails
  fi
  if [ -s "$dest" ] 
  then
      echo "$(date) : Copying to $pathAvailable. " >> $logDetails
      mv $dest $pathAvailable
  fi
 


  # Clean up
  rm $pathConverting/$mediaName*
  rm $source

fi



