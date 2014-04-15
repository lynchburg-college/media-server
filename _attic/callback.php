<?php
  include_once("common.php");
  include_once("database.php");

  header('Content-type: application/json');

  $currentUser = $oUser->userID();

  $action = "";
  if(isset( $_REQUEST['action'] ) )
  {
    $action = $_REQUEST['action'] ?: "";
  }

  $mediaName = "";
  if(isset( $_REQUEST['mediaName'] ) )
  {
    $mediaName = $_REQUEST['mediaName'];
  }


  switch( $action )
  {
                     
      case "tree" :   if( isset($_REQUEST['parentid'] ) )
                      {  
                         treeChildren( $_REQUEST['parentid'] );
                      }

      case "delete"  : deleteMedia( $mediaName );


      case "select" : $sql = "select * from `media` where mediaName='$mediaName'";
                	  $record = mysql_query( $sql );
                      $row = mysql_fetch_assoc($record);
                      echo json_encode($row);
                      break;


      case "update" : $sql = sprintf( 
                                      "update `media` set title='%s', description='%s', public='%s' where mediaName='$mediaName'",
                                      mysql_real_escape_string( $_POST['title'] ),
                                      mysql_real_escape_string( $_POST['description']),
                                      mysql_real_escape_string( $_POST['public'])
                                    );
                	  mysql_query( $sql );
                      break;


       default : $statusFile = $pathConverting.$mediaName.".status";

                $title=getMediaMeta_( $mediaName, "title") ?: $mediaName;
                $uploaded=getMediaMeta_( $mediaName, "uploaded") ?: "";

                 if ( file_exists( $statusFile ) )
                 {
                       $status="converting";
                       $summary="Converting : ".getProgress( $statusFile )."% complete.";
                     
                }
                else
                {
                      $status="available";
                      $summary="Video is available for playback!";
                }

                 $out = array( 
                               "mediaName"=>$mediaName,
                               "title"=>$title,
                               "uploaded"=>$uploaded,
                               "status"=>$status,
                               "summary"=>$summary
                             );

                 echo json_encode($out);
                 break;

  }




function treeChildren( $parentid )
{
         $children= mysql_query( "select * from `mediatree` where parentID='$parentid'" );
         while( $row = mysql_fetch_assoc($children, MYSQL_ASSOC) )
         {
           echo json_encode( $row );
           treeChildren( $row->$itemID );
         }
}

?>
