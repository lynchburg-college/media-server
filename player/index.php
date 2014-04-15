<?php

   include_once('../common.php');
   include_once('../database.php');
   header('Content-Type: text/html');

   $mediaName =  $_SERVER['QUERY_STRING'];

   $width=800;
   $height=600;

   $isEmbedded=0;
   if ( strpos( $mediaName, '&embedded') != FALSE )
   {
     $isEmbedded=1;
     $mediaName=str_replace( "&embedded", "", $mediaName );
     $width=(round($width/3))+40;
     $height=(round($height/3))+40;
   }
  
   $mediaTitle = getMediaMeta_($mediaName, "title") ?: "No Title";
   $mediaDescription = getMediaDescription($mediaName);

   $uploaded = getMediaMeta_($mediaName,"uploaded");
   $uploadedUser = getMediaMeta_($mediaName,"uploadedUser");

   $playbackPath = "../content/available/";

   $isAdmin = ( $oUser->isAdmin() );
   $isOwner = ( $isAdmin || $oUser->userID() == $uploaded );

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
  <title>Lynchburg College Media Player</title>

  <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css"  >

  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

  <script src="jquery.videosub.js"></script>

 <link type="text/css" href="../css/main.css" rel="stylesheet" >

 <script>

  var mediaName='<?php echo $mediaName ?>';

  var currentSource=0;
  var changeSource=function() {
   
       sourceCount=$('#player source').length;
       currentSource=(currentSource+1)%sourceCount;

       url=$( $('#player source:eq('+currentSource+')')).attr("src");
       $('#player').attr("src", url);
       console.log(url);
 
 }

 var formatSeconds = function(s) {
          var h = Math.floor(s/3600); //Get whole hours
          s -= h*3600;
          var m = Math.floor(s/60); //Get remaining minutes
          s -= m*60;
          return h+":"+(m < 10 ? '0'+m : m)+":"+(s < 10 ? '0'+s : s); //zero padding on minutes and seconds
}

var lastPosition=-99;
var logEvent = function( mediaAction, playerPosition ) {

         currentPosition=Math.round( playerPosition );
         if( mediaAction=='timeupdate' && Math.abs( currentPosition-lastPosition ) < 10 ) { return };
         lastPosition=currentPosition;

         $.post( "log.php", { mediaName:mediaName, mediaAction:mediaAction, mediaPosition:currentPosition,update:true } );
//       .done( function(data) { console.log( "Done : " + data ) } );
	 

 }

var lastTime=0;

$( function() {

   
    $('#dialogEmbed').dialog({ title:"Embed This Video",autoOpen:false,width:600,height:300, buttons:{ 'Close':function(){ $(this).dialog("close") } }  });

    $('#linkEmbed').click( function() {  $('#dialogEmbed').dialog("open")  } );

    $('#linkChangeSource').click( function(){ changeSource() });

    // $('#linkDownload').attr("href", $('#player source').first().attr("src") );
 
    $('#player').on( "loadstart play timeupdate pause seeked ended", function(event){ logEvent( event.type , this.currentTime ) });

<?php
     if( $isOwner ) { 
?>

      $("#linkMakePoster").click( function() {

        spot=formatSeconds( Math.round( document.getElementById("player").currentTime) )

        $("<div>")
        .html("<div id='divAction'>Use this frame for the video poster?</div>")
        .dialog( {
            title : 'Create Poster',
            buttons : {
                        "Never Mind" : function() { $(this).dialog("close") },
                        "Yes" : function() { 
                                                $(this).dialog("close");
                                                result=$.ajax({
                                                    type: "GET",
                                                    url: '../update.php?mediaName=<?php echo $mediaName ?>&setPoster='+spot,
                                                    async: false,
                                                }).responseText;
                                                                             
                                                $(this)
                                                 .html( result )
                                                 .dialog({ buttons:{"Close":function(){$(this).dialog("close")} } })
                                                 .dialog("open");
                                           }
                      }
        });

      });

<?php
    }
?>


});
 </script> 
</head>
<body>
<?php

      if( $isEmbedded != 1)
      {
          echo "<h1>$mediaTitle</h1>";
          echo "<h3>Posted $uploaded by $uploadedUser </h3>";
          echo "<hr>";
          echo "<div class=\"wrapper\">";
      }
      
      $options = "controls";

      echo "<video id=\"player\" width=\"$width\" height=\"$height\"  preload=\"none\" $options poster=\"$playbackPath$mediaName"."_poster.png\">" ;
      echo "\n";
      

      $sources = array(
           "h264" => array( "ext"=>".mp4","element"=>"source","type"=>"video/mp4;codecs=acv1.42E01E,mp4a.40.2" ),
           "x264" => array( "ext"=>".mp4","element"=>"source","type"=>"video/mp4" ),
           "webm" => array( "ext"=>".webm","element"=>"source","type"=>"video/webm" ),
           "ogg" => array( "ext"=>".ogv","element"=>"source","type"=>"video/ogg"),
           "srt" => array( "ext"=>".srt","element"=>"track",kind=>"subtitle",srclang=>"en-US",label=>"Subtitles")
      );

      $info = "";
      foreach( $sources as $source ) {

           $file="$playbackPath$mediaName".$source['ext'];

           if( file_exists( realpath($file) ) ) {

              $element=$source['element'];
              echo "<$element src='$file' ";
              foreach( $source as $k=>$v ) {
                  echo "$k='$v' ";
              }
              echo " />\n";
             
              
         }
      }
      
      echo "<h2>Could not find a suitable format for playback</h2>";
      echo "</video>";

      if( $isEmbedded != 1)
      {
         echo '</div>';

         echo '<div id="actions" style="text-align:center;">';
         echo '<span id="linkEmbed"><a href="#">Embed</a></span>';
         // echo '<span id="linkDownload"> | <a href="#">Download</a></span>';
         echo '</div>';

         if ( $isOwner ) {
          echo '<div id="actionsOwner" style="text-align:center;">';
          echo '<span id="linkMakePoster"><a href="#">Make Poster<a></span>';
          echo '</div>';
         }
         echo '<div id="dialogEmbed">';
         echo 'Copy the following code:';
         echo '<textarea style="width:80%;height:75%">';
         echo '&lt;iframe width="'.round($width/3).'" height="'.round($height/3).'" src="http://media.lynchburg.edu/player/?'.$mediaName.'&embedded" frameborder="0" allowfullscreen&gt;&lt;/iframe&gt;';
         echo '</textarea>';
         echo '</div>';

      }
?>
</body>
</html>

