if [ $# -ne 1 ]; then

 echo "Bad Syntax"

else

  pathConverting=./content/converting
  pathAvailable=./content/available
  base=`basename $1 '.in'`

  extThumbnail=_thumbnail.png
  echo "Thumbnailing :: $1"
  /usr/bin/ffmpegthumbnailer -i $1 -s160 -q10 -f  -t 00:00:01  -o $pathConverting/$base$extThumbnail
  mv $pathConverting/$base$extThumbnail $pathAvailable

fi



