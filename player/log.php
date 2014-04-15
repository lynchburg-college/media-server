<?php

include_once("../common.php");
include_once("../database.php");


if( ! isset( $_REQUEST['mediaName'] ) ) {
 die("no media name provided.");
}

    
$mediaName = $_REQUEST['mediaName'];
$mediaAction = $_REQUEST['mediaAction'];
$mediaPosition = $_REQUEST['mediaPosition'];

mediaLog( $mediaName, $mediaAction, $mediaPosition );

echo "Logged: $mediaName / $mediaAction / $mediaPosition";

?>
