<?php
   include_once('../common.php');
   include_once('../database.php');
   header('Content-Type: text/html');

   $mediaName =  $_SERVER['QUERY_STRING'];

   $mediaTitle = getMediaMeta_($mediaName, "title") ?: "No Title";
   $mediaDescription = getMediaDescription($mediaName);

   $uploaded = getMediaMeta_($mediaName,"uploaded");
   $uploadedUser = getMediaMeta_($mediaName,"uploadedUser");

   $playbackPath = "http://hudson-b.lynchburg.edu/content/available/";
   $playbackPath = "/content/available/";

   mediaViewed($mediaName);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">

   <title>Lynchburg College Media Player</title>

   <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/smoothness/jquery-ui.css">
   <link rel="stylesheet" type="text/css" href="../css/main.css">

   <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>

  <script>

 $( function() {

  player=$("#player");

  player.bind( "loadstart", function() {
                                            console.log( this.currentSrc );
                                       });

  player.bind( "loadedmetadata", function() { 
                                            });

  });
  
  
  </script>
</head>
<body>
<?php

      $options = "controls";

      if ( isMobile() ) 
      {
          $mediaTitle = $mediaTitle . " (mobile version)";
          $options = $options . " autoplay ";
          echo "<style>";
          include 'mobile.css';
          echo "</style>";
      }

      echo "<h1>$mediaTitle</h1>";
      echo "<h3>Posted $uploaded by $uploadedUser </h3>";
      echo "<h2 id=\"duration\"></h2>";

      echo "<hr>";

#     echo "<div class=\"wrapper\">";

      echo "<video id=\"player\" preload=\"none\" $options poster=\"$playbackPath$mediaName"."_poster.png\">" ;

      # echo "<source type=\"video/mp4\" src=\"$playbackPath$mediaName"."_mobile.mp4\"  media=\"all and (max-width:480px)\"  />";
      echo "<source type='video/webm' src=\"$playbackPath$mediaName.webm\"/>";
      echo "<source type='video/mp4;codecs=\"avc1.42E01E, mp4a.40.2\"' src=\"$playbackPath$mediaName.mp4\"/>";
      echo "<source type='video/ogg' src=\"$playbackPath$mediaName.ogv\"/>";
      echo "<track kind=\"subtitles\" src=\"$playbackPath$mediaName.vtt\" srclang=\"en-US\" label=\"English\" />";   
      echo "<h2>Could not find a suitable format for playback</h2>";
      echo "</video>";


#     echo "</div>";
?>

</body>
</html>




