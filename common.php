<?php

 include_once "config.php";

 if ( !isset( $pathRoot ) )
 {
  die ("Could not load config.php!");
 }


 $pathIncoming = "$pathRoot/content/incoming/";
 $pathConverting = "$pathRoot/content/converting/";
 $pathAvailable =  "$pathRoot/content/available/";
 $urlPlayback = "$urlRoot/player/?";

 if ( !is_dir($pathAvailable) )
 {
  die ("'content' directory not available");
 }



 ini_set('max_input_time', 9999);
 ini_set('upload_tmp_dir',$pathIncoming);
 ini_set('upload_max_filesize', '4G');
 ini_set('post_max_size', '8G');
 
 $ds = DIRECTORY_SEPARATOR; 
 $df = "D M j  g:i a";

 session_start();

 $oUser = new clsUser;
 
 # Handle logout
 if (isset( $_GET['actionLogout'] ) )
 {
  $oUser->Logout();
  header( 'Location: /' ) ;
  exit();
 }

 # Handle login
 if (isset( $_POST['actionLogin'] ) )
 {
  $oUser->Login( $_POST['user'], $_POST['pass'] );
 }



function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}


function bytesToSize($bytes, $precision = 2)
{  
    $kilobyte = 1024;
    $megabyte = $kilobyte * 1024;
    $gigabyte = $megabyte * 1024;
    $terabyte = $gigabyte * 1024;
   
    if (($bytes >= 0) && ($bytes < $kilobyte)) {
        return $bytes . ' B';
 
    } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
        return round($bytes / $kilobyte, $precision) . ' KB';
 
    } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
        return round($bytes / $megabyte, $precision) . ' MB';
 
    } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
        return round($bytes / $gigabyte, $precision) . ' GB';
 
    } elseif ($bytes >= $terabyte) {
        return round($bytes / $terabyte, $precision) . ' TB';
    } else {
        return $bytes . ' B';
    }
}

// --------------------------------------------

function getProgress( $statusFile )
{

   $task = `head -n 1 $statusFile`;
   $status = `sed 's|\\r|\\n|g' "$statusFile"| tail -n 1`;
   $line = "<div title=\"$status\">$task</div>";
   return $line;


}

function cleanup()
{
    foreach ( glob( $pathAvailable."*" ) as $file )
    {
              # To Do : Remove File
    }
}

function buildSearchSQL( $search )
{
    GLOBAL $currentUser;
    GLOBAL $pathIncoming;
    GLOBAL $pathConverting;
    GLOBAL $pathAvailable;
    GLOBAL $urlPlayback;


    switch($search)
	  {

        case "" : 
            return;

        case "orphans":
            $items= array();
                        
      		// Every thumbnail should have a corresponding media file (at least one)
      		foreach ( glob( $pathAvailable."*_thumbnail.png*" ) as $file )
            {
               
               $mediaName =  basename( $file, "_thumbnail.png" );

               $mediaCheck = getMediaMeta_( $mediaName, "mediaName" );
               if ($mediaCheck == "" )
               { 
                 // deleteMedia( $mediaName );
                 array_push( $items, $mediaName);
               }
            }
            echo "There are ".count($items)." orphan media files.<br>'";
            $sql="select * from `media` where mediaName IN ('".implode("','",$items)."') order by uploaded DESC; ";
            break;

		case "all" : 
		    $sql = "select * from `media` order by uploaded DESC; ";
		    break;

		case "my" : 
		    $sql = "select * from `media` where uploadedUser='$currentUser' order by uploaded DESC; ";
            break;

		case "popular" : 
		    $sql = "select * from `media` order by viewed DESC limit 20; ";
		    break;

		case "unwatched" : 
		    $sql = "select * from `media` where viewed=0 order by uploaded; ";
		    break;

		case "recent" : 
                case "today" :
		    $sql = "select * from `media` order by uploaded DESC limit 20; ";
		    break;

		default :

            // walk each search term
            $clause="";
            $concat="";
            foreach ( explode( " ", $search ) as $term ) {

                $term = mysql_real_escape_string($term);
                if (strtotime($term))
                {
                   $ts = date("Y-m-d", strtotime($term));
                   $clause .= $concat."( date(uploaded)=date('$ts') ) ";
                   $concat = " OR ";
                }
                else
                {
                   $clause .= $concat."( mediaName='$term' OR title like '%$term%' OR description like '%$term%' OR uploadedUser like '%$term%') ";
                   $concat = " OR ";
                }

            }

	    $sql = "select * from `media` where $clause order by uploaded DESC; ";

	  }

     return $sql;
     
}


