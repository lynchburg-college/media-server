<?php

include_once("common.php");
include_once("database.php");


function updateIfProvided( $mediaName, $item ) {
 if( isset( $_REQUEST[$item] ) )
 {
    //setMediaMeta_( $mediaName, $item. $_REQUEST[$item] );
    show ("Setting $item=".$_REQUEST[$item]);
 }
}
function show($msg) {
 echo "<div>$msg</div>";
}

if( ! isset( $_REQUEST['mediaName'] ) ) {
 die("no media name provided.");
}
    
    $mediaName = $_REQUEST['mediaName'];
   

if( isset( $_REQUEST['setPoster'] ) )
{
    $frame=$_REQUEST['setPoster'];
    show("Poster was set to frame $frame");
    $inFile="$pathRoot/content/available/$mediaName.mp4";
    shell_exec("$pathRoot/convert.sh '$inFile' '$frame' > /dev/null 2>&1 &");
    echo "$pathRoot/convert.sh '$inFile' '$frame' > /dev/null 2>&1 &";

}

    updateIfProvided($mediaName,"uploadedUser");
    updateIfProvided($mediaName,"public");
    updateIfProvided($mediaName,"title");
    updateIfProvided($mediaName,"description");


?>
