if [ $# -ne 1 ]; then

 echo "Bad Syntax"

else

  pathConverting=/var/www/content/converting
  pathAvailable=/var/www/content/available

  root=`basename $1`
  base=`echo ${root%.*}`

  echo $base

  #Poster
  ext=_poster.png
  #/usr/bin/ffmpegthumbnailer -i $1 -q10 -s0  -t 00:00:01 -o $pathConverting/$base$ext

  #Thumbnail
  extThumbnail=_thumbnail.png
  #/usr/bin/ffmpegthumbnailer -i $1 -s160 -q10 -f  -t 00:00:01  -o $pathConverting/$base$extThumbnail

  #mv $pathConverting/$base* $pathAvailable


fi