// --------------------------------------------
function showRSS( $search )
{
    GLOBAL $currentUser;
    GLOBAL $pathIncoming;
    GLOBAL $pathConverting;
    GLOBAL $pathAvailable;
    GLOBAL $urlPlayback;
  
//    $course=$_GET[0]; 
    $course=$search;
     
    echo '<?xml version="1.0" encoding="ISO-8859-1"?>';
    echo '<rss version="2.0">';
    echo "<channel>";
    echo "<description>Media</description>";
    echo "<title>" . $course . "</title>";
    echo "<link>http://media.lynchburg.edu/rss</link>";
    echo "<language>en-us</language>";
   
   
      $sql = buildSearchSQL( $search );
echo "\n\n\n$sql";
	  $matches = mysql_query( $sql );
      $rowCount = mysql_numrows($matches);
      if($rowCount>0)
	  {
 
        while($row = mysql_fetch_object($matches))
		{
		    $mediaName = $row->mediaName;
		    $title = $row->title ?: "No Title";
		    $description = $row->description;
            $link = $urlPlayback.$mediaName;

		    $uploaded = $row->uploaded;
            $uploaded = date( "D M j", strtotime($uploaded) );

		    $thumbnail = "http://$urlRoot/content/available/$mediaName"."_thumbnail.png";

            $description="<![CDATA[<div><a target=\"player\" href=\"$link\"><img width=\"80\" src=\"$thumbnail\" /></a></div><div>$description</div>]]>";

		    if ( !file_exists( realpath("./".$thumbnail) ) ) { $thumbnail=""; };
            echo "<item>";
            echo "<title>$uploaded</title>";
            echo "<description>" . $description . "</description>";
            echo "<link>" . $link . "</link>";
            echo "</item>";
           
         }

      }

   echo "</channel>";
   echo "</rss>";
   
}


// --------------------------------------------
function showTiles( $search )
{
    GLOBAL $pathIncoming;
    GLOBAL $pathConverting;
    GLOBAL $pathAvailable;
    GLOBAL $urlRoot;
    GLOBAL $urlPlayback;
    
    GLOBAL $oUser;
      $isAdmin=$oUser->isAdmin();
      $currentUser=$oUser->userID();
               
      $sql = buildSearchSQL( $search );
	  $matches = mysql_query( $sql );

      $rowCount = mysql_numrows($matches);
      if($rowCount>0)
	  {
         echo "<div class=\"results container\">";
         echo "<b>Found these videos:</b><br>";

         echo "<div class=\"page_navigation\"></div>";

         echo "<ul class=\"videos content\" >";

		  while($row = mysql_fetch_object($matches))
		  {

		    $mediaName = $row->mediaName;

            $uploadedUser =$row->uploadedUser ?: "anonymous";
                          
	    $uploaded = $row->uploaded;
            #$uploaded = date( "D M j  g:i a", strtotime($uploaded) );
            $uploaded = date( "D M j", strtotime($uploaded) );

            $isOwner = ( $currentUser!="" && $currentUser==$uploadedUser );

		    $title = $row->title ?: "No Title";
		    $description = $row->description;

		    $viewed = $row->viewed;

		    $public = $row->public;
                   $isPublic = ($public == "1" );

		    $thumbnail = "$mediaName"."_thumbnail.png";
		    if ( !file_exists( realpath("./content/available/".$thumbnail) ) ) { $thumbnail=""; };

            $link = "";
		    if ( glob( $pathAvailable.$mediaName.".*" ) )
		    {
		        $status="available";
                $link = $urlPlayback.$mediaName;
		    }
		    else if ( glob( $pathConverting.$mediaName.".*" ) )
		    {
               $status = "converting";
               $task = `head -n 1 $pathConverting/$mediaName.status`;
               $status = `sed 's|\\r|\\n|g' "$statusFile"| tail -n 1`;
               $description = "<div title=\"$status\">$task</div>";
  		    }
		    else if (glob( $pathIncoming.$mediaName.".*" ) )
		    {
		       $status="incoming";
               $description = "Awaiting Conversion";
		    }
		    else
		    {
		       $status="unknown";
		    }

           if($public == "0")
           {
             $status=$status." private";
             $uploaded=$uploaded." * private";
           }

            if( ($currentUser == $uploadedUser) OR ($isAdmin) )
            {
              $status=$status." canEdit";
            }

            switch ($viewed)
            {
               case 0 : $viewed = "";
                        break;

               case 1 : $viewed = " * viewed once";
                        break;

               default : $viewed = " * viewed $viewed times";

            }
            if( $isPublic OR $isAdmin OR $isOwner )
            {
               echo "<li class=\"video $status\" id=\"media_$mediaName\">" . 
                           "<img class=\"thumbnail\" src=\"content/available/$thumbnail\" >" . 
                             "<p class=\"title\">$title</p>" . 
                             "<p class=\"info\">$uploaded by <span class=\"by\">$uploadedUser</span>$viewed</p>" .
                             "<p class=\"description\">$description</p>" . 
                             "<p class=\"link\">$link</p>" . 
                    "</li>";
           }

   	    }

       echo "</ul>";
       echo "</div>";
     }
     else
     {
       echo "<b>No videos found.</b>";
     }


}





