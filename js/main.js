
Dropzone.autoDiscover = false;

jQuery.extend({
   postJSON: function( url, data, callback) { return jQuery.post(url, data, callback, "json") }
              });

$(document).keydown(function(e)
{
    switch( e.keyCode )
    {
          case 37 : $('.previous_link').click();
                    e.stopPropagation();
                    break;

          case 39 : $('.next_link').click();
                    e.stopPropagation();
                    break;

          default : break;
    };

});

 $(document).ready( function() {

          $("#tabs").tooltip( {show: {effect: "slideDown", delay: 200}  });

          $("#divAlert").dialog( { autoOpen : false, modal:true, width:600, height:300, title:"Alert", position: {my:"top", at:"top", of:window} } );

          $("#formUpload").dialog( { autoOpen : false, modal:true, width:500, height:400, show:"slideDown", hide:"fadeOut", position: { my:"top",at:"top",of:window}, title:"Drop your file(s) onto this box" } );

          $("#formLogin").dialog( { autoOpen : false, modal:true, width:600, height:300, show:"slideDown", title:"Login", position: {my:"top", at:"top", of:window} } );

          $("#formEdit").dialog( {  autoOpen: false, 
                                       modal:true, 
                                       width:780, 
                                      height:390, 
                                        show:"fadeIn", 
                                        hide:"fadeOut", 
                                       title:"Edit Video Information",
                                    position:{ my:"top", at:"top",of:window},
                                       close:function(e,ui) {
                                                            nextMediaName = ( editQueue.pop() || "" );
                                                            if(nextMediaName != "" ) { editMedia(nextMediaName)  };
                                                            }
                                } );

         $("#formEdit").ajaxForm ( { 
                                      beforeSubmit: function(a,f,opts) {
                                                       title=$('#formEdit input[name="title"]').val();
                                                       v="";
                                                       if(title.length < 1) { v="Title is too short!" };
                                                       if(title.length > 64) { v="Title is too long!" };
                                                       if(v!="")
                                                       {
                                                           window.alert(v);
                                                           return false;
                                                       }
                                                       return true;                   
                                                    },
                                      success: function() { 
                                                            $("#formEdit").dialog("close");
                                                            mediaName=$('#formEdit input[name="mediaName"]').val();
                                                            goSearch();
                                                } 
                                    });
               
           setupDropzone();
           setupTiles();
           setupTabs();
           setupResults();

           if($('#divAlert')) {  $("#divAlert").dialog("open") };

 });



var setupResults = function() {

	$('.results').pajinate( { items_per_page : 4,
                              abort_on_small_lists : true
                            } );

}


var setupTabs = function() {

           $("#tabs").tabs( {} );
           if($('#tabAdmin').length != 0)
           {
             $("#tabs ul").append("<li><a href=\"#tabAdmin\">Admin</a></li>");
             $("#tabs").tabs( "refresh" );
           };

} 


var dzCounter = 0;
var dz;
var setupDropzone = function() {

        if( $("#formUpload").length != 0 )
        {
         
          $("#linkUpload").bind("click", function() { 
                                                        $('#formUpload').dialog("open");
                                                     });

          $("#linkUploadOptions").bind("click", function() { 
                                                        $('#formUploadOptions').toggle();
                                                        batchMode = ( $('#formUploadOptions').is(":visible") ) ? "Y" : "N";
                                                        $('#batchMode').val(batchMode);
                                                     });

          dz=new Dropzone( document.body, 
              {
			   url:'upload.php',
               previewsContainer : "#formUpload",
               maxFilesize : 2500,
			   clickable : false,
               createImageThumbnails : false,
			   addRemoveLinks : true,       
               uploadMultiple : false,
               parallelUploads : 1,
			   dictCancelUpload : "cancel",
			   dictCancelUploadConfirmation : "Cancel this video upload?",
			   dictRemoveFile : "",
               dragover : function(e) { $("#formUpload").dialog("open"); } ,
               params :  uploadParams,
               sendingmultiple : function(e) { 
				           window.alert("Only one file at a time!");
				           this.removeAllFiles(true);
                           },
               accept: function(file,done) {
				           if( file.type.indexOf('ideo') == -1)
						   {    
						     done('Only video files are allowed.'); 
						   }
						   else
						   {
                            dzCounter++;
						    done();
						   }
				       },
			   success : function (file, mediaName)
				       {
                           dzCounter--;
                           file.previewElement.classList.add("dz-success");
                           dz.removeFile(file);
                           if(dzCounter < 1) {  $("#formUpload").dialog("close") };

                           if( !($('#formUploadOptions').is(":visible")) )
                           {
                              window.setTimeout( function(){ editMedia( mediaName ) }, 1005 );
                           }

				       },
			   error : function(file,response) 
				         {
				           window.alert(response);
				           this.removeFile(file);
				         }
		});
    };
};


