<?php

/* $Id: user_blog_album_browse.php 168 2009-05-22 23:15:00Z john $ */

$page = "user_blog_album_browse";
include "header.php";

// ENSURE ALBUMS ARE ENABLED FOR THIS USER
if( !$user->level_info['level_album_allow'] || !$user->level_info['level_blog_create'] )
{
  //header("Location: user_home.php");
  exit();
}


$album = new se_album($user->user_info['user_id']);


// Show albums
if( empty($_GET['album_id']) )
{
  // GET ALBUMS
  $total_albums = $album->album_total();
  $album_array = $album->album_list(0, $total_albums);

  $space_used = $album->album_space();
  $total_files = $album->album_files();
  
  $smarty->assign('albums_total', $total_albums);
  $smarty->assign_by_ref('albums', $album_array);
}


// Show media
else
{
  $album_id = $_GET['album_id'];
  $album_query = $database->database_query("SELECT * FROM se_albums WHERE album_id='{$album_id}' AND album_user_id='{$user->user_info['user_id']}'");
  $album_info = $database->database_fetch_assoc($album_query);
  
  $total_files = $album->album_files($album_info['album_id']);
  $file_array = $album->album_media_list(0, $total_files, "media_id ASC", "(media_album_id='{$album_id}')");
  
  $smarty->assign('album_id', $album_id);
  $smarty->assign('album_info', $album_info);
  $smarty->assign('media_total', $total_files);
  $smarty->assign('media', $file_array);
}





// ASSIGN VARIABLES AND SHOW VIEW ALBUMS PAGE
include "footer.php";
?>