class clsUser
{

  public function isAdmin()
  {
    global $adminUsers;
    return ( (strpos( $adminUsers, $this->userID() ) !== false ) );
  }

  public function remote_host() {
    return $_SERVER['REMOTE_ADDR'];
  }


  public function userID() 
  {

     $id="";
     if( isset($_SESSION['userID']) )
     {
         $id=$_SESSION['userID'];
     }
     return $id;
  }


  function Logout()
  {
     session_destroy();
     session_start();
  }

  function Login( $user, $pass )
  {
     global $autoLogin;
     global $ldap_host;
     global $ldap_port;
     
     session_destroy();
     session_start();

     # See if we can auto-login from this IP
     foreach ( $autoLogin as $item ) {

       if( $_SERVER['REMOTE_ADDR'] == $item['remote_host'])  {
         $_SESSION['userID'] = $item['userID'];
         echo "<div id=\"divAlert\">Automatic login : ".$item['userID']."</div>";
         return;
        }
     }
      
    
     # Already logged in?
     if (!isset($user, $pass)) return;

     # Not logged in.. we need a username and password to try
     if (empty( $user )) return;
     if (empty( $pass )) return;

     $ldapconn = ldap_connect($ldap_host, $ldap_port) or die("Could not connect to $ldap_host");

     if ($ldapconn) 
     {
       $ldapbind = ldap_bind($ldapconn, $user."@lynchburg.edu", $pass);
       if ($ldapbind)
       {
          $_SESSION['userID'] = $user;
       }
       else
       {
          echo "<div id=\"divAlert\">Login failed!  Incorrect username or password.</div>";
       }

     }
   }
 
  private $_loginResult;
  function loginResult()
  {
      if (!isset($_loginResult))
      {
         $_loginResult = "";
      }
      return $_loginResult;
  }


}




class clsLogger {

    // declare log file and file pointer as private properties
    private $log_file, $fp;
    // set log file (path and name)
    public function lfile($path) {
        $this->log_file = $path;
    }
    // write message to the log file
    public function lwrite($message) {
        // if file pointer doesn't exist, then open log file
        if (!is_resource($this->fp)) {
            $this->lopen();
        }
        // define script name
        $script_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
        // define current time and suppress E_WARNING if using the system TZ settings
        // (don't forget to set the INI setting date.timezone)
        $time = @date('[d/M/Y:H:i:s]');
        // write current time, script name and message to the log file
        fwrite($this->fp, "$time ($script_name) $message" . PHP_EOL);
    }
    // close log file (it's always a good idea to close a file when you're done with it)
    public function lclose() {
        fclose($this->fp);
    }
    // open log file (private method)
    private function lopen() {
        // in case of Windows set default log file
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $log_file_default = 'c:/php/logfile.txt';
        }
        // set default log file for Linux and other systems
        else {
            $log_file_default = '/tmp/logfile.txt';
        }
        // define log file from lfile method or use previously set default
        $lfile = $this->log_file ? $this->log_file : $log_file_default;
        // open log file for writing only and place file pointer at the end of the file
        // (if the file does not exist, try to create it)
        $this->fp = fopen($lfile, 'a') or exit("Can't open $lfile!");
    }
}

?>
