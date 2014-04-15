<?php
include_once("common.php");
include_once("database.php");
$currentUser = $oUser->userID();
?>
<html>
<head>
<!--[if IE]>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<![endif]-->

  <title><?=$siteTitle?></title>

  <link href="css/dropzone.css" rel="stylesheet" type="text/css" media="all"  />
  <link href="css/main.css" rel="stylesheet" type="text/css" media="all"  />
  <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css"  >

  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

  <script src="js/jquery.form.js"></script>
  <script src="js/dropzone.js"></script>
  <script src="js/jquery.pajinate.min.js"></script>
  <script src="js/main.js"></script>

  <script>
   //$( function() {
   //                $('.thumbnail').error(function(){  $(this).attr('src', './thumbnail_generic.png'); }); 
   //              }
   // );
 </script>

</head>
<body>
<form id="formLogin" method="POST">
 <h3>Use your Lynchburg College network account:</h3>
     <table>
      <tr><th>User:</th><td><input name="user"></td></tr>
      <tr><th>Password</th><td><input type="password" name="pass"></td></tr>
     </table>
  <hr>
  <input type="submit" name="actionLogin" value="Login">
  <?= $oUser->loginResult() ?>
</form>
<form id="formEdit" action="callback.php" method="POST">
<input type="hidden" name="mediaName" value="" />
<input type="hidden" name="action" value="update" />
   <table>
     <tr>  
       <td valign="top"><img class="thumbnail" src="img/logo.png"></td>
       <td valign="top">
         <table>
          <tr><td><label>Uploaded</label></td><td><span class="uploaded"></span></td></tr>
          <tr><td><label>Link</label></td><td><span class="link"></span></td></tr>
          <tr><td colspan="2"><hr></td></tr>
          <tr><td><label>Public?</label></td><td><select name="public" ><option value="1">Yes, video appears in search results</option><option value="0">No, hidden from search results</option></select></td></tr>
          <tr><td><label>Title</label></td><td><input name="title" size="40" maxlen="64" ></td></tr>
          <tr><td><label>Keywords</label></td><td><input name="keywords" size="40" maxlen="64"></td></tr>
          <tr><td><label>Description</label></td><td><textarea name="description"></textarea></td></tr>
        </table>
       </td>
     </tr>
   </table>
   <hr>
   <div class="center"><button type="submit">Save Changes</button></div>
</form>
<div id="header">
     <a href="?"><img src="img/logo.png" /></a>
     <h2><?=$siteTitle ?></h2>
     <div><?=getSystemSummary()?></div>
</div>
<div class="clear"> </div>
<div id="tabs">
<ul id="tabHeader">
    <li><a href="#tabSearch">Search</a></li>
    <li><a href="#tabHelp">Help</a></li>
</ul>
<div class="right tiny">
<?php
  if ($currentUser != "")
  {
		 echo "Logged in as $currentUser (<a id=\"linkLogout\" href=\"?actionLogout=go\">logout</a> / <a href=\"#\" id=\"linkUpload\">upload</a>)";
         echo "<form id=\"formUpload\" class=\"dropzone\">";
?>
  <div style="text-align:right;"><a href="#" id="linkUploadOptions">Batch Options</a></div><hr>
  <div id="formUploadOptions" style="display:none;">
  Use the following settings for all uploaded files:
  <input type="hidden" id="batchMode" name="batchMode" value="N">
  <table>
   <tr><td><label>Public?</label></td><td><select name="defaultPublic" id="defaultPublic" ><option value="1">Yes, video appears in search results</option><option value="0">No, hidden from search results</option></select></td></tr>
   <tr><td><label>Title</label></td><td><input type="text" name="defaultTitle" id="defaultTitle"></td></tr>
   <tr><td><label>Keywords</label></td><td><input type="text" name="defaultKeywords" id="defaultKeywords"></td></tr>
   <tr><td><label>Description</label></td><td><textarea  name="defaultDescription" id="defaultDescription"></textarea></td></tr>
  </table>
  </div>
<?php
        echo "</form>";
  }
  else
  {
		 echo "<a href=\"#\" onClick=\"showLogin()\">Login</a> to upload or manage your videos.";
  }
?>
</div>
<div id="tabSearch">
<?php

  $search = "";
  if(isset( $_POST['search'] ) ) 
  {
     $search = $_POST['search'];
  }

  echo "<form id=\"formSearch\" method=\"POST\" >" .
          "<table width=\"100%\">" . 
           "<tr>" . 
             "<th width=\"90\">Search</th>" . 
             "<td><input name=\"search\" id=\"search\" placeholder=\"title, description, date, owner, etc.\" value=\"$search\"></td>" . 
           "</tr>";

   if($currentUser != "")
   {
    echo "<tr><th>..or..</th><td>Show <a href=\"#\" onClick=\"goSearch('my')\">my videos</a>.  <span class=\"tiny\">To upload, drop your video file onto this page.</span> </td></tr>";
   }

   echo "</table>";

    if( $search != "" )
   {
      echo "<hr>";
      showTiles( $search ); 
   }
?>
</form>
</div>
<div id="tabHelp">
<?
   include ("./help.php") 
?>
</div>
</div>
</body>
</html>


