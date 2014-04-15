
jQuery.extend({
   postJSON: function( url, data, callback) { return jQuery.post(url, data, callback, "json") }
              });


var getVideos=function()
{
   return $('#myVideos');
}

var getPlaylists=function()
{
   return $('#myPlaylists');
}

var callback_Handler=function( data )
{
  
}




var tree_onLoadSuccess=function( element,data )
{

  cc=getPlaylists().tree('getChildren');
  $.each(cc, 
         function(k,v) {
                         $(v.target).droppable( { accept:'*',
                                                  onDrop: tree_onDrop
                                                });
                       }
        );

  if(cc.length==1)
  {
    
  }

}
var tree_onDblClick=function()
{
  t = getPlaylists();
  node=t.tree('getSelected');
  console.log(node);
  window.open( '/list/?'+node.id, '_playlist'+node.id );
}

var tree_Formatter=function(node)
{
  display=node.text;
  console.log(node);

  if( node.mediaName == '' || typeof node.mediaName == 'undefined')
  {
    items = '<a href="#" onclick="tree_Edit()">edit</a>' + 
            '<a href="#" onclick="tree_Add()">add</a>';
    if( $.isNumeric( node.parentID ) )
    {
    items = items+
            '<a href="#" onclick="tree_Delete()">delete</a>';
    };
    display = display + '<span class="treeEdit">'+items+'</span>';
  }
  return display;
}



var tree_onDrop=function( e, source )
{

   v=getVideos();
   t=getPlaylists();

   t.tree('expandAll', e.target  );

   if($(source).hasClass('tree-node'))
   {
   }

   else if( $(source).hasClass('datagrid-row'))
   {
	   $.each( v.datagrid('getSelections'),
		        function( k,o ) {
		                            console.log(o);
		                         }
		      );
    };
}

var tree_Edit=function()
{
    var t=getPlaylists();
    node=t.tree('getSelected');
    t.tree('beginEdit', node.target)
}

var tree_onAfterEdit=function( data )
{
   options={};
   options.action='_renamelist';
   options.id=data.id;
   options.text=data.text;

   $.postJSON( "callback.php?action=_renamelist", options );
}

var tree_reload=function()
{
  var t=getPlaylists();
  t.tree('reload');
}

var tree_Delete=function( confirmed, completed )
{

  var t=getPlaylists();
  node=t.tree('getSelected');
  if(!node) { return };

  if(!confirmed)
  {
   dd=$('<div></div>')
      .addClass('activeDialog')
      .html('This will remove the playlist only.<br>Videos will not be deleted.')
      .dialog(  {
                 modal : true,
                 draggable:false,
                 title:'Delete Playlist : ' + node.text,
                 iconCls:'icon-tip',
                 buttons: [
                            { text:'Delete Playlist', iconCls:'icon-ok', handler:function(){$('.activeDialog').dialog('destroy');tree_Delete(true)} }
                          ]
                });
  return;         
  }

  if(!completed)
  {
       options={ action:"_deletelist" };
       options.id = node.id;
       $.postJSON( "callback.php", options, function(o) { tree_Delete(true,true) } );
  return;
  }

  t.tree('remove', node.target );

}


var tree_Add=function( data )
{
    var t=getPlaylists();

    if(!data)
    {
       options={ action:"_addlist" };
       var node = t.tree('getSelected');
       t.tree('expandAll', node.target);
       if(node) {  options.parentID = node.id};
       $.postJSON( "callback.php", options, function(o) { tree_Add(o) } );
       return;     
    }

    parentID = data[0].parentID;
    parentNode = t.tree('find', parentID );
    if(parentNode) { parentNode = parentNode.target };

    console.log(parentNode);

    t.tree('append',  { parent: parentNode, data: data  });
    newNode = t.tree('find', data[0].id );
    t.tree('beginEdit', newNode.target );
}








var datagrid_error=function()
{
  console.log('error');
}


var datagrid_onLoadSuccess=function()
{

  t=getVideos();
  t.datagrid('enableDnd');

  $('.datagrid-row').droppable('disable');

  $('.datagrid-row').draggable( { edge: 5, 
		                          proxy: drag_Proxy 
		                        });

  datagrid_setupToolbar();

}


var datagrid_setupToolbar=function()
{

 $('#datagrid_Menu .btnDynamic').hide();

 selectCount=getVideos().datagrid('getSelections').length;

 if(selectCount > 0)
 {
    (selectCount == 1) ? cc='.btnSingle' : cc='.btnMultiple';
    $('#datagrid_Menu '+cc).show();
 }

}


var datagrid_Reload=function()
{
  t=getVideos();
  t.datagrid('reload');
  t.datagrid('clearSelections');
  datagrid_setupToolbar();
}


var datagrid_Edit=function()
{

    /* var row = $('#dg').datagrid('getSelected');
       if (row){
          $('#fm').form('load',row);
    */
    $('#dialogEdit').dialog('open').dialog('setTitle','Edit Video');

}




var drag_Proxy=function(source)
{

    var p = $('<div></div>');
        p.append('<div class="panel-title"><i>Drop On A Playlist</i></div><hr>');
        p.addClass("dragProxy");

    var t=getVideos();
    if(!t.datagrid('getSelected'))  
    {
       p.text( 'Check one or more videos before dragging' );
    }
   
    $.each( t.datagrid('getSelections'),
            function( k, v ) {
                                p.append( $('<div class="item" mediaName="'+v.mediaName+'">' + v.title + '</div>') );
                             }
          );

    p.hide();
    p.appendTo('body');
	return p;
}


