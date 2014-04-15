<html>
<head>

<link rel="stylesheet" type="text/css" href="easyui/themes/bootstrap/easyui.css">
<link rel="stylesheet" type="text/css" href="easyui/themes/icon.css">
<style>
*{
	font-size:14px;
}
html,body{
	margin:0;
	padding:0;
}

body{
    background-color: red;
    font-family:verdana,helvetica,arial,sans-serif;
	background:#fafafa;
	text-align:center;
    }

label { display: inline-block; width: 90px; }

.tree-editor { height: 24px!important; width:200px!important; }


.activeDialog {
               padding:1.25em;
              }
.dragProxy {
             text-align:left;
             padding:1em;
             border:1px solid;
             border-radius:5px;
             background-color:gainsboro;
           }

.panelVideos { width:60%; height: 100%; }
.panelPlaylists { width:40%; height: 100%; }

.btnDynamic  { display:none;} 
.cell-edit   { display:none; }
 td:hover .cell-edit { display:visible } 

.treeEdit { text-align:right; padding-left:3em;}
.treeEdit a { display:inline; font-size:10px; color:white; margin-left:0.50em; }
.treeEdit a:hover { position:relative; font-weight:bold;}
.tree-node .treeEdit { display:none; }
.tree-node-selected .treeEdit { display:inline; }

}
</style>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript" src="easyui/jquery.easyui.min.js"></script></head>
<script type="text/javascript" src="easyui/plugins/extensions/datagrid-dnd.js"></script></head>
<script type="text/javascript" src="easyui/plugins/extensions/datagrid-scrollview.js"></script></head>

<script type="text/javascript" src="js/my.js"></script>
</head>
<body style="margin:2em;">


<div id="dialogEdit" class="easyui-dialog" style="width:400px;height:220px;padding:10px 20px" data-options="modal:true,closed:true,buttons:'#dialogEditButtons'">
        <form id="fm" method="post" novalidate>
            <div>
                <label>Title:</label>
                <input name="title" class="easyui-validatebox" data-options="required:true,missingMessage:'Title is required',validType:'length[3,64]'">
            </div>
            <div>
                <label>Description:</label>
                <input name="description" class="easyui-validatebox" required="true">
            </div>
            <div>
                <label>Visibility:</label>
                <input name="public" class="easyui-validatebox" validType="email">
            </div>
        </form>
 </div>
 <div id="dialogEditButtons">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="">Save</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dialogEdit').dialog('close')">Cancel</a>
 </div>



 <div id="cc" class="easyui-layout" style="width:100%;height:100%;">

    <div class="panelVideos" data-options="region:'center',split:true,title:'My Uploaded Videos'">

		<div id="datagrid_Menu" style="height:2em;padding:0.25em;" >
			<a href="#" class="easyui-linkbutton" iconCls="icon-reload" onclick="datagrid_Reload()"></a>
            <a href="#" class="easyui-linkbutton btnDynamic btnSingle btnMultiple" iconCls="icon-delete" title="Delete"></a>
            <a href="#" class="easyui-linkbutton btnDynamic btnSingle btnMultiple" iconCls="icon-edit" title="Edit" onclick="datagrid_Edit()"></a>
            </span>
		</div>


		 <table  id="myVideos" 
		      class="easyui-datagrid" 
              style="height:800px;" 
       data-options="onLoadSuccess:datagrid_onLoadSuccess,
		             onLoadError:datagrid_error,
		             toolbar:'#datagrid_Menu',             
		             onSelect:datagrid_setupToolbar, 
		             onSelectAll:datagrid_setupToolbar, 
		             onUnselect:datagrid_setupToolbar, 
		             onUnselectAll:datagrid_setupToolbar, 
		             remoteSort:false,
		             striped:true,
                     fitColumns:true,
                     selectOnCheck:true,
		             checkOnSelect:true,
		             idField:'mediaName',
	                 url:'callback.php?action=my'" >
		    <thead>
		      <tr>
		       <th data-options="field:'mediaName',checkbox:true">Media</th>
		       <th data-options="field:'title',width:280,sortable:true,editor:{type:'text'}">Title</th>
		       <th data-options="field:'uploaded',width:125,sortable:true">Uploaded</th>
		     </tr>
		    </thead>
		  </table>

   </div>

    <div class="panelPlaylists" data-options="region:'east',split:true,title:'My Playlists'">

      <div id="myPlaylists_menu" style="height:2em;padding:0.25em;" class="datagrid-toolbar">
    	<a href="#" class="easyui-linkbutton" iconCls="icon-reload" onclick="tree_reload()"></a>
      </div>

      <ul id="myPlaylists" class="easyui-tree" data-options="dnd:true,
                                                         onLoadSuccess:tree_onLoadSuccess,
                                                         onAfterEdit: tree_onAfterEdit,
                                                         onDblClick: tree_onDblClick,
                                                         formatter:tree_Formatter,
                                                         url:'callback.php?action=_getlist'">
      </ul>

   </div>





</div>
</body>

</html>

