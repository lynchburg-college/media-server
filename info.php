<?php 

 $mediaName=$_REQUEST['mediaName'];
 echo "<h1>$mediaName</h1>";

 $files=glob('./content/log/'.$mediaName.'*');
 foreach($files as $file)
 {

    $fileContents=file_get_contents($file);
    $fileModified=date("F d Y H:i:s", filemtime($file) );

    echo "<h4>$file @ $fileModified</h4>";
    echo "<pre>$fileContents</pre>";
    echo "<hr>";
 }

?>
