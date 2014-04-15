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

  $testUser = 'elam_s';

  switch( $action )
  {
                     
      case "my"   :    $currentUser = $testUser;

                       $return=array();

				       $sql="select * from `media` where uploadedUser='$currentUser' order by uploaded DESC";

					   $items = mysql_query( $sql );

					   while( $item = mysql_fetch_assoc($items, MYSQL_ASSOC) )
							   {
                                 //$item['uploaded']='hmmph';
								 array_push( $return, $item );
							   }
                       if( count($return) > 0)
                       {
                           echo prettyPrint( json_encode($return) );
                       }
                       else
                       {
                           echo "{}";
                       }
                       break;


      case "_renamelist":
                           renameList( $_REQUEST['id'], $_REQUEST['text'] );
                           break;

      case "_deletelist":
                           deleteList( $_REQUEST['id'] );
                           break;

      case "_addlist" : 
                           $currentUser = $testUser;
                           $parentID = ( isset( $_REQUEST['parentID'] ) ) ? $_REQUEST['parentID'] : getRootListID($currentUser);
       				       $itemID = addList($parentID);

                           $return=array();
    				       $sql="select itemID as id, parentID, '' as mediaName, display as text from `medialist` where itemID=$itemID";
   					       $items = mysql_query( $sql );

   					       while( $item = mysql_fetch_assoc($items, MYSQL_ASSOC) )
							   {
								 array_push( $return, $item );
 							   }
                           $return[0]['state']="closed";
                           echo prettyPrint( json_encode($return) );
                           break;

                            

      case "_getlist"  : 
                           $currentUser = $testUser;

                           $itemID = ( isset( $_REQUEST['id'] ) ) ? $_REQUEST['id'] : '';

                           $return=array();

                           if($itemID == '' )
                           {
                               $rootID=getListRoot( $currentUser );
                               $sql = "select itemID as id, parentID, '' as mediaName, display as text  " .
                                      "from medialist " .
                                      "where itemID=$rootID";

                           }
                           else
                           {
		                       $sql = "select L.itemID as id, L.parentID, IFNULL(L.mediaName,'') as mediaName, IFNULL( M.Title, L.display) as text " . 
		                              "from `medialist` L " . 
		                              "left join `media` M on (M.mediaName=L.mediaName) " .
		                              "where L.parentID='$itemID' ";
                           }

    					   $items = mysql_query( $sql );
  	                       while( $item = mysql_fetch_assoc($items, MYSQL_ASSOC) )
		    			   {
                                 if( !($item['mediaName'])  )
                                 {  	 
                                      $item['state']="closed";
                                 }
                                 else
                                 {
                                      $item['attributes']=array( "mediaName"=>$item['mediaName'] );
                                 }
								 array_push( $return, $item );
                           }
                           echo prettyPrint( json_encode($return) );
                           break;


     
      case "list" :       {

                           $rootID = $_REQUEST['rootID'];

                           $itemID = "";
                           if( isset($_REQUEST['itemID']) )
                            { $itemID = $_REQUEST['itemID']; }

                           if($itemID == "")
                           { $itemID = $rootID; }

                            $return = array();

                            $items = mysql_query( "select T.itemID as id, T.display, M.mediaName as mediaName, M.title as title from `medialist` T left join `media` M on (M.mediaName=T.mediaName)  where T.parentID='$itemID'" ); 

  	                        while( $item = mysql_fetch_assoc($items, MYSQL_ASSOC) )
			    			 {
										$itemID = $item['itemID'];

										if( is_null( $item['mediaName'] ) )
										{
										   $item['label']= $item['display'];
										   $item['isFolder']=true;
										   $item['icon']="folder";
										}
										else
                                        {
										   $item['isFolder']=false;
										   $item['icon']="file";
										   $item['label']= $item['title'];
										}

										array_push( $return, $item );
					       }

		                echo prettyPrint( json_encode($return) );
                        }
                        break;
         

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



       case "delete"  : deleteMedia( $mediaName );
                        break;


       case "status" : $statusFile = $pathConverting.$mediaName.".status";

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





function prettyPrint( $json )
{
    $result = '';
    $level = 0;
    $prev_char = '';
    $in_quotes = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if( $char === '"' && $prev_char != '\\' ) {
            $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                case '{': case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
            }
        }
        if( $new_line_level !== NULL ) {
            $result .= "\n".str_repeat( "\t", $new_line_level );
        }
        $result .= $char.$post;
        $prev_char = $char;
    }

    return $result;
}

?>