var goSearch = function (term) {

   if(term) { $('input[name="search"]').val((term||'')); }
   $('#formSearch').submit();
}

var showLogin = function() {
  $("#formLogin").dialog("open")
}

function uploadParams() {

  opts = { "batchMode":"N" };

  if( $('#formUploadOptions').is(":visible") )
  {
   opts = { "batchMode":"Y",
            "public": $('#defaultPublic').val(),
            "title": $('#defaultTitle').val(),
            "description": $('#defaultDescription').val()
      };
  }

 return opts;
}


var editQueue = [];
var editMedia = function( media ) {

          if (!media) { return false };
 
          if(jQuery.type(media) == "string")
          {
              if ($('#formEdit').is(":visible"))
               {
                  editQueue.push( media );
               }
               else
               {
				     $.postJSON( "callback.php",
				                { mediaName:media, action:"select" },
				                function(o) {  editMedia(o) } 
				              );
               }             
              return;
          };
 
         media.title = (media.title==null) ? '' : media.title;
         media.description = (media.description==null) ? '':media.description;
         media.link="http://media.lynchburg.edu/player/?"+media.mediaName;
  
         $('#formEdit input[name="mediaName"]').val(media.mediaName);
         $('#formEdit span.uploaded').html(media.uploaded);
         $('#formEdit span.link').html(media.link);
         $('#formEdit img.thumbnail').attr("src","content/available/"+media.mediaName+"_thumbnail.png");
         $('#formEdit input[name="title"]').val(media.title);
         $('#formEdit textarea[name="description"]').val(media.description);
         $('#formEdit select[name="public"]').val(media.public);

         $('#formEdit').dialog("open");

}

var loadTile=function( media, stage )
{
          // initial call.. ask for data
          if(!stage)
          {
             $.postJSON( "callback.php",
                        { mediaName:media, action:"select" },
                        function(o) {  loadTile(o,true) } 
                      );
             return;
          };

  id="#media_"+media.mediaName;
  tile=$(id);
  if(tile)
  {
         $(id+ ' p.title').html(media.title);
         $(id+ ' p.description').html(media.description);
  }
}


var showStatus=function( media, callback )
{
   if(!callback)
   {
      $.postJSON( "callback.php", { action:"status", mediaName:media }, function(o) {  showStatus(o,true) } );
      return;
   };

   if(media == "") { return } ;
   item = eval( media );
   
   mediaName = item.mediaName;
   status = item.status;
   title = item.title;
   summary = item.summary;

   $.gritter.add( { title:title, text:summary } );

  if(status!="available")  
  {
     window.setTimeout( function() { showStatus()} , 7000);
  }

}



var setupTiles = function ()
 {

    // Play link
    $( ".video.available .title, .video.available .link, .video.available .thumbnail" ).each( function(i,o)  {  
           id=$(o).parents(".video").prop("id").substring(6,32);
           $(o).addClass("active").wrap('<a target=\"_player_'+id+'\" href="player/?'+id+'">');
    } );
    // Other clickables
    $( ".video.available .by" ).each( function(i,o)  {  
           id=$(o).parents(".video").prop("id").substring(6,32);
           $(o).addClass("active").wrap('<a href=\"#\" onClick=\"goSearch(\''+$(o).html()+'\')\">');
    } );


    
    // Action Bar
    $( ".video.canEdit" ).each( function(i,o) {  

           tile=$(o);
           id = tile.prop("id").substring(6,32);
           actionBar = $("<p></p>");
           actionBar.addClass("actionbar");

           linkDelete = $("<span></span>");
           linkDelete.html("delete");
           linkDelete.bind("click", function() {
												   if(window.confirm('Delete this video?') )
												   {  p=$(this).parents(".video");
													  mediaName=$(p).prop("id").substring(6,32);
													  $.post( "callback.php",
															 {"action":"delete", "mediaName":mediaName},
															 function(data,status) { if(status=='success') { p.fadeOut() } }
															 );
                                                   }
   										       });   

           linkEdit = $("<span></span>");
           linkEdit.html("edit");
           linkEdit.bind("click", function() {   p=$(this).parents(".video");
											  	 mediaName=$(p).prop("id").substring(6,32);
												 editMedia( mediaName );
  										      });   

           linkInfo = $("<span></span>");
           linkInfo.html("info");
           linkInfo.bind("click", function() {   p=$(this).parents(".video");
											  	 mediaName=$(p).prop("id").substring(6,32);
												 window.open( './info.php?mediaName='+mediaName, 'info');
  										      });   


           actionBar.append( [ linkDelete, linkEdit, linkInfo ] );

           tile.append( actionBar );
         

     } );

};

