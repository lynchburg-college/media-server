<?php

include_once("common.php");
include_once("database.php");

if( !isset($_POST['folder'] ) )
{
  // Show all the folders in the BATCH directory
  echo showContents("content/batch", 0);
}



function showContents( $folder, $level )
{
  $items = glob( $folder."/*" );
  foreach ($items as $item )
   {
       echo "<div style=\"padding-left:$level"."em\">$item";
       if(is_dir($item))
       {
          showContents( $item, ($level+1) );
       }
       else
       {
          echo " (".showMeta($item).")";
       }

       echo "</div>";
   }

}

function showMeta ( $item )
{
   $tokens = explode("/", $item);

   $owner = $tokens[2];
   $category = $tokens[3];
   $group = $tokens[4];
   $title = $tokens[5];

   return "$owner - $category - $group - $title";
}
 
?>

