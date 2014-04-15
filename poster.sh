if [ $# -eq 0 ]; then

 echo "Bad Syntax"

else

  pathConverting=/var/www/content/converting
  pathAvailable=/var/www/content/available
  ext=_poster.png
  
  # Get the base name
  base=$1
  base=${base##*/}
  base=${base%.*}

  echo $base

  # Remove the existing poster (if there)
  if [ -s $pathAvailable/$base$ext ]; then
   echo "Poster : Removing $pathAvailable/$base$ext"
   rm -f $pathAvailable/$base$ext
  fi

  #Specific time to grab?
  timeGrab=00:00:05
  if [ ! "$2" == "" ]; then
    timeGrab=$2
  fi

 echo "Poster : Grabbing frame at $timeGrab"

  # Make the new poster
  /usr/bin/ffmpegthumbnailer -i $1 -q10 -s0  -t $timeGrab -o $pathConverting/$base$ext

  mv $pathConverting/$base$ext $pathAvailable
  echo "Poster : Saved to $pathAvailable/$base$ext"

fi



