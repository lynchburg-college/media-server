<?php

include_once("common.php");
include_once("database.php");

set_time_limit(0);
ini_set('upload_max_filesize', '5G');
ini_set('post_max_size', '6G');
ini_set('max_input_time', 0);
ini_set('memory_limit', '6G');
ini_set('max_execution_time', 0);


$mediaName = "";
if (empty($_FILES))
{
  exit("Error : No files found in POST header");
}

    #  Create something unique for importing the file..
    $mediaName = uniqid();

    #  If the POSTed data includes a format designator, tack that on to the filename
    if( isset( $_POST['format'] ) )
    {
       $mediaName=$mediaName."_".$_POST['format'];
    }

    # Give it a place in the incoming directory
    $inFile = $pathIncoming.$mediaName.".in";

    $tempFile = $_FILES['file']['tmp_name'];          
    
    move_uploaded_file( $tempFile, $inFile );


    if( ! file_exists($inFile) )
    {
      exit("Error : Could not locate incoming file.");
    }

    # Set the permission wide the heck open
    chmod( $inFile, 0777 );

    # Add it to the database..
    addMedia( $mediaName );

    $userID = $oUser->userID();

    $batchMode = "N";
    $public = "1";
    $title = "";
    $description = "";

    if( isset( $_POST['batchMode']) )
    {
       if ( $_POST['batchMode'] == "Y" )   
       {

        if ( isset( $_POST['userID'] ) )
        {
         $userID=$_POST['userID'];
        }

        $public = ( $_POST['public'] );
        $originalFile = pathinfo( $_FILES['file']['name'], PATHINFO_FILENAME);
        $title = ( $_POST['title']  );
        $title = str_replace( "%title%", $originalFile, $title );

        $description = ($_POST['description'] );

       }
    }

    setMediaMeta_( $mediaName, "uploadedUser", $userID );
    setMediaMeta_( $mediaName, "public", $public );
    setMediaMeta_( $mediaName, "title", $title );
    setMediaMeta_( $mediaName, "description", $description );

    # Make a thumbnail immediately
    shell_exec("$pathRoot/thumbnail.sh '$inFile'");

    # Convert
    shell_exec("$pathRoot/convert.sh '$inFile' > /dev/null 2>&1 &");

    echo "Success : $mediaName";

?>
