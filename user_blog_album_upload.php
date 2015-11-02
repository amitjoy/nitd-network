<?php

$page = "user_blog_album_upload";
include "header.php";

$task     = ( !empty($_POST['task'])    ? $_POST['task']    : ( !empty($_GET['task'])   ? $_GET['task']   : NULL  ) );
$album_id = ( !empty($_POST['album_id'])  ? $_POST['album_id']  : ( !empty($_GET['album_id']) ? $_GET['album_id'] : FALSE ) );
$isAjax   = ( !empty($_POST['isAjax'])  ? $_POST['isAjax']  : ( !empty($_GET['isAjax']) ? $_GET['isAjax'] : FALSE ) );

$result = 0;
$is_error = 0;
$show_uploader = FALSE;


// ENSURE ALBUMS ARE ENABLED FOR THIS USER
if( !$user->level_info['level_album_allow'] || !$user->level_info['level_blog_create'] )
{
  //header("Location: user_home.php");
  exit();
}


// GET ALBUMS
$album = new se_album($user->user_info['user_id']);


if( $task=="doupload" )
{
  // CREATE NEW ALBUM IF SELECTED
  if( !$album_id )
  {
    $level_album_privacy = unserialize($user->level_info['level_album_privacy']);
    $level_album_comments = unserialize($user->level_info['level_album_comments']);
    $level_album_tag = unserialize($user->level_info['level_album_tag']);
    
    $album_title = censor($_POST['album_title']);
    $album_desc = '';
    $album_privacy = $level_album_privacy[0];
    $album_comments = $level_album_comments[0];
    $album_tag = $level_album_tag[0];
    $album_datecreated = time();
    
    // Untitled
    if( empty($album_title) )
      $album_title = SE_Language::get(1500015);
    
    // GET MAX ORDER
    $max = $database->database_fetch_assoc($database->database_query("SELECT max(album_order) AS max FROM se_albums WHERE album_user_id='{$user->user_info['user_id']}'"));
    $album_order = $max[max]+1;
    
    // INSERT NEW ALBUM INTO DATABASE
    $database->database_query("
      INSERT INTO se_albums (
				album_user_id,
				album_datecreated,
				album_dateupdated,
				album_title, 
				album_desc, 
				album_search,
				album_privacy,
				album_comments,
				album_tag,
				album_order
      ) VALUES (
				'{$user->user_info['user_id']}',
				'$album_datecreated',
				'$album_datecreated',
				'$album_title',
				'$album_desc',
				'$album_search',
				'$album_privacy',
				'$album_comments',
				'$album_tag',
				'$album_order'
      )
    ") or die($database->database_error());
    
    $album_id = $database->database_insert_id();
    
    // UPDATE LAST UPDATE DATE (SAY THAT 10 TIMES FAST)
    $user->user_lastupdate();
    
    // INSERT ACTION
    if(strlen($album_title) > 100) { $album_title = substr($album_title, 0, 97); $album_title .= "..."; }
    $actions->actions_add($user, "newalbum", Array($user->user_info[user_username], $user->user_displayname, $album_id, $album_title), Array(), 0, FALSE, "user", $user->user_info[user_id], $album_privacy);
    
    // CALL ALBUM CREATION HOOK
    ($hook = SE_Hook::exists('se_album_create')) ? SE_Hook::call($hook, array()) : NULL;
  }
  
  
  // BE SURE ALBUM BELONGS TO THIS USER
  $resource = $database->database_query("SELECT * FROM se_albums WHERE album_id='$album_id' AND album_user_id='".$user->user_info['user_id']."'");
  if( !$database->database_num_rows($resource) ) { header("Location: user_album.php"); exit(); }
  $album_info = $database->database_fetch_assoc($resource);
  
  
  // GET TOTAL SPACE USED
  $space_used = $album->album_space();
  if($user->level_info[level_album_storage]) {
    $space_left = $user->level_info[level_album_storage] - $space_used;
  } else {
    $space_left = ( $dfs=disk_free_space("/") ? $dfs : pow(2, 32) );
  } 
  
  
  // UPLOAD FILE
  $file_result = Array();
  
  // RUN FILE UPLOAD FUNCTION FOR EACH SUBMITTED FILE
  $update_album = 0;
  $new_album_cover = "";
  $action_media = Array();
  
  $fileid = "file1";
  if( !empty($_FILES[$fileid]['name']) )
  {
    $file_result[$fileid] = $album->album_media_upload($fileid, $album_id, $space_left);
    if( !$file_result[$fileid]['is_error'] )
    {
      $file_result[$fileid]['message'] = 1000086;
      $new_album_cover = $file_result[$fileid]['media_id'];
      $media_path = str_replace('./', '', $url->url_userdir($user->user_info['user_id']).$file_result[$fileid]['media_id']."_thumb.jpg");
      $media_link = str_replace($url->url_base, '', $url->url_create('album_file', $user->user_info['user_username'], $album_id, $file_result[$fileid]['media_id']));
      
      if( file_exists($media_path) )
      {
        $media_width = $misc->photo_size($media_path, "100", "100", "w");
        $media_height = $misc->photo_size($media_path, "100", "100", "h");
        $action_media[] = Array(
          'media_link' => $media_link,
          'media_path' => $media_path,
          'media_width' => $media_width,
          'media_height' => $media_height
        );
      }
      $file_result[$fileid]['media_path'] = $url->url_base.substr($url->url_userdir($user->user_info['user_id']), 2).$file_result[$fileid]['media_id'].'.'.$file_result[$fileid]['media_ext'];
      
      $update_album = 1;
    }
    
    else
    {
      $file_result[$fileid]['message'] = $file_result[$fileid]['is_error'];
    }
    
    SE_Language::_preload($file_result[$fileid]['message']);
  }

  // UPDATE ALBUM UPDATED DATE AND ALBUM COVER IF FILE UPLOADED
  if($update_album)
  {
    $newdate = time();
    if( $album_info['album_cover'] ) { $new_album_cover = $album_info[album_cover]; }
    $database->database_query("UPDATE se_albums SET album_cover='$new_album_cover', album_dateupdated='$newdate' WHERE album_id='$album_id'");
    
    // UPDATE LAST UPDATE DATE (SAY THAT 10 TIMES FAST)
    $user->user_lastupdate();
    
    // INSERT ACTION
    $album_title = $album_info[album_title];
    if(strlen($album_title) > 100) { $album_title = substr($album_title, 0, 97)."..."; }
    $actions->actions_add($user, "newmedia", Array($user->user_info[user_username], $user->user_displayname, $album_id, $album_title), $action_media, 60, FALSE, "user", $user->user_info[user_id], $album_info[album_privacy]);
  }
}




// Get album list
$total_albums = $album->album_total();
$album_array = $album->album_list(0, $total_albums);

$space_used = $album->album_space();
$total_files = $album->album_files();

$smarty->assign('albums_total', $total_albums);
$smarty->assign_by_ref('albums', $album_array);



// START NEW SESSION AND SET SESSION VARS FOR UPLOADER

// Backwards compatibility with <SE3.10
if( !session_id() ) session_start();
if( !empty($_COOKIE['user_id']) )
{
  $_SESSION['ul_user_id'] = $_COOKIE['user_id'];
  $_SESSION['ul_user_email'] = $_COOKIE['user_email'];
  $_SESSION['ul_user_password'] = $_COOKIE['se_user_pass'];
}

// Keep with 3.10+
$_SESSION['upload_token'] = md5(uniqid(rand(), true));
$_SESSION['action'] = "user_blog_album_upload.php";


// SET INPUTS
$inputs = array();


// ASSIGN VARIABLES AND SHOW UPLOAD FILES PAGE
//$smarty->assign('allowed_exts', str_replace(",", ", ", $user->level_info[level_music_exts]));
//$smarty->assign('space_left', $space_left_mb);
//$smarty->assign('max_filesize', $max_filesize_kb);

$smarty->assign('task', $task);
$smarty->assign('show_uploader', $show_uploader);
$smarty->assign('inputs', $inputs);
$smarty->assign('file_result', $file_result);
$smarty->assign('session_id', session_id());
$smarty->assign('upload_token', $_SESSION['upload_token']);

// SET UPLOADER PARAMS
$smarty->assign('user_upload_max_size', $user->level_info['level_album_maxsize']);
$smarty->assign('user_upload_allowed_extensions', $user->level_info['level_album_exts']);
include "footer.php";
?>