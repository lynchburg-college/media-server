<?php

include_once "config.php";

$db_connection = mysql_connect($db_host, $db_user, $db_pwd);
if (!$db_connection)
    die("Can't connect to database server");

if (!mysql_select_db($db, $db_connection))
    die("Can't open media server database ($db_host : $db) ");




function addMedia( $mediaName )
{

   $remote_host =  $_SERVER['REMOTE_ADDR'];
   $sql = "insert into `media` (mediaName,uploaded,uploadedHost) VALUES( '$mediaName', Now(), '$remote_host')";
   $result = mysql_query($sql);
   return $mediaName;

}
   
function deleteMedia( $mediaName )
{
  GLOBAL $pathAvailable;

  if($mediaName != "" )
  {
                       $sql = "delete from `media` where mediaName = '$mediaName' ";
    				   mysql_query($sql);
 
                       foreach( glob($pathAvailable.$mediaName."*") as $mediaFile)
                       {
                            unlink($mediaFile);
					   }
  }
}



function getConfig ( $setting )
{
   $sql = sprintf("select configValue from `config` where configSetting='$setting'");
   $result = mysql_query($sql);
   $row=mysql_fetch_row($result);
   return $row[0];
}

function getSystemSummary ()
{
   $summary = "";

   $sql = sprintf("select count(*) from `media`");
   $result = mysql_query($sql);
   $row=mysql_fetch_row($result);
   $total = $row[0];

   $sql = sprintf("select count(*) from `media` where uploaded >= now()-INTERVAL 24 HOUR");
   $result = mysql_query($sql);
   $row=mysql_fetch_row($result);
   $today = $row[0];

   $summary = "There are currently <b>".$total."</b> videos available.";
   if($today > 0 )
   {
       $summary=$summary." <b>$today</b> have been added recently.";
   }   

   return $summary;
}




function renameList( $id, $text )
{
   $sql = sprintf("update medialist set display='%s' where itemID=$id", mysql_real_escape_string($text) );
   $result = mysql_query($sql);
}

function deleteList( $id )
{
   // First, delete anything with this list as a parent
   $sql = "select itemID from medialist where parentID='$id'";
   $matches = mysql_query( $sql );
    while($row = mysql_fetch_object($matches))
	{
       deleteList( $row->itemID );
    }
       
   // Then delete the list
   $sql = "delete from medialist where itemID='$id'";
   $result = mysql_query( $sql );
   return true;
}

function addList( $parentID )
{
        $sql = "insert into medialist(parentID,display) values('$parentID','')";
        $result = mysql_query($sql);
        $itemID = mysql_insert_id();
        return $itemID;
}

function getListRoot( $user )
{
      $sql = sprintf("select itemID from `medialist` where parentID='%s'",  mysql_real_escape_string($user) );

      $result = mysql_query($sql);

      if( mysql_num_rows($result) == 0 )
      {
        $sql = sprintf("insert into medialist(parentID,display) values('%s','My Playlists')",  mysql_real_escape_string($user) );
        $result = mysql_query($sql);
        $itemID = mysql_insert_id();
      }
      else
      {
        $row=mysql_fetch_row($result);
        $itemID = $row[0];
      }

  return $itemID; 
}



function setMediaMeta_($mediaName,$item,$value )
{

   $sql = sprintf("update `media` set $item='%s' where mediaName='$mediaName'", 
                   mysql_real_escape_string($value) );

   $result = mysql_query($sql);

}

function getMediaMeta_($mediaName,$item)
{
   $sql = sprintf("select $item from `media` where mediaName='$mediaName'");
   $result = mysql_query($sql);
   $row=mysql_fetch_row($result);
   return $row[0];
}



function setMediaTitle($mediaName,$value)
{
   $result = setMediaMeta_($mediaName,'title',$value);
}
function getMediaTitle($mediaName)
{
   return getMediaMeta_($mediaName,'title');
}


function setMediaDescription($mediaName,$value )
{
   $result = setMediaMeta_($mediaName,'description',$value);
}
function getMediaDescription($mediaName)
{
   return getMediaMeta_($mediaName,'description');
}


function setMediaKeyword($mediaName, $keyword)
{
}



function mediaLog( $mediaName, $mediaAction, $mediaPosition )
{
   global $oUser;

     // Write it to the log table

     $remote_host=$oUser->remote_host();
     $currentUser=$oUser->userID();

     $sql = sprintf("insert into `medialog`(mediaName,mediaAction,mediaPosition,user,remote_host) values('$mediaName','$mediaAction',$mediaPosition,'$currentUser','$remote_host')");
     $result = mysql_query($sql);


    // Special case:  if the action is play, and we haven't logged this mediaName+IP+play in the last 18 hours, increment the "soft" counter
    if( ! $oUser->isAdmin() ) {

      if( $mediaAction == "play" ) {
 
          $sql = "select count(logTimestamp) from medialog where mediaName='$mediaName' AND remote_host='$remote_host' AND mediaAction='play' AND logTimestamp > now() - INTERVAL 1 DAY";
          $result = mysql_query($sql);
          $row=mysql_fetch_row($result);
          $count = $row[0];
          if( "$count" == "0" ) {

            $sql = sprintf("update media set viewed=viewed+1 where mediaName='$mediaName';");
            $result = mysql_query($sql);

          }

      }
   }

   return true;
}




?>
