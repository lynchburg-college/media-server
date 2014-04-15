<?php
    $rootID="";
    $rootID = $_SERVER['QUERY_STRING'] OR die("No Playlist Provided!");
?>
<html>
<head>
  <title>Media Server - Playlist</title>

  <link href="../css/main.css" rel="stylesheet" type="text/css" media="all"  />
  <link href="../css/aciTree.css" rel="stylesheet" type="text/css" media="all"  />
  <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css"  >

  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

  <script src="../js/jquery.aciPlugin.min.js"></script>
  <script src="../js/jquery.aciTree.min.js"></script>

  <style>
   #playerFrame { frameborder:none; border:none; width:100%;min-height:800px; }
  </style>

</head>
<body>
<table width="100%">
 <tr>
  <td valign="top" width="24%"><b>Choose a video</b><div class="aciTree" id="tree"></div></td>
  <td valign="top"><iframe frameborder="0" id="playerFrame" name="playerFrame"></iframe></td>
 </tr>
</table>
</body>
</html>
<script>

       $(document).ready(function () {

             rootID='<?php echo $rootID; ?>';
             if( rootID == '' )
             {
                window.alert("No list to display!");
                return;
             }

             $('#tree').aciTree({   jsonUrl: '/callback.php?action=list&rootID='+rootID+'&itemID='  })
                       .on('acitree', function(event,api,item,eventName,options) { 
                                                                                   if(eventName=='selected')
                                                                                   {
                                                                                        var itemData=api.itemData(item);
                                                                                        if(!itemData['isFolder'])
                                                                                        {
                                                                                           $('#playerFrame').attr('src','/player/?'+itemData['mediaName']);
                                                                                        };
                                                                                   } 
                                                                                 });

    });
  </script>

