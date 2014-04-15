<?php
include_once("../common.php");
include_once("../database.php");

header ("Content-type: application/rss+xml; charset=ISO-8859-1");
$course=$_GET["course"];

#  Course Format : DDDDCCCYYT,  ie. ENGL111A143

// trim off the trailing YT
## ***
#Check length, parse if needed
#$search=substr_replace( $course, "", -3 );
$search = $course;
showRSS( $search ); 

?>

