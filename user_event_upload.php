<?php

$page = "user_event_upload";
include "header.php";

$task       = ( !empty($_POST['task'])      ? $_POST['task']      : ( !empty($_GET['task'])       ? $_GET['task']       : NULL ) );
$event_id   = ( !empty($_POST['event_id'])  ? $_POST['event_id']  : ( !empty($_GET['event_id'])   ? $_GET['event_id']   : NULL ) );


// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( !$user->user_exists && !$setting['setting_permission_event'] ) exit();


// ENSURE EVENTS ARE ENABLED FOR THIS USER
if( 1 & ~$user->level_info['level_event_allow'] ) exit();


// INITIALIZE EVENT OBJECT
$event = new se_event($user->user_info['user_id'], $event_id);
if( !$event->event_exists ) exit();


// CHECK IF USER IS ALLOWED TO UPLOAD PHOTOS
$privacy_max = $event->event_privacy_max($user);
if( $privacy_max & ~ $event->event_info['event_privacy'] ) exit();
if( $privacy_max & ~ $event->event_info['event_upload']  ) exit();


// GET ALBUM INFO
$sql = "SELECT * FROM se_eventalbums WHERE eventalbum_event_id='{$event->event_info['event_id']}' LIMIT 1";
$resource = $database->database_query($sql);
$eventalbum_info = $database->database_fetch_assoc($resource);


// SET RESULT AND ERROR VARS
$result = FALSE;
$is_error = FALSE;
$show_uploader = TRUE;
$file_result = array();

// GET TOTAL SPACE USED
$space_used = $event->event_media_space();
if( $event->eventowner_level_info['level_event_album_storage'] )
{
  $space_left = $event->eventowner_level_info['level_event_album_storage'] - $space_used;
}
else
{
  $space_left = ( $dfs=disk_free_space("/") ? $dfs : pow(2, 32) );
} 



// UPLOAD FILES
if($task == "doupload")
{
  $isAjax = $_POST['isAjax'];
  $file_result = Array();

  // WORKAROUND FOR FLASH UPLOADER
  if($_FILES['file1']['type'] == "application/octet-stream" && $isAjax) { 
    $file_types = explode(",", str_replace(" ", "", strtolower($event->eventowner_level_info['level_event_album_mimes'])));
    $_FILES['file1']['type'] = $file_types[0];
  }

  // RUN FILE UPLOAD FUNCTION FOR EACH SUBMITTED FILE
  $update_album = 0;
  $action_media = Array();
  for($f=1;$f<6;$f++)
  {
    $fileid = "file".$f;
    if($_FILES[$fileid]['name'] != "")
    {
      $file_result[$fileid] = $event->event_media_upload($fileid, $eventalbum_info['eventalbum_id'], $space_left);
      if( !$file_result[$fileid]['is_error'] )
      {
        $file_result[$fileid]['message'] = 2000248;
        $media_path = str_replace('./', '', $event->event_dir($event->event_info['event_id']).$file_result[$fileid]['eventmedia_id']."_thumb.jpg");
        $media_link = str_replace($url->url_base, '', $url->url_create('event_media', NULL, $event->event_info['event_id'], $file_result[$fileid]['eventmedia_id']));
        
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
        $update_album = 1;
      }
      else
      {
        $file_result[$fileid]['message'] = $file_result[$fileid]['is_error'];
      }
      SE_Language::_preload($file_result[$fileid]['message']);
    }
  }

  // UPDATE ALBUM UPDATED DATE AND ALBUM COVER IF FILE UPLOADED
  if($update_album)
  {
    $database->database_query("UPDATE se_eventalbums SET eventalbum_dateupdated='".time()."' WHERE eventalbum_id='{$eventalbum_info['eventalbum_id']}'");
    
    // UPDATE LAST UPDATE DATE (SAY THAT 10 TIMES FAST)
    $event->event_lastupdate();
    
    // INSERT ACTION
    $event_title = $event->event_info['event_title'];
    if(strlen($event_title) > 100) { $event_title = substr($event_title, 0, 97)."..."; }
    $actions->actions_add(
      $user,
      "neweventmedia",
      Array(
        $user->user_info['user_username'],
        $user->user_displayname,
        $event->event_info['event_id'],
        $event_title
      ),
      $action_media,
      60,
      FALSE,
      "event",
      $event->event_info['event_id'],
      $event->event_info['event_privacy']
    );
  }

  // OUTPUT JSON RESULT
  if($isAjax)
  {
    SE_Language::load();
    if($update_album) {
      $result = "success"; 
      $size = sprintf(SE_Language::_get($file_result['file1']['message']), $file_result['file1']['file_name']);
      $error = null; 
    } else {
      $result = "failure";
      $error = sprintf(SE_Language::_get($file_result['file1']['message']), $file_result['file1']['file_name']);
      $size = null;
    }
    $json = '{"result":"'.$result.'","error":"'.$error.'","size":"'.$size.'"}';
    if(!headers_sent()) { header('Content-type: application/json'); }
    echo $json;
    exit();

  // SHOW PAGE WITH RESULTS
  } else {
   $show_uploader = 0;
  }

} // END TASK



// GET MAX FILESIZE ALLOWED
$max_filesize_kb = ($event->eventowner_level_info['level_event_album_maxsize']) / 1024;
$max_filesize_kb = round($max_filesize_kb, 0);

// CONVERT UPDATED SPACE LEFT TO MB
$space_left_mb = ($space_left / 1024) / 1024;
$space_left_mb = round($space_left_mb, 2);


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
$_SESSION['action'] = "user_event_upload.php";


// SET INPUTS
$inputs = Array('event_id' => $event->event_info['event_id']);


// ASSIGN VARIABLES AND SHOW UPLOAD FILES PAGE
$smarty->assign('show_uploader', $show_uploader);
$smarty->assign('session_id', session_id());
$smarty->assign('upload_token', $_SESSION['upload_token']);
$smarty->assign('file_result', $file_result);
$smarty->assign('eventalbum_info', $eventalbum_info);
$smarty->assign('inputs', $inputs);
$smarty->assign('space_left', $space_left_mb);
$smarty->assign('allowed_exts', str_replace(",", ", ", $event->eventowner_level_info['level_event_album_exts']));
$smarty->assign('max_filesize', $max_filesize_kb);

$smarty->assign('user_upload_allowed_extensions', $event->eventowner_level_info['level_event_album_exts']);
$smarty->assign('user_upload_max_size', $event->eventowner_level_info['level_event_album_maxsize']);

include "footer.php";
?>