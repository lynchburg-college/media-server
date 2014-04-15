<?php
    $rootID="";
    $rootID = $_SERVER['QUERY_STRING'] OR die("No Playlist Provided!");
?>
<html>
<head>
  <title>Media Server - Playlist</title>

<link rel="stylesheet" type="text/css" href="../easyui/themes/bootstrap/easyui.css">
<link rel="stylesheet" type="text/css" href="../easyui/themes/icon.css">
<style>
  .playlist { width:25%; height:100%; }
  .player { width:75%; height:100%; }
  #playerFrame { frameborder:none; border:none; width:100%,height:100%; min-width:1024px; min-height:768px; }
</style>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript" src="../easyui/jquery.easyui.min.js"></script></head>

</head>

<body>

 <div id="cc" class="easyui-layout" style="width:100%;height:100%;">

    <div class="playlist" style="width:300px;height:800px;" data-options="region:'west',title:'Choose A Video',split:true">

			<ul        class="easyui-tree"
				data-options="url:'callback.php?action=_getlist&id=<?=$rootID?>',
				              onClick:function(node) { if(node.attributes) { $('#playerFrame').attr('src','/player/?'+node.attributes['mediaName']+'') } }">
			</ul>
    </div>

    <div class="player" style="width:900px;height:1024px;" data-options="region:'center'">
      <iframe frameborder="0" id="playerFrame" name="playerFrame"></iframe>
    </div>

 </div>

</body>

</html>

